class ChangeStudentListType < ActiveRecord::Migration
  def self.up
    change_column :Classworks,  :student_list, :text
  end

  def self.down
  end
end
