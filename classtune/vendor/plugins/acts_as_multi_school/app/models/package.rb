class Package < ActiveRecord::Base
  validates_presence_of :name, :message => "Package name can't be empty"
  validates_uniqueness_of :name, :message => "Package name already exists"
  named_scope :active, :conditions => { :is_active => 1 }
  
  has_many :package_menus
end
