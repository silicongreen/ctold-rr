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
             
             if @exam.maximum_marks.to_f != max_mark[:maximum_marks].to_f and (max_score.blank? or max_score.marks.to_f < max_mark[:maximum_marks].to_f)
                @exam.update_attribute(:maximum_marks,max_mark[:maximum_marks])
                
             elsif !max_score.blank? and max_score.marks.to_f > max_mark[:maximum_marks].to_f  
                @exam_marks_error = true
             end 
           end
         end
       end 
     
      unless params[:exam_score].blank?
        params[:exam_score].each_pair do |exam_id, stdetails|
          @exam = Exam.find_by_id(exam_id)
          stdetails.each_pair do |student_id, details|
            @exam_score = ExamScore.find(:first, :conditions => {:exam_id => @exam.id, :student_id => student_id} )
            if details[:marks].nil?
               details[:marks] = 0
            end
            if @exam_score.nil?
              
              unless details[:marks].nil? 
                if details[:marks].to_f <= @exam.maximum_marks.to_f
                  
                  unless details[:remarks].blank?
                    if details[:remarks].kind_of?(Array)
                      remarks_details = details[:remarks].join("|")
                      ExamScore.create do |score|
                        score.exam_id          = @exam.id
                        score.student_id       = student_id
                        score.marks            = details[:marks]
                        score.remarks          = remarks_details
                      end
                      
                    end
                  else
                    ExamScore.create do |score|
                      score.exam_id          = @exam.id
                      score.student_id       = student_id
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
        end
      end
      
        
      
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
          @exam_comments.update_attributes(details)
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
      @group_exam = GroupedExam.find_all_by_connect_exam_id(@exam_connect.id, :order=>"priority ASC")
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
        @students = Student.active.find_all_by_batch_id(@batch.id, :order => 'first_name ASC, middle_name ASC, last_name ASC')
      elsif MultiSchool.current_school.id == 342
        @students = Student.active.find_all_by_batch_id(@batch.id, :order => 'class_roll_no ASC')
      else
        @students = Student.active.find_all_by_batch_id(@batch.id, :order => 'if(class_roll_no = "" or class_roll_no is null,0,cast(class_roll_no as unsigned)),first_name ASC')
      end
     
    else
      assigned_students = StudentsSubject.find_all_by_subject_id_and_batch_id(@exam_subject.id,@exam_subject.batch_id)
      @students = []
      @studentids = []
      assigned_students.each do |s|
        student = Student.find_by_id(s.student_id)
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
  
  def new_exam_connect
    @batch = Batch.find(params[:id])
    @exam_groups = ExamGroup.active.find_all_by_batch_id(@batch.id)
    @exam_groups.reject!{|e| e.exam_type=="Grades"}
    
    if request.post?
      #abort params.inspect
      unless params[:exam_grouping].nil?
        unless params[:exam_grouping][:exam_group_ids].nil?
          weightages = params[:weightage]
          priority = params[:priority]
          total = 0
          weightages.map{|w| total+=w.to_f}          
          unless total=="100".to_f
            flash[:notice]="#{t('flash9')}"
            return
          else
            if params[:exam_grouping][:name].nil?
              flash[:notice]="#{t('flash25')}"
              return
            else
              @exam_connect  = ExamConnect.create(:name => params[:exam_grouping][:name],:result_type => params[:exam_grouping][:result_type], :batch_id => params[:id], :school_id => MultiSchool.current_school.id,:attandence_start_date => params[:exam_grouping][:attandence_start_date],:attandence_end_date => params[:exam_grouping][:attandence_end_date],:published_date => params[:exam_grouping][:published_date])
              @exam_connect_id = @exam_connect.id
                            
              exam_group_ids = params[:exam_grouping][:exam_group_ids]
              exam_group_ids.each_with_index do |e,i|
                GroupedExam.create(:exam_group_id=>e,:batch_id=>@batch.id, :connect_exam_id => @exam_connect_id,:weightage=>weightages[i],:priority=>priority[i])
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
          #abort params.inspect
          
          total = 0
          weightages.map{|w| total+=w.to_f}          
          unless total=="100".to_f
            flash[:notice]="#{t('flash9')}"
            return
          else
            if params[:exam_grouping][:name].nil?
              flash[:notice]="#{t('flash25')}"
              return
            else              
              @exam_connect.update_attributes(:name=> params[:exam_grouping][:name],:result_type => params[:exam_grouping][:result_type],:attandence_start_date => params[:exam_grouping][:attandence_start_date],:attandence_end_date => params[:exam_grouping][:attandence_end_date],:published_date => params[:exam_grouping][:published_date])
                     
              @exam_connect_id = @exam_connect.id
              
              GroupedExam.delete_all(:connect_exam_id=>@exam_connect_id)              
              exam_group_ids = params[:exam_grouping][:exam_group_ids]
              exam_group_ids.each_with_index do |e,i|
                GroupedExam.create(:exam_group_id=>e,:batch_id=>@batch.id, :connect_exam_id => @exam_connect_id,:weightage=>weightages[i],:priority=>priority[i])
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
        unless st.immediate_contact.nil? 
          available_user_ids << st.immediate_contact.user_id
          batch_ids[st.immediate_contact.user_id] = st.batch_id
          student_ids[st.immediate_contact.user_id] = st.id
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
    @exam_group = ExamGroup.active.find(params[:exam_group])
    if @exam_group.is_current == false
      student_list = []
      allExam = @exam_group.exams
      allExam.each do |exams|
        score_data = exams.exam_scores
        score_data.each do |sd|
          std = Student.find_by_id(sd.student_id)
          if !std.blank? and std.batch_id == exams.batch_id
            student_list.push(sd.student_id) unless student_list.include?(sd.student_id)
          end
        end          
      end
      if student_list.nil?
        flash[:notice] = "#{t('flash_student_notice')}"
        redirect_to :action => 'exam_wise_report' and return
      end

      @batch = @exam_group.batch
      @students = Student.find_all_by_id(student_list, :order=>"class_roll_no ASC")
    else
      @batch = @exam_group.batch
      @students=Student.find_all_by_batch_id(@batch.id, :order=>"class_roll_no ASC")
    end
    
    @assigned_employee=@batch.employees
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
    
    @ranked_student = ExamScore.all(:select =>["SUM(exam_scores.marks) as total_score,exam_scores.student_id"],:conditions=>["exams.exam_group_id = ?",@exam_group.id],:joins=>[:exam,:student,:grading_level],:group =>"exam_scores.student_id",:order=>"total_score DESC")
    @tmp_students = []
    unless @ranked_student.blank?
      @ranked_student.each do |ras|
        std_data = Student.find_by_id(ras.student_id)
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
    
    @assigned_employee=@batch.employees
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
    
    @exam_group = ExamGroup.active.find(params[:exam_group])
    @student = Student.find_by_id(params[:student])
    @for_save = params[:for_save]
    
    @batch = @student.batch
    @assigned_employee=@batch.employees
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
      render :pdf => 'student_wise_generated_report'
    else
      pdf_name = "group_exam_"+params[:exam_group].to_s+"_"+params[:student].to_s+".pdf"
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
        @students = Student.find_all_by_id(student_list)
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
    if File.file?(file_name) && Rails.cache.exist?("continues_#{@id}_#{@batch.id}") && api_from != "local"
      FileUtils.chown 'champs21','champs21',file_name
      redirect_to "/result_pdf/0"+MultiSchool.current_school.id.to_s+"/0"+@batch.id.to_s+"/continues/0"+@connect_exam_obj.id.to_s+"/"+pdf_name
    else
      @assigned_employee=@batch.employees
      @report_data = Rails.cache.fetch("continues_#{@id}_#{@batch.id}"){
        get_continues(@id,@batch.id)
        report_data = []
        if @student_response['status']['code'].to_i == 200
          report_data = @student_response['data']
        end
        report_data
      }
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
  
  def comment_tabulation
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    @report_data = Rails.cache.fetch("tabulation_#{@id}_#{@batch.id}"){
      get_tabulation(@id,@batch.id)
      report_data = []
      if @student_response['status']['code'].to_i == 200
        report_data = @student_response['data']
      end
      report_data
    }
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
    
    pdf_name = "comment_tabulation_connect_exam_"+@connect_exam_obj.id.to_s+".pdf"
    dirname = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"tabulation","0"+@connect_exam_obj.id.to_s)
    unless File.directory?(dirname)
      FileUtils.mkdir_p(dirname)
    end
    FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s))
    file_name = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"tabulation","0"+@connect_exam_obj.id.to_s,pdf_name)
    champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_from = champs21_config['from']
    if File.file?(file_name) && Rails.cache.exist?("tabulation_#{@id}_#{@batch.id}") && api_from != "local"
      FileUtils.chown 'champs21','champs21',file_name
      redirect_to "/result_pdf/0"+MultiSchool.current_school.id.to_s+"/0"+@batch.id.to_s+"/tabulation/0"+@connect_exam_obj.id.to_s+"/"+pdf_name
    else
      @report_data = Rails.cache.fetch("tabulation_#{@id}_#{@batch.id}"){
        get_tabulation(@id,@batch.id)
        report_data = []
        if @student_response['status']['code'].to_i == 200
          report_data = @student_response['data']
        end
        report_data
      }
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
  
  def tabulation
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    @assigned_employee=@batch.employees
    
    pdf_name = "tabulation_connect_exam_"+@connect_exam_obj.id.to_s+".pdf"
    dirname = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"tabulation","0"+@connect_exam_obj.id.to_s)
    unless File.directory?(dirname)
      FileUtils.mkdir_p(dirname)
    end
    FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s))
    file_name = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"tabulation","0"+@connect_exam_obj.id.to_s,pdf_name)
    champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_from = champs21_config['from']
    if File.file?(file_name) && Rails.cache.exist?("tabulation_#{@id}_#{@batch.id}") && api_from != "local"
      FileUtils.chown 'champs21','champs21',file_name
      redirect_to "/result_pdf/0"+MultiSchool.current_school.id.to_s+"/0"+@batch.id.to_s+"/tabulation/0"+@connect_exam_obj.id.to_s+"/"+pdf_name
    else
      
      @report_data = Rails.cache.fetch("tabulation_#{@id}_#{@batch.id}"){
        get_tabulation(@id,@batch.id)
        report_data = []
        if @student_response['status']['code'].to_i == 200
          report_data = @student_response['data']
        end
        report_data
      }
     
  
       
      @exam_comment = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
      if MultiSchool.current_school.id == 280 && @connect_exam_obj.result_type==2
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
    @assigned_employee=@batch.employees
    
    
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
      @assigned_employee=@batch.employees
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
      @assigned_employee=@batch.employees
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
      @courses = Rails.cache.fetch("course_data_#{batch_name.parameterize("_")}_#{school_id}"){
        @batches_data = Batch.find(:all, :conditions => ["name = ?", batch_name], :select => "course_id")
        @batch_ids = @batches_data.map{|b| b.course_id}
        @tmp_courses = Course.find(:all, :conditions => ["courses.id IN (?) and batches.name = ?", @batch_ids, batch_name], :select => "courses.*,  GROUP_CONCAT(courses.section_name,'-',courses.id,'-',batches.id) as courses_batches", :joins=> "INNER JOIN `batches` ON batches.course_id = courses.id", :group => 'course_name', :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
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
            :margin => {:top=> 30,
            :bottom => 40,
            :left=> 10,
            :right => 10}
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
          if @connect_exam_obj.result_type != 1 and @connect_exam_obj.result_type != 6
            render :pdf => template,
            :save_to_file => file_name,
            :save_only    => for_save,
            :orientation => 'Portrait'
          else
            
            render :pdf => template,
            :save_to_file => file_name,
            :save_only    => for_save,
            :orientation => 'Landscape'
          end
        elsif  MultiSchool.current_school.id == 342 or MultiSchool.current_school.id == 324
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

end

