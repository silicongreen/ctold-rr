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

class ClubController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only => [:index,:categories,:addclub]
  filter_access_to :all
  
  def addclub
   
    @events = Event.new(params[:events])
    @event_categories = EventCategory.active_for_club
    if params[:id].nil?
      @date = Time.now
    else
      date = params[:id].to_date
      @date = date.to_time
    end
    if request.post?
      if @events.validate_for_club 
        if @events.save
          redirect_to :action=>"show", :id=>@events.id
        else
          @start_date = params[:events][:start_date].to_date
          @end_date = params[:events][:end_date].to_date
        end  
      else
        @start_date = params[:events][:start_date].to_date
        @end_date = params[:events][:end_date].to_date
      end
    end
  end
  
  def index
    event_categories = EventCategory.find(:all,:conditions=>{:is_club=>1})
    categories = []
    event_categories.each do |event_category|
      categories << event_category.id
    end 
    @all_clubs = Event.paginate(:conditions=>{:event_category_id=>categories}, :order=>"id DESC", :page => params[:page], :per_page => 10)
  end
  def club_member
    members = EventAcknowledge.find(:all,:conditions=>{:status => 1,:acknowledged_by => 1,:event_id=>params[:id]})
    students = []
    members.each do |member| 
      students << member.acknowledged_by_id
    end 
    @all_member = Student.paginate(:conditions=>{:id=>students},  :page => params[:page], :per_page => 10)

  end

  def event_group
    @event = Event.find(params[:id])
  end

  def select_course
    @event_id = params[:id]
    @batches = Batch.active
    render :update do |page|
      page.replace_html 'select-option', :partial => 'select_course'
    end
  end

  def course_event
    event = Event.find(params[:id])
    batch_id_list = params[:select_options][:batch_id] unless params[:select_options].nil?
    unless batch_id_list.nil?
      batch_id_list.each do |c|
        batch_event_exists = BatchEvent.find_by_event_id_and_batch_id(event.id,c)
        if batch_event_exists.nil?
          BatchEvent.create(:event_id => event.id,:batch_id=>c)
          #send reminder to students
          #        @batch_students = Student.find(:all, :conditions=>"batch_id = #{c}")
          #        @batch_students.each do |s|
          #          student_user = User.find_by_username(s.admission_no)
          #          unless student_user.nil?
          #            Reminder.create(:sender => current_user.id,:recipient=>student_user.id,
          #              :subject=>"New Event : #{event.title}",
          #              :body=>" New event description : #{event.description} <br/> start date : #{event.start_date} <br/> end date : #{event.end_date}")
          #          end
          #        end
          #send reminder to students end
        end
      end
    end

    flash[:notice] = "#{t('flash1')}"
    redirect_to :action=>"show", :id => event.id
  end

  def remove_batch
    @batch_event = BatchEvent.find(params[:id])
    @event = @batch_event.event_id
    @batch_event.delete
    flash[:notice] = "#{t('batches.flash4')}"
    redirect_to :action=>"show", :id=>@event
  end

  def select_employee_department
    @event_id = params[:id]
    @employee_department = EmployeeDepartment.find(:all, :conditions=>"status = true",:order=>"name ASC")
    render :update do |page|
      page.replace_html 'select-options', :partial => 'select_employee_department'
    end
  end

  def department_event
    event = Event.find(params[:id])
    department_id_list = params[:select_options][:department_id] unless params[:select_options].nil?
    unless department_id_list.nil?
      department_id_list.each do |c|
        department_event_exists = EmployeeDepartmentEvent.find_by_event_id_and_employee_department_id(event.id,c)
        if department_event_exists.nil?
          EmployeeDepartmentEvent.create(:event_id=>event.id,:employee_department_id=>c)
          #        @dept_emp = Employee.find(:all, :conditions=>"employee_department_id = #{c}")
          #        @dept_emp.each do |e|
          #          emp_user = User.find_by_username(e.employee_number)
          #          Reminder.create(:sender => current_user.id,:recipient=>emp_user.id,
          #            :subject=>"New Event : #{event.title}",
          #            :body=>" New event description : #{event.description} <br/> start date : #{event.start_date} <br/> end date : #{event.end_date}")
          #        end
        end
      end
    end
    flash[:notice] = "#{t('flash2')}"
    redirect_to :action=>"show", :id=>event.id
  end

  def remove_department
    @department_event = EmployeeDepartmentEvent.find(params[:id])
    @event = @department_event.event_id
    @department_event.delete
    flash[:notice] = "#{t('flash4')}"
    redirect_to :action=>"show", :id=>@event
  end

  def show
    @event = Event.find(params[:id])
    @command = params[:cmd]
    event_start_date = "#{@event.start_date.year}-#{@event.start_date.month}-#{@event.start_date.day}".to_date
    event_end_date = "#{@event.end_date.year}-#{@event.end_date.month}-#{@event.end_date.day}".to_date
    @other_events = Event.find(:all, :conditions=>"id != #{@event.id}")
    if @event.is_common ==false
      @batch_events = BatchEvent.find(:all, :conditions=>"event_id = #{@event.id}")
      @department_event = EmployeeDepartmentEvent.find(:all, :conditions=>"event_id = #{@event.id}")
    end
  end

  def confirm_event
    event = Event.find(params[:id])
    reminder_subject = "#{t('new_event')} : #{event.title}"
    reminder_body = " #{t('event_description')} : #{event.description} <br/> #{t('start_date')} : " + event.start_date.strftime("%d/%m/%Y %I:%M %p") + " <br/> #{t('end_date')} : " + event.end_date.strftime("%d/%m/%Y %I:%M %p")
    reminder_recipient_ids = []
    if event.is_common == true
      if event.is_holiday == true
        @pe = PeriodEntry.find(:all, :conditions=>"month_date BETWEEN '" + event.start_date.strftime("%Y-%m-%d") + "' AND '" +  event.end_date.strftime("%Y-%m-%d") +"'")
        unless @pe.nil?
          @pe.each do |p|
            p.delete
          end
        end
      end
      @users = User.active.find(:all)
      reminder_recipient_ids << @users.map(&:id)
      sms_setting = SmsSetting.new()
      if sms_setting.application_sms_active and sms_setting.event_news_sms_active
        recipients = []
        @users.each do |u|
          if u.student == true
            student = u.student_record
            unless student.nil?
              guardian = student.immediate_contact unless student.immediate_contact.nil?
              if student.is_sms_enabled
                if sms_setting.student_sms_active
                  recipients.push student.phone2 unless student.phone2.nil?
                end
                if sms_setting.parent_sms_active
                  unless guardian.nil?
                    recipients.push guardian.mobile_phone unless guardian.mobile_phone.nil?
                  end
                end
              end
            else
              employee = u.employee_record
              if sms_setting.employee_sms_active
                unless employee.nil?
                  recipients.push employee.mobile_phone unless employee.mobile_phone.nil?
                end
              end
            end
          end
        end
        
      end
    else
      recipients = []
      sms_setting = SmsSetting.new()
      batch_event = BatchEvent.find_all_by_event_id(event.id)
      unless batch_event.empty?
        batch_event.each do |b|
          if event.is_holiday == true
            @pe = PeriodEntry.find_all_by_batch_id(b.id, :conditions=>"month_date BETWEEN '" + event.start_date.strftime("%Y-%m-%d") + "' AND '" +  event.end_date.strftime("%Y-%m-%d") +"'")
            unless @pe.nil?
              @pe.each do |p|
                p.delete
              end
            end
          end
          @batch_students = Student.find(:all, :conditions=>"batch_id = #{b.batch_id}")
          @batch_students.each do |s|
            reminder_recipient_ids << s.user_id
            unless s.immediate_contact.nil?
              reminder_recipient_ids << s.immediate_contact.user_id
            end
            if sms_setting.application_sms_active and sms_setting.event_news_sms_active
              guardian = s.immediate_contact unless s.immediate_contact.nil?
              if s.is_sms_enabled
                if sms_setting.student_sms_active
                  recipients.push s.phone2 unless s.phone2.nil?
                end
                if sms_setting.parent_sms_active
                  unless guardian.nil?
                    recipients.push guardian.mobile_phone unless guardian.mobile_phone.nil?
                  end
                end
              end
            end
          end
        end
      end
      department_event = EmployeeDepartmentEvent.find_all_by_event_id(event.id)
      unless department_event.empty?
        department_event.each do |d|
          @dept_emp = Employee.find(:all, :conditions=>"employee_department_id = #{d.employee_department_id}")
          @dept_emp.each do |e|
            reminder_recipient_ids << e.user_id
            if sms_setting.application_sms_active and sms_setting.event_news_sms_active
              if sms_setting.employee_sms_active
                recipients.push e.mobile_phone unless e.mobile_phone.nil?
              end
            end
          end
        end
      end
     
    end
    # Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
    #   :recipient_ids => reminder_recipient_ids,
    #    :subject=>reminder_subject,
    #   :body=>reminder_body ))
    redirect_to :controller=>'club',:action=>'index'
  end

  def cancel_event
    event = Event.find(params[:id])
    batch_event = BatchEvent.find(:all, :conditions=>"event_id = #{params[:id]}")
    dept_event = EmployeeDepartmentEvent.find(:all, :conditions=>"event_id = #{params[:id]}")
    event.destroy
    
    batch_event.each { |x| x.destroy } unless batch_event.nil?
    dept_event.each { |x| x.destroy } unless dept_event.nil?
    flash[:notice] ="#{t('flash3')}"
    redirect_to :action=>"index"
  end
  def delete_event
    event = Event.find(params[:id])
    event.delete
    redirect_to :action=>"index"
  end

  def edit_event
    @event = Event.find_by_id(params[:id])
    @event_categories = EventCategory.active_for_club
    if @event.nil?
      page_not_found
    end
    if request.post? and @event.update_attributes(params[:event])
      redirect_to :action=>"show", :id=>@event.id, :cmd=>'edit'
    end
  end
  
  def categories
    @event_categories = EventCategory.active_for_club
    @event_category = EventCategory.new(params[:event_category])
    @images = Dir.glob("#{RAILS_ROOT}/public/images/icons/clubs/*.png")
    if request.post?
      if @event_category.save
        flash[:notice] = "#{t('flash7')}"
        redirect_to :action => 'categories'
      end
    end
  end

  def category_delete
    @event_category = EventCategory.update(params[:id], :status=>0)
    @event_categories = EventCategory.active_for_club
  end

  def category_edit
    @event_category = EventCategory.find(params[:id])
    @event_category_name = @event_category.name
    @images = Dir.glob("#{RAILS_ROOT}/public/images/icons/clubs/*.png")
  end

  def category_update
    @event_category = EventCategory.find(params[:id])
    @event_category_name = @event_category.name
    @images = Dir.glob("#{RAILS_ROOT}/public/images/icons/clubs/*.png")
    if @event_category.update_attributes(:name => params[:name], :icon_number => params[:icon_number])
      @event_categories = EventCategory.active_for_club
      @event_category = EventCategory.new
    end
  end

end



