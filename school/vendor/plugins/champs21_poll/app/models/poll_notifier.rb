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

class PollNotifier < ActionMailer::Base
  def poll_notify(latest_poll)
    @bcc = User.active.find(:all,:conditions=> 'employee=1 or admin=1').collect{|u| u.email}.join(",")
    @from = latest_poll.poll_creator.email
    @subject = "New Poll #{latest_poll.title} "
    @sent_on = Time.now
    @content_type = "text/html"
    @body[:url] = url_for(:host => "hr.champs21.com", :controller => "poll_questions", :id => latest_poll.id, :action => "voting")
    @body[:sender] = latest_poll.poll_creator.first_name
  end
end