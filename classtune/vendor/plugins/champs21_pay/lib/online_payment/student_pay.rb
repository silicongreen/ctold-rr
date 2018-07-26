module OnlinePayment
  class << self; attr_accessor_with_default :return_url,String.new; end
  module StudentPay
    def self.included(base)
      base.alias_method_chain :fee_details,:gateway
    end

    def fee_details_with_gateway
      require 'net/http'
      require 'uri'
      require "yaml"
      if Champs21Plugin.can_access_plugin?("champs21_pay")
        
        if (PaymentConfiguration.config_value("enabled_fees").present? and PaymentConfiguration.config_value("enabled_fees").include? "Student Fee") 
          @active_gateway = PaymentConfiguration.config_value("champs21_gateway")
          if @active_gateway == "Paypal"
            @merchant_id = PaymentConfiguration.config_value("paypal_id")
            @merchant_id ||= String.new
            @certificate = PaymentConfiguration.config_value("paypal_certificate")
            @certificate ||= String.new
          elsif @active_gateway == "Authorize.net"
            @merchant_id = PaymentConfiguration.config_value("authorize_net_merchant_id")
            @merchant_id ||= String.new
            @certificate = PaymentConfiguration.config_value("authorize_net_transaction_password")
            @certificate ||= String.new
          elsif @active_gateway == "ssl.commerce"
            @store_id = PaymentConfiguration.config_value("store_id")
            @store_id ||= String.new
            @store_password = PaymentConfiguration.config_value("store_password")
            @store_password ||= String.new
          elsif @active_gateway.nil?
            fee_details_without_gateway and return
          end
          current_school_name = Configuration.find_by_config_key('InstitutionName').try(:config_value)
          
          
          @student = Student.find(params[:id])
          @date = @fee_collection = FinanceFeeCollection.find(params[:id2])
          @student_has_due = false
          @std_finance_fee_due = FinanceFee.find(:first,:conditions=>["finance_fee_collections.due_date < ? and finance_fees.is_paid = 0 and finance_fees.student_id = ?", @date.due_date,@student.id],:include=>"finance_fee_collection")
          unless @std_finance_fee_due.blank?
            @student_has_due = true
          end
          @financefee = @student.finance_fee_by_date(@date)
          @due_date = @fee_collection.due_date
          @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
          flash[:warning]=nil
          flash[:notice]=nil
          @trans_id_ssl_commerce = "tran"+params[:id].to_s+params[:id2].to_s
          @paid_fees = @financefee.finance_transactions

          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
          @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
          @total_discount = 0
          
          calculate_discount(@date, @financefee.batch, @student, @financefee.is_paid)
          
          bal=(@total_payable-@total_discount).to_f
          
         
          days=(Date.today-@date.due_date.to_date).to_i
          auto_fine=@date.fine
          @has_fine_discount = false
          
          if days > 0 and auto_fine and @financefee.is_paid == false
            @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
            @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
            abort(@fine_amount.to_s)
            calculate_extra_fine(@date, @batch, @student, @fine_rule)
            @new_fine_amount = @fine_amount
            get_fine_discount(@date, @batch, @student)
            if @fine_amount < 0
              @fine_amount = 0
            end
          end
          
          @fine_amount=0 if @financefee.is_paid
          @has_fine_discount = false if @financefee.is_paid
          OnlinePayment.return_url = "http://#{request.host_with_port}/student/fee_details/#{params[:id]}/#{params[:id2]}?create_transaction=1" unless OnlinePayment.return_url.nil?
          total_fees = 0
          if @fine_amount.blank?
            @fine_amount = 0
          end
          total_fees =@financefee.balance.to_f+@fine_amount.to_f
          
          
          
          if @active_gateway == "Authorize.net"
            @sim_transaction = AuthorizeNet::SIM::Transaction.new(@merchant_id,@certificate, total_fees,{:hosted_payment_form => true,:x_description => "Fee-#{@student.admission_no}-#{@fee_collection.name}"})
            @sim_transaction.instance_variable_set("@custom_fields",{:x_description => "Fee (#{@student.full_name}-#{@student.admission_no}-#{@fee_collection.name})"})
            @sim_transaction.set_hosted_payment_receipt(AuthorizeNet::SIM::HostedReceiptPage.new(:link_method => AuthorizeNet::SIM::HostedReceiptPage::LinkMethod::GET, :link_text => "Back to #{current_school_name}", :link_url => URI.parse("http://#{request.host_with_port}/student/fee_details/#{params[:id]}/#{params[:id2]}?create_transaction=1&only_path=false")))
          end
          if params[:create_cancel_transaction].present?
            gateway_response = {
              :amount => params[:amount],
              :status => params[:status],
              :tran_id=>params[:tran_id],
              :tran_date=>params[:tran_date]
                
            }
            payment = Payment.new(:payee => @student,:payment => @financefee,:gateway_response => gateway_response)
            payment.save
            flash[:notice] = "#{t('payment_canceled')}"
          elsif params[:create_fail_transaction].present?
            
            gateway_response = {
              :amount => params[:amount],
              :status => params[:status],
              :transaction_id => params[:bank_tran_id],
              :tran_id=>params[:tran_id],
              :tran_date=>params[:tran_date],
              :card_type=>params[:card_type],
              :card_no=>params[:card_no],

              :card_issuer=>params[:card_issuer],

              :card_brand=>params[:card_brand],

              :card_issuer_country=>params[:card_issuer_country],

              :card_issuer_country_code=>params[:card_issuer_country_code],
              :error => params[:error]
            }
            payment = Payment.new(:payee => @student,:payment => @financefee,:gateway_response => gateway_response)
            payment.save
              
              
              
            flash[:notice] = "#{t('payment_failed')} : #{params[:error]}"   
          elsif params[:create_transaction].present?
            gateway_response = Hash.new
            if @active_gateway == "Paypal"
              gateway_response = {
                :amount => params[:amt],
                :status => params[:st],
                :transaction_id => params[:tx]
              }
            elsif @active_gateway == "Authorize.net"
              gateway_response = {
                :x_response_code => params[:x_response_code],
                :x_response_reason_code => params[:x_response_reason_code],
                :x_response_reason_text => params[:x_response_reason_text],
                :x_avs_code => params[:x_avs_code],
                :x_auth_code => params[:x_auth_code],
                :x_trans_id => params[:x_trans_id],
                :x_method => params[:x_method],
                :x_card_type => params[:x_card_type],
                :x_account_number => params[:x_account_number],
                :x_first_name => params[:x_first_name],
                :x_last_name => params[:x_last_name],
                :x_company => params[:x_company],
                :x_address => params[:x_address],
                :x_city => params[:x_city],
                :x_state => params[:x_state],
                :x_zip => params[:x_zip],
                :x_country => params[:x_country],
                :x_phone => params[:x_phone],
                :x_fax => params[:x_fax],
                :x_invoice_num => params[:x_invoice_num],
                :x_description => params[:x_description],
                :x_type => params[:x_type],
                :x_cust_id => params[:x_cust_id],
                :x_ship_to_first_name => params[:x_ship_to_first_name],
                :x_ship_to_last_name => params[:x_ship_to_last_name],
                :x_ship_to_company => params[:x_ship_to_company],
                :x_ship_to_address => params[:x_ship_to_address],
                :x_ship_to_city => params[:x_ship_to_city],
                :x_ship_to_zip => params[:x_ship_to_zip],
                :x_ship_to_country => params[:x_ship_to_country],
                :x_amount => params[:x_amount],
                :x_tax => params[:x_tax],
                :x_duty => params[:x_duty],
                :x_freight => params[:x_freight],
                :x_tax_exempt => params[:x_tax_exempt],
                :x_po_num => params[:x_po_num],
                :x_cvv2_resp_code => params[:x_cvv2_resp_code],
                :x_MD5_hash => params[:x_MD5_hash],
                :x_cavv_response => params[:x_cavv_response],
                :x_method_available => params[:x_method_available],
              }
            elsif @active_gateway == "ssl.commerce"
            
              gateway_response = {
                :amount => params[:amount],
                :status => params[:status],
                :transaction_id => params[:bank_tran_id],
                :tran_id=>params[:tran_id],
                :tran_date=>params[:tran_date],
                :card_type=>params[:card_type],
                :card_no=>params[:card_no],

                :card_issuer=>params[:card_issuer],

                :card_brand=>params[:card_brand],

                :card_issuer_country=>params[:card_issuer_country],

                :card_issuer_country_code=>params[:card_issuer_country_code],
                :val_id => params[:val_id]
              }


            end
            payment = Payment.new(:payee => @student,:payment => @financefee,:gateway_response => gateway_response)
            if payment.save
              gateway_status = false
              if @active_gateway == "Paypal"
                gateway_status = true if params[:st] == "Completed"
              elsif @active_gateway == "Authorize.net"
                gateway_status = true if params[:x_response_reason_code] == "1"
              elsif @active_gateway == "ssl.commerce"
                gateway_status = true if params[:status] == "VALID"
              
                if gateway_status == true
                  payment_urls = Hash.new
                  if File.exists?("#{Rails.root}/vendor/plugins/champs21_pay/config/online_payment_url.yml")
                    payment_urls = YAML.load_file(File.join(Rails.root,"vendor/plugins/champs21_pay/config/","online_payment_url.yml"))
                  end

                  if MultiSchool.current_school.id == 2
                    validation_url = payment_urls["ssl_commerce_sandbox_requested_url"]
                    validation_url ||= "https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php"
                  else
                    validation_url = payment_urls["ssl_commerce_requested_url"]
                    validation_url ||= "https://securepay.sslcommerz.com/validator/api/validationserverAPI.php"
                  end
                  val_id = params[:val_id]
                  requested_url=validation_url+"?val_id="+val_id+"&store_id="+@store_id+"&store_passwd="+@store_password  
                  uri = URI.parse(requested_url)


                  http = Net::HTTP.new(uri.host, uri.port)
                  http.use_ssl = true

                  request = Net::HTTP::Post.new(uri.request_uri)
                  response = http.request(request)
                
                
                  @ssl_data = JSON::parse(response.body)
                  unless @ssl_data['status'] == "VALID"
                    gateway_status = false
                  end
                end
              end

              amount_from_gateway = 0
              if @active_gateway == "Paypal"
                amount_from_gateway = params[:amt]
              elsif @active_gateway == "Authorize.net"
                amount_from_gateway = params[:x_amount]
              elsif @active_gateway == "ssl.commerce"
                amount_from_gateway=params[:amount]
              end
              trans_id=@financefee.fee_transactions.collect(&:finance_transaction_id).join(",")
           
              if gateway_status == true
              
                trans_id=@financefee.fee_transactions.collect(&:finance_transaction_id).join(",")
                
                unless @financefee.is_paid?
                  unless amount_from_gateway.to_f < 0
                   
                    unless amount_from_gateway.to_f > Champs21Precision.set_and_modify_precision(total_fees).to_f
                      
                      
                      transaction = FinanceTransaction.new
                      transaction.title = "#{t('receipt_no')}. F#{@financefee.id}"
                      transaction.category = FinanceTransactionCategory.find_by_name("Fee")
                      transaction.payee = @student
                      transaction.finance = @financefee
                      transaction.amount = total_fees
                      transaction.fine_included = (@fine.to_f + @fine_amount.to_f).zero? ? false : true
                      transaction.fine_amount = @fine.to_f + @fine_amount.to_f
                      transaction.transaction_date = Date.today
                      transaction.payment_mode = "Online Payment"
                      transaction.save
                      if transaction.save
                        total_fine_amount = 0
                        unless (@fine.to_f + @fine_amount.to_f).zero?
                          total_fine_amount = @fine.to_f + @fine_amount.to_f
                        end
                        is_paid =@financefee.balance==0 ? true : false
                        @financefee.update_attributes( :is_paid=>is_paid)

                        @paid_fees = @financefee.finance_transactions

                        proccess_particulars_category = []
                        loop_particular = 0
                        @fee_particulars.each do |fp|
                          finance_fee_particular_category_id = fp.finance_fee_particular_category_id
                          unless proccess_particulars_category.include?(finance_fee_particular_category_id)
                            proccess_particulars_category[loop_particular] = finance_fee_particular_category_id
                            f_particular = @fee_particulars.select{|fpp| fpp.finance_fee_particular_category_id == finance_fee_particular_category_id }
                            amount_particular = f_particular.map{|f_p| f_p.amount.to_f}.sum

                            onetime_discounts = @onetime_discounts.select{ |od| od.finance_fee_particular_category_id == finance_fee_particular_category_id }
                            unless onetime_discounts.nil? or onetime_discounts.empty? 
                              onetime_discounts.each do |od|
                                amount_particular = amount_particular - @onetime_discounts_amount[od.id]
                              end
                            end
                            unless @discounts.blank?
                              discounts = @discounts.select{ |od| od.finance_fee_particular_category_id == finance_fee_particular_category_id }
                              unless discounts.nil? or discounts.empty? 
                                discounts.each do |od|
                                  amount_particular = amount_particular - @discounts_amount[od.id]
                                end
                              end
                            end
                  
                            finance_transaction_particular = FinanceTransactionParticular.new
                            finance_transaction_particular.finance_transaction_id = transaction.id
                            finance_transaction_particular.particular_id = finance_fee_particular_category_id
                            finance_transaction_particular.particular_type = 'ParticularCategory'
                            finance_transaction_particular.amount = amount_particular
                            finance_transaction_particular.transaction_date = transaction.transaction_date
                            finance_transaction_particular.save
                            loop_particular = loop_particular + 1
                          end
                        end
            
                        @onetime_discounts.each do |od|
                          finance_transaction_particular = FinanceTransactionParticular.new
                          finance_transaction_particular.finance_transaction_id = transaction.id
                          finance_transaction_particular.particular_id = od.id
                          finance_transaction_particular.particular_type = 'OnetimeDiscount'
                          finance_transaction_particular.amount = @onetime_discounts_amount[od.id]
                          finance_transaction_particular.transaction_date = transaction.transaction_date
                          finance_transaction_particular.save
                        end
                        unless @discounts.blank?
                          @discounts.each do |od|
                            finance_transaction_particular = FinanceTransactionParticular.new
                            finance_transaction_particular.finance_transaction_id = transaction.id
                            finance_transaction_particular.particular_id = od.id
                            finance_transaction_particular.particular_type = 'Discount'
                            finance_transaction_particular.amount = @discounts_amount[od.id]
                            finance_transaction_particular.transaction_date = transaction.transaction_date
                            finance_transaction_particular.save
                          end
                        end
            
                        if transaction.vat_included?
                          finance_transaction_particular = FinanceTransactionParticular.new
                          finance_transaction_particular.finance_transaction_id = transaction.id
                          finance_transaction_particular.particular_id = 0
                          finance_transaction_particular.particular_type = 'Vat'
                          finance_transaction_particular.amount = transaction.vat_amount
                          finance_transaction_particular.transaction_date = transaction.transaction_date
                          finance_transaction_particular.save
                        end
            
                        if total_fine_amount
                          finance_transaction_particular = FinanceTransactionParticular.new
                          finance_transaction_particular.finance_transaction_id = transaction.id
                          finance_transaction_particular.particular_id = 0
                          finance_transaction_particular.particular_type = 'Fine'
                          finance_transaction_particular.amount = total_fine_amount
                          finance_transaction_particular.transaction_date = transaction.transaction_date
                          finance_transaction_particular.save
                        end
                        if @has_fine_discount
                          @discounts_on_lates.each do |od|
                            finance_transaction_particular = FinanceTransactionParticular.new
                            finance_transaction_particular.finance_transaction_id = transaction.id
                            finance_transaction_particular.particular_id = od.id
                            finance_transaction_particular.particular_type = 'Fine Discount'
                            finance_transaction_particular.amount = @discounts_late_amount[od.id]
                            finance_transaction_particular.transaction_date = transaction.transaction_date
                            finance_transaction_particular.save
                          end
                        end
                      end
                      payment.update_attributes(:finance_transaction_id => transaction.id)
                      unless @financefee.transaction_id.nil?
                        tid =   @financefee.transaction_id.to_s + ",#{transaction.id}"
                      else
                        tid=transaction.id
                      end
                      is_paid = @financefee.balance==0 ? true : false



                      @financefee.update_attributes(:transaction_id=>tid, :is_paid=>is_paid)
                      @paid_fees = FinanceTransaction.find(:all,:conditions=>"FIND_IN_SET(id,\"#{tid}\")")
                      online_transaction_id = payment.gateway_response[:transaction_id]
                      online_transaction_id ||= payment.gateway_response[:x_trans_id]
                      
                      g_data = Guardian.find_by_user_id(current_user.id);
                      if !g_data.blank? && !g_data.email.blank?
                        header_txt = "#{t('payment_success')} #{online_transaction_id}"
                        body_txt = render_to_string(:template => 'gateway_payments/paypal/student_fee_receipt', :layout => false)
                        champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
                        api_endpoint = champs21_api_config['api_url']
                        form_data = {}
                        form_data['body'] = body_txt
                        form_data['header'] = header_txt
                        form_data['email'] = g_data.email
                        form_data['first_name'] = g_data.first_name
                        form_data['last_name'] = g_data.last_name

                        api_uri = URI(api_endpoint + "api/user/paymentmail")


                        http = Net::HTTP.new(api_uri.host, api_uri.port)
                        request = Net::HTTP::Post.new(api_uri.path)
                        request.set_form_data(form_data)
                        http.request(request)
                      end 
                      
                      
                      

                      flash[:success] = "#{t('payment_success')} #{online_transaction_id}"
                    else
                      flash[:notice] = "#{t('payment_failed')}"
                    end
                  else
                    flash[:notice] = "#{t('payment_failed')}"
                  end
                else
                  flash[:notice] = "#{t('flash_payed')}"
                end
              else
                flash[:notice] = "#{t('payment_failed')}"
              end 
            else
              flash[:notice] = "#{t('payment_failed')}"
            end 
          
          end
          @fine_amount=0 if (@student.finance_fee_by_date @date).is_paid
          unless params[:mobile_view].blank?
            render 'gateway_payments/paypal/mobile_fee_details',:layout => false
          else
            render 'gateway_payments/paypal/fee_details'
          end
        else
          fee_details_without_gateway
        end
      else
        fee_details_without_gateway
      end
    end
    
    def date_format(date)
      /(\d{4}-\d{2}-\d{2})/.match(date)
    end

    def calculate_discount(date,batch,student,is_paid)
      one_time_discount = false
      one_time_total_amount_discount = false
      onetime_discount_particulars_id = []

      one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      @onetime_discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      if @onetime_discounts.length > 0
        one_time_total_amount_discount= true
        @onetime_discounts_amount = []
        @onetime_discounts.each do |d|
          @onetime_discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
          @total_discount = @total_discount + @onetime_discounts_amount[d.id]
        end
      else
        @onetime_discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @onetime_discounts.length > 0
          one_time_discount = true
          @onetime_discounts_amount = []
          i = 0
          @onetime_discounts.each do |d|   
            onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            @onetime_discounts_amount[d.id] = discount_amt
            @total_discount = @total_discount + discount_amt
            i = i + 1
          end
        end
      end

      unless one_time_total_amount_discount
        if onetime_discount_particulars_id.empty?
          onetime_discount_particulars_id[0] = 0
        end
        discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if discounts_on_particulars.length > 0
          @discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          @discounts_amount = []
          @discounts.each do |d|   
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            @discounts_amount[d.id] = discount_amt
            @total_discount = @total_discount + discount_amt
          end
        else  
          unless one_time_discount
            @discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            @discounts_amount = []
            @discounts.each do |d|
              @discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
              @total_discount = @total_discount + @discounts_amount[d.id]
            end
          end
        end
      end
    end
  
    def calculate_discount_index_all(date,batch,student,ind,is_paid)
      one_time_discount = false
      one_time_total_amount_discount = false
      onetime_discount_particulars_id = []
    
      one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      if @all_onetime_discounts[ind].length > 0
        one_time_total_amount_discount= true
        @all_onetime_discounts_amount[ind] = []
        @all_onetime_discounts[ind].each do |d|
          @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
          @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
        end
      else
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @all_onetime_discounts[ind].length > 0
          one_time_discount = true
          @all_onetime_discounts_amount[ind] = []
          i = 0
          @all_onetime_discounts[ind].each do |d|   
            onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            @all_onetime_discounts_amount[ind][d.id] = discount_amt
            @all_total_discount[ind] = @all_total_discount[ind] + discount_amt
            i = i + 1
          end
        end
      end

      unless one_time_total_amount_discount
        if onetime_discount_particulars_id.empty?
          onetime_discount_particulars_id[0] = 0
        end
        discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if discounts_on_particulars.length > 0
          @all_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          @all_discounts_amount[ind] = []
          @all_discounts[ind].each do |d|   
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            @all_discounts_amount[ind][d.id] = discount_amt
            @all_total_discount[ind] = @all_total_discount[ind] + discount_amt
          end
        else  
          unless one_time_discount
            @all_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            @all_discounts_amount[ind] = []
            @all_discounts[ind].each do |d|
              @all_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
              @all_total_discount[ind] = @all_total_discount[ind] + @all_discounts_amount[ind][d.id]
            end
          end
        end
      end
    end
  
    def calculate_discount_index(date,batch,student,ind,is_paid)
      one_time_discount = false
      one_time_total_amount_discount = false
      onetime_discount_particulars_id = []
    
      one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      if @onetime_discounts[ind].length > 0
        one_time_total_amount_discount= true
        @onetime_discounts_amount[ind] = []
        @onetime_discounts[ind].each do |d|
          @onetime_discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
          @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
        end
      else
        @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @onetime_discounts[ind].length > 0
          one_time_discount = true
          @onetime_discounts_amount[ind] = []
          i = 0
          @onetime_discounts[ind].each do |d|   
            onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            @onetime_discounts_amount[ind][d.id] = discount_amt
            @total_discount[ind] = @total_discount[ind] + discount_amt
            i = i + 1
          end
        end
      end

      unless one_time_total_amount_discount
        if onetime_discount_particulars_id.empty?
          onetime_discount_particulars_id[0] = 0
        end
        discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if discounts_on_particulars.length > 0
          @discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          @discounts_amount[ind] = []
          @discounts[ind].each do |d|   
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            @discounts_amount[ind][d.id] = discount_amt
            @total_discount[ind] = @total_discount[ind] + discount_amt
          end
        else  
          unless one_time_discount
            @discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            @discounts_amount[ind] = []
            @discounts[ind].each do |d|
              @discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
              @total_discount[ind] = @total_discount[ind] + @discounts_amount[ind][d.id]
            end
          end
        end
      end
    end
  
    def calculate_new_discount(date,batch,student,total_payable)
      total_discount = 0
      one_time_discount = false
      one_time_total_amount_discount = false
      onetime_discount_particulars_id = []

      one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      onetime_discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      if onetime_discounts.length > 0
        one_time_total_amount_discount= true
        onetime_discounts_amount = []
        onetime_discounts.each do |d|
          onetime_discounts_amount[d.id] = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
          total_discount = total_discount + onetime_discounts_amount[d.id]
        end
      else
        onetime_discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if onetime_discounts.length > 0
          one_time_discount = true
          onetime_discounts_amount = []
          i = 0
          onetime_discounts.each do |d|   
            onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            onetime_discounts_amount[d.id] = discount_amt
            total_discount = total_discount + discount_amt
            i = i + 1
          end
        end
      end

      unless one_time_total_amount_discount
        if onetime_discount_particulars_id.empty?
          onetime_discount_particulars_id[0] = 0
        end
        discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if discounts_on_particulars.length > 0
          discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          discounts_amount = []
          discounts.each do |d|   
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            discounts_amount[d.id] = discount_amt
            total_discount = total_discount + discount_amt
          end
        else  
          unless one_time_discount
            discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            discounts_amount = []
            discounts.each do |d|
              discounts_amount[d.id] = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
              total_discount = total_discount + discounts_amount[d.id]
            end
          end
        end
      end
      return total_discount
    end
    
    def calculate_extra_fine(date,batch,student,fine_rule)
      if MultiSchool.current_school.id == 340
        #GET THE NEXT ALL months 
        extra_fine = 0
        other_months = FinanceFeeCollection.find(:all, :conditions => ["due_date > ? and is_deleted=#{false}", date.due_date], :order => "due_date asc")
        unless other_months.nil? or other_months.empty?
          other_months.each do |other_month|
            fee_for_batch = FeeCollectionBatch.find(:all, :conditions => ["batch_id = ? and is_deleted=#{false} and finance_fee_collection_id != ?", batch.id, date.id])
            unless fee_for_batch.nil? or fee_for_batch.empty?
              fine_amount = fine_rule.fine_amount if fine_rule
              extra_fine = extra_fine + fine_amount
            end
          end
        end
        
        @fine_amount = @fine_amount + extra_fine
      end
    end
  
    def calculate_extra_fine_index_all(date,batch,student,fine_rule,ind)
      if MultiSchool.current_school.id == 340
        #GET THE NEXT ALL months 
        extra_fine = 0
        other_months = FinanceFeeCollection.find(:all, :conditions => ["due_date > ? and is_deleted=#{false}", date.due_date], :order => "due_date asc")
        unless other_months.nil? or other_months.empty?
          other_months.each do |other_month|
            fee_for_batch = FeeCollectionBatch.find(:all, :conditions => ["batch_id = ? and is_deleted=#{false} and finance_fee_collection_id != ?", batch.id, date.id])
            unless fee_for_batch.nil? or fee_for_batch.empty?
              fine_amount = fine_rule.fine_amount if fine_rule
              extra_fine = extra_fine + fine_amount
            end
          end
        end
        @all_fine_amount[ind] = @all_fine_amount[ind] + extra_fine
      end
    end
  
    def calculate_extra_fine_index(date,batch,student,fine_rule,ind)
      if MultiSchool.current_school.id == 340
        #GET THE NEXT ALL months 
         extra_fine = 0
        other_months = FinanceFeeCollection.find(:all, :conditions => ["due_date > ? and is_deleted=#{false}", date.due_date], :order => "due_date asc")
        unless other_months.nil? or other_months.empty?
          other_months.each do |other_month|
            fee_for_batch = FeeCollectionBatch.find(:all, :conditions => ["batch_id = ? and is_deleted=#{false} and finance_fee_collection_id != ?", batch.id, date.id])
            unless fee_for_batch.nil? or fee_for_batch.empty?
              fine_amount = fine_rule.fine_amount if fine_rule
              extra_fine = extra_fine + fine_amount
            end
          end
        end
        @fine_amount[ind] = @fine_amount[ind] + extra_fine
      end
    end
  
    def get_fine_discount(date,batch,student)
      if !@fine_amount.blank? and @fine_amount > 0
        fee_collection_discount_ids = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id_and_is_late(date.id, batch.id, true).map(&:fee_discount_id)
        unless fee_collection_discount_ids.nil? or fee_collection_discount_ids.empty?
          @discounts_on_lates = FeeDiscount.find(:all, :conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{true} and id IN (" + fee_collection_discount_ids.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          if @discounts_on_lates.length > 0
            @has_fine_discount = true
            @discounts_late_amount = []
            @discounts_on_lates.each do |d|   
              if @fine_amount > 0
                discount_amt = @new_fine_amount * d.discount.to_f/ (d.is_amount?? @new_fine_amount : 100)
                @fine_amount = @fine_amount - discount_amt
                if @fine_amount < 0
                  discount_amt = 0
                end
                @discounts_late_amount[d.id] = discount_amt
              else
                @discounts_late_amount[d.id] = 0
              end
            end
          end
        end
      else
        @fine_amount = 0
      end  
    end
  
    def get_fine_discount_index(date,batch,student,ind)
      if @fine_amount[ind] > 0
        fee_collection_discount_ids = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id_and_is_late(date.id, batch.id, true).map(&:fee_discount_id)
        unless fee_collection_discount_ids.nil? or fee_collection_discount_ids.empty?
          @discounts_on_lates[ind] = FeeDiscount.find(:all, :conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{true} and id IN (" + fee_collection_discount_ids.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          if @discounts_on_lates[ind].length > 0
            @has_fine_discount[ind] = true
            @discounts_late_amount[ind] = []
            @discounts_on_lates[ind].each do |d|   
              if @fine_amount[ind] > 0
                discount_amt = @new_fine_amount[ind] * d.discount.to_f/ (d.is_amount?? @new_fine_amount[ind] : 100)
                @fine_amount[ind] = @fine_amount[ind] - discount_amt
                if @fine_amount[ind] < 0
                  discount_amt = 0
                end
                @discounts_late_amount[ind][d.id] = discount_amt
              else
                @discounts_late_amount[ind][d.id] = 0
              end
            end
          end
        end
      end
    end
  
  
    def get_fine_discount_index_all(date,batch,student,ind)
    
      if @all_fine_amount[ind] > 0
        fee_collection_discount_ids = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id_and_is_late(date.id, batch.id, true).map(&:fee_discount_id)
        unless fee_collection_discount_ids.nil? or fee_collection_discount_ids.empty?
          @all_discounts_on_lates[ind] = FeeDiscount.find(:all, :conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{true} and id IN (" + fee_collection_discount_ids.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          if @all_discounts_on_lates[ind].length > 0
            @all_has_fine_discount[ind] = true
            @all_discounts_late_amount[ind] = []
            @all_discounts_on_lates[ind].each do |d|   
              if @all_fine_amount[ind] > 0
                discount_amt = @all_new_fine_amount[ind] * d.discount.to_f/ (d.is_amount?? @all_new_fine_amount[ind] : 100)
                @all_fine_amount[ind] = @all_fine_amount[ind] - discount_amt
                if @all_fine_amount[ind] < 0
                  discount_amt = 0
                end
                @all_discounts_late_amount[ind][d.id] = discount_amt
              else
                @all_discounts_late_amount[ind][d.id] = 0
              end
            end
          end
        end
      end
    end
  end

  #URI.parse("http://192.168.1.30:3000/student/fee_details/#{params[:id]}/#{params[:id2]}?create_transaction=1")

  module StudentPayReceipt

    def self.included(base)
      base.alias_method_chain :student_fee_receipt_pdf,:gateway
    end

    def student_fee_receipt_pdf_with_gateway
      if Champs21Plugin.can_access_plugin?("champs21_pay")
        @active_gateway = PaymentConfiguration.config_value("champs21_gateway")
        if @active_gateway.nil?
          student_fee_receipt_pdf_without_gateway and return
        end
        if (PaymentConfiguration.config_value("enabled_fees").present? and PaymentConfiguration.config_value("enabled_fees").include? "Student Fee")
          @date = @fee_collection = FinanceFeeCollection.find(params[:id2])
          @student = Student.find(params[:id])
          @batch=@student.batch
          @student_has_due = false
          @std_finance_fee_due = FinanceFee.find(:first,:conditions=>["finance_fee_collections.due_date < ? and finance_fees.is_paid = 0 and finance_fees.student_id = ?", @date.due_date,@student.id],:include=>"finance_fee_collection")
          unless @std_finance_fee_due.blank?
            @student_has_due = true
          end
          @financefee = @student.finance_fee_by_date @date
          @due_date = @fee_collection.due_date
          @paid_fees = @financefee.finance_transactions
          @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])

          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch)}
          @total_discount = 0
          @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f

          calculate_discount(@date, @student.batch, @student, @financefee.is_paid)

          bal=(@total_payable-@total_discount).to_f
          days=(Date.today-@date.due_date.to_date).to_i
          auto_fine=@date.fine

          @has_fine_discount = false
          if days > 0 and auto_fine
            @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
            @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
            calculate_extra_fine(@date, @batch, @student, @fine_rule)
            @new_fine_amount = @fine_amount
            get_fine_discount(@date, @batch, @student)
            if @fine_amount < 0
              @fine_amount = 0
            end
          end

          @fine_amount=0 if @financefee.is_paid
          @has_fine_discount = false if @financefee.is_paid

          respond_to do |format|
            format.pdf do
              render :pdf => "student_fee_receipt",
                :template => 'gateway_payments/paypal/student_fee_receipt_pdf',
                :orientation => 'Landscape', :zoom => 1.00,
                :page_size => 'A4',
                :margin => {    :top=> 10,
                :bottom => 0,
                :left=> 10,
                :right => 10},
                :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
                :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
            end
          end
        else
          student_fee_receipt_pdf_without_gateway
        end
      else
        student_fee_receipt_pdf_without_gateway
      end
    end
  end
end
