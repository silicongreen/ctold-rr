
class HostelFee < ActiveRecord::Base
  belongs_to :student
  belongs_to :hostel_fee_collection
  has_one :finance_transaction, :as => :finance
  before_save :verify_precision

  def verify_precision
    self.rent = Champs21Precision.set_and_modify_precision self.rent
  end

  named_scope :active , :joins=>[:hostel_fee_collection] ,:conditions=>{:hostel_fee_collections=>{:is_deleted=>false}}

  def payee_name
   if student.nil?
    archived_student= ArchivedStudent.find_by_former_id(student_id)
    if archived_student
    "#{archived_student.full_name}(#{archived_student.admission_no})"
    else
      "#{t('user_deleted')}"
    end
   else
      student.full_name
   end
  end
end
