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

class SubjectGroupsController < ApplicationController
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
      @subject_groups = SubjectGroup.for_batch(@batch.id, :include => :subjects)
    else
      @course_new = Course.new
      @batches = @course_new.find_batches_data(@batch_name, nil, nil, @batch.id)
      
      @subject_groups = SubjectGroup.find(:all, :conditions => ["batch_id IN (?) and is_deleted = 0", @batches], :include => :subjects, :group => "name")
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
        @subject_group = @batch.subject_groups.build
        @show_batch_subject = true
      else
        @subject_group = @batch.subject_groups.build
        @show_batch_subject = false
      end  
    else
      @subject_group = @batch.subject_groups.build
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
        @subject_group = SubjectGroup.new(params[:subject_group])
        @subject_group.batch_id = @batch.id
        if @subject_group.save
          flash[:notice] = "#{t('flash1')}"
          redirect_to batch_subject_groups_path(@batch, :from_action => @from_action)
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
        @subject_group = SubjectGroup.new(params[:subject_group])
        
        @batches.each do |b|
          @tmp_elective_group = SubjectGroup.new(params[:subject_group])
          @tmp_elective_group.batch_id = b
          if @tmp_elective_group.save
            @saved = true
          else
            @saved = false
            @subject_group = @tmp_elective_group
            break
          end
        end
        
        if @saved
          flash[:notice] = "#{t('flash1')}"
          if @batch_only
            redirect_to batch_subject_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action, :batch_only => "1", :batch_name => @batch_name)
          else
            redirect_to batch_subject_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action)
          end
        else
          if @batch_only
            redirect_to new_batch_elective_group_path(@batch, :show_batch_subject => "0", :from_action => @from_action, :batch_only => "1", :batch_name => @batch_name) 
          else
            redirect_to batch_subject_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action)
          end
        end
      end
    else
      @subject_group = SubjectGroup.new(params[:subject_group])
      @subject_group.batch_id = @batch.id
      if @subject_group.save
        flash[:notice] = "#{t('flash1')}"
        redirect_to batch_subject_groups_path(@batch, :from_action => @from_action)
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
        @subject_group = SubjectGroup.find(params[:id])
        @show_batch_subject = true
      else
        @subject_group = SubjectGroup.find(params[:id])
        @show_batch_subject = false
      end  
    else
      @subject_group = SubjectGroup.find(params[:id])
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
        @subject_group = SubjectGroup.find(params[:id])
        if @subject_group.update_attributes(params[:subject_group])
          flash[:notice] = "#{t('flash3')}"
          #redirect_to [@batch, @subject_group]
          redirect_to batch_subject_groups_path(@batch, :from_action => @from_action)
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
        
        @subject_group = SubjectGroup.find(params[:id])
        @subject_group_name = @subject_group.name
        @subject_groups = SubjectGroup.find(:all, :conditions => ["name like ? and batch_id IN (?)",@subject_group_name,@batches])
        @subject_groups.each do |e|
          @eid = e.id
          @tmp_elective_group = SubjectGroup.find(:first, :conditions => ["id = ? and batch_id IN (?)", @eid, @batches])
          if @tmp_elective_group.update_attributes(:name => params[:subject_group][:name])
            @saved = true
          else
            @saved = false
            @subject_group = @tmp_elective_group
            break
          end
        end
        
        if @saved
          flash[:notice] = "#{t('flash3')}"
          #redirect_to [@batch, @subject_group]
          if @batch_only
            redirect_to batch_subject_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action, :batch_only => "1", :batch_name => @batch_name)
          else
            redirect_to batch_subject_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action)
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
      @subject_group = SubjectGroup.find(params[:id])
      if @subject_group.update_attributes(params[:subject_group])
        flash[:notice] = "#{t('flash3')}"
        #redirect_to [@batch, @subject_group]
        redirect_to batch_subject_groups_path(@batch, :from_action => @from_action)
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
        @subject_group.inactivate
        flash[:notice] =  "#{t('flash2')}"
        redirect_to batch_subject_groups_path(@batch, :from_action => @from_action)
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
        
        @subject_group_name = @subject_group.name
        @subject_groups = SubjectGroup.find(:all, :conditions => ["name like ? and batch_id IN (?)",@subject_group_name,@batches])
        @subject_groups.each do |e|
          @eid = e.id
          @tmp_elective_group = SubjectGroup.find(:first, :conditions => ["id = ? and batch_id IN (?)", @eid, @batches])
          @tmp_elective_group.inactivate
        end
        flash[:notice] =  "#{t('flash2')}"
        if @batch_only
          redirect_to batch_subject_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action, :batch_only => "1", :batch_name => @batch_name)
        else
          redirect_to batch_subject_groups_path(@batch, :show_batch_subject => '0', :from_action => @from_action)
        end
      end
    else  
      @subject_group.inactivate
      flash[:notice] =  "#{t('flash2')}"
      redirect_to batch_subject_groups_path(@batch, :from_action => @from_action)
    end
  end

  private
  def pre_load_objects
    @batch = Batch.find(params[:batch_id], :include => :course)
    @course = @batch.course
    @subject_group = SubjectGroup.find(params[:id]) unless params[:id].nil?
  end
end
