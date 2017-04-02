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

class SmsController < ApplicationController
  before_filter :login_required
  filter_access_to :all
  
  def index
    @sms_setting = SmsSetting.new()
    @parents_sms_enabled = SmsSetting.find_by_settings_key("ParentSmsEnabled")
    @students_sms_enabled = SmsSetting.find_by_settings_key("StudentSmsEnabled")
    @employees_sms_enabled = SmsSetting.find_by_settings_key("EmployeeSmsEnabled")
  end

  def settings
    @application_sms_enabled = SmsSetting.find_by_settings_key("ApplicationEnabled")
    @student_admission_sms_enabled = SmsSetting.find_by_settings_key("StudentAdmissionEnabled")
    @exam_schedule_result_sms_enabled = SmsSetting.find_by_settings_key("ExamScheduleResultEnabled")
    @student_attendance_sms_enabled = SmsSetting.find_by_settings_key("AttendanceEnabled")
    @news_events_sms_enabled = SmsSetting.find_by_settings_key("NewsEventsEnabled")
    @parents_sms_enabled = SmsSetting.find_by_settings_key("ParentSmsEnabled")
    @students_sms_enabled = SmsSetting.find_by_settings_key("StudentSmsEnabled")
    @employees_sms_enabled = SmsSetting.find_by_settings_key("EmployeeSmsEnabled")
    if request.post?
      SmsSetting.update(@application_sms_enabled.id,:is_enabled=>params[:sms_settings][:application_enabled])
      redirect_to :action=>"settings"
    end
  end

  def update_general_sms_settings
    @student_admission_sms_enabled = SmsSetting.find_by_settings_key("StudentAdmissionEnabled")
    @exam_schedule_result_sms_enabled = SmsSetting.find_by_settings_key("ExamScheduleResultEnabled")
    @student_attendance_sms_enabled = SmsSetting.find_by_settings_key("AttendanceEnabled")
    @news_events_sms_enabled = SmsSetting.find_by_settings_key("NewsEventsEnabled")
    @parents_sms_enabled = SmsSetting.find_by_settings_key("ParentSmsEnabled")
    @students_sms_enabled = SmsSetting.find_by_settings_key("StudentSmsEnabled")
    @employees_sms_enabled = SmsSetting.find_by_settings_key("EmployeeSmsEnabled")
    SmsSetting.update(@student_admission_sms_enabled.id,:is_enabled=>params[:general_settings][:student_admission_enabled])
    SmsSetting.update(@exam_schedule_result_sms_enabled.id,:is_enabled=>params[:general_settings][:exam_schedule_result_enabled])
    SmsSetting.update(@student_attendance_sms_enabled.id,:is_enabled=>params[:general_settings][:student_attendance_enabled])
    SmsSetting.update(@news_events_sms_enabled.id,:is_enabled=>params[:general_settings][:news_events_enabled])
    SmsSetting.update(@parents_sms_enabled.id,:is_enabled=>params[:general_settings][:sms_parents_enabled])
    SmsSetting.update(@students_sms_enabled.id,:is_enabled=>params[:general_settings][:sms_students_enabled])
    SmsSetting.update(@employees_sms_enabled.id,:is_enabled=>params[:general_settings][:sms_employees_enabled])
    redirect_to :action=>"settings"
  end

  def students
    @batches=Batch.active.all(:include=>:course)
    if request.post?
      error=false
      unless params[:send_sms][:student_ids].nil?
        student_ids = params[:send_sms][:student_ids]
        sms_setting = SmsSetting.new()
        @recipients=[]
        if MultiSchool.current_school.id == 319
          @recipients=['8801918179040','8801711924683','8801678401308','8801815709133','8801764198796','8801680425262','8801941013013','8801911438293','8801771767811','8801716752996','8801715437299','8801763710825','8801714453713','8801714552559','8801715331407','8801715224886']
        end
        send_to = params[:send_sms][:send_to]
        student_ids.each do |s_id|
          student = Student.find(s_id)
          guardian = student.immediate_contact
          if student.is_sms_enabled
            
            if sms_setting.student_sms_active and (send_to.to_i == 1 or send_to.to_i == 2 or send_to.to_i == 5)          
              @recipients.push student.phone2 unless (student.phone2.nil? or student.phone2 == "")
            end
            
            if sms_setting.parent_sms_active and (send_to.to_i == 1 or send_to.to_i == 3)
              unless guardian.nil?
                @recipients.push guardian.mobile_phone unless (guardian.mobile_phone.nil? or guardian.mobile_phone == "")
                
              end
            end
            
            if sms_setting.parent_sms_active and (send_to.to_i == 4 or send_to.to_i == 5)
              guardians = student.student_guardian
              guardians.each do |sguardian|
                @recipients.push sguardian.mobile_phone unless (sguardian.mobile_phone.nil? or sguardian.mobile_phone == "")
              end 
            end
            
          end
        end
        unless @recipients.empty?
          message = params[:send_sms][:message]
          sms = Delayed::Job.enqueue(SmsManager.new(message,@recipients))
          # raise @recipients.inspect
          render(:update) do |page|
            page.replace_html 'status-message',:text=>"<p class=\"flash-msg\">#{t('sms_sending_intiated', :log_url => url_for(:controller => "sms", :action => "show_sms_messages"))}</p>"
            page.visual_effect(:highlight, 'status-message')
            page.replace_html 'student-list',:text=>""
          end
        else
          error=true
        end
      else
        error=true
      end
      if error
        render(:update) do |page|
          page.replace_html 'status-message',:text=>"<p class=\"flash-msg\">#{t('select_valid_students')}</p>"
        end
      end
    end
  end
  
  def list_students
    @students = []
    unless params[:batch_id].blank?
      batch_ids = params[:batch_id].split(",")
      @students = Student.find_all_by_batch_id(batch_ids,:conditions=>"is_sms_enabled=true")
    end
  end

  def batches
    @batches = Batch.active
    if request.post?
      unless params[:send_sms][:batch_ids].nil?
        batch_ids = params[:send_sms][:batch_ids]
        sms_setting = SmsSetting.new()
        send_to = params[:send_sms][:send_to]
        @recipients = []
        if MultiSchool.current_school.id == 319
          @recipients=['8801918179040','8801711924683','8801678401308','8801815709133','8801764198796','8801680425262','8801941013013','8801911438293','8801771767811','8801716752996','8801715437299','8801763710825','8801714453713','8801714552559','8801715331407','8801715224886']
        end
        batch_ids.each do |b_id|
          batch = Batch.find(b_id)
          batch_students = batch.students
          batch_students.each do |student|
            if student.is_sms_enabled
              if sms_setting.student_sms_active and (send_to.to_i == 1 or send_to.to_i == 2) 
                @recipients.push student.phone2 unless (student.phone2.nil? or student.phone2 == "")
              end
              if sms_setting.parent_sms_active and (send_to.to_i == 1 or send_to.to_i == 3) 
                guardian = student.immediate_contact
                unless guardian.nil?
                  @recipients.push guardian.mobile_phone unless (guardian.mobile_phone.nil? or guardian.mobile_phone == "")
                end
              end
            end
          end
        end
        unless @recipients.empty?
          message = params[:send_sms][:message]
          sms = Delayed::Job.enqueue(SmsManager.new(message,@recipients))
          render(:update) do |page|
            page.replace_html 'list',:text=>""
            page.replace_html 'status-message',:text=>"<p class=\"flash-msg\">#{t('sms_sending_intiated', :log_url => url_for(:controller => "sms", :action => "show_sms_messages"))}</p>"
            page.visual_effect(:highlight, 'status-message')
          end
        else
          error = true
        end
      else
        error = true
      end
      if error
        render(:update) do |page|
          page.replace_html 'status-message',:text=>"<p class=\"flash-msg\">#{t('select_valid_batches')}</p>"
        end
      end
    end
  end

  def sms_all
    batches=Batch.active.all({:include=>{:students=>:immediate_contact}})
    sms_setting = SmsSetting.new()
    student_sms=sms_setting.student_sms_active
    parent_sms=sms_setting.parent_sms_active
    employee_sms=sms_setting.employee_sms_active
    @recipients = []
    if MultiSchool.current_school.id == 319
        @recipients=['8801918179040','8801711924683','8801678401308','8801815709133','8801764198796','8801680425262','8801941013013','8801911438293','8801771767811','8801716752996','8801715437299','8801763710825','8801714453713','8801714552559','8801715331407','8801715224886']
    end
    batches.each do |batch|
      batch_students = batch.students
      batch_students.each do |student|
        if student.is_sms_enabled
          if student_sms
            @recipients.push student.phone2 unless (student.phone2.nil? or student.phone2 == "")
          end
          if parent_sms
            guardian = student.immediate_contact
            unless guardian.nil?
              @recipients.push guardian.mobile_phone unless (guardian.mobile_phone.nil? or guardian.mobile_phone == "")
            end
          end
        end
      end
    end
    emp_departments = EmployeeDepartment.active.find(:all,:include=>:employees)
    emp_departments.each do |dept|
      dept_employees = dept.employees
      dept_employees.each do |employee|
        if employee_sms
          @recipients.push employee.mobile_phone unless (employee.mobile_phone.nil? or employee.mobile_phone == "")
        end
      end
    end
    unless @recipients.empty?
      message = params[:send_sms][:message]
      Delayed::Job.enqueue(SmsManager.new(message,@recipients))
    end

  end

  def employees
    if request.post?
      unless params[:send_sms][:employee_ids].nil?
        employee_ids = params[:send_sms][:employee_ids]
        sms_setting = SmsSetting.new()
        @recipients=[]
        if MultiSchool.current_school.id == 319
          @recipients=['8801918179040','8801711924683','8801678401308','8801815709133','8801764198796','8801680425262','8801941013013','8801911438293','8801771767811','8801716752996','8801715437299','8801763710825','8801714453713','8801714552559','8801715331407','8801715224886']
        end
        employee_ids.each do |e_id|
          employee = Employee.find(e_id)
          if sms_setting.employee_sms_active
            @recipients.push employee.mobile_phone unless (employee.mobile_phone.nil? or employee.mobile_phone == "")
          end
        end
        unless @recipients.empty?
          message = params[:send_sms][:message]
          Delayed::Job.enqueue(SmsManager.new(message,@recipients))
          render(:update) do |page|
            page.replace_html 'employee-list',:text=>""
            page.replace_html 'status-message',:text=>"<p class=\"flash-msg\">#{t('sms_sending_intiated', :log_url => url_for(:controller => "sms", :action => "show_sms_messages"))}</p>"
            page.visual_effect(:highlight, 'status-message')
          end
        else
          error = true
        end
      else
        error = true
      end
      if error
        render(:update) do |page|
          page.replace_html 'status-message',:text=>"<p class=\"flash-msg\">#{t('select_valid_employees')}</p>"
        end
      end
    end
  end

  def list_employees
    @employees = []
    unless params[:dept_id].blank?
      dept_ids = params[:dept_id].split(",")
      @employees = Employee.find_all_by_employee_department_id(dept_ids)
    end
  end

  def departments
    @departments = EmployeeDepartment.find(:all)
    if request.post?
      unless params[:send_sms][:dept_ids].nil?
        dept_ids = params[:send_sms][:dept_ids]
        sms_setting = SmsSetting.new()
        @recipients = []
        if MultiSchool.current_school.id == 319
          @recipients=['8801918179040','8801711924683','8801678401308','8801815709133','8801764198796','8801680425262','8801941013013','8801911438293','8801771767811','8801716752996','8801715437299','8801763710825','8801714453713','8801714552559','8801715331407','8801715224886']
        end
        dept_ids.each do |d_id|
          department = EmployeeDepartment.find(d_id)
          department_employees = department.employees
          department_employees.each do |employee|
            if sms_setting.employee_sms_active
              @recipients.push employee.mobile_phone unless (employee.mobile_phone.nil? or employee.mobile_phone == "")
            end
          end
        end
        unless @recipients.empty?
          message = params[:send_sms][:message]
          Delayed::Job.enqueue(SmsManager.new(message,@recipients))
          render(:update) do |page|
            page.replace_html 'list',:text=>""
            page.replace_html 'status-message',:text=>"<p class=\"flash-msg\">#{t('sms_sending_intiated', :log_url => url_for(:controller => "sms", :action => "show_sms_messages"))}</p>"
            page.visual_effect(:highlight, 'status-message')
          end
        else
          error = true
        end
      else
        error = true
      end
      if error
        render(:update) do |page|
          page.replace_html 'status-message',:text=>"<p class=\"flash-msg\">#{t('select_valid_departments')}</p>"
        end
      end
    end
  end

  def show_sms_messages
    @sms_messages = SmsMessage.get_sms_messages(params[:page])
    @total_sms = Configuration.get_config_value("TotalSmsCount")
  end

  def show_sms_logs
    @sms_message = SmsMessage.find(params[:id])
    @sms_logs = @sms_message.get_sms_logs(params[:page])
  end
end
