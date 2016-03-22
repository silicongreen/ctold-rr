class IntelligenceController < ApplicationController
  include ActionView::Helpers::TextHelper
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time  
  def index
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    @config = Configuration.find_by_config_key('StudentAttendanceType')
    @date_today = @local_tzone_time.to_date
    if current_user.admin?
      @batches = Batch.active
    elsif @current_user.privileges.map{|p| p.name}.include?('StudentAttendanceRegister')
      @batches = Batch.active
    elsif @current_user.employee?
      if @config.config_value == 'Daily'
        @batches = @current_user.employee_record.batches
      else
        @batches = @current_user.employee_record.batches
        @batches += @current_user.employee_record.subjects.collect{|b| b.batch}
        @batches += TimetableSwap.find_all_by_employee_id(@current_user.employee_record.try(:id)).map(&:subject).flatten.compact.map(&:batch)
        @batches = @batches.uniq unless @batches.empty?
      end
    end
   # get_report_full()
    @report_data = []
#    if @student_response['status']['code'].to_i == 200
#      @report_data = @student_response['data']
#    end
    
  end
  def comparisom
    @courses = Course.find(:all, :conditions => ["is_deleted = 0"], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    render :partial=>"comparisom"
  end
  
  def get_att_report_class
    require 'json'
    
    if params[:student][:graph_type].blank?
      @graph_type = "Day"
    else
      @graph_type = params[:student][:graph_type]
    end 
    
    @date_used = @local_tzone_time.to_date
    if !params[:select_date].blank?
      @date_used = params[:select_date]
    end
    
    if params[:student][:data_type].blank?
      @data_type = 1
    elsif params[:student][:data_type]=="Present"
      @data_type = 1
    elsif   params[:student][:data_type]=="Absent"
      @data_type = 2
    elsif   params[:student][:data_type]=="Late"
      @data_type = 3
    elsif   params[:student][:data_type]=="Leave"
      @data_type = 4  
    else  
      @data_type = 1
    end
    
    if !params[:student].blank? and !params[:student][:class_name].blank?
      get_report_class(params[:student][:class_name],@graph_type,@data_type,@date_used)
    else
      get_report_class(false,@graph_type,@data_type,@date_used)
    end 
    
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
      
      x_value = []
      y_value = []
      @report_data['att_data_graph'].each do |key,value|
        x_value << key
        y_value << value
      end
     
      @att_data_x = x_value.join(",")
      @att_data_y = y_value.join(",")
      @graph = open_flash_chart_object(895, 450,
      "/intelligence/graph_for_attandence_class?att_data_x=#{@att_data_x}&att_data_y=#{@att_data_y}&graph_type=#{@graph_type}&data_type=#{@data_type}")
    end
    
    respond_to do |format|
      format.js { render :action => 'report_data_class' }
    end
    
  end
  
  def graph_for_attandence_class
    
    att_data_y = params[:att_data_y]
    att_data_y_array = att_data_y.split(",")
    
    att_data_x = params[:att_data_x]
    att_data_x_array = att_data_x.split(",")
    
    x_labels = []
    data = []
    max_value = 0
    min_value = 100
    key = 0
    att_data_y_array.each do |value|
      data << value.to_i
      if value.to_i>max_value
        max_value = value.to_i
      end
      if value.to_i<min_value
        min_value = value.to_i
      end
      
      x_labels << att_data_x_array[key]
      
      key = key+1;
      
    end
    
    if min_value>5
      min_value = min_value-5
    end
    
    if max_value < 95
      max_value = max_value+5
    end
    
    diff = max_value-min_value
    
    increament = 1
    
    if diff>0
      inc_float = diff/8
      increament = inc_float.ceil
    end
    
    bargraph = BarFilled.new()
    bargraph.width = 1;
    bargraph.colour = '#bb0000';
    bargraph.dot_size = 5;
    bargraph.text = "Your Mark"
    bargraph.values = data

    x_axis = XAxis.new
    x_axis.labels = x_labels
    x_axis.set_body_style("max-width: 30px; float: left; text-align: justify;")
    x_axis.set_title_style("max-width: 30px; float: left; text-align: justify;")

    y_axis = YAxis.new
    y_axis.set_range(min_value,max_value,increament)

    title = Title.new("Comparisom")

    x_legend = XLegend.new("Class/Section")
    x_legend.set_style('{font-size: 14px; color: #778877}')

    if params[:data_type].to_i == 1
      y_legend = YLegend.new("Present (%)")
    elsif params[:data_type].to_i == 2
      y_legend = YLegend.new("Absent (%)")
    elsif params[:data_type].to_i == 3
      y_legend = YLegend.new("Late (%)")
    else 
      y_legend = YLegend.new("Leave (%)")
    end
    y_legend.set_style('{font-size: 14px; color: #770077}')

    chart = OpenFlashChart.new
    chart.set_title(title)
    chart.y_axis = y_axis
    chart.x_axis = x_axis
    chart.y_legend = y_legend
    chart.x_legend = x_legend

    chart.add_element(bargraph)

    render :text => chart.render
    

  end
  
  
  def get_att_report 
    require 'json'
    
    if params[:student][:graph_type].blank?
      @graph_type = "Day"
    else
      @graph_type = params[:student][:graph_type]
    end 
    
    @date_used = @local_tzone_time.to_date
    if !params[:select_date].blank?
      @date_used = params[:select_date]
    end
    
    
    if params[:student][:data_type].blank?
      @data_type = 1
    elsif params[:student][:data_type]=="Present"
      @data_type = 1
    elsif   params[:student][:data_type]=="Absent"
      @data_type = 2
    elsif   params[:student][:data_type]=="Late"
      @data_type = 3
    elsif   params[:student][:data_type]=="Leave"
      @data_type = 4  
    else  
      @data_type = 1
    end
    
    if !params[:student].blank? and !params[:student][:batch_name].blank?
      batches_data = Batch.find_by_id(params[:student][:batch_name])
      params[:batch_name] = batches_data.name
    end
    
    if !params[:student].blank? and !params[:student][:class_name].blank?
      params[:class_name] = params[:student][:class_name]
    end
    
    if !params[:student].blank? and !params[:student][:section].blank?
      params[:course_id] = params[:student][:section]
    end
    


    
    if !params[:student].blank? and !params[:student][:batch_name].blank? and !params[:course_id].blank?
      batches_data = Batch.find_by_id(params[:student][:batch_name])
      batch_name = batches_data.name
      course_id = params[:course_id]
      @batch_data = Rails.cache.fetch("course_data_#{course_id}_#{batch_name}_#{current_user.id}"){
        if batch_name.length == 0
          batches = Batch.find_by_course_id(course_id)
        else
          batches = Batch.find_by_course_id_and_name(course_id, batch_name)
        end
        batches
      }
      params[:batch_id] = 0
      unless @batch_data.nil?
        params[:batch_id] = @batch_data.id 
      end
    end
    if !params[:batch_id].blank?
      get_report_full(params[:batch_id],false,false,@graph_type,@data_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    elsif !params[:batch_name].blank? and !params[:class_name].blank?
      get_report_full(false,params[:batch_name],params[:class_name],@graph_type,@data_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    elsif !params[:batch_name].blank?
     
      get_report_full(false,params[:batch_name],false,@graph_type,@data_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    else
      get_report_full(false,false,false,@graph_type,@data_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    end  
    
    if !@report_data.blank? and !@report_data['att_graph'].blank?
      @att_data = []
      
      a_data = @report_data['att_graph'].values
      
      @att_data_string = a_data.join(",")
      
      @graph = open_flash_chart_object(895, 450,
      "/intelligence/graph_for_attandence?att_data=#{@att_data_string}&graph_type=#{@graph_type}&data_type=#{@data_type}")

    end
    respond_to do |format|
      format.js { render :action => 'report_data' }
    end
  
  end
  def graph_for_attandence

     

    x_labels = []
    data_string = params[:att_data]
    data_array = data_string.split(",")
    data = []
    t = 1
    max_value = 0
    min_value = 100
    a_length = data_array.length
    data_array.each do |value|
      data << value.to_i
      if value.to_i>max_value
        max_value = value.to_i
      end
      if value.to_i<min_value
        min_value = value.to_i
      end
      
      if t==a_length
        if params[:graph_type]=="Day"
          x_labels << "Today"
        elsif params[:graph_type]=="Week"
          x_labels << "This Week"
        else
          x_labels << "This Month"
        end
      else
        if t==1
          x_labels << t.to_s+" "+params[:graph_type]
        else
          x_labels << t.to_s+" "+params[:graph_type]+"s"
        end
      end  
      t = t+1
    end
    
    
    line = Line.new
  

    line.width = 4; line.colour = '#5E4725'; line.dot_size = 10; line.values = data

    x_axis = XAxis.new
    x_axis.labels = x_labels

    y_axis = YAxis.new
    
    if min_value>5
      min_value = min_value-5
    end
    
    if max_value < 95
      max_value = max_value+5
    end
    
    diff = max_value-min_value
    increament = 1
    
    if diff>0
      inc_float = diff/8
      increament = inc_float.ceil
    end
    
    
    
    y_axis.set_range(min_value,max_value,increament)

    title = Title.new("Attendance")

    x_legend = XLegend.new(params[:graph_type]+"s")
    x_legend.set_style('{font-size: 14px; color: #778877; padding:5px;}')
    
    if params[:data_type].to_i == 1
      y_legend = YLegend.new("Present (%)")
    elsif params[:data_type].to_i == 2
      y_legend = YLegend.new("Absent (%)")
    elsif params[:data_type].to_i == 3
      y_legend = YLegend.new("Late (%)")
    else 
      y_legend = YLegend.new("Leave (%)")
    end
    
    y_legend.set_style('{font-size: 14px; color: #770077; padding:5px;}')

    chart = OpenFlashChart.new
    chart.set_title(title)
    chart.set_x_legend(x_legend)
    chart.set_y_legend(y_legend)
    chart.y_axis = y_axis
    chart.x_axis = x_axis

    chart.add_element(line)

    render :text => chart.to_s
    

    
    
    
  end
  
  private
  def get_report_full(batch_id=false,batch_name=false,class_name=false,type="Day",report_type=1,date_used=false)
    require 'net/http'
    require 'uri'
    require "yaml"
    
   
    if type == "Day"
      type = "days"
    end
    if type == "Week"
      type = "week"
    end
    if type == "Month"
      type = "months"
    end

    
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.employee? or current_user.admin?
      api_uri = URI(api_endpoint + "api/calender/studentattendenceintelligence")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      if !batch_id.blank?
          request.set_form_data({"batch_id"=>batch_id,"date"=>date_used,"type"=>type,"report_type"=>report_type,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      elsif !batch_name.blank? and !class_name.blank?
          request.set_form_data({"batch_name"=>batch_name,"date"=>date_used,"type"=>type,"report_type"=>report_type,"class_name"=>class_name,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      elsif !batch_name.blank?
          request.set_form_data({"batch_name"=>batch_name,"date"=>date_used,"type"=>type,"report_type"=>report_type,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      else
          request.set_form_data({"call_from_web"=>1,"date"=>date_used,"type"=>type,"report_type"=>report_type,"user_secret" =>session[:api_info][0]['user_secret']})
      end 
     
      response = http.request(request)
      @student_response = JSON::parse(response.body)
    end
    
    @student_response
  end
  
  def get_report_class(class_name=false,type="Day",report_type=1,date_used=false)
    require 'net/http'
    require 'uri'
    require "yaml"
    
   
    if type == "Day"
      type = "days"
    end
    if type == "Week"
      type = "week"
    end
    if type == "Month"
      type = "months"
    end

    
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.employee? or current_user.admin?
      api_uri = URI(api_endpoint + "api/calender/attcomparisom")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
     
      if !class_name.blank?
          request.set_form_data({"date"=>date_used,"type"=>type,"report_type"=>report_type,"class_name"=>class_name,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      
      else
          request.set_form_data({"call_from_web"=>1,"date"=>date_used,"type"=>type,"report_type"=>report_type,"user_secret" =>session[:api_info][0]['user_secret']})
      end 
     
      response = http.request(request)
      @student_response = JSON::parse(response.body)
    end
    
    @student_response
  end
end
