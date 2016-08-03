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

class FeeCollectionParticular < ActiveRecord::Base
  belongs_to :finance_fee_collection
  belongs_to :student_category
  validates_presence_of :name,:amount
  validates_numericality_of :amount
  cattr_reader :per_page
  @@per_page = 10
  named_scope :active,{ :conditions => { :is_deleted => false}}
  before_save :verify_precision

  def verify_precision
    self.amount = Champs21Precision.set_and_modify_precision self.amount
  end

  def student_name
    if admission_no.present?
      student = Student.find_by_admission_no(admission_no)
      student ||= ArchivedStudent.find_by_admission_no(admission_no)
      student.present? ? "#{student.first_name} (#{student.admission_no})" : "N.A. (N.A.)"
    end
  end
end
