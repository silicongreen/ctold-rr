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

class EmployeeAttendanceController < ApplicationController
  before_filter :login_required,:configuration_settings_for_hr
  before_filter :check_permission, :only=>[:leaves]
  before_filter :protect_leave_dashboard, :only => [:leaves]#, :employee_attendance_pdf]
  before_filter :protect_applied_leave, :only => [:own_leave_application, :cancel_application]
  before_filter :protect_manager_leave_application_view, :only => [:leave_application]
  before_filter :protect_leave_history, :only => [:leave_history,:update_leave_history]
  #    prawnto :prawn => {:left_margin => 25, :right_margin => 25}

  filter_access_to :all

  def add_leave_types
    @leave_types = EmployeeLeaveType.find(:all, :order => "name ASC",:conditions=>'status = 1')
    @inactive_leave_types = EmployeeLeaveType.find(:all, :order => "name ASC",:conditions=>'status = 0')
    @leave_type = EmployeeLeaveType.new(params[:leave_type])
    @employee = Employee.all
    if request.post? and @leave_type.save
      @employee.each do |e|
        EmployeeLeave.create( :employee_id => e.id, :employee_leave_type_id => @leave_type.id, :leave_count => @leave_type.max_leave_count)
      end
      flash[:notice] = "#{t('flash1')}"
      redirect_to :action => "add_leave_types"
    end
  end

  def edit_leave_types
    @leave_type = EmployeeLeaveType.find(params[:id])
    if request.post? and @leave_type.update_attributes(params[:leave_type])
      flash[:notice] = "#{t('flash2')}"
      redirect_to :action => "add_leave_types"
    end
  end

  def delete_leave_types
    @leave_type = EmployeeLeaveType.find(params[:id])
    @attendance = EmployeeAttendance.find_all_by_employee_leave_type_id(@leave_type.id)
    @leave_count = EmployeeLeave.find_all_by_employee_leave_type_id(@leave_type.id)
    if @attendance.blank?
      @leave_type.delete
      @leave_count.each do |e|
        e.delete
      end
      flash[:notice] = "#{t('flash3')}"
    else
      flash[:notice] = "#{t('flash13')}"
    end
    redirect_to :action => "add_leave_types"
    

  end

  def leave_reset_settings
    @auto_reset = Configuration.find_by_config_key('AutomaticLeaveReset')
    @reset_period = Configuration.find_by_config_key('LeaveResetPeriod')
    @last_reset = Configuration.find_by_config_key('LastAutoLeaveReset')
    @fin_start_date = Configuration.find_by_config_key('FinancialYearStartDate')
    if request.post?
      @auto_reset.update_attributes(:config_value=> params[:configuration][:automatic_leave_reset])
      @reset_period.update_attributes(:config_value=> params[:configuration][:leave_reset_period])
      @last_reset.update_attributes(:config_value=> params[:configuration][:financial_year_start_date])

      flash[:notice] = "#{t('flash_msg8')}"
      redirect_to :action => "leave_reset_settings"
    end
  end
 
  def update_employee_leave_reset_all
    @leave_count = EmployeeLeave.all
    @leave_count.each do |e|
      @leave_type = EmployeeLeaveType.find_by_id(e.employee_leave_type_id)
      if @leave_type.status
        default_leave_count = @leave_type.max_leave_count
        if @leave_type.carry_forward
          leave_taken = e.leave_taken
          available_leave = e.leave_count
          if leave_taken <= available_leave
            balance_leave = available_leave - leave_taken
            available_leave = balance_leave.to_f
            available_leave += default_leave_count.to_f
            leave_taken = 0
            e.update_attributes(:leave_taken => leave_taken,:leave_count => available_leave, :reset_date => Time.now)
          else
            available_leave = default_leave_count.to_f
            leave_taken = 0
            e.update_attributes(:leave_taken => leave_taken,:leave_count => available_leave, :reset_date => Time.now)
          end
        else
          available_leave = default_leave_count.to_f
          leave_taken = 0
          e.update_attributes(:leave_taken => leave_taken,:leave_count => available_leave, :reset_date => Time.now)
        end
      end
    end
    render :update do |page|
      page.replace_html "main-reset-box", :text => "<p class='flash-msg'>#{t('leave_count_reset_sucessfull')}</p>"
    end
  end

  def employee_leave_reset_by_department
    @departments = EmployeeDepartment.find(:all, :conditions => "status = true", :order=> "name ASC")

  end

  def list_department_leave_reset
    @leave_types = EmployeeLeaveType.find(:all, :conditions => "status = true")
    if params[:department_id] == ""
      render :update do |page|
        page.replace_html "department-list", :text => ""
      end
      return
    end
    @employees=Employee.find(:all,:conditions=>{:employee_department_id=>params[:department_id]}).sort_by{|s| s.full_name.downcase}
    render :update do |page|
      page.replace_html "department-list", :partial => 'department_list'
    end
  end

  def update_department_leave_reset
    @employee = params[:employee_id]
    @employee.each do |e|
      @leave_count = EmployeeLeave.find_all_by_employee_id(e)
      @leave_count.each do |c|
        @leave_type = EmployeeLeaveType.find_by_id(c.employee_leave_type_id)
        if @leave_type.status
          default_leave_count = @leave_type.max_leave_count
          if @leave_type.carry_forward
            leave_taken = c.leave_taken
            available_leave = c.leave_count
            if leave_taken <= available_leave
              balance_leave = available_leave - leave_taken
              available_leave = balance_leave.to_f
              available_leave += default_leave_count.to_f
              leave_taken = 0
              c.update_attributes(:leave_taken => leave_taken,:leave_count => available_leave, :reset_date => Time.now)
            else
              available_leave = default_leave_count.to_f
              leave_taken = 0
              c.update_attributes(:leave_taken => leave_taken,:leave_count => available_leave, :reset_date => Time.now)
            end
          else
            available_leave = default_leave_count.to_f
            leave_taken = 0
            c.update_attributes(:leave_taken => leave_taken,:leave_count => available_leave, :reset_date => Time.now)
          end
        end

      end
    end
    flash[:notice]= "#{t('flash12')}"
    redirect_to :controller=>"employee_attendance", :action => "employee_leave_reset_by_department"
  end


  def employee_leave_reset_by_employee
    @departments = EmployeeDepartment.find(:all,:order=>'name ASC')
    @categories  = EmployeeCategory.find(:all,:order=>'name ASC')
    @positions   = EmployeePosition.find(:all,:order=>'name ASC')
    @grades      = EmployeeGrade.find(:all,:order=>'name ASC')
  end

  def employee_search_ajax
    other_conditions = ""
    other_conditions += " AND employee_department_id = '#{params[:employee_department_id]}'" unless params[:employee_department_id] == ""
    other_conditions += " AND employee_category_id = '#{params[:employee_category_id]}'" unless params[:employee_category_id] == ""
    other_conditions += " AND employee_position_id = '#{params[:employee_position_id]}'" unless params[:employee_position_id] == ""
    other_conditions += " ANDreport employee_grade_id = '#{params[:employee_grade_id]}'" unless params[:employee_grade_id] == ""
    unless params[:query].length < 3
      @employee = Employee.find(:all,
        :conditions => ["(first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                       OR employee_number LIKE ? OR (concat(first_name, \" \", last_name) LIKE ?))" + other_conditions,
          "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
          "#{params[:query]}", "#{params[:query]}"],
        :order => "first_name asc") unless params[:query] == ''
    else
      @employee = Employee.find(:all,
        :conditions => ["employee_number = ? "+ other_conditions, "#{params[:query]}%"],
        :order => "first_name asc") unless params[:query] == ''
    end
    render :layout => false
  end

  def employee_view_all
    @departments = EmployeeDepartment.active.find(:all,:order=>'name ASC')
  end

  def employees_list
    department_id = params[:department_id]
    @employees = Employee.find_all_by_employee_department_id(department_id,:order=>'first_name ASC')

    render :update do |page|
      page.replace_html 'employee_list', :partial => 'employee_view_all_list', :object => @employees
    end
  end

  def employee_leave_details
    @employee = Employee.find(params[:id])
    @leave_count = EmployeeLeave.find_all_by_employee_id(@employee.id)
  end

  def employee_wise_leave_reset
    @employee = Employee.find(params[:id])
    @leave_count = EmployeeLeave.find_all_by_employee_id(@employee.id)
    @leave_count.each do |e|
      @leave_type = EmployeeLeaveType.find_by_id(e.employee_leave_type_id)
      if @leave_type.status
        default_leave_count = @leave_type.max_leave_count
        if @leave_type.carry_forward
          leave_taken = e.leave_taken
          available_leave = e.leave_count
          if leave_taken <= available_leave
            balance_leave = available_leave - leave_taken
            available_leave = balance_leave.to_f
            available_leave += default_leave_count.to_f
            leave_taken = 0
            e.update_attributes(:leave_taken => leave_taken,:leave_count => available_leave, :reset_date => Time.now)
          else
            available_leave = default_leave_count.to_f
            leave_taken = 0
            e.update_attributes(:leave_taken => leave_taken,:leave_count => available_leave, :reset_date => Time.now)
          end
        else
          available_leave = default_leave_count.to_f
          leave_taken = 0
          e.update_attributes(:leave_taken => leave_taken,:leave_count => available_leave, :reset_date => Time.now)
        end
      end
    end
    render :update do |page|
      flash.now[:notice]= "#{t('flash_msg12')}"
      page.replace_html "list", :partial => 'employee_reset_sucess'
    end
  end


  def register
    @departments = EmployeeDepartment.find(:all, :conditions=>"status = true", :order=> "name ASC")
    if request.post?
      unless params[:employee_attendance].nil?
        params[:employee_attendance].each_pair do |emp, att|
          @employee_attendance = EmployeeAttendance.create(:attendance_date => params[:date],
            :employees_id => emp, :employee_leave_types_id=> att) unless att == ""
        end
        flash[:notice]= "#{t('flash3')}"
        redirect_to :controller=>"employee_attendance", :action => "register"
      end
    end
  end

  def update_attendance_form
    @leave_types = EmployeeLeaveType.find(:all, :conditions=>"status = true", :order=>"name ASC")
    if params[:department_id] == ""
      render :update do |page|
        page.replace_html "attendance_form", :text => ""
      end
      return
    end

    @employees = Employee.find_all_by_employee_department_id(params[:department_id])
    render :update do |page|
      page.replace_html 'attendance_form', :partial => 'attendance_form'
    end
  end
  
  def card_attendance_pdf
    if !params[:month].blank? and !params[:year].blank? and !params[:employee_id].blank?
      @employee = Employee.find(params[:employee_id])
      @today = @local_tzone_time.to_date
      @month = params[:month]
      @year = params[:year]
      @date = '01-'+@month+'-'+@year
      @start_date = @date.to_date
      if @month == @today.month.to_s
        @end_date = @local_tzone_time.to_date
      else
        @end_date = @start_date.end_of_month
      end
      @emp_attendance = CardAttendance.all(:select=>'max(time) as maxtime,min(time) as mintime,date',:conditions=>{:profile_id=>params[:employee_id],:date => @start_date..@end_date,:type=>1},:order=>"date ASC",:group=>:date)
    end
    render    :pdf => "card_attendance_pdf",
              :orientation => 'Portrait',
              :margin => {    :top=> 10,
              :bottom => 10,
              :left=> 10,
              :right => 10},
              :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
              :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}} 
   
  end
  
  def card_attendance_pdf_details
    require "yaml"
    unless params[:summary].nil? or params[:summary].empty? or params[:summary].blank?
      @summary = params[:summary].to_i
      if @summary == 1
        orientation = "Portrait"
        top = 10
        bottom = 12
        @date_today = @local_tzone_time.to_date
        
        unless params[:report_date_from].nil? or params[:report_date_from].empty? or params[:report_date_from].blank?
          @report_date_from = params[:report_date_from]
          @report_date_to = params[:report_date_to]

          @report_date_from = @report_date_from.to_date.strftime("%Y-%m-%d")
          @report_date = @report_date_from.to_date.strftime("%B %Y")
          
          order_str = "employee_positions.order_by asc"
          
          @employees = Employee.find(:all, :select => "id,user_id, concat(  first_name,' ', last_name )  as employee_info, employee_department_id, employee_position_id, '' as in_time, '' as out_time, '' as stat", :include => :employee_position, :order=>""  + order_str)
          
          employess_id = @employees.map(&:user_id)
          data = []
          employee_department_ids = []
          department_names = []
          unless @employees.nil? or @employees.empty?

            @cardAttendances = CardAttendance.find(:all, :select=>'user_id, count( DISTINCT date ) as count_present',:conditions=>"date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and type = 1 and user_id in (" + employess_id.join(",") + ")", :group => "user_id")
            
            k = 0;
            m = 0

            @employees.each do |employee|
              unless employee_department_ids.include?(employee.employee_department_id)
                dept = EmployeeDepartment.find employee.employee_department_id
                employee_department_ids[m] = employee.employee_department_id
                department_names[m] = dept.name
                dept_name = dept.name
                m += 1
              else
                t = employee_department_ids.index(employee.employee_department_id)
                dept_name = department_names[t]
              end
              emp_id = employee.user_id
              cardAttendance = @cardAttendances.select{ |s| s.user_id == employee.user_id}

              if cardAttendance.nil? or cardAttendance.empty? or cardAttendance.blank?  
                  total_present = ' - '
                  total_absent = ' - '
                  total_late = ' - '
                  total_leave = ' - '
              else 
                is_late = ' - '
                @employee_setting = EmployeeSetting.find_by_employee_id(employee.id)

                unless @employee_setting.blank?
                    in_ofc_time = @employee_setting.start_time.strftime("%H:%M:%S")
                    lateAttendances = CardAttendance.all(:select=>'user_id',:conditions=>"date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and type = 1 and user_id = " + employee.user_id.to_s + "", :group => "date", :having => "min( time ) > '" + in_ofc_time + "'")
                    total_late = lateAttendances.length
                end
                total_present = cardAttendance[0].count_present
                leave_n_absent_count = EmployeeAttendance.find(:all, :conditions=>"attendance_date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and employee_id = " + employee.id.to_s).size

                leave_types = EmployeeLeaveType.find(:all, :conditions => "status = true")
                leave_count = EmployeeLeave.find_all_by_employee_id(employee,:joins=>:employee_leave_type,:conditions=>"status = true")
                total_leaves = 0
                leave_types.each do |lt|
                  leave_count = EmployeeAttendance.find(:all, :conditions=>"attendance_date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and employee_id = " + employee.id.to_s + " AND employee_leave_type_id = " + lt.id.to_s).size
                  total_leaves = total_leaves + leave_count
                end
                total_leave = total_leaves
                total_absent = leave_n_absent_count.to_i - total_leave.to_i
                if total_absent.to_i  < 0
                  total_absent = 0
                end
              end
              @emp = Employee.find(employee.id)
              position = EmployeePosition.find(@emp.employee_position_id)
              employee_image = "<img src='/images/HR/default_employee.png' width='100px' />"
              if @emp.photo.file?
                employee_image = "<img src='"+@emp.photo.url+"' width='100px' />"
              end
              if !employee.blank?
                unless cardAttendance.nil? or cardAttendance.empty? or cardAttendance.blank?  
                  emp = []
                  emp[0] = @emp.first_name + ' ' + @emp.middle_name + ' ' + @emp.last_name
                  emp[1] = position.name
                  emp[2] = dept_name
                  emp[3] = total_present
                  emp[4] = total_absent
                  emp[5] = total_late
                  emp[6] = total_leave 
                  emp[7] = @emp.employee_department_id
                  data[k] = emp
                  k += 1
                end
              end
            end
          end
          @employee_attendance = data


        else
          data = []
          @employee_attendance = data
        end
      else
        orientation = "Landscape"
        top = 5
        bottom = 18
        @date_today = @local_tzone_time.to_date
        
        unless params[:report_date_from].nil? or params[:report_date_from].empty? or params[:report_date_from].blank?
          @report_date_from = params[:report_date_from]
          @report_date_to = params[:report_date_to]

          @report_date_from = @report_date_from.to_date.strftime("%Y-%m-%d")
          @report_date = @report_date_from.to_date.strftime("%B %Y")
          
          order_str = "employee_positions.order_by asc"
          
          @employees = Employee.find(:all, :select => "id,user_id, concat(  first_name,' ', last_name )  as employee_info, employee_department_id, employee_position_id, '' as in_time, '' as out_time, '' as stat", :include => :employee_position, :order=>""  + order_str)
          
          employess_id = @employees.map(&:user_id)
          data = []
          employee_department_ids = []
          department_names = []
          unless @employees.nil? or @employees.empty?

            @cardAttendances = CardAttendance.find(:all, :select=>'user_id, date, min( time ) as min_time, max(time) as max_time',:conditions=>"date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and type = 1 and user_id in (" + employess_id.join(",") + ")", :group => "date, user_id", :order => 'date asc')
            
            k = 0;
            m = 0
            
            @employees.each do |employee|
              unless employee_department_ids.include?(employee.employee_department_id)
                dept = EmployeeDepartment.find employee.employee_department_id
                employee_department_ids[m] = employee.employee_department_id
                department_names[m] = dept.name
                dept_name = dept.name
                m += 1
              else
                t = employee_department_ids.index(employee.employee_department_id)
                dept_name = department_names[t]
              end
              emp_id = employee.user_id
              cardAttendance = @cardAttendances.select{ |s| s.user_id == employee.user_id}

              if cardAttendance.nil? or cardAttendance.empty? or cardAttendance.blank?  
                  in_time = ' - '
                  out_time = ' - '
                  late = ' - '
                  late_time = ' - '
                  absent = ' - '
                  leave = ' - '
                  leave_for = ' - '
              else 
                @employee_setting = EmployeeSetting.find_by_employee_id(employee.id)
                (@report_date_from.to_date..@report_date_to.to_date).each do |d|
                  dt = d.strftime("%Y-%m-%d")
                  dtCardAttendance = @cardAttendances.select{ |s| s.user_id == employee.user_id && s.date == dt.to_date}
                  
                  unless dtCardAttendance.nil? or dtCardAttendance.empty? or dtCardAttendance.blank?
                    unless @employee_setting.blank?
                      in_ofc_time = @employee_setting.start_time 
                      ofc_time = Time.parse(dtCardAttendance[0].min_time)
                      in_offc_time = @employee_setting.start_time.to_time.strftime("%H%M").to_i
                      offc_time = ofc_time.strftime("%H%M").to_i
                      if offc_time > in_offc_time
                        late = 'yes'
                        lt_ofc_time = @date_today.to_date.strftime("%Y-%m-%d " + @employee_setting.start_time.to_time.strftime("%H:%M:%S"))
                        lt_ofc_in_time = @date_today.to_date.strftime("%Y-%m-%d " + ofc_time.strftime("%H:%M:%S"))
                        late_time_i = (( lt_ofc_in_time.to_time - lt_ofc_time.to_time ) / 60).to_i
                        late_time = late_time_i.to_s + " Minutes"
                      end
                      in_time = dtCardAttendance[0].min_time
                      out_time = dtCardAttendance[0].max_time
                      #if cardAttendance[0]['time'].to_time.strftime("%H%M").to_i > @employee_setting.start_time.strftime("%H%M").to_i
                    end
                  else
                    in_time = ' - '
                    out_time = ' - '
                    late = ' - '
                    late_time = ' - '
                    
                    emp_attendance = EmployeeAttendance.find(:first, :conditions=>"attendance_date = '" + dt + "' and employee_id = " + employee.id.to_s + "")
                    unless emp_attendance.nil? or emp_attendance.blank?
                      unless  emp_attendance.employee_leave_type_id.nil? or emp_attendance.employee_leave_type_id.empty? or emp_attendance.employee_leave_type_id.blank? 
                        leave_types = EmployeeLeaveType.find(emp_attendance.employee_leave_type_id)
                        absent = '-'
                        leave = 'yes'
                        leave_for = leave_types.name
                      else  
                        absent = 'yes'
                        leave = ' - '
                        leave_for = ' - '
                      end
                    else
                      absent = ' - '
                      leave = ' - '
                      leave_for = ' - '
                    end
                  end
                  @emp = Employee.find(employee.id)
                  position = EmployeePosition.find(@emp.employee_position_id)
                  unless cardAttendance.nil? or cardAttendance.empty? or cardAttendance.blank?  
                    emp = []
                    emp[0] = @emp.first_name + ' ' + @emp.middle_name + ' ' + @emp.last_name
                    emp[1] = @emp.id
                    emp[2] = dt
                    emp[3] = position.name
                    emp[4] = dept_name
                    emp[5] = in_time
                    emp[6] = out_time
                    emp[7] = late
                    emp[8] = late_time 
                    emp[9] = absent
                    emp[10] = leave
                    emp[11] = leave_for
                    data[k] = emp
                    k += 1
                  end
                end
                
              end
            end
          end
          @employee_attendance = data


        else
          data = []
          @employee_attendance = data
        end
      end
    end
    adv_attendance_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/adv_attendance_report.yml")['school']
    
    @all_groups = adv_attendance_config['groups_' + MultiSchool.current_school.id.to_s].split(",")
    render    :pdf => "card_attendance_pdf_details",
              :orientation => orientation,
              :page_size => 'Legal',
              :zoom => 1.4,
              :margin => {    :top=> top,
              :bottom => bottom,
              :left=> 10,
              :right => 10},
              :header => {:html => { :template=> 'layouts/report/card_attendance_header_' + MultiSchool.current_school.code.to_s + '.html'}},
              :footer => {:html => { :template=> 'layouts/report/card_attendance_footer_' + MultiSchool.current_school.code.to_s + '.html'}} 
   
  end
  
  def report_card_generate
    if !params[:month].blank? and !params[:year].blank? and !params[:employee_id].blank?
      @employee = Employee.find(params[:employee_id])
      @today = @local_tzone_time.to_date
      @month = params[:month]
      @year = params[:year]
      @date = '01-'+@month+'-'+@year
      @start_date = @date.to_date
      if @month == @today.month.to_s
        @end_date = @local_tzone_time.to_date
      else
        @end_date = @start_date.end_of_month
      end
      @emp_attendance = CardAttendance.all(:select=>'max(time) as maxtime,min(time) as mintime,date',:conditions=>{:profile_id=>params[:employee_id],:date => @start_date..@end_date,:type=>1},:order=>"date ASC",:group=>:date)
    end
    render :update do |page|
        page.replace_html 'report_card_generate', :partial => 'report_card_generate'
    end
  end
  
  def card_report
    @employee = Employee.all
  end

  def report
    @departments = EmployeeDepartment.find(:all, :conditions => "status = true", :order=> "name ASC")
  end

  def update_attendance_report
    @leave_types = EmployeeLeaveType.find(:all, :conditions => "status = true")
    if params[:department_id] == ""
      render :update do |page|
        page.replace_html "attendance_report", :text => ""
      end
      return
    end
    unless (params[:department_id] == "All Departments")
      @employees = Employee.find_all_by_employee_department_id(params[:department_id])
    else
      @employees = Employee.all
    end
    render :update do |page|
      page.replace_html "attendance_report", :partial => 'attendance_report'
    end
  end

  def report_pdf
    @leave_types = EmployeeLeaveType.find(:all, :conditions => "status = true", :order => "name ASC")
    @employee_leave = EmployeeLeave.all
    unless (params[:department]== "All Departments")
      @employees = Employee.find_all_by_employee_department_id(params[:department])
      @department_name = EmployeeDepartment.find_by_id(params[:department]).name
    else
      @employees = Employee.all
      @department_name = t('all_departments')
    end
    render :pdf => 'report_pdf'#, :show_as_html => true
  end

  def emp_attendance
    @employee = Employee.find(params[:id])
    @attendance_report = EmployeeAttendance.find_all_by_employee_id(@employee.id)
    @leave_types = EmployeeLeaveType.find(:all, :conditions => "status = true")
    @leave_count = EmployeeLeave.find_all_by_employee_id(@employee,:joins=>:employee_leave_type,:conditions=>"status = true")
    @total_leaves = 0
    @leave_types.each do |lt|
      leave_count = EmployeeAttendance.find_all_by_employee_id_and_employee_leave_type_id(@employee.id,lt.id).size
      @total_leaves = @total_leaves +leave_count
    end
  end

  def leave_history
    @employee = Employee.find(params[:id])
    
    render :partial => 'leave_history'
  end

  def update_leave_history
    @employee = Employee.find(params[:id])
    @start_date = (params[:period][:start_date])
    @end_date = (params[:period][:end_date])
    @error=true if @end_date < @start_date
    @leave_types = EmployeeLeaveType.find(:all, :conditions => "status = true")
    @employee_attendances = {}
    @leave_types.each do |lt|
      @employee_attendances[lt.name] = EmployeeAttendance.find_all_by_employee_id_and_employee_leave_type_id(@employee.id,lt.id,:conditions=> "attendance_date between '#{@start_date.to_date}' and '#{@end_date.to_date}'")
    end
    render :update do |page|
      page.replace_html "attendance-report", :partial => 'update_leave_history'
    end
  end
  
  def leaves
    @leave_types = EmployeeLeaveType.find(:all, :conditions=>"status = true")
    @employee = Employee.find(params[:id])
    @reporting_employees = Employee.find_all_by_reporting_manager_id(@employee.user_id)
    
    reporting_manager = @employee.reporting_manager_id
   
 
    @total_leave_count = 0
    @reporting_employees.each do |e|
      @app_leaves = ApplyLeave.count(:conditions=>["employee_id =? AND (viewed_by_manager is NULL or viewed_by_manager =?)", e.id, false])
      @total_leave_count = @total_leave_count + @app_leaves
    end
    
    @total_leave_employee = @total_leave_count
    
    @config = Configuration.find_by_config_key('LeaveSectionManager')
    if (@config.blank? or @config.config_value.blank? or @config.config_value.to_i != 1)
      @app_leaves = ApplyLeaveStudent.count(:conditions=>["viewed_by_teacher is NULL or viewed_by_teacher=?",false])
      @total_leave_count = @total_leave_count + @app_leaves
    elsif @current_user.employee_record.meeting_forwarder.to_i == 1

      @employee_batches = @current_user.employee_record.batches
      batch_ids = @employee_batches.map(&:id)
      @app_leaves = ApplyLeaveStudent.count(:conditions=>["(viewed_by_teacher is NULL or viewed_by_teacher=?) AND students.batch_id in (?) and forward = ?",false,batch_ids,false],:include=>[:student])
      @total_leave_count = @total_leave_count + @app_leaves
    end 
    
    
    @leave_apply = ApplyLeave.new(params[:leave_apply])
    if request.post?
      if(@leave_apply.start_date.to_date != @leave_apply.end_date.to_date and @leave_apply.is_half_day == true)
        @leave_apply.errors.add_to_base("#{t('half_day_not_possible')}") and return
      end
      applied_dates = (@leave_apply.start_date..@leave_apply.end_date).to_a.uniq
      detect_overlaps = ApplyLeave.find(:all, :conditions => ["employee_id = ? AND (start_date IN (?) OR end_date IN (?))",@employee.id, applied_dates, applied_dates])
      if detect_overlaps.present? and detect_overlaps.map(&:approved).include? true
        @leave_apply.errors.add_to_base("#{t('range_conflict')}") and return
      end
      leaves_half_day = ApplyLeave.count(:all,:conditions=>{:employee_id=>params[:leave_apply][:employee_id],:start_date=>params[:leave_apply][:start_date],:end_date=>params[:leave_apply][:end_date],:is_half_day=>true})
      leaves = ApplyLeave.count(:all,:conditions=>{:approved => true, :employee_id=>params[:leave_apply][:employee_id],:start_date=>params[:leave_apply][:start_date],:end_date=>params[:leave_apply][:end_date]})
      already_apply = ApplyLeave.count(:all,:conditions=>{:approved => nil, :employee_id=>params[:leave_apply][:employee_id],:start_date=>params[:leave_apply][:start_date],:end_date=>params[:leave_apply][:end_date]})
      
      
      
      if(leaves == 0 and already_apply == 0) or (leaves <= 1 and leaves_half_day < 2)
        unless leaves_half_day == 1 and params[:leave_apply][:is_half_day]=='0'
          if @leave_apply.save
            ApplyLeave.update(@leave_apply, :approved=> nil, :viewed_by_manager=> false)
            
            unless reporting_manager.nil?
              Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
              :recipient_ids => reporting_manager,
              :subject=>"#{t('employee_leave_notice')}",
              :rtype=>7,
              :rid=>@leave_apply.id,
              :body=>""+@employee.first_name+" apply for leave from "+params[:leave_apply][:start_date]+" to "+params[:leave_apply][:end_date] ))
            end 
            flash[:notice]= "#{t('flash5')}"
            redirect_to :controller => "employee_attendance", :action=> "leaves", :id=>@employee.id
          end
        else
          @leave_apply.errors.add_to_base("#{t('half_day_alredy_applied')}")
        end
      else
        @leave_apply.errors.add_to_base("#{t('already_applied')}")
      end
    end
  end
  
  def leave_application
    @target = params[:target]
    @request_from = params[:request_from]
    if @target == "employee"
      @applied_leave = ApplyLeave.find(params[:id])
      @applied_employee = Employee.find(@applied_leave.employee_id)
      @leave_type = EmployeeLeaveType.find(@applied_leave.employee_leave_types_id)
      @manager = @applied_employee.reporting_manager_id
      @leave_count = EmployeeLeave.find_by_employee_id(@applied_employee.id,:conditions=> "employee_leave_type_id = '#{@leave_type.id}'")
    else
      @applied_leave = ApplyLeaveStudent.find(params[:id])
      @applied_employee = Student.find(@applied_leave.student_id)
      @leave_type = ""
      @manager = ""
      @leave_count = ApplyLeaveStudent.count(:conditions=>["student_id =? AND approved =?", @applied_leave.student_id, true])
    end  
  end

  def leave_app
    @employee = Employee.find(params[:id2])
    @applied_leave = ApplyLeave.find(params[:id])
    @leave_type = EmployeeLeaveType.find(@applied_leave.employee_leave_types_id)
    @applied_employee = Employee.find(@applied_leave.employee_id)
    @manager = @applied_employee.reporting_manager_id
  end
  
  def forward_to_admin
   @applied_leave = ApplyLeaveStudent.find(params[:id])
   render :action => "forward_to_admin_student.rjs"
  end
  

  def approve_remarks
    @target = params[:target]
    @request_from = params[:request_from]
    if @target == "student"
      @applied_leave = ApplyLeaveStudent.find(params[:id])
    else
      @applied_leave = ApplyLeave.find(params[:id])
    end  
    if params[:target] == "student"
      render :action => "approve_remarks_student.rjs"
    else
      render :action => "approve_remarks.rjs"
    end
  end

  def deny_remarks
    @target = params[:target]
    @request_from = params[:request_from]
    if @target == "student"
      @applied_leave = ApplyLeaveStudent.find(params[:id])
    else
      @applied_leave = ApplyLeave.find(params[:id])
    end  
    if params[:target] == "student"
      render :action => "deny_remarks_student.rjs"
    else
      render :action => "deny_remarks.rjs"
    end
  end
  
  def forward_leave
    @applied_leave = ApplyLeaveStudent.find(params[:applied_leave])
    @applied_student = Student.find(@applied_leave.student_id)
    @applied_leave.update_attributes(:forward =>true,:forwarder_remark => params[:forwarder_remark])
    @approving_teacher = Employee.find_by_user_id(current_user.id) 
    reminderrecipients = User.find(:all,:conditions=>["admin = ? and is_deleted = ?",true,false]).map(&:id)
    unless reminderrecipients.nil?
        Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
        :recipient_ids => reminderrecipients,
        :subject=>"A leave apllication need your action",
        :rtype=>9,
        :rid=>@applied_leave.id,
        :student_id => 0,
        :batch_id => 0,
        :body=>"A Leave Apllication from (#{@applied_student.first_name}) is need your action" ))
    end 
    
    flash[:notice]="Leave Application Forwarded"
    
    redirect_to :controller=>"employee_attendance", :action=>"leaves", :id=>@approving_teacher.id and return
  end

  def approve_leave
    @request_from = params[:request_from]
    if params[:target] == "student"
      @applied_leave = ApplyLeaveStudent.find(params[:applied_leave])
      @applied_student = Student.find(@applied_leave.student_id)
      @approving_teacher = Employee.find_by_user_id(current_user.id) 
      start_date = @applied_leave.start_date
      end_date = @applied_leave.end_date

      leave_text = ""
      unless @applied_leave.leave_subject.blank?
        leave_text = @applied_leave.leave_subject
      else
        leave_text = @applied_leave.reason
      end  
      
      (start_date..end_date).each do |d|
        emp_attendance = Attendance.find_by_student_id_and_month_date(@applied_student.id, d)
        unless emp_attendance.present?
          att = Attendance.new(:afternoon => 1,:forenoon => 1, :is_leave => 1, :month_date=> d, :student_id=>@applied_student.id, :batch_id=>@applied_student.batch.id, :reason => leave_text)
          if att.save

          end
        else
          emp_attendance.update_attribute('is_leave',1);
          emp_attendance.update_attribute('afternoon',1);
          emp_attendance.update_attribute('forenoon',1);
          emp_attendance.update_attribute('reason',leave_text);
        end
      end
      
      @applied_leave.update_attributes(:leave_subject => leave_text, :approved => true, :teacher_remark => params[:manager_remark],:viewed_by_teacher => true, :approving_teacher => @approving_teacher.id)
      
      reminderrecipients = []
      batch_ids = {}
      student_ids = {}
      
      #EDITED FOR MULTIPLE GUARDIAN
      unless @applied_student.student_guardian.empty?
        guardians = @applied_student.student_guardian
        guardians.each do |guardian|

          unless guardian.user_id.nil?
            reminderrecipients.push guardian.user_id
            batch_ids[guardian.user_id] = @applied_student.batch_id
            student_ids[guardian.user_id] = @applied_student.id
          end
        end  
      end
      
      #reminderrecipients.push @applied_student.user_id
