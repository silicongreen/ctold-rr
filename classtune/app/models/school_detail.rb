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
class SchoolDetail < ActiveRecord::Base
  has_attached_file :logo,
    :styles => { :original=> "x35"},
    :url => "/uploads/:class/:attachment/:id/:style/:attachment_fullname?:timestamp",
    :path => "public/uploads/:class/:attachment/:id/:style/:basename.:extension",
    :default_url  => '/images/application/dummy_logo.png',
    :default_path  => ':rails_root/public/images/application/dummy_logo.png'

  VALID_IMAGE_TYPES = ['image/gif', 'image/png','image/jpeg', 'image/jpg']

  validates_attachment_content_type :logo, :content_type =>VALID_IMAGE_TYPES,
    :message=>'Image can only be GIF, PNG, JPG',:if=> Proc.new { |p| !p.logo_file_name.blank? }
  validates_attachment_size :logo, :less_than => 512000,
    :message=>'must be less than 500 KB.',:if=> Proc.new { |p| p.logo_file_name_changed? }
  
  
  has_attached_file :cover,
    :styles => { :original=> "800x200"},
    :url => "/uploads/:class/:attachment/:id/:style/:attachment_fullname?:timestamp",
    :path => "public/uploads/:class/:attachment/:id/:style/:basename.:extension"
  
  validates_attachment_content_type :cover, :content_type =>VALID_IMAGE_TYPES,
    :message=>'Image can only be GIF, PNG, JPG',:if=> Proc.new { |p| !p.cover_file_name.blank? }
  validates_attachment_size :cover, :less_than => 512000,
    :message=>'must be less than 500 KB.',:if=> Proc.new { |p| p.cover_file_name_changed? }
end
