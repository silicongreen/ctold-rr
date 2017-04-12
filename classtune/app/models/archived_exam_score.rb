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

class ArchivedExamScore < ActiveRecord::Base
  belongs_to :student
  belongs_to :exam
  belongs_to :grading_level


  before_save :calculate_grade

  def calculate_percentage
    percentage = self.marks.to_i * 100 / self.exam.maximum_marks
  end

  def grouped_exam_subject_total(subject,student,type,batch = "")
    if batch == ""
      batch = student.batch.id
    end
    if type == 'grouped'
      grouped_exams = GroupedExam.find_all_by_batch_id(batch)
      exam_groups = []
      grouped_exams.each do |x|
        eg = ExamGroup.active.find(x.exam_group_id)
        exam_groups.push ExamGroup.active.find(x.exam_group_id)
      end
    else
      exam_groups = ExamGroup.active.find_all_by_batch_id(batch)
    end
    total_marks = 0
    exam_groups.each do |exam_group|
      unless exam_group.exam_type == 'Grades'
        exam = Exam.find_by_subject_id_and_exam_group_id(subject.id,exam_group.id)
        unless exam.nil?
          exam_score = ArchivedExamScore.find_by_student_id(student.id, :conditions=>{:exam_id=>exam.id})
          total_marks = total_marks+ (exam_score.marks || 0) unless exam_score.nil?
        end
      end
    end
    total_marks
  end


  def batch_wise_aggregate(student,batch)
    check = ExamGroup.active.find_all_by_batch_id(batch.id)
    var = []
    check.each do |x|
      if x.exam_type == 'Grades'
        var << 1
      end
    end
    if var.empty?
      grouped_exam = GroupedExam.find_all_by_batch_id(batch.id)
      if grouped_exam.empty?
        exam_groups = ExamGroup.active.find_all_by_batch_id(batch.id)
      else
        exam_groups = []
        grouped_exam.each do |x|
          exam_groups.push ExamGroup.active.find(x.exam_group_id)
        end
      end
      exam_groups.size
      max_total = 0
      marks_total = 0
      exam_groups.each do |exam_group|
        max_total = max_total + exam_group.archived_total_marks(student)[1]
        marks_total = marks_total + exam_group.archived_total_marks(student)[0]
      end
      aggr = (marks_total*100/max_total) unless max_total ==0
    else
      aggr = 'nil'
    end

  end

  private
  def calculate_grade
    exam = self.exam
    exam_group = exam.exam_group
    exam_type = exam_group.exam_type
    student_batch = ArchivedStudent.find(self.student_id).batch_id
    unless exam_type == 'Grades'
      unless self.marks.nil?
        percent_score = self.marks.to_i * 100 / self.exam.maximum_marks
        grade = GradingLevel.percentage_to_grade(percent_score, student_batch)
        self.grading_level_id = grade.id if exam_type == 'MarksAndGrades'
      else
        self.grading_level_id = nil
      end
    end
  end

end
