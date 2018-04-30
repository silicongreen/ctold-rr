#Copyright 2010 teamCreative Private Limited
#This product includes software developed at
#Project Champs21 - http://www.champs21.com/
#
#Licensed under the Apache License, Version 2.0 (the "License");
#you may not use this file except in compliance with the License.
#You may obtain a copy of the License at
#
#  http://www.apache.org/licenses/LICENSE-2.0
#
#Unless required by applicable law or agreed to in writing,
#software distributed under the License is distributed on an
#"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
#KIND, either express or implied.  See the License for the
#specific language governing permissions and limitations
#under the License.
class LibraryController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only=>[:index]
  before_filter :default_time_zone_present_time
  
  filter_access_to :all

  def index
    
  end

  def search_book
   
  end

  def search_result
    if request.get?
      page_not_found
      return
    end
    if params[:search][:search_by] == 'tag'
      @books = Book.find_tagged_with(params[:search][:name]).paginate( :page => params[:page], :per_page => 20) if params[:search][:name].length>=3
    elsif params[:search][:search_by] == 'title'
      @books = Book.paginate(:conditions=>['books.title LIKE ?',"%#{params[:search][:name]}%"] ,:per_page=>20,:page=>params[:page]) if params[:search][:name].length>=3
    elsif params[:search][:search_by] == 'author'
      @books = Book.paginate(:conditions=>['books.author LIKE ?',"%#{params[:search][:name]}%"] ,:per_page=>20,:page=>params[:page]) if params[:search][:name].length>=3
    else
      if params[:search][:name].length>=3
        @books = Book.paginate(:conditions=>['books.book_number LIKE ?',"%#{params[:search][:name]}%"] ,:per_page=>20,:page=>params[:page])
      else
        @books = Book.paginate(:conditions=>['books.book_number LIKE ?',"#{params[:search][:name]}"] ,:per_page=>20,:page=>params[:page])
      end
    end
    if request.xhr?
      render :update do |page|
        page.replace_html 'book-list', :partial => 'book_list'
      end
    end
  end

  def availabilty
    render :partial=>'availability'
  end

  def card_setting
   
  end

  def show_setting
    @course = Course.find(params[:course_name])
    @card_setting = LibraryCardSetting.find_all_by_course_id(@course.id)
    render(:update) do |page|
      page.replace_html 'card_setting', :partial=>'library_card_setting'
    end
  end

  def add_new_setting
    @setting = LibraryCardSetting.new
    @course = Course.find params[:id] if request.xhr? and params[:id]
    @student_categories = StudentCategory.active
    respond_to do |format|
      format.js { render :action => 'new' }
    end
  end

  def create_setting
    @library_setting = LibraryCardSetting.new(params[:library_card_setting])
    respond_to do |format|
      if  @library_setting.save
        @course = Course.find(@library_setting.course_id)
        @card_setting = LibraryCardSetting.find_all_by_course_id(@course.id)
        format.js { render :action => 'create' }
      
      else
        @error = true
        format.html { render :action => "new" }
        format.js { render :action => 'create' }
      end
    end
  end

  def edit_card_setting
    @setting = LibraryCardSetting.find(params[:id])
    @course = Course.find @setting.course_id
    @student_categories = StudentCategory.active
    respond_to do |format|
      format.js { render :action => 'edit' }
    end
  end

  def update_card_setting
    @setting = LibraryCardSetting.find(params[:id])
    respond_to do |format|
      if @setting.update_attributes(params[:library_card_setting])
        @course = Course.find(@setting.course_id)
        @card_setting = LibraryCardSetting.find_all_by_course_id(@course.id)
        format.js { render :action => 'update' }
      else
        @error = true
        format.html { render :action => "edit" }
        format.js { render :action => 'update' }
      end
    end
  end

  def delete_card_setting
    @setting = LibraryCardSetting.find(params[:id])
    @course = Course.find(@setting.course_id)
    @setting.delete
    @card_setting = LibraryCardSetting.find_all_by_course_id(@course.id)
    respond_to do |format|
      format.js { render :action => 'destroy' }
    end
  end
  
  def movement_log_details
    @log= BookMovement.find(:all,:select=>"students.id as student_id,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.id as user_id_log,users.employee,books.status as book_status,books.book_number,books.title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned' and users.id = ?",params[:user_id]],:order=>'due_date ASC')
  end
  
  def movement_log_details_csv
    log= BookMovement.all(:select=>"students.id as student_id,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.employee,books.status as book_status,books.book_number,books.title as title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned' and users.id = ?",params[:user_id]],:order=>'due_date ASC')
   
  
    csv_string=FasterCSV.generate do |csv|
      cols=["#{t('no_text')}","#{t('book_number')}","#{t('book_title')}","#{t('borrowed_by') }","#{t('status') }","#{t('issue_date')}","#{t('due_date')}"]
      csv << cols
      log.each_with_index do |s,i|
        col=[]
        col<< "#{i+1}"
        col<< "#{s.book_number}"
        col<< "#{s.title}"
        if s.student?
          col<< "#{s.first_name} #{s.last_name} - #{s.admission_no}"
        else
          col<< "#{s.first_name} #{s.last_name} - #{s.employee_number}"
        end
        col<< "#{s.status}"
        col<< "#{s.issue_date}"
        col<< "#{s.due_date}"
        col=col.flatten
        csv<< col
      end
    end
    filename = "#{t('library_text')}#{t('book_movement_log')}- #{Time.now.to_date.to_s}.csv"
    send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
  end

  def movement_log
   
    @sort_order=params[:sort_order]
    if params[:book_log].nil?
      if @sort_order.nil?
        @log= BookMovement.paginate(:select=>"students.id as student_id,count(book_movements.id) as number_of_book,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.id as user_id_log,users.employee,books.status as book_status,books.book_number,books.title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned'"],:page=>params[:page],:per_page=>20,:order=>'due_date ASC',:group=>"users.id")
      else
        @log= BookMovement.paginate(:select=>"students.id as student_id,count(book_movements.id) as number_of_book,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.id as user_id_log,users.employee,books.status as book_status,books.book_number,books.title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned'"],:page=>params[:page],:per_page=>20,:order=>@sort_order,:group=>"users.id")
      end
    else
      if params[:book_log][:date2].blank?
        params[:book_log][:date2] = "2080-01-01"
      end
      if params[:book_log][:date].blank?
        params[:book_log][:date] = "1977-01-01"
      end
      extra_condition = " AND users.id != ?"
      extra_params = 0
      if !params[:book_log][:batch].blank? and !params[:book_log][:user_type].blank? and params[:book_log][:user_type].to_i == 1
        extra_condition = " AND students.batch_id = ? "
        extra_params = params[:book_log][:batch]
      end
      
      params[:book_log][:date] = params[:book_log][:date].to_date
      params[:book_log][:date2] = params[:book_log][:date2].to_date
      
      if @sort_order.nil?
        if params[:book_log][:type]=="Due Date"
          @log= BookMovement.paginate(:select=>"students.id as student_id,count(book_movements.id) as number_of_book,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.id as user_id_log,users.employee,books.status as book_status,books.book_number,books.title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned' and book_movements.due_date >= ? and book_movements.due_date <= ? and users.student = ?"+extra_condition,params[:book_log][:date],params[:book_log][:date2],params[:book_log][:user_type],extra_params],:page=>params[:page],:per_page=>20,:order=>'due_date ASC',:group=>"users.id")
        else
          @log= BookMovement.paginate(:select=>"students.id as student_id,count(book_movements.id) as number_of_book,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.id as user_id_log,users.employee,books.status as book_status,books.book_number,books.title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned' and book_movements.issue_date >= ? and book_movements.issue_date <= ?  and users.student = ?"+extra_condition,params[:book_log][:date],params[:book_log][:date2],params[:book_log][:user_type],extra_params],:page=>params[:page],:per_page=>20,:order=>'due_date ASC',:group=>"users.id")
        end
      else
        if params[:book_log][:type]=="Due Date"
          @log= BookMovement.paginate(:select=>"students.id as student_id,count(book_movements.id) as number_of_book,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.id as user_id_log,users.employee,books.status as book_status,books.book_number,books.title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned' and book_movements.due_date >= ? and book_movements.due_date <= ?   and users.student = ?"+extra_condition,params[:book_log][:date],params[:book_log][:date2],params[:book_log][:user_type],extra_params],:page=>params[:page],:per_page=>20,:order=>@sort_order,:group=>"users.id")
        else
          @log= BookMovement.paginate(:select=>"students.id as student_id,count(book_movements.id) as number_of_book,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.id as user_id_log,users.employee,books.status as book_status,books.book_number,books.title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned' and book_movements.issue_date >= ? and book_movements.issue_date<= ?   and users.student = ?"+extra_condition,params[:book_log][:date],params[:book_log][:date2],params[:book_log][:user_type],extra_params],:page=>params[:page],:per_page=>20,:order=>@sort_order,:group=>"users.id")
        end
      end
    end
    if request.xhr?
      render :update do |page|
        page.replace_html "information", :partial => "movement_log_details"
      end
    end
  end

  def movement_log_csv
    
    sort_order=params[:sort_order]
    if params[:book_log].nil?
      if sort_order.nil?
        log= BookMovement.all(:select=>"students.id as student_id,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.employee,books.status as book_status,books.book_number,books.title as title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned'"],:order=>'due_date ASC')
      else
        log= BookMovement.all(:select=>"students.id as student_id,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.employee,books.status as book_status,books.book_number,books.title as title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned'"],:order=>sort_order)
      end
    else
      if params[:book_log][:date2].blank?
        params[:book_log][:date2] = "2080-01-01"
      end
      if params[:book_log][:date].blank?
        params[:book_log][:date] = "1977-01-01"
      end
      extra_condition = " AND users.id != ?"
      extra_params = 0
      if !params[:book_log][:batch].blank? and !params[:book_log][:user_type].blank? and params[:book_log][:user_type].to_i == 1
        extra_condition = " AND students.batch_id = ? "
        extra_params = params[:book_log][:batch]
      end
      
      params[:book_log][:date] = params[:book_log][:date].to_date
      params[:book_log][:date2] = params[:book_log][:date2].to_date
      
      if sort_order.nil?
        if params[:book_log][:type]=="Due Date"
          log = BookMovement.all(:select=>"students.id as student_id,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.employee,books.status as book_status,books.book_number,books.title as title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned' and book_movements.due_date >= ? and book_movements.due_date <= ? and users.student = ?"+extra_condition,params[:book_log][:date],params[:book_log][:date2],params[:book_log][:user_type],extra_params],:order=>'due_date ASC')
        else
          log = BookMovement.all(:select=>"students.id as student_id,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.employee,books.status as book_status,books.book_number,books.title as title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned' and book_movements.issue_date >= ? and book_movements.issue_date <= ?  and users.student = ?"+extra_condition,params[:book_log][:date],params[:book_log][:date2],params[:book_log][:user_type],extra_params],:order=>'due_date ASC')
        end
      else
        if params[:book_log][:type]=="Due Date"
          log = BookMovement.all(:select=>"students.id as student_id,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.employee,books.status as book_status,books.book_number,books.title as title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned' and book_movements.due_date >= ? and book_movements.due_date <= ?   and users.student = ?"+extra_condition,params[:book_log][:date],params[:book_log][:date2],params[:book_log][:user_type],extra_params],:order=>sort_order)
        else
          log = BookMovement.all(:select=>"students.id as student_id,students.admission_no,employees.employee_number ,employees.id as employee_id,book_movements.*,users.first_name,users.last_name,users.student,users.employee,books.status as book_status,books.book_number,books.title as title",:joins=>"INNER JOIN `users` ON `users`.id = `book_movements`.user_id INNER JOIN `books` ON `books`.id = `book_movements`.book_id LEFT OUTER JOIN `students` ON `users`.id = `students`.user_id LEFT OUTER JOIN `employees` ON `users`.id = `employees`.user_id",:conditions=>["book_movements.status !='Returned' and book_movements.issue_date >= ? and book_movements.issue_date<= ?   and users.student = ?"+extra_condition,params[:book_log][:date],params[:book_log][:date2],params[:book_log][:user_type],extra_params],:order=>sort_order)
        end
      end
      
    end
  
    csv_string=FasterCSV.generate do |csv|
      cols=["#{t('no_text')}","#{t('book_number')}","#{t('book_title')}","#{t('borrowed_by') }","#{t('status') }","#{t('issue_date')}","#{t('due_date')}"]
      csv << cols
      log.each_with_index do |s,i|
        col=[]
        col<< "#{i+1}"
        col<< "#{s.book_number}"
        col<< "#{s.title}"
        if s.student?
          col<< "#{s.first_name} #{s.last_name} - #{s.admission_no}"
        else
          col<< "#{s.first_name} #{s.last_name} - #{s.employee_number}"
        end
        col<< "#{s.status}"
        col<< "#{s.issue_date}"
        col<< "#{s.due_date}"
        col=col.flatten
        csv<< col
      end
    end
    filename = "#{t('library_text')}#{t('book_movement_log')}- #{Time.now.to_date.to_s}.csv"
    send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
  end

  def book_statistics
    if params[:type] == 'title'
      @books = Book.find_all_by_title(params[:name])
    else

      @books = Book.find_all_by_author(params[:name])
    end
  end

  def book_reservation
    @book_reservation_time_out = Configuration.find_by_config_key('BookReservationTimeOut')
    if request.post?
    end
  end

  def library_report
    if date_format_check
      @start_date = params[:start_date]
      @end_date  = params[:end_date]
      @batch = Batch.all
      library_id = FinanceTransactionCategory.find_by_name('Library').id
      @transactions = FinanceTransaction.find(:all, :conditions=>"category_id = '#{library_id}' and transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'")
    end
  end

  def library_report_pdf
    if date_format_check
      @start_date = params[:start_date]
      @end_date  = params[:end_date]
      @batch = Batch.all
      library_id = FinanceTransactionCategory.find_by_name('Library').id
      @transactions = FinanceTransaction.find(:all, :conditions=>"category_id = '#{library_id}' and transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'")
      render :pdf=>'library_report_pdf'
    end
  end

  def batch_library_report
    if date_format_check
      @start_date = params[:start_date]
      @end_date  = params[:end_date]
      library_id = FinanceTransactionCategory.find_by_name('Library').id
      @batch = Batch.find(params[:id])
      @transactions = FinanceTransaction.find(:all, :conditions=>"category_id = '#{library_id}' and transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'")
    end
  end

  def batch_library_report_pdf
    if date_format_check
      @start_date = params[:start_date]
      @end_date  = params[:end_date]
      library_id = FinanceTransactionCategory.find_by_name('Library').id
      @batch = Batch.find(params[:id])
      @transactions = FinanceTransaction.find(:all, :conditions=>"category_id = '#{library_id}' and transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'")
      render :pdf=>'batch_library_report_pdf'
    end
  end

  def student_library_details
    @current_user = current_user
    @available_modules = Configuration.available_modules
    @sms_module = Configuration.available_modules
    @student = Student.find(params[:id])
    @reserved = @student.book_reservations
    @borrowed = @student.book_movements.find(:all, :conditions=>["status !='Returned'"])
  end

  def employee_library_details
    @current_user = current_user
    @available_modules = Configuration.available_modules
    @employee = Employee.find(params[:id])
    @reserved = @employee.book_reservations
    @borrowed = @employee.book_movements.find(:all, :conditions=>["status !='Returned'"])
    @new_reminder_count = Reminder.find_all_by_recipient(@current_user.id, :conditions=>"is_read = false")
  end
end