require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_online_exam")
require 'dispatcher'


Champs21Plugin.register = {
  :name=>"champs21_online_exam",
  :description=>"Champs21 Online Exam Module",
  :auth_file=>"config/online_exam_auth.rb",
  :more_menu=>{:title=>"online_exam_text",:controller=>"online_student_exam",:action=>"index",:target_id=>"more-parent"},
  :sub_menus=>[{:title=>"online_exam_text",:controller=>"online_exam",:action=>"index",:target_id=>"exam-parent"}],
  :online_exam_index_link=>{:title=>"online_exam_text",:destination=>{:controller=>"online_exam",:action=>"index"},:description=>"manage_online_exam_system"},
  :multischool_models=>%w{OnlineExamAttendance OnlineExamGroup OnlineExamOption OnlineExamQuestion OnlineExamScoreDetail}
  

}

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

Champs21OnlineExam.attach_overrides

if RAILS_ENV == 'development'
  ActiveSupport::Dependencies.load_once_paths.reject!{|x| x =~ /^#{Regexp.escape(File.dirname(__FILE__))}/}
end

