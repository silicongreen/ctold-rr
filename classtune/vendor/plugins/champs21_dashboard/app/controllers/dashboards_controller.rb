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
      
      @school_code = MultiSchool.current_school.code
      @data = {}
      @view_layout = 'student'
      
      @allow_detention = false
      if all_schools.include?(current_school.to_s)
        @allow_detention = true      
      end
      time_diff = Time.now-time_now
      time_now = Time.now
      
      
      if current_user.employee?
        @view_layout = 'employee'
        @news = News.find(:all,:conditions=>["is_published = 1 AND (department_news.department_id = ? or news.is_common = 1 or author_id=? or user_news.user_id = ?)", current_user.employee_record.employee_department_id,current_user.id,current_user.id], :limit =>4,:include=>[:department_news,:user_news])
        
        if check_free_school?
          get_employee_homework
          if @employee_homework_response['status']['code'].to_i == 200
            @data['employee_homework'] = @employee_homework_response['data']['homework']
          end
        else
          get_next_class_routine
          if @next_routine_response['status']['code'].to_i == 200
            @data['next_class'] = @next_routine_response['data']['time_table']
          end

          get_class_routine
          if @routine_response['status']['code'].to_i == 200
            @data['today_class'] = @routine_response['data']['time_table']
          end
        end
      end
      time_diff1 = Time.now-time_now
      time_now = Time.now
      if current_user.admin?
        time_diff_if = Time.now-time_now
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
        
        @news = News.find(:all,:conditions=>["is_published = 1 AND (batch_news.batch_id = ? or user_news.user_id = ? or news.is_common = 1)", student.batch_id,student.user_id], :limit=>4,:include=>[:batch_news,:user_news]) 
      
      
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
      
      @time_diff_string = "Time  : "+time_diff.to_s+" || Time 1 : "+time_diff1.to_s+" || Time if : "+time_diff_if.to_s+" || Time 2 : "+time_diff2.to_s+" || Time 3 : "+time_diff3.to_s+" || Time 4 : "+time_diff4.to_s+" || Time 5 : "+time_diff5.to_s
      
