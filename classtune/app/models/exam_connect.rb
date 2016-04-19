class ExamConnect < ActiveRecord::Base
  
  self.table_name = "exam_connect"
  belongs_to :batch

  def before_save
    self.school_id = MultiSchool.current_school.id
  end

end
