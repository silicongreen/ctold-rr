require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_assignment")

Champs21Plugin.register = {
  :name=>"champs21_assignment",
  :description=>"Champs21 Assignment Module",
  :auth_file=>"config/assignment_auth.rb",
  :more_menu=>{:title=>"assignment_text",:controller=>"assignments",:action=>"index",:target_id=>"more-parent"},
  :multischool_models=>%w{Assignment AssignmentAnswer}
}
Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

if RAILS_ENV == 'development'
  ActiveSupport::Dependencies.load_once_paths.reject!{|x| x =~ /^#{Regexp.escape(File.dirname(__FILE__))}/}
end