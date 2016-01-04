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

class DelayedReminderJob

  def initialize(*args)
    opts = args.extract_options!
    @sender_id = opts[:sender_id]
    @recipient_ids = Array(opts[:recipient_ids]).flatten.uniq
    @subject = opts[:subject]
    @message = opts[:message]
    @body = opts[:body]
    
    @rid = 0;
    @rtype = 0;
    
    @student_id = {};
    @batch_id = {};
    
    unless opts[:student_id].nil?
      @student_id = opts[:student_id]
    end
    
    unless opts[:batch_id].nil?
      @batch_id = opts[:batch_id]
    end
    
    
    unless opts[:rid].nil?
      @rid = opts[:rid]
    end
    unless opts[:rtype].nil?
      @rtype = opts[:rtype]
    end  
  end

  def perform
    require 'net/http'
    require 'uri'
    require "yaml"

   
    @recipient_ids.each do |r_id|
      
      student_id = 0
      batch_id = 0
      
      unless @student_id[r_id].nil?
        student_id = @student_id[r_id]
      end
      
      unless @batch_id[r_id].nil?
        batch_id = @batch_id[r_id]
      end
      
      @reminder = Reminder.new(
        :sender => @sender_id,
        :recipient => r_id,
        :subject => @subject,
        :body => @body,
        :rid => @rid,
        :student_id => student_id,
        :batch_id => batch_id,
        :rtype => @rtype
      )
      
      if @reminder.save
        champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
        notification_url = champs21_api_config['notification_url']
        uri = URI(notification_url)
        http = Net::HTTP.new(uri.host, uri.port)
        auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
        auth_req.set_form_data({"user_id" => r_id, "notification_id" => @reminder.id})
        auth_res = http.request(auth_req)
      end
      
 
     
    end
    

  end

end
