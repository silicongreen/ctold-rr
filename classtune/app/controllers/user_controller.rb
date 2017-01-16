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

class UserController < ApplicationController
  layout :choose_layout
  before_filter :login_required, :except => [:new_student_registration,:logout,:get_section_data,:get_batches,:new_guardian, :forgot_password, :login, :set_new_password, :reset_password]
  before_filter :only_admin_allowed, :only => [:edit,:make_sibligns, :create, :index, :edit_privilege, :user_change_password,:delete,:list_user,:all]
  before_filter :protect_user_data, :only => [:profile, :user_change_password]
  before_filter :check_if_loggedin, :only => [:login]
  before_filter :check_permission,:only=>[:index]
  before_filter :default_time_zone_present_time
  
  # around_filter :cache_quick_links, :only => [:show_quick_links]

  def choose_layout
    return 'login' if action_name == 'login' or action_name == 'set_new_password'
    return 'forgotpw' if action_name == 'forgot_password' 
    return 'dashboard' if action_name == 'dashboard'
    'application'
  end
  
  def make_sibligns
    
    if request.post?
      @username1 = params[:user][:username1]
      @username2 = params[:user][:username2]
      @username3 = params[:user][:username3]
      @username4 = params[:user][:username4]
      @username5 = params[:user][:username5]
      
      @user_info1 = User.find_by_username(@username1)
       
      if !@user_info1.blank? && @user_info1.student
        @student_info1 = @user_info1.student_record
        @main_std_id = @student_info1.id
        @main_std_immediate_contact_id = @student_info1.immediate_contact_id
        @all_guardian = GuardianStudents.find_all_by_student_id(@main_std_id)
         
        if @username2
          @user_info2 = User.find_by_username(@username2)
          if !@user_info2.blank? && @user_info2.student
            @student_info2 = @user_info2.student_record
            @std_obj = Student.find @student_info2.id
            @std_obj.update_attributes(:immediate_contact_id => @main_std_immediate_contact_id)
            GuardianStudents.destroy_all(:student_id=>@std_obj.id)
            if !@all_guardian.blank?
              @all_guardian.each do |gu|
                stdgu = GuardianStudents.new
                stdgu.student_id = @std_obj.id
                stdgu.guardian_id = gu.guardian_id
                stdgu.relation = gu.relation
                stdgu.save
              end
            end
          end
        end
         
        if @username3
          @user_info3 = User.find_by_username(@username3)
          if !@user_info3.blank? && @user_info3.student
            @student_info3 = @user_info3.student_record
            @std_obj = Student.find @student_info3.id
            @std_obj.update_attributes(:immediate_contact_id => @main_std_immediate_contact_id)
            GuardianStudents.destroy_all(:student_id=>@std_obj.id)
            if !@all_guardian.blank?
              @all_guardian.each do |gu|
                stdgu = GuardianStudents.new
                stdgu.student_id = @std_obj.id
                stdgu.guardian_id = gu.guardian_id
                stdgu.relation = gu.relation
                stdgu.save
              end
            end
          end
        end
         
        if @username4
          @user_info3 = User.find_by_username(@username4)
          if !@user_info4.blank? && @user_info4.student
            @student_info4 = @user_info4.student_record
            @std_obj = Student.find @student_info4.id
            @std_obj.update_attributes(:immediate_contact_id => @main_std_immediate_contact_id)
            GuardianStudents.destroy_all(:student_id=>@std_obj.id)
            if !@all_guardian.blank?
              @all_guardian.each do |gu|
                stdgu = GuardianStudents.new
                stdgu.student_id = @std_obj.id
                stdgu.guardian_id = gu.guardian_id
                stdgu.relation = gu.relation
                stdgu.save
              end
            end
          end
        end
         
        if @username5
          @user_info3 = User.find_by_username(@username5)
          if !@user_info5.blank? && @user_info5.student
            @student_info5 = @user_info5.student_record
            @std_obj = Student.find @student_info5.id
            @std_obj.update_attributes(:immediate_contact_id => @main_std_immediate_contact_id)
            GuardianStudents.destroy_all(:student_id=>@std_obj.id)
            if !@all_guardian.blank?
              @all_guardian.each do |gu|
                stdgu = GuardianStudents.new
                stdgu.student_id = @std_obj.id
                stdgu.guardian_id = gu.guardian_id
                stdgu.relation = gu.relation
                stdgu.save
              end
            end
          end
        end
         
         
      end
    end
  end

  def all
    @users = User.active.all
  end
  
  def new_guardian
    @parent_info = Guardian.new(params[:parent_info])
    @selected_value = Configuration.default_country 

    if request.post?
      @activation_code_no_error = true
      if params[:activation_code]==""
        @activation_code_no_error = false
        @parent_info.errors.add("Activation Code", "must not be empty")
      else
        @activation_code = StudentActivationCode.find(:first,:conditions=>{:school_id=>MultiSchool.current_school.id,:code=>params[:activation_code]}) 
        if @activation_code.nil?
          @activation_code_no_error = false
          @parent_info.errors.add("Invalid", "Activation Code")
        elsif  @activation_code.is_active==1 || @activation_code.student_id==0 
          @activation_code_no_error = false
          @parent_info.errors.add("Student", "is not create yet using this activation code")
        end
      end 
      if @activation_code_no_error == true
        @student = Student.find(@activation_code.student_id) 
        @parent_info = @student.guardians.build(params[:parent_info])
        if @parent_info.save
          if !@student.immediate_contact_id
            @parent_info.create_guardian_user(@student)
            @student.update_attributes(:immediate_contact_id=>@parent_info.id)
            message = " Your Username is  #{@parent_info.user.username} and password is  123456 Please save it"
            flash[:notice] = message
          else
            @parent_info.create_guardian_user(@student,false)
            message = " Successfully Saved"
            flash[:notice] = message
          end  
           
          redirect_to :controller => "user", :action => "login"
        else
          render :layout => "std_registration"
        end
      else
        render :layout => "std_registration"
      end
    else
      render :layout => "std_registration"
    end  
  end
  
  def new_student_registration   
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @student = Student.new(params[:student])
    @selected_value = Configuration.default_country 
    @application_sms_enabled = SmsSetting.find_by_settings_key("ApplicationEnabled")
    @last_admitted_student = Student.find(:last)
    @config = Configuration.find_by_config_key('AdmissionNumberAutoIncrement')
    @categories = StudentCategory.active
    if request.post?    
      @activation_code_no_error = true
      @activation_code_free = params[:activation_code]
      if params[:activation_code]==""
        @activation_code_no_error = false
        @student.errors.add("Activation Code", "must not be empty")
      else
        @activation_code = StudentActivationCode.find(:first,:conditions=>{:school_id=>MultiSchool.current_school.id,:code=>params[:activation_code],:is_active=>1}) 
       
        if @activation_code.nil?
          @activation_code_no_error = false
          @student.errors.add("Invalid", "Activation Code"+MultiSchool.current_school.id.to_s+" ="+params[:activation_code]+"=")
        end
      end  
       
      if @activation_code_no_error == true
        @student.pass = params[:bng]
        champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
        api_endpoint = champs21_api_config['api_url']
        uri = URI(api_endpoint + "api/user/checkauth")
        http = Net::HTTP.new(uri.host, uri.port)
        auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
        auth_req.set_form_data({"user_id" => params[:user_id], "auth_id" => params[:auth_id], "activation_code" => params[:activation_code]})
        auth_res = http.request(auth_req)
        @auth_response = ActiveSupport::JSON.decode(auth_res.body)
        if @auth_response['status']['code']==200 and @auth_response['status']['msg'] == 'valid'
          if @config.config_value.to_i == 1
            @exist = Student.find_by_admission_no(params[:student][:admission_no])
            if @exist.nil?            
              @status = @student.save
            else
              @last_admitted_student = Student.find(:last)
              @student.admission_no = @last_admitted_student.admission_no.next
              @status = @student.save
            end
          else
            @status = @student.save
          end
        end
        if @status
          sms_setting = SmsSetting.new()
          if sms_setting.application_sms_active and @student.is_sms_enabled
            recipients = []
            message = "#{t('student_admission_done')} #{MultiSchool.current_school.code.to_s}-#{@student.admission_no} #{t('password_is')} 123456"
            if sms_setting.student_admission_sms_active
              recipients.push @student.phone2 unless @student.phone2.blank?
            end
            unless recipients.empty? or !send_sms("studentregister")
              Delayed::Job.enqueue(SmsManager.new(message,recipients))
            end
          end
          @activation_code.update_attributes(:is_active=> 0,:student_id=> @student.id)

          username = MultiSchool.current_school.code.to_s+"-"+@student.admission_no
        
          champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
          api_endpoint = champs21_api_config['api_url']
          uri = URI(api_endpoint + "api/user/updateprofile")
          http = Net::HTTP.new(uri.host, uri.port)
          auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
          auth_req.set_form_data({"user_id" => params[:user_id], "paid_id" => @student.id, "paid_username" => username, "paid_password" => params[:bng], "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @student.first_name, "middle_name" => @student.middle_name, "last_name" => @student.last_name, "gender" => (if @student.gender == 'm' then '1' else '0' end), "tds_country_id" => @student.nationality_id, "dob" => @student.date_of_birth})
          auth_res = http.request(auth_req)
          @auth_response = ActiveSupport::JSON.decode(auth_res.body)
        
          if Configuration.find_by_config_key('EnableSibling').present? and Configuration.find_by_config_key('EnableSibling').config_value=="1"
            message = "#{t('student_admission_done')}"
            flash[:notice] = message          
            redirect_to :controller => "user", :action => "login", :username=> username, :password => params[:bng],:auth_id => params[:auth_id],:user_id => params[:user_id]
          else
            message = "#{t('student_admission_done')}"
            flash[:notice] = message          
            redirect_to :controller => "user", :action => "login", :username=> username, :password => params[:bng],:auth_id => params[:auth_id],:user_id => params[:user_id]          
          end
        else
          render :layout => "std_registration"
        end 
      else
        render :layout => "std_registration"
      end  
    elsif  request.get? and params[:auth_id] and params[:user_id] and params[:activation_code]
      champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
      api_endpoint = champs21_api_config['api_url']
      uri = URI(api_endpoint + "api/user/checkauth")
      http = Net::HTTP.new(uri.host, uri.port)
      auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
      auth_req.set_form_data({"user_id" => params[:user_id], "auth_id" => params[:auth_id], "activation_code" => params[:activation_code]})
      auth_res = http.request(auth_req)
      @auth_response = ActiveSupport::JSON.decode(auth_res.body)
    
      if @auth_response['status']['code']==200 and @auth_response['status']['msg'] == 'valid'
        @student = Student.new(params[:student])
        @activation_code_free = params[:activation_code] 
        @student.first_name = params[:first_name] 
        @student.middle_name = params[:middle_name] 
        @student.last_name = params[:last_name] 
        @student.date_of_birth = params[:dob] 
        @email = params[:email] 
        @bng = params[:bng] 
        @student.nationality_id = params[:country_id] 
        @student.gender = if params[:gender] == 1 then 'm' else 'f' end 
      
        @auth_id = params[:auth_id]
        @user_id = params[:user_id]
      
        #@student = Student.new(params[:student].merge(:activation_code => params[:activation_code],:first_name =>params[:first_name],:middle_name => params[:middle_name],:last_name => params[:last_name],:date_of_birth => params[:dob],:bng => params[:bng],:nationality_id => params[:country_id],:gender => g ))
        @selected_value = Configuration.default_country 
        @application_sms_enabled = SmsSetting.find_by_settings_key("ApplicationEnabled")
        @last_admitted_student = Student.find(:last)
        @config = Configuration.find_by_config_key('AdmissionNumberAutoIncrement')
        @categories = StudentCategory.active
      
        render :layout => "std_registration"
      
      end          
    end
  end

  def get_section_data    
    @classes = Rails.cache.fetch("section_data_#{params[:class_name]}_#{current_user.id}"){
      class_data = Course.find(:all, :conditions => ["course_name LIKE ?",params[:class_name]])
      class_data
    }
    @selected_section = 0
    render :update do |page|
      if params[:page].nil?
        page.replace_html 'section', :partial => 'sections', :object => @classes
      else
        if params[:partial_view].nil?
          page.replace_html params[:page], :partial => 'sections', :object => @classes
        else
          page.replace_html params[:page], :partial => params[:partial_view], :object => @classes
        end
      end  
    end
  end

  def get_batches
    @batch_data = Rails.cache.fetch("course_data_#{params[:course_id]}_#{current_user.id}"){
      batches = Batch.find_by_course_id(params[:course_id])
      batches
    }
    @batch_id = 0
    unless @batch_data.nil?
      @batch_id = @batch_data.id 
    end
    
    render :update do |page|
      if params[:page].nil?
        page.replace_html 'batches', :partial => 'batches', :object => @batch_id
      else
        if params[:partial_view].nil?
          page.replace_html params[:page], :partial => 'sections', :object => @batch_id
        else
          page.replace_html params[:page], :partial => params[:partial_view], :object => @batch_id
        end
      end
    end
  end

  def list_user
    if params[:user_type].nil?
      page_not_found
    end
    if params[:user_type] == 'Admin'
      @users = User.activevisible.find(:all, :conditions => {:admin => true}, :order => 'first_name ASC')
      render(:update) do |page|
        page.replace_html 'users', :partial=> 'users'
        page.replace_html 'employee_user', :text => ''
        page.replace_html 'student_user', :text => ''
      end
    elsif params[:user_type] == 'Employee'
      render(:update) do |page|
        hr = Configuration.find_by_config_value("HR")
        unless hr.nil?
          page.replace_html 'employee_user', :partial=> 'employee_user'
          page.replace_html 'users', :text => ''
          page.replace_html 'student_user', :text => ''
        else
          @users = User.active.find_all_by_employee(1)
          page.replace_html 'users', :partial=> 'users'
          page.replace_html 'employee_user', :text => ''
          page.replace_html 'student_user', :text => ''
        end
      end
    elsif params[:user_type] == 'Student'
      render(:update) do |page|
        page.replace_html 'student_user', :partial=> 'student_user'
        page.replace_html 'users', :text => ''
        page.replace_html 'employee_user', :text => ''
      end
    elsif params[:user_type] == "Parent"
      render(:update) do |page|
        page.replace_html 'student_user', :partial=> 'parent_user'
        page.replace_html 'users', :text => ''
        page.replace_html 'employee_user', :text => ''
      end
    elsif params[:user_type] == ''
      @users = ""
      render(:update) do |page|
        page.replace_html 'users', :partial=> 'users'
        page.replace_html 'employee_user', :text => ''
        page.replace_html 'student_user', :text => ''
      end
    end
  end

  def list_employee_user
    emp_dept = params[:dept_id]
    @employee = Employee.find_all_by_employee_department_id(emp_dept, :order =>'first_name ASC')
    @users = @employee.collect { |employee| employee.user}
    @users.delete(nil)
    render(:update) {|page| page.replace_html 'users', :partial=> 'users'}
  end

  def list_student_user
    batch = params[:batch_id]
    @student = Student.find_all_by_batch_id(batch, :conditions => { :is_active => true },:order =>'first_name ASC')
    @users = @student.collect { |student| student.user}
    @users.delete(nil)
    render(:update) {|page| page.replace_html 'users', :partial=> 'users'}
  end

  def list_parent_user
    unless params[:batch_id].blank?
      batch = params[:batch_id]
      user_ids = Guardian.find(:all, :select=>'guardians.user_id',:joins=>'INNER JOIN students ON students.immediate_contact_id = guardians.id', :conditions => 'students.batch_id = ' + batch + ' AND is_active=1').collect(&:user_id).compact
      @users = User.find_all_by_id(user_ids,:conditions=>"is_deleted is false",:order =>'first_name ASC')
    else
      @users=[]
    end
    render(:update) {|page| page.replace_html 'users', :partial=> 'users'}
  end

  def change_password

    if request.post?
      @user = main_user_data
      if User.authenticate?(@user.username, params[:user][:old_password])
        if params[:user][:new_password] == params[:user][:confirm_password]
          @user.password = params[:user][:new_password]
          if @user.update_attributes(:password => @user.password, :role => @user.role_name)
          
            champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
            api_endpoint = champs21_api_config['api_url']
            uri = URI(api_endpoint + "api/user/UpdateProfilePaidUser")
            http = Net::HTTP.new(uri.host, uri.port)
            auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
            auth_req.set_form_data({"paid_id" => @user.id, "paid_password" => @user.password, "password" => @user.password, "paid_school_id" => MultiSchool.current_school.id})
            auth_res = http.request(auth_req)
            @auth_response = JSON::parse(auth_res.body)
          
            flash[:notice] = "#{t('flash9')}"
            redirect_to :action => 'dashboard'
          else
            flash[:warn_notice] = "<p>#{@user.errors.full_messages}</p>"
          end
        else
          flash[:warn_notice] = "<p>#{t('flash10')}</p>"
        end
      else
        flash[:warn_notice] = "<p>#{t('flash11')}</p>"
      end
    end
  end

  def user_change_password
    @user = User.active.find_by_username(params[:id])
    if @user.present?
      if request.post?
        if params[:user][:new_password]=='' and params[:user][:confirm_password]==''
          flash[:warn_notice]= "<p>#{t('flash6')}</p>"
        else
          if params[:user][:new_password] == params[:user][:confirm_password]
            @user.password = params[:user][:new_password]
            if @user.update_attributes(:password => @user.password,:role => @user.role_name)
              flash[:notice]= "#{t('flash7')}"
              redirect_to :action=>"profile", :id=>@user.username
            else
              render :user_change_password
            end
          else
            flash[:warn_notice] =  "<p>#{t('flash10')}</p>"
          end
        end
      end
    else
      flash[:notice] = t('no_users')
      redirect_to :controller => "user", :action => "dashboard"
    end
  end

  def create
    @config = Configuration.available_modules
    @school_code = MultiSchool.current_school.code.to_s
    @user = User.new(params[:user])
    if request.post?
      @user.username = MultiSchool.current_school.code.to_s+'-'+params[:user][:username].to_s;

      if @user.save
        flash[:notice] = "#{t('flash17')}"
        redirect_to :controller => 'user', :action => 'profile', :id => @user.username
      else
        flash[:notice] = "#{t('flash16')}"
      end

    end
  end

  def delete
    @user = User.active.find_by_username(params[:id],:conditions=>"admin = 1")
    unless @user.nil?
      if current_user == @user
        flash[:notice] = "You cannot delete your own profile"
        redirect_to :controller => "user", :action => "dashboard" and return
      else
        if @user.employee_record.nil?
          flash[:notice] = "#{t('flash12')}" if @user.destroy
        end
      end
    end
    redirect_to :controller => 'user'
  end

  def dashboard
    permitted_modules = Rails.cache.fetch("permitted_modules_dashboard_#{current_user.id}"){
      @dashboard_modules_tmp = []
      @a_user_modules = ['student_admission','student_details','user_text','news_text','examination','timetable_text','attendance','settings','human_resource','finance_text','my_profile','academics','leaves','examination']
      menu_links = MenuLink.find_all_by_name(@a_user_modules)
      menu_links.each do |menu_link|
        if menu_link.link_type == 'user_menu'
          menu_id = menu_link.id

          school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")

          if school_menu_links.nil? or school_menu_links.blank?
            @dashboard_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => false}
          else
            @dashboard_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => true}
          end
        else
          @dashboard_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => true}
        end
      end
      @dashboard_modules_tmp << {'name' => "remainders", "target_controller" => "remainder", "target_action" => "index", 'visible' => true}
      @dashboard_modules_tmp
    }
    @dashboard_modules = permitted_modules
  
    @user = current_user
    @config = Configuration.available_modules
    @employee = @user.employee_record if ["#{t('admin')}","#{t('employee_text')}"].include?(@user.role_name)
    if @user.student?
      @student = Student.find_by_admission_no(@user.username)
    end
    if @user.parent?
      session[:student_id]=params[:id].present?? params[:id] : @student.id
      Champs21.present_student_id=session[:student_id]
      @student=@user.guardian_entry.current_ward
      @students=@student.siblings.select{|g| g.immediate_contact_id=@user.guardian_entry.id}
    end
    @first_time_login = Configuration.get_config_value('FirstTimeLoginEnable')
    if  session[:user_id].present? and @first_time_login == "1" and @user.is_first_login != false
      flash[:notice] = "#{t('first_login_attempt')}"
      redirect_to :controller => "user",:action => "first_login_change_password"
    end
  end

  def edit
    #@user = User.active.find_by_username(params[:id])
    #@current_user = current_user
    #if request.post? and @user.update_attributes(params[:user])
    #flash[:notice] = "#{t('flash13')}"
    #redirect_to :controller => 'user', :action => 'profile', :id => @user.username
    #end
    redirect_to :action=> "dashboard"
  end

  def forgot_password
    #    flash[:notice]="You do not have permission to access forgot password!"
    #    redirect_to :action=>"login"
    if request.post? and params[:reset_password]
      if user = User.active.find_by_username(params[:reset_password][:username])
        unless user.email.blank?
          user.reset_password_code = Digest::SHA1.hexdigest( "#{user.email}#{Time.now.to_s.split(//).sort_by {rand}.join}" )
          user.reset_password_code_until = 1.day.from_now
          user.role = user.role_name
          user.save(false)
          url = "#{request.protocol}#{request.host_with_port}"
          begin
            UserNotifier.deliver_forgot_password(user,url)
          rescue Exception => e
            puts "Error------#{e.message}------#{e.backtrace.inspect}"
            flash[:notice] = "#{t('flash21')}"
            return
          end
          flash[:notice] = "#{t('flash18')}"
          redirect_to :action => "index"
        else
          flash[:notice] = "#{t('flash20')}"
          return
        end
      else
        flash[:notice] = "#{t('flash19')} #{params[:reset_password][:username]}"
      end
    end
  end


  def login
    require 'net/http'
    require 'uri'
    require "yaml"
  
    @institute = Configuration.find_by_config_key("LogoName")
    available_login_authes = Champs21Plugin::AVAILABLE_MODULES.select{|m| m[:name].camelize.constantize.respond_to?("login_hook")}
    selected_login_hook = available_login_authes.first if available_login_authes.count>=1
    
    if selected_login_hook
      authenticated_user = selected_login_hook[:name].camelize.constantize.send("login_hook",self)
    else
      if request.post? and params[:user]
      
        username = params[:user][:username]
        password = params[:user][:password]
      
        @user = User.new(params[:user])
        user = User.active.find_by_username @user.username
        if user.present? and User.authenticate?(@user.username, @user.password)
          authenticated_user = user
        end
      elsif  request.get? and params[:username] and params[:password] and params[:auth_id] and params[:user_id]
      
        username = params[:username]
        password = params[:password]
        
        champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
        api_endpoint = champs21_api_config['api_url']
        uri = URI(api_endpoint + "api/user/checkauth")
        http = Net::HTTP.new(uri.host, uri.port)
        auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
        auth_req.set_form_data({"user_id" => params[:user_id], "auth_id" => params[:auth_id]})
        auth_res = http.request(auth_req)
        @auth_response = ActiveSupport::JSON.decode(auth_res.body)
        
        if @auth_response['status']['code']==200
          user = User.active.find_by_username params[:username]
          if user.present? and User.authenticate?(params[:username], params[:password])
            authenticated_user = user
            
          end
        end  
      end
    end
  
    if authenticated_user.present?
    
      champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
      api_endpoint = champs21_config['api_url']
      api_from = champs21_config['from']
    
      uri = URI(api_endpoint + "api/user/auth")
      http = Net::HTTP.new(uri.host, uri.port)
      auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
      auth_req.set_form_data({"username" => username, "password" => password})
      auth_res = http.request(auth_req)
      auth_response = JSON::parse(auth_res.body)
    
      ar_user_cookie = auth_res.response['set-cookie'].split('; ');
      
      if api_from == "local"
        user_info = [ 
          "user_secret" => auth_response['data']['paid_user']['secret'],
          "user_cookie" => ar_user_cookie[1].split(",")[1],
          "user_cookie_exp" => ar_user_cookie[3].split('=')[1].to_time.to_i
        ]
        else
        user_info = [ 
          "user_secret" => auth_response['data']['paid_user']['secret'],
          "user_cookie" => ar_user_cookie[1].split(",")[1],
          "user_cookie_exp" => ar_user_cookie[2].split('=')[1].to_time.to_i
        ]
      end
        
      #"user_cookie_exp" => ar_user_cookie[2].split('=')[1].to_time.to_i  
      session[:api_info] = user_info
    
      flash.clear
      if !params[:connect_exam].blank? and !params[:batch_id].blank? and !params[:student].blank?
         successful_user_login_pdf(authenticated_user,params[:connect_exam],params[:batch_id],params[:student]) and return     
      else
        successful_user_login(authenticated_user) and return
      end
    elsif authenticated_user.blank? and request.post?
      flash[:notice] = "#{t('login_error_message')}"
    end
  end 

  def first_login_change_password
    @user = current_user
    @setting = Configuration.get_config_value('FirstTimeLoginEnable')
    if @setting == "1" and @user.is_first_login != false
      if request.post?
        if params[:user][:new_password] == params[:user][:confirm_password]
          if @user.update_attributes(:password => params[:user][:confirm_password],:is_first_login => false)
            flash[:notice] = "#{t('password_update')}"
            redirect_to :controller => "user",:action => "dashboard"
          else
            render :first_login_change_password
          end
        else
          @user.errors.add('password','and confirm password doesnot match')
          render :first_login_change_password
        end
      end
    else
      flash[:notice] = "#{t('not_applicable')}"
      redirect_to :controller => "user",:action => "dashboard"
    end
  end

  def logout
    unless session[:user_id].nil?
      Rails.cache.delete("user_main_menu#{session[:user_id]}")
      Rails.cache.delete("user_autocomplete_menu#{session[:user_id]}")
      current_user.delete_user_menu_caches

      now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
      
      @sesstion_time = 0
      @last_session_log = ActivityLog.find(:first,:conditions=>{:user_id=>current_user.id,:session_end=>1},:order=>"created_at DESC",:limit=>1)
      if !@last_session_log.nil?
        @session_start_log = ActivityLog.find(:first,:conditions=>["user_id =#{current_user.id} and created_at >'#{@last_session_log.created_at}'"],:order=>"created_at ASC",:limit=>1)
        @sesstion_time =  now.to_time-@session_start_log.created_at.to_time
      else
        @last_session_log = ActivityLog.find(:first,:conditions=>{:user_id=>current_user.id},:order=>"created_at ASC",:limit=>1)
        if !@last_session_log.nil?
          @sesstion_time =  now.to_time-@last_session_log.created_at.to_time
        end
      end  
  
      
      
      activity_log = ActivityLog.new
      activity_log.user_id = current_user.id
      activity_log.controller = params[:controller]
      activity_log.action = params[:action]
      activity_log.ip = request.remote_ip
      activity_log.user_agent = request.user_agent
      activity_log.created_at = now
      activity_log.updated_at = now
      activity_log.session_end = 1
      activity_log.session_time = @sesstion_time
      if current_user.admin?
        activity_log.user_type_paid = 4
      end
      if current_user.employee?
        activity_log.user_type_paid = 3
      end
      if current_user.parent?
        activity_log.user_type_paid = 2
      end
      if current_user.student?
        activity_log.user_type_paid = 1
      end
      activity_log.save

      session[:user_id] = nil if session[:user_id]
      session[:language] = nil
      flash[:notice] = "#{t('logged_out')}"
      available_login_authes = Champs21Plugin::AVAILABLE_MODULES.select{|m| m[:name].camelize.constantize.respond_to?("logout_hook")}
      selected_logout_hook = available_login_authes.first if available_login_authes.count>=1
      if selected_logout_hook
        selected_logout_hook[:name].camelize.constantize.send("logout_hook",self,"/")
      else
        champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
        if champs21_config['from'] == "remote"
          redirect_to "http://www.classtune.com" and return
        end
        redirect_to :controller => 'user', :action => 'login' and return
      end
    else
      champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
      if champs21_config['from'] == "remote"
        redirect_to "http://www.classtune.com" and return
      end
      redirect_to :controller => 'user', :action => 'login' and return
    end
  end

  def profile
    @config = Configuration.available_modules
    @current_user = current_user
    @username = @current_user.username if session[:user_id]
    @user = User.active.find_by_username(params[:id])
    unless @user.nil?
      @employee = Employee.find_by_user_id(@user.id)
      @student = Student.find_by_user_id(@user.id)
      @ward  = @user.parent_record if @user.parent

    else
      flash[:notice] = "#{t('flash14')}"
      redirect_to :action => 'dashboard'
    end
  end

  def reset_password
    user = User.active.find_by_reset_password_code(params[:id],:conditions=>"reset_password_code IS NOT NULL")
    if user
      if user.reset_password_code_until > Time.now
        redirect_to :action => 'set_new_password', :id => user.reset_password_code
      else
        flash[:notice] = "#{t('flash1')}"
        redirect_to :action => 'index'
      end
    else
      flash[:notice]= "#{t('flash2')}"
      redirect_to :action => 'index'
    end
  end

  def search_user_ajax
    unless params[:query].nil? or params[:query].empty? or params[:query] == ' '
      #      if params[:query].length>= 3
      #        @user = User.first_name_or_last_name_or_username_begins_with params[:query].split
      @user = User.active.find(:all,
        :conditions => ["first_name LIKE ? OR last_name LIKE ?
                            OR username = ? OR (concat(first_name, \" \", last_name) LIKE ? ) ",
          "#{params[:query]}%","#{params[:query]}%","#{params[:query]}",
          "#{params[:query]}%"],
        :order => "first_name asc") unless params[:query] == ''
      #      else
      #        @user = User.first_name_or_last_name_or_username_equals params[:query].split
      #      end
      #      @user = @user.sort_by { |u1| [u1.role_name,u1.full_name] } unless @user.nil?
    else
      @user = ''
    end
    render :layout => false
  end

  def set_new_password
    if request.post?
      user = User.active.find_by_reset_password_code(params[:id],:conditions=>"reset_password_code IS NOT NULL")
      if user
        if params[:set_new_password][:new_password]=='' and params[:set_new_password][:confirm_password]==''
          flash[:notice]= "#{t('flash6')}"
          redirect_to :action => 'set_new_password', :id => user.reset_password_code
        else
          if params[:set_new_password][:new_password] === params[:set_new_password][:confirm_password]
            user.password = params[:set_new_password][:new_password]
            user.update_attributes(:password => user.password, :reset_password_code => nil, :reset_password_code_until => nil, :role => user.role_name)
            user.clear_menu_cache
            #User.update(user.id, :password => params[:set_new_password][:new_password],
            # :reset_password_code => nil, :reset_password_code_until => nil)
            flash[:notice] = "#{t('flash3')}"
            redirect_to :action => 'index'
          else
            flash[:notice] = "#{t('user.flash4')}"
            redirect_to :action => 'set_new_password', :id => user.reset_password_code
          end
        end
      else
        flash[:notice] = "#{t('flash5')}"
        redirect_to :action => 'index'
      end
    end
  end

  def edit_privilege
    @user = User.active.find_by_username(params[:id])
    if @user.admin? or @user.student? or @user.parent?
      flash[:notice] = "#{t('flash_msg4')}"
      redirect_to :controller => "user",:action => "dashboard"
    else
      @finance = Configuration.find_by_config_value("Finance")
      @sms_setting = SmsSetting.application_sms_status
      @hr = Configuration.find_by_config_value("HR")
      @privilege_tags=PrivilegeTag.find(:all,:order=>"priority ASC")
      @user_privileges=@user.privileges
      if request.post?
        new_privileges = params[:user][:privilege_ids] if params[:user]
        new_privileges ||= []
        @user.privileges = Privilege.find_all_by_id(new_privileges)
        @user.clear_menu_cache
        @user.delete_user_menu_caches
        flash[:notice] = "#{t('flash15')}"
        redirect_to :action => 'profile',:id => @user.username
      end
    end
  end

  def header_link
    @user = current_user
    #@reminders = @users.check_reminders
    @config = Configuration.available_modules
    @employee = Employee.find_by_employee_number(@user.username)
    @employee ||= Employee.first if current_user.admin?
    @student = Student.find_by_admission_no(@user.username)
    render :partial=>'header_link'
  end

  def show_quick_links
    current_user.delete_user_menu_caches()
    
    quick_links = Rails.cache.fetch("user_quick_links#{current_user.id}"){
      school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ?",MultiSchool.current_school.id], :select => "menu_link_id")
      school_menu_links_arr = school_menu_links.map {|i| i.menu_link_id }
    
      links = current_user.menu_links
      #    i = 0
      #    s = ""
      #    m = ""
      #    links.each do |link|
      #      if link.link_type == 'user_menu'
      #        if school_menu_links_arr.select{|sm| sm == link.id}.nil? or school_menu_links_arr.select{|sm| sm == link.id}.blank?
      #            links.delete_at(i)
      #        end
      #      end
      #      i += 1
      #    end
    
      allowed_links = links.select{|l| can_access_request?(l.target_action.to_s.to_sym,@current_user,:context=>l.target_controller.to_s.to_sym)}
      current_user.menu_links = allowed_links
      allowed_links
      #puts(links[1])
      #current_user.menu_links = links
    }
    render :partial=>"layouts/quick_links", :locals=>{:menu_links=>quick_links}
  end

  def show_all_features
    #  Rails.cache.delete("user_cat_links_#{params[:cat_id]}_#{current_user.id}")
    cat_links = Rails.cache.fetch("user_cat_links_#{params[:cat_id]}_#{current_user.id}"){
      link_cat = MenuLinkCategory.find_by_id(params[:cat_id])
      all_links = link_cat.menu_links
      general_links = all_links.select{|l| l.link_type=="general"}
    
    
      school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ?",MultiSchool.current_school.id], :select => "menu_link_id")
      school_menu_links_arr = school_menu_links.map {|i| i.menu_link_id }
      #abort(school_menu_links_arr.inspect)
      user_menu_links = MenuLink.find(:all, :conditions => ["id IN (?) and link_type = 'user_menu' AND menu_link_category_id = ?",school_menu_links_arr, params[:cat_id] ])
      #abort(user_menu_links.map{|um| um.id}.inspect)
      general_links = general_links + user_menu_links
    
      if current_user.admin?
        selective_links = general_links
      elsif current_user.employee?
        own_links = all_links.select{|l| l.link_type=="own" and l.user_type=="employee"}
        selective_links = general_links + own_links
      else
        own_links = all_links.select{|l| l.link_type=="own" and l.user_type=="student" and l.reference_id == 0}
        selective_links = general_links + own_links
        own_links_with_references = all_links.select{|l| l.link_type=="own" and l.user_type=="student" and l.reference_id > 0}
        own_links_with_references.each do |own_link|
          menu_id = own_link.reference_id
          school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
          unless school_menu_links.blank?
            selective_links << own_link
          end
        end
      end
    
      allowed_links = selective_links.select{|l| can_access_request?(l.target_action.to_s.to_sym,@current_user,:context=>l.target_controller.to_s.to_sym)}
      allowed_links
    }
    render :partial=>"layouts/quick_links", :locals=>{:menu_links=>cat_links}
  end

  #  def show_edit_links
  #    general_links = MenuLink.all.select{|l| l.link_type=="general"}
  #    if current_user.admin?
  #      selective_links = general_links
  #    elsif current_user.employee?
  #      own_links = MenuLink.find_all_by_link_type_and_user_type("own","employee")
  #      selective_links = general_links + own_links
  #    else
  #      own_links = MenuLink.find_all_by_link_type_and_user_type("own","student")
  #      selective_links = general_links + own_links
  #    end
  #    allowed_links = selective_links.select{|l| can_access_request?(l.target_action.to_s.to_sym,l.target_controller.to_s.to_sym)}
  #    own_links = current_user.menu_links.select{|l| can_access_request?(l.target_action.to_s.to_sym,l.target_controller.to_s.to_sym)}
  #    render :partial=>"layouts/select_links", :locals=>{:all_links=>allowed_links,:own_links=>own_links}
  #  end

  def update_quick_links
    allowed_links = MenuLink.find_all_by_id(params[:selected_links])
    current_user.menu_links = allowed_links
    Rails.cache.delete("user_quick_links#{current_user.id}")
    flash[:notice]="Quick Links modified successfully."
    render :text=>""
  end

  def manage_quick_links
    u_roles = current_user.role_symbols
    @available_categories = MenuLinkCategory.find_all_by_name(["academics","collaboration","administration","data_and_reports"]).select{|m| !(m.allowed_roles & u_roles == [])}
    general_links = MenuLink.all.select{|l| l.link_type=="general"}
  
    school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ?",MultiSchool.current_school.id], :select => "menu_link_id")
    school_menu_links_arr = school_menu_links.map {|i| i.menu_link_id }
    user_menu_links = MenuLink.find(:all, :conditions => ["id IN (?) ",school_menu_links_arr])
    general_links = general_links + user_menu_links
  
    if current_user.admin?
      selective_links = general_links
    elsif current_user.employee?
      own_links = MenuLink.find_all_by_link_type_and_user_type("own","employee")
      selective_links = general_links + own_links
    else
      own_links = MenuLink.find_all_by_link_type_and_user_type("own","student")
      selective_links = general_links + own_links
    end
    @own_links = current_user.menu_links.select{|l| can_access_request?(l.target_action.to_s.to_sym,@current_user,:context=>l.target_controller.to_s.to_sym)}
    @allowed_links = selective_links.select{|l| can_access_request?(l.target_action.to_s.to_sym,@current_user,:context=>l.target_controller.to_s.to_sym)}
  end

  private
  
  def successful_user_login_pdf(user,connect_exam,batch_id,student_id)
    cookies.delete("_champs21_session")
    session[:user_id_main] = user.id
    session[:user_id] = user.id
    redirect_to ({:controller => 'exam', :action => 'generated_report5_pdf', :connect_exam =>connect_exam,:batch_id =>batch_id,:student_id =>student_id,:page_height=>450,:type=>"grouped"  })
  end
  
  def successful_user_login(user)
    cookies.delete("_champs21_session")
    session[:user_id_main] = user.id
    #    if user.is_deleted?
    #    
    #      guardian_data = Guardian.find_by_user_id(user.id)
    #      unless guardian_data.nil?
    #        students = Student.find_by_id(guardian_data.ward_id)
    #        if students.immediate_contact_id
    #          st_guardian = Guardian.find_by_id(students.immediate_contact_id)
    #          session[:user_id] = st_guardian.user_id
    #        end  
    #      end
    #    else
    session[:user_id] = user.id
    #    end
    #    flash[:notice] = "#{t('welcome')}, #{user.first_name} #{user.last_name}!"
    redirect_to ((session[:back_url] unless (session[:back_url]) =~ /user\/logout$/) || {:controller => 'user', :action => 'dashboard'})
  end

  private
  def successful_user_login_get(user)
    cookies.delete("_champs21_session")
    session[:user_id] = user.id
  end

  #  def cache_quick_links
  #    s=Rails.cache.fetch("user_quick_links#{current_user.id}"){
  #      yield
  #    }
  #    return s
  #  end
end

