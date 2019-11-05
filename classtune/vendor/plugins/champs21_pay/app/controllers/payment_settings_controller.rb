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
        res1 = result
        xml_str = Nokogiri::XML(result)
        
        verifiedId = 0
        found_verified = false
        xmlind = 0
        xml_transaction_infos = xml_str.xpath("//Response/TransactionInfo")
        xml_transaction_infos.each do |xml_transaction_info|
          childs = xml_transaction_info.children
          childs.each do |c|
            if c.name == "Verified"
              v = c.text
              if v.to_i == 1
                verifiedId = xmlind
                found_verified = true
              end
            end
          end
          xmlind += 1
        end
        if found_verified
          childs = xml_transaction_infos[verifiedId].children
        else  
          found_paid = false 
          paidId = 0
          xmlind = 0
          xml_transaction_infos.each do |xml_transaction_info|
            childs = xml_transaction_info.children
            childs.each do |c|
              if c.name == "Status"
                v = c.text
                if v.to_i == 1
                  paidId = xmlind
                  found_paid = true
                end
              end
            end
            xmlind += 1
          end
          if found_paid
            childs = xml_transaction_infos[paidId].children
          else
            childs = xml_transaction_infos[xml_transaction_infos.length - 1].children
          end
        end
        
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
        
        dt = trans_date.split(".")
        transaction_datetime = dt[0]
        
        if verified.to_i == 0
          if transaction_datetime.nil?
            dt = order_datetime.split(".")
            transaction_datetime = dt[0]
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
        
        res2 = result
        xml_str = Nokogiri::XML(result)
        #abort(res1 + "   \n\n\n " + res2)
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
        archived = false
        admission_no = name
        @student = Student.find_by_admission_no(admission_no)
        unless @student.nil?
          finance_order = FinanceOrder.find_by_order_id(o)
          unless finance_order.nil?
            fees = FinanceFee.find(:first, :conditions => "student_id = #{@student.id} and id = #{finance_order.finance_fee_id}")
          else  
            fees = FinanceFee.find(:first, :conditions => "student_id = #{@student.id} and batch_id = #{@student.batch_id}")
          end
          #fees = FinanceFee.find(:first, :conditions => "student_id = #{@student.id} and batch_id = #{@student.batch_id}")
        else
          archived = true
          @student = ArchivedStudent.find_by_admission_no(admission_no)
          unless @student.nil?
            finance_order = FinanceOrder.find_by_order_id(o)
            unless finance_order.nil?
              fees = FinanceFee.find(:first, :conditions => "student_id = #{@student.id} and id = #{finance_order.finance_fee_id}")
            else  
              fees = FinanceFee.find(:first, :conditions => "student_id = #{@student.id} and batch_id = #{@student.batch_id}")
            end
            #fees = FinanceFee.find(:first, :conditions => "student_id = #{@student.former_id} and batch_id = #{@student.batch_id}")
          end
        end
        unless archived
          if verified.to_i == 1 or verification_verified.to_i == 1
            if verified.to_i == 0
              if verification_verified.to_i == 1
                gateway_response = validation_response
              end
            end
            finance_fee_id = payment.payment_id
            payee_id = payment.payee_id
            @student = Student.find(payee_id)
            @batch = @student.batch
            fee = FinanceFee.find(:first, :conditions => "id = #{finance_fee_id} and student_id = #{payee_id}")
            
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
                      finance_notmatch_transaction.run_from = "PaymentSettingsController - PaymentVerification"
                      finance_notmatch_transaction.save
                    end
                    
                    
                    if payment.gateway_response[:verified].to_i == 0
                      unless payment.validation_response.nil?
                        if payment.validation_response[:verified].to_i == 1
                          payment.update_attributes(:gateway_response => payment.validation_response)
                        end
                      end
                    end

                    unless payment.gateway_response.nil?
                      unless payment.gateway_response[:tran_date].nil?
                        dt = payment.gateway_response[:tran_date].split(".")
                        transaction_datetime = dt[0]

                        payment.update_attributes(:transaction_datetime => transaction_datetime)
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
              send_sms_finance(messages,recipients)
            end

            paymentnew = Payment.find(payment.id)
            paymentnew.update_attributes(:gateway_response => gateway_response, :validation_response => validation_response, :transaction_datetime => transaction_datetime)
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
        else
          render :update do |page|
              page << "alert('To verified Archieved student transaction, Please use Order verification form')"
          end
        end
      else
        render :update do |page|
            page << "alert('To verified Archieved student transaction, Please use Order verification form')"
        end
      end
    end
  end
  
  def transactions
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
      extra_query += ' and gateway_response like \'%' + params[:order_id].to_s + '%\''
    end
    unless params[:ref_no].nil? or params[:ref_no].empty? or params[:ref_no].blank?
      extra_query += ' and gateway_response like \'%' + params[:ref_no].to_s + '%\''
    end
    unless params[:payment_type].nil? or params[:payment_type].empty? or params[:payment_type].blank?
      extra_query += ' and gateway_response like \'%:payment_type: ' + params[:payment_type].to_s + '%\''
    end
    #@online_payments = Payment.all.select{|p| p.created_at.to_date >= start_date.to_date and p.created_at.to_date <= end_date.to_date}.paginate(:page => params[:page],:per_page => 30)
    
    ###.paginate()
    
    unless params[:export].nil?
      if params[:export].to_i == 1
        if MultiSchool.current_school.id == 352
          @online_payments = Payment.find(:all, :conditions=>"CAST(transaction_datetime AS DATE) >= '#{start_date.to_date}' and CAST(transaction_datetime AS DATE) <= '#{end_date.to_date}' #{extra_query}", :order => "transaction_datetime DESC", :group => "order_id")
        else
          @online_payments = Payment.all.select{|p| p.created_at.to_date >= start_date.to_date and p.created_at.to_date <= end_date.to_date}
        end
        require 'spreadsheet'
        Spreadsheet.client_encoding = 'UTF-8'
    
        date = Spreadsheet::Format.new :number_format => 'MM/DD/YYYY'
    
        row_1 = ["Ref ID","Order ID","Name","Marchent ID","Amount","Fees","Service Charge","Status","Verified","Trn Date"]
        
        # Create a new Workbook
        new_book = Spreadsheet::Workbook.new

        # Create the worksheet
        new_book.create_worksheet :name => 'Online Transaction'

        # Add row_1
        new_book.worksheet(0).insert_row(0, row_1)

        ind = 1
        @online_payments.each do |payment|
          amt = payment.gateway_response[:amount].to_f
          service_change = payment.gateway_response[:service_charge].to_f
          tot_amt = amt + service_change
          verified = "false"
          unless payment.gateway_response[:verified].nil?
            if payment.gateway_response[:verified].to_i == 1
              verified = "true"
            end
          end
          row_new = [payment.gateway_response[:ref_id], payment.gateway_response[:order_id], payment.gateway_response[:name], payment.gateway_response[:merchant_id], Champs21Precision.set_and_modify_precision(tot_amt), Champs21Precision.set_and_modify_precision(amt), Champs21Precision.set_and_modify_precision(service_change), payment.gateway_response[:status], verified, I18n.l((payment.transaction_datetime.to_time).to_datetime,:format=>"%d %b %Y")]
          new_book.worksheet(0).insert_row(ind, row_new)
          ind += 1
        end
        spreadsheet = StringIO.new 
        new_book.write spreadsheet 

        send_data spreadsheet.string, :filename => "online_transactions.xls", :type =>  "application/vnd.ms-excel"
      else
        if MultiSchool.current_school.id == 352
          @online_payments = Payment.paginate(:conditions=>"CAST(transaction_datetime AS DATE) >= '#{start_date.to_date}' and CAST(transaction_datetime AS DATE) <= '#{end_date.to_date}' #{extra_query}",:page => params[:page],:per_page => 30, :order => "transaction_datetime DESC", :group => "order_id")
        else
          @online_payments = Payment.all.select{|p| p.created_at.to_date >= start_date.to_date and p.created_at.to_date <= end_date.to_date}.paginate(:page => params[:page],:per_page => 30)
        end
        #abort(@online_payments.inspect)
        respond_to do |format|
          format.html #transctions.html.erb
        end
      end
    else
      if MultiSchool.current_school.id == 352
        @online_payments = Payment.paginate(:conditions=>"CAST(transaction_datetime AS DATE) >= '#{start_date.to_date}' and CAST(transaction_datetime AS DATE) <= '#{end_date.to_date}' #{extra_query}",:page => params[:page],:per_page => 30, :order => "transaction_datetime DESC", :group => "order_id")
      else
        @online_payments = Payment.all.select{|p| p.created_at.to_date >= start_date.to_date and p.created_at.to_date <= end_date.to_date}.paginate(:page => params[:page],:per_page => 30)
      end
      respond_to do |format|
        format.html #transctions.html.erb
      end
    end
  end
  
  def order_verifications  
    
    #admission_nos = []
    if request.post?
      unless params[:order_id].blank?
        order_id_vals =  params[:order_id]
        order_ids = order_id_vals.split(",").map{ |s| s.strip }
        
        num_orders = order_ids.length
        verified_no = 0
        
        order_ids_new = []
        order_ids.each_with_index do |o, i|
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
            auth_req.set_form_data({"OrderID" => o.to_s, "MerchantID" => @merchant_id, "KeyCode" => @keycode})

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

            xml_str = Nokogiri::XML(result)
