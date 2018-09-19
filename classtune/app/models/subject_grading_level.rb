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

class SubjectGradingLevel < ActiveRecord::Base
  attr_accessor :type_data
  belongs_to :batch
  belongs_to :subject
  validates_presence_of :name, :grading_type
  validates_presence_of :min_score, :if => Proc.new { |subject_grading_level| subject_grading_level.grading_type}
  validates_presence_of :max_score, :if => Proc.new { |subject_grading_level| subject_grading_level.grading_type and subject_grading_level.type_data == "normal"}
#  validates_presence_of :credit_points, :if=>:batch_has_gpa
  validates_uniqueness_of :name, :scope => [:subject_id, :is_deleted],:case_sensitive => false
  validates_numericality_of :min_score ,:credit_points ,:greater_than_or_equal_to => 0, :message =>:must_be_positive ,:allow_blank=>true

  default_scope :order => 'min_score desc'
  
  after_save :delete_cache
  before_destroy :delete_cache 
  
  def delete_cache
    keygradinglevel = "subject_grading_level_"
    Rails.cache.delete_matched(/#{keygradinglevel}*/) 
  end

  def validate
    if self.min_score.to_i <= 100
      return true
    else
      errors.add_to_base :min_score_should_be_less_than_100
      return false
    end
  end
  
  

  def inactivate
    update_attribute :is_deleted, true
  end
  class << self
    def for_subject(subject_id)
      gradding_level = Rails.cache.fetch("subject_grading_level_#{subject_id}"){
          batch_grades = SubjectGradingLevel.find_all_by_subject_id(subject_id, :conditions=> 'is_deleted = false', :order => 'min_score desc')
          batch_grades
        }
      gradding_level
    end
    
    def percentage_to_grade(percent_score,batch_id,subject_id)
      if percent_score.to_s == "NaN"
          percent_score = 0
      end
      batch_grades = SubjectGradingLevel.for_subject(subject_id)
      grade = {}
      if batch_grades.empty?
        grade = GradingLevel.percentage_to_grade(percent_score,batch_id)
      else
        batch_grades.each do |gradevalue|
          if gradevalue.min_score <= percent_score.round
            grade = gradevalue
            break
          end  
        end
      end
      grade
    end
  end
end
