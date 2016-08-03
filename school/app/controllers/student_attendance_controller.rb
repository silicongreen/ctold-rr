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
  before_filter :only_assigned_employee_allowed
  before_filter :protect_other_student_data
  filter_access_to :all
  before_filter :check_status
 
  def index
  end

  def student
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
          @academic_days=@batch.academic_days.select{|v| v<=@end_date}.count
          leaves_forenoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:student_id=>@student.id,:forenoon=>true,:afternoon=>false,:month_date => @start_date..@end_date})
          leaves_afternoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:student_id=>@student.id,:forenoon=>false,:afternoon=>true,:month_date => @start_date..@end_date})
          leaves_full=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:student_id=>@student.id,:forenoon=>true,:afternoon=>true,:month_date => @start_date..@end_date})
          @leaves=leaves_full.to_f+(0.5*(leaves_forenoon.to_f+leaves_afternoon.to_f))
          @attendance = (@academic_days - @leaves)
          @percent = (@attendance.to_f/@academic_days)*100 unless @academic_days == 0
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
          leaves_forenoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:student_id=>@student.id,:forenoon=>true,:afternoon=>false,:month_date => @start_date..@end_date})
          leaves_afternoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:student_id=>@student.id,:forenoon=>false,:afternoon=>true,:month_date => @start_date..@end_date})
          leaves_full=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:student_id=>@student.id,:forenoon=>true,:afternoon=>true,:month_date => @start_date..@end_date})
          @leaves=leaves_full.to_f+(0.5*(leaves_forenoon.to_f+leaves_afternoon.to_f))
          @attendance = (@academic_days - @leaves)
          @percent = (@attendance.to_f/@academic_days)*100 unless @academic_days == 0
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
    @end_date =  @batch.end_date.to_date
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
      @leaves=leaves_full.to_f+(0.5*(leaves_forenoon.to_f+leaves_afternoon.to_f))
      @attendance = (@academic_days - @leaves)
      @percent = (@attendance.to_f/@academic_days)*100 unless @academic_days == 0
    end

  end
  
  def leaves
     @student = Student.find(params[:id])
     #@employee = Employee.find(:all)
     
     if @student.class_teacher_id == 0
      @general_subjects = Subject.find_all_by_batch_id(@student.batch_id, :conditions=>"elective_group_id IS NULL and is_deleted=false")
      puts @student.batch_id
      #puts @employee.length
      puts @general_subjects.length

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
    puts @total_leave_count;
     
    if request.post?
      applied_dates = (@leave_apply.start_date..@leave_apply.end_date).to_a.uniq
      detect_overlaps = ApplyLeaveStudent.find(:all, :conditions => ["student_id = ? AND (start_date IN (?) OR end_date IN (?))",@student.id, applied_dates, applied_dates])
      if detect_overlaps.present? and detect_overlaps.map(&:approved).include? true
        @leave_apply.errors.add_to_base("#{t('range_conflict')}") and return
      end
      leaves = ApplyLeaveStudent.count(:all,:conditions=>{:approved => true, :student_id=>params[:leave_apply][:student_id],:start_date=>params[:leave_apply][:start_date],:end_date=>params[:leave_apply][:end_date]})
      puts leaves
      already_apply = ApplyLeaveStudent.count(:all,:conditions=>{:approved => nil, :student_id=>params[:leave_apply][:student_id],:start_date=>params[:leave_apply][:start_date],:end_date=>params[:leave_apply][:end_date]})
      if(leaves == 0 and already_apply == 0) or (leaves <= 1 )
          if @leave_apply.save
            ApplyLeaveStudent.update(@leave_apply, :approved=> nil, :viewed_by_teacher=> false, :approving_teacher => @reporting_teacher.id)
            flash[:notice]=t('flash5')
            redirect_to :controller => "student_attendance", :action=> "leaves", :id=>@student.id
          end
      else
        @leave_apply.errors.add_to_base("#{t('already_applied')}")
      end
    end
  end
  
  def leave_history
    @student = Student.find(params[:id])
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
end