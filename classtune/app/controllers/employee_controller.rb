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

class EmployeeController < ApplicationController
  before_filter :login_required,:configuration_settings_for_hr
  filter_access_to :all
  before_filter :set_precision
  before_filter :check_permission, :only => [:index,:profile,:settings,:hr,:employee_attendance,:payslip,:search,:department_payslip]
  before_filter :only_allowed_when_parmitted, :only => [:edit_employee_own]

  before_filter :protect_other_employee_data, :only => [:individual_payslip_pdf,:timetable,:timetable_pdf,:profile_payroll_details,\
      :view_payslip ]
  before_filter :limit_employee_profile_access , :only => [:profile,:profile_pdf]

  def late_employee
    @today = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d')
    @employee = Employee.all
    @emp_data = []    
    @employee.each do |emp|
      @emp_set = EmployeeSetting.find_by_employee_id(emp.id)
      unless @emp_set.blank?
        @emp_attendance = CardAttendance.find(:first,:select=>'max(time) as maxtime,min(time) as mintime,date',:conditions=>{:profile_id=>emp.id,:date => @today,:type=>1})
        if @emp_attendance.blank?
          
        elsif @emp_attendance.mintime > @emp_set.start_time
          @emp_data << emp
        end  
      end      
    end 
    render :layout => false
  end
  def employee_settings
    @employee = Employee.find(params[:id])
    @employee_settings = EmployeeSetting.find_by_employee_id(params[:id])
    render :partial => "employee_settings"
  end
  
  def employee_add_attendance
    @employee = Employee.find(params[:id])
    render :partial => "employee_add_attendance"
  end
  
  def employee_create_attendance
    @employee = Employee.find(params[:id])
    @c_att = CardAttendance.new
    @c_att.time = params[:card_attendnace][:entry_time].to_time.strftime("%H:%M")
    @c_att.date = params[:card_attendnace][:entry_time].to_time.strftime('%Y-%m-%d')
    @c_att.type = 1
    @c_att.type_data = 1
    @c_att.profile_id = @employee.id
    @c_att.user_id = @employee.user_id
    if @c_att.save
      flash[:notice] = "#{t('employee_attendance_added')}"
    else
      flash[:notice] = "#{t('something_went_wrong')}"
    end  
    render :update do |page|
      page.replace_html 'profile-infos', :partial => 'employee_add_attendance'
    end
  end
  
  def list_options
    @data_found = false
    unless params[:option_type].nil? or params[:option_type].empty? or params[:option_type].blank?
      @option_type = params[:option_type]
      if @option_type == "Position"
        @data_found = true
        @options = EmployeePosition.find(:all,:order => "name asc",:conditions=>'status = 1')
      elsif @option_type == "Department"
        @data_found = true
        @options = EmployeeDepartment.find(:all,:order => "name asc",:conditions=>'status = 1')
      end
    end
    render :update do |page|
      page.replace_html 'options-select', :partial => 'options_office_time'
    end
  end
  
  def edit_employee_settings
    @employee = Employee.find(params[:id])
    @employee_settings = EmployeeSetting.find_by_employee_id(params[:id])
    @default_weekdays = WeekdaySet.default_weekdays
    render :update do |page|
      page.replace_html 'profile-infos', :partial => 'edit_employee_settings'
    end
  end
  
  def employee_attendance_time
    @default_weekdays = WeekdaySet.default_weekdays
  end
  
  def employee_setting_mass_update
    if request.post?
      attendance_type_options = params[:attendance_type_options]
      unless attendance_type_options == "Department" or attendance_type_options == "Position"
        flash[:notice] = "<p class=\"flash-msg\">#{t('flash52')}</p>"
      else
        option_id = params[:option_id]
        if option_id == "-- Select --"
          flash[:notice] = "<p class=\"flash-msg\">#{t('flash53')} #{params[:option_type]} #{t('flash54')}</p>"
        else
          option_type = params[:option_type]
          if option_type == "Position"
            employees = Employee.find(:all ,:conditions=>"employee_position_id = #{option_id.to_i}")
          elsif option_type == "Department"
            employees = Employee.find(:all ,:conditions=>"employee_department_id = #{option_id.to_i}")
          end
          
          unless employees.nil? or employees.empty?
            weekdays = params[:weekdays].join(",")
            employees.each do |e|
              @employee_settings = EmployeeSetting.find_by_employee_id(e.id)
              unless @employee_settings.nil? 
                @employee_settings.update_attributes(params[:employee_setting])
                @employee_settings.update_attribute("weekdays",weekdays)
              else
                @employee_settings = EmployeeSetting.new(params[:employee_setting])
                @employee_settings.weekdays = weekdays  
                @employee_settings.employee_id = e.id
                @employee_settings.save
              end   
            end
            flash[:notice] = "<p class=\"flash-msg\">#{t('flash55')}</p>"
          end
        end
      end
    end
    render :update do |page|
      page.replace_html 'message', :partial => 'employee_setting_mass_update_settings_msg'
    end
  end
  
  def employee_setting_update
    now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    @employee = Employee.find(params[:id])
    @employee_settings = EmployeeSetting.find_by_employee_id(params[:id])
    weekdays = params[:weekdays].join(",")
  
    unless @employee_settings.nil? 
      @employee_settings.update_attributes(params[:employee_setting])
      @employee_settings.update_attribute("weekdays",weekdays)
    else
      @employee_settings = EmployeeSetting.new(params[:employee_setting])
      @employee_settings.weekdays = weekdays  
      @employee_settings.employee_id = params[:id]
      @employee_settings.save
    end   
    render :update do |page|
      page.replace_html 'profile-infos',:partial => "employee_settings"
    end
  end
  
  def add_category
    @categories = EmployeeCategory.find(:all,:order => "name asc",:conditions=>'status = 1')
    @inactive_categories = EmployeeCategory.find(:all,:conditions=>'status = 0')
    @category = EmployeeCategory.new(params[:category])
    if request.post? and @category.save
      flash[:notice] = "#{t('flash1')}"
      redirect_to :controller => "employee", :action => "add_category"
    end
  end
  
  def settings
    has_permission_for_this_link = Rails.cache.fetch("has_permission_payroll_setting_#{current_user.id}"){
      has_permission = true
      menu_links = MenuLink.find_by_name(["create_payslip"])
      if menu_links.link_type == 'user_menu'
        menu_id = menu_links.id

        school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")

        if school_menu_links.nil? or school_menu_links.blank?
           has_permission = false
        end
      end

      has_permission
    }
    @no_payroll_menu = has_permission_for_this_link
  end

  def edit_category
    @category = EmployeeCategory.find(params[:id])
    employees = Employee.find(:all ,:conditions=>"employee_category_id = #{params[:id]}")
    if request.post?
      if (params[:category][:status] == 'false' and employees.blank?) or params[:category][:status] == 'true'

        if @category.update_attributes(params[:category])
          unless @category.status
            position = EmployeePosition.find_all_by_employee_category_id(@category.id)
            position.each do |p|
              p.update_attributes(:status=> '0')
            end
          end
          flash[:notice] = "#{t('flash2')}"
          redirect_to :action => "add_category"
        end
      else
        flash[:warn_notice] = "<p>#{t('flash47')}</p>"
      end

    end
  end

  def delete_category
    employees = Employee.find(:all ,:conditions=>"employee_category_id = #{params[:id]}")
    if employees.empty?
      employees = ArchivedEmployee.find(:all ,:conditions=>"employee_category_id = #{params[:id]}")
    end
    category_position = EmployeePosition.find(:all, :conditions=>"employee_category_id = #{params[:id]}")
    if employees.empty? and category_position.empty?
      EmployeeCategory.find(params[:id]).destroy
      @categories = EmployeeCategory.find :all
      flash[:notice]= "#{t('flash3')}"
      redirect_to :action => "add_category"
    else
      flash[:warn_notice]=t('flash4')
      redirect_to :action => "add_category"
    end
  end

  def add_position
    @positions = EmployeePosition.find(:all,:order => "name asc",:conditions=>'status = 1')
    @inactive_positions = EmployeePosition.find(:all,:order => "name asc",:conditions=>'status = 0')
    @categories = EmployeeCategory.find(:all,:order => "name asc",:conditions=> 'status = 1')
    @position = EmployeePosition.new(params[:position])
    if request.post? and @position.save
      flash[:notice] = "#{t('flash5')}"
      redirect_to :controller => "employee", :action => "add_position"
    end
  end

  def edit_position
    @categories = EmployeeCategory.find(:all)
    @position = EmployeePosition.find(params[:id])
    employees = Employee.find(:all ,:conditions=>"employee_position_id = #{params[:id]}")
    if request.post?
      if (params[:position][:status] == 'false' and employees.blank?) or params[:position][:status] == 'true'
        if @position.update_attributes(params[:position])
          flash[:notice] = "#{t('flash6')}"
          redirect_to :action => "add_position"
        end
      else
        flash[:warn_notice]="<p>#{t('flash48')}</p>"
      end
    end

  end

  def delete_position
    employees = Employee.find(:all ,:conditions=>"employee_position_id = #{params[:id]}")
    if employees.empty?
      employees = ArchivedEmployee.find(:all ,:conditions=>"employee_position_id = #{params[:id]}")
    end
    if employees.empty?
      EmployeePosition.find(params[:id]).destroy
      @positions = EmployeePosition.find :all
      flash[:notice]= "#{t('flash3')}"
      redirect_to :action => "add_position"
    else
      flash[:warn_notice]=t('flash4')
      redirect_to :action => "add_position"
    end
  end

  def add_department
    @departments = EmployeeDepartment.find(:all,:order => "name asc",:conditions=>'status = 1')
    @inactive_departments = EmployeeDepartment.find(:all,:order => "name asc",:conditions=>'status = 0')
    @department = EmployeeDepartment.new(params[:department])
    if request.post? and @department.save
      flash[:notice] =  "#{t('flash7')}"
      redirect_to :controller => "employee", :action => "add_department"
    end
  end

  def edit_department
    @department = EmployeeDepartment.find(params[:id])
    employees = Employee.find(:all ,:conditions=>"employee_department_id = #{params[:id]}")
    if request.post?
      if (params[:department][:status] == 'false' and employees.blank?) or params[:department][:status] == 'true'
        if @department.update_attributes(params[:department])
          flash[:notice] = "#{t('flash8')}"
          redirect_to :action => "add_department"
        end
      else
        flash[:warn_notice]="<p>#{t('flash50')}</p>"
      end
    end
  end

  def delete_department
    employees = Employee.find(:all ,:conditions=>"employee_department_id = #{params[:id]}")
    if employees.empty?
      employees = ArchivedEmployee.find(:all ,:conditions=>"employee_department_id = #{params[:id]}")
    end
    if employees.empty?
      EmployeeDepartment.find(params[:id]).destroy
      @departments = EmployeeDepartment.find :all
      flash[:notice]= "#{t('flash3')}"
      redirect_to :action => "add_department"
    else
      flash[:warn_notice]= "#{t('flash4')}"
      redirect_to :action => "add_department"
    end
  end

  def add_grade
    @grades = EmployeeGrade.find(:all,:order => "name asc",:conditions=>'status = 1')
    @inactive_grades = EmployeeGrade.find(:all,:order => "name asc",:conditions=>'status = 0')
    @grade = EmployeeGrade.new(params[:grade])
    if request.post? and @grade.save
      flash[:notice] =  "#{t('flash9')}"
      redirect_to :controller => "employee", :action => "add_grade"
    end
  end

  def edit_grade
    @grade = EmployeeGrade.find(params[:id])
    employees = Employee.find(:all ,:conditions=>"employee_grade_id = #{params[:id]}")
    if request.post?
      if (params[:grade][:status] == 'false' and employees.blank?) or params[:grade][:status] == 'true'
        if @grade.update_attributes(params[:grade])
          flash[:notice] = "#{t('flash10')}"
          redirect_to :action => "add_grade"
        end
      else
        flash[:warn_notice]="<p>#{t('flash49')}</p>"
      end
    end
  end

  def delete_grade
    employees = Employee.find(:all ,:conditions=>"employee_grade_id = #{params[:id]}")
    if employees.empty?
      employees = ArchivedEmployee.find(:all ,:conditions=>"employee_grade_id = #{params[:id]}")
    end
    if employees.empty?
      EmployeeGrade.find(params[:id]).destroy
      @grades = EmployeeGrade.find :all
      flash[:notice]= "#{t('flash3')}"
      redirect_to :action => "add_grade"
    else
      flash[:warn_notice]= "#{t('flash4')}"
      redirect_to :action => "add_grade"
    end
  end

  def add_bank_details
    @bank_details = BankField.find(:all,:order => "name asc",:conditions=>{:status => true})
    @inactive_bank_details = BankField.find(:all,:order => "name asc",:conditions=>{:status => false})
    @bank_field = BankField.new(params[:bank_field])
    if request.post? and @bank_field.save
      flash[:notice] = "#{t('flash11')}"
      redirect_to :controller => "employee", :action => "add_bank_details"
    end
  end

  def edit_bank_details
    @bank_details = BankField.find(params[:id])
    if request.post? and @bank_details.update_attributes(params[:bank_details])
      flash[:notice] = "#{t('flash12')}"
      redirect_to :action => "add_bank_details"
    end
  end
  def delete_bank_details
    employees = EmployeeBankDetail.find(:all ,:conditions=>"bank_field_id = #{params[:id]}")
    if employees.empty?
      BankField.find(params[:id]).destroy
      @bank_details = BankField.find(:all)
      flash[:notice]= "#{t('flash3')}"
      redirect_to :action => "add_bank_details"
    else
      flash[:warn_notice]= "#{t('flash4')}"
      redirect_to :action => "add_bank_details"
    end
  end

  def add_additional_details
    @all_details = AdditionalField.find(:all,:order=>"priority ASC")
    @additional_details = AdditionalField.find(:all, :conditions=>{:status=>true},:order=>"priority ASC")
    @inactive_additional_details = AdditionalField.find(:all, :conditions=>{:status=>false},:order=>"priority ASC")
    @additional_field = AdditionalField.new
    @additional_field_option = @additional_field.additional_field_options.build
    if request.post?
      priority = 1
      unless @all_details.empty?
        last_priority = @all_details.map{|r| r.priority}.compact.sort.last
        priority = last_priority + 1
      end
      @additional_field = AdditionalField.new(params[:additional_field])
      @additional_field.priority = priority
      if @additional_field.save
        flash[:notice] = "#{t('flash13')}"
        redirect_to :controller => "employee", :action => "add_additional_details"
      end
    end
  end

  def change_field_priority
    @additional_field = AdditionalField.find(params[:id])
    priority = @additional_field.priority
    @additional_fields = AdditionalField.find(:all, :conditions=>{:status=>true}, :order=> "priority ASC").map{|b| b.priority.to_i}
    position = @additional_fields.index(priority)
    if params[:order]=="up"
      prev_field = AdditionalField.find_by_priority(@additional_fields[position - 1])
    else
      prev_field = AdditionalField.find_by_priority(@additional_fields[position + 1])
    end
    @additional_field.update_attributes(:priority=>prev_field.priority)
    prev_field.update_attributes(:priority=>priority.to_i)
    @additional_field = AdditionalField.new
    @additional_details = AdditionalField.find(:all, :conditions=>{:status=>true},:order=>"priority ASC")
    @inactive_additional_details = AdditionalField.find(:all, :conditions=>{:status=>false},:order=>"priority ASC")
    render(:update) do|page|
      page.replace_html "category-list", :partial=>"additional_fields"
    end
  end

  def edit_additional_details
    @additional_details = AdditionalField.find(:all, :conditions=>{:status=>true},:order=>"priority ASC")
    @inactive_additional_details = AdditionalField.find(:all, :conditions=>{:status=>false},:order=>"priority ASC")
    @additional_field = AdditionalField.find(params[:id])
    @additional_field_option = @additional_field.additional_field_options
    if request.get?
      render :action=>'add_additional_details'
    else
      if @additional_field.update_attributes(params[:additional_field])
        flash[:notice] = "#{t('flash14')}"
        redirect_to :action => "add_additional_details"
      else
        render :action=>"add_additional_details"
      end
    end
  end

  def delete_additional_details
    if params[:id]
      employees = EmployeeAdditionalDetail.find(:all ,:conditions=>"additional_field_id = #{params[:id]}")
      if employees.empty?
        AdditionalField.find(params[:id]).destroy
        @additional_details = AdditionalField.find(:all)
        flash[:notice]= "#{t('flash3')}"
        redirect_to :action => "add_additional_details"
      else
        flash[:warn_notice]=t('flash4')
        redirect_to :action => "add_additional_details"
      end
    else
      redirect_to :action => "add_additional_details"
    end
  end

  def admission1
    @user = current_user
    @user_name = @user.username
    @employee1 = @user.employee_record
    @categories = EmployeeCategory.find(:all,:order => "name asc",:conditions => "status = true")
    @positions = []
    @grades = EmployeeGrade.find(:all,:order => "name asc",:conditions => "status = true")
    @departments = EmployeeDepartment.find(:all,:order => "name asc",:conditions => "status = true")
    @nationalities = Country.all
    @employee = Employee.new(params[:employee])
    @selected_value = Configuration.default_country
    @last_admitted_employee = Employee.find(:last,:conditions=>"employee_number != 'admin' AND employee_number != 'champs21'")
    @config = Configuration.find_by_config_key('EmployeeNumberAutoIncrement')

    if request.post?
      
      @pass_word_no_error = true
      
      unless params[:employee][:employee_number].to_i ==0
        @employee.employee_number= "E" + params[:employee][:employee_number].to_s
      end
      
      if params[:employee][:pass].to_s == blank?
        @pass_word_no_error = false
        @employee.errors.add(:pass, "Password Can't be blank.")
      else
        @employee.pass = params[:employee][:pass]
      end
      
      if params[:employee][:password_confirmation].to_s == blank?
        @pass_word_no_error = false
        @employee.errors.add(:password_confirmation, "Confirm Password Can't be blank.")
      else
        @employee.password_confirmation = params[:employee][:password_confirmation]
      end
      
      if @employee.pass != @employee.password_confirmation
        @pass_word_no_error = false
        @employee.errors.add(:password_confirmation, "Confirm Password and Password must be same.")
      end
      
      unless @employee.employee_number.to_s.downcase == 'admin' or @employee.employee_number.to_s.downcase == 'champs21'
        
        str_employee = @employee.employee_number.to_s
      
        if str_employee.index(MultiSchool.current_school.code.to_s+"-")==nil
          @employee.employee_number = MultiSchool.current_school.code.to_s+"-"+@employee.employee_number
        end 
        
        if @pass_word_no_error == true
          if @employee.save
            username = @employee.employee_number        
            champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
            api_endpoint = champs21_api_config['api_url']
            uri = URI(api_endpoint + "api/user/createuser")
            http = Net::HTTP.new(uri.host, uri.port)
            auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
            auth_req.set_form_data({"paid_id" => @employee.user.id, "paid_username" => username, "paid_password" => @employee.pass,"password" => @employee.pass, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @employee.first_name, "middle_name" => @employee.middle_name, "last_name" => @employee.last_name, "gender" => (if @employee.gender == 'm' then '1' else '0' end), "country" => @employee.nationality_id, "dob" => @employee.date_of_birth, "email" => username, "user_type" => "3" })
            auth_res = http.request(auth_req)
            @auth_response = JSON::parse(auth_res.body)

            flash[:notice] = "#{t('flash15')} #{@employee.first_name} #{t('flash16')}"
            redirect_to :controller =>"employee" ,:action => "admission2", :id => @employee.id
          end
        end
      else
        @employee.errors.add(:employee_number, "#{t('should_not_be_admin')}")
      end
      @positions = EmployeePosition.find_all_by_employee_category_id(params[:employee][:employee_category_id])
    end
  end

  def update_positions
    category = EmployeeCategory.find(params[:category_id])
    @positions = EmployeePosition.find_all_by_employee_category_id(category.id,:conditions=>'status = 1')
    render :update do |page|
      page.replace_html 'positions1', :partial => 'positions', :object => @positions
    end
  end
  
  def edit_employee_own
    @countries = @nationalities = Country.find(:all)
    @employee = Employee.find(current_user.employee_entry.id)
    unless @employee.gender.nil?
      @employee.gender=@employee.gender.downcase
    end
    @employee_user = @employee.user
    if request.post?
      
        if @employee.update_attributes(params[:employee])
          
          username = @employee.employee_number        
          champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
          api_endpoint = champs21_api_config['api_url']
          uri = URI(api_endpoint + "api/user/UpdateProfilePaidUser")
          http = Net::HTTP.new(uri.host, uri.port)
          auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
          auth_req.set_form_data({"paid_id" => @employee.user.id, "paid_username" => username, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @employee.first_name, "middle_name" => @employee.middle_name, "last_name" => @employee.last_name, "gender" => (if @employee.gender == 'm' then '1' else '0' end), "country" => @employee.nationality_id, "dob" => @employee.date_of_birth, "email" => username })
          auth_res = http.request(auth_req)
          @auth_response = JSON::parse(auth_res.body)
          
          flash[:notice] = "#{t('flash15')}  #{@employee.first_name} #{t('flash17')}"
          redirect_to :controller =>"employee" ,:action => "profile", :id => @employee.id
        end
    end
  end
  
  def only_allowed_when_parmitted
    @config = Configuration.find_by_config_key('TeacherCanEdit')
    if @config.blank? or @config.config_value.blank? or @config.config_value.to_i == 0
        flash[:notice] = "#{t('flash_msg4')}"
        redirect_to :controller => 'user', :action => 'dashboard'
    else
      @allow_access = true
    end 
  end
  
  

  def edit1
    @categories = EmployeeCategory.find(:all,:order => "name asc", :conditions => "status = true")
    @positions = EmployeePosition.find(:all)
    @grades = EmployeeGrade.find(:all,:order => "name asc", :conditions => "status = true")
    @departments = EmployeeDepartment.find(:all,:order => "name asc", :conditions => "status = true")
    @employee = Employee.find(params[:id])
    unless @employee.gender.nil?
      @employee.gender=@employee.gender.downcase
    end
    @employee_user = @employee.user
    @employee.biometric_id = BiometricInformation.find_by_user_id(@employee.user_id).try(:biometric_id)
    if request.post?
      #@employee.biometric_id = params[:employee][:biometric_id]
      if  params[:employee][:employee_number].downcase != 'admin' or @employee_user.admin
        if @employee.update_attributes(params[:employee])
          
          username = @employee.employee_number        
          champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
          api_endpoint = champs21_api_config['api_url']
          uri = URI(api_endpoint + "api/user/UpdateProfilePaidUser")
          http = Net::HTTP.new(uri.host, uri.port)
          auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
          auth_req.set_form_data({"paid_id" => @employee.user.id, "paid_username" => username, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @employee.first_name, "middle_name" => @employee.middle_name, "last_name" => @employee.last_name, "gender" => (if @employee.gender == 'm' then '1' else '0' end), "country" => @employee.nationality_id, "dob" => @employee.date_of_birth, "email" => username })
          auth_res = http.request(auth_req)
          @auth_response = JSON::parse(auth_res.body)
          
          flash[:notice] = "#{t('flash15')}  #{@employee.first_name} #{t('flash17')}"
          redirect_to :controller =>"employee" ,:action => "profile", :id => @employee.id
        end
      else
        @employee.errors.add(:employee_number, "#{t('should_not_be_admin')}")
      end
    end
  end

  def edit_personal
    @nationalities = Country.all
    @employee = Employee.find(params[:id])
    if request.post?
      size = 0
      size =  params[:employee][:image_file].size.to_f unless  params[:employee][:image_file].nil?
      if size < 280000
        if @employee.update_attributes(params[:employee])
          flash[:notice] = "#{t('flash15')}  #{@employee.first_name} #{t('flash18')}"
          redirect_to :controller =>"employee" ,:action => "profile", :id => @employee.id
        end
      else
        flash[:notice] = "#{t('flash19')}"
        redirect_to :controller => "employee", :action => "edit_personal", :id => @employee.id
      end
    end
  end

  def admission2
    @countries = Country.find(:all)
    @employee = Employee.find(params[:id])
    @selected_value = Configuration.default_country
    if request.post? and @employee.update_attributes(params[:employee])
      sms_setting = SmsSetting.new()
      if sms_setting.application_sms_active and sms_setting.employee_sms_active and send_sms("emploeeregister")
        recipient = ["#{@employee.mobile_phone}"]
        message = "#{t('joining_info')} #{@employee.first_name}. #{t('username')}: #{@employee.employee_number}, #{t('password')}: 123456. #{t('change_password_after_login')}"
        Delayed::Job.enqueue(SmsManager.new(message,recipient))
      end
      flash[:notice] = "#{t('flash20')} #{ @employee.first_name}"
      redirect_to :action => "admission3", :id => @employee.id
    end
  end

  def edit2
    @employee = Employee.find(params[:id])
    @countries = Country.find(:all)
    if request.post? and @employee.update_attributes(params[:employee])
      flash[:notice] = "#{t('flash21')} #{ @employee.first_name}"
      redirect_to :action => "profile", :id => @employee.id
    end
  end

  def edit_contact
    @employee = Employee.find(params[:id])
    if request.post? and @employee.update_attributes(params[:employee])
      User.update(@employee.user.id, :email=> @employee.email, :role=>@employee.user.role_name)
      flash[:notice] = "#{t('flash22')} #{ @employee.first_name}"
      redirect_to :action => "profile", :id => @employee.id
    end
  end


  def admission3
    @employee = Employee.find(params[:id])
    @bank_fields = BankField.find(:all, :conditions=>"status = true")
    if @bank_fields.empty?
      redirect_to :action => "admission3_1", :id => @employee.id
    end
    if request.post?
      params[:employee_bank_details].each_pair do |k, v|
        EmployeeBankDetail.create(:employee_id => params[:id],
          :bank_field_id => k,:bank_info => v['bank_info'])
      end
      flash[:notice] = "#{t('flash23')} #{@employee.first_name}"
      redirect_to :action => "admission3_1", :id => @employee.id
    end
  end

  def edit3
    @employee = Employee.find(params[:id])
    @bank_fields = BankField.find(:all, :conditions=>"status = true")
    if @bank_fields.empty?
      flash[:notice] = "#{t('flash35')}"
      redirect_to :action => "profile", :id => @employee.id
    end
    if request.post?
      params[:employee_bank_details].each_pair do |k, v|
        row_id= EmployeeBankDetail.find_by_employee_id_and_bank_field_id(@employee.id,k)
        unless row_id.nil?
          bank_detail = EmployeeBankDetail.find_by_employee_id_and_bank_field_id(@employee.id,k)
          EmployeeBankDetail.update(bank_detail.id,:bank_info => v['bank_info'])
        else
          EmployeeBankDetail.create(:employee_id=>@employee.id,:bank_field_id=>k,:bank_info=>v['bank_info'])
        end
      end
      flash[:notice] = "#{t('flash15')}#{' '}#{@employee.first_name} #{t('flash12')}"
      redirect_to :action => "profile", :id => @employee.id
    end
  end

  #  def admission3_1
  #    @employee = Employee.find(params[:id])
  #    @additional_fields = AdditionalField.find(:all, :conditions=>"status = true")
  #    if @additional_fields.empty?
  #      redirect_to :action => "edit_privilege", :id => @employee.employee_number
  #    end
  #    if request.post?
  #      params[:employee_additional_details].each_pair do |k, v|
  #        EmployeeAdditionalDetail.create(:employee_id => params[:id],
  #          :additional_field_id => k,:additional_info => v['additional_info'])
  #      end
  #      flash[:notice] = "#{t('flash25')}#{@employee.first_name}"
  #      redirect_to :action => "edit_privilege", :id => @employee.employee_number
  #    end
  #  end

  def admission3_1
    @employee = Employee.find(params[:id])
    @employee_additional_details = EmployeeAdditionalDetail.find_all_by_employee_id(@employee.id)
    @additional_fields = AdditionalField.find(:all, :conditions=> "status = true", :order=>"priority ASC")
    if @additional_fields.empty?
      redirect_to :action => "edit_privilege", :id => @employee.employee_number
    end
    if request.post?
      @error=false
      mandatory_fields = AdditionalField.find(:all, :conditions=>{:is_mandatory=>true, :status=>true})
      mandatory_fields.each do|m|
        unless params[:employee_additional_details][m.id.to_s.to_sym].present?
          @employee.errors.add_to_base("#{m.name} must contain atleast one selected option.")
          @error=true
        else
          if params[:employee_additional_details][m.id.to_s.to_sym][:additional_info]==""
            @employee.errors.add_to_base("#{m.name} cannot be blank.")
            @error=true
          end
        end
      end
      unless @error==true
        additional_field_ids_posted = []
        additional_field_ids = @additional_fields.map(&:id)
        if params[:employee_additional_details].present?
          params[:employee_additional_details].each_pair do |k, v|
            addl_info = v['additional_info']
            additional_field_ids_posted << k.to_i
            addl_field = AdditionalField.find_by_id(k)
            if addl_field.input_type == "has_many"
              addl_info = addl_info.join(", ")
            end
            prev_record = EmployeeAdditionalDetail.find_by_employee_id_and_additional_field_id(params[:id], k)
            unless prev_record.nil?
              unless addl_info.present?
                prev_record.destroy
              else
                prev_record.update_attributes(:additional_info => addl_info)
              end
            else
              addl_detail = EmployeeAdditionalDetail.new(:employee_id => params[:id],
                :additional_field_id => k,:additional_info => addl_info)
              addl_detail.save if addl_detail.valid?
            end
          end
        end
        if additional_field_ids.present?
          EmployeeAdditionalDetail.find_all_by_employee_id_and_additional_field_id(params[:id],(additional_field_ids - additional_field_ids_posted)).each do |additional_info|
            additional_info.destroy unless additional_info.additional_field.is_mandatory == true
          end
        end
        
        unless params[:edit_request].present?
          flash[:notice] = "#{t('flash25')}#{@employee.first_name}"
          redirect_to :action => "edit_privilege", :id => @employee.employee_number
        else
          flash[:notice] = "#{t('flash15')}#{' '}#{@employee.first_name} #{t('flash14')}"
          redirect_to :action => "profile", :id => @employee.id
        end
      end
    end
  end

  def edit_privilege
    @user = User.active.find_by_username(params[:id])
    @employee = @user.employee_record
    @finance = Configuration.find_by_config_value("Finance")
    @sms_setting = SmsSetting.application_sms_status
    @hr = Configuration.find_by_config_value("HR")
    @privilege_tags=PrivilegeTag.find(:all,:order=>"priority ASC")
    @user_privileges=@user.privileges
    @privileges = []
    @a_privileges = []
    @privilege_tags.each do |pt|
      privileges = pt.privileges.all(:order=>"priority ASC")
      @a_privileges = []
      privileges.each do |p|
        if p.menu_type == 'general' and p.menu_id > 0
          menu_id = p.menu_id
          menu_links = MenuLink.find_by_id(menu_id)
          if menu_links.link_type == 'user_menu'
              school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
              unless school_menu_links.blank?
                @a_privileges << p
              end
          elsif menu_links.link_type == 'general'
            @a_privileges << p
          end
        elsif p.menu_type == 'plugins' and p.menu_id > 0
          menu_id = p.menu_id
          menu_links = MenuLink.find_by_id(menu_id)
          if menu_links.link_type == 'general'
              if (can_access_request?(menu_links.target_action.to_s.to_sym,@user,:context=>menu_links.target_controller.to_s.to_sym))
                  @a_privileges << p
              end    
          end
        else
          @a_privileges << p
        end
      
      end
      @privileges << @a_privileges
    end
    
    if request.post?
      new_privileges = params[:user][:privilege_ids] if params[:user]
      new_privileges ||= []
      @user.privileges = Privilege.find_all_by_id(new_privileges)
      
      UserPalette.delete_all(:user_id=>@user.id)
      data_pallettes_plugins = {"polls" => "champs21_poll", "placements" => "champs21_placement", "book_return_due" => "champs21_library", "photos_added" => "champs21_gallery", "discussions" => "champs21_discussion", "blogs" => "champs21_blog", "online_meetings" => "champs21_bigbluebutton", "homework" => "champs21_assignment"}
      
      Authorization.current_user = @user
      user_roles = Authorization.current_user.role_symbols
      
      default_palettes = []
      
      teacher_palette = ["employees_on_leave","leave_applications","news","events","timetable"]
      default_palettes_teacher = Palette.find(:all, :conditions => ["name IN (?) AND menu_type IN ('general', 'user_menu_teacher')", teacher_palette])
      default_palettes_teacher.each do |dp| 
        if data_pallettes_plugins[dp.name].nil?
          if dp.menu_type == "user_menu_teacher"
            default_palettes << dp
          else
            menu_id = dp.menu_id
            if menu_id > 0
                menu_links = MenuLink.find_by_id(menu_id)
                if menu_links.link_type == 'user_menu'
                    school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
                    unless school_menu_links.blank?
                      default_palettes << dp
                    end
                elsif menu_links.link_type != 'not_active_menu'
                  default_palettes << dp
                end    
            else
              default_palettes << dp
            end
          end
        else
          plugins_name = data_pallettes_plugins[dp.name]
          plugins_data = AvailablePlugin.find_by_associated_id(MultiSchool.current_school.id)
          unless plugins_data.nil?
            plugins = plugins_data.plugins
            if plugins.include?(plugins_name)
              default_palettes << dp
            end
          else
            unless ARGV[1].nil? or ARGV[1].blank? or ARGV[1].empty? 
              package_id = ARGV[1]
              plugins_data = PackageMenu.find(:first, :conditions => ["package_id = ? AND plugins_name = ?",package_id,plugins_name])
              unless plugins_data.nil?
                default_palettes << dp
              end 
            end
          end
        end  
      end


      pallete_required = 5 - default_palettes.length
      if pallete_required > 0
        i = 0
        default_palettes_teacher = Palette.compatible_palettes(Palette.find(:all, :conditions => ["name NOT IN (?)",teacher_palette]),user_roles)
        default_palettes_teacher.each do |dp| 
          if data_pallettes_plugins[dp.name].nil?
            menu_id = dp.menu_id
            if menu_id > 0
                menu_links = MenuLink.find_by_id(menu_id)
                if menu_links.link_type == 'user_menu'
                    school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
                    unless school_menu_links.blank?
                        default_palettes << dp
                        if i + 1 == pallete_required
                          break
                        end
                        i = i + 1
                    end
                elsif menu_links.link_type != 'not_active_menu' and menu_links.link_type != 'own'
                    default_palettes << dp
                    if i + 1 == pallete_required
                      break
                    end
                    i = i + 1
                end    
            else
                default_palettes << dp
                if i + 1 == pallete_required
                  break
                end
                i = i + 1
            end
          else
            plugins_name = data_pallettes_plugins[dp.name]
            plugins_data = AvailablePlugin.find_by_associated_id(MultiSchool.current_school.id)
            unless plugins_data.nil?
              plugins = plugins_data.plugins
              if plugins.include?(plugins_name)
                default_palettes << dp
                if i + 1 == pallete_required
                  break
                end
                i = i + 1
              end
            else
              unless ARGV[1].nil? or ARGV[1].blank? or ARGV[1].empty? 
                package_id = ARGV[1]
                plugins_data = PackageMenu.find(:first, :conditions => ["package_id = ? AND plugins_name = ?",package_id,plugins_name])
                unless plugins_data.nil?
                  default_palettes << dp
                  if i + 1 == pallete_required
                    break
                  end
                  i = i + 1
                end 
              end
            end
          end
        end
      end
      
      default_palettes.each do|p|
        UserPalette.create(:user_id=>@user.id,:palette_id=>p.id)
      end
      
      redirect_to :action => 'admission4',:id => @employee.id
    end
  end

  def edit3_1
    @employee = Employee.find(params[:id])
    @additional_fields = AdditionalField.find(:all, :conditions=>"status = true")
    if @additional_fields.empty?
      flash[:notice] = "#{t('flash37')}"
      redirect_to :action => "profile", :id => @employee.id
    end
    if request.post?
      params[:employee_additional_details].each_pair do |k, v|
        row_id= EmployeeAdditionalDetail.find_by_employee_id_and_additional_field_id(@employee.id,k)
        unless row_id.nil?
          additional_detail = EmployeeAdditionalDetail.find_by_employee_id_and_additional_field_id(@employee.id,k)
          EmployeeAdditionalDetail.update(additional_detail.id,:additional_info => v['additional_info'])
        else
          EmployeeAdditionalDetail.create(:employee_id=>@employee.id,:additional_field_id=>k,:additional_info=>v['additional_info'])
        end
      end
      flash[:notice] = "#{t('flash15')}#{@employee.first_name} #{t('flash14')}"
      redirect_to :action => "profile", :id => @employee.id
    end
  end

  def admission4
    @departments = EmployeeDepartment.find(:all)
    @categories  = EmployeeCategory.find(:all)
    @positions   = EmployeePosition.find(:all)
    @grades      = EmployeeGrade.find(:all)
    if request.post?
      @employee = Employee.find(params[:id])
      manager=Employee.find_by_id(params[:employee][:reporting_manager_id])
      if manager.present?
        Employee.update(@employee, :reporting_manager_id => manager.user_id)
      end
      flash[:notice]= "#{t('flash25')}"
      redirect_to :controller => "payroll", :action => "manage_payroll", :id=>@employee.id
    end

  end

  def view_rep_manager
    @employee= Employee.find(params[:id])
    @reporting_manager = @employee.reporting_manager.first_name unless @employee.reporting_manager_id.nil?
    render :partial => "view_rep_manager"
  end

  def change_reporting_manager
    @departments = EmployeeDepartment.find(:all)
    @categories  = EmployeeCategory.find(:all)
    @positions   = EmployeePosition.find(:all)
    @grades      = EmployeeGrade.find(:all)
    @emp = Employee.find(params[:id])
    @reporting_manager = @emp.reporting_manager
    if request.post?
      manager = Employee.find_by_id(params[:employee][:reporting_manager_id])
      if manager.present?
        @emp.update_attributes(:reporting_manager_id => manager.user_id)
      else
        @emp.update_attributes(:reporting_manager_id => nil)
      end
      flash[:notice]= "#{t('flash26')}"
      redirect_to :action => "profile", :id=>@emp.id
    end
  end

  def update_reporting_manager_name
    employee = Employee.find_by_id(params[:employee_reporting_manager_id])
    render :text => employee.first_name + ' ' + employee.last_name
  end

  def search
    @departments = EmployeeDepartment.active.find(:all,:order=>'name ASC')
    @categories  = EmployeeCategory.active.find(:all,:order=>'name ASC')
    @positions   = EmployeePosition.active.find(:all,:order=>'name ASC')
    @grades      = EmployeeGrade.active.find(:all,:order=>'name ASC')
  end

  def search_ajax
    other_conditions = ""
    other_conditions += " AND employee_department_id = '#{params[:employee_department_id]}'" unless params[:employee_department_id] == ""
    other_conditions += " AND employee_category_id = '#{params[:employee_category_id]}'" unless params[:employee_category_id] == ""
    other_conditions += " AND employee_position_id = '#{params[:employee_position_id]}'" unless params[:employee_position_id] == ""
    other_conditions += " AND employee_grade_id = '#{params[:employee_grade_id]}'" unless params[:employee_grade_id] == ""
    if params[:query].length>= 3
      @employee = Employee.find(:all,
        :conditions => ["(first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                       OR employee_number = ? OR (concat(first_name, \" \", last_name) LIKE ? ))"+ other_conditions,
          "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
          "#{params[:query]}", "#{params[:query]}" ],
        :order => "employee_department_id asc,first_name asc",:include=>"employee_department") unless params[:query] == ''
    else
      @employee = Employee.find(:all,
        :conditions => ["(employee_number = ? )"+ other_conditions, "#{params[:query]}"],
        :order => "employee_department_id asc,first_name asc",:include=>"employee_department") unless params[:query] == ''
    end
    render :layout => false
  end

  def select_reporting_manager
    other_conditions = ""
    other_conditions += " AND employee_department_id = '#{params[:employee_department_id]}'" unless params[:employee_department_id] == ""
    other_conditions += " AND employee_category_id = '#{params[:employee_category_id]}'" unless params[:employee_category_id] == ""
    other_conditions += " AND employee_position_id = '#{params[:employee_position_id]}'" unless params[:employee_position_id] == ""
    other_conditions += " AND employee_grade_id = '#{params[:employee_grade_id]}'" unless params[:employee_grade_id] == ""
    if params[:query].length>= 3
      @employee = Employee.find(:all,
        :conditions => ["(first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                       OR employee_number = ? OR (concat(first_name, \" \", last_name) LIKE ? ))"+ other_conditions,
          "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
          "#{params[:query]}", "#{params[:query]}" ],
        :order => "employee_department_id asc,first_name asc") unless params[:query] == ''
    else
      @employee = Employee.find(:all,
        :conditions => ["(employee_number = ? )"+ other_conditions, "#{params[:query]}"],
        :order => "employee_department_id asc,first_name asc",:include=>"employee_department") unless params[:query] == ''
    end
    render :layout => false
  end

  def profile

    @current_user = current_user
    @employee = Employee.find(params[:id])
    @new_reminder_count = Reminder.find_all_by_recipient(@current_user.id, :conditions=>"is_read = false")
    @gender = "Male"
    @gender = "Female" if @employee.gender.downcase == "f"
    @status = "Active"
    @status = "Inactive" if @employee.status == false
    @reporting_manager = @employee.reporting_manager
    @biometric_id = BiometricInformation.find_by_user_id(@employee.user_id).try(:biometric_id)
    years = @employee.find_experience_years
    months = @employee.find_experience_months
    year = months/12
    month = months%12
    @total_years = years + year
    @total_months = month

    @att_text = ''
    @att_image = ''
    get_attendence_text
    unless @attendence_text.nil?
      if @attendence_text['status']['code'].to_i == 200
        @att_text = @attendence_text['data']['text']
        @att_image = @attendence_text['data']['profile_picture']
      end
    end
  end

  def profile_general
    @employee = Employee.find(params[:id])
    @gender = "Male"
    @gender = "Female" if @employee.gender.downcase == "f"
    @status = "Active"
    @status = "Inactive" if @employee.status == false
    @reporting_manager = @employee.reporting_manager
    years = @employee.find_experience_years
    months = @employee.find_experience_months
    year = months/12
    month = months%12
    @total_years = years + year
    @total_months = month
    render :partial => "general"
  end

  def profile_personal
    @employee = Employee.find(params[:id])
    render :partial => "personal"
  end

  def profile_address
    @employee = Employee.find(params[:id])
    @home_country = @employee.home_country.try(:name)#Country.find(@employee.home_country_id).name unless @employee.home_country_id.nil?
    @office_country = @employee.office_country.try(:name)#Country.find(@employee.office_country_id).name unless @employee.office_country_id.nil?
    render :partial => "address"
  end

  def profile_contact
    @employee = Employee.find(params[:id])
    render :partial => "contact"
  end

  def profile_bank_details
    @employee = Employee.find(params[:id])
    @bank_details = EmployeeBankDetail.find_all_by_employee_id(@employee.id)
    render :partial => "bank_details"
  end
  
  def references
    @employee = Employee.find(params[:id])
    @reference = EmployeeReferences.find_all_by_employee_id(@employee.id,:order=>"id DESC")
    render :partial => "references"
  end
  
  def edit_references
    @reference = EmployeeReferences.find_by_id(params[:id])
    render :update do |page|
      page.replace_html 'profile-infos', :partial => 'edit_references'
    end
  end
  def add_references
   @employee = Employee.find(params[:id])
    render :update do |page|
      page.replace_html 'profile-infos', :partial => 'add_references'
    end
  end
  def create_references
    @employeereferences = EmployeeReferences.new(params[:reference])
    @employeereferences.employee_id = params[:id]
    @employeereferences.save
    @reference = EmployeeReferences.find_all_by_employee_id(@employeereferences.employee_id,:order=>"id DESC")
    @employee = Employee.find(@employeereferences.employee_id)
    render :update do |page|
      page.replace_html 'profile-infos',:partial => "references"
    end
  end
  
   def update_references
    now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    @employeereferences = EmployeeReferences.find(params[:id])
    @employeereferences.update_attributes(params[:reference])
    @reference = EmployeeReferences.find_all_by_employee_id(@employeereferences.employee_id,:order=>"id DESC")
    @employee = Employee.find(@employeereferences.employee_id)
    render :update do |page|
      page.replace_html 'profile-infos',:partial => "references"
    end
  end
  
  
  
  
  def history
    @employee = Employee.find(params[:id])
    @history = EmployeeHistory.find_all_by_employee_id(@employee.id,:order=>"date_discontinuation DESC")
    render :partial => "history"
  end
  
  def edit_history
    @history = EmployeeHistory.find_by_id(params[:id])
    render :update do |page|
      page.replace_html 'profile-infos', :partial => 'edit_history'
    end
  end
  def add_history
   @employee = Employee.find(params[:id])
    render :update do |page|
      page.replace_html 'profile-infos', :partial => 'add_history'
    end
  end
  def create_history
    @employeehistory = EmployeeHistory.new(params[:history])
    @employeehistory.employee_id = params[:id]
    @employeehistory.save
    @history = EmployeeHistory.find_all_by_employee_id(@employeehistory.employee_id,:order=>"date_discontinuation DESC")
    @employee = Employee.find(@employeehistory.employee_id)
    render :update do |page|
      page.replace_html 'profile-infos',:partial => "history"
    end
  end
  
   def update_history
    now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    @employeehistory = EmployeeHistory.find(params[:id])
    @employeehistory.update_attributes(params[:history])
    @history = EmployeeHistory.find_all_by_employee_id(@employeehistory.employee_id,:order=>"date_discontinuation DESC")
    @employee = Employee.find(@employeehistory.employee_id)
    render :update do |page|
      page.replace_html 'profile-infos',:partial => "history"
    end
  end
  

  def profile_additional_details
    @employee = Employee.find(params[:id])
    @additional_details = AdditionalField.find(:all, :conditions=>{:status=>true},:order=>"priority ASC")
    #    @additional_details = EmployeeAdditionalDetail.find_all_by_employee_id(@employee.id).select{|a| a.additional_field.status==true}
    render :partial => "additional_details"
  end


  def profile_payroll_details
    @employee = Employee.find(params[:id])
    @active_payroll_count=PayrollCategory.active.count(:conditions=>{:status=>true})
    @payroll_details = EmployeeSalaryStructure.all(:select=>"employee_salary_structures.id,employee_id,amount,payroll_categories.name,payroll_categories.is_deduction,employee_salary_structures.payroll_category_id",:conditions=>{:employee_id=>params[:id],:payroll_categories=>{:status=>true}},:joins=>[:payroll_category],:order=>"payroll_category_id")
    render :partial => "payroll_details"
  end

  def profile_pdf
    @employee = Employee.find(params[:id])
    @gender = "Male"
    @gender = "Female" if @employee.gender.downcase == "f"
    @status = "Active"
    @status = "Inactive" if @employee.status == false
    @reporting_manager = @employee.reporting_manager unless @employee.reporting_manager_id.nil?
    years = @employee.find_experience_years
    months = @employee.find_experience_months
    year = months/12
    month = months%12
    @total_years = years + year
    @total_months = month
    @home_country = @employee.home_country.try(:name)
    @office_country = @employee.office_country.try(:name)
    @bank_details = EmployeeBankDetail.find_all_by_employee_id(@employee.id)
    @additional_details = EmployeeAdditionalDetail.find_all_by_employee_id(@employee.id).select{|a| a.additional_field.status==true}
    @biometric_id = BiometricInformation.find_by_user_id(@employee.user_id).try(:biometric_id)
    
    @att_text = ''
    @att_image = ''
