class ClassworkPaperclipPathUpdate < ActiveRecord::Migration
  def self.up
    Rake::Task["champs21_Classwork:update_plugins_paths"].execute
  end

  def self.down
  end
end
