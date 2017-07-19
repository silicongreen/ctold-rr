class RoutesController < ApplicationController
  filter_access_to :all
  before_filter :login_required
  before_filter :check_permission,:only=>[:index]
  before_filter  :set_precision
  
  def index
    @route = Route.all
  end

  def add_routes_schedules
    @route = Route.find(params[:id])
    @route_schedule = RouteSchedule.new
  end
  
  def create_routes_schedule
    @route = Route.find(params[:id])
    unless params[:route_schedule_week].nil? or params[:route_schedule_week][:weekday].nil? or params[:route_schedule_week][:weekday].empty? 
      weekday_id_list = params[:route_schedule_week][:weekday]
      weekday_id_list.each do |s|
          @previus_routes = RouteSchedule.find(:all,:conditions=>["route_id='"+@route.id.to_s+"' and weekday_id='"+s.to_s+"'"])
          unless @previus_routes.nil? || @previus_routes.empty? 
            @previus_routes.each do |previus_routes|
              previus_route_todestroy = RouteSchedule.find(previus_routes.id)
              previus_route_todestroy.destroy
            end  
          end
          @routes_schedule = RouteSchedule.new(params[:route_schedule])
          @routes_schedule.route_id= @route.id
          @routes_schedule.weekday_id= s
          @routes_schedule.school_id = MultiSchool.current_school.id
          @routes_schedule.save
          
      end
      flash[:notice] = "#{t('flash8')}"
      redirect_to :controller => "routes", :action => "route_schedule",:id=>@route.id
    else
      flash[:notice] = "#{t('flash9')}"
      redirect_to :controller => "routes", :action => "add_routes_schedules",:id=>@route.id
    end
  
  end
  
  def new
    @route = Route.new
    @main_routes = Route.all( :conditions=>["main_route_id is NULL"])
  end

  def create
    @route = Route.new(params[:route])
    
      
    @main_routes = Route.all( :conditions=>["main_route_id is NULL"])
    
    respond_to do |format|
      if @route.save
        flash[:notice] = "#{t('flash1')}"
        format.html { redirect_to(@route) }
        format.xml { render :xml => @route, :status => :created, :location => @route }
      else
        format.html { render :action => "new" }
        format.xml { render :xml => @route.errors, :status => :unprocessable_entity }
      end
    end
  end
  
  def route_schedule
    @route = Route.find(params[:id])
    @routes_schedule = RouteSchedule.find_all_by_route_id(@route.id)
  end
  
  def edit_routes_schedule
    @route = Route.find(params[:id])
    @route_schedule = RouteSchedule.find(params[:schedule_id])
  end

  def edit
    @route = Route.find(params[:id])
    @main_routes = Route.all( :conditions=>["main_route_id is NULL"])
  end
  
  def update_routes_schedule
   @route_schedule = RouteSchedule.find(params[:schedule_id])
   @route = Route.find(params[:id]) 
   respond_to do |format|
      if @route_schedule.update_attributes(params[:route_schedule])
        flash[:notice] = "#{t('flash7')}"
        format.html {redirect_to :controller => "routes", :action => "route_schedule",:id=>@route.id}
        format.xml { head :ok }
      else
        format.html {redirect_to :controller => "routes", :action => "edit_routes_schedule",:id=>@route.id,:schedule_id=>@route_schedule.id}
        format.xml { render :xml =>@route.errors, :status => :unprocessable_entity }
      end
   end
  end

  def update
    @route = Route.find(params[:id])
    @main_routes = Route.all( :conditions=>["main_route_id is NULL"])
    respond_to do |format|
      if @route.update_attributes(params[:route])
        flash[:notice] = "#{t('flash2')}"
        format.html { redirect_to(@route) }
        format.xml { head :ok }
      else
        format.html { render :action => "edit" }
        format.xml { render :xml =>@route.errors, :status => :unprocessable_entity }
      end
    end
  end
  
  def destroy_schedule
    @route_schedule = RouteSchedule.find(params[:id])
    route_id = @route_schedule.route_id
      @route_schedule.destroy
      flash[:notice] = "#{t('flash6')}"
    redirect_to :controller => "routes", :action => "route_schedule",:id=>route_id
  end

  def destroy
    @route = Route.find(params[:id])

    if @route.transports.empty? and @route.vehicles.empty?
      @route.destroy
      flash[:notice] = "#{t('flash3')}"
    else
      flash[:warn_notice] = "<p>#{t('flash4')}</p>" unless @route.transports.empty?
      flash[:warn_notice] = "<p>#{t('flash5')}</p>" unless @route.vehicles.empty?
      
    end
    redirect_to routes_url
  end


  def show
    redirect_to :action => "index"
    #    @route = Route.find(params[:id])
    #    respond_to do |format| format.html
    #    end
  end
end