#    get_attendence_text
#    if @attendence_text['status']['code'].to_i == 200
#      @att_text = @attendence_text['data']['text']
#      @att_image = @attendence_text['data']['profile_picture']
#    end
    
    render :pdf => 'profile_pdf'


    #    respond_to do |format|
    #      format.pdf { render :layout => false }
    #    end
  end

  def view_all
    @departments = EmployeeDepartment.active
  end
  
  def view_all_applicant
    @departments = EmployeeDepartment.active
  end
  
  def applicant_list
    @department_id = params[:department_id]
    sql = "SELECT emp.*
FROM
employees AS emp left join users as us on emp.user_id=us.id
WHERE emp.school_id = '#{MultiSchool.current_school.id}'
and emp.employee_department_id='#{@department_id}' and us.is_approved=0 and us.free_user_id!=0
ORDER BY emp.first_name ASC"
    
     unless params[:emp_id].nil?
      unless params[:status_change].nil?       
        if params[:status_change] == "1"
          
          activated_employee(params[:emp_id])
          flash[:notice] = "#{t('employee_activated')}"
        elsif params[:status_change] == "0"
          delete_employee(params[:emp_id])
          flash[:notice] = "#{t('employee_parmanantly_deleted')}"
        end  
        
      end
    end
   
    @employees = Employee.find_by_sql(sql)

    render :update do |page|
      page.replace_html 'employee_list', :partial => 'applicant_view_all_list', :object => @employees
    end
  end

  def employees_list
    department_id = params[:department_id]
    @employees = Employee.find_all_by_employee_department_id(department_id,:order=>'first_name ASC')

    render :update do |page|
      page.replace_html 'employee_list', :partial => 'employee_view_all_list', :object => @employees
    end
  end
  
  def allemployees_list    
    @employees = Employee.find(:all, :order => 'employee_department_id ASC')
  end

  def show
    @employee = Employee.find(params[:id])
    send_data(@employee.photo_data, :type => @employee.photo_content_type, :filename => @employee.photo_filename, :disposition => 'inline')
  end

  def create_payslip_category
    @employee=Employee.find(params[:employee_id])
    @salary_date= (params[:salary_date])
    @created_category = IndividualPayslipCategory.new(:employee_id=>params[:employee_id],:name=>params[:name],:amount=>params[:amount])
    if @created_category.save
      if params[:is_deduction] == nil
        IndividualPayslipCategory.update(@created_category.id, :is_deduction=>false)
      else
        IndividualPayslipCategory.update(@created_category.id, :is_deduction=>params[:is_deduction])
      end

      if params[:include_every_month] == nil
        IndividualPayslipCategory.update(@created_category.id, :include_every_month=>false)
      else
        IndividualPayslipCategory.update(@created_category.id, :include_every_month=>params[:include_every_month])
      end

      @new_payslip_category = IndividualPayslipCategory.find_all_by_employee_id_and_salary_date(@employee.id,nil)
      @individual = IndividualPayslipCategory.find_all_by_employee_id_and_salary_date(@employee.id,@salary_date)
      render :partial => "payslip_category_list",:locals => {:emp_id => @employee.id, :salary_date=>@salary_date}
    else
      render :partial => "payslip_category_form"
    end
  end

  def remove_new_paylist_category
    removal_category = IndividualPayslipCategory.find(params[:id])
    employee = removal_category.employee_id
    @salary_date = params[:id3]
    removal_category.destroy
    @new_payslip_category = IndividualPayslipCategory.find_all_by_employee_id_and_salary_date(employee,nil)
    @individual = IndividualPayslipCategory.find_all_by_employee_id_and_salary_date(employee,@salary_date, :conditions=>"") unless params[:id3]==''
    @individual ||= []
    render :partial => "list_payslip_category"
  end

  def create_monthly_payslip
    @employee = Employee.find(params[:id])
    #@independent_categories+@dependent_categories
    @payroll_categories = PayrollCategory.active.all(:conditions=>["(payroll_category_id != \'\' or payroll_category_id is NULL) and status=1"])
    category_ids=@payroll_categories.collect(&:id)
    @employee_salary_structure=EmployeeSalaryStructure.all(:conditions=>{:employee_id=>params[:id],:payroll_category_id=>category_ids}).group_by(&:payroll_category_id)
    if request.xhr?
      flash[:notice]=nil
      salary_date = Date.parse(params[:salary_date])
      error=0
      unless salary_date.to_date < @employee.joining_date.to_date
        if params[:manage_payroll].present?
          start_date = salary_date - ((salary_date.day - 1).days)
          end_date = start_date + 1.month
          payslip_exists = MonthlyPayslip.find_all_by_employee_id(@employee.id,:conditions => ["salary_date >= ? and salary_date < ?", start_date, end_date])
          if payslip_exists == []
            ActiveRecord::Base.transaction do
              error=1  unless @employee.update_attributes(:monthly_payslips_attributes=>params[:manage_payroll][:monthly_payslips_attributes])
              if params[:new_category].present?
                error=1  unless @employee.update_attributes(:individual_payslip_categories_attributes=>params[:new_category][:individual_payslip_categories_attributes])
              end
              if error==1
                raise ActiveRecord::Rollback
              end
            end
            flash[:notice] = "#{@employee.first_name} #{t('flash27')} #{params[:salary_date]}"
          else
            flash[:notice] = "#{@employee.first_name} #{t('flash28')} #{params[:salary_date]}"
          end
        else
          error=1
          @employee.errors.add_to_base("#{t('flash51')}")
        end
      else
        error=1
        @employee.errors.add_to_base("#{t('flash45')} #{params[:salary_date]}")
      end
      if error==1
        render :update do |page|
          page.replace_html 'errors', :partial => 'errors', :object => @employee
        end
      else
        privilege = Privilege.find_by_name("FinanceControl")
        finance_manager_ids = privilege.user_ids
        subject = t('payslip_generated')
        body = "#{t('payslip_generated_for')}  "+@employee.first_name+" "+@employee.last_name+". #{t('kindly_approve')}"
        Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
            :recipient_ids => finance_manager_ids,
            :subject=>subject,
            :body=>body ))
        render :update do |page|
          page.redirect_to :controller => "employee", :action => "select_department_employee"
        end
      end
    end
  end

 
  def view_payslip
    @employee = Employee.find(params[:id])
    @salary_dates = MonthlyPayslip.find_all_by_employee_id(params[:id], :conditions=>"is_approved = true",:select => "distinct salary_date")
    render :partial => "select_dates"
  end

  def update_monthly_payslip
    @currency_type = currency
    @salary_date = params[:salary_date]
    if params[:salary_date] == ""
      render :update do |page|
        page.replace_html "payslip_view", :text => ""
      end
      return
    end
    unless params[:salary_date]==nil
      @monthly_payslips = MonthlyPayslip.find_all_by_salary_date(params[:salary_date],
        :conditions=> "employee_id =#{params[:emp_id]}",
        :order=> "payroll_category_id ASC")

      @individual_payslip_category = IndividualPayslipCategory.find_all_by_salary_date(params[:salary_date],
        :conditions=>"employee_id =#{params[:emp_id]}",
        :order=>"id ASC")
      @individual_category_non_deductionable = 0.0
      @individual_category_deductionable = 0.0
      @individual_payslip_category.each do |pc|
        unless pc.is_deduction == true
          @individual_category_non_deductionable = @individual_category_non_deductionable + "#{sprintf("%.2f",(pc.amount || 0.0))}".to_f
        end
      end

      @individual_payslip_category.each do |pc|
        unless pc.is_deduction == false
          @individual_category_deductionable = @individual_category_deductionable + "#{sprintf("%.2f",(pc.amount || 0.0))}".to_f
        end
      end

      @non_deductionable_amount = 0.0
      @deductionable_amount = 0.0
      @monthly_payslips.each do |mp|
        category1 = PayrollCategory.find(mp.payroll_category_id)
        unless category1.is_deduction == true
          @non_deductionable_amount = @non_deductionable_amount + "#{sprintf("%.2f",(mp.amount || 0.0))}".to_f if mp.amount.present?
        end
      end

      @monthly_payslips.each do |mp|
        category2 = PayrollCategory.find(mp.payroll_category_id)
        unless category2.is_deduction == false
          @deductionable_amount = @deductionable_amount + "#{sprintf("%.2f",(mp.amount || 0.0))}".to_f if mp.amount.present?
        end
      end

      @net_non_deductionable_amount = @individual_category_non_deductionable + @non_deductionable_amount
      @net_deductionable_amount = @individual_category_deductionable + @deductionable_amount

      @net_amount = @net_non_deductionable_amount - @net_deductionable_amount
      render :update do |page|
        page.replace_html "payslip_view", :partial => "view_payslip"
      end
    else
      flash[:notice]="#{t('flash_msg4')}"
      redirect_to :controller => 'user', :action => 'dashboard'
    end
  end

  def delete_payslip
    @individual_payslip_category=IndividualPayslipCategory.find_all_by_employee_id_and_salary_date(params[:id],params[:id2])
    @individual_payslip_category.each do |pc|
      pc.destroy
    end
    @monthly_payslip = MonthlyPayslip.find_all_by_employee_id_and_salary_date(params[:id], params[:id2])
    @monthly_payslip.each do |m|
      m.destroy
    end
    flash[:notice]= "#{t('flash30')} #{params[:id2]}"
    redirect_to :controller=>"employee", :action=>"profile", :id=>params[:id]
  end
  
  def employee_attendance
    @employee = Employee.find_by_user_id(current_user.id)
    @reporting_employees = Employee.find_all_by_reporting_manager_id(@employee.user_id)
    @total_leave_count = 0
    @reporting_employees.each do |e|
      @app_leaves = ApplyLeave.count(:conditions=>["employee_id =? AND viewed_by_manager =?", e.id, false])
      @total_leave_count = @total_leave_count + @app_leaves
    end
    @config = Configuration.find_by_config_key('LeaveSectionManager')
    if (@config.blank? or @config.config_value.blank? or @config.config_value.to_i != 1)
      @app_leaves = ApplyLeaveStudent.count(:conditions=>["viewed_by_teacher is NULL or viewed_by_teacher=?",false])
    else
      @app_leaves = ApplyLeaveStudent.count(:conditions=>["(viewed_by_teacher is NULL or viewed_by_teacher=?) and forward=?",false,true])
    end  
    @total_leave_count = @total_leave_count + @app_leaves
  end

  def view_attendance
    @employee = Employee.find(params[:id])
    @attendance_report = EmployeeAttendance.find_all_by_employee_id(@employee.id)
    @leave_types = EmployeeLeaveType.find(:all, :conditions => "status = true")
    @leave_count = EmployeeLeave.find_all_by_employee_id(@employee,:joins=>:employee_leave_type,:conditions=>"status = true")
    render :partial => "attendance_report"
  end

  def employee_leave_count_edit
    @leave_count = EmployeeLeave.find_by_id(params[:id])
    @leave_type = EmployeeLeaveType.find_by_id(params[:leave_type])

    render :update do |page|
      page.replace_html 'profile-infos', :partial => 'edit_leave_count'
    end
  end

  def employee_leave_count_update
    available_leave = params[:leave_count][:leave_count]
    leave = EmployeeLeave.find_by_id(params[:id])
    leave.update_attributes(:leave_count => available_leave.to_f)
    @employee = Employee.find(leave.employee_id)
    @attendance_report = EmployeeAttendance.find_all_by_employee_id(@employee.id)
    @leave_types = EmployeeLeaveType.find(:all, :conditions => "status = true")
    @leave_count = EmployeeLeave.find_all_by_employee_id(@employee,:joins=>:employee_leave_type,:conditions=>"status = true")
    @total_leaves = 0
    @leave_types.each do |lt|
      leave_count = EmployeeAttendance.find_all_by_employee_id_and_employee_leave_type_id(@employee.id,lt.id).size
      @total_leaves = @total_leaves + leave_count
    end
    render :update do |page|
      page.replace_html 'profile-infos',:partial => "attendance_report"
    end
  end

  def subject_assignment
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    @batches = Batch.active
    @subjects = []
  end

  def update_subjects
    unless params[:batch_id].nil?
      @batch_id = params[:batch_id]
    else
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
        
        params[:batch_id] = 0
        unless @batch_data.nil?
          params[:batch_id] = @batch_data.id 
        end
      else
        batch = Batch.find(params[:batch_id])
        params[:batch_id] = batch.id
      end
    end
    
    batch = Batch.find(params[:batch_id])
    @subjects = Subject.find_all_by_batch_id(batch.id,:conditions=>"is_deleted=false",:order=>'name ASC')

    render :update do |page|
      page.replace_html 'subjects1', :partial => 'subjects', :object => @subjects
    end
  end

  def select_department
    @subject = Subject.find(params[:subject_id])
    @assigned_employee = EmployeesSubject.find_all_by_subject_id(@subject.id).sort_by{|s| s.employee.full_name.downcase}
    @departments = EmployeeDepartment.find(:all, :conditions =>{:status=>true},:order=>'name ASC')
    render :update do |page|
      page.replace_html 'department-select', :partial => 'select_department'
    end
  end

  def update_employees
    @subject = Subject.find(params[:subject_id])
    assigned_employee = EmployeesSubject.find_all_by_subject_id(@subject.id, :select => :employee_id).map(&:employee_id)
    if assigned_employee.present?
      @employees = Employee.find_all_by_employee_department_id(params[:department_id],:include=>:user,:conditions=>["users.admin=? AND employees.id NOT in (#{assigned_employee.join(',')})",false]).sort_by{|s| s.full_name.downcase}
    else
      @employees = Employee.find_all_by_employee_department_id(params[:department_id]).sort_by{|s| s.full_name.downcase}
    end
    @department_id = params[:department_id]
    render :update do |page|
      page.replace_html 'employee-list', :partial => 'employee_list'
    end
  end

  def assign_employee
    @departments = EmployeeDepartment.find(:all,:conditions =>{:status=>true},:order=>'name ASC')
    @subject = Subject.find(params[:id1])
    EmployeesSubject.create(:employee_id => params[:id], :subject_id => params[:id1])
    @department_id = Employee.find(params[:id]).employee_department_id
    @assigned_employee = EmployeesSubject.find_all_by_subject_id(@subject.id).sort_by{|s| s.employee.full_name.downcase}
    if @assigned_employee.present?
      @employees = Employee.find_all_by_employee_department_id(@department_id, :include => :user, :conditions => ["users.admin=? AND employees.id NOT in (#{@assigned_employee.map(&:employee_id).join(',')})",false]).sort_by{|s| s.full_name.downcase}
    else
      @employees = Employee.find_all_by_employee_department_id(@department_id, :include => :user, :conditions => ["users.admin=?",false]).sort_by{|s| s.full_name.downcase}
    end
    @show_employees = true
    flash[:notice]="#{t('subject_assigned_successfully')}"
    render :update do |page|
      page.replace_html 'department-select', :partial => 'select_department'
      page.replace_html 'employee-list', :partial => 'employee_list'
    end
  end

  def remove_employee
    @department_id = Employee.find(params[:id]).employee_department_id
    @departments = EmployeeDepartment.find(:all,:conditions =>{:status=>true})
    @subject = Subject.find(params[:id1])
    if TimetableEntry.find_all_by_subject_id_and_employee_id(@subject.id,params[:id]).blank?
      EmployeesSubject.find_by_employee_id_and_subject_id(params[:id], params[:id1]).destroy
      flash[:notice]="#{t('subject_de_assigned_successfully')}"
    else
      flash.now[:warn_notice]="<p>#{t('employee.flash41')}</p> <p>#{t('employee.flash42')}</p> "
    end
    @assigned_employee = EmployeesSubject.find_all_by_subject_id(@subject.id).sort_by{|s| s.employee.full_name.downcase}
    if @assigned_employee.present?
      @employees = Employee.find_all_by_employee_department_id(@department_id, :include => :user, :conditions => ["users.admin=? AND employees.id NOT in (#{@assigned_employee.map(&:employee_id).join(',')})",false]).sort_by{|s| s.full_name.downcase}
    else
      @employees = Employee.find_all_by_employee_department_id(@department_id, :include => :user, :conditions => ["users.admin=?",false]).sort_by{|s| s.full_name.downcase}
    end
    render :update do |page|
      page.replace_html 'department-select', :partial => 'select_department'
      page.replace_html 'employee-list', :partial => 'employee_list'
    end
  end

  #HR Management special methods...

  def hr
    user = current_user
    @employee = user.employee_record
    
    permitted_modules = Rails.cache.fetch("permitted_modules_human_resource_#{current_user.id}"){
      @human_resource_modules_tmp = []
      @a_user_modules = ['human_resource']
      menu_links = MenuLink.find_by_name(@a_user_modules)
      menu_id = menu_links.id
      menu_links = MenuLink.find_all_by_higher_link_id(menu_id)
      
      menu_links.each do |menu_link|
        if menu_link.link_type=="user_menu"
            menu_id = menu_link.id

            school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")

            if school_menu_links.nil? or school_menu_links.blank?
               @human_resource_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => false}
            else
               @human_resource_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => true}
            end
        else
          @human_resource_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => true}
        end
      end
      @human_resource_modules_tmp
    }
    @human_resource_modules = permitted_modules
  end

  def select_department_employee
    @departments = EmployeeDepartment.active.find(:all,:order=>'name ASC')
    @employees = []
  end

  def rejected_payslip
    @departments = EmployeeDepartment.active.find(:all,:order=>'name ASC')
    @employees = []
  end

  def update_rejected_employee_list
    department_id = params[:department_id]
    #@employees = Employee.find_all_by_employee_department_id(department_id)
    @employees = MonthlyPayslip.find(:all, :conditions =>"is_rejected is true", :group=>'employee_id', :joins=>"INNER JOIN employees on monthly_payslips.employee_id = employees.id")
    @employees.reject!{|x| x.employee.employee_department_id != department_id.to_i}

    render :update do |page|
      page.replace_html 'employees_select_list', :partial => 'rejected_employee_select_list', :object => @employees
    end
  end

  def edit_rejected_payslip
    salary_date_params = params[:id2] || params[:salary_date]
    @salary_date = 
      begin
      Date.parse(salary_date_params)
    rescue ArgumentError
    end
    unless @salary_date.present?
      flash[:notice] = "#{t('date_invalid')}"
      redirect_to :controller => "user", :action => "dashboard"
    end
    @employee = Employee.find(params[:id])
    @monthly_payslips = MonthlyPayslip.find_all_by_salary_date_and_employee_id(@salary_date,@employee.id,:select=>"monthly_payslips.*,payroll_categories.name as category_name",:order=> "payroll_category_id ASC",:joins=>[:payroll_category])
    @individual_payslips = IndividualPayslipCategory.find_all_by_employee_id_and_salary_date(@employee.id,@salary_date)
    if request.xhr?
      flash[:notice]=nil
      salary_date = Date.parse(params[:salary_date])
      error=0
      start_date = salary_date - ((salary_date.day - 1).days)
      end_date = start_date + 1.month
      unless end_date.to_date < @employee.joining_date.to_date
        if params[:manage_payroll].present?
          payslip_exists = MonthlyPayslip.find_all_by_employee_id(@employee.id,:conditions => ["salary_date >= ? and salary_date < ?", start_date, end_date])
          unless payslip_exists == [] or  payslip_exists.collect(&:is_rejected).include? false
            ActiveRecord::Base.transaction do
              error=1  unless @employee.update_attributes(:monthly_payslips_attributes=>params[:manage_payroll][:monthly_payslips_attributes])
              if params[:new_category].present?
                error=1  unless @employee.update_attributes(:individual_payslip_categories_attributes=>params[:new_category][:individual_payslip_categories_attributes])
              end
              if error==1
                raise ActiveRecord::Rollback
              end
            end
            flash[:notice] = "#{@employee.first_name} #{t('flash27')} #{params[:salary_date]}"
          else
            flash[:notice] = "#{@employee.first_name} #{t('flash28')} #{params[:salary_date]}"
          end
        else
          error=1
          @employee.errors.add_to_base("#{t('flash51')}")
        end
      else
        error=1
        @employee.errors.add_to_base("#{t('flash45')} #{params[:salary_date]}")
      end
      if error==1
        render :update do |page|
          page.replace_html 'errors', :partial => 'errors', :object => @employee
        end
      else
        privilege = Privilege.find_by_name("FinanceControl")
        available_user_ids = privilege.user_ids
        subject = "#{t('rejected_payslip_regenerated')}"
        body = "#{t('payslip_has_been_generated_for')}"+@employee.first_name+" "+@employee.last_name + " (#{t('employee_number')} :#{@employee.employee_number})" + " #{t('for_the_month')} #{salary_date.to_date.strftime("%B %Y")}. #{t('kindly_approve')}"
        Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
            :recipient_ids => available_user_ids,
            :subject=>subject,
            :body=>body ))
        render :update do |page|
          page.redirect_to :controller => "employee", :action => "rejected_payslip"
        end
      end
    end
  end
  def update_rejected_payslip
    @salary_date = params[:salary_date]
    @employee = Employee.find(params[:emp_id])
    @currency_type = currency

    if params[:salary_date] == ""
      render :update do |page|
        page.replace_html "rejected_payslip", :text => ""
      end
      return
    end
    @monthly_payslips = MonthlyPayslip.find_all_by_salary_date(@salary_date,
      :conditions=> "employee_id =#{params[:emp_id]}",
      :order=> "payroll_category_id ASC")

    @individual_payslip_category = IndividualPayslipCategory.find_all_by_salary_date(@salary_date,
      :conditions=>"employee_id =#{params[:emp_id]}",
      :order=>"id ASC")
    @individual_category_non_deductionable = 0
    @individual_category_deductionable = 0
    @individual_payslip_category.each do |pc|
      unless pc.is_deduction == true
        @individual_category_non_deductionable = @individual_category_non_deductionable + "#{sprintf("%.2f",(pc.amount || 0.0))}".to_f
      end
    end

    @individual_payslip_category.each do |pc|
      unless pc.is_deduction == false
        @individual_category_deductionable = @individual_category_deductionable + "#{sprintf("%.2f",(pc.amount || 0.0))}".to_f
      end
    end

    @non_deductionable_amount = 0
    @deductionable_amount = 0
    @monthly_payslips.each do |mp|
      category1 = PayrollCategory.find(mp.payroll_category_id)
      unless category1.is_deduction == true
        @non_deductionable_amount = @non_deductionable_amount + "#{sprintf("%.2f",(mp.amount || 0.0))}".to_f
      end
    end

    @monthly_payslips.each do |mp|
      category2 = PayrollCategory.find(mp.payroll_category_id)
      unless category2.is_deduction == false
        @deductionable_amount = @deductionable_amount + "#{sprintf("%.2f",(mp.amount || 0.0))}".to_f
      end
    end

    @net_non_deductionable_amount = @individual_category_non_deductionable + @non_deductionable_amount
    @net_deductionable_amount = @individual_category_deductionable + @deductionable_amount

    @net_amount = @net_non_deductionable_amount - @net_deductionable_amount

    render :update do |page|
      page.replace_html 'rejected_payslip', :partial => 'rejected_payslip'
    end
  end
  def view_rejected_payslip

    @payslips = MonthlyPayslip.find_all_by_employee_id(params[:id], :conditions =>"is_rejected is true", :group=>'salary_date')
    @emp = Employee.find(params[:id])
  end

  def update_employee_select_list
    department_id = params[:department_id]
    @employees = Employee.find_all_by_employee_department_id(department_id)
    @employees = @employees.sort_by { |u1| [u1.full_name.to_s.downcase ] } if @employees.present?
    render :update do |page|
      page.replace_html 'employees_select_list', :partial => 'employee_select_list', :object => @employees
    end
  end

  def payslip_date_select
    render :partial=>"one_click_payslip_date"
  end

  def one_click_payslip_generation

    @user = current_user
    finance_manager = find_finance_managers
    finance = Configuration.find_by_config_value("Finance")
    subject = "#{t('payslip_generated')}"
    body = "#{t('message_body')}"
    salary_date = Date.parse(params[:salary_date])
    start_date = salary_date - ((salary_date.day - 1).days)
    end_date = start_date + 1.month
    employees = Employee.find(:all)
    unless(finance_manager.nil? and finance.nil?)
      finance_manager_ids = Privilege.find_by_name('FinanceControl').user_ids
      Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => @user.id,
          :recipient_ids => finance_manager_ids,
          :subject=>subject,
          :body=>body ))
      employees.each do|e|
        payslip_exists = MonthlyPayslip.find_all_by_employee_id(e.id,
          :conditions => ["salary_date >= ? and salary_date < ?", start_date, end_date])
        if payslip_exists == []
          salary_structure = EmployeeSalaryStructure.find_all_by_employee_id(e.id)
          unless salary_structure == []
            salary_structure.each do |ss|
              MonthlyPayslip.create(:salary_date=>start_date,
                :employee_id=>e.id,
                :payroll_category_id=>ss.payroll_category_id,
                :amount=>ss.amount,:is_approved => false,:approver => nil)
            end
          end
        end
      end
    else
      employees.each do|e|
        payslip_exists = MonthlyPayslip.find_all_by_employee_id(e.id,
          :conditions => ["salary_date >= ? and salary_date < ?", start_date, end_date])
        if payslip_exists == []
          salary_structure = EmployeeSalaryStructure.find_all_by_employee_id(e.id)
          unless salary_structure == []
            salary_structure.each do |ss|
              MonthlyPayslip.create(:salary_date=>start_date,
                :employee_id=>e.id,
                :payroll_category_id=>ss.payroll_category_id,
                :amount=>ss.amount,:is_approved => true,:approver => @user.id)
            end
          end
        end
      end
    end
    render :text => "<p>#{t('salary_slip_for_month')}: #{salary_date.strftime("%B")}.<br/><b>#{t('note')}:</b> #{t('employees_salary_generated_manually')}</p>"
  end

  def payslip_revert_date_select
    @salary_dates = MonthlyPayslip.find(:all, :select => "distinct salary_date",:conditions=>{:is_approved=>false},:order=>"salary_date DESC")
    render :partial=>"one_click_payslip_revert_date"
  end

  def one_click_payslip_revert
    unless params[:one_click_payslip][:salary_date] == ""
      salary_date = Date.parse(params[:one_click_payslip][:salary_date])
      start_date = salary_date - ((salary_date.day - 1).days)
      end_date = start_date + 1.month
      employees = Employee.find(:all)
      employees.each do|e|
        payslip_record = MonthlyPayslip.find_all_by_employee_id(e.id,
          :conditions => ["salary_date >= ? and salary_date < ?", start_date, end_date])
        payslip_record.each do |pr|
          pr.destroy unless pr.is_approved
        end
        payslip_record = MonthlyPayslip.find_all_by_employee_id(e.id,
          :conditions => ["salary_date >= ? and salary_date < ?", start_date, end_date])

        if payslip_record.empty?
          individual_payslip_record = IndividualPayslipCategory.find_all_by_employee_id(e.id,
            :conditions => ["salary_date >= ? and salary_date < ?", start_date, end_date])
          unless individual_payslip_record.nil?
            individual_payslip_record.each do|ipr|
              ipr.destroy
            end
          end
        end
      end
      render :text=> "<p>#{t('salary_slip_reverted')}: #{salary_date.strftime("%B")}.</p>"
    else
      render :text=>"<p>#{t('please_select_month')}</p>"
    end
  end

  def leave_management
    user = current_user
    @employee = user.employee_record
    @all_employee = Employee.find(:all)
    @reporting_employees = Employee.find_all_by_reporting_manager_id(@employee.user_id)
    @leave_types = EmployeeLeaveType.find(:all)
    @total_leave_count = 0
    @reporting_employees.each do |e|
      @app_leaves = ApplyLeave.count(:conditions=>["employee_id =? AND viewed_by_manager =?", e.id, false])
      @total_leave_count = @total_leave_count + @app_leaves
    end
    @all_employee_total_leave_count = 0
    @all_employee.each do |a|
      @all_emp_app_leaves = ApplyLeave.count(:conditions=>["employee_id =? AND viewed_by_manager =?" , a.id, false])
      @all_employee_total_leave_count = @all_employee_total_leave_count + @all_emp_app_leaves
    end

    @leave_apply = ApplyLeave.new(params[:leave_apply])
    if request.post? and @leave_apply.save
      leaves_half_day = ApplyLeave.count(:all,:conditions=>{:employee_id=>params[:leave_apply][:employee_id],:start_date=>params[:leave_apply][:start_date],:end_date=>params[:leave_apply][:end_date],:is_half_day=>true})
      leaves = ApplyLeave.count(:all,:conditions=>{:approved => true, :employee_id=>params[:leave_apply][:employee_id],:start_date=>params[:leave_apply][:start_date],:end_date=>params[:leave_apply][:end_date]})
      already_apply = ApplyLeave.count(:all,:conditions=>{:approved => nil, :employee_id=>params[:leave_apply][:employee_id],:start_date=>params[:leave_apply][:start_date],:end_date=>params[:leave_apply][:end_date]})
      if(leaves == 0 and already_apply == 0) or (leaves <= 1 and leaves_half_day < 2)
        unless leaves_half_day == 1 and params[:leave_apply][:is_half_day]=='0'
          if @leave_apply.save
            ApplyLeave.update(@leave_apply, :approved=> nil, :viewed_by_manager=> false)
            flash[:notice]= "#{t('flash30')}"
            redirect_to :controller => "employee", :action=> "leave_management", :id=>@employee.id
          end
        else
          @leave_apply.errors.add_to_base("#{t('half_day_alredy_applied')}")
        end
      else
        @leave_apply.errors.add_to_base("#{t('already_applied')}")
      end
    end
  end
  
  def all_employee_leave_applications

    @employee = Employee.find(params[:id])
    @departments = EmployeeDepartment.find(:all, :order=>"name ASC")
    @employees = []
    render :partial=> "all_employee_leave_applications"
  end

  def update_employees_select
    @employee = params[:emp_id]
    department_id = params[:department_id]
    @employees = Employee.find_all_by_employee_department_id(department_id)

    render :update do |page|
      page.replace_html 'employees_select', :partial => 'employee_select', :object => @employees
    end
  end

  def leave_list
    if params[:employee_id] == ""
      render :update do |page|
        page.replace_html "leave-list", :text => "none"
      end
      return
    end
    @employee = params[:emp_id]
    @pending_applied_leaves = ApplyLeave.find_all_by_employee_id(params[:employee_id], :conditions=> "approved = false AND viewed_by_manager = false", :order=>"start_date DESC")
    @applied_leaves = ApplyLeave.find_all_by_employee_id(params[:employee_id], :conditions=> "viewed_by_manager = true", :order=>"start_date DESC")
    @all_leave_applications = ApplyLeave.find_all_by_employee_id(params[:employee_id])
    render :update do |page|
      page.replace_html "leave-list", :partial => "leave_list"
    end
  end

  def department_payslip
    @departments = EmployeeDepartment.find(:all, :conditions=>"status = true", :order=> "name ASC")
    @salary_dates = MonthlyPayslip.find(:all,:select => "distinct salary_date")
    if request.post?
      post_data = params[:payslip]
      unless post_data.blank?
        if post_data[:salary_date].present? and post_data[:department_id].present?
          @payslips = MonthlyPayslip.find_and_filter_by_department(post_data[:salary_date],post_data[:department_id])
        else
          flash[:notice] = "#{t('select_salary_date')}"
          redirect_to :action=>"department_payslip"
        end
      end
    end
  end

  def view_employee_payslip
    @monthly_payslips = MonthlyPayslip.find(:all,:conditions=>["employee_id=? AND salary_date = ?",params[:id],params[:salary_date]],:include=>:payroll_category)
    @individual_payslips =  IndividualPayslipCategory.find(:all,:conditions=>["employee_id=? AND salary_date = ?",params[:id],params[:salary_date]])
    @salary  = Employee.calculate_salary(@monthly_payslips, @individual_payslips)
    if @monthly_payslips.blank?
      flash[:notice] = "#{t('no_paylips_found_for_this_employee')}"
      redirect_to :controller => "employee", :action => "profile", :id => params[:id]
    end
  end

  #PDF methods

  def view_employee_payslip_pdf
    @employee = Employee.find(:first,:conditions => {:id => params[:id]})
    @employee ||= ArchivedEmployee.find(:first,:conditions => {:former_id => params[:id]})
    @monthly_payslips = MonthlyPayslip.find(:all,:conditions=>["employee_id=? AND salary_date = ?",params[:id],params[:salary_date]],:include=>:payroll_category)
    @individual_payslips =  IndividualPayslipCategory.find(:all,:conditions=>["employee_id=? AND salary_date = ?",params[:id],params[:salary_date]])
    @salary  = Employee.calculate_salary(@monthly_payslips, @individual_payslips)
    @salary_date = params[:salary_date] if params[:salary_date]
  end

  def department_payslip_pdf
    @department = EmployeeDepartment.find(params[:department])
    @employees = Employee.find_all_by_employee_department_id(@department.id)


    @currency_type = currency
    @salary_date = params[:salary_date] if params[:salary_date]

    render :pdf => 'department_payslip_pdf',
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 30,
      :right => 30}
    #    respond_to do |format|
    #      format.pdf { render :layout => false }
    #    end

  end

  def individual_payslip_pdf
    @employee = Employee.find(params[:id])
    @department = EmployeeDepartment.find(@employee.employee_department_id).name
    @currency_type = currency
    @category = EmployeeCategory.find(@employee.employee_category_id).name
    @grade = EmployeeGrade.find(@employee.employee_grade_id).name unless @employee.employee_grade_id.nil?
    @position = EmployeePosition.find(@employee.employee_position_id).name
    @salary_date =
      begin
      Date.parse(params[:id2])
    rescue ArgumentError
    end
    unless @salary_date.present?
      flash[:notice] = "#{t('date_invalid')}"
      redirect_to :controller => "employee", :action => "profile", :id => @employee.id and return
    end
    @bank_details = EmployeeBankDetail.find_all_by_employee_id(@employee.id)
    @monthly_payslips = MonthlyPayslip.find_all_by_salary_date_and_employee_id(@salary_date,params[:id],:order=> "payroll_category_id ASC")

    @individual_payslip_category = IndividualPayslipCategory.find_all_by_salary_date_and_employee_id(@salary_date,params[:id],:order=>"id ASC")
    @individual_category_non_deductionable = 0
    @individual_category_deductionable = 0
    @individual_payslip_category.each do |pc|
      unless pc.is_deduction == true
        @individual_category_non_deductionable = @individual_category_non_deductionable + "#{sprintf("%.2f",(pc.amount || 0.0))}".to_f
      end
    end

    @individual_payslip_category.each do |pc|
      unless pc.is_deduction == false
        @individual_category_deductionable = @individual_category_deductionable + "#{sprintf("%.2f",(pc.amount || 0.0))}".to_f
      end
    end

    @non_deductionable_amount = 0
    @deductionable_amount = 0
    @monthly_payslips.each do |mp|
      category1 = PayrollCategory.find(mp.payroll_category_id)
      unless category1.is_deduction == true
        @non_deductionable_amount = @non_deductionable_amount + "#{sprintf("%.2f",(mp.amount || 0.0))}".to_f
      end
    end

    @monthly_payslips.each do |mp|
      category2 = PayrollCategory.find(mp.payroll_category_id)
      unless category2.is_deduction == false
        @deductionable_amount = @deductionable_amount + "#{sprintf("%.2f",(mp.amount || 0.0))}".to_f
      end
    end

    @net_non_deductionable_amount = @individual_category_non_deductionable + @non_deductionable_amount
    @net_deductionable_amount = @individual_category_deductionable + @deductionable_amount

    @net_amount = @net_non_deductionable_amount - @net_deductionable_amount
    if @monthly_payslips.blank?
      flash[:notice] = "#{t('no_payslips_found')}"
      redirect_to :controller => "employee", :action => "profile", :id => params[:id]
    else
      render :pdf => 'individual_payslip_pdf'
    end



    #    respond_to do |format|
    #      format.pdf { render :layout => false }
    #    end
  end
  def employee_individual_payslip_pdf
    @employee = Employee.find_by_id(params[:id])
    unless @employee.present?
      @employee = ArchivedEmployee.find_by_former_id(params[:id])
      if @employee.present?
        @employee.id = @employee.former_id
      else
        flash[:notice] = "#{t('employee_does_not_exist')}"
        redirect_to :controller => "user", :action => "dashboard" and return
      end
    end
    @bank_details = EmployeeBankDetail.find_all_by_employee_id(@employee.id)
    @department = EmployeeDepartment.find(@employee.employee_department_id).name
    @currency_type = currency
    @category = EmployeeCategory.find(@employee.employee_category_id).name
    @grade = EmployeeGrade.find(@employee.employee_grade_id).name unless @employee.employee_grade_id.nil?
    @position = EmployeePosition.find(@employee.employee_position_id).name
    @salary_date =
      begin
      Date.parse(params[:id2])
    rescue ArgumentError
    end
    unless @salary_date.present?
      flash[:notice] = "#{t('date_invalid')}"
      redirect_to :controller => "employee", :action => "profile", :id => @employee.id and return
    end
    @monthly_payslips = MonthlyPayslip.find_all_by_salary_date_and_employee_id(@salary_date,params[:id],:order=> "payroll_category_id ASC")

    @individual_payslip_category = IndividualPayslipCategory.find_all_by_salary_date_and_employee_id(@salary_date,params[:id],:order=>"id ASC")
    @individual_category_non_deductionable = 0
    @individual_category_deductionable = 0
    @individual_payslip_category.each do |pc|
      unless pc.is_deduction == true
        @individual_category_non_deductionable = @individual_category_non_deductionable + "#{sprintf("%.2f",(pc.amount || 0.0))}".to_f
      end
    end

    @individual_payslip_category.each do |pc|
      unless pc.is_deduction == false
        @individual_category_deductionable = @individual_category_deductionable + "#{sprintf("%.2f",(pc.amount || 0.0))}".to_f
      end
    end

    @non_deductionable_amount = 0
    @deductionable_amount = 0
    @monthly_payslips.each do |mp|
      category1 = PayrollCategory.find(mp.payroll_category_id)
      unless category1.is_deduction == true
        @non_deductionable_amount = @non_deductionable_amount + "#{sprintf("%.2f",(mp.amount || 0.0))}".to_f
      end
    end

    @monthly_payslips.each do |mp|
      category2 = PayrollCategory.find(mp.payroll_category_id)
      unless category2.is_deduction == false
        @deductionable_amount = @deductionable_amount + "#{sprintf("%.2f",(mp.amount || 0.0))}".to_f
      end
    end

    @net_non_deductionable_amount = @individual_category_non_deductionable + @non_deductionable_amount
    @net_deductionable_amount = @individual_category_deductionable + @deductionable_amount

    @net_amount = @net_non_deductionable_amount - @net_deductionable_amount

    if @monthly_payslips.blank?
      flash[:notice] = "#{t('no_payslips_found')}"
      redirect_to :controller => "employee", :action => "profile", :id => params[:id]
    else
      render :pdf => 'individual_payslip_pdf'
    end
    #    respond_to do |format|
    #      format.pdf { render :layout => false }
    #    end
  end
  def advanced_search
    @search = Employee.search(params[:search])
    @sort_order=""
    @sort_order=params[:sort_order] if  params[:sort_order]
    if params[:search]
      if params[:search][:status_equals]=="true"
        @employees = Employee.ascend_by_first_name.search(params[:search]).paginate(:page => params[:page], :per_page => 30)
        #        @employees1 = @search.all
        #        @employees2 = []
      elsif params[:search][:status_equals]=="false"
        @employees = ArchivedEmployee.ascend_by_first_name.search(params[:search]).paginate(:page => params[:page], :per_page => 30)
        #        @employees1 = @search.all
        #        @employees2 = []
      else
        @employees = [{:employee => {:search_options => params[:search], :order => :first_name}}, {:archived_employee => {:search_options => params[:search], :order => :first_name}}].model_paginate(:page => params[:page],:per_page => 30)
        #        @search1 = Employee.search(params[:search]).all
        #        @search2 = ArchivedEmployee.search(params[:search]).all
        #        @employees1 = @search1
        #        @employees2 = @search2
      end
    end
  end

  def list_doj_year
    doj_option = params[:doj_option]
    if doj_option == "equal_to"
      render :update do |page|
        page.replace_html 'doj_year', :partial=>"equal_to_select"
      end
    elsif doj_option == "less_than"
      render :update do |page|
        page.replace_html 'doj_year', :partial=>"less_than_select"
      end
    else
      render :update do |page|
        page.replace_html 'doj_year', :partial=>"greater_than_select"
      end
    end
  end

  def doj_equal_to_update
    year = params[:year]
    @start_date = "#{year}-01-01".to_date
    @end_date = "#{year}-12-31".to_date
    render :update do |page|
      page.replace_html 'doj_year_hidden', :partial=>"equal_to_doj_select"
    end
  end

  def doj_less_than_update
    year = params[:year]
    @start_date = "1900-01-01".to_date
    @end_date = "#{year}-01-01".to_date
    render :update do |page|
      page.replace_html 'doj_year_hidden', :partial=>"less_than_doj_select"
    end
  end

  def doj_greater_than_update
    year = params[:year]
    @start_date = "2100-01-01".to_date
    @end_date = "#{year}-12-31".to_date
    render :update do |page|
      page.replace_html 'doj_year_hidden', :partial=>"greater_than_doj_select"
    end
  end

  def list_dob_year
    dob_option = params[:dob_option]
    if dob_option == "equal_to"
      render :update do |page|
        page.replace_html 'dob_year', :partial=>"equal_to_select_dob"
      end
    elsif dob_option == "less_than"
      render :update do |page|
        page.replace_html 'dob_year', :partial=>"less_than_select_dob"
      end
    else
      render :update do |page|
        page.replace_html 'dob_year', :partial=>"greater_than_select_dob"
      end
    end
  end

  def dob_equal_to_update
    year = params[:year]
    @start_date = "#{year}-01-01".to_date
    @end_date = "#{year}-12-31".to_date
    render :update do |page|
      page.replace_html 'dob_year_hidden', :partial=>"equal_to_dob_select"
    end
  end

  def dob_less_than_update
    year = params[:year]
    @start_date = "1900-01-01".to_date
    @end_date = "#{year}-01-01".to_date
    render :update do |page|
      page.replace_html 'dob_year_hidden', :partial=>"less_than_dob_select"
    end
  end

  def dob_greater_than_update
    year = params[:year]
    @start_date = "2100-01-01".to_date
    @end_date = "#{year}-12-31".to_date
    render :update do |page|
      page.replace_html 'dob_year_hidden', :partial=>"greater_than_dob_select"
    end
  end

  def remove
    @employee = Employee.find(params[:id])
    if current_user == @employee.user
      flash[:notice] = "#{t('you_cannot_delete_your_own_profile')}"
      redirect_to :controller => "user", :action => "dashboard" and return
    else
      associate_employee = Employee.find(:all, :conditions=>["reporting_manager_id=#{@employee.user_id}"])
      unless associate_employee.blank?
        flash[:notice] = "#{t('flash35')}"
        redirect_to :action=>'remove_subordinate_employee', :id=>@employee.id
      end
    end
  end

  def remove_subordinate_employee
    @current_manager = Employee.find(params[:id])
    @associate_employee = Employee.find(:all, :conditions=>["reporting_manager_id=#{@current_manager.user_id}"])
    @departments = EmployeeDepartment.find(:all)
    @categories  = EmployeeCategory.find(:all)
    @positions   = EmployeePosition.find(:all)
    @grades      = EmployeeGrade.find(:all)
    if request.post?
      manager = Employee.find_by_id(params[:employee][:reporting_manager_id])
      @associate_employee.each do |e|
        if manager.present?
          e.update_attributes(:reporting_manager_id => manager.user_id)
        else
          e.update_attributes(:reporting_manager_id => nil)
        end
      end
      redirect_to :action => "remove", :id=>@current_manager.id
    end
  end

  def change_to_former
    @employee = Employee.find(params[:id])
    @dependency = @employee.former_dependency
    if request.post?
      if current_user == @employee.user
        flash[:notice] = "#{t('you_cannot_delete_your_own_profile')}"
        redirect_to :controller => "user", :action => "dashboard" and return
      else
        flash[:notice]= "#{t('employee_text')} - #{@employee.employee_number} #{t('flash46')}"
        EmployeesSubject.destroy_all(:employee_id=>@employee.id)
        @employee.archive_employee(params[:remove][:status_description])
        redirect_to :action => "hr"
      end
    end
  end

  def delete
    employee = Employee.find(params[:id])
    if current_user == employee.user
      flash[:notice] = "#{t('you_cannot_delete_your_own_profile')}"
      redirect_to :controller => "user", :action => "dashboard" and return
    else
      unless employee.has_dependency
        employee_subject=EmployeesSubject.destroy_all(:employee_id=>employee.id)
        
        champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
        api_endpoint = champs21_api_config['api_url']
        uri = URI(api_endpoint + "api/user/delete_by_paid_id")
        http = Net::HTTP.new(uri.host, uri.port)
        auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
        auth_req.set_form_data({"paid_id" => employee.user.id, "paid_school_id" => MultiSchool.current_school.id})
        auth_res = http.request(auth_req)
        @auth_response = JSON::parse(auth_res.body)

        employee.user.destroy
        employee.destroy
        flash[:notice] = "#{t('flash32')} #{employee.employee_number}."
        redirect_to :controller => 'user', :action => 'dashboard'
      else
        flash[:notice] = "#{t('flash44')}"
        redirect_to  :action => 'remove' ,:id=>employee.id
      end
    end
  end

  def advanced_search_pdf
    @employee_ids = params[:result]
    @searched_for = params[:for]
    @status = params[:status]
    @employees = []
    if params[:status] == 'true'
      @search = Employee.ascend_by_first_name.search(params[:search])
      @employees += @search.all
      #      @employee_ids.each do |s|
      #        employee = Employee.find(s)
      #        @employees1.push employee
      #      end
    elsif params[:status] == 'false'
      @search = ArchivedEmployee.ascend_by_first_name.search(params[:search])
      @employees += @search.all
      #      @employee_ids.each do |s|
      #        employee = ArchivedEmployee.find(s)
      #        @employees1.push employee
      #      end
    else
      @search1 = Employee.ascend_by_first_name.search(params[:search]).all
      @search2 = ArchivedEmployee.ascend_by_first_name.search(params[:search]).all
      @employees+=@search1+@search2
    end
    render :pdf => 'employee_advanced_search_pdf'
  end


  def payslip_approve
    @salary_dates = MonthlyPayslip.find(:all, :select => "distinct salary_date")
  end

  def one_click_approve
    @dates = MonthlyPayslip.find_all_by_salary_date(params[:salary_date],:conditions => ["is_approved = false"])
    @salary_date = params[:salary_date]
    render :update do |page|
      page.replace_html "approve",:partial=> "one_click_approve"
    end
  end

  def one_click_approve_submit
    dates = MonthlyPayslip.find_all_by_salary_date(Date.parse(params[:date]))

    dates.each do |d|
      d.approve(current_user.id)
    end
    flash[:notice] = "#{t('flash34')}"
    redirect_to :action => "hr"

  end
