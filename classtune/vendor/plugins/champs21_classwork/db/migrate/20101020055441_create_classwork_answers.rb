class CreateClassworkAnswers < ActiveRecord::Migration
  def self.up
    create_table :Classwork_answers do |t|
      t.references :Classwork
      t.references :student
      t.string :status ,:default=>0
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
    drop_table :Classwork_answers
  end
end
