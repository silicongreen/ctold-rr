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

class Syllabus < ActiveRecord::Base
  belongs_to :batch
  belongs_to :exam_group
  belongs_to :author, :class_name => 'User'
  has_many :comments, :class_name => 'NewsComment'
  after_save :reload_news_bar
  before_destroy :delete_redactors
  after_destroy :reload_news_bar
  after_save :update_redactor
  attr_accessor :redactor_to_update, :redactor_to_delete

  validates_presence_of :title, :content

  default_scope :order => 'created_at DESC'

  cattr_reader :per_page
  xss_terminate :except => [:content]
  @@per_page = 12

  def self.get_latest
    Syllabus.find(:all, :limit => 3)
  end

  def reload_news_bar
    ActionController::Base.new.expire_fragment(Syllabus.cache_fragment_name)
  end

  def self.cache_fragment_name
    'Syllabus_latest_fragment'
  end
  
  def update_redactor
    RedactorUpload.update_redactors(self.redactor_to_update,self.redactor_to_delete)
  end

  def delete_redactors
    RedactorUpload.delete_after_create(self.content)
  end

end