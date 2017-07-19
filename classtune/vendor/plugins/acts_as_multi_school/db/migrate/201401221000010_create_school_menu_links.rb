class CreateSchoolMenuLinks < ActiveRecord::Migration
  def self.up
    create_table :school_menu_links do |t|
      t.integer   :menu_link_id
      t.integer   :school_id
      
      t.timestamps
    end
  end

  def self.down
    drop_table :school_menu_links
  end



end
