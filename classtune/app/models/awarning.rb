class Awarning < ActiveRecord::Base
  
  validates_presence_of :batch_id, :student_id,:subjects, :employee_id
  belongs_to :student
  belongs_to :batch
  belongs_to :employee

  def before_save
    self.school_id = MultiSchool.current_school.id
  end

end
