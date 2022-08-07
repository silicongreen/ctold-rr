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
            #            pdf_name = "connect_exam_"+@connect_exam.to_s+"_"+@student.id.to_s+".pdf"
            #            dirname = Rails.root.join('public','result_pdf_archive',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"connectexam","0"+@connect_exam.to_s)
            #            unless File.directory?(dirname)
            #              FileUtils.mkdir_p(dirname)
            #              FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf_archive',"0"+MultiSchool.current_school.id.to_s))
            #            end
            #            file_name = Rails.root.join('public','result_pdf_archive',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"connectexam","0"+@connect_exam.to_s,pdf_name)
            render_connect_exam("split_pdf_and_save",true)
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
        elective_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>["elective_group_id IS NOT NULL AND subjects.is_deleted = false and elective_groups.is_deleted = false and elective_groups.batch_id = ?",@batch.id],:include=>["elective_group"])
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
                if details[:remarks].kind_of?(Array)
                  remarks_details = details[:remarks].join("|")
                  ExamScore.create do |score|
                    score.exam_id          = @exam.id
                    score.student_id       = student_id
                    score.user_id          = current_user.id
                    score.remarks          = remarks_details
                    score.marks            = 0
                  end
                else
                  ExamScore.create do |score|
                    score.exam_id          = @exam.id
                    score.student_id       = student_id
                    score.user_id          = current_user.id
                    score.remarks          = details[:remarks]
                    score.marks            = 0
                  end
                end
              end
              
            else
              if details[:remarks].kind_of?(Array)
                details[:remarks] = details[:remarks].join("|")
              end
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
                    remarks = ""
                    unless details[:remarks].blank?
                      remarks  = details[:remarks]
                    end
                    ExamScore.create do |score|
                      score.exam_id          = @exam.id
                      score.student_id       = student_id
                      score.user_id          = current_user.id
                      score.marks            = details[:marks]
                      score.remarks          = remarks
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
    @no_exams = []
    
    
    if !@exam_subject.no_exams.blank?
      @group_exam = GroupedExam.find_all_by_connect_exam_id_and_show_in_connect(@exam_connect.id,1, :order=>"priority ASC")
      unless @group_exam.blank?
        @group_exam.each do |group_exam|
          exam_group = ExamGroup.active.find(group_exam.exam_group_id)
          unless exam_group.blank?
            exam = Exam.find_by_exam_group_id_and_subject_id(exam_group.id,@exam_subject.id)
            unless exam.blank?
              @no_exams << exam
            end
          end  
        end
      end 
    end
    
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
              student_list = ""
              if !params[:assignment].blank? && !params[:assignment][:student_ids].blank?
                student_ids = params[:assignment][:student_ids]
                if !student_ids.blank?
                  student_list = student_ids.join(",")
                end
              end
              @exam_connect.update_attributes(:name=> params[:exam_grouping][:name],:total_working_days=>params[:exam_grouping][:total_working_days],:students=> student_list,:is_common=> params[:exam_grouping][:is_common],:printing_date => params[:exam_grouping][:printing_date],:result_type => params[:exam_grouping][:result_type],:quarter_number => params[:exam_grouping][:quarter_number],:next_session_begins => params[:exam_grouping][:next_session_begins],:promoted_to => params[:exam_grouping][:promoted_to],:attandence_start_date => params[:exam_grouping][:attandence_start_date],:attandence_end_date => params[:exam_grouping][:attandence_end_date],:published_date => params[:exam_grouping][:published_date])
                     
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
    @batch_id = @exam_connect.batch_id
    unless @exam_connect.blank?
      @exam_groups = GroupedExam.find_all_by_connect_exam_id(@exam_connect.id)
      unless @exam_groups.blank?
        @exam_groups.each do |exam_group|
          exam_group_obj = ExamGroup.find_by_id(exam_group.exam_group_id)
          unless exam_group_obj.blank?
            exam_group_obj.is_deleted = 1
            exam_group_obj.save
          end
        end
      end
      @exam_connect.is_deleted = 1
      @exam_connect.save
    end
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
        @batch_data = Batch.find_by_course_id(course_id)
      else
        @batch_data = Batch.find_by_course_id_and_name(course_id, batch_name)
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
  
  def student_wise_tabulation_excel  
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'tabulation'
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
      if @students.blank?
        @students_archive = ArchivedStudent.find_all_by_former_id(student_list)
        unless @students_archive.blank?
          @students_archive.each do |std|
            std.id = std.former_id
            @students << std
          end
        end
        
      end
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
    if @std_subject_hash.nil?
      std_subject = StudentsSubject.find_all_by_batch_id(@batch.id)
      @std_subject_hash = []
      unless std_subject.blank?
        std_subject.each do |std_sub|
          @std_subject_hash << std_sub.student_id.to_s+"_"+std_sub.subject_id.to_s
        end
      end
    end
    @main_student = @students
    @students = []
    if !@main_student.blank?
      @main_student.each do |student|
        @student = student
        off_rank = false
        exam_score = []
        @exams.each do |exam| 
          exam_score.push exam.exam_scores.find_by_student_id(@student.id) unless exam.exam_scores.find_by_student_id(@student.id).nil?  
        end
        total = 0
    
        @exams.each do |exam|
          if exam.subject.no_exams or MultiSchool.current_school.id == 7
            next
          end 
      
          this_exam_score = 0
          unless exam_score.blank?
            exam_score.each do |es|
              if es.exam.subject.id.to_i == exam.subject.id.to_i
                this_exam_score = this_exam_score+1
                if es.grading_level.blank? or es.grading_level.name == "F"
                  off_rank = true
                else
            
                end
                total = total+1
              end  
            end  
          end
          subjectdata = Subject.find(exam.subject.id.to_i)
          has_exam_student = true
          check_std_subject = false
          if @std_subject_hash.include?(student.id.to_s+"_"+exam.subject.id.to_s)
            check_std_subject = true
          end

          if check_std_subject == false and subjectdata.elective_group_id.to_i != 0
            has_exam_student = false 
          end
          if this_exam_score == 0 and has_exam_student == true
            off_rank = true
          end  
        end 
        if total == 0
          off_rank = true
        end  
        if off_rank == false
          @students << student
        end
      end  
    end
    @main_student.each do |student|
      unless @students.include?(student)
        @students << student
      end
    end
    
    tmp_row = []
    tmp_row << "Roll"
    tmp_row << "Name"
    @exams.each do |exam|
      tmp_row << exam.subject.code
    end
    tmp_row << "Total"
    new_book.worksheet(0).insert_row(0, tmp_row)
    std_loop = 0
    @students.each do |student|
      std_loop = std_loop+1
      @student = student
      exam_score = []
      @exams.each do |exam| 
        exam_score.push exam.exam_scores.find_by_student_id(@student.id) unless exam.exam_scores.find_by_student_id(@student.id).nil?  
      end
      total = 0
      tmp_row = []
      tmp_row << student.class_roll_no
      tmp_row << student.full_name
      @exams.each do |exam|
        has_mark = false
        unless exam_score.blank?
          exam_score.each do |es|
            if es.exam.subject.id.to_i == exam.subject.id.to_i
              if exam.subject.no_exams
                tmp_row << es.remarks
              else
                tmp_row <<  es.marks.to_f
                total = total+es.marks.to_f
              end 
              has_mark = true
            end
          end
        end
        if has_mark == false
          tmp_row <<  ""
        end
      end
      tmp_row << total
      new_book.worksheet(0).insert_row(std_loop, tmp_row)
    end
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    
    send_data spreadsheet.string, :filename => "tabulation.xls", :type =>  "application/vnd.ms-excel"
    
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
      if @students.blank?
        @students_archive = ArchivedStudent.find_all_by_former_id(student_list)
        unless @students_archive.blank?
          @students_archive.each do |std|
            std.id = std.former_id
            @students << std
          end
        end
        
      end
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
    if MultiSchool.current_school.id != 325 and MultiSchool.current_school.id != 7 and MultiSchool.current_school.code != "baghc"
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

  def sems_connect_summary
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
    if @connect_exam_obj.result_type.to_i == 9
      sems_finding_data_2()
    elsif @connect_exam_obj.result_type.to_i == 13
      sems_finding_data_4()
    else
      sems_finding_data()
    end
    
    render :pdf => 'sems_connect_summary',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}} 
  end  

  def single_summary_report
    @subject_result = {}
    @summary_result = {}
    @total_students = 0
    @failed_student = 0

    @exam_group_main = ExamGroup.find(params[:exam_group])
    @exams_groups = [@exam_group_main]
    unless params[:class].blank?
      @batch = @exam_group_main.batch
      course = Course.find_by_id(@batch.course_id)
      courses = Course.find_all_by_course_name(course.course_name)
      batches = Batch.find_all_by_course_id_and_is_deleted(courses.map(&:id),false)
      @exams_groups = ExamGroup.find_all_by_batch_id_and_name(batches.map(&:id),@exam_group_main.name)
    end  
    @exams_groups.each do |exam_group|
      @exam_group = exam_group
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
        if @students.blank?
          @students_archive = ArchivedStudent.find_all_by_former_id(student_list)
          unless @students_archive.blank?
            @students_archive.each do |std|
              std.id = std.former_id
              @students << std
            end
          end    
        end    
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

        exam_score = []
        elective_groups_student = StudentsSubject.find_all_by_batch_id_and_student_id(@batch.id,@student.id)
        exam_subjects = []
        @exams.each do |exam|
          exam_score.push exam.exam_scores.find_by_student_id(@student.id) unless exam.exam_scores.find_by_student_id(@student.id).nil? 
          exam_subjects << exam.subject_id
        end  
        elective_group_mark = {}
        elective_group_mark_total = {}
        exam_score.each do |es|
          if es.marks.present?
            if elective_group_mark[es.exam.subject.elective_group_id].nil?
              elective_group_mark[es.exam.subject.elective_group_id] = (((es.marks.to_f*100)/es.exam.maximum_marks.to_f)*es.exam.subject.percentage.to_f)/100
              elective_group_mark_total[es.exam.subject.elective_group_id] = es.marks.to_f
            else
              if elective_group_mark[es.exam.subject.elective_group_id] == -1
                elective_group_mark[es.exam.subject.elective_group_id] = 0
                elective_group_mark_total[es.exam.subject.elective_group_id] = 0
              end 
              elective_group_mark[es.exam.subject.elective_group_id] = elective_group_mark[es.exam.subject.elective_group_id]+(((es.marks.to_f*100)/es.exam.maximum_marks.to_f)*es.exam.subject.percentage.to_f)/100
              elective_group_mark_total[es.exam.subject.elective_group_id] = elective_group_mark_total[es.exam.subject.elective_group_id]+es.marks.to_f
            end
          else
            if elective_group_mark[es.exam.subject.elective_group_id].nil?
              elective_group_mark[es.exam.subject.elective_group_id] = -1
            end  
          end     
        end 
        number_of_subject = 0
        total_credit = 0
        failed = false
        elective_group = []
        exam_score.each do |es|
          if !es.exam.subject.elective_group_id.blank? && !elective_group.include?(es.exam.subject.elective_group_id)
            elective_group << es.exam.subject.elective_group_id
            if elective_group_mark[es.exam.subject.elective_group_id] == -1
                next
            end  
            grade = GradingLevel.percentage_to_grade(elective_group_mark[es.exam.subject.elective_group_id].round(), @batch.id)
            if !grade.blank? and !grade.name.blank?
              es.exam.subject.elective_group.name.gsub! ' ', '-'
              if @subject_result[es.exam.subject.elective_group.name].blank?
                @subject_result[es.exam.subject.elective_group.name] = {}
              end  
              if @subject_result[es.exam.subject.elective_group.name][grade.name].blank?
                @subject_result[es.exam.subject.elective_group.name][grade.name] = 0
              end 
              total_credit = total_credit+grade.credit_points.to_f
              if grade.credit_points.to_f == 0
                failed = true
              end  
              number_of_subject = number_of_subject+1
              @subject_result[es.exam.subject.elective_group.name][grade.name] = @subject_result[es.exam.subject.elective_group.name][grade.name]+1 
            end  
          end  

        end  

        @total_students = @total_students+1

        @grade_name_main = "U"
        if failed == false && total_credit > 0
          grade_point_avg = total_credit.to_f/number_of_subject.to_f
          gradeObj = GradingLevel.grade_point_to_grade(grade_point_avg, @batch.id)
          if !gradeObj.blank? and !gradeObj.name.blank?
            if @summary_result[gradeObj.name].blank?
              @summary_result[gradeObj.name] = 0
            end 
            @summary_result[gradeObj.name] = @summary_result[gradeObj.name]+1 
          end  
        else
          @failed_student = @failed_student+1
        end  

      end
    end
    render :pdf => 'single_summary_report',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}  
  end 
  
  def mock_comparisom
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
      if @students.blank?
        @students_archive = ArchivedStudent.find_all_by_former_id(student_list)
        unless @students_archive.blank?
          @students_archive.each do |std|
            std.id = std.former_id
            @students << std
          end
        end
        
      end
      
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
    @exams2 = []
    @exams3 = []
    @all_exams = ExamGroup.find_by_exam_category_and_batch_id_and_is_deleted(@exam_group.exam_category,@exam_group.bathc_id,false)
    @subjects.each do |sub|
      iloop = 0
      @all_exams.each do |exam_group|
        iloop = iloop+1
        exam = Exam.find_by_exam_group_id_and_subject_id(exam_group.id,sub.id)
        if iloop == 1
          @exams.push exam unless exam.nil?
        elsif iloop == 2
          @exams2.push exam unless exam.nil?
        elsif iloop == 3  
          @exams3.push exam unless exam.nil?
        end  
      end
    end

    render :pdf => 'mock_comparisom',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :footer => {:html => { :template=> 'layouts/footer_single.html'}}

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
      if @students.blank?
        @students_archive = ArchivedStudent.find_all_by_former_id(student_list)
        unless @students_archive.blank?
          @students_archive.each do |std|
            std.id = std.former_id
            @students << std
          end
        end
        
      end
      
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
    if MultiSchool.current_school.id == 323 
      render :pdf => 'student_wise_generated_report_all',
        :orientation => 'Portrait', :zoom => 1.00,
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
        #:footer => {:html => { :template=> 'layouts/footer_single.html'}}
    else
      render :pdf => 'student_wise_generated_report_all',
        :orientation => 'Portrait', :zoom => 1.00,
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :footer => {:html => { :template=> 'layouts/footer_single.html'}}
    end
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
        @batch_dataå = Batch.find_by_course_id(course_id)
      else
        @batch_data = Batch.find_by_course_id_and_name(course_id, batch_name)
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
        @batch_data = Batch.find_by_course_id(course_id)
      else
        @batch_data = Batch.find_by_course_id_and_name(course_id, batch_name)
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
      @batch_data = Batch.find_by_course_id(course_id)
    else
      @batch_data = Batch.find_by_course_id_and_name(course_id, batch_name)
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
        @batch_data = Batch.find_by_course_id(course_id)
      else
        @batch_data = Batch.find_by_course_id_and_name(course_id, batch_name)
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
        @batch_data = Batch.find_by_course_id(course_id)
      else
        @batch_data = Batch.find_by_course_id_and_name(course_id, batch_name)
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
    @custom = false
    if @connect_exam_obj.result_type.to_i == 13 or @connect_exam_obj.result_type.to_i == 14 or @connect_exam_obj.result_type.to_i == 15 or @connect_exam_obj.result_type.to_i == 16 or @connect_exam_obj.result_type.to_i == 20
      finding_data_sagc_covid()
    elsif @connect_exam_obj.result_type.to_i == 17
      finding_data_sagc_25()
    elsif @connect_exam_obj.result_type.to_i == 18
      finding_data_sagc_18()
    elsif @connect_exam_obj.result_type.to_i == 21
      finding_data_sagc_21()
    elsif @connect_exam_obj.result_type.to_i == 19
      finding_data_19()
    elsif @connect_exam_obj.result_type.to_i == 9
      @custom = true
      group_course_ids = Course.find(:all, :conditions => "course_name = '#{@batch.course.course_name}' and `group` = '#{@batch.course.group}' and is_deleted = 0").map(&:id)
      group_batch_ids = Batch.find(:all, :conditions => "course_id IN (#{group_course_ids.join(",")}) and is_deleted = 0").map(&:id)
      
      qry = ""
      if @class.blank?
        qry = "connect_exam_id = #{@connect_exam_obj.id} and batch_id = #{@batch.id}"
      else
        qry = "batch_id IN (#{group_batch_ids.join(",")})"
      end
      exam_connect_merit_lists = ExamConnectMeritList.find(:first, :conditions=>"#{qry}") 
      unless exam_connect_merit_lists.blank?
        exam_connect_merit_lists = ExamConnectMeritList.find(:all, :conditions=>"#{qry}", :order=>"gpa DESC, marks DESC") 
        unless exam_connect_merit_lists.blank?
          i = 1
          exam_connect_merit_lists.each do |exam_connect_merit_list|
            pos = 0
            if exam_connect_merit_list.gpa.to_f > 0.0
              pos = i
              i = i + 1
            end
            exam_connect_merit_list.update_attributes(:position=>pos)
          end

          group_course_ids = Course.find(:all, :conditions => "course_name = '#{@batch.course.course_name}' and `group` = '#{@batch.course.group}' and is_deleted = 0").map(&:id)
          group_batch_ids = Batch.find(:all, :conditions => "course_id IN (#{group_course_ids.join(",")}) and is_deleted = 0").map(&:id)
          connect_exam_ids = ExamConnect.find(:all, :conditions=> "batch_id IN (#{group_batch_ids.join(",")}) and is_deleted = 0").map(&:id)

          exam_connect_merit_lists = ExamConnectMeritList.find(:all, :conditions=> "connect_exam_id IN (#{connect_exam_ids.join(",")}) AND batch_id IN (#{group_batch_ids.join(",")})", :order=>"gpa DESC, marks DESC") 
          unless exam_connect_merit_lists.blank?
            i = 1
            exam_connect_merit_lists.each do |exam_connect_merit_list|
              pos = 0
              if exam_connect_merit_list.gpa.to_f > 0.0
                pos = i
                i = i + 1
              end
              exam_connect_merit_list.update_attributes(:section_position=>pos)
            end
          end
        end
      end
      
      
      @exam_connect_merit_lists = ExamConnectMeritList.find(:all, :conditions=>"#{qry}", :order => 'gpa DESC, marks DESC, position ASC')
      @subject_code = [];
      @subject_passed = {};
      @subject_failed = {};
      @subject_appeard = {};
      @subject_absent = {};
      i = 0
      unless @exam_connect_merit_lists.blank?
        @exam_connect_merit_lists.each do |exam_connect_merit_list|
          subject_pass_failed = exam_connect_merit_list.subject_pass_failed.split(",")
            unless subject_pass_failed.blank?
              subject_pass_failed.each do |subject_pass_failed_single|
                subject_pass = subject_pass_failed_single.split('-')
                unless subject_pass.blank?
                  if subject_pass[0] == "pass"
                    code = subject_pass[1]
                    unless  @subject_code.include?(code)
                      @subject_code[i] = code
                      i = i + 1
                    end
                    if @subject_passed[code].blank?
                      @subject_passed[code] = 1
                    else
                      @subject_passed[code] = @subject_passed[code] + 1
                    end
                  end
                  
                  if subject_pass[0] == "fail"
                    code = subject_pass[1]
                    unless  @subject_code.include?(code)
                      @subject_code[i] = code
                      i = i + 1
                    end
                    if @subject_failed[code].blank?
                      @subject_failed[code] = 1
                    else
                      @subject_failed[code] = @subject_failed[code] + 1
                    end
                  end

                  if subject_pass[0] == "appear"
                    code = subject_pass[1]
                    unless  @subject_code.include?(code)
                      @subject_code[i] = code
                      i = i + 1
                    end
                    if @subject_appeard[code].blank?
                      @subject_appeard[code] = 1
                    else
                      @subject_appeard[code] = @subject_appeard[code] + 1
                    end
                  end

                  if subject_pass[0] == "absent"
                    code = subject_pass[1]
                    unless  @subject_code.include?(code)
                      @subject_code[i] = code
                      i = i + 1
                    end
                    if @subject_absent[code].blank?
                      @subject_absent[code] = 1
                    else
                      @subject_absent[code] = @subject_absent[code] + 1
                    end
                  end
                end
              end
            end
        end
      end
      @subjects = Subject.find(:all, :conditions=> "id IN (#{@subject_code.join(',')}) and is_deleted = 0", :group => "name",:order=>"priority asc")
      unless @class.blank?
        @subjects_all = Subject.find(:all, :conditions=> "id IN (#{@subject_code.join(',')}) and is_deleted = 0",:order=>"priority asc")
      end
      
    else
      finding_data5()
    end
    render :pdf => 'subject_wise_pass_failed',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 30,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
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
    if @connect_exam_obj.result_type.to_i == 13 or @connect_exam_obj.result_type.to_i == 14 or @connect_exam_obj.result_type.to_i == 15 or @connect_exam_obj.result_type.to_i == 16 or @connect_exam_obj.result_type.to_i == 20
      finding_data_sagc_covid()
    elsif @connect_exam_obj.result_type.to_i == 17
      finding_data_sagc_25()
    elsif @connect_exam_obj.result_type.to_i == 18
      finding_data_sagc_18()
    elsif @connect_exam_obj.result_type.to_i == 21
      finding_data_sagc_21()
    elsif @connect_exam_obj.result_type.to_i == 19
      finding_data_19()
    elsif @connect_exam_obj.result_type.to_i == 27
      finding_data_27()
    else
      finding_data5()
    end
    render :pdf => 'summary_report',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 30,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
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
    # @connect_exam_obj.result_type.to_i == 11 or 
    @class = params[:class]
    if @connect_exam_obj.result_type.to_i == 13 or @connect_exam_obj.result_type.to_i == 14 or @connect_exam_obj.result_type.to_i == 15 or @connect_exam_obj.result_type.to_i == 16 or @connect_exam_obj.result_type.to_i == 20
      finding_data_sagc_covid()
    elsif @connect_exam_obj.result_type.to_i == 17
      finding_data_sagc_25()
    elsif @connect_exam_obj.result_type.to_i == 18
      finding_data_sagc_18()
    elsif @connect_exam_obj.result_type.to_i == 21
      finding_data_sagc_21()
    elsif @connect_exam_obj.result_type.to_i == 19
      finding_data_19()
    elsif @connect_exam_obj.result_type.to_i == 20
      finding_data_27()
    elsif @connect_exam_obj.result_type.to_i == 9
      group_course_ids = Course.find(:all, :conditions => "course_name = '#{@batch.course.course_name}' and `group` = '#{@batch.course.group}' and is_deleted = 0").map(&:id)
      group_batch_ids = Batch.find(:all, :conditions => "course_id IN (#{group_course_ids.join(",")}) and is_deleted = 0").map(&:id)
      
      qry = ""
      if @class.blank?
        qry = "connect_exam_id = #{@connect_exam_obj.id} and batch_id = #{@batch.id}"
      else
        qry = "batch_id IN (#{group_batch_ids.join(",")})"
      end
      exam_connect_merit_lists = ExamConnectMeritList.find(:first, :conditions=>"#{qry}") 
      unless exam_connect_merit_lists.blank?
        exam_connect_merit_lists = ExamConnectMeritList.find(:all, :conditions=>"#{qry}", :order=>"gpa DESC, marks DESC") 
        unless exam_connect_merit_lists.blank?
          i = 1
          exam_connect_merit_lists.each do |exam_connect_merit_list|
            pos = 0
            if exam_connect_merit_list.gpa.to_f > 0.0
              pos = i
              i = i + 1
            end
            exam_connect_merit_list.update_attributes(:position=>pos)
          end

          group_course_ids = Course.find(:all, :conditions => "course_name = '#{@batch.course.course_name}' and `group` = '#{@batch.course.group}' and is_deleted = 0").map(&:id)
          group_batch_ids = Batch.find(:all, :conditions => "course_id IN (#{group_course_ids.join(",")}) and is_deleted = 0").map(&:id)
          connect_exam_ids = ExamConnect.find(:all, :conditions=> "batch_id IN (#{group_batch_ids.join(",")}) and is_deleted = 0").map(&:id)

          exam_connect_merit_lists = ExamConnectMeritList.find(:all, :conditions=> "connect_exam_id IN (#{connect_exam_ids.join(",")}) AND batch_id IN (#{group_batch_ids.join(",")})", :order=>"gpa DESC, marks DESC") 
          unless exam_connect_merit_lists.blank?
            i = 1
            exam_connect_merit_lists.each do |exam_connect_merit_list|
              pos = 0
              if exam_connect_merit_list.gpa.to_f > 0.0
                pos = i
                i = i + 1
              end
              exam_connect_merit_list.update_attributes(:section_position=>pos)
            end
          end
        end
      end
      
      
      @exam_connect_merit_lists = ExamConnectMeritList.find(:all, :conditions=>"#{qry}", :order => 'gpa DESC, marks DESC, position ASC')
    else
      finding_data5()
    end
    
    
 
    @std_resutl = []
   
    iloop = 0
    
    if !@student_position.blank? and !@student_position_batch.blank? and @student_position_first_term.blank?
      @student_position_first_term = @student_position
    elsif !@student_position_second_term.blank?  and !@student_position_second_term_batch.blank? and @student_position_first_term.blank?
      @student_position_first_term = @student_position_second_term
    end      
    
    if !@student_result.blank?
      @student_result.each do |std_result|
        
        position = 500000
       
        if !@student_position_first_term.blank? && !@student_position_first_term[std_result['id'].to_i].blank?
          position = @student_position_first_term[std_result['id'].to_i]
        else
          unless std_result['subject_failed'].blank?
            position = position+(std_result['subject_failed'].count*1000000)-(std_result['gpa'].to_f*500)-std_result['grand_total_with_fraction'].to_f
          end
        end  
        @student_result[iloop]['position'] = position
        iloop = iloop+1
      end
      @student_result.sort! { |x, y| x["position"] <=> y["position"] }
    end
    if @connect_exam_obj.result_type.to_i == 9
      render :pdf => 'merit_list_sagc_9',
        :orientation => 'Portrait', :zoom => 1.00,
        :margin => {    :top=> 52,
        :bottom => 30,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/header_sagc.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    else  
      render :pdf => 'merit_list_sagc',
        :orientation => 'Portrait', :zoom => 1.00,
        :margin => {    :top=> 52,
        :bottom => 30,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/header_sagc.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    end
  end
  
  def subject_wise_fourty_percent
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @subject = Subject.find_by_id(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    
    if @tabulation_data.nil?
      student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id,true)
      @tabulation_data = []
      if student_response['status']['code'].to_i == 200
        @tabulation_data = student_response['data']
      end
    end
    @class = params[:class]
    if @connect_exam_obj.result_type.to_i == 13 or @connect_exam_obj.result_type.to_i == 14 or @connect_exam_obj.result_type.to_i == 15 or @connect_exam_obj.result_type.to_i == 16 or @connect_exam_obj.result_type.to_i == 20
      finding_data_sagc_covid()
    elsif @connect_exam_obj.result_type.to_i == 17
      finding_data_sagc_25()
    elsif @connect_exam_obj.result_type.to_i == 18
      finding_data_sagc_18()
    elsif @connect_exam_obj.result_type.to_i == 21
      finding_data_sagc_21()
    elsif @connect_exam_obj.result_type.to_i == 19
      finding_data_19()
    else
      finding_data5()
    end
    render :pdf => 'subject_wise_pass_failed',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 30,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_footer_sagc.html'}}
  end
  
  
  def tabulation_excell_sjis
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'tabulation'
    @id = params[:id]
    @connect_exam_obj = ExamConnect.find_by_id(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id)
    center_align_format = Spreadsheet::Format.new :horizontal_align => :center,  :vertical_align => :middle,:left=>:thin,:right=>:thin,:top=>:thin,:bottom=>:thin
    @report_data = []
    if student_response['status']['code'].to_i == 200
      @report_data = student_response['data']
    end
    if !@report_data.blank?
      
      row_first = ['Roll','Students Name']
      j = 2
      @report_data['report']['subjects'].each do |sub|
        
        row_first << sub['code']
        row_first << ""
        new_book.worksheet(0).merge_cells(2,j,2,j+1)
        j =  j+2
      end
      @report_data['report']['no_exam_subject_resutl'].each do |sub|
        j =  j+1
        row_first << sub['code']
      end
      row_first << "Total"
      if MultiSchool.current_school.id == 280
        row_first << "Raniking"
      end  
      i = 2
      new_book.worksheet(0).insert_row(2, row_first)
      sheet1.row(2).default_format = center_align_format
      rank = 0
      @report_data['report']['students'].each do |std|
        i = i+1
        row = []
        row << std['class_roll_no']
        if !std['first_name'].blank? and !std['last_name'].blank?
          row << std['first_name']+" "+std['last_name']
        elsif !std['first_name'].blank?
          row << std['first_name']
        end  
        rank = rank+1
        total_mark = 0
        @report_data['report']['subjects'].each do |sub|
          subject_full_mark = 0 
          subject_obtain_mark = 0 
          subject_full_mark_ct1 = 0 
          subject_obtain_mark_ct1 = 0 
          subject_full_mark_ct2 = 0 
          subject_obtain_mark_ct2 = 0 
          term = 1
          @report_data['report']['exams'].each do |rs|
            if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
              if rs['exam_category'] == '1' && term == 1
                subject_full_mark_ct1 = subject_full_mark_ct1+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                subject_obtain_mark_ct1 = subject_obtain_mark_ct1+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
              elsif rs['exam_category'] == '1' && term == 2
                subject_full_mark_ct2 = subject_full_mark_ct2+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                subject_obtain_mark_ct2 = subject_obtain_mark_ct2+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
              elsif term == 1
                subject_full_mark = subject_full_mark+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                subject_obtain_mark = subject_obtain_mark+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                subject_obtain_mark = subject_obtain_mark.round()
                term = 2
              else
                subject_full_mark = subject_full_mark+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                subject_obtain_mark = subject_obtain_mark+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                #subject_obtain_mark = subject_obtain_mark.round()
              end
            end
          end
          
          if subject_full_mark_ct1 > 0
            if subject_full_mark_ct1 > 20 
              subject_full_mark = subject_full_mark+30 
              subjects_marks_ct_converted = (subject_obtain_mark_ct1/subject_full_mark_ct1)*30 
              subject_obtain_mark = subject_obtain_mark+subjects_marks_ct_converted 
            else 
              subject_full_mark = subject_full_mark+subject_full_mark_ct1 
              subjects_marks_ct_converted = subject_obtain_mark_ct1 
              subject_obtain_mark = subject_obtain_mark+subjects_marks_ct_converted 

            end 
          end 
          if subject_full_mark_ct2 > 0  
            if subject_full_mark_ct2 > 20 
              subject_full_mark = subject_full_mark+30 
              subjects_marks_ct_converted = (subject_obtain_mark_ct2/subject_full_mark_ct2)*30 
              subject_obtain_mark = subject_obtain_mark+subjects_marks_ct_converted 
            else 
              subject_full_mark = subject_full_mark+subject_full_mark_ct2 
              subjects_marks_ct_converted = subject_obtain_mark_ct2 
              subject_obtain_mark = subject_obtain_mark+subjects_marks_ct_converted 
            end 
          end 
          subject_obtain_mark = subject_obtain_mark.round() 


          main_mark = 0 
          if subject_obtain_mark.to_i > 0 and subject_full_mark.to_i > 0 
            main_mark = (subject_obtain_mark.to_f/subject_full_mark.to_f)*100   
          end 
          total_mark = total_mark+subject_obtain_mark.to_f
          grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
          row << subject_obtain_mark.to_s
          if !grade.blank? and !grade.name.blank?
            row << grade.name
          else
            row << ""
          end   
          
          
        end
        @report_data['report']['no_exam_subject_resutl'].each do |sub|
          if !sub['subject_comment'][std['id']].nil? && !sub['subject_comment'][std['id']].blank?
            row << sub['subject_comment'][std['id']]
          else
            row << ""
          end  
        end
        row << total_mark
        if MultiSchool.current_school.id == 280
          row << rank
        end
        new_book.worksheet(0).insert_row(i, row)
        sheet1.row(i).default_format = center_align_format
        
      end
    end
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => @batch.full_name + "-" + @connect_exam_obj.name + ".xls", :type =>  "application/vnd.ms-excel"
  end
  
  def sis_report_excell
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'marksheet'
    
    @id = params[:id]
    @subject_id = params[:subject_id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id) 
    @subject = Subject.find(@subject_id)
    @assigned_employee=@batch.all_class_teacher
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
    
    mock_1_count = 0
    @report_data['result']['ALL'].each do |rs|
      if rs['exam_category'] != "7"
        if rs['quarter'] == '1'
          mock_1_count = mock_1_count+1
        end
      end
    end
    
    center_align_format = Spreadsheet::Format.new :horizontal_align => :center,  :vertical_align => :middle,:left=>:thin,:right=>:thin,:top=>:thin,:bottom=>:thin
    row = [@subject.name]
    new_book.worksheet(0).insert_row(0, row)
    row = [@batch.full_name]
    new_book.worksheet(0).insert_row(1, row)
    
    if mock_1_count == 2
      new_book.worksheet(0).merge_cells(0,0,0,11)
      new_book.worksheet(0).merge_cells(1,0,1,11)
    else
      new_book.worksheet(0).merge_cells(0,0,0,7)
      new_book.worksheet(0).merge_cells(1,0,1,7)
    end   
    
    sheet1.row(0).default_format = center_align_format
    sheet1.row(1).default_format = center_align_format
    
    
    if mock_1_count == 2
      row_first = ['Sr No.','Name of Student','Mock 1','','Mock 2','','Mock 3','','Mock 4','','Best Mark','Grade']
      row_second = ['','','p1','p2','p1','p2','p1','p2','p1','p2','','']
    else
      row_first = ['Sr No.','Name of Student','Mock 1','Mock 2','Mock 3','Mock 4','Best Mark','Grade']
    end   
    row_third = ['','']
    @report_data['result']['ALL'].each do |rs|
      if rs['exam_category'] != "7"
        row_third << rs['maximum_marks'].to_i
      end
    end
    if mock_1_count == 2
      if row_third.count < 10
        if row_third.count == 6
          row_third << "-"
          row_third << "-"
          row_third << "-"
          row_third << "-"
        end
        if row_third.count == 8
          row_third << "-"
          row_third << "-"
        end
      end
    else
      if row_third.count < 6
        if row_third.count == 3
          row_third << "-"
          row_third << "-"
          row_third << "-"
        end
        if row_third.count == 4
          row_third << "-"
          row_third << "-"
        end
        if row_third.count == 5
          row_third << "-"
        end
      end
    end   
    row_third << "100"
    row_third << ""
    
    
    new_book.worksheet(0).insert_row(2, row_first)
    if mock_1_count == 2
      new_book.worksheet(0).insert_row(3, row_second)
      new_book.worksheet(0).insert_row(4, row_third)
    else
      new_book.worksheet(0).insert_row(3, row_third)
    end  
    
    
    if mock_1_count == 2
      new_book.worksheet(0).merge_cells(2,0,4,0)
      new_book.worksheet(0).merge_cells(2,1,4,1)
    else
      new_book.worksheet(0).merge_cells(2,0,3,0)
      new_book.worksheet(0).merge_cells(2,1,3,1)
    end  
    
    if mock_1_count == 2
      new_book.worksheet(0).merge_cells(2,2,2,3)
      new_book.worksheet(0).merge_cells(2,4,2,5)
      new_book.worksheet(0).merge_cells(2,6,2,7)
      new_book.worksheet(0).merge_cells(2,8,2,9)
    end
    
    if mock_1_count == 2
      new_book.worksheet(0).merge_cells(2,10,3,10)
      new_book.worksheet(0).merge_cells(2,11,4,11)
    else
      new_book.worksheet(0).merge_cells(2,7,3,7)
    end  
    
    sheet1.row(2).default_format = center_align_format
    sheet1.row(3).default_format = center_align_format
    if mock_1_count == 2
      sheet1.row(4).default_format = center_align_format
    end
    
    
    iloop = 0
    kloop = 3
    if mock_1_count == 2
      kloop = 4
    end
    
    std_list_sorted = []
    
    unless @report_data['result']['al_students'].blank?
      @report_data['result']['al_students'].each do |std|
        mock1 = 0
        mock2 = 0
        mock3 = 0
        mock4 = 0
        mock1_full = 0
        mock2_full = 0
        mock3_full = 0
        mock4_full = 0
        iloop = iloop+1
        kloop = kloop+1
        rows = [iloop,std['name']]
        
        @report_data['result']['ALL'].each do |rs|
          if !rs['students'].blank? && !rs['students'][std['id']].blank? && !rs['students'][std['id']]['score'].blank?
            rows << rs['students'][std['id']]['score'].to_i
            if rs['quarter'] == '1' 
              mock1 = mock1.to_f+rs['students'][std['id']]['score'].to_f
            end
            if rs['quarter'] == '2' 
              mock2 = mock2.to_f+rs['students'][std['id']]['score'].to_f
            end
            if rs['quarter'] == '3' 
              mock3 = mock3.to_f+rs['students'][std['id']]['score'].to_f
            end
            if rs['quarter'] == '4' 
              mock4 = mock4.to_f+rs['students'][std['id']]['score'].to_f
            end
          else
            rows << ""
          end
          
          if !rs['maximum_marks'].blank?
            if rs['quarter'] == '1' 
              mock1_full = mock1_full.to_f+rs['maximum_marks'].to_i
            end
            if rs['quarter'] == '2' 
              mock2_full = mock2_full.to_f+rs['maximum_marks'].to_i
            end
            if rs['quarter'] == '3' 
              mock3_full = mock3_full.to_f+rs['maximum_marks'].to_i
            end 
            if rs['quarter'] == '4' 
              mock4_full = mock4_full.to_f+rs['maximum_marks'].to_i
            end 
          end
        end
        
        if mock_1_count == 2
          if rows.count < 10
            if rows.count == 6
              rows << "-"
              rows << "-"
              rows << "-"
              rows << "-"
            end
            if rows.count == 8
              rows << "-"
              rows << "-"
            end
          end
        else
          if rows.count < 6
            if rows.count == 3
              rows << "-"
              rows << "-"
              rows << "-"
            end
            if rows.count == 4
              rows << "-"
              rows << "-"
            end
            if rows.count == 5
              rows << "-"
            end
          end
          
        end  
        
        
        best_mark = 0

        if mock1 > 0
          avg = (mock1.to_f/mock1_full.to_f)*100
          avg = avg.round()
          if avg > best_mark
            best_mark = avg
          end  
        end  
        if mock2 > 0
          avg = (mock2.to_f/mock2_full.to_f)*100
          avg = avg.round()
          if avg > best_mark
            best_mark = avg
          end  
        end 
        if mock3 > 0
          avg = (mock3.to_f/mock3_full.to_f)*100
          avg = avg.round()
          if avg > best_mark
            best_mark = avg
          end  
        end 
        if mock4 > 0
          avg = (mock4.to_f/mock4_full.to_f)*100
          avg = avg.round()
          if avg > best_mark
            best_mark = avg
          end  
        end
        if best_mark > 0
          rows << best_mark.round()
          std['best_mark'] = best_mark
          std_list_sorted << std
        else
          rows << ""
        end
        
        
        
        grade = SubjectGradingLevel.percentage_to_grade(best_mark, @batch.id, @subject.id.to_i)
        if !grade.blank? and !grade.name.blank? and best_mark > 0
          rows << grade.name
        else
          rows << ""
        end   
        new_book.worksheet(0).insert_row(kloop, rows)
        sheet1.row(kloop).default_format = center_align_format
      end
    end
    
    kloop = kloop+3
    
    unless std_list_sorted.blank?
      std_list_sorted.sort! { |a, b|  b['best_mark'].to_i <=> a['best_mark'].to_i }
      row = [@subject.name]
      new_book.worksheet(0).insert_row(kloop, row)
      new_book.worksheet(0).merge_cells(kloop,0,kloop,3)
      sheet1.row(kloop).default_format = center_align_format
      row = [@batch.full_name]
      kloop = kloop+1
      new_book.worksheet(0).insert_row(kloop, row)
      new_book.worksheet(0).merge_cells(kloop,0,kloop,3)
      sheet1.row(kloop).default_format = center_align_format
      row_first = ['Best Mark','Name of Student','Rank','']
      kloop = kloop+1
      new_book.worksheet(0).insert_row(kloop, row_first)
      new_book.worksheet(0).merge_cells(kloop,2,kloop,3)
      sheet1.row(kloop).default_format = center_align_format
      staring_row = kloop+1
      starting_grade = ""
      rank = 0
     
      std_list_sorted.each do |std|
        kloop = kloop+1
        grade = SubjectGradingLevel.percentage_to_grade(std['best_mark'].to_i, @batch.id, @subject.id.to_i)
        grade_name = "" 
        if !grade.blank? and !grade.name.blank?
          grade_name = grade.name
        end
        unless grade_name.blank?
          if starting_grade != "" and starting_grade != grade_name
            starting_grade = grade_name
            rank = 0
            end_kloop = kloop-1
            new_book.worksheet(0).merge_cells(staring_row,3,end_kloop,3)
            staring_row = kloop
          end
          rank = rank+1
          rows = [std['best_mark'],std['name'],rank,grade_name]
          
          if starting_grade == ""
            starting_grade = grade_name
          end
          
          new_book.worksheet(0).insert_row(kloop, rows)
          sheet1.row(kloop).default_format = center_align_format
        
        end  
      end
      end_kloop = kloop
      new_book.worksheet(0).merge_cells(staring_row,3,end_kloop,3)
    end  
    
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => @batch.full_name + "-" + @subject.name + ".xls", :type =>  "application/vnd.ms-excel"
  end


  def tabulation_excell_sems5
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'tabulation'
    @id = params[:id]
    @connect_exam_obj = ExamConnect.find_by_id(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    get_continues(@id,@batch.id)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
    
    if @tabulation_data.nil?
      student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id,true)
      @tabulation_data = []
      if student_response['status']['code'].to_i == 200
        @tabulation_data = student_response['data']
      end
    end
    finding_data_sems5()
    
    
    if !@report_data.blank?
      
      row_first = ['ID','Name','Class','House','Total Mark','Obtain Mark','Promotion Status','Merit Position','Prev Roll','New Roll','New Section']
      new_book.worksheet(0).insert_row(0, row_first)
      
      @all_subject_exam = @report_data['report']['subjects']
      @all_student_subject = StudentsSubject.find_all_by_batch_id(@batch.id)
      @total_std_in_class = @report_data['report']['students'].count
      iloop = 0
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
      
          if !@report_data['report']['all_result'][std['id']]['exams'].blank?
            @report_data['report']['exams'] = @report_data['report']['all_result'][std['id']]['exams']
            if !@report_data['present_all'].blank? and !@report_data['present_all'][std['id']].blank? and 
                @report_data['present'] = @report_data['present_all'][std['id']]
            end 

            if !@report_data['absent_all'].blank? and !@report_data['absent_all'][std['id']].blank?
              @report_data['absent'] = @report_data['absent_all'][std['id']]
            end 

            if !@report_data['first_term_total'].blank?
              @report_data['total_first_term'] = @report_data['first_term_total']
            end 
            if !@report_data['first_term_present_all'].blank? and !@report_data['first_term_present_all'][std['id']].blank?
              @report_data['present_first_term'] = @report_data['first_term_present_all'][std['id']]
            end
            if !@report_data['first_term_absent_all'].blank? and !@report_data['first_term_absent_all'][std['id']].blank?
              @report_data['absent_first_term'] = @report_data['first_term_absent_all'][std['id']]
            end
            if !@report_data['first_term_total_new'].blank? and !@report_data['first_term_total_new'][std['id']].blank?
              @report_data['total_new_std_first_term'] = @report_data['first_term_total_new'][std['id']]
              @report_data['total_first_term'] = @report_data['first_term_total_new'][std['id']]
            end
            iloop = iloop+1
            row = []
            row << @student.admission_no
            row << @student.full_name
            row << @batch.full_name
            ad_details = StudentAdditionalDetail.find_by_student_id_and_additional_field_id(@student.id,14)
            unless ad_details.blank?
              row << ad_details.additional_info
            else
              row << ""
            end
            grand_total_mark = 0
            gradn_obtain_mark = 0
            
            
            
            assessment = 0 
            hw = 0 
            cw = 0 
            total_mark = 0 
            total_subject = 0
            unless @report_data['report']['subjects'].blank? 
              @report_data['report']['subjects'].each do |subjects|
                if subjects['grade_subject'].to_i == 1
                  next
                end

                subjectdata = Subject.find(subjects['id'].to_i)

                has_exam = false
                loop=0
                unless @report_data['report']['exams'].blank?
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] != '6' 
                      next
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                      has_exam = true
                      break
                    end
                  end
                end
                if has_exam == false
                  next
                end
                sub_assessment = 0
                sub_hw = 0
                sub_cw = 0
                sub_hw_full_mark = 0
                sub_cw_full_mark = 0
                total_subject = total_subject+1
                class_test = []

                unless @report_data['report']['exams'].blank?  
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] != '6' 
                      next
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?

                      if report['exam_category'] == '7'
                        sub_hw = sub_hw.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end
                      if report['exam_category'] == '8'
                        sub_cw = sub_cw.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['full_mark'].blank?

                      if report['exam_category'] == '7'
                        sub_hw_full_mark = sub_hw_full_mark+report['result'][report['exam_id']][subjects['id']]['full_mark'].to_i
                      end
                      if report['exam_category'] == '8'
                        sub_cw_full_mark = sub_cw_full_mark+report['result'][report['exam_id']][subjects['id']]['full_mark'].to_i
                      end
                    end 
                  end
                end

                unless @report_data['report']['exams'].blank?  
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] != '6' 
                      next
                    end
                    if report['exam_category'] == '6'
                      if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                        class_test << report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                        sub_assessment = sub_assessment.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      else
                        class_test << 0
                      end

                    end
                  end
                end


                hw_avg = 0
                assessment_avg = 0
                cw_avg = 0

                if sub_assessment > 0
                  class_test.sort! {|x,y| y <=> x }
                  ass_total_mark = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                  assessment_avg = (ass_total_mark.to_f/30.00)*50
                  assessment_avg = assessment_avg.round()
                  assessment = assessment+assessment_avg
                end
                if sub_hw > 0
                  hw_avg = (sub_hw.to_f/sub_hw_full_mark.to_f)*30
                  hw_avg = hw_avg.round()
                  hw = hw+hw_avg
                end
                if sub_cw > 0
                  cw_avg = (sub_cw.to_f/sub_cw_full_mark.to_f)*10
                  cw_avg = cw_avg.round()
                  cw = cw+cw_avg
                end  
              end  
            end
            ag_hw = 0
            ag_assessment = 0
            ag_cw = 0

            if total_subject > 0 && 
                if assessment > 0
                ag_assessment = assessment.to_f/total_subject.to_f
                ag_assessment = sprintf( "%0.01f", ag_assessment)
              end
              if hw > 0
                ag_hw = hw.to_f/total_subject.to_f
                ag_hw = sprintf( "%0.01f", ag_hw)
              end
              if cw > 0
                ag_cw = cw.to_f/total_subject.to_f
                ag_cw = sprintf( "%0.01f", ag_cw)
              end
            end
            total_mark = total_mark+ag_assessment.to_f+ag_hw.to_f+ag_cw.to_f

            unless @report_data['report']['subjects'].blank? 
              finish = false
              total_mark_life = 0
              start = true
              sub = 0
              @report_data['report']['subjects'].each do |subjects|
                if subjects['grade_subject'].to_i != 1
                  next
                end

                sub = sub+1
                sub_mark = 0
                unless @report_data['report']['exams'].blank?  
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] != '6' 
                      next
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                      if report['exam_category'] == '6'
                        sub_mark = sub_mark.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end 
                    end
                  end
                end   
                sub_mark = sprintf( "%0.01f", sub_mark)
                total_mark_life = total_mark_life+sub_mark.to_f
              end
              total_mark_life = sprintf( "%0.01f", total_mark_life)
              total_mark = total_mark+total_mark_life.to_f
            end

           

            total_mark = sprintf( "%0.01f", total_mark)  
            total_mark_final_term = total_mark 
            
            
            
            assessment = 0 
            hw = 0 
            cw = 0 
            total_mark = 0 
            total_subject = 0
            unless @report_data['report']['subjects'].blank? 
              @report_data['report']['subjects'].each do |subjects|
                if subjects['grade_subject'].to_i == 1
                  next
                end

                subjectdata = Subject.find(subjects['id'].to_i)

                has_exam = false
                loop=0
                unless @report_data['report']['exams'].blank?
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] == '6' 
                      next
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                      has_exam = true
                      break
                    end
                  end
                end
                if has_exam == false
                  next
                end
                sub_assessment = 0
                sub_hw = 0
                sub_cw = 0
                sub_hw_full_mark = 0
                sub_cw_full_mark = 0
                total_subject = total_subject+1
                class_test = []

                unless @report_data['report']['exams'].blank?  
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] == '6' 
                      next
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?

                      if report['exam_category'] == '7'
                        sub_hw = sub_hw.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end
                      if report['exam_category'] == '8'
                        sub_cw = sub_cw.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['full_mark'].blank?

                      if report['exam_category'] == '7'
                        sub_hw_full_mark = sub_hw_full_mark+report['result'][report['exam_id']][subjects['id']]['full_mark'].to_i
                      end
                      if report['exam_category'] == '8'
                        sub_cw_full_mark = sub_cw_full_mark+report['result'][report['exam_id']][subjects['id']]['full_mark'].to_i
                      end
                    end 
                  end
                end

                unless @report_data['report']['exams'].blank?  
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] == '6' 
                      next
                    end
                    if report['exam_category'] == '6'
                      if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                        class_test << report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                        sub_assessment = sub_assessment.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      else
                        class_test << 0
                      end

                    end
                  end
                end


                hw_avg = 0
                assessment_avg = 0
                cw_avg = 0

                if sub_assessment > 0
                  class_test.sort! {|x,y| y <=> x }
                  ass_total_mark = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                  assessment_avg = (ass_total_mark.to_f/30.00)*50
                  assessment_avg = assessment_avg.round()
                  assessment = assessment+assessment_avg
                end
                if sub_hw > 0
                  hw_avg = (sub_hw.to_f/sub_hw_full_mark.to_f)*30
                  hw_avg = hw_avg.round()
                  hw = hw+hw_avg
                end
                if sub_cw > 0
                  cw_avg = (sub_cw.to_f/sub_cw_full_mark.to_f)*10
                  cw_avg = cw_avg.round()
                  cw = cw+cw_avg
                end  
              end  
            end
            ag_hw = 0
            ag_assessment = 0
            ag_cw = 0

            if total_subject > 0 && 
                if assessment > 0
                ag_assessment = assessment.to_f/total_subject.to_f
                ag_assessment = sprintf( "%0.01f", ag_assessment)
              end
              if hw > 0
                ag_hw = hw.to_f/total_subject.to_f
                ag_hw = sprintf( "%0.01f", ag_hw)
              end
              if cw > 0
                ag_cw = cw.to_f/total_subject.to_f
                ag_cw = sprintf( "%0.01f", ag_cw)
              end
            end
            total_mark = total_mark+ag_assessment.to_f+ag_hw.to_f+ag_cw.to_f

            unless @report_data['report']['subjects'].blank? 
              finish = false
              total_mark_life = 0
              start = true
              sub = 0
              @report_data['report']['subjects'].each do |subjects|
                if subjects['grade_subject'].to_i != 1
                  next
                end

                sub = sub+1
                sub_mark = 0
                unless @report_data['report']['exams'].blank?  
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] == '6' 
                      next
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                      if report['exam_category'] == '6'
                        sub_mark = sub_mark.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end 
                    end
                  end
                end   
                sub_mark = sprintf( "%0.01f", sub_mark)
                total_mark_life = total_mark_life+sub_mark.to_f
              end
              total_mark_life = sprintf( "%0.01f", total_mark_life)
              total_mark = total_mark+total_mark_life.to_f
            end

           
            total_mark = sprintf( "%0.01f", total_mark)
            total_mark_mid_term = total_mark
            total_30 = (total_mark_mid_term.to_f*30)/100
            total_70 = (total_mark_final_term.to_f*70)/100
            main_total = total_70+total_30
            main_total = sprintf( "%0.01f", main_total)
            main_total = main_total.to_f
            
            
            
            
            row << 100
            row << main_total
            unless @student_position[@student.id.to_i].blank?
              if @promoted_to!="0"
                row << "Promoted to STD- "+@promoted_to
              else
                row << "-"
              end 
              unless @student_position[@student.id.to_i].blank?
                row << @student_position[@student.id.to_i]
              else
                row << "-"
              end 
              row << @student.class_roll_no
              unless @student_roll[@student_real_position[@student.id.to_i].to_i].blank?
                row << @student_roll[@student_real_position[@student.id.to_i].to_i]
              else
                row << "-"
              end 
              unless @student_section[@student_real_position[@student.id.to_i].to_i].blank?
                row << @student_section[@student_real_position[@student.id.to_i].to_i]
              else
                row << "-"
              end 
            else
              row << "-"
              row << "-"
              row << @student.class_roll_no
              row << "-"
              row << "-"
            end  
            new_book.worksheet(0).insert_row(iloop, row)
          end
        end

      end
    end
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => @batch.full_name + "-" + @connect_exam_obj.name + ".xls", :type =>  "application/vnd.ms-excel"
  end

  def tabulation_excell_sems4
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'tabulation'
    @id = params[:id]
    @connect_exam_obj = ExamConnect.find_by_id(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    get_continues(@id,@batch.id)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
    
    if @tabulation_data.nil?
      student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id,true)
      @tabulation_data = []
      if student_response['status']['code'].to_i == 200
        @tabulation_data = student_response['data']
      end
    end
    finding_data_sems4()
    if !@report_data.blank?
      
      row_first = ['ID','Name','Class','House','Total Mark','Obtain Mark','Promotion Status','Merit Position','Prev Roll','New Roll','New Section']
      new_book.worksheet(0).insert_row(0, row_first)
      
      @all_subject_exam = @report_data['report']['subjects']
      @all_student_subject = StudentsSubject.find_all_by_batch_id(@batch.id)
      @total_std_in_class = @report_data['report']['students'].count
      iloop = 0
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
      
          if !@report_data['report']['all_result'][std['id']]['exams'].blank?
            @report_data['report']['exams'] = @report_data['report']['all_result'][std['id']]['exams']
            iloop = iloop+1
            row = []
            row << @student.admission_no
            row << @student.full_name
            row << @batch.full_name
            ad_details = StudentAdditionalDetail.find_by_student_id_and_additional_field_id(@student.id,14)
            unless ad_details.blank?
              row << ad_details.additional_info
            else
              row << ""
            end
            grand_total_mark = 0
            gradn_obtain_mark = 0
            unless @report_data['report']['subjects'].blank? 
              @report_data['report']['subjects'].each do |subjects|
                loop = 0
                
                
                total_mark = 0 
                full_mark = 22.5 
                class_test = []
                unless @report_data['report']['exams'].blank?   
                  @report_data['report']['exams'].each do |report| 
                    if report['exam_category'] != '1' or report['quarter'] != '6'  
                      next 
                    end 
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank? 
                      obt_mark = (report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f/report['result'][report['exam_id']][subjects['id']]['full_mark'].to_f)*7.5 
                      class_test << obt_mark.round(2) 
                    else 
                      class_test << 0 
                    end 
                  end 
                end 
                class_test.sort! {|x,y| y <=> x }  
                total_mark = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f 

                unless @report_data['report']['exams'].blank?   
                  @report_data['report']['exams'].each do |report| 
                    if report['exam_category'] == '1' or report['exam_category'] == '3' or report['quarter'] != '6'  
                      next 
                    end 

                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank? 
                      obt_mark = (report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f/report['result'][report['exam_id']][subjects['id']]['full_mark'].to_f)*2.5 
                      total_mark = total_mark.to_f+obt_mark.to_f 
                    end 
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['full_mark'].blank? 
                      full_mark = full_mark+2.5 
                    end
                  end 
                end 
                 
                

                unless @report_data['report']['exams'].blank?   
                  @report_data['report']['exams'].each do |report| 
                    if report['exam_category'] != '3' or report['quarter'] != '6'  
                      next 
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?  
                      marks_obtain = report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      total_mark = total_mark.to_f+marks_obtain.to_f 
                    end 
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['full_mark'].blank? 
                      new_full_mark = report['result'][report['exam_id']][subjects['id']]['full_mark'].to_f
                      full_mark = full_mark+new_full_mark 
                    end

                  end 
                end 
                total_mark = total_mark.round()
                
                midterm_total = 0
                midterm_30 = 0
                class_test = []
                unless @report_data['report']['exams'].blank?   
                  @report_data['report']['exams'].each do |report| 
                    if report['exam_category'] != '1' or report['quarter'] == '6'  
                      next 
                    end 
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank? 
                      obt_mark = (report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f/report['result'][report['exam_id']][subjects['id']]['full_mark'].to_f)*7.5 
                      class_test << obt_mark.round(2) 
                    else 
                      class_test << 0 
                    end 
                  end 
                end 
                class_test.sort! {|x,y| y <=> x }  
                midterm_total = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                unless @report_data['report']['exams'].blank?   
                  @report_data['report']['exams'].each do |report| 
                    if report['exam_category'] == '1' or report['exam_category'] == '3' or report['quarter'] == '6'  
                      next 
                    end 
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank? 
                      obt_mark = (report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f/report['result'][report['exam_id']][subjects['id']]['full_mark'].to_f)*2.5 
                      midterm_total = midterm_total.to_f+obt_mark.to_f 
                    end
                  end 
                end 
                

                unless @report_data['report']['exams'].blank?   
                  @report_data['report']['exams'].each do |report| 
                    if report['exam_category'] != '3' or report['quarter'] == '6'  
                      next 
                    end 
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank? 
                      obt_mark = report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f 
                      midterm_total = midterm_total.to_f+obt_mark.to_f 
                    end
                  end 
                end
                midterm_total = midterm_total.round()
                if midterm_total.to_f>0
                  midterm_30  = (midterm_total.to_f*30)/100
                end
                
                if total_mark.to_f>0 and full_mark.to_f>0
                  main_mark = (total_mark.to_f/full_mark.to_f)*100 
                  total_70 = (total_mark.to_f*70)/100
                else
                  main_mark = 0
                  total_70 = 0
                end
                accumulated = midterm_30+total_70
                accumulated = accumulated.round()
                grand_total_mark = grand_total_mark+100
                gradn_obtain_mark = gradn_obtain_mark+accumulated
              end
            end
            row << grand_total_mark
            row << gradn_obtain_mark
            unless @student_position[@student.id.to_i].blank?
              if @promoted_to!="0"
                row << "Promoted to STD- "+@promoted_to
              else
                row << "-"
              end 
              unless @student_position[@student.id.to_i].blank?
                row << @student_position[@student.id.to_i]
              else
                row << "-"
              end 
              row << @student.class_roll_no
              unless @student_roll[@student_real_position[@student.id.to_i].to_i].blank?
                row << @student_roll[@student_real_position[@student.id.to_i].to_i]
              else
                row << "-"
              end 
              unless @student_section[@student_real_position[@student.id.to_i].to_i].blank?
                row << @student_section[@student_real_position[@student.id.to_i].to_i]
              else
                row << "-"
              end 
            else
              row << "-"
              row << "-"
              row << @student.class_roll_no
              row << "-"
              row << "-"
            end 
            new_book.worksheet(0).insert_row(iloop, row)
          end
        end

      end
    end
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => @batch.full_name + "-" + @connect_exam_obj.name + ".xls", :type =>  "application/vnd.ms-excel"
  end
  
  
  def tabulation_excell_sems3
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'tabulation'
    @id = params[:id]
    @connect_exam_obj = ExamConnect.find_by_id(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    get_continues(@id,@batch.id)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
    
    if @tabulation_data.nil?
      student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id,true)
      @tabulation_data = []
      if student_response['status']['code'].to_i == 200
        @tabulation_data = student_response['data']
      end
    end
    finding_data_sems3()
    
    
    if !@report_data.blank?
      
      row_first = ['ID','Name','Class','House','Total Mark','Obtain Mark','Promotion Status','Merit Position','Prev Roll','New Roll','New Section']
      new_book.worksheet(0).insert_row(0, row_first)
      
      @all_subject_exam = @report_data['report']['subjects']
      @all_student_subject = StudentsSubject.find_all_by_batch_id(@batch.id)
      @total_std_in_class = @report_data['report']['students'].count
      iloop = 0
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
      
          if !@report_data['report']['all_result'][std['id']]['exams'].blank?
            @report_data['report']['exams'] = @report_data['report']['all_result'][std['id']]['exams']
            if !@report_data['present_all'].blank? and !@report_data['present_all'][std['id']].blank? and 
                @report_data['present'] = @report_data['present_all'][std['id']]
            end 

            if !@report_data['absent_all'].blank? and !@report_data['absent_all'][std['id']].blank?
              @report_data['absent'] = @report_data['absent_all'][std['id']]
            end 

            if !@report_data['first_term_total'].blank?
              @report_data['total_first_term'] = @report_data['first_term_total']
            end 
            if !@report_data['first_term_present_all'].blank? and !@report_data['first_term_present_all'][std['id']].blank?
              @report_data['present_first_term'] = @report_data['first_term_present_all'][std['id']]
            end
            if !@report_data['first_term_absent_all'].blank? and !@report_data['first_term_absent_all'][std['id']].blank?
              @report_data['absent_first_term'] = @report_data['first_term_absent_all'][std['id']]
            end
            if !@report_data['first_term_total_new'].blank? and !@report_data['first_term_total_new'][std['id']].blank?
              @report_data['total_new_std_first_term'] = @report_data['first_term_total_new'][std['id']]
              @report_data['total_first_term'] = @report_data['first_term_total_new'][std['id']]
            end
            iloop = iloop+1
            row = []
            row << @student.admission_no
            row << @student.full_name
            row << @batch.full_name
            ad_details = StudentAdditionalDetail.find_by_student_id_and_additional_field_id(@student.id,14)
            unless ad_details.blank?
              row << ad_details.additional_info
            else
              row << ""
            end
            grand_total_mark = 0
            gradn_obtain_mark = 0
            
            
            
            assessment = 0 
            hw = 0 
            cw = 0 
            total_mark = 0 
            total_subject = 0
            unless @report_data['report']['subjects'].blank? 
              @report_data['report']['subjects'].each do |subjects|
                if subjects['grade_subject'].to_i == 1
                  next
                end

                subjectdata = Subject.find(subjects['id'].to_i)

                has_exam = false
                loop=0
                unless @report_data['report']['exams'].blank?
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] != '6' 
                      next
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                      has_exam = true
                      break
                    end
                  end
                end
                if has_exam == false
                  next
                end
                sub_assessment = 0
                sub_hw = 0
                sub_cw = 0
                sub_hw_full_mark = 0
                sub_cw_full_mark = 0
                total_subject = total_subject+1
                class_test = []

                unless @report_data['report']['exams'].blank?  
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] != '6' 
                      next
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?

                      if report['exam_category'] == '7'
                        sub_hw = sub_hw.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end
                      if report['exam_category'] == '8'
                        sub_cw = sub_cw.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['full_mark'].blank?

                      if report['exam_category'] == '7'
                        sub_hw_full_mark = sub_hw_full_mark+report['result'][report['exam_id']][subjects['id']]['full_mark'].to_i
                      end
                      if report['exam_category'] == '8'
                        sub_cw_full_mark = sub_cw_full_mark+report['result'][report['exam_id']][subjects['id']]['full_mark'].to_i
                      end
                    end 
                  end
                end

                unless @report_data['report']['exams'].blank?  
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] != '6' 
                      next
                    end
                    if report['exam_category'] == '6'
                      if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                        class_test << report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                        sub_assessment = sub_assessment.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      else
                        class_test << 0
                      end

                    end
                  end
                end


                hw_avg = 0
                assessment_avg = 0
                cw_avg = 0

                if sub_assessment > 0
                  class_test.sort! {|x,y| y <=> x }
                  ass_total_mark = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                  assessment_avg = (ass_total_mark.to_f/30.00)*50
                  assessment_avg = assessment_avg.round()
                  assessment = assessment+assessment_avg
                end
                if sub_hw > 0
                  hw_avg = (sub_hw.to_f/sub_hw_full_mark.to_f)*20
                  hw_avg = hw_avg.round()
                  hw = hw+hw_avg
                end
                if sub_cw > 0
                  cw_avg = (sub_cw.to_f/sub_cw_full_mark.to_f)*10
                  cw_avg = cw_avg.round()
                  cw = cw+cw_avg
                end  
              end  
            end
            ag_hw = 0
            ag_assessment = 0
            ag_cw = 0

            if total_subject > 0 && 
                if assessment > 0
                ag_assessment = assessment.to_f/total_subject.to_f
                ag_assessment = sprintf( "%0.01f", ag_assessment)
              end
              if hw > 0
                ag_hw = hw.to_f/total_subject.to_f
                ag_hw = sprintf( "%0.01f", ag_hw)
              end
              if cw > 0
                ag_cw = cw.to_f/total_subject.to_f
                ag_cw = sprintf( "%0.01f", ag_cw)
              end
            end
            total_mark = total_mark+ag_assessment.to_f+ag_hw.to_f+ag_cw.to_f

            unless @report_data['report']['subjects'].blank? 
              finish = false
              total_mark_life = 0
              start = true
              sub = 0
              @report_data['report']['subjects'].each do |subjects|
                if subjects['grade_subject'].to_i != 1
                  next
                end

                sub = sub+1
                sub_mark = 0
                unless @report_data['report']['exams'].blank?  
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] != '6' 
                      next
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                      if report['exam_category'] == '6'
                        sub_mark = sub_mark.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end 
                    end
                  end
                end   
                sub_mark = sprintf( "%0.01f", sub_mark)
                total_mark_life = total_mark_life+sub_mark.to_f
              end
              total_mark_life = sprintf( "%0.01f", total_mark_life)
              total_mark = total_mark+total_mark_life.to_f
            end

            att_mark = 0
            unless @report_data['total'].blank?
              if @report_data['total'] > 0 && @report_data['present'] > 0
                att_mark = (@report_data['present'].to_f/@report_data['total'].to_f)*10
                att_mark = sprintf( "%0.01f", att_mark)
                total_mark = total_mark+att_mark.to_f
              end
            end

            total_mark = sprintf( "%0.01f", total_mark)  
            total_mark_final_term = total_mark 
            
            
            
            assessment = 0 
            hw = 0 
            cw = 0 
            total_mark = 0 
            total_subject = 0
            unless @report_data['report']['subjects'].blank? 
              @report_data['report']['subjects'].each do |subjects|
                if subjects['grade_subject'].to_i == 1
                  next
                end

                subjectdata = Subject.find(subjects['id'].to_i)

                has_exam = false
                loop=0
                unless @report_data['report']['exams'].blank?
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] == '6' 
                      next
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                      has_exam = true
                      break
                    end
                  end
                end
                if has_exam == false
                  next
                end
                sub_assessment = 0
                sub_hw = 0
                sub_cw = 0
                sub_hw_full_mark = 0
                sub_cw_full_mark = 0
                total_subject = total_subject+1
                class_test = []

                unless @report_data['report']['exams'].blank?  
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] == '6' 
                      next
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?

                      if report['exam_category'] == '7'
                        sub_hw = sub_hw.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end
                      if report['exam_category'] == '8'
                        sub_cw = sub_cw.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['full_mark'].blank?

                      if report['exam_category'] == '7'
                        sub_hw_full_mark = sub_hw_full_mark+report['result'][report['exam_id']][subjects['id']]['full_mark'].to_i
                      end
                      if report['exam_category'] == '8'
                        sub_cw_full_mark = sub_cw_full_mark+report['result'][report['exam_id']][subjects['id']]['full_mark'].to_i
                      end
                    end 
                  end
                end

                unless @report_data['report']['exams'].blank?  
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] == '6' 
                      next
                    end
                    if report['exam_category'] == '6'
                      if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                        class_test << report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                        sub_assessment = sub_assessment.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      else
                        class_test << 0
                      end

                    end
                  end
                end


                hw_avg = 0
                assessment_avg = 0
                cw_avg = 0

                if sub_assessment > 0
                  class_test.sort! {|x,y| y <=> x }
                  ass_total_mark = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                  assessment_avg = (ass_total_mark.to_f/30.00)*50
                  assessment_avg = assessment_avg.round()
                  assessment = assessment+assessment_avg
                end
                if sub_hw > 0
                  hw_avg = (sub_hw.to_f/sub_hw_full_mark.to_f)*20
                  hw_avg = hw_avg.round()
                  hw = hw+hw_avg
                end
                if sub_cw > 0
                  cw_avg = (sub_cw.to_f/sub_cw_full_mark.to_f)*10
                  cw_avg = cw_avg.round()
                  cw = cw+cw_avg
                end  
              end  
            end
            ag_hw = 0
            ag_assessment = 0
            ag_cw = 0

            if total_subject > 0 && 
                if assessment > 0
                ag_assessment = assessment.to_f/total_subject.to_f
                ag_assessment = sprintf( "%0.01f", ag_assessment)
              end
              if hw > 0
                ag_hw = hw.to_f/total_subject.to_f
                ag_hw = sprintf( "%0.01f", ag_hw)
              end
              if cw > 0
                ag_cw = cw.to_f/total_subject.to_f
                ag_cw = sprintf( "%0.01f", ag_cw)
              end
            end
            total_mark = total_mark+ag_assessment.to_f+ag_hw.to_f+ag_cw.to_f

            unless @report_data['report']['subjects'].blank? 
              finish = false
              total_mark_life = 0
              start = true
              sub = 0
              @report_data['report']['subjects'].each do |subjects|
                if subjects['grade_subject'].to_i != 1
                  next
                end

                sub = sub+1
                sub_mark = 0
                unless @report_data['report']['exams'].blank?  
                  @report_data['report']['exams'].each do |report|
                    if report['quarter'] == '6' 
                      next
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                      if report['exam_category'] == '6'
                        sub_mark = sub_mark.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end 
                    end
                  end
                end   
                sub_mark = sprintf( "%0.01f", sub_mark)
                total_mark_life = total_mark_life+sub_mark.to_f
              end
              total_mark_life = sprintf( "%0.01f", total_mark_life)
              total_mark = total_mark+total_mark_life.to_f
            end

            att_mark = 0
            unless @report_data['total_first_term'].blank?
              if @report_data['total_first_term'] > 0 && @report_data['present_first_term'] > 0
                att_mark = (@report_data['present_first_term'].to_f/@report_data['total_first_term'].to_f)*10
                att_mark = sprintf( "%0.01f", att_mark)
                total_mark = total_mark+att_mark.to_f
              end
            end
            total_mark = sprintf( "%0.01f", total_mark)
            total_mark_mid_term = total_mark
            total_70 = (total_mark_mid_term.to_f*70)/100
            total_30 = (total_mark_final_term.to_f*30)/100
            main_total = total_70+total_30
            main_total = sprintf( "%0.01f", main_total)
            main_total = main_total.to_f
            
            
            
            
            row << 100
            row << main_total
            unless @student_position[@student.id.to_i].blank?
              if @promoted_to!="0"
                row << "Promoted to STD- "+@promoted_to
              else
                row << "-"
              end 
              unless @student_position[@student.id.to_i].blank?
                row << @student_position[@student.id.to_i]
              else
                row << "-"
              end 
              row << @student.class_roll_no
              unless @student_roll[@student_real_position[@student.id.to_i].to_i].blank?
                row << @student_roll[@student_real_position[@student.id.to_i].to_i]
              else
                row << "-"
              end 
              unless @student_section[@student_real_position[@student.id.to_i].to_i].blank?
                row << @student_section[@student_real_position[@student.id.to_i].to_i]
              else
                row << "-"
              end 
            else
              row << "-"
              row << "-"
              row << @student.class_roll_no
              row << "-"
              row << "-"
            end  
            new_book.worksheet(0).insert_row(iloop, row)
          end
        end

      end
    end
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => @batch.full_name + "-" + @connect_exam_obj.name + ".xls", :type =>  "application/vnd.ms-excel"
  end
  
  def tabulation_excell_sems2
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'tabulation'
    @id = params[:id]
    @connect_exam_obj = ExamConnect.find_by_id(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    get_continues(@id,@batch.id)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
    
    if @tabulation_data.nil?
      student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id,true)
      @tabulation_data = []
      if student_response['status']['code'].to_i == 200
        @tabulation_data = student_response['data']
      end
    end
    finding_data_sems2()
    if !@report_data.blank?
      
      row_first = ['ID','Name','Class','House','Total Mark','Obtain Mark','Promotion Status','Merit Position','Prev Roll','New Roll','New Section']
      new_book.worksheet(0).insert_row(0, row_first)
      
      @all_subject_exam = @report_data['report']['subjects']
      @all_student_subject = StudentsSubject.find_all_by_batch_id(@batch.id)
      @total_std_in_class = @report_data['report']['students'].count
      iloop = 0
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
      
          if !@report_data['report']['all_result'][std['id']]['exams'].blank?
            @report_data['report']['exams'] = @report_data['report']['all_result'][std['id']]['exams']
            iloop = iloop+1
            row = []
            row << @student.admission_no
            row << @student.full_name
            row << @batch.full_name
            ad_details = StudentAdditionalDetail.find_by_student_id_and_additional_field_id(@student.id,14)
            unless ad_details.blank?
              row << ad_details.additional_info
            else
              row << ""
            end
            grand_total_mark = 0
            gradn_obtain_mark = 0
            unless @report_data['report']['subjects'].blank? 
              @report_data['report']['subjects'].each do |subjects|
                loop = 0
                
                
                total_mark = 0 
                full_mark = 22.5 
                class_test = []
                unless @report_data['report']['exams'].blank?   
                  @report_data['report']['exams'].each do |report| 
                    if report['exam_category'] != '1' or report['quarter'] != '6'  
                      next 
                    end 
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank? 
                      obt_mark = (report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f/report['result'][report['exam_id']][subjects['id']]['full_mark'].to_f)*7.5 
                      class_test << obt_mark.round(2) 
                    else 
                      class_test << 0 
                    end 
                  end 
                end 
                class_test.sort! {|x,y| y <=> x }  
                total_mark = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f 

                unless @report_data['report']['exams'].blank?   
                  @report_data['report']['exams'].each do |report| 
                    if report['exam_category'] == '1' or report['exam_category'] == '3' or report['quarter'] != '6'  
                      next 
                    end 

                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank? 
                      obt_mark = (report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f/report['result'][report['exam_id']][subjects['id']]['full_mark'].to_f)*2.5 
                      total_mark = total_mark.to_f+obt_mark.to_f 
                    end 
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['full_mark'].blank? 
                      full_mark = full_mark+2.5 
                    end
                  end 
                end 
                 
                

                unless @report_data['report']['exams'].blank?   
                  @report_data['report']['exams'].each do |report| 
                    if report['exam_category'] != '3' or report['quarter'] != '6'  
                      next 
                    end
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?  
                      marks_obtain = report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f*2.00 
                      total_mark = total_mark.to_f+marks_obtain.to_f 
                    end 
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['full_mark'].blank? 
                      new_full_mark = report['result'][report['exam_id']][subjects['id']]['full_mark'].to_f*2.00 
                      full_mark = full_mark+new_full_mark 
                    end

                  end 
                end 
                total_mark = total_mark.round()
                
                midterm_total = 0
                midterm_30 = 0
                class_test = []
                unless @report_data['report']['exams'].blank?   
                  @report_data['report']['exams'].each do |report| 
                    if report['exam_category'] != '1' or report['quarter'] == '6'  
                      next 
                    end 
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank? 
                      obt_mark = (report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f/report['result'][report['exam_id']][subjects['id']]['full_mark'].to_f)*7.5 
                      class_test << obt_mark.round(2) 
                    else 
                      class_test << 0 
                    end 
                  end 
                end 
                class_test.sort! {|x,y| y <=> x }  
                midterm_total = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                unless @report_data['report']['exams'].blank?   
                  @report_data['report']['exams'].each do |report| 
                    if report['exam_category'] == '1' or report['exam_category'] == '3' or report['quarter'] == '6'  
                      next 
                    end 
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank? 
                      obt_mark = (report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f/report['result'][report['exam_id']][subjects['id']]['full_mark'].to_f)*2.5 
                      midterm_total = midterm_total.to_f+obt_mark.to_f 
                    end
                  end 
                end 
                

                unless @report_data['report']['exams'].blank?   
                  @report_data['report']['exams'].each do |report| 
                    if report['exam_category'] != '3' or report['quarter'] == '6'  
                      next 
                    end 
                    if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank? 
                      obt_mark = report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f 
                      midterm_total = midterm_total.to_f+obt_mark.to_f 
                    end
                  end 
                end
                midterm_total = midterm_total.round()
                if midterm_total.to_f>0
                  midterm_30  = (midterm_total.to_f*70)/100
                end
                
                if total_mark.to_f>0 and full_mark.to_f>0
                  main_mark = (total_mark.to_f/full_mark.to_f)*100 
                  total_70 = (total_mark.to_f*30)/100
                else
                  main_mark = 0
                  total_70 = 0
                end
                accumulated = midterm_30+total_70
                accumulated = accumulated.round()
                grand_total_mark = grand_total_mark+100
                gradn_obtain_mark = gradn_obtain_mark+accumulated
              end
            end
            row << grand_total_mark
            row << gradn_obtain_mark
            unless @student_position[@student.id.to_i].blank?
              if @promoted_to!="0"
                row << "Promoted to STD- "+@promoted_to
              else
                row << "-"
              end 
              unless @student_position[@student.id.to_i].blank?
                row << @student_position[@student.id.to_i]
              else
                row << "-"
              end 
              row << @student.class_roll_no
              unless @student_roll[@student_real_position[@student.id.to_i].to_i].blank?
                row << @student_roll[@student_real_position[@student.id.to_i].to_i]
              else
                row << "-"
              end 
              unless @student_section[@student_real_position[@student.id.to_i].to_i].blank?
                row << @student_section[@student_real_position[@student.id.to_i].to_i]
              else
                row << "-"
              end 
            else
              row << "-"
              row << "-"
              row << @student.class_roll_no
              row << "-"
              row << "-"
            end 
            new_book.worksheet(0).insert_row(iloop, row)
          end
        end

      end
    end
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => @batch.full_name + "-" + @connect_exam_obj.name + ".xls", :type =>  "application/vnd.ms-excel"
  end
  
  def tabulation_excell_sems
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'tabulation'
    @id = params[:id]
    @connect_exam_obj = ExamConnect.find_by_id(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    get_continues(@id,@batch.id)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
    if !@report_data.blank?
      
      row_first = ['ID','Name','Class','House','Total Mark','Obtain Mark']
      new_book.worksheet(0).insert_row(0, row_first)
      
      @all_subject_exam = @report_data['report']['subjects']
      @all_student_subject = StudentsSubject.find_all_by_batch_id(@batch.id)
      @total_std_in_class = @report_data['report']['students'].count
      iloop = 0
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
      
          if !@report_data['report']['all_result'][std['id']]['exams'].blank?
            @report_data['report']['exams'] = @report_data['report']['all_result'][std['id']]['exams']
            iloop = iloop+1
            row = []
            row << @student.admission_no
            row << @student.full_name
            row << @batch.full_name
            ad_details = StudentAdditionalDetail.find_by_student_id_and_additional_field_id(@student.id,14)
            unless ad_details.blank?
              row << ad_details.additional_info
            else
              row << ""
            end
            grand_total_mark = 0
            gradn_obtain_mark = 0
            unless @report_data['report']['subjects'].blank? 
              @report_data['report']['subjects'].each do |subjects|
                loop = 0
                unless @report_data['report']['exams'].blank?
                  total_mark = 0
                  midterm_total = 0
                  midterm_30 = 0
                  total_70 = 0
                  @report_data['report']['exams'].each do |report|
                    loop = loop+1
                    if loop>2
                      if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                        midterm_total = midterm_total.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end
                    else
                      if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][subjects['id']].blank?  and !report['result'][report['exam_id']][subjects['id']]['marks_obtained'].blank?
                        total_mark = total_mark.to_f+report['result'][report['exam_id']][subjects['id']]['marks_obtained'].to_f
                      end
                    end
                  end
                  if midterm_total > 0
                    midterm_30  = (midterm_total.to_f*30)/100
                  end
                  if total_mark > 0
                    total_70  = (total_mark.to_f*70)/100
                  end
                  sub_total = midterm_30+total_70
                  sub_total = sub_total.round
                  grand_total_mark = grand_total_mark+100
                  gradn_obtain_mark = gradn_obtain_mark+sub_total
                end
              end
            end
            row << grand_total_mark
            row << gradn_obtain_mark
             
            new_book.worksheet(0).insert_row(iloop, row)
          end
        end

      end
    end
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => @batch.full_name + "-" + @connect_exam_obj.name + ".xls", :type =>  "application/vnd.ms-excel"
  end
  
  def tabulation_excell_baghc
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'tabulation'
    center_align_format = Spreadsheet::Format.new :horizontal_align => :center,  :vertical_align => :middle,:left=>:thin,:right=>:thin,:top=>:thin,:bottom=>:thin
    
    center_align_format_90 = Spreadsheet::Format.new :horizontal_align => :center,:rotation=> 90,  :vertical_align => :middle,:left=>:thin,:right=>:thin,:top=>:thin,:bottom=>:thin
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @exam_comment_all = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    if @report_data.nil?
      student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id)
      @report_data = []
      if student_response['status']['code'].to_i == 200
        @report_data = student_response['data']
      end
    end
    row_first = ['Roll','Name','Exam Name','']
    new_book.worksheet(0).merge_cells(0,0,3,0)
    new_book.worksheet(0).merge_cells(0,1,3,1)
    new_book.worksheet(0).merge_cells(0,2,3,2)
    new_book.worksheet(0).merge_cells(0,3,0,4)
    
    row_second = ['','','','Full Marks']
    row_third = ['','','','']
    row_fourth = ['','','','']
    
    new_book.worksheet(0).merge_cells(1,3,3,3)
    
    i = 4
    j = 4 
    t_subject = 0
    
    rotate_90 = []
    unless @report_data.blank?
      @report_data['report']['subjects'].each do |sub|
        row_first << sub['name']
        row_second << "CQ/SQ"
        row_third << "MCQ"
        row_fourth << "MTT/Prac"
        cq_max = ""
        mcq_max = ""
        mtt_max = ""
        
        @report_data['report']['exams'].each do |report|
          if report['exam_category'] == '1' && cq_max.blank? && !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][sub['id']].blank?  and !report['result'][report['exam_id']][sub['id']]['full_mark'].blank?
            cq_max = report['result'][report['exam_id']][sub['id']]['full_mark'].to_i.to_s
          end
          if report['exam_category'] == '2' && mcq_max.blank? && !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][sub['id']].blank?  and !report['result'][report['exam_id']][sub['id']]['full_mark'].blank?
            mcq_max = report['result'][report['exam_id']][sub['id']]['full_mark'].to_i.to_s
          end
          if report['exam_category'] == '3' && mtt_max.blank? && !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][sub['id']].blank?  and !report['result'][report['exam_id']][sub['id']]['full_mark'].blank?
            mtt_max = report['result'][report['exam_id']][sub['id']]['full_mark'].to_i.to_s
          end
        end  
        row_second << cq_max
        row_third << mcq_max
        row_fourth << mtt_max
        i = i+1
        j = j+4
        t_subject = t_subject+1
        
      end
    end
    
    row_first << "GPA"
    row_first << '"F" Grade Count count'
    row_second << ""
    row_second << ""
    row_third << ""
    row_third << ""
    row_fourth << ""
    row_fourth << "Total Sub :"+t_subject.to_s
    
    new_book.worksheet(0).merge_cells(0,i,3,i)
    new_book.worksheet(0).merge_cells(0,i+1,2,i+1)
   
    
    new_book.worksheet(0).insert_row(0, row_first)
    new_book.worksheet(0).insert_row(1, row_second)
    new_book.worksheet(0).insert_row(2, row_third)
    new_book.worksheet(0).insert_row(3, row_fourth)
    sheet1.row(0).default_format = center_align_format
    sheet1.row(1).default_format = center_align_format
    sheet1.row(2).default_format = center_align_format
    sheet1.row(3).default_format = center_align_format
 
    
    sheet1.row(0).set_format(2,center_align_format_90)
    sheet1.row(1).set_format(3,center_align_format_90)
    
    k = 4
    k3 = 4
    
    
    total_std = 0
    half_pass = {}
    final_pass = {}
    avg_pass = {}
    
    @report_data['report']['students'].each do |std|
      
      total_std = total_std+1
      row_first = []
      row_second = ['','']
      row_third = ['','']
      row_fourth = ['','']
      row_fifth = ['','']
      row_first << std['class_roll_no']
      row_first << std['first_name'].to_s+" "+std['last_name'].to_s
      
      new_book.worksheet(0).merge_cells(k3,0,k3+4,0)
      new_book.worksheet(0).merge_cells(k3,1,k3+4,1)
      
      row_first <<  "CQ/SQ"
      row_second << "MCQ"
      row_third <<  "MTT/Prac"
      row_fourth << "Total(%)"
      row_fifth <<  "GP"
      
      new_book.worksheet(0).merge_cells(k3,2,k3,4)
      new_book.worksheet(0).merge_cells(k3+1,2,k3+1,4)
      new_book.worksheet(0).merge_cells(k3+2,2,k3+2,4)
      new_book.worksheet(0).merge_cells(k3+3,2,k3+3,4)
      new_book.worksheet(0).merge_cells(k3+4,2,k3+4,4)
      
      fail_half = 0
      fail_final = 0
      fail_avg = 0
      
      
      
      @report_data['report']['subjects'].each do |sub|
        if final_pass[sub['id']].blank?
          final_pass[sub['id']] = 0
        end
        if half_pass[sub['id']].blank?
          half_pass[sub['id']] = 0
        end
        if avg_pass[sub['id']].blank?
          avg_pass[sub['id']] = 0
        end
        cq_total = 0
        mcq_total = 0
        mtt_total = 0
        cq = 0
        mcq = 0
        mtt = 0
        
        cq_total_main = 0
        mcq_total_main = 0
        mtt_total_main = 0
        
        
        cq1 = 0
        mcq1 = 0
        mtt1 = 0
        
        cq2 = 0
        mcq2 = 0
        mtt2 = 0
        
        cq3 = 0
        mcq3 = 0
        mtt3 = 0
        
        
        total_100 = 0
        total_1002 = 0
        full_100 = 0
        full_1002 = 0
        total_1003 = 0
        full_1003 = 0
        @report_data['report']['exams'].each do |report|
          if report['quarter'] == "0"
            grade_mark = 0
            full_mark = 0

            if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][sub['id']].blank?  and !report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].blank?
              total_100 = total_100.to_f+report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
              total_1003 = total_1003.to_f+report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
              grade_mark = report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
                    
              if report['exam_category'] == '1'
                cq = cq+report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
                cq1 =report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
              elsif report['exam_category'] == '2'
                mcq = mcq+report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
                mcq1 =report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
              elsif report['exam_category'] == '3'
                mtt = mtt+report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
                mtt1 =report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
              end  
                    
            end
            if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][sub['id']].blank?  and !report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].blank?
              full_100 = full_100+report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
              full_1003 = full_1003+report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
              full_mark = report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
              if report['exam_category'] == '1'
                cq_total = cq_total+report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                cq_total_main = report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
              elsif report['exam_category'] == '2'
                mcq_total = mcq_total+report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                mcq_total_main = report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
              elsif report['exam_category'] == '3'
                mtt_total = mtt_total+report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                mtt_total_main = report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
              end
            end 

            if full_mark > 0
              if grade_mark > 0
                point_mark = (grade_mark.to_f/full_mark.to_f)*100
                grade = GradingLevel.percentage_to_grade(point_mark, @batch.id)
                if !grade.blank? and !grade.name.blank? and !grade.credit_points.blank?
                  if grade.credit_points.to_i == 0
                    u_grade = true
                  end  
                end
              else
                u_grade = true
              end  
            end 
                  
          else
            grade_mark2 = 0
            full_mark2 = 0

            if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][sub['id']].blank?  and !report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].blank?
              total_1002 = total_1002.to_f+report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
              total_1003 = total_1003.to_f+report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
              grade_mark2 = report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
              if report['exam_category'] == '1'
                cq = cq+report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
                cq2 =report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
              elsif report['exam_category'] == '2'
                mcq = mcq+report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
                mcq2 =report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
              elsif report['exam_category'] == '3'
                mtt = mtt+report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
                mtt2 =report['result'][report['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round()
              end 
            end
            if !report['result'].blank? and !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][sub['id']].blank?  and !report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].blank?
              full_1002 = full_1002+report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
              full_1003 = full_1003+report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
              full_mark2 = report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
              if report['exam_category'] == '1'
                cq_total = cq_total+report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
              elsif report['exam_category'] == '2'
                mcq_total = mcq_total+report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
              elsif report['exam_category'] == '3'
                mtt_total = mtt_total+report['result'][report['exam_id']][sub['id']][std['id']]['full_mark'].to_i
              end
            end 

            if full_mark2 > 0
              if grade_mark2 > 0
                point_mark2 = (grade_mark2.to_f/full_mark2.to_f)*100
                grade2 = GradingLevel.percentage_to_grade(point_mark2, @batch.id)
                if !grade2.blank? and !grade2.name.blank? and !grade2.credit_points.blank?
                  if grade2.credit_points.to_i == 0
                    u_grade2 = true
                  end  
                end
              else
                u_grade2 = true
              end  
            end 
                  
          end 
        end
        
        if cq_total > 0
          if cq > 0
            point_mark3 = (cq.to_f/cq_total.to_f)*100
            
            cq3 = (cq.to_f/cq_total.to_f)*cq_total_main
            cq3 = cq3.round()
            grade3 = GradingLevel.percentage_to_grade(point_mark3, @batch.id)
            if !grade3.blank? and !grade3.name.blank? and !grade3.credit_points.blank?
              if grade3.credit_points.to_i == 0
                u_grade3 = true
              end  
            end
          else
            u_grade3 = true
          end  
        end 
        if mcq_total > 0
          if cq > 0
            point_mark3 = (mcq.to_f/mcq_total.to_f)*100
              
            mcq3 = (mcq.to_f/mcq_total.to_f)*mcq_total_main
            mcq3 = mcq3.round()
            grade3 = GradingLevel.percentage_to_grade(point_mark3, @batch.id)
            if !grade3.blank? and !grade3.name.blank? and !grade3.credit_points.blank?
              if grade3.credit_points.to_i == 0
                u_grade3 = true
              end  
            end
          else
            u_grade3 = true
          end  
        end
        if mtt_total > 0
          if mtt > 0
            point_mark3 = (mtt.to_f/mtt_total.to_f)*100
            mtt3 = (mtt.to_f/mtt_total.to_f)*mtt_total_main
            mtt3 = mtt3.round()
            grade3 = GradingLevel.percentage_to_grade(point_mark3, @batch.id)
            if !grade3.blank? and !grade3.name.blank? and !grade3.credit_points.blank?
              if grade3.credit_points.to_i == 0
                u_grade3 = true
              end  
            end
          else
            u_grade3 = true
          end  
        end
          
        
        coverted_100 = 0
        if total_100 > 0
          coverted_100 = (total_100.to_f/full_100.to_f)*100
        end
        coverted_100_fraction = coverted_100
        coverted_100 = coverted_100.round()
        gp_point =0
        if full_100 > 0
          grade = GradingLevel.percentage_to_grade(coverted_100, @batch.id)
          if !grade.blank? and !grade.name.blank? and !grade.credit_points.blank?
            gp_point = grade.credit_points
            if grade.credit_points.to_i == 0
              u_grade = true
            end  
          end
        end
        coverted_1002 = 0
        if total_1002 > 0
          coverted_1002 = (total_1002.to_f/full_1002.to_f)*100
        end
        coverted_100_fraction2 = coverted_1002
        coverted_1002 = coverted_1002.round()
        gp_point2 =0
        if full_1002 > 0
          grade2 = GradingLevel.percentage_to_grade(coverted_1002, @batch.id)
          if !grade2.blank? and !grade2.name.blank? and !grade2.credit_points.blank?
            gp_point2 = grade2.credit_points
            if grade2.credit_points.to_i == 0
              u_grade2 = true
            end  
          end
        end
          
        coverted_1003 = 0
        if total_1003 > 0
          coverted_1003 = (total_1003.to_f/full_1003.to_f)*100
        end
        coverted_100_fraction3 = coverted_1003
        coverted_1003 = coverted_1003.round()
        gp_point3 =0
        if full_1003 > 0
          grade3 = GradingLevel.percentage_to_grade(coverted_1003, @batch.id)
          if !grade3.blank? and !grade3.name.blank? and !grade3.credit_points.blank?
            gp_point3 = grade3.credit_points
            if grade3.credit_points.to_i == 0
              u_grade3 = true
            end  
          end
        end
          
        row_first <<  cq1
        row_first <<  mcq1
        row_first <<  mtt1
        if u_grade
          fail_half = fail_half+1
          row_first << "0.0"
        elsif !grade.blank?
          row_first << grade.credit_points
          half_pass[sub['id']] = half_pass[sub['id']]+1
        else
          row_first << "-"
        end
          
        row_second <<  cq2
        row_second <<  mcq2
        row_second <<  mtt2
        if u_grade2
          fail_final = fail_final+1
          row_second << "0.0"
        elsif !grade2.blank?
          row_second << grade2.credit_points
          final_pass[sub['id']] = final_pass[sub['id']]+1
        else
          row_second << "-"
        end
          
        row_third <<  cq3
        row_third <<  mcq3
        row_third <<  mtt3
        if u_grade3
          fail_avg = fail_avg+1
          row_third << "0.0"
        elsif !grade3.blank?
          row_third << grade3.credit_points
          avg_pass[sub['id']] = avg_pass[sub['id']]+1
        else
          row_third << "-"
        end
         
        
      end
      row_first << fail_half
      row_second << fail_final
      row_third << fail_avg
      
      row_first << ""
      row_second << ""
      row_third << ""
      
      new_book.worksheet(0).insert_row(k3, row_first)
      new_book.worksheet(0).insert_row(k3+1, row_second)
      new_book.worksheet(0).insert_row(k3+2, row_third)
      sheet1.row(k3).default_format = center_align_format
      sheet1.row(k3+1).default_format = center_align_format
      sheet1.row(k3+2).default_format = center_align_format
      k = k+1
      k3 = k3+3
    end
    
    k = k3
    k = k+2
    k3 = k3+2
    row_first = ['Summarize','Half Yearly/Pre-Test','Total Students']
    row_second = ['','Yearly Final/Test','Total Students']
    row_third = ['','Average','Total Students']
   
    row1 = ['','','Pass']
    row2 = ['','','Fail']
    row3 = ['','','Pass(%)']
    
    row4 = ['','','Pass']
    row5 = ['','','Fail']
    row6 = ['','','Pass(%)']
    
    row7 = ['','','Pass']
    row8 = ['','','Fail']
    row9 = ['','','Pass(%)']
    
    new_book.worksheet(0).merge_cells(k3,0,k3+11,0)
    new_book.worksheet(0).merge_cells(k3,1,k3+3,1)
    
    new_book.worksheet(0).merge_cells(k3+4,1,k3+7,1)
    new_book.worksheet(0).merge_cells(k3+8,1,k3+11,1)
    
    
    
    j = 3
    @report_data['report']['subjects'].each do |sub|
      row_first << total_std
      unless half_pass[sub['id']].blank?
        row1 << half_pass[sub['id']]
        fail = total_std.to_i - half_pass[sub['id']].to_i
        row2 << fail
        percent = (half_pass[sub['id']].to_f/total_std.to_f)*100
        row3 << sprintf( "%0.02f", percent)
      else
        row1 << "0"
        row2 << total_std
        row3 << "100%"
      end 
      
      
      
      row_second << total_std
      unless half_pass[sub['id']].blank?
        row4 << final_pass[sub['id']]
        fail = total_std.to_i - final_pass[sub['id']].to_i
        row5 << fail
        percent = (final_pass[sub['id']].to_f/total_std.to_f)*100
        row6 << sprintf( "%0.02f", percent)
      else
        row4 << "0"
        row5 << total_std
        row6 << "100%"
      end
      
      
      row_third << total_std
      unless half_pass[sub['id']].blank?
        row7 << avg_pass[sub['id']]
        fail = total_std.to_i - avg_pass[sub['id']].to_i
        row8 << fail
        percent = (avg_pass[sub['id']].to_f/total_std.to_f)*100
        row9 << sprintf( "%0.02f", percent)
      else
        row7 << "0"
        row8 << total_std
        row9 << "100%"
      end
      
      row1 << ''
      row1 << ''
      row1 << ''
      row2 << ''
      row2 << ''
      row2 << ''
      row3 << ''
      row3 << ''
      row3 << ''
      row4 << ''
      row4 << ''
      row4 << ''
      row5 << ''
      row5 << ''
      row5 << ''
      row6 << ''
      row6 << ''
      row6 << ''
      row7 << ''
      row7 << ''
      row7 << ''
      row8 << ''
      row8 << ''
      row8 << ''
      row9 << ''
      row9 << ''
      row9 << ''
      
      row_first << ''
      row_first << ''
      row_first << ''
      row_second << ''
      row_second << ''
      row_second << ''
      row_third << ''
      row_third << ''
      row_third << ''
      
      new_book.worksheet(0).merge_cells(k,j,k,j+3)
      new_book.worksheet(0).merge_cells(k+1,j,k+1,j+3)
      new_book.worksheet(0).merge_cells(k+2,j,k+2,j+3)
      new_book.worksheet(0).merge_cells(k+3,j,k+3,j+3)
      new_book.worksheet(0).merge_cells(k+4,j,k+4,j+3)
      new_book.worksheet(0).merge_cells(k+5,j,k+5,j+3)
      new_book.worksheet(0).merge_cells(k+6,j,k+6,j+3)
      new_book.worksheet(0).merge_cells(k+7,j,k+7,j+3)
      new_book.worksheet(0).merge_cells(k+8,j,k+8,j+3)
      new_book.worksheet(0).merge_cells(k+9,j,k+9,j+3)
      new_book.worksheet(0).merge_cells(k+10,j,k+10,j+3)
      new_book.worksheet(0).merge_cells(k+11,j,k+11,j+3)
      j = j+4
    end
    
    new_book.worksheet(0).insert_row(k, row_first)
    sheet1.row(k).default_format = center_align_format
    new_book.worksheet(0).insert_row(k+1, row1)
    sheet1.row(k+1).default_format = center_align_format
    new_book.worksheet(0).insert_row(k+2, row2)
    sheet1.row(k+2).default_format = center_align_format
    new_book.worksheet(0).insert_row(k+3, row3)
    sheet1.row(k+3).default_format = center_align_format
    new_book.worksheet(0).insert_row(k+4, row_second)
    sheet1.row(k+4).default_format = center_align_format
    new_book.worksheet(0).insert_row(k+5, row4)
    sheet1.row(k+5).default_format = center_align_format
    new_book.worksheet(0).insert_row(k+6, row5)
    sheet1.row(k+6).default_format = center_align_format
    new_book.worksheet(0).insert_row(k+7, row6)
    sheet1.row(k+7).default_format = center_align_format
    new_book.worksheet(0).insert_row(k+8, row_third)
    sheet1.row(k+8).default_format = center_align_format
    new_book.worksheet(0).insert_row(k+9, row7)
    sheet1.row(k+9).default_format = center_align_format
    new_book.worksheet(0).insert_row(k+10, row8)
    sheet1.row(k+10).default_format = center_align_format
    new_book.worksheet(0).insert_row(k+11, row9)
    sheet1.row(k+11).default_format = center_align_format
    
    
    center_align_format_big_90 = Spreadsheet::Format.new :horizontal_align => :center,:size => 18,:rotation=> 90,  :vertical_align => :middle,:left=>:thin,:right=>:thin,:top=>:thin,:bottom=>:thin
    
    sheet1.row(k).set_format(0,center_align_format_big_90)
    
    batch_split = @batch.name.split(" ")
    
    group_name = ""
    unless @batch.course.group.blank?
      group_split = @batch.course.group.split(" ")
      unless group_split[2].blank?
        group_split[0] = group_split[0]+" "+group_split[1]
      end
      group_name = group_split[0]
    end
    sheet1.add_header("Banophool Adibashi Green Heart College (Tabulation Sheet : "+@connect_exam_obj.name.to_s+")
        Class :"+@batch.course.course_name.to_s+" || Section :"+@batch.course.section_name.to_s+" || Version :"+batch_split[1]+"
      ")
    
    
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => @batch.full_name + "-" + @connect_exam_obj.name + ".xls", :type =>  "application/vnd.ms-excel"
  end
  
  def tabulation_excell
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'tabulation'
    
    
    center_align_format = Spreadsheet::Format.new :horizontal_align => :center,  :vertical_align => :middle
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @exam_comment_all = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
  
    @batch = Batch.find(@connect_exam_obj.batch_id)
    
    std_subject = StudentsSubject.find_all_by_batch_id_and_elective_type(@batch.id,4,:include=>[:subject])
    
    if @tabulation_data.nil?
      student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id,true)
      @tabulation_data = []
      if student_response['status']['code'].to_i == 200
        @tabulation_data = student_response['data']
      end
    end
    if @connect_exam_obj.result_type.to_i == 13 or @connect_exam_obj.result_type.to_i == 14 or @connect_exam_obj.result_type.to_i == 15 or @connect_exam_obj.result_type.to_i == 16 or @connect_exam_obj.result_type.to_i == 20
      finding_data_sagc_covid()
    elsif @connect_exam_obj.result_type.to_i == 17
      finding_data_sagc_25()
    elsif @connect_exam_obj.result_type.to_i == 18
      finding_data_sagc_18()
    elsif @connect_exam_obj.result_type.to_i == 21
      finding_data_sagc_21()
    elsif @connect_exam_obj.result_type.to_i == 19
      finding_data_19()
    else
      finding_data5()
    end
    
    if !@student_position.blank?
      @student_position_first_term = @student_position
      @subject_highest_1st_term = @subject_highest
      @student_position_first_term = @student_position
      @student_position_first_term_batch = @student_position_batch
    elsif !@student_position_second_term.blank?
      @subject_highest_1st_term = @subject_highest_2nd_term
      @student_position_first_term = @student_position_second_term
      @student_position_first_term_batch = @student_position_second_term_batch
    end 
    
 
    
    row_first = ['Srl','S. ID','Roll','Student Name','Total','GPA & GP','LG','M.C','M.S','WD','PD']
    starting_row = 11
    sub_id_array = []
    @subject_result.each do |key,sub_res|
      unless sub_id_array.include?(key)
        sub_id_array << key
      end
    end
    @all_subject_connect_exam = Subject.find_all_by_code(sub_id_array,:conditions=>["batch_id = ?",@batch.id],:order=>"priority asc")
    subject_map = @all_subject_connect_exam.map(&:id)
    @all_subject_connect_exam.each do |value|       
      key = value.code.to_s
      if @subject_result[key].blank?
        next
      end
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
    classes = ["NURSERY",'KG',"ONE",'TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT']
    unless classes.include?(@batch.course.course_name.upcase)
      row_first << "4th Subject Name"
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
      key = sub_result.code.to_s
      unless @subject_result[key].blank?
        row_first << "AT"
        row_first << "CW"
        row_first << "OB"
        row_first << "SB"
        row_first << "PR"
        row_first << "+RT"
        row_first << "+CT"
        row_first << "LG"
      end
    end
    row_first << ""
    new_book.worksheet(0).insert_row(1, row_first)
    
    std_loop = 2
    sl = 1
    #abort(@student_result.inspect) 
    @student_result.each do |std_result|
      if std_result['batch_id'].to_i != @batch.id.to_i
        next
      end
      tmp_row = []
      tmp_row << sl 
      #std_result['sl']
      sl = sl + 1
      tmp_row << std_result['sid'].to_s
      tmp_row << std_result['roll'].to_s
      tmp_row << std_result['name'].to_s
      tmp_row << std_result['grand_total_with_fraction'].to_f.round().to_s
      tmp_row << std_result['gp'].to_s+"("+std_result['gpa'].to_s+")"
      if !@student_position_first_term_batch.blank? && !@student_position_first_term_batch[std_result['id'].to_i].blank?
        tmp_row << std_result['lg']
      else
        tmp_row << "F"
      end 
      exam_comment = {}
      unless @exam_comment_all.blank? 
        @exam_comment_all.each do |ec|
          if ec.student_id.to_i == std_result['id'].to_i
            exam_comment = ec
            break
          end
        end
      end
      
      
      if !@student_position_first_term.blank? && !@student_position_first_term[std_result['id'].to_i].blank?
        tmp_row <<  @student_position_first_term[std_result['id'].to_i]
      else
        tmp_row << ""
      end 
      
      
      if !@student_position_first_term_batch.blank? && !@student_position_first_term_batch[std_result['id'].to_i].blank?
        tmp_row << @student_position_first_term_batch[std_result['id'].to_i]
      else
        tmp_row << ""
      end
      
      unless exam_comment.blank?
        total_att = ""
        absent = ""
        if !exam_comment.blank?
          all_comments = exam_comment.comments
          if !all_comments.blank?
            all_comments_array = all_comments.split("|")
            total_att = all_comments_array[0]
            if !all_comments_array[1].nil?
              absent = all_comments_array[1]
            end
          end
        end
        tmp_row << total_att
        tmp_row << absent
      else
        tmp_row << ""
        tmp_row << ""
      end
      rt = 0
      courseObj = Course.find_by_id(@batch.course_id)
      unless std_result['subjects'].blank?
        @all_subject_connect_exam.each do |value|
          key = value.code.to_s
          unless @subject_result[key].blank?
            unless std_result['subjects'][key].blank?
              rt = std_result['subjects'][key]['result']['at'].to_f + std_result['subjects'][key]['result']['cw'].to_f + std_result['subjects'][key]['result']['ob'].to_f + std_result['subjects'][key]['result']['sb'].to_f + std_result['subjects'][key]['result']['pr'].to_f
              tmp_row << std_result['subjects'][key]['result']['at'].to_s
              tmp_row << std_result['subjects'][key]['result']['cw'].to_s
              tmp_row << std_result['subjects'][key]['result']['ob'].to_s
              tmp_row << std_result['subjects'][key]['result']['sb'].to_s
              tmp_row << std_result['subjects'][key]['result']['pr'].to_s
              if courseObj.course_name == "Ten"
                tmp_row << std_result['subjects'][key]['result']['ct'].to_s
              else
                tmp_row << rt.to_s
              end
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
      end
      subject_std = std_subject.find{|val| val.student_id.to_i == std_result['id'].to_i and subject_map.include?(val.subject_id.to_i) }
      
      unless classes.include?(@batch.course.course_name.upcase)
        unless subject_std.blank?
          tmp_row << subject_std.subject.name
        else
          tmp_row << "-"
        end  
      end  
      
      new_book.worksheet(0).insert_row(std_loop, tmp_row)
      
      
      std_loop = std_loop+1
      
    end
    batch_split = @batch.name.split(" ")
    
    group_name = ""
    unless @batch.course.group.blank?
      group_split = @batch.course.group.split(" ")
      unless group_split[2].blank?
        group_split[0] = group_split[0]+" "+group_split[1]
      end
      group_name = group_split[0]
    end
    shift = ""
    version= ""
    unless batch_split[1].blank?
      version = batch_split[1]
    end
    sheet1.add_header("SHAHEED BIR UTTAM LT. ANWAR GIRLS' COLLEGE (Tabulation Sheet : "+@connect_exam_obj.name.to_s+")
 Program :"+@batch.course.course_name.to_s+" || Group :"+group_name.to_s+" || Section :"+@batch.course.section_name.to_s+" || Shift :"+batch_split[0]+" || Session :"+@batch.course.session.to_s+" || Version :"+version+"
      ")
    sheet1.add_footer("TIPS :: M.C = Merit in Class  ||  M.S = Merit in Section  ||  +RT = Raw Total  ||  +CT = Converted Total")
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => @batch.full_name + "-" + @connect_exam_obj.name + ".xls", :type =>  "application/vnd.ms-excel"
  end
  
  def continues
    @id = params[:id]
    @transcript = params[:transscript]
    @student_main_id = params[:student]
    @unsolved_exam = params[:unsolved_exam]
    
    @connect_exam_obj = ExamConnect.find_by_id(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id,:include=>["course"])
   
    exam_connect_merit_lists = ExamConnectMeritList.find(:first, :conditions=>"connect_exam_id = #{@connect_exam_obj.id} and batch_id = #{@batch.id}") 
    unless exam_connect_merit_lists.blank?
      exam_connect_merit_lists = ExamConnectMeritList.find(:all, :conditions=>"connect_exam_id = #{@connect_exam_obj.id} and batch_id = #{@batch.id}", :order=>"gpa DESC, marks DESC") 
      unless exam_connect_merit_lists.blank?
        i = 1
        exam_connect_merit_lists.each do |exam_connect_merit_list|
          pos = 0
          if exam_connect_merit_list.gpa.to_f > 0.0
            pos = i
            i = i + 1
          end
          exam_connect_merit_list.update_attributes(:position=>pos)
        end
        
        group_course_ids = Course.find(:all, :conditions => "course_name = '#{@batch.course.course_name}' and `group` = '#{@batch.course.group}' and is_deleted = 0").map(&:id)
        group_batch_ids = Batch.find(:all, :conditions => "course_id IN (#{group_course_ids.join(",")}) and is_deleted = 0").map(&:id)
        connect_exam_ids = ExamConnect.find(:all, :conditions=> "batch_id IN (#{group_batch_ids.join(",")}) and is_deleted = 0").map(&:id)
        
        exam_connect_merit_lists = ExamConnectMeritList.find(:all, :conditions=> "connect_exam_id IN (#{connect_exam_ids.join(",")}) AND batch_id IN (#{group_batch_ids.join(",")})", :order=>"gpa DESC, marks DESC") 
        unless exam_connect_merit_lists.blank?
          i = 1
          exam_connect_merit_lists.each do |exam_connect_merit_list|
            pos = 0
            if exam_connect_merit_list.gpa.to_f > 0.0
              pos = i
              i = i + 1
            end
            exam_connect_merit_list.update_attributes(:section_position=>pos)
          end
        end
      end
    end
    
    @student_position = [];
    exam_connect_merit_lists = ExamConnectMeritList.find(:all, :conditions=>"connect_exam_id = #{@connect_exam_obj.id} and batch_id = #{@batch.id} and position > 0", :order => "gpa DESC, marks DESC, position asc") 
    unless exam_connect_merit_lists.blank?
      exam_connect_merit_lists.each do |exam_connect_merit_list|
         @student_position[exam_connect_merit_list.student.id] = [];   
         @student_position[exam_connect_merit_list.student.id][0] = exam_connect_merit_list.position
         @student_position[exam_connect_merit_list.student.id][1] = exam_connect_merit_list.section_position
      end
    end
    
    #    pdf_name = "continues_connect_exam_"+@connect_exam_obj.id.to_s+".pdf"
    #    dirname = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"continues","0"+@connect_exam_obj.id.to_s)
    #    unless File.directory?(dirname)
    #      FileUtils.mkdir_p(dirname)
    #    end
    #    #    FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s))
    #    file_name = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"continues","0"+@connect_exam_obj.id.to_s,pdf_name)
    #    champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    #    api_from = champs21_config['from']
   
    @assigned_employee=@batch.all_class_teacher
    get_continues(@id,@batch.id,@unsolved_exam)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end 
    @exam_comment_all = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
    render_connect_exam("continues",false)
   
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
    #    pdf_name = "comment_tabulation_connect_exam_"+@connect_exam_obj.id.to_s+".pdf"
    #    dirname = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"tabulation","0"+@connect_exam_obj.id.to_s)
    #    unless File.directory?(dirname)
    #      FileUtils.mkdir_p(dirname)
    #    end
    #    #    FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s))
    #    file_name = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"tabulation","0"+@connect_exam_obj.id.to_s,pdf_name)
    #    champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    #    api_from = champs21_config['from']
   
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
      :orientation => 'Landscape', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    
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

  def tabulation_excell
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'tabulation'
    
    
    center_align_format = Spreadsheet::Format.new :horizontal_align => :center,  :vertical_align => :middle
    @id = params[:id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @exam_comment_all = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
  
    @batch = Batch.find(@connect_exam_obj.batch_id)
    
    std_subject = StudentsSubject.find_all_by_batch_id_and_elective_type(@batch.id,4,:include=>[:subject])
    
    if @tabulation_data.nil?
      student_response = get_tabulation_connect_exam(@connect_exam_obj.id,@batch.id,true)
      @tabulation_data = []
      if student_response['status']['code'].to_i == 200
        @tabulation_data = student_response['data']
      end
    end
    if @connect_exam_obj.result_type.to_i == 13 or @connect_exam_obj.result_type.to_i == 14 or @connect_exam_obj.result_type.to_i == 15 or @connect_exam_obj.result_type.to_i == 16 or @connect_exam_obj.result_type.to_i == 20
      finding_data_sagc_covid()
    elsif @connect_exam_obj.result_type.to_i == 17
      finding_data_sagc_25()
    elsif @connect_exam_obj.result_type.to_i == 18
      finding_data_sagc_18()
    elsif @connect_exam_obj.result_type.to_i == 21
      finding_data_sagc_21()
    elsif @connect_exam_obj.result_type.to_i == 19
      finding_data_19()
    else
      finding_data5()
    end
    
    if !@student_position.blank?
      @student_position_first_term = @student_position
      @subject_highest_1st_term = @subject_highest
      @student_position_first_term = @student_position
      @student_position_first_term_batch = @student_position_batch
    elsif !@student_position_second_term.blank?
      @subject_highest_1st_term = @subject_highest_2nd_term
      @student_position_first_term = @student_position_second_term
      @student_position_first_term_batch = @student_position_second_term_batch
    end 
    
 
    
    row_first = ['Srl','S. ID','Roll','Student Name','Total','GPA & GP','LG','M.C','M.S','WD','PD']
    starting_row = 11
    sub_id_array = []
    @subject_result.each do |key,sub_res|
      unless sub_id_array.include?(key)
        sub_id_array << key
      end
    end
    @all_subject_connect_exam = Subject.find_all_by_code(sub_id_array,:conditions=>["batch_id = ?",@batch.id],:order=>"priority asc")
    subject_map = @all_subject_connect_exam.map(&:id)
    @all_subject_connect_exam.each do |value|       
      key = value.code.to_s
      if @subject_result[key].blank?
        next
      end
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
    classes = ["NURSERY",'KG',"ONE",'TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT']
    unless classes.include?(@batch.course.course_name.upcase)
      row_first << "4th Subject Name"
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
      key = sub_result.code.to_s
      unless @subject_result[key].blank?
        row_first << "AT"
        row_first << "CW"
        row_first << "OB"
        row_first << "SB"
        row_first << "PR"
        row_first << "+RT"
        row_first << "+CT"
        row_first << "LG"
      end
    end
    row_first << ""
    new_book.worksheet(0).insert_row(1, row_first)
    
    std_loop = 2
    sl = 1
    #abort(@student_result.inspect) 
    @student_result.each do |std_result|
      if std_result['batch_id'].to_i != @batch.id.to_i
        next
      end
      tmp_row = []
      tmp_row << sl 
      #std_result['sl']
      sl = sl + 1
      tmp_row << std_result['sid'].to_s
      tmp_row << std_result['roll'].to_s
      tmp_row << std_result['name'].to_s
      tmp_row << std_result['grand_total_with_fraction'].to_f.round().to_s
      tmp_row << std_result['gp'].to_s+"("+std_result['gpa'].to_s+")"
      if !@student_position_first_term_batch.blank? && !@student_position_first_term_batch[std_result['id'].to_i].blank?
        tmp_row << std_result['lg']
      else
        tmp_row << "F"
      end 
      exam_comment = {}
      unless @exam_comment_all.blank? 
        @exam_comment_all.each do |ec|
          if ec.student_id.to_i == std_result['id'].to_i
            exam_comment = ec
            break
          end
        end
      end
      
      
      if !@student_position_first_term.blank? && !@student_position_first_term[std_result['id'].to_i].blank?
        tmp_row <<  @student_position_first_term[std_result['id'].to_i]
      else
        tmp_row << ""
      end 
      
      
      if !@student_position_first_term_batch.blank? && !@student_position_first_term_batch[std_result['id'].to_i].blank?
        tmp_row << @student_position_first_term_batch[std_result['id'].to_i]
      else
        tmp_row << ""
      end
      
      unless exam_comment.blank?
        total_att = ""
        absent = ""
        if !exam_comment.blank?
          all_comments = exam_comment.comments
          if !all_comments.blank?
            all_comments_array = all_comments.split("|")
            total_att = all_comments_array[0]
            if !all_comments_array[1].nil?
              absent = all_comments_array[1]
            end
          end
        end
        tmp_row << total_att
        tmp_row << absent
      else
        tmp_row << ""
        tmp_row << ""
      end
      rt = 0
      courseObj = Course.find_by_id(@batch.course_id)
      unless std_result['subjects'].blank?
        @all_subject_connect_exam.each do |value|
          key = value.code.to_s
          unless @subject_result[key].blank?
            unless std_result['subjects'][key].blank?
              rt = std_result['subjects'][key]['result']['at'].to_f + std_result['subjects'][key]['result']['cw'].to_f + std_result['subjects'][key]['result']['ob'].to_f + std_result['subjects'][key]['result']['sb'].to_f + std_result['subjects'][key]['result']['pr'].to_f
              tmp_row << std_result['subjects'][key]['result']['at'].to_s
              tmp_row << std_result['subjects'][key]['result']['cw'].to_s
              tmp_row << std_result['subjects'][key]['result']['ob'].to_s
              tmp_row << std_result['subjects'][key]['result']['sb'].to_s
              tmp_row << std_result['subjects'][key]['result']['pr'].to_s
              if courseObj.course_name == "Ten"
                tmp_row << std_result['subjects'][key]['result']['ct'].to_s
              else
                tmp_row << rt.to_s
              end
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
      end
      subject_std = std_subject.find{|val| val.student_id.to_i == std_result['id'].to_i and subject_map.include?(val.subject_id.to_i) }
      
      unless classes.include?(@batch.course.course_name.upcase)
        unless subject_std.blank?
          tmp_row << subject_std.subject.name
        else
          tmp_row << "-"
        end  
      end  
      
      new_book.worksheet(0).insert_row(std_loop, tmp_row)
      
      
      std_loop = std_loop+1
      
    end
    batch_split = @batch.name.split(" ")
    
    group_name = ""
    unless @batch.course.group.blank?
      group_split = @batch.course.group.split(" ")
      unless group_split[2].blank?
        group_split[0] = group_split[0]+" "+group_split[1]
      end
      group_name = group_split[0]
    end
    shift = ""
    version= ""
    unless batch_split[1].blank?
      version = batch_split[1]
    end
    sheet1.add_header("SHAHEED BIR UTTAM LT. ANWAR GIRLS' COLLEGE (Tabulation Sheet : "+@connect_exam_obj.name.to_s+")
 Program :"+@batch.course.course_name.to_s+" || Group :"+group_name.to_s+" || Section :"+@batch.course.section_name.to_s+" || Shift :"+batch_split[0]+" || Session :"+@batch.course.session.to_s+" || Version :"+version+"
      ")
    sheet1.add_footer("TIPS :: M.C = Merit in Class  ||  M.S = Merit in Section  ||  +RT = Raw Total  ||  +CT = Converted Total")
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => @batch.full_name + "-" + @connect_exam_obj.name + ".xls", :type =>  "application/vnd.ms-excel"
  end

  def tabulation_failed_excell
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'tabulation'
    center_align_format = Spreadsheet::Format.new :horizontal_align => :center,  :vertical_align => :middle
    @id = params[:id]
    @connect_exam_obj = ExamConnect.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    @assigned_employee=@batch.all_class_teacher
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
    @exam_comment = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
    row_first = ["Sl","Roll","Student Name"]
    starting_row = 3
    @all_subject_id = []
    @all_group_exams = GroupedExam.find(
      :all,
      :conditions => [
        "connect_exam_id = ?",
        @connect_exam_obj.id
      ]
    )
    unless @all_group_exams.blank?
      @all_exam_group_id = @all_group_exams.map(&:exam_group_id).uniq
      @all_exams = Exam.find(
          :all,
          :conditions => ["exam_group_id IN (?)", @all_exam_group_id]
      )
      unless @all_exams.blank?
        @all_subject_id = @all_exams.map(&:subject_id).uniq
      end
    end
    @report_data['report']['subjects'].each do |sub|
      has_exam = false 
      if @all_subject_id.include?(sub['id'].to_i)
        has_exam = true
      end 
      if has_exam == true 
        row_first << sub['name']
      end
    end  
    row_first << "Total"
    
    new_book.worksheet(0).insert_row(0, row_first)
    
    std_done = []
    start_index = 1
    @report_data['report']['students'].each do |std|
      if std_done.include?(std['id'])
        next
      end
      std_done << std['id']
      if std.blank? || std['first_name'].blank?  
        next
      end 
      row_first = []
      start_index = start_index+1
      s = Student.find(std['id'])
      @config = Configuration.find_by_config_key('StudentAttendanceType')
      @b = Batch.find(s.batch_id)
      @start_date = @connect_exam_obj.attandence_start_date.to_date
      @end_date = @connect_exam_obj.attandence_end_date.to_date
      @leaves=Hash.new { |h, k| h[k] = Hash.new(&h.default_proc) }
      @today = @local_tzone_time.to_date
      working_days=@b.working_days(@start_date.to_date)
      unless @start_date > @local_tzone_time.to_date
        unless @config.config_value == 'Daily'
          unless params[:subject] == '0'
            @subject = Subject.find params[:subject]
            @academic_days=@b.subject_hours(@start_date, @end_date, params[:subject]).values.flatten.compact.count
            @grouped = SubjectLeave.find_all_by_subject_id(@subject.id,  :conditions =>{:month_date => @start_date..@end_date}).group_by(&:student_id)
            if @grouped[s.id].nil?
          @leaves[s.id]['leave']=0
        else
          @leaves[s.id]['leave']=@grouped[s.id].count
        end
        @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
        @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          else
            @academic_days=@b.subject_hours(@start_date, @end_date, 0).values.flatten.compact.count
            @grouped = @b.subject_leaves.find(:all,  :conditions =>{:month_date => @start_date..@end_date, :student_id => s.id})
            if @grouped[s.id].nil?
        @leaves[s.id]['leave']=0
        else
          @leaves[s.id]['leave']=@grouped[s.id].count
            end
        @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
        @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          end
        else
            @start_date_main = @start_date
            
              @start_date = @start_date_main
              unless std['admission_date'].blank?
                if std['admission_date'].to_date > @start_date
                  @start_date = std['admission_date'].to_date
                end
              end

              @academic_days = 0
              @student_leaves = []
              if @end_date >= @start_date
                @academic_days =  @b.find_working_days(@start_date,@end_date).select{|v| v<=@end_date}.count
                @student_leaves = Attendance.find(:all,:conditions =>{:student_id=>s.id,:batch_id=>@b.id,:month_date => @start_date..@end_date})
              end
            
            on_leaves = 0;
            leaves_other = 0;
            leaves_full = 0;
            unless @student_leaves.empty?
              @student_leaves.each do |r|
                if r.student_id == s.id
                  working_days_count=@b.find_working_days(r.month_date.to_date,r.month_date.to_date).select{|v| v<=r.month_date.to_date}.count

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
            @leaves[s.id]['late'] = leaves_other
            @leaves[s.id]['absent'] = leaves_full
            @leaves[s.id]['on_leave'] = on_leaves
            @leaves[s.id]['present'] = @academic_days-on_leaves-leaves_full
            @leaves[s.id]['total']=@academic_days-leaves_full.to_f-(0.5*leaves_other)
            @leaves[s.id]['percent'] = 0.0
            @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          @academic_days =  @b.find_working_days(@start_date_main,@end_date).select{|v| v<=@end_date}.count
        end
      else
        @report = ''
      end 
      total_failed = 0
      student_attendance_percent = 0 	
      student_attendance_mark = 0 	
      unless @leaves[s.id]['percent'].nil? 
        student_attendance_percent =  @leaves[s.id]['percent'] 
      end 	
      if student_attendance_percent.to_f > 0 
        if student_attendance_percent >= 0 and student_attendance_percent < 60 
          student_attendance_mark = 0 	
        elsif student_attendance_percent >= 60 and student_attendance_percent < 71 
          student_attendance_mark = 3 	
        elsif student_attendance_percent >= 71 and student_attendance_percent < 80 
          student_attendance_mark = 4 	
        elsif student_attendance_percent >= 80 
          student_attendance_mark = 5 	
        end 
      end 

      row_first << start_index-1
      row_first << std['class_roll_no']
      row_first << std['first_name']+" "+std['last_name']
      total_mark = 0 
      full_mark_all = 0 
      grades = [] 
      grade_count = {} 
      total_grade = 0 
      failed = false 
      subject_count = 0 
      pass_mark_cq = 0 
      total_cq = 0     
      pass_mark_mcq = 0 
      total_mcq = 0 
      @report_data['report']['subjects'].each do |sub|
        subjectdata = Subject.find(sub['id'].to_i)
        student_id = std['id'].to_s
        subject_id = sub['id'].to_s
        elective_group_id = subjectdata.elective_group_id.to_i
        if @std_subject_hash.nil?
          std_subject = StudentsSubject.find_all_by_batch_id(@batch.id)
          @std_subject_hash = []
          unless std_subject.blank?
            std_subject.each do |std_sub|
              @std_subject_hash << std_sub.student_id.to_s+"_"+std_sub.subject_id.to_s
            end
          end
        end
        @has_exam_student = true
        check_std_subject = false
        if @std_subject_hash.include?(student_id+"_"+subject_id)
          check_std_subject = true
        end
      
        if check_std_subject == false and elective_group_id != 0
          @has_exam_student = false 
        end 
        if @has_exam_student == false
          row_first << ""
          next
        end  
        subject_failed = false
        mcq = 0
        mcq_total = 0
        cq = 0
        cq_total = 0
        att = 0

        exam_marks = 0 
        exam_full_marks = 0 
        @report_data['report']['exams'].each do |rs| 
          if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank? 
            if rs['exam_category'] == '3'
              cq = cq+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i
              cq_total = cq_total+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
            elsif rs['exam_category'] == '4'
              mcq = mcq+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i
              mcq_total = mcq_total+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
            else
              #att = att+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i
              att = att+student_attendance_mark.to_i
            end
            exam_marks = rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i
            exam_full_marks = rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
          end 
        end 
        if mcq_total > 0 && mcq > 0
          if mcq_total == 35 or mcq_total == 40
            mcq = (mcq.to_f/mcq_total.to_f)*25
            mcq = mcq.round()
          end
        end
        if cq_total > 0 && cq > 0
          if cq_total == 90
            cq = (cq.to_f/cq_total.to_f)*70
            cq = cq.round()
          end
          if cq_total == 130
            cq = (cq.to_f/cq_total.to_f)*95
            cq = cq.round()
          end
        end
        main_mark = cq+mcq+student_attendance_mark
        total_mark = total_mark+main_mark.to_f
        grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
        if !grade.blank? and !grade.name.blank?
          if grade.credit_points.to_i == 0
            subject_failed = true
            failed = true
            total_failed = total_failed+1
            row_first << "Failed"
          else
            row_first << ""
          end  
          total_grade = total_grade+grade.credit_points.to_f
        end 
        full_mark_all = full_mark_all+main_mark.to_f
        
      end 
      percentage = 0
      if full_mark_all > 0 &&  total_mark > 0 
        percentage = (total_mark.to_f/full_mark_all.to_f)*100  
      end  
      
      grade_point_avg = 0.00 
      if total_grade > 0 && subject_count > 0 
        grade_point_avg = total_grade.to_f/subject_count.to_f 
        if grade_point_avg > 5 
          grade_point_avg = 5.00 
        end   
      end  
      grade_point_avg = (grade_point_avg.to_f*100).to_f.round() 
      grade_point_avg = grade_point_avg.to_f/100 
      row_first << total_failed
      new_book.worksheet(0).insert_row(start_index, row_first) 
    end  
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => "Failed_list_"+@batch.full_name + "-" + @connect_exam_obj.name + ".xls", :type =>  "application/vnd.ms-excel"
  end

  

  def tabulation_bncd_excell
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'tabulation'
    center_align_format = Spreadsheet::Format.new :horizontal_align => :center,  :vertical_align => :middle
    @id = params[:id]
    @connect_exam_obj = ExamConnect.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    @assigned_employee=@batch.all_class_teacher
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
    @exam_comment = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
    row_first = ["Sl","Roll","Student Name"]
    starting_row = 3
    @all_subject_id = []
    @all_group_exams = GroupedExam.find(
      :all,
      :conditions => [
        "connect_exam_id = ?",
        @connect_exam_obj.id
      ]
    )
    unless @all_group_exams.blank?
      @all_exam_group_id = @all_group_exams.map(&:exam_group_id).uniq
      @all_exams = Exam.find(
          :all,
          :conditions => ["exam_group_id IN (?)", @all_exam_group_id]
      )
      unless @all_exams.blank?
        @all_subject_id = @all_exams.map(&:subject_id).uniq
      end
    end
    @report_data['report']['subjects'].each do |sub|
      has_exam = false 
      if @all_subject_id.include?(sub['id'].to_i)
        has_exam = true
      end 
      if has_exam == true 
        row_first << sub['name']
        row_first << ""
        end_row = starting_row+1
        new_book.worksheet(0).merge_cells(0,starting_row,0,end_row)
        starting_row = starting_row+2
      end
    end  
    row_first << "G. Total"
    row_first << "Total GP"
    row_first << "GPA"
    row_first << "Remarks"
    
    new_book.worksheet(0).insert_row(0, row_first)
    row_first = ["","",""]
    @report_data['report']['subjects'].each do |sub|
      has_exam = false 
      if @all_subject_id.include?(sub['id'].to_i)
        has_exam = true
      end 
      if has_exam == true 
        row_first << "TTL"
        row_first << "GP"
      end
    end 
    row_first << ""
    row_first << ""
    row_first << ""
    row_first << ""
    new_book.worksheet(0).insert_row(1, row_first)

    new_book.worksheet(0).merge_cells(0,0,1,0)
    new_book.worksheet(0).merge_cells(0,1,1,1)
    new_book.worksheet(0).merge_cells(0,2,1,2)
    new_book.worksheet(0).merge_cells(0,starting_row,1,starting_row)
    new_book.worksheet(0).merge_cells(0,starting_row+1,1,starting_row+1)
    new_book.worksheet(0).merge_cells(0,starting_row+2,1,starting_row+2)
    new_book.worksheet(0).merge_cells(0,starting_row+3,1,starting_row+3)
    
    std_done = []
    start_index = 1
    @report_data['report']['students'].each do |std|
      if std_done.include?(std['id'])
        next
      end
      std_done << std['id']
      if std.blank? || std['first_name'].blank?  
        next
      end 
      row_first = []
      start_index = start_index+1
      s = Student.find(std['id'])
      @config = Configuration.find_by_config_key('StudentAttendanceType')
      @b = Batch.find(s.batch_id)
      @start_date = @connect_exam_obj.attandence_start_date.to_date
      @end_date = @connect_exam_obj.attandence_end_date.to_date
      @leaves=Hash.new { |h, k| h[k] = Hash.new(&h.default_proc) }
      @today = @local_tzone_time.to_date
      working_days=@b.working_days(@start_date.to_date)
      unless @start_date > @local_tzone_time.to_date
        unless @config.config_value == 'Daily'
          unless params[:subject] == '0'
            @subject = Subject.find params[:subject]
            @academic_days=@b.subject_hours(@start_date, @end_date, params[:subject]).values.flatten.compact.count
            @grouped = SubjectLeave.find_all_by_subject_id(@subject.id,  :conditions =>{:month_date => @start_date..@end_date}).group_by(&:student_id)
            if @grouped[s.id].nil?
          @leaves[s.id]['leave']=0
        else
          @leaves[s.id]['leave']=@grouped[s.id].count
        end
        @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
        @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          else
            @academic_days=@b.subject_hours(@start_date, @end_date, 0).values.flatten.compact.count
            @grouped = @b.subject_leaves.find(:all,  :conditions =>{:month_date => @start_date..@end_date, :student_id => s.id})
            if @grouped[s.id].nil?
        @leaves[s.id]['leave']=0
        else
          @leaves[s.id]['leave']=@grouped[s.id].count
            end
        @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
        @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          end
        else
            @start_date_main = @start_date
            
              @start_date = @start_date_main
              unless std['admission_date'].blank?
                if std['admission_date'].to_date > @start_date
                  @start_date = std['admission_date'].to_date
                end
              end

              @academic_days = 0
              @student_leaves = []
              if @end_date >= @start_date
                @academic_days =  @b.find_working_days(@start_date,@end_date).select{|v| v<=@end_date}.count
                @student_leaves = Attendance.find(:all,:conditions =>{:student_id=>s.id,:batch_id=>@b.id,:month_date => @start_date..@end_date})
              end
            
            on_leaves = 0;
            leaves_other = 0;
            leaves_full = 0;
            unless @student_leaves.empty?
              @student_leaves.each do |r|
                if r.student_id == s.id
                  working_days_count=@b.find_working_days(r.month_date.to_date,r.month_date.to_date).select{|v| v<=r.month_date.to_date}.count

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
            @leaves[s.id]['late'] = leaves_other
            @leaves[s.id]['absent'] = leaves_full
            @leaves[s.id]['on_leave'] = on_leaves
            @leaves[s.id]['present'] = @academic_days-on_leaves-leaves_full
            @leaves[s.id]['total']=@academic_days-leaves_full.to_f-(0.5*leaves_other)
            @leaves[s.id]['percent'] = 0.0
            @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          @academic_days =  @b.find_working_days(@start_date_main,@end_date).select{|v| v<=@end_date}.count
        end
      else
        @report = ''
      end 

      student_attendance_percent = 0 	
      student_attendance_mark = 0 	
      unless @leaves[s.id]['percent'].nil? 
        student_attendance_percent =  @leaves[s.id]['percent'] 
      end 	
      if student_attendance_percent.to_f > 0 
        if student_attendance_percent >= 0 and student_attendance_percent < 60 
          student_attendance_mark = 0 	
        elsif student_attendance_percent >= 60 and student_attendance_percent < 71 
          student_attendance_mark = 3 	
        elsif student_attendance_percent >= 71 and student_attendance_percent < 80 
          student_attendance_mark = 4 	
        elsif student_attendance_percent >= 80 
          student_attendance_mark = 5 	
        end 
      end 

      row_first << start_index-1
      row_first << std['class_roll_no']
      row_first << std['first_name']+" "+std['last_name']
      total_mark = 0 
      full_mark_all = 0 
      grades = [] 
      grade_count = {} 
      total_grade = 0 
      failed = false 
      subject_count = 0 
      pass_mark_cq = 0 
      total_cq = 0     
      pass_mark_mcq = 0 
      total_mcq = 0 
      @report_data['report']['subjects'].each do |sub|
        subjectdata = Subject.find(sub['id'].to_i)
        student_id = std['id'].to_s
        subject_id = sub['id'].to_s
        elective_group_id = subjectdata.elective_group_id.to_i
        if @std_subject_hash.nil?
          std_subject = StudentsSubject.find_all_by_batch_id(@batch.id)
          @std_subject_hash = []
          unless std_subject.blank?
            std_subject.each do |std_sub|
              @std_subject_hash << std_sub.student_id.to_s+"_"+std_sub.subject_id.to_s
            end
          end
        end
        @has_exam_student = true
        check_std_subject = false
        if @std_subject_hash.include?(student_id+"_"+subject_id)
          check_std_subject = true
        end
      
        if check_std_subject == false and elective_group_id != 0
          @has_exam_student = false 
        end 
        if @has_exam_student == false
          row_first << ""
          row_first << ""
          next
        end  
        subject_failed = false
        mcq = 0
        mcq_total = 0
        cq = 0
        cq_total = 0
        att = 0

        exam_marks = 0 
        exam_full_marks = 0 
        @report_data['report']['exams'].each do |rs| 
          if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank? 
            if rs['exam_category'] == '3'
              cq = cq+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i
              cq_total = cq_total+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
            elsif rs['exam_category'] == '4'
              mcq = mcq+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i
              mcq_total = mcq_total+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
            else
              #att = att+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i
              att = att+student_attendance_mark.to_i
            end
            exam_marks = rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i
            exam_full_marks = rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
          end 
        end 
        if mcq_total > 0 && mcq > 0
          if mcq_total == 35 or mcq_total == 40
            mcq = (mcq.to_f/mcq_total.to_f)*25
            mcq = mcq.round()
          end
        end
        if cq_total > 0 && cq > 0
          if cq_total == 90
            cq = (cq.to_f/cq_total.to_f)*70
            cq = cq.round()
          end
          if cq_total == 130
            cq = (cq.to_f/cq_total.to_f)*95
            cq = cq.round()
          end
        end
        main_mark = cq+mcq+student_attendance_mark
        row_first << main_mark.to_i
        total_mark = total_mark+main_mark.to_f
        grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
        if !grade.blank? and !grade.name.blank?
          if grade.credit_points.to_i == 0
            subject_failed = true
            failed = true
          end
          total_grade = total_grade+grade.credit_points.to_f
          row_first << sprintf( "%0.01f", grade.credit_points.to_f)
        end 
        full_mark_all = full_mark_all+main_mark.to_f
        
      end 
      percentage = 0
      if full_mark_all > 0 &&  total_mark > 0 
        percentage = (total_mark.to_f/full_mark_all.to_f)*100  
      end  
      
      grade_point_avg = 0.00 
      if total_grade > 0 && subject_count > 0 
        grade_point_avg = total_grade.to_f/subject_count.to_f 
        if grade_point_avg > 5 
          grade_point_avg = 5.00 
        end   
      end  
      grade_point_avg = (grade_point_avg.to_f*100).to_f.round() 
      grade_point_avg = grade_point_avg.to_f/100 
      row_first << sprintf( "%0.02f", total_mark) 
      row_first << sprintf( "%0.02f", total_grade)
      row_first << sprintf( "%0.02f", grade_point_avg)
      if failed
        row_first << "Failed"
      else
        row_first << "Passed"
      end  
      new_book.worksheet(0).insert_row(start_index, row_first) 
    end  
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => "Tabulation_"+@batch.full_name + "-" + @connect_exam_obj.name + ".xls", :type =>  "application/vnd.ms-excel"
  end  
  
  def tabulation
    @id = params[:id]
    @connect_exam_obj = ExamConnect.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id)
    @assigned_employee=@batch.all_class_teacher
    
    #    pdf_name = "tabulation_connect_exam_"+@connect_exam_obj.id.to_s+".pdf"
    #    dirname = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"tabulation","0"+@connect_exam_obj.id.to_s)
    #    unless File.directory?(dirname)
    #      FileUtils.mkdir_p(dirname)
    #    end
    #    #    FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s))
    #    file_name = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"tabulation","0"+@connect_exam_obj.id.to_s,pdf_name)
    #    champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    #    api_from = champs21_config['real_from']
  
      
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
     
  
       
    @exam_comment = ExamConnectComment.find_all_by_exam_connect_id(@connect_exam_obj.id)
    if (MultiSchool.current_school.id == 280 && @connect_exam_obj.result_type==2) or 
        (MultiSchool.current_school.id == 323 && @connect_exam_obj.result_type==6)
      render :pdf => 'tabulation',
        :orientation => 'Landscape', :zoom => 1.00,
        :page_size => 'Legal',
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}} 
    elsif (MultiSchool.current_school.id == 362)
        render :pdf => 'tabulation',
          :orientation => 'Landscape', :zoom => 1.00,
          :page_size => 'A2',
          :margin => {    :top=> 10,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}} 
    else
      render :pdf => 'tabulation',
        :orientation => 'Landscape', :zoom => 1.00,
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    end
    
  end
  def marksheet_excell
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'marksheet'
    
    @id = params[:id]
    @subject_id = params[:subject_id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id) 
    @subject = Subject.find(@subject_id)
    @assigned_employee=@batch.all_class_teacher
    @grades = @batch.grading_level_list

    @employee_sub = EmployeesSubject.find_by_subject_id(@subject_id)
    if !@employee_sub.nil?
      @employee = Employee.find(@employee_sub.employee_id)
    end

    if MultiSchool.current_school.code == "nascd" && @connect_exam_obj.result_type == 7
      get_continues(@id,@batch.id)
      
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end 
      @report_data['report']['students'].each do |std|
        @report_data['report']['exams'] = @report_data['report']['all_result'][std['id']]['exams']
        break
      end  
      row_first = ['Roll','Student Name']
      unless @report_data['report']['exams'].blank?
        @report_data['report']['exams'].each do |report|
          row_first << report['exam_name']
        end  
      end 
    
      new_book.worksheet(0).insert_row(0, row_first)
      iloop = 0
      @report_data['report']['students'].each do |std|
        iloop = iloop+1
        @student = Student.find_by_id(std['id'].to_i,:include=>["student_category"])
        rows = [@student.class_roll_no,@student.full_name]
        @report_data['report']['exams'] = @report_data['report']['all_result'][std['id']]['exams']
        @report_data['report']['exams'].each do |report|
          if !report['result'][report['exam_id']].blank? and !report['result'][report['exam_id']][@subject.id.to_s].blank? and !report['result'][report['exam_id']][@subject.id.to_s]['remarks'].blank?
            remarks_exam = report['result'][report['exam_id']][@subject.id.to_s]['remarks']
            if remarks_exam.to_i == 1
              rows << 40
            end
            if remarks_exam.to_i == 2
              rows << 35
            end
            if remarks_exam.to_i == 3
              rows << 30
            end
            if remarks_exam.to_i == 4
              rows << 20
            end  
          else
            rows << ""
          end        
        end  
        new_book.worksheet(0).insert_row(iloop, rows)
      end  
    else
    
      get_subject_mark_sheet(@id,@subject_id)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
      
      row_first = ['Roll','Student Name']
      @report_data['result']['ALL'].each do |rs|
        row_first << rs['name']+"( "+rs['maximum_marks']+" ) "
      end
      new_book.worksheet(0).insert_row(0, row_first)
      iloop = 0
      unless @report_data['result']['al_students'].blank?
        @report_data['result']['al_students'].each do |std|
          iloop = iloop+1
          rows = [std['class_roll_no'],std['name']]
          @report_data['result']['ALL'].each do |rs|
            if !rs['students'].blank? && !rs['students'][std['id']].blank? && !rs['students'][std['id']]['score'].blank?
              rows << rs['students'][std['id']]['score']
            else
              rows << ""
            end
          end
          new_book.worksheet(0).insert_row(iloop, rows)
        end
      end
    end  
    
    batch_split = @batch.name.split(" ")
    
    group_name = ""
    unless @batch.course.group.blank?
      group_split = @batch.course.group.split(" ")
      unless group_split[2].blank?
        group_split[0] = group_split[0]+" "+group_split[1]
      end
      group_name = group_split[0]
    end
    shift = ""
    version= ""
    unless batch_split[1].blank?
      version = batch_split[1]
    end
    school_data = School.find_by_id(MultiSchool.current_school.id)
    sheet1.add_header(school_data.name+" (Mark Sheet : "+@subject.name.to_s+")
 Program :"+@batch.course.course_name.to_s+" || Group :"+group_name.to_s+" || Section :"+@batch.course.section_name.to_s+" || Shift :"+batch_split[0]+" || Session :"+@batch.course.session.to_s+" || Version :"+version+"
      ")
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => @batch.full_name + "-" + @subject.name + ".xls", :type =>  "application/vnd.ms-excel"
  end
  def marksheet    
    @id = params[:id]
    @subject_id = params[:subject_id]
    @connect_exam_obj = ExamConnect.active.find(@id)
    @batch = Batch.find(@connect_exam_obj.batch_id) 
    @subject = Subject.find(@subject_id)
    @assigned_employee=@batch.all_class_teacher
    
    
    #    pdf_name = "marksheet_connect_exam_"+@subject_id.to_s+"_"+@connect_exam_obj.id.to_s+".pdf"
    #    dirname = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"marksheet","0"+@connect_exam_obj.id.to_s)
    #    unless File.directory?(dirname)
    #      FileUtils.mkdir_p(dirname)
    #    end
    #    FileUtils.chmod_R(0777, Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s))
    #    file_name = Rails.root.join('public','result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"marksheet","0"+@connect_exam_obj.id.to_s,pdf_name)
    #    
    #    champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    #    api_from = champs21_config['from']
    
    #    if File.file?(file_name) && Rails.cache.exist?("marksheet_#{@id}_#{@subject_id}") && api_from != "local"
    #      FileUtils.chown 'champs21','champs21',file_name
    #      redirect_to "/result_pdf/0"+MultiSchool.current_school.id.to_s+"/0"+@batch.id.to_s+"/marksheet/0"+@connect_exam_obj.id.to_s+"/"+pdf_name
    #    else
    @grades = @batch.grading_level_list
    
    @employee_sub_all = EmployeesSubject.find_all_by_subject_id(@subject_id)
    @employee_all = []
    if !@employee_sub_all.nil?
      @employee_sub_all.each do |emp_sub|
        @employee_all << Employee.find(emp_sub.employee_id)
      end
    end

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
        :orientation => 'Landscape', :zoom => 1.00,
        :page_size => 'A4',
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    elsif MultiSchool.current_school.id == 323
      render :pdf => 'marksheet',
        :orientation => 'Portrait', :zoom => 1.00,
        :page_size => 'A4',
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    elsif MultiSchool.current_school.id == 348
      render :pdf => 'marksheet',
        :orientation => 'Landscape', :zoom => 1.00,
        :page_size => 'A4',
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    else 
      render :pdf => 'marksheet',
        :orientation => 'Landscape', :zoom => 1.00,
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
    
    if !current_user.admin? and !current_user.employee? and @connect_exam_obj.is_common.to_i == 0
      student_list = @connect_exam_obj.students
      if student_list
        std_array = student_list.split(",")
        if !std_array.include?(params[:student].to_s)
          flash[:notice] = "Your result is pending. please contact school admin"
          redirect_to :controller=>"user", :action=>"dashboard"
        end
      end
      
    end
    
    
    
    
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
    
    if !current_user.admin? and !current_user.employee? and @connect_exam_obj.is_common.to_i == 0
      student_list = @connect_exam_obj.students
      if student_list
        std_array = student_list.split(",")
        if !std_array.include?(params[:student].to_s)
          flash[:notice] = "Your result is pending. please contact school admin"
          redirect_to :controller=>"user", :action=>"dashboard"
        end
      end 
    end
    
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
    
    #    pdf_name = "continues_connect_exam_"+@student.id.to_s+"_"+@connect_exam_obj.id.to_s+".pdf"
    #    dirname = Rails.root.join('public','all_result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"continues","0"+@connect_exam_obj.id.to_s)
    #    unless File.directory?(dirname)
    #      FileUtils.mkdir_p(dirname)
    #    end
    #    FileUtils.chmod_R(0777, Rails.root.join('public','all_result_pdf',"0"+MultiSchool.current_school.id.to_s))
    #    file_name = Rails.root.join('public','all_result_pdf',"0"+MultiSchool.current_school.id.to_s,"0"+@batch.id.to_s,"continues","0"+@connect_exam_obj.id.to_s,pdf_name)
    
    @exam_comment = ExamConnectComment.find_by_exam_connect_id_and_student_id(@connect_exam_obj.id,@student.id)
    render_connect_exam("generated_report5_pdf",false)

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
        
        :save_only    => for_save,
        #:orientation => 'Portrait',
        :orientation => 'Landscape',
        #:page_size => 'Legal',
        #:zoom => 1,
        :margin => {:top=> 2,
        :bottom => 2,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    elsif MultiSchool.current_school.id == 280
      render :pdf => template,
        
        :save_only    => for_save,
        :orientation => 'Landscape',
        :margin => {:top=> 3,
        :bottom => 3,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    elsif MultiSchool.current_school.code == "ess"
      if @connect_exam_obj.result_type == 1 
        render :pdf => template,
          :save_only    => for_save,
          :orientation => 'Landscape',
          :grayscale => true,
          :margin => {:top=> 10,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      else
        render :pdf => template,
        :save_only    => for_save,
        :orientation => 'Portrait',
        :margin => {:top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      end
    elsif MultiSchool.current_school.code == "nascd"
      if @connect_exam_obj.result_type == 14 or @connect_exam_obj.result_type == 15  or @connect_exam_obj.result_type == 16
        render :pdf => template, 
        :save_only    => for_save,
        :orientation => 'Portrait',
        :margin => {    :top=> 0,
        :bottom => 0,
        :left=> 7,
        :right => 5},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      else  
      render :pdf => template, 
        :save_only    => for_save,
        :orientation => 'Portrait',
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      end   
    elsif MultiSchool.current_school.id == 355
      
      if @connect_exam_obj.result_type == 1
        render :pdf => template,
          
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
          
          :save_only    => for_save,
          :orientation => 'Portrait',
          :margin => {    :top=> 10,
          :bottom => 40,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer_oisd.html'}}
        
      end
    elsif MultiSchool.current_school.id == 352 or MultiSchool.current_school.id == 346 or MultiSchool.current_school.id == 324 or MultiSchool.current_school.id == 357
      if (MultiSchool.current_school.id == 352 and @connect_exam_obj.result_type >= 20) or @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 5 or @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 9 or @connect_exam_obj.result_type.to_i == 11 or @connect_exam_obj.result_type.to_i == 12 or @connect_exam_obj.result_type.to_i == 13 or @connect_exam_obj.result_type.to_i == 14 or @connect_exam_obj.result_type.to_i == 15 or @connect_exam_obj.result_type.to_i == 16 or @connect_exam_obj.result_type.to_i == 18 or @connect_exam_obj.result_type.to_i == 19 or @connect_exam_obj.result_type.to_i == 17 or @connect_exam_obj.result_type.to_i == 22 or @connect_exam_obj.result_type.to_i == 21 or @connect_exam_obj.result_type.to_i >= 23
        render :pdf => template,
          
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
      if (@connect_exam_obj.result_type == 8 or @connect_exam_obj.result_type == 9 or @connect_exam_obj.result_type == 10 or @connect_exam_obj.result_type == 12 or @connect_exam_obj.result_type == 14) and @transcript.blank?
        render :pdf => template,
          
          :save_only    => for_save,
          :orientation => 'Landscape',
          :encoding =>    'utf8',
          :margin => {    :top=> 0,
          :bottom => 0,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      elsif !@transcript.blank?  
        render :pdf => template,
          
          :save_only    => for_save,
          :orientation => 'Portrait',
          
          :encoding =>    'utf8',
          :margin => {    :top=> 10,
          :bottom => 30,
          :left=> 8,
          :right => 8},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_footer_sis_transcript.html'}}
      else
        render :pdf => template,
          
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
      if MultiSchool.current_school.id == 319  and (@connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 4 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 5 or @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 8 or @connect_exam_obj.result_type == 9 or @connect_exam_obj.result_type == 11 or @connect_exam_obj.result_type == 12)
        render :pdf => template,
          
          :save_only    => for_save,
          :orientation => 'Portrait',
          :margin => {    :top=> 5,
          :bottom => 5,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      elsif MultiSchool.current_school.id == 323 and (@connect_exam_obj.result_type == 9 or @connect_exam_obj.result_type == 11 or @connect_exam_obj.result_type == 5 or @connect_exam_obj.result_type == 8 or @connect_exam_obj.result_type == 10 or @connect_exam_obj.result_type == 13 or @connect_exam_obj.result_type == 14)
        render :pdf => template,
          
          :save_only    => for_save,
          :orientation => 'Portrait',
          :margin => {    :top=> 5,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      else
        render :pdf => template,
          
          :save_only    => for_save,
          :orientation => 'Portrait'
      end
    elsif  MultiSchool.current_school.id == 356
      if @connect_exam_obj.result_type != 3
        render :pdf => template,
          
          :save_only    => for_save,
          :orientation => 'Portrait',
          :margin => {    :top=> 10,
          :bottom => 30,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_footer_baghc.html'}}
      else
        render :pdf => template,
          
          :save_only    => for_save,
          :orientation => 'Landscape',
          :page_size => 'Legal',
          :margin => {    :top=> 10,
          :bottom => 30,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_footer_baghc_landscape.html'}}
      end  
    elsif  MultiSchool.current_school.id == 312 or MultiSchool.current_school.id == 2 
      if @connect_exam_obj.result_type != 1 and @connect_exam_obj.result_type != 6 and @connect_exam_obj.result_type != 7 and @connect_exam_obj.result_type != 10 and @connect_exam_obj.result_type != 11 and @connect_exam_obj.result_type != 16 and @connect_exam_obj.result_type != 17
        render :pdf => template,
          
          :save_only    => for_save,
          :orientation => 'Portrait',
          :margin => { :top=> 5,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      else
            
        render :pdf => template,
          
          :save_only    => for_save,
          :orientation => 'Landscape',
          :margin => {  :top=> 5,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      end
    elsif  MultiSchool.current_school.id == 342 or MultiSchool.current_school.id == 324 or MultiSchool.current_school.id == 3
      render :pdf => template,
        
        :save_only    => for_save,
        :orientation => 'Portrait',
        :margin => {:top=> 10,
        :bottom => 0,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}} 
    elsif  MultiSchool.current_school.id == 340  
      if @connect_exam_obj.result_type == 14 or @connect_exam_obj.result_type == 13
        render :pdf => template,
          
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
  def get_continues(connect_exam_id,batch_id,unsolved_exam = 0)
    require 'net/http'
    require 'uri'
    require "yaml"
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    api_uri = URI(api_endpoint + "api/report/continues")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"connect_exam_id"=>connect_exam_id,"batch_id"=>batch_id,"call_from_web"=>1,"is_unsolved"=>unsolved_exam,"user_secret" =>session[:api_info][0]['user_secret']})
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
  def finding_data_sagc_21
    if @total_std.blank?
      @total_std_batch = 0
      @total_std = 0
      @student_list = []
      @student_list_batch = []
      @subject_highest = {}
      @student_avg_mark = {}
      @student_result = []
      @subject_result = {}
      @subject_code = {}
      @student_subject_marks = {}
      @absent_in_all_subject = 0
      @section_wise_position = {}	
      @failed_partial_absent = {}	
      @failed_appeared_absent = {}
      @grade_count = {}
      @exam_type_array = []
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
        @group_name_upper = group_name
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

          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end

          std_list = []
          tab['students'].each do |std| 
            std_list << std['id'].to_i
          end
          @student_all_tab = []
          unless std_list.blank?
            @student_all_tab = Student.find_all_by_id(std_list)
            all_total_std_subject = StudentsSubject.find_all_by_student_id(std_list)
          end
          tab['students'].each do |std| 
            total_failed = 0	
            total_failed_appaered = 0
            full_absent = true
            failed_on_appread = false
            grand_total = 0
            grand_total_with_fraction = 0
            grand_grade_point = 0
            u_grade = 0
            grand_total_main = 0
            grade_poin_main = 0

            @student_tab = @student_all_tab.find{|val| val.id.to_i == std['id'].to_i }
            if @student_tab.blank?
              next
            end
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
            total_std_subject = all_total_std_subject.select{|val| val.student_id.to_i == std['id'].to_i }
            std_subject_id = total_std_subject.map(&:subject_id)
            total_subject = 0
            subject_grade_done = []
            subject_array = []
            tab['subjects'].each do |sub|
              if subject_array.include?(sub['id'].to_i)
                next
              end
              subject_array << sub['id'].to_i
              if !@subject_code.blank? && !@subject_code[sub['code']].blank?	
                main_sub_id = @subject_code[sub['code']]
              else	
                @subject_code[sub['code'].to_s] = sub['code']	
                main_sub_id = sub['code']	
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
                if fourth_subject == false && sub['subject_group_id'].to_i == 0 &&  sub['grade_subject'].to_i == 0 and !subject_grade_done.include?(sub['id'].to_i)
                  total_subject = total_subject+1
                end
                subject_failed = false
                appeared = false
                four_subject_failed = false
                total_mark_subject = 0
                full_mark_subject = 0
                main_mark = 0
                total_sb = 0
                total_ob = 0
                total_pr = 0
            
                full_sb = 0
                full_ob = 0
                full_pr = 0
                tab['exams'].each do |rs|
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    if rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_s != "AB"
                      appeared = true
                      full_absent = false
                    end
                    if rs['exam_category'] == '3'
                      total_sb = total_sb+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      full_sb = full_sb+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                    end
                    if rs['exam_category'] == '4'
                      total_ob = total_ob+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      full_ob = full_ob+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                    end
                    if rs['exam_category'] == '5'
                      total_pr = total_pr+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      full_pr = full_pr+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                    end
                  end
                end
                converted_sb_full = 50
                converted_ob_full = 25
                if full_sb > 0
                  if full_sb.to_i == 40
                    converted_sb_full = 70
                  end
                  if full_sb.to_i == 50
                    converted_sb_full = 100
                  end
                  if total_sb > 0
                    total_sb = (total_sb.to_f/full_sb.to_f)*converted_sb_full
                    grand_total_with_fraction = grand_total_with_fraction+total_sb.to_f
                    total_sb = total_sb.round()
                  end  
                end
                if full_ob > 0
                  converted_ob_full = 100-converted_sb_full-full_pr
                  if total_ob > 0
                    total_ob = (total_ob.to_f/full_ob.to_f)*converted_ob_full
                    grand_total_with_fraction = grand_total_with_fraction+total_ob.to_f
                    total_ob = total_ob.round()
                  end
                end
                
                if full_pr > 0
                  if total_pr > 0
                      grand_total_with_fraction = grand_total_with_fraction+total_pr.to_f
                      total_pr = total_pr.round()
                  end
                end
                total_mark_subject = total_ob+total_sb+total_pr
                

                if sub['subject_group_id'].to_i > 0
                  tab['subjects'].each do |sub2|
                    if subject_array.include?(sub2['id'].to_i) or sub['subject_group_id'].to_i != sub2['subject_group_id'].to_i
                      next
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
                    subject_grade_done << sub2['id'].to_i
                    full_mark_subject2 = 0
                    total_mark_subject2 = 0
                    total_sb2 = 0
                    total_ob2 = 0
                    total_pr2 = 0
                
                    full_sb2 = 0
                    full_ob2 = 0
                    full_pr2 = 0
                    tab['exams'].each do |rs|
                      if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub2['id']].blank? and !rs['result'][rs['exam_id']][sub2['id']][std['id']].blank?
                        if rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_s != "AB"
                          appeared = true
                          full_absent = false
                        end
                        if rs['exam_category'] == '3'
                          total_sb2 = total_sb2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                          full_sb2 = full_sb2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                        end
                        if rs['exam_category'] == '4'
                          total_ob2 = total_ob2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                          full_ob2 = full_ob2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                        end
                        if rs['exam_category'] == '5'
                          total_pr2 = total_pr2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                          full_pr2 = full_pr2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                        end
                      end
                    end
                    converted_sb_full2 = 50
                    converted_ob_full2 = 25
                    if full_sb2 > 0
                      if full_sb2.to_i == 40
                        converted_sb_full2 = 70
                      end
                      if full_sb2.to_i == 50
                        converted_sb_full2 = 100
                      end
                      total_sb2_before = total_sb2
                      if total_sb2 > 0
                        total_sb2 = (total_sb2.to_f/full_sb2.to_f)*converted_sb_full2
                        total_sb2 = total_sb2.round()
                      end  
                      t_sb_before= total_sb
                      converted_main_full_sb = converted_sb_full2+converted_sb_full
                      total_sb =  total_sb+total_sb2
                      sb_main = (total_sb.round.to_f/converted_main_full_sb.to_f)*100
                      grade = GradingLevel.percentage_to_grade(sb_main, @batch.id)
                      if !grade.blank? and !grade.name.blank?
                        if grade.credit_points.to_i == 0 || (total_sb.round < 34 && converted_main_full_sb == 100)
                            if fourth_subject.blank?
                              u_grade = u_grade+1 
                              subject_failed = true
                            else
                              four_subject_failed = true
                            end
                        end
                      end  
                    end
                    if full_ob > 0 or full_ob2 > 0
                      converted_ob_full2 = 100-converted_sb_full2-full_pr2
                      if total_ob2 > 0
                        total_ob2 = (total_ob2.to_f/full_ob2.to_f)*converted_ob_full2
                        total_ob2 = total_ob2.round()
                        
                      end
                      if full_ob2 == 0
                          converted_ob_full2 = 0
                      end
                      converted_ob_full_main = converted_ob_full2+converted_ob_full
                      total_ob = total_ob2+total_ob

                      if (total_ob.round < 16 && converted_ob_full_main == 50) || (total_ob.round < 10 && converted_ob_full_main == 30) || (total_ob.round < 20 && converted_ob_full_main == 60)
                          if fourth_subject.blank?
                            u_grade = u_grade+1 
                            subject_failed = true
                          else
                            four_subject_failed = true
                          end
                      end 
                    end
                    
                    
                    if full_pr2 > 0
                      if total_pr2 > 0 || total_pr > 0
                          total_pr2 = total_pr2.round()
                          total_pr =  total_pr+total_pr2
                          
                          if total_pr < 16
                            if fourth_subject.blank?
                              u_grade = u_grade+1 
                              subject_failed = true
                            else
                              four_subject_failed = true
                            end
                          end
                      else
                        if fourth_subject.blank?
                          u_grade = u_grade+1 
                          subject_failed = true
                        else
                          four_subject_failed = true
                        end
                      end
                    end
                  
                    
                    total_mark_subject2 = total_ob+total_sb+total_pr
                    grand_total = grand_total+total_mark_subject2
                    main_mark = (total_mark_subject2.to_f/200.00)*100
                    main_mark = main_mark.round()

                    grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                  
                    if !grade.blank? and !grade.name.blank?
                      if fourth_subject.blank? && subject_failed == false
                        grand_grade_point = grand_grade_point+grade.credit_points.to_f
                      elsif four_subject_failed == false && !fourth_subject.blank?
                        new_grade_point = grade.credit_points.to_f-2
                        if new_grade_point > 0
                          grand_grade_point = grand_grade_point+new_grade_point.to_f
                        end
                      end
                    end
                  end
                end

                if @student_subject_marks[sub['id'].to_i].blank?
                  @student_subject_marks[sub['id'].to_i] = {}
                end
                @student_subject_marks[sub['id'].to_i][std['id'].to_i] = total_mark_subject


                if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)

                  if appeared
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = total_mark_subject
                  else
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = "AB"
                  end     


                  @student_result[loop_std]['subjects'][main_sub_id]['result']['rt'] = total_mark_subject

                  @student_result[loop_std]['subjects'][main_sub_id]['result']['ct'] = total_mark_subject.round()


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
                  
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['parcent'] = main_mark

                  grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                  if !grade.blank? && !grade.name.blank?
                    if subject_failed == true or four_subject_failed == true
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = "F"
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = grade.name
                    end
                  end
                  if !grade.blank? && !grade.name.blank? && sub['grade_subject'].to_i != 1
                    if subject_failed == true
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

                        @student_result[loop_std]['subject_failed'] << sub['code']+"-"+total_mark_subject.round().to_s
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

                if @subject_highest[sub['id'].to_i].blank?
                  @subject_highest[sub['id'].to_i] = total_mark_subject
                elsif total_mark_subject.to_f > @subject_highest[sub['id'].to_i].to_f
                  @subject_highest[sub['id'].to_i] = total_mark_subject.to_f
                end

              end

            end 

            grade_point_avg = 0
            if grand_grade_point > 0 && total_subject > 0
              grade_point_avg = grand_grade_point.to_f/total_subject.to_f
              grade_point_avg = grade_point_avg.round(2)
              if grade_point_avg > 5
                grade_point_avg = 5.00     
              end
            end
            grand_total_with_fraction = sprintf( "%0.02f", grand_total_with_fraction)
            grand_total_with_fraction = grand_total_with_fraction.to_f
            if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
              @total_std_batch = @total_std_batch+1
              if full_absent
                @absent_in_all_subject = @absent_in_all_subject+1
              end
              grade_point_avg = 0
              if grand_grade_point > 0 && total_subject > 0
                grade_point_avg = grand_grade_point.to_f/total_subject.to_f
                grade_point_avg = grade_point_avg.round(2)
                if grade_point_avg > 5
                  grade_point_avg = 5.00
                end
              end
              @student_result[loop_std]['gpa'] = grand_grade_point
              @student_result[loop_std]['grand_total'] = grand_total
              @student_result[loop_std]['grand_total_with_fraction'] = grand_total_with_fraction
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
                if !gradeObj.blank? and !gradeObj.name.blank?
                  if @grade_count[gradeObj.name].blank?
                    @grade_count[gradeObj.name] = 1
                  else
                    @grade_count[gradeObj.name] = @grade_count[gradeObj.name]+1
                  end
                end
              end 
              if std_group_name == group_name or connect_exam_id.to_i == @connect_exam_obj.id
                @student_list << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
                if @section_wise_position[batch_data.id].blank?
                  @section_wise_position[batch_data.id] = []
                end
                @section_wise_position[batch_data.id] << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
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
      @student_position = {}
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

    end
  end
  def finding_data_19
    if @total_std.blank?
      @total_std_batch = 0
      @total_std = 0
      @student_list = []
      @student_list_batch = []
      @subject_highest = {}
      @student_avg_mark = {}
      @student_result = []
      @subject_result = {}
      @subject_code = {}
      @student_subject_marks = {}
      @absent_in_all_subject = 0
      @section_wise_position = {}	
      @failed_partial_absent = {}	
      @failed_appeared_absent = {}
      @grade_count = {}
      @exam_type_array = []
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
        @group_name_upper = group_name
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

          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end

          std_list = []
          tab['students'].each do |std| 
            std_list << std['id'].to_i
          end
          @student_all_tab = []
          unless std_list.blank?
            @student_all_tab = Student.find_all_by_id(std_list)
            all_total_std_subject = StudentsSubject.find_all_by_student_id(std_list)
          end
          tab['students'].each do |std| 
            total_failed = 0	
            total_failed_appaered = 0
            full_absent = true
            failed_on_appread = false
            grand_total = 0
            grand_total_with_fraction = 0
            grand_grade_point = 0
            u_grade = 0
            grand_total_main = 0
            grade_poin_main = 0
            @student_tab = @student_all_tab.find{|val| val.id.to_i == std['id'].to_i }
            if @student_tab.blank?
              next
            end  
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
            total_std_subject = all_total_std_subject.select{|val| val.student_id.to_i == std['id'].to_i }
            std_subject_id = total_std_subject.map(&:subject_id)
            total_subject = 0
            subject_grade_done = []
            subject_array = []
            tab['subjects'].each do |sub|
              if subject_array.include?(sub['id'].to_i)
                next
              end
              subject_array << sub['id'].to_i
              if !@subject_code.blank? && !@subject_code[sub['code']].blank?	
                main_sub_id = @subject_code[sub['code']]
              else	
                @subject_code[sub['code'].to_s] = sub['code']	
                main_sub_id = sub['code']	
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
                if fourth_subject == false &&  sub['grade_subject'].to_i == 0 and !subject_grade_done.include?(sub['id'].to_i)
                 unless subject_grade_done.include?(sub['id'].to_i)
                    total_subject = total_subject+1
                 end
                end
                subject_failed = false
                appeared = false
                four_subject_failed = false
                total_mark_subject = 0
                full_mark_subject = 0
                sb = 0
                ob = 0
                pr = 0
                at = 0
                cw = 0
                full_sb = 0
                full_ob = 0
                full_pr = 0
                tab['exams'].each do |rs|
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    full_mark_subject = full_mark_subject+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                    if rs['exam_category'] == '3' 
                      sb = sb+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      full_sb = full_sb+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                    end 
                    if rs['exam_category'] == '4' 
                      ob = ob+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      full_ob = full_ob+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                    end 
                    if rs['exam_category'] == '5'
                      pr = pr+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      full_pr = full_ob+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                    end 
                    if rs['exam_category'] == '2'
                      at = at+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                    end 
                    if rs['exam_category'] == '1'
                      cw = cw+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                    end 
                    if rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_s != "AB"
                      appeared = true
                      full_absent = false
                    end
                    total_mark_subject = total_mark_subject+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                  end
                end
                
                
                grand_total = grand_total+total_mark_subject
                grand_total_with_fraction = grand_total_with_fraction+total_mark_subject

                if full_sb > 0
                  if full_sb == 50 && sb <23
                    if fourth_subject.blank?
                      u_grade = u_grade+1
                      subject_failed = true
                    else
                      four_subject_failed = true
                    end
                  end
                  if full_sb == 35 && sb <16
                    if fourth_subject.blank?
                      u_grade = u_grade+1
                      subject_failed = true
                    else
                      four_subject_failed = true
                    end
                  end
                  if full_sb == 25 && sb <11
                    if fourth_subject.blank?
                      u_grade = u_grade+1
                      subject_failed = true
                    else
                      four_subject_failed = true
                    end
                  end
                end
                
                #full_ob == 16 && 
                if full_ob > 0
                  if ob <7
                    if fourth_subject.blank?
                      u_grade = u_grade+1
                      subject_failed = true
                    else
                      four_subject_failed = true
                    end
                  end
                end

                main_mark = (total_mark_subject.to_f/full_mark_subject.to_f)*100
                main_mark = main_mark.round()

                if main_mark < 45
                  if fourth_subject.blank?
                    u_grade = u_grade+1
                    subject_failed = true
                  else
                    four_subject_failed = true
                  end
                end
                
                

                

                if @student_subject_marks[sub['id'].to_i].blank?
                  @student_subject_marks[sub['id'].to_i] = {}
                end
                @student_subject_marks[sub['id'].to_i][std['id'].to_i] = total_mark_subject
                

                unless subject_grade_done.include?(sub['id'].to_i)
                  if fourth_subject.blank? && subject_failed == false
                    grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                    if !grade.blank? and !grade.name.blank?
                      grand_grade_point = grand_grade_point+grade.credit_points.to_f
                    end
                  elsif subject_failed == false and four_subject_failed == false
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

                  if appeared
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = sb
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['ob'] = ob
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['pr'] = pr
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['at'] = at
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['cw'] = cw
                  else
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = "AB"
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['ob'] = "AB"
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['pr'] = "AB"
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['at'] = "AB"
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['cw'] = "AB"
                  end 



                  @student_result[loop_std]['subjects'][main_sub_id]['result']['rt'] = total_mark_subject

                  @student_result[loop_std]['subjects'][main_sub_id]['result']['ct'] = total_mark_subject.round()


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

                  if main_mark >= 40
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['parcent'] = main_mark
                  end

                  grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                  if !grade.blank? && !grade.name.blank?
                    if subject_failed == true or four_subject_failed == true
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = "F"
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = grade.name
                    end
                  end
                  if !grade.blank? && !grade.name.blank? && sub['grade_subject'].to_i != 1
                    if subject_failed == true
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

                        @student_result[loop_std]['subject_failed'] << sub['code']+"-"+total_mark_subject.round().to_s
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

                if @subject_highest[sub['id'].to_i].blank?
                  @subject_highest[sub['id'].to_i] = total_mark_subject
                elsif total_mark_subject.to_f > @subject_highest[sub['id'].to_i].to_f
                  @subject_highest[sub['id'].to_i] = total_mark_subject.to_f
                end

              end

            end 

            grade_point_avg = grand_grade_point.to_f/total_subject.to_f
            grade_point_avg = grade_point_avg.round(2)
            if grade_point_avg > 5
              grade_point_avg = 5.00     
            end
            grand_total_with_fraction = sprintf( "%0.02f", grand_total_with_fraction)
            grand_total_with_fraction = grand_total_with_fraction.to_f
            if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
              @total_std_batch = @total_std_batch+1
              if full_absent
                @absent_in_all_subject = @absent_in_all_subject+1
              end
              grade_point_avg = grand_grade_point.to_f/total_subject.to_f
              grade_point_avg = grade_point_avg.round(2)
              if grade_point_avg > 5
                grade_point_avg = 5.00
              end
              @student_result[loop_std]['gpa'] = grand_grade_point
              @student_result[loop_std]['grand_total'] = grand_total
              @student_result[loop_std]['grand_total_with_fraction'] = grand_total_with_fraction
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
                if !gradeObj.blank? and !gradeObj.name.blank?
                  if @grade_count[gradeObj.name].blank?
                    @grade_count[gradeObj.name] = 1
                  else
                    @grade_count[gradeObj.name] = @grade_count[gradeObj.name]+1
                  end
                end
              end 
              if std_group_name == group_name or connect_exam_id.to_i == @connect_exam_obj.id
                @student_list << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
                if @section_wise_position[batch_data.id].blank?
                  @section_wise_position[batch_data.id] = []
                end
                @section_wise_position[batch_data.id] << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
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
      @student_position = {}
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

    end
  end
          
  def finding_data_9
    if @total_std.blank?
      @total_std_batch = 0
      @total_std = 0
      @student_list = []
      @student_list_batch = []
      @subject_highest = {}
      @student_avg_mark = {}
      @student_result = []
      @subject_result = {}
      @subject_code = {}
      @student_subject_marks = {}
      @absent_in_all_subject = 0
      @section_wise_position = {}	
      @failed_partial_absent = {}	
      @failed_appeared_absent = {}
      @grade_count = {}
      @exam_type_array = []
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
        @group_name_upper = group_name
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

          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end

          std_list = []
          tab['students'].each do |std| 
            std_list << std['id'].to_i
          end
          @student_all_tab = []
          unless std_list.blank?
            @student_all_tab = Student.find_all_by_id(std_list)
            all_total_std_subject = StudentsSubject.find_all_by_student_id(std_list)
          end
          tab['students'].each do |std| 
            total_failed = 0	
            total_failed_appaered = 0
            full_absent = true
            failed_on_appread = false
            grand_total = 0
            grand_total_with_fraction = 0
            grand_grade_point = 0
            u_grade = 0
            grand_total_main = 0
            grade_poin_main = 0
            
            @student_tab = @student_all_tab.find{|val| val.id.to_i == std['id'].to_i }
            if @student_tab.blank?
              next
            end  
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
            total_std_subject = all_total_std_subject.select{|val| val.student_id.to_i == std['id'].to_i }
            std_subject_id = total_std_subject.map(&:subject_id)
            total_grade = 0 
            total_subject = 0
            total_subject_main = 0
            subject_grade_done = []
            subject_array = []
            grand_total = 0
            grand_total_fraction = 0
            grade_point_avg = 0
            failed = false
            already_done = false
            tab['subjects'].each do |sub|
              if sub['grade_subject'].to_i == 1 
                next
              end
              failed_subject = false
              total_mark = 0
              full_mark = 0
              if subject_array.include?(sub['id'].to_i)
                next
              end
              subject_array << sub['id'].to_i
              if !@subject_code.blank? && !@subject_code[sub['code']].blank?	
                main_sub_id = @subject_code[sub['code']]
              else	
                @subject_code[sub['code'].to_s] = sub['code']	
                main_sub_id = sub['code']	
              end 	
              if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)	
                @student_result[loop_std]['subjects'][main_sub_id.to_s] = {}	
                @student_result[loop_std]['subjects'][main_sub_id.to_s]['name'] = sub['name']	
                @student_result[loop_std]['subjects'][main_sub_id.to_s]['id'] = main_sub_id	
                @student_result[loop_std]['subjects'][main_sub_id.to_s]['result'] = {}	
              end
              fourth_subject = false
              third_subject = false
              if !@std_subject_hash_type.blank?
                if @std_subject_hash_type.include?(std['id'].to_s+"|||"+sub['id'].to_s+"|||4")
                  fourth_subject = true
                end  
                if @std_subject_hash_type.include?(std['id'].to_s+"|||"+sub['id'].to_s+"|||3")
                  third_subject = true
                end  
              end 
              if fourth_subject.blank?
                total_subject = total_subject+1
              end
              total_subject_main = total_subject_main+1
              class_test_full_mark = 0
              class_test_ab = true 
              has_exam = false
              tab['exams'].each do |rs|
                if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank?  and !rs['result'][rs['exam_id']][sub['id']]['full_mark'].blank? and rs['exam_category'] != '1'
                  has_exam = true
                  full_mark = full_mark+rs['result'][rs['exam_id']][sub['id']]['full_mark'].to_i
                end
                
                if rs['exam_category'] == '1'
                  class_test_full_mark = rs['result'][rs['exam_id']][sub['id']]['full_mark'].to_i
                end
              end 
              
              if full_mark > 0
                full_mark = full_mark+20
              end  
              
              highest_mark = 0
              if !@subject_highest_1st_term.blank? and !@subject_highest_1st_term[sub['code'].to_s].blank?
                highest_mark = @subject_highest_1st_term[sub['code'].to_s].to_f
              end
              
              if full_mark.to_f > 0
                full_mark_all = 50 
                if full_mark.to_f >= 100
                  full_mark_all = 100
                end
                class_test_mark = 0
                at_mark = 0
                tab['exams'].each do |rs|
                  if rs['exam_category'] == '1' or rs['exam_category'] == '2'
                    if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank?  and !rs['result'][rs['exam_id']][sub['id']]['marks_obtained'].blank?
                       if rs['exam_category'] == '1'
                         if rs['result'][rs['exam_id']][sub['id']]['marks_obtained'].to_s != "AB"
                           class_test_ab = false
                         end
                         class_test_mark = class_test_mark+rs['result'][rs['exam_id']][sub['id']]['marks_obtained'].to_f
                       else
                         at_mark = at_mark+rs['result'][rs['exam_id']][sub['id']]['marks_obtained'].to_f
                       end
                    end
                  end
                end
              end
              
              if class_test_full_mark > 0
                class_test_mark = (class_test_mark.to_f/class_test_full_mark.to_f)*20
                class_test_mark = class_test_mark.round()
              end
              
              main_exam_mark = 0
              appeared = false
              tab['exams'].each do |rs|
                if rs['exam_category'] != '1' and rs['exam_category'] != '2'
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank?  and !rs['result'][rs['exam_id']][sub['id']]['marks_obtained'].blank?
                    if rs['result'][rs['exam_id']][sub['id']]['marks_obtained'].to_s != "AB"
                      main_exam_mark = main_exam_mark+rs['result'][report['exam_id']][sub['id']]['marks_obtained'].to_f  
                      appeared = true
                    end
                    if rs['result'][rs['exam_id']][sub['id']]['grade'] == "F" && fourth_subject.blank? && (rs['result'][rs['exam_id']][sub['id']]['full_mark'].to_i != 25 or rs['result'][rs['exam_id']][sub['id']]['marks_obtained'].to_f.round.to_i != 8)
                      failed = true
                      failed_subject = true
                      appeared = true
                    end
                  end
                end
              end
              
              avg_mark = main_exam_mark.to_f
              if full_mark.to_f > 100
                avg_mark = main_exam_mark.to_f*0.75 
              end
              tota_mark_with_monthly = avg_mark+class_test_mark+at_mark 
              
              main_mark = (tota_mark_with_monthly.to_f.round/full_mark_all.to_f)*100
              
              grand_total_fraction = grand_total_fraction+tota_mark_with_monthly.to_f
              grand_total = grand_total+tota_mark_with_monthly.to_f.round.to_i
              
              grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
              if !grade.blank? and !grade.name.blank?
                if grade.credit_points.to_i == 0 && fourth_subject.blank?
                  failed = true
                  failed_subject = true
                end
                if  failed_subject == true
                  grade.credit_points = 0.0
                  grade.name = "F"
                end

                if fourth_subject.blank?
                  total_grade = total_grade.to_f+grade.credit_points.to_f
                else
                  new_grade_point = grade.credit_points.to_f - 2.0
                  if new_grade_point > 0
                    total_grade = total_grade.to_f+new_grade_point.to_f
                  end  
                end
              end
              
              if total_grade > 0 && total_subject > 0
                grade_point_avg = total_grade.to_f/total_subject.to_f
                if grade_point_avg > 5
                  grade_point_avg = 5.00
                end
              end
              if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)	
                if appeared
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['fullmark'] = full_mark_all
                  if at_mark > 0
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['att'] = at_mark
                  else
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['att'] = 0
                  end

                  if class_test_mark > 0
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['ct'] = class_test_mark
                  elsif class_test_ab == true
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['ct'] = "AB"
                  else
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['ct'] = 0
                  end

                  @student_result[loop_std]['subjects'][main_sub_id]['result']['main_exam_mark'] = main_exam_mark
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['avg_mark'] = avg_mark
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['total'] = tota_mark_with_monthly
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = grade.name 
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['gp'] = grade.credit_points
                else
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['fullmark'] = full_mark_all
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['att'] = 0
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['main_exam_mark'] = "AB"
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['avg_mark'] = "AB"
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['total'] = "AB"
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = "F"
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['gp'] = "0"
                end 

                @student_result[loop_std]['subjects'][main_sub_id]['result']['rt'] = tota_mark_with_monthly

                @student_result[loop_std]['subjects'][main_sub_id]['result']['ct'] = tota_mark_with_monthly.round()

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

                if @subject_highest[sub['id'].to_i].blank?
                  @subject_highest[sub['id'].to_i] = tota_mark_with_monthly
                elsif tota_mark_with_monthly.to_f > @subject_highest[sub['id'].to_i].to_f
                  @subject_highest[sub['id'].to_i] = tota_mark_with_monthly.to_f
                end

                if !grade.blank? && !grade.name.blank? && sub['grade_subject'].to_i != 1
                  if failed_subject == true
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

                      @student_result[loop_std]['subject_failed'] << sub['code']+"-"+tota_mark_with_monthly.round().to_s
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
            end

            
              
              
              #if sub['grade_subject'].to_i != 1
              #  next
              #end
              
              #total_mark = 0
              #full_mark = 0
              #total_subject = total_subject+1
          #end 
          #end 

            grade_point_avg = grand_grade_point.to_f/total_subject.to_f
            grade_point_avg = grade_point_avg.round(2)
            if grade_point_avg > 5
              grade_point_avg = 5.00     
            end
            grand_total_with_fraction = sprintf( "%0.02f", grand_total_with_fraction)
            grand_total_with_fraction = grand_total_with_fraction.to_f
            if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
              @total_std_batch = @total_std_batch+1
              if full_absent
                @absent_in_all_subject = @absent_in_all_subject+1
              end
              grade_point_avg = grand_grade_point.to_f/total_subject.to_f
              grade_point_avg = grade_point_avg.round(2)
              if grade_point_avg > 5
                grade_point_avg = 5.00
              end
              @student_result[loop_std]['gpa'] = grand_grade_point
              @student_result[loop_std]['grand_total'] = grand_total
              @student_result[loop_std]['grand_total_with_fraction'] = grand_total_with_fraction
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
                if !gradeObj.blank? and !gradeObj.name.blank?
                  if @grade_count[gradeObj.name].blank?
                    @grade_count[gradeObj.name] = 1
                  else
                    @grade_count[gradeObj.name] = @grade_count[gradeObj.name]+1
                  end
                end
              end 
              if std_group_name == group_name or connect_exam_id.to_i == @connect_exam_obj.id
                @student_list << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
                if @section_wise_position[batch_data.id].blank?
                  @section_wise_position[batch_data.id] = []
                end
                @section_wise_position[batch_data.id] << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
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
      @student_position = {}
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

    end
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


  def finding_data_sagc_25
    if @total_std.blank?
      @total_std_batch = 0
      @total_std = 0
      @student_list = []
      @student_list_batch = []
      @subject_highest = {}
      @student_avg_mark = {}
      @student_result = []
      @subject_result = {}
      @subject_code = {}
      @student_subject_marks = {}
      @absent_in_all_subject = 0
      @section_wise_position = {}	
      @failed_partial_absent = {}	
      @failed_appeared_absent = {}
      @grade_count = {}
      @exam_type_array = []
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
        @group_name_upper = group_name
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

          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end

          std_list = []
          tab['students'].each do |std| 
            std_list << std['id'].to_i
          end
          @student_all_tab = []
          unless std_list.blank?
            @student_all_tab = Student.find_all_by_id(std_list)
            all_total_std_subject = StudentsSubject.find_all_by_student_id(std_list)
          end
          tab['students'].each do |std| 
            total_failed = 0	
            total_failed_appaered = 0
            full_absent = true
            failed_on_appread = false
            grand_total = 0
            grand_total_with_fraction = 0
            grand_grade_point = 0
            u_grade = 0
            grand_total_main = 0
            grade_poin_main = 0
            @student_tab = @student_all_tab.find{|val| val.id.to_i == std['id'].to_i }
            if @student_tab.blank?
              next
            end  
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
            total_std_subject = all_total_std_subject.select{|val| val.student_id.to_i == std['id'].to_i }
            std_subject_id = total_std_subject.map(&:subject_id)
            total_subject = 0
            subject_grade_done = []
            subject_array = []
            tab['subjects'].each do |sub|
              if subject_array.include?(sub['id'].to_i)
                next
              end
              subject_array << sub['id'].to_i
              if !@subject_code.blank? && !@subject_code[sub['code']].blank?	
                main_sub_id = @subject_code[sub['code']]
              else	
                @subject_code[sub['code'].to_s] = sub['code']	
                main_sub_id = sub['code']	
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
                if fourth_subject == false &&  sub['grade_subject'].to_i == 0 and !subject_grade_done.include?(sub['id'].to_i)
                 unless subject_grade_done.include?(sub['id'].to_i)
                    total_subject = total_subject+1
                 end
                end
                subject_failed = false
                appeared = false
                four_subject_failed = false
                total_mark_subject = 0
                full_mark_subject = 0
                sb = 0
                ob = 0
                tab['exams'].each do |rs|
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    full_mark_subject = full_mark_subject+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                    if rs['exam_category'] == '3' 
                      sb = sb+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                    end 
                    if rs['exam_category'] == '4' 
                      ob = ob+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                    end 
                    if rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_s != "AB"
                      appeared = true
                      full_absent = false
                    end
                    total_mark_subject = total_mark_subject+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                  end
                end
                
                
                grand_total = grand_total+total_mark_subject
                grand_total_with_fraction = grand_total_with_fraction+total_mark_subject
                if total_mark_subject < 11
                  if fourth_subject.blank?
                    u_grade = u_grade+1
                    subject_failed = true
                  else
                    four_subject_failed = true
                  end
                end
                main_mark = (total_mark_subject.to_f/full_mark_subject.to_f)*100
                main_mark = main_mark.round()
                

                

                if @student_subject_marks[sub['id'].to_i].blank?
                  @student_subject_marks[sub['id'].to_i] = {}
                end
                @student_subject_marks[sub['id'].to_i][std['id'].to_i] = total_mark_subject
                

                unless subject_grade_done.include?(sub['id'].to_i)
                  if fourth_subject.blank? && subject_failed == false
                    grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                    if !grade.blank? and !grade.name.blank?
                      grand_grade_point = grand_grade_point+grade.credit_points.to_f
                    end
                  elsif subject_failed == false and four_subject_failed == false
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

                  if appeared
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = sb
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['ob'] = ob
                  else
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = "AB"
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['ob'] = "AB"
                  end 



                  @student_result[loop_std]['subjects'][main_sub_id]['result']['rt'] = total_mark_subject

                  @student_result[loop_std]['subjects'][main_sub_id]['result']['ct'] = total_mark_subject.round()


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

                  if main_mark >= 40
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['parcent'] = main_mark
                  end

                  grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                  if !grade.blank? && !grade.name.blank?
                    if subject_failed == true or four_subject_failed == true
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = "F"
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = grade.name
                    end
                  end
                  if !grade.blank? && !grade.name.blank? && sub['grade_subject'].to_i != 1
                    if subject_failed == true
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

                        @student_result[loop_std]['subject_failed'] << sub['code']+"-"+total_mark_subject.round().to_s
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

                if @subject_highest[sub['id'].to_i].blank?
                  @subject_highest[sub['id'].to_i] = total_mark_subject
                elsif total_mark_subject.to_f > @subject_highest[sub['id'].to_i].to_f
                  @subject_highest[sub['id'].to_i] = total_mark_subject.to_f
                end

              end

            end 

            grade_point_avg = grand_grade_point.to_f/total_subject.to_f
            grade_point_avg = grade_point_avg.round(2)
            if grade_point_avg > 5
              grade_point_avg = 5.00     
            end
            grand_total_with_fraction = sprintf( "%0.02f", grand_total_with_fraction)
            grand_total_with_fraction = grand_total_with_fraction.to_f
            if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
              @total_std_batch = @total_std_batch+1
              if full_absent
                @absent_in_all_subject = @absent_in_all_subject+1
              end
              grade_point_avg = grand_grade_point.to_f/total_subject.to_f
              grade_point_avg = grade_point_avg.round(2)
              if grade_point_avg > 5
                grade_point_avg = 5.00
              end
              @student_result[loop_std]['gpa'] = grand_grade_point
              @student_result[loop_std]['grand_total'] = grand_total
              @student_result[loop_std]['grand_total_with_fraction'] = grand_total_with_fraction
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
                if !gradeObj.blank? and !gradeObj.name.blank?
                  if @grade_count[gradeObj.name].blank?
                    @grade_count[gradeObj.name] = 1
                  else
                    @grade_count[gradeObj.name] = @grade_count[gradeObj.name]+1
                  end
                end
              end 
              if std_group_name == group_name or connect_exam_id.to_i == @connect_exam_obj.id
                @student_list << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
                if @section_wise_position[batch_data.id].blank?
                  @section_wise_position[batch_data.id] = []
                end
                @section_wise_position[batch_data.id] << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
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
      @student_position = {}
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

    end
  end

  def finding_data_sagc_18
    if @total_std.blank?
      @total_std_batch = 0
      @total_std = 0
      @student_list = []
      @student_list_batch = []
      @subject_highest = {}
      @student_avg_mark = {}
      @student_result = []
      @subject_result = {}
      @subject_code = {}
      @student_subject_marks = {}
      @absent_in_all_subject = 0
      @section_wise_position = {}	
      @failed_partial_absent = {}	
      @failed_appeared_absent = {}
      @grade_count = {}
      @exam_type_array = []
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
        @group_name_upper = group_name
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

          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end

          std_list = []
          tab['students'].each do |std| 
            std_list << std['id'].to_i
          end
          @student_all_tab = []
          unless std_list.blank?
            @student_all_tab = Student.find_all_by_id(std_list)
            all_total_std_subject = StudentsSubject.find_all_by_student_id(std_list)
          end
          tab['students'].each do |std| 
            total_failed = 0	
            total_failed_appaered = 0
            full_absent = true
            failed_on_appread = false
            grand_total = 0
            grand_total_with_fraction = 0
            grand_grade_point = 0
            u_grade = 0
            grand_total_main = 0
            grade_poin_main = 0

            @student_tab = @student_all_tab.find{|val| val.id.to_i == std['id'].to_i }
            if @student_tab.blank?
              next
            end
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
            total_std_subject = all_total_std_subject.select{|val| val.student_id.to_i == std['id'].to_i }
            std_subject_id = total_std_subject.map(&:subject_id)
            total_subject = 0
            subject_grade_done = []
            subject_array = []
            tab['subjects'].each do |sub|
              if subject_array.include?(sub['id'].to_i)
                next
              end
              subject_array << sub['id'].to_i
              if !@subject_code.blank? && !@subject_code[sub['code']].blank?	
                main_sub_id = @subject_code[sub['code']]
              else	
                @subject_code[sub['code'].to_s] = sub['code']	
                main_sub_id = sub['code']	
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
                if fourth_subject == false &&  sub['grade_subject'].to_i == 0 and !subject_grade_done.include?(sub['id'].to_i)
                  total_subject = total_subject+1
                end
                subject_failed = false
                appeared = false
                four_subject_failed = false
                total_mark_subject = 0
                full_mark_subject = 0
                main_mark = 0
                total_sb = 0
                total_ob = 0
                total_pr = 0
            
                full_sb = 0
                full_ob = 0
                full_pr = 0
                tab['exams'].each do |rs|
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    if rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_s != "AB"
                      appeared = true
                      full_absent = false
                    end
                    if rs['exam_category'] == '3'
                      total_sb = total_sb+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      full_sb = full_sb+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                    end
                    if rs['exam_category'] == '4'
                      total_ob = total_ob+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      full_ob = full_ob+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                    end
                    if rs['exam_category'] == '5'
                      total_pr = total_pr+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      full_pr = full_pr+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                    end
                  end
                end

                if full_ob > 0
                  if full_ob.to_i == 15
                      total_ob = total_ob*2
                      grand_total_with_fraction = grand_total_with_fraction+total_ob.to_f
                  elsif total_ob > 0
                      total_ob = (total_ob/12.00)*25
                      grand_total_with_fraction = grand_total_with_fraction+total_ob.to_f
                      total_ob = total_ob.round()
                  end
                end
                if full_sb > 0
                  if full_sb.to_i == 30 && total_sb > 0
                      total_sb = (total_sb/30)*70
                      grand_total_with_fraction = grand_total_with_fraction+total_sb.to_f
                      total_sb = total_sb.round()
                  elsif total_sb > 0
                      total_sb = (total_sb/20.00)*50
                      grand_total_with_fraction = grand_total_with_fraction+total_sb.to_f
                      total_sb = total_sb.round()
                  end
                end
                if full_pr > 0
                  if total_pr > 0
                      total_pr = (total_pr/5.00)*25
                      grand_total_with_fraction = grand_total_with_fraction+total_pr.to_f
                      total_pr = total_pr.round()
                  end
                end
                total_mark_subject = total_ob+total_sb+total_pr
                

                if sub['subject_group_id'].to_i > 0
                  tab['subjects'].each do |sub2|
                    if subject_array.include?(sub2['id'].to_i) or sub['subject_group_id'].to_i != sub2['subject_group_id'].to_i
                      next
                    end
                    fourth_subject = false
                    if !@std_subject_hash_type.blank?
                      if @std_subject_hash_type.include?(std['id'].to_s+"|||"+sub2['id'].to_s+"|||4")
                        fourth_subject = true
                      end  
                    end
                    subject_grade_done << sub2['id'].to_i
                    full_mark_subject2 = 0
                    total_mark_subject2 = 0
                    total_sb2 = 0
                    total_ob2 = 0
                    total_pr2 = 0
                
                    full_sb2 = 0
                    full_ob2 = 0
                    full_pr2 = 0
                    tab['exams'].each do |rs|
                      if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub2['id']].blank? and !rs['result'][rs['exam_id']][sub2['id']][std['id']].blank?
                        if rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_s != "AB"
                          appeared = true
                          full_absent = false
                        end
                        if rs['exam_category'] == '3'
                          total_sb2 = total_sb2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                          full_sb2 = full_sb2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                        end
                        if rs['exam_category'] == '4'
                          total_ob2 = total_ob2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                          full_ob2 = full_ob2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                        end
                        if rs['exam_category'] == '5'
                          total_pr2 = total_pr2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                          full_pr2 = full_pr2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                        end
                      end
                    end
                    if full_ob2 > 0
                      if full_ob2.to_i == 15
                          total_ob2 = total_ob2*2
                          total_ob =  total_ob+total_ob2
                          grand_total_with_fraction = grand_total_with_fraction+total_ob2.to_f
                          if total_ob < 20
                            if fourth_subject.blank?
                              u_grade = u_grade+1 
                              subject_failed = true
                            else
                              four_subject_failed = true
                            end
                          end
                      elsif total_ob2 > 0
                          total_ob2 = (total_ob2/12.00)*25
                          grand_total_with_fraction = grand_total_with_fraction+total_ob2.to_f
                          total_ob2 = total_ob2.round()
                          total_ob =  total_ob+total_ob2
                          if total_ob < 16
                            if fourth_subject.blank?
                              u_grade = u_grade+1 
                              subject_failed = true
                            else
                              four_subject_failed = true
                            end
                          end
                      else
                        if fourth_subject.blank?
                          u_grade = u_grade+1 
                          subject_failed = true
                        else
                          four_subject_failed = true
                        end
                      end
                    end
                    if full_sb2 > 0
                      if full_sb2.to_i == 30 && total_sb2 > 0
                          total_sb2 = (total_sb2/30)*70
                          grand_total_with_fraction = grand_total_with_fraction+total_sb2.to_f
                          total_sb2 = total_sb2.round()
                          total_sb =  total_sb+total_sb2
                          if total_sb < 46
                            if fourth_subject.blank?
                              u_grade = u_grade+1 
                              subject_failed = true
                            else
                              four_subject_failed = true
                            end
                          end
                      elsif total_sb2 > 0
                          total_sb2 = (total_sb2/20.00)*50
                          grand_total_with_fraction = grand_total_with_fraction+total_sb2.to_f
                          total_sb2 = total_sb2.round()
                          total_sb =  total_sb+total_sb2
                          if total_sb < 34
                            if fourth_subject.blank?
                              u_grade = u_grade+1 
                              subject_failed = true
                            else
                              four_subject_failed = true
                            end
                          end
                      else
                        if fourth_subject.blank?
                          u_grade = u_grade+1 
                          subject_failed = true
                        else
                          four_subject_failed = true
                        end
                      end
                    end
                    if full_pr2 > 0
                      if total_pr2 > 0
                          total_pr2 = (total_pr2/5.00)*25
                          grand_total_with_fraction = grand_total_with_fraction+total_pr2.to_f
                          total_pr2 = total_pr2.round()
                          total_pr =  total_pr+total_pr2
                          if total_pr < 16
                            if fourth_subject.blank?
                              u_grade = u_grade+1 
                              subject_failed = true
                            else
                              four_subject_failed = true
                            end
                          end
                      else
                        if fourth_subject.blank?
                          u_grade = u_grade+1 
                          subject_failed = true
                        else
                          four_subject_failed = true
                        end
                      end
                    end
                  
                    
                    total_mark_subject2 = total_ob+total_sb+total_pr
                    grand_total = grand_total+total_mark_subject2
                    main_mark = (total_mark_subject2.to_f/200.00)*100
                    main_mark = main_mark.round()
                    grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                    if !grade.blank? and !grade.name.blank?
                      if fourth_subject.blank? && subject_failed == false
                        grand_grade_point = grand_grade_point+grade.credit_points.to_f
                      elsif subject_failed == false and four_subject_failed == false
                        new_grade_point = grade.credit_points.to_f-2
                        if new_grade_point > 0
                          grand_grade_point = grand_grade_point+new_grade_point.to_f
                        end
                      end
                    end
                  end
                end

                if @student_subject_marks[sub['id'].to_i].blank?
                  @student_subject_marks[sub['id'].to_i] = {}
                end
                @student_subject_marks[sub['id'].to_i][std['id'].to_i] = total_mark_subject


                if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)

                  if appeared
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = total_mark_subject
                  else
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = "AB"
                  end     


                  @student_result[loop_std]['subjects'][main_sub_id]['result']['rt'] = total_mark_subject

                  @student_result[loop_std]['subjects'][main_sub_id]['result']['ct'] = total_mark_subject.round()


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
                  
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['parcent'] = main_mark

                  grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                  if !grade.blank? && !grade.name.blank?
                    if subject_failed == true or four_subject_failed == true
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = "F"
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = grade.name
                    end
                  end
                  if !grade.blank? && !grade.name.blank? && sub['grade_subject'].to_i != 1
                    if subject_failed == true
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

                        @student_result[loop_std]['subject_failed'] << sub['code']+"-"+total_mark_subject.round().to_s
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

                if @subject_highest[sub['id'].to_i].blank?
                  @subject_highest[sub['id'].to_i] = total_mark_subject
                elsif total_mark_subject.to_f > @subject_highest[sub['id'].to_i].to_f
                  @subject_highest[sub['id'].to_i] = total_mark_subject.to_f
                end

              end

            end 

            grade_point_avg = 0
            if grand_grade_point > 0 && total_subject > 0
              grade_point_avg = grand_grade_point.to_f/total_subject.to_f
              grade_point_avg = grade_point_avg.round(2)
              if grade_point_avg > 5
                grade_point_avg = 5.00     
              end
            end
            grand_total_with_fraction = sprintf( "%0.02f", grand_total_with_fraction)
            grand_total_with_fraction = grand_total_with_fraction.to_f
            if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
              @total_std_batch = @total_std_batch+1
              if full_absent
                @absent_in_all_subject = @absent_in_all_subject+1
              end
              grade_point_avg = 0
              if grand_grade_point > 0 && total_subject > 0
                grade_point_avg = grand_grade_point.to_f/total_subject.to_f
                grade_point_avg = grade_point_avg.round(2)
                if grade_point_avg > 5
                  grade_point_avg = 5.00
                end
              end
              @student_result[loop_std]['gpa'] = grand_grade_point
              @student_result[loop_std]['grand_total'] = grand_total
              @student_result[loop_std]['grand_total_with_fraction'] = grand_total_with_fraction
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
                if !gradeObj.blank? and !gradeObj.name.blank?
                  if @grade_count[gradeObj.name].blank?
                    @grade_count[gradeObj.name] = 1
                  else
                    @grade_count[gradeObj.name] = @grade_count[gradeObj.name]+1
                  end
                end
              end 
              if std_group_name == group_name or connect_exam_id.to_i == @connect_exam_obj.id
                @student_list << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
                if @section_wise_position[batch_data.id].blank?
                  @section_wise_position[batch_data.id] = []
                end
                @section_wise_position[batch_data.id] << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
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
      @student_position = {}
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

    end
  end
  
  def finding_data_sagc_covid
    if @total_std.blank?
      @total_std_batch = 0
      @total_std = 0
      @student_list = []
      @student_list_batch = []
      @subject_highest = {}
      @student_avg_mark = {}
      @student_result = []
      @subject_result = {}
      @subject_code = {}
      @student_subject_marks = {}
      @absent_in_all_subject = 0
      @section_wise_position = {}	
      @failed_partial_absent = {}	
      @failed_appeared_absent = {}
      @grade_count = {}
      @exam_type_array = []
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
        @group_name_upper = group_name
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

          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end

          std_list = []
          tab['students'].each do |std| 
            std_list << std['id'].to_i
          end
          @student_all_tab = []
          unless std_list.blank?
            @student_all_tab = Student.find_all_by_id(std_list)
            all_total_std_subject = StudentsSubject.find_all_by_student_id(std_list)
          end
          tab['students'].each do |std| 
            total_failed = 0	
            total_failed_appaered = 0
            full_absent = true
            failed_on_appread = false
            grand_total = 0
            grand_total_with_fraction = 0
            grand_grade_point = 0
            u_grade = 0
            grand_total_main = 0
            grade_poin_main = 0
            @student_tab = @student_all_tab.find{|val| val.id.to_i == std['id'].to_i }
            if @student_tab.blank?
              next
            end  
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
            total_std_subject = all_total_std_subject.select{|val| val.student_id.to_i == std['id'].to_i }
            std_subject_id = total_std_subject.map(&:subject_id)
            total_subject = 0
            subject_grade_done = []
            subject_array = []
            tab['subjects'].each do |sub|
             
              if subject_array.include?(sub['id'].to_i)
                next
              end
              subject_array << sub['id'].to_i
              if !@subject_code.blank? && !@subject_code[sub['code']].blank?	
                main_sub_id = @subject_code[sub['code']]
              else	
                @subject_code[sub['code'].to_s] = sub['code']	
                main_sub_id = sub['code']	
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
                if fourth_subject == false &&  sub['grade_subject'].to_i == 0 and !subject_grade_done.include?(sub['id'].to_i)
                 unless subject_grade_done.include?(sub['id'].to_i)
                    total_subject = total_subject+1
                 end
                end
                subject_failed = false
                appeared = false
                four_subject_failed = false
                total_mark_subject = 0
                full_mark_subject = 0
                total_monthly_mark = 0
                tab['exams'].each do |rs|
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    full_mark_subject = full_mark_subject+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f
                    if rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_s != "AB"
                      appeared = true
                      full_absent = false
                    end
                    if rs['exam_category'] == '1'
                      total_monthly_mark = total_monthly_mark+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                    end
                    total_mark_subject = total_mark_subject+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                  end
                end
                
                main_mark = (total_mark_subject.to_f/full_mark_subject.to_f)*100
                main_mark = main_mark.round()
                real_main_mark = main_mark
                if sub['grade_subject'].to_i != 1
                  grand_total = grand_total+main_mark
                  grand_total_with_fraction = grand_total_with_fraction+total_mark_subject.to_f
                end
                grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                if @connect_exam_obj.result_type.to_i == 16 or @connect_exam_obj.result_type.to_i == 20
                  if grade.credit_points.to_i == 0 and sub['subject_group_id'].to_i == 0 and sub['grade_subject'].to_i != 1
                    if fourth_subject.blank?
                        u_grade = u_grade+1
                        subject_failed = true
                    else
                      four_subject_failed = true
                    end
                  end
                else
                  if !grade.blank? and !grade.name.blank? and ( grade.credit_points.to_i == 0 or total_mark_subject < 20 or (@connect_exam_obj.result_type.to_i == 14 and main_mark < 40)  or (@connect_exam_obj.result_type.to_i == 15 and main_mark < 45) ) and sub['subject_group_id'].to_i == 0 and sub['grade_subject'].to_i != 1
                    if fourth_subject.blank?
                      u_grade = u_grade+1
                      subject_failed = true
                    else
                      four_subject_failed = true
                    end
                  end
                end

                if sub['subject_group_id'].to_i > 0
                  tab['subjects'].each do |sub2|
                    if sub['subject_group_id'].to_i != sub2['subject_group_id'].to_i or sub2['code'] == main_sub_id
                      next
                    end
                    fourth_subject = false
                    if !@std_subject_hash_type.blank?
                      if @std_subject_hash_type.include?(std['id'].to_s+"|||"+sub2['id'].to_s+"|||4")
                        fourth_subject = true
                      end  
                    end
                    subject_grade_done << sub2['id'].to_i
                    full_mark_subject2 = 0
                    total_mark_subject2 = 0
                    tab['exams'].each do |rs|
                      if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub2['id']].blank? and !rs['result'][rs['exam_id']][sub2['id']][std['id']].blank?
                        full_mark_subject2 = full_mark_subject2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['full_mark'].to_f
                        total_mark_subject2 = total_mark_subject2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                      end
                    end
                  
                    total_mark_subject2 = total_mark_subject2+total_mark_subject
                    full_mark_subject2 = full_mark_subject2+full_mark_subject
                    main_mark = (total_mark_subject2.to_f/full_mark_subject2.to_f)*100
                    main_mark = main_mark.round()
                    grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                    if !grade.blank? and !grade.name.blank? and (grade.credit_points.to_i == 0 or (@connect_exam_obj.result_type.to_i == 14 and main_mark < 40)) and sub['subject_group_id'].to_i > 0 and sub['grade_subject'].to_i != 1
                      if fourth_subject.blank?
                        u_grade = u_grade+1
                        subject_failed = true
                      else
                        four_subject_failed = true
                      end
                    end

                  end
                end

                if @student_subject_marks[sub['id'].to_i].blank?
                  @student_subject_marks[sub['id'].to_i] = {}
                end
                @student_subject_marks[sub['id'].to_i][std['id'].to_i] = real_main_mark
                

                unless subject_grade_done.include?(sub['id'].to_i)
                  if fourth_subject.blank? && subject_failed == false and sub['grade_subject'].to_i != 1
                    grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                    if !grade.blank? and !grade.name.blank?
                      grand_grade_point = grand_grade_point+grade.credit_points.to_f
                    end
                  elsif subject_failed == false and four_subject_failed == false and sub['grade_subject'].to_i != 1
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

                  if appeared
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = total_mark_subject - total_monthly_mark
                  else
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = "AB"
                  end     
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['cw'] = total_monthly_mark    


                  @student_result[loop_std]['subjects'][main_sub_id]['result']['rt'] = real_main_mark

                  @student_result[loop_std]['subjects'][main_sub_id]['result']['ct'] = real_main_mark.round()


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

                  if real_main_mark >= 40
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['parcent'] = real_main_mark
                  end

                  grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                  if !grade.blank? && !grade.name.blank?
                    if subject_failed == true or four_subject_failed == true
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = "F"
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = grade.name
                    end
                  end
                  if !grade.blank? && !grade.name.blank? && sub['grade_subject'].to_i != 1
                    if subject_failed == true
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

                        @student_result[loop_std]['subject_failed'] << sub['code']+"-"+total_mark_subject.round().to_s
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

                if @subject_highest[sub['id'].to_i].blank?
                  @subject_highest[sub['id'].to_i] = total_mark_subject
                elsif total_mark_subject.to_f > @subject_highest[sub['id'].to_i].to_f
                  @subject_highest[sub['id'].to_i] = total_mark_subject.to_f
                end

              end

            end 

            grade_point_avg = grand_grade_point.to_f/total_subject.to_f
            grade_point_avg = grade_point_avg.round(2)
            if grade_point_avg > 5
              grade_point_avg = 5.00     
            end
            grand_total_with_fraction = sprintf( "%0.02f", grand_total_with_fraction)
            grand_total_with_fraction = grand_total_with_fraction.to_f
            if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
              @total_std_batch = @total_std_batch+1
              if full_absent
                @absent_in_all_subject = @absent_in_all_subject+1
              end
              grade_point_avg = grand_grade_point.to_f/total_subject.to_f
              grade_point_avg = grade_point_avg.round(2)
              if grade_point_avg > 5
                grade_point_avg = 5.00
              end
              @student_result[loop_std]['gpa'] = grand_grade_point
              @student_result[loop_std]['grand_total'] = grand_total
              @student_result[loop_std]['grand_total_with_fraction'] = grand_total_with_fraction
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
                if !gradeObj.blank? and !gradeObj.name.blank?
                  if @grade_count[gradeObj.name].blank?
                    @grade_count[gradeObj.name] = 1
                  else
                    @grade_count[gradeObj.name] = @grade_count[gradeObj.name]+1
                  end
                end
              end 
              if std_group_name == group_name or connect_exam_id.to_i == @connect_exam_obj.id
                @student_list << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
                if @section_wise_position[batch_data.id].blank?
                  @section_wise_position[batch_data.id] = []
                end
                @section_wise_position[batch_data.id] << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
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
      @student_position = {}
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

    end
  end
  
  
  def finding_data5
    @part_by_pass_fail = true
    if @connect_exam_obj.result_type.to_i == 7
      if @batch.course.course_name.upcase == "EIGHT" or @batch.course.course_name.upcase == "SIX" or @batch.course.course_name.upcase == "SEVEN"
          @part_by_pass_fail = false
      end
    end
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
      @section_wise_position_2nd_term = {}
      @section_wise_position_final_exam = {}
      @failed_partial_absent = {}	
      @failed_appeared_absent = {}	
      @grade_count = {}
      @exam_type_array = []
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
        
          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end
        
          std_list = []
          tab['students'].each do |std| 
            std_list << std['id'].to_i
          end
          @student_all_tab = []
          unless std_list.blank?
            @student_all_tab = Student.find_all_by_id(std_list)
            all_total_std_subject = StudentsSubject.find_all_by_student_id(std_list)
          end
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
            @student_tab = @student_all_tab.find{|val| val.id.to_i == std['id'].to_i }
            if @student_tab.blank?
              @student_tab = ArchivedStudent.find_by_former_id(std['id'].to_i)
            end
          
            if @student_tab.blank?
              next
            end
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
            total_std_subject = all_total_std_subject.select{|val| val.student_id.to_i == std['id'].to_i }
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
                @subject_code[sub['code'].to_s] = sub['code']	
                main_sub_id = sub['code']	
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
                
                total_sb1_main = 0
                total_sb2_main = 0
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
                four_subject_failed = false
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
                        if rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 5 && !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? &&  (@connect_exam_obj.result_type == 5 ||  @connect_exam_obj.result_type == 6) && rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 13 && @part_by_pass_fail
                          u_grade1 = u_grade1+1
                          subject_failed = true
                        end
                      end  
                      if rs['quarter'] == '2'
                        monthly_total_mark2 = monthly_total_mark2+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        if rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 5 && !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? &&  (@connect_exam_obj.result_type == 5 ||  @connect_exam_obj.result_type == 6) && rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 13 && @part_by_pass_fail
                          u_grade2 = u_grade2+1
                          subject_failed = true
                        end
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
                        if  @part_by_pass_fail
                          if @connect_exam_obj.result_type != 11
                            if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 11 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                              unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 12 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or sub['grade_subject'].to_i == 1
                                if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11) && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 30 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 13)
                                  if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8 || @connect_exam_obj.result_type != 9)  
                                    if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 50 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 22  || @connect_exam_obj.result_type == 7 )
                                      u_grade1 = u_grade1+1
                                      subject_failed = true
                                    end 
                                  end  
                                end
                              end
                            end 
                            if fourth_subject.blank? && sub['grade_subject'].to_i != 1 && sub['subject_group_id'].to_i == 0 && rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i == 70 && rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i == 31 && @connect_exam_obj.result_type == 11 && rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 5
                              u_grade1 = u_grade1+1
                              subject_failed = true
                            end
                            if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 11 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                              unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 12 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or sub['grade_subject'].to_i == 1
                                if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11) && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 30 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 13)
                                  if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8 || @connect_exam_obj.result_type != 9)
                                    four_subject_failed = true
                                  end 
                                end
                              end
                            end 
                            if sub['grade_subject'].to_i != 1 && sub['subject_group_id'].to_i == 0 && rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i == 70 && rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i == 31 && @connect_exam_obj.result_type == 11 && rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 5
                              four_subject_failed = true
                            end
                          else
                            if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F"
                              if sub['subject_group_id'].to_i == 0 && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8)
                                if fourth_subject.blank?
                                  u_grade1 = u_grade1+1
                                  subject_failed = true
                                else
                                  four_subject_failed = true
                                end  
                              end
                            end
                            
                          end 
                        end 
                      end  
                      if rs['quarter'] == '2'
                        total_sb2 = total_sb2+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        if  @part_by_pass_fail
                        if @connect_exam_obj.result_type != 11
                          if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 11 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                            unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 12 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or sub['grade_subject'].to_i == 1
                              if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11) && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 30 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 13)
                                if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8 || @connect_exam_obj.result_type != 9)  
                                  if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 50 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 22)
                                    u_grade2 = u_grade2+1
                                    subject_failed = true
                                  end
                                end  
                              end
                            end
                          end 
                          if fourth_subject.blank? && sub['grade_subject'].to_i != 1 && sub['subject_group_id'].to_i == 0 && rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i == 70 && rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i == 31 && @connect_exam_obj.result_type == 11
                            u_grade1 = u_grade1+1
                            subject_failed = true
                          end
                          if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 11 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                            unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 12 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or sub['grade_subject'].to_i == 1
                              if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11) && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 30 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 13)
                                if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8 || @connect_exam_obj.result_type != 9)
                                  four_subject_failed = true
                                end
                              end
                            end
                          end 
                          if sub['grade_subject'].to_i != 1 && sub['subject_group_id'].to_i == 0 && rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i == 70 && rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i == 31 && @connect_exam_obj.result_type == 11
                            four_subject_failed = true
                          end
                        else
                          if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F"
                            if sub['subject_group_id'].to_i == 0 && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8)
                              if fourth_subject.blank?
                                u_grade1 = u_grade1+1
                                subject_failed = true
                              else
                                four_subject_failed = true
                              end  
                            end
                          end
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
                        if  @part_by_pass_fail
                        if @connect_exam_obj.result_type != 11
                          if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 11 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                            unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 12 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or sub['grade_subject'].to_i == 1
                              if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11) && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 30 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 13)
                                if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8 || @connect_exam_obj.result_type != 9)  
                                  u_grade1 = u_grade1+1
                                  subject_failed = true
                                end  
                              end
                            end
                          end 
                        else
                          if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F"
                            if sub['subject_group_id'].to_i == 0 && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8)
                              if fourth_subject.blank?
                                u_grade1 = u_grade1+1
                                subject_failed = true
                              else
                                four_subject_failed = true
                              end  
                            end
                          end
                        end  
                       end   
                      end  
                      if rs['quarter'] == '2'
                        total_ob2 = total_ob2+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        if  @part_by_pass_fail
                        if @connect_exam_obj.result_type != 11
                          if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 11 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                            unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 12 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or sub['grade_subject'].to_i == 1
                              if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11) && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 30 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 13)
                                if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8 || @connect_exam_obj.result_type != 9)
                                  u_grade2 = u_grade2+1
                                  subject_failed = true
                                end
                              end 
                            end
                          end 
                        else
                          if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F"
                            if sub['subject_group_id'].to_i == 0 && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8)
                              if fourth_subject.blank?
                                u_grade1 = u_grade1+1
                                subject_failed = true
                              else
                                four_subject_failed = true
                              end  
                            end
                          end
                         
                        end  
                        end  
                      end
                       if @connect_exam_obj.result_type != 11
                          if rs['quarter'] == '1' && @part_by_pass_fail
                            if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F"  && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 11 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                              unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 12 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or sub['grade_subject'].to_i == 1
                                if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11) && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 30 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 13)
                                  if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8 || @connect_exam_obj.result_type != 9)  
                                    four_subject_failed = true
                                  end  
                                end
                              end
                            end 
                          end  
                          if rs['quarter'] == '2' && @part_by_pass_fail
                            if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F"  && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 11 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                              unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 12 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or sub['grade_subject'].to_i == 1
                                if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11) && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 30 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 13)
                                  if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8 || @connect_exam_obj.result_type != 9) 
                                    four_subject_failed = true
                                  end  
                                end 
                              end
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
                        if  @part_by_pass_fail
                        if @connect_exam_obj.result_type != 11
                          if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 11 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                            unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 12 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or sub['grade_subject'].to_i == 1
                              if rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11
                                if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8 || @connect_exam_obj.result_type != 9)  
                                  u_grade1 = u_grade1+1
                                  subject_failed = true
                                end
                              end
                            end
                          end
                        else
                          if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F"
                            if sub['subject_group_id'].to_i == 0 && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8)
                              if fourth_subject.blank?
                                u_grade1 = u_grade1+1
                                subject_failed = true
                              else
                                four_subject_failed = true
                              end  
                            end
                          end
                        end 
                        end   
                      end  
                      if rs['quarter'] == '2'
                        total_pr2 = total_pr2+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        if  @part_by_pass_fail
                        if @connect_exam_obj.result_type != 11
                          if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F" && fourth_subject.blank? && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 11 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                            unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 12 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or sub['grade_subject'].to_i == 1
                              if rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11
                                if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8 || @connect_exam_obj.result_type != 9)  
                                  u_grade2 = u_grade2+1
                                  subject_failed = true
                                end
                              end
                            end
                          end 
                        else
                          if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F"
                            if sub['subject_group_id'].to_i == 0 && (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8)
                              if fourth_subject.blank?
                                u_grade1 = u_grade1+1
                                subject_failed = true
                              else
                                four_subject_failed = true
                              end  
                            end
                          end
                        end
                        end
                      end
                      
                      if @connect_exam_obj.result_type != 11 && @part_by_pass_fail
                        if rs['quarter'] == '1'
                          if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F"  && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 11 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                            unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 12 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or sub['grade_subject'].to_i == 1
                              if rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11
                                if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8 || @connect_exam_obj.result_type != 9) 
                                  four_subject_failed = true
                                end
                              end
                            end
                          end 
                        end  
                        if rs['quarter'] == '2'
                          if !rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'].blank? && rs['result'][rs['exam_id']][sub['id']][std['id']]['grade'] == "F"  && (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_i != 11 or rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25)
                            unless sub['subject_group_id'].to_i > 0 or @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 2 or @connect_exam_obj.result_type == 12 or @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4 or sub['grade_subject'].to_i == 1
                              if rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11
                                if (rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 8 || @connect_exam_obj.result_type != 9)  
                                  four_subject_failed = true
                                end
                              end
                            end
                          end 
                        end
                      end
                      
                    end
                  end    
                end
                
                if @connect_exam_obj.result_type == 8 && sub['grade_subject'].to_i != 1
                  if monthly_full_mark1 == 20 
                    if monthly_total_mark1 < 9
                      if fourth_subject.blank?
                        u_grade1 = u_grade1+1
                        subject_failed = true
                      else
                        four_subject_failed = true
                      end    
                    end
                  else
                    if monthly_total_mark1 < 13
                      if fourth_subject.blank?
                        u_grade1 = u_grade1+1
                        subject_failed = true
                      else
                        four_subject_failed = true
                      end    
                    end
                  end 
                  
                  if monthly_full_mark2 == 20 
                    if monthly_total_mark2 < 9
                      if fourth_subject.blank?
                        u_grade2 = u_grade2+1
                        subject_failed = true
                      else
                        four_subject_failed = true
                      end    
                    end
                  else
                    if monthly_total_mark2 < 13
                      if fourth_subject.blank?
                        u_grade2 = u_grade2+1
                        subject_failed = true
                      else
                        four_subject_failed = true
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
                  subject_failed = false
                  four_subject_failed = false
                elsif full_mark2 > 0
                  exam_type = 2
                end
                
                
                
                
                if exam_type == 3
                  
                  if @connect_exam_obj.result_type == 8 && sub['grade_subject'].to_i != 1
                    monthly_mark_combined = 0
                    if monthly_total_mark1 > 0 || monthly_total_mark2 > 0
                      monthly_mark_combined = (monthly_total_mark1+monthly_total_mark2)/2.00
                      monthly_mark_combined = monthly_mark_combined.round()
                    end
                    if monthly_full_mark1 == 20 
                      if monthly_mark_combined < 9
                        if fourth_subject.blank?
                          u_grade = u_grade+1
                          subject_failed = true
                        else
                          four_subject_failed = true
                        end    
                      end
                    else
                      if monthly_mark_combined < 13
                        if fourth_subject.blank?
                          u_grade = u_grade+1
                          subject_failed = true
                        else
                          four_subject_failed = true
                        end    
                      end
                    end
                  end
                  
                  if monthly_full_mark1 > 5 && monthly_full_mark2 > 5  && @connect_exam_obj.result_type == 6
                    monthly_mark = (monthly_total_mark1+monthly_total_mark2)/2
                    if monthly_mark.round() != 13
                      grade_mark = (monthly_mark.round().to_f/monthly_full_mark1.to_f)*100
                      grade = GradingLevel.percentage_to_grade(grade_mark, @batch.id)
                      if !grade.blank? and !grade.name.blank?
                        if grade.credit_points.to_i == 0
                          if fourth_subject.blank?
                            u_grade = u_grade+1
                            subject_failed = true
                          else
                            four_subject_failed = true
                          end
                        end
                      end
                    end
                  end
                  
                  if full_sb1 > 0 && full_sb2 > 0 && sub['subject_group_id'].to_i == 0 && @connect_exam_obj.result_type != 1  && @connect_exam_obj.result_type != 2 && @connect_exam_obj.result_type != 12 && @connect_exam_obj.result_type != 3 && @connect_exam_obj.result_type != 4  && sub['grade_subject'].to_i != 1
                    mark = (total_sb1+total_sb2)/2
                    if ( full_sb1 != 25 || mark.round() != 11) && ( (full_sb1 != 50 || mark.round() != 22 ) || (@connect_exam_obj.result_type != 8 || full_sb1 != 50 || mark.round() != 23) ) && ( full_sb1 != 25 || mark.round() != 8 || @connect_exam_obj.result_type != 9)
                      grade_mark = (mark.round().to_f/full_sb1.to_f)*100
                      grade = GradingLevel.percentage_to_grade(grade_mark, @batch.id)
                      if !grade.blank? and !grade.name.blank?
                        if grade.credit_points.to_i == 0
                          if fourth_subject.blank?
                            u_grade = u_grade+1
                            subject_failed = true
                          else
                            four_subject_failed = true
                          end
                        end
                      end
                    end
                  end
                  
                  if full_ob1 > 0 && full_ob2 > 0 && sub['subject_group_id'].to_i == 0 && @connect_exam_obj.result_type != 1  && @connect_exam_obj.result_type != 2 && @connect_exam_obj.result_type != 12 && @connect_exam_obj.result_type != 3 && @connect_exam_obj.result_type != 4  && sub['grade_subject'].to_i != 1
                    mark = (total_ob1+total_ob2)/2
                    if ( full_ob1 != 25 || mark.round() != 11) && ( full_ob1 != 25 || mark.round() != 8 || @connect_exam_obj.result_type != 9)
                      grade_mark = (mark.round().to_f/full_ob1.to_f)*100
                      grade = GradingLevel.percentage_to_grade(grade_mark, @batch.id)
                      if !grade.blank? and !grade.name.blank?
                        if grade.credit_points.to_i == 0
                          if fourth_subject.blank?
                            u_grade = u_grade+1
                            subject_failed = true
                          else
                            four_subject_failed = true
                          end
                        end
                      end
                    end
                  end
                  
                  if full_pr1 > 0 && full_pr2 > 0 && sub['subject_group_id'].to_i == 0 && @connect_exam_obj.result_type != 1  && @connect_exam_obj.result_type != 2 && @connect_exam_obj.result_type != 12 && @connect_exam_obj.result_type != 3 && @connect_exam_obj.result_type != 4  && sub['grade_subject'].to_i != 1
                    mark = (total_pr1+total_pr2)/2
                    if ( full_pr1 != 25 || mark.round() != 11) && ( full_pr1 != 25 || mark.round() != 8 || @connect_exam_obj.result_type != 9)
                      grade_mark = (mark.round().to_f/full_pr1.to_f)*100
                      grade = GradingLevel.percentage_to_grade(grade_mark, @batch.id)
                      if !grade.blank? and !grade.name.blank?
                        if grade.credit_points.to_i == 0
                          if fourth_subject.blank?
                            u_grade = u_grade+1
                            subject_failed = true
                          else
                            four_subject_failed = true
                          end
                        end
                      end
                    end
                  end
                  
                end
                
               
                term_mark_multiplier = 0.75
                if @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4
                  term_mark_multiplier = 0.80
                end
                if @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 8
                  if monthly_full_mark1 > 0 or monthly_full_mark2 > 0
                    term_mark_multiplier = 0.90
                  else
                    term_mark_multiplier = 1
                  end
                end
                
                if @connect_exam_obj.result_type == 5 or @connect_exam_obj.result_type == 6 or sub['grade_subject'].to_i == 1
                  term_mark_multiplier = 1.00
                end
                
                if sub['name'].upcase == "ICT" and @connect_exam_obj.result_type == 9
                  term_mark_multiplier = 1.00
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
                
                if @connect_exam_obj.result_type != 5 and @connect_exam_obj.result_type != 6 and @connect_exam_obj.result_type != 1  and @connect_exam_obj.result_type != 2 and @connect_exam_obj.result_type != 2
                  if monthly_total_mark1 > 0
                    monthly_total_mark1 = (monthly_total_mark1/monthly_full_mark1)*monthly_mark_multiply
                    monthly_total_mark1 = monthly_total_mark1.round()
                    if @connect_exam_obj.result_type == 7 && @batch.course.course_name.upcase == "NINE"
                      monthly_full_mark1 = monthly_mark_multiply
                    end
                  end 
                  if monthly_total_mark2 > 0
                    monthly_total_mark2 = (monthly_total_mark2/monthly_full_mark2)*monthly_mark_multiply2
                    monthly_total_mark2 = monthly_total_mark2.round()
                    if @connect_exam_obj.result_type == 7 && @batch.course.course_name.upcase == "NINE"
                      monthly_total_mark2 = monthly_mark_multiply2
                    end
                  end
                end
                
                
                
                total_mark2 = total_mark2_80+monthly_total_mark2+at_total_mark2
                total_mark1 = total_mark1_80+monthly_total_mark1+at_total_mark1
                
                
                total_sb1_main = total_sb1
                total_sb2_main = total_sb2
                
                if @connect_exam_obj.result_type == 5 or @connect_exam_obj.result_type == 6 or (@connect_exam_obj.result_type == 7 && @batch.course.course_name.upcase == "NINE")
                  full_mark_sb1_converted = full_mark1-full_pr1-full_ob1-monthly_full_mark1
                  full_mark_sb2_converted = full_mark2-full_pr2-full_ob2-monthly_full_mark2
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
                
                subject_mark = 0
                if exam_type == 3
                  if @connect_exam_obj.result_type == 8
                    main_mark = (total_mark1_no_round.to_f+total_mark2_no_round.to_f)/(full_mark1.to_f+full_mark2.to_f)*100
                  else
                    main_mark = (total_mark1.to_f+total_mark2.to_f)/(full_mark1.to_f+full_mark2.to_f)*100
                  end 
                  main_mark = main_mark.round()
                  mark_1_half = total_mark1_no_round.to_f/2.00
                  mark_2_half = total_mark2_no_round.to_f/2.00
                  main_mark_no_round = mark_1_half+mark_2_half
                  subject_mark = (total_mark1+total_mark2)/2
                  subject_mark = subject_mark.round()
                elsif  exam_type == 2
                  
                 
                  main_mark_no_round = total_mark2_no_round.to_f/full_mark2.to_f*100
                  subject_mark = total_mark2
                else
                  main_mark = total_mark1.to_f/full_mark1.to_f*100
                  
                  main_mark_no_round = total_mark1_no_round.to_f/full_mark1.to_f*100
                  subject_mark = total_mark1
                end  
                subject_full_marks = main_mark_no_round.round()
                ct_marks_main = (main_mark_no_round/100)*full_mark1
                if sub['grade_subject'].to_i != 1
                  if @student_subject_marks[sub['id'].to_i].blank?
                    @student_subject_marks[sub['id'].to_i] = {}
                  end
                  @student_subject_marks[sub['id'].to_i][std['id'].to_i] = subject_full_marks
                  
                  
                  grand_total1 = grand_total1+total_mark1
                  grand_total2 = grand_total2+total_mark2
                  grand_total = grand_total+main_mark_no_round
                  grand_total1_with_fraction = grand_total1_with_fraction+total_mark1_no_round
                  grand_total2_with_fraction = grand_total2_with_fraction+total_mark2_no_round
                  grand_total_with_fraction = grand_total_with_fraction+main_mark_no_round
                  
                  if full_mark1 == 50 && main_mark1 == 44 && (@connect_exam_obj.result_type == 12 || @connect_exam_obj.result_type == 6 || @connect_exam_obj.result_type == 5 )
                    main_mark1 = 45
                    main_mark = 45
                  end
                  if full_mark2 == 50 && main_mark2 == 44 && (@connect_exam_obj.result_type == 12 || @connect_exam_obj.result_type == 6 || @connect_exam_obj.result_type == 5 )
                    main_mark2 = 45
                  end
                
              
                
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
                  elsif subject_failed == false and four_subject_failed == false
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
                ob_round = 0
                ob_not_round = 0
                sb_round = 0
                sb_not_round = 0
                pr_round = 0
                pr_not_round = 0
                if connect_exam_id.to_i == @connect_exam_obj.id or (std_group_name == group_name && !@class.blank?)
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['at'] = at_total_mark1+at_total_mark2
                  
                  
                  if monthly_full_mark1 > 0 || monthly_full_mark2 > 0
                    if appeared_ct
                      ct_not_round = ct_round = monthly_total_main_mark1+monthly_total_main_mark2
                      ct_round = ct_round.round()
                      if monthly_full_mark1 > 0 && monthly_full_mark2 > 0
                        ct_not_round = ct_round = (monthly_total_main_mark1+monthly_total_main_mark2)/2
                        ct_round = ct_round.round()
                      end
                      if @connect_exam_obj.result_type < 5
                        @student_result[loop_std]['subjects'][main_sub_id]['result']['cw'] = ct_not_round
                      else
                        @student_result[loop_std]['subjects'][main_sub_id]['result']['cw'] = ct_round
                      end 
                      
                      
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['cw'] = "AB"
                    end  
                  end
                  if full_ob1 > 0 || full_ob2 > 0
                    if appeared_ob
                      
                      ob_not_round = ob_round = total_ob1+total_ob2
                      ob_round = ob_round.round()
                      if full_ob1 > 0 && full_ob2 > 0
                        ob_not_round = ob_round = (total_ob1+total_ob2)/2
                        ob_round = ob_round.round()
                      end
                      if @connect_exam_obj.result_type < 5
                        @student_result[loop_std]['subjects'][main_sub_id]['result']['ob'] = ob_not_round
                      else
                        @student_result[loop_std]['subjects'][main_sub_id]['result']['ob'] = ob_round
                      end 
                      
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['ob'] = "AB"
                    end  
                  end
                  if full_sb1 > 0 || full_sb2 > 0
                    if appeared_sb
                      sb_not_round = sb_round = total_sb1_main+total_sb2_main
                      sb_round = sb_round.round()
                      if full_sb1 > 0 && full_sb2 > 0
                        sb_not_round = sb_round = (total_sb1_main+total_sb2_main)/2
                        sb_round = sb_round.round()
                      end
                      if @connect_exam_obj.result_type < 5
                        @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = sb_not_round
                      else
                        @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = sb_round
                      end 
                      
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = "AB"
                    end  
                  end
                  if full_pr1 > 0 || full_pr2 > 0
                    pr_not_round = pr_round = total_pr1+total_pr2
                    pr_round = pr_round.round()
                    if full_sb1 > 0 && full_sb2 > 0
                      pr_not_round = pr_round = (total_pr1+total_pr2)/2
                      pr_round = pr_round.round()
                    end
                    if appeared_pr
                      if @connect_exam_obj.result_type < 5
                        @student_result[loop_std]['subjects'][main_sub_id]['result']['pr'] = pr_not_round
                      else
                        @student_result[loop_std]['subjects'][main_sub_id]['result']['pr'] = pr_round
                      end    
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['pr'] = "AB"
                    end  
                  end
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['rt'] = ob_round+sb_round+pr_round
                  
                  @student_result[loop_std]['subjects'][main_sub_id]['result']['ct'] = ct_marks_main.round()
                  
                  
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
                  
                  if main_mark >= 40
                    @student_result[loop_std]['subjects'][main_sub_id]['result']['parcent'] = main_mark
                  end
                  
                  grade = GradingLevel.percentage_to_grade(main_mark, @batch.id)
                  if !grade.blank? && !grade.name.blank?
                    if (subject_failed == true or four_subject_failed == true) and @connect_exam_obj.result_type != 1  and @connect_exam_obj.result_type != 2
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = "F"
                    else
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['lg'] = grade.name
                    end
                  end
                  if !grade.blank? && !grade.name.blank? && sub['grade_subject'].to_i != 1
                    if (grade.credit_points.to_i == 0 and sub['subject_group_id'].to_i == 0) or (subject_failed == true and @connect_exam_obj.result_type != 1  and @connect_exam_obj.result_type != 2)
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
                        
                        @student_result[loop_std]['subject_failed'] << sub['code']+"-"+subject_full_marks.round().to_s
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
                    @subject_code[sub2['code'].to_s] = sub2['code']	
                    main_sub_id = sub2['code']	
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
                    
                    total_sb12_main = 0
                    total_sb22_main = 0
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
                            total_sb1 = total_sb1_main+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            total_sb12 = total_sb12+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            main_sub_grade = (total_sb1.to_f/full_sb1.to_f)*100
                            
                            if  @part_by_pass_fail
                            grade = GradingLevel.percentage_to_grade(main_sub_grade, @batch.id)
                            # if @student_tab.admission_no == "19626" && sub2['name'] == "Bangla 2nd Paper"
                            #   abort(total_sb1.to_s+"_"+full_sb1.to_s+"_"+main_sub_grade.to_s)
                            # end
                            if !grade.blank? and !grade.credit_points.blank?
                              if @connect_exam_obj.result_type != 11 
                                if (grade.credit_points.to_i == 0 and fourth_subject.blank?) or (total_sb1 < 64 and full_sb1 == 140 and fourth_subject.blank?) or (total_sb1 < 90 and full_sb1 == 200 and fourth_subject.blank?)
                                  u_grade1 = u_grade1+1
                                  subject_failed = true
                                end
                              else
                                if (grade.credit_points.to_i == 0 and fourth_subject.blank?) or (total_sb1 < 34 and full_sb1 == 100 and fourth_subject.blank?)
                                  u_grade1 = u_grade1+1
                                  subject_failed = true
                                end
                              end  
                            end 
                            end
                          end  
                          if rs['quarter'] == '2'
                            total_sb2 = total_sb2_main+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            total_sb22 = total_sb22+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            main_sub_grade = (total_sb2.to_f/full_sb2.to_f)*100
                            if  @part_by_pass_fail
                            grade = GradingLevel.percentage_to_grade(main_sub_grade, @batch.id)
                            if !grade.blank? and !grade.credit_points.blank?
                              if @connect_exam_obj.result_type != 11
                                if (grade.credit_points.to_i == 0 and fourth_subject.blank?) or (total_sb2 < 64 and full_sb2 == 140 and fourth_subject.blank?)
                                  if rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11 
                                    u_grade2 = u_grade2+1
                                    subject_failed = true
                                  end
                                end
                              else
                                if (grade.credit_points.to_i == 0 and fourth_subject.blank?) or (total_sb2 < 34 and full_sb2 == 100 and fourth_subject.blank?)
                                  u_grade1 = u_grade1+1
                                  subject_failed = true
                                end
                              end  
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
                            if  @part_by_pass_fail
                            grade = GradingLevel.percentage_to_grade(main_sub_grade, @batch.id)
                            if !grade.blank? and !grade.credit_points.blank?
                              if @connect_exam_obj.result_type != 11
                                if grade.credit_points.to_i == 0 && fourth_subject.blank?
                                  if total_ob1 != 26 || full_ob1 != 60 || !fourth_subject.blank?
                                    if rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11
                                      u_grade1 = u_grade1+1
                                      subject_failed = true
                                    end
                                  end
                                end
                              else
                                if grade.credit_points.to_i == 0 && fourth_subject.blank? && (full_ob1 != 50 || total_ob1 != 16)
                                  u_grade1 = u_grade1+1
                                  subject_failed = true
                                end
                              end  
                            end 
                            end
                          end  
                          if rs['quarter'] == '2'
                            total_ob2 = total_ob2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            total_ob22 = total_ob22+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            main_sub_grade = (total_ob2.to_f/full_ob2.to_f)*100
                            if  @part_by_pass_fail
                            grade = GradingLevel.percentage_to_grade(main_sub_grade, @batch.id)
                            if !grade.blank? and !grade.credit_points.blank?
                              if @connect_exam_obj.result_type != 11
                                if grade.credit_points.to_i == 0 && fourth_subject.blank?
                                  if total_ob2 != 26 || full_ob2 != 60 || !fourth_subject.blank?
                                    if rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11
                                      u_grade2 = u_grade2+1
                                      subject_failed = true
                                    end
                                  end
                                end
                              else
                                if grade.credit_points.to_i == 0 && fourth_subject.blank? && (full_ob2 != 50 || total_ob2 != 16)
                                  u_grade1 = u_grade1+1
                                  subject_failed = true
                                end
                                
                              end  
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
                            if  @part_by_pass_fail
                            grade = GradingLevel.percentage_to_grade(main_sub_grade, @batch.id)
                            if !grade.blank? and !grade.credit_points.blank?
                              if @connect_exam_obj.result_type != 11
                                if grade.credit_points.to_i == 0 and fourth_subject.blank?
                                  if rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11
                                    u_grade1 = u_grade1+1
                                    subject_failed = true
                                  end
                                end
                              else
                                if grade.credit_points.to_i == 0 && fourth_subject.blank? && (full_pr1 != 50 || total_pr1 != 16)
                                  u_grade1 = u_grade1+1
                                  subject_failed = true
                                end
                              end  
                            end 
                            end 
                          end  
                          if rs['quarter'] == '2'
                            total_pr2 = total_pr2+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            total_pr22 = total_pr22+rs['result'][rs['exam_id']][sub2['id']][std['id']]['marks_obtained'].to_f
                            main_sub_grade = (total_pr2.to_f/full_pr2.to_f)*100
                            if  @part_by_pass_fail
                            grade = GradingLevel.percentage_to_grade(main_sub_grade, @batch.id)
                            if !grade.blank? and !grade.credit_points.blank?
                              if @connect_exam_obj.result_type != 11
                                if grade.credit_points.to_i == 0 and fourth_subject.blank?
                                  if rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i != 25 || rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f.round.to_i != 11
                                    u_grade2 = u_grade2+1
                                    subject_failed = true
                                  end
                                end
                              else
                                if grade.credit_points.to_i == 0 && fourth_subject.blank? && (full_pr2 != 50 || total_pr2 != 16)
                                  u_grade1 = u_grade1+1
                                  subject_failed = true
                                end
                              end  
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
                    
                    if @connect_exam_obj.result_type == 8
                      if monthly_full_mark1 == 20 
                        if monthly_total_mark1 < 9
                          if fourth_subject.blank?
                            u_grade1 = u_grade1+1
                            subject_failed = true
                          else
                            four_subject_failed = true
                          end    
                        end
                      else
                        if monthly_total_mark1 < 13
                          if fourth_subject.blank?
                            u_grade1 = u_grade1+1
                            subject_failed = true
                          else
                            four_subject_failed = true
                          end    
                        end
                      end 
                      if monthly_full_mark2 == 20 
                        if monthly_total_mark2 < 9
                          if fourth_subject.blank?
                            u_grade2 = u_grade2+1
                            subject_failed = true
                          else
                            four_subject_failed = true
                          end    
                        end
                      else
                        if monthly_total_mark2 < 13
                          if fourth_subject.blank?
                            u_grade2 = u_grade2+1
                            subject_failed = true
                          else
                            four_subject_failed = true
                          end    
                        end
                      end
                    end
                    if full_mark1 > 0 && full_mark2 > 0
                      exam_type = 3
                      subject_failed = false
                      four_subject_failed = false
                    elsif full_mark2 > 0
                      exam_type = 2
                    end
                    
                     
                    
                    
                    
                    
                    if exam_type == 3
                  
                      if @connect_exam_obj.result_type == 8 && sub['grade_subject'].to_i != 1
                        monthly_mark_combined = 0
                        if monthly_total_mark1 > 0 || monthly_total_mark2 > 0
                          monthly_mark_combined = (monthly_total_mark1+monthly_total_mark2)/2.00
                          monthly_mark_combined = monthly_mark_combined.round()
                        end
                        if monthly_full_mark1 == 20 
                          if monthly_mark_combined < 9
                            if fourth_subject.blank?
                              u_grade = u_grade+1
                              subject_failed = true
                            else
                              four_subject_failed = true
                            end    
                          end
                        else
                          if monthly_mark_combined < 13
                            if fourth_subject.blank?
                              u_grade = u_grade+1
                              subject_failed = true
                            else
                              four_subject_failed = true
                            end    
                          end
                        end
                      end
                  
                      if monthly_full_mark1 > 5 && monthly_full_mark2 > 5  && @connect_exam_obj.result_type == 6
                        monthly_mark = (monthly_total_mark1+monthly_total_mark2)/2
                        if monthly_mark != 13
                          grade_mark = (monthly_mark.to_f/monthly_total_mark1.to_f)*100
                          grade = GradingLevel.percentage_to_grade(grade_mark, @batch.id)
                          if !grade.blank? and !grade.name.blank?
                            if grade.credit_points.to_i == 0
                              if fourth_subject.blank?
                                u_grade = u_grade+1
                                subject_failed = true
                              else
                                four_subject_failed = true
                              end
                            end
                          end
                        end
                      end
                  
                      if full_sb1 > 0 && full_sb2 > 0 && @connect_exam_obj.result_type != 1  && @connect_exam_obj.result_type != 2 && @connect_exam_obj.result_type != 12 && @connect_exam_obj.result_type != 3 && @connect_exam_obj.result_type != 4  && sub['grade_subject'].to_i != 1
                        mark = (total_sb1+total_sb2)/4
                        full_sb1 = full_sb1/2
                        if ( full_sb1 != 25 || mark.round() != 11) && ( full_sb1 != 25 || mark.round() != 8 || @connect_exam_obj.result_type != 9)
                          grade_mark = (mark.to_f/full_sb1.to_f)*100
                          grade = GradingLevel.percentage_to_grade(grade_mark, @batch.id)
                          if !grade.blank? and !grade.name.blank?
                            if grade.credit_points.to_i == 0
                              if fourth_subject.blank?
                                u_grade = u_grade+1
                                subject_failed = true
                              end
                            end
                          end
                        end
                      end
                  
                      if full_ob1 > 0 && full_ob2 > 0 && sub['subject_group_id'].to_i == 0 && @connect_exam_obj.result_type != 1  && @connect_exam_obj.result_type != 2 && @connect_exam_obj.result_type != 12 && @connect_exam_obj.result_type != 3 && @connect_exam_obj.result_type != 4  && sub['grade_subject'].to_i != 1
                        mark = (total_ob1+total_ob2)/4
                        full_ob1 = full_ob1/2
                        if ( full_ob1 != 25 || mark.round() != 11) && ( full_ob1 != 25 || mark.round() != 8 || @connect_exam_obj.result_type != 9)
                          grade_mark = (mark.to_f/full_ob1.to_f)*100
                          grade = GradingLevel.percentage_to_grade(grade_mark, @batch.id)
                          if !grade.blank? and !grade.name.blank?
                            if grade.credit_points.to_i == 0
                              if fourth_subject.blank?
                                u_grade = u_grade+1
                                subject_failed = true
                              end
                            end
                          end
                        end
                      end
                  
                      if full_pr1 > 0 && full_pr2 > 0 && sub['subject_group_id'].to_i == 0 && @connect_exam_obj.result_type != 1  && @connect_exam_obj.result_type != 2 && @connect_exam_obj.result_type != 12 && @connect_exam_obj.result_type != 3 && @connect_exam_obj.result_type != 4  && sub['grade_subject'].to_i != 1
                        mark = (total_pr1+total_pr2)/4
                        full_pr1 = full_pr1/2
                        if ( full_pr1 != 25 || mark.round() != 11) && ( full_pr1 != 25 || mark.round() != 8 || @connect_exam_obj.result_type != 9)
                          grade_mark = (mark.to_f/full_pr1.to_f)*100
                          grade = GradingLevel.percentage_to_grade(grade_mark, @batch.id)
                          if !grade.blank? and !grade.name.blank?
                            if grade.credit_points.to_i == 0
                              if fourth_subject.blank?
                                u_grade = u_grade+1
                                subject_failed = true
                              end
                            end
                          end
                        end
                      end
                      
                      
                      #if full_sb12 > 0 && full_sb22 > 0 && @connect_exam_obj.result_type != 1  && @connect_exam_obj.result_type != 2 && @connect_exam_obj.result_type != 12 && @connect_exam_obj.result_type != 3 && @connect_exam_obj.result_type != 4  && sub['grade_subject'].to_i != 1
                        #mark = (total_sb12+total_sb22)/2
                        #full_sb1 = full_sb12
                        #if ( full_sb1 != 25 || mark.round() != 11) && ( full_sb1 != 25 || mark.round() != 8 || @connect_exam_obj.result_type != 9)
                          #grade_mark = (mark.to_f/full_sb1.to_f)*100
                          #grade = GradingLevel.percentage_to_grade(grade_mark, @batch.id)
                          #if !grade.blank? and !grade.name.blank?
                            #if grade.credit_points.to_i == 0
                              #if fourth_subject.blank?
                                #subject_failed = true
                              #else
                                #four_subject_failed = true
                              #end  
                            #end
                          #end
                        #end
                      #end
                  
                      #if full_ob12 > 0 && full_ob22 > 0 && sub['subject_group_id'].to_i == 0 && @connect_exam_obj.result_type != 1  && @connect_exam_obj.result_type != 2 && @connect_exam_obj.result_type != 12 && @connect_exam_obj.result_type != 3 && @connect_exam_obj.result_type != 4  && sub['grade_subject'].to_i != 1
                        #mark = (total_ob12+total_ob22)/2
                        #full_ob1 = full_ob12
                        #if ( full_ob1 != 25 || mark.round() != 11) && ( full_ob1 != 25 || mark.round() != 8 || @connect_exam_obj.result_type != 9)
                          #grade_mark = (mark.to_f/full_ob1.to_f)*100
                          #grade = GradingLevel.percentage_to_grade(grade_mark, @batch.id)
                          #if !grade.blank? and !grade.name.blank?
                            #if grade.credit_points.to_i == 0
                              #if fourth_subject.blank?
                                #subject_failed = true
                              #else
                                #four_subject_failed = true
                              #end
                            #end
                          #end
                        #end
                      #end
                  
                      #if full_pr12 > 0 && full_pr22 > 0 && sub['subject_group_id'].to_i == 0 && @connect_exam_obj.result_type != 1  && @connect_exam_obj.result_type != 2 && @connect_exam_obj.result_type != 12 && @connect_exam_obj.result_type != 3 && @connect_exam_obj.result_type != 4  && sub['grade_subject'].to_i != 1
                        #mark = (total_pr12+total_pr22)/2
                        #full_pr1 = full_pr12
                        #if ( full_pr1 != 25 || mark.round() != 11) && ( full_pr1 != 25 || mark.round() != 8 || @connect_exam_obj.result_type != 9)
                          #grade_mark = (mark.to_f/full_pr1.to_f)*100
                          #grade = GradingLevel.percentage_to_grade(grade_mark, @batch.id)
                          #if !grade.blank? and !grade.name.blank?
                            #if grade.credit_points.to_i == 0
                              #if fourth_subject.blank?
                                #subject_failed = true
                              #else
                                #four_subject_failed = true
                              #end
                            #end
                          #end
                        #end
                      #end
                  
                    end
                    
                    
                    
                    term_mark_multiplier = 0.75
                    if @connect_exam_obj.result_type == 3 or @connect_exam_obj.result_type == 4
                      term_mark_multiplier = 0.80
                    end
                    if @connect_exam_obj.result_type == 7 or @connect_exam_obj.result_type == 8
                      term_mark_multiplier = 0.90
                    end
                    
                    if sub['grade_subject'].to_i == 1
                      term_mark_multiplier = 1.00
                    end
                    
                    if @connect_exam_obj.result_type == 1 or @connect_exam_obj.result_type == 2
                      term_mark_multiplier = 1.00
                    end
                    total_mark2 = total_ob22+total_sb22+total_pr22
                    total_mark2_80 = total_mark2.to_f
                    if full_mark2 > 100 or term_mark_multiplier == 0.80 or term_mark_multiplier == 0.90
                      total_mark2_80 = total_mark2.to_f*term_mark_multiplier
                    end  
                    total_mark1 = total_ob12+total_sb12+total_pr12
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
                        if @connect_exam_obj.result_type == 7 && @batch.course.course_name.upcase == "NINE"
                          monthly_full_mark1 = monthly_mark_multiply
                        end
                      end 
                      if monthly_total_mark2 > 0
                        monthly_total_mark2 = (monthly_total_mark2/monthly_full_mark2)*monthly_mark_multiply2
                        monthly_total_mark2 = monthly_total_mark2.round()
                        if @connect_exam_obj.result_type == 7 && @batch.course.course_name.upcase == "NINE"
                         monthly_full_mark2 = monthly_mark_multiply2
                        end 
                      end
                    end
                    
                    total_mark2 = total_mark2_80+monthly_total_mark2+at_total_mark2
                    total_mark1 = total_mark1_80+monthly_total_mark1+at_total_mark1
                    if @connect_exam_obj.result_type == 5 or @connect_exam_obj.result_type == 6  or (@connect_exam_obj.result_type == 7 && @batch.course.course_name.upcase == "NINE")
                      full_mark_sb1_converted = full_mark1-full_pr12-full_ob12-monthly_full_mark1
                      full_mark_sb2_converted = full_mark2-full_pr22-full_ob22-monthly_full_mark2
                      if total_sb12 > 0
                        total_sb12 = (total_sb12.to_f/full_sb12.to_f)*full_mark_sb1_converted.to_f
                      end
                      if total_sb22 > 0
                        total_sb22 = (total_sb22.to_f/full_sb22.to_f)*full_mark_sb2_converted.to_f
                      end
                      total_mark1 = total_ob12+total_sb12+total_pr12+monthly_total_mark1
                      total_mark2 = total_ob22+total_sb22+total_pr22+monthly_total_mark2
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
                      if @connect_exam_obj.result_type == 8
                        main_mark = main_mark+(total_mark1_no_round.to_f+total_mark2_no_round.to_f)/(full_mark1.to_f+full_mark2.to_f)*100
                      else
                        main_mark = main_mark+(total_mark1.to_f+total_mark2.to_f)/(full_mark1.to_f+full_mark2.to_f)*100
                      end 
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
                    subject_full_marks = main_mark_no_round.round()
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
                      
                      if monthly_full_mark1 > 0 || monthly_full_mark2 > 0
                        if monthly_total_main_mark1 > 0 || monthly_total_main_mark2 > 0
                          ct_round = monthly_total_main_mark1+monthly_total_main_mark2
                          ct_round = ct_round.round()
                          if monthly_full_mark1 > 0 && monthly_full_mark2 > 0
                            ct_round = (monthly_total_main_mark1+monthly_total_main_mark2)/2
                            ct_round = ct_round.round()
                          end
                          @student_result[loop_std]['subjects'][main_sub_id]['result']['cw'] = ct_round
                        else
                          @student_result[loop_std]['subjects'][main_sub_id]['result']['cw'] = "AB"
                        end  
                      end
                      if full_ob1 > 0 || full_ob2 > 0
                        if total_ob12 > 0 || total_ob22 > 0 
                          ob_round = total_ob12+total_ob22
                          ob_round = ob_round.round()
                          if full_ob1 > 0 && full_ob2 > 0
                            ob_round = (total_ob12+total_ob22)/2
                            ob_round = ob_round.round()
                          end
                          @student_result[loop_std]['subjects'][main_sub_id]['result']['ob'] = ob_round
                        else
                          @student_result[loop_std]['subjects'][main_sub_id]['result']['ob'] = "AB"
                        end  
                      end
                      if full_sb1 > 0 || full_sb2 > 0
                        if total_sb12 > 0 || total_sb22 > 0
                          sb_round = total_sb12+total_sb22
                          sb_round = sb_round.round()
                          if full_sb1 > 0 && full_sb2 > 0
                            sb_round = (total_sb12+total_sb22)/2
                            sb_round = sb_round.round()
                          end
                          @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = sb_round
                        else
                          @student_result[loop_std]['subjects'][main_sub_id]['result']['sb'] = "AB"
                        end  
                      end
                      if full_pr1 > 0 || full_pr2 > 0
                        pr_round = total_pr12+total_pr22
                        pr_round = pr_round.round()
                        if total_pr12 > 0 && total_pr22 > 0
                          pr_round = (total_pr12+total_pr22)/2
                          pr_round = pr_round.round()
                        end
                        if total_pr12 > 0 || total_pr22 > 0
                          @student_result[loop_std]['subjects'][main_sub_id]['result']['pr'] = pr_round
                        else
                          @student_result[loop_std]['subjects'][main_sub_id]['result']['pr'] = "AB"
                        end  
                      end
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['rt'] = ob_round+sb_round+pr_round
                      @student_result[loop_std]['subjects'][main_sub_id]['result']['ct'] = subject_full_marks
                      
                   
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
                      if main_mark >= 40
                        @student_result[loop_std]['subjects'][main_sub_id]['result']['parcent'] = main_mark
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
                            total_mark_main = total_mark1+total_mark2
                            @student_result[loop_std]['subject_failed'] << sub2['code']+"-"+total_mark_main.round().to_s
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
    
            
            if exam_type == 3
              grade_point_avg = grand_grade_point.to_f/total_subject.to_f
              grade_point_avg = grade_point_avg.round(2)
              if grade_point_avg > 5
                grade_point_avg = 5.00
                  
              end
            end
            if exam_type == 1
              grade_point_avg = grand_grade_point1.to_f/total_subject.to_f
              grade_point_avg = grade_point_avg.round(2)
              if grade_point_avg > 5
                grade_point_avg = 5.00
                  
              end
            end
            if exam_type == 2
              grade_point_avg = grand_grade_point2.to_f/total_subject.to_f
              grade_point_avg = grade_point_avg.round(2)
              if grade_point_avg > 5
                grade_point_avg = 5.00
                  
              end
            end
            grand_total_with_fraction = sprintf( "%0.02f", grand_total_with_fraction)
            grand_total_with_fraction = grand_total_with_fraction.to_f
            grand_total1_with_fraction = sprintf( "%0.02f", grand_total1_with_fraction)
            grand_total1_with_fraction = grand_total1_with_fraction.to_f
            grand_total2_with_fraction = sprintf( "%0.02f", grand_total2_with_fraction)
            grand_total2_with_fraction = grand_total2_with_fraction.to_f
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
                if @section_wise_position_final_exam[batch_data.id].blank?
                  @section_wise_position_final_exam[batch_data.id] = []
                end
                @section_wise_position_final_exam[batch_data.id] << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
              end
            end  
        
            
            
            if u_grade1 == 0
              grand_total_new = 55500-grand_total1_with_fraction
              grand_grade_new = 50000-grand_grade_point1
              if connect_exam_id.to_i == @connect_exam_obj.id || (std_group_name == group_name && !@class.blank?)
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
              if std_group_name == group_name || connect_exam_id.to_i == @connect_exam_obj.id
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
                if @section_wise_position_2nd_term[batch_data.id].blank?
                  @section_wise_position_2nd_term[batch_data.id] = []
                end
                @section_wise_position_2nd_term[batch_data.id] << [grand_grade_new.to_f,grand_total_new.to_f,std['id'].to_i]
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
      @section_all_position_batch_2nd_term = {}
      @section_all_position_batch_final_term = {}
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
      
      unless @section_wise_position_final_exam.blank?
        @section_wise_position_final_exam.each do|key,value|
          position = 0
         
          @sorted_students = @section_wise_position_final_exam[key].sort
          @sorted_students.each do|s|
            
            if last_grade != s[0] or last_total != s[1]
              position = position+1
            end
            last_grade = s[0]
            last_total = s[1]
            if @section_all_position_batch_final_term[key].blank?
              @section_all_position_batch_final_term[key] = {}
            end
            @section_all_position_batch_final_term[key][s[2].to_i] = position
          end 
        end
      end
      
      
      last_grade = 0.0
      last_total = 0.0
      
      unless @section_wise_position_2nd_term.blank?
        @section_wise_position_2nd_term.each do|key,value|
          position = 0
         
          @sorted_students = @section_wise_position_2nd_term[key].sort
          @sorted_students.each do|s|
            
            if last_grade != s[0] or last_total != s[1]
              position = position+1
            end
            last_grade = s[0]
            last_total = s[1]
            if @section_all_position_batch_2nd_term[key].blank?
              @section_all_position_batch_2nd_term[key] = {}
            end
            @section_all_position_batch_2nd_term[key][s[2].to_i] = position
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
  
  def finding_data_sems2
    if @student_position.blank?
      if @grading_levels.blank?
        @grading_levels = GradingLevel.for_batch(@batch.id)
        if @grading_levels.blank?
          @grading_levels = GradingLevel.default
        end
      end
      @student_list = []
      @student_subject_marks = {}
      @student_subject_mark_test = {}
      @subject_highest = {}
      @student_avg_mark = {}
      unless @tabulation_data.blank?
        connect_exam = 0
        batch_loop = 0
        @tabulation_data['report'].each do |tab|
          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end
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

              has_exam = true
              loop = 0

              if batch_subject_id.include?(sub['id'].to_i) or std_subject_id.include?(sub['id'].to_i)

                subject_count_std = subject_count_std+1
                std_subject_marks_final = 0
                class_test = []
                tab['exams'].each do |rs|
                  if rs['exam_category'] != '1' or rs['quarter'] != '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*7.5
                    class_test << obt_mark.round(2)
                  else
                    class_test << 0
                  end  
                end 
                class_test.sort! {|x,y| y <=> x }
                std_subject_marks_final = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                tab['exams'].each do |rs|
                  if rs['exam_category'] == '1' or rs['exam_category'] == '3' or rs['quarter'] != '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*2.5
                    std_subject_marks_final = std_subject_marks_final+obt_mark.to_f
                  end  
                end

                tab['exams'].each do |rs|
                  if rs['exam_category'] != '3' or rs['quarter'] != '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f*2
                    std_subject_marks_final = std_subject_marks_final+obt_mark.to_f
                  end  
                end

                std_subject_marks_mid = 0
                class_test = []


                tab['exams'].each do |rs|
                  if rs['exam_category'] != '1' or rs['quarter'] == '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*7.5
                    class_test << obt_mark.round(2)
                  else
                    class_test << 0
                  end  
                end 
                class_test.sort! {|x,y| y <=> x }
                std_subject_marks_mid = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                tab['exams'].each do |rs|
                  if rs['exam_category'] == '1' or rs['exam_category'] == '3' or rs['quarter'] == '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*2.5
                    std_subject_marks_mid = std_subject_marks_mid+obt_mark.to_f
                  end  
                end


                tab['exams'].each do |rs|
                  if rs['exam_category'] != '3' or rs['quarter'] == '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                    std_subject_marks_mid = std_subject_marks_mid+obt_mark.to_f
                  end  
                end

                std_subject_marks_mid = std_subject_marks_mid.round()
                std_subject_marks_final = std_subject_marks_final.round()
                subject_full_marks = ((std_subject_marks_final.to_f*30)/100)+((std_subject_marks_mid.to_f*70)/100)

                std_marks_full = std_marks_full+subject_full_marks.round()

                if @student_subject_marks[sub['id'].to_i].blank?
                  @student_subject_marks[sub['id'].to_i] = {}
                end

                if @student_subject_mark_test[std['id'].to_i].blank?
                  @student_subject_mark_test[std['id'].to_i] = {}
                end


                @student_subject_mark_test[std['id'].to_i][sub['id'].to_i] = subject_full_marks.round()
                @student_subject_marks[sub['id'].to_i][std['id'].to_i] = subject_full_marks.round()

                subject_grade = ""
                grade = GradingLevel.percentage_to_grade(subject_full_marks, @batch.id)
                if !grade.blank? and !grade.name.blank?
                  if grade.name == "U"
                    u_grade = u_grade+1
                  end
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

            @exam_comment_main_all = ExamConnectComment.find_by_exam_connect_id_and_student_id(connect_exam_id,std['id'].to_i)
            result = ""
            promotion_status = ""
            merit_position = ""
            new_roll = ""
            new_section = ""
            if !@exam_comment_main_all.blank?
              all_comments = @exam_comment_main_all.comments
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
              @student_list << [std_marks_full_new.to_f,std['id'].to_i,std_marks_full.to_f]
            else
              @student_avg_mark[std['id'].to_i] = u_grade
            end  

          end
        end
      end

      @student_real_position = {}
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
            @sections = ["Marigold","Camellia","Jasmine","Lily"]
          end
          if @promoted_to == "II"
            @sections = ["Rose","Sunflower","Gardenia","Lotus"]
          end
          if @promoted_to == "III"
            @sections = ["Orchid","Tulip","Cosmos","Lilac"]
          end
          if @promoted_to == "IV"
            @sections = ["Daisy","Daffodil","Salvia","Dahlia"]
          end
          if @promoted_to == "V"
            @sections = ["Bluebell","Lavender","Zinia","Rosemary"]
          end
          if @promoted_to == "VI"
            @sections = ["Primrose","Snowdrop","Magnolia"]
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
        position_rank = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"]
        @sorted_students = @student_list.sort
        position = 0
        real_position = 0
        p_rank_loop = 0
        prev_student = 0
        prev_mark = 0
        @sorted_students.each do|s|
          new_mark = s[0]
          if new_mark == prev_mark and position != 0
            if p_rank_loop == 0
              @student_position[prev_student] = @student_position[prev_student].to_s+position_rank[p_rank_loop].to_s
            end
            p_rank_loop = p_rank_loop+1
            @student_position[s[1].to_i] = position.to_s+position_rank[p_rank_loop].to_s      
          else
            position = position+1
            @student_position[s[1].to_i] = position
            prev_student = s[1].to_i
            p_rank_loop = 0
          end
          prev_mark = s[0]
          real_position = real_position+1
          @student_real_position[s[1].to_i] = real_position
        end

        @iloop = 0
        @jloop = 0

        @sloop = 0

        @s_a_loop = 1
        @s_b_loop = 1
        @s_c_loop = 1
        @s_d_loop = 1
        @new_sections = @sections
        while @iloop < @sorted_students.count do
          if @jloop > (@new_sections.count-1)
            @jloop = 0
            @new_sections = @new_sections.reverse
          end
          @iloop +=1 

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
          if @sections.count>3
            if @new_sections[@jloop] == @sections[3]
              @student_roll[@iloop] = @s_d_loop
              @s_d_loop +=1
            end
          end
          @jloop +=1
          @sloop +=1
        end

      end
    end
  end
  
  def finding_data_sems3
    if @student_position.blank?
      if @grading_levels.blank?
        @grading_levels = GradingLevel.for_batch(@batch.id)
        if @grading_levels.blank?
          @grading_levels = GradingLevel.default
        end
      end
    
      @student_list = []
      @student_subject_marks = {}
      @subject_highest = {}
      @student_avg_mark = {}
      unless @tabulation_data.blank?
        connect_exam = 0
        batch_loop = 0
        @tabulation_data['report'].each do |tab|
          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end
          batch_subject = Subject.find_all_by_batch_id(@tabulation_data['batches'][batch_loop], :conditions=>"elective_group_id IS NULL and is_deleted=false")
          batch_subject_id = batch_subject.map(&:id)
          batch_id = @tabulation_data['batches'][batch_loop]
          batch_loop = batch_loop+1
          connect_exam_id = @tabulation_data['connect_exams'][connect_exam]
          connect_exam = connect_exam+1


          tab['students'].each do |std| 
            subject_count_std = 0
            total_std_subject = StudentsSubject.find_all_by_student_id(std['id'].to_i)
            std_subject_id = total_std_subject.map(&:subject_id)


            assessment = 0 
            hw = 0 
            cw = 0 
            total_mark = 0 
            total_subject = 0
            unless tab['subjects'].blank? 
              tab['subjects'].each do |sub|
                if sub['grade_subject'].to_i == 1
                  next
                end
                if !batch_subject_id.include?(sub['id'].to_i) and !std_subject_id.include?(sub['id'].to_i)
                  next
                end  

                subjectdata = Subject.find(sub['id'].to_i)

                has_exam = false
                loop=0

                sub_assessment = 0
                sub_hw = 0
                sub_cw = 0
                sub_hw_full_mark = 0
                sub_cw_full_mark = 0
                total_subject = total_subject+1
                class_test = []

                unless tab['exams'].blank?  
                  tab['exams'].each do |rs|
                    if rs['quarter'] != '6' 
                      next
                    end
                    if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?

                      if rs['exam_category'] == '7'
                        sub_hw = sub_hw.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      end
                      if rs['exam_category'] == '8'
                        sub_cw = sub_cw.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      end
                      if rs['exam_category'] == '7'
                        sub_hw_full_mark = sub_hw_full_mark+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                      end
                      if rs['exam_category'] == '8'
                        sub_cw_full_mark = sub_cw_full_mark+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                      end
                    end
                  end
                end

                unless tab['exams'].blank?  
                  tab['exams'].each do |rs|
                    if rs['quarter'] != '6'
                      next
                    end
                    if rs['exam_category'] == '6'
                      if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                        class_test << rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        sub_assessment = sub_assessment.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      else
                        class_test << 0
                      end

                    end
                  end
                end


                hw_avg = 0
                assessment_avg = 0
                cw_avg = 0

                if sub_assessment > 0
                  class_test.sort! {|x,y| y <=> x }
                  ass_total_mark = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                  assessment_avg = (ass_total_mark.to_f/30.00)*50
                  assessment_avg = assessment_avg.round()
                  assessment = assessment+assessment_avg
                end
                if sub_hw > 0
                  hw_avg = (sub_hw.to_f/sub_hw_full_mark.to_f)*20
                  hw_avg = hw_avg.round()
                  hw = hw+hw_avg
                end
                if sub_cw > 0
                  cw_avg = (sub_cw.to_f/sub_cw_full_mark.to_f)*10
                  cw_avg = cw_avg.round()
                  cw = cw+cw_avg
                end  
              end  
            end

            ag_hw = 0
            ag_assessment = 0
            ag_cw = 0

            if total_subject > 0 && 
                if assessment > 0
                ag_assessment = assessment.to_f/total_subject.to_f
                ag_assessment = sprintf( "%0.01f", ag_assessment)
              end
              if hw > 0
                ag_hw = hw.to_f/total_subject.to_f
                ag_hw = sprintf( "%0.01f", ag_hw)
              end
              if cw > 0
                ag_cw = cw.to_f/total_subject.to_f
                ag_cw = sprintf( "%0.01f", ag_cw)
              end
            end
            total_mark = total_mark+ag_assessment.to_f+ag_hw.to_f+ag_cw.to_f

            unless tab['subjects'].blank? 
              finish = false
              total_mark_life = 0
              start = true

              tab['subjects'].each do |sub|
                if sub['grade_subject'].to_i != 1
                  next
                end
                if !batch_subject_id.include?(sub['id'].to_i) and !std_subject_id.include?(sub['id'].to_i)
                  next
                end


                sub_mark = 0
                unless tab['exams'].blank?  
                  tab['exams'].each do |rs|
                    if rs['quarter'] != '6' 
                      next
                    end
                    if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                      if rs['exam_category'] == '6'
                        sub_mark = sub_mark.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      end 
                    end
                  end
                end   
                sub_mark = sprintf( "%0.01f", sub_mark)
                total_mark_life = total_mark_life+sub_mark.to_f
              end
              total_mark_life = sprintf( "%0.01f", total_mark_life)
              total_mark = total_mark+total_mark_life.to_f
            end

            att_mark = 0
            if !@tabulation_data['adata'].blank? and !@tabulation_data['adata'][batch_id].blank? and !@tabulation_data['adata'][batch_id][0].blank?
              total = @tabulation_data['adata'][batch_id][0]
              present = @tabulation_data['adata'][batch_id][1][std['id']]
              att_mark = (present.to_f/total.to_f)*10
              att_mark = sprintf( "%0.01f", att_mark)
              total_mark = total_mark+att_mark.to_f
            end
            total_mark = sprintf( "%0.01f", total_mark)
            total_mark_final_term = total_mark


            assessment = 0 
            hw = 0 
            cw = 0 
            total_mark = 0 
            total_subject = 0
            unless tab['subjects'].blank? 
              tab['subjects'].each do |sub|
                if sub['grade_subject'].to_i == 1
                  next
                end
                if !batch_subject_id.include?(sub['id'].to_i) and !std_subject_id.include?(sub['id'].to_i)
                  next
                end

                subjectdata = Subject.find(sub['id'].to_i)

                has_exam = false
                loop=0

                sub_assessment = 0
                sub_hw = 0
                sub_cw = 0
                sub_hw_full_mark = 0
                sub_cw_full_mark = 0
                total_subject = total_subject+1
                class_test = []

                unless tab['exams'].blank?  
                  tab['exams'].each do |rs|
                    if rs['quarter'] == '6' 
                      next
                    end
                    if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?

                      if rs['exam_category'] == '7'
                        sub_hw = sub_hw.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      end
                      if rs['exam_category'] == '8'
                        sub_cw = sub_cw.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      end
                      if rs['exam_category'] == '7'
                        sub_hw_full_mark = sub_hw_full_mark+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                      end
                      if rs['exam_category'] == '8'
                        sub_cw_full_mark = sub_cw_full_mark+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                      end
                    end
                  end
                end

                unless tab['exams'].blank?  
                  tab['exams'].each do |rs|
                    if rs['quarter'] == '6' 
                      next
                    end
                    if rs['exam_category'] == '6'
                      if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                        class_test << rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        sub_assessment = sub_assessment.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      else
                        class_test << 0
                      end

                    end
                  end
                end


                hw_avg = 0
                assessment_avg = 0
                cw_avg = 0

                if sub_assessment > 0
                  class_test.sort! {|x,y| y <=> x }
                  ass_total_mark = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                  assessment_avg = (ass_total_mark.to_f/30.00)*50
                  assessment_avg = assessment_avg.round()
                  assessment = assessment+assessment_avg
                end
                if sub_hw > 0
                  hw_avg = (sub_hw.to_f/sub_hw_full_mark.to_f)*20
                  hw_avg = hw_avg.round()
                  hw = hw+hw_avg
                end
                if sub_cw > 0
                  cw_avg = (sub_cw.to_f/sub_cw_full_mark.to_f)*10
                  cw_avg = cw_avg.round()
                  cw = cw+cw_avg
                end  
              end  
            end

            ag_hw = 0
            ag_assessment = 0
            ag_cw = 0

            if total_subject > 0 && 
                if assessment > 0
                ag_assessment = assessment.to_f/total_subject.to_f
                ag_assessment = sprintf( "%0.01f", ag_assessment)
              end
              if hw > 0
                ag_hw = hw.to_f/total_subject.to_f
                ag_hw = sprintf( "%0.01f", ag_hw)
              end
              if cw > 0
                ag_cw = cw.to_f/total_subject.to_f
                ag_cw = sprintf( "%0.01f", ag_cw)
              end
            end
            total_mark = total_mark+ag_assessment.to_f+ag_hw.to_f+ag_cw.to_f

            unless tab['subjects'].blank? 
              finish = false
              total_mark_life = 0
              start = true

              tab['subjects'].each do |sub|
                if sub['grade_subject'].to_i != 1
                  next
                end
                if !batch_subject_id.include?(sub['id'].to_i) and !std_subject_id.include?(sub['id'].to_i)
                  next
                end


                sub_mark = 0
                unless tab['exams'].blank?  
                  tab['exams'].each do |rs|
                    if rs['quarter'] == '6' 
                      next
                    end
                    if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                      if rs['exam_category'] == '6'
                        sub_mark = sub_mark.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      end 
                    end
                  end
                end   
                sub_mark = sprintf( "%0.01f", sub_mark)
                total_mark_life = total_mark_life+sub_mark.to_f
              end
              total_mark_life = sprintf( "%0.01f", total_mark_life)
              total_mark = total_mark+total_mark_life.to_f
            end

            att_mark = 0
            if !@tabulation_data['adata_first_term'].blank? and !@tabulation_data['adata_first_term'][batch_id.to_s].blank? and !@tabulation_data['adata_first_term'][batch_id.to_s][0].blank?
              total_days = @tabulation_data['adata_first_term'][batch_id.to_s][0]
              present = @tabulation_data['adata_first_term'][batch_id.to_s][1][std['id'].to_s]
              att_mark = (present.to_f/total_days.to_f)*10
              att_mark = sprintf( "%0.01f", att_mark)
              total_mark = total_mark+att_mark.to_f
            end
            total_mark = sprintf( "%0.01f", total_mark)
            total_mark_mid_term = total_mark
            total_70 = (total_mark_mid_term.to_f*70)/100
            total_30 = (total_mark_final_term.to_f*30)/100
            main_total = total_70+total_30
            main_total = sprintf( "%0.01f", main_total)
            main_total = main_total.to_f
            grade = GradingLevel.percentage_to_grade(main_total, @batch.id)
            u_grade = 0
            if !grade.blank? and !grade.name.blank?
              if grade.name == "U"
                u_grade = u_grade+1
              end
            end


            @exam_comment_main_all = ExamConnectComment.find_by_exam_connect_id_and_student_id(connect_exam_id,std['id'].to_i)
            result = ""
            promotion_status = ""
            merit_position = ""
            new_roll = ""
            new_section = ""
            if !@exam_comment_main_all.blank?
              all_comments = @exam_comment_main_all.comments
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
            if u_grade == 0  
              std_marks_full_new = 5000.00-main_total.to_f
              @student_list << [std_marks_full_new.to_f,std['id'].to_i]
            end


          end
        end
      end

      @student_real_position = {}
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
            @sections = ["Marigold","Camellia","Jasmine","Lily"]
          end
          if @promoted_to == "II"
            @sections = ["Rose","Sunflower","Gardenia","Lotus"]
          end
          if @promoted_to == "III"
            @sections = ["Orchid","Tulip","Cosmos","Lilac"]
          end
          if @promoted_to == "IV"
            @sections = ["Daisy","Daffodil","Salvia","Dahlia"]
          end
          if @promoted_to == "V"
            @sections = ["Bluebell","Lavender","Zinia","Rosemary"]
          end
          if @promoted_to == "VI"
            @sections = ["Primrose","Snowdrop","Magnolia"]
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
        position_rank = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"]
        @sorted_students = @student_list.sort
        position = 0
        real_position = 0
        p_rank_loop = 0
        prev_student = 0
        prev_mark = 0
        @sorted_students.each do|s|
          new_mark = s[0]
          if new_mark == prev_mark and position != 0
            if p_rank_loop == 0
              @student_position[prev_student] = @student_position[prev_student].to_s+position_rank[p_rank_loop].to_s
            end
            p_rank_loop = p_rank_loop+1
            @student_position[s[1].to_i] = position.to_s+position_rank[p_rank_loop].to_s      
          else
            position = position+1
            @student_position[s[1].to_i] = position
            prev_student = s[1].to_i
            p_rank_loop = 0
          end
          prev_mark = s[0]
          real_position = real_position+1
          @student_real_position[s[1].to_i] = real_position
        end

        @iloop = 0
        @jloop = 0

        @sloop = 0

        @s_a_loop = 1
        @s_b_loop = 1
        @s_c_loop = 1
        @s_d_loop = 1
        @new_sections = @sections
        while @iloop < @sorted_students.count do
          if @jloop > (@new_sections.count-1)
            @jloop = 0
            @new_sections = @new_sections.reverse
          end
          @iloop +=1 

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
          if @sections.count>3
            if @new_sections[@jloop] == @sections[3]
              @student_roll[@iloop] = @s_d_loop
              @s_d_loop +=1
            end
          end
          @jloop +=1
          @sloop +=1
        end

      end
    end
  end

  def finding_data_sems4
    if @student_position.blank?
      if @grading_levels.blank?
        @grading_levels = GradingLevel.for_batch(@batch.id)
        if @grading_levels.blank?
          @grading_levels = GradingLevel.default
        end
      end
      @student_list = []
      @student_subject_marks = {}
      @student_subject_mark_test = {}
      @subject_highest = {}
      @student_avg_mark = {}
      unless @tabulation_data.blank?
        connect_exam = 0
        batch_loop = 0
        @tabulation_data['report'].each do |tab|
          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end
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
            
              has_exam = true
              loop = 0
              
              if batch_subject_id.include?(sub['id'].to_i) or std_subject_id.include?(sub['id'].to_i)
                
                subject_count_std = subject_count_std+1
                std_subject_marks_final = 0
                class_test = []
                tab['exams'].each do |rs|
                  if rs['exam_category'] != '1' or rs['quarter'] != '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*7.5
                    class_test << obt_mark.round(2)
                  else
                    class_test << 0
                  end  
                end 
                class_test.sort! {|x,y| y <=> x }
                std_subject_marks_final = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                tab['exams'].each do |rs|
                  if rs['exam_category'] == '1' or rs['exam_category'] == '3' or rs['quarter'] != '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*2.5
                    std_subject_marks_final = std_subject_marks_final+obt_mark.to_f
                  end  
                end
              
                tab['exams'].each do |rs|
                  if rs['exam_category'] != '3' or rs['quarter'] != '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                    std_subject_marks_final = std_subject_marks_final+obt_mark.to_f
                  end  
                end

                std_subject_marks_mid = 0
                class_test = []
                
                
                tab['exams'].each do |rs|
                  if rs['exam_category'] != '1' or rs['quarter'] == '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*7.5
                    class_test << obt_mark.round(2)
                  else
                    class_test << 0
                  end  
                end 
                class_test.sort! {|x,y| y <=> x }
                std_subject_marks_mid = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                tab['exams'].each do |rs|
                  if rs['exam_category'] == '1' or rs['exam_category'] == '3' or rs['quarter'] == '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*2.5
                    std_subject_marks_mid = std_subject_marks_mid+obt_mark.to_f
                  end  
                end
                
                
                tab['exams'].each do |rs|
                  if rs['exam_category'] != '3' or rs['quarter'] == '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                    std_subject_marks_mid = std_subject_marks_mid+obt_mark.to_f
                  end  
                end
                
                std_subject_marks_mid = std_subject_marks_mid.round()
                std_subject_marks_final = std_subject_marks_final.round()
                subject_full_marks = ((std_subject_marks_final.to_f*70)/100)+((std_subject_marks_mid.to_f*30)/100)

                std_marks_full = std_marks_full+subject_full_marks.round()
                
                if @student_subject_marks[sub['id'].to_i].blank?
                  @student_subject_marks[sub['id'].to_i] = {}
                end
              
                if @student_subject_mark_test[std['id'].to_i].blank?
                  @student_subject_mark_test[std['id'].to_i] = {}
                end
              
                
                @student_subject_mark_test[std['id'].to_i][sub['id'].to_i] = subject_full_marks.round()
                @student_subject_marks[sub['id'].to_i][std['id'].to_i] = subject_full_marks.round()
              
              subject_grade = ""
              grade = GradingLevel.percentage_to_grade(subject_full_marks, @batch.id)
              if !grade.blank? and !grade.name.blank?
                if grade.name == "U"
                  u_grade = u_grade+1
                end
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

            @exam_comment_main_all = ExamConnectComment.find_by_exam_connect_id_and_student_id(connect_exam_id,std['id'].to_i)
            result = ""
            promotion_status = ""
            merit_position = ""
            new_roll = ""
            new_section = ""
            if !@exam_comment_main_all.blank?
              all_comments = @exam_comment_main_all.comments
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
              @student_list << [std_marks_full_new.to_f,std_marks_core_subject,std['first_name'],std['id'].to_i]
            else
              @student_avg_mark[std['id'].to_i] = u_grade
            end  

          end
        end
      end

        @student_real_position = {}
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
                @sections = ["Marigold","Camellia","Jasmine","Lily"]
              end
              if @promoted_to == "II"
                @sections = ["Rose","Sunflower","Gardenia","Lotus"]
              end
              if @promoted_to == "III"
                @sections = ["Orchid","Tulip","Cosmos","Lilac"]
              end
              if @promoted_to == "IV"
                @sections = ["Daisy","Daffodil","Salvia","Dahlia"]
              end
              if @promoted_to == "V"
                @sections = ["Bluebell","Lavender","Zinia","Rosemary"]
              end
              if @promoted_to == "VI"
                @sections = ["Primrose","Snowdrop","Magnolia","Carnation"]
              end
              if @promoted_to == "VII"
                @sections = ["Parrot","Swift","Bluebird"]
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
          position_rank = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"]
          @sorted_students = @student_list.sort
          position = 0
          real_position = 0
          p_rank_loop = 0
          prev_student = 0
          prev_mark = 0
          @sorted_students.each do|s|
              new_mark = s[0]
              if new_mark == prev_mark and position != 0
                if p_rank_loop == 0
                  @student_position[prev_student] = @student_position[prev_student].to_s+position_rank[p_rank_loop].to_s
                end
                p_rank_loop = p_rank_loop+1
                @student_position[s[3].to_i] = position.to_s+position_rank[p_rank_loop].to_s      
              else
                position = position+1
                @student_position[s[3].to_i] = position
                prev_student = s[3].to_i
                p_rank_loop = 0
              end
              prev_mark = s[0]
              real_position = real_position+1
              @student_real_position[s[3].to_i] = real_position
          end

          @iloop = 0
          @jloop = 0

          @sloop = 0

          @s_a_loop = 1
          @s_b_loop = 1
          @s_c_loop = 1
          @s_d_loop = 1
          @new_sections = @sections
          while @iloop < @sorted_students.count do
              if @jloop > (@new_sections.count-1)
                @jloop = 0
                @new_sections = @new_sections.reverse
              end
              @iloop +=1 
              
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
              if @sections.count>3
                if @new_sections[@jloop] == @sections[3]
                  @student_roll[@iloop] = @s_d_loop
                  @s_d_loop +=1
                end
              end
              @jloop +=1
              @sloop +=1
          end

        end
      end
  end
  
  def finding_data_sems5
    if @student_position.blank?
      if @grading_levels.blank?
      @grading_levels = GradingLevel.for_batch(@batch.id)
         if @grading_levels.blank?
          @grading_levels = GradingLevel.default
         end
      end
      
      @student_list = []
      @student_subject_marks = {}
      @subject_highest = {}
      @student_avg_mark = {}
      unless @tabulation_data.blank?
        connect_exam = 0
        batch_loop = 0
        @tabulation_data['report'].each do |tab|
          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end
          batch_subject = Subject.find_all_by_batch_id(@tabulation_data['batches'][batch_loop], :conditions=>"elective_group_id IS NULL and is_deleted=false")
          batch_subject_id = batch_subject.map(&:id)
          batch_id = @tabulation_data['batches'][batch_loop]
          batch_loop = batch_loop+1
          connect_exam_id = @tabulation_data['connect_exams'][connect_exam]
          connect_exam = connect_exam+1
          
          
          tab['students'].each do |std| 
            subject_count_std = 0
            std_marks_core_subject = 0
            total_std_subject = StudentsSubject.find_all_by_student_id(std['id'].to_i)
            std_subject_id = total_std_subject.map(&:subject_id)
            
          
            assessment = 0 
            hw = 0 
            cw = 0 
            total_mark = 0 
            total_subject = 0
            unless tab['subjects'].blank? 
              tab['subjects'].each do |sub|
                if sub['grade_subject'].to_i == 1
                  next
                end
                if !batch_subject_id.include?(sub['id'].to_i) and !std_subject_id.include?(sub['id'].to_i)
                  next
                end  
    
                subjectdata = Subject.find(sub['id'].to_i)
    
                has_exam = false
                loop=0
                subject_count_std = subject_count_std+1
               
                sub_assessment = 0
                sub_hw = 0
                sub_cw = 0
                sub_hw_full_mark = 0
                sub_cw_full_mark = 0
                total_subject = total_subject+1
                class_test = []
    
                unless tab['exams'].blank?  
                  tab['exams'].each do |rs|
                    if rs['quarter'] != '6' 
                      next
                    end
                    if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
    
                      if rs['exam_category'] == '7'
                        sub_hw = sub_hw.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        end
                      if rs['exam_category'] == '8'
                        sub_cw = sub_cw.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      end
                      if rs['exam_category'] == '7'
                        sub_hw_full_mark = sub_hw_full_mark+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                        end
                      if rs['exam_category'] == '8'
                        sub_cw_full_mark = sub_cw_full_mark+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                      end
                    end
                  end
                  end
    
                unless tab['exams'].blank?  
                  tab['exams'].each do |rs|
                    if rs['quarter'] != '6'
                      next
                    end
                    if rs['exam_category'] == '6'
                      if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                        class_test << rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        sub_assessment = sub_assessment.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        else
                        class_test << 0
                      end
    
                    end
                  end
                  end
    
    
                hw_avg = 0
                assessment_avg = 0
                cw_avg = 0
    
                if sub_assessment > 0
                  class_test.sort! {|x,y| y <=> x }
                  ass_total_mark = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                  assessment_avg = (ass_total_mark.to_f/30.00)*50
                  assessment_avg = assessment_avg.round()
                  assessment = assessment+assessment_avg
                  end
                if sub_hw > 0
                  hw_avg = (sub_hw.to_f/sub_hw_full_mark.to_f)*30
                  hw_avg = hw_avg.round()
                  hw = hw+hw_avg
                end
                if sub_cw > 0
                  cw_avg = (sub_cw.to_f/sub_cw_full_mark.to_f)*10
                  cw_avg = cw_avg.round()
                  cw = cw+cw_avg
                end 
                if subject_count_std == 3
                  std_marks_core_subject = assessment.to_f+hw.to_f+cw.to_f
                end 
              end  
            end
          
            ag_hw = 0
            ag_assessment = 0
            ag_cw = 0
    
            if total_subject > 0 && 
              if assessment > 0
                ag_assessment = assessment.to_f/total_subject.to_f
                ag_assessment = sprintf( "%0.01f", ag_assessment)
              end
              if hw > 0
                ag_hw = hw.to_f/total_subject.to_f
                ag_hw = sprintf( "%0.01f", ag_hw)
              end
              if cw > 0
                ag_cw = cw.to_f/total_subject.to_f
                ag_cw = sprintf( "%0.01f", ag_cw)
              end
            end
            total_mark = total_mark+ag_assessment.to_f+ag_hw.to_f+ag_cw.to_f
    
            unless tab['subjects'].blank? 
                finish = false
                total_mark_life = 0
                start = true
               
                tab['subjects'].each do |sub|
                  if sub['grade_subject'].to_i != 1
                    next
                  end
                  if !batch_subject_id.include?(sub['id'].to_i) and !std_subject_id.include?(sub['id'].to_i)
                    next
                  end
    
                
                  sub_mark = 0
                  unless tab['exams'].blank?  
                    tab['exams'].each do |rs|
                        if rs['quarter'] != '6' 
                          next
                        end
                        if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                          if rs['exam_category'] == '6'
                            sub_mark = sub_mark.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                          end 
                        end
                    end
                  end   
                  sub_mark = sprintf( "%0.01f", sub_mark)
                  total_mark_life = total_mark_life+sub_mark.to_f
                end
                total_mark_life = sprintf( "%0.01f", total_mark_life)
                total_mark = total_mark+total_mark_life.to_f
            end
    
          
            total_mark = sprintf( "%0.01f", total_mark)
            total_mark_final_term = total_mark
            
            
            assessment = 0 
            hw = 0 
            cw = 0 
            total_mark = 0 
            total_subject = 0
            unless tab['subjects'].blank? 
              tab['subjects'].each do |sub|
                if sub['grade_subject'].to_i == 1
                  next
                end
                if !batch_subject_id.include?(sub['id'].to_i) and !std_subject_id.include?(sub['id'].to_i)
                  next
                end
    
                subjectdata = Subject.find(sub['id'].to_i)
    
                has_exam = false
                loop=0
               
                sub_assessment = 0
                sub_hw = 0
                sub_cw = 0
                sub_hw_full_mark = 0
                sub_cw_full_mark = 0
                total_subject = total_subject+1
                class_test = []
    
                unless tab['exams'].blank?  
                  tab['exams'].each do |rs|
                    if rs['quarter'] == '6' 
                      next
                    end
                    if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
    
                      if rs['exam_category'] == '7'
                        sub_hw = sub_hw.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        end
                      if rs['exam_category'] == '8'
                        sub_cw = sub_cw.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                      end
                      if rs['exam_category'] == '7'
                        sub_hw_full_mark = sub_hw_full_mark+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                        end
                      if rs['exam_category'] == '8'
                        sub_cw_full_mark = sub_cw_full_mark+rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_i
                      end
                    end
                  end
                  end
    
                unless tab['exams'].blank?  
                  tab['exams'].each do |rs|
                    if rs['quarter'] == '6' 
                      next
                    end
                    if rs['exam_category'] == '6'
                      if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                        class_test << rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        sub_assessment = sub_assessment.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                        else
                        class_test << 0
                      end
    
                    end
                  end
                  end
    
    
                hw_avg = 0
                assessment_avg = 0
                cw_avg = 0
    
                if sub_assessment > 0
                  class_test.sort! {|x,y| y <=> x }
                  ass_total_mark = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                  assessment_avg = (ass_total_mark.to_f/30.00)*50
                  assessment_avg = assessment_avg.round()
                  assessment = assessment+assessment_avg
                  end
                if sub_hw > 0
                  hw_avg = (sub_hw.to_f/sub_hw_full_mark.to_f)*30
                  hw_avg = hw_avg.round()
                  hw = hw+hw_avg
                  end
                if sub_cw > 0
                  cw_avg = (sub_cw.to_f/sub_cw_full_mark.to_f)*10
                  cw_avg = cw_avg.round()
                  cw = cw+cw_avg
                end  
              end  
            end
          
            ag_hw = 0
            ag_assessment = 0
            ag_cw = 0
    
            if total_subject > 0 && 
              if assessment > 0
                ag_assessment = assessment.to_f/total_subject.to_f
                ag_assessment = sprintf( "%0.01f", ag_assessment)
              end
              if hw > 0
                ag_hw = hw.to_f/total_subject.to_f
                ag_hw = sprintf( "%0.01f", ag_hw)
              end
              if cw > 0
                ag_cw = cw.to_f/total_subject.to_f
                ag_cw = sprintf( "%0.01f", ag_cw)
              end
            end
            total_mark = total_mark+ag_assessment.to_f+ag_hw.to_f+ag_cw.to_f
    
            unless tab['subjects'].blank? 
                finish = false
                total_mark_life = 0
                start = true
                
                tab['subjects'].each do |sub|
                  if sub['grade_subject'].to_i != 1
                    next
                  end
                  if !batch_subject_id.include?(sub['id'].to_i) and !std_subject_id.include?(sub['id'].to_i)
                    next
                  end
    
                  
                  sub_mark = 0
                  unless tab['exams'].blank?  
                    tab['exams'].each do |rs|
                        if rs['quarter'] == '6' 
                          next
                        end
                        if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                          if rs['exam_category'] == '6'
                            sub_mark = sub_mark.to_f+rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                          end 
                        end
                    end
                  end   
                  sub_mark = sprintf( "%0.01f", sub_mark)
                  total_mark_life = total_mark_life+sub_mark.to_f
                end
                total_mark_life = sprintf( "%0.01f", total_mark_life)
                total_mark = total_mark+total_mark_life.to_f
            end
    
           
            total_mark = sprintf( "%0.01f", total_mark)
            total_mark_mid_term = total_mark
            total_30 = (total_mark_mid_term.to_f*30)/100
            total_70 = (total_mark_final_term.to_f*70)/100
            main_total = total_70+total_30
            main_total = sprintf( "%0.01f", main_total)
            main_total = main_total.to_f
            grade = GradingLevel.percentage_to_grade(main_total, @batch.id)
            u_grade = 0
            if !grade.blank? and !grade.name.blank?
              if grade.name == "U"
               u_grade = u_grade+1
              end
            end
            
    
            @exam_comment_main_all = ExamConnectComment.find_by_exam_connect_id_and_student_id(connect_exam_id,std['id'].to_i)
            result = ""
            promotion_status = ""
            merit_position = ""
            new_roll = ""
            new_section = ""
            if !@exam_comment_main_all.blank?
              all_comments = @exam_comment_main_all.comments
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
            if u_grade == 0  
              std_marks_full_new = 5000.00-main_total.to_f
              std_marks_core_subject = 5000-std_marks_core_subject.round()
              @student_list << [std_marks_full_new.to_f,std_marks_core_subject.to_f,std['first_name'],std['id'].to_i]
            end
             
    
          end
        end
       end
    
        @student_real_position = {}
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
                @sections = ["Marigold","Camellia","Jasmine","Lily"]
              end
              if @promoted_to == "II"
                @sections = ["Rose","Sunflower","Gardenia","Lotus"]
              end
              if @promoted_to == "III"
                @sections = ["Orchid","Tulip","Cosmos","Lilac"]
              end
              if @promoted_to == "IV"
                @sections = ["Daisy","Daffodil","Salvia","Dahlia"]
              end
              if @promoted_to == "V"
                @sections = ["Bluebell","Lavender","Zinia","Rosemary"]
              end
              if @promoted_to == "VI"
                @sections = ["Primrose","Snowdrop","Magnolia"]
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
          position_rank = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"]
          @sorted_students = @student_list.sort
          position = 0
          real_position = 0
          p_rank_loop = 0
          prev_student = 0
          prev_mark = 0
          @sorted_students.each do|s|
              new_mark = s[0]
              if new_mark == prev_mark and position != 0
                if p_rank_loop == 0
                  @student_position[prev_student] = @student_position[prev_student].to_s+position_rank[p_rank_loop].to_s
                end
                p_rank_loop = p_rank_loop+1
                @student_position[s[3].to_i] = position.to_s+position_rank[p_rank_loop].to_s      
              else
                position = position+1
                @student_position[s[3].to_i] = position
                prev_student = s[3].to_i
                p_rank_loop = 0
              end
              prev_mark = s[0]
              real_position = real_position+1
              @student_real_position[s[3].to_i] = real_position
          end
    
          @iloop = 0
          @jloop = 0
    
          @sloop = 0
    
          @s_a_loop = 1
          @s_b_loop = 1
          @s_c_loop = 1
          @s_d_loop = 1
          @new_sections = @sections
          while @iloop < @sorted_students.count do
              if @jloop > (@new_sections.count-1)
                @jloop = 0
                @new_sections = @new_sections.reverse
              end
              @iloop +=1 
              
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
              if @sections.count>3
                if @new_sections[@jloop] == @sections[3]
                  @student_roll[@iloop] = @s_d_loop
                  @s_d_loop +=1
                end
              end
              @jloop +=1
              @sloop +=1
          end
    
        end
      end

  end

  

  def sems_finding_data_4
    if @student_position.blank?
      if @grading_levels.blank?
        @grading_levels = GradingLevel.for_batch(@batch.id)
        if @grading_levels.blank?
          @grading_levels = GradingLevel.default
        end
      end

      @subject_result = {}
      @summary_result = {}
      @total_students = 0
      @failed_student = 0

      @student_list = []
      @student_subject_marks = {}
      @subject_highest = {}
      @student_avg_mark = {}
      @subjects = []
      subject_code = []
      unless @tabulation_data.blank?
        connect_exam = 0
        batch_loop = 0
        @tabulation_data['report'].each do |tab|
          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end
          batch_subject = Subject.find_all_by_batch_id(@tabulation_data['batches'][batch_loop], :conditions=>"elective_group_id IS NULL and is_deleted=false")
          batch_subject_id = batch_subject.map(&:id)
          batch_loop = batch_loop+1
          connect_exam_id = @tabulation_data['connect_exams'][connect_exam]
          connect_exam = connect_exam+1
          if connect_exam_id.to_i != @connect_exam_obj.id and @class.blank?
            next
          end  
          
          tab['students'].each do |std| 
            subject_count_std = 0
            total_std_subject = StudentsSubject.find_all_by_student_id(std['id'].to_i)
            std_subject_id = total_std_subject.map(&:subject_id)
            
            std_marks_full = 0
            std_marks_core_subject = 0
            total_subject = 0
            u_grade = 0
            number_of_subject = 0
            total_credit = 0
            tab['subjects'].each do |sub|

              unless subject_code.include?(sub['code'])
                subject_code << sub['code']
                @subjects << sub
              end  
            
              has_exam = false
              loop = 0
              tab['exams'].each do |rs|
                loop = loop+1  
                if loop>2
                  break
                end
                if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].blank?
                  has_exam = true
                  break
                end  
              end 
              if has_exam == false
                next
              end  
              if batch_subject_id.include?(sub['id'].to_i) or std_subject_id.include?(sub['id'].to_i)
                
                subject_count_std = subject_count_std+1
                std_subject_marks_final = 0
                class_test = []
                tab['exams'].each do |rs|
                  if rs['exam_category'] != '1' or rs['quarter'] != '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*7.5
                    class_test << obt_mark.round(2)
                  else
                    class_test << 0
                  end  
                end 
                class_test.sort! {|x,y| y <=> x }
                std_subject_marks_final = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                tab['exams'].each do |rs|
                  if rs['exam_category'] == '1' or rs['exam_category'] == '3' or rs['quarter'] != '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*2.5
                    std_subject_marks_final = std_subject_marks_final+obt_mark.to_f
                  end  
                end
              
                tab['exams'].each do |rs|
                  if rs['exam_category'] != '3' or rs['quarter'] != '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                    std_subject_marks_final = std_subject_marks_final+obt_mark.to_f
                  end  
                end

                std_subject_marks_mid = 0
                class_test = []
                
                
                tab['exams'].each do |rs|
                  if rs['exam_category'] != '1' or rs['quarter'] == '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*7.5
                    class_test << obt_mark.round(2)
                  else
                    class_test << 0
                  end  
                end 
                class_test.sort! {|x,y| y <=> x }
                std_subject_marks_mid = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                tab['exams'].each do |rs|
                  if rs['exam_category'] == '1' or rs['exam_category'] == '3' or rs['quarter'] == '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*2.5
                    std_subject_marks_mid = std_subject_marks_mid+obt_mark.to_f
                  end  
                end
                
                
                tab['exams'].each do |rs|
                  if rs['exam_category'] != '3' or rs['quarter'] == '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                    std_subject_marks_mid = std_subject_marks_mid+obt_mark.to_f
                  end  
                end
                
                std_subject_marks_mid = std_subject_marks_mid.round()
                std_subject_marks_final = std_subject_marks_final.round()
                subject_full_marks = ((std_subject_marks_final.to_f*70)/100)+((std_subject_marks_mid.to_f*30)/100)

                std_marks_full = std_marks_full+subject_full_marks.round()
                
                if @student_subject_marks[sub['id'].to_i].blank?
                  @student_subject_marks[sub['id'].to_i] = {}
                end
              
                if @student_subject_mark_test[std['id'].to_i].blank?
                  @student_subject_mark_test[std['id'].to_i] = {}
                end
              
                
                @student_subject_mark_test[std['id'].to_i][sub['id'].to_i] = subject_full_marks.round()
                @student_subject_marks[sub['id'].to_i][std['id'].to_i] = subject_full_marks.round()

                number_of_subject = number_of_subject+1
              
                subject_grade = ""
                grade = GradingLevel.percentage_to_grade(subject_full_marks, @batch.id)
                if !grade.blank? and !grade.name.blank?
                  sub['name'].gsub! ' ', '-'
                  if @subject_result[sub['name']].blank?
                    @subject_result[sub['name']] = {}
                  end  
                  if @subject_result[sub['name']][grade.name].blank?
                    @subject_result[sub['name']][grade.name] = 0
                  end 
                  total_credit = total_credit+grade.credit_points.to_f
                  @subject_result[sub['name']][grade.name] = @subject_result[sub['name']][grade.name]+1 
                  if grade.name == "U"
                    u_grade = u_grade+1
                  end
                end   
              end  
            end
            
            #students
            @total_students = @total_students+1

            @grade_name_main = "U"
            if u_grade == 0 && total_credit > 0
              grade_point_avg = total_credit.to_f/number_of_subject.to_f
              gradeObj = GradingLevel.grade_point_to_grade(grade_point_avg, @batch.id)
              if !gradeObj.blank? and !gradeObj.name.blank?
                if @summary_result[gradeObj.name].blank?
                  @summary_result[gradeObj.name] = 0
                end 
                @summary_result[gradeObj.name] = @summary_result[gradeObj.name]+1 
              end  
            else
              @failed_student = @failed_student+1
            end 
          end
        end
      end
    end
  end 

  def sems_finding_data_2
    if @student_position.blank?
      if @grading_levels.blank?
        @grading_levels = GradingLevel.for_batch(@batch.id)
        if @grading_levels.blank?
          @grading_levels = GradingLevel.default
        end
      end

      @subject_result = {}
      @summary_result = {}
      @total_students = 0
      @failed_student = 0

      @student_list = []
      @student_subject_marks = {}
      @subject_highest = {}
      @student_avg_mark = {}
      @subjects = []
      subject_code = []
      unless @tabulation_data.blank?
        connect_exam = 0
        batch_loop = 0
        @tabulation_data['report'].each do |tab|
          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end
          batch_subject = Subject.find_all_by_batch_id(@tabulation_data['batches'][batch_loop], :conditions=>"elective_group_id IS NULL and is_deleted=false")
          batch_subject_id = batch_subject.map(&:id)
          batch_loop = batch_loop+1
          connect_exam_id = @tabulation_data['connect_exams'][connect_exam]
          connect_exam = connect_exam+1
          if connect_exam_id.to_i != @connect_exam_obj.id and @class.blank?
            next
          end  
          
          tab['students'].each do |std| 
            subject_count_std = 0
            total_std_subject = StudentsSubject.find_all_by_student_id(std['id'].to_i)
            std_subject_id = total_std_subject.map(&:subject_id)
            
            std_marks_full = 0
            std_marks_core_subject = 0
            total_subject = 0
            u_grade = 0
            number_of_subject = 0
            total_credit = 0
            tab['subjects'].each do |sub|

              unless subject_code.include?(sub['code'])
                subject_code << sub['code']
                @subjects << sub
              end  
            
              has_exam = false
              loop = 0
              tab['exams'].each do |rs|
                loop = loop+1  
                if loop>2
                  break
                end
                if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].blank?
                  has_exam = true
                  break
                end  
              end 
              if has_exam == false
                next
              end  
              if batch_subject_id.include?(sub['id'].to_i) or std_subject_id.include?(sub['id'].to_i)
                
                subject_count_std = subject_count_std+1
                std_subject_marks_final = 0
                class_test = []
                tab['exams'].each do |rs|
                  if rs['exam_category'] != '1' or rs['quarter'] != '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*7.5
                    class_test << obt_mark.round(2)
                  else
                    class_test << 0
                  end  
                end 
                class_test.sort! {|x,y| y <=> x }
                std_subject_marks_final = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                tab['exams'].each do |rs|
                  if rs['exam_category'] == '1' or rs['exam_category'] == '3' or rs['quarter'] != '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*2.5
                    std_subject_marks_final = std_subject_marks_final+obt_mark.to_f
                  end  
                end
              
                tab['exams'].each do |rs|
                  if rs['exam_category'] != '3' or rs['quarter'] != '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f*2
                    std_subject_marks_final = std_subject_marks_final+obt_mark.to_f
                  end  
                end

                std_subject_marks_mid = 0
                class_test = []
                
                
                tab['exams'].each do |rs|
                  if rs['exam_category'] != '1' or rs['quarter'] == '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*7.5
                    class_test << obt_mark.round(2)
                  else
                    class_test << 0
                  end  
                end 
                class_test.sort! {|x,y| y <=> x }
                std_subject_marks_mid = class_test[0].to_f+class_test[1].to_f+class_test[2].to_f
                tab['exams'].each do |rs|
                  if rs['exam_category'] == '1' or rs['exam_category'] == '3' or rs['quarter'] == '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = (rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f/rs['result'][rs['exam_id']][sub['id']][std['id']]['full_mark'].to_f)*2.5
                    std_subject_marks_mid = std_subject_marks_mid+obt_mark.to_f
                  end  
                end
                
                
                tab['exams'].each do |rs|
                  if rs['exam_category'] != '3' or rs['quarter'] == '6'
                    next
                  end  
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    obt_mark = rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f
                    std_subject_marks_mid = std_subject_marks_mid+obt_mark.to_f
                  end  
                end
                
                std_subject_marks_mid = std_subject_marks_mid.round()
                std_subject_marks_final = std_subject_marks_final.round()
                subject_full_marks = ((std_subject_marks_final.to_f*30)/100)+((std_subject_marks_mid.to_f*70)/100)

                std_marks_full = std_marks_full+subject_full_marks.round()
                
                if @student_subject_marks[sub['id'].to_i].blank?
                  @student_subject_marks[sub['id'].to_i] = {}
                end
              
                if @student_subject_mark_test[std['id'].to_i].blank?
                  @student_subject_mark_test[std['id'].to_i] = {}
                end
              
                
                @student_subject_mark_test[std['id'].to_i][sub['id'].to_i] = subject_full_marks.round()
                @student_subject_marks[sub['id'].to_i][std['id'].to_i] = subject_full_marks.round()

                number_of_subject = number_of_subject+1
              
                subject_grade = ""
                grade = GradingLevel.percentage_to_grade(subject_full_marks, @batch.id)
                if !grade.blank? and !grade.name.blank?
                  sub['name'].gsub! ' ', '-'
                  if @subject_result[sub['name']].blank?
                    @subject_result[sub['name']] = {}
                  end  
                  if @subject_result[sub['name']][grade.name].blank?
                    @subject_result[sub['name']][grade.name] = 0
                  end 
                  total_credit = total_credit+grade.credit_points.to_f
                  @subject_result[sub['name']][grade.name] = @subject_result[sub['name']][grade.name]+1 
                  if grade.name == "U"
                    u_grade = u_grade+1
                  end
                end   
              end  
            end
            
            #students
            @total_students = @total_students+1

            @grade_name_main = "U"
            if u_grade == 0 && total_credit > 0
              grade_point_avg = total_credit.to_f/number_of_subject.to_f
              gradeObj = GradingLevel.grade_point_to_grade(grade_point_avg, @batch.id)
              if !gradeObj.blank? and !gradeObj.name.blank?
                if @summary_result[gradeObj.name].blank?
                  @summary_result[gradeObj.name] = 0
                end 
                @summary_result[gradeObj.name] = @summary_result[gradeObj.name]+1 
              end  
            else
              @failed_student = @failed_student+1
            end 
          end
        end
      end
    end
  end 

  def finding_data_27

  end  
  
  def sems_finding_data
    if @student_position.blank?
      if @grading_levels.blank?
        @grading_levels = GradingLevel.for_batch(@batch.id)
        if @grading_levels.blank?
          @grading_levels = GradingLevel.default
        end
      end

      @subject_result = {}
      @summary_result = {}
      @total_students = 0
      @failed_student = 0

      @student_list = []
      @student_subject_marks = {}
      @subject_highest = {}
      @student_avg_mark = {}
      @subjects = []
      subject_code = []
      unless @tabulation_data.blank?
        connect_exam = 0
        batch_loop = 0
        @tabulation_data['report'].each do |tab|
          if tab.kind_of?(Array) or tab.blank? or tab['students'].blank?
            next
          end
          batch_subject = Subject.find_all_by_batch_id(@tabulation_data['batches'][batch_loop], :conditions=>"elective_group_id IS NULL and is_deleted=false")
          batch_subject_id = batch_subject.map(&:id)
          batch_loop = batch_loop+1
          connect_exam_id = @tabulation_data['connect_exams'][connect_exam]
          connect_exam = connect_exam+1
          if connect_exam_id.to_i != @connect_exam_obj.id and @class.blank?
            next
          end  
          
          tab['students'].each do |std| 
            subject_count_std = 0
            total_std_subject = StudentsSubject.find_all_by_student_id(std['id'].to_i)
            std_subject_id = total_std_subject.map(&:subject_id)
            
            std_marks_full = 0
            std_marks_core_subject = 0
            total_subject = 0
            u_grade = 0
            number_of_subject = 0
            total_credit = 0
            tab['subjects'].each do |sub|

              unless subject_code.include?(sub['code'])
                subject_code << sub['code']
                @subjects << sub
              end  
            
              has_exam = false
              loop = 0
              tab['exams'].each do |rs|
                loop = loop+1  
                if loop>2
                  break
                end
                if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].blank?
                  has_exam = true
                  break
                end  
              end 
              if has_exam == false
                next
              end  
              if batch_subject_id.include?(sub['id'].to_i) or std_subject_id.include?(sub['id'].to_i)
                
                subject_count_std = subject_count_std+1
                std_subject_marks_final = 0
                i = 0
                tab['exams'].each do |rs|
                  if !rs['result'].blank? and !rs['result'][rs['exam_id']].blank? and !rs['result'][rs['exam_id']][sub['id']].blank? and !rs['result'][rs['exam_id']][sub['id']][std['id']].blank?
                    i = i+1
                    if i<3
                      if i > 1
                        rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'] = rs['result'][rs['exam_id']][sub['id']][std['id']]['marks_obtained'].to_f*2
                      end  
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
                
                if @student_subject_marks[sub['id'].to_i].blank?
                  @student_subject_marks[sub['id'].to_i] = {}
                end
                
                @student_subject_marks[sub['id'].to_i][std['id'].to_i] = subject_full_marks

                number_of_subject = number_of_subject+1
              
                subject_grade = ""
                grade = GradingLevel.percentage_to_grade(subject_full_marks, @batch.id)
                if !grade.blank? and !grade.name.blank?
                  sub['name'].gsub! ' ', '-'
                  if @subject_result[sub['name']].blank?
                    @subject_result[sub['name']] = {}
                  end  
                  if @subject_result[sub['name']][grade.name].blank?
                    @subject_result[sub['name']][grade.name] = 0
                  end 
                  total_credit = total_credit+grade.credit_points.to_f
                  @subject_result[sub['name']][grade.name] = @subject_result[sub['name']][grade.name]+1 
                  if grade.name == "U"
                    u_grade = u_grade+1
                  end
                end   
              end  
            end
            
            #students
            @total_students = @total_students+1

            @grade_name_main = "U"
            if u_grade == 0 && total_credit > 0
              grade_point_avg = total_credit.to_f/number_of_subject.to_f
              gradeObj = GradingLevel.grade_point_to_grade(grade_point_avg, @batch.id)
              if !gradeObj.blank? and !gradeObj.name.blank?
                if @summary_result[gradeObj.name].blank?
                  @summary_result[gradeObj.name] = 0
                end 
                @summary_result[gradeObj.name] = @summary_result[gradeObj.name]+1 
              end  
            else
              @failed_student = @failed_student+1
            end 
          end
        end
      end
    end
  end 
  
  
end
