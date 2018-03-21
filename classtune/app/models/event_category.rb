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

class EventCategory < ActiveRecord::Base
  
  has_many :events
  before_destroy :check_dependence
  validates_presence_of :name

  named_scope :active_for_event, :conditions => { :status => 1,:is_club => 0}
  named_scope :active_for_club, :conditions => { :status => 1,:is_club => 1}
  
  VALID_IMAGE_TYPES = ['image/gif', 'image/png','image/jpeg', 'image/jpg']



  def check_dependence
    if Event.find_all_by_event_category_id(self.id).blank?
       errors.add_to_base :category_is_in_use
       return false
    end

  end
end
