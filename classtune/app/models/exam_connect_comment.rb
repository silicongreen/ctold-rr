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

class ExamConnectComment < ActiveRecord::Base
  validates_presence_of :comments
  belongs_to :student
  belongs_to :employee
  belongs_to :exam_connect
  def before_save
    self.school_id = MultiSchool.current_school.id
  end  
  
  def after_save
    exam_connect = ExamConnect.find_by_id(self.exam_connect_id)
    unless exam_connect.blank?
        Rails.cache.delete("tabulation_#{exam_connect.id}_#{exam_connect.batch_id}")
        Rails.cache.delete("continues_#{exam_connect.id}_#{exam_connect.batch_id}")
        key = "student_exam_#{exam_connect.id}_#{exam_connect.batch_id}"
        Rails.cache.delete_matched(/#{key}*/)
        
    end
  end
end

