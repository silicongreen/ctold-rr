class MarksController < ApplicationController
  include ActionView::Helpers::TextHelper
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time
  
  def index
    if current_user.employee
    @subjects = current_user.employee_record.subjects.active
    elsif current_user.admin
      @subjects = Subject.active
    end  
    @subjects.reject! {|s| !s.batch.is_active}
    @exams = []
    @batches = []
    @subjects.each do |sub|
      exams = Exam.find_all_by_subject_id(sub.id)
      unless exams.blank?
        exams.each do |exam|  
          @exams.push exam unless exam.nil? or exam.exam_group.result_published == true
          unless exam.nil? or exam.exam_group.result_published == true or @batches.include?(exam.exam_group.batch)
            unless exam.exam_group.batch.nil?
              @batches << exam.exam_group.batch
            end
          end 
        end
      end
    end 
    
    @exams.sort! { |a, b|  a.created_at <=> b.created_at }
  end
  def connect_exam
    @today = @local_tzone_time.to_date
    @exam_connect = ExamConnect.find(:all,:conditions => ["published_date < ?",@today] )
    @batches = []
    @exam_connect.each do |exam_connect|
      unless @batches.include?(exam_connect.batch)
        unless exam_connect.batch.nil?
          @batches << exam_connect.batch
        end
      end  
    end
  end
 
end
