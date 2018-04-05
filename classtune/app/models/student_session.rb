class StudentSession < ActiveRecord::Base
  validates_presence_of :admission_session, :exam_session
  named_scope :active, :conditions => { :status => true}
end
