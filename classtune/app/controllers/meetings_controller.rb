class MeetingsController < ApplicationController
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time
  
  def index
    school = MultiSchool.current_school
    include_rel = []
    condition = ["students.school_id = ? AND employees.school_id = ? AND meeting_type = ? ", school.id, school.id, 2]
    if @current_user.admin?
      include_rel = [:student, :employee]
    elsif @current_user.employee?
      condition = ["students.school_id = ? AND teacher_id = ? AND meeting_type = ? AND forward = ?", school.id, @current_user.employee_entry.id, 2, 1]
      include_rel = [:student]
    else
#      student = @current_user.guardian_entry.current_ward
#      students = student.siblings.select{|g| g.immediate_contact_id = @current_user.guardian_entry.id}
#      @student_ids = students.map{|s| s.id} << student.id
      
      #EDITED FOR MULTIPLE GUARDIAN
      @student_ids = []
      unless @current_user.guardian_entry.guardian_student.empty?
        students = @current_user.guardian_entry.guardian_student
        students.each do |student|
          @student_ids << student.id
        end  
      end
      
      condition = ["employees.school_id = ? AND parent_id IN (?) AND meeting_type = ? AND forward = ?", school.id, @student_ids, 1, 1]
      include_rel = [:employee]
    end
    
    @meetings = MeetingRequest.paginate(:order=>"meeting_requests.id DESC", :conditions => condition, :page => params[:page], :per_page => 10, :include => include_rel)
  end
  
  def outbox
    school = MultiSchool.current_school
    include_rel = []
    condition = ["students.school_id = ? AND employees.school_id = ?  AND meeting_type = ? ", school.id, school.id, 1]
    if @current_user.admin?
      include_rel = [:student, :employee]
    elsif @current_user.employee?
      condition = ["students.school_id = ? AND teacher_id = ? AND meeting_type = ?", school.id, @current_user.employee_entry.id, 1]
      include_rel = [:student]
    else
#      student = @current_user.guardian_entry.current_ward
#      students = student.siblings.select{|g| g.immediate_contact_id = @current_user.guardian_entry.id}
#      @student_ids = students.map{|s| s.id} << student.id

      #EDITED FOR MULTIPLE GUARDIAN
      @student_ids = []
      unless @current_user.guardian_entry.guardian_student.empty?
        students = @current_user.guardian_entry.guardian_student
        students.each do |student|
          @student_ids << student.id
        end  
      end
      condition = ["employees.school_id = ? AND parent_id IN (?) AND meeting_type = ?", school.id, @student_ids, 2]
      include_rel = [:employee]
    end
    
    @meetings = MeetingRequest.paginate(:order=>"meeting_requests.id DESC", :conditions => condition, :page => params[:page], :per_page => 10, :include => include_rel)
    render :partial=>"list_outbox"
     
  end
  
   def inbox
    school = MultiSchool.current_school
    include_rel = []
    condition = ["students.school_id = ? AND employees.school_id = ?  AND meeting_type = ? ", school.id, school.id, 2]
    if @current_user.admin?
      include_rel = [:student, :employee]
    elsif @current_user.employee?
      condition = ["students.school_id = ? AND teacher_id = ? AND meeting_type = ? AND forward = ?", school.id, @current_user.employee_entry.id, 2, 1]
      include_rel = [:student]
    else
