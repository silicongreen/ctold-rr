class CreateStudentActivationCodes < ActiveRecord::Migration
  def self.up
    create_table :student_activation_codes do |t|
      t.integer   :is_active
      t.string    :code
      t.integer   :student_id , :default=>0
      t.integer   :school_id
      
      t.timestamps
    end
  end

  def self.down
    drop_table :student_activation_codes
  end



end
