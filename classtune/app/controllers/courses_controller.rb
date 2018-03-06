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

class CoursesController < ApplicationController
  before_filter :login_required
  before_filter :check_permission,:only=>[:manage_course]
  before_filter :find_course, :only => [:show, :edit, :update, :destroy]
  filter_access_to :all
  before_filter :set_precision
  
  def index
    #redirect_to :action => "manage_course"
    
    @courses = Course.active
  end

  def new
    @course = Course.new
    @grade_types=Course.grading_types_as_options
    respond_to do |format|
      format.js { render :action => 'new' }
    end
    #    gpa = Configuration.find_by_config_key("GPA").config_value
    #    if gpa == "1"
    #      @grade_types << "GPA"
    #    end
    #    cwa = Configuration.find_by_config_key("CWA").config_value
    #    if cwa == "1"
    #      @grade_types << "CWA"
    #    end
  end

  def manage_course
    @courses = Course.active_courses.paginate(:per_page=>30,:page=>params[:page])
  end

  def assign_subject_amount
    @course = Course.active.find(params[:id])
    @subjects = @course.batches.map(&:subjects).flatten.compact.map(&:code).compact.flatten.uniq
    @subject_amount = @course.subject_amounts.build
    @subject_amounts = @course.subject_amounts.reject{|sa| sa.new_record?}
    if request.post?
      code = params[:subject_amount][:code]
      @subject_amount = @course.subject_amounts.build(params[:subject_amount])
      if @subject_amount.save
        @subject_amounts = @course.subject_amounts.reject{|sa| sa.new_record?}
        flash[:notice] = "#{t('subject_amount_saved_successfully')}"
        redirect_to assign_subject_amount_courses_path(:id => @course.id)
      else
        render :assign_subject_amount
      end
    end
  end

  def edit_subject_amount
    @subject_amount = SubjectAmount.find(params[:subject_amount_id])
    @course = @subject_amount.course
    @subjects = @course.batches.map(&:subjects).flatten.compact.map(&:code).compact.flatten.uniq
    if request.post?
      if @subject_amount.update_attributes(params[:subject_amount])
        flash[:notice] = "#{t('subject_amount_has_been_updated_successfully')}"
        redirect_to assign_subject_amount_courses_path(:id => @subject_amount.course_id)
      else
        render :edit_subject_amount
      end
    end
  end

  def destroy_subject_amount
    subject_amount = SubjectAmount.find(params[:subject_amount_id])
    course_id = subject_amount.course_id
    subject_amount.destroy
    flash[:notice] = "#{t('subject_amount_has_been_destroyed_sucessfully')}"
    redirect_to assign_subject_amount_courses_path(:id => course_id)
  end

  def manage_batches
    @batches = Batch.active.find(:all, :group => "name")
    if @batches.length == 1
        @batch = @batches[0]
        batch_name = @batch.name
        school_id = MultiSchool.current_school.id
        Rails.cache.delete("course_data_#{batch_name.parameterize("_")}_#{school_id}")
        @courses = Rails.cache.fetch("course_data_#{batch_name.parameterize("_")}_#{school_id}"){
          @batches_data = Batch.find(:all, :conditions => ["name = ?", batch_name], :select => "course_id")
          @batch_ids = @batches_data.map{|b| b.course_id}
          @courses = Course.find(:all, :conditions => ["courses.id IN (?) and courses.is_deleted = 0 and batches.is_deleted = 0 and batches.name = ?", @batch_ids, batch_name], :select => "courses.*,  GROUP_CONCAT(courses.section_name,'--',courses.id,'--',batches.id) as courses_batches", :joins=> "INNER JOIN `batches` ON batches.course_id = courses.id", :group => 'course_name', :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
          @tmp_courses 
        }
    end
  end

  def edit_batches
    @batches = Batch.find_by_id(params[:id])
    @batches_id = params[:id]
    respond_to do |format|
      format.js { render :action => 'edit_batches' }
    end
  end
  
  def get_batches_classes
    @for_exam = false
    unless params[:for_exam].nil?
      if params[:for_exam]
        @for_exam = true
      end
    end
    
    unless params[:from].nil?
      if params[:from] == "subject"
        @tmp_batch = Batch.active.find_by_id(params[:id])
        unless @tmp_batch.nil?
          batch_name = @tmp_batch.name
        else
          batch_name = params[:name]
        end
      else
        @batch = Batch.active.find_by_id(params[:id])
        unless @batch.nil?
          batch_name = @batch.name
        else
          batch_name = params[:name]
        end
      end  
    else  
      @batch = Batch.active.find_by_id(params[:id])
      unless @batch.nil?
        batch_name = @batch.name
      else
        batch_name = params[:name]
      end
    end
    
    
    
    school_id = MultiSchool.current_school.id
    Rails.cache.delete("course_data_#{batch_name.parameterize("_")}_#{school_id}")
    @courses = Rails.cache.fetch("course_data_#{batch_name.parameterize("_")}_#{school_id}"){
      @batches = Batch.active.find(:all, :conditions => ["name = ?", batch_name], :select => "course_id")
      @batch_ids = @batches.map{|b| b.course_id}
      @tmp_courses = Course.find(:all, :conditions => ["courses.id IN (?) and courses.is_deleted = 0 and batches.is_deleted = 0 and batches.name = ?", @batch_ids, batch_name], :select => "courses.*,  GROUP_CONCAT(courses.section_name,'--',courses.id,'--',batches.id) as courses_batches", :joins=> "INNER JOIN `batches` ON batches.course_id = courses.id", :group => 'course_name', :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
      @tmp_courses
    }
    
    unless params[:from].nil?
      if params[:from] == "subject"
        respond_to do |format|
          format.js { render :action => 'batch_classes_subjects' }
        end
      elsif params[:from] == "syllabus"
        respond_to do |format|
          format.js { render :action => 'batch_classes_syllabus' }
        end
      else
        respond_to do |format|
          format.js { render :action => 'batch_classes' }
        end
      end
    else
      respond_to do |format|
        format.js { render :action => 'batch_classes' }
      end
    end
  end
  
  def save_batches
    @batch = Batch.find_by_id(params[:main_batch_id])
    old_batch_name = @batch.name
    @batch_name = params[:batch][:name]
    @batch_new = Batch.new
    @main_batch_id = params[:main_batch_id]
    unless @batch_name.nil?
      if @batch_name.to_s.length > 0
        @batches_exists = Batch.find(:all, :conditions => ["name = ?", @batch_name])
        unless @batches_exists.empty?
          @batch_new.errors.add("Batch Name","already exists")
          @error = true
        else  
          @batches = Batch.find(:all, :conditions => ["name = ?", old_batch_name])
          @batches.each do |batch|
            @batch_data = Batch.find_by_id(batch.id)
            @batch_data.update_attributes(:name => @batch_name)
          end
        end
      else
        @batch_new.errors.add("Batch Name","Can't be empty")
        @error = true
      end
    else
      @batch_new.errors.add("Batch Name","Can't be empty")
      @error = true
    end
  end
  
  def grouped_batches
    @course = Course.find(params[:id])
    @batch_groups = @course.batch_groups
    @batches = @course.active_batches.reject{|b| GroupedBatch.exists?(:batch_id=>b.id)}
    @batch_group = BatchGroup.new
  end

  def create_batch_group
    @batch_group = BatchGroup.new(params[:batch_group])
    @course = Course.find(params[:course_id])
    @batch_group.course_id = @course.id
    @error=false
    if params[:batch_ids].blank?
      @error=true
    end
    if @batch_group.valid? and @error==false
      @batch_group.save
      batches = params[:batch_ids]
      batches.each do|batch|
        GroupedBatch.create(:batch_group_id=>@batch_group.id,:batch_id=>batch)
      end
      @batch_group = BatchGroup.new
      @batch_groups = @course.batch_groups
      @batches = @course.active_batches.reject{|b| GroupedBatch.exists?(:batch_id=>b.id)}
      render(:update) do|page|
        page.replace_html "category-list", :partial=>"batch_groups"
        page.replace_html 'flash', :text=>'<p class="flash-msg"> Batch Group created successfully. </p>'
        page.replace_html 'errors', :partial=>"form_errors"
        page.replace_html 'class_form', :partial=>"batch_group_form"
      end
    else
      if params[:batch_ids].blank?
        @batch_group.errors.add_to_base "Atleast one batch must be selected."
      end
      render(:update) do|page|
        page.replace_html 'errors', :partial=>'form_errors'
        page.replace_html 'flash', :text=>""
      end
    end
  end

  def edit_batch_group
    @batch_group = BatchGroup.find(params[:id])
    @course = @batch_group.course
    @assigned_batches = @course.active_batches.reject{|b| (!GroupedBatch.exists?(:batch_id=>b.id,:batch_group_id=>@batch_group.id))}
    @batches = @course.active_batches.reject{|b| (GroupedBatch.exists?(:batch_id=>b.id))}
    @batches = @assigned_batches + @batches
    render(:update) do|page|
      page.replace_html "class_form", :partial=>"batch_group_edit_form"
      page.replace_html 'errors', :partial=>'form_errors'
      page.replace_html 'flash', :text=>""
    end
  end

  def update_batch_group
    @batch_group = BatchGroup.find(params[:id])
    @course = @batch_group.course
    unless params[:batch_ids].blank?
      if @batch_group.update_attributes(params[:batch_group])
        @batch_group.grouped_batches.map{|b| b.destroy}
        batches = params[:batch_ids]
        batches.each do|batch|
          GroupedBatch.create(:batch_group_id=>@batch_group.id,:batch_id=>batch)
        end
        @batch_group = BatchGroup.new
        @batch_groups = @course.batch_groups
        @batches = @course.active_batches.reject{|b| GroupedBatch.exists?(:batch_id=>b.id)}
        render(:update) do|page|
          page.replace_html "category-list", :partial=>"batch_groups"
          page.replace_html 'flash', :text=>'<p class="flash-msg"> Batch Group updated successfully. </p>'
          page.replace_html 'errors', :partial=>"form_errors"
          page.replace_html 'class_form', :partial=>"batch_group_form"
        end
      else
        render(:update) do|page|
          page.replace_html 'errors', :partial=>'form_errors'
          page.replace_html 'flash', :text=>""
        end
      end
    else
      @batch_group.errors.add_to_base("Atleat one Batch must be selected.")
      render(:update) do|page|
        page.replace_html 'errors', :partial=>'form_errors'
        page.replace_html 'flash', :text=>""
      end
    end
  end

  def delete_batch_group
    @batch_group = BatchGroup.find(params[:id])
    @course = @batch_group.course
    @batch_group.destroy
    @batch_group = BatchGroup.new
    @batch_groups = @course.batch_groups
    @batches = @course.active_batches.reject{|b| GroupedBatch.exists?(:batch_id=>b.id)}
    render(:update) do|page|
      page.replace_html "category-list", :partial=>"batch_groups"
      page.replace_html 'flash', :text=>'<p class="flash-msg"> Batch Group deleted successfully. </p>'
      page.replace_html 'errors', :partial=>"form_errors"
      page.replace_html 'class_form', :partial=>"batch_group_form"
    end
  end

  def update_batch
    @batch = Batch.find_all_by_course_id(params[:course_name], :conditions => { :is_deleted => false, :is_active => true })

    render(:update) do |page|
      page.replace_html 'update_batch', :partial=>'update_batch'
    end

  end

  def create
    course_name = params[:course][:course_name].strip
    code = params[:course][:code].strip
    section = params[:course][:section_name].strip
    #grading_type = params[:course][:grading_type]
    #course = Course.find_by_course_name_and_code_and_section_name_and_grading_type(course_name, code, section, grading_type, :include => :batches)
    course = Course.find_by_course_name_and_code_and_section_name_and_is_deleted(course_name, code, section,false, :include => :batches)
    if params[:new_batches_selection].join(',').to_i == 1
      #FIND IF COURSE EXISTS
      if course.nil?
        @course = Course.new params[:course]
        @tmp_course = Course.find(:all, :conditions => ["is_deleted = 0 and code = ?", params[:course][:code] ])

        @save_the_course = true
        unless @tmp_course.nil? or @tmp_course.empty?
          @course.errors.add("Class Code"," is already taken")
          @save_the_course = false
          @error = true
        end
        
        if @save_the_course
          has_batch = false
          course_name = params[:course][:course_name]
          course_ids = Course.find(:all, :conditions => ["is_deleted = 0 and course_name LIKE ?", course_name]).map{|c| c.id}
          params[:course][:batches_attributes].each do |k, b|
            tmp_batch = Batch.find(:all, :conditions => ['course_id IN (?) and name LIKE ?', course_ids, b['name']])
            unless tmp_batch.nil?
              has_batch = true
              break
            end
          end
          
          if has_batch
            @tmp_course = Course.find(:all, :conditions => ["is_deleted = 0 and course_name LIKE ? and section_name = ?", course_name, params[:course][:section_name] ])
            unless @tmp_course.nil? or @tmp_course.empty?
              @course = Course.new params[:course]
              @course.errors.add("Section"," is already taken")
              @save_the_course = false
              @error = true
            end
          end
          
        end
        
        if @save_the_course
          if @course.save
            course_id = @course.id
            @batches_for_this_course = Batch.find_all_by_course_id(course_id)
            
            params[:course][:batches_attributes].each do |k, b|
              batch_id = 0
              @batches_for_this_course.each do |batch|
                if batch.name == b['name']
                  batch_id = batch.id
                  break
                end
              end
              if batch_id > 0
                course_batches = @course.find_batches_data(b['name'], params[:course][:course_name])
                course_batches.each do |cb|
                  if cb != batch_id
                    subjects = Subject.find_all_by_batch_id(cb,:conditions=>'is_deleted=false')
                    unless subjects.nil? or subjects.empty?
                      subjects.each do |subject|
                        if subject.elective_group_id.nil?
                          Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>batch_id,:no_exams=>subject.no_exams,:no_exams_sjws=>subject.no_exams_sjws, :icon_number => subject.icon_number,
                            :max_weekly_classes=>subject.max_weekly_classes,:elective_group_id=>subject.elective_group_id,:credit_hours=>subject.credit_hours,:is_deleted=>false)
                        else
                          elect_group_exists = ElectiveGroup.find_by_name_and_batch_id(ElectiveGroup.find(subject.elective_group_id).name,batch_id)
                          if elect_group_exists.nil?
                            elect_group = ElectiveGroup.create(:name=>ElectiveGroup.find(subject.elective_group_id).name,
                              :batch_id=>self.id,:is_deleted=>false)
                            Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>batch_id,:no_exams=>subject.no_exams,:no_exams_sjws=>subject.no_exams_sjws, :icon_number => subject.icon_number,
                              :max_weekly_classes=>subject.max_weekly_classes,:elective_group_id=>elect_group.id,:credit_hours=>subject.credit_hours,:is_deleted=>false)
                          else
                            Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>batch_id,:no_exams=>subject.no_exams,:no_exams_sjws=>subject.no_exams_sjws, :icon_number => subject.icon_number,
                              :max_weekly_classes=>subject.max_weekly_classes,:elective_group_id=>elect_group_exists.id,:credit_hours=>subject.credit_hours,:is_deleted=>false)
                          end
                        end
                      end
                      break;
                    end
                  end
                end
              end
            end
            @courses = Course.active_courses.paginate(:per_page=>30,:page=>params[:page])
            t_course = Course.find @course.id, :include => :batches
            
            unless t_course.batches.nil?
              school_id = MultiSchool.current_school.id
              t_course.batches.each do |b|
                batch_id = b.id
                if b.name.strip.downcase == "general"
                  batch_id_zero = 0
                else
                  batch_id_zero = batch_id
                end
                Rails.cache.delete("section_data_#{t_course.course_name}_#{batch_id_zero}_#{school_id}")
                Rails.cache.delete("classes_data_#{batch_id}_#{school_id}")
                Rails.cache.delete("course_data_#{t_course.id}_#{b.name}_#{school_id}")
              end
            end
            flash[:notice] = "#{t('flash1')}"
          else
            @grade_types=Course.grading_types_as_options
            @error = true
          end
        else
          @grade_types=Course.grading_types_as_options
          @error = true
        end
      else
        course_id = course.id
        has_batch = false
        params[:course][:batches_attributes].each do |k, b|
          tmp_batch = Batch.find_by_course_id_and_name(course_id, b["name"])
          unless tmp_batch.nil?
            has_batch = true
            break
          end
        end
        if has_batch
          @course = Course.new params[:course]
          @course.errors.add("Shift"," and class already exists")
          @error = true
        else
          #IF there is a General Batch for this course, then we have to remove that
          t_batch = Batch.find_by_name_and_course_id('General',course_id)
          unless t_batch.nil?
            t_batch.destroy
          end
          saved = false
          params[:course][:batches_attributes].each do |k, b|
            @batch = course.batches.build(b)
            if @batch.save
              saved = true
              @batch.importPreviousBatchSubject(course_id)
              @batch.importPreviousBatchFees(course_id)
            else
              saved = false
            end
          end
          
          if saved
            @courses = Course.active_courses.paginate(:per_page=>30,:page=>params[:page])
            t_course = Course.find course_id, :include => :batches
            
            unless t_course.batches.nil?
              school_id = MultiSchool.current_school.id
              t_course.batches.each do |b|
                batch_id = b.id
                if b.name.strip.downcase == "general"
                  batch_id_zero = 0
                else
                  batch_id_zero = batch_id
                end
                Rails.cache.delete("section_data_#{t_course.course_name}_#{batch_id_zero}_#{school_id}")
                Rails.cache.delete("classes_data_#{batch_id}_#{school_id}")
                Rails.cache.delete("course_data_#{t_course.id}_#{b.name}_#{school_id}")
              end
            end
            flash[:notice] = "#{t('flash1')}"
          else
            @grade_types=Course.grading_types_as_options
            @error = true
          end
        end
      end
    else
      if course.nil?
        @tmp_course = Course.find(:all, :conditions => ["is_deleted = 0 and code = ?", params[:course][:code] ])

        @save_the_course = true
        unless @tmp_course.nil? or @tmp_course.empty?
          @course = Course.new params[:course]
          @course.errors.add("Class Code"," is already taken")
          @save_the_course = false
          @error = true
        end
        
        check_batch_nil = false
