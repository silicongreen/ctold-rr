class ClassworksController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only=>[:index]
  filter_access_to :all,:except=>[:show]
  filter_access_to :show,:attribute_check=>true
  before_filter :default_time_zone_present_time
  def get_classwork_filter
    batch_id = params[:batch_name]
    student_class_name = params[:student_class_name]
    student_section = params[:student_section]
    @pub_date_string =  classwork_publish_date = params[:classwork_publish_date]
    @classworks = []
    unless batch_id.nil?
      batchdata = Batch.find_by_id(batch_id)
      unless batchdata.blank?
        batch_name = batchdata.name
        if student_class_name.blank?
          if classwork_publish_date.blank?
            @classworks =Classwork.paginate  :conditions=>"batches.name = '#{batch_name}'  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}]     
          else
            @pub_date = classwork_publish_date.to_datetime.strftime("%Y-%m-%d")
            @classworks =Classwork.paginate  :conditions=>"batches.name = '#{batch_name}' and ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(classworks.created_at) = '#{@pub_date}' and content not like '%</%' ) ) and content not like '%</%' ) )  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}]     
          end
        elsif student_section.blank?
          if classwork_publish_date.blank?
            @classworks =Classwork.paginate  :conditions=>"batches.name = '#{batch_name}' and courses.course_name = '#{student_class_name}'  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[{:batch=>[:course]}]}] 
          else
            @pub_date = classwork_publish_date.to_datetime.strftime("%Y-%m-%d")
            @classworks =Classwork.paginate  :conditions=>"batches.name = '#{batch_name}' and ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(classworks.created_at) = '#{@pub_date}' and content not like '%</%' ) )  and courses.course_name = '#{student_class_name}'  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[{:batch=>[:course]}]}] 
          end
        else
          batch = Batch.find_by_course_id_and_name(student_section, batch_name)
          unless batch.blank?
            if classwork_publish_date.blank?
              @classworks =Classwork.paginate  :conditions=>"batches.id = '#{batch.id}'  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
            else
              @pub_date = classwork_publish_date.to_datetime.strftime("%Y-%m-%d")
              @classworks =Classwork.paginate  :conditions=>"batches.id = '#{batch.id}'  and is_published=1 and ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(classworks.created_at) = '#{@pub_date}' and content not like '%</%' ) )",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
            end
          end
        end  
      end
    else
      if assignment_publish_date.blank?
        @classworks =Classwork.paginate  :conditions=>"is_published=1",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
      else
        @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
        @classworks =Classwork.paginate  :conditions=>"is_published=1 and ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(classworks.created_at) = '#{@pub_date}' and content not like '%</%' ) )",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
      end 
    end  
    respond_to do |format|
      format.js { render :action => 'get_classwork_filter' }
    end
   
  end

  def download_pdf
    batch_id = params[:batch_name]
    student_class_name = params[:student_class_name]
    student_section = params[:student_section]
    @pub_date_string =  classwork_publish_date = params[:classwork_publish_date]
    @classworks = []
    unless batch_id.nil?
      batchdata = Batch.find_by_id(batch_id)
      unless batchdata.blank?
        batch_name = batchdata.name
        if student_class_name.blank?
          if classwork_publish_date.blank?
            @classworks =Classwork.paginate  :conditions=>"batches.name = '#{batch_name}'  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}]     
          else
            @pub_date = classwork_publish_date.to_datetime.strftime("%Y-%m-%d")
            @classworks =Classwork.paginate  :conditions=>"batches.name = '#{batch_name}' and ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(classworks.created_at) = '#{@pub_date}' and content not like '%</%' ) ) and content not like '%</%' ) )  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}]     
          end
        elsif student_section.blank?
          if classwork_publish_date.blank?
            @classworks =Classwork.paginate  :conditions=>"batches.name = '#{batch_name}' and courses.course_name = '#{student_class_name}'  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[{:batch=>[:course]}]}] 
          else
            @pub_date = classwork_publish_date.to_datetime.strftime("%Y-%m-%d")
            @classworks =Classwork.paginate  :conditions=>"batches.name = '#{batch_name}' and ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(classworks.created_at) = '#{@pub_date}' and content not like '%</%' ) )  and courses.course_name = '#{student_class_name}'  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[{:batch=>[:course]}]}] 
          end
        else
          batch = Batch.find_by_course_id_and_name(student_section, batch_name)
          unless batch.blank?
            if classwork_publish_date.blank?
              @classworks =Classwork.paginate  :conditions=>"batches.id = '#{batch.id}'  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
            else
              @pub_date = classwork_publish_date.to_datetime.strftime("%Y-%m-%d")
              @classworks =Classwork.paginate  :conditions=>"batches.id = '#{batch.id}'  and is_published=1 and ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(classworks.created_at) = '#{@pub_date}' and content not like '%</%' ) )",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
            end
          end
        end  
      end
    else
      #@classworks =Classwork.paginate  :conditions=>"is_published=1",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
      if assignment_publish_date.blank?
        @classworks =Classwork.paginate  :conditions=>"is_published=1",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
      else
        @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
        @classworks =Classwork.paginate  :conditions=>"is_published=1 and ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(classworks.created_at) = '#{@pub_date}' and content not like '%</%' ) )",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
      end 
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
    row_first = ['SL','Class','Group','Subject','Title','Teacher','Assign Date']
    new_book.worksheet(0).insert_row(0, row_first)
    
    batch_id = params[:batch_name]
    student_class_name = params[:student_class_name]
    student_section = params[:student_section]
    @pub_date_string =  classwork_publish_date = params[:classwork_publish_date]
    @classworks = []
    unless batch_id.nil?
      batchdata = Batch.find_by_id(batch_id)
      unless batchdata.blank?
        batch_name = batchdata.name
        if student_class_name.blank?
          if classwork_publish_date.blank?
            @classworks =Classwork.paginate  :conditions=>"batches.name = '#{batch_name}'  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}]     
          else
            @pub_date = classwork_publish_date.to_datetime.strftime("%Y-%m-%d")
            @classworks =Classwork.paginate  :conditions=>"batches.name = '#{batch_name}' and ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(classworks.created_at) = '#{@pub_date}' and content not like '%</%' ) ) and content not like '%</%' ) )  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}]     
          end
        elsif student_section.blank?
          if classwork_publish_date.blank?
            @classworks =Classwork.paginate  :conditions=>"batches.name = '#{batch_name}' and courses.course_name = '#{student_class_name}'  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[{:batch=>[:course]}]}] 
          else
            @pub_date = classwork_publish_date.to_datetime.strftime("%Y-%m-%d")
            @classworks =Classwork.paginate  :conditions=>"batches.name = '#{batch_name}' and ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(classworks.created_at) = '#{@pub_date}' and content not like '%</%' ) )  and courses.course_name = '#{student_class_name}'  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[{:batch=>[:course]}]}] 
          end
        else
          batch = Batch.find_by_course_id_and_name(student_section, batch_name)
          unless batch.blank?
            if classwork_publish_date.blank?
              @classworks =Classwork.paginate  :conditions=>"batches.id = '#{batch.id}'  and is_published=1 ",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
            else
              @pub_date = classwork_publish_date.to_datetime.strftime("%Y-%m-%d")
              @classworks =Classwork.paginate  :conditions=>"batches.id = '#{batch.id}'  and is_published=1 and ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(classworks.created_at) = '#{@pub_date}' and content not like '%</%' ) )",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
            end
          end
        end  
      end
    else
      #@classworks =Classwork.paginate  :conditions=>"is_published=1",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
      if assignment_publish_date.blank?
        @classworks =Classwork.paginate  :conditions=>"is_published=1",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
      else
        @pub_date = assignment_publish_date.to_datetime.strftime("%Y-%m-%d")
        @classworks =Classwork.paginate  :conditions=>"is_published=1 and ( (DATE(DATE_ADD(classworks.created_at, INTERVAL 6 HOUR)) = '#{@pub_date}' and content like '%</%') OR ( DATE(classworks.created_at) = '#{@pub_date}' and content not like '%</%' ) )",:order=>"classworks.created_at desc", :page=>params[:page], :per_page => 20,:include=>[{:subject=>[:batch]}] 
      end 
    end   
    
    iloop = 0
    unless @classworks.blank?
      @classworks.each_with_index do |classwork,i|
        iloop = iloop+1
        tmp_row = []
        tmp_row << iloop
        tmp_row << classwork.subject.batch.full_name
        tmp_row << classwork.subject.batch.course.group
        tmp_row << classwork.subject.name
        tmp_row << classwork.title
        unless classwork.employee.blank?
          tmp_row << classwork.employee.full_name
        else
          tmp_row << ""
        end
        tmp_row << I18n.l(classwork.created_at,:format=>"%d-%m-%Y")
        new_book.worksheet(0).insert_row(iloop, tmp_row)
      end
    end
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => "classwork_report.xls", :type =>  "application/vnd.ms-excel"
    
  end


  def remove_attachment
    
    @classwork = Classwork.find_by_id(params[:id])
    unless @classwork.blank?
      @classwork.attachment.destroy
      @classwork.save
    end  
    flash[:notice] = "Attachment Successfully Removed"
    redirect_to :action=>"show",:id=>@classwork.id
  end
  def index
   
    
    @current_user = current_user
    if    @current_user.employee?
      emp_record = current_user.employee_record 
      @subjects = emp_record.subjects.active
      if emp_record.all_access.to_i == 1
        batches = @current_user.employee_record.batches
        batches += @current_user.employee_record.subjects.collect{|b| b.batch}
        batches = batches.uniq unless batches.empty?
        unless batches.blank?
          batches.each do |batch|
            @subjects += batch.subjects
          end
        end
      end
      @subjects = @subjects.uniq unless @subjects.empty?
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
      
      @classworks = Classwork.paginate  :conditions=>"FIND_IN_SET(#{student.id},student_list) and is_published=1",:order=>"created_at desc", :page=>params[:page], :per_page => 10

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
      @classworks = Classwork.paginate  :conditions=>"FIND_IN_SET(#{student.id},student_list) and is_published=1",:order=>"created_at desc", :page=>params[:page], :per_page => 10
      

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

  def subject_classworks

    
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
        batches += @current_user.employee_record.subjects.collect{|b| b.batch}
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
      if !@subject.nil?
        @classworks = Classwork.paginate :conditions=>["subject_id=#{@subject.id} and subject_id in (?) #{publish_condition}",sub_id],:order=>"created_at desc", :page=>params[:page]
      else
        @classworks = Classwork.paginate :conditions=>["subject_id in (?) #{publish_condition} ",sub_id],:order=>"created_at desc", :page=>params[:page]
      end
      
    else
      if !@subject.nil?
        @classworks = Classwork.paginate :conditions=>"subject_id=#{@subject.id} #{publish_condition} and employee_id=#{employee_id}",:order=>"created_at desc", :page=>params[:page]
      else
        @classworks = Classwork.paginate :conditions=>"employee_id=#{employee_id} #{publish_condition} ",:order=>"created_at desc", :page=>params[:page]
      end
    end
    
    render(:update) do |page|
      page.replace_html 'subject_classworks_list', :partial=>'subject_classworks'
    end
  end

  def subject_classworks2

    
    @subject =Subject.find_by_id params[:subject_id]
    @subject_id = params[:subject_id]
    if @current_user.parent?
      target = @current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
    else  
      student=current_user.student_record
    end
    if !@subject.nil?
      @classworks = Classwork.paginate  :conditions=>"subject_id=#{@subject.id} and is_published=1 and FIND_IN_SET(#{student.id},student_list)",:order=>"created_at desc", :page=>params[:page], :per_page => 10
    else
      @classworks = Classwork.paginate  :conditions=>"FIND_IN_SET(#{student.id},student_list) and is_published=1",:order=>"created_at desc", :page=>params[:page], :per_page => 10
    end
    render(:update) do |page|
      page.replace_html 'listing', :partial=>'subject_classworks2'
    end
  end
  
  def subject_classworks3

    @subject =Subject.find_by_id params[:subject_id]    
    unless @subject.nil?
      @classworks =Classwork.paginate  :conditions=>"subject_id=#{@subject.id} and is_published=1 ",:order=>"created_at desc", :page=>params[:page]    
    else
      @classworks = []
    end
    render(:update) do |page|
      page.replace_html 'listing', :partial=>'subject_classworks3'
    end
  end
  
  def new
    if current_user.employee?
      emp_record = current_user.employee_record 
      @subjects = emp_record.subjects.active
      @subjects.reject! {|s| !s.batch.is_active}
      if emp_record.all_access.to_i == 1
        batches = @current_user.employee_record.batches
        batches += @current_user.employee_record.subjects.collect{|b| b.batch}
        batches = batches.uniq unless batches.empty?
        unless batches.blank?
          batches.each do |batch|
            @subjects += batch.subjects
          end
        end
      end
      @subjects = @subjects.uniq unless @subjects.empty?
      @subjects.sort_by{|s| s.batch.course.code.to_i}
      
      @classwork= Classwork.new
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
          @classwork = subject.classworks.build
        end
      end
    end
    if current_user.admin?
      @subject =Subject.find_by_id params[:subject_id] 
      @classwork= Classwork.new
            
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


  def classwork_student_list
    #List the students of the classwork based on their Status of their classwork
    @classwork = Classwork.active.find_by_id params[:id]
    unless @classwork.nil?
      @status = params[:status]
      if @status == "assigned"
        assigned_students= @classwork.student_list.split(",")
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
        @answers = @classwork.classwork_answers
        #        @answers  = @answers.sort_by{|a| a.updated_at}
        @answers  = @answers.sort_by{|a| (a && a.updated_at(:length)) || 0}
        @students = @answers.map{|a| a.student }
      elsif @status== "pending"
        answers = @classwork.classwork_answers
        answered_students = answers.map{|a| a.student_id.to_s }
        assigned_students= @classwork.student_list.split(",")
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
    student_ids = params[:classwork][:student_ids]
    params[:classwork].delete(:student_ids)
    @subject = Subject.find_by_id(params[:classwork][:subject_id])
    subject = params[:classwork][:subject_id]
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
        params[:classwork][:subject_id] = sub_id
        @subject = Subject.find_by_id(sub_id)
        @classwork = Classwork.new(params[:classwork])
        all_std = []
        @all_students.each do |std|
          if @subject.batch_id == std.batch_id
            all_std <<  std.id
          end
        end
        @classwork.student_list = all_std.join(",") unless all_std.nil?
        @classwork.employee = current_user.employee_record
    
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
          if @classwork.save
            if @classwork.is_published==1
              Delayed::Job.enqueue(
                DelayedReminderJob.new( :sender_id  => current_user.id,
                  :recipient_ids => available_user_ids,
                  :subject=>"#{t('new_classwork_added')} : "+params[:classwork][:title],
                  :rtype=>31,
                  :rid=>@classwork.id,
                  :student_id => student_ids,
                  :batch_id => batch_ids,
                  :body=>"#{t('classwork_added_for')} #{@subject.name}  <br/>#{t('view_reports_classwork')}")
              )
              @config_notification = Configuration.find_by_config_key('AllNotificationAdmin')
              if (!@config_notification.blank? and !@config_notification.config_value.blank? and @config_notification.config_value.to_i == 1)
                all_admin = User.find_all_by_admin_and_is_deleted(true,false)
                available_user_ids = all_admin.map(&:id)
                batch = @classwork.subject.batch
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
                      :subject=>"New classwork '#{@classwork.title}' added for #{@subject.name}(Class : #{@subject.batch.course.course_name} #{@subject.batch.course.section_name}) by #{@classwork.employee.full_name}",
                      :rtype=>31,
                      :rid=>@classwork.id,
                      :student_id => 0,
                      :batch_id => 0,
                      :body=>"New classwork '#{@classwork.title}' added for #{@subject.name}(Class : #{@subject.batch.course.course_name} #{@subject.batch.course.section_name}) by #{@classwork.employee.full_name}. <br/>#{t('view_reports_classwork')}")
                  )
                end
              end 
            end
            flash[:notice] = "#{t('new_classwork_sucessfuly_created')}"
            save = true
          else
            if current_user.employee?
              @subjects = current_user.employee_record.subjects
        
              @subjects.reject! {|s| !s.batch.is_active?}
              @students = @subject.batch.students
            end
          end
        else
          unless @classwork.save
            @subjects = current_user.employee_record.subjects
          end
        end
      end
      if save
        redirect_to :action=>:index
      else
        render :action=>:new
      end
    end
  end

  def show
    @classwork  = Classwork.active.find(params[:id], :include => [:employee])
    unless @classwork.nil?
      @current_user = current_user
      @classwork_answers = @classwork.classwork_answers
      @students_assigned_count = @classwork.student_list.split(",").count
      @answered_count = @classwork_answers.count
      @pending_count =     @students_assigned_count  -  @answered_count
      @classwork_answers = ClassworkAnswer.find_all_by_student_id_and_classwork_id(current_user.student_record.id,@classwork.id) if current_user.student?
      
      @subject =Subject.find_by_id @classwork.subject_id
      student=current_user.student_record
      unless @subject.nil?
        @classworks_list =Classwork.paginate  :conditions=>"subject_id=#{@subject.id} and is_published=1 and FIND_IN_SET(#{student.id},student_list)",:order=>"created_at desc", :page=>params[:page]
      else
        @classworks_list =Classwork.paginate  :conditions=>"FIND_IN_SET(#{student.id},student_list) and is_published=1",:order=>"created_at desc", :page=>params[:page]
      end
      
      Reminder.update_all("is_read='1'",  ["rid = ? and rtype = ? and recipient= ?", params[:id], 31,current_user.id])
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end

  def edit
    @classwork  = Classwork.active.find(params[:id])
    unless @classwork.nil?
      #if @classwork.employee_id==current_user.employee_record.id
      if @current_user.admin? or @current_user.employee?
        load_data
      else
        flash[:notice] ="#{t('you_cannot_edit_this_classwork')}"
        redirect_to classworks_path
      end
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end
  def update
    @classwork = Classwork.find_by_id(params[:id])
    unless @classwork.nil?
      student_ids = params[:classwork][:student_ids]
      params[:classwork].delete(:student_ids)
      @classwork.student_list = student_ids.join(",") unless student_ids.nil?
      Classwork.record_timestamps=false
      if  @classwork.update_attributes(params[:classwork])
        flash[:notice]="#{t('classwork_details_updated')}"
        Classwork.record_timestamps=true
        redirect_to @classwork
      else
        load_data
        render :edit ,:id=>params[:id]
      end
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end
  
  def published_classwork
    now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    @classwork = Classwork.find_by_id(params[:id])
    @classwork.is_published = 1
    @classwork.created_at = now
    
    if @classwork.save
      a_student = @classwork.student_list
      if !a_student.blank?
        @subject = Subject.find_by_id(@classwork.subject_id)
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
            :subject=>"#{t('new_classwork_added')} : "+@classwork.title,
            :rtype=>31,
            :rid=>@classwork.id,
            :student_id => student_ids,
            :batch_id => batch_ids,
            :body=>"#{t('classwork_added_for')} #{@subject.name}  <br/>#{t('view_reports_classwork')}")
        )
        @config_notification = Configuration.find_by_config_key('AllNotificationAdmin')
        if (!@config_notification.blank? and !@config_notification.config_value.blank? and @config_notification.config_value.to_i == 1)
          all_admin = User.find_all_by_admin_and_is_deleted(true,false)
          available_user_ids = all_admin.map(&:id)
          batch = @classwork.subject.batch
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
                :subject=>"New classwork '#{@classwork.title}' added for #{@subject.name}(Class : #{@subject.batch.course.course_name} #{@subject.batch.course.section_name}) by #{@classwork.employee.full_name}",
                :rtype=>31,
                :rid=>@classwork.id,
                :student_id => 0,
                :batch_id => 0,
                :body=>"New classwork '#{@classwork.title}' added for #{@subject.name}(Class : #{@subject.batch.course.course_name} #{@subject.batch.course.section_name}) by #{@classwork.employee.full_name}. <br/>#{t('view_reports_classwork')}")
            )
          end
        end
      end
      flash[:notice] = "Classwork successfully published"
      redirect_to classworks_path
    end
  end

  def destroy
    @classwork = Classwork.find_by_id(params[:id])
    if (current_user.admin?) or (@classwork.employee_id == current_user.employee_record.id)
      @classwork.destroy
      flash[:notice] = "#{t('classwork_sucessfully_deleted')}"
      redirect_to classworks_path
    else
      flash[:notice] = "#{t('you_do_not_have_permission_to_delete_this_classwork')}"
      redirect_to edit_classwork_path(@classwork)
    end

  end
  def download_attachment
    #download the  attached file
    @classwork =Classwork.active.find params[:id]
    unless @classwork.nil?
      if @classwork.download_allowed_for(current_user)
        filename = @classwork.attachment_file_name
        send_file  @classwork.attachment.path , :type=>@classwork.attachment.content_type,:filename => filename
      else
        flash[:notice] = "#{t('you_are_not_allowed_to_download_that_file')}"
        redirect_to :controller=>:classworks
      end
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end

  def load_data
    @subject = @classwork.subject
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
    @classwork.student_list.split(",").each do |s|
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
