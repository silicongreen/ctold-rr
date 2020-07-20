class AssignmentAnswersController < ApplicationController
  before_filter :login_required
  filter_access_to :all

  def new
    @assignment= Assignment.active.find_by_id(params[:assignment_id])
    unless @assignment.nil?
      if @assignment.subject.batch_id==current_user.student_record.batch_id
        @assignment_answer = @assignment.assignment_answers.find_by_student_id(current_user.student_record.id)
        unless @assignment_answer.present?
          @assignment_answer = @assignment.assignment_answers.build
        else
          flash[:notice] = "#{t('already_answered')}"
          redirect_to assignments_path
        end
      else
        flash[:notice]="#{t('flash_msg4')}"
        redirect_to :controller => 'user',:action => 'dashboard'
      end
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end
  
  def done
    @aid = params[:assignment_id]
    @assignment= Assignment.active.find_by_id(params[:assignment_id])
    unless @assignment.nil?
      @assignment_answer = @assignment.assignment_answers.build(params[:assignment_answer])
      @assignment_answer.student_id = current_user.student_record.id
      if @assignment.assignment_type != 2
        @assignment_answer.title = "Done"
        @assignment_answer.status = "ACCEPTED"
        @assignment_answer.content = "Homework Submitted"
      end
      if @assignment_answer.save
        if @assignment.assignment_type != 2
          render :partial => 'assignment_done'
        else
          flash[:notice] = "#{t('assignment_submitted')}"
          redirect_to assignments_path
        end
      else
        render :action=>:new
      end
    else
      flash[:notice]=t('flash_msg4')
      render :partial => 'assignment_done'
    end
  end

  def create
    @assignment= Assignment.active.find_by_id(params[:assignment_id])
    unless @assignment.nil?
      @assignment_answer = @assignment.assignment_answers.build(params[:assignment_answer])
      @assignment_answer.student_id = current_user.student_record.id
      @assignment_answer.from_web = 1
      if @assignment.assignment_type != 2
        @assignment_answer.title = "Done"
        @assignment_answer.content = "Homework Submitted Please Check"
      end
      if @assignment_answer.save
        if @assignment.assignment_type != 2
          render :partial => 'assignment_answer'
        else
          flash[:notice] = "#{t('assignment_submitted')}"
          user_ass = []
          student_ids = []
          batch_ids = []
          student_ids << 0
          batch_ids << 0
          user_ass << @assignment.employee.user_id
          Delayed::Job.enqueue(
            DelayedReminderJob.new( :sender_id  => current_user.id,
              :recipient_ids => user_ass,
              :subject=>"New Assignment Submitted By Student",
              :rtype=>4,
              :rid=>@assignment.id,
              :student_id => student_ids,
              :batch_id => batch_ids,
              :body=>"New Assignment Submitted By Student ("+current_user.student_record.full_name+")")
          )
          redirect_to assignments_path
        end
      else
        render :action=>:new
      end
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end
  
  def message
    @assignment= Assignment.active.find params[:assignment_id]
    @assignment_id = params[:assignment_id]
    @student_id = params[:student_id]
    show_comments_associate(@assignment_id, @student_id)
  end

  def show
    @assignment= Assignment.active.find params[:assignment_id]
    @assignment_answer= AssignmentAnswer.find_by_id(params[:id])
    if @assignment_answer.present?
      @student_id = @assignment_answer.student_id
      @assignment_id = @assignment_answer.assignment_id
      show_comments_associate(@assignment_id, @student_id)
      unless (@assignment.download_allowed_for(current_user))
        flash[:notice] = "#{t('you_are_not_allowed_to_view_that_page')}"
        redirect_to assignments_path
      end
    else
      flash[:notice] = "#{t('you_are_not_allowed_to_view_that_page')}"
      redirect_to assignments_path
    end
  end

  def edit
    @assignment_answer= AssignmentAnswer.find params[:id]
    @assignment=@assignment_answer.assignment
    if @assignment_answer.download_allowed_for(current_user)
      unless @assignment_answer.status == "REJECTED"
        flash[:notice] ="#{t('you_cannot_edit_this_assignment')}"
        redirect_to assignments_path
      end
    else
      flash[:notice] ="#{t('you_cannot_edit_this_assignment')}"
      redirect_to assignments_path
    end
  end
  def update
    @assignment = Assignment.active.find params[:assignment_id]
    unless @assignment.nil?
      @assignment_answer= AssignmentAnswer.find params[:id]
      @assignment_answer.status = "0"
      if @assignment_answer.update_attributes(params[:assignment_answer])
        flash[:notice] = "#{t('assignment_successfuly_updated')}"
        redirect_to assignment_assignment_answer_path @assignment,@assignment_answer
      else
        flash[:notice] = "#{t('failed_to_update_assignment')}"
        render :action=>:edit
      end
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end

  def evaluate_assignment
    #Change the status of assignment Answer submitted
    @assignment_answer=AssignmentAnswer.find params[:id]
    if current_user.employee? or current_user.admin?
      if  current_user.employee_record.id == @assignment_answer.assignment.employee_id or current_user.admin?
        if params[:status] == "DELETED"
          @assignment=@assignment_answer.assignment
          @assignment_answer.destroy
          flash[:notice] = "Answer successfully removed"
          redirect_to assignments_path
        else
          @assignment_answer.status = params[:status]
          if @assignment_answer.save
            flash[:notice] = "#{t('assignment_text')}" + " #{@assignment_answer.status.capitalize}"
          else
            flash[:notice] = "#{t('failed_to_set_status_of_assignment')}"
          end
          redirect_to assignment_assignment_answer_path(@assignment_answer.assignment,@assignment_answer)
        end
      end
    else
      flash[:notice] = "#{t('you_cannot_approve_or_reject_this_assignment')}"
      redirect_to assignment_assignment_answer_path(@assignment_answer.assignment,@assignment_answer)
    end
    
  end
  def download_attachment
    #Method for downloading the attachment
    @number = params[:number]
    @assignment_answer =AssignmentAnswer.find params[:assignment_answer]
    if @assignment_answer.download_allowed_for(current_user)
      unless params[:check_file].blank?
        abort(@assignment_answer.attachment.path)
      end
      if @number.blank? or @number.to_i == 1
        send_file  @assignment_answer.attachment.path, :type=>@assignment_answer.attachment.content_type
      elsif @number.to_i == 2
        send_file  @assignment_answer.attachment2.path, :type=>@assignment_answer.attachment2.content_type
      elsif @number.to_i == 3
        send_file  @assignment_answer.attachment3.path, :type=>@assignment_answer.attachment3.content_type
      end 
      
    else
      flash[:notice] = "#{t('you_are_not_allowed_to_download_that_file')}"
      redirect_to :controller=>:assignments
    end
  end
  def comment_view
    @student_id = params[:student_id]
    @assignment_id = params[:assignment_id]
    show_comments_associate(@assignment_id, @student_id)
    render :update do |page|
      page.replace_html 'comments-list', :partial=>"comment"
    end
  end
  def add_comment
    @student_id = params[:comment][:student_id]
    @assignment_id = params[:comment][:assignment_id]
    @cmnt = AssignmentComment.new(params[:comment])
    @current_user = @cmnt.author = current_user
    @cmnt.created_at = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    @cmnt.save
    show_comments_associate(@assignment_id, @student_id)
    render :update do |page|
      page.replace_html 'comments-list', :partial=>"comment"
    end
  end
  def delete_comment
    @comment = AssignmentComment.find(params[:id])
    @student_id = @comment.student_id
    @assignment_id = @comment.assignment_id
    @comment.destroy
    show_comments_associate(@assignment_id, @student_id)
    render :update do |page|
      page.replace_html 'comments-list', :partial=>"comment"
    end
  end
  
  private

  def show_comments_associate( assignment_id, student_id )
    @comments = AssignmentComment.find(:all,:conditions=>['student_id = ? and assignment_id = ?',student_id,assignment_id], :include =>[:author])
    @current_user = current_user
  end
end