#        if Batch.active.find(:all, :group => "name").length == 1
#          batch_data = Batch.active.find(:all, :group => "name")
#          if batch_data[0].name.downcase != "general"
#            check_batch_nil = true
#          end
#        elsif Batch.active.find(:all, :group => "name").length > 1
#          check_batch_nil = true
#        end
        
        if check_batch_nil == false
          if params[:batches_selection].nil?
            params[:batches_selection] = ['General']
          end
        end
        
        error_batch_empty = false
        if params[:batches_selection].nil?
          error_batch_empty = true
          @course = Course.new params[:course]
          @course.errors.add("You"," Must select at least on Shift to continue")
          @error = true
        end
        
        if error_batch_empty == false
          if @save_the_course
            has_batch = false
            course_name = params[:course][:course_name]
            course_ids = Course.find(:all, :conditions => ["course_name LIKE ?", course_name]).map{|c| c.id}
            params[:batches_selection].each do |b|
              tmp_batch = Batch.find(:all, :conditions => ['course_id IN (?) and name LIKE ?', course_ids, b])
              unless tmp_batch.nil?
                has_batch = true
                break
              end
            end

            if has_batch
              @tmp_course = Course.find(:all, :conditions => ["is_deleted = 0 and course_name LIKE ? and section_name = ?", course_name, params[:course][:section_name] ])
              unless @tmp_course.nil? or @tmp_course.empty?
                @course = Course.new params[:course]
                @course.errors.add("Section"," is already taken")
                @save_the_course = false
                @error = true
              end
            end

          end

          if @save_the_course
              i = 0
              params[:batches_selection].each do |b|
                unless params[:course][:batches_attributes][i.to_s].nil?
                  params[:course][:batches_attributes][i.to_s]['name'] = b
                else
                  params[:course][:batches_attributes][i.to_s] = {}
                  params[:course][:batches_attributes][i.to_s]['name'] = b
                  params[:course][:batches_attributes][i.to_s]['start_date'] = I18n.l(Date.today,:format=>:default)
                  params[:course][:batches_attributes][i.to_s]['end_date'] = I18n.l(Date.today + 5.year,:format=>:default)
                end
                i += 1;
              end
              @course = Course.new params[:course]
              if @course.save
                course_id = @course.id
                @batches_for_this_course = Batch.find_all_by_course_id(course_id)
                params[:course][:batches_attributes].each do |k, b|
                  batch_id = 0
                  @batches_for_this_course.each do |batch|
                    if batch.name == b['name']
                      batch_id = batch.id
                      break
                    end
                  end
                  if batch_id > 0
                    course_batches = @course.find_batches_data(b['name'], params[:course][:course_name])
                    course_batches.each do |cb|
                      if cb != batch_id
                        subjects = Subject.find_all_by_batch_id(cb,:conditions=>'is_deleted=false')
                        unless subjects.nil? or subjects.empty?
                          subjects.each do |subject|
                            if subject.elective_group_id.nil?
                              Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>batch_id,:no_exams=>subject.no_exams,:no_exams_sjws=>subject.no_exams_sjws, :icon_number => subject.icon_number,
                                :max_weekly_classes=>subject.max_weekly_classes,:elective_group_id=>subject.elective_group_id,:credit_hours=>subject.credit_hours,:is_deleted=>false)
                            else
                              elect_group_exists = ElectiveGroup.find_by_name_and_batch_id(ElectiveGroup.find(subject.elective_group_id).name,batch_id)
                              if elect_group_exists.nil?
                                elect_group = ElectiveGroup.create(:name=>ElectiveGroup.find(subject.elective_group_id).name,
                                  :batch_id=>self.id,:is_deleted=>false)
                                Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>batch_id,:no_exams=>subject.no_exams,:no_exams_sjws=>subject.no_exams_sjws, :icon_number => subject.icon_number,
                                  :max_weekly_classes=>subject.max_weekly_classes,:elective_group_id=>elect_group.id,:credit_hours=>subject.credit_hours,:is_deleted=>false)
                              else
                                Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>batch_id,:no_exams=>subject.no_exams,:no_exams_sjws=>subject.no_exams_sjws, :icon_number => subject.icon_number,
                                  :max_weekly_classes=>subject.max_weekly_classes,:elective_group_id=>elect_group_exists.id,:credit_hours=>subject.credit_hours,:is_deleted=>false)
                              end
                            end
                          end
                          break;
                        end
                      end
                    end
                  end
                end
                @courses = Course.active_courses.paginate(:per_page=>30,:page=>params[:page])
                t_course = Course.find course_id, :include => :batches
            
                unless t_course.batches.nil?
                  school_id = MultiSchool.current_school.id
                  t_course.batches.each do |b|
                    batch_id = b.id
                    if b.name.strip.downcase == "general"
                      batch_id_zero = 0
                    else
                      batch_id_zero = batch_id
                    end
                    Rails.cache.delete("section_data_#{t_course.course_name}_#{batch_id_zero}_#{school_id}")
                    Rails.cache.delete("classes_data_#{batch_id}_#{school_id}")
                    Rails.cache.delete("course_data_#{t_course.id}_#{b.name}_#{school_id}")
                  end
                end
                flash[:notice] = "#{t('flash1')}"
              else
                @grade_types=Course.grading_types_as_options
                @error = true
              end
          else  
            @grade_types=Course.grading_types_as_options
            @error = true
          end
        else
          @grade_types=Course.grading_types_as_options
          @error = true
        end
      else  
        course_id = course.id
        has_batch = false
        check_batch_nil = false
        if Batch.active.find(:all, :group => "name").length == 1
          batch_data = Batch.active.find(:all, :group => "name")
          
          if batch_data[0].name.downcase != "general"
            check_batch_nil = true
          end
        elsif Batch.active.find(:all, :group => "name").length > 1
          check_batch_nil = true
        end
        
        if check_batch_nil == false
          if params[:batches_selection].nil?
            params[:batches_selection] = ['General']
          end
        end
        
        error_batch_empty = false
        if params[:batches_selection].nil?
          error_batch_empty = true
          @course = Course.new params[:course]
          @course.errors.add("You"," Must select at least on Shift to continue")
          @error = true
        end
        
        if error_batch_empty == false
          params[:batches_selection].each do |b|
            tmp_batch = Batch.find_by_course_id_and_name(course_id, b)
            unless tmp_batch.nil?
              has_batch = true
              break
            end
          end
          
          if has_batch
            @course = Course.new params[:course]
            @course.errors.add("Shift"," and class already exists")
            @error = true
          else
            t_batch = Batch.find_by_name_and_course_id('General',course_id)
            unless t_batch.nil?
              t_batch.destroy
            end
            i = 0
            params[:batches_selection].each do |b|
              unless params[:course][:batches_attributes][i.to_s].nil?
                params[:course][:batches_attributes][i.to_s]['name'] = b
              else
                params[:course][:batches_attributes][i.to_s] = {}
                params[:course][:batches_attributes][i.to_s]['name'] = b
                params[:course][:batches_attributes][i.to_s]['start_date'] = I18n.l(Date.today,:format=>:default)
                params[:course][:batches_attributes][i.to_s]['end_date'] = I18n.l(Date.today + 5.year,:format=>:default)
              end
              i += 1;
            end
            @course = Course.new
            params[:course][:batches_attributes].each do |k, b|
              @batch = course.batches.build(b)
              if @batch.save
                saved = true
                @batch.importPreviousBatchSubject(course_id)
                @batch.importPreviousBatchFees(course_id)
              else
                saved = false
              end
            end

            if saved
              @courses = Course.active_courses.paginate(:per_page=>30,:page=>params[:page])
              t_course = Course.find course_id, :include => :batches
            
              unless t_course.batches.nil?
                school_id = MultiSchool.current_school.id
                t_course.batches.each do |b|
                  batch_id = b.id
                  if b.name.strip.downcase == "general"
                    batch_id_zero = 0
                  else
                    batch_id_zero = batch_id
                  end
                  Rails.cache.delete("section_data_#{t_course.course_name}_#{batch_id_zero}_#{school_id}")
                  Rails.cache.delete("classes_data_#{batch_id}_#{school_id}")
                  Rails.cache.delete("course_data_#{t_course.id}_#{b.name}_#{school_id}")
                end
              end
              flash[:notice] = "#{t('flash1')}"
            else
              @grade_types=Course.grading_types_as_options
              @error = true
            end
          end
        else
          @grade_types=Course.grading_types_as_options
          @error = true
        end
      end
    end
    if @error.nil?
      school_id = MultiSchool.current_school.id
      params[:course][:batches_attributes].each do |k, b|
        Rails.cache.delete("user_cat_links_for_course_#{b['name']}_#{school_id}")
      end
    end
  end

  def edit
    @course = Course.find params[:id]
    @grade_types=Course.grading_types_as_options
    respond_to do |format|
      format.js { render :action => 'edit' }
    end
    #    @grade_types=[]
    #    gpa = Configuration.find_by_config_key("GPA").config_value
    #    if gpa == "1"
    #      @grade_types << "GPA"
    #    end
    #    cwa = Configuration.find_by_config_key("CWA").config_value
    #    if cwa == "1"
    #      @grade_types << "CWA"
    #    end
  end
  
  def edit_section
    @course = Course.find params[:id]
    @grade_types=Course.grading_types_as_options
    @main_course_id = params[:main_course_id]
    respond_to do |format|
      format.js { render :action => 'edit_section' }
    end
    #    @grade_types=[]
    #    gpa = Configuration.find_by_config_key("GPA").config_value
    #    if gpa == "1"
    #      @grade_types << "GPA"
    #    end
    #    cwa = Configuration.find_by_config_key("CWA").config_value
    #    if cwa == "1"
    #      @grade_types << "CWA"
    #    end
  end
  
  def back_add_section
    @course = Course.new
    @course_id = params[:id]
    respond_to do |format|
      format.js { render :action => 'back_add_section' }
    end
  end
  
  def add_section
    @course = Course.new
    @course_id = params[:id]
    @tmp_course = Course.find_by_id(params[:id])
    @course_name = ""
    unless @tmp_course.nil?
      @courses_data = Course.find(:all, :conditions => ["course_name = ? and is_deleted = 0 ", @tmp_course.course_name], :group => "section_name")
      unless @courses_data.nil?
        @course_name = @courses_data[0].course_name
      end
    end
    respond_to do |format|
      format.js { render :action => 'add_section' }
    end
    #    @grade_types=[]
    #    gpa = Configuration.find_by_config_key("GPA").config_value
    #    if gpa == "1"
    #      @grade_types << "GPA"
    #    end
    #    cwa = Configuration.find_by_config_key("CWA").config_value
    #    if cwa == "1"
    #      @grade_types << "CWA"
    #    end
  end
  
  def save_section
    @course = Course.new
    @courses_dt = Course.find_by_id(params[:course][:id])
    
    if @courses_dt.section_name.strip.length == 0
      @courses_dt.update_attributes(:section_name => params[:course][:section_name])
      @sections = Course.find(:all, :conditions => ["course_name = ? and is_deleted = 0 ", @courses_dt.course_name], :select => "section_name", :group => "section_name")
      @course_id = params[:course][:id]
      @tmp_course = Course.find_by_id(params[:course][:id])
      @course_name = ""
      unless @tmp_course.nil?
        @courses_data = Course.find(:all, :conditions => ["course_name = ? and is_deleted = 0  ", @tmp_course.course_name], :group => "section_name")
        unless @courses_data.nil?
          @course_name = @courses_data[0].course_name
        end
      end
    else  
      @batches = Batch.find(:all, :conditions => ["course_id IN (?) ", params[:course][:id]])
      code_ini = @courses_dt.course_name[0,1].upcase
      num_zeros = 4
      nums = @courses_dt.course_name.gsub("Class ","").to_s
      n = nums.bytesize
      num_zeros = num_zeros - n

      k = 0;
      zeros = ""
      while k < num_zeros do
        zeros += "0"
        k += 1
      end	
      @course_new = Course.new
      code = @courses_dt.course_name + params[:course][:section_name]
      @courses_exits = Course.find_by_course_name_and_section_name(@courses_dt.course_name, params[:course][:section_name], :conditions => {:is_deleted => false})
      if @courses_exits.nil?
        if params[:course][:section_name].strip.length == 0
          @course_new.errors.add("Section Name","must not be empty")
          @error = true
        else
          @shifts_data = []
          @subject_data = []
          @batches.each do |b|
            @subject_data = []
            @subjects = Subject.find(:all, :conditions => ["batch_id = ?", b.id])
            @subjects.each do |s|
              @subject_data << {"name" => s.name, "code" => s.code, "max_weekly_classes" => s.max_weekly_classes, "credit_hours" => s.credit_hours, "icon_number" => s.icon_number, "no_exams" => s.no_exams, "no_exams_sjws" => s.no_exams_sjws, "elective_group_id" => s.elective_group_id, "is_deleted" => s.is_deleted, "prefer_consecutive" => s.prefer_consecutive, "amount" => s.amount}
            end
            @shifts_data << {"name" => b.name, "start_date" => b.start_date, "end_date" => b.end_date, "weekday_set_id" => b.weekday_set_id, "class_timing_set_id" => b.class_timing_set_id, "subjects_attributes" => @subject_data}
            school_id = MultiSchool.current_school.id
            Rails.cache.delete("user_cat_links_for_course_#{b.name}_#{school_id}")
          end
          @class_data = []
          @class_data << {"course_name" => @courses_dt.course_name, "section_name" => params[:course][:section_name], "code" => code, "grading_type" => @courses_dt.grading_type, "batches_attributes" => @shifts_data}
          @course_new = Course.new @class_data[0]
          if @course_new.save (false)
            flash[:notice] = "#{t('flash5')}"
            @sections = Course.find(:all, :conditions => ["course_name = ? and is_deleted = 0 ", @courses_dt.course_name], :select => "section_name", :group => "section_name")
            @course_id = params[:course][:id]
            @tmp_course = Course.find_by_id(params[:course][:id])
            @course_name = ""
            unless @tmp_course.nil?
              @courses_data = Course.find(:all, :conditions => ["course_name = ? and is_deleted = 0  ", @tmp_course.course_name], :group => "section_name")
              unless @courses_data.nil?
                @course_name = @courses_data[0].course_name
              end
            end
            
            t_course = Course.find @course_id, :include => :batches
            
            unless t_course.batches.nil?
              school_id = MultiSchool.current_school.id
              t_course.batches.each do |b|
                batch_id = b.id
                if b.name.strip.downcase == "general"
                  batch_id_zero = 0
                else
                  batch_id_zero = batch_id
                end
                Rails.cache.delete("section_data_#{t_course.course_name}_#{batch_id_zero}_#{school_id}")
                Rails.cache.delete("classes_data_#{batch_id}_#{school_id}")
                Rails.cache.delete("course_data_#{t_course.id}_#{b.name}_#{school_id}")
              end
            end
          else
            @error = true
          end
        end
      else
        @course_new.errors.add("Section Name","already exists")
        @error = true
      end
    end
  end

  def update_section
    @course = Course.find_by_id(params[:id])
    if params[:course][:section_name].strip.length == 0
        @course.errors.add("Section Name","must not be empty")
        @error = true
    else
      @courses_exits = Course.find_by_course_name_and_section_name(params[:course][:course_name], params[:course][:section_name])
      if @courses_exits.nil?
        if @course.update_attributes(params[:course])
          @sections = Course.find(:all, :conditions => ["course_name = ? and is_deleted = 0  ", @course.course_name], :select => "section_name", :group => "section_name")
          @main_course_id = params[:main_course_id]
          @course_id = params[:id]
          @tmp_course = Course.find_by_id(params[:id])
          @course_name = ""
          unless @tmp_course.nil?
            @courses_data = Course.find(:all, :conditions => ["course_name = ? and is_deleted = 0  ", @tmp_course.course_name], :group => "section_name")
            unless @courses_data.nil?
              @course_name = @courses_data[0].course_name
            end
          end
          flash[:notice] = "#{t('flash2')}"
        else
          @error = true
        end
      else
        @course.errors.add("Section Name","already exists")
        @error = true
      end
    end
  end
  
  def update
    @courses_dt = Course.find(:all, :conditions => ["id = ? ", params[:id]], :select => "course_name")
    @courses_dt_name = @courses_dt.map{|b| b.course_name}
    @courses_datas = Course.find(:all, :conditions => ["course_name IN (?) AND is_deleted = 0 AND id != ? ",@courses_dt_name, params[:id] ])
    
    if @course.update_attributes(params[:course])
      @courses_datas.each do |c|
        @tmp_course = Course.find_by_id(c.id)
        c.course_name = params[:course][:course_name]
        c.grading_type = params[:course][:grading_type]
        c.save
      end
      flash[:notice] = "#{t('flash2')}"
      @course_name = params[:course][:course_name]
      @course_id = params[:id]
    else
      @error = true
    end
  end

  def destroy
    @courses_dt = Course.find(:all, :conditions => ["id = ? ", params[:id]], :select => "course_name")
    @courses_dt_name = @courses_dt.map{|b| b.course_name}
    @courses_datas = Course.find(:all, :conditions => ["course_name IN (?) ",@courses_dt_name ])
    @found_students = false
    @found_subjects = false
    @courses_datas.each do |course|
      course.batches.each do |batch|
        @batch = Batch.find_by_id(batch.id)
        if @batch.subjects.length > 0
          @found_subjects = true
          break
        elsif @batch.students.length > 0
          @found_students = true
          break
        end
      end
    end
    if @found_students or @found_subjects
      flash[:warn_notice]="<p>#{t('courses.flash4')}</p>"
      redirect_to :action=>'manage_course'
    else
      @courses_datas.each do |course|
        course.batches.each do |batch|
          @batch = Batch.find_by_id(batch.id)
          @batch.inactivate
        end
      end
      @courses_datas.each do |course|
        if course.batches.active.empty?
          course.inactivate
        end
      end
      flash[:notice]="#{t('flash3')}"
      redirect_to :action=>'manage_course'
    end
