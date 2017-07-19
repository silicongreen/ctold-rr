class TallyExportFile < ActiveRecord::Base

  has_attached_file :export_file,
  :url => "/tally_exports/download/:id",
#  :path => "uploads/:class/:attachment/:basename.:extension",
#  :path => "uploads/:class/:attachment/:id_partition/:basename.:extension",  
  :path => "public/uploads/:class/:attachment/:id/:style/:basename.:extension",
  :use_timestamp => false


#  validates_attachment_content_type :export_file, :content_type =>'text/xml',
#  :message=>'Only XML file permitted'

end
