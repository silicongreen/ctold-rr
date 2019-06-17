class AttendanceVehicle < ActiveRecord::Base 
  belongs_to :student
  belongs_to :vechicle
end
