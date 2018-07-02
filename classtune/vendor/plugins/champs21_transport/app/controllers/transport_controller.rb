class TransportController < ApplicationController
  before_filter :login_required
  before_filter :check_permission,:only=>[:index,:dash_board,:vehicle_report]
  before_filter :default_time_zone_present_time
  filter_access_to :all
  before_filter  :set_precision
  def dash_board
    
  end
  def search_ajax
    @transport = Transport.new
    if params[:option] == "student"
      if params[:query].length>= 2
        params[:query] = params[:query].gsub("+", " ")
        @students = Student.find(:all,
          :conditions => ["first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                            OR admission_no = ? OR (concat(first_name, \" \", last_name) LIKE ? OR batches.name LIKE ?  OR courses.course_name LIKE ? OR concat(batches.name, \" \", courses.course_name, \" \", courses.section_name) LIKE ? ) ",
            "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
            "#{params[:query]}", "#{params[:query]}", "#{params[:query]}", "#{params[:query]}", "#{params[:query]}%" ],
          :order => "batch_id asc,first_name asc",:include=>[{:batch=>:course},:transport]) unless params[:query] == ''
      else
        @students = Student.find(:all,
          :conditions => ["admission_no = ? " , params[:query]],
          :order => "batch_id asc,first_name asc",:include=>[{:batch=>:course},:transport]) unless params[:query] == ''
      end
      render :partial => "student_search_ajax"
    else
      if params[:query].length>= 2
        @employees = Employee.find(:all,
          :conditions => ["(first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                       OR employee_number = ? OR (concat(first_name, \" \", last_name) LIKE ? ))",
            "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
            "#{params[:query]}", "#{params[:query]}" ],
          :order => "employee_department_id asc,first_name asc",:include=>[:employee_department,:transport]) unless params[:query] == ''
      else
        @employees = Employee.find(:all,
          :conditions => ["(employee_number = ? )", "#{params[:query]}"],
          :order => "employee_department_id asc,first_name asc",:include=>[:employee_department,:transport]) unless params[:query] == ''
      end
      render :partial => "employee_search_ajax"
    end
  end

  def transport_details
    @vehicles = Vehicle.all
  end

  def pdf_report
    @transport = Transport.find_all_by_vehicle_id(params[:id])
    @vehicle = Vehicle.find_by_id(params[:id])
    render :pdf=>'pdf_report'
  end

  def ajax_transport_details
    unless params[:id].blank?
      @transport = Transport.find_all_by_vehicle_id(params[:id])
      @vehicle = Vehicle.find_by_id(params[:id])
      #@route = Route.find_by_main_route(params[@vehicle])
      render(:update) do |page|
        page.replace_html 'transport_details', :partial=>'ajax_transport_details'
      end
    else
      render(:update) do |page|
        page.replace_html 'transport_details', :text=>''
      end
    end
  end
  
  def add_transport
    if params[:user] == 'student'
      @user = Student.find params[:id]
    elsif params[:user] == 'employee'
      @user = Employee.find params[:id]
    end
    @transport = Transport.new
    @routes = Route.all( :conditions=>["main_route_id is NULL"])
    @vehicles = []
    @destination = []
    if request.post?
      @transport = Transport.new(params[:transport])
      @transport.receiver = @user
      if @transport.save
        flash[:notice] = "#{t('flash1')}"
        redirect_to :controller => :transport
      end
    end
  end

  def add_transport_all
    @user_ids = params[:transportuser][:user_ids] 
    if params[:user] == 'student'
      @user_data = Student.find_all_by_id(@user_ids)
    elsif params[:user] == 'employee'
      @user_data = Employee.find_all_by_id(@user_ids)
    end  
    @transport = Transport.new
    @routes = Route.all( :conditions=>["main_route_id is NULL"])
    @vehicles = []
    @destination = []
    if request.post? && params[:transport]
      @user_ids.each do |e|
        if params[:user] == 'student'
          @user = Student.find e
          @transport_delete = Transport.find_by_receiver_id_and_receiver_type(e,'Student')
          unless @transport_delete.nil?
            @transport_delete.destroy
          end  
        elsif params[:user] == 'employee'
          @user = Employee.find e
          @transport_delete = Transport.find_by_receiver_id_and_receiver_type(e,'Employee')
          unless @transport_delete.nil?
            @transport_delete.destroy
          end 
        end
      
        @transport = Transport.new(params[:transport])
        @transport.receiver = @user
        @transport.save
      end
       
      flash[:notice] = "#{t('flash1')}"
      redirect_to :controller => :transport

    end
  end

  def update_vehicle
    render :update do |page|
      unless params[:route].blank?
        @vehicles = Vehicle.find(:all,:conditions=>"main_route_id=#{params[:route]} and status='Active'")
        @destination = Route.find(:all,:conditions=>"id = #{params[:route]} OR main_route_id = #{params[:route]}") if params[:route].present?
        page.replace_html 'update_vehicle', :partial=>'update_vehicle'
        page.replace_html 'update_destination', :partial=>'update_destination'
      else
        @vehicles = []
        @destination = []
        @cost = ""
        page.replace_html 'update_vehicle', :partial=>'update_vehicle'
        page.replace_html 'update_destination', :partial=>'update_destination'
        page.replace_html 'seat_description', :text=>''
        page.replace_html 'fare', :partial=>'load_fare'
      end
    end
  end

  def load_fare
    @route = Route.find_by_id(params[:route])
    @cost = @route.cost
    render(:update) do |page|
      page.replace_html 'fare', :partial=>'load_fare'
    end
  end

  def seat_description
    @vehicle = Vehicle.find_by_id(params[:id])
    @no_of_seat = @vehicle.no_of_seats
    @allocated_seats = Transport.find_all_by_vehicle_id(@vehicle.id)
    @available_seats = @no_of_seat - @allocated_seats.count

    render(:update) do |page|
      page.replace_html 'seat_description', :partial=>'seat_description'
    end
  end

  def delete_transport
    @transport = Transport.find(params[:id])
    @transport.destroy
    flash[:notice] = "#{t('flash2')}"
    redirect_to :controller => 'transport', :action => 'transport_details'
  end

  def edit_transport
    @transport = Transport.find(params[:id])
    @routes = Route.all( :conditions=>["main_route_id IS NULL"])
    @vehicles = @transport.get_vehicles
    unless @transport.route.main_route_id.nil?
      @destination = Route.find(:all, :conditions=>"main_route_id = #{@transport.route.main_route_id} OR id = #{@transport.route.main_route_id} ")
    else
      @destination = Route.find(:all, :conditions=>"main_route_id = #{@transport.route.id} OR id = #{@transport.route.id} ")
    end
    if request.post?
      if params[:transport][:vehicle_id].nil?
        params[:transport][:vehicle_id]=""
      end
      if @transport.update_attributes(params[:transport])
        flash[:notice] = "#{t('flash3')}"
        redirect_to :controller => :transport
      end
    else
      #      flash[:warn_notice] = "<p>#{params[:user].humanize} already assigned with a vehicle.</p>"
    end
  end

  def student_transport_details
    @current_user = current_user
    @available_modules = Configuration.available_modules
    @student = Student.find(params[:id])
    @transport = nil
    transport = get_transport_data
    if transport['status']['code'] == 200
      @transport = transport['data']['transport']
    end
    
  end

  def employee_transport_details
    @current_user = current_user
    @new_reminder_count = Reminder.find_all_by_recipient(@current_user.id, :conditions=>"is_read = false")
    @available_modules = Configuration.available_modules
    @employee = Employee.find(params[:id])
    @transport = @employee.transport
    unless @transport.nil?
      @vehicle = @transport.vehicle
      @route = @transport.route
    end
  end
  
  def vehicle_report
    @sort_order=params[:sort_order]
    if @sort_order.nil?
      @vehicles=Vehicle.paginate(:select=>"vehicles.*,no_of_seats-count(transports.id) as available_seats",:conditions=>["status LIKE ?","active"],:joins=>[:transports],:group=>'vehicles.id',:per_page=>10,:page=>params[:page],:order=>'vehicle_no')
    else
      @vehicles=Vehicle.paginate(:select=>"vehicles.*,no_of_seats-count(transports.id) as available_seats",:conditions=>["status LIKE ?","active"],:joins=>[:transports],:group=>'vehicles.id',:per_page=>10,:page=>params[:page],:order=>@sort_order)
    end
    if request.xhr?
      render :update do |page|
        page.replace_html "information", :partial => "vehicle_details"
      end
    end
  end
  
  def vehicle_report_csv
    sort_order=params[:sort_order]
    if sort_order.nil?
      vehicles=Vehicle.all(:select=>"vehicles.*,no_of_seats-count(transports.id) as available_seats",:conditions=>["status LIKE ?","active"],:joins=>[:transports],:group=>'vehicles.id',:order=>'vehicle_no')
    else
      vehicles=Vehicle.all(:select=>"vehicles.*,no_of_seats-count(transports.id) as available_seats",:conditions=>["status LIKE ?","active"],:joins=>[:transports],:group=>'vehicles.id',:order=>sort_order)
    end
    csv_string=FasterCSV.generate do |csv|
      cols=["#{t('no_text')}","#{t('vehicle_no')}","#{t('route') }","#{t('no_of_seats') }","#{t('available_seats')}"]
      csv << cols
      vehicles.each_with_index do |s,i|
        col=[]
        col<< "#{i+1}"
        col<< "#{s.vehicle_no}"
        col<< "#{s.main_route.nil? ? t('deleted_route') : s.main_route.destination}"
        col<< "#{s.no_of_seats}"
        col<< "#{s.available_seats}"
        col=col.flatten
        csv<< col
      end
    end
    filename = "#{t('vehicle')}#{t('details')}- #{Time.now.to_date.to_s}.csv"
    send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
  end
  def single_vehicle_details
    @sort_order=params[:sort_order]
    if @sort_order.nil?
      @receivers=Transport.paginate(:select=>"transports.*,students.first_name,students.middle_name,students.last_name,students.admission_no,employees.first_name as emp_first_name ,employees.middle_name as emp_middle_name,employees.last_name as emp_last_name,employees.employee_number,routes.destination ,IF(transports.receiver_type='Student',students.first_name,employees.first_name) as receiver_name",:joins=>"LEFT OUTER JOIN `routes` ON `routes`.id = `transports`.route_id LEFT OUTER JOIN students on students.id=transports.receiver_id LEFT OUTER JOIN employees on employees.id=transports.receiver_id",:conditions=>{:vehicle_id=>params[:id]},:per_page=>15,:page=>params[:page],:order=>'receiver_name ASC')
    else
      @receivers=Transport.paginate(:select=>"transports.*,students.first_name,students.middle_name,students.last_name,students.admission_no,employees.first_name as emp_first_name ,employees.middle_name as emp_middle_name,employees.last_name as emp_last_name,employees.employee_number,routes.destination ,IF(transports.receiver_type='Student',students.first_name,employees.first_name) as receiver_name",:joins=>"LEFT OUTER JOIN `routes` ON `routes`.id = `transports`.route_id LEFT OUTER JOIN students on students.id=transports.receiver_id LEFT OUTER JOIN employees on employees.id=transports.receiver_id",:conditions=>{:vehicle_id=>params[:id]},:per_page=>15,:page=>params[:page],:order=>@sort_order)
    end
    if request.xhr?
      render :update do |page|
        page.replace_html "information", :partial => "single_vehicle_report"
      end
    end
  end
  def single_vehicle_details_csv
    parameters={:sort_order=>params[:sort_order],:vehicle_id=>params[:id]}
    model='transport'
    method='single_vehicle_details'
    csv_report=AdditionalReportCsv.find_by_model_name_and_method_name(model,method)
    if csv_report.nil?
      csv_report=AdditionalReportCsv.new(:model_name=>model,:method_name=>method,:parameters=>parameters)
      if csv_report.save
        Delayed::Job.enqueue(DelayedAdditionalReportCsv.new(csv_report.id))
      end
    else
      if csv_report.update_attributes(:parameters=>parameters,:csv_report=>nil)
        Delayed::Job.enqueue(DelayedAdditionalReportCsv.new(csv_report.id))
      end
    end
    flash[:notice]="#{t('csv_report_is_in_queue')}"
    redirect_to :controller=>:report,:action=>:csv_reports,:model=>model,:method=>method
  end
  
end

private
def get_transport_data
  require 'net/http'
  require 'uri'
  require "yaml"
 
  champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
  api_endpoint = champs21_api_config['api_url']
  
  form_data = {}
  form_data['user_secret'] = session[:api_info][0]['user_secret']
    
  uri = URI(api_endpoint + "api/transport")
  http = Net::HTTP.new(uri.host, uri.port)
  request = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded', 'Cookie' => session[:api_info][0]['user_cookie'] })
    
  if current_user.parent
    target = current_user.guardian_entry.current_ward_id      
    student = Student.find_by_id(target)
    form_data['student_id'] = student.id
  end
    
  request.set_form_data(form_data)
  response = http.request(request)

  return JSON::parse(response.body)
end