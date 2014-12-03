require 'translator'
require 'dispatcher'

require File.join(File.dirname(__FILE__), "lib", "champs21_mobile")

Champs21Plugin.register = {
  :name=>"champs21_mobile",
  :description=>"Champs21 Mobile",
  :auth_file=>"config/mobile_auth.rb"
}

Champs21Mobile.attach_overrides

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end