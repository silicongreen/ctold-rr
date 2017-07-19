class CreatePackageMenus < ActiveRecord::Migration
  def self.up
    create_table :package_menus do |t|
      t.integer   :package_id
      t.integer   :menu_id
      t.string    :plugins_name
      t.boolean  :is_active, :default=>true
      
      t.timestamps
    end
  end

  def self.down
    drop_table :package_menus
  end



end
