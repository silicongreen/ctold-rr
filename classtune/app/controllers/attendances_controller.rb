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

class AttendancesController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only=>[:index]
  filter_access_to :all, :except=>[:index,:graph_code,:rollcall,:subjects,:show_report_student,:show_student,:class_report,:subject_report,:student_report,:show_report, :list_subject, :show, :new, :create, :edit,:update, :destroy,:subject_wise_register]
  filter_access_to [:index,:graph_code,:show_report_student,:rollcall,:show_student,:class_report,:subject_report,:student_report,:show_report, :list_subject, :show, :new, :create, :edit,:update, :destroy,:subject_wise_register], :attribute_check=>true, :load_method => lambda { current_user }
  before_filter :only_assigned_employee_allowed, :except => [:index,:graph_code,:show_report_student,:rollcall,:show_student,:class_report,:subject_report,:student_report,:show_report]
  before_filter :only_privileged_employee_allowed, :only => [:index,:graph_code,:show_report_student,:rollcall,:show_student,:class_report,:subject_report,:student_report,:show_report]
  before_filter :default_time_zone_present_time
  before_filter :check_status
  
  
  def subject_report
    @subjects = []
    @batches = []
    @batch_no = 0
    @date_today = @local_tzone_time.to_date
    if current_user.admin?
      @batches = Batch.active
    elsif @current_user.privileges.map{|p| p.name}.include?('StudentAttendanceRegister')
      @batches = Batch.active
    elsif @current_user.employee?
      @batches = @current_user.employee_record.batches
      @batches += @current_user.employee_record.subjects.collect{|b| b.batch}
      @batches += TimetableSwap.find_all_by_employee_id(@current_user.employee_record.try(:id)).map(&:subject).flatten.compact.map(&:batch)
      @batches = @batches.uniq unless @batches.empty
    end
    render :partial=>"subject_report"
  end
  
#  def subjects2
#    @subjects = []
#    if params[:batch_id].present?
#      @batch = Batch.find(params[:batch_id])
#      @subjects = @batch.subjects
#      if @current_user.employee? and @allow_access ==true and !@current_user.privileges.map{|m| m.name}.include?("StudentAttendanceRegister")
#        employee = @current_user.employee_record
#        if @batch.employee_id.to_i == employee.id
#          @subjects= @batch.subjects
#        else
#          subjects = Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{employee.try(:id)} AND batch_id = #{@batch.id} ")
#          swapped_subjects = Subject.find(:all, :joins => :timetable_swaps, :conditions => ["subjects.batch_id = ? AND timetable_swaps.employee_id = ?",params[:batch_id],employee.try(:id)])
#          @subjects = (subjects + swapped_subjects).compact.flatten.uniq
#        end
#      end 
#    end
#    render(:update) do |page|
#      page.replace_html 'subjects', :partial=> 'subjects2'
#    end
#  end
  
  def subjects3
    @subjects = []
    if params[:batch_id].present?
      @batch = Batch.find(params[:batch_id])
      @class_timing_id = []
      unless @batch.class_timing_set.blank?
        @class_timings = @batch.class_timing_set.class_timings
        unless @class_timings.blank?
          @class_timing_id = @class_timings.map(&:id)
        end
      end
      
      @subjects = @batch.subjects
      @current_timetable=Timetable.find(:first,:conditions=>["timetables.start_date <= ? AND timetables.end_date >= ?",@local_tzone_time.to_date,@local_tzone_time.to_date])
      
      
      unless @current_timetable.blank?
        @subjects = []
        @subject_names = []
        if @current_user.employee? and @allow_access ==true and !@current_user.privileges.map{|m| m.name}.include?("StudentAttendanceRegister")
          @employee = @current_user.employee_record
          @employee_subjects = @employee.subjects
          subjects = @employee_subjects.select{|sub| sub.elective_group_id.nil?}
          electives = @employee_subjects.select{|sub| sub.elective_group_id.present?}
          elective_subjects=electives.map{|x| x.elective_group.subjects.first}
          @entries=[]
          @entries += @current_timetable.timetable_entries.find(:all,:conditions=>{:batch_id=>@batch.id,:employee_id => @employee.id,:class_timing_id=>@class_timing_id})
          @entries += @current_timetable.timetable_entries.find(:all,:conditions=>{:batch_id=>@batch.id,:subject_id=>elective_subjects,:class_timing_id=>@class_timing_id})
          unless @entries.blank?
            @entries.each do |te|
              @timetable_subject = Subject.active.find_by_id(te.subject_id)
              unless @timetable_subject.blank?
                if @timetable_subject.elective_group_id.present?
                  @all_sub_elective = Subject.active.find_all_by_elective_group_id(@timetable_subject.elective_group_id)
                  unless @all_sub_elective.blank?
                    @all_sub_elective.each do |esub|
                      if @employee_subjects.include?(esub) and !@subjects.include?(esub) and !@subject_names.include?(esub.name)
                        @subjects << esub
                        @subject_names << esub.name
                      end  
                    end
                    
                  end
                else
                  if !@subjects.include?(@timetable_subject) and !@subject_names.include?(@timetable_subject.name)
                    @subjects << @timetable_subject
                    @subject_names << @timetable_subject.name
                  end
                end
              end
            end
          end
            

        else
          @entries=[]
          @entries += @current_timetable.timetable_entries.find(:all,:conditions=>{:batch_id=>@batch.id,:class_timing_id=>@class_timing_id})
          
         
          unless @entries.blank?
            @entries.each do |te|
              @timetable_subject = Subject.active.find_by_id(te.subject_id)
              unless @timetable_subject.blank?
                if @timetable_subject.elective_group_id.present?
                  @all_sub_elective = Subject.active.find_all_by_elective_group_id(@timetable_subject.elective_group_id)
                  unless @all_sub_elective.blank?
                    @all_sub_elective.each do |esub|
                      if !@subjects.include?(esub) and !@subject_names.include?(esub.name)
                        @subjects << esub
                        @subject_names << esub.name
                      end 
                    end
                  end
                else
                  if !@subjects.include?(@timetable_subject) and !@subject_names.include?(@timetable_subject.name)
                    @subjects << @timetable_subject
                    @subject_names << @timetable_subject.name
                  end 
                end
              end
            end
          end

         end
        
          
      end 
      @subjects.sort! { |a, b|  a.name <=> b.name }
        
    end
   
  render(:update) do |page|
    page.replace_html 'subjects', :partial=> 'subjects3'
  end
