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

class ExamConnect < ActiveRecord::Base
  validates_presence_of :name  
  belongs_to :batch
  has_many :grouped_exam
  named_scope :active, :conditions => {:is_deleted=>false}
  def after_save
    keymarksheet = "marksheet_#{self.id}"
    Rails.cache.delete_matched(/#{keymarksheet}*/)
    Rails.cache.delete("tabulation_#{self.id}_#{self.batch_id}")
    Rails.cache.delete("continues_#{self.id}_#{self.batch_id}")
    key = "student_exam_#{self.id}_#{self.batch_id}"
    Rails.cache.delete_matched(/#{key}*/)
  end  

  
end

