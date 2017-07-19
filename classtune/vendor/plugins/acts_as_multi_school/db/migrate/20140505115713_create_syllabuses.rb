class CreateSyllabuses < ActiveRecord::Migration
  def self.up
    create_table :syllabuses do |t|
        t.integer  "id"
        t.string   "title"
        t.integer  "batch_id"	
        t.integer  "exam_group_id"	
        t.boolean  "is_yearly", :default => false
        t.integer  "subject_id"	
        t.text     "content"
        t.integer  "author_id"	
        t.datetime "created_at"
        t.datetime "updated_at"
        t.integer  "school_id"
    end
#    AdminUser.create(:username=>"admin",:password=>"123456",:email=>"info@champs21.com",:full_name=>"Administrator")
  end

  def self.down
    drop_table :syllabuses
  end
end

