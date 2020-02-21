module OnlinePayment
  class << self; attr_accessor_with_default :return_url,String.new; end
  module StudentPay
    def self.included(base)
      base.alias_method_chain :fee_details,:gateway
    end

    def fee_details_with_gateway
      require 'net/http'
      require 'soap/wsdlDriver'
      require 'uri'
      require "yaml"
      require 'nokogiri'
      fee_requests = ""
      msg = ""
      orderId = ""
      ref_id = ""
      merchant_id = ""
      now = I18n.l(Time.now, :format=>'%Y-%m-%d %H:%M:%S')
      transaction_datetime = now
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
            testssl = false
            if PaymentConfiguration.config_value('is_test_sslcommerz').to_i == 1
              if File.exists?("#{Rails.root}/vendor/plugins/champs21_pay/config/payment_config.yml")
                payment_configs = YAML.load_file(File.join(Rails.root,"vendor/plugins/champs21_pay/config/","payment_config.yml"))
                unless payment_configs.nil? or payment_configs.empty? or payment_configs.blank?
                  testssl = payment_configs["testsslcommer"]
                end
              end
            end
            if testssl
              store_info = payment_configs["store_info_" + MultiSchool.current_school.id.to_s]
              @store_id = store_info["store_id"]
              @store_password = store_info["store_password"]
              @store_password ||= String.new
            else
              @store_id = PaymentConfiguration.config_value("store_id")
              @store_id ||= String.new
              @store_password = PaymentConfiguration.config_value("store_password")
              @store_password ||= String.new
            end
          elsif @active_gateway == "trustbank"
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
              @merchant_id ||= String.new
            else  
              @merchant_id = PaymentConfiguration.config_value("merchant_id")
              @merchant_id ||= String.new
            end
          elsif @active_gateway.nil?
            fee_details_without_gateway and return
          end
          
          current_school_name = Configuration.find_by_config_key('InstitutionName').try(:config_value)
          
          #@fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
          #@total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
          
          if request.post? and params[:order_id].present?
            unless params[:multiple].nil?
              if params[:multiple].to_s == "true"
                order_id = params[:order_id]
                fees = params[:fees].split(",")
                fees.each do |fee|
                  f = fee.to_i
                  @finance_order = FinanceOrder.find_by_order_id_and_finance_fee_id(params[:order_id], f)
                  @finance_order.update_attributes(:request_params => params)
                end
                
                @student = Student.find(params[:id])
                @total_amount = params[:amount_to_pay]
                
                unless params[:mobile_view].blank?
                  render 'gateway_payments/paypal/mobile_fee_details_execute',:layout => false
                else
                  render 'gateway_payments/paypal/fee_details_execute',:layout => false
                end
              else
                @finance_order = FinanceOrder.find_by_order_id(params[:order_id])
                @finance_order.update_attributes(:request_params => params)

                @student = Student.find(params[:id])
                @total_amount = params[:amount_to_pay]

                unless params[:mobile_view].blank?
                  render 'gateway_payments/paypal/mobile_fee_details_execute',:layout => false
                else
                  render 'gateway_payments/paypal/fee_details_execute',:layout => false
                end
              end
            else
              @finance_order = FinanceOrder.find_by_order_id(params[:order_id])
              @finance_order.update_attributes(:request_params => params)

              @student = Student.find(params[:id])
              @total_amount = params[:amount_to_pay]

              unless params[:mobile_view].blank?
                render 'gateway_payments/paypal/mobile_fee_details_execute',:layout => false
              else
                render 'gateway_payments/paypal/fee_details_execute',:layout => false
              end
            end
          else
            if params[:create_transaction].present? and @active_gateway == "trustbank"
              result = Base64.decode64(params[:CheckoutXmlMsg])
              #result = '<Response date="2016-06-20 10:14:53.213">  <RefID>133783A000129D</RefID>  <OrderID>O100010</OrderID>  <Name> Customer1</Name>  <Email> mr.customer@gmail.com </Email>  <Amount>2090.00</Amount>  <ServiceCharge>0.00</ServiceCharge>  <Status>1</Status>  <StatusText>PAID</StatusText>  <Used>0</Used>  <Verified>0</Verified>  <PaymentType>ITCL</PaymentType>  <PAN>712300XXXX1277</PAN>  <TBMM_Account></TBMM_Account>  <MarchentID>SAGC</MarchentID>  <OrderDateTime>2016-06-20 10:14:24.700</OrderDateTime>  <PaymentDateTime>2016-06-20 10:21:34.303</PaymentDateTime>  <EMI_No>0</EMI_No>  <InterestAmount>0.00</InterestAmount>  <PayWithCharge>1</PayWithCharge>  <CardResponseCode>00</CardResponseCode>  <CardResponseDescription>APPROVED</CardResponseDescription>  <CardOrderStatus>APPROVED</CardOrderStatus> </Response> '
              xml_res = Nokogiri::XML(result)
              status_post = 0
              status_text_post = ""
              amount_post = 0.00
              service_charge_post = 0.00
              trans_date = 0.00
              payment_type = ""
              pan = ""
              ref_id = ""
              used = ""
              verified = ""
              name = ""
              email = ""
              tbbmm_account = ""
              merchant_id = ""
              order_datetime = ""
              emi_no = ""
              interest_amount = ""
              pay_with_charge = ""
              card_response_code = ""
              card_response_desc = ""
              card_order_status = ""
              unless xml_res.xpath("//Response/Name").empty?
                name = xml_res.xpath("//Response/Name").text
              end
              unless xml_res.xpath("//Response/Email").empty?
                email = xml_res.xpath("//Response/Email").text
              end
              unless xml_res.xpath("//Response/MarchentID").empty?
                merchant_id = xml_res.xpath("//Response/MarchentID").text
              end
              unless xml_res.xpath("//Response/OrderDateTime").empty?
                order_datetime = xml_res.xpath("//Response/OrderDateTime").text
              end
              unless xml_res.xpath("//Response/EMI_No").empty?
                emi_no = xml_res.xpath("//Response/EMI_No").text
              end
              unless xml_res.xpath("//Response/TBMM_Account").empty?
                tbbmm_account = xml_res.xpath("//Response/TBMM_Account").text
              end
              unless xml_res.xpath("//Response/InterestAmount").empty?
                interest_amount = xml_res.xpath("//Response/InterestAmount").text
              end
              unless xml_res.xpath("//Response/PayWithCharge").empty?
                pay_with_charge = xml_res.xpath("//Response/PayWithCharge").text
              end
              unless xml_res.xpath("//Response/CardResponseCode").empty?
                card_response_code = xml_res.xpath("//Response/CardResponseCode").text
              end
              unless xml_res.xpath("//Response/CardResponseDescription").empty?
                card_response_desc = xml_res.xpath("//Response/CardResponseDescription").text
              end
              unless xml_res.xpath("//Response/CardOrderStatus").empty?
                card_order_status = xml_res.xpath("//Response/CardOrderStatus").text
              end
              unless xml_res.xpath("//Response/Status").empty?
                status_post = xml_res.xpath("//Response/Status").text
              end
              unless xml_res.xpath("//Response/StatusText").empty?
                status_text_post = xml_res.xpath("//Response/StatusText").text
              end
              unless xml_res.xpath("//Response/Used").empty?
                used = xml_res.xpath("//Response/Used").text
              end
              unless xml_res.xpath("//Response/Verified").empty?
                verified = xml_res.xpath("//Response/Verified").text
              end
              unless xml_res.xpath("//Response/Amount").empty?
                amount_post = xml_res.xpath("//Response/Amount").text
              end
              unless xml_res.xpath("//Response/ServiceCharge").empty?
                service_charge_post = xml_res.xpath("//Response/ServiceCharge").text
              end
              unless xml_res.xpath("//Response/OrderID").empty?
                orderId = xml_res.xpath("//Response/OrderID").text
              end
              unless xml_res.xpath("//Response/RefID").empty?
                ref_id = xml_res.xpath("//Response/RefID").text
              end
              unless xml_res.xpath("//Response/PaymentDateTime").empty?
                trans_date = xml_res.xpath("//Response/PaymentDateTime").text
              end
              unless xml_res.xpath("//Response/PaymentType").empty?
                payment_type = xml_res.xpath("//Response/PaymentType").text
              end
              unless xml_res.xpath("//Response/PAN").empty?
                pan = xml_res.xpath("//Response/PAN").text
              end

              gateway_response = {
                :amount => amount_post,
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
                :status_text => status_text_post,
                :status => status_post,
                :ref_id => ref_id,
                :order_id=>orderId,
                :tran_date=>trans_date,
                :payment_type=>payment_type,
                :service_charge=>service_charge_post,
                :pan=>pan
              }
              return_order_id = orderId.strip

              @finance_order = FinanceOrder.find_by_order_id(orderId.strip)
              #abort(@finance_order.inspect)
              request_params = @finance_order.request_params
              
              multiple_param = request_params[:multiple]
              unless multiple_param.nil?
                if multiple_param.to_s == "true"
                  @collection_fees = request_params[:fees]
                  fees = request_params[:fees].split(",")
                  fee_requests = fees
                  #abort('here1')
                  if params[:id].to_i != 25403
                    arrange_multiple_pay(params[:id], fees, params[:submission_date])
                  end
                else  
                  fee_requests = params[:id2]
                  if params[:id].to_i != 25403
                    arrange_pay(params[:id], params[:id2], params[:submission_date])
                  end
                end
              else
                fee_requests = params[:id2]
                if params[:id].to_i != 25403
                  arrange_pay(params[:id], params[:id2], params[:submission_date])
                end
              end
            else
              multiple_param = params[:multiple]
              unless multiple_param.nil?
                #abort('here-1')
                if multiple_param.to_s == "true"
                  @collection_fees = params[:fees]
                  fees = params[:fees].split(",")
                  fee_requests = fees
                  if params[:id].to_i != 25403
                    arrange_multiple_pay(params[:id], fees, params[:submission_date])
                  end
                else  
                  fee_requests = params[:id2]
                  if params[:id].to_i != 25403
                    arrange_pay(params[:id], params[:id2], params[:submission_date])
                  end
                end
              else
                fee_requests = params[:id2]
                if params[:id].to_i != 25403
                  arrange_pay(params[:id], params[:id2], params[:submission_date])
                end
              end
            end
            
            validate_payment_types(params)
            unless multiple_param.nil?
              if multiple_param.to_s == "true"
                collection_fees = fee_requests
                arrange_multiple_pay(params[:id], fees, params[:submission_date])
              else  
                arrange_pay(params[:id], fee_requests, params[:submission_date])
              end
            end
            
