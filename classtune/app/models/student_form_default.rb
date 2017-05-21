class StudentFormDefault < ActiveRecord::Base
  belongs_to :student
  belongs_to :student_form
  
  def after_save
    school_id = MultiSchool.current_school.id
  end
end
