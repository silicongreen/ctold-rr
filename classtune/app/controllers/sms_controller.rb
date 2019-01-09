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
    if current_user.admin?
      @batches = Batch.active
    elsif @current_user.employee?
      @batches=@current_user.employee_record.batches
      @batches+=@current_user.employee_record.subjects.collect{|b| b.batch}
      @batches=@batches.uniq unless @batches.empty?
    end 
    
    if request.post?
      error=false
      unless params[:send_sms][:student_ids].nil?
        student_ids = params[:send_sms][:student_ids]
        sms_setting = SmsSetting.new()
        @recipients=[]
        send_to = params[:send_sms][:send_to]
        if MultiSchool.current_school.id == 3319 and send_to.to_i != 6
          @recipients=['8801918179040','8801711924683','8801678401308','8801815709133','8801764198796','8801680425262','8801941013013','8801911438293','8801771767811','8801716752996','8801715437299','8801763710825','8801714453713','8801714552559','8801715331407','8801715224886']
        end
        
        student_ids.each do |s_id|
          student = Student.find(s_id)
          guardian = student.immediate_contact
          if student.is_sms_enabled
            
            if sms_setting.student_sms_active and (send_to.to_i == 1 or send_to.to_i == 2 or send_to.to_i == 5 or send_to.to_i == 6)    
              if student.sms_number.nil? or student.sms_number == ""
                @recipients.push student.phone2 unless (student.phone2.nil? or student.phone2 == "")
              else
                @recipients.push student.sms_number unless (student.sms_number.nil? or student.sms_number == "")
              end  
            end
            
            if sms_setting.parent_sms_active and (send_to.to_i == 1 or send_to.to_i == 3)
              unless guardian.nil?
                @recipients.push guardian.mobile_phone unless (guardian.mobile_phone.nil? or guardian.mobile_phone == "")
                
              end
            end
            
            if sms_setting.parent_sms_active and (send_to.to_i == 4 or send_to.to_i == 5 or send_to.to_i == 6)
              guardians = student.student_guardian
              guardians.each do |sguardian|
                @recipients.push sguardian.mobile_phone unless (sguardian.mobile_phone.nil? or sguardian.mobile_phone == "")
              end 
            end
            
          end
        end
        
        unless @recipients.empty?
          @recipients = @recipients.uniq
          if params[:send_sms][:download].blank? or params[:send_sms][:download].to_i!=1
            
            message = params[:send_sms][:message]
            sms = Delayed::Job.enqueue(SmsManager.new(message,@recipients))
            flash[:notice]="#{t('succesffully_send')}"
          else
           
            csv_string = FasterCSV.generate do |csv|
              rows = []
              rows << "Mobile No"
              csv << rows
              @recipients.each do |number|
                rows = []
                rows << "#{number}"
                csv << rows
              end
            end
            
            filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.csv"
            send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
          end
          
        else
          flash[:notice]="#{t('no_number_selected')}"
        end
      else
        flash[:notice]="#{t('no_student_selected')}"
      end
      
    end
  end
  
  def panel
    if current_user.admin?
      @batches = Batch.active
    elsif @current_user.employee?
      @batches=@current_user.employee_record.batches
      @batches+=@current_user.employee_record.subjects.collect{|b| b.batch}
      @batches=@batches.uniq unless @batches.empty?
    end 
  end
  
  def list_students
    @students = []
    unless params[:batch_id].blank?
      batch_ids = params[:batch_id].split(",")
      @students = Student.find_all_by_batch_id(batch_ids,:conditions=>"is_sms_enabled=true")
    end
  end
  
  def list_students_new
    @students = []
    unless params[:batch_id].blank?
      batch_ids = params[:batch_id].split(",")
      @students = Student.find_all_by_batch_id(batch_ids,:conditions=>"is_sms_enabled=true")
    end
  end
  
  def list_students_new_upassword
    @students = []
    unless params[:batch_id].blank?
      batch_ids = params[:batch_id].split(",")
      @students = Student.find_all_by_batch_id(batch_ids,:conditions=>"is_sms_enabled=true")
    end
  end

  def batches
    if current_user.admin?
      @batches = Batch.active
    elsif @current_user.employee?
      @batches=@current_user.employee_record.batches
      @batches+=@current_user.employee_record.subjects.collect{|b| b.batch}
      @batches=@batches.uniq unless @batches.empty?
    end
    if request.post?
      unless params[:send_sms][:batch_ids].nil?
        batch_ids = params[:send_sms][:batch_ids]
        sms_setting = SmsSetting.new()
        send_to = params[:send_sms][:send_to]
        @recipients = []
        if MultiSchool.current_school.id == 3319
          @recipients=['8801918179040','8801711924683','8801678401308','8801815709133','8801764198796','8801680425262','8801941013013','8801911438293','8801771767811','8801716752996','8801715437299','8801763710825','8801714453713','8801714552559','8801715331407','8801715224886']
        end
        batch_ids.each do |b_id|
          batch = Batch.find(b_id)
          batch_students = batch.students
          batch_students.each do |student|
            if student.is_sms_enabled
              if sms_setting.student_sms_active and (send_to.to_i == 1 or send_to.to_i == 2) 
                if student.sms_number.nil? or student.sms_number == ""
                  @recipients.push student.phone2 unless (student.phone2.nil? or student.phone2 == "")
                else
                  @recipients.push student.sms_number unless (student.sms_number.nil? or student.sms_number == "")
                end
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
    if MultiSchool.current_school.id == 3319
        @recipients=['8801918179040','8801711924683','8801678401308','8801815709133','8801764198796','8801680425262','8801941013013','8801911438293','8801771767811','8801716752996','8801715437299','8801763710825','8801714453713','8801714552559','8801715331407','8801715224886']
    end
    batches.each do |batch|
      batch_students = batch.students
      batch_students.each do |student|
        if student.is_sms_enabled
          if student_sms
            if student.sms_number.nil? or student.sms_number == ""
                @recipients.push student.phone2 unless (student.phone2.nil? or student.phone2 == "")
            else
                @recipients.push student.sms_number unless (student.sms_number.nil? or student.sms_number == "")
            end
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
        if MultiSchool.current_school.id == 3319
          @recipients=['8801918179040','8801711924683','8801678401308','8801815709133','8801764198796','8801680425262','8801941013013','8801911438293','8801771767811','8801716752996','8801715437299','8801763710825','8801714453713','8801714552559','8801715331407','8801715224886']
        end
        employee_ids.each do |e_id|
          employee = Employee.find(e_id)
          if sms_setting.employee_sms_active
            @recipients.push employee.mobile_phone unless (employee.mobile_phone.nil? or employee.mobile_phone == "")
          end
        end
        unless @recipients.empty?
          @recipients = @recipients.uniq
          if params[:send_sms][:download].blank? or params[:send_sms][:download].to_i!=1
            
            message = params[:send_sms][:message]
            sms = Delayed::Job.enqueue(SmsManager.new(message,@recipients))
            flash[:notice]="#{t('succesffully_send')}"
          else
           
            csv_string = FasterCSV.generate do |csv|
              rows = []
              rows << "Mobile No"
              csv << rows
              @recipients.each do |number|
                rows = []
                rows << "#{number}"
                csv << rows
              end
            end
            
            filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.csv"
            send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
          end
          
        else
         flash[:notice]="#{t('no_number_selected')}"
        end
      else
        flash[:notice]="#{t('no_employee_selected')}"
      end
      
    end
  end
  
  def show_option_student_upass
    if current_user.admin?
      @batches = Batch.active
    elsif @current_user.employee?
      @batches=@current_user.employee_record.batches
      @batches+=@current_user.employee_record.subjects.collect{|b| b.batch}
      @batches=@batches.uniq unless @batches.empty?
    end
    render :update do |page|
      page.replace_html "student-custom-option", :partial => "student_custom_option"
      page << 'j("#student-custom-option").show();'
      page << 'j("#submit_button").show();'
    end
  end
  
  def show_option_student
    unless params[:option].blank?
      if params[:option] == "general"
        if current_user.admin?
          @batches = Batch.active
        elsif @current_user.employee?
          @batches=@current_user.employee_record.batches
          @batches+=@current_user.employee_record.subjects.collect{|b| b.batch}
          @batches=@batches.uniq unless @batches.empty?
        end
        render :update do |page|
          page.replace_html "sms_opt", :partial => "student_general"
          page << 'j("#sms_opt").show();'
        end
      elsif params[:option] == "upassword"  
        @message = ""
        if File.exists?("#{Rails.root}/config/sms_text_#{MultiSchool.current_school.id}.yml")
          sms_text_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/sms_text_#{MultiSchool.current_school.id}.yml")['school']
          @message = sms_text_config['upass']
        end
        
        render :update do |page|
          page.replace_html "sms_opt", :partial => "student_upassword"
          page << 'j("#sms_opt").show();'
        end
      else
        render :update do |page|
          page.replace_html "sms_panel", :partial => "employees"
          page << 'j("#sms_panel").show();'
        end
      end
    else
      render :update do |page|
        page.replace_html "sms_opt", :text => ""
        page << 'j("#sms_opt").hide();'
      end
    end 
  end
  
  def show_option
    flash[:notice] = nil
    if request.post?
      unless params[:option_sms].blank?
        if params[:option_sms] == 'employee'
          unless params[:sms_message].nil? or params[:sms_message].empty? or params[:sms_message].blank?
            message = params[:sms_message]
            unless params[:sms_to].nil? or params[:sms_to].empty? or params[:sms_to].blank?
              employee_ids = params[:sms_to]
              @recipients=[]
              sms_setting = SmsSetting.new()
              employee_ids.each do |e_id|
                student = Student.find(e_id)
                if sms_setting.employee_sms_active
                  @recipients.push employee.mobile_phone unless (employee.mobile_phone.nil? or employee.mobile_phone == "")
                end
              end
              
              unless @recipients.empty?
                if params[:send_sms][:download].blank? or params[:send_sms][:download].to_i!=1
                  message = params[:send_sms][:message]
                  sms = Delayed::Job.enqueue(SmsManager.new(message,@recipients))
                  flash[:notice]="#{t('succesffully_send')}"
                  redirect_to :action => "panel"
                else

                  csv_string = FasterCSV.generate do |csv|
                    rows = []
                    rows << "Mobile No"
                    rows << "Message"
                    csv << rows
                    @recipients.each do |number|
                      rows = []
                      rows << "#{number}"
                      rows << "#{message}"
                      csv << rows
                    end
                  end

                  filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.csv"
                  send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
                end
              end
            else
              employee_ids = params[:sms_numbers].split(",")
              @recipients = employee_ids
              unless @recipients.empty?
                if params[:send_sms][:download].blank? or params[:send_sms][:download].to_i!=1
                  message = params[:send_sms][:message]
                  sms = Delayed::Job.enqueue(SmsManager.new(message,@recipients))
                  flash[:notice]="#{t('succesffully_send')}"
                  redirect_to :action => "panel"
                else

                  csv_string = FasterCSV.generate do |csv|
                    rows = []
                    rows << "Mobile No"
                    rows << "Message"
                    csv << rows
                    @recipients.each do |number|
                      rows = []
                      rows << "#{number}"
                      rows << "#{message}"
                      csv << rows
                    end
                  end

                  filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.csv"
                  send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
                end
              end
            end
          end
        elsif params[:option_sms] == 'student_general'
          sent_to = params[:send_sms][:send_to]
          unless params[:sms_message].nil? or params[:sms_message].empty? or params[:sms_message].blank?
            message = params[:sms_message]
            unless params[:students].nil? or params[:students].empty? or params[:students].blank?
              student_ids = params[:students]
              @recipients=[]
              sms_setting = SmsSetting.new()
              student_ids.each do |s_id|
                student = Student.find(s_id)
                if sent_to.to_i == 1
                  if sms_setting.student_sms_active
                    
                    if student.sms_number.nil? or student.sms_number == ""
                      @recipients.push student.phone2 unless (student.phone2.nil? or student.phone2 == "")
                    else
                      @recipients.push student.sms_number unless (student.sms_number.nil? or student.sms_number == "")
                    end
                    
                    guardian = student.immediate_contact
                    unless guardian.nil?
                      @recipients.push guardian.mobile_phone unless (guardian.mobile_phone.nil? or guardian.mobile_phone == "")
                    end
                  end
                elsif sent_to.to_i == 2
                  if student.sms_number.nil? or student.sms_number == ""
                    @recipients.push student.phone2 unless (student.phone2.nil? or student.phone2 == "")
                  else
                    @recipients.push student.sms_number unless (student.sms_number.nil? or student.sms_number == "")
                  end
                elsif sent_to.to_i == 3
                  guardian = student.immediate_contact
                  unless guardian.nil?
                    @recipients.push guardian.mobile_phone unless (guardian.mobile_phone.nil? or guardian.mobile_phone == "")
                  end
                end
              end
              
              unless @recipients.empty?
                if params[:send_sms][:download].blank? or params[:send_sms][:download].to_i!=1
                  message = params[:send_sms][:message]
                  sms = Delayed::Job.enqueue(SmsManager.new(message,@recipients))
                  flash[:notice]="#{t('succesffully_send')}"
                  redirect_to :action => "panel"
                else

                  csv_string = FasterCSV.generate do |csv|
                    rows = []
                    rows << "Mobile No"
                    rows << "Message"
                    csv << rows
                    @recipients.each do |number|
                      rows = []
                      rows << "#{number}"
                      rows << "#{message}"
                      csv << rows
                    end
                  end

                  filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.csv"
                  send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
                end
              end
            else
              student_ids = params[:sms_numbers].split(",")
              @recipients = student_ids
              unless @recipients.empty?
                if params[:send_sms][:download].blank? or params[:send_sms][:download].to_i!=1
                  message = params[:send_sms][:message]
                  sms = Delayed::Job.enqueue(SmsManager.new(message,@recipients))
                  flash[:notice]="#{t('succesffully_send')}"
                  redirect_to :action => "panel"
                else

                  csv_string = FasterCSV.generate do |csv|
                    rows = []
                    rows << "Mobile No"
                    rows << "Message"
                    csv << rows
                    @recipients.each do |number|
                      rows = []
                      rows << "#{number}"
                      rows << "#{message}"
                      csv << rows
                    end
                  end

                  filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.csv"
                  send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
                end
              end
            end
          end
        elsif params[:option_sms] == 'student_upassword'
          sent_to = params[:send_sms][:send_to]
          unless params[:sms_message].nil? or params[:sms_message].empty? or params[:sms_message].blank?
            message = params[:sms_message]
            unless params[:students].nil? or params[:students].empty? or params[:students].blank?
              student_ids = params[:students]
              send_sms_student(student_ids, message, params[:send_sms][:download], sent_to)
            else
              if current_user.admin?
                batches = Batch.active
              elsif @current_user.employee?
                batches=@current_user.employee_record.batches
                batches+=@current_user.employee_record.subjects.collect{|b| b.batch}
                batches=batches.uniq unless batches.empty?
              end
              
              unless batches.nil?
                students = []
                batch_ids = batches.map(&:id)
                students = Student.find_all_by_batch_id(batch_ids,:conditions=>"is_sms_enabled=true").map(&:id)
                send_sms_student(students, message, params[:send_sms][:download], sent_to)
              end
              
            end
          end
        end
      else
        unless params[:option].blank?
          if params[:option] == "student"
            render :update do |page|
              page.replace_html "sms_panel", :partial => "student"
              page << 'j("#sms_panel").show();'
            end
          else
            render :update do |page|
              page.replace_html "sms_panel", :partial => "employees"
              page << 'j("#sms_panel").show();'
            end
          end
        else
          render :update do |page|
            page.replace_html "sms_panel", :text => ""
            page << 'j("#sms_panel").hide();'
          end
        end  
      end
    end
  end
  
  def show_test_employee
    render :update do |page|
      page.replace_html "employee-list", :partial => "test_employee"
      page << 'j("#batch_panel").hide();'
    end
  end
  
  def show_test_student
    render :update do |page|
      page.replace_html "student-list", :partial => "test_student"
      page << 'j("#batch_panel_student").hide();'
    end
  end

  def list_employees
    @employees = []
    unless params[:dept_id].blank?
      dept_ids = params[:dept_id].split(",")
      @employees = Employee.find_all_by_employee_department_id(dept_ids)
    end
  end
  
  def list_employees_new
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
        if MultiSchool.current_school.id == 3319
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
  
  private 
  
  def send_sms_student(student_ids,message,download_opt,sent_to)
    @conn = ActiveRecord::Base.connection
    
    row_header = ['Mobile No','Message']
    csv = true
    if MultiSchool.current_school.id == 352
      row_header = ['start','']
      csv = false
    end
    @recipients=[]
    i = 0
    tmp_message = []
    sms_setting = SmsSetting.new()
    student_ids.each do |s_id|
      student = Student.find(s_id)
      user_id = student.user.id
      
      sql = "SELECT fu.paid_username,fu.paid_password FROM students as s left join tds_free_users as fu on s.user_id=fu.paid_id where fu.paid_school_id=#{MultiSchool.current_school.id} and  fu.paid_id=#{user_id} and s.is_deleted = 0"
      student_data = @conn.execute(sql).all_hashes
      unless student_data.nil? or student_data.empty? or student_data.blank?
        full_name = student.full_name
        user_name = student_data[0]['paid_username']
        password = student_data[0]['paid_password']

        if sent_to.to_i == 1
          if sms_setting.student_sms_active

            if student.sms_number.nil? or student.sms_number == ""
              unless student.phone2.nil? or student.phone2 == ""
                tmp_message[i] = message
                tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
                tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
                tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
                i += 1
                @recipients.push student.phone2
              end
            else
              unless student.sms_number.nil? or student.sms_number == ""
                tmp_message[i] = message
                tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
                tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
                tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
                i += 1
                @recipients.push student.sms_number
              end
            end

            p1found = false
            guardian = student.immediate_contact
            unless guardian.nil?
              user_id = guardian.user.id
              sql = "SELECT fu.paid_username,fu.paid_password FROM guardians as s left join tds_free_users as fu on s.user_id=fu.paid_id where fu.paid_school_id=#{MultiSchool.current_school.id} and  fu.paid_id=#{user_id} and fu.paid_username LIKE '%p1%'"
              student_data = @conn.execute(sql).all_hashes
              unless student_data.nil? or student_data.empty? or student_data.blank?
                p1found = true
                full_name = guardian.full_name
                user_name = student_data[0]['paid_username']
                password = student_data[0]['paid_password']
                unless (guardian.mobile_phone.nil? or guardian.mobile_phone == "")
                  tmp_message[i] = message
                  tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
                  tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
                  tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
                  i += 1
                  @recipients.push guardian.mobile_phone 
                end
              end
            end

            if p1found == false
              guardians = student.guardians
              guardians.each do |guardian|
                user_id = guardian.user.id
                sql = "SELECT fu.paid_username,fu.paid_password FROM guardians as s left join tds_free_users as fu on s.user_id=fu.paid_id where fu.paid_school_id=#{MultiSchool.current_school.id} and  fu.paid_id=#{user_id} and fu.paid_username LIKE '%p1%'"
                student_data = @conn.execute(sql).all_hashes
                unless student_data.nil? or student_data.empty? or student_data.blank?
                  p1found = true
                  full_name = guardian.full_name
                  user_name = student_data[0]['paid_username']
                  password = student_data[0]['paid_password']
                  unless (guardian.mobile_phone.nil? or guardian.mobile_phone == "")
                    tmp_message[i] = message
                    tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
                    tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
                    tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
                    i += 1
                    unless guardian.mobile_phone.nil? or guardian.mobile_phone == ""
                      @recipients.push guardian.mobile_phone 
                    else
                       unless student.sms_number.nil? or student.sms_number == ""
                        tmp_message[i] = message
                        tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
                        tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
                        tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
                        i += 1
                        @recipients.push student.sms_number
                      end
                    end
                  end
                end
              end
            end
          end
        elsif sent_to.to_i == 2
          if student.sms_number.nil? or student.sms_number == ""
            unless student.phone2.nil? or student.phone2 == ""
              tmp_message[i] = message
              tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
              tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
              tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
              i += 1
              @recipients.push student.phone2
            end
          else
            unless student.sms_number.nil? or student.sms_number == ""
              tmp_message[i] = message
              tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
              tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
              tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
              i += 1
              @recipients.push student.sms_number
            end
          end
        elsif sent_to.to_i == 3
          p1found = false
          guardian = student.immediate_contact
          unless guardian.nil?
            user_id = guardian.user.id
            sql = "SELECT fu.paid_username,fu.paid_password FROM guardians as s left join tds_free_users as fu on s.user_id=fu.paid_id where fu.paid_school_id=#{MultiSchool.current_school.id} and  fu.paid_id=#{user_id} and fu.paid_username LIKE '%p1%'"
            student_data = @conn.execute(sql).all_hashes
            unless student_data.nil? or student_data.empty? or student_data.blank?
              p1found = true
              full_name = guardian.full_name
              user_name = student_data[0]['paid_username']
              password = student_data[0]['paid_password']
              unless (guardian.mobile_phone.nil? or guardian.mobile_phone == "")
                tmp_message[i] = message
                tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
                tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
                tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
                i += 1
                @recipients.push guardian.mobile_phone 
              else
                unless student.sms_number.nil? or student.sms_number == ""
                  tmp_message[i] = message
                  tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
                  tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
                  tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
                  i += 1
                  @recipients.push student.sms_number
                end
              end
            end
          end

          if p1found == false
            guardians = student.guardians
            guardians.each do |guardian|
              user_id = guardian.user.id
              sql = "SELECT fu.paid_username,fu.paid_password FROM guardians as s left join tds_free_users as fu on s.user_id=fu.paid_id where fu.paid_school_id=#{MultiSchool.current_school.id} and  fu.paid_id=#{user_id} and fu.paid_username LIKE '%p1%'"
              student_data = @conn.execute(sql).all_hashes
              unless student_data.nil? or student_data.empty? or student_data.blank?
                full_name = guardian.full_name
                user_name = student_data[0]['paid_username']
                password = student_data[0]['paid_password']
                unless (guardian.mobile_phone.nil? or guardian.mobile_phone == "")
                  tmp_message[i] = message
                  tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
                  tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
                  tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
                  i += 1
                  @recipients.push guardian.mobile_phone 
                else
                  unless student.sms_number.nil? or student.sms_number == ""
                    tmp_message[i] = message
                    tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
                    tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
                    tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
                    i += 1
                    @recipients.push student.sms_number
                  end
                end
              end
            end
          end
        end
      end
    end

    unless @recipients.empty?
      if download_opt.blank? or download_opt.to_i!=1
        sms = Delayed::Job.enqueue(SmsManager.new(message,@recipients))
        flash[:notice]="#{t('succesffully_send')}"
        redirect_to :action => "panel"
      else
        i = 0
        if csv
          csv_string = FasterCSV.generate do |csv|
            rows = []
            row_header.each do |r|
              rows << r
            end
            csv << rows
            @recipients.each do |number|
              rows = []
              rows << "#{number}"
              rows << "#{tmp_message[i]}"
              csv << rows
              i += 1
            end
          end

          filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.csv"
          send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
        else
          require 'spreadsheet'
          Spreadsheet.client_encoding = 'UTF-8'
          row_1 = row_header
          new_book = Spreadsheet::Workbook.new
          
          new_book.create_worksheet :name => 'Student Data'
          new_book.worksheet(0).insert_row(0, row_1)
          
          new_book.worksheet(0).row(0).format 2
          
          ind = 1
          k = 0
          @recipients.each do |number|
            row_new = [number, tmp_message[k]]
            new_book.worksheet(0).insert_row(ind, row_new)
            ind += 1
            k += 1
          end
          
          spreadsheet = StringIO.new 
          new_book.write spreadsheet 
          
          filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.xls"
          
          send_data spreadsheet.string, :filename => filename, :type =>  "application/vnd.ms-excel"
        end
      end
    end
  end
end
