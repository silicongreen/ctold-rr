class OnlineExamAttendance < ActiveRecord::Base
  has_many :online_exam_score_details, :dependent => :destroy
  belongs_to :online_exam_group
  belongs_to :student

  validates_associated :online_exam_score_details
  accepts_nested_attributes_for :online_exam_score_details ,:reject_if=>:reject_exam_score , :allow_destroy=>true

  def reject_exam_score(attributed)
    exam_option_id=attributed["online_exam_option_id"].to_i
    if OnlineExamOption.find(exam_option_id).is_answer
      attributed["is_correct"] = true
    else
      attributed["is_correct"] = false
    end
    return false
  end

  def student_score
    score=self.online_exam_score_details
    score_all=score.group_by(&:online_exam_question_id)
    question_ids=score.collect(&:online_exam_question_id)
    correct_options=OnlineExamOption.all(:conditions=>{:online_exam_question_id=>question_ids,:is_answer=>true}).group_by(&:online_exam_question_id)
    active_question_ids=[]
    question_ids.each do |q|
      if score_all[q].count == correct_options[q].count
        flag=0
        score_all[q].each do |s|
          if s.is_correct==false
            flag=1
          end
        end
        if flag==0
          active_question_ids << q
        end
      end
    end
    score_total=self.online_exam_group.online_exam_questions.sum('mark',:conditions=>["online_exam_questions.id IN (?)",active_question_ids]).to_f
    return score_total
  end

  def student_full_name
    self.student.present?? self.student.full_name : archieved_student_name
  end

  def archieved_student_name
    student=ArchivedStudent.find_by_former_id(self.student_id)
    return student.full_name
  end

  def student_admission_no
    self.student.present?? self.student.admission_no : archieved_student_admission_no
  end

  def archieved_student_admission_no
    student=ArchivedStudent.find_by_former_id(self.student_id)
    return student.admission_no
  end

  def self.hard_delete(attendance_id)
    OnlineExamScoreDetail.delete_all(["online_exam_attendance_id IN(?)",attendance_id])
    OnlineExamAttendance.delete_all([ "id IN (?)",attendance_id])
  end

end
