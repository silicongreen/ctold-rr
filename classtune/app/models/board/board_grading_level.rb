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

class BoardGradingLevel < ActiveRecord::Base
  validates_presence_of :name
  validates_uniqueness_of :name
  validates_numericality_of :min_score ,:credit_points ,:greater_than_or_equal_to => 0, :message =>:must_be_positive ,:allow_blank=>true
  default_scope :order => 'min_score desc'
  
  def delete_cache
    keygradinglevel = "board_grading_level"
    Rails.cache.delete_matched(/#{keygradinglevel}*/) 
  end

  def default
      gradding_level = Rails.cache.fetch("board_grading_level"){
          grades = BoardGradingLevel.find(:all, :order => 'min_score desc')
          grades
        }
      gradding_level
  end 
  def percentage_to_grade(percent_score)
    grade = {}
    grades = self.default
    grades.each do |gradevalue|
      if gradevalue.min_score <= percent_score.round
        grade = gradevalue
        break
      end  
    end
    grade
  end
  def grade_point_to_grade(grade_point)
    grade = {}
    grades = self.default
    grades.each do |gradevalue|
      if gradevalue.credit_points <= grade_point
        grade = gradevalue
        break
      end  
    end
    grade
  end
end
