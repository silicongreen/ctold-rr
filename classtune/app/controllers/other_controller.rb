class OtherController < ApplicationController
  include ActionView::Helpers::TextHelper
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time
  
  
  def admit_card
    @batches=Batch.active.all(:include=>:course)
  end
  def bus_card
    @transports = Transport.find(:all,:conditions=>["receiver_type = ?","Student"])
    @student_ids = @transports.map(&:receiver_id)
    @students = Student.find_all_by_id(@student_ids,:include=>[{:batch=>[:course]}],:conditions=>["is_deleted = ?",false])
  end
  def print_bus_card_excell
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'bus_student_list'
    row_first = ['SL','Student Id','Roll No.','Name','Shift','Class','Section','Departure','Bus No','Fare','Mobile No']
    new_book.worksheet(0).insert_row(0, row_first)
    
    if request.post?
      unless params[:students].nil?
          @student_ids = params[:students]
          @students = Student.find_all_by_id(@student_ids,:include=>[{:batch=>[:course]}],:conditions=>["is_deleted = ?",false])
          std_ids = @students.map(&:id)
          @transports = Transport.find_all_by_receiver_id(std_ids,:conditions=>["receiver_type = ?","Student"],:include=>[:route,:vehicle])
          std_loop = 0
          @students.each do |std_info|
            std_loop = std_loop+1
            transport = @transports.find{|v| v.receiver_id == std_info.id}
            batchsplit = std_info.batch.name.split(" ")
            version = ""
            batch = batchsplit[0]
            unless batchsplit[1].blank?
              version = batchsplit[1]
            end
            unless batchsplit[2].blank?
              version = version+" "+batchsplit[2]
            end
          
            tmp_row = []
            tmp_row << std_loop
            tmp_row << std_info.admission_no
            tmp_row << std_info.class_roll_no
            tmp_row << std_info.full_name
            tmp_row << batch
            tmp_row << std_info.batch.course.course_name
            tmp_row << std_info.batch.course.section_name
            if transport.blank?
              tmp_row << ""
              tmp_row << ""
              tmp_row << ""
            else 
              tmp_row << transport.route.destination
              tmp_row << transport.vehicle.vehicle_no
              tmp_row << transport.bus_fare
            end
            tmp_row << std_info.sms_number
            new_book.worksheet(0).insert_row(std_loop, tmp_row)
          end
      end
      
      sheet1.add_header("SHAHEED BIR UTTAM LT. ANWAR GIRLS' COLLEGE (Bus Student List)")
      spreadsheet = StringIO.new 
      new_book.write spreadsheet 
      send_data spreadsheet.string, :filename => "Bus_student_list.xls", :type =>  "application/vnd.ms-excel"
    end
    
  end
  def print_bus_card
    if request.post?
      unless params[:students].nil?
          @student_ids = params[:students]
          @students = Student.find_all_by_id(@student_ids,:include=>[{:batch=>[:course]}],:conditions=>["is_deleted = ?",false])
          std_ids = @students.map(&:id)
          @transports = Transport.find_all_by_receiver_id(std_ids,:conditions=>["receiver_type = ?","Student"],:include=>[:route,:vehicle])
      end   
    end
    render :layout => false
  end
  def list_students
    @students = []
    unless params[:batch_id].blank?
      batch_ids = params[:batch_id].split(",")
      @students = Student.find_all_by_batch_id(batch_ids,:conditions=>["is_deleted = ?",false])
    end
  end
  def list_student_bus_card
    @students = []
    unless params[:batch_id].blank?
      batch_ids = params[:batch_id].split(",")
      @students = Student.find_all_by_batch_id(batch_ids,:conditions=>["is_deleted = ?",false])
    end
  end
  def print_admit_card
    if request.post?
      unless params[:admid_card][:student_ids].nil?
          @student_ids = params[:admid_card][:student_ids]
          @students = Student.find_all_by_id(@student_ids,:include=>[{:batch=>[:course]}],:conditions=>["is_deleted = ?",false])
          @exam_name = params[:exam_name]
          @term_year = params[:term_year]
      end   
    end
    render :layout => false
  end
  
  def student_record
    @batches=Batch.active.all(:include=>:course)
  end
  
  def list_student_record
    @students = []
    unless params[:batch_id].blank?
      batch_ids = params[:batch_id].split(",")
      @students = Student.find_all_by_batch_id(batch_ids,:order=>"batch_id asc,first_name asc, last_name asc",:conditions=>["is_deleted = ?",false])
    end
  end
  
  def print_student_record
    if request.post?
      unless params[:student_record][:student_ids].nil?
          @student_ids = params[:student_record][:student_ids]
          @students = Student.find_all_by_id(@student_ids,:order=>"batch_id asc,first_name asc, last_name asc",:include=>[{:batch=>[:course]}],:conditions=>["is_deleted = ?",false])
          @section_name = params[:section_name]
          @year = params[:year]
      end   
    end
    render :layout => false
  end
  
  def excel_student_record
    if request.post?
      require 'spreadsheet'
      Spreadsheet.client_encoding = 'UTF-8'
      new_book = Spreadsheet::Workbook.new
      sheet1 = new_book.create_worksheet :name => 'student_record'
      center_align_format = Spreadsheet::Format.new :weight => :bold, :horizontal_align => :center, :vertical_align => :middle, :size => 13
      large_bold_format = Spreadsheet::Format.new :weight => :bold,:size => 18, :horizontal_align => :center, :vertical_align => :middle
      border_bottom_format = Spreadsheet::Format.new :bottom => :thin, :size => 13
      border_top_format = Spreadsheet::Format.new :top => :thin, :size => 13
      
      unless params[:student_record][:student_ids].nil?
        @student_ids = params[:student_record][:student_ids]
        @students = Student.find_all_by_id(@student_ids,:include=>[{:batch=>[:course]}],:conditions=>["is_deleted = ?",false])
        @section_name = params[:section_name]
        @year = params[:year]
        stu_record_row = []
        school_name_row = []
        center_row = [0]
        address_row = []
        border_bottom = []
        border_top = []
        row_begin = [Date.today,'','Student Record','']
        new_book.worksheet(0).insert_row(0, row_begin)
        std_loop = 4
        @students.each do |std|
          startrow = std_loop
          row_1 = ['','','Student Record(Anual)','']
          new_book.worksheet(0).insert_row(startrow+1, row_1)
          stu_record_row.push(startrow+1)
          row_2 = ['','',@section_name,'']
          new_book.worksheet(0).insert_row(startrow+2, row_2)
          center_row.push(startrow+2)
          row_3 = ['','',@year,'']
          new_book.worksheet(0).insert_row(startrow+3, row_3)
          center_row.push(startrow+3)
          row_4 = ['','',Configuration.get_config_value('InstitutionName'),'']
          new_book.worksheet(0).insert_row(startrow+4, row_4)
          school_name_row.push(startrow+4)
          row_5 = ['','',Configuration.get_config_value('InstitutionAddress'),'']
          new_book.worksheet(0).insert_row(startrow+5, row_5)
           center_row.push(startrow+5)
           
          std_nationality = std.nationality_id.nil? ? '': std.nationality.nationality.nationality
          row_6 = ['', 'ID: '+std.admission_no, 'Name: '+std.full_name,'Class & Section: '+std.batch.course.course_name+' '+std.batch.course.section_name,'Nationality: '+std_nationality]
          new_book.worksheet(0).insert_row(startrow+7, row_6)
          border_bottom.push(startrow+7)
          
          std_dob = std.date_of_birth.nil? ? '' : I18n.l(std.date_of_birth,:format=>"%d %b %Y")
          std_admdate = std.admission_date.nil? ? '' : I18n.l(std.admission_date,:format=>"%d %b %Y")
          std_religion = std.religion.blank? ? '' : std.religion.titlecase
          row_8 = ['', 'DOB: '+std_dob,'Date of Admission: '+std_admdate,'Religion: '+std_religion]
          new_book.worksheet(0).insert_row(startrow+8, row_8)
          border_bottom.push(startrow+8)
          office_phone = ''
          father_mobile = ''
          mother_mobile = ''
          guardians = GuardianStudents.find_all_by_student_id(std.id)
          unless guardians.blank? 
            guardians.each do |gur| 
              gurdian = Guardian.find_by_id(gur.guardian_id)
              unless gurdian.blank?
                if gurdian.relation.index("Father") || gurdian.relation.index("father")
                  qualification = gurdian.education.blank? ? '' : gurdian.education
                  occupation = gurdian.occupation.blank? ? '' : gurdian.occupation
                  row_9 = ['','Father Name: '+gurdian.first_name+ '' +gurdian.last_name, 'Qualification: '+qualification,'Occupation: '+occupation,]
                  new_book.worksheet(0).insert_row(startrow+9, row_9)
                   border_bottom.push(startrow+9)
                   
                  designation = gurdian.occupation.blank? ? ' ' : gurdian.designation  
                  idno = gurdian.occupation.blank? ? ' ' : gurdian.passport
                  row_10 = ['','Designation: '+designation.to_s,'Passport / ID No: '+idno.to_s,'','']
                  new_book.worksheet(0).insert_row(startrow+10, row_10)
                  border_bottom.push(startrow+10)
                  
                  father_mobile = gurdian.mobile_phone
                  office_phone = gurdian.office_phone1
                  break
                end
              end
            end
          end
          unless guardians.blank? 
            guardians.each do |gur| 
              gurdian = Guardian.find_by_id(gur.guardian_id)
              unless gurdian.blank?
                if gurdian.relation.index("Mother") || gurdian.relation.index("mother")
                  qualification = gurdian.education.blank? ? '' : gurdian.education
                  occupation = gurdian.occupation.blank? ? '' : gurdian.occupation
                  row_11 = ['','Mother Name: '+gurdian.first_name+ '' +gurdian.last_name, 'Qualification: '+qualification,'Occupation: '+occupation,]
                  new_book.worksheet(0).insert_row(startrow+11, row_11)
                  border_bottom.push(startrow+11)
                   
                  designation = gurdian.occupation.blank? ? ' ' : gurdian.designation  
                  idno = gurdian.occupation.blank? ? ' ' : gurdian.passport
                  row_12 = ['','Designation: '+designation.to_s,'Passport / ID No: '+idno.to_s,'','']
                  new_book.worksheet(0).insert_row(startrow+12, row_12)
                  border_bottom.push(startrow+12)
                   
                  mother_mobile = gurdian.mobile_phone
                  break
                end
              end
            end
          end
          address = std.address_line1.blank? ? ' ' : std.address_line1
          land_phone = std.phone1.blank? ? ' ': std.phone1.to_s 
          row_13 = ['','Present Address: '+address.to_s,'','Land Phone(Res): '+land_phone.to_s]
          new_book.worksheet(0).insert_row(startrow+13, row_13)
          border_bottom.push(startrow+13)
           
          address_row.push(startrow+13)
          row_14 = ['','Office: '+office_phone.to_s ,'Mobile Phone (Father): '+father_mobile.to_s,'(Mother): '+mother_mobile.to_s]
          new_book.worksheet(0).insert_row(startrow+14, row_14)
          border_bottom.push(startrow+14)
           
          row_22 = ['','Signature of Father & Date','','Signature of Mother & Date']
          new_book.worksheet(0).insert_row(startrow+22, row_22)
          center_row.push(startrow+22)
          border_top.push(startrow+22)
          
          std_loop += 35
        end
      end
      
      center_row.each do |i|
          sheet1.row(i).default_format = center_align_format
      end
      
      stu_record_row.each do |r|
        sheet1.row(r).default_format = large_bold_format
        sheet1.merge_cells(r, 1, r, 3)
      end
      
      school_name_row.each do |n|
        sheet1.row(n).default_format = large_bold_format
        sheet1.merge_cells(n, 1, n, 3)
      end
      
      address_row.each do |n|
        sheet1.merge_cells(n, 1, n, 2)
      end
      
      border_bottom.each do |n|
        sheet1.row(n).set_format(1,border_bottom_format)
        sheet1.row(n).set_format(2,border_bottom_format)
        sheet1.row(n).set_format(3,border_bottom_format)
      end
      
      border_top.each do |n|
        sheet1.row(n).set_format(1,border_top_format)
        sheet1.row(n).set_format(3,border_top_format)
      end
      
      sheet1.add_header("S.F.X. Greenherald International (School Student Record)")
      spreadsheet = StringIO.new 
      new_book.write spreadsheet 
      send_data spreadsheet.string, :filename => "Student_record.xls", :type =>  "application/vnd.ms-excel"
    end
  end
  
  
end
