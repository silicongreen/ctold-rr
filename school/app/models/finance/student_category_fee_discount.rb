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


class StudentCategoryFeeDiscount < FeeDiscount

  belongs_to :receiver ,:class_name=>'StudentCategory'
  validates_presence_of  :receiver_id , :message => :student_category_cant_be_blank

  validates_uniqueness_of :name, :scope=>[:finance_fee_category_id, :type]

  #validates_uniqueness_of :receiver_id, :scope=>[:type,:finance_fee_category_id],:message=>'Discount already exists for the student category'
  before_save :verify_precision

  def verify_precision
    self.discount = Champs21Precision.set_and_modify_precision self.discount
  end

  def total_payable
    payable = finance_fee_category.fee_particulars.active.map(&:amount).compact.flatten.sum
    payable
  end

  def discount
    super
  end
  
  def category_name
    c =StudentCategory.find(self.receiver_id)
    c.name unless c.nil?
  end

  
end