#      student = @current_user.guardian_entry.current_ward
#      students = student.siblings.select{|g| g.immediate_contact_id = @current_user.guardian_entry.id}
#      @student_ids = students.map{|s| s.id} << student.id

      #EDITED FOR MULTIPLE GUARDIAN
      @student_ids = []
      unless @current_user.guardian_entry.guardian_student.empty?
        students = @current_user.guardian_entry.guardian_student
        students.each do |student|
          @student_ids << student.id
        end  
      end
      
      condition = ["employees.school_id = ? AND parent_id IN (?) AND meeting_type = ? AND forward = ?", school.id, @student_ids, 1, 1]
      include_rel = [:employee]
    end
    
    @meetings = MeetingRequest.paginate(:order=>"meeting_requests.id DESC", :conditions => condition, :page => params[:page], :per_page => 10, :include => include_rel)
    render :partial=>"list"
     
  end

  def new
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    
    @batches = Batch.active
    @departments = EmployeeDepartment.active
    @meeting = MeetingRequest.new()
    @action = 'create'
  end

  def create
    @errors = nil
    datetime = params[:datetime]
    description = params[:meeting_request][:description]
    
    if @current_user.admin? or @current_user.employee?
      
      unless params[:meeting_request][:parent_id].nil?
        teacher_id = @current_user.employee_entry.id
        meeting_type = 1
      
        i = 0
        
        reminder_need_admin_approval = Configuration.find_by_sql ["SELECT id, config_value FROM configurations WHERE config_key = ? AND school_id = ?", 'ReminderNeedAdminApproval', @current_user.school_id]
        
        forward = 0
        if reminder_need_admin_approval.nil? or reminder_need_admin_approval[0]['config_value'].to_i == 0
          forward = 1
        end
        
        params[:meeting_request][:parent_id].each do |p|
          @meeting = MeetingRequest.new()
          @meeting.meeting_type = meeting_type
          @meeting.teacher_id = teacher_id
          @meeting.parent_id = p
          @meeting.description = description
          @meeting.datetime = datetime
          @meeting.status = 0
          @meeting.forward = forward
          if @meeting.valid?
            @meeting.save
            
            reminderrecipients = []
            batch_ids = {}
            student_ids = {}
            @applied_student = Student.find(p)
            
            #EDITED FOR MULTIPLE GUARDIAN
            unless @applied_student.student_guardian.empty?
              guardians = @applied_student.student_guardian
              guardians.each do |guardian|

                unless guardian.user_id.nil?
                  reminderrecipients.push guardian.user_id
                  batch_ids[guardian.user_id] = @applied_student.batch_id
                  student_ids[guardian.user_id] = @applied_student.id
                end
              end  
            end
            
#            unless @applied_student.immediate_contact_id.nil?
#                guardian = Guardian.find(@applied_student.immediate_contact_id)
#                unless guardian.user_id.nil?
#                  reminderrecipients.push guardian.user_id
#                  batch_ids[guardian.user_id] = @applied_student.batch_id
#                  student_ids[guardian.user_id] =  @applied_student.id
#                end
#            end
            unless reminderrecipients.nil? and forward == 1
              Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
              :recipient_ids => reminderrecipients,
              :subject=>"New Meeting Request",
              :rtype=>11,
              :rid=>@meeting.id,
              :student_id => student_ids,
              :batch_id => batch_ids,
              :body=>"New Meeting Request Send from #{@current_user.employee_entry.first_name} at #{@meeting.datetime}" ))
            end 
            
            i = i + 1
          else
            @errors = @meeting.errors
          end
        end
        
        if params[:meeting_request][:parent_id].length == i
          flash[:success] = "Meeting request successfully sent."
        else
          flash[:error] = "Could not sent meeting request at the moment."
        end
        
      else
        flash[:error] = "Form is not properly filled."
      end
      
    else
      
      unless params[:meeting_request][:teacher_id].nil?
        teacher_id = params[:meeting_request][:teacher_id]
        meeting_type = 2
      
        @meeting = MeetingRequest.new()
        @meeting.meeting_type = meeting_type
        @meeting.teacher_id = teacher_id
        @meeting.parent_id = @current_user.guardian_entry.current_ward.id
        @meeting.description = description
        @meeting.datetime = datetime
        @meeting.status = 0
        @meeting.forward = 1
        if @meeting.valid?
          @meeting.save
          
          reminderrecipients = []          
          employees = Employee.find(teacher_id)          
          reminderrecipients.push employees.user_id unless employees.user_id.nil?
          unless reminderrecipients.nil? and @meeting.forward == 1
            Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
            :recipient_ids => reminderrecipients,
            :subject=>"New Meeting Request",
            :rtype=>12,
            :rid=>@meeting.id,
            :body=>"New Meeting Request Send from #{@current_user.guardian_entry.first_name} at #{@meeting.datetime}" ))
          end 
          
          flash[:success] = "Meeting request successfully sent."
        else
          @errors = @meeting.errors
          flash[:error] = "Could not sent meeting request at the moment."
        end
      else
        flash[:error] = "Form is not properly filled."
      end
      
    end
    
    redirect_to :action => "new"
  end

  def edit
  end

  def update
    meeting_id = params[:id]
    forward = 0
    if params[:request] == 'Accept'
      status = 1
      status_text = "Accepted"
    elsif params[:request] == 'Reject'
      status = 2
      status_text = "Declined"
    elsif params[:request] == 'Forward'
      forward = 1
      status = 0
      status_text = "Forwarded"
    else
      status = 0
    end
    
    @meetings = MeetingRequest.find(meeting_id)
    @meetings.status = status 
    
    if forward!=0
      @meetings.forward = forward
    end
    
    if @meetings.save
      if @meetings.status!=0
        if @current_user.admin? or @current_user.employee?
            reminderrecipients = []
            batch_ids = {}
            student_ids = {}
            @applied_student = Student.find(@meetings.parent_id)
            
             #EDITED FOR MULTIPLE GUARDIAN
            unless @applied_student.student_guardian.empty?
              guardians = @applied_student.student_guardian
              guardians.each do |guardian|

                unless guardian.user_id.nil?
                  reminderrecipients.push guardian.user_id
                  batch_ids[guardian.user_id] = @applied_student.batch_id
                  student_ids[guardian.user_id] = @applied_student.id
                end
              end  
            end
            
