class Vehicle < ActiveRecord::Base
  belongs_to :main_route, :class_name => "Route"
  has_many :transports
  validates_presence_of :vehicle_no, :main_route, :no_of_seats
  validates_uniqueness_of :vehicle_no
  validates_format_of :vehicle_no, :with => /^[A-Za-z0-9 -]+$/
  validates_numericality_of :no_of_seats
  validates_format_of :status, :with => /^[A-Za-z]+$/, :allow_blank => true
  before_destroy :check_dependencies_for_destroy
  before_update :check_dependencies_for_save

  def check_dependencies_for_destroy
    if Transport.exists? :vehicle_id=>(self.id)
      errors.add_to_base :travellers_exist_cannot_delete_vehicle
      return false
    end
  end

  def check_dependencies_for_save
    find = Vehicle.find_by_id(self.id)
    if Transport.exists? :vehicle_id=>(self.id) and find.main_route_id != self.main_route_id
      errors.add_to_base :travellers_exist_in_the_main_route
      self.main_route_id = find.main_route_id
      return false
    end
  end

  def available_seats
    no_of_seats.to_i - transports.count.to_i
  end
end
