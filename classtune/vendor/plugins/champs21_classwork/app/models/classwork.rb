class Classwork < ActiveRecord::Base
  belongs_to :employee
  belongs_to :subject
  before_destroy :delete_redactors
  after_save :update_redactor
  attr_accessor :redactor_to_update, :redactor_to_delete
  has_many :classwork_answers , :dependent=>:destroy
  named_scope :active,:conditions => {:subjects=>{:is_deleted => false}},:joins=>[:subject]
  xss_terminate :except => [:content]
  validates_presence_of :title, :content,:student_list

  has_attached_file :attachment ,
    :url => "/uploads/:class/:attachment/:id/:style/:attachment_fullname?:timestamp"
    #:path => "public/uploads/:class/:attachment/:id/:style/:basename.:extension"

  named_scope :for_student, lambda { |s|{ :conditions => ["FIND_IN_SET(?,student_list)",s],:order=>"created_at desc"} }
  
  def update_attributes(attributes)
    self.attributes = attributes
    update_at_old = self.updated_at
    save
    self.updated_at = update_at_old
    save
  end
  def download_allowed_for user
    return true if user.admin?
    return (user.employee_record.id==self.employee_id) if user.employee?
    return (self.student_list.split(",").include? user.student_record.id.to_s) if user.student?
    return (self.student_list.split(",").include? user.parent_record.id.to_s) if user.parent?
    false
  end
  def update_redactor
    RedactorUpload.update_redactors(self.redactor_to_update,self.redactor_to_delete)
  end

  def delete_redactors
    RedactorUpload.delete_after_create(self.content)
  end
  def classwork_student_ids
    student_list.split(",").collect{|s| s.to_i}
  end
  def validate
    return true
  end
end