#            unless @applied_student.immediate_contact_id.nil?
#                guardian = Guardian.find(@applied_student.immediate_contact_id)
#                unless guardian.user_id.nil?
#                  reminderrecipients.push guardian.user_id
#                  batch_ids[guardian.user_id] = @applied_student.batch_id
#                  student_ids[guardian.user_id] =  @applied_student.id
#                end
#            end
            unless reminderrecipients.nil? and params[:request] != 'Forward'
              Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
              :recipient_ids => reminderrecipients,
              :subject=>"Your Meeting Request is "+status_text,
              :rtype=>13,
              :rid=>@meetings.id,
              :student_id => student_ids,
              :batch_id => batch_ids,
              :body=>"Your meeting request with #{@current_user.employee_entry.first_name}  have been  #{status_text} for #{@meetings.datetime}" ))
            end 
        else
          reminderrecipients = []          
          employees = Employee.find(@meetings.teacher_id)          
          reminderrecipients.push employees.user_id unless employees.user_id.nil?
          unless reminderrecipients.nil? and params[:request] != 'Forward'
            Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
            :recipient_ids => reminderrecipients,
            :subject=>"Your Meeting Request is "+status_text,
            :rtype=>14,
            :rid=>@meetings.id,
            :body=>"Your meeting request with #{@current_user.guardian_entry.first_name} have been  #{status_text} for #{@meetings.datetime}" ))
          end 

        end 
      end
      
      flash[:success] = "Meeting request successfully sent."
    else
      @errors = @meeting.errors
      flash[:error] = "Could not sent meeting request at the moment."
    end
    redirect_to :action => "show", :id => @meetings.id
  end

  def show
    meeting_id = params[:id]
    school = MultiSchool.current_school
    include_rel = []
    condition = ["students.school_id = ? AND employees.school_id = ?", school.id, school.id]
    if @current_user.admin?
      include_rel = [:student, :employee]
    elsif @current_user.employee?
      condition = ["students.school_id = ? AND meeting_requests.teacher_id = ? and (forward = 1 or meeting_type = 2)", school.id, @current_user.employee_entry.id]
      include_rel = [:student]
    else
