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
  
  
end
