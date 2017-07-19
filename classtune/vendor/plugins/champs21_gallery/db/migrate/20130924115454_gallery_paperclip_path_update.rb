class GalleryPaperclipPathUpdate < ActiveRecord::Migration
  def self.up
    Rake::Task["champs21_gallery:update_plugins_paths"].execute
  end

  def self.down
  end
end
