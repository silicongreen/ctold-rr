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
            if params[:create_transaction].present?
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
                  arrange_multiple_pay(params[:id], fees, params[:submission_date])
                else  
                  arrange_pay(params[:id], params[:id2], params[:submission_date])
                end
              else
                arrange_pay(params[:id], params[:id2], params[:submission_date])
              end
            else
              multiple_param = params[:multiple]
              unless multiple_param.nil?
                #abort('here-1')
                if multiple_param.to_s == "true"
                  @collection_fees = params[:fees]
                  fees = params[:fees].split(",")
                  arrange_multiple_pay(params[:id], fees, params[:submission_date])
                else  
                  arrange_pay(params[:id], params[:id2], params[:submission_date])
                end
              else
                #abort('here')
                arrange_pay(params[:id], params[:id2], params[:submission_date])
              end
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
              elsif @active_gateway == "trustbank"
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
                
                dt = trans_date.split(".")
                transaction_datetime = dt[0]

                if verified.to_i == 0
                  if transaction_datetime.nil?
                    dt = order_datetime.split(".")
                    transaction_datetime = dt[0]
                  end
                end

              end
              payment_saved = false
              unless request_params.nil?
                multiple = request_params[:multiple]
                unless multiple.nil?
                  if multiple.to_s == "true"
                    fees = request_params[:fees].split(",")
                    fees.each do |fee|
                      f = fee.to_i
                      feenew = FinanceFee.find(f)
                      payment = Payment.find(:first, :conditions => "order_id = '#{orderId}' and payee_id = #{@student.id} and payment_id = #{feenew.id}")
                      unless payment.nil?
                        payment_saved = true
                      else  
                        payment = Payment.new(:order_id => orderId, :payee => @student,:payment => feenew,:gateway_response => gateway_response, :transaction_datetime => transaction_datetime)
                        if payment.save
                          payment_saved = true
                        end 
                      end
                    end
                  else
                    payment = Payment.find(:first, :conditions => "order_id = '#{orderId}' and payee_id = #{@student.id} and payment_id = #{@financefee.id}")
                    unless payment.nil?
                      payment_saved = true
                    else  
                      payment = Payment.new(:order_id => orderId, :payee => @student,:payment => @financefee,:gateway_response => gateway_response, :transaction_datetime => transaction_datetime)
                      if payment.save
                        payment_saved = true
                      end 
                    end
                  end
                else
                  payment = Payment.find(:first, :conditions => "order_id = '#{orderId}' and payee_id = #{@student.id} and payment_id = #{@financefee.id}")
                  unless payment.nil?
                    payment_saved = true
                  else  
                    payment = Payment.new(:order_id => orderId, :payee => @student,:payment => @financefee,:gateway_response => gateway_response, :transaction_datetime => transaction_datetime)
                    if payment.save
                      payment_saved = true
                    end 
                  end
                end
              end
              
              if payment_saved
                gateway_status = false
                if @active_gateway == "Paypal"
                  gateway_status = true if params[:st] == "Completed"
                elsif @active_gateway == "Authorize.net"
                  gateway_status = true if params[:x_response_reason_code] == "1"
                elsif @active_gateway == "ssl.commerce"
                  gateway_status = true if params[:status] == "VALID"

                  if gateway_status == true
                    testssl = false
                    if PaymentConfiguration.config_value('is_test_sslcommerz').to_i == 1
                      if File.exists?("#{Rails.root}/vendor/plugins/champs21_pay/config/payment_config.yml")
                        payment_configs = YAML.load_file(File.join(Rails.root,"vendor/plugins/champs21_pay/config/","payment_config.yml"))
                        unless payment_configs.nil? or payment_configs.empty? or payment_configs.blank?
                          testssl = payment_configs["testsslcommer"]
                        end
                      end
                      if testssl 
                        validation_url = payment_configs["validation_api"]
                      else
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
                      end
                    else
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
                elsif @active_gateway == "trustbank"
                  gateway_status = true if status_post.to_i == 1

                  if gateway_status == true
                    testtrustbank = false
                    validation_url_found = false
                    if PaymentConfiguration.config_value('is_test_testtrustbank').to_i == 1
                      if File.exists?("#{Rails.root}/vendor/plugins/champs21_pay/config/payment_config_tcash.yml")
                        payment_configs = YAML.load_file(File.join(Rails.root,"vendor/plugins/champs21_pay/config/","payment_config_tcash.yml"))
                        unless payment_configs.nil? or payment_configs.empty? or payment_configs.blank?
                          testtrustbank = payment_configs["testtrustbank"]
                        end
                      end
                    end
                    if testtrustbank
                      validation_url = payment_configs["validation_api"]
                      validation_url ||= "https://ibanking.tblbd.com/TestCheckout/Checkout_Payment_Verify.asmx?WSDL"

                      merchant_info = payment_configs["merchant_info_" + MultiSchool.current_school.id.to_s]
                      merchant_id = merchant_info["merchant_id"]
                      merchant_id ||= String.new
                    else  
                      payment_urls = Hash.new
                      if File.exists?("#{Rails.root}/vendor/plugins/champs21_pay/config/online_payment_url.yml")
                        payment_urls = YAML.load_file(File.join(Rails.root,"vendor/plugins/champs21_pay/config/","online_payment_url.yml"))
                        validation_url = payment_urls["trustbank_requested_url"]
                        validation_url ||= "https://ibanking.tblbd.com/Checkout/Checkout_Payment_Verify.asmx?WSDL"

                        merchant_id = PaymentConfiguration.config_value("merchant_id")
                        merchant_id ||= String.new
                      else
                        validation_url ||= "https://ibanking.tblbd.com/Checkout/Checkout_Payment_Verify.asmx?WSDL"
                        merchant_id ||= String.new
                      end
                    end

                    wsdl_url = validation_url
                    soapDriver = SOAP::WSDLDriverFactory.new(wsdl_url).create_rpc_driver()
                    detail_result = soapDriver.Transaction_Verify_Details({:OrderID => orderId, :RefID => ref_id, :MerchantID => merchant_id});
                    result = Base64.decode64(detail_result["Transaction_Verify_DetailsResult"])
                    #result = '<Response date="2016-06-20 10:14:53.213">  <RefID>133783A000129D</RefID>  <OrderID>O100010</OrderID>  <Name> Customer1</Name>  <Email> mr.customer@gmail.com </Email>  <Amount>2090.00</Amount>  <ServiceCharge>0.00</ServiceCharge>  <Status>1</Status>  <StatusText>PAID</StatusText>  <Used>0</Used>  <Verified>0</Verified>  <PaymentType>ITCL</PaymentType>  <PAN>712300XXXX1277</PAN>  <TBMM_Account></TBMM_Account>  <MarchentID>SAGC</MarchentID>  <OrderDateTime>2016-06-20 10:14:24.700</OrderDateTime>  <PaymentDateTime>2016-06-20 10:21:34.303</PaymentDateTime>  <EMI_No>0</EMI_No>  <InterestAmount>0.00</InterestAmount>  <PayWithCharge>1</PayWithCharge>  <CardResponseCode>00</CardResponseCode>  <CardResponseDescription>APPROVED</CardResponseDescription>  <CardOrderStatus>APPROVED</CardOrderStatus> </Response> '
                    xml_res = Nokogiri::XML(result)


                    validation_status_post = 0
                    validation_status_text_post = ""
                    validation_amount_post = 0.00
                    validation_service_charge_post = 0.00
                    validation_trans_date = 0.00
                    validation_payment_type = ""
                    validation_pan = ""
                    validation_ref_id = ""
                    validation_used = ""
                    validation_verified = ""
                    validation_name = ""
                    validation_email = ""
                    validation_tbbmm_account = ""
                    validation_merchant_id = ""
                    validation_order_datetime = ""
                    validation_emi_no = ""
                    validation_interest_amount = ""
                    validation_pay_with_charge = ""
                    validation_card_response_code = ""
                    validation_card_response_desc = ""
                    validation_card_order_status = ""
                    unless xml_res.xpath("//Response/Name").empty?
                      validation_name = xml_res.xpath("//Response/Name").text
                    end
                    unless xml_res.xpath("//Response/Email").empty?
                      validation_email = xml_res.xpath("//Response/Email").text
                    end
                    unless xml_res.xpath("//Response/MarchentID").empty?
                      validation_merchant_id = xml_res.xpath("//Response/MarchentID").text
                    end
                    unless xml_res.xpath("//Response/OrderDateTime").empty?
                      validation_order_datetime = xml_res.xpath("//Response/OrderDateTime").text
                    end
                    unless xml_res.xpath("//Response/EMI_No").empty?
                      validation_emi_no = xml_res.xpath("//Response/EMI_No").text
                    end
                    unless xml_res.xpath("//Response/TBMM_Account").empty?
                      validation_tbbmm_account = xml_res.xpath("//Response/TBMM_Account").text
                    end
                    unless xml_res.xpath("//Response/InterestAmount").empty?
                      validation_interest_amount = xml_res.xpath("//Response/InterestAmount").text
                    end
                    unless xml_res.xpath("//Response/PayWithCharge").empty?
                      validation_pay_with_charge = xml_res.xpath("//Response/PayWithCharge").text
                    end
                    unless xml_res.xpath("//Response/CardResponseCode").empty?
                      validation_card_response_code = xml_res.xpath("//Response/CardResponseCode").text
                    end
                    unless xml_res.xpath("//Response/CardResponseDescription").empty?
                      validation_card_response_desc = xml_res.xpath("//Response/CardResponseDescription").text
                    end
                    unless xml_res.xpath("//Response/CardOrderStatus").empty?
                      validation_card_order_status = xml_res.xpath("//Response/CardOrderStatus").text
                    end
                    unless xml_res.xpath("//Response/Status").empty?
                      validation_status_post = xml_res.xpath("//Response/Status").text
                    end
                    unless xml_res.xpath("//Response/StatusText").empty?
                      validation_status_text_post = xml_res.xpath("//Response/StatusText").text
                    end
                    unless xml_res.xpath("//Response/Used").empty?
                      validation_used = xml_res.xpath("//Response/Used").text
                    end
                    unless xml_res.xpath("//Response/Verified").empty?
                      validation_verified = xml_res.xpath("//Response/Verified").text
                    end
                    unless xml_res.xpath("//Response/Amount").empty?
                      validation_amount_post = xml_res.xpath("//Response/Amount").text
                    end
                    unless xml_res.xpath("//Response/ServiceCharge").empty?
                      validation_service_charge_post = xml_res.xpath("//Response/ServiceCharge").text
                    end
                    unless xml_res.xpath("//Response/OrderID").empty?
                      validation_orderId = xml_res.xpath("//Response/OrderID").text
                    end
                    unless xml_res.xpath("//Response/RefID").empty?
                      validation_ref_id = xml_res.xpath("//Response/RefID").text
                    end
                    unless xml_res.xpath("//Response/PaymentDateTime").empty?
                      validation_trans_date = xml_res.xpath("//Response/PaymentDateTime").text
                    end
                    unless xml_res.xpath("//Response/PaymentType").empty?
                      validation_payment_type = xml_res.xpath("//Response/PaymentType").text
                    end
                    unless xml_res.xpath("//Response/PAN").empty?
                      validation_pan = xml_res.xpath("//Response/PAN").text
                    end

                    validation_response = {
                      :amount => validation_amount_post,
                      :name => validation_name,
                      :email => validation_email,
                      :merchant_id => validation_merchant_id,
                      :order_datetime => validation_order_datetime,
                      :emi_no => validation_emi_no,
                      :tbbmm_account => validation_tbbmm_account,
                      :interest_amount => validation_interest_amount,
                      :pay_with_charge => validation_pay_with_charge,
                      :card_response_code => validation_card_response_code,
                      :card_response_desc => validation_card_response_desc,
                      :card_order_status => validation_card_order_status,
                      :used => validation_used,
                      :verified => validation_verified,
                      :status_text => validation_status_text_post,
                      :status => validation_status_post,
                      :ref_id => validation_ref_id,
                      :order_id=>validation_orderId,
                      :tran_date=>validation_trans_date,
                      :payment_type=>validation_payment_type,
                      :service_charge=>validation_service_charge_post,
                      :pan=>validation_pan
                    }
                    @finance_order = FinanceOrder.find_by_order_id(orderId.strip)
                
                    request_params = @finance_order.request_params
                    
                    unless request_params.nil?
                      multiple = request_params[:multiple]
                      unless multiple.nil?
                        if multiple.to_s == "true"
                          fees = request_params[:fees].split(",")
                          fees.each do |fee|
                            f = fee.to_i
                            payment = Payment.find(:first, :conditions => "order_id = '#{validation_orderId}' and payee_id = #{@student.id} and payment_id = #{f}")
                            payment.update_attributes(:gateway_response => gateway_response, :validation_response => validation_response)
                          end
                        else
                          payment.update_attributes(:gateway_response => gateway_response, :validation_response => validation_response)
                        end
                      else
                        payment.update_attributes(:gateway_response => gateway_response, :validation_response => validation_response)
                      end
                    end
                    
                    #validation_payment = Payment.new(:payee => @student,:payment => @financefee, :gateway_response => gateway_response, :validation_response => validation_response)
                    #validation_payment.save
                    status = 0
                    status_text = 0
                    amount = 0
                    service_charge = 0
                    unless xml_res.xpath("//Response/Status").empty?
                      status = xml_res.xpath("//Response/Status").text
                    end
                    unless xml_res.xpath("//Response/StatusText").empty?
                      status_text = xml_res.xpath("//Response/StatusText").text
                    end
                    unless xml_res.xpath("//Response/Amount").empty?
                      amount = xml_res.xpath("//Response/Amount").text
                    end
                    unless xml_res.xpath("//Response/ServiceCharge").empty?
                      service_charge = xml_res.xpath("//Response/ServiceCharge").text
                    end

                    if status.to_i != 1
                      gateway_status = false
                      if status.to_i == 8
                        msg = "Payment unsuccessful!! Transaction is rejected"
                      elsif status.to_i == 9
                        msg = "Payment unsuccessful!! Transaction is cancelled"
                      else
                        msg = "Payment unsuccessful!! Invalid Transaction"
                      end
                    else
                      if amount.to_f == amount_post.to_f && service_charge.to_f == service_charge_post.to_f
                        gateway_status = true
                      else
                        msg = "Payment unsuccessful!! Invalid Transaction, Amount or service charge mismatch"
                        gateway_status = false
                      end
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
                elsif @active_gateway == "trustbank"
                  amount_from_gateway=amount
                end
                
                #trans_id=@financefee.fee_transactions.collect(&:finance_transaction_id).join(",")
                
                unless request_params.nil?
                  total_fees = request_params[:amount_to_pay]
                end
                
                if gateway_status == true

                  #trans_id=@financefee.fee_transactions.collect(&:finance_transaction_id).join(",")

                  unless request_params.nil?
                    multiple = request_params[:multiple]
                    unless multiple.nil?
                      if multiple.to_s == "true"
                        fees = request_params[:fees].split(",")
                        pay_student_index(amount_from_gateway, total_fees, request_params, orderId, trans_date, ref_id, fees)
                      else
                        pay_student(amount_from_gateway, total_fees, request_params, orderId, trans_date, ref_id)
                      end
                    else
                      pay_student(amount_from_gateway, total_fees, request_params, orderId, trans_date, ref_id)
                    end
                  end
                  
                else
                  if @active_gateway == "trustbank"
                    flash[:notice] = msg
                  else
                    flash[:notice] = "#{t('payment_failed')}"
                  end
                end 
              else
                flash[:notice] = "#{t('payment_failed')}"
              end 

            end
            
            #@fine_amount=0 if (@student.finance_fee_by_date @date).is_paid
            unless params[:mobile_view].blank?
              render 'gateway_payments/paypal/mobile_fee_details',:layout => false
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
      advance_fee_particular = []
      unless advance_fee.nil? or advance_fee.empty? or advance_fee.blank?
        advance_fee_particular = advance_fee.map(&:particular_id)
      end
      if MultiSchool.current_school.id == 312
        if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee_particular.include?(0))
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)) }
          @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          @discounts_amount[ind] = []
          @discounts[ind].each do |d|
            @discounts_amount[ind][d.id] = @total_payable[ind] * d.discount.to_f/ (d.is_amount?? @total_payable[ind] : 100)
            @total_discount[ind] = @total_discount[ind] + @discounts_amount[ind][d.id]
          end
        else
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)) }
          @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          @discounts_amount[ind] = []
          @discounts[ind].each do |d|
            @discounts_amount[ind][d.id] = @total_payable[ind] * d.discount.to_f/ (d.is_amount?? @total_payable[ind] : 100)
            @total_discount[ind] = @total_discount[ind] + @discounts_amount[ind][d.id]
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
          @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          if @onetime_discounts[ind].length > 0
            one_time_total_amount_discount= true
            @onetime_discounts_amount = []
            @onetime_discounts[ind].each do |d|
              @onetime_discounts_amount[ind][d.id] = (@total_payable[ind] - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable[ind] : 100)
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
                unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                  payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
                  discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                  @onetime_discounts_amount[ind][d.id] = discount_amt
                  @total_discount[ind] = @total_discount[ind] + discount_amt
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
              @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
              @discounts_amount[ind] = []
              @discounts[ind].each do |d|   
                fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                  payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
                  discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                  @discounts_amount[ind][d.id] = discount_amt
                  @total_discount[ind] = @total_discount[ind] + discount_amt
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
                @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                @discounts_amount[ind] = []
                @discounts[ind].each do |d|
                  @discounts_amount[ind][d.id] = (@total_payable[ind] - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable[ind] : 100)
                  @total_discount[ind] = @total_discount[ind] + @discounts_amount[ind][d.id]
                end
              end
            end
          end
        else
          one_time_total_amount_discount= false
          one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }

          if @onetime_discounts[ind].length > 0
            one_time_total_amount_discount= true
            @onetime_discounts_amount[ind] = []
            @onetime_discounts[ind].each do |d|
              @onetime_discounts_amount[ind][d.id] = @total_payable[ind] * d.discount.to_f/ (d.is_amount?? @total_payable[ind] : 100)
              @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
            end
          end

          unless one_time_total_amount_discount
            fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
            discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            if discounts_on_particulars.length > 0
              @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
              @discounts_amount[ind] = []
              @discounts[ind].each do |d|   
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
                  @discounts_amount[ind][d.id] = discount_amt
                  @total_discount[ind] = @total_discount[ind] + discount_amt
                end
              end
            else  
              unless one_time_discount
                deduct_fee = 0
                @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                @discounts_amount[ind] = []
                @discounts[ind].each do |d|
                  @discounts_amount[ind][d.id] = (@total_payable[ind] - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable[ind] : 100)
                  @total_discount[ind] = @total_discount[ind] + @discounts_amount[ind][d.id]
                end
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
    
    def arrange_pay(student_id, fee_collection_id, submission_date)
      advance_fee_collection = false
      @self_advance_fee = false
      @fee_has_advance_particular = false

      @student = Student.find(student_id)
      @batch = @student.batch

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
      
      flash[:warning]=nil
      #flash[:notice]=nil
      
      @trans_id_ssl_commerce = "tran"+student_id.to_s+fee_collection_id.to_s
      @paid_fees = @financefee.finance_transactions

      if advance_fee_collection
        fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
        if fee_collection_advances_particular.include?(0)
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
        else
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee.batch_id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
        end
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
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
        calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
      else
        if @fee_has_advance_particular
          calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
        else
          calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
        end
      end

      bal=(@total_payable-@total_discount).to_f
      unless submission_date.nil? or submission_date.empty? or submission_date.blank?
        require 'date'
        @submission_date = Date.parse(submission_date)
        days=(Date.parse(submission_date)-@date.due_date.to_date).to_i
      else
        @submission_date = Date.today
        if @financefee.is_paid
          @paid_fees = @financefee.finance_transactions
          days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
        else
          days=(Date.today-@date.due_date.to_date).to_i
        end
      end

      auto_fine=@date.fine

      @has_fine_discount = false
      if days > 0 and auto_fine #and @financefee.is_paid == false
        @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
        @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule

        calculate_extra_fine(@date, @financefee.batch, @student, @fine_rule)

        @new_fine_amount = @fine_amount
        get_fine_discount(@date, @financefee.batch, @student)
        if @fine_amount < 0
           @fine_amount = 0
        end
      end

      @fine_amount=0 if @financefee.is_paid

      unless advance_fee_collection
        if @total_discount == 0
          @adv_fee_discount = true
          @actual_discount = 0
          calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
        end
      end

      total_fees =@financefee.balance.to_f+@fine_amount.to_f

      if @active_gateway == "trustbank"
        paid_fees = @financefee.finance_transactions
        paid_amount = 0.0
        unless paid_fees.nil? or paid_fees.blank?
          paid_fees.each do |pf|
            paid_amount += pf.amount
          end
        end
        remaining_amount = bal - paid_amount

        remaining_amount = total_fees - paid_amount

        unless @financefee.is_paid
          finance_order = FinanceOrder.find(:first, :conditions => "finance_fee_id = #{@financefee.id} and student_id = #{@financefee.student_id} and batch_id = #{@financefee.batch_id} and status = 0")
          unless finance_order.nil?
            @order_id = "O" + finance_order.id.to_s
            finance_order.update_attributes(:order_id => @order_id)
          else
            finance_order = FinanceOrder.new()
            finance_order.finance_fee_id = @financefee.id
            finance_order.student_id = @financefee.student_id
            finance_order.batch_id = @financefee.batch_id
            finance_order.balance = remaining_amount
            finance_order.save
            @order_id = "O" + finance_order.id.to_s
            finance_order.update_attributes(:order_id => @order_id)
          end
          
          payment = Payment.find(:first, :conditions => "order_id = '#{@order_id}'")
          unless payment.nil?
            finance_transaction_id = payment.finance_transaction_id
            unless finance_transaction_id.nil?
              finance_order = FinanceOrder.new()
              finance_order.finance_fee_id = @financefee.id
              finance_order.student_id = @financefee.student_id
              finance_order.batch_id = @financefee.batch_id
              finance_order.balance = remaining_amount
              finance_order.save
              @order_id = "O" + finance_order.id.to_s
              finance_order.update_attributes(:order_id => @order_id)
            end

          end
        end
        
        
      end

      if @active_gateway == "Authorize.net"
        @sim_transaction = AuthorizeNet::SIM::Transaction.new(@merchant_id,@certificate, total_fees,{:hosted_payment_form => true,:x_description => "Fee-#{@student.admission_no}-#{@fee_collection.name}"})
        @sim_transaction.instance_variable_set("@custom_fields",{:x_description => "Fee (#{@student.full_name}-#{@student.admission_no}-#{@fee_collection.name})"})
        @sim_transaction.set_hosted_payment_receipt(AuthorizeNet::SIM::HostedReceiptPage.new(:link_method => AuthorizeNet::SIM::HostedReceiptPage::LinkMethod::GET, :link_text => "Back to #{current_school_name}", :link_url => URI.parse("http://#{request.host_with_port}/student/fee_details/#{student_id}/#{fee_collection_id}?create_transaction=1&only_path=false")))
      end
    end
    
    def arrange_multiple_pay(student_id, fees, submission_date)
      @order_id_saved = false
      @student = Student.find(student_id)
      @batch = @student.batch

      @fees_collections = fees

      @self_advance_fee = [] 
      @fee_has_advance_particular = []
      @date = [] 
      @fee_collection = []
      @student_has_due = []
      @financefee = []
      @advance_ids = []
      @fee_collection_advances = []
      @paid_fees = []
      @fee_particulars = []
      @total_payable = []
      @total_discount = []
      @adv_fee_discount = []
      @actual_discount = []
      @discounts_amount = []
      @discounts = []
      @onetime_discounts = []
      @onetime_discounts_amount = []
      @has_fine_discount = []
      @fine = []
      @fine_rule = []
      @fine_amount = []
      @vat = []
      @amount_to_pay = []
      @new_fine_amount = []

      fees.each do |fee|
        f = fee.to_i
        finance_fee = FinanceFee.find(f)
        fee_collection_id = finance_fee.fee_collection_id
        advance_fee_collection = false
        @self_advance_fee[f] = false
        @fee_has_advance_particular[f] = false

        @date[f] = @fee_collection[f] = FinanceFeeCollection.find(fee_collection_id)
        @student_has_due[f] = false
        @std_finance_fee_due = FinanceFee.find(:first,:conditions=>["finance_fee_collections.due_date < ? and finance_fees.is_paid = 0 and finance_fees.student_id = ?", @date[f].due_date,@student.id],:include=>"finance_fee_collection")
        unless @std_finance_fee_due.blank?
          @student_has_due[f] = true
        end
        @financefee[f] = @student.finance_fee_by_date(@date[f])

        if @financefee[f].has_advance_fee_id
          if @date[f].is_advance_fee_collection
            @self_advance_fee[f] = true
            advance_fee_collection = true
          end
          @fee_has_advance_particular[f] = true
          @advance_ids[f] = @financefee[f].fees_advances.map(&:advance_fee_id)
          @fee_collection_advances[f] = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids[f].join(",")})")
        end

        @due_date = @fee_collection[f].due_date

        flash[:warning]=nil
        #flash[:notice]=nil

        @trans_id_ssl_commerce = "tran"+student_id.to_s+fee_collection_id.to_s
        @paid_fees[f] = @financefee[f].finance_transactions

        if advance_fee_collection
          fee_collection_advances_particular = @fee_collection_advances[f].map(&:particular_id)
          if fee_collection_advances_particular.include?(0)
            @fee_particulars[f] = @date[f].finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee[f].batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[f].batch) }
          else
            @fee_particulars[f] = @date[f].finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee[f].batch_id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[f].batch) }
          end
        else
          @fee_particulars[f] = @date[f].finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee[f].batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[f].batch) }
        end

        if advance_fee_collection
          month = 1
          payable = 0
          @fee_collection_advances[f].each do |fee_collection_advance|
            @fee_particulars[f].each do |particular|
              if fee_collection_advance.particular_id == particular.finance_fee_particular_category_id
                payable += particular.amount * fee_collection_advance.no_of_month.to_i
              else
                payable += particular.amount
              end
            end
          end
          @total_payable[f]=payable.to_f
        else  
          @total_payable[f]=@fee_particulars[f].map{|s| s.amount}.sum.to_f
        end

        @total_discount[f] = 0

        @adv_fee_discount[f] = false
        @actual_discount[f] = 1

        if advance_fee_collection
          calculate_discount_index(@date[f],@financefee[f].batch,@student,f,true,@fee_collection_advances[f],@fee_has_advance_particular[f])
        else
          if @fee_has_advance_particular[f]
            calculate_discount_index(@date[f], @financefee[f].batch, @student,f, false, @fee_collection_advances[f], @fee_has_advance_particular[f])
          else
            calculate_discount_index(@date[f], @financefee[f].batch, @student,f, false, nil, @fee_has_advance_particular[f])
          end
        end

        bal=(@total_payable[f]-@total_discount[f]).to_f
        unless submission_date.nil? or submission_date.empty? or submission_date.blank?
          require 'date'
          @submission_date = Date.parse(submission_date)
          days=(Date.parse(submission_date)-@date[f].due_date.to_date).to_i
        else
          @submission_date = Date.today
          if @financefee[f].is_paid
            @paid_fees[f] = @financefee[f].finance_transactions
            days=(@paid_fees[f].first.transaction_date-@date[f].due_date.to_date).to_i
          else
            days=(Date.today-@date[f].due_date.to_date).to_i
          end
        end

        auto_fine=@date[f].fine

        @has_fine_discount[f] = false
        if days > 0 and auto_fine #and @financefee[f].is_paid == false
          @fine_rule[f]=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date[f].created_at}'"],:order=>'fine_days ASC')
          @fine_amount[f]=@fine_rule[f].is_amount ? @fine_rule[f].fine_amount : (bal*@fine_rule[f].fine_amount)/100 if @fine_rule[f]

          calculate_extra_fine_index(@date[f], @financefee[f].batch, @student, @fine_rule[f],f)

          @new_fine_amount[f] = @fine_amount[f]
          get_fine_discount_index(@date[f], @financefee[f].batch, @student, f)
          if @fine_amount[f] < 0
             @fine_amount[f] = 0
          end
        end

        @fine_amount[f]=0 if @financefee[f].is_paid

        unless advance_fee_collection
          if @total_discount[f] == 0
            @adv_fee_discount[f] = true
            @actual_discount[f] = 0
            calculate_discount_index(@date[f], @financefee[f].batch, @student, f,false, nil, @fee_has_advance_particular[f])
          end
        end

        total_fees =@financefee[f].balance.to_f+@fine_amount[f].to_f

        if @active_gateway == "trustbank"
          paid_fees = @financefee[f].finance_transactions
          paid_amount = 0.0
          unless paid_fees.nil? or paid_fees.blank?
            paid_fees.each do |pf|
              paid_amount += pf.amount
            end
          end
          remaining_amount = total_fees - paid_amount

          unless @financefee[f].is_paid
            finance_order = FinanceOrder.find(:first, :conditions => "finance_fee_id = #{@financefee[f].id} and student_id = #{@financefee[f].student_id} and batch_id = #{@financefee[f].batch_id} and status = 0")
            unless finance_order.nil?
              if @order_id_saved
                finance_order.update_attributes(:order_id => @order_id)
              else
                @order_id = "O" + finance_order.id.to_s
                finance_order.update_attributes(:order_id => @order_id)
                @order_id_saved = true
              end

            else
              if @order_id_saved
                finance_order = FinanceOrder.new()
                finance_order.finance_fee_id = @financefee[f].id
                finance_order.order_id = @order_id
                finance_order.student_id = @financefee[f].student_id
                finance_order.batch_id = @financefee[f].batch_id
                finance_order.balance = remaining_amount
                finance_order.save
              else
                finance_order = FinanceOrder.new()
                finance_order.finance_fee_id = @financefee[f].id
                finance_order.student_id = @financefee[f].student_id
                finance_order.batch_id = @financefee[f].batch_id
                finance_order.balance = remaining_amount
                finance_order.save
                @order_id = "O" + finance_order.id.to_s
                @order_id_saved = true
                finance_order.update_attributes(:order_id => @order_id)
              end
            end
            
            payment = Payment.find(:first, :conditions => "order_id = '#{@order_id}'")
            unless payment.nil?
              finance_transaction_id = payment.finance_transaction_id
              unless finance_transaction_id.nil?
                finance_order = FinanceOrder.new()
                finance_order.finance_fee_id = @financefee.id
                finance_order.student_id = @financefee.student_id
                finance_order.batch_id = @financefee.batch_id
                finance_order.balance = remaining_amount
                finance_order.save
                @order_id = "O" + finance_order.id.to_s
                finance_order.update_attributes(:order_id => @order_id)
              end

            end
          end
        end

        if @active_gateway == "Authorize.net"
          @sim_transaction = AuthorizeNet::SIM::Transaction.new(@merchant_id,@certificate, total_fees,{:hosted_payment_form => true,:x_description => "Fee-#{@student.admission_no}-#{@fee_collection[f].name}"})
          @sim_transaction.instance_variable_set("@custom_fields",{:x_description => "Fee (#{@student.full_name}-#{@student.admission_no}-#{@fee_collection[f].name})"})
          @sim_transaction.set_hosted_payment_receipt(AuthorizeNet::SIM::HostedReceiptPage.new(:link_method => AuthorizeNet::SIM::HostedReceiptPage::LinkMethod::GET, :link_text => "Back to #{current_school_name}", :link_url => URI.parse("http://#{request.host_with_port}/student/fee_details/#{student_id}/#{fee_collection_id}?create_transaction=1&only_path=false")))
        end
      end
    end
    
    def pay_student_index(amount_from_gateway, total_fees, request_params, orderId, trans_date, ref_id, fees)
      unless amount_from_gateway.to_f < 0
        unless amount_from_gateway.to_f > Champs21Precision.set_and_modify_precision(total_fees).to_f
          transaction_parent = FinanceTransaction.new
          transaction_parent.title = "#{t('receipt_no')}. F#{orderId}"
          transaction_parent.category = FinanceTransactionCategory.find_by_name("Fee")
          transaction_parent.payee = @student
          #transaction_parent.finance = @financefee[f]
          transaction_parent.amount = total_fees
          transaction_parent.fine_included = false
          transaction_parent.fine_amount = 0.00
          transaction_parent.transaction_date = Date.today
          transaction_parent.payment_mode = "Online Payment"
          transaction_parent.save
        
          #abort(transaction_parent.inspect)
          if transaction_parent.save
            fees.each do |fee|
              f = fee.to_i
              #abort(request_params.inspect)
              unless @financefee[f].is_paid?
                unless amount_from_gateway.to_f < 0
                    unless amount_from_gateway.to_f > Champs21Precision.set_and_modify_precision(total_fees).to_f

                    transaction = FinanceTransaction.new
                    transaction.title = "#{t('receipt_no')}. F#{@financefee[f].id}"
                    transaction.category = FinanceTransactionCategory.find_by_name("Fee")
                    transaction.payee = @student
                    transaction.finance = @financefee[f]
                    transaction.amount = request_params["amount_to_pay_#{f.to_s}"]
                    transaction.fine_included = (@fine[f].to_f + @fine_amount[f].to_f).zero? ? false : true
                    transaction.fine_amount = @fine[f].to_f + @fine_amount[f].to_f
                    transaction.transaction_date = Date.today
                    transaction.payment_mode = "Online Payment"
                    transaction.is_child_transaction = true
                    transaction.parent_transaction_id = transaction_parent.id
                    transaction.save
                    if transaction.save
                      total_fine_amount = 0
                      unless (@fine[f].to_f + @fine_amount[f].to_f).zero?
                        total_fine_amount = @fine[f].to_f + @fine_amount[f].to_f
                      end
                      is_paid =@financefee[f].balance==0 ? true : false
                      @financefee[f].update_attributes( :is_paid=>is_paid)

                      proccess_particulars_category = []
                      loop_particular = 0
                      unless request_params.nil?
                        @fee_particulars[f].each do |fp|
                          advanced = false
                          particular_amount = fp.amount.to_f
                          unless request_params["fee_particular_" + fp.id.to_s + "_" + f.to_s].nil?
                            if request_params["fee_particular_" + fp.id.to_s + "_" + f.to_s] == "on"
                              paid_amount = request_params["fee_particular_amount_" + fp.id.to_s + "_" + f.to_s].to_f
                              left_amount = particular_amount - paid_amount
                              amount_paid = 0
                              if  left_amount == 0
                                amount_paid = particular_amount
                              elsif  left_amount < 0
                                advanced = true
                                amount_paid = particular_amount
                              elsif left_amount > 0
                                amount_paid = paid_amount
                              end
                              
                              finance_transaction_particular = FinanceTransactionParticular.new
                              finance_transaction_particular.finance_transaction_id = transaction.id
                              finance_transaction_particular.particular_id = fp.id
                              finance_transaction_particular.particular_type = 'Particular'
                              finance_transaction_particular.transaction_type = 'Fee Collection'
                              finance_transaction_particular.amount = amount_paid
                              finance_transaction_particular.transaction_date = transaction.transaction_date
                              finance_transaction_particular.save

                              if advanced
                                left_amount = paid_amount - particular_amount
                                finance_transaction_particular = FinanceTransactionParticular.new
                                finance_transaction_particular.finance_transaction_id = transaction.id
                                finance_transaction_particular.particular_id = fp.id
                                finance_transaction_particular.particular_type = 'Particular'
                                finance_transaction_particular.transaction_type = 'Advance'
                                finance_transaction_particular.amount = left_amount
                                finance_transaction_particular.transaction_date = transaction.transaction_date
                                finance_transaction_particular.save
                              end
                            end
                          end
                        end

                        unless @onetime_discounts[f].blank?
                          @onetime_discounts[f].each do |od|
                            unless request_params["fee_discount_" + od.id.to_s + "_" + f.to_s].nil?
                              if request_params["fee_discount_" + od.id.to_s + "_" + f.to_s] == "on"
                                discount_amount = request_params["fee_discount_amount_" + od.id.to_s + "_" + f.to_s].to_f
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
                          end
                        end

                        unless @discounts[f].blank?
                          @discounts[f].each do |od|
                            unless request_params["fee_discount_" + od.id.to_s + "_" + f.to_s].nil?
                              if request_params["fee_discount_" + od.id.to_s + "_" + f.to_s] == "on"
                                discount_amount = request_params["fee_discount_amount_" + od.id.to_s + "_" + f.to_s].to_f
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
                          end
                        end

                        unless request_params["fee_vat" + "_" + f.to_s].nil?
                          if request_params["fee_vat" + "_" + f.to_s] == "on"
                            vat_amount = request_params["fee_vat_amount" + "_" + f.to_s].to_f
                            finance_transaction_particular = FinanceTransactionParticular.new
                            finance_transaction_particular.finance_transaction_id = transaction.id
                            finance_transaction_particular.particular_id = 0
                            finance_transaction_particular.particular_type = 'VAT'
                            finance_transaction_particular.transaction_type = ''
                            finance_transaction_particular.amount = vat_amount
                            finance_transaction_particular.transaction_date = transaction.transaction_date
                            finance_transaction_particular.save
                          end
                        end

                        unless request_params["fee_fine" + "_" + f.to_s].nil?
                          if request_params["fee_fine" + "_" + f.to_s] == "on"
                            fine_amount = request_params["fine_amount_to_pay" + "_" + f.to_s].to_f
                            finance_transaction_particular = FinanceTransactionParticular.new
                            finance_transaction_particular.finance_transaction_id = transaction.id
                            finance_transaction_particular.particular_id = 0
                            finance_transaction_particular.particular_type = 'Fine'
                            finance_transaction_particular.transaction_type = ''
                            finance_transaction_particular.amount = fine_amount
                            finance_transaction_particular.transaction_date = transaction.transaction_date
                            finance_transaction_particular.save
                          end
                        end

                        if @has_fine_discount[f]
                          @discounts_on_lates[f].each do |fd|
                            unless request_params["fee_fine_discount_" + fd.id.to_s + "_" + f.to_s].nil?
                              if request_params["fee_fine_discount_" + fd.id.to_s + "_" + f.to_s] == "on"
                                discount_amount = request_params["fee_fine_discount_amount_" + fd.id.to_s + "_" + f.to_s].to_f
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
                        end


                        @finance_order = FinanceOrder.find_by_order_id_and_finance_fee_id(orderId, f)
                        #@finance_order[f] = FinanceOrder.find_by_order_id(orderId)
                        @finance_order.update_attributes(:status => 1)

                      else
                        @fee_particulars[f].each do |fp|
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

                        unless @onetime_discounts[f].blank?
                          @onetime_discounts[f].each do |od|
                            discount_amount = @onetime_discounts_amount[f][od.id].to_f
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


                        unless @discounts[f].blank?
                          @discounts[f].each do |od|
                            discount_amount = @discounts_amount[f][od.id]
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


                        if @has_fine_discount[f]
                          @discounts_on_lates[f].each do |fd|
                            discount_amount = @discounts_late_amount[f][od.id]
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
                    end
                    
                    transaction_id = transaction.id
                    particular_amount = 0.00
                    particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction_id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection'"], :group => "finance_transaction_particulars.finance_transaction_id")
                    particular_wise_transactions.each do |pt|
                      particular_amount += pt.amount.to_f
                    end

                    particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction_id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Advance'"], :group => "finance_transaction_particulars.finance_transaction_id")
                    particular_wise_transactions.each do |pt|
                      particular_amount += pt.amount.to_f
                    end

                    particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction_id} and finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount'"], :group => "finance_transaction_particulars.finance_transaction_id")
                    particular_wise_transactions.each do |pt|
                      particular_amount -= pt.amount.to_f
                    end

                    if particular_amount.to_f != transaction.amount.to_f
                      finance_notmatch_transaction = FinanceNotmatchTransaction.new
                      finance_notmatch_transaction.transaction_id = transaction_id
                      finance_notmatch_transaction.run_from = "StudentPayController - PayStudentIndex"
                      finance_notmatch_transaction.save
                    end

                    payment = Payment.find_by_order_id_and_payee_id_and_payment_id(orderId, @student.id, f)
                    #abort(payment.inspect)
                    payment.update_attributes(:finance_transaction_id => transaction.id)
                    unless @financefee[f].transaction_id.nil?
                      tid =   @financefee[f].transaction_id.to_s + ",#{transaction.id}"
                    else
                      tid=transaction.id
                    end
                    is_paid = @financefee[f].balance==0 ? true : false



                    @financefee[f].update_attributes(:transaction_id=>tid, :is_paid=>is_paid)
                    #@paid_fees = FinanceTransaction.find(:all,:conditions=>"FIND_IN_SET(id,\"#{tid}\")")
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
                        message = message.gsub("#AMOUNT#", amount_from_gateway.to_s)
                        message = message.gsub("#PAIDDATE#", trans_date.to_date.strftime("%d-%m-%Y"))
                        message = message.gsub("#TRANID#", orderId)
                        message = message.gsub("#TRANREF#", ref_id)
                        recipients.push @student.sms_number
                      else
                        unless @student.phone2.nil? or @student.phone2.empty? or @student.phone2.blank?
                          message = message
                          message = message.gsub("#UNAME#", @student.full_name)
                          message = message.gsub("#UID#", @student.admission_no)
                          message = message.gsub("#AMOUNT#", amount_from_gateway.to_s)
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


                    flash[:success] = "Thanks for your payment, payment was Successfull. Your order ID is: #{orderId}"
                  else
                    flash[:notice] = "#{t('payment_failed')}"
                  end
                else
                  flash[:notice] = "#{t('payment_failed')}"
                end
              else
                flash[:notice] = "#{t('flash_payed')}"
              end

            end
          end
        end
      end
    end
    
    def pay_student(amount_from_gateway, total_fees, request_params, orderId, trans_date, ref_id)
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
              unless request_params.nil?
                @fee_particulars.each do |fp|
                  advanced = false
                  particular_amount = fp.amount.to_f
                  unless request_params["fee_particular_" + fp.id.to_s].nil?
                    if request_params["fee_particular_" + fp.id.to_s] == "on"
                      paid_amount = request_params["fee_particular_amount_" + fp.id.to_s].to_f
                      left_amount = particular_amount - paid_amount
                      amount_paid = 0
                      if  left_amount == 0
                        amount_paid = particular_amount
                      elsif  left_amount < 0
                        advanced = true
                        amount_paid = particular_amount
                      elsif left_amount > 0
                        amount_paid = paid_amount
                      end
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = fp.id
                      finance_transaction_particular.particular_type = 'Particular'
                      finance_transaction_particular.transaction_type = 'Fee Collection'
                      finance_transaction_particular.amount = amount_paid
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save

                      if advanced
                        left_amount = paid_amount - particular_amount
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = fp.id
                        finance_transaction_particular.particular_type = 'Particular'
                        finance_transaction_particular.transaction_type = 'Advance'
                        finance_transaction_particular.amount = left_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      end
                    else
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
                  else
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
                end

                unless @onetime_discounts.blank?
                  @onetime_discounts.each do |od|
                    unless request_params["fee_discount_" + od.id.to_s].nil?
                      if request_params["fee_discount_" + od.id.to_s] == "on"
                        discount_amount = request_params["fee_discount_amount_" + od.id.to_s].to_f
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = od.id
                        finance_transaction_particular.particular_type = 'Adjustment'
                        finance_transaction_particular.transaction_type = 'Discount'
                        finance_transaction_particular.amount = discount_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      else
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
                    else
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
                end

                unless @discounts.blank?
                  @discounts.each do |od|
                    unless request_params["fee_discount_" + od.id.to_s].nil?
                      if request_params["fee_discount_" + od.id.to_s] == "on"
                        discount_amount = request_params["fee_discount_amount_" + od.id.to_s].to_f
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = od.id
                        finance_transaction_particular.particular_type = 'Adjustment'
                        finance_transaction_particular.transaction_type = 'Discount'
                        finance_transaction_particular.amount = discount_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      else
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
                    else
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
                end

                unless request_params[:fee_vat].nil?
                  if request_params[:fee_vat] == "on"
                    vat_amount = request_params[:fee_vat_amount].to_f
                    finance_transaction_particular = FinanceTransactionParticular.new
                    finance_transaction_particular.finance_transaction_id = transaction.id
                    finance_transaction_particular.particular_id = 0
                    finance_transaction_particular.particular_type = 'VAT'
                    finance_transaction_particular.transaction_type = ''
                    finance_transaction_particular.amount = vat_amount
                    finance_transaction_particular.transaction_date = transaction.transaction_date
                    finance_transaction_particular.save
                  else
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
                  end
                else
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
                end

                unless request_params[:fee_fine].nil?
                  if request_params[:fee_fine] == "on"
                    fine_amount = request_params[:fine_amount_to_pay].to_f
                    finance_transaction_particular = FinanceTransactionParticular.new
                    finance_transaction_particular.finance_transaction_id = transaction.id
                    finance_transaction_particular.particular_id = 0
                    finance_transaction_particular.particular_type = 'Fine'
                    finance_transaction_particular.transaction_type = ''
                    finance_transaction_particular.amount = fine_amount
                    finance_transaction_particular.transaction_date = transaction.transaction_date
                    finance_transaction_particular.save
                  else
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
                  end
                else
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
                end

                if @has_fine_discount
                  @discounts_on_lates.each do |fd|
                    unless request_params["fee_fine_discount_" + fd.id.to_s].nil?
                      if request_params["fee_fine_discount_" + fd.id.to_s] == "on"
                        discount_amount = request_params["fee_fine_discount_amount_" + fd.id.to_s].to_f
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = fd.id
                        finance_transaction_particular.particular_type = 'FineAdjustment'
                        finance_transaction_particular.transaction_type = 'Discount'
                        finance_transaction_particular.amount = discount_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      else
                        discount_amount = @discounts_late_amount[od.id]
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = fd.id
                        finance_transaction_particular.particular_type = 'FineAdjustment'
                        finance_transaction_particular.transaction_type = 'Discount'
                        finance_transaction_particular.amount = discount_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      end
                    else
                      discount_amount = @discounts_late_amount[od.id]
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


                @finance_order = FinanceOrder.find_by_order_id(orderId)
                @finance_order.update_attributes(:status => 1)

              else
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
            end
            
            transaction_id = transaction.id
            particular_amount = 0.00
            particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction_id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection'"], :group => "finance_transaction_particulars.finance_transaction_id")
            particular_wise_transactions.each do |pt|
              particular_amount += pt.amount.to_f
            end

            particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction_id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Advance'"], :group => "finance_transaction_particulars.finance_transaction_id")
            particular_wise_transactions.each do |pt|
              particular_amount += pt.amount.to_f
            end

            particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction_id} and finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount'"], :group => "finance_transaction_particulars.finance_transaction_id")
            particular_wise_transactions.each do |pt|
              particular_amount -= pt.amount.to_f
            end

            if particular_amount.to_f != transaction.amount.to_f
              finance_notmatch_transaction = FinanceNotmatchTransaction.new
              finance_notmatch_transaction.run_from = "StudentPayController - PayStudent"
              finance_notmatch_transaction.transaction_id = transaction_id
              finance_notmatch_transaction.save
            end

            payment = Payment.find_by_order_id_and_payee_id_and_payment_id(orderId, @student.id, @financefee.id)
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
                message = message.gsub("#AMOUNT#", amount_from_gateway.to_s)
                message = message.gsub("#PAIDDATE#", trans_date.to_date.strftime("%d-%m-%Y"))
                message = message.gsub("#TRANID#", orderId)
                message = message.gsub("#TRANREF#", ref_id)
                recipients.push @student.sms_number
              else
                unless @student.phone2.nil? or @student.phone2.empty? or @student.phone2.blank?
                  message = message
                  message = message.gsub("#UNAME#", @student.full_name)
                  message = message.gsub("#UID#", @student.admission_no)
                  message = message.gsub("#AMOUNT#", amount_from_gateway.to_s)
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


            flash[:success] = "Thanks for your payment, payment was Successfull. Your order ID is: #{orderId}"
          else
            flash[:notice] = "#{t('payment_failed')}"
          end
        else
          flash[:notice] = "#{t('payment_failed')}"
        end
      else
        flash[:notice] = "#{t('flash_payed')}"
      end
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
