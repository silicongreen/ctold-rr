require 'translator'
require 'fastercsv'

Dir[File.dirname(__FILE__) + '/lib/*.rb'].each {|file| require file }

Champs21Plugin.register = {
  :name=>"champs21_custom_report",
  :description=>"Champs21 Custom Report Module",
  :auth_file=>"config/custom_reports_auth.rb",
  :more_menu=>{:title=>"custom_report",:controller=>"custom_reports",:action=>"index",:target_id=>"more-parent"},
  :autosuggest_menuitems=>[
    {:menu_type => 'link' ,:label => "autosuggest_menu.custom_reports",:value =>{:controller => :custom_reports,:action => :index}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.new_student_report",:value =>{:controller => :custom_reports,:action => :new, :id=>'student'}},
    {:menu_type => 'link' ,:label => "autosuggest_menu.new_employee_report",:value =>{:controller => :custom_reports,:action => :new, :id=>'employee'}}
  ],
  :multischool_models=>%w{Report ReportColumn ReportQuery}
}

Champs21CustomReport.attach_overrides

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

ActionView::live_validations = false

