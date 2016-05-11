class CreateStudetsHistory < ActiveRecord::Migration
  def self.up
    create_table :exam_connects do |t|
      t.string  :student_id
      t.integer :previous_batch_id
      t.integer :school_id      
      t.timestamps
    end
  end

  def self.down
    drop_table :students_history
  end
end
