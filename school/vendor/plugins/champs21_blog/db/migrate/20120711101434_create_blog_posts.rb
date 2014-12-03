class CreateBlogPosts < ActiveRecord::Migration
  def self.up
    create_table :blog_posts do |t|
      t.string :title
      t.text :body
      t.boolean :is_active
      t.boolean :is_published
      t.boolean :is_deleted
      t.references :blog

      t.timestamps
    end
  end

  def self.down
    drop_table :blog_posts
  end
end
