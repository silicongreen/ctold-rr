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

class ExamScore < ActiveRecord::Base
  belongs_to :student
  belongs_to :exam
  belongs_to :grading_level

  before_save :calculate_grade
  before_save :check_existing


  validates_presence_of :student_id
  validates_presence_of :exam_id,:message =>  "Name/Batch Name/Subject Code is invalid"
  validates_numericality_of :marks,:allow_nil => true
  validates_uniqueness_of :exam_id, :scope=> [:student_id],:message => "score already present."
  

  def after_save
    grouped_exams = GroupedExam.find_all_by_exam_group_id(self.exam.exam_group.id)
    unless grouped_exams.blank?
      grouped_exams.each do |grouped_exam|
        Rails.cache.delete("tabulation_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}")
        Rails.cache.delete("continues_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}")
        key = "student_exam_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}"
        Rails.cache.delete_matched(/#{key}*/)
      end
    end
  end

  def check_existing
    exam_score = ExamScore.find(:first,:conditions => {:exam_id => self.exam_id,:student_id => self.student_id})
    if exam_score
      self.id = exam_score.id
      self.instance_variable_set("@new_record",false)    #If the record exists,then make the new record as a copy of the existing one and allow rails to chhose
      #the update operation instead of insert.
    end
    return true
  end

  
  def validate
    unless self.marks.nil?
      unless self.exam.nil?
        if self.exam.maximum_marks.to_f < self.marks.to_f
          errors.add('marks','cannot be greater than maximum marks')
          return false
        else
          cur_student=self.student
          cur_batch_id=self.exam.exam_group.batch_id
          unless cur_student.nil?
            student_batches = cur_student.batch_students.collect(&:batch_id)
            if (cur_student.batch_id==cur_batch_id) or (student_batches.include? cur_batch_id )
              return true
            else
              errors.add :student_not_belongs_to_this_batch
              return false
            end
          else
            errors.add :student_is_not_exist
            return false
          end
        end
      end
    end
  end

  
  def calculate_percentage
    percentage = self.marks.to_f * 100 / self.exam.maximum_marks.to_f
  end

  def grouped_exam_subject_total(subject,student,type,batch = "")
    if batch == ""
      batch = student.batch.id
    end
    if type == 'grouped'
      grouped_exams = GroupedExam.find_all_by_batch_id(batch)
      exam_groups = []
      grouped_exams.each do |x|
        eg = ExamGroup.find(x.exam_group_id)
        exam_groups.push ExamGroup.find(x.exam_group_id)
      end
    else
      exam_groups = ExamGroup.find_all_by_batch_id(batch)
    end
    total_marks = 0
    exam_groups.each do |exam_group|
      unless exam_group.exam_type == 'Grades'
        exam = Exam.find_by_subject_id_and_exam_group_id(subject.id,exam_group.id)
        unless exam.nil?
          exam_score = ExamScore.find_by_student_id(student.id, :conditions=>{:exam_id=>exam.id})
          marks = exam_score.nil? ? 0 : exam_score.marks.nil? ? 0 : exam_score.marks
          total_marks = total_marks + marks unless exam_score.nil?
        end
      end
    end
    total_marks
  end

  

  def batch_wise_aggregate(student,batch)
    check = ExamGroup.find_all_by_batch_id(batch.id)
    var = []
    check.each do |x|
      if x.exam_type == 'Grades'
        var << 1
      end
    end
    if var.empty?
      grouped_exam = GroupedExam.find_all_by_batch_id(batch.id)
      if grouped_exam.empty?
        exam_groups = ExamGroup.find_all_by_batch_id(batch.id)
      else
        exam_groups = []
        grouped_exam.each do |x|
          exam_groups.push ExamGroup.find(x.exam_group_id)
        end
      end
      exam_groups.size
      max_total = 0
      marks_total = 0
      exam_groups.each do |exam_group|
        max_total = max_total + exam_group.total_marks(student)[1]
        marks_total = marks_total + exam_group.total_marks(student)[0]
      end
      aggr = (marks_total*100/max_total) unless max_total==0
    else
      aggr = 'nil'
    end
    
  end

  private
  def calculate_grade
    exam = self.exam
    exam_group = exam.exam_group
    exam_type = exam_group.exam_type
    unless exam_type == 'Grades'
      unless self.marks.nil?
        percent_score = self.marks * 100 / self.exam.maximum_marks
        grade = GradingLevel.percentage_to_grade(percent_score, self.exam.exam_group.batch_id)
        unless grade.nil?
          self.grading_level_id = grade.id if exam_type == 'MarksAndGrades'
        else
          self.grading_level_id = nil
        end
      else
        self.grading_level_id = nil
      end
    end
  end

end
