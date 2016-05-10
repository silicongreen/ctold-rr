class AssignmentsController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only=>[:index]
  filter_access_to :all,:except=>[:show]
  filter_access_to :show,:attribute_check=>true
  before_filter :default_time_zone_present_time
  
  def index
   
    
    @current_user = current_user
    if    @current_user.employee?
      @subjects = current_user.employee_record.subjects.active
      @subjects.reject! {|s| !s.batch.is_active}
    elsif    @current_user.student?
      student=current_user.student_record
      
      @batch = student.batch      
      @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"elective_group_id IS NULL AND is_deleted = false", :include => [:employees])
      @student_electives =StudentsSubject.all(:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
      @elective_subjects = []
      @student_electives.each do |e|
        @elective_subjects.push Subject.find(e.subject_id)
      end
      @subjects = @normal_subjects+@elective_subjects
      
      @assignments = Assignment.paginate  :conditions=>"FIND_IN_SET(#{student.id},student_list) and is_published=1",:order=>"created_at desc", :page=>params[:page], :per_page => 10

    elsif    @current_user.parent?
      target = @current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      @batch = student.batch      
      @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"elective_group_id IS NULL AND is_deleted = false", :include => [:employees])
      @student_electives =StudentsSubject.all(:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
      @elective_subjects = []
      @student_electives.each do |e|
        @elective_subjects.push Subject.find(e.subject_id)
      end
      @subjects = @normal_subjects+@elective_subjects
      @assignments = Assignment.paginate  :conditions=>"FIND_IN_SET(#{student.id},student_list) and is_published=1",:order=>"created_at desc", :page=>params[:page], :per_page => 10
      

    elsif @current_user.admin?
      @classes = []
      @batches = []
      @batch_no = 0
      @course_name = ""
      @courses = []
      @batches = Batch.active      
    end
  end
  def showsubjects
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
    
    @batch_data = Rails.cache.fetch("course_data_#{course_id}_#{batch_name}_#{current_user.id}"){
      if batch_name.length == 0
        batches = Batch.find_by_course_id(course_id)
      else
        batches = Batch.find_by_course_id_and_name(course_id, batch_name)
      end
      batches
    }
    @batch_id = 0
    unless @batch_data.nil?
      @batch_id = @batch_data.id 
    end
    
    if @batch_id == ''
      @subjects = []
    else
      @batch = Batch.find @batch_id
      @normal_subjects = Subject.find_all_by_batch_id(@batch,:conditions=>"elective_group_id IS NULL AND is_deleted = false")
      @student_electives =StudentsSubject.all(:conditions=>{:batch_id=>@batch,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
      @elective_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>["elective_group_id IS NOT NULL AND is_deleted = false"])
#      @elective_subjects = []
#      @student_electives.each do |e|
#        @elective_subjects.push Subject.find(e.subject_id)
#      end
      @subjects = @normal_subjects+@elective_subjects
    end
    
    #puts @elective_groups.to_yaml
    respond_to do |format|
      format.js { render :action => 'showsubjects' }
    end
  end

  def subject_assignments

    @due_date = params[:due_date]
    @is_published = params[:is_published]
    @subject = Subject.find_by_id params[:subject_id]
    employee_id = current_user.employee_record.id
    publish_condition = ""
    if !@is_published.blank?
      publish_condition = " and is_published='#{@is_published}'";
    end
    
    if !@subject.nil? and !@due_date.blank? 
      @due_date = @due_date.to_datetime.strftime("%Y-%m-%d")
      @assignments = Assignment.paginate :conditions=>"subject_id=#{@subject.id} and DATE(duedate) = '#{@due_date}' #{publish_condition} and employee_id=#{employee_id}",:order=>"duedate desc", :page=>params[:page]
    elsif @subject.nil? and !@due_date.blank?
      @due_date = @due_date.to_datetime.strftime("%Y-%m-%d")
      @assignments = Assignment.paginate :conditions=>"DATE(duedate) = '#{@due_date}' #{publish_condition} and employee_id=#{employee_id}",:order=>"duedate desc", :page=>params[:page]
    elsif !@subject.nil? and @due_date.blank?
      @assignments = Assignment.paginate :conditions=>"subject_id=#{@subject.id} #{publish_condition} and employee_id=#{employee_id}",:order=>"duedate desc", :page=>params[:page]
    else
      @assignments = Assignment.paginate :conditions=>"employee_id=#{employee_id} #{publish_condition} ",:order=>"duedate desc", :page=>params[:page]
    end
    
    render(:update) do |page|
      page.replace_html 'subject_assignments_list', :partial=>'subject_assignments'
    end
  end

  def subject_assignments2

    @due_date = params[:due_date]
    @subject =Subject.find_by_id params[:subject_id]
    @subject_id = params[:subject_id]
    if @current_user.parent?
      target = @current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
    else  
      student=current_user.student_record
    end
    if !@subject.nil? and !@due_date.blank?
      @due_date = @due_date.to_datetime.strftime("%Y-%m-%d")
      @assignments = Assignment.paginate  :conditions=>"subject_id=#{@subject.id} and is_published=1 and DATE(duedate) = '#{@due_date}' and FIND_IN_SET(#{student.id},student_list)",:order=>"created_at desc", :page=>params[:page], :per_page => 10
    elsif @subject.nil? and !@due_date.blank?
      @due_date = @due_date.to_datetime.strftime("%Y-%m-%d")
      @assignments = Assignment.paginate  :conditions=>"DATE(duedate) = '#{@due_date}' and is_published=1 and FIND_IN_SET(#{student.id},student_list)",:order=>"created_at desc", :page=>params[:page], :per_page => 10
    elsif !@subject.nil? and @due_date.blank?
      @assignments = Assignment.paginate  :conditions=>"subject_id=#{@subject.id} and is_published=1 and FIND_IN_SET(#{student.id},student_list)",:order=>"created_at desc", :page=>params[:page], :per_page => 10
    else
      @assignments = Assignment.paginate  :conditions=>"FIND_IN_SET(#{student.id},student_list) and is_published=1",:order=>"created_at desc", :page=>params[:page], :per_page => 10
    end
    render(:update) do |page|
      page.replace_html 'listing', :partial=>'subject_assignments2'
    end
  end
  
  def subject_assignments3

    @subject =Subject.find_by_id params[:subject_id]    
    unless @subject.nil?
      @assignments =Assignment.paginate  :conditions=>"subject_id=#{@subject.id} and is_published=1 ",:order=>"duedate desc", :page=>params[:page]    
    else
      @assignments = []
    end
    render(:update) do |page|
      page.replace_html 'listing', :partial=>'subject_assignments3'
    end
  end
  
  def new
    if current_user.employee?
      @subjects = current_user.employee_record.subjects
      
      @subjects.reject! {|s| !s.batch.is_active}
      @assignment= Assignment.new
      unless params[:id].nil?
        subject = Subject.find_by_id(params[:id])
        employeeassociated = EmployeesSubject.find_by_subject_id_and_employee_id( subject.id,current_user.employee_record.id)
        unless employeeassociated.nil?
          if subject.elective_group_id.nil?
            @students = subject.batch.students
          else
            assigned_students = StudentsSubject.find_all_by_subject_id(subject.id)
            @students = assigned_students.map{|s| Student.find s}
          end
          @assignment = subject.assignments.build
        end
      end
    end
    if current_user.admin?
      @subject =Subject.find_by_id params[:subject_id] 
      @assignment= Assignment.new
            
      unless @subject.nil?
        if @subject.elective_group_id.nil?
          @students = @subject.batch.students
        else
          assigned_students = StudentsSubject.find_all_by_subject_id(@subject)
          @students= []
          @assigned_students = []
          assigned_students.each do |assigned_student|
            s = Student.find_by_id(assigned_student.student_id)
            if s.nil?
              s=ArchivedStudent.find_by_former_id assigned_student.student_id
            end
            @students << s
            @assigned_students << s
          end
        end
      end

      
    end
  end


  def subjects_students_list
    @subject = Subject.find_by_id params[:subject_id]
    unless @subject.nil?
      if @subject.elective_group_id.nil?
        @students = @subject.batch.students
      else
        assigned_students = StudentsSubject.find_all_by_subject_id(@subject.id)
        @students = assigned_students.map{|s| s.student}
        @students=@students.compact
      end
    end
    render(:update) do |page|
      page.replace_html 'subjects_student_list', :partial=>"subjects_student_list"
    end
  end


  def assignment_student_list
    #List the students of the assignment based on their Status of their assignment
    @assignment = Assignment.active.find_by_id params[:id]
    unless @assignment.nil?
      @status = params[:status]
      if @status == "assigned"
        assigned_students= @assignment.student_list.split(",")
        @students=[]
        assigned_students.each do |assigned_student|
          s = Student.find_by_id assigned_student
          if s.nil?
            s = ArchivedStudent.find_by_former_id assigned_student
          end
          @students << s if s.present?
        end
        @students = @students.sort_by{|s| s.full_name}
      elsif @status == "answered"
        @answers = @assignment.assignment_answers
#        @answers  = @answers.sort_by{|a| a.updated_at}
        @answers  = @answers.sort_by{|a| (a && a.updated_at(:length)) || 0}
        @students = @answers.map{|a| a.student }
      elsif @status== "pending"
        answers = @assignment.assignment_answers
        answered_students = answers.map{|a| a.student_id.to_s }
        assigned_students= @assignment.student_list.split(",")
        pending_students = assigned_students - answered_students
        @students = []
        pending_students.each do |pending_student|
          s = Student.find_by_id pending_student
          if s.nil?
            s=ArchivedStudent.find_by_former_id pending_student
          end
          @students << s if s.present?
        end
        @students = @students.sort_by{|s| s.full_name}
      end
      render(:update) do |page|
        page.replace_html 'student_list', :partial=>'student_list'
      end
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end
  
  
  

  def create
    student_ids = params[:assignment][:student_ids]
    params[:assignment].delete(:student_ids)
    @subject = Subject.find_by_id(params[:assignment][:subject_id])
    @assignment = Assignment.new(params[:assignment])
    @assignment.student_list = student_ids.join(",") unless student_ids.nil?
    @assignment.employee = current_user.employee_record
    
    students = Student.find_all_by_id(student_ids)
    available_user_ids = []
    batch_ids = {}
    student_ids = {}

    students.each do |st|
      available_user_ids << st.user_id
      batch_ids[st.user_id] = st.batch_id
      student_ids[st.user_id] = st.id
      @student = Student.find(st.id)
      unless @student.student_guardian.empty?
          guardians = @student.student_guardian
          guardians.each do |guardian|
#            guardian = Guardian.find(@student.immediate_contact_id)

            unless guardian.user_id.nil?
              available_user_ids << guardian.user_id
              batch_ids[guardian.user_id] = @student.batch_id
              student_ids[guardian.user_id] = @student.id
            end
          end  
      end
    end
    #available_user_ids = students.collect(&:user_id).compact unless student_ids.nil?
    unless @subject.nil?
      if @assignment.save
        if @assignment.is_published==1
          Delayed::Job.enqueue(
            DelayedReminderJob.new( :sender_id  => current_user.id,
              :recipient_ids => available_user_ids,
              :subject=>"#{t('new_homework_added')} : "+params[:assignment][:title],
              :rtype=>4,
              :rid=>@assignment.id,
              :student_id => student_ids,
              :batch_id => batch_ids,
              :body=>"#{t('homework_added_for')} #{@subject.name}  <br/>#{t('view_reports_homework')}")
          )
        end
        flash[:notice] = "#{t('new_assignment_sucessfuly_created')}"
        redirect_to :action=>:index
      else
        if current_user.employee?
          @subjects = current_user.employee_record.subjects
        
          @subjects.reject! {|s| !s.batch.is_active?}
          @students = @subject.batch.students
        end
        render :action=>:new
      end
    else
      unless @assignment.save
        @subjects = current_user.employee_record.subjects
        render :action=>:new
      end
    end
  end

  def show
    @assignment  = Assignment.active.find(params[:id], :include => [:employee])
    unless @assignment.nil?
      @current_user = current_user
      @assignment_answers = @assignment.assignment_answers
      @students_assigned_count = @assignment.student_list.split(",").count
      @answered_count = @assignment_answers.count
      @pending_count =     @students_assigned_count  -  @answered_count
      @assignment_answers = AssignmentAnswer.find_all_by_student_id_and_assignment_id(current_user.student_record.id,@assignment.id) if current_user.student?
      
      @subject =Subject.find_by_id @assignment.subject_id
      student=current_user.student_record
      unless @subject.nil?
        @assignments_list =Assignment.paginate  :conditions=>"subject_id=#{@subject.id} and is_published=1 and FIND_IN_SET(#{student.id},student_list)",:order=>"duedate desc", :page=>params[:page]
      else
        @assignments_list =Assignment.paginate  :conditions=>"FIND_IN_SET(#{student.id},student_list) and is_published=1",:order=>"duedate desc", :page=>params[:page]
      end
      
      Reminder.update_all("is_read='1'",  ["rid = ? and rtype = ? and recipient= ?", params[:id], 4,current_user.id])
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end

  def edit
    @assignment  = Assignment.active.find(params[:id])
    unless @assignment.nil?
      #if @assignment.employee_id==current_user.employee_record.id
      if @current_user.admin? or @current_user.employee?
        load_data
      else
        flash[:notice] ="#{t('you_cannot_edit_this_assignment')}"
        redirect_to assignments_path
      end
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end
  def update
    @assignment = Assignment.find_by_id(params[:id])
    unless @assignment.nil?
      student_ids = params[:assignment][:student_ids]
      params[:assignment].delete(:student_ids)
      @assignment.student_list = student_ids.join(",") unless student_ids.nil?
      if  @assignment.update_attributes(params[:assignment])
        flash[:notice]="#{t('assignment_details_updated')}"
        redirect_to @assignment
      else
        load_data
        render :edit ,:id=>params[:id]
      end
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end
  
  def published_homework
    now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    @assignment = Assignment.find_by_id(params[:id])
    @assignment.is_published = 1
    @assignment.created_at = now
    
    if @assignment.save
      a_student = @assignment.student_list
      if !a_student.blank?
        @subject = Subject.find_by_id(@assignment.subject_id)
        student_ids = a_student.split(',')
        students = Student.find_all_by_id(student_ids)
        available_user_ids = []
        batch_ids = {}
        student_ids = {}

        students.each do |st|
          available_user_ids << st.user_id
          batch_ids[st.user_id] = st.batch_id
          student_ids[st.user_id] = st.id
          @student = Student.find(st.id)
          unless @student.student_guardian.empty?
              guardians = @student.student_guardian
              guardians.each do |guardian|
    #            guardian = Guardian.find(@student.immediate_contact_id)

                unless guardian.user_id.nil?
                  available_user_ids << guardian.user_id
                  batch_ids[guardian.user_id] = @student.batch_id
                  student_ids[guardian.user_id] = @student.id
                end
              end  
          end
        end
        #available_user_ids = students.collect(&:user_id).compact unless student_ids.nil?
        Delayed::Job.enqueue(
            DelayedReminderJob.new( :sender_id  => current_user.id,
              :recipient_ids => available_user_ids,
              :subject=>"#{t('new_homework_added')} : "+@assignment.title,
              :rtype=>4,
              :rid=>@assignment.id,
              :student_id => student_ids,
              :batch_id => batch_ids,
              :body=>"#{t('homework_added_for')} #{@subject.name}  <br/>#{t('view_reports_homework')}")
          )
      end
      flash[:notice] = "Homerok successfully published"
      redirect_to assignments_path
    end
  end

  def destroy
    @assignment = Assignment.find_by_id(params[:id])
    if (current_user.admin?) or (@assignment.employee_id == current_user.employee_record.id)
      @assignment.destroy
      flash[:notice] = "#{t('assignment_sucessfully_deleted')}"
      redirect_to assignments_path
    else
      flash[:notice] = "#{t('you_do_not_have_permission_to_delete_this_assignment')}"
      redirect_to edit_assignment_path(@assignment)
    end

  end
  def download_attachment
    #download the  attached file
    @assignment =Assignment.active.find params[:id]
    unless @assignment.nil?
      if @assignment.download_allowed_for(current_user)
        send_file  @assignment.attachment.path , :type=>@assignment.attachment.content_type
      else
        flash[:notice] = "#{t('you_are_not_allowed_to_download_that_file')}"
        redirect_to :controller=>:assignments
      end
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end

  def load_data
    @subject = @assignment.subject
    unless @subject.nil?
      if @subject.elective_group_id.nil?
        @students = @subject.batch.students
      else
        assigned_students = StudentsSubject.find(:all , :conditions => {:subject_id => @subject.id})
        @students= []
        assigned_students.each do |assigned_student|
          s = Student.find_by_id(assigned_student.student_id)
          if s.nil?
            s=ArchivedStudent.find_by_former_id assigned_student.student_id
          end
          @students << s
        end
      end
    end
    @assigned_students = []
    @assignment.student_list.split(",").each do |s|
      student = Student.find_by_id s
      if student.nil?
        student=ArchivedStudent.find_by_former_id s
      end
      @assigned_students << student
    end
    if current_user.admin?      
      @subjects = @subject     
    elsif current_user.employee?
      @subjects = current_user.employee_record.subjects
      @subjects.reject! {|s| !s.batch.is_active}
    end   
  end
  
  

end