end
  
  
  def subjects2
    @subjects = []
    if params[:batch_id].present?
      @batch = Batch.find(params[:batch_id])
      @class_timing_id = []
      unless @batch.class_timing_set.blank?
        @class_timings = @batch.class_timing_set.class_timings
        unless @class_timings.blank?
          @class_timing_id = @class_timings.map(&:id)
        end
      end
      
      @subjects = @batch.subjects
      @current_timetable=Timetable.find(:first,:conditions=>["timetables.start_date <= ? AND timetables.end_date >= ?",@local_tzone_time.to_date,@local_tzone_time.to_date])
      
      
      unless @current_timetable.blank?
        @subjects = []
        if @current_user.employee? and @allow_access ==true and !@current_user.privileges.map{|m| m.name}.include?("StudentAttendanceRegister")
          @employee = @current_user.employee_record
          @employee_subjects = @employee.subjects
          subjects = @employee_subjects.select{|sub| sub.elective_group_id.nil?}
          electives = @employee_subjects.select{|sub| sub.elective_group_id.present?}
          elective_subjects=electives.map{|x| x.elective_group.subjects.first}
          @entries=[]
          @entries += @current_timetable.timetable_entries.find(:all,:conditions=>{:batch_id=>@batch.id,:employee_id => @employee.id,:class_timing_id=>@class_timing_id})
          @entries += @current_timetable.timetable_entries.find(:all,:conditions=>{:batch_id=>@batch.id,:subject_id=>elective_subjects,:class_timing_id=>@class_timing_id})
          unless @entries.blank?
            @entries.each do |te|
              @timetable_subject = Subject.active.find_by_id(te.subject_id)
              unless @timetable_subject.blank?
                if @timetable_subject.elective_group_id.present?
                  @all_sub_elective = Subject.active.find_all_by_elective_group_id(@timetable_subject.elective_group_id)
                  unless @all_sub_elective.blank?
                    @all_sub_elective.each do |esub|
                      if @employee_subjects.include?(esub) and !@subjects.include?(esub)
                        @subjects << esub
                      end  
                    end
                    
                  end
                else
                  unless @subjects.include?(@timetable_subject)
                    @subjects << @timetable_subject
                  end
                end
              end
            end
          end
            

        else
          @entries=[]
          @entries += @current_timetable.timetable_entries.find(:all,:conditions=>{:batch_id=>@batch.id,:class_timing_id=>@class_timing_id})
          
         
          unless @entries.blank?
            @entries.each do |te|
              @timetable_subject = Subject.active.find_by_id(te.subject_id)
              unless @timetable_subject.blank?
                if @timetable_subject.elective_group_id.present?
                  @all_sub_elective = Subject.active.find_all_by_elective_group_id(@timetable_subject.elective_group_id)
                  unless @all_sub_elective.blank?
                    @all_sub_elective.each do |esub|
                      unless @subjects.include?(esub)
                        @subjects << esub
                      end
                    end
                  end
                else
                  unless @subjects.include?(@timetable_subject)
                    @subjects << @timetable_subject  
                  end 
                end
              end
            end
          end

         end
        
          
      end 
      @subjects.sort! { |a, b|  a.name <=> b.name }
        
    end
   
  render(:update) do |page|
    page.replace_html 'subjects', :partial=> 'subjects2'
  end
