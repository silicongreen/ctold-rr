class Suspension < ActiveRecord::Base
  
  validates_presence_of :batch_id, :student_id,:total_detention,:start_time,:end_time,:start_date,:end_date,:reason,:meeting_date, :employee_id
  belongs_to :student
  belongs_to :batch
  belongs_to :employee

  def before_save
    self.school_id = MultiSchool.current_school.id
  end

end