#      student = @current_user.guardian_entry.current_ward
#      students = student.siblings.select{|g| g.immediate_contact_id = @current_user.guardian_entry.id}
#      @student_ids = students.map{|s| s.id} << student.id
      
      #EDITED FOR MULTIPLE GUARDIAN
      @student_ids = []
      unless @current_user.guardian_entry.guardian_student.empty?
        students = @current_user.guardian_entry.guardian_student
        students.each do |student|
          @student_ids << student.id
        end  
      end
      
      condition = ["employees.school_id = ? AND parent_id IN (?) and (forward = 1 or meeting_type = 2)", school.id, @student_ids]
      include_rel = [:employee]
    end
    @meetings = MeetingRequest.find(meeting_id, :conditions => condition, :include => include_rel)
  end

  def destroy
  end
  
  def get_classes
    school_id = MultiSchool.current_school.id
    unless params[:batch_id].empty?
        batch_data = Batch.find params[:batch_id]
        batch_name = batch_data.name
    end 
    @courses = []
    unless batch_name.blank?
    @courses = Rails.cache.fetch("classes_data_#{batch_name.parameterize("_")}_#{school_id}"){
      @batch_name = batch_name;
      batches = Batch.find(:all, :conditions => ["name = ? and is_deleted = 0", batch_name]).map{|b| b.course_id}
      tmp_classes = Course.find(:all, :conditions => ["id IN (?) and is_deleted = 0", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
      class_data = tmp_classes
      class_data
    }
    end
    @classes = []
    @batch_id = ''
    @course_name = ""
    render :update do |page|
      if params[:page].nil?
        page.replace_html 'course', :partial => 'courses', :object => @courses
        unless params[:section_page].nil? and params[:section_partial].nil?
          page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
        end
        unless params[:page_batch].nil? and params[:partial_view_batch].nil?
          page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
        end
      else
        if params[:partial_view].nil?
          page.replace_html params[:page], :partial => 'courses', :object => @courses
          unless params[:section_page].nil? and params[:section_partial].nil?
            page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
          end
          unless params[:page_batch].nil? and params[:partial_view_batch].nil?
            page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
          end
        else
          page.replace_html params[:page], :partial => params[:partial_view], :object => @courses
          unless params[:section_page].nil? and params[:section_partial].nil?
            page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
          end
          unless params[:page_batch].nil? and params[:partial_view_batch].nil?
            page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
          end
        end
      end  
    end
  end
  
  def get_batches
    batch_name = ""
    if Batch.active.find(:all, :group => "name").length > 1
      unless params[:student].nil?
        unless params[:student][:batch_name].nil?
          batch_id = params[:student][:batch_name]
          batches_data = Batch.find_by_id(batch_id)
          batch_name = batches_data.name
        end
      end

      unless params[:advv_search].nil?
        unless params[:advv_search][:batch_name].nil?
          batch_id = params[:advv_search][:batch_name]
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
      unless params[:advv_search].nil?
        unless params[:advv_search][:section].nil?
          course_id = params[:advv_search][:section]
        end
      end
    end
    
    school_id = MultiSchool.current_school.id
    
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
      
    @batch_id = 0
    unless @batch_data.nil?
      @batch_id = @batch_data.id 
    end
    
    render :update do |page|
      if params[:page].nil?
        page.replace_html 'batches', :partial => 'batches', :object => @batch_id
      else
        if params[:partial_view].nil?
          page.replace_html params[:page], :partial => 'sections', :object => @batch_id
        else
          page.replace_html params[:page], :partial => params[:partial_view], :object => @batch_id
        end
      end
    end
  end
  
  def get_section_data
    batch_id = 0
    unless params[:student].nil?
      unless params[:student][:batch_name].nil?
        batch_id = params[:student][:batch_name]
      end
    end
    
    unless params[:advv_search].nil?
      unless params[:advv_search][:batch_name].nil?
        batch_id = params[:advv_search][:batch_name]
      end
    end
    
    school_id = MultiSchool.current_school.id
    @classes = Rails.cache.fetch("section_data_#{params[:class_name]}_#{batch_id}_#{school_id}"){
      if batch_id.to_i > 0
        batch = Batch.find batch_id
        batch_name = batch.name
        
        batches = Batch.find(:all, :conditions => ["name = ? and is_active = 1 and is_deleted = 0", batch_name]).map{|b| b.course_id}
        tmp_class_data = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id IN (?)",params[:class_name], batches])
      else  
        tmp_class_data = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0",params[:class_name]])
      end
      class_data = tmp_class_data
      class_data
    }
    @selected_section = 0
    
    @batch_id = 0
    @courses = []
    
    render :update do |page|
      
      if params[:page].nil?
        page.replace_html 'section', :partial => 'sections', :object => @classes
        unless params[:section_page].nil? and params[:section_partial].nil?
          page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
        end
        unless params[:page_batch].nil? and params[:partial_view_batch].nil?
          page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
        end
      else
        if params[:partial_view].nil?
          page.replace_html params[:page], :partial => 'sections', :object => @classes
          unless params[:section_page].nil? and params[:section_partial].nil?
            page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
          end
          unless params[:page_batch].nil? and params[:partial_view_batch].nil?
            page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
          end
        else
          page.replace_html params[:page], :partial => params[:partial_view], :object => @classes
          unless params[:section_page].nil? and params[:section_partial].nil?
            page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
          end
          unless params[:page_batch].nil? and params[:partial_view_batch].nil?
            page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
          end
        end
      end  
    end
  end
  
  def list_students_by_course
    batch_name = ""
    unless params[:student][:batch_name].nil?
      batch_id = params[:student][:batch_name]
      batches_data = Batch.find_by_id(batch_id)
      batch_name = batches_data.name
      
    end
    course_id = 0
    unless params[:course_id].nil?
      course_id = params[:course_id]
    end
    if course_id == 0
      unless params[:student][:section].nil?
        course_id = params[:student][:section]
      end
    end
    
    if course_id.to_i > 0
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
      
      @batch_id = 0
      unless @batch_data.nil?
        @batch_id = @batch_data.id 
      end
    else
      @batch_id = params[:batch_id]
    end
    @students = Student.find_all_by_batch_id(@batch_id, :order => 'first_name ASC')
    render(:update) { |page| page.replace_html 'students', :partial => 'students_by_course' }
  end
  
  def employees_list
    department_id = params[:department_id]
    @employees = Employee.find_all_by_employee_department_id(department_id,:order=>'first_name ASC')

    render :update do |page|
      page.replace_html 'employee_list', :partial => 'employee_view_all_list', :object => @employees
    end
  end

end
