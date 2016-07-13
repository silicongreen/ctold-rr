class DetentionController < ApplicationController
  include ActionView::Helpers::TextHelper
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time  
  before_filter :only_privileged_school_allowed
  before_filter :only_pod_allowed , :only=>[:done,:add_warning,:add_suspension]
  
  def suspension
    @batches = Batch.active
    if @current_user.admin?
      @suspension = Suspension.paginate  :order=>"created_at desc", :page=>params[:page], :per_page => 10
    elsif @current_user.employee?
      @employee= @current_user.employee_record
      if @employee.is_pod.to_i == 1
        @suspension = Suspension.paginate  :order=>"created_at desc", :page=>params[:page], :per_page => 10
      end
    else
      if  @current_user.parent?
        target = @current_user.guardian_entry.current_ward_id      
        student = Student.find_by_id(target)
      else
        student=current_user.student_record
      end
      @suspension = Suspension.paginate  :conditions=>"student_id = #{student.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10 
    end 
  end
  
  
  def add_suspension
    @batches = Batch.active
    @employee= @current_user.employee_record
    @suspension = Suspension.new(params[:suspension])
    @suspension.employee_id = @employee.id
  
    
    if request.post? and @suspension.save
      reminder_recipient_ids = []
      batch_ids = {}
      student_ids = {}
      recipients = []
      sms_setting = SmsSetting.new()
      @student = Student.find(@suspension.student_id)
      
      
      reminder_recipient_ids << @student.user_id
      batch_ids[@student.user_id] = @student.batch_id
      student_ids[@student.user_id] = @student.id
      unless @student.immediate_contact.nil? 
        reminder_recipient_ids << @student.immediate_contact.user_id
        batch_ids[@student.immediate_contact.user_id] = @student.batch_id
        student_ids[@student.immediate_contact.user_id] = @student.id
      end
      
      if sms_setting.application_sms_active
        guardian = @student.immediate_contact unless @student.immediate_contact.nil?
        if @student.is_sms_enabled
          if sms_setting.student_sms_active
            recipients.push @student.phone2 unless @student.phone2.nil?
          end
          if sms_setting.parent_sms_active
            unless guardian.nil?
              recipients.push guardian.mobile_phone unless guardian.mobile_phone.nil?
            end
          end
        end
      end
      
      messege = @suspension.student.full_name+", "+@suspension.batch.full_name+", roll :"+ @suspension.student.class_roll_no+"  has received a letter of suspension  by "+@suspension.employee.first_name+" "+@suspension.employee.last_name+" on "+I18n.l(@suspension.created_at.to_date, :format=>'%d/%m/%Y')
  
      unless recipients.empty? or !send_sms("suspension")
        Delayed::Job.enqueue(SmsManager.new(message,recipients))
      end
      unless reminder_recipient_ids.empty?
        Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
            :recipient_ids => reminder_recipient_ids,
            :subject=>"Later of Suspension",
            :rtype=>32,
            :rid=>@detention.id,
            :student_id => student_ids,
            :batch_id => batch_ids,
            :body=>messege ))
      end
      
      flash[:notice] = "Succesfully Saved"
      redirect_to :controller => 'detention', :action => 'suspension'
    end
   
  end
  
  
  def show_suspension
    @batch_id = params[:batch_id]
    @achnowladge = params[:achnowladge_id]
    @student_id = params[:student_id]
    @suspension_id = params[:id]
    
    extra_condition = ""
    if !@batch_id.blank? and @batch_id!=""
      extra_condition = "batch_id = #{@batch_id}"
      @student_list = Student.find_all_by_batch_id(@batch_id,:order=>"number_of_detention DESC")
    end
    
    if !extra_condition.blank? and !@achnowladge.blank? and @achnowladge!=""
      extra_condition = extra_condition+" and ackhnowledged=#{@achnowladge}"
    elsif !@achnowladge.blank? and @achnowladge!=""
      extra_condition = "ackhnowledged = #{@achnowladge}"
    end
    
    if !extra_condition.blank? and !@student_id.blank? and @student_id!=""
      extra_condition = extra_condition+" and student_id=#{@student_id}"
    elsif !@student_id.blank? and @student_id!=""
      extra_condition = "student_id = #{@student_id}"
    end
    
    if !@suspension_id.blank?
      @suspension_obj = Suspension.find(@suspension_id)
      if @suspension_obj.ackhnowledged == 0
        if @current_user.admin?
          @suspension_obj.delete
        elsif @current_user.employee?
          @employee= @current_user.employee_record
          if @employee.is_pod.to_i == 1 
            @suspension_obj.delete
          end  
        end 
      end
    end
    
    if extra_condition==""
      if @current_user.employee?
        @employee= @current_user.employee_record
        if @employee.is_pod.to_i == 1
          @suspension = Suspension.paginate  :order=>"created_at desc", :page=>params[:page], :per_page => 10
        else
          @suspension = Suspension.paginate  :conditions=>"employee_id = #{@employee.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
        end  
      elsif @current_user.admin?
        @suspension = Suspension.paginate  :order=>"created_at desc", :page=>params[:page], :per_page => 10
      else
        if  @current_user.parent?
          target = @current_user.guardian_entry.current_ward_id      
          student = Student.find_by_id(target)
        else
          student=current_user.student_record
        end
        @suspension = Suspension.paginate  :conditions=>"student_id = #{student.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
      
      end
    else     
      if @current_user.employee?
        @employee= @current_user.employee_record
        if @employee.is_pod.to_i == 1
          @suspension = Suspension.paginate :conditions=>extra_condition,  :order=>"created_at desc", :page=>params[:page], :per_page => 10
        else
          @suspension = Suspension.paginate  :conditions=>extra_condition+" and employee_id = #{@employee.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
        end  
      elsif @current_user.admin?
        @suspension = Suspension.paginate :conditions=>extra_condition,  :order=>"created_at desc", :page=>params[:page], :per_page => 10
      else
        if  @current_user.parent?
          target = @current_user.guardian_entry.current_ward_id      
          student = Student.find_by_id(target)
        else
          student=current_user.student_record
        end
        @suspension = Suspension.paginate  :conditions=>extra_condition+" and student_id = #{student.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
      
      end
    end 
    render :partial => 'suspension_list'
  end
  
  def ackhnowledged_suspension
    @suspension = Suspension.find params[:id]
    @suspension.update_attributes(:ackhnowledged=>1)
    render :partial => 'suspension_status'
  end
  
  
  
  
  def warning
    @batches = Batch.active
    if @current_user.admin?
      
      @warning = Warning.paginate  :order=>"created_at desc", :page=>params[:page], :per_page => 10
    elsif @current_user.employee?
      @employee= @current_user.employee_record
      if @employee.is_pod.to_i == 1
        @warning = Warning.paginate  :order=>"created_at desc", :page=>params[:page], :per_page => 10
      end
    else
      if  @current_user.parent?
        target = @current_user.guardian_entry.current_ward_id      
        student = Student.find_by_id(target)
      else
        student=current_user.student_record
      end
      @warning = Warning.paginate  :conditions=>"student_id = #{student.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10 
    end 
  end
  
  
  def add_warning
    @batches = Batch.active
    @employee= @current_user.employee_record
    @warning = Warning.new(params[:warning])
    @warning.employee_id = @employee.id
  
    
    if request.post? and @warning.save
      reminder_recipient_ids = []
      batch_ids = {}
      student_ids = {}
      recipients = []
      sms_setting = SmsSetting.new()
      @student = Student.find(@warning.student_id)
      
      
      reminder_recipient_ids << @student.user_id
      batch_ids[@student.user_id] = @student.batch_id
      student_ids[@student.user_id] = @student.id
      unless @student.immediate_contact.nil? 
        reminder_recipient_ids << @student.immediate_contact.user_id
        batch_ids[@student.immediate_contact.user_id] = @student.batch_id
        student_ids[@student.immediate_contact.user_id] = @student.id
      end
      
      if sms_setting.application_sms_active
        guardian = @student.immediate_contact unless @student.immediate_contact.nil?
        if @student.is_sms_enabled
          if sms_setting.student_sms_active
            recipients.push @student.phone2 unless @student.phone2.nil?
          end
          if sms_setting.parent_sms_active
            unless guardian.nil?
              recipients.push guardian.mobile_phone unless guardian.mobile_phone.nil?
            end
          end
        end
      end
      
      messege = @warning.student.full_name+", "+@warning.batch.full_name+", roll :"+ @warning.student.class_roll_no+"  has received a letter of warning  by "+@warning.employee.first_name+" "+@warning.employee.last_name+" on "+I18n.l(@warning.created_at.to_date, :format=>'%d/%m/%Y')
  
      unless recipients.empty? or !send_sms("warning")
        Delayed::Job.enqueue(SmsManager.new(message,recipients))
      end
      unless reminder_recipient_ids.empty?
        Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
            :recipient_ids => reminder_recipient_ids,
            :subject=>"Later of Warning",
            :rtype=>32,
            :rid=>@detention.id,
            :student_id => student_ids,
            :batch_id => batch_ids,
            :body=>messege ))
      end
      
      flash[:notice] = "Succesfully Saved"
      redirect_to :controller => 'detention', :action => 'warning'
    end
   
  end
  
  
  def show_warning
    @batch_id = params[:batch_id]
    @achnowladge = params[:achnowladge_id]
    @student_id = params[:student_id]
    @warning_id = params[:id]
    
    extra_condition = ""
    if !@batch_id.blank? and @batch_id!=""
      extra_condition = "batch_id = #{@batch_id}"
      @student_list = Student.find_all_by_batch_id(@batch_id,:order=>"number_of_detention DESC")
    end
    
    if !extra_condition.blank? and !@achnowladge.blank? and @achnowladge!=""
      extra_condition = extra_condition+" and ackhnowledged=#{@achnowladge}"
    elsif !@achnowladge.blank? and @achnowladge!=""
      extra_condition = "ackhnowledged = #{@achnowladge}"
    end
    
    if !extra_condition.blank? and !@student_id.blank? and @student_id!=""
      extra_condition = extra_condition+" and student_id=#{@student_id}"
    elsif !@student_id.blank? and @student_id!=""
      extra_condition = "student_id = #{@student_id}"
    end
    
    if !@warning_id.blank?
      @warning_obj = Warning.find(@warning_id)
      if @warning_obj.ackhnowledged == 0
        if @current_user.admin?
          @warning_obj.delete
        elsif @current_user.employee?
          @employee= @current_user.employee_record
          if @employee.is_pod.to_i == 1 
            @warning_obj.delete
          end  
        end 
      end
    end
    
    if extra_condition==""
      if @current_user.employee?
        @employee= @current_user.employee_record
        if @employee.is_pod.to_i == 1
          @warning = Warning.paginate  :order=>"created_at desc", :page=>params[:page], :per_page => 10
        else
          @warning = Warning.paginate  :conditions=>"employee_id = #{@employee.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
        end  
      elsif @current_user.admin?
        @warning = Warning.paginate  :order=>"created_at desc", :page=>params[:page], :per_page => 10
      else
        if  @current_user.parent?
          target = @current_user.guardian_entry.current_ward_id      
          student = Student.find_by_id(target)
        else
          student=current_user.student_record
        end
        @warning = Warning.paginate  :conditions=>"student_id = #{student.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
      
      end
    else     
      if @current_user.employee?
        @employee= @current_user.employee_record
        if @employee.is_pod.to_i == 1
          @warning = Warning.paginate :conditions=>extra_condition,  :order=>"created_at desc", :page=>params[:page], :per_page => 10
        else
          @warning = Warning.paginate  :conditions=>extra_condition+" and employee_id = #{@employee.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
        end  
      elsif @current_user.admin?
        @warning = Warning.paginate :conditions=>extra_condition,  :order=>"created_at desc", :page=>params[:page], :per_page => 10
      else
        if  @current_user.parent?
          target = @current_user.guardian_entry.current_ward_id      
          student = Student.find_by_id(target)
        else
          student=current_user.student_record
        end
        @warning = Warning.paginate  :conditions=>extra_condition+" and student_id = #{student.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
      
      end
    end 
    render :partial => 'warning_list'
  end
  
  def ackhnowledged_warning
    @warning = Warning.find params[:id]
    @warning.update_attributes(:ackhnowledged=>1)
    render :partial => 'warning_status'
  end
  
  
  def index
    @batches = Batch.active
    if @current_user.employee?
      @employee= @current_user.employee_record
      if @employee.is_pod.to_i == 1
        @detention = Detention.paginate  :order=>"created_at desc", :page=>params[:page], :per_page => 10
      else
        @detention = Detention.paginate  :conditions=>"employee_id = #{@employee.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
      end  
    elsif @current_user.admin?
      @detention = Detention.paginate  :order=>"created_at desc", :page=>params[:page], :per_page => 10
    else
      if @current_user.parent?
        target = @current_user.guardian_entry.current_ward_id      
        student = Student.find_by_id(target)
      else
        student=current_user.student_record
      end
      @detention = Detention.paginate  :conditions=>"student_id = #{student.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
      
    end
    
  end
  def suspension_student_list
    @batch_id = params[:batch_id]
    @students = Student.find_all_by_batch_id(@batch_id,:order=>"number_of_detention DESC")
    render :partial => 'suspension_student_list'
  end
  def warning_student_list
    @batch_id = params[:batch_id]
    @students = Student.find_all_by_batch_id(@batch_id,:order=>"number_of_detention DESC")
    render :partial => 'warning_student_list'
  end
  def student_list
    @batch_id = params[:batch_id]
    @students = Student.find_all_by_batch_id(@batch_id,:order=>"number_of_detention DESC")
    render :partial => 'student_list'
  end
  def students
    @batches = Batch.active
    @all_students = Student.paginate  :conditions=>"number_of_detention > 0",:order=>"number_of_detention DESC", :page=>params[:page], :per_page => 10
  end
  def ajax_students
    @batch_id = params[:batch_id]
    extra_condition = ""
    if !@batch_id.blank? and @batch_id!=""
      extra_condition = "batch_id = #{@batch_id} and "
    end
    @all_students = Student.paginate  :conditions=>extra_condition+"number_of_detention > 0",:order=>"number_of_detention DESC", :page=>params[:page], :per_page => 10
    render :partial => 'student_detention'
  end
  def add
    @batches = Batch.active
    @employee= @current_user.employee_record
    @detention = Detention.new(params[:detention])
    @detention.employee_id = @employee.id
    
    if request.post? and !params[:detention].blank? and params[:detention][:reason] == "Others"
      @detention.reason = params[:reason_others]
    end
    
    if request.post? and @detention.save
      reminder_recipient_ids = []
      batch_ids = {}
      student_ids = {}
      recipients = []
      sms_setting = SmsSetting.new()
      @student = Student.find(@detention.student_id)
      
      @student.number_of_detention = @student.number_of_detention+1;
      @student.save
      
      reminder_recipient_ids << @student.user_id
      batch_ids[@student.user_id] = @student.batch_id
      student_ids[@student.user_id] = @student.id
      unless @student.immediate_contact.nil? 
        reminder_recipient_ids << @student.immediate_contact.user_id
        batch_ids[@student.immediate_contact.user_id] = @student.batch_id
        student_ids[@student.immediate_contact.user_id] = @student.id
      end
      
      if sms_setting.application_sms_active
        guardian = @student.immediate_contact unless @student.immediate_contact.nil?
        if @student.is_sms_enabled
          if sms_setting.student_sms_active
            recipients.push @student.phone2 unless @student.phone2.nil?
          end
          if sms_setting.parent_sms_active
            unless guardian.nil?
              recipients.push guardian.mobile_phone unless guardian.mobile_phone.nil?
            end
          end
        end
      end
      
      messege =@detention.student.full_name+", "+@detention.batch.full_name+", roll :"+ @detention.student.class_roll_no+"  has received a detention for"+ @detention.reason+" by "+@detention.employee.first_name+" "+@detention.employee.last_name+" on "+I18n.l(@detention.created_at.to_date, :format=>'%d/%m/%Y')
  
      unless recipients.empty? or !send_sms("detention")
        Delayed::Job.enqueue(SmsManager.new(message,recipients))
      end
      unless reminder_recipient_ids.empty?
        Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
            :recipient_ids => reminder_recipient_ids,
            :subject=>"Detention Notice",
            :rtype=>31,
            :rid=>@detention.id,
            :student_id => student_ids,
            :batch_id => batch_ids,
            :body=>messege ))
      end
      
      flash[:notice] = "Succesfully Saved"
      redirect_to :controller => 'detention', :action => 'index'
    end
   
  end
  def show_detention
    @batch_id = params[:batch_id]
    @status_id = params[:status_id]
    @achnowladge = params[:achnowladge_id]
    @student_id = params[:student_id]
    @detention_id = params[:id]
    
    extra_condition = ""
    if (!@status_id.blank? and @status_id!="")
      extra_condition = "status=#{@status_id}"
    end
    if !extra_condition.blank? and !@batch_id.blank? and @batch_id!=""
      extra_condition = extra_condition+" and batch_id=#{@batch_id}"
    elsif !@batch_id.blank? and @batch_id!=""
      extra_condition = "batch_id = #{@batch_id}"
      @student_list = Student.find_all_by_batch_id(@batch_id,:order=>"number_of_detention DESC")
    end
    
    
    
    if !extra_condition.blank? and !@achnowladge.blank? and @achnowladge!=""
      extra_condition = extra_condition+" and ackhnowledged=#{@achnowladge}"
    elsif !@achnowladge.blank? and @achnowladge!=""
      extra_condition = "ackhnowledged = #{@achnowladge}"
    end
    
    if !extra_condition.blank? and !@student_id.blank? and @student_id!=""
      extra_condition = extra_condition+" and student_id=#{@student_id}"
    elsif !@student_id.blank? and @student_id!=""
      extra_condition = "student_id = #{@student_id}"
    end
    
    if !@detention_id.blank?
      @detention_obj = Detention.find(@detention_id)
      if @detention_obj.status == 0 and @detention_obj.ackhnowledged == 0
        if @current_user.admin?
          @student = Student.find(@detention_obj.student_id)
          if @student.number_of_detention>0
            @student.number_of_detention = @student.number_of_detention-1;
            @student.save
          end
          @detention_obj.delete
        elsif @current_user.employee?
          @employee= @current_user.employee_record
          if @employee.is_pod.to_i == 1
            @student = Student.find(@detention_obj.student_id)
            if @student.number_of_detention>0
              @student.number_of_detention = @student.number_of_detention-1;
              @student.save
            end
            @detention_obj.delete
          elsif @detention_obj.employee_id == @employee.id
            @student = Student.find(@detention_obj.student_id)
            if @student.number_of_detention>0
              @student.number_of_detention = @student.number_of_detention-1;
              @student.save
            end
            @detention_obj.delete
          end  
        end 
      end
    end
    
    if extra_condition==""
      if @current_user.employee?
        @employee= @current_user.employee_record
        if @employee.is_pod.to_i == 1
          @detention = Detention.paginate  :order=>"created_at desc", :page=>params[:page], :per_page => 10
        else
          @detention = Detention.paginate  :conditions=>"employee_id = #{@employee.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
        end  
      elsif @current_user.admin?
        @detention = Detention.paginate  :order=>"created_at desc", :page=>params[:page], :per_page => 10
      else
        if  @current_user.parent?
          target = @current_user.guardian_entry.current_ward_id      
          student = Student.find_by_id(target)
        else
          student=current_user.student_record
        end
        @detention = Detention.paginate  :conditions=>"student_id = #{student.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
      
      end
    else     
      if @current_user.employee?
        @employee= @current_user.employee_record
        if @employee.is_pod.to_i == 1
          @detention = Detention.paginate :conditions=>extra_condition,  :order=>"created_at desc", :page=>params[:page], :per_page => 10
        else
          @detention = Detention.paginate  :conditions=>extra_condition+" and employee_id = #{@employee.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
        end  
      elsif @current_user.admin?
        @detention = Detention.paginate :conditions=>extra_condition,  :order=>"created_at desc", :page=>params[:page], :per_page => 10
      else
        if  @current_user.parent?
          target = @current_user.guardian_entry.current_ward_id      
          student = Student.find_by_id(target)
        else
          student=current_user.student_record
        end
        @detention = Detention.paginate  :conditions=>extra_condition+" and student_id = #{student.id}",:order=>"created_at desc", :page=>params[:page], :per_page => 10
      
      end
    end 
    render :partial => 'detention_list'
  end
  def done
    @detention = Detention.find params[:id]
    @detention.update_attributes(:status=>1)
    render :partial => 'detention_status'
  end
  def ackhnowledged
    @detention = Detention.find params[:id]
    @detention.update_attributes(:ackhnowledged=>1)
    render :partial => 'detention_status'
  end
  
  def only_privileged_school_allowed
    require "yaml"
    detention_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/detention.yml")['school']
    all_schools = detention_config['numbers'].split(",")
    current_school = MultiSchool.current_school.id
    if all_schools.include?(current_school.to_s)
      @allow_access = true
    else
      flash[:notice] = "#{t('flash_msg4')}"
      redirect_to :controller => 'user', :action => 'dashboard'
    end
  end
  def only_pod_allowed
    if @current_user.employee?
      employee= @current_user.employee_record
      if employee.is_pod.to_i == 1
        @allow_access = true
      else
        flash[:notice] = "#{t('flash_msg4')}"
        redirect_to :controller => 'user', :action => 'dashboard'
      end
    elsif @current_user.admin?
      @allow_access = true
    else  
      flash[:notice] = "#{t('flash_msg4')}"
      redirect_to :controller => 'user', :action => 'dashboard'
    end  
  end
  
end
