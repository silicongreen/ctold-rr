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

class News < ActiveRecord::Base
  belongs_to :author, :class_name => 'User'
  has_many :comments, :class_name => 'NewsComment'
  has_many :batch_news, :dependent => :destroy
  has_many :user_news, :dependent => :destroy
  has_many :department_news, :dependent => :destroy
  after_save :reload_news_bar
  before_destroy :delete_redactors
  after_destroy :reload_news_bar
  after_save :update_redactor
  attr_accessor :redactor_to_update, :redactor_to_delete
  
  has_attached_file :attachment,
    :url => "/uploads/:class/:attachment/:id/:style/:attachment_fullname?:timestamp"
  
  VALID_IMAGE_TYPES = ['image/gif', 'image/png','image/jpeg', 'image/jpg']
  has_attached_file :icon,
    :styles => { :original=> "50x50"},
    :url => "/uploads/:class/icon/:id/:style/:attachment_fullname"
  
  validates_attachment_content_type :icon, :content_type =>VALID_IMAGE_TYPES,
    :message=>'Image can only be GIF, PNG, JPG',:if=> Proc.new { |p| !p.icon_file_name.blank? }
  validates_attachment_size :icon, :less_than => 512000,
    :message=>'must be less than 500 KB.',:if=> Proc.new { |p| p.icon_file_name_changed? }

  validates_presence_of :title, :content

  default_scope :order => 'created_at DESC'

  cattr_reader :per_page
  xss_terminate :except => [:content]
  @@per_page = 12
  
  def download_allowed_for user
    return true if user.admin?
    return true if user.employee?
    return true if user.student?
    return true if user.parent?
    false
  end

  def self.get_latest
    News.find(:all, :limit => 3)
  end

  def reload_news_bar
    ActionController::Base.new.expire_fragment(News.cache_fragment_name)
  end

  def self.cache_fragment_name
    'News_latest_fragment'
  end
  
  def update_redactor
    RedactorUpload.update_redactors(self.redactor_to_update,self.redactor_to_delete)
  end

  def delete_redactors
    RedactorUpload.delete_after_create(self.content)
  end

end