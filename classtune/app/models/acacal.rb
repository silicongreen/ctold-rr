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

class Acacal < ActiveRecord::Base
  belongs_to :author, :class_name => 'User'
  has_many :batch_acacal, :dependent => :destroy
  has_many :department_acacal, :dependent => :destroy
  
  has_attached_file :attachment,
    :url => "/uploads/:class/:attachment/:id/:style/:attachment_fullname?:timestamp"

  validates_presence_of :title, :attachment

  default_scope :order => 'created_at DESC'

  cattr_reader :per_page
  @@per_page = 12
 
  
  def download_allowed_for user
    return true if user.admin?
    return true if user.employee?
    return true if user.student?
    return true if user.parent?
    false
  end

end