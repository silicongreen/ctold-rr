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

class StudentAttendanceController < ApplicationController
  before_filter :login_required
  #before_filter :check_permission, :only=>[:index, :leaves]
  before_filter :only_assigned_employee_allowed
  before_filter :protect_other_student_data, :except=>[:own_leave_application,:download_attachment,:year_report,:graph_code,:month_report,:subject_report,:subject_report_pdf,:new_calendar, :cancel_application, :month_report_data]
  before_filter :protect_applied_leave_parent, :only => [:own_leave_application, :cancel_application]
  before_filter :default_time_zone_present_time
  filter_access_to :all
  before_filter :check_status
 
  def download_attachment
    if params[:target] == "student"
      @leave =  ApplyLeaveStudent.find params[:id]
    else
      @leave =  ApplyLeave.find params[:id]
    end  
    unless @leave.nil?
      filename = @leave.attachment_file_name
      send_file  @leave.attachment.path, :type=>@leave.attachment.content_type,:filename => filename
    else
      flash[:notice]="No File Found For Download"
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end
  def index
    permitted_modules = Rails.cache.fetch("permitted_modules_student_attendance_#{current_user.id}"){
      @student_attendance_modules_tmp = []
      @a_user_modules = ['attendance']
      menu_links = MenuLink.find_by_name(@a_user_modules)
      menu_id = menu_links.id
      menu_links = MenuLink.find_all_by_higher_link_id(menu_id)
      
      menu_links.each do |menu_link|
        if menu_link.link_type=="user_menu"
          menu_id = menu_link.id

          school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")

          if school_menu_links.nil? or school_menu_links.blank?
            @student_attendance_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => false}
          else
            @student_attendance_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => true}
          end
        end
      end
      @student_attendance_modules_tmp
    }
    @student_attendance_modules = permitted_modules
  end
  
  def month_report
    if params[:new_month].nil?
      @show_month = @local_tzone_time.to_date
    else
      passed_date = (params[:passed_date]).to_date
      if params[:new_month].to_i > passed_date.month
        @show_month  = passed_date+1.month
      else
        @show_month = passed_date-1.month
      end
    end
    @start_date = @show_month.beginning_of_month
    @start_date_day = @start_date.wday
    @last_day = @show_month.end_of_month
    first_day = @show_month.beginning_of_month
    last_day =  @show_month.end_of_month
    
    get_month_report(first_day,last_day)
    @attendence = []
    if @attendence_data['status']['code'].to_i == 200
      @attendence = @attendence_data['data']
    end
  end
  
  def month_report_data
    
    @show_month = @local_tzone_time.to_date
    @start_date = @show_month.beginning_of_month
    @start_date_day = @start_date.wday
    @last_day = @show_month.end_of_month
    first_day = @show_month.beginning_of_month
    last_day =  @show_month.end_of_month
    
    get_month_report(first_day,last_day)
    @attendence = []
    if @attendence_data['status']['code'].to_i == 200
      @attendence = @attendence_data['data']
    end
    
    render :partial => 'monthreport'
  end
  
  def new_calendar
    d = params[:new_month].to_i
    passed_date = (params[:passed_date]).to_date
    if params[:new_month].to_i > passed_date.month
      @show_month  = passed_date+1.month
    else
      @show_month = passed_date-1.month
    end
    @start_date = @show_month.beginning_of_month
    @start_date_day = @start_date.wday
    @last_day = @show_month.end_of_month
    first_day = @show_month.beginning_of_month
    last_day =  @show_month.end_of_month
    get_month_report(first_day,last_day)
    @attendence = []
    if @attendence_data['status']['code'].to_i == 200
      @attendence = @attendence_data['data']
    end
    render :update do |page|
      page.replace_html 'monthreport', :partial => 'monthreport'
    end
  end

  def student
    if current_user.parent? or current_user.student?
      redirect_to :controller => "student_attendance", :action => 'month_report'
    end
    @config = Configuration.find_by_config_key('StudentAttendanceType')
    @student = Student.find(params[:id])
    @batch = Batch.find(@student.batch_id)
    @subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>'is_deleted = false')
    @electives = @subjects.map{|x|x unless x.elective_group_id.nil?}.compact
    @electives.reject! { |z| z.students.include?(@student)  }
    @subjects -= @electives

    if request.post?
      @detail_report = []
      if params[:advance_search][:mode]== 'Overall'
        @start_date = @batch.start_date.to_date
        @end_date = Date.today
        unless @config.config_value == 'Daily'
          unless params[:advance_search][:subject_id].empty?
            @academic_days=@batch.subject_hours(@start_date, @end_date, params[:advance_search][:subject_id]).values.flatten.compact.count
            @subject=Subject.find(params[:advance_search][:subject_id])
            @student_leaves = SubjectLeave.find(:all,:conditions =>{:subject_id=>@subject.id,:student_id=>@student.id,:month_date => @start_date..@end_date})
          else
            @subjects = @batch.subjects
            @academic_days=@batch.subject_hours(@start_date, @end_date, 0).values.flatten.compact.count
            @student_leaves = SubjectLeave.find(:all,  :conditions =>{:student_id=>@student.id,:month_date => @start_date..@end_date, :subject_id => @subjects.map(&:id)})
          end
          @leaves= @student_leaves.count
          @leaves||=0
          @attendance = (@academic_days - @leaves)
          @percent = (@attendance.to_f/@academic_days)*100 unless @academic_days == 0
        else
          @student_leaves = Attendance.find(:all,  :conditions =>{:batch_id=>@batch.id,:student_id=>@student.id,:month_date => @start_date..@end_date})
          on_leaves = 0;
          leaves_other = 0;
          leaves_full = 0;
          @student_leaves_real = []
          unless @student_leaves.empty?
            @student_leaves.each do |r|
              working_days_count=@batch.find_working_days(r.month_date.to_date,r.month_date.to_date).select{|v| v<=r.month_date.to_date}.count
              
              if working_days_count==1
                @student_leaves_real << r
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
          
          @student_leaves = @student_leaves_real
          @academic_days=@batch.academic_days.select{|v| v<=@end_date}.count
          #          leaves_forenoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:student_id=>@student.id,:forenoon=>true,:afternoon=>false,:month_date => @start_date..@end_date})
          #          leaves_afternoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:student_id=>@student.id,:forenoon=>false,:afternoon=>true,:month_date => @start_date..@end_date})
          #          leaves_full=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:student_id=>@student.id,:forenoon=>true,:afternoon=>true,:month_date => @start_date..@end_date})
          #          
          #          on_leaves_forenoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>1,:student_id=>@student.id,:forenoon=>true,:afternoon=>false,:month_date => @start_date..@end_date})
          #          on_leaves_afternoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>1,:student_id=>@student.id,:forenoon=>false,:afternoon=>true,:month_date => @start_date..@end_date})
          #          on_leaves_full=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>1,:student_id=>@student.id,:forenoon=>true,:afternoon=>true,:month_date => @start_date..@end_date})
          
          @onleaves = on_leaves.to_f
          @leaves=leaves_full.to_f+(0.5*leaves_other)
          @attendance = (@academic_days - @leaves - @onleaves)
          @attendance_without_leave = @attendance+@onleaves
          @percent = (@attendance_without_leave.to_f/@academic_days)*100 unless @academic_days == 0
        end
      elsif params[:advance_search][:mode]== 'Monthly'
        @month = params[:advance_search][:month]
        @year = params[:advance_search][:year]
        unless(@month.present? and @year.present?)
          render :update do |page|
            page.replace_html 'error-container', :text => "<div id='errorExplanation' class='errorExplanation'><p>#{t('please_select_month_and_year')}.</p></div>"
          end
          return
        end
        @start_date = "01-#{@month}-#{@year}".to_date
        #        @start_date = @date
        @today = Date.today
        @end_date = @start_date.end_of_month
        if @end_date > Date.today
          @end_date = Date.today
        end
        unless @config.config_value == 'Daily'
          unless params[:advance_search][:subject_id].empty?
            @academic_days=@batch.subject_hours(@start_date, @end_date, params[:advance_search][:subject_id]).values.flatten.compact.count
            @subject=Subject.find(params[:advance_search][:subject_id])
            @student_leaves = SubjectLeave.find(:all,:conditions =>{:subject_id=>@subject.id,:student_id=>@student.id,:month_date => @start_date..@end_date})
          else
            @subjects = @batch.subjects
            @academic_days=@batch.subject_hours(@start_date, @end_date, 0).values.flatten.compact.count
            @student_leaves = SubjectLeave.find(:all,  :conditions =>{:student_id=>@student.id,:month_date => @start_date..@end_date, :subject_id => @subjects.map(&:id)})
          end
          @leaves= @student_leaves.count
          @leaves||=0
          @attendance = (@academic_days - @leaves)
          @percent = (@attendance.to_f/@academic_days)*100 unless @academic_days == 0
        else
          @student_leaves = Attendance.find(:all,  :conditions =>{:batch_id=>@batch.id,:student_id=>@student.id,:month_date => @start_date..@end_date})
          
          
          @academic_days=@batch.working_days(@start_date.to_date).select{|v| v<=@end_date}.count
          on_leaves = 0;
          leaves_other = 0;
          leaves_full = 0;
          @student_leaves_real = []
          unless @student_leaves.empty?
            @student_leaves.each do |r|
              working_days_count=@batch.find_working_days(r.month_date.to_date,r.month_date.to_date).select{|v| v<=r.month_date.to_date}.count
              
              if working_days_count==1
                @student_leaves_real << r
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
          
          @student_leaves = @student_leaves_real
          
          #          leaves_forenoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:student_id=>@student.id,:forenoon=>true,:afternoon=>false,:month_date => @start_date..@end_date})
          #          leaves_afternoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:student_id=>@student.id,:forenoon=>false,:afternoon=>true,:month_date => @start_date..@end_date})
          #          leaves_full=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:student_id=>@student.id,:forenoon=>true,:afternoon=>true,:month_date => @start_date..@end_date})
          #          
          #          on_leaves_forenoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>1,:student_id=>@student.id,:forenoon=>true,:afternoon=>false,:month_date => @start_date..@end_date})
          #          on_leaves_afternoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>1,:student_id=>@student.id,:forenoon=>false,:afternoon=>true,:month_date => @start_date..@end_date})
          #          on_leaves_full=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>1,:student_id=>@student.id,:forenoon=>true,:afternoon=>true,:month_date => @start_date..@end_date})
          
          @onleaves = on_leaves.to_f
          @leaves=leaves_full.to_f+(0.5*leaves_other)
          @attendance = (@academic_days - @leaves - @onleaves)
          @attendance_without_leave = @attendance+@onleaves
          @percent = (@attendance_without_leave.to_f/@academic_days)*100 unless @academic_days == 0
        end
      else
        render :update do |page|
          page.replace_html 'error-container', :text => "<div id='errorExplanation' class='errorExplanation'><p>#{t('please_select_mode')}.</p></div>"
        end
        return
      end
      
      render :update do |page|
        page.replace_html 'report', :partial => 'report'
        page.replace_html 'error-container', :text => ''
      end
    end
    
  end

  def month
    if params[:mode] == 'Monthly'
      @year = Date.today.year
      render :update do |page|
        page.replace_html 'month', :partial => 'month'
        page.replace_html 'error-container', :text => ''
      end
    else
      render :update do |page|
        page.replace_html 'month', :text =>''
        page.replace_html 'error-container', :text => ''
      end
    end
  end

  def student_report
    @config = Configuration.find_by_config_key('StudentAttendanceType')
    @student = Student.find(params[:id])
    @batch = Batch.find(params[:year])
    @start_date = @batch.start_date.to_date
    @end_date = Date.today
    unless @config.config_value == 'Daily'
      @academic_days=@batch.subject_hours(@start_date, @end_date, 0).values.flatten.compact.count
      @subjects = @batch.subjects
      @student_leaves = SubjectLeave.find(:all,  :conditions =>{:batch_id=>@batch.id,:student_id=>@student.id,:month_date => @start_date..@end_date, :subject_id => @subjects.map(&:id)})
      @leaves= @student_leaves.count
      @leaves||=0
      @attendance = (@academic_days - @leaves)
      @percent = (@attendance.to_f/@academic_days)*100 unless @academic_days == 0
    else
      @student_leaves = Attendance.find(:all,  :conditions =>{:student_id=>@student.id,:batch_id => @batch.id, :month_date => @start_date..@end_date})
      @academic_days=@batch.academic_days.select{|v| v<=@end_date}.count
      leaves_forenoon=Attendance.count(:all,:conditions=>{:student_id=>@student.id, :batch_id => @batch.id, :forenoon=>true,:afternoon=>false,:month_date => @start_date..@end_date})
      leaves_afternoon=Attendance.count(:all,:conditions=>{:student_id=>@student.id,:batch_id => @batch.id, :forenoon=>false,:afternoon=>true,:month_date => @start_date..@end_date})
      leaves_full=Attendance.count(:all,:conditions=>{:student_id=>@student.id,:batch_id => @batch.id, :forenoon=>true,:afternoon=>true,:month_date => @start_date..@end_date})
      
      on_leaves_forenoon=Attendance.count(:all,:conditions=>{:student_id=>@student.id,:is_leave=>1, :batch_id => @batch.id, :forenoon=>true,:afternoon=>false,:month_date => @start_date..@end_date})
      on_leaves_afternoon=Attendance.count(:all,:conditions=>{:student_id=>@student.id,:is_leave=>1,:batch_id => @batch.id, :forenoon=>false,:afternoon=>true,:month_date => @start_date..@end_date})
      on_leaves_full=Attendance.count(:all,:conditions=>{:student_id=>@student.id,:is_leave=>1,:batch_id => @batch.id, :forenoon=>true,:afternoon=>true,:month_date => @start_date..@end_date})
      
      
      
      @onleaves = on_leaves_full.to_f+(0.5*(on_leaves_forenoon.to_f+on_leaves_afternoon.to_f))
      @leaves=leaves_full.to_f+(0.5*(leaves_forenoon.to_f+leaves_afternoon.to_f))
      @attendance = (@academic_days - @leaves)
      @attendance_without_leave = @attendance+@onleaves
      @percent = (@attendance_without_leave.to_f/@academic_days)*100 unless @academic_days == 0
    end

  end
  
  def leaves
    @student = Student.find(params[:id])
    #@employee = Employee.find(:all)
    
    reminder_recipient_ids = []
    @all_employees = Employee.find(:all)
    reminder_recipient_ids = @all_employees.collect(&:user_id).compact
      
    if @student.class_teacher_id == 0
      @general_subjects = Subject.find_all_by_batch_id(@student.batch_id, :conditions=>"elective_group_id IS NULL and is_deleted=false")
      @reporting_manager_id = []
      reporting_managers = []
      employee_user_ids = []
      @general_subjects.each do |s|
        @assigned_employee = EmployeesSubject.find_all_by_subject_id(s.id)
        if @assigned_employee.present?
          @assigned_employee.each do |ae|
            @employee = Employee.find(ae.employee_id)
            reporting_managers.push(@employee.reporting_manager_id)
            puts @employee.reporting_manager_id.to_s + "  "  + @employee.id.to_s
            employee_user_ids.push(@employee.id)
          end
        end
      end

      found_nil = false
      i = 0
      index = nil
      reporting_managers.each do |rm|
        i = i + 1
        if rm == nil and ! found_nil
          found_nil = true
          index = i
        end
      end

      if found_nil
        @reporting_teacher = Employee.find(employee_user_ids[index])
      else
        @admin_for_school = MultiSchool.current_school.code + "-admin"
        @user_reporting = User.find_by_username(@admin_for_school)
        @reporting_teacher = Employee.find_by_user_id(@user_reporting.id)
      end
      @student.update_attributes(:class_teacher_id=>@reporting_teacher.id)
    else
      @reporting_teacher = Employee.find(@student.class_teacher_id)
    end  
    @total_leave_count = 0
     
    @app_leaves = ApplyLeaveStudent.count(:conditions=>["student_id =? AND viewed_by_teacher =?", @reporting_teacher.id, false])
    @total_leave_count = @total_leave_count + @app_leaves
    
    @leave_apply = ApplyLeaveStudent.new(params[:leave_apply])
     
    if request.post?
      applied_dates = (@leave_apply.start_date..@leave_apply.end_date).to_a.uniq
      detect_overlaps = ApplyLeaveStudent.find(:all, :conditions => ["student_id = ? AND (start_date IN (?) OR end_date IN (?))",@student.id, applied_dates, applied_dates])
      if detect_overlaps.present? and detect_overlaps.map(&:approved).include? true
        @leave_apply.errors.add_to_base("#{t('range_conflict')}") and return
      end
      leaves = ApplyLeaveStudent.count(:all,:conditions=>{:approved => true, :student_id=>params[:leave_apply][:student_id],:start_date=>params[:leave_apply][:start_date],:end_date=>params[:leave_apply][:end_date]})
      already_apply = ApplyLeaveStudent.count(:all,:conditions=>{:approved => nil, :student_id=>params[:leave_apply][:student_id],:start_date=>params[:leave_apply][:start_date],:end_date=>params[:leave_apply][:end_date]})
      if(leaves == 0 and already_apply == 0) or (leaves <= 1 )
        if @leave_apply.save
            
          @config = Configuration.find_by_config_key('LeaveSectionManager')
          if (@config.blank? or @config.config_value.blank? or @config.config_value.to_i != 1)
              unless reminder_recipient_ids.nil?
              Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => @student.user_id,
                  :recipient_ids => reminder_recipient_ids,
                  :subject=>"#{t('student_leave_notice')}",
                  :rtype=>9,
                  :rid=>@leave_apply.id,
                  :body=>""+@student.first_name+" apply for leave from "+params[:leave_apply][:start_date]+" to "+params[:leave_apply][:end_date] ))
            end 
          else
            ApplyLeaveStudent.update(@leave_apply, :approved=> nil, :viewed_by_teacher=> false, :approving_teacher => @reporting_teacher.id)
            batch = @leave_apply.student.batch

            unless batch.blank?
              batch_tutor = batch.employees
              available_user_ids = []
              unless batch_tutor.blank?
                batch_tutor.each do |employee|
                  if employee.meeting_forwarder == 1
                    available_user_ids << employee.user_id
                  end
                end
              end
            end

            unless available_user_ids.nil?
              Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => @student.user_id,
                  :recipient_ids => available_user_ids,
                  :subject=>"#{t('student_leave_notice')}",
                  :rtype=>9,
                  :rid=>@leave_apply.id,
                  :body=>""+@student.first_name+" apply for leave from "+params[:leave_apply][:start_date]+" to "+params[:leave_apply][:end_date] ))
            end 
          end
          
          flash[:notice] = "Leave Applied Successfully"
          redirect_to :controller => "student_attendance", :action=> "leaves", :id=>@student.id
        end
      else
        @leave_apply.errors.add_to_base("#{t('already_applied')}")
      end
    end
  end
  
  def individual_leave_applications
    @student = Student.find(params[:id])
    @pending_applied_leaves = ApplyLeaveStudent.find_all_by_student_id(@student.id, :conditions=> "approved = false AND viewed_by_teacher = false", :order=>"start_date DESC")
    @applied_leaves = ApplyLeaveStudent.paginate(:page => params[:page],:per_page=>10 , :conditions=>[ "student_id = '#{@student.id}'"], :order=>"start_date DESC")
    render :partial => "individual_leave_applications"
  end

  def own_leave_application
    @applied_leave = ApplyLeaveStudent.find(params[:id])
    @student = Student.find(@applied_leave.student_id)
  end
  
  def cancel_application
    @applied_leave = ApplyLeaveStudent.find(params[:id])
    @students = Student.find(@applied_leave.student_id)
    unless @applied_leave.viewed_by_teacher
      ApplyLeaveStudent.destroy(params[:id])
      flash[:notice] = t('student_cancle_leave_done')
    else
      flash[:notice] = t('student_cancle_leave_viewed')
    end
    redirect_to :action=>"leaves", :id=>@students.id
  end
  
  
  def leave_history
    @student = Student.find(params[:id])
    render :partial => 'leave_history'
  end
  
  def update_leave_history
    @student = Student.find(params[:id])
    @start_date = (params[:period][:start_date])
    @end_date = (params[:period][:end_date])
    
    @student_attendances = Attendance.find_all_by_student_id(@student.id,:conditions=> "is_leave = 1 AND month_date between '#{@start_date.to_date}' and '#{@end_date.to_date}'")
    
    render :update do |page|
      page.replace_html "attendance-report", :partial => 'update_leave_history'
    end
  end
  
  def subject_report
    get_subject_report
    if @attendence_data['status']['code'].to_i == 200
      @report = @attendence_data['data']['report']
    end
    render :partial => 'subject_report'
  end
  def subject_report_pdf
    get_subject_report
    if @attendence_data['status']['code'].to_i == 200
      @report = @attendence_data['data']['report']
    end
    render :pdf => 'subject_report_pdf',
      :margin => {:top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def year_report
    get_year_report
    if @attendence_data['status']['code'].to_i == 200
      
      @absent = @attendence_data['data']['absent']
      @late = @attendence_data['data']['late']
      @leave = @attendence_data['data']['leave']
      @present = @attendence_data['data']['total']-@absent-@leave
      @graph = open_flash_chart_object('100%',600,"/student_attendance/graph_code?absent=#{@absent}&late=#{@late}&leave=#{@leave}&present=#{@present}")
    end
    render :partial => 'year_report'
  end
  
  def graph_code
    
      title = Title.new("")
      
      absent = params[:absent].to_i
      late = params[:late].to_i*0.5
      
      leave = params[:leave].to_i
      
      present = params[:present].to_i
      present = present-late
      
      main_late =late*2
      
      values_main = [PieValue.new(absent,"Absent"),PieValue.new(late,"Late"), PieValue.new(leave,"Leave"),PieValue.new(present,"Present")]
      
      colours_main = ["#EC1300", "#FFB800", "#922CB0", "#8DC63F"]
      
      if present ==0
        colours_main.delete_at(3)
        values_main.delete_at(3)
      end
      if leave ==0
        colours_main.delete_at(2)
        values_main.delete_at(2)
      end
      if late ==0
        colours_main.delete_at(1)
        values_main.delete_at(1)
      end
      if absent ==0
        colours_main.delete_at(0)
        values_main.delete_at(0)
      end
      
     
      pie = Pie.new
      pie.start_angle = 35
      pie.animate = true
      pie.tooltip = '#percent# of 100%'
      pie.colours = colours_main
      pie.values  = values_main

      
      chart = OpenFlashChart.new
      chart.title = title
      chart.add_element(pie)

      chart.x_axis = nil

      render :text => chart.render 
    
  end
  
  private
  
  def get_subject_report()
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    
    if current_user.student
      homework_uri = URI(api_endpoint + "api/attendance/reportallstd")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @attendence_data = JSON::parse(homework_res.body)
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      homework_uri = URI(api_endpoint + "api/attendance/reportallstd")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"batch_id"=>student.batch_id,"student_id"=>student.id,"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @attendence_data = JSON::parse(homework_res.body)
    end
    
    @attendence_data
  end
  
  def get_year_report()
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    
    if current_user.student
      homework_uri = URI(api_endpoint + "api/calender/getattendence")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @attendence_data = JSON::parse(homework_res.body)
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      homework_uri = URI(api_endpoint + "api/calender/getattendence")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"school"=>student.school_id,"batch_id"=>student.batch_id,"student_id"=>student.id,"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @attendence_data = JSON::parse(homework_res.body)
    end
    
    @attendence_data
  end
  
  
  def get_month_report(first_day,last_day)
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    uri = URI(api_endpoint + "api/calender/getattendence")
    http = Net::HTTP.new(uri.host, uri.port)
    request = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    
    form_data = {}
    form_data['user_secret'] = session[:api_info][0]['user_secret']
    form_data['start_date'] = first_day.to_date
    form_data['end_date'] = last_day.to_date
    
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      
      form_data['school'] = student.school_id
      form_data['batch_id'] = student.batch_id
      form_data['student_id'] = student.id
    end
    
    request.set_form_data(form_data)
    
    response = http.request(request)
    @attendence_data = JSON::parse(response.body)
    
  end
end