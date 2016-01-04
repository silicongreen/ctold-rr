class AddNewColumnsForAcl < ActiveRecord::Migration
  def self.up
    add_column :palettes,  :menu_id, :integer, :default => 0
    add_column :palettes,  :menu_type, :string, :default => "general"
    
    add_column :privileges,  :menu_id, :integer, :default => 0
    add_column :privileges,  :menu_type, :string, :default => "general"
    
    add_column :menu_links,  :reference_id, :integer, :default => 0
    
    add_column :employees,  :is_visible, :integer, :default => 1
    
    add_column :oauth_clients,  :is_visible, :integer, :default => 1
    
    add_column :users,  :is_visible, :integer, :default => 1
    
    add_column :students,  :student_activation_code, :string, :default => null
    
    add_column :students,  :class_teacher_id, :integer, :default => 0
    
    add_column :events,  :event_category_id, :integer, :default => null
    
    add_column :events,  :fees, :integer, :default => 0.00
    
    add_column :events,  :icon_number, :integer, :default => null
    
    add_column :events,  :is_club, :integer, :default => 0
    
    add_column :subjects,  :icon_number, :integer, :default => null
    
    add_column :reminders,  :type, :integer, :default => 0
    
    add_column :reminders,  :rid, :integer, :default => 0
  end

  def self.down
    remove_column :palettes,  :menu_id
    remove_column :palettes,  :menu_type
    
    remove_column :privileges,  :menu_id
    remove_column :privileges,  :menu_type
    
    remove_column :menu_links,  :reference_id
  end



end
