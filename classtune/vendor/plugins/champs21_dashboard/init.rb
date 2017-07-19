require 'dispatcher'
require 'champs21_dashboard/palette_text'
require File.join(File.dirname(__FILE__), "lib", "champs21_dashboard")

Champs21Plugin.register = {
  :name=>"champs21_dashboard",
  :description=>"Champs21 Data palettes",
  :auth_file=>"config/dashboard_auth.rb",
  :multischool_models=>%w{UserDashboard}
}


Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

Dispatcher.to_prepare :champs21_dashboard do
  User.send(:has_many, :user_dashboards)
  User.send(:has_many, :dashboards, :through=>:user_dashboards)
  News.send :include, Champs21Dashboard::NewsDashboardText
  Event.send :include, Champs21Dashboard::EventsDashboardText
  Student.send :include, Champs21Dashboard::StudentsDashboardText
  ArchivedStudent.send :include, Champs21Dashboard::ArchivedStudentsDashboardText
  Employee.send :include, Champs21Dashboard::EmployeesDashboardText
  ArchivedEmployee.send :include, Champs21Dashboard::ArchivedEmployeesDashboardText
  EmployeeAttendance.send :include, Champs21Dashboard::EmployeeAttendanceDashboardText
  Exam.send :include, Champs21Dashboard::ExamDashboardText
  ApplyLeave.send :include, Champs21Dashboard::ApplyLeaveDashboardText
  SmsLog.send :include, Champs21Dashboard::SmsDashboardText
  FinanceTransaction.send :include, Champs21Dashboard::FinanceDashboardText
  TimetableEntry.send :include, Champs21Dashboard::TimetableDashboardText
  Task.send :include, Champs21Dashboard::TaskDashboardText if Champs21Plugin.plugin_installed?("champs21_task")
  GroupPost.send :include, Champs21Dashboard::DiscussionDashboardText if Champs21Plugin.plugin_installed?("champs21_discussion")
  BlogPost.send :include, Champs21Dashboard::BlogDashboardText if Champs21Plugin.plugin_installed?("champs21_blog")
  PollQuestion.send :include, Champs21Dashboard::PollDashboardText if Champs21Plugin.plugin_installed?("champs21_poll")
  BookMovement.send :include, Champs21Dashboard::LibraryDashboardText if Champs21Plugin.plugin_installed?("champs21_library")
  OnlineMeetingRoom.send :include, Champs21Dashboard::OnlineMeetingDashboardText if Champs21Plugin.plugin_installed?("champs21_bigbluebutton")
  Placementevent.send :include, Champs21Dashboard::PlacementDashboardText if Champs21Plugin.plugin_installed?("champs21_placement")
  GalleryPhoto.send :include, Champs21Dashboard::GalleryDashboardText if Champs21Plugin.plugin_installed?("champs21_gallery")
  User.send :include, Champs21Dashboard::BirthdayDashboardText
  User.send :include, Champs21Dashboard::UserMethodDashboard
  Attendance.send :include, Champs21Dashboard::AttendanceDashboardText
#  UserController.send :include, Champs21Dashboard::DashboardOverride
  Assignment.send :include, Champs21Dashboard::AssignmentDashboardText if Champs21Plugin.plugin_installed?("champs21_assignment")
end
