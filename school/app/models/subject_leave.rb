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
class SubjectLeave < ActiveRecord::Base
  belongs_to :student
  belongs_to :batch
  belongs_to :subject
  belongs_to :employee
  belongs_to :class_timing

  validates_presence_of :subject_id
  validates_presence_of :batch_id
  validates_presence_of :student_id
  validates_presence_of :month_date
  validates_presence_of :class_timing_id
  validates_presence_of :reason

  named_scope :by_month_and_subject, lambda { |d,s| { :conditions  => { :month_date  => d.beginning_of_month..d.end_of_month , :subject_id => s} } }
  named_scope :by_month_batch_subject, lambda { |d,b,s| {  :conditions  => { :month_date  => d.beginning_of_month..d.end_of_month , :subject_id => s,:batch_id=>b} } }
  validates_uniqueness_of :student_id,:scope=>[:class_timing_id,:month_date],:message=>"already marked as absent"
  def validate
    unless student.nil?
      errors.add :attendance_before_the_date_of_admission  if self.month_date < self.student.admission_date unless month_date.nil?
    end
    errors.add(:month_date, :future_attendance_cannot_be_marked) if month_date > Configuration.default_time_zone_present_time.to_date
  end
end
