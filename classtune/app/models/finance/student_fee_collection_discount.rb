#Champs21
#Copyright 2011 teamCreative Private Limited
#
#This product includes software developed at
#Project Champs21 - http://www.champs21.com/
#
#Licensed under the Apache License, Version 2.0 (the "License");
#you may not use this file except in compliance with the License.
#You may obtain a copy of the License at
#
#  http://www.apache.org/licenses/LICENSE-2.0
#
#Unless required by applicable law or agreed to in writing, software
#distributed under the License is distributed on an "AS IS" BASIS,
#WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#See the License for the specific language governing permissions and
#limitations under the License.


class StudentFeeCollectionDiscount < FeeCollectionDiscount
  belongs_to :receiver ,:class_name=>'Student'

  validates_presence_of  :receiver_id , :message => :student_admission_no_cant_be_blank

  before_save :verify_precision

  def verify_precision
    self.discount = Champs21Precision.set_and_modify_precision self.discount
  end

  def student_name
    s = Student.find_by_id(self.receiver_id)
    s ||= ArchivedStudent.find_by_former_id(self.receiver_id)
    student.present? ? "#{s.full_name} <br /><b>Admission No:</b> #{s.admission_no}" : "N.A. (N.A.)"
  end

  def total_payable(student = nil)
    if student.nil?
      payable = finance_fee_collection.fee_category.fee_particulars.active.map(&:amount).compact.flatten.sum
    else
      payable = finance_fee_collection.fees_particulars(student).select{|f| (f.is_deleted==false)}.map(&:amount).compact.flatten.sum
    end
    payable
  end

  def discount(student = nil)
    student.nil? ? super : super(student)
  end
  
end
