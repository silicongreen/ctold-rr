class OnlineStudentExamController < ApplicationController

  before_filter :login_required#,:only_student_allowed,:online_exam_enabled
  filter_access_to :all
  def index
    @student = current_user.student_record
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

  def start_exam
    @student = Student.find_by_user_id(current_user.id,:select=>"id,batch_id")
    @exam = @student.available_online_exams.find_by_id(params[:id].to_i)
    if @exam.present?
      unless @exam.already_attended(@student.id)
        @exam_attendance = OnlineExamAttendance.create(:online_exam_group_id=> @exam.id, :student_id=>@student.id, :start_time=>Time.now)
        @exam_questions=@exam.online_exam_questions.paginate(:per_page=>5,:page=>params[:page])
        question_ids=@exam_questions.collect(&:id)
        @options=@exam.online_exam_options.all(:conditions=>{:online_exam_question_id=>question_ids}).map {|op| @exam_attendance.online_exam_score_details.build(:online_exam_question_id=>op.online_exam_question_id, :online_exam_option_id=>op.id)}.group_by(&:online_exam_question_id)
      else
        render :partial => 'already_attended' and return
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
    render :partial => 'late_submit' and return if @exam_attendance.start_time+@exam_attendance.online_exam_group.maximum_time.minutes+2.minutes < Time.now
    @exam_questions=@exam.online_exam_questions.paginate(:per_page=>5,:page=>params[:page])
    question_ids=@exam_questions.collect(&:id)
    @selected_options=@exam_attendance.online_exam_score_details.all(:select=>"id,online_exam_option_id",:conditions=>{:online_exam_question_id=>question_ids,:online_exam_attendance_id=>@exam_attendance.id}).group_by(&:online_exam_option_id)
    @options=@exam.online_exam_options.all(:conditions=>{:online_exam_question_id=>question_ids}).map {|op| @exam_attendance.online_exam_score_details.build(:online_exam_question_id=>op.online_exam_question_id, :online_exam_option_id=>op.id)}.group_by(&:online_exam_question_id)
    render :update do |page|
      page.replace_html 'questions', :partial=>'exam_questions'
    end
  end

  def save_scores
    @exam_attendance = OnlineExamAttendance.find(params[:attendance_id])
    render :partial => 'late_submit' and return if @exam_attendance.start_time+@exam_attendance.online_exam_group.maximum_time.minutes+2.minutes < Time.now
    @exam_attendance.update_attributes(:online_exam_score_details_attributes=>params[:online_exam_attendance][:online_exam_score_details_attributes])
    render :nothing=>true
  end

  def save_exam
    @exam_attendance = OnlineExamAttendance.find(params[:attendance_id])
    render :partial => 'late_submit' and return if @exam_attendance.start_time+@exam_attendance.online_exam_group.maximum_time.minutes+2.minutes < Time.now
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