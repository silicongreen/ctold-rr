ActionController::Routing::Routes.draw do |map|
  map.resources :schools,:member=>{:add_domain=>:post,:delete_domain=>:get,:profile=>:get,:domain=>:get}, :collection=>{:search=>:get} do |school|
    school.resource :available_plugin, :member=>{:plugin_list=>:get}
  end
  map.login_admin_users '/login', :controller=>:admin_users, :action=>:login
  map.logout_admin_users '/logout', :controller=>:admin_users, :action=>:logout
  map.forgot_password_admin_users '/forgot_password', :controller=>:admin_users, :action=>:forgot_password
  map.resources :admin_users,:except=>[:index], :collection=>{:find_stats=>[:get,:post]}, :member=>{:change_password=>[:get, :put],:profile=>:get,:reset_password=>[:get,:post],:set_new_password=>[:get,:post]}

  map.resources :multi_school_groups,:member=>{:edit_profile=>[:get,:put],:add_domain=>:post,:delete_domain=>:get,:domain=>:get, :assign_plugins=>[:get,:post], :plugin_list=>:get,:profile=>:get, :assign_schools=>[:get,:put]} do |msg|
    msg.resource :available_plugin, :member=>{:plugin_list=>:get}
    msg.resources :admin_users, :only=>[:index]
    msg.resources :schools, :only=>[], :collection=>{:list_schools=>:get}
  end

  map.resource :additional_setting, :path_prefix=>':owner_type/:owner_id/:type', :member=>{:settings_list=>:get,:check_smtp_settings=>[:get,:post]},:except => [:show]
  map.resource :plugin_setting, :only=>[],:collection=>{:settings=>[:get,:post]}

end