#    abort(@courses_datas.inspect)
#    if @course.batches.active.empty?
#      @course.inactivate
#      flash[:notice]="#{t('flash3')}"
#      redirect_to :action=>'manage_course'
#    else
#      flash[:warn_notice]="<p>#{t('courses.flash4')}</p>"
#      redirect_to :action=>'manage_course'
#    end
  end
  
  def delete_section
    @course = Course.find_by_id(params[:id])
    @found_students = false
    @found_subjects = false
    @course.batches.each do |batch|
      @batch = Batch.find_by_id(batch.id)
      if @batch.subjects.length > 0
        @found_subjects = true
        break
      elsif @batch.students.length > 0
        @found_students = true
        break
      end
    end
    #abort(@batch.students.inspect)
    if @found_students or @found_subjects
      flash[:warn_notice]="<p>#{t('courses.flash4')}</p>"
      @warn_notice = true
    else
      unless @course.batches.nil?
        school_id = MultiSchool.current_school.id
        @course.batches.each do |b|
          batch_id = b.id
          if b.name.strip.downcase == "general"
            batch_id_zero = 0
          else
            batch_id_zero = batch_id
          end
          Rails.cache.delete("section_data_#{@course.course_name}_#{batch_id_zero}_#{school_id}")
          Rails.cache.delete("classes_data_#{batch_id}_#{school_id}")
          Rails.cache.delete("course_data_#{@course.id}_#{b.name}_#{school_id}")
          Rails.cache.delete("user_cat_links_for_course_#{b.name}_#{school_id}")
        end
      end
      @course.batches.each do |batch|
        @batch = Batch.find_by_id(batch.id)
        @batch.inactivate
      end
      if @course.batches.active.empty?
        @course.inactivate
        @sections = Course.find(:all, :conditions => ["course_name = ? and is_deleted = 0 ", @course.course_name], :select => "section_name", :group => "section_name")
        @main_course_id = params[:main_course_id]
        @course_id = params[:id]
        @tmp_course = Course.find_by_id(params[:id])
        @course_name = ""
        unless @tmp_course.nil?
          @courses_data = Course.find(:all, :conditions => ["course_name = ? and is_deleted = 0  ", @tmp_course.course_name], :group => "section_name")
          unless @courses_data.nil? or @courses_data.empty?
            @course_name = @courses_data[0].course_name
          else
            @no_class = true
            @courses = Course.active_courses.paginate(:per_page=>30,:page=>params[:page])
          end
        end
        flash[:notice]="#{t('flash3')}"
      else
        flash[:warn_notice]="<p>#{t('courses.flash4')}</p>"
        @warn_notice = true
      end
    end
  end

  def show
    @batches = @course.batches.active
  end

  private
  def find_course
    @course = Course.find params[:id]
  end


end