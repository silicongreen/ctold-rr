class ClassworkAnswersController < ApplicationController
  before_filter :login_required
  filter_access_to :all

  def new
    @classwork= Classwork.active.find_by_id(params[:classwork_id])
    unless @classwork.nil?
      if @classwork.subject.batch_id==current_user.student_record.batch_id
        @classwork_answer = @classwork.classwork_answers.find_by_student_id(current_user.student_record.id)
        unless @classwork_answer.present?
          @classwork_answer = @classwork.classwork_answers.build
        else
          flash[:notice] = "#{t('already_answered')}"
          redirect_to classworks_path
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
    @aid = params[:classwork_id]
    @classwork= Classwork.active.find_by_id(params[:classwork_id])
    unless @classwork.nil?
      @classwork_answer = @classwork.classwork_answers.build(params[:classwork_answer])
      @classwork_answer.student_id = current_user.student_record.id
      if @classwork.classwork_type != 2
        @classwork_answer.title = "Done"
        @classwork_answer.status = "ACCEPTED"
        @classwork_answer.content = "classwork Submitted"
      end
      if @classwork_answer.save
        if @classwork.classwork_type != 2
          render :partial => 'classwork_done'
        else
          flash[:notice] = "#{t('classwork_submitted')}"
          redirect_to classworks_path
        end
      else
        render :action=>:new
      end
    else
      flash[:notice]=t('flash_msg4')
      render :partial => 'classwork_done'
    end
  end

  def create
    @classwork= Classwork.active.find_by_id(params[:classwork_id])
    unless @classwork.nil?
      @classwork_answer = @classwork.classwork_answers.build(params[:classwork_answer])
      @classwork_answer.student_id = current_user.student_record.id
      if @classwork.classwork_type != 2
        @classwork_answer.title = "Done"
        @classwork_answer.content = "classwork Submitted Please Check"
      end
      if @classwork_answer.save
        if @classwork.classwork_type != 2
          render :partial => 'classwork_answer'
        else
          flash[:notice] = "#{t('classwork_submitted')}"
          redirect_to classworks_path
        end
      else
        render :action=>:new
      end
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end

  def show
    @classwork= Classwork.active.find params[:classwork_id]
    @classwork_answer= ClassworkAnswer.find_by_id(params[:id])
    if @classwork_answer.present?
      unless (@classwork.download_allowed_for(current_user))
        flash[:notice] = "#{t('you_are_not_allowed_to_view_that_page')}"
        redirect_to classworks_path
      end
    else
      flash[:notice] = "#{t('you_are_not_allowed_to_view_that_page')}"
      redirect_to classworks_path
    end
  end

  def edit
    @classwork_answer= ClassworkAnswer.find params[:id]
    @classwork=@classwork_answer.classwork
    if @classwork_answer.download_allowed_for(current_user)
      unless @classwork_answer.status == "REJECTED"
        flash[:notice] ="#{t('you_cannot_edit_this_classwork')}"
        redirect_to classworks_path
      end
    else
      flash[:notice] ="#{t('you_cannot_edit_this_classwork')}"
      redirect_to classworks_path
    end
  end
  def update
    @classwork = Classwork.active.find params[:classwork_id]
    unless @classwork.nil?
      @classwork_answer= ClassworkAnswer.find params[:id]
      @classwork_answer.status = "0"
      if @classwork_answer.update_attributes(params[:classwork_answer])
        flash[:notice] = "#{t('classwork_successfuly_updated')}"
        redirect_to classwork_classwork_answer_path @classwork,@classwork_answer
      else
        flash[:notice] = "#{t('failed_to_update_classwork')}"
        render :action=>:edit
      end
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end

  def evaluate_classwork
    #Change the status of classwork Answer submitted
    @classwork_answer=ClassworkAnswer.find params[:id]
    if current_user.employee?
      if  current_user.employee_record.id == @classwork_answer.classwork.employee_id
        @classwork_answer.status = params[:status]
        if @classwork_answer.save
          flash[:notice] = "#{t('classwork_text')}" + " #{@classwork_answer.status.capitalize}"
        else
          flash[:notice] = "#{t('failed_to_set_status_of_classwork')}"
        end
      end
    else
      flash[:notice] = "#{t('you_cannot_approve_or_reject_this_classwork')}"
    end
    redirect_to classwork_classwork_answer_path(@classwork_answer.classwork,@classwork_answer)
  end
  def download_attachment
    #Method for downloading the attachment
    @classwork_answer =ClassworkAnswer.find params[:classwork_answer]
    if @classwork_answer.download_allowed_for(current_user)
      send_file  @classwork_answer.attachment.path, :type=>@classwork_answer.attachment.content_type
    else
      flash[:notice] = "#{t('you_are_not_allowed_to_download_that_file')}"
      redirect_to :controller=>:classworks
    end
  end
end
