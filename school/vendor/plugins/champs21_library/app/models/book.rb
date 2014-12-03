#Copyright 2010 teamCreative Private Limited
#This product includes software developed at
#Project Champs21 - http://www.champs21.com/
#
#Licensed under the Apache License, Version 2.0 (the "License");
#you may not use this file except in compliance with the License.
#You may obtain a copy of the License at
#
#  http://www.apache.org/licenses/LICENSE-2.0
#
#Unless required by applicable law or agreed to in writing,
#software distributed under the License is distributed on an
#"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
#KIND, either express or implied.  See the License for the
#specific language governing permissions and limitations
#under the License.
class Book < ActiveRecord::Base
  acts_as_taggable
  belongs_to :book_movement
  has_many :book_reservations, :dependent => :destroy
  has_many :book_additional_details, :dependent => :destroy
  validates_presence_of :book_number, :title, :author
  validates_uniqueness_of :book_number
  before_destroy :delete_dependency

  cattr_reader :per_page

  @@per_page = 25

  def validate
    if self.tag_list.present?
      t = self.tag_list
      t.each do|tag|
        if (tag.length > 30)
          self.errors.add_to_base(:custom_tag_tool_long)
          return false
        end
      end
    end
  end

  def get_student_id
    return Student.find_by_admission_no(self.book_movement.user.username).id
  end

  def get_employee_id
    return  Employee.find_by_employee_number(self.book_movement.user.username).id
  end

  def delete_dependency
    movements = BookMovement.find_all_by_book_id(self.id)
    BookMovement.destroy_all(:id => movements.map(&:id))
  end

end
