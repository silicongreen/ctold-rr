class RoutesController < ApplicationController
  filter_access_to :all
  before_filter :login_required
  before_filter  :set_precision
  
  def index
    @route = Route.all
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

  def edit
    @route = Route.find(params[:id])
    @main_routes = Route.all( :conditions=>["main_route_id is NULL"])
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
