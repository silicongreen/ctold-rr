class AssignmentsController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only=>[:index]
  filter_access_to :all,:except=>[:show]
  filter_access_to :show
  before_filter :default_time_zone_present_time
  before_filter :only_publisher_admin_allowed , :only=>[:publisher,:show_publisher,:get_homework_filter_publisher,:showsubjects_publisher,:publisher_homework,:deny_homework,:subject_assignments_publisher]
  
  def get_homework_filter
    @ba_id = batch_id = params[:batch_name]
    @class_id = student_class_name = params[:student_class_name]
    @std_id = student_section = params[:student_section]
    @pub_date_string = assignment_publish_date = params[:assignment_publish_date]
    @assignments = []
    unless batch_id.nil?
      batchdata = Batch.find_by_id(batch_id)
      unless batchdata.blank?
        batch_name = batchdata.name
        if student_class_name.blank?
          if assignment_publish_date.blank?
            @assignments =Assignment.paginate  :conditions=>"batches.name = '#{batch_name}'  and is_published=1 ",:order=>"duedate desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}]     
          else
            @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
            @assignments =Assignment.paginate  :conditions=>"batches.name = '#{batch_name}' and ( (DATE(DATE_ADD(assignments.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( ( (DATE(DATE_ADD(assignments.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(assignments.created_at) = '#{@pub_date}' and content not like '%</%' ) ) and content not like '%</%' ) ) and is_published=1 ",:order=>"duedate desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}]  
          end   
       elsif student_section.blank?
         if assignment_publish_date.blank?
            @assignments =Assignment.paginate  :conditions=>"batches.name = '#{batch_name}' and courses.course_name = '#{student_class_name}'  and is_published=1 ",:order=>"duedate desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[{:batch=>[:course]}]}] 
         else
            @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
            @assignments =Assignment.paginate  :conditions=>"batches.name = '#{batch_name}' and ( (DATE(DATE_ADD(assignments.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(assignments.created_at) = '#{@pub_date}' and content not like '%</%' ) ) and courses.course_name = '#{student_class_name}'  and is_published=1 ",:order=>"duedate desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[{:batch=>[:course]}]}]
         end   
       else
          batch = Batch.find_by_course_id_and_name(student_section, batch_name)
          unless batch.blank?
            if assignment_publish_date.blank?
              @assignments =Assignment.paginate  :conditions=>"batches.id = '#{batch.id}'  and is_published=1 ",:order=>"duedate desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
            else
              @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
              @batch_id_main = batch.id
              @assignments =Assignment.paginate  :conditions=>"batches.id = '#{batch.id}'  and is_published=1 and ( (DATE(DATE_ADD(assignments.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(assignments.created_at) = '#{@pub_date}' and content not like '%</%' ) )",:order=>"duedate desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
            end
          end
        end  
      end
    else
      @assignments =Assignment.paginate  :conditions=>"is_published=1",:order=>"duedate desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}]
    end  
    respond_to do |format|
      format.js { render :action => 'get_homework_filter' }
    end
   
  end
  
  def download_pdf_publush_date
    batch_id = params[:batch_name]
    student_class_name = @class_name =  params[:student_class_name]
    student_section  = @section_name = params[:student_section]
    assignment_publish_date = params[:assignment_publish_date]
    @assignments = []
    unless batch_id.nil?
      batchdata = @batch = Batch.find_by_id(batch_id)
      unless batchdata.blank?
        batch_name = batchdata.name
        if student_class_name.blank?
          if assignment_publish_date.blank?
            @assignments =Assignment.find(:all,:conditions=>"batches.name = '#{batch_name}'  and is_published=1 and assignment_type = 2",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}])     
          else
            @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
            @assignments =Assignment.find(:all,:conditions=>"batches.name = '#{batch_name}' and DATE(assignments.duedate) = '#{@pub_date}' and is_published=1 and assignment_type = 2",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}]) 
          end   
       elsif student_section.blank?
         if assignment_publish_date.blank?
            @assignments =Assignment.find(:all,:conditions=>"batches.name = '#{batch_name}' and courses.course_name = '#{student_class_name}'  and is_published=1 and assignment_type = 2",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}] )
         else
            @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
            @assignments =Assignment.find(:all,:conditions=>"batches.name = '#{batch_name}' and DATE(assignments.duedate) = '#{@pub_date}' and courses.course_name = '#{student_class_name}'  and is_published=1 and assignment_type = 2",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}])
         end   
       else
          batch = Batch.find_by_course_id_and_name(student_section, batch_name)
          unless batch.blank?
            if assignment_publish_date.blank?
              @assignments =Assignment.find(:all,:conditions=>"batches.id = '#{batch.id}'  and is_published=1 and assignment_type = 2",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}] )
            else
              @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
              @batch_id_main = batch.id
              @assignments =Assignment.find(:all,:conditions=>"batches.id = '#{batch.id}'  and is_published=1 and DATE(assignments.duedate) = '#{@pub_date}' and assignment_type = 2",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}] )
            end
          end
        end  
      end
    else
      @assignments =Assignment.find(:all,:conditions=>"is_published=1 and assignment_type = 2",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}])
    end 
    @asignment_ids = []
    @asignment_ids = @assignments.map(&:id).uniq unless @assignments.blank?
    
    unless @asignment_ids.blank?
      @assignment_submits = AssignmentAnswer.find(:all,:select=>"count( distinct student_id) as total, assignment_id",:group=>"assignment_id",:conditions=>["assignment_id in (?)",@asignment_ids])
    end
    
    render :pdf => 'download_pdf_publush_date',
        :orientation => 'Portrait', :zoom => 1.00,
        :page_size => 'A4',
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  
  
  def download_pdf
    batch_id = params[:batch_name]
    student_class_name = @class_name =  params[:student_class_name]
    student_section  = @section_name = params[:student_section]
    assignment_publish_date = params[:assignment_publish_date]
    @assignments = []
    unless batch_id.nil?
      batchdata = @batch = Batch.find_by_id(batch_id)
      unless batchdata.blank?
        
        batch_name = batchdata.name
        if student_class_name.blank?
          
          
          if assignment_publish_date.blank?
            get_total_class(false,0,batch_name,false)
            @assignments =Assignment.find(:all,:conditions=>"batches.name = '#{batch_name}'  and is_published=1 ",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}])     
          else
            @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
            get_total_class(@pub_date,0,batch_name,false)
            @assignments =Assignment.find(:all,:conditions=>"batches.name = '#{batch_name}' and ( (DATE(DATE_ADD(assignments.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(assignments.created_at) = '#{@pub_date}' and content not like '%</%' ) ) and is_published=1 ",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}]) 
          end   
       elsif student_section.blank?
         if assignment_publish_date.blank?
            get_total_class(false,0,batch_name,student_class_name)
            @assignments =Assignment.find(:all,:conditions=>"batches.name = '#{batch_name}' and courses.course_name = '#{student_class_name}'  and is_published=1 ",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}] )
         else
            @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
            get_total_class(@pub_date,0,batch_name,student_class_name)
            @assignments =Assignment.find(:all,:conditions=>"batches.name = '#{batch_name}' and ( (DATE(DATE_ADD(assignments.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(assignments.created_at) = '#{@pub_date}' and content not like '%</%' ) ) and courses.course_name = '#{student_class_name}'  and is_published=1 ",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}])
         end   
       else
          batch = Batch.find_by_course_id_and_name(student_section, batch_name)
          unless batch.blank?
            if assignment_publish_date.blank?
              get_total_class(false,batch.id,false,false)
              @assignments =Assignment.find(:all,:conditions=>"batches.id = '#{batch.id}'  and is_published=1 ",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}] )
            else
              @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
              get_total_class(@pub_date,batch.id,false,false)
              @batch_id_main = batch.id
              @assignments =Assignment.find(:all,:conditions=>"batches.id = '#{batch.id}'  and is_published=1 and ( (DATE(DATE_ADD(assignments.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(assignments.created_at) = '#{@pub_date}' and content not like '%</%' ) )",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}] )
            end
          end
        end  
      end
    else
      @assignments =Assignment.find(:all,:conditions=>"is_published=1",:order=>"courses.priority asc",:include=>[{:subject=>[{:batch=>[:course]}]}])
    end 
    
    @report_data = []
    if !@routine_response.blank? and @routine_response['status']['code'].to_i == 200
      @report_data = @routine_response['data']
    end
    @employee_ids = []
    @employee_ids = @assignments.map(&:employee_id).uniq unless @assignments.blank?
    
    render :pdf => 'download_pdf',
        :orientation => 'Portrait', :zoom => 1.00,
        :page_size => 'A4',
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def download_excell
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'homework_report'
    row_first = ['SL','Class','Group','Assignment Type','Subject','Title','Teacher','Due Date','Assign Date']
    new_book.worksheet(0).insert_row(0, row_first)
    
    batch_id = params[:batch_name]
    student_class_name = params[:student_class_name]
    student_section = params[:student_section]
    assignment_publish_date = params[:assignment_publish_date]
    @assignments = []
    unless batch_id.nil?
      batchdata = Batch.find_by_id(batch_id)
      unless batchdata.blank?
        batch_name = batchdata.name
        if student_class_name.blank?
          if assignment_publish_date.blank?
            @assignments =Assignment.find(:all,:conditions=>"batches.name = '#{batch_name}'  and is_published=1 ",:order=>"duedate desc",:include=>[{:subject=>[:batch]}])     
          else
            @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
            @assignments =Assignment.find(:all,:conditions=>"batches.name = '#{batch_name}' and ( (DATE(DATE_ADD(assignments.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(assignments.created_at) = '#{@pub_date}' and content not like '%</%' ) ) and is_published=1 ",:order=>"duedate desc",:include=>[{:subject=>[:batch]}]) 
          end   
       elsif student_section.blank?
         if assignment_publish_date.blank?
            @assignments =Assignment.find(:all,:conditions=>"batches.name = '#{batch_name}' and courses.course_name = '#{student_class_name}'  and is_published=1 ",:order=>"duedate desc",:include=>[{:subject=>[{:batch=>[:course]}]}] )
         else
            @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
            @assignments =Assignment.find(:all,:conditions=>"batches.name = '#{batch_name}' and ( (DATE(DATE_ADD(assignments.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(assignments.created_at) = '#{@pub_date}' and content not like '%</%' ) ) and courses.course_name = '#{student_class_name}'  and is_published=1 ",:order=>"duedate desc",:include=>[{:subject=>[{:batch=>[:course]}]}])
         end   
       else
          batch = Batch.find_by_course_id_and_name(student_section, batch_name)
          unless batch.blank?
            if assignment_publish_date.blank?
              @assignments =Assignment.find(:all,:conditions=>"batches.id = '#{batch.id}'  and is_published=1 ",:order=>"duedate desc",:include=>[{:subject=>[:batch]}] )
            else
              @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
              @batch_id_main = batch.id
              @assignments =Assignment.find(:all,:conditions=>"batches.id = '#{batch.id}'  and is_published=1 and ( (DATE(DATE_ADD(assignments.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(assignments.created_at) = '#{@pub_date}' and content not like '%</%' ) )",:order=>"duedate desc",:include=>[{:subject=>[:batch]}] )
            end
          end
        end  
      end
    else
      @assignments =Assignment.find(:all,:conditions=>"is_published=1",:order=>"duedate desc",:include=>[{:subject=>[:batch]}])
    end 
    
    iloop = 0
    unless @assignments.blank?
      @assignments.each_with_index do |assignment,i|
        iloop = iloop+1
        tmp_row = []
        tmp_row << iloop
        tmp_row << assignment.subject.batch.full_name
        tmp_row << assignment.subject.batch.course.group
        if assignment.assignment_type == 1
          tmp_row << "Homework"
        else
          tmp_row << "Assignment"
        end  
        tmp_row << assignment.subject.name
        tmp_row << assignment.title
        unless assignment.employee.blank?
          tmp_row << assignment.employee.full_name
        else
          tmp_row << ""
        end
        tmp_row << I18n.l(assignment.duedate,:format=>"%d-%m-%Y")
        tmp_row << I18n.l(assignment.created_at,:format=>"%d-%m-%Y")
        new_book.worksheet(0).insert_row(iloop, tmp_row)
      end
    end
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => "homework_report.xls", :type =>  "application/vnd.ms-excel"
    
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
  def use_homework
    @subjects = current_user.employee_record.subjects.active
    @subjects.reject! {|s| !s.batch.is_active}
    subject_class = []
    subject_id = []
    unless @subjects.blank?
      @subjects.each do |subject|
        subject_class << subject.name+"-"+subject.batch.course.course_name
        subject_id << subject.id
      end  
    end
    is_publish = [1,3]
    @main_assignment = Assignment.find(:first,:select=>"assignments.*,courses.course_name as course_name_asignment,subjects.name as subject_name_asignment",:conditions=>["CONCAT(subjects.name,'-',courses.course_name) IN (?) and assignments.is_published IN (?) and assignments.id = ?",subject_class,is_publish,params[:id]],:joins=>[{:subject=>{:batch=>[:course]}}])
    unless @main_assignment.blank?
      @assignment= Assignment.new
      @subjects_main = Subject.find(:all,:conditions=>["subjects.name = ? and courses.course_name = ? and subjects.is_deleted = ? and subjects.id IN (?)",@main_assignment.subject_name_asignment,@main_assignment.course_name_asignment,false,subject_id],:joins=>[{:batch=>[:course]}])
    else
      flash[:notice]="You are not allowed to use this homework"
      redirect_to :controller=>:user ,:action=>:dashboard
    end  
  
  end
  
  
  def create_use_homwork
    @main_assignment = Assignment.find(params[:used_id])
    student_ids = params[:assignment][:student_ids]
    params[:assignment].delete(:student_ids)
    @subject = Subject.find_by_id(params[:assignment][:subject_id])
    @assignment = Assignment.new(params[:assignment])
    @assignment.title = @main_assignment.title
    @assignment.content = @main_assignment.content
    @assignment.assignment_type = @main_assignment.assignment_type
    @assignment.attachment_file_name = @main_assignment.attachment_file_name
    @assignment.student_list = student_ids.join(",") unless student_ids.nil?
    @assignment.employee = current_user.employee_record
    @assignment.used_id = @main_assignment.id
    @assignment.is_published = 1
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
              :subject=>"#{t('new_homework_added')} : "+@assignment.title,
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
        render :action=>:use_homework,:id=>params[:used_id]
      end
    else
      unless @assignment.save
        @subjects = current_user.employee_record.subjects
        render :action=>:use_homework,:id=>params[:used_id]
      end
    end
  end
  
  
  def approved_homework    
    @subjects_main = current_user.employee_record.subjects.active
    @subjects_main.reject! {|s| !s.batch.is_active}
    
    @subjects = []
    subject_class = []
    unless @subjects_main.blank?
      @subjects_main.each do |subject|
        unless subject_class.include?(subject.name+"-"+subject.batch.course.course_name)
           @subjects << subject
           subject_class << subject.name+"-"+subject.batch.course.course_name
        end
        
      end  
    end
   
    @assignments = []
    is_publish = [1,3]
    unless subject_class.blank?
      @assignments = Assignment.paginate :select=>"assignments.*",:conditions=>["CONCAT(subjects.name,'-',courses.course_name) IN (?) and assignments.is_published IN (?) and (assignments.used_id = ? or assignments.used_id is NULL)",subject_class,is_publish,false],:joins=>[{:subject=>{:batch=>[:course]}}],:order=>"assignments.created_at desc", :page=>params[:page], :per_page => 10
    end
  end
  def subject_assignments_approved
    @due_date = params[:due_date]
    @publish_date= params[:publish_date]
    @subject = Subject.find_by_id params[:subject_id]
    @subjects = current_user.employee_record.subjects.active
    @subjects.reject! {|s| !s.batch.is_active}
    subject_class = []
    unless @subjects.blank?
      @subjects.each do |subject|
        subject_class << "'"+subject.name+"-"+subject.batch.course.course_name+"'"
      end  
    end
    @assignments = []
    is_publish = [1,3]
    conditions = "CONCAT(subjects.name,'-',courses.course_name) IN ("+subject_class.join(",")+") and assignments.is_published IN (#{is_publish.join(',')}) and (assignments.used_id = 0 or assignments.used_id is NULL)"
    unless  @publish_date.blank?
      @publish_date = @publish_date.to_datetime.strftime("%Y-%m-%d")
      conditions+=" and DATE(assignments.created_at) = '#{@publish_date}'"
    end
    unless  @due_date.blank?
      @due_date = @due_date.to_datetime.strftime("%Y-%m-%d")
      conditions+=" and DATE(assignments.duedate) = '#{@due_date}'"
    end
    unless  @subject.blank?
      conditions+=" and subjects.name = '#{@subject.name}'"
    end
    @assignments = Assignment.paginate :conditions=>conditions,:order=>"duedate desc",:joins=>[{:subject=>{:batch=>[:course]}}],:order=>"assignments.created_at desc", :page=>params[:page]
    
    
    render(:update) do |page|
      page.replace_html 'subject_assignments_list', :partial=>'approved_homework'
    end
  end
  
  def index
    @current_user = current_user
    if @current_user.employee?
      emp_record = current_user.employee_record 
      @subjects = emp_record.subjects.active
      @subjects.reject! {|s| !s.batch.is_active}
      if emp_record.all_access.to_i == 1
        batches = @current_user.employee_record.batches
        #batches += @current_user.employee_record.subjects.collect{|b| b.batch}
        batches = batches.uniq unless batches.empty?
        unless batches.blank?
          batches.each do |batch|
            @subjects += batch.subjects
          end
        end
      end
      @subjects = @subjects.uniq unless @subjects.empty?
      @subjects.sort_by{|s| s.batch.course.code.to_i}
       
    elsif @current_user.student?
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
      if @current_user.employee?
        emp_record = current_user.employee_record 
        @subject_employees = emp_record.subjects.active
        @subject_employees.reject! {|s| !s.batch.is_active}
        if emp_record.all_access.to_i == 1
          batches = @current_user.employee_record.batches
          #batches += @current_user.employee_record.subjects.collect{|b| b.batch}
          batches = batches.uniq unless batches.empty?
          unless batches.blank?
            batches.each do |batch|
              @subject_employees += batch.subjects
            end
          end
        end
        @subject_employees = @subject_employees.uniq unless @subject_employees.empty?
        sub_id = @subject_employees.map{|b| b.id}
        @normal_subjects = Subject.find_all_by_batch_id(@batch,:conditions=>["elective_group_id IS NULL AND is_deleted = false and id IN (?)",sub_id])
        @student_electives =StudentsSubject.all(:conditions=>{:batch_id=>@batch,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
        @elective_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>["elective_group_id IS NOT NULL AND is_deleted = false and id IN (?)",sub_id])
        @subjects = @normal_subjects+@elective_subjects
        @subjects = @subjects.uniq unless @subjects.empty?
        
      else
        @normal_subjects = Subject.find_all_by_batch_id(@batch,:conditions=>"elective_group_id IS NULL AND is_deleted = false")
        @student_electives =StudentsSubject.all(:conditions=>{:batch_id=>@batch,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
        @elective_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>["elective_group_id IS NOT NULL AND is_deleted = false"])
        @subjects = @normal_subjects+@elective_subjects
        @subjects = @subjects.uniq unless @subjects.empty?
      end
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
    
    if current_user.employee_record.all_access.to_i == 1
      emp_record = current_user.employee_record 
      @subjects = emp_record.subjects.active
      @subjects.reject! {|s| !s.batch.is_active}
      if emp_record.all_access.to_i == 1
        batches = @current_user.employee_record.batches
        #batches += @current_user.employee_record.subjects.collect{|b| b.batch}
        batches = batches.uniq unless batches.empty?
        unless batches.blank?
          batches.each do |batch|
            @subjects += batch.subjects
          end
        end
      end
      @subjects = @subjects.uniq unless @subjects.empty?
      @subjects.sort_by{|s| s.batch.course.code.to_i}
      sub_id = @subjects.map(&:id)
      
      if !@subject.nil? and !@due_date.blank? 
        @due_date = @due_date.to_datetime.strftime("%Y-%m-%d")
        @assignments = Assignment.paginate :conditions=>["subject_id=#{@subject.id} and subject_id in (?) and DATE(duedate) = '#{@due_date}' #{publish_condition}",sub_id],:order=>"duedate desc", :page=>params[:page]
      elsif @subject.nil? and !@due_date.blank?
        @due_date = @due_date.to_datetime.strftime("%Y-%m-%d")
        @assignments = Assignment.paginate :conditions=>["DATE(duedate) = '#{@due_date}' and subject_id in (?) #{publish_condition}",sub_id],:order=>"duedate desc", :page=>params[:page]
      elsif !@subject.nil? and @due_date.blank?
        @assignments = Assignment.paginate :conditions=>["subject_id=#{@subject.id} and subject_id in (?) #{publish_condition}",sub_id],:order=>"duedate desc", :page=>params[:page]
      else
        @assignments = Assignment.paginate :conditions=>["subject_id in (?) #{publish_condition} ",sub_id],:order=>"duedate desc", :page=>params[:page]
      end
    else
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
      emp_record = current_user.employee_record 
      @subjects = emp_record.subjects.active
      @subjects.reject! {|s| !s.batch.is_active}
      if emp_record.all_access.to_i == 1
        batches = @current_user.employee_record.batches
        #batches += @current_user.employee_record.subjects.collect{|b| b.batch}
        batches = batches.uniq unless batches.empty?
        unless batches.blank?
          batches.each do |batch|
            @subjects += batch.subjects
          end
        end
      end
      @subjects = @subjects.uniq unless @subjects.empty?
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
          @students.reject!{|e| e.is_deleted == true}
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
            unless s.nil?
              @students << s
              @assigned_students << s
            end
          end
          @students.reject!{|e| e.batch_id!=@subject.batch_id}
        end
        @students.reject!{|e| e.is_deleted == true}
      end

      
    end
  end


  def subjects_students_list
    
    @subject = Subject.find_all_by_id(params[:subject_id].split(","))
    unless @subject.nil?
      @students = []
      @subject.each do |sub|
        if sub.elective_group_id.nil?
          students_all = Student.find_all_by_batch_id(sub.batch_id)
          students_all.each do |std|
            @students << std
          end
        else
          assigned_students = StudentsSubject.find_all_by_subject_id_and_batch_id(sub.id,sub.batch_id)
          unless assigned_students.blank?
            assigned_students.each do |std|
              unless std.student.blank?
                unless std.student.batch_id.blank?
                  if sub.batch_id == std.student.batch_id
                    @students << std.student
                  end
                end
              end 
            end
          end
          
        end
      end
      @students=@students.compact
      @students.reject!{|e| e.is_deleted == true}
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
        @students = @students.uniq unless @students.blank?
      elsif @status == "answered"
        @answers = @assignment.assignment_answers
        #        @answers  = @answers.sort_by{|a| a.updated_at}
        @answers  = @answers.sort_by{|a| (a && a.created_at(:length)) || 0}
        @students = @answers.map{|a| a.student }
        @students = @students.uniq unless @students.blank?
      elsif @status== "comments"  
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
        @students = @students.uniq unless @students.blank?
        
        @ass_comments = AssignmentComment.find_all_by_assignment_id(@assignment.id,:select=>"student_id,created_at",:order=>"created_at Desc")
        @students_comments=[]
        unless @ass_comments.blank?
          @ass_comments.each do |ass_comment|
            s = Student.find_by_id ass_comment.student_id
            if s.nil?
              s = ArchivedStudent.find_by_former_id ass_comment.student_id
            end
            @students_comments << s if s.present?
          end
        end
        @students_comments = @students_comments.uniq unless @students_comments.blank?
        
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
        @students = @students.uniq unless @students.blank?
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
    subject = params[:assignment][:subject_id]
    if !subject.kind_of?(Array)
      @subjects = []
      @subjects << subject
    else
      @subjects = subject
    end  
    save = false
    if !@subjects.blank? && !student_ids.blank?
      @all_students = Student.find_all_by_id(student_ids)
      @subjects.each do |sub_id|
        params[:assignment][:subject_id] = sub_id
        @subject = Subject.find_by_id(sub_id)
        
        @assignment = Assignment.new(params[:assignment])
        
       
        all_std = []
        @all_students.each do |std|
          if @subject.batch_id == std.batch_id
            all_std <<  std.id
          end
        end
        
        @assignment.student_list = all_std.join(",") unless all_std.nil?
        @assignment.employee = current_user.employee_record
        
        @config = Configuration.find_by_config_key('HomeworkWillForwardOnly')
        if (!@config.blank? and !@config.config_value.blank? and @config.config_value.to_i == 1) and !@current_user.admin? and @current_user.employee_entry.homework_publisher != 1 and @assignment.is_published != 0        
          @assignment.is_published = 2
        end
        
        students = Student.find_all_by_id(all_std)
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
              @config_notification = Configuration.find_by_config_key('AllNotificationAdmin')
              if (!@config_notification.blank? and !@config_notification.config_value.blank? and @config_notification.config_value.to_i == 1)
                all_admin = User.find_all_by_admin_and_is_deleted(true,false)
                available_user_ids = all_admin.map(&:id)
                batch = @assignment.subject.batch
                unless batch.blank?
                  batch_tutor = batch.employees
                  unless batch_tutor.blank?
                    batch_tutor.each do |employee|
                      if employee.all_access.to_i == 1
                        available_user_ids << employee.user_id
                      end
                    end
                  end
                end
                unless available_user_ids.blank?
                  Delayed::Job.enqueue(
                    DelayedReminderJob.new( :sender_id  => current_user.id,
                      :recipient_ids => available_user_ids,
                      :subject=>"New homework '#{@assignment.title}' added for #{@subject.name}( Class: #{@subject.batch.course.course_name} #{@subject.batch.course.section_name}) by #{@assignment.employee.full_name}",
                      :rtype=>4,
                      :rid=>@assignment.id,
                      :student_id => 0,
                      :batch_id => 0,
                      :body=>"New homework '#{@assignment.title}' added for #{@subject.name}( Class: #{@subject.batch.course.course_name} #{@subject.batch.course.section_name}) by #{@assignment.employee.full_name}. <br/>#{t('view_reports_homework')}")
                  )
                end
                
              end  
              
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
            save = true
            
          else
            if current_user.employee?
              @subjects = current_user.employee_record.subjects
              
              @subjects.reject! {|s| !s.batch.is_active?}
              @students = @subject.batch.students
            end
            
          end
        else
          unless @assignment.save
            @subjects = current_user.employee_record.subjects
          end
        end
      end
      if save
        redirect_to :action=>:index
      else
        render :action=>:new
      end  
    else
      render :update do |page|
        page << 'alert("invalid Request, No Student Selected");'
      end
    end
  end

  def show
    @assignment  = Assignment.active.find_by_id(params[:id], :include => [:employee])
    unless @assignment.nil?
      
      #RR assignment defaulter added
      @defaulter_registered = AssignmentDefaulterRegistration.find_by_assignment_id(@assignment.id)
      @current_user = current_user
      
      
      @ass_comments = AssignmentComment.find_all_by_assignment_id(@assignment.id,:select=>"student_id",:order=>"created_at Desc")
      @students_comments=[]
      unless @ass_comments.blank?
        @ass_comments.each do |ass_comment|
          s = Student.find_by_id ass_comment.student_id
          if s.nil?
            s = ArchivedStudent.find_by_former_id ass_comment.student_id
          end
          @students_comments << s if s.present?
        end
      end
      @students_comments = @students_comments.uniq unless @students_comments.blank?
      @comments_count = @students_comments.count
    
      assigned_students= @assignment.student_list.split(",")
      @students=[]
      assigned_students.each do |assigned_student|
        s = Student.find_by_id assigned_student
        if s.nil?
          s = ArchivedStudent.find_by_former_id assigned_student
        end
        @students << s if s.present?
      end
      @students = @students.uniq unless @students.blank?
      @students_assigned_count = @students.count

      @answers = @assignment.assignment_answers
      #        @answers  = @answers.sort_by{|a| a.updated_at}
      @answers  = @answers.sort_by{|a| (a && a.created_at(:length)) || 0}
      @students = @answers.map{|a| a.student }
      @students = @students.uniq unless @students.blank?
      @answered_count = @students.count
      
      
      
      
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
      @students = @students.uniq unless @students.blank?
      @pending_count = @students.count
    
      @assignment_answers = AssignmentAnswer.find_all_by_student_id_and_assignment_id(current_user.student_record.id,@assignment.id) if current_user.student?
      
      @subject =Subject.find_by_id @assignment.subject_id
      student=current_user.student_record
      
      unless @subject.nil?
        @assignments_list =Assignment.paginate  :conditions=>"subject_id=#{@subject.id} and is_published=1 and FIND_IN_SET(#{student.id},student_list)",:order=>"duedate desc", :page=>params[:page]
      else
        @assignments_list =Assignment.paginate  :conditions=>"FIND_IN_SET(#{student.id},student_list) and is_published=1",:order=>"duedate desc", :page=>params[:page]
      end
      
      if current_user.student?
        show_comments_associate(@assignment.id, current_user.student_record.id)
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
    updated_at = @assignment.updated_at
    unless @assignment.nil?
      student_ids = params[:assignment][:student_ids]
      params[:assignment].delete(:student_ids)
      @assignment.student_list = student_ids.join(",") unless student_ids.nil?
      Assignment.record_timestamps=false
      if  @assignment.update_attributes(params[:assignment])
        flash[:notice]="#{t('assignment_details_updated')}"
        @config = Configuration.find_by_config_key('HomeworkWillForwardOnly')
        #@assignment.update_attribute("updated_at",updated_at)
        Assignment.record_timestamps=true
        
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
        @config_notification = Configuration.find_by_config_key('AllNotificationAdmin')
        if (!@config_notification.blank? and !@config_notification.config_value.blank? and @config_notification.config_value.to_i == 1)
          all_admin = User.find_all_by_admin_and_is_deleted(true,false)
          available_user_ids = all_admin.map(&:id)
          batch = @assignment.subject.batch
          unless batch.blank?
            batch_tutor = batch.employees
            unless batch_tutor.blank?
              batch_tutor.each do |employee|
                if employee.all_access.to_i == 1
                  available_user_ids << employee.user_id
                end
              end
            end
          end
          unless available_user_ids.blank?
              Delayed::Job.enqueue(
                DelayedReminderJob.new( :sender_id  => current_user.id,
                  :recipient_ids => available_user_ids,
                  :subject=>"New homework '#{@assignment.title}' added for #{@subject.name}( Class: #{@subject.batch.course.course_name} #{@subject.batch.course.section_name}) by #{@assignment.employee.full_name}",
                  :rtype=>4,
                  :rid=>@assignment.id,
                  :student_id => 0,
                  :batch_id => 0,
                  :body=>"New homework '#{@assignment.title}' added for #{@subject.name}( Class: #{@subject.batch.course.course_name} #{@subject.batch.course.section_name}) by #{@assignment.employee.full_name}. <br/>#{t('view_reports_homework')}")
              )
            end

        end 
      
        
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
  
  def remove_attachment
    @assignment =Assignment.find_by_id(params[:id])
    unless @assignment.blank?
      @assignment.attachment.destroy
      @assignment.save
    end  
    flash[:notice] = "Attachment Successfully Removed"
    redirect_to :action=>"show",:id=>@assignment.id
  end
  
  def assignment_all_attchment_download
    require 'zip/zipfilesystem'
    @assignment =Assignment.active.find params[:id]
    unless @assignment.blank?
      batch_id = @assignment.subject.batch_id
      batch = Batch.find_by_id(batch_id)
      zip_name = batch.course.course_name+" "+batch.course.section_name+" "+@assignment.title+".zip"
      answers = AssignmentAnswer.find_all_by_assignment_id(@assignment.id)
      rails_tmp_path = File.join(RAILS_ROOT, "/tmp/")
      tmp_zip_path = File.join(rails_tmp_path, "assignmnet_attachments.zip")
      File.delete(tmp_zip_path) if File.exist?(tmp_zip_path)
      unless assignments.blank?
        Zip::ZipFile.open(tmp_zip_path,Zip::ZipFile::CREATE) do |zipfile|
          answers.each do |answer|
            unless answer.attachment_file_name.blank?
              spilt_file_name = answer.attachment_file_name.split(".")
              total_count = spilt_file_name.count-1
              if total_count > 0
                file_extenstion = spilt_file_name[total_count]
                student = answer.student
                img_name = student.admission_no+"-"+student.full_name+"-1."+file_extenstion
                if File.exists? answer.attachment.path
                  attachment = open(answer.attachment.path)
                  zipfile.add(img_name, attachment.path)
                end
              end
            end
            
            unless answer.attachment2_file_name.blank?
              spilt_file_name = answer.attachment2_file_name.split(".")
              total_count = spilt_file_name.count-1
              if total_count > 0
                file_extenstion = spilt_file_name[total_count]
                student = answer.student
                img_name = student.admission_no+"-"+student.full_name+"-2."+file_extenstion
                if File.exists? answer.attachment2.path
                  attachment = open(answer.attachment2.path)
                  zipfile.add(img_name, attachment.path)
                end
              end
            end
            
            unless answer.attachment3_file_name.blank?
              spilt_file_name = answer.attachment3_file_name.split(".")
              total_count = spilt_file_name.count-1
              if total_count > 0
                file_extenstion = spilt_file_name[total_count]
                student = answer.student
                img_name = student.admission_no+"-"+student.full_name+"-3."+file_extenstion
                if File.exists? answer.attachment3.path
                  attachment = open(answer.attachment3.path)
                  zipfile.add(img_name, attachment.path)
                end
              end
            end
            
          end
        end
        send_file  tmp_zip_path,:filename => zip_name
      end
    end
  end
  
  def download_attachment
    #download the  attached file
    @number = params[:number]
    @assignment =Assignment.active.find params[:id]
    unless @assignment.nil?
      if @assignment.download_allowed_for(current_user)
        if @number.blank? or @number.to_i == 1
          filename = @assignment.attachment_file_name
          send_file  @assignment.attachment.path , :type=>@assignment.attachment.content_type,:filename => filename
        elsif @number.to_i == 2
          filename = @assignment.attachment2_file_name
          send_file  @assignment.attachment2.path , :type=>@assignment.attachment2.content_type,:filename => filename
        elsif @number.to_i == 3
          filename = @assignment.attachment3_file_name
          send_file  @assignment.attachment3.path , :type=>@assignment.attachment3.content_type,:filename => filename
        end  
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
      emp_record = current_user.employee_record 
      @subjects = emp_record.subjects.active
      @subjects.reject! {|s| !s.batch.is_active}
      if emp_record.all_access.to_i == 1
        batches = @current_user.employee_record.batches
        #batches += @current_user.employee_record.subjects.collect{|b| b.batch}
        batches = batches.uniq unless batches.empty?
        unless batches.blank?
          batches.each do |batch|
            @subjects += batch.subjects
          end
        end
      end
      @subjects = @subjects.uniq unless @subjects.empty?
      @subjects.sort_by{|s| s.batch.course.code.to_i}
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
  
  private

  def show_comments_associate( assignment_id, student_id )
    @comments = AssignmentComment.find(:all,:conditions=>['student_id = ? and assignment_id = ?',student_id,assignment_id], :include =>[:author])
    @current_user = current_user
  end
  
  def get_total_class(date = "", batch_id = 0, batch_name = "", class_name = "")
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    
    homework_uri = URI(api_endpoint + "api/homework/totalclass")
    http = Net::HTTP.new(homework_uri.host, homework_uri.port)
    homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    homework_req.set_form_data({"daily"=>1,"call_from_web"=>1,"date" => date, "batch_id"=> batch_id, "batch_name"=> batch_name, "class_name"=> class_name,"user_secret" => session[:api_info][0]['user_secret']})

    homework_res = http.request(homework_req)
    @routine_response = JSON::parse(homework_res.body)
  end
end
