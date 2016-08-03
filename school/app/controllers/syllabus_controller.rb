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

class SyllabusController < ApplicationController
  before_filter :login_required
  filter_access_to :all

  def add
    @batches = Batch.active
    @syllabus = Syllabus.new(params[:syllabus])
    @syllabus.author = current_user
    if @syllabus.exam_group_id.nil?
      @syllabus.exam_group_id = 0
    end
    if request.post? and @syllabus.save      
      flash[:notice] = "#{t('flash1')}"
      redirect_to :controller => 'syllabus', :action => 'view', :id => @syllabus.id
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
    @syllabus = Syllabus.paginate :page => params[:page]
  end

  def delete
    @syllabus = News.find(params[:id]).destroy
    flash[:notice] = "#{t('flash2')}"
    redirect_to :controller => 'syllabus', :action => 'index'
  end

  def delete_comment
    @comment = NewsComment.find(params[:id])
    news_id = @comment.news_id
    @comment.destroy
    show_comments_associate(news_id)
  end

  def edit
    @syllabus = Syllabus.find(params[:id])
    @batches = Batch.active
    @exam_groups = ExamGroup.find_all_by_batch_id(@syllabus.batch_id)
    @subjects = Subject.find_all_by_batch_id(@syllabus.batch_id,:conditions=>"is_deleted=false AND no_exams=false")
    @syllabus.author = current_user
    if @syllabus.exam_group_id.nil?
      @syllabus.exam_group_id = 0
    end
    if request.post? and @syllabus.update_attributes(params[:syllabus])
      flash[:notice] = "#{t('flash3')}"
      redirect_to :controller => 'syllabus', :action => 'view', :id => @syllabus.id
    end
  end

  def index
    @batches = Batch.active
    @current_user = current_user
    @syllabus = []
    if request.get?
      @syllabus = Syllabus.title_like_all params[:query].split unless params[:query].nil?
    end
  end

  def search_news_ajax
    @news = nil
    conditions = ["title LIKE ?", "%#{params[:query]}%"]
    @news = News.find(:all, :conditions => conditions) unless params[:query] == ''
    render :layout => false
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
  def show
    if params[:batch_id] == ''
      @subjects = []
    else
      @batch = Batch.find params[:batch_id]
      #@subjects = @batch.normal_batch_subject
      #@elective_groups = ElectiveGroup.find_all_by_batch_id(params[:batch_id], :conditions =>{:is_deleted=>false})
      #@exam_group = ExamGroup.find(params[:batch_id])
      @exam_groups = ExamGroup.find_all_by_batch_id(params[:batch_id])
      @subjects = Subject.find_all_by_batch_id(params[:batch_id],:conditions=>"is_deleted=false AND no_exams=false")
    end
    
    #puts @elective_groups.to_yaml
    #abort("90")
    respond_to do |format|
      format.js { render :action => 'show' }
    end
  end
  def showall
    if params[:batch_id] == ''
      @subjects = []
    else
      @batch = Batch.find params[:batch_id]
      @syllabus = Syllabus.find_all_by_batch_id(params[:batch_id])      
    end
    respond_to do |format|
      format.js { render :action => 'showall' }
    end
  end
  private

  def show_comments_associate(news_id, params_page=nil)
    @syllabus = Syllabus.find(news_id, :include=>[:author])
  end

end
