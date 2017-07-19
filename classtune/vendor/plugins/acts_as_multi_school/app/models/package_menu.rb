class PackageMenu < ActiveRecord::Base
  validates_presence_of :package_id
  named_scope :active, :conditions => { :is_active => 1 }

end
