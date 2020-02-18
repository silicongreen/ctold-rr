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

        unless payment.blank?
          unless order_verify_trust_bank(payment.gateway_response[:order_id]) 
            render :update do |page|
              page << "alert('Still unverified please try again later')"
            end
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
            if order_verify_trust_bank(o)
              verified_no += 1
            else
              order_ids_new << o
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
