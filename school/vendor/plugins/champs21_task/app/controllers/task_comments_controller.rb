class TaskCommentsController < ApplicationController
  before_filter :login_required
  filter_access_to :all 

  def download_attachment
    @comment = TaskComment.find(params[:id])
    if @comment.can_be_downloaded_by?(current_user)
      send_file (@comment.attachment.path)
    else
      flash[:notice] = "#{t('no_permission_to_download_file')}"
      redirect_to tasks_path
    end
  end

  def create  
    @cmnt = TaskComment.new(params[:cmnt])
    @cmnt.user = current_user
    @task = @cmnt.task
    if @cmnt.save
      flash[:notice]="#{t('update_creation_successful')}"
      redirect_to task_path(:id=>@task)
    else
      @comments = @task.task_comments
      render 'tasks/show'
    end
  end

  def destroy
    @user = current_user
    @comment = TaskComment.find(params[:id])
    if @comment.can_be_deleted_by?(@user)
      TaskComment.destroy(params[:id])
      flash[:notice] = "#{t('task_comment_deleted_successfully')}"
    end
  end

end