#abort(xml_str.to_s)
            verifiedId = 0 
            found_verified = false 
            xmlind = 0
            xml_transaction_infos = xml_str.xpath("//Response/TransactionInfo")
            xml_transaction_infos.each do |xml_transaction_info|
              childs = xml_transaction_info.children
              childs.each do |c|
                if c.name == "Verified"
                  v = c.text
                  if v.to_i == 1
                    verifiedId = xmlind
                    found_verified = true
                  end
                end
              end
              xmlind += 1
            end
            if found_verified
              childs = xml_transaction_infos[verifiedId].children
            else  
              found_paid = false 
              paidId = 0
              xmlind = 0
              xml_transaction_infos.each do |xml_transaction_info|
                childs = xml_transaction_info.children
                childs.each do |c|
                  if c.name == "Status"
                    v = c.text
                    if v.to_i == 1
                      paidId = xmlind
                      found_paid = true
                    end
                  end
                end
                xmlind += 1
              end
              if found_paid
                childs = xml_transaction_infos[paidId].children
              else
                childs = xml_transaction_infos[xml_transaction_infos.length - 1].children
              end
            end

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

            dt = trans_date.split(".")
            transaction_datetime = dt[0]

            if verified.to_i == 0
              if transaction_datetime.nil?
                dt = order_datetime.split(".")
                transaction_datetime = dt[0]
              end
            end

            archived = false
            #admission_no = admission_nos[i]
            admission_no = name
            @student = Student.find_by_admission_no(admission_no)
            
              
            if @student.nil?
              archived = true
              @student = ArchivedStudent.find_by_admission_no(admission_no)
            end
            
          
            request_url = @verification_url + '/Transaction_Verify_Details'
            uri = URI(request_url)
            http = Net::HTTP.new(uri.host, uri.port)
            auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
            auth_req.set_form_data({"OrderID" => orderId, "MerchantID" => @merchant_id, "RefID" => ref_id})

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

            verify_order = false
            if verified.to_i == 1 or verification_verified.to_i == 1
              if verified.to_i == 0
                if verification_verified.to_i == 1
                  gateway_response = validation_response
                end
              end
              verify_order = true
              order_ids_new << o
              verified_no += 1
            end
            
            if verify_order
              unless @student.nil?
                finance_orders = FinanceOrder.find_all_by_order_id(o)
                unless finance_orders.nil?
                  
                  finance_orders.each do |finance_order|
                    request_params = finance_order.request_params

                    fee_id = finance_order.finance_fee_id

                    fee = FinanceFee.find(:first, :conditions => "student_id = #{@student.id} and id = #{fee_id}")
                    unless fee.nil?
                      payment = Payment.find(:first, :conditions => "order_id = '#{finance_order.order_id}' and payee_id = #{@student.id} and payment_id = #{fee.id}")
                      if payment.nil?
                        payment = Payment.new(:payee => @student,:payment => fee, :order_id => finance_order.order_id,:gateway_response => gateway_response, :validation_response => validation_response, :transaction_datetime => transaction_datetime)
                        payment.save
                      else
                        payment.update_attributes(:gateway_response => gateway_response, :validation_response => validation_response, :transaction_datetime => transaction_datetime)
                      end
                      
                      unless fee.is_paid
                        date = FinanceFeeCollection.find(:first, :conditions => "id = #{fee.fee_collection_id}")
                        unless date.nil?
                          @financefee = FinanceFee.find(fee.id)
                          
                          
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
                          unless archived
                            @financefee = @student.finance_fee_by_date(@date)
                          else
                            @financefee = FinanceFee.find_by_fee_collection_id_and_student_id(@date.id, @student.former_id)
                          end

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
                              @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{fee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==fee.batch) }
                            else
                              @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{fee.batch_id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==fee.batch) }
                            end
                          else
                            @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{fee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==fee.batch) }
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
                            calculate_discount(@date, fee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
                          else
                            if @fee_has_advance_particular
                              calculate_discount(@date, fee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
                            else
                              calculate_discount(@date, fee.batch, @student, false, nil, @fee_has_advance_particular)
                            end
                          end

                          bal=(@total_payable-@total_discount).to_f

                          @fine_amount=0 if @financefee.is_paid

                          unless advance_fee_collection
                            if @total_discount == 0
                              @adv_fee_discount = true
                              @actual_discount = 0
                              calculate_discount(@date, fee.batch, @student, false, nil, @fee_has_advance_particular)
                            end
                          end
                          #abort(@financefee.inspect)
                          total_fees = @financefee.balance.to_f+@fine_amount.to_f

                          amount_from_gateway = amount
                          if total_fees.to_f > amount_from_gateway.to_f
                            amount_from_gateway = total_fees - 1
                          end
                          
                          #abort(amount_from_gateway.to_s + " " + total_fees.to_s + "  " + @fine_amount.to_s)
                          pay_student(amount_from_gateway, total_fees, request_params, finance_order.order_id, verification_trans_date, ref_id)
                        end
                      else
                        paid_fees = fee.finance_transactions
                        unless paid_fees.nil?
                          paid_fees.each do |paid_fee|
                            payment.update_attributes(:finance_transaction_id => paid_fee.id)
                          end
                        end
                      end
                    end
                  end
                end
              end
            end

        end
        
        if verified_no.to_i == num_orders.to_i
          flash[:notice] = "All Orders has been changed successfully"
        else
          if verified_no.to_i == 0
            flash[:notice] = "Orders has not verify yet"
          else
            flash[:notice] = verified_no.to_s + " of " + num_orders.to_s + " Order has been verified, Order IDs are: " + order_ids.reject{|x| order_ids_new.include?(x)}.join(", ")
          end
        end
      else
        flash[:notice] = "Order ID can't be blank"
      end
    else
      @order_id = ""
      unless params[:order_tid].nil?
        @order_id = params[:order_tid]
      end
    end
  end
  
  def settings
#    @conn = ActiveRecord::Base.connection
#    sql = "SELECT finance_transactions.id as transaction_id,finance_transactions.transaction_date,finance_transactions.amount as t_amount, finance_fees.id as f_id, finance_fees.student_id as std_id,finance_fees.fee_collection_id as fee_collection_id FROM `finance_transactions` inner join finance_fees on finance_fees.id = finance_transactions.finance_id  WHERE finance_transactions.`school_id` = 352 and finance_transactions.id not in (select finance_transaction_id FROM finance_transaction_particulars WHERE finance_transaction_particulars.`school_id` = 352)"
#    @finance_data = @conn.execute(sql).all_hashes
#    @finance_data.each_with_index do |b,i|
#      trans_amount = b['t_amount']
#      finance_id = b['f_id']
#      student_id = b['std_id']
#      submission_date = b['transaction_date']
#      transaction_id = b['transaction_id']
#      fee_collection_id = b['fee_collection_id']
#      
#      fee_collection = FinanceFeeCollection.find_by_id(fee_collection_id.to_i)
#      std_data = Student.find_by_id(student_id.to_i)
#      if !fee_collection.blank? && !std_data.blank?
#        
#        now_amount = FinanceFee.check_update_student_fee(fee_collection,std_data,"fee")
#        if now_amount.to_f.to_s == trans_amount.to_f.to_s
#          arrange_pay(student_id.to_i, fee_collection_id.to_i,submission_date)
#          pay_student(transaction_id)
#        end
#      end
#    end
    #abort("here")
    #    online_payments = Payment.all
#    online_payments.each do |op|
#      op.order_id = op.gateway_response[:order_id]
#      op.save
#    end

#    online_payments = Payment.all
#    finance_amount_not_match = ""
#    online_payments.each do |op|
#      unless op.finance_transaction_id.nil?
#        f_trans = FinanceTransaction.find(:first, :conditions => "id = #{op.finance_transaction_id}")
#        unless f_trans.nil?
#          if f_trans.amount.to_f != op.gateway_response[:amount].to_f
#            finance_amount_not_match += f_trans.id.to_s + "-" + f_trans.amount.to_s + "-" + op.gateway_response[:amount].to_s + ","
#          end
#        end
#      end
#    end
#
#    cnt = 0
#    std_id = ""
#    students = Student.find(:all, :conditions => "id IN (21312,21744,21873,22064,22618,22922,25100,25392,25407,25417,25478,25494,25565,25590,25601,25660,25711,25718,25769,25852,25854,25857,25867,25897,25899,25923,25940,25941,25963,26119,26234,26401,30586,30597,30647,30768,30814,30928,30964,31176,31222,31226,31249,31254,31966,32070,30779)")
#    students.each do |s|
#      ff = FinanceFee.find(:first, :conditions => "student_id = #{s.id} and batch_id = #{s.batch_id} and is_paid=#{true}")
#      unless ff.nil?
#        fts = ff.finance_transactions
#        unless fts.nil?
#          fts.each do |ft|
#            transaction_id = ft.id
#            payment_id = ff.id
#            op = Payment.find(:first, :conditions => "payee_id = #{s.id}")
#            op.update_attributes(:finance_transaction_id => transaction_id, :payment_id => payment_id)
#  #          f_collection_id = ff.fee_collection_id
#  #          fc = FinanceFeeCollection.find(f_collection_id)
#  #          FinanceFee.update_student_fee(fc,s,ff)
#            cnt += 1
#            std_id += s.id.to_s + "-" + ft.id.to_s + "-" + op.id.to_s + ","
#          end
#        end
#        #ff.destroy
#      else
#        
#      end
#    end
#    abort(cnt.to_s + "  " + std_id)
#    cnt = 0
#    online_payments = Payment.all
#    finance_amount_not_match = ""
#    online_payments.each do |op|
#      ff = FinanceFee.find(:first, :conditions => "id = #{op.payment_id} and student_id = #{op.payee_id}")
#      if ff.nil?
#        
#        unless ff.nil?
#          fts = ff.finance_transactions
#          fts.each do |ft|
#            if ft.amount.to_f == op.gateway_response[:amount].to_f
#              op.update_attributes(:finance_transaction_id => ft.id)
#              cnt += 1
              #finance_amount_not_match += op.id.to_s + "-" + op.payee_id.to_s + "-" + op.payment_id.to_s + ","
#              finance_amount_not_match += op.payee_id.to_s  + ","
#            end
#          end
#          
#        end
#      end
#    end
#    
#    abort(cnt.to_s + "  " + finance_amount_not_match)
#    online_payments = Payment.all
#    i = 0
#    j = 0
#    order_ids = []
#    verified = 0
#    order_ids_no_verified = []
#    user_ids = [22479,23675,25360,25372,26164,26302,26467,26533,27312,28528,28966,29116,29915,30092,30373,30632,31978]
#    online_payments.each do |op|
#      unless order_ids.include?(op.order_id) 
#        order_ids[i] = op.order_id
#        i += 1
#        if op.gateway_response[:verified].to_i == 1
#          verified += 1
#        end
#      else
#        ords = Payment.find(:all, :conditions => "order_id = #{op.order_id}")
#        fnd = false
#        not_inc_k = false
#        k = 1
#        ords.each do |o|
#          unless o.gateway_response[:name].nil?
#            admission_no = o.gateway_response[:name]
#            unless user_ids.include?(o.payee_id)
#              std = Student.find(o.payee_id)
#              adm_no = std.admission_no
#            else
#              adm_no = admission_no
#            end
#            if adm_no.strip.to_s == admission_no.strip.to_s
#              if k > 1
#                o.destroy
#                fnd = true
#              else
#                k = 2
#              end
#            else
#              o.destroy
#              fnd = true
#            end
#          else
#            o.destroy
#            fnd = true
#          end
#        end
#      end
#    end
#    #abort(order_ids_no_verified.inspect)
##    order_ids = ["410202", "588254", "889707", "346240", "284674", "752775", "900481", "144658", "994418", "805254", "145218", "487866", "126529", "977381", "352622", "180363", "871216", "180783", "510797", "913520", "989037", "191434", "782724", "350415", "923373", "669304", "242781"]
#    abort(online_payments.length.to_s + "  " + order_ids.uniq.length.to_s + "  " + order_ids.length.to_s + "  " + verified.to_s)
    
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
    
    @include_combined_fees = PaymentConfiguration.find_by_config_key("include_combined_fees").try(:config_value)  
    @include_combined_fees ||= Array.new

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
      unless payment_settings.keys.include? "include_combined_fees"
        configuration = PaymentConfiguration.find_or_initialize_by_config_key("include_combined_fees")
        configuration.update_attributes(:config_value => Array.new)
      else  
        student_fee_configurations = StudentFeeConfiguration.find(:all, :conditions => "config_key = 'combined_payment_student' and config_value = '#{0}'")
        student_fee_configurations.each do |sfc|
          sfc.destroy
        end
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
    
end
