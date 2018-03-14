class IntelligenceController < ApplicationController
  include ActionView::Helpers::TextHelper
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time

  
  
  def teacher_lessonplan
    @date_today = @local_tzone_time.to_date
    @departments = EmployeeDepartment.find(:all,:order => "name asc",:conditions=>'status = 1')
    render :partial=>"teacher_lessonplan"
  end
  
  def get_teacher_lessonplans 
    require 'json'
    
    @department_id = 0
    @sort_by = "lessonplan_given";
    @sort_type = 1;
    @time_range = "day";
    @date = @local_tzone_time.to_date
    
    if !params[:student][:department].blank?
      @department_id = params[:student][:department]
    end
    if !params[:student][:sort_by].blank?
      @sort_by = params[:student][:sort_by]
    end 
    if !params[:student][:sort_type].blank?
      @sort_type = params[:student][:sort_type]
    end
    if !params[:student][:timerange].blank?
      @time_range = params[:student][:timerange]
    end
    if !params[:select_date].blank?
      @date = params[:select_date]
    end
    
    get_lessonplan_report_full_teacher(@department_id,@sort_by,@sort_type,@time_range,@date)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
    
    respond_to do |format|
      format.js { render :action => 'report_data_teacher_lessonplan' }
    end
  
  end
  
  def lessonplan
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
    @report_data = []
    
  end
  
  def get_lessonplan_report 
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
      if batch_name.length == 0
        @batch_data = Rails.cache.fetch("batch_data_#{course_id}"){
          batches = Batch.find_by_course_id(course_id)
          batches
        }
      else
        @batch_data = Rails.cache.fetch("batch_data_#{course_id}_#{batch_name.parameterize("_")}"){
          batches = Batch.find_by_course_id_and_name(course_id, batch_name)
          batches
        }
      end 
      
      params[:batch_id] = 0
      unless @batch_data.nil?
        params[:batch_id] = @batch_data.id 
      end
    end
    if !params[:batch_id].blank?
      get_lessonplan_report_full(params[:batch_id],false,false,@graph_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    elsif !params[:batch_name].blank? and !params[:class_name].blank?
      get_lessonplan_report_full(false,params[:batch_name],params[:class_name],@graph_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    elsif !params[:batch_name].blank?
     
      get_lessonplan_report_full(false,params[:batch_name],false,@graph_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    else
      get_lessonplan_report_full(false,false,false,@graph_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    end  
    
    if !@report_data.blank? and !@report_data['att_graph'].blank?
      @att_data = []
      
      a_data = @report_data['att_graph'].values
      
      a_data_date = @report_data['att_graph_date'].values
      
      @att_data_string = a_data.join(",")
      @att_data_string_date = a_data_date.join(",")
      
      @graph = open_flash_chart_object(895, 450,
      "/intelligence/graph_for_lessonplan?att_data=#{@att_data_string}&att_data_string_date=#{@att_data_string_date}&graph_type=#{@graph_type}")

    end
    respond_to do |format|
      format.js { render :action => 'report_data_lessonplan' }
    end
  
  end
  
  def graph_for_lessonplan

    x_labels = []
    data_string = params[:att_data]
    data_array = data_string.split(",")
    
    
    data_string_date = params[:att_data_string_date]
    data_array_date = data_string_date.split(",")
    
    data = []
    t = 1
    l = 0
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
      x_labels << data_array_date[l]
      

      t = t+1
      l = l+1
    end
    
    
    line = Line.new
  

    line.width = 4; line.colour = '#64B846'; line.dot_size = 10; line.values = data

    x_axis = XAxis.new
    x_axis.labels = x_labels

    y_axis = YAxis.new
    
    if min_value>2
      min_value = min_value-2
    end
    
    
    max_value = max_value+2
   
    
    diff = max_value-min_value
    increament = 1
    
    if diff>0
      inc_float = diff/8
      increament = inc_float.ceil
    end
    
    
    
    y_axis.set_range(min_value,max_value,increament)

    title = Title.new("Lessonplan")

    x_legend = XLegend.new(params[:graph_type]+"s")
    x_legend.set_style('{font-size: 14px; color: #784016; padding:5px;}')
    
   
    y_legend = YLegend.new("Frequency (%)")
   
    
    y_legend.set_style('{font-size: 14px; color: #784016; padding:5px;}')

    chart = OpenFlashChart.new
    chart.set_title(title)
    chart.set_x_legend(x_legend)
    chart.set_y_legend(y_legend)
    chart.y_axis = y_axis
    chart.x_axis = x_axis

    chart.add_element(line)
    chart.set_bg_colour( '#DAF6DA' );

    render :text => chart.to_s
    
  end
  
  
  
  
  
  def teacher_classwork
    @date_today = @local_tzone_time.to_date
    @departments = EmployeeDepartment.find(:all,:order => "name asc",:conditions=>'status = 1')
    render :partial=>"teacher_classwork"
  end
  
  def teacher_classwork_pdf
    @department_id = 0
    @sort_by = "classwork_given";
    @sort_type = 1;
    @time_range = "day";
    @date = @local_tzone_time.to_date
    
    if !params[:student][:department].blank?
      @department_id = params[:student][:department]
    end
    if !params[:student][:sort_by].blank?
      @sort_by = params[:student][:sort_by]
    end 
    if !params[:student][:sort_type].blank?
      @sort_type = params[:student][:sort_type]
    end
    if !params[:student][:timerange].blank?
      @time_range = params[:student][:timerange]
    end
    if !params[:select_date].blank?
      @date = params[:select_date]
    end
    
    get_classwork_report_full_teacher(@department_id,@sort_by,@sort_type,@time_range,@date)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
    render :pdf => "teacher_classwork_pdf",
    :orientation => 'Portrait',
    :margin => {    :top=> 10,
    :bottom => 10,
    :left=> 10,
    :right => 10},
    :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
    :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def get_teacher_classworks 
    require 'json'
    
    @department_id = 0
    @sort_by = "classwork_given";
    @sort_type = 1;
    @time_range = "day";
    @date = @local_tzone_time.to_date
    
    if !params[:student][:department].blank?
      @department_id = params[:student][:department]
    end
    if !params[:student][:sort_by].blank?
      @sort_by = params[:student][:sort_by]
    end 
    if !params[:student][:sort_type].blank?
      @sort_type = params[:student][:sort_type]
    end
    if !params[:student][:timerange].blank?
      @time_range = params[:student][:timerange]
    end
    if !params[:select_date].blank?
      @date = params[:select_date]
    end
    
    get_classwork_report_full_teacher(@department_id,@sort_by,@sort_type,@time_range,@date)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
    
    respond_to do |format|
      format.js { render :action => 'report_data_teacher_classwork' }
    end
  
  end
  
  def classwork
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
    @report_data = []
    
  end
  
  def get_classwork_report 
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
      if batch_name.length == 0
        @batch_data = Rails.cache.fetch("batch_data_#{course_id}"){
          batches = Batch.find_by_course_id(course_id)
          batches
        }
      else
        @batch_data = Rails.cache.fetch("batch_data_#{course_id}_#{batch_name.parameterize("_")}"){
          batches = Batch.find_by_course_id_and_name(course_id, batch_name)
          batches
        }
      end 
      
      params[:batch_id] = 0
      unless @batch_data.nil?
        params[:batch_id] = @batch_data.id 
      end
    end
    if !params[:batch_id].blank?
      get_classwork_report_full(params[:batch_id],false,false,@graph_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    elsif !params[:batch_name].blank? and !params[:class_name].blank?
      get_classwork_report_full(false,params[:batch_name],params[:class_name],@graph_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    elsif !params[:batch_name].blank?
     
      get_classwork_report_full(false,params[:batch_name],false,@graph_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    else
      get_classwork_report_full(false,false,false,@graph_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    end  
    
    if !@report_data.blank? and !@report_data['att_graph'].blank?
      @att_data = []
      
      a_data = @report_data['att_graph'].values
      
      a_data_date = @report_data['att_graph_date'].values
      
      @att_data_string = a_data.join(",")
      @att_data_string_date = a_data_date.join(",")
      
      @graph = open_flash_chart_object(895, 450,
      "/intelligence/graph_for_classwork?att_data=#{@att_data_string}&att_data_string_date=#{@att_data_string_date}&graph_type=#{@graph_type}")

    end
    respond_to do |format|
      format.js { render :action => 'report_data_classwork' }
    end
  
  end
  
  def graph_for_classwork

    x_labels = []
    data_string = params[:att_data]
    data_array = data_string.split(",")
    
    
    data_string_date = params[:att_data_string_date]
    data_array_date = data_string_date.split(",")
    
    data = []
    t = 1
    l = 0
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
      x_labels << data_array_date[l]
      

      t = t+1
      l = l+1
    end
    
    
    line = Line.new
  

    line.width = 4; line.colour = '#64B846'; line.dot_size = 10; line.values = data

    x_axis = XAxis.new
    x_axis.labels = x_labels

    y_axis = YAxis.new
    
    if min_value>2
      min_value = min_value-2
    end
    
    
    max_value = max_value+2
   
    
    diff = max_value-min_value
    increament = 1
    
    if diff>0
      inc_float = diff/8
      increament = inc_float.ceil
    end
    
    
    
    y_axis.set_range(min_value,max_value,increament)

    title = Title.new("Classwork")

    x_legend = XLegend.new(params[:graph_type]+"s")
    x_legend.set_style('{font-size: 14px; color: #784016; padding:5px;}')
    
   
    y_legend = YLegend.new("Frequency (%)")
   
    
    y_legend.set_style('{font-size: 14px; color: #784016; padding:5px;}')

    chart = OpenFlashChart.new
    chart.set_title(title)
    chart.set_x_legend(x_legend)
    chart.set_y_legend(y_legend)
    chart.y_axis = y_axis
    chart.x_axis = x_axis

    chart.add_element(line)
    chart.set_bg_colour( '#DAF6DA' );

    render :text => chart.to_s
    
  end
  

  
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
  def teacher_homework
    @date_today = @local_tzone_time.to_date
    @departments = EmployeeDepartment.find(:all,:order => "name asc",:conditions=>'status = 1')
    render :partial=>"teacher_homework"
  end
  
  def teacher_homework_pdf
    @department_id = 0
    @sort_by = "homework_given";
    @sort_type = 1;
    @time_range = "day";
    @date = @local_tzone_time.to_date
    
    if !params[:student][:department].blank?
      @department_id = params[:student][:department]
    end
    if !params[:student][:sort_by].blank?
      @sort_by = params[:student][:sort_by]
    end 
    if !params[:student][:sort_type].blank?
      @sort_type = params[:student][:sort_type]
    end
    if !params[:student][:timerange].blank?
      @time_range = params[:student][:timerange]
    end
    if !params[:select_date].blank?
      @date = params[:select_date]
    end
    
    get_homework_report_full_teacher(@department_id,@sort_by,@sort_type,@time_range,@date)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
    render :pdf => "teacher_homework_pdf",
    :orientation => 'Portrait',
    :margin => {    :top=> 10,
    :bottom => 10,
    :left=> 10,
    :right => 10},
    :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
    :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def get_teacher_homeworks 
    require 'json'
    
    @department_id = 0
    @sort_by = "homework_given";
    @sort_type = 1;
    @time_range = "day";
    @date = @local_tzone_time.to_date
    
    if !params[:student][:department].blank?
      @department_id = params[:student][:department]
    end
    if !params[:student][:sort_by].blank?
      @sort_by = params[:student][:sort_by]
    end 
    if !params[:student][:sort_type].blank?
      @sort_type = params[:student][:sort_type]
    end
    if !params[:student][:timerange].blank?
      @time_range = params[:student][:timerange]
    end
    if !params[:select_date].blank?
      @date = params[:select_date]
    end
    
    get_homework_report_full_teacher(@department_id,@sort_by,@sort_type,@time_range,@date)
    @report_data = []
    if @student_response['status']['code'].to_i == 200
      @report_data = @student_response['data']
    end
    
    respond_to do |format|
      format.js { render :action => 'report_data_teacher_homework' }
    end
  
  end
  
  def homework
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
    @report_data = []
    
  end
  
  def subject_wise_report
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    @batches = Batch.active
    @subjects = []
    render :partial=>"subject_wise_report"
  end
  
  def section_report 
    @courses = Course.find(:all, :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    @exam_groups = []
    render :partial=>"section_report"
  end
  def report_section 
    class_name = params[:class_name]
    exam_name = params[:exam_name]
    @graph = open_flash_chart_object(900, 450,
           "graph_for_generated_report_section?class_name=#{class_name}&exam_name=#{exam_name}")
   
    render :partial=>"report_section"
  end
  
  def graph_for_generated_report_section
    class_name = params[:class_name]
    exam_name = params[:exam_name]
    get_exams_section_report(class_name,exam_name)
    @exam_data = []
    if @exam_response['status']['code'].to_i == 200
      @exam_data = @exam_response['data']
    end
    
    color_array = ['#000000','#FF0000','#0000FF','#4B0082','#00FF00','#FFFF00','#FF7F00','#9400D3']

    x_labels = []
    chart = OpenFlashChart.new
    max_value = 0
    min_value = 100
    k = 0
    @exam_data['result'].keys.sort.each_with_index do |key,index|
      data = []
      colour = "%006x" % (rand * 0xffffff)
      colour = color_array[k]
      value = @exam_data['result'][key]
      k = k+1
      
      value.keys.sort.each_with_index do |kkey,kindex|
        kalue = value[kkey]
        if !x_labels.include?(kkey)
          x_labels << kkey
        end   
        data << kalue.to_i
        
        if kalue.to_i > max_value
          max_value = kalue.to_i
        end
        if kalue.to_i < min_value
          min_value = kalue.to_i
        end
      end 
      bargraph = BarFilled.new()
      bargraph.width = 1;
      bargraph.colour = colour;
      bargraph.dot_size = 5;
      bargraph.text = key 
      bargraph.values = data
      chart.add_element(bargraph)
    end
    
    if min_value>5
      min_value = min_value-5
    end
    
    max_value = max_value+5
    
    diff = max_value-min_value
    increament = 1
    
    if diff>0
      inc_float = diff/8
      increament = inc_float.ceil
    end

    x_axis = XAxis.new
    x_axis.labels = x_labels
    x_axis.set_body_style("max-width: 30px; float: left; text-align: justify;")
    x_axis.set_title_style("max-width: 30px; float: left; text-align: justify;")

    y_axis = YAxis.new
    y_axis.set_range(min_value,max_value,increament)

    title = Title.new("Section Comparisom")

    x_legend = XLegend.new("Marks (%)")
    x_legend.set_style('{font-size: 14px; color: #778877}')

    y_legend = YLegend.new("Number of students (%)")
    y_legend.set_style('{font-size: 14px; color: #770077}')

   
    chart.set_title(title)
    chart.y_axis = y_axis
    chart.x_axis = x_axis
    chart.y_legend = y_legend
    chart.x_legend = x_legend
    render :text => chart.render
  end
  
  def get_exam 
    class_name = params[:class_name]
    get_exams_class(class_name)
    @exam_data = []
    if @exam_response['status']['code'].to_i == 200
      @exam_data = @exam_response['data']
    end
    render(:update) do |page|
      page.replace_html 'exam-group-select', :partial=>'common_exam'
    end
  end
  
  def individual_report
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    @batches = Batch.active
    @exam_groups = []
    @report_data = []
    render :partial=>"individual_report"
  end
  
  def report_overall_subject 
    @subject_id = 0
    if !params[:subject_id].blank?
      @subject_id = params[:subject_id]
      @subject = Subject.find(params[:subject_id])
      @batch = @subject.batch
      @students = @batch.students
      @exam_groups = ExamGroup.active.find(:all,:conditions=>{:batch_id=>@batch.id})
    end
    render :partial=>"report_overall_subject"
  end
  
  def report_overall_individual
    @exam_id = 0;
    if !params[:exam_id].blank?
      @exam_id = params[:exam_id]
      if params[:student].nil?
         @exam_group = ExamGroup.active.find(@exam_id)
         @batch = @exam_group.batch
         @students=@batch.students.by_first_name
         @student = @students.first  unless @students.empty?
         if @student.nil?
           flash[:notice] = "#{t('flash_student_notice')}"
           redirect_to :action => 'exam_wise_report' and return
         end
         general_subjects = Subject.find_all_by_batch_id(@batch.id, :conditions=>"elective_group_id IS NULL")
         student_electives = StudentsSubject.find_all_by_student_id(@student.id,:conditions=>"batch_id = #{@batch.id}")
         elective_subjects = []
         student_electives.each do |elect|
           elective_subjects.push Subject.find(elect.subject_id)
         end
         @subjects = general_subjects + elective_subjects
         @exams = []
         @subjects.each do |sub|
           exam = Exam.find_by_exam_group_id_and_subject_id(@exam_group.id,sub.id)
           @exams.push exam unless exam.nil?
         end
         
         @graph = open_flash_chart_object(650, 350,
           "/exam/graph_for_generated_report?batch=#{@student.batch.id}&examgroup=#{@exam_group.id}&student=#{@student.id}")
         
        render :partial=>"exam_report_student_single"
      else
        @exam_group = ExamGroup.active.find(params[:exam_id])
        @student = Student.find_by_id(params[:student])
        @batch = @student.batch
        general_subjects = Subject.find_all_by_batch_id(@student.batch.id, :conditions=>"elective_group_id IS NULL")
        student_electives = StudentsSubject.find_all_by_student_id(@student.id,:conditions=>"batch_id = #{@student.batch.id}")
        elective_subjects = []
        student_electives.each do |elect|
          elective_subjects.push Subject.find(elect.subject_id)
        end
        @subjects = general_subjects + elective_subjects
        @exams = []
        @subjects.each do |sub|
          exam = Exam.find_by_exam_group_id_and_subject_id(@exam_group.id,sub.id)
          @exams.push exam unless exam.nil?
        end
        
        @graph = open_flash_chart_object(650, 350,
          "/exam/graph_for_generated_report?batch=#{@student.batch.id}&examgroup=#{@exam_group.id}&student=#{@student.id}")
        
        if request.xhr?
          render(:update) do |page|
            page.replace_html   'exam_wise_report', :partial=>"exam_wise_report"
          end
          else
            @students = Student.find_all_by_id(params[:student])
            render :partial=>"exam_report_student_single"
       end
        
      end
    end
  end
  
  def report_overall
    @exam_id = 0;
    if !params[:exam_id].blank?
      @exam_id = params[:exam_id]
      get_exam_report(@exam_id)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    end
    render :partial=>"exam_report_student"
  end
  def report
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    @batches = Batch.active
    @exam_groups = []
    @report_data = []
    
  end
  
  
  def comparisom
    @courses = Course.find(:all, :conditions => ["is_deleted = 0"], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    render :partial=>"comparisom"
  end
  
  def get_homework_report 
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
      if batch_name.length == 0
        @batch_data = Rails.cache.fetch("batch_data_#{course_id}"){
          batches = Batch.find_by_course_id(course_id)
          batches
        }
      else
        @batch_data = Rails.cache.fetch("batch_data_#{course_id}_#{batch_name.parameterize("_")}"){
          batches = Batch.find_by_course_id_and_name(course_id, batch_name)
          batches
        }
      end 
      
      params[:batch_id] = 0
      unless @batch_data.nil?
        params[:batch_id] = @batch_data.id 
      end
    end
    if !params[:batch_id].blank?
      get_homework_report_full(params[:batch_id],false,false,@graph_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    elsif !params[:batch_name].blank? and !params[:class_name].blank?
      get_homework_report_full(false,params[:batch_name],params[:class_name],@graph_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    elsif !params[:batch_name].blank?
     
      get_homework_report_full(false,params[:batch_name],false,@graph_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    else
      get_homework_report_full(false,false,false,@graph_type,@date_used)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    end  
    
    if !@report_data.blank? and !@report_data['att_graph'].blank?
      @att_data = []
      
      a_data = @report_data['att_graph'].values
      
      a_data_date = @report_data['att_graph_date'].values
      
      @att_data_string = a_data.join(",")
      @att_data_string_date = a_data_date.join(",")
      
      @graph = open_flash_chart_object(895, 450,
      "/intelligence/graph_for_homework?att_data=#{@att_data_string}&att_data_string_date=#{@att_data_string_date}&graph_type=#{@graph_type}")

    end
    respond_to do |format|
      format.js { render :action => 'report_data_homework' }
    end
  
  end
  
  def graph_for_homework

     

    x_labels = []
    data_string = params[:att_data]
    data_array = data_string.split(",")
    
    
    data_string_date = params[:att_data_string_date]
    data_array_date = data_string_date.split(",")
    
    data = []
    t = 1
    l = 0
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
      x_labels << data_array_date[l]
      

      t = t+1
      l = l+1
    end
    
    
    line = Line.new
  

    line.width = 4; line.colour = '#64B846'; line.dot_size = 10; line.values = data

    x_axis = XAxis.new
    x_axis.labels = x_labels

    y_axis = YAxis.new
    
    if min_value>2
      min_value = min_value-2
    end
    
    
    max_value = max_value+2
   
    
    diff = max_value-min_value
    increament = 1
    
    if diff>0
      inc_float = diff/8
      increament = inc_float.ceil
    end
    
    
    
    y_axis.set_range(min_value,max_value,increament)

    title = Title.new("Homework")

    x_legend = XLegend.new(params[:graph_type]+"s")
    x_legend.set_style('{font-size: 14px; color: #784016; padding:5px;}')
    
   
    y_legend = YLegend.new("Frequency (%)")
   
    
    y_legend.set_style('{font-size: 14px; color: #784016; padding:5px;}')

    chart = OpenFlashChart.new
    chart.set_title(title)
    chart.set_x_legend(x_legend)
    chart.set_y_legend(y_legend)
    chart.y_axis = y_axis
    chart.x_axis = x_axis

    chart.add_element(line)
    chart.set_bg_colour( '#DAF6DA' );

    render :text => chart.to_s
    

    
    
    
  end
  
  def cricticalinfo
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
    render :partial=>"cricticalinfo"
  end
  
  def get_att_report_crictal 
    require 'json'
    
    if params[:student][:type].blank?
      @type = 1
      @limit = 10
    elsif params[:student][:type]=="3"
      @type = params[:student][:type]
      @limit = 5
    elsif params[:student][:type]=="4"
      @type = params[:student][:type]
      @limit = 3
    else
      @type = params[:student][:type]
      @limit = 10
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
      if batch_name.length == 0
        @batch_data = Rails.cache.fetch("batch_data_#{course_id}"){
          batches = Batch.find_by_course_id(course_id)
          batches
        }
      else
        @batch_data = Rails.cache.fetch("batch_data_#{course_id}_#{batch_name.parameterize("_")}"){
          batches = Batch.find_by_course_id_and_name(course_id, batch_name)
          batches
        }
      end 
      
      params[:batch_id] = 0
      unless @batch_data.nil?
        params[:batch_id] = @batch_data.id 
      end
    end
    if !params[:batch_id].blank?
      get_report_crictical(params[:batch_id],false,false,@type,@limit)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    elsif !params[:batch_name].blank? and !params[:class_name].blank?
      get_report_crictical(false,params[:batch_name],params[:class_name],@type,@limit)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    elsif !params[:batch_name].blank?
     
      get_report_crictical(false,params[:batch_name],false,@type,@limit)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    else
      get_report_crictical(false,false,false,@type,@limit)
      @report_data = []
      if @student_response['status']['code'].to_i == 200
        @report_data = @student_response['data']
      end
    end  
    respond_to do |format|
      format.js { render :action => 'report_data_crictical' }
    end
  
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
      @main_data_type = "Present"
    else
      @main_data_type = params[:student][:data_type]
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
    bargraph.colour = '#64B846';
    bargraph.dot_size = 5;
    bargraph.text = ""
    bargraph.values = data

    x_axis = XAxis.new
    x_axis.labels = x_labels
    x_axis.set_body_style("max-width: 30px; float: left; text-align: justify;")
    x_axis.set_title_style("max-width: 30px; float: left; text-align: justify;")

    y_axis = YAxis.new
    y_axis.set_range(min_value,max_value,increament)

    title = Title.new("Comparisom")

    x_legend = XLegend.new("Class/Section")
    x_legend.set_style('{font-size: 14px; color: #784016}')

    if params[:data_type].to_i == 1
      y_legend = YLegend.new("Present (%)")
    elsif params[:data_type].to_i == 2
      y_legend = YLegend.new("Absent (%)")
    elsif params[:data_type].to_i == 3
      y_legend = YLegend.new("Late (%)")
    else 
      y_legend = YLegend.new("Leave (%)")
    end
    y_legend.set_style('{font-size: 14px; color: #784016}')

    chart = OpenFlashChart.new
    chart.set_title(title)
    chart.y_axis = y_axis
    chart.x_axis = x_axis
    chart.y_legend = y_legend
    chart.x_legend = x_legend
    chart.set_bg_colour( '#DAF6DA' );

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
      @main_data_type = "Present"
    else
      @main_data_type = params[:student][:data_type]
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
      if batch_name.length == 0
        @batch_data = Rails.cache.fetch("batch_data_#{course_id}"){
          batches = Batch.find_by_course_id(course_id)
          batches
        }
      else
        @batch_data = Rails.cache.fetch("batch_data_#{course_id}_#{batch_name.parameterize("_")}"){
          batches = Batch.find_by_course_id_and_name(course_id, batch_name)
          batches
        }
      end 
      
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
      
      a_data_date = @report_data['att_graph_date'].values
      
      @att_data_string = a_data.join(",")
      @att_data_string_date = a_data_date.join(",")
      
      @graph = open_flash_chart_object(895, 450,
      "/intelligence/graph_for_attandence?att_data=#{@att_data_string}&att_data_string_date=#{@att_data_string_date}&graph_type=#{@graph_type}&data_type=#{@data_type}")

    end
    respond_to do |format|
      format.js { render :action => 'report_data' }
    end
  
  end
  def graph_for_attandence

     

    x_labels = []
    data_string = params[:att_data]
    data_array = data_string.split(",")
    
    
    data_string_date = params[:att_data_string_date]
    data_array_date = data_string_date.split(",")
    
    data = []
    t = 1
    l = 0
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
      x_labels << data_array_date[l]
      
#      if t==a_length
#        if params[:graph_type]=="Day"
#          x_labels << "Today"
#        elsif params[:graph_type]=="Week"
#          x_labels << "This Week"
#        else
#          x_labels << "This Month"
#        end
#      else
#        if t==1
#          x_labels << t.to_s+" "+params[:graph_type]
#        else
#          x_labels << t.to_s+" "+params[:graph_type]+"s"
#        end
#      end  
      t = t+1
      l = l+1
    end
    
    
    line = Line.new
  

    line.width = 4; line.colour = '#64B846'; line.dot_size = 10; line.values = data

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
    x_legend.set_style('{font-size: 14px; color: #784016; padding:5px;}')
    
    if params[:data_type].to_i == 1
      y_legend = YLegend.new("Present (%)")
    elsif params[:data_type].to_i == 2
      y_legend = YLegend.new("Absent (%)")
    elsif params[:data_type].to_i == 3
      y_legend = YLegend.new("Late (%)")
    else 
      y_legend = YLegend.new("Leave (%)")
    end
    
    y_legend.set_style('{font-size: 14px; color: #784016; padding:5px;}')

    chart = OpenFlashChart.new
    chart.set_title(title)
    chart.set_x_legend(x_legend)
    chart.set_y_legend(y_legend)
    chart.y_axis = y_axis
    chart.x_axis = x_axis

    chart.add_element(line)
    chart.set_bg_colour( '#DAF6DA' );

    render :text => chart.to_s
    

    
    
    
  end
  
  private
  
  
  def get_lessonplan_report_full(batch_id=false,batch_name=false,class_name=false,type="Day",date_used=false)
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
    date_used = date_used.to_date unless date_used.blank?
    if current_user.employee? or current_user.admin?
      api_uri = URI(api_endpoint + "api/syllabus/intelligence")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      if !batch_id.blank?
          request.set_form_data({"batch_id"=>batch_id,"date"=>date_used,"type"=>type,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      elsif !batch_name.blank? and !class_name.blank?
          request.set_form_data({"batch_name"=>batch_name,"date"=>date_used,"type"=>type,"class_name"=>class_name,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      elsif !batch_name.blank?
          request.set_form_data({"batch_name"=>batch_name,"date"=>date_used,"type"=>type,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      else
          request.set_form_data({"call_from_web"=>1,"date"=>date_used,"type"=>type,"user_secret" =>session[:api_info][0]['user_secret']})
      end 
     
      response = http.request(request)
      @student_response = JSON::parse(response.body)
    end
    
    @student_response
  end
  
  def get_lessonplan_report_full_teacher(department_id,sort_by,sort_type,time_range,date)
    require 'net/http'
    require 'uri'
    require "yaml"
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    date = date.to_date unless date.blank?
    if current_user.employee? or current_user.admin?
      api_uri = URI(api_endpoint + "api/syllabus/teacherintelligence")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })    
      request.set_form_data({"department_id"=>department_id,"sort_by"=>sort_by,"sort_type"=>sort_type,"time_range"=>time_range,"date"=>date,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      response = http.request(request)
      @student_response = JSON::parse(response.body)
    end 
    @student_response
  end
  
  
  def get_homework_report_full(batch_id=false,batch_name=false,class_name=false,type="Day",date_used=false)
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
    date_used = date_used.to_date unless date_used.blank?
    if current_user.employee? or current_user.admin?
      api_uri = URI(api_endpoint + "api/homework/homeworkintelligence")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      if !batch_id.blank?
          request.set_form_data({"batch_id"=>batch_id,"date"=>date_used,"type"=>type,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      elsif !batch_name.blank? and !class_name.blank?
          request.set_form_data({"batch_name"=>batch_name,"date"=>date_used,"type"=>type,"class_name"=>class_name,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      elsif !batch_name.blank?
          request.set_form_data({"batch_name"=>batch_name,"date"=>date_used,"type"=>type,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      else
          request.set_form_data({"call_from_web"=>1,"date"=>date_used,"type"=>type,"user_secret" =>session[:api_info][0]['user_secret']})
      end 
     
      response = http.request(request)
      @student_response = JSON::parse(response.body)
    end
    
    @student_response
  end
  
  
  
  
  def get_homework_report_full_teacher(department_id,sort_by,sort_type,time_range,date)
    require 'net/http'
    require 'uri'
    require "yaml"
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    date = date.to_date unless date.blank?
    if current_user.employee? or current_user.admin?
      api_uri = URI(api_endpoint + "api/homework/teacherintelligence")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })    
      request.set_form_data({"department_id"=>department_id,"sort_by"=>sort_by,"sort_type"=>sort_type,"time_range"=>time_range,"date"=>date,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      response = http.request(request)
      @student_response = JSON::parse(response.body)
    end
    
    @student_response
  end
  
  def get_classwork_report_full(batch_id=false,batch_name=false,class_name=false,type="Day",date_used=false)
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

    
    date_used = date_used.to_date unless date_used.blank?
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.employee? or current_user.admin?
      api_uri = URI(api_endpoint + "api/classwork/intelligence")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      if !batch_id.blank?
          request.set_form_data({"batch_id"=>batch_id,"date"=>date_used,"type"=>type,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      elsif !batch_name.blank? and !class_name.blank?
          request.set_form_data({"batch_name"=>batch_name,"date"=>date_used,"type"=>type,"class_name"=>class_name,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      elsif !batch_name.blank?
          request.set_form_data({"batch_name"=>batch_name,"date"=>date_used,"type"=>type,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      else
          request.set_form_data({"call_from_web"=>1,"date"=>date_used,"type"=>type,"user_secret" =>session[:api_info][0]['user_secret']})
      end 
     
      response = http.request(request)
      @student_response = JSON::parse(response.body)
    end
    
    @student_response
  end
  
  def get_classwork_report_full_teacher(department_id,sort_by,sort_type,time_range,date)
    require 'net/http'
    require 'uri'
    require "yaml"
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    date = date.to_date unless date.blank?
    if current_user.employee? or current_user.admin?
      api_uri = URI(api_endpoint + "api/classwork/teacherintelligence")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })    
      request.set_form_data({"department_id"=>department_id,"sort_by"=>sort_by,"sort_type"=>sort_type,"time_range"=>time_range,"date"=>date,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      response = http.request(request)
      @student_response = JSON::parse(response.body)
    end 
    @student_response
  end
  
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
    date_used = date_used.to_date unless date_used.blank?
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
  
  def get_report_crictical(batch_id=false,batch_name=false,class_name=false,type=1,limit=10)
    require 'net/http'
    require 'uri'
    require "yaml"

    
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.employee? or current_user.admin?
      api_uri = URI(api_endpoint + "api/calender/attendencecritical")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
      if !batch_id.blank?
          request.set_form_data({"batch_id"=>batch_id,"type"=>type,"limit"=>limit,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      elsif !batch_name.blank? and !class_name.blank?
          request.set_form_data({"batch_name"=>batch_name,"type"=>type,"limit"=>limit,"class_name"=>class_name,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      elsif !batch_name.blank?
          request.set_form_data({"batch_name"=>batch_name,"type"=>type,"limit"=>limit,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
      else
          request.set_form_data({"call_from_web"=>1,"type"=>type,"limit"=>limit,"user_secret" =>session[:api_info][0]['user_secret']})
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
     date_used = date_used.to_date unless date_used.blank?
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
  def get_exams_section_report(class_name,exam_name)
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.employee? or current_user.admin?
      api_uri = URI(api_endpoint + "api/report/getsectionreport")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
     request.set_form_data({"class_name"=>class_name,"exam_name"=>exam_name,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
    
     
      response = http.request(request)
      @exam_response = JSON::parse(response.body)
    end
  end
  
  def get_exams_class(class_name)
    require 'net/http'
    require 'uri'
    require "yaml"
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.employee? or current_user.admin?
      api_uri = URI(api_endpoint + "api/report/getexamclass")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
     request.set_form_data({"class_name"=>class_name,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
    
     
      response = http.request(request)
      @exam_response = JSON::parse(response.body)
    end
  end
  
  def get_exam_report(exam_id)
    require 'net/http'
    require 'uri'
    require "yaml"
    

    
 
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']

    if current_user.employee? or current_user.admin?
      api_uri = URI(api_endpoint + "api/report/gettermreportall")
      http = Net::HTTP.new(api_uri.host, api_uri.port)
      request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
     
     
          request.set_form_data({"id"=>exam_id,"call_from_web"=>1,"user_secret" =>session[:api_info][0]['user_secret']})
    
     
      response = http.request(request)
      @student_response = JSON::parse(response.body)
    end
    
    @student_response
  end
end
