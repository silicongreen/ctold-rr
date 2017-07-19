class RoomDetailsController < ApplicationController
  before_filter :login_required
  before_filter :check_permission,:only=>[:index]
  before_filter :set_precision
  filter_access_to :all
  def index
    @hostels = Hostel.all
  end

  def update_room_list
    #@room_details = RoomDetail.find_all_by_hostel_id params[:hostel_id]
    @room_details = RoomDetail.paginate(:conditions=>["hostel_id=?",params[:hostel_id]], :page=>params[:page])
    render :partial=>'room_list'
  end

  def new
    @room_detail = RoomDetail.new
    @hostel = Hostel.all
  end

  def create
    @room_detail = RoomDetail.new(params[:room_detail])
    @hostel = Hostel.all
    if params[:room][:count].to_i == 0
      if @room_detail.save
        @hostel = Hostel.find @room_detail.hostel_id
        flash[:notice]="#{t('room_has_been_created')}"
        redirect_to room_details_path
      else
        render 'new'
      end
    else
      count = params[:room][:count].to_i
      @params = params[:room_detail]
      room_number = params[:room_detail][:room_number]
      @params.delete('room_number')
      saved = 0
      if count.to_i <= 300
      count.times do |c|
        @params["room_number"] = room_number
        #room = RoomDetail.create(:hostel_id=>params[:room_detail][:hostel_id],:students_per_room=>params[:room_detail][:students_per_room], :rent=>params[:room_detail][:rent], :room_number=>room_number)
        @room_detail = RoomDetail.create(@params)
        unless @room_detail.id.nil?
          room_split = room_number.to_s.scan(/[A-Z]+|\d+/i)
          if room_split[1].blank?
            room_number = room_split[0].next
          else
            room_number = room_split[0]+room_split[1].next
          end
          saved += 1
          @room_detail = ''
        end
      end
      else
       @room_detail.errors.add_to_base("Maximum rooms should be less than 300")
      end
      if saved == count
        @hostel = Hostel.find params[:room_detail][:hostel_id]
        flash[:notice]="#{t('room_has_been_created')}"
        redirect_to room_details_path
      else
        render 'new'
      end
    end
  end

  def destroy
    @room_detail = RoomDetail.find(params[:id])
    hostel_id = @room_detail.hostel_id
    @vacant = RoomAllocation.find_all_by_room_detail_id(params[:id], :conditions=>["is_vacated is false"])
    if @vacant.size == 0
      @room_detail.destroy
      flash[:message2]=''
      flash[:message]="#{t('room_has_been_successfully_deleted')}"
    else
      flash[:message]=''
      flash[:message2]="#{t('unable_to_delete_the_room_when_allocated')}"
    end
    @room_details = RoomDetail.paginate(:conditions=>["hostel_id=#{hostel_id}"], :page=>params[:page])
    render :update do |page|
      page.replace_html 'room-list', :partial=>'room_list'
    end
  end

  def edit
    @room_details = RoomDetail.find(params[:id])
  end

  def update
    @room_details = RoomDetail.find(params[:id])
    if @room_details.update_attributes(params[:room_detail])
      flash[:notice]="#{t('room_details_successfully_updated')}"
      redirect_to @room_details
    else
      render :action => "edit"
    end
  end

  def show
    @room_details = RoomDetail.find params[:id]
    @students = @students = RoomAllocation.find_all_by_room_detail_id_and_is_vacated(params[:id],false).sort_by{|s| s.student.full_name.downcase unless s.student.nil?}
    @students.reject!{|x|x.student.nil?}
  end

end
