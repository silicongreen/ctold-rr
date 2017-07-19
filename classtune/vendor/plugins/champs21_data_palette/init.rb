require 'dispatcher'
require 'champs21_data_palette/palette_text'
require File.join(File.dirname(__FILE__), "lib", "champs21_data_palette")

Champs21Plugin.register = {
  :name=>"champs21_data_palette",
  :description=>"Champs21 Data palettes",
  :auth_file=>"config/data_palette_auth.rb",
  :multischool_models=>%w{UserPalette}
}


Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

Dispatcher.to_prepare :champs21_data_palette do
  User.send(:has_many, :user_palettes)
  User.send(:has_many, :palettes, :through=>:user_palettes)
  News.send :include, Champs21DataPalette::NewsPaletteText
  Event.send :include, Champs21DataPalette::EventsPaletteText
  Student.send :include, Champs21DataPalette::StudentsPaletteText
  ArchivedStudent.send :include, Champs21DataPalette::ArchivedStudentsPaletteText
  Employee.send :include, Champs21DataPalette::EmployeesPaletteText
  ArchivedEmployee.send :include, Champs21DataPalette::ArchivedEmployeesPaletteText
  EmployeeAttendance.send :include, Champs21DataPalette::EmployeeAttendancePaletteText
  Exam.send :include, Champs21DataPalette::ExamPaletteText
  ApplyLeave.send :include, Champs21DataPalette::ApplyLeavePaletteText
  SmsLog.send :include, Champs21DataPalette::SmsPaletteText
  FinanceTransaction.send :include, Champs21DataPalette::FinancePaletteText
  TimetableEntry.send :include, Champs21DataPalette::TimetablePaletteText
  Task.send :include, Champs21DataPalette::TaskPaletteText if Champs21Plugin.plugin_installed?("champs21_task")
  GroupPost.send :include, Champs21DataPalette::DiscussionPaletteText if Champs21Plugin.plugin_installed?("champs21_discussion")
  BlogPost.send :include, Champs21DataPalette::BlogPaletteText if Champs21Plugin.plugin_installed?("champs21_blog")
  PollQuestion.send :include, Champs21DataPalette::PollPaletteText if Champs21Plugin.plugin_installed?("champs21_poll")
  BookMovement.send :include, Champs21DataPalette::LibraryPaletteText if Champs21Plugin.plugin_installed?("champs21_library")
  OnlineMeetingRoom.send :include, Champs21DataPalette::OnlineMeetingPaletteText if Champs21Plugin.plugin_installed?("champs21_bigbluebutton")
  Placementevent.send :include, Champs21DataPalette::PlacementPaletteText if Champs21Plugin.plugin_installed?("champs21_placement")
  GalleryPhoto.send :include, Champs21DataPalette::GalleryPaletteText if Champs21Plugin.plugin_installed?("champs21_gallery")
  User.send :include, Champs21DataPalette::BirthdayPaletteText
  User.send :include, Champs21DataPalette::UserMethod
  Attendance.send :include, Champs21DataPalette::AttendancePaletteText
  UserController.send :include, Champs21DataPalette::DashboardOverride
  Assignment.send :include, Champs21DataPalette::AssignmentPaletteText if Champs21Plugin.plugin_installed?("champs21_assignment")
end
