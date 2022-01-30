class CustomReportsController < ApplicationController
  filter_access_to :all
  before_filter :login_required
  before_filter :check_permission,:only=>[:index]
  before_filter :find_and_check_model,:only=>[:generate,:edit]
  def find_and_check_model
   
    @model_name = params[:id].camelize.singularize
    unless ["Student","Employee"].include? @model_name
      flash[:notice] = "#{t(@model_name.underscore)} #{t('report_cannot_be_generated')}"
      redirect_to :action=>:index
      return
    else
      @model = Kernel.const_get(@model_name)
    end
  end  
  def index
    @reports=Report.find(:all,:order =>'name ASC').paginate(:page => params[:page], :per_page => 20)
  end

  def generate    
    @report=Report.new
    @report.model = @model_name
    @model.extend JoinScope
    @model.extend AdditionalFieldScope
    @search_fields = @model.fields_to_search.deep_copy
    make_report_columns
    @search_fields.each do |type,columns|
      case type
      when :string
        columns.each do |col|
          ["like","begins_with","equals"].each do |criteria|
            @report.report_queries.build(:table_name => @model_name,:column_name=>col,:criteria=>criteria,:column_type=>type,:field_name=>col)
          end
        end
      when :date
        columns.each do |col|
          ["gte","lte","equals"].each do |criteria|
            @report.report_queries.build(:table_name => @model_name,:column_name=>col,:criteria=>criteria,:column_type=>type,:field_name=>col)
          end
        end
      when :association
        columns.each do |col|
          @report.report_queries.build(:table_name => @model_name,:column_name=>col,:criteria=>"in",:column_type=>type,:field_name=>col)
        end
      when :boolean
        columns.each do |col|
          @report.report_queries.build(:table_name => @model_name,:column_name=>col,:criteria=>"is",:column_type=>type,:field_name=>col)
        end
      when :integer
        columns.each do |col|
          ["gte","lte","equals"].each do |criteria|
            @report.report_queries.build(:table_name => @model_name,:column_name=>col,:criteria=>criteria,:column_type=>type,:field_name=>col)
          end
        end
      end

    end
    @search_fields[:additional]= @model.additional_field_methods
    @model.get_additional_fields.each do |f|
      if f.name.to_i == 0
        ["equals"].each do |criteria|
          @report.report_queries.build(:table_name => @model.additional_detail_model.to_s,:column_name=>f.id,:criteria=>criteria,:column_type=>:additional,:field_name=>( f.name.downcase.gsub(" ","_") + "_additional_fields_" + f.id.to_s))
        end
      end
    end
    
    if request.post?
      @report = Report.new(params[:report])
      if @report.save
        flash[:notice]="#{t('report_created_successfully')}"
        redirect_to :action => "index"
      else
        render :action => "generate"
      end
    end
    
  end

  def edit
    @report=Report.find params[:id]
    @model_name=@report.model
    @all_columns=@model.fields_to_search
  end

  def show
    @report=Report.find params[:id]
    @report_columns = @report.report_columns
    @report_columns.delete_if{|rc| !((@report.model_object.instance_methods+@report.model_object.column_names).include?(rc.method))}
    @column_type = Hash.new
    @report.model_object.columns_hash.each{|key,val| @column_type[key]=val.type }
    search = @report.model_object.report_search(@report.search_param)
    search = search.all.uniq
    @search_results = search.paginate(:include=>@report.include_param,:page=>params[:page])
  end

  def custom_report_pdf
    @report=Report.find params[:id]
    @report_columns = @report.report_columns
    @report_columns.delete_if{|rc| !((@report.model_object.instance_methods+@report.model_object.column_names).include?(rc.method))}
    @column_type = Hash.new
    @report.model_object.columns_hash.each{|key,val| @column_type[key]=val.type }
    @search_results = @report.model_object.report_search(@report.search_param)
    
    render :pdf => 'custom_report_pdf',
        :margin => {    :top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end

  def to_csv
    report=Report.find params[:id]
    report_columns = report.report_columns
    report_columns.delete_if{|rc| !((report.model_object.instance_methods+report.model_object.column_names).include?(rc.method))}
    if MultiSchool.current_school.id == 340
      csv = report.to_csv_sjws_2
    else
      csv = report.to_csv
    end
    filename = "#{report.name}-#{Time.now.to_date.to_s}.csv"
    send_data(csv, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
  end
  
  def delete
    if Report.destroy params[:id]
      flash[:notice]="#{t('report_deleted_successfully')}."
    else
      flash[:notice]="#{t('report_delete_error')}."
    end
    redirect_to :action=>'index'
  end
 

  private

  def make_report_columns
    @model.fields_to_display.each do |col|
      if col.to_s == "parent_passport"
        @report.report_columns.build(:method=>col,:title=>"NID/Passport Number")
      else
        @report.report_columns.build(:method=>col,:title=>t(col))
      end
    end
    @model.additional_field_methods.each do |col|
      @report.report_columns.build(:method=>col,:title=>col.to_s.titleize)
    end
  end
end
