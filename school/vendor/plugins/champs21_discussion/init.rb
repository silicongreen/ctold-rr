require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_discussion")
require 'dispatcher'

Champs21Plugin.register = {
  :name=>"champs21_discussion",
  :description=>"Champs21 Discussion Module for Champs21 MS",
  :auth_file=>"config/discussion_auth.rb",
  :more_menu=>{:title=>"discussion",:controller=>"groups",:action=>"index",:target_id=>"more-parent"},
  :multischool_models=>%w{Group GroupFile GroupMember GroupPost GroupPostComment}

}

Champs21Discussion.attach_overrides

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

