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

class AttendanceReportsController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only=>[:index]
  filter_access_to :all, :except=>[:index,:load_end_date, :subject, :show, :year, :report, :filter, :student_details,:report_pdf,:filter_report_pdf]
  filter_access_to [:index, :subject,:load_end_date, :mode, :show, :year, :report, :filter, :student_details,:report_pdf,:filter_report_pdf], :attribute_check=>true, :load_method => lambda { current_user }
  before_filter :only_assigned_employee_allowed
  before_filter :default_time_zone_present_time
  before_filter :check_status


  def index
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    @config = Configuration.find_by_config_key('StudentAttendanceType')
    if current_user.admin?
      @batches = Batch.active
    elsif @current_user.privileges.map{|p| p.name}.include?('StudentAttendanceView')
      @batches = Batch.active
    elsif @current_user.employee?
      if @config.config_value == 'Daily'
        @batches=@current_user.employee_record.batches
      else
        @batches=@current_user.employee_record.batches
        @batches+=@current_user.employee_record.subjects.collect{|b| b.batch}
        @batches=@batches.uniq unless @batches.empty?
      end
    end
    @config = Configuration.find_by_config_key('StudentAttendanceType')
  end

  def subject
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

      @batch_data = Rails.cache.fetch("course_data_#{course_id}_#{batch_name.parameterize("_")}_#{current_user.id}"){
        if batch_name.length == 0
          batches = Batch.find_by_course_id(course_id)
        else
          batches = Batch.find_by_course_id_and_name(course_id, batch_name)
        end
        batches
      }
      params[:batch_id] = 0
      unless @batch_data.nil?
        params[:batch_id] = @batch_data.id 
      end
    else
      batch = Batch.find(params[:batch_id])
      params[:batch_id] = batch.id
    end
    
    
    @batch = Batch.find params[:batch_id]

    if @current_user.employee? 
      @role_symb = @current_user.role_symbols
      if @role_symb.include?(:student_attendance_view) or @role_symb.include?(:student_attendance_register)
        @subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>'is_deleted = false')
      else
        if @batch.employee_id.to_i==@current_user.employee_record.id
          @subjects= @subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>'is_deleted = false')
        else
          @subjects= Subject.find(:all,:joins=>"INNER JOIN employees_subjects ON employees_subjects.subject_id = subjects.id AND employee_id = #{@current_user.employee_record.id} AND batch_id = #{@batch.id} AND is_deleted = false")
        end
      end
    else
      @subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>'is_deleted = false')
    end

    render :update do |page|
      page.replace_html 'subject', :partial => 'subject'
    end
  end

  def mode
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

      @batch_data = Rails.cache.fetch("course_data_#{course_id}_#{batch_name.parameterize("_")}_#{current_user.id}"){
        if batch_name.length == 0
          batches = Batch.find_by_course_id(course_id)
        else
          batches = Batch.find_by_course_id_and_name(course_id, batch_name)
        end
        batches
      }
      params[:batch_id] = 0
      unless @batch_data.nil?
        params[:batch_id] = @batch_data.id 
      end
    else
      batch = Batch.find(params[:batch_id])
      params[:batch_id] = batch.id
    end
    
    @batch = Batch.find params[:batch_id]
    @config = Configuration.find_by_config_key('StudentAttendanceType')
    if @config.config_value == 'Daily'
      unless params[:subject_id] == ''
        @subject = params[:subject_id]
      else
        @subject = 0
      end
      render :update do |page|
        page.replace_html 'mode', :partial => 'mode'
        page.replace_html 'month', :text => ''
      end
    else
      if params[:subject_id] ==''
        render :update do |page|
          page.replace_html 'mode', :text => ''
          page.replace_html 'month', :text => ''
        end
      else
        unless params[:subject_id] == 'all_sub'
          @subject = params[:subject_id]
        else
          @subject = 0
        end
        render :update do |page|
          page.replace_html 'mode', :partial => 'mode'
          page.replace_html 'month', :text => ''
        end
      end
    end
  end
  def show
    @batch = Batch.find params[:batch_id]
    @start_date = @batch.start_date.to_date
    @end_date = @local_tzone_time.to_date
    @leaves=Hash.new { |h, k| h[k] = Hash.new(&h.default_proc) }
    @mode = params[:mode]
    @config = Configuration.find_by_config_key('StudentAttendanceType')
    @students = @batch.students.by_first_name
    unless @config.config_value == 'Daily'
      if @mode == 'Overall'
        #        @academic_days=@batch.academic_days.count
        unless params[:subject_id] == '0'
          @subject = Subject.find params[:subject_id]
          unless @subject.elective_group_id.nil?
            @students = @subject.students.by_first_name
          end
          @academic_days=@batch.subject_hours(@start_date, @end_date, params[:subject_id]).values.flatten.compact.count
          @subject = Subject.find params[:subject_id] 
          @grouped = @batch.subject_leaves.find(:all,  :conditions =>{:subject_id => @subject.id, :batch_id=>@batch.id,:month_date => @start_date..@end_date}).group_by(&:student_id)
          @report = @batch.subject_leaves.find(:all,:conditions =>{:subject_id => @subject.id, :batch_id=>@batch.id,:month_date => @start_date..@end_date})
          @students.each do |s|
            if @grouped[s.id].nil?
              @leaves[s.id]['leave']=0
            else
              @leaves[s.id]['leave']=@grouped[s.id].count
            end
            @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
            @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          end
        else
          @academic_days=@batch.subject_hours(@start_date, @end_date, 0).values.flatten.compact.count
          @report = @batch.subject_leaves.find(:all,:conditions =>{:batch_id=>@batch.id,:month_date => @start_date..@end_date})
          @grouped = @batch.subject_leaves.find(:all,  :conditions =>{:batch_id=>@batch.id,:month_date => @start_date..@end_date}).group_by(&:student_id)
          @students.each do |s|
            if @grouped[s.id].nil?
              @leaves[s.id]['leave']=0
            else
              @leaves[s.id]['leave']=@grouped[s.id].count
            end
            @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
            @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          end
        end
        render :update do |page|
          page.replace_html 'report', :partial => 'report'
          page.replace_html 'month', :text => ''
          page.replace_html 'year', :text => ''
        end
      else
        @year = @local_tzone_time.to_date.year
        @academic_days=@batch.working_days(@local_tzone_time.to_date).count
        @subject = params[:subject_id]
        render :update do |page|
          page.replace_html 'month', :partial => 'month'
        end
      end
    else
      if @mode == 'Overall'
        @academic_days=@batch.academic_days.count
        @student_leaves = Attendance.find(:all,:conditions =>{:batch_id=>@batch.id,:month_date => @start_date..@end_date})
        
