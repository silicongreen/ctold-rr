class AssignmentsController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only=>[:index]
  filter_access_to :all,:except=>[:show]
  filter_access_to :show,:attribute_check=>true
  before_filter :default_time_zone_present_time
  before_filter :only_publisher_admin_allowed , :only=>[:publisher,:show_publisher,:get_homework_filter_publisher,:showsubjects_publisher,:publisher_homework,:deny_homework,:subject_assignments_publisher]
  
  def get_homework_filter
    batch_id = params[:batch_name]
    student_class_name = params[:student_class_name]
    student_section = params[:student_section]
    @assignments = []
    unless batch_id.nil?
      batchdata = Batch.find_by_id(batch_id)
      unless batchdata.blank?
        batch_name = batchdata.name
        if student_class_name.blank?
          @assignments =Assignment.paginate  :conditions=>"batches.name = '#{batch_name}'  and is_published=1 ",:order=>"duedate desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}]     
        elsif student_section.blank?
          @assignments =Assignment.paginate  :conditions=>"batches.name = '#{batch_name}' and courses.course_name = '#{student_class_name}'  and is_published=1 ",:order=>"duedate desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[{:batch=>[:course]}]}] 
        else
          batch = Batch.find_by_course_id_and_name(student_section, batch_name)
          unless batch.blank?
            @assignments =Assignment.paginate  :conditions=>"batches.id = '#{batch.id}'  and is_published=1 ",:order=>"duedate desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
          end
        end  
      end
    end
    respond_to do |format|
      format.js { render :action => 'get_homework_filter' }
    end
   
  end
  
  def get_homework_filter_publisher
    batch_id = params[:batch_name]
    student_class_name = params[:student_class_name]
    student_section = params[:student_section]
    batches_all_id = []
    if current_user.employee
      batches_all = @current_user.employee_record.batches
    else
      batches_all = Batch.active
    end 
    
    batches_all_id = batches_all.map{|b| b.id}
    
    @assignments = []
    unless batch_id.nil?
      batchdata = Batch.find_by_id(batch_id)
      unless batchdata.blank?
        batch_name = batchdata.name
        if student_class_name.blank?
          @assignments =Assignment.paginate  :conditions=>["batches.id IN (?) and batches.name = ?  and (is_published=2 or is_published=3 or is_published=4)",batches_all_id,batch_name],:order=>"duedate desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}]     
        elsif student_section.blank?
          @assignments =Assignment.paginate  :conditions=>["batches.id IN (?) and batches.name = ? and courses.course_name = ?  and (is_published=2 or is_published=3 or is_published=4)",batches_all_id,batch_name,student_class_name],:order=>"duedate desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[{:batch=>[:course]}]}] 
        else
          batch = Batch.find_by_course_id_and_name(student_section, batch_name)
          unless batch.blank?
            @assignments =Assignment.paginate  :conditions=>["batches.id IN (?) and batches.id = ?  and (is_published=2 or is_published=3 or is_published=4)",batches_all_id,batch.id],:order=>"duedate desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
          end
        end  
      end
    end
    respond_to do |format|
      format.js { render :action => 'get_homework_filter_publisher' }
    end
   
  end
  
  def index
    @current_user = current_user
    if    @current_user.employee?
      @subjects = current_user.employee_record.subjects.active
      @subjects.reject! {|s| !s.batch.is_active}
      @subjects.sort_by{|s| s.batch.course.code.to_i}
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
  
  def publisher
    
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    if current_user.employee
      @batches2 = @current_user.employee_record.batches
      @batches2 = @batches2.uniq unless @batches.empty?
      batch_name = []
      @batches2.each do |batch|
        unless batch_name.include?(batch.name)
          batch_name << batch.name
          @batches << batch
        end
      end
      
    elsif current_user.admin
      @batches2 = Batch.active
      batch_name = []
       @batches2.each do |batch|
         unless batch_name.include?(batch.name)
           batch_name << batch.name
           @batches << batch
         end
       end
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
    unless params[:student].nil? and params[:student][:from].nil?
      @from = params[:student][:from]
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
      
    @batch_id = 0
    unless @batch_data.nil?
      @batch_id = @batch_data.id 
    end
    
    if @batch_id.blank? or @batch_id == 0
      @subjects = []
    else
      @batch = Batch.find @batch_id
      @normal_subjects = Subject.find_all_by_batch_id(@batch,:conditions=>"elective_group_id IS NULL AND is_deleted = false")
      @student_electives =StudentsSubject.all(:conditions=>{:batch_id=>@batch,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
      @elective_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>["elective_group_id IS NOT NULL AND is_deleted = false"])
      @subjects = @normal_subjects+@elective_subjects
    end
    
    #puts @elective_groups.to_yaml
    respond_to do |format|
      format.js { render :action => 'showsubjects' }
    end
  end
  
  def showsubjects_publisher
    batch_name = ""
    if Batch.active.find(:all, :group => "name").length > 1
      unless params[:student].nil?
        unless params[:student][:batch_name].nil?
          batch_id = params[:student][:batch_name]
          batches_data = Batch.find_by_id(batch_id)
          batch_name = batches_data.name
        end
        unless params[:student][:from].nil?
          @from = params[:student][:from]
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
      
    @batch_id = 0
    unless @batch_data.nil?
      @batch_id = @batch_data.id 
    end
    
    if @batch_id.blank? or @batch_id == 0
      @subjects = []
    else
      @batch = Batch.find @batch_id
      @normal_subjects = Subject.find_all_by_batch_id(@batch,:conditions=>"elective_group_id IS NULL AND is_deleted = false")
      @student_electives =StudentsSubject.all(:conditions=>{:batch_id=>@batch,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
      @elective_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>["elective_group_id IS NOT NULL AND is_deleted = false"])
      @subjects = @normal_subjects+@elective_subjects
    end

    respond_to do |format|
      format.js { render :action => 'showsubjects_publisher' }
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
  
  def subject_assignments_publisher

    @subject =Subject.find_by_id params[:subject_id]    
    unless @subject.nil?
      @assignments =Assignment.paginate  :conditions=>"subject_id=#{@subject.id} and (is_published=2 or is_published=3) ",:order=>"duedate desc", :page=>params[:page] , :per_page => 20   
    else
      @assignments = []
    end
    render(:update) do |page|
      page.replace_html 'listing', :partial=>'subject_assignments_publisher'
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
      @assignments =Assignment.paginate  :conditions=>"subject_id=#{@subject.id} and is_published=1 ",:order=>"duedate desc", :page=>params[:page] , :per_page => 20   
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
      
      @subjects.sort_by{|s| s.batch.course.code.to_i}
      
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
            @students.reject!{|e| e.batch_id!=@subject.batch_id}
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
          assigned_students = StudentsSubject.find_all_by_subject_id(@subject.id)
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
          @students.reject!{|e| e.batch_id!=@subject.batch_id}
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
        assigned_students = StudentsSubject.find_all_by_subject_id_and_batch_id(@subject.id,@subject.batch_id)
        @students = []
        unless assigned_students.blank?
          assigned_students.each do |std|
            unless std.blank?
              unless std.batch_id.blank?
                if std.batch_id == @subject.batch_id
                  @students << std
                end
              end
            end 
          end
        end
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
    
    @config = Configuration.find_by_config_key('HomeworkWillForwardOnly')
    if (!@config.blank? and !@config.config_value.blank? and @config.config_value.to_i == 1) and !@current_user.admin? and @current_user.employee_entry.homework_publisher != 1 and @assignment.is_published != 0        
      @assignment.is_published = 2
    end
    
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
        elsif @assignment.save and @assignment.is_published == 2
          batch = @assignment.subject.batch
          unless batch.blank?
            batch_tutor = batch.employees
            available_user_ids = []
            unless batch_tutor.blank?
              batch_tutor.each do |employee|
                if employee.homework_publisher == 1
                  available_user_ids << employee.user_id
                end
              end
            end
          end

          unless available_user_ids.blank?
            Delayed::Job.enqueue(
              DelayedReminderJob.new( :sender_id  => current_user.id,
                :recipient_ids => available_user_ids,
                :subject=>"#{t('your_action_required')} : #{t('new_homework_added')} '#{@assignment.title}' #{t('added_for')} #{@subject.name}",
                :rtype=>4,
                :rid=>@assignment.id,
                :student_id => 0,
                :batch_id => 0,
                :body=>"#{t('your_action_required')} : #{t('new_homework_added')} '#{@assignment.title}' #{t('added_for')} #{@subject.name} <br/>#{t('view_reports_homework')}")
            )
          end
        
        end
        
        if (!@config.blank? and !@config.config_value.blank? and @config.config_value.to_i == 1) and !@current_user.admin? and @current_user.employee_entry.homework_publisher != 1 and @assignment.is_published != 0
          flash[:notice] = "#{t('new_assignment_sucessfuly_forwarded')}"
        else
          flash[:notice] = "#{t('new_assignment_sucessfuly_created')}"
        end
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
      #RR assignment defaulter added
      @defaulter_registered = AssignmentDefaulterRegistration.find_by_assignment_id(@assignment.id)
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
  
  def show_publisher
    @assignment  = Assignment.active.find(params[:id], :include => [:employee])
    unless @assignment.nil?
      #RR assignment defaulter added
      @defaulter_registered = AssignmentDefaulterRegistration.find_by_assignment_id(@assignment.id)
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
    @config = Configuration.find_by_config_key('HomeworkWillForwardOnly')
    unless @assignment.nil?
      #if @assignment.employee_id==current_user.employee_record.id
      if @current_user.admin? or @current_user.employee? or (!@config.blank? and !@config.config_value.blank? and @config.config_value.to_i == 1 and @current_user.employee_entry.homework_publisher == 1)
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
        @config = Configuration.find_by_config_key('HomeworkWillForwardOnly')
        if !@config.blank? and !@config.config_value.blank? and @config.config_value.to_i == 1 and (@current_user.employee_entry.homework_publisher == 1 or @current_user.admin?) 
          redirect_to :controller=>:assignments ,:action=>:show_publisher, :id=>@assignment.id
        else
          redirect_to @assignment
        end
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
    
    @config = Configuration.find_by_config_key('HomeworkWillForwardOnly')
   
    if !@config.blank? and !@config.config_value.blank? and @config.config_value.to_i == 1 and !@current_user.admin? and @current_user.employee_entry.homework_publisher != 1 and  @assignment.is_published.to_i!=3          
      @assignment.is_published = 2
    else
      @assignment.is_published = 1
    end  
      
    @assignment.created_at = now
    
    if @assignment.save and @assignment.is_published == 1
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
      
      flash[:notice] = "Homework successfully published"
      redirect_to assignments_path
      elsif @assignment.save and @assignment.is_published == 2
        batch = @assignment.subject.batch
        unless batch.blank?
          batch_tutor = batch.employees
          available_user_ids = []
          unless batch_tutor.blank?
            batch_tutor.each do |employee|
              if employee.homework_publisher == 1
                available_user_ids << employee.user_id
              end
            end
          end
        end

        unless available_user_ids.blank?
          Delayed::Job.enqueue(
            DelayedReminderJob.new( :sender_id  => current_user.id,
              :recipient_ids => available_user_ids,
              :subject=>"#{t('your_action_required')} : #{t('new_homework_added')} #{@assignment.title} #{t('added_for')}  #{@assignment.subject.name}",
              :rtype=>4,
              :rid=>@assignment.id,
              :student_id => 0,
              :batch_id => 0,
              :body=>"#{t('your_action_required')} : #{t('new_homework_added')} #{@assignment.title} #{t('added_for')} #{@assignment.subject.name} <br/>#{t('view_reports_homework')}")
          )
        end 
        flash[:notice] = "#{t('new_assignment_sucessfuly_forwarded')}"
        redirect_to assignments_path
      end   
  end
  
  
  def deny_homework
    now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    @assignment = Assignment.find_by_id(params[:id])
    previous_status = @assignment.is_published
    @assignment.is_published = 4
    
    if @assignment.save
          @subject = Subject.find_by_id(@assignment.subject_id)
          emp = @assignment.employee
          emp_user_id = []
          unless emp.blank?
            emp_user_id << emp.user_id
            Delayed::Job.enqueue(
              DelayedReminderJob.new( :sender_id  => current_user.id,
                :recipient_ids => emp_user_id,
                :subject=>"Your homework : #{@assignment.title} is Denied",
                :rtype=>4,
                :rid=>@assignment.id,
                :student_id => 0,
                :batch_id => 0,
                :body=>"Your homework '#{@assignment.title}' for  #{@subject.name} is Denied.<br/>#{t('view_reports_homework')}")
            )
          end
      flash[:notice] = "Homerok successfully Denied"
      redirect_to :action=>"show_publisher",:id=>@assignment.id
    end
  end
  
  def publisher_homework
    now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    @assignment = Assignment.find_by_id(params[:id])
    previous_status = @assignment.is_published
    @assignment.is_published = 3
    
    if @assignment.save
        @subject = Subject.find_by_id(@assignment.subject_id)
        emp = @assignment.employee
        emp_user_id = []
        unless emp.blank?
          emp_user_id << emp.user_id
          Delayed::Job.enqueue(
            DelayedReminderJob.new( :sender_id  => current_user.id,
              :recipient_ids => emp_user_id,
              :subject=>"Your homework : #{@assignment.title} is approved",
              :rtype=>4,
              :rid=>@assignment.id,
              :student_id => 0,
              :batch_id => 0,
              :body=>"Your homework '#{@assignment.title}' for  #{@subject.name} is approved now <br/>#{t('view_reports_homework')}")
          )
        end
   
      flash[:notice] = "Homerok successfully Approved"
      redirect_to :action=>"show_publisher",:id=>@assignment.id
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
        filename = @assignment.attachment_file_name
        send_file  @assignment.attachment.path , :type=>@assignment.attachment.content_type,:filename => filename
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
  
  # RR sept 20
  def defaulter_registration
    
    
    @assignment = Assignment.find_by_id(params[:id])
    #abort(@assignment.inspect)
    @assigned_students = []
    @assignment.student_list.split(",").each do |s|
      student = Student.find_by_id s
      if student.nil?
        student=ArchivedStudent.find_by_former_id s
      end
      @assigned_students << student
    end
    
    @defaulter = AssignmentDefaulterList.find_all_by_assignment_id(@assignment.id)
    @defaulter_registered = AssignmentDefaulterRegistration.find_by_assignment_id(@assignment.id)
    @defaulter_students = []
    
    unless @defaulter.nil? 
      @defaulter.each do |s|
        @defaulter_students << s.student_id
      end
    end
    
    #abort(@assignment.inspect)
   
    
    if request.post?
      #abort("thats a post request")
      student_ids = []
      unless params[:assignment].blank?
        student_ids = params[:assignment][:student_ids]
      end
      registration_checked = params[:isRegistered]
      #abort(params.inspect)
      if !@defaulter_registered.blank? or !registration_checked.blank?
        assignment_given = @assigned_students.count
        if !student_ids.blank?
          assignment_given -= student_ids.count
        end
        assignment_not_given = @assigned_students.count - assignment_given
        
        if @defaulter_registered.blank?
          #RR, create new row
          row = AssignmentDefaulterRegistration.new(:employee_id => @assignment.employee_id, :assignment_id => @assignment.id, :assignment_given => assignment_given, :assignment_not_given => assignment_not_given)
          row.save
        else
          
          @defaulter_registered.update_attributes(:assignment_given => assignment_given, :assignment_not_given => assignment_not_given)
        end

        # remove all by assignment id
        prev_students_ids = []
        rows = AssignmentDefaulterList.find_all_by_assignment_id(@assignment.id)
        rows.each do |row|
          prev_students_ids << row.student_id
          row.destroy
        end
         
        available_user_ids = []
        batch_ids = {}
        std_ids = {}
        
        available_user_ids2 = []
        batch_ids2 = {}
        student_ids2 = {}
        
        #insert checked in checkbox
        @mobile_number_sms = []
        unless student_ids.blank?
          student_ids.each do |s|
            row = AssignmentDefaulterList.new(:assignment_id => @assignment.id, :student_id => s)
            row.save
            unless prev_students_ids.include?(s)
              @student = Student.find(s)
              available_user_ids << @student.user_id
              batch_ids[@student.user_id] = @student.batch_id
              std_ids[@student.user_id] = @student.id
              
              immediate_contact_guardian = @student.immediate_contact
              unless immediate_contact_guardian.nil?
                @mobile_number_sms.push immediate_contact_guardian.mobile_phone unless (immediate_contact_guardian.mobile_phone.nil? or immediate_contact_guardian.mobile_phone == "") 
              end
              
              unless @student.student_guardian.empty?
                guardians = @student.student_guardian
                guardians.each do |guardian|
                  #            guardian = Guardian.find(@student.immediate_contact_id)

                  unless guardian.user_id.nil?
                    available_user_ids2 << guardian.user_id
                    batch_ids2[guardian.user_id] = @student.batch_id
                    student_ids2[guardian.user_id] = @student.id
                  end
                end  
              end
            end
          end
          
          if !@mobile_number_sms.blank? and MultiSchool.current_school.id == 312
            message = "Your child did not submit the homework '#{@assignment.title}' for '#{@assignment.subject.name}'"
            Delayed::Job.enqueue(SmsManager.new(message,@mobile_number_sms))
          end
          
          unless available_user_ids.blank?
            Delayed::Job.enqueue(
              DelayedReminderJob.new( :sender_id  => current_user.id,
                :recipient_ids => available_user_ids,
                :subject=>'Homework Defaulter : '+@assignment.title,
                :rtype=>4,
                :rid=>@assignment.id,
                :student_id => std_ids,
                :batch_id => batch_ids,
                :body=>"You did not submit the homework '#{@assignment.title}' for '#{@assignment.subject.name}' <br/>#{t('view_reports_homework')}")
            )
          end
          
          unless available_user_ids2.blank?
            Delayed::Job.enqueue(
              DelayedReminderJob.new( :sender_id  => current_user.id,
                :recipient_ids => available_user_ids2,
                :subject=>'Homework Defaulter : '+@assignment.title,
                :rtype=>4,
                :rid=>@assignment.id,
                :student_id => student_ids2,
                :batch_id => batch_ids2,
                :body=>"Your child did not submit the homework '#{@assignment.title}' for '#{@assignment.subject.name}' <br/>#{t('view_reports_homework')}")
            )
          end
          
        end
         
        redirect_to :controller=>:assignments,:action=>:show
      end
    end
    
  end
  
  
  def defaulter_students
    @assignment  = Assignment.active.find(params[:id], :include => [:employee])
    unless @assignment.nil?
      #RR assignment defaulter added
      defaulter_student_rows = AssignmentDefaulterList.find_all_by_assignment_id(@assignment.id)
      @defaulter_students = []
      unless defaulter_student_rows.blank?
        defaulter_student_rows.each do |s|
          std = Student.find_by_id(s.student_id)
          if std.nil?
            std=ArchivedStudent.find_by_former_id s.student_id
          end
          @defaulter_students << std
        end
      end
    end
  end
  
  def only_publisher_admin_allowed
    @config = Configuration.find_by_config_key('HomeworkWillForwardOnly')
    
    if (!@config.blank? and !@config.config_value.blank? and @config.config_value.to_i == 1)
      if @current_user.employee?
        employee= @current_user.employee_record
        if employee.homework_publisher.to_i == 1
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
    else
      flash[:notice] = "#{t('flash_msg4')}"
      redirect_to :controller => 'user', :action => 'dashboard'
    end  
  end
end
