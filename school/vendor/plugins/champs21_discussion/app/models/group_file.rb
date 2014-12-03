class GroupFile < ActiveRecord::Base
  belongs_to :group
  belongs_to :user
  belongs_to :group_post
  has_attached_file :doc,
    :styles => {:small  => "250x200>"},
    #:url => "discussion/:class/:attachment/:id/:style/:basename.:extension",
    #:path => "uploads/:class/:attachment/:id_partition/:style/:basename.:extension",
	:url => "/uploads/:class/:attachment/:id/:style/:attachment_fullname?:timestamp",
    :path => "public/uploads/:class/:attachment/:id/:style/:basename.:extension",
  :whiny=>false

  validates_attachment_size :doc, :less_than => 5120000,\
    :message=>'must be less than 5 MB.',:if=> Proc.new { |p| p.doc_file_name_changed? }

  validates_presence_of :doc_file_name
end