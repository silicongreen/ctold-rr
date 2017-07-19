require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_discipline")
require 'dispatcher'
Champs21Plugin.register = {
  :name=>"champs21_discipline",
  :auth_file => "config/discipline_auth.rb",
  :description=>"Champs21 Discipline",
  :more_menu=>{:title=>"discipline",:controller=>"discipline_complaints",:action=>"index",:target_id=>"more-parent"},
  :multischool_models=>%w{DisciplineComplaint DisciplineParticipation DisciplineComment DisciplineAction DisciplineAttachment}
}

Champs21Discipline.attach_overrides

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end