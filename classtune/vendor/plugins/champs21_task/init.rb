require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_task")


Champs21Plugin.register = {
  :name=>"champs21_task",
  :description=>"Champs21 Task Module for Champs21 MS",
  :auth_file=>"config/task_auth.rb",
  :more_menu=>{:title=>"tasks_label",:controller=>"tasks",:action=>"index",:target_id=>"more-parent"},
  :multischool_models=>%w{Task TaskAssignee TaskComment}
}

Champs21Task.attach_overrides

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end
