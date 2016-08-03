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
class CceReport < ActiveRecord::Base

  belongs_to    :batch
  belongs_to    :student
#  has_and_belongs_to_many   :exams
  belongs_to    :observable, :polymorphic=>true
  belongs_to    :exam
  belongs_to    :fa_criteria, :class_name=>'FaCriteria', :foreign_key=>'observable_id'
  belongs_to    :observation, :class_name=>'Observation', :foreign_key=>'observable_id'
  named_scope :scholastic,{:conditions=>{:observable_type=>"FaCriteria"}}
  named_scope :coscholastic,{:conditions=>{:observable_type=>"Observation"}}
end
