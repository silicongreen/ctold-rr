class CreateClassworks < ActiveRecord::Migration
  def self.up
    create_table :Classworks do |t|
      t.references :employee
      t.references :subject
      t.string :student_list
      t.string :title
      t.text :content
      t.string :attachment_file_name
      t.string :attachment_content_type
      t.integer :attachment_file_size
      t.datetime :attachment_updated_at
      t.timestamps
    end
  end

  def self.down
    drop_table :Classworks
  end
end
