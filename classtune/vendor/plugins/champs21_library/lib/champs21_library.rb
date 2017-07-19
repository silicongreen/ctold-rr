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
#
#under the License.
# Champs21Library
require 'dispatcher'
module Champs21Library
  def self.attach_overrides
    Dispatcher.to_prepare :champs21_library do
      ::Student.instance_eval { has_many :book_movements, :through=>:user }
      ::Student.instance_eval { has_many :book_reservations, :through=>:user }
      ::Employee.instance_eval { has_many :book_movements, :through=>:user }
      ::Employee.instance_eval { has_many :book_reservations, :through=>:user }
      ::Student.instance_eval { include Champs21LibraryBookMovement }
      ::Employee.instance_eval { include Champs21LibraryBookMovement }
      ::User.instance_eval { include UserExtension }
    end
  end
  
#  def self.student_profile_hook #library card is not used anymore in champs21.
#    "shared/student_profile"
#  end

  def self.student_dependency_hook
    "shared/student_dependency"
  end

  def self.employee_dependency_hook
    "shared/employee_dependency"
  end

  def self.dependency_check(record,type)
    if record.class.to_s == "Student" or record.class.to_s == "Employee"
      return true if record.book_movements.all(:conditions=>"status = 'Renewed' or status = 'Issued' ").present?
    end
    return false
  end


  module Champs21LibraryBookMovement
    def issued_books
      self.book_movements.all(:conditions=>"status = 'Issued' or status = 'Renewed'")
    end
  end

  module UserExtension
    def self.included(base)
      base.instance_eval do
        has_many :book_movements, :dependent=>:destroy
        has_many :book_reservations, :dependent=>:destroy
        before_destroy :clear_book_movements
      end
    end
      
    def  clear_book_movements
      self.book_movements.destroy_all
      self.book_reservations.destroy_all
    end
  end
end

