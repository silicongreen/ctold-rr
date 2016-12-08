#Champs21
#Copyright 2011 teamCreative Private Limited
#
#This product includes software developed at
#Project Champs21 - http://www.champs21.com/
#
#Licensed under the Apache License, Version 2.0 (the "License");
#you may not use this file except in compliance with the License.
#You may obtain a copy of the License at
#
#  http://www.apache.org/licenses/LICENSE-2.0
#
#Unless required by applicable law or agreed to in writing, software
#distributed under the License is distributed on an "AS IS" BASIS,
#WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#See the License for the specific language governing permissions and
#limitations under the License.

class ExamGroupsController < ApplicationController
  before_filter :login_required
  filter_access_to :all, :except=>[:index,:show,:show_pdf,:set_exam_maximum_marks,:set_exam_minimum_marks, :assign_exam]
  filter_access_to [:index,:show,:show_pdf,:set_exam_maximum_marks,:set_exam_minimum_marks, :assign_exam], :attribute_check=>true, :load_method => lambda { current_user }
  before_filter :initial_queries
  before_filter :protect_other_student_data
  before_filter :restrict_employees_from_exam
  before_filter :protect_other_batch_exams, :only => [:show,:show_pdf, :index]
  in_place_edit_with_validation_for :exam_group, :name
  in_place_edit_with_validation_for :exam, :maximum_marks
  in_place_edit_with_validation_for :exam, :minimum_marks
  in_place_edit_with_validation_for :exam, :weightage

  def index
    @from = 'exam' 
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @is_class_exam = false
    unless params[:class_exam].nil? 
      @is_class_exam = params[:class_exam]
    end 
    
    @is_batch_exam = false
    unless params[:batch_exam].nil? 
      @is_batch_exam = params[:batch_exam]
    end 
    
    if @is_class_exam
      @batch_name = @batch.name
      
      @course = @batch.course
      
      @course_name = @course.course_name
      
      batch_name_tmp = @is_batch_exam ? nil : @batch_name
      
      @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
    end
    
    @sms_setting = SmsSetting.new
    @exam_groups = @batch.exam_groups
    
    
    #@batches = []
