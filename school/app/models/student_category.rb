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

class StudentCategory < ActiveRecord::Base

  has_many :students
  has_many :fee_category ,:class_name =>"FinanceFeeCategory"
  before_destroy :check_dependence
  validates_presence_of :name
  validates_uniqueness_of :name, :scope=>:is_deleted,:case_sensitive => false, :if=> 'is_deleted == false'

  named_scope :active, :conditions => { :is_deleted => false}

  def empty_students
    Student.find_all_by_student_category_id(self.id).each do |s|
      s.update_attributes(:student_category_id=>nil)
    end

  end

  def check_dependence
    if Student.find_all_by_student_category_id(self.id).blank?
       errors.add_to_base :category_is_in_use
       return false
    end

  end
end
