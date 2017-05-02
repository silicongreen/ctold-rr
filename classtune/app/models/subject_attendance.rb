class SubjectAttendance < ActiveRecord::Base
  belongs_to :subject
  belongs_to :batch
  belongs_to :student
end
