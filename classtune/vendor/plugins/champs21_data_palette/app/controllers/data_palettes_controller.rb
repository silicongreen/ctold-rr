class DataPalettesController < ApplicationController
  before_filter :login_required
  filter_access_to :all

  def index
#    unless current_user.admin? 
      redirect_to :controller => "dashboards"
#    else
#      @user_palettes = current_user.own_palettes
#      @cur_date = Date.today
#    end
    #@events = get_school_feed_champs21    
  end

  def update_palette
    current_palette = Palette.find_by_name(params[:palette][:palette_name])
    @cur_date = params[:palette][:cur_date].to_date
    render :partial=>"palette_subcontent", :locals=>{:palette=>current_palette, :cur_date=>@cur_date, :off=>0, :lim=>4}
  end

  def toggle_minimize
    palette_id = params[:palette][:id].to_i
    user_palette = UserPalette.find_by_user_id_and_palette_id(current_user.id,palette_id)
    if user_palette.is_minimized == false
      user_palette.is_minimized = true
    else
      user_palette.is_minimized = false
    end
    user_palette.save
    render :text=>"#{palette_id}"
  end

  def remove_palette
    palette_id = params[:palette][:id].to_i
    user_palette = UserPalette.find_by_user_id_and_palette_id(current_user.id,palette_id)
    user_palette.destroy
    render :text=>"removed"
  end

  def refresh_palette
    palette = Palette.find(params[:palette][:id].to_i)
    @cur_date = Date.today
    render :partial=>"palette_content", :locals=>{:palette=>palette, :cur_date=>@cur_date, :off=>0, :lim=>4}
  end

  def show_palette_list
    @user = current_user
    
    #@data_palettes = Palette.compatible_palettes(Palette.find(:all),[:employee])
    @data_palettes = Palette.allowed_palettes.sort_by{|p| p.name}
    
    @available_palettes = []
    data_pallettes_plugins = {"polls" => "champs21_poll", "placements" => "champs21_placement", "book_return_due" => "champs21_library", "photos_added" => "champs21_gallery", "discussions" => "champs21_discussion", "blogs" => "champs21_blog", "online_meetings" => "champs21_bigbluebutton", "homework" => "champs21_assignment"}
    @data_palettes.each do |dp|
      if data_pallettes_plugins[dp.name].nil?
        menu_id = dp.menu_id
        if menu_id > 0
          if dp.menu_type == 'general'

            menu_links = MenuLink.find_by_id(menu_id)
            if menu_links.link_type == 'user_menu'
              school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
              unless school_menu_links.blank?
                @available_palettes << dp
              end
            elsif menu_links.link_type != 'not_active_menu'
              @available_palettes << dp
            end
          else
            if @user.student
              if dp.menu_type == 'user_menu_student'
                menu_links = MenuLink.find_by_id(menu_id)
                if menu_links.link_type == 'user_menu'
                  school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
                  unless school_menu_links.blank?
                    @available_palettes << dp
                  end
                elsif menu_links.link_type != 'not_active_menu'
                  @available_palettes << dp
                end
              end
            elsif @user.employee
              if dp.menu_type == 'user_menu_teacher'
                menu_links = MenuLink.find_by_id(menu_id)
                if menu_links.link_type == 'user_menu'
                  school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
                  unless school_menu_links.blank?
                    @available_palettes << dp
                  end
                elsif menu_links.link_type != 'not_active_menu'
                  @available_palettes << dp
                end
              end
            end
          end
        else
          @available_palettes << dp
        end
      else
        plugins_name = data_pallettes_plugins[dp.name]
        plugins_data = AvailablePlugin.find_by_associated_id(MultiSchool.current_school.id)
        unless plugins_data.nil?
          plugins = plugins_data.plugins
          if plugins.include?(plugins_name)
            @available_palettes << dp
          end
        end   
      end
    end
    #    @available_palettes = @data_palettes
  
    render :partial=>"palette_list", :locals=>{:available_palettes=>@available_palettes}
  end

  def modify_user_palettes
    selected_palettes = []
    if params[:palette] and params[:palette][:selected_palettes]
      selected_palettes = params[:palette][:selected_palettes]
    end
    prev_palettes = current_user.palettes.map{|p| p.id.to_s}
    removed_palettes = prev_palettes - selected_palettes
    added_palettes = selected_palettes - prev_palettes
    unless removed_palettes.empty?
      UserPalette.find_all_by_user_id_and_palette_id(current_user.id,removed_palettes).map{|u| u.destroy}
    end
    unless added_palettes.empty?
      added_palettes.each do|palette|
        UserPalette.create(:user_id=>current_user.id,:palette_id=>palette.to_i)
      end
    end
    #redirect_to :controller=>"data_palettes", :action=>"index"
    render :partial=>"palettes_main", :locals=>{:user_palettes=>current_user.own_palettes, :cur_date=>Date.today}
  end

  def sort_palettes
    palette = Palette.find(params[:palette][:id].to_i)
    current_palette = UserPalette.find_by_user_id_and_palette_id(current_user.id,palette.id)
    previous_column = current_palette.column_number
    previous_position = current_palette.position
    new_column = params[:palette][:column_number].to_i
    new_position = params[:palette][:position].to_i
    if previous_column == new_column
      if previous_position > new_position
        intermediate_palettes = UserPalette.find_all_by_user_id_and_column_number_and_position(current_user.id,new_column,(new_position..(previous_position-1)).to_a)
        intermediate_palettes.each do|palette|
          palette.update_attributes(:position=>(palette.position+1))
        end
      else
        intermediate_palettes = UserPalette.find_all_by_user_id_and_column_number_and_position(current_user.id,new_column,((previous_position+1)..new_position).to_a)
        intermediate_palettes.each do|palette|
          palette.update_attributes(:position=>(palette.position-1))
        end
      end
    else
      old_column_palettes = UserPalette.find(:all, :conditions=>["user_id = ? AND column_number = ? AND position > ?",current_user.id,previous_column,previous_position])
      new_column_palettes = UserPalette.find(:all, :conditions=>["user_id = ? AND column_number = ? AND position >= ?",current_user.id,new_column,new_position])
      old_column_palettes.each do|palette|
        palette.update_attributes(:position=>(palette.position-1))
      end
      new_column_palettes.each do|palette|
        palette.update_attributes(:position=>(palette.position+1))
      end
    end
    current_palette.update_attributes(:column_number=>new_column, :position=>new_position)
    render :text=>""
  end

  def view_more
    current_palette = Palette.find_by_name(params[:palette][:palette_name])
    offset = params[:palette][:offset].to_i
    cur_date = params[:palette][:cur_date].to_date
    render :partial=>"palette_subcontent", :locals=>{:palette=>current_palette, :cur_date=>cur_date, :off=>offset, :lim=>4}
  end
  
  def get_school_feed_champs21
    require 'net/http'
    require 'uri'
    require "yaml"
    
    @user = current_user
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    username = champs21_api_config['username']
    password = champs21_api_config['password']
    
    if File.file?("#{RAILS_ROOT.to_s}/public/user_configs/feed_" + @user.id.to_s + "_config.yml")
      user_info = YAML.load_file("#{RAILS_ROOT.to_s}/public/user_configs/feed_" + @user.id.to_s + "_config.yml")

      
      if Time.now.to_i >= user_info[0]['api_info'][0]['user_cookie_exp'].to_i
        
        uri = URI(api_endpoint + "api/user/auth")
        http = Net::HTTP.new(uri.host, uri.port)
        auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
        auth_req.set_form_data({"username" => username, "password" => password})
        auth_res = http.request(auth_req)
        auth_response = ActiveSupport::JSON.decode(auth_res.body)

        ar_user_cookie = auth_res.response['set-cookie'].split('; ');
        
        user_info = [
          "api_info" => [
            "user_secret" => auth_response['data']['paid_user']['secret'],
            "user_cookie" => ar_user_cookie[0],
            "user_cookie_exp" => ar_user_cookie[2].split('=')[1].to_time.to_i
          ]
        ]        

        File.open("#{RAILS_ROOT.to_s}/public/user_configs/feed_" + @user.id.to_s + "_config.yml", 'w') {|f| f.write(YAML.dump(user_info)) }

      end
    else
      
      uri = URI(api_endpoint + "api/user/auth")
      http = Net::HTTP.new(uri.host, uri.port)
      auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
      auth_req.set_form_data({"username" => username, "password" => password})
      auth_res = http.request(auth_req)
      @auth_response = ActiveSupport::JSON.decode(auth_res.body)

      ar_user_cookie = auth_res.response['set-cookie'].split('; ');
      
      user_info = [
        "api_info" => [
          "user_secret" => @auth_response['data']['paid_user']['secret'],
          "user_cookie" => ar_user_cookie[0],
          "user_cookie_exp" => ar_user_cookie[2].split('=')[1].to_time.to_i
        ]
      ]
      File.open("#{RAILS_ROOT.to_s}/public/user_configs/feed_" + @user.id.to_s + "_config.yml", 'w') {|f| f.write(YAML.dump(user_info)) }

    end
    
    event_uri = URI(api_endpoint + "api/freeuser")
    http = Net::HTTP.new(event_uri.host, event_uri.port)
    event_req = Net::HTTP::Post.new(event_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => user_info[0]['api_info'][0]['user_cookie'] })
    event_req.set_form_data({"call_from_web"=>1,"user_secret" => user_info[0]['api_info'][0]['user_secret']})
    
    event_res = http.request(event_req)
    event_response = JSON::parse(event_res.body)
    
    if event_response['status']['code'].to_i == 200
      events = event_response['data']['post']
    end
    
    return events
  end
end
