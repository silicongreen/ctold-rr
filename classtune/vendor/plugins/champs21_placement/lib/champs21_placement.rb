require 'dispatcher'
# Champs21Placement
module Champs21Placement
  def self.attach_overrides
    Dispatcher.to_prepare :champs21_placement do
      ::Student.instance_eval { has_many :placement_registrations, :dependent => :destroy }
      ::Student.instance_eval { has_many :placementevents ,:through=>:placement_registrations }
    end
  end
  
  def self.dependency_check(record,type)
    if type == "permanant"
      if record.class.to_s == "Student"
        return true if record.placement_registrations.present?
      end
    end
    return false
  end
end

#
