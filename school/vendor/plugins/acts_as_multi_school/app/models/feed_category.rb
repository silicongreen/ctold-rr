class FeedCategory < ActiveRecord::Base
  validates_presence_of :description,:name
  named_scope :active, :conditions => { :status => 1 }
  has_attached_file :logo,
    :styles => { :original=> "150x110#"},
    :url => "/uploads/feed_category/:attachment_fullname?:timestamp",
    :path => "public/uploads/feed_category/:basename.:extension",
    :default_url  => '/images/application/dummy_logo.png',
    :default_path  => ':rails_root/public/images/application/dummy_logo.png'

  VALID_IMAGE_TYPES = ['image/gif', 'image/png','image/jpeg', 'image/jpg']

  validates_attachment_content_type :logo, :content_type =>VALID_IMAGE_TYPES,
    :message=>'Image can only be GIF, PNG, JPG',:if=> Proc.new { |p| !p.logo_file_name.blank? }
  validates_attachment_size :logo, :less_than => 512000,
    :message=>'must be less than 500 KB.',:if=> Proc.new { |p| p.logo_file_name_changed? }
end
