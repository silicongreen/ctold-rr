class OnlineExamQuestion < ActiveRecord::Base
  has_many :online_exam_options , :dependent => :destroy
  has_many :online_exam_score_details
  belongs_to :online_exam_group
  validates_presence_of :question, :mark
  validates_associated :online_exam_options, :on=>:create
  accepts_nested_attributes_for :online_exam_options
  attr_accessor :option_count
  validates_numericality_of :mark, :less_than_or_equal_to=> 100,:greater_than=> 0

  attr_accessor :redactor_to_update, :redactor_to_delete
    
  before_create :min_one_answer
  xss_terminate :except => [ :question ]

  def min_one_answer
    flag = false
    self.online_exam_options.each do |v|
      flag = true if v.is_answer
    end
    if flag
      return true
    else
      errors.add_to_base:atleast_one_option_must_be_answer
      return false
    end
  end

  def update_redactor
    RedactorUpload.update_redactors(self.redactor_to_update,self.redactor_to_delete)
  end

  def delete_redactors
    RedactorUpload.delete_after_create(self.content)
  end
end
