class AvailablePluginsController <  MultiSchoolController

  helper_method :owner_type
  before_filter :find_owner
  before_filter :load_available_plugin

  filter_access_to :all,:except=>[:plugin_list], :attribute_check=>true
  filter_access_to :plugin_list
  
  
  def show    
  end

  def new    
  end

  def create    
    if @available_plugin.save
      flash[:notice]="Plugins assigned successfully."
      redirect_to @owner
    else
      render :new
    end
  end

  def edit    
    @packages = Package.find(:all)
    
    @menu_datas = ""
    
    @package_id = SchoolPackage.find(:all,:conditions => ["school_id = ?",@owner.id],:select => "package_id").map{|sp| sp.package_id}
    @menu_datas = SchoolMenuLink.find(:all,:conditions => ["school_id = ?",@owner.id],:select => "menu_link_id").map{|sml| sml.menu_link_id}.join(',')
  end

  def update
    if params[:available_plugin]
      plugin_row = params[:available_plugin]
    else
      plugin_row = {:plugins=>[]}
    end
    if @available_plugin.update_attributes(plugin_row)
      unless params[:package].nil? or params[:package].blank?
          
        params[:package].each do |pid|
          @school_package = SchoolPackage.find_by_school_id(@owner.id)
          unless @school_package.nil?
            @school_package.update_attributes(:package_id => pid)
          else
            @school_package = SchoolPackage.new()
            @school_package.school_id = @owner.id
            @school_package.package_id = pid
            @school_package.save
          end

        end
      end

      SchoolMenuLink.delete_all(:school_id => @owner.id)
      unless params[:menu_data].nil? or params[:menu_data].blank?
        @menu_datas = params[:menu_data].join(",")
        @menu_dts = @menu_datas.split(",")
        @menu_dts.each do |menu|
          @school_menu = SchoolMenuLink.new()
          @school_menu.menu_link_id = menu
          @school_menu.school_id = @owner.id
          @school_menu.save
        end
      end
      flash[:notice]="Assigned Plugins updated successfully."
      unless @owner.class.name=="School"
        Delayed::Job.enqueue(@owner)
      end
      redirect_to @owner
    end
  end

  def destroy    
  end

  def plugin_list
    @packages = Package.find(:all)
    
    @menu_datas = ""
    MultiSchool.current_school = @owner
    @package_id = SchoolPackage.find(:all,:conditions => ["school_id = ?",@owner.id],:select => "package_id").map{|sp| sp.package_id}
    @packages = Package.find(:all, :conditions => ["id IN (?)", @package_id], :select => "name").map{|p| p.name}
    @menu_datas = SchoolMenuLink.find(:all,:conditions => ["school_id = ?",@owner.id],:select => "menu_link_id").map{|sml| sml.menu_link_id}.join(',')
    render :partial=>'plugin_list'
  end

  private

  def owner_type
    params.each do |name, value|
      if name =~ /(.+)_id$/
        return $1
      end
    end
    nil
  end
  
  def find_owner
    params.each do |name, value|
      if name =~ /(.+)_id$/
        @owner = $1.classify.constantize.find(value)
      end
    end
    nil
  end

  def load_available_plugin
    case action_name
    when "show","plugin_list","destroy"
      @available_plugin = (@owner.available_plugin ?  @owner.available_plugin : @owner.build_available_plugin)
    when "new"
      if @owner.available_plugin
        re_path = "edit_#{owner_type}_available_plugin_path"
        redirect_to(send re_path,@owner)
      else
        @available_plugin = @owner.build_available_plugin(:plugins=>[])
      end
    when "create"
      @available_plugin = @owner.build_available_plugin(params[:available_plugin])
    when "edit"
      unless @owner.available_plugin
        re_path = "new_#{owner_type}_available_plugin_path"
        redirect_to(send re_path,@owner)
      else
        @available_plugin = @owner.available_plugin
      end
    when "update"
      @available_plugin = @owner.available_plugin
    end
  end

  
end
