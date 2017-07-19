class CreateGalleryPhotos < ActiveRecord::Migration
  def self.up
    create_table :gallery_photos do |t|
      t.references :gallery_category
      t.string :description

      t.timestamps
    end
  end

  def self.down
    drop_table :gallery_photos
  end
end
