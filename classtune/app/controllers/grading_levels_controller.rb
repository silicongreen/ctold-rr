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

class GradingLevelsController < ApplicationController
  before_filter :login_required
  filter_access_to :all
  def index
    @batches = Batch.active
    @courses = Course.active_courses
    @grading_levels = GradingLevel.default
  end

  def new
    @grading_level = GradingLevel.new
    @course = Course.find params[:id], :include => :batches if request.xhr? and params[:id]
    #@batch = Batch.find params[:id] if request.xhr? and params[:id]
    if @course.present?
      if @course.batches[0].present?
        @credit = @course.batches[0].gpa_enabled? || @course.batches[0].cce_enabled?
      else  
        @credit = Configuration.cce_enabled? || Configuration.get_config_value('CWA')=='1' || Configuration.get_config_value('GPA')=='1'
      end
    else
      @credit = Configuration.cce_enabled? || Configuration.get_config_value('CWA')=='1' || Configuration.get_config_value('GPA')=='1'
    end
    respond_to do |format|
      format.js { render :action => 'new' }
    end
  end

  def create
    @grading_level = GradingLevel.new(params[:grading_level])
    @course = Course.find params[:course][:id], :include => :batches unless params[:course][:id].empty?
    #@batch = Batch.find params[:grading_level][:batch_id] unless params[:grading_level][:batch_id].empty?
    respond_to do |format|
      @error_data = false
      unless params[:course][:id].empty?
        @courses = Course.find(:all, :conditions => ["course_name like ?", @course.course_name])
        @courses.each do |c|
          @batches = Batch.find_all_by_course_id(c.id)
          @grading_level.batch_id = @batches[0].id
          @batches.each do |batch|
            @tmp_grading_level = GradingLevel.new(params[:grading_level])
            @tmp_grading_level.batch_id = batch.id
            if params[:grading_level][:type_data] == "percentage"
              @tmp_grading_level.max_score = nil
            end
            unless @tmp_grading_level.save
              @error_data = true
            end
          end  
        end
      else
        if params[:grading_level][:type_data] == "percentage"
          @grading_level.max_score = nil
        end
        unless @grading_level.save
          @error_data = true
        end
      end
      
      unless @error_data
        @grading_level.batch.nil? ?
          @grading_levels = GradingLevel.default :
          @grading_levels = GradingLevel.for_batch(@grading_level.batch_id)
        #flash[:notice] = 'Grading level was successfully created.'
        format.html { redirect_to grading_level_url(@grading_level) }
        format.js { render :action => 'create' }
      else
        @error = true
        format.html { render :action => "new" }
        format.js { render :action => 'create' }
      end
    end
  end

  def edit
    @grading_level = GradingLevel.find params[:id]
    @batch = Batch.find(@grading_level.batch_id) unless @grading_level.batch_id.nil?
    if @batch.present?
      @credit = @batch.gpa_enabled? || @batch.cce_enabled?
    else
      @credit = Configuration.get_config_value('CCE')=='1' || Configuration.get_config_value('CWA')=='1' || Configuration.get_config_value('GPA')=='1'
    end
    respond_to do |format|
      format.html { }
      format.js { render :action => 'edit' }
    end
  end

  def update
    if params[:grading_level][:type_data] == "percentage"
      params[:grading_level][:max_score] = nil
    end
    @grading_level = GradingLevel.find params[:id]
    @grade_name = @grading_level.name
    respond_to do |format|
      if @grading_level.update_attributes(params[:grading_level])
        if @grading_level.batch.nil?
          @grading_levels = GradingLevel.default
        else
          @batch = @grading_level.batch
          @grading_levels = GradingLevel.for_batch(@grading_level.batch_id)
          this_id = @grading_level.batch_id
          @batch = Batch.find(@grading_level.batch_id) unless @grading_level.batch_id.nil?
          @courses = Course.find(:all, :conditions => ["course_name like ?", @batch.course.course_name])
          @courses.each do |c|
            @batches = Batch.find_all_by_course_id(c.id)
            @batches.each do |batch|
              if this_id != batch.id
                @tmp_grading_level = GradingLevel.find_by_batch_id_and_name_and_is_deleted(batch.id,@grade_name,0)
                if !@tmp_grading_level.blank?
                  @tmp_grading_level.update_attributes(params[:grading_level])
                end
              end
            end  
          end
        end
        #flash[:notice] = 'Grading level update successfully.'
        format.html { redirect_to grading_level_url(@grading_level) }
        format.js { render :action => 'update' }
      else
        @error = true
        format.html { render :action => "new" }
        format.js { render :action => 'create' }
      end
    end
  end

  def destroy
    @grading_level = GradingLevel.find params[:id]
    this_id = @grading_level.batch_id
    @grading_level.inactivate
    @batch = Batch.find(@grading_level.batch_id) unless @grading_level.batch_id.nil?
    unless @batch.nil?
      @courses = Course.find(:all, :conditions => ["course_name like ?", @batch.course.course_name])
      @courses.each do |c|
        @batches = Batch.find_all_by_course_id(c.id)
        @batches.each do |batch|
          if this_id != batch.id
            @tmp_grading_level = GradingLevel.find_by_batch_id_and_name_and_is_deleted(batch.id,@grading_level.name,0)
            unless @tmp_grading_level.blank?
              @tmp_grading_level.inactivate
            end
          end
        end  
      end
    end
    unless @grading_level.batch.nil?
      @batch = @grading_level.batch
    end
  end

  def show
    @batch = nil
    if params[:course_id] == ''
      @grading_levels = GradingLevel.default
    else
      @tmp_batch = Batch.find_by_course_id(params[:course_id])
      @grading_levels = GradingLevel.for_batch(@tmp_batch.id)
      #@batch = @tmp_batch #Batch.find params[:batch_id] unless params[:batch_id] == ''
      @course = Course.find params[:course_id], :include => :batches unless params[:course_id] == ''
    end
    respond_to do |format|
      format.js { render :action => 'show' }
    end
  end

end