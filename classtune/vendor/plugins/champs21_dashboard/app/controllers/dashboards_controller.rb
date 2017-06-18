class DashboardsController < ApplicationController
  before_filter :login_required
  before_filter :default_time_zone_present_time
  filter_access_to :all

  def index
      
      require "yaml"
      require "time"
      time_now = Time.now
      detention_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/detention.yml")['school']
      all_schools = detention_config['numbers'].split(",")
      current_school = MultiSchool.current_school.id
      
      @data = {}
      @view_layout = 'student'
      
      @allow_detention = false
      if all_schools.include?(current_school.to_s)
        @allow_detention = true      
      end
      time_diff = Time.now-time_now
      time_now = Time.now
      
      
#      if current_user.employee?
#        @view_layout = 'employee'
#        @news = News.find(:all,:conditions=>["is_published = 1 AND (department_news.department_id = ? or news.is_common = 1 or author_id=?)", current_user.employee_record.employee_department_id,current_user.id], :limit =>4,:include=>[:department_news])
#        
#        if check_free_school?
#          get_employee_homework
#          if @employee_homework_response['status']['code'].to_i == 200
#            @data['employee_homework'] = @employee_homework_response['data']['homework']
#          end
#        else
#          get_next_class_routine
#          if @next_routine_response['status']['code'].to_i == 200
#            @data['next_class'] = @next_routine_response['data']['time_table']
#          end
#
#          get_class_routine
#          if @routine_response['status']['code'].to_i == 200
#            @data['today_class'] = @routine_response['data']['time_table']
#          end
#        end
#      end
      
      if current_user.admin?
        time_diff1 = Time.now-time_now
        time_now = Time.now
        @news = News.find(:all,:conditions=>{:is_published=>1}, :limit =>3)
        time_diff2 = Time.now-time_now
        time_now = Time.now
        @view_layout = 'employee'
        
        if check_free_school?
          get_employee_homework
          if @employee_homework_response['status']['code'].to_i == 200
            @data['employee_homework'] = @employee_homework_response['data']['homework']
          end
        else
          get_next_class_routine_admin
          time_diff3 = Time.now-time_now
          time_now = Time.now
          if @next_routine_response['status']['code'].to_i == 200
            @data['next_class'] = @next_routine_response['data']['next_classess']
            @data['current_class'] = @next_routine_response['data']['current_class']
          end
        end
      end
      
     
    
      if current_user.parent? or current_user.student?
        get_homework
        if @homework_response['status']['code'].to_i == 200
          @data = @homework_response['data']['homework']
        end
      end
    
      if current_user.student?
        student = current_user.student_record
      end  
      if current_user.parent?
        target = current_user.guardian_entry.current_ward_id      
        student = Student.find_by_id(target)
      end
      
      @subject_id = 0
      @due_date = ''  
      if current_user.student? or current_user.parent?
        @batch = student.batch      
        @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"elective_group_id IS NULL AND is_deleted = false", :include => [:employees])
        @student_electives =StudentsSubject.all(:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
        @elective_subjects = []
        @student_electives.each do |e|
          @elective_subjects.push Subject.find(e.subject_id)
        end
        @subjects = @normal_subjects+@elective_subjects
        
        @news = News.find(:all,:conditions=>["is_published = 1 AND (batch_news.batch_id = ? or news.is_common = 1)", student.batch_id], :limit=>4,:include=>[:batch_news]) 
      
      
      end
    
      
      @event = Event.find(:last, :conditions=>" is_common = 1 AND is_exam = 0 AND is_due = 0 AND is_club = 0 AND end_date >= '" + I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')+ "'")
      time_diff4 = Time.now-time_now
      time_now = Time.now
      @att_text = ''
      @att_image = ''
      get_attendence_text
      if @attendence_text['status']['code'].to_i == 200
        @att_text = @attendence_text['data']['text']
        @att_image = @attendence_text['data']['profile_picture']
      end
      time_diff5 = Time.now-time_now
      time_now = Time.now
      
      @time_diff_string = "Time  : "+time_diff.to_s+" || Time 1 : "+time_diff1.to_s+" || Time 2 : "+time_diff2.to_s+" || Time 3 : "+time_diff3.to_s+" || Time 4 : "+time_diff4.to_s+" || Time 5 : "+time_diff5.to_s
      
#    end
  end
  
  def getglobalsearch  
    term = params[:term] 
    get_global_search(term)
    @search_r = []
    if @search_result['status']['code'].to_i == 200
        @search_r = @search_result['data']
    end
    render :partial=>"global_search", :locals=>{:search_r => @search_r }
  end
  
  def notice_data
    @notice = News.find(:all, :limit => 10)
    render :partial=>"notice_free", :locals=>{:news => @notice }
  end 
  
  def homework_data
    
    @subject_id = 0
    @due_date = ''
    hw_data = false
    
    unless params[:due_date].nil?
      @due_date = params[:due_date]
      hw_data = true
    end
    
    unless params[:subject_id].nil?
      @subject_id = params[:subject_id]
      hw_data = true
    end
    
    if current_user.student?
      student = current_user.student_record
    end  
    if current_user.parent?
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
    end
      
      
    if current_user.student? or current_user.parent?
      @batch = student.batch      
      @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"elective_group_id IS NULL AND is_deleted = false", :include => [:employees])
      @student_electives =StudentsSubject.all(:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
      @elective_subjects = []
      @student_electives.each do |e|
        @elective_subjects.push Subject.find(e.subject_id)
      end
      @subjects = @normal_subjects+@elective_subjects
    end
    
    get_homework(@subject_id, @due_date)
    @homework = []
    if @homework_response['status']['code'].to_i == 200
      @homework = @homework_response['data']['homework']
    end
    
    render :partial=>"homework", :locals=>{:homework=>@homework, :hw_data => hw_data}
    
  end

  def notice_main
    if current_user.admin?
      if params[:category] == "all"
        @notice = News.find(:all, :limit => 3)
      elsif params[:category] == "general"
        @notice = News.find(:all, :conditions=>"category_id = 1", :limit => 3)
      elsif params[:category] == "others"
        @notice = News.find(:all, :conditions=>"category_id != 1", :limit => 3)
      end 
    else
      if params[:category] == "all"
        @notice = News.find(:all, :limit => 4)
      elsif params[:category] == "general"
        @notice = News.find(:all, :conditions=>"category_id = 1", :limit => 4)
      elsif params[:category] == "others"
        @notice = News.find(:all, :conditions=>"category_id != 1", :limit => 4)
      end
    end
    
    render :partial=>"notice", :locals=>{:news => @notice, :type => params[:category] }
  end 

  def employee_homework_data
    
    @data = {}
    @view_layout = 'student'
    
    if current_user.employee? || current_user.admin?
      @view_layout = 'employee'
      get_employee_homework
      if @employee_homework_response['status']['code'].to_i == 200
        @data['employee_homework'] = @employee_homework_response['data']['homework']
      end
    end
    
    render :partial=>@view_layout + "_homework", :locals=>{:homework => @data}
  end
  
  def employee_task_data
    
    @data = {}
    @view_layout = 'student'
    
    if current_user.employee? || current_user.admin?
      @view_layout = 'employee'
      get_employee_task
      if @employee_task_response['status']['code'].to_i == 200
        @data['employee_task_for_me'] = @employee_task_response['data']['task_for_me']
        @data['employee_task_by_me'] = @employee_task_response['data']['task_by_me']
      end
    end
    
    render :partial=>@view_layout + "_task", :locals=>{:homework => @data}
  end
  
  def quiz_result_data_student
    get_quiz_result_data(params[:id])
    @data = {}
    if @quize_result_response['status']['code'].to_i == 200
      @data['quiz_result'] = @quize_result_response['data']['assesment']
    end
    
    respond_to do |format|
      format.js { render :action => 'quiz_result' }
    end
    
  end
  
  def employee_exam_routine_data
    
    @data = {}
    @view_layout = 'student'
    
    if current_user.employee? || current_user.admin?
      @view_layout = 'employee'
      get_employee_exam_routine
      if @employee_exam_routine_response['status']['code'].to_i == 200
        @data['employee_exam_routine'] = @employee_exam_routine_response['data']['time_table']
      end
    end
    
    render :partial=>@view_layout + "_exam_routine", :locals=>{:homework => @data}
  end
  
  def quize_data
    #    @user_palettes = current_user.own_dashboard
    get_quize_data
    @quize = []
    if @quize_response['status']['code'].to_i == 200
      @quize = @quize_response['data']['homework']
    end
    render :partial=>"quize", :locals=>{:quizes=>@quize}
    #@events = get_school_feed_champs21    
  end
  
  def employee_exam_routine_data
    
    @data = {}
    @view_layout = 'student'
    
    if current_user.employee? || current_user.admin?
      @view_layout = 'employee'
      get_employee_exam_routine
      if @employee_exam_routine_response['status']['code'].to_i == 200
        @data['employee_exam_routine'] = @employee_exam_routine_response['data']['time_table']
      end
    end
    
    render :partial=>@view_layout + "_exam_routine", :locals=>{:homework => @data}
  end
  
  def employee_quiz_data
    
    @data = {}
    @view_layout = 'student'
    
    if current_user.employee? || current_user.admin?
      @view_layout = 'employee'
      get_employee_quiz
      if @employee_quiz_response['status']['code'].to_i == 200
        @data['employee_exam_routine'] = @employee_quiz_response['data']['teacher_quiz']
      end
    end
    
    render :partial=>@view_layout + "_quiz", :locals=>{:homework => @data}
  end
  
  def get_school_feed_champs21
    require 'net/http'
    require 'uri'
    require "yaml"
    
    @user = current_user
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    username = champs21_api_config['username']
    password = champs21_api_config['password']
    
    if File.file?("#{RAILS_ROOT.to_s}/public/user_configs/feed_" + @user.id.to_s + "_config.yml")
      user_info = YAML.load_file("#{RAILS_ROOT.to_s}/public/user_configs/feed_" + @user.id.to_s + "_config.yml")

      
      if Time.now.to_i >= user_info[0]['api_info'][0]['user_cookie_exp'].to_i
        
        uri = URI(api_endpoint + "api/user/auth")
        http = Net::HTTP.new(uri.host, uri.port)
        auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
        auth_req.set_form_data({"username" => username, "password" => password})
        auth_res = http.request(auth_req)
        auth_response = ActiveSupport::JSON.decode(auth_res.body)

        ar_user_cookie = auth_res.response['set-cookie'].split('; ');
        
        user_info = [
          "api_info" => [
            "user_secret" => auth_response['data']['paid_user']['secret'],
            "user_cookie" => ar_user_cookie[0],
            "user_cookie_exp" => ar_user_cookie[2].split('=')[1].to_time.to_i
          ]
        ]        

        File.open("#{RAILS_ROOT.to_s}/public/user_configs/feed_" + @user.id.to_s + "_config.yml", 'w') {|f| f.write(YAML.dump(user_info)) }

      end
    else
      
      uri = URI(api_endpoint + "api/user/auth")
      http = Net::HTTP.new(uri.host, uri.port)
      auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
      auth_req.set_form_data({"username" => username, "password" => password})
      auth_res = http.request(auth_req)
      @auth_response = ActiveSupport::JSON.decode(auth_res.body)

      ar_user_cookie = auth_res.response['set-cookie'].split('; ');
      
      user_info = [
        "api_info" => [
          "user_secret" => @auth_response['data']['paid_user']['secret'],
          "user_cookie" => ar_user_cookie[0],
          "user_cookie_exp" => ar_user_cookie[2].split('=')[1].to_time.to_i
        ]
      ]
      File.open("#{RAILS_ROOT.to_s}/public/user_configs/feed_" + @user.id.to_s + "_config.yml", 'w') {|f| f.write(YAML.dump(user_info)) }

    end
    
    event_uri = URI(api_endpoint + "api/freeuser")
    http = Net::HTTP.new(event_uri.host, event_uri.port)
    event_req = Net::HTTP::Post.new(event_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => user_info[0]['api_info'][0]['user_cookie'] })
    event_req.set_form_data({"call_from_web"=>1,"user_secret" => user_info[0]['api_info'][0]['user_secret']})
    
    event_res = http.request(event_req)
    event_response = JSON::parse(event_res.body)
    
    if event_response['status']['code'].to_i == 200
      events = event_response['data']['post']
    end
    
    return events
  end
  
  
  def class_routine_data_student
    #    @user_palettes = current_user.own_dashboard
    get_class_routine_student
    @routine = []
    @nextclass = []
    if @routine_response['status']['code'].to_i == 200
      @routine = @routine_response['data']['time_table']
    end
    if @next_class['status']['code'].to_i == 200
      @nextclass = @next_class['data']['time_table']
    end
    render :partial=>"class_routine_data_student", :locals=>{:routine=>@routine,:student_id=>@student_id}
    #@events = get_school_feed_champs21    
  end
  
  def exam_routine_data_student
    #    @user_palettes = current_user.own_dashboard
    get_all_exam_routine
    @routine = []
    if @routine_response['status']['code'].to_i == 200
      @routine = @routine_response['data']['all_exam']
    end
    render :partial=>"exam_routine_data_student", :locals=>{:routine=>@routine}
    #@events = get_school_feed_champs21    
  end
  
  def exam_result_data_student
    #    @user_palettes = current_user.own_dashboard
    get_all_exam_result
    get_class_test_result
    @routine = []
    @class_test = []
    if @routine_response['status']['code'].to_i == 200
      @routine = @routine_response['data']['all_exam']
    end
    if @class_test_response['status']['code'].to_i == 200
      @class_test = @class_test_response['data']['all_exam']
    end
    render :partial=>"exam_result_data_student", :locals=>{:routine=>@routine,:class_test=>@class_test,:student_id=>@student_id}
    #@events = get_school_feed_champs21    
  end
  
  def routine_data
    
    @data = {}
    @view_layout = 'student'
    
    if current_user.employee?
      @view_layout = 'employee'
      get_next_class_routine
      @data['next_class'] = ""
      if @next_routine_response['status']['code'].to_i == 200
        @data['next_class'] = @next_routine_response['data']['time_table']
      end
      
      get_class_routine
      @data['today_class'] = ""
      if @routine_response['status']['code'].to_i == 200
        @data['today_class'] = @routine_response['data']['time_table']
      end
    end
    if current_user.admin?
      @view_layout = 'employee'
        get_next_class_routine_admin
        if @next_routine_response['status']['code'].to_i == 200
          @data['next_class'] = @next_routine_response['data']['next_classess']
          @data['current_class'] = @next_routine_response['data']['current_class']
        end
    end
    
    render :partial=>@view_layout + "_routine", :locals=>{:homework => @data}
  end
  
  
  private
  
  
  def get_attendence_text
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    
    if current_user.student or current_user.employee or current_user.admin?
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
  
  def get_class_test_result
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    
    if current_user.student
      homework_uri = URI(api_endpoint + "api/report/allexam")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"category_id"=>1,"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @class_test_response = JSON::parse(homework_res.body)
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      homework_uri = URI(api_endpoint + "api/report/allexam")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"category_id"=>1,"school"=>student.school_id,"batch_id"=>student.batch_id,"student_id"=>student.id,"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @class_test_response = JSON::parse(homework_res.body)
    end
    
    @class_test_response
  end
  
  def get_all_exam_result
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    
    if current_user.student
      homework_uri = URI(api_endpoint + "api/report/allexam")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @routine_response = JSON::parse(homework_res.body)
      @student_id = current_user.student_record.id
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      homework_uri = URI(api_endpoint + "api/report/allexam")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"school"=>student.school_id,"batch_id"=>student.batch_id,"student_id"=>student.id,"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @routine_response = JSON::parse(homework_res.body)
      @student_id = student.id
    end
    
    @routine_response
  end
  
  def get_all_exam_routine
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']


    if current_user.student
      homework_uri = URI(api_endpoint + "api/routine/allexam")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @routine_response = JSON::parse(homework_res.body)
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      homework_uri = URI(api_endpoint + "api/routine/allexam")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"school"=>student.school_id,"batch_id"=>student.batch_id,"student_id"=>student.id,"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @routine_response = JSON::parse(homework_res.body)
    end
    
    @routine_response
  end
  
  def get_class_routine_student
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']


    if current_user.student
      homework_uri = URI(api_endpoint + "api/routine")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"daily"=>1,"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @routine_response = JSON::parse(homework_res.body)
      
      homework_uri = URI(api_endpoint + "api/routine/nextclassstudent")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @next_class = JSON::parse(homework_res.body)
      @student_id = current_user.student_record.id
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      homework_uri = URI(api_endpoint + "api/routine")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"daily"=>1,"batch_id"=>student.batch_id,"student_id"=>student.id,"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @routine_response = JSON::parse(homework_res.body)
      
      homework_uri = URI(api_endpoint + "api/routine/nextclassstudent")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"batch_id"=>student.batch_id,"student_id"=>student.id,"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @next_class = JSON::parse(homework_res.body)
      @student_id = student.id
      
      
    end
    @next_class
    @routine_response
  end
  
  def get_homework(subject_id = 0, due_date = '')
    require 'net/http'
    require 'uri'
    require "yaml"
    form_data = {}
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    form_data['user_secret'] = session[:api_info][0]['user_secret']
    form_data['call_from_web'] = 1
    if current_user.student
      homework_uri = URI(api_endpoint + "api/homework")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      homework_uri = URI(api_endpoint + "api/homework")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      form_data['batch_id'] = student.batch_id
      form_data['student_id'] = student.id
    end
    
    if subject_id.to_i > 0
      form_data['subject_id'] = subject_id
    end
    
    unless due_date.blank?
      form_data['duedate'] = due_date
    end
    
    homework_req.set_form_data(form_data)
    homework_res = http.request(homework_req)
    @homework_response = JSON::parse(homework_res.body)
  end
  
  def get_quize_data
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.student
      homework_uri = URI(api_endpoint + "api/homework/assessment")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"call_from_web"=>1,"not_started"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @quize_response = JSON::parse(homework_res.body)
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      homework_uri = URI(api_endpoint + "api/homework/assessment")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"batch_id"=>student.batch_id,"student_id"=>student.id,"call_from_web"=>1,"not_started"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @quize_response = JSON::parse(homework_res.body)
    end
    
    @quize_response
  end
  
  def get_class_routine
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    api_uri = URI(api_endpoint + "api/routine")

    if current_user.employee? || current_user.admin?
      api_uri = URI(api_endpoint + "api/routine/teacher")
    end

    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })

    if current_user.employee? || current_user.admin?
      request.set_form_data({"school"=>MultiSchool.current_school.id, "call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})
    end
    
    response = http.request(request)
    @routine_response = JSON::parse(response.body)
    
    @routine_response
  end
  
  def get_next_class_routine_admin
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.employee? || current_user.admin?
      api_uri = URI(api_endpoint + "api/routine/nextclassadmin")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      request.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret'], "school" => MultiSchool.current_school.id})

      response = http.request(request)
      @next_routine_response = JSON::parse(response.body)
    end
    
    @next_routine_response
  end
  
  def get_next_class_routine
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.employee? || current_user.admin?
      api_uri = URI(api_endpoint + "api/routine/nextClass")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      request.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret'], "school" => MultiSchool.current_school.id})

      response = http.request(request)
      @next_routine_response = JSON::parse(response.body)
    end
    
    @next_routine_response
  end
  
  def get_employee_homework
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.employee? || current_user.admin?
      api_uri = URI(api_endpoint + "api/homework/teacherhomework")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      request.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret'], "page_size" => '9'})

      response = http.request(request)
      @employee_homework_response = JSON::parse(response.body)
    end
    
    @employee_homework_response
  end
  
  def get_employee_task
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.employee? || current_user.admin?
      api_uri = URI(api_endpoint + "api/task")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      request.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret'], "school" => MultiSchool.current_school.id})

      response = http.request(request)
      @employee_task_response = JSON::parse(response.body)
    end
    
    @employee_task_response
  end
  
  def get_employee_exam_routine
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.employee? || current_user.admin?
      api_uri = URI(api_endpoint + "api/routine/teacherExam")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      request.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      response = http.request(request)
      @employee_exam_routine_response = JSON::parse(response.body)
    end
    
    @employee_exam_routine_response
  end
  
  def get_employee_quiz
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.employee? || current_user.admin?
      api_uri = URI(api_endpoint + "api/homework/teacherQuiz")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      request.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      response = http.request(request)
      @employee_quiz_response = JSON::parse(response.body)
    end
    
    @employee_quiz_response
  end
  
   def get_quiz_result_data(id)
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    form_data = {}
    form_data['user_secret'] = session[:api_info][0]['user_secret']
    form_data['id'] = id
    
    api_uri = URI(api_endpoint + "api/homework/assessmentScore")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      form_data['student_id'] = student.id
      form_data['batch_id'] = student.batch_id
    end
    
    request.set_form_data(form_data)

    response = http.request(request)
    @quize_result_response = JSON::parse(response.body)
  end
  
  def get_global_search(term)
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    form_data = {}
    form_data['user_secret'] = session[:api_info][0]['user_secret']
    form_data['term'] = term
    
    api_uri = URI(api_endpoint + "api/event/globalsearch")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      form_data['student_id'] = student.id
      form_data['batch_id'] = student.batch_id
    end
    
    form_data['call_from_web'] = 1
    
    request.set_form_data(form_data)

    response = http.request(request)
    @search_result = JSON::parse(response.body)
  end
  
end
