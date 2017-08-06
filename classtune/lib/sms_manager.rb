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

# Configure your SMS API settings
require 'net/http'
require 'yaml'
require 'translator'

class SmsManager
  attr_accessor :recipients, :message

  def initialize(message, recipients)
    @recipients = recipients.map{|r| r.gsub(' ','')}
    @message = CGI::escape message
    @message_without_encode = message
    @config = SmsSetting.get_sms_config
    unless @config.blank?
      @sendername = @config['sms_settings']['sendername']
      @sms_url = @config['sms_settings']['host_url']
      @username = @config['sms_settings']['username']
      @password = @config['sms_settings']['password']
      @success_code = @config['sms_settings']['success_code']
      @username_mapping = @config['parameter_mappings']['username']
      @username_mapping ||= 'username'
      @password_mapping = @config['parameter_mappings']['password']
      @password_mapping ||= 'password'
      @phone_mapping = @config['parameter_mappings']['phone']
      @phone_mapping ||= 'phone'
      @sender_mapping = @config['parameter_mappings']['sendername']
      @sender_mapping ||= 'sendername'
      @message_mapping = @config['parameter_mappings']['message']
      @message_mapping ||= 'message'
      unless @config['additional_parameters'].blank?
        @additional_param = ""
        @config['additional_parameters'].split(',').each do |param|
          @additional_param += "&#{param}"
        end
      end
    end
  end

  def perform
    if @config.present?
      message_log = SmsMessage.new(:body=> @message)
      message_log.save
      encoded_message = @message
      @sms_hash = {"user"=>@username,"pass"=>@password,"sid" =>@sendername}
     
      @i_sms_loop = 0
      @recipients.each do |recipient|
       if @i_sms_loop == 3
         message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>"Successfull")
         @sms_hash["sms[#{@i_sms_loop}][0]"] = recipient
         @sms_hash["sms[#{@i_sms_loop}][1]"] = @message_without_encode
         @sms_hash["sms[#{@i_sms_loop}][2]"] = @i_sms_loop
        
         api_uri = URI.parse(@sms_url)
         http = Net::HTTP.new(api_uri.host, api_uri.port)
         request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
         request.set_form_data(@sms_hash)
         
         http.request(request)
        
         sms_count = Configuration.find_by_config_key("TotalSmsCount")
         new_count = sms_count.config_value.to_i + 4
         sms_count.update_attributes(:config_value=>new_count)
         
         @sms_hash = {"user"=>@username,"pass"=>@password,"sid" =>@sendername}
       
         @i_sms_loop = 0
       elsif recipient.equal? @recipients.last
         message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>"Successfull")
         @sms_hash["sms[#{@i_sms_loop}][0]"] = recipient
         @sms_hash["sms[#{@i_sms_loop}][1]"] = @message_without_encode
         @sms_hash["sms[#{@i_sms_loop}][2]"] = @i_sms_loop
         
         api_uri = URI.parse(@sms_url)
         http = Net::HTTP.new(api_uri.host, api_uri.port)
         request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
         request.set_form_data(@sms_hash)
         http.request(request)
        
         sms_count = Configuration.find_by_config_key("TotalSmsCount")
         new_count = sms_count.config_value.to_i + 1+@i_sms_loop
         sms_count.update_attributes(:config_value=>new_count)
       else
         @sms_hash["sms[#{@i_sms_loop}][0]"] = recipient
         @sms_hash["sms[#{@i_sms_loop}][1]"] = @message_without_encode
         @sms_hash["sms[#{@i_sms_loop}][2]"] = @i_sms_loop
         message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>"Successfull")
         @i_sms_loop = @i_sms_loop+1
       end   
       
       
      end
    end
  end
end