#    end
  end
  
  ####
  ## Huffas's Work for the Admin Dashboard
  ####
  
  def get_summary_strip
    date_today = @local_tzone_time.to_date
    yesterday  = @local_tzone_time.to_date - 1.day
    user_login = UserLogin.find_all_by_date(date_today.strftime("%Y-%m-%d"))
    
    @user_login = user_login.length
    
    @user_login_web = user_login.select{|u| u.login_from == 'web' }.length
    @user_login_mobile = user_login.select{|u| u.login_from == 'mobile' }.length
    
    user_attendance = AttendanceRegister.find_all_by_attendance_date(date_today.strftime("%Y-%m-%d"))
    @user_attendance = 0
    unless user_attendance.nil? or user_attendance.empty?
      user_attendance.each do |attendance|
        @user_attendance += attendance.present
      end
    end

    @campus_attendances_count = 0 #CampusAttendance.find(:all, :conditions=>"date = '" + date_today.strftime("%Y-%m-%d").to_s + "' and type_data = 2", :group => "user_id").length
    @hr_attendances_count = CardAttendance.find(:all, :conditions=>"date = '" + date_today.strftime("%Y-%m-%d").to_s + "' and type_data = 1", :group => "user_id").length
    
    render :partial => '/dashboards/partial/ajax/summary_strip'
  end
  
  def get_news
    @news = News.find(:all,:conditions=>{:is_published=>1}, :limit =>3, :order => "id desc")
    render :partial => '/dashboards/partial/ajax/news'
  end
  
  def get_events
    from_date = @local_tzone_time.to_date
    to_date = @local_tzone_time.to_date + 7

    @events = Event.find(:all,:conditions=>["((start_date < ? and end_date > ?) OR (start_date < ? and end_date > ?) OR (start_date < ? and end_date > ?) OR (start_date > ?)) and is_common = 1 and is_published = 1", from_date, from_date, to_date, to_date, to_date, from_date, from_date], :limit =>4, :order => "id desc")
    render :partial => '/dashboards/partial/ajax/events'
  end
  
  def get_lesson_plan
    require 'date'
    @lessonplans = Lessonplan.find_all_by_tte_id_and_subject_id_and_author_id_and_publish_date_and_is_show(params[:id], params[:subject_id], params[:author_id], Date.parse(params[:time_class]).strftime("%Y-%m-%d"), 1)
    render :partial => '/dashboards/partial/lesson_plans'
  end
  
  def get_own_summary
    if current_user.admin?
      @departments = EmployeeDepartment.active
      date_today = @local_tzone_time.to_date
      yesterday  = @local_tzone_time.to_date - 1.day

      lessonplan_today = Lessonplan.find(:all, :conditions => "created_at >= '" + date_today.strftime("%Y-%m-%d 00:00:00") + "' and created_at <= '" + date_today.strftime("%Y-%m-%d 23:59:59") + "'").length
      @lessonplan_summary_today_all = Lessonplan.find(:all, :conditions => "created_at >= '" + date_today.strftime("%Y-%m-%d 00:00:00") + "' and created_at <= '" + date_today.strftime("%Y-%m-%d 23:59:59") + "'").length
      lessonplan_publish_today = Lessonplan.find(:all, :conditions => "publish_date = '" + date_today.strftime("%Y-%m-%d") + "'").length

      @total_lessonplan_all = lessonplan_today + @lessonplan_summary_today_all + lessonplan_publish_today

      last_week = @local_tzone_time.to_date - 7

      assignments_id = Assignment.find(:all, :conditions => "duedate >= '" + date_today.strftime("%Y-%m-%d 00:00:00") + "' and duedate <= '" + last_week.strftime("%Y-%m-%d 23:59:59") + "'").map(&:id)
      @assignments_submitted_all = 0
      unless assignments_id.nil? or assignments_id.empty?
        @assignments_submitted_all = AssignmentAnswer.find(:all, :conditions => "assignment_id IN (" +  assignments_id.join(",") + ")").length
      end
    end
    user_summary(current_user.employee_record.id, current_user.id)
    render :partial => "/dashboards/partial/ajax/own_summary"
  end
  
  def get_attendace_graph
    date_today = @local_tzone_time.to_date
    last_week = @local_tzone_time.to_date - 7

    @total_presents = []
    @total_absents = []
    i = 0
    (last_week..date_today).each do |d|
      user_attendance_by_date = AttendanceRegister.find(:all, :conditions => ["attendance_date = ? ", d.strftime("%Y-%m-%d")])
      present = 0
      absent = 0
      unless user_attendance_by_date.nil? or user_attendance_by_date.empty?
        user_attendance_by_date.each do |user_attendance|
          present += user_attendance.present
          absent += user_attendance.absent
        end
      end

      @total_presents[i] = present
      @total_absents[i] = absent
      i += 1
    end
    render :partial => '/dashboards/partial/ajax/attendance_graph'
  end
  
  def get_course_report
    @batches = []
    @courses = []
    @classes = []
    if current_user.admin?
      @batches = Batch.active
    elsif @current_user.privileges.map{|p| p.name}.include?('StudentAttendanceRegister')
      @batches = Batch.active
    elsif @current_user.employee?
      if @config.config_value == 'Daily'
        @batches = @current_user.employee_record.batches
      else
        @batches = @current_user.employee_record.batches
        @batches += @current_user.employee_record.subjects.collect{|b| b.batch}
        @batches += TimetableSwap.find_all_by_employee_id(@current_user.employee_record.try(:id)).map(&:subject).flatten.compact.map(&:batch)
        @batches = @batches.uniq unless @batches.empty?
      end
    end
    render :partial => "/dashboards/ajax/batches"
  end
  
  def get_courses
    school_id = MultiSchool.current_school.id
    @batch_name = false
    unless params[:batch_id].empty?
        batch_data = Batch.find params[:batch_id]
        batch_name = batch_data.name
    end 
    @courses = []
    unless batch_name.blank?
      @courses = Rails.cache.fetch("classes_data_#{batch_name.parameterize("_")}_#{school_id}"){
        @batch_name = batch_name;
        batches = Batch.find(:all, :conditions => ["name = ? and is_deleted = 0", batch_name]).map{|b| b.course_id}
        tmp_classes = Course.find(:all, :conditions => ["id IN (?) and is_deleted = 0", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
        class_data = tmp_classes
        class_data
      }
    end
    render :partial => '/dashboards/ajax/courses'
  end
  
  def get_sections
    @batch_name = ""
    @class_name = ""
    batch_id = 0
    
    school_id = MultiSchool.current_school.id
    
    batch_name = ""
    if batch_id.to_i > 0
        batch = Batch.find batch_id
        batch_name = batch.name
        @batch_name = batch_name
    end
    
    unless batch_name.blank?    
      @classes = Rails.cache.fetch("section_data_#{params[:class_name].parameterize("_")}_#{batch_name.parameterize("_")}_#{school_id}"){
        batches = Batch.find(:all, :conditions => ["name = ? and is_active = 1 and is_deleted = 0", batch_name]).map{|b| b.course_id}
        tmp_class_data = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id IN (?)",params[:class_name], batches])
        tmp_class_data
      }     
    else    
      @classes = Rails.cache.fetch("section_data_#{params[:class_name].parameterize("_")}_#{school_id}"){
          tmp_class_data = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0",params[:class_name]])
          tmp_class_data
      }
    end
    @selected_section = 0
    
    @batch_id = 0
    @courses = []
    
    @class_name = params[:class_name]
    if batch_id.to_i > 0
        batch = Batch.find batch_id
        @batch_name = batch.name
    end
    
    render :partial => '/dashboards/ajax/sections'
  end
  
  def get_routines_data
    get_routines(current_user.employee_record.id)
    if current_user.admin?
      get_routines_all(0)
    end
    render :partial => '/dashboards/partial/ajax/current_running'
  end
  
  def get_all_routines
    unless params[:filter_enable].nil? or params[:filter_enable].empty?
      if params[:filter_enable].to_i == 1
          routine_for_section = params[:routine_for_section]
          course_id = params[:course_id]
          if  routine_for_section.to_i == 1 
            section_id = params[:section_id]
            courses = Course.find(section_id)
            unless courses.nil? or courses.blank?
              courses_id = courses.id
            end
          elsif  routine_for_section.to_i == 0
            courses = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0",course_id])
            unless courses.nil? or courses.empty?
              courses_id = courses.map(&:id)
            end
            #abort(courses_id.inspect)
          end
          if courses_id.kind_of?(Array)
            batches = Batch.active.find(:all, :conditions => ["course_id IN (?)", courses_id.join(",")]).map(&:id)
          else
            batches = Batch.active.find(:all, :conditions => ["course_id = ?", courses_id.to_s]).map(&:id)
          end
          #abort(batches.inspect)
      end
    end
    get_routines(current_user.employee_record.id)
    if current_user.admin?
      get_routines_all(0)
    end
    render :partial => '/dashboards/partial/ajax/routine'
  end
  
  def get_tasks_count
    from_date = @local_tzone_time.to_date
    to_date = @local_tzone_time.to_date + 7
    
    @tasks_assigned = Task.find(:all, :conditions => ["((tasks.start_date < ? and tasks.due_date > ?) OR (tasks.start_date < ? and tasks.due_date > ?) OR (tasks.start_date < ? and tasks.due_date > ?)) and task_assignees.assignee_id = ?", from_date, from_date, to_date, to_date, to_date, from_date, current_user.id], :joins => "Inner Join task_assignees ON task_assignees.task_id = tasks.id")
        
    @tasks_assigned_active = @tasks_assigned.select{|t| t.task_status == 0}.length
    @tasks_assigned_done = @tasks_assigned.select{|t| t.task_status == 1}.length
    render :partial => '/dashboards/partial/ajax/task'
  end
  
  def get_graph_class
    graph_for_section = params[:graph_for_section]
    course_id = params[:course_id]
    if  graph_for_section.to_i == 1 
      section_id = params[:section_id]
      courses = Course.find(section_id)
      unless courses.nil? or courses.blank?
        courses_id = courses.id
      end
    elsif  graph_for_section.to_i == 0
      courses = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0",course_id])
      unless courses.nil? or courses.empty?
        courses_id = courses.map(&:id)
      end
    end
    
    date_today = @local_tzone_time.to_date
    last_week = @local_tzone_time.to_date - 7
    
    if courses_id.kind_of?(Array)
      s_present_classes = ""
      s_absent_classes = ""
      courses_id.each do |course|  
        batches = Batch.active.find_all_by_course_id(course).map(&:id)
        total_presents = []
        total_absents = []
        i = 0
        (last_week..date_today).each do |d|
            present = 0
            absent = 0
            user_attendance_by_date = AttendanceRegister.find(:all, :conditions => ["attendance_date = ? and batch_id IN (?) ", d.strftime("%Y-%m-%d"), batches.join(",")], :order => "batch_id asc")
            unless user_attendance_by_date.nil? or user_attendance_by_date.empty?
              user_attendance_by_date.each do |user_attendance|
                present += user_attendance.present
                absent += user_attendance.absent
              end
              total_presents[i] = present
              total_absents[i] = absent
            else  
              if MultiSchool.current_school.code == "chs" 
                total_presents[i] = rand(200) + 40
                if total_presents[i] < 200
                  total_absents[i] = 200 - total_presents[i]
                  if total_absents[i] > total_presents[i]
                    diff = total_absents[i] - total_presents[i]
                    total_presents[i] = total_presents[i] + diff
                    total_absents[i] = total_absents[i] - diff
                  end
                else
                  total_presents[i] = 200
                  total_absents[i] = 0
                end
              else
                total_presents[i] = 0
                total_absents[i] = 0
              end
            end
            i += 1
        end
        course = Course.find(course)
        s_present_classes += course.course_name.to_s + ", Section: " + course.section_name.to_s + "+++" + total_presents.join(",,,") + "---"
        s_absent_classes += course.course_name.to_s + ", Section: " + course.section_name.to_s + "+++" + total_absents.join(",,,") + "---"
      end
      s_present_classes = s_present_classes[0, s_present_classes.length - 3]
      s_absent_classes = s_absent_classes[0, s_absent_classes.length - 3]
    else  
      course = courses_id
      s_present_classes = ""
      s_absent_classes = ""
  
      batches = Batch.active.find_all_by_course_id(course).map(&:id)
      total_presents = []
      total_absents = []
      i = 0
      (last_week..date_today).each do |d|
          present = 0
          absent = 0
          user_attendance_by_date = AttendanceRegister.find(:all, :conditions => ["attendance_date = ? and batch_id IN (?) ", d.strftime("%Y-%m-%d"), batches.join(",")], :order => "batch_id asc")
          unless user_attendance_by_date.nil? or user_attendance_by_date.empty?
            user_attendance_by_date.each do |user_attendance|
              present += user_attendance.present
              absent += user_attendance.absent
            end
            total_presents[i] = present
            total_absents[i] = absent
          else  
            if MultiSchool.current_school.code == "chs" 
              total_presents[i] = rand(200) + 40
              if total_presents[i] < 200
                total_absents[i] = 200 - total_presents[i]
                if total_absents[i] > total_presents[i]
                  diff = total_absents[i] - total_presents[i]
                  total_presents[i] = total_presents[i] + diff
                  total_absents[i] = total_absents[i] - diff
                end
              else
                total_presents[i] = 200
                total_absents[i] = 0
              end
            else
              total_presents[i] = 0
              total_absents[i] = 0
            end
          end
          i += 1
      end
      course = Course.find(course)
      s_present_classes += course.course_name.to_s + ", Section: " + course.section_name.to_s + "+++" + total_presents.join(",,,")
      s_absent_classes += course.course_name.to_s + ", Section: " + course.section_name.to_s + "+++" + total_absents.join(",,,")
    end
    
    render :text => s_present_classes + "~~~" + s_absent_classes
  end
  
  ####
  ## 
  ####
  
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
    elsif current_user.employee?
      
      if params[:category] == "all"
        @notice = News.find(:all,:conditions=>["is_published = 1 AND (department_news.department_id = ? or news.is_common = 1 or author_id=? or user_news.user_id = ?)", current_user.employee_record.employee_department_id,current_user.id,current_user.id], :limit =>4,:include=>[:department_news,:user_news])
      elsif params[:category] == "general"
        @notice = News.find(:all,:conditions=>["is_published = 1 AND (department_news.department_id = ? or news.is_common = 1 or author_id=? or user_news.user_id = ?) and category_id = 1", current_user.employee_record.employee_department_id,current_user.id,current_user.id], :limit =>4,:include=>[:department_news,:user_news])
      elsif params[:category] == "others"
        @notice = News.find(:all,:conditions=>["is_published = 1 AND (department_news.department_id = ? or news.is_common = 1 or author_id=? or user_news.user_id = ?) and category_id != 1", current_user.employee_record.employee_department_id,current_user.id,current_user.id], :limit =>4,:include=>[:department_news,:user_news])
      end
    else
      if current_user.student?
        student = current_user.student_record
      end  
      if current_user.parent?
        target = current_user.guardian_entry.current_ward_id      
        student = Student.find_by_id(target)
      end
      if params[:category] == "all"
        @notice = News.find(:all,:conditions=>["is_published = 1 AND (batch_news.batch_id = ? or user_news.user_id = ? or news.is_common = 1)", student.batch_id,student.user_id], :limit=>4,:include=>[:batch_news,:user_news]) 
      elsif params[:category] == "general"
        @notice = News.find(:all,:conditions=>["is_published = 1 AND (batch_news.batch_id = ? or user_news.user_id = ? or news.is_common = 1) and category_id = 1", student.batch_id,student.user_id], :limit=>4,:include=>[:batch_news,:user_news]) 
      elsif params[:category] == "others"
        @notice = News.find(:all,:conditions=>["is_published = 1 AND (batch_news.batch_id = ? or user_news.user_id = ? or news.is_common = 1) and category_id != 1", student.batch_id,student.user_id], :limit=>4,:include=>[:batch_news,:user_news])
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
  
  def get_routines(employee_id)
    date_today = @local_tzone_time.to_date
    
    current_tts = Timetable.find(:all,:conditions=>["timetables.start_date <= ? AND timetables.end_date >= ? and is_active = 1", date_today, date_today]).map(&:id)
    @time_table_found = false
    @assign_to_class = false
    
    unless current_tts.nil? or current_tts.empty?
      @time_table_found = true
      weekdays = TimetableEntry.find(:all, :conditions => "employee_id = " + employee_id.to_s + " and timetable_id In (" + current_tts.join(",") + ")", :order => "weekday_id asc", :group => "weekday_id, class_timing_id, subject_id, employee_id, timetable_id").map(&:weekday_id).uniq
      
      unless weekdays.nil? or weekdays.empty?
        @assign_to_class = true
        current_weekday = date_today.wday
        need_weekday = -1
        inc = 0
        weekdays.each do |weekday|
          if weekday >= current_weekday
            inc = weekday - current_weekday
            need_weekday = weekday
            break
          end
        end

        if need_weekday == -1
          inc = 6 - current_weekday
          need_weekday = weekdays[0]
          inc = inc + need_weekday.to_i + 1
        end
        new_date = @local_tzone_time.to_date + inc.to_i

        @next_class_date = new_date

        @tts = TimetableEntry.find(:all, :conditions => "employee_id = " + employee_id.to_s + " and weekday_id = " + @next_class_date.wday.to_s + " and timetable_id In (" + current_tts.join(",") + ")", :order => "class_timing_id asc", :group => "weekday_id, class_timing_id, subject_id, employee_id, timetable_id")

        i = 0
        j = 0
        k = 0
        @completed_class = []
        @next_class = []
        @running_class = []
        @time_difference = []
        @hours = []
        @minutes = []
        @seconds = []
        
        require 'time'
        @tts.each do |tt|
          class_timing_id = tt.class_timing_id
          classtiming = ClassTiming.find(class_timing_id)
          start_time = classtiming.start_time.change(:year => new_date.strftime("%Y").to_i, :day => new_date.strftime("%d").to_i, :month => new_date.strftime("%m").to_i).to_time
          
          end_time = classtiming.end_time.change(:year => new_date.strftime("%Y").to_i, :day => new_date.strftime("%d").to_i, :month => new_date.strftime("%m").to_i).to_time
          
          if ( @local_tzone_time.to_time > start_time and @local_tzone_time.to_time > end_time )
            @completed_class[i] = tt.id
            i += 1
          elsif ( @local_tzone_time.to_time > start_time and @local_tzone_time.to_time < end_time )
            seconds_diff = (end_time - @local_tzone_time.to_time).to_i.abs
            hours = seconds_diff / 3600
            seconds_diff -= hours * 3600

            minutes = seconds_diff / 60
            seconds_diff -= minutes * 60

            seconds = seconds_diff
            @hours[tt.id] = hours.to_s.rjust(2, '0')
            @minutes[tt.id] = minutes.to_s.rjust(2, '0')
            @seconds[tt.id] = seconds.to_s.rjust(2, '0')
            @time_difference[tt.id]  = "#{hours.to_s.rjust(2, '0')}:#{minutes.to_s.rjust(2, '0')}:#{seconds.to_s.rjust(2, '0')}"
            @running_class[k] = tt.id
            k += 1
          else
            @next_class[j] = tt.id
            j += 1
          end
        end
      end
    end
  end
  
  def get_routines_all(employee_id)
    date_today = @local_tzone_time.to_date
    current_tts = Timetable.find(:all,:conditions=>["timetables.start_date <= ? AND timetables.end_date >= ? and is_active = 1", date_today, date_today]).map(&:id)
    @invalid_weekdays = true
    
    unless current_tts.nil? or current_tts.empty?
      weekdays = TimetableEntry.find(:all, :conditions => "timetable_id In (" + current_tts.join(",") + ")", :order => "weekday_id asc", :group => "weekday_id, class_timing_id, subject_id, employee_id, timetable_id").map(&:weekday_id).uniq
      
      unless weekdays.nil? or weekdays.empty?
        @invalid_weekdays = false
        current_weekday = date_today.wday
        need_weekday = -1
        inc = 0
        weekdays.each do |weekday|
          if weekday >= current_weekday
            inc = weekday - current_weekday
            need_weekday = weekday
            break
          end
        end

        if need_weekday == -1
          inc = 6 - current_weekday
          need_weekday = weekdays[0]
          inc = inc + need_weekday.to_i + 1
        end
        
        new_date = @local_tzone_time.to_date + inc.to_i

        @next_class_date = new_date
        
        @tts_all = TimetableEntry.find(:all, :conditions => "weekday_id = " + @next_class_date.wday.to_s + " and timetable_id In (" + current_tts.join(",") + ")", :order => "class_timing_id asc", :group => "weekday_id, class_timing_id, subject_id, employee_id, timetable_id")

        i = 0
        j = 0
        k = 0
        @completed_class_all = []
        @next_class_all = []
        @running_class_all = []
        @time_difference_all = []
        @hours_all = []
        @minutes_all = []
        @seconds_all = []
        
        @tts_all.each do |tt|
          class_timing_id = tt.class_timing_id
          classtiming = ClassTiming.find(class_timing_id)
          start_time = classtiming.start_time.change(:year => new_date.strftime("%Y").to_i, :day => new_date.strftime("%d").to_i, :month => new_date.strftime("%m").to_i).to_time
          
          end_time = classtiming.end_time.change(:year => new_date.strftime("%Y").to_i, :day => new_date.strftime("%d").to_i, :month => new_date.strftime("%m").to_i).to_time
          
          if ( @local_tzone_time.to_time > start_time and @local_tzone_time.to_time > end_time )
            if employee_id.to_i > 0
              if tt.employee_id == employee_id.to_i
                @completed_class_all[i] = tt.id
                i += 1
              end  
            else  
              @completed_class_all[i] = tt.id
              i += 1
            end
          elsif ( @local_tzone_time.to_time >= start_time and @local_tzone_time.to_time < end_time )
            if employee_id.to_i > 0
              if tt.employee_id == employee_id.to_i
                seconds_diff = (end_time - @local_tzone_time.to_time).to_i.abs
                hours = seconds_diff / 3600
                seconds_diff -= hours * 3600

                minutes = seconds_diff / 60
                seconds_diff -= minutes * 60

                seconds = seconds_diff
                @hours_all[tt.id] = hours.to_s.rjust(2, '0')
                @minutes_all[tt.id] = minutes.to_s.rjust(2, '0')
                @seconds_all[tt.id] = seconds.to_s.rjust(2, '0')
                @time_difference_all[tt.id]  = "#{hours.to_s.rjust(2, '0')}:#{minutes.to_s.rjust(2, '0')}:#{seconds.to_s.rjust(2, '0')}"
                
                @running_class_all[k] = tt.id
                k += 1
              end  
            else  
              seconds_diff = (end_time - @local_tzone_time.to_time).to_i.abs
              hours = seconds_diff / 3600
              seconds_diff -= hours * 3600

              minutes = seconds_diff / 60
              seconds_diff -= minutes * 60

              seconds = seconds_diff
              @hours_all[tt.id] = hours.to_s.rjust(2, '0')
              @minutes_all[tt.id] = minutes.to_s.rjust(2, '0')
              @seconds_all[tt.id] = seconds.to_s.rjust(2, '0')
              @time_difference_all[tt.id]  = "#{hours.to_s.rjust(2, '0')}:#{minutes.to_s.rjust(2, '0')}:#{seconds.to_s.rjust(2, '0')}"
              
              @running_class_all[k] = tt.id
              k += 1
            end
          else
            if employee_id.to_i > 0
              if tt.employee_id == employee_id.to_i
                @next_class_all[j] = tt.id
                j += 1
              end    
            else  
              @next_class_all[j] = tt.id
              j += 1
            end
          end
        end
      end
    end
  end
  
  def user_summary(employee_id, user_id)
    date_today = @local_tzone_time.to_date
    assignments = Assignment.find(:all, :conditions => "employee_id = " +  employee_id.to_s + " and duedate >= '" + date_today.strftime("%Y-%m-%d 00:00:00") + "' and duedate <= '" + date_today.strftime("%Y-%m-%d 23:59:59") + "'").length
        
    assignments_today = Assignment.find(:all, :conditions => "employee_id = " +  employee_id.to_s + " and created_at >= '" + date_today.strftime("%Y-%m-%d 00:00:00") + "' and created_at <= '" + date_today.strftime("%Y-%m-%d 23:59:59") + "'").length
    @total_assignment = assignments_today + assignments
    
    lessonplan_today = Lessonplan.find(:all, :conditions => "author_id = " +  user_id.to_s + " and created_at >= '" + date_today.strftime("%Y-%m-%d 00:00:00") + "' and created_at <= '" + date_today.strftime("%Y-%m-%d 23:59:59") + "'").length
    @lessonplan_summary_today = Lessonplan.find(:all, :conditions => "author_id = " +  user_id.to_s + " and created_at >= '" + date_today.strftime("%Y-%m-%d 00:00:00") + "' and created_at <= '" + date_today.strftime("%Y-%m-%d 23:59:59") + "'").length
    lessonplan_publish_today = Lessonplan.find(:all, :conditions => "author_id = " +  user_id.to_s + " and publish_date = '" + date_today.strftime("%Y-%m-%d") + "'").length
    
    @total_lessonplan = lessonplan_today + @lessonplan_summary_today + lessonplan_publish_today
    
    last_week = @local_tzone_time.to_date - 7
    
    assignments_id = Assignment.find(:all, :conditions => "employee_id = " +  employee_id.to_s + " and duedate >= '" + date_today.strftime("%Y-%m-%d 00:00:00") + "' and duedate <= '" + last_week.strftime("%Y-%m-%d 23:59:59") + "'").map(&:id)
    @assignments_submitted = 0
    unless assignments_id.nil? or assignments_id.empty?
      @assignments_submitted = AssignmentAnswer.find(:all, :conditions => "assignment_id IN (" +  assignments_id.join(",") + ")").length
    end
    
    @total_class_length = 0
    unless @tts_all.nil? or @tts_all.empty?
      @completed_class_length = 0
      unless @completed_class_all.nil? or @completed_class_all.empty?
        @completed_class_length = @tts_all.select{ |tt| @completed_class_all.include?(tt.id) and tt.employee_id == employee_id.to_i }.length
      end
      
      @current_class_length = 0
      unless @running_class_all.nil? or @running_class_all.empty?
        @current_class_length = @tts_all.select{ |tt| @running_class_all.include?(tt.id) and tt.employee_id == employee_id.to_i }.length
      end
      
      @next_class_length = 0
      unless @next_class_all.nil? or @next_class_all.empty?
        @next_class_length = @tts_all.select{ |tt| @next_class_all.include?(tt.id) and tt.employee_id == employee_id.to_i }.length
      end
      @total_class_length = @completed_class_length.to_i + @current_class_length.to_i + @next_class_length.to_i
    else
      if @current_user.admin? 
        get_routines_all(employee_id)
        unless @tts_all.nil? or @tts_all.empty?
          @total_class_length = @tts_all.select{ |tt| tt.employee_id == employee_id.to_i }.length
        end
      elsif @current_user.employee? 
        get_routines(employee_id)
        unless @tts.nil? or @tts.empty?
          @total_class_length = @tts.select{ |tt| tt.employee_id == employee_id.to_i }.length
        end
      end
    end
  end
  
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
