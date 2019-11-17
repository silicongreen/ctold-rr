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
                  #abort('here1')
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
