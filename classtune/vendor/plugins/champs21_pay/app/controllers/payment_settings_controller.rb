class PaymentSettingsController < ApplicationController
  require 'will_paginate/array'
  before_filter :login_required
  filter_access_to :all

  def index
    @payment_gateways = PaymentConfiguration.config_value("champs21_gateway")
    @payment_gateway = @payment_gateways.split(",") unless @payment_gateways.blank?
    @payment_gateway ||= Array.new
  end

  def search_transaction
    if Champs21Plugin.can_access_plugin?("champs21_pay")
      if (PaymentConfiguration.config_value("enabled_fees").present? and PaymentConfiguration.config_value("enabled_fees").include? "Student Fee") 
        @payment_gateways = PaymentConfiguration.config_value("champs21_gateway")
        @payment_gateway = @payment_gateways.split(",") unless @payment_gateways.blank?
        @payment_gateway ||= Array.new
        unless @payment_gateways.include?("bkash")
          flash[:notice] = "Only available if bkash payment is enabled"
          redirect_to :action => "index"
        end
      else
        flash[:notice] = "Online Payment is not active"
        redirect_to :action => "index"
      end
    else
      flash[:notice] = "Online Payment is not active"
      redirect_to :action => "index"
    end
  end
  
  def search_transaction_bkash
    unless params[:query].blank?
      trx_id = params[:query]
      tokens = get_bkash_token()
      unless tokens.blank?
        @payment_infos = search_bkash_payment(tokens[:id_token], trx_id)
        render :update do |page|
          page.replace_html 'information', :partial => "order_info"
          page << "j('#loader').hide();"
        end
      else
        render :update do |page|
          page.replace_html 'information', :text => ""
          page << "j('#loader').hide();"
        end
      end
    else
      render :update do |page|
        page.replace_html 'information', :text => ""
        page << "j('#loader').hide();"
      end
    end
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
      @payment_gateways = PaymentConfiguration.config_value("champs21_gateway")
      @payment_gateway = @payment_gateways.split(",") unless @payment_gateways.blank?
      @payment_gateway ||= Array.new
      
      
      @gateway = params[:gateway]
      start_date = params[:start_date]
      start_date ||= Date.today
      end_date = params[:end_date]
      end_date ||= Date.today

      extra_query = ""
      if @gateway == "citybank"
        extra_query += ' and gateway_response like \'%OrderDescription:%\''
      end
      unless params[:payment_status].nil? or params[:payment_status].empty? or params[:payment_status].blank?
        payment_status = 0
        if params[:payment_status].to_i == 1
          payment_status = params[:payment_status].to_i
        end
        extra_query += ' and gateway_response like \'%:status: "' + payment_status.to_s + '%\''
      end
      #abort(params[:transactionStatus].inspect)
      if @gateway == "bkash"
        unless params[:transactionStatus].nil? or params[:transactionStatus].empty? or params[:transactionStatus].blank?
          extra_query += ' and gateway_response like \'%:transactionStatus: ' + params[:transactionStatus].to_s + '%\''
        end
      else  
        unless params[:transactionStatus].nil? or params[:transactionStatus].empty? or params[:transactionStatus].blank?
          extra_query += ' and gateway_response like \'%:transactionStatus: "' + params[:transactionStatus].to_s + '%\''
        end
      end
      unless params[:citybank_order_id].nil? or params[:citybank_order_id].empty? or params[:citybank_order_id].blank?
        extra_query += ' and gateway_response like \'%OrderID: "' + params[:citybank_order_id].to_s + '%\''
      end
      unless params[:trxID].nil? or params[:trxID].empty? or params[:trxID].blank?
        extra_query += ' and gateway_response like \'%:trxID: ' + params[:trxID].to_s + '%\''
      end
      unless params[:paymentID].nil? or params[:paymentID].empty? or params[:paymentID].blank?
        extra_query += ' and gateway_response like \'%:paymentID: "' + params[:paymentID].to_s + '%\''
      end
      unless params[:session_id].nil? or params[:session_id].empty? or params[:session_id].blank?
        extra_query += ' and gateway_response like \'%SessionID: ' + params[:session_id].to_s + '%\''
      end
      unless params[:order_status].nil? or params[:order_status].empty? or params[:order_status].blank?
        extra_query += ' and gateway_response like \'%OrderStatus: ' + params[:order_status].to_s + '%\''
      end
      unless params[:order_id].nil? or params[:order_id].empty? or params[:order_id].blank?
        if @gateway == "trustbank"
          extra_query += ' and gateway_response like \'%' + params[:order_id].to_s + '%\''
        elsif @gateway == "citybank"
          extra_query += ' and order_id like \'%' + params[:order_id].to_s + '%\''
        elsif @gateway == "bkash"
          extra_query += ' and gateway_response like \'%:merchantInvoiceNumber: ' + params[:order_id].to_s + '%\''
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
        page << "j('#loader').hide();"
      end
    else
      render :update do |page|
        page.replace_html 'payment_gatway_settings',:text => '<p class="flash-msg"> invalid Request. </p>'
        page << "j('#loader').hide();"
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
            
            unless current_user.username.index('tbl').blank?
              @gateway = 'trustbank'
              @payment_gateway ||= Array.new
              @payment_gateway << "trustbank" 
            end
            
            start_date = params[:start_date]
            start_date ||= Date.today
            end_date = params[:end_date]
            end_date ||= Date.today

            extra_query = ""
            if @gateway == "citybank"
              extra_query += ' and gateway_response like \'%OrderDescription:%\''
            end
            
            unless params[:payment_status].nil? or params[:payment_status].empty? or params[:payment_status].blank?
              payment_status = 0
              if params[:payment_status].to_i == 1
                payment_status = params[:payment_status].to_i
              end
              extra_query += ' and gateway_response like \'%:status: "' + payment_status.to_s + '%\''
            end
            if @gateway == "bkash"
              unless params[:transactionStatus].nil? or params[:transactionStatus].empty? or params[:transactionStatus].blank?
                extra_query += ' and gateway_response like \'%:transactionStatus: ' + params[:transactionStatus].to_s + '%\''
              end
            else  
              unless params[:transactionStatus].nil? or params[:transactionStatus].empty? or params[:transactionStatus].blank?
                extra_query += ' and gateway_response like \'%:transactionStatus: "' + params[:transactionStatus].to_s + '%\''
              end
            end
            
            unless params[:citybank_order_id].nil? or params[:citybank_order_id].empty? or params[:citybank_order_id].blank?
              extra_query += ' and gateway_response like \'%OrderID: "' + params[:citybank_order_id].to_s + '%\''
            end
            unless params[:trxID].nil? or params[:trxID].empty? or params[:trxID].blank?
              extra_query += ' and gateway_response like \'%:trxID: ' + params[:trxID].to_s + '%\''
            end
            unless params[:paymentID].nil? or params[:paymentID].empty? or params[:paymentID].blank?
              extra_query += ' and gateway_response like \'%:paymentID: "' + params[:paymentID].to_s + '%\''
            end
            unless params[:session_id].nil? or params[:session_id].empty? or params[:session_id].blank?
              extra_query += ' and gateway_response like \'%SessionID: ' + params[:session_id].to_s + '%\''
            end
            unless params[:order_status].nil? or params[:order_status].empty? or params[:order_status].blank?
              extra_query += ' and gateway_response like \'%OrderStatus: ' + params[:order_status].to_s + '%\''
            end
            unless params[:order_id].nil? or params[:order_id].empty? or params[:order_id].blank?
              if @gateway == "trustbank"
                extra_query += ' and gateway_response like \'%' + params[:order_id].to_s + '%\''
              elsif @gateway == "citybank"
                extra_query += ' and order_id like \'%' + params[:order_id].to_s + '%\''
              elsif @gateway == "bkash"
                extra_query += ' and gateway_response like \'%:merchantInvoiceNumber: ' + params[:order_id].to_s + '%\''
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
                
                amount_format = Spreadsheet::Format.new({
                    :horizontal_align => :right,
                    :number_format    => "0.00",
                    :vertical_align => :centre
                });
              
                title_format = Spreadsheet::Format.new({
                  :weight           => :bold,
                  :size             => 11,
                  :horizontal_align => :centre,
                  :vertical_align => :centre
                })
              
                center_format = Spreadsheet::Format.new({
                  :horizontal_align => :centre,
                  :vertical_align => :centre
                });
                vertical_format = Spreadsheet::Format.new({
                  :vertical_align => :centre
                });

                date = Spreadsheet::Format.new :number_format => 'MM/DD/YYYY'
                if @gateway == "trustbank"
                  row_1 = ["Ref ID","Order ID","Name","Merchant ID","Amount","Fees","Service Charge","Status","Verified","Trn Date"]
                elsif @gateway == "citybank"
                  row_1 = ["Ref ID","Order ID","Student ID","Full Name", "Roll No", "Class & Section","Card Info","Fees","Service Charge","Total Amount","Status","Trn Date"]
                elsif @gateway == "bkash"
                  row_1 = ["trxID","Payment ID","Order ID","Student ID","Full Name", "Roll No", "Class & Section","Fees","Service Charge","Total Amount","Status","Trn Date"]
                end
                # Create a new Workbook
                new_book = Spreadsheet::Workbook.new

                # Create the worksheet
                new_book.create_worksheet :name => 'Online Transaction'
                
                # Add row_1
                new_book.worksheet(0).insert_row(0, row_1)
                
                row_1.each_with_index do |e, ind_row|
                  new_book.worksheet(0).row(0).set_format(ind_row, title_format)
                end
                new_book.worksheet(0).row(0).height = 22
                
                ind = 1
                @online_payments.each do |payment|
                  if @gateway == "trustbank"
                    amt = payment.gateway_response[:amount].to_f
                    service_change = payment.gateway_response[:service_charge].to_f
                    tot_amt = amt + service_change
                  elsif @gateway == "citybank"
                    fee_percent = 0.00
                    amount_return = payment.gateway_response[:Message][:TotalAmount].to_f / 100
                    amount = amount_return
                    paid_amount = 0
                    total_payments = Payment.find(:all, :conditions => "order_id = '#{payment.order_id}' and gateway_response like \'%OrderDescription:%\'")
                    unless total_payments.blank?
                      total_payments.each do |p|
                        unless p.finance_transaction.nil?
                          paid_amount += p.finance_transaction.amount.to_f
                        else
                          fee_id = p.payment_id
                          fee = FinanceFee.find(:first, :conditions => "id = #{fee_id}") 
                          unless fee.nil? 
                            date = FinanceFeeCollection.find(:first, :conditions => "id = #{fee.fee_collection_id}")
                            unless date.blank?
                              paid_amount += FinanceFee.get_student_balance(date, payment.payee, fee)
                            end
                          end
                        end
                      end
                    end
                    fee_percent = paid_amount.to_f * (1.5 / 100)
                    
                    no_charge_apply_citybank = [312] 
                    no_charge_apply_citybank = PaymentNewConfiguration.config_value("no_charge_apply_citybank") 

                    no_charge_apply_citybank = no_charge_apply_citybank.split(",").map(&:to_i) unless no_charge_apply_citybank.blank?
                    no_charge_apply_citybank ||= Array.new
                  
                    unless no_charge_apply_citybank.include?(MultiSchool.current_school.id)
                      unless paid_amount == amount_return
                        amount = amount.to_f - fee_percent.to_f
                      end
                    end
                    unless no_charge_apply_citybank.include?(MultiSchool.current_school.id)
                      total_amount = amount + fee_percent
                    else
                      total_amount = amount
                    end
                    amt = amount
                    #amt = payment.gateway_response[:Message][:TotalAmount].to_f
                    #service_change = payment.gateway_response[:service_charge].to_f
                    tot_amt = amount 
                  elsif @gateway == "bkash"
                    fee_percent = 0.00
                    amount_return = payment.gateway_response[:amount].to_f 
                    amount = amount_return
                    paid_amount = 0
                    total_payments = Payment.find(:all, :conditions => "order_id = '#{payment.order_id}' and gateway_response like \'%:transactionStatus:%\'")
                    unless total_payments.blank?
                      total_payments.each do |p|
                        unless p.finance_transaction.nil?
                          paid_amount += p.finance_transaction.amount.to_f
                        else
                          fee_id = p.payment_id
                          fee = FinanceFee.find(:first, :conditions => "id = #{fee_id}") 
                          unless fee.nil? 
                            date = FinanceFeeCollection.find(:first, :conditions => "id = #{fee.fee_collection_id}")
                            unless date.blank?
                              paid_amount += FinanceFee.get_student_balance(date, payment.payee, fee)
                            end
                            #paid_amount += FinanceFee.get_student_balance(date, payment.payee, fee)
                          end
                        end
                      end
                    else

                    end
                    #fee_percent = paid_amount.to_f * (1.5 / 100)
                    
                    no_charge_apply_bkash = [312] 
                    no_charge_apply_bkash = PaymentNewConfiguration.config_value("no_charge_apply_bkash") 

                    no_charge_apply_bkash = no_charge_apply_bkash.split(",").map(&:to_i) unless no_charge_apply_bkash.blank?
                    no_charge_apply_bkash ||= Array.new
                  
                    unless no_charge_apply_bkash.include?(MultiSchool.current_school.id)
                      unless paid_amount == amount_return
                        amount = paid_amount.to_f
                        #amount = '%.2f' % (paid_amount.to_f  / (1 - (1.5/100)))
                      end
                    end
                    total_amount = amount_return.to_f #+ fee_percent
                    fee_percent = amount_return.to_f - amount.to_f
                    amount ||= payment.gateway_response[:x_amount]  
                    
                  
                    #total_amount = amount + fee_percent
                    amt = amount
                    #amt = payment.gateway_response[:Message][:TotalAmount].to_f
                    #service_change = payment.gateway_response[:service_charge].to_f
                    tot_amt = amount
                  end
                  verified = "false"
                  unless payment.gateway_response[:verified].nil?
                    if payment.gateway_response[:verified].to_i == 1
                      verified = "true"
                    end
                  end
                  if @gateway == "trustbank"
                    row_new = [payment.gateway_response[:ref_id], payment.gateway_response[:order_id], payment.gateway_response[:name], payment.gateway_response[:merchant_id], Champs21Precision.set_and_modify_precision(tot_amt), Champs21Precision.set_and_modify_precision(amt), Champs21Precision.set_and_modify_precision(service_change), payment.gateway_response[:status], verified, I18n.l((payment.transaction_datetime.to_time).to_datetime,:format=>"%d %b %Y")]
                  elsif @gateway == "citybank"
                    row_new = [payment.gateway_response[:Message][:OrderID], payment.order_id, payment.payee_admission_no, payment.payee_name, payment.payee_roll_no, payment.payee_batch_full_name, payment.gateway_response[:Message][:CardHolderName].to_s + " - " + payment.gateway_response[:Message][:PAN], Champs21Precision.set_and_modify_precision(amt), Champs21Precision.set_and_modify_precision(fee_percent), Champs21Precision.set_and_modify_precision(total_amount), payment.gateway_response[:Message][:OrderStatus], I18n.l((payment.transaction_datetime.to_time).to_datetime,:format=>"%d %b %Y %H:%M:%S")]
                  elsif @gateway == "bkash"
                    row_new = [payment.gateway_response[:trxID], payment.gateway_response[:paymentID], payment.gateway_response[:merchantInvoiceNumber], payment.payee_admission_no, payment.payee_name, payment.payee_roll_no, payment.payee_batch_full_name, Champs21Precision.set_and_modify_precision(amt), Champs21Precision.set_and_modify_precision(fee_percent), Champs21Precision.set_and_modify_precision(total_amount), payment.gateway_response[:transactionStatus], I18n.l((payment.transaction_datetime.to_time).to_datetime,:format=>"%d %b %Y %H:%M:%S")]
                  end
                  new_book.worksheet(0).insert_row(ind, row_new)
                  if @gateway == "trustbank"
                    new_book.worksheet(0).row(ind).set_format(4, amount_format)
                    new_book.worksheet(0).row(ind).set_format(5, amount_format)
                    new_book.worksheet(0).row(ind).set_format(6, amount_format)
                  else  
                    new_book.worksheet(0).row(ind).set_format(7, amount_format)
                    new_book.worksheet(0).row(ind).set_format(8, amount_format)
                    new_book.worksheet(0).row(ind).set_format(9, amount_format)
                  end
                  new_book.worksheet(0).row(ind).height = 20
                  if @gateway == "trustbank"
                    new_book.worksheet(0).column(0).width = 20
                    new_book.worksheet(0).column(1).width = 20
                    new_book.worksheet(0).column(2).width = 20
                    new_book.worksheet(0).column(3).width = 20
                    new_book.worksheet(0).column(4).width = 20
                    new_book.worksheet(0).column(5).width = 20
                    new_book.worksheet(0).column(6).width = 20
                    new_book.worksheet(0).column(7).width = 20
                    new_book.worksheet(0).column(8).width = 20
                    new_book.worksheet(0).column(9).width = 20
                    
                    new_book.worksheet(0).row(ind).set_format(0, center_format)
                    new_book.worksheet(0).row(ind).set_format(1, center_format)
                    new_book.worksheet(0).row(ind).set_format(2, center_format)
                    new_book.worksheet(0).row(ind).set_format(3, center_format)
                    new_book.worksheet(0).row(ind).set_format(7, center_format)
                    new_book.worksheet(0).row(ind).set_format(8, center_format)
                    new_book.worksheet(0).row(ind).set_format(9, center_format)
                    
                  elsif @gateway == "citybank"
                    new_book.worksheet(0).column(0).width = 20
                    new_book.worksheet(0).column(1).width = 20
                    new_book.worksheet(0).column(2).width = 20
                    new_book.worksheet(0).column(3).width = 40
                    new_book.worksheet(0).column(4).width = 20
                    new_book.worksheet(0).column(5).width = 40
                    new_book.worksheet(0).column(6).width = 50
                    new_book.worksheet(0).column(7).width = 20
                    new_book.worksheet(0).column(8).width = 20
                    new_book.worksheet(0).column(9).width = 20
                    new_book.worksheet(0).column(10).width = 25
                    new_book.worksheet(0).column(11).width = 25
                    
                    new_book.worksheet(0).row(ind).set_format(0, center_format)
                    new_book.worksheet(0).row(ind).set_format(1, center_format)
                    new_book.worksheet(0).row(ind).set_format(2, center_format)
                    new_book.worksheet(0).row(ind).set_format(3, vertical_format)
                    new_book.worksheet(0).row(ind).set_format(4, center_format)
                    new_book.worksheet(0).row(ind).set_format(5, vertical_format)
                    new_book.worksheet(0).row(ind).set_format(6, vertical_format)
                    new_book.worksheet(0).row(ind).set_format(10, center_format)
                    new_book.worksheet(0).row(ind).set_format(11, center_format)
                  elsif @gateway == "bkash"
                    new_book.worksheet(0).column(0).width = 20
                    new_book.worksheet(0).column(1).width = 25
                    new_book.worksheet(0).column(2).width = 20
                    new_book.worksheet(0).column(3).width = 20
                    new_book.worksheet(0).column(4).width = 40
                    new_book.worksheet(0).column(5).width = 20
                    new_book.worksheet(0).column(6).width = 40
                    new_book.worksheet(0).column(7).width = 20
                    new_book.worksheet(0).column(8).width = 20
                    new_book.worksheet(0).column(9).width = 20
                    new_book.worksheet(0).column(10).width = 20
                    new_book.worksheet(0).column(11).width = 25
                    
                    new_book.worksheet(0).row(ind).set_format(0, center_format)
                    new_book.worksheet(0).row(ind).set_format(1, center_format)
                    new_book.worksheet(0).row(ind).set_format(2, center_format)
                    new_book.worksheet(0).row(ind).set_format(3, center_format)
                    new_book.worksheet(0).row(ind).set_format(4, vertical_format)
                    new_book.worksheet(0).row(ind).set_format(5, center_format)
                    new_book.worksheet(0).row(ind).set_format(6, vertical_format)
                    new_book.worksheet(0).row(ind).set_format(10, center_format)
                    new_book.worksheet(0).row(ind).set_format(11, center_format)
                  end
                
                  
                  #if @gateway == "trustbank" or @gateway == "citybank"
                    
                  #end
                  
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
      @fee_collections = FinanceFeeCollection.find(:all, :conditions => "is_deleted = #{false}", :group => "name", :order => "due_date desc")
      render :update do |page|
        page.replace_html 'order_panel',:partial => "order_verifications"
        page << "j('#loader').hide();"
      end
    else
      render :update do |page|
        page.replace_html 'order_panel',:text => '<p class="flash-msg"> invalid Request. </p>'
        page << "j('#loader').hide();"
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
          
          @fee_collections = FinanceFeeCollection.find(:all, :conditions => "is_deleted = #{false}", :group => "name", :order => "due_date desc")
          
          if request.post?
            if params[:order_id].blank?
              unless params[:classtune_order_id].blank?
                params[:order_id] = params[:classtune_order_id]
                @classtune_order_id = params[:classtune_order_id]
              end
              unless params[:trx_id].blank?
                params[:order_id] = params[:trx_id]
              end
            end
            unless params[:order_id].blank?
              order_id_vals =  params[:order_id]
              order_ids = order_id_vals.split(",").map{ |s| s.strip }
              #abort(order_ids.inspect)
              num_orders = order_ids.length
              verified_no = 0
              
              get_the_token = true
              
              if @gateway == "citybank"
                citybank_token = get_citybank_token()
                unless citybank_token[:responseCode].to_i == 100
                  get_the_token = false
                else
                  flash[:notice] = citybank_token[:responseMessage]
                end
              end
              
              if get_the_token
                order_ids_new = []
                order_ids.each_with_index do |o, i|
                    if @gateway == "trustbank"
                      if order_verify_trust_bank(o)
                        verified_no += 1
                      else
                        order_ids_new << o
                      end
                    elsif @gateway == "citybank"
                      unless params[:query_type].blank?
                        if params[:query_type] == "order_id"
                          payments = Payment.find(:all, :conditions => "order_id = '#{o}' and gateway_txt = 'citybank'") 

                          unless payments.blank?
                             payments.each do |payment|
                                 unless payment.gateway_response[:Message].blank?
                                  session_id = payment.gateway_response[:Message][:SessionID] unless payment.gateway_response[:Message][:SessionID].nil?
                                  order_id = payment.gateway_response[:Message][:OrderID] unless payment.gateway_response[:Message][:OrderID].nil?
                                  result = validate_citybank_transaction(citybank_token[:transactionId], order_id, session_id)

                                  if result[:orderStatus].present?
                                    if result[:orderStatus] == "APPROVED"
                                      trans_date_time = payment.gateway_response[:Message][:TranDateTime]
                                      a_trans_date_time = trans_date_time.split(' ')
                                      trans_date = a_trans_date_time[0].split('/').reverse.join('-')
                                      trans_date_time = trans_date + " " + a_trans_date_time[1]
                                      #@student = payment.payee
                                      require 'date'
                                      transaction_datetime = DateTime.parse(trans_date_time).to_datetime.strftime("%Y-%m-%d %H:%M:%S")

                                      finance_order_data = FinanceOrder.find_by_order_id_and_student_id(o.strip, payment.payee.id)
                                      unless finance_order_data.blank?
                                        amount_to_pay = finance_order_data.request_params[:total_payable]
                                        validation_response = result
                                        payment.update_attributes(:validation_response => validation_response)
                                        #abort(amount_to_pay.to_s)
                                        unless payment.payee.blank?
                                          @student = payment.payee
                                        end
                                        amount = amount_to_pay
                                        fee_percent = 0.00
                                        fee_percent = (amount_to_pay.to_f  * 100) * (1.5 / 100)
                                        
                                        no_charge_apply_citybank = [312] 
                                        no_charge_apply_citybank = PaymentNewConfiguration.config_value("no_charge_apply_citybank") 

                                        no_charge_apply_citybank = no_charge_apply_citybank.split(",").map(&:to_i) unless no_charge_apply_citybank.blank?
                                        no_charge_apply_citybank ||= Array.new
                                    
                                        unless no_charge_apply_citybank.include?(MultiSchool.current_school.id)
                                          amount = (amount_to_pay.to_f * 100) + fee_percent.to_f
                                        else
                                          amount = (amount_to_pay.to_f * 100)
                                        end
                                        unless order_verify(o, 'citybank', transaction_datetime, order_id, amount)
                                          order_ids_new << o
                                        else
                                          verified_no += 1
                                        end
                                      end
                                    else
                                      order_ids_new << o
                                    end
                                  else
                                    order_ids_new << o
                                  end
                                 else
                                   unless payment.gateway_response[:order_id].blank?
                                      unless payment.gateway_response[:session_id].blank?
                                          @session_id = payment.gateway_response[:session_id]
                                          @order_id = payment.gateway_response[:order_id]
                                          get_the_token = true

                                          payment = Payment.find(:first, :conditions => "gateway_response LIKE '%%#{@order_id}%%' and gateway_txt = 'citybank'") 

                                          unless payment.blank?
                                            if @gateway == "citybank"
                                              citybank_token = get_citybank_token()
                                              unless citybank_token[:responseCode].to_i == 100
                                                get_the_token = false
                                              else
                                                flash[:notice] = citybank_token[:responseMessage]
                                              end
                                            end
                                          else
                                            get_the_token = false
                                          end
                                          if verify_citybank_payment(citybank_token, @order_id, @session_id, payment, get_the_token)
                                            verified_no += 1
                                          else
                                            order_ids_new << o
                                          end
                                      end
                                   end
                                 end
                             end
                          else  
                            order_ids_new << o
                          end
                        elsif params[:query_type] == "citybank_order_id"
                          @session_id = params[:session_id]
                          @order_id = params[:order_id]
                          get_the_token = true
                          
                          payment = Payment.find(:first, :conditions => "gateway_response LIKE '%%#{@order_id}%%' and gateway_txt = 'citybank'") 
                          
                          unless payment.blank?
                            if @gateway == "citybank"
                              citybank_token = get_citybank_token()
                              unless citybank_token[:responseCode].to_i == 100
                                get_the_token = false
                              else
                                flash[:notice] = citybank_token[:responseMessage]
                              end
                            end
                          else
                            get_the_token = false
                          end
                          if verify_citybank_payment(citybank_token, @order_id, @session_id, payment, get_the_token)
                            verified_no += 1
                          else
                            order_ids_new << o
                          end
                        end
                       else
                        flash[:notice] = "Invalid Request"
                       end
                    elsif @gateway == "bkash"
                      paymentID = []
                      unless params[:query_type].blank?
                        if params[:query_type] == "order_id"
                          payments = Payment.find(:all, :conditions => "order_id IN (#{order_ids.map{ |l| "'" + l + "'" }.join(",")})")
                          unless payments.blank?
                            payments.each do |payment|
                              unless payment.gateway_response[:paymentID].nil?
                                paymentID << payment.gateway_response[:paymentID]
                              end
                            end
                            if paymentID.blank?
                              if order_ids.length > 1
                                flash[:notice] = "No Order found with these order IDs"
                              else
                                flash[:notice] = "No Order found with this order ID"
                              end
                            end
                          else
                            if order_ids.length > 1
                              flash[:notice] = "No Order found with these order IDs"
                            else
                              flash[:notice] = "No Order found with this order ID"
                            end
                          end
                        elsif params[:query_type] == "payment_id"
                          paymentID = order_ids
                        elsif params[:query_type] == "trx_id"
                          paymentID = []
                          tokens = get_bkash_token()
                          @admission_no = params[:admission_no]
                          @trx_id = params[:trx_id]
                          @finance_fee = params[:finance_fee]
                          @student = Student.find_by_admission_no(@admission_no)
                          if @student.nil?
                            @student = ArchivedStudent.find_by_admission_no(admission_no)
                          end
                        
                          order_id = @trx_id
                          verified_already = false
                          if @finance_fee != ""
                            unless @student.blank?
                              @fee_collections_id = FinanceFeeCollection.find(:all, :conditions => ["is_deleted = #{false} and name = ?", @finance_fee]).map(&:id)
                              finance_f = FinanceFee.find(:all, :conditions => "student_id = #{@student.id} and fee_collection_id IN (#{@fee_collections_id.join(",")})").map(&:id)
                              unless finance_f.blank?
                                payment = Payment.find(:last, :conditions => "payee_id = #{@student.id} and gateway_txt = 'bkash' and payment_id IN (#{finance_f.join(",")})") 
                                
                                unless payment.blank?
                                  transaction_info = search_bkash_payment(tokens[:id_token], order_id)  
                                  unless transaction_info[:transactionStatus].blank?
                                    #gateway_response = transaction_info
                                    unless payment.gateway_response.blank?
                                      response_ssl = transaction_info
                                      unless payment.gateway_response[:paymentID].blank?
                                        payment_id = payment.gateway_response[:paymentID]
                                        query_info = query_bkash_payment(tokens[:id_token], payment_id) 
                                        response_ssl[:merchantInvoiceNumber] = payment.order_id
                                        unless query_info[:refundAmount].blank?
                                          response_ssl[:refundAmount] = query_info[:refundAmount]
                                        end
                                        unless query_info[:paymentID].blank?
                                          response_ssl[:paymentID] = query_info[:paymentID]
                                        end
                                        unless query_info[:intent].blank?
                                          response_ssl[:intent] = query_info[:intent]
                                        end
                                      end
                                    end
                                    if response_ssl[:refundAmount].blank?
                                      response_ssl[:refundAmount] = "0"
                                    end
                                    if response_ssl[:paymentID].blank?
                                      o = [('a'..'z'), ('A'..'Z')].map(&:to_a).flatten
                                      payID = (0...20).map { o[rand(o.length)] }.join
                                      response_ssl[:paymentID] = payID
                                    end
                                    if response_ssl[:intent].blank?
                                      response_ssl[:intent] = 'sale'
                                    end
                                    if save_bkash_payment(response_ssl)
                                      verified_already = true
                                    end
                                  end
                                end
                              end
                              
                            end
                          else  
                            unless @student.blank?
                              transaction_info = search_bkash_payment(tokens[:id_token], order_id)  
                              unless transaction_info[:transactionStatus].blank?
                                if transaction_info[:transactionStatus].to_s == "Completed"
                                  payments = Payment.find(:all, :conditions => "payee_id = #{@student.id} and gateway_txt = 'bkash' and finance_transaction_id IS NULL") 
                                  unless payments.blank?
                                    payments.each do |payment|
                                      payment_id = payment.gateway_response[:paymentID]
                                      query_info = query_bkash_payment(tokens[:id_token], payment_id)  
                                      unless query_info[:trxID].blank?
                                        if query_info[:trxID].to_s == order_id.to_s
                                          paymentID << paymentID
                                          break
                                        end
                                      end
                                    end
                                  end
                                end
                              end
                            end
                          end
                          
                          
                          
                          #abort(paymentID.inspect)
                          if paymentID.blank?
                            tokens = get_bkash_token()
                            transaction_info = search_bkash_payment(tokens[:id_token], params[:order_id])  
                            #abort(transaction_info.inspect)
                            unless verified_already
                              if order_ids.length > 1
                                flash[:notice] = "No Order found with these Transaction IDs"
                              else
                                flash[:notice] = "No Order found with this Transaction ID"
                              end
                            else
                              flash[:notice] = "All Orders has been verified successfully"
                            end
                            
                          end
                        end
                        unless paymentID.blank?
                          #paymentID = paymentID.uniq
                          tokens = get_bkash_token()
                          cnt = 0
                          paymentID.each do |pid|
                            #abort(tokens[:id_token].inspect)
                            if verify_bkash_payment(tokens[:id_token], pid)
                              cnt += 1
                            end
                          end
                          if cnt == 0
                            flash[:notice] = "Orders has not verify yet"
                          else  
                            unless cnt == order_ids.length
                              flash[:notice] = "There are some problem to verify some orders, Few orders successfully verified"
                            else
                              flash[:notice] = "All Orders has been verified successfully"
                            end
                          end
                        end
                      else
                        flash[:notice] = "Invalid Request"
                      end
                    end

                end
              end

              if get_the_token
                if @gateway != "bkash" and verified_no.to_i == num_orders.to_i
                  flash[:notice] = "All Orders has been changed successfully"
                else
                  if @gateway != "bkash"
                    if verified_no.to_i == 0
                      flash[:notice] = "Orders has not verify yet"
                    else
                      flash[:notice] = verified_no.to_s + " of " + num_orders.to_s + " Order has been verified, Order IDs are: " + order_ids.reject{|x| order_ids_new.include?(x)}.join(", ")
                    end
                  end
                end
              end
            else
              query_type = "Order ID"
              if @gateway == "bkash"
                unless params[:query_type].blank?
                  if params[:query_type] == "payment_id"
                    query_type = "Payment ID"
                  elsif params[:query_type] == "trx_id"  
                    query_type = "Transaction ID"
                  end
                end
              end
              flash[:notice] = "#{query_type} can't be blank"
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
          if con.config_key.to_s != "test_user_for_payment_check"
            con.destroy
          end
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
