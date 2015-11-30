class CreateMeetingRequests < ActiveRecord::Migration
  def self.up
    create_table :meeting_requests do |t|
      t.integer :meeting_type
      t.integer :teacher_id
      t.integer :parent_id
      t.text :description
      t.text :student_ids
      t.text :reciver_messege
      t.timestamps :datetime
      t.integer :status

      t.timestamps
    end
  end

  def self.down
    drop_table :meeting_requests
  end
end
