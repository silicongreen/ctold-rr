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
class CceGradeSet < ActiveRecord::Base
  has_many     :observation_groups
  has_many     :cce_grades

  validates_presence_of :name

  before_destroy :check_dependencies

  def grade_string_for(point)
    grade_obj = cce_grades.select{|g| g.grade_point.to_i == point.to_i}.first
    grade_obj.nil? ? "No Grade" : grade_obj.name
  end

  def max_grade_point
    cce_grades.collect(&:grade_point).max || 1
  end

  private

  def check_dependencies
    !(cce_grades.present? or observation_groups.present?)
  end

end
