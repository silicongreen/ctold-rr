class CreateRouteSchedules < ActiveRecord::Migration
  def self.up
    create_table :route_schedules do |t|
      t.integer   :route_id
      t.integer   :weekday_id
      t.time      :home_pickup_time
      t.time      :school_pickup_time
      t.integer   :school_id
      
      t.timestamps
    end
  end

  def self.down
    drop_table :route_schedules
  end



end
