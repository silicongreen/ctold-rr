class ForceAttendence < ActiveRecord::Base
  
  belongs_to :user

  def before_save
    self.school_id = MultiSchool.current_school.id
  end

end