end
  
  
  
  def get_subject_report_pdf
    unless params[:subject_id].nil?
      if !params[:subject_id].blank?
        if !params[:date].blank? and !params[:date2].blank?
          @date1 = params[:date].to_date.strftime("%Y-%m-%d")
          @date2 = params[:date2].to_date.strftime("%Y-%m-%d")
          get_subject_report_name(params[:subject_id],@date1,@date2)
        else
          get_subject_report_name(params[:subject_id])
        end
        @subject = Subject.find(params[:subject_id])
        @batch = @subject.batch
        if @student_response['status']['code'].to_i == 200
          @data = @student_response['data']
        end
      end
    end
    
    render :pdf => 'get_subject_report_pdf',
      :margin => {:top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def get_subject_report_all_name 
    unless params[:subject_id].nil? or  params[:date].nil?
      if !params[:subject_id].blank? and !params[:date].blank? and params[:date2].blank?
        @date = params[:date].to_date.strftime("%Y-%m-%d")
        get_subject_report_date_name(params[:subject_id],@date)
        if @student_response['status']['code'].to_i == 200
          @report_data = @student_response['data']
        end
        
        get_subject_attendence_student_name(params[:subject_id],@date)
        @subject_id = params[:subject_id]
        if @student_response['status']['code'].to_i == 200
          @students = @student_response['data']['students']
          @register_id = @student_response['data']['register']
        end
        
        respond_to do |format|
          format.js { render :action => 'get_subject_report_date' }
        end
      else
        if !params[:date].blank? and !params[:date2].blank?
          @date1 = params[:date].to_date.strftime("%Y-%m-%d")
          @date2 = params[:date2].to_date.strftime("%Y-%m-%d")
          get_subject_report_name(params[:subject_id],@date1,@date2)
        else
          get_subject_report_name(params[:subject_id])
        end
        @subject = Subject.find(params[:subject_id])
        if @student_response['status']['code'].to_i == 200
          @data = @student_response['data']
        end
        respond_to do |format|
          format.js { render :action => 'get_subject_report' }
        end
      end  
    end
  end
  
  def get_subject_report_all 
    unless params[:subject_id].nil? or  params[:date].nil?
      if !params[:subject_id].blank? and !params[:date].blank? 
        @date = params[:date].to_date.strftime("%Y-%m-%d")
        get_subject_report_date(params[:subject_id],@date)
        if @student_response['status']['code'].to_i == 200
          @report_data = @student_response['data']
        end
        
        get_subject_attendence_student(params[:subject_id],@date)
        @subject_id = params[:subject_id]
        if @student_response['status']['code'].to_i == 200
          @students = @student_response['data']['students']
          @register_id = @student_response['data']['register']
        end
        
        respond_to do |format|
          format.js { render :action => 'get_subject_report_date' }
        end
      else
        get_subject_report(params[:subject_id])
        @subject = Subject.find(params[:subject_id])
        if @student_response['status']['code'].to_i == 200
          @data = @student_response['data']
        end
        respond_to do |format|
          format.js { render :action => 'get_subject_report' }
        end
      end  
    end
  end
  
  def subjects
    @subjects = []
    if params[:batch_id].present?
      @batch = Batch.find(params[:batch_id])
      @class_timing_id = []
      unless @batch.class_timing_set.blank?
        @class_timings = @batch.class_timing_set.class_timings
        unless @class_timings.blank?
          @class_timing_id = @class_timings.map(&:id)
        end
      end
      
      @subjects = @batch.subjects
      @current_timetable=Timetable.find(:first,:conditions=>["timetables.start_date <= ? AND timetables.end_date >= ?",@local_tzone_time.to_date,@local_tzone_time.to_date])
      @date_to_use = params[:date_to_use].to_date
      @weekday_id = @date_to_use.strftime("%w")
      unless @current_timetable.blank?
        @subjects = []
        if @current_user.employee?
          @employee = @current_user.employee_record
          @employee_subjects = @employee.subjects
          subjects = @employee_subjects.select{|sub| sub.elective_group_id.nil?}
          electives = @employee_subjects.select{|sub| sub.elective_group_id.present?}
          elective_subjects=electives.map{|x| x.elective_group.subjects}
          @entries=[]
          @entries += @current_timetable.timetable_entries.find(:all,:conditions=>{:batch_id=>@batch.id,:weekday_id=>@weekday_id.to_i,:employee_id => @employee.id,:class_timing_id=>@class_timing_id})
          @entries += @current_timetable.timetable_entries.find(:all,:conditions=>{:batch_id=>@batch.id,:subject_id=>elective_subjects,:weekday_id=>@weekday_id.to_i,:class_timing_id=>@class_timing_id})
          unless @entries.blank?
            @entries.each do |te|
              @timetable_subject = Subject.active.find_by_id(te.subject_id)
              unless @timetable_subject.blank?
                if @timetable_subject.elective_group_id.present?
                  @all_sub_elective = Subject.active.find_all_by_elective_group_id(@timetable_subject.elective_group_id)
                  unless @all_sub_elective.blank?
                    @all_sub_elective.each do |esub|
                      if !@employee_subjects.include?(esub) && !@subjects.include?(esub) && esub.elective_group_id.present?
                        all_elective_sub = esub.elective_group.subjects
                        unless all_elective_sub.blank?
                          all_elective_sub.each do |sube|
                            if @employee_subjects.include?(sube)
                              @subjects << sube
                              break
                            end
                          end
                        end
                      elsif @employee_subjects.include?(esub) && !@subjects.include?(esub)
                        @subjects << esub
                      end  
                    end
                    
                  end
                else
                  @subjects << @timetable_subject
                    
                end
              end
            end
          end
            

        else
          @entries=[]
          @entries += @current_timetable.timetable_entries.find(:all,:conditions=>{:batch_id=>@batch.id,:weekday_id=>@weekday_id.to_i,:class_timing_id=>@class_timing_id})
          
         
          unless @entries.blank?
            @entries.each do |te|
              @timetable_subject = Subject.active.find_by_id(te.subject_id)
              unless @timetable_subject.blank?
                if @timetable_subject.elective_group_id.present?
                  @all_sub_elective = Subject.active.find_all_by_elective_group_id(@timetable_subject.elective_group_id)
                  unless @all_sub_elective.blank?
                    @all_sub_elective.each do |esub|
                       @subjects << esub
                    end
                  end
                else
                   @subjects << @timetable_subject  
                end
              end
            end
          end

         end
        
          
      end  
        
    end
   
  render(:update) do |page|
    page.replace_html 'subjects', :partial=> 'subjects'
  end
end
  
def save_attendance_subject
  now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
  if params[:attandence_date].nil? || params[:attandence_date].empty?
    @date_to_use = @local_tzone_time.to_date
  elsif current_user.admin?
    @date_to_use = params[:attandence_date].to_date
  else
    @date_to_use = @local_tzone_time.to_date
  end 
  add_attendence_subject(params[:subject_id],@date_to_use,params[:student_id],params[:late])
  render :text=>"success"
    
end
  
def get_subject_student
  now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
  if params[:date_to_use].nil? || params[:date_to_use].empty?
    @date_to_use = @local_tzone_time.to_date
  elsif current_user.admin?
    @date_to_use = params[:date_to_use].to_date
  else
    @date_to_use = @local_tzone_time.to_date
  end  
  @students = []
  if params[:subject_id].present?
    get_subject_attendence_student(params[:subject_id],@date_to_use)
    @subject_id = params[:subject_id]
    if @student_response['status']['code'].to_i == 200
      @students = @student_response['data']['students']
      @register_id = @student_response['data']['register']
    end
  end
  respond_to do |format|
    format.js { render :action => 'roll_sub' }
  end
end
  
def rollcall
  @subjects = []
  @batches = []
  @batch_no = 0
  @date_today = @local_tzone_time.to_date
  if current_user.admin?
    @batches = Batch.active
  elsif @current_user.privileges.map{|p| p.name}.include?('StudentAttendanceRegister')
    @batches = Batch.active
  elsif @current_user.employee?
    @batches = @current_user.employee_record.batches
    @batches += @current_user.employee_record.subjects.collect{|b| b.batch}
    @batches += TimetableSwap.find_all_by_employee_id(@current_user.employee_record.try(:id)).map(&:subject).flatten.compact.map(&:batch)
    @batches = @batches.uniq unless @batches.empty
  end
  render :partial=>"rollcall"
end
  
def show_report_student 
  unless params[:batch_id].nil? or  params[:student_id].nil?
    if params[:start_date] && params[:end_date] 
      get_report_year(params[:batch_id],params[:student_id],params[:start_date],params[:end_date])
    else
      get_report_year(params[:batch_id],params[:student_id])
    end  
    if @attendence_data['status']['code'].to_i == 200
      @absent = @attendence_data['data']['absent']
      @late = @attendence_data['data']['late']
      @leave = @attendence_data['data']['leave']
      @present = @attendence_data['data']['total']-@absent-@leave
      @graph = open_flash_chart_object('100%',389,"/attendances/graph_code?absent=#{@absent}&late=#{@late}&leave=#{@leave}&present=#{@present}")
    end
  end
    
    
     
  respond_to do |format|
    format.js { render :action => 'show_report_student' }
  end
end
  
def graph_code
    
  title = Title.new("")
      
  absent = params[:absent].to_i
  late = params[:late].to_i*0.5
      
  leave = params[:leave].to_i
      
  present = params[:present].to_i
  present = present-late
      
  main_late =late*2
      
  values_main = [PieValue.new(absent,"Absent"),PieValue.new(late,"Late"), PieValue.new(leave,"Leave"),PieValue.new(present,"Present")]
      
  colours_main = ["#EC1300", "#FFB800", "#922CB0", "#8DC63F"]
  if present ==0
    colours_main.delete_at(3)
    values_main.delete_at(3)
  end
  if leave ==0
    colours_main.delete_at(2)
    values_main.delete_at(2)
  end
  if late ==0
    colours_main.delete_at(1)
    values_main.delete_at(1)
  end
  if absent ==0
    colours_main.delete_at(0)
    values_main.delete_at(0)
  end
     
  pie = Pie.new
  pie.start_angle = 35
  pie.animate = true
  pie.tooltip = '#percent# of 100%'
  pie.colours = colours_main
  pie.values  = values_main

      
  chart = OpenFlashChart.new
  chart.title = title
  chart.add_element(pie)

  chart.x_axis = nil

  render :text => chart.render
    
    
end
  
def student_report
  @classes = []
  @batches = []
  @batch_no = 0
  @course_name = ""
  @courses = []
  @config = Configuration.find_by_config_key('StudentAttendanceType')
  @date_today = @local_tzone_time.to_date
  if current_user.admin?
    @batches = Batch.active
  elsif @current_user.privileges.map{|p| p.name}.include?('StudentAttendanceRegister')
    @batches = Batch.active
  elsif @current_user.employee?
    if @config.config_value == 'Daily'
      @batches = @current_user.employee_record.batches
    else
      @batches = @current_user.employee_record.batches
      @batches += @current_user.employee_record.subjects.collect{|b| b.batch}
      @batches += TimetableSwap.find_all_by_employee_id(@current_user.employee_record.try(:id)).map(&:subject).flatten.compact.map(&:batch)
      @batches = @batches.uniq unless @batches.empty?
    end
  end
  render :partial=>"student_report"
end
  
def show_student   
  if params[:batch_id].nil?
    batch_name = ""
    if Batch.active.find(:all, :group => "name").length > 1
      unless params[:student].nil?
        unless params[:student][:batch_name].nil?
          batch_id = params[:student][:batch_name]
          batches_data = Batch.find_by_id(batch_id)
          batch_name = batches_data.name
        end
      end
    else
      batches = Batch.active
      batch_name = batches[0].name
    end
    course_id = 0
    unless params[:course_id].nil?
      course_id = params[:course_id]
    end
    if course_id == 0
      unless params[:student].nil?
        unless params[:student][:section].nil?
          course_id = params[:student][:section]
        end
      end
    end

    if batch_name.length == 0
      @batch_data = Rails.cache.fetch("batch_data_#{course_id}"){
        batches = Batch.find_by_course_id(course_id)
        batches
      }
    else
      @batch_data = Rails.cache.fetch("batch_data_#{course_id}_#{batch_name.parameterize("_")}"){
        batches = Batch.find_by_course_id_and_name(course_id, batch_name)
        batches
      }
    end 
    params[:batch_id] = 0
    unless @batch_data.nil?
      params[:batch_id] = @batch_data.id 
    end
  else
    batch = Batch.find(params[:batch_id])
    params[:batch_id] = batch.id
  end
  @all_students = Student.paginate(:conditions=>{:batch_id=>params[:batch_id]},  :page => params[:page], :per_page => 10)

  render :partial => "show_student"

end
  
def class_report
  @classes = []
  @batches = []
  @batch_no = 0
  @course_name = ""
  @courses = []
  @config = Configuration.find_by_config_key('StudentAttendanceType')
  @date_today = @local_tzone_time.to_date
  if current_user.admin?
    @batches = Batch.active
  elsif @current_user.privileges.map{|p| p.name}.include?('StudentAttendanceRegister')
    @batches = Batch.active
  elsif @current_user.employee?
    if @config.config_value == 'Daily'
      @batches = @current_user.employee_record.batches
    else
      @batches = @current_user.employee_record.batches
      @batches += @current_user.employee_record.subjects.collect{|b| b.batch}
      @batches += TimetableSwap.find_all_by_employee_id(@current_user.employee_record.try(:id)).map(&:subject).flatten.compact.map(&:batch)
      @batches = @batches.uniq unless @batches.empty?
    end
  end
  render :partial=>"class_report"
end
  
def show_report   
  if !params[:batch_id].nil? and  !params[:date_report].nil? and !params[:batch_id].blank?
    get_report(params[:batch_id], params[:date_report])
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
  elsif !params[:date_report].nil?
    get_report_full(params[:date_report])
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
  end
  respond_to do |format|
    format.js { render :action => 'report_data' }
  end
end
  
def index
  @classes = []
  @batches = []
  @batch_no = 0
  @course_name = ""
  @courses = []
  @config = Configuration.find_by_config_key('StudentAttendanceType')
  @date_today = @local_tzone_time.to_date
  if current_user.admin?
    @batches = Batch.active
  elsif @current_user.privileges.map{|p| p.name}.include?('StudentAttendanceRegister')
    @batches = Batch.active
  elsif @current_user.employee?
    if @config.config_value == 'Daily'
      @batches = @current_user.employee_record.batches
    else
      @batches = @current_user.employee_record.batches
      @batches += @current_user.employee_record.subjects.collect{|b| b.batch}
      @batches += TimetableSwap.find_all_by_employee_id(@current_user.employee_record.try(:id)).map(&:subject).flatten.compact.map(&:batch)
      @batches = @batches.uniq unless @batches.empty?
    end
  end
end

def list_subject    
  if params[:batch_id].nil?
    batch_name = ""
    if Batch.active.find(:all, :group => "name").length > 1
      unless params[:student].nil?
        unless params[:student][:batch_name].nil?
          batch_id = params[:student][:batch_name]
          batches_data = Batch.find_by_id(batch_id)
          batch_name = batches_data.name
        end
      end
    else
      batches = Batch.active
      batch_name = batches[0].name
    end
    course_id = 0
    unless params[:course_id].nil?
      course_id = params[:course_id]
    end
    if course_id == 0
      unless params[:student].nil?
        unless params[:student][:section].nil?
          course_id = params[:student][:section]
        end
      end
    end

    if batch_name.length == 0
      @batch_data = Rails.cache.fetch("batch_data_#{course_id}"){
        batches = Batch.find_by_course_id(course_id)
        batches
      }
    else
      @batch_data = Rails.cache.fetch("batch_data_#{course_id}_#{batch_name.parameterize("_")}"){
        batches = Batch.find_by_course_id_and_name(course_id, batch_name)
        batches
      }
    end 
    params[:batch_id] = 0
    unless @batch_data.nil?
      params[:batch_id] = @batch_data.id 
    end
  else
    batch = Batch.find(params[:batch_id])
    params[:batch_id] = batch.id
  end
    
  if params[:batch_id].present?
    @batch = Batch.find(params[:batch_id])
    @subjects = @batch.subjects
    if @current_user.employee? and @allow_access ==true and !@current_user.privileges.map{|m| m.name}.include?("StudentAttendanceRegister")
      employee = @current_user.employee_record
      if @batch.employee_id.to_i == employee.id
        @subjects= @batch.subjects
      else
        subjects = Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{employee.try(:id)} AND batch_id = #{@batch.id} ")
        swapped_subjects = Subject.find(:all, :joins => :timetable_swaps, :conditions => ["subjects.batch_id = ? AND timetable_swaps.employee_id = ?",params[:batch_id],employee.try(:id)])
        @subjects = (subjects + swapped_subjects).compact.flatten.uniq
      end
    end
    render(:update) do |page|
      page.replace_html 'subjects', :partial=> 'subjects'
    end
  else
    render(:update) do |page|
      page.replace_html "register", :text => ""
      page.replace_html "subjects", :text => ""
    end
  end
end
  
def show 
  now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
  if params[:attandence_date].nil? || params[:attandence_date].empty?
    @date_to_use = @local_tzone_time.to_date
  elsif current_user.admin?
    @date_to_use = params[:attandence_date].to_date
  else
    @date_to_use = @local_tzone_time.to_date
  end  
  if params[:batch_id].nil?
    batch_name = ""
    if Batch.active.find(:all, :group => "name").length > 1
      unless params[:student].nil?
        unless params[:student][:batch_name].nil?
          batch_id = params[:student][:batch_name]
          batches_data = Batch.find_by_id(batch_id)
          batch_name = batches_data.name
        end
      end
    else
      batches = Batch.active
      batch_name = batches[0].name
    end
    course_id = 0
    unless params[:course_id].nil?
      course_id = params[:course_id]
    end
    if course_id == 0
      unless params[:student].nil?
        unless params[:student][:section].nil?
          course_id = params[:student][:section]
        end
      end
    end

    if batch_name.length == 0
      @batch_data = Rails.cache.fetch("batch_data_#{course_id}"){
        batches = Batch.find_by_course_id(course_id)
        batches
      }
    else
      @batch_data = Rails.cache.fetch("batch_data_#{course_id}_#{batch_name.parameterize("_")}"){
        batches = Batch.find_by_course_id_and_name(course_id, batch_name)
        batches
      }
    end 
    params[:batch_id] = 0
    unless @batch_data.nil?
      params[:batch_id] = @batch_data.id 
    end
  else
    batch = Batch.find(params[:batch_id])
    params[:batch_id] = batch.id
  end
  unless params[:student_id].nil?
    unless params[:status_change].nil?
       
      if params[:status_change] == "0"
        add_attendence_student(params[:batch_id],@date_to_use,params[:student_id],0,0)
      elsif params[:status_change] == "2"
        add_attendence_student(params[:batch_id],@date_to_use,params[:student_id],1,0)
      elsif params[:status_change] == "3" or params[:status_change] == "4"
        leave_approve_deny(params[:leave_id], params[:student_id], params[:status_change])
      elsif params[:status_change] == "1"
        add_attendence_student(params[:batch_id],@date_to_use,params[:student_id],0,1)
      elsif params[:status_change] == "5"
        fattendence = ForceAttendence.find_by_student_id_and_date(params[:student_id],@date_to_use)
        if fattendence.nil?
          fatttendencenew = ForceAttendence.new
          fatttendencenew.student_id = params[:student_id]
          fatttendencenew.batch_id = params[:batch_id]
          fatttendencenew.date = @date_to_use
          fatttendencenew.created_at = now
          fatttendencenew.updated_at = now
          fatttendencenew.save
        else
          fattendence.destroy
        end   
      end
        
    end
  end
  get_attendence_student(params[:batch_id],@date_to_use)
  @students = []
  if @student_response['status']['code'].to_i == 200
    @students = @student_response['data']['batch_attendence']
    allfattendence = ForceAttendence.find_all_by_batch_id_and_date(params[:batch_id],@date_to_use)
    @stdids = allfattendence.map(&:student_id)
  end
    
  respond_to do |format|
    format.js { render :action => 'roll' }
  end
end
  
def immediate_leave 
    
  if params[:attandence_date].nil? || params[:attandence_date].empty?
    @date_to_use = @local_tzone_time.to_date
  elsif current_user.admin?
    @date_to_use = params[:attandence_date].to_date
  else
    @date_to_use = @local_tzone_time.to_date
  end
    
  if params[:batch_id].nil?
    batch_name = ""
    if Batch.active.find(:all, :group => "name").length > 1
      unless params[:student].nil?
        unless params[:student][:batch_name].nil?
          batch_id = params[:student][:batch_name]
          batches_data = Batch.find_by_id(batch_id)
          batch_name = batches_data.name
        end
      end
    else
      batches = Batch.active
      batch_name = batches[0].name
    end
    course_id = 0
    unless params[:course_id].nil?
      course_id = params[:course_id]
    end
    if course_id == 0
      unless params[:student].nil?
        unless params[:student][:section].nil?
          course_id = params[:student][:section]
        end
      end
    end

    if batch_name.length == 0
      @batch_data = Rails.cache.fetch("batch_data_#{course_id}"){
        batches = Batch.find_by_course_id(course_id)
        batches
      }
    else
      @batch_data = Rails.cache.fetch("batch_data_#{course_id}_#{batch_name.parameterize("_")}"){
        batches = Batch.find_by_course_id_and_name(course_id, batch_name)
        batches
      }
    end 
    params[:batch_id] = 0
    unless @batch_data.nil?
      params[:batch_id] = @batch_data.id 
    end
  else
    batch = Batch.find(params[:batch_id])
    params[:batch_id] = batch.id
  end
    
  unless params[:student_id].nil?
    params[:status_change] == "3"
  end
    
  immidiate_leave(params[:student_id], @date_to_use, @date_to_use)
    
  leave_id = 0
  if @immidiate_leave_response['status']['code'].to_i == 200
    leave_id = @immidiate_leave_response['data']['leave_id']
    leave_approve_deny(leave_id, params[:student_id], params[:status_change])
  end
    
  get_attendence_student(params[:batch_id],@date_to_use)
  @students = []
  if @student_response['status']['code'].to_i == 200
    @students = @student_response['data']['batch_attendence']
  end
    
  respond_to do |format|
    format.js { render :action => 'roll' }
  end
end
  
def show_before    
  if params[:batch_id].nil?
    batch_name = ""
    if Batch.active.find(:all, :group => "name").length > 1
      unless params[:student].nil?
        unless params[:student][:batch_name].nil?
          batch_id = params[:student][:batch_name]
          batches_data = Batch.find_by_id(batch_id)
          batch_name = batches_data.name
        end
      end
    else
      batches = Batch.active
      batch_name = batches[0].name
    end
    course_id = 0
    unless params[:course_id].nil?
      course_id = params[:course_id]
    end
    if course_id == 0
      unless params[:student].nil?
        unless params[:student][:section].nil?
          course_id = params[:student][:section]
        end
      end
    end

    if batch_name.length == 0
      @batch_data = Rails.cache.fetch("batch_data_#{course_id}"){
        batches = Batch.find_by_course_id(course_id)
        batches
      }
    else
      @batch_data = Rails.cache.fetch("batch_data_#{course_id}_#{batch_name.parameterize("_")}"){
        batches = Batch.find_by_course_id_and_name(course_id, batch_name)
        batches
      }
    end 
      
    params[:batch_id] = 0
    unless @batch_data.nil?
      params[:batch_id] = @batch_data.id 
    end
  else
    batch = Batch.find(params[:batch_id])
    params[:batch_id] = batch.id
  end
    
  @config = Configuration.find_by_config_key('StudentAttendanceType')
  unless params[:next].nil?
    @today = params[:next].to_date
  else
    @today = @local_tzone_time.to_date
  end
    
  start_date = @today.beginning_of_month
  end_date = @today.end_of_month
    
  if @config.config_value == 'Daily'
    @batch = Batch.find(params[:batch_id])
    @students = Student.find_all_by_batch_id(@batch.id)      
    @dates=@batch.working_days(@today)
  else
    @sub =Subject.find params[:subject_id]
    @batch=Batch.find(@sub.batch_id)
    unless @sub.elective_group_id.nil?
      elective_student_ids = StudentsSubject.find_all_by_subject_id_and_batch_id(@sub.id,@sub.batch_id).map { |x| x.student_id }
      @students = Student.find_all_by_batch_id(@batch, :conditions=>"FIND_IN_SET(id,\"#{elective_student_ids.split.join(',')}\")")
    else
      @students = Student.find_all_by_batch_id(@batch)
    end
    @dates=Timetable.tte_for_range(@batch,@today,@sub)
    @dates_key=@dates.keys - @batch.holiday_event_dates
  end
  respond_to do |format|
    format.js { render :action => 'show' }
  end
end
def get_att_log
  student_id = params[:student_id]
  date = params[:date].to_date
  sub_id = 0
  unless params[:subject_id].blank?
    sub_id = params[:subject_id]
  end
  all_log = AttendanceLog.find_all_by_student_id_and_date_and_subject_id(student_id,date,0,:limit=>10,:order=>"updated_at DESC")
  data = []
  att_text = ["Absent","late","Present"]
  unless all_log.blank?
    all_log.each do |log|
     logdata = {:employee_name=>log.employee.full_name,:status=>att_text[log.attendance],:date=>I18n.l(log.created_at, :format=>'%d %b %Y %I:%M %p')}
     data << logdata 
    end
  end
  @data = JSON.generate(data)
  render :text =>@data
  
end

def subject_wise_register
  if params[:subject_id].present?
    @sub = Subject.find params[:subject_id]
    to_search = @sub.elective_group_id.nil? ? @sub : @sub.elective_group.subjects.active.first
    @batch = Batch.find(@sub.batch_id)
    @timetable = TimetableEntry.find(:all, :conditions => {:batch_id => to_search.batch_id, :subject_id => to_search.id})
    unless(@timetable.present? and @batch.present? and @batch.weekday_set_id.present? and @batch.class_timing_set_id.present?)
      render :update do |page|
        page.replace_html "register", :partial => "no_timetable"
        page.hide "loader"
      end
      return
    end      
    @today = params[:next].present? ? params[:next].to_date : @local_tzone_time.to_date
    unless @sub.elective_group_id.nil?
      elective_student_ids = StudentsSubject.find_all_by_subject_id_and_batch_id(@sub.id,@sub.batch_id).map { |x| x.student_id }
      @students = @batch.students.by_first_name.with_full_name_only.all(:conditions=>"FIND_IN_SET(id,\"#{elective_student_ids.split.join(',')}\")")
    else
      @students = @batch.students.by_first_name.with_full_name_only
    end
    subject_leaves = SubjectLeave.by_month_batch_subject(@today,@batch.id,@sub.id).group_by(&:student_id)
    @leaves = Hash.new
    @students.each do |student|
      @leaves[student.id] = Hash.new(false)
      unless subject_leaves[student.id].nil?
        subject_leaves[student.id].group_by(&:month_date).each do |m,mleave|
          @leaves[student.id]["#{m}"]={}
          mleave.group_by(&:class_timing_id).each do |ct,ctleave|
            ctleave.each do |leave|
              @leaves[student.id]["#{m}"][ct] = leave.id
            end
          end
        end
      end
    end
    @dates = Timetable.tte_for_range(@batch,@today,@sub,current_user.employee_record)
    @translated=Hash.new
    @translated['name']=t('name')
    (0..6).each do |i|
      @translated[Date::ABBR_DAYNAMES[i].to_s]=t(Date::ABBR_DAYNAMES[i].downcase)
    end
    (1..12).each do |i|
      @translated[Date::MONTHNAMES[i].to_s]=t(Date::MONTHNAMES[i].downcase)
    end
    respond_to do |fmt|
      fmt.json {render :json=>{'leaves'=>@leaves,'students'=>@students,'dates'=>@dates,'batch'=>@batch,'today'=>@today,'translated'=>@translated}}
    end
  else
    render :update do |page|
      page.replace_html "register", :text => ""
      page.hide "loader"
    end
    return
  end
end

def daily_register    
  @batch = Batch.find_by_id(params[:batch_id])
  @timetable = TimetableEntry.find(:all, :conditions => {:batch_id => @batch.try(:id)})
  if(@timetable.nil? or @batch.nil?)
    render :update do |page|
      page.replace_html "register", :partial => "no_timetable"
      page.hide "loader"
    end
    return
  end      
  @today = params[:next].present? ? params[:next].to_date : @local_tzone_time.to_date
  @students = @batch.students.by_first_name.with_full_name_only
  @leaves = Hash.new
  attendances = Attendance.by_month_and_batch(@today,params[:batch_id]).group_by(&:student_id)
  @students.each do |student|
    @leaves[student.id] = Hash.new(false)
    unless attendances[student.id].nil?
      attendances[student.id].each do |attendance|
        @leaves[student.id]["#{attendance.month_date}"] = attendance.id
      end
    end
  end
  #    @dates=((@batch.end_date.to_date > @today.end_of_month) ? (@today.beginning_of_month..@today.end_of_month) : (@today.beginning_of_month..@batch.end_date.to_date))
  @dates=@batch.working_days(@today)
  @holidays = []
  @translated=Hash.new
  @translated['name']=t('name')
  (0..6).each do |i|
    @translated[Date::ABBR_DAYNAMES[i].to_s]=t(Date::ABBR_DAYNAMES[i].downcase)
  end
  (1..12).each do |i|
    @translated[Date::MONTHNAMES[i].to_s]=t(Date::MONTHNAMES[i].downcase)
  end
  respond_to do |fmt|
    fmt.json {render :json=>{'leaves'=>@leaves,'students'=>@students,'dates'=>@dates,'holidays'=>@holidays,'batch'=>@batch,'today'=>@today, 'translated'=>@translated}}
    #      format.js { render :action => 'show' }
  end
end
  
def new
  @config = Configuration.find_by_config_key('StudentAttendanceType')
  if @config.config_value=='Daily'
    @student = Student.find(params[:id])
    @month_date = params[:month_date]
    @absentee = Attendance.new
  else
    @student = Student.find(params[:id]) unless params[:id].nil?
    @student ||= Student.find(params[:subject_leave][:student_id])
    @subject_leave=SubjectLeave.new
  end
  respond_to do |format|
    format.js { render :action => 'new' }
  end
end

def create
  @config = Configuration.find_by_config_key('StudentAttendanceType')
  if @config.config_value=="SubjectWise"
    @student = Student.find(params[:subject_leave][:student_id])
    @tte=TimetableEntry.find(params[:timetable_entry])
    @absentee = SubjectLeave.new(params[:subject_leave])
    @absentee.subject_id=params[:subject_leave][:subject_id]
    @absentee.employee_id=@tte.employee_id
    #      @absentee.subject_id=@tte.subject_id
    @absentee.class_timing_id=@tte.class_timing_id
    @absentee.batch_id = @student.batch_id
      
  else
    @student = Student.find(params[:attendance][:student_id])
    @absentee = Attendance.new(params[:attendance])
  end
  respond_to do |format|
    if @absentee.save
      sms_setting = SmsSetting.new()
      message = ""
        
      reminderrecipients = []
      batch_ids = {}
      student_ids = {}
      unless @config.config_value=="SubjectWise"
        if @absentee.is_full_day
          message = "#{@student.first_name} #{@student.last_name} #{t('flash_msg7')} #{@absentee.month_date}"
        elsif @absentee.forenoon == true and @absentee.afternoon == false
          message = "#{@student.first_name} #{@student.last_name} #{t('flash_msg7')} Present but Late on #{@absentee.month_date}"
        elsif @absentee.afternoon == true and @absentee.forenoon == false
          message = "#{@student.first_name} #{@student.last_name} #{t('flash_msg7')} Present but Late on #{@absentee.month_date}"
        end
      else
        message = "#{@student.first_name} #{@student.last_name} #{t('flash_msg7')} #{@absentee.month_date}  #{t('flash_subject')} #{@absentee.subject.name} #{t('flash_period')} #{@absentee.class_timing.try(:name)}"
      end
        
      reminderrecipients.push @student.user_id
      batch_ids[@student.user_id] = @student.batch_id
      student_ids[@student.user_id] = @student.id
        
      #EDITED FOR MULTIPLE GUARDIAN
      #
      #unless @student.immediate_contact_id.ni?
        
      unless @student.student_guardian.empty?
        guardians = @student.student_guardian
        guardians.each do |guardian|
          #            guardian = Guardian.find(@student.immediate_contact_id)

          unless guardian.user_id.nil?
            reminderrecipients.push guardian.user_id
            batch_ids[guardian.user_id] = @student.batch_id
            student_ids[guardian.user_id] = @student.id
          end
        end  
      end
        
      unless reminderrecipients.empty? && @absentee.is_leave==0
        Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
            :recipient_ids => reminderrecipients,
            :subject=>"Attendance Notice",
            :rtype=>6,
            :rid => @absentee.id,
            :student_id => student_ids,
            :batch_id => batch_ids,
            :body=>message ))
      end
        
      if sms_setting.application_sms_active and @student.is_sms_enabled and sms_setting.attendance_sms_active
        recipients = []
        if sms_setting.student_sms_active
          recipients.push @student.phone2 unless @student.phone2.nil?
        end
          
          
          
        if sms_setting.parent_sms_active
          #EDITED FOR MULTIPLE GUARDIAN

        
            
          #            unless @student.immediate_contact_id.nil?
          #              recipients.push guardian.mobile_phone unless guardian.mobile_phone.nil?
          #            end
        end
          
        unless recipients.empty?  and send_sms("attandence")
          Delayed::Job.enqueue(SmsManager.new(message,recipients))
        end
      end
      format.js { render :action => 'create' }
    else
      @error = true
      format.html { render :action => "new" }
      format.js { render :action => 'create' }
    end
  end
