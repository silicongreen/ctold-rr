class ActivityLog < ActiveRecord::Base
  
  belongs_to :user
  serialize :post_requests
  def before_save
    self.school_id = MultiSchool.current_school.id
  end
 
end
