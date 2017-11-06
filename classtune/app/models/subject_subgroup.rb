class SubjectSubgroup < ActiveRecord::Base
  
  belongs_to :subject
  default_scope :order => 'parent_id ASC, priority ASC'
  def before_save
    self.school_id = MultiSchool.current_school.id
  end

end
