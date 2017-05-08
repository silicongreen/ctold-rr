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

# Configure your SMS API settings
require 'net/http'
require 'yaml'
require 'translator'

class DelayedBatchTranfer
  attr_accessor :students, :from, :to,:session,:graduation, :status_description, :leaving_date, :local_tzone_time,:current_user,:request,:prev_start,:prev_end,:next_start,:next_end

  def initialize(students, from, to, session, graduation, status_description, leaving_date, local_tzone_time,current_user,request,prev_start,prev_end,next_start,next_end)
    @students = students.split(",")
    @from = from
    @to = to
    @session = session
    @graduation = graduation
    @status_description = status_description
    @leaving_date = leaving_date
    @local_tzone_time = local_tzone_time
    @current_user = current_user
    @prev_start = prev_start
    @prev_end = prev_end
    @next_start = next_start
    @next_end = next_end
    @request = request
    
    
  end

  def perform
    unless @students.blank?
      @batch = Batch.find @from, :include => [:students],:order => "students.first_name ASC"
      @exam_groups = ExamGroup.active.find_all_by_batch_id(@batch.id)
      @connect_exam = ExamConnect.active.find_all_by_batch_id(@batch.id) 
      students = Student.find(@students)
      now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
      create_user_cookie()
      save_batch_transfer(@from,@to,@session,now)
      reminder_recipient_ids = []

      students.each do |s|
        batch_student = s.batch_students.find_or_create_by_batch_id_and_session_and_batch_start_and_batch_end(s.batch.id,@session,@prev_start,@prev_end)
        unless @exam_groups.blank?
          @exam_groups.each do |eg|
            save_group_pdf(eg.id,s.id,@user_cookie_variable)
            create_group_exam_student(@batch,s,eg,now,batch_student.id)
          end
        end
      end
     

      students.each do |s| 
        batch_student = s.batch_students.find_or_create_by_batch_id_and_session_and_batch_start_and_batch_end(s.batch.id,@session,@prev_start,@prev_end)
        unless @connect_exam.blank?
          @connect_exam.each do |ec|
            save_combained_pdf(ec.id,s.id,@user_cookie_variable,@batch.id)
            create_combined_exam_student(@batch,s,ec,now,batch_student.id) 
          end
        end
      end  

     
      
      students.each do |s|    
        if @graduation == false
          s.update_attribute(:batch_id, @to)
          s.update_attribute(:has_paid_fees,0)
          s.update_attribute(:is_promoted,1)

          reminder_recipient_ids << s.user_id            
          unless s.immediate_contact.nil? 
            reminder_recipient_ids << s.immediate_contact.user_id
          end
        end
      end
      
      
      if @graduation == false
        unless reminder_recipient_ids.empty?
          Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => @current_user.id,
              :recipient_ids => reminder_recipient_ids,
              :subject=>"Promoted",
              :rtype=>25,
              :rid=>0,
              :body=>"Congratulation. You have been promoted to new Class. Wish you all the best." ))
        end 
      end
      
      if @graduation == true
        students.each { |s| s.archive_student(@status_description,@leaving_date) }
      end
      
      
      
      @stu = Student.find_all_by_batch_id(@batch.id)
      unless @stu.empty?
          
        @stu.each do |s|
          batch_student = s.batch_students.find_or_create_by_batch_id_and_session_and_batch_start_and_batch_end(s.batch.id,@session,@prev_start,@prev_end)
          unless @exam_groups.blank?
            @exam_groups.each do |eg|
            
              save_group_pdf(eg.id,s.id,@user_cookie_variable)
              create_group_exam_student(@batch,s,eg,now,batch_student.id)
            end
          end
        end
        
        @stu.each do |s|
          batch_student = s.batch_students.find_or_create_by_batch_id_and_session_and_batch_start_and_batch_end(s.batch.id,@session,@prev_start,@prev_end)
          unless @connect_exam.blank?
            @connect_exam.each do |ec|
             
              save_combained_pdf(ec.id,s.id,@user_cookie_variable,@batch.id)
              create_combined_exam_student(@batch,s,ec,now,batch_student.id) 
            end
          end
        end 
        
      end

      unless @exam_groups.blank?
        @exam_groups.each do |eg|
          eg.update_attribute(:is_deleted,true)
        end
      end

      unless @connect_exam.blank?
        @connect_exam.each do |ec|
          ec.update_attribute(:is_deleted,true)
        end
      end 
      
      @batch.update_attribute(:start_date,@next_start+" 00:00:00")
      @batch.update_attribute(:end_date,@next_end+" 00:00:00")
      
      @attendance = AttendanceRegister.find_all_by_batch_id_and_previous(@batch.id,0)
      unless @attendance.blank?
        @attendance.each do |att|
          att.update_attribute(:previous,@batch_tranfer_id)
        end
      end
      
      @attendance = SubjectAttendanceRegister.find_all_by_batch_id_and_previous(@batch.id,0)
      unless @attendance.blank?
        @attendance.each do |att|
          att.update_attribute(:previous,@batch_tranfer_id)
        end
      end
      
      
    end
    
  end
  
  private
  
  def create_user_cookie
    require 'net/http'
    require 'uri'
    require "yaml"
    @user_name = @current_user.username
    @free_user_obj = TdsFreeUser.find_by_paid_id(@current_user.id)
    @password = @free_user_obj.paid_password


    o = [('a'..'z'), ('A'..'Z'), (0..9)].map { |i| i.to_a }.flatten
    rand_val = (0...16).map { o[rand(o.length)] }.join

    now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    t_10 = now.to_datetime + 10*60 

    @tds_user_auth = TdsUserAuth.new
    @tds_user_auth.user_id = @current_user.id
    @tds_user_auth.auth_id = rand_val
    @tds_user_auth.expire = t_10
    @tds_user_auth.save

    uri = URI('http://'+MultiSchool.current_school.code+'.'+@request+'/user/login?username='+@user_name+'&password='+@password+'&auth_id='+rand_val.to_s+'&user_id='+@current_user.id.to_s)
    http = Net::HTTP.new(uri.host, uri.port)
    auth_req = Net::HTTP::Get.new('http://'+MultiSchool.current_school.code+'.'+@request+'/user/login?username='+@user_name+'&password='+@password+'&auth_id='+rand_val.to_s+'&user_id='+@current_user.id.to_s)
    auth_res = http.request(auth_req)
    ar_user_cookie = auth_res.response['set-cookie'].split('; ')
    @user_cookie_variable = ar_user_cookie[2].split(", ")[2]
    
  end
  
  def save_group_pdf(exam_group,student,user_cookie_variable)
    require 'net/http'
    require 'uri'
    require "yaml"
    parsed_url = 'http://'+MultiSchool.current_school.code+'.'+@request+'/exam/student_wise_generated_report?exam_group='+exam_group.to_s+'&for_save=true&student='+student.to_s
    uri = URI(parsed_url)
    http = Net::HTTP.new(uri.host, uri.port)
    auth_req = Net::HTTP::Get.new(parsed_url, initheader ={'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => user_cookie_variable })
    http.request(auth_req)
  end
  def save_combained_pdf(connect_exam,student,user_cookie_variable,batch_id)
    require 'net/http'
    require 'uri'
    require "yaml"
    parsed_url = 'http://'+MultiSchool.current_school.code+'.'+@request+'/exam/generated_report5_pdf?batch_id='+batch_id.to_s+'&connect_exam='+connect_exam.to_s+'&for_save=true&student='+student.to_s
    uri = URI(parsed_url)
    http = Net::HTTP.new(uri.host, uri.port)
    auth_req = Net::HTTP::Get.new(parsed_url, initheader ={'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => user_cookie_variable })
    http.request(auth_req)
   
  end
  
  def save_batch_transfer(from,to,session,now)
    batchboj = BatchTransfer.new
    batchboj.from_id = from
    batchboj.to_id = to
    batchboj.session = session
    batchboj.created_at = now
    batchboj.updated_at = now
    batchboj.save()
    @batch_tranfer_id = batchboj.id
  end
  
  def create_group_exam_student(batch,s,eg,now,batch_student_id)
    examgroup = GroupExamStudent.new
    examgroup.batch_id = batch.id
    examgroup.student_id = s.id
    examgroup.exam_group_id = eg.id
    examgroup.batch_student_id = batch_student_id
    examgroup.year = eg.exam_date
    examgroup.created_at = now
    examgroup.updated_at = now
    examgroup.save
  end
  
  def create_combined_exam_student(batch,s,ec,now,batch_student_id)
    examconnect = ExamConnectStudent.new
    examconnect.batch_id = batch.id
    examconnect.student_id = s.id
    examconnect.exam_connect_id = ec.id
    examconnect.batch_student_id = batch_student_id
    examconnect.year = ec.published_date
    examconnect.created_at = now
    examconnect.updated_at = now
    examconnect.save
  end
  
end