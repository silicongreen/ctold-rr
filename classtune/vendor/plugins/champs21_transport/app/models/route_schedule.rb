class RouteSchedule < ActiveRecord::Base
  belongs_to :route, :class_name => "Route"
  validates_presence_of :route_id, :weekday_id, :home_pickup_time, :school_pickup_time
end
