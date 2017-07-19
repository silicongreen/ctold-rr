class AddNewColumnsForAcl < ActiveRecord::Migration
  def self.up
    add_column :palettes,  :menu_id, :integer, :default => 0
    add_column :palettes,  :menu_type, :string, :default => "general"
    
    add_column :privileges,  :menu_id, :integer, :default => 0
    add_column :privileges,  :menu_type, :string, :default => "general"
    
    add_column :menu_links,  :reference_id, :integer, :default => 0
  end

  def self.down
    remove_column :palettes,  :menu_id
    remove_column :palettes,  :menu_type
    
    remove_column :privileges,  :menu_id
    remove_column :privileges,  :menu_type
    
    remove_column :menu_links,  :reference_id
  end



end
