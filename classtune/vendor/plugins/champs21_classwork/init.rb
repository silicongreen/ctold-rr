require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_classwork")

Champs21Plugin.register = {
  :name=>"champs21_classwork",
  :description=>"Champs21 Classwork Module",
  :auth_file=>"config/classwork_auth.rb",
  :more_menu=>{:title=>"classwork_text",:controller=>"classworks",:action=>"index",:target_id=>"more-parent"},
  :multischool_models=>%w{Classwork ClassworkAnswer}
}
Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

if RAILS_ENV == 'development'
  ActiveSupport::Dependencies.load_once_paths.reject!{|x| x =~ /^#{Regexp.escape(File.dirname(__FILE__))}/}
end