end

def edit
  @config = Configuration.find_by_config_key('StudentAttendanceType')
  if @config.config_value=='Daily'
    @absentee = Attendance.find params[:id]
  else
    @absentee = SubjectLeave.find params[:id]
  end
  @student = Student.find(@absentee.student_id)
  respond_to do |format|
    format.html { }
    format.js { render :action => 'edit' }
  end
end

def update            
  abort('there')
  @config = Configuration.find_by_config_key('StudentAttendanceType')
  reminderrecipients = []
  batch_ids = {}
  student_ids = {}
  message = ""
  if @config.config_value=='Daily'
    @absentee = Attendance.find params[:id]
    @student = Student.find(@absentee.student_id)
    if @absentee.update_attributes(params[:attendance])
      Reminder.delete_all("rtype = 6 AND rid ="+@absentee.id.to_s)
      if @absentee.is_full_day
        message = "#{@student.first_name} #{@student.last_name} #{t('flash_msg7')} #{@absentee.month_date}"
      elsif @absentee.forenoon == true and @absentee.afternoon == false
        message = "#{@student.first_name} #{@student.last_name} #{t('flash_msg7')} (forenoon) #{@absentee.month_date}"
      elsif @absentee.afternoon == true and @absentee.forenoon == false
        message = "#{@student.first_name} #{@student.last_name} #{t('flash_msg7')} (afternoon) #{@absentee.month_date}"
      end
        
      reminderrecipients.push @student.user_id
      batch_ids[@student.user_id] = @student.batch_id
      student_ids[@student.user_id] = @student.id
        
      #EDITED FOR MULTIPLE GUARDIAN
      unless @student.student_guardian.empty?
        guardians = @student.student_guardian
        guardians.each do |guardian|

          unless guardian.user_id.nil?
            reminderrecipients.push guardian.user_id
            batch_ids[guardian.user_id] = @student.batch_id
            student_ids[guardian.user_id] = @student.id
          end
        end  
      end
        
        
      #        unless @student.immediate_contact_id.nil?
      #          guardian = Guardian.find(@student.immediate_contact_id)
      #          unless guardian.user_id.nil?
      #            reminderrecipients.push guardian.user_id
      #            batch_ids[guardian.user_id] = @student.batch_id
      #            student_ids[guardian.user_id] = @student.id
      #          end
      #        end
        
      unless reminderrecipients.empty?
        Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
            :recipient_ids => reminderrecipients,
            :subject=>"Attendance Notice",
            :rtype=>6,
            :rid => @absentee.id,
            :student_id => student_ids,
            :batch_id => batch_ids,
            :body=>message ))
      end
    else
      @error = true
    end
  else
    @absentee = SubjectLeave.find params[:id]
    @student = Student.find(@absentee.student_id)
    if @absentee.update_attributes(params[:subject_leave])
      Reminder.delete_all("rtype = 6 AND rid ="+@absentee.id.to_s)
      message = "#{@student.first_name} #{@student.last_name} #{t('flash_msg7')} #{@absentee.month_date}  #{t('flash_subject')} #{@absentee.subject.name} #{t('flash_period')} #{@absentee.class_timing.try(:name)}"
        
      reminderrecipients.push @student.user_id
      batch_ids[@student.user_id] = @student.batch_id
      student_ids[@student.user_id] = @student.id
        
      #EDITED FOR MULTIPLE GUARDIAN
      unless @student.student_guardian.empty?
        guardians = @student.student_guardian
        guardians.each do |guardian|

          unless guardian.user_id.nil?
            reminderrecipients.push guardian.user_id
            batch_ids[guardian.user_id] = @student.batch_id
            student_ids[guardian.user_id] = @student.id
          end
        end  
      end
        
      #        unless @student.immediate_contact_id.nil?
      #          guardian = Guardian.find(@student.immediate_contact_id)
      #          reminderrecipients.push guardian.user_id unless guardian.user_id.nil?
      #          unless guardian.user_id.nil?
      #            reminderrecipients.push guardian.user_id
      #            batch_ids[guardian.user_id] = @student.batch_id
      #            student_ids[guardian.user_id] = @student.id
      #          end
      #        end
        
      unless reminderrecipients.empty?
        Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
            :recipient_ids => reminderrecipients,
            :subject=>"Attendance Notice",
            :rtype=>6,
            :rid => @absentee.id,
            :student_id => student_ids,
            :batch_id => batch_ids,
            :body=>message ))
      end
    else
      @error = true
    end
  end
  respond_to do |format|
    format.js { render :action => 'update' }
  end
