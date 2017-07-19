class Route < ActiveRecord::Base
  has_many :routes
  has_many :transports
  has_many :vehicles,:foreign_key=>'main_route_id',:conditions=>"status='Active'"
  validates_presence_of :destination, :cost
#  validates_numericality_of :cost,:only_integer =>true, :greater_than_or_equal_to =>0, :allow_nil => true
  before_update :check_for_depenencies

  before_save :verify_precision

  def verify_precision
    self.cost = Champs21Precision.set_and_modify_precision self.cost
  end


  def check_for_depenencies
    route = Route.find(self.id)
    if Transport.exists?(:route_id => self.id) and route.main_route_id != self.main_route_id
      errors.add_to_base :main_route_contains_travellers
      self.main_route_id = route.main_route_id
      return false
    end
  end

  def main_route
    if self.main_route_id.nil?
      return self
    else
      Route.find_by_id(self.main_route_id)
    end
  end

end
