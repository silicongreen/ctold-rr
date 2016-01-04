class CreateSyllabus < ActiveRecord::Migration
  def self.up
    create_table :syllabuses do |t|
      t.string     :title
      t.text       :content
      t.references :author
	    t.integer    :is_yearly
      t.integer    :subject_id
      t.integer    :exam_group_id
      t.integer    :batch_id
      t.string     :related_syllabus_id
      t.integer    :school_id
      t.timestamps
    end
  end

  def self.down    
    drop_table :syllabuses
  end
end
