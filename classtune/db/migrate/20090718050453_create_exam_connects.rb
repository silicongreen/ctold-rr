class CreateExamConnects < ActiveRecord::Migration
  def self.up
    create_table :exam_connects do |t|
      t.string     :name
      t.integer :batch_id
      t.integer :school_id
      t.date :attandence_start_date
      t.date :attandence_end_date
      t.timestamps
    end
  end

  def self.down
    drop_table :exam_connects
  end
end
