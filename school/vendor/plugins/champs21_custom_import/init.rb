require 'translator'
require 'dispatcher'
require 'fastercsv'
require File.join(File.dirname(__FILE__), "lib", "champs21_custom_import")

Champs21Plugin.register = {
  :name=>"champs21_custom_import",
  :description=>"Champs21 Data Imports Module",
  :auth_file=>"config/champs21_custom_import_auth.rb",
  :more_menu=>{:title=>"champs21_custom_import_label",:controller=>"exports",:action=>"index",:target_id=>"more-parent"},
  :multischool_models=>%w{Export Import ImportLogDetail},
  :school_specific=>true
}

Champs21CustomImport.attach_overrides

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

