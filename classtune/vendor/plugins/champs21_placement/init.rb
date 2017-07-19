require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_placement")
require 'dispatcher'

Champs21Plugin.register = {
  :name=>"champs21_placement",
  :description=>"Champs21 Placement Module",
  :auth_file=>"config/placement_auth.rb",
  :more_menu=>{:title=>"placement",:controller=>"placementevents",:action=>"index",:target_id=>"more-parent"},
  :multischool_models=>%w{Placementevent PlacementRegistration}
}

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

Champs21Placement.attach_overrides

if RAILS_ENV == 'development'
  ActiveSupport::Dependencies.load_once_paths.reject!{|x| x =~ /^#{Regexp.escape(File.dirname(__FILE__))}/}
end
