class Api::UsersController < ApiController

  def show
    @xml = Builder::XmlMarkup.new
    if params[:id]=="loginhook"
      username = params[:username]
      password = params[:password]
      @user = User.active.find_by_username username
      if @user.present? and User.authenticate?(username, password)
        authenticated_user = @user
        successful_user_login(authenticated_user)
        @privileges = @user.privileges.all.map(&:description)
      end
      respond_to do |format|
        if authenticated_user.present?
          format.xml  { render :user }
        else
          render "single_access_tokens/500.xml", :status => :bad_request  and return
        end
      end
    elsif  params[:id]=="sessionhook"
      @sessionvalue = request.session_options[:id]
      respond_to do |format|
        format.xml  { render :session }
      end
    else
      @user = User.active.find_by_username(params[:id])
      @privileges = @user.privileges.all.map(&:description)

      respond_to do |format|
        unless @user.nil?
          format.xml  { render :user }
        else
          render "single_access_tokens/500.xml", :status => :bad_request  and return
        end
      end
    end
    
  end
  
  private
    def successful_user_login(user)
      cookies.delete("_champs21_session")
      session[:user_id] = user.id
    end
  
  
  
end
