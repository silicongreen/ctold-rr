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
  before_filter :check_permission, :only=>[:index]
  before_filter :default_time_zone_present_time
  filter_access_to :all

  def classes_view
    @batches = Batch.active.find(:all, :group => "name")
    if @batches.length == 1
        @batch = @batches[0]
        batch_name = @batch.name
        school_id = MultiSchool.current_school.id
        @courses = Rails.cache.fetch("course_data_#{batch_name.parameterize("_")}_#{school_id}"){
          @batches_data = Batch.find(:all, :conditions => ["name = ?", batch_name], :select => "course_id")
          @batch_ids = @batches_data.map{|b| b.course_id}
          @tmp_courses = Course.find(:all, :conditions => ["courses.id IN (?) and courses.is_deleted = 0 and batches.name = ?", @batch_ids, batch_name], :select => "courses.*,  GROUP_CONCAT(courses.section_name,'-',courses.id,'-',batches.id) as courses_batches", :joins=> "INNER JOIN `batches` ON batches.course_id = courses.id", :group => 'course_name', :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
          @tmp_courses 
        }
    end
  end
  
  def syllabus_view
    terms = get_terms(3)
    @data = {}
    if terms['status']['code'].to_i == 200
      @data = terms['data']
    end
  end
  
  def syllabus_by_term
    
    terms = {}
    view = 'term'
    unless params[:id].nil?
      terms = get_terms(params[:id])
    end
    
    unless params[:term_id].nil?
      view = 'syllabus'
      terms = get_syllabus(params[:term_id])
    end
    
    @data = {}
    if terms['status']['code'].to_i == 200
      @data = terms['data']
    end
    
    render :partial => view + '_list'
    
  end
  
  def single_syllabus
    show_comments_associate(params[:id], params[:page])
    render :partial => 'single_syllabus'
  end
  
  def show_syllabus
    @is_class = false
    unless params[:class].nil? 
      @is_class = params[:class]
    end 
    unless params[:batch_name].nil? 
      @batch_name = params[:batch_name]
    end 
    
    if params[:batch_id] == ''
      @subjects = []
    else
      @batch = Batch.find params[:batch_id]
      @course = @batch.course 
      @syllabus = Syllabus.find_all_by_batch_id(params[:batch_id])      
    end
        
    if @is_class and @batch_name      
      @course = @batch.course      
      @course_name = @course.course_name      
      batch_name_tmp = @batch_name
      @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
    end
    
    this_id = params[:batch_id]
    unless @batches.nil? or @batches.empty?
      @batches.each do |batch_id|
        if batch_id != this_id                    
          @tmp_syllabus_groups = Syllabus.find_all_by_batch_id(batch_id)
          @syllabus = @syllabus + @tmp_syllabus_groups
        end
      end      
    end
    
    @tmp_syllabus = @syllabus
    e_name = []    
    @t_syllabus = []
    @x_syllabus = []
    @tmp_syllabus.each do |t|
      if !e_name.include?(t.title+"_"+t.exam_group_id.to_s)
        @t_syllabus << t
        e_name << t.title+"_"+t.exam_group_id.to_s
      else
        @x_syllabus << t
      end   
    end    

    @syllabus = @t_syllabus
    
  end
  
  def add
    @batches = Batch.active
    @syllabus = Syllabus.new(params[:syllabus])
    @syllabus.author = current_user
    if @syllabus.exam_group_id.nil?
      @syllabus.exam_group_id = 0
    end
    abort(params[:syllabus].inspect)
    if request.post? and @syllabus.save      
      flash[:notice] = "#{t('flash1')}"
      redirect_to :controller => 'syllabus', :action => 'view', :id => @syllabus.id
    end
  end
  
  def new
    @is_class = false
    unless params[:class].nil? 
      @is_class = params[:class]
    end 
    unless params[:batch_name].nil? 
      @batch_name = params[:batch_name]
    end  
    
    if request.post?         
      if params[:syllabus][:title]=="" or params[:syllabus][:content]==""      
        if @is_class          
          flash[:notice] = "Please fill up the required field." 
          redirect_to :controller => 'syllabus', :action => 'new', :batch_id => params[:batch_id], :batch_name => params[:batch_name],:class => true,:course_name => params[:course_name]
        else
          flash[:notice] = "Please fill up the required field." 
          redirect_to :controller => 'syllabus', :action => 'new', :batch_id => params[:batch_id], :batch_name => params[:batch_name]        
        end
      end  
      
      if params[:batch].count > 1
        unless params[:batch].nil?    
          i = 0          
          params[:batch].each do |bid|
            if i == 1
              @a = @syllabus.id
            end           
            @syllabus = Syllabus.new(params[:syllabus])  
            @syllabus.batch_id =  bid
            
            if params[:syllabus][:subject_id].to_i == 0
              @syllabus.subject_id = 0
            end
            
            if params[:syllabus][:is_yearly].to_i == 1
              @syllabus.exam_group_id = 0
            else  
              exg = ExamGroup.active.find_by_id(params[:syllabus][:exam_group_id]) 
              exgb = ExamGroup.active.find_all_by_batch_id(bid)
              unless exg.nil?
                unless exgb.nil?
                  exgb.each do |exdata|
                    if exdata.name == exg.name
                      @syllabus.exam_group_id = exdata.id
                    end
                  end
                end
              else
                @syllabus.exam_group_id = 0
              end
              
            end
            
            
            @syllabus.author_id = current_user.id      
            if i > 0
              @syllabus.related_syllabus_id = @a
            else
              @syllabus.related_syllabus_id = 0
            end            
            @syllabus.save 
            i += 1
          end
        end        
        if @syllabus.id
          flash[:notice] = "#{t('flash1')}"          
          redirect_to :controller => 'syllabus', :action => 'view', :id => @a
        end
      else         
        @syllabus = Syllabus.new(params[:syllabus])  
        unless params[:batch].nil?          
          @syllabus.batch_id =  params[:batch][0]
        else          
          flash[:notice] = "Batch is Empty. Please select a Batch"
        end
        
        if params[:syllabus][:subject_id].to_i == 0
          @syllabus.subject_id = 0
        end

        if params[:syllabus][:is_yearly].to_i == 1
          @syllabus.exam_group_id = 0
        end
        
        if params[:syllabus][:exam_group_id].to_i == 0
          @syllabus.exam_group_id = 0
        end
        
        @syllabus.author_id = current_user.id
        @syllabus.related_syllabus_id = 0
        
        if @syllabus.save      
          flash[:notice] = "#{t('flash1')}"          
          redirect_to :controller => 'syllabus', :action => 'view', :id => @syllabus.id
        else
          flash[:notice] = "Hello Sir."
        end
      end
      
    else
      if params[:batch_id] == ''
        @subjects = []
        @exam_groups = []
      else        
        @batch = Batch.find params[:batch_id]
        @exam_groups = ExamGroup.active.find_all_by_batch_id(params[:batch_id])
        @subjects = Subject.find_all_by_batch_id(params[:batch_id],:conditions=>"is_deleted=false AND no_exams=false")
      end

      if @is_class and @batch_name      
        @course = @batch.course      
        @course_name = @course.course_name      
        batch_name_tmp = @batch_name
        @batches = @course.find_batches_data(batch_name_tmp, @course.course_name);
      end

      @ar_bathces = []    
      this_id = @batch.id
      @ar_bathces << this_id
      unless @batches.nil? or @batches.empty?
        @batches.each do |batch_id|
          if batch_id != this_id
            @ar_bathces << batch_id
            @tmp_exam_groups = ExamGroup.active.find_all_by_batch_id(batch_id)          
            @exam_groups = @exam_groups + @tmp_exam_groups

            #@tmp_subjects = Subject.find_all_by_batch_id(batch_id,:conditions=>"is_deleted=false AND no_exams=false")
            #@subjects = @subjects + @tmp_subjects
          end
        end
      end
    
      @tmp_exam = @exam_groups
      
      e_name = []    
      @t_exam = []
      @x_exam = []
      @tmp_exam.each do |t|
        if !e_name.include?(t.name)
          @t_exam << t
          e_name << t.name
        else
          @x_exam << t
        end   
      end    
      
      #@exam_groups = @t_exam
      @common_exam = []
      unless @x_exam.nil? or @x_exam.empty?         
        @batch_count = @ar_bathces.count
        if @batch_count > 1
          @t_exam.each do |tex|
            z = 0
            @x_exam.each do |xex|
              if tex.name == xex.name
                z += 1
              end
            end
            
            if z == (@batch_count-1)
              @common_exam << tex
            end
          end
          @exam_groups = @common_exam
        else
          @exam_groups = @x_exam
        end        
      else
        unless @t_exam.nil? or @t_exam.empty? 
          @exam_groups = @t_exam
        else
          @exam_groups = []
        end
      end

      @all_subjects = Subject.find(:all, :conditions=> ["is_deleted = false AND no_exams=false and batch_id IN (?)", @ar_bathces], :group => "name")   
    
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
    @is_class = false
    unless params[:class].nil? 
      @is_class = params[:class]
    end
    
    @syllabus = Syllabus.find(params[:id])
    
    if @syllabus.related_syllabus_id.to_i == 0
      @related_syllabuses = Syllabus.find_all_by_related_syllabus_id(@syllabus.id)
    else      
      @syllabus = Syllabus.find_by_id(@syllabus.related_syllabus_id)
      @related_syllabuses = Syllabus.find_all_by_related_syllabus_id(@syllabus.id)
    end
        
    if Syllabus.find(@syllabus.id).destroy                 
      unless @related_syllabuses.nil?
        @related_syllabuses.each do |rs|
          Syllabus.find(rs.id).destroy 
        end
      end
      if @is_class          
        flash[:notice] = "#{t('flash2')}"
        redirect_to :controller => 'syllabus', :action => 'show_syllabus', :batch_id => params[:batch_id], :batch_name => params[:batch_name],:class => true,:course_name => params[:course_name]
      else
        flash[:notice] = "#{t('flash2')}"
        redirect_to :controller => 'syllabus', :action => 'show_syllabus', :batch_id => params[:batch_id], :batch_name => params[:batch_name]        
      end
    end
    
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
    @exam_groups = ExamGroup.active.find_all_by_batch_id(@syllabus.batch_id)
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
  
  def update
    @syllabus = Syllabus.find(params[:id])
    @batches = Batch.active
    if @syllabus.related_syllabus_id.to_i == 0
      @related_syllabuses = Syllabus.find_all_by_related_syllabus_id(@syllabus.id)
    else      
      @syllabus = Syllabus.find_by_id(@syllabus.related_syllabus_id)
      @related_syllabuses = Syllabus.find_all_by_related_syllabus_id(@syllabus.id)
    end
    
    @batch = Batch.find @syllabus.batch_id
    @course = @batch.course
    
    if request.post? and @syllabus.update_attributes(params[:syllabus])                  
      unless @related_syllabuses.nil?
        @related_syllabuses.each do |rs|
          @syllabuses = Syllabus.find(rs.id)
          params[:syllabus][:id] = @syllabuses.id
          params[:syllabus][:batch_id] = @syllabuses.batch_id          
          @syllabuses.update_attributes(params[:syllabus])
        end
      end
      
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
      #@exam_group = ExamGroup.active.find(params[:batch_id])
      @exam_groups = ExamGroup.active.find_all_by_batch_id(params[:batch_id])
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
    @syllabus = Syllabus.find(news_id, :include=>[:author, :subject])
  end
  
  def get_terms(category_id)
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    uri = URI(api_endpoint + "api/syllabus/terms")
    http = Net::HTTP.new(uri.host, uri.port)
    request = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    form_data = {}
    
    form_data['user_secret'] = session[:api_info][0]['user_secret']
    form_data['category_id'] = category_id
    
    if current_user.student?
      form_data['school'] = MultiSchool.current_school.id
      form_data['batch_id'] = current_user.student_record.batch_id
      
    elsif current_user.parent?
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      
      form_data['school'] = student.school_id
      form_data['batch_id'] = student.batch_id
    end
    
    request.set_form_data(form_data)

    response = http.request(request)
    return JSON::parse(response.body)
  end
  
  def get_syllabus(term_id)
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    uri = URI(api_endpoint + "api/syllabus")
    http = Net::HTTP.new(uri.host, uri.port)
    request = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    form_data = {}
    
    form_data['user_secret'] = session[:api_info][0]['user_secret']
    form_data['term'] = term_id
    
    if current_user.student?
      form_data['school'] = MultiSchool.current_school.id
      form_data['batch_id'] = current_user.student_record.batch_id
      
    elsif current_user.parent?
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      
      form_data['school'] = student.school_id
      form_data['batch_id'] = student.batch_id
    end
    
    request.set_form_data(form_data)

    response = http.request(request)
    return JSON::parse(response.body)
  end

end
