class GalleryPhoto < ActiveRecord::Base
  belongs_to :gallery_category
  has_many :gallery_tags, :dependent => :destroy

  has_attached_file :photo,
    :styles => {
    :thumb=> "181x132#",
    :small  => "150x150>"},
    #:path => "uploads/:class/:attachment/:id_partition/:style/:basename.:extension",
    #:url => "uploads/:class/:attachment/:id/:style/:basename.:extension"
	:url => "/uploads/:class/:attachment/:id/:style/:attachment_fullname?:timestamp",
    :path => "public/uploads/:class/:attachment/:id/:style/:basename.:extension"

  VALID_IMAGE_TYPES = ['image/gif', 'image/png','image/jpeg', 'image/jpg']

  validates_attachment_content_type :photo, :content_type =>VALID_IMAGE_TYPES,:message=>'Image can only be GIF, PNG, JPG',:if=> Proc.new { |p| !p.photo_file_name.blank? }
  validates_attachment_size :photo, :less_than => 5120000,\
    :message=>:must_be_less_than_5_mb,:if=> Proc.new { |p| p.photo_file_name_changed? }

  validates_presence_of :gallery_category_id,:name

  validates_presence_of :photo_file_name
  #there is no problem with original pic..

end
