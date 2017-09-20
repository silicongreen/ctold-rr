class Api::UsersController < ApiController

  def show
    @xml = Builder::XmlMarkup.new
    if params[:id]=="loginhook"
        username = params[:username]
        password = params[:password]
        @user = User.active.find_by_username username
        if @user.present? and User.authenticate?(username, password)
          champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
          api_endpoint = champs21_config['api_url']
          api_from = champs21_config['from']

          uri = URI(api_endpoint + "api/user/auth")
          http = Net::HTTP.new(uri.host, uri.port)
          auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
          auth_req.set_form_data({"username" => username, "password" => password})
          auth_res = http.request(auth_req)
          auth_response = JSON::parse(auth_res.body)

          ar_user_cookie = auth_res.response['set-cookie'].split('; ');
          #abort ar_user_cookie.inspect
          if api_from == "local"
            user_info = [ 
              "user_secret" => auth_response['data']['paid_user']['secret'],
              "user_cookie" => ar_user_cookie[1].split(",")[1],
              "user_cookie_exp" => ar_user_cookie[3].split('=')[1].to_time.to_i
            ]
            else
            user_info = [ 
              "user_secret" => auth_response['data']['paid_user']['secret'],
              "user_cookie" => ar_user_cookie[1].split(",")[1],
              "user_cookie_exp" => ar_user_cookie[2].split('=')[1].to_time.to_i
            ]
          end
          session[:api_info] = user_info
          
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
      session[:user_id_main] = user.id
      session[:user_id] = user.id
    end
  
  
  
end
