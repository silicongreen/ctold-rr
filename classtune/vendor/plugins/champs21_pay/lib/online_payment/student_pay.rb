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
      #test_user_for_payment_check = []
      test_user = PaymentConfiguration.config_value("test_user_for_payment_check")
      test_users = test_user.split(",") unless test_user.blank?
      test_users ||= Array.new
      found_test_user = false
      has_test_user = false
      unless test_users.blank?
        student_id = params[:id]
        has_test_user = true
        if test_users.map(&:to_i).include?(student_id.to_i)
          found_test_user = true
        end
      end
#      require 'date'
#      s = "2020-02-25T13:22:32:790 GMT+0000"
#      abort(DateTime.parse(s).to_datetime.strftime("%Y-%m-%d %H:%M:%S"))

      
      transaction_datetime = now
      if Champs21Plugin.can_access_plugin?("champs21_pay")
        if (PaymentConfiguration.config_value("enabled_fees").present? and PaymentConfiguration.config_value("enabled_fees").include? "Student Fee") 
          @payment_gateways = PaymentConfiguration.config_value("champs21_gateway")
          @payment_gateway = @payment_gateways.split(",") unless @payment_gateways.blank?
          @payment_gateway ||= Array.new
          unless @payment_gateway.blank?
            @gateway_settings = {}
            @payment_gateway.each do |active_gateway|
              constant_val = active_gateway + "_CONFIG_KEYS"
              if Champs21Pay.const_defined?(constant_val.upcase)
                tmp = Hash.new
                active_gateway_fields = Champs21Pay.const_get(constant_val.upcase)
                active_gateway_fields.each do |field|
                  if field.index("is_").nil?
                    config_val = PaymentConfiguration.config_value(active_gateway + "_" + field)
                    config_val ||= String.new
                    tmp[field] = config_val
                    config_val = String.new
                  end
                end
                must_include = true
                if found_test_user
                  if PaymentConfiguration.config_value('is_test_' + active_gateway).to_i == 0
                    must_include = false
                  end
                end
                if has_test_user and found_test_user == false
                  if PaymentConfiguration.config_value('is_test_' + active_gateway).to_i == 1
                    must_include = false
                  end
                end
                if must_include
                  @gateway_settings[active_gateway] = tmp
                end
              end
            end
          else  
            fee_details_without_gateway and return
          end
          #abort(@gateway_settings.)
          current_school_name = Configuration.find_by_config_key('InstitutionName').try(:config_value)
          
          #@fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
          #@total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
          
          if request.post? and params[:order_id].present?
            
            @fee_collection_name = ( params[:fee_collection_name].blank? ) ? "Student Fees" : params[:fee_collection_name]
            @user_gateway = @gateway_settings.keys[0].to_s
            unless params[:user_gateway].blank?
              @user_gateway = params[:user_gateway].to_s
            end
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
            #
            #if params[:create_transaction].present? and params[:target_gateway] == "citybank"
