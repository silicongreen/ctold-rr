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
class SmsLog < ActiveRecord::Base
  belongs_to :sms_message, :class_name => 'SmsMessage'

  def self.get_sms_logs(page = 1)
    SmsLog.paginate(:order=>"id DESC", :page => page, :per_page => 30)
  end
  
  def self.get_filter_sms_logs(mobile = nil, start_date = nil, end_date = nil)
    filter_data = nil
    unless start_date.blank? and end_date.blank?
      date1 = start_date.to_date.strftime("%Y-%m-%d")
      date2 = end_date.to_date.strftime("%Y-%m-%d")
      filter_data =  SmsLog.find(:all,:conditions=> ["DATE(created_at) >= '#{date1}' AND DATE(created_at) <= '#{date2}'"])
    end
    unless mobile.blank?
      filter_data = SmsLog.find(:all, :conditions=> ["mobile = #{mobile}"])
    end
    return filter_data
  end
  
  def self.get_sms_logs_by_date(dt)
    today = dt.to_date.strftime("%Y-%m-%d")
    return SmsLog.find(:all,:conditions=> ["DATE(created_at) = '#{today}'"], :select => "id")
  end

  def self.default_time_zone_present_time(time_stamp)
    server_time = time_stamp
    server_time_to_gmt = server_time.getgm
    local_tzone_time = server_time
    time_zone = Configuration.find_by_config_key("TimeZone")
    unless time_zone.nil?
      unless time_zone.config_value.nil?
        zone = TimeZone.find(time_zone.config_value)
        if zone.difference_type=="+"
          local_tzone_time = server_time_to_gmt + zone.time_difference
        else
          local_tzone_time = server_time_to_gmt - zone.time_difference
        end
      end
    end
    return local_tzone_time
  end

end
