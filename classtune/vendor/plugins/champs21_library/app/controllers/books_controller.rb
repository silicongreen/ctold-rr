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
class BooksController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only=>[:index,:library_transactions,:add_additional_details]
  before_filter :check_book_status, :only =>[ :edit , :update ]
  filter_access_to :all

  def index
    @book_call_number = params[:book_call_number]
    unless @book_call_number.blank?
      @book_call_number_obj = BookCallNumber.find_by_id(@book_call_number)
      @books = Book.paginate(:page => params[:page],:conditions=>["book_call_number_id = ?",@book_call_number],:include=>:tags)
    else
      @books = Book.paginate(:page => params[:page],:include=>:tags)
    end
  end
  
  def book_call_numbers
    @book_call_numbers = BookCallNumber.paginate(:page => params[:page])
  end
  
  def add_call_numbers
    @book_call_number = BookCallNumber.new
  end
  def create_call_number
    @book_call_number = BookCallNumber.new(params[:book_call_number])
    if @book_call_number.save
      flash[:notice]="#{t('flash1')}"
      redirect_to :controller => "books", :action => "book_call_numbers"
    else
      render 'add_call_numbers'
    end  
  end
  
  def edit_call_number
    @book_call_number = BookCallNumber.find(params[:id])
  end
  def update_call_number
    @book_call_number = BookCallNumber.find(params[:id]) 
    if @book_call_number.update_attributes(params[:book_call_number])
      flash[:notice]="#{t('flash6')}"
      redirect_to :controller => "books", :action => "book_call_numbers",:page=>params[:page]
    else
      render 'edit_call_number'
    end
  end
  
  def destroy_call_number
    @book_call_number = BookCallNumber.find(params[:id])
    can_destroy = true
    books = Book.find_all_by_book_call_number_id(self.id)
    unless books.blank?
      books.each do |book|
        unless book.book_movement_id.nil?
          can_destroy = false
          break
        end
      end
    end 
    if can_destroy
      @book_call_number.destroy
      flash[:notice] ="#{t('flash3')}"
    else
      flash[:warn_notice] ="#{t('flash4')}"
    end 
    redirect_to :controller => "books", :action => "book_call_numbers",:page=>params[:page]
  end

  def filter_by
    filter_field = params[:filter][:on]
    search = params[:filter][:search]
    unless filter_field.blank?
      @book_call_numbers = BookCallNumber.paginate(:all,:conditions=>[filter_field.to_s+" like ?",search],:page=>params[:page])
    else
      @book_call_numbers = BookCallNumber.paginate(:all,:conditions=>["call_number like ? or title like ? or subject like ? or author like ? or version like ? or language like ? or publish_year like ? or published_by like ?",search,search,search,search,search,search,search,search],:page=>params[:page])
    end  
    render(:update) do |page|
      page.replace_html 'books', :partial=>'book_call_number'
    end
  end

  def new
    @tagg = []
    @book = Book.find(:last)
    @book_call_number = params[:book_call_number]
    unless @book_call_number.nil?
      @book_call_numbers = BookCallNumber.find_all_by_id(@book_call_number)
    else
      @book_call_numbers = BookCallNumber.find(:all)
    end
    
    @book_number = @book.book_number.next unless @book.nil?
    unless params[:author].nil?
      @book_title = params[:title]
      @author = params[:author]
      @detail = Book.find_by_author_and_title(@author, @book_title)
      @tagg = @detail.tag_list
    else
      @book_title = ''
      @author = ''
    end
    
    @book = Book.new
    @tags = Tag.find(:all)
  end

  def create
    author = ""
    title = ""
    unless params[:book][:book_call_number_id].nil?
      book_call_number = BookCallNumber.find(params[:book][:book_call_number_id]) 
      unless book_call_number.blank?
        author = book_call_number.author
        title = book_call_number.title
      end
    end
    if @book = Book.create(:title=> title, :author=> author,:source=>params[:book][:source], :price=>params[:book][:price], :book_number =>params[:book][:book_number], :book_call_number_id =>params[:book][:book_call_number_id], :status=>'Available')
       flash[:notice]="#{t('flash1')}"
       redirect_to additional_data_books_path(:id => @created_books,:book_call_number=>params[:book_call_number])     
    else
      render 'new'
    end    
  end

  def edit
    @book = Book.find(params[:id])
    @book_call_number = params[:book_call_number]
    unless @book_call_number.nil?
      @book_call_numbers = BookCallNumber.find_all_by_id(@book_call_number)
    else
      @book_call_numbers = BookCallNumber.find(:all)
    end
    @tags = Tag.find(:all)
  end

  def update
    @book = Book.find(params[:id])  
    author = ""
    title = ""
    unless params[:book][:book_call_number_id].nil?
      book_call_number = BookCallNumber.find(params[:book][:book_call_number_id]) 
      unless book_call_number.blank?
        author = book_call_number.author
        title = book_call_number.title
      end
    end
    if @book.update_attributes(:title=> title, :author=> author,:source=>params[:book][:source], :price=>params[:book][:price], :book_number =>params[:book][:book_number], :book_call_number_id =>params[:book][:book_call_number_id], :status=>'Available')
       flash[:notice]="#{t('flash2')}"
       redirect_to edit_additional_data_books_path(:id => @book.id,:book_call_number=>params[:book_call_number])     
    else
      render 'edit'
    end
  end

  def additional_data
    @book = Book.new
    @books = Book.find_all_by_id(params[:id])
    @additional_fields = BookAdditionalField.find(:all, :conditions=> "is_active = true", :order=>"priority ASC")
    if @additional_fields.empty?
      flash[:notice] = "Books created successfully."
      redirect_to :controller => "books", :action => "index",:book_call_number=>params[:book_call_number] and return
    end
    if request.post?
      @books.each do |book|
        @error=false
        @book_additional_details = BookAdditionalDetail.find_all_by_book_id(book.id)
        mandatory_fields = BookAdditionalField.find(:all, :conditions=>{:is_mandatory=>true, :is_active=>true})
        mandatory_fields.each do|m|
          unless params[:book_additional_details][m.id.to_s.to_sym].present?
            @book.errors.add_to_base("#{m.name} must contain atleast one selected option.") 
            @error=true
          else
            if params[:book_additional_details][m.id.to_s.to_sym][:additional_info]==""
              @book.errors.add_to_base("#{m.name} cannot be blank.") 
              @error=true
            end
          end
        end
        unless @error==true
          params[:book_additional_details].each_pair do |k, v|
            addl_info = v['additional_info']
            addl_field = BookAdditionalField.find_by_id(k)
            if addl_field.input_type == "has_many"
              addl_info = addl_info.join(", ")
            end
            prev_record = BookAdditionalDetail.find_by_book_id_and_book_additional_field_id(book.id, k)
            unless prev_record.nil?
              unless addl_info.present?
                prev_record.destroy
              else
                prev_record.update_attributes(:additional_info => addl_info)
              end
            else
              addl_detail = BookAdditionalDetail.new(:book_id => book.id,
                :book_additional_field_id => k,:additional_info => addl_info)
              addl_detail.save if addl_detail.valid?
            end
          end
        else
          render :additional_data and return
        end
      end
      flash[:notice] = "Book saved with additional data successfully"
      redirect_to :controller => "books", :action => "index",:book_call_number=>params[:book_call_number]
    end
  end

  def edit_additional_data
    @book = Book.find(params[:id])
    @additional_fields = BookAdditionalField.find(:all, :conditions=> "is_active = true", :order=>"priority ASC")
    @book_additional_details = BookAdditionalDetail.find_all_by_book_id(@book.id)
    if @additional_fields.blank?
      flash[:notice] = t('book_updated')
      redirect_to :controller => "books", :action => "index",:book_call_number=>params[:book_call_number]
    end
    if request.post?
      @error=false
      mandatory_fields = BookAdditionalField.find(:all, :conditions=>{:is_mandatory=>true, :is_active=>true})
      mandatory_fields.each do|m|
        unless params[:book_additional_details][m.id.to_s.to_sym].present?
          @book.errors.add_to_base("#{m.name} must contain atleast one selected option.")
          @error=true
        else
          if params[:book_additional_details][m.id.to_s.to_sym][:additional_info]==""
            @book.errors.add_to_base("#{m.name} cannot be blank.")
            @error=true
          end
        end
      end
      unless @error==true
        params[:book_additional_details].each_pair do |k, v|
          addl_info = v['additional_info']
          addl_field = BookAdditionalField.find_by_id(k)
          if addl_field.input_type == "has_many"
            addl_info = addl_info.join(", ")
          end
          prev_record = BookAdditionalDetail.find_by_book_id_and_book_additional_field_id(@book.id, k)
          unless prev_record.nil?
            unless addl_info.present?
              prev_record.destroy
            else
              prev_record.update_attributes(:additional_info => addl_info)
            end
          else
            addl_detail = BookAdditionalDetail.new(:book_id => @book.id,
              :book_additional_field_id => k,:additional_info => addl_info)
            addl_detail.save if addl_detail.valid?
          end
        end
      else
        render :edit_additional_data and return
      end

      flash[:notice] = "Book saved with additional data successfully"
      redirect_to :controller => "books", :action => "index",:book_call_number=>params[:book_call_number]
    end
  end

  def show
    @book = Book.find(params[:id])
    @lender = Student.find_by_admission_no @book.book_movement.user.username unless @book.book_movement_id.nil?
    @lender ||= ArchivedStudent.find_by_admission_no @book.book_movement.user.username unless @book.book_movement_id.nil?
    @lender ||= Employee.find_by_employee_number @book.book_movement.user.username unless @book.book_movement_id.nil?
    @lender ||= ArchivedEmployee.find_by_employee_number @book.book_movement.user.username unless @book.book_movement_id.nil?
    @reservations = BookReservation.find_all_by_book_id(@book.id)
    @book_reserved = BookReservation.find_by_book_id(@book.id)
    @additional_details = BookAdditionalDetail.find_all_by_book_id(@book.id)
  end

  def destroy
    @book = Book.find(params[:id])
    if @book.book_movement_id.nil? #or @book.status=='Lost'
      @book.destroy
      flash[:notice] ="#{t('flash3')}"
    else
      flash[:warn_notice] ="#{t('flash4')}"
    end
    redirect_to books_path
  end

  def sort_by
    sort = params[:sort][:on]
    @books = Book.search(:status_like=>"#{sort}").paginate(:all,:page=>params[:page],:include=>:tags)
    render(:update) do |page|
      page.replace_html 'books', :partial=>'books'
    end
  end

  def add_additional_details
    @all_details = BookAdditionalField.find(:all,:order=>"priority ASC")
    @additional_details = BookAdditionalField.find(:all, :conditions=>{:is_active=>true},:order=>"priority ASC")
    @inactive_additional_details = BookAdditionalField.find(:all, :conditions=>{:is_active=>false},:order=>"priority ASC")
    @additional_field = BookAdditionalField.new
    @book_additional_field_option = @additional_field.book_additional_field_options.build
    if request.post?
      priority = 1
      unless @all_details.empty?
        last_priority = @all_details.map{|r| r.priority}.compact.sort.last
        priority = last_priority + 1
      end
      @additional_field = BookAdditionalField.new(params[:book_additional_field])
      @additional_field.priority = priority
      if @additional_field.save
        flash[:notice] = "Additional field added successfully"
        redirect_to :controller => "books", :action => "add_additional_details"
      end
    end
  end

  def change_field_priority
    @additional_field = BookAdditionalField.find(params[:id])
    priority = @additional_field.priority
    @additional_fields = BookAdditionalField.find(:all, :conditions=>{:is_active=>true}, :order=> "priority ASC").map{|b| b.priority.to_i}
    position = @additional_fields.index(priority)
    if params[:order]=="up"
      prev_field = BookAdditionalField.find_by_priority(@additional_fields[position - 1])
    else
      prev_field = BookAdditionalField.find_by_priority(@additional_fields[position + 1])
    end
    @additional_field.update_attributes(:priority=>prev_field.priority)
    prev_field.update_attributes(:priority=>priority.to_i)
    @additional_field = BookAdditionalField.new
    @additional_details = BookAdditionalField.find(:all, :conditions=>{:is_active=>true},:order=>"priority ASC")
    @inactive_additional_details = BookAdditionalField.find(:all, :conditions=>{:is_active=>false},:order=>"priority ASC")
    render(:update) do|page|
      page.replace_html "category-list", :partial=>"additional_fields"
    end
  end

  def edit_additional_details
    @additional_details = BookAdditionalField.find(:all, :conditions=>{:is_active=>true},:order=>"priority ASC")
    @inactive_additional_details = BookAdditionalField.find(:all, :conditions=>{:is_active=>false},:order=>"priority ASC")
    @additional_field = BookAdditionalField.find(params[:id])
    @book_additional_field_option = @additional_field.book_additional_field_options
    if request.get?
      render :action=>'add_additional_details'
    else
      if @additional_field.update_attributes(params[:book_additional_field])
        flash[:notice] = "Additional field updated successfully"
        redirect_to :action => "add_additional_details"
      else
        render :action=>"add_additional_details"
      end
    end
  end

  def delete_additional_details
    books = BookAdditionalDetail.find(:all ,:conditions=>"book_additional_field_id = #{params[:id]}")
    if books.blank?
      BookAdditionalField.find(params[:id]).destroy
      @additional_details = BookAdditionalField.find(:all, :conditions=>{:is_active=>true},:order=>"priority ASC")
      @inactive_additional_details = BookAdditionalField.find(:all, :conditions=>{:is_active=>false},:order=>"priority ASC")
      flash[:notice]="Additional field deleted successfully"
      redirect_to :action => "add_additional_details"
    else
      flash[:notice]="Additional field is in use and cannot be deleted"
      redirect_to :action => "add_additional_details"
    end
  end
  
  def library_transactions
    @transactions=FinanceTransaction.paginate(:per_page=>20,:page=>params[:page],:conditions=>["created_at >='#{Date.today}' and created_at <'#{Date.today+1.day}' and finance_type='BookMovement'"],:order=>'created_at desc')
  end

  def search_library_transactions
    @transactions=FinanceTransaction.paginate(:per_page=>20,:page=>params[:page],:joins=>'LEFT OUTER JOIN students ON students.id = payee_id',:conditions => ["(students.admission_no LIKE ? OR students.first_name LIKE ?) and finance_type=?",
        "#{params[:query]}%","#{params[:query]}%",'BookMovement'],:order=>'created_at desc')  unless params[:query] == ''
    render :update do |page|
      page.replace_html 'deleted_transactions', :partial => "books/search_library_transactions"
    end
    #render :partial => "books/search_library_transactions"
  end

  def library_transaction_filter_by_date
    @start_date=params[:s_date]
    @end_date=params[:e_date]
    @transactions=FinanceTransaction.paginate(:per_page=>20,:page=>params[:page],:joins=>'LEFT OUTER JOIN students ON students.id = payee_id',:conditions=>["(finance_transactions.created_at >='#{@start_date}' and finance_transactions.created_at <'#{@end_date.to_date+1.day}') and (finance_type='BookMovement' and payee_id=students.id)"],:order=>'created_at desc')
    render :update do |page|
      page.replace_html 'deleted_transactions', :partial => "books/library_transactions_date_filter"
    end
  end
  #render :partial => "books/library_transactions"
  def delete_library_transaction
    @financetransaction=FinanceTransaction.find(params[:id])
    if @financetransaction
      transaction_attributes=@financetransaction.attributes
      transaction_attributes.delete "id"
      transaction_attributes.delete "created_at"
      transaction_attributes.delete "updated_at"
      transaction_attributes.merge!(:user_id=>current_user.id,:collection_name=>@financetransaction.title)
      cancelled_transaction=CancelledFinanceTransaction.new(transaction_attributes)
      if @financetransaction.destroy
        cancelled_transaction.save
      end

    end
    if params[:s_date].present?
      @start_date=params[:s_date]
      @end_date=params[:e_date]
      @transactions=FinanceTransaction.paginate(:per_page=>20,:page=>params[:page],:joins=>'LEFT OUTER JOIN students ON students.id = payee_id',:conditions=>["(finance_transactions.created_at >='#{@start_date}' and finance_transactions.created_at <='#{@end_date}') and (finance_type='BookMovement' and payee_id=students.id)"],:order=>'created_at desc')
      render :update do |page|
        page.replace_html 'deleted_transactions', :partial => "books/library_transactions_date_filter"
      end
    elsif params[:query].present?
      @transactions=FinanceTransaction.paginate(:per_page=>20,:page=>params[:page],:joins=>'LEFT OUTER JOIN students ON students.id = payee_id',:conditions => ["(students.admission_no LIKE ? OR students.first_name LIKE ?) and finance_type=?",
          "#{params[:query]}%","#{params[:query]}%",'BookMovement'],:order=>'created_at desc')  unless params[:query] == ''
      render :update do |page|
        page.replace_html 'deleted_transactions', :partial => "books/search_library_transactions"
      end
    else
      @transactions=FinanceTransaction.paginate(:per_page=>20,:page=>params[:page],:conditions=>["created_at >='#{Date.today}' and created_at <'#{Date.today+1.day}' and finance_type='BookMovement'"],:order=>'created_at desc')
      render :update do |page|
        page.replace_html 'deleted_transactions', :partial => "books/library_transactions"
      end
    end
  end
  private

  def check_book_status
    @book = Book.find(params[:id])
    redirect_to :action => :show , :id => @book.id  if @book.status == 'Borrowed'
  end

end
