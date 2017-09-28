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

class ElectiveGroupsController < ApplicationController
  before_filter :pre_load_objects#, :except => [:new_elective_subject, :create_elective_subject]
  before_filter :login_required
  filter_access_to :all
  
  def index
    @from_action = "shift"
    unless params[:from_action].nil?
      @from_action = params[:from_action]
    end
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
    
    if @show_batch_subject
      @elective_groups = ElectiveGroup.for_batch(@batch.id, :include => :subjects)
    else
      @course_new = Course.new
      @batches = @course_new.find_batches_data(@batch_name, nil, nil, @batch.id)
      
      @elective_groups = ElectiveGroup.find(:all, :conditions => ["batch_id IN (?) and is_deleted = 0", @batches], :include => :subjects, :group => "name")
    end
  end

  def new
    @from_action = "shift"
    unless params[:from_action].nil?
      @from_action = params[:from_action]
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
    
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 1
        @elective_group = @batch.elective_groups.build
        @show_batch_subject = true
      else
        @elective_group = @batch.elective_groups.build
        @show_batch_subject = false
      end  
    else
      @elective_group = @batch.elective_groups.build
      @show_batch_subject = true
    end
  end

  def create
    @from_action = "shift"
    unless params[:from][:action].nil?
      @from_action = params[:from][:action]
    end
    
    unless params[:batch_subject][:show].nil?
      if params[:batch_subject][:show].to_i == 1
        @elective_group = ElectiveGroup.new(params[:elective_group])
        @elective_group.batch_id = @batch.id
        if @elective_group.save
          flash[:notice] = "#{t('flash1')}"
          redirect_to batch_elective_groups_path(@batch, :from_action => @from_action)
        else
          render :action=>'new', :from_action => @from_action
        end
      else
        @batch_only = false
        @batch_name = ""
        
        unless params[:course].nil?
          unless params[:course][:batch_only].nil?
            if params[:course][:batch_only].to_i == 1
              @batch_only = true
            end
          end

          unless params[:course][:batch_name].nil?
            @batch_name = params[:course][:batch_name]
          end
        end
        
        @batch_name = @batch_only ? @batch_name : nil
      
        @batches = @course.find_batches_data(@batch_name, @course.course_name)
        @saved = false
        @elective_group = ElectiveGroup.new(params[:elective_group])
        
        @batches.each do |b|
          @tmp_elective_group = ElectiveGroup.new(params[:elective_group])
          @tmp_elective_group.batch_id = b
          if @tmp_elective_group.save
            @saved = true
          else
            @saved = false
            @elective_group = @tmp_elective_group
            break
          end
        end
        
        if @saved
          flash[:notice] = "#{t('flash1')}"
          if @batch_only
            redirect_to batch_elective_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action, :batch_only => "1", :batch_name => @batch_name)
          else
            redirect_to batch_elective_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action)
          end
        else
          if @batch_only
            redirect_to new_batch_elective_group_path(@batch, :show_batch_subject => "0", :from_action => @from_action, :batch_only => "1", :batch_name => @batch_name) 
          else
            redirect_to batch_elective_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action)
          end
        end
      end
    else
      @elective_group = ElectiveGroup.new(params[:elective_group])
      @elective_group.batch_id = @batch.id
      if @elective_group.save
        flash[:notice] = "#{t('flash1')}"
        redirect_to batch_elective_groups_path(@batch, :from_action => @from_action)
      else
        render :action=>'new', :from_action => @from_action
      end
    end
  end

  def edit
    @from_action = "shift"
    unless params[:from_action].nil?
      @from_action = params[:from_action]
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
    
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 1
        @elective_group = ElectiveGroup.find(params[:id])
        @show_batch_subject = true
      else
        @elective_group = ElectiveGroup.find(params[:id])
        @show_batch_subject = false
      end  
    else
      @elective_group = ElectiveGroup.find(params[:id])
      @show_batch_subject = true
    end
    render 'edit'
  end

  def update
    @from_action = "shift"
    unless params[:from][:action].nil?
      @from_action = params[:from][:action]
    end
    unless params[:batch_subject][:show].nil?
      if params[:batch_subject][:show].to_i == 1
        @elective_group = ElectiveGroup.find(params[:id])
        if @elective_group.update_attributes(params[:elective_group])
          flash[:notice] = "#{t('flash3')}"
          #redirect_to [@batch, @elective_group]
          redirect_to batch_elective_groups_path(@batch, :from_action => @from_action)
        else
          render 'edit', :from_action => @from_action
        end
      else
        @batch_only = false
        @batch_name = ""
        
        unless params[:course].nil?
          unless params[:course][:batch_only].nil?
            if params[:course][:batch_only].to_i == 1
              @batch_only = true
            end
          end

          unless params[:course][:batch_name].nil?
            @batch_name = params[:course][:batch_name]
          end
        end
        
        @batch_name = @batch_only ? @batch_name : nil
        
        @batches = @course.find_batches_data(@batch_name, @course.course_name)
        
        @saved = false
        
        @elective_group = ElectiveGroup.find(params[:id])
        @elective_group_name = @elective_group.name
        @elective_groups = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?)",@elective_group_name,@batches])
        @elective_groups.each do |e|
          @eid = e.id
          @tmp_elective_group = ElectiveGroup.find(:first, :conditions => ["id = ? and batch_id IN (?)", @eid, @batches])
          if @tmp_elective_group.update_attributes(:name => params[:elective_group][:name])
            @saved = true
          else
            @saved = false
            @elective_group = @tmp_elective_group
            break
          end
        end
        
        if @saved
          flash[:notice] = "#{t('flash3')}"
          #redirect_to [@batch, @elective_group]
          if @batch_only
            redirect_to batch_elective_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action, :batch_only => "1", :batch_name => @batch_name)
          else
            redirect_to batch_elective_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action)
          end
        else
          if @batch_only
            redirect_to edit_batch_elective_group_path(@batch, :show_batch_subject => "0", :from_action => @from_action, :batch_only => "1", :batch_name => @batch_name) 
          else
            redirect_to edit_batch_elective_group_path(@batch, :show_batch_subject => "0", :from_action => @from_action) 
          end
        end
      end
    else
      @elective_group = ElectiveGroup.find(params[:id])
      if @elective_group.update_attributes(params[:elective_group])
        flash[:notice] = "#{t('flash3')}"
        #redirect_to [@batch, @elective_group]
        redirect_to batch_elective_groups_path(@batch, :from_action => @from_action)
      else
        render 'edit', :from_action => @from_action
      end
    end
  end

  def destroy
    @from_action = "shift"
    unless params[:from_action].nil?
      @from_action = params[:from_action]
    end
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 1
        @elective_group.inactivate
        flash[:notice] =  "#{t('flash2')}"
        redirect_to batch_elective_groups_path(@batch, :from_action => @from_action)
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

        @batch_name = @batch_only ? @batch_name : nil
        
        @batches = @course.find_batches_data(@batch_name, @course.course_name)
        
        @elective_group_name = @elective_group.name
        @elective_groups = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?)",@elective_group_name,@batches])
        @elective_groups.each do |e|
          @eid = e.id
          @tmp_elective_group = ElectiveGroup.find(:first, :conditions => ["id = ? and batch_id IN (?)", @eid, @batches])
          @tmp_elective_group.inactivate
        end
        flash[:notice] =  "#{t('flash2')}"
        if @batch_only
          redirect_to batch_elective_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action, :batch_only => "1", :batch_name => @batch_name)
        else
          redirect_to batch_elective_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action)
        end
      end
    else  
      @elective_group.inactivate
      flash[:notice] =  "#{t('flash2')}"
      redirect_to batch_elective_groups_path(@batch, :from_action => @from_action)
    end
  end

  def show
    @from_action = "shift"
    unless params[:from_action].nil?
      @from_action = params[:from_action]
    end
    @show_batch_subject = true
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 0
        @show_batch_subject = false
      end
    end
    if @show_batch_subject 
      @electives = Subject.find_all_by_batch_id_and_elective_group_id(@batch.id,@elective_group.id, :conditions=>["is_deleted = false"])
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

      @batch_name = @batch_only ? @batch_name : nil
      
      @course_new = Course.new
      @batches = @course_new.find_batches_data(@batch_name, nil, nil, @batch.id)
      
      @elective_group_name = @elective_group.name
      
      @elective_active_batch_ids = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?) and is_deleted = 0", @elective_group_name, @batches]).map{|b| b.batch_id}
      
      elective_batch_ids = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?)", @elective_group_name, @elective_active_batch_ids]).map{|b| b.id}
      
      @electives = Subject.find(:all, :conditions => ["elective_group_id IN (?) and is_deleted = 0", elective_batch_ids], :group => "name")
      
    end
  end

  def new_elective_subject
    @from_action = "shift"
    unless params[:from_action].nil?
      @from_action = params[:from_action]
    end
    
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 1
        @show_batch_subject = true
      else
        @show_batch_subject = false
      end  
    else
      @show_batch_subject = true
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
    
    @subject = Subject.new
    @electives = Subject.find_all_by_batch_id_and_elective_group_id(@batch.id,@elective_group.id, :conditions=>["is_deleted = false"])
    @images = Dir.glob("#{RAILS_ROOT}/public/images/icons/subjects/*.png")
    respond_to do |format|
      format.js { render :action => 'new_elective_subject' }
    end
  end

  def create_elective_subject
    @from_action = "shift"
    unless params[:from][:action].nil?
      @from_action = params[:from][:action]
    end
    @saved = false
    @show_batch_subject = true
    unless params[:batch_subject][:show].nil?
      if params[:batch_subject][:show].to_i == 0
        @show_batch_subject = false
      end
    end
    
    if @show_batch_subject
      @subject = Subject.new(params[:subject])
      if @subject.save
        @saved = true
      end
    else
      @batch_only = false
      @batch_name = ""
      
      unless params[:course].nil?
        unless params[:course][:batch_only].nil?
          if params[:course][:batch_only].to_i == 1
            @batch_only = true
          end
        end

        unless params[:course][:batch_name].nil?
          @batch_name = params[:course][:batch_name]
        end
      end
      
      @batch_name = @batch_only ? @batch_name : nil
      
      @course = @batch.course
      @batches = @course.find_batches_data(@batch_name, @course.course_name)
      
      @saved = false

      @batches.each do |b|
        @tmp_subject = Subject.new(params[:subject])
        elective_group_id = @tmp_subject.get_appropriate_group_id(b) 

        if elective_group_id > 0
          @tmp_subject.batch_id = b
          @tmp_subject.elective_group_id = elective_group_id
          if @tmp_subject.save
            @saved = true
          else
            @saved = false
            @subject = @tmp_subject
            break
          end
        end
      end
    end
      
    if @saved
      if @show_batch_subject
        @electives = Subject.find_all_by_batch_id_and_elective_group_id(@batch.id,@elective_group.id, :conditions=>["is_deleted = false"])
      else
        @elective_group_name = @elective_group.name
      
        @elective_active_batch_ids = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?) and is_deleted = 0", @elective_group_name, @batches]).map{|b| b.batch_id}

        elective_batch_ids = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?)", @elective_group_name, @elective_active_batch_ids]).map{|b| b.id}

        @electives = Subject.find(:all, :conditions => ["elective_group_id IN (?) and is_deleted = 0", elective_batch_ids], :group => "name")
      end
      flash[:notice] = "#{t('elective_subject_created_successfully')}"
    else
      @error = true
    end
  end

  def edit_elective_subject
    @from_action = "shift"
    unless params[:from_action].nil?
      @from_action = params[:from_action]
    end
    
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
    
    @subject = Subject.find params[:id2]
    @electives = Subject.find_all_by_batch_id_and_elective_group_id(@batch.id,@elective_group.id, :conditions=>["is_deleted = false"])
    @images = Dir.glob("#{RAILS_ROOT}/public/images/icons/subjects/*.png")
    respond_to do |format|
      format.html { }
      format.js { render :action => 'edit_elective_subject' }
    end
  end

  def update_elective_subject
    @from_action = "shift"
    @saved = false
    @show_batch_subject = true
    unless params[:from_action].nil?
      @from_action = params[:from_action]
    end
    
    unless params[:batch_subject][:show].nil?
      if params[:batch_subject][:show].to_i == 0
        @show_batch_subject = false
      end
    end
    
    if @show_batch_subject
      @subject = Subject.find params[:id2]
      if @subject.update_attributes(params[:subject])
         @saved = true
      end
    else  
      @batch_only = false
      @batch_name = ""
      
      unless params[:course].nil?
        unless params[:course][:batch_only].nil?
          if params[:course][:batch_only].to_i == 1
            @batch_only = true
          end
        end

        unless params[:course][:batch_name].nil?
          @batch_name = params[:course][:batch_name]
        end
      end
      
      @batch_name = @batch_only ? @batch_name : nil
      
      @course_name = @course.course_name
      @batches = @course.find_batches_data(@batch_name, @course_name)
      @saved = false

      @subject = Subject.find params[:id2]
      @subject_name = @subject.name
      @tmp_subjects = Subject.find(:all, :conditions => ["name like ? and batch_id IN (?)",@subject_name,@batches])
      @tmp_subjects.each do |s|
        elective_group_id = s.get_appropriate_group_id(s.batch_id) 
        @sid = s.id
        params[:subject][:elective_group_id] = elective_group_id
        @tmp_subjects_single = Subject.find(:first, :conditions => ["id = ? and batch_id IN (?)", @sid, @batches])
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
      if @show_batch_subject
        @elective_groups = ElectiveGroup.find_all_by_batch_id(@batch.id, :conditions =>{:is_deleted=>false})
        @electives = Subject.find_all_by_batch_id_and_elective_group_id(@batch.id,@elective_group.id, :conditions=>["is_deleted = false"])
      else
        @elective_group_name = @elective_group.name

        @elective_active_batch_ids = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?) and is_deleted = 0", @elective_group_name, @batches]).map{|b| b.batch_id}
        
        elective_batch_ids = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?)", @elective_group_name, @elective_active_batch_ids]).map{|b| b.id}

        @electives = Subject.find(:all, :conditions => ["elective_group_id IN (?) and is_deleted = 0", elective_batch_ids], :group => "name")
        
      end
      
      flash[:notice] = "#{t('elective_subject_updated_successfully')}"
    else
      @error = true
    end
  end

  private
  def pre_load_objects
    @batch = Batch.find(params[:batch_id], :include => :course)
    @course = @batch.course
    @elective_group = ElectiveGroup.find(params[:id]) unless params[:id].nil?
  end
end
