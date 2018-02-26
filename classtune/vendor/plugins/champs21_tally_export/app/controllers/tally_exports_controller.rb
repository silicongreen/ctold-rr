class TallyExportsController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only => [:index]
  filter_access_to :all
  in_place_edit_with_validation_for :tally_company, :company_name
  in_place_edit_with_validation_for :tally_account, :account_name
  in_place_edit_with_validation_for :tally_voucher_type, :voucher_name

  def index
    
  end

  def settings

  end

  def general_settings
    @config = TallyExportConfiguration.get_multiple_configs_as_hash ['TallyUrl', 'EnableLiveSync', 'LiveSyncStartDate']

    if request.post?
      configs = params[:tally_export_configuration]
      if configs[:enable_live_sync] == '1'
        configs[:live_sync_start_date] = I18n.l(Date.today, :format=>:default) if @config[:live_sync_start_date].blank?
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
    @batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>true},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
    @inactive_batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>false},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
    @dates = []
  end
  
  def export_receipt
    @option = 'receipt'
    @batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>true},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
    @inactive_batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>false},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
    @dates = []
  end
  
  def update_fees_collection_dates
    @batch = Batch.find(params[:batch_id])
    @dates = @batch.finance_fee_collections
    @option = params[:opt]
    render :update do |page|
      page.replace_html "fees_collection_dates", :partial => "fees_collection_dates"
    end
  end
  
  def download_journal
    @batch   = Batch.find(params[:batch_id])
    @date    = @fee_collection =  FinanceFeeCollection.find(params[:date])
    @type    = params[:type]
    
    if @type.to_i == 0
      student_ids = params[:students]
      @fees = FinanceFee.all(:conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(finance_fees.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
    else
      student_ids = @date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
      @fees = FinanceFee.all(:conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
    end
    
    @dates   = @batch.finance_fee_collections
    
    if @type.to_i == 2
      @fees_data = @fees.select{|f| f.is_paid}
    elsif @type.to_i == 3
     @fees_data = @fees.select{|f| !f.is_paid}
    else
      @fees_data = @fees
    end
    
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    
    row_1 = ["Date","Voucher No","vType","By","To","Amount","Narration"]
    
    # Create a new Workbook
    new_book = Spreadsheet::Workbook.new

    # Create the worksheet
    new_book.create_worksheet :name => 'Journal'

    # Add row_1
    new_book.worksheet(0).insert_row(0, row_1)
    
    unless @fees_data.nil?
      ind = 1
      @fees_data.each do |fee|
          student ||= fee.student
          @financefee = student.finance_fee_by_date @date
          voucher_no = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
          @due_date = @fee_collection.due_date
          if @financefee.is_paid
            @paid_fees = fee.finance_transactions
            total_amount = 0
            unless @paid_fees.blank?
              description = @paid_fees[0].title
              @paid_fees.each do |trans|
                total_amount += trans.amount
              end
            end
            vtype = 'Journal'
            by = student.admission_no + " - " + student.full_name
            to = "TutionFees"
            row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no, vtype, by, to, total_amount, description]
            new_book.worksheet(0).insert_row(ind, row_new)
            ind += 1
            
          
            @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==@batch) }
            amount_paid = 0
            unless @fee_particulars.nil?
              @fee_particulars.each do |fee_p|
                voucher_no_new = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
                description = fee_p.name.capitalize + " payment for " + student.full_name
                vtype = 'Journal'
                by = student.admission_no + " - " + student.full_name
                to = fee_p.name
                total_amount = fee_p.amount
                amount_paid += total_amount
                row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no_new, vtype, by, to, total_amount, description]
                new_book.worksheet(0).insert_row(ind, row_new)
                ind += 1
              end
            end
            @discounts=@date.fee_discounts.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==@batch) }
            @total_discount = 0
            @total_discount =@discounts.map{|d| @total_payable * d.discount.to_f/(d.is_amount? ? @total_payable : 100)}.sum.to_f unless @discounts.nil?
            unless @total_discount == 0
              @discounts.each do |d|
                voucher_no_new = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
                discount_text = d.is_amount == true ? "#{d.name}" : "#{d.name}&#x200E; (#{d.discount})% &#x200E;"
                description = discount_text + " for " + student.full_name
                vtype = 'Journal'
                by = student.admission_no + " - " + student.full_name
                to = discount_text
                total_amount = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
                amount_paid -= total_amount
                row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no_new, vtype, by, to, total_amount, description]
                new_book.worksheet(0).insert_row(ind, row_new)
                ind += 1
              end
            end
            @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
            bal=(@total_payable-@total_discount).to_f
            days=(Date.today-@date.due_date.to_date).to_i
            auto_fine=@date.fine
            if days > 0 and auto_fine
              @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
              @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
            end
            if @fine_rule
                voucher_no_new = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
                description = "Fine"
                vtype = 'Journal'
                by = student.admission_no + " - " + student.full_name
                to = "Fine"
                total_amount = @fine_amount=@fine_rule
                amount_paid += total_amount
                row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no_new, vtype, by, to, total_amount, description]
                new_book.worksheet(0).insert_row(ind, row_new)
                ind += 1
            end
            
            amnt = 0
            fine_found = false
            voucher_no_new = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
            unless @paid_fees.blank?
              description = @paid_fees[0].title + " - Fine"
              @paid_fees.each do |trans|
                if trans.fine_included
                  amnt += trans.fine_amount
                  fine_found = true
                end
              end
            end
            if fine_found && amnt > 0
              vtype = 'Journal'
              by = student.admission_no + " - " + student.full_name
              to = "Fine"
              row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no, vtype, by, to, total_amount, description]
              new_book.worksheet(0).insert_row(ind, row_new)
              ind += 1
            end
            
            amnt = 0
            vat_found = false
            voucher_no_new = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
            unless @paid_fees.blank?
              description = @paid_fees[0].title + " - VAT"
              @paid_fees.each do |trans|
                if trans.vat_included
                  amnt += trans.vat_amount
                  vat_found = true
                end
              end
            end
            
            if vat_found && amnt > 0
              vtype = 'Journal'
              by = student.admission_no + " - " + student.full_name
              to = "VAT"
              row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no, vtype, by, to, total_amount, description]
              new_book.worksheet(0).insert_row(ind, row_new)
              ind += 1
            end
          else
            description = "Total Payable fees for " + student.full_name
            if @financefee.balance == 0
              @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==@batch) }
              @discounts=@date.fee_discounts.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==@batch) }
              total_amount = 0
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
              total_amount = bal + @fine_amount.to_f
            else
              total_amount = @financefee.balance
            end
            vtype = 'Journal'
            by = student.admission_no + " - " + student.full_name
            to = "TutionFees"

            row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no, vtype, by, to, total_amount, description]
            new_book.worksheet(0).insert_row(ind, row_new)
            ind += 1
            
            @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==@batch) }
            amount_paid = 0
            unless @fee_particulars.nil?
              @fee_particulars.each do |fee_p|
                voucher_no_new = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
                description = fee_p.name.capitalize + " payment for " + student.full_name
                vtype = 'Journal'
                by = student.admission_no + " - " + student.full_name
                to = fee_p.name
                total_amount = fee_p.amount
                amount_paid += total_amount
                row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no_new, vtype, by, to, total_amount, description]
                new_book.worksheet(0).insert_row(ind, row_new)
                ind += 1
              end
            end
            @discounts=@date.fee_discounts.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==@batch) }
            @total_discount = 0
            @total_discount =@discounts.map{|d| @total_payable * d.discount.to_f/(d.is_amount? ? @total_payable : 100)}.sum.to_f unless @discounts.nil?
            unless @total_discount == 0
              @discounts.each do |d|
                voucher_no_new = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
                discount_text = d.is_amount == true ? "#{d.name}" : "#{d.name}&#x200E; (#{d.discount})% &#x200E;"
                description = discount_text + " for " + student.full_name
                vtype = 'Journal'
                by = student.admission_no + " - " + student.full_name
                to = discount_text
                total_amount = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
                amount_paid -= total_amount
                row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no_new, vtype, by, to, total_amount, description]
                new_book.worksheet(0).insert_row(ind, row_new)
                ind += 1
              end
            end
            @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
            bal=(@total_payable-@total_discount).to_f
            days=(Date.today-@date.due_date.to_date).to_i
            auto_fine=@date.fine
            if days > 0 and auto_fine
              @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
              @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
            end
            if @fine_rule
                voucher_no_new = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
                description = "Fine"
                vtype = 'Journal'
                by = student.admission_no + " - " + student.full_name
                to = "Fine"
                total_amount = @fine_amount=@fine_rule
                amount_paid += total_amount
                row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no_new, vtype, by, to, total_amount, description]
                new_book.worksheet(0).insert_row(ind, row_new)
                ind += 1
            end
          end
          
      end
    end
    
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    
    send_data spreadsheet.string, :filename => @date.full_name + ".xls", :type =>  "application/vnd.ms-excel"
  end
  
  def download_receipt
    @batch   = Batch.find(params[:batch_id])
    @date    = @fee_collection =  FinanceFeeCollection.find(params[:date])
    @type    = params[:type]
    
    if @type.to_i == 0
      student_ids = params[:students]
      @fees = FinanceFee.all(:conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(finance_fees.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
    else
      student_ids = @date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
      @fees = FinanceFee.all(:conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
    end
    
    @dates   = @batch.finance_fee_collections
    
    #For Receipt we only need Paid User, So I am Removing the others 
    @fees_data = @fees.select{|f| f.is_paid}
    
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    
    row_1 = ["Date","Voucher No","vType","Type","To","Amount","Narration"]
    
    # Create a new Workbook
    new_book = Spreadsheet::Workbook.new

    # Create the worksheet
    new_book.create_worksheet :name => 'Receipt'

    # Add row_1
    new_book.worksheet(0).insert_row(0, row_1)
    
    unless @fees_data.nil?
      ind = 1
      @fees_data.each do |fee|
          student ||= fee.student
          @financefee = student.finance_fee_by_date @date
          
          @due_date = @fee_collection.due_date
          if @financefee.is_paid
            @paid_fees = fee.finance_transactions
            
            unless @paid_fees.blank?
              description = @paid_fees[0].title
              @paid_fees.each do |trans|
                voucher_no = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
                vtype = 'Receipt'
                if trans.payment_mode.nil? or trans.payment_mode.blank? or trans.payment_mode.empty?
                  type = "Cash"
                else
                  type = trans.payment_mode
                end
                to = student.admission_no + " - " + student.full_name
                amount = trans.amount
                description = trans.title
                
                row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no, vtype, type, to, amount, description]
                new_book.worksheet(0).insert_row(ind, row_new)
                ind += 1
              end
            end
            
            if @paid_fees[0].payment_mode.nil? or @paid_fees[0].payment_mode.blank? or @paid_fees[0].payment_mode.empty?
              type = "Cash"
            else
              type = @paid_fees[0].payment_mode
            end
            @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==@batch) }
            unless @fee_particulars.nil?
              @fee_particulars.each do |fee_p|
                voucher_no_new = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
                description = fee_p.name.capitalize + " payment for " + student.full_name
                vtype = 'Receipt'
                to = student.admission_no + " - " + student.full_name
                total_amount = fee_p.amount
                row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no_new, vtype, type, to, total_amount, description]
                new_book.worksheet(0).insert_row(ind, row_new)
                ind += 1
              end
            end
            
            @discounts=@date.fee_discounts.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==@batch) }
            @total_discount = 0
            @total_discount =@discounts.map{|d| @total_payable * d.discount.to_f/(d.is_amount? ? @total_payable : 100)}.sum.to_f unless @discounts.nil?
            unless @total_discount == 0
              @discounts.each do |d|
                voucher_no_new = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
                discount_text = d.is_amount == true ? "#{d.name}" : "#{d.name}&#x200E; (#{d.discount})% &#x200E;"
                description = discount_text + " for " + student.full_name
                vtype = 'Receipt'
                to = student.admission_no + " - " + student.full_name
                total_amount = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
                amount_paid -= total_amount
                row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no_new, vtype, type, to, total_amount, description]
                new_book.worksheet(0).insert_row(ind, row_new)
                ind += 1
              end
            end
            @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
            bal=(@total_payable-@total_discount).to_f
            days=(Date.today-@date.due_date.to_date).to_i
            auto_fine=@date.fine
            if days > 0 and auto_fine
              @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
              @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
            end
            if @fine_rule
                voucher_no_new = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
                description = "Fine"
                vtype = 'Receipt'
                to = student.admission_no + " - " + student.full_name
                total_amount = @fine_amount=@fine_rule
                amount_paid += total_amount
                row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no_new, vtype, type, to, total_amount, description]
                new_book.worksheet(0).insert_row(ind, row_new)
                ind += 1
            end
            
            amnt = 0
            fine_found = false
            voucher_no = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
            unless @paid_fees.blank?
              description = @paid_fees[0].title + " - Fine"
              @paid_fees.each do |trans|
                if trans.fine_included
                  amnt += trans.fine_amount
                  fine_found = true
                end
              end
            end
            if fine_found && amnt > 0
              vtype = 'Receipt'
              to = student.admission_no + " - " + student.full_name
              row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no, vtype, type, to, total_amount, description]
              new_book.worksheet(0).insert_row(ind, row_new)
              ind += 1
            end
            
            amnt = 0
            vat_found = false
            voucher_no = (0...8).map { (65 + rand(26)).chr }.join.to_s + @financefee.id.to_s
            unless @paid_fees.blank?
              description = @paid_fees[0].title + " - VAT"
              @paid_fees.each do |trans|
                if trans.vat_included
                  amnt += trans.vat_amount
                  vat_found = true
                end
              end
            end
            
            if vat_found && amnt > 0
              vtype = 'Receipt'
              to = student.admission_no + " - " + student.full_name
              row_new = [@due_date.to_date.strftime("%m/%d/%Y"), voucher_no, vtype, type, to, total_amount, description]
              new_book.worksheet(0).insert_row(ind, row_new)
              ind += 1
            end
          end
          
      end
    end
    
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    
    send_data spreadsheet.string, :filename => @date.full_name + ".xls", :type =>  "application/vnd.ms-excel"
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

end
