class AssignmentDefaulterRegistration < ActiveRecord::Base
  belongs_to :employee
  belongs_to :assignment
end