#        leaves_forenoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>true,:afternoon=>false,:month_date => @start_date..@end_date},:group=>:student_id)
#        leaves_afternoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>false,:afternoon=>true,:month_date => @start_date..@end_date},:group=>:student_id)
#        leaves_full=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>true,:afternoon=>true,:month_date => @start_date..@end_date},:group=>:student_id)
        @students.each do |student|
          on_leaves = 0;
          leaves_other = 0;
          leaves_full = 0;
          unless @student_leaves.empty?
            @student_leaves.each do |r|
              if r.student_id == student.id
                working_days_count=@batch.find_working_days(r.month_date.to_date,r.month_date.to_date).select{|v| v<=r.month_date.to_date}.count

                if working_days_count==1
                  if r.is_leave == true
                    on_leaves = on_leaves+1;
                  elsif r.forenoon==true && r.afternoon==false
                    leaves_other = leaves_other+1;
                  elsif r.forenoon==false && r.afternoon==true  
                    leaves_other = leaves_other+1;
                  else
                    leaves_full = leaves_full+1;
                  end 
                end
              end
            end
          end
          @leaves[student.id]['late'] = leaves_other
          @leaves[student.id]['absent'] = leaves_full
          @leaves[student.id]['on_leave'] = on_leaves
          @leaves[student.id]['present'] = @academic_days-on_leaves-leaves_full
          @leaves[student.id]['total']=@academic_days-leaves_full.to_f-(0.5*leaves_other)
          @leaves[student.id]['percent'] = (@leaves[student.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
        end
        render :update do |page|
          page.replace_html 'report', :partial => 'report'
          page.replace_html 'month', :text => ''
          page.replace_html 'year', :text => ''
        end
      elsif @mode == 'Monthly'
        @year = @local_tzone_time.to_date.year
        @subject = params[:subject_id]
        render :update do |page|
          page.replace_html 'month', :partial => 'month'
        end
      else
        render :update do |page|
          page.replace_html 'month', :partial => 'daterange'
        end
      end
    end
  end
  def load_end_date
    @batch = Batch.find params[:batch_id]
    if request.xhr?
     
      @start_date = params[:start_date].to_date
      render :update do |page|
        page.replace_html 'year', :partial => 'end_date'
      end
    end
  end
  def year
    @batch = Batch.find params[:batch_id]
    @subject = params[:subject_id]
    if request.xhr?
      @year = @local_tzone_time.to_date.year
      @month = params[:month]
      render :update do |page|
        page.replace_html 'year', :partial => 'year'
      end
    end
  end

  def report2
    @batch = Batch.find params[:batch_id]
    @month = params[:month]
    @year = params[:year]
    @students = @batch.students.by_first_name
    @config = Configuration.find_by_config_key('StudentAttendanceType')
    #    @date = "01-#{@month}-#{@year}"
    @date = '01-'+@month+'-'+@year
    @start_date = @date.to_date
    @today = @local_tzone_time.to_date
    working_days=@batch.working_days(@date.to_date)
    unless @start_date > @local_tzone_time.to_date
      if @month == @today.month.to_s
        @end_date = @local_tzone_time.to_date
      else
        @end_date = @start_date.end_of_month
      end
      @academic_days=  working_days.select{|v| v<=@end_date}.count
      if @config.config_value == 'Daily'
        @report = @batch.attendances.find(:all,:conditions =>{:month_date => @start_date..@end_date})
      else
        unless params[:subject_id] == '0'
          @subject = Subject.find params[:subject_id]
          @report = SubjectLeave.find_all_by_subject_id(@subject.id,  :conditions =>{:batch_id=>@batch.id,:month_date => @start_date..@end_date})
        else
          @report = @batch.subject_leaves.find(:all,:conditions =>{:batch_id=>@batch.id,:month_date => @start_date..@end_date})
        end
      end
    else
      @report = ''
    end
    render :update do |page|
      page.replace_html 'report', :partial => 'report'
    end
  end

  def report
    @batch = Batch.find params[:batch_id]
    unless params[:month].nil?
      @month = params[:month]
      @year = params[:year]
    end  
    @students = @batch.students.by_first_name
    @config = Configuration.find_by_config_key('StudentAttendanceType')
    #    @date = "01-#{@month}-#{@year}"
    unless params[:month].nil?
      @date = '01-'+@month+'-'+@year
    else
      @date = params[:start_date]
    end  
    @start_date = @date.to_date
    
    @today = @local_tzone_time.to_date
    if (@start_date<@batch.start_date.to_date.beginning_of_month || @start_date>@batch.end_date.to_date || @start_date>=@today.next_month.beginning_of_month)
      render :update do |page|
        page.replace_html 'report', :text => t('no_reports')
      end
    else
      @leaves=Hash.new { |h, k| h[k] = Hash.new(&h.default_proc) }
      working_days=@batch.working_days(@date.to_date)
      unless @start_date > @local_tzone_time.to_date
        unless params[:month].nil?
          if @month == @today.month.to_s
            @end_date = @local_tzone_time.to_date
          else
            @end_date = @start_date.end_of_month
          end
        else
          @end = params[:end_date]
          @end_date = @end.to_date
          if @end_date > @local_tzone_time.to_date
            @end_date = @local_tzone_time.to_date
          end
        end   
        if @config.config_value == 'Daily'
          @academic_days=  @batch.find_working_days(@start_date,@end_date).select{|v| v<=@end_date}.count
          #working_days.select{|v| v<=@end_date}.count
          
          @student_leaves = Attendance.find(:all,:conditions =>{:batch_id=>@batch.id,:month_date => @start_date..@end_date})
          @students.each do |student|
            on_leaves = 0;
            leaves_other = 0;
            leaves_full = 0;
            unless @student_leaves.empty?
              @student_leaves.each do |r|
                if r.student_id == student.id
                  working_days_count=@batch.find_working_days(r.month_date.to_date,r.month_date.to_date).select{|v| v<=r.month_date.to_date}.count

                  if working_days_count==1
                    if r.is_leave == true
                      on_leaves = on_leaves+1;
                    elsif r.forenoon==true && r.afternoon==false
                      leaves_other = leaves_other+1;
                    elsif r.forenoon==false && r.afternoon==true  
                      leaves_other = leaves_other+1;
                    else
                      leaves_full = leaves_full+1;
                    end 
                  end
                end
              end
            end
            @leaves[student.id]['late'] = leaves_other
            @leaves[student.id]['absent'] = leaves_full
            @leaves[student.id]['on_leave'] = on_leaves
            @leaves[student.id]['present'] = @academic_days-on_leaves-leaves_full
            @leaves[student.id]['total']=@academic_days-leaves_full.to_f-(0.5*leaves_other)
            @leaves[student.id]['percent'] = (@leaves[student.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          end
#          leaves_forenoon=Attendance.count(:all,:joins=>:student,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>true,:afternoon=>false,:month_date => @start_date..@end_date},:group=>:student_id)
#          leaves_afternoon=Attendance.count(:all,:joins=>:student,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>false,:afternoon=>true,:month_date => @start_date..@end_date},:group=>:student_id)
#          leaves_full=Attendance.count(:all,:joins=>:student,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>true,:afternoon=>true,:month_date => @start_date..@end_date},:group=>:student_id)
#          @students.each do |student|
#            @leaves[student.id]['total']=@academic_days-leaves_full[student.id].to_f-(0.5*(leaves_forenoon[student.id].to_f+leaves_afternoon[student.id].to_f))
#            @leaves[student.id]['percent'] = (@leaves[student.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
#          end
        else
          unless params[:subject_id] == '0'
            @subject = Subject.find params[:subject_id]
            unless @subject.elective_group_id.nil?
              @students = @subject.students.by_first_name
            end
            @academic_days=@batch.subject_hours(@start_date, @end_date, params[:subject_id]).values.flatten.compact.count
            @report = SubjectLeave.find_all_by_subject_id(@subject.id,  :conditions =>{:batch_id=>@batch.id,:month_date => @start_date..@end_date})
            @grouped = SubjectLeave.find_all_by_subject_id(@subject.id,  :conditions =>{:batch_id=>@batch.id,:month_date => @start_date..@end_date}).group_by(&:student_id)
            @batch.students.by_first_name.each do |s|
              if @grouped[s.id].nil?
                @leaves[s.id]['leave']=0
              else
                @leaves[s.id]['leave']=@grouped[s.id].count
              end
              @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
              @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
            end
          else
            @academic_days=@batch.subject_hours(@start_date, @end_date, 0).values.flatten.compact.count
            @report = @batch.subject_leaves.find(:all,:conditions =>{:month_date => @start_date..@end_date})
            @grouped = @batch.subject_leaves.find(:all,  :conditions =>{:month_date => @start_date..@end_date}).group_by(&:student_id)
            @batch.students.by_first_name.each do |s|
              if @grouped[s.id].nil?
                @leaves[s.id]['leave']=0
              else
                @leaves[s.id]['leave']=@grouped[s.id].count
              end
              @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
              @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
            end
          end
        end
      else
        @report = ''
      end
      render :update do |page|
        page.replace_html 'report', :partial => 'report'
      end
    end
  end

  def student_details
    @student = Student.find params[:id]
    @batch = @student.batch
    @config = Configuration.find_by_config_key('StudentAttendanceType')
    if @config.config_value == 'Daily'
      @report = Attendance.find(:all,:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id})
      
      @report_real = []
      unless @report.empty?
        @report.each do |r|
          working_days_count=@batch.find_working_days(r.month_date.to_date,r.month_date.to_date).select{|v| v<=r.month_date.to_date}.count

          if working_days_count==1
            @report_real << r
          end  
        end
      end
      @report = @report_real
    else
      unless params[:subject_id].to_i == 0
        @report = SubjectLeave.find(:all,:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id, :subject_id => params[:subject_id]})
      else
        @report = SubjectLeave.find(:all,:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id})
      end
    end
  end

  def filter
    @config = Configuration.find_by_config_key('StudentAttendanceType')
    @batch = Batch.find(params[:filter][:batch])
    @students = @batch.students.by_first_name
    @start_date = (params[:filter][:start_date]).to_date
    @end_date = (params[:filter][:end_date]).to_date
    @range = (params[:filter][:range])
    @value = (params[:filter][:value])
    @leaves=Hash.new { |h, k| h[k] = Hash.new(&h.default_proc) }
    #    @academic_days=  @working_days.select{|v| v<=@end_date}.count
    @today = @local_tzone_time.to_date
    @mode=params[:filter][:report_type]
    working_days=@batch.working_days(@start_date.to_date)
    if request.post?
      unless @start_date > @local_tzone_time.to_date
        unless @config.config_value == 'Daily'
          unless params[:filter][:subject] == '0'
            @subject = Subject.find params[:filter][:subject]
            @academic_days=@batch.subject_hours(@start_date, @end_date, params[:filter][:subject]).values.flatten.compact.count
            @report = SubjectLeave.find_all_by_subject_id(@subject.id,  :conditions =>{:batch_id=>@batch.id,:month_date => @start_date..@end_date})
            @grouped = SubjectLeave.find_all_by_subject_id(@subject.id,  :conditions =>{:batch_id=>@batch.id,:month_date => @start_date..@end_date}).group_by(&:student_id)
            @batch.students.by_first_name.each do |s|
              if @grouped[s.id].nil?
                @leaves[s.id]['leave']=0
              else
                @leaves[s.id]['leave']=@grouped[s.id].count
              end
              
            
              
              @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
              @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
            end
          else
            @academic_days=@batch.subject_hours(@start_date, @end_date, 0).values.flatten.compact.count
            @report = @batch.subject_leaves.find(:all,:conditions =>{:month_date => @start_date..@end_date})
            @grouped = @batch.subject_leaves.find(:all,  :conditions =>{:month_date => @start_date..@end_date}).group_by(&:student_id)
            @batch.students.by_first_name.each do |s|
              if @grouped[s.id].nil?
                @leaves[s.id]['leave']=0
              else
                @leaves[s.id]['leave']=@grouped[s.id].count
              end
              @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
              @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
            end
          end
        else
          if @mode=='Overall'
            #            @working_days=@batch.academic_days.count
            @academic_days=@batch.academic_days.count
          else
            working_days=@batch.working_days(@start_date.to_date)
            @academic_days=  @batch.find_working_days(@start_date,@end_date).select{|v| v<=@end_date}.count
            #            @working_days=  working_days.select{|v| v<=@end_date}.count
           # @academic_days=  working_days.select{|v| v<=@end_date}.count
          end
          
          @student_leaves = Attendance.find(:all,:conditions =>{:batch_id=>@batch.id,:month_date => @start_date..@end_date})
          @students.each do |student|
            on_leaves = 0;
            leaves_other = 0;
            leaves_full = 0;
            unless @student_leaves.empty?
              @student_leaves.each do |r|
                if r.student_id == student.id
                  working_days_count=@batch.find_working_days(r.month_date.to_date,r.month_date.to_date).select{|v| v<=r.month_date.to_date}.count

                  if working_days_count==1
                    if r.is_leave == true
                      on_leaves = on_leaves+1;
                    elsif r.forenoon==true && r.afternoon==false
                      leaves_other = leaves_other+1;
                    elsif r.forenoon==false && r.afternoon==true  
                      leaves_other = leaves_other+1;
                    else
                      leaves_full = leaves_full+1;
                    end 
                  end
                end
              end
            end
            @leaves[student.id]['late'] = leaves_other
            @leaves[student.id]['absent'] = leaves_full
            @leaves[student.id]['on_leave'] = on_leaves
            @leaves[student.id]['present'] = @academic_days-on_leaves-leaves_full
              
            @leaves[student.id]['total']=@academic_days-leaves_full.to_f-(0.5*leaves_other)
            @leaves[student.id]['percent'] = (@leaves[student.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          end
#          leaves_forenoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>true,:afternoon=>false,:month_date => @start_date..@end_date},:group=>:student_id)
#          leaves_afternoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>false,:afternoon=>true,:month_date => @start_date..@end_date},:group=>:student_id)
#          leaves_full=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>true,:afternoon=>true,:month_date => @start_date..@end_date},:group=>:student_id)
#          @students.each do |student|
#            @leaves[student.id]['total']=@academic_days-leaves_full[student.id].to_f-(0.5*(leaves_forenoon[student.id].to_f+leaves_afternoon[student.id].to_f))
#            @leaves[student.id]['percent'] = (@leaves[student.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
#          end
          #          @report = @batch.attendances.find(:all,:conditions =>{:month_date => @start_date..@end_date})
          #          @report = @batch.attendances.find(:all,:conditions =>{:month_date => @start_date..@end_date})
        end
      end
    end
  end

  def filter2
    @config = Configuration.find_by_config_key('StudentAttendanceType')
    @batch = Batch.find(params[:filter][:batch])
    @students = @batch.students.by_first_name
    @start_date = (params[:filter][:start_date]).to_date
    @end_date = (params[:filter][:end_date]).to_date
    @range = (params[:filter][:range])
    @value = (params[:filter][:value])
    if request.post?
      unless @config.config_value == 'Daily'
        unless params[:filter][:subject] == '0'
          @subject = Subject.find params[:filter][:subject]
        end
        if params[:filter][:subject] == '0'
          @report = @batch.subject_leaves.find(:all,:conditions =>{:month_date => @start_date..@end_date})
        else
          @report = SubjectLeave.find_all_by_subject_id(@subject.id,  :conditions =>{:month_date => @start_date..@end_date})
        end
      else
        @report = @batch.attendances.find(:all,:conditions =>{:month_date => @start_date..@end_date})
      end
    end
  end

  def advance_search
    @batches = []
  end

  def report_pdf
    #if request.post?
    @config = Configuration.find_by_config_key('StudentAttendanceType')
    @batch = Batch.find(params[:batch])
    @students = @batch.students.by_first_name
    @start_date = (params[:start_date]).to_date
    @end_date = (params[:end_date]).to_date
    @range = (params[:range])
    @value = (params[:value])
    @leaves=Hash.new { |h, k| h[k] = Hash.new(&h.default_proc) }
    @today = @local_tzone_time.to_date
    @mode=params[:report_type]
    working_days=@batch.working_days(@start_date.to_date)
    unless @start_date > @local_tzone_time.to_date
      unless @config.config_value == 'Daily'
        unless params[:subject] == '0'
          @subject = Subject.find params[:subject]
          unless @subject.elective_group_id.nil?
            @students = @subject.students.by_first_name
          end
          @academic_days=@batch.subject_hours(@start_date, @end_date, params[:subject]).values.flatten.compact.count
          @grouped = SubjectLeave.find_all_by_subject_id(@subject.id,  :conditions =>{:month_date => @start_date..@end_date}).group_by(&:student_id)
          @students.each do |s|
            if @grouped[s.id].nil?
              @leaves[s.id]['leave']=0
            else
              @leaves[s.id]['leave']=@grouped[s.id].count
            end
            @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
            @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          end
        else
          @academic_days=@batch.subject_hours(@start_date, @end_date, 0).values.flatten.compact.count
          @grouped = @batch.subject_leaves.find(:all,  :conditions =>{:month_date => @start_date..@end_date}).group_by(&:student_id)
          @students.each do |s|
            if @grouped[s.id].nil?
              @leaves[s.id]['leave']=0
            else
              @leaves[s.id]['leave']=@grouped[s.id].count
            end
            @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
            @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          end
        end
      else
        if @mode=='Overall'
          #            @working_days=@batch.academic_days.count
          @academic_days=@batch.academic_days.count
        else
          working_days=@batch.working_days(@start_date.to_date)
          @academic_days=  @batch.find_working_days(@start_date,@end_date).select{|v| v<=@end_date}.count
          #            @working_days=  working_days.select{|v| v<=@end_date}.count
          #@academic_days=  working_days.select{|v| v<=@end_date}.count
        end
        @student_leaves = Attendance.find(:all,:conditions =>{:batch_id=>@batch.id,:month_date => @start_date..@end_date})
        @students.each do |student|
          on_leaves = 0;
          leaves_other = 0;
          leaves_full = 0;
          unless @student_leaves.empty?
            @student_leaves.each do |r|
              if r.student_id == student.id
                working_days_count=@batch.find_working_days(r.month_date.to_date,r.month_date.to_date).select{|v| v<=r.month_date.to_date}.count

                if working_days_count==1
                  if r.is_leave == true
                    on_leaves = on_leaves+1;
                  elsif r.forenoon==true && r.afternoon==false
                    leaves_other = leaves_other+1;
                  elsif r.forenoon==false && r.afternoon==true  
                    leaves_other = leaves_other+1;
                  else
                    leaves_full = leaves_full+1;
                  end 
                end
              end
            end
          end
          @leaves[student.id]['late'] = leaves_other
          @leaves[student.id]['absent'] = leaves_full
          @leaves[student.id]['on_leave'] = on_leaves
          @leaves[student.id]['present'] = @academic_days-on_leaves-leaves_full
          @leaves[student.id]['total']=@academic_days-leaves_full.to_f-(0.5*leaves_other)
          @leaves[student.id]['percent'] = (@leaves[student.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
        end
#        leaves_forenoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>true,:afternoon=>false,:month_date => @start_date..@end_date},:group=>:student_id)
#        leaves_afternoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>false,:afternoon=>true,:month_date => @start_date..@end_date},:group=>:student_id)
#        leaves_full=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>true,:afternoon=>true,:month_date => @start_date..@end_date},:group=>:student_id)
#        @students.each do |student|
#          @leaves[student.id]['total']=@academic_days-leaves_full[student.id].to_f-(0.5*(leaves_forenoon[student.id].to_f+leaves_afternoon[student.id].to_f))
#          @leaves[student.id]['percent'] = (@leaves[student.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
#        end
      end
    else
      @report = ''
    end
    render :pdf => 'report_pdf'
  end

  def filter_report_pdf
    @config = Configuration.find_by_config_key('StudentAttendanceType')
    @batch = Batch.find(params[:batch])
    @students = @batch.students.by_first_name
    @start_date = (params[:start_date]).to_date
    @end_date = (params[:end_date]).to_date
    @range = (params[:range])
    @value = (params[:value])
    @mode=params[:report_type]
    @leaves=Hash.new { |h, k| h[k] = Hash.new(&h.default_proc) }
    unless @start_date > @local_tzone_time.to_date
      unless @config.config_value == 'Daily'
        if params[:subject].present?
          @subject = Subject.find params[:subject]
          @academic_days=@batch.subject_hours(@start_date, @end_date, params[:subject]).values.flatten.compact.count
          @grouped = SubjectLeave.find_all_by_subject_id(@subject.id,  :conditions =>{:month_date => @start_date..@end_date}).group_by(&:student_id)
          @batch.students.by_first_name.each do |s|
            if @grouped[s.id].nil?
              @leaves[s.id]['leave']=0
            else
              @leaves[s.id]['leave']=@grouped[s.id].count
            end
            @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
            @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          end
        else
          @academic_days=@batch.subject_hours(@start_date, @end_date, 0).values.flatten.compact.count
          @grouped = @batch.subject_leaves.find(:all,  :conditions =>{:month_date => @start_date..@end_date}).group_by(&:student_id) unless @batch.nil?
          @batch.students.by_first_name.each do |s|
            if @grouped[s.id].nil?
              @leaves[s.id]['leave']=0
            else
              @leaves[s.id]['leave']=@grouped[s.id].count
            end
            @leaves[s.id]['total'] = (@academic_days - @leaves[s.id]['leave'])
            @leaves[s.id]['percent'] = (@leaves[s.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
          end
        end
      else
        if @mode=='Overall'
          #            @working_days=@batch.academic_days.count
          @academic_days=@batch.academic_days.count
        else
          working_days=@batch.working_days(@start_date.to_date)
          @academic_days=  @batch.find_working_days(@start_date,@end_date).select{|v| v<=@end_date}.count
          #            @working_days=  working_days.select{|v| v<=@end_date}.count
          #@academic_days=  working_days.select{|v| v<=@end_date}.count
        end
        @students = @batch.students.by_first_name
        @student_leaves = Attendance.find(:all,:conditions =>{:batch_id=>@batch.id,:month_date => @start_date..@end_date})
        @students.each do |student|
          on_leaves = 0;
          leaves_other = 0;
          leaves_full = 0;
          unless @student_leaves.empty?
            @student_leaves.each do |r|
              if r.student_id == student.id
                working_days_count=@batch.find_working_days(r.month_date.to_date,r.month_date.to_date).select{|v| v<=r.month_date.to_date}.count

                if working_days_count==1
                  if r.is_leave == true
                    on_leaves = on_leaves+1;
                  elsif r.forenoon==true && r.afternoon==false
                    leaves_other = leaves_other+1;
                  elsif r.forenoon==false && r.afternoon==true  
                    leaves_other = leaves_other+1;
                  else
                    leaves_full = leaves_full+1;
                  end 
                end
              end
            end
          end
          @leaves[student.id]['late'] = leaves_other
          @leaves[student.id]['absent'] = leaves_full
          @leaves[student.id]['on_leave'] = on_leaves
          @leaves[student.id]['present'] = @academic_days-on_leaves-leaves_full
          @leaves[student.id]['total']=@academic_days-leaves_full.to_f-(0.5*leaves_other)
          @leaves[student.id]['percent'] = (@leaves[student.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
        end
#        leaves_forenoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>true,:afternoon=>false,:month_date => @start_date..@end_date},:group=>:student_id)
#        leaves_afternoon=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>false,:afternoon=>true,:month_date => @start_date..@end_date},:group=>:student_id)
#        leaves_full=Attendance.count(:all,:conditions=>{:batch_id=>@batch.id,:is_leave=>0,:forenoon=>true,:afternoon=>true,:month_date => @start_date..@end_date},:group=>:student_id)
#        @students.each do |student|
#          @leaves[student.id]['total']=@academic_days-leaves_full[student.id].to_f-(0.5*(leaves_forenoon[student.id].to_f+leaves_afternoon[student.id].to_f))
#          @leaves[student.id]['percent'] = (@leaves[student.id]['total'].to_f/@academic_days)*100 unless @academic_days == 0
#        end
        #        @report = @batch.attendances.find(:all,:conditions =>{:month_date => @start_date..@end_date})
        #          @report = @batch.attendances.find(:all,:conditions =>{:month_date => @start_date..@end_date})
      end
    else
      @report = ''
    end
    render :pdf => 'filter_report_pdf'
  end




  #    respond_to do |format|
  #      format.pdf { render :layout => false }
  #    end
end