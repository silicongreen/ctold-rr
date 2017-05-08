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

class ExamsController < ApplicationController
  before_filter :login_required
  before_filter :query_data
  before_filter :protect_other_student_data
  before_filter :restrict_employees_from_exam, :except=>[:edit, :destroy]
  before_filter :restrict_employees_from_exam_edit, :only=>[:edit, :destroy]
  filter_access_to :all, :except=>[:show,:save_scores]
  filter_access_to [:show,:save_scores], :attribute_check=>true, :load_method => lambda { current_user }

  def new
    @from = 'exam' 
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @exam = Exam.new
    
    @is_class_exam = false
    unless params[:class_exam].nil?
      if params[:class_exam]
        @is_class_exam = true
      end
    end
    
    @is_batch_exam = false
    unless params[:batch_exam].nil?
      @is_batch_exam = true
    end
    
    @exam_group = ExamGroup.active.find params[:exam_group_id]
    
    if @is_class_exam
      @batch_name = @batch.name
      @course = @batch.course
      
      @course_name = @course.course_name
      
      batch_name_tmp = @is_batch_exam ? nil : @batch_name
      @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
    end
    
    if @is_class_exam
      @subject_ids = Subject.find(:all, :conditions => ["batch_id IN (?)", @batches]).map{|s| s.id}
      @exam_groups = ExamGroup.active.find(:all, :conditions => ["name LIKE ? and batch_id IN (?) and exam_type = ? and exam_category = ? and exam_date = ?", @exam_group.name, @batches, @exam_group.exam_type, @exam_group.exam_category, @exam_group.exam_date]).map{|eg| eg.id}
      @tmp_subjects = Exam.find(:all, :conditions => ["exam_group_id IN (?)", @exam_groups]).map{|s| s.subject_id}
      @subject_ids.reject!{|s| (@tmp_subjects.include?(s))}
      @subjects = Subject.find(:all, :conditions => ["id IN (?) and batch_id IN (?)",@subject_ids, @batches], :group => 'name')
      
      if @current_user.employee?  and !@current_user.privileges.map{|m| m.name}.include?("ExaminationControl")
        if @is_class_exam
          @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id IN (#{@batches.join(',')}) ", :group => "subjects.name")
        else
          @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id = #{@batch.id} ")
        end
      end
      
    else
      @subjects = @batch.subjects
      
      if @current_user.employee?  and !@current_user.privileges.map{|m| m.name}.include?("ExaminationControl")
        if @is_class_exam
          @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id IN (#{@batches.join(',')}) ", :group => "subjects.name")
        else
          @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id = #{@batch.id} ")
        end
      end
      
      @subjects.reject!{|s| (@exam_group.exams.map{|e| e.subject_id}.include?(s.id))}
    end
    
    if @subjects.blank?
      flash[:notice] = "#{t('flash_msg44')}"
      if @is_class_exam
        if @is_batch_exam
          redirect_to '/batches/' + @batch.id.to_s + '/exam_groups/' + @exam_group.id.to_s + '?class_exam=true&batch_exam=true'
        else
          redirect_to '/batches/' + @batch.id.to_s + '/exam_groups/' + @exam_group.id.to_s + '?class_exam=true'
        end
      else
        redirect_to [@batch, @exam_group]
      end
    end
  end

  def create
    @from = 'exam'
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @is_class_exam = false
    
    unless params[:class_exam][:class_exam].nil?
      if params[:class_exam][:class_exam].to_i == 1
        @is_class_exam = true
      end
    end
    
    @is_batch_exam = false
    unless params[:class_exam][:batch_exam].nil?
      if params[:class_exam][:batch_exam].to_i == 1
        @is_batch_exam = true
      end
    end
    
    if @is_class_exam
      @batch_name = @batch.name
      @course = @batch.course
      
      @course_name = @course.course_name
      
      batch_name_tmp = @is_batch_exam ? nil : @batch_name
      @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
    end
    
    saved = false
    if @is_class_exam
      @exam = Exam.new(params[:exam])
      @exam_group = ExamGroup.active.find params[:exam_group_id]
      tmp_subject_id = params[:exam][:subject_id]
      tmp_subject = Subject.find tmp_subject_id
      
      found_elective = false

      unless tmp_subject.elective_group_id.nil?
        elective_group_id = tmp_subject.elective_group_id
        elective = ElectiveGroup.find elective_group_id
        elective_group_name = elective.name
        elective_active_batch_ids = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?) and is_deleted = 0", elective_group_name, @batches]).map{|e| e.id}
        found_elective = true
      end
      
      if found_elective
        ar_subjects = Subject.find(:all, :conditions => ["name like ? and batch_id IN (?) and elective_group_id IN (?) and is_deleted = 0", tmp_subject.name, @batches, elective_active_batch_ids ], :group => "batch_id").map{|s| [s.id, s.batch_id]}
      else
        ar_subjects = Subject.find(:all, :conditions => ["name like ? and batch_id IN (?) and is_deleted = 0", tmp_subject.name, @batches ], :group => "batch_id").map{|s| [s.id, s.batch_id]}
      end
      
      @exam_groups = ExamGroup.active.find(:all, :conditions => ["name LIKE ? and batch_id IN (?) and exam_type = ? and exam_category = ? and exam_date = ?", @exam_group.name, @batches, @exam_group.exam_type, @exam_group.exam_category, @exam_group.exam_date]).map{|eg| [eg.id, eg.batch_id]}
      @error=false
      @exam_groups.each do |eg|
        tmp_exam_grp = ExamGroup.active.find eg[0]
        unless tmp_exam_grp.exam_type=="Grades"
          unless params[:exam][:maximum_marks].present?
            @exam.errors.add_to_base("#{t('maxmarks_cant_be_blank')}")
            @error=true
          end
          unless params[:exam][:minimum_marks].present?
            @exam.errors.add_to_base("#{t('minmarks_cant_be_blank')}")
            @error=true
          end
        end
      end
      
      if @error==false
        @exam_groups.each do |eg|
          @tmp_exam = Exam.new
          @tmp_exam.minimum_marks = params[:exam][:minimum_marks]
          @tmp_exam.maximum_marks = params[:exam][:maximum_marks]
          @tmp_exam.start_time = params[:exam][:start_time]
          @tmp_exam.end_time = params[:exam][:end_time]
          @tmp_exam.minimum_marks = params[:exam][:minimum_marks]
          @tmp_exam.exam_group_id = eg[0]
          b_found_subject = false
          s_subject_id = 0
          ar_subjects.each do |s|
            if s[1] == eg[1]
              b_found_subject = true
              s_subject_id = s[0]
              break
            end
          end
          if b_found_subject
            @tmp_exam.subject_id = s_subject_id
            if @tmp_exam.save
              saved = true
            end
          end
        end
      end
    else
      @exam = Exam.new(params[:exam])
      @exam_group_id = params[:exam_group_id]
      @exam.exam_group_id = @exam_group.id
      @error=false
      
      unless @exam_group.exam_type=="Grades"
        unless params[:exam][:maximum_marks].present?
          @exam.errors.add_to_base("#{t('maxmarks_cant_be_blank')}")
          @error=true
        end
        unless params[:exam][:minimum_marks].present?
          @exam.errors.add_to_base("#{t('minmarks_cant_be_blank')}")
          @error=true
        end
      end
      
      if @error==false and @exam.save
        saved = true
      end
    end
    
    if saved
      flash[:notice] = "#{t('flash_msg10')}"
      if @is_class_exam
        if @is_batch_exam
          redirect_to '/batches/' + @batch.id.to_s + '/exam_groups/' + @exam_group.id.to_s + '?class_exam=true&batch_exam=true'
        else
          redirect_to '/batches/' + @batch.id.to_s + '/exam_groups/' + @exam_group.id.to_s + '?class_exam=true'
        end
      else
        redirect_to [@batch, @exam_group]
      end
    else
      if @is_class_exam
        @subject_ids = Subject.find(:all, :conditions => ["batch_id IN (?)", @batches]).map{|s| s.id}
        @exam_groups = ExamGroup.active.find(:all, :conditions => ["name LIKE ? and batch_id IN (?) and exam_type = ? and exam_category = ? and exam_date = ?", @exam_group.name, @batches, @exam_group.exam_type, @exam_group.exam_category, @exam_group.exam_date]).map{|eg| eg.id}
        @tmp_subjects = Exam.find(:all, :conditions => ["exam_group_id IN (?)", @exam_groups]).map{|s| s.subject_id}
        @subject_ids.reject!{|s| (@tmp_subjects.include?(s))}
        @subjects = Subject.find(:all, :conditions => ["id IN (?) and batch_id IN (?)",@subject_ids, @batches], :group => 'name')

        if @current_user.employee?  and !@current_user.privileges.map{|m| m.name}.include?("ExaminationControl")
          if @is_class_exam
            @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id IN (#{@batches.join(',')}) ", :group => "subjects.name")
          else
            @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id = #{@batch.id} ")
          end
        end

      else
        @subjects = @batch.subjects

        if @current_user.employee?  and !@current_user.privileges.map{|m| m.name}.include?("ExaminationControl")
          if @is_class_exam
            @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id IN (#{@batches.join(',')}) ", :group => "subjects.name")
          else
            @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id = #{@batch.id} ")
          end
        end

        @subjects.reject!{|s| (@exam_group.exams.map{|e| e.subject_id}.include?(s.id))}
      end

      if @subjects.blank?
        flash[:notice] = "#{t('flash_msg44')}"
        if @is_class_exam
          if @is_batch_exam
            redirect_to '/batches/' + @batch.id.to_s + '/exam_groups/' + @exam_group.id.to_s + '?class_exam=true&batch_exam=true'
          else
            redirect_to '/batches/' + @batch.id.to_s + '/exam_groups/' + @exam_group.id.to_s + '?class_exam=true'
          end
        else
          redirect_to [@batch, @exam_group]
        end
      end
      render 'new'
    end
  end

  def edit
    @from = 'exam'
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @is_class_exam = false
    unless params[:class_exam].nil?
      if params[:class_exam]
        @is_class_exam = true
      end
    end
    
    @is_batch_exam = false
    unless params[:batch_exam].nil?
      @is_batch_exam = true
    end
    
    @exam = Exam.find params[:id], :include => :exam_group
    
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
      @subjects = Subject.find(:all, :conditions => ["batch_id IN (?)", @batches], :group => "name")
    else
      @subjects = @exam_group.batch.subjects
    end
    
    if @current_user.employee?  and !@current_user.privileges.map{|m| m.name}.include?("ExaminationControl")
      if @is_class_exam
        @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id IN (#{@batches.join(',')}) ", :group => "subjects.name")
      else
        @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id = #{@batch.id} ")
      end
      unless @subjects.map{|m| m.id}.include?(@exam.subject_id)
        flash[:notice] = "#{t('flash_msg4')}"
        redirect_to [@batch, @exam_group]
      end
    end
  end

  def update
    @from = 'exam'
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @is_class_exam = false
    
    unless params[:class_exam][:class_exam].nil?
      if params[:class_exam][:class_exam].to_i == 1
        @is_class_exam = true
      end
    end
    
    @is_batch_exam = false
    unless params[:class_exam][:batch_exam].nil?
      if params[:class_exam][:batch_exam].to_i == 1
        @is_batch_exam = true
      end
    end
    
    @exam_group = ExamGroup.active.find params[:exam_group_id]
    @batch = Batch.find @exam_group.batch_id
    
    if @is_class_exam
      @batch_name = @batch.name
      @course = @batch.course
      
      @course_name = @course.course_name
      
      batch_name_tmp = @is_batch_exam ? nil : @batch_name
      @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
    end
    
    @updated = false
    if @is_class_exam
      @exam = Exam.find(params[:id])
    
      @exam_group = ExamGroup.active.find @exam.exam_group_id
      @tmp_subject = Subject.find @exam.subject_id
      @exam_group_name = @exam_group.name
      @exam_groups = ExamGroup.active.find(:all, :conditions => ["name LIKE ? and batch_id IN (?) and exam_type = ? and exam_category = ? and exam_date = ?", @exam_group.name, @batches, @exam_group.exam_type, @exam_group.exam_category, @exam_group.exam_date]).map{|eg| eg.id}
      @tmp_subjects = Subject.find(:all, :conditions => ["name LIKE ? and batch_id IN (?)", @tmp_subject.name, @batches]).map{|s| s.id}
      
      tmp_subject_id = params[:exam][:subject_id]
      t_subject = Subject.find tmp_subject_id
      @subjects_data = Subject.find(:all, :conditions => ["name LIKE ? and batch_id IN (?)", t_subject.name, @batches]).map{|s| [s.id, s.batch_id]}
      
      @exam_groups.each do |exam_group_id|
        @tmp_subjects.each do |subject_id|
          @t_exam_group = ExamGroup.active.find exam_group_id
          b_found = false
          s_id = 0
          tmp_exam = Exam.find_by_exam_group_id_and_subject_id_and_start_time_and_end_time_and_maximum_marks_and_minimum_marks(exam_group_id, subject_id, @exam.start_time, @exam.end_time, @exam.maximum_marks, @exam.minimum_marks)
          unless tmp_exam.nil?
            @subjects_data.each do |s|
              if s[1] == @t_exam_group.batch_id
                b_found = true
                s_id = s[0]
              end
            end
            if b_found
              if tmp_exam.update_attributes(:subject_id => s_id, :minimum_marks => params[:exam][:minimum_marks], :end_time => params[:exam][:end_time], :start_time => params[:exam][:start_time], :maximum_marks => params[:exam][:maximum_marks])
                @updated = true
              end
            else
              if tmp_exam.destroy
                batch_id = @t_exam_group.batch_id
                batch_event = BatchEvent.find_by_event_id_and_batch_id(tmp_exam.event_id,batch_id)
                event = Event.find_by_id(tmp_exam.event_id)
                unless event.nil?
                  event.destroy
                end
                unless batch_event.nil?
                  batch_event.destroy
                end
              end
            end
          end
        end
      end
    else  
      @exam = Exam.find params[:id], :include => :exam_group
      if @exam.update_attributes(params[:exam])
        @updated = true
      end
    end
    
    if @updated
      flash[:notice] = "#{t('flash1')}"
      if @is_class_exam
        if @is_batch_exam
          redirect_to '/batches/' + @batch.id.to_s + '/exam_groups/' + @exam_group.id.to_s + '?class_exam=true&batch_exam=true'
        else
          redirect_to '/batches/' + @batch.id.to_s + '/exam_groups/' + @exam_group.id.to_s + '?is_class_exam=true'
        end
      else
        redirect_to [@batch, @exam_group]
      end
    else
      if @is_class_exam
        @subjects = Subject.find(:all, :conditions => ["batch_id IN (?)", @batches], :group => "name")
      else  
        @subjects = @batch.subjects
      end
      
      if @current_user.employee? and  !@current_user.privileges.map{|m| m.name}.include?("ExaminationControl")
        if @is_class_exam
          @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id IN (#{@batches.join(',')}) ", :group => "subjects.name")
        else
          @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id = #{@batch.id} ")
        end
      end
      render 'edit'
    end
  end

  def show
    @from = 'exam'
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @employee_subjects=[]
    @employee_subjects= @current_user.employee_record.subjects.map { |n| n.id} if @current_user.employee?
    @exam = Exam.find params[:id], :include => :exam_group
    unless @employee_subjects.include?(@exam.subject_id) or @current_user.admin? or @current_user.privileges.map{|p| p.name}.include?('ExaminationControl') or @current_user.privileges.map{|p| p.name}.include?('EnterResults')
      flash[:notice] = "#{t('flash_msg6')}"
      redirect_to :controller=>"user", :action=>"dashboard"
    end
    exam_subject = Subject.find(@exam.subject_id)
    is_elective = exam_subject.elective_group_id
    if is_elective == nil
      if MultiSchool.current_school.id == 319
        @students = @batch.students.by_first_name
      else
        @students = @batch.students.by_roll_number_name
      end
    else
      assigned_students = StudentsSubject.find_all_by_subject_id(exam_subject.id)
      @students = {}
      assigned_students.each do |s|
        student = Student.find_by_id(s.student_id)
        unless student.nil?
          if student.batch_id.to_i == s.batch_id
            if MultiSchool.current_school.id == 319
              @students.push [student.first_name,student.last_name, student.id, student] unless student.nil?
            else
              @students.push [student.class_roll_no.to_i,student.first_name, student.id, student] unless student.nil? 
            end
          end
        end
      end
      unless @students.blank?
        @ordered_students = @students.sort
        @students=[]
        @ordered_students.each do|s|
          @students.push s[3]
        end
      end
    end
    @config = Configuration.get_config_value('ExamResultType') || 'Marks'

    @grades = @batch.grading_level_list
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
    
    @exam_group = ExamGroup.active.find params[:exam_group_id]
    
    if @is_class_exam
      @batch_name = @batch.name
      @course = @batch.course
      
      @course_name = @course.course_name
      
      batch_name_tmp = @is_batch_exam ? nil : @batch_name
      @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
    end
    
    deleted = false
    if @is_class_exam
      @exam = Exam.find(params[:id])
    
      @tmp_subject = Subject.find @exam.subject_id
      @exam_group_name = @exam_group.name
      @exam_groups = ExamGroup.active.find(:all, :conditions => ["name LIKE ? and batch_id IN (?) and exam_type = ? and exam_category = ? and exam_date = ?", @exam_group.name, @batches, @exam_group.exam_type, @exam_group.exam_category, @exam_group.exam_date]).map{|eg| eg.id}
      @tmp_subjects = Subject.find(:all, :conditions => ["name LIKE ? and batch_id IN (?)", @tmp_subject.name, @batches]).map{|s| s.id}
      if @current_user.employee? and  !@current_user.privileges.map{|m| m.name}.include?("ExaminationControl")
        @subjects = Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id IN (#{@batches.join(',')}) ", :group => "subjects.name").map{|s| s.id}
      end
      can_delete = true
      unless @subjects.nil?
        @subjects.each do |s|
          if @tmp_subjects.include?(s)
            can_delete = false
            break
          end
        end
      end
      if can_delete == false
        flash[:notice] = "#{t('flash_msg4')}"
        redirect_to [@batch, @exam_group] and return
      else
        @exam_groups.each do |exam_group_id|
          @tmp_subjects.each do |subject_id|
            @t_exam_group = ExamGroup.active.find exam_group_id
            batch_id = @t_exam_group.batch_id
            tmp_exam = Exam.find_by_exam_group_id_and_subject_id_and_start_time_and_end_time_and_maximum_marks_and_minimum_marks(exam_group_id, subject_id, @exam.start_time, @exam.end_time, @exam.maximum_marks, @exam.minimum_marks)
            unless tmp_exam.nil?
              if tmp_exam.destroy
                batch_event = BatchEvent.find_by_event_id_and_batch_id(tmp_exam.event_id,batch_id)
                event = Event.find_by_id(tmp_exam.event_id)
                unless event.nil?
                  event.destroy
                end
                unless batch_event.nil?
                  batch_event.destroy
                end
                flash[:notice] = "#{t('flash5')}"
                deleted = true
              end
            end
          end
        end
      end
    else
      @exam = Exam.find params[:id], :include => :exam_group
      if @current_user.employee?  and !@current_user.privileges.map{|m| m.name}.include?("ExaminationControl")
        @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id = #{@batch.id} ")
        unless @subjects.map{|m| m.id}.include?(@exam.subject_id)
          flash[:notice] = "#{t('flash_msg4')}"
          redirect_to [@batch, @exam_group] and return
        end
      end
      if @exam.destroy
        batch_id = @exam.exam_group.batch_id
        batch_event = BatchEvent.find_by_event_id_and_batch_id(@exam.event_id,batch_id)
        event = Event.find_by_id(@exam.event_id)
        unless event.nil?
          event.destroy
        end
        unless batch_event.nil?
          batch_event.destroy
        end
        flash[:notice] = "#{t('flash5')}"
        deleted = true
      end
    end
    
    if deleted
      if @is_class_exam
        if @is_batch_exam
          redirect_to '/batches/' + @batch.id.to_s + '/exam_groups/' + @exam_group.id.to_s + '?class_exam=true&batch_exam=true'
        else
          redirect_to '/batches/' + @batch.id.to_s + '/exam_groups/' + @exam_group.id.to_s + '?class_exam=true'
        end
      else
        redirect_to [@batch, @exam_group]
      end
    else
      if @is_class_exam
        if @is_batch_exam
          redirect_to '/batches/' + @batch.id.to_s + '/exam_groups/' + @exam_group.id.to_s + '?class_exam=true&batch_exam=true'
        else
          redirect_to '/batches/' + @batch.id.to_s + '/exam_groups/' + @exam_group.id.to_s + '?class_exam=true'
        end
      else
        redirect_to [@batch, @exam_group]
      end
    end
  end

  def save_scores
    @exam = Exam.find(params[:id])
    @error= false
    params[:exam].each_pair do |student_id, details|
      @exam_score = ExamScore.find(:first, :conditions => {:exam_id => @exam.id, :student_id => student_id} )
      if @exam_score.nil?
        unless details[:marks].nil? 
          if details[:marks].to_f <= @exam.maximum_marks.to_f
            ExamScore.create do |score|
              score.exam_id          = @exam.id
              score.student_id       = student_id
              score.marks            = details[:marks]
              score.grading_level_id = details[:grading_level_id]
              score.remarks          = details[:remarks]
            end
          else
            @error = true
          end
        end
      else
        if details[:marks].to_f <= @exam.maximum_marks.to_f
          if @exam_score.update_attributes(details)
          else
            flash[:warn_notice] = "#{t('flash4')}"
            @error = nil
          end
        else
          @error = true
        end
      end
    end
    flash[:warn_notice] = "#{t('flash2')}" if @error == true
    flash[:notice] = "#{t('flash3')}" if @error == false
    redirect_to [@exam_group, @exam]
  end


  private
  def query_data
    @exam_group = ExamGroup.active.find(params[:exam_group_id], :include => :batch)
    @batch = @exam_group.batch
    @course = @batch.course
  end

  def restrict_employees_from_exam_edit
    if @current_user.employee?
      if !@current_user.privileges.map{|p| p.name}.include?("ExaminationControl")
        flash[:notice] = "#{t('flash_msg4')}"
        redirect_to :back
      else
        @allow_for_exams = true
      end
    end
  end
end
