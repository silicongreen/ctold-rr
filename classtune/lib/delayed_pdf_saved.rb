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

class DelayedPdfSaved
  attr_accessor :request, :current_user, :local_tzone_time

  def initialize(request,current_user,local_tzone_time)
    @request = request
    @current_user = current_user
    @local_tzone_time = local_tzone_time
  end

  def perform
    @all_batches = Batch.find(:all,:conditions=>["courses.is_deleted = ? and batches.is_deleted = ?",false,false],:include => [:course],:order=>"courses.course_name ASC")
    create_user_cookie()
    previous_course_name = ""
    current_course_name = ""
    batch_ids = []
    unless @all_batches.blank?
      @all_batches.each do |batch|
        save_pdf(batch.id)
        @connect_exam = ExamConnect.active.find_all_by_batch_id(batch.id) 
        @connect_exam.each do |ec|
          save_combained_pdf(ec.id,@user_cookie_variable)
        end
        batch_ids << batch.id
        
        current_course_name = batch.course.course_name
        if previous_course_name !="" &&  previous_course_name != current_course_name
          change_status(batch_ids)
          batch_ids = []
        end
        previous_course_name = current_course_name
      end
      
      unless batch_ids.blank?
        change_status(batch_ids)
        batch_ids = []
      end
      
    end
  end
  
  private
  
  def save_pdf(batch_id)
    obj_pdf_save = PdfSave.new()
    obj_pdf_save.batch_id = batch_id
    obj_pdf_save.save
  end
  
  def change_status(batch_ids)
    all_saved_batch = PdfSave.find_all_by_batch_id(batch_ids)
    unless all_saved_batch.blank?
      all_saved_batch.each do |saved_batch|
        obj_pdf_save_edit = PdfSave.find(saved_batch.id)
        obj_pdf_save_edit.status = 1
        obj_pdf_save_edit.save
      end
    end
  end
  
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
  
  
  def save_combained_pdf(connect_exam,user_cookie_variable)
    require 'net/http'
    require 'uri'
    require "yaml"
    parsed_url = 'http://'+MultiSchool.current_school.code+'.'+@request+'/exam/split_pdf_and_save/'+connect_exam.to_s
    uri = URI(parsed_url)
    http = Net::HTTP.new(uri.host, uri.port)
    http.read_timeout = 36000000
    auth_req = Net::HTTP::Get.new(parsed_url, initheader ={'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => user_cookie_variable, "Origin"=>'' })
    http.request(auth_req)
   
  end
  
end