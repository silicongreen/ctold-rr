class PaymentSettingsController < ApplicationController
  require 'will_paginate/array'
  before_filter :login_required
  filter_access_to :all

  def index
    
  end

  def verify_payment
    #Initial Built for Validate Trust Bank Payment for SAGC (may modify further for different School)
    require 'net/http'
    require 'soap/wsdlDriver'
    require 'uri'
    require "yaml"
    require 'nokogiri'
    
    if MultiSchool.current_school.id == 352
      unless params[:payment_id].nil?
        payment_id = params[:payment_id]
        payment = Payment.find(payment_id)
        
        testtrustbank = false
        if PaymentConfiguration.config_value('is_test_testtrustbank').to_i == 1
          if File.exists?("#{Rails.root}/vendor/plugins/champs21_pay/config/payment_config_tcash.yml")
            payment_configs = YAML.load_file(File.join(Rails.root,"vendor/plugins/champs21_pay/config/","payment_config_tcash.yml"))
            unless payment_configs.nil? or payment_configs.empty? or payment_configs.blank?
              testtrustbank = payment_configs["testtrustbank"]
            end
          end
        end
        if testtrustbank
          merchant_info = payment_configs["merchant_info_" + MultiSchool.current_school.id.to_s]
          @merchant_id = merchant_info["merchant_id"]
          @keycode = merchant_info["keycode"]
          @verification_url = merchant_info["validation_api"]
          @merchant_id ||= String.new
          @keycode ||= String.new
          @verification_url ||= "https://ibanking.tblbd.com/TestCheckout/Services/Payment_Info.asmx"
        else  
          if File.exists?("#{Rails.root}/vendor/plugins/champs21_pay/config/online_payment_url.yml")
            payment_urls = YAML.load_file(File.join(Rails.root,"vendor/plugins/champs21_pay/config/","online_payment_url.yml"))
            @verification_url = payment_urls["trustbank_verification_url"]
            @verification_url ||= "https://ibanking.tblbd.com/Checkout/Services/Payment_Info.asmx"
          else
            @verification_url ||= "https://ibanking.tblbd.com/Checkout/Services/Payment_Info.asmx"
          end
          @merchant_id = PaymentConfiguration.config_value("merchant_id")
          @keycode = PaymentConfiguration.config_value("keycode_verification")
          @merchant_id ||= String.new
          @keycode ||= String.new
        end
        request_url = @verification_url + '/Get_Transaction_Ref'
        #requested_url = request_url + "?OrderID=" + payment.gateway_response[:order_id] + "&MerchantID=" + @merchant_id + "&KeyCode=" + @keycode  
        
        uri = URI(request_url)
        http = Net::HTTP.new(uri.host, uri.port)
        auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
        auth_req.set_form_data({"OrderID" => payment.gateway_response[:order_id], "MerchantID" => @merchant_id, "KeyCode" => @keycode})
        
        http.use_ssl = true
        auth_res = http.request(auth_req)
        
        xml_res = Nokogiri::XML(auth_res.body)
        
        status = ""
        unless xml_res.xpath("/").empty?
          status = xml_res.xpath("/").text
        end
        
        result = Base64.decode64(status)
        
        ref_id = ""
        orderId = ""
        name = ""
        email = ""
        amount = 0.00
        service_charge = 0.00
        total_amount = 0.00
        status = 0
        status_text = ""
        used = ""
        verified = 0
        payment_type = ""
        pan = ""
        tbbmm_account = ""
        merchant_id = ""
        order_datetime = ""
        trans_date = ""
        emi_no = ""
        interest_amount = ""
        pay_with_charge = ""
        card_response_code = ""
        card_response_desc = ""
        card_order_status = ""
        abort(result.inspect)
        xml_str = Nokogiri::XML(result)
        
        xml_transaction_info = xml_str.xpath("//Response/TransactionInfo")
        childs = xml_transaction_info[xml_transaction_info.length - 1].children
        #abort(childs.inspect)
        childs.each do |c|
          if c.name == "RefID"
            ref_id = c.text
          elsif c.name == "OrderID"
            orderId = c.text
          elsif c.name == "Name"
            name = c.text
          elsif c.name == "Email"
            email = c.text
          elsif c.name == "Amount"
            amount = c.text
          elsif c.name == "ServiceCharge"
            service_charge = c.text
          elsif c.name == "TotalAmount"
            total_amount = c.text
          elsif c.name == "Status"
            status = c.text
          elsif c.name == "StatusText"
            status_text = c.text
          elsif c.name == "Used"
            used = c.text
          elsif c.name == "Verified"
            verified = c.text
          elsif c.name == "PaymentType"
            payment_type = c.text
          elsif c.name == "PAN"
            pan = c.text
          elsif c.name == "TBMM_Account"
            tbbmm_account = c.text
          elsif c.name == "MarchentID"
            merchant_id = c.text
          elsif c.name == "OrderDateTime"
            order_datetime = c.text
          elsif c.name == "PaymentDateTime"
            trans_date = c.text
          elsif c.name == "EMI_No"
            emi_no = c.text
          elsif c.name == "InterestAmount"
            interest_amount = c.text
          elsif c.name == "PayWithCharge"
            pay_with_charge = c.text
          elsif c.name == "CardResponseCode"
            card_response_code = c.text
          elsif c.name == "CardResponseDescription"
            card_response_desc = c.text
          elsif c.name == "CardOrderStatus"
            card_order_status = c.text
          end
          
        end
        
        
        gateway_response = {
          :total_amount => total_amount,
          :amount => amount,
          :name => name,
          :email => email,
          :merchant_id => merchant_id,
          :order_datetime => order_datetime,
          :emi_no => emi_no,
          :tbbmm_account => tbbmm_account,
          :interest_amount => interest_amount,
          :pay_with_charge => pay_with_charge,
          :card_response_code => card_response_code,
          :card_response_desc => card_response_desc,
          :card_order_status => card_order_status,
          :used => used,
          :verified => verified,
          :status_text => status_text,
          :status => status,
          :ref_id => ref_id,
          :order_id=>orderId,
          :tran_date=>trans_date,
          :payment_type=>payment_type,
          :service_charge=>service_charge,
          :pan=>pan
        }
        
        request_url = @verification_url + '/Transaction_Verify_Details'
        #requested_url = request_url + "?OrderID=" + payment.gateway_response[:order_id] + "&MerchantID=" + @merchant_id + "&KeyCode=" + @keycode  
        
        uri = URI(request_url)
        http = Net::HTTP.new(uri.host, uri.port)
        auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
        auth_req.set_form_data({"OrderID" => payment.gateway_response[:order_id], "MerchantID" => @merchant_id, "RefID" => ref_id})
        
        http.use_ssl = true
        auth_res = http.request(auth_req)
        
        xml_res = Nokogiri::XML(auth_res.body)
        status = ""
        unless xml_res.xpath("/").empty?
          status = xml_res.xpath("/").text
        end
        
        result = Base64.decode64(status)
        #abort(result.inspect)
        verification_ref_id = ""
        verification_orderId = ""
        verification_name = ""
        verification_email = ""
        verification_amount = 0.00
        verification_service_charge = 0.00
        verification_total_amount = 0.00
        verification_status = 0
        verification_status_text = ""
        verification_used = ""
        verification_verified = 0
        verification_payment_type = ""
        verification_pan = ""
        verification_tbbmm_account = ""
        verification_merchant_id = ""
        verification_order_datetime = ""
        verification_trans_date = ""
        verification_emi_no = ""
        verification_interest_amount = ""
        verification_pay_with_charge = ""
        verification_card_response_code = ""
        verification_card_response_desc = ""
        verification_card_order_status = ""
        
        xml_str = Nokogiri::XML(result)
        
        unless xml_str.xpath("//Response/RefID").empty?
          verification_ref_id = xml_str.xpath("//Response/RefID").text
        end
        unless xml_str.xpath("//Response/OrderID").empty?
          verification_orderId = xml_str.xpath("//Response/OrderID").text
        end
        unless xml_str.xpath("//Response/Name").empty?
          verification_name = xml_str.xpath("//Response/Name").text
        end
        unless xml_str.xpath("//Response/Email").empty?
          verification_email = xml_str.xpath("//Response/Email").text
        end
        unless xml_str.xpath("//Response/Amount").empty?
          verification_amount = xml_str.xpath("//Response/Amount").text
        end
        unless xml_str.xpath("//Response/ServiceCharge").empty?
          verification_service_charge = xml_str.xpath("//Response/ServiceCharge").text
        end
        unless xml_str.xpath("//Response/TotalAmount").empty?
          verification_total_amount = xml_str.xpath("//Response/TotalAmount").text
        end
        unless xml_str.xpath("//Response/Status").empty?
          verification_status = xml_str.xpath("//Response/Status").text
        end
        unless xml_str.xpath("//Response/StatusText").empty?
          verification_status_text = xml_str.xpath("//Response/StatusText").text
        end
        unless xml_str.xpath("//Response/Used").empty?
          verification_used = xml_str.xpath("//Response/Used").text
        end
        unless xml_str.xpath("//Response/Verified").empty?
          verification_verified = xml_str.xpath("//Response/Verified").text
        end
        unless xml_str.xpath("//Response/PaymentType").empty?
          verification_payment_type = xml_str.xpath("//Response/PaymentType").text
        end
        unless xml_str.xpath("//Response/PAN").empty?
          verification_pan = xml_str.xpath("//Response/PAN").text
        end
        unless xml_str.xpath("//Response/TBMM_Account").empty?
          verification_tbbmm_account = xml_str.xpath("//Response/TBMM_Account").text
        end
        unless xml_str.xpath("//Response/MarchentID").empty?
          verification_merchant_id = xml_str.xpath("//Response/MarchentID").text
        end
        unless xml_str.xpath("//Response/OrderDateTime").empty?
          verification_order_datetime = xml_str.xpath("//Response/OrderDateTime").text
        end
        unless xml_str.xpath("//Response/PaymentDateTime").empty?
          verification_trans_date = xml_str.xpath("//Response/PaymentDateTime").text
        end
        unless xml_str.xpath("//Response/EMI_No").empty?
          verification_emi_no = xml_str.xpath("//Response/EMI_No").text
        end
        unless xml_str.xpath("//Response/InterestAmount").empty?
          verification_interest_amount = xml_str.xpath("//Response/InterestAmount").text
        end
        unless xml_str.xpath("//Response/PayWithCharge").empty?
          verification_pay_with_charge = xml_str.xpath("//Response/PayWithCharge").text
        end
        unless xml_str.xpath("//Response/CardResponseCode").empty?
          verification_card_response_code = xml_str.xpath("//Response/CardResponseCode").text
        end
        unless xml_str.xpath("//Response/CardResponseDescription").empty?
          verification_card_response_desc = xml_str.xpath("//Response/CardResponseDescription").text
        end
        unless xml_str.xpath("//Response/CardOrderStatus").empty?
          verification_card_order_status = xml_str.xpath("//Response/CardOrderStatus").text
        end
        
        validation_response = {
          :total_amount => verification_total_amount,
          :amount => verification_amount,
          :name => verification_name,
          :email => verification_email,
          :merchant_id => verification_merchant_id,
          :order_datetime => verification_order_datetime,
          :emi_no => verification_emi_no,
          :tbbmm_account => verification_tbbmm_account,
          :interest_amount => verification_interest_amount,
          :pay_with_charge => verification_pay_with_charge,
          :card_response_code => verification_card_response_code,
          :card_response_desc => verification_card_response_desc,
          :card_order_status => verification_card_order_status,
          :used => verification_used,
          :verified => verification_verified,
          :status_text => verification_status_text,
          :status => verification_status,
          :ref_id => verification_ref_id,
          :order_id=>verification_orderId,
          :tran_date=>verification_trans_date,
          :payment_type=>verification_payment_type,
          :service_charge=>verification_service_charge,
          :pan=>verification_pan
        }
        
        
        
        if verified.to_i == 1 or verification_verified.to_i == 1
          if verified.to_i == 0
            if verification_verified.to_i == 1
              gateway_response = validation_response
            end
          end
          finance_fee_id = payment.payment_id
          payee_id = payment.payee_id
          fee = FinanceFee.find(:first, :conditions => "id = #{finance_fee_id} and student_id = #{payee_id}")
          @student = Student.find(payee_id)
          @batch = @student.batch
          unless fee.nil?
            unless fee.is_paid
              fee_collection_id = fee.fee_collection_id
              advance_fee_collection = false
              @self_advance_fee = false
              @fee_has_advance_particular = false

              @date = @fee_collection = FinanceFeeCollection.find(fee_collection_id)
              @student_has_due = false
              @std_finance_fee_due = FinanceFee.find(:first,:conditions=>["finance_fee_collections.due_date < ? and finance_fees.is_paid = 0 and finance_fees.student_id = ?", @date.due_date,@student.id],:include=>"finance_fee_collection")
              unless @std_finance_fee_due.blank?
                @student_has_due = true
              end
              @financefee = @student.finance_fee_by_date(@date)

              if @financefee.has_advance_fee_id
                if @date.is_advance_fee_collection
                  @self_advance_fee = true
                  advance_fee_collection = true
                end
                @fee_has_advance_particular = true
                @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
                @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
              end

              @due_date = @fee_collection.due_date
              @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])

              @paid_fees = @financefee.finance_transactions

              if advance_fee_collection
                fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
                if fee_collection_advances_particular.include?(0)
                  @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
                else
                  @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
                end
              else
                @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
              end

              if advance_fee_collection
                month = 1
                payable = 0
                @fee_collection_advances.each do |fee_collection_advance|
                  @fee_particulars.each do |particular|
                    if fee_collection_advance.particular_id == particular.finance_fee_particular_category_id
                      payable += particular.amount * fee_collection_advance.no_of_month.to_i
                    else
                      payable += particular.amount
                    end
                  end
                end
                @total_payable=payable.to_f
              else  
                @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
              end

              @total_discount = 0

              #calculate_discount(@date, @financefee.batch, @student, @financefee.is_paid)
              @adv_fee_discount = false
              @actual_discount = 1

              if advance_fee_collection
                calculate_discount(@date, @batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
              else
                if @fee_has_advance_particular
                  calculate_discount(@date, @batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
                else
                  calculate_discount(@date, @batch, @student, false, nil, @fee_has_advance_particular)
                end
              end

              bal=(@total_payable-@total_discount).to_f

              require 'date'
              days=(verification_trans_date.to_date - @date.due_date.to_date).to_i

              auto_fine=@date.fine

              @has_fine_discount = false
              if days > 0 and auto_fine #and @financefee.is_paid == false
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

              unless advance_fee_collection
                if @total_discount == 0
                  @adv_fee_discount = true
                  @actual_discount = 0
                  calculate_discount(@date, @batch, @student, false, nil, @fee_has_advance_particular)
                end
              end

              total_fees = @financefee.balance.to_f+@fine_amount.to_f

              if amount.to_f > 0
                if amount.to_f == Champs21Precision.set_and_modify_precision(total_fees).to_f
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
                      particular_amount = fp.amount.to_f
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = fp.id
                      finance_transaction_particular.particular_type = 'Particular'
                      finance_transaction_particular.transaction_type = 'Fee Collection'
                      finance_transaction_particular.amount = particular_amount
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save
                    end

                    unless @onetime_discounts.blank?
                      @onetime_discounts.each do |od|
                        discount_amount = @onetime_discounts_amount[od.id].to_f
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = od.id
                        finance_transaction_particular.particular_type = 'Adjustment'
                        finance_transaction_particular.transaction_type = 'Discount'
                        finance_transaction_particular.amount = discount_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      end
                    end


                    unless @discounts.blank?
                      @discounts.each do |od|
                        discount_amount = @discounts_amount[od.id]
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = od.id
                        finance_transaction_particular.particular_type = 'Adjustment'
                        finance_transaction_particular.transaction_type = 'Discount'
                        finance_transaction_particular.amount = discount_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      end
                    end

                    if transaction.vat_included?
                      vat_amount = transaction.vat_amount
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = 0
                      finance_transaction_particular.particular_type = 'VAT'
                      finance_transaction_particular.transaction_type = ''
                      finance_transaction_particular.amount = vat_amount
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save
                    end

                    if total_fine_amount
                      fine_amount = total_fine_amount
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = 0
                      finance_transaction_particular.particular_type = 'Fine'
                      finance_transaction_particular.transaction_type = ''
                      finance_transaction_particular.amount = fine_amount
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save
                    end


                    if @has_fine_discount
                      @discounts_on_lates.each do |fd|
                        discount_amount = @discounts_late_amount[od.id]
                        discount_amount = params["fee_fine_discount_amount_" + fd.id.to_s].to_f
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = fd.id
                        finance_transaction_particular.particular_type = 'FineAdjustment'
                        finance_transaction_particular.transaction_type = 'Discount'
                        finance_transaction_particular.amount = discount_amount
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

                end
              end
            end
          end
          sms_setting = SmsSetting.new()
          if sms_setting.student_sms_active or sms_setting.parent_sms_active    
            message = "Fees received BDT #AMOUNT# for #UNAME#(#UID#) as on #PAIDDATE# by TBL. TranID-#TRANID# TranRef-#TRANREF#, Sender - SAGC"
            if File.exists?("#{Rails.root}/config/sms_text_#{MultiSchool.current_school.id}.yml")
              sms_text_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/sms_text_#{MultiSchool.current_school.id}.yml")['school']
              message = sms_text_config['feepaid']
            end
            recipients = []
            unless @student.sms_number.nil? or @student.sms_number.empty? or @student.sms_number.blank?
              message = message.gsub("#UNAME#", @student.full_name)
              message = message.gsub("#UID#", @student.admission_no)
              message = message.gsub("#AMOUNT#", amount.to_s)
              message = message.gsub("#PAIDDATE#", trans_date.to_date.strftime("%d-%m-%Y"))
              message = message.gsub("#TRANID#", orderId)
              message = message.gsub("#TRANREF#", ref_id)
              recipients.push @student.sms_number
            else
              unless @student.phone2.nil? or @student.phone2.empty? or @student.phone2.blank?
                message = message
                message = message.gsub("#UNAME#", @student.full_name)
                message = message.gsub("#UID#", @student.admission_no)
                message = message.gsub("#AMOUNT#", amount.to_s)
                message = message.gsub("#PAIDDATE#", trans_date.to_date.strftime("%d-%m-%Y"))
                message = message.gsub("#TRANID#", orderId)
                message = message.gsub("#TRANREF#", ref_id)
                recipients.push @student.phone2
              end
            end
            messages = []
            messages[0] = message
            #sms = Delayed::Job.enqueue(SmsManager.new(message,recipients))
            send_sms(messages,recipients)
          end
          
          paymentnew = Payment.find(payment.id)
          paymentnew.update_attributes(:gateway_response => gateway_response, :validation_response => validation_response)
          render :update do |page|
            page << "j('#payment#{payment.id}').removeClass('payment_verify');"
            page << "j('#payment#{payment.id}').removeClass('fa-square-o');"
            page << "j('#payment#{payment.id}').addClass('fa-check-square-o');"
            page << "j('#payment#{payment.id}').css('cursor','default');"
          end
        else
          render :update do |page|
            page << "alert('Still unverified please try again later')"
          end
        end
      end
    end
  end
  
  def transactions
#    #order_ids = [578295,183713,164960,313774,829086,116118,316269,218700,730609,582237,443344,206133,982115,35211,744726,238119,158413,818546,281728,299472,645537,489160,209061,604298,468086,582005,378562,46380,558208,580076,959126,219310,467155,730538,422603,226044,803161,871810,88918,305829,37984,208863,252379,670872,705585,202593,535532,750873,224746,189135,748423,648411,588957,956877,714919,261896,315153,590181,434118,320801,30913,599161,275793,319681,819909,650009,402711,537808,932751,716113,188536,994595,930841,688095,912503,21250,191039,570850,218572,863043,898196,693319,154917,740912,373785,242982,883858,997010,708797,877488,274153,413169,107202,675566,754385,403237,766840,478038,854723,28597,652614,299279,51740,635440,613291,466245,724483,446689,756258,774909,891350,142665,602192,820346,731291,229013,441398,771468,35242,389216,706950,626058,961271,918433,833185,661401,659944,244430,535780,705490,251140,866601,120695,157144,766891,416334,284051,225046,327909,115933,193054,862344,49123,647545,589797,535082,429234,9962,55591,239413,594570,527153,152674,76387,776635,224005,289457,773749,683572,243570,539289,672529,231428,618347,660098,927822,400085,457711,576043,466398,390335]
#    order_ids = [193054,862344,49123,647545,589797,535082,429234,9962,55591,239413,594570,527153,152674,76387,776635,224005,289457,773749,683572,243570,539289,672529,231428,618347,660098,927822,400085,457711,576043,466398,390335]
#    #order_ids = [578295]
#    
#    order_ids.each do |o|
#      testtrustbank = false
#        if PaymentConfiguration.config_value('is_test_testtrustbank').to_i == 1
#          if File.exists?("#{Rails.root}/vendor/plugins/champs21_pay/config/payment_config_tcash.yml")
#            payment_configs = YAML.load_file(File.join(Rails.root,"vendor/plugins/champs21_pay/config/","payment_config_tcash.yml"))
#            unless payment_configs.nil? or payment_configs.empty? or payment_configs.blank?
#              testtrustbank = payment_configs["testtrustbank"]
#            end
#          end
#        end
#        if testtrustbank
#          merchant_info = payment_configs["merchant_info_" + MultiSchool.current_school.id.to_s]
#          @merchant_id = merchant_info["merchant_id"]
#          @keycode = merchant_info["keycode"]
#          @verification_url = merchant_info["validation_api"]
#          @merchant_id ||= String.new
#          @keycode ||= String.new
#          @verification_url ||= "https://ibanking.tblbd.com/TestCheckout/Services/Payment_Info.asmx"
#        else  
#          if File.exists?("#{Rails.root}/vendor/plugins/champs21_pay/config/online_payment_url.yml")
#            payment_urls = YAML.load_file(File.join(Rails.root,"vendor/plugins/champs21_pay/config/","online_payment_url.yml"))
#            @verification_url = payment_urls["trustbank_verification_url"]
#            @verification_url ||= "https://ibanking.tblbd.com/Checkout/Services/Payment_Info.asmx"
#          else
#            @verification_url ||= "https://ibanking.tblbd.com/Checkout/Services/Payment_Info.asmx"
#          end
#          @merchant_id = PaymentConfiguration.config_value("merchant_id")
#          @keycode = PaymentConfiguration.config_value("keycode_verification")
#          @merchant_id ||= String.new
#          @keycode ||= String.new
#        end
#        request_url = @verification_url + '/Get_Transaction_Ref'
#        #requested_url = request_url + "?OrderID=" + payment.gateway_response[:order_id] + "&MerchantID=" + @merchant_id + "&KeyCode=" + @keycode  
#        
#        uri = URI(request_url)
#        http = Net::HTTP.new(uri.host, uri.port)
#        auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
#        auth_req.set_form_data({"OrderID" => o.to_s, "MerchantID" => @merchant_id, "KeyCode" => @keycode})
#        
#        http.use_ssl = true
#        auth_res = http.request(auth_req)
#        
#        xml_res = Nokogiri::XML(auth_res.body)
#        
#        status = ""
#        unless xml_res.xpath("/").empty?
#          status = xml_res.xpath("/").text
#        end
#        
#        result = Base64.decode64(status)
#        #s = Hash.from_xml(result).to_json
#        #@financefee = FinanceFee.find(83278)
#        #@student = Student.find(22845)
#      
#        #payment = Payment.new(:payee => @student,:payment => @financefee,:gateway_response => s, :validation_response => o.to_s)
#        #payment.save
#        
#        ref_id = ""
#          orderId = ""
#          name = ""
#          email = ""
#          amount = 0.00
#          service_charge = 0.00
#          total_amount = 0.00
#          status = 0
#          status_text = ""
#          used = ""
#          verified = 0
#          payment_type = ""
#          pan = ""
#          tbbmm_account = ""
#          merchant_id = ""
#          order_datetime = ""
#          trans_date = ""
#          emi_no = ""
#          interest_amount = ""
#          pay_with_charge = ""
#          card_response_code = ""
#          card_response_desc = ""
#          card_order_status = ""
#
#          xml_str = Nokogiri::XML(result)
#
#          xml_transaction_info = xml_str.xpath("//Response/TransactionInfo")
#          childs = xml_transaction_info[xml_transaction_info.length - 1].children
#          #abort(childs.inspect)
#          childs.each do |c|
#            if c.name == "RefID"
#              ref_id = c.text
#            elsif c.name == "OrderID"
#              orderId = c.text
#            elsif c.name == "Name"
#              name = c.text
#            elsif c.name == "Email"
#              email = c.text
#            elsif c.name == "Amount"
#              amount = c.text
#            elsif c.name == "ServiceCharge"
#              service_charge = c.text
#            elsif c.name == "TotalAmount"
#              total_amount = c.text
#            elsif c.name == "Status"
#              status = c.text
#            elsif c.name == "StatusText"
#              status_text = c.text
#            elsif c.name == "Used"
#              used = c.text
#            elsif c.name == "Verified"
#              verified = c.text
#            elsif c.name == "PaymentType"
#              payment_type = c.text
#            elsif c.name == "PAN"
#              pan = c.text
#            elsif c.name == "TBMM_Account"
#              tbbmm_account = c.text
#            elsif c.name == "MarchentID"
#              merchant_id = c.text
#            elsif c.name == "OrderDateTime"
#              order_datetime = c.text
#            elsif c.name == "PaymentDateTime"
#              trans_date = c.text
#            elsif c.name == "EMI_No"
#              emi_no = c.text
#            elsif c.name == "InterestAmount"
#              interest_amount = c.text
#            elsif c.name == "PayWithCharge"
#              pay_with_charge = c.text
#            elsif c.name == "CardResponseCode"
#              card_response_code = c.text
#            elsif c.name == "CardResponseDescription"
#              card_response_desc = c.text
#            elsif c.name == "CardOrderStatus"
#              card_order_status = c.text
#            end
#
#          end
#
#
#          gateway_response = {
#            :total_amount => total_amount,
#            :amount => amount,
#            :name => name,
#            :email => email,
#            :merchant_id => merchant_id,
#            :order_datetime => order_datetime,
#            :emi_no => emi_no,
#            :tbbmm_account => tbbmm_account,
#            :interest_amount => interest_amount,
#            :pay_with_charge => pay_with_charge,
#            :card_response_code => card_response_code,
#            :card_response_desc => card_response_desc,
#            :card_order_status => card_order_status,
#            :used => used,
#            :verified => verified,
#            :status_text => status_text,
#            :status => status,
#            :ref_id => ref_id,
#            :order_id=>orderId,
#            :tran_date=>trans_date,
#            :payment_type=>payment_type,
#            :service_charge=>service_charge,
#            :pan=>pan
#          }
#      
#          @student = Student.find_by_admission_no(name)
#          #create_at = Date.parse(trans_date)
#          #start_month = create_at.beginning_of_month
#          #end_month = create_at.end_of_month
#          
#          #fee_collection = FinanceFeeCollection.find(:all, :conditions => "due_date >= #{start_month.to_date} and end_date >= #{end_month.to_date}")
#          fees = FinanceFee.find(:first, :conditions => "student_id = #{@student.id}")
#          
#          unless fees.nil?
#            @financefee = FinanceFee.find(fees.id)
#            
#            request_url = @verification_url + '/Transaction_Verify_Details'
#            #requested_url = request_url + "?OrderID=" + payment.gateway_response[:order_id] + "&MerchantID=" + @merchant_id + "&KeyCode=" + @keycode  
#
#            uri = URI(request_url)
#            http = Net::HTTP.new(uri.host, uri.port)
#            auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
#            auth_req.set_form_data({"OrderID" => orderId, "MerchantID" => @merchant_id, "RefID" => ref_id})
#
#            http.use_ssl = true
#            auth_res = http.request(auth_req)
#
#            xml_res = Nokogiri::XML(auth_res.body)
#            status = ""
#            unless xml_res.xpath("/").empty?
#              status = xml_res.xpath("/").text
#            end
#
#            result = Base64.decode64(status)
#            #abort(result.inspect)
#            verification_ref_id = ""
#            verification_orderId = ""
#            verification_name = ""
#            verification_email = ""
#            verification_amount = 0.00
#            verification_service_charge = 0.00
#            verification_total_amount = 0.00
#            verification_status = 0
#            verification_status_text = ""
#            verification_used = ""
#            verification_verified = 0
#            verification_payment_type = ""
#            verification_pan = ""
#            verification_tbbmm_account = ""
#            verification_merchant_id = ""
#            verification_order_datetime = ""
#            verification_trans_date = ""
#            verification_emi_no = ""
#            verification_interest_amount = ""
#            verification_pay_with_charge = ""
#            verification_card_response_code = ""
#            verification_card_response_desc = ""
#            verification_card_order_status = ""
#
#            xml_str = Nokogiri::XML(result)
#
#            unless xml_str.xpath("//Response/RefID").empty?
#              verification_ref_id = xml_str.xpath("//Response/RefID").text
#            end
#            unless xml_str.xpath("//Response/OrderID").empty?
#              verification_orderId = xml_str.xpath("//Response/OrderID").text
#            end
#            unless xml_str.xpath("//Response/Name").empty?
#              verification_name = xml_str.xpath("//Response/Name").text
#            end
#            unless xml_str.xpath("//Response/Email").empty?
#              verification_email = xml_str.xpath("//Response/Email").text
#            end
#            unless xml_str.xpath("//Response/Amount").empty?
#              verification_amount = xml_str.xpath("//Response/Amount").text
#            end
#            unless xml_str.xpath("//Response/ServiceCharge").empty?
#              verification_service_charge = xml_str.xpath("//Response/ServiceCharge").text
#            end
#            unless xml_str.xpath("//Response/TotalAmount").empty?
#              verification_total_amount = xml_str.xpath("//Response/TotalAmount").text
#            end
#            unless xml_str.xpath("//Response/Status").empty?
#              verification_status = xml_str.xpath("//Response/Status").text
#            end
#            unless xml_str.xpath("//Response/StatusText").empty?
#              verification_status_text = xml_str.xpath("//Response/StatusText").text
#            end
#            unless xml_str.xpath("//Response/Used").empty?
#              verification_used = xml_str.xpath("//Response/Used").text
#            end
#            unless xml_str.xpath("//Response/Verified").empty?
#              verification_verified = xml_str.xpath("//Response/Verified").text
#            end
#            unless xml_str.xpath("//Response/PaymentType").empty?
#              verification_payment_type = xml_str.xpath("//Response/PaymentType").text
#            end
#            unless xml_str.xpath("//Response/PAN").empty?
#              verification_pan = xml_str.xpath("//Response/PAN").text
#            end
#            unless xml_str.xpath("//Response/TBMM_Account").empty?
#              verification_tbbmm_account = xml_str.xpath("//Response/TBMM_Account").text
#            end
#            unless xml_str.xpath("//Response/MarchentID").empty?
#              verification_merchant_id = xml_str.xpath("//Response/MarchentID").text
#            end
#            unless xml_str.xpath("//Response/OrderDateTime").empty?
#              verification_order_datetime = xml_str.xpath("//Response/OrderDateTime").text
#            end
#            unless xml_str.xpath("//Response/PaymentDateTime").empty?
#              verification_trans_date = xml_str.xpath("//Response/PaymentDateTime").text
#            end
#            unless xml_str.xpath("//Response/EMI_No").empty?
#              verification_emi_no = xml_str.xpath("//Response/EMI_No").text
#            end
#            unless xml_str.xpath("//Response/InterestAmount").empty?
#              verification_interest_amount = xml_str.xpath("//Response/InterestAmount").text
#            end
#            unless xml_str.xpath("//Response/PayWithCharge").empty?
#              verification_pay_with_charge = xml_str.xpath("//Response/PayWithCharge").text
#            end
#            unless xml_str.xpath("//Response/CardResponseCode").empty?
#              verification_card_response_code = xml_str.xpath("//Response/CardResponseCode").text
#            end
#            unless xml_str.xpath("//Response/CardResponseDescription").empty?
#              verification_card_response_desc = xml_str.xpath("//Response/CardResponseDescription").text
#            end
#            unless xml_str.xpath("//Response/CardOrderStatus").empty?
#              verification_card_order_status = xml_str.xpath("//Response/CardOrderStatus").text
#            end
#
#            validation_response = {
#              :total_amount => verification_total_amount,
#              :amount => verification_amount,
#              :name => verification_name,
#              :email => verification_email,
#              :merchant_id => verification_merchant_id,
#              :order_datetime => verification_order_datetime,
#              :emi_no => verification_emi_no,
#              :tbbmm_account => verification_tbbmm_account,
#              :interest_amount => verification_interest_amount,
#              :pay_with_charge => verification_pay_with_charge,
#              :card_response_code => verification_card_response_code,
#              :card_response_desc => verification_card_response_desc,
#              :card_order_status => verification_card_order_status,
#              :used => verification_used,
#              :verified => verification_verified,
#              :status_text => verification_status_text,
#              :status => verification_status,
#              :ref_id => verification_ref_id,
#              :order_id=>verification_orderId,
#              :tran_date=>verification_trans_date,
#              :payment_type=>verification_payment_type,
#              :service_charge=>verification_service_charge,
#              :pan=>verification_pan
#            }
#        
#            payment = Payment.find_by_payee_id_and_payment_id(@student.id,@financefee.id)
#            if payment.nil?
#              payment = Payment.new(:payee => @student,:payment => @financefee,:gateway_response => gateway_response, :validation_response => validation_response)
#              payment.save
#            end
#            
#            finance_fee_id = payment.payment_id
#            payee_id = payment.payee_id
#            fee = FinanceFee.find(:first, :conditions => "id = #{finance_fee_id} and student_id = #{payee_id}")
#            @student = Student.find(payee_id)
#            @batch = @student.batch
#            unless fee.nil?
#              unless fee.is_paid
#                fee_collection_id = fee.fee_collection_id
#                advance_fee_collection = false
#                @self_advance_fee = false
#                @fee_has_advance_particular = false
#
#                @date = @fee_collection = FinanceFeeCollection.find(fee_collection_id)
#                @student_has_due = false
#                @std_finance_fee_due = FinanceFee.find(:first,:conditions=>["finance_fee_collections.due_date < ? and finance_fees.is_paid = 0 and finance_fees.student_id = ?", @date.due_date,@student.id],:include=>"finance_fee_collection")
#                unless @std_finance_fee_due.blank?
#                  @student_has_due = true
#                end
#                @financefee = @student.finance_fee_by_date(@date)
#
#                if @financefee.has_advance_fee_id
#                  if @date.is_advance_fee_collection
#                    @self_advance_fee = true
#                    advance_fee_collection = true
#                  end
#                  @fee_has_advance_particular = true
#                  @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
#                  @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
#                end
#
#                @due_date = @fee_collection.due_date
#                @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
#
#                @paid_fees = @financefee.finance_transactions
#
#                if advance_fee_collection
#                  fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
#                  if fee_collection_advances_particular.include?(0)
#                    @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
#                  else
#                    @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
#                  end
#                else
#                  @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
#                end
#
#                if advance_fee_collection
#                  month = 1
#                  payable = 0
#                  @fee_collection_advances.each do |fee_collection_advance|
#                    @fee_particulars.each do |particular|
#                      if fee_collection_advance.particular_id == particular.finance_fee_particular_category_id
#                        payable += particular.amount * fee_collection_advance.no_of_month.to_i
#                      else
#                        payable += particular.amount
#                      end
#                    end
#                  end
#                  @total_payable=payable.to_f
#                else  
#                  @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
#                end
#
#                @total_discount = 0
#
#                #calculate_discount(@date, @financefee.batch, @student, @financefee.is_paid)
#                @adv_fee_discount = false
#                @actual_discount = 1
#
#                if advance_fee_collection
#                  calculate_discount(@date, @batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
#                else
#                  if @fee_has_advance_particular
#                    calculate_discount(@date, @batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
#                  else
#                    calculate_discount(@date, @batch, @student, false, nil, @fee_has_advance_particular)
#                  end
#                end
#
#                bal=(@total_payable-@total_discount).to_f
#
#                @fine_amount=0 if @financefee.is_paid
#
#                unless advance_fee_collection
#                  if @total_discount == 0
#                    @adv_fee_discount = true
#                    @actual_discount = 0
#                    calculate_discount(@date, @batch, @student, false, nil, @fee_has_advance_particular)
#                  end
#                end
#
#                total_fees = @financefee.balance.to_f+@fine_amount.to_f
#
#                if amount.to_f > 0
#                  if amount.to_f == Champs21Precision.set_and_modify_precision(total_fees).to_f
#                    transaction = FinanceTransaction.new
#                    transaction.title = "#{t('receipt_no')}. F#{@financefee.id}"
#                    transaction.category = FinanceTransactionCategory.find_by_name("Fee")
#                    transaction.payee = @student
#                    transaction.finance = @financefee
#                    transaction.amount = total_fees
#                    transaction.fine_included = (@fine.to_f + @fine_amount.to_f).zero? ? false : true
#                    transaction.fine_amount = @fine.to_f + @fine_amount.to_f
#                    transaction.transaction_date = Date.today
#                    transaction.payment_mode = "Online Payment"
#                    transaction.save
#                    if transaction.save
#                      total_fine_amount = 0
#                      unless (@fine.to_f + @fine_amount.to_f).zero?
#                        total_fine_amount = @fine.to_f + @fine_amount.to_f
#                      end
#                      is_paid =@financefee.balance==0 ? true : false
#                      @financefee.update_attributes( :is_paid=>is_paid)
#
#                      @paid_fees = @financefee.finance_transactions
#
#                      proccess_particulars_category = []
#                      loop_particular = 0
#                      @fee_particulars.each do |fp|
#                        particular_amount = fp.amount.to_f
#                        finance_transaction_particular = FinanceTransactionParticular.new
#                        finance_transaction_particular.finance_transaction_id = transaction.id
#                        finance_transaction_particular.particular_id = fp.id
#                        finance_transaction_particular.particular_type = 'Particular'
#                        finance_transaction_particular.transaction_type = 'Fee Collection'
#                        finance_transaction_particular.amount = particular_amount
#                        finance_transaction_particular.transaction_date = transaction.transaction_date
#                        finance_transaction_particular.save
#                      end
#
#                      unless @onetime_discounts.blank?
#                        @onetime_discounts.each do |od|
#                          discount_amount = @onetime_discounts_amount[od.id].to_f
#                          finance_transaction_particular = FinanceTransactionParticular.new
#                          finance_transaction_particular.finance_transaction_id = transaction.id
#                          finance_transaction_particular.particular_id = od.id
#                          finance_transaction_particular.particular_type = 'Adjustment'
#                          finance_transaction_particular.transaction_type = 'Discount'
#                          finance_transaction_particular.amount = discount_amount
#                          finance_transaction_particular.transaction_date = transaction.transaction_date
#                          finance_transaction_particular.save
#                        end
#                      end
#
#
#                      unless @discounts.blank?
#                        @discounts.each do |od|
#                          discount_amount = @discounts_amount[od.id]
#                          finance_transaction_particular = FinanceTransactionParticular.new
#                          finance_transaction_particular.finance_transaction_id = transaction.id
#                          finance_transaction_particular.particular_id = od.id
#                          finance_transaction_particular.particular_type = 'Adjustment'
#                          finance_transaction_particular.transaction_type = 'Discount'
#                          finance_transaction_particular.amount = discount_amount
#                          finance_transaction_particular.transaction_date = transaction.transaction_date
#                          finance_transaction_particular.save
#                        end
#                      end
#
#                      if transaction.vat_included?
#                        vat_amount = transaction.vat_amount
#                        finance_transaction_particular = FinanceTransactionParticular.new
#                        finance_transaction_particular.finance_transaction_id = transaction.id
#                        finance_transaction_particular.particular_id = 0
#                        finance_transaction_particular.particular_type = 'VAT'
#                        finance_transaction_particular.transaction_type = ''
#                        finance_transaction_particular.amount = vat_amount
#                        finance_transaction_particular.transaction_date = transaction.transaction_date
#                        finance_transaction_particular.save
#                      end
#
#                      if total_fine_amount
#                        fine_amount = total_fine_amount
#                        finance_transaction_particular = FinanceTransactionParticular.new
#                        finance_transaction_particular.finance_transaction_id = transaction.id
#                        finance_transaction_particular.particular_id = 0
#                        finance_transaction_particular.particular_type = 'Fine'
#                        finance_transaction_particular.transaction_type = ''
#                        finance_transaction_particular.amount = fine_amount
#                        finance_transaction_particular.transaction_date = transaction.transaction_date
#                        finance_transaction_particular.save
#                      end
#
#
#                      if @has_fine_discount
#                        @discounts_on_lates.each do |fd|
#                          discount_amount = @discounts_late_amount[od.id]
#                          discount_amount = params["fee_fine_discount_amount_" + fd.id.to_s].to_f
#                          finance_transaction_particular = FinanceTransactionParticular.new
#                          finance_transaction_particular.finance_transaction_id = transaction.id
#                          finance_transaction_particular.particular_id = fd.id
#                          finance_transaction_particular.particular_type = 'FineAdjustment'
#                          finance_transaction_particular.transaction_type = 'Discount'
#                          finance_transaction_particular.amount = discount_amount
#                          finance_transaction_particular.transaction_date = transaction.transaction_date
#                          finance_transaction_particular.save
#                        end
#                      end
#                    end
#                    payment.update_attributes(:finance_transaction_id => transaction.id)
#                    unless @financefee.transaction_id.nil?
#                      tid =   @financefee.transaction_id.to_s + ",#{transaction.id}"
#                    else
#                      tid=transaction.id
#                    end
#                    is_paid = @financefee.balance==0 ? true : false
#
#
#
#                    @financefee.update_attributes(:transaction_id=>tid, :is_paid=>is_paid)
#                    @paid_fees = FinanceTransaction.find(:all,:conditions=>"FIND_IN_SET(id,\"#{tid}\")")
#
#                  end
#                end
#              end
#            end
#            paymentnew = Payment.find(payment.id)
#            paymentnew.update_attributes(:gateway_response => gateway_response, :validation_response => validation_response)
#          end
#    end
    
    start_date = params[:start_date]
    start_date ||= Date.today
    end_date = params[:end_date]
    end_date ||= Date.today
    
    extra_query = ""
    unless params[:payment_status].nil? or params[:payment_status].empty? or params[:payment_status].blank?
      payment_status = 0
      if params[:payment_status].to_i == 1
        payment_status = params[:payment_status].to_i
      end
      extra_query += ' and gateway_response like \'%:status: "' + payment_status.to_s + '%\''
    end
    unless params[:order_id].nil? or params[:order_id].empty? or params[:order_id].blank?
      extra_query += ' and gateway_response like \'%:order_id: "' + params[:order_id].to_s + '%\''
    end
    unless params[:ref_no].nil? or params[:ref_no].empty? or params[:ref_no].blank?
      extra_query += ' and gateway_response like \'%:ref_id: ' + params[:ref_no].to_s + '%\''
    end
    unless params[:payment_type].nil? or params[:payment_type].empty? or params[:payment_type].blank?
      extra_query += ' and gateway_response like \'%:payment_type: ' + params[:payment_type].to_s + '%\''
    end
    #@online_payments = Payment.all.select{|p| p.created_at.to_date >= start_date.to_date and p.created_at.to_date <= end_date.to_date}.paginate(:page => params[:page],:per_page => 30)
    @online_payments = Payment.paginate(:conditions=>"CAST( DATE_ADD( created_at, INTERVAL 6 HOUR ) AS DATE ) >= '#{start_date.to_date}' and CAST( DATE_ADD( created_at, INTERVAL 6 HOUR ) AS DATE ) <= '#{end_date.to_date}' #{extra_query}",:page => params[:page],:per_page => 30, :order => "created_at DESC", :group => "id")
    ###.paginate()
    
    respond_to do |format|
      format.html #transctions.html.erb
    end
  end
  
  def settings
    @active_gateway = PaymentConfiguration.config_value("champs21_gateway")
    if @active_gateway == "Paypal"
      @gateway_fields = Champs21Pay::PAYPAL_CONFIG_KEYS
    elsif @active_gateway == "Authorize.net"
      @gateway_fields = Champs21Pay::AUTHORIZENET_CONFIG_KEYS
    elsif @active_gateway == "ssl.commerce"
      @gateway_fields = Champs21Pay::SSL_COMMERCE_CONFIG_KEYS
    elsif @active_gateway == "trustbank"
      @gateway_fields = Champs21Pay::TRUST_BANK_CONFIG_KEYS
    end
    @enabled_fees = PaymentConfiguration.find_by_config_key("enabled_fees").try(:config_value)  
    @enabled_fees ||= Array.new

    if request.post?
      payment_settings = params[:payment_settings]
      configuration = PaymentConfiguration.find_or_initialize_by_config_key('is_test_sslcommerz')
      configuration.update_attributes(:config_value => 0)
      configuration = PaymentConfiguration.find_or_initialize_by_config_key('is_test_testtrustbank')
      configuration.update_attributes(:config_value => 0)
      payment_settings.each_pair do |key,value|
        if key == 'is_test_sslcommerz' && value == 'on'
          value = 1
        elsif key == 'is_test_sslcommerz' && value != 'on'
          value = 0
        end
        if key == 'is_test_testtrustbank' && value == 'on'
          value = 1
        elsif key == 'is_test_testtrustbank' && value != 'on'
          value = 0
        end
        configuration = PaymentConfiguration.find_or_initialize_by_config_key(key)
        if configuration.update_attributes(:config_value => value)
          flash[:notice] = "Payment setting has been saved successfully."
        else
          flash[:notice] = "#{configuration.errors.full_messages.join("\n")}"
        end
      end
      unless payment_settings.keys.include? "enabled_fees"
        configuration = PaymentConfiguration.find_or_initialize_by_config_key("enabled_fees")
        configuration.update_attributes(:config_value => Array.new)
      end
      redirect_to settings_online_payments_path
    end
  end

  def show_gateway_fields
    @active_gateway = params[:gateway]
    if @active_gateway == "Paypal"
      @gateway_fields = Champs21Pay::PAYPAL_CONFIG_KEYS
    elsif @active_gateway == "Authorize.net"
      @gateway_fields = Champs21Pay::AUTHORIZENET_CONFIG_KEYS
    elsif @active_gateway == "ssl.commerce"
      @gateway_fields = Champs21Pay::SSL_COMMERCE_CONFIG_KEYS
    elsif @active_gateway == "trustbank"
      @gateway_fields = Champs21Pay::TRUST_BANK_CONFIG_KEYS  
    end
    

    render :update do |page|
      if @gateway_fields.present?
        page.replace_html 'gateway_fields',:partial => "gateway_fields"
      else
        page.replace_html 'gateway_fields',:text => ""
      end
    end
  end

  def return_to_champs21_pages
    @active_gateway = PaymentConfiguration.config_value("champs21_gateway")
    if @active_gateway == "Paypal"
      return_url = OnlinePayment.return_url + {:tx => "#{params[:tx]}",:st => "#{params[:st]}",:amt => "#{params[:amt]}"}.to_param
    else
      return_url = URI.parse(OnlinePayment.return_url)
    end
    redirect_to return_url
    OnlinePayment.return_url = nil
  end

  private
  
  def send_sms(multi_message, recipients)
    @recipients = recipients.map{|r| r.gsub(' ','')}
    @multi_message = multi_message
    @config = SmsSetting.get_sms_config
    unless @config.blank?
      @sendername = @config['sms_settings']['sendername']
      @sms_url = @config['sms_settings']['host_url']
      @username = @config['sms_settings']['username']
      @password = @config['sms_settings']['password']
      @success_code = @config['sms_settings']['success_code']
      @username_mapping = @config['parameter_mappings']['username']
      @username_mapping ||= 'username'
      @password_mapping = @config['parameter_mappings']['password']
      @password_mapping ||= 'password'
      @phone_mapping = @config['parameter_mappings']['phone']
      @phone_mapping ||= 'phone'
      @sender_mapping = @config['parameter_mappings']['sendername']
      @sender_mapping ||= 'sendername'
      @message_mapping = @config['parameter_mappings']['message']
      @message_mapping ||= 'message'
      unless @config['additional_parameters'].blank?
        @additional_param = ""
        @config['additional_parameters'].split(',').each do |param|
          @additional_param += "&#{param}"
        end
      end
    end

    if @config.present?
      @sms_hash = {"user"=>@username,"pass"=>@password,"sid" =>@sendername}

      i = 0
      @i_sms_loop = 0
      @recipients.each do |recipient|
       message = @multi_message[i]
       message_escape = CGI::escape message
       if @i_sms_loop == 3
         message_log = SmsMessage.new(:body=> message_escape)
         message_log.save
         message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>"Successfull")
         @sms_hash["sms[#{@i_sms_loop}][0]"] = recipient
         @sms_hash["sms[#{@i_sms_loop}][1]"] = message
         @sms_hash["sms[#{@i_sms_loop}][2]"] = @i_sms_loop

         api_uri = URI.parse(@sms_url)
         http = Net::HTTP.new(api_uri.host, api_uri.port)
         request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
         request.set_form_data(@sms_hash)

         http.request(request)

         sms_count = Configuration.find_by_config_key("TotalSmsCount")
         new_count = sms_count.config_value.to_i + 4
         sms_count.update_attributes(:config_value=>new_count)

         @sms_hash = {"user"=>@username,"pass"=>@password,"sid" =>@sendername}

         @i_sms_loop = 0
       elsif recipient.equal? @recipients.last
         message_log = SmsMessage.new(:body=> message_escape)
         message_log.save
         message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>"Successfull")
         @sms_hash["sms[#{@i_sms_loop}][0]"] = recipient
         @sms_hash["sms[#{@i_sms_loop}][1]"] = message
         @sms_hash["sms[#{@i_sms_loop}][2]"] = @i_sms_loop

         api_uri = URI.parse(@sms_url)
         http = Net::HTTP.new(api_uri.host, api_uri.port)
         request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
         request.set_form_data(@sms_hash)
         http.request(request)

         sms_count = Configuration.find_by_config_key("TotalSmsCount")
         new_count = sms_count.config_value.to_i + 1+@i_sms_loop
         sms_count.update_attributes(:config_value=>new_count)
       else
         @sms_hash["sms[#{@i_sms_loop}][0]"] = recipient
         @sms_hash["sms[#{@i_sms_loop}][1]"] = message
         @sms_hash["sms[#{@i_sms_loop}][2]"] = @i_sms_loop
         message_log = SmsMessage.new(:body=> message_escape)
         message_log.save
         message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>"Successfull")
         @i_sms_loop = @i_sms_loop+1
       end   

       i += 1
      end
    end
  end

  def calculate_discount(date,batch,student,is_advance_fee_collection,advance_fee,fee_has_advance_particular)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id(student.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []
    advance_fee_particular = []
    unless advance_fee.nil? or advance_fee.empty? or advance_fee.blank?
      advance_fee_particular = advance_fee.map(&:particular_id)
    end
    if MultiSchool.current_school.id == 312
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee_particular.include?(0))
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)) }
        @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        @discounts_amount = []
          @discounts.each do |d|
            @discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount = @total_discount + @discounts_amount[d.id]
        end
      else
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)) }
        @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        @discounts_amount = []
          @discounts.each do |d|
            @discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount = @total_discount + @discounts_amount[d.id]
        end
      end
    else
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee_particular.include?(0))
        deduct_fee = 0
        if fee_has_advance_particular and !advance_fee_particular.include?(0)
          unless advance_fee_particular.blank?
            particular_id = advance_fee_particular.join(",")
            fee_particulars_deduct = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{particular_id})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            deduct_fee = fee_particulars_deduct.map{|l| l.amount}.sum.to_f
          end
        end
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @onetime_discounts.length > 0
          one_time_total_amount_discount= true
          @onetime_discounts_amount = []
          @onetime_discounts.each do |d|
            @onetime_discounts_amount[d.id] = (@total_payable - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount = @total_discount + @onetime_discounts_amount[d.id]
          end
        else
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          @onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if @onetime_discounts.length > 0
            one_time_discount = true
            @onetime_discounts_amount = []
            i = 0
            @onetime_discounts.each do |d|   
              onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @onetime_discounts_amount[d.id] = discount_amt
                @total_discount = @total_discount + discount_amt
                i = i + 1
              end
            end
          end
        end

        unless one_time_total_amount_discount
          if onetime_discount_particulars_id.empty?
            onetime_discount_particulars_id[0] = 0
          end
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @discounts_amount = []
            @discounts.each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @discounts_amount[d.id] = discount_amt
                @total_discount = @total_discount + discount_amt
              end
            end
          else  
            unless one_time_discount
              deduct_fee = 0
              if fee_has_advance_particular and !advance_fee_particular.include?(0)
                unless advance_fee_particular.blank?
                  particular_id = advance_fee_particular.join(",")
                  fee_particulars_deduct = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{particular_id})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                  deduct_fee = fee_particulars_deduct.map{|l| l.amount}.sum.to_f
                end
              end
              @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              @discounts_amount = []
              @discounts.each do |d|
                @discounts_amount[d.id] = (@total_payable - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
                @total_discount = @total_discount + @discounts_amount[d.id]
              end
            end
          end
        end
      else
        one_time_total_amount_discount= false
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        
        if @onetime_discounts.length > 0
          one_time_total_amount_discount= true
          @onetime_discounts_amount = []
          @onetime_discounts.each do |d|
            @onetime_discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount = @total_discount + @onetime_discounts_amount[d.id]
          end
        end

        unless one_time_total_amount_discount
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @discounts_amount = []
            @discounts.each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                month = 1
                unless advance_fee.nil?
                  advance_fee.each do |fee|
                    if fee.particular_id == fee_particulars_single.finance_fee_particular_category_id
                        month = fee.no_of_month.to_i
                    end
                  end
                end
                payable_ampt = (fee_particulars_single.map{|l| l.amount}.sum.to_f * month.to_i).to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @discounts_amount[d.id] = discount_amt
                @total_discount = @total_discount + discount_amt
              end
            end
          else  
            unless one_time_discount
              deduct_fee = 0
              @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              @discounts_amount = []
              @discounts.each do |d|
                @discounts_amount[d.id] = (@total_payable - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
                @total_discount = @total_discount + @discounts_amount[d.id]
              end
            end  
          end
        end
      end
    end
  end
  
  def calculate_discount_index_all(date,batch,student,ind,is_advance_fee_collection,advance_fee,fee_has_advance_particular)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id(student.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []
    
    if MultiSchool.current_school.id == 312
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee.particular_id.to_i == 0)
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if @all_onetime_discounts[ind].length > 0
          @all_onetime_discounts_amount[ind] = []
            @all_onetime_discounts[ind].each do |d|
              @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
              @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
          end
        end
      else
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if @all_onetime_discounts[ind].length > 0
          @all_onetime_discounts_amount[ind] = []
            @all_onetime_discounts[ind].each do |d|
              @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
              @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
          end
        end
      end
    else
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee.particular_id.to_i == 0)
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @all_onetime_discounts[ind].length > 0
          one_time_total_amount_discount= true
          @all_onetime_discounts_amount[ind] = []
          @all_onetime_discounts[ind].each do |d|
            @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
            @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
          end
        else
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if @all_onetime_discounts[ind].length > 0
            one_time_discount = true
            @all_onetime_discounts_amount[ind] = []
            i = 0
            @all_onetime_discounts[ind].each do |d|   
              onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @all_onetime_discounts_amount[ind][d.id] = discount_amt
                @all_total_discount[ind] = @all_total_discount[ind] + discount_amt
                i = i + 1
              end
            end
          end
        end

        unless one_time_total_amount_discount
          if onetime_discount_particulars_id.empty?
            onetime_discount_particulars_id[0] = 0
          end
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)}
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @all_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @all_discounts_amount[ind] = []
            @all_discounts[ind].each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @all_discounts_amount[ind][d.id] = discount_amt
                @all_total_discount[ind] = @all_total_discount[ind] + discount_amt
              end
            end
          else  
            unless one_time_discount
              @all_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              @all_discounts_amount[ind] = []
              @all_discounts[ind].each do |d|
                @all_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
                @all_total_discount[ind] = @all_total_discount[ind] + @all_discounts_amount[ind][d.id]
              end
            end
          end
        end
      else
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee.discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @all_onetime_discounts[ind].length > 0
          one_time_total_amount_discount= true
          @all_onetime_discounts_amount[ind] = []
          @all_onetime_discounts[ind].each do |d|
            @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
            @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
          end
        end

        unless one_time_total_amount_discount
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)}
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @all_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @all_discounts_amount[ind] = []
            @all_discounts[ind].each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                payable_ampt = (fee_particulars_single.map{|l| l.amount}.sum.to_f * advance_fee.no_of_month.to_i).to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @all_discounts_amount[ind][d.id] = discount_amt
                @all_total_discount[ind] = @all_total_discount[ind] + discount_amt
              end
            end
          end
        end
      end
    end
  end
  
  def calculate_discount_index(date,batch,student,ind,is_advance_fee_collection,advance_fee,fee_has_advance_particular)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id(student.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []
    
    if MultiSchool.current_school.id == 312
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee.particular_id.to_i == 0)
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if @onetime_discounts[ind].length > 0
          @onetime_discounts_amount[ind] = []
            @onetime_discounts[ind].each do |d|
              @onetime_discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
              @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
          end
        end
      else
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if @onetime_discounts[ind].length > 0
          @onetime_discounts_amount[ind] = []
            @onetime_discounts[ind].each do |d|
              @onetime_discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
              @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
          end
        end
      end
    else
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee.particular_id.to_i == 0)
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @onetime_discounts[ind].length > 0
          one_time_total_amount_discount= true
          @onetime_discounts_amount[ind] = []
          @onetime_discounts[ind].each do |d|
            @onetime_discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
          end
        else
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
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
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
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
              @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              @discounts_amount[ind] = []
              @discounts[ind].each do |d|
                @discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
                @total_discount[ind] = @total_discount[ind] + @discounts_amount[ind][d.id]
              end
            end
          end
        end
      else
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @onetime_discounts[ind].length > 0
          one_time_total_amount_discount= true
          @onetime_discounts_amount[ind] = []
          @onetime_discounts[ind].each do |d|
            @onetime_discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
          end
        end

        unless one_time_total_amount_discount
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @discounts_amount[ind] = []
            @discounts[ind].each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              payable_ampt = (fee_particulars_single.map{|l| l.amount}.sum.to_f * advance_fee.no_of_month.to_i).to_f
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              @discounts_amount[ind][d.id] = discount_amt
              @total_discount[ind] = @total_discount[ind] + discount_amt
            end
          end
        end
      end
    end
  end
  
  def calculate_extra_fine(date,batch,student,fine_rule)
    if MultiSchool.current_school.id == 340
      #GET THE NEXT ALL months 
      extra_fine = 0
      s_date = date.start_date.to_date.beginning_of_month
      e_date = date.start_date.to_date.end_of_month
      other_months = FinanceFeeCollection.find(:all, :conditions => ["start_date NOT BETWEEN ? AND ? and is_deleted=#{false}", s_date, e_date], :order => "due_date asc")
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
      s_date = date.start_date.to_date.beginning_of_month
      e_date = date.start_date.to_date.end_of_month
      other_months = FinanceFeeCollection.find(:all, :conditions => ["start_date NOT BETWEEN ? AND ? and is_deleted=#{false}", s_date, e_date], :order => "due_date asc")
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
      s_date = date.start_date.to_date.beginning_of_month
      e_date = date.start_date.to_date.end_of_month
      other_months = FinanceFeeCollection.find(:all, :conditions => ["start_date NOT BETWEEN ? AND ? and is_deleted=#{false}", s_date, e_date], :order => "due_date asc")
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
