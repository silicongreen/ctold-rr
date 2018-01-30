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

class TimetableController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only=>[:index,:new_timetable,:edit_master,:view,:teachers_timetable,:timetable,:work_allotment,:student_view]
  before_filter :protect_other_student_data, :except=>[:student_timetable_pdf]
  before_filter :default_time_zone_present_time
  filter_access_to :all
  before_filter :check_status

  def index
    permitted_modules = Rails.cache.fetch("permitted_modules_timetable_#{current_user.id}"){
      @timetable_modules_tmp = []
      @a_user_modules = ['timetable_text']
      menu_links = MenuLink.find_by_name(@a_user_modules)
      menu_id = menu_links.id
      
      menu_links = MenuLink.find_all_by_higher_link_id(menu_id)
      
      menu_links.each do |menu_link|
        if menu_link.link_type=="user_menu"
            menu_id = menu_link.id

            school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")

            if school_menu_links.nil? or school_menu_links.blank?
               @timetable_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => false}
            else
               @timetable_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => true}
            end
        else
          @timetable_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => true}
        end
      end
      @timetable_modules_tmp
    }
    @timetable_modules = permitted_modules
  end
  
  def new_timetable

    if request.post?
      @timetable=Timetable.new(params[:timetable])
      @error=false
      previous=Timetable.find(:all,:conditions=>["end_date >= ? AND start_date <= ?",@timetable.start_date,@timetable.start_date])
      unless previous.empty?
        @error=true
        @timetable.errors.add_to_base('start_date_overlap')
      end
      conflicts=Timetable.find(:all,:conditions=>["end_date >= ? AND start_date <= ?",@timetable.end_date,@timetable.end_date])
      unless conflicts.empty?
        @error=true
        @timetable.errors.add_to_base('end_date_overlap')
      end
      fully_overlapping=Timetable.find(:all,:conditions=>["end_date <= ? AND start_date >= ?",@timetable.end_date,@timetable.start_date])
      unless fully_overlapping.empty?
        @error=true
        @timetable.errors.add_to_base('timetable_in_between_given_dates')
      end
      #      unless @timetable.start_date>=Date.today
      #        @error=true
      #        @timetable.errors.add_to_base('start_date_is_lower_than_today')
      #      end
      if @timetable.start_date > @timetable.end_date
        @error=true
        @timetable.errors.add_to_base('start_date_is_lower_than_end_date')
      end
      unless @error
        if @timetable.save
          flash[:notice]="#{t('timetable_created_from')} #{@timetable.start_date} - #{@timetable.end_date}"
          redirect_to :controller=>:timetable_entries,:action => "new",:timetable_id=>@timetable.id
        else
          flash[:notice]='error_occured'
          render :action=>'new_timetable'
        end
      else
        flash[:warn_notice]=@timetable.errors.full_messages unless @timetable.errors.empty?
        render :action=>'new_timetable'
      end
    end
  end

  def update_timetable
    @timetable=Timetable.find(params[:id])
    @current=false
    if (@timetable.start_date <= Date.today and @timetable.end_date >= Date.today)
      @current=true
    end
    if (@timetable.start_date > Date.today and @timetable.end_date > Date.today)
      @removable=true
    end
    if request.post?
      @tt=Timetable.find(params[:id])
      @error=false
      if params[:timetable][:"start_date(1i)"].present?
        date_start=[params[:timetable][:"start_date(1i)"].to_i,params[:timetable][:"start_date(2i)"].to_i,params[:timetable][:"start_date(3i)"].to_i]
        unless Date::valid_date?(date_start[0],date_start[1],date_start[2]).nil?
          new_start = Date.civil(date_start[0],date_start[1],date_start[2])
        else
          @timetable.errors.add_to_base('start_date_is_invalid')
          @error=true
          new_start=@tt.start_date
        end
      else
        new_start=@tt.start_date
      end
      if params[:timetable][:"end_date(1i)"].present?
        date_end=[params[:timetable][:"end_date(1i)"].to_i,params[:timetable][:"end_date(2i)"].to_i,params[:timetable][:"end_date(3i)"].to_i]
        unless Date::valid_date?(date_end[0],date_end[1],date_end[2]).nil?
          new_end = Date.civil(date_end[0],date_end[1],date_end[2])
        else
          @timetable.errors.add_to_base('end_date_is_invalid')
          @error=true
          new_end=@tt.end_date
        end
      else
        new_end=@tt.end_date
      end
      if new_end<new_start
        @error=true
        @timetable.errors.add_to_base('start_date_is_lower_than_end_date')
      end
      if new_end < Date.today
        @error=true
        @timetable.errors.add_to_base('end_date_is_lower_than_today')
      end
      #      @end_conflicts=Timetable.find(:all,:conditions=>["start_date <= ? AND id != ?",new_end,@tt.id])
      @end_conflicts=Timetable.find(:all,:conditions=>["start_date <= ? AND end_date >= ? AND id != ?",new_end,new_start,@tt.id])
      unless @end_conflicts.empty?
        @error=true
        @timetable.errors.add_to_base('end_date_overlap')
      end
      fully_overlapping=Timetable.find(:all,:conditions=>["end_date <= ? AND start_date >= ? AND id != ?",@timetable.end_date,@timetable.start_date,@timetable.id])
      unless fully_overlapping.empty?
        @error=true
        @timetable.errors.add_to_base('timetable_in_between_given_dates')
      end
      unless @current
        if new_start<=Date.today
          @timetable.errors.add_to_base('start_date_is_lower_than_today')
          @error=true
        end
      end
      unless @error
        if (@tt.start_date <= Date.today and @tt.end_date >= Date.today)
          @tt.end_date=Date.today
          if @tt.save
            unless new_end<=Date.today
              @tt2=Timetable.new
              @tt2.start_date=Date.today+1.days
              @tt2.end_date=new_end
              if @tt2.save
                entries=@tt.timetable_entries
                entries.each do |e|
                  entry2=e.clone
                  entry2.timetable_id=@tt2.id
                  entry2.save
                end
              end
              flash[:notice]=t('timetable_updated')
              redirect_to :controller=>:timetable_entries,:action => "new",:timetable_id=>@tt2.id
            else
              flash[:notice]=t('timetable_updated')
              redirect_to :controller=>:timetable,:action=>:edit_master
            end
          else
            flash[:warn_notice]=@timetable.errors.full_messages unless @timetable.errors.empty?
            render :action => "new_timetable"
          end
        else
          if @tt.update_attributes(params[:timetable])
            flash[:notice]=t('timetable_updated')
            redirect_to :controller=>"timetable",:action => "edit_master"
          else
            @timetable.errors.add_to_base("timetable_update_failure")
            @error=true
            flash[:notice]=t('timetable_update_failure')
          end
        end
      else
        flash[:warn_notice]=@timetable.errors.full_messages unless @timetable.errors.empty?
        #        redirect_to :controller=>"timetable",:action => "update_timetable",:id=>@timetable.id
        render :action => "update_timetable"#,:id=>@timetable.id
      end
    end
  end

  def view
    @timetables=Timetable.all(:order=>"start_date DESC")
    @current_timetable=Timetable.find(:first,:conditions=>["timetables.start_date <= ? AND timetables.end_date >= ?",@local_tzone_time.to_date,@local_tzone_time.to_date])
    @courses=@current_timetable.nil?? [] : Batch.all(:joins=>[:timetable_entries,{:time_table_class_timings=>:timetable}],:conditions=>["timetables.id=#{@current_timetable.id} and batches.class_timing_set_id is NOT NULL and batches.weekday_set_id is NOT NULL "],:include=>:course).uniq
  end

  def edit_master
    @courses = Batch.active
    @timetables=Timetable.find(:all,:conditions=>["end_date > ?",@local_tzone_time.to_date])
  end

  def teachers_timetable
    @timetables=Timetable.all
    ## Prints out timetable of all teachers
    @current=Timetable.find(:first,:conditions=>["timetables.start_date <= ? AND timetables.end_date >= ?",@local_tzone_time.to_date,@local_tzone_time.to_date])
    if @current
      @timetable_entries = Hash.new { |l, k| l[k] = Hash.new(&l.default_proc) }
      @all_timetable_entries = @current.timetable_entries.select{|t| t.batch.is_active}.select{|s| s.class_timing.is_deleted==false}
      @all_batches = @all_timetable_entries.collect(&:batch).uniq#.sort!{|a,b| a.class_timing <=> b.class_timing}
      @all_weekdays = @all_timetable_entries.collect(&:weekday_id).uniq.sort
      @all_classtimings = @all_timetable_entries.collect(&:class_timing).uniq.sort!{|a,b| a.start_time <=> b.start_time}
      @all_subjects = @all_timetable_entries.collect(&:subject).uniq
      @all_teachers = @all_timetable_entries.collect(&:employee).uniq
      @all_timetable_entries.each_with_index do |tt , i|
        @timetable_entries[tt.employee_id][tt.weekday_id][tt.class_timing_id][i] = tt
      end
      @all_subjects.each do |sub|
        unless sub.elective_group.nil?
          @all_teachers+=sub.elective_group.subjects.collect(&:employees).flatten
          @elective_teachers=sub.elective_group.subjects.collect(&:employees).flatten
          @current.timetable_entries.find_all_by_subject_id(sub.id).each_with_index do |tt , i|
            @elective_teachers.each do |e|
              unless sub.elective_group.subjects.first == sub && sub.employees.first == e
                @timetable_entries[e.id][tt.weekday_id][tt.class_timing_id][i] = tt
              end
            end
          end
        end
      end
      @all_teachers=@all_teachers.uniq
    else
      @all_timetable_entries=[]
    end
  end
  #    if request.xhr?
  def update_teacher_tt
    if params[:timetable_id].nil?
      @current=Timetable.find(:first,:conditions=>["timetables.start_date <= ? AND timetables.end_date >= ?",@local_tzone_time.to_date,@local_tzone_time.to_date])
    else
      if params[:timetable_id]==""
        render :update do |page|
          page.replace_html "timetable_view", :text => ""
        end
        return
      else
        @current=Timetable.find(params[:timetable_id])
      end
    end
    @timetable_entries = Hash.new { |l, k| l[k] = Hash.new(&l.default_proc) }
    @all_timetable_entries = @current.timetable_entries.select{|t| t.batch.is_active}.select{|s| s.class_timing.is_deleted==false}
    @all_batches = @all_timetable_entries.collect(&:batch).uniq
    @all_weekdays = @all_timetable_entries.collect(&:weekday_id).uniq.sort
    @all_classtimings = @all_timetable_entries.collect(&:class_timing).uniq.sort!{|a,b| a.start_time <=> b.start_time}
    @all_subjects = @all_timetable_entries.collect(&:subject).uniq
    @all_teachers = @all_timetable_entries.collect(&:employee).uniq
    @all_timetable_entries.each_with_index do |tt , i|
      @timetable_entries[tt.employee_id][tt.weekday_id][tt.class_timing_id][i] = tt
    end
    @all_subjects.each do |sub|
      unless sub.elective_group.nil?
        @all_teachers+=sub.elective_group.subjects.collect(&:employees).flatten
        @elective_teachers=sub.elective_group.subjects.collect(&:employees).flatten
        @current.timetable_entries.find_all_by_subject_id(sub.id).each_with_index do |tt , i|
          @elective_teachers.each do |e|
            unless sub.elective_group.subjects.first == sub && sub.employees.first == e
              @timetable_entries[e.id][tt.weekday_id][tt.class_timing_id][i] = tt
            end
          end
        end
      end
    end
    @all_teachers=@all_teachers.uniq
    render :update do |page|
      page.replace_html "timetable_view", :partial => "teacher_timetable"
    end
  end

  def update_timetable_view
    if(params[:course_id] == "" || params[:course_id].nil? or params[:timetable_id] == "" or params[:timetable_id].nil?)
      if((params[:course_id] == "" or params[:course_id].nil?) and params[:timetable_id].present?)
        @courses=Batch.all(:joins=>[:timetable_entries,{:time_table_class_timings=>:timetable}],:conditions=>["timetables.id=#{params[:timetable_id]} and batches.class_timing_set_id is NOT NULL and batches.weekday_set_id is NOT NULL "],:include=>:course).uniq
        render :update do |page|
          page.replace_html "timetable_view", :text => ""
          page.replace_html "batches", :partial => "timetable_batches"
        end
        return
      end
      if(params[:timetable_id] == "")
        render :update do |page|
          page.replace_html "timetable_view", :text => ""
          page.replace_html "batches", :text => ""
        end
        return
      end
    end
    @batch = Batch.find(params[:course_id])
    @tt = Timetable.find(params[:timetable_id])
    @timetable = TimetableEntry.find_all_by_batch_id_and_timetable_id(@batch.id,@tt.id)
    if @timetable.empty?
      render :update do |page|
        page.replace_html "timetable_view", :text => ""
      end
      return
    end
    @weekday = @tt.time_table_weekdays.find_by_batch_id(@batch.id).weekday_set.weekday_ids
    timetable_class_timing = TimeTableClassTiming.find_by_batch_id_and_timetable_id(@batch.id,@tt.id)
    @class_timing = timetable_class_timing.nil? ? Array.new : timetable_class_timing.class_timing_set.class_timings.timetable_timings
    @timetable_entries=TimetableEntry.find(:all,:conditions=>{:batch_id=>@batch.id,:timetable_id=>@tt.id},:include=>[:subject,:employee])
    @timetable= Hash.new { |h, k| h[k] = Hash.new(&h.default_proc)}
    
    
    if Configuration.find_by_config_key('ViewSmallRoutine').present? and Configuration.find_by_config_key('ViewSmallRoutine').config_value=="1"
  
      @main_time_table_id = Hash.new { |h, k| h[k] = Hash.new(&h.default_proc)}
      @temp_timing = {}
      @all_timing_id = {}
      @i = 0
      @new_class_timing = []
      @running_id = 0
      @checking_max = 0
      @class_timing.each do |ct|

        if @prev_max.blank?
          @prev_max = ct.end_time.strftime("%H%M")
          @prev_max_main = ct.end_time
        end

        if @prev_min.blank?
          @prev_min = ct.start_time.strftime("%H%M")
          @prev_min_main = ct.start_time
        end


        if @checking_max.to_i != ct.start_time.strftime("%H%M").to_i and @i != 0
          if ct.end_time.strftime("%H%M").to_i > @prev_max.to_i
            @prev_max = ct.end_time.strftime("%H%M")
            @prev_max_main = ct.end_time
          end
          if ct.start_time.strftime("%H%M").to_i < @prev_min.to_i
            @prev_min = ct.start_time.strftime("%H%M")
            @prev_min_main = ct.start_time
          end
          @temp_timing.start_time = @prev_min_main
          @temp_timing.end_time = @prev_max_main
          @all_timing_id[@running_id] << ct.id
        else
          if @i != 0
            @new_class_timing << @temp_timing
            @prev_max = ct.end_time.strftime("%H%M")
            @prev_min = ct.start_time.strftime("%H%M")
            @prev_max_main = ct.end_time
            @prev_min_main = ct.start_time
          end
          @checking_max = ct.end_time.strftime("%H%M").to_i
          @temp_timing = ct
          @running_id = ct.id
          @all_timing_id[ct.id]=[]
        end
        j = @i+1
        if j == @class_timing.size
          @new_class_timing << @temp_timing
        end

        @i = @i+1
      end
      @class_timing = @new_class_timing
   
      @timetable_entries.each do |tte|
        if !@all_timing_id.blank? and !@all_timing_id[tte.class_timing_id].blank?
          @timetable[tte.weekday_id][tte.class_timing_id]=tte
          @main_time_table_id[tte.weekday_id][tte.class_timing_id]=tte.class_timing_id
        else
          new_ct = tte.class_timing_id
          if !@all_timing_id.blank?
            @all_timing_id.each do |k,at|
                if at.include?(tte.class_timing_id)
                  new_ct = k
                end
            end
          end
          @timetable[tte.weekday_id][new_ct]=tte
          @main_time_table_id[tte.weekday_id][new_ct]=tte.class_timing_id
        end  
      end
    else
      @timetable_entries.each do |tte|
        @timetable[tte.weekday_id][tte.class_timing_id]=tte
      end
    end  

    render :update do |page|
      page.replace_html "timetable_view", :partial => "view_timetable"
    end
  end

  def destroy
    @timetable=Timetable.find(params[:id])
    if @timetable.destroy
      flash[:notice]=t('timetable_deleted')
      redirect_to :controller=>:timetable
    end
  end

  def employee_timetable
    @employee=Employee.find(params[:id])
    @blocked=true
    if permitted_to? :employee_timetable, :timetable
      @blocked=false
    elsif @current_user.employee_record==@employee
      @blocked=false
    elsif @current_user.admin?
      @blocked=false
    end
    unless @blocked

      @timetables=Timetable.all
      ## Prints out timetable of all teachers
      @current=Timetable.find(:first,:conditions=>["timetables.start_date <= ? AND timetables.end_date >= ?",@local_tzone_time.to_date,@local_tzone_time.to_date])
      unless @current.nil?
        @electives=@employee.subjects.group_by(&:elective_group_id)
        @timetable_entries = Hash.new { |l, k| l[k] = Hash.new(&l.default_proc) }
        
        @employee_subjects = @employee.subjects
        subjects = @employee_subjects.select{|sub| sub.elective_group_id.nil?}
        electives = @employee_subjects.select{|sub| sub.elective_group_id.present?}
        elective_subjects=electives.map{|x| x.elective_group.subjects.first}
        @employee_timetable_subjects = @employee_subjects.map {|sub| sub.elective_group_id.nil? ? sub : sub.elective_group.subjects.first}
        @entries=[]
        @entries += @current.timetable_entries.find(:all,:conditions=>{:subject_id=>subjects,:employee_id => @employee.id})
        @entries += @current.timetable_entries.find(:all,:conditions=>{:subject_id=>elective_subjects})
        @all_timetable_entries = @entries.select{|t| t.batch.is_active}.select{|s| s.class_timing.is_deleted==false}
        @all_batches = @all_timetable_entries.collect(&:batch).uniq
        @all_weekdays = @all_timetable_entries.collect(&:weekday_id).uniq.sort
        @all_classtimings = @all_timetable_entries.collect(&:class_timing).uniq.sort!{|a,b| a.start_time <=> b.start_time}
        @all_teachers = @all_timetable_entries.collect(&:employee).uniq
        
        if Configuration.find_by_config_key('ViewSmallRoutine').present? and Configuration.find_by_config_key('ViewSmallRoutine').config_value=="1"
         
          @main_time_table_id = Hash.new { |h, k| h[k] = Hash.new(&h.default_proc)}
          @temp_timing = {}
          @all_timing_id = {}
          @i = 0
          @new_class_timing = []
          @running_id = 0
          @checking_max = 0
          @all_classtimings.each do |ct|

            if @prev_max.blank?
              @prev_max = ct.end_time.strftime("%H%M")
              @prev_max_main = ct.end_time
            end

            if @prev_min.blank?
              @prev_min = ct.start_time.strftime("%H%M")
              @prev_min_main = ct.start_time
            end


            if @checking_max.to_i != ct.start_time.strftime("%H%M").to_i and @i != 0
              if ct.end_time.strftime("%H%M").to_i > @prev_max.to_i
                @prev_max = ct.end_time.strftime("%H%M")
                @prev_max_main = ct.end_time
              end
              if ct.start_time.strftime("%H%M").to_i < @prev_min.to_i
                @prev_min = ct.start_time.strftime("%H%M")
                @prev_min_main = ct.start_time
              end
              @temp_timing.start_time = @prev_min_main
              @temp_timing.end_time = @prev_max_main
              @all_timing_id[@running_id] << ct.id
            else
              if @i != 0
                @new_class_timing << @temp_timing
                @prev_max = ct.end_time.strftime("%H%M")
                @prev_min = ct.start_time.strftime("%H%M")
                @prev_max_main = ct.end_time
                @prev_min_main = ct.start_time
              end
              @checking_max = ct.end_time.strftime("%H%M").to_i
              @temp_timing = ct
              @running_id = ct.id
              @all_timing_id[ct.id]=[]
            end
            j = @i+1
            if j == @all_classtimings.size
              @new_class_timing << @temp_timing
            end

            @i = @i+1
          end
          @all_classtimings = @new_class_timing


          @all_timetable_entries.each do |tte, i|
            if !@all_timing_id.blank? and !@all_timing_id[tte.class_timing_id].blank?
              @timetable_entries[tte.weekday_id][tte.class_timing_id][i]=tte
              @main_time_table_id[tte.weekday_id][tte.class_timing_id]=tte.class_timing_id
            else
              new_ct = tte.class_timing_id
              if !@all_timing_id.blank?
                @all_timing_id.each do |k,at|
                    if at.include?(tte.class_timing_id)
                      new_ct = k
                    end
                end
              end
              @timetable_entries[tte.weekday_id][new_ct][i]=tte
              @main_time_table_id[tte.weekday_id][new_ct]=tte.class_timing_id
            end  
          end
        else
         
          @all_timetable_entries.each_with_index do |tt , i|
            @timetable_entries[tt.weekday_id][tt.class_timing_id][i] = tt
          end
        end   
        
      
      else
        flash[:notice]=t('no_entries_found')
      end
    else
      flash[:notice]=t('flash_msg6')
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end

  #    if request.xhr?
  def update_employee_tt
    @employee=Employee.find(params[:employee_id])
    if params[:timetable_id].nil?
      @current=Timetable.find(:first,:conditions=>["timetables.start_date <= ? AND timetables.end_date >= ?",@local_tzone_time.to_date,@local_tzone_time.to_date])
    else
      if params[:timetable_id]==""
        render :update do |page|
          page.replace_html "timetable_view", :text => ""
        end
        return
      else
        @current=Timetable.find(params[:timetable_id])
      end
    end
    @electives=@employee.subjects.group_by(&:elective_group_id)
    @timetable_entries = Hash.new { |l, k| l[k] = Hash.new(&l.default_proc) }
    @employee_subjects = @employee.subjects
    subjects = @employee_subjects.select{|sub| sub.elective_group_id.nil?}
    electives = @employee_subjects.select{|sub| sub.elective_group_id.present?}
    elective_subjects=electives.map{|x| x.elective_group.subjects.first}
    @employee_timetable_subjects = @employee_subjects.map {|sub| sub.elective_group_id.nil? ? sub : sub.elective_group.subjects.first}
    @entries=[]
    @entries += @current.timetable_entries.find(:all,:conditions=>{:subject_id=>subjects,:employee_id => @employee.id})
    @entries += @current.timetable_entries.find(:all,:conditions=>{:subject_id=>elective_subjects})
    @all_timetable_entries = @entries.select{|t| t.batch.is_active}.select{|s| s.class_timing.is_deleted==false}
    @all_batches = @all_timetable_entries.collect(&:batch).uniq
    @all_weekdays = @all_timetable_entries.collect(&:weekday_id).uniq.sort
    @all_classtimings = @all_timetable_entries.collect(&:class_timing).uniq.sort!{|a,b| a.start_time <=> b.start_time}
    @all_teachers = @all_timetable_entries.collect(&:employee).uniq
    @all_timetable_entries.each_with_index do |tt , i|
      @timetable_entries[tt.weekday_id][tt.class_timing_id][i] = tt
    end
    render :update do |page|
      page.replace_html "timetable_view", :partial => "employee_timetable"
    end
  end

  def student_view
    @student = Student.find(params[:id])
    @batch=@student.batch
    @course = @batch.course unless @batch.nil?
    if @batch.weekday_set_id.present? and @batch.class_timing_set_id.present?
      timetable_ids = @batch.timetable_entries.collect(&:timetable_id).uniq
      @timetables=Timetable.find timetable_ids      
      @current=Timetable.find(:first,:conditions=>["timetables.start_date <= ? AND timetables.end_date >= ? and id IN (?)",@local_tzone_time.to_date,@local_tzone_time.to_date,timetable_ids])
      @timetable_entries = Hash.new { |l, k| l[k] = Hash.new(&l.default_proc) }
      @main_time_table_id = Hash.new { |l, k| l[k] = Hash.new(&l.default_proc) }
      
      unless @current.nil?
        @class_timings = TimeTableClassTiming.find_by_batch_id_and_timetable_id(@batch.try(:id),@current.try(:id)).try(:class_timing_set).class_timings.map(&:id)
        @entries=@current.timetable_entries.find(:all,:conditions=>{:batch_id=>@batch.id, :class_timing_id => @class_timings})
        @all_timetable_entries = @entries.select{|s| s.class_timing.is_deleted==false}
        @all_weekdays = @all_timetable_entries.collect(&:weekday_id).uniq.sort
        #@all_classtimings = @all_timetable_entries.collect(&:class_timing).uniq.sort!{|a,b| a.start_time <=> b.start_time}
        @all_classtimings = []
        
