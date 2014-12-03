require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_transport")


Champs21Plugin.register = {
  :name=>"champs21_transport",
  :description=>"Champs21 Transport Module",
  :auth_file=>"config/transport_auth.rb",
  :more_menu=>{:title=>"transport_label",:controller=>"transport",:action=>"dash_board",:target_id=>"more-parent"},
  :sub_menus=>[{:title=>"transport.set_routes",:controller=>"routes",:action=>"index",:target_id=>"champs21_transport"},
    {:title=>"transport.vehicles",:controller=>"vehicles",:action=>"index",:target_id=>"champs21_transport"},
    {:title=>"transport_text",:controller=>"transport",:action=>"index",:target_id=>"champs21_transport"},
    {:title=>"transport_fee_text",:controller=>"transport_fee",:action=>"index",:target_id=>"champs21_transport"}],
  :dashboard_menu=>{:title=>"transport_label",:controller=>"transport",:action=>"dash_board",\
      :options=>{:class=>"option_buttons",:id => "transport_button", :title => "manage_transport"}},
  :student_profile_more_menu=>{:title=>"transport_label",:destination=>{:controller=>"transport",:action=>"student_transport_details"}},
  :employee_profile_more_menu=>{:title=>"transport_label",:destination=>{:controller=>"transport",:action=>"employee_transport_details"}},
  :finance=>{:category_name=>"transport",:destination=>{:controller=>"transport_fee" , :action => "transport_fees_report"}},
  :css_overrides=>[{:controller=>"user",:action=>"dashboard"}],
  :autosuggest_menuitems=>[
    {:menu_type => 'link' ,:label => "autosuggest_menu.transport_text",:value =>{:controller => :transport,:action => :dash_board}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.set_routes",:value =>{:controller => :routes,:action => :index}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.add_routes",:value =>{:controller => :routes,:action => :new}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.vehicles",:value =>{:controller => :vehicles,:action => :index}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.add_vehicle",:value =>{:controller => :vehicles,:action => :new}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.search_transport_details",:value =>{:controller => :transport,:action => :index}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.transportation_details",:value =>{:controller => :transport,:action => :transport_details}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.transport_fee",:value =>{:controller => :transport_fee,:action => :index}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.transport_fee_collections",:value =>{:controller => :transport_fee,:action => :transport_fee_collections}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.view_transport_fee_collection",:value =>{:controller => :transport_fee,:action => :transport_fee_collection_view}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.transport_fee_search",:value =>{:controller => :transport_fee,:action => :transport_fee_search}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.transport_fee_student_defaulters",:value =>{:controller => :transport_fee,:action => :transport_fee_defaulters_view}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.transport_fee_employee_defaulters",:value =>{:controller => :transport_fee,:action => :employee_defaulters_transport_fee_collection}}
  ],
  :multischool_models=>%w{Route Transport TransportFee TransportFeeCollection Vehicle}
}

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

Champs21Transport.attach_overrides

if RAILS_ENV == 'development'
  ActiveSupport::Dependencies.load_once_paths.reject!{|x| x =~ /^#{Regexp.escape(File.dirname(__FILE__))}/}
end


