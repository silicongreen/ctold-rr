require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_poll")

Champs21Plugin.register = {
  :name=>"champs21_poll",
  :description=>"Champs21 Poll Module",
  :auth_file=>"config/poll_auth.rb",
  :more_menu=>{:title=>"poll_label",:controller=>"poll_questions",:action=>"index",:target_id=>"more-parent"},
  :multischool_models=>%w{PollMember PollOption PollQuestion PollVote},
  #  :dashboard_menu=>{:title=>"poll_label",:controller=>"poll_questions",:action=>"index",\
  #  :options=>{:class => "option_buttons", :id => "online_poll_button", :title => "online_poll"}},
  #  :css_overrides=>[{:controller=>"user",:action=>"dashboard"}]
}

Champs21Poll.attach_overrides

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

