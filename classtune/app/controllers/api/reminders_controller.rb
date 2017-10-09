class Api::RemindersController < ApiController
  filter_access_to :all

  def index
    @xml = Builder::XmlMarkup.new
    @reminders = Reminder.search(params[:search]).scoped(:conditions => ["DATE(reminders.created_at)<='#{params[:created_at]}'"],:limit => 15)
    respond_to do |format|
      unless (params[:search].present? and params[:search][:to_user_username_equals].present? and params[:created_at].present?)
        render "single_access_tokens/500.xml", :status => :bad_request  and return
      else
        format.xml  { render :reminders }
      end
    end
  end
  
  def collections
    @xml = Builder::XmlMarkup.new
    user = User.active.find_by_username(params[:id])
    
    @reminders = Reminder.find_all_by_recipient(user.id, :conditions=>"is_read = false and is_deleted_by_recipient = false",:limit=>10,:order=>"created_at DESC")
    
    respond_to do |format|
      format.xml  { render :reminders_collections }
    end
  end

  def count
    @xml = Builder::XmlMarkup.new
    user = User.active.find_by_username(params[:id])
    
    reminders = Reminder.find(:all , :conditions => ["recipient = '#{user.id}'"])
    count = 0
    reminders.each do |r|
      unless r.is_read
        count += 1
      end
    end
    @reminder_count = count
    respond_to do |format|
      format.xml  { render :reminder_count }
    end
  end
  
  def create
    @xml = Builder::XmlMarkup.new
    @reminder = Reminder.new
    @reminder.user = User.find_by_username(params[:sender])
    @reminder.to_user = User.find_by_username(params[:receiver])
    @reminder.subject = params[:subject]
    @reminder.body = params[:body]
    respond_to do |format|
      if @reminder.save
        format.xml  { render :reminder, :status => :created }
      else
        format.xml  { render :xml => @reminder.errors, :status => :unprocessable_entity }
      end
    end
  end
end
