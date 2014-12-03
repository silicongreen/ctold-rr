require 'oauth2'
require File.join(File.dirname(__FILE__), "lib", "champs21_google_doc")

Champs21Plugin.register = {
  :name=>"champs21_google_doc",
  :description=>"Champs21 Google Doc",
  :auth_file=>"config/google_docs_auth.rb",
  :more_menu=>{ :title=>"google_docs", :controller=>"google_docs", :action=>"index", :target_id=>"more-parent" },
  :sub_menus=>[{:title=>"view_all_docs",:controller=>"google_docs",:action=>"index",:target_id=>"champs21_google_doc"},
    {:title=>"upload_document",:controller=>"google_docs",:action=>"upload",:target_id=>"champs21_google_doc"}]
}

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

if RAILS_ENV == 'development'
  ActiveSupport::Dependencies.load_once_paths.reject!{|x| x =~ /^#{Regexp.escape(File.dirname(__FILE__))}/}
end
