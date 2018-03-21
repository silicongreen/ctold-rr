class OtherController < ApplicationController
  include ActionView::Helpers::TextHelper
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time
  
  
  def admit_card
    @batches=Batch.active.all(:include=>:course)
  end
  def list_students
    @students = []
    unless params[:batch_id].blank?
      batch_ids = params[:batch_id].split(",")
      @students = Student.find_all_by_batch_id(batch_ids)
    end
  end
  def print_admit_card
    if request.post?
      unless params[:admid_card][:student_ids].nil?
          @student_ids = params[:admid_card][:student_ids]
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
      @students = Student.find_all_by_batch_id(batch_ids,:order=>"batch_id asc,first_name asc, last_name asc")
    end
  end
  def print_student_record
    if request.post?
      unless params[:student_record][:student_ids].nil?
          @student_ids = params[:student_record][:student_ids]
          @section_name = params[:section_name]
          @year = params[:year]
      end   
    end
    render :layout => false
  end
  
  
end
