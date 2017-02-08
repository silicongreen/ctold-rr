class MarksController < ApplicationController
  include ActionView::Helpers::TextHelper
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time
  
  def index
    @subjects = current_user.employee_record.subjects.active
    @subjects.reject! {|s| !s.batch.is_active}
    @exams = []
    @subjects.each do |sub|
      exam = Exam.find_by_subject_id(sub.id)
      @exams.push exam unless exam.nil?
    end 
    @exams.sort! { |a, b|  b.start_time <=> a.start_time }
  end
 
end
