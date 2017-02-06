class ClassworkAnswer < ActiveRecord::Base
  belongs_to :classwork
  belongs_to :student
  validates_presence_of :title,:content
  has_attached_file :attachment ,
#    :url => "/user/paperclip_attachment/:id?attachment=attachment&class=classwork_answer",
#    :path => "uploads/classworks/:classwork_id/:class/:id_partition/:basename.:extension"
#	:url => "/uploads/:class/:attachment/:id/:style/:attachment_fullname?:timestamp",
    :path => "public/uploads/:class/:attachment/:classwork_id/:basename.:extension"

  def download_allowed_for user
    return true if user.admin?
    return  (user.employee_record.id==self.classwork.employee_id) if user.employee?
    return (self.student_id == user.student_record.id) if user.student?
    return (self.student_id == user.parent_record.id) if user.parent?
    false
  end

  def student_details
    if self.student.present?
      return self.student
    else
      return ArchivedStudent.find_by_former_id(self.student_id)
    end
  end

  Paperclip.interpolates :student_id do |attachment,style|
    attachment.instance.student_id
  end

  Paperclip.interpolates :classwork_id do |attachment,style|
    custom_id_partition attachment.instance.classwork_id
  end
end
