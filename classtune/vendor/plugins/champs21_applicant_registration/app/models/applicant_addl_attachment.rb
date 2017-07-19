class ApplicantAddlAttachment < ActiveRecord::Base
has_attached_file :attachment,
    :url => "/uploads/:class/:attachment/:id/:style/:attachment_fullname?:timestamp",
    :path => "public/uploads/:class/:attachment/:id/:style/:basename.:extension"
end
