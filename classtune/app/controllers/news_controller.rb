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

class NewsController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only => [:index]
  filter_access_to :all
  before_filter :default_time_zone_present_time

  def add
    @news = News.new(params[:news])
    @employee_department = EmployeeDepartment.find(:all, :conditions=>"status = true",:order=>"name ASC")
    if current_user.admin?
      @batches = Batch.active
    elsif @current_user.employee?
      @batches=@current_user.employee_record.batches
      @batches+=@current_user.employee_record.subjects.collect{|b| b.batch}
      @batches=@batches.uniq unless @batches.empty?
    end  
    @news.author = current_user
    if request.post? and @news.save
      if @news.is_common == 0
        batch_id_list = params[:select_options][:batch_id] unless params[:select_options].nil?
        BatchNews.delete_all("news_id ="+@news.id.to_s)
        unless batch_id_list.nil?
          batch_id_list.each do |c|
              BatchNews.create(:news_id => @news.id,:batch_id=>c)
          end
        end
        UserNews.delete_all("news_id ="+@news.id.to_s)
        department_id_list = params[:select_options][:department] unless params[:select_options].nil?
        unless department_id_list.nil?
          DepartmentNews.delete_all("news_id ="+@news.id.to_s)
          department_id_list.each do |d|
              DepartmentNews.create(:news_id => @news.id,:department_id=>d)
          end
        end
      elsif @news.is_common == 2 or @news.is_common == 3
        student_id_list = params[:list][:student_ids] unless params[:list].nil?
        BatchNews.delete_all("news_id ="+@news.id.to_s)
        UserNews.delete_all("news_id ="+@news.id.to_s)
        DepartmentNews.delete_all("news_id ="+@news.id.to_s)
        unless student_id_list.nil?
          student_id_list.each do |c|
              UserNews.create(:news_id => @news.id,:user_id=>c)
          end
        end
      else
        UserNews.delete_all("news_id ="+@news.id.to_s)
        BatchNews.delete_all("news_id ="+@news.id.to_s)
        DepartmentNews.delete_all("news_id ="+@news.id.to_s)
      end   
      
      if @news.is_published == 1
        sms_setting = SmsSetting.new()
