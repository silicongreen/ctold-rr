class SessionRoll < ActiveRecord::Base
  self.table_name = "session_roll"
  def before_save
    self.school_id = MultiSchool.current_school.id
  end
end