private
  def get_attendence_text
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    
    if current_user.student or current_user.employee
      homework_uri = URI(api_endpoint + "api/report/attendence")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @attendence_text = JSON::parse(homework_res.body)
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      homework_uri = URI(api_endpoint + "api/report/attendence")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"batch_id"=>student.batch_id,"student_id"=>student.id,"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @attendence_text = JSON::parse(homework_res.body)
    end
    
    @attendence_text
  end
  
end
private
def is_number?(num)
  /^[\d]+(\.[\d]+){0,1}$/ === num.to_s
end

 def delete_employee(emp_id)
    @employee = Employee.find_by_id(emp_id)
    unless @employee.nil?
      user_employee = User.find_by_id(@employee.user_id)
      unless user_employee.nil?
        user_employee.delete
      end  
      @employee.delete
    end
  end
  
  def activated_employee(emp_id)
    require 'net/http'
    require 'uri'
    require "yaml"
 
    @employee = Employee.find_by_id(emp_id)
    unless @employee.nil?
      @user_employee = User.find_by_id(@employee.user_id)
      
      unless @user_employee.nil?
        @user_employee.is_deleted = 0
        @user_employee.is_approved = 1
        @user_employee.save
       
        if @user_employee.free_user_id?
          champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
          api_endpoint = champs21_api_config['api_url']
          uri = URI(api_endpoint + "api/user/updateprofile")
          http = Net::HTTP.new(uri.host, uri.port)
          auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
          auth_req.set_form_data({"user_id" =>@user_employee.free_user_id, "paid_id" => @employee.id, "paid_username" => @user_employee.username, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s})
          auth_res = http.request(auth_req)
          @auth_response = ActiveSupport::JSON.decode(auth_res.body)
        end   
      end 
      
    end
  end
