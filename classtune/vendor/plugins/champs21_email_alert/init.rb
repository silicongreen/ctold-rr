require 'translator'
require 'dispatcher'
require 'champs21'
require File.join(File.dirname(__FILE__), "lib", "champs21_email_alert")
require File.join(File.dirname(__FILE__), "lib", 'email_alert','alert_data')

Champs21Plugin.register = {
  :name=>"champs21_email_alert",
  :description=>"Champs21 Email Alert",
  :auth_file=>"config/email_alert_auth.rb",
  :more_menu=>{:title=>"email_send",:controller=>"email_alerts",:action=>"index",:target_id=>"more-parent"},
  :multischool_models=>%w{EmailAlert EmailSubscription},
  :multischool_classes=>%w{Champs21EmailAlertEmailMaker}

}

Champs21EmailAlert.attach_overrides

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end
