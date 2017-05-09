class AttendanceRegister < ActiveRecord::Base
  belongs_to :batch
  belongs_to :employee
end
