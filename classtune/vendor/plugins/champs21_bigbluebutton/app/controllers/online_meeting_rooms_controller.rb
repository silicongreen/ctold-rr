#Copyright 2010 teamCreative Private Limited
#This product includes software developed at
#Project Champs21 - http://www.champs21.com/
#
#Licensed under the Apache License, Version 2.0 (the "License");
#you may not use this file except in compliance with the License.
#You may obtain a copy of the License at
#
#  http://www.apache.org/licenses/LICENSE-2.0
#
#Unless required by applicable law or agreed to in writing,
#software distributed under the License is distributed on an
#"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
#KIND, either express or implied.  See the License for the
#specific language governing permissions and limitations
#under the License.
class OnlineMeetingRoomsController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only=>[:index]
  before_filter :default_time_zone_present_time
  filter_access_to :all


  #respond_to :html, :except => :running
  #respond_to :json, :only => [:running, :show, :new, :index, :create, :update, :end, :destroy]

  def index
    @date=@local_tzone_time.to_date
    if current_user.admin?
      @rooms = OnlineMeetingRoom.all(:conditions=>"(scheduled_on >= '#{@date.strftime("%Y-%m-%d 00:00:00")}' and is_active = #{true} and scheduled_on <= '#{@date.strftime("%Y-%m-%d 23:59:59")}' )",:order=>"id DESC")
      @inactive_rooms = OnlineMeetingRoom.all(:conditions=>"(scheduled_on >= '#{@date.strftime("%Y-%m-%d 00:00:00")}' and is_active = #{false} and scheduled_on <= '#{@date.strftime("%Y-%m-%d 23:59:59")}' )",:order=>"id DESC")
    else
      @rooms = OnlineMeetingRoom.rooms_for_user(current_user,@date)
      if current_user.employee?
        @inactive_rooms = OnlineMeetingRoom.rooms_for_user_inactive(current_user,@date)
      end
    end
    @current_user = current_user
  end

  def show
    @room = OnlineMeetingRoom.find(params[:id])
  end

  def new
    @room = OnlineMeetingRoom.new
    load_data
  end

  def edit
    @room = OnlineMeetingRoom.find(params[:id])
    if @room.user_id == current_user.id or current_user.admin
      @recipients = @room.members.sort_by{|a| a.full_name.downcase}
      load_data
    else
      flash[:notice] = "#{t('access_denied')}"
      redirect_to :action => "index" and return
    end
  end

  def create
    unless params[:student_list].blank?
      @room = OnlineMeetingRoom.new(params[:online_meeting_room])
      @room.user_id = current_user.id
      
      unless params[:select_user].blank?
        unless params[:select_user][:employee].blank?
          @room.moderator_id = params[:select_user][:employee]
        else
          @room.moderator_id = current_user.id
        end
      else
        @room.moderator_id = current_user.id
      end
      
      params[:recipients] = params[:student_list].join(',')
      #abort(params[:recipients].inspect)
      @room.member_ids = params[:recipients].split(",").reject{|a| a.strip.blank?}.collect{ |s| s.to_i }.uniq
      
      unless params[:moderator_password].blank?
        @room.moderator_password = params[:moderator_password]
      end
      unless params[:attendee_password].blank?
        @room.attendee_password = params[:attendee_password]
      end
      
      unless params[:student_list].blank?
        @room.max_participants = params[:student_list].length
      else
        @room.max_participants = 30
      end
      
      respond_to do |format|
        if @room.save
          #room_tmp = OnlineMeetingRoom.find(@room.id)
          #room_tmp.update_attributes(:logout_url => @room.logout_url + "/" + @room.id.to_s + "/end_meeting")
          
          settings = ["mute_user", "disabled_mic", "disabled_webcam", "private_chat", "recording"]
          settings.each do |ss|
            online_meeting_settings = OnlineMeetingSetting.new
            online_meeting_settings.online_meeting_room_id = @room.id
            online_meeting_settings.config_name = ss
            online_meeting_settings.config_value = 0
            online_meeting_settings.save
          end
          online_meeting_settings = OnlineMeetingSetting.new
          online_meeting_settings.online_meeting_room_id = @room.id
          online_meeting_settings.config_name = "duration"
          online_meeting_settings.config_value = 0
          online_meeting_settings.save
          
          unless params[:meeting_settings].blank?
            meeting_settings = params[:meeting_settings]
            #abort(meeting_settings.inspect)
            meeting_settings.each do |ss|
              online_meeting_settings = OnlineMeetingSetting.find_by_online_meeting_room_id_and_config_name(@room.id, ss)
              unless online_meeting_settings.blank?
                online_meeting_settings.update_attributes(:config_value => 1)
              else
                online_meeting_settings = OnlineMeetingSetting.new
                online_meeting_settings.online_meeting_room_id = @room.id
                online_meeting_settings.config_name = ss
                online_meeting_settings.config_value = 1
                online_meeting_settings.save
              end
            end
          end
          
          unless params[:meeting_duration].blank?
            online_meeting_settings = OnlineMeetingSetting.find_by_online_meeting_room_id_and_config_name(@room.id, "duration")
            unless online_meeting_settings.blank?
              online_meeting_settings.update_attributes(:config_value => params[:meeting_duration])
            else
              online_meeting_settings = OnlineMeetingSetting.new
              online_meeting_settings.online_meeting_room_id = @room.id
              online_meeting_settings.config_name = "duration"
              online_meeting_settings.config_value = params[:meeting_duration]
              online_meeting_settings.save
            end
          end
          
          message = t('online_meeting_room_created_successfully')
          format.html {
            params[:redir_url] ||= online_meeting_rooms_path
            flash[:notice] = message
            redirect_to params[:redir_url]
          }
          format.json { render :json => { :message => message }, :status => :created }
        else
          @recipients=User.find_all_by_id(params[:recipients].split(","))
          message = t('failed_to_create_online_meeting_room')
          flash[:notice] = message
          redirect_to :action => "new" and return
        end
      end
    else
      unless params[:redir_url].blank?
        message = t('failed_to_create_online_meeting_room')
        redirect_to params[:redir_url], :error => message
      else
        load_data
        render :action => "new"
      end
    end
  end

  def update_recipient_list
    recipients_array = params[:recipients].split(",").collect{ |s| s.to_i }
    @recipients = User.active.find_all_by_id(recipients_array).sort_by{|a| a.full_name.downcase}
    render :update do |page|
      page.replace_html 'recipient-list', :partial => 'recipient_list'
    end
  end

  def list_employees
    unless params[:id] == ''
      @employees = Employee.find(:all, :conditions=>{:employee_department_id => params[:id]},:order=>"id DESC")
    else
      @employees = []
    end
    render(:update) do |page|
      page.replace_html 'select_employees', :partial=> 'list_employees'
    end
  end

  def select_employee_department
    @user = current_user
    @departments = EmployeeDepartment.find(:all, :conditions=>"status = true")
    render :partial=>"select_employee_department"
  end

  def select_users
    @user = current_user
    users = User.find(:all, :conditions=>"student = false")
    @to_users = users.map { |s| s.id unless s.nil? }
    render :partial=>"to_users", :object => @to_users
  end

  def select_student_course
    @user = current_user
    @batches = Batch.active
    render :partial=> "select_student_course"
  end

  def to_employees
    if params[:dept_id] == ""
      render :update do |page|
        page.replace_html "to_users", :text => ""
      end
      return
    end
    department = EmployeeDepartment.find(params[:dept_id])
    employees = department.employees(:include=>:user).sort_by{|a| a.full_name.downcase}
    @to_users = employees.map { |s| s.user }.compact||[]
    
    #if current_user.employee?
    #  @employee_id = current_user.id
    #end
    
    render :update do |page|
      page.replace_html 'to_users', :partial => 'to_users', :object => @to_users
      page << 'j(document).ready(function(){ j("#select_user_employee").select2(); });'
    end
  end
  
  def get_options
    if params[:employee_id] == ""
      render :update do |page|
        page.replace_html "option_list", :text => ""
      end
      return
    end
    
    employee = User.find(params[:employee_id])
    emp_record = employee.employee_record 
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
    #@subjects = @subjects.map { |s| s.user }.compact||[]
    
    @subject = Subject.find_all_by_id(@subjects.map(&:id))
    unless @subject.nil?
      @students = []
      @subject.each do |sub|
        if sub.elective_group_id.nil?
          students_all = Student.find_all_by_batch_id(sub.batch_id)
          students_all.each do |std|
            @students << std
          end
        else
          assigned_students = StudentsSubject.find_all_by_subject_id_and_batch_id(sub.id,sub.batch_id)
          unless assigned_students.blank?
            assigned_students.each do |std|
              unless std.student.blank?
                unless std.student.batch_id.blank?
                  if sub.batch_id == std.student.batch_id
                    @students << std.student
                  end
                end
              end 
            end
          end
          
        end
      end
      @students=@students.compact
      @students.reject!{|e| e.is_deleted == true}
    end
    
    render :update do |page|
      page.replace_html 'option_list', :partial => 'option_list'
      page.replace_html 'subject_list', :partial => 'subject_list', :object => @subjects
      page.replace_html 'student_list', :partial=>"student_list", :object => @students
      page << 'j("#submit-btn").show();'
    end
  end
  
  def get_subjects
    if params[:employee_id] == ""
      render :update do |page|
        page.replace_html "subject_list", :text => ""
      end
      return
    end
    employee = User.find(params[:employee_id])
    emp_record = employee.employee_record 
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
    #@subjects = @subjects.map { |s| s.user }.compact||[]
    
    @subject = Subject.find_all_by_id(@subjects.map(&:id))
    unless @subject.nil?
      @students = []
      @subject.each do |sub|
        if sub.elective_group_id.nil?
          students_all = Student.find_all_by_batch_id(sub.batch_id)
          students_all.each do |std|
            @students << std
          end
        else
          assigned_students = StudentsSubject.find_all_by_subject_id_and_batch_id(sub.id,sub.batch_id)
          unless assigned_students.blank?
            assigned_students.each do |std|
              unless std.student.blank?
                unless std.student.batch_id.blank?
                  if sub.batch_id == std.student.batch_id
                    @students << std.student
                  end
                end
              end 
            end
          end
          
        end
      end
      @students=@students.compact
      @students.reject!{|e| e.is_deleted == true}
    end
    
    render :update do |page|
      page.replace_html 'subject_list', :partial => 'subject_list', :object => @subjects
      page.replace_html 'student_list', :partial=>"student_list", :object => @students
      page << 'j("#loader_option").hide();'
      page << 'j("#submit-btn").show();'
    end
  end
  
  def get_courses
    if params[:employee_id] == ""
      render :update do |page|
        page.replace_html "course_list", :text => ""
      end
      return
    end
    employee = User.find(params[:employee_id])
    emp_record = employee.employee_record 
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
    unless @subjects.blank?
      batches_ids = @subjects.map{|s| s.batch.id}.uniq
    end
    @batches = Batch.find(:all, :conditions => "id IN (#{batches_ids.join(",")})")
    @batches.sort_by{|batch| batch.course.code.to_i}
    #@subjects = @subjects.map { |s| s.user }.compact||[]
    
    @batches = Batch.find_all_by_id(@batches.map(&:id))
    unless @batches.nil?
      @students = []
      @batches.each do |batch|
        students_all = Student.find_all_by_batch_id(batch.id)
        students_all.each do |std|
          @students << std
        end
      end
      @students=@students.compact
      @students.reject!{|e| e.is_deleted == true}
    end
      
    render :update do |page|
      page.replace_html 'course_list', :partial => 'course_list', :object => @batches
      page.replace_html 'student_list', :partial=>"student_list", :object => @students
      page << 'j("#loader_option").hide();'
      page << 'j("#submit-btn").show();'
    end
  end
  
  def subject_students_list
    unless params[:subject_id].blank?
      subject_batch_ids = params[:subject_id].split(",")
      subject_ids = []
      subject_batch_ids.each do |subject_batch_id|
        subject_batch_id_comb = subject_batch_id.split("---")
        subject_ids << subject_batch_id_comb[0].to_i
      end
      students_id = []
      @subject = Subject.find_all_by_id(subject_ids)
      unless @subject.nil?
        @students = []
        @subject.each do |sub|
          if sub.elective_group_id.nil?
            batch_ids = []
            subject_batch_ids.each do |subject_batch_id|
              subject_batch_id_comb = subject_batch_id.split("---")
              if  sub.id.to_i == subject_batch_id_comb[0].to_i
                batch_ids << subject_batch_id_comb[1]
              end
            end
            students_all = Student.find(:all, :conditions => "batch_id IN (#{batch_ids.join(",")})")
            unless students_all.blank?
              students_all.each do |std|
                unless students_id.include?(std.id)
                  @students << std
                  students_id << std.id
                end
              end
            end
          else
            batch_ids = []
            subject_batch_ids.each do |subject_batch_id|
              subject_batch_id_comb = subject_batch_id.split("---")
              if  sub.id.to_i == subject_batch_id_comb[0].to_i
                batch_ids << subject_batch_id_comb[1]
              end
            end
            assigned_students = StudentsSubject.find(:all, :conditions => "subject_id = #{sub.id} and batch_id IN #{batch_ids.join(",")}")
            unless assigned_students.blank?
              assigned_students.each do |std|
                unless std.student.blank?
                  unless std.student.batch_id.blank?
                    unless students_id.include?(std.student.id)
                      @students << std.student
                      students_id << std.student.id
                    end
                  end
                end 
              end
            end

          end
        end
        @students=@students.compact
        @students.reject!{|e| e.is_deleted == true}
      end
    end
    render(:update) do |page|
      page.replace_html 'student_list', :partial=>"student_list"
      page << 'j("#loader_student").hide();'
      page << 'j("#submit-btn").show();'
    end
  end
  
  def course_students_list
    @batches = Batch.find_all_by_id(params[:course_id].split(","))
    unless @batches.nil?
      @students = []
      @batches.each do |batch|
        students_all = Student.find_all_by_batch_id(batch.id)
        students_all.each do |std|
          @students << std
        end
      end
      @students=@students.compact
      @students.reject!{|e| e.is_deleted == true}
    end
    render(:update) do |page|
      page.replace_html 'student_list', :partial=>"student_list"
      page << 'j("#loader_student").hide();'
      page << 'j("#submit-btn").show();'
    end
  end

  def to_students
    if params[:batch_id] == ""
      render :update do |page|
        page.replace_html "to_users2", :text => ""
      end
      return
    end
    batch = Batch.find(params[:batch_id])
    students = batch.students(:include=>:user).sort_by{|a| a.full_name.downcase}
    @to_users = students.map { |s| s.user }.compact||[]
    render :update do |page|
      page.replace_html 'to_users2', :partial => 'to_users', :object => @to_users
    end
  end

  def load_data
    @servers = OnlineMeetingServer.all
    @departments = EmployeeDepartment.active(:order=>"name asc")
    @batches = Batch.active
    if current_user.employee?
      emp_record = current_user.employee_record 
      @employee_department_id = emp_record.employee_department_id
    end
  end

  def update
    @room = OnlineMeetingRoom.find(params[:id])
    @room.user_id = current_user.id
    respond_to  do |format|
      @room.member_ids = params[:recipients].split(",").reject{|a| a.strip.blank?}.collect{ |s| s.to_i }
      @room.save
      if @room.update_attributes(params[:online_meeting_room])
        message = t('online_meeting_room_successfully_updated')
        flash[:notice] = message
        format.html {
          params[:redir_url] ||= online_meeting_room_path(:id=>@room)
          redirect_to params[:redir_url], :notice => message
        }
        format.json { render :json => { :message => message } }
      else
        @recipients=User.find_all_by_id(params[:recipients].split(","))
        format.html {
          unless params[:redir_url].blank?
            message = t('failed_to_update_online_meeting_room')
            redirect_to params[:redir_url], :error => message
          else
            load_data
            render :action => "edit"
          end
        }
        format.json { render :json => @room.errors.full_messages, :status => :unprocessable_entity }
      end
    end
  end

  def destroy
    @room = OnlineMeetingRoom.find(params[:id])

    # TODO Destroy the room record even if end_meeting failed?

    error = false
    begin
      @room.fetch_is_running?
      @room.send_end if @room.is_running?
    rescue BigBlueButton::BigBlueButtonException => e
      error = true
      message = e.to_s
      # TODO Better error message: "Room destroyed in DB, but not in BBB..."
    end

    online_meeting_settings = OnlineMeetingSetting.find_all_by_online_meeting_room_id(@room.id)
    online_meeting_settings.each do |om|
      om.destroy
    end
    @room.destroy

    respond_to do |format|
      format.html {
        flash[:error] = message if error
        params[:redir_url] ||= online_meeting_rooms_url
        redirect_to params[:redir_url]
      }
      if error
        format.json { render :json => { :message => message }, :status => :error }
      else
        message = t('online_meeting_room_successfully_destroyed')
        format.json { render :json => { :message => message } }
      end
    end
    flash[:notice] = "#{t('online_meeting_room_successfully_destroyed')}"
  end

  # Used by logged users to join public rooms.
  def join
    @room = OnlineMeetingRoom.find(params[:id])
    role = @room.user_role(current_user)
    unless role == :denied
      begin
        join_internal(current_user.full_name, role, :join)
      rescue  BigBlueButton::BigBlueButtonException => e
        flash[:notice] = e.to_s
        redirect_to :action => "index"
      end
    else
      flash[:notice] = "#{t('access_denied')}"
      redirect_to :index and return
    end
  end


  def running
    @room = OnlineMeetingRoom.find(params[:id])

    begin
      @room.fetch_is_running?
    rescue BigBlueButton::BigBlueButtonException => e
      flash[:error] = e.to_s
      render :json => { :running => "false", :error => "#{e.to_s}" }
    else
      render :json => { :running => "#{@room.is_running?}" }
    end

  end
  
  def mark_active_meeting
    @room = OnlineMeetingRoom.find(params[:id])

    @room.make_active
    flash[:notice] = "Meeting has been Marked as active"
    redirect_to :action => "index" and return
  end

  def end_meeting
    @room = OnlineMeetingRoom.find(params[:id])

    error = false
    begin
      @room.fetch_is_running?
      if @room.is_running?
        @room.send_end
        @room.make_inactive
        message = t('online_meeting_successfully_ended')
      else
        @room.make_inactive
        message = t('end_failure_online_meeting_not_running')
      end
    rescue BigBlueButton::BigBlueButtonException => e
      error = true
      message = e.to_s
    end

    #@room.make_inactive
    #message = t('online_meeting_successfully_ended')
    if error
      respond_to do |format|
        format.html {
          flash[:error] = message
          redirect_to request.referer
        }
        format.json { render :json => message, :status => :error }
      end
    else
      respond_to do |format|
        format.html {
          redirect_to(online_meeting_room_path(@room), :notice => message)
        }
        format.json { render :json => message }
      end
    end
    flash[:notice] = "#{t('online_meeting_successfully_ended')}"
  end
  
  def logout_from_meeting
    @room = OnlineMeetingRoom.find(params[:id])

    error = false
    begin
      @room.fetch_is_running?
      if @room.is_running?
        @room.send_end
        message = t('online_meeting_successfully_ended')
      end
    rescue BigBlueButton::BigBlueButtonException => e
      error = true
      message = e.to_s
    end
    @room.update_attributes(:is_running => false)
    flash[:notice] = message
    redirect_to :action => "index" and return
  end

  def view_meetings_by_date
    @date = (params[:meetings][:search_date]).to_date
    
    if current_user.admin?
      @rooms = OnlineMeetingRoom.all(:conditions=>"(scheduled_on >= '#{@date.strftime("%Y-%m-%d 00:00:00")}' and is_active = #{true} and scheduled_on <= '#{@date.strftime("%Y-%m-%d 23:59:59")}' )",:order=>"id DESC")
      @inactive_rooms = OnlineMeetingRoom.all(:conditions=>"(scheduled_on >= '#{@date.strftime("%Y-%m-%d 00:00:00")}' and is_active = #{false} and scheduled_on <= '#{@date.strftime("%Y-%m-%d 23:59:59")}' )",:order=>"id DESC")
    else
      @rooms = OnlineMeetingRoom.rooms_for_user(current_user,@date)
      if current_user.employee?
        @inactive_rooms = OnlineMeetingRoom.rooms_for_user_inactive(current_user,@date)
      end
    end

    render :update do|page|
      page.replace_html "activities", :partial=>"date_show"
      page.replace_html "event-table", :partial=>"meetings"
      flash[:notices]= "#{t('online_meeting_for_selected_date')}"
    end
  end


  protected

  def join_internal(username, role, wait_action)


    @room.fetch_is_running?

    # if the current user is a moderator, create the room (if needed)
    # and join it
    if role == :moderator

      add_domain_to_logout_url(@room, request.protocol, request.host)

      @room.send_create unless @room.is_running?
      join_url = @room.join_url(username, role)
      @room.update_attributes(:is_running => true)
      redirect_to(join_url)

      # normal user only joins if the conference is running
      # if it's not, wait for a moderator to create the conference
    else
      if @room.is_running?
        join_url = @room.join_url(username, role)
        redirect_to(join_url)
      else
        flash[:error] = t('authentication_failure_online_meeting_not_running')
        render :action => wait_action
      end
    end


  end

  def add_domain_to_logout_url(room, protocol, host)
    unless @room.logout_url.nil? or @room.logout_url =~ /^[a-z]+:\/\//  # matches the protocol
      unless @room.logout_url =~ /^[a-z0-9]+([\-\.]{ 1}[a-z0-9]+)*/     # matches the host domain
        @room.logout_url = host + @room.logout_url
      end
      @room.logout_url = protocol + @room.logout_url
    end
  end




  helper_method :bigbluebutton_user, :bigbluebutton_role

  def bigbluebutton_user
    @current_user
  end

  def bigbluebutton_role(room)
    if room.private or bigbluebutton_user.nil?
      :password # ask for a password
    else
      :moderator
    end
  end



end
