class OnlineExamScoreDetail < ActiveRecord::Base
    belongs_to :online_exam_attendance
    belongs_to :online_exam_question
    belongs_to :online_exam_option
    validates_uniqueness_of :online_exam_option_id,:scope=>[:online_exam_question_id,:online_exam_attendance_id]
end
