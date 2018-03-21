class SubscriptionInfo < ActiveRecord::Base
  self.table_name = "subscription_info"
  def before_save
    self.school_id = MultiSchool.current_school.id
  end

end
