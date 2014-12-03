class ExportsController < ApplicationController
  before_filter :login_required
  filter_access_to :all
  
  def index
    @exports = Export.all.paginate :per_page=> 20,:page => params[:page]
  end

  def new
    @models = Export.get_models.select{|model| defined?model.second.camelize.constantize == "constant"}
    @export = Export.new
  end
  
  def create
    @export = Export.new
    unless params[:model_name].blank?
      all_columns = params[:associated_columns].nil? ? Array.new : params[:associated_columns].split(',')
      join_columns = params[:join_columns].nil? ? Array.new : params[:join_columns].split(',')
      if all_columns.nil? and join_columns.nil?
        associated_columns = []
      elsif join_columns.blank?
        associated_columns = all_columns
      else
        associated_columns = all_columns - join_columns
      end
      structure = Export.make_final_columns_set(params[:model_name],associated_columns,join_columns)
      @export = Export.new(:name => params[:export][:name],:structure => structure.first,:associated_columns => structure.second,:join_columns => structure.third,:model => params[:model_name])
      if @export.save
        flash[:notice] = "CSV format saved successfully"
        redirect_to exports_path
      else
        @models = Export.get_models
        render :new
      end
    else
      @models = Export.get_models
      @export.errors.add "model","cant be blank"
      render :new
    end
  end

  def populate_associates
    render :update do |page|
      @model = params[:model]
      unless @model.blank?
        model_name = @model.underscore
        settings = Export.load_yaml(@model)
        @associated_models = settings[model_name]["associates"].nil? ? Array.new : settings[model_name]["associates"].keys.map{|key| [key.humanize,key]}
        @join_models = settings[model_name]["joins"].nil? ? Array.new : settings[model_name]["joins"].keys.map{|key| [key.humanize,"#{key}|join"]}
        page.replace_html 'associates',:partial => 'associates'
        page.replace_html 'associate_columns1',:text => ""
      else
        page.replace_html 'associates',:text => ''
        page.replace_html 'associate_columns1',:text => ""
      end
    end
  end

  def show_associated_columns
    associated_model = Array.new
    join_model = Array.new
    model_names = params[:model_names].split(',')
    associated_model = model_names.select{|model| model.split('|').count == 1}
    join_model = model_names - associated_model
    model = params[:model]
    render :update do |page|
      @associated_columns = Export.prepare_associated_columns(model,associated_model) unless associated_model.blank?
      @join_columns = Export.prepare_join_columns(model,join_model.map{|join_model_name| join_model_name.split('|').first}) unless join_model.blank?
      all_columns = [@associated_columns,@join_columns].compact
      @all_columns = all_columns.flatten
      page.replace_html 'associate_columns1',:partial => "associates_columns"
    end
  end

  def export_csv
    @export = Export.find(params[:id])
    header_columns = @export.associated_columns
    model = @export.model
    csv_data = Export.load_fastercsv(header_columns,model)
    send_csv_format(@export.name,csv_data.first)
  end

  def destroy
    @export = Export.find(params[:id])
    @export.destroy
    flash[:notice] = "Export format destroyed successfully"
    redirect_to exports_path
  end

  private
  def send_csv_format(name,csv_data)
    filename = "#{name}_csv_format (Created on #{Time.now.to_date.to_s}).csv"
    send_data(csv_data, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
  end

end
