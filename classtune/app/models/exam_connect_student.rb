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
class ExamConnectStudent < ActiveRecord::Base
  belongs_to :batch
  belongs_to :student
  belongs_to :exam_connect
  belongs_to :school
  belongs_to :batch_student
  validates_presence_of :student_id,:batch_id,:exam_connect_id
  
  def before_save
    self.school_id = MultiSchool.current_school.id
  end
end
