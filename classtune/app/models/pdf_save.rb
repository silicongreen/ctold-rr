class PdfSave < ActiveRecord::Base
  
  belongs_to :batch
  validates_presence_of :batch_id

end
