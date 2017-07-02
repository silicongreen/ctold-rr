class CardAttendance < ActiveRecord::Base
  self.table_name = "card_attendance"
  def before_save
    self.school_id = MultiSchool.current_school.id
  end

end
