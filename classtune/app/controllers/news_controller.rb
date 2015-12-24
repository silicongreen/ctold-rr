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

class NewsController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only => [:index]
  filter_access_to :all

  def add
    @news = News.new(params[:news])
    @news.author = current_user
    if request.post? and @news.save
      sms_setting = SmsSetting.new()
      if sms_setting.application_sms_active
        students = Student.find(:all,:select=>'phone2',:conditions=>'is_sms_enabled = true')
      end
      users = User.find(:all)
      available_user_ids = users.collect(&:id).compact
      Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
          :recipient_ids => available_user_ids,
          :subject=>"#{t('reminder_notice')}",
          :rtype=>5,
          :rid=>@news.id,
          :body=>"#{t('reminder_notice')} : "+params[:news][:title] ))
      flash[:notice] = "#{t('flash1')}"
      redirect_to :controller => 'news', :action => 'view', :id => @news.id
    end
  end
  def download_attachment
    #download the  attached file
    @news = News.find params[:id]
    filename = @news.attachment_file_name
    unless @news.nil?
      if @news.download_allowed_for(current_user)
        send_file  @news.attachment.path , :type=>@news.attachment.content_type, :filename => filename
      else
        flash[:notice] = "#{t('you_are_not_allowed_to_download_that_file')}"
        redirect_to :controller=>:news
      end
    else
      flash[:notice]=t('flash_msg4')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end

  def add_comment
    @cmnt = NewsComment.new(params[:comment])
    @current_user = @cmnt.author = current_user
    @cmnt.is_approved =true if @current_user.privileges.include?(Privilege.find_by_name('ManageNews')) || @current_user.admin?
    @cmnt.save
    show_comments_associate(@cmnt.news.id)
  end

  def all
    @news = News.paginate( :page => params[:page], :per_page => 10)
  end

  def delete
    @news = News.find(params[:id]).destroy
    flash[:notice] = "#{t('flash2')}"
    redirect_to :controller => 'news', :action => 'all'
  end

  def delete_comment
    @comment = NewsComment.find(params[:id])
    news_id = @comment.news_id
    @comment.destroy
    show_comments_associate(news_id)
  end

  def edit
    @news = News.find(params[:id])
    if request.post? and @news.update_attributes(params[:news])
      flash[:notice] = "#{t('flash3')}"
      redirect_to :controller => 'news', :action => 'view', :id => @news.id
    end
  end

  def index
    @current_user = current_user
    @news = []
    if request.get?
      @news = News.title_like_all params[:query].split unless params[:query].nil?
    end
  end

  def search_news_ajax
    @news = nil
    
    unless params[:query].nil? and params[:query] == ''
      conditions = ["title LIKE ?", "%#{params[:query]}%"]
      @news = News.paginate(:conditions => conditions, :page => params[:page], :per_page => 10)
    else
      @news = News.paginate(:page => params[:page], :per_page => 10)
    end
    render :partial=>"news_list"
  end

  def view
    show_comments_associate(params[:id], params[:page])
  end

  def comment_view
    show_comments_associate(params[:id], params[:page])
    render :update do |page|
      page.replace_html 'comments-list', :partial=>"comment"
    end
  end

  def comment_approved
    @comment = NewsComment.find(params[:id])
    status=@comment.is_approved ? false : true
    @comment.update_attributes(:is_approved=>status)
    render :update do |page|
      page.reload
    end
  end

  private

  def show_comments_associate(news_id, params_page=nil)
    @news = News.find(news_id, :include=>[:author])
    @comments = @news.comments.latest.paginate(:page => params_page, :per_page => 15, :include =>[:author])
    @current_user = current_user
    @is_moderator = @current_user.admin? || @current_user.privileges.include?(Privilege.find_by_name('ManageNews'))
    @config = Configuration.find_by_config_key('EnableNewsCommentModeration')
    @permitted_to_delete_comment_news = permitted_to? :delete_comment , :news
  end

end
