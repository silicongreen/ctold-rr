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

class StudentsSubject < ActiveRecord::Base
  belongs_to :student, :conditions => { :is_deleted => false,:is_active => true }
  belongs_to :subject
  belongs_to :batch
  validates_uniqueness_of :student_id,:scope=>[:subject_id]

  def student_assigned(student,subject)
    StudentsSubject.find_by_student_id_and_subject_id(student,subject)
  end
end
