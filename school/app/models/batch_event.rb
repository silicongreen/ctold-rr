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

class BatchEvent < ActiveRecord::Base
  belongs_to :batch
  belongs_to :event
   def batch_event_emails
    s=self.batch.students.select{|s| (s.is_email_enabled)}
     s.collect(&:email).zip(s.collect(&:first_name))
  end
  def parent_event_emails
    email=[]
    students=self.batch.students.select{|s| (s.is_email_enabled)}
    students.each do |s| email=email+(s.immediate_contact.present?? (s.immediate_contact.email.zip(s.immediate_contact.first_name)):[]) ;end
    return email
  end
end