#        sms_setting = SmsSetting.new()
#        if sms_setting.application_sms_active
#          students = Student.find(:all,:select=>'phone2',:conditions=>'is_sms_enabled = true')
#        end
        reminder_recipient_ids = []
        batch_ids = {}
        student_ids = {}
        recipients = []
        if @news.is_common == 1
          @users = User.active.find(:all)
          reminder_recipient_ids << @users.map(&:id)
          @users.each do |u|

            if u.student == true
              student = u.student_record
              unless student.nil?
                batch_ids[u.id] = student.batch_id
                student_ids[u.id] = student.id
              end
            elsif u.parent == true
              guardian = Guardian.find_by_user_id(u.id)
              unless guardian.nil?
                student = Student.find_by_id(guardian.ward_id)
                if !student.blank?
                  batch_ids[u.id] = student.batch_id
                  student_ids[u.id] = student.id
                else
                  batch_ids[u.id] = 0
                  student_ids[u.id] =  0
                end
              else
                batch_ids[u.id] = 0
                student_ids[u.id] =  0
              end  

            else
              batch_ids[u.id] = 0
              student_ids[u.id] = 0
            end 
            
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
          
        else
          @batchnews = BatchNews.find_all_by_news_id(@news.id)
          @usernews = UserNews.find_all_by_news_id(@news.id)
          unless @batchnews.empty?
            @batchnews.each do |b|
              @batch_students = Student.find(:all, :conditions=>"batch_id = #{b.batch_id}")
              @batch_students.each do |s|
                reminder_recipient_ids << s.user_id
                batch_ids[s.user_id] = s.batch_id
                student_ids[s.user_id] = s.id
                guardians = s.student_guardian
                guardians.each do |guardian|
                  unless guardian.user_id.nil?
                    reminder_recipient_ids << guardian.user_id
                    batch_ids[guardian.user_id] = s.batch_id
                    student_ids[guardian.user_id] = s.id
                  end
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
          
          unless @usernews.empty?
              @usernews.each do |s|
                reminder_recipient_ids << s.user_id
                
                if @news.is_common == 2
                  std_data = Student.find_by_user_id(s.user_id)
                  unless std_data.blank?
                    batch_ids[std_data.user_id] = std_data.batch_id
                    student_ids[std_data.user_id] = std_data.id
                    guardians = std_data.student_guardian
                    guardians.each do |guardian|
                      unless guardian.user_id.nil?
                        reminder_recipient_ids << guardian.user_id
                        batch_ids[guardian.user_id] = std_data.batch_id
                        student_ids[guardian.user_id] = std_data.id
                      end
                    end
                  end
                else
                  batch_ids[s.user_id] = 0
                  student_ids[s.user_id] = 0
                end  
              end
              
          end
          
          @departmentnews = DepartmentNews.find_all_by_news_id(@news.id)
          unless @departmentnews.empty?
              @departmentnews.each do |d|
                @dept_emp = Employee.find(:all, :conditions=>"employee_department_id = #{d.department_id}")
                @dept_emp.each do |e|
                  reminder_recipient_ids << e.user_id
                  batch_ids[e.user_id] = 0
                  student_ids[e.user_id] = 0

                  if sms_setting.application_sms_active and sms_setting.event_news_sms_active
                    if sms_setting.employee_sms_active
                      recipients.push e.mobile_phone unless e.mobile_phone.nil?
                    end
                  end
                end
          
              end
          end    
        end 
        
     
        unless recipients.empty? or !send_sms("notice")
          message = "#{t('reminder_notice')} : "+params[:news][:title]
          Delayed::Job.enqueue(SmsManager.new(message,recipients))
        end
        unless reminder_recipient_ids.empty?
          Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
              :recipient_ids => reminder_recipient_ids,
              :subject=>@news.title,
              :rtype=>5,
              :rid=>@news.id,
              :student_id => student_ids,
              :batch_id => batch_ids,
              :body=>"#{t('reminder_notice')} : "+params[:news][:title] ))
        end
      end  
      flash[:notice] = "#{t('flash1')}"
      redirect_to :controller => 'news', :action => 'view', :id => @news.id
    end
  end
  def list_students
    @students = []
    unless params[:batch_id].blank?
      batch_ids = params[:batch_id].split(",")
      @students = Student.find_all_by_batch_id(batch_ids)
    end
    render :update do |page|
      page.replace_html 'student-list', :partial => 'list_students'
    end
  end
  def list_employees
    @employees = []
    unless params[:department_id].blank?
      department_ids = params[:department_id].split(",")
      @employees = Employee.find_all_by_employee_department_id(department_ids)
    end
    render :update do |page|
      page.replace_html 'student-list', :partial => 'list_employees'
    end
  end
  def download_attachment
    #download the  attached file
    @news = News.find params[:id]
    filename = @news.attachment_file_name
    unless @news.nil?
        send_file  @news.attachment.path , :type=>@news.attachment.content_type, :filename => filename
    else
      flash[:notice]= "#{t('flash_msg4')}"
      redirect_to :controller=>:user ,:action=>:dashboard
    end
  end

  def add_comment
    @cmnt = NewsComment.new(params[:comment])
    @current_user = @cmnt.author = current_user
    @cmnt.is_approved =true if @current_user.privileges.include?(Privilege.find_by_name('ManageNews')) || @current_user.admin?
    @cmnt.save
    show_comments_associate(@cmnt.news.id)
  end
  
  def all_draft
    if current_user.admin?
      @news = News.paginate(:conditions=>{:is_published=>0}, :page => params[:page], :per_page => 10)
    else
      @news = News.paginate(:conditions=>{:is_published=>0,:author_id=>current_user.id}, :page => params[:page], :per_page => 10)
    end  
  end
  
  def published_news
    now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    @news = News.find_by_id(params[:id])
    @news.is_published = 1
  
    if @news.save
        sms_setting = SmsSetting.new()
        reminder_recipient_ids = []
        batch_ids = {}
        student_ids = {}
        recipients = []
        if @news.is_common == 1
          @users = User.active.find(:all)
          reminder_recipient_ids << @users.map(&:id)
          @users.each do |u|

            if u.student == true
              student = u.student_record
              unless student.nil?
                batch_ids[u.id] = student.batch_id
                student_ids[u.id] = student.id
              end
            elsif u.parent == true
              guardian = Guardian.find_by_user_id(u.id)
              unless guardian.nil?
                student = Student.find_by_id(guardian.ward_id)
                if !student.blank?
                  batch_ids[u.id] = student.batch_id
                  student_ids[u.id] = student.id
                else
                  batch_ids[u.id] = 0
                  student_ids[u.id] =  0
                end
              else
                batch_ids[u.id] = 0
                student_ids[u.id] =  0
              end  

            else
              batch_ids[u.id] = 0
              student_ids[u.id] = 0
            end 
            
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
          
        else
          @batchnews = BatchNews.find_all_by_news_id(@news.id)
          @usernews = UserNews.find_all_by_news_id(@news.id)
          unless @batchnews.empty?
            @batchnews.each do |b|
              @batch_students = Student.find(:all, :conditions=>"batch_id = #{b.batch_id}")
              @batch_students.each do |s|
                reminder_recipient_ids << s.user_id
                batch_ids[s.user_id] = s.batch_id
                student_ids[s.user_id] = s.id
                guardians = s.student_guardian
                guardians.each do |guardian|
                  unless guardian.user_id.nil?
                    reminder_recipient_ids << guardian.user_id
                    batch_ids[guardian.user_id] = s.batch_id
                    student_ids[guardian.user_id] = s.id
                  end
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
          unless @usernews.empty?
              @usernews.each do |s|
                reminder_recipient_ids << s.user_id
                if @news.is_common == 2
                  std_data = Student.find_by_user_id(s.user_id)
                  unless std_data.blank?
                    batch_ids[std_data.user_id] = std_data.batch_id
                    student_ids[std_data.user_id] = std_data.id
                    guardians = std_data.student_guardian
                    guardians.each do |guardian|
                      unless guardian.user_id.nil?
                        reminder_recipient_ids << guardian.user_id
                        batch_ids[guardian.user_id] = std_data.batch_id
                        student_ids[guardian.user_id] = std_data.id
                      end
                    end
                  end
                else
                  batch_ids[s.user_id] = 0
                  student_ids[s.user_id] = 0
                end 
              end
          end
          
          @departmentnews = DepartmentNews.find_all_by_news_id(@news.id)
          unless @departmentnews.empty?
              @departmentnews.each do |d|
                @dept_emp = Employee.find(:all, :conditions=>"employee_department_id = #{d.department_id}")
                @dept_emp.each do |e|
                  reminder_recipient_ids << e.user_id
                  batch_ids[e.user_id] = 0
                  student_ids[e.user_id] = 0

                  if sms_setting.application_sms_active and sms_setting.event_news_sms_active
                    if sms_setting.employee_sms_active
                      recipients.push e.mobile_phone unless e.mobile_phone.nil?
                    end
                  end
                end
          
              end
          end    
        end 
       
        unless recipients.empty? or !send_sms("notice")
          message = "#{t('reminder_notice')} : "+params[:news][:title]
          Delayed::Job.enqueue(SmsManager.new(message,recipients))
        end
       unless reminder_recipient_ids.empty?
          Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
              :recipient_ids => reminder_recipient_ids,
              :subject=>"#{t('reminder_notice')}",
              :rtype=>5,
              :rid=>@news.id,
              :student_id => student_ids,
              :batch_id => batch_ids,
              :body=>"#{t('reminder_notice')} : "+@news.title ))
       end
       
     
     end
     flash[:notice] = "#{t('notice_successfully_published')}"
     redirect_to :controller => 'news', :action => 'all'
    
  end

  def all
    if current_user.admin? 
      @news = News.paginate(:conditions=>{:is_published=>1}, :page => params[:page], :per_page => 10)
    end
    if current_user.employee?
      @news = News.paginate(:conditions=>["is_published = 1 AND (department_news.department_id = ? or news.is_common = 1 or author_id=? or user_news.user_id = ?)", current_user.employee_record.employee_department_id,current_user.id,current_user.id], :page => params[:page], :per_page => 10,:include=>[:department_news,:user_news])
    end
    if current_user.student?
      @news = News.paginate(:conditions=>["is_published = 1 AND (batch_news.batch_id = ? or user_news.user_id = ? or news.is_common = 1)", current_user.student_record.batch_id,current_user.id], :page => params[:page], :per_page => 10,:include=>[:batch_news,:user_news]) 
    end
    if current_user.parent?
      student_id = @current_user.guardian_entry.current_ward_id 
      @std_record = Student.find(student_id)
      @news = News.paginate(:conditions=>["is_published = 1 AND (batch_news.batch_id = ?  or user_news.user_id = ? or news.is_common = 1)", @std_record.batch_id,@std_record.user_id], :page => params[:page], :per_page => 10,:include=>[:batch_news,:user_news]) 
    end  
  end

  def delete
    @news = News.find(params[:id]).destroy
    flash[:notice] = "#{t('flash2')}"
    redirect_to :controller => 'news', :action => 'all'
  end

  def delete_comment
    @comment = NewsComment.find(params[:id])
    news_id = @comment.news_id
    @comment.destroy
    show_comments_associate(news_id)
  end

  def edit
    @news = News.find(params[:id])
    @employee_department = EmployeeDepartment.find(:all, :conditions=>"status = true",:order=>"name ASC")
    if current_user.admin?
      @batches = Batch.active
    elsif @current_user.employee?
      @batches=@current_user.employee_record.batches
      @batches+=@current_user.employee_record.subjects.collect{|b| b.batch}
      @batches=@batches.uniq unless @batches.empty?
    end 
    @batchnews = BatchNews.find_all_by_news_id(@news.id)
    @departmentnews = DepartmentNews.find_all_by_news_id(@news.id)
    @usernews = UserNews.find_all_by_news_id(@news.id)
    @batches_ids = []
    @department_ids = []
    @usernews_ids = []
    if !@batchnews.blank?
      @batches_ids = @batchnews.map{|c| c.batch_id} 
    end
    
    if !@departmentnews.blank?
      @department_ids = @departmentnews.map{|d| d.department_id} 
    end
    @batch_id_for_user_news = []
    if !@usernews.blank?
      @usernews_ids = @usernews.map{|d| d.user_id} 
      @usernews.each do |d|
          if  @news.is_common == 2
            std = Student.find_by_user_id(d.user_id)
            unless std.blank? 
              if !@batch_id_for_user_news.include?(std.batch_id)
                @batch_id_for_user_news << std.batch_id
              end
            end
          elsif @news.is_common == 3
            employee = Employee.find_by_user_id(d.user_id)
            unless employee.blank? 
              if !@batch_id_for_user_news.include?(employee.employee_department_id)
                @batch_id_for_user_news = employee.employee_department_id
              end
            end
          end 
      end
      
    end
    
    if request.post? and @news.update_attributes(params[:news])
      if @news.is_common == 0
        batch_id_list = params[:select_options][:batch_id] unless params[:select_options].nil?
        BatchNews.delete_all("news_id ="+@news.id.to_s)
        UserNews.delete_all("news_id ="+@news.id.to_s)
        unless batch_id_list.nil?
          batch_id_list.each do |c|
              BatchNews.create(:news_id => @news.id,:batch_id=>c)
          end
        end
        
        department_id_list = params[:select_options][:department] unless params[:select_options].nil?
        unless department_id_list.nil?
          DepartmentNews.delete_all("news_id ="+@news.id.to_s)
          UserNews.delete_all("news_id ="+@news.id.to_s)
          department_id_list.each do |d|
              DepartmentNews.create(:news_id => @news.id,:department_id=>d)
          end
        end
      elsif @news.is_common == 2 or @news.is_common == 3
        student_id_list = params[:list][:student_ids] unless params[:list].nil?
        BatchNews.delete_all("news_id ="+@news.id.to_s)
        UserNews.delete_all("news_id ="+@news.id.to_s)
        DepartmentNews.delete_all("news_id ="+@news.id.to_s)
        unless student_id_list.nil?
          student_id_list.each do |c|
              UserNews.create(:news_id => @news.id,:user_id=>c)
          end
        end
      else
        UserNews.delete_all("news_id ="+@news.id.to_s)
        BatchNews.delete_all("news_id ="+@news.id.to_s)
        DepartmentNews.delete_all("news_id ="+@news.id.to_s)
      end 
      
      if @news.is_published == 1
        Reminder.delete_all("rtype = 5 AND rid ="+@news.id.to_s)
        sms_setting = SmsSetting.new()
        reminder_recipient_ids = []
        batch_ids = {}
        student_ids = {}
        recipients = []
        if @news.is_common == 1
          @users = User.active.find(:all)
          reminder_recipient_ids << @users.map(&:id)
          @users.each do |u|

            if u.student == true
              student = u.student_record
              unless student.nil?
                batch_ids[u.id] = student.batch_id
                student_ids[u.id] = student.id
              end
            elsif u.parent == true
              guardian = Guardian.find_by_user_id(u.id)
              unless guardian.nil?
                student = Student.find_by_id(guardian.ward_id)
                if !student.blank?
                  batch_ids[u.id] = student.batch_id
                  student_ids[u.id] = student.id
                else
                  batch_ids[u.id] = 0
                  student_ids[u.id] =  0
                end
              else
                batch_ids[u.id] = 0
                student_ids[u.id] =  0
              end  

            else
              batch_ids[u.id] = 0
              student_ids[u.id] = 0
            end 
            
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
          
        else
          @batchnews = BatchNews.find_all_by_news_id(@news.id)
          @usernews = UserNews.find_all_by_news_id(@news.id)
          unless @batchnews.empty?
            @batchnews.each do |b|
              @batch_students = Student.find(:all, :conditions=>"batch_id = #{b.batch_id}")
              @batch_students.each do |s|
                reminder_recipient_ids << s.user_id
                batch_ids[s.user_id] = s.batch_id
                student_ids[s.user_id] = s.id
                guardians = s.student_guardian
                guardians.each do |guardian|
                  unless guardian.user_id.nil?
                    reminder_recipient_ids << guardian.user_id
                    batch_ids[guardian.user_id] = s.batch_id
                    student_ids[guardian.user_id] = s.id
                  end
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
          unless @usernews.empty?
              @usernews.each do |s|
                reminder_recipient_ids << s.user_id
                if @news.is_common == 2
                  std_data = Student.find_by_user_id(s.user_id)
                  unless std_data.blank?
                    batch_ids[std_data.user_id] = std_data.batch_id
                    student_ids[std_data.user_id] = std_data.id
                    guardians = std_data.student_guardian
                    guardians.each do |guardian|
                      unless guardian.user_id.nil?
                        reminder_recipient_ids << guardian.user_id
                        batch_ids[guardian.user_id] = std_data.batch_id
                        student_ids[guardian.user_id] = std_data.id
                      end
                    end
                  end
                else
                  batch_ids[s.user_id] = 0
                  student_ids[s.user_id] = 0
                end 
              end
          end
          
          @departmentnews = DepartmentNews.find_all_by_news_id(@news.id)
          unless @departmentnews.empty?
              @departmentnews.each do |d|
                @dept_emp = Employee.find(:all, :conditions=>"employee_department_id = #{d.department_id}")
                @dept_emp.each do |e|
                  reminder_recipient_ids << e.user_id
                  batch_ids[e.user_id] = 0
                  student_ids[e.user_id] = 0

                  if sms_setting.application_sms_active and sms_setting.event_news_sms_active
                    if sms_setting.employee_sms_active
                      recipients.push e.mobile_phone unless e.mobile_phone.nil?
                    end
                  end
                end
          
              end
          end    
        end 
       
        unless recipients.empty? or !send_sms("notice")
          message = "#{t('reminder_notice')} : "+params[:news][:title]
          Delayed::Job.enqueue(SmsManager.new(message,recipients))
        end
       unless reminder_recipient_ids.empty?
          Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
              :recipient_ids => reminder_recipient_ids,
              :subject=>"#{t('reminder_notice')}",
              :rtype=>5,
              :rid=>@news.id,
              :student_id => student_ids,
              :batch_id => batch_ids,
              :body=>"#{t('reminder_notice')} : "+@news.title ))
       end
       
     
     end
      
      flash[:notice] = "#{t('flash3')}"
      redirect_to :controller => 'news', :action => 'view', :id => @news.id
    end
  end

  def index
    @current_user = current_user
    @news = []
    if request.get?
      @news = News.title_like_all params[:query].split unless params[:query].nil?
    end
  end

  def search_news_ajax
    @news = nil
    unless params[:query].nil? and params[:query] == ''
      if current_user.admin? 
        @news = News.paginate(:conditions=>["title LIKE ? and is_published=1", "%#{params[:query]}%"], :page => params[:page], :per_page => 10)
      end
      if current_user.employee?
        @news = News.paginate(:conditions=>["is_published = 1 AND (department_news.department_id = ? or news.is_common = 1 or user_news.user_id = ?) AND title LIKE ?", current_user.employee_record.employee_department_id,current_user.id,"%#{params[:query]}%"], :page => params[:page], :per_page => 10,:include=>[:department_news,:user_news])
      end
      if current_user.student?
        @news = News.paginate(:conditions=>["is_published = 1 AND (batch_news.batch_id = ? or news.is_common = 1 or user_news.user_id = ?) AND title LIKE ?", current_user.student_record.batch_id,current_user.id,"%#{params[:query]}%"], :page => params[:page], :per_page => 10,:include=>[:batch_news,:user_news]) 
      end
      if current_user.parent?
        student_id = @current_user.guardian_entry.current_ward_id 
        @std_record = Student.find(student_id)
        @news = News.paginate(:conditions=>["is_published = 1 AND (batch_news.batch_id = ? or news.is_common = 1 or user_news.user_id = ?) AND title LIKE ?", @std_record.batch_id,@std_record.user_id,"%#{params[:query]}%"], :page => params[:page], :per_page => 10,:include=>[:batch_news,:user_news]) 
      end  
    else
      if current_user.admin? 
          @news = News.paginate(:conditions=>{:is_published=>1}, :page => params[:page], :per_page => 10)
      end
      if current_user.employee?
        @news = News.paginate(:conditions=>["is_published = 1 AND (department_news.department_id = ? or news.is_common = 1 or user_news.user_id = ?)", current_user.employee_record.employee_department_id,current_user.id], :page => params[:page], :per_page => 10,:include=>[:department_news,:user_news])
      end
      if current_user.student?
        @news = News.paginate(:conditions=>["is_published = 1 AND (batch_news.batch_id = ? or news.is_common = 1 or user_news.user_id = ?)", current_user.student_record.batch_id,current_user.id], :page => params[:page], :per_page => 10,:include=>[:batch_news,:user_news]) 
      end
      if current_user.parent?
        student_id = @current_user.guardian_entry.current_ward_id 
        @std_record = Students.find(student_id)
        @news = News.paginate(:conditions=>["is_published = 1 AND (batch_news.batch_id = ? or news.is_common = 1 or user_news.user_id = ?)", @std_record.batch_id, @std_record.user_id], :page => params[:page], :per_page => 10,:include=>[:batch_news,:user_news]) 
      end 
    end
    render :partial=>"news_list"
  end

  def view
    Reminder.update_all("is_read='1'",  ["rid = ? and rtype = ? and recipient= ?", params[:id], 5,current_user.id])
    @total_obj = Reminder.find(:first,:select=>"count(distinct recipient) as total",:conditions=>["reminders.rid = ? and reminders.rtype = ? and users.is_deleted = ?", params[:id], 5, false],:joins=>[:to_user])
    @total_read_obj = Reminder.find(:first,:select=>"count(distinct recipient) as total",:conditions=>["reminders.rid = ? and reminders.rtype = ? and users.is_deleted = ? and reminders.is_read = ?", params[:id], 5, false, true],:joins=>[:to_user])
    @total = 0
    @total_read = 0
    unless @total_obj.blank?
      @total = @total_obj.total
    end
    unless @total_read_obj.blank?
      @total_read = @total_read_obj.total
    end
    
    show_comments_associate(params[:id], params[:page])
  end

  def comment_view
    show_comments_associate(params[:id], params[:page])
    render :update do |page|
      page.replace_html 'comments-list', :partial=>"comment"
    end
  end

  def comment_approved
    @comment = NewsComment.find(params[:id])
    status=@comment.is_approved ? false : true
    @comment.update_attributes(:is_approved=>status)
    render :update do |page|
      page.reload
    end
  end

  private

  def show_comments_associate(news_id, params_page=nil)
    @news = News.find_by_id(news_id, :include=>[:author])
    unless @news.blank?
      @comments = @news.comments.latest.paginate(:page => params_page, :per_page => 15, :include =>[:author])
      @current_user = current_user
      @is_moderator = false
      if @current_user.admin?
        @is_moderator = true
      elsif @current_user.employee? and @current_user.privileges.include?(Privilege.find_by_name('ManageNews')) and @news.author_id == @current_user.id
        @is_moderator = true
      end  
      @config = Configuration.find_by_config_key('EnableNewsCommentModeration')
      @permitted_to_delete_comment_news = permitted_to? :delete_comment , :news
    end
  end

end
