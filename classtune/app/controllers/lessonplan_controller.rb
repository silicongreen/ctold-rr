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

class LessonplanController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only=>[:index,:categories]
  filter_access_to :all
  def download_attachment
    #download the  attached file
    @lessonplan = Lessonplan.find params[:id]
    filename = @lessonplan.attachment_file_name
    unless @lessonplan.nil?
      if @lessonplan.download_allowed_for(current_user)
        send_file  @lessonplan.attachment.path , :type=>@lessonplan.attachment.content_type, :filename => filename
      else
        flash[:notice] = "#{t('you_are_not_allowed_to_download_that_file')}"
        redirect_to :controller=>:news
      end
    else
      flash[:notice]= "#{t('flash_msg4')}"
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end
  def add    
    @lessonplan = Lessonplan.new(params[:lessonplan])
    @lessonplan_categories = LessonplanCategory.active_for_current_user(current_user.id)
    @lessonplan.author = current_user    
    @lessonplan.school_id = MultiSchool.current_school.id    
    
    @current_user = current_user
    @error = false
    if @current_user.employee?
      emp_record = current_user.employee_record 
      @subjects = emp_record.subjects.active
      @subjects.reject! {|s| !s.batch.is_active}
      if emp_record.all_access.to_i == 1
        batches = @current_user.employee_record.batches
        batches += @current_user.employee_record.subjects.collect{|b| b.batch}
        batches = batches.uniq unless batches.empty?
        unless batches.blank?
          batches.each do |batch|
            @subjects += batch.subjects
          end
        end
      end
      @subjects = @subjects.uniq unless @subjects.empty?
      @subjects.sort_by{|s| s.batch.course.code.to_i}
    end
    
    if request.post? and @lessonplan.save      
      subject_ids = ""
      batch_ids = ""
      batch_id = 0
      unless params[:lessonplan][:subject_ids].nil?
        params[:lessonplan][:subject_ids].each do |subject_id|
          subject_ids = subject_ids + subject_id + ","
          @sdata =Subject.find_by_id subject_id
          if batch_id != @sdata.batch_id
            batch_ids = batch_ids + @sdata.batch_id.to_s + ","
            batch_id = @sdata.batch_id
          end

        end
      end

      @lessonplan.update_attributes(:subject_ids => subject_ids)
      @lessonplan.update_attributes(:batch_ids => batch_ids)
      
      flash[:notice] = "#{t('flash1')}"
      redirect_to :controller => 'lessonplan', :action => 'view', :id => @lessonplan.id
    end
  end
    
  def delete
    @lessonplan = Lessonplan.find(params[:id]).destroy
    flash[:notice] = "#{t('flash4')}"
    redirect_to :controller => 'lessonplan', :action => 'index'
  end
  
  def edit
    @lessonplan = Lessonplan.find(params[:id])    
    @lessonplan.author = current_user
    @lessonplan_categories = LessonplanCategory.active_for_current_user(current_user.id)
    @current_user = current_user
    @error = false
    if @current_user.employee?
      @subjects = current_user.employee_record.subjects.active
      @subjects = @subjects.uniq
    end
    
    if request.post? and @lessonplan.update_attributes(params[:lessonplan])
      subject_ids = ""
      batch_ids = ""
      batch_id = 0
      unless params[:lessonplan][:subject_ids].nil?
        params[:lessonplan][:subject_ids].each do |subject_id|
          subject_ids = subject_ids + subject_id + ","
          @sdata =Subject.find_by_id subject_id
          if batch_id != @sdata.batch_id
            batch_ids = batch_ids + @sdata.batch_id.to_s + ","
            batch_id = @sdata.batch_id
          end

        end
      end

      @lessonplan.update_attributes(:subject_ids => subject_ids)
      @lessonplan.update_attributes(:batch_ids => batch_ids)
      
      flash[:notice] = "#{t('flash3')}"
      redirect_to :controller => 'lessonplan', :action => 'view', :id => @lessonplan.id
    end
  end
  
  def update
    @syllabus = Syllabus.find(params[:id])
    @batches = Batch.active
    @related_syllabuses = Syllabus.find_all_by_related_syllabus_id(@syllabus.id)
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
    @lessonplan = nil
    if @current_user.student?
      @student = @current_user.student_record
      @batch = @student.batch
      if @batch.weekday_set_id.present? and @batch.class_timing_set_id.present?
        @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"elective_group_id IS NULL AND is_deleted = false", :include => [:employees])
        @student_electives =StudentsSubject.all(:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
        @elective_subjects = []
        @student_electives.each do |e|
          @elective_subjects.push Subject.find(e.subject_id)
        end
        @subjects = @normal_subjects+@elective_subjects
        
        @lessonplan = Lessonplan.paginate  :conditions=>"FIND_IN_SET(#{@batch.id},batch_ids) AND publish_date is not null AND publish_date <= NOW() AND is_show = 1", :page=>params[:page]
      end
    elsif @current_user.parent?
      target = @current_user.guardian_entry.current_ward_id       
      @student = Student.find_by_id(target)
      @batch=@student.batch
      
      if @batch.weekday_set_id.present? and @batch.class_timing_set_id.present?
        @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"elective_group_id IS NULL AND is_deleted = false", :include => [:employees])
        @student_electives =StudentsSubject.all(:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
        @elective_subjects = []
        @student_electives.each do |e|
          @elective_subjects.push Subject.find(e.subject_id)
        end
        @subjects = @normal_subjects+@elective_subjects
        
        @lessonplan = Lessonplan.paginate  :conditions=>"FIND_IN_SET(#{@batch.id},batch_ids) AND publish_date is not null AND publish_date <= NOW() AND is_show = 1", :page=>params[:page]        
      end
    elsif @current_user.employee?
      @lessonplan_categories = LessonplanCategory.active_for_current_user(current_user.id)    
      @lessonplan = Lessonplan.paginate  :conditions=>"author_id = #{current_user.id}", :page => params[:page]    
    else
      @classes = []
      @batches = []
      @batch_no = 0
      @course_name = ""
      @courses = []
      @batches = Batch.active
      @lessonplan = Lessonplan.paginate  :conditions=>"school_id = #{MultiSchool.current_school.id} AND publish_date is not null AND publish_date <= NOW() AND is_show = 1", :page => params[:page]    
    end    
  end
  def subject_lessonplan
    if @current_user.student?
      @student = current_user.student_record
    elsif    @current_user.parent?
      target = @current_user.guardian_entry.current_ward_id      
      @student = Student.find_by_id(target)
    end
    
    @batch=@student.batch

    @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"elective_group_id IS NULL AND is_deleted = false", :include => [:employees])
    @student_electives =StudentsSubject.all(:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
    @elective_subjects = []
    @student_electives.each do |e|
      @elective_subjects.push Subject.find(e.subject_id)
    end
    @subjects = @normal_subjects+@elective_subjects    
    
    @subject =Subject.find_by_id params[:subject_id]    
    unless @subject.nil?
      @lessonplan = Lessonplan.paginate  :conditions=>"FIND_IN_SET(#{@subject.id},subject_ids) AND FIND_IN_SET(#{@batch.id},batch_ids) AND publish_date is not null AND publish_date <= NOW() AND is_show = 1", :page=>params[:page]  
    else
      @lessonplan = Lessonplan.paginate  :conditions=>"FIND_IN_SET(#{@batch.id},batch_ids) AND publish_date is not null AND publish_date <= NOW() AND is_show = 1", :page=>params[:page]
    end    
    
    render(:update) do |page|
      page.replace_html 'all_news', :partial=>'subject_lessonplan'
    end
  end
  
  def lessonplan_by_subject
    
    @subject =Subject.find_by_id params[:subject_id]    
    unless @subject.nil?
      @lessonplan = Lessonplan.paginate  :conditions=>"FIND_IN_SET(#{@subject.id},subject_ids) AND publish_date is not null AND publish_date <= NOW() AND is_show = 1", :page=>params[:page]  
    else
      @lessonplan = Lessonplan.paginate  :conditions=>"school_id = #{MultiSchool.current_school.id} AND publish_date is not null AND publish_date <= NOW() AND is_show = 1", :page => params[:page]    
    end    
    
    render(:update) do |page|
      page.replace_html 'all_news', :partial=>'subject_lessonplan'
    end
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
  def assign_to_class    
    @is_group = false
    @current_user = current_user
    @error = false
    
    unless params[:lessonplan_ids].nil?
      @is_group = true
      if    @current_user.employee?        
        @subjects = current_user.employee_record.subjects.active
        @subjects.reject! {|s| !s.batch.is_active}          
        @lessonplan_ids = params[:lessonplan_ids]
        if request.post? and !params[:subject_ids].nil?
          @lessonplan_ids.each do |lessonplan_id|
            @lessonplan = Lessonplan.find(lessonplan_id)  
            subject_ids = ""
            batch_ids = ""
            batch_id = 0
            unless params[:subject_ids].nil?
              params[:subject_ids].each do |subject_id|
                subject_ids = subject_ids + subject_id + ","
                @sdata =Subject.find_by_id subject_id
                if batch_id != @sdata.batch_id
                  batch_ids = batch_ids + @sdata.batch_id.to_s + ","
                  batch_id = @sdata.batch_id
                end

              end
            end

            @lessonplan.update_attributes(:subject_ids => subject_ids)
            @lessonplan.update_attributes(:batch_ids => batch_ids)
          end
          @error = true
        end        
      end
    else
      @lessonplan = Lessonplan.find(params[:id])
      if    @current_user.employee?
        @subjects = current_user.employee_record.subjects.active
        @subjects.reject! {|s| !s.batch.is_active}      
        if request.post?
          #abort(params.inspect)
          subject_ids = ""
          batch_ids = ""
          batch_id = 0
          unless params[:subject_ids].nil?
            params[:subject_ids].each do |subject_id|
              subject_ids = subject_ids + subject_id + ","
              @sdata =Subject.find_by_id subject_id
              if batch_id != @sdata.batch_id
                batch_ids = batch_ids + @sdata.batch_id.to_s + ","
                batch_id = @sdata.batch_id
              end

            end
          end

          @lessonplan.update_attributes(:subject_ids => subject_ids)
          @lessonplan.update_attributes(:batch_ids => batch_ids)
          flash[:notice] = "#{t('updated')}"
          @error = true
        end
      end
    end   
    
    respond_to do |format|
      format.js { render :action => 'assign_to_class' }
    end
  end
  def categories
    @lessonplan_categories = LessonplanCategory.active_for_current_user(current_user.id)
    @lessonplan_category = LessonplanCategory.new(params[:lessonplan_category])
    @lessonplan_category.school_id = MultiSchool.current_school.id
    @lessonplan_category.author_id = current_user.id
    @images = Dir.glob("#{RAILS_ROOT}/public/images/icons/events/*.png")
    if request.post?
      params[:lessonplan_category].each_value(&:strip!)
      if @lessonplan_category.save
        flash[:notice] = "#{t('flash7')}"
        redirect_to :action => 'categories'
      end
    end
  end

  def category_delete
    @lessonplan_category = LessonplanCategory.update(params[:id], :status=>0)
    @lessonplan_categories = LessonplanCategory.active_for_current_user(current_user.id)
  end

  def category_edit
    @lessonplan_category = LessonplanCategory.find(params[:id])    
  end

  def category_update
    @lessonplan_category = LessonplanCategory.find(params[:id])
    @lessonplan_category_name = @lessonplan_category.name    
    if @lessonplan_category.update_attributes(:name => params[:name])
      @lessonplan_categories = LessonplanCategory.active_for_current_user(current_user.id)
      @lessonplan_category = LessonplanCategory.new
    end
  end
  
  def category_wise_lessonplan
    
    @lessonplan_categories = LessonplanCategory.active_for_current_user(current_user.id)   
    unless params[:lessonplan_category_id].blank?
      @lessonplan = Lessonplan.paginate :conditions=>"lessonplan_category_id=#{params[:lessonplan_category_id].to_s} and author_id=#{current_user.id.to_s}", :page => params[:page] 
    else
      @lessonplan = Lessonplan.paginate :conditions=>"author_id=#{current_user.id.to_s}", :page => params[:page] 
    end  
    render(:update) do |page|
      page.replace_html 'all_news', :partial=>'category_wise_lessonplan'
    end
  end
  
  private

  def show_comments_associate(news_id, params_page=nil)
    if @current_user.student?
      @student = @current_user.student_record
      @batch = @student.batch
      @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"elective_group_id IS NULL AND is_deleted = false", :include => [:employees])
      @student_electives =StudentsSubject.all(:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
      @elective_subjects = []
      @student_electives.each do |e|
          @elective_subjects.push Subject.find(e.subject_id)
      end
      @subjects = @normal_subjects+@elective_subjects
        
      @lessonplan = Lessonplan.find(:first,:conditions => ["FIND_IN_SET(#{@batch.id},batch_ids) AND publish_date is not null AND publish_date <= NOW() AND id = ? and is_show = 1", news_id], :include=>[:author])      
    elsif @current_user.parent?
      target = @current_user.guardian_entry.current_ward_id       
      @student = Student.find_by_id(target)
      @batch = @student.batch
      
      @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"elective_group_id IS NULL AND is_deleted = false", :include => [:employees])
      @student_electives =StudentsSubject.all(:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
      @elective_subjects = []
      @student_electives.each do |e|
        @elective_subjects.push Subject.find(e.subject_id)
      end
      @subjects = @normal_subjects+@elective_subjects
      
      @lessonplan = Lessonplan.find(:first,:conditions => ["FIND_IN_SET(#{@batch.id},batch_ids) AND publish_date is not null AND publish_date <= NOW() AND id = ? and is_show = 1", news_id], :include=>[:author])      
    elsif @current_user.employee?
      @lessonplan = Lessonplan.find(:first,:conditions => ["id = ? and author_id = ?", news_id, @current_user.id], :include=>[:author])
    else
      @lessonplan = Lessonplan.find(:first,:conditions => ["id = ? ", news_id], :include=>[:author])
    end
    if @lessonplan.nil?
      redirect_to :controller => 'lessonplan', :action => 'index'
    end
  end

end
