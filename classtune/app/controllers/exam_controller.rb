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

class ExamController < ApplicationController
  #include 'action_view/helpers/text_helper'
  include ActionView::Helpers::TextHelper
  before_filter :login_required
  before_filter :check_permission, :only=>[:index,:settings,:create_exam,:generate_reports,:report_center]
  before_filter :protect_other_student_data, :except=>[:student_exam_schedule,:edit_exam_group,:student_exam_schedule_view,:exam_schedule_pdf]
  before_filter :restrict_employees_from_exam
  before_filter :default_time_zone_present_time
  filter_access_to :all, :except=>[:index,:create_exam,:update_batch,:exam_wise_report,:list_exam_types,:generated_report,:graph_for_generated_report,
    :generated_report_pdf,:student_wise_generated_report,:consolidated_exam_report,:consolidated_exam_report_pdf,:subject_wise_report,
    :subject_rank,:course_rank,:batch_groups,:student_course_rank,:student_course_rank_pdf,:student_school_rank,:student_school_rank_pdf,
    :attendance_rank,:student_attendance_rank,:student_attendance_rank_pdf,:report_center,:gpa_cwa_reports,:list_batch_groups,:ranking_level_report,
    :student_ranking_level_report,:student_ranking_level_report_pdf,:transcript,:student_transcript,:student_transcript_pdf,:combined_report,:load_levels,
    :student_combined_report,:student_combined_report_pdf,:load_batch_students,:select_mode,:select_batch_group,:select_type,:select_report_type,:batch_rank,
    :student_batch_rank,:student_batch_rank_pdf,:student_subject_rank,:student_subject_rank_pdf,:list_subjects,:list_batch_subjects,:generated_report2,
    :generated_report2_pdf,:grouped_exam_report,:final_report_type,:generated_report4,:generated_report4_pdf,:combined_grouped_exam_report_pdf]
    
  
  filter_access_to [:index,:create_exam,:update_batch,:exam_wise_report,:list_exam_types,:generated_report,:graph_for_generated_report,
    :generated_report_pdf,:student_wise_generated_report,:consolidated_exam_report,:consolidated_exam_report_pdf,:subject_wise_report,
    :subject_rank,:course_rank,:batch_groups,:student_course_rank,:student_course_rank_pdf,:student_school_rank,:student_school_rank_pdf,
    :attendance_rank,:student_attendance_rank,:student_attendance_rank_pdf,:report_center,:gpa_cwa_reports,:list_batch_groups,:ranking_level_report,
    :student_ranking_level_report,:student_ranking_level_report_pdf,:transcript,:student_transcript,:student_transcript_pdf,:combined_report,:load_levels,
    :student_combined_report,:student_combined_report_pdf,:load_batch_students,:select_mode,:select_batch_group,:select_type,:select_report_type,:batch_rank,
    :student_batch_rank,:student_batch_rank_pdf,:student_subject_rank,:student_subject_rank_pdf,:list_subjects,:list_batch_subjects,:generated_report2,
    :generated_report2_pdf,:grouped_exam_report,:final_report_type,:generated_report4,:generated_report4_pdf,:combined_grouped_exam_report_pdf],:attribute_check=>true, :load_method => lambda { current_user }
  
  def index
    permitted_modules = Rails.cache.fetch("permitted_modules_exam_#{current_user.id}"){ 
      @exam_modules_tmp = []
      @a_user_modules = ['examination']
      menu_links = MenuLink.find_by_name(@a_user_modules)
      menu_id = menu_links.id
      menu_links = MenuLink.find_all_by_higher_link_id(menu_id)
      
      menu_links.each do |menu_link|
        if menu_link.link_type=="user_menu" and menu_link.target_controller=="exam"
          menu_id = menu_link.id

          school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")

          if school_menu_links.nil? or school_menu_links.blank?
            @exam_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => false}
          else
            @exam_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => true}
          end
        end
      end
      @exam_modules_tmp
    }
    @exam_modules = permitted_modules
    
  end
  
  def edit_exam_group
    @examgroup = ExamGroup.find_by_id(params[:id])
    @batch = @examgroup.batch
    if request.post? and @examgroup.update_attributes(params[:exam_group]) 
      flash[:notice] = "Successfully Saved"
      redirect_to batch_exam_groups_path(@batch)
    end
  end
  
  def split_pdf_and_save_group  
    @exam_group = ExamGroup.active.find(params[:exam_group])
    
    
    if @exam_group.is_current == false
      student_list = []
      allExam = @exam_group.exams
      allExam.each do |exams|
        score_data = exams.exam_scores
        score_data.each do |sd|
          student_list.push(sd.student_id) unless student_list.include?(sd.student_id)
        end          
      end
      if student_list.nil?
        flash[:notice] = "#{t('flash_student_notice')}"
        redirect_to :action => 'exam_wise_report' and return
      end

      @batch = @exam_group.batch
      @students = Student.find_all_by_id(student_list)
    else
      @batch = @exam_group.batch
      @students=@batch.students.by_first_name
    end
    
    @students.sort! { |a, b|  a.class_roll_no.to_i <=> b.class_roll_no.to_i }
    
    @assigned_employee=@batch.all_class_teacher
    general_subjects = Subject.find_all_by_batch_id(@batch.id, :conditions=>"elective_group_id IS NULL and is_deleted=0")
    student_electives = StudentsSubject.find_all_by_batch_id(@batch.id)
    elective_subjects = []
    elective_subjects_id = []
    student_electives.each do |elect|
      if !elective_subjects_id.include?(elect.subject_id)
        elective_subjects_id << elect.subject_id
        subject = Subject.find_by_id(elect.subject_id, :conditions=>"is_deleted=0")
        unless subject.blank?
          elective_subjects.push subject
        end
      end     
    end
    
    @subjects = general_subjects + elective_subjects
    @subjects.sort! { |a, b|  a.priority.to_i <=> b.priority.to_i }
    @exams = []
    @subjects.each do |sub|
      exam = Exam.find_by_exam_group_id_and_subject_id(@exam_group.id,sub.id)
      @exams.push exam unless exam.nil?
    end
    
    if !@exam_group.attandence_start_date.blank? and !@exam_group.attandence_end_date.blank?
      
      @academic_days = @batch.find_working_days(@exam_group.attandence_start_date.to_date,@exam_group.attandence_end_date.to_date).select{|v| v<=@exam_group.attandence_end_date.to_date}.count
      @student_leaves = Attendance.find(:all,:conditions =>{:batch_id=>@batch.id,:month_date => @exam_group.attandence_start_date..@exam_group.attandence_end_date})
    else
      @academic_days = @batch.find_working_days(@batch.start_date.to_date,@exam_group.exam_date.to_date).select{|v| v<=@exam_group.exam_date.to_date}.count
      @student_leaves = Attendance.find(:all,:conditions =>{:batch_id=>@batch.id,:month_date => @batch.start_date..@exam_group.exam_date})
    end 
    @exam_comments = ExamGroupComment.find_all_by_exam_group_id(@exam_group.id)
    if !@students.blank?
      @all_student_subject = StudentsSubject.find_all_by_batch_id(@batch.id)
      @all_subject_exam = @exams
      @students.each do |student|
        @student = student
        student_subject = []
        exam_new = []
        unless @subjects.blank? 
          @subjects.each do |subjects|
            if subjects['elective_group_id'].to_i!=0
              @all_student_subject.each do |sub_std|
                if subjects.id.to_i == sub_std.subject_id and student.id.to_i == sub_std.student_id
                  student_subject << subjects.id
                end 
              end 
            else
              student_subject << subjects.id 
            end 
          end
        end 

        unless @all_subject_exam.blank? 
          @all_subject_exam.each do |exam|
            if student_subject.include?(exam.subject_id) 
              exam_new << exam
            end 
          end
        end
        @exams = exam_new

        comments_student = []
        unless @exam_comments.blank? 
          @exam_comments.each do |cmt|
            if cmt.student_id = @student.id
              comments_student << cmt
            end 
          end
        end
        @exam_comment = comments_student


        on_leaves = 0;
        leaves_other = 0;
        leaves_full = 0;
        unless @student_leaves.empty?
          @student_leaves.each do |r|
            if r.student_id == @student.id
              working_days_count=@batch.find_working_days(r.month_date.to_date,r.month_date.to_date).select{|v| v<=r.month_date.to_date}.count
              if working_days_count==1
                if r.is_leave == true
                  on_leaves = on_leaves+1;
                elsif r.forenoon==true && r.afternoon==false
                  leaves_other = leaves_other+1;
                elsif r.forenoon==false && r.afternoon==true  
                  leaves_other = leaves_other+1;
                else
                  leaves_full = leaves_full+1;
                end 
              end
            end
          end
        end
        @late = leaves_other
        @absent = leaves_full
        @on_leave = on_leaves
        @present = @academic_days-on_leaves-leaves_full
        @absent = @academic_days-@present
        pdf_name = "group_exam_"+@exam_group.id.to_s+"_"+@student.id.to_s+".pdf"
        dirname = Rails.root.join('public','result_pdf_archive',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"examgroup","0"+@exam_group.id.to_s)
        unless File.directory?(dirname)
          FileUtils.mkdir_p(dirname)
          FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf_archive',"0"+MultiSchool.current_school.id.to_s))
        end
        render :pdf  => 'student_wise_generated_report',
          :save_to_file => Rails.root.join('public','result_pdf_archive',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"examgroup","0"+@exam_group.id.to_s,pdf_name),
          :save_only    => true
      end
    end
  end
  
  def split_pdf_and_save
    @id = params[:id] 

    @connect_exam_obj = ExamConnect.find(@id)
    @connect_exam = @id

    @batch = Batch.find(@connect_exam_obj.batch_id)
   
    
    @report_data = Rails.cache.fetch("continues_#{@id}_#{@batch.id}"){
      get_continues(@id,@batch.id)
      report_data = []
      if @student_response['status']['code'].to_i == 200
        report_data = @student_response['data']
      end
      report_data
    }

    @exam_comment_all = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
    if !@report_data.blank?
      @all_subject_exam = @report_data['report']['subjects'] 
      @all_student_subject = StudentsSubject.find_all_by_batch_id(@batch.id)
      @report_data['report']['students'].each do |std|
        
        subjects_new = []
        subjects_ids = []
        unless @all_subject_exam.blank? 
          @all_subject_exam.each do |subjects|
            if subjects['elective_group_id'].to_i!=0 && MultiSchool.current_school.id!=319 && MultiSchool.current_school.id!=324
              @all_student_subject.each do |sub_std|
                if subjects['id'].to_i == sub_std.subject_id and std['id'].to_i == sub_std.student_id and !subjects_ids.include?(subjects['id'].to_i)
                  subjects_new << subjects
                  subjects_ids << subjects['id'].to_i
                end 
              end 
            elsif !subjects_ids.include?(subjects['id'].to_i)
              subjects_new << subjects 
              subjects_ids << subjects['id'].to_i
            end 
          end
        end
        @report_data['report']['subjects'] = subjects_new
        @student = Student.find_by_id(std['id'].to_i)
        
        unless @student.blank?
          @report_data['report']['no_exam_subject_resutl'] = []
          @report_data['report']['exams'] = []
          @report_data['report']['comments'] = []
          @report_data['present'] = 0;
          if !@report_data['report']['all_result'][std['id']]['exams'].blank?
            
            
            @report_data['report']['exams'] = @report_data['report']['all_result'][std['id']]['exams']
            
            if !@report_data['report']['exam_comments'].blank? and !@report_data['report']['exam_comments'][std['id']]['comments'].blank?
              @report_data['report']['comments'] = @report_data['report']['exam_comments'][std['id']]['comments']
            end
            if !@report_data['report']['no_exam_comments'].blank? and !@report_data['report']['no_exam_comments'][std['id']]['no_exam_subject_resutl'].blank?
              @report_data['report']['no_exam_subject_resutl'] = @report_data['report']['no_exam_comments'][std['id']]['no_exam_subject_resutl']
            end 
            
            if !@report_data['present_all'].blank? and !@report_data['present_all'][std['id']].blank?
              @report_data['present'] = @report_data['present_all'][std['id']]
            end  
            
            @exam_comment = {}
            @exam_comment_all.each do |ec|
              if ec.student_id.to_i == std['id'].to_i
                @exam_comment = ec
                break
              end
            end
            pdf_name = "connect_exam_"+@connect_exam.to_s+"_"+@student.id.to_s+".pdf"
            dirname = Rails.root.join('public','result_pdf_archive',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"connectexam","0"+@connect_exam.to_s)
            unless File.directory?(dirname)
              FileUtils.mkdir_p(dirname)
              FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf_archive',"0"+MultiSchool.current_school.id.to_s))
            end
            file_name = Rails.root.join('public','result_pdf_archive',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"connectexam","0"+@connect_exam.to_s,pdf_name)
            render_connect_exam("split_pdf_and_save",true,file_name)
          end
        end
      end
    end  
    render :text=>"Saved"  
  end

  def update_exam_form
    @from = 'exam'
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @batch = Batch.find(params[:batch])
    @name = params[:exam_option][:name]
    
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
    @type = params[:exam_option][:exam_type]
    @exam_category = params[:exam_option][:exam_category]
    @quarter = params[:exam_option][:quarter]
    @attandence_start_date = params[:exam_option][:attandence_start_date]
    @attandence_end_date = params[:exam_option][:attandence_end_date]
    @topic = params[:exam_option][:topic]
    name=@batch.exam_groups.collect(&:name)
    #    if name.include?@name
    #      @error=true
    #    end
    @cce_exam_category_id = params[:exam_option][:cce_exam_category_id]
    @cce_exam_categories = CceExamCategory.all if @batch.cce_enabled?
    unless @name == '' or @error
      @exam_group = ExamGroup.new
      if @is_class_exam
        @batch_name = @batch.name
        @course = @batch.course
        
        batch_name_tmp = @is_batch_exam ? nil : @batch_name
        @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
        
        @normal_subjects = Subject.find(:all, :conditions=> ["elective_group_id IS NULL AND is_deleted = false and batch_id IN (?)", @batches], :group => "name")
        @elective_subjects = []
        
        elective_subjects = Subject.find(:all,:conditions=> ["elective_group_id IS NOT NULL AND is_deleted = false and batch_id IN (?)", @batches])
        
        elective = []
        elective_subjects.each do |e|
          is_assigned = StudentsSubject.find_all_by_subject_id(e.id)
          unless is_assigned.empty?
            unless elective.include?(e.code)
              @elective_subjects.push e
              elective << e.code
            end
          end
        end
        @all_subjects = @normal_subjects+@elective_subjects
      else  
        @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"elective_group_id IS NULL AND is_deleted = false")
        @elective_subjects = []
        elective_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"elective_group_id IS NOT NULL AND is_deleted = false")
        elective_subjects.each do |e|
          is_assigned = StudentsSubject.find_all_by_subject_id(e.id)
          unless is_assigned.empty?
            @elective_subjects.push e
          end
        end
        @all_subjects = @normal_subjects+@elective_subjects
      end
      #      @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"no_exams = false AND elective_group_id IS NULL AND is_deleted = false")
      #      @elective_subjects = []
      #      elective_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"no_exams = false AND elective_group_id IS NOT NULL AND is_deleted = false")
      #      elective_subjects.each do |e|
      #        is_assigned = StudentsSubject.find_all_by_subject_id(e.id)
      #        unless is_assigned.empty?
      #          @elective_subjects.push e
      #        end
      #      end
      #      @all_subjects = @normal_subjects+@elective_subjects
      @all_subjects.each { |subject| @exam_group.exams.build(:subject_id => subject.id) }
      if @type == 'Marks' or @type == 'MarksAndGrades'
        render(:update) do |page|
          page.replace_html 'exam-form', :partial=>'exam_marks_form'
          page.replace_html 'flash', :text=>''
        end
      else
        render(:update) do |page|
          page.replace_html 'exam-form', :partial=>'exam_grade_form'
          page.replace_html 'flash', :text=>''
        end
      end
      
    else
      render(:update) do |page|
        if @error
          page.replace_html 'flash', :text=>"<div class='errorExplanation'><p>#{t('name_already_taken')}</p></div>"
        else
          page.replace_html 'flash', :text=>"<div class='errorExplanation'><p>#{t('flash_msg9')}</p></div>"
        end
      end
    end
  end

  def publish
    @from = 'exam'
    unless ARGV[0].nil?
      @from = ARGV[0]
    end
    @exam_group = ExamGroup.active.find(params[:id])
    @exams = @exam_group.exams
    @batch = @exam_group.batch
    @sms_setting_notice = ""
    @no_exam_notice = ""
    
    this_id = @exam_group.id
    @exam_groups_ids = []
    
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
      @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
    end
    
    if @is_class_exam
      @exam_group_id = this_id
      @exam_groups_all_batches = ExamGroup.active.find(:all, :conditions => ["name = ? and batch_id IN (?)", @exam_group.name, @batches ])
      @exam_groups_ids = @exam_groups_all_batches.map{|e| e.id}
    end
    
    if params[:status] == "schedule"
      unless @exam_group.is_published
        
        available_user_ids = []
        batch_ids = {}
        student_ids = {}
        
        students = Student.find_all_by_batch_id(@batch.id)
        
        students.each do |st|
          available_user_ids << st.user_id
          batch_ids[st.user_id] = st.batch_id
          student_ids[st.user_id] = st.id
          guardians = st.student_guardian
          unless guardians.blank?
            guardians.each do |guardian|
              unless guardian.user_id.nil?
                available_user_ids << guardian.user_id
                batch_ids[guardian.user_id] = st.batch_id
                student_ids[guardian.user_id] = st.id
              end
            end
          end
          
          
        end
        
        #        guardians = students.map {|x| x.immediate_contact.user_id if x.immediate_contact.present?}.compact
        #        available_user_ids = students.collect(&:user_id).compact
        #        available_user_ids << guardians
        #        
        #        batches_guardian = students.map {|x| x.batch_id if x.immediate_contact.present?}.compact
        #        students_guardian = students.map {|x| x.id if x.immediate_contact.present?}.compact
        #        
        #        batch_ids = students.collect(&:batch_id).compact
        #        batch_ids << batches_guardian
        #        student_ids = students.collect(&:id).compact
        #        student_ids << students_guardian
        
        Delayed::Job.enqueue(
          DelayedReminderJob.new( :sender_id  => current_user.id,
            :recipient_ids => available_user_ids,
            :subject=>"#{t('exam_scheduled')}",
            :rtype=>2,
            :rid=>@exam_group.id,
            :student_id => student_ids,
            :batch_id => batch_ids,
            :body=>"#{@exam_group.name} #{t('has_been_scheduled')}")
        )
      end
      
      unless @exam_groups_ids.nil? or @exam_groups_ids.empty?
        @exam_groups_ids.each do |exam_groups_id|
          if exam_groups_id != this_id
            @tmp_exam_group = ExamGroup.active.find(exam_groups_id)
            unless @tmp_exam_group.is_published
              @tmp_batch = @tmp_exam_group.batch
              available_user_ids = []
              batch_ids = {}
              student_ids = {}
              students = Student.find_all_by_batch_id(@tmp_batch.id)
              
              students.each do |st|
                available_user_ids << st.user_id
                batch_ids[st.user_id] = st.batch_id
                student_ids[st.user_id] = st.id
                unless st.immediate_contact.nil? 
                  available_user_ids << st.immediate_contact.user_id
                  batch_ids[st.immediate_contact.user_id] = st.batch_id
                  student_ids[st.immediate_contact.user_id] = st.id
                end

              end
              
              #              guardians = students.map {|x| x.immediate_contact.user_id if x.immediate_contact.present?}.compact
              #              available_user_ids = students.collect(&:user_id).compact
              #              available_user_ids << guardians
              #              
              #              batches_guardian = students.map {|x| x.batch_id if x.immediate_contact.present?}.compact
              #              students_guardian = students.map {|x| x.id if x.immediate_contact.present?}.compact
              #
              #              batch_ids = students.collect(&:batch_id).compact
              #              batch_ids << batches_guardian
              #              student_ids = students.collect(&:id).compact
              #              student_ids << students_guardian
              
              Delayed::Job.enqueue(
                DelayedReminderJob.new( :sender_id  => current_user.id,
                  :recipient_ids => available_user_ids,
                  :subject=>"#{t('exam_scheduled')}",
                  :rtype=>2,
                  :rid=>@tmp_exam_group.id,
                  :student_id => student_ids,
                  :batch_id => batch_ids,
                  :body=>"#{@tmp_exam_group.name} #{t('has_been_scheduled')}")
              )
            end
          end
        end
      end
    end
    unless @exams.empty?
      ExamGroup.update(@exam_group.id,:is_published=>true) if params[:status] == "schedule"
      ExamGroup.update(@exam_group.id,:result_published=>true) if params[:status] == "result"
      unless @exam_groups_ids.nil? or @exam_groups_ids.empty?
        @exam_groups_ids.each do |exam_groups_id|
          if exam_groups_id != this_id
            ExamGroup.update(exam_groups_id,:is_published=>true) if params[:status] == "schedule"
          end
        end
      end
      sms_setting = SmsSetting.new()
      if sms_setting.application_sms_active and sms_setting.exam_result_schedule_sms_active
        students = @batch.students
        students.each do |s|
          guardian = s.immediate_contact
          recipients = []
          if s.is_sms_enabled
            if sms_setting.student_sms_active
              recipients.push s.phone2 unless s.phone2.nil?
            end
            if sms_setting.parent_sms_active
              unless guardian.nil?
                recipients.push guardian.mobile_phone unless guardian.mobile_phone.nil?
              end
            end
            @message = "#{@exam_group.name} #{t('exam_timetable_published')}" if params[:status] == "schedule"
            @message = "#{@exam_group.name} #{t('exam_result_published')}" if params[:status] == "result"
            unless recipients.empty? or !send_sms("exam")
              sms = Delayed::Job.enqueue(SmsManager.new(@message,recipients))
            end
          end
        end
        unless @exam_groups_ids.nil? or @exam_groups_ids.empty?
          @exam_groups_ids.each do |exam_groups_id|
            if exam_groups_id != this_id
              @tmp_exam_group = ExamGroup.active.find(exam_groups_id)
              @tmp_batch = @tmp_exam_group.batch
              students = @tmp_batch.students
              students.each do |s|
                guardian = s.immediate_contact
                recipients = []
                if s.is_sms_enabled
                  if sms_setting.student_sms_active
                    recipients.push s.phone2 unless s.phone2.nil?
                  end
                  if sms_setting.parent_sms_active
                    unless guardian.nil?
                      recipients.push guardian.mobile_phone unless guardian.mobile_phone.nil?
                    end
                  end
                  @message = "#{@tmp_exam_group.name} #{t('exam_timetable_published')}" if params[:status] == "schedule"
                  unless recipients.empty? or !send_sms("exam")
                    sms = Delayed::Job.enqueue(SmsManager.new(@message,recipients))
                  end
                end
              end
            end
          end
        end
        @sms_setting_notice = "#{t('exam_schedule_published')}" if params[:status] == "schedule"
        @sms_setting_notice = "#{t('result_has_been_published')}" if params[:status] == "result"
      else
        @sms_setting_notice = "#{t('exam_schedule_published_no_sms')}" if params[:status] == "schedule"
        @sms_setting_notice = "#{t('exam_result_published_no_sms')}" if params[:status] == "result"
      end
      if params[:status] == "result"
        students = Student.find_all_by_batch_id(@batch.id)
        
        available_user_ids = []
        batch_ids = {}
        student_ids = {}


        students.each do |st|
          available_user_ids << st.user_id
          batch_ids[st.user_id] = st.batch_id
          student_ids[st.user_id] = st.id
          unless st.immediate_contact.nil? 
            available_user_ids << st.immediate_contact.user_id
            batch_ids[st.immediate_contact.user_id] = st.batch_id
            student_ids[st.immediate_contact.user_id] = st.id
          end

        end
        #        guardians = students.map {|x| x.immediate_contact.user_id if x.immediate_contact.present?}.compact
        #        available_user_ids = students.collect(&:user_id).compact
        #        available_user_ids << guardians
        #        
        #        
        #        
        #        batches_guardian = students.map {|x| x.batch_id if x.immediate_contact.present?}.compact
        #        students_guardian = students.map {|x| x.id if x.immediate_contact.present?}.compact
        #        batch_ids = students.collect(&:batch_id).compact
        #        batch_ids << batches_guardian
        #        student_ids = students.collect(&:id).compact
        #        student_ids << students_guardian
        
        Delayed::Job.enqueue(
          DelayedReminderJob.new( :sender_id  => current_user.id,
            :recipient_ids => available_user_ids,
            :subject=>"#{t('result_published')}",
            :rtype=>3,
            :student_id => student_ids,
            :batch_id => batch_ids,
            :rid=>@exam_group.id,
            :body=>"#{@exam_group.name} #{t('result_has_been_published')}  <br/>#{t('view_reports')}")
        )
      end
    else
      @no_exam_notice = "#{t('exam_scheduling_not_done')}"
    end
  end
  
  def student_exam_schedule
    @current_user = current_user   
    if @current_user.student?
      @student = current_user.student_record        
      @batch = @student.batch
      @course = @batch.course unless @batch.nil?
      @exam_groups = @batch.exam_groups      
    elsif    @current_user.parent?
      target = @current_user.guardian_entry.current_ward_id      
      @student = Student.find_by_id(target)
      @batch = @student.batch
      @course = @batch.course unless @batch.nil?
      @exam_groups = @batch.exam_groups
    else
      abort("@current_user.inspect")
    end      
  end
  
  def student_exam_schedule_view
    @current_user = current_user
    if @current_user.student?
      @student = current_user.student_record   
      @batch = @student.batch
      @course = @batch.course unless @batch.nil?
      @exam_group = ExamGroup.active.find(params[:id], :include => :exams) 
      Reminder.update_all("is_read='1'",  ["rid = ? and rtype = ? and recipient= ?", params[:id], 2,current_user.id])
    elsif    @current_user.parent?
      target = @current_user.guardian_entry.current_ward_id      
      @student = Student.find_by_id(target)
      @batch = @student.batch
      @course = @batch.course unless @batch.nil?
      @exam_group = ExamGroup.active.find(params[:id], :include => :exams)
      Reminder.update_all("is_read='1'",  ["rid = ? and rtype = ? and recipient= ?", params[:id], 2,current_user.id])
    end    
  end
  
  def exam_schedule_pdf
    @current_user = current_user
    if @current_user.student?
      @student = current_user.student_record   
      @batch = @student.batch
      @course = @batch.course unless @batch.nil?
      @exam_group = ExamGroup.active.find(params[:id], :include => :exams) 
    elsif    @current_user.parent?
      target = @current_user.guardian_entry.current_ward_id      
      @student = Student.find_by_id(target)
      @batch = @student.batch
      @course = @batch.course unless @batch.nil?
      @exam_group = ExamGroup.active.find(params[:id], :include => :exams)
    elsif    @current_user.employee?
      @batch = Batch.find_by_id(params[:batch_id])
      @course = @batch.course unless @batch.nil?
      @exam_group = ExamGroup.active.find(params[:id], :include => :exams)
    elsif    @current_user.admin?
      @batch = Batch.find_by_id(params[:batch_id])
      @course = @batch.course unless @batch.nil?
      @exam_group = ExamGroup.active.find(params[:id], :include => :exams)
    end    
    
    render :pdf => 'exam_schedule_pdf'
  end
  
  def exam_connect_list
    @batch = Batch.find(params[:id])
    @exam_connect_data = ExamConnect.active.find_all_by_batch_id(@batch.id)
  end
  
  def connect_exam_subject
    @exam_connect = ExamConnect.active.find_by_id(params[:id])        
    @batch = Batch.find(@exam_connect.batch_id)
    @group_exams = GroupedExam.find_all_by_connect_exam_id(@exam_connect.id)    
    @subjects = []
    @group_exams.each do |group_exam|
      exams = Exam.find_all_by_exam_group_id(group_exam.exam_group_id);
      exams.each do |exam|
        if !@subjects.include?(exam.subject)
          @subjects << exam.subject
        end
      end
    end
  end
  
  def connect_exam_subject_comments
 
    @employee_subjects=[]
    
    exam_subject_id = params[:id]
    exam_subject_id_array = exam_subject_id.split("|")
    
    @exam_connect = ExamConnect.active.find_by_id(exam_subject_id_array[0])     
    @batch = Batch.find(@exam_connect.batch_id)
    @exam_subject = Subject.find_by_id(exam_subject_id_array[1])
    @employee_subjects= @current_user.employee_record.subjects.map { |n| n.id} if @current_user.employee?
    
    unless @employee_subjects.include?(@exam_subject.id) or @current_user.admin? or @current_user.privileges.map{|p| p.name}.include?('ExaminationControl') or @current_user.privileges.map{|p| p.name}.include?('EnterResults')
      flash[:notice] = "#{t('flash_msg6')}"
      redirect_to :controller=>"user", :action=>"dashboard"
    end
    
    if request.post?
      @exam_marks_error = false
      unless params[:exam_marks].blank?
        params[:exam_marks].each_pair do |exam_id, max_mark|
          @exam = Exam.find_by_id(exam_id)
          unless @exam.nil?
            max_score = ExamScore.find(:first,:select=>'max(marks) as marks',:conditions => {:exam_id => @exam.id})
            if @exam.alternative_title != max_mark[:alternative_title]
              @exam.update_attribute(:alternative_title,max_mark[:alternative_title])
            end 
            unless max_mark[:exam_date].blank?
              if @exam.exam_date != max_mark[:exam_date]
                @exam.update_attribute(:exam_date,max_mark[:exam_date])
              end
            end 
             
            if @exam.maximum_marks.to_f != max_mark[:maximum_marks].to_f and (max_score.blank? or max_score.marks.to_f < max_mark[:maximum_marks].to_f)
              @exam.update_attribute(:maximum_marks,max_mark[:maximum_marks])
            elsif !max_score.blank? and !max_mark[:maximum_marks].blank? and max_score.marks.to_f > max_mark[:maximum_marks].to_f  
              @exam_marks_error = true
            end 
          end
        end
      end 
     
      unless params[:exam_remarks].blank?
        params[:exam_remarks].each_pair do |exam_id, stdetails|
          @exam = Exam.find_by_id(exam_id)
          stdetails.each_pair do |student_id, details|
            @exam_score = ExamScore.find(:first, :conditions => {:exam_id => @exam.id, :student_id => student_id} )
            if @exam_score.nil?
              unless details[:remarks].nil? 
                ExamScore.create do |score|
                  score.exam_id          = @exam.id
                  score.student_id       = student_id
                  score.user_id          = current_user.id
                  score.remarks          = details[:remarks]
                  score.marks            = 0
                end
              end
              
            else
              @exam_score.update_attributes(details)
            end  
          end
        end
        
      end
      
      unless params[:exam_score].blank?
        params[:exam_score].each_pair do |exam_id, stdetails|
          @exam = Exam.find_by_id(exam_id)
          stdetails.each_pair do |student_id, details|
            @exam_score = ExamScore.find(:first, :conditions => {:exam_id => @exam.id, :student_id => student_id} )
            @exam_absent = ExamAbsent.find(:first, :conditions => {:exam_id => @exam.id, :student_id => student_id} )
            if details[:marks].nil?
              details[:marks] = 0
            end
            if details[:marks]!= 0 && (details[:marks].downcase == "ab" or details[:marks].downcase == "na" or details[:marks].downcase == "n/a")
              if @exam_absent.nil?
                ExamAbsent.create do |absent|
                  absent.exam_id          = @exam.id
                  absent.student_id       = student_id
                  absent.remarks          = details[:marks]
                end 
              else
                @exam_absent.update_attribute("remarks",details[:marks])
              end  
            end
            if @exam_score.nil?
              
              if !details[:marks].nil? && details[:marks].to_f >= 0
                if details[:marks].to_f <= @exam.maximum_marks.to_f
                  
                  
                  unless details[:remarks].blank?
                    if details[:remarks].kind_of?(Array)
                      remarks_details = details[:remarks].join("|")
                      ExamScore.create do |score|
                        score.exam_id          = @exam.id
                        score.student_id       = student_id
                        score.user_id          = current_user.id
                        score.marks            = details[:marks]
                        score.remarks          = remarks_details
                      end

                    end
                  else
                    ExamScore.create do |score|
                      score.exam_id          = @exam.id
                      score.student_id       = student_id
                      score.user_id          = current_user.id
                      score.marks            = details[:marks]
                    end
                  end
                
                  
                else
                  @error = true
                end
              end
            else
              if details[:marks].to_f <= @exam.maximum_marks.to_f
                unless details[:remarks].blank?
                  if details[:remarks].kind_of?(Array)
                    remarks_details = details[:remarks].join("|")
                    details[:remarks] = remarks_details
                  end
                end 
                if details[:marks]!= 0  && (details[:marks].downcase == "ab" or details[:marks].downcase == "na" or details[:marks].downcase == "n/a" or details[:marks].to_f < 0)
                  @exam_score.destroy
                else
                  unless details[:marks].nil? 
                    details[:user_id] = current_user.id
                    if @exam_score.update_attributes(details)
                    else
                      flash[:warn_notice] = "#{t('flash4')}"
                      @error = nil
                    end
                  end
                end  
              else
                @error = true
              end
            end
          end
        end
      end
      
        
      unless params[:exam].blank?
        params[:exam].each_pair do |student_id, details|
          @exam_comments = ExamConnectSubjectComment.find(:first, :conditions => {:exam_connect_id=>@exam_connect.id,:subject_id => @exam_subject.id, :student_id => student_id} )
          if @exam_comments.nil?

            ExamConnectSubjectComment.create do |score|
              score.subject_id       = @exam_subject.id
              score.student_id       = student_id
              score.exam_connect_id  = exam_subject_id_array[0]
              score.employee_id      = current_user.employee_record.id
              score.comments         = details[:comments]
              score.effort           = details[:effort] # For Sir John Wilson School
            end
          else
            if !details[:comments].blank? || !details[:effort].blank? 
              @exam_comments.update_attributes(details)
            else
              @exam_comments.destroy
            end
          end
        end
      end
      unless @exam_marks_error == true
        flash[:notice] = "#{t('successfully_saved')}"
      else
        flash[:notice] = "#{t('exam_score_is_greter_the_exam_maximum_marks')}"
      end
    end
    
    @exams = []
    
    if @exam_subject.no_exams.blank?
      @group_exam = GroupedExam.find_all_by_connect_exam_id_and_show_in_connect(@exam_connect.id,1, :order=>"priority ASC")
      unless @group_exam.blank?
        @group_exam.each do |group_exam|
          exam_group = ExamGroup.active.find(group_exam.exam_group_id)
          unless exam_group.blank?
            exam = Exam.find_by_exam_group_id_and_subject_id(exam_group.id,@exam_subject.id)
            unless exam.blank?
              @exams << exam
            end
          end  
        end
      else
        flash[:notice] = "#{t('something_went_wrong')}"
        redirect_to :controller=>"user", :action=>"dashboard"
      end 
    end  
    
    is_elective = @exam_subject.elective_group_id
    
    if is_elective == nil
      if MultiSchool.current_school.id == 319
        @students = Student.active.find_all_by_batch_id(@batch.id, :order => 'CONCAT(first_name,middle_name,last_name) ASC')
      elsif MultiSchool.current_school.id == 342
        @students = Student.active.find_all_by_batch_id(@batch.id, :order => 'class_roll_no ASC')
      else
        @students = Student.active.find_all_by_batch_id(@batch.id, :order => 'if(class_roll_no = "" or class_roll_no is null,0,cast(class_roll_no as unsigned)),first_name ASC')
      end
     
    else
      assigned_students = StudentsSubject.find_all_by_subject_id_and_batch_id(@exam_subject.id,@exam_subject.batch_id,:order=>"CONCAT(students.first_name, students.middle_name, students.last_name) ASC",:include=>[:student])
      @students = []
      @studentids = []
      assigned_students.each do |s|
        student = s.student
        unless student.nil?
          if student.batch_id.to_i == s.batch_id
            unless @studentids.include?(student.id)
              @studentids << student.id
              if MultiSchool.current_school.id == 319
                @students.push [student.first_name,student.last_name, student.id, student]
              elsif MultiSchool.current_school.id == 342
                @students.push [student.class_roll_no,student.first_name, student.id, student] 
              else
                @students.push [student.class_roll_no.to_i,student.first_name, student.id, student] 
              end
            end
          end
        end
      end
      unless @students.blank?
        if MultiSchool.current_school.id == 319
          @ordered_students = @students
        else
          @ordered_students = @students.sort
        end
        @students=[]
        @ordered_students.each do|s|
          @students.push s[3]
        end
      end
    end 
    
    @config = Configuration.get_config_value('ExamResultType') || 'Marks'
    @grades = @batch.grading_level_list
    
    
    
  end
  
  def new_exam_connect
    @batch = Batch.find(params[:id])
    @exam_groups = ExamGroup.active.find_all_by_batch_id(@batch.id)
    
    if request.post?
      #abort params.inspect
      unless params[:exam_grouping].nil?
        unless params[:exam_grouping][:exam_group_ids].nil?
          weightages = params[:weightage]
          priority = params[:priority]
          show_in_connect = params[:show_in_connect]
          total = 100
                    
          unless total=="100".to_f
            flash[:notice]="#{t('flash9')}"
            return
          else
            if params[:exam_grouping][:name].nil?
              flash[:notice]="#{t('flash25')}"
              return
            else
              @exam_connect  = ExamConnect.create(:name => params[:exam_grouping][:name],:printing_date => params[:exam_grouping][:printing_date],:result_type => params[:exam_grouping][:result_type],:quarter_number => params[:exam_grouping][:quarter_number],:next_session_begins => params[:exam_grouping][:next_session_begins],:promoted_to => params[:exam_grouping][:promoted_to], :batch_id => params[:id], :school_id => MultiSchool.current_school.id,:attandence_start_date => params[:exam_grouping][:attandence_start_date],:attandence_end_date => params[:exam_grouping][:attandence_end_date],:published_date => params[:exam_grouping][:published_date])
              @exam_connect_id = @exam_connect.id
                            
              exam_group_ids = params[:exam_grouping][:exam_group_ids]
              exam_group_ids.each_with_index do |e,i|
                weightages[i] = 3
                GroupedExam.create(:exam_group_id=>e,:batch_id=>@batch.id, :connect_exam_id => @exam_connect_id,:weightage=>weightages[i],:priority=>priority[i],:show_in_connect=>show_in_connect[i])
              end
            end            
          end
        end      
      end
      flash[:notice]="#{t('flash1')}"
      redirect_to :controller=>"exam", :action=>"exam_connect_list", :id=>@batch.id
    end
  end
  
  def edit_exam_connect
    @exam_connect = ExamConnect.active.find_by_id(params[:id])        
    @batch = Batch.find(@exam_connect.batch_id)
    @exam_groups = ExamGroup.active.find_all_by_batch_id(@exam_connect.batch_id)
    @exam_groups.reject!{|e| e.exam_type=="Grades"}
    
    if request.post? 
      #abort params.inspect
      unless params[:exam_grouping].nil?
        unless params[:exam_grouping][:exam_group_ids].nil?
          weightages = params[:weightage]
          priority = params[:priority]
          show_in_connect = params[:show_in_connect]
          #abort params.inspect
          
          total = 100         
          unless total=="100".to_f
            flash[:notice]="#{t('flash9')}"
            return
          else
            if params[:exam_grouping][:name].nil?
              flash[:notice]="#{t('flash25')}"
              return
            else              
              @exam_connect.update_attributes(:name=> params[:exam_grouping][:name],:printing_date => params[:exam_grouping][:printing_date],:result_type => params[:exam_grouping][:result_type],:quarter_number => params[:exam_grouping][:quarter_number],:next_session_begins => params[:exam_grouping][:next_session_begins],:promoted_to => params[:exam_grouping][:promoted_to],:attandence_start_date => params[:exam_grouping][:attandence_start_date],:attandence_end_date => params[:exam_grouping][:attandence_end_date],:published_date => params[:exam_grouping][:published_date])
                     
              @exam_connect_id = @exam_connect.id
              
              GroupedExam.delete_all(:connect_exam_id=>@exam_connect_id)              
              exam_group_ids = params[:exam_grouping][:exam_group_ids]
              exam_group_ids.each_with_index do |e,i|
                weightages[i] = 3
                GroupedExam.create(:exam_group_id=>e,:batch_id=>@batch.id, :connect_exam_id => @exam_connect_id,:weightage=>weightages[i],:priority=>priority[i],:show_in_connect=>show_in_connect[i])
              end
            end            
          end
        end
      else
        GroupedExam.delete_all(:connect_exam_id=>@exam_connect_id)
      end
      flash[:notice]="#{t('flash1')}"
      redirect_to :controller=>"exam", :action=>"exam_connect_list", :id=>@batch.id
    end
    
  end
  
  def remove_exam_connect    
    @exam_connect = ExamConnect.active.find_by_id(params[:id])    
    #abort @exam_connect.inspect
    @batch_id = @exam_connect.batch_id
    @exam_connect.delete
    flash[:notice] = "#{t('flash26')}"
    redirect_to :controller=>"exam", :action=>"exam_connect_list", :id=>@batch_id
  end
  
  def publish_connect_exam    
    @exam_connect = ExamConnect.active.find_by_id(params[:id])    
    #abort @exam_connect.inspect
    @batch_id = @exam_connect.batch_id
    @exam_connect.is_published = 1
    if @exam_connect.save
      available_user_ids = []
      batch_ids = {}
      student_ids = {}

      students = Student.find_all_by_batch_id(@batch_id)

      students.each do |st|
        available_user_ids << st.user_id
        batch_ids[st.user_id] = st.batch_id
        student_ids[st.user_id] = st.id
        guardians = st.student_guardian
        unless guardians.blank?
          guardians.each do |guardian|
            unless guardian.user_id.nil?
              available_user_ids << guardian.user_id
              batch_ids[guardian.user_id] = st.batch_id
              student_ids[guardian.user_id] = st.id
            end
          end
        end
      end
      unless available_user_ids.blank?
        Delayed::Job.enqueue(
          DelayedReminderJob.new( :sender_id  => current_user.id,
            :recipient_ids => available_user_ids,
            :subject=>"#{t('result_published')}",
            :rtype=>2001,
            :student_id => student_ids,
            :batch_id => batch_ids,
            :rid=>@exam_connect.id,
            :body=>"#{@exam_connect.name} #{t('result_has_been_published')}  <br/>#{t('view_reports')}")
        )
      end  
    end
    flash[:notice] = "Exam Successfully Published"
    redirect_to :controller=>"exam", :action=>"exam_connect_list", :id=>@batch_id
  end
  
  def grouping
    @batch = Batch.find(params[:id])
    @exam_groups = ExamGroup.active.find_all_by_batch_id(@batch.id)
    @exam_groups.reject!{|e| e.exam_type=="Grades"}
    if request.post?
      unless params[:exam_grouping].nil?
        unless params[:exam_grouping][:exam_group_ids].nil?
          weightages = params[:weightage]
          total = 0
          weightages.map{|w| total+=w.to_f}
          unless total=="100".to_f
            flash[:notice]="#{t('flash9')}"
            return
          else
            GroupedExam.delete_all(:batch_id=>@batch.id)
            exam_group_ids = params[:exam_grouping][:exam_group_ids]
            exam_group_ids.each_with_index do |e,i|
              GroupedExam.create(:exam_group_id=>e,:batch_id=>@batch.id,:weightage=>weightages[i])
            end
          end
        end
      else
        GroupedExam.delete_all(:batch_id=>@batch.id)
      end
      flash[:notice]="#{t('flash1')}"
    end
  end

  #REPORTS

  def list_batch_groups
    unless params[:course_id]==""
      @batch_groups = BatchGroup.find_all_by_course_id(params[:course_id])
      if @batch_groups.empty?
        render(:update) do|page|
          page.replace_html "batch_group_list", :text=>""
        end
      else
        render(:update) do|page|
          page.replace_html "batch_group_list", :partial=>"select_batch_group"
        end
      end
    else
      render(:update) do|page|
        page.replace_html "batch_group_list", :text=>""
      end
    end
  end

  def generate_previous_reports
    if request.post?
      unless params[:report][:batch_ids].blank?
        @batches = Batch.find_all_by_id(params[:report][:batch_ids])
        @batches.each do|batch|
          batch.job_type = "2"
          Delayed::Job.enqueue(batch)
        end
        flash[:notice]="#{t('report_generation_in_queue_for_batches')}" + " #{@batches.collect(&:full_name).join(", ")}. <a href='/scheduled_jobs/Batch/2'>" + "#{t('cick_here_to_view_the_scheduled_job')}"
      else
        flash[:notice]="#{t('flash11')}"
        return
      end
    end
  end

  def select_inactive_batches
    unless params[:course_id]==""
      @batches = Batch.find(:all, :conditions=>{:course_id=>params[:course_id],:is_active=>false,:is_deleted=>:false})
      if @batches.empty?
        render(:update) do|page|
          page.replace_html "select_inactive_batches", :text=>"<p class='flash-msg'>#{t('exam.flash12')}</p>"
        end
      else
        render(:update) do|page|
          page.replace_html "select_inactive_batches", :partial=>"inactive_batch_list"
        end
      end
    else
      render(:update) do|page|
        page.replace_html "select_inactive_batches", :text=>""
      end
    end
  end

  def generate_reports
    if request.post?
      unless !params[:report][:course_id].present? or params[:report][:course_id]==""
        @course = Course.find(params[:report][:course_id])
        if @course.has_batch_groups_with_active_batches
          unless !params[:report][:batch_group_id].present? or params[:report][:batch_group_id]==""
            @batch_group = BatchGroup.find(params[:report][:batch_group_id])
            @batches = @batch_group.batches
          end
        else
          @batches = @course.active_batches
        end
      end
      if @batches
        @batches.each do|batch|
          batch.job_type = "1"
          Delayed::Job.enqueue(batch)
        end
        flash[:notice]="#{t('report_generation_in_queue_for_batches')}" + " #{@batches.collect(&:full_name).join(", ")}. <a href='/scheduled_jobs/Batch/2'>" + "#{t('cick_here_to_view_the_scheduled_job')}"
      else
        flash[:notice]="#{t('flash11')}"
        return
      end
    end
  end
  
  def attendance_rank
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
    @exam_groups = []
  end

  def exam_wise_report
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
    @exam_groups = []
  end

  def list_exam_types
    if params[:batch_id].nil?
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
      
      @batch_id = 0
      unless @batch_data.nil?
        @batch_id = @batch_data.id 
      end
    else
      batch = Batch.find(params[:batch_id])
      @batch_id = batch.id
    end  
    
    @exam_groups = ExamGroup.active.find_all_by_batch_id(@batch_id)
    #@exam_groups.map! {|exam| [:name=>exam.is_current? ? "#{exam.name} (Current)" : exam.name,:id=>exam.id] } # now names contains ['Danil', 'Edmund']
    #abort @exam_groups.inspect
    render(:update) do |page|
      page.replace_html 'exam-group-select', :partial=>'exam_group_select'
    end
  end
  
  def student_wise_tabulation  
    @exam_group = ExamGroup.find(params[:exam_group])
    if @exam_group.is_deleted.to_i == 1
      student_list = []
      allExam = @exam_group.exams
      allExam.each do |exams|
        score_data = exams.exam_scores
        score_data.each do |sd|
          student_list.push(sd.student_id) unless student_list.include?(sd.student_id)
        end          
      end
      if student_list.nil?
        flash[:notice] = "#{t('flash_student_notice')}"
        redirect_to :action => 'exam_wise_report' and return
      end

      @batch = @exam_group.batch
      @students = Student.find_all_by_id(student_list, :order=>"class_roll_no ASC",:conditions=>["is_deleted = ? and is_active = ?",false,true])
    else
      @batch = @exam_group.batch
      @students=Student.find_all_by_batch_id(@batch.id, :order=>"class_roll_no ASC",:conditions=>["is_deleted = ? and is_active = ?",false,true])
    end
    
    @assigned_employee=@batch.all_class_teacher
    general_subjects = Subject.find_all_by_batch_id(@batch.id, :conditions=>"elective_group_id IS NULL")
    student_electives = StudentsSubject.find_all_by_batch_id(@batch.id)
    elective_subjects = []
    elective_subjects_id = []
    student_electives.each do |elect|
      if !elective_subjects_id.include?(elect.subject_id)
        elective_subjects_id << elect.subject_id
        subject = Subject.find_by_id(elect.subject_id)
        unless subject.blank?
          elective_subjects.push subject
        end
      end     
    end
    @subjects = general_subjects + elective_subjects
    @subjects.sort! { |a, b|  a.priority.to_i <=> b.priority.to_i }
    @exams = []
    @subjects.each do |sub|
      exam = Exam.find_by_exam_group_id_and_subject_id(@exam_group.id,sub.id)
      @exams.push exam unless exam.nil?
    end
    
    @ranked_student = ExamScore.all(:select =>["SUM(exam_scores.marks)/SUM(exams.maximum_marks) as total_score,exam_scores.student_id"],:conditions=>["exams.exam_group_id = ?",@exam_group.id],:joins=>[:exam,:student],:group =>"exam_scores.student_id",:order=>"total_score DESC")
    @tmp_students = []
    unless @ranked_student.blank?
      @ranked_student.each do |ras|
        std_data = Student.find_by_id(ras.student_id,:conditions=>["is_deleted = ? and is_active = ?",false,true])
        if !std_data.blank? && !@tmp_students.include?(std_data)
          @tmp_students << std_data
        end
      end
    end
    
    unless @students.blank?
      @students.each do |std|
        unless @tmp_students.include?(std)
          @tmp_students << std
        end
      end
    end
    
    @students = @tmp_students
    if MultiSchool.current_school.id != 325 and MultiSchool.current_school.id != 7
      @students.sort! { |a, b|  a.class_roll_no.to_i <=> b.class_roll_no.to_i }
    end  
    
    render :pdf => 'student_wise_tabulation',
      :orientation => 'Landscape', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def student_wise_generated_report_all  
    @exam_group = ExamGroup.find(params[:exam_group])
    
    
    if @exam_group.is_deleted.to_i == 1
      student_list = []
      allExam = @exam_group.exams
      allExam.each do |exams|
        score_data = exams.exam_scores
        score_data.each do |sd|
          student_list.push(sd.student_id) unless student_list.include?(sd.student_id)
        end          
      end
      if student_list.nil?
        flash[:notice] = "#{t('flash_student_notice')}"
        redirect_to :action => 'exam_wise_report' and return
      end

      @batch = @exam_group.batch
      @students = Student.find_all_by_id(student_list,:conditions=>["is_deleted = ? and is_active = ?",false,true])
    else
      @batch = @exam_group.batch
      @students=@batch.students.by_first_name
    end
    
    @students.sort! { |a, b|  a.class_roll_no.to_i <=> b.class_roll_no.to_i }
    
    @assigned_employee=@batch.all_class_teacher
    general_subjects = Subject.find_all_by_batch_id(@batch.id, :conditions=>"elective_group_id IS NULL")
    student_electives = StudentsSubject.find_all_by_batch_id(@batch.id)
    elective_subjects = []
    elective_subjects_id = []
    student_electives.each do |elect|
      if !elective_subjects_id.include?(elect.subject_id)
        elective_subjects_id << elect.subject_id
        subject = Subject.find_by_id(elect.subject_id)
        unless subject.blank?
          elective_subjects.push subject
        end
      end     
    end
    
    @subjects = general_subjects + elective_subjects
    @subjects.sort! { |a, b|  a.priority.to_i <=> b.priority.to_i }
    @exams = []
    @subjects.each do |sub|
      exam = Exam.find_by_exam_group_id_and_subject_id(@exam_group.id,sub.id)
      @exams.push exam unless exam.nil?
    end
    
    if !@exam_group.attandence_start_date.blank? and !@exam_group.attandence_end_date.blank?
      
      @academic_days = @batch.find_working_days(@exam_group.attandence_start_date.to_date,@exam_group.attandence_end_date.to_date).select{|v| v<=@exam_group.attandence_end_date.to_date}.count
      @student_leaves = Attendance.find(:all,:conditions =>{:batch_id=>@batch.id,:month_date => @exam_group.attandence_start_date..@exam_group.attandence_end_date})
      
    else
      @academic_days = @batch.find_working_days(@batch.start_date.to_date,@exam_group.exam_date.to_date).select{|v| v<=@exam_group.exam_date.to_date}.count
      @student_leaves = Attendance.find(:all,:conditions =>{:batch_id=>@batch.id,:month_date => @batch.start_date..@exam_group.exam_date})
      
    end
    
    
    @exam_comments = ExamGroupComment.find_all_by_exam_group_id(@exam_group.id)
    #    on_leaves = 0;
    #    leaves_other = 0;
    #    leaves_full = 0;
    #    unless @student_leaves.empty?
    #      @student_leaves.each do |r|
    #        if r.student_id == @student.id
    #          working_days_count=@batch.find_working_days(r.month_date.to_date,r.month_date.to_date).select{|v| v<=r.month_date.to_date}.count
    #
    #          if working_days_count==1
    #            if r.is_leave == true
    #              on_leaves = on_leaves+1;
    #            elsif r.forenoon==true && r.afternoon==false
    #              leaves_other = leaves_other+1;
    #            elsif r.forenoon==false && r.afternoon==true  
    #              leaves_other = leaves_other+1;
    #            else
    #              leaves_full = leaves_full+1;
    #            end 
    #          end
    #        end
    #      end
    #    end
    #    #    @late = leaves_other
    #    #    @absent = leaves_full
    #    #    @on_leave = on_leaves
    #    @present = @academic_days-on_leaves-leaves_full
    #    @absent = @academic_days-@present
    #    @exam_comment = ExamGroupComment.find_by_exam_group_id_and_student_id(@exam_group.id,@student.id)
   
    render :pdf => 'student_wise_generated_report_all'
  end

  def student_wise_generated_report
    
    @exam_group = ExamGroup.find(params[:exam_group])
    @student = Student.find_by_id(params[:student])
    if @student.blank?
      @student = ArchivedStudent.find_by_former_id(params[:student])
      unless @student.blank?
        @student.id = @student.former_id
      end
    end
    @for_save = params[:for_save]
    
    @batch = @exam_group.batch
    @assigned_employee=@batch.all_class_teacher
    general_subjects = Subject.find_all_by_batch_id(@batch.id, :conditions=>"elective_group_id IS NULL")
    student_electives = StudentsSubject.find_all_by_student_id(@student.id,:conditions=>"batch_id = #{@batch.id}")
    elective_subjects = []
    student_electives.each do |elect|
      subject = Subject.find_by_id(elect.subject_id)
      unless subject.blank?
        elective_subjects.push subject
      end
    end
   
    
    @subjects = general_subjects + elective_subjects
    @subjects.sort! { |a, b|  a.priority.to_i <=> b.priority.to_i }
    @exams = []
    @subjects.each do |sub|
      exam = Exam.find_by_exam_group_id_and_subject_id(@exam_group.id,sub.id)
      @exams.push exam unless exam.nil?
    end
    
    if !@exam_group.attandence_start_date.blank? and !@exam_group.attandence_end_date.blank?
      
      @academic_days = @batch.find_working_days(@exam_group.attandence_start_date.to_date,@exam_group.attandence_end_date.to_date).select{|v| v<=@exam_group.attandence_end_date.to_date}.count
      @student_leaves = Attendance.find(:all,:conditions =>{:batch_id=>@batch.id,:student_id=>@student.id,:month_date => @exam_group.attandence_start_date..@exam_group.attandence_end_date})
      
    else
      @academic_days = @batch.find_working_days(@batch.start_date.to_date,@exam_group.exam_date.to_date).select{|v| v<=@exam_group.exam_date.to_date}.count
      @student_leaves = Attendance.find(:all,:conditions =>{:batch_id=>@batch.id,:student_id=>@student.id,:month_date => @batch.start_date..@exam_group.exam_date})
      
    end
    
    on_leaves = 0;
    leaves_other = 0;
    leaves_full = 0;
    unless @student_leaves.empty?
      @student_leaves.each do |r|
        if r.student_id == @student.id
          working_days_count=@batch.find_working_days(r.month_date.to_date,r.month_date.to_date).select{|v| v<=r.month_date.to_date}.count

          if working_days_count==1
            if r.is_leave == true
              on_leaves = on_leaves+1;
            elsif r.forenoon==true && r.afternoon==false
              leaves_other = leaves_other+1;
            elsif r.forenoon==false && r.afternoon==true  
              leaves_other = leaves_other+1;
            else
              leaves_full = leaves_full+1;
            end 
          end
        end
      end
    end
    #    @late = leaves_other
    #    @absent = leaves_full
    #    @on_leave = on_leaves
    @present = @academic_days-on_leaves-leaves_full
    @absent = @academic_days-@present
    @exam_comment = ExamGroupComment.find_by_exam_group_id_and_student_id(@exam_group.id,@student.id)
    
    if @for_save.blank?
      render :pdf => 'student_wise_generated_report',
        :footer => {:html => { :template=> 'layouts/footer_single.html'}}
    else
      pdf_name = "group_exam_"+params[:exam_group].to_s+"_"+params[:student].to_s+".pdf"
      dirname = Rails.root.join('public','result_pdf_archive',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"examgroup","0"+@exam_group.id.to_s)
      unless File.directory?(dirname)
        FileUtils.mkdir_p(dirname)
        FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf_archive',"0"+MultiSchool.current_school.id.to_s))
      end
      render :pdf  => 'student_wise_generated_report',
        :footer => {:html => { :template=> 'layouts/footer_single.html'}},
        :save_to_file => Rails.root.join('public','result_pdf_archive',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"examgroup","0"+@exam_group.id.to_s,pdf_name),
        :save_only    => true
    end
  end

  def generated_report
    if params[:student].nil? or !params[:student][:class_name].nil?
      if params[:exam_report].nil? or params[:exam_report][:exam_group_id].empty?
        flash[:notice] = "#{t('flash2')}"
        redirect_to :action=>'exam_wise_report' and return
      end
    else
      if params[:exam_group].nil?
        flash[:notice] = "#{t('flash3')}"
        redirect_to :action=>'exam_wise_report' and return
      end
    end
    if params[:student].nil? or !params[:student][:class_name].nil?
      @exam_group = ExamGroup.active.find(params[:exam_report][:exam_group_id])
      
      if @exam_group.is_current == false
        student_list = []
        allExam = @exam_group.exams
        allExam.each do |exams|
          score_data = exams.exam_scores
          score_data.each do |sd|
            student_list.push(sd.student_id) unless student_list.include?(sd.student_id)
          end          
        end
        if student_list.nil?
          flash[:notice] = "#{t('flash_student_notice')}"
          redirect_to :action => 'exam_wise_report' and return
        end
        
        @batch = @exam_group.batch
        @students = Student.find_all_by_id(student_list,:conditions=>["is_deleted = ? and is_active = ?",false,true])
        @student = @students.first  unless @students.empty?
      else
        @batch = @exam_group.batch
        @students=@batch.students.by_first_name
        @student = @students.first  unless @students.empty?
      end
      
      if @student.nil?
        flash[:notice] = "#{t('flash_student_notice')}"
        redirect_to :action => 'exam_wise_report' and return
      end
      general_subjects = Subject.find_all_by_batch_id(@batch.id, :conditions=>"elective_group_id IS NULL and is_deleted=0")
      student_electives = StudentsSubject.find_all_by_student_id(@student.id,:conditions=>"batch_id = #{@batch.id}")
      elective_subjects = []
      student_electives.each do |elect|
        subject = Subject.find_by_id(elect.subject_id, :conditions=>"is_deleted=0")
        unless subject.blank?
          elective_subjects.push subject
        end
      end
      @subjects = general_subjects + elective_subjects
      @exams = [] 
      @subjects.each do |sub|
        exam = Exam.find_by_exam_group_id_and_subject_id(@exam_group.id,sub.id)
        @exams.push exam unless exam.nil?
      end
      @exam_comment = ExamGroupComment.find_by_exam_group_id_and_student_id(@exam_group.id,@student.id)
      Reminder.update_all("is_read='1'",  ["rid = ? and rtype = ? and recipient= ?", params[:exam_group], 3,current_user.id])
      @graph = open_flash_chart_object(700, 350,
        "/exam/graph_for_generated_report?batch=#{@student.batch.id}&examgroup=#{@exam_group.id}&student=#{@student.id}")
    else
      @exam_group = ExamGroup.active.find(params[:exam_group])
      @student = Student.find_by_id(params[:student])
      if params[:batch_id].nil? 
        @batch = @student.batch
      else
        @batch = Batch.find_by_id(params[:batch_id])        
      end
      
      general_subjects = Subject.find_all_by_batch_id(@batch.id, :conditions=>"elective_group_id IS NULL and is_deleted=0")
      student_electives = StudentsSubject.find_all_by_student_id(@student.id,:conditions=>"batch_id = #{@batch.id}")
      elective_subjects = []
      student_electives.each do |elect|
        subject = Subject.find_by_id(elect.subject_id, :conditions=>"is_deleted=0")
        unless subject.blank?
          elective_subjects.push subject
        end
      end
      @subjects = general_subjects + elective_subjects
      @exams = []
      @subjects.each do |sub|
        exam = Exam.find_by_exam_group_id_and_subject_id(@exam_group.id,sub.id)
        @exams.push exam unless exam.nil?
      end
      Reminder.update_all("is_read='1'",  ["rid = ? and rtype = ? and recipient= ?", params[:exam_group], 3,current_user.id])
      @graph = open_flash_chart_object(700, 350,
        "/exam/graph_for_generated_report?batch=#{@batch.id}&examgroup=#{@exam_group.id}&student=#{@student.id}")
      
      now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
      if !params[:comments].blank?   
        @exam_comment = ExamGroupComment.find_by_exam_group_id_and_student_id(@exam_group.id,@student.id)
        if @exam_comment.blank?
          exam_comment_new = ExamGroupComment.new
          exam_comment_new.exam_group_id = @exam_group.id
          exam_comment_new.comments = params[:comments]
          exam_comment_new.student_id = @student.id
          exam_comment_new.employee_id = current_user.employee_record.id
          exam_comment_new.created_at = now
          exam_comment_new.updated_at = now
          exam_comment_new.school_id = MultiSchool.current_school.id
          exam_comment_new.save 
        else
          @exam_comment.update_attribute(:comments,params[:comments])
        end     
      end
      
      @exam_comment = ExamGroupComment.find_by_exam_group_id_and_student_id(@exam_group.id,@student.id)
      
      if request.xhr?
        render(:update) do |page|
          page.replace_html   'exam_wise_report', :partial=>"exam_wise_report"
        end
      else
        @students = Student.find_all_by_id(params[:student])
      end
    end
  end

  def generated_report_pdf
    @config = Configuration.get_config_value('InstitutionName')
    @exam_group = ExamGroup.active.find(params[:exam_group])
    @batch = Batch.find(params[:batch])
    @students = @batch.students.by_first_name
    render :pdf => 'generated_report_pdf'
  end


  def consolidated_exam_report
    @exam_group = ExamGroup.active.find(params[:exam_group])
    @batch = @exam_group.batch
  end

  def consolidated_exam_report_pdf
    @exam_group = ExamGroup.active.find(params[:exam_group])
    @batch = @exam_group.batch
    render :pdf => 'consolidated_exam_report_pdf'
    #        respond_to do |format|
    #            format.pdf { render :layout => false }
    #        end
  end

  def subject_rank
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
    @subjects = []
  end

  def list_batch_subjects
    if params[:batch_id].nil?
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
      
      @batch_id = 0
      unless @batch_data.nil?
        @batch_id = @batch_data.id 
      end
    else
      batch = Batch.find(params[:batch_id])
      @batch_id = batch.id
    end 
    @subjects = Subject.find_all_by_batch_id(@batch_id,:conditions=>"is_deleted=false AND no_exams=false")
    render(:update) do |page|
      page.replace_html 'subject-select', :partial=>'rank_subject_select'
    end
  end

  def student_subject_rank
    unless params[:rank_report].nil? or params[:rank_report][:subject_id] == ""
      @subject = Subject.find(params[:rank_report][:subject_id])
      @batch = @subject.batch
      @students = @batch.students.by_first_name
      unless @subject.elective_group_id.nil?
        @students.reject!{|s| !StudentsSubject.exists?(:student_id=>s.id,:subject_id=>@subject.id)}
      end
      @exam_groups = ExamGroup.active.find(:all,:conditions=>{:batch_id=>@batch.id})
      @exam_groups.reject!{|e| e.exam_type=="Grades"}
    else
      flash[:notice] = "#{t('flash4')}"
      redirect_to :action=>'subject_rank'
    end
  end

  def student_subject_rank_pdf
    @subject = Subject.find(params[:subject_id])
    @batch = @subject.batch
    @students = @batch.students.by_first_name
    unless @subject.elective_group_id.nil?
      @students.reject!{|s| !StudentsSubject.exists?(:student_id=>s.id,:subject_id=>@subject.id)}
    end
    @exam_groups = ExamGroup.active.find(:all,:conditions=>{:batch_id=>@batch.id})
    @exam_groups.reject!{|e| e.exam_type=="Grades"}
    render :pdf => 'student_subject_rank_pdf',
      :zoom => 0.68,:orientation => :landscape
  end

  def subject_wise_report
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
    @subjects = []
  end

  def list_subjects
    if params[:batch_id].nil?
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
      
      @batch_id = 0
      unless @batch_data.nil?
        @batch_id = @batch_data.id 
      end
    else
      batch = Batch.find(params[:batch_id])
      @batch_id = batch.id
    end 
    @subjects = Subject.find_all_by_batch_id(@batch_id,:conditions=>"is_deleted=false AND no_exams=false")
    render(:update) do |page|
      page.replace_html 'subject-select', :partial=>'subject_select'
    end
  end

  def generated_report2
    #subject-wise-report-for-batch
    unless params[:exam_report][:subject_id] == ""
      @subject = Subject.find(params[:exam_report][:subject_id])
      @batch = @subject.batch
      @students = @batch.students
      @exam_groups = ExamGroup.active.find(:all,:conditions=>{:batch_id=>@batch.id})
    else
      flash[:notice] = "#{t('flash4')}"
      redirect_to :action=>'subject_wise_report'
    end
  end
  def generated_report2_pdf
    #subject-wise-report-for-batch
    @subject = Subject.find(params[:subject_id])
    @batch = @subject.batch
    @students = @batch.students
    @exam_groups = ExamGroup.active.find(:all,:conditions=>{:batch_id=>@batch.id})
    render :pdf => 'generated_report_pdf'
    
    #        respond_to do |format|
    #            format.pdf { render :layout => false }
    #        end
  end

  def student_batch_rank
    if params[:batch_rank].nil? or params[:batch_rank][:batch_id].empty?
      flash[:notice] = "#{t('select_a_batch_to_continue')}"
      redirect_to :action=>'batch_rank' and return
    else
      @batch = Batch.find(params[:batch_rank][:batch_id])
      @students = Student.find_all_by_batch_id(@batch.id)
      @grouped_exams = GroupedExam.find_all_by_batch_id(@batch.id)
      @ranked_students = @batch.find_batch_rank
    end
  end

  def student_batch_rank_pdf
    @batch = Batch.find(params[:batch_id])
    @students = Student.find_all_by_batch_id(@batch.id)
    @grouped_exams = GroupedExam.find_all_by_batch_id(@batch.id)
    @ranked_students = @batch.find_batch_rank
    render :pdf => "student_batch_rank_pdf"
  end
  
  def course_rank
  end

  def batch_groups
    unless params[:course_id]==""
      @course = Course.find(params[:course_id])
      if @course.has_batch_groups_with_active_batches
        @batch_groups = BatchGroup.find_all_by_course_id(params[:course_id])
        render(:update) do|page|
          page.replace_html "batch_group_list", :partial=>"batch_groups"
        end
      else
        render(:update) do|page|
          page.replace_html "batch_group_list", :text=>""
        end
      end
    else
      render(:update) do|page|
        page.replace_html "batch_group_list", :text=>""
      end
    end
  end

  def student_course_rank
    if params[:course_rank].nil? or params[:course_rank][:course_id]==""
      flash[:notice] = "#{t('flash13')}"
      redirect_to :action=>'course_rank' and return
    else
      @course = Course.find(params[:course_rank][:course_id])
      if @course.has_batch_groups_with_active_batches and (!params[:course_rank][:batch_group_id].present? or params[:course_rank][:batch_group_id]=="")
        flash[:notice] = "#{t('flash14')}"
        redirect_to :action=>'course_rank' and return
      else
        if @course.has_batch_groups_with_active_batches
          @batch_group = BatchGroup.find(params[:course_rank][:batch_group_id])
          @batches = @batch_group.batches
        else
          @batches = @course.active_batches
        end
        @students = Student.find_all_by_batch_id(@batches)
        @grouped_exams = GroupedExam.find_all_by_batch_id(@batches)
        @sort_order=""
        unless !params[:sort_order].present?
          @sort_order=params[:sort_order]
        end
        @ranked_students = @course.find_course_rank(@batches.collect(&:id),@sort_order).paginate(:page => params[:page], :per_page=>25)
      end
    end
  end

  def student_course_rank_pdf
    @course = Course.find(params[:course_id])
    if @course.has_batch_groups_with_active_batches
      @batch_group = BatchGroup.find(params[:batch_group_id])
      @batches = @batch_group.batches
    else
      @batches = @course.active_batches
    end
    @students = Student.find_all_by_batch_id(@batches)
    @grouped_exams = GroupedExam.find_all_by_batch_id(@batches)
    @sort_order=""
    unless !params[:sort_order].present?
      @sort_order=params[:sort_order]
    end
    @ranked_students = @course.find_course_rank(@batches.collect(&:id),@sort_order)
    render :pdf => "student_course_rank_pdf"
  end

  def student_school_rank
    @courses = Course.all(:conditions=>{:is_deleted=>false})
    @batches = Batch.all(:conditions=>{:course_id=>@courses,:is_deleted=>false,:is_active=>true})
    @students = Student.find_all_by_batch_id(@batches)
    @grouped_exams = GroupedExam.find_all_by_batch_id(@batches)
    @sort_order=""
    unless !params[:sort_order].present?
      @sort_order=params[:sort_order]
    end
    unless @courses.empty?
      @ranked_students = @courses.first.find_course_rank(@batches.collect(&:id),@sort_order).paginate(:page => params[:page], :per_page=>25)
    else
      @ranked_students=[]
    end
  end

  def student_school_rank_pdf
    @courses = Course.all(:conditions=>{:is_deleted=>false})
    @batches = Batch.all(:conditions=>{:course_id=>@courses,:is_deleted=>false,:is_active=>true})
    @students = Student.find_all_by_batch_id(@batches)
    @grouped_exams = GroupedExam.find_all_by_batch_id(@batches)
    @sort_order=""
    unless !params[:sort_order].present?
      @sort_order=params[:sort_order]
    end
    unless @courses.empty?
      @ranked_students = @courses.first.find_course_rank(@batches.collect(&:id),@sort_order)
    else
      @ranked_students=[]
    end
    render :pdf => "student_school_rank_pdf"
  end

  def student_attendance_rank
    if params[:attendance_rank].nil? or params[:attendance_rank][:batch_id].nil? or params[:attendance_rank][:batch_id].empty?
      flash[:notice] = "#{t('select_a_batch_to_continue')}"
      redirect_to :action=>'attendance_rank' and return
    else
      if params[:attendance_rank][:start_date].to_date > params[:attendance_rank][:end_date].to_date
        flash[:notice] = "#{t('flash15')}"
        redirect_to :action=>'attendance_rank' and return
      else
        @batch = Batch.find(params[:attendance_rank][:batch_id])
        @students = Student.find_all_by_batch_id(@batch.id)
        @start_date = params[:attendance_rank][:start_date].to_date
        @end_date = params[:attendance_rank][:end_date].to_date
        @ranked_students = @batch.find_attendance_rank(@start_date,@end_date)
      end
    end
  end

  def student_attendance_rank_pdf
    @batch = Batch.find(params[:batch_id])
    @students = Student.find_all_by_batch_id(@batch.id)
    @start_date = params[:start_date].to_date
    @end_date = params[:end_date].to_date
    @ranked_students = @batch.find_attendance_rank(@start_date,@end_date)
    render :pdf => "student_attendance_rank_pdf"
  end

  def ranking_level_report
  end

  def select_mode
    unless params[:mode].nil? or params[:mode]==""
      if params[:mode] == "batch"
        @batches = Batch.active
        render(:update) do|page|
          page.replace_html "course-batch", :partial=>"batch_select"
        end
      else
        @courses = Course.active
        render(:update) do|page|
          page.replace_html "course-batch", :partial=>"course_select"
        end
      end
    else
      render(:update) do|page|
        page.replace_html "course-batch", :text=>""
      end
    end
  end

  def select_batch_group
    unless params[:course_id].nil? or params[:course_id]==""
      @course = Course.find(params[:course_id])
      if @course.has_batch_groups_with_active_batches
        @batch_groups = BatchGroup.find_all_by_course_id(params[:course_id])
      end
      @ranking_levels = RankingLevel.find_all_by_course_id(params[:course_id])
      render(:update) do|page|
        page.replace_html "batch_groups", :partial=>"report_batch_groups"
      end
    else
      render(:update) do|page|
        page.replace_html "batch_groups", :text=>""
      end
    end
  end

  def select_type
    unless params[:report_type].nil? or params[:report_type]=="" or params[:report_type]=="overall"
      unless params[:batch_id].nil? or params[:batch_id]==""
        @batch = Batch.find(params[:batch_id])
        @subjects = Subject.find(:all,:conditions=>{:batch_id=>@batch.id,:is_deleted=>false})
        render(:update) do|page|
          page.replace_html "subject-select", :partial=>"subject_list"
        end
      else
        render(:update) do|page|
          page.replace_html "subject-select", :text=>""
        end
      end
    else
      render(:update) do|page|
        page.replace_html "subject-select", :text=>""
      end
    end
  end

  def student_ranking_level_report
    if params[:ranking_level_report].nil? or params[:ranking_level_report][:mode]==""
      flash[:warn_notice]="#{t('flash16')}"
      redirect_to :action=>"ranking_level_report" and return
    else
      @mode = params[:ranking_level_report][:mode]
      if params[:ranking_level_report][:mode]=="batch"
        if params[:ranking_level_report][:batch_id]==""
          flash[:warn_notice]="#{t('select_a_batch_to_continue')}"
          redirect_to :action=>"ranking_level_report" and return
        else
          @batch = Batch.find(params[:ranking_level_report][:batch_id])
          if params[:ranking_level_report].nil? or params[:ranking_level_report][:ranking_level_id]==""
            flash[:warn_notice]="#{t('flash17')}"
            redirect_to :action=>"ranking_level_report" and return
          elsif params[:ranking_level_report][:report_type]==""
            flash[:warn_notice]="#{t('flash18')}"
            redirect_to :action=>"ranking_level_report" and return
          else
            @ranking_level = RankingLevel.find(params[:ranking_level_report][:ranking_level_id])
            if @ranking_level.marks.nil? && !@batch.gpa_enabled?
              flash[:warn_notice] = "#{t('flash23')}"
              redirect_to :action=>"ranking_level_report" and return
            elsif @ranking_level.gpa.nil? && @batch.gpa_enabled?
              flash[:warn_notice] = "#{t('flash24')}"
              redirect_to :action=>"ranking_level_report" and return
            else
           
              @report_type = params[:ranking_level_report][:report_type]
              if params[:ranking_level_report][:report_type]=="subject"
                if params[:ranking_level_report][:subject_id]==""
                  flash[:warn_notice]="#{t('flash4')}."
                  redirect_to :action=>"ranking_level_report" and return
                else
                  @students = @batch.students(:conditions=>{:is_active=>true,:is_deleted=>true})
                  @subject = Subject.find(params[:ranking_level_report][:subject_id])
                  @scores = GroupedExamReport.find(:all,:conditions=>{:student_id=>@students.collect(&:id),:batch_id=>@batch.id,:subject_id=>@subject.id,:score_type=>"s"})
                  unless @scores.empty?
                    if @batch.gpa_enabled?
                      @scores.reject!{|s| !((s.marks < @ranking_level.gpa if @ranking_level.marks_limit_type=="upper") or (s.marks >= @ranking_level.gpa if @ranking_level.marks_limit_type=="lower") or (s.marks == @ranking_level.gpa if @ranking_level.marks_limit_type=="exact"))}
                    else
                      @scores.reject!{|s| !((s.marks < @ranking_level.marks if @ranking_level.marks_limit_type=="upper") or (s.marks >= @ranking_level.marks if @ranking_level.marks_limit_type=="lower") or (s.marks == @ranking_level.marks if @ranking_level.marks_limit_type=="exact"))}
                    end
                  else
                    flash[:warn_notice]="#{t('flash19')}"
                    redirect_to :action=>"ranking_level_report" and return
                  end
                end
              else
                @students = @batch.students(:conditions=>{:is_active=>true,:is_deleted=>true})
                unless @ranking_level.subject_count.nil?
                  unless @ranking_level.full_course==true
                    @subjects = @batch.subjects
                    @scores = GroupedExamReport.find(:all,:conditions=>{:student_id=>@students.collect(&:id),:batch_id=>@batch.id,:subject_id=>@subjects.collect(&:id),:score_type=>"s"})
                  else
                    @scores = GroupedExamReport.find(:all,:conditions=>{:student_id=>@students.collect(&:id),:score_type=>"s"})
                  end
                  unless @scores.empty?
                    if @batch.gpa_enabled?
                      @scores.reject!{|s| !((s.marks < @ranking_level.gpa if @ranking_level.marks_limit_type=="upper") or (s.marks >= @ranking_level.gpa if @ranking_level.marks_limit_type=="lower") or (s.marks == @ranking_level.gpa if @ranking_level.marks_limit_type=="exact"))}
                    else
                      @scores.reject!{|s| !((s.marks < @ranking_level.marks if @ranking_level.marks_limit_type=="upper") or (s.marks >= @ranking_level.marks if @ranking_level.marks_limit_type=="lower") or (s.marks == @ranking_level.marks if @ranking_level.marks_limit_type=="exact"))}
                    end
                  else
                    flash[:warn_notice]="#{t('flash19')}"
                    redirect_to :action=>"ranking_level_report" and return
                  end
                else
                  unless @ranking_level.full_course==true
                    @scores = GroupedExamReport.find(:all,:conditions=>{:student_id=>@students.collect(&:id),:batch_id=>@batch.id,:score_type=>"c"})
                  else
                    @scores = []
                    @students.each do|student|
                      total_student_score = 0
                      avg_student_score = 0
                      marks = GroupedExamReport.find_all_by_student_id_and_score_type(student.id,"c")
                      unless marks.empty?
                        marks.map{|m| total_student_score+=m.marks}
                        avg_student_score = total_student_score.to_f/marks.count.to_f
                        marks.first.marks = avg_student_score
                        @scores.push marks.first
                      end
                    end
                  end
                  unless @scores.empty?
                    if @batch.gpa_enabled?
                      @scores.reject!{|s| !((s.marks < @ranking_level.gpa if @ranking_level.marks_limit_type=="upper") or (s.marks >= @ranking_level.gpa if @ranking_level.marks_limit_type=="lower") or (s.marks == @ranking_level.gpa if @ranking_level.marks_limit_type=="exact"))}
                    else
                      @scores.reject!{|s| !((s.marks < @ranking_level.marks if @ranking_level.marks_limit_type=="upper") or (s.marks >= @ranking_level.marks if @ranking_level.marks_limit_type=="lower") or (s.marks == @ranking_level.marks if @ranking_level.marks_limit_type=="exact"))}
                    end
                  else
                    flash[:warn_notice]="#{t('flash19')}"
                    redirect_to :action=>"ranking_level_report" and return
                  end
                end
              end

            end

          end
        end
      else
        if params[:ranking_level_report][:course_id]==""
          flash[:notice]="#{t('flash13')}"
          redirect_to :action=>"ranking_level_report" and return
        else
          @course = Course.find(params[:ranking_level_report][:course_id])
          if @course.has_batch_groups_with_active_batches and (!params[:ranking_level_report][:batch_group_id].present? or params[:ranking_level_report][:batch_group_id]=="")
            flash[:warn_notice]="#{t('flash14')}"
            redirect_to :action=>"ranking_level_report" and return
          elsif params[:ranking_level_report].nil? or params[:ranking_level_report][:ranking_level_id]==""
            flash[:warn_notice]="#{t('flash17')}"
            redirect_to :action=>"ranking_level_report" and return
          else
            @ranking_level = RankingLevel.find(params[:ranking_level_report][:ranking_level_id])
            if @ranking_level.marks.nil? && !@course.gpa_enabled?
              flash[:warn_notice] = "#{t('flash23')}"
              redirect_to :action=>"ranking_level_report" and return
            elsif @ranking_level.gpa.nil? && @course.gpa_enabled?
              flash[:warn_notice] = "#{t('flash24')}"
              redirect_to :action=>"ranking_level_report" and return
            else
            
              if @course.has_batch_groups_with_active_batches
                @batch_group = BatchGroup.find(params[:ranking_level_report][:batch_group_id])
                @batches = @batch_group.batches
              else
                @batches = @course.active_batches
              end
              @students = Student.find_all_by_batch_id(@batches.collect(&:id))
              unless @ranking_level.subject_count.nil?
                @scores = GroupedExamReport.find(:all,:conditions=>{:student_id=>@students.collect(&:id),:score_type=>"s"})
              else
                unless @ranking_level.full_course==true
                  @scores = GroupedExamReport.find(:all,:conditions=>{:student_id=>@students.collect(&:id),:score_type=>"c"})
                else
                  @scores = []
                  @students.each do|student|
                    total_student_score = 0
                    avg_student_score = 0
                    marks = GroupedExamReport.find_all_by_student_id_and_score_type(student.id,"c")
                    unless marks.empty?
                      marks.map{|m| total_student_score+=m.marks}
                      avg_student_score = total_student_score.to_f/marks.count.to_f
                      marks.first.marks = avg_student_score
                      @scores.push marks.first
                    end
                  end
                end
              end
              unless @scores.empty?
                if @ranking_level.marks_limit_type=="upper"
                  @scores.reject!{|s| !(((s.marks < @ranking_level.gpa unless @ranking_level.gpa.nil?) if s.student.batch.gpa_enabled?) or (s.marks < @ranking_level.marks unless @ranking_level.marks.nil?))}
                elsif @ranking_level.marks_limit_type=="exact"
                  @scores.reject!{|s| !(((s.marks == @ranking_level.gpa unless @ranking_level.gpa.nil?) if s.student.batch.gpa_enabled?) or (s.marks == @ranking_level.marks unless @ranking_level.marks.nil?))}
                else
                  @scores.reject!{|s| !(((s.marks >= @ranking_level.gpa unless @ranking_level.gpa.nil?) if s.student.batch.gpa_enabled?) or (s.marks >= @ranking_level.marks unless @ranking_level.marks.nil?))}
                end
              else
                flash[:warn_notice]="#{t('flash20')}"
                redirect_to :action=>"ranking_level_report" and return
              end
            end
          end
        end
      end
    end
  end

  def student_ranking_level_report_pdf
    @ranking_level = RankingLevel.find(params[:ranking_level_id])
    @mode = params[:mode]
    if @mode=="batch"
      @batch = Batch.find(params[:batch_id])
      @report_type = params[:report_type]
      if @report_type=="subject"
        @students = @batch.students(:conditions=>{:is_active=>true,:is_deleted=>true})
        @subject = Subject.find(params[:subject_id])
        @scores = GroupedExamReport.find(:all,:conditions=>{:student_id=>@students.collect(&:id),:batch_id=>@batch.id,:subject_id=>@subject.id,:score_type=>"s"})
        if @batch.gpa_enabled?
          @scores.reject!{|s| !((s.marks < @ranking_level.gpa if @ranking_level.marks_limit_type=="upper") or (s.marks >= @ranking_level.gpa if @ranking_level.marks_limit_type=="lower") or (s.marks == @ranking_level.gpa if @ranking_level.marks_limit_type=="exact"))}
        else
          @scores.reject!{|s| !((s.marks < @ranking_level.marks if @ranking_level.marks_limit_type=="upper") or (s.marks >= @ranking_level.marks if @ranking_level.marks_limit_type=="lower") or (s.marks == @ranking_level.marks if @ranking_level.marks_limit_type=="exact"))}
        end
      else
        @students = @batch.students(:conditions=>{:is_active=>true,:is_deleted=>true})
        unless @ranking_level.subject_count.nil?
          unless @ranking_level.full_course==true
            @subjects = @batch.subjects
            @scores = GroupedExamReport.find(:all,:conditions=>{:student_id=>@students.collect(&:id),:batch_id=>@batch.id,:subject_id=>@subjects.collect(&:id),:score_type=>"s"})
          else
            @scores = GroupedExamReport.find(:all,:conditions=>{:student_id=>@students.collect(&:id),:score_type=>"s"})
          end
          if @batch.gpa_enabled?
            @scores.reject!{|s| !((s.marks < @ranking_level.gpa if @ranking_level.marks_limit_type=="upper") or (s.marks >= @ranking_level.gpa if @ranking_level.marks_limit_type=="lower") or (s.marks == @ranking_level.gpa if @ranking_level.marks_limit_type=="exact"))}
          else
            @scores.reject!{|s| !((s.marks < @ranking_level.marks if @ranking_level.marks_limit_type=="upper") or (s.marks >= @ranking_level.marks if @ranking_level.marks_limit_type=="lower") or (s.marks == @ranking_level.marks if @ranking_level.marks_limit_type=="exact"))}
          end
        else
          unless @ranking_level.full_course==true
            @scores = GroupedExamReport.find(:all,:conditions=>{:student_id=>@students.collect(&:id),:batch_id=>@batch.id,:score_type=>"c"})
          else
            @scores = []
            @students.each do|student|
              total_student_score = 0
              avg_student_score = 0
              marks = GroupedExamReport.find_all_by_student_id_and_score_type(student.id,"c")
              unless marks.empty?
                marks.map{|m| total_student_score+=m.marks}
                avg_student_score = total_student_score.to_f/marks.count.to_f
                marks.first.marks = avg_student_score
                @scores.push marks.first
              end
            end
          end
          if @batch.gpa_enabled?
            @scores.reject!{|s| !((s.marks < @ranking_level.gpa if @ranking_level.marks_limit_type=="upper") or (s.marks >= @ranking_level.gpa if @ranking_level.marks_limit_type=="lower") or (s.marks == @ranking_level.gpa if @ranking_level.marks_limit_type=="exact"))}
          else
            @scores.reject!{|s| !((s.marks < @ranking_level.marks if @ranking_level.marks_limit_type=="upper") or (s.marks >= @ranking_level.marks if @ranking_level.marks_limit_type=="lower") or (s.marks == @ranking_level.marks if @ranking_level.marks_limit_type=="exact"))}
          end
        end
      end
    else
      @course = Course.find(params[:course_id])
      if @course.has_batch_groups_with_active_batches
        @batch_group = BatchGroup.find(params[:batch_group_id])
        @batches = @batch_group.batches
      else
        @batches = @course.active_batches
      end
      @students = Student.find_all_by_batch_id(@batches.collect(&:id))
      unless @ranking_level.subject_count.nil?
        @scores = GroupedExamReport.find(:all,:conditions=>{:student_id=>@students.collect(&:id),:score_type=>"s"})
      else
        unless @ranking_level.full_course==true
          @scores = GroupedExamReport.find(:all,:conditions=>{:student_id=>@students.collect(&:id),:score_type=>"c"})
        else
          @scores = []
          @students.each do|student|
            total_student_score = 0
            avg_student_score = 0
            marks = GroupedExamReport.find_all_by_student_id_and_score_type(student.id,"c")
            unless marks.empty?
              marks.map{|m| total_student_score+=m.marks}
              avg_student_score = total_student_score.to_f/marks.count.to_f
              marks.first.marks = avg_student_score
              @scores.push marks.first
            end
          end
        end
      end
      if @ranking_level.marks_limit_type=="upper"
        @scores.reject!{|s| !(((s.marks < @ranking_level.gpa unless @ranking_level.gpa.nil?) if s.student.batch.gpa_enabled?) or (s.marks < @ranking_level.marks unless @ranking_level.marks.nil?))}
      elsif @ranking_level.marks_limit_type=="exact"
        @scores.reject!{|s| !(((s.marks == @ranking_level.gpa unless @ranking_level.gpa.nil?) if s.student.batch.gpa_enabled?) or (s.marks == @ranking_level.marks unless @ranking_level.marks.nil?))}
      else
        @scores.reject!{|s| !(((s.marks >= @ranking_level.gpa unless @ranking_level.gpa.nil?) if s.student.batch.gpa_enabled?) or (s.marks >= @ranking_level.marks unless @ranking_level.marks.nil?))}
      end
    end
    render :pdf=>"student_ranking_level_report_pdf"
  end

  def transcript
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    @batches = Batch.active
  end

  def student_transcript
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
      
    
    
    params[:transcript][:batch_id] = 0
    unless @batch_data.nil?
      params[:transcript][:batch_id] = @batch_data.id 
    end
    
    
    unless params[:transcript][:batch_id].present?
      unless params[:student][:batch_id].present?
        flash[:notice] = "#{t('select_a_batch')}"
        redirect_to :action=>"transcript" and return
      else
        params[:transcript][:batch_id] = params[:student][:batch_id]
      end      
    end
    if params[:transcript].nil? or params[:transcript][:student_id]==""
      flash[:notice] = "#{t('flash21')}"
      redirect_to :action=>"transcript" and return
    else
      @batch = Batch.find(params[:transcript][:batch_id])
      if params[:flag].present? and params[:flag]=="1"
        @students = Student.find_all_by_id(params[:student_id])
        if @students.empty?
          @students = ArchivedStudent.find_all_by_former_id(params[:student_id])
          @students.each do|student|
            student.id=student.former_id
          end
        end
        @flag = "1"
      else
        @students = @batch.students.by_first_name
      end
      unless @students.empty?
        unless !params[:student_id].present? or params[:student_id].nil?
          @student = Student.find_by_id(params[:student_id])
          if @student.nil?
            @student = ArchivedStudent.find_by_former_id(params[:student_id])
            @student.id = @student.former_id
          end
        else
          @student = @students.first
        end
        @grade_type = @batch.grading_type
        @batches=Batch.all(:select=>"DISTINCT batches.*",:joins=>"LEFT OUTER JOIN `batch_students` ON batch_students.batch_id = batches.id",:conditions=>["batch_students.student_id = ?",@student.id],:order=>"batch_students.id")
        @batches << @batch
      else
        flash[:notice] = "#{t('no_students_in_this_batch')}"
        redirect_to :action=>"transcript" and return
      end
    end
  end

  def student_transcript_pdf
    @student = Student.find_by_id(params[:student_id])
    if @student.nil?
      @student = ArchivedStudent.find_by_former_id(params[:student_id])
      @student.id = @student.former_id
    end
    @batch = @student.batch
    @grade_type = @batch.grading_type
    @batches=Batch.all(:select=>"DISTINCT batches.*",:joins=>"LEFT OUTER JOIN `batch_students` ON batch_students.batch_id = batches.id",:conditions=>["batch_students.student_id = ?",@student.id],:order=>"batch_students.id")
    @batches << @batch
    render :pdf=>"student_transcript_pdf"
  end

  def load_batch_students
    unless params[:id].nil? or params[:id]==""
      @batch = Batch.find(params[:id])
      @students = @batch.students.by_first_name
    else
      @students = []
    end
    render(:update) do|page|
      page.replace_html "student_selection", :partial=>"student_selection"
    end
  end

  def combined_report
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    @batches = Batch.active
  end

  def load_levels
    unless params[:batch_id].nil?
      @batch_id = params[:batch_id]
    else
      unless params[:student][:batch_name].nil?
        params[:batch_id] = params[:student][:batch_name]
      else
        unless params[:course_id].nil?
          @batch = Batch.find_by_course_id(params[:course_id])
          params[:batch_id] = @batch.id
        else 
          params[:batch_id] = 0
        end
      end
    end 
    
    unless params[:batch_id]==""
      @batch = Batch.find(params[:batch_id])
      @course = @batch.course
      @class_designations = @course.class_designations.all
      @ranking_levels = @course.ranking_levels.all.reject{|r| !(r.full_course==false)}
      render(:update) do|page|
        page.replace_html "levels", :partial=>"levels"
      end
    else
      render(:update) do|page|
        page.replace_html "levels", :text=>""
      end
    end
  end

  def student_combined_report
    if params[:combined_report][:batch_id]=="" or (params[:combined_report][:designation_ids].blank? and params[:combined_report][:level_ids].blank?)
      flash[:notice] = "#{t('flash22')}"
      redirect_to :action=>"combined_report" and return
    else
      @batch = Batch.find(params[:combined_report][:batch_id])
      @students = @batch.students
      unless params[:combined_report][:designation_ids].blank?
        @designations = ClassDesignation.find_all_by_id(params[:combined_report][:designation_ids])
      end
      unless params[:combined_report][:level_ids].blank?
        @levels = RankingLevel.find_all_by_id(params[:combined_report][:level_ids])
      end
    end
  end

  def student_combined_report_pdf
    @batch = Batch.find(params[:batch_id])
    @students = @batch.students
    unless params[:designations].blank?
      @designations = ClassDesignation.find_all_by_id(params[:designations])
    end
    unless params[:levels].blank?
      @levels = RankingLevel.find_all_by_id(params[:levels])
    end
    render :pdf=>"student_combined_report_pdf"#, :show_as_html=>true
  end



  def select_report_type
    unless params[:batch_id].nil? or params[:batch_id]==""
      @batch = Batch.find(params[:batch_id])
      @ranking_levels = RankingLevel.find_all_by_course_id(@batch.course_id)
      render(:update) do|page|
        page.replace_html "report_type_select", :partial=>"report_type_select"
      end
    else
      render(:update) do|page|
        page.replace_html "report_type_select", :text=>""
      end
    end
  end

  def generated_report3
    #student-subject-wise-report
    @student = Student.find(params[:student])
    @batch = @student.batch
    @subject = Subject.find(params[:subject])
    @exam_groups = ExamGroup.active.find(:all,:conditions=>{:batch_id=>@batch.id})
    @exam_groups.reject!{|e| e.result_published==false}
    @graph = open_flash_chart_object(950, 450,
      "/exam/graph_for_generated_report3?subject=#{@subject.id}&student=#{@student.id}")
  end
  def generated_report_all_subject
    @student = Student.find(params[:student])
    @exam_type = params[:exam_type]
    @graph = open_flash_chart_object(950, 450,
      "/exam/graph_for_generated_report_all_subject?student=#{@student.id}&exam_type=#{@exam_type}")
  end
  
  def final_report_type_new
    if params[:batch_id].nil?
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
      
      @batch_id = 0
      unless @batch_data.nil?
        @batch_id = @batch_data.id 
      end
    else
      batch = Batch.find(params[:batch_id])
      @batch_id = batch.id
    end
    batch = Batch.find(@batch_id)
    if params[:for_batch_rank].nil?
      @grouped_exams = GroupedExam.find_all_by_batch_id(batch.id)
    else
      @for_batch_rank = params[:for_batch_rank]
    end 
    
    @all_connect_exam = ExamConnect.active.find_all_by_batch_id(batch.id);
    
    render(:update) do |page|
      page.replace_html 'report_type',:partial=>'report_type_new'
    end
  end
  
  

  def final_report_type
    if params[:batch_id].nil?
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
      
      @batch_id = 0
      unless @batch_data.nil?
        @batch_id = @batch_data.id 
      end
    else
      batch = Batch.find(params[:batch_id])
      @batch_id = batch.id
    end
    batch = Batch.find(@batch_id)
    if params[:for_batch_rank].nil?
      @grouped_exams = GroupedExam.find_all_by_batch_id(batch.id)
    else
      @for_batch_rank = params[:for_batch_rank]
    end  
    render(:update) do |page|
      page.replace_html 'report_type',:partial=>'report_type'
    end
  end
  
  def batch_rank
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
    @exam_groups = []
  end
  
  def grouped_exam_report
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
    @exam_groups = []
  end
  
  def grouped_exam_report_new
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
    @exam_groups = []
  end
  def merit_list
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    @assigned_employee = @batch.all_class_teacher
    @report_data = Rails.cache.fetch("continues_#{@id}_#{@batch.id}"){
      get_continues(@id,@batch.id)
      report_data = []
      if @student_response['status']['code'].to_i == 200
        report_data = @student_response['data']
      end
      report_data
    }
    @exam_comment_all = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
    student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id,true)
    @tabulation_data = []
    if student_response['status']['code'].to_i == 200
      @tabulation_data = student_response['data']
    end
    render :pdf => 'merit_list',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
   
  end
  def subject_wise_pass_failed
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
  
    @batch = Batch.find(@connect_exam_obj.batch_id)
    
    if @tabulation_data.nil?
      student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id,true)
      @tabulation_data = []
      if student_response['status']['code'].to_i == 200
        @tabulation_data = student_response['data']
      end
    end
    @class = params[:class]
    finding_data5()
    render :pdf => 'subject_wise_pass_failed',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 28,
      :bottom => 30,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_header_summary.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_footer_sagc.html'}}
  end
  def summary_report
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
  
    @batch = Batch.find(@connect_exam_obj.batch_id)
    
    if @tabulation_data.nil?
      student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id,true)
      @tabulation_data = []
      if student_response['status']['code'].to_i == 200
        @tabulation_data = student_response['data']
      end
    end
    @class = params[:class]
    finding_data5()
    render :pdf => 'summary_report',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 28,
      :bottom => 30,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_header_summary.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_footer_sagc.html'}}
  end
  def mert_list_sagc
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
  
    @batch = Batch.find(@connect_exam_obj.batch_id)
    
    if @tabulation_data.nil?
      student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id,true)
      @tabulation_data = []
      if student_response['status']['code'].to_i == 200
        @tabulation_data = student_response['data']
      end
    end
    @class = params[:class]
    finding_data5()
    if @student_list_first_term.blank?
      @subject_highest_1st_term = @subject_highest_2nd_term
      @student_position_first_term = @student_position_second_term
      @student_position_first_term_batch = @student_position_second_term_batch
    end
    @std_resutl = []
   
    iloop = 0
    if !@student_result.blank?
      @student_result.each do |std_result|
        
        position = 50000
        if !@student_position_first_term.blank? && !@student_position_first_term[std_result['id'].to_i].blank?
          position = @student_position_first_term[std_result['id'].to_i]
        else
          unless std_result['subject_failed'].blank?
            position = position+std_result['subject_failed'].count
          end
        end  
        @student_result[iloop]['position'] = position
        iloop = iloop+1
      end
      @student_result.sort! { |x, y| x["position"] <=> y["position"] }
    end
    render :pdf => 'merit_list_sagc',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 32,
      :bottom => 30,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_header_sagc.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def tabulation_excell
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'tabulation'
    
    
    center_align_format = Spreadsheet::Format.new :horizontal_align => :center,  :vertical_align => :middle
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
  
    @batch = Batch.find(@connect_exam_obj.batch_id)
    
    if @tabulation_data.nil?
      student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id,true)
      @tabulation_data = []
      if student_response['status']['code'].to_i == 200
        @tabulation_data = student_response['data']
      end
    end
    finding_data5()
    if @student_list_first_term.blank?
      @subject_highest_1st_term = @subject_highest_2nd_term
      @student_position_first_term = @student_position_second_term
      @student_position_first_term_batch = @student_position_second_term_batch
    end
    
 
    
    row_first = ['Srl','S. ID','Roll','Student Name','Total','GPA & GP','LG','M.C','M.S','WD','PD']
    starting_row = 11
    sub_id_array = []
    @subject_result.each do |key,sub_res|
      sub_id_array << key
    end
    @all_subject_connect_exam = Subject.find_all_by_id(sub_id_array,:order=>"priority asc")
    @all_subject_connect_exam.each do |value|
      key = value.id.to_s
      end_row = starting_row+7
      (starting_row..end_row).each do |i|
        sheet1.row(i).default_format = center_align_format
      end
      row_first << @subject_result[key]['name']
      row_first << ""
      row_first << ""
      row_first << ""
      row_first << ""
      row_first << ""
      row_first << ""
      row_first << ""
      new_book.worksheet(0).merge_cells(0,starting_row,0,end_row)
      starting_row = starting_row+8
    end
    new_book.worksheet(0).insert_row(0, row_first)
    
    new_book.worksheet(0).merge_cells(0,0,1,0)
    new_book.worksheet(0).merge_cells(0,1,1,1)
    new_book.worksheet(0).merge_cells(0,2,1,2)
    new_book.worksheet(0).merge_cells(0,3,1,3)
    new_book.worksheet(0).merge_cells(0,4,1,4)
    new_book.worksheet(0).merge_cells(0,5,1,5)
    new_book.worksheet(0).merge_cells(0,6,1,6)
    new_book.worksheet(0).merge_cells(0,7,1,7)
    new_book.worksheet(0).merge_cells(0,8,1,8)
    new_book.worksheet(0).merge_cells(0,9,1,9)
    new_book.worksheet(0).merge_cells(0,10,1,10)
    
    row_first = ['','','','','','','','','','','']
    @all_subject_connect_exam.each do |sub_result|
      row_first << "AT"
      row_first << "CW"
      row_first << "OB"
      row_first << "SB"
      row_first << "PR"
      row_first << "+RT"
      row_first << "+CT"
      row_first << "LG"
    end
    new_book.worksheet(0).insert_row(1, row_first)
    
    std_loop = 2
    @student_result.each do |std_result|
      tmp_row = []
      tmp_row << std_result['sl']
      tmp_row << std_result['sid'].to_s
      tmp_row << std_result['roll'].to_s
      tmp_row << std_result['name'].to_s
      tmp_row << std_result['grand_total'].to_s
      tmp_row << std_result['gp'].to_s+"("+std_result['gpa'].to_s+")"
      if !@student_position_first_term_batch.blank? && !@student_position_first_term_batch[std_result['id'].to_i].blank?
        tmp_row << std_result['lg']
      else
        tmp_row << "F"
      end 
      
      if @batch.name == "Morning English" 
        if !@student_position_first_term_batch.blank? && !@student_position_first_term_batch[std_result['id'].to_i].blank?
          tmp_row << @student_position_first_term_batch[std_result['id'].to_i]
        else
          tmp_row << ""
        end  
      else
        if !@student_position_first_term.blank? && !@student_position_first_term[std_result['id'].to_i].blank?
          tmp_row <<  @student_position_first_term[std_result['id'].to_i]
        else
          tmp_row << ""
        end 
      end
      
      if !@student_position_first_term_batch.blank? && !@student_position_first_term_batch[std_result['id'].to_i].blank?
        tmp_row << @student_position_first_term_batch[std_result['id'].to_i]
      else
        tmp_row << ""
      end
      
      tmp_row << ""
      tmp_row << ""
      unless std_result['subjects'].blank?
        @all_subject_connect_exam.each do |value|
          key = value.id.to_s
          unless std_result['subjects'][key].blank?
            
            tmp_row << std_result['subjects'][key]['result']['at'].to_s
            tmp_row << std_result['subjects'][key]['result']['cw'].to_s
            tmp_row << std_result['subjects'][key]['result']['ob'].to_s
            tmp_row << std_result['subjects'][key]['result']['sb'].to_s
            tmp_row << std_result['subjects'][key]['result']['pr'].to_s
            tmp_row << std_result['subjects'][key]['result']['rt'].to_s
            tmp_row << std_result['subjects'][key]['result']['ct'].to_s
            tmp_row << std_result['subjects'][key]['result']['lg'].to_s
          else
            tmp_row << "-"
            tmp_row << "-"
            tmp_row << "-"
            tmp_row << "-"
            tmp_row << "-"
            tmp_row << "-"
            tmp_row << "-"
            tmp_row << "-"
            
          end  
        end
      end
      new_book.worksheet(0).insert_row(std_loop, tmp_row)
      
      
      std_loop = std_loop+1
      
    end
    batch_split = @batch.name.split(" ")
    sheet1.add_header("SHAHEED BIR UTTAM LT. ANWAR GIRLS' COLLEGE (Tabulation Sheet : "+@connect_exam_obj.name.to_s+")
 Program :"+@batch.course.course_name.to_s+" || Group :"+@batch.course.group.to_s+" || Section :"+@batch.course.section_name.to_s+" || Shift :"+batch_split[0]+" || Session :"+@batch.course.session.to_s+" || Version :"+batch_split[1]+"
      ")
    sheet1.add_footer("TIPS :: M.C = Merit in Class  ||  M.S = Merit in Section  ||  +RT = Raw Total  ||  +CT = Converted Total")
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => @batch.full_name + "-" + @connect_exam_obj.name + ".xls", :type =>  "application/vnd.ms-excel"
  end
  
  
  
  def continues
    @id = params[:id]
    
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
   
    
    pdf_name = "continues_connect_exam_"+@connect_exam_obj.id.to_s+".pdf"
    dirname = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"continues","0"+@connect_exam_obj.id.to_s)
    unless File.directory?(dirname)
      FileUtils.mkdir_p(dirname)
    end
    FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s))
    file_name = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"continues","0"+@connect_exam_obj.id.to_s,pdf_name)
    champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_from = champs21_config['from']
    if File.file?(file_name) && Rails.cache.exist?("continues_#{@id}_#{@batch.id}") && api_from != "local" && api_from != "remote" && MultiSchool.current_school.id != 312
      FileUtils.chown 'champs21','champs21',file_name
      redirect_to "/result_pdf/0"+MultiSchool.current_school.id.to_s+"/0"+@batch.id.to_s+"/continues/0"+@connect_exam_obj.id.to_s+"/"+pdf_name
    else
      @assigned_employee=@batch.all_class_teacher
      get_continues(@id,@batch.id)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end 
      @exam_comment_all = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
      render_connect_exam("continues",false,file_name)  
    end
  end
  
  def add_comments_connect_exam
    @comments= params[:comments].split('||') 
    @student_ids=params[:student_ids].split('||')
    @connect_exam= params[:connect_exam]
    i = 0
    now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    unless @comments.blank? 
      @comments.each do |cmt|
        @exam_comment_exists = ExamConnectComment.find_by_exam_connect_id_and_student_id(@connect_exam.to_i,@student_ids[i].to_i)



        if !cmt.blank?     
          if @exam_comment_exists.blank?
            ecobj = ExamConnectComment.new
            ecobj.employee_id = current_user.employee_record.id
            ecobj.created_at = now
            ecobj.updated_at = now
            ecobj.school_id = MultiSchool.current_school.id
            ecobj.student_id = @student_ids[i]
            ecobj.comments = cmt
            ecobj.exam_connect_id = @connect_exam
            ecobj.save
          else
            @exam_comment_exists.comments = cmt
            @exam_comment_exists.updated_at = now
            @exam_comment_exists.save          
          end
        elsif !@exam_comment_exists.blank?
          @exam_comment_exists.destroy
        end         
        i = i+1
      end
    else
      @student_ids.each do |std|
        @exam_comment_exists = ExamConnectComment.find_by_exam_connect_id_and_student_id(@connect_exam.to_i,std.to_i)
        if !@exam_comment_exists.blank?
          @exam_comment_exists.destroy
        end
      end
    end  
    render :text =>"Succesfully Saved" and return
    
  end
  
  def d_grade_students
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    get_tabulation(@id,@batch.id)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
    render :pdf => 'd_grade_students',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def section_wise_subject_comparisam
    @id = params[:id]
    @subject_id = params[:subject_id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id) 
    @subject = Subject.find(@subject_id)
    @student_response = get_tabulation_connect_exam(@id,@batch.id,true)
    @graph_data = []
    @grading_levels = GradingLevel.for_batch(@batch.id)
    if @grading_levels.blank?
      @grading_levels = GradingLevel.default
    end
    if @student_response['status']['code'].to_i == 200
      @tabulation_data = @student_response['data']
    end
    render :layout => false
  end
  
  def comment_tabulation
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    @assigned_employee=@batch.all_class_teacher
    if  MultiSchool.current_school.id == 312
      get_tabulation(@id,@batch.id)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end 
    else
      @report_data = Rails.cache.fetch("tabulation_#{@id}_#{@batch.id}"){
        get_tabulation(@id,@batch.id)
        report_data = []
        if @student_response['status']['code'].to_i == 200
          report_data = @student_response['data']
        end
        report_data
      }
    end
    @exam_comment = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id) 
    @student_exam_comment = {}
    
    @exam_comment.each do |cmt|
      @student_exam_comment[cmt.student_id.to_s] = cmt.comments
    end 
  end 

  def comment_tabulation_pdf
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    @assigned_employee=@batch.all_class_teacher
    pdf_name = "comment_tabulation_connect_exam_"+@connect_exam_obj.id.to_s+".pdf"
    dirname = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"tabulation","0"+@connect_exam_obj.id.to_s)
    unless File.directory?(dirname)
      FileUtils.mkdir_p(dirname)
    end
    FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s))
    file_name = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"tabulation","0"+@connect_exam_obj.id.to_s,pdf_name)
    champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_from = champs21_config['from']
    if File.file?(file_name) && Rails.cache.exist?("tabulation_#{@id}_#{@batch.id}") && api_from != "local" && MultiSchool.current_school.id != 312 && MultiSchool.current_school.id != 346
      FileUtils.chown 'champs21','champs21',file_name
      redirect_to "/result_pdf/0"+MultiSchool.current_school.id.to_s+"/0"+@batch.id.to_s+"/tabulation/0"+@connect_exam_obj.id.to_s+"/"+pdf_name
    else
      if  MultiSchool.current_school.id == 312
        get_tabulation(@id,@batch.id)
        @report_data = []
        if @student_response['status']['code'].to_i == 200
          @report_data = @student_response['data']
        end 
      else
        @report_data = Rails.cache.fetch("tabulation_#{@id}_#{@batch.id}"){
          get_tabulation(@id,@batch.id)
          report_data = []
          if @student_response['status']['code'].to_i == 200
            report_data = @student_response['data']
          end
          report_data
        }
      end
      @exam_comment = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id) 
      @student_exam_comment = {}

      @exam_comment.each do |cmt|
        @student_exam_comment[cmt.student_id.to_s] = cmt.comments
      end
      render :pdf => 'comment_tabulation_pdf',
        :orientation => 'Landscape', :zoom => 1.00,:save_to_file => file_name,
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    end
    
  end 

  def class_performance_student
    @id = params[:id]
    @subject_id = params[:subject_id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    @assigned_employee=@batch.all_class_teacher
      
    get_tabulation(@id,@batch.id)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end 
    
    unless @subject_id.blank?
      @subject = Subject.find_by_id(@subject_id)
    end 

    @exam_comment = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
    render :pdf => "class_performance_student",
      :orientation => 'Portrait',
      :margin => {:top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}} 
  end
  
  def failed_grade
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    @connect_exam_result_type = @connect_exam_obj.result_type
    render :pdf => 'failed_grade',
      :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def tabulation
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    @assigned_employee=@batch.all_class_teacher
    
    pdf_name = "tabulation_connect_exam_"+@connect_exam_obj.id.to_s+".pdf"
    dirname = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"tabulation","0"+@connect_exam_obj.id.to_s)
    unless File.directory?(dirname)
      FileUtils.mkdir_p(dirname)
    end
    FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s))
    file_name = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"tabulation","0"+@connect_exam_obj.id.to_s,pdf_name)
    champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_from = champs21_config['real_from']
    if File.file?(file_name) && Rails.cache.exist?("tabulation_#{@id}_#{@batch.id}") && api_from != "local" && MultiSchool.current_school.id != 312 && MultiSchool.current_school.id == 312
      FileUtils.chown 'champs21','champs21',file_name
      redirect_to "/result_pdf/0"+MultiSchool.current_school.id.to_s+"/0"+@batch.id.to_s+"/tabulation/0"+@connect_exam_obj.id.to_s+"/"+pdf_name
    else
      
      if  MultiSchool.current_school.id == 312
        get_tabulation(@id,@batch.id)
        @report_data = []
        if @student_response['status']['code'].to_i == 200
          @report_data = @student_response['data']
        end 
      else
        @report_data = Rails.cache.fetch("tabulation_#{@id}_#{@batch.id}"){
          get_tabulation(@id,@batch.id)
          report_data = []
          if @student_response['status']['code'].to_i == 200
            report_data = @student_response['data']
          end
          report_data
        }
      end
     
  
       
      @exam_comment = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
      if (MultiSchool.current_school.id == 280 && @connect_exam_obj.result_type==2) or 
          (MultiSchool.current_school.id == 323 && @connect_exam_obj.result_type==6)
        render :pdf => 'tabulation',
          :orientation => 'Landscape', :zoom => 1.00,:save_to_file => file_name,
          :page_size => 'Legal',
          :margin => {    :top=> 10,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}} 
      else
        render :pdf => 'tabulation',
          :orientation => 'Landscape', :zoom => 1.00,:save_to_file => file_name,
          :margin => {    :top=> 10,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      end
    end  
  end
  
  def marksheet    
    @id = params[:id]
    @subject_id = params[:subject_id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id) 
    @subject = Subject.find(@subject_id)
    @assigned_employee=@batch.all_class_teacher
    
    
    pdf_name = "marksheet_connect_exam_"+@subject_id.to_s+"_"+@connect_exam_obj.id.to_s+".pdf"
    dirname = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"marksheet","0"+@connect_exam_obj.id.to_s)
    unless File.directory?(dirname)
      FileUtils.mkdir_p(dirname)
    end
    FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s))
    file_name = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"marksheet","0"+@connect_exam_obj.id.to_s,pdf_name)
    
    champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_from = champs21_config['from']
    
    #    if File.file?(file_name) && Rails.cache.exist?("marksheet_#{@id}_#{@subject_id}") && api_from != "local"
    #      FileUtils.chown 'champs21','champs21',file_name
    #      redirect_to "/result_pdf/0"+MultiSchool.current_school.id.to_s+"/0"+@batch.id.to_s+"/marksheet/0"+@connect_exam_obj.id.to_s+"/"+pdf_name
    #    else
    @grades = @batch.grading_level_list

    @employee_sub = EmployeesSubject.find_by_subject_id(@subject_id)
    if !@employee_sub.nil?
      @employee = Employee.find(@employee_sub.employee_id)
    end
      
    get_subject_mark_sheet(@id,@subject_id)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
      
    if MultiSchool.current_school.id == 340 && params[:evaluation].blank?
      render :pdf => 'marksheet',
        :orientation => 'Landscape', :zoom => 1.00,:save_to_file => file_name,
        :page_size => 'A4',
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    elsif MultiSchool.current_school.id == 323
      render :pdf => 'marksheet',
        :orientation => 'Portrait', :zoom => 1.00,:save_to_file => file_name,
        :page_size => 'A4',
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    elsif MultiSchool.current_school.id == 348
      render :pdf => 'marksheet',
        :orientation => 'Landscape', :zoom => 1.00,:save_to_file => file_name,
        :page_size => 'A4',
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    else 
      render :pdf => 'marksheet',
        :orientation => 'Landscape', :zoom => 1.00,:save_to_file => file_name,
        :page_size => 'Legal',
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    end
    
  end
  
  def effot_gradesheet    
    #This is used for Sir John Wilson School Report Card First Term First Half Effort/Grade Sheet
    @id = params[:id]
    @subject_id = params[:subject_id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    
    @batch = Batch.find(@connect_exam_obj.batch_id) 
    @subject = Subject.find(@subject_id)
    
    @grades = @batch.grading_level_list
    
    @employee_sub = EmployeesSubject.find_by_subject_id(@subject_id)
    if !@employee_sub.nil?
      @employee = Employee.find(@employee_sub.employee_id)
    end
    @report_data = Rails.cache.fetch("marksheet_#{@id}_#{@subject_id}"){
      get_subject_mark_sheet(@id,@subject_id)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    }    
    render :pdf => 'effot_gradesheet',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def score_sheet    
    #This is used for Sir John Wilson School Report Card Half Yearly Grade Sheet
    @id = params[:id]
    @subject_id = params[:subject_id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    
    @batch = Batch.find(@connect_exam_obj.batch_id) 
    @subject = Subject.find(@subject_id)
    
    @grades = @batch.grading_level_list
    
    @employee_sub = EmployeesSubject.find_by_subject_id(@subject_id)
    if !@employee_sub.nil?
      @employee = Employee.find(@employee_sub.employee_id)
    end
    @report_data = Rails.cache.fetch("marksheet_#{@id}_#{@subject_id}"){
      get_subject_mark_sheet(@id,@subject_id)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    }  
    
    if @tabulation_data.nil?
      student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id,true)
      @tabulation_data = []
      if student_response['status']['code'].to_i == 200
        @tabulation_data = student_response['data']
      end
    end
    
    render :pdf => 'score_sheet',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def exam_connect_comment_entry
    @connect_exam = params[:connect_exam]
    @connect_exam_obj = ExamConnect.active.find(@connect_exam)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    @students=@batch.students.by_first_name
    @comments = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
  end
  
  def generated_report5
    unless params[:batch_id].nil?
      params[:exam_report] = {}
      params[:exam_report][:batch_id] = params[:batch_id]
    end 
    if params[:student].nil? or !params[:student][:class_name].nil?
      if params[:exam_report].nil? or params[:exam_report][:batch_id].empty?
        flash[:notice] = "#{t('select_a_batch_to_continue')}"
        redirect_to :action=>'grouped_exam_report_new' and return
      end
    else
      if params[:type].nil?
        flash[:notice] = "#{t('invalid_parameters')}"
        redirect_to :action=>'grouped_exam_report_new' and return
      end
    end
    
    if params[:student].nil?  or params[:student][:connect_exam].blank? 
      if params[:connect_exam].blank? 
        flash[:notice] = "#{t('select_a_combined_exam_please')}"
        redirect_to :action=>'grouped_exam_report_new' and return
      else
        @connect_exam = params[:connect_exam]
      end  
    else
      @connect_exam = params[:student][:connect_exam]
    end 
    
    
    @connect_exam_obj = ExamConnect.active.find(@connect_exam)
    
    
    
    
    @previous_batch = 0
    if params[:student].nil? or !params[:student][:class_name].nil?
      @type = params[:type]
      @batch = Batch.find(params[:exam_report][:batch_id])
      @students=@batch.students.by_first_name
      @student = @students.first  unless @students.empty?
      if @student.blank?
        flash[:notice] = "#{t('flash5')}"
        redirect_to :action=>'grouped_exam_report_new' and return
      end
      #      @report_data = Rails.cache.fetch("student_exam_#{@connect_exam}_#{@batch.id}_#{@student.id}"){
      get_exam_report(@connect_exam,@student.id,@batch.id)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
      #      report_data
      #      }
      @exam_comment = ExamConnectComment.find_by_exam_connect_id_and_student_id(@connect_exam_obj.id,@student.id)
      
    else
      @student = Student.find(params[:student])
      if params[:batch].present?
        @batch = Batch.find(params[:batch])
        @previous_batch = 1
      else
        @batch = @student.batch
      end
      @type  = params[:type]
      # @report_data = Rails.cache.fetch("student_exam_#{@connect_exam}_#{@batch.id}_#{@student.id}"){
      get_exam_report(@connect_exam,@student.id,@batch.id)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
      #      report_data
      #      }
      if current_user.admin? or current_user.employee?  
      
        now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
        if !params[:comments].blank?   
          @exam_comment = ExamConnectComment.find_by_exam_connect_id_and_student_id(@connect_exam_obj.id,@student.id)
          if @exam_comment.blank?
            exam_comment_new = ExamConnectComment.new
            exam_comment_new.exam_connect_id = @connect_exam_obj.id
            exam_comment_new.comments = params[:comments]
            exam_comment_new.student_id = @student.id
            exam_comment_new.employee_id = current_user.employee_record.id
            exam_comment_new.created_at = now
            exam_comment_new.updated_at = now
            exam_comment_new.school_id = MultiSchool.current_school.id
            exam_comment_new.save 
          else
            @exam_comment.update_attribute(:comments,params[:comments])
          end     
        end
      end
      @exam_comment = ExamConnectComment.find_by_exam_connect_id_and_student_id(@connect_exam_obj.id,@student.id)
      
      if request.xhr?
        render(:update) do |page|
          page.replace_html   'grouped_exam_report', :partial=>"grouped_exam_report_new"
        end
      else
        @students = Student.find_all_by_id(params[:student])
      end
    end
    
   
    
  end

  def generated_report4
    if params[:student].nil? or !params[:student][:class_name].nil?
      if params[:exam_report].nil? or params[:exam_report][:batch_id].empty?
        flash[:notice] = "#{t('select_a_batch_to_continue')}"
        redirect_to :action=>'grouped_exam_report' and return
      end
    else
      if params[:type].nil?
        flash[:notice] = "#{t('invalid_parameters')}"
        redirect_to :action=>'grouped_exam_report' and return
      end
    end
    @previous_batch = 0
    #grouped-exam-report-for-batch
    if params[:student].nil? or !params[:student][:class_name].nil?
      @type = params[:type]
      @batch = Batch.find(params[:exam_report][:batch_id])
      @students=@batch.students.by_first_name
      @student = @students.first  unless @students.empty?
      if @student.blank?
        flash[:notice] = "#{t('flash5')}"
        redirect_to :action=>'grouped_exam_report' and return
      end
      if @type == 'grouped'
        @grouped_exams = GroupedExam.find_all_by_batch_id(@batch.id)
        @exam_groups = []
        @grouped_exams.each do |x|
          @exam_groups.push ExamGroup.active.find(x.exam_group_id)
        end
      else
        @exam_groups = ExamGroup.active.find_all_by_batch_id(@batch.id)
        @exam_groups.reject!{|e| e.result_published==false}
      end
      general_subjects = Subject.find_all_by_batch_id(@batch.id, :conditions=>"elective_group_id IS NULL AND is_deleted=false")
      student_electives = StudentsSubject.find_all_by_student_id(@student.id,:conditions=>"batch_id = #{@batch.id}")
      elective_subjects = []
      student_electives.each do |elect|
        subject = Subject.find_by_id(elect.subject_id, :conditions=>"is_deleted=0")
        unless subject.blank?
          elective_subjects.push subject
        end
      end
      @subjects = general_subjects + elective_subjects
      @subjects.reject!{|s| (s.no_exams==true or s.exam_not_created(@exam_groups.collect(&:id)))}
    else
      @student = Student.find(params[:student])
      if params[:batch].present?
        @batch = Batch.find(params[:batch])
        @previous_batch = 1
      else
        @batch = @student.batch
      end
      @type  = params[:type]
      if params[:type] == 'grouped'
        @grouped_exams = GroupedExam.find_all_by_batch_id(@batch.id)
        @exam_groups = []
        @grouped_exams.each do |x|
          @exam_groups.push ExamGroup.active.find(x.exam_group_id)
        end
      else
        @exam_groups = ExamGroup.active.find_all_by_batch_id(@batch.id)
        @exam_groups.reject!{|e| e.result_published==false}
      end
      general_subjects = Subject.find_all_by_batch_id(@batch.id, :conditions=>"elective_group_id IS NULL AND is_deleted=false")
      student_electives = StudentsSubject.find_all_by_student_id(@student.id,:conditions=>"batch_id = #{@batch.id}")
      elective_subjects = []
      student_electives.each do |elect|
        subject = Subject.find_by_id(elect.subject_id, :conditions=>"is_deleted=0")
        unless subject.blank?
          elective_subjects.push subject
        end
      end
      @subjects = general_subjects + elective_subjects
      @subjects.reject!{|s| (s.no_exams==true or s.exam_not_created(@exam_groups.collect(&:id)))}
      if request.xhr?
        render(:update) do |page|
          page.replace_html   'grouped_exam_report', :partial=>"grouped_exam_report"
        end
      else
        @students = Student.find_all_by_id(params[:student])
      end
    end


  end
  
  def generated_report5_pdf
    

    if params[:student].nil?  or params[:student][:connect_exam].blank? 
      if params[:connect_exam].blank? 
        flash[:notice] = "#{t('select_a_combined_exam_please')}"
        redirect_to :action=>'grouped_exam_report_new' and return
      else
        @connect_exam = params[:connect_exam]
      end  
    else
      @connect_exam = params[:student][:connect_exam]
    end 
    
    @connect_exam_obj = ExamConnect.active.find(@connect_exam)
    
    if params[:student].nil?
      @type = params[:type]
      @batch = Batch.find(params[:exam_report][:batch_id])
      @assigned_employee=@batch.all_class_teacher
      @student = @batch.students.first
      if @student.blank?
        flash[:notice] = "#{t('flash5')}"
        redirect_to :action=>'grouped_exam_report_new' and return
      end
      
      #      @report_data = Rails.cache.fetch("student_exam_#{@connect_exam}_#{@batch.id}_#{@student.id}"){
      get_exam_report(@connect_exam,@student.id,@batch.id)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
      #      report_data
      #      }
    else
      @student = Student.find(params[:student])
      @batch = Batch.find_by_id(params[:batch_id])
      @assigned_employee=@batch.all_class_teacher
      if @student.blank?
        flash[:notice] = "#{t('flash5')}"
        redirect_to :action=>'grouped_exam_report_new' and return
      end
      
      #      @report_data = Rails.cache.fetch("student_exam_#{@connect_exam}_#{@batch.id}_#{@student.id}"){
      get_exam_report(@connect_exam,@student.id,@batch.id)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
      #      report_data
      #      }
      
    end
    
    pdf_name = "continues_connect_exam_"+@student.id.to_s+"_"+@connect_exam_obj.id.to_s+".pdf"
    dirname = Rails.root.join('public','all_result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"continues","0"+@connect_exam_obj.id.to_s)
    unless File.directory?(dirname)
      FileUtils.mkdir_p(dirname)
    end
    FileUtils.chmod_R(0777, Rails.root.join('public','all_result_pdf',"0"+MultiSchool.current_school.id.to_s))
    file_name = Rails.root.join('public','all_result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"continues","0"+@connect_exam_obj.id.to_s,pdf_name)
    
    @exam_comment = ExamConnectComment.find_by_exam_connect_id_and_student_id(@connect_exam_obj.id,@student.id)
    render_connect_exam("generated_report5_pdf",false,file_name)

  end
  
  
  def generated_report4_pdf
    #grouped-exam-report-for-batch
    if params[:student].nil?
      @type = params[:type]
      @batch = Batch.find(params[:exam_report][:batch_id])
      @student = @batch.students.first
      if @type == 'grouped'
        @grouped_exams = GroupedExam.find_all_by_batch_id(@batch.id)
        @exam_groups = []
        @grouped_exams.each do |x|
          @exam_groups.push ExamGroup.active.find(x.exam_group_id)
        end
      else
        @exam_groups = ExamGroup.active.find_all_by_batch_id(@batch.id)
        @exam_groups.reject!{|e| e.result_published==false}
      end
      general_subjects = Subject.find_all_by_batch_id(@batch.id, :conditions=>"elective_group_id IS NULL and is_deleted=false")
      student_electives = StudentsSubject.find_all_by_student_id(@student.id,:conditions=>"batch_id = #{@batch.id}")
      elective_subjects = []
      student_electives.each do |elect|
        subject = Subject.find_by_id(elect.subject_id, :conditions=>"is_deleted=0")
        unless subject.blank?
          elective_subjects.push subject
        end
      end
      @subjects = general_subjects + elective_subjects
      @subjects.reject!{|s| s.no_exams==true}
      exams = Exam.find_all_by_exam_group_id(@exam_groups.collect(&:id))
      subject_ids = exams.collect(&:subject_id)
      @subjects.reject!{|sub| !(subject_ids.include?(sub.id))}
    else
      @student = Student.find(params[:student])
      @batch = Batch.find_by_id(params[:batch_id])
      @type  = params[:type]
      if params[:type] == 'grouped'
        @grouped_exams = GroupedExam.find_all_by_batch_id(@batch.id)
        @exam_groups = []
        @grouped_exams.each do |x|
          @exam_groups.push ExamGroup.active.find(x.exam_group_id)
        end
      else
        @exam_groups = ExamGroup.active.find_all_by_batch_id(@batch.id)
        @exam_groups.reject!{|e| e.result_published==false}
      end
      general_subjects = Subject.find_all_by_batch_id(@batch.id, :conditions=>"elective_group_id IS NULL and is_deleted=false")
      student_electives = StudentsSubject.find_all_by_student_id(@student.id,:conditions=>"batch_id = #{@batch.id}")
      elective_subjects = []
      student_electives.each do |elect|
        subject = Subject.find_by_id(elect.subject_id, :conditions=>"is_deleted=0")
        unless subject.blank?
          elective_subjects.push subject
        end
      end
      @subjects = general_subjects + elective_subjects
      @subjects.reject!{|s| s.no_exams==true}
      exams = Exam.find_all_by_exam_group_id(@exam_groups.collect(&:id))
      subject_ids = exams.collect(&:subject_id)
      @subjects.reject!{|sub| !(subject_ids.include?(sub.id))}
    end
    render :pdf => 'generated_report4_pdf',
      :orientation => 'Landscape', :zoom => 0.68
    #    respond_to do |format|
    #      format.pdf { render :layout => false }
    #    end

  end

  def combined_grouped_exam_report_pdf
    @type = params[:type]
    @batch = Batch.find(params[:batch])
    @students = @batch.students.by_first_name
    if @type == 'grouped'
      @grouped_exams = GroupedExam.find_all_by_batch_id(@batch.id)
      @exam_groups = []
      @grouped_exams.each do |x|
        @exam_groups.push ExamGroup.active.find(x.exam_group_id)
      end
    else
      @exam_groups = ExamGroup.active.find_all_by_batch_id(@batch.id)
      @exam_groups.reject!{|e| e.result_published==false}
    end
    render :pdf => 'combined_grouped_exam_report_pdf'
  end

  def previous_years_marks_overview
    @student = Student.find(params[:student])
    @all_batches = @student.all_batches
    @graph = open_flash_chart_object(700, 350,
      "/exam/graph_for_previous_years_marks_overview?student=#{params[:student]}&graphtype=#{params[:graphtype]}")
    respond_to do |format|
      format.pdf { render :layout => false }
      format.html
    end

  end
  
  def previous_years_marks_overview_pdf
    @student = Student.find(params[:student])
    @all_batches = @student.all_batches
    render :pdf => 'previous_years_marks_overview_pdf',
      :orientation => 'Landscape'
    
    
  end

  def academic_report
    #academic-archived-report
    @student = Student.find(params[:student])
    @batch = Batch.find(params[:year])
    if params[:type] == 'grouped'
      @grouped_exams = GroupedExam.find_all_by_batch_id(@batch.id)
      @exam_groups = []
      @grouped_exams.each do |x|
        @exam_groups.push ExamGroup.active.find(x.exam_group_id)
      end
    else
      @exam_groups = ExamGroup.active.find_all_by_batch_id(@batch.id)
    end
    general_subjects = Subject.find_all_by_batch_id(@batch.id, :conditions=>"elective_group_id IS NULL and is_deleted=false and no_exams=false")
    student_electives = StudentsSubject.find_all_by_student_id(@student.id,:conditions=>"batch_id = #{@batch.id}")
    elective_subjects = []
    student_electives.each do |elect|
      subject = Subject.find_by_id(elect.subject_id, :conditions=>"is_deleted=0")
      unless subject.blank?
        elective_subjects.push subject
      end
    end
    @subjects = general_subjects + elective_subjects
    @subjects.reject!{|s| (s.no_exams==true or s.exam_not_created(@exam_groups.collect(&:id)))}
  end

  def previous_batch_exams

  end

  def list_inactive_batches
    unless params[:course_id]==""
      @batches = Batch.find(:all, :conditions=>{:course_id=>params[:course_id],:is_active=>false,:is_deleted=>false})
      render(:update) do|page|
        page.replace_html "inactive_batches", :partial=>"inactive_batches"
      end
    else
      render(:update) do|page|
        page.replace_html "inactive_batches", :text=>""
      end
    end
  end

  def list_inactive_exam_groups
    unless params[:batch_id]==""
      @exam_groups = ExamGroup.active.find(:all, :conditions=>{:batch_id=>params[:batch_id]})
      @exam_groups.reject!{|e| !GroupedExam.exists?(:exam_group_id=>e.id,:batch_id=>params[:batch_id])}
      render(:update) do|page|
        page.replace_html "inactive_exam_groups", :partial=>"inactive_exam_groups"
      end
    else
      render(:update) do|page|
        page.replace_html "inactive_exam_groups", :text=>""
      end
    end
  end

  def previous_exam_marks
    #abort params.inspect
    unless params[:exam_goup_id]==""
      @exam_group = ExamGroup.active.find(params[:exam_group_id], :include => :exams)
      render(:update) do|page|
        page.replace_html "previous_exam_marks", :partial=>"previous_exam_marks"
      end
    else
      render(:update) do|page|
        page.replace_html "previous_exam_marks", :text=>""
      end
    end
  end

  def edit_previous_marks
    @employee_subjects=[]
    @employee_subjects= @current_user.employee_record.subjects.map { |n| n.id} if @current_user.employee?
    @exam = Exam.find params[:exam_id], :include => :exam_group
    @exam_group = @exam.exam_group
    @batch = @exam_group.batch
    #unless @employee_subjects.include?(@exam.subject_id) or @current_user.admin? or @current_user.privileges.map{|p| p.name}.include?('ExaminationControl') or @current_user.privileges.map{|p| p.name}.include?('EnterResults')
    #  flash[:notice] = "#{t('flash_msg6')}"
    #  redirect_to :controller=>"user", :action=>"dashboard"
    #end
    #scores = ExamScore.find_all_by_exam_id(@exam.id)
    exam_subject = Subject.find(@exam.subject_id)
    is_elective = exam_subject.elective_group_id
    if is_elective == nil
      if MultiSchool.current_school.id == 319
        @students = @batch.students.by_first_name
      else
        @students = @batch.students.by_roll_number_name
      end
    else
      assigned_students = StudentsSubject.find_all_by_subject_id_and_batch_id(exam_subject.id,exam_subject.batch_id)
      @students = []
      assigned_students.each do |s|
        student = Student.find_by_id(s.student_id)
        if MultiSchool.current_school.id == 319
          @students.push [student.first_name,student.last_name, student.id, student] unless student.nil?
        else
          @students.push [student.class_roll_no.to_i,student.first_name, student.id, student] unless student.nil? 
        end
        #        @students.push [student.class_roll_no,student.first_name, student.id, student] unless student.nil?
      end
      
      @ordered_students = @students.sort
      @students=[]
      @ordered_students.each do|s|
        @students.push s[3]
      end
    end
    
    @config = Configuration.get_config_value('ExamResultType') || 'Marks'

    @grades = @batch.grading_level_list
  end

  def update_previous_marks
    @exam = Exam.find(params[:exam_id])
    @error= false
    params[:exam].each_pair do |student_id, details|
      exam_score = ExamScore.find(:first, :conditions => {:exam_id => @exam.id, :student_id => student_id} )
      prev_score = ExamScore.find(:first, :conditions => {:exam_id => @exam.id, :student_id => student_id} )
      unless exam_score.nil?
        #unless details[:marks].to_f == exam_score.marks.to_f
        if details[:marks].to_f <= @exam.maximum_marks.to_f
          if exam_score.update_attributes(details)
            if params[:student_ids] and params[:student_ids].include?(student_id)
              PreviousExamScore.create(:student_id=>prev_score.student_id,:exam_id=>prev_score.exam_id,:marks=>prev_score.marks,:grading_level_id=>prev_score.grading_level_id,:remarks=>prev_score.remarks,:is_failed=>prev_score.is_failed)
            end
          else
            flash[:warn_notice] = "#{t('flash8')}"
            @error = nil
          end
        else
          @error = true
        end
        #end
      else
        if details[:marks].to_f <= @exam.maximum_marks.to_f
          ExamScore.create do |score|
            score.exam_id          = @exam.id
            score.student_id       = student_id
            score.user_id          = current_user.id
            score.marks            = details[:marks]
            score.grading_level_id = details[:grading_level_id]
            score.remarks          = details[:remarks]
          end
        else
          @error = true
        end
      end
    end
    flash[:notice] = "#{t('flash6')}" if @error == true
    flash[:notice] = "#{t('flash7')}" if @error == false
    redirect_to :controller=>"exam", :action=>"edit_previous_marks", :exam_id=>@exam.id
  end

  def redirect_exam
    @course = Course.new
    @course_name = params[:course_name]
    @batches = @course.find_batches_data(nil, @course_name)
    @batch_id = @batches[0]
    respond_to do |format|
      format.js { render :action => 'redirect_exam' }
    end
  end
  
  def create_exam
    @show_class_exam = true
    unless params[:show_class].nil?
      @show_class_exam = false
    end
    @batches = Batch.active.find(:all, :group => "name")
    if @batches.length == 1
      @for_exam = true
      @batch = @batches[0]
      batch_name = @batch.name
      school_id = MultiSchool.current_school.id
      Rails.cache.delete("course_data_#{batch_name.parameterize("_")}_#{school_id}")
      @courses = Rails.cache.fetch("course_data_#{batch_name.parameterize("_")}_#{school_id}"){
        @batches_data = Batch.find(:all, :conditions => ["name = ?", batch_name], :select => "course_id")
        @batch_ids = @batches_data.map{|b| b.course_id}
        @tmp_courses = Course.find(:all, :conditions => ["courses.id IN (?) and courses.is_deleted = 0 and batches.is_deleted = 0 and batches.name = ?", @batch_ids, batch_name], :select => "courses.*,  GROUP_CONCAT(courses.section_name,'--',courses.id,'--',batches.id) as courses_batches", :joins=> "INNER JOIN `batches` ON batches.course_id = courses.id", :group => 'course_name', :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
        @tmp_courses
      }
    end
    privilege = current_user.privileges.map{|p| p.name}
    if current_user.admin or privilege.include?("ExaminationControl") or privilege.include?("EnterResults")
      @course= Course.find(:all,:conditions => { :is_deleted => false }, :order => 'course_name asc')
    elsif current_user.employee
      @course= current_user.employee_record.subjects.all(:group => 'batch_id').map{|x|x.batch.course}.uniq.sort_by{|c| c.course_name}
    end
  end

  def update_batch_ex_result
    @batch = Batch.find_all_by_course_id(params[:course_name], :conditions => { :is_deleted => false, :is_active => true })

    render(:update) do |page|
      page.replace_html 'update_batch', :partial=>'update_batch_ex_result'
    end
  end

  def update_batch
    @batch = Batch.find_all_by_course_id(params[:course_name], :conditions => { :is_deleted => false, :is_active => true })

    render(:update) do |page|
      page.replace_html 'update_batch', :partial=>'update_batch'
    end

  end

  
  #GRAPHS

  def graph_for_generated_report
    student = Student.find(params[:student])
    examgroup = ExamGroup.active.find(params[:examgroup])
    if params[:batch].nil?
      batch = student.batch
    else
      batch = Batch.find(params[:batch])
    end
    general_subjects = Subject.find_all_by_batch_id(batch.id, :conditions=>"elective_group_id IS NULL AND no_exams = 0")
    student_electives = StudentsSubject.find_all_by_student_id(student.id,:conditions=>"batch_id = #{batch.id}")
    elective_subjects = []
    student_electives.each do |elect|
      elective_subjects.push Subject.find(elect.subject_id)
    end
    subjects = general_subjects + elective_subjects

    x_labels = []
    data = []
    data2 = []

    subjects.each do |s|
      exam = Exam.find_by_exam_group_id_and_subject_id(examgroup.id,s.id)
      res = ExamScore.find_by_exam_id_and_student_id(exam, student)
      unless res.nil?
        maximum_mark= res.exam.maximum_marks
        res_percentage=res.marks.present?? (res.marks/maximum_mark)*100 : 0
        unless res.nil?
          x_labels << truncate(s.name, :length => 8, :omission => '...')
          data << res_percentage
          data2 << exam.class_average_marks
        end
      end
    end

    bargraph = BarFilled.new()
    bargraph.width = 1;
    bargraph.colour = '#bb0000';
    bargraph.dot_size = 5;
    bargraph.text = "#{t('students_marks')}"
    bargraph.values = data

    bargraph2 = BarFilled.new
    bargraph2.width = 1;
    bargraph2.colour = '#5E4725';
    bargraph2.dot_size = 5;
    bargraph2.text = "#{t('class_average')}"
    bargraph2.values = data2

    x_axis = XAxis.new
    x_axis.labels = x_labels
    x_axis.set_body_style("max-width: 30px; float: left; text-align: justify;")
    x_axis.set_title_style("max-width: 30px; float: left; text-align: justify;")

    y_axis = YAxis.new
    y_axis.set_range(0,100,20)

    title = Title.new(student.full_name)

    x_legend = XLegend.new("#{t('subjects_text')}")
    x_legend.set_style('{font-size: 14px; color: #778877}')

    y_legend = YLegend.new("#{t('marks')+" (%)"}")
    y_legend.set_style('{font-size: 14px; color: #770077}')

    chart = OpenFlashChart.new
    chart.set_title(title)
    chart.y_axis = y_axis
    chart.x_axis = x_axis
    chart.y_legend = y_legend
    chart.x_legend = x_legend

    chart.add_element(bargraph)
    chart.add_element(bargraph2)

    render :text => chart.render
  end

  def graph_for_generated_report3
    
    student = Student.find params[:student]
    subject = Subject.find params[:subject]
    exams = Exam.find_all_by_subject_id(subject.id, :order => 'start_time asc')
    exams.reject!{|e| e.exam_group.result_published==false}
    
    data = []
    data2 = []
    data3 = []
    x_labels = []
    exams.each do |e|
      exam_result = ExamScore.find_by_exam_id_and_student_id(e, student.id)
      unless exam_result.nil?
        exam_score_high = ExamScore.find_by_exam_id(e,:limit=>1, :order => 'marks desc')
        exam_avg_high = ExamScore.find_by_exam_id(e,:limit=>1,:select=>'AVG(marks) as marks')
        data << exam_result.marks
        data3 << exam_score_high.marks
        data2 << exam_avg_high.marks
        x_labels << XAxisLabel.new(exam_result.exam.exam_group.name, '#000000', 10, 0)
      end
    end
      
    
    bargraph = BarFilled.new()
    bargraph.width = 1;
    bargraph.colour = '#bb0000';
    bargraph.dot_size = 5;
    bargraph.text = "Your Mark"
    bargraph.values = data

    bargraph2 = BarFilled.new
    bargraph2.width = 1;
    bargraph2.colour = '#5E4725';
    bargraph2.dot_size = 5;
    bargraph2.text = "Average Mark"
    bargraph2.values = data2
    
    
    bargraph3 = BarFilled.new
    bargraph3.width = 1;
    bargraph3.colour = '#639F45';
    bargraph3.dot_size = 5;
    bargraph3.text = "Highest Mark"
    bargraph3.values = data3

    x_axis = XAxis.new
    x_axis.labels = x_labels
    x_axis.set_body_style("max-width: 30px; float: left; text-align: justify;")
    x_axis.set_title_style("max-width: 30px; float: left; text-align: justify;")

    y_axis = YAxis.new
    y_axis.set_range(0,100,20)

    title = Title.new(subject.name)

    x_legend = XLegend.new("#{t('examination_Name')}")
    x_legend.set_style('{font-size: 14px; color: #778877}')

    y_legend = YLegend.new("#{t('marks')}")
    y_legend.set_style('{font-size: 14px; color: #770077}')

    chart = OpenFlashChart.new
    chart.set_title(title)
    chart.y_axis = y_axis
    chart.x_axis = x_axis
    chart.y_legend = y_legend
    chart.x_legend = x_legend

    chart.add_element(bargraph)
    chart.add_element(bargraph2)
    chart.add_element(bargraph3)

    render :text => chart.render
    
    #      bar1 = Bar.new(50, '#0066CC')
    #      bar1.key('Me', 10)
    #
    #      bar2 = Bar.new(50, '#9933CC')
    #      bar2.key('You', 10)
    #
    #      bar3 = Bar.new(50, '#639F45')
    #      bar3.key('Them', 10)
    #
    #      10.times do |t|
    #              bar1.data << rand(7) + 3
    #              bar2.data << rand(7) + 3
    #              bar3.data << rand(7) + 3
    #      end
    #
    #      g = Graph.new
    #      g.title("Bar Graph", "{font-size: 26px;}")
    #
    #      g.data_sets << bar1
    #      g.data_sets << bar2
    #      g.data_sets << bar3
    #
    #      g.set_x_labels(%w(Jan Feb Mar Apr May Jun Jul Aug Sep Oct))
    #      g.set_x_label_style(10, '#9933CC', 0, 2)
    #      g.set_x_axis_steps(2)
    #      g.set_y_max(10)
    #      g.set_y_label_steps(2)
    #      g.set_y_legend("Open Flash Chart", 12, "0x736AFF")
    #      render :text => g.render
    #    student = Student.find params[:student]
    #    subject = Subject.find params[:subject]
    #    exams = Exam.find_all_by_subject_id(subject.id, :order => 'start_time asc')
    #    exams.reject!{|e| e.exam_group.result_published==false}
    #    
    #    bar1 = BarFilled.new
    #    bar1.colour = '#0066CC';
    #    bar1.width = 50;
    #    bar1.text = "Your Mark"
    #    
    #    bar2 = BarFilled.new
    #    bar2.colour = '#9933CC';
    #    bar2.width = 50;
    #    bar2.text = "Highest"
    #    
    #    bar3 = BarFilled.new
    #    bar3.colour = '#639F45';
    #    bar3.width = 50;
    #    bar3.text = "Average"
    #
    #    data = []
    #    x_labels = []
    #
    #    exams.each do |e|
    #      exam_result = ExamScore.find_by_exam_id_and_student_id(e, student.id)
    #      unless exam_result.nil?
    #        exam_score_high = ExamScore.find_by_exam_id(e,:limit=>1, :order => 'marks desc')
    #        exam_avg_high = ExamScore.find_by_exam_id(e,:limit=>1,:select=>'AVG(marks) as marks')
    #        bar1.values << exam_result.marks
    #        bar2.values << exam_score_high.marks
    #        bar3.values << exam_avg_high.marks
    #        x_labels << XAxisLabel.new(exam_result.exam.exam_group.name, '#000000', 10, 0)
    #      end
    #    end
    #    
    #    
    #    
    #
    #    g = Graph.new
    #    g.title(subject.name, "{font-size: 26px;}")
    #
    #    g.data_sets << bar1
    #    g.data_sets << bar2
    #    g.data_sets << bar3
    #    
    #    x_legend = XLegend.new("#{t('examination_Name')}")
    #    x_legend.set_style('{font-size: 14px; color: #778877}')
    #
    #    y_legend = YLegend.new("#{t('marks')}")
    #    y_legend.set_style('{font-size: 14px; color: #770077}')
    #    
    #    g.set_x_legend(x_legend)
    #    g.set_y_legend(y_legend)
    #
    #    g.set_x_labels(x_labels)
    #    g.set_x_label_style(10, '#9933CC', 0, 2)
    #    g.set_x_axis_steps(2)
    #    g.set_y_max(100)
    #    g.set_y_label_steps(2)
    #    g.set_y_legend("Open Flash Chart", 12, "0x736AFF")
    #    render :text => g.render
    
    
    

    #    x_axis = XAxis.new
    #    x_axis.labels = x_labels
    #
    #    line = BarFilled.new
    #
    #    line.width = 1
    #    line.colour = '#5E4725'
    #    line.dot_size = 5
    #    line.values = data
    #
    #    y = YAxis.new
    #    y.set_range(0,100,20)
    #
    #    title = Title.new(subject.name)
    #
    #    x_legend = XLegend.new("#{t('examination_Name')}")
    #    x_legend.set_style('{font-size: 14px; color: #778877}')
    #
    #    y_legend = YLegend.new("#{t('marks')}")
    #    y_legend.set_style('{font-size: 14px; color: #770077}')
    #
    #    chart = OpenFlashChart.new
    #    chart.set_title(title)
    #    chart.set_x_legend(x_legend)
    #    chart.set_y_legend(y_legend)
    #    chart.y_axis = y
    #    chart.x_axis = x_axis
    #
    #    chart.add_element(line)

    #    render :text => chart.to_s
  end
  
  def graph_for_generated_report_all_subject
    
    
    student = Student.find(params[:student])
    exam_type = 0
    if params[:exam_type]!=0
      exam_type = params[:exam_type].to_i
    end
    
    if exam_type == 0
      title = Title.new("All Subject Progress")
    elsif exam_type == 1
      title = Title.new("All Subject Class Test Progress")
    elsif exam_type == 2
      title = Title.new("All Subject Projects Progress")
    elsif exam_type == 1
      title = Title.new("All Subject Term Test Progress")
    end  
    
    
    
    
    batch = student.batch
    general_subjects = Subject.find_all_by_batch_id(batch.id, :conditions=>"no_exams = 0 and elective_group_id IS NULL")
    student_electives = StudentsSubject.find_all_by_student_id(student.id,:conditions=>"batch_id = #{batch.id}")
    elective_subjects = []
    student_electives.each do |elect|
      elective_subjects.push Subject.find(elect.subject_id)
    end
    subjects = general_subjects + elective_subjects
    
    

    chart =OpenFlashChart.new
    
   
    color_array = ['#000000','#FF0000','#00FF00','#0000FF','#BF8277','#FF00FF','#00FFFF','#5954D8','#84C1A3','#AAA5BF','#D3CE87','#DDBA87','#CE5E60','#829E8C','#876656','#CE5E60','#7F7F9B','#AD998C']
   
    k = 0
    x_labels = []
    subjects.each do |subject|
      exams = Exam.find_all_by_subject_id(subject.id, :order => 'start_time asc')
      if exam_type != 0
        exams.reject!{|e| e.exam_group.result_published==false || e.exam_group.exam_category!=exam_type}
      else
        exams.reject!{|e| e.exam_group.result_published==false}
      end  
      data = []
      
      t = 1
      c = 1
      p = 1
      exams.each do |exam|
        res = ExamScore.find_by_exam_id_and_student_id(exam, student)
        unless res.nil?
          maximum_mark= res.exam.maximum_marks
          res_percentage=res.marks.present?? (res.marks/maximum_mark)*100 : 0
          data << res_percentage
        else
          maximum_mark= exam.maximum_marks
          res_percentage = 0
          data << res_percentage
        end
          
        if exam.exam_group.exam_category==1
          exam_name = "C"
          full_exam_name = exam_name+" "+c.to_s
          c = c+1
        end
          
        if exam.exam_group.exam_category==2
          exam_name = "P"
          full_exam_name = exam_name+" "+p.to_s
          p = p+1
        end

        if exam.exam_group.exam_category==3
          exam_name = "T"
          full_exam_name = exam_name+" "+t.to_s
          t = t+1
        end
          
          
        unless x_labels.include? full_exam_name
          x_labels << full_exam_name
        end
          
      end
      
      unless data.empty?
        colour = "%006x" % (rand * 0xffffff)
        line_dot = LineHollow.new
        line_dot.text = subject.name
        line_dot.width = 3
        line_dot.colour = color_array[k]
        line_dot.dot_size = 4
        line_dot.values = data
        chart.add_element(line_dot)
        k = k+1  
      end  
    end
    y = YAxis.new
    y.set_range(0,100,10)
    
    x_axis = XAxis.new
    x_axis.labels = x_labels

    x_legend = XLegend.new("Exams")
    x_legend.set_style('{font-size: 20px; color: #778877}')

    y_legend = YLegend.new("Marks (%)")
    y_legend.set_style('{font-size: 20px; color: #770077}')
    
    chart.set_title(title)
    chart.set_x_legend(x_legend)
    chart.set_y_legend(y_legend)
    chart.y_axis = y
    chart.x_axis = x_axis

    render :text => chart.to_s
  end

  def graph_for_previous_years_marks_overview
    student = Student.find(params[:student])

    x_labels = []
    data = []

    student.all_batches.each do |b|
      x_labels << b.name
      exam = ExamScore.new()
      data << exam.batch_wise_aggregate(student,b)
    end

    if params[:graphtype] == 'Line'
      line = Line.new
    else
      line = BarFilled.new
    end

    line.width = 1; line.colour = '#5E4725'; line.dot_size = 5; line.values = data

    x_axis = XAxis.new
    x_axis.labels = x_labels

    y_axis = YAxis.new
    y_axis.set_range(0,100,20)

    title = Title.new(student.full_name)

    x_legend = XLegend.new("#{t('academic_year')}")
    x_legend.set_style('{font-size: 14px; color: #778877}')

    y_legend = YLegend.new("#{t('total_marks')}")
    y_legend.set_style('{font-size: 14px; color: #770077}')

    chart = OpenFlashChart.new
    chart.set_title(title)
    chart.y_axis = y_axis
    chart.x_axis = x_axis

    chart.add_element(line)

    render :text => chart.to_s
  end
  
  private
  
  def render_connect_exam(template,for_save=false,file_name="")
    
    if MultiSchool.current_school.id == 246
      render :pdf => template,
        :save_to_file => file_name,
        :save_only    => for_save,
        :orientation => 'Landscape',
        :margin => {:top=> 35,
        :bottom => 40,
        :left=> 10,
        :right => 10}
    elsif MultiSchool.current_school.id == 352 or MultiSchool.current_school.id == 346
      if @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 5 or @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 9 or @connect_exam_obj.result_type.to_i == 11
        render :pdf => template,
          :save_to_file => file_name,
          :save_only    => for_save,
          :orientation => 'Portrait',
          :margin => {    :top=> 10,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      else
        render :pdf => template,
          :save_to_file => file_name,
          :save_only    => for_save,
          :orientation => 'Landscape',
          :margin => {    :top=> 10,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
        
      end
      
      
    elsif MultiSchool.current_school.id == 348
      if @connect_exam_obj.result_type == 8 or @connect_exam_obj.result_type == 9 or @connect_exam_obj.result_type == 10
        render :pdf => template,
          :save_to_file => file_name,
          :save_only    => for_save,
          :orientation => 'Landscape',
          :encoding =>    'utf8',
          :margin => {    :top=> 0,
          :bottom => 0,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      else
        render :pdf => template,
          :save_to_file => file_name,
          :save_only    => for_save,
          :orientation => 'Portrait',
          
          :encoding =>    'utf8',
          :margin => {    :top=> 10,
          :bottom => 5,
          :left=> 20,
          :right => 20},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      end
    elsif MultiSchool.current_school.id == 319 or MultiSchool.current_school.id == 323 or MultiSchool.current_school.id == 325
      if MultiSchool.current_school.id == 319  and (@connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 5 or @connect_exam_obj.result_type == 7)
        render :pdf => template,
          :save_to_file => file_name,
          :save_only    => for_save,
          :orientation => 'Portrait',
  
          :margin => {    :top=> 10,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      else  
        render :pdf => template,
          :save_to_file => file_name,
          :save_only    => for_save,
          :orientation => 'Portrait'
      end
    elsif  MultiSchool.current_school.id == 312 or MultiSchool.current_school.id == 2 
      if @connect_exam_obj.result_type != 1 and @connect_exam_obj.result_type != 6 and @connect_exam_obj.result_type != 7
        render :pdf => template,
          :save_to_file => file_name,
          :save_only    => for_save,
          :orientation => 'Portrait',
          :margin => { :top=> 30,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      else
            
        render :pdf => template,
          :save_to_file => file_name,
          :save_only    => for_save,
          :orientation => 'Landscape',
          :margin => {  :top=> 30,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      end
    elsif  MultiSchool.current_school.id == 342 or MultiSchool.current_school.id == 324 or MultiSchool.current_school.id == 3
      render :pdf => template,
        :save_to_file => file_name,
        :save_only    => for_save,
        :orientation => 'Portrait',
        :margin => {:top=> 35,
        :bottom => 35,
        :left=> 10,
        :right => 10}
    elsif  MultiSchool.current_school.id == 340  
      if @connect_exam_obj.result_type == 14 or @connect_exam_obj.result_type == 13
        render :pdf => template,
          :save_to_file => file_name,
          :save_only    => for_save,
          :orientation => 'Landscape',
          :page_size=>'A5',
          :margin => {:top=> 5,
          :bottom =>0,
          :left=> 30,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}} 
      else
        render :pdf => template,
          :save_to_file => file_name,
          :save_only    => for_save,
          :orientation => 'Portrait',
          :margin => {    :top=> 10,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}  
      end
    else 
      render :pdf => template,
        :save_to_file => file_name,
        :save_only    => for_save,
        :orientation => 'Landscape',
        :margin => {:top=> 25,
        :bottom => 40,
        :left=> 10,
        :right => 10}
    end
    
  end
  
  
  def get_exam_report(connect_exam_id,student_id,batch_id)
    require 'net/http'
    require 'uri'
    require "yaml"
    

    
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    api_uri = URI(api_endpoint + "api/report/groupedexamreport")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })


    request.set_form_data({"connect_exam_id"=>connect_exam_id,"student_id"=>student_id,"batch_id"=>batch_id,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})


    response = http.request(request)
    @student_response = JSON::parse(response.body)

  end
  def get_continues(connect_exam_id,batch_id)
    require 'net/http'
    require 'uri'
    require "yaml"
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    api_uri = URI(api_endpoint + "api/report/continues")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"connect_exam_id"=>connect_exam_id,"batch_id"=>batch_id,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
    response = http.request(request)
    @student_response = JSON::parse(response.body)

  end
  def get_tabulation(connect_exam_id,batch_id)
    require 'net/http'
    require 'uri'
    require "yaml"
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    api_uri = URI(api_endpoint + "api/report/tabulation")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"connect_exam_id"=>connect_exam_id,"batch_id"=>batch_id,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
    response = http.request(request)
    @student_response = JSON::parse(response.body)

  end
  def finding_data
    @grading_levels = GradingLevel.for_batch(@batch.id)
    if @grading_levels.blank?
      @grading_levels = GradingLevel.default
    end
    @student_list = []
    @subject_highest = {}
    @student_avg_mark = {}
    unless @tabulation_data.blank?
      connect_exam = 0
      batch_loop = 0
      @tabulation_data['report'].each do |tab|
        batch_subject = Subject.find_all_by_batch_id(@tabulation_data['batches'][batch_loop], :conditions=>"elective_group_id IS NULL and is_deleted=false")
        batch_subject_id = batch_subject.map(&:id)
        batch_loop = batch_loop+1
        connect_exam_id = @tabulation_data['connect_exams'][connect_exam]
        connect_exam = connect_exam+1
        
        tab['students'].each do |std| 
          subject_count_std = 0
          total_std_subject = StudentsSubject.find_all_by_student_id(std['id'].to_i)
          std_subject_id = total_std_subject.map(&:subject_id)
          std_marks_full = 0
          std_marks_core_subject = 0
          total_subject = 0
          u_grade = 0
          tab['subjects'].each do |sub|
            if batch_subject_id.include?(sub['id'].to_i) or std_subject_id.include?(sub['id'].to_i)
              
              subject_count_std = subject_count_std+1
              std_subject_marks_final = 0
              i = 0
              tab['exams'].each do |rs|
                if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                  i = i+1
                  if i<3
                    std_subject_marks_final = std_subject_marks_final+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                  else
                    break
                  end  
                end    
              end
              
              
              std_subject_marks_mid = 0
              i = 0
              tab['exams'].each do |rs|
                if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                  i = i+1
                  if i>2
                    std_subject_marks_mid = std_subject_marks_mid+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                  end
                end    
              end
              
              #unless @final_term_student_list.include?(std['id'].to_i)
              subject_full_marks = ((std_subject_marks_final.to_f*70)/100)+((std_subject_marks_mid.to_f*30)/100)
              #else
              #subject_full_marks = std_subject_marks_final.to_f
              #end
              
              std_marks_full = std_marks_full+subject_full_marks.round()
              
              
              subject_grade = ""
              grade = GradingLevel.percentage_to_grade(subject_full_marks, @batch.id)
              if !grade.blank? and !grade.name.blank?
                subject_grade = grade.name
              end   
              
              
              
              
              if subject_grade == "U"
                u_grade = u_grade+1
              end  
              
              if @subject_highest[sub['id'].to_i].blank?
                @subject_highest[sub['id'].to_i] = subject_full_marks
              elsif subject_full_marks.to_f > @subject_highest[sub['id'].to_i].to_f
                @subject_highest[sub['id'].to_i] = subject_full_marks.to_f
              end
              
              
              
              if subject_count_std<4
                std_marks_core_subject = std_marks_core_subject+subject_full_marks.round()
              end 
            end  
          end
          
          @exam_comment = ExamConnectComment.find_by_exam_connect_id_and_student_id(connect_exam_id,std['id'].to_i)
          result = ""
          promotion_status = ""
          merit_position = ""
          new_roll = ""
          new_section = ""
          if !@exam_comment.blank?
            all_comments = @exam_comment.comments
            if !all_comments.blank?
              all_comments_array = all_comments.split("|")
              result = all_comments_array[0]
              if !all_comments_array[1].nil?
                promotion_status = all_comments_array[1]
                if !all_comments_array[2].nil?
                  merit_position = all_comments_array[2]
                  if !all_comments_array[3].nil?
                    new_roll = all_comments_array[3]
                    if !all_comments_array[4].nil?
                      new_section = all_comments_array[4]
                    end
                  end
                  
                end
                
              end
            end
          end
          
          
          
          if subject_count_std>0 and (u_grade==0 or (@batch.course.course_name!="VIII" and @batch.course.course_name!="IX" and @batch.course.course_name!="X" and  u_grade<2))           
            
            std_marks_full_new = std_marks_full.to_f/subject_count_std.to_f
            std_marks_full_new = 5000.00-std_marks_full_new.to_f
            std_marks_core_subject = 5000-std_marks_core_subject.round()
            subject_count_std = 5000-subject_count_std
            @student_avg_mark[std['id'].to_i] = u_grade
            @student_list << [std_marks_full_new.to_f,std_marks_core_subject,subject_count_std,std['first_name']+" "+std['last_name'],std['id'].to_i]
          else
            @student_avg_mark[std['id'].to_i] = u_grade
          end  
          
        end
      end
    end
    
    @student_position = {}
    @student_section = {}
    @student_roll = {}
    
    @sections = ["A","B","C"]
    student_classes = ["KG","I","II","III","IV","V","VI","VII","VIII","IX","X"]
    @promoted_to = "";
    if student_classes.include?(@batch.course.course_name)
      array_index = student_classes.index @batch.course.course_name
      if array_index < student_classes.count
        @promoted_to = student_classes[array_index+1]
        if @promoted_to == "I"
          @sections = ["Camellia","Marigold","Cosmos"]
        end
        if @promoted_to == "II"
          @sections = ["Rose","Sunflower","Gardenia"]
        end
        if @promoted_to == "III"
          @sections = ["Orchid","Tulip","Jasmine"]
        end
        if @promoted_to == "IV"
          @sections = ["Daisy","Daffodil"]
        end
        if @promoted_to == "V"
          @sections = ["Bluebell","Lavender"]
        end
        if @promoted_to == "VI"
          @sections = ["Primrose","Snowdrop"]
        end
        if @promoted_to == "VII"
          @sections = ["Parrot","Swift"]
        end
        if @promoted_to == "VIII"
          @sections = ["Kingfisher","Nightingale"]
        end
        if @promoted_to == "IX"
          @sections = ["Robin","Seagull"]
        end
        if @promoted_to == "X"
          @sections = ["Penguin","Pelican"]
        end
        
        
      else
        @promoted_to = "0"
      end
    end  
    
    unless @student_list.blank?
      @sorted_students = @student_list.sort
      position = 0
      @sorted_students.each do|s|
        position = position+1
        @student_position[s[4].to_i] = position
      end
      
      @iloop = 0
      @jloop = 0
      
      @sloop = 0
      
      @s_a_loop = 1
      @s_b_loop = 1
      @s_c_loop = 1
      @new_sections = @sections
      while @iloop < @sorted_students.count do
        if @jloop > (@new_sections.count-1)
          @jloop = 0
          @new_sections = @new_sections.reverse
        end
        @iloop +=1
        
        if @sorted_students[@sloop][4].to_i == 9998344 || @sorted_students[@sloop][4].to_i == 99911700 || @sorted_students[@sloop][4].to_i == 9998058 || @sorted_students[@sloop][4].to_i == 9998051
          @student_section[@iloop] = @sections[0]
          @student_roll[@iloop] = @s_a_loop
          @s_a_loop +=1
        else
          @student_section[@iloop] = @new_sections[@jloop]
          if @new_sections[@jloop] == @sections[0]
            @student_roll[@iloop] = @s_a_loop
            @s_a_loop +=1
          end
          
          if @new_sections[@jloop] == @sections[1]
            @student_roll[@iloop] = @s_b_loop
            @s_b_loop +=1
          end
          
          if @sections.count>2
            if @new_sections[@jloop] == @sections[2]
              @student_roll[@iloop] = @s_c_loop
              @s_c_loop +=1
            end
          end
        end
        
        @jloop +=1
        @sloop +=1
      end   
    end
  end
  
  def get_subject_mark_sheet(connect_exam_id,subject_id)
    require 'net/http'
    require 'uri'
    require "yaml"
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    api_uri = URI(api_endpoint + "api/report/groupexamsubject")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"id"=>connect_exam_id,"subject_id"=>subject_id,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
    response = http.request(request)
    @student_response = JSON::parse(response.body)

  end
  def get_subject_mark_sheet_all(connect_exam_id,subject_id)
    require 'net/http'
    require 'uri'
    require "yaml"
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    api_uri = URI(api_endpoint + "api/report/groupexamsubject")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"id"=>connect_exam_id,"subject_id"=>subject_id,"all_section"=>"1","call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
    response = http.request(request)
    @student_response = JSON::parse(response.body)

  end
  
  def finding_data5
    if @total_std.blank?
      if @grading_levels.blank?
        @grading_levels = GradingLevel.for_batch(@batch.id)
        if @grading_levels.blank?
          @grading_levels = GradingLevel.default
        end
      end
      @total_std_batch = 0
      @total_std = 0
      @student_list_first_term = []
      @student_list_second_term = []
      @student_list = []
      @student_list_batch = []
      @student_list_first_term_batch = []
      @student_list_second_term_batch = []
      @student_subject_marks = {}
      @subject_highest = {}
      @subject_highest_1st_term = {}
      @subject_highest_2nd_term = {}
      @student_avg_mark = {}
      @student_result = []
      @subject_result = {}
      @subject_code = {}
      @absent_in_all_subject = 0
      @section_wise_position = {}	
      @failed_partial_absent = {}	
      @failed_appeared_absent = {}	
      @grade_count = {}
      loop_std = 0
      batchobj = Batch.find_by_id(@batch.id) 
      courseObj = Course.find_by_id(batchobj.course_id)
      all_courses = Course.find_all_by_course_name(courseObj.course_name)
      all_batch = Batch.find_all_by_course_id(all_courses.map(&:id))
      std_subject = StudentsSubject.find_all_by_batch_id(all_batch.map(&:id),:include=>[:subject])
      @std_subject_hash_type = []
      @std_subject_hash_code = []
      unless std_subject.blank?
        std_subject.each do |std_sub|
          @std_subject_hash_type << std_sub.student_id.to_s+"|||"+std_sub.subject_id.to_s+"|||"+std_sub.elective_type.to_s
          @std_subject_hash_code << std_sub.student_id.to_s+"|||"+std_sub.subject.code
        end
      end
      @batch_subject_hash_code = []
      batch_subject = Subject.find_all_by_batch_id(@batch.id, :conditions=>"elective_group_id IS NULL and is_deleted=false")
      unless batch_subject.blank?
        batch_subject.each do |std_sub|
          @batch_subject_hash_code << std_sub.code
        end
      end

      unless @tabulation_data.blank?
        connect_exam = 0
        batch_loop = 0
        group_name = courseObj.group
        #        @tabulation_data['report'].each do |tab|
        #          connect_exam_id = @tabulation_data['connect_exams'][connect_exam]
        #          connect_exam = connect_exam+1
        #          if connect_exam_id.to_i == @connect_exam_obj.id
        #            tab['students'].each do |std| 
        #              if @std_subject_hash_code.include?(std['id'].to_s+"|||Bio") or @batch_subject_hash_code.include?("Phy.") or @batch_subject_hash_code.include?("Chem.")
        #                group_name = "Science" 
        #                break
        #              elsif @std_subject_hash_code.include?(std['id'].to_s+"|||F&B") or @batch_subject_hash_code.include?("Acc..") or @batch_subject_hash_code.include?("BOM..") or @batch_subject_hash_code.include?("PMM..") or @batch_subject_hash_code.include?("Acc.") or @batch_subject_hash_code.include?("BOM.") or @batch_subject_hash_code.include?("PMM.") or @batch_subject_hash_code.include?("Acc..") or @batch_subject_hash_code.include?("BOM..") or @batch_subject_hash_code.include?("PMM..")
        #                group_name = "Business Studies" 
        #                break
        #              elsif @std_subject_hash_code.include?(std['id'].to_s+"|||Civics") or @std_subject_hash_code.include?(std['id'].to_s+"|||Islam") or @std_subject_hash_code.include?(std['id'].to_s+"|||Geo")
        #                group_name = "Humanities" 
        #                break
        #              end
        #            end
        #          end
        #          if group_name != ""
        #            break 
        #          end
        #        end
        @group_name_upper = group_name	
     
        connect_exam = 0
        batch_loop = 0
        @tabulation_data['report'].each do |tab|
          batch_subject = Subject.find_all_by_batch_id(@tabulation_data['batches'][batch_loop], :conditions=>"elective_group_id IS NULL and is_deleted=false")
          batch_subject_id = batch_subject.map(&:id)
          batch_subject_hash_code_main = []	
          unless batch_subject.blank?	
            batch_subject.each do |std_sub|	
              batch_subject_hash_code_main << std_sub.code	
            end	
          end
          batch_data = Batch.find(@tabulation_data['batches'][batch_loop])
          batch_loop = batch_loop+1
          connect_exam_id = @tabulation_data['connect_exams'][connect_exam]

          exam_type = 1
          connect_exam = connect_exam+1
          std_group_name = batch_data.course.group
        


          tab['students'].each do |std| 
            total_failed = 0	
            total_failed_appaered = 0
            full_absent = true
            failed_on_appread = false
            #            std_group_name = ""
            #            if @std_subject_hash_code.include?(std['id'].to_s+"|||Bio") or batch_subject_hash_code_main.include?("Phy.") or batch_subject_hash_code_main.include?("Chem.")
            #              std_group_name = "Science" 
            #            elsif @std_subject_hash_code.include?(std['id'].to_s+"|||F&B") or batch_subject_hash_code_main.include?("Acc..") or batch_subject_hash_code_main.include?("BOM..") or batch_subject_hash_code_main.include?("PMM..") or batch_subject_hash_code_main.include?("Acc.") or batch_subject_hash_code_main.include?("BOM.") or batch_subject_hash_code_main.include?("PMM.") or batch_subject_hash_code_main.include?("Acc..") or batch_subject_hash_code_main.include?("BOM..") or batch_subject_hash_code_main.include?("PMM..")
            #              std_group_name = "Business Studies" 
            #            elsif @std_subject_hash_code.include?(std['id'].to_s+"|||Eco") or @std_subject_hash_code.include?(std['id'].to_s+"|||Islam") or @std_subject_hash_code.include?(std['id'].to_s+"|||Geo")
            #              std_group_name = "Humanities" 
            #            end
            
            grand_total = 0
            grand_total_with_fraction = 0
            grand_grade_point = 0

            grand_total1 = 0
            grand_total1_with_fraction = 0
            grand_grade_point1 = 0

            grand_total2 = 0
            grand_total2_with_fraction = 0
            grand_grade_point2 = 0
            u_grade = 0
            u_grade1 = 0
            u_grade2 = 0

            grand_total_main = 0
            grade_poin_main = 0
            @student_tab = Student.find_by_id(std['id'].to_i)
            if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
              if @student_result[loop_std].blank?
                @student_result[loop_std] = {}
              end
              @student_result[loop_std]['id'] = std['id']
              @student_result[loop_std]['sl'] = loop_std+1
              @student_result[loop_std]['batch_id'] = batch_data.id	
              @student_result[loop_std]['batch_data'] = batch_data.full_name
              @student_result[loop_std]['sid'] = @student_tab.admission_no
              @student_result[loop_std]['roll'] = @student_tab.class_roll_no
              @student_result[loop_std]['name'] = @student_tab.full_name
              @student_result[loop_std]['subjects'] = {}

            end

            if std_group_name == group_name or connect_exam_id.to_i == @connect_exam_obj.id
              @total_std = @total_std+1
            end
            total_std_subject = StudentsSubject.find_all_by_student_id(std['id'].to_i)
            std_subject_id = total_std_subject.map(&:subject_id)
            total_subject = 0
            subject_array = []
            tab['subjects'].each do |sub|
              if subject_array.include?(sub['id'].to_i)
                next
              end
              subject_array << sub['id'].to_i
              if !@subject_code.blank? && !@subject_code[sub['code']].blank?	
                main_sub_id = @subject_code[sub['code']]
              else	
                @subject_code[sub['code'].to_s] = sub['id']	
                main_sub_id = sub['id']	
              end 	
              if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)	
                @student_result[loop_std]['subjects'][main_sub_id.to_s] = {}	
                @student_result[loop_std]['subjects'][main_sub_id.to_s]['name'] = sub['name']	
                @student_result[loop_std]['subjects'][main_sub_id.to_s]['id'] = main_sub_id	
                @student_result[loop_std]['subjects'][main_sub_id.to_s]['result'] = {}	
              end

              fourth_subject = false
              if !@std_subject_hash_type.blank?
                if @std_subject_hash_type.include?(std['id'].to_s+"|||"+sub['id'].to_s+"|||4")
                  fourth_subject = true
                end  
              end 



              has_exam = false
              tab['exams'].each do |rs|
                if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].blank?
                  has_exam = true
                  break
                end  
              end 
              if has_exam == false
                next
              end 

              if batch_subject_id.include?(sub['id'].to_i) or std_subject_id.include?(sub['id'].to_i)
                if fourth_subject == false && sub['subject_group_id'].to_i == 0 &&  sub['grade_subject'].to_i == 0
                  total_subject = total_subject+1
                end
                total_mark1 = 0
                full_mark1 = 0
                total_mark2 = 0
                full_mark2 = 0

                at_total_mark1 = 0
                at_total_mark2 = 0

                monthly_full_mark1 = 0
                monthly_full_mark2 = 0
                monthly_total_mark1 = 0
                monthly_total_mark2 = 0
                monthly_total_main_mark1 = 0
                monthly_total_main_mark2 = 0
                appeared = false
                
                appeared_ct = false
                appeared_sb = false
                appeared_ob = false
                appeared_pr = false
                
                

                full_sb1 = 0
                full_sb2 = 0
                total_sb1 = 0
                total_sb2 = 0

                full_ob1 = 0
                full_ob2 = 0
                total_ob1 = 0
                total_ob2 = 0

                full_pr1 = 0
                full_pr2 = 0
                total_pr1 = 0
                total_pr2 = 0
                main_mark = 0
                subject_failed = false

                tab['exams'].each do |rs|
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank? 
                    if rs['exam_category'] == '1'
                      if rs['quarter'] == '1'
                        monthly_full_mark1 = monthly_full_mark1+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                      end  
                      if rs['quarter'] == '2'
                        monthly_full_mark2 = monthly_full_mark2+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                      end
                    elsif rs['exam_category'] == '2'
                      if rs['quarter'] == '1'
                        full_mark1 = full_mark1+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                      end  
                      if rs['quarter'] == '2'
                        full_mark2 = full_mark2+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                      end
                    elsif rs['exam_category'] == '3' 

                      if rs['quarter'] == '1'
                        full_sb1 = full_sb1+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                        full_mark1 = full_mark1+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f

                      end  
                      if rs['quarter'] == '2'
                        full_sb2 = full_sb2+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                        full_mark2 = full_mark2+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f

                      end
                    elsif rs['exam_category'] == '4' 

                      if rs['quarter'] == '1'
                        full_ob1 = full_ob1+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                        full_mark1 = full_mark1+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f

                      end  
                      if rs['quarter'] == '2'
                        full_ob2 = full_ob2+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                        full_mark2 = full_mark2+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                      end
                    elsif rs['exam_category'] == '5' 
                      if rs['quarter'] == '1'
                        full_pr1 = full_pr1+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                        full_mark1 = full_mark1+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                      end  
                      if rs['quarter'] == '2'
                        full_pr2 = full_pr2+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                        full_mark2 = full_mark2+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                      end
                    end


                    if rs['exam_category'] == '1'
                      if rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_s != "AB"
                        appeared_ct = true
                      end
                      if rs['quarter'] == '1'
                        monthly_total_mark1 = monthly_total_mark1+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      end  
                      if rs['quarter'] == '2'
                        monthly_total_mark2 = monthly_total_mark2+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      end
                    elsif rs['exam_category'] == '2'
                      if rs['quarter'] == '1'
                        at_total_mark1 = at_total_mark1+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      end  
                      if rs['quarter'] == '2'
                        at_total_mark2 = at_total_mark2+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      end
                    elsif rs['exam_category'] == '3' 
                      if rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_s != "AB"
                        appeared = true
                        full_absent = false
                        appeared_sb = true
                      end
                      if rs['quarter'] == '1'
                        total_sb1 = total_sb1+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 8 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                          unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 8 or sub['grade_subject'].to_i == 1
                            u_grade1 = u_grade1+1
                            subject_failed = true
                          end
                        end  
                      end  
                      if rs['quarter'] == '2'
                        total_sb2 = total_sb2+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 8 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                          unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 8 or sub['grade_subject'].to_i == 1
                           u_grade2 = u_grade2+1
                           subject_failed = true
                          end
                        end 
                      end
                    elsif rs['exam_category'] == '4' 
                      if rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_s != "AB"
                        appeared = true
                        full_absent = false
                        appeared_ob = true
                      end
                      if rs['quarter'] == '1'
                        total_ob1 = total_ob1+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 8 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                          unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 8 or sub['grade_subject'].to_i == 1
                            u_grade1 = u_grade1+1
                            subject_failed = true
                          end
                        end 
                      end  
                      if rs['quarter'] == '2'
                        total_ob2 = total_ob2+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 8 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                          unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 8 or sub['grade_subject'].to_i == 1
                            u_grade2 = u_grade2+1
                            subject_failed = true
                          end
                        end 
                      end
                    elsif rs['exam_category'] == '5' 
                      if rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_s != "AB"
                        appeared = true
                        full_absent = false
                        appeared_pr = true
                      end
                      if rs['quarter'] == '1'
                        total_pr1 = total_pr1+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 8 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                          unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 8 or sub['grade_subject'].to_i == 1
                            u_grade1 = u_grade1+1
                            subject_failed = true
                          end
                        end 
                      end  
                      if rs['quarter'] == '2'
                        total_pr2 = total_pr2+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 8 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                          unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 8 or sub['grade_subject'].to_i == 1
                            u_grade2 = u_grade2+1
                            subject_failed = true
                          end
                        end 
                      end
                    end
                  end    
                end

                if full_mark1 > 0 && monthly_full_mark1 != 0
                  full_mark1 = full_mark1+20
                end
                if full_mark2 > 0 && monthly_full_mark2 != 0
                  full_mark2 = full_mark2+20
                end
                
                if full_mark1 > 0 && full_mark2 > 0
                  exam_type = 3
                elsif full_mark2 > 0
                  exam_type = 2
                end
                
               

                term_mark_multiplier = 0.75

                if @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4
                   term_mark_multiplier = 0.80
                end
                if @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 8
                   term_mark_multiplier = 0.90
                end
                
                

                total_mark2 = total_ob2+total_sb2+total_pr2
                total_mark2_80 = total_mark2.to_f
                if full_mark2 > 100 or term_mark_multiplier == 0.80 or term_mark_multiplier == 0.90
                  total_mark2_80 = total_mark2.to_f*term_mark_multiplier
                end  

                

                total_mark1 = total_ob1+total_sb1+total_pr1
                total_mark1_80 = total_mark1.to_f
                if full_mark1 > 100 or term_mark_multiplier == 0.80 or term_mark_multiplier == 0.90
                  total_mark1_80 = total_mark1.to_f*term_mark_multiplier
                end
                
                monthly_mark_multiply = 20
                if full_mark1 == 75
                  full_mark1 = 75
                elsif full_mark1 >=100
                  full_mark1 = 100
                else
                  full_mark1 = 50
                  monthly_mark_multiply = 10
                end
                monthly_mark_multiply2 = 20
                if full_mark2 == 75
                  full_mark2 = 75
                elsif full_mark2 >=100
                  full_mark2 = 100
                else
                  full_mark2 = 50
                  monthly_mark_multiply2 = 10
                end 
                
                if @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 8
                  monthly_mark_multiply = monthly_mark_multiply/2
                  monthly_mark_multiply2 = monthly_mark_multiply2/2
                end
                
                monthly_total_main_mark1 = monthly_total_mark1
                monthly_total_main_mark2 = monthly_total_mark2
                if @connect_exam_obj.result_type != 5 and @connect_exam_obj.result_type != 6
                  if monthly_total_mark1 > 0
                    monthly_total_mark1 = (monthly_total_mark1/monthly_full_mark1)*monthly_mark_multiply
                    monthly_total_mark1 = monthly_total_mark1.round()
                  end 
                  if monthly_total_mark2 > 0
                    monthly_total_mark2 = (monthly_total_mark2/monthly_full_mark2)*monthly_mark_multiply2
                    monthly_total_mark2 = monthly_total_mark2.round()
                  end
                end
                
                total_mark2 = total_mark2_80+monthly_total_mark2+at_total_mark2

                total_mark1 = total_mark1_80+monthly_total_mark1+at_total_mark1
                
                if @connect_exam_obj.result_type == 5 or @connect_exam_obj.result_type == 6
                   full_mark_sb1_converted = full_mark1-full_pr1-full_ob1-monthly_total_mark1
                   full_mark_sb2_converted = full_mark2-full_pr2-full_ob2-monthly_total_mark2
                   if total_sb1 > 0
                     total_sb1 = (total_sb1.to_f/full_sb1.to_f)*full_mark_sb1_converted.to_f
                   end
                   if total_sb2 > 0
                     total_sb2 = (total_sb2.to_f/full_sb2.to_f)*full_mark_sb2_converted.to_f
                   end
                   total_mark1 = total_ob1+total_sb1+total_pr1+monthly_total_mark1
                   total_mark2 = total_ob2+total_sb2+total_pr2+monthly_total_mark2
                end

                  

                total_mark2_no_round = total_mark2
                full_mark2 = full_mark2
                total_mark2 = total_mark2.round()

                total_mark1_no_round = total_mark1
                full_mark1 = full_mark1
                total_mark1 = total_mark1.round()


                if total_mark2.to_f>0 and full_mark2.to_f>0
                  main_mark2 = (total_mark2.to_f/full_mark2.to_f)*100
                 
                else
                  main_mark2 = 0
                end 
                if total_mark1.to_f>0 and full_mark1.to_f>0
                  main_mark1 = (total_mark1.to_f/full_mark1.to_f)*100
               
                else
                  main_mark1 = 0
                end 
                if exam_type == 3
                  main_mark = (total_mark1.to_f+total_mark2.to_f)/(full_mark1.to_f+full_mark2.to_f)*100
             
                  main_mark_no_round = (total_mark1_no_round.to_f+total_mark2_no_round.to_f)/(full_mark1.to_f+full_mark2.to_f)*100
                elsif  exam_type == 2
                  main_mark = total_mark2.to_f/full_mark2.to_f*100
                 
                  main_mark_no_round = total_mark2_no_round.to_f/full_mark2.to_f*100
                else
                  main_mark = total_mark1.to_f/full_mark1.to_f*100
                  
                  main_mark_no_round = total_mark1_no_round.to_f/full_mark1.to_f*100
                end  

                subject_full_marks = main_mark
                if sub['grade_subject'].to_i != 1
                  if @student_subject_marks[sub['id'].to_i].blank?
                    @student_subject_marks[sub['id'].to_i] = {}
                  end

                  @student_subject_marks[sub['id'].to_i][std['id'].to_i] = subject_full_marks


                  grand_total1 = grand_total1+total_mark1
                  grand_total2 = grand_total2+total_mark2
                  grand_total = grand_total+subject_full_marks

                  grand_total1_with_fraction = grand_total1_with_fraction+total_mark1_no_round
                  grand_total2_with_fraction = grand_total2_with_fraction+total_mark2_no_round
                  grand_total_with_fraction = grand_total_with_fraction+main_mark_no_round



                  if fourth_subject.blank? && subject_failed == false
                    grade = GradingLevel.percentage_to_grade(main_mark1, @batch.id)
                    if !grade.blank? and !grade.name.blank?
                      unless sub['subject_group_id'].to_i > 0
                        grand_grade_point1 = grand_grade_point1+grade.credit_points.to_f
                      
                        if grade.credit_points.to_i == 0
                          u_grade1 = u_grade1+1
                        end
                      end
                    end

                    grade = GradingLevel.percentage_to_grade(main_mark2, @batch.id)
                    if !grade.blank? and !grade.name.blank?
                      unless sub['subject_group_id'].to_i > 0
                        grand_grade_point2 = grand_grade_point2+grade.credit_points.to_f
                      
                        if grade.credit_points.to_i == 0
                          u_grade2 = u_grade2+1
                        end
                      end
                    end

                    grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                    if !grade.blank? and !grade.name.blank?
                      unless sub['subject_group_id'].to_i > 0
                        grand_grade_point = grand_grade_point+grade.credit_points.to_f
                      
                        if grade.credit_points.to_i == 0
                          u_grade = u_grade+1
                        end
                      end
                    end 
                  elsif subject_failed == false
                    grade = GradingLevel.percentage_to_grade(main_mark1, @batch.id)
                    if !grade.blank? and !grade.name.blank? and sub['subject_group_id'].to_i == 0

                      new_grade_point = grade.credit_points.to_f-2

                      if new_grade_point > 0
                        grand_grade_point1 = grand_grade_point1+new_grade_point.to_f
                      end
                    end

                    grade = GradingLevel.percentage_to_grade(main_mark2, @batch.id)
                    if !grade.blank? and !grade.name.blank? and sub['subject_group_id'].to_i == 0
                      new_grade_point = grade.credit_points.to_f-2
                      if new_grade_point > 0
                        grand_grade_point2 = grand_grade_point2+new_grade_point.to_f
                      end
                    end

                    grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                    if !grade.blank? and !grade.name.blank? and sub['subject_group_id'].to_i == 0
                      new_grade_point = grade.credit_points.to_f-2
                      if new_grade_point > 0
                        grand_grade_point = grand_grade_point+new_grade_point.to_f
                      end
                    end
                  end  

                end
                if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['at'] = at_total_mark1+at_total_mark2
                  
                  
                  if monthly_full_mark1 > 0 || monthly_full_mark2 > 0
                    if appeared_ct
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['cw'] = monthly_total_main_mark1+monthly_total_main_mark2
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['cw'] = "AB"
                    end  
                  end

                  if full_ob1 > 0 || full_ob2 > 0
                    if appeared_ob
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['ob'] = total_ob1+total_ob2
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['ob'] = "AB"
                    end  
                  end
                  if full_sb1 > 0 || full_sb2 > 0
                    if appeared_sb
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = total_sb1+total_sb2
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = "AB"
                    end  
                  end
                  if full_pr1 > 0 || full_pr2 > 0
                    if appeared_pr
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['pr'] = total_pr1+total_pr2
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['pr'] = "AB"
                    end  
                  end
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['rt'] = total_ob1+total_ob2+total_sb1+total_sb2+total_pr1+total_pr2
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['ct'] = total_mark1+total_mark2
                  if @subject_result[main_sub_id].blank?
                    @subject_result[main_sub_id] = {}
                    @subject_result[main_sub_id]['id'] = main_sub_id
                    @subject_result[main_sub_id]['name'] = sub['name']
                    
                  end
                  if @subject_result[main_sub_id]['total'].blank?
                    @subject_result[main_sub_id]['total'] = 1
                  else
                    @subject_result[main_sub_id]['total'] = @subject_result[main_sub_id]['total']+1
                  end
                  
                  grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                  if !grade.blank? && !grade.name.blank?
                    if subject_failed == true
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = "F"
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = grade.name
                    end
                  end
                  if !grade.blank? && !grade.name.blank? && sub['grade_subject'].to_i != 1
                    if grade.credit_points.to_i == 0 or subject_failed == true

                      if @subject_result[main_sub_id]['failed'].blank?
                        @subject_result[main_sub_id]['failed'] = 1
                      else
                        @subject_result[main_sub_id]['failed'] = @subject_result[main_sub_id]['failed']+1
                      end

                      if appeared
                        if @subject_result[main_sub_id]['appeared'].blank?
                          @subject_result[main_sub_id]['appeared'] = 1
                        else
                          @subject_result[main_sub_id]['appeared'] = @subject_result[main_sub_id]['appeared']+1
                        end
                      else
                        if @subject_result[main_sub_id]['absent'].blank?
                          @subject_result[main_sub_id]['absent'] = 1
                        else
                          @subject_result[main_sub_id]['absent'] = @subject_result[main_sub_id]['absent']+1
                        end

                      end

                      if fourth_subject.blank?
                        if @student_result[loop_std]['subject_failed'].blank?
                          @student_result[loop_std]['subject_failed'] = []
                        end
                        @student_result[loop_std]['subject_failed'] << sub['code']+"-"+main_mark.round().to_s
                      end 
                    else
                      if @subject_result[main_sub_id].blank?
                        @subject_result[main_sub_id] = {}
                        @subject_result[main_sub_id]['id'] = main_sub_id
                        @subject_result[main_sub_id]['name'] = sub['name']
                      end
                      if @subject_result[main_sub_id]['passed'].blank?
                        @subject_result[main_sub_id]['passed'] = 1
                      else
                        @subject_result[main_sub_id]['passed'] = @subject_result[main_sub_id]['passed']+1
                      end

                    end  
                  end
                end

                if @subject_highest_1st_term[sub['id'].to_i].blank?
                  @subject_highest_1st_term[sub['id'].to_i] = total_mark1
                elsif total_mark1.to_f > @subject_highest_1st_term[sub['id'].to_i].to_f
                  @subject_highest_1st_term[sub['id'].to_i] = total_mark1.to_f
                end

                if @subject_highest_2nd_term[sub['id'].to_i].blank?
                  @subject_highest_2nd_term[sub['id'].to_i] = total_mark2
                elsif total_mark2.to_f > @subject_highest_2nd_term[sub['id'].to_i].to_f
                  @subject_highest_2nd_term[sub['id'].to_i] = total_mark2.to_f
                end


                if @subject_highest[sub['id'].to_i].blank?
                  @subject_highest[sub['id'].to_i] = subject_full_marks
                elsif subject_full_marks.to_f > @subject_highest[sub['id'].to_i].to_f
                  @subject_highest[sub['id'].to_i] = subject_full_marks.to_f
                end

               
                unless sub['subject_group_id'].to_i > 0
                  next
                end
                #Start of 2nd subject
              
                tab['subjects'].each do |sub2|
                  if subject_array.include?(sub2['id'].to_i) or sub['subject_group_id'].to_i != sub2['subject_group_id'].to_i
                    next
                  end
                  subject_array << sub2['id'].to_i
                  if !@subject_code.blank? && !@subject_code[sub2['code']].blank?	
                    main_sub_id = @subject_code[sub2['code']]
                  else	
                    @subject_code[sub2['code'].to_s] = sub2['id']	
                    main_sub_id = sub2['id']	
                  end 	
                  if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)	
                    @student_result[loop_std]['subjects'][main_sub_id.to_s] = {}	
                    @student_result[loop_std]['subjects'][main_sub_id.to_s]['name'] = sub2['name']	
                    @student_result[loop_std]['subjects'][main_sub_id.to_s]['id'] = main_sub_id	
                    @student_result[loop_std]['subjects'][main_sub_id.to_s]['result'] = {}	
                  end

                  fourth_subject = false
                  if !@std_subject_hash_type.blank?
                    if @std_subject_hash_type.include?(std['id'].to_s+"|||"+sub2['id'].to_s+"|||4")
                      fourth_subject = true
                    end  
                  end 
                  if fourth_subject == false
                    total_subject = total_subject+1
                  end



                  has_exam = false
                  tab['exams'].each do |rs|
                    if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub2['id']].blank? and !rs['result'][rs['exam_id']][sub2['id']][std['id']].blank? and !rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].blank?
                      has_exam = true
                      break
                    end  
                  end 
                  if has_exam == false
                    next
                  end 

                  if batch_subject_id.include?(sub2['id'].to_i) or std_subject_id.include?(sub2['id'].to_i)

                    total_mark1 = 0
                    full_mark1 = 0
                    total_mark2 = 0
                    full_mark2 = 0

                    at_total_mark1 = 0
                    at_total_mark2 = 0

                    monthly_full_mark1 = 0
                    monthly_full_mark2 = 0
                    monthly_total_mark1 = 0
                    monthly_total_mark2 = 0
                    
                    monthly_total_main_mark1 = 0
                    monthly_total_main_mark2 = 0
                    
                    appeared = false
                    
                    full_sb12 = 0
                    full_sb22 = 0
                    total_sb12 = 0
                    total_sb22 = 0

                    full_ob12 = 0
                    full_ob22 = 0
                    total_ob12 = 0
                    total_ob22 = 0

                    full_pr12 = 0
                    full_pr22 = 0
                    total_pr12 = 0
                    total_pr22 = 0

                    
                    subject_failed = false

                    tab['exams'].each do |rs|
                      if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub2['id']].blank? and !rs['result'][rs['exam_id']][sub2['id']][std['id']].blank? 
                        if rs['exam_category'] == '1'
                          if rs['quarter'] == '1'
                            monthly_full_mark1 = monthly_full_mark1+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                          end  
                          if rs['quarter'] == '2'
                            monthly_full_mark2 = monthly_full_mark2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                          end
                        elsif rs['exam_category'] == '2'
                          if rs['quarter'] == '1'
                            full_mark1 = full_mark1+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                          end  
                          if rs['quarter'] == '2'
                            full_mark2 = full_mark2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                          end
                        elsif rs['exam_category'] == '3' 

                          if rs['quarter'] == '1'
                            full_sb1 = full_sb1+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                            full_sb12= full_sb12+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                            full_mark1 = full_mark1+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f

                          end  
                          if rs['quarter'] == '2'
                            full_sb2 = full_sb2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                            full_sb22 = full_sb22+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                            full_mark2 = full_mark2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f

                          end
                        elsif rs['exam_category'] == '4' 

                          if rs['quarter'] == '1'
                            full_ob1 = full_ob1+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                            full_ob12 = full_ob12+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                            full_mark1 = full_mark1+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f

                          end  
                          if rs['quarter'] == '2'
                            full_ob2 = full_ob2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                            full_ob22 = full_ob22+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                            full_mark2 = full_mark2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                          end
                        elsif rs['exam_category'] == '5' 
                          if rs['quarter'] == '1'
                            full_pr1 = full_pr1+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                            full_pr12 = full_pr12+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                            full_mark1 = full_mark1+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                          end  
                          if rs['quarter'] == '2'
                            full_pr2 = full_pr2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                            full_pr22 = full_pr22+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                            full_mark2 = full_mark2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                          end
                        end

                       

                        if rs['exam_category'] == '1'
                          if rs['quarter'] == '1'
                            monthly_total_mark1 = monthly_total_mark1+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                          end  
                          if rs['quarter'] == '2'
                            monthly_total_mark2 = monthly_total_mark2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                          end
                        elsif rs['exam_category'] == '2'
                          if rs['quarter'] == '1'
                            at_total_mark1 = at_total_mark1+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                          end  
                          if rs['quarter'] == '2'
                            at_total_mark2 = at_total_mark2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                          end
                        elsif rs['exam_category'] == '3' 
                          if rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_s != "AB"
                            appeared = true
                            full_absent = false
                          end
                          if rs['quarter'] == '1'
                            total_sb1 = total_sb1+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            total_sb12 = total_sb12+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            main_sub_grade = (total_sb1.to_f/full_sb1.to_f)*100
                            grade = GradingLevel.percentage_to_grade(main_sub_grade, @batch.id)
                            if !grade.blank? and !grade.credit_points.blank?
                              if grade.credit_points.to_i == 0 and fourth_subject.blank?
                                u_grade1 = u_grade1+1
                                subject_failed = true
                              end
                            end 
                          end  
                          if rs['quarter'] == '2'
                            total_sb2 = total_sb2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            total_sb22 = total_sb22+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            main_sub_grade = (total_sb2.to_f/full_sb2.to_f)*100
                            grade = GradingLevel.percentage_to_grade(main_sub_grade, @batch.id)
                            if !grade.blank? and !grade.credit_points.blank?
                              if grade.credit_points.to_i == 0 and fourth_subject.blank?
                                u_grade1 = u_grade1+1
                                subject_failed = true
                              end
                            end 
                          end
                        elsif rs['exam_category'] == '4' 
                          if rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_s != "AB"
                            appeared = true
                            full_absent = false
                          end
                          if rs['quarter'] == '1'
                            total_ob1 = total_ob1+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            total_ob12 = total_ob12+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            main_sub_grade = (total_ob1.to_f/full_ob1.to_f)*100
                            grade = GradingLevel.percentage_to_grade(main_sub_grade, @batch.id)
                            if !grade.blank? and !grade.credit_points.blank?
                              if grade.credit_points.to_i == 0 and fourth_subject.blank?
                                u_grade1 = u_grade1+1
                                subject_failed = true
                              end
                            end 
                          end  
                          if rs['quarter'] == '2'
                            total_ob2 = total_ob2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            total_ob22 = total_ob22+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            main_sub_grade = (total_ob2.to_f/full_ob2.to_f)*100
                            grade = GradingLevel.percentage_to_grade(main_sub_grade, @batch.id)
                            if !grade.blank? and !grade.credit_points.blank?
                              if grade.credit_points.to_i == 0 and fourth_subject.blank?
                                u_grade1 = u_grade1+1
                                subject_failed = true
                              end
                            end 
                          end
                        elsif rs['exam_category'] == '5' 
                          if rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_s != "AB"
                            appeared = true
                            full_absent = false
                          end
                          if rs['quarter'] == '1'
                            total_pr1 = total_pr1+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            total_pr12 = total_pr12+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            main_sub_grade = (total_pr1.to_f/full_pr1.to_f)*100
                            grade = GradingLevel.percentage_to_grade(main_sub_grade, @batch.id)
                            if !grade.blank? and !grade.credit_points.blank?
                              if grade.credit_points.to_i == 0 and fourth_subject.blank?
                                u_grade1 = u_grade1+1
                                subject_failed = true
                              end
                            end  
                          end  
                          if rs['quarter'] == '2'
                            total_pr2 = total_pr2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            total_pr22 = total_pr22+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            main_sub_grade = (total_pr2.to_f/full_pr2.to_f)*100
                            grade = GradingLevel.percentage_to_grade(main_sub_grade, @batch.id)
                            if !grade.blank? and !grade.credit_points.blank?
                              if grade.credit_points.to_i == 0 and fourth_subject.blank?
                                u_grade1 = u_grade1+1
                                subject_failed = true
                              end
                            end
                          end
                        end
                      end    
                    end

                    if full_mark1 > 0 && monthly_full_mark1 != 0
                      full_mark1 = full_mark1+20
                    end
                    if full_mark2 > 0 && monthly_full_mark2 != 0
                      full_mark2 = full_mark2+20
                    end

                    if full_mark1 > 0 && full_mark2 > 0
                      exam_type = 3
                    elsif full_mark2 > 0
                      exam_type = 2
                    end

                    



                    term_mark_multiplier = 0.75
                    if @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4
                       term_mark_multiplier = 0.80
                    end
                    if @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 8
                       term_mark_multiplier = 0.90
                    end



                    total_mark2 = total_ob22+total_sb22+total_pr22
                    total_mark2_80 = total_mark2.to_f
                    if full_mark2 > 100 or term_mark_multiplier == 0.80
                      total_mark2_80 = total_mark2.to_f*term_mark_multiplier
                    end  
                    total_mark1 = total_ob12+total_sb12+total_pr12
                    total_mark1_80 = total_mark1.to_f
                    if full_mark1 > 100 or term_mark_multiplier == 0.80
                      total_mark1_80 = total_mark1.to_f*term_mark_multiplier
                    end


                    monthly_mark_multiply = 20
                    if full_mark1 == 75
                      full_mark1 = 75
                    elsif full_mark1 >=100
                      full_mark1 = 100
                    else
                      full_mark1 = 50
                      monthly_mark_multiply = 10
                    end
                    monthly_mark_multiply2 = 20
                    if full_mark2 == 75
                      full_mark2 = 75
                    elsif full_mark2 >=100
                      full_mark2 = 100
                    else
                      full_mark2 = 50
                      monthly_mark_multiply2 = 10
                    end 

                    if @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 8
                      monthly_mark_multiply = monthly_mark_multiply/2
                      monthly_mark_multiply2 = monthly_mark_multiply2/2
                    end

                    monthly_total_main_mark1 = monthly_total_mark1
                    monthly_total_main_mark2 = monthly_total_mark2
                    if @connect_exam_obj.result_type != 5 and @connect_exam_obj.result_type != 6
                      if monthly_total_mark1 > 0
                        monthly_total_mark1 = (monthly_total_mark1/monthly_full_mark1)*monthly_mark_multiply
                        monthly_total_mark1 = monthly_total_mark1.round()
                      end 
                      if monthly_total_mark2 > 0
                        monthly_total_mark2 = (monthly_total_mark2/monthly_full_mark2)*monthly_mark_multiply2
                        monthly_total_mark2 = monthly_total_mark2.round()
                      end
                    end

                    total_mark2_no_round = total_mark2
                    full_mark2 = full_mark2
                    total_mark2 = total_mark2.round()

                    total_mark1_no_round = total_mark1
                    full_mark1 = full_mark1
                    total_mark1 = total_mark1.round()


                    if total_mark2.to_f>0 and full_mark2.to_f>0
                      main_mark2 = main_mark2+((total_mark2.to_f/full_mark2.to_f)*100)
                    end 
                    if total_mark1.to_f>0 and full_mark1.to_f>0
                      main_mark1 = main_mark1+((total_mark1.to_f/full_mark1.to_f)*100)
                    end 
                    if exam_type == 3
                      main_mark = main_mark+((total_mark1.to_f+total_mark2.to_f)/(full_mark1.to_f+full_mark2.to_f)*100)
                      main_mark = main_mark.round()
                      main_mark_no_round = (total_mark1_no_round.to_f+total_mark2_no_round.to_f)/(full_mark1.to_f+full_mark2.to_f)*100
                    elsif  exam_type == 2
                      main_mark = main_mark+(total_mark2.to_f/full_mark2.to_f*100)
                      main_mark = main_mark.round()
                      main_mark_no_round = total_mark2_no_round.to_f/full_mark2.to_f*100
                    else
                      main_mark = main_mark+(total_mark1.to_f/full_mark1.to_f*100)
                      main_mark = main_mark.round()
                      main_mark_no_round = total_mark1_no_round.to_f/full_mark1.to_f*100
                    end 
                    
                    
                    

                    if main_mark1 > 0
                      main_mark1 = main_mark1/2.00
                      main_mark1 = main_mark1.round()
                    end
                    if main_mark > 0
                      main_mark = main_mark/2.00
                      main_mark = main_mark.round()
                    end
                    if main_mark2 > 0
                      main_mark2 = main_mark2/2.00
                      main_mark2 = main_mark2.round()
                    end

                    subject_full_marks = main_mark
                    if sub2['grade_subject'].to_i != 1
                      if @student_subject_marks[sub2['id'].to_i].blank?
                        @student_subject_marks[sub2['id'].to_i] = {}
                      end

                      @student_subject_marks[sub2['id'].to_i][std['id'].to_i] = subject_full_marks


                      grand_total1 = grand_total1+total_mark1
                      grand_total2 = grand_total2+total_mark2
                      grand_total = grand_total+subject_full_marks

                      grand_total1_with_fraction = grand_total1_with_fraction+total_mark1_no_round
                      grand_total2_with_fraction = grand_total2_with_fraction+total_mark2_no_round
                      grand_total_with_fraction = grand_total_with_fraction+main_mark_no_round



                      if fourth_subject.blank?
                        grade = GradingLevel.percentage_to_grade(main_mark1, @batch.id)
                        if !grade.blank? and !grade.name.blank?
                          grand_grade_point1 = grand_grade_point1+grade.credit_points.to_f
                          if grade.credit_points.to_i == 0
                            u_grade1 = u_grade1+1
                          end
                        end

                        grade = GradingLevel.percentage_to_grade(main_mark2, @batch.id)
                        if !grade.blank? and !grade.name.blank?
                          grand_grade_point2 = grand_grade_point2+grade.credit_points.to_f
                          if grade.credit_points.to_i == 0
                            u_grade2 = u_grade2+1
                          end
                        end

                        grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                        if !grade.blank? and !grade.name.blank?
                          grand_grade_point = grand_grade_point+grade.credit_points.to_f
                          if grade.credit_points.to_i == 0
                            u_grade = u_grade+1
                          end
                        end 
                      else
                        grade = GradingLevel.percentage_to_grade(main_mark1, @batch.id)
                        if !grade.blank? and !grade.name.blank?

                          new_grade_point = grade.credit_points.to_f-2

                          if new_grade_point > 0
                            grand_grade_point1 = grand_grade_point1+new_grade_point.to_f
                          end
                        end

                        grade = GradingLevel.percentage_to_grade(main_mark2, @batch.id)
                        if !grade.blank? and !grade.name.blank?
                          new_grade_point = grade.credit_points.to_f-2
                          if new_grade_point > 0
                            grand_grade_point2 = grand_grade_point2+new_grade_point.to_f
                          end
                        end

                        grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                        if !grade.blank? and !grade.name.blank?
                          new_grade_point = grade.credit_points.to_f-2
                          if new_grade_point > 0
                            grand_grade_point = grand_grade_point+new_grade_point.to_f
                          end
                        end
                      end  

                    end
                    if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['at'] = at_total_mark1+at_total_mark2
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['cw'] = monthly_total_main_mark1+monthly_total_main_mark2

                      @student_result[loop_std]['subjects'][main_sub_id]['result']['ob'] = total_ob12+total_ob22
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = total_sb12+total_sb22
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['pr'] = total_pr12+total_pr22
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['rt'] = total_ob12+total_ob22+total_sb12+total_sb22+total_pr12+total_pr22
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['ct'] = total_mark1+total_mark2
                      if @subject_result[main_sub_id].blank?
                        @subject_result[main_sub_id] = {}
                        @subject_result[main_sub_id]['id'] = main_sub_id
                        @subject_result[main_sub_id]['name'] = sub2['name']
                        
                      end
                      if @subject_result[main_sub_id]['total'].blank?
                        @subject_result[main_sub_id]['total'] = 1
                      else
                        @subject_result[main_sub_id]['total'] = @subject_result[main_sub_id]['total']+1
                      end
                      grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                      if !grade.blank? && !grade.name.blank?
                        if subject_failed == true
                          @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = "F"
                        else
                          @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = grade.name
                        end
                      end
                      if !grade.blank? && !grade.name.blank? && sub2['grade_subject'].to_i != 1
                        
                        if grade.credit_points.to_i == 0 or subject_failed == true

                          if @subject_result[main_sub_id]['failed'].blank?
                            @subject_result[main_sub_id]['failed'] = 1
                          else
                            @subject_result[main_sub_id]['failed'] = @subject_result[main_sub_id]['failed']+1
                          end

                          if appeared
                            if @subject_result[main_sub_id]['appeared'].blank?
                              @subject_result[main_sub_id]['appeared'] = 1
                            else
                              @subject_result[main_sub_id]['appeared'] = @subject_result[main_sub_id]['appeared']+1
                            end
                          else
                            if @subject_result[main_sub_id]['absent'].blank?
                              @subject_result[main_sub_id]['absent'] = 1
                            else
                              @subject_result[main_sub_id]['absent'] = @subject_result[main_sub_id]['absent']+1
                            end

                          end

                          if fourth_subject.blank?
                            if @student_result[loop_std]['subject_failed'].blank?
                              @student_result[loop_std]['subject_failed'] = []
                            end
                            @student_result[loop_std]['subject_failed'] << sub2['code']+"-"+main_mark.round().to_s
                          end 
                        else
                          if @subject_result[main_sub_id].blank?
                            @subject_result[main_sub_id] = {}
                            @subject_result[main_sub_id]['id'] = main_sub_id
                            @subject_result[main_sub_id]['name'] = sub2['name']
                          end
                          if @subject_result[main_sub_id]['passed'].blank?
                            @subject_result[main_sub_id]['passed'] = 1
                          else
                            @subject_result[main_sub_id]['passed'] = @subject_result[main_sub_id]['passed']+1
                          end

                        end  
                      end
                    end

                    if @subject_highest_1st_term[sub2['id'].to_i].blank?
                      @subject_highest_1st_term[sub2['id'].to_i] = total_mark1
                    elsif total_mark1.to_f > @subject_highest_1st_term[sub2['id'].to_i].to_f
                      @subject_highest_1st_term[sub2['id'].to_i] = total_mark1.to_f
                    end

                    if @subject_highest_2nd_term[sub2['id'].to_i].blank?
                      @subject_highest_2nd_term[sub2['id'].to_i] = total_mark2
                    elsif total_mark2.to_f > @subject_highest_2nd_term[sub2['id'].to_i].to_f
                      @subject_highest_2nd_term[sub2['id'].to_i] = total_mark2.to_f
                    end


                    if @subject_highest[sub2['id'].to_i].blank?
                      @subject_highest[sub2['id'].to_i] = subject_full_marks
                    elsif subject_full_marks.to_f > @subject_highest[sub2['id'].to_i].to_f
                      @subject_highest[sub2['id'].to_i] = subject_full_marks.to_f
                    end

                  end
                end
              end
            end 
    
            



            if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
              @total_std_batch = @total_std_batch+1
              
              
              
              if full_absent
                @absent_in_all_subject = @absent_in_all_subject+1
              end
              
              if exam_type == 3
                grade_point_avg = grand_grade_point.to_f/total_subject.to_f
                grade_point_avg = grade_point_avg.round(2)
                if grade_point_avg > 5
                  grade_point_avg = 5.00
                  grand_grade_point = total_subject*5
                end
                @student_result[loop_std]['gpa'] = grand_grade_point
                @student_result[loop_std]['grand_total'] = grand_total
                @student_result[loop_std]['grand_total_with_fraction'] = grand_total_with_fraction
              end
              if exam_type == 1
                grade_point_avg = grand_grade_point1.to_f/total_subject.to_f
                grade_point_avg = grade_point_avg.round(2)
                if grade_point_avg > 5
                  grade_point_avg = 5.00
                  grand_grade_point1 = total_subject*5
                end
                @student_result[loop_std]['gpa'] = grand_grade_point1
                @student_result[loop_std]['grand_total'] = grand_total1
                @student_result[loop_std]['grand_total_with_fraction'] = grand_total1_with_fraction
              end
              if exam_type == 2
                grade_point_avg = grand_grade_point2.to_f/total_subject.to_f
                grade_point_avg = grade_point_avg.round(2)
                if grade_point_avg > 5
                  grade_point_avg = 5.00
                  grand_grade_point2 = total_subject*5
                end
                @student_result[loop_std]['gpa'] = grand_grade_point2
                @student_result[loop_std]['grand_total'] = grand_total2
                @student_result[loop_std]['grand_total_with_fraction'] = grand_total2_with_fraction
              end
              
              @student_result[loop_std]['gp'] = grade_point_avg
              
              gradeObj = GradingLevel.grade_point_to_grade(grade_point_avg, @batch.id)
              if !gradeObj.blank? and !gradeObj.name.blank?
                @student_result[loop_std]['lg'] = gradeObj.name
              end
              loop_std = loop_std+1
            end
            
            if u_grade == 0  
              grand_total_new = 50000-grand_total_with_fraction
              grand_grade_new = 50000-grand_grade_point
              
              if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
                @student_list_batch << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
                if exam_type == 3
                  if !gradeObj.blank? and !gradeObj.name.blank?
                    if @grade_count[gradeObj.name].blank?
                      @grade_count[gradeObj.name] = 1
                    else
                      @grade_count[gradeObj.name] = @grade_count[gradeObj.name]+1
                    end
                  end
                end
              end 
              if std_group_name == group_name or connect_exam_id.to_i == @connect_exam_obj.id
                @student_list << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
              end
            end  
        
            if u_grade1 == 0  
              grand_total_new = 50000-grand_total1_with_fraction
              grand_grade_new = 50000-grand_grade_point1
              if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
                @student_list_first_term_batch << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
                if exam_type == 1
                  if !gradeObj.blank? and !gradeObj.name.blank?
                    if @grade_count[gradeObj.name].blank?
                      @grade_count[gradeObj.name] = 1
                    else
                      @grade_count[gradeObj.name] = @grade_count[gradeObj.name]+1
                    end
                  end
                end
              end 
              if std_group_name == group_name or connect_exam_id.to_i == @connect_exam_obj.id
                @student_list_first_term << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
                if @section_wise_position[batch_data.id].blank?
                  @section_wise_position[batch_data.id] = []
                end
                @section_wise_position[batch_data.id] << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
              end
            end  
        
            if u_grade2 == 0  
              grand_total_new = 50000-grand_total2_with_fraction
              grand_grade_new = 50000-grand_grade_point2
              if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
                @student_list_second_term_batch << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
                if exam_type == 2
                  if !gradeObj.blank? and !gradeObj.name.blank?
                    if @grade_count[gradeObj.name].blank?
                      @grade_count[gradeObj.name] = 1
                    else
                      @grade_count[gradeObj.name] = @grade_count[gradeObj.name]+1
                    end
                  end
                end
              end
              if std_group_name == group_name or connect_exam_id.to_i == @connect_exam_obj.id
                @student_list_second_term << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
              end
            end  
            

            if total_failed_appaered > 0
              if @failed_appeared_absent[total_failed_appaered].blank?
                @failed_appeared_absent[total_failed_appaered] = 1
              else
                @failed_appeared_absent[total_failed_appaered] = @failed_appeared_absent[total_failed_appaered]+1
              end
            end

            if total_failed > 0
              if @failed_partial_absent[total_failed].blank?
                @failed_partial_absent[total_failed] = 1
              else
                @failed_partial_absent[total_failed] = @failed_partial_absent[total_failed]+1
              end
            end


          end
        end
      end

      @student_position_first_term = {}
      @student_position_second_term = {}
      @student_position = {}
   
      @student_position_first_term_batch = {}
      @student_position_second_term_batch = {}
      @student_position_batch = {}
      
      @section_all_position_batch = {}
      last_grade = 0.0
      last_total = 0.0
      
      unless @student_list.blank?
        position = 0
        @sorted_students = @student_list.sort
        @sorted_students.each do|s|
          if last_grade != s[0] or last_total != s[1]
            position = position+1
          end
          last_grade = s[0]
          last_total = s[1]
          @student_position[s[2].to_i] = position
        end 
      end
    
      last_grade = 0.0
      last_total = 0.0
      unless @section_wise_position.blank?
        @section_wise_position.each do|key,value|
          position = 0
         
          @sorted_students = @section_wise_position[key].sort
          @sorted_students.each do|s|
            
            if last_grade != s[0] or last_total != s[1]
              position = position+1
            end
            last_grade = s[0]
            last_total = s[1]
            if @section_all_position_batch[key].blank?
              @section_all_position_batch[key] = {}
            end
            @section_all_position_batch[key][s[2].to_i] = position
          end 
        end
      end
      
     
      last_grade = 0.0
      last_total = 0.0
      unless @student_list_first_term.blank?
        position = 0
        @sorted_students = @student_list_first_term.sort
        @sorted_students.each do|s|
          if last_grade != s[0] or last_total != s[1]
            position = position+1
          end
          last_grade = s[0]
          last_total = s[1]
          @student_position_first_term[s[2].to_i] = position
        end 
      end
    
      last_grade = 0.0
      last_total = 0.0
      unless @student_list_second_term.blank?
        position = 0
        @sorted_students = @student_list_second_term.sort
        @sorted_students.each do|s|
          if last_grade != s[0] or last_total != s[1]
            position = position+1
          end
          last_grade = s[0]
          last_total = s[1]
          @student_position_second_term[s[2].to_i] = position
        end 
      end
    
      last_grade = 0.0
      last_total = 0.0
      unless @student_list_batch.blank?
        position = 0
        @sorted_students = @student_list_batch.sort
        @sorted_students.each do|s|
          if last_grade != s[0] or last_total != s[1]
            position = position+1
          end
          last_grade = s[0]
          last_total = s[1]
          @student_position_batch[s[2].to_i] = position
        end 
      end
    
      last_grade = 0.0
      last_total = 0.0
      unless @student_list_first_term_batch.blank?
        position = 0
        @sorted_students = @student_list_first_term_batch.sort
        @sorted_students.each do|s|
          if last_grade != s[0] or last_total != s[1]
            position = position+1
          end
          last_grade = s[0]
          last_total = s[1]
          @student_position_first_term_batch[s[2].to_i] = position
        end 
      end
    
      last_grade = 0.0
      last_total = 0.0
      unless @student_list_second_term_batch.blank?
        position = 0
        @sorted_students = @student_list_second_term_batch.sort
        @sorted_students.each do|s|
          if last_grade != s[0] or last_total != s[1]
            position = position+1
          end
          last_grade = s[0]
          last_total = s[1]
          @student_position_second_term_batch[s[2].to_i] = position
        end 
      end
    end
  end  
  
  
end

