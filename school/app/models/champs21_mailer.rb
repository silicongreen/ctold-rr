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

class Champs21Mailer < ActionMailer::Base
  def email(sender,recipients, subject, message)
    recipient_emails = (recipients.class == String) ? recipients.gsub(' ','').split(',').compact : recipients.compact
    setup_email(sender, recipient_emails, subject, message)
  end

  protected
  def setup_email(sender, emails, subject, message)
    @from = sender
    @recipients = emails
    @subject = subject
    @sent_on = Time.now
    @body['message'] = message
  end
  
end
