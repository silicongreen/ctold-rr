authorization do
  role :tokens do
    has_permission_on :oauth_user_tokens, :to=>[:index,:revoke]
  end
  
  role :oauth2_manage do
    has_permission_on :oauth_clients, :to=>[:index,:new,:create,:show,:edit,:update,:destroy]
  end

  role :admin do
    includes :oauth2_manage
    includes :tokens
    includes :basic
    has_permission_on :api_attendances, :to => [:index,:new,:create,:edit,:update,:show,:destroy]
    has_permission_on :api_batches, :to => [:index,:create,:update,:destroy]
    has_permission_on :api_biometric_informations, :to => [:show]
    has_permission_on :api_courses, :to => [:index,:create,:update,:destroy]
    has_permission_on :api_employee_attendances, :to => [:index,:create,:update,:destroy]
    has_permission_on :api_employee_categories, :to => [:index]
    has_permission_on :api_employee_departments, :to => [:index]
    has_permission_on :api_employee_grades, :to => [:index]
    has_permission_on :api_employee_positions, :to => [:index]
    has_permission_on :api_employee_leave_types, :to => [:index]
    has_permission_on :api_employees, :to => [:index,:show,:employee_structure,:create,:upload_photo,:destroy]
    has_permission_on :api_exam_groups, :to => [:index,:create]
    has_permission_on :api_exam_scores, :to => [:index]
    has_permission_on :api_grading_levels, :to => [:index]
    has_permission_on :api_news, :to => [:index]
    has_permission_on :api_schools, :to => [:index]
    has_permission_on :api_student_categories, :to => [:index]
    has_permission_on :api_students, :to => [:index,:show,:fee_dues,:student_structure,:create,:upload_photo,:destroy,:exam_report_profile]
    has_permission_on :api_subjects, :to => [:index,:new,:create,:edit,:update,:destroy,:show]
    has_permission_on :api_timetables, :to => [:index]
    has_permission_on :api_finance_transactions, :to => [:index]
    has_permission_on :api_payroll_categories, :to => [:index]
  end
  
  role :employee do
    includes :tokens
    includes :basic
    has_permission_on :api_attendances, :to => [:index,:new,:create,:edit,:update,:show,:destroy]
    has_permission_on :api_biometric_informations, :to => [:show]
    has_permission_on :api_employees, :to => [:show]
    has_permission_on :api_news, :to => [:index]
    has_permission_on :api_reminders, :to => [:create]
    has_permission_on :api_books, :to => [:index]
  end

  role :student do
    includes :basic
    includes :tokens
    has_permission_on :api_biometric_informations, :to => [:show]
    has_permission_on :api_news, :to => [:index]
    has_permission_on :api_books, :to => [:index]
    has_permission_on :api_students, :to => [:show]
    has_permission_on :api_timetables, :to => [:index]
  end

  role :parent do
    includes :basic
    includes :tokens
    has_permission_on :api_biometric_informations, :to => [:show]
    has_permission_on :api_news, :to => [:index]
  end

  role :basic do
    has_permission_on :api_events, :to => [:index]
    has_permission_on :api_reminders, :to => [:index]
    has_permission_on :api_timetables, :to => [:show]
  end

  role :student_attendance_register do
    has_permission_on :api_attendances, :to => [:index,:new,:create,:edit,:update,:show,:destroy]
  end

  role :subject_attendance do
    has_permission_on :api_attendances, :to => [:index,:new,:create,:edit,:update,:show,:destroy]
  end

  role :add_new_batch do
    has_permission_on :api_batches, :to => [:index,:create,:update,:destroy]
    has_permission_on :api_biometric_informations, :to => [:show]
    has_permission_on :api_courses, :to => [:index,:create,:update,:destroy]
    has_permission_on :api_students, :to => [:show]
    has_permission_on :api_subjects, :to => [:index,:new,:create,:edit,:update,:destroy,:show]
  end

  role :hr_basics do
    has_permission_on :api_biometric_informations, :to => [:show]
    has_permission_on :api_employee_categories, :to => [:index]
    has_permission_on :api_employee_departments, :to => [:index]
    has_permission_on :api_employee_grades, :to => [:index]
    has_permission_on :api_employee_positions, :to => [:index]
    has_permission_on :api_employees, :to => [:index,:show,:employee_structure,:create,:upload_photo]
    has_permission_on :api_payroll_categories, :to => [:index]
    has_permission_on :api_finance_transactions, :to => [:index]
  end
  
  role :employee_search do
    has_permission_on :api_biometric_informations, :to => [:show]
    has_permission_on :api_employees, :to => [:index,:show]
  end

  role :admission do
    has_permission_on :api_biometric_informations, :to => [:show]
    has_permission_on :api_students, :to => [:show,:create,:upload_photo]
  end
  
  role :students_control do
    has_permission_on :api_biometric_informations, :to => [:show]
    has_permission_on :api_students, :to => [:index,:show,:student_structure,:destroy]
  end
  
  role :students_view do
    has_permission_on :api_biometric_informations, :to => [:show]
    has_permission_on :api_students, :to => [:show]
  end

  role :employee_attendance do
    has_permission_on :api_employee_attendances, :to => [:index,:create,:update,:destroy]
    has_permission_on :api_employee_leave_types, :to => [:index]
    has_permission_on :api_employees, :to => [:index]
  end

  role :examination_control do
    has_permission_on :api_exam_groups, :to => [:index,:create]
    has_permission_on :api_exam_scores, :to => [:index]
    has_permission_on :api_grading_levels, :to => [:index]
    has_permission_on :api_students, :to => [:index,:exam_report_profile]
  end
  
  role :enter_results do
    has_permission_on :api_exam_groups, :to => [:index]
    has_permission_on :api_exam_scores, :to => [:index]
  end
  
  role :subject_exam do
    has_permission_on :api_exam_groups, :to => [:index]
    has_permission_on :api_exam_scores, :to => [:index]
  end

  role :manage_news do
    has_permission_on :api_news, :to => [:index]
  end

  role :finance_control do
    has_permission_on :api_payroll_categories, :to => [:index]
    has_permission_on :api_finance_transactions, :to => [:index]
    has_permission_on :api_students, :to => [:fee_dues]
  end

  role :general_settings do
    has_permission_on :api_schools, :to => [:index]
    has_permission_on :api_student_categories, :to => [:index]
  end

  role :subject_master do
    has_permission_on :api_subjects, :to => [:index,:new,:create,:edit,:update,:destroy,:show]
  end

  role :manage_timetable do
    has_permission_on :api_timetables, :to => [:index]
  end

  role :timetable_view do
    has_permission_on :api_timetables, :to => [:index]
  end

  role :hostel_admin do
    has_permission_on :api_hostels, :to => [:index]
  end

  role :transport_admin do
    has_permission_on :api_vehicles, :to => [:index]
  end

  role :librarian do
    has_permission_on :api_books, :to => [:index]
  end
end