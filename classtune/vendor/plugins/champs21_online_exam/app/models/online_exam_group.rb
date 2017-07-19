class OnlineExamGroup < ActiveRecord::Base
  has_many :online_exam_attendances
  has_many :online_exam_questions
  has_many :online_exam_options, :through=>:online_exam_questions
  has_many :online_exam_score_details, :through=>:online_exam_questions
  belongs_to :batch
  belongs_to :subject

  validates_presence_of :name, :start_date, :end_date, :maximum_time, :pass_percentage
  validates_numericality_of :pass_percentage, :less_than_or_equal_to=> 100,:greater_than_or_equal_to=> 0
  validates_numericality_of :maximum_time, :greater_than => 0
  before_save :end_date_check
  cattr_reader :per_page
  @@per_page = 13
    


  def already_attended(student)
    OnlineExamAttendance.exists?( :student_id => student, :online_exam_group_id=>self.id)
  end

  def has_attendence
    if self.online_exam_attendances.blank?
      return false
    else
      return true
    end
  end

  def validate
    unless self.start_date.nil? or self.end_date.nil?
      if self.start_date.to_date > self.end_date.to_date
        errors.add_to_base :end_date_should_be_after_start_date
        return false
      else
        return true
      end
    end
  end
  def end_date_check
    if self.end_date < Date.today
      errors.add_to_base :should_not_be_less_than_today
      return false
    else
      return true
    end
  end

end
