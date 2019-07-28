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
          @students = Student.find_all_by_id(@student_ids,:include=>[{:batch=>[:course]}],:conditions=>["is_deleted = ?",false])
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
      sheet1 = new_book.create_worksheet :name => 'tabulation'
      center_align_format = Spreadsheet::Format.new :horizontal_align => :center,  :vertical_align => :middle
      
      row_first = ['date','','Student Record','']
      new_book.worksheet(0).insert_row(1, row_first)
      starting_row = 5
      end_row = starting_row+7

      (starting_row..end_row).each do |i|
        sheet1.row(i).default_format = center_align_format
      end
      unless params[:student_record][:student_ids].nil?
        @student_ids = params[:student_record][:student_ids]
        @students = Student.find_all_by_id(@student_ids,:include=>[{:batch=>[:course]}],:conditions=>["is_deleted = ?",false])
        @section_name = params[:section_name]
        @year = params[:year]
        std_loop= 5
        @students.each do |std|
          tmp_row = []
          tmp_row << std.full_name.to_s
          tmp_row << std.full_name.to_s
          new_book.worksheet(0).insert_row(std_loop, tmp_row)
          std_loop += 1
        end
      end

      sheet1.add_header("S.F.X. Greenherald International (School Student Record)")
      spreadsheet = StringIO.new 
      new_book.write spreadsheet 
      send_data spreadsheet.string, :filename => "Student_record.xls", :type =>  "application/vnd.ms-excel"
    end
  end
  
  
end
