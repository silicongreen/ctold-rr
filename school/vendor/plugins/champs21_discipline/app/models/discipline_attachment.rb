class DisciplineAttachment < ActiveRecord::Base
  belongs_to :discipline_participation
  has_attached_file :attachment,
	#:path => "uploads/:class/:discipline_participation_id/:id_partition/:basename.:extension"	
    :path => "public/uploads/:class/:discipline_participation_id/:id/:style/:basename.:extension"
  Paperclip.interpolates :discipline_participation_id do |attachment,style|
    attachment.instance.discipline_participation_id
  end
end
