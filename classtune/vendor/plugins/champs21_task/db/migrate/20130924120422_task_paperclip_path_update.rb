class TaskPaperclipPathUpdate < ActiveRecord::Migration
  def self.up
    Rake::Task["champs21_task:update_plugins_paths"].execute
  end

  def self.down
  end
end
