class EmailAlertsController < ApplicationController
  before_filter :login_required, :except=>[:email_subscription]
  before_filter :check_permission, :only => [:index]
  filter_access_to :index,:show,:create,:show_student_list,:email_alert_settings,:update_recipient_list,:email_unsubscription_list
  layout :get_layout


  def get_layout
    return 'email_subscription' if action_name == 'email_subscription'
    'application'
  end


  def index
   
    
  end
  def show
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    if params[:recipient]=="employee"
      @employee_departments=EmployeeDepartment.active(:include=>:employees).select{|b| b.employees.present?}
    else
      @batches=Batch.active(:include=>:students).select{|b| b.students.present?}
    end
  end
  def create
    unless params[:email_alerts][:body] == "" or params[:recipients] == ""
      recipients=[]
      recipients_array = params[:recipients].split(",").collect{ |s| s.to_i }
      if params[:recipient]=="employee"
        recipients_array.each do |r|
          recipients=recipients+ User.find(r).email.zip(User.find(r).first_name) if User.find(r).present?
        end
      elsif params[:recipient]=="student"
        recipients_array.each do |r|
          recipients=recipients+ User.find(r).email.zip(User.find(r).first_name) if User.find(r).email.present? and User.find(r).student_record.is_email_enabled?
        end
      else
        recipients_array.each do |r|
          recipients=recipients+User.find(r).student_record.immediate_contact.email.zip(User.find(r).student_record.immediate_contact.first_name) if User.find(r).student_record.immediate_contact.present?
        end
      end
      subject= params[:email_alerts][:subject]
      msg= params[:email_alerts][:body]
      footer="#{t('footer',:school_details=>current_user.school_details)}"
      sender=current_user.email
      hostname="#{request.protocol}#{request.host_with_port}"
      recipients.each do |rec|
        message="#{t('dear')} #{rec.last},"+"<p/>"+msg
        Delayed::Job.enqueue(Champs21EmailAlertEmailMaker.new(sender,subject,message,rec.first,hostname,footer,Champs21.rtl))
        message=""
      end
      flash[:notice]="#{t('mail_sent_successfully')}"
      redirect_to :controller=>"email_alerts",:action=>"index"
    else
      flash[:notice]="<b>ERROR:</b>#{t('reminder.flash6')}"
      redirect_to :controller=>"email_alerts",:action=>"show",:recipient=>params[:recipient]
    end
  end
  def show_students_list       
    if params[:type] == "s"    
      if params[:course_name].nil?
        batch_name = ""
        if Batch.active.find(:all, :group => "name").length > 1
          unless params[:student].nil?
            unless params[:student][:batch_name].nil?
              batch_id = params[:student][:batch_name]
              batches_data = Batch.find_by_id(batch_id)
              batch_name = batches_data.name
            end
          end
        else
          batches = Batch.active
          batch_name = batches[0].name
        end
        course_id = 0
        unless params[:course_id].nil?
          course_id = params[:course_id]
        end
        if course_id == 0
          unless params[:student].nil?
            unless params[:student][:section].nil?
              course_id = params[:student][:section]
            end
          end
        end

        if batch_name.length == 0
          @batch_data = Rails.cache.fetch("batch_data_#{course_id}"){
            batches = Batch.find_by_course_id(course_id)
            batches
          }
        else
          @batch_data = Rails.cache.fetch("batch_data_#{course_id}_#{batch_name.parameterize("_")}"){
            batches = Batch.find_by_course_id_and_name(course_id, batch_name)
            batches
          }
        end 
      
        params[:course_name] = 0
        unless @batch_data.nil?
          params[:course_name] = @batch_data.id 
        end
      else
        batch = Batch.find(params[:batch_id])
        params[:course_name] = batch.id
      end
    end
    if params[:type] == "g"    
      if params[:guardian_name].nil?
        batch_name = ""
        if Batch.active.find(:all, :group => "name").length > 1
          unless params[:student].nil?
            unless params[:student][:batch_name].nil?
              batch_id = params[:student][:batch_name]
              batches_data = Batch.find_by_id(batch_id)
              batch_name = batches_data.name
            end
          end
        else
          batches = Batch.active
          batch_name = batches[0].name
        end
        course_id = 0
        unless params[:course_id].nil?
          course_id = params[:course_id]
        end
        if course_id == 0
          unless params[:student].nil?
            unless params[:student][:section].nil?
              course_id = params[:student][:section]
            end
          end
        end

        if batch_name.length == 0
        @batch_data = Rails.cache.fetch("batch_data_#{course_id}"){
          batches = Batch.find_by_course_id(course_id)
          batches
          }
        else
          @batch_data = Rails.cache.fetch("batch_data_#{course_id}_#{batch_name.parameterize("_")}"){
            batches = Batch.find_by_course_id_and_name(course_id, batch_name)
            batches
          }
        end 
      
        params[:guardian_name] = 0
        unless @batch_data.nil?
          params[:guardian_name] = @batch_data.id 
        end
      else
        batch = Batch.find(params[:batch_id])
        params[:guardian_name] = batch.id
      end
    end
    
    if params[:course_name]
      @students= Batch.find(params[:course_name]).students.select{|s| (s.email.present? and s.is_email_enabled)}.sort_by{|st| st.first_name}
    elsif params[:department_name]
      @employees=EmployeeDepartment.find(params[:department_name]).employees.select{|e| e.email.present?}.sort_by{|em| em.first_name}
    else
      @students= Batch.find(params[:guardian_name]).students.select{|s| (s.immediate_contact.present? and (s.immediate_contact.email.present?))}.sort_by{|sg| sg.first_name}
    end
    
    render :update do |page|
      page.replace_html 'to_users2', :partial => 'student_list'
    end
  end
  def email_alert_settings
    if request.post?
    
      if params[:select_options].present? 
        params[:select_options].each do|em,val|
          if EmailAlert.find_by_model_name(em).nil?
            EmailAlert.create(:model_name=>em,:value=>true,:mail_to=>params[:select_options][em].present?? params[:select_options][em][:mail_to]:[])
          else
            EmailAlert.find_by_model_name(em).update_attributes(:value=>true,:mail_to=>params[:select_options][em].present?? params[:select_options][em][:mail_to]:[])
          end
        end
        (EmailAlert.active.collect(&:model_name)-params[:select_options].keys).each do |eml|
          EmailAlert.find_by_model_name(eml).update_attributes(:value=>false,:mail_to=>[])
        end
      else
        EmailAlert.active.collect(&:model_name).each do |eml|
          EmailAlert.find_by_model_name(eml).update_attributes(:value=>false,:mail_to=>[])
        end
      end
      flash[:notice] = t('email_settings_saved')
    end
  end
  def update_recipient_list
    if params[:recipients]
      recipients_array = params[:recipients].split(",").collect{ |s| s.to_i }
      @recipients = User.active.find_all_by_id(recipients_array).sort_by{|r| r.first_name}
      render :update do |page|
        page.replace_html 'recipient-list', :partial => 'recipient_list'
      end
    else
      redirect_to :controller=>:user,:action=>:dashboard
    end
  end
  def email_unsubscription_list
    
    @date=Date.today
    @i=params[:page].present? ? (params[:page].to_i-1)*10 :0
    if params[:date].present?
      @date=params[:date]
    end
    @unsubscribers= EmailSubscription.all.select{|e| e.updated_at.to_date==@date.to_date}.paginate(:per_page=>10,:page=>params[:page])
    if request.post?
      render(:update) do |page|
        page.replace_html "u-list", :partial=>"email_alerts/unsubscription_list"
        
      end
    end
  end
  def email_subscription
    if @user=User.active.find_by_reset_password_code(params[:id],:conditions=>"reset_password_code IS NOT NULL")
      if request.post?
        student=@user.student_record
        student.update_attributes(:is_email_enabled=>false)
        d=EmailSubscription.find_or_create_by_student_id(:student_id=>student.id,:name=>student.full_name)
        d.touch

        @user.update_attributes(:reset_password_code => nil)
        flash[:notice]= "#{t('unsubscribed')}"
      end
    else
      flash[:notice]= "#{t('invalid_unsubscription_link')}"
    end
  end
end

