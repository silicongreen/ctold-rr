class PackageController <  MultiSchoolController
  helper_method :admin_user_session
  helper_method :school_group_session


  filter_access_to [:index,:new_package,:edit_package,:delete_package,:create_package,:package_modules, :edit_package_modules, :edit_package_data]

  CONN = ActiveRecord::Base.connection
  
  def initialize
    @settings_plugin = YAML.load_file(File.dirname(__FILE__)+"/../../config/plugins.yml")
    @required_plugins = @settings_plugin["required_plugins"]
    @not_required_plugins = @settings_plugin["not_required_plugins"]
  end
  
  def index   
    @packages = Package.active.paginate(:order=>"id ASC",:page => params[:page], :per_page=>10)

    respond_to do |format|
      format.html # index.html.erb
      format.xml  { render :xml => @packages }
    end
  end
  
  def package_modules
    @school_group = admin_user_session.school_group
    @school = @school_group.schools.build
    @school.school_domains.build
    @school.build_available_plugin(:plugins=>[])
    
    @menu_links_main = MenuLink.find(:all, :conditions => ['link_type = ? AND `higher_link_id` IS NULL ', 'user_menu'])
    @menu_links_child_group_by = MenuLink.find(:all, :select => 'higher_link_id',:group => 'higher_link_id', :conditions => ['link_type = ? AND `higher_link_id` NOT IN (SELECT id FROM  `menu_links` WHERE  `higher_link_id` IS NULL AND  `link_type` LIKE  ?) ', 'user_menu', 'user_menu'])
    @menu_links_child_group_by_arr = @menu_links_child_group_by.map {|i| i.higher_link_id }
    @menu_links_child_all = MenuLink.find(:all, :conditions => ['link_type = ? AND `higher_link_id` NOT IN (SELECT id FROM  `menu_links` WHERE  `higher_link_id` IS NULL AND  `link_type` LIKE  ?) ', 'user_menu', 'user_menu'])
    
    @top_menu_for_child = MenuLink.find(:all, :conditions => ["id IN (?)",@menu_links_child_group_by_arr ])
    
    @menu_links_childs = []
    i = 0
    while i < @menu_links_child_group_by_arr.length
      @menu_links_childs << {'name' => @top_menu_for_child.select{|l| l.id == @menu_links_child_group_by_arr[i]}.map{|k| k.name}.join(''), 'permission_menus' => @menu_links_child_all.select{|o| o.higher_link_id == @menu_links_child_group_by_arr[i]}}
      i += 1
    end
    
    @package_modules_data = Package.find_by_id(params[:id])
    
    @package = Package.new(params[:package])
  end
  
  def new_package
    @school_group = admin_user_session.school_group
    @school = @school_group.schools.build
    @school.school_domains.build
    @school.build_available_plugin(:plugins=>[])
    
    @menu_links_main = MenuLink.find(:all, :conditions => ['link_type = ? AND `higher_link_id` IS NULL ', 'user_menu'])
    @menu_links_child_group_by = MenuLink.find(:all, :select => 'higher_link_id',:group => 'higher_link_id', :conditions => ['link_type = ? AND `higher_link_id` NOT IN (SELECT id FROM  `menu_links` WHERE  `higher_link_id` IS NULL AND  `link_type` LIKE  ?) ', 'user_menu', 'user_menu'])
    @menu_links_child_group_by_arr = @menu_links_child_group_by.map {|i| i.higher_link_id }
    @menu_links_child_all = MenuLink.find(:all, :conditions => ['link_type = ? AND `higher_link_id` NOT IN (SELECT id FROM  `menu_links` WHERE  `higher_link_id` IS NULL AND  `link_type` LIKE  ?) ', 'user_menu', 'user_menu'])
    
    @top_menu_for_child = MenuLink.find(:all, :conditions => ["id IN (?)",@menu_links_child_group_by_arr ])
    
    @menu_links_childs = []
    i = 0
    while i < @menu_links_child_group_by_arr.length
      @menu_links_childs << {'name' => @top_menu_for_child.select{|l| l.id == @menu_links_child_group_by_arr[i]}.map{|k| k.name}.join(''), 'permission_menus' => @menu_links_child_all.select{|o| o.higher_link_id == @menu_links_child_group_by_arr[i]}}
      i += 1
    end
    
    @package = Package.new(params[:package])
  end 
  
  def edit_package_modules
    package_id = params[:id]
    PackageMenu.delete_all(:package_id=>package_id)
    
    unless params[:school].nil?
      unless params[:school][:available_plugin_attributes].nil?
        unless params[:school][:available_plugin_attributes][:plugins].nil?
          params[:school][:available_plugin_attributes][:plugins].each do |plugin|
            packageMenu = PackageMenu.new()
            packageMenu.package_id = package_id
            packageMenu.menu_id = 0
            packageMenu.plugins_name = plugin
            packageMenu.is_active = 1
            packageMenu.save
          end
        end
      end
    end
    
    unless params[:package].nil?
      unless params[:package][:available_permission_menus].nil?
        unless params[:package][:available_permission_menus][:menu_link].nil?
          available_permission_menus = params[:package][:available_permission_menus][:menu_link]
          menu_links = MenuLink.find_all_by_name(available_permission_menus)
          menu_links.map{|m| m.id}.each do |permission_menus_id|
            packageMenu = PackageMenu.new()
            packageMenu.package_id = package_id
            packageMenu.menu_id = permission_menus_id
            packageMenu.plugins_name = ""
            packageMenu.is_active = 1
            packageMenu.save
          end
        end
      end
    end

    unless params[:package][:available_menu_link_main].nil?
      unless params[:package][:available_menu_link_main].nil?
        unless params[:package][:available_menu_link_main][:menu_link].nil?
          available_permission_menus = params[:package][:available_menu_link_main][:menu_link]
          menu_links = MenuLink.find_all_by_name(available_permission_menus)
          menu_links.map{|m| m.id}.each do |permission_menus_id|
            packageMenu = PackageMenu.new()
            packageMenu.package_id = package_id
            packageMenu.menu_id = permission_menus_id
            packageMenu.plugins_name = ""
            packageMenu.is_active = 1
            packageMenu.save
            menu_links_childs = MenuLink.find_all_by_higher_link_id(permission_menus_id)
            menu_links_childs.map{|m_child| m_child.id}.each do |menu_child_id|
              packageMenu = PackageMenu.new()
              packageMenu.package_id = package_id
              packageMenu.menu_id = menu_child_id
              packageMenu.plugins_name = ""
              packageMenu.is_active = 1
              packageMenu.save
            end
          end
        end
      end
    end
      
    flash[:notice] = "Successfully Saved"
    redirect_to :controller=>:package ,:action=>:index
  end 
  
  def create_package
    @package = Package.new()
    @package.name = params[:package][:name]
    @package.is_active = 1
    if @package.save
      package_id = @package.id
      unless params[:school].nil?
        unless params[:school][:available_plugin_attributes].nil?
          unless params[:school][:available_plugin_attributes][:plugins].nil?
            params[:school][:available_plugin_attributes][:plugins].each do |plugin|
              packageMenu = PackageMenu.new()
              packageMenu.package_id = package_id
              packageMenu.menu_id = 0
              packageMenu.plugins_name = plugin
              packageMenu.is_active = 1
              packageMenu.save
            end
          end
        end
      end

      unless params[:package].nil?
        unless params[:package][:available_permission_menus].nil?
          unless params[:package][:available_permission_menus][:menu_link].nil?
            available_permission_menus = params[:package][:available_permission_menus][:menu_link]
            menu_links = MenuLink.find_all_by_name(available_permission_menus)
            menu_links.map{|m| m.id}.each do |permission_menus_id|
              packageMenu = PackageMenu.new()
              packageMenu.package_id = package_id
              packageMenu.menu_id = permission_menus_id
              packageMenu.plugins_name = ""
              packageMenu.is_active = 1
              packageMenu.save
            end
          end
        end
      end

      unless params[:package].nil?
        unless params[:package][:available_menu_link_main].nil?
          unless params[:package][:available_menu_link_main][:menu_link].nil?
            available_permission_menus = params[:package][:available_menu_link_main][:menu_link]
            menu_links = MenuLink.find_all_by_name(available_permission_menus)
            menu_links.map{|m| m.id}.each do |permission_menus_id|
              packageMenu = PackageMenu.new()
              packageMenu.package_id = package_id
              packageMenu.menu_id = permission_menus_id
              packageMenu.plugins_name = ""
              packageMenu.is_active = 1
              packageMenu.save
              menu_links_childs = MenuLink.find_all_by_higher_link_id(permission_menus_id)
              menu_links_childs.map{|m_child| m_child.id}.each do |menu_child_id|
                packageMenu = PackageMenu.new()
                packageMenu.package_id = package_id
                packageMenu.menu_id = menu_child_id
                packageMenu.plugins_name = ""
                packageMenu.is_active = 1
                packageMenu.save
              end
            end
          end
        end
      end
      
      flash[:notice] = "Successfully Saved"
      redirect_to :controller=>:package ,:action=>:index
    else
      str_notices = "<br />"
      @package.errors.each do |error|
        str_notices = str_notices + error[1].to_s + "<br />"
      end
      flash[:notice] = "Some error occurs while saving packages " + str_notices
      redirect_to :controller=>:package ,:action=>:new_package
    end
    
  end 
  
  def delete_package
    package_id = params[:id]
    @package = Package.find(package_id)
    
    PackageMenu.delete_all(:package_id=>package_id)
    
    @package.delete
    flash[:notice] = "Successfully Deleted"
    redirect_to :controller=>:package ,:action=>:index
  end
  
  def edit_package
    @school_group = admin_user_session.school_group
    @school = @school_group.schools.build
    @school.school_domains.build
    @school.build_available_plugin(:plugins=>[])
    
    @menu_links_main = MenuLink.find(:all, :conditions => ['link_type = ? AND `higher_link_id` IS NULL ', 'user_menu'])
    @menu_links_child_group_by = MenuLink.find(:all, :select => 'higher_link_id',:group => 'higher_link_id', :conditions => ['link_type = ? AND `higher_link_id` NOT IN (SELECT id FROM  `menu_links` WHERE  `higher_link_id` IS NULL AND  `link_type` LIKE  ?) ', 'user_menu', 'user_menu'])
    @menu_links_child_group_by_arr = @menu_links_child_group_by.map {|i| i.higher_link_id }
    @menu_links_child_all = MenuLink.find(:all, :conditions => ['link_type = ? AND `higher_link_id` NOT IN (SELECT id FROM  `menu_links` WHERE  `higher_link_id` IS NULL AND  `link_type` LIKE  ?) ', 'user_menu', 'user_menu'])
    
    @top_menu_for_child = MenuLink.find(:all, :conditions => ["id IN (?)",@menu_links_child_group_by_arr ])
    
    @menu_links_childs = []
    i = 0
    while i < @menu_links_child_group_by_arr.length
      @menu_links_childs << {'name' => @top_menu_for_child.select{|l| l.id == @menu_links_child_group_by_arr[i]}.map{|k| k.name}.join(''), 'permission_menus' => @menu_links_child_all.select{|o| o.higher_link_id == @menu_links_child_group_by_arr[i]}}
      i += 1
    end
    
    @package_modules_data = Package.find_by_id(params[:id])
    
    @package = Package.new(params[:package])
  end 
  
  def edit_package_data
    package_id = params[:id]
    @package = Package.find(package_id)
    @package.update_attributes(:name => params[:package][:name])
    
    PackageMenu.delete_all(:package_id=>package_id)
    
    unless params[:school].nil?
      unless params[:school][:available_plugin_attributes].nil?
        unless params[:school][:available_plugin_attributes][:plugins].nil?
          params[:school][:available_plugin_attributes][:plugins].each do |plugin|
            packageMenu = PackageMenu.new()
            packageMenu.package_id = package_id
            packageMenu.menu_id = 0
            packageMenu.plugins_name = plugin
            packageMenu.is_active = 1
            packageMenu.save
          end
        end
      end
    end
    
    unless params[:package].nil?
      unless params[:package][:available_permission_menus].nil?
        unless params[:package][:available_permission_menus][:menu_link].nil?
          available_permission_menus = params[:package][:available_permission_menus][:menu_link]
          menu_links = MenuLink.find_all_by_name(available_permission_menus)
          menu_links.map{|m| m.id}.each do |permission_menus_id|
            packageMenu = PackageMenu.new()
            packageMenu.package_id = package_id
            packageMenu.menu_id = permission_menus_id
            packageMenu.plugins_name = ""
            packageMenu.is_active = 1
            packageMenu.save
          end
        end
      end
    end

    unless params[:package].nil?
      unless params[:package][:available_menu_link_main].nil?
        unless params[:package][:available_menu_link_main][:menu_link].nil?
          available_permission_menus = params[:package][:available_menu_link_main][:menu_link]
          menu_links = MenuLink.find_all_by_name(available_permission_menus)
          menu_links.map{|m| m.id}.each do |permission_menus_id|
            packageMenu = PackageMenu.new()
            packageMenu.package_id = package_id
            packageMenu.menu_id = permission_menus_id
            packageMenu.plugins_name = ""
            packageMenu.is_active = 1
            packageMenu.save
            menu_links_childs = MenuLink.find_all_by_higher_link_id(permission_menus_id)
            menu_links_childs.map{|m_child| m_child.id}.each do |menu_child_id|
              packageMenu = PackageMenu.new()
              packageMenu.package_id = package_id
              packageMenu.menu_id = menu_child_id
              packageMenu.plugins_name = ""
              packageMenu.is_active = 1
              packageMenu.save
            end
          end
        end
      end
    end

    flash[:notice] = "Successfully Updated"
    redirect_to :controller=>:package ,:action=>:index
  end 
  
end
