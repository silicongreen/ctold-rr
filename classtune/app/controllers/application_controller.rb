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
#require 'champs21_setting.rb'
class ApplicationController < ActionController::Base
  helper :all
  helper_method :has_subject_group
  helper_method :get_subject_group
  helper_method :get_subject_sub_group
  helper_method :can_access_request?
  helper_method :check_permission_link?
  helper_method :get_attendence_data_all
  helper_method :get_subscrived_link
  helper_method :in_words
  helper_method :send_sms
  helper_method :sms_enable?
  helper_method :save_group_exam_pdf
  helper_method :school_mock_test?
  helper_method :school_form_apply?
  helper_method :get_tabulation_connect_exam
  helper_method :get_exam_result_type
  helper_method :get_exam_result_type2
  helper_method :get_exam_result_quarter
  helper_method :school_detention_allowed?
  helper_method :school_smartcard_allowed?
  helper_method :dec_student_count_subscription
  helper_method :exam_marks_entry_allowed
  helper_method :check_free_school?
  helper_method :can_access_plugin?
  helper_method :can_access_feature?
  helper_method :currency
  protect_from_forgery # :secret => '434571160a81b5595319c859d32060c1'
  filter_parameter_logging :password
  
  
  
  before_filter { |c| Authorization.current_user = c.current_user }
  before_filter :message_user
  before_filter :set_user_language
  before_filter :set_font_face
  before_filter :set_variables
  before_filter :login_check
  before_filter :set_host
  before_filter :default_time_zone_present_time

  before_filter :dev_mode
  include CustomInPlaceEditing
  


  def exam_marks_entry_allowed(exam_group)
    if exam_group.marks_entry.to_i == 1 and (exam_group.last_date_marks_entry.blank? or @local_tzone_time.to_date <= exam_group.last_date_marks_entry)
      return true
    end
    return false
  end
  def in_words(int)
    set1 = ["","one","two","three","four","five","six","seven",
      "eight","nine","ten","eleven","twelve","thirteen",
      "fourteen","fifteen","sixteen","seventeen","eighteen",
      "nineteen","twenty","twenty one","twenty two","twenty three","twenty four","twenty five","twenty six","twenty seven","twenty eight","twenty nine","thirty","thirty one","thirty two","thirty three"]

    set2 = ["","","twenty","thirty","forty","fifty","sixty",
      "seventy","eighty","ninety"]

    thousands = (int/1000)
    hundreds = ((int%1000) / 100)
    tens = ((int % 100) / 10)
    ones = int % 10
    string = ""
    
    

    string += set1[thousands] + " thousand " if thousands != 0 if thousands > 0
    string += set1[hundreds] + " hundred" if hundreds != 0
    string +=" and " if tens != 0 || ones != 0 
    string = string + set1[tens*10+ones] if tens < 2
    string += set2[tens]
    string = string + " " + set1[ones] if ones != 0 and tens > 1    
    string << 'zero' if int == 0  
    
    return string+" Taka Only"
  end
  
  def get_tabulation_connect_exam(connect_exam_id,batch_id,all_class_report=false)
    require 'net/http'
    require 'uri'
    require "yaml"
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    api_uri = URI(api_endpoint + "api/report/tabulation")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    if all_class_report
      request.set_form_data({"connect_exam_id"=>connect_exam_id,"batch_id"=>batch_id,"all_class_report"=>all_class_report,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
    else
      request.set_form_data({"connect_exam_id"=>connect_exam_id,"batch_id"=>batch_id,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
    end
    response = http.request(request)
    student_response = JSON::parse(response.body)
    return student_response
  end
  
  def get_subject_group(subject_id)
    require "yaml"
    subject_subgroup = SubjectSubgroup.find(:all,:conditions=>["subject_id=? and (parent_id is null or parent_id = 0)",subject_id],:order=>"priority ASC")   
    return subject_subgroup  
  end
  
  def get_subject_sub_group(subject_subgroup_id)
    require "yaml"
    vreturn = false
    subject_subgroup = SubjectSubgroup.find(:all,:conditions=>["parent_id = ?",subject_subgroup_id],:order=>"priority ASC")
    unless subject_subgroup.blank?
      vreturn = subject_subgroup.map(&:name)
    end    
    return vreturn  
  end
  
  
  def has_subject_group(subject_id)
    require "yaml"
    vreturn = false
    subject_subgroup = SubjectSubgroup.find(:all,:conditions=>["subject_id=? and (parent_id is null or parent_id = 0)",subject_id])
    unless subject_subgroup.blank?
      vreturn = true
    end   
    return vreturn 
  end
  
  def school_mock_test?
    require "yaml"
    vreturn = false
    detention_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/other.yml")['mocktest']
    all_schools = detention_config['numbers'].split(",")
    current_school = MultiSchool.current_school.id
    if all_schools.include?(current_school.to_s)
      vreturn = true
    end
    return vreturn
  end
  
  def get_exam_result_quarter()
    require "yaml"
    vreturn = {}
    type_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/other.yml")['quarter']
    all_schools = type_config['numbers'].split(",")
    current_school = MultiSchool.current_school.id
    if all_schools.include?(current_school.to_s)
      all_types = type_config['type_'+current_school.to_s].split(",")     
      all_types.each do |examtype|
        type_array = examtype.split("_")
        vreturn[type_array[0].to_s] = type_array[1].to_s
      end  
    else
      all_types = type_config['default'].split(",") 
      all_types.each do |examtype|
        type_array = examtype.split("_")
        vreturn[type_array[0].to_s] = type_array[1].to_s
      end
    end  
    return vreturn
  end
  
  def get_exam_result_type2()
    require "yaml"
    vreturn = {}
    type_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/other.yml")['resulttype2']
    all_schools = type_config['numbers'].split(",")
    current_school = MultiSchool.current_school.id
    if all_schools.include?(current_school.to_s)
      all_types = type_config['type_'+current_school.to_s].split(",")     
      all_types.each do |examtype|
        type_array = examtype.split("_")
        vreturn[type_array[0].to_s] = type_array[1].to_s
      end   
    else
      all_types = type_config['default'].split(",") 
      all_types.each do |examtype|
        type_array = examtype.split("_")
        vreturn[type_array[0].to_s] = type_array[1].to_s
      end
    end  
    return vreturn
  end
  
  def get_exam_result_type()
    require "yaml"
    vreturn = {}
    type_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/other.yml")['resulttype']
    all_schools = type_config['numbers'].split(",")
    current_school = MultiSchool.current_school.id
    if all_schools.include?(current_school.to_s)
      all_types = type_config['type_'+current_school.to_s].split(",")
      il = 0
      all_types.each do |examtype|
        
        il = il+1
        if current_school != 340 or (il!=2 and  il!=3 and  il!=9 and  il!=12 and  il!=5 and  il!=11) 
          vreturn[il.to_s] = examtype
        end  
      end
      
    else
      all_types = type_config['default'].split(",") 
      il = 0
      all_types.each do |examtype|
        il = il+1
        vreturn[il.to_s] = examtype
      end
    end  
    return vreturn
  end
  
  def school_form_apply?
    require "yaml"
    vreturn = false
    detention_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/other.yml")['formapply']
    all_schools = detention_config['numbers'].split(",")
    current_school = MultiSchool.current_school.id
    if all_schools.include?(current_school.to_s)
      vreturn = true
    end
    return vreturn
  end
  
  def send_sms(feature_name)
    require "yaml"
    vreturn = false
    sms_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/sms.yml")['school']
    all_schools = sms_config['numbers'].split(",")
    current_school = MultiSchool.current_school.id
    if all_schools.include?(current_school.to_s)
      string_to_match = current_school.to_s+"_"+feature_name
      all_features = sms_config['feature'].split(",")
      if all_features.include?(string_to_match)
        vreturn = true
      end
      
    end
    return vreturn
  end
  def school_smartcard_allowed?
    require "yaml"
    vreturn = false
    detention_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/smartcard.yml")['school']
    all_schools = detention_config['numbers'].split(",")
    current_school = MultiSchool.current_school.id
    if all_schools.include?(current_school.to_s)
      vreturn = true
    end
    return vreturn
  end
  def school_detention_allowed?
    require "yaml"
    vreturn = false
    detention_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/detention.yml")['school']
    all_schools = detention_config['numbers'].split(",")
    current_school = MultiSchool.current_school.id
    if all_schools.include?(current_school.to_s)
      vreturn = true
    end
    return vreturn
  end
  def sms_enable?
    require "yaml"
    vreturn = false
    sms_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/sms.yml")['school']
    all_schools = sms_config['numbers'].split(",")
    current_school = MultiSchool.current_school.id
    if all_schools.include?(current_school.to_s)
      vreturn = true
    end
    return vreturn
  end
  def get_subscrived_link(link_text)
    link_return = "<a href='javascript:void(0)' class='dim_link subscribed-messege' >#{link_text}</a>"
    return link_return
  end
  
  def dec_student_count_subscription
    school_subscription_info = SubscriptionInfo.find(:first,:conditions=>{:school_id=>MultiSchool.current_school.id},:limit=>1)
    if !school_subscription_info.nil?
      school_subscription_info.current_count = school_subscription_info.current_count-1
      school_subscription_info.save;
    end
  end
  
  def get_attendence_data_all
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    
    if current_user.student or current_user.employee  or current_user.admin
      homework_uri = URI(api_endpoint + "api/report/attendence")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @attendence_text_default = JSON::parse(homework_res.body)
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      homework_uri = URI(api_endpoint + "api/report/attendence")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"batch_id"=>student.batch_id,"student_id"=>student.id,"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @attendence_text_default = JSON::parse(homework_res.body)
    end
    
    
    return @attendence_text_default
  end

  def check_status
    unless Configuration.find_by_config_key("SetupAttendance").try(:config_value) == "1"
      flash[:notice] = "#{t('system_under_maintainance')}"
      redirect_to :controller => "user", :action => "dashboard"
    end
  end
  
  def check_free_school?
    free = false
    school_subscription_info = SubscriptionInfo.find(:first,:conditions=>{:school_id=>MultiSchool.current_school.id},:limit=>1)
    if !school_subscription_info.nil?
      date_to_check = Date.today
      if date_to_check>school_subscription_info.end_date.to_date
        free = true 
      end
    else
      free = true
    end  
    return free
  end 
  
  def check_free_school_test?
    free_code = ['free'] 
    school_domains = MultiSchool.current_school.school_domains
    free = false
    free_code.each do |c|
      school_domains.each do |d|
        if d.domain.index(c)
          free = true
        end
      end  
    end 
    return free
  end  
  
  def activity_check
    @session_end_time_diff = 15
    now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    if session[:user_id].present? and params[:action] != 'show_quick_links'
      if params[:controller] != 'data_palettes' or current_user.admin?
        
        
        @sesstion_time = 0
        @last_log = ActivityLog.find(:first,:conditions=>{:user_id=>current_user.id},:order=>"created_at DESC",:limit=>1)
        if !@last_log.nil?
          @time_last_log =  ((now.to_time-@last_log.created_at.to_time)/60).round
          if @time_last_log>@session_end_time_diff and @last_log.session_end!=1
            @last_session_log = ActivityLog.find(:first,:conditions=>{:user_id=>current_user.id,:session_end=>1},:order=>"created_at DESC",:limit=>1)
            if !@last_session_log.nil?
              @session_start_log = ActivityLog.find(:first,:conditions=>["user_id =#{current_user.id} and created_at >'#{@last_session_log.created_at}'"],:order=>"created_at ASC",:limit=>1)
              if !@session_start_log.nil?
                @sesstion_time =  now.to_time-@session_start_log.created_at.to_time
                activity_log_update = ActivityLog.find(@last_log.id)
                activity_log_update.session_end = 1
                activity_log_update.session_time = @sesstion_time
                activity_log_update.save
              else
                @sesstion_time =  now.to_time-@last_session_log.created_at.to_time
                activity_log_update = ActivityLog.find(@last_log.id)
                activity_log_update.session_end = 1
                activity_log_update.session_time = @sesstion_time
                activity_log_update.save
              end
            else
              @last_session_log = ActivityLog.find(:first,:conditions=>{:user_id=>current_user.id},:order=>"created_at ASC",:limit=>1)
              if !@last_session_log.nil?
                @sesstion_time =  now.to_time-@last_session_log.created_at.to_time
                activity_log_update = ActivityLog.find(@last_log.id)
                activity_log_update.session_end = 1
                activity_log_update.session_time = @sesstion_time
                activity_log_update.save
              end
            end  
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
      end  
    end
  end
  
  
  def check_permission_link? (con,act)
    has_permission_for_this_link = Rails.cache.fetch("has_permission_for_user_links_#{con}_#{act}_#{current_user.id}"){
      controller_name = con
      action_name = act
    
      menu_links = MenuLink.find(:first, :conditions => ["target_controller = ? AND target_action = ?",controller_name, action_name], :select => "id,link_type")
      
      has_permission = true
      if menu_links.link_type == 'user_menu'
        menu_id = menu_links.id
        
        menu_links = MenuLink.find(:first, :conditions => ["target_controller = ? AND target_action = ?",controller_name, action_name], :select => "id")
        menu_id = menu_links.id
    
        school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
      
        if school_menu_links.nil? or school_menu_links.blank?
          has_permission = false
        end
      end  
      has_permission
    }
    
    if has_permission_for_this_link == false
      return false
    end
    
    return true
  end

  def check_permission
    has_permission_for_this_link = Rails.cache.fetch("has_permission_for_user_links_#{params[:controller]}_#{params[:action]}_#{current_user.id}"){
      controller_name = params[:controller]
      action_name = params[:action]
    
      menu_links = MenuLink.find(:first, :conditions => ["target_controller = ? AND target_action = ?",controller_name, action_name], :select => "id,link_type")
      
      has_permission = true
      unless menu_links.blank?
        if menu_links.link_type == 'user_menu'
          menu_id = menu_links.id

          menu_links = MenuLink.find(:first, :conditions => ["target_controller = ? AND target_action = ?",controller_name, action_name], :select => "id")
          menu_id = menu_links.id

          school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")

          if school_menu_links.nil? or school_menu_links.blank?
            has_permission = false
          end
        end  
      end
      has_permission
    }
    
    if has_permission_for_this_link == false
      flash[:notice] = "#{t('flash_msg4')}"
      redirect_to :controller => 'user', :action => 'dashboard'
    end
  end
  
  def login_check
    if session[:user_id].present?
      unless (controller_name == "user") and ["first_login_change_password","login","logout","forgot_password"].include? action_name
        user = User.active.find(session[:user_id])
        setting = Configuration.get_config_value('FirstTimeLoginEnable')
        if setting == "1" and user.is_first_login != false
          flash[:notice] = "#{t('first_login_attempt')}"
          redirect_to :controller => "user",:action => "first_login_change_password"
        end
      end
    end
  end


  def dev_mode
    if Rails.env == "development"

    end
  end

  def set_variables
    unless @current_user.nil?
      @attendance_type = Configuration.get_config_value('StudentAttendanceType') unless @current_user.student?
      @modules = Configuration.available_modules
    end
  end


  def set_language
    session[:language] = params[:language]
    @current_user.clear_menu_cache
    render :update do |page|
      page.reload
    end
  end


  if Rails.env.production?
    rescue_from ActiveRecord::RecordNotFound do |exception|
      flash[:notice] = "#{t('flash_msg2')} , #{exception} ."
      logger.info "[Champs21Rescue] AR-Record_Not_Found #{exception.to_s}"
      log_error exception
      redirect_to :controller=>:user ,:action=>:dashboard
    end

    rescue_from NoMethodError do |exception|
      flash[:notice] = "#{t('flash_msg3')}"
      logger.info "[Champs21Rescue] No method error #{exception.to_s}"
      log_error exception
      redirect_to :controller=>:user ,:action=>:dashboard
    end

    rescue_from ActionController::InvalidAuthenticityToken do|exception|
      flash[:notice] = "#{t('flash_msg43')}"
      logger.info "[Champs21Rescue] Invalid Authenticity Token #{exception.to_s}"
      log_error exception
      if request.xhr?
        render(:update) do|page|
          page.redirect_to :controller => 'user', :action => 'dashboard'
        end
      else
        redirect_to :controller => 'user', :action => 'dashboard'
      end
    end

    rescue_from ActionController::MethodNotAllowed do |exception|
      logger.info "[Champs21Rescue] Method Not Allowed #{exception.to_s}"
      log_error exception
      respond_to do |format|
        format.html { render :file => "#{Rails.root}/public/404.html", :status => :not_found }
        format.xml  { head :not_found }
        format.any  { head :not_found }
      end
    end
  end

 
  def only_assigned_employee_allowed
    @privilege = @current_user.privileges.map{|p| p.name}
    if @current_user.employee?
      @employee_subjects= @current_user.employee_record.subjects
      if @employee_subjects.empty? and !@privilege.include?("StudentAttendanceView") and !@privilege.include?("StudentAttendanceRegister")
        flash[:notice] = "#{t('flash_msg4')}"
        redirect_to :controller => 'user', :action => 'dashboard'
      else
        @allow_access = true
      end
    end
  end

  def restrict_employees_from_exam
    if @current_user.employee?
      @employee_subjects= @current_user.employee_record.subjects
      if @employee_subjects.empty? and !(Batch.active.collect(&:employee_id).include?(@current_user.employee_record.id.to_s)) and !@current_user.privileges.map{|p| p.name}.include?("ExaminationControl") and !@current_user.privileges.map{|p| p.name}.include?("EnterResults") and !@current_user.privileges.map{|p| p.name}.include?("ViewResults")
        flash[:notice] = "#{t('flash_msg4')}"
        redirect_to :controller => 'user', :action => 'dashboard'
      else
        @allow_for_exams = true
      end
    end
  end

  def block_unauthorised_entry
    if @current_user.employee?
      @employee_subjects= @current_user.employee_record.subjects
      if @employee_subjects.empty? and !@current_user.privileges.map{|p| p.name}.include?("ExaminationControl")
        flash[:notice] = "#{t('flash_msg4')}"
        redirect_to :controller => 'user', :action => 'dashboard'
      else
        @allow_for_exams = true
      end
    end
  end
  
  def initialize
    #    require 'net/http'
    #    require 'uri'
    #    require "yaml"
    
    @title = 'ClassTune'
    # @title = Champs21Setting.company_details[:company_name]
    #    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
    #    champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    #    api_endpoint = champs21_api_config['api_url']
    #    
    #    uri = URI(api_endpoint + "api/freeschool/getbanner")
    #    http = Net::HTTP.new(uri.host, uri.port)
    #    auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
    #    auth_req.set_form_data({"school_id" => MultiSchool.current_school.id})
    #    @current_user = current_user
    #    abort(@current_user.inspect)
    # 
    #    
    #    if champs21_config['from'] == "remote"
    #      auth_res = http.request(auth_req)    
    #      @auth_response = JSON::parse(auth_res.body)
    #      if @auth_response['status']['code']==200
    #          @obj_banner = @auth_response
    #      end  
    #    end
    
  end

  def message_user
    @current_user = current_user
    @main_user_data = main_user_data
    logger.info("Username : #{@current_user.username} Role : #{@current_user.role_name}") if @current_user.present?
  end
  
  def current_user
    User.active.find(session[:user_id]) unless session[:user_id].nil?
  end

  def main_user_data
    User.active.find(session[:user_id_main]) unless session[:user_id_main].nil?
  end

  
  def find_finance_managers
    Privilege.find_by_name('FinanceControl').users
  end

  def permission_denied
    flash[:notice] = "#{t('flash_msg4')}"
    redirect_to :controller => 'user', :action => 'dashboard'
  end

  def date_format_check
    begin
      @start_date= Date.parse(params[:start_date]) if params[:start_date]
      @end_date= Date.parse(params[:end_date]) if params[:end_date]
    rescue ArgumentError
    end

    if (@start_date.nil? or @end_date.nil?)
      flash[:notice]="#{t('invalid_date_format')}"
      redirect_to :controller => "user", :action => "dashboard"
      return false
    end
    return true
  end
  
  protected

  def set_precision
    precision_count = Configuration.get_config_value('PrecisionCount')
    @precision = precision_count.to_i < 2 ? 2 : precision_count.to_i > 9 ? 8 : precision_count
  end

  def set_host
    Champs21.present_user=current_user
    Champs21.present_student_id=session[:student_id]
    Champs21.hostname="#{request.protocol}#{request.host_with_port}"
    Champs21.rtl=RTL_LANGUAGES.include? I18n.locale.to_sym
  end
  def login_required
    unless session[:user_id]
      session[:back_url] = request.url
      redirect_to '/'
    end
  end

  def check_if_loggedin
    if session[:user_id]
      if !params[:connect_exam].blank? and !params[:batch_id].blank? and !params[:student].blank?
        redirect_to ({:controller => 'exam', :action => 'generated_report5_pdf', :connect_exam =>params[:connect_exam],:batch_id =>params[:batch_id],:student =>params[:student],:page_height=>450,:type=>"grouped"  })
      else
        redirect_to :controller => 'user', :action => 'dashboard'
      end
    end
  end

  def configuration_settings_for_hr
    hr = Configuration.find_by_config_value("HR")
    if hr.nil?
      redirect_to :controller => 'user', :action => 'dashboard'
      flash[:notice] = "#{t('flash_msg4')}"
    end
  end

  

  def configuration_settings_for_finance
    finance = Configuration.find_by_config_value("Finance")
    if finance.nil?
      redirect_to :controller => 'user', :action => 'dashboard'
      flash[:notice] = "#{t('flash_msg4')}"
    end
  end

  def only_admin_allowed
    redirect_to :controller => 'user', :action => 'dashboard' unless current_user.admin?
  end
  
  
  #EDITED FOR MULTIPLE GUARDIAN
  def protect_other_student_data
    if current_user.student? or current_user.parent?
      student = current_user.student_record if current_user.student?
      student = current_user.parent_record if current_user.parent?
      
      params[:id].nil?? student_id=session[:student_id]: student_id=params[:id]
      guardian_check = false
      if current_user.parent?
        gstd = current_user.guardian_entry.guardian_student
        gstd.each do|s| 
          if s.id == params[:id].to_i or (!params[:student].is_a? Hash and params[:student].to_i == s.id) or params[:student_id].to_i == s.id
            guardian_check = true
          end
        end
      end
      #      render :text =>student.id and return
      
      unless params[:id].to_i == student.id or params[:student].to_i == student.id or params[:student_id].to_i == student.id or guardian_check == true
       
        flash[:notice] = "#{t('flash_msg5')}" + params.to_s + "  " + params[:id].to_s + "  " + student.id.to_s
        redirect_to :controller=>"user", :action=>"dashboard"
      end
    end
  end

  #  def protect_other_student_data
  #    if current_user.student? or current_user.parent?
  #      student = current_user.student_record if current_user.student?
  #      student = current_user.parent_record if current_user.parent?
  #      #      render :text =>student.id and return
  #      params[:id].nil?? student_id=session[:student_id]: student_id=params[:id]
  #      unless params[:id].to_i == student.id or params[:student].to_i == student.id or params[:student_id].to_i == student.id or student.siblings.select{|s| s.immediate_contact_id==current_user.guardian_entry.id}.collect(&:id).include?student_id.to_i
  #       
  #        flash[:notice] = "#{t('flash_msg5')}" + params.to_s + "  " + params[:id].to_s + "  " + student.id.to_s
  #        redirect_to :controller=>"user", :action=>"dashboard"
  #      end
  #    end
  #  end

  def protect_user_data
    unless current_user.admin?
      unless params[:id].to_s == current_user.username or params[:id].to_s == main_user_data.username
        flash[:notice] = "#{t('flash_msg5')}"
        redirect_to :controller=>"user", :action=>"dashboard"
      end
    end
  end
  
  def limit_employee_profile_access
    unless @current_user.employee
      unless params[:id] == @current_user.employee_record.id
        priv = @current_user.privileges.map{|p| p.name}
        unless current_user.admin? or priv.include?("HrBasics") or priv.include?("EmployeeSearch")
          flash[:notice] = "#{t('flash_msg5')}"
          redirect_to :controller=>"user", :action=>"dashboard"
        end
      end
    end
  end

  def protect_other_employee_data
    if current_user.employee?
      employee = current_user.employee_record
      #    pri = Privilege.find(:all,:select => "privilege_id",:conditions=> 'privileges_users.user_id = ' + current_user.id.to_s, :joins =>'INNER JOIN `privileges_users` ON `privileges`.id = `privileges_users`.privilege_id' )
      #    privilege =[]
      #    pri.each do |p|
      #      privilege.push p.privilege_id
      #    end
      #    unless privilege.include?('9') or privilege.include?('14') or privilege.include?('17') or privilege.include?('18') or privilege.include?('19')
      unless params[:id].to_i == employee.id or current_user.role_symbols.include? "payslip_powers".to_sym
        flash[:notice] = "#{t('flash_msg5')}"
        redirect_to :controller=>"user", :action=>"dashboard"
      end
    end
  end

  def protect_leave_history
    if current_user.employee?
      employee = Employee.find(params[:id])
      employee_user = employee.user
      unless employee_user.id == current_user.id
        unless current_user.role_symbols.include?(:hr_basics) or current_user.role_symbols.include?(:employee_attendance)
          flash[:notice] = "#{t('flash_msg6')}"
          redirect_to :controller=>"user", :action=>"dashboard"
        end
      end
    end
  end
  #  end

  #reminder filters
  def protect_view_reminders
    reminder = Reminder.find(params[:id2])
    unless reminder.recipient == current_user.id
      flash[:notice] = "#{t('flash_msg5')}"
      redirect_to :controller=>"reminder", :action=>"index"
    end
  end

  def protect_sent_reminders
    reminder = Reminder.find(params[:id2])
    unless reminder.sender == current_user.id
      flash[:notice] = "#{t('flash_msg5')}"
      redirect_to :controller=>"reminder", :action=>"index"
    end
  end

  #employee_leaves_filters
  def protect_leave_dashboard
    employee = Employee.find(params[:id])
    employee_user = employee.user
    #    unless permitted_to? :employee_attendance_pdf, :employee_attendance
    unless employee_user.id == current_user.id
      flash[:notice] = "#{t('flash_msg6')}"
      redirect_to :controller=>"user", :action=>"dashboard"
      #    end
    end
  end
  
  #EDITED FOR MULTIPLE GUARDIAN
  #  def protect_applied_leave_parent
  #    applied_leave = ApplyLeaveStudent.find(params[:id])
  #    applied_student = Student.find(applied_leave.student_id);
  #    guardian = Guardian.find(applied_student.immediate_contact_id)
  #    unless guardian.user_id == current_user.id
  #      flash[:notice]="#{t('flash_msg5')}"
  #      redirect_to :controller=>"user", :action=>"dashboard"
  #    end
  #  end
  
  def protect_applied_leave_parent
    applied_leave = ApplyLeaveStudent.find(params[:id])
    applied_student = Student.find(applied_leave.student_id);
    
    guardian_check = false
    if current_user.parent?
      gstd = current_user.guardian_entry.guardian_student
      gstd.each do|s| 
        if s.id == applied_student.id
          guardian_check = true
        end
      end
    end
    unless guardian_check == true
      flash[:notice]="#{t('flash_msg5')}"
      redirect_to :controller=>"user", :action=>"dashboard"
    end
  end

  def protect_applied_leave
    applied_leave = ApplyLeave.find(params[:id])
    applied_employee = applied_leave.employee
    applied_employee_user = applied_employee.user
    unless applied_employee_user.id == current_user.id
      flash[:notice]="#{t('flash_msg5')}"
      redirect_to :controller=>"user", :action=>"dashboard"
    end
  end
  
  #  rescue_from ActiveRecord::RecordNotFound do
  #    flash[:notice]="#{t('flash_msg3')}"
  #    redirect_to :controller=>"user", :action=>"dashboard"
  #  end
  
  def protect_manager_leave_application_view
    if (action_name == "leave_application" and params[:target] == "student")
      applied_leave = ApplyLeaveStudent.find(params[:id])
      applied_student = Student.find(applied_leave.student_id)
      applied_leave_approving_teacher_id = applied_student.class_teacher_id
      
      unless applied_leave_approving_teacher_id == current_user.id or current_user.employee or current_user.admin? or current_user.privileges.map(&:name).include? 'HrBasics' or current_user.privileges.map(&:name).include? 'EmployeeAttendance'
        flash[:notice]="#{t('flash_msg5')}"
        redirect_to :controller=>"user", :action=>"dashboard"
      end
    else
      applied_leave = ApplyLeave.find(params[:id])
      applied_employee = applied_leave.employee
      applied_employees_manager = Employee.find_by_user_id(applied_employee.reporting_manager_id)
      applied_employees_manager_user = applied_employees_manager.user
      unless applied_employees_manager_user.id == current_user.id or current_user.admin? or current_user.privileges.map(&:name).include? 'HrBasics' or current_user.privileges.map(&:name).include? 'EmployeeAttendance'
        flash[:notice]="#{t('flash_msg5')}"
        redirect_to :controller=>"user", :action=>"dashboard"
      end
    end
    
  end

  def render(options = nil, extra_options = {}, &block)
    if RTL_LANGUAGES.include? I18n.locale.to_sym
      unless options.nil?
        unless request.xhr?
          if options.class == Hash
            if options[:pdf]
              options ||= {}
              options = options.merge(:zoom => 0.68) if options[:zoom].blank?
            end
          end
        end
      end
    end
    super(options, extra_options, &block)
  end

  def default_time_zone_present_time
    server_time = Time.now
    server_time_to_gmt = server_time.getgm
    @local_tzone_time = server_time
    time_zone = Configuration.find_by_config_key("TimeZone")
    unless time_zone.nil?
      unless time_zone.config_value.nil?
        zone = TimeZone.find(time_zone.config_value)
        if zone.difference_type=="+"
          @local_tzone_time = server_time_to_gmt + zone.time_difference
        else
          @local_tzone_time = server_time_to_gmt - zone.time_difference
        end
      end
    end
    return @local_tzone_time
  end

  #  def can_access_request? (action,controller)
  #    permitted_to?(action,controller)
  #  end

  def can_access_request? (privilege, object_or_sym = nil, options = {}, &block)
    permitted_to?(privilege, object_or_sym, options, &block)
  end

  def can_access_plugin?(plugin)
    Champs21Plugin.can_access_plugin?(plugin)
  end

  def can_access_feature?(feature)
    if Feature.find_by_feature_key(feature).try(:is_enabled)==false
      return false
    else
      return true
    end
  end

  def currency
    Configuration.currency 
  end

  private
  def set_user_language
    lan = Configuration.find_by_config_key("Locale")
    I18n.default_locale = :en
    Translator.fallback(true)
    if session[:language].nil?
      I18n.locale = lan.config_value
    else
      I18n.locale = session[:language]
    end
    News.new.reload_news_bar
  end
  
  def set_font_face
    font = Configuration.find_by_config_key("FontFace")
    unless font.nil?
      if session[:font].nil?
        session[:font] = font.config_value
      elsif session[:font].present? && session[:font] != font.config_value.to_s
        session[:font] = font.config_value
      end
    else
      session[:font] = 'arial'
    end
  end
  
  def page_not_found
    respond_to do |format|
      format.html { render :file => "#{Rails.root}/public/404.html", :status => :not_found }
      format.xml  { head :not_found }
      format.any  { head :not_found }
    end
  end
end
