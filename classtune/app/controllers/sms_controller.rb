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
    row_header = ['Mobile No','Message']
    csv = true
    if MultiSchool.current_school.id == 352
      row_header = ['start','']
      csv = false
    end
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
                  csv << rows
                end
              end

              filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.csv"
              send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
            else
              require 'spreadsheet'
              Spreadsheet.client_encoding = 'UTF-8'
              row_1 = row_header
              new_book = Spreadsheet::Workbook.new

              new_book.create_worksheet :name => 'SMS Data'
              new_book.worksheet(0).insert_row(0, row_1)

              new_book.worksheet(0).row(0).format 2

              ind = 1
              k = 0
              @recipients.each do |number|
                row_new = [number, message]
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
          
        else
          flash[:notice]="#{t('no_number_selected')}"
        end
      else
        flash[:notice]="#{t('no_student_selected')}"
      end
      
    end
  end
  
  def panel
    #abort(.inspect)
    #@valid_exam_group_ids =  if ((||[]) & [:admin, :examination_control,:enter_results,:view_results]).present? && @unpublished_exam_group_ids.present?
    
    
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
  
  def list_students_new_feedues
    @students = []
    unless params[:batch_id].blank?
      unless params[:fee_id].blank?
        batch_ids = params[:batch_id].split(",")
        fee_collection = FinanceFeeCollection.find(params[:fee_id])
        unless fee_collection.blank?  
          student_ids = fee_collection.finance_fees.find_all_by_batch_id(batch_ids).collect(&:student_id).join(',')
          fees_students = FinanceFee.find(:all, :conditions=>"fee_collection_id = #{fee_collection.id} and is_paid = #{false} and balance > 0 and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id').map(&:student_id)
          @students = Student.find_all_by_id(fees_students,:conditions=>"is_sms_enabled=true")
        end
      end
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
    row_header = ['Mobile No','Message']
    csv = true
    if MultiSchool.current_school.id == 352
      row_header = ['start','']
      csv = false
    end
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
                  csv << rows
                end
              end

              filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.csv"
              send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
            else
              require 'spreadsheet'
              Spreadsheet.client_encoding = 'UTF-8'
              row_1 = row_header
              new_book = Spreadsheet::Workbook.new

              new_book.create_worksheet :name => 'SMS Data'
              new_book.worksheet(0).insert_row(0, row_1)

              new_book.worksheet(0).row(0).format 2

              ind = 1
              k = 0
              @recipients.each do |number|
                row_new = [number, message]
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
  
  def show_option_student_feedues
    unless params[:fee_id].nil? or params[:fee_id].empty? or params[:fee_id].blank?
      date    =  FinanceFeeCollection.find(params[:fee_id])
      @batches = date.batches
      
      render :update do |page|
        page.replace_html "student-custom-option", :partial => "student_custom_option_feedues"
        page << 'j("#student-custom-option").show();'
        page << 'j("#submit_button").show();'
      end
    else
      render :update do |page|
        page.replace_html "student-custom-option", :text => ""
        page << 'j("#student-custom-option").hide();'
        page << 'j("#submit_button").hide();'
      end
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
      elsif params[:option] == "feedues"  
        @finance_fee_collections = FinanceFeeCollection.find(:all, :conditions => "is_advance_fee_collection = #{false} and finance_fee_collections.is_deleted = '#{false}'")
        @message = ""
        if File.exists?("#{Rails.root}/config/sms_text_#{MultiSchool.current_school.id}.yml")
          sms_text_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/sms_text_#{MultiSchool.current_school.id}.yml")['school']
          @message = sms_text_config['feedues']
        end
        
        render :update do |page|
          page.replace_html "sms_opt", :partial => "student_feedues"
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
    row_header = ['Mobile No','Message']
    csv = true
    if MultiSchool.current_school.id == 352
      row_header = ['start','']
      csv = false
    end
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
                  message = params[:sms_message]
                  sms = Delayed::Job.enqueue(SmsManager.new(message,@recipients))
                  flash[:notice]="#{t('succesffully_send')}"
                  redirect_to :action => "panel"
                else
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
                        rows << "#{message}"
                        csv << rows
                      end
                    end

                    filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.csv"
                    send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
                  else
                    require 'spreadsheet'
                    Spreadsheet.client_encoding = 'UTF-8'
                    row_1 = row_header
                    new_book = Spreadsheet::Workbook.new

                    new_book.create_worksheet :name => 'SMS Data'
                    new_book.worksheet(0).insert_row(0, row_1)

                    new_book.worksheet(0).row(0).format 2

                    ind = 1
                    k = 0
                    @recipients.each do |number|
                      row_new = [number, message]
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
            else
              employee_ids = params[:sms_numbers].split(",")
              @recipients = employee_ids
              unless @recipients.empty?
                if params[:send_sms][:download].blank? or params[:send_sms][:download].to_i!=1
                  message = params[:sms_message]
                  sms = Delayed::Job.enqueue(SmsManager.new(message,@recipients))
                  flash[:notice]="#{t('succesffully_send')}"
                  redirect_to :action => "panel"
                else
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
                        rows << "#{message}"
                        csv << rows
                      end
                    end

                    filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.csv"
                    send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
                  else
                    require 'spreadsheet'
                    Spreadsheet.client_encoding = 'UTF-8'
                    row_1 = row_header
                    new_book = Spreadsheet::Workbook.new

                    new_book.create_worksheet :name => 'SMS Data'
                    new_book.worksheet(0).insert_row(0, row_1)

                    new_book.worksheet(0).row(0).format 2

                    ind = 1
                    k = 0
                    @recipients.each do |number|
                      row_new = [number, message]
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
                  message = params[:sms_message]
                  sms = Delayed::Job.enqueue(SmsManager.new(message,@recipients))
                  flash[:notice]="#{t('succesffully_send')}"
                  redirect_to :action => "panel"
                else
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
                        rows << "#{message}"
                        csv << rows
                      end
                    end

                    filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.csv"
                    send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
                  else
                    require 'spreadsheet'
                    Spreadsheet.client_encoding = 'UTF-8'
                    row_1 = row_header
                    new_book = Spreadsheet::Workbook.new

                    new_book.create_worksheet :name => 'SMS Data'
                    new_book.worksheet(0).insert_row(0, row_1)

                    new_book.worksheet(0).row(0).format 2

                    ind = 1
                    k = 0
                    @recipients.each do |number|
                      row_new = [number, message]
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
            else
              student_ids = params[:sms_numbers].split(",")
              @recipients = student_ids
              unless @recipients.empty?
                if params[:send_sms][:download].blank? or params[:send_sms][:download].to_i!=1
                  message = params[:sms_message]
                  sms = Delayed::Job.enqueue(SmsManager.new(message,@recipients))
                  flash[:notice]="#{t('succesffully_send')}"
                  redirect_to :action => "panel"
                else
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
                        rows << "#{message}"
                        csv << rows
                      end
                    end

                    filename = "#{MultiSchool.current_school.name}-sms-list-#{Time.now.to_date.to_s}.csv"
                    send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
                  else
                    require 'spreadsheet'
                    Spreadsheet.client_encoding = 'UTF-8'
                    row_1 = row_header
                    new_book = Spreadsheet::Workbook.new

                    new_book.create_worksheet :name => 'SMS Data'
                    new_book.worksheet(0).insert_row(0, row_1)

                    new_book.worksheet(0).row(0).format 2

                    ind = 1
                    k = 0
                    @recipients.each do |number|
                      row_new = [number, message]
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
        elsif params[:option_sms] == 'student_feedues'
          sent_to = params[:send_sms][:send_to]
          fee_collection_id = params[:fee_collections]
          unless params[:sms_message].nil? or params[:sms_message].empty? or params[:sms_message].blank?
            message = params[:sms_message]
            unless params[:students].nil? or params[:students].empty? or params[:students].blank?
              fee_collection  =  FinanceFeeCollection.find(fee_collection_id)
              student_ids = params[:students].join(',')
              fees_students = FinanceFee.find(:all, :conditions=>"fee_collection_id = #{fee_collection.id} and is_paid = #{false} and balance > 0 and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id').map(&:student_id)
              students = Student.find_all_by_id(fees_students,:conditions=>"is_sms_enabled=true").map(&:id)
              send_sms_student_fees(fee_collection_id, students, message, params[:send_sms][:download], sent_to)
            else
              fee_collection  =  FinanceFeeCollection.find(fee_collection_id)
              batches = fee_collection.batches
              
              unless batches.nil?
                students = []
                batch_ids = batches.map(&:id).join(",")
                student_ids = fee_collection.finance_fees.find(:all,:conditions=>"batch_id IN ('#{batch_ids}')").collect(&:student_id).join(',')
                fees_students = FinanceFee.find(:all, :conditions=>"fee_collection_id = #{fee_collection.id} and is_paid = #{false} and balance > 0 and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id').map(&:student_id)
                students = Student.find_all_by_id(fees_students,:conditions=>"is_sms_enabled=true").map(&:id)
                send_sms_student_fees(fee_collection_id, students, message, params[:send_sms][:download], sent_to)
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
    
    if sent_to.to_i != 3
      sql = "SELECT s.first_name, s.middle_name, s.last_name, s.sms_number, s.phone2, fu.paid_username,fu.paid_password FROM students as s left join tds_free_users as fu on s.user_id=fu.paid_id where fu.paid_school_id=#{MultiSchool.current_school.id} and s.id IN (#{student_ids.join(',')}) and s.is_deleted = 0"
      student_data = @conn.execute(sql).all_hashes
    end
    
    if sent_to.to_i == 1 or sent_to.to_i == 3
      guardians = Guardian.find(:all, :conditions => "ward_id IN (#{student_ids.join(',')})").map(&:user_id)
      
      if MultiSchool.current_school.id == 352
        sql = "SELECT g.first_name, g.last_name, s.sms_number, g.mobile_phone,fu.paid_username,fu.paid_password FROM guardians as g INNER join students s ON s.id = g.ward_id left join tds_free_users as fu on g.user_id=fu.paid_id where fu.paid_school_id=#{MultiSchool.current_school.id} and fu.paid_id IN (#{guardians.join(',')}) and fu.paid_username LIKE '%p1%' and s.is_deleted = 0" 
      else
        sql = "SELECT g.first_name, g.last_name, s.sms_number, g.mobile_phone,fu.paid_username,fu.paid_password FROM guardians as g INNER join students s ON s.id = g.ward_id left join tds_free_users as fu on g.user_id=fu.paid_id where fu.paid_school_id=#{MultiSchool.current_school.id} and fu.paid_id IN (#{guardians.join(',')}) and s.is_deleted = 0"
      end
      guardians_data = @conn.execute(sql).all_hashes
      
    end
    
    if sent_to.to_i != 3
      student_data.each do |s|
        full_name = "#{s["first_name"]} #{s["middle_name"]} #{s["last_name"]}"
        full_name.gsub("  "," ")
        full_name.gsub("- ","-")
        full_name.gsub(" -","-")
        user_name = s['paid_username']
        password = s['paid_password']
        unless s['sms_number'].nil? or s['sms_number'].empty? or s['sms_number'].blank?
          tmp_message[i] = message
          tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
          tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
          tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
          i += 1
          @recipients.push s['sms_number']
        else
          unless s['phone2'].nil? or s['phone2'].empty? or s['phone2'].blank?
            tmp_message[i] = message
            tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
            tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
            tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
            i += 1
            @recipients.push s['phone2']
          end
        end
      end
    end
    
    if sent_to.to_i == 1 or sent_to.to_i == 3
      guardians_data.each do |g|
        full_name = "#{g["first_name"]} #{g["last_name"]}"
        full_name.gsub("  "," ")
        full_name.gsub("- ","-")
        full_name.gsub(" -","-")
        user_name = g['paid_username']
        password = g['paid_password']
        unless g['sms_number'].nil? or g['sms_number'].empty? or g['sms_number'].blank?
          tmp_message[i] = message
          tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
          tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
          tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
          i += 1
          @recipients.push g['sms_number']
        else
          unless g['mobile_phone'].nil? or g['mobile_phone'].empty? or g['mobile_phone'].blank?
            tmp_message[i] = message
            tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
            tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
            tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
            i += 1
            @recipients.push g['mobile_phone']
          end
        end
      end
    end
    
    unless @recipients.empty?
      if download_opt.blank? or download_opt.to_i!=1
        sms = Delayed::Job.enqueue(SmsManagerIndividualMessage.new(tmp_message,@recipients))
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
  
  def send_sms_student_fees(fee_collection_id, student_ids,message,download_opt,sent_to)
    fee_collection  =  FinanceFeeCollection.find(fee_collection_id)
    
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
    std_ids = student_ids.join(",")
    fees_students = FinanceFee.find(:all, :conditions=>"fee_collection_id = #{fee_collection.id} and is_paid = #{false} and balance > 0 and students.id IN (#{ std_ids})" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
    if sent_to.to_i == 2
      fees_students.each do |fee|
        std = Student.find(fee.student_id)
        balance = '%.2f' % fee.balance 
        full_name = std.full_name
        unless std.sms_number.nil? or std.sms_number.empty? or std.sms_number.blank?
          tmp_message[i] = message
          tmp_message[i] = tmp_message[i].gsub("#UNAME#", full_name)
          tmp_message[i] = tmp_message[i].gsub("#UID#", std.admission_no)
          tmp_message[i] = tmp_message[i].gsub("#AMOUNT#", balance)
          tmp_message[i] = tmp_message[i].gsub("#DUE_DATE#", fee.due_date.to_date.strftime("%d-%b-%Y"))
          i += 1
          @recipients.push std.sms_number
        else
          unless std.phone2.nil? or std.phone2.empty? or std.phone2.blank?
            tmp_message[i] = message
            tmp_message[i] = tmp_message[i].gsub("#UNAME#", full_name)
            tmp_message[i] = tmp_message[i].gsub("#UID#", std.admission_no)
            tmp_message[i] = tmp_message[i].gsub("#AMOUNT#", balance)
            tmp_message[i] = tmp_message[i].gsub("#DUE_DATE#", fee.due_date.to_date.strftime("%d-%b-%Y"))
            i += 1
            @recipients.push std.phone2
          end
        end
      end
    end
    
    if sent_to.to_i == 3
      fees_students.each do |fee|
        std = Student.find(fee.student_id)
        balance = '%.2f' % fee.balance 
        full_name = std.full_name
        unless std.sms_number.nil? or std.sms_number.empty? or std.sms_number.blank?
          tmp_message[i] = message
          tmp_message[i] = tmp_message[i].gsub("#UNAME#", full_name)
          tmp_message[i] = tmp_message[i].gsub("#UID#", std.admission_no)
          tmp_message[i] = tmp_message[i].gsub("#AMOUNT#", balance)
          tmp_message[i] = tmp_message[i].gsub("#DUE_DATE#", fee.due_date.to_date.strftime("%d-%b-%Y"))
          i += 1
          @recipients.push std.sms_number
        else
          unless std.phone2.nil? or std.phone2.empty? or std.phone2.blank?
            tmp_message[i] = message
            tmp_message[i] = tmp_message[i].gsub("#UNAME#", full_name)
            tmp_message[i] = tmp_message[i].gsub("#UID#", std.admission_no)
            tmp_message[i] = tmp_message[i].gsub("#AMOUNT#", balance)
            tmp_message[i] = tmp_message[i].gsub("#DUE_DATE#", fee.due_date.to_date.strftime("%d-%b-%Y"))
            i += 1
            @recipients.push std.phone2
          end
        end
      end
    end
    
    unless @recipients.empty?
      if download_opt.blank? or download_opt.to_i!=1
        sms = Delayed::Job.enqueue(SmsManagerIndividualMessage.new(tmp_message,@recipients))
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
