class BoardController < ApplicationController
  include ActionView::Helpers::TextHelper
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time
  
  def marks_entry
    @subject = BoardExamSubject.find_by_id(params[:id])
    @board_exam_marks = BoardExamMark.find_all_by_board_exam_subject_id(@subject.id)
    @board_exam = BoardExam.find(@subject.board_exam_id,:include=>["board_exam_name","board_exam_group","board_session"])
    if @subject.is_elective?
      @student_id = BoardExamStudentSubject.find_all_by_board_exam_subject_id(params[:id])
      unless @student_id.blank?
        @std_ids = @student_id.map(&:board_exam_student_id)
        @students = BoardExamStudent.find_all_by_id(@std_ids)
      end
    else
      @students = BoardExamStudent.find_all_by_board_exam_id(@subject.board_exam_id,:include=>["batch"])
    end  
  end
  def subject_result
    @subject = BoardExamSubject.find_by_id(params[:id])
    @board_exam_marks = BoardExamMark.find_all_by_board_exam_subject_id(@subject.id)
    @board_exam = BoardExam.find(@subject.board_exam_id,:include=>["board_exam_name","board_exam_group","board_session"])
    if @subject.is_elective?
      @student_id = BoardExamStudentSubject.find_all_by_board_exam_subject_id(params[:id])
      unless @student_id.blank?
        @std_ids = @student_id.map(&:board_exam_student_id)
        @students = BoardExamStudent.find_all_by_id(@std_ids)
      end
    else
      @students = BoardExamStudent.find_all_by_board_exam_id(@subject.board_exam_id,:include=>["batch"])
    end
  end
  def student_marks_entry
    @board_exam = BoardExam.find(params[:id],:include=>["board_exam_name","board_exam_group","board_session"])
    @board_exam_student = BoardExamStudent.find(params[:id2])
    @std_info = get_student_all_type(@board_exam_student.student_id)
    @board_exam_subject = BoardExamSubject.find_all_by_board_exam_id(@board_exam.id,:conditions=>["is_elective = ?",false],:order=>"priority ASC")
    student_subject = BoardExamStudentSubject.find_all_by_board_exam_student_id(params[:id2])
    
    unless student_subject.blank?
      subject_id = student_subject.map(&:board_exam_subject_id)
      board_exam_subject_elective = BoardExamSubject.find_all_by_id(subject_id)
      @board_exam_subject =  @board_exam_subject+board_exam_subject_elective
      @board_exam_subject.sort! { |a, b|  a.priority.to_i <=> b.priority.to_i }
    end
    board_exam_subject_ids = @board_exam_subject.map(&:id)
    @marks = BoardExamMark.find_all_by_board_exam_student_id_and_board_exam_subject_id(params[:id2],board_exam_subject_ids)
    
  end
  def exam_result
    @board_exam = BoardExam.find(params[:id],:include=>["board_exam_name","board_exam_group","board_session"])
    @board_exam_student = BoardExamStudent.find(params[:id2])
    @std_info = get_student_all_type(@board_exam_student.student_id)
    @board_exam_subject = BoardExamSubject.find_all_by_board_exam_id(@board_exam.id,:conditions=>["is_elective = ?",false],:order=>"priority ASC")
    student_subject = BoardExamStudentSubject.find_all_by_board_exam_student_id(params[:id2])
    
    unless student_subject.blank?
      subject_id = student_subject.map(&:board_exam_subject_id)
      board_exam_subject_elective = BoardExamSubject.find_all_by_id(subject_id)
      @board_exam_subject =  @board_exam_subject+board_exam_subject_elective
      @board_exam_subject.sort! { |a, b|  a.priority.to_i <=> b.priority.to_i }
    end
    board_exam_subject_ids = @board_exam_subject.map(&:id)
    @marks = BoardExamMark.find_all_by_board_exam_student_id_and_board_exam_subject_id(params[:id2],board_exam_subject_ids)
    
  end
  
  def exam_process
    students = BoardExamStudent.find_all_by_board_exam_id(params[:id],:include=>["batch"])
    @board_exam = BoardExam.find(params[:id])
    all_subjects = BoardExamSubject.find_all_by_board_exam_id(params[:id])
    
    if !students.blank? && !all_subjects.blank?
      students.each do |std_data|
        sub_count = 0
        total_point = 0
        avg_point = 0
        all_subjects.each do |sub_data|
          if sub_data.is_elective?
            assign_student = BoardExamStudentSubject.find_by_board_exam_subject_id_and_board_exam_student_id(sub_data.id,std_data.id)
            if assign_student.blank?
              next
            else
              unless assign_student.subject_taken_as == "4th"
                sub_count = sub_count+1
              end
            end  
          else
            sub_count = sub_count+1
          end
          board_exam_mark = BoardExamMark.find_by_board_exam_subject_id_and_board_exam_student_id(sub_data.id,std_data.id,:include=>[:board_grading_level])
          unless board_exam_mark.blank?
            total_point = total_point+board_exam_mark.board_grading_level.credit_points
          end
        end
        total_max_point = 5*sub_count
        if total_point > total_max_point
          total_point = total_max_point
        end
        
        if total_point > 0
          avg_point = (total_point.to_f/sub_count)  
        end
        
        gradeObj = BoardGradingLevel.new
        grading_level = gradeObj.grade_point_to_grade(avg_point)
        unless grading_level.blank?
          std_data.board_grading_level_id = grading_level.id
          std_data.total_credit_points = total_point
          std_data.avg_credit_points = avg_point
          std_data.save
        end
      end
      @board_exam.is_published = 1
      @board_exam.save
      flash[:notice] = "Process Complete"
    end
    @board_sessions = BoardSession.find(:all,:order=>"created_at DESC")
    @board_exam_names = BoardExamName.find(:all,:order=>"created_at ASC")
    @board_exam_groups = BoardExamGroup.find(:all,:order=>"created_at ASC")
    @exams = BoardExam.paginate(:order=>"created_at DESC",:page => params[:page], :per_page => 10)
  end
  
  def save_marks
    unless params[:board_exam_mark].blank?
      params[:board_exam_mark].each_pair do |subject_id, stdetails|
        @subject = BoardExamSubject.find_by_id(subject_id)
        stdetails.each_pair do |student_id, details|
          assign_student = BoardExamStudentSubject.find_by_board_exam_subject_id_and_board_exam_student_id(subject_id,student_id)
          bm_prev = BoardExamMark.find_by_board_exam_subject_id_and_board_exam_student_id(subject_id,student_id)
          unless bm_prev.blank?
            bm_prev.destroy
          end
          b_marks = BoardExamMark.new
          b_marks.board_exam_id = @subject.board_exam_id
          b_marks.board_exam_student_id = student_id
          b_marks.board_exam_subject_id = subject_id
          b_marks.board_grading_level_id = details[:grade]
          b_marks.subject_taken_as = (assign_student.blank?)?"-":assign_student.subject_taken_as
          b_marks.save
        end
      end
      flash[:notice] = "Marks successfully Updated"
    end
  end
  
  def add_session
    @board_sessions = BoardSession.find(:all,:order=>"created_at DESC")
    @board_session = BoardSession.new
    if request.post?
      @board_session = BoardSession.new(params[:board_session])
      if @board_session.save
        flash[:notice] = "Session successfully saved"
        redirect_to :controller => "board", :action => "add_session"
      end
    end
  end
 
  def edit_session
    @board_sessions = BoardSession.find(:all,:order=>"created_at DESC")
    @board_session = BoardSession.find(params[:id])
    if request.post?
      if @board_session.update_attributes(params[:board_session])
        flash[:notice] = "Session successfully Updated"
        redirect_to :controller => "board", :action => "add_session"
      end
    end
  end
  def delete_session
    board_exam = BoardExam.find_by_board_session_id(params[:id])
    if board_exam.blank?
      BoardSession.find(params[:id]).destroy
      flash[:notice] = "Session successfully deleted"
      redirect_to :controller => "board", :action => "add_session"
    else
      flash[:notice] = "This session has exam please remove them first"
      redirect_to :controller => "board", :action => "add_session"
    end  
  end
  
  def assign_students
    @subject = BoardExamSubject.find_by_id(params[:id])
    @board_exam = BoardExam.find(@subject.board_exam_id,:include=>["board_exam_name","board_exam_group","board_session"])
    @students = BoardExamStudent.find_all_by_board_exam_id(@subject.board_exam_id,:include=>["batch"])
    @assign_students = BoardExamStudentSubject.find_all_by_board_exam_subject_id(params[:id])
  end
  def unassign_student   
    BoardExamStudentSubject.find_by_board_exam_subject_id_and_board_exam_student_id(params[:id2],params[:id]).destroy
    @subject = BoardExamSubject.find_by_id(params[:id2])
    @board_exam = BoardExam.find(@subject.board_exam_id,:include=>["board_exam_name","board_exam_group","board_session"])
    @students = BoardExamStudent.find_all_by_board_exam_id(@subject.board_exam_id,:include=>["batch"])
    @assign_students = BoardExamStudentSubject.find_all_by_board_exam_subject_id(params[:id2])
    flash[:notice] = "Unassigned Successfully"  
  end
  def assign_to_subject
    @board_exam_student_subject = BoardExamStudentSubject.new
    @subject = BoardExamSubject.find_by_id(params[:id2])
    @student = BoardExamStudent.find_by_id(params[:id])
    respond_to do |format|
      format.js { render :action => 'assign_to_subject' }
    end
  end
  def assign_student
    @error = true
    @board_exam_student_subject = BoardExamStudentSubject.new(params[:board_exam_student_subject])
    @subject = BoardExamSubject.find_by_id(params[:id2])
    @student = BoardExamStudent.find_by_id(params[:id])
    if @board_exam_student_subject.save
      @board_exam = BoardExam.find(@subject.board_exam_id,:include=>["board_exam_name","board_exam_group","board_session"])
      @students = BoardExamStudent.find_all_by_board_exam_id(@subject.board_exam_id,:include=>["batch"])
      @assign_students = BoardExamStudentSubject.find_all_by_board_exam_subject_id(params[:id2])
      @error = false
      flash[:notice] = "Student Successfuly Assigned"
    end    
  end
  
  def exam
    @board_sessions = BoardSession.find(:all,:order=>"created_at DESC")
    @board_exam_names = BoardExamName.find(:all,:order=>"created_at ASC")
    @board_exam_groups = BoardExamGroup.find(:all,:order=>"created_at ASC")
    @exams = BoardExam.paginate(:order=>"created_at DESC",:page => params[:page], :per_page => 10)
  end
  def get_exam_filter
    conditions = "1 = 1"
    unless params[:board_exam_name_id].blank?
      conditions = conditions+" and board_exam_name_id="+params[:board_exam_name_id].to_s
    end 
    unless params[:board_exam_session_id].blank?
      conditions = conditions+" and board_session_id="+params[:board_exam_session_id].to_s
    end 
    unless params[:board_exam_group_id].blank?
      conditions = conditions+" and board_exam_group_id="+params[:board_exam_group_id].to_s
    end
    @exams = BoardExam.paginate(:order=>"created_at DESC",:conditions=>conditions,:page => params[:page], :per_page => 10)
    respond_to do |format|
      format.js { render :action => 'get_exam_filter' }
    end
  end
  
  
  def exam_students
    @board_exam = BoardExam.find(params[:id],:include=>["board_exam_name","board_exam_group","board_session"])
    @students = BoardExamStudent.find_all_by_board_exam_id(params[:id],:include=>["batch"])
    @page = params[:page]
  end
  
  def search_student
    require 'json'
    require "yaml"
    data = []
    term = params[:term]
    students = Student.find(:all,:conditions=>["students.admission_no like ? OR students.first_name like ? OR students.last_name like ? OR concat('students.first_name',' ','students.last_name') like ? OR concat('students.first_name',' ','students.middle_name',' ','students.last_name') like ?",'%'+term+'%','%'+term+'%','%'+term+'%','%'+term+'%','%'+term+'%'])
    unless students.blank?
      students.each do |student|
        data_user = {:value=>student.admission_no,:label=>student.first_name+" "+student.last_name+"("+student.batch.full_name+")"}
        data << data_user
      end
    end
    @data = JSON.generate(data)
    render :text=>@data
  end
  
  def new_student
    @student = BoardExamStudent.new
    respond_to do |format|
      format.js { render :action => 'new_student' }
    end
  end
  
  def edit_student
    @board_exam_student = BoardExamStudent.find(params[:id])
    respond_to do |format|
      format.js { render :action => 'edit_student' }
    end
  end
  
  def update_student
    @error = true
    @student = BoardExamStudent.find(params[:id])
    @board_exam = @student.board_exam
    
    if @student.update_attributes(params[:board_exam_student])
      @error = false
      @students = BoardExamStudent.find_all_by_board_exam_id(@board_exam.id)
      flash[:notice] = "Student Successfuly Update"
    end    
  end
  
  def create_student
    @error = true
    std_id = params[:board_exam_student][:student_id]
    std_info = Student.find_by_admission_no(std_id)
    params[:board_exam_student][:batch_id] = std_info.batch_id
    params[:board_exam_student][:student_id] = std_info.id
    @student = BoardExamStudent.new(params[:board_exam_student])
    @board_exam = BoardExam.find(params[:board_exam_student][:board_exam_id])
    
    if @student.save
      count = @board_exam.number_of_students+1
      @board_exam.update_attribute("number_of_students",count)
      @students = BoardExamStudent.find_all_by_board_exam_id(@board_exam.id)
      @error = false
      flash[:notice] = "Student Successfuly Added"
    end    
  end
  
  def delete_student
    @student = BoardExamStudent.find(params[:id])
    @board_exam = BoardExam.find(@student.board_exam_id)
    count = @board_exam.number_of_students-1
    @board_exam.update_attribute("number_of_students",count)
    BoardExamStudent.find(params[:id]).destroy
    flash[:notice] = "#{t('subject_deleted_successfully')}"
  end
  
  
  
  
  def exam_subjects
    @board_exam = BoardExam.find(params[:id],:include=>["board_exam_name","board_exam_group","board_session"])
    @subjects = BoardExamSubject.find_all_by_board_exam_id(params[:id],:order=>"priority ASC")
    @board_exam_marks = BoardExamMark.find_all_by_board_exam_id(params[:id])
    @page = params[:page]
  end
  def import_subject
    @board_exam = BoardExam.find(params[:id],:include=>["board_exam_name","board_exam_group","board_session"])
    @board_exam_marks = BoardExamMark.find_all_by_board_exam_id(params[:id])
    @group_subjects = BoardExamGroupSubject.find_all_by_board_exam_name_id_and_board_exam_group_id(@board_exam.board_exam_name_id,@board_exam.board_exam_group_id,:order=>"priority ASC")
    unless @group_subjects.blank?
      @group_subjects.each do |subject|
          newsubject = BoardExamSubject.new
          newsubject.board_exam_id = @board_exam.id
          newsubject.code = subject.code
          newsubject.name = subject.name
          newsubject.credit_hours = subject.credit_hours
          newsubject.is_elective = subject.is_elective
          newsubject.priority = subject.priority
          if newsubject.save
            count = @board_exam.number_of_subjects+1
            @board_exam.update_attribute("number_of_subjects",count)
          end
      end
    end
    @subjects = BoardExamSubject.find_all_by_board_exam_id(params[:id],:order=>"priority ASC")
    respond_to do |format|
      format.js { render :action => 'import_subject' }
    end
  end
  
  def new_subject
    @subject = BoardExamSubject.new
    respond_to do |format|
      format.js { render :action => 'new_subject' }
    end
  end
  
  def edit_subject
    @board_exam_subject = BoardExamSubject.find(params[:id])
    respond_to do |format|
      format.js { render :action => 'edit_subject' }
    end
  end
  
  def update_subject
    @error = true
    @board_exam_subject = BoardExamSubject.find(params[:id])
    @board_exam = @board_exam_subject.board_exam
    
    if @board_exam_subject.update_attributes(params[:board_exam_subject])
      @error = false
      @subjects = BoardExamSubject.find_all_by_board_exam_id(@board_exam.id,:order=>"priority ASC")
      @board_exam_marks = BoardExamMark.find_all_by_board_exam_id(params[:id])
      flash[:notice] = "#{t('subject_created_successfully')}"
    end    
  end
  
  def create_subject
    @error = true
    @subject = BoardExamSubject.new(params[:board_exam_subject])
    @board_exam = BoardExam.find(params[:board_exam_subject][:board_exam_id])
    
    if @subject.save
      count = @board_exam.number_of_subjects+1
      @board_exam.update_attribute("number_of_subjects",count)
      @subjects = BoardExamSubject.find_all_by_board_exam_id(@board_exam.id,:order=>"priority ASC")
      @board_exam_marks = BoardExamMark.find_all_by_board_exam_id(params[:id])
      @error = false
      flash[:notice] = "#{t('subject_created_successfully')}"
    end    
  end
  
  def delete_subject
    @board_exam_subject = BoardExamSubject.find(params[:id])
    @board_exam = BoardExam.find(@board_exam_subject.board_exam_id)
    @board_exam_marks = BoardExamMark.find_all_by_board_exam_id(@board_exam_subject.board_exam_id)
    count = @board_exam.number_of_subjects-1
    @board_exam.update_attribute("number_of_subjects",count)
    BoardExamSubject.find(params[:id]).destroy
    flash[:notice] = "#{t('subject_deleted_successfully')}"
  end
  
  def add_exam
    @board_exam = BoardExam.new
    @board_sessions = BoardSession.find(:all,:order=>"created_at DESC")
    @board_exam_names = BoardExamName.find(:all,:order=>"created_at ASC")
    @board_exam_groups = BoardExamGroup.find(:all,:order=>"created_at ASC")
    respond_to do |format|
      format.js { render :action => 'add_exam' }
    end
  end
  
  def edit_exam
    @board_exam = BoardExam.find(params[:id])
    @board_sessions = BoardSession.find(:all,:order=>"created_at DESC")
    @board_exam_names = BoardExamName.find(:all,:order=>"created_at ASC")
    @board_exam_groups = BoardExamGroup.find(:all,:order=>"created_at ASC")
    respond_to do |format|
      format.js { render :action => 'edit_exam' }
    end
  end
  
  def update_exam
    @error = true
    @board_exam = BoardExam.find(params[:id])
    if @board_exam.update_attributes(params[:board_exam])
      @error = false
      @exams = BoardExam.paginate(:order=>"created_at DESC",:page => params[:page], :per_page => 10)
      flash[:notice] = "Exam Successfully Updated"
    end    
  end
  
  def create_exam
    @error = true
    @board_exam = BoardExam.new(params[:board_exam])
    if @board_exam.save
      @exams = BoardExam.paginate(:order=>"created_at DESC",:page => params[:page], :per_page => 10)
      @error = false
      flash[:notice] = "Exam Successfully Created"
    end    
  end
  
  def delete_exam
    BoardExam.find(params[:id]).destroy
    flash[:notice] = "Exam Successfully Deleted"
  end
  
  
  def add_group
    @board_exam_groups = BoardExamGroup.find(:all,:order=>"created_at DESC")
    @board_exam_group = BoardExamGroup.new
    if request.post?
      @board_exam_group = BoardExamGroup.new(params[:board_exam_group])
      if @board_exam_group.save
        flash[:notice] = "Student group successfully saved"
        redirect_to :controller => "board", :action => "add_group"
      end
    end
  end
  
  def edit_group
    @board_exam_groups = BoardExamGroup.find(:all,:order=>"created_at DESC")
    @board_exam_group = BoardExamGroup.find(params[:id])
    if request.post?
      if @board_exam_group.update_attributes(params[:board_exam_group])
        flash[:notice] = "Student group  successfully Updated"
        redirect_to :controller => "board", :action => "add_group"
      end
    end
  end
  
  def delete_group
    board_exam = BoardExam.find_by_board_exam_group_id(params[:id])
    if board_exam.blank?
      BoardExamGroup.find(params[:id]).destroy
      flash[:notice] = "Student group  successfully deleted"
      redirect_to :controller => "board", :action => "add_group"
    else
      flash[:notice] = "This student group has exam please remove them first"
      redirect_to :controller => "board", :action => "add_group"
    end  
  end
  
  def new_group_subject
    @subject = BoardExamGroupSubject.new
    respond_to do |format|
      format.js { render :action => 'new_group_subject' }
    end
  end
  
  def edit_group_subject
    @board_exam_group_subject = BoardExamGroupSubject.find(params[:id])
    respond_to do |format|
      format.js { render :action => 'edit_group_subject' }
    end
  end
  
  def update
    @error = true
    @board_exam_group_subject = BoardExamGroupSubject.find(params[:id])
    @board_exam_name = @board_exam_group_subject.board_exam_name
    @board_exam_group = @board_exam_group_subject.board_exam_group
    
    if @board_exam_group_subject.update_attributes(params[:board_exam_group_subject])
      @error = false
      @group_subjects = BoardExamGroupSubject.find_all_by_board_exam_name_id_and_board_exam_group_id(@board_exam_name.id,@board_exam_group.id,:order=>"priority ASC")
      flash[:notice] = "#{t('subject_created_successfully')}"
    end    
  end
  
  def create
    @error = true
    @subject = BoardExamGroupSubject.new(params[:board_exam_group_subject])
    @board_exam_name = BoardExamName.find(params[:board_exam_group_subject][:board_exam_name_id])
    @board_exam_group = BoardExamGroup.find(params[:board_exam_group_subject][:board_exam_group_id])
    
    if @subject.save
      @group_subjects = BoardExamGroupSubject.find_all_by_board_exam_name_id_and_board_exam_group_id(params[:board_exam_group_subject][:board_exam_name_id],params[:board_exam_group_subject][:board_exam_group_id],:order=>"priority ASC")
      @error = false
      flash[:notice] = "#{t('subject_created_successfully')}"
    end    
  end
  
  def delete_group_subject
    BoardExamGroupSubject.find(params[:id]).destroy
    flash[:notice] = "#{t('subject_deleted_successfully')}"
  end
  
  def exam_group_subject
    @board_exam_names = BoardExamName.find(:all,:order=>"created_at DESC")
    @board_exam_groups = BoardExamGroup.find(:all,:order=>"created_at DESC")
  end
  def get_exam_group_subject
    @board_exam_name = BoardExamName.find(params[:exam_name_id])
    @board_exam_group = BoardExamGroup.find(params[:id])
    @group_subjects = BoardExamGroupSubject.find_all_by_board_exam_name_id_and_board_exam_group_id(params[:exam_name_id],params[:id],:order=>"priority ASC")
    respond_to do |format|
      format.js { render :action => 'get_exam_group_subject' }
    end
  end
  
end
