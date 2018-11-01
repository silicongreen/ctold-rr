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

class FinanceFeeAdvance < ActiveRecord::Base
  
  belongs_to :finance_fee_collection ,:foreign_key => 'fee_collection_id'
  belongs_to :student
  belongs_to :batch
  named_scope :active , :joins=>[:finance_fee_collection] ,:conditions=>{:finance_fee_collections=>{:is_deleted=>false}}
  
end
