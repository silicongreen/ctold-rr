# This file is auto-generated from the current state of the database. Instead of editing this file, 
# please use the migrations feature of Active Record to incrementally modify your database, and
# then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your database schema. If you need
# to create the application database on another system, you should be using db:schema:load, not running
# all the migrations from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended to check this file into your version control system.

ActiveRecord::Schema.define(:version => 201401221000010) do

  create_table "YiiCache", :force => true do |t|
    t.integer "expire", :null => false
    t.binary  "value",  :null => false
  end

  create_table "additional_exam_groups", :force => true do |t|
    t.string  "name"
    t.integer "batch_id"
    t.string  "exam_type"
    t.boolean "is_published",     :default => false
    t.boolean "result_published", :default => false
    t.string  "students_list"
    t.date    "exam_date"
  end

  create_table "additional_exam_scores", :force => true do |t|
    t.integer  "student_id"
    t.integer  "additional_exam_id"
    t.decimal  "marks",              :precision => 7, :scale => 2
    t.integer  "grading_level_id"
    t.string   "remarks"
    t.boolean  "is_failed"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "additional_exams", :force => true do |t|
    t.integer  "additional_exam_group_id"
    t.integer  "subject_id"
    t.datetime "start_time"
    t.datetime "end_time"
    t.integer  "maximum_marks"
    t.integer  "minimum_marks"
    t.integer  "grading_level_id"
    t.integer  "weightage",                :default => 0
    t.integer  "event_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "additional_field_options", :force => true do |t|
    t.integer  "additional_field_id"
    t.string   "field_option"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "additional_field_options", ["school_id"], :name => "index_additional_field_options_on_school_id", :limit => {"school_id"=>nil}

  create_table "additional_fields", :force => true do |t|
    t.string   "name"
    t.boolean  "status"
    t.boolean  "is_mandatory", :default => false
    t.string   "input_type"
    t.integer  "priority"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "additional_fields", ["school_id"], :name => "index_additional_fields_on_school_id", :limit => {"school_id"=>nil}

  create_table "additional_report_csvs", :force => true do |t|
    t.string   "model_name"
    t.string   "method_name"
    t.text     "parameters"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "csv_report_file_name"
    t.string   "csv_report_content_type"
    t.integer  "csv_report_file_size"
    t.datetime "csv_report_updated_at"
    t.integer  "school_id"
  end

  add_index "additional_report_csvs", ["school_id"], :name => "index_additional_report_csvs_on_school_id", :limit => {"school_id"=>nil}

  create_table "additional_settings", :force => true do |t|
    t.integer  "owner_id"
    t.string   "owner_type"
    t.text     "settings"
    t.string   "type"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "additional_settings", ["id", "type"], :name => "index_additional_settings_on_id_and_type", :limit => {"id"=>nil, "type"=>nil}
  add_index "additional_settings", ["owner_id", "owner_type", "type"], :name => "index_of_owner_on_setting_type", :limit => {"owner_id"=>nil, "owner_type"=>nil, "type"=>nil}
  add_index "additional_settings", ["owner_id", "owner_type"], :name => "index_additional_settings_on_owner_id_and_owner_type", :limit => {"owner_id"=>nil, "owner_type"=>nil}

  create_table "admin_users", :force => true do |t|
    t.string   "username"
    t.string   "password_salt"
    t.string   "crypted_password"
    t.string   "email"
    t.string   "full_name"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "type"
    t.integer  "higher_user_id"
    t.boolean  "is_deleted",                :default => false
    t.text     "description"
    t.string   "contact_no"
    t.string   "reset_password_code"
    t.datetime "reset_password_code_until"
  end

  add_index "admin_users", ["id", "type"], :name => "index_admin_users_on_id_and_type", :limit => {"id"=>nil, "type"=>nil}
  add_index "admin_users", ["type", "is_deleted"], :name => "index_admin_users_on_type_and_is_deleted", :limit => {"is_deleted"=>nil, "type"=>nil}
  add_index "admin_users", ["type"], :name => "index_admin_users_on_type", :limit => {"type"=>nil}
  add_index "admin_users", ["username"], :name => "index_admin_users_on_username", :limit => {"username"=>nil}

  create_table "allotment_log_details", :force => true do |t|
    t.string   "name"
    t.string   "registration_no"
    t.string   "status"
    t.string   "description"
    t.integer  "registration_course_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "app_session_store", :force => true do |t|
    t.integer   "session_id_crc",               :null => false
    t.string    "session_id",     :limit => 32, :null => false
    t.timestamp "updated_at",                   :null => false
    t.text      "data"
  end

  add_index "app_session_store", ["session_id_crc", "session_id"], :name => "session_id", :unique => true, :limit => {"session_id_crc"=>nil, "session_id"=>nil}
  add_index "app_session_store", ["updated_at"], :name => "updated_at", :limit => {"updated_at"=>nil}

  create_table "applicant_additional_details", :force => true do |t|
    t.integer  "applicant_id"
    t.integer  "additional_field_id"
    t.string   "additional_info"
    t.integer  "school_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "applicant_additional_details", ["additional_field_id"], :name => "by_additional_field_id", :limit => {"additional_field_id"=>nil}
  add_index "applicant_additional_details", ["applicant_id"], :name => "by_applicant_id", :limit => {"applicant_id"=>nil}
  add_index "applicant_additional_details", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "applicant_addl_attachments", :force => true do |t|
    t.integer  "school_id"
    t.integer  "applicant_id"
    t.string   "attachment_file_name"
    t.string   "attachment_content_type"
    t.integer  "attachment_file_size"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "attachment_updated_at"
  end

  add_index "applicant_addl_attachments", ["applicant_id"], :name => "index_applicant_addl_attachments_on_applicant_id", :limit => {"applicant_id"=>nil}
  add_index "applicant_addl_attachments", ["school_id"], :name => "index_applicant_addl_attachments_on_school_id", :limit => {"school_id"=>nil}

  create_table "applicant_addl_field_groups", :force => true do |t|
    t.integer  "school_id"
    t.integer  "registration_course_id"
    t.string   "name"
    t.boolean  "is_active",              :default => true
    t.integer  "position"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "applicant_addl_field_groups", ["is_active"], :name => "index_applicant_addl_field_groups_on_is_active", :limit => {"is_active"=>nil}
  add_index "applicant_addl_field_groups", ["registration_course_id"], :name => "index_applicant_addl_field_groups_on_registration_course_id", :limit => {"registration_course_id"=>nil}
  add_index "applicant_addl_field_groups", ["school_id"], :name => "index_applicant_addl_field_groups_on_school_id", :limit => {"school_id"=>nil}

  create_table "applicant_addl_field_values", :force => true do |t|
    t.integer  "school_id"
    t.integer  "applicant_addl_field_id"
    t.string   "option"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "applicant_addl_field_values", ["applicant_addl_field_id"], :name => "index_applicant_addl_field_values_on_applicant_addl_field_id", :limit => {"applicant_addl_field_id"=>nil}
  add_index "applicant_addl_field_values", ["school_id"], :name => "index_applicant_addl_field_values_on_school_id", :limit => {"school_id"=>nil}

  create_table "applicant_addl_fields", :force => true do |t|
    t.integer  "school_id"
    t.integer  "applicant_addl_field_group_id"
    t.string   "field_name"
    t.string   "field_type"
    t.boolean  "is_active",                     :default => true
    t.integer  "position"
    t.boolean  "is_mandatory",                  :default => true
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "applicant_addl_fields", ["applicant_addl_field_group_id"], :name => "index_applicant_addl_fields_on_applicant_addl_field_group_id", :limit => {"applicant_addl_field_group_id"=>nil}
  add_index "applicant_addl_fields", ["school_id"], :name => "index_applicant_addl_fields_on_school_id", :limit => {"school_id"=>nil}

  create_table "applicant_addl_values", :force => true do |t|
    t.integer  "school_id"
    t.integer  "applicant_id"
    t.integer  "applicant_addl_field_id"
    t.text     "option"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "applicant_addl_values", ["applicant_addl_field_id"], :name => "index_applicant_addl_values_on_applicant_addl_field_id", :limit => {"applicant_addl_field_id"=>nil}
  add_index "applicant_addl_values", ["applicant_id"], :name => "index_applicant_addl_values_on_applicant_id", :limit => {"applicant_id"=>nil}
  add_index "applicant_addl_values", ["school_id"], :name => "index_applicant_addl_values_on_school_id", :limit => {"school_id"=>nil}

  create_table "applicant_guardians", :force => true do |t|
    t.integer  "school_id"
    t.integer  "applicant_id"
    t.string   "first_name"
    t.string   "last_name"
    t.string   "relation"
    t.string   "email"
    t.string   "office_phone1"
    t.string   "office_phone2"
    t.string   "mobile_phone"
    t.string   "office_address_line1"
    t.string   "office_address_line2"
    t.string   "city"
    t.string   "state"
    t.integer  "country_id"
    t.date     "dob"
    t.string   "occupation"
    t.string   "income"
    t.string   "education"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "applicant_guardians", ["applicant_id"], :name => "index_applicant_guardians_on_applicant_id", :limit => {"applicant_id"=>nil}
  add_index "applicant_guardians", ["school_id"], :name => "index_applicant_guardians_on_school_id", :limit => {"school_id"=>nil}

  create_table "applicant_previous_datas", :force => true do |t|
    t.integer  "school_id"
    t.integer  "applicant_id"
    t.string   "last_attended_school"
    t.string   "qualifying_exam"
    t.string   "qualifying_exam_year"
    t.string   "qualifying_exam_roll"
    t.string   "qualifying_exam_final_score"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "applicant_previous_datas", ["applicant_id"], :name => "index_applicant_previous_datas_on_applicant_id", :limit => {"applicant_id"=>nil}
  add_index "applicant_previous_datas", ["school_id"], :name => "index_applicant_previous_datas_on_school_id", :limit => {"school_id"=>nil}

  create_table "applicant_registration_settings", :force => true do |t|
    t.integer  "school_id"
    t.string   "key"
    t.string   "value"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "applicant_registration_settings", ["school_id"], :name => "index_applicant_registration_settings_on_school_id", :limit => {"school_id"=>nil}

  create_table "applicants", :force => true do |t|
    t.integer  "school_id"
    t.string   "reg_no"
    t.string   "first_name"
    t.string   "middle_name"
    t.string   "last_name"
    t.date     "date_of_birth"
    t.string   "address_line1"
    t.string   "address_line2"
    t.string   "city"
    t.string   "state"
    t.integer  "country_id"
    t.integer  "nationality_id"
    t.string   "pin_code"
    t.string   "phone1"
    t.string   "phone2"
    t.string   "email"
    t.string   "gender"
    t.integer  "registration_course_id"
    t.integer  "photo_file_size"
    t.string   "photo_file_name"
    t.string   "photo_content_type"
    t.string   "status"
    t.boolean  "has_paid"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.text     "pin_number"
    t.string   "print_token"
    t.text     "subject_ids"
    t.boolean  "is_academically_cleared"
    t.boolean  "is_financially_cleared"
    t.decimal  "amount",                  :precision => 12, :scale => 2
    t.text     "normal_subject_ids"
    t.datetime "photo_updated_at"
  end

  add_index "applicants", ["created_at"], :name => "index_applicants_on_created_at", :limit => {"created_at"=>nil}
  add_index "applicants", ["school_id"], :name => "index_applicants_on_school_id", :limit => {"school_id"=>nil}
  add_index "applicants", ["status"], :name => "index_applicants_on_status", :limit => {"status"=>nil}

  create_table "apply_leave_students", :force => true do |t|
    t.integer  "student_id"
    t.date     "start_date"
    t.date     "end_date"
    t.text     "reason"
    t.boolean  "approved",          :default => false
    t.boolean  "viewed_by_teacher", :default => false
    t.string   "teacher_remark"
    t.integer  "approving_teacher"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "apply_leave_students", ["school_id"], :name => "index_apply_leaves_student_on_school_id", :limit => {"school_id"=>nil}

  create_table "apply_leaves", :force => true do |t|
    t.integer  "employee_id"
    t.integer  "employee_leave_types_id"
    t.boolean  "is_half_day"
    t.date     "start_date"
    t.date     "end_date"
    t.string   "reason"
    t.boolean  "approved",                :default => false
    t.boolean  "viewed_by_manager",       :default => false
    t.string   "manager_remark"
    t.integer  "approving_manager"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "apply_leaves", ["school_id"], :name => "index_apply_leaves_on_school_id", :limit => {"school_id"=>nil}

  create_table "archived_employee_additional_details", :force => true do |t|
    t.integer  "employee_id"
    t.integer  "additional_field_id"
    t.string   "additional_info"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "archived_employee_additional_details", ["school_id"], :name => "index_archived_employee_additional_details_on_school_id", :limit => {"school_id"=>nil}

  create_table "archived_employee_bank_details", :force => true do |t|
    t.integer  "employee_id"
    t.integer  "bank_field_id"
    t.string   "bank_info"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "archived_employee_bank_details", ["school_id"], :name => "index_archived_employee_bank_details_on_school_id", :limit => {"school_id"=>nil}

  create_table "archived_employee_salary_structures", :force => true do |t|
    t.integer  "employee_id"
    t.integer  "payroll_category_id"
    t.string   "amount"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "archived_employee_salary_structures", ["school_id"], :name => "index_archived_employee_salary_structures_on_school_id", :limit => {"school_id"=>nil}

  create_table "archived_employees", :force => true do |t|
    t.integer  "employee_category_id"
    t.string   "employee_number"
    t.date     "joining_date"
    t.string   "first_name"
    t.string   "middle_name"
    t.string   "last_name"
    t.string   "gender"
    t.string   "job_title"
    t.integer  "employee_position_id"
    t.integer  "employee_department_id"
    t.integer  "reporting_manager_id"
    t.integer  "employee_grade_id"
    t.string   "qualification"
    t.text     "experience_detail"
    t.integer  "experience_year"
    t.integer  "experience_month"
    t.boolean  "status"
    t.string   "status_description"
    t.date     "date_of_birth"
    t.string   "marital_status"
    t.integer  "children_count"
    t.string   "father_name"
    t.string   "mother_name"
    t.string   "husband_name"
    t.string   "blood_group"
    t.integer  "nationality_id"
    t.string   "home_address_line1"
    t.string   "home_address_line2"
    t.string   "home_city"
    t.string   "home_state"
    t.integer  "home_country_id"
    t.string   "home_pin_code"
    t.string   "office_address_line1"
    t.string   "office_address_line2"
    t.string   "office_city"
    t.string   "office_state"
    t.integer  "office_country_id"
    t.string   "office_pin_code"
    t.string   "office_phone1"
    t.string   "office_phone2"
    t.string   "mobile_phone"
    t.string   "home_phone"
    t.string   "email"
    t.string   "fax"
    t.string   "photo_file_name"
    t.string   "photo_content_type"
    t.binary   "photo_data",             :limit => 16777215
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "photo_file_size"
    t.string   "former_id"
    t.integer  "user_id"
    t.datetime "photo_updated_at"
    t.string   "library_card"
    t.integer  "school_id"
  end

  add_index "archived_employees", ["school_id"], :name => "index_archived_employees_on_school_id", :limit => {"school_id"=>nil}

  create_table "archived_exam_scores", :force => true do |t|
    t.integer  "student_id"
    t.integer  "exam_id"
    t.decimal  "marks",            :precision => 7, :scale => 2
    t.integer  "grading_level_id"
    t.string   "remarks"
    t.boolean  "is_failed"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "archived_exam_scores", ["school_id"], :name => "index_archived_exam_scores_on_school_id", :limit => {"school_id"=>nil}
  add_index "archived_exam_scores", ["student_id", "exam_id"], :name => "index_archived_exam_scores_on_student_id_and_exam_id", :limit => {"student_id"=>nil, "exam_id"=>nil}

  create_table "archived_guardians", :force => true do |t|
    t.integer  "ward_id"
    t.string   "first_name"
    t.string   "last_name"
    t.string   "relation"
    t.string   "email"
    t.string   "office_phone1"
    t.string   "office_phone2"
    t.string   "mobile_phone"
    t.string   "office_address_line1"
    t.string   "office_address_line2"
    t.string   "city"
    t.string   "state"
    t.integer  "country_id"
    t.date     "dob"
    t.string   "occupation"
    t.string   "income"
    t.string   "education"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "archived_guardians", ["school_id"], :name => "index_archived_guardians_on_school_id", :limit => {"school_id"=>nil}

  create_table "archived_students", :force => true do |t|
    t.string   "admission_no"
    t.string   "class_roll_no"
    t.date     "admission_date"
    t.string   "first_name"
    t.string   "middle_name"
    t.string   "last_name"
    t.integer  "batch_id"
    t.date     "date_of_birth"
    t.string   "gender"
    t.string   "blood_group"
    t.string   "birth_place"
    t.integer  "nationality_id"
    t.string   "language"
    t.string   "religion"
    t.integer  "student_category_id"
    t.string   "address_line1"
    t.string   "address_line2"
    t.string   "city"
    t.string   "state"
    t.string   "pin_code"
    t.integer  "country_id"
    t.string   "phone1"
    t.string   "phone2"
    t.string   "email"
    t.string   "photo_file_name"
    t.string   "photo_content_type"
    t.binary   "photo_data",           :limit => 16777215
    t.string   "status_description"
    t.boolean  "is_active",                                :default => true
    t.boolean  "is_deleted",                               :default => false
    t.integer  "immediate_contact_id"
    t.boolean  "is_sms_enabled",                           :default => true
    t.string   "former_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "photo_file_size"
    t.integer  "user_id"
    t.boolean  "is_email_enabled",                         :default => true
    t.integer  "sibling_id"
    t.datetime "photo_updated_at"
    t.date     "date_of_leaving"
    t.string   "library_card"
    t.integer  "school_id"
  end

  add_index "archived_students", ["school_id"], :name => "index_archived_students_on_school_id", :limit => {"school_id"=>nil}

  create_table "assessment_scores", :force => true do |t|
    t.integer  "student_id"
    t.float    "grade_points"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "exam_id"
    t.integer  "batch_id"
    t.integer  "descriptive_indicator_id"
    t.integer  "school_id"
  end

  add_index "assessment_scores", ["school_id"], :name => "index_assessment_scores_on_school_id", :limit => {"school_id"=>nil}
  add_index "assessment_scores", ["student_id", "batch_id", "descriptive_indicator_id", "exam_id"], :name => "score_index", :limit => {"student_id"=>nil, "batch_id"=>nil, "descriptive_indicator_id"=>nil, "exam_id"=>nil}

  create_table "asset_entries", :force => true do |t|
    t.text     "dynamic_attributes"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_asset_id"
    t.integer  "school_id"
  end

  add_index "asset_entries", ["school_id"], :name => "index_asset_entries_on_school_id", :limit => {"school_id"=>nil}

  create_table "asset_field_options", :force => true do |t|
    t.integer  "asset_field_id"
    t.string   "option"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "asset_field_options", ["school_id"], :name => "index_asset_field_options_on_school_id", :limit => {"school_id"=>nil}

  create_table "asset_fields", :force => true do |t|
    t.integer  "school_asset_id"
    t.string   "field_name"
    t.string   "field_type"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "asset_fields", ["school_id"], :name => "index_asset_fields_on_school_id", :limit => {"school_id"=>nil}

  create_table "assets", :force => true do |t|
    t.string   "title"
    t.text     "description"
    t.decimal  "amount",      :precision => 10, :scale => 4
    t.boolean  "is_inactive",                                :default => false
    t.boolean  "is_deleted",                                 :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "assets", ["school_id"], :name => "index_assets_on_school_id", :limit => {"school_id"=>nil}

  create_table "assignment_answers", :force => true do |t|
    t.integer  "assignment_id"
    t.integer  "student_id"
    t.string   "status",                  :default => "0"
    t.string   "title"
    t.text     "content"
    t.string   "attachment_file_name"
    t.string   "attachment_content_type"
    t.integer  "attachment_file_size"
    t.datetime "attachment_updated_at"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "assignment_answers", ["school_id"], :name => "index_assignment_answers_on_school_id", :limit => {"school_id"=>nil}

  create_table "assignments", :force => true do |t|
    t.integer  "employee_id"
    t.integer  "subject_id"
    t.text     "student_list"
    t.string   "title"
    t.integer  "assignment_type",         :default => 1, :null => false
    t.text     "content"
    t.datetime "duedate"
    t.string   "attachment_file_name"
    t.string   "attachment_content_type"
    t.integer  "attachment_file_size"
    t.datetime "attachment_updated_at"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "assignments", ["school_id"], :name => "index_assignments_on_school_id", :limit => {"school_id"=>nil}

  create_table "attendances", :force => true do |t|
    t.integer  "student_id"
    t.integer  "period_table_entry_id"
    t.boolean  "forenoon",              :default => false
    t.boolean  "afternoon",             :default => false
    t.string   "reason"
    t.date     "month_date"
    t.integer  "batch_id"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
    t.boolean  "is_leave",              :default => false, :null => false
  end

  add_index "attendances", ["month_date", "batch_id"], :name => "index_attendances_on_month_date_and_batch_id", :limit => {"batch_id"=>nil, "month_date"=>nil}
  add_index "attendances", ["school_id"], :name => "index_attendances_on_school_id", :limit => {"school_id"=>nil}
  add_index "attendances", ["student_id", "batch_id"], :name => "index_attendances_on_student_id_and_batch_id", :limit => {"student_id"=>nil, "batch_id"=>nil}

  create_table "available_plugins", :force => true do |t|
    t.integer  "associated_id"
    t.string   "associated_type"
    t.text     "plugins"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "available_plugins", ["associated_id", "associated_type"], :name => "index_available_plugins_on_associated_id_and_associated_type", :limit => {"associated_id"=>nil, "associated_type"=>nil}

  create_table "bank_fields", :force => true do |t|
    t.string   "name"
    t.boolean  "status"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "bank_fields", ["school_id"], :name => "index_bank_fields_on_school_id", :limit => {"school_id"=>nil}

  create_table "batch_events", :force => true do |t|
    t.integer  "event_id"
    t.integer  "batch_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "batch_events", ["batch_id"], :name => "index_batch_events_on_batch_id", :limit => {"batch_id"=>nil}
  add_index "batch_events", ["school_id"], :name => "index_batch_events_on_school_id", :limit => {"school_id"=>nil}

  create_table "batch_groups", :force => true do |t|
    t.integer  "course_id"
    t.string   "name"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "batch_groups", ["school_id"], :name => "index_batch_groups_on_school_id", :limit => {"school_id"=>nil}

  create_table "batch_students", :force => true do |t|
    t.integer  "student_id"
    t.integer  "batch_id"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "batch_students", ["batch_id", "student_id"], :name => "index_batch_students_on_batch_id_and_student_id", :limit => {"student_id"=>nil, "batch_id"=>nil}
  add_index "batch_students", ["school_id"], :name => "index_batch_students_on_school_id", :limit => {"school_id"=>nil}

  create_table "batch_tutors", :id => false, :force => true do |t|
    t.integer "employee_id"
    t.integer "batch_id"
  end

  add_index "batch_tutors", ["employee_id", "batch_id"], :name => "index_batch_tutors_on_employee_id_and_batch_id", :limit => {"batch_id"=>nil, "employee_id"=>nil}

  create_table "batches", :force => true do |t|
    t.string   "name"
    t.integer  "course_id"
    t.datetime "start_date"
    t.datetime "end_date"
    t.boolean  "is_active",           :default => true
    t.boolean  "is_deleted",          :default => false
    t.string   "employee_id"
    t.integer  "weekday_set_id"
    t.integer  "class_timing_set_id"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "batches", ["course_id"], :name => "index_batches_on_course_id", :limit => {"course_id"=>nil}
  add_index "batches", ["is_deleted", "is_active", "course_id", "name"], :name => "index_batches_on_is_deleted_and_is_active_and_course_id_and_name", :limit => {"name"=>nil, "course_id"=>nil, "is_active"=>nil, "is_deleted"=>nil}
  add_index "batches", ["school_id"], :name => "index_batches_on_school_id", :limit => {"school_id"=>nil}

  create_table "biometric_informations", :force => true do |t|
    t.integer  "user_id"
    t.string   "biometric_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "biometric_informations", ["school_id"], :name => "index_biometric_informations_on_school_id", :limit => {"school_id"=>nil}

  create_table "blog_comments", :force => true do |t|
    t.text     "body"
    t.boolean  "is_deleted"
    t.integer  "user_id"
    t.integer  "blog_post_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "blog_comments", ["blog_post_id"], :name => "by_blog_post_id", :limit => {"blog_post_id"=>nil}
  add_index "blog_comments", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "blog_posts", :force => true do |t|
    t.string   "title"
    t.text     "body"
    t.boolean  "is_active"
    t.boolean  "is_published"
    t.boolean  "is_deleted"
    t.integer  "blog_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "blog_posts", ["blog_id"], :name => "by_blog_id", :limit => {"blog_id"=>nil}
  add_index "blog_posts", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "blogs", :force => true do |t|
    t.string   "name"
    t.boolean  "is_active"
    t.boolean  "is_published"
    t.integer  "user_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "blogs", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "book_additional_details", :force => true do |t|
    t.integer  "book_id"
    t.integer  "book_additional_field_id"
    t.string   "additional_info"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "book_additional_details", ["school_id"], :name => "index_book_additional_details_on_school_id", :limit => {"school_id"=>nil}

  create_table "book_additional_field_options", :force => true do |t|
    t.string   "field_option"
    t.integer  "book_additional_field_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "book_additional_field_options", ["school_id"], :name => "index_book_additional_field_options_on_school_id", :limit => {"school_id"=>nil}

  create_table "book_additional_fields", :force => true do |t|
    t.string   "name"
    t.boolean  "is_mandatory"
    t.string   "input_type"
    t.integer  "priority"
    t.boolean  "is_active"
    t.integer  "school_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "book_additional_fields", ["school_id"], :name => "index_book_additional_fields_on_school_id", :limit => {"school_id"=>nil}

  create_table "book_movements", :force => true do |t|
    t.integer  "user_id"
    t.integer  "book_id"
    t.date     "issue_date"
    t.date     "due_date"
    t.string   "status"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "book_movements", ["school_id"], :name => "index_book_movements_on_school_id", :limit => {"school_id"=>nil}

  create_table "book_reservations", :force => true do |t|
    t.integer  "user_id"
    t.integer  "book_id"
    t.datetime "reserved_on"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "book_reservations", ["school_id"], :name => "index_book_reservations_on_school_id", :limit => {"school_id"=>nil}

  create_table "books", :force => true do |t|
    t.string   "title"
    t.string   "author"
    t.string   "book_number"
    t.integer  "book_movement_id"
    t.string   "status",           :default => "Available"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "books", ["school_id"], :name => "index_books_on_school_id", :limit => {"school_id"=>nil}

  create_table "cancelled_finance_transactions", :force => true do |t|
    t.string   "title"
    t.string   "description"
    t.decimal  "amount",                :precision => 15, :scale => 4
    t.boolean  "fine_included",                                        :default => false
    t.integer  "category_id"
    t.integer  "student_id"
    t.integer  "finance_fees_id"
    t.date     "transaction_date"
    t.decimal  "fine_amount",           :precision => 10, :scale => 4, :default => 0.0
    t.integer  "master_transaction_id",                                :default => 0
    t.integer  "finance_id"
    t.string   "finance_type"
    t.integer  "payee_id"
    t.string   "payee_type"
    t.string   "receipt_no"
    t.string   "voucher_no"
    t.integer  "lastvchid"
    t.string   "payment_mode"
    t.text     "payment_note"
    t.integer  "user_id"
    t.string   "collection_name"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "batch_id"
    t.integer  "school_id"
  end

  add_index "cancelled_finance_transactions", ["school_id"], :name => "index_cancelled_finance_transactions_on_school_id", :limit => {"school_id"=>nil}

  create_table "category_batches", :force => true do |t|
    t.integer  "finance_fee_category_id"
    t.integer  "batch_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "category_batches", ["finance_fee_category_id", "batch_id"], :name => "index_category_batches_on_finance_fee_category_id_and_batch_id", :limit => {"finance_fee_category_id"=>nil, "batch_id"=>nil}
  add_index "category_batches", ["school_id"], :name => "index_category_batches_on_school_id", :limit => {"school_id"=>nil}

  create_table "cce_exam_categories", :force => true do |t|
    t.string   "name"
    t.string   "desc"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "cce_exam_categories", ["school_id"], :name => "index_cce_exam_categories_on_school_id", :limit => {"school_id"=>nil}

  create_table "cce_grade_sets", :force => true do |t|
    t.string   "name"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "cce_grade_sets", ["school_id"], :name => "index_cce_grade_sets_on_school_id", :limit => {"school_id"=>nil}

  create_table "cce_grades", :force => true do |t|
    t.string   "name"
    t.float    "grade_point"
    t.integer  "cce_grade_set_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "cce_grades", ["cce_grade_set_id"], :name => "index_cce_grades_on_cce_grade_set_id", :limit => {"cce_grade_set_id"=>nil}
  add_index "cce_grades", ["school_id"], :name => "index_cce_grades_on_school_id", :limit => {"school_id"=>nil}

  create_table "cce_reports", :force => true do |t|
    t.integer  "observable_id"
    t.string   "observable_type"
    t.integer  "student_id"
    t.integer  "batch_id"
    t.string   "grade_string"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "exam_id"
    t.integer  "school_id"
  end

  add_index "cce_reports", ["observable_id", "student_id", "batch_id", "exam_id", "observable_type"], :name => "cce_report_join_index", :limit => {"student_id"=>nil, "batch_id"=>nil, "exam_id"=>nil, "observable_id"=>nil, "observable_type"=>nil}
  add_index "cce_reports", ["school_id"], :name => "index_cce_reports_on_school_id", :limit => {"school_id"=>nil}

  create_table "cce_weightages", :force => true do |t|
    t.integer  "weightage"
    t.string   "criteria_type"
    t.integer  "cce_exam_category_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "cce_weightages", ["school_id"], :name => "index_cce_weightages_on_school_id", :limit => {"school_id"=>nil}

  create_table "cce_weightages_courses", :id => false, :force => true do |t|
    t.integer "cce_weightage_id"
    t.integer "course_id"
  end

  add_index "cce_weightages_courses", ["cce_weightage_id"], :name => "index_cce_weightages_courses_on_cce_weightage_id", :limit => {"cce_weightage_id"=>nil}
  add_index "cce_weightages_courses", ["course_id", "cce_weightage_id"], :name => "index_for_join_table_cce_weightage_courses", :limit => {"cce_weightage_id"=>nil, "course_id"=>nil}
  add_index "cce_weightages_courses", ["course_id"], :name => "index_cce_weightages_courses_on_course_id", :limit => {"course_id"=>nil}

  create_table "class_designations", :force => true do |t|
    t.string   "name",                                      :null => false
    t.decimal  "cgpa",       :precision => 15, :scale => 2
    t.datetime "created_at"
    t.datetime "updated_at"
    t.decimal  "marks",      :precision => 15, :scale => 2
    t.integer  "course_id"
    t.integer  "school_id"
  end

  add_index "class_designations", ["school_id"], :name => "index_class_designations_on_school_id", :limit => {"school_id"=>nil}

  create_table "class_timing_sets", :force => true do |t|
    t.string   "name"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "class_timing_sets", ["school_id"], :name => "index_class_timing_sets_on_school_id", :limit => {"school_id"=>nil}

  create_table "class_timing_sets_class_timings", :force => true do |t|
    t.integer  "class_timing_set_id"
    t.integer  "class_timing_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "class_timings", :force => true do |t|
    t.integer  "batch_id"
    t.string   "name"
    t.time     "start_time"
    t.time     "end_time"
    t.boolean  "is_break"
    t.boolean  "is_deleted",          :default => false
    t.integer  "class_timing_set_id"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "class_timings", ["batch_id", "start_time", "end_time"], :name => "index_class_timings_on_batch_id_and_start_time_and_end_time", :limit => {"end_time"=>nil, "batch_id"=>nil, "start_time"=>nil}
  add_index "class_timings", ["school_id"], :name => "index_class_timings_on_school_id", :limit => {"school_id"=>nil}

  create_table "collection_discounts", :force => true do |t|
    t.integer  "finance_fee_collection_id"
    t.integer  "fee_discount_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "collection_discounts", ["finance_fee_collection_id", "fee_discount_id"], :name => "fee_discount_index", :limit => {"fee_discount_id"=>nil, "finance_fee_collection_id"=>nil}
  add_index "collection_discounts", ["school_id"], :name => "index_collection_discounts_on_school_id", :limit => {"school_id"=>nil}

  create_table "collection_particulars", :force => true do |t|
    t.integer  "finance_fee_collection_id"
    t.integer  "finance_fee_particular_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "collection_particulars", ["finance_fee_collection_id", "finance_fee_particular_id"], :name => "fee_particular_index", :limit => {"finance_fee_particular_id"=>nil, "finance_fee_collection_id"=>nil}
  add_index "collection_particulars", ["school_id"], :name => "index_collection_particulars_on_school_id", :limit => {"school_id"=>nil}

  create_table "configurations", :force => true do |t|
    t.string   "config_key"
    t.string   "config_value"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "configurations", ["config_key"], :name => "index_configurations_on_config_key", :limit => {"config_key"=>"10"}
  add_index "configurations", ["config_value"], :name => "index_configurations_on_config_value", :limit => {"config_value"=>"10"}
  add_index "configurations", ["school_id"], :name => "index_configurations_on_school_id", :limit => {"school_id"=>nil}

  create_table "countries", :force => true do |t|
    t.string   "name"
    t.datetime "updated_at"
    t.datetime "created_at"
  end

  create_table "course_pins", :force => true do |t|
    t.boolean  "is_pin_enabled"
    t.integer  "course_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  create_table "courses", :force => true do |t|
    t.string   "course_name"
    t.string   "code"
    t.string   "section_name"
    t.boolean  "is_deleted",   :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "grading_type"
    t.integer  "school_id"
  end

  add_index "courses", ["grading_type"], :name => "index_courses_on_grading_type", :limit => {"grading_type"=>nil}
  add_index "courses", ["school_id"], :name => "index_courses_on_school_id", :limit => {"school_id"=>nil}

  create_table "courses_observation_groups", :id => false, :force => true do |t|
    t.integer "course_id"
    t.integer "observation_group_id"
  end

  add_index "courses_observation_groups", ["course_id"], :name => "index_courses_observation_groups_on_course_id", :limit => {"course_id"=>nil}
  add_index "courses_observation_groups", ["observation_group_id"], :name => "index_courses_observation_groups_on_observation_group_id", :limit => {"observation_group_id"=>nil}

  create_table "data_exports", :force => true do |t|
    t.integer  "export_structure_id"
    t.string   "file_format"
    t.string   "status"
    t.string   "export_file_file_name"
    t.string   "export_file_content_type"
    t.integer  "export_file_file_size"
    t.datetime "export_file_updated_at"
    t.integer  "school_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "delayed_jobs", :force => true do |t|
    t.integer  "priority",   :default => 0
    t.integer  "attempts",   :default => 0
    t.text     "handler"
    t.text     "last_error"
    t.datetime "run_at"
    t.datetime "locked_at"
    t.datetime "failed_at"
    t.string   "locked_by"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "delayed_jobs", ["locked_by"], :name => "index_delayed_jobs_on_locked_by", :limit => {"locked_by"=>nil}

  create_table "descriptive_indicators", :force => true do |t|
    t.string   "name"
    t.string   "desc"
    t.integer  "describable_id"
    t.string   "describable_type"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "sort_order"
    t.integer  "school_id"
  end

  add_index "descriptive_indicators", ["describable_id", "describable_type", "sort_order"], :name => "describable_index", :limit => {"describable_type"=>nil, "describable_id"=>nil, "sort_order"=>nil}
  add_index "descriptive_indicators", ["school_id"], :name => "index_descriptive_indicators_on_school_id", :limit => {"school_id"=>nil}

  create_table "discipline_actions", :force => true do |t|
    t.text     "body"
    t.string   "remarks"
    t.integer  "school_id"
    t.integer  "user_id"
    t.integer  "discipline_complaint_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "discipline_actions", ["school_id"], :name => "index_discipline_actions_on_school_id", :limit => {"school_id"=>nil}
  add_index "discipline_actions", ["user_id"], :name => "index_discipline_actions_on_user_id", :limit => {"user_id"=>nil}

  create_table "discipline_attachments", :force => true do |t|
    t.integer  "school_id"
    t.integer  "discipline_participation_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "attachment_file_name"
    t.string   "attachment_content_type"
    t.integer  "attachment_file_size"
    t.datetime "attachment_updated_at"
  end

  create_table "discipline_comments", :force => true do |t|
    t.text     "body"
    t.integer  "commentable_id"
    t.string   "commentable_type"
    t.integer  "school_id"
    t.integer  "user_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "discipline_comments", ["school_id"], :name => "index_discipline_comments_on_school_id", :limit => {"school_id"=>nil}
  add_index "discipline_comments", ["user_id"], :name => "index_discipline_comments_on_user_id", :limit => {"user_id"=>nil}

  create_table "discipline_complaints", :force => true do |t|
    t.string   "subject"
    t.text     "body"
    t.date     "trial_date"
    t.integer  "school_id"
    t.integer  "user_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "complaint_no"
    t.boolean  "action_taken", :default => false
  end

  add_index "discipline_complaints", ["school_id"], :name => "index_discipline_complaints_on_school_id", :limit => {"school_id"=>nil}
  add_index "discipline_complaints", ["user_id"], :name => "index_discipline_complaints_on_user_id", :limit => {"user_id"=>nil}

  create_table "discipline_participations", :force => true do |t|
    t.string   "type"
    t.boolean  "action_taken"
    t.integer  "school_id"
    t.integer  "user_id"
    t.integer  "discipline_complaint_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "discipline_participations", ["school_id"], :name => "index_discipline_participations_on_school_id", :limit => {"school_id"=>nil}
  add_index "discipline_participations", ["user_id", "discipline_complaint_id", "type"], :name => "by_user_and_complaint", :limit => {"user_id"=>nil, "discipline_complaint_id"=>nil, "type"=>nil}

  create_table "discipline_student_actions", :force => true do |t|
    t.integer  "discipline_action_id"
    t.integer  "discipline_participation_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "discipline_student_actions", ["discipline_participation_id", "discipline_action_id"], :name => "by_action_and_participation", :limit => {"discipline_action_id"=>nil, "discipline_participation_id"=>nil}

  create_table "elective_groups", :force => true do |t|
    t.string   "name"
    t.integer  "batch_id"
    t.boolean  "is_deleted", :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "elective_groups", ["school_id"], :name => "index_elective_groups_on_school_id", :limit => {"school_id"=>nil}

  create_table "electives", :force => true do |t|
    t.integer  "elective_group_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "electives", ["school_id"], :name => "index_electives_on_school_id", :limit => {"school_id"=>nil}

  create_table "email_alerts", :force => true do |t|
    t.string   "model_name"
    t.boolean  "value"
    t.string   "mail_to"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  create_table "email_subscriptions", :force => true do |t|
    t.integer  "student_id"
    t.string   "name"
    t.integer  "school_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "employee_additional_details", :force => true do |t|
    t.integer  "employee_id"
    t.integer  "additional_field_id"
    t.string   "additional_info"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "employee_additional_details", ["school_id"], :name => "index_employee_additional_details_on_school_id", :limit => {"school_id"=>nil}

  create_table "employee_attendances", :force => true do |t|
    t.date     "attendance_date"
    t.integer  "employee_id"
    t.integer  "employee_leave_type_id"
    t.string   "reason"
    t.boolean  "is_half_day"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "employee_attendances", ["school_id"], :name => "index_employee_attendances_on_school_id", :limit => {"school_id"=>nil}

  create_table "employee_bank_details", :force => true do |t|
    t.integer  "employee_id"
    t.integer  "bank_field_id"
    t.string   "bank_info"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "employee_bank_details", ["school_id"], :name => "index_employee_bank_details_on_school_id", :limit => {"school_id"=>nil}

  create_table "employee_categories", :force => true do |t|
    t.string   "name"
    t.string   "prefix"
    t.boolean  "status"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "employee_categories", ["school_id"], :name => "index_employee_categories_on_school_id", :limit => {"school_id"=>nil}

  create_table "employee_department_events", :force => true do |t|
    t.integer  "event_id"
    t.integer  "employee_department_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "employee_department_events", ["school_id"], :name => "index_employee_department_events_on_school_id", :limit => {"school_id"=>nil}

  create_table "employee_departments", :force => true do |t|
    t.string   "code"
    t.string   "name"
    t.boolean  "status"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "employee_departments", ["school_id"], :name => "index_employee_departments_on_school_id", :limit => {"school_id"=>nil}

  create_table "employee_grades", :force => true do |t|
    t.string   "name"
    t.integer  "priority"
    t.boolean  "status"
    t.integer  "max_hours_day"
    t.integer  "max_hours_week"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "employee_grades", ["school_id"], :name => "index_employee_grades_on_school_id", :limit => {"school_id"=>nil}

  create_table "employee_leave_types", :force => true do |t|
    t.string   "name"
    t.string   "code"
    t.boolean  "status"
    t.string   "max_leave_count"
    t.boolean  "carry_forward",   :default => false, :null => false
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "employee_leave_types", ["school_id"], :name => "index_employee_leave_types_on_school_id", :limit => {"school_id"=>nil}

  create_table "employee_leaves", :force => true do |t|
    t.integer  "employee_id"
    t.integer  "employee_leave_type_id"
    t.decimal  "leave_count",            :precision => 5, :scale => 1, :default => 0.0
    t.decimal  "leave_taken",            :precision => 5, :scale => 1, :default => 0.0
    t.datetime "reset_date"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "employee_leaves", ["school_id"], :name => "index_employee_leaves_on_school_id", :limit => {"school_id"=>nil}

  create_table "employee_positions", :force => true do |t|
    t.string   "name"
    t.integer  "employee_category_id"
    t.boolean  "status"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "employee_positions", ["school_id"], :name => "index_employee_positions_on_school_id", :limit => {"school_id"=>nil}

  create_table "employee_salary_structures", :force => true do |t|
    t.integer  "employee_id"
    t.integer  "payroll_category_id"
    t.string   "amount"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "employee_salary_structures", ["school_id"], :name => "index_employee_salary_structures_on_school_id", :limit => {"school_id"=>nil}

  create_table "employees", :force => true do |t|
    t.integer  "employee_category_id"
    t.string   "employee_number"
    t.date     "joining_date"
    t.string   "first_name"
    t.string   "middle_name"
    t.string   "last_name"
    t.string   "gender"
    t.string   "job_title"
    t.integer  "employee_position_id"
    t.integer  "employee_department_id"
    t.integer  "reporting_manager_id"
    t.integer  "employee_grade_id"
    t.string   "qualification"
    t.text     "experience_detail"
    t.integer  "experience_year"
    t.integer  "experience_month"
    t.boolean  "status"
    t.string   "status_description"
    t.date     "date_of_birth"
    t.string   "marital_status"
    t.integer  "children_count"
    t.string   "father_name"
    t.string   "mother_name"
    t.string   "husband_name"
    t.string   "blood_group"
    t.integer  "nationality_id"
    t.string   "home_address_line1"
    t.string   "home_address_line2"
    t.string   "home_city"
    t.string   "home_state"
    t.integer  "home_country_id"
    t.string   "home_pin_code"
    t.string   "office_address_line1"
    t.string   "office_address_line2"
    t.string   "office_city"
    t.string   "office_state"
    t.integer  "office_country_id"
    t.string   "office_pin_code"
    t.string   "office_phone1"
    t.string   "office_phone2"
    t.string   "mobile_phone"
    t.string   "home_phone"
    t.string   "email"
    t.string   "fax"
    t.string   "photo_file_name"
    t.string   "photo_content_type"
    t.binary   "photo_data",             :limit => 16777215
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "photo_file_size"
    t.integer  "user_id"
    t.datetime "photo_updated_at"
    t.string   "library_card"
    t.integer  "school_id"
    t.boolean  "is_visible",                                 :default => true, :null => false
  end

  add_index "employees", ["employee_number"], :name => "index_employees_on_employee_number", :limit => {"employee_number"=>"10"}
  add_index "employees", ["school_id"], :name => "index_employees_on_school_id", :limit => {"school_id"=>nil}
  add_index "employees", ["user_id"], :name => "index_employees_on_user_id", :limit => {"user_id"=>nil}

  create_table "employees_subjects", :force => true do |t|
    t.integer  "employee_id"
    t.integer  "subject_id"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "employees_subjects", ["school_id"], :name => "index_employees_subjects_on_school_id", :limit => {"school_id"=>nil}
  add_index "employees_subjects", ["subject_id"], :name => "index_employees_subjects_on_subject_id", :limit => {"subject_id"=>nil}

  create_table "event_acknowledges", :force => true do |t|
    t.integer "event_id"
    t.integer "acknowledged_by",    :limit => 1
    t.integer "acknowledged_by_id"
    t.integer "school_id"
    t.integer "status",             :limit => 1, :default => 1, :null => false
  end

  create_table "event_categories", :force => true do |t|
    t.string   "name",                                    :null => false
    t.string   "icon_number"
    t.integer  "is_club",     :limit => 1, :default => 0, :null => false
    t.datetime "created_at",                              :null => false
    t.datetime "updated_at",                              :null => false
    t.integer  "status",      :limit => 1, :default => 1, :null => false
    t.integer  "school_id",                               :null => false
  end

  create_table "events", :force => true do |t|
    t.integer  "event_category_id"
    t.string   "title"
    t.decimal  "fees",              :precision => 13, :scale => 2, :default => 0.0,   :null => false
    t.string   "icon_number"
    t.text     "description"
    t.datetime "start_date"
    t.datetime "end_date"
    t.boolean  "is_common",                                        :default => false
    t.boolean  "is_holiday",                                       :default => false
    t.boolean  "is_exam",                                          :default => false
    t.boolean  "is_due",                                           :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "origin_id"
    t.string   "origin_type"
    t.integer  "school_id"
    t.integer  "is_club",                                          :default => 0,     :null => false
  end

  add_index "events", ["is_common", "is_holiday", "is_exam"], :name => "index_events_on_is_common_and_is_holiday_and_is_exam", :limit => {"is_exam"=>nil, "is_common"=>nil, "is_holiday"=>nil}
  add_index "events", ["school_id"], :name => "index_events_on_school_id", :limit => {"school_id"=>nil}

  create_table "exam_groups", :force => true do |t|
    t.string   "name"
    t.integer  "batch_id"
    t.string   "exam_type"
    t.integer  "exam_category",        :default => 1,     :null => false
    t.text     "topic"
    t.boolean  "is_published",         :default => false
    t.boolean  "is_current",           :default => false, :null => false
    t.boolean  "result_published",     :default => false
    t.date     "exam_date"
    t.boolean  "is_final_exam",        :default => false, :null => false
    t.integer  "cce_exam_category_id"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "exam_groups", ["school_id"], :name => "index_exam_groups_on_school_id", :limit => {"school_id"=>nil}

  create_table "exam_scores", :force => true do |t|
    t.integer  "student_id"
    t.integer  "exam_id"
    t.decimal  "marks",            :precision => 7, :scale => 2
    t.integer  "grading_level_id"
    t.string   "remarks"
    t.boolean  "is_failed"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "exam_scores", ["school_id"], :name => "index_exam_scores_on_school_id", :limit => {"school_id"=>nil}
  add_index "exam_scores", ["student_id", "exam_id"], :name => "index_exam_scores_on_student_id_and_exam_id", :limit => {"student_id"=>nil, "exam_id"=>nil}

  create_table "exams", :force => true do |t|
    t.integer  "exam_group_id"
    t.integer  "subject_id"
    t.datetime "start_time"
    t.datetime "end_time"
    t.decimal  "maximum_marks",    :precision => 10, :scale => 2
    t.decimal  "minimum_marks",    :precision => 10, :scale => 2
    t.integer  "grading_level_id"
    t.integer  "weightage",                                       :default => 0
    t.integer  "event_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "exams", ["exam_group_id", "subject_id"], :name => "index_exams_on_exam_group_id_and_subject_id", :limit => {"subject_id"=>nil, "exam_group_id"=>nil}
  add_index "exams", ["school_id"], :name => "index_exams_on_school_id", :limit => {"school_id"=>nil}

  create_table "export_structures", :force => true do |t|
    t.string   "model_name"
    t.text     "query"
    t.string   "template"
    t.string   "plugin_name"
    t.text     "csv_header_order"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "exports", :force => true do |t|
    t.text     "structure"
    t.string   "name"
    t.string   "model"
    t.text     "associated_columns"
    t.text     "join_columns"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "exports", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "fa_criterias", :force => true do |t|
    t.string   "fa_name"
    t.string   "desc"
    t.integer  "fa_group_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "sort_order"
    t.boolean  "is_deleted",  :default => false
    t.integer  "school_id"
  end

  add_index "fa_criterias", ["fa_group_id"], :name => "index_fa_criterias_on_fa_group_id", :limit => {"fa_group_id"=>nil}
  add_index "fa_criterias", ["school_id"], :name => "index_fa_criterias_on_school_id", :limit => {"school_id"=>nil}

  create_table "fa_groups", :force => true do |t|
    t.string   "name"
    t.text     "desc"
    t.integer  "cce_exam_category_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "cce_grade_set_id"
    t.float    "max_marks",            :default => 100.0
    t.boolean  "is_deleted",           :default => false
    t.integer  "school_id"
  end

  add_index "fa_groups", ["school_id"], :name => "index_fa_groups_on_school_id", :limit => {"school_id"=>nil}

  create_table "fa_groups_subjects", :id => false, :force => true do |t|
    t.integer "subject_id"
    t.integer "fa_group_id"
  end

  add_index "fa_groups_subjects", ["fa_group_id", "subject_id"], :name => "score_index", :limit => {"subject_id"=>nil, "fa_group_id"=>nil}
  add_index "fa_groups_subjects", ["fa_group_id"], :name => "index_fa_groups_subjects_on_fa_group_id", :limit => {"fa_group_id"=>nil}
  add_index "fa_groups_subjects", ["subject_id"], :name => "index_fa_groups_subjects_on_subject_id", :limit => {"subject_id"=>nil}

  create_table "features", :force => true do |t|
    t.string   "feature_key"
    t.boolean  "is_enabled",  :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "fee_collection_batches", :force => true do |t|
    t.integer  "finance_fee_collection_id"
    t.integer  "batch_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "fee_collection_batches", ["batch_id"], :name => "index_fee_collection_batches_on_batch_id", :limit => {"batch_id"=>nil}
  add_index "fee_collection_batches", ["finance_fee_collection_id"], :name => "index_fee_collection_batches_on_finance_fee_collection_id", :limit => {"finance_fee_collection_id"=>nil}
  add_index "fee_collection_batches", ["school_id"], :name => "index_fee_collection_batches_on_school_id", :limit => {"school_id"=>nil}

  create_table "fee_collection_discounts", :force => true do |t|
    t.string   "type"
    t.string   "name"
    t.integer  "receiver_id"
    t.integer  "finance_fee_collection_id"
    t.decimal  "discount",                  :precision => 15, :scale => 4
    t.boolean  "is_amount",                                                :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "fee_collection_discounts", ["school_id"], :name => "index_fee_collection_discounts_on_school_id", :limit => {"school_id"=>nil}

  create_table "fee_collection_particulars", :force => true do |t|
    t.string   "name"
    t.text     "description"
    t.decimal  "amount",                    :precision => 12, :scale => 4
    t.integer  "finance_fee_collection_id"
    t.integer  "student_category_id"
    t.string   "admission_no"
    t.integer  "student_id"
    t.boolean  "is_deleted",                                               :default => false, :null => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "fee_collection_particulars", ["school_id"], :name => "index_fee_collection_particulars_on_school_id", :limit => {"school_id"=>nil}

  create_table "fee_discounts", :force => true do |t|
    t.string   "type"
    t.string   "name"
    t.integer  "receiver_id"
    t.integer  "finance_fee_category_id"
    t.decimal  "discount",                :precision => 15, :scale => 4
    t.boolean  "is_amount",                                              :default => false
    t.string   "receiver_type"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "batch_id"
    t.boolean  "is_deleted",                                             :default => false
    t.integer  "school_id"
  end

  add_index "fee_discounts", ["school_id"], :name => "index_fee_discounts_on_school_id", :limit => {"school_id"=>nil}

  create_table "fee_refunds", :force => true do |t|
    t.integer  "finance_fee_id"
    t.text     "reason"
    t.decimal  "amount",                 :precision => 15, :scale => 4
    t.integer  "finance_transaction_id"
    t.integer  "refund_rule_id"
    t.integer  "user_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "fee_refunds", ["school_id"], :name => "index_fee_refunds_on_school_id", :limit => {"school_id"=>nil}

  create_table "fee_transactions", :force => true do |t|
    t.integer  "finance_fee_id"
    t.integer  "finance_transaction_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "fee_transactions", ["finance_fee_id", "finance_transaction_id"], :name => "finance_transaction_index", :limit => {"finance_transaction_id"=>nil, "finance_fee_id"=>nil}
  add_index "fee_transactions", ["school_id"], :name => "index_fee_transactions_on_school_id", :limit => {"school_id"=>nil}

  create_table "feed_categories", :force => true do |t|
    t.string   "name",                             :null => false
    t.text     "description",                      :null => false
    t.string   "logo_file_name"
    t.string   "logo_content_type"
    t.integer  "logo_file_size"
    t.integer  "columns",           :default => 0, :null => false
    t.integer  "position",          :default => 0, :null => false
    t.datetime "last_entry_date"
    t.integer  "parent_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "status",            :default => 1, :null => false
  end

  create_table "finance_donations", :force => true do |t|
    t.string   "donor"
    t.string   "description"
    t.decimal  "amount",           :precision => 15, :scale => 4
    t.integer  "transaction_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.date     "transaction_date"
    t.integer  "school_id"
  end

  add_index "finance_donations", ["school_id"], :name => "index_finance_donations_on_school_id", :limit => {"school_id"=>nil}

  create_table "finance_fee_categories", :force => true do |t|
    t.string   "name"
    t.text     "description"
    t.integer  "batch_id"
    t.boolean  "is_deleted",  :default => false, :null => false
    t.boolean  "is_master",   :default => false, :null => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "finance_fee_categories", ["school_id"], :name => "index_finance_fee_categories_on_school_id", :limit => {"school_id"=>nil}

  create_table "finance_fee_collections", :force => true do |t|
    t.string   "name"
    t.date     "start_date"
    t.date     "end_date"
    t.date     "due_date"
    t.integer  "fee_category_id"
    t.integer  "batch_id"
    t.boolean  "is_deleted",      :default => false, :null => false
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "fine_id"
    t.integer  "school_id"
  end

  add_index "finance_fee_collections", ["batch_id"], :name => "index_finance_fee_collections_on_batch_id", :limit => {"batch_id"=>nil}
  add_index "finance_fee_collections", ["fee_category_id"], :name => "index_finance_fee_collections_on_fee_category_id", :limit => {"fee_category_id"=>nil}
  add_index "finance_fee_collections", ["school_id"], :name => "index_finance_fee_collections_on_school_id", :limit => {"school_id"=>nil}

  create_table "finance_fee_particulars", :force => true do |t|
    t.string   "name"
    t.text     "description"
    t.decimal  "amount",                  :precision => 15, :scale => 4
    t.integer  "finance_fee_category_id"
    t.integer  "student_category_id"
    t.string   "admission_no"
    t.integer  "student_id"
    t.boolean  "is_deleted",                                             :default => false, :null => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "receiver_id"
    t.string   "receiver_type"
    t.integer  "batch_id"
    t.integer  "school_id"
  end

  add_index "finance_fee_particulars", ["school_id"], :name => "index_finance_fee_particulars_on_school_id", :limit => {"school_id"=>nil}

  create_table "finance_fee_structure_elements", :force => true do |t|
    t.decimal  "amount",              :precision => 15, :scale => 4
    t.string   "label"
    t.integer  "batch_id"
    t.integer  "student_category_id"
    t.integer  "student_id"
    t.integer  "parent_id"
    t.integer  "fee_collection_id"
    t.boolean  "deleted",                                            :default => false
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "finance_fee_structure_elements", ["school_id"], :name => "index_finance_fee_structure_elements_on_school_id", :limit => {"school_id"=>nil}

  create_table "finance_fees", :force => true do |t|
    t.integer  "fee_collection_id"
    t.string   "transaction_id"
    t.integer  "student_id"
    t.boolean  "is_paid",                                          :default => false
    t.decimal  "balance",           :precision => 15, :scale => 4, :default => 0.0
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "batch_id"
    t.integer  "school_id"
  end

  add_index "finance_fees", ["batch_id"], :name => "index_finance_fees_on_batch_id", :limit => {"batch_id"=>nil}
  add_index "finance_fees", ["fee_collection_id", "student_id"], :name => "index_finance_fees_on_fee_collection_id_and_student_id", :limit => {"student_id"=>nil, "fee_collection_id"=>nil}
  add_index "finance_fees", ["school_id"], :name => "index_finance_fees_on_school_id", :limit => {"school_id"=>nil}
  add_index "finance_fees", ["student_id", "fee_collection_id", "is_paid"], :name => "index_on_is_paid", :limit => {"student_id"=>nil, "fee_collection_id"=>nil, "is_paid"=>nil}

  create_table "finance_transaction_categories", :force => true do |t|
    t.string   "name"
    t.string   "description"
    t.boolean  "is_income"
    t.boolean  "deleted",         :default => false, :null => false
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "tally_ledger_id"
    t.integer  "school_id"
  end

  add_index "finance_transaction_categories", ["school_id"], :name => "index_finance_transaction_categories_on_school_id", :limit => {"school_id"=>nil}

  create_table "finance_transaction_triggers", :force => true do |t|
    t.integer  "finance_category_id"
    t.decimal  "percentage",          :precision => 8, :scale => 2
    t.string   "title"
    t.string   "description"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "finance_transaction_triggers", ["school_id"], :name => "index_finance_transaction_triggers_on_school_id", :limit => {"school_id"=>nil}

  create_table "finance_transactions", :force => true do |t|
    t.string   "title"
    t.string   "description"
    t.decimal  "amount",                :precision => 15, :scale => 4
    t.boolean  "fine_included",                                        :default => false
    t.integer  "category_id"
    t.integer  "student_id"
    t.integer  "finance_fees_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.date     "transaction_date"
    t.decimal  "fine_amount",           :precision => 10, :scale => 4, :default => 0.0
    t.integer  "master_transaction_id",                                :default => 0
    t.integer  "finance_id"
    t.string   "finance_type"
    t.integer  "payee_id"
    t.string   "payee_type"
    t.string   "receipt_no"
    t.string   "voucher_no"
    t.string   "payment_mode"
    t.text     "payment_note"
    t.integer  "user_id"
    t.integer  "batch_id"
    t.integer  "lastvchid"
    t.integer  "school_id"
  end

  add_index "finance_transactions", ["school_id"], :name => "index_finance_transactions_on_school_id", :limit => {"school_id"=>nil}

  create_table "fine_rules", :force => true do |t|
    t.integer  "fine_id"
    t.integer  "fine_days"
    t.decimal  "fine_amount", :precision => 10, :scale => 4
    t.boolean  "is_amount"
    t.integer  "user_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "fine_rules", ["school_id"], :name => "index_fine_rules_on_school_id", :limit => {"school_id"=>nil}

  create_table "fines", :force => true do |t|
    t.string   "name"
    t.boolean  "is_deleted", :default => false
    t.integer  "user_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "fines", ["school_id"], :name => "index_fines_on_school_id", :limit => {"school_id"=>nil}

  create_table "freeuser_grade", :force => true do |t|
    t.integer "freeuser_id", :null => false
    t.integer "grade_id",    :null => false
  end

  create_table "freeusers", :force => true do |t|
    t.string  "username",                                           :null => false
    t.string  "password",                                           :null => false
    t.string  "email"
    t.string  "first_name",                                         :null => false
    t.string  "middle_name"
    t.string  "last_name"
    t.string  "nick_name"
    t.integer "type",        :limit => 1, :default => 1,            :null => false
    t.integer "sex",         :limit => 1, :default => 1,            :null => false
    t.integer "for_all",     :limit => 1, :default => 0,            :null => false
    t.string  "country",                  :default => "Bangladesh", :null => false
    t.string  "district",                 :default => "Dhaka",      :null => false
  end

  create_table "gallery_categories", :force => true do |t|
    t.string   "name"
    t.string   "description"
    t.boolean  "is_delete",   :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "gallery_categories", ["school_id"], :name => "index_gallery_categories_on_school_id", :limit => {"school_id"=>nil}

  create_table "gallery_photos", :force => true do |t|
    t.integer  "gallery_category_id"
    t.string   "description"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "photo_file_name"
    t.string   "photo_content_type"
    t.integer  "photo_file_size"
    t.datetime "photo_updated_at"
    t.string   "name"
    t.integer  "school_id"
  end

  add_index "gallery_photos", ["school_id"], :name => "index_gallery_photos_on_school_id", :limit => {"school_id"=>nil}

  create_table "gallery_tags", :force => true do |t|
    t.integer  "gallery_photo_id"
    t.integer  "member_id"
    t.string   "member_type"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "gallery_tags", ["school_id"], :name => "index_gallery_tags_on_school_id", :limit => {"school_id"=>nil}

  create_table "grading_levels", :force => true do |t|
    t.string   "name"
    t.integer  "batch_id"
    t.integer  "min_score"
    t.integer  "order"
    t.boolean  "is_deleted",                                   :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.decimal  "credit_points", :precision => 15, :scale => 2
    t.string   "description"
    t.integer  "school_id"
  end

  add_index "grading_levels", ["batch_id", "is_deleted"], :name => "index_grading_levels_on_batch_id_and_is_deleted", :limit => {"batch_id"=>nil, "is_deleted"=>nil}
  add_index "grading_levels", ["school_id"], :name => "index_grading_levels_on_school_id", :limit => {"school_id"=>nil}

  create_table "grn_items", :force => true do |t|
    t.integer  "quantity"
    t.decimal  "unit_price",    :precision => 10, :scale => 4
    t.decimal  "tax",           :precision => 10, :scale => 4
    t.decimal  "discount",      :precision => 10, :scale => 4
    t.datetime "expiry_date"
    t.boolean  "is_deleted",                                   :default => false
    t.integer  "grn_id"
    t.integer  "store_item_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "grn_items", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "grns", :force => true do |t|
    t.string   "grn_no"
    t.string   "invoice_no"
    t.datetime "grn_date"
    t.datetime "invoice_date"
    t.decimal  "other_charges",          :precision => 10, :scale => 4
    t.boolean  "is_deleted",                                            :default => false
    t.integer  "purchase_order_id"
    t.integer  "finance_transaction_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "grns", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "group_files", :force => true do |t|
    t.integer  "group_id"
    t.integer  "user_id"
    t.string   "file_description"
    t.integer  "group_post_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "doc_file_name"
    t.string   "doc_content_type"
    t.integer  "doc_file_size"
    t.datetime "doc_updated_at"
    t.integer  "school_id"
  end

  add_index "group_files", ["school_id"], :name => "index_group_files_on_school_id", :limit => {"school_id"=>nil}

  create_table "group_members", :force => true do |t|
    t.integer  "group_id"
    t.integer  "user_id"
    t.boolean  "is_admin",   :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "group_members", ["school_id"], :name => "index_group_members_on_school_id", :limit => {"school_id"=>nil}

  create_table "group_post_comments", :force => true do |t|
    t.integer  "group_post_id"
    t.integer  "user_id"
    t.text     "comment_body"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "group_post_comments", ["school_id"], :name => "index_group_post_comments_on_school_id", :limit => {"school_id"=>nil}

  create_table "group_posts", :force => true do |t|
    t.integer  "group_id"
    t.integer  "user_id"
    t.string   "post_title"
    t.text     "post_body"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "group_posts", ["school_id"], :name => "index_group_posts_on_school_id", :limit => {"school_id"=>nil}

  create_table "grouped_batches", :force => true do |t|
    t.integer  "batch_group_id"
    t.integer  "batch_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "grouped_batches", ["batch_group_id"], :name => "index_grouped_batches_on_batch_group_id", :limit => {"batch_group_id"=>nil}
  add_index "grouped_batches", ["school_id"], :name => "index_grouped_batches_on_school_id", :limit => {"school_id"=>nil}

  create_table "grouped_exam_reports", :force => true do |t|
    t.integer  "batch_id"
    t.integer  "student_id"
    t.integer  "exam_group_id"
    t.decimal  "marks",         :precision => 15, :scale => 2
    t.string   "score_type"
    t.integer  "subject_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "grouped_exam_reports", ["batch_id", "student_id", "score_type"], :name => "by_batch_student_and_score_type", :limit => {"score_type"=>nil, "student_id"=>nil, "batch_id"=>nil}
  add_index "grouped_exam_reports", ["school_id"], :name => "index_grouped_exam_reports_on_school_id", :limit => {"school_id"=>nil}

  create_table "grouped_exams", :force => true do |t|
    t.integer  "exam_group_id"
    t.integer  "batch_id"
    t.decimal  "weightage",     :precision => 15, :scale => 2
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "grouped_exams", ["batch_id", "exam_group_id"], :name => "index_grouped_exams_on_batch_id_and_exam_group_id", :limit => {"batch_id"=>nil, "exam_group_id"=>nil}
  add_index "grouped_exams", ["batch_id"], :name => "index_grouped_exams_on_batch_id", :limit => {"batch_id"=>nil}
  add_index "grouped_exams", ["school_id"], :name => "index_grouped_exams_on_school_id", :limit => {"school_id"=>nil}

  create_table "groups", :force => true do |t|
    t.string   "group_name"
    t.text     "group_description"
    t.integer  "user_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "logo_file_name"
    t.string   "logo_content_type"
    t.integer  "logo_file_size"
    t.datetime "logo_updated_at"
    t.integer  "school_id"
  end

  add_index "groups", ["school_id"], :name => "index_groups_on_school_id", :limit => {"school_id"=>nil}

  create_table "guardians", :force => true do |t|
    t.integer  "ward_id"
    t.string   "first_name"
    t.string   "last_name"
    t.string   "relation"
    t.string   "email"
    t.string   "office_phone1"
    t.string   "office_phone2"
    t.string   "mobile_phone"
    t.string   "office_address_line1"
    t.string   "office_address_line2"
    t.string   "city"
    t.string   "state"
    t.integer  "country_id"
    t.date     "dob"
    t.string   "occupation"
    t.string   "income"
    t.string   "education"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "user_id"
    t.integer  "school_id"
  end

  add_index "guardians", ["school_id"], :name => "index_guardians_on_school_id", :limit => {"school_id"=>nil}

  create_table "hostel_fee_collections", :force => true do |t|
    t.string   "name"
    t.integer  "batch_id"
    t.date     "start_date"
    t.date     "end_date"
    t.date     "due_date"
    t.boolean  "is_deleted", :default => false, :null => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "hostel_fee_collections", ["batch_id"], :name => "index_hostel_fee_collections_on_batch_id", :limit => {"batch_id"=>nil}
  add_index "hostel_fee_collections", ["school_id"], :name => "index_hostel_fee_collections_on_school_id", :limit => {"school_id"=>nil}

  create_table "hostel_fees", :force => true do |t|
    t.integer  "student_id"
    t.integer  "finance_transaction_id"
    t.integer  "hostel_fee_collection_id"
    t.decimal  "rent",                     :precision => 8, :scale => 4
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "hostel_fees", ["hostel_fee_collection_id"], :name => "index_hostel_fees_on_hostel_fee_collection_id", :limit => {"hostel_fee_collection_id"=>nil}
  add_index "hostel_fees", ["school_id"], :name => "index_hostel_fees_on_school_id", :limit => {"school_id"=>nil}
  add_index "hostel_fees", ["student_id", "finance_transaction_id"], :name => "index_on_finance_transactions", :limit => {"student_id"=>nil, "finance_transaction_id"=>nil}

  create_table "hostels", :force => true do |t|
    t.string   "name"
    t.string   "hostel_type"
    t.string   "other_info"
    t.integer  "employee_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "hostels", ["school_id"], :name => "index_hostels_on_school_id", :limit => {"school_id"=>nil}

  create_table "import_log_details", :force => true do |t|
    t.integer  "import_id"
    t.string   "model"
    t.string   "status"
    t.string   "description"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "import_log_details", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "imports", :force => true do |t|
    t.integer  "export_id"
    t.string   "status"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
    t.string   "csv_file_file_name"
    t.string   "csv_file_content_type"
    t.integer  "csv_file_file_size"
    t.datetime "csv_file_updated_at"
  end

  add_index "imports", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "indent_items", :force => true do |t|
    t.integer  "quantity"
    t.string   "batch_no"
    t.integer  "pending"
    t.integer  "issued"
    t.string   "issued_type"
    t.decimal  "price",         :precision => 10, :scale => 4
    t.integer  "required"
    t.boolean  "is_deleted",                                   :default => false
    t.integer  "indent_id"
    t.integer  "store_item_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "indent_items", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "indents", :force => true do |t|
    t.string   "indent_no"
    t.datetime "expected_date"
    t.string   "status"
    t.boolean  "is_deleted",    :default => false
    t.text     "description"
    t.integer  "user_id"
    t.integer  "store_id"
    t.integer  "manager_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "indents", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "individual_payslip_categories", :force => true do |t|
    t.integer  "employee_id"
    t.date     "salary_date"
    t.string   "name"
    t.string   "amount"
    t.boolean  "is_deduction"
    t.boolean  "include_every_month"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "individual_payslip_categories", ["school_id"], :name => "index_individual_payslip_categories_on_school_id", :limit => {"school_id"=>nil}

  create_table "instant_fee_categories", :force => true do |t|
    t.string   "name"
    t.string   "description"
    t.boolean  "is_deleted",  :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "instant_fee_categories", ["school_id"], :name => "index_instant_fee_categories_on_school_id", :limit => {"school_id"=>nil}

  create_table "instant_fee_details", :force => true do |t|
    t.integer  "instant_fee_id"
    t.integer  "instant_fee_particular_id"
    t.string   "custom_particular"
    t.decimal  "amount",                    :precision => 15, :scale => 4
    t.decimal  "discount",                  :precision => 15, :scale => 4
    t.decimal  "net_amount",                :precision => 15, :scale => 4
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "instant_fee_details", ["school_id"], :name => "index_instant_fee_details_on_school_id", :limit => {"school_id"=>nil}

  create_table "instant_fee_particulars", :force => true do |t|
    t.string   "name"
    t.string   "description"
    t.decimal  "amount",                  :precision => 15, :scale => 4
    t.integer  "instant_fee_category_id"
    t.boolean  "is_deleted",                                             :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "instant_fee_particulars", ["school_id"], :name => "index_instant_fee_particulars_on_school_id", :limit => {"school_id"=>nil}

  create_table "instant_fees", :force => true do |t|
    t.integer  "instant_fee_category_id"
    t.string   "custom_category"
    t.integer  "payee_id"
    t.string   "payee_type"
    t.string   "guest_payee"
    t.decimal  "amount",                  :precision => 15, :scale => 4
    t.datetime "pay_date"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
    t.text     "custom_description"
  end

  add_index "instant_fees", ["school_id"], :name => "index_instant_fees_on_school_id", :limit => {"school_id"=>nil}

  create_table "liabilities", :force => true do |t|
    t.string   "title"
    t.text     "description"
    t.decimal  "amount",      :precision => 10, :scale => 4
    t.boolean  "is_solved",                                  :default => false
    t.boolean  "is_deleted",                                 :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "liabilities", ["school_id"], :name => "index_liabilities_on_school_id", :limit => {"school_id"=>nil}

  create_table "library_card_settings", :force => true do |t|
    t.integer  "course_id"
    t.integer  "student_category_id"
    t.integer  "books_issueable"
    t.integer  "time_period",         :default => 30
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "library_card_settings", ["school_id"], :name => "index_library_card_settings_on_school_id", :limit => {"school_id"=>nil}

  create_table "meeting_request", :force => true do |t|
    t.integer  "type",            :limit => 1, :default => 1, :null => false
    t.integer  "teacher_id",                                  :null => false
    t.integer  "parent_id",                                   :null => false
    t.text     "description",                                 :null => false
    t.datetime "datetime",                                    :null => false
    t.text     "student_ids"
    t.integer  "status",          :limit => 1, :default => 0, :null => false
    t.text     "reciver_messege"
  end

  create_table "menu_link_categories", :force => true do |t|
    t.string   "name"
    t.text     "allowed_roles"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "origin_name"
  end

  create_table "menu_links", :force => true do |t|
    t.string   "name"
    t.string   "target_controller"
    t.string   "target_action"
    t.integer  "higher_link_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "icon_class"
    t.string   "link_type"
    t.string   "user_type"
    t.integer  "menu_link_category_id"
    t.integer  "reference_id",          :default => 0
  end

  create_table "monthly_payslips", :force => true do |t|
    t.date     "salary_date"
    t.integer  "employee_id"
    t.integer  "payroll_category_id"
    t.string   "amount"
    t.boolean  "is_approved",            :default => false, :null => false
    t.integer  "approver_id"
    t.boolean  "is_rejected",            :default => false, :null => false
    t.integer  "rejector_id"
    t.string   "reason"
    t.string   "remark"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "finance_transaction_id"
    t.integer  "school_id"
  end

  add_index "monthly_payslips", ["school_id"], :name => "index_monthly_payslips_on_school_id", :limit => {"school_id"=>nil}

  create_table "news", :force => true do |t|
    t.integer  "category_id", :limit => 1, :default => 1, :null => false
    t.string   "title"
    t.text     "content"
    t.integer  "author_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "news", ["school_id"], :name => "index_news_on_school_id", :limit => {"school_id"=>nil}

  create_table "news_acknowledges", :force => true do |t|
    t.integer "news_id"
    t.integer "acknowledged_by",    :limit => 1
    t.integer "acknowledged_by_id"
    t.integer "school_id"
    t.integer "status",             :limit => 1, :default => 1, :null => false
  end

  create_table "news_comments", :force => true do |t|
    t.text     "content"
    t.integer  "news_id"
    t.integer  "author_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.boolean  "is_approved", :default => false
    t.integer  "school_id"
  end

  add_index "news_comments", ["school_id"], :name => "index_news_comments_on_school_id", :limit => {"school_id"=>nil}

  create_table "oauth_authorizations", :force => true do |t|
    t.string   "user_id"
    t.integer  "oauth_client_id"
    t.string   "code"
    t.integer  "expires_at"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "oauth_clients", :force => true do |t|
    t.string   "name"
    t.string   "client_id"
    t.string   "client_secret"
    t.string   "redirect_uri"
    t.boolean  "verified"
    t.integer  "school_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.boolean  "is_visible",    :default => true, :null => false
  end

  create_table "oauth_tokens", :force => true do |t|
    t.string   "user_id"
    t.integer  "oauth_client_id"
    t.string   "access_token"
    t.string   "refresh_token"
    t.integer  "expires_at"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "observation_groups", :force => true do |t|
    t.string   "name"
    t.string   "header_name"
    t.string   "desc"
    t.string   "cce_grade_set_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "observation_kind"
    t.float    "max_marks"
    t.boolean  "is_deleted",       :default => false
    t.integer  "school_id"
  end

  add_index "observation_groups", ["school_id"], :name => "index_observation_groups_on_school_id", :limit => {"school_id"=>nil}

  create_table "observations", :force => true do |t|
    t.string   "name"
    t.string   "desc"
    t.boolean  "is_active"
    t.integer  "observation_group_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "sort_order"
    t.integer  "school_id"
  end

  add_index "observations", ["observation_group_id"], :name => "index_observations_on_observation_group_id", :limit => {"observation_group_id"=>nil}
  add_index "observations", ["school_id"], :name => "index_observations_on_school_id", :limit => {"school_id"=>nil}

  create_table "online_exam_attendances", :force => true do |t|
    t.integer  "online_exam_group_id"
    t.integer  "student_id"
    t.datetime "start_time"
    t.datetime "end_time"
    t.decimal  "total_score",          :precision => 7, :scale => 2
    t.boolean  "is_passed"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "online_exam_attendances", ["school_id"], :name => "index_online_exam_attendances_on_school_id", :limit => {"school_id"=>nil}

  create_table "online_exam_groups", :force => true do |t|
    t.string   "name"
    t.date     "start_date"
    t.date     "end_date"
    t.decimal  "maximum_time",    :precision => 7, :scale => 2
    t.decimal  "pass_percentage", :precision => 6, :scale => 2
    t.integer  "option_count"
    t.integer  "batch_id"
    t.boolean  "is_deleted",                                    :default => false
    t.boolean  "is_published",                                  :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "online_exam_groups", ["school_id"], :name => "index_online_exam_groups_on_school_id", :limit => {"school_id"=>nil}

  create_table "online_exam_options", :force => true do |t|
    t.integer  "online_exam_question_id"
    t.text     "option"
    t.boolean  "is_answer"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "online_exam_options", ["school_id"], :name => "index_online_exam_options_on_school_id", :limit => {"school_id"=>nil}

  create_table "online_exam_questions", :force => true do |t|
    t.integer  "online_exam_group_id"
    t.text     "question"
    t.decimal  "mark",                 :precision => 7, :scale => 2
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "online_exam_questions", ["school_id"], :name => "index_online_exam_questions_on_school_id", :limit => {"school_id"=>nil}

  create_table "online_exam_score_details", :force => true do |t|
    t.integer  "online_exam_question_id"
    t.integer  "online_exam_attendance_id"
    t.integer  "online_exam_option_id"
    t.boolean  "is_correct"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "online_exam_score_details", ["school_id"], :name => "index_online_exam_score_details_on_school_id", :limit => {"school_id"=>nil}

  create_table "online_meeting_members", :force => true do |t|
    t.integer  "member_id"
    t.integer  "online_meeting_room_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "online_meeting_members", ["school_id"], :name => "index_online_meeting_members_on_school_id", :limit => {"school_id"=>nil}

  create_table "online_meeting_rooms", :force => true do |t|
    t.integer  "server_id"
    t.integer  "user_id"
    t.string   "meetingid"
    t.string   "name"
    t.string   "attendee_password"
    t.string   "moderator_password"
    t.string   "welcome_msg"
    t.string   "logout_url"
    t.string   "voice_bridge"
    t.string   "dial_number"
    t.integer  "max_participants"
    t.boolean  "private",             :default => false
    t.boolean  "randomize_meetingid", :default => true
    t.boolean  "external",            :default => false
    t.string   "param"
    t.datetime "scheduled_on"
    t.boolean  "is_active",           :default => true
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "online_meeting_rooms", ["meetingid"], :name => "index_online_meeting_rooms_on_meetingid", :unique => true, :limit => {"meetingid"=>nil}
  add_index "online_meeting_rooms", ["school_id"], :name => "index_online_meeting_rooms_on_school_id", :limit => {"school_id"=>nil}
  add_index "online_meeting_rooms", ["server_id"], :name => "index_online_meeting_rooms_on_server_id", :limit => {"server_id"=>nil}
  add_index "online_meeting_rooms", ["voice_bridge"], :name => "index_online_meeting_rooms_on_voice_bridge", :unique => true, :limit => {"voice_bridge"=>nil}

  create_table "online_meeting_servers", :force => true do |t|
    t.string   "name"
    t.string   "url"
    t.string   "salt"
    t.string   "version"
    t.string   "param"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "online_meeting_servers", ["school_id"], :name => "index_online_meeting_servers_on_school_id", :limit => {"school_id"=>nil}

  create_table "optimized_images", :force => true do |t|
    t.text "image_name", :null => false
  end

  create_table "palette_queries", :force => true do |t|
    t.integer  "palette_id"
    t.text     "user_roles"
    t.text     "query"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.text     "parameters"
  end

  create_table "palettes", :force => true do |t|
    t.string   "name"
    t.string   "model_name"
    t.string   "icon"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "plugin"
    t.integer  "menu_id",    :default => 0
    t.string   "menu_type",  :default => "general"
  end

  create_table "payment_configurations", :force => true do |t|
    t.string   "config_key"
    t.string   "config_value"
    t.integer  "school_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "payments", :force => true do |t|
    t.string   "payee_type"
    t.integer  "payee_id"
    t.string   "payment_type"
    t.integer  "payment_id"
    t.text     "gateway_response"
    t.integer  "finance_transaction_id"
    t.integer  "school_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "payroll_categories", :force => true do |t|
    t.string   "name"
    t.float    "percentage"
    t.integer  "payroll_category_id"
    t.boolean  "is_deduction"
    t.boolean  "status"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.boolean  "is_deleted",          :default => false
    t.integer  "school_id"
  end

  add_index "payroll_categories", ["school_id"], :name => "index_payroll_categories_on_school_id", :limit => {"school_id"=>nil}

  create_table "period_entries", :force => true do |t|
    t.date     "month_date"
    t.integer  "batch_id"
    t.integer  "subject_id"
    t.integer  "class_timing_id"
    t.integer  "employee_id"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "period_entries", ["month_date", "batch_id"], :name => "index_period_entries_on_month_date_and_batch_id", :limit => {"batch_id"=>nil, "month_date"=>nil}
  add_index "period_entries", ["school_id"], :name => "index_period_entries_on_school_id", :limit => {"school_id"=>nil}

  create_table "pin_groups", :force => true do |t|
    t.text     "course_ids"
    t.date     "valid_from"
    t.date     "valid_till"
    t.string   "name"
    t.integer  "pin_count"
    t.boolean  "is_active"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  create_table "pin_numbers", :force => true do |t|
    t.string   "number"
    t.boolean  "is_active"
    t.boolean  "is_registered"
    t.integer  "pin_group_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  create_table "placement_registrations", :force => true do |t|
    t.integer  "student_id"
    t.integer  "placementevent_id"
    t.boolean  "is_applied",        :default => false
    t.boolean  "is_approved"
    t.boolean  "is_attended",       :default => false
    t.boolean  "is_placed",         :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "placement_registrations", ["school_id"], :name => "index_placement_registrations_on_school_id", :limit => {"school_id"=>nil}

  create_table "placementevents", :force => true do |t|
    t.string   "title"
    t.string   "company"
    t.string   "place"
    t.text     "description"
    t.boolean  "is_active",   :default => true
    t.datetime "date"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "placementevents", ["school_id"], :name => "index_placementevents_on_school_id", :limit => {"school_id"=>nil}

  create_table "poll_members", :force => true do |t|
    t.integer  "poll_question_id"
    t.integer  "member_id"
    t.string   "member_type"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "poll_members", ["school_id"], :name => "index_poll_members_on_school_id", :limit => {"school_id"=>nil}

  create_table "poll_options", :force => true do |t|
    t.integer  "poll_question_id"
    t.text     "option"
    t.integer  "sort_order"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "poll_options", ["school_id"], :name => "index_poll_options_on_school_id", :limit => {"school_id"=>nil}

  create_table "poll_questions", :force => true do |t|
    t.boolean  "is_active"
    t.string   "title"
    t.text     "description"
    t.boolean  "allow_custom_ans"
    t.integer  "poll_creator_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "poll_questions", ["school_id"], :name => "index_poll_questions_on_school_id", :limit => {"school_id"=>nil}

  create_table "poll_votes", :force => true do |t|
    t.integer  "poll_question_id"
    t.integer  "poll_option_id"
    t.string   "custom_answer"
    t.integer  "user_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "poll_votes", ["school_id"], :name => "index_poll_votes_on_school_id", :limit => {"school_id"=>nil}

  create_table "post_model_hit", :force => true do |t|
    t.integer "hit",       :null => false
    t.date    "hit_date",  :null => false
    t.string  "from_data"
    t.string  "name"
  end

  create_table "previous_exam_scores", :force => true do |t|
    t.integer  "student_id"
    t.integer  "exam_id"
    t.decimal  "marks",            :precision => 7, :scale => 2
    t.integer  "grading_level_id"
    t.string   "remarks"
    t.boolean  "is_failed"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "previous_exam_scores", ["school_id"], :name => "index_previous_exam_scores_on_school_id", :limit => {"school_id"=>nil}
  add_index "previous_exam_scores", ["student_id", "exam_id"], :name => "index_previous_exam_scores_on_student_id_and_exam_id", :limit => {"student_id"=>nil, "exam_id"=>nil}

  create_table "privilege_tags", :force => true do |t|
    t.string   "name_tag"
    t.integer  "priority"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "privileges", :force => true do |t|
    t.string   "name"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.text     "description"
    t.integer  "privilege_tag_id"
    t.integer  "priority"
    t.integer  "menu_id",          :default => 0
    t.string   "menu_type",        :default => "general"
  end

  create_table "privileges_users", :id => false, :force => true do |t|
    t.integer "user_id"
    t.integer "privilege_id"
  end

  add_index "privileges_users", ["user_id"], :name => "index_privileges_users_on_user_id", :limit => {"user_id"=>nil}

  create_table "purchase_items", :force => true do |t|
    t.integer  "quantity"
    t.decimal  "discount",          :precision => 10, :scale => 4
    t.decimal  "tax",               :precision => 10, :scale => 4
    t.decimal  "price",             :precision => 10, :scale => 4
    t.boolean  "is_deleted",                                       :default => false
    t.integer  "user_id"
    t.integer  "purchase_order_id"
    t.integer  "store_item_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "purchase_items", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "purchase_orders", :force => true do |t|
    t.string   "po_no"
    t.datetime "po_date"
    t.string   "po_status",        :default => "Pending"
    t.string   "reference"
    t.boolean  "is_deleted",       :default => false
    t.integer  "store_id"
    t.integer  "indent_id"
    t.integer  "supplier_id"
    t.integer  "supplier_type_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "purchase_orders", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "ranking_levels", :force => true do |t|
    t.string   "name",                                                                 :null => false
    t.decimal  "gpa",                :precision => 15, :scale => 2
    t.decimal  "marks",              :precision => 15, :scale => 2
    t.integer  "subject_count"
    t.integer  "priority"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.boolean  "full_course",                                       :default => false
    t.integer  "course_id"
    t.string   "subject_limit_type"
    t.string   "marks_limit_type"
    t.integer  "school_id"
  end

  add_index "ranking_levels", ["school_id"], :name => "index_ranking_levels_on_school_id", :limit => {"school_id"=>nil}

  create_table "record_updates", :force => true do |t|
    t.string   "file_name"
    t.integer  "school_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "redactor_uploads", :force => true do |t|
    t.string   "name"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "image_file_name"
    t.string   "image_content_type"
    t.integer  "image_file_size"
    t.datetime "image_updated_at"
    t.boolean  "is_used",            :default => false
    t.integer  "school_id"
  end

  create_table "refund_rules", :force => true do |t|
    t.integer  "finance_fee_collection_id"
    t.string   "name"
    t.date     "refund_validity"
    t.decimal  "refund_percentage",         :precision => 15, :scale => 4
    t.integer  "user_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "refund_rules", ["school_id"], :name => "index_refund_rules_on_school_id", :limit => {"school_id"=>nil}

  create_table "registration_courses", :force => true do |t|
    t.integer  "school_id"
    t.integer  "course_id"
    t.integer  "minimum_score"
    t.boolean  "is_active"
    t.float    "amount"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.boolean  "subject_based_fee_colletion"
    t.boolean  "enable_approval_system"
    t.integer  "min_electives"
    t.integer  "max_electives"
    t.boolean  "is_subject_based_registration"
    t.boolean  "include_additional_details"
    t.string   "additional_field_ids"
  end

  add_index "registration_courses", ["course_id"], :name => "index_registration_courses_on_course_id", :limit => {"course_id"=>nil}
  add_index "registration_courses", ["school_id"], :name => "index_registration_courses_on_school_id", :limit => {"school_id"=>nil}

  create_table "reminders", :force => true do |t|
    t.integer  "sender"
    t.integer  "recipient"
    t.string   "subject"
    t.text     "body"
    t.boolean  "is_read",                 :default => false
    t.boolean  "is_deleted_by_sender",    :default => false
    t.boolean  "is_deleted_by_recipient", :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "reminders", ["recipient"], :name => "index_reminders_on_recipient", :limit => {"recipient"=>nil}
  add_index "reminders", ["school_id"], :name => "index_reminders_on_school_id", :limit => {"school_id"=>nil}

  create_table "report_columns", :force => true do |t|
    t.integer  "report_id"
    t.string   "title"
    t.string   "method"
    t.integer  "position"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "report_columns", ["report_id"], :name => "index_report_columns_on_report_id", :limit => {"report_id"=>nil}
  add_index "report_columns", ["school_id"], :name => "index_report_columns_on_school_id", :limit => {"school_id"=>nil}

  create_table "report_queries", :force => true do |t|
    t.integer  "report_id"
    t.string   "table_name"
    t.string   "column_name"
    t.string   "criteria"
    t.text     "query"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "column_type"
    t.integer  "school_id"
  end

  add_index "report_queries", ["report_id"], :name => "index_report_queries_on_report_id", :limit => {"report_id"=>nil}
  add_index "report_queries", ["school_id"], :name => "index_report_queries_on_school_id", :limit => {"school_id"=>nil}

  create_table "reports", :force => true do |t|
    t.string   "name"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "model"
    t.integer  "school_id"
  end

  add_index "reports", ["school_id"], :name => "index_reports_on_school_id", :limit => {"school_id"=>nil}

  create_table "room_allocations", :force => true do |t|
    t.integer  "room_detail_id"
    t.integer  "student_id"
    t.boolean  "is_vacated",     :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "room_allocations", ["school_id"], :name => "index_room_allocations_on_school_id", :limit => {"school_id"=>nil}

  create_table "room_details", :force => true do |t|
    t.integer  "hostel_id"
    t.string   "room_number"
    t.integer  "students_per_room"
    t.decimal  "rent",              :precision => 15, :scale => 4
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "room_details", ["school_id"], :name => "index_room_details_on_school_id", :limit => {"school_id"=>nil}

  create_table "route_schedules", :force => true do |t|
    t.integer "route_id",           :null => false
    t.integer "weekday_id",         :null => false
    t.time    "home_pickup_time"
    t.time    "school_pickup_time"
    t.integer "school_id"
  end

  create_table "routes", :force => true do |t|
    t.string   "destination"
    t.string   "cost"
    t.integer  "main_route_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "routes", ["school_id"], :name => "index_routes_on_school_id", :limit => {"school_id"=>nil}

  create_table "school_assets", :force => true do |t|
    t.string   "asset_name"
    t.string   "asset_description"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "school_assets", ["school_id"], :name => "index_school_assets_on_school_id", :limit => {"school_id"=>nil}

  create_table "school_details", :force => true do |t|
    t.integer  "school_id"
    t.string   "logo_file_name"
    t.string   "logo_content_type"
    t.string   "logo_file_size"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "logo_updated_at"
    t.string   "cover_file_name"
    t.string   "cover_content_type"
    t.string   "cover_file_size"
  end

  create_table "school_domains", :force => true do |t|
    t.string   "domain"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "linkable_id"
    t.string   "linkable_type"
  end

  add_index "school_domains", ["linkable_id", "linkable_type"], :name => "index_school_domains_on_linkable_id_and_linkable_type", :limit => {"linkable_type"=>nil, "linkable_id"=>nil}

  create_table "school_group_users", :force => true do |t|
    t.integer  "admin_user_id"
    t.integer  "school_group_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "school_group_users", ["admin_user_id"], :name => "index_school_group_users_on_admin_user_id", :limit => {"admin_user_id"=>nil}
  add_index "school_group_users", ["school_group_id"], :name => "index_school_group_users_on_school_group_id", :limit => {"school_group_id"=>nil}

  create_table "school_groups", :force => true do |t|
    t.string   "name"
    t.integer  "admin_user_id"
    t.integer  "parent_group_id"
    t.string   "type"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.boolean  "whitelabel_enabled",    :default => false
    t.integer  "license_count"
    t.boolean  "inherit_sms_settings",  :default => false
    t.boolean  "inherit_smtp_settings", :default => false
    t.boolean  "is_deleted",            :default => false
  end

  add_index "school_groups", ["id", "type"], :name => "index_school_groups_on_id_and_type", :limit => {"id"=>nil, "type"=>nil}
  add_index "school_groups", ["type", "is_deleted"], :name => "index_school_groups_on_type_and_is_deleted", :limit => {"is_deleted"=>nil, "type"=>nil}
  add_index "school_groups", ["type", "parent_group_id", "is_deleted"], :name => "index_of_parent_group_on_active_group", :limit => {"parent_group_id"=>nil, "is_deleted"=>nil, "type"=>nil}
  add_index "school_groups", ["type", "parent_group_id"], :name => "index_school_groups_on_type_and_parent_group_id", :limit => {"parent_group_id"=>nil, "type"=>nil}
  add_index "school_groups", ["type"], :name => "index_school_groups_on_type", :limit => {"type"=>nil}

  create_table "schools", :force => true do |t|
    t.string   "name"
    t.string   "code"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "last_seeded_at"
    t.boolean  "is_deleted",            :default => false
    t.integer  "school_group_id"
    t.integer  "creator_id"
    t.boolean  "inherit_sms_settings",  :default => false
    t.boolean  "inherit_smtp_settings", :default => false
    t.boolean  "access_locked",         :default => false
  end

  add_index "schools", ["is_deleted"], :name => "index_schools_on_is_deleted", :limit => {"is_deleted"=>nil}
  add_index "schools", ["school_group_id", "is_deleted"], :name => "index_schools_on_school_group_id_and_is_deleted", :limit => {"school_group_id"=>nil, "is_deleted"=>nil}

  create_table "single_access_tokens", :force => true do |t|
    t.string   "client_name"
    t.string   "access_token"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "sms_logs", :force => true do |t|
    t.string   "mobile"
    t.string   "gateway_response"
    t.string   "sms_message_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "sms_logs", ["school_id"], :name => "index_sms_logs_on_school_id", :limit => {"school_id"=>nil}

  create_table "sms_messages", :force => true do |t|
    t.string   "body"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "sms_messages", ["school_id"], :name => "index_sms_messages_on_school_id", :limit => {"school_id"=>nil}

  create_table "sms_settings", :force => true do |t|
    t.string   "settings_key"
    t.boolean  "is_enabled",   :default => false
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "sms_settings", ["school_id"], :name => "index_sms_settings_on_school_id", :limit => {"school_id"=>nil}

  create_table "store_categories", :force => true do |t|
    t.string   "name"
    t.string   "code"
    t.boolean  "is_deleted", :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "store_categories", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "store_items", :force => true do |t|
    t.string   "item_name"
    t.integer  "quantity"
    t.decimal  "unit_price",   :precision => 10, :scale => 4
    t.decimal  "tax",          :precision => 10, :scale => 4
    t.string   "batch_number"
    t.boolean  "is_deleted",                                  :default => false
    t.integer  "store_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "store_items", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "store_types", :force => true do |t|
    t.string   "name"
    t.string   "code"
    t.boolean  "is_deleted", :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "store_types", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "stores", :force => true do |t|
    t.string   "name"
    t.string   "code"
    t.boolean  "is_deleted",        :default => false
    t.integer  "store_category_id"
    t.integer  "store_type_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "stores", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "student_activation_codes", :force => true do |t|
    t.integer "school_id",                              :null => false
    t.string  "code",                                   :null => false
    t.integer "is_active",  :limit => 1, :default => 1, :null => false
    t.integer "student_id",              :default => 0, :null => false
  end

  create_table "student_additional_details", :force => true do |t|
    t.integer  "student_id"
    t.integer  "additional_field_id"
    t.string   "additional_info"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "student_additional_details", ["school_id"], :name => "index_student_additional_details_on_school_id", :limit => {"school_id"=>nil}
  add_index "student_additional_details", ["student_id", "additional_field_id"], :name => "student_data_index", :limit => {"student_id"=>nil, "additional_field_id"=>nil}

  create_table "student_additional_field_options", :force => true do |t|
    t.integer  "student_additional_field_id"
    t.string   "field_option"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "student_additional_field_options", ["school_id"], :name => "index_student_additional_field_options_on_school_id", :limit => {"school_id"=>nil}

  create_table "student_additional_fields", :force => true do |t|
    t.string   "name"
    t.boolean  "status"
    t.boolean  "is_mandatory", :default => false
    t.string   "input_type"
    t.integer  "priority"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "student_additional_fields", ["school_id"], :name => "index_student_additional_fields_on_school_id", :limit => {"school_id"=>nil}

  create_table "student_categories", :force => true do |t|
    t.string   "name"
    t.boolean  "is_deleted", :default => false, :null => false
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "student_categories", ["school_id"], :name => "index_student_categories_on_school_id", :limit => {"school_id"=>nil}

  create_table "student_previous_datas", :force => true do |t|
    t.integer  "student_id"
    t.string   "institution"
    t.string   "year"
    t.string   "course"
    t.string   "total_mark"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "student_previous_datas", ["school_id"], :name => "index_student_previous_datas_on_school_id", :limit => {"school_id"=>nil}

  create_table "student_previous_subject_marks", :force => true do |t|
    t.integer  "student_id"
    t.string   "subject"
    t.string   "mark"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "student_previous_subject_marks", ["school_id"], :name => "index_student_previous_subject_marks_on_school_id", :limit => {"school_id"=>nil}

  create_table "students", :force => true do |t|
    t.string   "admission_no"
    t.string   "class_roll_no"
    t.date     "admission_date"
    t.string   "first_name"
    t.string   "middle_name"
    t.string   "last_name"
    t.integer  "batch_id"
    t.date     "date_of_birth"
    t.string   "gender"
    t.string   "blood_group"
    t.string   "birth_place"
    t.integer  "nationality_id"
    t.string   "language"
    t.string   "religion"
    t.integer  "student_category_id"
    t.string   "address_line1"
    t.string   "address_line2"
    t.string   "city"
    t.string   "state"
    t.string   "student_activation_code"
    t.string   "pin_code"
    t.integer  "country_id"
    t.string   "phone1"
    t.string   "phone2"
    t.string   "email"
    t.integer  "immediate_contact_id"
    t.boolean  "is_sms_enabled",                              :default => true
    t.string   "photo_file_name"
    t.string   "photo_content_type"
    t.binary   "photo_data",              :limit => 16777215
    t.string   "status_description"
    t.boolean  "is_active",                                   :default => true
    t.boolean  "is_deleted",                                  :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.boolean  "has_paid_fees",                               :default => false
    t.integer  "photo_file_size"
    t.integer  "user_id"
    t.boolean  "is_email_enabled",                            :default => true
    t.integer  "sibling_id"
    t.datetime "photo_updated_at"
    t.string   "library_card"
    t.integer  "school_id"
    t.integer  "class_teacher_id",                            :default => 0,     :null => false
  end

  add_index "students", ["admission_no"], :name => "index_students_on_admission_no", :limit => {"admission_no"=>"10"}
  add_index "students", ["batch_id"], :name => "index_students_on_batch_id", :limit => {"batch_id"=>nil}
  add_index "students", ["first_name", "middle_name", "last_name"], :name => "index_students_on_first_name_and_middle_name_and_last_name", :limit => {"first_name"=>"10", "last_name"=>"10", "middle_name"=>"10"}
  add_index "students", ["nationality_id", "immediate_contact_id", "student_category_id"], :name => "student_data_index", :limit => {"nationality_id"=>nil, "immediate_contact_id"=>nil, "student_category_id"=>nil}
  add_index "students", ["school_id"], :name => "index_students_on_school_id", :limit => {"school_id"=>nil}
  add_index "students", ["user_id"], :name => "index_students_on_user_id", :limit => {"user_id"=>nil}

  create_table "students_subjects", :force => true do |t|
    t.integer  "student_id"
    t.integer  "subject_id"
    t.integer  "batch_id"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "students_subjects", ["school_id"], :name => "index_students_subjects_on_school_id", :limit => {"school_id"=>nil}
  add_index "students_subjects", ["student_id", "subject_id"], :name => "index_students_subjects_on_student_id_and_subject_id", :limit => {"subject_id"=>nil, "student_id"=>nil}

  create_table "subject_amounts", :force => true do |t|
    t.integer  "course_id"
    t.decimal  "amount",     :precision => 15, :scale => 4
    t.string   "code"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "subject_amounts", ["school_id"], :name => "index_subject_amounts_on_school_id", :limit => {"school_id"=>nil}

  create_table "subject_leaves", :force => true do |t|
    t.integer  "student_id"
    t.date     "month_date"
    t.integer  "subject_id"
    t.integer  "employee_id"
    t.integer  "class_timing_id"
    t.string   "reason"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "batch_id"
    t.integer  "school_id"
  end

  add_index "subject_leaves", ["month_date", "subject_id", "batch_id"], :name => "index_subject_leaves_on_month_date_and_subject_id_and_batch_id", :limit => {"subject_id"=>nil, "batch_id"=>nil, "month_date"=>nil}
  add_index "subject_leaves", ["school_id"], :name => "index_subject_leaves_on_school_id", :limit => {"school_id"=>nil}
  add_index "subject_leaves", ["student_id", "batch_id"], :name => "index_subject_leaves_on_student_id_and_batch_id", :limit => {"student_id"=>nil, "batch_id"=>nil}

  create_table "subjects", :force => true do |t|
    t.string   "name"
    t.string   "code"
    t.integer  "batch_id"
    t.string   "icon_number"
    t.boolean  "no_exams",                                          :default => false
    t.integer  "max_weekly_classes"
    t.integer  "elective_group_id"
    t.boolean  "is_deleted",                                        :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.decimal  "credit_hours",       :precision => 15, :scale => 2
    t.boolean  "prefer_consecutive",                                :default => false
    t.decimal  "amount",             :precision => 15, :scale => 4
    t.integer  "school_id"
  end

  add_index "subjects", ["batch_id", "elective_group_id", "is_deleted"], :name => "index_subjects_on_batch_id_and_elective_group_id_and_is_deleted", :limit => {"batch_id"=>nil, "elective_group_id"=>nil, "is_deleted"=>nil}
  add_index "subjects", ["school_id"], :name => "index_subjects_on_school_id", :limit => {"school_id"=>nil}

  create_table "supplier_types", :force => true do |t|
    t.string   "name"
    t.string   "code"
    t.boolean  "is_deleted", :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "supplier_types", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "suppliers", :force => true do |t|
    t.string   "name"
    t.string   "contact_no"
    t.text     "address"
    t.integer  "tin_no"
    t.string   "region"
    t.text     "help_desk"
    t.boolean  "is_deleted",       :default => false
    t.integer  "supplier_type_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "suppliers", ["school_id"], :name => "by_school_id", :limit => {"school_id"=>nil}

  create_table "syllabuses", :force => true do |t|
    t.string   "title"
    t.integer  "batch_id",                                  :null => false
    t.integer  "exam_group_id",                             :null => false
    t.integer  "is_yearly",     :limit => 1, :default => 0, :null => false
    t.integer  "subject_id",                                :null => false
    t.text     "content",                                   :null => false
    t.integer  "author_id",                                 :null => false
    t.datetime "created_at",                                :null => false
    t.datetime "updated_at",                                :null => false
    t.integer  "school_id",                                 :null => false
  end

  create_table "taggings", :force => true do |t|
    t.integer  "tag_id"
    t.integer  "taggable_id"
    t.string   "taggable_type"
    t.datetime "created_at"
    t.integer  "school_id"
    t.datetime "updated_at"
  end

  add_index "taggings", ["school_id"], :name => "index_taggings_on_school_id", :limit => {"school_id"=>nil}
  add_index "taggings", ["tag_id"], :name => "index_taggings_on_tag_id", :limit => {"tag_id"=>nil}
  add_index "taggings", ["taggable_id", "taggable_type"], :name => "index_taggings_on_taggable_id_and_taggable_type", :limit => {"taggable_id"=>nil, "taggable_type"=>nil}

  create_table "tags", :force => true do |t|
    t.string   "name"
    t.integer  "school_id"
    t.datetime "updated_at"
    t.datetime "created_at"
  end

  add_index "tags", ["school_id"], :name => "index_tags_on_school_id", :limit => {"school_id"=>nil}

  create_table "tally_accounts", :force => true do |t|
    t.integer  "school_id"
    t.string   "account_name"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "tally_companies", :force => true do |t|
    t.integer  "school_id"
    t.string   "company_name"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "tally_export_configurations", :force => true do |t|
    t.integer  "school_id"
    t.string   "config_key"
    t.string   "config_value"
    t.datetime "updated_at"
    t.datetime "created_at"
  end

  create_table "tally_export_files", :force => true do |t|
    t.integer  "download_no",              :default => 0
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
    t.string   "export_file_file_name"
    t.string   "export_file_content_type"
    t.integer  "export_file_file_size"
    t.datetime "export_file_updated_at"
  end

  create_table "tally_export_logs", :force => true do |t|
    t.integer  "school_id"
    t.integer  "finance_transaction_id"
    t.boolean  "status"
    t.string   "message"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "tally_export_logs", ["status"], :name => "index_tally_export_logs_on_status", :limit => {"status"=>nil}
  add_index "tally_export_logs", ["updated_at"], :name => "by_updation", :limit => {"updated_at"=>nil}

  create_table "tally_ledgers", :force => true do |t|
    t.integer  "school_id"
    t.string   "ledger_name"
    t.integer  "tally_company_id"
    t.integer  "tally_voucher_type_id"
    t.integer  "tally_account_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "tally_voucher_types", :force => true do |t|
    t.integer  "school_id"
    t.string   "voucher_name"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "task_assignees", :force => true do |t|
    t.integer  "task_id"
    t.integer  "assignee_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "task_assignees", ["school_id"], :name => "index_task_assignees_on_school_id", :limit => {"school_id"=>nil}

  create_table "task_comments", :force => true do |t|
    t.integer  "user_id"
    t.integer  "task_id"
    t.text     "description"
    t.string   "attachment_file_name"
    t.string   "attachment_content_type"
    t.integer  "attachment_file_size"
    t.datetime "attachment_updated_at"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "task_comments", ["school_id"], :name => "index_task_comments_on_school_id", :limit => {"school_id"=>nil}

  create_table "tasks", :force => true do |t|
    t.integer  "user_id"
    t.string   "title"
    t.text     "description"
    t.string   "status"
    t.date     "start_date"
    t.date     "due_date"
    t.string   "attachment_file_name"
    t.string   "attachment_content_type"
    t.integer  "attachment_file_size"
    t.datetime "attachment_updated_at"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "tasks", ["school_id"], :name => "index_tasks_on_school_id", :limit => {"school_id"=>nil}

  create_table "tds_ad", :force => true do |t|
    t.string    "name",                :limit => 150,                         :null => false
    t.string    "url_link"
    t.integer   "user_id",                                                    :null => false
    t.integer   "type",                :limit => 1,          :default => 1,   :null => false
    t.timestamp "start_date"
    t.timestamp "end_date"
    t.integer   "total_view",          :limit => 8,          :default => 0,   :null => false
    t.integer   "total_click",         :limit => 8,          :default => 0,   :null => false
    t.integer   "is_active",           :limit => 1,          :default => 0,   :null => false
    t.string    "location",            :limit => 50
    t.string    "country_iso",         :limit => 2
    t.integer   "gender",              :limit => 1
    t.integer   "age_from",            :limit => 1
    t.integer   "age_to",              :limit => 1
    t.string    "user_group"
    t.text      "html_code",           :limit => 2147483647
    t.string    "image_path"
    t.string    "imagethumb_path"
    t.string    "gmt_offset",          :limit => 15
    t.string    "disallow_controller"
    t.integer   "for_all",                                   :default => 0,   :null => false
    t.integer   "priority",                                  :default => 200, :null => false
    t.integer   "plan_id",                                                    :null => false
    t.integer   "sponser_id",                                :default => 1,   :null => false
    t.string    "menu_ci_key",                               :default => "0", :null => false
  end

  add_index "tds_ad", ["plan_id"], :name => "foreign_plan_idx", :limit => {"plan_id"=>nil}
  add_index "tds_ad", ["sponser_id"], :name => "foreign_sponser_idx", :limit => {"sponser_id"=>nil}
  add_index "tds_ad", ["user_id"], :name => "foreign_users_idx", :limit => {"user_id"=>nil}

  create_table "tds_ad_menu", :force => true do |t|
    t.integer "menu_id", :null => false
    t.integer "ad_id",   :null => false
  end

  add_index "tds_ad_menu", ["menu_id"], :name => "foreign_menu_idx", :limit => {"menu_id"=>nil}

  create_table "tds_ad_plan", :force => true do |t|
    t.string  "d_width",       :limit => 4
    t.string  "d_height",      :limit => 4
    t.integer "block",         :limit => 1,        :default => 0,       :null => false
    t.string  "title",                                                  :null => false
    t.text    "cost",          :limit => 16777215,                      :null => false
    t.boolean "is_active",                         :default => false,   :null => false
    t.integer "qty",                               :default => 1,       :null => false
    t.string  "link_location", :limit => 20,       :default => "index", :null => false
    t.integer "priority",                          :default => 200,     :null => false
    t.integer "is_ok",                             :default => 0,       :null => false
  end

  add_index "tds_ad_plan", ["block", "is_active"], :name => "block_id", :limit => {"is_active"=>nil, "block"=>nil}

  create_table "tds_ad_section", :force => true do |t|
    t.integer "section_menu_id",                                :null => false
    t.integer "ad_id",                                          :null => false
    t.integer "ad_plan_id",                                     :null => false
    t.string  "menu_ci_key",     :limit => 50, :default => "0", :null => false
    t.string  "location",        :limit => 20,                  :null => false
  end

  create_table "tds_ad_sponsers", :force => true do |t|
    t.string "sponser_name", :limit => 100
    t.string "sponser_url"
    t.string "sponser_desc"
  end

  create_table "tds_ad_track", :force => true do |t|
    t.integer   "ad_id",                                   :null => false
    t.integer   "user_id",                  :default => 0
    t.string    "ip_address", :limit => 15,                :null => false
    t.timestamp "time_stamp",                              :null => false
    t.string    "session_id", :limit => 45
  end

  add_index "tds_ad_track", ["ad_id", "ip_address"], :name => "ad_id_2", :limit => {"ip_address"=>nil, "ad_id"=>nil}
  add_index "tds_ad_track", ["ad_id", "user_id"], :name => "ad_id", :limit => {"user_id"=>nil, "ad_id"=>nil}
  add_index "tds_ad_track", ["user_id"], :name => "foreign_user_idx", :limit => {"user_id"=>nil}

  create_table "tds_assesment_mark", :force => true do |t|
    t.integer   "user_id",                                                           :null => false
    t.integer   "assessment_id",                                                     :null => false
    t.integer   "mark",                                             :default => 0,   :null => false
    t.integer   "time_taken",                                       :default => 0
    t.integer   "no_played",                                        :default => 0
    t.decimal   "avg_time_per_ques", :precision => 10, :scale => 2, :default => 0.0
    t.timestamp "created_date",                                                      :null => false
  end

  create_table "tds_assessment", :force => true do |t|
    t.string    "title",                                     :null => false
    t.integer   "use_time",     :limit => 1, :default => 1,  :null => false
    t.integer   "time",                      :default => 30, :null => false
    t.timestamp "created_date",                              :null => false
    t.integer   "played",                    :default => 0,  :null => false
    t.string    "topic",                                     :null => false
  end

  create_table "tds_assessment_option", :force => true do |t|
    t.integer "question_id",                     :null => false
    t.text    "answer"
    t.string  "answer_image"
    t.boolean "correct",      :default => false, :null => false
  end

  create_table "tds_assessment_question", :force => true do |t|
    t.integer   "assesment_id",                   :null => false
    t.text      "question",                       :null => false
    t.text      "explanation"
    t.integer   "mark",         :default => 10,   :null => false
    t.integer   "time"
    t.boolean   "style",        :default => true, :null => false
    t.timestamp "created_date",                   :null => false
  end

  create_table "tds_bylines", :force => true do |t|
    t.string    "title",        :limit => 250,                    :null => false
    t.timestamp "created",                                        :null => false
    t.timestamp "updated"
    t.integer   "is_columnist", :limit => 1,   :default => 0
    t.integer   "priority",                    :default => 0
    t.string    "image"
    t.boolean   "is_feature",                  :default => false, :null => false
    t.integer   "type",         :limit => 1,   :default => 1,     :null => false
    t.integer   "user_id",                     :default => 0,     :null => false
    t.string    "designation"
  end

  create_table "tds_captcha", :primary_key => "captcha_id", :force => true do |t|
    t.integer "captcha_time",                                :null => false
    t.string  "ip_address",   :limit => 16, :default => "0", :null => false
    t.string  "word",         :limit => 20,                  :null => false
  end

  add_index "tds_captcha", ["word"], :name => "word", :limit => {"word"=>nil}

  create_table "tds_categories", :force => true do |t|
    t.string  "name",                                                :null => false
    t.string  "display_name"
    t.text    "description",                                         :null => false
    t.integer "category_type",    :limit => 1, :default => 1,        :null => false
    t.text    "embedded",                                            :null => false
    t.string  "cover"
    t.string  "icon"
    t.string  "menu_icon"
    t.integer "game_type",        :limit => 1, :default => 0,        :null => false
    t.integer "status",           :limit => 1, :default => 1,        :null => false
    t.integer "show",             :limit => 1, :default => 1,        :null => false
    t.integer "parent_id"
    t.integer "category_type_id"
    t.integer "priority"
    t.string  "background_color", :limit => 7, :default => "003773", :null => false
    t.boolean "enable_sort",                   :default => true,     :null => false
    t.integer "weekly_priority"
    t.string  "responsible"
    t.string  "backup"
  end

  add_index "tds_categories", ["category_type_id"], :name => "foreign_category_type_idx", :limit => {"category_type_id"=>nil}
  add_index "tds_categories", ["name", "parent_id"], :name => "name", :unique => true, :limit => {"parent_id"=>nil, "name"=>nil}

  create_table "tds_category_cover", :force => true do |t|
    t.integer "category_id", :null => false
    t.string  "image",       :null => false
    t.date    "issue_date",  :null => false
  end

  create_table "tds_category_pdf", :force => true do |t|
    t.integer "category_id", :null => false
    t.string  "pdf",         :null => false
    t.date    "issue_date",  :null => false
  end

  create_table "tds_category_type", :force => true do |t|
    t.string  "type_name", :limit => 45
    t.integer "is_active", :limit => 1,  :default => 1
  end

  create_table "tds_channels", :force => true do |t|
    t.string  "name",                                    :null => false
    t.string  "logo",      :limit => 150
    t.integer "is_active", :limit => 1,   :default => 1
  end

  create_table "tds_contact_us", :force => true do |t|
    t.string   "full_name",           :limit => 150,                       :null => false
    t.string   "email",               :limit => 150,                       :null => false
    t.string   "contact_type",        :limit => 50,                        :null => false
    t.text     "description",         :limit => 2147483647,                :null => false
    t.datetime "created_date",                                             :null => false
    t.datetime "modified_date"
    t.integer  "answered_by_user_id"
    t.datetime "answered_date"
    t.integer  "status",              :limit => 1,          :default => 1, :null => false
  end

  create_table "tds_controllers", :force => true do |t|
    t.string    "name",                                       :null => false
    t.string    "controller",                                 :null => false
    t.integer   "allow_from_all", :limit => 1, :default => 1, :null => false
    t.timestamp "created",                                    :null => false
    t.timestamp "updated",                                    :null => false
  end

  add_index "tds_controllers", ["controller"], :name => "unq_controller", :unique => true, :limit => {"controller"=>nil}
  add_index "tds_controllers", ["name"], :name => "unq_name", :unique => true, :limit => {"name"=>nil}

  create_table "tds_countries", :force => true do |t|
    t.string   "name"
    t.datetime "updated_at"
    t.datetime "created_at"
  end

  create_table "tds_free_user_preference", :force => true do |t|
    t.integer "free_user_id", :null => false
    t.text    "category_ids"
  end

  create_table "tds_free_users", :force => true do |t|
    t.string   "username"
    t.string   "nick_name"
    t.string   "email"
    t.string   "password"
    t.string   "salt",                    :limit => 50
    t.string   "fb_profile_id",           :limit => 100
    t.string   "fb_profile_url"
    t.string   "gl_profile_id",           :limit => 100
    t.string   "google_profile_url"
    t.string   "first_name",              :limit => 50
    t.string   "middle_name",             :limit => 50
    t.string   "last_name",               :limit => 50
    t.string   "tds_country_id",          :limit => 150
    t.string   "district",                :limit => 100
    t.string   "mobile_no",               :limit => 25
    t.date     "dob"
    t.integer  "medium",                  :limit => 1
    t.boolean  "gender"
    t.text     "grade_ids"
    t.boolean  "user_type",                              :default => true, :null => false
    t.string   "user_type_text",          :limit => 150
    t.string   "cover_image"
    t.string   "profile_image"
    t.string   "student_activation_code"
    t.integer  "status",                  :limit => 1,   :default => 1
    t.string   "school_name"
    t.string   "location"
    t.string   "teaching_for"
    t.string   "occupation"
    t.string   "password_reset_token"
    t.datetime "reset_token_expire"
    t.integer  "paid_id"
    t.string   "paid_username"
    t.string   "paid_password"
    t.integer  "paid_school_id"
    t.string   "paid_school_code"
    t.string   "designation"
  end

  create_table "tds_functions", :force => true do |t|
    t.string    "name",                                       :null => false
    t.string    "function",                                   :null => false
    t.integer   "controller_id"
    t.integer   "allow_from_all", :limit => 1, :default => 1, :null => false
    t.timestamp "created",                                    :null => false
    t.timestamp "updated",                                    :null => false
  end

  create_table "tds_gallery", :force => true do |t|
    t.string  "gallery_name", :limit => 100, :null => false
    t.integer "gallery_type", :limit => 1,   :null => false
  end

  add_index "tds_gallery", ["gallery_name"], :name => "gallery_name_UNIQUE", :unique => true, :limit => {"gallery_name"=>nil}

  create_table "tds_gcm_ids", :force => true do |t|
    t.text "gcm_id",    :null => false
    t.text "device_id"
  end

  add_index "tds_gcm_ids", ["id"], :name => "id", :unique => true, :limit => {"id"=>nil}

  create_table "tds_general_knowladge", :force => true do |t|
    t.string  "question",                                         :null => false
    t.string  "ans1",                                             :null => false
    t.string  "ans2",                                             :null => false
    t.string  "ans3",                                             :null => false
    t.string  "ans4",                                             :null => false
    t.integer "correct",      :limit => 1, :default => 1,         :null => false
    t.integer "post_id",                   :default => 0,         :null => false
    t.date    "post_date",                                        :null => false
    t.integer "layout",       :limit => 1, :default => 1,         :null => false
    t.string  "layout_color",              :default => "#52BE80", :null => false
  end

  create_table "tds_grades", :force => true do |t|
    t.string  "grade_name",                             :null => false
    t.integer "status",     :limit => 1, :default => 1, :null => false
  end

  create_table "tds_groups", :force => true do |t|
    t.string    "name",        :null => false
    t.text      "description"
    t.timestamp "created",     :null => false
    t.timestamp "updated",     :null => false
  end

  add_index "tds_groups", ["name"], :name => "name", :unique => true, :limit => {"name"=>nil}

  create_table "tds_groups_controllers", :force => true do |t|
    t.integer   "group_id",      :null => false
    t.integer   "controller_id", :null => false
    t.timestamp "created",       :null => false
    t.timestamp "updated",       :null => false
  end

  add_index "tds_groups_controllers", ["controller_id"], :name => "controller_id", :limit => {"controller_id"=>nil}
  add_index "tds_groups_controllers", ["group_id"], :name => "group_id", :limit => {"group_id"=>nil}

  create_table "tds_groups_functions", :force => true do |t|
    t.integer   "group_id",    :null => false
    t.integer   "function_id", :null => false
    t.timestamp "created",     :null => false
    t.timestamp "updated",     :null => false
  end

  add_index "tds_groups_functions", ["function_id"], :name => "function_id", :limit => {"function_id"=>nil}
  add_index "tds_groups_functions", ["group_id"], :name => "group_id", :limit => {"group_id"=>nil}

  create_table "tds_groups_users", :force => true do |t|
    t.integer   "group_id", :null => false
    t.integer   "user_id",  :null => false
    t.timestamp "created",  :null => false
    t.timestamp "updated",  :null => false
  end

  create_table "tds_homepage_data", :force => true do |t|
    t.integer "post_id",                                 :null => false
    t.integer "post_type", :limit => 1, :default => 1,   :null => false
    t.string  "priority",               :default => "0", :null => false
    t.date    "date",                                    :null => false
    t.integer "status",    :limit => 1, :default => 1,   :null => false
  end

  create_table "tds_inpictures_author", :force => true do |t|
    t.string    "name",                                      :null => false
    t.string    "email",                                     :null => false
    t.string    "phone",        :limit => 50,                :null => false
    t.timestamp "created_date",                              :null => false
    t.string    "profession",                                :null => false
    t.string    "address"
    t.integer   "is_active",    :limit => 1,  :default => 1, :null => false
  end

  create_table "tds_inpictures_photos", :force => true do |t|
    t.integer   "author_id",     :null => false
    t.integer   "theme_id",      :null => false
    t.text      "photo_caption", :null => false
    t.timestamp "date_taken",    :null => false
    t.string    "image",         :null => false
  end

  create_table "tds_inpictures_theme", :force => true do |t|
    t.string    "name",                                     :null => false
    t.timestamp "publish_date",                             :null => false
    t.text      "description",                              :null => false
    t.string    "image"
    t.integer   "is_current",   :limit => 1, :default => 0, :null => false
    t.integer   "is_active",    :limit => 1, :default => 1, :null => false
  end

  create_table "tds_issue_date", :force => true do |t|
    t.date "issue_date"
  end

  create_table "tds_keywords", :force => true do |t|
    t.string "value", :null => false
  end

  create_table "tds_material_menu", :force => true do |t|
    t.integer "material_id", :null => false
    t.integer "menu_id",     :null => false
    t.integer "type",        :null => false
    t.string  "sname"
    t.date    "issue_date",  :null => false
  end

  create_table "tds_materials", :force => true do |t|
    t.string  "material_url", :limit => 200,                :null => false
    t.integer "gallery_id",                                 :null => false
    t.date    "imagedate",                                  :null => false
    t.text    "caption",                                    :null => false
    t.string  "source",       :limit => 100,                :null => false
    t.integer "video_id",                    :default => 0, :null => false
    t.integer "menu_id",                     :default => 0, :null => false
  end

  add_index "tds_materials", ["gallery_id"], :name => "foreign_gallery_id_idx", :limit => {"gallery_id"=>nil}

  create_table "tds_materials_video", :force => true do |t|
    t.string  "video_id",                                        :null => false
    t.string  "video_type",       :limit => 8,                   :null => false
    t.string  "thumb_small",                     :default => "", :null => false
    t.string  "thumb_medium",                    :default => "", :null => false
    t.string  "thumb_large",                     :default => "", :null => false
    t.string  "additional_thumb",                :default => "", :null => false
    t.string  "video_title",      :limit => 500
    t.string  "url"
    t.integer "likes",                           :default => 0,  :null => false
    t.integer "views",                           :default => 0,  :null => false
    t.integer "comments",                        :default => 0,  :null => false
    t.integer "duration",                        :default => 0,  :null => false
    t.integer "width",                           :default => 0,  :null => false
    t.integer "height",                          :default => 0,  :null => false
    t.string  "video_cat",        :limit => 300,                 :null => false
    t.text    "tags",                                            :null => false
    t.integer "material_id",                                     :null => false
  end

  add_index "tds_materials_video", ["video_type"], :name => "video_type", :limit => {"video_type"=>nil}

  create_table "tds_menu", :force => true do |t|
    t.integer   "type",                   :limit => 1
    t.string    "title",                  :limit => 80
    t.string    "icon_name",              :limit => 20
    t.string    "image"
    t.integer   "is_active",              :limit => 1,          :default => 0
    t.integer   "has_right",              :limit => 1,          :default => 1,     :null => false
    t.integer   "full_custom",            :limit => 1,          :default => 0,     :null => false
    t.integer   "all_ad",                 :limit => 1,          :default => 1,     :null => false
    t.integer   "position",               :limit => 1
    t.string    "footer_group",           :limit => 20
    t.string    "ci_key"
    t.string    "link_type",              :limit => 0
    t.text      "link_text",              :limit => 2147483647
    t.string    "permalink"
    t.integer   "category_id"
    t.integer   "news_id"
    t.integer   "news_num",                                     :default => 2
    t.integer   "priority",               :limit => 1,          :default => 1,     :null => false
    t.timestamp "created"
    t.timestamp "updated"
    t.date      "startdate"
    t.date      "expired"
    t.integer   "parent_menu_id"
    t.string    "gallery_name"
    t.boolean   "has_gallery",                                  :default => false, :null => false
    t.integer   "show_gallery",           :limit => 1,          :default => 1,     :null => false
    t.integer   "ad_plan_id_for_gallery",                       :default => 0,     :null => false
    t.string    "twitter_name"
    t.integer   "widget_id",              :limit => 8,          :default => 0,     :null => false
    t.integer   "has_ad",                 :limit => 1,          :default => 0,     :null => false
  end

  add_index "tds_menu", ["type", "position", "ci_key"], :name => "ci_key", :unique => true, :limit => {"ci_key"=>nil, "position"=>nil, "type"=>nil}

  create_table "tds_options", :force => true do |t|
    t.integer "ques_id"
    t.string  "value",   :limit => 300, :null => false
  end

  create_table "tds_paid_schools", :force => true do |t|
    t.string  "school_name"
    t.integer "school_id"
    t.string  "school_code",   :limit => 50
    t.string  "redirect_url",                                   :null => false
    t.string  "client_id",     :limit => 64,                    :null => false
    t.string  "client_secret", :limit => 64,                    :null => false
    t.string  "username",      :limit => 100,                   :null => false
    t.string  "password",      :limit => 150,                   :null => false
    t.boolean "is_active",                    :default => true, :null => false
  end

  create_table "tds_paid_users", :force => true do |t|
    t.integer "free_user_id",                 :null => false
    t.integer "paid_user_id",                 :null => false
    t.boolean "paid_user_type",               :null => false
    t.integer "paid_profile_id",              :null => false
    t.integer "paid_school_id",               :null => false
    t.integer "is_active",       :limit => 1, :null => false
  end

  create_table "tds_personality", :force => true do |t|
    t.string  "name",        :limit => 200,                :null => false
    t.string  "description"
    t.integer "is_active",   :limit => 1,   :default => 1, :null => false
  end

  create_table "tds_pin_post", :force => true do |t|
    t.integer "category_id", :default => 0, :null => false
    t.integer "post_id",                    :null => false
    t.integer "position",                   :null => false
  end

  create_table "tds_post", :force => true do |t|
    t.integer   "force_assessment",                            :default => 0,       :null => false
    t.integer   "assessment_id"
    t.text      "shoulder"
    t.text      "headline",                                                         :null => false
    t.string    "headline_color",                              :default => "0",     :null => false
    t.string    "sub_head"
    t.integer   "byline_id"
    t.text      "summary"
    t.text      "embedded"
    t.text      "short_title"
    t.string    "google_short_url"
    t.text      "content",               :limit => 2147483647
    t.boolean   "can_comment",                                 :default => false
    t.string    "lead_material"
    t.string    "mobile_image"
    t.integer   "mobile_view_type",      :limit => 1,          :default => 1,       :null => false
    t.integer   "post_type",             :limit => 1,          :default => 1,       :null => false
    t.integer   "status",                :limit => 1,          :default => 1,       :null => false
    t.integer   "show",                  :limit => 1,          :default => 1,       :null => false
    t.integer   "for_all",               :limit => 1,          :default => 1,       :null => false
    t.string    "type",                  :limit => 0,          :default => "Print", :null => false
    t.integer   "is_featured",           :limit => 1
    t.integer   "is_breaking",           :limit => 1
    t.timestamp "breaking_expire"
    t.integer   "is_developing",         :limit => 1
    t.integer   "is_exclusive",          :limit => 1
    t.timestamp "exclusive_expired"
    t.string    "latitude"
    t.string    "longitude"
    t.integer   "view_count",            :limit => 8,          :default => 0
    t.timestamp "published_date"
    t.date      "publish_date_only"
    t.integer   "priority_type",         :limit => 1,          :default => 5
    t.integer   "priority"
    t.timestamp "created"
    t.timestamp "updated"
    t.boolean   "has_image",                                   :default => false,   :null => false
    t.boolean   "has_video",                                   :default => false,   :null => false
    t.boolean   "has_pdf",                                     :default => false,   :null => false
    t.boolean   "has_related_news",                            :default => false,   :null => false
    t.timestamp "news_expire_date",                                                 :null => false
    t.text      "lead_caption"
    t.string    "lead_source"
    t.text      "meta_description"
    t.text      "ip_address"
    t.integer   "referance_id",                                :default => 0,       :null => false
    t.string    "language",              :limit => 25,         :default => "en",    :null => false
    t.text      "other_language"
    t.integer   "all_country",                                 :default => 1,       :null => false
    t.integer   "feature_position",      :limit => 1,          :default => 1,       :null => false
    t.integer   "user_view_count",                             :default => 0,       :null => false
    t.integer   "game_type",             :limit => 1
    t.string    "lead_link"
    t.string    "attach"
    t.integer   "user_type",                                   :default => 1,       :null => false
    t.string    "mobile_num"
    t.text      "layout_color"
    t.boolean   "layout",                                      :default => true,    :null => false
    t.boolean   "show_byline_image",                           :default => false,   :null => false
    t.text      "mobile_content"
    t.integer   "pdf_top",               :limit => 1,          :default => 0,       :null => false
    t.integer   "post_layout",                                 :default => 0,       :null => false
    t.integer   "sort_title_type",                             :default => 0,       :null => false
    t.string    "inside_image"
    t.string    "video_file"
    t.integer   "school_id",                                   :default => 0,       :null => false
    t.integer   "teacher_id",                                  :default => 0,       :null => false
    t.integer   "candle_type",                                 :default => 0,       :null => false
    t.string    "subject"
    t.boolean   "show_comment_to_all",                         :default => true,    :null => false
    t.integer   "user_id"
    t.boolean   "force_web_view_mobie",                        :default => false,   :null => false
    t.integer   "wow_count",                                   :default => 0,       :null => false
    t.string    "author_image_post"
    t.integer   "category_id"
    t.boolean   "related_post_type",                           :default => true,    :null => false
    t.integer   "share_post_id",                               :default => 0,       :null => false
    t.integer   "share_by",                                    :default => 0
    t.integer   "subcategory_id_to_use"
  end

  create_table "tds_post_attachment", :force => true do |t|
    t.integer "post_id",                               :null => false
    t.string  "file_name",                             :null => false
    t.integer "show",      :limit => 1, :default => 1, :null => false
    t.string  "caption"
  end

  create_table "tds_post_category", :force => true do |t|
    t.integer "post_id",        :limit => 8,                :null => false
    t.integer "category_id",                                :null => false
    t.integer "inner_priority",              :default => 0, :null => false
  end

  add_index "tds_post_category", ["category_id"], :name => "foreign_category_id_idx", :limit => {"category_id"=>nil}
  add_index "tds_post_category", ["post_id"], :name => "foreign_post_id_idx", :limit => {"post_id"=>nil}

  create_table "tds_post_class", :force => true do |t|
    t.integer "post_id",  :null => false
    t.integer "class_id", :null => false
  end

  create_table "tds_post_comments", :force => true do |t|
    t.integer   "post_id",                        :null => false
    t.integer   "user_id",                        :null => false
    t.string    "title"
    t.string    "details",                        :null => false
    t.boolean   "show_comment", :default => true, :null => false
    t.integer   "parent_id"
    t.timestamp "created_date",                   :null => false
  end

  create_table "tds_post_country", :force => true do |t|
    t.integer "post_id",    :null => false
    t.integer "country_id", :null => false
  end

  create_table "tds_post_gallery", :force => true do |t|
    t.integer "type",           :limit => 1,   :default => 1, :null => false
    t.integer "post_id",        :limit => 8
    t.integer "material_id"
    t.text    "caption"
    t.string  "source",         :limit => 100
    t.integer "category_id",                   :default => 0
    t.integer "subcategory_id",                :default => 0
  end

  add_index "tds_post_gallery", ["material_id"], :name => "foreign_material_id_idx", :limit => {"material_id"=>nil}
  add_index "tds_post_gallery", ["post_id"], :name => "foreign_post_id_idx", :limit => {"post_id"=>nil}

  create_table "tds_post_keyword", :force => true do |t|
    t.integer "post_id",    :null => false
    t.integer "keyword_id", :null => false
  end

  create_table "tds_post_share", :force => true do |t|
    t.integer   "school_id",    :null => false
    t.integer   "post_id",      :null => false
    t.integer   "user_id",      :null => false
    t.timestamp "created_date", :null => false
  end

  create_table "tds_post_statistic", :force => true do |t|
    t.integer "news_id",                                               :null => false
    t.string  "ip_address",                                            :null => false
    t.integer "home_or_abroad", :limit => 1, :default => 1,            :null => false
    t.string  "country",                     :default => "Bangladesh", :null => false
    t.string  "city",                        :default => "Dhaka",      :null => false
    t.date    "date",                                                  :null => false
  end

  create_table "tds_post_tags", :force => true do |t|
    t.integer "post_id", :limit => 8, :null => false
    t.integer "tag_id",               :null => false
  end

  add_index "tds_post_tags", ["post_id"], :name => "foreign_post_idx", :limit => {"post_id"=>nil}
  add_index "tds_post_tags", ["tag_id"], :name => "foreign_tags_idx", :limit => {"tag_id"=>nil}

  create_table "tds_post_type", :force => true do |t|
    t.integer "post_id", :null => false
    t.integer "type_id", :null => false
  end

  create_table "tds_post_user_activity", :force => true do |t|
    t.integer   "user_id",                       :null => false
    t.integer   "post_id",        :limit => 8,   :null => false
    t.integer   "operation_type", :limit => 1
    t.timestamp "operation_date"
    t.string    "ip_address",     :limit => 20
    t.text      "user_agent"
    t.string    "os",             :limit => 100
    t.string    "latitude"
    t.string    "longitude"
    t.string    "session_id",     :limit => 45
  end

  add_index "tds_post_user_activity", ["post_id"], :name => "foreign_post_idx", :limit => {"post_id"=>nil}
  add_index "tds_post_user_activity", ["user_id"], :name => "foreign_user_idx", :limit => {"user_id"=>nil}

  create_table "tds_priority_log", :force => true do |t|
    t.text    "priority",        :null => false
    t.integer "current_post_id", :null => false
    t.string  "operation",       :null => false
  end

  create_table "tds_questions", :force => true do |t|
    t.text      "ques",    :null => false
    t.timestamp "created", :null => false
  end

  create_table "tds_quotes", :force => true do |t|
    t.text      "quote",                                      :null => false
    t.integer   "personality_id",                             :null => false
    t.timestamp "published_date"
    t.integer   "is_active",      :limit => 1, :default => 1, :null => false
    t.timestamp "create_date"
    t.timestamp "update_date",                                :null => false
  end

  add_index "tds_quotes", ["personality_id"], :name => "personality_id", :limit => {"personality_id"=>nil}

  create_table "tds_related_news", :force => true do |t|
    t.integer "post_id",        :limit => 8
    t.string  "new_link"
    t.string  "title",          :limit => 150
    t.string  "image"
    t.string  "published_date", :limit => 20,                    :null => false
    t.boolean "related_type",                  :default => true, :null => false
  end

  add_index "tds_related_news", ["post_id"], :name => "foreign_post_idx", :limit => {"post_id"=>nil}

  create_table "tds_school", :force => true do |t|
    t.integer "girls",                       :default => 0, :null => false
    t.integer "boys",                        :default => 0, :null => false
    t.string  "name",                                       :null => false
    t.integer "is_paid",        :limit => 1, :default => 0, :null => false
    t.integer "paid_school_id"
    t.string  "code",           :limit => 8
    t.string  "location",                                   :null => false
    t.string  "district",                                   :null => false
    t.integer "gender",         :limit => 1, :default => 0, :null => false
    t.string  "medium"
    t.string  "level"
    t.string  "shift"
    t.string  "logo"
    t.string  "cover"
    t.integer "views",                       :default => 0, :null => false
    t.integer "status",         :limit => 1, :default => 1, :null => false
    t.integer "user_type",      :limit => 1, :default => 1, :null => false
    t.string  "picture"
  end

  create_table "tds_school_activities", :force => true do |t|
    t.integer  "school_id",      :null => false
    t.string   "title",          :null => false
    t.text     "content",        :null => false
    t.datetime "date",           :null => false
    t.text     "mobile_content"
  end

  create_table "tds_school_activities_gallery", :force => true do |t|
    t.integer "activities_id", :limit => 8
    t.integer "material_id"
    t.text    "caption"
    t.string  "source",        :limit => 100
  end

  add_index "tds_school_activities_gallery", ["activities_id"], :name => "foreign_page_id_idx", :limit => {"activities_id"=>nil}
  add_index "tds_school_activities_gallery", ["material_id"], :name => "foreign_material_id_idx", :limit => {"material_id"=>nil}

  create_table "tds_school_menu", :force => true do |t|
    t.string "title", :null => false
  end

  create_table "tds_school_page", :force => true do |t|
    t.integer   "school_id",      :null => false
    t.integer   "menu_id",        :null => false
    t.string    "title",          :null => false
    t.text      "content"
    t.timestamp "date",           :null => false
    t.text      "mobile_content"
  end

  create_table "tds_school_page_gallery", :force => true do |t|
    t.integer "page_id",     :limit => 8
    t.integer "material_id"
    t.text    "caption"
    t.string  "source",      :limit => 100
  end

  add_index "tds_school_page_gallery", ["material_id"], :name => "foreign_material_id_idx", :limit => {"material_id"=>nil}
  add_index "tds_school_page_gallery", ["page_id"], :name => "foreign_page_id_idx", :limit => {"page_id"=>nil}

  create_table "tds_selected_post", :force => true do |t|
    t.integer "category_id", :default => 0, :null => false
    t.integer "post_id",                    :null => false
    t.integer "position",                   :null => false
  end

  create_table "tds_sessions", :primary_key => "session_id", :force => true do |t|
    t.string  "ip_address",    :limit => 45,  :default => "0", :null => false
    t.string  "user_agent",    :limit => 120,                  :null => false
    t.integer "last_activity",                :default => 0,   :null => false
    t.text    "user_data",                                     :null => false
  end

  add_index "tds_sessions", ["last_activity"], :name => "last_activity_idx", :limit => {"last_activity"=>nil}

  create_table "tds_settings", :force => true do |t|
    t.string  "key",         :limit => 100,                :null => false
    t.string  "value",       :limit => 45,                 :null => false
    t.integer "is_active",   :limit => 1,   :default => 1, :null => false
    t.string  "description", :limit => 275
  end

  add_index "tds_settings", ["key"], :name => "key_UNIQUE", :unique => true, :limit => {"key"=>nil}

