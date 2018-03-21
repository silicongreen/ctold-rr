class MeetingRequest < ActiveRecord::Base
  belongs_to :employee, :class_name => 'Employee', :foreign_key=>:teacher_id
  belongs_to :student, :foreign_key=>'parent_id'
  
  validates_presence_of :meeting_type, :teacher_id, :parent_id, :description, :datetime, :status
end
