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

class ElectiveGroup < ActiveRecord::Base
  belongs_to :batch
  has_many :subjects

  validates_presence_of :name,:batch_id

  named_scope :for_batch, lambda { |b| { :conditions => { :batch_id => b, :is_deleted => false } } }
  named_scope :active, :conditions => {:is_deleted => false}
  
  def inactivate
    update_attribute(:is_deleted, true)
    subjects.map{|subject| subject.update_attributes(:is_deleted => true)}
  end
  
  def get_message( elective_active_batch_ids, active_batches_id, half_no_of_batches_active, execute = true )
    unless execute
      return ""
    end
    s_message = ""
    if active_batches_id.length <= half_no_of_batches_active
      s_message = "<p class='course_text'>This Elective group is not registered to: "
      ar_batches_name = Batch.find(:all, :conditions => ["id IN (?)", active_batches_id ], :group => "name").map{|b| b.name}
      ar_subjects = active_batches_id
    else
      s_message = "<p class='course_text'>This Elective group is only registered to: "
      ar_batches_name = Batch.find(:all, :conditions => ["id IN (?)", elective_active_batch_ids ], :group => "name").map{|b| b.name}
      ar_subjects = elective_active_batch_ids
    end

    i = 0
    ar_batches_name.each do |b|
      s_section_name = Course.find(:all, :conditions => ["batches.id IN (?) and batches.name = ?", ar_subjects, b ], :joins => "INNER JOIN batches ON batches.course_id = courses.id").map{|c| c.section_name}.join(', ')
      s_message = s_message + b + " shift, Section: " + s_section_name
      if ar_batches_name.length > i + 1
        s_message = s_message + " and "
      end
      i += 1
    end
    s_message = s_message + "</p>"
  end
end
