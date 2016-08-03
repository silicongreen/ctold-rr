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

class EmployeeAdditionalDetail < ActiveRecord::Base
  belongs_to :employee
  belongs_to :additional_field

  validates_presence_of :additional_field_id
  
  def archive_employee_additional_detail(archived_employee)
    additional_detail_attributes = self.attributes
    additional_detail_attributes.delete "id"
    additional_detail_attributes["employee_id"] = archived_employee
    self.delete if ArchivedEmployeeAdditionalDetail.create(additional_detail_attributes)
  end

  def validate
    unless self.additional_field.nil?
      if self.additional_field.status == true
        if self.additional_field.is_mandatory == true
          unless self.additional_info.present?
            errors.add("additional_info","can't be blank")
          end
        end
      else
        if self.additional_field.is_mandatory == true
          unless self.additional_info.present?
            errors.add("additional_info","can't be blank")
          end
        end
      end
    end
  end

  def before_create
    if self.additional_info.present? and self.additional_field.status == true
      return true
    else
      return false
    end
  end

  def before_update
    if self.additional_info.present? and self.additional_field.status == true
      return true
    else
      self.destroy
    end
  end
end
