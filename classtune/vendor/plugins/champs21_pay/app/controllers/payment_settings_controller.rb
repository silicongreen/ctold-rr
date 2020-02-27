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
  
  def transaction_list
    unless params[:gateway].blank?
      @gateway = params[:gateway]
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
      unless params[:transactionStatus].nil? or params[:transactionStatus].empty? or params[:transactionStatus].blank?
        extra_query += ' and gateway_response like \'%:transactionStatus: "' + params[:transactionStatus].to_s + '%\''
      end
      unless params[:trxID].nil? or params[:trxID].empty? or params[:trxID].blank?
        extra_query += ' and gateway_response like \'%:trxID: "' + params[:trxID].to_s + '%\''
      end
      unless params[:paymentID].nil? or params[:paymentID].empty? or params[:paymentID].blank?
        extra_query += ' and gateway_response like \'%:paymentID: "' + params[:paymentID].to_s + '%\''
      end
      unless params[:order_id].nil? or params[:order_id].empty? or params[:order_id].blank?
        if @gateway != "bkash"
          extra_query += ' and gateway_response like \'%' + params[:order_id].to_s + '%\''
        elsif @gateway == "bkash"
          extra_query += ' and gateway_response like \'%:merchantInvoiceNumb: "' + params[:order_id].to_s + '%\''
        end
      end
      unless params[:ref_no].nil? or params[:ref_no].empty? or params[:ref_no].blank?
        extra_query += ' and gateway_response like \'%' + params[:ref_no].to_s + '%\''
      end
      unless params[:payment_type].nil? or params[:payment_type].empty? or params[:payment_type].blank?
        extra_query += ' and gateway_response like \'%:payment_type: ' + params[:payment_type].to_s + '%\''
      end
      #@online_payments = Payment.all.select{|p| p.created_at.to_date >= start_date.to_date and p.created_at.to_date <= end_date.to_date}.paginate(:page => params[:page],:per_page => 30)

      ###.paginate()

      @online_payments = Payment.paginate(:conditions=>"gateway_txt = '#{@gateway}' and CAST(transaction_datetime AS DATE) >= '#{start_date.to_date}' and CAST(transaction_datetime AS DATE) <= '#{end_date.to_date}' #{extra_query}",:page => params[:page],:per_page => 30, :order => "transaction_datetime DESC", :group => "order_id")
      render :update do |page|
        page.replace_html 'payment_gatway_settings',:partial => "transaction"
      end
    else
      render :update do |page|
        page.replace_html 'payment_gatway_settings',:text => '<p class="flash-msg"> invalid Request. </p>'
      end
    end
  end
  
  def transactions
    if Champs21Plugin.can_access_plugin?("champs21_pay")
      if (PaymentConfiguration.config_value("enabled_fees").present? and PaymentConfiguration.config_value("enabled_fees").include? "Student Fee") 
          @payment_gateways = PaymentConfiguration.config_value("champs21_gateway")
          @payment_gateway = @payment_gateways.split(",") unless @payment_gateways.blank?
          @payment_gateway ||= Array.new
          unless @payment_gateway.blank?
            unless params[:gateway].blank?
              @gateway = params[:gateway]
            else
              if @payment_gateway.include?('citybank')
                @gateway = 'citybank'
              else
                @gateway = @payment_gateway[0]
              end
            end
            
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
            unless params[:transactionStatus].nil? or params[:transactionStatus].empty? or params[:transactionStatus].blank?
              extra_query += ' and gateway_response like \'%:transactionStatus: "' + params[:transactionStatus].to_s + '%\''
            end
            unless params[:trxID].nil? or params[:trxID].empty? or params[:trxID].blank?
              extra_query += ' and gateway_response like \'%:trxID: "' + params[:trxID].to_s + '%\''
            end
            unless params[:paymentID].nil? or params[:paymentID].empty? or params[:paymentID].blank?
              extra_query += ' and gateway_response like \'%:paymentID: "' + params[:paymentID].to_s + '%\''
            end
            unless params[:order_id].nil? or params[:order_id].empty? or params[:order_id].blank?
              if @gateway != "bkash"
                extra_query += ' and gateway_response like \'%' + params[:order_id].to_s + '%\''
              elsif @gateway == "bkash"
                extra_query += ' and gateway_response like \'%:merchantInvoiceNumb: "' + params[:order_id].to_s + '%\''
              end
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
                #if MultiSchool.current_school.id == 352
                  @online_payments = Payment.find(:all, :conditions=>"gateway_txt = '#{@gateway}' and CAST(transaction_datetime AS DATE) >= '#{start_date.to_date}' and CAST(transaction_datetime AS DATE) <= '#{end_date.to_date}' #{extra_query}", :order => "transaction_datetime DESC", :group => "order_id")
                #else
                #  @online_payments = Payment.all.select{|p| p.created_at.to_date >= start_date.to_date and p.created_at.to_date <= end_date.to_date}
                #end
                require 'spreadsheet'
                Spreadsheet.client_encoding = 'UTF-8'

                date = Spreadsheet::Format.new :number_format => 'MM/DD/YYYY'
                if @gateway != "bkash"
                  row_1 = ["Ref ID","Order ID","Name","Merchant ID","Amount","Fees","Service Charge","Status","Verified","Trn Date"]
                elsif @gateway == "bkash"
                  row_1 = ["trxID","Payment ID","Order ID","Student ID","Full Name","Amount","Fees","Status","Trn Date"]
                end
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
                  if @gateway != "bkash"
                    row_new = [payment.gateway_response[:ref_id], payment.gateway_response[:order_id], payment.gateway_response[:name], payment.gateway_response[:merchant_id], Champs21Precision.set_and_modify_precision(tot_amt), Champs21Precision.set_and_modify_precision(amt), Champs21Precision.set_and_modify_precision(service_change), payment.gateway_response[:status], verified, I18n.l((payment.transaction_datetime.to_time).to_datetime,:format=>"%d %b %Y")]
                  elsif @gateway == "bkash"
                    row_new = [payment.gateway_response[:trxID], payment.gateway_response[:paymentID], payment.gateway_response[:merchantInvoiceNumber], payment.payee.admission_no, payment.payee.full_name, Champs21Precision.set_and_modify_precision(tot_amt), Champs21Precision.set_and_modify_precision(amt), payment.gateway_response[:transactionStatus], I18n.l((payment.transaction_datetime.to_time).to_datetime,:format=>"%d %b %Y")]
                  end
                  new_book.worksheet(0).insert_row(ind, row_new)
                  ind += 1
                end
                spreadsheet = StringIO.new 
                new_book.write spreadsheet 

                send_data spreadsheet.string, :filename => "online_transactions-#{@gateway}.xls", :type =>  "application/vnd.ms-excel"
              else
                #if MultiSchool.current_school.id == 352
                  @online_payments = Payment.paginate(:conditions=>"gateway_txt = '#{@gateway}' and CAST(transaction_datetime AS DATE) >= '#{start_date.to_date}' and CAST(transaction_datetime AS DATE) <= '#{end_date.to_date}' #{extra_query}",:page => params[:page],:per_page => 30, :order => "transaction_datetime DESC", :group => "order_id")
                #else
                #  @online_payments = Payment.all.select{|p| p.created_at.to_date >= start_date.to_date and p.created_at.to_date <= end_date.to_date}.paginate(:page => params[:page],:per_page => 30)
                #end
                #abort(@online_payments.inspect)
                respond_to do |format|
                  format.html #transctions.html.erb
                end
              end
            else
              #if MultiSchool.current_school.id == 352
              @online_payments = Payment.paginate(:conditions=>"gateway_txt = '#{@gateway}' and CAST(transaction_datetime AS DATE) >= '#{start_date.to_date}' and CAST(transaction_datetime AS DATE) <= '#{end_date.to_date}' #{extra_query}",:page => params[:page],:per_page => 30, :order => "transaction_datetime DESC", :group => "order_id")
              #else
              #  @online_payments = Payment.all.select{|p| p.created_at.to_date >= start_date.to_date and p.created_at.to_date <= end_date.to_date}.paginate(:page => params[:page],:per_page => 30)
              #end
              respond_to do |format|
                format.html #transctions.html.erb
              end
            end
          else
            flash[:notice] = "No Payment gateway is active"
            redirect_to :controller => "user", :action => "dashboard"
          end
      else
        flash[:notice] = "Online Payment is not active"
        redirect_to :controller => "user", :action => "dashboard"
      end
    else
      flash[:notice] = "Online Payment is not active"
      redirect_to :controller => "user", :action => "dashboard"
    end
  end
  
  def order_verifications_partials
    unless params[:gateway].blank?
      @gateway = params[:gateway]
      @order_id = ""
      unless params[:order_tid].nil?
        @order_id = params[:order_tid]
      end
      render :update do |page|
        page.replace_html 'order_panel',:partial => "order_verifications"
        page << "alert(j('#order_panel').html())"
      end
    else
      render :update do |page|
        page.replace_html 'order_panel',:text => '<p class="flash-msg"> invalid Request. </p>'
      end
    end
  end
  
  def order_verifications  
    if Champs21Plugin.can_access_plugin?("champs21_pay")
      if (PaymentConfiguration.config_value("enabled_fees").present? and PaymentConfiguration.config_value("enabled_fees").include? "Student Fee") 
        @payment_gateways = PaymentConfiguration.config_value("champs21_gateway")
        @payment_gateway = @payment_gateways.split(",") unless @payment_gateways.blank?
        @payment_gateway ||= Array.new
        unless @payment_gateway.blank?
          unless params[:gateway].blank?
            @gateway = params[:gateway]
          else
            if @payment_gateway.include?('citybank')
              @gateway = 'citybank'
            else
              @gateway = @payment_gateway[0]
            end
          end
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
        else
          flash[:notice] = "No Payment gateway is active"
          redirect_to :controller => "user", :action => "dashboard"
        end
      else
        flash[:notice] = "Online Payment is not active"
        redirect_to :controller => "user", :action => "dashboard"
      end
    else
      flash[:notice] = "Online Payment is not active"
      redirect_to :controller => "user", :action => "dashboard"
    end
  end
  
  def settings  
    @active_gateways = PaymentConfiguration.config_value("champs21_gateway")
    @active_gateway = @active_gateways.split(",") unless @active_gateways.blank?
    @active_gateway ||= Array.new
    @enabled_fees = PaymentConfiguration.find_by_config_key("enabled_fees").try(:config_value)  
    @enabled_fees ||= Array.new
    
    @include_combined_fees = PaymentConfiguration.find_by_config_key("include_combined_fees").try(:config_value)  
    @include_combined_fees ||= Array.new

    if request.post?
      configurations = PaymentConfiguration.all
      unless configurations.blank?
        configurations.each do |con|
          con.destroy
        end
      end
      #abort(configurations.inspect)
      payment_settings = params[:payment_settings]
      champs21_gateway = payment_settings[:champs21_gateway]
      unless champs21_gateway.blank?
        configuration = PaymentConfiguration.find_or_initialize_by_config_key("champs21_gateway")
        configuration.update_attributes(:config_value => champs21_gateway.join(","))
        champs21_gateway.each do |gateway|
          configuration = PaymentConfiguration.find_or_initialize_by_config_key('is_test_' + gateway.to_s)
          configuration.update_attributes(:config_value => 0)
          is_test = 'is_test_' + gateway.to_s
          value = 0
          unless payment_settings[gateway.to_sym][is_test.to_sym].blank?
            value = 1
          end
          configuration.update_attributes(:config_value => value)
          gateway_sym = payment_settings[gateway.to_sym]
          gateway_sym.each_pair do |key,value|
            if key != is_test
              configuration = PaymentConfiguration.find_or_initialize_by_config_key(gateway.to_s + "_" + key)
              configuration.update_attributes(:config_value => value)
            end
          end
        end
      end
      
      #abort(payment_settings[:enabled_fees].inspect)
      unless payment_settings.keys.include? "enabled_fees"
        configuration = PaymentConfiguration.find_or_initialize_by_config_key("enabled_fees")
        configuration.update_attributes(:config_value => Array.new)
      else
        configuration = PaymentConfiguration.find_or_initialize_by_config_key("enabled_fees")
        configuration.update_attributes(:config_value => payment_settings[:enabled_fees])
      end
      unless payment_settings.keys.include? "include_combined_fees"
        configuration = PaymentConfiguration.find_or_initialize_by_config_key("include_combined_fees")
        configuration.update_attributes(:config_value => Array.new)
      else  
        configuration = PaymentConfiguration.find_or_initialize_by_config_key("include_combined_fees")
        configuration.update_attributes(:config_value => payment_settings[:include_combined_fees])
        
        student_fee_configurations = StudentFeeConfiguration.find(:all, :conditions => "config_key = 'combined_payment_student' and config_value = '#{0}'")
        student_fee_configurations.each do |sfc|
          sfc.destroy
        end
      end
      flash[:notice] = "Payment setting has been saved successfully."
      redirect_to settings_online_payments_path
    end
  end

  def show_gateway_fields
    active_gateway = params[:gateway]
    constant_val = active_gateway + "_CONFIG_KEYS"
    if Champs21Pay.const_defined?(constant_val.upcase)
        active_gateway_fields = Champs21Pay.const_get(constant_val.upcase)
        @is_check = params[:is_check] 
        if @is_check == "true"
          render :update do |page|
            page.replace_html 'gateway_fields_' + active_gateway, :partial => "gateway_fields", :locals => {:active_gateway => active_gateway, :active_gateway_fields => active_gateway_fields}
            page << "j('#gateway_fields_clear_#{active_gateway}').show();"
            page << "j('#no_gatway').hide();"
          end
        else
          render :update do |page|
            page.replace_html 'gateway_fields_' + active_gateway, :text => ""
            page << "j('#gateway_fields_clear_#{active_gateway}').hide();"
            page << "var fnd = 0; j('.gateway_fields_settings_clear').each(function(){ if ( j(this).css('display') != 'none' ){ fnd++; }; if (fnd == 0){j('#no_gatway').show();} });"
          end
        end
    else  
      render :update do |page|
        page.replace_html 'gateway_fields_' + active_gateway,:text => ""
        page << "j('#gateway_fields_clear_#{active_gateway}').hide();"
        page << "var fnd = 0; j('.gateway_fields_settings_clear').each(function(){ if ( j(this).css('display') != 'none' ){ fnd++; }; if (fnd == 0){j('#no_gatway').show();} });"
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
