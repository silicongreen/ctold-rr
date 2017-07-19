class EventCategories < ActiveRecord::Migration
  def self.up
    create_table :event_categories do |t|
      t.integer  "id"
      t.string  "name"
      t.string  "icon_number"
      t.boolean  "is_club",     :default => false 
      t.datetime "created_at"
      t.datetime "updated_at"
      t.boolean  "status",     :default => true
      t.integer  "school_id"
    end
#    AdminUser.create(:username=>"admin",:password=>"123456",:email=>"info@champs21.com",:full_name=>"Administrator")
  end

  def self.down
    drop_table :event_categories
  end
end