end
  
def destroy
  @config = Configuration.find_by_config_key('StudentAttendanceType')
  if @config.config_value=='Daily'
    @absentee = Attendance.find params[:id]
  else
    @absentee = SubjectLeave.find params[:id]
    @tte_entry = TimetableEntry.find_by_subject_id_and_class_timing_id(@absentee.subject_id,@absentee.class_timing_id)
    sub=Subject.find @absentee.subject_id
  end
  @absentee.delete
    
  Reminder.delete_all("rtype = 6 AND rid ="+@absentee.id.to_s)
    
  @student = Student.find(@absentee.student_id)
  respond_to do |format|
    format.js { render :action => 'update' }
  end
end

def only_privileged_employee_allowed
  @privilege = @current_user.privileges.map{|p| p.name}
  if @current_user.employee?
    @employee_subjects= @current_user.employee_record.subjects
    if @employee_subjects.empty? and !@privilege.include?("StudentAttendanceRegister")
      flash[:notice] = "#{t('flash_msg4')}"
      redirect_to :controller => 'user', :action => 'dashboard'
    else
      @allow_access = true
    end
  end
end
  
private
  
def get_report_year(batch_id,student_id,start_date="",end_date="")
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
  start_date = start_date.to_date unless start_date.blank?
  end_date = end_date.to_date unless end_date.blank?
  if current_user.employee? or current_user.admin?
    api_uri = URI(api_endpoint + "api/calender/getattendence")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    if start_date!="" && end_date!=""
      request.set_form_data({"start_date" =>start_date,"end_date" =>end_date,'send_yearly'=>1,"student_id" =>student_id,"batch_id" =>batch_id ,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
    else
      request.set_form_data({"student_id" =>student_id,"batch_id" =>batch_id ,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
    end  
    response = http.request(request)
    @attendence_data = JSON::parse(response.body)
  end
    
  @attendence_data
end
  
def get_report_full(date)
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
  date = date.to_date unless date.blank?
  if current_user.employee? or current_user.admin?
    api_uri = URI(api_endpoint + "api/calender/studentattendencereportfull")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"date" =>date ,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})

    response = http.request(request)
    @student_response = JSON::parse(response.body)
  end
    
  @student_response
end
  
def get_report(batch_id,date)
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
  date = date.to_date unless date.blank?
  if current_user.employee? or current_user.admin?
    api_uri = URI(api_endpoint + "api/calender/studentattendencereport")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"date" =>date,"batch_id" =>batch_id ,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})

    response = http.request(request)
    @student_response = JSON::parse(response.body)
  end
    
  @student_response
