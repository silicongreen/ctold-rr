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
require 'net/https'
require 'uri'
require "yaml"
require 'translator'

class DelayedUpdateClassPay
  attr_accessor :student,:new_record, :employee, :school_code,:student_id,:guardain_id

  def initialize(student, new_record,employee, school_code, student_id, guardain_id)
    @student = student
    @employee = employee
    @school_code = school_code
    @student_id = student_id
    @guardain_id = guardain_id 
    @new_record = new_record
  end

  def perform
    api_endpoint = "https://pay.classtune.com/"
    school_array = ['bncd','ess','sis','nascd']
    
    if @new_record?
     
    end  
    unless api_link.blank?
      parsed_url = api_endpoint+api_link
      uri = URI(parsed_url)
      http = Net::HTTP.new(uri.host, uri.port)
      http.use_ssl = true
      @data = http.get(uri.request_uri)
    end
  end
end  
