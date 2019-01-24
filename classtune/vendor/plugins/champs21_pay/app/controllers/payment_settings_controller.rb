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
            @verification_url ||= "https://ibanking.tblbd.com/TestCheckout/Services/Payment_Info.asmx"
          else
            @verification_url ||= "https://ibanking.tblbd.com/TestCheckout/Services/Payment_Info.asmx"
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
        abort(@keycode.inspect)
        http.use_ssl = true
        auth_res = http.request(auth_req)
        
        xml_res = Nokogiri::XML(auth_res.body)
        status = ""
        unless xml_res.xpath("/").empty?
          status = xml_res.xpath("/").text
        end
        abort(status.inspect)
        result = Base64.decode64(status)
        #@verification_data = JSON::parse(response.body)
        abort(result.inspect)
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
      extra_query += ' and gateway_response like \'%:order_id: "' + params[:order_id].to_s + '%\''
    end
    unless params[:ref_no].nil? or params[:ref_no].empty? or params[:ref_no].blank?
      extra_query += ' and gateway_response like \'%:ref_id: ' + params[:ref_no].to_s + '%\''
    end
    unless params[:payment_type].nil? or params[:payment_type].empty? or params[:payment_type].blank?
      extra_query += ' and gateway_response like \'%:payment_type: ' + params[:payment_type].to_s + '%\''
    end
    #@online_payments = Payment.all.select{|p| p.created_at.to_date >= start_date.to_date and p.created_at.to_date <= end_date.to_date}.paginate(:page => params[:page],:per_page => 30)
    @online_payments = Payment.paginate(:conditions=>"CAST( DATE_ADD( created_at, INTERVAL 6 HOUR ) AS DATE ) >= '#{start_date.to_date}' and CAST( DATE_ADD( created_at, INTERVAL 6 HOUR ) AS DATE ) <= '#{end_date.to_date}' AND validation_response IS NULL #{extra_query}",:page => params[:page],:per_page => 30, :order => "created_at DESC")
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

end
