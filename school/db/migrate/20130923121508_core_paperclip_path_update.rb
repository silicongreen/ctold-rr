class CorePaperclipPathUpdate < ActiveRecord::Migration
  def self.up
    Rake::Task["champs21:data:update_paths"].execute
  end

  def self.down
  end
end
