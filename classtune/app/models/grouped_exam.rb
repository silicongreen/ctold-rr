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

class GroupedExam < ActiveRecord::Base
  has_many :exam_groups
  def after_save
    
    grouped_exam = GroupedExam.find_by_id(self.id)
    unless grouped_exam.blank?
      Rails.cache.delete("tabulation_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}")
      Rails.cache.delete("continues_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}")
      key = "student_exam_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}"
      Rails.cache.delete_matched(/#{key}*/)
      
      keymarksheet = "marksheet_#{grouped_exam.connect_exam_id}"
      Rails.cache.delete_matched(/#{keymarksheet}*/)
      
    end
    
  end
end
