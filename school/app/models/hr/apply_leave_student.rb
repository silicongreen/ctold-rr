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

class ApplyLeaveStudent < ActiveRecord::Base
  validates_presence_of :start_date, :end_date, :reason
  belongs_to :student
  
  attr_accessor :redactor_to_update, :redactor_to_delete
  
  cattr_reader :per_page
  @@per_page = 12

  def validate
    search = ApplyLeaveStudent.find(:all,:conditions => ["(student_id = ? AND (approved IS NULL OR approved = ?)) AND ((? BETWEEN start_date AND end_date) OR (? BETWEEN start_date AND end_date) OR (start_date BETWEEN ? AND ?) OR (end_date BETWEEN ? AND ?))",student_id,true,start_date,end_date,start_date,end_date,start_date,end_date])
    errors.add(:base, :same_range_of_date_exists) if(((search.count == 1 ) or (search.count == 2 )) and new_record?)
  end

  def check_leave_count

    unless self.start_date.nil? or self.end_date.nil?
      errors.add_to_base:end_date_cant_before_start_date if self.end_date < self.start_date

    end
    unless self.start_date.nil? or self.end_date.nil? or self.employee_leave_types_id.nil?
      leave = EmployeeLeave.find_by_student_id(self.student_id, :conditions=> "employee_leave_type_id = '#{self.employee_leave_types_id}'")
      leave_required = (self.end_date.to_date-self.start_date.to_date).numerator+1
      if self.start_date.to_date < self.employee.joining_date.to_date
        errors.add_to_base :date_marked_is_before_join_date

      else
        unless leave.nil?
          if leave.leave_taken.to_f == leave.leave_count.to_f
            errors.add_to_base :you_have_already_availed

          else
            if self.is_half_day == true
              new_leave_count = (leave_required)/2
              if leave.leave_taken.to_f+new_leave_count.to_f > leave.leave_count.to_f
                errors.add_to_base :no_of_leaves_exceeded_max_allowed
              end
            else
              new_leave_count = leave_required.to_f
              if leave.leave_taken.to_f+new_leave_count.to_f > leave.leave_count.to_f
                errors.add_to_base :no_of_leaves_exceeded_max_allowed

              end
            end
          end
        else
          errors.add_to_base :no_leave_assigned_yet
        end
      end
    end
    if self.errors.present?
      return false
    else
      return true
    end
  end

  def leave_status
    if self.viewed_by_manager and self.approved
      return "approved"
    else
      return "rejected"
    end
  end
  
  def leave_days
    if start_date==end_date
      start_date.strftime "%a,%d %b %Y"
    else
      "#{start_date.strftime "%a,%d %b %Y"} to #{end_date.strftime "%a,%d %b %Y"}"
    end
  end

   def update_redactor
    RedactorUpload.update_redactors(self.redactor_to_update,self.redactor_to_delete)
  end

  def delete_redactors
    RedactorUpload.delete_after_create(self.reason)
  end
  
end