#              if params[:t].present?
#                city_fees = "t=" + params[:t].gsub("--","&")
#                kparams = {}
#                city_fees.split(/&/).inject({}) do |hash, setting|
#                  key, val = setting.split(/=/)
#                  params[key.to_sym] = val
#                end
#                if params[:target_gateway] == "citybank" and params[:create_fail_transaction].present?
#                  flash[:notice] = "Payment unsuccessful"
#                elsif params[:target_gateway] == "citybank" and params[:create_cancel_transaction].present?
#                  flash[:notice] = "Payment cancel by user"
#                end
#              end
#              abort(params.inspect)
#if MultiSchool.current_school.id == 2
#              abort(params.inspect)
#            end
              unless params[:target_gateway] == "trustbank"
                unless params[:fees].blank?
                  unless params[:fees].index("--").nil?
                    city_fees = "fees=" + params[:fees].gsub("--","&")
                    kparams = {}
                    city_fees.split(/&/).inject({}) do |hash, setting|
                      key, val = setting.split(/=/)
                      params[key.to_sym] = val
                    end
                  end
                else
                  if params[:t].present?
                    unless params[:t].index("--").nil?
                      city_fees = "t=" + params[:t].gsub("--","&")
                      kparams = {}
                      city_fees.split(/&/).inject({}) do |hash, setting|
                        key, val = setting.split(/=/)
                        params[key.to_sym] = val
                      end
                    end
                  elsif params[:mobile_view].present?
                    unless params[:mobile_view].index("--").nil?
                      city_fees = "mobile_view=" + params[:mobile_view].gsub("--","&")
                      kparams = {}
                      city_fees.split(/&/).inject({}) do |hash, setting|
                        key, val = setting.split(/=/)
                        params[key.to_sym] = val
                      end
                    end
                  end
                end
                if params[:target_gateway] == "citybank" and params[:create_fail_transaction].present?
                  save_fail_cancel_response_citybank 
                  flash[:notice] = "Payment unsuccessful"
                elsif params[:target_gateway] == "citybank" and params[:create_cancel_transaction].present?
                  save_fail_cancel_response_citybank 
                  flash[:notice] = "Payment cancel by user"
                end
              end
              
              #
            #end
            if params[:create_transaction].present?
              if params[:target_gateway] == "trustbank"
                result = Base64.decode64(params[:CheckoutXmlMsg])
                #result = '<Response date="2016-06-20 10:14:53.213">  <RefID>133783A000129D</RefID>  <OrderID>O100010</OrderID>  <Name> Customer1</Name>  <Email> mr.customer@gmail.com </Email>  <Amount>2090.00</Amount>  <ServiceCharge>0.00</ServiceCharge>  <Status>1</Status>  <StatusText>PAID</StatusText>  <Used>0</Used>  <Verified>0</Verified>  <PaymentType>ITCL</PaymentType>  <PAN>712300XXXX1277</PAN>  <TBMM_Account></TBMM_Account>  <MarchentID>SAGC</MarchentID>  <OrderDateTime>2016-06-20 10:14:24.700</OrderDateTime>  <PaymentDateTime>2016-06-20 10:21:34.303</PaymentDateTime>  <EMI_No>0</EMI_No>  <InterestAmount>0.00</InterestAmount>  <PayWithCharge>1</PayWithCharge>  <CardResponseCode>00</CardResponseCode>  <CardResponseDescription>APPROVED</CardResponseDescription>  <CardOrderStatus>APPROVED</CardOrderStatus> </Response> '
                xml_res = Nokogiri::XML(result)
                orderId = ""
                unless xml_res.xpath("//Response/OrderID").empty?
                  orderId = xml_res.xpath("//Response/OrderID").text
                end
              elsif params[:target_gateway] == "citybank"
                orderId = params[:order_id_in_trans]
              end

              @finance_order = FinanceOrder.find_by_order_id(orderId.strip)
              #abort(@finance_order.inspect)
              request_params = @finance_order.request_params
              
              multiple_param = request_params[:multiple]
              unless multiple_param.nil?
                if multiple_param.to_s == "true"
                  @collection_fees = request_params[:fees]
                  fees = request_params[:fees].split(",")
                  fee_requests = fees
                else  
                  fee_requests = params[:id2]
                end
              else
                fee_requests = params[:id2]
              end
            else
              multiple_param = params[:multiple]
              unless multiple_param.nil?
                #abort('here-1')
                if multiple_param.to_s == "true"
                  @collection_fees = params[:fees]
                  fees = params[:fees].split(",")
                  fee_requests = fees
                else  
                  fee_requests = params[:id2]
                end
              else
                fee_requests = params[:id2]
              end
            end
            if params[:order_id].present?
              now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
              activity_log = ActivityLog.new
              activity_log.user_id = current_user.id
              activity_log.controller = "Finance Log - POST CHECK"
              activity_log.action = params[:order_id].to_s
              activity_log.post_requests = params
              activity_log.ip = request.remote_ip
              activity_log.user_agent = request.user_agent
              activity_log.created_at = now
              activity_log.updated_at = now
              activity_log.save
            end
            if params[:create_transaction].present?
              
              validate_payment_types(params)
            end
            unless multiple_param.nil?
              if multiple_param.to_s == "true"
                collection_fees = fee_requests
                arrange_multiple_pay(params[:id], fees, params[:submission_date])
              else  
                arrange_pay(params[:id], fee_requests, params[:submission_date])
              end
            else
              arrange_pay(params[:id], fee_requests, params[:submission_date])
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
        @payment_gateways = PaymentConfiguration.config_value("champs21_gateway")
        if @payment_gateways.blank?
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
