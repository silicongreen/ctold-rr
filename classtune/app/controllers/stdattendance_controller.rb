class StdattendanceController < ApplicationController
  include ActionView::Helpers::TextHelper
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time
  
  
  def index
    @date_today = @local_tzone_time.to_date.strftime("%Y-%m-%d")
  end
  def campus_report_show
    @batches = Batch.active
    unless params[:campus_report].nil? or params[:campus_report].empty? or params[:campus_report].blank?
      campus_report = params[:campus_report]
      @report_type = campus_report[:report_type]
      @report_date = campus_report[:attandence_date]
      respond_to do |format|
        format.js { render :action => 'campus_attendance_student' }
      end

    else
      render :update do |page|
        page.replace_html 'report_content', :text => "Invalid Request"
      end
    end
    
  end
  
  def campus_report_view
    require 'json'
    @date_today = @local_tzone_time.to_date
    unless params[:report_type].nil? or params[:report_type].empty? or params[:report_type].blank?
      draw = params[:draw]
    else
      draw = "5"
    end
    
    unless params[:report_type].nil? or params[:report_type].empty? or params[:report_type].blank?
      @report_type = params[:report_type]
      @report_date = params[:report_date]
      
      @report_date_from = @report_date
      @date_today = @report_date
      @year = @report_date.to_date.strftime("%Y")
      @month = @report_date.to_date.strftime("%m")
     
      
      @all_record = params[:all_record]
      @batch_id = params[:batch_id]
      
      orders = params[:order]
      
      search = params[:search]
      
      columns = params[:columns]
      
      #orders.each_pair { |key, value| orders[key] = value.to_a }
      search.each_pair { |key, value| search[key] = value.to_a }
      fields = ['student_info', 'batch_id']
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
      
      a = 0
      if is_second_filter_enable == false and draw.to_i == 1
        if @batch_id.to_i > 0
            is_second_filter_enable = true
            filter_field[a] = 'batch_id'
            filter_data[a] = @batch_id
            a += 1
        end
        
      end
      search_value = search["value"]
      
      per_page = params[:length]
      start = params[:start]
      
      page = ( start.to_i / per_page.to_i ) + 1
      
      b_filtered_search = false
      conditions = ""
      unless search_value.nil? or search_value.empty? or search_value.blank?
        conditions = " ((admission_no LIKE '%" + search_value.to_s + "%') OR (first_name LIKE '%" + search_value.to_s + "%'))"
        if is_second_filter_enable
            conditions += " AND "
            b = 0
            filter_field.each do |field|
              conditions += " (" + field + " = '" + filter_data[b] + "') "
              if (filter_field.length - 1) > b
                conditions += " AND "
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
              if (filter_field.length - 1) > b
                conditions += " AND "
              end
              b += 1
            end
            b_filtered_search = true
        end
      end
      unless @report_type.to_i == 3
        unless @report_type.to_i == 1
          if b_filtered_search == true
            conditions += " AND "
          end
          conditions += "  user_id NOT IN (select user_id from card_attendance where date BETWEEN '" + @report_date_from + "' and '" + @date_today + "' and type = 2 and school_id = '"+MultiSchool.current_school.id.to_s+"') "
          b_filtered_search = true
        end  
        if b_filtered_search
          std_length = Student.count(:conditions => conditions)
          @students = Student.paginate(:conditions => conditions,:select => "id,user_id, concat( admission_no, ' - ', first_name,' ', last_name )  as student_info, batch_id, '' as in_time, '' as out_time, '' as stat", :page => page.to_i, :per_page => per_page.to_i,:order=>""  + order_str)
          recordsFiltered = std_length
        else  
          std_length = Student.count
          @students = Student.paginate(:select => "id,user_id, concat( admission_no, ' - ', first_name,' ', last_name )  as student_info, batch_id, '' as in_time, '' as out_time, '' as stat", :page => page.to_i, :per_page => per_page.to_i,:order=>""  + order_str)
          recordsFiltered = std_length
        end
      else
        if b_filtered_search
          conditions += " AND "
        end
        @students_all = Student.find(:all, :conditions=>conditions + " date BETWEEN '" + @report_date_from + "' and '" + @date_today + "' and type = 1", :select => "students.id,students.user_id, concat( students.admission_no, ' - ', students.first_name,' ',students.last_name )  as student_info, students.batch_id, '' as in_time, '' as out_time, '' as stat", :joins => "INNER JOIN card_attendance ON students.user_id = card_attendance.user_id", :group => "students.user_id")
        @students = Student.paginate(:conditions=>conditions + " date BETWEEN '" + @report_date_from + "' and '" + @date_today + "' and type = 1", :select => "students.id,students.user_id, concat( students.admission_no, ' - ', students.first_name,' ',students.last_name )  as student_info, students.batch_id, '' as in_time, '' as out_time, '' as stat", :joins => "INNER JOIN card_attendance ON students.user_id = card_attendance.user_id", :page => page.to_i, :per_page => per_page.to_i,:order=>""  + order_str, :group => "students.user_id")
        std_ids = @students_all.map(&:user_id).uniq
        std_length = std_ids.length
        recordsFiltered = std_length
      end
      
      students_id = @students.map(&:user_id)
      data = []
      batch_ids = []
      batches_names = []
      unless @students.nil? or @students.empty?
       
       @cardAttendances = CardAttendance.all(:select=>'user_id, time',:conditions=>"date BETWEEN '" + @report_date_from + "' and '" + @date_today + "' and type = 2 and user_id in (" + students_id.join(",") + ")", :group => "time")
      
        k = 0;
        m = 0
      
        @students.each do |student|
          unless batch_ids.include?(student.batch_id)
            batches = Batch.find student.batch_id
            batch_ids[m] = student.batch_id
            batches_names[m] = batches.full_name
            batch_name = batches.full_name
            m += 1
          else
            t = batch_ids.index(student.batch_id)
            batch_name = batches_names[t]
          end
          std_id = student.user_id
          cardAttendance = @cardAttendances.select{ |s| s.user_id == student.user_id}

          
          if cardAttendance.nil? or cardAttendance.empty? or cardAttendance.blank?  
              in_time = ' - '
              out_time = ' - '
              time_diff = ' - '
          else 
            if cardAttendance.length == 1
              in_time = cardAttendance[0]['time'].strftime("%I:%M %p")
              out_time = cardAttendance[0]['time'].strftime("%I:%M %p")
              if @date_today == @report_date.to_date
                time_diff = ' In Office '
              else
                time_diff = ' - '
              end
            else  
              cardAttendance = cardAttendance.sort_by {|c| c['time']  unless c.blank?}
              in_time = cardAttendance[0]['time'].strftime("%I:%M %p")
              out_time = cardAttendance[cardAttendance.length - 1]['time'].strftime("%I:%M %p")
              time_diff = time_diff(cardAttendance[0]['time'], cardAttendance[cardAttendance.length - 1]['time'])
            end
           
          end
          emp = {:student_info => "<a href='/student_attendance/card_attendance_pdf?student_id=" + student.id.to_s + "&month=" + @month.to_s + "&year="+@year.to_s+"' target='_blank'>" + student.student_info + "<a/>", :batch => batch_name, :in_time => in_time, :out_time => out_time, :status => time_diff }
          data[k] = emp
          k += 1
        end
      end
      data_hash = {:draw => draw, :recordsTotal => std_length, :recordsFiltered => recordsFiltered, :data => data}
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
