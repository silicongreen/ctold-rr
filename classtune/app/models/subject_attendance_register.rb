class SubjectAttendanceRegister < ActiveRecord::Base
  belongs_to :subject
  belongs_to :batch
  belongs_to :employee
end
