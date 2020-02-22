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
    
    @active_gateway = PaymentConfiguration.config_value("champs21_gateway")
    if @active_gateway == "Paypal"  
      @gateway_fields = Champs21Pay::PAYPAL_CONFIG_KEYS
    elsif @active_gateway == "Authorize.net"
      @gateway_fields = Champs21Pay::AUTHORIZENET_CONFIG_KEYS
    elsif @active_gateway == "sslcommerce"
      @gateway_fields = Champs21Pay::SSL_COMMERCE_CONFIG_KEYS
    elsif @active_gateway == "trustbank"
      @gateway_fields = Champs21Pay::TRUST_BANK_CONFIG_KEYS
    elsif @active_gateway == "bkash"
      @gateway_fields = Champs21Pay::BKASH_CONFIG_KEYS  
    elsif @active_gateway == "citybank"
      @gateway_fields = Champs21Pay::CITY_BANK_CONFIG_KEYS  
    end
    @enabled_fees = PaymentConfiguration.find_by_config_key("enabled_fees").try(:config_value)  
    @enabled_fees ||= Array.new
    
    @include_combined_fees = PaymentConfiguration.find_by_config_key("include_combined_fees").try(:config_value)  
    @include_combined_fees ||= Array.new

    if request.post?
      payment_settings = params[:payment_settings]
      champs21_gateway = payment_settings[:champs21_gateway]
      configuration = PaymentConfiguration.find_or_initialize_by_config_key("champs21_gateway")
      configuration.update_attributes(:config_value => champs21_gateway.join(","))
      champs21_gateway.each do |gateway|
        abort(gateway.to_s)
      end
      abort(payment_settings.inspect)
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
    @is_check = params[:is_check]
    if @active_gateway == "Paypal"
      @gateway_fields = Champs21Pay::PAYPAL_CONFIG_KEYS
    elsif @active_gateway == "Authorize.net"
      @gateway_fields = Champs21Pay::AUTHORIZENET_CONFIG_KEYS
    elsif @active_gateway == "sslcommerce"
      @gateway_fields = Champs21Pay::SSL_COMMERCE_CONFIG_KEYS
    elsif @active_gateway == "trustbank"
      @gateway_fields = Champs21Pay::TRUST_BANK_CONFIG_KEYS
    elsif @active_gateway == "bkash"
      @gateway_fields = Champs21Pay::BKASH_CONFIG_KEYS  
    elsif @active_gateway == "citybank"
      @gateway_fields = Champs21Pay::CITY_BANK_CONFIG_KEYS  
    end
    
    if @is_check == "true"
      render :update do |page|
        if @gateway_fields.present?
          page.replace_html 'gateway_fields_' + @active_gateway,:partial => "gateway_fields"
        end
      end
    else
      render :update do |page|
        page.replace_html 'gateway_fields_' + @active_gateway,:text => ""
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
