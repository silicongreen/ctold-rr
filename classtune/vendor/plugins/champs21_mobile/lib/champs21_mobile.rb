module Champs21Mobile
  def self.attach_overrides
    Dispatcher.to_prepare :champs21_mobile do
      ApplicationController.instance_eval { include Champs21Mobile::MobileApplication }
      UserController.instance_eval { include Champs21Mobile::MobileUser }
      ReminderController.instance_eval { include Champs21Mobile::MobileReminder }
      CalendarController.instance_eval { include Champs21Mobile::MobileCalendar }
      TimetableController.instance_eval { include Champs21Mobile::MobileTimetable }
      AttendanceReportsController.instance_eval { include Champs21Mobile::MobileAttendanceReports }
      EmployeeAttendanceController.instance_eval { include Champs21Mobile::MobileEmployeeAttendance }
      AttendancesController.instance_eval { include Champs21Mobile::MobileAttendances }
      StudentController.instance_eval { include Champs21Mobile::MobileStudent }
    end
  end
end