# Could not dump table "tds_spellingbee" because of following StandardError
#   Unknown type 'year(4)' for column 'year'

  create_table "tds_tags", :force => true do |t|
    t.string  "tags_name", :limit => 200,                :null => false
    t.integer "hit_count", :limit => 8,   :default => 0
  end

  add_index "tds_tags", ["tags_name"], :name => "tags_name_UNIQUE", :unique => true, :limit => {"tags_name"=>nil}

  create_table "tds_teacher_follower", :force => true do |t|
    t.integer "user_id",                    :null => false
    t.integer "teacher_id",                 :null => false
    t.integer "is_approved", :default => 1, :null => false
  end

  create_table "tds_twitter_settings", :force => true do |t|
    t.string  "key",             :limit => 100,                :null => false
    t.string  "consumer_key",    :limit => 50,                 :null => false
    t.string  "consumer_secret", :limit => 50,                 :null => false
    t.integer "status",          :limit => 1,   :default => 1, :null => false
  end

  create_table "tds_user_auth", :force => true do |t|
    t.integer  "user_id",                       :null => false
    t.string   "auth_id",         :limit => 32, :null => false
    t.string   "activation_code", :limit => 32
    t.datetime "expire",                        :null => false
  end

  create_table "tds_user_created_school", :force => true do |t|
    t.integer   "freeuser_id", :null => false
    t.string    "school_name", :null => false
    t.string    "contact",     :null => false
    t.text      "address",     :null => false
    t.string    "zip_code"
    t.text      "about",       :null => false
    t.string    "logo"
    t.string    "picture"
    t.timestamp "created",     :null => false
  end

  create_table "tds_user_folder", :force => true do |t|
    t.string  "title",                     :null => false
    t.integer "user_id",                   :null => false
    t.boolean "status",  :default => true, :null => false
    t.boolean "visible", :default => true, :null => false
  end

  create_table "tds_user_gk_answers", :force => true do |t|
    t.integer "user_id",                           :null => false
    t.string  "question",                          :null => false
    t.string  "user_answer",                       :null => false
    t.string  "correct_answer",                    :null => false
    t.date    "date",                              :null => false
    t.boolean "is_correct",     :default => false, :null => false
    t.integer "post_id",                           :null => false
  end

  create_table "tds_user_good_read", :force => true do |t|
    t.integer "folder_id",                   :null => false
    t.integer "post_id",                     :null => false
    t.boolean "is_read",   :default => true, :null => false
    t.integer "user_id",                     :null => false
  end

  create_table "tds_user_notification", :force => true do |t|
    t.integer "user_id",                        :null => false
    t.string  "title",                          :null => false
    t.text    "description",                    :null => false
    t.integer "post_id",                        :null => false
    t.boolean "is_read",     :default => false, :null => false
  end

  create_table "tds_user_post_wow", :force => true do |t|
    t.integer "user_id", :null => false
    t.integer "post_id", :null => false
  end

  create_table "tds_user_school", :force => true do |t|
    t.integer "user_id",                      :null => false
    t.integer "school_id",                    :null => false
    t.date    "approved_date"
    t.string  "approved_by"
    t.date    "deny_date"
    t.integer "is_approved",   :default => 0, :null => false
    t.text    "grade"
    t.integer "type",          :default => 1, :null => false
    t.integer "ratting",       :default => 1
    t.text    "information"
    t.string  "deny_by"
  end

  create_table "tds_user_teacher", :id => false, :force => true do |t|
    t.integer "id",                           :null => false
    t.integer "user_id",                      :null => false
    t.date    "approved_date"
    t.string  "approved_by"
    t.date    "deny_date"
    t.integer "is_approved",   :default => 0, :null => false
    t.text    "grade"
    t.text    "subjects"
    t.integer "type",          :default => 1, :null => false
    t.integer "ratting",       :default => 1
    t.text    "information"
    t.string  "deny_by"
  end

  create_table "tds_users", :force => true do |t|
    t.string    "username", :limit => 15,  :null => false
    t.string    "password",                :null => false
    t.string    "email",    :limit => 100
    t.string    "name",     :limit => 200
    t.string    "salt",                    :null => false
    t.timestamp "created",                 :null => false
    t.timestamp "updated",                 :null => false
  end

  add_index "tds_users", ["username"], :name => "username_UNIQUE", :unique => true, :limit => {"username"=>nil}

  create_table "tds_voice_box", :force => true do |t|
    t.integer  "personality_id",                  :null => false
    t.string   "voice"
    t.datetime "published_date"
    t.string   "is_active",      :default => "1", :null => false
  end

  create_table "tds_votes", :force => true do |t|
    t.integer  "option_id",               :null => false
    t.datetime "voted_on",                :null => false
    t.string   "ip",        :limit => 16
  end

  create_table "tds_whats_on", :force => true do |t|
    t.integer "channel_id",                                  :null => false
    t.integer "program_type",    :limit => 1, :default => 1, :null => false
    t.text    "program_details",                             :null => false
    t.integer "category_id",                                 :null => false
    t.date    "show_date",                                   :null => false
    t.integer "is_active",       :limit => 1, :default => 1, :null => false
  end

  add_index "tds_whats_on", ["category_id"], :name => "fk_category_id", :limit => {"category_id"=>nil}
  add_index "tds_whats_on", ["channel_id"], :name => "fk_channel_id", :limit => {"channel_id"=>nil}

  create_table "tds_widget", :force => true do |t|
    t.string  "widget_name",                                         :null => false
    t.string  "widget_key",        :limit => 100,                    :null => false
    t.string  "widget_type",       :limit => 0,   :default => "Tab", :null => false
    t.integer "category_id",                      :default => 0,     :null => false
    t.boolean "is_enabled",                       :default => true,  :null => false
    t.boolean "has_ad_before",                    :default => true,  :null => false
    t.boolean "has_ad_after",                     :default => true,  :null => false
    t.integer "ad_plan_id_top",                   :default => 0,     :null => false
    t.integer "ad_plan_id_bottom",                :default => 0,     :null => false
    t.integer "news_qty",                         :default => 0,     :null => false
  end

  create_table "time_table_class_timings", :force => true do |t|
    t.integer  "batch_id"
    t.integer  "timetable_id"
    t.integer  "class_timing_set_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "time_table_class_timings", ["school_id"], :name => "index_time_table_class_timings_on_school_id", :limit => {"school_id"=>nil}

  create_table "time_table_weekdays", :force => true do |t|
    t.integer  "batch_id"
    t.integer  "timetable_id"
    t.integer  "weekday_set_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "time_table_weekdays", ["school_id"], :name => "index_time_table_weekdays_on_school_id", :limit => {"school_id"=>nil}

  create_table "time_zones", :force => true do |t|
    t.string   "name"
    t.string   "code"
    t.string   "difference_type"
    t.integer  "time_difference"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "timetable_entries", :force => true do |t|
    t.integer  "batch_id"
    t.integer  "weekday_id"
    t.integer  "class_timing_id"
    t.integer  "subject_id"
    t.integer  "employee_id"
    t.integer  "timetable_id"
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "timetable_entries", ["school_id"], :name => "index_timetable_entries_on_school_id", :limit => {"school_id"=>nil}
  add_index "timetable_entries", ["timetable_id"], :name => "index_timetable_entries_on_timetable_id", :limit => {"timetable_id"=>nil}

  create_table "timetable_swaps", :force => true do |t|
    t.date     "date"
    t.integer  "timetable_entry_id"
    t.integer  "employee_id"
    t.integer  "subject_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "timetable_swaps", ["school_id"], :name => "index_timetable_swaps_on_school_id", :limit => {"school_id"=>nil}

  create_table "timetables", :force => true do |t|
    t.date     "start_date"
    t.date     "end_date"
    t.boolean  "is_active",  :default => false
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "timetables", ["end_date"], :name => "index_timetables_on_end_date", :limit => {"end_date"=>nil}
  add_index "timetables", ["school_id"], :name => "index_timetables_on_school_id", :limit => {"school_id"=>nil}
  add_index "timetables", ["start_date", "end_date"], :name => "by_start_and_end", :limit => {"start_date"=>nil, "end_date"=>nil}
  add_index "timetables", ["start_date"], :name => "index_timetables_on_start_date", :limit => {"start_date"=>nil}

  create_table "transport_fee_collections", :force => true do |t|
    t.string   "name"
    t.integer  "batch_id"
    t.date     "start_date"
    t.date     "end_date"
    t.date     "due_date"
    t.boolean  "is_deleted", :default => false, :null => false
    t.integer  "school_id"
    t.datetime "updated_at"
    t.datetime "created_at"
  end

  add_index "transport_fee_collections", ["batch_id"], :name => "index_transport_fee_collections_on_batch_id", :limit => {"batch_id"=>nil}
  add_index "transport_fee_collections", ["school_id"], :name => "index_transport_fee_collections_on_school_id", :limit => {"school_id"=>nil}

  create_table "transport_fees", :force => true do |t|
    t.integer  "receiver_id"
    t.decimal  "bus_fare",                    :precision => 8, :scale => 4
    t.integer  "transaction_id"
    t.integer  "transport_fee_collection_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "receiver_type"
    t.integer  "school_id"
  end

  add_index "transport_fees", ["receiver_id", "transaction_id"], :name => "indices_on_transactions", :limit => {"receiver_id"=>nil, "transaction_id"=>nil}
  add_index "transport_fees", ["school_id"], :name => "index_transport_fees_on_school_id", :limit => {"school_id"=>nil}
  add_index "transport_fees", ["transport_fee_collection_id"], :name => "transport_fee_collection_id", :limit => {"transport_fee_collection_id"=>nil}

  create_table "transports", :force => true do |t|
    t.integer  "receiver_id"
    t.integer  "vehicle_id"
    t.integer  "route_id"
    t.string   "bus_fare"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "receiver_type"
    t.integer  "school_id"
  end

  add_index "transports", ["school_id"], :name => "index_transports_on_school_id", :limit => {"school_id"=>nil}

  create_table "user_events", :force => true do |t|
    t.integer  "event_id"
    t.integer  "user_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "user_events", ["school_id"], :name => "index_user_events_on_school_id", :limit => {"school_id"=>nil}

  create_table "user_exam_acknowledge", :force => true do |t|
    t.integer "exam_id",                          :null => false
    t.integer "acknowledge_by",    :default => 0, :null => false
    t.integer "acknowledge_by_id",                :null => false
    t.integer "school_id",                        :null => false
  end

  create_table "user_menu_links", :force => true do |t|
    t.integer  "user_id"
    t.integer  "menu_link_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "user_menu_links", ["school_id"], :name => "index_user_menu_links_on_school_id", :limit => {"school_id"=>nil}

  create_table "user_palettes", :force => true do |t|
    t.integer  "user_id"
    t.integer  "palette_id"
    t.integer  "position"
    t.boolean  "is_minimized",  :default => false
    t.integer  "school_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "column_number"
  end

  create_table "users", :force => true do |t|
    t.string   "username"
    t.string   "first_name"
    t.string   "last_name"
    t.string   "email"
    t.boolean  "admin"
    t.boolean  "student"
    t.boolean  "employee"
    t.string   "hashed_password"
    t.string   "salt"
    t.string   "reset_password_code"
    t.datetime "reset_password_code_until"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.boolean  "parent"
    t.boolean  "is_first_login"
    t.boolean  "is_deleted",                :default => false
    t.string   "google_refresh_token"
    t.string   "google_access_token"
    t.string   "google_expired_at"
    t.integer  "school_id"
    t.string   "ud_id"
    t.string   "api_token"
    t.boolean  "is_visible",                :default => true,  :null => false
  end

  add_index "users", ["google_access_token"], :name => "index_users_on_google_access_token", :limit => {"google_access_token"=>nil}
  add_index "users", ["school_id"], :name => "index_users_on_school_id", :limit => {"school_id"=>nil}
  add_index "users", ["username"], :name => "index_users_on_username", :limit => {"username"=>"10"}

  create_table "vehicles", :force => true do |t|
    t.string   "vehicle_no"
    t.integer  "main_route_id"
    t.integer  "no_of_seats"
    t.string   "status"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "vehicles", ["school_id"], :name => "index_vehicles_on_school_id", :limit => {"school_id"=>nil}

  create_table "votes", :force => true do |t|
    t.boolean  "vote",          :default => false
    t.integer  "voteable_id",                      :null => false
    t.string   "voteable_type",                    :null => false
    t.integer  "voter_id"
    t.string   "voter_type"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  add_index "votes", ["voteable_id", "voteable_type"], :name => "fk_voteables", :limit => {"voteable_id"=>nil, "voteable_type"=>nil}
  add_index "votes", ["voter_id", "voter_type"], :name => "fk_voters", :limit => {"voter_type"=>nil, "voter_id"=>nil}

  create_table "wardens", :force => true do |t|
    t.integer  "hostel_id"
    t.integer  "employee_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "wardens", ["school_id"], :name => "index_wardens_on_school_id", :limit => {"school_id"=>nil}

  create_table "weekday_sets", :force => true do |t|
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "weekday_sets", ["school_id"], :name => "index_weekday_sets_on_school_id", :limit => {"school_id"=>nil}

  create_table "weekday_sets_weekdays", :force => true do |t|
    t.integer  "weekday_id"
    t.integer  "weekday_set_id"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "school_id"
  end

  add_index "weekday_sets_weekdays", ["school_id"], :name => "index_weekday_sets_weekdays_on_school_id", :limit => {"school_id"=>nil}

  create_table "weekdays", :force => true do |t|
    t.integer  "batch_id"
    t.string   "weekday"
    t.string   "name"
    t.integer  "sort_order"
    t.integer  "day_of_week"
    t.boolean  "is_deleted",  :default => false
    t.datetime "updated_at"
    t.datetime "created_at"
    t.integer  "school_id"
  end

  add_index "weekdays", ["batch_id"], :name => "index_weekdays_on_batch_id", :limit => {"batch_id"=>nil}
  add_index "weekdays", ["school_id"], :name => "index_weekdays_on_school_id", :limit => {"school_id"=>nil}

end
