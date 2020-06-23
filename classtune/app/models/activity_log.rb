class ActivityLog < ActiveRecord::Base
  
  belongs_to :user
  serialize :post_requests
  def before_save
    self.school_id = MultiSchool.current_school.id
  end
  
  after_initialize do |activity_log|
    unless MultiSchool.current_school.nil?
        self.table_name = MultiSchool.current_school.code + "_activity_logs"
    end
  end
 
end
