class UploadSyllabus < ActiveRecord::Base
  self.table_name = "upload_syllabus"
  has_attached_file :attachment,
    :url => "/uploads/:class/:attachment/:id/:style/:attachment_fullname"
  validates_presence_of :title, :batch_name, :course_name, :author_id
 
end
