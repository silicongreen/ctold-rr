class DataExportPaperclipPathUpdate < ActiveRecord::Migration
  def self.up
     Rake::Task["champs21_data_export:update_plugins_paths"].execute
  end

  def self.down
  end
end
