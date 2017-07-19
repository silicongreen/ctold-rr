class HostelFeeController < ApplicationController
  require 'authorize_net'
  helper :authorize_net
  before_filter :login_required
  before_filter :check_permission,:only=>[:index,:hostel_fee_defaulters,:hostel_fee_pay,:hostel_fee_collection]
  before_filter :set_precision
  filter_access_to :all
  def hostel_fee_collection_new
    @hostel_fee_collection = HostelFeeCollection.new
    @batches = Batch.active.reject{|b| !b.room_allocations_present}
  end

  def hostel_fee_collection_create
    @hostel_fee_collection = HostelFeeCollection.new
    @batches = Batch.active.reject{|b| !b.room_allocations_present}
    if request.post?
      unless params[:hostel_fee_collection].nil?
        @batch = params[:hostel_fee_collection][:batch_id]
        @params = params[:hostel_fee_collection]
        @params.delete("batch_id")
        @hostel_fee_collection = HostelFeeCollection.new(@params)
        unless @hostel_fee_collection.valid?
          @error = true
        end
        saved = 0
        allocation = RoomAllocation.find(:all, :conditions=>["is_vacated is false"])
        unless @batch.nil?
          @event=Event.find_by_title('Hostel Fee', :conditions=>["description=?","'Fee name: #{params[:hostel_fee_collection][:name]}' and start_date='#{params[:hostel_fee_collection][:due_date]}' and end_date='#{params[:hostel_fee_collection][:due_date]}'"])
          @batch.each do |b|
            @params["batch_id"] = b
            @hostel_fee_collection = HostelFeeCollection.new(@params)
            if @hostel_fee_collection.save
              @event= Event.create(:title=> "#{t('hostel_fee_text')}", :description=> "#{t('fee_name')}: #{params[:hostel_fee_collection][:name]}", :start_date=> params[:hostel_fee_collection][:due_date], :end_date=> params[:hostel_fee_collection][:due_date], :is_due => true, :origin=>@hostel_fee_collection)
              @batch_event = BatchEvent.create(:event_id => @event.id, :batch_id=>b)
              recipients = []
              subject = "#{t('fees_submission_date')}"
              body = "<p><b>#{t('fee_submission_date_for')} <i>"+ "#{@hostel_fee_collection.name}" +"</i> #{t('has_been_published')} </b><br /><br/>
                                {t('start_date')} : #"+@hostel_fee_collection.start_date.to_s+" <br />"+
                " #{t('end_date')} :"+@hostel_fee_collection.end_date.to_s+" <br /> "+
                " #{t('due_date')} :"+@hostel_fee_collection.due_date.to_s+" <br /><br /><br /> "+
                "#{t('regards')}, <br/>" + current_user.full_name.capitalize
              allocation.each do |a|
                unless a.student.nil?
                  if a.student.batch_id == b.to_i
                    @hostel_fee = HostelFee.new()
                    @hostel_fee.student_id = a.student_id
                    @hostel_fee.hostel_fee_collection_id = @hostel_fee_collection.id
                    @hostel_fee.rent = a.room_detail.rent
                    @hostel_fee.save
                    recipients << a.student.user_id
                  end
                end
              end
              saved += 1

              Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
                  :recipient_ids => recipients,
                  :subject=>subject,
                  :body=>body ))
            else
              @error = true
            end
          end
        else
          @error = true
          @hostel_fee_collection.errors.add_to_base("#{t('no_batch_selected')}")
        end
      end
      if @error.nil?
        if saved == @batch.size
          flash[:notice]="#{t('collection_date_has_been_created')}"
          redirect_to :action => 'hostel_fee_collection_view'
        else
          render :action => 'hostel_fee_collection_new'
        end
      else
        render :action => 'hostel_fee_collection_new'
      end
    else
      redirect_to :action => 'hostel_fee_collection_new'
    end
  end

  def hostel_fee_collection_view
    @batches = Batch.active.all(:include=>:course)
  end

  def batchwise_collection_dates
    unless params[:batch_id]==""
      @hostel_fee_collection = HostelFeeCollection.find(:all,:conditions=>{:batch_id=>params[:batch_id],:is_deleted=>false},:include=>{:batch=>:course})
      render(:update) do|page|
        page.replace_html 'flash',:text=>""
        page.replace_html "fee-collection-edit", :partial=>"fee_collection_edit"
      end
    else
      render(:update) do|page|
        page.replace_html 'flash',:text=>""
        page.replace_html "fee-collection-edit", :text=>""
      end
    end
  end

  def hostel_fee_pay
    @hostel_fee_collection = []
  end

  def hostel_fee_collection_edit
    @hostel_fee_collection = HostelFeeCollection.find params[:id]
  end

  def update_hostel_fee_collection_date
    hostel_fee_collection = HostelFeeCollection.find params[:id]
    render :update do |page|
      if params[:hostel_fee_collection][:due_date].to_date >= params[:hostel_fee_collection][:end_date].to_date
        if hostel_fee_collection.update_attributes(params[:hostel_fee_collection])
          hostel_fee_collection.event.update_attributes(:start_date=>hostel_fee_collection.due_date.to_datetime,:end_date=>hostel_fee_collection.due_date.to_datetime)
          page.replace_html 'form-errors', :text => ''
          page << "Modalbox.hide();"
          page.replace_html 'flash',:text=>"<p class='flash-msg'>#{t('hostel_flash12')} </p>"
          @hostel_fee_collection = HostelFeeCollection.find(:all,:conditions=>{:batch_id=>hostel_fee_collection.batch_id,:is_deleted=>false},:include=>{:batch=>:course})
          page.replace_html 'fee-collection-edit', :partial => 'fee_collection_edit',:object => @hostel_fee_collection
        else
          page.replace_html 'flash',:text=>""
          page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => hostel_fee_collection
          page.visual_effect(:highlight, 'form-errors')
        end
      else
        page.replace_html 'form-errors', :text => "<div id='error-box'><ul><li>#{t('hostel_flash13')}</li></ul></div>"
        flash[:notice]=""
      end
    end
  end

  def update_fee_collection_dates
    @hostel_fee_collection = HostelFeeCollection.find_all_by_batch_id(params[:batch_id],:conditions=>{:is_deleted => false})
    render :update do |page|
      page.replace_html "hostel_fee_collection_dates", :partial=>'hostel_fee_collection_dates'
    end
  end

  def hostel_fee_collection_details
    flash[:notice]=nil
    flash[:warn_notice]=nil
    @hostel_fee = HostelFee.find_all_by_hostel_fee_collection_id(params[:collection_id],:joins=> 'INNER JOIN students ON hostel_fees.student_id = students.id').sort_by{|s| s.student.full_name.downcase unless s.student.nil?}
    @hostel_fee.reject!{|x|x.student.nil?}
    render :update do |page|
      page.replace_html "hostel_fee_collection_details", :partial=>'hostel_fee_collection_details'
    end
  end

  def hostel_fee_submission_student
    @student = params[:id]
    @transaction = HostelFee.find_by_student_id_and_hostel_fee_collection_id(params[:id],params[:date])
    @finance_transaction = @transaction.finance_transaction
    render :update do |page|
      page.replace_html "hostel_fee_collection_details", :partial => "fees_submission_form"
    end
  end

  def update_student_fine_ajax
    flash[:notice]=nil
    flash[:warn_notice]=nil
    @fine = (params[:fine][:fee])
    @student = params[:fine][:_id]
    @transaction = HostelFee.find_by_student_id_and_hostel_fee_collection_id(params[:fine][:_id],params[:fine][:hostel_fee_collection])
    @finance_transaction = @transaction.finance_transaction
    render :update do |page|
      page.replace_html "hostel_fee_collection_details", :partial => 'fees_submission_form'
    end
  end

  def pay_fees
    @pay = HostelFee.find params[:id]
    category_id = FinanceTransactionCategory.find_by_name("Hostel").id
    transaction = FinanceTransaction.new
    transaction.title = @pay.hostel_fee_collection.name
    transaction.category_id = category_id
    transaction.finance = @pay
    transaction.amount = @pay.rent
    transaction.transaction_date = Date.today
    transaction.payee = @pay.student
    if transaction.save
      @pay.update_attribute(:finance_transaction_id, transaction.id)
    end
    @hostel_fee = HostelFee.find_all_by_hostel_fee_collection_id(@pay.hostel_fee_collection_id)
    @hostel_fee.reject!{|x|x.student.nil?}
    render :update do |page|
      page.replace_html "hostel_fee_collection_details", :partial=>'hostel_fee_collection_details'
    end
  end

  def hostel_fee_defaulters
    @hostel_fee_collection = []
  end

  def update_fee_collection_defaulters_dates
    @hostel_fee_collection = HostelFeeCollection.find_all_by_batch_id(params[:batch_id],:conditions => {:is_deleted=>false})
    render :update do |page|
      page.replace_html "hostel_fee_collection_dates", :partial=>'hostel_fee_collection_defaulters_dates'
    end
  end

  def hostel_fee_collection_defaulters_details
    @hostel_fee = HostelFee.find_all_by_hostel_fee_collection_id(params[:collection_id], :conditions=>"finance_transaction_id is null",:joins=> 'INNER JOIN students ON hostel_fees.student_id = students.id').sort_by{|s| s.student.full_name.downcase unless s.student.nil?}
    render :update do |page|
      page.replace_html "hostel_fee_collection_details", :partial=>'hostel_fee_collection_details'
    end
  end

  def pay_defaulters_fees
    category_id = FinanceTransactionCategory.find_by_name("Hostel").id
    @pay = HostelFee.find params[:id]
    transaction = FinanceTransaction.new
    transaction.title = @pay.hostel_fee_collection.name
    transaction.category_id = category_id
    transaction.finance = @pay
    transaction.amount = @pay.rent
    transaction.payee = @pay.student
    transaction.transaction_date = Date.today.to_date

    if transaction.save
      @pay.update_attribute(:finance_transaction_id, transaction.id)
    end
    @hostel_fee = HostelFee.find_all_by_hostel_fee_collection_id(@pay.hostel_fee_collection_id, :conditions=>["finance_transaction_id is null"])
    @hostel_fee.reject!{|x|x.student.nil?}
    render :update do |page|
      page.replace_html "hostel_fee_collection_details", :partial=>'hostel_fee_collection_defaulters_details'
      page.replace_html "pay_msg", :text=>"<p class='flash-msg'> #{t('fees_paid')} </p>"
    end
  end
  def search_ajax
    #if params[:query].length >= 3
    #@usnconfig = Configuration.find_by_config_key('EnableUsn')

    #    if @usnconfig.config_value == '1'
    #      @students = Student.usn_no_or_first_name_or_middle_name_or_last_name_or_admission_no_begins_with params[:query].split unless params[:query].empty?
    #      @students.reject! {|s| RoomAllocation.find_all_by_student_id(s.id, :conditions=>["is_vacated is false"]).empty?}
    #    else
    ###########
    #     if params[:query].length > 0
    #      @students = Student.first_name_or_middle_name_or_last_name_or_admission_no_begins_with params[:query].split unless params[:query].empty?
    #      @students.reject! {|s| RoomAllocation.find_all_by_student_id(s.id, :conditions=>["is_vacated is false"]).empty?}
    ##    end
    #    render :partial => "search_ajax"
    #    end
    ############
    if params[:query].length>= 3
      @students = Student.find(:all,
        :conditions => ["first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                            OR admission_no = ? OR (concat(first_name, \" \", last_name) LIKE ? ) and is_vacated is false",
          "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
          "#{params[:query]}", "#{params[:query]}" ],:joins=>[:room_allocations],
        :order => "batch_id asc,first_name asc",:include=>[:batch=>:course]).uniq unless params[:query] == ''
    else
      @students = Student.find(:all,
        :conditions => ["first_name = ? OR middle_name = ? OR last_name = ?
                            OR admission_no = ? OR (concat(first_name, \" \", last_name) = ? ) and is_vacated is false",
          "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
          "#{params[:query]}", "#{params[:query]}" ],:joins=>[:room_allocations],
        :order => "batch_id asc,first_name asc",:include=>[:batch=>:course]).uniq unless params[:query] == ''
    end
    render :partial => "search_ajax"
  end

  def student_hostel_fee
    @student = Student.find_by_id(params[:id])
    @hostel_dates = @student.hostel_fees
    #@dates = @hostel_dates.map{|t| t.hostel_fee_collection}
    @dates = []
    @hostel_dates.each do |hostel_date|
      @dates << hostel_date.hostel_fee_collection unless hostel_date.hostel_fee_collection.is_deleted == true
    end
  end

  def fees_submission_student
    flash[:notice]=nil
    flash[:warn_notice]=nil
    @student = params[:id]
    @transaction = HostelFee.find_by_student_id_and_hostel_fee_collection_id(params[:id],params[:date])
    @finance_transaction = @transaction.finance_transaction
    render :update do |page|
      page.replace_html "hostel_fee_collection_details", :partial => "fees_submission_form"
    end
  end
  def select_payment_mode
    if  params[:payment_mode]=="#{t('others')}"
      render :update do |page|
        page.replace_html "payment_mode", :partial => "select_payment_mode"
      end
    else
      render :update do |page|
        page.replace_html "payment_mode", :text=>""
      end
    end
  end

  def hostel_fee_collection_pay
    @transaction = HostelFee.find(params[:fees][:hostel_fee_id])
    @student = params[:student]
    unless params[:fees][:payment_mode].blank?
      transaction = FinanceTransaction.new
      transaction.title = @transaction.hostel_fee_collection.name
      transaction.category_id = FinanceTransactionCategory.find_by_name('Hostel').id
      transaction.finance = @transaction
      transaction.amount = @transaction.rent
      transaction.amount += params[:fees][:fine].to_f unless params[:fees][:fine].nil?
      transaction.fine_amount = params[:fees][:fine].to_f unless params[:fees][:fine].nil?
      transaction.fine_included = true unless params[:fees][:fine].nil?
      transaction.transaction_date = Date.today
      transaction.payment_mode = params[:fees][:payment_mode]
      transaction.payment_note = params[:fees][:payment_note]
      transaction.payee = @transaction.student
      if transaction.save
        @transaction.update_attributes(:finance_transaction_id => transaction.id)
        flash[:notice]="#{t('fee_paid')}"
        flash[:warn_notice]=nil
      end
      @finance_transaction = @transaction.finance_transaction
      @student = params[:student]
      @fine = params[:fees][:fine]
    else
      flash[:notice]=nil
      flash[:warn_notice]="#{t('select_one_payment_mode')}"
    end
    render :update do |page|
      page.replace_html 'hostel_fee_collection_details', :partial => 'fees_submission_form'
    end
  end
    
  def student_fee_receipt_pdf
    @transaction = HostelFee.find params[:id]
    @finance_transaction = @transaction.finance_transaction
    @fine = @finance_transaction.fine_amount if @finance_transaction.fine_included
    if Champs21Plugin.can_access_plugin?("champs21_pay")
      response = @finance_transaction.try(:payment).try(:gateway_response)
      @online_transaction_id = response.nil? ? nil : response[:transaction_id]
      @online_transaction_id ||= response.nil? ? nil : response[:x_trans_id]
    end
    render :pdf=>'hostel_fee_receipt'
  end

  def delete_fee_collection_date
    transaction = HostelFee.find_by_hostel_fee_collection_id(params[:id])
    hostel_fee_collection=HostelFeeCollection.find params[:id]
    unless transaction.nil?
      if hostel_fee_collection.update_attributes(:is_deleted=>true)
        event=hostel_fee_collection.event
        event.destroy
        render :update do |page|
          page.replace_html 'flash',:text=>"<p class='flash-msg'>#{t('deleted_successfully')} </p>"
          @hostel_fee_collection = HostelFeeCollection.find(:all,:conditions=>{:batch_id=>hostel_fee_collection.batch_id,:is_deleted=>false},:include=>{:batch=>:course})
          page.replace_html 'fee-collection-edit', :partial => 'fee_collection_edit',:object => @hostel_fee_collection
        end
      end
    else
      render :update do |page|
        page.replace_html 'flash',:text=>"<div id='errorExplanation' class='errorExplanation'><ul><li>#{t('cant_delete_collection_date_with_transactions')}</li></ul></div>"
        @hostel_fee_collection = HostelFeeCollection.find(:all,:conditions=>{:batch_id=>hostel_fee_collection.batch_id,:is_deleted=>false},:include=>{:batch=>:course})
        page.replace_html 'fee-collection-edit', :partial => 'fee_collection_edit',:object => @hostel_fee_collection
      end

    end
  end

  def hostel_fees_report
    if date_format_check

      @start_date = params[:start_date]
      @end_date  = params[:end_date]
      hostel_id = FinanceTransactionCategory.find_by_name('Hostel').id
      @collection = HostelFeeCollection.find(:all,:joins=>"INNER JOIN hostel_fees ON hostel_fees.hostel_fee_collection_id = hostel_fee_collections.id INNER JOIN finance_transactions ON finance_transactions.finance_id = hostel_fees.id and finance_transactions.finance_type = 'HostelFee' and finance_transactions.transaction_date >= '#{@start_date}' AND finance_transactions.transaction_date <= '#{@end_date}'and finance_transactions.category_id ='#{hostel_id}'",:group=>"hostel_fee_collections.id")
    end
  end

  def batch_hostel_fees_report
    if date_format_check

      @start_date = params[:start_date]
      @end_date  = params[:end_date]
      @fee_collection = HostelFeeCollection.find(params[:id])
      @batch = @fee_collection.batch
      hostel_id = FinanceTransactionCategory.find_by_name('Hostel').id

      @transaction =[]
      @fee_collection.finance_transaction.each{|f| @transaction<<f if (f.transaction_date.to_s >= @start_date and f.transaction_date.to_s <= @end_date)}
    end
  end

  def student_profile_fee_details
    @student=Student.find(params[:id])
    @fee= HostelFee.find_by_hostel_fee_collection_id_and_student_id(params[:id2],params[:id])
    @paid_fees=@fee.finance_transaction unless @fee.finance_transaction_id.blank?

    if Champs21Plugin.can_access_plugin?("champs21_pay")
      if ((PaymentConfiguration.config_value("enabled_fees").present? and PaymentConfiguration.config_value("enabled_fees").include? "Hostel Fee"))
        @active_gateway = PaymentConfiguration.config_value("champs21_gateway")
        if @active_gateway == "Paypal"
          @merchant_id = PaymentConfiguration.config_value("paypal_id")
          @merchant ||= String.new
          @certificate = PaymentConfiguration.config_value("paypal_certificate")
          @certificate ||= String.new
        elsif @active_gateway == "Authorize.net"
          @merchant_id = PaymentConfiguration.config_value("authorize_net_merchant_id")
          @merchant_id ||= String.new
          @certificate = PaymentConfiguration.config_value("authorize_net_transaction_password")
          @certificate ||= String.new
        end
      end
    end
    
    if params[:create_transaction] == "1"
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
      end
      payment = Payment.new(:payee => @student,:payment => @fee,:gateway_response => gateway_response)
      if @fee.finance_transaction_id.nil?
        transaction = FinanceTransaction.new
        transaction.title = @fee.hostel_fee_collection.name
        transaction.category_id = FinanceTransactionCategory.find_by_name('Hostel').id
        transaction.finance = @fee
        transaction.amount = @fee.rent
        transaction.transaction_date = Date.today
        transaction.payment_mode = "Online payment"
        transaction.payee = @fee.student
        if transaction.save
          @fee.update_attributes(:finance_transaction_id => transaction.id)
          payment.update_attributes(:finance_transaction_id => transaction.id)
          online_transaction_id = payment.gateway_response[:transaction_id]
          online_transaction_id ||= payment.gateway_response[:x_trans_id]
          @paid_fees=@fee.finance_transaction unless @fee.finance_transaction_id.blank?
          flash[:notice]= "#{t('payment_success')} #{online_transaction_id}"
          flash[:warn_notice]=nil
        end
      else
        flash[:notice] = "#{t('already_paid')}"
      end
    end
  end

  def delete_hostel_fee_transaction
    @financetransaction=FinanceTransaction.find(params[:id])
    @student=@financetransaction.payee.id
    @hostel_fee=@financetransaction.finance
    @date=@hostel_fee.hostel_fee_collection
    if @financetransaction
      transaction_attributes=@financetransaction.attributes
      transaction_attributes.delete "id"
      transaction_attributes.delete "created_at"
      transaction_attributes.delete "updated_at"
      transaction_attributes.merge!(:user_id=>current_user.id,:collection_name=>@date.name)
      cancelled_transaction=CancelledFinanceTransaction.new(transaction_attributes)
      if @financetransaction.destroy
        @hostel_fee.update_attributes(:finance_transaction_id=>nil)
        cancelled_transaction.save
      end

    end
    redirect_to :action=>'hostel_fee_submission_student',:id=>@student,:date=>@date.id
  end

end
