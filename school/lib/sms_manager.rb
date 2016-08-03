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
      request = "#{@sms_url}?#{@username_mapping}=#{@username}&#{@password_mapping}=#{@password}&#{@sender_mapping}=#{@sendername}&#{@message_mapping}=#{encoded_message}#{@additional_param}&#{@phone_mapping}="
      @recipients.each do |recipient|
        cur_request = request
        cur_request += "#{recipient}"
        begin
          uri = URI.parse(cur_request)
          http = Net::HTTP.new(uri.host, uri.port)
          if cur_request.include? "https://"
            http.use_ssl = true
            http.verify_mode = OpenSSL::SSL::VERIFY_NONE
          end
          get_request = Net::HTTP::Get.new(uri.request_uri)
          response = http.request(get_request)
          if response.body.present?
            message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>response.body)
            if @success_code.present?
              if response.body.to_s.include? @success_code
                sms_count = Configuration.find_by_config_key("TotalSmsCount")
                new_count = sms_count.config_value.to_i + 1
                sms_count.update_attributes(:config_value=>new_count)
              end
            end
          end
        rescue Timeout::Error => e
          message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>e.message)
        rescue Errno::ECONNREFUSED => e
          message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>e.message)
        end
      end
    end
  end
end