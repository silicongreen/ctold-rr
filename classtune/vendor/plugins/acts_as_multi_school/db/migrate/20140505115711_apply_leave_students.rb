class ApplyLeaveStudents < ActiveRecord::Migration
  def self.up
    create_table :apply_leave_students do |t|
      t.integer  "id"
      t.integer  "student_id"
      t.date "start_date"
      t.date "end_date"
      t.text  "reason"
      t.boolean  "approved",     :default => false 
      t.boolean  "viewed_by_teacher",     :default => false
      t.string  "teacher_remark"
      t.integer  "approving_teacher"
      t.datetime "created_at"
      t.datetime "updated_at"
      t.integer  "school_id"
    end
#    AdminUser.create(:username=>"admin",:password=>"123456",:email=>"info@champs21.com",:full_name=>"Administrator")
  end

  def self.down
    drop_table :apply_leave_students
  end
end

