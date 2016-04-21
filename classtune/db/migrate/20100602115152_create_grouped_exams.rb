class CreateGroupedExams < ActiveRecord::Migration
  def self.up
    create_table :grouped_exams do |t|
      t.references :exam_group
      t.references :batch
      t.references :exam_connect
      t.integer :priority
    end
  end

  def self.down
    drop_table :grouped_exams
  end
end
