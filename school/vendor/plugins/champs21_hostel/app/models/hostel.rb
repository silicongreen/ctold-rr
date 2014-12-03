class Hostel < ActiveRecord::Base
  before_destroy :check_allocation
  has_many :wardens,:dependent=>:destroy
  has_many :room_details,:dependent=>:destroy
  validates_presence_of :name, :hostel_type


  def check_allocation
    self.room_details.each do |r|
      vacant = RoomAllocation.find_all_by_room_detail_id(r.id, :conditions=>["is_vacated is false"])
      unless vacant.size == 0
        errors.add_to_base :cant_delete_hostel_allocated
        return false
      end
    end
  end


end
