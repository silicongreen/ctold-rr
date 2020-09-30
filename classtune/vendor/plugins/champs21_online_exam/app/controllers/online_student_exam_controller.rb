class OnlineStudentExamController < ApplicationController

  before_filter :login_required#,:only_student_allowed,:online_exam_enabled
  before_filter :check_permission, :only=>[:index]
  before_filter :default_time_zone_present_time
  filter_access_to :all
  def index
    @current_user = current_user
    if @current_user.student?
      @student = @current_user.student_record
    end
    
    if @current_user.parent?
      target = @current_user.guardian_entry.current_ward_id      
      @student = Student.find_by_id(target) 
    end
    
    @exams = @student.available_online_exams
    if request.post?
      unless params[:exam][:exam_id].blank?
        @current_exam = OnlineExamGroup.find(params[:exam][:exam_id])
        render :update do |page|
          page.replace_html 'box', :partial=>'details'
        end
      else
        flash[:warn_notice]="#{t('please_select_one_exam')}"
        render :update do |page|
          page.replace_html 'errors', :partial=>'errors'
        end
      end
    end
  end
  
  def exam_result_details
    server_time = Time.now
    server_time_to_gmt = server_time.getgm
    local_tzone_time = server_time
    time_zone = Configuration.find_by_config_key("TimeZone")
    unless time_zone.nil?
      unless time_zone.config_value.nil?
        zone = TimeZone.find(time_zone.config_value)
        if zone.difference_type=="+"
          local_tzone_time = server_time_to_gmt + zone.time_difference
        else
          local_tzone_time = server_time_to_gmt - zone.time_difference
        end
      end
    end

    time_now = local_tzone_time.strftime("%H:%M:%S")
    @exam_attendance = OnlineExamAttendance.find_by_online_exam_group_id_and_student_id(params[:id],@current_user.student_record.id)
    @exam_group = OnlineExamGroup.find_by_id(@exam_attendance.online_exam_group_id,:conditions=>"end_date < '#{local_tzone_time.to_date}'" ,:include => [:subject])
    unless @exam_group.blank?
      @exam_result = OnlineExamScoreDetail.find_all_by_online_exam_attendance_id(@exam_attendance.id)
      @attendance = @exam_group.has_attendence
      @exam_questions = @exam_group.online_exam_questions.all(:include=>[:online_exam_options])
    else
      flash[:notice]="Sorry Exam Day must be over before you can see the exam details"
      redirect_to :controller => 'user', :action => 'dashboard'
    end
  end

  def start_exam
    @student = Student.find_by_user_id(current_user.id,:select=>"id,batch_id")
    @exam = @student.available_online_exams.find_by_id(params[:id].to_i)
    @per_page = 100
    if @exam.present?
      unless @exam.already_attended(@student.id)
        #Reminder.update_all("is_read='1'",  ["rid = ? and rtype = ? and recipient= ?", params[:id], 15,current_user.id])
        @exam_attendance = OnlineExamAttendance.create(:online_exam_group_id=> @exam.id, :student_id=>@student.id, :start_time=>I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S'))
        session[:exam_attendance_id] = @exam_attendance.id
        @exam_questions = Rails.cache.fetch("online_exam_questions_#{params[:id]}"){
          @exam_question_main = @exam.online_exam_questions.paginate(:per_page=>@per_page,:page=>params[:page])
          @exam_question_main
        }
        @num_exam_questions = @exam.online_exam_questions.count
        question_ids = @exam_questions.collect(&:id)
        @options = Rails.cache.fetch("online_exam_options_#{params[:id]}"){
          @option_main = @exam.online_exam_options.all(:conditions=>{:online_exam_question_id=>question_ids}).map {|op| @exam_attendance.online_exam_score_details.build(:online_exam_question_id=>op.online_exam_question_id, :online_exam_option_id=>op.id)}.group_by(&:online_exam_question_id)
          @option_main
        }
      else
        exam_attendance_previous = OnlineExamAttendance.find(:first, :conditions=>{:student_id => @student.id, :online_exam_group_id=>@exam.id})
        if exam_attendance_previous.blank?
          exam_attendance_previous = OnlineExamAttendance.create(:online_exam_group_id=> @exam.id, :student_id=>@student.id, :start_time=>I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S'))
        end
        if exam_attendance_previous.total_score.blank? and exam_attendance_previous.end_time.blank?
          exam_attendance_previous.destroy
          @exam_attendance = OnlineExamAttendance.create(:online_exam_group_id=> @exam.id, :student_id=>@student.id, :start_time=>I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S'))
          session[:exam_attendance_id] = @exam_attendance.id
          @exam_questions = Rails.cache.fetch("online_exam_questions_#{params[:id]}"){
            @exam_question_main = @exam.online_exam_questions.paginate(:per_page=>@per_page,:page=>params[:page])
            @exam_question_main
          }
          @num_exam_questions = @exam.online_exam_questions.count
          question_ids = @exam_questions.collect(&:id)
          @options = Rails.cache.fetch("online_exam_options_#{params[:id]}"){
            @option_main = @exam.online_exam_options.all(:conditions=>{:online_exam_question_id=>question_ids}).map {|op| @exam_attendance.online_exam_score_details.build(:online_exam_question_id=>op.online_exam_question_id, :online_exam_option_id=>op.id)}.group_by(&:online_exam_question_id)
            @option_main
          }
        else
          render :partial => 'already_attended' and return
        end
      end
      render :layout => false
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller => 'user', :action => 'dashboard'
    end
  end
  
  def started_exam
    @exam = OnlineExamGroup.find(params[:id])
    @exam_attendance = OnlineExamAttendance.find(params[:attendance_id])
    @per_page = 20
    render :partial => 'late_submit' and return if @exam_attendance.start_time+@exam_attendance.online_exam_group.maximum_time.minutes+6.minutes < @local_tzone_time.to_time
    @exam_questions = @exam.online_exam_questions.paginate(:per_page=>@per_page,:page=>params[:page])
    @num_exam_questions = @exam.online_exam_questions.count
    question_ids = @exam_questions.collect(&:id)
    @selected_options = @exam_attendance.online_exam_score_details.all(:select=>"id,online_exam_option_id",:conditions=>{:online_exam_question_id=>question_ids,:online_exam_attendance_id=>@exam_attendance.id}).group_by(&:online_exam_option_id)
    @options = @exam.online_exam_options.all(:conditions=>{:online_exam_question_id=>question_ids}).map {|op| @exam_attendance.online_exam_score_details.build(:online_exam_question_id=>op.online_exam_question_id, :online_exam_option_id=>op.id)}.group_by(&:online_exam_question_id)
    render :update do |page|
      page.replace_html 'questions', :partial=>'exam_questions'
    end
  end
  def save_history
    @exam_attendance = OnlineExamAttendance.find(params[:attendance_id])
    if @exam_attendance.blank?
      exam_id = params[:exam_id]
      if session[:exam_attendance_id]
        att_id = session[:exam_attendance_id]
        @exam_attendance = OnlineExamAttendance.find_by_id(att_id)
      elsif !exam_id.blank?
        @exam = OnlineExamGroup.find_by_id(params[:exam_id])
        @student = Student.find_by_user_id(current_user.id,:select=>"id,batch_id")
        @exam_attendance = OnlineExamAttendance.find(:first, :conditions=>{:student_id => @student.id, :online_exam_group_id=>@exam.id})  
      end
    end
    OnlineExamScoreHistoryDetail.destroy_all(:online_exam_attendance_id => @exam_attendance.id)
    @exam_attendance.update_attributes(:online_exam_score_history_details_attributes=>params[:online_exam_attendance][:online_exam_score_details_attributes])
    render :nothing=>true
  end

  def save_scores
    @exam_attendance = OnlineExamAttendance.find(params[:attendance_id])
    render :partial => 'late_submit' and return if @exam_attendance.start_time+@exam_attendance.online_exam_group.maximum_time.minutes+6.minutes < Time.now
    @exam_attendance.update_attributes(:online_exam_score_details_attributes=>params[:online_exam_attendance][:online_exam_score_details_attributes])
    render :nothing=>true
  end

  def save_exam
    @exam_attendance = OnlineExamAttendance.find_by_id(params[:attendance_id])
    if @exam_attendance.blank?
      exam_id = params[:exam_id]
      if session[:exam_attendance_id]
        att_id = session[:exam_attendance_id]
        @exam_attendance = OnlineExamAttendance.find_by_id(att_id)
      elsif !exam_id.blank?
        @exam = OnlineExamGroup.find_by_id(params[:exam_id])
        @student = Student.find_by_user_id(current_user.id,:select=>"id,batch_id")
        @exam_attendance = OnlineExamAttendance.find(:first, :conditions=>{:student_id => @student.id, :online_exam_group_id=>@exam.id})
        if @exam_attendance.blank?
          @exam_attendance = OnlineExamAttendance.create(:online_exam_group_id=> @exam.id, :student_id=>@student.id, :start_time=>I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S'))
        end   
      end
    end
    
    
    session[:exam_attendance_id] = nil if session[:exam_attendance_id]
    if @exam_attendance.blank? or !@exam_attendance.end_time.blank?
      render :partial => 'already_attended' and return
    end
    exam_attendance_all_count = OnlineExamAttendance.count(:conditions=>{:student_id => @student.id, :online_exam_group_id=>@exam_attendance.online_exam_group_id})
    if exam_attendance_all_count > 1
      render :partial => 'already_attended' and return
    end
    
    render :partial => 'late_submit' and return if @exam_attendance.start_time+@exam_attendance.online_exam_group.maximum_time.minutes+6.minutes < Time.now
    @exam_attendance.update_attributes(:online_exam_score_details_attributes=>params[:online_exam_attendance][:online_exam_score_details_attributes])
    @exam_attendance.reload
    @total_score = @exam_attendance.online_exam_group.online_exam_questions.sum('mark')
    score = @exam_attendance.student_score
    pass_mark = (@total_score*@exam_attendance.online_exam_group.pass_percentage.to_f)/100
    score >= pass_mark ? passed = true : passed = false
    if @exam_attendance.update_attributes(:total_score=>score, :is_passed=>passed, :end_time=>local_time_zone)
      flash.now[:notice]="#{t('you_have_successfully_completed_the_exam')}"
    end
    render :layout => false and return
  end

  def exam_list
    quizes = {}
    quize_response = get_quiz_data
    if quize_response['status']['code'].to_i == 200
      quizes['total'] = quize_response['data']['total']
      quizes['has_next'] = quize_response['data']['has_next']
      quizes['data'] = quize_response['data']['homework']
    end
    @current_user = current_user
    render :partial=>"quize", :locals=>{:quizes=>quizes}
  end

end

private

def local_time_zone
  server_time = Time.now
  server_time_to_gmt = server_time.getgm
  local_tzone_time = server_time
  time_zone = Configuration.find_by_config_key("TimeZone")
  unless time_zone.nil?
    unless time_zone.config_value.nil?
      zone = TimeZone.find(time_zone.config_value)
      if zone.difference_type=="+"
        local_tzone_time = server_time_to_gmt + zone.time_difference
      else
        local_tzone_time = server_time_to_gmt - zone.time_difference
      end
    end
  end
  return local_tzone_time
end

def get_quiz_data
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
  
  form_data = {}
  form_data['user_secret'] = session[:api_info][0]['user_secret']
    
  homework_uri = URI(api_endpoint + "api/homework/assessment")
  http = Net::HTTP.new(homework_uri.host, homework_uri.port)
  homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    
  if current_user.parent
    target = current_user.guardian_entry.current_ward_id      
    student = Student.find_by_id(target)
      
    form_data['batch_id'] = student.batch_id
    form_data['student_id'] = student.id
  end
  form_data['not_started'] = 1
  form_data['call_from_web'] = 1
    
  homework_req.set_form_data(form_data)
  homework_res = http.request(homework_req)

  return JSON::parse(homework_res.body)
end