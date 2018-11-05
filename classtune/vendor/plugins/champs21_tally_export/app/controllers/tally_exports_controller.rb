class TallyExportsController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only => [:index]
  filter_access_to :all
  in_place_edit_with_validation_for :tally_company, :company_name
  in_place_edit_with_validation_for :tally_account, :account_name
  in_place_edit_with_validation_for :tally_voucher_type, :voucher_name

  def index
    tally_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/tally.yml")['champs21']
    @only_import_export = tally_config['only_import_export']
  end

  def settings

  end

  def general_settings
    @config = TallyExportConfiguration.get_multiple_configs_as_hash ['TallyUrl', 'EnableLiveSync', 'LiveSyncStartDate']

    if request.post?
      configs = params[:tally_export_configuration]
      if configs[:enable_live_sync] == '1'
        configs[:live_sync_start_date] = I18n.l(Date.today, :format=>"%d %b %Y") if @config[:live_sync_start_date].blank?
      end
      TallyExportConfiguration.set_config_values(configs)
      @config = TallyExportConfiguration.get_multiple_configs_as_hash ['TallyUrl', 'EnableLiveSync', 'LiveSyncStartDate']
      return
      flash[:notice] = "#{t('flash_msg8')}"
      redirect_to :action => "general_settings"  and return
    end
  end

  def companies
    @companies = TallyCompany.all
    if request.post?

      if TallyCompany.create(params[:company])
        flash[:notice] = "#{t('company_created_successfully')}"
      end
      @companies = TallyCompany.all
    end
  end

  def delete_company
    company = TallyCompany.find_by_id params[:id]

    unless company.tally_ledgers.present?
      if company.destroy
        flash[:notice] = "#{t('company_deleted_successfully')}"
      end
    else
      flash[:notice] = "#{t('company_cannot_be_deleted')}"
    end
    redirect_to :action => 'companies'
  end

  def voucher_types
    @vouchers = TallyVoucherType.all

    if request.post?
      TallyVoucherType.create(params[:voucher])
      flash[:notice] = "#{t('voucher_type_created_successfully')}"

      @vouchers = TallyVoucherType.all
    end
  end

  def delete_voucher
    voucher = TallyVoucherType.find_by_id params[:id]

    unless voucher.tally_ledgers.present?
      if voucher.destroy
        flash[:notice] = "#{t('voucher_type_deleted_successfully')}"
      end
    else
      flash[:notice] = "#{t('voucher_type_cannot_be_deleted')}"
    end
    redirect_to :action => 'voucher_types'
  end

  def accounts
    @accounts = TallyAccount.all

    if request.post?
      TallyAccount.create(params[:account])
      flash[:notice] = "#{t('account_created_successfully')}"

      @accounts = TallyAccount.all
    end
  end

  def delete_account
    account = TallyAccount.find_by_id params[:id]

    unless account.tally_ledgers.present?
      if account.destroy
        flash[:notice] = "#{t('account_deleted_successfully')}"
      end
    else
      flash[:notice] = "#{t('account_cannot_be_deleted')}"
    end
    redirect_to :action => 'accounts'
  end

  def create_ledger
    @tally_ledger = TallyLedger.new
    @companies = TallyCompany.all
    @vouchers = TallyVoucherType.all
    @accounts = TallyAccount.all

    @transaction_categories = FinanceTransactionCategory.all(:conditions => "tally_ledger_id IS NULL AND deleted = 0")
    if request.post?

      category_ids = params[:tally_ledger][:finance_transaction_category_ids]
      categories = FinanceTransactionCategory.all(:conditions => "id IN (#{category_ids.join(',')}) AND tally_ledger_id IS NULL AND deleted = 0") unless category_ids.blank?

      params[:tally_ledger].delete('finance_transaction_category_ids')

      @tally_ledger = TallyLedger.new(params[:tally_ledger])

      unless categories.nil?
        if @tally_ledger.save
          categories.each do |cat|
            cat.update_attributes(:tally_ledger_id => @tally_ledger.id)
          end
          @transaction_categories = FinanceTransactionCategory.all(:conditions => "tally_ledger_id IS NULL AND deleted = 0")
          flash[:notice] = "#{t('ledger_created_successfully')}"
          redirect_to :action=>:create_ledger
        end
      else
        @tally_ledger.errors.add(:category, "#{t('must_be_selected')}.")
        render :action=>:create_ledger
      end
    end
  end

  def edit_ledger
    @tally_ledger = TallyLedger.find_by_id(params[:id], :include=>[ :finance_transaction_categories ])
    @companies = TallyCompany.all
    @vouchers = TallyVoucherType.all
    @accounts = TallyAccount.all
    @transaction_categories = FinanceTransactionCategory.all(:conditions => "(tally_ledger_id = #{@tally_ledger.id} OR tally_ledger_id IS NULL) AND deleted = 0")
    if request.post?
      category_ids = params[:tally_ledger][:finance_transaction_category_ids]
      categories = FinanceTransactionCategory.all(:conditions => "id IN (#{category_ids.join(',')}) AND deleted = 0") unless category_ids.blank?
      params[:tally_ledger].delete('finance_transaction_category_ids')
      unless categories.nil?
        if @tally_ledger.update_attributes(params[:tally_ledger])
          @transaction_categories.each do |trans_cat|
            if categories.include?(trans_cat)
              trans_cat.update_attributes(:tally_ledger_id => @tally_ledger.id)
            else
              trans_cat.update_attributes(:tally_ledger_id => nil)
            end
          end
          flash[:notice] = "#{t('ledger_updated_successfully')}"
        else
          render :edit_ledger
        end
      else
        @tally_ledger.errors.add(:category, "#{t('must_be_selected')}.")
        render :edit_ledger
      end
    end
  end

  def view_ledgers
    @ledgers = TallyLedger.all(:include => [:finance_transaction_categories])

  end

  def delete_ledger
    ledger = TallyLedger.find_by_id params[:id]

    transactions = ledger.finance_transaction_categories
    transactions.each do |trans|
      trans.update_attributes(:tally_ledger_id => nil)
    end

    if ledger.destroy
      flash[:notice] = "#{t('ledger_deleted_successfully')}"
    end
    redirect_to :action => 'view_ledgers'
  end

  def manual_sync

    if request.post?
      Delayed::Job.enqueue(TallyManualSyncJob.new(params[:manual_sync][:start_date], params[:manual_sync][:end_date] ))
      flash[:notice] = "#{t('manual_sync_scheduled_successfully')} <a href='#{scheduled_task_path(:job_object=>"TallyManualSyncJob",:job_type=>"1")}'>#{t('click_here')}</a> #{t('to_view_status')}"
    end
  end

  def bulk_export

  end

  def schedule
    @ledgers = TallyLedger.all
    if request.post?
      @errors = []
      unless params[:bulk_export][:ledger_ids].blank?
        Delayed::Job.enqueue(TallyBulkExportJob.new(params[:bulk_export][:start_date], params[:bulk_export][:end_date], params[:bulk_export][:ledger_ids].join(',')))
        flash[:notice] = "#{t('export_scheduled_successfully')} <a href='#{scheduled_task_path(:job_object=>"TallyBulkExportJob",:job_type=>"1")}'>#{t('click_here')}</a> #{t('to_view_status')}"
      else
        @errors << "#{t('please_select_any_ledger')}"
      end
    end
  end

  def downloads
    @files = TallyExportFile.all
  end

  def download
    export_file = TallyExportFile.find(params[:id])
    file_name = export_file.export_file.path
    export_file.update_attribute('download_no',"#{export_file.download_no.next}")
    send_file file_name, :type => 'text/xml'
  end

  def failed_syncs
    unless request.post?
      @logs = TallyExportLog.find(:all, :conditions => { :status => false }, :order=> "updated_at DESC", :include => [:finance_transaction])
    else
      @date = params[:date]
      @logs = TallyExportLog.find(:all, :conditions => ["status = ? AND updated_at >= ? AND updated_at < ?",false, @date.to_datetime, @date.to_datetime + 1.day], :order=> "updated_at DESC", :include => [:finance_transaction])
    end
    @logs.reject!{|l| l.finance_transaction.nil?}
    @logs=@logs.paginate( :page => params[:page], :per_page => 5)
  end
  
  def export_journal
    @option = 'journal'
    @date_today = Date.today
    end_of_month = @date_today.next_month.end_of_month
    start_month = end_of_month - 120
    
    @finance_fee_collections = FinanceFeeCollection.find(:all,:order=>'due_date DESC',:conditions => ["is_deleted = #{false} and due_date >= '#{start_month.to_date.strftime("%Y-%m-%d")}' and due_date <= '#{end_of_month.to_date.strftime("%Y-%m-%d")}'"] )
    @all_finance_fee_collections = FinanceFeeCollection.find(:all,:order=>'due_date DESC',:conditions => ["is_deleted = #{false}"] )
  end
  
  def get_batches
    unless params[:id].nil? or params[:id].empty?
      date_id = params[:id]
      @date = FinanceFeeCollection.find(date_id)
      @batches = @date.batches
      @check_paid = "0"
      unless params[:check_paid].nil? or params[:check_paid].empty?
        @check_paid = params[:check_paid]
      end
    end
    render :update do |page|
      page.replace_html "batchs1", :partial => "fees_collection_batches"
    end
  end
  
  def export_receipt
    @option = 'receipt'
    @date_today = Date.today
    end_of_month = @date_today.next_month.end_of_month
    start_month = end_of_month - 120
    
    @finance_fee_collections = FinanceFeeCollection.find(:all,:order=>'due_date DESC',:conditions => ["is_deleted = #{false} and due_date >= '#{start_month.to_date.strftime("%Y-%m-%d")}' and due_date <= '#{end_of_month.to_date.strftime("%Y-%m-%d")}'"] )
    @all_finance_fee_collections = FinanceFeeCollection.find(:all,:order=>'due_date DESC',:conditions => ["is_deleted = #{false}"] )
  end
  
  def update_fees_collection_dates
    @batch = Batch.find(params[:batch_id])
    @dates = @batch.finance_fee_collections
    @option = params[:opt]
    render :update do |page|
      page.replace_html "fees_collection_dates", :partial => "fees_collection_dates"
    end
  end
  
  def export_batches
    unless params[:batches].nil? or params[:batches].empty?
      @batches = Batch.find(:all,:conditions=> ["is_deleted=#{false} and is_active=#{true} and id IN (" + params[:batches] + ")"])
      @check_paid = params[:check_paid]
    end
    if request.xhr?
      render(:update) do |page|
        page.replace_html   'customized_div', :partial=>"student_batches_export"
      end
    end
  end
  
  def get_user_by_batches
    unless params[:id].nil? or params[:id].empty?
      @date    = @fee_collection =  FinanceFeeCollection.find(params[:date_id])
      
      @batch   = Batch.find(params[:id])
      
      student_ids = @date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
      unless params[:check_paid].nil? or params[:check_paid].empty?
        if params[:check_paid].to_i == 1
          @fees = FinanceFee.all(:conditions=>"is_paid=#{true} and fee_collection_id = #{@date.id} and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
        else  
          @fees = FinanceFee.all(:conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
        end
      else
        @fees = FinanceFee.all(:conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
      end
      
      @student_ids = []
      unless params[:student_ids].nil? or params[:student_ids].empty?
        @student_ids = params[:student_ids].split(",")
      end
      
    end
    if request.xhr?
      render(:update) do |page|
        page.replace_html   'student_div', :partial=>"student_fees_batches"
      end
    end
  end
  
  def download_journal
    auto_generate_voucher = false
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    
    date = Spreadsheet::Format.new :number_format => 'MM/DD/YYYY'
    
    row_1 = ["Voucher Date","Voucher Number","Voucher Type","Debit Ledger","Debit Amount","Cost Centre","Credit Ledger","Credit Amount","Cost Centre","Narration","bill wise details(refno)"]

    # Create a new Workbook
    new_book = Spreadsheet::Workbook.new

    # Create the worksheet
    new_book.create_worksheet :name => 'Journal'

    # Add row_1
    new_book.worksheet(0).insert_row(0, row_1)
    
    vtype = 'Journal-CT'
    ind = 1
    
    unless params[:batches].nil? or params[:batches].empty?
      particulars = ['particular','discount','late']
      unless params[:particulars].nil?
        particulars = params[:particulars].split(",")
      end
      #transaction_date = Date.today.to_date.strftime("%m/%d/%Y")
      transaction_date = Date.today.to_date.strftime("%e-%b-%Y")
      unless params[:export_date].nil?
        #transaction_date = params[:export_date].to_date.strftime("%m/%d/%Y")
        trans_date = params[:export_date].to_date
        transaction_date = params[:export_date].to_date.strftime("%e-%b-%Y")
      end
      @date    = @fee_collection =  FinanceFeeCollection.find(params[:date_id])
      batches = params[:batches].split(",")
      batches.each do |batch_id|
        @batch   = Batch.find(batch_id)
        
        @type    = params[:type]
        
        if @type.to_i == 0
          unless params[:students].nil? or params[:students].empty?
            student_ids = params[:students]
          else
            student_ids = @date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
          end
          @fees = FinanceFee.all(:conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(students.id,'#{ student_ids}') and batches.id = #{@batch.id}" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id INNER JOIN batches ON batches.id = students.batch_id')
        else
          student_ids = @date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
          @fees = FinanceFee.all(:conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
        end
        
        @dates   = @batch.finance_fee_collections

        #@fees_data = @fees.select{|f| !f.is_paid}
        @fees_data = @fees #.select{|f| !f.is_paid}
        unless @fees_data.nil?
          
          @fees_data.each do |fee|
            student ||= fee.student
            student ||= fee.student
            by = student.admission_no.gsub("SJW","")
            by = by.gsub("FC","")
            by = by.gsub("MC","")
            by = by.gsub("-","")
            
            student_admission_no = student.admission_no.gsub("SJW","")
            student_admission_no = student_admission_no.gsub("FC","")
            student_admission_no = student_admission_no.gsub("MC","")
            student_admission_no = student_admission_no.gsub("-","")
            
            @financefee = student.finance_fee_by_date @date
            
            @due_date = @fee_collection.start_date
            #transaction_date = @due_date.to_date.strftime("%m/%d/%Y")
            if auto_generate_voucher
              voucher_no = (0...4).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
            else
              voucher_no = @due_date.strftime "%M%Y" + "-" 
            end
            
            if @financefee.is_paid
              @paid_fees = fee.finance_transactions
            end
            total_amount = 0
            amount_paid = 0
            @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==@batch) }
            
            if particulars.include?("particular")
              unless @fee_particulars.nil?
                  @fee_particulars.each do |fee_p|
                    s_initial = ""
                    vn = ""
                    description = fee_p.name + " payment for " + student.full_name
                    fee_name = fee_p.name;
                    unless fee_name.capitalize.include? "due"
                      a_fee_name = fee_name.split(" ")
                      if a_fee_name.length == 2
                        s_initial = a_fee_name[0].capitalize[0, 1] + a_fee_name[1].capitalize[0, 1]
                      elsif a_fee_name.length == 1
                        s_initial = a_fee_name[0].capitalize[0, 2]
                      else  
                        s_initial = a_fee_name[0].capitalize[0, 3]
                      end
                      #vn = student_admission_no + "-" + voucher_no + s_initial
                      vn = student_admission_no + "-" + s_initial
                      to = fee_p.name
                      total_amount = fee_p.amount
                      amount_paid += total_amount

                      row_1 = ["Voucher Date","Voucher Number","Voucher Type","Debit Ledger","Debit Amount","Cost Centre","Credit Ledger","Credit Amount","Cost Centre","Narration","bill wise details(refno)"]
                      dt_due = @due_date.strftime "%M%Y";
                      #bill = student_admission_no + "-" + s_initial + "-" + dt_due;
                      if fee_p.name.downcase.include? "tuition"
                        bill = trans_date.strftime("%b") + "-" + trans_date.strftime("%y");
                      elsif fee_p.name.downcase.include? "transport"
                        bill = "Transport-" + trans_date.strftime("%b") + "-" + trans_date.strftime("%y");
                      elsif fee_p.name.downcase.include? "piano"
                        bill = "Piano-" + trans_date.strftime("%b") + "-" + trans_date.strftime("%y");
                      end
                    
                      #new_book.row(ind).set_format(0, date)
                      row_new = [transaction_date, vn, vtype, student_admission_no, total_amount, fee_name, fee_name, total_amount, "", description, bill]
                      new_book.worksheet(0).insert_row(ind, row_new)
                      ind += 1
                    end
                  end
              end
            end
            
            @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
            @total_discount = 0
            
            calculate_discount(@date, @batch, student)
            if @total_discount > 0
              if particulars.include?("discount")
                s_initial = "DIS"
                #vn = student_admission_no + "-" + voucher_no + s_initial
                vn = student_admission_no + "-" + s_initial
                description = "Discount for " + student.full_name
                vtype = 'Journal-CT'
                dl = "Discount"
                total_amount = @total_discount
                dt_due = @due_date.strftime "%M%Y";
                #bill = student_admission_no + "-TF-" + dt_due;
                bill = "Discount";
                row_new = [transaction_date, vn, vtype, dl, total_amount, dl, student_admission_no, total_amount, "", description, bill]
                new_book.worksheet(0).insert_row(ind, row_new)
                ind += 1
              end
            end
            
            bal=(@total_payable-@total_discount).to_f
            days=(Date.today-@date.due_date.to_date).to_i
            auto_fine=@date.fine

            if days > 0 and auto_fine and particulars.include?("late")
                fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
                fine_amount=fine_rule.is_amount ? fine_rule.fine_amount : (bal*fine_rule.fine_amount)/100 if fine_rule
                extra_fine = calculate_extra_fine(@date, fine_rule)
                fine_amount = fine_amount + extra_fine

                description = "Fine for " + student.full_name
                s_initial = "FI"
                #vn = student_admission_no + "-" + voucher_no + s_initial
                vn = student_admission_no + "-" + s_initial
                vtype = 'Journal-CT'
                to = "Late Fine"
                total_amount = fine_amount
                dt_due = @due_date.strftime "%M%Y";
                #bill = student_admission_no + "-FI-" + dt_due;
                bill = "Late Fine";
                row_new = [transaction_date, vn, vtype, student_admission_no, total_amount, to,to,total_amount,"", description, bill]
                new_book.worksheet(0).insert_row(ind, row_new)
                ind += 1

                fine_discount = 0
                fine_discount = get_fine_discount(@date, @batch, student, fine_amount)
                if fine_discount > 0
                  description = "Fine Discount for " + student.full_name
                  s_initial = "FID"
                  #vn = student_admission_no + "-" + voucher_no + s_initial
                  vn = student_admission_no + "-" + s_initial
                  vtype = 'Journal-CT'
                  to = "Fine"
                  
                  bill = "Late Fine Discount";
                  
                  total_amount = fine_amount
                  row_new = [transaction_date, vn, vtype, to, total_amount, "Fine", student_admission_no, total_amount, "",description, bill]
                  new_book.worksheet(0).insert_row(ind, row_new)
                  ind += 1
                end
            end
          end
        end
      end
      
      spreadsheet = StringIO.new 
      new_book.write spreadsheet 

      send_data spreadsheet.string, :filename => @date.full_name + ".xls", :type =>  "application/vnd.ms-excel"
    else
      flash[:notice] = "#{t('no_batch_selected')}"
      unless params[:date_id].nil? or params[:date_id].empty?
        date_id = params[:date_id]
        @date = FinanceFeeCollection.find(date_id)
        @batches = @date.batches
      end
      render :update do |page|
        page.replace_html "batchs1", :partial => "fees_collection_batches"
        page.replace_html "error", :partial => "error_message"
      end
    end
  end
  
  
  def download_receipt
    auto_generate_voucher = false
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'

    row_1 = ["Voucher Date","Voucher Number","Voucher Type","Debit Ledger","Debit Amount","Cost Centre","Credit Ledger","Credit Amount","Cost Centre","Narration","bill wise details(refno)"]

    # Create a new Workbook
    new_book = Spreadsheet::Workbook.new

    # Create the worksheet
    new_book.create_worksheet :name => 'Journal'

    # Add row_1
    new_book.worksheet(0).insert_row(0, row_1)
    
    vtype = 'Journal'
    ind = 1
    
    unless params[:batches].nil? or params[:batches].empty?
      particulars = ['particular','discount','late']
      unless params[:particulars].nil?
        particulars = params[:particulars].split(",")
      end
      
      transaction_export_from_date = Date.today.to_date.strftime("%Y-%m-%d")
      transaction_export_to_date = Date.today.to_date.strftime("%Y-%m-%d")
      
      unless params[:from_date].nil?
        #transaction_date = params[:export_date].to_date.strftime("%m/%d/%Y")
        transaction_export_from_date = params[:from_date].to_date.strftime("%Y-%m-%d")
      end
      unless params[:to_date].nil?
        #transaction_date = params[:export_date].to_date.strftime("%m/%d/%Y")
        transaction_export_to_date = params[:to_date].to_date.strftime("%Y-%m-%d")
      end
      
      @date    = @fee_collection =  FinanceFeeCollection.find(params[:date_id])
      batches = params[:batches].split(",")
      batches.each do |batch_id|
        @batch   = Batch.find(batch_id)
        
        @type    = params[:type]
        
        if @type.to_i == 0
          unless params[:students].nil? or params[:students].empty?
            student_ids = params[:students]
          else
            student_ids = @date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
          end
          @fees = FinanceFee.all(:conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(students.id,'#{ student_ids}') and batches.id = #{@batch.id}" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id INNER JOIN batches ON batches.id = students.batch_id')
        else
          student_ids = @date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
          @fees = FinanceFee.all(:conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
        end
        
        @dates   = @batch.finance_fee_collections
        
        @fees_data = @fees #.select{|f| f.is_paid}
        
        @due_date = @fee_collection.start_date
        if auto_generate_voucher
          voucher_no = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
        else
          #voucher_no = @due_date.strftime "%b-%y" 
          voucher_no = @due_date.strftime "%M%Y" + "-" 
        end
        
        unless @fees_data.nil?
          
          @fees_data.each do |fee|
            @paid_fees = fee.finance_transactions.find(:all, :conditions => "transaction_date BETWEEN '#{transaction_export_from_date}' and '#{transaction_export_to_date}'")
            unless @paid_fees.blank?
              student ||= fee.student
              student ||= fee.student
              
              to = student.admission_no.gsub("SJW","")
              to = to.gsub("FC","")
              to = to.gsub("MC","")
              to = to.gsub("-","")

              student_admission_no = student.admission_no.gsub("SJW","")
              student_admission_no = student_admission_no.gsub("FC","")
              student_admission_no = student_admission_no.gsub("MC","")
              student_admission_no = student_admission_no.gsub("-","")
              
              @paid_fees.each do |paid_fees|
                description = paid_fees.title
                transaction_date = paid_fees.transaction_date
                trans_date = transaction_date.strftime("%e-%b-%Y")
                
                vtype = 'Receipt'
                if paid_fees.payment_mode.nil? or paid_fees.payment_mode.blank? or paid_fees.payment_mode.empty?
                  type = "PBL-STD A/C - 877"
                else
                  type = paid_fees.payment_mode
                end

                #s_initial = "PBL-STD A/C - 877"
                #vn = student_admission_no + "-" + voucher_no + s_initial
                vn = student_admission_no + "-" + vtype
                #type = 'PBL-STD A/C - 877'

                amount = paid_fees.amount

                dt_due = @due_date.strftime "%M%Y";
                bill = student_admission_no + "-CASH-" + dt_due;

                #row_new = [transaction_date, vn, vtype, type, amount, "", student_admission_no, amount, "",description, bill]
                #new_book.worksheet(0).insert_row(ind, row_new)
                #ind += 1

                @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==@batch) }

                unless @fee_particulars.nil?
                    @fee_particulars.each do |fee_p|
                      if fee_p.name.downcase != 'vat'
                        description = fee_p.name + " payment for " + student.full_name
                        fee_name = fee_p.name;
                        paidFess = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Particular' AND transaction_type = 'Fee Collection' AND finance_transaction_id = " + paid_fees.id.to_s + " AND particular_id = #{fee_p.id}")
                        paidAmount = 0
                        unless paidFess.blank?
                          paidAmount += paidFess.map(&:amount).sum.to_f
                          paidFess = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Particular' AND transaction_type = 'Advance' AND finance_transaction_id  = " + paid_fees.id.to_s + " AND particular_id = #{fee_p.id}")
                          unless paidFess.blank?
                            paidAmount += paidFess.map(&:amount).sum.to_f
                          end
                          if paidAmount.to_i > 0
                            unless fee_name.capitalize.include? "due"
                              #s_initial = "PBL-STD A/C - 877"
                              #vn = student_admission_no + "-" + voucher_no + s_initial
                              vn = student_admission_no + "-" + vtype
                              #type = 'PBL-STD A/C - 877'

                              amount = paidAmount

                              dt_due = @due_date.strftime "%M%Y";
                              if fee_p.name.downcase.include? "tuition"
                                bill = transaction_date.strftime("%b") + "-" + transaction_date.strftime("%y");
                              elsif fee_p.name.downcase.include? "transport"
                                bill = "Transport-" + transaction_date.strftime("%b") + "-" + transaction_date.strftime("%y");
                              elsif fee_p.name.downcase.include? "piano"
                                bill = "Piano-" + transaction_date.strftime("%b") + "-" + transaction_date.strftime("%y");
                              end

                              row_new = [transaction_date, vn, vtype, type, amount, "", student_admission_no, amount, fee_name,description, bill]
                              new_book.worksheet(0).insert_row(ind, row_new)
                              ind += 1

                            end
                          end
                        end
                      end
                    end
                end
                
                paidFess = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Adjustment' AND transaction_type = 'Discount' AND finance_transaction_id = " + paid_fees.id.to_s + "")
                paidAmount = 0
                unless paidFess.blank?
                  paidAmount += paidFess.map(&:amount).sum.to_f
                  vn = student_admission_no + "-" + vtype
                  description = "Discount for " + student.full_name
                  amount = paidAmount
                  bill = "Discount"

                  row_new = [transaction_date, vn, vtype, type, amount, "", student_admission_no, amount, "Discount",description, bill]
                  new_book.worksheet(0).insert_row(ind, row_new)
                  ind += 1
                end
                
                paidFess = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'VAT' AND finance_transaction_id = " + paid_fees.id.to_s + "")
                paidAmount = 0
                unless paidFess.blank?
                  paidAmount += paidFess.map(&:amount).sum.to_f
                  
                  vn = student_admission_no + "-" + vtype
                  description = "Vat for " + student.full_name
                  amount = paidAmount
                  bill = "VAT"

                  row_new = [transaction_date, vn, vtype, type, amount, "", student_admission_no, amount, "VAT",description, bill]
                  new_book.worksheet(0).insert_row(ind, row_new)
                  ind += 1
                end
                
                paidFess = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Fine' AND finance_transaction_id = " + paid_fees.id.to_s + "")
                paidAmount = 0
                unless paidFess.blank?
                  paidAmount += paidFess.map(&:amount).sum.to_f
                  
                  description = "Fine for " + student.full_name
                  vn = student_admission_no + "-" + vtype
                  amount = paidAmount
                  bill = "Late Fine"

                  row_new = [transaction_date, vn, vtype, type, amount, "", student_admission_no, amount, "Late Fine",description, bill]
                  new_book.worksheet(0).insert_row(ind, row_new)
                  ind += 1
                end
                
                paidFess = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'FineAdjustment' and transaction_type = 'Discount' AND finance_transaction_id = " + paid_fees.id.to_s + "")
                paidAmount = 0
                unless paidFess.blank?
                  paidAmount += paidFess.map(&:amount).sum.to_f
                  
                  description = "Fine Discount for " + student.full_name
                  vn = student_admission_no + "-" + vtype
                  amount = paidAmount
                  bill = "Late Fine Discount"

                  row_new = [transaction_date, vn, vtype, type, amount, "", student_admission_no, amount, "Late Fine Discount",description, bill]
                  new_book.worksheet(0).insert_row(ind, row_new)
                  ind += 1
                end
              end
            end
          end
        end
      end
      
      spreadsheet = StringIO.new 
      new_book.write spreadsheet 

      send_data spreadsheet.string, :filename => @date.full_name + ".xls", :type =>  "application/vnd.ms-excel"
    else
      flash[:notice] = "#{t('no_batch_selected')}"
      unless params[:date_id].nil? or params[:date_id].empty?
        date_id = params[:date_id]
        @date = FinanceFeeCollection.find(date_id)
        @batches = @date.batches
      end
      render :update do |page|
        page.replace_html "batchs1", :partial => "fees_collection_batches"
        page.replace_html "error", :partial => "error_message"
      end
    end
  end
  
  def load_fees_submission_batch
    @batch   = Batch.find(params[:batch_id])
    @date    = @fee_collection =  FinanceFeeCollection.find(params[:date])
    @option  = params[:opt]
    
    student_ids = @date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
    
    @dates   = @batch.finance_fee_collections
    

    @fees = FinanceFee.all(:select => "finance_fees.id, finance_fees.fee_collection_id, students.id as student_id, finance_fees.is_paid, students.first_name, students.middle_name, students.last_name", :conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
    
    unless @fees.nil?
      @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
      
      @student ||= @fee.student
      @prev_student = @student.previous_fee_student(@date.id,student_ids)
      @next_student = @student.next_fee_student(@date.id,student_ids)
      @financefee = @student.finance_fee_by_date @date
      @due_date = @fee_collection.due_date
      @paid_fees = @fee.finance_transactions
      
      @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])

      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      
      @discounts=@date.fee_discounts.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }

      @total_discount = 0
      @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
      @total_discount =@discounts.map{|d| @total_payable * d.discount.to_f/(d.is_amount? ? @total_payable : 100)}.sum.to_f unless @discounts.nil?
      bal=(@total_payable-@total_discount).to_f
      days=(Date.today-@date.due_date.to_date).to_i
      auto_fine=@date.fine
      if days > 0 and auto_fine
        @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
        @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
      end
      @fine_amount=0 if @financefee.is_paid
        
      render :update do |page|
        page.replace_html "student", :partial => "student_fees_submission"
      end
    else
      render :update do |page|
        page.replace_html "student", :text => '<p class="flash-msg">No students have been assigned this fee.</p>'
      end
    end
  end
  
  def load_student_details
    @batch   = Batch.find(params[:batch_id])
    @date    =  @fee_collection = FinanceFeeCollection.find(params[:date])
    student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')

    @dates   = @batch.finance_fee_collections


    if params[:student]
      @student = Student.find(params[:student])
      @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{@student.id}'")
    else
      @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
    end
    
    unless @fee.nil?

      @student ||= @fee.student
      @prev_student = @student.previous_fee_student(@date.id,student_ids)
      @next_student = @student.next_fee_student(@date.id,student_ids)
      @financefee = @student.finance_fee_by_date @date
      @due_date = @fee_collection.due_date
      @paid_fees = @fee.finance_transactions
      @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])

      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      @discounts=@date.fee_discounts.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }

      @total_discount = 0
      @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
      @total_discount =@discounts.map{|d| @total_payable * d.discount.to_f/(d.is_amount? ? @total_payable : 100)}.sum.to_f unless @discounts.nil?
      bal=(@total_payable-@total_discount).to_f
      days=(Date.today-@date.due_date.to_date).to_i
      auto_fine=@date.fine
      if days > 0 and auto_fine
        @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
        @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
      end
      @fine_amount=0 if @financefee.is_paid
      render :update do |page|
        page.replace_html "student_details", :partial => "student_details"
      end
    else
      render :update do |page|
        page.replace_html "student", :text => '<p class="flash-msg">No more student found...</p>'
      end
    end
  end
  
  private
  
  def calculate_discount(date,batch,student)
    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []

    one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
    @onetime_discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
    if @onetime_discounts.length > 0
      one_time_total_amount_discount= true
      @onetime_discounts_amount = []
      @onetime_discounts.each do |d|
        @onetime_discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
        @total_discount = @total_discount + @onetime_discounts_amount[d.id]
      end
    else
      @onetime_discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      if @onetime_discounts.length > 0
        one_time_discount = true
        @onetime_discounts_amount = []
        i = 0
        @onetime_discounts.each do |d|   
          onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
          fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
          discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
          @onetime_discounts_amount[d.id] = discount_amt
          @total_discount = @total_discount + discount_amt
          i = i + 1
        end
      end
    end

    unless one_time_total_amount_discount
      if onetime_discount_particulars_id.empty?
        onetime_discount_particulars_id[0] = 0
      end
      discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      if discounts_on_particulars.length > 0
        @discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @discounts_amount = []
        @discounts.each do |d|   
          fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
          discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
          @discounts_amount[d.id] = discount_amt
          @total_discount = @total_discount + discount_amt
        end
      else  
        unless one_time_discount
          @discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          @discounts_amount = []
          @discounts.each do |d|
            @discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount = @total_discount + @discounts_amount[d.id]
          end
        end
      end
    end
  end
  
  def calculate_extra_fine(date,fine_rule)
    extra_fine = 0
    if MultiSchool.current_school.id == 340
      #GET THE NEXT ALL months 
      extra_fine = 0
      other_months = FinanceFeeCollection.find(:all, :conditions => ["due_date < ?", date.due_date], :order => "due_date asc")
      unless other_months.nil? or other_months.empty?
        other_months.each do |other_month|
          fine_amount = fine_rule.fine_amount if fine_rule
          extra_fine = extra_fine + fine_amount
        end
      end
    end
    return extra_fine
  end
  
  def get_fine_discount(date,batch,student,fine_amount)
    total_fine_discount = 0.00
    if fine_amount > 0
      new_fine_amount = fine_amount
      fee_collection_discount_ids = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id_and_is_late(date.id, batch.id, true).map(&:fee_discount_id)
      unless fee_collection_discount_ids.nil? or fee_collection_discount_ids.empty?
        discounts_on_lates = FeeDiscount.find(:all, :conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{true} and id IN (" + fee_collection_discount_ids.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if discounts_on_lates.length > 0
          discounts_on_lates.each do |d|   
            if fine_amount > 0
              discount_amt = new_fine_amount * d.discount.to_f/ (d.is_amount?? new_fine_amount : 100)
              fine_amount = fine_amount - discount_amt
              if fine_amount < 0
                discount_amt = 0
              end
            end
            total_fine_discount += discount_amt
          end
        end
      end
    end
    return total_fine_discount
  end
  
end
