class TransportFeeController < ApplicationController
  require 'authorize_net'
  helper :authorize_net
  before_filter :login_required
  before_filter :check_permission,:only=>[:index]
  filter_access_to :all
  before_filter :set_precision
  
  def index
    
  end

  def transport_fee_collection_new
    @fee_categories = FinanceFeeCategory.find(:all , :conditions => ["is_master = '#{1}' and is_deleted = '#{false}'"])
    @transport_fee_collection = TransportFeeCollection.new
    @batches = Batch.active
    @batches.reject! {|x|x.transports.blank?}
  end

  def transport_fee_collection_create
    @transport_fee_collection = TransportFeeCollection.new
    @batches = Batch.active
    @batches.reject! {|x|x.transports.blank?}
    if request.post?
      unless params[:transport_fee_collection].nil?
        @batchs = params[:transport_fee_collection][:batch_ids]
        @include_employee = params[:transport_fee_collection][:employee]
        @params = params[:transport_fee_collection]
        @params.delete("batch_ids")
        @params.delete("employee")
        @transport_fee_collection = TransportFeeCollection.new(@params)
        unless @transport_fee_collection.valid?
          @error = true
        end
        unless @batchs.blank? and @include_employee.blank?
          unless @batchs.blank?
            @batchs.each do |b|
              batch = Batch.find(b)
              @params["batch_id"] = b
              @transport_fee_collection = TransportFeeCollection.new(@params)
              if @transport_fee_collection.save
                @event= Event.create(:title=> "#{t('transport_fee_text')}", :description=> "#{t('fee_name')}: #{params[:transport_fee_collection][:name]}", :start_date=> params[:transport_fee_collection][:due_date], :end_date=> params[:transport_fee_collection][:due_date], :is_due => true, :origin=>@transport_fee_collection)
                recipients = []
                subject = "#{t('fees_submission_date')}"
                body = "<p><b>#{t('fee_submission_date_for')} <i>"+ "#{@transport_fee_collection.name}" +"</i> #{t('has_been_published')} </b><br /><br/>
                                #{t('start_date')} : "+@transport_fee_collection.start_date.to_s+" <br />"+
                  " #{t('end_date')} :"+@transport_fee_collection.end_date.to_s+" <br /> "+
                  " #{t('due_date')} :"+@transport_fee_collection.due_date.to_s+" <br /><br /><br /> "+
                  "#{t('regards')}, <br/>" + current_user.full_name.capitalize
                batch.active_transports.each do |t|
                  student = t.receiver
                  unless student.nil?
                    recipients << student.user.id
                    TransportFee.create(:receiver =>student, :bus_fare => t.bus_fare, :transport_fee_collection_id=>@transport_fee_collection.id)
                    UserEvent.create(:event_id=> @event.id, :user_id => student.user.id)
                  end
                end
                Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
                    :recipient_ids => recipients,
                    :subject=>subject,
                    :body=>body ))
              else
                @error = true
              end
            end
          end
          unless @include_employee.blank?
            @params["batch_id"]=nil
            @transport_fee_collection = TransportFeeCollection.new(@params)
            if @transport_fee_collection.save
              recipients = []
              @event=Event.create(:title=> "#{t('transport_fee_text')}", :description=> "#{t('fee_name')}: #{params[:transport_fee_collection][:name]}", :start_date=> params[:transport_fee_collection][:due_date], :end_date=> params[:transport_fee_collection][:due_date], :is_due => true, :origin=>@transport_fee_collection)
              subject = "#{t('fees_submission_date')}"
              body = "<p><b>#{t('fee_submission_date_for')} <i>"+ "#{@transport_fee_collection.name}" +"</i> #{t('has_been_published')} </b><br /><br/>
                                #{t('start_date')} : "+@transport_fee_collection.start_date.to_s+" <br />"+
                " #{t('end_date')} :"+@transport_fee_collection.end_date.to_s+" <br /> "+
                " #{t('due_date')} :"+@transport_fee_collection.due_date.to_s+" <br /><br /><br /> "+
                "#{t('regards')}, <br/>" + current_user.full_name.capitalize
              employee_transport = Transport.find(:all,:include => :vehicle, :conditions => ["receiver_type = 'Employee' AND vehicles.status = ?", "Active"])
              employee_transport.each do |t|
                emp = t.receiver
                unless emp.nil?
                  TransportFee.create(:receiver =>emp,  :bus_fare => t.bus_fare, :transport_fee_collection_id=>@transport_fee_collection.id)
                  UserEvent.create(:event_id=> @event.id, :user_id => emp.user.id)
                  recipients << emp.user.id
                end
              end
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
          @transport_fee_collection.errors.add_to_base("#{t('please_select_a_batch_or_emp')}")
        end
      end
      if @error.nil?
        flash[:notice]="#{t('collection_date_has_been_created')}"
        redirect_to :action => 'transport_fee_collection_new'
      else
        render :action => 'transport_fee_collection_new'
      end
    else
      redirect_to :action => 'transport_fee_collection_new'
    end
  end

  def transport_fee_collection_view
    #@transport_fee_collection = ''
    #@batches = Batch.active
    @transport_fee_collection =TransportFeeCollection.find(:all,:conditions=>{:is_deleted=>false})

  end

  #def transport_fee_collection_details
  # @transport_fee_details = TransportFee.find_by_transport_fee_collection_id(params[:id])
  #render :update do |page|
  # page.replace_html 'transport_fee_collection_details', :partial => 'transport_fee_collection_details'
  #end
  #end

  def transport_fee_collection_date_edit
    @transport_fee_collection = TransportFeeCollection.find params[:id]
  end

  def transport_fee_collection_date_update
    @transport_fee_collection = TransportFeeCollection.find params[:id]
   
    render :update do |page|
      if @transport_fee_collection.update_attributes(params[:fee_collection])
        @user_type=params[:user_type]
        @transport_fee_collection.event.update_attributes(:start_date=>@transport_fee_collection.due_date.to_datetime, :end_date=>@transport_fee_collection.due_date.to_datetime)
        if @user_type=='employee'
          @transport_fee_collection = TransportFeeCollection.find(:all, :conditions=>'batch_id IS NULL')
          @user_type = 'employee'
          page.replace_html 'fee_collection_list', :partial=>'fee_collection_list'
          page.replace_html 'flash-box', :text=>"<p class='flash-msg'>#{t('flash1')}</p>" unless flash[:notice].nil?
          page.replace_html 'batch_list', :text=>''
        elsif @user_type=='student'
          @transport_fee_collection = TransportFeeCollection.find_all_by_batch_id(params[:batch_id])
          @user_type = 'student'
          @batches = Batch.active
          page.replace_html 'batch_list', :partial=>'students_batch_list'
          page.replace_html 'fee_collection_list', :partial=>'fee_collection_list'
          page.replace_html 'flash-box', :text=>"<p class='flash-msg'>#{t('flash1')}</p>" unless flash[:notice].nil?
        else
          page.replace_html 'batch_list', :text=>''
          page.replace_html 'fee_collection_list', :text=>''
        end
        page << "Modalbox.hide()"
      else
        @errors = true
        page.replace_html 'form-errors', :partial => 'transport_fee/errors', :object => @transport_fee_collection
        page.visual_effect(:highlight, 'form-errors')
      end
    end
  end

  def transport_fee_collection_edit
    @transport_fee_collection = TransportFee.find params[:id]
    @batches = Batch.active
    @selected_batches = [1]
  end

  def transport_fee_collection_update
    @transport_fee_collection = TransportFee.find params[:id]
    flash[:notice]="#{t('flash2')}" if @transport_fee_collection.update_attributes(params[:fee_collection]) if request.post?
    @transport_fee_collection_details = TransportFee.find_all_by_name(@transport_fee_collection.name)
  end

  def transport_fee_collection_delete
    @transport_fee_collection = TransportFee.find params[:id]
    @transport_fee_collection.destroy
    flash[:notice] = "#{t('flash3')}"
    redirect_to :controller => 'transport_fee', :action => 'transport_fee_collection_view'
  end
  
  def transport_fee_pay
  
    @transport_fee_collection_details = TransportFee.find params[:id]
    category_id = FinanceTransactionCategory.find_by_name("Transport").id
    transaction = FinanceTransaction.new
    transaction.title = @transport_fee_collection_details.transport_fee_collection.name
    transaction.category_id = category_id
    transaction.amount = @transport_fee_collection_details.bus_fare
    transaction.amount += params[:fine].to_f unless params[:fine].nil?
    transaction.fine_included = true unless params[:fine].nil?
    transaction.transaction_date = Date.today
    transaction.payee = @transport_fee_collection_details.receiver
    transaction.finance = @transport_fee_collection_details
    if transaction.save
      @transport_fee_collection_details.update_attribute(:transaction_id, transaction.id)
    else
      render :text=>transaction.errors.full_messages and return
    end
    @collection_id = params[:collection_id]
    @transport_fee = TransportFee.find_all_by_transport_fee_collection_id(params[:collection_id])
    #    @transport_fee = TransportFee.find_all_by_transport_fee_collection_id(@transport_fee_collection_details.transport_fee_collection_id)
    @user = TransportFee.find_by_transport_fee_collection_id_and_id(params[:collection_id],params[:id]) unless params[:id].nil?
    @user ||= @transport_fee.first
    @next_user = @user.next_user
    @prev_user = @user.previous_user
    @transport_fee_collection= TransportFeeCollection.find_by_id(@user.transport_fee_collection_id)
    @transaction = FinanceTransaction.find_by_id(@user.transaction_id)
    render :update do |page|
      page.replace_html 'transport_fee_collection_details', :partial => 'transport_fee_collection_details'
    end
  end

  def transport_fee_defaulters_view
    @transport_fee_collection = ''
    @batches = Batch.active
  end

  def transport_fee_defaulters_details
    @transport_fee_details = TransportFeeCollection.find_all_by_name(params[:name])
    @transport_defaulters = @transport_fee_details.reject{|u| !u.transaction_id.nil? }
    render :update do |page|
      page.replace_html 'transport_fee_defaulters_details', :partial => 'transport_fee_defaulters_details'
    end
  end

  def transport_defaulters_fee_pay
    @transport_fee_defaulters_details = TransportFee.find params[:id]
    category_id = FinanceTransactionCategory.find_by_name("Transport").id
    transaction = FinanceTransaction.new
    transaction.title = @transport_fee_defaulters_details.transport_fee_collection.name
    transaction.category_id = category_id
    transaction.transaction_date = Date.today
    transaction.amount = @transport_fee_defaulters_details.bus_fare
    transaction.amount += params[:fine].to_f unless params[:fine].nil?
    transaction.fine_included = true unless params[:fine].nil?
    transaction.payee = @transport_fee_defaulters_details.receiver
    transaction.finance = @transport_fee_defaulters_details
    if transaction.save
      @transport_fee_defaulters_details.update_attribute(:transaction_id, transaction.id)
    end
    @transport_defaulters = TransportFee.find_all_by_transport_fee_collection_id(@transport_fee_defaulters_details.transport_fee_collection_id)
    @transport_defaulters = @transport_defaulters.reject{|u| !u.transaction_id.nil? }
    @collection_id = params[:collection_id]
    @transport_fee_collection= TransportFeeCollection.find_by_id(params[:collection_id])
    @transport_fee = TransportFee.find_all_by_transport_fee_collection_id(params[:collection_id])
    #@transport_fee = @transport_fee.reject{|u| !u.transaction_id.nil? }
    @user = TransportFee.find_by_transport_fee_collection_id_and_id(params[:collection_id], params[:id]) unless params[:id].nil?
    @user ||= @transport_fee_collection.transport_fees.first( :conditions=>["transaction_id is null"])
    @next_user = @user.next_default_user unless @user.nil?
    @prev_user = @user.previous_default_user unless @user.nil?
    @transaction = FinanceTransaction.find_by_id(@user.transaction_id) unless @user.nil?
    @transport_fee_collection= TransportFeeCollection.find_by_id(@user.transport_fee_collection_id) unless @user.nil?
    render :update do |page|
      page.replace_html 'defaulters_transport_fee_collection_details', :partial => 'defaulters_transport_fee_collection_details'
    end
  end

  def tsearch_logic # transport search fees structure
    @option = params[:option]
    if params[:option] == "student"
      if params[:query].length>= 3
        @students_result = Student.find(:all,
          :conditions => ["first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                            OR admission_no = ? OR (concat(first_name, \" \", last_name) LIKE ? ) ",
            "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
            "#{params[:query]}", "#{params[:query]}" ],
          :order => "batch_id asc,first_name asc") unless params[:query] == ''
        @students_result.reject! {|s| s.transport.nil?}
      else
        @students_result = Student.find(:all,
          :conditions => ["admission_no = ? " , params[:query]],
          :order => "batch_id asc,first_name asc") unless params[:query] == ''
        @students_result.reject! {|s| s.transport.nil?}
      end if params[:query].present?
    else
      
      if params[:query].length>= 3
        @employee_result = Employee.find(:all,
          :conditions => ["(first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                       OR employee_number = ? OR (concat(first_name, \" \", last_name) LIKE ? ))",
            "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
            "#{params[:query]}", "#{params[:query]}" ],
          :order => "employee_department_id asc,first_name asc") unless params[:query] == ''
        @employee_result.reject! {|s| s.transport.nil?}
      else
        @employee_result = Employee.find(:all,
          :conditions => ["(employee_number = ? )", "#{params[:query]}"],
          :order => "employee_department_id asc,first_name asc") unless params[:query] == ''
        @employee_result.reject! {|s| s.transport.nil?}
      end if params[:query].present?
    end
    render :layout => false
  end
  
  def fees_student_dates
    @student = Student.find(params[:id])
    @transport_fees = @student.transport_fees.all(:conditions=>["bus_fare IS NOT NULL"])
    @dates = @transport_fees.map{|t| t.transport_fee_collection}
    @dates.compact!
  end

  def fees_employee_dates
    @employee = Employee.find(params[:id])
    @transport_fees = @employee.transport_fees
    @dates = @transport_fees.map{|t| t.transport_fee_collection}
    @dates.compact!
  end

  def fees_submission_student
    @user = params[:id]
    @student = Student.find(params[:id])
    unless params[:date].blank?
      @transport_fee = TransportFee.find_by_receiver_id_and_transport_fee_collection_id(params[:id],params[:date], :conditions=>"receiver_type = 'Student'")
      @transport_fee_collection = @transport_fee.transport_fee_collection
      @transaction = FinanceTransaction.find(@transport_fee.transaction_id) unless @transport_fee.transaction_id.nil?
    end
    render :update do |page|
      page.replace_html "fee_submission", :partial => "fees_submission_form"
    end
  end

  def fees_submission_employee
    if params[:date]==""
      render :update do |page|
        page.replace_html "fee_submission", :text => ""
      end
    else
      @user = params[:id]
      @employee = Employee.find(params[:id])
      @transport_fee = TransportFee.find_by_receiver_id_and_transport_fee_collection_id(params[:id],params[:date], :conditions=>"receiver_type = 'Employee'")
      @transport_fee_collection = @transport_fee.transport_fee_collection
      @transaction = FinanceTransaction.find(@transport_fee.transaction_id) unless @transport_fee.transaction_id.nil?
      render :update do |page|
        page.replace_html "fee_submission", :partial => "fees_submission_form"
      end
    end
  end

  def update_fee_collection_dates
    @transport_fee_collection = TransportFeeCollection.find_all_by_batch_id(params[:batch_id])
    render :update do |page|
      page.replace_html 'fees_collection_dates', :partial => 'transport_fee_collection_dates'
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
  
  def transport_fee_collection_pay
    @transport_fee = TransportFee.find(params[:fees][:transport_fee_id])
    @transport_fee_collection = @transport_fee.transport_fee_collection
    category_id = FinanceTransactionCategory.find_by_name("Transport").id
    
    @transaction = FinanceTransaction.new
    unless params[:fees][:payment_mode].blank?
      @transaction.title = @transport_fee.transport_fee_collection.name
      @transaction.category_id = category_id
      unless params[:fees][:fine].nil?
        @transaction.amount = @transport_fee.bus_fare + params[:fees][:fine].to_i
        @transaction.fine_included = true
        @transaction.fine_amount = params[:fees][:fine]
      else
        @transaction.amount = @transport_fee.bus_fare
      end
      @transaction.payee = @transport_fee.receiver
      @transaction.finance = @transport_fee
      @transaction.transaction_date = Date.today
      @transaction.payment_mode = params[:fees][:payment_mode]
      @transaction.payment_note = params[:fees][:payment_note]
      if @transaction.save
        @transport_fee.update_attributes(:transaction_id => @transaction.id)
        flash[:notice]="#{t('fee_paid')}"
        flash[:warn_notice]=nil
      else
        abort(@transaction.errors.full_messages.inspect)
      end
    else
      flash[:notice]=nil
      flash[:warn_notice]="#{t('select_one_payment_mode')}"
    end
    
    render :update do |page|
      page.replace_html 'fee_submission', :partial => 'fees_submission_form'
    end
  end

  def transport_fee_collection_details
    @collection_id = params[:collection_id]
    @transport_fee_collection= TransportFeeCollection.find_by_id(params[:collection_id])
    @transport_fee = TransportFee.find_all_by_transport_fee_collection_id(params[:collection_id])
    @user = TransportFee.find_by_transport_fee_collection_id_and_id(params[:collection_id], params[:id]) unless params[:id].nil?
    @user ||= @transport_fee_collection.transport_fees.first
    @next_user = @user.next_user unless @user.nil?
    @prev_user = @user.previous_user unless @user.nil?
    @transaction = FinanceTransaction.find_by_id(@user.transaction_id) unless @user.nil?
    @transport_fee_collection= TransportFeeCollection.find_by_id(@user.transport_fee_collection_id) unless @user.nil?
    render :update do |page|
      page.replace_html 'transport_fee_collection_details', :partial => 'transport_fee_collection_details'
    end
  end

  def update_fine_ajax
    @collection_id = params[:fine][:transport_fee_collection]
    @transport_fee = TransportFee.find_all_by_transport_fee_collection_id(params[:fine][:transport_fee_collection])
    @user = TransportFee.find_by_transport_fee_collection_id_and_id(params[:fine][:transport_fee_collection], params[:fine][:_id]) unless params[:fine][:_id].nil?
    @user ||= @transport_fee.first
    @next_user = @user.next_user
    @prev_user = @user.previous_user
    @fine = (params[:fine][:fee])
    @transport_fee_collection= TransportFeeCollection.find_by_id(@user.transport_fee_collection_id)
    @transaction = FinanceTransaction.find_by_id(@user.transaction_id)
    render :update do |page|
      page.replace_html 'transport_fee_collection_details', :partial => 'transport_fee_collection_details'
    end
  end

  def update_student_fine_ajax
    @collection_id = params[:fine][:transport_fee_collection]
    @transport_fee = TransportFee.find_by_transport_fee_collection_id_and_receiver_id_and_receiver_type(params[:fine][:transport_fee_collection], params[:fine][:_id],'Student') unless params[:fine][:_id].nil?
    @transport_fee_collection= TransportFeeCollection.find_by_id(@transport_fee.transport_fee_collection_id)
    @transaction = FinanceTransaction.find_by_id(@transport_fee.transaction_id)
    render :update do |page|
      unless params[:fine][:fee].to_f < 0
        @fine = params[:fine][:fee]
        @student = Student.find(params[:fine][:_id])
        page.replace_html 'fee_submission',:partial => 'fees_submission_form',:with => @student
        page.replace_html 'flash-msg',:text => ""
      else
        @student = Student.find(params[:fine][:_id])
        page.replace_html 'fee_submission',:partial => 'fees_submission_form'
        page.replace_html 'flash-msg',:text => "<p class='flash-msg'>Fine amount cannot be negative</p>"
      end
    end
  end

   
  def employee_transport_fee_collection
    @transport_fee_collection =TransportFeeCollection.employee
  end
  
  def employee_transport_fee_collection_details
    @collection_id = params[:collection_id]
    @transport_fee_collection= TransportFeeCollection.find_by_id(params[:collection_id])
    @transport_fee = TransportFee.find_all_by_transport_fee_collection_id(params[:collection_id])
    @user = TransportFee.find_by_transport_fee_collection_id_and_id(params[:collection_id], params[:id]) unless params[:id].nil?
    @user ||= @transport_fee_collection.transport_fees.first
    unless @user.nil?
      @next_user = @user.next_user
      @prev_user = @user.previous_user
      @transaction = FinanceTransaction.find_by_id(@user.transaction_id)
      @transport_fee_collection= TransportFeeCollection.find_by_id(@user.transport_fee_collection_id)
    end
    render :update do |page|
      page.replace_html 'transport_fee_collection_details', :partial => 'employee_transport_fee_collection_details'
    end
  end

  def update_employee_fine_ajax
    @collection_id = params[:fine][:transport_fee_collection]
    @transport_fee = TransportFee.find_all_by_transport_fee_collection_id(params[:fine][:transport_fee_collection])
    @user = TransportFee.find_by_transport_fee_collection_id_and_id(params[:fine][:transport_fee_collection], params[:fine][:_id]) unless params[:fine][:_id].nil?
    @user ||= @transport_fee.first
    @next_user = @user.next_user
    @prev_user = @user.previous_user
    @fine = (params[:fine][:fee])
    @transport_fee_collection= TransportFeeCollection.find_by_id(@user.transport_fee_collection_id)
    @transaction = FinanceTransaction.find_by_id(@user.transaction_id)
    render :update do |page|
      page.replace_html 'transport_fee_collection_details', :partial => 'employee_transport_fee_collection_details'
    end
  end

  def update_employee_fine_ajax2
    #lkjlkj
    @collection_id = params[:fine][:transport_fee_collection]
    @transport_fee = TransportFee.find_by_transport_fee_collection_id_and_receiver_id_and_receiver_type(params[:fine][:transport_fee_collection], params[:fine][:_id],'Employee') unless params[:fine][:_id].nil?
    @transport_fee_collection= TransportFeeCollection.find_by_id(@transport_fee.transport_fee_collection_id)
    @transaction = FinanceTransaction.find_by_id(@transport_fee.transaction_id)
    render :update do |page|
      unless params[:fine][:fee].to_f < 0
        @fine = params[:fine][:fee].to_f
        @employee = Employee.find(params[:fine][:_id])
        page.replace_html 'fee_submission',:partial => 'fees_submission_form'
        page.replace_html 'flash-msg',:text => ""
      else
        @employee = Employee.find(params[:fine][:_id])
        page.replace_html 'fee_submission',:partial => 'fees_submission_form'
        page.replace_html 'flash-msg',:text => "<p class='flash-msg'>Fine amount cannot be negative</p>"
      end
    end
  end

  def defaulters_update_fee_collection_dates
    @transport_fee_collection = TransportFeeCollection.find_all_by_batch_id(params[:batch_id])
    render :update do |page|
      page.replace_html 'fees_collection_dates', :partial => 'defaulters_transport_fee_collection_dates'
    end
  end
  def defaulters_transport_fee_collection_details
    @collection_id = params[:collection_id]
    @transport_fee = TransportFee.find_all_by_transport_fee_collection_id(params[:collection_id], :conditions=>'transaction_id IS NULL')
    @transport_fee.reject!{|x|x.receiver.nil?}
    #   @transaction = FinanceTransaction.find_by_id(@user.transaction_id) unless @user.nil?
    #  @transport_fee_collection= TransportFeeCollection.find_by_id(@user.transport_fee_collection_id) unless @user.nil?
    render :update do |page|
      page.replace_html 'fee_submission', :partial => 'students_list'
    end
  end

  def fees_submission_defaulter_student
    @user = params[:id]
    @student = Student.find(params[:id])
    @transport_fee = TransportFee.find_by_receiver_id_and_transport_fee_collection_id(params[:id],params[:date], :conditions=>"receiver_type = 'Student'")
    @transport_fee_collection = @transport_fee.transport_fee_collection
    @transaction = FinanceTransaction.find(@transport_fee.transaction_id) unless @transport_fee.transaction_id.nil?
    render :update do |page|
      page.replace_html "fee_submission", :partial => "fees_submission_form"
    end
  end

  def update_defaulters_fine_ajax
    @collection_id = params[:fine][:transport_fee_cofind_all_by_transport_fee_collection_idllection]
    @transport_fee = TransportFee.find_all_by_transport_fee_collection_id(params[:fine][:transport_fee_collection])
    @user = TransportFee.find_by_transport_fee_collection_id_and_id(params[:fine][:transport_fee_collection], params[:fine][:_id]) unless params[:fine][:_id].nil?
    @user ||= @transport_fee.first
    @next_user = @user.next_user unless @user.nil?
    @prev_user = @user.previous_user unless @user.nil?
    @fine = (params[:fine][:fee])
    @transport_fee_collection= TransportFeeCollection.find_by_id(@user.transport_fee_collection_id)
    @transaction = FinanceTransaction.find_by_id(@user.transaction_id)
    render :update do |page|
      page.replace_html 'defaulters_transport_fee_collection_details', :partial => 'defaulters_transport_fee_collection_details'
    end
  end

  def employee_defaulters_transport_fee_collection
    @transport_fee_collection =TransportFeeCollection.employee
  end
  def employee_defaulters_transport_fee_collection_details
    @collection_id = params[:collection_id]
    @transport_fee_collection= TransportFeeCollection.find_by_id(params[:collection_id])
    @transport_fee = TransportFee.find_all_by_transport_fee_collection_id(params[:collection_id], :conditions=>'transaction_id IS NULL')
    @transport_fee.reject!{|x|x.receiver.nil?}
    @transaction = FinanceTransaction.find_by_id(@user.transaction_id) unless @user.nil?
    @transport_fee_collection= TransportFeeCollection.find_by_id(@user.transport_fee_collection_id) unless @user.nil?
    render :update do |page|
      page.replace_html 'fee_submission', :partial => 'students_list'
    end
  end

  def update_employee_defaulters_fine_ajax
    @collection_id = params[:fine][:transport_fee_collection]
    @transport_fee = TransportFee.find_all_by_transport_fee_collection_id(params[:fine][:transport_fee_collection])
    @user = TransportFee.find_by_transport_fee_collection_id_and_id(params[:fine][:transport_fee_collection], params[:fine][:_id]) unless params[:fine][:_id].nil?
    @user ||= @transport_fee_collection.transport_fees.first( :conditions=>["transaction_id is null"])
    @next_user = @user.next_default_user unless @user.nil?
    @prev_user = @user.previous_default_user unless @user.nil?
    @transaction = FinanceTransaction.find_by_id(@user.transaction_id) unless @user.nil?
    @fine = (params[:fine][:fee])
    @transport_fee_collection= TransportFeeCollection.find_by_id(@user.transport_fee_collection_id)
    render :update do |page|
      page.replace_html 'defaulters_transport_fee_collection_details', :partial => 'employee_defaulters_transport_fee_collection_details'
    end
  end


  def transport_fee_receipt_pdf
    @transaction = FinanceTransaction.find params[:id]
    @transport_fee = @transaction.finance
    @fee_collection = @transport_fee.transport_fee_collection
    @user = @transport_fee.receiver
    @bus_fare = @transaction.fine_included ? ((@transaction.amount.to_f) - (@transaction.fine_amount.to_f) ) :@transaction.amount.to_f
    @currency = currency
    if Champs21Plugin.can_access_plugin?("champs21_pay")
      response = @transaction.try(:payment).try(:gateway_response)
      @online_transaction_id = response.nil? ? nil : response[:transaction_id]
      @online_transaction_id ||= response.nil? ? nil : response[:x_trans_id]
    end
    render :pdf => 'transport_fee_receipt' , :layout=>'pdf'
  end

  def delete_fee_collection_date
    @transaction = TransportFee.find_by_transport_fee_collection_id(params[:id])
    unless @transaction.nil?
      fee_collection=TransportFeeCollection.find params[:id]
      event=fee_collection.event
      event.destroy
      fee_collection.destroy
      flash[:notice]="#{t('flash4')}"
    else
      @error_text=true
      render :update do |page|
        flash[:error]="#{t('flash5')}"
        page.redirect_to :action => 'transport_fee_collection_view'
      end
    end
  end

  def update_user_ajax
    if params[:user_type] == 'employee'
      @transport_fee_collection = TransportFeeCollection.find(:all, :conditions=>'batch_id IS NULL')
      @user_type = 'employee'
      render :update do |page|
        page.replace_html 'fee_collection_list', :partial=>'fee_collection_list'
        page.replace_html 'batch_list', :text=>''
      end
    elsif params[:user_type] == 'student'
      @user_type = 'student'
      @batches = Batch.active
      render :update do |page|
        page.replace_html 'batch_list', :partial=>'students_batch_list'
        page.replace_html 'fee_collection_list', :text=>''
      end
    else
      render :update do |page|
        page.replace_html 'batch_list', :text=>''
        page.replace_html 'fee_collection_list', :text=>''
      end
    end
  end

  def update_batch_list_ajax
    @transport_fee_collection = TransportFeeCollection.find_all_by_batch_id(params[:batch_id])
    @user_type = 'student'
    render :update do |page|
      page.replace_html 'fee_collection_list', :partial=>'fee_collection_list'
    end
  end

  def transport_fees_report
    if date_format_check
      @start_date=@start_date.to_s
      @end_date=@end_date.to_s
      transport_id = FinanceTransactionCategory.find_by_name('Transport').id
      @fees = FinanceTransaction.find(:all,:order => 'created_at desc', :conditions => ["transaction_date >= '#{@start_date}' and transaction_date <= '#{@last_date}'and category_id ='#{transport_id}'"])
      @collection = TransportFeeCollection.find(:all,:joins=>"INNER JOIN transport_fees ON transport_fees.transport_fee_collection_id = transport_fee_collections.id INNER JOIN finance_transactions ON finance_transactions.finance_id = transport_fees.id and finance_transactions.finance_type = 'TransportFee' and finance_transactions.transaction_date >= '#{@start_date}' AND finance_transactions.transaction_date <= '#{@end_date}'and finance_transactions.category_id ='#{transport_id}'",:group=>"transport_fee_collections.id")
      @student_collection = @collection.reject{|x|x.batch_id.nil?}
      @employee_collection = @collection.reject{|x|!x.batch_id.nil?}
      @employees = Employee.find(:all)
    end
  end

  def batch_transport_fees_report

    if date_format_check
      @start_date=@start_date.to_s
      @end_date=@end_date.to_s
      @fee_collection = TransportFeeCollection.find(params[:id])
      @batch = @fee_collection.batch
      transport_id = FinanceTransactionCategory.find_by_name('Transport').id
      @transaction =[]
      @fee_collection.finance_transaction.each{|f| @transaction<<f if (f.transaction_date.to_s >= @start_date and f.transaction_date.to_s <= @end_date)}
    end
  end

  def employee_transport_fees_report
    if date_format_check
      @start_date=@start_date.to_s
      @end_date=@end_date.to_s
      @fee_collection = TransportFeeCollection.find(params[:id])
      transport_id = FinanceTransactionCategory.find_by_name('Transport').id
      @transaction = @fee_collection.finance_transaction(:conditions=>"transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'and category_id ='#{transport_id}'")
    end
  end

  def student_profile_fee_details
    if Champs21Plugin.can_access_plugin?("champs21_pay")
      if ((PaymentConfiguration.config_value("enabled_fees").present? and PaymentConfiguration.config_value("enabled_fees").include? "Transport Fee"))
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
    
    @student=Student.find(params[:id])
    @fee= TransportFee.find_by_transport_fee_collection_id_and_receiver_id(params[:id2],params[:id])
    @paid_fees = @fee.finance_transaction unless @fee.transaction_id.blank?

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
      if @fee.transaction_id.nil?
        transaction = FinanceTransaction.new
        transaction.title = @fee.transport_fee_collection.name
        transaction.category_id = FinanceTransactionCategory.find_by_name('Transport').id
        transaction.finance = @fee
        transaction.amount = @fee.bus_fare
        transaction.transaction_date = Date.today
        transaction.payment_mode = "Online payment"
        transaction.payee = @fee.receiver
        if transaction.save
          @fee.update_attributes(:transaction_id => transaction.id)
          payment.update_attributes(:finance_transaction_id => transaction.id)
          online_transaction_id = payment.gateway_response[:transaction_id]
          online_transaction_id ||= payment.gateway_response[:x_trans_id]
          @paid_fees=@fee.finance_transaction unless @fee.transaction_id.blank?
          flash[:notice]= "#{t('payment_success')} #{online_transaction_id}"
          flash[:warn_notice]=nil
        end
      else
        flash[:notice] = "#{t('already_paid')}"
      end
    end
  end
  def delete_transport_transaction
    @transport_fee=TransportFee.find(params[:transaction_id])
    @financetransaction=@transport_fee.finance_transaction
    if @financetransaction
      transaction_attributes=@financetransaction.attributes
      transaction_attributes.delete "id"
      transaction_attributes.delete "created_at"
      transaction_attributes.delete "updated_at"
      transaction_attributes.merge!(:user_id=>current_user.id,:collection_name=>@transport_fee.transport_fee_collection.name)
      cancelled_transaction=CancelledFinanceTransaction.new(transaction_attributes)
      if @financetransaction.destroy
        @transport_fee.update_attributes(:transaction_id=>nil)
        cancelled_transaction.save
      end

    end
    if @transport_fee.receiver_type=="Employee"
      redirect_to :action=>'fees_submission_employee',:id=>params[:id],:date=>params[:date]
    else
      redirect_to :action=>'fees_submission_student',:id=>params[:id],:date=>params[:date]
    end
    #    render :update do |page|
    #          page.replace_html 'payments_details',:text => ''
    #        end
  end
end