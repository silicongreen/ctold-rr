require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_pay")

Champs21Plugin.register = {
  :name=>"champs21_pay",
  :description=>"Champs21 Pay Module",
  :auth_file=>"config/champs21_pay_auth.rb",
  :more_menu=>{:title=>"champs21_pay_label",:controller=>"online_payments",:action=>"index",:target_id=>"more-parent"},
  :multischool_models=>%w{Payment PaymentConfiguration},
  :school_specific=>true
}

Champs21Pay.attach_overrides

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

