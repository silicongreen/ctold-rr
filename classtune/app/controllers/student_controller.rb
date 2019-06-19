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

class StudentController < ApplicationController
  filter_access_to :all, :except=>[:reports,:get_student_ajax]
  filter_access_to [:reports,:get_student_ajax], :attribute_check=>true, :load_method => lambda { current_user }
  before_filter :login_required
  before_filter :check_permission, :only=>[:index,:admission1,:profile,:reports,:categories,:add_additional_details]
  before_filter :set_precision
  before_filter :protect_other_student_data, :except =>[:edit_guardian_own,:generate_ssl_url,:get_previous_exam,:update_is_promoted,:insert_into_new_parent_student_table,:show,:class_test_report,:previous_batch_report,:combined_exam,:progress_report,:class_test_report_single,:term_test_report]
  before_filter :default_time_zone_present_time
  before_filter :only_allowed_when_parmitted , :only=>[:edit_guardian_own,:edit_student_guardian]
  
  protect_from_forgery :except => [:fee_details]
  
  before_filter :find_student, :only => [:previous_report,
    :academic_report, :academic_report_all, :admission3, :change_to_former,
    :delete, :edit, :add_guardian, :email, :remove, :reports, 
    :guardians, :academic_pdf,:show_previous_details,:fees,:fee_details, :form_to_apply, :noc_letter, :noc_letter_update, :close_letter
  ]
  CONN = ActiveRecord::Base.connection
  def get_section_by_class
    class_name = params[:id]
    school_id = MultiSchool.current_school.id
    @section = Course.active.find(:all, :group => "`section_name`",:conditions=>"course_name LIKE '%#{class_name}%' AND school_id = #{school_id}")
    render :update do |page|
      page.replace_html "studentSection", :partial => "get_section_by_class"
      page << 'jq(".js-example-basic-single").select2();'
     end
  end	 
  def graduation_lists
    @schoo_batch_id = Batch.all.map(&:id)
    @graduation_session = BatchTransfer.find(:all,:conditions=>["from_id IN (?) and to_id = ?",@schoo_batch_id,0],:limit=>100,:order=>'created_at DESC')
  end
  
  def generate_ssl_url
    require 'net/http'
    require 'uri'
    require "yaml"
    
    testssl = false
    if PaymentConfiguration.config_value('is_test_sslcommerz').to_i == 1
      if File.exists?("#{Rails.root}/vendor/plugins/champs21_pay/config/payment_config.yml")
        payment_configs = YAML.load_file(File.join(Rails.root,"vendor/plugins/champs21_pay/config/","payment_config.yml"))
        unless payment_configs.nil? or payment_configs.empty? or payment_configs.blank?
          testssl = payment_configs["testsslcommer"]
        end
      end
    end
    if testssl
      api_uri = URI(payment_configs["session_api_to_generate_transaction"])
    else  
      api_uri = URI("https://securepay.sslcommerz.com/gwprocess/v3/api_convenient_fee.php")
    end
    
    http = Net::HTTP.new(api_uri.host, api_uri.port)
    http.use_ssl = true
    request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded' })
    request.set_form_data({"tran_id"=>params[:tran_id],"store_id"=>params[:store_id],"store_passwd"=>params[:store_passwd],"cart[0][product]"=>params[:product],"cart[0][amount]"=>params[:amount],"total_amount"=>params[:total_amount],"success_url"=>params[:success_url],"fail_url"=>params[:fail_url],"cancel_url"=>params[:cancel_url],"version"=>params[:version]})
    response = http.request(request)
    @response_ssl = JSON::parse(response.body)
    
    if @response_ssl['status'] == "FAILED" 
      flash[:notice] = @response_ssl['failedreason']
      redirect_to params[:ret_url]
      #redirect_to :controller => "student", :action => "fee_details", :id=>13429, :id2=>1569
    else
      redirect_to @response_ssl['GatewayPageURL']
    end
    
  end
  
  def remove_photo
    @student = Student.find(params[:id])
    @student.photo.destroy
    @student.photo.clear
    @student.save
    flash[:notice] = "Photo Successfully Removed"
    redirect_to :controller => "student", :action => "profile", :id=>params[:id]
  end
  
  def get_graduation_students
    @archived_students = []
    unless params[:transfer_id].blank?
      transfer_session = BatchTransfer.find params[:transfer_id]
      unless transfer_session.blank?
        @archived_students = ArchivedStudent.find_all_by_batch_id_and_status_description(transfer_session.from_id,transfer_session.session)
      end
    end
    render :update do |page|
      page.replace_html 'students', :partial => 'get_graduation_students'
    end
  end
  
  def get_previous_exam
    unless params[:batch_id].blank?
      @batch_previous = BatchStudent.find(params[:batch_id])
      @previous_exam = ExamConnectStudent.find_all_by_batch_student_id(@batch_previous.id)
      @previous_group_exam = GroupExamStudent.find_all_by_batch_student_id(@batch_previous.id)
      render :update do |page|
        page.replace_html 'exams', :partial => 'get_previous_exam'
      end
    else
      render :update do |page|
        page.replace_html 'exams', :text => 'Select A Class'
      end
    end  
      
  end
  def save_roll_no
    params[:student].each_pair do |student_id, details|
      @std = Student.find(student_id)
      @std.update_attribute("class_roll_no",details[:class_roll_no])
    end
    render :text=> "Save Succesfully"
  end
  
  def previous_report
    @previous_batches = BatchStudent.find_all_by_student_id(@student.id)
    @previous_batch = []
    @batch_ids = []
    @group_exam_count = {}
    @position = {}
    @connect_exam_count = {}
    @iloop = 0
    unless @previous_batches.blank?
      @previous_batches.each do |pv|
        unless pv.batch_id == @student.batch_id
          @previous_exam = ExamConnectStudent.find_all_by_batch_student_id(pv.id)
          @previous_group_exam = GroupExamStudent.find_all_by_batch_student_id(pv.id)
          if !@previous_exam.blank? or !@previous_group_exam.blank?
            
            unless @batch_ids.include?(pv.batch_id)
              @batch_ids << pv.batch_id

              if !@previous_exam.blank?
                @group_exam_count[pv.batch_id.to_s] = @previous_exam.count
              else
                @group_exam_count[pv.batch_id.to_s] = 0
              end  

              if !@previous_group_exam.blank?
                @connect_exam_count[pv.batch_id.to_s] = @previous_group_exam.count
              else
                @connect_exam_count[pv.batch_id.to_s] = 0
              end  
              @position[pv.batch_id.to_s] = @iloop
              @previous_batch << pv
              @iloop = @iloop+1
            else
              if @previous_exam.count > @group_exam_count[pv.batch_id.to_s] or @previous_group_exam.count > @connect_exam_count[pv.batch_id.to_s]
                if !@previous_exam.blank?
                  @group_exam_count[pv.batch_id.to_s] = @previous_exam.count
                else
                  @group_exam_count[pv.batch_id.to_s] = 0
                end  

                if !@previous_group_exam.blank?
                  @connect_exam_count[pv.batch_id.to_s] = @previous_group_exam.count
                else
                  @connect_exam_count[pv.batch_id.to_s] = 0
                end 
                @previous_batch.delete(@position[pv.batch_id.to_s].to_i)
                @position[pv.batch_id.to_s] = @iloop
                @previous_batch << pv
                @iloop = @iloop+1
                
              end
            end  
          end
        end
      end
    end
    
    @sms_module = Configuration.available_modules
  end
  def download_student_family_list
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'student_list'
    row_first = ['SL','Student Id','Roll No.','Student Name','Father Name','Mother Name']
    new_book.worksheet(0).insert_row(0, row_first)
    @batch_name_pdf = batch_name = params[:batch_name]
    @version_pdf = version_name = params[:version_name]
    @class_name_pdf = class_name = params[:class_name]
    session_name = params[:session_name]
    group_name = params[:group_name]
    @section_pdf = section_name = params[:section_name]
    category_name = params[:category_name]
    condition = "1 = 1"
    unless batch_name.blank?
      condition = condition+" and batches.name like '%"+batch_name+"%'"
    end
    unless version_name.blank?
      condition = condition+" and batches.name like '%"+version_name+"%'"
    end
    unless class_name.blank?
      condition = condition+" and courses.course_name = '"+class_name+"'"
    end
    unless section_name.blank?
      condition = condition+" and courses.section_name = '"+section_name+"'"
    end
    unless session_name.blank?
      condition = condition+" and courses.session = '"+session_name+"'"
    end
    unless group_name.blank?
      condition = condition+" and courses.group = '"+group_name+"'"
    end
    unless category_name.blank?
      condition = condition+" and student_categories.name = '"+category_name+"'"
    end
    order_str = "courses.course_name asc,courses.section_name asc,courses.session asc,if(class_roll_no = '' or class_roll_no is null,0,cast(class_roll_no as unsigned)),students.admission_no asc"
    @students_pdf = students = Student.find(:all,:conditions=>condition,:include=>[{:batch=>[:course]},:student_category],:order=>order_str)
    std_loop = 1
    
      unless students.blank?
        students.each do |student|
          father_name = ""
          mother_name = ""
          guardians = student.student_guardian  
          unless guardians.nil?
            p_loop = 0
            guardians.each do |guardian|
                if p_loop == 0
                  father_name = guardian.first_name.to_s+" "+guardian.last_name.to_s
                else
                  mother_name = guardian.first_name.to_s+" "+guardian.last_name.to_s
                  break
                end
                p_loop = p_loop+1
            end
          end
          
      
          tmp_row = []
          tmp_row << std_loop
          tmp_row << student.admission_no
          tmp_row << student.class_roll_no
          tmp_row << student.full_name
          tmp_row << father_name
          tmp_row << mother_name
          
          new_book.worksheet(0).insert_row(std_loop, tmp_row)
          std_loop = std_loop+1
        end
      end
      if MultiSchool.current_school.id == 352 and !@section_pdf.blank?
        course_data = Course.find_by_section_name(@section_pdf)
        unless course_data.blank? 
          @batch = Batch.find_by_course_id(course_data.id)
          batch_split = @batch.name.split(" ")
          sheet1.add_header("SHAHEED BIR UTTAM LT. ANWAR GIRLS' COLLEGE (Student List Family)
     Program :"+@batch.course.course_name.to_s+" || Group :"+@batch.course.group.to_s+" || Section :"+@batch.course.section_name.to_s+" || Shift :"+batch_split[0]+" || Session :"+@batch.course.session.to_s+" || Version :"+batch_split[1]+"
          ")
        end
      end
      spreadsheet = StringIO.new 
      new_book.write spreadsheet 
      send_data spreadsheet.string, :filename => "Student_family_list.xls", :type =>  "application/vnd.ms-excel"
    
  end
  def download_student_list
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'student_list'
    row_first = ['SL','Student Id','Roll','Name','Blood Group','Category','Class','Shift','Section','Session','Version','Group','Tuition Fees','Mobile']
    new_book.worksheet(0).insert_row(0, row_first)
    @batch_name_pdf = batch_name = params[:batch_name]
    @version_pdf = version_name = params[:version_name]
    @class_name_pdf = class_name = params[:class_name]
    @session_name_pdf = session_name = params[:session_name]
    @group_name_pdf = group_name = params[:group_name]
    @section_pdf = section_name = params[:section_name]
    category_name = params[:category_name]
    condition = "1 = 1"
    unless batch_name.blank?
      condition = condition+" and batches.name like '%"+batch_name+"%'"
    end
    unless version_name.blank?
      condition = condition+" and batches.name like '%"+version_name+"%'"
    end
    unless class_name.blank?
      condition = condition+" and courses.course_name = '"+class_name+"'"
    end
    unless section_name.blank?
      condition = condition+" and courses.section_name = '"+section_name+"'"
    end
    unless session_name.blank?
      condition = condition+" and courses.session = '"+session_name+"'"
    end
    unless group_name.blank?
      condition = condition+" and courses.group = '"+group_name+"'"
    end
    unless category_name.blank?
      condition = condition+" and student_categories.name = '"+category_name+"'"
    end
    order_str = "courses.course_name asc,courses.section_name asc,courses.session asc,if(class_roll_no = '' or class_roll_no is null,0,cast(class_roll_no as unsigned)),students.admission_no asc"
    @students_pdf = students = Student.find(:all,:conditions=>condition,:include=>[{:batch=>[:course]},:student_category],:order=>order_str)
    if !params[:pdf].blank? and params[:pdf] == "1"
      render :pdf => "download_student_list",
      :orientation => 'Portrait',
      :page_size => 'Legal',
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    else
      std_loop = 1
      unless students.blank?
        students.each do |student|
          fee_particular = FinanceFeeParticular.find(:all,:conditions=>"is_deleted=#{false} and finance_fee_particular_category_id=54 and batch_id=#{student.batch.id}",:order=>"FIELD(receiver_type,'Student','StudentCategory','Batch'), id DESC").find{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          monthly_fee = 0
          unless fee_particular.blank?
            monthly_fee = fee_particular.amount
          end
          FinanceFeeParticularCategory.find(:first,:conditions=>[""])
          batchsplit = student.batch.name.split(" ")
          version = ""
          batch = batchsplit[0]
          unless batchsplit[1].blank?
            version = batchsplit[1]
          end
          unless batchsplit[2].blank?
            version = version+" "+batchsplit[2]
          end
          std_category = ""
          unless student.student_category.blank?
            std_category = student.student_category.name
          end
          tmp_row = []
          tmp_row << std_loop
          tmp_row << student.admission_no
          tmp_row << student.class_roll_no
          tmp_row << student.full_name
          tmp_row << student.blood_group unless student.blood_group.nil?
          tmp_row << std_category
          tmp_row << student.batch.course.course_name
          tmp_row << batch
          tmp_row << student.batch.course.section_name
          tmp_row << student.batch.course.session
          tmp_row << version
          tmp_row << student.batch.course.group
          tmp_row << monthly_fee
          tmp_row << student.sms_number
          new_book.worksheet(0).insert_row(std_loop, tmp_row)
          std_loop = std_loop+1
        end
      end
      if MultiSchool.current_school.id == 352 and !@section_pdf.blank?
        course_data = Course.find_by_section_name(@section_pdf)
        unless course_data.blank? 
          @batch = Batch.find_by_course_id(course_data.id)
          batch_split = @batch.name.split(" ")
          sheet1.add_header("SHAHEED BIR UTTAM LT. ANWAR GIRLS' COLLEGE (Student List)
     Program :"+@batch.course.course_name.to_s+" || Group :"+@batch.course.group.to_s+" || Section :"+@batch.course.section_name.to_s+" || Shift :"+batch_split[0]+" || Session :"+@batch.course.session.to_s+" || Version :"+batch_split[1]+"
          ")
        end
      end
      spreadsheet = StringIO.new 
      new_book.write spreadsheet 
      send_data spreadsheet.string, :filename => "Student_list.xls", :type =>  "application/vnd.ms-excel"
    end
  end
  def get_student_ajax
    require 'json'
    require "yaml"
    condition = "1 = 1"
    per_page = params[:length]
    start = params[:start]
    orders = params[:order]
    search = params[:search]
    search_value = search["value"]
    page = ( start.to_i / per_page.to_i ) + 1
    order_str = "courses.course_name asc,courses.section_name asc,courses.session asc,if(class_roll_no = '' or class_roll_no is null,0,cast(class_roll_no as unsigned)),students.admission_no asc"
    columns = params[:columns]
    columns.keys.each do |key|
      columns[key].each do |k, v|
        if k == "search"
          columns[key][k].each do |k1,v1|
            if k1 == "value"
              unless v1.blank?
                if key.to_i == 3
                  condition = condition+" and batches.name like '%"+v1+"%'"
                elsif key.to_i == 4
                  condition = condition+" and courses.course_name = '"+v1+"'"
                elsif key.to_i == 5
                  condition = condition+" and courses.section_name = '"+v1+"'"
                elsif key.to_i == 6
                  condition = condition+" and courses.session = '"+v1+"'"
                elsif key.to_i == 7
                  condition = condition+" and courses.group = '"+v1+"'"
                elsif key.to_i == 8
                  condition = condition+" and student_categories.name = '"+v1+"'"
                end  
              end
            end
          end
        end
      end
    end
    
    unless search_value.blank?
      condition = condition+" and students.admission_no = '"+search_value+"'"
    end
    
    students_all = Student.count(:all,:conditions=>condition,:include=>[{:batch=>[:course]},:student_category])
    
    students = Student.paginate(:conditions=>condition,:include=>[{:batch=>[:course]},:student_category], :page => page.to_i, :per_page => per_page.to_i,:order=>order_str)
    k = 0
    data = []
    students.each do |student|
      srl = k+1
      batchsplit = student.batch.name.split(" ")
      version = ""
      batch = batchsplit[0]
      unless batchsplit[1].blank?
        version = batchsplit[1]
      end
      unless batchsplit[2].blank?
        version = version+" "+batchsplit[2]
      end
      std_category = ""
      unless student.student_category.blank?
        std_category = student.student_category.name
      end
      password = "";
      guardians = student.student_guardian
      
      unless guardians.nil?
        guardians.each do |guardian|
          guser = User.find_by_id(guardian.user_id)
          unless guser.blank?
            unless guser.username.index("p1").blank?
              @paid_data = TdsFreeUser.find_by_paid_id(guser.id)
              unless @paid_data.blank?
                password = @paid_data.paid_password
              end
              break
            end
          end
          
        end
      end
      
       if student.photo.file? 
         @profile_image = student.photo.url 
         unless @profile_image.index("RackMultipart").nil? 	
            @profile_image.gsub! '?', '.?' 	
         end 
       else 
         @profile_image = "/images/master_student/profile/default_student.png" 
       end 
      blood_group = student.blood_group
      p_image = "<img src='#{@profile_image}' width='80' />"
      send_sms = "<a href='javascript:void(0)' id='student_"+student.id.to_s+"' onClick='send_sms("+student.id.to_s+")'>Send</a>"
      send_sms = send_sms+"&nbsp;&nbsp;|&nbsp;&nbsp;<a href='/student/edit/"+student.id.to_s+"' target='_blank' >Edit</a>"
      std = {:p_image=>p_image,:admission_no=>student.admission_no,:roll_no=>student.class_roll_no,:password=>password,:sms_number=>student.sms_number,:student_name=>"<a href='/student/profile/"+student.id.to_s+"'>"+student.full_name+"</a>",:category=>std_category,:blood_group=>blood_group,:class=>student.batch.course.course_name,:batch=>batch,:section=>student.batch.course.section_name,:session=>student.batch.course.session,:version=>version,:group=>student.batch.course.group,:send_sms=>send_sms}
      data[k] = std
      k += 1
    end
    draw = 1
    unless params[:draw].blank?
      draw = params[:draw]
    end
    data_hash = {:draw => draw, :recordsTotal => students_all.to_s, :recordsFiltered => students_all.to_s, :data => data}
    @data = JSON.generate(data_hash)
  end
  
  def send_sms_username_password
    @student = Student.find(params[:id])
    student_ids = []
    student_ids << @student.id
    message = ""
    if File.exists?("#{Rails.root}/config/sms_text_#{MultiSchool.current_school.id}.yml")
      sms_text_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/sms_text_#{MultiSchool.current_school.id}.yml")['school']
      message = sms_text_config['upass']
    end
    download_opt = 0
    sent_to = 3
    unless message.blank?
      send_sms_student(student_ids,message,sent_to)
    end
    render :text => "Successfully Send"
  end
  
  def academic_report_all
    @user = current_user
    @prev_student = @student.previous_student
    @next_student = @student.next_student
    @course = @student.course
    @examtypes = ExaminationType.find( ( @course.examinations.collect { |x| x.examination_type_id } ).uniq )
    
    @graph = open_flash_chart_object(965, 350, "/student/graph_for_academic_report?course=#{@course.id}&student=#{@student.id}")
    @graph2 = open_flash_chart_object(965, 350, "/student/graph_for_annual_academic_report?course=#{@course.id}&student=#{@student.id}")
  end
  
  def get_section_data
    @batch_name = ""
    @class_name = ""
    
    batch_id = 0
    unless params[:student].nil?
      unless params[:student][:batch_name].nil?
        
        batch_id = params[:student][:batch_name]
        
      end
    end
    
    unless params[:advv_search].nil?
      unless params[:advv_search][:batch_name].nil?
        batch_id = params[:advv_search][:batch_name]
      end
    end
    
    school_id = MultiSchool.current_school.id
    batch_name = ""
    if batch_id.to_i > 0
      batch = Batch.find batch_id
      batch_name = batch.name
      @batch_name = batch_name
    end
    
    unless batch_name.blank?
      if current_user.employee
        batches_all = @current_user.employee_record.batches
        batches_all += @current_user.employee_record.subjects.collect{|b| b.batch}
        batches_all = batches_all.uniq unless batches_all.empty?
        batches_all.reject! {|s| s.name!=batch_name}
      else
        batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
      end 
      
      batches = batches_all.map{|b| b.course_id}    
      @classes = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id IN (?)",params[:class_name], batches])      
    else    
      @classes =  Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0",params[:class_name]]) 
    end
    
    @selected_section = 0
    
    unless params[:section_name].blank?
      @selected_section = params[:section_name].to_i
    end
    
    @batch_id = 0
    @courses = []
    
    @class_name = params[:class_name]
    if batch_id.to_i > 0
      batch = Batch.find batch_id
      @batch_name = batch.name
    end
    
    render :update do |page|
      
      if params[:page].nil?
        page.replace_html 'section', :partial => 'sections', :object => @classes
        unless params[:section_page].nil? and params[:section_partial].nil?
          page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
        end
        unless params[:page_batch].nil? and params[:partial_view_batch].nil?
          page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
        end
      else
        if params[:partial_view].nil?
          page.replace_html params[:page], :partial => 'sections', :object => @classes
          unless params[:section_page].nil? and params[:section_partial].nil?
            page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
          end
          unless params[:page_batch].nil? and params[:partial_view_batch].nil?
            page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
          end
        else
          page.replace_html params[:page], :partial => params[:partial_view], :object => @classes
          unless params[:section_page].nil? and params[:section_partial].nil?
            page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
          end
          unless params[:page_batch].nil? and params[:partial_view_batch].nil?
            page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
          end
        end
      end  
    end
  end
  
  def get_classes
    school_id = MultiSchool.current_school.id
    @batch_name = false
    unless params[:batch_id].empty?
      batch_data = Batch.find params[:batch_id]
      batch_name = batch_data.name
    end 
    
    
    if current_user.employee
      batches_all = @current_user.employee_record.batches
      batches_all += @current_user.employee_record.subjects.collect{|b| b.batch}
      batches_all = batches_all.uniq unless batches_all.empty?
      batches_all.reject! {|s| s.name!=batch_name}
    else
      batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
    end 
    
    batches = batches_all.map{|b| b.course_id}
    @courses = Course.find(:all, :conditions => ["id IN (?) and is_deleted = 0", batches], :group => "course_name", :select => "course_name,no_call", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    
    #    @courses = []
    #    unless batch_name.blank?
    #      @courses = Rails.cache.fetch("classes_data_#{batch_name.parameterize("_")}_#{school_id}"){
    #        @batch_name = batch_name;
    #        batches = Batch.find(:all, :conditions => ["name = ? and is_deleted = 0", batch_name]).map{|b| b.course_id}
    #        tmp_classes = Course.find(:all, :conditions => ["id IN (?) and is_deleted = 0", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    #        class_data = tmp_classes
    #        class_data
    #      }
    
    @classes = []
    @batch_id = ''
    @course_name = ""
    @section_name = ""
    unless params[:course_name].blank?
      @course_name = params[:course_name]
    end
    unless params[:section_name].blank?
      @section_name = params[:section_name]
    end
    render :update do |page|
      if params[:page].nil?
        page.replace_html 'course', :partial => 'courses', :object => @courses
        unless params[:section_page].nil? and params[:section_partial].nil?
          page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
        end
        unless params[:page_batch].nil? and params[:partial_view_batch].nil?
          page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
        end
      else
        if params[:partial_view].nil?
          page.replace_html params[:page], :partial => 'courses', :object => @courses
          unless params[:section_page].nil? and params[:section_partial].nil?
            page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
          end
          unless params[:page_batch].nil? and params[:partial_view_batch].nil?
            page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
          end
        else
          page.replace_html params[:page], :partial => params[:partial_view], :object => @courses
          unless params[:section_page].nil? and params[:section_partial].nil?
            page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
          end
          unless params[:page_batch].nil? and params[:partial_view_batch].nil?
            page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
          end
        end
      end  
    end
  end

  
  def get_classes_publisher
    school_id = MultiSchool.current_school.id
    @batch_name = false
    unless params[:batch_id].empty?
      batch_data = Batch.find params[:batch_id]
      batch_name = batch_data.name
    end 
    
    if current_user.employee
      batches_all = @current_user.employee_record.batches
      batches_all += @current_user.employee_record.subjects.collect{|b| b.batch}
      batches_all = batches_all.uniq unless batches_all.empty?
      batches_all.reject! {|s| s.name!=batch_name}
    else
      batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
    end 
    
    batches = batches_all.map{|b| b.course_id}
    
    @courses = Course.find(:all, :conditions => ["id IN (?) and is_deleted = 0", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    
    @classes = []
    @batch_id = ''
    @course_name = ""
    render :update do |page|
      if params[:page].nil?
        page.replace_html 'course', :partial => 'courses', :object => @courses
        unless params[:section_page].nil? and params[:section_partial].nil?
          page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
        end
        unless params[:page_batch].nil? and params[:partial_view_batch].nil?
          page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
        end
      else
        if params[:partial_view].nil?
          page.replace_html params[:page], :partial => 'courses', :object => @courses
          unless params[:section_page].nil? and params[:section_partial].nil?
            page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
          end
          unless params[:page_batch].nil? and params[:partial_view_batch].nil?
            page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
          end
        else
          page.replace_html params[:page], :partial => params[:partial_view], :object => @courses
          unless params[:section_page].nil? and params[:section_partial].nil?
            page.replace_html params[:section_page], :partial => params[:section_partial], :object => @classes
          end
          unless params[:page_batch].nil? and params[:partial_view_batch].nil?
            page.replace_html params[:page_batch], :partial => params[:partial_view_batch], :object => @classes
          end
        end
      end  
    end
  end
  
  def insert_into_new_parent_student_table
    
    @students = Student.active.find(:all)
    @students.each do |student|
      guardians = student.student_guardian
      unless guardians.nil?
        guardians.each do |guardian|
          
          check_guardian = GuardianStudents.find_by_student_id_and_guardian_id(student.id,guardian.id)
          if check_guardian.nil?
            stdgu = GuardianStudents.new
            stdgu.student_id = student.id
            stdgu.guardian_id = guardian.id
            stdgu.relation = guardian.relation
            stdgu.save
          end 
          
        end
      end
    end
    render :nothing => true
    
  end
  
  def get_batches
    batch_name = ""
    if Batch.active.find(:all, :group => "name").length > 1
      unless params[:student].nil?
        unless params[:student][:batch_name].nil?
          batch_id = params[:student][:batch_name]
          batches_data = Batch.find_by_id(batch_id)
          batch_name = batches_data.name
        end
      end

      unless params[:advv_search].nil?
        unless params[:advv_search][:batch_name].nil?
          batch_id = params[:advv_search][:batch_name]
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
      unless params[:advv_search].nil?
        unless params[:advv_search][:section].nil?
          course_id = params[:advv_search][:section]
        end
      end
    end
    
    school_id = MultiSchool.current_school.id
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
      
    @batch_id = 0
    unless @batch_data.nil?
      @batch_id = @batch_data.id 
    end
    
    render :update do |page|
      if params[:page].nil?
        page.replace_html 'batches', :partial => 'batches', :object => @batch_id
      else
        if params[:partial_view].nil?
          page.replace_html params[:page], :partial => 'sections', :object => @batch_id
        else
          page.replace_html params[:page], :partial => params[:partial_view], :object => @batch_id
        end
      end
    end
  end
  def all_students_import
    require 'spreadsheet'   
    
    if request.post?
      file = params[:import_batches][:class_file]
       
      unless file.nil?               
        case File.extname(file.original_filename)        
        when ".xls" then @hasfile = 1        
        else flash[:notice] =  "#{t('invalid_file_type_for_import')}: #{file.original_filename}"
        end
        @all = []
        @success_data = []
        inserts = []
        @msg = 0
        i = 0
        #begin
        if @hasfile == 1
          Spreadsheet.open(file.path) do |book|
            book.worksheet('Sheet1').each do |row|              
              break if row[1].nil?
              next if row[0] == "Admission-ID*"
              @all << row
              
              @success_data[i] = {:sdata => [],:pdata => []}

              
              @classes = Rails.cache.fetch("section_data_section_#{row[7].parameterize("_")}_#{MultiSchool.current_school.id}"){
                class_data = Course.find(:all, :conditions => ["course_name LIKE ?",row[7].strip])
                class_data
              }
                
              @classes.each do|c|                
                if c.section_name == row[8]  
                  @batch_data = Rails.cache.fetch("course_data_batch_#{c.id}"){
                    batches = Batch.find(:all, :conditions => ["course_id =?",c.id])
                    batches
                  }
                else
                  if row[8].nil? and c.section_name == ""
                    @batch_data = Rails.cache.fetch("course_data_batch_#{c.id}"){
                      batches = Batch.find(:all, :conditions => ["course_id =?",c.id])
                      batches
                    }
                  end
                end
              end
              
              @batch_id = 0
              @batch_data.each do|b|
                if b.name == row[6].strip
                  @batch_id = b.id
                end
              end
              
              if @batch_id == 0 or @batch_id == "0" or @batch_id == ""
                flash[:notice] =  "#{t('student_batch_not_found_please_check_class_section_and_shift_for_student')} '#{row[1]} #{row[3]}'"
                break
              end
              
              @student = Student.new(params[:student]) 
              @selected_value = Configuration.default_country 
              @application_sms_enabled = SmsSetting.find_by_settings_key("ApplicationEnabled")
              
              @config = Configuration.find_by_config_key('AdmissionNumberAutoIncrement')
              @categories = StudentCategory.active              
              
              @last_admitted_student = Student.find(:last,:order=>"id ASC")
              if row[0].nil?                
                unless @config.config_value == '0' or @last_admitted_student.nil?
                  @student.admission_no = @last_admitted_student.admission_no.next
                else
                  @student.admission_no = "ST0001"
                end
              else
                @student.admission_no = row[0].to_s.gsub('.0','')                
              end
                            
              @student.admission_date = DateTime.now.strftime('%Y/%m/%d')
              @student.first_name = row[1]
              @student.middle_name = row[2] 
              @student.last_name = row[3]
              @student.email = row[4]
              unless row[5].nil?
                @student.class_roll_no = row[5].to_i              
              end              
              @student.class_name = row[7]
              @student.section = row[8]
              @student.batch_id = @batch_id
              
              unless row[9].nil?
                @student.date_of_birth = row[9].strftime('%Y/%m/%d')
              end              
              @student.gender = row[10]
              @student.blood_group = row[11]
              @student.religion =   row[12]                                      
              @student.address_line1 = row[13]   
              @student.address_line2 = row[14]   
              @student.city = row[15]   
              
              @student.phone1 =   row[16] 
              @student.country_id = 14
              @student.nationality_id = 14  
              @s_pass = (('0'..'9').to_a + ('a'..'z').to_a + ('A'..'Z').to_a).shuffle.first(6).join #(0...8).map { (65 + rand(26)).chr }.join  
              @student.pass = @s_pass
              @student.student_activation_code = "rzs-jkbhaiudiu"                            
         
              @student.tmp = 0
              
              @status = @student.save 

              
              @guardian = Guardian.new(params[:guardian])              
              @guardian.first_name = row[17]              
              @guardian.last_name = row[18]              
              @guardian.relation = row[19]
              unless row[20].nil?
                @guardian.dob = row[20].strftime('%Y/%m/%d')
              end              
              @guardian.occupation = row[21]
              @guardian.email = row[22]
              
              @guardian.office_address_line1 = row[23]
              @guardian.income = row[24]               
              @guardian.city = row[25]   
              
              @guardian.mobile_phone = row[26]     
              @guardian.country_id = 14       
              @guardian.ward_id = @student.id
              
              if @status                                
                @success_data[i][:sdata] << @student
                
                username = MultiSchool.current_school.code.to_s+"-"+@student.admission_no        
                champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
                api_endpoint = champs21_api_config['api_url']
                uri = URI(api_endpoint + "api/user/createuser")
                http = Net::HTTP.new(uri.host, uri.port)
                auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
                auth_req.set_form_data({"paid_id" => @student.user.id, "paid_username" => username, "paid_password" => @s_pass,"password" => @s_pass, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @student.first_name, "middle_name" => @student.middle_name, "last_name" => @student.last_name, "gender" => (if @student.gender == 'm' then '1' else '0' end), "country" => @student.nationality_id, "dob" => @student.date_of_birth, "email" => username, "user_type" => "2" })
                auth_res = http.request(auth_req)
                @auth_response = JSON::parse(auth_res.body)                
                @guardian.set_immediate_contact = @student.admission_no
                @guardian.pass = (('0'..'9').to_a + ('a'..'z').to_a + ('A'..'Z').to_a).shuffle.first(6).join
                @guardian.save_to_free = 1
                @status2 = @guardian.save
                
                if @status2    
                  usernamep = MultiSchool.current_school.code.to_s+"-p1"+@student.admission_no        
                  #                  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
                  #                  api_endpoint = champs21_api_config['api_url']
                  #                  uri = URI(api_endpoint + "api/user/createuser")
                  #                  http = Net::HTTP.new(uri.host, uri.port)
                  #                  auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
                  #                  auth_req.set_form_data({"paid_id" => @guardian.user.id, "paid_username" => usernamep, "paid_password" => "123456","password" => "123456", "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @guardian.first_name, "last_name" => @guardian.last_name, "country" => @guardian.country_id, "email" => usernamep, "user_type" => "4" })
                  #                  auth_res = http.request(auth_req)
                  #                  @auth_response = JSON::parse(auth_res.body)
                  
                  #@success_data['parent']['msg'] = "0"
                  @success_data[i][:pdata] << @guardian
                  
                  @msg = 3 
                  inserts.push "('#{@student.admission_no}','#{username}','#{@s_pass}','#{@student.first_name}','#{@student.middle_name}','#{@student.last_name}','#{@student.user.id}','#{@guardian.first_name}','#{@guardian.last_name}','#{usernamep}','#{@guardian.pass}','#{@guardian.user_id}','#{@guardian.office_phone1}','#{MultiSchool.current_school.id}')"
                else
                  @msg = 2                  
                  #@success_data['parent']['msg'] = "1"
                  #@success_data['parent'] << @parent
                end
              else
                @msg = 1 
              end               
              i +=1  
            end
          end          
        end
        
        unless inserts.empty?          
          sql = "insert into students_guardians (`admission_no`,`s_username`,`s_password`,`s_first_name`,`s_middle_name`,`s_last_name`,`student_id`,`g_first_name`,`g_last_name`,`g_username`,`g_password`,`guardian_id`,`g_phone`,`school_id`) VALUES #{inserts.join(", ")}"                  
          CONN.execute sql
        end
        #rescue Exception => e  
        #  flash[:notice] = e.message
        #end 
        if @msg == 1
          flash[:notice] =  "#{t('no_student_added_there_is_some_error_please_contact_to_your_administrator')}"
        elsif @msg ==2
          flash[:notice] =  "#{t('there_is_some_error_please_contact_to_your_administrator')}"
        elsif @msg ==3
          flash[:notice] =  "#{t('all_students_added')}"
        end
        
      end
    end
  end
  
  def admission_sagc   
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @section_name = ""
    @courses = []
    inserts = []
    @additional_fields = StudentAdditionalField.find(:all, :conditions=> "status = true", :order=>"priority ASC")
    @student_additional_details = StudentAdditionalDetail.find_all_by_student_id(0)
    @std_session = StudentSession.active
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    @user = current_user
    @student = Student.new(params[:student])
    
    @student_limit = @student.get_student_limit();
    #@student.student_activation_code = nil
    @selected_value = Configuration.default_country 
    @application_sms_enabled = SmsSetting.find_by_settings_key("ApplicationEnabled")
    @last_admitted_student = Student.find(:first,:order=>"id desc")
    @config = Configuration.find_by_config_key('AdmissionNumberAutoIncrement')
    @categories = StudentCategory.active
    if request.post?
     
      if MultiSchool.current_school.id == 340 and params[:id].blank?
        std_session = StudentSession.find(@student.session_id)
        std_id_match = "SJW"+std_session.admission_session
        last_admitted_student_sjws = Student.find(:first,:conditions=>["admission_no like ?",std_id_match+"%"],:order=>"admission_no desc")
        unless last_admitted_student_sjws.blank?
          @student.admission_no = last_admitted_student_sjws.admission_no.next
        else
          @student.admission_no = std_id_match+"001"
        end  
        
      end
      
      if @student.student_category_id != 433
        @student.staff_id = 0
      end
      @student.pass = (('2'..'9').to_a + ('a'..'h').to_a + ('p'..'z').to_a + ('A'..'H').to_a + ('P'..'Z').to_a).shuffle.first(6).join
      @student.save_log = true
      @student.save_to_free = true
      #Huffas: Task end
      @activation_code_no_error = true
      
      if @activation_code_no_error == true 
        if @config.config_value.to_i == 1
          @exist = Student.find_by_admission_no(@student.admission_no)
          #abort(@student.inspect)
          if @exist.nil?
            
            @status = @student.save
          else
            
            @last_admitted_student = Student.find(:first,:order=>"admission_no desc")
            @student.admission_no = @last_admitted_student.admission_no.next
            @status = @student.save
          end
        else
          
          @status = @student.save
        end
        
        if @status
          sms_setting = SmsSetting.new()
          if sms_setting.application_sms_active and @student.is_sms_enabled
            recipients = []
            message = "#{t('student_admission_done')} #{@student.admission_no} #{t('password_is')} 123456"
            if sms_setting.student_admission_sms_active
              recipients.push @student.phone2 unless @student.phone2.blank?
            end
            unless recipients.empty? or !send_sms("studentregister")
              Delayed::Job.enqueue(SmsManager.new(message,recipients))
            end
          end
          params[:guardian] = {}
          @error=false
          
          @student = Student.find(@student.id)
          
#          @fee_collection_dates = @student.batch.fee_collection_dates
#          @fee_collection_dates = @fee_collection_dates.select{|d| d.due_date>=@student.admission_date }
         @fee_collection_dates = FinanceFeeParticular.find(:all,:joins=>"INNER JOIN collection_particulars on collection_particulars.finance_fee_particular_id=finance_fee_particulars.id INNER JOIN finance_fee_collections on finance_fee_collections.id=collection_particulars.finance_fee_collection_id",:conditions=>"finance_fee_particulars.batch_id='#{@student.batch_id}' and finance_fee_particulars.receiver_type='Batch' and finance_fee_collections.due_date>='#{@student.admission_date}'",:select=>"finance_fee_collections.*").uniq
          @fee_collection_dates.each do |date|
            d = FinanceFeeCollection.find(date.id)
            FinanceFee.new_student_fee(d,@student)
          end
            
          
          
          if !params[:f_first_name].blank?
            params[:guardian][:first_name] = params[:f_first_name]
            params[:guardian][:relation] = "Father"
            @guardian = @student.guardians.build(params[:guardian])
            @guardian.set_immediate_contact = @student.admission_no
            @guardian.save_to_free = true
            if @guardian.save
              check_guardian = GuardianStudents.find_by_student_id_and_guardian_id(@student.id,@guardian.id)
              if check_guardian.nil?
                stdgu = GuardianStudents.new
                stdgu.student_id = @student.id
                stdgu.guardian_id = @guardian.id
                stdgu.save
              end
              Student.update(@student.id, :immediate_contact_id => @guardian.id)
            else
              @error=true
            end 
          end
          
          if !params[:m_first_name].blank?
            params[:guardian][:first_name] = params[:m_first_name]
            params[:guardian][:relation] = "Mother"
            @guardian = @student.guardians.build(params[:guardian])
            if @guardian.save
              check_guardian = GuardianStudents.find_by_student_id_and_guardian_id(@student.id,@guardian.id)
              if check_guardian.nil?
                stdgu = GuardianStudents.new
                stdgu.student_id = @student.id
                stdgu.guardian_id = @guardian.id
                stdgu.save
              end
              @guardian.save_to_free = true
              @guardian.create_guardian_user(@student,false)
            else
              @error=true
            end  
          end
          
          
          
          mandatory_fields = StudentAdditionalField.find(:all, :conditions=>{:is_mandatory=>true, :status=>true})
          mandatory_fields.each do|m|
            unless params[:student_additional_details][m.id.to_s.to_sym].present?
              @student.errors.add_to_base("#{m.name} must contain atleast one selected option.")
              @error=true
            else
              if params[:student_additional_details][m.id.to_s.to_sym][:additional_info]==""
                @student.errors.add_to_base("#{m.name} cannot be blank.")
                @error=true
              end
            end
          end
      
          unless @error==true
            additional_field_ids_posted = []
            additional_field_ids = @additional_fields.map(&:id)
            if params[:student_additional_details].present?
              params[:student_additional_details].each_pair do |k, v|
                addl_info = v['additional_info']
                additional_field_ids_posted << k.to_i
                addl_field = StudentAdditionalField.find_by_id(k)
                if addl_field.input_type == "has_many"
                  addl_info = addl_info.join(", ")
                end
                prev_record = StudentAdditionalDetail.find_by_student_id_and_additional_field_id( @student.id, k)
                unless prev_record.nil?
                  unless addl_info.present?
                    prev_record.destroy
                  else
                    prev_record.update_attributes(:additional_info => addl_info)
                  end
                else
                  addl_detail = StudentAdditionalDetail.new(:student_id =>  @student.id,
                    :additional_field_id => k,:additional_info => addl_info)
                  addl_detail.save if addl_detail.valid?
                end
              end
            end
            if additional_field_ids.present?
              StudentAdditionalDetail.find_all_by_student_id_and_additional_field_id(@student.id,(additional_field_ids - additional_field_ids_posted)).each do |additional_info|
                additional_info.destroy unless additional_info.student_additional_field.is_mandatory == true
              end
            end
          end
    
          flash[:notice] = "#{t('flash23')}"
          redirect_to :controller => "student", :action => "profile", :id => @student.id
  
        else
          @classes = Course.find(:all, :conditions => ["course_name LIKE ?",params[:student][:class_name]])
          @selected_section = params[:student][:section]
          @batch_id = params[:student][:batch_id]
          @batch_no = params[:student][:batch_name]
          @course_name = params[:student][:class_name]
          @section_name = params[:student][:section]
        end
      end
    end
  end
  
  def admission1 
    if MultiSchool.current_school.id == 352 or MultiSchool.current_school.id == 346
      redirect_to :controller => "student", :action => "admission_sagc"
    end
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @section_name = ""
    @courses = []
    inserts = []
    @std_session = StudentSession.active
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    @user = current_user
    @student = Student.new(params[:student])
    @student_limit = @student.get_student_limit();
    #@student.student_activation_code = nil
    @selected_value = Configuration.default_country 
    @application_sms_enabled = SmsSetting.find_by_settings_key("ApplicationEnabled")
    @last_admitted_student = Student.find(:first,:order=>"admission_no desc")
    @config = Configuration.find_by_config_key('AdmissionNumberAutoIncrement')
    @categories = StudentCategory.active
    if request.post?
     
      if MultiSchool.current_school.id == 340 and params[:id].blank?
        std_session = StudentSession.find(@student.session_id)
        std_id_match = "SJW"+std_session.admission_session
        last_admitted_student_sjws = Student.find(:first,:conditions=>["admission_no like ?",std_id_match+"%"],:order=>"admission_no desc")
        unless last_admitted_student_sjws.blank?
          @student.admission_no = last_admitted_student_sjws.admission_no.next
        else
          @student.admission_no = std_id_match+"001"
        end  
        
      end
      
      if @student.student_category_id != 433
        @student.staff_id = 0
      end
      
      @student.save_log = true
      @student.save_to_free = true
      #Huffas: Task end
      @activation_code_no_error = true
      
      if @activation_code_no_error == true 
        if @config.config_value.to_i == 1
          @exist = Student.find_by_admission_no(@student.admission_no)
          #abort(@student.inspect)
          if @exist.nil?
            
            @status = @student.save
          else
            
            @last_admitted_student = Student.find(:first,:order=>"admission_no desc")
            @student.admission_no = @last_admitted_student.admission_no.next
            @status = @student.save
          end
        else
          
          @status = @student.save
        end
        
        if @status
          sms_setting = SmsSetting.new()
          if sms_setting.application_sms_active and @student.is_sms_enabled
            recipients = []
            message = "#{t('student_admission_done')} #{@student.admission_no} #{t('password_is')} 123456"
            if sms_setting.student_admission_sms_active
              recipients.push @student.phone2 unless @student.phone2.blank?
            end
            unless recipients.empty? or !send_sms("studentregister")
              Delayed::Job.enqueue(SmsManager.new(message,recipients))
            end
          end
          if Configuration.find_by_config_key('EnableSibling').present? and Configuration.find_by_config_key('EnableSibling').config_value=="1"
            flash[:notice] = "#{t('flash22')}"
            redirect_to :controller => "student", :action => "admission1_2", :id => @student.id
          else
            flash[:notice] = "#{t('flash8')}"
            redirect_to :controller => "student", :action => "admission2", :id => @student.id
          end
        else
          @classes = Course.find(:all, :conditions => ["course_name LIKE ?",params[:student][:class_name]])
          @selected_section = params[:student][:section]
          @batch_id = params[:student][:batch_id]
          @batch_no = params[:student][:batch_name]
          @course_name = params[:student][:class_name]
          @section_name = params[:student][:section]
        end
      end
    end
  end
  def admission1_2
    if Configuration.find_by_config_key('EnableSibling').present? and Configuration.find_by_config_key('EnableSibling').config_value=="1"
      @batches=Batch.active
      @student=Student.find(params[:id])
      if request.post? and params[:sibling_no].present?
        sibling=Student.find(params[:sibling_no])
        #student=Student.find(params[:id])
        unless @student.all_siblings.present?
          @student.guardians.each do|guardian|
            guardian.user.destroy if guardian.user.present?
            guardian.destroy
          end
        else
          unless @student.all_siblings.collect(&:immediate_contact_id).include?(@student.immediate_contact_id)
            @student.immediate_contact.user.destroy if @student.immediate_contact.user.present?
          end
        end
        
        @sib_guardian = sibling.student_guardian
        unless @sib_guardian.empty?
          @sib_guardian.each do |sg|
            check_guardian = GuardianStudents.find_by_student_id_and_guardian_id(@student.id,sg.id)
            if check_guardian.nil?
              stdgu = GuardianStudents.new
              stdgu.student_id = @student.id
              stdgu.guardian_id = sg.id
              stdgu.save
            end  
          end
        end

        @student.update_attributes(:immediate_contact_id=>sibling.immediate_contact_id,:sibling_id=>sibling.sibling_id)
        if params[:configure].present?
          redirect_to :controller => "student", :action => "profile", :id => params[:id]
        else
          redirect_to :controller => "student", :action => "admission2", :id => params[:id]
        end
      end
    else
      flash[:notice] = "#{t('flash_msg4')}"
      redirect_to :controller => 'user', :action => 'dashboard'
    end
  end

  def admission2
   
    @student = Student.find params[:id]
    @guardian = Guardian.new(params[:guardian])
    if request.post? and @guardian.save
      check_guardian = GuardianStudents.find_by_student_id_and_guardian_id(@student.id,@guardian.id)
      if check_guardian.nil?
        stdgu = GuardianStudents.new
        stdgu.student_id = @student.id
        stdgu.guardian_id = @guardian.id
        stdgu.save
      end
      @guardian.create_guardian_user(@student,false)
      
      redirect_to :controller => "student", :action => "admission2", :id => @student.id
    end
  end

  def admission3
    @student = Student.find(params[:id])
    @parents = @student.student_guardian
    
    if @parents.empty?
      redirect_to :controller => "student", :action => "previous_data", :id => @student.id
    elsif MultiSchool.current_school.id == 340
      @parents.each do |immediate_contact|
        sms_setting = SmsSetting.new()
        @student = Student.update(@student.id, :immediate_contact_id => immediate_contact.id)
        @guardian = Guardian.find(immediate_contact.id)
        usernamep = @guardian.user.username
        champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
        api_endpoint = champs21_api_config['api_url']
        uri = URI(api_endpoint + "api/user/createuser")
        http = Net::HTTP.new(uri.host, uri.port)
        auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
        auth_req.set_form_data({"paid_id" => @guardian.user.id, "paid_username" => usernamep, "paid_password" => "123456","password" => "123456", "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @guardian.first_name, "last_name" => @guardian.last_name, "country" => @guardian.country_id, "email" => usernamep, "user_type" => "4" })
        auth_res = http.request(auth_req)
        @auth_response = JSON::parse(auth_res.body)
        sql = "update students_guardians set `g_first_name`='#{@guardian.first_name}',`g_last_name`='#{@guardian.last_name}',`g_username`='#{usernamep}',`g_password`='123456',`guardian_id`= #{@guardian.user.id},`g_phone`='#{@guardian.mobile_phone}' where `student_id`= #{@student.user.id} and`school_id` = #{MultiSchool.current_school.id}"
        CONN.execute sql
      end
      redirect_to :action => "previous_data", :id => @student.id
    end  
    return if params[:immediate_contact].nil?
    if request.post?
      sms_setting = SmsSetting.new()
      @student = Student.update(@student.id, :immediate_contact_id => params[:immediate_contact][:contact])
      
      @guardian = Guardian.find(params[:immediate_contact][:contact])
      usernamep = @guardian.user.username
      champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
      api_endpoint = champs21_api_config['api_url']
      uri = URI(api_endpoint + "api/user/createuser")
      http = Net::HTTP.new(uri.host, uri.port)
      auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
      auth_req.set_form_data({"paid_id" => @guardian.user.id, "paid_username" => usernamep, "paid_password" => "123456","password" => "123456", "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @guardian.first_name, "last_name" => @guardian.last_name, "country" => @guardian.country_id, "email" => usernamep, "user_type" => "4" })
      auth_res = http.request(auth_req)
      @auth_response = JSON::parse(auth_res.body)      
            
      sql = "update students_guardians set `g_first_name`='#{@guardian.first_name}',`g_last_name`='#{@guardian.last_name}',`g_username`='#{usernamep}',`g_password`='123456',`guardian_id`= #{@guardian.user.id},`g_phone`='#{@guardian.mobile_phone}' where `student_id`= #{@student.user.id} and`school_id` = #{MultiSchool.current_school.id}"
      CONN.execute sql
      
      if sms_setting.application_sms_active and sms_setting.student_admission_sms_active and @student.is_sms_enabled
        recipients = []
        message = "#{t('student_admission_done')}  #{@student.admission_no} #{t('password_is')} 123456"
        if sms_setting.parent_sms_active
          guardian = Guardian.find(@student.immediate_contact_id)
          recipients.push guardian.mobile_phone unless guardian.mobile_phone.nil?
        end
        unless recipients.empty? or !send_sms("studentregister")
          Delayed::Job.enqueue(SmsManager.new(message,recipients))
        end
      end
      redirect_to :action => "previous_data", :id => @student.id
    end
  end

  def admission3_1
    @student = Student.find(params[:id])
    @parents = @student.student_guardian
    
    if @parents.empty?
      redirect_to :controller => "student", :action => "admission4", :id => @student.id
    end
    return if params[:immediate_contact].nil?
    
    if request.post?
      sms_setting = SmsSetting.new()
      #abort(params[:immediate_contact][:contact])
      @student = Student.update(@student.id, :immediate_contact_id => params[:immediate_contact][:contact])
      #      @student.update_attribute(:immediate_contact_id,params[:immediate_contact][:contact])
      #      
      @guardian = Guardian.find(params[:immediate_contact][:contact])      
      usernamep = @guardian.user.username
      champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
      api_endpoint = champs21_api_config['api_url']
      uri = URI(api_endpoint + "api/user/createuser")
      http = Net::HTTP.new(uri.host, uri.port)
      auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
      auth_req.set_form_data({"paid_id" => @guardian.user.id, "paid_username" => usernamep, "paid_password" => "123456","password" => "123456", "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @guardian.first_name, "last_name" => @guardian.last_name, "country" => @guardian.country_id, "email" => usernamep, "user_type" => "4" })
      auth_res = http.request(auth_req)
      @auth_response = JSON::parse(auth_res.body) 
      
      sql = "update students_guardians set `g_first_name`='#{@guardian.first_name}',`g_last_name`='#{@guardian.last_name}',`g_username`='#{usernamep}',`g_password`='123456',`guardian_id`= #{@guardian.user.id},`g_phone`='#{@guardian.mobile_phone}' where `student_id`= #{@student.user.id} and`school_id` = #{MultiSchool.current_school.id}"      
      CONN.execute sql         
      
      if sms_setting.application_sms_active and sms_setting.student_admission_sms_active and @student.is_sms_enabled
        recipients = []
        message = "#{t('student_admission_done')}   #{@student.admission_no} #{t('password_is')} 123456"
        if sms_setting.parent_sms_active
          unless @student.student_guardian.empty?
            guardians = @student.student_guardian
            guardians.each do |guardian|
              recipients.push guardian.mobile_phone unless guardian.mobile_phone.nil?
            end  
          end
          #          guardian = Guardian.find(@student.immediate_contact_id)
          #          recipients.push guardian.mobile_phone unless guardian.mobile_phone.nil?
        end
        unless recipients.empty? or !send_sms("studentregister")
          Delayed::Job.enqueue(SmsManager.new(message,recipients))
        end
      end
      flash[:notice] = "#{t('flash23')}"
      redirect_to :action => "profile", :id => @student.id
    end
  end

  def previous_data
    @student = Student.find(params[:id])
    new_entry = false
    @previous_data = StudentPreviousData.find_by_student_id(params[:id])
    if @previous_data.nil?
      @previous_data = StudentPreviousData.new params[:previous_data]
      new_entry = true
    end
    @previous_subject = StudentPreviousSubjectMark.find_all_by_student_id(@student)
    if request.post?
      if new_entry
        @previous_data.save
      else
        @previous_data.update_attributes(params[:previous_data])
      end  
      redirect_to :action => "admission4", :id => @student.id
    else
      return
    end
  end

  def previous_data_from_profile
    @student = Student.find(params[:id])
    @previous_data = StudentPreviousData.new(params[:student_previous_details])
    @previous_subject = StudentPreviousSubjectMark.find_all_by_student_id(@student)
    if request.post?
      @previous_data.save
      redirect_to :action => "profile", :id => @student.id
    else
      return
    end
  end

  def previous_data_edit
    @student = Student.find(params[:id])
    @previous_data = StudentPreviousData.find_by_student_id(params[:id])
    unless @previous_data.nil?
      @previous_subject = StudentPreviousSubjectMark.find_all_by_student_id(@student)
      if request.post?
        @previous_data.update_attributes(params[:previous_data])
        redirect_to :action => "show_previous_details", :id => @student.id
      end
    else
      redirect_to :action => "previous_data", :id => @student.id
    end
  end
  def previous_subject
    @student = Student.find(params[:id])
    @student_previous_subject_details=StudentPreviousSubjectMark.new
    render(:update) do |page|
      page.replace_html 'subject', :partial=>"previous_subject"
    end
  end

  def save_previous_subject
    @student_previous_subject_details = StudentPreviousSubjectMark.new params[:student_previous_subject_details]
    @student_previous_subject_details.save
    #@all_previous_subject = StudentPreviousSubjectMark.find(:all,:conditions=>"student_id = #{@previous_subject.student_id}")
  end

  def delete_previous_subject
    @previous_subject = StudentPreviousSubjectMark.find(params[:id])
    @student =Student.find(@previous_subject.student_id)
    if@previous_subject.delete
      @previous_subject=StudentPreviousSubjectMark.find_all_by_student_id(@student.id)
    end
    #@all_previous_subject = StudentPreviousSubjectMark.find(:all,:conditions=>"student_id = #{@previous_subject.student_id}")
  end

  def admission4
    @student = Student.find(params[:id])
    @student_additional_details = StudentAdditionalDetail.find_all_by_student_id(@student.id)
    @additional_fields = StudentAdditionalField.find(:all, :conditions=> "status = true", :order=>"priority ASC")
    if @additional_fields.empty?
      flash[:notice] = "#{t('flash9')} #{@student.first_name} #{@student.last_name}. #{t('new_admission_link')} <a href='/student/admission1'>#{t('click_here')}</a>"
      redirect_to(:controller => "student", :action => "profile", :id => @student.id ) and return
    end
    if request.post?
      @error=false
      mandatory_fields = StudentAdditionalField.find(:all, :conditions=>{:is_mandatory=>true, :status=>true})
      mandatory_fields.each do|m|
        unless params[:student_additional_details][m.id.to_s.to_sym].present?
          @student.errors.add_to_base("#{m.name} must contain atleast one selected option.")
          @error=true
        else
          if params[:student_additional_details][m.id.to_s.to_sym][:additional_info]==""
            @student.errors.add_to_base("#{m.name} cannot be blank.")
            @error=true
          end
        end
      end
      
      unless @error==true
        additional_field_ids_posted = []
        additional_field_ids = @additional_fields.map(&:id)
        if params[:student_additional_details].present?
          params[:student_additional_details].each_pair do |k, v|
            addl_info = v['additional_info']
            additional_field_ids_posted << k.to_i
            addl_field = StudentAdditionalField.find_by_id(k)
            if addl_field.input_type == "has_many"
              addl_info = addl_info.join(", ")
            end
            prev_record = StudentAdditionalDetail.find_by_student_id_and_additional_field_id(params[:id], k)
            unless prev_record.nil?
              unless addl_info.present?
                prev_record.destroy
              else
                prev_record.update_attributes(:additional_info => addl_info)
              end
            else
              addl_detail = StudentAdditionalDetail.new(:student_id => params[:id],
                :additional_field_id => k,:additional_info => addl_info)
              addl_detail.save if addl_detail.valid?
            end
          end
        end
        if additional_field_ids.present?
          StudentAdditionalDetail.find_all_by_student_id_and_additional_field_id(params[:id],(additional_field_ids - additional_field_ids_posted)).each do |additional_info|
            additional_info.destroy unless additional_info.student_additional_field.is_mandatory == true
          end
        end
        flash[:notice] = "#{t('flash9')} #{@student.first_name} #{@student.last_name}. #{t('new_admission_link')} <a href='/student/admission1'>#{t('click_here')}</a>"
        redirect_to :controller => "student", :action => "profile", :id => @student.id
      end
    end
  end

  def edit_admission4
    @student = Student.find(params[:id])
    @additional_fields = StudentAdditionalField.find(:all, :conditions=> "status = true")
    @additional_details = StudentAdditionalDetail.find_all_by_student_id(@student)
    
    if @additional_details.empty?
      redirect_to :controller => "student",:action => "admission4" , :id => @student.id
    end
    if request.post?
   
      params[:student_additional_details].each_pair do |k, v|
        row_id=StudentAdditionalDetail.find_by_student_id_and_additional_field_id(@student.id,k)
        unless row_id.nil?
          additional_detail = StudentAdditionalDetail.find_by_student_id_and_additional_field_id(@student.id,k)
          StudentAdditionalDetail.update(additional_detail.id,:additional_info => v['additional_info'])
        else
          StudentAdditionalDetail.create(:student_id=>@student.id,:additional_field_id=>k,:additional_info=>v['additional_info'])
        end
      end
      flash[:notice] = "#{t('student_text')} #{@student.first_name} #{t('flash2')}"
      redirect_to :action => "profile", :id => @student.id
    end
  end
  
  def add_student_session
    @all_details = StudentSession.find(:all)
    @additional_details = StudentSession.find(:all, :conditions=>{:status=>true})
    @inactive_additional_details = StudentSession.find(:all, :conditions=>{:status=>false})
    @student_session = StudentSession.new(params[:student_session])
    if request.post? 
      if @student_session.save
        flash[:notice] = "Saved Successfully"
        redirect_to :controller => "student", :action => "add_student_session"
      end
    end
  end
  def edit_student_session
    @all_details = StudentSession.find(:all)
    @additional_details = StudentSession.find(:all, :conditions=>{:status=>true})
    @inactive_additional_details = StudentSession.find(:all, :conditions=>{:status=>false})
    @student_session = StudentSession.find(params[:id])
    if request.get?
      render :action=>'add_student_session'
    else
      if @student_session.update_attributes(params[:student_session])
        flash[:notice] = "Saved Successfully"
        redirect_to :action => "add_student_session"
      else
        render :action=>"add_student_session"
      end
    end
  end
  def delete_student_session
    students = Student.find(:all ,:conditions=>"session_id = #{params[:id]}")
    if students.blank?
      StudentSession.find(params[:id]).destroy
      @additional_details = StudentSession.find(:all, :conditions=>{:status=>true})
      @inactive_additional_details = StudentSession.find(:all, :conditions=>{:status=>false})
      flash[:notice]="Successfully Deleted"
      redirect_to :action => "add_student_session"
    else
      flash[:notice]="Sorry Student Already Assigned For This Session"
      redirect_to :action => "add_student_session"
    end
  end
  
  def add_additional_details
    @all_details = StudentAdditionalField.find(:all, :order=>"priority ASC")
    @additional_details = StudentAdditionalField.find(:all, :conditions=>{:status=>true},:order=>"priority ASC")
    @inactive_additional_details = StudentAdditionalField.find(:all, :conditions=>{:status=>false},:order=>"priority ASC")
    @additional_field = StudentAdditionalField.new    
    @student_additional_field_option = @additional_field.student_additional_field_options.build
    if request.post?
      priority = 1
      unless @all_details.empty?
        last_priority = @all_details.map{|r| r.priority}.compact.sort.last
        priority = last_priority + 1
      end
      @additional_field = StudentAdditionalField.new(params[:student_additional_field])
      @additional_field.priority = priority
      if @additional_field.save
        flash[:notice] = "#{t('flash1')}"
        redirect_to :controller => "student", :action => "add_additional_details"
      end
    end
  end
  def change_field_priority
    @additional_field = StudentAdditionalField.find(params[:id])
    priority = @additional_field.priority
    @additional_fields = StudentAdditionalField.find(:all, :conditions=>{:status=>true}, :order=> "priority ASC").map{|b| b.priority.to_i}
    position = @additional_fields.index(priority)
    if params[:order]=="up"
      prev_field = StudentAdditionalField.find_by_priority(@additional_fields[position - 1])
    else
      prev_field = StudentAdditionalField.find_by_priority(@additional_fields[position + 1])
    end
    @additional_field.update_attributes(:priority=>prev_field.priority)
    prev_field.update_attributes(:priority=>priority.to_i)
    @additional_field = StudentAdditionalField.new
    @additional_details = StudentAdditionalField.find(:all, :conditions=>{:status=>true},:order=>"priority ASC")
    @inactive_additional_details = StudentAdditionalField.find(:all, :conditions=>{:status=>false},:order=>"priority ASC")
    render(:update) do|page|
      page.replace_html "category-list", :partial=>"additional_fields"
    end
  end

  def edit_additional_details
    @additional_details = StudentAdditionalField.find(:all, :conditions=>{:status=>true},:order=>"priority ASC")
    @inactive_additional_details = StudentAdditionalField.find(:all, :conditions=>{:status=>false},:order=>"priority ASC")
    @additional_field = StudentAdditionalField.find(params[:id])
    @student_additional_field_option = @additional_field.student_additional_field_options
    if request.get?
      render :action=>'add_additional_details'
    else
      if @additional_field.update_attributes(params[:student_additional_field])
        flash[:notice] = "#{t('flash2')}"
        redirect_to :action => "add_additional_details"
      else
        render :action=>"add_additional_details"
      end
    end
  end

  def delete_additional_details
    students = StudentAdditionalDetail.find(:all ,:conditions=>"additional_field_id = #{params[:id]}")
    if students.blank?
      StudentAdditionalField.find(params[:id]).destroy
      @additional_details = StudentAdditionalField.find(:all, :conditions=>{:status=>true},:order=>"priority ASC")
      @inactive_additional_details = StudentAdditionalField.find(:all, :conditions=>{:status=>false},:order=>"priority ASC")
      flash[:notice]="#{t('flash13')}"
      redirect_to :action => "add_additional_details"
    else
      flash[:notice]="#{t('flash14')}"
      redirect_to :action => "add_additional_details"
    end
  end

  def change_to_former
    @dependency = @student.former_dependency
    if request.post?
      @student.archive_student(params[:remove][:status_description],params[:leaving_date])
      dec_student_count_subscription
      render :update do |page|
        page.replace_html 'remove-student', :partial => 'student_tc_generate'
      end
    end
  end

  def generate_tc_pdf
    @student = ArchivedStudent.find_by_former_id(params[:id])
    
    student_electives = StudentsSubject.find_all_by_student_id(params[:id],:conditions=>"batch_id = #{@student.batch_id}")
    @group = @student.batch.course.group
    
    if student_electives
      student_electives.each do |elect|
        sub = Subject.find(elect.subject_id)
        if sub.code == "Bio"
           std_group_name = "Science" 
        elsif sub.code == "F&B"
           std_group_name = "Business Studies" 
        elsif sub.code == "Eco" or sub.code == "Islam" or sub.code == "Geo"
           std_group_name = "Humanities" 
        end  
      end
     
    end
    
    @std_guardians = @student.student_guardian
    @father = ArchivedGuardian.find_by_ward_id(@student.id, :conditions=>"relation = 'father'")
    @mother = ArchivedGuardian.find_by_ward_id(@student.id, :conditions=>"relation = 'mother'")
    @immediate_contact = ArchivedGuardian.find_by_ward_id(@student.immediate_contact_id) \
      unless @student.immediate_contact_id.nil? or @student.immediate_contact_id == ''
    if MultiSchool.current_school.code == "sagc"
      render :template => "student/generate_tc", :layout => false  
    else 
      render :pdf=>'generate_tc_pdf'
    end
  end

  def generate_all_tc_pdf
    @ids = params[:stud]
    @students = @ids.map { |st_id| ArchivedStudent.find(st_id) }
    
    render :pdf=>'generate_all_tc_pdf'
  end

  def destroy
    student = Student.find(params[:id])
    #unless student.check_dependency
    unless student.all_siblings.present?
      student.guardians.each do|guardian|
          
        champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
        api_endpoint = champs21_api_config['api_url']
        uri = URI(api_endpoint + "api/user/delete_by_paid_id")
        http = Net::HTTP.new(uri.host, uri.port)
        auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
        auth_req.set_form_data({"paid_id" => guardian.user.id, "paid_school_id" => MultiSchool.current_school.id})
        auth_res = http.request(auth_req)
        @auth_response = JSON::parse(auth_res.body)

        guardian.user.destroy if guardian.user.present?
        guardian.destroy
          
      end
    end
      
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    uri = URI(api_endpoint + "api/user/delete_by_paid_id")
    http = Net::HTTP.new(uri.host, uri.port)
    auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
    auth_req.set_form_data({"paid_id" => student.user.id, "paid_school_id" => MultiSchool.current_school.id})
    auth_res = http.request(auth_req)
    @auth_response = JSON::parse(auth_res.body)

    session[:student_id]=nil if student.id==session[:student_id]
    student.user.destroy
    student.destroy
    dec_student_count_subscription
    flash[:notice] = "#{t('flash10')}. #{student.admission_no}."
    redirect_to :controller => 'user', :action => 'dashboard'
    #else
    #flash[:warn_notice] = "#{t('flash15')}"
    #redirect_to  :action => 'remove', :id=>student.id
    #end
  end
  
  def edit_inactive_student
    @classes = []
    @batches = []
    @user = current_user
    @student = Student.find(params[:id])
    @student.pass = "01"
    @student.gender=@student.gender.downcase
    @student_user = @student.user
    @student_categories = StudentCategory.active
    @courses = []
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    
    unless @student.student_category.present? and @student_categories.collect(&:name).include?(@student.student_category.name)
      current_student_category=@student.student_category
      @student_categories << current_student_category if current_student_category.present?
    end 
    @batches = Batch.active
    @student.biometric_id = BiometricInformation.find_by_user_id(@student.user_id).try(:biometric_id)
    @application_sms_enabled = SmsSetting.find_by_settings_key("ApplicationEnabled")

    if request.post?
      params[:student].delete "pass"
      #abort(params[:student].inspect)
      unless params[:student][:image_file].blank?
        unless params[:student][:image_file].size.to_f > 280000
          if @student.update_attributes(params[:student])
            
            username = MultiSchool.current_school.code.to_s+"-"+@student.admission_no        
            champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
            api_endpoint = champs21_api_config['api_url']
            uri = URI(api_endpoint + "api/user/UpdateProfilePaidUser")
            http = Net::HTTP.new(uri.host, uri.port)
            auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
            auth_req.set_form_data({"paid_id" => @student.user.id, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @student.first_name, "middle_name" => @student.middle_name, "last_name" => @student.last_name, "gender" => (if @student.gender == 'm' then '1' else '0' end), "country" => @student.nationality_id, "dob" => @student.date_of_birth, "email" => username})
            auth_res = http.request(auth_req)
            @auth_response = JSON::parse(auth_res.body)
            
            flash[:notice] = "#{t('flash3')}"
            redirect_to :controller => "student", :action => "web_register", :id => @student.id
          end
        else
          flash[:notice] = "#{t('flash_msg11')}"
          redirect_to :controller => "student", :action => "edit_inactive_student", :id => @student.id
        end
      else
        if @student.update_attributes(params[:student])
          username = MultiSchool.current_school.code.to_s+"-"+@student.admission_no        
          champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
          api_endpoint = champs21_api_config['api_url']
          uri = URI(api_endpoint + "api/user/UpdateProfilePaidUser")
          http = Net::HTTP.new(uri.host, uri.port)
          auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
          auth_req.set_form_data({"paid_id" => @student.id, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @student.first_name, "middle_name" => @student.middle_name, "last_name" => @student.last_name, "gender" => (if @student.gender == 'm' then '1' else '0' end), "country" => @student.nationality_id, "dob" => @student.date_of_birth, "email" => username})
          auth_res = http.request(auth_req)
          @auth_response = JSON::parse(auth_res.body)
          
          flash[:notice] = "#{t('flash3')}"
          redirect_to :controller => "student", :action => "web_register", :id => @student.id
        else
          @classes = Course.find(:all, :conditions => ["course_name LIKE ?",params[:student][:class_name]])
          @selected_section = params[:student][:section]
          @batch_id = params[:student][:batch_id]
          @batch_no = params[:student][:batch_name]
          @course_name = params[:student][:class_name];
          
          batch = Batch.find params[:student][:batch_id]
          batch_name = batch.name
          batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
          @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
        end
      end
    else
      @batch_id = @student.batch_id
      @batch_no = @student.batch_id
      @batch_data = Batch.active.find(:first, :conditions => ["batches.id = ?", @batch_id])
      
      @course_data = Course.find_by_id(@batch_data.course_id)
      @course_name = @course_data.course_name

      @classes = Course.find(:all, :conditions => ["course_name LIKE ?",@course_name])
      @selected_section = @course_data.id
      
      batch = Batch.find @batch_id
      batch_name = batch.name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
      
    end
  end
  
  
  def edit_sagc
    @classes = []
    @batches = []
    @user = current_user
    @student = Student.find(params[:id])
    
    @previous_batch_id = @student.batch_id
    @previous_category_id = @student.student_category_id
    
    @student.pass = "01"
    @student.gender=@student.gender.downcase
    @student_user = @student.user
    @student_categories = StudentCategory.active
    @courses = []
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    
    unless @student.student_category.present? and @student_categories.collect(&:name).include?(@student.student_category.name)
      current_student_category=@student.student_category
      @student_categories << current_student_category if current_student_category.present?
    end 
    @batches = Batch.active
    @student.biometric_id = BiometricInformation.find_by_user_id(@student.user_id).try(:biometric_id)
    @application_sms_enabled = SmsSetting.find_by_settings_key("ApplicationEnabled")
    
    @action_name_main = "profile"
    if !params[:steps].blank? and params[:steps]=="one"
      @already_sibling_selected = false
      @guardian_already_added = false
      if @student.id != @student.sibling_id
        @already_sibling_selected = true
      else
        unless @student.guardians.blank?
          @guardian_already_added = true
        end
      end  
      if @already_sibling_selected == false
        if Configuration.find_by_config_key('EnableSibling').present? and Configuration.find_by_config_key('EnableSibling').config_value=="1" and @guardian_already_added == false
          @action_name_main = "admission1_2"
        else
          @action_name_main = "admission2"
        end
      else
        @action_name_main = "admission2"
      end  
    end

    if request.post?
      params[:student].delete "pass"
      if params[:student][:student_category_id] != "433"
        params[:student][:staff_id] = 0
      end
      #abort(params[:student].inspect)
      unless params[:student][:image_file].blank?
        unless params[:student][:image_file].size.to_f > 280000
          if @student.update_attributes(params[:student])
            
            if MultiSchool.current_school.id == 352
              if @previous_category_id != @student.student_category_id
                @finance_fees = FinanceFee.find_all_by_student_id(@student.id)
                @student_fees = @finance_fees.map{|s| s.fee_collection_id}

                @payed_fees=FinanceFee.find(:all,:joins=>"INNER JOIN fee_transactions on fee_transactions.finance_fee_id=finance_fees.id INNER JOIN finance_fee_collections on finance_fee_collections.id=finance_fees.fee_collection_id",:conditions=>"finance_fees.student_id=#{@student.id}",:select=>"finance_fees.fee_collection_id").map{|s| s.fee_collection_id}
                @payed_fees ||= []

                @fee_collection_dates =FinanceFeeParticular.find(:all,:joins=>"INNER JOIN collection_particulars on collection_particulars.finance_fee_particular_id=finance_fee_particulars.id INNER JOIN finance_fee_collections on finance_fee_collections.id=collection_particulars.finance_fee_collection_id",:conditions=>"finance_fee_particulars.batch_id='#{@student.batch_id}' and finance_fee_particulars.receiver_type='Batch'",:select=>"finance_fee_collections.*").uniq
                @fee_collection_dates.each do |date|
                  d = FinanceFeeCollection.find(date.id)
                  if @student_fees.include?(d.id)
                    fee = FinanceFee.find_by_student_id_and_fee_collection_id_batch_id_and_is_paid(@student.id, d.id, @student.batch_id, false)
                    unless fee.blank?
                      s = Student.find(@student.id)
                      FinanceFee.update_student_fee(d,s, fee)
                    else
                      s = Student.find(@student.id)
                      FinanceFee.new_student_fee(d,s)
                    end
                  end
                end
              end

              if @previous_batch_id != @student.batch_id
                @finance_fees = FinanceFee.find_all_by_student_id(@student.id)
                @student_fees = @finance_fees.map{|s| s.fee_collection_id}

                @payed_fees=FinanceFee.find(:all,:joins=>"INNER JOIN fee_transactions on fee_transactions.finance_fee_id=finance_fees.id INNER JOIN finance_fee_collections on finance_fee_collections.id=finance_fees.fee_collection_id",:conditions=>"finance_fees.student_id=#{@student.id}",:select=>"finance_fees.fee_collection_id").map{|s| s.fee_collection_id}
                @payed_fees ||= []

                @fee_collection_dates =FinanceFeeParticular.find(:all,:joins=>"INNER JOIN collection_particulars on collection_particulars.finance_fee_particular_id=finance_fee_particulars.id INNER JOIN finance_fee_collections on finance_fee_collections.id=collection_particulars.finance_fee_collection_id",:conditions=>"finance_fee_particulars.batch_id='#{@student.batch_id}' and finance_fee_particulars.receiver_type='Batch'",:select=>"finance_fee_collections.*").uniq
                @fee_collection_dates.each do |date|
                  d = FinanceFeeCollection.find(date.id)
                  if @student_fees.include?(d.id)
                    fee = FinanceFee.find_by_student_id_and_fee_collection_id_batch_id_and_is_paid(@student.id, d.id, @previous_batch_id, false)
                    fee.destroy if fee.finance_transactions.empty?
                    fee = FinanceFee.find_by_student_id_and_fee_collection_id_batch_id_and_is_paid(@student.id, d.id, @student.batch_id, false)
                    unless fee.blank?
                      s = Student.find(@student.id)
                      FinanceFee.update_student_fee(d,s, fee)
                    else
                      s = Student.find(@student.id)
                      FinanceFee.new_student_fee(d,s)
                    end
                  end
                end
              end
            end 
            username = MultiSchool.current_school.code.to_s+"-"+@student.admission_no        
            champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
            api_endpoint = champs21_api_config['api_url']
            uri = URI(api_endpoint + "api/user/UpdateProfilePaidUser")
            http = Net::HTTP.new(uri.host, uri.port)
            auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
            auth_req.set_form_data({"paid_id" => @student.user.id, "paid_username" => username, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @student.first_name, "middle_name" => @student.middle_name, "last_name" => @student.last_name, "gender" => (if @student.gender == 'm' then '1' else '0' end), "country" => @student.nationality_id, "dob" => @student.date_of_birth, "email" => username})
            auth_res = http.request(auth_req)
            @auth_response = JSON::parse(auth_res.body)
            
            flash[:notice] = "#{t('flash3')}"
            redirect_to :controller => "student", :action => @action_name_main, :id => @student.id
          end
        else
          flash[:notice] = "#{t('flash_msg11')}"
          if !params[:steps].blank? and params[:steps]=="one"
            redirect_to :controller => "student", :action => "edit",:steps=>"one", :id => @student.id
          else 
            redirect_to :controller => "student", :action => "edit", :id => @student.id
          end
        end
      else
        if @student.update_attributes(params[:student])
          found_paid_fees = false
          paid_fees_type = ""
          @fee_ids = []
          fee_ind = 0
          if MultiSchool.current_school.id == 352
            if @previous_category_id != @student.student_category_id
              @finance_fees = FinanceFee.find_all_by_student_id(@student.id)
              @student_fees = @finance_fees.map{|s| s.fee_collection_id}

              @fee_collection_dates =FinanceFeeParticular.find(:all,:joins=>"INNER JOIN collection_particulars on collection_particulars.finance_fee_particular_id=finance_fee_particulars.id INNER JOIN finance_fee_collections on finance_fee_collections.id=collection_particulars.finance_fee_collection_id",:conditions=>"finance_fee_particulars.batch_id='#{@student.batch_id}' and finance_fee_particulars.receiver_type='Batch'",:select=>"finance_fee_collections.*").uniq
              @fee_collection_dates.each do |date|
                d = FinanceFeeCollection.find(date.id)
                if @student_fees.include?(d.id)
                  fee = FinanceFee.find_by_student_id_and_fee_collection_id_and_batch_id_and_is_paid(@student.id, d.id, @student.batch_id, false)
                  unless fee.blank?
                      s = Student.find(@student.id)
                      FinanceFee.update_student_fee(d,s, fee)
                  else
                    fee = FinanceFee.find_by_student_id_and_fee_collection_id_and_batch_id_and_is_paid(@student.id, d.id, @student.batch_id, true)
                    unless fee.blank?
                      found_paid_fees = true
                      paid_fees_type = "category"
                      @fee_ids[fee_ind] = fee.id
                    else
                      s = Student.find(@student.id)
                      FinanceFee.new_student_fee(d,s)
                    end
                  end
                end
              end
            end

          #   if @previous_batch_id != @student.batch_id
          #     @finance_fees = FinanceFee.find_all_by_student_id(@student.id)
          #     @student_fees = @finance_fees.map{|s| s.fee_collection_id}
          #
          #     @fee_collection_dates =FinanceFeeParticular.find(:all,:joins=>"INNER JOIN collection_particulars on collection_particulars.finance_fee_particular_id=finance_fee_particulars.id INNER JOIN finance_fee_collections on finance_fee_collections.id=collection_particulars.finance_fee_collection_id",:conditions=>"finance_fee_particulars.batch_id='#{@student.batch_id}' and finance_fee_particulars.receiver_type='Batch'",:select=>"finance_fee_collections.*").uniq
          #     @fee_collection_dates.each do |date|
          #       d = FinanceFeeCollection.find(date.id)
          #       if @student_fees.include?(d.id)
          #         fee = FinanceFee.find_by_student_id_and_fee_collection_id_and_batch_id_and_is_paid(@student.id, d.id, @previous_batch_id, false)
          #         fee.destroy if fee.finance_transactions.empty?
          #         fee = FinanceFee.find_by_student_id_and_fee_collection_id_and_batch_id_and_is_paid(@student.id, d.id, @student.batch_id, false)
          #         unless fee.blank?
          #           s = Student.find(@student.id)
          #           FinanceFee.update_student_fee(d,s, fee)
          #         else
          #           s = Student.find(@student.id)
          #           FinanceFee.new_student_fee(d,s)
          #         end
          #       end
          #     end
          #   end
          end
          
          username = MultiSchool.current_school.code.to_s+"-"+@student.admission_no        
          champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
          api_endpoint = champs21_api_config['api_url']
          uri = URI(api_endpoint + "api/user/UpdateProfilePaidUser")
          http = Net::HTTP.new(uri.host, uri.port)
          auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
          auth_req.set_form_data({"paid_id" => @student.id, "paid_username" => username, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @student.first_name, "middle_name" => @student.middle_name, "last_name" => @student.last_name, "gender" => (if @student.gender == 'm' then '1' else '0' end), "country" => @student.nationality_id, "dob" => @student.date_of_birth, "email" => username})
          auth_res = http.request(auth_req)
          @auth_response = JSON::parse(auth_res.body)
          
          flash[:notice] = "#{t('flash3')}"
          #if found_paid_fees
          #  redirect_to :controller => "student", :action => "adjust_paid_fees", :id => @student.id, :paid_fees => @fee_ids, :paid_fees_type => paid_fees_type
          #else
            redirect_to :controller => "student", :action => @action_name_main, :id => @student.id
          #end
        else
          @classes = Course.find(:all, :conditions => ["course_name LIKE ?",params[:student][:class_name]])
          @selected_section = params[:student][:section]
          @section_name = params[:student][:section]
          @batch_id = params[:student][:batch_id]
          @batch_no = params[:student][:batch_name]
          @course_name = params[:student][:class_name];
          
          batch = Batch.find params[:student][:batch_id]
          batch_name = batch.name
          batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
          @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
        end
      end
    else
      @batch_id = @student.batch_id
      @batch_no = @student.batch_id
      @batch_data = Batch.active.find(:first, :conditions => ["batches.id = ?", @batch_id])
      
      @course_data = Course.find_by_id(@batch_data.course_id)
      @course_name = @course_data.course_name

      @classes = Course.find(:all, :conditions => ["course_name LIKE ?",@course_name])
      @selected_section = @course_data.id
      @section_name = @course_data.id
      
      batch = Batch.find @batch_id
      batch_name = batch.name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
      
    end
  end
  

  def edit
    @classes = []
    @batches = []
    @user = current_user
    @student = Student.find(params[:id])
    
    guardians = @student.student_guardian
    unless guardians.blank?
      iloop = 0
      guardians.each do |duardian|
        if iloop == 0
          @guardian_father = duardian 
        end
        if iloop == 1
          @guardian_mother = duardian
        end
        iloop = iloop+1
      end
    end
   
    
    @previous_batch_id = @student.batch_id
    @previous_category_id = @student.student_category_id
    
    @student.pass = "01"
    @student.gender=@student.gender.downcase
    @student_user = @student.user
    @student_categories = StudentCategory.active
    @courses = []
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    
    unless @student.student_category.present? and @student_categories.collect(&:name).include?(@student.student_category.name)
      current_student_category=@student.student_category
      @student_categories << current_student_category if current_student_category.present?
    end 
    @batches = Batch.active
    @student.biometric_id = BiometricInformation.find_by_user_id(@student.user_id).try(:biometric_id)
    @application_sms_enabled = SmsSetting.find_by_settings_key("ApplicationEnabled")
    
    @action_name_main = "profile"
    if !params[:steps].blank? and params[:steps]=="one"
      @already_sibling_selected = false
      @guardian_already_added = false
      if @student.id != @student.sibling_id
        @already_sibling_selected = true
      else
        unless @student.guardians.blank?
          @guardian_already_added = true
        end
      end  
      if @already_sibling_selected == false
        if Configuration.find_by_config_key('EnableSibling').present? and Configuration.find_by_config_key('EnableSibling').config_value=="1" and @guardian_already_added == false
          @action_name_main = "admission1_2"
        else
          @action_name_main = "admission2"
        end
      else
        @action_name_main = "admission2"
      end  
    end

    if request.post?
      if !params[:f_first_name].blank? && !@guardian_father.blank?
          @guardian_father.first_name = params[:f_first_name]
          @guardian_father.save
      end
      if !params[:m_first_name].blank? && !@guardian_mother.blank?
          @guardian_mother.first_name = params[:m_first_name]
          @guardian_mother.save
      end
      params[:student].delete "pass"
      if params[:student][:student_category_id] != "433"
        params[:student][:staff_id] = 0
      end
      #abort(params[:student].inspect)
      unless params[:student][:image_file].blank?
        unless params[:student][:image_file].size.to_f > 280000
          if @student.update_attributes(params[:student])
            
            if MultiSchool.current_school.id == 352
              if @previous_category_id != @student.student_category_id
                @finance_fees = FinanceFee.find_all_by_student_id(@student.id)
                @student_fees = @finance_fees.map{|s| s.fee_collection_id}

                @payed_fees=FinanceFee.find(:all,:joins=>"INNER JOIN fee_transactions on fee_transactions.finance_fee_id=finance_fees.id INNER JOIN finance_fee_collections on finance_fee_collections.id=finance_fees.fee_collection_id",:conditions=>"finance_fees.student_id=#{@student.id}",:select=>"finance_fees.fee_collection_id").map{|s| s.fee_collection_id}
                @payed_fees ||= []

                @fee_collection_dates =FinanceFeeParticular.find(:all,:joins=>"INNER JOIN collection_particulars on collection_particulars.finance_fee_particular_id=finance_fee_particulars.id INNER JOIN finance_fee_collections on finance_fee_collections.id=collection_particulars.finance_fee_collection_id",:conditions=>"finance_fee_particulars.batch_id='#{@student.batch_id}' and finance_fee_particulars.receiver_type='Batch'",:select=>"finance_fee_collections.*").uniq
                @fee_collection_dates.each do |date|
                  d = FinanceFeeCollection.find(date.id)
                  if @student_fees.include?(d.id)
                    fee = FinanceFee.find_by_student_id_and_fee_collection_id_batch_id_and_is_paid(@student.id, d.id, @student.batch_id, false)
                    unless fee.blank?
                      s = Student.find(@student.id)
                      FinanceFee.update_student_fee(d,s, fee)
                    else
                      s = Student.find(@student.id)
                      FinanceFee.new_student_fee(d,s)
                    end
                  end
                end
              end

              if @previous_batch_id != @student.batch_id
                @finance_fees = FinanceFee.find_all_by_student_id(@student.id)
                @student_fees = @finance_fees.map{|s| s.fee_collection_id}

                @payed_fees=FinanceFee.find(:all,:joins=>"INNER JOIN fee_transactions on fee_transactions.finance_fee_id=finance_fees.id INNER JOIN finance_fee_collections on finance_fee_collections.id=finance_fees.fee_collection_id",:conditions=>"finance_fees.student_id=#{@student.id}",:select=>"finance_fees.fee_collection_id").map{|s| s.fee_collection_id}
                @payed_fees ||= []

                @fee_collection_dates =FinanceFeeParticular.find(:all,:joins=>"INNER JOIN collection_particulars on collection_particulars.finance_fee_particular_id=finance_fee_particulars.id INNER JOIN finance_fee_collections on finance_fee_collections.id=collection_particulars.finance_fee_collection_id",:conditions=>"finance_fee_particulars.batch_id='#{@student.batch_id}' and finance_fee_particulars.receiver_type='Batch'",:select=>"finance_fee_collections.*").uniq
                @fee_collection_dates.each do |date|
                  d = FinanceFeeCollection.find(date.id)
                  if @student_fees.include?(d.id)
                    fee = FinanceFee.find_by_student_id_and_fee_collection_id_batch_id_and_is_paid(@student.id, d.id, @previous_batch_id, false)
                    fee.destroy if fee.finance_transactions.empty?
                    fee = FinanceFee.find_by_student_id_and_fee_collection_id_batch_id_and_is_paid(@student.id, d.id, @student.batch_id, false)
                    unless fee.blank?
                      s = Student.find(@student.id)
                      FinanceFee.update_student_fee(d,s, fee)
                    else
                      s = Student.find(@student.id)
                      FinanceFee.new_student_fee(d,s)
                    end
                  end
                end
              end
            end 
            username = MultiSchool.current_school.code.to_s+"-"+@student.admission_no        
            champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
            api_endpoint = champs21_api_config['api_url']
            uri = URI(api_endpoint + "api/user/UpdateProfilePaidUser")
            http = Net::HTTP.new(uri.host, uri.port)
            auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
            auth_req.set_form_data({"paid_id" => @student.user.id, "paid_username" => username, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @student.first_name, "middle_name" => @student.middle_name, "last_name" => @student.last_name, "gender" => (if @student.gender == 'm' then '1' else '0' end), "country" => @student.nationality_id, "dob" => @student.date_of_birth, "email" => username})
            auth_res = http.request(auth_req)
            @auth_response = JSON::parse(auth_res.body)
            
            flash[:notice] = "#{t('flash3')}"
            redirect_to :controller => "student", :action => @action_name_main, :id => @student.id
          end
        else
          flash[:notice] = "#{t('flash_msg11')}"
          if !params[:steps].blank? and params[:steps]=="one"
            redirect_to :controller => "student", :action => "edit",:steps=>"one", :id => @student.id
          else 
            redirect_to :controller => "student", :action => "edit", :id => @student.id
          end
        end
      else
        if @student.update_attributes(params[:student])
          found_paid_fees = false
          paid_fees_type = ""
          @fee_ids = []
          fee_ind = 0
          if MultiSchool.current_school.id == 352
            if @previous_category_id != @student.student_category_id
              @finance_fees = FinanceFee.find_all_by_student_id(@student.id)
              @student_fees = @finance_fees.map{|s| s.fee_collection_id}

              @fee_collection_dates =FinanceFeeParticular.find(:all,:joins=>"INNER JOIN collection_particulars on collection_particulars.finance_fee_particular_id=finance_fee_particulars.id INNER JOIN finance_fee_collections on finance_fee_collections.id=collection_particulars.finance_fee_collection_id",:conditions=>"finance_fee_particulars.batch_id='#{@student.batch_id}' and finance_fee_particulars.receiver_type='Batch'",:select=>"finance_fee_collections.*").uniq
              @fee_collection_dates.each do |date|
                d = FinanceFeeCollection.find(date.id)
                if @student_fees.include?(d.id)
                  fee = FinanceFee.find_by_student_id_and_fee_collection_id_and_batch_id_and_is_paid(@student.id, d.id, @student.batch_id, false)
                  unless fee.blank?
                      s = Student.find(@student.id)
                      FinanceFee.update_student_fee(d,s, fee)
                  else
                    fee = FinanceFee.find_by_student_id_and_fee_collection_id_and_batch_id_and_is_paid(@student.id, d.id, @student.batch_id, true)
                    unless fee.blank?
                      found_paid_fees = true
                      paid_fees_type = "category"
                      @fee_ids[fee_ind] = fee.id
                    else
                      s = Student.find(@student.id)
                      FinanceFee.new_student_fee(d,s)
                    end
                  end
                end
              end
            end

          #   if @previous_batch_id != @student.batch_id
          #     @finance_fees = FinanceFee.find_all_by_student_id(@student.id)
          #     @student_fees = @finance_fees.map{|s| s.fee_collection_id}
          #
          #     @fee_collection_dates =FinanceFeeParticular.find(:all,:joins=>"INNER JOIN collection_particulars on collection_particulars.finance_fee_particular_id=finance_fee_particulars.id INNER JOIN finance_fee_collections on finance_fee_collections.id=collection_particulars.finance_fee_collection_id",:conditions=>"finance_fee_particulars.batch_id='#{@student.batch_id}' and finance_fee_particulars.receiver_type='Batch'",:select=>"finance_fee_collections.*").uniq
          #     @fee_collection_dates.each do |date|
          #       d = FinanceFeeCollection.find(date.id)
          #       if @student_fees.include?(d.id)
          #         fee = FinanceFee.find_by_student_id_and_fee_collection_id_and_batch_id_and_is_paid(@student.id, d.id, @previous_batch_id, false)
          #         fee.destroy if fee.finance_transactions.empty?
          #         fee = FinanceFee.find_by_student_id_and_fee_collection_id_and_batch_id_and_is_paid(@student.id, d.id, @student.batch_id, false)
          #         unless fee.blank?
          #           s = Student.find(@student.id)
          #           FinanceFee.update_student_fee(d,s, fee)
          #         else
          #           s = Student.find(@student.id)
          #           FinanceFee.new_student_fee(d,s)
          #         end
          #       end
          #     end
          #   end
          end
          
          username = MultiSchool.current_school.code.to_s+"-"+@student.admission_no        
          champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
          api_endpoint = champs21_api_config['api_url']
          uri = URI(api_endpoint + "api/user/UpdateProfilePaidUser")
          http = Net::HTTP.new(uri.host, uri.port)
          auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
          auth_req.set_form_data({"paid_id" => @student.id, "paid_username" => username, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @student.first_name, "middle_name" => @student.middle_name, "last_name" => @student.last_name, "gender" => (if @student.gender == 'm' then '1' else '0' end), "country" => @student.nationality_id, "dob" => @student.date_of_birth, "email" => username})
          auth_res = http.request(auth_req)
          @auth_response = JSON::parse(auth_res.body)
          
          flash[:notice] = "#{t('flash3')}"
          #if found_paid_fees
          #  redirect_to :controller => "student", :action => "adjust_paid_fees", :id => @student.id, :paid_fees => @fee_ids, :paid_fees_type => paid_fees_type
          #else
            redirect_to :controller => "student", :action => @action_name_main, :id => @student.id
          #end
        else
          @classes = Course.find(:all, :conditions => ["course_name LIKE ?",params[:student][:class_name]])
          @selected_section = params[:student][:section]
          @section_name = params[:student][:section]
          @batch_id = params[:student][:batch_id]
          @batch_no = params[:student][:batch_name]
          @course_name = params[:student][:class_name];
          
          batch = Batch.find params[:student][:batch_id]
          batch_name = batch.name
          batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
          @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
        end
      end
    else
      @batch_id = @student.batch_id
      @batch_no = @student.batch_id
      @batch_data = Batch.active.find(:first, :conditions => ["batches.id = ?", @batch_id])
      
      @course_data = Course.find_by_id(@batch_data.course_id)
      @course_name = @course_data.course_name

      @classes = Course.find(:all, :conditions => ["course_name LIKE ?",@course_name])
      @selected_section = @course_data.id
      @section_name = @course_data.id
      
      batch = Batch.find @batch_id
      batch_name = batch.name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
      
    end
  end
  
  def adjust_paid_fees
    @student = Student.find(params[:id])
    unless params[:paid_fees_type].nil?
      @paid_fees_type = params[:paid_fees_type]
      @paid_fees = params[:paid_fees]
    end
  end
  
  def edit_guardian_own
    @parent = Guardian.find(current_user.guardian_entry.id)
    
    @countries = Country.all
    params[:parent_detail].delete "ward_id" if  params[:parent_detail]
    if request.post? and @parent.update_attributes(params[:parent_detail])  
      g_students = GuardianStudents.find_all_by_guardian_id(@parent.id)
      
      unless g_students.nil?
        g_students.each do |g_student|
          GuardianStudents.update(g_student.id, :relation=> params[:parent_detail][:relation])
        end
      end
      
      unless @parent.user.nil?
        User.update(@parent.user.id, :first_name=> @parent.first_name, :last_name=> @parent.last_name, :email=> @parent.email, :role =>"Parent")
      else
        @parent.create_guardian_user(@student)
      end
      #      end
      flash[:notice] = "#{t('student.flash4')}"
      redirect_to :controller => "student", :action => "guardians", :id => current_user.guardian_entry.current_ward_id
    end
  end
  
  def edit_student_guardian
    @user = current_user
    @student = Student.find(params[:id])
    @additional_fields = StudentAdditionalField.find(:all, :conditions=> "status = true", :order=>"priority ASC")
    @additional_details = StudentAdditionalDetail.find_all_by_student_id(@student)
    if MultiSchool.current_school.id != 352 and MultiSchool.current_school.id != 346
      @student.pass = "01"
    end
    @student.gender=@student.gender.downcase
    @student_user = @student.user
    

    if request.post?
      if MultiSchool.current_school.id != 352 and MultiSchool.current_school.id != 346
        params[:student].delete "pass"
        params[:student_additional_details].each_pair do |k, v|
          additional_detail=StudentAdditionalDetail.find_by_student_id_and_additional_field_id(@student.id,k)
          unless additional_detail.blank?
            if v['additional_info'].blank?
              additional_detail.destroy
            else 
              StudentAdditionalDetail.update(additional_detail.id,:additional_info => v['additional_info'])
            end
          else
            StudentAdditionalDetail.create(:student_id=>@student.id,:additional_field_id=>k,:additional_info=>v['additional_info'])
          end
        end
      end
      unless params[:student][:image_file].blank?
        unless params[:student][:image_file].size.to_f > 280000
          if @student.update_attributes(params[:student])
            username = MultiSchool.current_school.code.to_s+"-"+@student.admission_no        
            champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
            api_endpoint = champs21_api_config['api_url']
            uri = URI(api_endpoint + "api/user/UpdateProfilePaidUser")
            http = Net::HTTP.new(uri.host, uri.port)
            auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
            password_main = ""
            unless params[:student][:pass].blank?
              password_main = params[:student][:pass]
            end
            auth_req.set_form_data({"paid_id" => @student.user.id,"paid_password"=>password_main, "paid_username" => username, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @student.first_name, "middle_name" => @student.middle_name, "last_name" => @student.last_name, "gender" => (if @student.gender == 'm' then '1' else '0' end), "country" => @student.nationality_id, "dob" => @student.date_of_birth, "email" => username})
            auth_res = http.request(auth_req)
            @auth_response = JSON::parse(auth_res.body)
            
            flash[:notice] = "#{t('flash3')}"
            redirect_to :controller => "student", :action => "profile", :id => @student.id
          end
        else
          flash[:notice] = "#{t('flash_msg11')}"
          redirect_to :controller => "student", :action => "edit", :id => @student.id
        end
      else
        
        if @student.update_attributes(params[:student])
          
          username = MultiSchool.current_school.code.to_s+"-"+@student.admission_no        
          champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
          api_endpoint = champs21_api_config['api_url']
          uri = URI(api_endpoint + "api/user/UpdateProfilePaidUser")
          http = Net::HTTP.new(uri.host, uri.port)
          auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
          auth_req.set_form_data({"paid_id" => @student.id, "paid_username" => username, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s, "first_name" => @student.first_name, "middle_name" => @student.middle_name, "last_name" => @student.last_name, "gender" => (if @student.gender == 'm' then '1' else '0' end), "country" => @student.nationality_id, "dob" => @student.date_of_birth, "email" => username})
          auth_res = http.request(auth_req)
          @auth_response = JSON::parse(auth_res.body)
          
          flash[:notice] = "#{t('flash3')}"
          redirect_to :controller => "student", :action => "profile", :id => @student.id
        
        end
      end
    else 
    end
  end
  
  
  
  
  def only_allowed_when_parmitted
    @config = Configuration.find_by_config_key('ParentCanEdit')
    if @config.blank? or @config.config_value.blank? or @config.config_value.to_i == 0
      flash[:notice] = "#{t('flash_msg4')}"
      redirect_to :controller => 'user', :action => 'dashboard'
    else
      @allow_access = true
    end 
  end


  def edit_guardian
    @parent = Guardian.find(params[:id])
    params[:student_id].present? ? @student = Student.find(params[:student_id]): @student = Student.find(params[:parent_detail][:ward_id])
    @countries = Country.all
    params[:parent_detail].delete "ward_id" if  params[:parent_detail]
    if request.post? and @parent.update_attributes(params[:parent_detail])    
      #      if @parent.email.blank?
      #        @parent.email= "noreplyp#{@parent.ward.admission_no}@champs21.com"
      #        @parent.save
      #      end
      #      if @parent.id  == @student.immediate_contact_id
      
      g_students = GuardianStudents.find_by_student_id_and_guardian_id(@student.id,@parent.id)
      
      unless g_students.nil?
        GuardianStudents.update(g_students.id,:student_id=> @student.id, :guardian_id=> @parent.id , :relation=> params[:parent_detail][:relation])
      end
      
      unless @parent.user.nil?
        User.update(@parent.user.id, :first_name=> @parent.first_name, :last_name=> @parent.last_name, :email=> @parent.email, :role =>"Parent")
      else
        @parent.create_guardian_user(@student)
      end
      #      end
      flash[:notice] = "#{t('student.flash4')}"
      if !params[:steps].blank? and params[:steps]=="one" 
        redirect_to :controller => "student", :action => "admission2", :id => @student.id
      else
        redirect_to :controller => "student", :action => "guardians", :id => @student.id
      end
    end
  end

  def email
    if @student.is_email_enabled
      @sms_module = Configuration.available_modules
      sender = current_user.email
      if request.post?
        recipient_list = []
        case params['email']['recipients']
        when 'Student'
          recipient_list << @student.email unless @student.email == ""
        when 'Guardian'
          recipient_list << @student.immediate_contact.email unless (@student.immediate_contact.nil? or @student.immediate_contact.email=="")
        when 'Student & Guardian'
          recipient_list << @student.email unless @student.email == ""
          recipient_list << @student.immediate_contact.email unless (@student.immediate_contact.nil? or @student.immediate_contact.email=="")
        end
        begin
          unless recipient_list.empty?
            Champs21Mailer::deliver_email(sender, recipient_list, params['email']['subject'], params['email']['message'])
            flash[:notice] = "#{t('flash12')} #{recipient_list.join(', ')}"
            redirect_to :controller => 'student', :action => 'profile', :id => @student.id
          else
            @student.errors.add_to_base("#{t('flash20')}")
          end
        rescue Exception => e
          puts "Error----------#{e.message}----------#{e.backtrace.inspect}"
          @student.errors.add_to_base("#{t('flash21')}")
        end
      end
    else
      flash[:notice] = "#{t('flash_msg4')}"
      redirect_to :controller => 'user', :action => 'dashboard'
    end
  end

  def exam_report
    @user = current_user
    @examtype = ExaminationType.find(params[:exam])
    @course = Course.find(params[:course])
    @student = Student.find(params[:student]) if params[:student]
    @student ||= @course.students.first
    @prev_student = @student.previous_student
    @next_student = @student.next_student
    @subjects = @course.subjects_with_exams
    @results = {}
    @subjects.each do |s|
      exam = Examination.find_by_subject_id_and_examination_type_id(s, @examtype)
      res = ExaminationResult.find_by_examination_id_and_student_id(exam, @student)
      @results[s.id.to_s] = { 'subject' => s, 'result' => res } unless res.nil?
    end
    @graph = open_flash_chart_object(770, 350, "/student/graph_for_exam_report?course=#{@course.id}&examtype=#{@examtype.id}&student=#{@student.id}")
  end

  def update_student_result_for_examtype
    @student = Student.find(params[:student])
    @examtype = ExaminationType.find(params[:examtype])
    @course = @student.course
    @prev_student = @student.previous_student
    @next_student = @student.next_student
    @subjects = @course.subjects_with_exams
    @results = {}
    @subjects.each do |s|
      exam = Examination.find_by_subject_id_and_examination_type_id(s, @examtype)
      res = ExaminationResult.find_by_examination_id_and_student_id(exam, @student)
      @results[s.id.to_s] = { 'subject' => s, 'result' => res } unless res.nil?
    end
    @graph = open_flash_chart_object(770, 350, "/exam/graph_for_student_exam_result?course=#{@course.id}&examtype=#{@examtype.id}&student=#{@student.id}")
    render(:update) { |page| page.replace_html 'exam-results', :partial => 'student_result_for_examtype' }
  end

  def previous_years_marks_overview
    @student = Student.find(params[:student])
    @all_courses = @student.all_courses
    @graph = open_flash_chart_object(770, 350, "/student/graph_for_previous_years_marks_overview?student=#{params[:student]}&graphtype=#{params[:graphtype]}")
  end 

  def class_test_report
    if current_user.student
      @student = Student.find(current_user.student_record.id)
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      @student = Student.find_by_id(target) 
    end
    
    @batch = @student.batch
    
    #@previous_batch = @student.batch_students
    @previous_batch = Batch.all(:joins=>[:batch_students],:conditions=>["batch_students.student_id=#{@student.id}"]).uniq
    @courses_all = Batch.all(:joins=>[:batch_students],:conditions=>["batch_students.student_id=#{@student.id}"],:include=>:course).uniq
    
    @previous_batch.push(@batch)
    #abort @previous_batch.inspect
    get_class_test_report
    @extra = "class_test"
    if params[:extra]
      @extra = params[:extra].to_s
      if @extra == "term"  
        @exam_groups = @batch.exam_groups
        @exam_groups.reject!{|e| e.result_published==false or e.exam_category!=3}
      elsif @extra == "progress"  
        @grouped_exams = GroupedExam.find_all_by_batch_id(@batch.id)
        @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"no_exams = false AND elective_group_id IS NULL AND is_deleted = false")
        @student_electives =StudentsSubject.all(:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
        @elective_subjects = []
        @student_electives.each do |e|
          @elective_subjects.push Subject.find(e.subject_id)
        end
        @subjects = @normal_subjects+@elective_subjects
      else
        @extra = "class_test"
        @class_test = []
        if @class_test_report_data['status']['code'].to_i == 200
          @class_test = @class_test_report_data['data']['class_test_report']
        end
      end  
    else
      @class_test = []
      if @class_test_report_data['status']['code'].to_i == 200
        @class_test = @class_test_report_data['data']['class_test_report']
      end
    end
  end
  
  #def class_test_report
    
  #end
  def combined_exam
    if current_user.student
      @student = Student.find(current_user.student_record.id)
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      @student = Student.find_by_id(target) 
    end
    @batch = @student.batch
    @all_connect_exam = ExamConnect.active.find_all_by_batch_id(@batch.id,:conditions=>"is_published = 1")
    
    render :partial=>"combined_exam"
  end
  
  def progress_report
    if current_user.student
      @student = Student.find(current_user.student_record.id)
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      @student = Student.find_by_id(target) 
    end
    @batch = @student.batch
    @grouped_exams = GroupedExam.find_all_by_batch_id(@batch.id)
    @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"no_exams = false AND elective_group_id IS NULL AND is_deleted = false")
    @student_electives =StudentsSubject.all(:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
    @elective_subjects = []
    @student_electives.each do |e|
      @elective_subjects.push Subject.find(e.subject_id)
    end
    @subjects = @normal_subjects+@elective_subjects
    
    render :partial=>"progress_report", :locals=>{:subjects=>@subjects}
  end
  
  def term_test_report
    if current_user.student
      @student = Student.find(current_user.student_record.id)
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      @student = Student.find_by_id(target) 
    end
    #@batch = @student.batch
    #abort params[:batch_id].inspect
    @batch = Batch.find_by_id(params[:batch_id])
    @exam_groups = @batch.exam_groups
    @exam_groups.reject!{|e| e.result_published==false or e.exam_category!=3}
    
    render :partial=>"term_test", :locals=>{:exam_groups=>@exam_groups}
  end
  
  def class_test_report_single
    get_class_test_report
    @class_test = []
    if @class_test_report_data['status']['code'].to_i == 200
      @class_test = @class_test_report_data['data']['class_test_report']
    end
    render :partial=>"class_test", :locals=>{:class_test=>@class_test}
  end
  
  def reports
    if (current_user.parent? or current_user.student?) and params[:p].blank?
      redirect_to :controller => "student", :action => 'class_test_report'
    end
    @sms_module = Configuration.available_modules
    @batch = @student.batch
    @grouped_exams = GroupedExam.find_all_by_batch_id(@batch.id)
    @normal_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>"no_exams = false AND elective_group_id IS NULL AND is_deleted = false")
    @student_electives =StudentsSubject.all(:conditions=>{:student_id=>@student.id,:batch_id=>@batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
    @elective_subjects = []
    @student_electives.each do |e|
      @elective_subjects.push Subject.find(e.subject_id)
    end
    @subjects = @normal_subjects+@elective_subjects
    @exam_groups = @batch.exam_groups
    @exam_groups.reject!{|e| e.result_published==false}
    @old_batches = @student.graduated_batches
  end

  def search_ajax  
    if params[:option] == "guardian"
      @gur_bool = true
      if params[:query].length>= 3
        params[:query].gsub! '+', ' '
        @guardians = Guardian.find(:all,
          :conditions => ["first_name LIKE ?  OR last_name LIKE ?
                          OR (concat(first_name, \" \", last_name) LIKE ? )",
            "#{params[:query]}%","#{params[:query]}%","%#{params[:query]}%"],
          :order => "first_name asc") unless params[:query] == ''
      end 
      render :partial => "search_ajax"
    elsif params[:option] == "active" or params[:option]=="sibling"
      if params[:query].length>= 3
        params[:query].gsub! '+', ' '
        @students = Student.active.find(:all,
          :conditions => ["first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                            OR admission_no LIKE ? OR (concat(first_name, \" \", last_name) LIKE ? ) OR (concat(first_name, \"+\", last_name) LIKE ? ) ",
            "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
            "%#{params[:query]}%", "%#{params[:query]}%", "%#{params[:query]}%" ],
          :order => "batch_id asc,first_name asc",:include =>  [{:batch=>:course}]) unless params[:query] == ''
      else
        @students = Student.active.find(:all,
          :conditions => ["admission_no = ? " , params[:query]],
          :order => "batch_id asc,first_name asc",:include =>  [{:batch=>:course}]) unless params[:query] == ''
      end   
      @students.reject!{|r| r.immediate_contact_id.nil?} if @students.present? and params[:option]=="sibling"
      render :layout => false
    else
      if params[:query].length>= 3
        @archived_students = ArchivedStudent.find(:all,
          :conditions => ["first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                            OR admission_no = ? OR (concat(first_name, \" \", last_name) LIKE ? ) ",
            "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
            "#{params[:query]}", "#{params[:query]}" ],
          :order => "batch_id asc,first_name asc",:include =>  [{:batch=>:course}]) unless params[:query] == ''
      else
        @archived_students = ArchivedStudent.find(:all,
          :conditions => ["admission_no = ? " , params[:query]],
          :order => "batch_id asc,first_name asc",:include =>  [{:batch=>:course}]) unless params[:query] == ''
      end
      render :partial => "search_ajax"
    end   
  end

  def student_annual_overview
    @graph = open_flash_chart_object(770, 350, "/student/graph_for_student_annual_overview?student=#{params[:student]}&year=#{params[:year]}")
  end

  def subject_wise_report
    @student = Student.active.find(params[:student])
    @subject = Subject.find(params[:subject])
    @examtypes = @subject.examination_types
    @graph = open_flash_chart_object(770, 350, "/student/graph_for_subject_wise_report_for_one_subject?student=#{params[:student]}&subject=#{params[:subject]}")
  end

  def add_guardian
    @parent_info = @student.guardians.build(params[:parent_info])
    @parent_info.set_immediate_contact = @student.admission_no
    @countries = Country.all
    if request.post? and @parent_info.save
      #       @parent_info.update_attribute(:ward_id,@student.guardians.first.ward_id) if @student.guardians.present?
      check_guardian = GuardianStudents.find_by_student_id_and_guardian_id(@student.id,@parent_info.id)
      if check_guardian.nil?
        stdgu = GuardianStudents.new
        stdgu.student_id = @student.id
        stdgu.guardian_id = @parent_info.id
        stdgu.relation = @parent_info.relation  
        stdgu.save
      end 
      flash[:notice] = "#{t('flash5')} #{@student.full_name}"
      redirect_to :controller => "student" , :action => "admission3_1", :id => @student.id
    end
  end
  
  def list_applicant_by_course
    if params[:batch_id].nil?
      batch_name = ""
      unless params[:student][:batch_name].nil?
        batch_id = params[:student][:batch_name]
        batches_data = Batch.find_by_id(batch_id)
        batch_name = batches_data.name

      end
      course_id = 0
      unless params[:course_id].nil?
        course_id = params[:course_id]
      end
      if course_id == 0
        unless params[:student][:section].nil?
          course_id = params[:student][:section]
        end
      end

      if course_id.to_i > 0
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
      
        @batch_id = 0
        unless @batch_data.nil?
          @batch_id = @batch_data.id 
        end
      else
        @batch_id = params[:batch_id]
      end
    else
      @batch_id = params[:batch_id]
    end 
    
    unless params[:student_id].nil?
      unless params[:status_change].nil?
       
        if params[:status_change] == "1"
          activated_student_and_guardian(params[:student_id])
          flash[:notice] = "#{t('student_activated')}"
        elsif params[:status_change] == "0"
          delete_student_and_guardian(params[:student_id])
          flash[:notice] = "#{t('student_parmanantly_deleted')}"
        end  
        
      end
    end
    
    unless params[:student_ids].nil?
      unless params[:status_change].nil?
       
        if params[:status_change] == "1"
          student_ids = params[:student_ids].split("|") 
          student_ids.each do |r|
            activated_student_and_guardian(r)
          end
          flash[:notice] = "#{t('student_activated')}"
        elsif params[:status_change] == "0"
          student_ids = params[:student_ids].split("|") 
          student_ids.each do |r|
            delete_student_and_guardian(r)
          end
          flash[:notice] = "#{t('student_parmanantly_deleted')}"
        end  
        
      end
    end
    
    if @batch_id.to_i==0
      sql = "SELECT std.*,GROUP_CONCAT( CONCAT(gu.first_name, '  ',gu.last_name ) SEPARATOR '|') as gnames,batch.name as bname,course.course_name,course.section_name
  FROM
  students AS std left join users as us on std.user_id=us.id
  join guardians as gu on gu.ward_id=std.id
  left join batches as batch on std.batch_id=batch.id
  left join courses as course on course_id=course.id
  WHERE std.school_id = '#{MultiSchool.current_school.id}'
  and us.is_approved=0 and us.free_user_id!=0
  GROUP BY std.id"
      @students = Student.paginate_by_sql(sql, :page => params[:page], :per_page => 25)
      render(:update) { |page| page.replace_html 'students', :partial => 'applicant_by_course' }
    else
      sql = "SELECT std.*,GROUP_CONCAT( CONCAT(gu.first_name, '  ',gu.last_name ) SEPARATOR '|') as gnames,batch.name as bname,course.course_name,course.section_name
  FROM
  students AS std left join users as us on std.user_id=us.id
  join guardians as gu on gu.ward_id=std.id
  left join batches as batch on std.batch_id=batch.id
  left join courses as course on course_id=course.id
  WHERE std.school_id = '#{MultiSchool.current_school.id}'
  and batch_id='#{@batch_id}' and us.is_approved=0 and us.free_user_id!=0
  GROUP BY std.id"

      @students = Student.paginate_by_sql(sql, :page => params[:page], :per_page => 25)
      render(:update) { |page| page.replace_html 'students', :partial => 'applicant_by_course' }
    end
 
    
  end

  def list_students_by_course
    batch_name = ""
    unless params[:student][:batch_name].nil?
      batch_id = params[:student][:batch_name]
      batches_data = Batch.find_by_id(batch_id)
      batch_name = batches_data.name
      
    end
    course_id = 0
    unless params[:course_id].nil?
      course_id = params[:course_id]
    end
    if course_id == 0
      unless params[:student][:section].nil?
        course_id = params[:student][:section]
      end
    end
    
    if course_id.to_i > 0
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
      
      @batch_id = 0
      unless @batch_data.nil?
        @batch_id = @batch_data.id 
      end
    else
      @batch_id = params[:batch_id]
    end
    if MultiSchool.current_school.id == 319
      @students = Student.active.find_all_by_batch_id(@batch_id, :order => 'first_name ASC, middle_name ASC, last_name ASC')
    else
      @students = Student.active.find_all_by_batch_id(@batch_id, :order => 'if(class_roll_no = "" or class_roll_no is null,0,cast(class_roll_no as unsigned)),first_name ASC')
    end
    render(:update) { |page| page.replace_html 'students', :partial => 'students_by_course' }
  end
  
  def list_all_students
    if MultiSchool.current_school.id == 319
      @students = Student.active.find(:all, :order => 'first_name ASC, middle_name ASC, last_name ASC') 
    else
      @students = Student.active.find(:all,:order => 'if(class_roll_no = "" or class_roll_no is null,0,cast(class_roll_no as unsigned)),first_name ASC')
    end        
  end
  
  def profile
    @from = ''
    @course_id = 0
    @batch_id = 0
    unless params[:from].nil?
      @from = params[:from]
      unless params[:course_id].nil?
        @course_id = params[:course_id]
      end
      unless params[:batch_id].nil?
        @batch_id = params[:batch_id]
        @batch = Batch.find @batch_id
      end
    end
    @student = Student.find(params[:id])
    @current_user = current_user
    @address = @student.address_line1.to_s + ' ' + @student.address_line2.to_s
    @sms_module = Configuration.available_modules
    @biometric_id = BiometricInformation.find_by_user_id(@student.user_id).try(:biometric_id)
    @sms_setting = SmsSetting.new
    @previous_data = @student.student_previous_data
    @immediate_contact = @student.immediate_contact
    @assigned_employees = @student.batch.employees
    @additional_fields = StudentAdditionalField.find(:all, :conditions=> "status = true")
    @additional_details = @student.student_additional_details.find(:all,:include => [:student_additional_field],:conditions => ["student_additional_fields.status = true"],:order => "student_additional_fields.priority ASC")
    @additional_fields_count = StudentAdditionalField.count(:conditions => "status = true")    
    @siblings = Student.find(:all,:conditions => ["sibling_id = ? AND id != ?", @student.sibling_id,@student.id])
    @att_text = ''
    @att_image = ''
    get_attendence_text
    unless @attendence_text.nil?
      if @attendence_text['status']['code'].to_i == 200
        @att_text = @attendence_text['data']['text']
        @att_image = @attendence_text['data']['profile_picture']
      end
    end
    
  end
  
  def profile_pdf
    @student = Student.find(params[:id])
    @current_user = current_user
    @address = @student.address_line1.to_s + ' ' + @student.address_line2.to_s
    @sms_module = Configuration.available_modules
    @biometric_id = BiometricInformation.find_by_user_id(@student.user_id).try(:biometric_id)
    @sms_setting = SmsSetting.new
    @previous_data = StudentPreviousData.find_by_student_id(@student.id)
    @immediate_contact = @student.immediate_contact
    @assigned_employees = @student.batch.employees
    @additional_details = @student.student_additional_details.find(:all,:include => [:student_additional_field],:conditions => ["student_additional_fields.status = true"],:order => "student_additional_fields.priority ASC")
    
    @att_text = ''
    @att_image = ''
    @normal_subjects = Subject.find_all_by_batch_id(@student.batch.id,:conditions=>"no_exams = false AND elective_group_id IS NULL AND is_deleted = false")
    @student_electives =StudentsSubject.all(:conditions=>{:student_id=>@student.id,:batch_id=>@student.batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
    @elective_subjects = []
    @student_electives.each do |e|
      @elective_subjects.push Subject.find(e.subject_id)
    end
    @subjects = @normal_subjects+@elective_subjects
    get_attendence_text
    unless @attendence_text.nil?
      if @attendence_text['status']['code'].to_i == 200
        @att_text = @attendence_text['data']['text']
        @att_image = @attendence_text['data']['profile_picture']
      end
    end
    if MultiSchool.current_school.id == 352
    render :pdf => "profile_pdf",
      :page_size => 'Legal',
      :margin => {    :top=> 0,
      :bottom => 0,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    else
      render :pdf => "profile_pdf",
      :margin => {    :top=> 0,
      :bottom => 0,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    end
      
      
    
  end

  def show_previous_details
    @previous_data = StudentPreviousData.find_by_student_id(@student.id)
    @previous_subjects = StudentPreviousSubjectMark.find_all_by_student_id(@student.id)
  end
  
  def show
    @student = Student.find_by_admission_no(params[:id])
    send_data(@student.photo_data,
      :type => @student.photo_content_type,
      :filename => @student.photo_filename,
      :disposition => 'inline')
  end

  def guardians
    @sms_module = Configuration.available_modules
    @parents = @student.student_guardian
    @siblings = Student.find(:all,:conditions => ["sibling_id = ? AND id != ?", @student.sibling_id,@student.id])
  end
  
  def studentformlist
    @batch_id = params[:batch_id]
    @status_id = params[:status_id]
    @form_type = params[:form_type]
    extra_condition = ""
    if current_user.admin?
      extra_condition = ""
    elsif @current_user.employee? and @current_user.employee_record.meeting_forwarder.to_i == 1
      @employee_batches = @current_user.employee_record.batches
      unless @employee_batches.blank?
        batch_ids = @employee_batches.map(&:id) 
        extra_condition+= " and students.batch_id in ("+batch_ids.join(',')+")"
      else
        extra_condition+= " and student_forms.is_delete=true"
      end  
    elsif @current_user.employee?
      extra_condition+= " and student_forms.employee_id="+@current_user.employee_record.id.to_s
    else
      extra_condition+= " and student_forms.is_delete=true"
    end  
    school_id = MultiSchool.current_school.id
    if !@batch_id.blank? and @batch_id!=""
      extra_condition += " and students.batch_id = #{@batch_id}"
    end 
    if !@status_id.blank? and @status_id!=""
      extra_condition += " and student_forms.status = #{@status_id}"
    end
    
    if !@form_type.blank? and @form_type!=""
      extra_condition += " and student_forms.form_type_text = '#{@form_type}'"
    end
    @formData = StudentForm.paginate :conditions=>"student_forms.school_id = #{school_id} and student_forms.is_delete=false"+extra_condition, :order=>"student_forms.created_at desc",:include=>[:student], :page=>params[:page], :per_page => 10
  end
  
  def forward_form
    @formData = StudentForm.find_by_id(params[:aid])
    @formData.update_attributes(:forwarded => 1)
    @formData.update_attributes(:employee_id => params[:emp])
    forwarder_notification(params[:emp])
    flash[:warn_notice]="<p>Form Forwarded.</p>"
    redirect_to :action=>'studentformlist'
  end
  def generate_pdf_letter
    unless params[:st_content].nil?      
      @formData = StudentForm.find_by_id(params[:aid])
      @formData.update_attributes(:form_head => params[:form_head])
      @formData.update_attributes(:generated_form => params[:content_editor])
      
      flash[:warn_notice]="<p>Form updated.</p>"
      redirect_to :action=>'studentformlist'
    else
      school_id = MultiSchool.current_school.id 
      
      @schoolData = MultiSchool.current_school
      @student = Student.find_by_id(params[:id])
      @formData = StudentForm.find_by_id(params[:aid])
      @defaultFromData = StudentFormDefault.find_by_form_type_text_and_school_id(@formData.form_type_text, school_id)
      
    end
    
  end
  
  def view_pdf_letter
    @student = Student.find(params[:id])
    @formData = StudentForm.find_by_id(params[:aid])    
    @schoolData = MultiSchool.current_school
    
    render :pdf=>'view_pdf_letter',
      :margin => {
      :top=> 40,
      :bottom => 20,
      :left=> 20,
      :right => 20 
    },            
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def form_to_apply    
    @transferFormData             = StudentForm.find_all_by_student_id_and_form_type_text_and_is_delete(params[:id],"transfer_letter", 0)
    @nocFormData                  = StudentForm.find_all_by_student_id_and_form_type_text_and_is_delete(params[:id],"noc_letter", 0)
    @recommendationFormData       = StudentForm.find_all_by_student_id_and_form_type_text_and_is_delete(params[:id],"recommendation_letter", 0)
    @studentshipFormData          = StudentForm.find_all_by_student_id_and_form_type_text_and_is_delete(params[:id],"studentship_letter", 0)
    @visa_recommendationFormData  = StudentForm.find_all_by_student_id_and_form_type_text_and_is_delete(params[:id],"visa_recommendation_letter", 0)
  end
 
  def transfer_letter
    @student = Student.find(params[:id])
    respond_to do |format|
      format.js { render :action => 'transfer_letter' }
    end    
  end
  def transfer_letter_update
    @studentForm = StudentForm.new
    
    @studentForm.student_id =  params[:id]
    @studentForm.form_type_text =  "transfer_letter"
    @studentForm.form_data =  params[:letter_data]
    @studentForm.status =  1    
    @studentForm.school_id = MultiSchool.current_school.id    
    
    if @studentForm.save
      @error = true
      send_notification_section_manager
    end
    render :update do |page|
      if @studentForm.save
        
        page.replace_html 'form-errors', :text => ''
        page << "Modalbox.hide();"
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg20')}</p>"

      else
        page.replace_html 'form-errors', :partial => 'transfer_letter', :object => @student
        page.visual_effect(:highlight, 'form-errors')
      end
    end
  end
  
  def noc_letter
    @student = Student.find(params[:id])
    respond_to do |format|
      format.js { render :action => 'noc_letter' }
    end    
  end
  def noc_letter_update
    @studentForm = StudentForm.new
    
    @studentForm.student_id =  params[:id]
    @studentForm.form_type_text =  "noc_letter"
    @studentForm.form_data =  params[:letter_data]
    @studentForm.status =  1 
    @studentForm.school_id = MultiSchool.current_school.id  
    
    if @studentForm.save
      @error = true
      send_notification_section_manager
    end
    render :update do |page|
      if @studentForm.save
        
        page.replace_html 'form-errors', :text => ''
        page << "Modalbox.hide();"
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg20')}</p>"

      else
        page.replace_html 'form-errors', :partial => 'noc_letter', :object => @student
        page.visual_effect(:highlight, 'form-errors')
      end
    end
  end
  
  def recommendation_letter
    @student = Student.find(params[:id])
    respond_to do |format|
      format.js { render :action => 'recommendation_letter' }
    end    
  end
  def recommendation_letter_update
    @studentForm = StudentForm.new
    
    @studentForm.student_id =  params[:id]
    @studentForm.form_type_text =  "recommendation_letter"
    @studentForm.form_data =  params[:letter_data]
    @studentForm.status =  1 
    @studentForm.school_id = MultiSchool.current_school.id  
    
    if @studentForm.save
      @error = true
      send_notification_section_manager
    end
    render :update do |page|
      if @studentForm.save
        
        page.replace_html 'form-errors', :text => ''
        page << "Modalbox.hide();"
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg20')}</p>"

      else
        page.replace_html 'form-errors', :partial => 'recommendation_letter', :object => @student
        page.visual_effect(:highlight, 'form-errors')
      end
    end
  end
  
  def studentship_letter
    @student = Student.find(params[:id])
    respond_to do |format|
      format.js { render :action => 'studentship_letter' }
    end 
  end
  
  def studentship_letter_update
    @studentForm = StudentForm.new
    
    @studentForm.student_id =  params[:id]
    @studentForm.form_type_text =  "studentship_letter"
    @studentForm.form_data =  params[:letter_data]
    @studentForm.status =  1    
    @studentForm.school_id = MultiSchool.current_school.id  
    
    if @studentForm.save
      @error = true
      send_notification_section_manager
    end
    render :update do |page|
      if @studentForm.save
        page.replace_html 'form-errors', :text => ''
        page << "Modalbox.hide();"
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg20')}</p>"

      else
        page.replace_html 'form-errors', :partial => 'studentship_letter', :object => @student
        page.visual_effect(:highlight, 'form-errors')
      end
    end
  end
  
  def visa_recommendation_letter
    @student = Student.find(params[:id])
    respond_to do |format|
      format.js { render :action => 'visa_recommendation_letter' }
    end 
  end
  
  def visa_recommendation_letter_update
    @studentForm = StudentForm.new
    
    @studentForm.student_id =  params[:id]
    @studentForm.form_type_text =  "visa_recommendation_letter"
    @studentForm.form_data =  params[:letter_data]
    @studentForm.status =  1    
    @studentForm.school_id = MultiSchool.current_school.id  
    
    if @studentForm.save
      @error = true
      send_notification_section_manager
    end
    render :update do |page|
      if @studentForm.save
        page.replace_html 'form-errors', :text => ''
        page << "Modalbox.hide();"
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg20')}</p>"

      else
        page.replace_html 'form-errors', :partial => 'visa_recommendation_letter', :object => @student
        page.visual_effect(:highlight, 'form-errors')
      end
    end
  end
  
  def close_letter
    @formData = StudentForm.find_by_id_and_student_id(params[:aid],current_user.guardian_entry.current_ward_id )  
    unless @formData.blank?
      @formData.update_attributes(:is_delete => 1)
    end
    flash[:warn_notice]="<p>Request is closed.</p>"
    redirect_to :action=>'form_to_apply', :id => params[:id]
  end
  
  def update_letter_status
    @formData = StudentForm.find_by_id(params[:aid])        
    @formData.update_attributes(:status => params[:status])
    
    send_guardian_notification
    send_notification_section_manager_update
    
    flash[:warn_notice]="<p>Request is Updated.</p>"
    redirect_to :action=>'studentformlist', :id => params[:id], :page => params[:page], :status_id => params[:status_id], :batch_id => params[:batch_id], :form_type => params[:form_type]
  end
  def del_only_this_guardian
    guardianstudent = GuardianStudents.find_by_guardian_id_and_student_id(params[:id],params[:student_id])
    if guardianstudent.destroy
      flash[:notice] = "#{t('flash6')}"
      redirect_to :controller => 'student', :action => 'guardians', :id => params[:student_id]
    else
      flash[:notice] = "Cant't Delete guardian. Please try agin or contact with administration"
      redirect_to :controller => 'student', :action => 'profile', :id => params[:student_id]
    end  
  end
  def del_guardian
    @guardian = Guardian.find(params[:id])
    @student = Student.find(params[:student_id])
    total_std = 0
    unless @guardian.guardian_student.empty?
      stds = @guardian.guardian_student
      total_std = stds.count
    end  
  

    unless @student.all_siblings.collect(&:immediate_contact_id).include? params[:id].to_i or total_std>1
      if @guardian.id==@student.immediate_contact_id
        if @guardian.destroy
          @guardian.user.destroy
          flash[:notice] = "#{t('flash6')}"
          redirect_to :controller => 'student', :action => 'admission3_1', :id => params[:student_id]
        end
      else
        if @guardian.destroy
          @guardian.user.destroy
          flash[:notice] = "#{t('flash6')}"
          redirect_to :controller => 'student', :action => 'profile', :id => params[:student_id]
        end
      end
    else
      if @guardian.destroy
        @guardian.user.destroy
      end
      redirect_to :controller => 'student', :action => 'profile', :id => params[:student_id]
    end
  end

  def academic_pdf
    @course = @student.old_courses.find_by_academic_year_id(params[:year]) if params[:year]
    @course ||= @student.course
    @subjects = Subject.find_all_by_course_id(@course, :conditions => "no_exams = false")
    @examtypes = ExaminationType.find( ( @course.examinations.collect { |x| x.examination_type_id } ).uniq )

    @arr_total_wt = {}
    @arr_score_wt = {}

    @subjects.each do |s|
      @arr_total_wt[s.name] = 0
      @arr_score_wt[s.name] = 0
    end

    @course.examinations.each do |x|
      @arr_total_wt[x.subject.name] += x.weightage
      ex_score = ExaminationResult.find_by_examination_id_and_student_id(x.id, @student.id)
      @arr_score_wt[x.subject.name] += ex_score.marks * x.weightage / x.max_marks unless ex_score.nil?
    end

    respond_to do |format|
      format.pdf { render :layout => false }
    end
  end

  def categories
    @student_categories = StudentCategory.active
    @student_category = StudentCategory.new(params[:student_category])
    if request.post?
      params[:student_category].each_value(&:strip!)
      if @student_category.save
        flash[:notice] = "#{t('flash7')}"
        redirect_to :action => 'categories'
      end
    end
  end

  def category_delete
    @student_category = StudentCategory.update(params[:id], :is_deleted=>true)
    @student_categories = StudentCategory.active
  end

  def category_edit
    @student_category = StudentCategory.find(params[:id])
    @student_category_name=@student_category.name
  end

  def category_update
    @student_category = StudentCategory.find(params[:id])
    @student_category_name=@student_category.name
    if @student_category.update_attributes(:name => params[:name])
      @student_categories = StudentCategory.active
      @student_category = StudentCategory.new
    end
  end
  def class_section
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?) and is_deleted = ?", batches, false], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    
    @batches = Batch.active
  end
  def view_all
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?) and is_deleted = ?", batches, false], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    
    @batches = Batch.active
  end

  def category_student
    @students = []
    # @dates=FinanceFeeCollection.find(:all,:joins=>"INNER JOIN fee_collection_batches on fee_collection_batches.finance_fee_collection_id=finance_fee_collections.id INNER JOIN finance_fees on finance_fees.fee_collection_id=finance_fee_collections.id",:conditions=>"finance_fees.student_id='#{@student.id}'  and finance_fee_collections.is_deleted=#{false} and ((finance_fees.balance > 0 and finance_fees.batch_id<>#{@student.batch_id}) or (finance_fees.batch_id=#{@student.batch_id}) )").uniq
    @students = Student.find(:all,:select=>'COUNT(*) as number_of_students, student_categories.name as category_name', :joins=>'INNER JOIN student_categories on student_categories.id = students.student_category_id',:group=>'student_categories.id')
  end
  
  def web_register
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    
    if @current_user.employee?
      employee= @current_user.employee_record
      @employee_obj = Employee.find_by_id(employee.id)
      @employee_batches = @employee_obj.batches
      if @employee_batches.empty?
        flash[:notice]="#{t('only_class_teacher_can_access_this_page')}"
        redirect_to :controller => "user", :action => "dashboard"
      else
        @batch_id = @employee_batches[0].batch_id
      end  
    end
    
    @batches = Batch.active
  end

  def advanced_search
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    @search = Student.search(params[:search])
    @batch_id = 0
    
    
    unless params[:search]
      @batches = [] #Batch.all
    else
      school_id = MultiSchool.current_school.id
      @classes = Rails.cache.fetch("section_data_#{params[:advv_search][:class_name].parameterize("_")}_#{school_id}"){
        class_data = Course.find(:all, :conditions => ["course_name LIKE ?",params[:advv_search][:class_name]])
        class_data
      }
      @selected_section = 0
      
      if params[:search]
        @students = Array.new
        unless params[:advv_search][:course_id].empty?
          if params[:search][:batch_id_equals].empty?
            params[:search][:batch_id_in] = Batch.find_all_by_course_id(params[:advv_search][:course_id],:select => "id")
          end
        end
        
        unless params[:advv_search][:section].nil? or params[:advv_search][:section].empty?
          if params[:advv_search][:batch_name].empty?
            params[:search][:batch_id_in] = Batch.find_all_by_course_id(params[:advv_search][:section],:select => "id")
          else
            params[:search][:batch_id_in] = Batch.find(:all, :conditions => ["course_id IN (?) and name LIKE ?", params[:advv_search][:section], params[:advv_search][:batch_name]],:select => "id")
          end
        else
          unless params[:advv_search][:class_name].empty?
            class_data = Course.find(:all, :conditions => ["course_name LIKE ?",params[:advv_search][:class_name]])
            class_data_id = class_data.map{|c| c.id}
            if params[:advv_search][:batch_name].empty?
              params[:search][:batch_id_in] = Batch.find(:all, :conditions => ["course_id IN (?)", class_data_id],:select => "id")
            else
              batches_data = Batch.find params[:advv_search][:batch_name]
              batch_name = batches_data.name
              params[:search][:batch_id_in] = Batch.find(:all, :conditions => ["course_id IN (?) and name LIKE ?", class_data_id, batch_name],:select => "id")
            end
          else
            unless params[:advv_search][:batch_name].empty?
              batches_data = Batch.find params[:advv_search][:batch_name]
              batch_name = batches_data.name
              params[:search][:batch_id_in] = Batch.find(:all, :conditions => ["name LIKE ?", batch_name],:select => "id")
            end
          end
        end
        
        unless params[:search][:batch_id_in].nil? or params[:search][:batch_id_in].empty?
          params[:search][:batch_id_in] = params[:search][:batch_id_in].map{|b| b.id}
        end
        
        if params[:search][:is_active_equals]=="true"
          @students = Student.ascend_by_first_name.search(params[:search]).paginate(:page => params[:page],:per_page => 30)
        elsif params[:search][:is_active_equals]=="false"
          @students = ArchivedStudent.ascend_by_first_name.search(params[:search]).paginate(:page => params[:page],:per_page => 30)
        else
          #          @search1 = Student.search(params[:search]).all
          #          @search2 = ArchivedStudent.search(params[:search]).all
          #          @students=@search1+@search2
          @students = [{:student => {:search_options => params[:search], :order => :first_name}},{:archived_student => {:search_options => params[:search], :order => :first_name}}].model_paginate(:page => params[:page],:per_page => 30)#.sort!{|m, n| m.first_name.capitalize <=> n.first_name.capitalize}

        end
        
        unless params[:advv_search][:batch_name].empty?
          if batch_name.nil?
            batches_data = Batch.find params[:advv_search][:batch_name]
            batch_name = batches_data.name
          end
        end
        
        #abort(params.inspect)

        #        unless params[:advv_search][:course_id].empty?
        #          if params[:search][:batch_id_equals].empty?
        #            batches = Batch.find_all_by_course_id(params[:advv_search][:course_id]).collect{|b|b.id}
        #          end
        #        end
        #        if batches.is_a?(Array)
        #
        #          @students = []
        #          batches.each do |b|
        #            params[:search][:batch_id_equals] = b
        #            if params[:search][:is_active_equals]=="true"
        #              @search = Student.search(params[:search])
        #              @students+=@search.all
        #            elsif params[:search][:is_active_equals]=="false"
        #              @search = ArchivedStudent.search(params[:search])
        #              @students+=@search.all
        #            else
        #              @search1 = Student.search(params[:search]).all
        #              @search2 = ArchivedStudent.search(params[:search]).all
        #              @students+=@search1+@search2
        #            end
        #          end
        #          params[:search][:batch_id_equals] = nil
        #        else
        #          if params[:search][:is_active_equals]=="true"
        #            @search = Student.search(params[:search])
        #            @students = @search.all
        #          elsif params[:search][:is_active_equals]=="false"
        #            @search = ArchivedStudent.search(params[:search])
        #            @students = @search.all
        #          else
        #            @search1 = Student.search(params[:search]).all
        #            @search2 = ArchivedStudent.search(params[:search]).all
        #            @students = @search1+@search2
        #          end
        #        end
        @searched_for = ''
        @searched_for += "<span>#{t('name')}: </span>" + params[:search][:first_name_or_middle_name_or_last_name_like].to_s unless params[:search][:first_name_or_middle_name_or_last_name_like].empty?
        @searched_for += " <span>#{t('admission_no')}: </span>" + params[:search][:admission_no_equals].to_s unless params[:search][:admission_no_equals].empty?
        
        unless params[:advv_search][:section].nil? or params[:advv_search][:section].empty?
          if params[:advv_search][:batch_name].empty?
            course_name = params[:advv_search][:class_name]
            sections = Course.find_by_id(params[:advv_search][:section])
            @searched_for += "<span>#{t('class_label')}: </span>" + course_name
            @searched_for += "<span>#{t('section_label')}: </span>" + sections.section_name
          else
            course_name = params[:advv_search][:class_name]
            sections = Course.find_by_id(params[:advv_search][:section])
            @searched_for += "<span>#{t('shift_label')}: </span>" + batch_name
            @searched_for += "<span>#{t('class_label')}: </span>" + course_name
            @searched_for += "<span>#{t('section_label')}: </span>" + sections.section_name
          end
        else
          unless params[:advv_search][:class_name].empty?
            if params[:advv_search][:batch_name].empty?
              course_name = params[:advv_search][:class_name]
              @searched_for += "<span>#{t('class_label')}: </span>" + course_name
            else
              course_name = params[:advv_search][:class_name]
              @searched_for += "<span>#{t('shift_label')}: </span>" + batch_name
              @searched_for += "<span>#{t('class_label')}: </span>" + course_name
            end
          else
            unless params[:advv_search][:batch_name].empty?
              @searched_for += "<span>#{t('shift_label')}: </span>" + batch_name
            end
          end
        end
        
        unless params[:advv_search][:course_id].empty?
          course = Course.find(params[:advv_search][:course_id])
          batch = Batch.find(params[:search][:batch_id_equals]) unless (params[:search][:batch_id_equals]).blank?
          @searched_for += "<span>#{t('course_text')}: </span>" + course.full_name
          @searched_for += "<span>#{t('batch')}: </span>" + batch.full_name unless batch.nil?
        end
        @searched_for += "<span>#{t('category')}: </span>" + StudentCategory.find(params[:search][:student_category_id_equals]).name.to_s unless params[:search][:student_category_id_equals].empty?
        unless  params[:search][:gender_equals].empty?
          if  params[:search][:gender_equals] == 'm'
            @searched_for += "<span>#{t('gender')}: </span>#{t('male')}"
          elsif  params[:search][:gender_equals] == 'f'
            @searched_for += " <span>#{t('gender')}: </span>#{t('female')}"
          else
            @searched_for += " <span>#{t('gender')}: </span>#{t('all')}"
          end
        end
        @searched_for += "<span>#{t('blood_group')}: </span>" + params[:search][:blood_group_like].to_s unless params[:search][:blood_group_like].empty?
        @searched_for += "<span>#{t('nationality')}: </span>" + Country.find(params[:search][:nationality_id_equals]).name.to_s unless params[:search][:nationality_id_equals].empty?
        @searched_for += "<span>#{t('year_of_admission')}: </span>" +  params[:advv_search][:doa_option].to_s + ' '+ params[:adv_search][:admission_date_year].to_s unless  params[:advv_search][:doa_option].empty?
        @searched_for += "<span>#{t('year_of_birth')}: </span>" +  params[:advv_search][:dob_option].to_s + ' ' + params[:adv_search][:birth_date_year].to_s unless  params[:advv_search][:dob_option].empty?
        if params[:search][:is_active_equals]=="true"
          @searched_for += "<span>#{t('present_student')}</span>"
        elsif params[:search][:is_active_equals]=="false"
          @searched_for += "<span>#{t('former_student')}</span>"
        else
          @searched_for += "<span>#{t('all_students')}</span>"
        end
      end
    end
  end

   

  #  def adv_search
  #    @batches = []
  #    @search = Student.search(params[:search])
  #    if params[:search]
  #      if params[:search][:is_active_equals]=="true"
  #        @search = Student.search(params[:search])
  #        @students = @search.all
  #      elsif params[:search][:is_active_equals]=="false"
  #        @search = ArchivedStudent.search(params[:search])
  #        @students = @search.all
  #      else
  #        @search = Student.search(params[:search])
  #        @students = @search.all
  #      end
  #    end
  #  end

  def list_doa_year
    doa_option = params[:doa_option]
    if doa_option == "Equal to"
      render :update do |page|
        page.replace_html 'doa_year', :partial=>"equal_to_select"
      end
    elsif doa_option == "Less than"
      render :update do |page|
        page.replace_html 'doa_year', :partial=>"less_than_select"
      end
    else
      render :update do |page|
        page.replace_html 'doa_year', :partial=>"greater_than_select"
      end
    end
  end

  def doa_equal_to_update
    year = params[:year]
    @start_date = "#{year}-01-01".to_date
    @end_date = "#{year}-12-31".to_date
    render :update do |page|
      page.replace_html 'doa_year_hidden', :partial=>"equal_to_doa_select"
    end
  end

  def doa_less_than_update
    year = params[:year]
    @start_date = "1900-01-01".to_date
    @end_date = "#{year}-01-01".to_date
    render :update do |page|
      page.replace_html 'doa_year_hidden', :partial=>"less_than_doa_select"
    end
  end

  def doa_greater_than_update
    year = params[:year]
    @start_date = "2100-01-01".to_date
    @end_date = "#{year}-12-31".to_date
    render :update do |page|
      page.replace_html 'doa_year_hidden', :partial=>"greater_than_doa_select"
    end
  end

  def list_dob_year
    dob_option = params[:dob_option]
    if dob_option == "Equal to"
      render :update do |page|
        page.replace_html 'dob_year', :partial=>"equal_to_select_dob"
      end
    elsif dob_option == "Less than"
      render :update do |page|
        page.replace_html 'dob_year', :partial=>"less_than_select_dob"
      end
    else
      render :update do |page|
        page.replace_html 'dob_year', :partial=>"greater_than_select_dob"
      end
    end
  end

  def dob_equal_to_update
    year = params[:year]
    @start_date = "#{year}-01-01".to_date
    @end_date = "#{year}-12-31".to_date
    render :update do |page|
      page.replace_html 'dob_year_hidden', :partial=>"equal_to_dob_select"
    end
  end

  def dob_less_than_update
    year = params[:year]
    @start_date = "1900-01-01".to_date
    @end_date = "#{year}-01-01".to_date
    render :update do |page|
      page.replace_html 'dob_year_hidden', :partial=>"less_than_dob_select"
    end
  end

  def dob_greater_than_update
    year = params[:year]
    @start_date = "2100-01-01".to_date
    @end_date = "#{year}-12-31".to_date
    render :update do |page|
      page.replace_html 'dob_year_hidden', :partial=>"greater_than_dob_select"
    end
  end

  def list_batches
    unless params[:course_id] == ''
      @batches = Batch.find(:all, :conditions=>"course_id = #{params[:course_id]}",:order=>"id DESC")
    else
      @batches = []
    end
    render(:update) do |page|
      page.replace_html 'course_batches', :partial=> 'list_batches'
    end
  end

  def advanced_search_pdf
    @searched_for = ''
    @searched_for += "<span>#{t('name')}: </span>" + params[:search][:first_name_or_middle_name_or_last_name_like].to_s + ', 'unless params[:search][:first_name_or_middle_name_or_last_name_like].empty?
    @searched_for += "<span>#{t('admission_no')}: </span>" + params[:search][:admission_no_equals].to_s + ', ' unless params[:search][:admission_no_equals].empty?
    unless params[:advv_search][:course_id].empty?
      course = Course.find(params[:advv_search][:course_id])
      batch = Batch.find(params[:search][:batch_id_equals]) unless (params[:search][:batch_id_equals]).blank?
      @searched_for += "<span>#{t('course_text')}: </span>" + course.full_name + ', '
      @searched_for += "<span>#{t('batch')}: </span>" + batch.full_name + ', ' unless batch.nil?
    end
    @searched_for += "<span>#{t('category')}: </span>" + StudentCategory.find(params[:search][:student_category_id_equals]).name.to_s + ', 'unless params[:search][:student_category_id_equals].empty?
    unless  params[:search][:gender_equals].empty?
      if  params[:search][:gender_equals] == 'm'
        @searched_for += "<span>#{t('gender')}: </span>#{t('male')} " + ', '
      elsif  params[:search][:gender_equals] == 'f'
        @searched_for += "<span>#{t('gender')}: </span>#{t('female')} " + ', '
      else
        @searched_for += "<span>#{t('gender')}: </span>#{t('all')} " + ', '
      end
    end
    @searched_for += "<span>#{t('blood_group')}: </span>" + params[:search][:blood_group_like].to_s + ', ' unless params[:search][:blood_group_like].empty?
    @searched_for += "<span>#{t('nationality')}: </span>" + Country.find(params[:search][:nationality_id_equals]).name.to_s + ', ' unless params[:search][:nationality_id_equals].empty?
    @searched_for += "<span>#{t('year_of_admission')}: </span>" +  params[:advv_search][:doa_option].to_s + ' '+ params[:adv_search][:admission_date_year].to_s  + ', ' unless  params[:advv_search][:doa_option].empty?
    @searched_for += "<span>#{t('year_of_birth')}: </span>" +  params[:advv_search][:dob_option].to_s + ' ' + params[:adv_search][:birth_date_year].to_s + ', ' unless  params[:advv_search][:dob_option].empty?
    if params[:search][:is_active_equals]=="true"
      @searched_for += "<span>#{t('present_student')} </span>"
    elsif params[:search][:is_active_equals]=="false"
      @searched_for += "<span>#{t('former_student')} </span>"
    else
      @searched_for += "<span>#{t('all_students')} </span>"
    end

    unless params[:advv_search][:course_id].empty?
      if params[:search][:batch_id_equals].empty?
        batches = Batch.find_all_by_course_id(params[:advv_search][:course_id]).collect{|b|b.id}
      end
    end
    if batches.is_a?(Array)

      @students = []
      batches.each do |b|
        params[:search][:batch_id_equals] = b
        if params[:search][:is_active_equals]=="true"
          @search = Student.ascend_by_first_name.search(params[:search])
          @students+=@search.all
        elsif params[:search][:is_active_equals]=="false"
          @search = ArchivedStudent.ascend_by_first_name.search(params[:search])
          @students+=@search.all
        else
          @search1 = Student.ascend_by_first_name.search(params[:search]).all
          @search2 = ArchivedStudent.ascend_by_first_name.search(params[:search]).all
          @students+=@search1+@search2
        end
      end
      params[:search][:batch_id_equals] = nil
    else
      if params[:search][:is_active_equals]=="true"
        @search = Student.ascend_by_first_name.search(params[:search])
        @students = @search.all
      elsif params[:search][:is_active_equals]=="false"
        @search = ArchivedStudent.ascend_by_first_name.search(params[:search])
        @students = @search.all
      else
        @search1 = Student.ascend_by_first_name.search(params[:search]).all
        @search2 = ArchivedStudent.ascend_by_first_name.search(params[:search]).all
        @students = @search1+@search2.sort! {|x,y| x.first_name <=> y.first_name}
      end
    end
    render :pdf=>'generate_tc_pdf'
         
  end

  #  def new_adv
  #    if params[:adv][:option] == "present"
  #      @search = Student.search(params[:search])
  #      @students = @search.all
  #    end
  #  end
  
  
  def electives
    flash[:notice] = nil
    @show_batch_subject = true
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 0
        @show_batch_subject = false
      end
    end
    @from_action = "shift"
    @s_message_for_subject = ""
    @s_message_for_elective = ""
    unless params[:from_action].nil?
      @from_action = params[:from_action]
    end
    
    if @show_batch_subject
      @batch = Batch.find(params[:id])
      @elective_subject = Subject.find(params[:id2])
      @students = @batch.students.all(:order=>"first_name ASC")
      @elective_group = ElectiveGroup.find(@elective_subject.elective_group_id)
    else
      @batch_only = false
      unless params[:batch_only].nil?
        if params[:batch_only].to_i == 1
          @batch_only = true
        end
      end

      @batch_name = ""
      unless params[:batch_name].nil?
        @batch_name = params[:batch_name]
      end

      @batch_name = @batch_only ? @batch_name : nil
      @batch = Batch.find(params[:id])
      
      @course = @batch.course
      @elective_subject = Subject.find(params[:id2])
      
      @batches = @course.find_batches_data(@batch_name, @course.course_name)
      
      @students = []
      @student = []
      @batch_data = {}
      
      @subjects_code = @elective_subject.code
      @elective_group = ElectiveGroup.find(@elective_subject.elective_group_id)
      
      @elective_group_name = @elective_group.name
      @elective_active_batch_ids = ElectiveGroup.find(:all, :conditions => ["name like ? and batch_id IN (?) and is_deleted = 0", @elective_group_name, @batches]).map{|b| b.batch_id}
      
      half_no_of_batches_active = ( @batches.length / 2 ).floor
      active_batches_id = @batches - @elective_active_batch_ids
      
      @s_message_for_subject = @elective_subject.other_batches(@elective_active_batch_ids, false)
      if active_batches_id.length > 0
        @s_message_for_elective = @elective_group.get_message(@elective_active_batch_ids, active_batches_id, half_no_of_batches_active )
      end
      
      
      @elective_active_batch_ids.each do |b|
        elective_group_id = @elective_subject.get_appropriate_group_id(b)
        
        @tmp_batch = Batch.find(b)
        @tmp_course = @tmp_batch.course
        if @batch_data[@tmp_batch.name].nil?
          @batch_data[@tmp_batch.name] = []
        end
        
        if elective_group_id > 0
          @subject_tmp = Subject.find(:all, :conditions => ["batch_id = ? and code = ? and elective_group_id = ?", b, @subjects_code, elective_group_id])
          
          unless @subject_tmp.nil? or @subject_tmp.empty?
            @batch_data[@tmp_batch.name] << {"course_name" => @tmp_course.course_name, "section" => @tmp_course.section_name, "students" => @tmp_batch.students.all(:order=>"first_name ASC")}
          else
            @batch_data[@tmp_batch.name] << {"course_name" => @tmp_course.course_name, "section" => @tmp_course.section_name, "students" => nil, "message" => "<b style='color: #f00;'>This Elective Group Subject is not assigned to this Section</b>"}
          end
        else
          @batch_data[@tmp_batch.name] << {"course_name" => @tmp_course.course_name, "section" => @tmp_course.section_name, "students" => nil, "message" => "<b style='color: #f00;'>Elective Group is not assigned to this Section</b>"}
        end
      end
      @students = @batch_data
      @elective_group = ElectiveGroup.find(@elective_subject.elective_group_id)
      
    end
    flash[:notice] = nil
  end

  def assigned_student_list
    @batch = Batch.find(params[:id])
    @elective_subject = Subject.find(params[:id2])
    
    @courses = Course.find_all_by_course_name(@batch.course.course_name) 
    course_ids = @courses.map(&:id)
    @batches = Batch.find_all_by_course_id(course_ids)
    batch_ids = @batches.map(&:id)
    @subjects = Subject.find_all_by_batch_id_and_code(batch_ids,@elective_subject.code)
    subject_ids = @subjects.map(&:id)
    @subject_students = StudentsSubject.find_all_by_subject_id(subject_ids,:include=>[{:student=>{:batch=>[:course]}}])
    render :pdf => 'assigned_student_list',
      :orientation => 'portrait', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def assign_students
    @student = Student.find(params[:id])
    
    appropriate_elective_subject_id = 0
    
    b = @student.batch_id
    elective_type = 0
    unless params[:elective_type].blank?
      elective_type = params[:elective_type]
    end
    
    @subject = Subject.find params[:id2]
    
    @subjects_code = @subject.code
    
    @tmp_subject_to_test = Subject.active.find_by_code_and_batch_id(@subjects_code, b)
    unless @tmp_subject_to_test.nil?
      appropriate_elective_subject_id = @tmp_subject_to_test.id
    end
    
    if appropriate_elective_subject_id > 0
      @assigned = StudentsSubject.find_by_student_id_and_subject_id(@student.id,appropriate_elective_subject_id)
      StudentsSubject.create(:student_id=>params[:id],:subject_id=>appropriate_elective_subject_id,:elective_type=>elective_type,:batch_id=>b) if @assigned.nil?
    end
    
    @student = Student.find(params[:id])
    @elective_subject = Subject.find(params[:id2])
    render(:update) do |page|
      page.replace_html "stud_#{params[:id]}", :partial=> 'unassign_students'
      page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg39')}</p>"
    end
  end

  def assign_all_students
    @assign_all = true
    @remove_all = false
    @show_batch_subject = true
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 0
        @show_batch_subject = false
      end
    end
    @from_action = "shift"
    unless params[:from_action].nil?
      @from_action = params[:from_action]
    end
    elective_type = 0
    unless params[:elective_type].blank?
      elective_type = params[:elective_type]
    end
    
    if @show_batch_subject
      @batch = Batch.find(params[:id])
      @students = @batch.students.all(:order=>"first_name ASC")
      @students.each do |s|
        @assigned = StudentsSubject.find_by_student_id_and_subject_id(s.id,params[:id2])
        StudentsSubject.create(:student_id=>s.id,:subject_id=>params[:id2],:elective_type=>elective_type,:batch_id=>@batch.id) if @assigned.nil?
      end
      @elective_subject = Subject.find(params[:id2])
    else
      @batch = Batch.find(params[:id])
      
      @course = @batch.course
      @elective_subject = Subject.find(params[:id2])
      
      @batch_only = false
      unless params[:batch_only].nil?
        if params[:batch_only].to_i == 1
          @batch_only = true
        end
      end

      @batch_name = ""
      unless params[:batch_name].nil?
        @batch_name = params[:batch_name]
      end

      @batch_name = @batch_only ? @batch_name : nil
      @batches = @course.find_batches_data(@batch_name, @course.course_name)
      
      @students = []
      @student = []
      @batch_data = {}
      
      @subjects_code = @elective_subject.code
      @batches.each do |b|
        appropriate_elective_subject_id = 0
        @tmp_batch = Batch.find(b)
        @tmp_course = @tmp_batch.course
        @tmp_students = @tmp_batch.students.all(:order=>"first_name ASC")
        if @batch_data[@tmp_batch.name].nil?
          @batch_data[@tmp_batch.name] = []
        end
        
        @batch_data[@tmp_batch.name] << {"course_name" => @tmp_course.course_name, "section" => @tmp_course.section_name, "students" => @tmp_students}
        @tmp_students.each do |s|
          if appropriate_elective_subject_id == 0
            @tmp_subject_to_test = Subject.active.find_by_code_and_batch_id(@subjects_code, b)
            unless @tmp_subject_to_test.nil?
              appropriate_elective_subject_id = @tmp_subject_to_test.id
            end
          end
          if appropriate_elective_subject_id > 0
            @assigned = StudentsSubject.find_by_student_id_and_subject_id(s.id,appropriate_elective_subject_id)
            StudentsSubject.create(:student_id=>s.id,:subject_id=>appropriate_elective_subject_id,:elective_type=>elective_type,:batch_id=>@tmp_batch.id) if @assigned.nil?
          end
        end
      end
      @students = @batch_data
      @elective_group = ElectiveGroup.find(@elective_subject.elective_group_id)
    end
    render(:update) do |page|
      page.replace_html 'category-list', :partial=>"all_assign"
      page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg40')}</p>"
    end
  end

  def unassign_students
    @student = Student.find(params[:id])
    
    appropriate_elective_subject_id = 0
    
    b = @student.batch_id
    
    
    @subject = Subject.find params[:id2]
    
    @subjects_code = @subject.code
    
    @tmp_subject_to_test = Subject.active.find_by_code_and_batch_id(@subjects_code, b,:conditions=>"subjects.elective_group_id IS NOT NULL")
    unless @tmp_subject_to_test.nil?
      appropriate_elective_subject_id = @tmp_subject_to_test.id
    end
    
    if appropriate_elective_subject_id > 0
      StudentsSubject.find_by_student_id_and_subject_id(@student.id,appropriate_elective_subject_id).delete
    end
    
    @student = Student.find(params[:id])
    @elective_subject = Subject.find(params[:id2])
    render(:update) do |page|
      page.replace_html "stud_#{params[:id]}", :partial=> 'assign_students'
      page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg41')}</p>"
    end
  end

  def unassign_all_students
    @remove_all = true
    @assign_all = false
    @show_batch_subject = true
    unless params[:show_batch_subject].nil?
      if params[:show_batch_subject].to_i == 0
        @show_batch_subject = false
      end
    end
    @from_action = "shift"
    unless params[:from_action].nil?
      @from_action = params[:from_action]
    end
    
    if @show_batch_subject
      @batch = Batch.find(params[:id])
      @students = @batch.students.all(:order=>"first_name ASC")
      @students.each do |s|
        @assigned = StudentsSubject.find_by_student_id_and_subject_id(s.id,params[:id2])
        @assigned.delete unless @assigned.nil?
      end
      @elective_subject = Subject.find(params[:id2])
    else
      @batch = Batch.find(params[:id])
      
      @course = @batch.course
      @elective_subject = Subject.find(params[:id2])
      
      @batch_only = false
      unless params[:batch_only].nil?
        if params[:batch_only].to_i == 1
          @batch_only = true
        end
      end

      @batch_name = ""
      unless params[:batch_name].nil?
        @batch_name = params[:batch_name]
      end

      @batch_name = @batch_only ? @batch_name : nil
      @batches = @course.find_batches_data(@batch_name, @course.course_name)
      
      @students = []
      @student = []
      @batch_data = {}
      
      @subjects_code = @elective_subject.code
      
      @batches.each do |b|
        appropriate_elective_subject_id = 0
        @tmp_batch = Batch.find(b)
        @tmp_course = @tmp_batch.course
        @tmp_students = @tmp_batch.students.all(:order=>"first_name ASC")
        if @batch_data[@tmp_batch.name].nil?
          @batch_data[@tmp_batch.name] = []
        end
        
        @batch_data[@tmp_batch.name] << {"course_name" => @tmp_course.course_name, "section" => @tmp_course.section_name, "students" => @tmp_students}
        @tmp_students.each do |s|
          if appropriate_elective_subject_id == 0
            @tmp_subject_to_test = Subject.active.find_by_code_and_batch_id(@subjects_code, b)
            unless @tmp_subject_to_test.nil?
              appropriate_elective_subject_id = @tmp_subject_to_test.id
            end
          end
          if appropriate_elective_subject_id > 0
            @assigned = StudentsSubject.find_by_student_id_and_subject_id(s.id,appropriate_elective_subject_id)
            @assigned.delete unless @assigned.nil?
          end
        end
      end
      @students = @batch_data
      @elective_group = ElectiveGroup.find(@elective_subject.elective_group_id)
    end
    render(:update) do |page|
      page.replace_html 'category-list', :partial=>"all_assign"
      page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg42')}</p>"
    end
  end

  

  def fees
    @dates=FinanceFeeCollection.find(:all,:joins=>"INNER JOIN fee_collection_batches on fee_collection_batches.finance_fee_collection_id=finance_fee_collections.id INNER JOIN finance_fees on finance_fees.fee_collection_id=finance_fee_collections.id",:conditions=>"finance_fees.student_id='#{@student.id}'  and finance_fee_collections.is_deleted=#{false} and ((finance_fees.balance > 0 and finance_fees.batch_id<>#{@student.batch_id}) or (finance_fees.batch_id=#{@student.batch_id}) )").uniq
    
    @dates_paid = FinanceFeeCollection.find(:all,:joins=>"INNER JOIN fee_collection_batches on fee_collection_batches.finance_fee_collection_id=finance_fee_collections.id INNER JOIN finance_fees on finance_fees.fee_collection_id=finance_fee_collections.id",:conditions=>"finance_fees.student_id='#{@student.id}'  and finance_fee_collections.is_deleted=#{false} and ((finance_fees.balance = 0))", :order=>'finance_fee_collections.due_date DESC').uniq # and finance_fees.batch_id = #{@student.batch_id}
    @dates_unpaid = FinanceFeeCollection.find(:all,:joins=>"INNER JOIN fee_collection_batches on fee_collection_batches.finance_fee_collection_id=finance_fee_collections.id INNER JOIN finance_fees on finance_fees.fee_collection_id=finance_fee_collections.id",:conditions=>"finance_fees.student_id='#{@student.id}'  and finance_fee_collections.is_deleted=#{false} and ((finance_fees.balance > 0)  )", :order=>'finance_fee_collections.due_date DESC').uniq # and finance_fees.batch_id = #{@student.batch_id}
    
    if request.post?
      @student.update_attribute(:has_paid_fees,params[:fee][:has_paid_fees]) unless params[:fee].nil?
      flash[:notice] = "#{t('status_updated')}"
      redirect_to :action => "fees", :id => @student.id
    end
    unless params[:mobile_view].blank?
      render "mobile_fees",:layout => false
    end
  end

  def fee_details
    advance_fee_collection = false
    @self_advance_fee = false
    @fee_has_advance_particular = false
    
    @batch = @student.batch
    
    @date  = FinanceFeeCollection.find(params[:id2])
    
    @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{@student.id}'")
    
    @financefee = @student.finance_fee_by_date @date
    
    if @financefee.has_advance_fee_id
      if @date.is_advance_fee_collection
        @self_advance_fee = true
        advance_fee_collection = true
      end
      @fee_has_advance_particular = true
      @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
      @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
    end
    
    @fee_collection = FinanceFeeCollection.find(params[:id2])
    @due_date = @fee_collection.due_date
    @paid_fees = @fee.finance_transactions

    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    
    if advance_fee_collection
      fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
      if fee_collection_advances_particular.include?(0)
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      end
    else
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
    end
    
    if advance_fee_collection
      month = 1
      payable = 0
      @fee_collection_advances.each do |fee_collection_advance|
        @fee_particulars.each do |particular|
          if fee_collection_advance.particular_id == particular.finance_fee_particular_category_id
            payable += particular.amount * fee_collection_advance.no_of_month.to_i
          else
            payable += particular.amount
          end
        end
      end
      @total_payable=payable.to_f
    else  
      @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
    end
    
    @total_discount = 0
        
    @adv_fee_discount = false
    @actual_discount = 1

    if advance_fee_collection
      calculate_discount(@date, @batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
    else
      if @fee_has_advance_particular
        calculate_discount(@date, @batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
      else
        calculate_discount(@date, @batch, @student, false, nil, @fee_has_advance_particular)
      end
    end

    bal=(@total_payable-@total_discount).to_f
    unless params[:submission_date].nil? or params[:submission_date].empty? or params[:submission_date].blank?
      require 'date'
      @submission_date = Date.parse(params[:submission_date])
      days=(Date.parse(params[:submission_date])-@date.due_date.to_date).to_i
    else
      @submission_date = Date.today
      if @financefee.is_paid
        @paid_fees = @financefee.finance_transactions
        days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
      else
        days=(Date.today-@date.due_date.to_date).to_i
      end
    end

    auto_fine=@date.fine

    @has_fine_discount = false
    if days > 0 and auto_fine #and @financefee.is_paid == false
      @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
      @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule

      calculate_extra_fine(@date, @batch, @student, @fine_rule)

      @new_fine_amount = @fine_amount
      get_fine_discount(@date, @batch, @student)
      if @fine_amount < 0
        @fine_amount = 0
      end
    end

    @fine_amount=0 if @financefee.is_paid

    unless advance_fee_collection
      if @total_discount == 0
        @adv_fee_discount = true
        @actual_discount = 0
        calculate_discount(@date, @batch, @student, false, nil, @fee_has_advance_particular)
      end
    end
    unless params[:mobile_view].blank?
      render 'mobile_fee_details',:layout => false
    end
  end
  
  #  # Graphs
  #
  #  def graph_for_previous_years_marks_overview
  #    student = Student.find(params[:student])
  #
  #    x_labels = []
  #    data = []
  #
  #    student.all_courses.each do |c|
  #      x_labels << c.name
  #      data << student.annual_weighted_marks(c.academic_year_id)
  #    end
  #
  #    if params[:graphtype] == 'Line'
  #      line = Line.new
  #    else
  #      line = BarFilled.new
  #    end
  #
  #    line.width = 1; line.colour = '#5E4725'; line.dot_size = 5; line.values = data
  #
  #    x_axis = XAxis.new
  #    x_axis.labels = x_labels
  #
  #    y_axis = YAxis.new
  #    y_axis.set_range(0,100,20)
  #
  #    title = Title.new(student.full_name)
  #
  #    x_legend = XLegend.new("Academic year")
  #    x_legend.set_style('{font-size: 14px; color: #778877}')
  #
  #    y_legend = YLegend.new("Total marks")
  #    y_legend.set_style('{font-size: 14px; color: #770077}')
  #
  #    chart = OpenFlashChart.new
  #    chart.set_title(title)
  #    chart.y_axis = y_axis
  #    chart.x_axis = x_axis
  #
  #    chart.add_element(line)
  #
  #    render :text => chart.to_s
  #  end
  #
  #  def graph_for_student_annual_overview
  #    student = Student.find(params[:student])
  #    course = Course.find_by_academic_year_id(params[:year]) if params[:year]
  #    course ||= student.course
  #    subs = course.subjects
  #    exams = Examination.find_all_by_subject_id(subs, :select => "DISTINCT examination_type_id")
  #    etype_ids = exams.collect { |x| x.examination_type_id }
  #    examtypes = ExaminationType.find(etype_ids)
  #
  #    x_labels = []
  #    data = []
  #
  #    examtypes.each do |et|
  #      x_labels << et.name
  #      data << student.examtype_average_marks(et, course)
  #    end
  #
  #    x_axis = XAxis.new
  #    x_axis.labels = x_labels
  #
  #    line = BarFilled.new
  #
  #    line.width = 1
  #    line.colour = '#5E4725'
  #    line.dot_size = 5
  #    line.values = data
  #
  #    y = YAxis.new
  #    y.set_range(0,100,20)
  #
  #    title = Title.new('Title')
  #
  #    x_legend = XLegend.new("Examination name")
  #    x_legend.set_style('{font-size: 14px; color: #778877}')
  #
  #    y_legend = YLegend.new("Average marks")
  #    y_legend.set_style('{font-size: 14px; color: #770077}')
  #
  #    chart = OpenFlashChart.new
  #    chart.set_title(title)
  #    chart.set_x_legend(x_legend)
  #    chart.set_y_legend(y_legend)
  #    chart.y_axis = y
  #    chart.x_axis = x_axis
  #
  #    chart.add_element(line)
  #
  #    render :text => chart.to_s
  #  end
  #
  #  def graph_for_subject_wise_report_for_one_subject
  #    student = Student.find params[:student]
  #    subject = Subject.find params[:subject]
  #    exams = Examination.find_all_by_subject_id(subject.id, :order => 'date asc')
  #
  #    data = []
  #    x_labels = []
  #
  #    exams.each do |e|
  #      exam_result = ExaminationResult.find_by_examination_id_and_student_id(e, student.id)
  #      unless exam_result.nil?
  #        data << exam_result.percentage_marks
  #        x_labels << XAxisLabel.new(exam_result.examination.examination_type.name, '#000000', 10, 0)
  #      end
  #    end
  #
  #    x_axis = XAxis.new
  #    x_axis.labels = x_labels
  #
  #    line = BarFilled.new
  #
  #    line.width = 1
  #    line.colour = '#5E4725'
  #    line.dot_size = 5
  #    line.values = data
  #
  #    y = YAxis.new
  #    y.set_range(0,100,20)
  #
  #    title = Title.new(subject.name)
  #
  #    x_legend = XLegend.new("Examination name")
  #    x_legend.set_style('{font-size: 14px; color: #778877}')
  #
  #    y_legend = YLegend.new("Marks")
  #    y_legend.set_style('{font-size: 14px; color: #770077}')
  #
  #    chart = OpenFlashChart.new
  #    chart.set_title(title)
  #    chart.set_x_legend(x_legend)
  #    chart.set_y_legend(y_legend)
  #    chart.y_axis = y
  #    chart.x_axis = x_axis
  #
  #    chart.add_element(line)
  #
  #    render :text => chart.to_s
  #  end
  #
  #  def graph_for_exam_report
  #    student = Student.find(params[:student])
  #    examtype = ExaminationType.find(params[:examtype])
  #    course = student.course
  #    subjects = course.subjects_with_exams
  #
  #    x_labels = []
  #    data = []
  #    data2 = []
  #
  #    subjects.each do |s|
  #      exam = Examination.find_by_subject_id_and_examination_type_id(s, examtype)
  #      res = ExaminationResult.find_by_examination_id_and_student_id(exam, student)
  #      unless res.nil?
  #        x_labels << s.name
  #        data << res.percentage_marks
  #        data2 << exam.average_marks * 100 / exam.max_marks
  #      end
  #    end
  #
  #    bargraph = BarFilled.new()
  #    bargraph.width = 1;
  #    bargraph.colour = '#bb0000';
  #    bargraph.dot_size = 5;
  #    bargraph.text = "Student's marks"
  #    bargraph.values = data
  #
  #    bargraph2 = BarFilled.new
  #    bargraph2.width = 1;
  #    bargraph2.colour = '#5E4725';
  #    bargraph2.dot_size = 5;
  #    bargraph2.text = "Class average"
  #    bargraph2.values = data2
  #
  #    x_axis = XAxis.new
  #    x_axis.labels = x_labels
  #
  #    y_axis = YAxis.new
  #    y_axis.set_range(0,100,20)
  #
  #    title = Title.new(student.full_name)
  #
  #    x_legend = XLegend.new("Academic year")
  #    x_legend.set_style('{font-size: 14px; color: #778877}')
  #
  #    y_legend = YLegend.new("Total marks")
  #    y_legend.set_style('{font-size: 14px; color: #770077}')
  #
  #    chart = OpenFlashChart.new
  #    chart.set_title(title)
  #    chart.y_axis = y_axis
  #    chart.x_axis = x_axis
  #    chart.y_legend = y_legend
  #    chart.x_legend = x_legend
  #
  #    chart.add_element(bargraph)
  #    chart.add_element(bargraph2)
  #
  #    render :text => chart.render
  #  end
  #
  #  def graph_for_academic_report
  #    student = Student.find(params[:student])
  #    course = student.course
  #    examtypes = ExaminationType.find( ( course.examinations.collect { |x| x.examination_type_id } ).uniq )
  #    x_labels = []
  #    data = []
  #    data2 = []
  #
  #    examtypes.each do |e_type|
  #      total = 0
  #      max_total = 0
  #      exam = Examination.find_all_by_examination_type_id(e_type.id)
  #      exam.each do |t|
  #        res = ExaminationResult.find_by_examination_id_and_student_id(t.id, student.id)
  #        total += res.marks
  #        max_total += res.maximum_marks
  #      end
  #      class_max =0
  #      class_total = 0
  #      exam.each do |t|
  #        res = ExaminationResult.find_all_by_examination_id(t.id)
  #        res.each do |res|
  #          class_max += res.maximum_marks
  #          class_total += res.marks
  #        end
  #      end
  #      class_avg = (class_total*100/class_max).to_f
  #      percentage = ((total*100)/max_total).to_f
  #      x_labels << e_type.name
  #      data << percentage
  #      data2 << class_avg
  #    end
  #
  #    bargraph = BarFilled.new()
  #    bargraph.width = 1;
  #    bargraph.colour = '#bb0000';
  #    bargraph.dot_size = 5;
  #    bargraph.text = "Student's average"
  #    bargraph.values = data
  #
  #    bargraph2 = BarFilled.new
  #    bargraph2.width = 1;
  #    bargraph2.colour = '#5E4725';
  #    bargraph2.dot_size = 5;
  #    bargraph2.text = "Class average"
  #    bargraph2.values = data2
  #
  #    x_axis = XAxis.new
  #    x_axis.labels = x_labels
  #    y_axis = YAxis.new
  #    y_axis.set_range(0,100,20)
  #
  #    x_legend = XLegend.new("Examinations")
  #    x_legend.set_style('{font-size: 14px; color: #778877}')
  #
  #    y_legend = YLegend.new("Percentage")
  #    y_legend.set_style('{font-size: 14px; color: #770077}')
  #
  #    chart = OpenFlashChart.new
  #
  #    chart.y_axis = y_axis
  #    chart.x_axis = x_axis
  #    chart.y_legend = y_legend
  #    chart.x_legend = x_legend
  #
  #    chart.add_element(bargraph)
  #    chart.add_element(bargraph2)
  #
  #    render :text => chart.render
  #  end
  #
  #  def graph_for_annual_academic_report
  #    student = Student.find(params[:student])
  #    student_all = Student.find_all_by_course_id(params[:course])
  #    total = 0
  #    sum = student_all.size
  #    student_all.each { |s| total += s.annual_weighted_marks(s.course.academic_year_id) }
  #    t = (total/sum).to_f
  #
  #    x_labels = []
  #    data = []
  #    data2 = []
  #
  #    x_labels << "Annual report".to_s
  #    data << student.annual_weighted_marks(student.course.academic_year_id)
  #    data2 << t
  #
  #    bargraph = BarFilled.new()
  #    bargraph.width = 1;
  #    bargraph.colour = '#bb0000';
  #    bargraph.dot_size = 5;
  #    bargraph.text = "Student's average"
  #    bargraph.values = data
  #
  #    bargraph2 = BarFilled.new
  #    bargraph2.width = 1;
  #    bargraph2.colour = '#5E4725';
  #    bargraph2.dot_size = 5;
  #    bargraph2.text = "Class average"
  #    bargraph2.values = data2
  #
  #    x_axis = XAxis.new
  #    x_axis.labels = x_labels
  #
  #    y_axis = YAxis.new
  #    y_axis.set_range(0,100,20)
  #
  #    x_legend = XLegend.new("Examinations")
  #    x_legend.set_style('{font-size: 14px; color: #778877}')
  #
  #    y_legend = YLegend.new("Weightage")
  #    y_legend.set_style('{font-size: 14px; color: #770077}')
  #
  #    chart = OpenFlashChart.new
  #
  #    chart.y_axis = y_axis
  #    chart.x_axis = x_axis
  #    chart.y_legend = y_legend
  #    chart.x_legend = x_legend
  #
  #    chart.add_element(bargraph)
  #    chart.add_element(bargraph2)
  #
  #    render :text => chart.render
  #
  #  end
  def update_is_promoted
    student_id = @current_user.student_entry.id
    @student = Student.find_by_id(student_id)
    @student.update_attribute(:is_promoted,0)
    render :nothing => true
  end

  private
  
  def delete_student_and_guardian(std_id)
    @student = Student.find_by_id(std_id)
    unless @student.nil?
      user_student = User.find_by_id(@student.user_id)
      std_guardian_list = Guardian.find_all_by_ward_id(@student.id)
      unless std_guardian_list.nil? 
        std_guardian_list.each do |g|
          user_guardian = User.find_by_id(g.user_id)
          user_guardian.delete
          g.delete
        end
      end  
      user_student.delete
      @student.delete
    end
  end
  
  def activated_student_and_guardian(std_id)
    require 'net/http'
    require 'uri'
    require "yaml"
    
    now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    
    @student = Student.find_by_id(std_id)
    unless @student.nil?
      @student.is_active = 1
      @student.is_deleted = 0
      @student.admission_date = now
      @student.save
      
      @user_student = User.find_by_id(@student.user_id)
      unless @user_student.nil?
        
        
        
        @user_student.is_deleted = 0
        @user_student.is_approved = 1
        @user_student.save
        
        
        @student_gurdian = StudentsGuardian.new
        @student_gurdian.s_username = @user_student.username
        @student_gurdian.admission_no = @student.admission_no
        @student_gurdian.s_first_name = @student.first_name
        @student_gurdian.s_middle_name = @student.middle_name
        @student_gurdian.s_last_name = @student.last_name
        @student_gurdian.student_id = @student.id
        @student_gurdian.school_id = @student.school_id
        
        
        if @user_student.free_user_id?
          champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
          api_endpoint = champs21_api_config['api_url']
          uri = URI(api_endpoint + "api/user/updateprofile")
          http = Net::HTTP.new(uri.host, uri.port)
          auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
          auth_req.set_form_data({"user_id" =>@user_student.free_user_id, "paid_id" => @student.id, "paid_username" => @user_student.username, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s})
          auth_res = http.request(auth_req)
          @auth_response = ActiveSupport::JSON.decode(auth_res.body)
        end  
        
        @std_guardian_list = Guardian.find_all_by_ward_id(@student.id)
        unless @std_guardian_list.nil? 
          @std_guardian_list.each do |g|
            
            @user_guardian = User.find_by_id(g.user_id)
            
            if @student.immediate_contact_id?
              if @student.immediate_contact_id == g.id
                @user_guardian.is_deleted = 0
                
                @student_gurdian.g_first_name = @user_guardian.first_name
                @student_gurdian.g_last_name = @user_guardian.last_name
                @student_gurdian.g_username = @user_guardian.username
                @student_gurdian.guardian_id = g.id
                
              end
            end
            @user_guardian.is_approved = 1
            @user_guardian.save
            
            if @user_guardian.free_user_id?
              champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
              api_endpoint = champs21_api_config['api_url']
              uri = URI(api_endpoint + "api/user/updateprofile")
              http = Net::HTTP.new(uri.host, uri.port)
              auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
              auth_req.set_form_data({"user_id" =>@user_guardian.free_user_id, "paid_id" => g.id, "paid_username" => @user_guardian.username, "paid_school_id" => MultiSchool.current_school.id, "paid_school_code" => MultiSchool.current_school.code.to_s})
              auth_res = http.request(auth_req)
              @auth_response = ActiveSupport::JSON.decode(auth_res.body)
            end 
            
          end
        end
        
        @student_gurdian.save
        
        
      end 
      
    end
  end
  
  
  def find_student
    @sms_module = Configuration.available_modules
    @student = Student.find(params[:id])
  end
  

  def paginate_collection(model1,model1_condition,model2 = nil,model2_condition = Hash.new,per_page = 30,current_page = 0,type = "find",sort_by = nil)
    per_page = per_page.to_i
    klass1 = model1.to_s.camelize.constantize
    klass2 = model2.to_s.camelize.constantize
    collection1 = klass1.find(:all,:conditions => model1_condition).paginate :per_page => per_page/2 ,:page => current_page if type == "find"
    collection1 = klass1.send("ascend_by_#{sort_by}").search(model1_condition).paginate :per_page => per_page/2 ,:page => current_page if type == "search"
    per_page1 = per_page / 2
    if collection1.count < per_page/2
      per_page1 = per_page / 2 + (per_page / 2 - collection1.count)
    end
    collection2 = klass2.find(:all,:conditions => model2_condition).paginate :per_page => per_page1 ,:page => current_page  if type == "find"
    collection2 = klass2.send("ascend_by_#{sort_by}").search(model2_condition).paginate :per_page => per_page1 ,:page => current_page  if type == "search"
    final_collection = (collection1 + collection2).sort_by{|a| [a.send(sort_by).downcase]}
    return final_collection
  end

  def model_records_count(model1,model1_condition,model2,model2_condition,type)
    
  end
  
  def date_format(date)
    /(\d{4}-\d{2}-\d{2})/.match(date)
  end

  def calculate_discount(date,batch,student,is_advance_fee_collection,advance_fee,fee_has_advance_particular)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id(student.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end

    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []
    advance_fee_particular = []
    unless advance_fee.nil? or advance_fee.empty? or advance_fee.blank?
      advance_fee_particular = advance_fee.map(&:particular_id)
    end
    if MultiSchool.current_school.id == 312
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee_particular.include?(0))
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)) }
        @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        @discounts_amount = []
        @discounts.each do |d|
          @discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
          @total_discount = @total_discount + @discounts_amount[d.id]
        end
      else
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)) }
        @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        @discounts_amount = []
        @discounts.each do |d|
          @discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
          @total_discount = @total_discount + @discounts_amount[d.id]
        end
      end
    else
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee_particular.include?(0))
        deduct_fee = 0
        if fee_has_advance_particular and !advance_fee_particular.include?(0)
          unless advance_fee_particular.blank?
            particular_id = advance_fee_particular.join(",")
            fee_particulars_deduct = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{particular_id})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            deduct_fee = fee_particulars_deduct.map{|l| l.amount}.sum.to_f
          end
        end
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @onetime_discounts.length > 0
          one_time_total_amount_discount= true
          @onetime_discounts_amount = []
          @onetime_discounts.each do |d|
            @onetime_discounts_amount[d.id] = (@total_payable - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount = @total_discount + @onetime_discounts_amount[d.id]
          end
        else
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          @onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if @onetime_discounts.length > 0
            one_time_discount = true
            @onetime_discounts_amount = []
            i = 0
            @onetime_discounts.each do |d|   
              onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @onetime_discounts_amount[d.id] = discount_amt
                @total_discount = @total_discount + discount_amt
                i = i + 1
              end
            end
          end
        end

        unless one_time_total_amount_discount
          if onetime_discount_particulars_id.empty?
            onetime_discount_particulars_id[0] = 0
          end
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @discounts_amount = []
            @discounts.each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @discounts_amount[d.id] = discount_amt
                @total_discount = @total_discount + discount_amt
              end
            end
          else  
            unless one_time_discount
              deduct_fee = 0
              if fee_has_advance_particular and !advance_fee_particular.include?(0)
                unless advance_fee_particular.blank?
                  particular_id = advance_fee_particular.join(",")
                  fee_particulars_deduct = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{particular_id})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                  deduct_fee = fee_particulars_deduct.map{|l| l.amount}.sum.to_f
                end
              end
              @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              @discounts_amount = []
              @discounts.each do |d|
                @discounts_amount[d.id] = (@total_payable - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
                @total_discount = @total_discount + @discounts_amount[d.id]
              end
            end
          end
        end
      else
        one_time_total_amount_discount= false
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }

        if @onetime_discounts.length > 0
          one_time_total_amount_discount= true
          @onetime_discounts_amount = []
          @onetime_discounts.each do |d|
            @onetime_discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount = @total_discount + @onetime_discounts_amount[d.id]
          end
        end

        unless one_time_total_amount_discount
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @discounts_amount = []
            @discounts.each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                month = 1
                unless advance_fee.nil?
                  advance_fee.each do |fee|
                    if fee.particular_id == fee_particulars_single.finance_fee_particular_category_id
                      month = fee.no_of_month.to_i
                    end
                  end
                end
                payable_ampt = (fee_particulars_single.map{|l| l.amount}.sum.to_f * month.to_i).to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @discounts_amount[d.id] = discount_amt
                @total_discount = @total_discount + discount_amt
              end
            end
          else  
            unless one_time_discount
              deduct_fee = 0
              @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              @discounts_amount = []
              @discounts.each do |d|
                @discounts_amount[d.id] = (@total_payable - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
                @total_discount = @total_discount + @discounts_amount[d.id]
              end
            end  
          end
        end
      end
    end
  end

  def calculate_discount_index_all(date,batch,student,ind,is_advance_fee_collection,advance_fee,fee_has_advance_particular)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id(student.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end

    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []

    if MultiSchool.current_school.id == 312
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee.particular_id.to_i == 0)
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if @all_onetime_discounts[ind].length > 0
          @all_onetime_discounts_amount[ind] = []
          @all_onetime_discounts[ind].each do |d|
            @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
            @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
          end
        end
      else
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if @all_onetime_discounts[ind].length > 0
          @all_onetime_discounts_amount[ind] = []
          @all_onetime_discounts[ind].each do |d|
            @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
            @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
          end
        end
      end
    else
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee.particular_id.to_i == 0)
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @all_onetime_discounts[ind].length > 0
          one_time_total_amount_discount= true
          @all_onetime_discounts_amount[ind] = []
          @all_onetime_discounts[ind].each do |d|
            @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
            @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
          end
        else
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if @all_onetime_discounts[ind].length > 0
            one_time_discount = true
            @all_onetime_discounts_amount[ind] = []
            i = 0
            @all_onetime_discounts[ind].each do |d|   
              onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @all_onetime_discounts_amount[ind][d.id] = discount_amt
                @all_total_discount[ind] = @all_total_discount[ind] + discount_amt
                i = i + 1
              end
            end
          end
        end

        unless one_time_total_amount_discount
          if onetime_discount_particulars_id.empty?
            onetime_discount_particulars_id[0] = 0
          end
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)}
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @all_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @all_discounts_amount[ind] = []
            @all_discounts[ind].each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @all_discounts_amount[ind][d.id] = discount_amt
                @all_total_discount[ind] = @all_total_discount[ind] + discount_amt
              end
            end
          else  
            unless one_time_discount
              @all_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              @all_discounts_amount[ind] = []
              @all_discounts[ind].each do |d|
                @all_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
                @all_total_discount[ind] = @all_total_discount[ind] + @all_discounts_amount[ind][d.id]
              end
            end
          end
        end
      else
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee.discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @all_onetime_discounts[ind].length > 0
          one_time_total_amount_discount= true
          @all_onetime_discounts_amount[ind] = []
          @all_onetime_discounts[ind].each do |d|
            @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
            @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
          end
        end

        unless one_time_total_amount_discount
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)}
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @all_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @all_discounts_amount[ind] = []
            @all_discounts[ind].each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                payable_ampt = (fee_particulars_single.map{|l| l.amount}.sum.to_f * advance_fee.no_of_month.to_i).to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @all_discounts_amount[ind][d.id] = discount_amt
                @all_total_discount[ind] = @all_total_discount[ind] + discount_amt
              end
            end
          end
        end
      end
    end
  end

  def calculate_discount_index(date,batch,student,ind,is_advance_fee_collection,advance_fee,fee_has_advance_particular)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id(student.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end

    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []
    advance_fee_particular = []
    unless advance_fee.nil? or advance_fee.empty? or advance_fee.blank?
      advance_fee_particular = advance_fee.map(&:particular_id)
    end
    if MultiSchool.current_school.id == 312
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee_particular.include?(0))
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)) }
        @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        @discounts_amount[ind] = []
        @discounts[ind].each do |d|
          @discounts_amount[ind][d.id] = @total_payable[ind] * d.discount.to_f/ (d.is_amount?? @total_payable[ind] : 100)
          @total_discount[ind] = @total_discount[ind] + @discounts_amount[ind][d.id]
        end
      else
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)) }
        @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        @discounts_amount[ind] = []
        @discounts[ind].each do |d|
          @discounts_amount[ind][d.id] = @total_payable[ind] * d.discount.to_f/ (d.is_amount?? @total_payable[ind] : 100)
          @total_discount[ind] = @total_discount[ind] + @discounts_amount[ind][d.id]
        end
      end
    else
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee_particular.include?(0))
        deduct_fee = 0
        if fee_has_advance_particular and !advance_fee_particular.include?(0)
          unless advance_fee_particular.blank?
            particular_id = advance_fee_particular.join(",")
            fee_particulars_deduct = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{particular_id})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            deduct_fee = fee_particulars_deduct.map{|l| l.amount}.sum.to_f
          end
        end
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @onetime_discounts[ind].length > 0
          one_time_total_amount_discount= true
          @onetime_discounts_amount = []
          @onetime_discounts[ind].each do |d|
            @onetime_discounts_amount[ind][d.id] = (@total_payable[ind] - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable[ind] : 100)
            @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
          end
        else
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if @onetime_discounts[ind].length > 0
            one_time_discount = true
            @onetime_discounts_amount[ind] = []
            i = 0
            @onetime_discounts[ind].each do |d|   
              onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @onetime_discounts_amount[ind][d.id] = discount_amt
                @total_discount[ind] = @total_discount[ind] + discount_amt
                i = i + 1
              end
            end
          end
        end

        unless one_time_total_amount_discount
          if onetime_discount_particulars_id.empty?
            onetime_discount_particulars_id[0] = 0
          end
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @discounts_amount[ind] = []
            @discounts[ind].each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @discounts_amount[ind][d.id] = discount_amt
                @total_discount[ind] = @total_discount[ind] + discount_amt
              end
            end
          else  
            unless one_time_discount
              deduct_fee = 0
              if fee_has_advance_particular and !advance_fee_particular.include?(0)
                unless advance_fee_particular.blank?
                  particular_id = advance_fee_particular.join(",")
                  fee_particulars_deduct = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{particular_id})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                  deduct_fee = fee_particulars_deduct.map{|l| l.amount}.sum.to_f
                end
              end
              @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              @discounts_amount[ind] = []
              @discounts[ind].each do |d|
                @discounts_amount[ind][d.id] = (@total_payable[ind] - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable[ind] : 100)
                @total_discount[ind] = @total_discount[ind] + @discounts_amount[ind][d.id]
              end
            end
          end
        end
      else
        one_time_total_amount_discount= false
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }

        if @onetime_discounts[ind].length > 0
          one_time_total_amount_discount= true
          @onetime_discounts_amount[ind] = []
          @onetime_discounts[ind].each do |d|
            @onetime_discounts_amount[ind][d.id] = @total_payable[ind] * d.discount.to_f/ (d.is_amount?? @total_payable[ind] : 100)
            @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
          end
        end

        unless one_time_total_amount_discount
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @discounts_amount[ind] = []
            @discounts[ind].each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                month = 1
                unless advance_fee.nil?
                  advance_fee.each do |fee|
                    if fee.particular_id == fee_particulars_single.finance_fee_particular_category_id
                      month = fee.no_of_month.to_i
                    end
                  end
                end
                payable_ampt = (fee_particulars_single.map{|l| l.amount}.sum.to_f * month.to_i).to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @discounts_amount[ind][d.id] = discount_amt
                @total_discount[ind] = @total_discount[ind] + discount_amt
              end
            end
          else  
            unless one_time_discount
              deduct_fee = 0
              @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              @discounts_amount[ind] = []
              @discounts[ind].each do |d|
                @discounts_amount[ind][d.id] = (@total_payable[ind] - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable[ind] : 100)
                @total_discount[ind] = @total_discount[ind] + @discounts_amount[ind][d.id]
              end
            end  
          end
        end
      end
    end
  end

  def calculate_extra_fine(date,batch,student,fine_rule)
    if MultiSchool.current_school.id == 340
      #GET THE NEXT ALL months 
      extra_fine = 0
      s_date = date.start_date.to_date.beginning_of_month
      e_date = date.start_date.to_date.end_of_month
      other_months = FinanceFeeCollection.find(:all, :conditions => ["start_date NOT BETWEEN ? AND ? and is_deleted=#{false}", s_date, e_date], :order => "due_date asc")
      unless other_months.nil? or other_months.empty?
        other_months.each do |other_month|
          fee_for_batch = FeeCollectionBatch.find(:all, :conditions => ["batch_id = ? and is_deleted=#{false} and finance_fee_collection_id != ?", batch.id, date.id])
          unless fee_for_batch.nil? or fee_for_batch.empty?
            fine_amount = fine_rule.fine_amount if fine_rule
            extra_fine = extra_fine + fine_amount
          end
        end
      end
      @fine_amount = @fine_amount + extra_fine
    end
  end

  def calculate_extra_fine_index_all(date,batch,student,fine_rule,ind)
    if MultiSchool.current_school.id == 340
      #GET THE NEXT ALL months 
      extra_fine = 0
      s_date = date.start_date.to_date.beginning_of_month
      e_date = date.start_date.to_date.end_of_month
      other_months = FinanceFeeCollection.find(:all, :conditions => ["start_date NOT BETWEEN ? AND ? and is_deleted=#{false}", s_date, e_date], :order => "due_date asc")
      unless other_months.nil? or other_months.empty?
        other_months.each do |other_month|
          fee_for_batch = FeeCollectionBatch.find(:all, :conditions => ["batch_id = ? and is_deleted=#{false} and finance_fee_collection_id != ?", batch.id, date.id])
          unless fee_for_batch.nil? or fee_for_batch.empty?
            fine_amount = fine_rule.fine_amount if fine_rule
            extra_fine = extra_fine + fine_amount
          end
        end
      end
      @all_fine_amount[ind] = @all_fine_amount[ind] + extra_fine
    end
  end

  def calculate_extra_fine_index(date,batch,student,fine_rule,ind)
    if MultiSchool.current_school.id == 340
      #GET THE NEXT ALL months 
      extra_fine = 0
      s_date = date.start_date.to_date.beginning_of_month
      e_date = date.start_date.to_date.end_of_month
      other_months = FinanceFeeCollection.find(:all, :conditions => ["start_date NOT BETWEEN ? AND ? and is_deleted=#{false}", s_date, e_date], :order => "due_date asc")
      unless other_months.nil? or other_months.empty?
        other_months.each do |other_month|
          fee_for_batch = FeeCollectionBatch.find(:all, :conditions => ["batch_id = ? and is_deleted=#{false} and finance_fee_collection_id != ?", batch.id, date.id])
          unless fee_for_batch.nil? or fee_for_batch.empty?
            fine_amount = fine_rule.fine_amount if fine_rule
            extra_fine = extra_fine + fine_amount
          end
        end
      end
      @fine_amount[ind] = @fine_amount[ind] + extra_fine
    end
  end

  def get_fine_discount(date,batch,student)
    if !@fine_amount.blank? and @fine_amount > 0
      fee_collection_discount_ids = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id_and_is_late(date.id, batch.id, true).map(&:fee_discount_id)
      unless fee_collection_discount_ids.nil? or fee_collection_discount_ids.empty?
        @discounts_on_lates = FeeDiscount.find(:all, :conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{true} and id IN (" + fee_collection_discount_ids.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @discounts_on_lates.length > 0
          @has_fine_discount = true
          @discounts_late_amount = []
          @discounts_on_lates.each do |d|   
            if @fine_amount > 0
              discount_amt = @new_fine_amount * d.discount.to_f/ (d.is_amount?? @new_fine_amount : 100)
              @fine_amount = @fine_amount - discount_amt
              if @fine_amount < 0
                discount_amt = 0
              end
              @discounts_late_amount[d.id] = discount_amt
            else
              @discounts_late_amount[d.id] = 0
            end
          end
        end
      end
    else
      @fine_amount = 0
    end  
  end

  def get_fine_discount_index(date,batch,student,ind)
    if @fine_amount[ind] > 0
      fee_collection_discount_ids = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id_and_is_late(date.id, batch.id, true).map(&:fee_discount_id)
      unless fee_collection_discount_ids.nil? or fee_collection_discount_ids.empty?
        @discounts_on_lates[ind] = FeeDiscount.find(:all, :conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{true} and id IN (" + fee_collection_discount_ids.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @discounts_on_lates[ind].length > 0
          @has_fine_discount[ind] = true
          @discounts_late_amount[ind] = []
          @discounts_on_lates[ind].each do |d|   
            if @fine_amount[ind] > 0
              discount_amt = @new_fine_amount[ind] * d.discount.to_f/ (d.is_amount?? @new_fine_amount[ind] : 100)
              @fine_amount[ind] = @fine_amount[ind] - discount_amt
              if @fine_amount[ind] < 0
                discount_amt = 0
              end
              @discounts_late_amount[ind][d.id] = discount_amt
            else
              @discounts_late_amount[ind][d.id] = 0
            end
          end
        end
      end
    end
  end

  def get_fine_discount_index_all(date,batch,student,ind)

    if @all_fine_amount[ind] > 0
      fee_collection_discount_ids = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id_and_is_late(date.id, batch.id, true).map(&:fee_discount_id)
      unless fee_collection_discount_ids.nil? or fee_collection_discount_ids.empty?
        @all_discounts_on_lates[ind] = FeeDiscount.find(:all, :conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{true} and id IN (" + fee_collection_discount_ids.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @all_discounts_on_lates[ind].length > 0
          @all_has_fine_discount[ind] = true
          @all_discounts_late_amount[ind] = []
          @all_discounts_on_lates[ind].each do |d|   
            if @all_fine_amount[ind] > 0
              discount_amt = @all_new_fine_amount[ind] * d.discount.to_f/ (d.is_amount?? @all_new_fine_amount[ind] : 100)
              @all_fine_amount[ind] = @all_fine_amount[ind] - discount_amt
              if @all_fine_amount[ind] < 0
                discount_amt = 0
              end
              @all_discounts_late_amount[ind][d.id] = discount_amt
            else
              @all_discounts_late_amount[ind][d.id] = 0
            end
          end
        end
      end
    end
  end

  def arrange_pay(student_id, fee_collection_id, submission_date)
      advance_fee_collection = false
      @self_advance_fee = false
      @fee_has_advance_particular = false

      @student = Student.find(student_id)
      
      @date = @fee_collection = FinanceFeeCollection.find(fee_collection_id)
      
      @batch = @fee_collection.batch
      
      @student_has_due = false
      @std_finance_fee_due = FinanceFee.find(:first,:conditions=>["finance_fee_collections.due_date < ? and finance_fees.is_paid = 0 and finance_fees.student_id = ?", @date.due_date,@student.id],:include=>"finance_fee_collection")
      unless @std_finance_fee_due.blank?
        @student_has_due = true
      end
      @financefee = @student.finance_fee_by_date(@date)

      if @financefee.has_advance_fee_id
        if @date.is_advance_fee_collection
          @self_advance_fee = true
          advance_fee_collection = true
        end
        @fee_has_advance_particular = true
        @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
        @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
      end

      @due_date = @fee_collection.due_date
      
      flash[:warning]=nil
      #flash[:notice]=nil
      
      @trans_id_ssl_commerce = "tran"+student_id.to_s+fee_collection_id.to_s
      @paid_fees = @financefee.finance_transactions

      if advance_fee_collection
        fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
        if fee_collection_advances_particular.include?(0)
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
        else
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee.batch_id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
        end
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
      end

      if advance_fee_collection
        month = 1
        payable = 0
        @fee_collection_advances.each do |fee_collection_advance|
          @fee_particulars.each do |particular|
            if fee_collection_advance.particular_id == particular.finance_fee_particular_category_id
              payable += particular.amount * fee_collection_advance.no_of_month.to_i
            else
              payable += particular.amount
            end
          end
        end
        @total_payable=payable.to_f
      else  
        @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
      end

      @total_discount = 0

      #calculate_discount(@date, @financefee.batch, @student, @financefee.is_paid)
      @adv_fee_discount = false
      @actual_discount = 1

      if advance_fee_collection
        calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
      else
        if @fee_has_advance_particular
          calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
        else
          calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
        end
      end

      bal=(@total_payable-@total_discount).to_f
      unless submission_date.nil? or submission_date.empty? or submission_date.blank?
        require 'date'
        @submission_date = Date.parse(submission_date)
        days=(Date.parse(submission_date)-@date.due_date.to_date).to_i
      else
        @submission_date = Date.today
        if @financefee.is_paid
          @paid_fees = @financefee.finance_transactions
          days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
        else
          days=(Date.today-@date.due_date.to_date).to_i
        end
      end

      auto_fine=@date.fine

      @has_fine_discount = false
      if days > 0 and auto_fine #and @financefee.is_paid == false
        @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
        @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule

        calculate_extra_fine(@date, @financefee.batch, @student, @fine_rule)

        @new_fine_amount = @fine_amount
        get_fine_discount(@date, @financefee.batch, @student)
        if @fine_amount < 0
           @fine_amount = 0
        end
      end

      @fine_amount=0 if @financefee.is_paid

      unless advance_fee_collection
        if @total_discount == 0
          @adv_fee_discount = true
          @actual_discount = 0
          calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
        end
      end

      total_fees =@financefee.balance.to_f+@fine_amount.to_f

      if @active_gateway == "trustbank"
        paid_fees = @financefee.finance_transactions
        paid_amount = 0.0
        unless paid_fees.nil? or paid_fees.blank?
          paid_fees.each do |pf|
            paid_amount += pf.amount
          end
        end
        remaining_amount = bal - paid_amount

        remaining_amount = total_fees - paid_amount

        unless @financefee.is_paid
          finance_order = FinanceOrder.find(:first, :conditions => "finance_fee_id = #{@financefee.id} and student_id = #{@financefee.student_id} and batch_id = #{@financefee.batch_id} and status = 0")
          unless finance_order.nil?
            @order_id = "O" + finance_order.id.to_s
            finance_order.update_attributes(:order_id => @order_id)
          else
            finance_order = FinanceOrder.new()
            finance_order.finance_fee_id = @financefee.id
            finance_order.student_id = @financefee.student_id
            finance_order.batch_id = @financefee.batch_id
            finance_order.balance = remaining_amount
            finance_order.save
            @order_id = "O" + finance_order.id.to_s
            finance_order.update_attributes(:order_id => @order_id)
          end
        end
      end

      if @active_gateway == "Authorize.net"
        @sim_transaction = AuthorizeNet::SIM::Transaction.new(@merchant_id,@certificate, total_fees,{:hosted_payment_form => true,:x_description => "Fee-#{@student.admission_no}-#{@fee_collection.name}"})
        @sim_transaction.instance_variable_set("@custom_fields",{:x_description => "Fee (#{@student.full_name}-#{@student.admission_no}-#{@fee_collection.name})"})
        @sim_transaction.set_hosted_payment_receipt(AuthorizeNet::SIM::HostedReceiptPage.new(:link_method => AuthorizeNet::SIM::HostedReceiptPage::LinkMethod::GET, :link_text => "Back to #{current_school_name}", :link_url => URI.parse("http://#{request.host_with_port}/student/fee_details/#{student_id}/#{fee_collection_id}?create_transaction=1&only_path=false")))
      end
    end
    
    def arrange_multiple_pay(student_id, fees, submission_date)
      @order_id_saved = false
      @student = Student.find(student_id)
      @batch = @student.batch

      @fees_collections = fees

      @self_advance_fee = [] 
      @fee_has_advance_particular = []
      @date = [] 
      @fee_collection = []
      @student_has_due = []
      @financefee = []
      @advance_ids = []
      @fee_collection_advances = []
      @paid_fees = []
      @fee_particulars = []
      @total_payable = []
      @total_discount = []
      @adv_fee_discount = []
      @actual_discount = []
      @discounts_amount = []
      @discounts = []
      @onetime_discounts = []
      @onetime_discounts_amount = []
      @has_fine_discount = []
      @fine = []
      @fine_rule = []
      @fine_amount = []
      @vat = []
      @amount_to_pay = []
      @new_fine_amount = []

      fees.each do |fee|
        f = fee.to_i
        finance_fee = FinanceFee.find(f)
        fee_collection_id = finance_fee.fee_collection_id
        advance_fee_collection = false
        @self_advance_fee[f] = false
        @fee_has_advance_particular[f] = false

        @date[f] = @fee_collection[f] = FinanceFeeCollection.find(fee_collection_id)
        @student_has_due[f] = false
        @std_finance_fee_due = FinanceFee.find(:first,:conditions=>["finance_fee_collections.due_date < ? and finance_fees.is_paid = 0 and finance_fees.student_id = ?", @date[f].due_date,@student.id],:include=>"finance_fee_collection")
        unless @std_finance_fee_due.blank?
          @student_has_due[f] = true
        end
        @financefee[f] = @student.finance_fee_by_date(@date[f])

        if @financefee[f].has_advance_fee_id
          if @date[f].is_advance_fee_collection
            @self_advance_fee[f] = true
            advance_fee_collection = true
          end
          @fee_has_advance_particular[f] = true
          @advance_ids[f] = @financefee[f].fees_advances.map(&:advance_fee_id)
          @fee_collection_advances[f] = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids[f].join(",")})")
        end

        @due_date = @fee_collection[f].due_date

        flash[:warning]=nil
        #flash[:notice]=nil

        @trans_id_ssl_commerce = "tran"+student_id.to_s+fee_collection_id.to_s
        @paid_fees[f] = @financefee[f].finance_transactions

        if advance_fee_collection
          fee_collection_advances_particular = @fee_collection_advances[f].map(&:particular_id)
          if fee_collection_advances_particular.include?(0)
            @fee_particulars[f] = @date[f].finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee[f].batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[f].batch) }
          else
            @fee_particulars[f] = @date[f].finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee[f].batch_id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[f].batch) }
          end
        else
          @fee_particulars[f] = @date[f].finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee[f].batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[f].batch) }
        end

        if advance_fee_collection
          month = 1
          payable = 0
          @fee_collection_advances[f].each do |fee_collection_advance|
            @fee_particulars[f].each do |particular|
              if fee_collection_advance.particular_id == particular.finance_fee_particular_category_id
                payable += particular.amount * fee_collection_advance.no_of_month.to_i
              else
                payable += particular.amount
              end
            end
          end
          @total_payable[f]=payable.to_f
        else  
          @total_payable[f]=@fee_particulars[f].map{|s| s.amount}.sum.to_f
        end

        @total_discount[f] = 0

        @adv_fee_discount[f] = false
        @actual_discount[f] = 1

        if advance_fee_collection
          calculate_discount_index(@date[f],@financefee[f].batch,@student,f,true,@fee_collection_advances[f],@fee_has_advance_particular[f])
        else
          if @fee_has_advance_particular[f]
            calculate_discount_index(@date[f], @financefee[f].batch, @student,f, false, @fee_collection_advances[f], @fee_has_advance_particular[f])
          else
            calculate_discount_index(@date[f], @financefee[f].batch, @student,f, false, nil, @fee_has_advance_particular[f])
          end
        end

        bal=(@total_payable[f]-@total_discount[f]).to_f
        unless submission_date.nil? or submission_date.empty? or submission_date.blank?
          require 'date'
          @submission_date = Date.parse(submission_date)
          days=(Date.parse(submission_date)-@date[f].due_date.to_date).to_i
        else
          @submission_date = Date.today
          if @financefee[f].is_paid
            @paid_fees[f] = @financefee[f].finance_transactions
            days=(@paid_fees[f].first.transaction_date-@date[f].due_date.to_date).to_i
          else
            days=(Date.today-@date[f].due_date.to_date).to_i
          end
        end

        auto_fine=@date[f].fine

        @has_fine_discount[f] = false
        if days > 0 and auto_fine #and @financefee[f].is_paid == false
          @fine_rule[f]=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date[f].created_at}'"],:order=>'fine_days ASC')
          @fine_amount[f]=@fine_rule[f].is_amount ? @fine_rule[f].fine_amount : (bal*@fine_rule[f].fine_amount)/100 if @fine_rule[f]

          calculate_extra_fine_index(@date[f], @financefee[f].batch, @student, @fine_rule[f],f)

          @new_fine_amount[f] = @fine_amount[f]
          get_fine_discount_index(@date[f], @financefee[f].batch, @student, f)
          if @fine_amount[f] < 0
             @fine_amount[f] = 0
          end
        end

        @fine_amount[f]=0 if @financefee[f].is_paid

        unless advance_fee_collection
          if @total_discount[f] == 0
            @adv_fee_discount[f] = true
            @actual_discount[f] = 0
            calculate_discount_index(@date[f], @financefee[f].batch, @student, f,false, nil, @fee_has_advance_particular[f])
          end
        end

        total_fees =@financefee[f].balance.to_f+@fine_amount[f].to_f

        if @active_gateway == "trustbank"
          paid_fees = @financefee[f].finance_transactions
          paid_amount = 0.0
          unless paid_fees.nil? or paid_fees.blank?
            paid_fees.each do |pf|
              paid_amount += pf.amount
            end
          end
          remaining_amount = total_fees - paid_amount

          unless @financefee[f].is_paid
            finance_order = FinanceOrder.find(:first, :conditions => "finance_fee_id = #{@financefee[f].id} and student_id = #{@financefee[f].student_id} and batch_id = #{@financefee[f].batch_id} and status = 0")
            unless finance_order.nil?
              if @order_id_saved
                finance_order.update_attributes(:order_id => @order_id)
              else
                @order_id = "O" + finance_order.id.to_s
                finance_order.update_attributes(:order_id => @order_id)
                @order_id_saved = true
              end

            else
              if @order_id_saved
                finance_order = FinanceOrder.new()
                finance_order.finance_fee_id = @financefee[f].id
                finance_order.order_id = @order_id
                finance_order.student_id = @financefee[f].student_id
                finance_order.batch_id = @financefee[f].batch_id
                finance_order.balance = remaining_amount
                finance_order.save
              else
                finance_order = FinanceOrder.new()
                finance_order.finance_fee_id = @financefee[f].id
                finance_order.student_id = @financefee[f].student_id
                finance_order.batch_id = @financefee[f].batch_id
                finance_order.balance = remaining_amount
                finance_order.save
                @order_id = "O" + finance_order.id.to_s
                @order_id_saved = true
                finance_order.update_attributes(:order_id => @order_id)
              end
            end
          end
        end

        if @active_gateway == "Authorize.net"
          @sim_transaction = AuthorizeNet::SIM::Transaction.new(@merchant_id,@certificate, total_fees,{:hosted_payment_form => true,:x_description => "Fee-#{@student.admission_no}-#{@fee_collection[f].name}"})
          @sim_transaction.instance_variable_set("@custom_fields",{:x_description => "Fee (#{@student.full_name}-#{@student.admission_no}-#{@fee_collection[f].name})"})
          @sim_transaction.set_hosted_payment_receipt(AuthorizeNet::SIM::HostedReceiptPage.new(:link_method => AuthorizeNet::SIM::HostedReceiptPage::LinkMethod::GET, :link_text => "Back to #{current_school_name}", :link_url => URI.parse("http://#{request.host_with_port}/student/fee_details/#{student_id}/#{fee_collection_id}?create_transaction=1&only_path=false")))
        end
      end
    end
    
    def pay_student_index(amount_from_gateway, total_fees, request_params, orderId, trans_date, ref_id, fees)
      unless amount_from_gateway.to_f < 0
        unless amount_from_gateway.to_f > Champs21Precision.set_and_modify_precision(total_fees).to_f
          transaction_parent = FinanceTransaction.new
          transaction_parent.title = "#{t('receipt_no')}. F#{orderId}"
          transaction_parent.category = FinanceTransactionCategory.find_by_name("Fee")
          transaction_parent.payee = @student
          #transaction_parent.finance = @financefee[f]
          transaction_parent.amount = total_fees
          transaction_parent.fine_included = false
          transaction_parent.fine_amount = 0.00
          transaction_parent.transaction_date = Date.today
          transaction_parent.payment_mode = "Online Payment"
          transaction_parent.save
        
          #abort(transaction_parent.inspect)
          if transaction_parent.save
            fees.each do |fee|
              f = fee.to_i
              #abort(request_params.inspect)
              unless @financefee[f].is_paid?
                unless amount_from_gateway.to_f < 0
                    unless amount_from_gateway.to_f > Champs21Precision.set_and_modify_precision(total_fees).to_f

                    transaction = FinanceTransaction.new
                    transaction.title = "#{t('receipt_no')}. F#{@financefee[f].id}"
                    transaction.category = FinanceTransactionCategory.find_by_name("Fee")
                    transaction.payee = @student
                    transaction.finance = @financefee[f]
                    transaction.amount = request_params["amount_to_pay_#{f.to_s}"]
                    transaction.fine_included = (@fine[f].to_f + @fine_amount[f].to_f).zero? ? false : true
                    transaction.fine_amount = @fine[f].to_f + @fine_amount[f].to_f
                    transaction.transaction_date = Date.today
                    transaction.payment_mode = "Online Payment"
                    transaction.is_child_transaction = true
                    transaction.parent_transaction_id = transaction_parent.id
                    transaction.save
                    if transaction.save
                      total_fine_amount = 0
                      unless (@fine[f].to_f + @fine_amount[f].to_f).zero?
                        total_fine_amount = @fine[f].to_f + @fine_amount[f].to_f
                      end
                      is_paid =@financefee[f].balance==0 ? true : false
                      @financefee[f].update_attributes( :is_paid=>is_paid)

                      proccess_particulars_category = []
                      loop_particular = 0
                      unless request_params.nil?
                        @fee_particulars[f].each do |fp|
                          advanced = false
                          particular_amount = fp.amount.to_f
                          unless request_params["fee_particular_" + fp.id.to_s + "_" + f.to_s].nil?
                            if request_params["fee_particular_" + fp.id.to_s + "_" + f.to_s] == "on"
                              paid_amount = request_params["fee_particular_amount_" + fp.id.to_s + "_" + f.to_s].to_f
                              left_amount = particular_amount - paid_amount
                              amount_paid = 0
                              if  left_amount == 0
                                amount_paid = particular_amount
                              elsif  left_amount < 0
                                advanced = true
                                amount_paid = particular_amount
                              elsif left_amount > 0
                                amount_paid = paid_amount
                              end
                              
                              finance_transaction_particular = FinanceTransactionParticular.new
                              finance_transaction_particular.finance_transaction_id = transaction.id
                              finance_transaction_particular.particular_id = fp.id
                              finance_transaction_particular.particular_type = 'Particular'
                              finance_transaction_particular.transaction_type = 'Fee Collection'
                              finance_transaction_particular.amount = amount_paid
                              finance_transaction_particular.transaction_date = transaction.transaction_date
                              finance_transaction_particular.save

                              if advanced
                                left_amount = paid_amount - particular_amount
                                finance_transaction_particular = FinanceTransactionParticular.new
                                finance_transaction_particular.finance_transaction_id = transaction.id
                                finance_transaction_particular.particular_id = fp.id
                                finance_transaction_particular.particular_type = 'Particular'
                                finance_transaction_particular.transaction_type = 'Advance'
                                finance_transaction_particular.amount = left_amount
                                finance_transaction_particular.transaction_date = transaction.transaction_date
                                finance_transaction_particular.save
                              end
                            end
                          end
                        end

                        unless @onetime_discounts[f].blank?
                          @onetime_discounts[f].each do |od|
                            unless request_params["fee_discount_" + od.id.to_s + "_" + f.to_s].nil?
                              if request_params["fee_discount_" + od.id.to_s + "_" + f.to_s] == "on"
                                discount_amount = request_params["fee_discount_amount_" + od.id.to_s + "_" + f.to_s].to_f
                                finance_transaction_particular = FinanceTransactionParticular.new
                                finance_transaction_particular.finance_transaction_id = transaction.id
                                finance_transaction_particular.particular_id = od.id
                                finance_transaction_particular.particular_type = 'Adjustment'
                                finance_transaction_particular.transaction_type = 'Discount'
                                finance_transaction_particular.amount = discount_amount
                                finance_transaction_particular.transaction_date = transaction.transaction_date
                                finance_transaction_particular.save
                              end
                            end
                          end
                        end

                        unless @discounts[f].blank?
                          @discounts[f].each do |od|
                            unless request_params["fee_discount_" + od.id.to_s + "_" + f.to_s].nil?
                              if request_params["fee_discount_" + od.id.to_s + "_" + f.to_s] == "on"
                                discount_amount = request_params["fee_discount_amount_" + od.id.to_s + "_" + f.to_s].to_f
                                finance_transaction_particular = FinanceTransactionParticular.new
                                finance_transaction_particular.finance_transaction_id = transaction.id
                                finance_transaction_particular.particular_id = od.id
                                finance_transaction_particular.particular_type = 'Adjustment'
                                finance_transaction_particular.transaction_type = 'Discount'
                                finance_transaction_particular.amount = discount_amount
                                finance_transaction_particular.transaction_date = transaction.transaction_date
                                finance_transaction_particular.save
                              end
                            end
                          end
                        end

                        unless request_params["fee_vat" + "_" + f.to_s].nil?
                          if request_params["fee_vat" + "_" + f.to_s] == "on"
                            vat_amount = request_params["fee_vat_amount" + "_" + f.to_s].to_f
                            finance_transaction_particular = FinanceTransactionParticular.new
                            finance_transaction_particular.finance_transaction_id = transaction.id
                            finance_transaction_particular.particular_id = 0
                            finance_transaction_particular.particular_type = 'VAT'
                            finance_transaction_particular.transaction_type = ''
                            finance_transaction_particular.amount = vat_amount
                            finance_transaction_particular.transaction_date = transaction.transaction_date
                            finance_transaction_particular.save
                          end
                        end

                        unless request_params["fee_fine" + "_" + f.to_s].nil?
                          if request_params["fee_fine" + "_" + f.to_s] == "on"
                            fine_amount = request_params["fine_amount_to_pay" + "_" + f.to_s].to_f
                            finance_transaction_particular = FinanceTransactionParticular.new
                            finance_transaction_particular.finance_transaction_id = transaction.id
                            finance_transaction_particular.particular_id = 0
                            finance_transaction_particular.particular_type = 'Fine'
                            finance_transaction_particular.transaction_type = ''
                            finance_transaction_particular.amount = fine_amount
                            finance_transaction_particular.transaction_date = transaction.transaction_date
                            finance_transaction_particular.save
                          end
                        end

                        if @has_fine_discount[f]
                          @discounts_on_lates[f].each do |fd|
                            unless request_params["fee_fine_discount_" + fd.id.to_s + "_" + f.to_s].nil?
                              if request_params["fee_fine_discount_" + fd.id.to_s + "_" + f.to_s] == "on"
                                discount_amount = request_params["fee_fine_discount_amount_" + fd.id.to_s + "_" + f.to_s].to_f
                                finance_transaction_particular = FinanceTransactionParticular.new
                                finance_transaction_particular.finance_transaction_id = transaction.id
                                finance_transaction_particular.particular_id = fd.id
                                finance_transaction_particular.particular_type = 'FineAdjustment'
                                finance_transaction_particular.transaction_type = 'Discount'
                                finance_transaction_particular.amount = discount_amount
                                finance_transaction_particular.transaction_date = transaction.transaction_date
                                finance_transaction_particular.save
                              end
                            end
                          end
                        end


                        @finance_order = FinanceOrder.find_by_order_id_and_finance_fee_id(orderId, f)
                        #@finance_order[f] = FinanceOrder.find_by_order_id(orderId)
                        @finance_order.update_attributes(:status => 1)

                      else
                        @fee_particulars[f].each do |fp|
                          particular_amount = fp.amount.to_f
                          finance_transaction_particular = FinanceTransactionParticular.new
                          finance_transaction_particular.finance_transaction_id = transaction.id
                          finance_transaction_particular.particular_id = fp.id
                          finance_transaction_particular.particular_type = 'Particular'
                          finance_transaction_particular.transaction_type = 'Fee Collection'
                          finance_transaction_particular.amount = particular_amount
                          finance_transaction_particular.transaction_date = transaction.transaction_date
                          finance_transaction_particular.save
                        end

                        unless @onetime_discounts[f].blank?
                          @onetime_discounts[f].each do |od|
                            discount_amount = @onetime_discounts_amount[f][od.id].to_f
                            finance_transaction_particular = FinanceTransactionParticular.new
                            finance_transaction_particular.finance_transaction_id = transaction.id
                            finance_transaction_particular.particular_id = od.id
                            finance_transaction_particular.particular_type = 'Adjustment'
                            finance_transaction_particular.transaction_type = 'Discount'
                            finance_transaction_particular.amount = discount_amount
                            finance_transaction_particular.transaction_date = transaction.transaction_date
                            finance_transaction_particular.save
                          end
                        end


                        unless @discounts[f].blank?
                          @discounts[f].each do |od|
                            discount_amount = @discounts_amount[f][od.id]
                            finance_transaction_particular = FinanceTransactionParticular.new
                            finance_transaction_particular.finance_transaction_id = transaction.id
                            finance_transaction_particular.particular_id = od.id
                            finance_transaction_particular.particular_type = 'Adjustment'
                            finance_transaction_particular.transaction_type = 'Discount'
                            finance_transaction_particular.amount = discount_amount
                            finance_transaction_particular.transaction_date = transaction.transaction_date
                            finance_transaction_particular.save
                          end
                        end

                        if transaction.vat_included?
                          vat_amount = transaction.vat_amount
                          finance_transaction_particular = FinanceTransactionParticular.new
                          finance_transaction_particular.finance_transaction_id = transaction.id
                          finance_transaction_particular.particular_id = 0
                          finance_transaction_particular.particular_type = 'VAT'
                          finance_transaction_particular.transaction_type = ''
                          finance_transaction_particular.amount = vat_amount
                          finance_transaction_particular.transaction_date = transaction.transaction_date
                          finance_transaction_particular.save
                        end

                        if total_fine_amount
                          fine_amount = total_fine_amount
                          finance_transaction_particular = FinanceTransactionParticular.new
                          finance_transaction_particular.finance_transaction_id = transaction.id
                          finance_transaction_particular.particular_id = 0
                          finance_transaction_particular.particular_type = 'Fine'
                          finance_transaction_particular.transaction_type = ''
                          finance_transaction_particular.amount = fine_amount
                          finance_transaction_particular.transaction_date = transaction.transaction_date
                          finance_transaction_particular.save
                        end


                        if @has_fine_discount[f]
                          @discounts_on_lates[f].each do |fd|
                            discount_amount = @discounts_late_amount[f][od.id]
                            discount_amount = params["fee_fine_discount_amount_" + fd.id.to_s].to_f
                            finance_transaction_particular = FinanceTransactionParticular.new
                            finance_transaction_particular.finance_transaction_id = transaction.id
                            finance_transaction_particular.particular_id = fd.id
                            finance_transaction_particular.particular_type = 'FineAdjustment'
                            finance_transaction_particular.transaction_type = 'Discount'
                            finance_transaction_particular.amount = discount_amount
                            finance_transaction_particular.transaction_date = transaction.transaction_date
                            finance_transaction_particular.save
                          end
                        end
                      end
                    end

                    payment = Payment.find_by_order_id_and_payee_id_and_payment_id(orderId, @student.id, f)
                    #abort(payment.inspect)
                    payment.update_attributes(:finance_transaction_id => transaction.id)
                    unless @financefee[f].transaction_id.nil?
                      tid =   @financefee[f].transaction_id.to_s + ",#{transaction.id}"
                    else
                      tid=transaction.id
                    end
                    is_paid = @financefee[f].balance==0 ? true : false



                    @financefee[f].update_attributes(:transaction_id=>tid, :is_paid=>is_paid)
                    #@paid_fees = FinanceTransaction.find(:all,:conditions=>"FIND_IN_SET(id,\"#{tid}\")")
                    online_transaction_id = payment.gateway_response[:transaction_id]
                    online_transaction_id ||= payment.gateway_response[:x_trans_id]

                    g_data = Guardian.find_by_user_id(current_user.id);
                    if !g_data.blank? && !g_data.email.blank?
                      header_txt = "#{t('payment_success')} #{online_transaction_id}"
                      body_txt = render_to_string(:template => 'gateway_payments/paypal/student_fee_receipt', :layout => false)
                      champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
                      api_endpoint = champs21_api_config['api_url']
                      form_data = {}
                      form_data['body'] = body_txt
                      form_data['header'] = header_txt
                      form_data['email'] = g_data.email
                      form_data['first_name'] = g_data.first_name
                      form_data['last_name'] = g_data.last_name

                      api_uri = URI(api_endpoint + "api/user/paymentmail")


                      http = Net::HTTP.new(api_uri.host, api_uri.port)
                      request = Net::HTTP::Post.new(api_uri.path)
                      request.set_form_data(form_data)
                      http.request(request)
                    end 


                    sms_setting = SmsSetting.new()
                    if sms_setting.student_sms_active or sms_setting.parent_sms_active    
                      message = "Fees received BDT #AMOUNT# for #UNAME#(#UID#) as on #PAIDDATE# by TBL. TranID-#TRANID# TranRef-#TRANREF#, Sender - SAGC"
                      if File.exists?("#{Rails.root}/config/sms_text_#{MultiSchool.current_school.id}.yml")
                        sms_text_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/sms_text_#{MultiSchool.current_school.id}.yml")['school']
                        message = sms_text_config['feepaid']
                      end
                      recipients = []
                      unless @student.sms_number.nil? or @student.sms_number.empty? or @student.sms_number.blank?
                        message = message.gsub("#UNAME#", @student.full_name)
                        message = message.gsub("#UID#", @student.admission_no)
                        message = message.gsub("#AMOUNT#", amount_from_gateway.to_s)
                        message = message.gsub("#PAIDDATE#", trans_date.to_date.strftime("%d-%m-%Y"))
                        message = message.gsub("#TRANID#", orderId)
                        message = message.gsub("#TRANREF#", ref_id)
                        recipients.push @student.sms_number
                      else
                        unless @student.phone2.nil? or @student.phone2.empty? or @student.phone2.blank?
                          message = message
                          message = message.gsub("#UNAME#", @student.full_name)
                          message = message.gsub("#UID#", @student.admission_no)
                          message = message.gsub("#AMOUNT#", amount_from_gateway.to_s)
                          message = message.gsub("#PAIDDATE#", trans_date.to_date.strftime("%d-%m-%Y"))
                          message = message.gsub("#TRANID#", orderId)
                          message = message.gsub("#TRANREF#", ref_id)
                          recipients.push @student.phone2
                        end
                      end
                      messages = []
                      messages[0] = message
                      #sms = Delayed::Job.enqueue(SmsManager.new(message,recipients))
                      send_sms(messages,recipients)
                    end


                    flash[:success] = "Thanks for your payment, payment was Successfull. Your order ID is: #{orderId}"
                  else
                    flash[:notice] = "#{t('payment_failed')}"
                  end
                else
                  flash[:notice] = "#{t('payment_failed')}"
                end
              else
                flash[:notice] = "#{t('flash_payed')}"
              end

            end
          end
        end
      end
    end
    
    def pay_student(amount_from_gateway, total_fees, request_params, orderId, trans_date, ref_id)
      unless @financefee.is_paid?
        unless amount_from_gateway.to_f < 0
            unless amount_from_gateway.to_f > Champs21Precision.set_and_modify_precision(total_fees).to_f

            transaction = FinanceTransaction.new
            transaction.title = "#{t('receipt_no')}. F#{@financefee.id}"
            transaction.category = FinanceTransactionCategory.find_by_name("Fee")
            transaction.payee = @student
            transaction.finance = @financefee
            transaction.amount = total_fees
            transaction.fine_included = (@fine.to_f + @fine_amount.to_f).zero? ? false : true
            transaction.fine_amount = @fine.to_f + @fine_amount.to_f
            transaction.transaction_date = Date.today
            transaction.payment_mode = "Online Payment"
            transaction.save
            if transaction.save
              total_fine_amount = 0
              unless (@fine.to_f + @fine_amount.to_f).zero?
                total_fine_amount = @fine.to_f + @fine_amount.to_f
              end
              is_paid =@financefee.balance==0 ? true : false
              @financefee.update_attributes( :is_paid=>is_paid)

              @paid_fees = @financefee.finance_transactions

              proccess_particulars_category = []
              loop_particular = 0
              unless request_params.nil?
                @fee_particulars.each do |fp|
                  advanced = false
                  particular_amount = fp.amount.to_f
                  unless request_params["fee_particular_" + fp.id.to_s].nil?
                    if request_params["fee_particular_" + fp.id.to_s] == "on"
                      paid_amount = request_params["fee_particular_amount_" + fp.id.to_s].to_f
                      left_amount = particular_amount - paid_amount
                      amount_paid = 0
                      if  left_amount == 0
                        amount_paid = particular_amount
                      elsif  left_amount < 0
                        advanced = true
                        amount_paid = particular_amount
                      elsif left_amount > 0
                        amount_paid = paid_amount
                      end
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = fp.id
                      finance_transaction_particular.particular_type = 'Particular'
                      finance_transaction_particular.transaction_type = 'Fee Collection'
                      finance_transaction_particular.amount = amount_paid
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save

                      if advanced
                        left_amount = paid_amount - particular_amount
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = fp.id
                        finance_transaction_particular.particular_type = 'Particular'
                        finance_transaction_particular.transaction_type = 'Advance'
                        finance_transaction_particular.amount = left_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      end
                    else
                      particular_amount = fp.amount.to_f
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = fp.id
                      finance_transaction_particular.particular_type = 'Particular'
                      finance_transaction_particular.transaction_type = 'Fee Collection'
                      finance_transaction_particular.amount = particular_amount
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save
                    end
                  else
                    particular_amount = fp.amount.to_f
                    finance_transaction_particular = FinanceTransactionParticular.new
                    finance_transaction_particular.finance_transaction_id = transaction.id
                    finance_transaction_particular.particular_id = fp.id
                    finance_transaction_particular.particular_type = 'Particular'
                    finance_transaction_particular.transaction_type = 'Fee Collection'
                    finance_transaction_particular.amount = particular_amount
                    finance_transaction_particular.transaction_date = transaction.transaction_date
                    finance_transaction_particular.save
                  end
                end

                unless @onetime_discounts.blank?
                  @onetime_discounts.each do |od|
                    unless request_params["fee_discount_" + od.id.to_s].nil?
                      if request_params["fee_discount_" + od.id.to_s] == "on"
                        discount_amount = request_params["fee_discount_amount_" + od.id.to_s].to_f
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = od.id
                        finance_transaction_particular.particular_type = 'Adjustment'
                        finance_transaction_particular.transaction_type = 'Discount'
                        finance_transaction_particular.amount = discount_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      else
                        discount_amount = @onetime_discounts_amount[od.id].to_f
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = od.id
                        finance_transaction_particular.particular_type = 'Adjustment'
                        finance_transaction_particular.transaction_type = 'Discount'
                        finance_transaction_particular.amount = discount_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      end
                    else
                      discount_amount = @onetime_discounts_amount[od.id].to_f
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = od.id
                      finance_transaction_particular.particular_type = 'Adjustment'
                      finance_transaction_particular.transaction_type = 'Discount'
                      finance_transaction_particular.amount = discount_amount
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save
                    end
                  end
                end

                unless @discounts.blank?
                  @discounts.each do |od|
                    unless request_params["fee_discount_" + od.id.to_s].nil?
                      if request_params["fee_discount_" + od.id.to_s] == "on"
                        discount_amount = request_params["fee_discount_amount_" + od.id.to_s].to_f
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = od.id
                        finance_transaction_particular.particular_type = 'Adjustment'
                        finance_transaction_particular.transaction_type = 'Discount'
                        finance_transaction_particular.amount = discount_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      else
                        discount_amount = @discounts_amount[od.id]
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = od.id
                        finance_transaction_particular.particular_type = 'Adjustment'
                        finance_transaction_particular.transaction_type = 'Discount'
                        finance_transaction_particular.amount = discount_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      end
                    else
                      discount_amount = @discounts_amount[od.id]
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = od.id
                      finance_transaction_particular.particular_type = 'Adjustment'
                      finance_transaction_particular.transaction_type = 'Discount'
                      finance_transaction_particular.amount = discount_amount
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save
                    end
                  end
                end

                unless request_params[:fee_vat].nil?
                  if request_params[:fee_vat] == "on"
                    vat_amount = request_params[:fee_vat_amount].to_f
                    finance_transaction_particular = FinanceTransactionParticular.new
                    finance_transaction_particular.finance_transaction_id = transaction.id
                    finance_transaction_particular.particular_id = 0
                    finance_transaction_particular.particular_type = 'VAT'
                    finance_transaction_particular.transaction_type = ''
                    finance_transaction_particular.amount = vat_amount
                    finance_transaction_particular.transaction_date = transaction.transaction_date
                    finance_transaction_particular.save
                  else
                    if transaction.vat_included?
                      vat_amount = transaction.vat_amount
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = 0
                      finance_transaction_particular.particular_type = 'VAT'
                      finance_transaction_particular.transaction_type = ''
                      finance_transaction_particular.amount = vat_amount
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save
                    end
                  end
                else
                  if transaction.vat_included?
                    vat_amount = transaction.vat_amount
                    finance_transaction_particular = FinanceTransactionParticular.new
                    finance_transaction_particular.finance_transaction_id = transaction.id
                    finance_transaction_particular.particular_id = 0
                    finance_transaction_particular.particular_type = 'VAT'
                    finance_transaction_particular.transaction_type = ''
                    finance_transaction_particular.amount = vat_amount
                    finance_transaction_particular.transaction_date = transaction.transaction_date
                    finance_transaction_particular.save
                  end
                end

                unless request_params[:fee_fine].nil?
                  if request_params[:fee_fine] == "on"
                    fine_amount = request_params[:fine_amount_to_pay].to_f
                    finance_transaction_particular = FinanceTransactionParticular.new
                    finance_transaction_particular.finance_transaction_id = transaction.id
                    finance_transaction_particular.particular_id = 0
                    finance_transaction_particular.particular_type = 'Fine'
                    finance_transaction_particular.transaction_type = ''
                    finance_transaction_particular.amount = fine_amount
                    finance_transaction_particular.transaction_date = transaction.transaction_date
                    finance_transaction_particular.save
                  else
                    if total_fine_amount
                      fine_amount = total_fine_amount
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = 0
                      finance_transaction_particular.particular_type = 'Fine'
                      finance_transaction_particular.transaction_type = ''
                      finance_transaction_particular.amount = fine_amount
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save
                    end
                  end
                else
                  if total_fine_amount
                    fine_amount = total_fine_amount
                    finance_transaction_particular = FinanceTransactionParticular.new
                    finance_transaction_particular.finance_transaction_id = transaction.id
                    finance_transaction_particular.particular_id = 0
                    finance_transaction_particular.particular_type = 'Fine'
                    finance_transaction_particular.transaction_type = ''
                    finance_transaction_particular.amount = fine_amount
                    finance_transaction_particular.transaction_date = transaction.transaction_date
                    finance_transaction_particular.save
                  end
                end

                if @has_fine_discount
                  @discounts_on_lates.each do |fd|
                    unless request_params["fee_fine_discount_" + fd.id.to_s].nil?
                      if request_params["fee_fine_discount_" + fd.id.to_s] == "on"
                        discount_amount = request_params["fee_fine_discount_amount_" + fd.id.to_s].to_f
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = fd.id
                        finance_transaction_particular.particular_type = 'FineAdjustment'
                        finance_transaction_particular.transaction_type = 'Discount'
                        finance_transaction_particular.amount = discount_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      else
                        discount_amount = @discounts_late_amount[od.id]
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = fd.id
                        finance_transaction_particular.particular_type = 'FineAdjustment'
                        finance_transaction_particular.transaction_type = 'Discount'
                        finance_transaction_particular.amount = discount_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      end
                    else
                      discount_amount = @discounts_late_amount[od.id]
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = fd.id
                      finance_transaction_particular.particular_type = 'FineAdjustment'
                      finance_transaction_particular.transaction_type = 'Discount'
                      finance_transaction_particular.amount = discount_amount
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save
                    end
                  end
                end


                @finance_order = FinanceOrder.find_by_order_id(orderId)
                @finance_order.update_attributes(:status => 1)

              else
                @fee_particulars.each do |fp|
                  particular_amount = fp.amount.to_f
                  finance_transaction_particular = FinanceTransactionParticular.new
                  finance_transaction_particular.finance_transaction_id = transaction.id
                  finance_transaction_particular.particular_id = fp.id
                  finance_transaction_particular.particular_type = 'Particular'
                  finance_transaction_particular.transaction_type = 'Fee Collection'
                  finance_transaction_particular.amount = particular_amount
                  finance_transaction_particular.transaction_date = transaction.transaction_date
                  finance_transaction_particular.save
                end

                unless @onetime_discounts.blank?
                  @onetime_discounts.each do |od|
                    discount_amount = @onetime_discounts_amount[od.id].to_f
                    finance_transaction_particular = FinanceTransactionParticular.new
                    finance_transaction_particular.finance_transaction_id = transaction.id
                    finance_transaction_particular.particular_id = od.id
                    finance_transaction_particular.particular_type = 'Adjustment'
                    finance_transaction_particular.transaction_type = 'Discount'
                    finance_transaction_particular.amount = discount_amount
                    finance_transaction_particular.transaction_date = transaction.transaction_date
                    finance_transaction_particular.save
                  end
                end


                unless @discounts.blank?
                  @discounts.each do |od|
                    discount_amount = @discounts_amount[od.id]
                    finance_transaction_particular = FinanceTransactionParticular.new
                    finance_transaction_particular.finance_transaction_id = transaction.id
                    finance_transaction_particular.particular_id = od.id
                    finance_transaction_particular.particular_type = 'Adjustment'
                    finance_transaction_particular.transaction_type = 'Discount'
                    finance_transaction_particular.amount = discount_amount
                    finance_transaction_particular.transaction_date = transaction.transaction_date
                    finance_transaction_particular.save
                  end
                end

                if transaction.vat_included?
                  vat_amount = transaction.vat_amount
                  finance_transaction_particular = FinanceTransactionParticular.new
                  finance_transaction_particular.finance_transaction_id = transaction.id
                  finance_transaction_particular.particular_id = 0
                  finance_transaction_particular.particular_type = 'VAT'
                  finance_transaction_particular.transaction_type = ''
                  finance_transaction_particular.amount = vat_amount
                  finance_transaction_particular.transaction_date = transaction.transaction_date
                  finance_transaction_particular.save
                end

                if total_fine_amount
                  fine_amount = total_fine_amount
                  finance_transaction_particular = FinanceTransactionParticular.new
                  finance_transaction_particular.finance_transaction_id = transaction.id
                  finance_transaction_particular.particular_id = 0
                  finance_transaction_particular.particular_type = 'Fine'
                  finance_transaction_particular.transaction_type = ''
                  finance_transaction_particular.amount = fine_amount
                  finance_transaction_particular.transaction_date = transaction.transaction_date
                  finance_transaction_particular.save
                end


                if @has_fine_discount
                  @discounts_on_lates.each do |fd|
                    discount_amount = @discounts_late_amount[od.id]
                    finance_transaction_particular = FinanceTransactionParticular.new
                    finance_transaction_particular.finance_transaction_id = transaction.id
                    finance_transaction_particular.particular_id = fd.id
                    finance_transaction_particular.particular_type = 'FineAdjustment'
                    finance_transaction_particular.transaction_type = 'Discount'
                    finance_transaction_particular.amount = discount_amount
                    finance_transaction_particular.transaction_date = transaction.transaction_date
                    finance_transaction_particular.save
                  end
                end
              end
            end

            payment = Payment.find_by_order_id_and_payee_id_and_payment_id(orderId, @student.id, @financefee.id)
            payment.update_attributes(:finance_transaction_id => transaction.id)
            unless @financefee.transaction_id.nil?
              tid =   @financefee.transaction_id.to_s + ",#{transaction.id}"
            else
              tid=transaction.id
            end
            is_paid = @financefee.balance==0 ? true : false



            @financefee.update_attributes(:transaction_id=>tid, :is_paid=>is_paid)
            @paid_fees = FinanceTransaction.find(:all,:conditions=>"FIND_IN_SET(id,\"#{tid}\")")
            online_transaction_id = payment.gateway_response[:transaction_id]
            online_transaction_id ||= payment.gateway_response[:x_trans_id]

            g_data = Guardian.find_by_user_id(current_user.id);
            if !g_data.blank? && !g_data.email.blank?
              header_txt = "#{t('payment_success')} #{online_transaction_id}"
              body_txt = render_to_string(:template => 'gateway_payments/paypal/student_fee_receipt', :layout => false)
              champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
              api_endpoint = champs21_api_config['api_url']
              form_data = {}
              form_data['body'] = body_txt
              form_data['header'] = header_txt
              form_data['email'] = g_data.email
              form_data['first_name'] = g_data.first_name
              form_data['last_name'] = g_data.last_name

              api_uri = URI(api_endpoint + "api/user/paymentmail")


              http = Net::HTTP.new(api_uri.host, api_uri.port)
              request = Net::HTTP::Post.new(api_uri.path)
              request.set_form_data(form_data)
              http.request(request)
            end 


            sms_setting = SmsSetting.new()
            if sms_setting.student_sms_active or sms_setting.parent_sms_active    
              message = "Fees received BDT #AMOUNT# for #UNAME#(#UID#) as on #PAIDDATE# by TBL. TranID-#TRANID# TranRef-#TRANREF#, Sender - SAGC"
              if File.exists?("#{Rails.root}/config/sms_text_#{MultiSchool.current_school.id}.yml")
                sms_text_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/sms_text_#{MultiSchool.current_school.id}.yml")['school']
                message = sms_text_config['feepaid']
              end
              recipients = []
              unless @student.sms_number.nil? or @student.sms_number.empty? or @student.sms_number.blank?
                message = message.gsub("#UNAME#", @student.full_name)
                message = message.gsub("#UID#", @student.admission_no)
                message = message.gsub("#AMOUNT#", amount_from_gateway.to_s)
                message = message.gsub("#PAIDDATE#", trans_date.to_date.strftime("%d-%m-%Y"))
                message = message.gsub("#TRANID#", orderId)
                message = message.gsub("#TRANREF#", ref_id)
                recipients.push @student.sms_number
              else
                unless @student.phone2.nil? or @student.phone2.empty? or @student.phone2.blank?
                  message = message
                  message = message.gsub("#UNAME#", @student.full_name)
                  message = message.gsub("#UID#", @student.admission_no)
                  message = message.gsub("#AMOUNT#", amount_from_gateway.to_s)
                  message = message.gsub("#PAIDDATE#", trans_date.to_date.strftime("%d-%m-%Y"))
                  message = message.gsub("#TRANID#", orderId)
                  message = message.gsub("#TRANREF#", ref_id)
                  recipients.push @student.phone2
                end
              end
              messages = []
              messages[0] = message
              #sms = Delayed::Job.enqueue(SmsManager.new(message,recipients))
              send_sms(messages,recipients)
            end


            flash[:success] = "Thanks for your payment, payment was Successfull. Your order ID is: #{orderId}"
          else
            flash[:notice] = "#{t('payment_failed')}"
          end
        else
          flash[:notice] = "#{t('payment_failed')}"
        end
      else
        flash[:notice] = "#{t('flash_payed')}"
      end
    end
  
  def get_class_test_report(batch_id = 0)
    require 'net/http'
    require 'uri'
    require "yaml"
    
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

   
    
    if current_user.student
      homework_uri = URI(api_endpoint + "api/report/classtestreport")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"batch_id" => params[:batch_id] ,"exam_group" =>params[:exam_group],"call_from_web"=>1,"no_exams"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @class_test_report_data = JSON::parse(homework_res.body)
    end
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      homework_uri = URI(api_endpoint + "api/report/classtestreport")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"batch_id" => params[:batch_id] ,"exam_group" =>params[:exam_group],"batch_id"=>student.batch_id,"no_exams"=>1,"student_id"=>student.id,"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @class_test_report_data = JSON::parse(homework_res.body)
    end
    
    @class_test_report_data
  end
  
  def get_attendence_text
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    
    if current_user.student or current_user.employee
      homework_uri = URI(api_endpoint + "api/report/attendence")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @attendence_text = JSON::parse(homework_res.body)
    end
    
    if current_user.parent
      target = current_user.guardian_entry.current_ward_id      
      student = Student.find_by_id(target)
      homework_uri = URI(api_endpoint + "api/report/attendence")
      http = Net::HTTP.new(homework_uri.host, homework_uri.port)
      homework_req = Net::HTTP::Post.new(homework_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      homework_req.set_form_data({"batch_id"=>student.batch_id,"student_id"=>student.id,"call_from_web"=>1,"user_secret" => session[:api_info][0]['user_secret']})

      homework_res = http.request(homework_req)
      @attendence_text = JSON::parse(homework_res.body)
    end
    
    @attendence_text
  end
  
  def send_guardian_notification
    @applied_student = @formData.student
    reminderrecipients = []
    batch_ids = {}
    student_ids = {}
    
    unless @applied_student.student_guardian.empty?
      guardians = @applied_student.student_guardian
      guardians.each do |guardian|

        unless guardian.user_id.nil?
          reminderrecipients.push guardian.user_id
          batch_ids[guardian.user_id] = @applied_student.batch_id
          student_ids[guardian.user_id] = @applied_student.id
        end
      end  
    end
    
    if @formData.form_type_text == "transfer_letter" 
      title = "Transfer Certificate"
    elsif @formData.form_type_text == "noc_letter" 
      title = "No Objection Certificate"
    elsif @formData.form_type_text == "recommendation_letter"
      title = "Recommendation Letter"
    elsif @formData.form_type_text == "studentship_letter" 
      title = "Studentship Certificate"
    elsif @formData.form_type_text == "visa_recommendation_letter" 
      title = "VISA Recommendation Letter"
    end 
    
    unless reminderrecipients.nil?
      Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
          :recipient_ids => reminderrecipients,
          :subject=>"Your application status for "+title+" is updated",
          :rtype=>1000,
          :rid=>@formData.id,
          :student_id => student_ids,
          :batch_id => batch_ids,
          :body=>"Your application status for "+title+" is updated. please check student profile form." ))
    end
    
  end
  
  def forwarder_notification(id)
    available_user_ids = []
    employee = Employee.find_by_id(id.to_i)
    available_user_ids << employee.user_id
    title = ""
    if @formData.form_type_text == "transfer_letter" 
      title = "Transfer Certificate"
    elsif @formData.form_type_text == "noc_letter" 
      title = "No Objection Certificate"
    elsif @formData.form_type_text == "recommendation_letter"
      title = "Recommendation Letter"
    elsif @formData.form_type_text == "studentship_letter" 
      title = "Studentship Certificate"
    elsif @formData.form_type_text == "visa_recommendation_letter" 
      title = "VISA Recommendation Letter"
    end
    
    unless available_user_ids.nil?
      Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
          :recipient_ids => available_user_ids,
          :subject=>title+" Need Your Action",
          :rtype=>1000,
          :rid=>@formData.id,
          :body=>title+" is forwarded to you. Need your action" ))
    end
    
  end
  
  def send_notification_section_manager_update
    batch = @formData.student.batch       
    unless batch.blank?
      batch_tutor = batch.employees
      available_user_ids = []
      unless batch_tutor.blank?
        batch_tutor.each do |employee|
          if employee.meeting_forwarder == 1
            available_user_ids << employee.user_id
          end
        end
      end
    end
    
     
    if @formData.form_type_text == "transfer_letter" 
      title = "Transfer Certificate"
    elsif @formData.form_type_text == "noc_letter" 
      title = "No Objection Certificate"
    elsif @formData.form_type_text == "recommendation_letter"
      title = "Recommendation Letter"
    elsif @formData.form_type_text == "studentship_letter" 
      title = "Studentship Certificate"
    elsif @formData.form_type_text == "visa_recommendation_letter" 
      title = "VISA Recommendation Letter"
    end 
        
    
    unless available_user_ids.nil?
      Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
          :recipient_ids => available_user_ids,
          :subject=>title+" Status Updated",
          :rtype=>1000,
          :rid=>@formData.id,
          :body=>title+" Status Updated. Need your action" ))
    end
    
    
  end
  
  def send_notification_section_manager
    batch = @studentForm.student.batch       
    unless batch.blank?
      batch_tutor = batch.employees
      available_user_ids = []
      unless batch_tutor.blank?
        batch_tutor.each do |employee|
          if employee.meeting_forwarder == 1
            available_user_ids << employee.user_id
          end
        end
      end
    end
    
     
    if @studentForm.form_type_text == "transfer_letter" 
      title = "Transfer Certificate"
    elsif @studentForm.form_type_text == "noc_letter" 
      title = "No Objection Certificate"
    elsif @studentForm.form_type_text == "recommendation_letter"
      title = "Recommendation Letter"
    elsif @studentForm.form_type_text == "studentship_letter" 
      title = "Studentship Certificate"
    elsif @studentForm.form_type_text == "visa_recommendation_letter" 
      title = "VISA Recommendation Letter"
    end 
        
    
    unless available_user_ids.nil?
      Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
          :recipient_ids => available_user_ids,
          :subject=>"New "+title+" Submitted",
          :rtype=>1000,
          :rid=>@studentForm.id,
          :body=>"New "+title+" Submitted from #{@current_user.guardian_entry.first_name}. Need your action" ))
    end
    
    
  end
  
  def send_sms_student(student_ids,message,sent_to)
    @conn = ActiveRecord::Base.connection
    
    row_header = ['Mobile No','Message']
    csv = true
    if MultiSchool.current_school.id == 352
      row_header = ['start','']
      csv = false
    end
    @recipients=[]
    i = 0
    tmp_message = []
    sms_setting = SmsSetting.new()
    
    if sent_to.to_i != 3
      sql = "SELECT s.first_name, s.middle_name, s.last_name, s.sms_number, s.phone2, fu.paid_username,fu.paid_password FROM students as s left join tds_free_users as fu on s.user_id=fu.paid_id where fu.paid_school_id=#{MultiSchool.current_school.id} and s.id IN (#{student_ids.join(',')}) and s.is_deleted = 0"
      student_data = @conn.execute(sql).all_hashes
    end
    
    if sent_to.to_i == 1 or sent_to.to_i == 3
      guardians = Guardian.find(:all, :conditions => "ward_id IN (#{student_ids.join(',')})").map(&:user_id)
      
      if MultiSchool.current_school.id == 352 or MultiSchool.current_school.id == 346
        sql = "SELECT g.first_name, g.last_name, s.sms_number, g.mobile_phone,fu.paid_username,fu.paid_password FROM guardians as g INNER join students s ON s.id = g.ward_id left join tds_free_users as fu on g.user_id=fu.paid_id where fu.paid_school_id=#{MultiSchool.current_school.id} and fu.paid_id IN (#{guardians.join(',')}) and fu.paid_username LIKE '%p1%' and s.is_deleted = 0" 
      else
        sql = "SELECT g.first_name, g.last_name, s.sms_number, g.mobile_phone,fu.paid_username,fu.paid_password FROM guardians as g INNER join students s ON s.id = g.ward_id left join tds_free_users as fu on g.user_id=fu.paid_id where fu.paid_school_id=#{MultiSchool.current_school.id} and fu.paid_id IN (#{guardians.join(',')}) and s.is_deleted = 0"
      end
      guardians_data = @conn.execute(sql).all_hashes
      
    end
    
    if sent_to.to_i != 3
      student_data.each do |s|
        full_name = "#{s["first_name"]} #{s["middle_name"]} #{s["last_name"]}"
        full_name.gsub("  "," ")
        full_name.gsub("- ","-")
        full_name.gsub(" -","-")
        user_name = s['paid_username']
        password = s['paid_password']
        unless s['sms_number'].nil? or s['sms_number'].empty? or s['sms_number'].blank?
          tmp_message[i] = message
          tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
          tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
          tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
          i += 1
          @recipients.push s['sms_number']
        else
          unless s['phone2'].nil? or s['phone2'].empty? or s['phone2'].blank?
            tmp_message[i] = message
            tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
            tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
            tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
            i += 1
            @recipients.push s['phone2']
          end
        end
      end
    end
    
    if sent_to.to_i == 1 or sent_to.to_i == 3
      guardians_data.each do |g|
        full_name = "#{g["first_name"]} #{g["last_name"]}"
        full_name.gsub("  "," ")
        full_name.gsub("- ","-")
        full_name.gsub(" -","-")
        user_name = g['paid_username']
        password = g['paid_password']
        unless g['sms_number'].nil? or g['sms_number'].empty? or g['sms_number'].blank?
          tmp_message[i] = message
          tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
          tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
          tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
          i += 1
          @recipients.push g['sms_number']
        else
          unless g['mobile_phone'].nil? or g['mobile_phone'].empty? or g['mobile_phone'].blank?
            tmp_message[i] = message
            tmp_message[i] = tmp_message[i].gsub("#NAME#", full_name)
            tmp_message[i] = tmp_message[i].gsub("#UNAME#", user_name)
            tmp_message[i] = tmp_message[i].gsub("#PASSWORD#", password)
            i += 1
            @recipients.push g['mobile_phone']
          end
        end
      end
    end
    
    unless @recipients.empty?
      #sms = Delayed::Job.enqueue(SmsManagerIndividualMessage.new(tmp_message,@recipients)) 
      send_sms(tmp_message,@recipients)
    end
  end
  
  def send_sms(multi_message, recipients)
    @recipients = recipients.map{|r| r.gsub(' ','')}
    @multi_message = multi_message
    @config = SmsSetting.get_sms_config
    unless @config.blank?
      @sendername = @config['sms_settings']['sendername']
      @sms_url = @config['sms_settings']['host_url']
      @username = @config['sms_settings']['username']
      @password = @config['sms_settings']['password']
      @success_code = @config['sms_settings']['success_code']
      @username_mapping = @config['parameter_mappings']['username']
      @username_mapping ||= 'username'
      @password_mapping = @config['parameter_mappings']['password']
      @password_mapping ||= 'password'
      @phone_mapping = @config['parameter_mappings']['phone']
      @phone_mapping ||= 'phone'
      @sender_mapping = @config['parameter_mappings']['sendername']
      @sender_mapping ||= 'sendername'
      @message_mapping = @config['parameter_mappings']['message']
      @message_mapping ||= 'message'
      unless @config['additional_parameters'].blank?
        @additional_param = ""
        @config['additional_parameters'].split(',').each do |param|
          @additional_param += "&#{param}"
        end
      end
    end

    if @config.present?
      @sms_hash = {"user"=>@username,"pass"=>@password,"sid" =>@sendername}
     
      i = 0
      @i_sms_loop = 0
      @recipients.each do |recipient|
       message = @multi_message[i]
       message_escape = CGI::escape message
       if @i_sms_loop == 3
         message_log = SmsMessage.new(:body=> message_escape)
         message_log.save
         message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>"Successfull")
         @sms_hash["sms[#{@i_sms_loop}][0]"] = recipient
         @sms_hash["sms[#{@i_sms_loop}][1]"] = message
         @sms_hash["sms[#{@i_sms_loop}][2]"] = @i_sms_loop
        
         api_uri = URI.parse(@sms_url)
         http = Net::HTTP.new(api_uri.host, api_uri.port)
         request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
         request.set_form_data(@sms_hash)
         
         http.request(request)
        
         sms_count = Configuration.find_by_config_key("TotalSmsCount")
         new_count = sms_count.config_value.to_i + 4
         sms_count.update_attributes(:config_value=>new_count)
         
         @sms_hash = {"user"=>@username,"pass"=>@password,"sid" =>@sendername}
       
         @i_sms_loop = 0
       elsif recipient.equal? @recipients.last
         message_log = SmsMessage.new(:body=> message_escape)
         message_log.save
         message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>"Successfull")
         @sms_hash["sms[#{@i_sms_loop}][0]"] = recipient
         @sms_hash["sms[#{@i_sms_loop}][1]"] = message
         @sms_hash["sms[#{@i_sms_loop}][2]"] = @i_sms_loop
         
         api_uri = URI.parse(@sms_url)
         http = Net::HTTP.new(api_uri.host, api_uri.port)
         request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
         request.set_form_data(@sms_hash)
         http.request(request)
        
         sms_count = Configuration.find_by_config_key("TotalSmsCount")
         new_count = sms_count.config_value.to_i + 1+@i_sms_loop
         sms_count.update_attributes(:config_value=>new_count)
       else
         @sms_hash["sms[#{@i_sms_loop}][0]"] = recipient
         @sms_hash["sms[#{@i_sms_loop}][1]"] = message
         @sms_hash["sms[#{@i_sms_loop}][2]"] = @i_sms_loop
         message_log = SmsMessage.new(:body=> message_escape)
         message_log.save
         message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>"Successfull")
         @i_sms_loop = @i_sms_loop+1
       end   
       
       i += 1
      end
    end
  end
 
end
