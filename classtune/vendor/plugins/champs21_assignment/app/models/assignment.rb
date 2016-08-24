class Assignment < ActiveRecord::Base
  belongs_to :employee
  belongs_to :subject
  before_destroy :delete_redactors
  after_save :update_redactor
  attr_accessor :redactor_to_update, :redactor_to_delete
  has_many :assignment_answers , :dependent=>:destroy
  named_scope :active,:conditions => {:subjects=>{:is_deleted => false}},:joins=>[:subject]

  xss_terminate :except => [:content]
  validates_presence_of :title, :content,:student_list, :duedate

  has_attached_file :attachment ,
    :url => "/uploads/:class/:attachment/:id/:style/:attachment_fullname?:timestamp"
    #:path => "public/uploads/:class/:attachment/:id/:style/:basename.:extension"

  named_scope :for_student, lambda { |s|{ :conditions => ["FIND_IN_SET(?,student_list)",s],:order=>"duedate asc"} }

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
  def assignment_student_ids
    student_list.split(",").collect{|s| s.to_i}
  end
  def validate
    if self.duedate.to_date < Date.today
      errors.add_to_base :date_cant_be_past_date
      return false
    else
      return true
    end
  end
end
