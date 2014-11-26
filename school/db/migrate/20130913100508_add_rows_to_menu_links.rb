class AddRowsToMenuLinks < ActiveRecord::Migration
  def self.up
    academics_category = MenuLinkCategory.find_by_name("academics")
    collaboration_category = MenuLinkCategory.find_by_name("collaboration")
    reports_category = MenuLinkCategory.find_by_name("data_and_reports")
    administration_category = MenuLinkCategory.find_by_name("administration")

    MenuLink.create(:name=>'students',:target_controller=>'student',:target_action=>'index',:higher_link_id=>nil,:icon_class=>'student-icon',:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'students')
    MenuLink.create(:name=>'student_details',:target_controller=>'student',:target_action=>'index',:higher_link_id=>MenuLink.find_by_name('students').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'student_details')
    MenuLink.create(:name=>'student_admission',:target_controller=>'student',:target_action=>'admission1',:higher_link_id=>MenuLink.find_by_name('students').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'student_admission')
    MenuLink.create(:name=>'attendance',:target_controller=>'student_attendance',:target_action=>'index',:higher_link_id=>nil,:icon_class=>'attendance-icon',:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'attendance')
    MenuLink.create(:name=>'attendance_register',:target_controller=>'attendances',:target_action=>'index',:higher_link_id=>MenuLink.find_by_name('attendance').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'attendance_register')
    MenuLink.create(:name=>'attendance_report',:target_controller=>'attendance_reports',:target_action=>'index',:higher_link_id=>MenuLink.find_by_name('attendance').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'attendance_report')
    MenuLink.create(:name=>'settings',:target_controller=>'configuration',:target_action=>'index',:higher_link_id=>nil,:icon_class=>'settings-icon',:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'settings')
    MenuLink.create(:name=>'manage_course_batch',:target_controller=>'courses',:target_action=>'index',:higher_link_id=>MenuLink.find_by_name('settings').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'manage_course_batch')
    MenuLink.create(:name=>'manage_student_category',:target_controller=>'student',:target_action=>'categories',:higher_link_id=>MenuLink.find_by_name('settings').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'manage_student_category')
    MenuLink.create(:name=>'manage_subject',:target_controller=>'subjects',:target_action=>'index',:higher_link_id=>MenuLink.find_by_name('settings').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'manage_subject')
    MenuLink.create(:name=>'general_settings',:target_controller=>'configuration',:target_action=>'settings',:higher_link_id=>MenuLink.find_by_name('settings').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'general_settings')
    MenuLink.create(:name=>'add_admission_additional_detail',:target_controller=>'student',:target_action=>'add_additional_details',:higher_link_id=>MenuLink.find_by_name('settings').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'add_admission_additional_detail')
    MenuLink.create(:name=>'sms_module',:target_controller=>'sms',:target_action=>'index',:higher_link_id=>MenuLink.find_by_name('settings').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'sms_module')
    
    MenuLink.create(:name=>'timetable_text',:target_controller=>'timetable',:target_action=>'index',:higher_link_id=>nil,:icon_class=>'timetable-icon',:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'timetable_text',:link_type=>'general')
    MenuLink.create(:name=>'create_timetable',:target_controller=>'timetable',:target_action=>'new_timetable',:higher_link_id=>MenuLink.find_by_name_and_link_type('timetable_text','general').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'create_timetable')
    MenuLink.create(:name=>'edit_timetable',:target_controller=>'timetable',:target_action=>'edit_master',:higher_link_id=>MenuLink.find_by_name_and_link_type('timetable_text','general').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'edit_timetable')
    MenuLink.create(:name=>'set_class_timings',:target_controller=>'class_timing_sets',:target_action=>'new_batch_class_timing_set',:higher_link_id=>MenuLink.find_by_name_and_link_type('timetable_text','general').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'set_class_timings')
    MenuLink.create(:name=>'create_weekdays',:target_controller=>'weekday',:target_action=>'index',:higher_link_id=>MenuLink.find_by_name_and_link_type('timetable_text','general').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'create_weekdays')
    MenuLink.create(:name=>'view_timetable',:target_controller=>'timetable',:target_action=>'view',:higher_link_id=>MenuLink.find_by_name_and_link_type('timetable_text','general').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'view_timetable')
    MenuLink.create(:name=>'teacher_timetable',:target_controller=>'timetable',:target_action=>'teachers_timetable',:higher_link_id=>MenuLink.find_by_name_and_link_type('timetable_text','general').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'teacher_timetable')
    MenuLink.create(:name=>'institutional_timetable',:target_controller=>'timetable',:target_action=>'timetable',:higher_link_id=>MenuLink.find_by_name_and_link_type('timetable_text','general').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'institutional_timetable')
    MenuLink.create(:name=>'work_allotment',:target_controller=>'timetable',:target_action=>'work_allotment',:higher_link_id=>MenuLink.find_by_name_and_link_type('timetable_text','general').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'work_allotment')
    MenuLink.create(:name=>'timetable_tracker',:target_controller=>'timetable_tracker',:target_action=>'index',:higher_link_id=>MenuLink.find_by_name_and_link_type('timetable_text','general').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'timetable_tracker')
    MenuLink.create(:name=>'calendar_text',:target_controller=>'calendar',:target_action=>'index',:higher_link_id=>nil,:icon_class=>'calendar-icon',:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'calendar_text')
    MenuLink.create(:name=>'examination',:target_controller=>'exam',:target_action=>'index',:higher_link_id=>nil,:icon_class=>'examination-icon',:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'examination')
    MenuLink.create(:name=>'settings',:target_controller=>'exam',:target_action=>'settings',:higher_link_id=>MenuLink.find_by_name('examination').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'settings',:higher_link_id=>MenuLink.find_by_name('examination').id)
    MenuLink.create(:name=>'exam_management',:target_controller=>'exam',:target_action=>'create_exam',:higher_link_id=>MenuLink.find_by_name('examination').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'exam_management')
    MenuLink.create(:name=>'generate_reports',:target_controller=>'exam',:target_action=>'generate_reports',:higher_link_id=>MenuLink.find_by_name('examination').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'generate_reports')
    MenuLink.create(:name=>'report_center',:target_controller=>'exam',:target_action=>'report_center',:higher_link_id=>MenuLink.find_by_name('examination').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'report_center')
    MenuLink.create(:name=>'cce_reports',:target_controller=>'cce_reports',:target_action=>'index',:higher_link_id=>MenuLink.find_by_name('examination').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'cce_reports')
    
    MenuLink.create(:name=>'news_text',:target_controller=>'news',:target_action=>'index',:higher_link_id=>nil,:icon_class=>'news-icon',:link_type=>'general',:user_type=>nil,:menu_link_category_id=>collaboration_category.id) unless MenuLink.exists?(:name=>'news_text')
    MenuLink.create(:name=>'event_creations',:target_controller=>'event',:target_action=>'index',:higher_link_id=>nil,:icon_class=>'event-icon',:link_type=>'general',:user_type=>nil,:menu_link_category_id=>collaboration_category.id) unless MenuLink.exists?(:name=>'event_creations')
    MenuLink.create(:name=>'human_resource',:target_controller=>'employee',:target_action=>'hr',:higher_link_id=>nil,:icon_class=>'hr-icon',:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'human_resource')
    MenuLink.create(:name=>'setting',:target_controller=>'employee',:target_action=>'settings',:higher_link_id=>MenuLink.find_by_name('human_resource').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'setting')
    MenuLink.create(:name=>'employee_management_text',:target_controller=>'employee',:target_action=>'employee_management',:higher_link_id=>MenuLink.find_by_name('human_resource').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'employee_management_text')
    MenuLink.create(:name=>'employee_leave_management',:target_controller=>'employee',:target_action=>'employee_attendance',:higher_link_id=>MenuLink.find_by_name('human_resource').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'employee_leave_management')
    MenuLink.create(:name=>'create_payslip',:target_controller=>'employee',:target_action=>'payslip',:higher_link_id=>MenuLink.find_by_name('human_resource').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'create_payslip')
    MenuLink.create(:name=>'employee_search',:target_controller=>'employee',:target_action=>'search',:higher_link_id=>MenuLink.find_by_name('human_resource').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'employee_search')
    MenuLink.create(:name=>'employee_payslip',:target_controller=>'employee',:target_action=>'department_payslip',:higher_link_id=>MenuLink.find_by_name('human_resource').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'employee_payslip')
    MenuLink.create(:name=>'finance_text',:target_controller=>'finance',:target_action=>'index',:higher_link_id=>nil,:icon_class=>'finance-icon',:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'finance_text')
    MenuLink.create(:name=>'fees_text',:target_controller=>'finance',:target_action=>'fees_index',:higher_link_id=>MenuLink.find_by_name('finance_text').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'fees_text')
    MenuLink.create(:name=>'category',:target_controller=>'finance',:target_action=>'categories',:higher_link_id=>MenuLink.find_by_name('finance_text').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'category')
    MenuLink.create(:name=>'transactions',:target_controller=>'finance',:target_action=>'transactions',:higher_link_id=>MenuLink.find_by_name('finance_text').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'transactions')
    MenuLink.create(:name=>'donations',:target_controller=>'finance',:target_action=>'donation',:higher_link_id=>MenuLink.find_by_name('finance_text').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'donations')
    MenuLink.create(:name=>'automatic_transactions',:target_controller=>'finance',:target_action=>'automatic_transactions',:higher_link_id=>MenuLink.find_by_name('finance_text').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'automatic_transactions')
    MenuLink.create(:name=>'payslip_text',:target_controller=>'finance',:target_action=>'payslip_index',:higher_link_id=>MenuLink.find_by_name('finance_text').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'payslip_text')
    MenuLink.create(:name=>'asset_liability_management',:target_controller=>'finance',:target_action=>'asset_liability',:higher_link_id=>MenuLink.find_by_name('finance_text').id,:icon_class=>nil,:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'asset_liability_management')
    
    MenuLink.create(:name=>'user_text',:target_controller=>'user',:target_action=>'index',:higher_link_id=>nil,:icon_class=>'user-icon',:link_type=>'general',:user_type=>nil,:menu_link_category_id=>administration_category.id) unless MenuLink.exists?(:name=>'user_text')
    
    MenuLink.create(:name=>'my_profile',:target_controller=>'student',:target_action=>'profile',:higher_link_id=>nil,:icon_class=>'profile-icon',:link_type=>'own',:user_type=>'student',:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'my_profile',:user_type=>'student')
    MenuLink.create(:name=>'timetable_text',:target_controller=>'timetable',:target_action=>'student_view',:higher_link_id=>nil,:icon_class=>'timetable-icon',:link_type=>'own',:user_type=>'student',:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'timetable_text',:user_type=>'student')
    MenuLink.create(:name=>'academics',:target_controller=>'student',:target_action=>'reports',:higher_link_id=>nil,:icon_class=>'academics-icon',:link_type=>'own',:user_type=>'student',:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'academics')
    
    MenuLink.create(:name=>'my_profile',:target_controller=>'employee',:target_action=>'profile',:higher_link_id=>nil,:icon_class=>'profile-icon',:link_type=>'own',:user_type=>'employee',:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'my_profile',:user_type=>'employee')
    MenuLink.create(:name=>'leaves',:target_controller=>'employee_attendance',:target_action=>'leaves',:higher_link_id=>nil,:icon_class=>'leaves-icon',:link_type=>'own',:user_type=>'employee',:menu_link_category_id=>academics_category.id) unless MenuLink.exists?(:name=>'leaves')

  end

  def self.down
    MenuLink.destroy_all
  end
end
