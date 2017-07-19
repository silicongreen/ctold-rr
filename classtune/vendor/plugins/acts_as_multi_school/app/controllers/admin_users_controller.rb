class AdminUsersController < MultiSchoolController
  helper_method :admin_user_session
  helper_method :school_group_session
  before_filter :require_admin_session,:except=>[:login,:forgot_password,:reset_password,:set_new_password]
  before_filter :load_admin_user,:except=>[:index, :login, :logout, :forgot_password,:reset_password,:set_new_password]
  filter_access_to :show,:profile,:update,:edit,:change_password,:destroy, :attribute_check=>true
  filter_access_to :new, :create
  
  def create_defaults
    @default = Default.new
    
    unless params[:key].nil? and params[:value].nil?
      
      defaults = Default.find(:all, :conditions => ["`key` = ? AND `value` = ? AND `status` =?", params[:key], params[:value], 1])
      
      if defaults.empty? or defaults.nil?
        @default.key = params[:key]
        @default.value = params[:value]
        @default.display_key = @default.get_default_keys[params[:key]]
        if @default.save
          flash[:notice] = "New key successfully created."
        else
          flash[:notice] = "Something is wrong."
        end
      else
        flash[:notice] = "Default value already exists."
      end
    end
  end
  
  def edit_default
    unless params[:id].nil?
      @default = Default.find(:last, :conditions => ["`id` = ? AND `status` =?", params[:id], 1])
    else
      flash[:notice] = "Bad Request."
      redirect_to :action => 'defaults_list'
    end
  end
  
  def update_default
    
    @default = Default.find(:last, :conditions => ["`id` = ? AND `status` =?", params[:id], 1])
    
    if @default.nil?
      flash[:notice] = "Default value not exists."
    else
        
      other_defaults = Default.find(:all, :conditions => ["`key` = ? AND `value` = ? AND `status` = ? AND `id` <> ?", params[:key], params[:value], 1, params[:id]])
      
      if other_defaults.empty? or other_defaults.nil?
        @default.key = params[:key]
        @default.value = params[:value]
        @default.display_key = @default.get_default_keys()[params[:key]]
        if @default.save
          flash[:notice] = "Key successfully updated."
        else
          flash[:notice] = "Something is wrong."
        end
      else
        flash[:notice] = "Default value already exists."
      end
    end
    redirect_to :action => 'edit_default', :id => @default.id
  end
  
  def delete_default
    
    if params[:id].nil?
      flash[:notice] = "Bad request."
    else
      @default = Default.find(:last, :conditions => ["`id` = ? AND `status` =?", params[:id], 1])
    
      if @default.nil?
        flash[:notice] = "Default value not exists."
      else
        @default.status = 0
        if @default.save
          flash[:notice] = "Key successfully deleted."
        else
          flash[:notice] = "Something is wrong."
        end
      end
    end
    redirect_to :action => 'defaults_list'
  end
  
  def defaults_list_data
    @defaults = Default.paginate(:conditions => ["`status` =? AND `key` =?", 1, params[:id]], :order=>"id DESC", :page => params[:page], :per_page=>10)
    render :partial=> "defaults_list_data", :locals => {:key => params[:id]}
  end
  
  def defaults_list
    @mod_default = Default.new
    @defaults = Default.paginate(:conditions => ["`status` =? AND `key` =?", 1, 'employee_category'], :order=>"id DESC", :page => params[:page], :per_page=>10)
  end
  
  def index
    @school_group = SchoolGroup.find(params[:multi_school_group_id])
    @admin_users = @school_group.multi_school_admins.paginate(:page => params[:page], :per_page=>10)
  end

  # GET /admin_users/1
  # GET /admin_users/1.xml
  def show
    respond_to do |format|
      format.html # show.html.erb
      format.xml  { render :xml => @admin_user }
    end
  end

  def profile
    render :partial=>"profile"
  end

  def change_password
    if request.put?      
      if @admin_user.change_password(params[:admin_user])
        render :update do |page|
          page.replace_html 'content_div', "<label> Password changed successfully. <label>"
        end
      else
        render :update do |page|
          page.replace_html 'content_div', :partial => 'change_password'
        end
      end
      return
    end
    render :partial=>"change_password"
  end
  
  # GET /admin_users/new
  # GET /admin_users/new.xml
  def new    
  end

  # GET /admin_users/1/edit
  def edit
  end

  # POST /admin_users
  # POST /admin_users.xml
  def create

    respond_to do |format|
      if @admin_user.save
        group = admin_user_session.school_group
        @admin_user.school_group = group
        flash[:notice] = 'User was successfully created.'
        format.html { redirect_to multi_school_group_admin_users_path(@school_group) }
      else
        format.html { render :action => "new" }
        format.xml  { render :xml => @admin_user.errors, :status => :unprocessable_entity }
      end
    end
  end

  # PUT /admin_users/1
  # PUT /admin_users/1.xml
  def update

    respond_to do |format|
      if @admin_user.update_attributes(params[:admin_user])
        flash[:notice] = 'User was successfully updated.'
        format.html { redirect_to admin_user_path(@admin_user) }
        format.xml  { head :ok }
      else
        format.html { render :action => "edit" }
        format.xml  { render :xml => @admin_user.errors, :status => :unprocessable_entity }
      end
    end
  end

  # DELETE /admin_users/1
  # DELETE /admin_users/1.xml
  def destroy
    if @admin_user.destroy
      flash[:notice] = 'User deleted successfully.'
      respond_to do |format|
        format.html { redirect_to(user_dashboard) }
        format.xml  { head :ok }
      end
    else
    end
  end


  def login
    if admin_user_session
      redirect_to user_dashboard and return
    end
    if request.post?
      if @admin_user = AdminUser.active.authenticate?(params[:user][:username],params[:user][:password])
        cookies.delete("_champs21_session")
        session[:admin_user] = @admin_user.id
        redirect_to user_dashboard and return
      else
        flash[:notice] = "Invalid Username/Password"
      end
    end
  end

  def logout
    session[:admin_user]  =nil
    redirect_to login_admin_users_url
  end

  def forgot_password
    #@network_state = Configuration.find_by_config_key("NetworkState")
    if request.post? and params[:reset_password]
      if admin_user = AdminUser.find_by_username_and_is_deleted(params[:reset_password][:username],false)
        unless admin_user.email.blank?
          admin_user.reset_password_code = Digest::SHA1.hexdigest( "#{admin_user.email}#{Time.now.to_s.split(//).sort_by {rand}.join}" )
          admin_user.reset_password_code_until = 1.day.from_now
          #admin_user.role = admin_user.role_name
          admin_user.save(false)
          url = "#{request.protocol}#{request.host_with_port}"
          AdminUserNotifier.deliver_forgot_password(admin_user,url)
          flash[:notice] = "Reset Password link has been send to your email"
          redirect_to login_admin_users_url
        else
          flash[:notice] = "User does not have an email id!"
          return
        end
      else
        flash[:notice] = "No user exists with username #{params[:reset_password][:username]}"
      end
    end
  end

  def reset_password
    admin_user = AdminUser.find_by_reset_password_code_and_is_deleted(params[:id],false,:conditions=>"reset_password_code IS NOT NULL")
    if admin_user
      if admin_user.reset_password_code_until > Time.now
        redirect_to :action => 'set_new_password', :id => admin_user.reset_password_code
      else
        flash[:notice] = "Reset time expired"
        redirect_to login_admin_users_url
      end
    else
      flash[:notice]= "Invalid reset link"
      redirect_to login_admin_users_url
    end
  end

  def set_new_password
    if request.post?
      admin_user = AdminUser.find_by_reset_password_code_and_is_deleted(params[:id],false,:conditions=>"reset_password_code IS NOT NULL")
      if admin_user
        unless params[:set_new_password][:new_password]==""
          if params[:set_new_password][:new_password] == params[:set_new_password][:confirm_password]
            admin_user.password = params[:set_new_password][:new_password]
            admin_user.update_attributes(:password => admin_user.password, :reset_password_code => nil, :reset_password_code_until => nil)
            #user.clear_menu_cache
            #User.update(user.id, :password => params[:set_new_password][:new_password],
            # :reset_password_code => nil, :reset_password_code_until => nil)
            flash[:notice] = "Password succesfully reset. Use new password to log in."
            redirect_to login_admin_users_url
          else
            flash[:notice] = "Password confirmation failed. Please enter password again."
            redirect_to :action => 'set_new_password', :id => admin_user.reset_password_code
          end
        else
          flash[:notice] = "Password cannot be blank."
          redirect_to :action => 'set_new_password', :id => admin_user.reset_password_code
        end
      else
        flash[:notice] = "You have followed an invalid link. Please try again."
        redirect_to login_admin_users_url
      end
    end
  end

  private

  def load_admin_user
    case action_name
    when "edit","update","destroy","change_password","profile","show"
      @admin_user = AdminUser.find(params[:id])
    when "new"
      @school_group = admin_user_session.school_group
      @admin_user = @school_group.multi_school_admins.build
    when "create_defaults"
      @school_group = admin_user_session.school_group
      @admin_user = @school_group.multi_school_admins.build
    when "create"
      @school_group = admin_user_session.school_group
      @admin_user = @school_group.multi_school_admins.build(params[:admin_user])
      @admin_user.higher_user = admin_user_session
    end
  end
  
end