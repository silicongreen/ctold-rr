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

class SubjectsController < ApplicationController
  before_filter :login_required
  before_filter :check_permission,:only=>[:index]
  filter_access_to :all
  
  def index
    @show_batch_subject = true
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 0
        @show_batch_subject = false
      end
    else
      @show_batch_subject = false
    end
    
    @batches = Batch.active.find(:all, :group => "name")
    if @batches.length == 1
        @batch = @batches[0]
        batch_name = @batch.name 
        school_id = MultiSchool.current_school.id
        Rails.cache.delete("course_data_#{batch_name.parameterize("_")}_#{school_id}")
        @courses = Rails.cache.fetch("course_data_#{batch_name.parameterize("_")}_#{school_id}"){
          @batches_data = Batch.find(:all, :conditions => ["name = ?", batch_name], :select => "course_id")
          @batch_ids = @batches_data.map{|b| b.course_id}
          @tmp_courses = Course.find(:all, :conditions => ["courses.id IN (?) and courses.is_deleted = 0 and batches.is_deleted = 0 and batches.name = ?", @batch_ids, batch_name], :select => "courses.*,  GROUP_CONCAT(courses.section_name,'--',courses.id,'--',batches.id) as courses_batches", :joins=> "INNER JOIN `batches` ON batches.course_id = courses.id", :group => 'course_name', :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
          @tmp_courses
        }
    end
  end
  def subgroups
    @subject_id = params[:subject_id]
    @subject = Subject.find_by_id(@subject_id)
    unless @subject.blank?
      @batch = @subject.batch
      @subject_subgroups = SubjectSubgroup.find_all_by_subject_id(@subject_id)
      
    else
      
    end   
    
  end
  def new_subgroup
    @subject_subgroup = SubjectSubgroup.new
    @subject = Subject.find params[:subject_id]
    @subject_subgroups = SubjectSubgroup.find_all_by_subject_id(@subject.id)
    respond_to do |format|
      format.js { render :action => 'new_subgroup' }
    end
  end
  def create_group
    @subject = Subject.find params[:subject_id]
    @subject_subgroup = SubjectSubgroup.new(params[:subject_subgroup])
    @subject_subgroup.subject_id = @subject.id
    if @subject_subgroup.save
      @error = false
      @batch = @subject.batch
      @subject_subgroups = SubjectSubgroup.find_all_by_subject_id(@subject.id)
      flash[:notice] = "Subject Group Created Succesfully"
    else
      @error = true
    end 
    respond_to do |format|
      format.js { render :action => 'create_group' }
    end
    
  end
  
  def edit_subgroup
    @subject_subgroup = SubjectSubgroup.find params[:id]
    @subject = @subject_subgroup.subject
    @subject_subgroups = SubjectSubgroup.find_all_by_subject_id(@subject.id,:conditions=>["id!=? and parent_id!=?",@subject_subgroup.id,@subject_subgroup.id])
    respond_to do |format|
      format.js { render :action => 'edit_subgroup' }
    end
  end
  def update_group
    @subject = Subject.find params[:subject_id]
    @subject_subgroup = SubjectSubgroup.find params[:id]
    @subject_subgroup.parent_id = params[:subject_subgroup][:parent_id]
    @subject_subgroup.name = params[:subject_subgroup][:name]
    @subject_subgroup.priority = params[:subject_subgroup][:priority]
    if @subject_subgroup.save
      @error = false
      @batch = @subject.batch
      @subject_subgroups = SubjectSubgroup.find_all_by_subject_id(@subject.id)
      flash[:notice] = "Subject Group Saved Succesfully"
    else
      @error = true
    end 
    respond_to do |format|
      format.js { render :action => 'update_group' }
    end
    
  end
  def delete_subgroup
    @subject_subgroup = SubjectSubgroup.find params[:id]
    @subject = @subject_subgroup.subject
    @subject_subgroups = SubjectSubgroup.find_all_by_subject_id(@subject.id)
    @subject_subgroup.destroy
    flash[:notice] = "Subject Group Deleted Succesfully"
  end
  
  
  
  
  def new
    @subject = Subject.new
    @batch = Batch.find params[:id] if request.xhr? and params[:id]
    @course_name = params[:course_name]  if request.xhr? and params[:course_name]
    
    @show_batch_subject = params[:show_batch_subject]  if request.xhr? and params[:show_batch_subject]
    
    if @show_batch_subject.to_i == 0
      @course_new = Course.new
      @batches = @course_new.find_batches_data(nil, params[:course_name]);
      @batch_id = @batches[0]
      @batch = Batch.find @batch_id
    end
    
    @batch_only = false
    unless params[:batch_only].nil?
      if params[:batch_only].to_i == 1
        @batch_only = true
      end
    end

    @batch_name = ""
    unless params[:batch_name].nil?
      @batch_name = params[:batch_name]
    end
    
    @elective_group = ElectiveGroup.find params[:id2] unless params[:id2].nil?
    @images = Dir.glob("#{RAILS_ROOT}/public/images/icons/subjects/*.png")
    respond_to do |format|
      format.js { render :action => 'new' }
    end
  end

  def create
    @show_batch_subject = true
    unless params[:course][:show_batch_subject].nil?
      if params[:course][:show_batch_subject].to_i == 0
        @show_batch_subject = false
      end
    end
    
    if @show_batch_subject
      @subject = Subject.new(params[:subject])
        @batch = @subject.batch
        if @subject.save
          if params[:subject][:elective_group_id] == ""
            @subjects = @subject.batch.normal_batch_subject
            @normal_subjects = @subject
            @elective_groups = ElectiveGroup.find_all_by_batch_id(@batch.id)
            flash[:notice] = "#{t('subject_created_successfully')}"
          else
            @batch = @subject.batch
            @elective_groups = ElectiveGroup.find_all_by_batch_id(@batch.id, :conditions =>{:is_deleted=>false})
            @subjects = @subject.batch.normal_batch_subject
            flash[:notice] = "#{t('elective_subject_created_successfully')}"
          end
        else
          @error = true
        end
    else
      
      @batch_only = false
      unless params[:course][:batch_only].nil?
        if params[:course][:batch_only].to_i == 1
          @batch_only = true
        end
      end

      @batch_name = ""
      unless params[:course][:batch_name].nil?
        @batch_name = params[:course][:batch_name]
      end
      
      @batch_name = @batch_only ? @batch_name : nil
      
      @saved = false
      @course_new = Course.new 
      @batches = @course_new.find_batches_data(@batch_name, params[:course][:course_name])
      @batches.each do |b|
        @tmp_subject = Subject.find_by_name_and_code_and_batch_id(params[:subject][:name], params[:subject][:code], b)
        
        if @tmp_subject.nil?
          if params[:subject][:elective_group_id] == ""
            @subject = Subject.new(params[:subject])
            @subject.batch_id = b
            if @subject.save
              @saved = true
            else
              @saved = false
              break
            end
          else
            elective_group_id = params[:subject][:elective_group_id]
            elective = ElectiveGroup.find elective_group_id
            elective_group_name = elective.name
            tmp_elective = ElectiveGroup.find_by_name_and_batch_id(elective_group_name, b)
            unless tmp_elective.nil?
              elective_group_id = tmp_elective.id
              params[:subject][:elective_group_id] = elective_group_id
              @subject = Subject.new(params[:subject])
              @subject.batch_id = b
              if @subject.save
                @saved = true
              else
                @saved = false
                break
              end
            end
          end
        else
          @subject = Subject.new(params[:subject])
          @subject.batch_id = b
          if @subject.save
            @saved = true
          else
            @saved = false
            break
          end
        end
      end
      
      if ! @saved
        @course_name = params[:course][:course_name]
        @error = true
      else
        @course_new = Course.new
        @batches = @course_new.find_batches_data(@batch_name, params[:course][:course_name]);
        @course_name = params[:course][:course_name]
        
        @batch_id = @batches[0]

        @batch = Batch.find @batch_id

        @subjects = Subject.find(:all, :conditions => ["elective_group_id IS NULL AND is_deleted = false and batch_id IN (?)", @batches], :group => "name")
        @elective_groups = ElectiveGroup.find(:all, :conditions => ["is_deleted = false and batch_id IN (?)", @batches], :group => "name")
        if params[:subject][:elective_group_id] == ""
          flash[:notice] = "#{t('subject_created_successfully')}"
        else
          flash[:notice] = "#{t('elective_subject_created_successfully')}"
        end
      end
    end
  end

  def edit
    @subject = Subject.find params[:id]
    @batch = @subject.batch
    @elective_group = ElectiveGroup.find params[:id2] unless params[:id2].nil?
    
    @show_batch_subject = 1
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 0
        @show_batch_subject = 0
      end  
    end
    
    @batch_only = false
    unless params[:batch_only].nil?
      if params[:batch_only].to_i == 1
        @batch_only = true
      end
    end

    @batch_name = ""
    unless params[:batch_name].nil?
      @batch_name = params[:batch_name]
    end
    
    @images = Dir.glob("#{RAILS_ROOT}/public/images/icons/subjects/*.png")
    respond_to do |format|
      format.html { }
      format.js { render :action => 'edit' }
    end
  end

  def update
    @show_batch_subject = true
    
    unless params[:course][:show_batch_subject].nil?
      if params[:course][:show_batch_subject].to_i == 0
        @show_batch_subject = false
      end
    end
    
    if @show_batch_subject
      @subject = Subject.find params[:id]
      @batch = @subject.batch
      if @subject.update_attributes(params[:subject])
        if params[:subject][:elective_group_id] == ""
          @subjects = @subject.batch.normal_batch_subject
          @normal_subjects = @subject
          @elective_groups = ElectiveGroup.find_all_by_batch_id(@batch.id, :conditions =>{:is_deleted=>false})
          flash[:notice] = "#{t('subject_updated_successfully')}"
        else
          @batch = @subject.batch
          @elective_groups = ElectiveGroup.find_all_by_batch_id(@batch.id, :conditions =>{:is_deleted=>false})
          @subjects = @subject.batch.normal_batch_subject
          flash[:notice] = "#{t('elective_subject_updated_successfully')}"
        end
      else
        @error = true
      end
    else
      @subject = Subject.find params[:id]
      @batch = @subject.batch
      @course = @batch.course
      
      @batch_only = false
      unless params[:course][:batch_only].nil?
        if params[:course][:batch_only].to_i == 1
          @batch_only = true
        end
      end

      @batch_name = ""
      unless params[:course][:batch_name].nil?
        @batch_name = params[:course][:batch_name]
      end
      
      @batch_name = @batch_only ? @batch_name : nil
      
      @batches = @course.find_batches_data(@batch_name, @course.course_name)
      
      @saved = false
      @subject_name = @subject.name
      
      found_elective = false
    
      if params[:subject][:elective_group_id] != ""
        elective_group_id = params[:subject][:elective_group_id]
        elective = ElectiveGroup.find elective_group_id
        elective_group_name = elective.name
        elective_active_batch_ids = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?) and is_deleted = 0", elective_group_name, @batches]).map{|e| e.id}
        found_elective = true
      end
      
      if found_elective
        @tmp_subjects = Subject.find(:all, :conditions => ["name like ? and batch_id IN (?) and elective_group_id IN (?) and is_deleted = 0",@subject_name,@batches, elective_active_batch_ids])
      else
        @tmp_subjects = Subject.find(:all, :conditions => ["name like ? and batch_id IN (?) and is_deleted = 0",@subject_name,@batches])
      end
      
      @tmp_subjects.each do |s|
        @sid = s.id
        
        if params[:subject][:elective_group_id] != ""
          elective_group_id = s.get_appropriate_group_id(s.batch_id) 
          params[:subject][:elective_group_id] = elective_group_id
        end
        @tmp_subjects_single = Subject.find(:first, :conditions => ["id = ? and batch_id IN (?) and is_deleted = 0", @sid, @batches])
        unless @tmp_subjects_single.nil?
          if @tmp_subjects_single.update_attributes(params[:subject])
            @saved = true
          else
            @saved = false
            @subject = @tmp_subjects_single
            break
          end
        end
      end
      if @saved
        @subject = Subject.find params[:id]
        @batch = @subject.batch
        @course = @batch.course
        
        @batches = @course.find_batches_data(@batch_name, @course.course_name)
        
        @batch_id = @batches[0]

        @batch = Batch.find @batch_id

        @subjects = Subject.find(:all, :conditions => ["elective_group_id IS NULL AND is_deleted = false and batch_id IN (?)", @batches], :group => "name")
        @elective_groups = ElectiveGroup.find(:all, :conditions => ["is_deleted = false and batch_id IN (?)", @batches], :group => "name")
        if params[:subject][:elective_group_id] == ""
          flash[:notice] = "#{t('subject_updated_successfully')}"
        else
          flash[:notice] = "#{t('elective_subject_updated_successfully')}"
        end
      else
        @error = true
      end
    end
  end

  def destroy
    @has_error = false
    @show_batch_subject = true
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 0
        @show_batch_subject = false
      end
    end
    if ! @show_batch_subject
      @subject = Subject.find params[:id]
      is_elective_group_subject = false
      unless @subject.elective_group_id.nil?
        is_elective_group_subject = true
      end
      @subject_name = @subject.name
      @batch_id = @subject.batch_id
      
      @batch_data = Batch.find @batch_id
      @course = @batch_data.course
      
      @batch_only = false
      unless params[:batch_only].nil?
        if params[:batch_only].to_i == 1
          @batch_only = true
        end
      end

      @batch_name = ""
      unless params[:batch_name].nil?
        @batch_name = params[:batch_name]
      end

      @batch_name = @batch_only ? @batch_name : nil
      
      @batches = @course.find_batches_data(@batch_name, @course.course_name)
      
      found_elective = false
    
      if @subject.elective_group_id != ""
        unless @subject.elective_group_id.nil?
          elective_group_id = @subject.elective_group_id
          elective = ElectiveGroup.find elective_group_id
          elective_group_name = elective.name
          elective_active_batch_ids = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?) and is_deleted = 0", elective_group_name, @batches]).map{|e| e.id}
          found_elective = true
        end
      end
      
      if found_elective
        @tmp_subjects = Subject.find(:all, :conditions => ["name like ? and batch_id IN (?) and elective_group_id IN (?)",@subject_name,@batches, elective_active_batch_ids])
      else
        @tmp_subjects = Subject.find(:all, :conditions => ["name like ? and batch_id IN (?) and elective_group_id IS NULL",@subject_name,@batches])
      end
      
      @tmp_subjects.each do |s|
        @sid = s.id
        if is_elective_group_subject
          elective_group_id = s.get_appropriate_group_id(s.batch_id) 
          if elective_group_id == s.elective_group_id
            @subject_exams= Exam.find_by_subject_id(@sid)
            unless @subject_exams.nil? and @subject.students.empty?
              @has_error = true
            end
          end
        else
          @subject_exams= Exam.find_by_subject_id(@sid)
          unless @subject_exams.nil? and @subject.students.empty?
            @has_error = true
          end
        end
      end
      
      if ! @has_error
        @tmp_subjects.each do |s|
          @tmp_subject_to_destroy = Subject.find s.id
          @tmp_subject_to_destroy.inactivate
        end
      end
    else
      @subject = Subject.find params[:id]
      @subject_exams= Exam.find_by_subject_id(@subject.id)
      if @subject_exams.nil? and @subject.students.empty?
        @subject.inactivate
      else
        @has_error = true
      end
    end
    if @has_error
      @error_text = "#{t('cannot_delete_subjects')}"
      flash[:notice] = "#{t('cannot_delete_subjects')}"
    else
      flash[:notice] = "#{t('subject_deleted_successfully')}"
    end
    
  end

  def assign_elective_group
    @tmp_elective = ElectiveGroup.find params[:id]
    @show_batch_subject = true
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 0
        @show_batch_subject = false
      end
    end
    
    @batch_only = false
    unless params[:batch_only].nil?
      if params[:batch_only].to_i == 1
        @batch_only = true
      end
    end

    @batch_name = ""
    unless params[:batch_name].nil?
      @batch_name = params[:batch_name]
    end

    @batch_name = @batch_only ? @batch_name : nil
    
    @batch_id = @tmp_elective.batch_id
    @batch = Batch.find @batch_id
    @course = @batch.course
    
    @batches = @course.find_batches_data(@batch_name, @course.course_name)
    
    elective = {}
    
    elective[:name] = @tmp_elective.name
    elective[:is_deleted] = 0
    
    ar_elective_groups = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?) and is_deleted = 0", @tmp_elective.name, @batches ], :group => "batch_id").map{|s| s.batch_id}
    
    @error_found = false
    @batches.each do |b|
      if ! ar_elective_groups.include?(b)
        @tmp_elective_get =  ElectiveGroup.find_by_name_and_batch_id(@tmp_elective.name, b)
        if @tmp_elective_get.nil?
          elective[:batch_id] = b
          @tmp_elective_for_save = ElectiveGroup.new elective
          if ! @tmp_elective_for_save.save
            @error_found = true 
            @elective = @tmp_elective_for_save
          end
        else  
          if @tmp_elective_get.is_deleted
            @tmp_elective_get.update_attributes(:is_deleted => false)
          end
        end
      end
    end
    
    @from_action = "shift"
    unless params[:from_action].nil?
      @from_action = params[:from_action]
    end
    
    if @show_batch_subject
      @elective_groups = ElectiveGroup.for_batch(@batch.id, :include => :subjects)
    else
      @course_new = Course.new
      @batches = @course_new.find_batches_data(@batch_name, nil, nil, @batch.id)
      
      @elective_groups = ElectiveGroup.find(:all, :conditions => ["batch_id IN (?) and is_deleted = 0", @batches], :include => :subjects, :group => "name")
    end
    
    @subjects = Subject.find(:all, :conditions => ["elective_group_id IS NULL AND is_deleted = false and batch_id IN (?)", @batches], :group => "name")
    
    if @error_found
      flash[:notice] = "#{t('subject_cant_be_assigned_for_some_batches')}"
    else
      flash[:notice] = "#{t('subject_assigned_successfully')}"
    end
    
    partial_path = ""
    unless params[:from].nil?
      partial_path = params[:from] + '/'
    end
    
    respond_to do |format|
      format.js { render :action => partial_path + 'assign_elective_group' }
    end
  end
  
  def assign
    @elective_group_name = ""
    @found_elective = false
    @tmp_subject = Subject.find params[:id]
    @show_batch_subject = true
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 0
        @show_batch_subject = false
      end
    end
    
    @batch_id = @tmp_subject.batch_id
    
    @batch = Batch.find @batch_id
    @course = @batch.course

    @batch_only = false
    unless params[:batch_only].nil?
      if params[:batch_only].to_i == 1
        @batch_only = true
      end
    end

    @batch_name = ""
    unless params[:batch_name].nil?
      @batch_name = params[:batch_name]
    end

    @batch_name = @batch_only ? @batch_name : nil
    
    if @batch_only
      @batches = @course.find_batches_data(@batch_name, @course.course_name)
    else
      @batches = @course.find_batches_data
    end
    
    subject = {}
    
    subject[:name] = @tmp_subject.name
    subject[:code] = @tmp_subject.code
    subject[:icon_number] = @tmp_subject.icon_number
    subject[:no_exams] = @tmp_subject.no_exams
    subject[:no_exams_sjws] = @tmp_subject.no_exams_sjws
    subject[:max_weekly_classes] = @tmp_subject.max_weekly_classes
    subject[:credit_hours] = @tmp_subject.credit_hours
    subject[:is_deleted] = 0
    subject[:prefer_consecutive] = @tmp_subject.prefer_consecutive
    subject[:amount] = @tmp_subject.amount
    
    found_elective = false
    
    unless @tmp_subject.elective_group_id.nil?
      elective_group_id = @tmp_subject.elective_group_id
      elective = ElectiveGroup.find elective_group_id
      elective_group_name = elective.name
      elective_active_batch_ids = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?) and is_deleted = 0", elective_group_name, @batches]).map{|e| e.id}
      found_elective = true
    end
    
    if found_elective
      ar_subjects = Subject.find(:all, :conditions => ["name like ? and batch_id IN (?) and elective_group_id IN (?) and is_deleted = 0", @tmp_subject.name, @batches, elective_active_batch_ids ], :group => "batch_id").map{|s| s.batch_id}
    else
      ar_subjects = Subject.find(:all, :conditions => ["name like ? and batch_id IN (?) and is_deleted = 0", @tmp_subject.name, @batches ], :group => "batch_id").map{|s| s.batch_id}
    end
    
    @error_found = false
    @batches.each do |b|
      if ! ar_subjects.include?(b)
        found_elective = false
        tmp_group_id = 0
        unless @tmp_subject.elective_group_id.nil? 
          elective_group_id = @tmp_subject.elective_group_id
          elective = ElectiveGroup.find elective_group_id
          elective_group_name = elective.name
          tmp_elective = ElectiveGroup.find_by_name_and_batch_id(elective_group_name, b)
          found_elective = true 
          @elective_group_name = elective_group_name
          @found_elective = true
          unless tmp_elective.nil?
            tmp_group_id = tmp_elective.id
          end
        end
        
        if found_elective == false
          @tmp_subject_get =  Subject.find_by_name_and_code_and_batch_id(subject[:name], subject[:code], b)
          if @tmp_subject_get.nil?
            subject[:batch_id] = b
            @tmp_subject_for_save = Subject.new subject
            if !@tmp_subject_for_save.save
              @error_found = true 
              @subject = @tmp_subject_for_save
            end
          else
            if @tmp_subject_get.is_deleted
              @tmp_subject_get.update_attributes(:is_deleted => false)
            end
          end
        else
          if tmp_group_id > 0
            @tmp_subject_get =  Subject.find_by_name_and_code_and_batch_id(subject[:name], subject[:code], b)
            if @tmp_subject_get.nil?
              subject[:elective_group_id] = tmp_group_id
              subject[:batch_id] = b
              @tmp_subject_for_save = Subject.new subject
              if !@tmp_subject_for_save.save
                @error_found = true 
                @subject = @tmp_subject_for_save
              end
            else
              if @tmp_subject_get.is_deleted
                @tmp_subject_get.update_attributes(:is_deleted => false)
              elsif @tmp_subject_get.elective_group_id != tmp_group_id
                @tmp_subject_get.update_attributes(:elective_group_id => tmp_group_id)
              end
            end
          end
        end
      end
    end
    
    
    @batch = Batch.find @batch_id

    @subjects = Subject.find(:all, :conditions => ["elective_group_id IS NULL AND is_deleted = false and batch_id IN (?)", @batches], :group => "name")
    @elective_groups = ElectiveGroup.find(:all, :conditions => ["is_deleted = false and batch_id IN (?)", @batches], :group => "name")
    
    if @found_elective
      @elective_active_batch_ids = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?) and is_deleted = 0", @elective_group_name, @batches]).map{|b| b.batch_id}
      elective_batch_ids = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?)", @elective_group_name, @elective_active_batch_ids]).map{|b| b.id}
      
      @electives = Subject.find(:all, :conditions => ["elective_group_id IN (?) and is_deleted = 0", elective_batch_ids], :group => "name")
    end
    
    #if params[:subject][:elective_group_id] == ""
    if @error_found
      flash[:notice] = "#{t('subject_cant_be_assigned_for_some_batches')}"
    else
      flash[:notice] = "#{t('subject_assigned_successfully')}"
    end
   # else
    #  flash[:notice] = "#{t('elective_subject_updated_successfully')}"
    #end
    
    partial_path = ""
    unless params[:from].nil?
      partial_path = params[:from] + '/'
    end
    
    respond_to do |format|
      format.js { render :action => partial_path + 'assign' }
    end
  end
  
  
  def show
    @show_batch_subject = true
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 0
        @show_batch_subject = false
      end
    end  
    
    if @show_batch_subject
      if params[:batch_id] == ''
        @subjects = []
      else
        @batch = Batch.find params[:batch_id]
        @subjects = @batch.normal_batch_subject
        @elective_groups = ElectiveGroup.find_all_by_batch_id(params[:batch_id], :conditions =>{:is_deleted=>false})
      end
    else
      @batch_only = false
      unless params[:batch_only].nil?
        if params[:batch_only].to_i == 1
          @batch_only = true
        end
      end
      
      @batch_name = ""
      unless params[:batch_name].nil?
        @batch_name = params[:batch_name]
      end
      
      if params[:course_name] == ''
        @subjects = []
      else
        @course_new = Course.new
        @batch_name = @batch_only ? @batch_name : nil
        
        @batches = @course_new.find_batches_data(@batch_name, params[:course_name])
        
        @course_name = params[:course_name]
        
        @batch_id = @batches[0]
        
        @batch = Batch.find @batch_id
        
        @subjects = Subject.find(:all, :conditions => ["elective_group_id IS NULL AND is_deleted = false and batch_id IN (?)", @batches], :group => "name")
        @elective_groups = ElectiveGroup.find(:all, :conditions => ["is_deleted = false and batch_id IN (?)", @batches], :group => "name")
      end
    end
    respond_to do |format|
      format.js { render :action => 'show' }
    end
  end

end