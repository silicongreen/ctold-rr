
class RoomDetail < ActiveRecord::Base
  cattr_reader :per_page
  @@per_page = 20

  belongs_to :hostel
  has_many   :room_allocation,:dependent=>:destroy

  validates_presence_of :hostel_id, :room_number, :students_per_room, :rent
  validates_uniqueness_of :room_number, :scope => :hostel_id
  validates_numericality_of :students_per_room,:less_than_or_equal_to=>10
  before_save :verify_precision

  def verify_precision
    self.rent = Champs21Precision.set_and_modify_precision self.rent
  end

  def get_room_allocation
    return RoomAllocation.find_all_by_room_detail_id(self.id, :conditions=>["is_vacated is false"])
  end

  def self.room_list(parameters)
    sort_order=parameters[:sort_order]
    type=parameters[:type]
    hostel_id=parameters[:hostel_id]
    if type.nil?
      if sort_order.nil?
        rooms=RoomDetail.all(:select=>"hostel_id,rent,room_details.id,room_number,students_per_room,(students_per_room-count(IF(is_vacated=0,1,NULL))) as available" ,:joins=>"LEFT OUTER JOIN `room_allocations` ON room_allocations.room_detail_id = room_details.id",:group=>'room_number',:conditions=>{:hostel_id=>hostel_id},:order=>'room_number ASC')
      else
        rooms=RoomDetail.all(:select=>"hostel_id,rent,room_details.id,room_number,students_per_room,(students_per_room-count(IF(is_vacated=0,1,NULL))) as available" ,:joins=>"LEFT OUTER JOIN `room_allocations` ON room_allocations.room_detail_id = room_details.id",:group=>'room_number',:conditions=>{:hostel_id=>hostel_id},:order=>sort_order)
      end
    else
      if type=='available'
        if sort_order.nil?
          rooms=RoomDetail.all(:select=>"hostel_id,rent,room_details.id,room_number,students_per_room,(students_per_room-count(IF(is_vacated=0,1,NULL))) as available" ,:joins=>"LEFT OUTER JOIN `room_allocations` ON room_allocations.room_detail_id = room_details.id",:group=>'room_number',:conditions=>{:hostel_id=>hostel_id},:having=>'available > 0',:order=>'room_number ASC')
        else
          rooms=RoomDetail.all(:select=>"hostel_id,rent,room_details.id,room_number,students_per_room,(students_per_room-count(IF(is_vacated=0,1,NULL))) as available" ,:joins=>"LEFT OUTER JOIN `room_allocations` ON room_allocations.room_detail_id = room_details.id",:group=>'room_number',:conditions=>{:hostel_id=>hostel_id},:having=>'available > 0',:order=>sort_order)
        end
      else
        if sort_order.nil?
          rooms=RoomDetail.all(:select=>"hostel_id,rent,room_details.id,room_number,students_per_room,(students_per_room-count(IF(is_vacated=0,1,NULL))) as available" ,:joins=>"LEFT OUTER JOIN `room_allocations` ON room_allocations.room_detail_id = room_details.id",:group=>'room_number',:conditions=>{:hostel_id=>hostel_id},:having=>'available = 0',:order=>'room_number ASC')
        else
          rooms=RoomDetail.all(:select=>"hostel_id,rent,room_details.id,room_number,students_per_room,(students_per_room-count(IF(is_vacated=0,1,NULL))) as available" ,:joins=>"LEFT OUTER JOIN `room_allocations` ON room_allocations.room_detail_id = room_details.id",:group=>'room_number',:conditions=>{:hostel_id=>hostel_id},:having=>'available = 0',:order=>sort_order)
        end
      end
    end
    data=[]
    col_heads=["#{t('no_text')}","#{t('room_number')}","#{t('students_per_room') }","#{t('availability') }","#{t('rent')}"]
    data << col_heads
    rooms.each_with_index do |s,i|
      col=[]
      col<< "#{i+1}"
      col<< "#{s.room_number}"
      col<< "#{s.students_per_room}"
      col<< "#{s.available}"
      col<< "#{s.rent}"
      col=col.flatten
      data<< col
    end
    return data
  end
  
end
