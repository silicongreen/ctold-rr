class EmpattendanceController < ApplicationController
  include ActionView::Helpers::TextHelper
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time
  
  
  def index
    require "yaml"
    @has_advance_attendance_report = false
    adv_attendance_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/adv_attendance_report.yml")['school']
    all_schools = adv_attendance_config['numbers'].split(",")
    current_school = MultiSchool.current_school.id
    
    if all_schools.include?(current_school.to_s)
      @has_advance_attendance_report = true
    end
    @date_today = @local_tzone_time.to_date.strftime("%Y-%m-%d")
  end
  
  def list_options
    @option_type = params[:option_type]
    @date_today = @local_tzone_time.to_date.strftime("%Y-%m-%d")
    render :update do |page|
      page.replace_html 'report_for', :partial => 'options_report_for'
    end
  end
  
  def campus_report_show
    require "yaml"
    @has_advance_attendance_report = false
    adv_attendance_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/adv_attendance_report.yml")['school']
    all_schools = adv_attendance_config['numbers'].split(",")
    current_school = MultiSchool.current_school.id
    
    if all_schools.include?(current_school.to_s)
      @has_advance_attendance_report = true
    end
    
    @departments = EmployeeDepartment.active
    @categories  = EmployeeCategory.active.find(:all,:order=>'name ASC')
    @positions   = EmployeePosition.active.find(:all,:order=>'name ASC')
    @grades      = EmployeeGrade.active.find(:all,:order=>'name ASC')
    unless params[:campus_report].nil? or params[:campus_report].empty? or params[:campus_report].blank?
      campus_report = params[:campus_report]
      @report_type = campus_report[:report_type]
      unless params[:attendance_report_for].nil? or params[:attendance_report_for].empty? or params[:attendance_report_for].blank?
        @report_for = params[:attendance_report_for];
      else
        @report_for = "1";
      end
      
      valid_req = true
      if params[:attendance_report_for].to_i == 2
        current_year = Date.today.year
        current_day = Date.today.day
        month = params[:attendance_report_month].to_i
        
        if month == 0
          valid_req = false
        else
          if ( month < 10 )
            mon = '0' + month.to_s
          else
            mon = month.to_s
          end
          current_day = "01"
          dat_str = current_day.to_s + '-' + mon + '-' + current_year.to_s
          d = Date.parse(dat_str)
          @report_from_date = d.beginning_of_month.strftime("%Y-%m-%d")
          @report_to_date = d.end_of_month.strftime("%Y-%m-%d")
        end
      else
        @report_from_date = campus_report[:attandence_date]
        if @report_for.to_i == 1
          @report_to_date = campus_report[:attandence_date]
        else
          unless campus_report[:attandence_to_date].nil? or campus_report[:attandence_to_date].empty? or campus_report[:attandence_to_date].blank?
            @report_to_date = campus_report[:attandence_to_date]
          else
            @report_to_date = campus_report[:attandence_date]
          end
        end
      end
      if valid_req
        @has_advance_attendance_report = false
        adv_attendance_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/adv_attendance_report.yml")['school']
        all_schools = adv_attendance_config['numbers'].split(",")
        current_school = MultiSchool.current_school.id

        if all_schools.include?(current_school.to_s)
          @has_advance_attendance_report = true
        end
        @attendance_report_for = params[:attendance_report_for]
        respond_to do |format|
          format.js { render :action => 'campus_attendance_employee' }
        end
      else
        render :update do |page|
          page.replace_html 'report_content', :text => "<div style=\"clear: both; margin-top: 10px;\"><p class=\"flash-msg\">Invalid Request</p></div>"
        end
      end
    else
      render :update do |page|
        page.replace_html 'report_content', :text => "<div style=\"clear: both; margin-top: 10px;\"><p class=\"flash-msg\">Invalid Request</p></div>"
      end
    end
    
  end
  
  def campus_report_view
    require 'json'
    require "yaml"
    
    @has_advance_attendance_report = false
    adv_attendance_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/adv_attendance_report.yml")['school']
    all_schools = adv_attendance_config['numbers'].split(",")
    current_school = MultiSchool.current_school.id
    
    if all_schools.include?(current_school.to_s)
      @has_advance_attendance_report = true
    end
    
    unless adv_attendance_config['order_by_custom_' + MultiSchool.current_school.id.to_s].nil?
      @order_by_custom = adv_attendance_config['order_by_custom_' + MultiSchool.current_school.id.to_s]
    else
      @order_by_custom = false
    end
    
    unless adv_attendance_config['hide_blank_' + MultiSchool.current_school.id.to_s].nil?
      @hide_blank = adv_attendance_config['hide_blank_' + MultiSchool.current_school.id.to_s]
    else
      @hide_blank = false
    end

    @date_today = @local_tzone_time.to_date
    unless params[:report_type].nil? or params[:report_type].empty? or params[:report_type].blank?
      draw = params[:draw]
    else
      draw = "5"
    end
    
    unless params[:report_type].nil? or params[:report_type].empty? or params[:report_type].blank?
      @report_type = params[:report_type]
      @report_for = params[:report_for]
      @report_date = params[:report_date_from]
      @report_date_from = params[:report_date_from]
      @report_date_to = params[:report_date_to]
      
      @report_date_from = @report_date_from.to_date.strftime("%Y-%m-%d")
      @report_date_to = @report_date_to.to_date.strftime("%Y-%m-%d")
      @year = @report_date_from.to_date.strftime("%Y")
      @month = @report_date_from.to_date.strftime("%m")
     
      events = Event.find(:all, :select => "title, start_date, end_date", :conditions => ["( (start_date BETWEEN ? AND ?) OR (end_date BETWEEN ? AND ?) OR (start_date <= ? AND end_date >= ?) ) AND is_holiday = 1 AND is_published = 1", @report_date_from, @report_date_to, @report_date_from, @report_date_to,@report_date_from, @report_date_to])
      event_dates = []
      p = 0
      unless events.nil? or events.empty?
        events.each do |e|
          sdate = e.start_date.to_date
          edate = e.end_date.to_date
          (sdate..edate).each do |d|
            if d >= @report_date_from.to_date and d <= @report_date_to.to_date
              unless event_dates.include?(d.to_date.strftime("%Y-%m-%d"))
                event_dates[p] = d.to_date.strftime("%Y-%m-%d")
                p = p + 1
              end
            end
          end
        end
      end 
      
      event_dates_count = event_dates.length
      
      @all_record = params[:all_record]
      @employee_department_id = params[:employee_department_id]
      @category_id = params[:category_id]
      @position_id = params[:position_id]
      @grade_id = params[:grade_id]
      
      orders = params[:order]
      
      search = params[:search]
      
      columns = params[:columns]
      
      #orders.each_pair { |key, value| orders[key] = value.to_a }
      search.each_pair { |key, value| search[key] = value.to_a }
      fields = ['employee_info', 'employees.employee_department_id', 'employees.employee_category_id', 'employees.employee_position_id', 'employees.employee_position_id']
      order_field = []
      order_dir = []
      l = 0
      o = 0
      orders.keys.each do |key|
        orders[key].each do |k, v|
          if k == "dir"
            order_dir[l] = v
            l += 1
          elsif k == "column"  
            order_field[o] = fields[v.to_i]
            o += 1
          end
        end
      end
      order_str = ""
      m = 0
      order_field.each do |field|
        order_str += field + " " + order_dir[m] + ", "
        m += 1
      end
      order_str = order_str[0..order_str.length - 3]
      
      is_second_filter_enable = false
      filter_field = []
      filter_data = []
      a = 0
      columns.keys.each do |key|
        columns[key].each do |k, v|
          if k == "search"
            columns[key][k].each do |k1,v1|
              if k1 == "value"
                unless v1.blank?
                  is_second_filter_enable = true
                  filter_field[a] = fields[key.to_i]
                  filter_data[a] = v1
                  a += 1
                end
              end
            end
          end
        end
      end
      
      if @order_by_custom
        order_str = "employee_positions.order_by asc"
      end
      
      a = 0
      if is_second_filter_enable == false and draw.to_i == 1
        if @employee_department_id.to_i > 0
            is_second_filter_enable = true
            filter_field[a] = 'employee_department_id'
            filter_data[a] = @employee_department_id
            a += 1
        end
        if @category_id.to_i > 0
            is_second_filter_enable = true
            filter_field[a] = 'employee_category_id'
            filter_data[a] = @category_id
            a += 1
        end
        if @position_id.to_i > 0
            is_second_filter_enable = true
            filter_field[a] = 'employee_position_id'
            filter_data[a] = @position_id
            a += 1
        end
        if @grade_id.to_i > 0
            is_second_filter_enable = true
            filter_field[a] = 'employee_position_id'
            filter_data[a] = @grade_id
        end
      end
      search_value = search["value"]
      
      per_page = params[:length]
      start = params[:start]
      
      page = ( start.to_i / per_page.to_i ) + 1
      
      b_filtered_search = false
      has_filter = false
      conditions = ""
      filters_conditions = ""
      unless search_value.nil? or search_value.empty? or search_value.blank?
        conditions = " ((employee_number LIKE '%" + search_value.to_s + "%') OR (first_name LIKE '%" + search_value.to_s + "%')) "
        filters_conditions = " ((employee_number LIKE '%" + search_value.to_s + "%') OR (first_name LIKE '%" + search_value.to_s + "%')) "
        has_filter = true
        if is_second_filter_enable
            conditions += " AND "
            filters_conditions += " AND "
            has_filter = true
            b = 0
            filter_field.each do |field|
              conditions += " (" + field + " = '" + filter_data[b] + "') "
              filters_conditions += " (" + field + " = '" + filter_data[b] + "') "
              has_filter = true
              if (filter_field.length - 1) > b
                conditions += " AND "
                filters_conditions += " AND "
                has_filter = true
              end
              b += 1
            end
        end
        b_filtered_search = true
      else
        if is_second_filter_enable
            b = 0
            filter_field.each do |field|
              conditions += " (" + field + " = '" + filter_data[b] + "') "
              filters_conditions += " (" + field + " = '" + filter_data[b] + "') "
              has_filter = true
              if (filter_field.length - 1) > b
                conditions += " AND "
                filters_conditions += " AND "
                has_filter = true
              end
              b += 1
            end
            b_filtered_search = true
        end
      end
      
      unless @report_type.to_i == 3 or @report_type.to_i == 4
        unless @report_type.to_i == 1
          if b_filtered_search == true
            conditions += " AND "
          end
          @hide_blank  = false
          if has_filter
            conditions += "  user_id NOT IN (select card_attendance.user_id from card_attendance INNER JOIN employees ON card_attendance.profile_id = employees.id where " + filters_conditions + " AND card_attendance.date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and card_attendance.type = 1 and card_attendance.school_id = '"+MultiSchool.current_school.id.to_s+"') "
          else
            conditions += "  user_id NOT IN (select card_attendance.user_id from card_attendance INNER JOIN employees ON card_attendance.profile_id = employees.id where card_attendance.date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and card_attendance.type = 1 and card_attendance.school_id = '"+MultiSchool.current_school.id.to_s+"') "
          end
          b_filtered_search = true
        end  
        if @hide_blank
          if b_filtered_search == true
            conditions += " AND "
          end
          already_blank_filter  = true
          if has_filter
            conditions += "  employees.user_id IN (select card_attendance.user_id from card_attendance INNER JOIN employees ON card_attendance.profile_id = employees.id where " + filters_conditions + " AND card_attendance.date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and card_attendance.type = 1 and card_attendance.school_id = '"+MultiSchool.current_school.id.to_s+"' GROUP BY card_attendance.user_id) or employees.id IN (select employee_attendances.employee_id from employee_attendances INNER JOIN employees ON employee_attendances.employee_id = employees.id where " + filters_conditions + " AND employee_attendances.attendance_date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and employee_attendances.school_id = '"+MultiSchool.current_school.id.to_s+"' GROUP BY employee_attendances.employee_id) "
          else
            conditions += "  employees.user_id IN (select card_attendance.user_id from card_attendance INNER JOIN employees ON card_attendance.profile_id = employees.id where card_attendance.date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and card_attendance.type = 1 and card_attendance.school_id = '"+MultiSchool.current_school.id.to_s+"' GROUP BY card_attendance.user_id) or employees.id IN (select employee_attendances.employee_id from employee_attendances INNER JOIN employees ON employee_attendances.employee_id = employees.id where employee_attendances.attendance_date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and employee_attendances.school_id = '"+MultiSchool.current_school.id.to_s+"' GROUP BY employee_attendances.employee_id) "
          end
          b_filtered_search = true
        end 
        if b_filtered_search
          employees_length = Employee.count(:conditions => conditions)
          if @order_by_custom
            @employees = Employee.paginate(:conditions => conditions,:select => "id,user_id, concat(  first_name,' ', last_name )  as employee_info, employee_department_id, '' as in_time, '' as out_time, '' as stat", :page => page.to_i, :per_page => per_page.to_i,:order=>""  + order_str, :include => :employee_position)
          else
            @employees = Employee.paginate(:conditions => conditions,:select => "id,user_id, concat(  first_name,' ', last_name )  as employee_info, employee_department_id, '' as in_time, '' as out_time, '' as stat", :page => page.to_i, :per_page => per_page.to_i,:order=>""  + order_str)
          end
          recordsFiltered = employees_length
        else  
          employees_length = Employee.count
          if @order_by_custom
            @employees = Employee.paginate(:select => "id,user_id, concat(  first_name,' ', last_name )  as employee_info, employee_department_id, '' as in_time, '' as out_time, '' as stat", :page => page.to_i, :per_page => per_page.to_i,:order=>""  + order_str, :include => :employee_position)
          else
            @employees = Employee.paginate(:select => "id,user_id, concat(  first_name,' ', last_name )  as employee_info, employee_department_id, '' as in_time, '' as out_time, '' as stat", :page => page.to_i, :per_page => per_page.to_i,:order=>""  + order_str)
          end
          recordsFiltered = employees_length
        end
      else
        if @report_type.to_i == 3
          if b_filtered_search
            conditions += " AND "
          end
          @employees_all = Employee.find(:all, :conditions=>conditions + " date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and type = 1", :select => "employees.id,employees.user_id, concat( employees.first_name,' ',employees.last_name )  as employee_info, employees.employee_department_id, '' as in_time, '' as out_time, '' as stat", :joins => "INNER JOIN card_attendance ON employees.user_id = card_attendance.user_id", :group => "employees.user_id")
          if @order_by_custom
            @employees = Employee.paginate(:conditions=>conditions + " date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and type = 1", :select => "employees.id,employees.user_id, concat( employees.first_name,' ',employees.last_name )  as employee_info, employees.employee_department_id, '' as in_time, '' as out_time, '' as stat", :joins => "INNER JOIN card_attendance ON employees.user_id = card_attendance.user_id", :page => page.to_i, :per_page => per_page.to_i,:order=>""  + order_str, :group => "employees.user_id", :include => :employee_position)
          else
            @employees = Employee.paginate(:conditions=>conditions + " date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and type = 1", :select => "employees.id,employees.user_id, concat( employees.first_name,' ',employees.last_name )  as employee_info, employees.employee_department_id, '' as in_time, '' as out_time, '' as stat", :joins => "INNER JOIN card_attendance ON employees.user_id = card_attendance.user_id", :page => page.to_i, :per_page => per_page.to_i,:order=>""  + order_str, :group => "employees.user_id")
          end
          emp_ids = @employees_all.map(&:user_id).uniq
          employees_length = emp_ids.length
          recordsFiltered = employees_length
        else
          if b_filtered_search
            conditions += " AND "
          end
          @employees_all = Employee.find(:all, :conditions=>conditions + " date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and type = 1", :select => "employees.id,employees.user_id, concat( employees.first_name,' ',employees.last_name )  as employee_info, employees.employee_department_id, '' as in_time, '' as out_time, '' as stat", :joins => "INNER JOIN card_attendance ON employees.user_id = card_attendance.user_id", :group => "employees.user_id")
          @employees = @employees_all
          emp_ids = @employees_all.map(&:user_id).uniq
          employees_length = emp_ids.length
          recordsFiltered = employees_length
          
        end  
      end
      num_weekdays = [0,1,2,3,4,5,6]
      employess_id = @employees.map(&:user_id)
      data = []
      employee_department_ids = []
      department_names = []
      unless @employees.nil? or @employees.empty?
       
        if @report_for.to_i == 1
          @cardAttendances = CardAttendance.all(:select=>'user_id, time',:conditions=>"date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and type = 1 and user_id in (" + employess_id.join(",") + ")", :group => "time")
        elsif @report_for.to_i == 2 or @report_for.to_i == 3
          @cardAttendances = CardAttendance.find(:all, :select=>'user_id, count( DISTINCT date ) as count_present',:conditions=>"date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and type = 1 and user_id in (" + employess_id.join(",") + ")", :group => "user_id")
        end
      
        k = 0;
        m = 0
        
        @employees.each do |employee|
          unless employee_department_ids.include?(employee.employee_department_id)
            dept = EmployeeDepartment.find employee.employee_department_id
            employee_department_ids[m] = employee.employee_department_id
            department_names[m] = dept.name
            dept_name = dept.name
            m += 1
          else
            t = employee_department_ids.index(employee.employee_department_id)
            dept_name = department_names[t]
          end
          emp_id = employee.user_id
          cardAttendance = @cardAttendances.select{ |s| s.user_id == employee.user_id}
          populate_emp = true
          if cardAttendance.nil? or cardAttendance.empty? or cardAttendance.blank? 
              found_leave_or_absent = false
              if @report_for.to_i == 1
                in_time = ' - '
                out_time = ' - '
                time_diff = ' - '
                is_late = ' - '
                late = ' - '
                absent = ' - '
                leave = ' - '
                leave_for = ' - '
                
                dt = @report_date_from.to_date.strftime("%Y-%m-%d")
              
                emp_attendance = EmployeeAttendance.find(:first, :conditions=>"attendance_date = '" + dt + "' and employee_id = " + employee.id.to_s + "")
                unless emp_attendance.nil? or emp_attendance.blank?
                  unless  emp_attendance.employee_leave_type_id.nil? or emp_attendance.employee_leave_type_id.empty? or emp_attendance.employee_leave_type_id.blank? 
                    found_leave_or_absent = true
                    leave_types = EmployeeLeaveType.find(emp_attendance.employee_leave_type_id)
                    absent = '-'
                    leave = '&#10004;'
                    leave_for = leave_types.name
                  else  
                    found_leave_or_absent = true
                    absent = '&#10004;'
                    leave = ' - '
                    leave_for = ' - '
                  end
                else
                  absent = ' - '
                  leave = ' - '
                  leave_for = ' - '
                end
              
              elsif @report_for.to_i == 2 or @report_for.to_i == 3
                total_present = ' - '
                total_absent = ' - '
                total_late = ' - '
                total_leave = ' - '
              end
              
              if @hide_blank and  !found_leave_or_absent
                populate_emp = false
              end
          else 
            is_late = ' - '
            @employee_setting = EmployeeSetting.find_by_employee_id(employee.id)
            if cardAttendance.length == 1
              unless @employee_setting.blank?
                unless @employee_setting.weekdays.blank? or @employee_setting.weekdays.nil? or @employee_setting.weekdays.empty?
                  emp_weekdays = @employee_setting.weekdays.split(",").map(&:to_i)
                  week_off_day = num_weekdays - emp_weekdays
                  if @report_date_to.to_date > Date.today
                    a_week_off_days = (@report_date_from.to_date..@report_date_to.to_date).to_a.select {|l| week_off_day.include?(l.wday) && l <= Date.today && !event_dates.map{|c| c.to_date}.include?(l)}.map{|l| l.to_date.strftime("%Y-%m-%d")}
                  else
                    a_week_off_days = (@report_date_from.to_date..@report_date_to.to_date).to_a.select {|l| week_off_day.include?(l.wday) && l <= @report_date_to.to_date && !event_dates.map{|c| c.to_date}.include?(l)}.map{|l| l.to_date.strftime("%Y-%m-%d")}
                  end
                  num_week_off_days = a_week_off_days.length
                else
                  emp_weekdays = WeekdaySet.default_weekdays.to_a.map{|l| l[0]}.map(&:to_i)
                  week_off_day = num_weekdays - emp_weekdays
                  if @report_date_to.to_date > Date.today
                    a_week_off_days = (@report_date_from.to_date..@report_date_to.to_date).to_a.select {|l| week_off_day.include?(l.wday) && l <= Date.today && !event_dates.map{|c| c.to_date}.include?(l)}.map{|l| l.to_date.strftime("%Y-%m-%d")}
                  else
                    a_week_off_days = (@report_date_from.to_date..@report_date_to.to_date).to_a.select {|l| week_off_day.include?(l.wday) && l <= @report_date_to.to_date && !event_dates.map{|c| c.to_date}.include?(l)}.map{|l| l.to_date.strftime("%Y-%m-%d")}
                  end
                  num_week_off_days = a_week_off_days.length
                end
                if @report_for.to_i == 1
                  if cardAttendance[0]['time'].to_time.strftime("%H%M").to_i > @employee_setting.start_time.strftime("%H%M").to_i
                    is_late = 'Late'
                    late = '&#10004;'
                  else
                    is_late = 'On Time'
                  end  
                elsif @report_for.to_i == 2 or @report_for.to_i == 3
                  in_ofc_time = @employee_setting.start_time.strftime("%H:%M:%S")
                  lateAttendances = CardAttendance.all(:select=>'user_id',:conditions=>"date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and type = 1 and user_id = " + employee.user_id.to_s + "", :group => "date", :having => "min( time ) > '" + in_ofc_time + "'")
                  total_late = lateAttendances.length
                end
              else
                total_late = 0
                emp_weekdays = WeekdaySet.default_weekdays.to_a.map{|l| l[0]}.map(&:to_i)
                week_off_day = num_weekdays - emp_weekdays
                if @report_date_to.to_date > Date.today
                  a_week_off_days = (@report_date_from.to_date..@report_date_to.to_date).to_a.select {|l| week_off_day.include?(l.wday) && l <= Date.today && !event_dates.map{|c| c.to_date}.include?(l)}.map{|l| l.to_date.strftime("%Y-%m-%d")}
                else
                  a_week_off_days = (@report_date_from.to_date..@report_date_to.to_date).to_a.select {|l| week_off_day.include?(l.wday) && l <= @report_date_to.to_date && !event_dates.map{|c| c.to_date}.include?(l)}.map{|l| l.to_date.strftime("%Y-%m-%d")}
                end
                num_week_off_days = a_week_off_days.length
              end
              
              if @report_for.to_i == 1
                in_time = cardAttendance[0]['time'].strftime("%I:%M %p")
                out_time = cardAttendance[0]['time'].strftime("%I:%M %p")
                if @date_today == @report_date.to_date
                  time_diff = ' In Office '
                else
                  time_diff = ' - '
                end
              elsif @report_for.to_i == 2 or @report_for.to_i == 3  
                total_present = cardAttendance[0].count_present
                cardAttendancesDate = CardAttendance.find(:all, :select=>'date',:conditions=>"date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and type = 1 and profile_id = " + employee.id.to_s + "", :group => "date").map(&:date).map{|l| l.to_date.strftime("%Y-%m-%d")}
                
                #Check IF Attendance exists on Weekend days
                cardAttendancesInWeekendDay = 0
                unless a_week_off_days.blank?
                  cardAttendancesInWeekendDay = CardAttendance.find(:all, :select=>'date',:conditions=>"date IN (" + a_week_off_days.map{ |l| "'" + l + "'" }.join(",") + ") and type = 1 and profile_id = " + employee.id.to_s + "", :group => "date").size
                end
                if  cardAttendancesInWeekendDay.to_i < 0
                  cardAttendancesInWeekendDay = 0
                end
                num_week_off_days = num_week_off_days - cardAttendancesInWeekendDay
                
                #Check IF Attendance exists on Event days
                cardAttendancesInEventDay = 0
                unless event_dates.blank?
                  cardAttendancesInEventDay = CardAttendance.find(:all, :select=>'date',:conditions=>"date IN (" + event_dates.map{ |l| "'" + l + "'" }.join(",") + ") and type = 1 and profile_id = " + employee.id.to_s + "", :group => "date").size
                end
                if  cardAttendancesInEventDay.to_i < 0
                  cardAttendancesInEventDay = 0
                end
                event_dates_count = event_dates_count - cardAttendancesInEventDay
                
                #Leave or Absent count in the given date (Date From to Date TO)
                leave_n_absent_count = EmployeeAttendance.find(:all, :conditions=>"attendance_date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and employee_id = " + employee.id.to_s + " and attendance_date NOT IN (" + cardAttendancesDate.map{ |l| "'" + l + "'" }.join(",") + ")").size
                
                #Check IF Absent/Leave exists on Weekend days
                leave_n_absent_weekendday_count = 0
                unless a_week_off_days.blank?
                  leave_n_absent_weekendday_count = EmployeeAttendance.find(:all, :conditions=>"employee_id = " + employee.id.to_s + " and attendance_date IN (" + a_week_off_days.map{ |l| "'" + l + "'" }.join(",") + ")").size
                end
                if  leave_n_absent_weekendday_count.to_i < 0
                  leave_n_absent_weekendday_count = 0
                end
                num_week_off_days_remaining = num_week_off_days - leave_n_absent_weekendday_count
                
                #Check IF Absent/Leave exists on Event days
                leave_n_absent_events_count = 0
                unless event_dates.blank?
                  leave_n_absent_events_count = EmployeeAttendance.find(:all, :conditions=>"employee_id = " + employee.id.to_s + " and attendance_date IN (" + event_dates.map{ |l| "'" + l + "'" }.join(",") + ")").size
                end
                if  leave_n_absent_events_count.to_i < 0
                  leave_n_absent_events_count = 0
                end
                num_event_days_remaining = event_dates_count - leave_n_absent_events_count
                
                #Calculate the Leave and Absent Count (Add the remaining Weekend and Event date)
                leave_n_absent_count = leave_n_absent_count + num_week_off_days_remaining
                leave_n_absent_count = leave_n_absent_count + num_event_days_remaining
                
                leave_types = EmployeeLeaveType.find(:all, :conditions => "status = true")
                leave_count = EmployeeLeave.find_all_by_employee_id(employee,:joins=>:employee_leave_type,:conditions=>"status = true")
                total_leaves = 0
                leave_types.each do |lt|
                  leave_count = EmployeeAttendance.find(:all, :conditions=>"attendance_date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and employee_id = " + employee.id.to_s + " AND employee_leave_type_id = " + lt.id.to_s).size
                  total_leaves = total_leaves + leave_count
                end
                total_leave = total_leaves
                total_absent = leave_n_absent_count.to_i - ( total_leave.to_i + num_week_off_days.to_i + event_dates_count.to_i)
                if total_absent.to_i  < 0
                  total_absent = 0
                end
              end
            else  
                if @report_for.to_i == 1
                  cardAttendance = cardAttendance.sort_by {|c| c['time']  unless c.blank?}
                  in_time = cardAttendance[0]['time'].strftime("%I:%M %p")
                  unless @employee_setting.blank?
                    if cardAttendance[0]['time'].to_time.strftime("%H%M").to_i > @employee_setting.start_time.strftime("%H%M").to_i
                      is_late = 'Late'
                      late = '&#10004;'
                    else
                      is_late = 'On Time'
                    end  
                  end
                  out_time = cardAttendance[cardAttendance.length - 1]['time'].strftime("%I:%M %p")
                  time_diff = time_diff(cardAttendance[0]['time'], cardAttendance[cardAttendance.length - 1]['time'])
              elsif @report_for.to_i == 2 or @report_for.to_i == 3
                unless @employee_setting.blank?
                    in_ofc_time = @employee_setting.start_time.strftime("%H:%M:%S")
                    lateAttendances = CardAttendance.all(:select=>'user_id',:conditions=>"date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and type = 1 and user_id = " + employee.user_id.to_s + "", :group => "date", :having => "min( time ) > '" + in_ofc_time + "'")
                    total_late = lateAttendances.length
                end
                total_present = cardAttendance[0].count_present
                
                cardAttendancesDate = CardAttendance.find(:all, :select=>'date',:conditions=>"date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and type = 1 and profile_id = " + employee.id.to_s + "", :group => "date").map(&:date).map{|l| l.to_date.strftime("%Y-%m-%d")}
                
                #Check IF Attendance exists on Weekend days
                cardAttendancesInWeekendDay = 0
                unless a_week_off_days.blank?
                  cardAttendancesInWeekendDay = CardAttendance.find(:all, :select=>'date',:conditions=>"date IN (" + a_week_off_days.map{ |l| "'" + l + "'" }.join(",") + ") and type = 1 and profile_id = " + employee.id.to_s + "", :group => "date").size
                end
                if  cardAttendancesInWeekendDay.to_i < 0
                  cardAttendancesInWeekendDay = 0
                end
                num_week_off_days = num_week_off_days - cardAttendancesInWeekendDay
                
                #Check IF Attendance exists on Event days
                cardAttendancesInEventDay = 0
                unless event_dates.blank?
                  cardAttendancesInEventDay = CardAttendance.find(:all, :select=>'date',:conditions=>"date IN (" + event_dates.map{ |l| "'" + l + "'" }.join(",") + ") and type = 1 and profile_id = " + employee.id.to_s + "", :group => "date").size
                end
                if  cardAttendancesInEventDay.to_i < 0
                  cardAttendancesInEventDay = 0
                end
                event_dates_count = event_dates_count - cardAttendancesInEventDay
                
                #Leave or Absent count in the given date (Date From to Date TO)
                leave_n_absent_count = EmployeeAttendance.find(:all, :conditions=>"attendance_date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and employee_id = " + employee.id.to_s + " and attendance_date NOT IN (" + cardAttendancesDate.map{ |l| "'" + l + "'" }.join(",") + ")").size
                
                #Check IF Absent/Leave exists on Weekend days
                leave_n_absent_weekendday_count = 0
                unless a_week_off_days.blank?
                  leave_n_absent_weekendday_count = EmployeeAttendance.find(:all, :conditions=>"employee_id = " + employee.id.to_s + " and attendance_date IN (" + a_week_off_days.map{ |l| "'" + l + "'" }.join(",") + ")").size
                end
                if  leave_n_absent_weekendday_count.to_i < 0
                  leave_n_absent_weekendday_count = 0
                end
                num_week_off_days_remaining = num_week_off_days - leave_n_absent_weekendday_count
                
                #Check IF Absent/Leave exists on Event days
                leave_n_absent_events_count = 0
                unless event_dates.blank?
                  leave_n_absent_events_count = EmployeeAttendance.find(:all, :conditions=>"employee_id = " + employee.id.to_s + " and attendance_date IN (" + event_dates.map{ |l| "'" + l + "'" }.join(",") + ")").size
                end
                if  leave_n_absent_events_count.to_i < 0
                  leave_n_absent_events_count = 0
                end
                num_event_days_remaining = event_dates_count - leave_n_absent_events_count
                
                #Calculate the Leave and Absent Count (Add the remaining Weekend and Event date)
                leave_n_absent_count = leave_n_absent_count + num_week_off_days_remaining
                leave_n_absent_count = leave_n_absent_count + num_event_days_remaining
                
                leave_types = EmployeeLeaveType.find(:all, :conditions => "status = true")
                leave_count = EmployeeLeave.find_all_by_employee_id(employee,:joins=>:employee_leave_type,:conditions=>"status = true")
                total_leaves = 0
                leave_types.each do |lt|
                  leave_count = EmployeeAttendance.find(:all, :conditions=>"attendance_date BETWEEN '" + @report_date_from + "' and '" + @report_date_to + "' and employee_id = " + employee.id.to_s + " AND employee_leave_type_id = " + lt.id.to_s).size
                  total_leaves = total_leaves + leave_count
                end
                total_leave = total_leaves
                total_absent = leave_n_absent_count.to_i - ( total_leave.to_i + num_week_off_days.to_i + event_dates_count.to_i)
                if total_absent.to_i  < 0
                  total_absent = 0
                end
              end
            end
          end
          @emp = Employee.find(employee.id)
          employee_image = "<img src='/images/HR/default_employee.png' width='100px' />"
          if @emp.photo.file?
            employee_image = "<img src='"+@emp.photo.url+"' width='100px' />"
          end
          
          if populate_emp
            if @report_for.to_i == 1
              if @report_type.to_i != 4 or is_late == "Late" 
                if !employee.blank? and !@month.blank? and !@year.blank? and !@emp.full_name.blank?
                  if @has_advance_attendance_report
                    emp = {:employee_image => employee_image,:employee_info => "<a href='/employee_attendance/card_attendance_pdf?employee_id=" + employee.id.to_s + "&month=" + @month.to_s + "&year="+@year.to_s+"' target='_blank'>" + @emp.full_name + "<a/>", :department => dept_name, :in_time => in_time, :out_time => out_time, :status => time_diff,:late => late, :absent =>  absent, :leave => leave}
                  else  
                    emp = {:employee_image => employee_image,:employee_info => "<a href='/employee_attendance/card_attendance_pdf?employee_id=" + employee.id.to_s + "&month=" + @month.to_s + "&year="+@year.to_s+"' target='_blank'>" + @emp.full_name + "<a/>", :department => dept_name, :in_time => in_time, :out_time => out_time, :status => time_diff,:late => is_late }
                  end
                  data[k] = emp
                  k += 1
                end
              end
            else
              if !employee.blank? and !@month.blank? and !@year.blank? and !@emp.full_name.blank?
                emp = {:employee_image => employee_image,:employee_info => "<a href='/employee_attendance/card_attendance_pdf?employee_id=" + employee.id.to_s + "&month=" + @month.to_s + "&year="+@year.to_s+"' target='_blank'>" + @emp.full_name + "<a/>", :department => dept_name, :total_present => total_present, :total_absent => total_absent, :total_late => total_late,:total_leave => total_leave }
                data[k] = emp
                k += 1
              end
            end
          end
        end
      end
      data_hash = {:draw => draw, :recordsTotal => employees_length, :recordsFiltered => recordsFiltered, :data => data}
      @data = JSON.generate(data_hash)
      

    else
      data = []
      data_hash = {:draw => draw, :recordsTotal => 0, :recordsFiltered => 0, :data => data}
      @data = JSON.generate(data_hash)
    end
  end
   
  private
  
  def time_diff(start_time, end_time)
    seconds_diff = (start_time - end_time).to_i.abs

    hours = seconds_diff / 3600
    seconds_diff -= hours * 3600

    minutes = seconds_diff / 60
    seconds_diff -= minutes * 60

    seconds = seconds_diff

    #if minutes.to_i == 0
    #  "#{hours.to_s.rjust(2, '0')} Hours"
    #else  
    #  "#{hours.to_s.rjust(2, '0')} Hours,  #{minutes.to_s.rjust(2, '0')} Minutes"
    #end
    if minutes.to_i == 0
      "#{hours.to_s.rjust(2, '0')}:00"
    else  
      "#{hours.to_s.rjust(2, '0')} :#{minutes.to_s.rjust(2, '0')}"
    end
  end
  
end
