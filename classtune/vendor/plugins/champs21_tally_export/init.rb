require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_tally_export")


Champs21Plugin.register = {
  :name=>"champs21_tally_export",
  :description=>"Champs21 Tally Export Module",
  :auth_file=>"config/tally_export_auth.rb",
  :more_menu=> {:title=>"tally_export_text",:controller=>"tally_exports",:action=>"index",:target_id=>"finance_menu"},
  :generic_hook => {:title=>"tally_export_text", :source=>{:controller=>"finance", :action=>"index" }, :destination=>{:controller=>"tally_exports",:action=>"index"},:description=>"manage_tally_exports",:menu_id=>"finance_menu"},
  :multischool_models=>%w{TallyExportConfiguration TallyCompany TallyVoucherType TallyAccount TallyLedger TallyExportFile},
  :multischool_classes=>%w{TallyExportJob TallyManualSyncJob TallyBulkExportJob}
}

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

Champs21TallyExport.attach_overrides

if RAILS_ENV == 'development'
  ActiveSupport::Dependencies.load_once_paths.reject!{|x| x =~ /^#{Regexp.escape(File.dirname(__FILE__))}/}
end

