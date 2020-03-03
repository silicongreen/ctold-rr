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

class FinanceOrder < ActiveRecord::Base
  
  belongs_to :finance_fee
  belongs_to :student
  belongs_to :batch
  
  serialize :request_params
  
  before_create :change_table
  before_save :change_table
  before_update :change_table
  
  def change_table
    unless MultiSchool.current_school.nil?
      if MultiSchool.current_school.id != 352
        self.table_name = MultiSchool.current_school.code + "_payments"
      end
    end
  end
  
#  after_initialize do |finance_order|
#    unless MultiSchool.current_school.nil?
#      if MultiSchool.current_school.id != 352
#        self.table_name = MultiSchool.current_school.code + "_finance_orders"
#      end
#    end
#  end
  
end
