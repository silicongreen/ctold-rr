class FeedsController <  MultiSchoolController
  helper_method :admin_user_session
  helper_method :school_group_session


  filter_access_to [:index,:new_category,:edit_category,:delete_category,:arrange_category,:save_priorities]

  CONN = ActiveRecord::Base.connection
  # GET /schools
  # GET /schools.xml
  def index   
    @feeds_category = FeedCategory.active.paginate(:order=>"position ASC",:page => params[:page], :per_page=>10)

    respond_to do |format|
      format.html # index.html.erb
      format.xml  { render :xml => @feeds_category }
    end
  end
  
  def new_category
    @feed_category = FeedCategory.new(params[:feed_category])
    @last_feed = FeedCategory.active.find(:first,:order=>"position DESC")
    if request.post?
      @feed_category.logo = params[:feed_category][:logo] if params[:feed_category][:logo].present?
      if @last_feed
        @feed_category.position = @last_feed.position.next
      end
      if @feed_category.save
        flash[:notice] = "Successfully Saved"
        redirect_to :controller=>:feeds ,:action=>:index
      end
    end
  end 
  def delete_category
    @feed_category = FeedCategory.find(params[:id])
    @feed_category.delete
    flash[:notice] = "Successfully Deleted"
    redirect_to :controller=>:feeds ,:action=>:index
  end
  
  def edit_category
    @feed_category = FeedCategory.find(params[:id])
    if request.put?
       @feed_category.logo = params[:feed_category][:logo] if params[:feed_category][:logo].present?
       if @feed_category.update_attributes(params[:feed_category])
        flash[:notice] = "Successfully Updated"
        redirect_to :controller=>:feeds ,:action=>:index
       end 
    end
  end 
  
  def arrange_category
    @feeds_category = FeedCategory.active.find(:all,:order=>"position ASC")
  end
  
  def save_priorities
    category_string = params[:category_ids]
    @category_array = category_string.split(",")
    @i = 1
    @category_array.each do |id|
      @feed_category = FeedCategory.find(id)
      @feed_category.update_attributes(:position=>@i)
      @i = @i.next
    end
    
    render :text => "Done"
  end

  
end
