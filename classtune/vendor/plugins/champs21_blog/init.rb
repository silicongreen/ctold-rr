# Include hook code here
require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_blog")

Champs21Plugin.register = {
  :name=>"champs21_blog",
  :description=>"Champs21 Blog Module",
  :auth_file=>"config/blog_auth.rb",
  :dashboard_menu=>{:title=>"blog_text",:controller => "blog_posts",:action => "index",\
      :options=>{:class=>"option_buttons",:id => "blog_button", :title => "manage_blog"}},
  :more_menu=>{:title=>"blog_text",:controller => "blog_posts",:action => "index",:target_id=>"more-parent"},
  :css_overrides=>[{:controller=> "user",:action=>"dashboard"}],
  :multischool_models=>%w{Blog BlogPost BlogComment},
}

Champs21Blog.attach_overrides

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end