#    if @is_class_exam
#      @course_name = @batch.course.course_name
#      @batch_name = @batch.name
#      @tmp_courses = Course.find(:all, :conditions => ["courses.course_name LIKE (?) and batches.name = ?", @course_name, @batch_name], :select => "batches.id", :joins=> "INNER JOIN `batches` ON batches.course_id = courses.id")
#      @batches = @tmp_courses.map{|tc| tc.id}
#    end
    
    this_id = @batch.id
    unless @batches.nil? or @batches.empty?
      @batches.each do |batch_id|
        if batch_id != this_id
          @batch_data = Batch.find batch_id
          @tmp_exam_groups = @batch_data.exam_groups
          @exam_groups = @exam_groups + @tmp_exam_groups
        end
      end
      @tmp_exam = @exam_groups
      e_name = []
      @t_exam = []
      @tmp_exam.each do |t|
        if !e_name.include?(t.name)
           @t_exam << t
           e_name << t.name
        end   
      end
      @exam_groups = @t_exam
    end
    if @current_user.employee?
      @user_privileges = @current_user.privileges
      @employee_subjects= @current_user.employee_record.subjects.map { |n| n.id}
      if @employee_subjects.empty? and !@user_privileges.map{|p| p.name}.include?('ExaminationControl') and !@user_privileges.map{|p| p.name}.include?('EnterResults')
        flash[:notice] = "#{t('flash_msg4')}"
        redirect_to :controller => 'user', :action => 'dashboard'
      end
    end
  end

  def new
    @from = 'exam'
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @is_class_exam = false
    unless params[:class_exam].nil? 
      @is_class_exam = params[:class_exam]
    end 
    
    @is_batch_exam = false
    unless params[:batch_exam].nil? 
      @is_batch_exam = params[:batch_exam]
    end 
    
    @user_privileges = @current_user.privileges
    @cce_exam_categories = CceExamCategory.all if @batch.cce_enabled?
    if !@current_user.admin? and !@user_privileges.map{|p| p.name}.include?('ExaminationControl') and !@user_privileges.map{|p| p.name}.include?('EnterResults')
      flash[:notice] = "#{t('flash_msg4')}"
      redirect_to :controller => 'user', :action => 'dashboard'
    end
  end

  def set_exam_minimum_marks
    @is_class_exam = false
    unless params[:class_exam].nil?
      @is_class_exam = true
    end
    
    @is_batch_exam = false
    unless params[:batch_exam].nil?
      @is_batch_exam = true
    end
    
    @exam = Exam.find(params[:id])
    
    @exam_group = ExamGroup.find @exam.exam_group_id
    @tmp_subject = Subject.find @exam.subject_id
    
    @batch = Batch.find @exam_group.batch_id
    
    if @is_class_exam
      @batch_name = @batch.name
      @course = @batch.course
      
      @course_name = @course.course_name
      
      batch_name_tmp = @is_batch_exam ? nil : @batch_name
      @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
    end
    
    if @is_class_exam
      @exam_group_name = @exam_group.name
      @exam_groups = ExamGroup.find(:all, :conditions => ["name LIKE ? and batch_id IN (?) and exam_type = ? and exam_category = ? and exam_date = ?", @exam_group.name, @batches, @exam_group.exam_type, @exam_group.exam_category, @exam_group.exam_date]).map{|eg| eg.id}
      @subjects = Subject.find(:all, :conditions => ["name LIKE ? and batch_id IN (?)", @tmp_subject.name, @batches]).map{|s| s.id}
      @exam_groups.each do |exam_group_id|
        @subjects.each do |subject_id|
          tmp_exam = Exam.find_by_exam_group_id_and_subject_id_and_start_time_and_end_time_and_maximum_marks_and_minimum_marks(exam_group_id, subject_id, @exam.start_time, @exam.end_time, @exam.maximum_marks, @exam.minimum_marks)
          unless tmp_exam.nil?
            tmp_exam.update_attributes(:minimum_marks => params[:value])
            break
          end
        end
      end
    else
      @exam.update_attributes(:minimum_marks => params[:value])
    end
    @value = params[:value]
  end
  
  def assign_exam
    @from = 'exam'
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @is_class_exam = false
    unless params[:class_exam].nil?
      @is_class_exam = true
    end
    
    @is_batch_exam = false
    unless params[:batch_exam].nil?
      @is_batch_exam = true
    end
    
    @exam_group = ExamGroup.find(params[:id], :include => :exams)
    @batch = Batch.find @exam_group.batch_id
    this_id = @batch.id
    if @is_class_exam
      @batch_name = @batch.name
      @course = @batch.course
      
      @course_name = @course.course_name
      
      batch_name_tmp = @is_batch_exam ? nil : @batch_name
      @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
    end
     
    
    if @is_class_exam
      @batches.each do |b|
        if b != this_id
          
          tmp_subject = Subject.new()
          @exam_attrs = {}
          i = 0
          @exam_group.exams.each do |exam|
            tmp_exam_attributes = {"subject_id" => exam.subject_id, "start_time" => exam.start_time, "end_time" => exam.end_time, "maximum_marks" => exam.maximum_marks, "minimum_marks" => exam.minimum_marks}
            @exam_attrs[i] = tmp_exam_attributes
            i += 1
          end
          
          @exam_attributes = tmp_subject.getExamSubjects(@exam_attrs, b, @batches)
          @exam_data = {"name" => @exam_group.name,"is_current" => 1, "exam_category" => @exam_group.exam_category, "topic" => @exam_group.topic, "exam_type" => @exam_group.exam_type, "exam_date" => @exam_group.exam_date, "exams_attributes" => @exam_attributes}
          
          @new_exam = ExamGroup.new(@exam_data)
          @new_exam.batch_id = b
          if @new_exam.save
            flash[:notice] = "#{t('flash_msg46')}"
          else  
            flash[:notice] = "#{t('flash_msg47')}"
          end
        end
      end
    end
    if @is_class_exam
      if @is_batch_exam
        redirect_to batch_exam_groups_path(@batch, :class_exam => true, :batch_exam => true)
      else  
        redirect_to batch_exam_groups_path(@batch, :class_exam => true)
      end
    else  
      redirect_to batch_exam_groups_path(@batch)
    end
  end
  
  def set_exam_maximum_marks
    @is_class_exam = false
    unless params[:class_exam].nil?
      @is_class_exam = true
    end
    
    @is_batch_exam = false
    unless params[:batch_exam].nil?
      @is_batch_exam = true
    end
    
    @exam = Exam.find(params[:id])
    
    @exam_group = ExamGroup.find @exam.exam_group_id
    @tmp_subject = Subject.find @exam.subject_id
    
    @batch = Batch.find @exam_group.batch_id
    
    if @is_class_exam
      @batch_name = @batch.name
      @course = @batch.course
      
      @course_name = @course.course_name
      
      batch_name_tmp = @is_batch_exam ? nil : @batch_name
      @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
    end
    
    if @is_class_exam
      @exam_group_name = @exam_group.name
      @exam_groups = ExamGroup.find(:all, :conditions => ["name LIKE ? and batch_id IN (?) and exam_type = ? and exam_category = ? and exam_date = ?", @exam_group.name, @batches, @exam_group.exam_type, @exam_group.exam_category, @exam_group.exam_date]).map{|eg| eg.id}
      @subjects = Subject.find(:all, :conditions => ["name LIKE ? and batch_id IN (?)", @tmp_subject.name, @batches]).map{|s| s.id}
      @exam_groups.each do |exam_group_id|
        @subjects.each do |subject_id|
          tmp_exam = Exam.find_by_exam_group_id_and_subject_id_and_start_time_and_end_time_and_maximum_marks_and_minimum_marks(exam_group_id, subject_id, @exam.start_time, @exam.end_time, @exam.maximum_marks, @exam.minimum_marks)
          unless tmp_exam.nil?
            tmp_exam.update_attributes(:maximum_marks => params[:value])
            break
          end
        end
      end
    else
      @exam.update_attributes(:maximum_marks => params[:value])
    end
    @value = params[:value]
  end
  
  def create
    @from = 'exam'
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @exam_attrs = params[:exam_group][:exams_attributes]
#    @batches = []
#    unless params[:class_exam][:class_exam].nil? or params[:class_exam][:class_exam].empty?
#      if params[:class_exam][:class_exam].to_i == 1
#        @course_name = @batch.course.course_name
#        @batch_name = @batch.name
#        @tmp_courses = Course.find(:all, :conditions => ["courses.course_name LIKE (?) and batches.name = ?", @course_name, @batch_name], :select => "batches.id", :joins=> "INNER JOIN `batches` ON batches.course_id = courses.id")
#        @batches = @tmp_courses.map{|tc| tc.id}
#      end
#    end
    
    this_id = @batch.id
    
    @is_class_exam = false
    @is_batch_exam = false
    unless params[:class_exam].nil?
      unless params[:class_exam][:class_exam].nil?
        if params[:class_exam][:class_exam].to_i == 1
          @is_class_exam = params[:class_exam][:class_exam]
        end
      end
      unless params[:class_exam][:batch_exam].nil?
        if params[:class_exam][:batch_exam].to_i == 1
          @is_batch_exam = true
        end
      end
    end
    
    if @is_class_exam
      @batch_name = @batch.name
      @course = @batch.course
      
      @course_name = @course.course_name
      
      batch_name_tmp = @is_batch_exam ? nil : @batch_name
      @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
    end
    
    @exam_data = {}
    if @is_class_exam
      tmp_subject = Subject.new()
      @exam_attributes = tmp_subject.getExamSubjects(@exam_attrs, @batch.id, @batches)
      
      @exam_data = {"name" => params[:exam_group][:name],"is_current" => 1, "exam_category" => params[:exam_group][:exam_category], "quarter" => params[:exam_group][:quarter], "attandence_start_date" => params[:exam_group][:attandence_start_date], "attandence_end_date" => params[:exam_group][:attandence_end_date], "topic" => params[:exam_group][:topic], "exam_type" => params[:exam_group][:exam_type], "maximum_marks" => params[:exam_group][:maximum_marks], "minimum_marks" => params[:exam_group][:minimum_marks], "exams_attributes" => @exam_attributes}
    else
      @exam_data = {"name" => params[:exam_group][:name],"is_current" => 1, "exam_category" => params[:exam_group][:exam_category], "quarter" => params[:exam_group][:quarter], "attandence_start_date" => params[:exam_group][:attandence_start_date], "attandence_end_date" => params[:exam_group][:attandence_end_date], "topic" => params[:exam_group][:topic], "exam_type" => params[:exam_group][:exam_type], "maximum_marks" => params[:exam_group][:maximum_marks], "minimum_marks" => params[:exam_group][:minimum_marks], "exams_attributes" => params[:exam_group][:exams_attributes]}
    end
    
    @exam_group = ExamGroup.new(@exam_data)
    
    @exam_group.batch_id = @batch.id
    @type = @exam_group.exam_type
    @error=false
    unless @type=="Grades"
      params[:exam_group][:exams_attributes].each do |exam|
        if exam[1][:_delete].to_s=="0" and @error==false
          unless exam[1][:maximum_marks].present?
            @exam_group.errors.add_to_base("#{t('maxmarks_cant_be_blank')}")
            @error=true
          end
          unless exam[1][:minimum_marks].present?
            @exam_group.errors.add_to_base("#{t('minmarks_cant_be_blank')}")
            @error=true
          end
        end
      end
    end
    
    if @error==false and @exam_group.save
      unless @batches.nil? or @batches.empty?
        @batches.each do |batch_id|
          @exam_data = {}
          if batch_id != this_id
            if @is_class_exam
              tmp_subject = Subject.new()
              @exam_attributes = tmp_subject.getExamSubjects(@exam_attrs, batch_id, @batches)
              @exam_data = {"name" => params[:exam_group][:name],"is_current" => 1, "exam_category" => params[:exam_group][:exam_category], "quarter" => params[:exam_group][:quarter], "attandence_start_date" => params[:exam_group][:attandence_start_date], "attandence_end_date" => params[:exam_group][:attandence_end_date], "topic" => params[:exam_group][:topic], "exam_type" => params[:exam_group][:exam_type], "maximum_marks" => params[:exam_group][:maximum_marks], "minimum_marks" => params[:exam_group][:minimum_marks], "exams_attributes" => @exam_attributes}
            else
              @exam_data = {"name" => params[:exam_group][:name],"is_current" => 1, "exam_category" => params[:exam_group][:exam_category], "quarter" => params[:exam_group][:quarter], "attandence_start_date" => params[:exam_group][:attandence_start_date], "attandence_end_date" => params[:exam_group][:attandence_end_date], "topic" => params[:exam_group][:topic], "exam_type" => params[:exam_group][:exam_type], "maximum_marks" => params[:exam_group][:maximum_marks], "minimum_marks" => params[:exam_group][:minimum_marks], "exams_attributes" => params[:exam_group][:exams_attributes]}
            end
            
            
            @exam_group = ExamGroup.new(@exam_data)
            @exam_group.batch_id = batch_id
            @exam_group.save
          end
        end
      end
      flash[:notice] =  "#{t('flash1')}"
      if @is_class_exam
        if @is_batch_exam
          redirect_to batch_exam_groups_path(@batch, :class_exam => true, :batch_exam => true  )
        else
          redirect_to batch_exam_groups_path(@batch, :class_exam => true  )
        end
      else
        redirect_to batch_exam_groups_path(@batch)
      end
    else
      @cce_exam_categories = CceExamCategory.all if @batch.cce_enabled?
      render 'new'
    end
  end

  def edit
    @from = 'exam'
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @exam_group = ExamGroup.find params[:id]
    @cce_exam_categories = CceExamCategory.all if @batch.cce_enabled?
  end

  def update
    @from = 'exam'
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @exam_group = ExamGroup.find params[:id]
    if @exam_group.update_attributes(params[:exam_group])
      flash[:notice] = "#{t('flash2')}"
      redirect_to [@batch, @exam_group]
    else
      @cce_exam_categories = CceExamCategory.all if @batch.cce_enabled?
      render 'edit'
    end
  end

  def destroy
    @from = 'exam'
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @is_class_exam = false
    unless params[:class_exam].nil?
      @is_class_exam = true
    end
    
    @is_batch_exam = false
    unless params[:batch_exam].nil?
      @is_batch_exam = true
    end
    
    @exam_group = ExamGroup.find(params[:id], :include => :exams)
    
    if @is_class_exam
      @batch_name = @batch.name
      @course = @batch.course
      
      @course_name = @course.course_name
      
      batch_name_tmp = @is_batch_exam ? nil : @batch_name
      @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
    end
    
    if @current_user.employee?
      @employee_subjects= @current_user.employee_record.subjects.map { |n| n.id}
      if @employee_subjects.empty? and !@current_user.privileges.map{|p| p.name}.include?("ExaminationControl") and !@current_user.privileges.map{|p| p.name}.include?("EnterResults")
        flash[:notice] = "#{t('flash_msg4')}"
        redirect_to :controller => 'user', :action => 'dashboard'
      end
    end 
    
    deleted = false
    if @is_class_exam
       @exam_groups = ExamGroup.find(:all, :conditions => ["name LIKE ? and batch_id IN (?) and exam_type = ? and exam_category = ? and exam_date = ?", @exam_group.name, @batches, @exam_group.exam_type, @exam_group.exam_category, @exam_group.exam_date]).map{|eg| eg.id}
       @exam_groups.each do |exam_group_id|
         @t_exam_group = ExamGroup.find(exam_group_id, :include => :exams)
         if @t_exam_group.destroy
           deleted = true
           flash[:notice] = "#{t('flash3')}"
         end
       end
    else
      if @exam_group.destroy
        deleted = true
        flash[:notice] = "#{t('flash3')}"
      end
    end
    
    if @is_class_exam
      if @is_batch_exam
        redirect_to batch_exam_groups_path(@batch, :class_exam => true, :batch_exam => true)
      else  
        redirect_to batch_exam_groups_path(@batch, :class_exam => true)
      end
    else  
      redirect_to batch_exam_groups_path(@batch)
    end
  end

  def show
    @from = 'exam'
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @is_class_exam = false
    unless params[:class_exam].nil?
      @is_class_exam = true
    end
    
    @is_batch_exam = false
    unless params[:batch_exam].nil?
      @is_batch_exam = true
    end
    
    if @is_class_exam
      @batch_name = @batch.name
      @course = @batch.course
      
      @course_name = @course.course_name
      
      batch_name_tmp = @is_batch_exam ? nil : @batch_name
      @batches = @course.find_batches_data(batch_name_tmp, @course.course_name)
      @enabled_subject_link = false
    else  
      @enabled_subject_link = true
    end
     
    @exam_group = ExamGroup.find(params[:id], :include => :exams)
    
    if @is_class_exam
      @exam_group_name = @exam_group.name
      @exam_groups = ExamGroup.find(:all, :conditions => ["name LIKE ? and batch_id IN (?) and exam_type = ? and exam_category = ? and exam_date = ?", @exam_group.name, @batches, @exam_group.exam_type, @exam_group.exam_category, @exam_group.exam_date]).map{|eg| eg.id}
      
      @exams = Exam.find(:all, :conditions => ["exam_group_id IN (?)", @exam_groups], :joins => "INNER JOIN subjects ON subjects.id = exams.subject_id", :group => "subjects.name, exams.start_time, exams.end_time, exams.maximum_marks, exams.minimum_marks",:order => "exams.start_time asc")
    else
      @exams  = @exam_group.exams
    end    
        
    if @current_user.employee?
      @user_privileges = @current_user.privileges
      @employee_subjects= @current_user.employee_record.subjects.map { |n| n.id}
      if @employee_subjects.empty? and !@current_user.privileges.map{|p| p.name}.include?("ExaminationControl") and !@current_user.privileges.map{|p| p.name}.include?("EnterResults")
        flash[:notice] = "#{t('flash_msg4')}"
        redirect_to :controller => 'user', :action => 'dashboard'
      end
    end
  end

  private
  def initial_queries
    @batch = Batch.find params[:batch_id], :include => :course unless params[:batch_id].nil?
    @course = @batch.course unless @batch.nil?
  end

  def protect_other_batch_exams
    @user_privileges = @current_user.privileges
    if !@current_user.admin? and !@user_privileges.map{|p| p.name}.include?('ExaminationControl') and !@user_privileges.map{|p| p.name}.include?('EnterResults')
      @user_subjects = @current_user.employee_record.subjects.all(:group => 'batch_id')
      @user_batches = @user_subjects.map{|x|x.batch_id} unless @current_user.employee_record.blank? or @user_subjects.nil?

      unless @user_batches.include?(params[:batch_id].to_i)
        flash[:notice] = "#{t('flash_msg4')}"
        redirect_to :controller => 'user', :action => 'dashboard'
      end
    end
  end
end