end
  
  
def add_attendence_subject(subject_id,date_to_use,student_id,late)
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
  date_to_use = date_to_use.to_date unless date_to_use.blank?
  if current_user.employee? or current_user.admin?
    api_uri = URI(api_endpoint + "api/attendance/addattendence")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"date"=>date_to_use,"student_id"=>student_id,"late"=>late,"subject_id" =>subject_id ,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})

    response = http.request(request)
    @student_response = JSON::parse(response.body)
  end
    
  @student_response
end


def get_subject_report_date_name(subject_id,date_to_use)
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
  date_to_use = date_to_use.to_date unless date_to_use.blank?
  if current_user.employee? or current_user.admin?
    api_uri = URI(api_endpoint + "api/attendance/Reportteacherbyname")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"subject_id" =>subject_id ,"date"=>date_to_use,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})

    response = http.request(request)
    @student_response = JSON::parse(response.body)
  end
    
  @student_response
end

def get_subject_attendence_student_name(subject_id,date_to_use)
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
  date_to_use = date_to_use.to_date unless date_to_use.blank?
  if current_user.employee? or current_user.admin?
    api_uri = URI(api_endpoint + "api/attendance/getstudentsbysubname")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"subject_id" =>subject_id ,"date"=>date_to_use,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})

    response = http.request(request)
    @student_response = JSON::parse(response.body)
  end
    
  @student_response