#            
            
            #@fine_amount=0 if (@student.finance_fee_by_date @date).is_paid
            unless params[:mobile_view].blank?
              unless multiple_param.nil?
                if multiple_param.to_s == "true"
                  render 'gateway_payments/paypal/fee_details_multiple_mobile',:layout => false
                else  
                  render 'gateway_payments/paypal/mobile_fee_details',:layout => false
                end
              else
                render 'gateway_payments/paypal/mobile_fee_details',:layout => false
              end
            else
              unless multiple_param.nil?
                if multiple_param.to_s == "true"
                  render 'gateway_payments/paypal/fee_details_multiple'
                else  
                  render 'gateway_payments/paypal/fee_details'
                end
              else
                render 'gateway_payments/paypal/fee_details'
              end
            end
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
    
  end

  #URI.parse("http://192.168.1.30:3000/student/fee_details/#{params[:id]}/#{params[:id2]}?create_transaction=1")

  module StudentPayReceipt

    def self.included(base)
      #base.alias_method_chain :student_fee_receipt_pdf,:gateway
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

          calculate_discount(@date, @financefee.batch, @student, @financefee.is_paid)

          bal=(@total_payable-@total_discount).to_f
          days=(Date.today-@date.due_date.to_date).to_i
          auto_fine=@date.fine

          @has_fine_discount = false
          if days > 0 and auto_fine
            @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
            @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
            calculate_extra_fine(@date, @financefee.batch, @student, @fine_rule)
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
