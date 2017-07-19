class Api::VehiclesController < ApiController

  def index
    @xml = Builder::XmlMarkup.new
    @vehicles = Vehicle.search(params[:search])

    respond_to do |format|
       unless params[:search].present?
        render "single_access_tokens/500.xml"  and return
      else
      format.xml  { render :vehicles }
      end
    end
  end
end