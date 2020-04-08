#Copyright 2010 teamCreative Private Limited
#This product includes software developed at
#Project Champs21 - http://www.champs21.com/
#
#Licensed under the Apache License, Version 2.0 (the "License");
#you may not use this file except in compliance with the License.
#You may obtain a copy of the License at
#
#  http://www.apache.org/licenses/LICENSE-2.0
#
#Unless required by applicable law or agreed to in writing,
#software distributed under the License is distributed on an
#"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
#KIND, either express or implied.  See the License for the
#specific language governing permissions and limitations
#under the License.
require File.join(File.dirname(__FILE__), "lib", "champs21_bigbluebutton")

Champs21Plugin.register = {
  :name=>"champs21_bigbluebutton",
  :description=>"Champs21 Module to integrate with BigBlueButton",
  :auth_file=>"config/online_meetings_auth.rb",
  :more_menu=>{:title=>"collaborate_text",:controller=>"online_meeting_rooms",:action=>"index",:target_id=>"more-parent"},
  :multischool_models=>%w{OnlineMeetingServer OnlineMeetingRoom OnlineMeetingMember OnlineMeetingSetting OnlineRandomMeetingName }
}

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end
