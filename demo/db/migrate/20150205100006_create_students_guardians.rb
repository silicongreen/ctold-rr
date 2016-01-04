class StudentsGuardians < ActiveRecord::Migration
  def self.up
    create_table :students_guardians do |t|
      t.string    :admission_no
      t.string    :s_username
      t.string    :s_password
      t.string    :s_first_name
      t.string    :s_middle_name
      t.string    :s_last_name
      t.integer   :student_id , :default=>0
      t.string    :g_first_name      
      t.string    :g_last_name
      t.string    :g_username
      t.string    :g_password
      t.integer   :guardian_id , :default=>0
      t.string    :g_phone
      t.integer   :school_id
      
      t.timestamps
    end
  end

  def self.down
    drop_table :students_guardians
  end



end