#        @classtimings_ids = @class_timings.map(&:class_timing_set_id)
        @all_classtimings_previous = ClassTiming.find(:all,:conditions=>["id IN (?)",@class_timings],:order=>"start_time ASC")
        @all_classtimings_previous.each do |ct|
          @class_timings_data = ClassTiming.find(:all,:conditions=>["id	= ?",ct.id])
          @all_classtimings << @class_timings_data
        end
        @all_teachers = @all_timetable_entries.collect(&:employee).uniq
        if Configuration.find_by_config_key('ViewSmallRoutine').present? and Configuration.find_by_config_key('ViewSmallRoutine').config_value=="1"
       
          @temp_timing = {}
          @all_timing_id = {}
          @i = 0
          @new_class_timing = []
          @running_id = 0
          @checking_max = 0
          @all_classtimings.each do |ct|

            if @prev_max.blank?
              @prev_max = ct[0].end_time.strftime("%H%M")
              @prev_max_main = ct[0].end_time
            end

            if @prev_min.blank?
              @prev_min = ct[0].start_time.strftime("%H%M")
              @prev_min_main = ct[0].start_time
            end


            if @checking_max.to_i != ct[0].start_time.strftime("%H%M").to_i and @i != 0
              if ct[0].end_time.strftime("%H%M").to_i > @prev_max.to_i
                @prev_max = ct[0].end_time.strftime("%H%M")
                @prev_max_main = ct[0].end_time
              end
              if ct[0].start_time.strftime("%H%M").to_i < @prev_min.to_i
                @prev_min = ct[0].start_time.strftime("%H%M")
                @prev_min_main = ct[0].start_time
              end
              @temp_timing.start_time = @prev_min_main
              @temp_timing.end_time = @prev_max_main
              @all_timing_id[@running_id] << ct[0].id
            else 
              if @i != 0
                @new_class_timing << @temp_timing
                @prev_max = ct[0].end_time.strftime("%H%M")
                @prev_min = ct[0].start_time.strftime("%H%M")
                @prev_max_main = ct[0].end_time
                @prev_min_main = ct[0].start_time
              end
              @checking_max = ct[0].end_time.strftime("%H%M").to_i
              @temp_timing = ct[0]
              @running_id = ct[0].id
              @all_timing_id[ct[0].id]=[]
            end

            j = @i+1
            if j == @all_classtimings.size
              @new_class_timing << @temp_timing
            end


            @i = @i+1
          end


          @all_classtimings = @new_class_timing
          
          @all_timetable_entries.each do |tte|
             if !@all_timing_id.blank? and !@all_timing_id[tte.class_timing_id].blank?
              @timetable_entries[tte.weekday_id][tte.class_timing_id]=tte
              @main_time_table_id[tte.weekday_id][tte.class_timing_id]=tte.class_timing_id
            else
              new_ct = tte.class_timing_id
              if !@all_timing_id.blank?
                @all_timing_id.each do |k,at|
                    if at.include?(tte.class_timing_id)
                      new_ct = k
                    end
                end
              end
              @timetable_entries[tte.weekday_id][new_ct]=tte
              @main_time_table_id[tte.weekday_id][new_ct]=tte.class_timing_id
            end 
          end
        else
          @all_timetable_entries.each do |tt|
            @timetable_entries[tt.weekday_id][tt.class_timing_id] = tt
          end
        end  
      end
    else
      flash[:notice] = t('timetable_not_set')
      redirect_to :controller => 'user', :action => 'dashboard'
    end
  end

  def update_student_tt
    @student = Student.find(params[:id])
    @batch=@student.batch
    @all_timetable_entries = Array.new
    if params[:timetable_id].nil?
      @current=Timetable.find(:first,:conditions=>["timetables.start_date <= ? AND timetables.end_date >= ?",@local_tzone_time.to_date,@local_tzone_time.to_date])
    else
      if params[:timetable_id]==""
        render :update do |page|
          page.replace_html "box", :text => ""
        end
        return
      else
        @current=Timetable.find(params[:timetable_id])
      end
    end
    @timetable_entries = Hash.new { |l, k| l[k] = Hash.new(&l.default_proc) }
    unless @current.nil?
      ttct = TimeTableClassTiming.find_by_batch_id_and_timetable_id(@batch.try(:id),@current.try(:id))
      if ttct.present?
        @class_timings = ttct.try(:class_timing_set).class_timings.map(&:id)
        @entries=@current.timetable_entries.find(:all,:conditions=>{:batch_id=>@batch.id, :class_timing_id => @class_timings})
        @all_timetable_entries = @entries.select{|s| s.class_timing.is_deleted==false}
        @all_weekdays = @all_timetable_entries.collect(&:weekday_id).uniq.sort
        @all_classtimings = @all_timetable_entries.collect(&:class_timing).uniq.sort!{|a,b| a.start_time <=> b.start_time}
        @all_teachers = @all_timetable_entries.collect(&:employee).uniq
        @all_timetable_entries.each do |tt|
          @timetable_entries[tt.weekday_id][tt.class_timing_id] = tt
        end
      end
    end

    render :update do |page|
      page.replace_html "box", :partial => "student_timetable"
    end
  end

  def weekdays
    @batches = Batch.active
  end

  def timetable_pdf
    @batch = Batch.find(params[:course_id])
    @master = Timetable.find(params[:timetable_id])
    @timetable = TimetableEntry.find_all_by_batch_id_and_timetable_id(@batch.id,params[:timetable_id])
    timetable_class_timing = TimeTableClassTiming.find_by_batch_id_and_timetable_id(@batch.id,@master.id)
    @class_timings = TimeTableClassTiming.find_by_batch_id_and_timetable_id(@batch.try(:id),@master.id).try(:class_timing_set).class_timings.map(&:id)
    #@class_timing = timetable_class_timing.nil? ? Array.new : timetable_class_timing.class_timing_set.class_timings.timetable_timings
    @class_timing = []
    @class_timings.each do |ct|
      @class_timings_data = ClassTiming.find(:all,:conditions=>["id	= ?",ct])
      @class_timing << @class_timings_data
    end     
    @subjects = Subject.find_all_by_batch_id(@batch.id)
    @weekday = @batch.weekday_set.nil? ? WeekdaySet.first.weekday_ids : @batch.weekday_set.weekday_ids
    render :pdf => 'timetable_pdf',
        :orientation => 'Landscape', :zoom => 1.00,
        :margin => {    :top=> 0,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def student_timetable_pdf
    @batch = Batch.find(params[:course_id])
    @master = Timetable.find(params[:timetable_id])
    @timetable = TimetableEntry.find_all_by_batch_id_and_timetable_id(@batch.id,params[:timetable_id])
    timetable_class_timing = TimeTableClassTiming.find_by_batch_id_and_timetable_id(@batch.id,@master.id)
    @class_timings = TimeTableClassTiming.find_by_batch_id_and_timetable_id(@batch.try(:id),@master.id).try(:class_timing_set).class_timings.map(&:id)
    #@class_timing = timetable_class_timing.nil? ? Array.new : timetable_class_timing.class_timing_set.class_timings.timetable_timings
    @class_timing = []
    @class_timings.each do |ct|
      @class_timings_data = ClassTiming.find(:all,:conditions=>["id	= ?",ct])
      @class_timing << @class_timings_data
    end  
    @subjects = Subject.find_all_by_batch_id(@batch.id)
    @weekday = @batch.weekday_set.nil? ? WeekdaySet.first.weekday_ids : @batch.weekday_set.weekday_ids
    render :pdf=>'timetable_pdf',:margin => {    :top=> 40,
      :bottom => 20,
      :left=> 10,
      :right => 10}
  end

  def work_allotment
    @employees = Employee.all(:include=>[:employee_grade,:employees_subjects, :user])
    @employees.reject!{|e| (e.user.nil? or e.user.admin?)}
    @emp_subs = []
    @employees.map{|employee| (employee[:total_time] = ((employee.max_hours_week).to_i))}
    if request.post?
      if params[:employee_subjects].present?
        params[:employee_subjects].delete_blank
        success,@error_obj = EmployeesSubject.allot_work(params[:employee_subjects])
        if success
          flash[:notice] = t('work_allotment_success')
        else
          flash[:notice] = t('updated_with_errors')
        end
      else
        flash[:notice] = t('updated_with_errors')
      end
    end
    @batches = Batch.active :include=>[{:subjects=>:employees},:course]
    @subjects = @batches.collect(&:subjects).flatten
  end

  def timetable
    @config = Configuration.available_modules
    @batches = Batch.active
    unless params[:next].nil?
      @today = params[:next].to_date
      render (:update) do |page|
        page.replace_html "timetable", :partial => 'table'
      end
    else
      @today = @local_tzone_time.to_date
    end
  end
  
  
end
class Hash
  def delete_blank
    delete_if{|k, v| v.empty? or v.instance_of?(Hash) && v.delete_blank.empty?}
  end
end