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
class ClassDesignation < ActiveRecord::Base
  validates_presence_of :name
  validates_numericality_of :cgpa,:if=>:has_gpa
  validates_numericality_of :marks,:greater_than => 0 ,:less_than_or_equal_to => 100, :if=>:has_cwa 

  belongs_to :course

  def has_gpa
    self.course.gpa_enabled?
  end

  def has_cwa
    self.course.cwa_enabled? or self.course.normal_enabled?
  end

  HUMANIZED_COLUMNS = {:cgpa => "CGPA"}

  def self.human_attribute_name(attribute)
    HUMANIZED_COLUMNS[attribute.to_sym] || super
  end
end