end
  
def get_subject_report_date(subject_id,date_to_use)
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
  date_to_use = date_to_use.to_date unless date_to_use.blank?
  if current_user.employee? or current_user.admin?
    api_uri = URI(api_endpoint + "api/attendance/reportteacher")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"subject_id" =>subject_id ,"date"=>date_to_use,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})

    response = http.request(request)
    @student_response = JSON::parse(response.body)
  end
    
  @student_response
end
  
def get_subject_report(subject_id)
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']

  if current_user.employee? or current_user.admin?
    api_uri = URI(api_endpoint + "api/attendance/reportallteacher")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"subject_id" =>subject_id ,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})

    response = http.request(request)
    @student_response = JSON::parse(response.body)
  end
    
  @student_response
end

def get_subject_report_name(subject_id,date_start=false,date_end=false)
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
  date_start = date_start.to_date unless date_start.blank?
  date_end = date_end.to_date unless date_end.blank?
  if current_user.employee? or current_user.admin?
    api_uri = URI(api_endpoint + "api/attendance/reportallteachername")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
   
    unless date_start.blank?
      request.set_form_data({"subject_id" =>subject_id, "date_start"=>date_start,"date_end"=>date_end ,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
    else
      request.set_form_data({"subject_id" =>subject_id ,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
    end  
    response = http.request(request)
    @student_response = JSON::parse(response.body)
  end
    
  @student_response
end
  
def get_subject_attendence_student(subject_id,date_to_use)
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
  date_to_use = date_to_use.to_date unless date_to_use.blank?
  if current_user.employee? or current_user.admin?
    api_uri = URI(api_endpoint + "api/attendance/getstudents")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"subject_id" =>subject_id ,"date"=>date_to_use,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})

    response = http.request(request)
    @student_response = JSON::parse(response.body)
  end
    
  @student_response
end
  
def get_attendence_student(batch_id,date_to_use)
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
  date_to_use = date_to_use.to_date unless date_to_use.blank?
  if current_user.employee? or current_user.admin?
    api_uri = URI(api_endpoint + "api/calender/getbatchstudentattendence")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"batch_id" =>batch_id ,"date"=>date_to_use,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})

    response = http.request(request)
    @student_response = JSON::parse(response.body)
  end
    
  @student_response
