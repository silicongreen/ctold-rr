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

class FinanceFeeParticularCategory < ActiveRecord::Base
  
  has_many   :fee_particulars, :class_name => "FinanceFeeParticular"
  validates_uniqueness_of :name,:case_sensitive => false,:message=>"already marked as absent"

  named_scope :active,:conditions => {:is_deleted => false}
  
  def fee_particular_exists
    fees_particular = FinanceFeeParticular.find(:all, :conditions => {:finance_fee_particular_category_id => id.to_i})
    if fees_particular.present?
      return true
    else
      return false
    end
  end
end
