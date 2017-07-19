require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_fee_import")

Champs21Plugin.register = {
  :name=>"champs21_fee_import",
  :description=>"Champs21 Fee Import Module",
  :auth_file=>"config/fee_import_auth.rb",
  :instant_fees_index_link=>{:title=>"fee_imports",:destination=>{:controller=>"fee_imports",:action=>"select_student"},:description=>"manage_imported_fees"}
}

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

Champs21FeeImport.attach_overrides

if RAILS_ENV == 'development'
  ActiveSupport::Dependencies.load_once_paths.reject!{|x| x =~ /^#{Regexp.escape(File.dirname(__FILE__))}/}
end