end
  
def add_attendence_student(batch_id,date_to_use,student_id,late=0,remove_only=0)
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
  date_to_use = date_to_use.to_date unless date_to_use.blank?
  if current_user.employee? or current_user.admin?
    api_uri = URI(api_endpoint + "api/calender/addattendencesingle")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"remove_only"=>remove_only,"date"=>date_to_use,"student_id"=>student_id,"late"=>late,"batch_id" =>batch_id ,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})

    response = http.request(request)
    @student_response = JSON::parse(response.body)
  end
    
  @student_response
end
  
def leave_approve_deny(leave_id,student_id,status)
  require 'net/http'
  require 'uri'
  require "yaml"
 
  if status == '3'
    status = '1'
  elsif status == '4'
    status = '0'
  end
    
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']

  if current_user.employee? or current_user.admin?
    api_uri = URI(api_endpoint + "api/calender/approveLeave")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data({"leave_id"=>leave_id,"student_id"=>student_id,"status" =>status, "user_secret" =>session[:api_info][0]['user_secret']})

    response = http.request(request)
    @student_response = JSON::parse(response.body)
  end
    
  @student_response
end
  
def immidiate_leave(student_id, start_date, end_date)
  require 'net/http'
  require 'uri'
  require "yaml"
    
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
    
  if current_user.employee? or current_user.admin?
      
    form_data = {}
    form_data['user_secret'] = session[:api_info][0]['user_secret']
    form_data['leave_subject'] = 'On Leave'
    form_data['reason'] = 'On Leave'
    form_data['start_date'] = start_date
    form_data['end_date'] = end_date
    form_data['student_id'] = student_id
    start_date = start_date.to_date unless start_date.blank?
    end_date = end_date.to_date unless end_date.blank?
    api_uri = URI(api_endpoint + "api/event/addLeaveStudent")
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    request.set_form_data(form_data)

    response = http.request(request)
    @immidiate_leave_response = JSON::parse(response.body)
  end
    
  @immidiate_leave_response
end
  
  
end
