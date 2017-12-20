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

class ExamAbsent < ActiveRecord::Base
  belongs_to :student
  belongs_to :exam
  validates_presence_of :student_id
  validates_presence_of :exam_id,:message =>  "Name/Batch Name/Subject Code is invalid"
  def after_save
    grouped_exams = GroupedExam.find_all_by_exam_group_id(self.exam.exam_group.id)
    unless grouped_exams.blank?
      grouped_exams.each do |grouped_exam|
        Rails.cache.delete("tabulation_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}")
        Rails.cache.delete("continues_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}")
        Rails.cache.delete("student_exam_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}_#{self.student_id}")
        Rails.cache.delete("marksheet_#{grouped_exam.connect_exam_id}_#{self.exam.subject_id}")        
      end
    end
  end
  
  def before_destroy
    grouped_exams = GroupedExam.find_all_by_exam_group_id(self.exam.exam_group.id)
    unless grouped_exams.blank?
      grouped_exams.each do |grouped_exam|
        Rails.cache.delete("tabulation_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}")
        Rails.cache.delete("continues_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}")
        Rails.cache.delete("student_exam_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}_#{self.student_id}")
        Rails.cache.delete("marksheet_#{grouped_exam.connect_exam_id}_#{self.exam.subject_id}")        
      end
    end
  end

  

end
