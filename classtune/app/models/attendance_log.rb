class AttendanceLog < ActiveRecord::Base
  
  belongs_to :student
  belongs_to :employee

  def before_save
    self.school_id = MultiSchool.current_school.id
  end

end
