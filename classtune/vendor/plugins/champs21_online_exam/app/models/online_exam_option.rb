class OnlineExamOption < ActiveRecord::Base
  belongs_to :online_exam_question
  has_many :online_exam_score_details

  validates_presence_of :option

  attr_accessor :redactor_to_update, :redactor_to_delete
  
  xss_terminate :except => [ :option ]

  def after_save
    question = OnlineExamQuestion.find_by_id(self.online_exam_question_id)
    unless question.blank?
     Rails.cache.delete("online_exam_options_#{question.online_exam_group_id}")
    end
  end
  def update_redactor
    RedactorUpload.update_redactors(self.redactor_to_update,self.redactor_to_delete)
  end

  def delete_redactors
    RedactorUpload.delete_after_create(self.content)
  end
  
end
