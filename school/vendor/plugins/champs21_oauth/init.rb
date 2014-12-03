require 'oauth2'
require File.join(File.dirname(__FILE__), "lib", "champs21_oauth")

Champs21Plugin.register = {
  :name=>"champs21_oauth",
  :description=>"Champs21 Oauth",
  :auth_file=>"config/oauth_auth.rb",
  :multi_school_settings_hook=>{:title=>"Google OAuth",:destination=>{:controller=>:oauth_settings,:action=>:settings,:provider=>"google"}}
}

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

Champs21Oauth.attach_overrides

if RAILS_ENV == 'development'
  ActiveSupport::Dependencies.load_once_paths.reject!{|x| x =~ /^#{Regexp.escape(File.dirname(__FILE__))}/}
end
