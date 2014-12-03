class CreateBlogs < ActiveRecord::Migration
  def self.up
    create_table :blogs do |t|
      t.string :name
      t.boolean :is_active
      t.boolean :is_published
      t.references :user

      t.timestamps
    end
  end

  def self.down
    drop_table :blogs
  end
end
