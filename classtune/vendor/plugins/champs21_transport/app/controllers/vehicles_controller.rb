class VehiclesController < ApplicationController
  filter_access_to :all
  before_filter :login_required
  before_filter :check_permission,:only=>[:index]

  def index
    @vehicle = Vehicle.all
  end

  def new
    @vehicle = Vehicle.new
    @routes = Route.all( :conditions=>["main_route_id is NULL"])
  end

  def create
    @vehicle = Vehicle.new(params[:vehicle])
    @routes = Route.all( :conditions=>["main_route_id is NULL"])
    respond_to do |format|
      if @vehicle.save
        flash[:notice] = "#{t('flash1')}"
        format.html { redirect_to(@vehicle) }
        format.xml { render :xml => @vehicle, :status => :created, :location => @vehicle }
      else
        format.html { render :action => "new" }
        format.xml { render :xml => @vehicle.errors, :status => :unprocessable_entity }
      end
    end
  end

  def edit
    @vehicle = Vehicle.find(params[:id])
    @routes = Route.all( :conditions=>["main_route_id is NULL"])
  end
    
  def update
    @vehicle = Vehicle.find(params[:id])
    @routes = Route.all( :conditions=>["main_route_id is NULL"])
    respond_to do |format|
      if @vehicle.update_attributes(params[:vehicle])
        flash[:notice] = "#{t('flash2')}"
        format.html { redirect_to(@vehicle) }
      else
        format.html { render :action => "edit" }
      end
    end
  end

  def destroy
    @vehicle = Vehicle.find(params[:id])
    if @vehicle.destroy
      flash[:notice]= "#{t('flash3')}"
    else
      flash[:warn_notice]="<p>#{@vehicle.errors.full_messages}</p>"
    end
    respond_to do |format|
      format.html { redirect_to(vehicles_url) }
      format.xml { head :ok }
    end
  end

  def show
    redirect_to :action => "index"
    #    @vehicle = Vehicle.find(params[:id])
    #    respond_to do |format| format.html
    #    end
  end
end