#      unless @applied_student.immediate_contact_id.nil?
#          guardian = Guardian.find(@applied_student.immediate_contact_id)
#          unless guardian.user_id.nil?
#            reminderrecipients.push guardian.user_id
#            batch_ids[guardian.user_id] = @applied_student.batch_id
#            student_ids[guardian.user_id] = @applied_student.id
#            
#          end
#      end
      
      unless reminderrecipients.nil?
        Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
        :recipient_ids => reminderrecipients,
        :subject=>"#{t('your_leave_approved')} (#{@applied_student.first_name})",
        :rtype=>10,
        :rid=>@applied_leave.id,
        :student_id => student_ids,
        :batch_id => batch_ids,
        :body=>"Your (#{@applied_student.first_name}) leave application from #{@applied_leave.start_date} to #{@applied_leave.end_date} is Approved" ))
      end 
      
      flash[:notice]="#{t('flash6')} #{@applied_student.first_name} from #{@applied_leave.start_date} to #{@applied_leave.end_date}"
      if @request_from == "admin"
        redirect_to :controller=>"employee", :action=>"employee_attendance" and return
      else
        redirect_to :controller=>"employee_attendance", :action=>"leaves", :id=>@approving_teacher.id and return
      end
    else
      @applied_leave = ApplyLeave.find(params[:applied_leave])
      @applied_employee = Employee.find(@applied_leave.employee_id)
      @employee_leave = EmployeeLeave.find_by_employee_id_and_employee_leave_type_id(@applied_employee.id,@applied_leave.employee_leave_types_id)
      @manager = @applied_employee.reporting_manager_id    
      start_date = @applied_leave.start_date
      end_date = @applied_leave.end_date
      reset_date = @employee_leave.reset_date || @applied_employee.joining_date - 1.day
      @leave_count = 0
      (start_date..end_date).each do |date|
        @leave_count += @applied_leave.is_half_day == true ? 0.5 : 1.0
      end
      if @employee_leave.leave_count >= (@employee_leave.leave_taken + @leave_count)
        if start_date >= reset_date.to_date
          (start_date..end_date).each do |d|
            emp_attendance = EmployeeAttendance.find_by_employee_id_and_attendance_date(@applied_employee.id, d)
            unless emp_attendance.present?
              att = EmployeeAttendance.new(:attendance_date=> d, :employee_id=>@applied_employee.id,:employee_leave_type_id=>@applied_leave.employee_leave_types_id, :reason => @applied_leave.reason, :is_half_day => @applied_leave.is_half_day)
              if att.save
                att.update_attributes(:is_half_day => @applied_leave.is_half_day)
                @reset_count = EmployeeLeave.find_by_employee_id(@applied_leave.employee_id, :conditions=> "employee_leave_type_id = '#{@applied_leave.employee_leave_types_id}'")
                leave_taken = @reset_count.leave_taken
                if @applied_leave.is_half_day
                  leave_taken += 0.5
                  @reset_count.update_attributes(:leave_taken=> leave_taken)
                else
                  leave_taken += 1
                  @reset_count.update_attributes(:leave_taken=> leave_taken)
                end
              end
            else
              already_half_day = emp_attendance.is_half_day
              emp_attendance.update_attributes(:is_half_day => false)
              @reset_count = EmployeeLeave.find_by_employee_id(@applied_leave.employee_id, :conditions=> "employee_leave_type_id = '#{@applied_leave.employee_leave_types_id}'")
              leave_taken = @reset_count.leave_taken
              if already_half_day == true
                if @applied_leave.is_half_day
                  leave_taken += 0.5
                  @reset_count.update_attributes(:leave_taken=> leave_taken)
                else
                  leave_taken += 0.5
                  @reset_count.update_attributes(:leave_taken=> leave_taken)
                end
              end
            end
          end
          @applied_leave.update_attributes(:approved => true, :manager_remark => params[:manager_remark],:viewed_by_manager => true, :approving_manager => current_user.id)
          
            unless @applied_employee.nil?
              Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
              :recipient_ids => @applied_employee.user_id,
              :subject=>"#{t('your_leave_approved')}",
              :rtype=>8,
              :rid=>@applied_leave.id,
              :body=>"Your leave application from #{@applied_leave.start_date} to #{@applied_leave.end_date} is Approved" ))
            end 
          
          flash[:notice]="#{t('flash6')} #{@applied_employee.first_name} from #{@applied_leave.start_date} to #{@applied_leave.end_date}"
          if @request_from == "admin"
            redirect_to :controller=>"employee", :action=>"employee_attendance" and return
          else
            redirect_to :controller=>"employee_attendance", :action=>"leaves", :id=>@applied_employee.reporting_manager.employee_record.id and return
          end  
        else
          flash[:notice] = "#{t('the_application_contains_dates_which_are_earlier_than_reset_date')}"
          if @request_from == "admin"
            redirect_to :controller=>"employee", :action=>"employee_attendance" and return
          else
            redirect_to :controller=>"employee_attendance", :action=>"leaves", :id=>@applied_employee.reporting_manager.employee_record.id and return
          end  
        end
      else
        flash[:notice] = "#{t('total_amount_of_leave_exceeded')}"
        if @request_from == "admin"
            redirect_to :controller=>"employee", :action=>"employee_attendance" and return
        else
            redirect_to :controller => "employee_attendance", :action => "leave_application", :id => @applied_leave.id and return
        end    
      end
    end 
  end

  def deny_leave
    @request_from = params[:request_from]
    if params[:target] == "student"
      @applied_leave = ApplyLeaveStudent.find(params[:applied_leave])
      start_date = @applied_leave.start_date
      end_date = @applied_leave.end_date
      @applied_student = Student.find(@applied_leave.student_id)
      @approving_teacher = Employee.find_by_user_id(current_user.id) 
      
      leave_text = ""
      unless @applied_leave.leave_subject.blank?
        leave_text = @applied_leave.leave_subject
      else
        leave_text = @applied_leave.reason
      end
      
      (start_date..end_date).each do |d|
        emp_attendance = Attendance.find_by_student_id_and_month_date(@applied_student.id, d)
        if emp_attendance.present?
          emp_attendance.update_attribute('is_leave',0);
          emp_attendance.update_attribute('afternoon',1);
          emp_attendance.update_attribute('forenoon',1);
          emp_attendance.update_attribute('reason',leave_text);
        end
      end
      
      @applied_leave.update_attributes(:leave_subject => leave_text, :approved => false,:viewed_by_teacher => true, :teacher_remark =>params[:manager_remark], :approving_teacher => current_user.id)

      reminderrecipients = []
      batch_ids = {}
      student_ids = {}
      
      #EDITED FOR MULTIPLE GUARDIAN
      unless @applied_student.student_guardian.empty?
        guardians = @applied_student.student_guardian
        guardians.each do |guardian|

          unless guardian.user_id.nil?
            reminderrecipients.push guardian.user_id
            batch_ids[guardian.user_id] = @applied_student.batch_id
            student_ids[guardian.user_id] = @applied_student.id
          end
        end  
      end
      
      #reminderrecipients.push @applied_student.user_id
