class StudentForm < ActiveRecord::Base
  belongs_to :student
  
  def after_save
    school_id = MultiSchool.current_school.id
  end
end