#      unless @applied_student.immediate_contact_id.nil?
#          guardian = Guardian.find(@applied_student.immediate_contact_id)
#          unless guardian.user_id.nil?
#            reminderrecipients.push guardian.user_id
#            batch_ids[guardian.user_id] = @applied_student.batch_id
#            student_ids[guardian.user_id] = @applied_student.id
#          end
#      end
      unless reminderrecipients.nil?
        Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
        :recipient_ids => reminderrecipients,
        :subject=>"#{t('your_leave_denied')} (#{@applied_student.first_name})",
        :rtype=>10,
        :rid=>@applied_leave.id,
        :student_id => student_ids,
        :batch_id => batch_ids,
        :body=>"Your (#{@applied_student.first_name}) leave application from #{@applied_leave.start_date} to #{@applied_leave.end_date} is Denied" ))
      end 
      
      flash[:notice]="#{t('flash7')} #{@applied_student.first_name} from #{@applied_leave.start_date} to #{@applied_leave.end_date}"
      if @request_from == "admin"
         redirect_to :controller=>"employee", :action=>"employee_attendance" and return
      else
         redirect_to :action=>"leaves", :id=>@approving_teacher.id
      end   
    else
      @applied_leave = ApplyLeave.find(params[:applied_leave])
      start_date = @applied_leave.start_date
      end_date = @applied_leave.end_date
      @applied_employee = Employee.find(@applied_leave.employee_id)
      @employee_leave = EmployeeLeave.find_by_employee_id_and_employee_leave_type_id(@applied_employee.id,@applied_leave.employee_leave_types_id)
      @manager = @applied_employee.reporting_manager_id
      @employee_attendances = EmployeeAttendance.find(:all, :conditions => ["((attendance_date = ?) OR (attendance_date = ?) or (attendance_date BETWEEN ? and ?)) AND employee_id = ?",start_date,end_date,start_date,end_date,@applied_employee.id])
      @employee_half_day_attendances = EmployeeAttendance.count(:all, :conditions => ["((attendance_date = ?) OR (attendance_date = ?) or (attendance_date BETWEEN ? and ?)) AND (is_half_day = ? AND employee_id = ?)",start_date,end_date,start_date,end_date,true, @applied_employee.id])
      count = @employee_attendances.count.to_f - (@employee_half_day_attendances.to_f / 2)
      @employee_attendances.each do |attendance|
        if attendance.created_at < @employee_leave.reset_date
          update_value = @applied_leave.is_half_day == true ? 0.5 : 1.0
          @employee_leave.update_attributes(:leave_count => @employee_leave.leave_count + update_value)
        else
          update_value = @applied_leave.is_half_day == true ? 0.5 : 1.0
          @employee_leave.update_attributes(:leave_taken => @employee_leave.leave_taken - update_value)
        end
      end
      @employee_attendances.each do |employee_attendance|
        if @applied_leave.is_half_day == true and employee_attendance.is_half_day == true
          employee_attendance.destroy
        elsif @applied_leave.is_half_day == true and employee_attendance.is_half_day == false
          employee_attendance.update_attributes(:is_half_day => true)
        elsif @applied_leave.is_half_day == false and employee_attendance.is_half_day == false
          employee_attendance.destroy
        elsif @applied_leave.is_half_day == false and employee_attendance.is_half_day == true
          employee_attendance.destroy
        end
      end
      @applied_leave.update_attributes(:approved => false,:viewed_by_manager => true, :manager_remark =>params[:manager_remark], :approving_manager => current_user.id)
     
      
      unless @applied_employee.nil?
          Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
          :recipient_ids => @applied_employee.user_id,
          :subject=>"#{t('your_leave_denied')}",
          :rtype=>8,
          :rid=>@applied_leave.id,
          :body=>"Your leave application from #{@applied_leave.start_date} to #{@applied_leave.end_date} is Denied" ))
      end
      
      flash[:notice]="#{t('flash7')} #{@applied_employee.first_name} from #{@applied_leave.start_date} to #{@applied_leave.end_date}"
      if @request_from == "admin"
         redirect_to :controller=>"employee", :action=>"employee_attendance" and return
      else
         redirect_to :action=>"leaves", :id=>@applied_employee.reporting_manager.employee_record.id
      end   
    end  
  end

  def cancel
    render :text=>""
  end

  def new_leave_applications
    @target = params[:target]
    @employee = Employee.find(params[:id])
    @reporting_employees = Employee.find_all_by_reporting_manager_id(@employee.user_id)
    if @target == "no-popup"
      render "new_leave_applications_admin"
    else
      render :partial => "new_leave_applications"
    end
  end
  
  def all_employee_new_leave_applications
    @employee = Employee.find(params[:id])
    @all_employees = Employee.find(:all)
    render :partial => "all_employee_new_leave_applications"
  end

  def all_leave_applications
    @employee = Employee.find(params[:id])
    @reporting_employees = Employee.find_all_by_reporting_manager_id(@employee.user_id)
    render :partial => "all_leave_applications"
  end

  def individual_leave_applications
    @employee = Employee.find(params[:id])
    @pending_applied_leaves = ApplyLeave.find_all_by_employee_id(@employee.id, :conditions=> "approved = false AND viewed_by_manager = false", :order=>"start_date DESC")
    @applied_leaves = ApplyLeave.paginate(:page => params[:page],:per_page=>10 , :conditions=>[ "employee_id = '#{@employee.id}'"], :order=>"start_date DESC")
    render :partial => "individual_leave_applications"
  end

  def own_leave_application
    @applied_leave = ApplyLeave.find(params[:id])
    @leave_type = EmployeeLeaveType.find(@applied_leave.employee_leave_types_id)
    @employee = Employee.find(@applied_leave.employee_id)
  end

  def cancel_application
    @applied_leave = ApplyLeave.find(params[:id])
    @employee = Employee.find(@applied_leave.employee_id)
    unless @applied_leave.viewed_by_manager
      ApplyLeave.destroy(params[:id])
      flash[:notice] = "#{t('flash8')}"
    else
      flash[:notice] = "#{t('flash10')}"
    end
    redirect_to :action=>"leaves", :id=>@employee.id
  end

  def update_all_application_view
    if params[:employee_id] == ""
      render :update do |page|
        page.replace_html "all-application-view", :text => ""
      end
      return
    end
    @employee = Employee.find(params[:employee_id])

    @all_pending_applied_leaves = ApplyLeave.find_all_by_employee_id(params[:employee_id], :conditions=> "approved = false AND viewed_by_manager = false", :order=>"start_date DESC")
    @all_applied_leaves = ApplyLeave.paginate(:page => params[:page], :per_page=>10, :conditions=> ["employee_id = '#{@employee.id}'"], :order=>"start_date DESC")
    render :update do |page|
      page.replace_html "all-application-view", :partial => "all_leave_application_lists"
    end
  end

  #PDF Methods

  def employee_attendance_pdf
    @employee = Employee.find(params[:id])
    @attendance_report = EmployeeAttendance.find_all_by_employee_id(@employee.id)
    @leave_types = EmployeeLeaveType.find(:all, :conditions => "status = true")
    @leave_count = EmployeeLeave.find_all_by_employee_id(@employee,:joins=>:employee_leave_type,:conditions=>"status = true")
    @total_leaves = 0
    @leave_types.each do |lt|
      leave_count = EmployeeAttendance.find_all_by_employee_id_and_employee_leave_type_id(@employee.id,lt.id).size
      @total_leaves = @total_leaves + leave_count
    end
    render :pdf => 'employee_attendance_pdf'
          

    #        respond_to do |format|
    #            format.pdf { render :layout => false }
    #        end
  end
end
