#Champs21
#Copyright 2011 teamCreative Private Limited
#
#This product includes software developed at
#Project Champs21 - http://www.champs21.com/
#
#Licensed under the Apache License, Version 2.0 (the "License");
#you may not use this file except in compliance with the License.
#You may obtain a copy of the License at
#
#  http://www.apache.org/licenses/LICENSE-2.0
#
#Unless required by applicable law or agreed to in writing, software
#distributed under the License is distributed on an "AS IS" BASIS,
#WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#See the License for the specific language governing permissions and
#limitations under the License.

 
class FinanceController < ApplicationController
  before_filter :login_required,:configuration_settings_for_finance
  #before_filter :check_permission,:only=>[:index,:fees_index,:categories,:transactions,:donation,:automatic_transactions,:payslip_index,:asset_liability,:finance_reports]
  before_filter :set_precision
  filter_access_to :all

  def index
    @hr = Configuration.find_by_config_value("HR")
    
    permitted_modules = Rails.cache.fetch("permitted_modules_finance_#{current_user.id}"){
      @finance_modules_tmp = []
      @a_user_modules = ['finance_text']
      menu_links = MenuLink.find_by_name(@a_user_modules)
      menu_id = menu_links.id
      menu_links = MenuLink.find_all_by_higher_link_id(menu_id)
      
      menu_links.each do |menu_link|
        if menu_link.link_type=="user_menu"
          menu_id = menu_link.id

          school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")

          if school_menu_links.nil? or school_menu_links.blank?
            @finance_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => false}
          else
            @finance_modules_tmp << {'name' => menu_link.name, "target_controller" => menu_link.target_controller, "target_action" => menu_link.target_action, 'visible' => true}
          end
        end
      end
      @finance_modules_tmp
    }
    @finance_modules = permitted_modules
  end

  def automatic_transactions
    @cat_names = ["'Fee'","'Salary'"]
    Champs21Plugin::FINANCE_CATEGORY.each do |category|
      @cat_names << "'#{category[:category_name]}'"
    end
    @triggers = FinanceTransactionTrigger.all
    @cat_names = @cat_names.reject { |c| c.to_s.empty? }
    if @cat_names.blank?
      @cat_names[0] = 0
    end
    @categories = FinanceTransactionCategory.find(:all ,:conditions => ["name NOT IN (#{@cat_names.join(',')}) and is_income=1 and deleted=0 "])
  end

  def donation
    @donation = FinanceDonation.new(params[:donation])
    if request.post? and @donation.save
      flash[:notice] = "#{t('flash1')}"
      redirect_to :action => 'donation_receipt', :id => @donation.id
    end
  end

  def donation_receipt
    @donation = FinanceDonation.find(params[:id])
  end

  def donation_edit
    @donation = FinanceDonation.find(params[:id])
    #@transaction = FinanceTransaction.find(@donation.transaction_id)
    if request.post? and @donation.update_attributes(params[:donation])
      donor = "#{t('flash15')} #{params[:donation][:donor]}"
      #FinanceTransaction.update(@transaction.id, :description => params[:donation][:description], :title=>donor, :amount=>params[:donation][:amount], :transaction_date=>@donation.transaction_date)
      redirect_to :action => 'donors'
      flash[:notice] = "#{t('flash16')}"
    end
  end

  def donation_delete
    @donation = FinanceDonation.find(params[:id])
    @transaction = FinanceTransaction.find(@donation.transaction_id)
    if  @donation.destroy
      @transaction.destroy
      redirect_to :action => 'donors'
      flash[:notice] = "#{t('flash25')}"
    end
  end
  
  def donation_receipt_pdf
    @donation = FinanceDonation.find(params[:id])
    @currency_type = currency
    render :pdf => 'donation_receipt_pdf'

  end

  def donors
    @donations = FinanceDonation.find(:all, :order => 'transaction_date desc')
  end

  def expense_create
    @finance_transaction = FinanceTransaction.new
    @categories = FinanceTransactionCategory.expense_categories
    if @categories.empty?
      flash[:notice] = "#{t('flash2')}"
    end
    if request.post?
      @finance_transaction = FinanceTransaction.new(params[:finance_transaction])
      if @finance_transaction.save
        flash[:notice] = "#{t('flash3')}"
        redirect_to :action=>"expense_create"
      else
        render :action=>"expense_create"
      end
    end
  end

  def expense_edit
    @transaction = FinanceTransaction.find(params[:id])
    @categories = FinanceTransactionCategory.all(:conditions =>"name != 'Salary' and is_income = false and deleted = false" )
    if request.post? and @transaction.update_attributes(params[:transaction])
      flash[:notice] = "#{t('flash4')}"
      redirect_to  :action=>:expense_list
    end
  end

  def expense_list
  end

  def expense_list_update
    if params[:start_date].to_date > params[:end_date].to_date
      flash[:warn_notice] = "#{t('flash17')}"
      redirect_to :action => 'expense_list'
    end
    @start_date = (params[:start_date]).to_date
    @end_date = (params[:end_date]).to_date
    @expenses = FinanceTransaction.expenses(@start_date,@end_date)
  end

  def expense_list_pdf
    if date_format_check
      @currency_type = currency
      @expenses = FinanceTransaction.expenses(@start_date,@end_date)
      render :pdf => 'expense_list_pdf'
    end
  end

  def income_createstudent_fee_receipt_pdf
    @finance_transaction = FinanceTransaction.new()
    @categories = FinanceTransactionCategory.income_categories
    if @categories.empty?
      flash[:notice] = "#{t('flash5')}"
    end
    if request.post?
      @finance_transaction = FinanceTransaction.new(params[:finance_transaction])
      if @finance_transaction.save
        flash[:notice] = "#{t('flash6')}"
        redirect_to :action=>"income_create"
      else
        render :action=>"income_create"
      end
    end
  end

  def monthly_income

  end

  def income_edit
    @cat_names = ["'Fee'","'Salary'","'Donation'"]
    Champs21Plugin::FINANCE_CATEGORY.each do |category|
      @cat_names << "'#{category[:category_name]}'"
    end
    @transaction = FinanceTransaction.find(params[:id])
    @cat_names = @cat_names.reject { |c| c.to_s.empty? }
    if @cat_names.blank?
      @cat_names[0] = 0
    end
    @categories = FinanceTransactionCategory.all(:conditions => "is_income=true and name NOT IN (#{@cat_names.join(',')}) and deleted = false")
    if request.post? and @transaction.update_attributes(params[:transaction])
      flash[:notice] = "#{t('flash7')}"
      redirect_to :action=> 'income_list'
    else
      render :income_edit
    end
  end

  def income_list
  end

  def delete_transaction
    @transaction = FinanceTransaction.find_by_id(params[:id])
    income = @transaction.category.is_income?
    if income
      auto_transactions = FinanceTransaction.find_all_by_master_transaction_id(params[:id])
      auto_transactions.each { |a| a.destroy } unless auto_transactions.nil?
    end
    @transaction.destroy
    flash[:notice]="#{t('flash18')}"
    if income
      redirect_to :action=>'income_list'
    else
      redirect_to :action=>'expense_list'
    end


  end

  def income_list_update
    @start_date = (params[:start_date]).to_date
    @end_date = (params[:end_date]).to_date
    @incomes = FinanceTransaction.incomes(@start_date,@end_date)
  end

  def income_details
    if date_format_check

      @income_category = FinanceTransactionCategory.find(params[:id])
      @incomes = @income_category.finance_transactions.find(:all,:conditions => ["transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'"])

    end
  end

  def income_list_pdf
    if date_format_check
      @currency_type = currency
      @incomes = FinanceTransaction.incomes(@start_date,@end_date)
      render :pdf => 'income_list_pdf', :zoom=>0.68#, :show_as_html=>true
    end
  end

  def income_details_pdf
    if date_format_check
      @income_category = FinanceTransactionCategory.find(params[:id])
      @incomes = @income_category.finance_transactions.find(:all,:conditions => ["transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'"])
      render :pdf => 'income_details_pdf'
    end
  end

  def categories
    @categories = FinanceTransactionCategory.all(:conditions => {:deleted => false},:order=>'name asc')
    @fixed_categories = @categories.reject{|c|!c.is_fixed}
    @other_categories = @categories.reject{|c|c.is_fixed}
  end

  def category_new
    @finance_transaction_category = FinanceTransactionCategory.new
  end

  def category_create
    @finance_category = FinanceTransactionCategory.new(params[:finance_category])
    render :update do |page|
      if @finance_category.save
        @categories = FinanceTransactionCategory.all(:conditions => {:deleted => false})
        @fixed_categories = @categories.reject{|c|!c.is_fixed}
        @other_categories = @categories.reject{|c|c.is_fixed}
        page.replace_html 'form-errors', :text => ''
        page << "Modalbox.hide();"
        page.replace_html 'category-list', :partial => 'category_list'
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg35')}</p>"

      else
        page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => @finance_category
        page.visual_effect(:highlight, 'form-errors')
      end
    end
  end

  def category_delete
    @finance_category = FinanceTransactionCategory.find(params[:id])
    @finance_category.update_attributes(:deleted => true)
    @categories = FinanceTransactionCategory.all(:conditions => {:deleted => false})
    @fixed_categories = @categories.reject{|c|!c.is_fixed}
    @other_categories = @categories.reject{|c|c.is_fixed}
  end

  def category_edit
    @finance_category = FinanceTransactionCategory.find(params[:id])
    @categories = FinanceTransactionCategory.all(:conditions => {:deleted => false})
  end

  def category_update
    @finance_category = FinanceTransactionCategory.find(params[:id])
    unless  @finance_category.update_attributes(params[:finance_category])
      @errors=true
    end
    @categories = FinanceTransactionCategory.all(:conditions => {:deleted => false})
    @fixed_categories = @categories.reject{|c|!c.is_fixed}
    @other_categories = @categories.reject{|c|c.is_fixed}
  end

  def transaction_trigger_create
    @trigger = FinanceTransactionTrigger.new(params[:transaction_trigger])
    render :update do |page|
      if @trigger.save
        @triggers = FinanceTransactionTrigger.all
        page.replace_html 'transaction-triggers-list', :partial => 'transaction_triggers_list'
        page.replace_html 'form-errors', :text => ''
        page << "Modalbox.hide();"
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg17')}</p>"

      else
        page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => @trigger
        page.visual_effect(:highlight, 'form-errors')
      end
    end
  end

  def transaction_trigger_edit
    @cat_names = ["'Fee'","'Salary'"]
    Champs21Plugin::FINANCE_CATEGORY.each do |category|
      @cat_names << "'#{category[:category_name]}'"
    end
    @transaction_trigger = FinanceTransactionTrigger.find(params[:id])
    @cat_names = @cat_names.reject { |c| c.to_s.empty? }
    if @cat_names.blank?
      @cat_names[0] = 0
    end
    @categories = FinanceTransactionCategory.find(:all ,:conditions => ["name NOT IN (#{@cat_names.join(',')}) and is_income=1 and deleted=0 "])
  end

  def transaction_trigger_update
    @transaction_trigger = FinanceTransactionTrigger.find(params[:id])
    render :update do |page|
      if @transaction_trigger.update_attributes(params[:transaction_trigger])
        @triggers = FinanceTransactionTrigger.all
        page.replace_html 'transaction-triggers-list', :partial => 'transaction_triggers_list'
        page.replace_html 'form-errors', :text => ''
        page << "Modalbox.hide();"
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg17')}</p>"

      else
        page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => @transaction_trigger
        page.visual_effect(:highlight, 'form-errors')
      end
    end
  end

  def transaction_trigger_delete
    @trigger = FinanceTransactionTrigger.find(params[:id])
    @trigger.destroy
    @triggers = FinanceTransactionTrigger.all
    render :update do |page|
      page.replace_html 'transaction-triggers-list', :partial => 'transaction_triggers_list'
      page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg19')}</p>"
    end
  end

  #transaction-----------------------
  def all_monthly_report_fees
    fixed_category_name
    @fin_start_date = Configuration.find_by_config_key('FinancialYearStartDate').config_value
    @fin_end_date = Configuration.find_by_config_key('FinancialYearEndDate').config_value

    @finance_fee_category = FinanceFeeParticularCategory.find(:all,:conditions => ["is_deleted = ?", false])
    @all_fees_extra_particulers = []
    @all_fees_extra_particulers << "Discount"
    @all_fees_extra_particulers << "Fine"
    @all_fees_extra_particulers << "VAT"
    @transactions_particular = FinanceTransactionParticular.find(:all, :order => 'transaction_date desc', :conditions => ["transaction_date >= '#{@fin_start_date.to_date.strftime("%Y-%m-%d")}' and transaction_date <= '#{@fin_end_date.to_date.strftime("%Y-%m-%d")}'"])
  
    
    start_date = @fin_start_date.to_date
    end_date = @fin_end_date.to_date
    if end_date > Date.today.to_date
      end_date = Date.today.to_date
    end
    number_of_months = (end_date.year*12+end_date.month)-(start_date.year*12+start_date.month)+1
    @dates = number_of_months.times.each_with_object([]) do |count, array|
      month_name_count = start_date.beginning_of_month + count.months
      month_name = month_name_count.to_date.strftime("%b, %y")
      array << [start_date.beginning_of_month + count.months,
        start_date.end_of_month + count.months,month_name]
    end
  end
  
  def date_wise_transaction
    fixed_category_name
#    online_id = []
#        
#    online_payments = Payment.find(:all, :conditions => "`transaction_datetime` LIKE '%2019-01-24%'")
#    online_payments.each do |o|
#      unless o.finance_transaction_id.nil?
#        finance_transaction = FinanceTransaction.find(:first, :conditions => "id = #{o.finance_transaction_id}")
#        unless finance_transaction.nil?
#          if finance_transaction.amount.to_f != o.gateway_response[:amount].to_f
#            online_id << o.id
#          end
#        end 
#      else
#        online_id << o.id
#      end
#    end
#    abort(online_id.inspect)
    
#    online_payments = Payment.find(:all, :conditions => "`transaction_datetime` LIKE '%2019-01-29%'")
#    online_payments.each do |o|
#      unless o.finance_transaction_id.nil?
#        finance_transaction = FinanceTransaction.find(:first, :conditions => "id = #{o.finance_transaction_id}")
#        if finance_transaction.nil? 
#          finance_transaction = FinanceTransaction.find(:first, :conditions => "finance_id = #{o.payment_id} and payee_id = #{o.payee_id}")
#          if finance_transaction.nil? 
#            finance_transaction = FinanceTransaction.find(:all, :conditions => "payee_id = #{o.payee_id} and amount = #{o.gateway_response[:amount]}")
#            if finance_transaction.length == 1
#              finance_transaction.each do |f|
#                finance_transaction_id = f.id
#                finance_fee_id = f.finance_id
#                o.update_attributes(:finance_transaction_id => finance_transaction_id, :payment_id => finance_fee_id)
#              end
#            else
#              online_id << o.id
#            end
#          else
#            o.update_attributes(:finance_transaction_id => finance_transaction.id)
#          end
#        end
#      else
#        online_id << o.id
#      end
#    end
#    abort(online_id.inspect)
#    trans_ids = []
#    p_amount = 0.00
#    a_amount = 0.00
#    d_amount = 0.00
#    #@transactions = FinanceTransaction.find(:all, :conditions => ["payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}'"], :joins => "INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id")
#    @transactions = FinanceTransaction.find(:all, :joins => "INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id")
#    #abort(@transactions.map(&:id).inspect)
#    @transactions.each do |pwt|
#      amount = 0.00
#      @particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{pwt.id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection'"], :group => "finance_transaction_particulars.finance_transaction_id")
#      @particular_wise_transactions.each do |pt|
#        amount += pt.amount.to_f
#        p_amount += pt.amount.to_f
#      end
#      @particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{pwt.id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Advance'"], :group => "finance_transaction_particulars.finance_transaction_id")
#      @particular_wise_transactions.each do |pt|
#        amount += pt.amount.to_f
#        a_amount += pt.amount.to_f
#      end
#      @particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{pwt.id} and finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount'"], :group => "finance_transaction_particulars.finance_transaction_id")
#      @particular_wise_transactions.each do |pt|
#        amount -= pt.amount.to_f
#        d_amount += pt.amount.to_f
#      end
#      if amount.to_f != pwt.amount.to_f
#        trans_ids << pwt.id
#      end
#      #tot_amount += amount
#    end
    #abort(trans_ids.inspect)
    if date_format_check
      unless @start_date > @end_date
        
        @fin_start_date = Configuration.find_by_config_key('FinancialYearStartDate').config_value
        @fin_end_date = Configuration.find_by_config_key('FinancialYearEndDate').config_value
        
        
        @filter_by_course = params[:filter_by_course]
        unless params[:filter_by_course].nil?
          if params[:filter_by_course].to_i == 1
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            school_course_id = school_course_id.reject { |s| s.to_s.empty? }
            if school_course_id.blank?
              school_course_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          elsif params[:filter_by_course].to_i == 2
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          else
            batches = Batch.all.map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          end
        else
          batches = Batch.all.map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
          extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
          extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
          extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          #abort(params.inspect)
        end
        #abort(extra_params.inspect)
        @filter_by_payment_type = params[:filter_by_payment_type]
        unless params[:filter_by_payment_type].nil?
          if params[:filter_by_payment_type].to_i != 0
            filter_by_payment_type = "MB"
            if params[:filter_by_payment_type].to_i == 1
              filter_by_payment_type = "MB"
            elsif params[:filter_by_payment_type].to_i == 2
              filter_by_payment_type = "ITCL"
            end 
            extra_params += ' and gateway_response like \'%%:payment_type: ' + filter_by_payment_type + '%%\''
            #extra_params += " and payments.gateway_response like '%:payment_type: " + filter_by_payment_type + "%'"
            #extra_params += " and payments.gateway_response like '%" + params[:filter_by_payment_type].to_s + "%'"
          end
        end
        
        @particular_wise_transactions_payee = FinanceTransaction.paginate(:all, :select => "finance_transactions.payee_id, finance_transactions.id, sum( finance_transactions.amount ) as amount", :order => 'payments.payee_id ASC', :conditions => ["payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}'" + extra_params], :joins => "INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id " + extra_joins, :group => "finance_transactions.payee_id",:page => params[:page],:per_page => 10)
        #@particular_wise_transactions_payee = FinanceTransactionParticular.paginate(:all, :select => "finance_transactions.payee_id, finance_transactions.id, finance_fee_particular_categories.name, IFNULL(finance_fee_particular_categories.id, 0) as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false}"], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN finance_fee_particulars ON finance_fee_particulars.id = finance_transaction_particulars.particular_id LEFT JOIN finance_fee_particular_categories ON finance_fee_particular_categories.id = finance_fee_particulars.finance_fee_particular_category_id", :group => "finance_transactions.payee_id, finance_transaction_particulars.particular_id",:page => params[:page],:per_page => 10)
        particular_wise_transactions_payee = @particular_wise_transactions_payee.map(&:payee_id)
        particular_wise_transactions_payee = particular_wise_transactions_payee.reject { |p| p.to_s.empty? }
        if particular_wise_transactions_payee.blank?
          particular_wise_transactions_payee[0] = 0
        end
        @particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "finance_transactions.payee_id, finance_transactions.id, finance_fee_particular_categories.name, IFNULL(finance_fee_particular_categories.id, 0) as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transactions.payee_id IN (#{particular_wise_transactions_payee.join(",")}) and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false}"], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN finance_fee_particulars ON finance_fee_particulars.id = finance_transaction_particulars.particular_id LEFT JOIN finance_fee_particular_categories ON finance_fee_particular_categories.id = finance_fee_particulars.finance_fee_particular_category_id", :group => "finance_transactions.payee_id, finance_transaction_particulars.particular_id")
        @student_ids = @particular_wise_transactions.map(&:payee_id).uniq
        
        @transactions_advances = FinanceTransactionParticular.find(:all, :select => "finance_transactions.payee_id, finance_fee_particular_categories.name, IFNULL(finance_fee_particular_categories.id, 0) as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Advance' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false}"], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN finance_fee_particulars ON finance_fee_particulars.id = finance_transaction_particulars.particular_id LEFT JOIN finance_fee_particular_categories ON finance_fee_particular_categories.id = finance_fee_particulars.finance_fee_particular_category_id", :group => "finance_transactions.payee_id, finance_transaction_particulars.particular_id")
        @transactions_discount = FinanceTransactionParticular.find(:all, :select => "finance_transactions.payee_id, finance_fee_particular_categories.name, IFNULL(finance_fee_particular_categories.id, 0) as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false}"], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN fee_discounts ON fee_discounts.id = finance_transaction_particulars.particular_id LEFT JOIN finance_fee_particular_categories ON finance_fee_particular_categories.id = fee_discounts.finance_fee_particular_category_id", :group => "finance_transactions.payee_id, finance_transaction_particulars.particular_id")
        @transactions_discount_total_fees = FinanceTransactionParticular.find(:all, :select => "finance_transactions.payee_id, 'Total Fee Discount' as  name, 0 as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount' and fee_discounts.finance_fee_particular_category_id = 0 and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}'"], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN fee_discounts ON fee_discounts.id = finance_transaction_particulars.particular_id ", :group => "finance_transactions.payee_id, finance_transaction_particulars.particular_id")
        @transactions_fine = FinanceTransactionParticular.find(:all, :select => "finance_transactions.payee_id, 'Fine' as name, 0 as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Fine' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}'"], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id", :group => "finance_transactions.payee_id, finance_transaction_particulars.particular_id")
        
        unless @particular_wise_transactions.blank?
          @finance_particular_categories_id = @particular_wise_transactions.map(&:finance_fee_particular_category_id).uniq
          @finance_particular_categories_id = @finance_particular_categories_id.reject { |f| f.to_s.empty? }
          if @finance_particular_categories_id.blank?
            @finance_particular_categories_id[0] = 0
          end
          @finance_particular_categories = FinanceFeeParticularCategory.find(:all,:conditions => ["is_deleted = ? and id IN (" +  @finance_particular_categories_id.join(",") + ")", false])
        end
        
        
#        @fin_start_date = Configuration.find_by_config_key('FinancialYearStartDate').config_value
#        @fin_end_date = Configuration.find_by_config_key('FinancialYearEndDate').config_value
#        @finance_fee_category = FinanceFeeParticularCategory.find(:all,:conditions => ["is_deleted = ?", false])
#        @all_fees_extra_particulers = []
#        @all_fees_extra_particulers << "Discount"
#        @all_fees_extra_particulers << "Fine"
#        @all_fees_extra_particulers << "VAT"
#        @transactions = FinanceTransaction.find(:all, :order => 'finance_transactions.transaction_date desc', :conditions => ["finance_transactions.transaction_date >= '#{@start_date}' and finance_transactions.transaction_date <= '#{@end_date}'"])
#        @transactions_particular = FinanceTransactionParticular.find(:all, :order => 'finance_transactions.transaction_date desc', :conditions => ["finance_transactions.transaction_date >= '#{@start_date}' and finance_transactions.transaction_date <= '#{@end_date}'"],:include=>"finance_transaction")
      end
    end
  end
  
  def transaction_pdf_fees_csv
    fixed_category_name
    if date_format_check
      unless @start_date > @end_date
        online_id = []
        extra_params = ""
        extra_joins = ""
        @filter_by_course = params[:filter_by_course]
        unless params[:filter_by_course].nil?
          if params[:filter_by_course].to_i == 1
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            school_course_id = school_course_id.reject { |s| s.to_s.empty? }
            if school_course_id.blank?
              school_course_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          elsif params[:filter_by_course].to_i == 2
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          else
            batches = Batch.active.map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          end
        else
          batches = Batch.active.map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
          extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
          extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
          extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
        end
        @filter_by_payment_type = params[:filter_by_payment_type]
        unless params[:filter_by_payment_type].nil?
          if params[:filter_by_payment_type].to_i != 0
            filter_by_payment_type = "MB"
            if params[:filter_by_payment_type].to_i == 1
              filter_by_payment_type = "MB"
            elsif params[:filter_by_payment_type].to_i == 2
              filter_by_payment_type = "ITCL"
            end 
            extra_params += ' and gateway_response like \'%%:payment_type: ' + filter_by_payment_type + '%%\''
          end
        end
        
        @fin_start_date = Configuration.find_by_config_key('FinancialYearStartDate').config_value
        @fin_end_date = Configuration.find_by_config_key('FinancialYearEndDate').config_value

        @particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, finance_fee_particular_categories.name, IFNULL(finance_fee_particular_categories.id, 0) as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false} " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN finance_fee_particulars ON finance_fee_particulars.id = finance_transaction_particulars.particular_id LEFT JOIN finance_fee_particular_categories ON finance_fee_particular_categories.id = finance_fee_particulars.finance_fee_particular_category_id " + extra_joins, :group => "transaction_date, finance_fee_particular_categories.id")
        tot_amount = 0
        @particular_wise_transactions.each do |pwt|
          tot_amount += pwt.amount
        end
        @transactions_advances = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, finance_fee_particular_categories.name, IFNULL(finance_fee_particular_categories.id, 0) as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Advance' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false} " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN finance_fee_particulars ON finance_fee_particulars.id = finance_transaction_particulars.particular_id LEFT JOIN finance_fee_particular_categories ON finance_fee_particular_categories.id = finance_fee_particulars.finance_fee_particular_category_id " + extra_joins, :group => "transaction_date, finance_fee_particular_categories.id")
        @transactions_discount = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, finance_fee_particular_categories.name, IFNULL(finance_fee_particular_categories.id, 0) as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false} " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN fee_discounts ON fee_discounts.id = finance_transaction_particulars.particular_id LEFT JOIN finance_fee_particular_categories ON finance_fee_particular_categories.id = fee_discounts.finance_fee_particular_category_id " + extra_joins, :group => "transaction_date, finance_fee_particular_categories.id")
        @transactions_discount_total_fees = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, 'Total Fee Discount' as  name, 0 as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount' and fee_discounts.finance_fee_particular_category_id = 0 and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN fee_discounts ON fee_discounts.id = finance_transaction_particulars.particular_id " + extra_joins, :group => "transaction_date")
        @transactions_fine = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, 'Fine' as name, 0 as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Fine' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id " + extra_joins, :group => "transaction_date")
        
        unless @particular_wise_transactions.blank?
          @finance_particular_categories_id = @particular_wise_transactions.map(&:finance_fee_particular_category_id).uniq
          @finance_particular_categories_id = @finance_particular_categories_id.reject { |f| f.to_s.empty? }
          @finance_particular_categories = FinanceFeeParticularCategory.find(:all,:conditions => ["is_deleted = ? and id IN (" +  @finance_particular_categories_id.join(",") + ")", false])
        end
        
      else
        flash[:warn_notice] = "#{t('flash17')}"
        redirect_to :action=>:monthly_report_fees
      end
    end
    
    fees_month = {}
    fees_month_diff = {}
    
    particular_id = 0
    particular_wise_fees_amount = []
    particular_wise_adv_amount = []
    particular_wise_discount_amount = []
    particular_wise_fees_amount_total = 0.00
    particular_wise_adv_amount_total = 0.00
    particular_wise_discount_amount_total = 0.00
    particular_wise_total = []
    grand_total = 0.00
    grand_fine = 0.00
    total_fees_discount = 0.00

    @finance_particular_categories.each do |fees_particular|
      particular_wise_fees_amount[fees_particular.id] = 0
      particular_wise_adv_amount[fees_particular.id] = 0
      particular_wise_discount_amount[fees_particular.id] = 0
    end
    
    (@start_date.to_date..@end_date.to_date).each do |day|
      dt = day.to_date.strftime("%m%d")
      date_wise_fees = 0.00
      date_wise_adv = 0.00
      date_wise_discount = 0.00
      date_wise_total = 0.00
      date_wise_fine = 0.00
      
      i = 1
      pt = @particular_wise_transactions.select{|pwt| I18n.l(pwt.transaction_date.to_date,:format=>"%Y-%m-%d") == I18n.l(day.to_date,:format=>"%Y-%m-%d") }
      @finance_particular_categories.each do |fees_particular|
        ptd = pt.select{|p| p.finance_fee_particular_category_id.to_i == fees_particular.id.to_i }
        apwfc = @transactions_advances.select{|p|  I18n.l(p.transaction_date.to_date,:format=>"%Y-%m-%d") == I18n.l(day.to_date,:format=>"%Y-%m-%d") and p.finance_fee_particular_category_id.to_i == fees_particular.id.to_i }
        td = @transactions_discount.select{|p|  I18n.l(p.transaction_date.to_date,:format=>"%Y-%m-%d") == I18n.l(day.to_date,:format=>"%Y-%m-%d") and p.finance_fee_particular_category_id.to_i == fees_particular.id.to_i }
        amt = 0
        adv_amt = 0
        td_amt = 0
        ptd.each do |pp|
          amt += pp.amount.to_f
        end
        apwfc.each do |pp|
          adv_amt += pp.amount.to_f
        end
        td.each do |pp|
          td_amt += pp.amount.to_f
        end
        date_wise_fees += amt
        date_wise_adv += adv_amt
        date_wise_discount += td_amt
        particular_wise_fees_amount[fees_particular.id] += amt
        if i == 1
          particular_id = fees_particular.id
        end
        particular_wise_adv_amount[fees_particular.id] += adv_amt
        particular_wise_discount_amount[fees_particular.id] += td_amt
        i += 1
      end
      unless  fees_month["#{dt}"].nil?
        tot_amt = (date_wise_fees.to_f + date_wise_adv.to_f) - date_wise_discount.to_f
        act_tot = fees_month["#{dt}"].to_f
        diff_amount = tot_amt - act_tot
        
        if diff_amount < 0
          diff_amount = act_tot - tot_amt
          date_wise_fees = (tot_amt + date_wise_discount.to_f + diff_amount) - (date_wise_adv.to_f )
          particular_wise_fees_amount[particular_id] = particular_wise_fees_amount[particular_id].to_f  + diff_amount
        else
          date_wise_fees = (tot_amt + date_wise_discount.to_f) - (date_wise_adv.to_f - diff_amount)
          particular_wise_fees_amount[particular_id] = particular_wise_fees_amount[particular_id].to_f  - diff_amount
        end
      else
        unless  fees_month_diff["#{dt}"].nil?
          tot_amt = (date_wise_fees.to_f + date_wise_adv.to_f) - date_wise_discount.to_f
          diff_amount = fees_month_diff["#{dt}"].to_f
          if diff_amount < 0
            diff_amount = diff_amount * (-1)
            date_wise_fees = date_wise_fees - diff_amount
            particular_wise_fees_amount[particular_id] = particular_wise_fees_amount[particular_id].to_f  - diff_amount
          else
            date_wise_fees = date_wise_fees + diff_amount
            particular_wise_fees_amount[particular_id] = particular_wise_fees_amount[particular_id].to_f  + diff_amount
          end
        end
      end
    end
    
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    
    fmt = Spreadsheet::Format.new :number_format => "0.00"
    row_1 = ["Sl No","Particular Name","Amount"]
    
    # Create a new Workbook
    new_book = Spreadsheet::Workbook.new

    # Create the worksheet
    new_book.create_worksheet :name => 'Particular Wise Transaction'

    # Add row_1
    new_book.worksheet(0).insert_row(0, row_1)
    ind = 1
    total_amount = 0.00
    k = 0
    @finance_particular_categories.each_with_index do |fees_particular, i| 
      amt = (particular_wise_fees_amount[fees_particular.id] + particular_wise_adv_amount[fees_particular.id]) - particular_wise_discount_amount[fees_particular.id]
      total_amount += amt
      row_new = [i+1, fees_particular.name, amt]
      k = i + 1
      new_book.worksheet(0).insert_row(ind, row_new)
      new_book.worksheet(0).row(ind).set_format(2, fmt)
      ind += 1
    end
    
    unless @transactions_fine.blank?
      fine_amount = @transactions_fine.map(&:amount).sum
      total_amount += fine_amount
      row_new = [k, "Late Fine", fine_amount]
      k += 1
      new_book.worksheet(0).insert_row(ind, row_new)
      new_book.worksheet(0).row(ind).set_format(2, fmt)
      ind += 1
    end
    
    unless @transactions_discount_total_fees.blank?
      discount_amount = @transactions_discount_total_fees.map(&:amount).sum
      total_amount += discount_amount
      row_new = [k, "Total Discount", discount_amount]
      k += 1
      new_book.worksheet(0).insert_row(ind, row_new)
      new_book.worksheet(0).row(ind).set_format(2, fmt)
      ind += 1
    end
    
    row_new = ["", "Total Amount", total_amount]
    new_book.worksheet(0).insert_row(ind, row_new)
    new_book.worksheet(0).row(ind).set_format(2, fmt)
    
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 

    filename = "monthly-report-#{Time.now.to_date.to_s}.xls"
    send_data spreadsheet.string, :filename => filename, :type =>  "application/vnd.ms-excel"
   
    #    csv = FasterCSV.generate do |csv|
    #      cols = []
    #      cols << "Sl No"
    #      cols << "Particular Name"
    #      cols << "Amount"
    #      
    #      csv << cols
    #      @finance_particular_categories.each_with_index do |fees_particular, i| 
    #        cols = []
    #        cols << i + 1
    #        cols << fees_particular.name
    #        amt = (particular_wise_fees_amount[fees_particular.id] + particular_wise_adv_amount[fees_particular.id]) - particular_wise_discount_amount[fees_particular.id]
    #        cols << sprintf('%.2f', amt)
    #        
    #        csv << cols
    #      end
    #    end   
    #      
    #      
    #    filename = "monthly-report-#{Time.now.to_date.to_s}.csv"
    #    send_data(csv, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
  end
  
  def transaction_pdf_fees_month_csv
    fixed_category_name

    if date_format_check
      unless @start_date > @end_date

        @filter_by_course = params[:filter_by_course]
        unless params[:filter_by_course].nil?
          if params[:filter_by_course].to_i == 1
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            school_course_id = school_course_id.reject { |s| s.to_s.empty? }
            if school_course_id.blank?
              school_course_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          elsif params[:filter_by_course].to_i == 2
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          else
            batches = Batch.all.map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          end
        else
          batches = Batch.all.map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
          extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
          extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
          extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          #abort(params.inspect)
        end
        #abort(extra_params.inspect)
        @filter_by_payment_type = params[:filter_by_payment_type]
        unless params[:filter_by_payment_type].nil?
          if params[:filter_by_payment_type].to_i != 0
            filter_by_payment_type = "MB"
            if params[:filter_by_payment_type].to_i == 1
              filter_by_payment_type = "MB"
            elsif params[:filter_by_payment_type].to_i == 2
              filter_by_payment_type = "ITCL"
            end 
            extra_params += ' and gateway_response like \'%%:payment_type: ' + filter_by_payment_type + '%%\''
            #extra_params += " and payments.gateway_response like '%:payment_type: " + filter_by_payment_type + "%'"
            #extra_params += " and payments.gateway_response like '%" + params[:filter_by_payment_type].to_s + "%'"
          end
        end
        
        @particular_wise_transactions = FinanceTransaction.find(:all, :select => "finance_transactions.payee_id, finance_transactions.id, finance_transactions.amount as amount, payments.order_id", :order => 'payments.payee_id ASC', :conditions => ["payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}'" + extra_params], :joins => "INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id " + extra_joins, :group => "payments.order_id")
        @order_ids = @particular_wise_transactions.map(&:order_id).uniq

      end
    end
    
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    
    fmt = Spreadsheet::Format.new :number_format => "0.00"
    title_format = Spreadsheet::Format.new({
      :weight           => :bold,
      :size             => 11,
      :horizontal_align => :centre
    })
  
    center_font_format = Spreadsheet::Format.new({
      :horizontal_align => :centre,
      :size             => 12
    });
    font_format = Spreadsheet::Format.new({
      :size             => 12
    });
    font_format_footer = Spreadsheet::Format.new({
      :size             => 12,
      :weight           => :bold
    });
    amount_format = Spreadsheet::Format.new({
      :size             => 12,
      :number_format    => "0.00"
    });
    amount_format_footer = Spreadsheet::Format.new({
      :size             => 12,
      :weight           => :bold, 
      :number_format    => "0.00"
    });

    row_1 = ["Sl No","Student Name","Student ID","Order Id","Amount"]
    
    # Create a new Workbook
    new_book = Spreadsheet::Workbook.new

    # Create the worksheet
    new_book.create_worksheet :name => 'Particular Wise Transaction'

    # Add row_1
    new_book.worksheet(0).insert_row(0, row_1)
    new_book.worksheet(0).row(0).set_format(0, title_format)
    new_book.worksheet(0).row(0).set_format(1, title_format)
    new_book.worksheet(0).row(0).set_format(2, title_format)
    new_book.worksheet(0).row(0).set_format(3, title_format)
    new_book.worksheet(0).row(0).set_format(4, title_format)
    
    ind = 1
    total_amount = 0.00
    @order_ids.each_with_index do |order, i|
      pt = @particular_wise_transactions.select{|pwt| pwt.order_id == order }.first
      transaction_id = pt.id
      std_id = pt.payee_id
      student = Student.find(:first, :conditions => "id = #{std_id}")
      if student.nil?
        student = ArchivedStudent.find(:first, :conditions => "former_id = #{std_id}")
      end
      payment = Payment.find_by_finance_transaction_id(transaction_id)
      amount = payment.gateway_response[:amount]
      
      total_amount += amount.to_f
      row_new = [i+1, student.full_name, student.admission_no, order, amount.to_f]
      new_book.worksheet(0).insert_row(ind, row_new)
      new_book.worksheet(0).row(ind).set_format(0, center_font_format)
      new_book.worksheet(0).row(ind).set_format(1, font_format)
      new_book.worksheet(0).row(ind).set_format(2, center_font_format)
      new_book.worksheet(0).row(ind).set_format(3, center_font_format)
      new_book.worksheet(0).row(ind).set_format(4, amount_format)
      new_book.worksheet(0).column(0).width = 10
      new_book.worksheet(0).column(1).width = 50
      new_book.worksheet(0).column(2).width = 15
      new_book.worksheet(0).column(3).width = 20
      new_book.worksheet(0).column(4).width = 20
      ind += 1
    end
    row_new = ["", "Total Amount", "", "", total_amount]
    new_book.worksheet(0).insert_row(ind, row_new)
    new_book.worksheet(0).merge_cells(ind, 1, ind, 3)
    new_book.worksheet(0).row(ind).set_format(1, font_format_footer)
    new_book.worksheet(0).row(ind).set_format(4, amount_format_footer)
    
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 

    filename = "monthly-report-#{Time.now.to_date.to_s}.xls"
    send_data spreadsheet.string, :filename => filename, :type =>  "application/vnd.ms-excel"
  end
  
  def transaction_pdf_fees_month
    fixed_category_name

    if date_format_check
      unless @start_date > @end_date
        
        @filter_by_course = params[:filter_by_course]
        unless params[:filter_by_course].nil?
          if params[:filter_by_course].to_i == 1
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            school_course_id = school_course_id.reject { |s| s.to_s.empty? }
            if school_course_id.blank?
              school_course_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          elsif params[:filter_by_course].to_i == 2
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          else
            batches = Batch.all.map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          end
        else
          batches = Batch.all.map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
          extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
          extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
          extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          #abort(params.inspect)
        end
        #abort(extra_params.inspect)
        @filter_by_payment_type = params[:filter_by_payment_type]
        unless params[:filter_by_payment_type].nil?
          if params[:filter_by_payment_type].to_i != 0
            filter_by_payment_type = "MB"
            if params[:filter_by_payment_type].to_i == 1
              filter_by_payment_type = "MB"
            elsif params[:filter_by_payment_type].to_i == 2
              filter_by_payment_type = "ITCL"
            end 
            extra_params += ' and gateway_response like \'%%:payment_type: ' + filter_by_payment_type + '%%\''
            #extra_params += " and payments.gateway_response like '%:payment_type: " + filter_by_payment_type + "%'"
            #extra_params += " and payments.gateway_response like '%" + params[:filter_by_payment_type].to_s + "%'"
          end
        end
        
        @particular_wise_transactions = FinanceTransaction.find(:all, :select => "finance_transactions.payee_id, finance_transactions.id, finance_transactions.amount as amount, payments.order_id", :order => 'payments.payee_id ASC', :conditions => ["payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}'" + extra_params], :joins => "INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id " + extra_joins, :group => "payments.order_id")
        @order_ids = @particular_wise_transactions.map(&:order_id).uniq
        
      end
    end
    render :pdf => 'transaction_pdf_fees_month',
      :margin => {:top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :orientation => 'Landscape',
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
   
  end
  
  def update_monthly_report_fees
    
    fixed_category_name
    if date_format_check
      unless @start_date > @end_date
        online_id = []
        #        online_payments = Payment.find(:all, :conditions => "finance_transaction_id IS NOT NULL")
        #        online_payments.each do |o|
        #          finance_fees = FinanceFee.find(:first, :conditions => "id = #{o.payment_id}")
        #          if finance_fees.nil? 
        #            online_id << o.id
        #          end
        #        end
        #        abort(online_id.inspect)
        unless params[:transaction_test].nil?
          #finance_notmatch_transactions = FinanceNotmatchTransaction.all
          trans_id = "65070,66179,66182,66423,66848,67167,67716,67723,68337,69339,71033,71386,72322,72303,72300,72296,72197,72152,76695,77324,77482,77502,77512,77538,77550,76327,76789,77013,76330,76567,76568,77179,77247,77255,77257,77260,77262,77264,77270,77272,77277,77280,77282,77286,77288,77293,77294,77296,77300,77308,77312,77314,77316,77318,77320,77322,77327,77330,77336,77343,77345,77349,77351,77353,77355,77359,77361,77365,77370,77372,77374,77376,77378,77380,77381,77384,77387,77389,77393,77395,77398,77400,77405,77406,77408,77409,77411,77413,77417,77419,77421,77423,77427,77430,77434,77436,77438,77440,77442,77444,77451,77452,77453,77456,77458,77460,77465,77469,77471,77473,77475,77477,77479,77483,77485,77487,77489,77496,77498,77500,77504,77506,77514,77517,77523,77526,77529,77541,77543,77545,77555,77558,77560,77561,77563,77564,77567,77569,77571,77575,77577,77580,77582,77587,77589,77594,77595,77597,77600,77608,77610,77612,77614,77616,77618,77621,77629,77631,77635,77890"
          finance_notmatch_transactions = FinanceTransaction.find(:all, :conditions => "id IN (#{trans_id})")
          finance_notmatch_transactions.each do |finance_notmatch_transaction|
            
            transaction = FinanceTransaction.find(:first, :conditions => "id = #{finance_notmatch_transactions.id}")
            if transaction.blank?
              finance_notmatch_transaction.destroy
            else
              transaction_id = transaction.id
              particular_amount = 0.00
              particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction_id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection'"], :group => "finance_transaction_particulars.finance_transaction_id")
              particular_wise_transactions.each do |pt|
                particular_amount += pt.amount.to_f
              end

              particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction_id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Advance'"], :group => "finance_transaction_particulars.finance_transaction_id")
              particular_wise_transactions.each do |pt|
                particular_amount += pt.amount.to_f
              end

              particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction_id} and finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount'"], :group => "finance_transaction_particulars.finance_transaction_id")
              particular_wise_transactions.each do |pt|
                particular_amount -= pt.amount.to_f
              end
              if transaction.id == 44610
                abort(particular_amount.to_s + " " + transaction.amount.to_s)
              end
              if particular_amount.to_f == transaction.amount.to_f
                finance_notmatch_transaction.destroy
              else  
                payments = Payment.find(:all, :conditions => "finance_transaction_id = #{finance_notmatch_transaction.transaction_id}")
                unless payments.blank?

                  if payments.length == 1
                    payment = payments[0]
                    order_id = payment.order_id
                    finance_orders = FinanceOrder.find(:all, :conditions => "order_id = '#{order_id}'")
                    unless finance_orders.blank?
                      if finance_orders.length == 1
                        finance_order = finance_orders[0]
                        request_params = finance_order.request_params
                        request_params_array = finance_order.request_params.map{|k,v| [k,v]}

                        #abort(key.to_s + "  " + values.to_s) 
                        finance_id = transaction.finance_id
                        transaction_particulars = FinanceTransactionParticular.find(:all, :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction.id}"])
                        unless transaction_particulars.blank?
                          transaction_particulars.each do |transaction_particular|
                            if transaction_particular.particular_type == "Particular"
                              particular_id = transaction_particular.particular_id
                              particular_found = false
                              unless request_params["fee_particular_amount_" + particular_id.to_s + "_" + finance_id.to_s].blank?
                                particular_found = true
                              end
                              unless request_params["fee_particular_amount_" + particular_id.to_s].blank?
                                particular_found = true
                              end
                              unless particular_found
                                transaction_particular.destroy
                              end
                            elsif transaction_particular.particular_type == "Adjustment"
                              particular_id = transaction_particular.particular_id
                              particular_found = false
                              unless request_params["fee_discount_amount_" + particular_id.to_s + "_" + finance_id.to_s].blank?
                                particular_found = true
                              end
                              unless request_params["fee_discount_amount_" + particular_id.to_s].blank?
                                particular_found = true
                              end
                              unless particular_found
                                transaction_particular.destroy
                              end
                            elsif transaction_particular.particular_type == "FineAdjustment"
                              particular_id = transaction_particular.particular_id
                              particular_found = false
                              unless request_params["fee_fine_discount_amount_" + particular_id.to_s + "_" + finance_id.to_s].blank?
                                particular_found = true
                              end
                              unless request_params["fee_fine_discount_amount_" + particular_id.to_s].blank?
                                particular_found = true
                              end
                              unless particular_found
                                transaction_particular.destroy
                              end
                            elsif transaction_particular.particular_type == "VAT"
                              particular_found = false
                              unless request_params["fee_vat_amount_" + finance_id.to_s].blank?
                                particular_found = true
                              end
                              unless request_params["fee_vat_amount"].blank?
                                particular_found = true
                              end
                              unless particular_found
                                transaction_particular.destroy
                              end
                            elsif transaction_particular.particular_type == "Fine"
                              particular_found = false
                              unless request_params["fine_amount_to_pay_" + finance_id.to_s].blank?
                                particular_found = true
                              end
                              unless request_params["fine_amount_to_pay"].blank?
                                particular_found = true
                              end
                              unless particular_found
                                transaction_particular.destroy
                              end
                            end
                          end
                        end

                        particular_amount = 0.00
                        particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction.id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection'"], :group => "finance_transaction_particulars.finance_transaction_id")
                        particular_wise_transactions.each do |pt|
                          particular_amount += pt.amount.to_f
                        end

                        particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction.id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Advance'"], :group => "finance_transaction_particulars.finance_transaction_id")
                        particular_wise_transactions.each do |pt|
                          particular_amount += pt.amount.to_f
                        end

                        particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction.id} and finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount'"], :group => "finance_transaction_particulars.finance_transaction_id")
                        particular_wise_transactions.each do |pt|
                          particular_amount -= pt.amount.to_f
                        end

                        if particular_amount.to_f == transaction.amount.to_f
                          finance_notmatch_transaction.destroy
                        else
                          transaction_particulars = FinanceTransactionParticular.find(:all, :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction.id}"])
                          unless transaction_particulars.blank?
                            transaction_particulars.each do |transaction_particular|
                              transaction_particular.destroy
                            end
                          end
                          request_params_array.each do |k, v|
                            unless k.index('fee_particular_amount_').nil?
                              particular_id_array = k.gsub('fee_particular_amount_','').split("_")
                              particular_id = particular_id_array[0]
                              amount = v.to_f
                              finance_transaction_particular = FinanceTransactionParticular.new
                              finance_transaction_particular.finance_transaction_id = transaction.id
                              finance_transaction_particular.particular_id = particular_id
                              finance_transaction_particular.particular_type = 'Particular'
                              finance_transaction_particular.transaction_type = 'Fee Collection'
                              finance_transaction_particular.amount = amount
                              finance_transaction_particular.transaction_date = transaction.transaction_date
                              finance_transaction_particular.save
                            end
                            unless k.index('fee_discount_amount_').nil?
                              particular_id_array = k.gsub('fee_discount_amount_','').split("_")
                              particular_id = particular_id_array[0]
                              amount = v.to_f
                              finance_transaction_particular = FinanceTransactionParticular.new
                              finance_transaction_particular.finance_transaction_id = transaction.id
                              finance_transaction_particular.particular_id = particular_id
                              finance_transaction_particular.particular_type = 'Adjustment'
                              finance_transaction_particular.transaction_type = 'Discount'
                              finance_transaction_particular.amount = amount
                              finance_transaction_particular.transaction_date = transaction.transaction_date
                              finance_transaction_particular.save
                            end
                            unless k.index('fee_fine_discount_amount_').nil?
                              particular_id_array = k.gsub('fee_fine_discount_amount_','').split("_")
                              particular_id = particular_id_array[0]
                              amount = v.to_f
                              finance_transaction_particular = FinanceTransactionParticular.new
                              finance_transaction_particular.finance_transaction_id = transaction.id
                              finance_transaction_particular.particular_id = particular_id
                              finance_transaction_particular.particular_type = 'FineAdjustment'
                              finance_transaction_particular.transaction_type = 'Discount'
                              finance_transaction_particular.amount = amount
                              finance_transaction_particular.transaction_date = transaction.transaction_date
                              finance_transaction_particular.save
                            end
                            unless k.index('fee_vat_amount').nil?
                              amount = v.to_f
                              finance_transaction_particular = FinanceTransactionParticular.new
                              finance_transaction_particular.finance_transaction_id = transaction.id
                              finance_transaction_particular.particular_id = 0
                              finance_transaction_particular.particular_type = 'VAT'
                              finance_transaction_particular.transaction_type = ''
                              finance_transaction_particular.amount = amount
                              finance_transaction_particular.transaction_date = transaction.transaction_date
                              finance_transaction_particular.save
                            end
                            unless k.index('fine_amount_to_pay').nil?
                              amount = v.to_f
                              finance_transaction_particular = FinanceTransactionParticular.new
                              finance_transaction_particular.finance_transaction_id = transaction.id
                              finance_transaction_particular.particular_id = 0
                              finance_transaction_particular.particular_type = 'Fine'
                              finance_transaction_particular.transaction_type = ''
                              finance_transaction_particular.amount = amount
                              finance_transaction_particular.transaction_date = transaction.transaction_date
                              finance_transaction_particular.save
                            end
                          end

                          particular_amount = 0.00
                          particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction.id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection'"], :group => "finance_transaction_particulars.finance_transaction_id")
                          particular_wise_transactions.each do |pt|
                            particular_amount += pt.amount.to_f
                          end

                          particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction.id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Advance'"], :group => "finance_transaction_particulars.finance_transaction_id")
                          particular_wise_transactions.each do |pt|
                            particular_amount += pt.amount.to_f
                          end

                          particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction.id} and finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount'"], :group => "finance_transaction_particulars.finance_transaction_id")
                          particular_wise_transactions.each do |pt|
                            particular_amount -= pt.amount.to_f
                          end

                          if particular_amount.to_f == transaction.amount.to_f
                            finance_notmatch_transaction.destroy
                            #abort('here')
                          end
                        end
                      end
                    end
                  end
                end
              end
              
            end
          end
        end
        unless params[:test].nil?
          if params[:test].to_i > 0
            trans_ids = []
            p_amount = 0.00
            a_amount = 0.00
            d_amount = 0.00
            f_amount = 0.00
            amount = 0.00
            s = []
            i = 0
            tot_amount = 0.00

            batches = Batch.active.map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 

            @transactions = FinanceTransaction.find(:all, :conditions => ["payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' " + extra_params], :joins => "INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id " + extra_joins)
            #abort("payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}'")
            #@transactions = FinanceTransaction.find(:all, :joins => "INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id")
            #abort(@transactions.length.to_s)
            @transactions.each do |pwt|
              if params[:test].to_i == 1
                amount = 0.00
                @particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{pwt.id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection'"], :group => "finance_transaction_particulars.finance_transaction_id")
                @particular_wise_transactions.each do |pt|
                  amount += pt.amount.to_f
                  p_amount += pt.amount.to_f
                end
                @particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{pwt.id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Advance'"], :group => "finance_transaction_particulars.finance_transaction_id")
                @particular_wise_transactions.each do |pt|
                  amount += pt.amount.to_f
                  a_amount += pt.amount.to_f
                end
                @particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{pwt.id} and finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount'"], :group => "finance_transaction_particulars.finance_transaction_id")
                @particular_wise_transactions.each do |pt|
                  amount -= pt.amount.to_f
                  d_amount += pt.amount.to_f
                end
                @particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{pwt.id} and finance_transaction_particulars.particular_type = 'Fine'"], :group => "finance_transaction_particulars.finance_transaction_id")
                #if pwt.id == 79653
                #  abort(@particular_wise_transactions.inspect)
                #end
                @particular_wise_transactions.each do |pt|
                  amount += pt.amount.to_f
                  f_amount += pt.amount.to_f
                end
                if amount.to_f != pwt.amount.to_f
                  trans_ids << pwt.id
                end
                tot_amount += amount
              elsif params[:test].to_i == 2  
                online_payment = Payment.find(:first, :conditions => "finance_transaction_id = #{pwt.id}")
                if pwt.amount.to_f != online_payment.gateway_response[:amount].to_f
                  order_id = online_payment.order_id
                  online_amount = online_payment.gateway_response[:amount].to_f
                  online_payments = Payment.find(:all, :conditions => "order_id = '#{order_id}'")
                  transaction_ids = online_payments.map(&:finance_transaction_id)
                  transaction_ids = transaction_ids.reject { |t| t.to_s.empty? }
                  if transaction_ids.blank?
                    transaction_ids[0] = 0
                  end
                  @online_payment_transactions = FinanceTransaction.find(:all, :conditions => ["id IN (#{transaction_ids.join(",")})"])
                  amt = 0.0
                  @online_payment_transactions.each do |opt|
                    amt += opt.amount.to_f
                  end
                  if amt.to_f != online_amount
                    trans_ids << pwt.id
                  end
                end
              elsif params[:test].to_i == 3  
                online_payments = Payment.find(:all, :conditions => "finance_transaction_id = #{pwt.id}")
                if online_payments.length > 1
                  trans_ids << pwt.id
                end
              end
            end
            abort(trans_ids.inspect)
          end
        end
        extra_params = ""
        extra_joins = ""
        @filter_by_course = params[:filter_by_course]
        unless params[:filter_by_course].nil?
          if params[:filter_by_course].to_i == 1
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            school_course_id = school_course_id.reject { |s| s.to_s.empty? }
            if school_course_id.blank?
              school_course_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          elsif params[:filter_by_course].to_i == 2
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          else
            batches = Batch.all.map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          end
        else
          batches = Batch.all.map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
          extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
          extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
          extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          #abort(params.inspect)
        end
        #abort(extra_params.inspect)
        @filter_by_payment_type = params[:filter_by_payment_type]
        unless params[:filter_by_payment_type].nil?
          if params[:filter_by_payment_type].to_i != 0
            filter_by_payment_type = "MB"
            if params[:filter_by_payment_type].to_i == 1
              filter_by_payment_type = "MB"
            elsif params[:filter_by_payment_type].to_i == 2
              filter_by_payment_type = "ITCL"
            end 
            extra_params += ' and gateway_response like \'%%:payment_type: ' + filter_by_payment_type + '%%\''
            #extra_params += " and payments.gateway_response like '%:payment_type: " + filter_by_payment_type + "%'"
            #extra_params += " and payments.gateway_response like '%" + params[:filter_by_payment_type].to_s + "%'"
          end
        end
        
        @fin_start_date = Configuration.find_by_config_key('FinancialYearStartDate').config_value
        @fin_end_date = Configuration.find_by_config_key('FinancialYearEndDate').config_value
        
        #abort("finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false} " + extra_params)
        @particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, finance_fee_particular_categories.name, IFNULL(finance_fee_particular_categories.id, 0) as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false} " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN finance_fee_particulars ON finance_fee_particulars.id = finance_transaction_particulars.particular_id LEFT JOIN finance_fee_particular_categories ON finance_fee_particular_categories.id = finance_fee_particulars.finance_fee_particular_category_id " + extra_joins, :group => "transaction_date, finance_fee_particular_categories.id")
        tot_amount = 0
        @particular_wise_transactions.each do |pwt|
          tot_amount += pwt.amount
        end
        #abort(tot_amount.to_s)
        @transactions_advances = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, finance_fee_particular_categories.name, IFNULL(finance_fee_particular_categories.id, 0) as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Advance' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false} " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN finance_fee_particulars ON finance_fee_particulars.id = finance_transaction_particulars.particular_id LEFT JOIN finance_fee_particular_categories ON finance_fee_particular_categories.id = finance_fee_particulars.finance_fee_particular_category_id " + extra_joins, :group => "transaction_date, finance_fee_particular_categories.id")
        @transactions_discount = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, finance_fee_particular_categories.name, IFNULL(finance_fee_particular_categories.id, 0) as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false} " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN fee_discounts ON fee_discounts.id = finance_transaction_particulars.particular_id LEFT JOIN finance_fee_particular_categories ON finance_fee_particular_categories.id = fee_discounts.finance_fee_particular_category_id " + extra_joins, :group => "transaction_date, finance_fee_particular_categories.id")
        @transactions_discount_total_fees = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, 'Total Fee Discount' as  name, 0 as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount' and fee_discounts.finance_fee_particular_category_id = 0 and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN fee_discounts ON fee_discounts.id = finance_transaction_particulars.particular_id " + extra_joins, :group => "transaction_date")
        @transactions_fine = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, 'Fine' as name, 0 as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Fine' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id " + extra_joins, :group => "transaction_date")
        
        unless @particular_wise_transactions.blank?
          @finance_particular_categories_id = @particular_wise_transactions.map(&:finance_fee_particular_category_id).uniq
          @finance_particular_categories_id = @finance_particular_categories_id.reject { |f| f.to_s.empty? }
          if @finance_particular_categories_id.blank?
            @finance_particular_categories_id[0] = 0
          end
          @finance_particular_categories = FinanceFeeParticularCategory.find(:all,:conditions => ["is_deleted = ? and id IN (" +  @finance_particular_categories_id.join(",") + ")", false])
        end
        
        
        #        @finance_fee_category = FinanceFeeParticularCategory.find(:all,:conditions => ["is_deleted = ?", false])
        #        @all_fees_extra_particulers = []
        #        @all_fees_extra_particulers << "Discount"
        #        @all_fees_extra_particulers << "Fine"
        #        @all_fees_extra_particulers << "VAT"
        #        @transactions_particular = FinanceTransactionParticular.find(:all, :order => 'transaction_date desc', :conditions => ["transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'"])
        
      else
        flash[:warn_notice] = "#{t('flash17')}"
        redirect_to :action=>:monthly_report_fees
      end
    end
  end
  
  def student_ledger
    unless params[:id].nil?
      @from_student_view = false
      unless params[:id2].nil?
        @from_student_view = true
      end
      @student = Student.find(params[:id])
      @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "id, ledger_date, ledger_title, amount_to_pay, amount_paid, particular_id", :order => 'ledger_date ASC', :conditions => ["student_id = #{params[:id]}"]) #, :group => "ledger_date"
    end
  end
  
  def student_ledger_pdf_fees
    
    unless params[:id].nil?
      if params[:id].to_i == 0
        paidFines = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Fine' AND amount > 0.0")
        unless paidFines.blank?
          paidFines.each do |paidFine|
            transaction_id = paidFine.finance_transaction_id
            finance_transaction = FinanceTransaction.find(transaction_id)
            unless finance_transaction.blank?
              payment = Payment.find(:first, :conditions => "finance_transaction_id = #{transaction_id}", :group => "order_id")
              unless payment.blank?
                student_id = finance_transaction.payee_id
                s = Student.find(:first, :conditions => "id = #{student_id}")
                if s.blank?
                  s = ArchivedStudent.find(:first, :conditions => "former_id = #{student_id}")
                  unless s.blank?
                    student_id = s.former_id
                  end
                else
                  student_id = s.id
                end
                unless s.blank?
                  fee_id = finance_transaction.finance_id

                  unless fee_id.blank?
                    fee = FinanceFee.find(fee_id)
                    unless fee.blank?
                      date = FinanceFeeCollection.find(:first, :conditions => "id = #{fee.fee_collection_id}")
                      unless date.nil?
                          balance = paidFine.amount
                          ledger_date = date.start_date
                          student_fee_ledger = StudentFeeLedger.new
                          student_fee_ledger.student_id = student_id
                          student_fee_ledger.ledger_title = "Fine On " + payment.transaction_datetime.strftime("%d %B, %Y")
                          unless payment.transaction_datetime.blank?
                            student_fee_ledger.ledger_date = payment.transaction_datetime.strftime("%Y-%m-%d")
                          else
                            student_fee_ledger.ledger_date = ledger_date
                          end
                          student_fee_ledger.amount_to_pay = balance.to_f
                          student_fee_ledger.transaction_id = transaction_id
                          student_fee_ledger.fee_id = fee.id
                          student_fee_ledger.is_fine = 1
                          student_fee_ledger.save
                      end
                    end
                  end
                end
              end
            end
          end
          #abort(paidFines.inspect)
        end
      end
      @student = Student.find(params[:id])
      @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "id, ledger_date, ledger_title, amount_to_pay, amount_paid, particular_id", :order => 'ledger_date ASC', :conditions => ["student_id = #{params[:id]}"], :order => "ledger_date asc") #, :group => "ledger_date"
    end
    
    render :pdf => 'student_ledger_pdf_fees',
          :margin => {:top=> 10,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :orientation => 'Portrait',
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_ledger_footer.html'}}
  end
  
  def student_ledger_xls_fees
    unless params[:id].nil?
      @student = Student.find(params[:id])
      #@student_fee_ledgers = StudentFeeLedger.find(:all, :select => "ledger_date, ledger_title, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid", :order => 'ledger_date ASC', :conditions => ["student_id = #{params[:id]}"], :order => "ledger_date asc", :group => "ledger_date")
      @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "id, ledger_date, ledger_title, amount_to_pay, amount_paid, particular_id", :order => 'ledger_date ASC', :conditions => ["student_id = #{params[:id]}"], :order => "ledger_date asc")
      
      require 'spreadsheet'
      Spreadsheet.client_encoding = 'UTF-8'

      amount_format = Spreadsheet::Format.new({
        :number_format    => "0.00"
      });
      title_header_format = Spreadsheet::Format.new({
        :weight           => :bold,
        :size             => 10,
        :horizontal_align => :centre,
        :pattern_bg_color => :grey,
        :pattern_fg_color => :grey,
        :color => :blue
      })
      center_format = Spreadsheet::Format.new({
        :horizontal_align => :centre
      })
      right_format = Spreadsheet::Format.new({
        :horizontal_align => :right
      })

      row_1 = ["Sl No","Transaction Date","Transaction Type","Transaction ID","For","Particulars","Debit","Credit","Balance"]

      # Create a new Workbook
      new_book = Spreadsheet::Workbook.new

      # Create the worksheet
      new_book.create_worksheet :name => 'Student Ledger'

      # Add row_1
      new_book.worksheet(0).insert_row(0, row_1)
      new_book.worksheet(0).column(0).width = 10
      new_book.worksheet(0).column(1).width = 20
      new_book.worksheet(0).column(2).width = 20
      new_book.worksheet(0).column(3).width = 20
      new_book.worksheet(0).column(4).width = 25
      new_book.worksheet(0).column(5).width = 40
      new_book.worksheet(0).column(6).width = 12
      new_book.worksheet(0).column(7).width = 12
      new_book.worksheet(0).column(8).width = 12
      new_book.worksheet(0).row(0).set_format(0, title_header_format)
      new_book.worksheet(0).row(0).set_format(1, title_header_format)
      new_book.worksheet(0).row(0).set_format(2, title_header_format)
      new_book.worksheet(0).row(0).set_format(3, title_header_format)
      new_book.worksheet(0).row(0).set_format(4, title_header_format)
      new_book.worksheet(0).row(0).set_format(5, title_header_format)
      new_book.worksheet(0).row(0).set_format(6, title_header_format)
      new_book.worksheet(0).row(0).set_format(7, title_header_format)
      new_book.worksheet(0).row(0).set_format(8, title_header_format)
      
      
      spreadsheet = StringIO.new 
      new_book.write spreadsheet 

      carried_amount = 0
      current_balance = 0
      ind = 1;
      
      balance_forward = false
      already_shown = false
      k = 0
      @student_fee_ledgers.each_with_index do |student_fee_ledger, i|
        k = i + 1
        balance_forward = false
        info = ""
        trans_type = ""
        trans = []
        finance_transaction_id = []
        for_id = []
        particulars = []
        particular_name = ""
        trans_id = ""
        finance_transaction_ids = ""
        if student_fee_ledger.amount_to_pay.to_f == 0.00 and student_fee_ledger.amount_paid.to_f == 0.00
          balance_forward = true
          info = ""
          trans_type = ""
          particular_name = "Balance Forward upto " + student_fee_ledger.ledger_date.strftime("%d-%m-%Y")
        elsif student_fee_ledger.amount_to_pay.to_f > 0.00 and student_fee_ledger.amount_paid.to_f == 0.00
          trans_type = "Receivable"
          student_ledgers = StudentFeeLedger.find(:all, :select => "id, fee_id, particular_id", :conditions => ["id = #{student_fee_ledger.id} and student_id = #{@student.id} and ledger_date = '#{student_fee_ledger.ledger_date}'"])
          unless student_ledgers.nil?
            student_ledgers.each do |student_ledger|
              fee = FinanceFee.find(:first, :conditions => "id = #{student_ledger.fee_id}")
              unless fee.nil?
                trans << fee.id
                date_id = fee.fee_collection_id
                d = FinanceFeeCollection.find(date_id)
                for_id << d.name
                  exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
                  unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
                    exclude_particular_ids = exclude_particular_ids
                  else
                    exclude_particular_ids = [0]
                  end
                if student_ledger.particular_id == 0 or student_ledger.particular_id.blank?
                fee_particulars = d.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{fee.batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==fee.batch) }
                else
                fee_particulars = d.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and finance_fee_particulars.id = #{student_ledger.particular_id} and is_deleted=#{false} and batch_id=#{fee.batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==fee.batch) }
                end
                unless fee_particulars.blank?
                particular_name = fee_particulars.map(&:name).join(", ") + " for " + d.name 
                end
                #fee_particulars = d.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@student.batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
                #particular_name = fee_particulars.map(&:name).join(",") + " for " + d.name 
              else
                student_ledger.destroy
              end
              trans_id = trans.join(",")
              info = for_id.join(",")
            end
          end
        elsif student_fee_ledger.amount_to_pay.to_f == 0.00 and student_fee_ledger.amount_paid.to_f > 0.00
            trans_type = "Received"
            student_ledgers = StudentFeeLedger.find(:all, :select => "id, fee_id, transaction_id, particular_id", :conditions => ["id = #{student_fee_ledger.id} and student_id = #{@student.id} and ledger_date = '#{student_fee_ledger.ledger_date}'"])
            unless student_ledgers.nil?
              student_ledgers.each do |student_ledger|
                if student_ledger.transaction_id == 0 and student_ledger.particular_id > 0
                  trans_type = "Discount"
                  fee = FinanceFee.find(:first, :conditions => "id = #{student_ledger.fee_id}")
                  unless fee.blank?
                      date_id = fee.fee_collection_id
                      d = FinanceFeeCollection.find(date_id)
                      for_id << d.name
                  else    
                      student_ledger.destroy   
                  end
                  trans_id = trans.join(", ")
                  info = for_id.join(", ")
                  particular_name = particulars.join(", ")
                else
                  payments = Payment.find(:all, :conditions => "finance_transaction_id = #{student_ledger.transaction_id}", :group => "id")
                  unless payments.nil?
                    payments.each do |payment|
                      trans << payment.order_id
                      finance_transaction_id << payment.finance_transaction_id
                      fee = FinanceFee.find(:first, :conditions => "id = #{payment.payment_id}")
                      unless fee.blank?
                        date_id = fee.fee_collection_id
                        d = FinanceFeeCollection.find(date_id)
                        for_id << d.name
                        particulars << d.name
                      else
                        student_ledger.destroy
                      end
                      particulars << "Trust Bank Payment"  
                      payment_type = (payment.gateway_response[:payment_type] = "MB") ? "Mobile Banking" : "Card Online"
                      particulars << payment_type    
                      particulars << "Reference ID: " + payment.gateway_response[:ref_id]    
                    end
                    trans_id = trans.join(", ")
                    finance_transaction_ids = finance_transaction_id.join(", ")
                    info = for_id.join(", ")
                    particular_name = particulars.join(", ")
                  else
                    student_ledger.destroy
                  end
                end
              end
            end
        end
        
        ledger_found = true
        is_fine = false 
        student_ledger = StudentFeeLedger.find(:first, :select => "is_fine", :conditions => ["id = #{student_fee_ledger.id} and student_id = #{@student.id} and ledger_date = '#{student_fee_ledger.ledger_date}'"]) 
        unless student_ledger.blank? 
          if student_ledger.is_fine 
            is_fine = true 
            ledger_found = false 
          end 
        end 
        
        unless is_fine
          if student_fee_ledger.amount_to_pay.to_f == 0.00 and student_fee_ledger.amount_paid.to_f == 0.00
          elsif student_fee_ledger.amount_to_pay.to_f > 0.00 and student_fee_ledger.amount_paid.to_f == 0.00
            student_ledgers = StudentFeeLedger.find(:all, :select => "id, fee_id", :conditions => ["id = #{student_fee_ledger.id} and student_id = #{@student.id} and ledger_date = '#{student_fee_ledger.ledger_date}'"])
            if student_ledgers.blank?
              ledger_found = false
            end  
          elsif student_fee_ledger.amount_to_pay.to_f == 0.00 and student_fee_ledger.amount_paid.to_f > 0.00
            student_ledgers = StudentFeeLedger.find(:all, :select => "id, fee_id, transaction_id", :conditions => ["id = #{student_fee_ledger.id} and student_id = #{@student.id} and ledger_date = '#{student_fee_ledger.ledger_date}'"])
            if student_ledgers.blank?
              ledger_found = false
            end
          end
        end
        
        if trans_type == "Received" 
          unless finance_transaction_ids.blank? 
            amt = 0 
            amt_paid = 0 
            ledger_date = '' 
            fee_id = '' 
            info_fine = '' 
            fine_title = '' 
            student_ledgers = StudentFeeLedger.find(:all, :select => "id, fee_id, transaction_id, amount_to_pay, amount_paid, ledger_date, ledger_title", :conditions => ["transaction_id IN (#{finance_transaction_ids}) and student_id = #{@student.id} and is_fine = #{true}"]) 
            unless student_ledgers.blank? 
              student_ledgers.each do |student_ledger| 
                amt += student_ledger.amount_to_pay 
                amt_paid += student_ledger.amount_paid 
                ledger_date = student_ledger.ledger_date 
                fee_id = student_ledger.fee_id 
                fee = FinanceFee.find(:first, :conditions => "id = #{student_ledger.fee_id}") 
                unless fee.blank? 
                   date_id = fee.fee_collection_id 
                    d = FinanceFeeCollection.find(date_id) 
                    info_fine = d.name 
                end 
                fine_title = student_ledger.ledger_title 
              end 
            end
            if amt > 0
              current_balance = (carried_amount + amt.to_f) - amt_paid.to_f
              carried_amount = current_balance
              if current_balance < 0
                current_balance = current_balance * (-1)
              end
              row_new = [i+1, ledger_date.strftime("%d-%m-%Y"), "Fine", fee_id.to_s, info_fine, fine_title, amt.to_f, amt_paid.to_f, current_balance.to_f]
              
              new_book.worksheet(0).insert_row(ind, row_new)
              #new_book.worksheet(0).row(ind).column(0).width = 100
              new_book.worksheet(0).row(ind).set_format(0, center_format)
              new_book.worksheet(0).row(ind).set_format(1, center_format)
              new_book.worksheet(0).row(ind).set_format(2, center_format)
              new_book.worksheet(0).row(ind).set_format(6, amount_format)
              new_book.worksheet(0).row(ind).set_format(7, amount_format)
              new_book.worksheet(0).row(ind).set_format(8, amount_format)
              ind += 1
            end
          end 
        end 
        
        if ledger_found and already_shown and !balance_forward
          current_balance = (carried_amount + student_fee_ledger.amount_to_pay.to_f) - student_fee_ledger.amount_paid.to_f
          carried_amount = current_balance
          if current_balance < 0
            current_balance = current_balance * (-1)
          end
          title = (student_fee_ledger.ledger_title.blank?) ? particular_name : student_fee_ledger.ledger_title
          unless balance_forward
            row_new = [i+1, student_fee_ledger.ledger_date.strftime("%d-%m-%Y"), trans_type, trans_id, info, title, student_fee_ledger.amount_to_pay.to_f, student_fee_ledger.amount_paid.to_f, current_balance.to_f]
          else
            row_new = [i+1, title, trans_type, trans_id, info, title, student_fee_ledger.amount_to_pay.to_f, student_fee_ledger.amount_paid.to_f, current_balance.to_f]
          end
          new_book.worksheet(0).insert_row(ind, row_new)
          #new_book.worksheet(0).row(ind).column(0).width = 100
          new_book.worksheet(0).row(ind).set_format(0, center_format)
          new_book.worksheet(0).row(ind).set_format(1, center_format)
          new_book.worksheet(0).row(ind).set_format(2, center_format)
          new_book.worksheet(0).row(ind).set_format(6, amount_format)
          new_book.worksheet(0).row(ind).set_format(7, amount_format)
          new_book.worksheet(0).row(ind).set_format(8, amount_format)
          if balance_forward
            new_book.worksheet(0).merge_cells(ind, 1, ind, 5)
            new_book.worksheet(0).row(ind).set_format(1, right_format)
          else
            new_book.worksheet(0).row(ind).set_format(1, center_format)
          end
          ind += 1
        elsif ledger_found and !already_shown
          already_shown = true
          current_balance = (carried_amount + student_fee_ledger.amount_to_pay.to_f) - student_fee_ledger.amount_paid.to_f
          carried_amount = current_balance
          if current_balance < 0
            current_balance = current_balance * (-1)
          end
          title = (student_fee_ledger.ledger_title.blank?) ? particular_name : student_fee_ledger.ledger_title
          unless balance_forward
            row_new = [i+1, student_fee_ledger.ledger_date.strftime("%d-%m-%Y"), trans_type, trans_id, info, title, student_fee_ledger.amount_to_pay.to_f, student_fee_ledger.amount_paid.to_f, current_balance.to_f]
          else
            row_new = [i+1, title, trans_type, trans_id, info, title, student_fee_ledger.amount_to_pay.to_f, student_fee_ledger.amount_paid.to_f, current_balance.to_f]
          end
          new_book.worksheet(0).insert_row(ind, row_new)
          #new_book.worksheet(0).row(ind).column(0).width = 100
          new_book.worksheet(0).row(ind).set_format(0, center_format)
          new_book.worksheet(0).row(ind).set_format(1, center_format)
          new_book.worksheet(0).row(ind).set_format(2, center_format)
          new_book.worksheet(0).row(ind).set_format(6, amount_format)
          new_book.worksheet(0).row(ind).set_format(7, amount_format)
          new_book.worksheet(0).row(ind).set_format(8, amount_format)
          if balance_forward
            new_book.worksheet(0).merge_cells(ind, 1, ind, 5)
            new_book.worksheet(0).row(ind).set_format(1, right_format)
          else
            new_book.worksheet(0).row(ind).set_format(1, center_format)
          end
          ind += 1
        end
      end
      
      title_footer_format = Spreadsheet::Format.new({
        :weight           => :bold,
        :size             => 10,
        :horizontal_align => :right
      })
      amount_bold_format = Spreadsheet::Format.new({
        :weight           => :bold,
        :size             => 10,
        :number_format    => "0.00"
      })
      remaining_amount = 0.00
      if carried_amount < 0
        remaining_amount = carried_amount * (-1)
      else
        remaining_amount = carried_amount
      end
      title = (carried_amount < 0) ? "Advance" : "Current Dues"
      row_new = [title, "", "", "", "", "", "", "", remaining_amount.to_f]
      k += 1
      new_book.worksheet(0).insert_row(ind, row_new)
      new_book.worksheet(0).row(ind).set_format(0, title_footer_format)
      new_book.worksheet(0).row(ind).set_format(8, amount_bold_format)
      new_book.worksheet(0).merge_cells(ind, 0, ind, 7)
      ind += 1
      
      
      spreadsheet = StringIO.new 
      new_book.write spreadsheet 
    
      filename = "student-ledger-#{@student.id}-#{Time.now.to_date.to_s}.xls"
      send_data spreadsheet.string, :filename => filename, :type =>  "application/vnd.ms-excel"
    else
      render :text => ""
    end
  end
  
  def student_ledger_list
    
    #if date_format_check
     # unless @start_date > @end_date
        extra_params = ""
        extra_joins = ""
        unless params[:filter_by_course].nil?
          @filter_by_course = params[:filter_by_course]
        else
          @filter_by_course = 0
        end
        unless @filter_by_course.nil?
          if @filter_by_course.to_i == 1
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            school_course_id = school_course_id.reject { |s| s.to_s.empty? }
            if school_course_id.blank?
              school_course_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " ( batches.id IN (#{batches.join(",")}) )"
            extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
          elsif @filter_by_course.to_i == 2
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " ( batches.id IN (#{batches.join(",")}) )"
            extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
          end
        end
        
        unless params[:filter_by_dues].nil?
          @filter_by_dues = params[:filter_by_dues]
        else
          @filter_by_dues = 0
        end
        #abort(extra_params.inspect)
        if @filter_by_dues.to_i == 0
          @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id").count
          @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id",:page => params[:page],:per_page => 10)
          unless extra_params.blank?
            unless @students.nil?
              student_ids = @students.map(&:student_id)
              student_ids = student_ids.reject { |s| s.to_s.empty? }
              if student_ids.blank?
                student_ids[0] = 0
              end
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :conditions => ["student_id IN (#{student_ids.join(",")}) and " + extra_params], :joins => extra_joins, :group => "student_id")
            else
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :conditions => ["" + extra_params], :joins => extra_joins, :group => "student_id")
            end
          else
            unless @students.nil?
              student_ids = @students.map(&:student_id)
              student_ids = student_ids.reject { |s| s.to_s.empty? }
              if student_ids.blank?
                student_ids[0] = 0
              end
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :conditions => ["student_id IN (#{student_ids.join(",")})" + extra_params], :joins => extra_joins, :group => "student_id")
            else
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id")
            end
          end
        elsif @filter_by_dues.to_i == 1
          @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) = sum( amount_paid )").count
          @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) = sum( amount_paid )",:page => params[:page],:per_page => 10)
          unless extra_params.blank?
            unless @students.nil?
              student_ids = @students.map(&:student_id)
              student_ids = student_ids.reject { |s| s.to_s.empty? }
              if student_ids.blank?
                student_ids[0] = 0
              end
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) = sum( amount_paid )", :conditions => ["student_id IN (#{student_ids.join(",")}) and " + extra_params], :joins => extra_joins, :group => "student_id")
            else
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) = sum( amount_paid )", :conditions => ["" + extra_params], :joins => extra_joins, :group => "student_id")
            end
          else
            unless @students.nil?
              student_ids = @students.map(&:student_id)
              student_ids = student_ids.reject { |s| s.to_s.empty? }
              if student_ids.blank?
                student_ids[0] = 0
              end
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) = sum( amount_paid )", :conditions => ["student_id IN (#{student_ids.join(",")})" + extra_params], :joins => extra_joins, :group => "student_id")
            else
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) = sum( amount_paid )", :conditions => ["" + extra_params], :joins => extra_joins, :group => "student_id")
            end
          end
        elsif @filter_by_dues.to_i == 2
          @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) > sum( amount_paid )").count
          @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) > sum( amount_paid )",:page => params[:page],:per_page => 10)
          unless extra_params.blank?
            unless @students.nil?
              student_ids = @students.map(&:student_id)
              student_ids = student_ids.reject { |s| s.to_s.empty? }
              if student_ids.blank?
                student_ids[0] = 0
              end
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) > sum( amount_paid )", :conditions => ["student_id IN (#{student_ids.join(",")}) and " + extra_params], :joins => extra_joins, :group => "student_id")
            else
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) > sum( amount_paid )", :conditions => ["" + extra_params], :joins => extra_joins, :group => "student_id")
            end
          else
            unless @students.nil?
              student_ids = @students.map(&:student_id)
              student_ids = student_ids.reject { |s| s.to_s.empty? }
              if student_ids.blank?
                student_ids[0] = 0
              end
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) > sum( amount_paid )", :conditions => ["student_id IN (#{student_ids.join(",")})" + extra_params], :joins => extra_joins, :group => "student_id")
            else
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) > sum( amount_paid )", :conditions => ["" + extra_params], :joins => extra_joins, :group => "student_id")
            end
          end
        elsif @filter_by_dues.to_i == 3
          @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) < sum( amount_paid )").count
          @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) < sum( amount_paid )",:page => params[:page],:per_page => 10)
          unless extra_params.blank?
            unless @students.nil?
              student_ids = @students.map(&:student_id)
              student_ids = student_ids.reject { |s| s.to_s.empty? }
              if student_ids.blank?
                student_ids[0] = 0
              end
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) < sum( amount_paid )", :conditions => ["student_id IN (#{student_ids.join(",")}) and " + extra_params], :joins => extra_joins, :group => "student_id")
            else
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) < sum( amount_paid )", :conditions => ["" + extra_params], :joins => extra_joins, :group => "student_id")
            end
          else
            unless @students.nil?
              student_ids = @students.map(&:student_id)
              student_ids = student_ids.reject { |s| s.to_s.empty? }
              if student_ids.blank?
                student_ids[0] = 0
              end
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) < sum( amount_paid )", :conditions => ["student_id IN (#{student_ids.join(",")})" + extra_params], :joins => extra_joins, :group => "student_id")
            else
              @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) < sum( amount_paid )", :conditions => ["" + extra_params], :joins => extra_joins, :group => "student_id")
            end
          end
        end
        
      
    #end
  end
  
  def ajax_student_ledger_list
    
    #if date_format_check
     # unless @start_date > @end_date
    extra_params = ""
    extra_joins = ""

    @admission_no = ""
    unless params[:admission_no].nil?
      @admission_no = params[:admission_no]
    end

    @filter_by_course = 0
    unless params[:filter_by_course].nil?
      @filter_by_course = params[:filter_by_course]
    end

    unless @filter_by_course.nil?
      if @filter_by_course.to_i == 1
        eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
        tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
        hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
        college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
        if college_courses_id.blank?
          college_courses_id[0] = 0
        end
        school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
        school_course_id = school_course_id.reject { |s| s.to_s.empty? }
        if school_course_id.blank?
          school_course_id[0] = 0
        end
        batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
        extra_params = " ( batches.id IN (#{batches.join(",")}) )"
        unless @admission_no.blank?
          extra_params += " and students.admission_no Like '#{@admission_no}%%'"
        end
        extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
      elsif @filter_by_course.to_i == 2
        eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
        tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
        hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
        college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
        #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
        college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
        if college_courses_id.blank?
          college_courses_id[0] = 0
        end
        batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
        extra_params = " ( batches.id IN (#{batches.join(",")}) )"
        unless @admission_no.blank?
          extra_params += " and students.admission_no Like '#{@admission_no}%%'"
        end
        extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
      else
        unless @admission_no.blank?
          extra_params = " students.admission_no Like '#{@admission_no}%%'"
        end
        extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
      end
    else
      unless @admission_no.blank?
        extra_params = " students.admission_no Like '#{@admission_no}%%'"
      end
      extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
    end

    unless params[:filter_by_dues].nil?
      @filter_by_dues = params[:filter_by_dues]
    else
      @filter_by_dues = 0
    end
    #abort(extra_params.inspect)
    if @filter_by_dues.to_i == 0
      @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id").count
      @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id",:page => params[:page],:per_page => 10)
      student_ids = @students.map(&:student_id)
      student_ids = student_ids.reject { |s| s.to_s.empty? }
      if student_ids.blank?
        student_ids[0] = 0
      end
      unless extra_params.blank?
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :conditions => ["student_id IN (#{student_ids.join(",")}) and " + extra_params], :joins => extra_joins, :group => "student_id")
      else
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :conditions => ["student_id IN (#{student_ids.join(",")})" + extra_params], :joins => extra_joins, :group => "student_id")
      end
    elsif @filter_by_dues.to_i == 1
      @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) = sum( amount_paid )").count
      @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) = sum( amount_paid )",:page => params[:page],:per_page => 10)
      student_ids = @students.map(&:student_id)
      student_ids = student_ids.reject { |s| s.to_s.empty? }
      if student_ids.blank?
        student_ids[0] = 0
      end
      unless extra_params.blank?
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) = sum( amount_paid )", :conditions => ["student_id IN (#{student_ids.join(",")}) and " + extra_params], :joins => extra_joins, :group => "student_id")
      else
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) = sum( amount_paid )", :conditions => ["student_id IN (#{student_ids.join(",")})" + extra_params], :joins => extra_joins, :group => "student_id")
      end
    elsif @filter_by_dues.to_i == 2
      @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) > sum( amount_paid )").count
      @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) > sum( amount_paid )",:page => params[:page],:per_page => 10)
      student_ids = @students.map(&:student_id)
      student_ids = student_ids.reject { |s| s.to_s.empty? }
      if student_ids.blank?
        student_ids[0] = 0
      end
      unless extra_params.blank?
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) > sum( amount_paid )", :conditions => ["student_id IN (#{student_ids.join(",")}) and " + extra_params], :joins => extra_joins, :group => "student_id")
      else
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) > sum( amount_paid )", :conditions => ["student_id IN (#{student_ids.join(",")})" + extra_params], :joins => extra_joins, :group => "student_id")
      end
    elsif @filter_by_dues.to_i == 3
      @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) < sum( amount_paid )").count
      @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) < sum( amount_paid )",:page => params[:page],:per_page => 10)
      student_ids = @students.map(&:student_id)
      student_ids = student_ids.reject { |s| s.to_s.empty? }
      if student_ids.blank?
        student_ids[0] = 0
      end
      unless extra_params.blank?
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) < sum( amount_paid )", :conditions => ["student_id IN (#{student_ids.join(",")}) and " + extra_params], :joins => extra_joins, :group => "student_id")
      else
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) < sum( amount_paid )", :conditions => ["student_id IN (#{student_ids.join(",")})" + extra_params], :joins => extra_joins, :group => "student_id")
      end
    end
        
    render :update do |page|
      page << "req = false;"
      page.replace_html "data-records",:partial=> "student_ledger_list"
    end  
  end
  
  def ledger_pdf_fees
    extra_params = ""
    extra_joins = ""

    @admission_no = ""
    unless params[:admission_no].nil?
      @admission_no = params[:admission_no]
    end

    @filter_by_course = 0
    unless params[:filter_by_course].nil?
      @filter_by_course = params[:filter_by_course]
    end

    unless @filter_by_course.nil?
      if @filter_by_course.to_i == 1
        eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
        tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
        hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
        college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
        college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
        if college_courses_id.blank?
          college_courses_id[0] = 0
        end
        school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
        school_course_id = school_course_id.reject { |s| s.to_s.empty? }
        if school_course_id.blank?
          school_course_id[0] = 0
        end
        batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
        extra_params = " ( batches.id IN (#{batches.join(",")}) )"
        unless @admission_no.blank?
          extra_params += " and students.admission_no Like '#{@admission_no}%%'"
        end
        extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
      elsif @filter_by_course.to_i == 2
        eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
        tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
        hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
        college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
        #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
        college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
        if college_courses_id.blank?
          college_courses_id[0] = 0
        end
        batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
        extra_params = " ( batches.id IN (#{batches.join(",")}) )"
        unless @admission_no.blank?
          extra_params += " and students.admission_no Like '#{@admission_no}%%'"
        end
        extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
      else
        unless @admission_no.blank?
          extra_params = " students.admission_no Like '#{@admission_no}%%'"
        end
        extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
      end
    else
      unless @admission_no.blank?
        extra_params = " students.admission_no Like '#{@admission_no}%%'"
      end
      extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
    end

    unless params[:filter_by_dues].nil?
      @filter_by_dues = params[:filter_by_dues]
    else
      @filter_by_dues = 0
    end
    #abort(extra_params.inspect)
    if @filter_by_dues.to_i == 0
      unless extra_params.blank?
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id")
      else
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id")
      end
    elsif @filter_by_dues.to_i == 1
      unless extra_params.blank?
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) = sum( amount_paid )", :conditions => [extra_params], :joins => extra_joins, :group => "student_id")
      else
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) = sum( amount_paid )", :conditions => [extra_params], :joins => extra_joins, :group => "student_id")
      end
    elsif @filter_by_dues.to_i == 2
      @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) > sum( amount_paid )").count
      @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) > sum( amount_paid )",:page => params[:page],:per_page => 10)
      unless extra_params.blank?
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) > sum( amount_paid )", :conditions => [extra_params], :joins => extra_joins, :group => "student_id")
      else
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) > sum( amount_paid )", :conditions => [extra_params], :joins => extra_joins, :group => "student_id")
      end
    elsif @filter_by_dues.to_i == 3
      @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) < sum( amount_paid )").count
      @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) < sum( amount_paid )",:page => params[:page],:per_page => 10)
      unless extra_params.blank?
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) < sum( amount_paid )", :conditions => [extra_params], :joins => extra_joins, :group => "student_id")
      else
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) < sum( amount_paid )", :conditions => [extra_params], :joins => extra_joins, :group => "student_id")
      end
    end
    
    render :pdf => 'ledger_pdf_fees',
          :margin => {:top=> 10,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :orientation => 'Portrait',
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def ledger_csv_fees
    xtra_params = ""
    extra_joins = ""

    @admission_no = ""
    unless params[:admission_no].nil?
      @admission_no = params[:admission_no]
    end

    @filter_by_course = 0
    unless params[:filter_by_course].nil?
      @filter_by_course = params[:filter_by_course]
    end

    unless @filter_by_course.nil?
      if @filter_by_course.to_i == 1
        eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
        tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
        hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
        college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
        college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
        if college_courses_id.blank?
          college_courses_id[0] = 0
        end
        school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
        school_course_id = school_course_id.reject { |s| s.to_s.empty? }
        if school_course_id.blank?
          school_course_id[0] = 0
        end
        batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
        extra_params = " ( batches.id IN (#{batches.join(",")}) )"
        unless @admission_no.blank?
          extra_params += " and students.admission_no Like '#{@admission_no}%%'"
        end
        extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
      elsif @filter_by_course.to_i == 2
        eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
        tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
        hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
        college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
        #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
        college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
        if college_courses_id.blank?
          college_courses_id[0] = 0
        end
        batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
        extra_params = " ( batches.id IN (#{batches.join(",")}) )"
        unless @admission_no.blank?
          extra_params += " and students.admission_no Like '#{@admission_no}%%'"
        end
        extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
      else
        unless @admission_no.blank?
          extra_params = " students.admission_no Like '#{@admission_no}%%'"
        end
        extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
      end
    else
      unless @admission_no.blank?
        extra_params = " students.admission_no Like '#{@admission_no}%%'"
      end
      extra_joins = " INNER JOIN students ON students.id = student_fee_ledgers.student_id INNER JOIN batches ON batches.id = students.batch_id" 
    end

    unless params[:filter_by_dues].nil?
      @filter_by_dues = params[:filter_by_dues]
    else
      @filter_by_dues = 0
    end
    #abort(extra_params.inspect)
    if @filter_by_dues.to_i == 0
      unless extra_params.blank?
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id")
      else
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :joins => extra_joins, :group => "student_id")
      end
    elsif @filter_by_dues.to_i == 1
      unless extra_params.blank?
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) = sum( amount_paid )", :conditions => [extra_params], :joins => extra_joins, :group => "student_id")
      else
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) = sum( amount_paid )", :joins => extra_joins, :group => "student_id")
      end
    elsif @filter_by_dues.to_i == 2
      unless extra_params.blank?
        @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) > sum( amount_paid )").count
        @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) > sum( amount_paid )",:page => params[:page],:per_page => 10)
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) > sum( amount_paid )", :conditions => [extra_params], :joins => extra_joins, :group => "student_id")
      else
        @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC', :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) > sum( amount_paid )").count
        @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC', :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) > sum( amount_paid )",:page => params[:page],:per_page => 10)
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) > sum( amount_paid )", :joins => extra_joins, :group => "student_id")
      end
    elsif @filter_by_dues.to_i == 3
      unless extra_params.blank?
        @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) < sum( amount_paid )").count
        @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC', :conditions => [extra_params], :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) < sum( amount_paid )",:page => params[:page],:per_page => 10)
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) < sum( amount_paid )", :conditions => [extra_params], :joins => extra_joins, :group => "student_id")
      else
        @count_students = StudentFeeLedger.find(:all, :select => "student_id", :order => 'student_id ASC',  :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) < sum( amount_paid )").count
        @students = StudentFeeLedger.paginate(:all, :select => "student_id", :order => 'student_id ASC',  :joins => extra_joins, :group => "student_id", :having => "sum(amount_to_pay) < sum( amount_paid )",:page => params[:page],:per_page => 10)
        @student_fee_ledgers = StudentFeeLedger.find(:all, :select => "student_id, sum(amount_to_pay) as amount_to_pay, sum(amount_paid) as amount_paid, sum( amount_to_pay ) - sum( amount_paid ) as diff_amount", :order => 'student_id ASC', :having => "sum(amount_to_pay) < sum( amount_paid )", :joins => extra_joins, :group => "student_id")
      end
    end
    
    
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    
    fmt = Spreadsheet::Format.new :number_format => "0.00"
    title_format = Spreadsheet::Format.new({
      :weight           => :bold,
      :size             => 11,
      :horizontal_align => :centre
    })
  
    font_format = Spreadsheet::Format.new({
      :size             => 12
    });
    
    amount_format = Spreadsheet::Format.new({
      :number_format    => "0.00"
    });
    

    row_1 = ["Sl No","Student Name","Student ID","Amount to Pay","Paid Amount","Dues","Advance"]
    
    # Create a new Workbook
    new_book = Spreadsheet::Workbook.new

    # Create the worksheet
    new_book.create_worksheet :name => 'Student Ledger'

    # Add row_1
    new_book.worksheet(0).insert_row(0, row_1)
    new_book.worksheet(0).row(0).set_format(0, title_format)
    new_book.worksheet(0).row(0).set_format(1, title_format)
    new_book.worksheet(0).row(0).set_format(2, title_format)
    new_book.worksheet(0).row(0).set_format(3, title_format)
    new_book.worksheet(0).row(0).set_format(4, title_format)
    new_book.worksheet(0).row(0).set_format(5, title_format)
    new_book.worksheet(0).row(0).set_format(6, title_format)
    
    ind = 1
    total_amount = 0.00
    @student_fee_ledgers.each_with_index do |student_fee_ledger, i|
      student = Student.find(:first, :conditions => "id = #{student_fee_ledger.student_id}") 
      if student.nil?
        student = ArchivedStudent.find(:first, :conditions => "former_id = #{student_fee_ledger.student_id}")
      end
      dues = 0.00
      advance = 0.00
      if student_fee_ledger.diff_amount.to_f >= 0
        dues = student_fee_ledger.diff_amount.to_f
      end
      if student_fee_ledger.diff_amount.to_f < 0
        dues = 0.00
        advance = student_fee_ledger.diff_amount.to_f * -1
      end
      row_new = [i+1, student.full_name, student.admission_no, student_fee_ledger.amount_to_pay.to_f, student_fee_ledger.amount_paid.to_f, dues.to_f, advance.to_f]
      new_book.worksheet(0).insert_row(ind, row_new)
      new_book.worksheet(0).row(ind).set_format(0, font_format)
      new_book.worksheet(0).row(ind).set_format(1, font_format)
      new_book.worksheet(0).row(ind).set_format(2, font_format)
      new_book.worksheet(0).row(ind).set_format(3, amount_format)
      new_book.worksheet(0).row(ind).set_format(4, amount_format)
      new_book.worksheet(0).row(ind).set_format(5, amount_format)
      new_book.worksheet(0).row(ind).set_format(6, amount_format)
      new_book.worksheet(0).column(0).width = 10
      new_book.worksheet(0).column(1).width = 50
      new_book.worksheet(0).column(2).width = 15
      new_book.worksheet(0).column(3).width = 20
      new_book.worksheet(0).column(4).width = 20
      new_book.worksheet(0).column(5).width = 20
      new_book.worksheet(0).column(6).width = 20
      ind += 1
    end
    
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 

    filename = "ledger-#{Time.now.to_date.to_s}.xls"
    send_data spreadsheet.string, :filename => filename, :type =>  "application/vnd.ms-excel"
  end
  
  def transaction_pdf_fees
    fixed_category_name
    if date_format_check
      unless @start_date > @end_date
        online_id = []
        extra_params = ""
        extra_joins = ""
        @filter_by_course = params[:filter_by_course]
        unless params[:filter_by_course].nil?
          if params[:filter_by_course].to_i == 1
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            school_course_id = school_course_id.reject { |s| s.to_s.empty? }
            if school_course_id.blank?
              school_course_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          elsif params[:filter_by_course].to_i == 2
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          else
            batches = Batch.active.map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
            extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
            extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
            extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
          end
        else
          batches = Batch.active.map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
          extra_params = " and ( batches.id IN (#{batches.join(",")}) or archived_batches.id IN (#{batches.join(",")}) )"
          extra_joins = " LEFT JOIN students ON students.id = finance_transactions.payee_id LEFT  JOIN batches ON batches.id = students.batch_id" 
          extra_joins += " LEFT JOIN archived_students ON archived_students.former_id = finance_transactions.payee_id LEFT JOIN batches as archived_batches ON archived_batches.id = archived_students.batch_id" 
        end
        @filter_by_payment_type = params[:filter_by_payment_type]
        unless params[:filter_by_payment_type].nil?
          if params[:filter_by_payment_type].to_i != 0
            filter_by_payment_type = "MB"
            if params[:filter_by_payment_type].to_i == 1
              filter_by_payment_type = "MB"
            elsif params[:filter_by_payment_type].to_i == 2
              filter_by_payment_type = "ITCL"
            end 
            extra_params += ' and gateway_response like \'%%:payment_type: ' + filter_by_payment_type + '%%\''
          end
        end
        
        @fin_start_date = Configuration.find_by_config_key('FinancialYearStartDate').config_value
        @fin_end_date = Configuration.find_by_config_key('FinancialYearEndDate').config_value

        @particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, finance_fee_particular_categories.name, IFNULL(finance_fee_particular_categories.id, 0) as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false} " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN finance_fee_particulars ON finance_fee_particulars.id = finance_transaction_particulars.particular_id LEFT JOIN finance_fee_particular_categories ON finance_fee_particular_categories.id = finance_fee_particulars.finance_fee_particular_category_id " + extra_joins, :group => "transaction_date, finance_fee_particular_categories.id")
        tot_amount = 0
        @particular_wise_transactions.each do |pwt|
          tot_amount += pwt.amount
        end
        @transactions_advances = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, finance_fee_particular_categories.name, IFNULL(finance_fee_particular_categories.id, 0) as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Advance' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false} " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN finance_fee_particulars ON finance_fee_particulars.id = finance_transaction_particulars.particular_id LEFT JOIN finance_fee_particular_categories ON finance_fee_particular_categories.id = finance_fee_particulars.finance_fee_particular_category_id " + extra_joins, :group => "transaction_date, finance_fee_particular_categories.id")
        @transactions_discount = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, finance_fee_particular_categories.name, IFNULL(finance_fee_particular_categories.id, 0) as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' and finance_fee_particular_categories.is_deleted = #{false} " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN fee_discounts ON fee_discounts.id = finance_transaction_particulars.particular_id LEFT JOIN finance_fee_particular_categories ON finance_fee_particular_categories.id = fee_discounts.finance_fee_particular_category_id " + extra_joins, :group => "transaction_date, finance_fee_particular_categories.id")
        @transactions_discount_total_fees = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, 'Total Fee Discount' as  name, 0 as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount' and fee_discounts.finance_fee_particular_category_id = 0 and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id LEFT JOIN fee_discounts ON fee_discounts.id = finance_transaction_particulars.particular_id " + extra_joins, :group => "transaction_date")
        @transactions_fine = FinanceTransactionParticular.find(:all, :select => "CAST(payments.transaction_datetime as DATE) as transaction_date, 'Fine' as name, 0 as finance_fee_particular_category_id, sum( finance_transaction_particulars.amount ) as amount", :order => 'finance_transaction_particulars.transaction_date ASC', :conditions => ["finance_transaction_particulars.particular_type = 'Fine' and payments.transaction_datetime >= '#{@start_date.to_date.strftime("%Y-%m-%d 00:00:00")}' and payments.transaction_datetime <= '#{@end_date.to_date.strftime("%Y-%m-%d 23:59:59")}' " + extra_params], :joins => "INNER JOIN finance_transactions ON finance_transactions.id = finance_transaction_particulars.finance_transaction_id INNER JOIN payments ON finance_transactions.id = payments.finance_transaction_id " + extra_joins, :group => "transaction_date")
        
        unless @particular_wise_transactions.blank?
          @finance_particular_categories_id = @particular_wise_transactions.map(&:finance_fee_particular_category_id).uniq
          @finance_particular_categories_id = @finance_particular_categories_id.reject { |f| f.to_s.empty? }
          if @finance_particular_categories_id.blank?
            @finance_particular_categories_id[0] = 0
          end
          
          @finance_particular_categories = FinanceFeeParticularCategory.find(:all,:conditions => ["is_deleted = ? and id IN (" +  @finance_particular_categories_id.join(",") + ")", false])
        end
        
        render :pdf => 'transaction_pdf_fees',
          :margin => {:top=> 10,
          :bottom => 10,
          :left=> 10,
          :right => 10},
          :orientation => 'Portrait',
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      
      else
        flash[:warn_notice] = "#{t('flash17')}"
        redirect_to :action=>:monthly_report_fees
      end
    end
  end

  def update_monthly_report

    fixed_category_name
    @hr = Configuration.find_by_config_value("HR")
    if date_format_check
      unless @start_date > @end_date
        @transactions = FinanceTransaction.find(:all, :order => 'transaction_date desc', :conditions => ["transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'"])
        @fixed_cat_ids = @fixed_cat_ids.reject { |f| f.to_s.empty? }
        if @fixed_cat_ids.blank?
          @fixed_cat_ids[0] = 0
        end
        @other_transaction_categories = FinanceTransactionCategory.find(:all, :conditions => ["finance_transactions.transaction_date >= '#{@start_date}' and finance_transactions.transaction_date <= '#{@end_date}'and finance_transaction_categories.id NOT IN (#{@fixed_cat_ids.join(",")})"],:joins=>[:finance_transactions]).uniq
        @transactions_fees = FinanceTransaction.total_fees(@start_date,@end_date).map{|t| t.transaction_total.to_f}.sum
        @salary = FinanceTransaction.sum('amount',:conditions=>{:title=>"Monthly Salary",:transaction_date=>@start_date..@end_date}).to_f
        @donations_total = FinanceTransaction.donations_triggers(@start_date,@end_date)
        @grand_total = FinanceTransaction.grand_total(@start_date,@end_date)
        @category_transaction_totals = {}
        Champs21Plugin::FINANCE_CATEGORY.each do |category|
          @category_transaction_totals["#{category[:category_name]}"] =   FinanceTransaction.total_transaction_amount(category[:category_name],@start_date,@end_date)
        end
        @graph = open_flash_chart_object(960, 500, "graph_for_update_monthly_report?start_date=#{@start_date}&end_date=#{@end_date}")
      else
        flash[:warn_notice] = "#{t('flash17')}"
        redirect_to :action=>:monthly_report
      end
    end
  end

  def transaction_pdf
    fixed_category_name
    @hr = Configuration.find_by_config_value("HR")
    if date_format_check
      @transactions = FinanceTransaction.find(:all,
        :order => 'transaction_date desc', :conditions => ["transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'"])
      @fixed_cat_ids = @fixed_cat_ids.reject { |f| f.to_s.empty? }
      if @fixed_cat_ids.blank?
        @fixed_cat_ids[0] = 0
      end
      @other_transaction_categories = FinanceTransactionCategory.find(:all, :conditions => ["finance_transactions.transaction_date >= '#{@start_date}' and finance_transactions.transaction_date <= '#{@end_date}'and finance_transaction_categories.id NOT IN (#{@fixed_cat_ids.join(",")})"],:joins=>[:finance_transactions]).uniq
      @transactions_fees = FinanceTransaction.total_fees(@start_date,@end_date).map{|t| t.transaction_total.to_f}.sum
      @salary = FinanceTransaction.sum('amount',:conditions=>{:title=>"Monthly Salary",:transaction_date=>@start_date..@end_date}).to_f
      @donations_total = FinanceTransaction.donations_triggers(@start_date,@end_date)
      @grand_total = FinanceTransaction.grand_total(@start_date,@end_date)
      @category_transaction_totals = {}
      Champs21Plugin::FINANCE_CATEGORY.each do |category|
        @category_transaction_totals["#{category[:category_name]}"] =   FinanceTransaction.total_transaction_amount(category[:category_name],@start_date,@end_date)
      end
      render :pdf => 'transaction_pdf'
    end
  end

  def salary_department
    if date_format_check
      archived_employee_salary=FinanceTransaction.all(:select=>"sum(finance_transactions.amount) as amount,employee_departments.id,employee_departments.name",:conditions=>{:title=>"Monthly Salary",:transaction_date=>@start_date..@end_date},:joins=>"INNER JOIN archived_employees on archived_employees.former_id= finance_transactions.payee_id INNER JOIN employee_departments on employee_departments.id= archived_employees.employee_department_id",:group=>"employee_departments.id",:order=>"employee_departments.name").group_by(&:id)
      employee_salary=FinanceTransaction.all(:select=>"sum(finance_transactions.amount) as amount,employee_departments.id,employee_departments.name",:conditions=>{:title=>"Monthly Salary",:transaction_date=>@start_date..@end_date},:joins=>"INNER JOIN employees on employees.id= finance_transactions.payee_id LEFT OUTER JOIN employee_departments on employee_departments.id= employees.employee_department_id",:group=>"employee_departments.id",:order=>"employee_departments.name").group_by(&:id)
      @departments=EmployeeDepartment.all(:select=>"id, name" ,:order=>'name ASC')
      @departments.each do |d|
        total=0.0
        total+=archived_employee_salary[d.id].nil?? 0 : archived_employee_salary[d.id][0].amount.to_f
        total+=employee_salary[d.id].nil?? 0 : employee_salary[d.id][0].amount.to_f
        d['amount']=total
      end
    end
  end

  def salary_employee
    if date_format_check
      employee_salary=FinanceTransaction.all(:select=>"amount,employees.first_name ,employees.middle_name,employees.last_name,employees.id as employee_id ,finance_transactions.id",:conditions=>{:title=>"Monthly Salary",:transaction_date=>@start_date..@end_date,:employees=>{:employee_department_id=>params[:id]}},:joins=>"INNER JOIN employees on employees.id= finance_transactions.payee_id",:include=>:monthly_payslips)
      archived_employee_salary=FinanceTransaction.all(:select=>"amount,archived_employees.first_name ,archived_employees.middle_name,archived_employees.last_name,archived_employees.id as employee_id ,finance_transactions.id",:conditions=>{:title=>"Monthly Salary",:transaction_date=>@start_date..@end_date,:archived_employees=>{:employee_department_id=>params[:id]}},:joins=>"INNER JOIN archived_employees on archived_employees.former_id= finance_transactions.payee_id",:include=>:monthly_payslips)
      @employees_salary=archived_employee_salary+employee_salary
      @employees_salary.each{|employee| employee['salary_date']= employee.monthly_payslips.first.salary_date}
      @employees_salary=@employees_salary.sort_by{|salary| salary.salary_date}
      @department = EmployeeDepartment.find(params[:id])
    end
  end

  def employee_payslip_monthly_report
    if date_format(params[:salary_date]).nil?
      flash[:notice]="#{t('bad_request')}"
      return redirect_to :action=>:monthly_report
    end
    @employee = Employee.find_in_active_or_archived(params[:employee_id])
    @currency_type = currency
    ft=FinanceTransaction.find(params[:finance_transaction_id])
    @monthly_payslips=MonthlyPayslip.find(:all,:conditions=>{:finance_transaction_id=>ft.id},:include=>:payroll_category) if ft
    @individual_payslips =  IndividualPayslipCategory.find(:all,:conditions=>["employee_id=? AND salary_date = ?", params[:employee_id], params[:salary_date]])
    @salary  = Employee.calculate_salary(@monthly_payslips, @individual_payslips)
  end

  def donations_report
    if date_format_check
      category_id = FinanceTransactionCategory.find_by_name("Donation").id
      @donations = FinanceTransaction.find(:all,:order => 'transaction_date desc', :conditions => ["transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'and category_id ='#{category_id}'"])
    end
  end
  
  def bill_generation_report
#    @batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>true},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
#    @inactive_batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>false},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
#    @dates = []
    @batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>true},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
    batches = @batches.map{|b| b.course_id}
    #@courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    @courses = []
    @dates = []
    @sections = []
  end
  
  def particular_wise_report
    @date_today = Date.today
    end_of_month = @date_today.next_month.end_of_month
    start_month = end_of_month - 120
    
    @dates = FinanceFeeCollection.find(:all,:order=>'due_date DESC',:conditions => ["is_deleted = #{false} and due_date >= '#{start_month.to_date.strftime("%Y-%m-%d")}' and due_date <= '#{end_of_month.to_date.strftime("%Y-%m-%d")}'"] )
  end
  
  def bill_staus_report
    @date_today = Date.today
    end_of_month = @date_today.next_month.end_of_month
    start_month = end_of_month - 120
    
    @dates = FinanceFeeCollection.find(:all,:order=>'due_date DESC',:conditions => ["is_deleted = #{false} and due_date >= '#{start_month.to_date.strftime("%Y-%m-%d")}' and due_date <= '#{end_of_month.to_date.strftime("%Y-%m-%d")}'"] )
  end

  def fees_report
    month_date
    @batches= FinanceTransaction.total_fees(@start_date,@end_date)
    #fees_id = FinanceTransactionCategory.find_by_name('Fee').id
    #@fee_collections = FinanceFeeCollection.find(:all,:joins=>"INNER JOIN finance_fees ON finance_fees.fee_collection_id = finance_fee_collections.id INNER JOIN finance_transactions ON finance_transactions.finance_id = finance_fees.id AND finance_transactions.transaction_date >= '#{@start_date}' AND finance_transactions.transaction_date <= '#{@end_date}' AND finance_transactions.category_id = #{fees_id}",:group=>"finance_fee_collections.id")
  end

  def batch_fees_report
    month_date
    @fee_collection = FinanceFeeCollection.find(params[:id])
    @batch = Batch.find(params[:batch_id])
    @transaction =  FinanceTransaction.find(:all,:joins=>"INNER JOIN fee_transactions on fee_transactions.finance_transaction_id=finance_transactions.id INNER JOIN finance_fees on finance_fees.id=fee_transactions.finance_fee_id",:conditions=>["finance_fees.fee_collection_id='#{@fee_collection.id}' and finance_transactions.batch_id='#{@batch.id}' and finance_transactions.transaction_date >= '#{@start_date}' and finance_transactions.transaction_date <= '#{@end_date}'"])
  end

  def student_fees_structure

    month_date
    @student = Student.find(params[:id])
    @components = @student.get_fee_strucure_elements

  end

  # approve montly payslip ----------------------

  def approve_monthly_payslip
    @salary_dates = MonthlyPayslip.find(:all, :select => "distinct salary_date")

  end

  def one_click_approve
    @dates = MonthlyPayslip.find_all_by_salary_date(params[:salary_date],:conditions => ["is_approved = false"])
    @salary_date = params[:salary_date]
    render :update do |page|
      page.replace_html "approve",:partial=> "one_click_approve"
    end
  end

  def one_click_approve_submit
    dates = MonthlyPayslip.find_all_by_salary_date(Date.parse(params[:date]), :conditions=>["is_rejected is false"])

    dates.each do |d|
      d.approve(current_user.id,"Approved")
    end

    emp_ids = dates.map{|date| date.employee_id }.uniq.join(',')
    Delayed::Job.enqueue(PayslipTransactionJob.new(
        :salary_date => params[:date],
        :employee_id => emp_ids
      ))

    flash[:notice] = "#{t('flash8')}"
    redirect_to :action => "index"


  end

  def employee_payslip_approve
    dates = MonthlyPayslip.find_all_by_salary_date_and_employee_id(Date.parse(params[:id2]),params[:id])
    dates.each do |d|
      d.approve(current_user.id,params[:payslip_accept][:remark])
    end
    Delayed::Job.enqueue(PayslipTransactionJob.new(
        :salary_date => params[:id2],
        :employee_id => params[:id]
      ))
    flash[:notice] = "#{t('flash8')}"
    render :update do |page|
      page.reload
    end
  end
  
  def employee_payslip_reject
    dates = MonthlyPayslip.find_all_by_salary_date_and_employee_id(Date.parse(params[:id2]),params[:id])
    employee = Employee.find(params[:id])

    dates.each do |d|
      d.reject(current_user.id, params[:payslip_reject][:reason])
    end
    privilege = Privilege.find_by_name("PayslipPowers")
    hr_ids = privilege.user_ids
    subject = "#{t('payslip_rejected')}"
    body = "#{t('payslip_rejected_for')} "+ employee.first_name+" "+ employee.last_name+ " (#{t('employee_number')} : #{employee.employee_number})" +" #{t('for_the_month')} #{params[:id2].to_date.strftime("%B %Y")}"
    Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
        :recipient_ids => hr_ids,
        :subject=>subject,
        :body=>body ))
    render :update do |page|
      page.reload
    end
  end

  def employee_payslip_accept_form
    @id1 = params[:id]
    @id2 = params[:id2]
    respond_to do |format|
      format.js { render :action => 'accept' }
    end
  end

  def employee_payslip_reject_form
    @id1 = params[:id]
    @id2 = params[:id2]
    respond_to do |format|
      format.js { render :action => 'reject' }
    end
  end

  #view monthly payslip -------------------------------
  def view_monthly_payslip

    @departments = EmployeeDepartment.find(:all, :conditions=>"status = true", :order=> "name ASC")
    @salary_dates = MonthlyPayslip.find(:all,:select => "distinct salary_date")
    if request.post?
      post_data = params[:payslip]
      unless post_data.blank?
        if post_data[:salary_date].present? and post_data[:department_id].present?
          @payslips = MonthlyPayslip.find_and_filter_by_department(post_data[:salary_date],post_data[:department_id])
        else
          flash[:notice] = "#{t('select_salary_date')}"
          redirect_to :action=>"view_monthly_payslip"
        end
      end
    end
  end

  def view_employee_payslip
    @is_present_employee=true
    @is_present_employee=false if (Employee.find_by_id(params[:id]).nil?)
    @monthly_payslips = MonthlyPayslip.find(:all,:conditions=>["employee_id=? AND salary_date = ?",params[:id],params[:salary_date]],:include=>:payroll_category)
    @individual_payslips =  IndividualPayslipCategory.find(:all,:conditions=>["employee_id=? AND salary_date = ?",params[:id],params[:salary_date]])
    @salary  = Employee.calculate_salary(@monthly_payslips, @individual_payslips)
    @currency_type= currency
    if @monthly_payslips.blank?
      flash[:notice] = "#{t('no_paylips_found_for_this_employee')}"
      redirect_to :controller => "finance", :action => "view_monthly_payslip"
    end
  end

  def search_ajax
    other_conditions = ""
    other_conditions += " AND employee_department_id = '#{params[:employee_department_id]}'" unless params[:employee_department_id] == ""
    other_conditions += " AND employee_category_id = '#{params[:employee_category_id]}'" unless params[:employee_category_id] == ""
    other_conditions += " AND employee_position_id = '#{params[:employee_position_id]}'" unless params[:employee_position_id] == ""
    other_conditions += " AND employee_grade_id = '#{params[:employee_grade_id]}'" unless params[:employee_grade_id] == ""
    if params[:query].length>= 3
      @employee = Employee.find(:all,
        :conditions => ["(first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                       OR employee_number LIKE ? OR (concat(first_name, \" \", last_name) LIKE ?))" + other_conditions,
          "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
          "#{params[:query]}", "#{params[:query]}"],
        :order => "first_name asc") unless params[:query] == ''
    else
      @employee = Employee.find(:all,
        :conditions => ["(employee_number LIKE ?)" + other_conditions,"#{params[:query]}%"],
        :order => "first_name asc") unless params[:query] == ''
    end
    render :layout => false
  end

  #asset-liability-----------

  def create_liability
    @liability = Liability.new(params[:liability])
    render :update do |page|
      if @liability.save
        page.replace_html 'form-errors', :text => ''
        page << "Modalbox.hide();"
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg23')}</p>"
      else
        page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => @liability
        page.visual_effect(:highlight, 'form-errors')
      end
    end

  end

  def edit_liability
    @liability = Liability.find(params[:id])
  end

  def update_liability
    @liability = Liability.find(params[:id])
    @currency_type = currency

    render :update do |page|
      if @liability.update_attributes(params[:liability])
        @liabilities = Liability.find(:all,:conditions => 'is_deleted = 0')
        page.replace_html "liability_list", :partial => "liability_list"
        page << "Modalbox.hide();"
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg24')}</p>"
      else
        page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => @liability
        page.visual_effect(:highlight, 'form-errors')
      end
    end
  end

  def view_liability
    @liabilities = Liability.find(:all,:conditions => 'is_deleted = 0')
    @currency_type = currency
  end

  def liability_pdf
    @liabilities = Liability.find(:all,:conditions => 'is_deleted = 0')
    @currency_type = currency
    render :pdf => 'liability_report_pdf'
  end

  def delete_liability
    @liability = Liability.find(params[:id])
    @liability.update_attributes(:is_deleted => true)
    @liabilities = Liability.find(:all ,:conditions => 'is_deleted = 0')
    @currency_type = currency
    render :update do |page|
      page.replace_html "liability_list", :partial => "liability_list"
      page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg25')}</p>"
    end
  end

  def each_liability_view
    @liability = Liability.find(params[:id])
    @currency_type = currency
  end

  def create_asset
    @asset = Asset.new(params[:asset])
    render :update do |page|
      if @asset.save
        page.replace_html 'form-errors', :text => ''
        page << "Modalbox.hide();"
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg20')}</p>"

      else
        page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => @asset
        page.visual_effect(:highlight, 'form-errors')
      end
    end
  end

  def view_asset
    @assets = Asset.find(:all,:conditions => 'is_deleted = 0')
    @currency_type = currency
  end

  def asset_pdf
    @assets = Asset.find(:all,:conditions => 'is_deleted = 0')
    @currency_type = currency
    render :pdf => 'asset_report_pdf'
  end

  def edit_asset
    @asset = Asset.find(params[:id])
  end

  def update_asset
    @asset = Asset.find(params[:id])
    @currency_type = currency

    render :update do |page|
      if @asset.update_attributes(params[:asset])
        @assets = Asset.find(:all,:conditions => 'is_deleted = 0')
        page.replace_html "asset_list", :partial => "asset_list"
        page << "Modalbox.hide();"
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg21')}</p>"
      else
        page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => @asset
        page.visual_effect(:highlight, 'form-errors')
      end
    end
  end

  def delete_asset
    @asset = Asset.find(params[:id])
    @asset.update_attributes(:is_deleted => true)
    @assets = Asset.all(:conditions => 'is_deleted = 0')
    @currency_type = currency
    render :update do |page|
      page.replace_html "asset_list", :partial => "asset_list"
      page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg22')}</p>"
    end
  end

  def each_asset_view
    @asset = Asset.find(params[:id])
    @currency_type = currency
  end
  #fees ----------------

  def master_fees
    @finance_fee_category = FinanceFeeCategory.new
    @finance_fee_particular = FinanceFeeParticular.new
    @batchs = Batch.active
    @master_categories = FinanceFeeCategory.find(:all,:conditions=> ["is_deleted = '#{false}' and is_master = 1 and batch_id=?",params[:batch_id]]) unless params[:batch_id].blank?
    @student_categories = StudentCategory.active
  end

  def master_category_new
    @finance_fee_category = FinanceFeeCategory.new
    @batches = Batch.active
    respond_to do |format|
      format.js { render :action => 'master_category_new' }
    end
  end
  
  def master_particular_category_new
    @finance_fee_particular_category = FinanceFeeParticularCategory.new
    @finance_fee_particular_categories = FinanceFeeParticularCategory.active
    respond_to do |format|
      format.js { render :action => 'master_particular_category_new' }
    end
  end
  
  def set_combined_payment_configuration
    unless params[:student].nil?
      student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{params[:student].to_i} and config_key = '#{params[:key]}'")
      if student_fee_configuration.nil?
        student_fee_configuration = StudentFeeConfiguration.new
        student_fee_configuration.student_id = params[:student].to_i
        student_fee_configuration.config_key = params[:key]
        student_fee_configuration.config_value = params[:value]
        if student_fee_configuration.save
          if params[:value].to_i == 1
            render :update do |page|
              page << "enable_combined_payment();"
            end
          else
            render :update do |page|
              page << "remove_combined_payment();"
            end
          end
        else
          render :update do |page|
            page << "remove_combined_payment();"
          end
        end
      else
        student_fee_configuration.update_attributes(:config_value => params[:value])
        if params[:value].to_i == 1
          render :update do |page|
            page << "enable_combined_payment();"
          end
        else
          render :update do |page|
            page << "remove_combined_payment();"
          end
        end
      end
    else
      render :update do |page|
        page << "remove_combined_payment();"
      end
    end
  end
  
  def set_fine_payment_configuration
    unless params[:student].blank?
      unless params[:date_id].blank?
        student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{params[:student].to_i} and date_id = #{params[:date_id]} and config_key = '#{params[:key]}'")
        if student_fee_configuration.nil?
          student_fee_configuration = StudentFeeConfiguration.new
          student_fee_configuration.student_id = params[:student].to_i
          student_fee_configuration.date_id = params[:date_id].to_i
          student_fee_configuration.config_key = params[:key]
          student_fee_configuration.config_value = params[:value]
          if student_fee_configuration.save
            if params[:value].to_i == 1
              render :update do |page|
                page << "update_user_amount_with_fine(#{params[:date_id].to_i});"
              end
            else
              render :update do |page|
                page << "update_user_amount_without_fine(#{params[:date_id].to_i});"
              end
            end
          else
            render :update do |page|
              page << "alert('An error occur while enable/disabled fine for this student, Please try again later')"
            end
          end
        else
          student_fee_configuration.update_attributes(:config_value => params[:value])
          if params[:value].to_i == 1
            render :update do |page|
              page << "update_user_amount_with_fine(#{params[:date_id].to_i});"
            end
          else
            render :update do |page|
              page << "update_user_amount_without_fine(#{params[:date_id].to_i});"
            end
          end
        end
      else
        render :update do |page|
          page << "alert('An error occur while enable/disabled fine for this student, Please try again later')"
        end
      end
    else
      render :update do |page|
        page << "alert('An error occur while enable/disabled fine for this student, Please try again later')"
      end
    end
  end
  
  def particular_category_new
    @update = false
    @finance_fee_particular_category = FinanceFeeParticularCategory.new
    respond_to do |format|
      format.js { render :action => 'particular_category_new' }
    end
  end

  def master_category_create
    if request.post?

      if params[:finance_fee_category][:category_batches_attributes].present?
        FinanceFeeCategory.transaction do
          @finance_fee_category = FinanceFeeCategory.find_or_create_by_name_and_description_and_is_deleted(params[:finance_fee_category][:name],params[:finance_fee_category][:description],false)


          @finance_fee_category.is_master = true
          if @finance_fee_category.update_attributes(params[:finance_fee_category]) and @finance_fee_category.check_name_uniqueness

          else
            @batch_error=true if params[:finance_fee_category][:category_batches_attributes].nil?
            @error = true
            raise ActiveRecord::Rollback
          end
        end
      else
        @batch_error=true
        @finance_fee_category = FinanceFeeCategory.new(params[:finance_fee_category])
        @finance_fee_category.valid?
        @error = true
      end
      @master_categories = FinanceFeeCategory.find(:all,:conditions=> ["is_deleted = '#{false}' and is_master = 1"])
      respond_to do |format|
        format.js { render :action => 'master_category_create' }
      end
    end
  end
  
  def particular_category_create
    if request.post?
      if params[:finance_fee_particular_category][:name].present?
        @finance_fee_particular_category = FinanceFeeParticularCategory.find_or_create_by_name_and_description_and_is_deleted(params[:finance_fee_particular_category][:name],params[:finance_fee_particular_category][:description],false)
      else
        @errors = {}
        @errors[:errors] = {}
        @finance_fee_particular_category = FinanceFeeParticularCategory.new
        @finance_fee_particular_category.valid?
        @errors[:errors][:full_messages] = "Name can't be empty"
        @error = true
      end
      respond_to do |format|
        format.js { render :action => 'particular_category_create' }
      end
    end
  end

  def particular_category_edit
    @finance_fee_particular_category = FinanceFeeParticularCategory.find(params[:id])
    respond_to do |format|
      format.js { render :action => 'particular_category_edit' }
    end
  end
  
  def particular_category_delete
    if params[:id].present?
      @del = "Particular Category deleted successfully"
      finance_fee_particular_category = FinanceFeeParticularCategory.find(params[:id])
      finance_fee_particular_category.destroy
      @finance_fee_particular_category_id = params[:id]
      respond_to do |format|
        format.js { render :action => 'particular_category_delete' }
      end
    else
      @del = "No Particular Category found to delete"
      @finance_fee_particular_category_id = 0
      respond_to do |format|
        format.js { render :action => 'particular_category_delete' }
      end
    end
  end
  
  def particular_category_update
    @update = false
    finance_fee_category = FinanceFeeParticularCategory.find(params[:id])
    if (params[:finance_fee_particular_category][:name]==finance_fee_category.name) and (params[:finance_fee_particular_category][:description]==finance_fee_category.description)
      @finance_update_category = finance_fee_category
      @finance_fee_particular_category = FinanceFeeParticularCategory.new
      respond_to do |format|
        format.js { render :action => 'particular_category_new' }
      end
    else
      if params[:opt].to_i == 1
        finance_fee_category.update_attributes({:name => params[:finance_fee_particular_category][:name], :description => params[:finance_fee_particular_category][:description]})
        @update = true
      end
      
      @finance_update_category = finance_fee_category
      @finance_fee_particular_category = FinanceFeeParticularCategory.new
      respond_to do |format|
        format.js { render :action => 'particular_category_new' }
      end
    end
  end
  
  def master_category_edit
    @batch=Batch.find(params[:batch_id])
    @finance_fee_category = FinanceFeeCategory.find(params[:id])
    respond_to do |format|
      format.js { render :action => 'master_category_edit' }
    end
  end

  def master_category_update
    @batches=Batch.find(params[:batch_id])
    finance_fee_category = FinanceFeeCategory.find(params[:id])
    if (params[:finance_fee_category][:name]==finance_fee_category.name) and (params[:finance_fee_category][:description]==finance_fee_category.description)
      render :update do |page|
        @master_categories = @batches.finance_fee_categories.find(:all, :conditions =>["is_deleted = '#{false}' and is_master = 1 "])
        page.replace_html 'form-errors', :text => ''
        page << "Modalbox.hide();"
        page.replace_html 'categories', :partial => 'master_category_list'
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg13')}</p>"
        @error=false
      end
    else
      attributes=finance_fee_category.attributes
      attributes.delete_if{|key,value| ["id","name","description","created_at"].include? key }
      #@finance_fee_category=FinanceFeeCategory.new(attributes)
      @error=true
      render :update do |page|
        FinanceFeeCategory.transaction do
          @finance_fee_category=FinanceFeeCategory.find_or_create_by_name_and_description_and_is_deleted(params[:finance_fee_category][:name],params[:finance_fee_category][:description],false)
          if CategoryBatch.find_by_finance_fee_category_id_and_batch_id(@finance_fee_category.id,@batches.id).present?
            @error=true
            @finance_fee_category.errors.add_to_base(t('name_already_taken'))
          else
            if @finance_fee_category.update_attributes(attributes)
              @finance_fee_category.create_associates(finance_fee_category.id,@batches.id)
              cat_batch=CategoryBatch.find_by_finance_fee_category_id_and_batch_id(finance_fee_category.id,@batches.id)
              cat_batch.destroy if cat_batch
              finance_fee_category.update_attributes(:is_deleted => true) unless finance_fee_category.category_batches.present?
              @master_categories = @batches.finance_fee_categories.find(:all, :conditions =>["is_deleted = '#{false}' and is_master = 1 "])

              if @finance_fee_category.check_category_name_exists(@batches)
                page.replace_html 'form-errors', :text => ''
                page << "Modalbox.hide();"
                page.replace_html 'categories', :partial => 'master_category_list'
                page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg13')}</p>"
                @error=false
              else
                @error=true
                @finance_fee_category.errors.add_to_base(t('name_already_taken'))
              end
            end
          end
          if @error
            page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => @finance_fee_category

            page.visual_effect(:highlight, 'form-errors')
            raise ActiveRecord::Rollback
          end


        end
      end

    end
  end


  def master_category_particulars
    @batch=Batch.find(params[:batch_id])
    unless params[:id].blank?
      @is_common_particular = false
      @finance_fee_category = FinanceFeeCategory.find(params[:id])
      @particulars = FinanceFeeParticular.paginate(:page => params[:page],:conditions => ["finance_fee_particulars.is_deleted = '#{false}' and finance_fee_particulars.is_tmp = '#{false}' and finance_fee_particulars.finance_fee_category_id = '#{@finance_fee_category.id}' and finance_fee_particulars.batch_id='#{@batch.id}' "], :joins => [:finance_fee_particular_category])
    else
      @is_common_particular = true
      @particulars = FinanceFeeParticular.paginate(:page => params[:page],:conditions => ["finance_fee_particulars.is_deleted = '#{false}' and finance_fee_particulars.is_tmp = '#{false}' and finance_fee_particulars.finance_fee_category_id = '#{0}' and finance_fee_particulars.batch_id='#{@batch.id}' "], :joins => [:finance_fee_particular_category])
    end
  end
  
  def master_category_particulars_edit
    #DONE
    @finance_fee_particular= FinanceFeeParticular.find(params[:id])
    @student_categories = StudentCategory.active
    unless @finance_fee_particular.student_category.present? and @student_categories.collect(&:name).include?(@finance_fee_particular.student_category.name)
      current_student_category=@finance_fee_particular.student_category
      @student_categories << current_student_category if current_student_category.present?
    end
    respond_to do |format|
      format.js { render :action => 'master_category_particulars_edit' }
    end
  end

  def master_category_particulars_update
    @feeparticulars = FinanceFeeParticular.find( params[:id])
    render :update do |page|
      #params[:finance_fee_particular][:student_category_id]="" if params[:finance_fee_particular][:student_category_id].nil?
      if @feeparticulars.collection_exist
        if @feeparticulars.update_attributes(params[:finance_fee_particular])
          unless @feeparticulars.finance_fee_category_id.to_i == 0
            @finance_fee_category = FinanceFeeCategory.find(@feeparticulars.finance_fee_category_id)
            @particulars = FinanceFeeParticular.paginate(:page => params[:page],:conditions => ["is_deleted = '#{false}' and finance_fee_category_id = '#{@finance_fee_category.id}' and batch_id='#{@feeparticulars.batch_id}'"])
            page.replace_html 'form-errors', :text => ''
            page << "Modalbox.hide();"
            page.replace_html 'categories', :partial => 'master_particulars_list'
            page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg14')}</p>"
          else
            @particulars = FinanceFeeParticular.paginate(:page => params[:page],:conditions => ["is_deleted = '#{false}' and finance_fee_category_id = '#{0}' and batch_id='#{@feeparticulars.batch_id}'"])
            page.replace_html 'form-errors', :text => ''
            page << "Modalbox.hide();"
            page.replace_html 'categories', :partial => 'master_particulars_list'
            page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg14')}</p>"
          end
        else
          page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => @feeparticulars
          page.visual_effect(:highlight, 'form-errors')
        end
      else
        page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => @feeparticulars
        page.visual_effect(:highlight, 'form-errors')
      end
    end
    #    respond_to do |format|
    #      format.js { render :action => 'master_category_particulars' }
    #    end
  end
  
  def master_category_particulars_delete
    @feeparticular = FinanceFeeParticular.find( params[:id])
    #discounts=@feeparticular.finance_fee_category.fee_discounts.all(:conditions=>"batch_id=#{@feeparticular.batch_id}")
    @error=true unless @feeparticular.delete_particular

    @finance_fee_category = FinanceFeeCategory.find(@feeparticular.finance_fee_category_id)
    @particulars = FinanceFeeParticular.paginate(:page => params[:page],:conditions => ["is_deleted = '#{false}' and finance_fee_category_id = '#{@finance_fee_category.id}' and batch_id='#{@feeparticular.batch_id}' "])
    respond_to do |format|
      format.js { render :action => 'master_category_particulars' }
    end
  end
  
  def master_category_delete
    @error=false
    @batches=Batch.find(params[:batch_id])
    @finance_fee_category = FinanceFeeCategory.find(params[:id])
    @catbatch=CategoryBatch.find_by_finance_fee_category_id_and_batch_id(params[:id],params[:batch_id])
    unless @catbatch.destroy
      @catbatch.errors.add_to_base(t('fee_collection_exists_cant_delete_this_category'))
      @error=true
    end
    @finance_fee_category.update_attributes(:is_deleted => true) unless @finance_fee_category.category_batches.present?
    #@finance_fee_category.delete_particulars
    @master_categories = @batches.finance_fee_categories.find(:all, :conditions =>["is_deleted = '#{false}' and is_master = 1 "])
    respond_to do |format|
      format.js { render :action => 'master_category_delete' }
    end
  end

  def show_master_categories_list
    unless params[:id].empty?
      @finance_fee_category = FinanceFeeCategory.new
      @finance_fee_particular = FinanceFeeParticular.new
      @batches = Batch.find params[:id] unless params[:id] == ""
      @master_categories =@batches.finance_fee_categories.find(:all, :conditions =>["is_deleted = '#{false}' and is_master = 1"])
      #@master_categories = FinanceFeeCategory.find(:all,:conditions=> ["is_deleted = '#{false}' and is_master = 1 and batch_id=?",params[:id]])
      @student_categories = StudentCategory.active

      render :update do |page|
        page.replace_html 'categories', :partial => 'master_category_list'
      end
    else
      render :update do |page|
        page.replace_html 'categories', :text=>""
      end
    end
  end

  def fees_particulars_new
    @finance_fee_particular =FinanceFeeParticular.new()
    @fees_categories = FinanceFeeCategory.find(:all,:group=>'concat(name,description)',:conditions=> "is_deleted = 0 and is_master = 1")
    #@fees_categories.reject!{|f|f.batch.is_deleted or !f.batch.is_active }
    @student_categories = StudentCategory.active
    @all=true
    @student=false
    @category=false
  end

  def list_category_batch
    #DONE
    unless params[:category_id]== "0"
      fee_category=FinanceFeeCategory.find(params[:category_id])
      #@batches= Batch.find(:all,:joins=>"INNER JOIN `category_batches` ON `batches`.id = `category_batches`.batch_id INNER JOIN finance_fee_categories on finance_fee_categories.id=category_batches.finance_fee_category_id INNER JOIN courses on courses.id=batches.course_id",:conditions=>"finance_fee_categories.name = '#{fee_category.name}' and finance_fee_categories.description = '#{fee_category.description}'",:order=>"courses.code ASC")
      @batches=Batch.active.find(:all,:joins=>[{:category_batches=>:finance_fee_category},:course],:conditions=>"finance_fee_categories.id =#{fee_category.id}",:order=>"courses.code ASC").uniq
      #@batches=fee_category.batches.all(:order=>"name ASC")
      
      #@finance_fee_particular_categories_id = FinanceFeeParticular.find_all_by_finance_fee_category_id(params[:category_id]).map(&:finance_fee_particular_category_id).uniq.delete_if{ |x| x==0 }
      
      #unless @finance_fee_particular_categories_id.empty?
      #  @finance_fee_particular_categories = FinanceFeeParticularCategory.find(:all, :conditions => ["id NOT IN (?) and is_deleted = ?", @finance_fee_particular_categories_id, false])
      #else
      @finance_fee_particular_categories = FinanceFeeParticularCategory.active
      #end
    else
      @finance_fee_particular_categories = FinanceFeeParticularCategory.active
      @batches = Batch.active.find(:all,:order=>"courses.code ASC").uniq
    end
    render :update do |page|
      page.replace_html 'list-category-batch', :partial => 'list_category_batch'
      page.replace_html 'list-particular-category', :partial => 'list_particular_category'
    end
  end

  def fees_particulars_create
    if request.get?
      redirect_to :action => "fees_particulars_new"
    else
      @finance_category=FinanceFeeCategory.find_by_id(params[:finance_fee_particular][:finance_fee_category_id])
      @batches= Batch.find(:all,:joins=>"INNER JOIN `category_batches` ON `batches`.id = `category_batches`.batch_id INNER JOIN finance_fee_categories on finance_fee_categories.id=category_batches.finance_fee_category_id INNER JOIN courses on courses.id=batches.course_id",:conditions=>"finance_fee_categories.name = '#{@finance_category.name}' and finance_fee_categories.description = '#{@finance_category.description}'",:order=>"courses.code ASC") if  @finance_category
      if params[:particular] and params[:particular][:batch_ids]
        batches=Batch.find(params[:particular][:batch_ids])
        @cat_ids=params[:particular][:batch_ids]
        if params[:finance_fee_particular][:receiver_type]=="Student"
          
          @selected_student_ids = all_student_ids = student_ids = params[:fee_collection][:students]
          all_students = batches.map{|b| b.students.map{|stu| stu.id}}.flatten
          
          rejected_student_id = student_ids.select{|adm| !all_students.include? adm.to_i}
          
          unless (rejected_student_id.empty?)
            @error = true
            @finance_fee_particular = FinanceFeeParticular.new(params[:finance_fee_particular])
            @finance_fee_particular.batch_id=1
            @finance_fee_particular.save
            @finance_fee_particular.errors.add_to_base("#{rejected_student_id.join(',')} #{t('does_not_belong_to_batch')} #{batches.map{|batch| batch.full_name}.join(',')}")
          end
          
          selected_student_id = all_student_ids.select{|adm| all_students.include? adm.to_i}
          selected_student_id.each do |a|
            s = Student.find(a)
            if s.nil?
              @error = true
              @finance_fee_particular = FinanceFeeParticular.new(params[:finance_fee_particular])
              @finance_fee_particular.save
              @finance_fee_particular.errors.add_to_base("#{a} #{t('does_not_exist')}")
            end
          end
          
          unless @error
            selected_student_id.each do |a|
              s = Student.find(a)
              batch=s.batch
              @finance_fee_particular = batch.finance_fee_particulars.new(params[:finance_fee_particular])
              @finance_fee_particular.receiver_id=s.id
              @error = true unless @finance_fee_particular.save
            end
          end
        else
          batches.each do |batch|
            if params[:finance_fee_particular][:receiver_type]=="Batch"

              @finance_fee_particular = batch.finance_fee_particulars.new(params[:finance_fee_particular])
              @finance_fee_particular.receiver_id=batch.id
              @error = true unless @finance_fee_particular.save
            elsif params[:finance_fee_particular][:receiver_type]=="StudentCategory"
              @finance_fee_particular = batch.finance_fee_particulars.new(params[:finance_fee_particular])
              @error = true unless @finance_fee_particular.save
              @finance_fee_particular.errors.add_to_base("#{t('category_cant_be_blank')}") if params[:finance_fee_particular][:receiver_id]==""
            else

              @finance_fee_particular = batch.finance_fee_particulars.new(params[:finance_fee_particular])
              @error = true unless @finance_fee_particular.save
              @finance_fee_particular.errors.add_to_base("#{t('admission_no_cant_be_blank')}")
            end

          end
        end
      else
        @error=true
        @finance_fee_particular =FinanceFeeParticular.new(params[:finance_fee_particular])
        @finance_fee_particular.save
      end

      if @error
        @fees_categories = FinanceFeeCategory.find(:all,:group=>:name,:conditions=> "is_deleted = 0 and is_master = 1")
        @student_categories = StudentCategory.active

        @render=true
        if params[:finance_fee_particular][:receiver_type]=="Student"
          unless params[:particular][:batch_ids].nil? or params[:particular][:batch_ids].empty?
            @student=true
            batches=Batch.find(params[:particular][:batch_ids])
            batches_id = batches.map(&:id)
            batches_id = batches_id.reject { |b| b.to_s.empty? }
            if batches_id.blank?
              batches_id[0] = 0
            end
            @students = Student.active.find(:all, :conditions => "batch_id IN (" + batches_id.join(",") + ")", :order => 'first_name ASC, middle_name ASC, last_name ASC')
          end
        elsif params[:finance_fee_particular][:receiver_type]=="StudentCategory"
          @category=true
        else
          @all=true
        end

        render :action => 'fees_particulars_new'
      else
        flash[:notice]="#{t('particulars_created_successfully')}"
        redirect_to :action => "fees_particulars_new"
      end
    end
  end
  
  def add_fees_particulars_create
    if request.get?
      redirect_to :action => "fees_particulars_new"
    else
      @finance_category=FinanceFeeCategory.find_by_id(params[:finance_fee_category_id])
      @batches= Batch.find(:all,:joins=>"INNER JOIN `category_batches` ON `batches`.id = `category_batches`.batch_id INNER JOIN finance_fee_categories on finance_fee_categories.id=category_batches.finance_fee_category_id INNER JOIN courses on courses.id=batches.course_id",:conditions=>"finance_fee_categories.name = '#{@finance_category.name}' and finance_fee_categories.description = '#{@finance_category.description}'",:order=>"courses.code ASC") if  @finance_category
      if params[:batch_ids]
        batches=Batch.find(:all, :conditions => "id IN (#{params[:batch_ids]})")
        @cat_ids=params[:batch_ids]
        if params[:receiver_type]=="Student"
          @selected_student_ids = all_student_ids = student_ids = params[:students].split(",")
          #abort(@selected_student_ids.inspect)
          all_students = batches.map{|b| b.students.map{|stu| stu.id}}.flatten
          
          rejected_student_id = student_ids.select{|adm| !all_students.include? adm.to_i}
          
          unless (rejected_student_id.empty?)
            @error = true
            @finance_fee_particular = FinanceFeeParticular.new
            @finance_fee_particular.batch_id=1
            @finance_fee_particular.name = params[:particular_name]
            @finance_fee_particular.receiver_type = params[:receiver_type]
            @finance_fee_particular.amount = params[:amount]
            @finance_fee_particular.is_tmp = 1
            @finance_fee_particular.opt = 1
            @finance_fee_particular.finance_fee_category_id = params[:finance_fee_category_id]
            @finance_fee_particular.finance_fee_particular_category_id = params[:finance_fee_particular_category_id]
            @finance_fee_particular.save
            @finance_fee_particular.errors.add_to_base("#{rejected_student_id.join(',')} #{t('does_not_belong_to_batch')} #{batches.map{|batch| batch.full_name}.join(',')}")
          end
          
          selected_student_id = all_student_ids.select{|adm| all_students.include? adm.to_i}
          selected_student_id.each do |a|
            s = Student.find(a)
            if s.nil?
              @error = true
              @finance_fee_particular = FinanceFeeParticular.new
              @finance_fee_particular.name = params[:particular_name]
              @finance_fee_particular.receiver_type = params[:receiver_type]
              @finance_fee_particular.amount = params[:amount]
              @finance_fee_particular.is_tmp = 1
              @finance_fee_particular.opt = 1
              @finance_fee_particular.finance_fee_category_id = params[:finance_fee_category_id]
              @finance_fee_particular.finance_fee_particular_category_id = params[:finance_fee_particular_category_id]
              @finance_fee_particular.receiver_id=s.id
              @finance_fee_particular.batch_id = s.batch.id
              @finance_fee_particular.save
              @finance_fee_particular.errors.add_to_base("#{a} #{t('does_not_exist')}")
            end
          end
          
          unless @error
            selected_student_id.each do |a|
              s = Student.find(a)
              batch=s.batch
              @finance_fee_particular = FinanceFeeParticular.new
              @finance_fee_particular.name = params[:particular_name]
              @finance_fee_particular.receiver_type = params[:receiver_type]
              @finance_fee_particular.amount = params[:amount]
              @finance_fee_particular.is_tmp = 1
              @finance_fee_particular.opt = 1
              @finance_fee_particular.finance_fee_category_id = params[:finance_fee_category_id]
              @finance_fee_particular.finance_fee_particular_category_id = params[:finance_fee_particular_category_id]
              @finance_fee_particular.receiver_id=s.id
              @finance_fee_particular.batch_id = s.batch.id
              @error = true unless @finance_fee_particular.save
            end
          end
        else
          batches.each do |batch|
            @finance_fee_particular = FinanceFeeParticular.new
            @finance_fee_particular.name = params[:particular_name]
            @finance_fee_particular.receiver_type = params[:receiver_type]
            @finance_fee_particular.amount = params[:amount]
            @finance_fee_particular.is_tmp = 1
            @finance_fee_particular.opt = 1
            @finance_fee_particular.finance_fee_category_id = params[:finance_fee_category_id]
            @finance_fee_particular.finance_fee_particular_category_id = params[:finance_fee_particular_category_id]
            if params[:receiver_type]=="Batch"
              @finance_fee_particular.receiver_id = batch.id
              @finance_fee_particular.batch_id = batch.id
              @error = true unless @finance_fee_particular.save
            elsif params[:receiver_type]=="StudentCategory"
              @finance_fee_particular.receiver_id = params[:student_category_id]
              @finance_fee_particular.batch_id = batch.id
              @error = true unless @finance_fee_particular.save
              @finance_fee_particular.errors.add_to_base("#{t('category_cant_be_blank')}") if params[:receiver_id]==""
            else
              @finance_fee_particular.receiver_id = params[:student_category_id]
              @finance_fee_particular.batch_id = batch.id
              @error = true unless @finance_fee_particular.save
              @finance_fee_particular.errors.add_to_base("#{t('admission_no_cant_be_blank')}")
            end

          end
        end
      else
        @error=true
        @finance_fee_particular =FinanceFeeParticular.new
        @finance_fee_particular.save
      end

      if @error
        @fees_categories = FinanceFeeCategory.find(:all,:group=>:name,:conditions=> "is_deleted = 0 and is_master = 1")
        @student_categories = StudentCategory.active

        @render=true
        if params[:receiver_type]=="Student"
          unless params[:batch_ids].nil? or params[:batch_ids].empty?
            @student=true
            batches=Batch.find(:all, :conditions => "id IN (#{params[:batch_ids]})")
            batches_id = batches.map(&:id)
            batches_id = batches_id.reject { |b| b.to_s.empty? }
            if batches_id.blank?
              batches_id[0] = 0
            end
            @students = Student.active.find(:all, :conditions => "batch_id IN (" + batches_id.join(",") + ")", :order => 'first_name ASC, middle_name ASC, last_name ASC')
          end
        elsif params[:receiver_type]=="StudentCategory"
          @category=true
        else
          @all=true
        end
        render :update do |page|
          page << "j('#replaceIfDataSaved').empty()"
          page << "j('#replaceIfDataSaved').removeClass('addParticularRow')"
          page.replace_html 'replaceIfDataSaved', :partial => 'add_fee_particular_row'
        end
      else
        render :update do |page|
          page << "j('#replaceIfDataSaved').empty()"
          page << "j('#replaceIfDataSaved').removeClass('addParticularRow')"
          page.replace_html 'replaceIfDataSaved', :partial => 'add_fee_particular_row'
        end
      end
    end
  end

  def fees_particulars_new2
    #DONE
    @batch=Batch.find(params[:batch_id])
    @fees_category = FinanceFeeCategory.find(params[:category_id])
    @student_categories = StudentCategory.active
    
    @finance_fee_particular_categories_id = FinanceFeeParticular.find_all_by_finance_fee_category_id_and_batch_id(params[:category_id], params[:batch_id]).map(&:finance_fee_particular_category_id).uniq.delete_if{ |x| x==0 }
    
    unless @finance_fee_particular_categories_id.empty?
      @finance_fee_particular_categories = FinanceFeeParticularCategory.find(:all, :conditions => ["id NOT IN (?) and is_deleted = ?", @finance_fee_particular_categories_id, false])
    else
      @finance_fee_particular_categories = FinanceFeeParticularCategory.active
    end
    
    respond_to do |format|
      format.js { render :action => 'fees_particulars_new2' }
    end
  end

  def fees_particulars_create2
    batch=Batch.find(params[:finance_fee_particular][:batch_id])
    if params[:finance_fee_particular][:receiver_type]=="Student"
      all_student_ids = student_ids = params[:fee_collection][:students]
      all_students = batch.students.map{|stu| stu.id}.flatten
      rejected_student_id = student_ids.select{|sid| !all_students.include? sid.to_i}
      unless (rejected_student_id.empty?)
        @error = true
        @finance_fee_particular = batch.finance_fee_particulars.new(params[:finance_fee_particular])
        @finance_fee_particular.save
        @finance_fee_particular.errors.add_to_base("#{rejected_student_id.join(',')} #{t('does_not_belong_to_batch')} #{batch.full_name}")
      end

      selected_student_id = all_student_ids.select{|sid| all_students.include? sid.to_i}
      selected_student_id.each do |a|
        s = Student.find(a)
        if s.nil?
          @error = true
          @finance_fee_particular = batch.finance_fee_particulars.new(params[:finance_fee_particular])
          @finance_fee_particular.save
          @finance_fee_particular.errors.add_to_base("#{a} #{t('does_not_exist')}")
        end
      end
      unless @error
        unless selected_student_id.present?
          @finance_fee_particular=batch.finance_fee_particulars.new(params[:finance_fee_particular])
          @finance_fee_particular.save
          @finance_fee_particular.errors.add_to_base("#{t('admission_no_cant_be_blank')}")
          @error = true
        else
          selected_student_id.each do |a|
            s = Student.find(a)
            @finance_fee_particular = batch.finance_fee_particulars.new(params[:finance_fee_particular])
            @finance_fee_particular.receiver_id=s.id
            @error = true unless @finance_fee_particular.save
          end
        end
      end
    elsif params[:finance_fee_particular][:receiver_type]=="Batch"
      @finance_fee_particular = batch.finance_fee_particulars.new(params[:finance_fee_particular])
      @finance_fee_particular.receiver_id=batch.id
      @error = true unless @finance_fee_particular.save
    else
      @finance_fee_particular = batch.finance_fee_particulars.new(params[:finance_fee_particular])
      @error = true unless @finance_fee_particular.save
      @finance_fee_particular.errors.add_to_base("#{t('category_cant_be_blank')}") if params[:finance_fee_particular][:receiver_id]==""
    end
    @batch=batch
    @finance_fee_category = FinanceFeeCategory.find(params[:finance_fee_particular][:finance_fee_category_id])
    @particulars = FinanceFeeParticular.paginate(:page => params[:page],:conditions => ["is_deleted = '#{false}' and finance_fee_category_id = '#{@finance_fee_category.id}' and batch_id='#{@batch.id}' "])

  end

  def additional_fees_create_form
    @batches = Batch.active
    @student_categories = StudentCategory.active
  end

  def additional_fees_create

    batch = params[:additional_fees][:batch_id] unless params[:additional_fees][:batch_id].nil?
    # batch ||=[]
    @batches = Batch.active
    @user = current_user
    @students = Student.find_all_by_batch_id(batch) unless batch.nil?
    @additional_category = FinanceFeeCategory.new(
      :name => params[:additional_fees][:name],
      :description => params[:additional_fees][:description],
      :batch_id => params[:additional_fees][:batch_id]
    )
    if params[:additional_fees][:due_date].to_date >= params[:additional_fees][:end_date].to_date
      if @additional_category.save && params[:additional_fees][:start_date].strip.length!=0 && params[:additional_fees][:due_date].strip.length!=0 && params[:additional_fees][:end_date].strip.length!=0
        @collection_date = FinanceFeeCollection.create(
          :name => @additional_category.name,
          :start_date => params[:additional_fees][:start_date],
          :end_date => params[:additional_fees][:end_date],
          :due_date => params[:additional_fees][:due_date],
          :batch_id => params[:additional_fees][:batch_id],
          :fee_category_id => @additional_category.id
        )
        body = "<p>#{t('fee_submission_date_for')} "+@additional_category.name+" #{t('has_been_published')} <br />
                               #{t('fees_submiting_date_starts_on')}< br />
                               #{t('start_date')} : "+@collection_date.start_date.to_s+" <br />"+
          "#{t('end_date')} : "+@collection_date.end_date.to_s+" <br />"+
          "#{t('due_date')} : "+@collection_date.due_date.to_s
        subject = "#{t('fees_submission_date')}"
        @due_date = @collection_date.due_date.strftime("%Y-%b-%d") +  " 00:00:00"
        unless batch.empty?
          @students.each do |s|
            FinanceFee.create(:student_id => s.id,:fee_collection_id => @collection_date.id)
            Reminder.create(:sender=>@user.id, :recipient=>s.id, :subject=> subject,
              :body => body, :is_read=>false, :is_deleted_by_sender=>false,:is_deleted_by_recipient=>false)
          end
          Event.create(:title=> "#{t('fees_due')}", :description =>@additional_category.name, :start_date => @due_date.to_datetime, :end_date => @due_date.to_datetime, :is_due => true, :origin => @collection_date)
        else
          @batches.each do |b|
            @students = Student.find_all_by_batch_id(b.id)
            @students.each do |s|
              FinanceFee.create(:student_id => s.id,:fee_collection_id => @collection_date.id)
              Reminder.create(:sender=>@user.id, :recipient=>s.user.id, :subject=> subject,
                :body => body, :is_read=>false, :is_deleted_by_sender=>false,:is_deleted_by_recipient=>false)
            end
          end
          Event.create(:title=> "#{t('fees_due')}", :description =>@additional_category.name, :start_date => @due_date.to_datetime, :end_date => @due_date.to_datetime, :is_due => true, :origin => @collection_date)
        end
        flash[:notice] = "#{t('flash9')}"
        redirect_to(:action => "add_particulars" ,:id => @collection_date.id)
      else
        flash[:notice] = "#{t('flash10')}"
        redirect_to :action => "additional_fees_create_form"
      end
    else
      flash[:notice] = "#{t('flash11')}"
      redirect_to :action => "additional_fees_create_form"
    end
  end

  def additional_fees_edit
    @finance_fee_category = FinanceFeeCategory.find(params[:id])
    @collection_date = FinanceFeeCollection.find_by_fee_category_id(@finance_fee_category.id)
    respond_to do |format|
      format.js { render :action => 'additional_fees_edit' }
    end
    flash[:notice] = "#{t('flash26')}"
  end

  def additional_fees_update
    @finance_fee_category = FinanceFeeCategory.find(params[:id])
    @collection_date = FinanceFeeCollection.find_by_fee_category_id(@finance_fee_category.id)
    #    render :update do |page|

    if @finance_fee_category.update_attributes(:name =>params[:finance_fee_category][:name], :description =>params[:finance_fee_category][:description])
      if @collection_date.update_attributes(:start_date=>params[:additional_fees][:start_date], :end_date=>params[:additional_fees][:end_date],:due_date=>params[:additional_fees][:due_date])
        @collection_date.event.update_attributes(:start_date=>@collection_date.due_date.to_datetime, :end_date=>@collection_date.due_date.to_datetime)
        @additional_categories = FinanceFeeCategory.find(:all, :conditions =>["is_deleted = '#{false}' and is_master = '#{false}' and batch_id = '#{@finance_fee_category.batch_id}'"])
        #        page.replace_html 'form-errors', :text => ''
        #        page << "Modalbox.hide();"
        #        page.replace_html 'particulars', :partial => 'additional_fees_list'
        #        end
      else
        @error = true
      end
    else
      #        page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => @finance_fee_category
      #        page.visual_effect(:highlight, 'form-errors')
      @error = true
    end
    #    end
  end

  def additional_fees_delete
    @finance_fee_category = FinanceFeeCategory.find(params[:id])
    @finance_fee_category.update_attributes(:is_deleted => true)
    @finance_fee_collection = FinanceFeeCollection.find_by_fee_category_id(params[:id])
    @finance_fee_collection.update_attributes(:is_deleted => true)
    @finance_fee_category.delete_particulars
    # redirect_to :action => "additional_fees_list"
    @additional_categories = FinanceFeeCategory.find(:all, :conditions =>["is_deleted = '#{false}' and is_master = '#{false}' and batch_id = '#{@finance_fee_category.batch_id}'"])
    respond_to do |format|
      format.js { render :action => 'additional_fees_delete' }
      flash[:notice] = "#{t('flash27')}"
    end
  end

  def add_particulars
    @collection_date = FinanceFeeCollection.find(params[:id])
    @additional_category = FinanceFeeCategory.find(@collection_date.fee_category_id)
    @student_categories = StudentCategory.active
    @finance_fee_particulars = FeeCollectionParticular.new
    @finance_fee_particulars_list = FeeCollectionParticular.find(:all,:conditions => ["is_deleted = '#{false}' and finance_fee_collection_id = '#{@collection_date.id}'"])
  end

  def add_particulars_new
    @collection_date = FinanceFeeCollection.find(params[:id])
    @additional_category = FinanceFeeCategory.find(@collection_date.fee_category_id)
    @student_categories = StudentCategory.active
    @finance_fee_particulars = FeeCollectionParticular.new
  end

  def add_particulars_create
    @collection_date = FinanceFeeCollection.find(params[:id])
    @additional_category = FinanceFeeCategory.find(@collection_date.fee_category_id)
    @error = false
    unless params[:finance_fee_particulars][:admission_no].nil?
      unless params[:finance_fee_particulars][:admission_no].empty?
        posted_params = params[:finance_fee_particulars]
        admission_no = posted_params[:admission_no].split(",")
        posted_params.delete "admission_no"
        err = ""
        admission_no.each do |a|
          posted_params["admission_no"] = a.to_s
          @finance_fee_particulars = FeeCollectionParticular.new(posted_params)
          @finance_fee_particulars.finance_fee_collection_id = @collection_date.id
          s = Student.find_by_admission_no(a)
          unless s.nil?
            if (s.batch_id == @collection_date.batch_id) or (@collection_date.batch_id.nil?)
              unless @finance_fee_particulars.save
                @error = true
              end
            else
              @error = true
              err = err + "#{a}#{t('does_not_belong_to_batch')} #{@collection_date.batch.full_name}. <br />"
            end
          else
            @error = true
            err = err + "#{a} #{t('does_not_exist')}<br />"
          end
        end
        @finance_fee_particulars.errors.add(:admission_no," #{t('invalid')} : <br />" + err) if @error==true
        @finance_fee_particulars_list = FeeCollectionParticular.find(:all,:conditions => ["is_deleted = '#{false}' and finance_fee_collection_id = '#{@collection_date.id}'"])  unless @error== true
      else
        @error = true
        @finance_fee_particulars = FeeCollectionParticular.new(params[:finance_fee_particulars])
        @finance_fee_particulars.valid?
        @finance_fee_particulars.errors.add(:admission_no,"#{t('is_blank')}")
      end
    else
      @finance_fee_particulars = FeeCollectionParticular.new(params[:finance_fee_particulars])
      @finance_fee_particulars.finance_fee_collection_id = @collection_date.id
      unless @finance_fee_particulars.save
        @error = true
      else
        @finance_fee_particulars_list = FeeCollectionParticular.find(:all,:conditions => ["is_deleted = '#{false}' and finance_fee_collection_id = '#{@collection_date.id}'"])
      end

    end
  end

  def student_or_student_category
    @student_categories = StudentCategory.active

    select_value = params[:select_value]

    if select_value == "StudentCategory"
      render :update do |page|
        page.replace_html "student", :partial => "student_category_particulars"
      end
    elsif select_value == "Student"
      unless params[:batch_id].nil? or params[:batch_id].empty?
        unless params[:selected].nil? or params[:selected].empty?
          @selected_student_ids = params[:selected].split(",")
        else
          @selected_student_ids = []
        end
        if params[:batch_id].present?
          if params[:batch_id].index(",") 
            @batch_id = params[:batch_id]
            @students = Student.active.find(:all, :conditions => "batch_id IN (" + @batch_id + ")", :order => 'first_name ASC, middle_name ASC, last_name ASC')
          else
            @batch_id = params[:batch_id]
            @students = Student.active.find_all_by_batch_id(@batch_id, :order => 'first_name ASC, middle_name ASC, last_name ASC')
          end
        end
        render :update do |page|
          page.replace_html "student" ,:partial => "fee_collection_student_list_new"
        end
      else
        render :update do |page|
          page.replace_html "student" ,:text => "<p class='flash-msg'>#{t('flash_msg48')}</p>"
        end
      end
    elsif select_value == "Batch"
      render :update do |page|
        page.replace_html "student", :text=>""
      end
    end
  end
  
  def new_student_or_student_category
    @student_categories = StudentCategory.active

    select_value = params[:select_value]

    if select_value == "StudentCategory"
      render :update do |page|
        page.replace_html "student", :partial => "add_student_category_particulars"
      end
    elsif select_value == "Student"
      unless params[:batch_id].nil? or params[:batch_id].empty?
        unless params[:selected].nil? or params[:selected].empty?
          @selected_student_ids = params[:selected].split(",")
        else
          @selected_student_ids = []
        end
        if params[:batch_id].present?
          if params[:batch_id].index(",") 
            @batch_id = params[:batch_id]
            @students = Student.active.find(:all, :conditions => "batch_id IN (" + @batch_id + ")", :order => 'first_name ASC, middle_name ASC, last_name ASC')
          else
            @batch_id = params[:batch_id]
            @students = Student.active.find_all_by_batch_id(@batch_id, :order => 'first_name ASC, middle_name ASC, last_name ASC')
          end
        end
        render :update do |page|
          page.replace_html "student" ,:partial => "add_fee_collection_student_list"
        end
      else
        render :update do |page|
          page.replace_html "student" ,:text => "<p class='flash-msg'>#{t('flash_msg48')}</p>"
        end
      end
    elsif select_value == "Batch"
      render :update do |page|
        page.replace_html "student", :text=>""
      end
    end
  end

  def additional_fees_list
    @batchs=Batch.active
    #@additional_categories = FinanceFeeCategory.paginate(:page => params[:page],:conditions => ["is_deleted = '#{false}' and is_master = '#{false}'"])
  end

  def show_additional_fees_list
    @additional_categories = FinanceFeeCategory.find(:all,:conditions => ["is_deleted = '#{false}' and is_master = '#{false}' and batch_id=?",params[:id]])
    render :update do |page|
      page.replace_html 'particulars', :partial =>'additional_fees_list'
    end
  end

  def additional_particulars
    @additional_category = FinanceFeeCategory.find(params[:id])
    @collection_date = FinanceFeeCollection.find_by_fee_category_id(@additional_category.id)
    @particulars = FeeCollectionParticular.find(:all,:conditions => ["is_deleted = '#{false}' and finance_fee_collection_id = '#{@collection_date.id}' "])
  end

  def add_particulars_edit
    @finance_fee_particulars = FeeCollectionParticular.find(params[:id])
  end

  def add_particulars_update
    @finance_fee_particulars = FeeCollectionParticular.find(params[:id])
    render :update do |page|
      if @finance_fee_particulars.update_attributes(params[:finance_fee_particulars])
        @collection_date = @finance_fee_particulars.finance_fee_collection
        @additional_category =@collection_date.fee_category
        @particulars = FeeCollectionParticular.paginate(:page => params[:page],:conditions => ["is_deleted = '#{false}' and finance_fee_collection_id = '#{@collection_date.id}' "])
        page.replace_html 'form-errors', :text => ''
        page << "Modalbox.hide();"
        page.replace_html 'particulars', :partial => 'additional_particulars_list'
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg32')}</p>"
      else
        page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => @finance_fee_particulars
        page.visual_effect(:highlight, 'form-errors')
      end
    end
  end

  def add_particulars_delete
    @finance_fee_particulars = FeeCollectionParticular.find(params[:id])
    @finance_fee_particulars.update_attributes(:is_deleted => true)
    @collection_date = @finance_fee_particulars.finance_fee_collection
    @additional_category =@collection_date.fee_category
    @particulars = FeeCollectionParticular.paginate(:page => params[:page],:conditions => ["is_deleted = '#{false}' and finance_fee_collection_id = '#{@collection_date.id}' "])
    render :update do |page|
      page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('particulars_deleted_successfully')}</p>"
      page.replace_html 'particulars', :partial => 'additional_particulars_list'
    end
  end

  def fee_collection_batch_update
    if params[:id].present?
      id_str = params[:id].split("-")
      finance_fee_category_id = id_str[1]
      if finance_fee_category_id.to_i > 0
        finance_fee_particular_category_id = id_str[0]
        
        @fee_category = FinanceFeeCategory.find(finance_fee_category_id)
        is_late = 0
        if finance_fee_particular_category_id.index('F') == 0 
          is_late = 1
        end
        if finance_fee_particular_category_id.index('F') == 0 or finance_fee_particular_category_id.to_i == 0
          @batches=Batch.active.find(:all,:joins=>[{:finance_fee_particulars=>:finance_fee_category},:course],:conditions=>"finance_fee_categories.id =#{@fee_category.id} and finance_fee_particulars.is_deleted=#{false}",:order=>"courses.code ASC").uniq
        else
          @batches=Batch.active.find(:all,:joins=>[{:finance_fee_particulars=>:finance_fee_category},:course],:conditions=>"finance_fee_categories.id =#{@fee_category.id} and finance_fee_particulars.is_deleted=#{false} and finance_fee_particulars.finance_fee_particular_category_id = #{finance_fee_particular_category_id}",:order=>"courses.code ASC").uniq
        end
      end
    end
    render :update do |page|
      page.replace_html "batchs" ,:partial => "fee_collection_batchs_discount"
      if is_late == 1
        page << "$('discount_types_radio').hide()"
        page << "j('#fee_discount_discount').css('width','84%')"
        page << "$('percent_span').show()"
      else
        page << "$('discount_types_radio').show()"
        page << "j('#fee_discount_discount').css('width','82%')"
        page << "$('percent_span').hide()"
      end
    end
  end
  
  def fee_collection_batch_list
    @category_id = params[:id]
    if params[:id].present?
      require "yaml"
      transport_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/finance_transport.yml")['school']
      transport_particular_id = transport_config['transport_particular_id_' + MultiSchool.current_school.id.to_s]
      if transport_particular_id.blank?
        transport_particular_id = 0
      end
      
      if params[:id].to_i == 0
        @batches = Batch.active.find(:all,:joins=>[:finance_fee_particulars,:course],:conditions=>"finance_fee_particulars.finance_fee_category_id=0 and finance_fee_particulars.is_deleted=#{false}",:order=>"courses.code ASC").uniq
        @fee_particulars = FinanceFeeParticular.find(:all, :conditions=>"finance_fee_category_id=0 and finance_fee_particular_category_id != #{transport_particular_id.to_i} and is_tmp=#{false}",:group=>"finance_fee_particular_category_id").uniq
        @fee_particulars_transport = FinanceFeeParticularCategory.find(:all, :conditions=>"id = #{transport_particular_id.to_i}")
        #@all_particulars = FinanceFeeParticular.find(:all,:group=>"finance_fee_particular_category_id").uniq
        @all_particulars = FinanceFeeParticularCategory.active
      else  
        @fee_category=FinanceFeeCategory.find(params[:id])
        @batches = Batch.active.find(:all,:joins=>[{:finance_fee_particulars=>:finance_fee_category},:course],:conditions=>"finance_fee_categories.id =#{@fee_category.id} and finance_fee_particulars.is_deleted=#{false}",:order=>"courses.code ASC").uniq
        @fee_particulars = FinanceFeeParticular.find(:all, :conditions=>"finance_fee_category_id=#{@fee_category.id} and finance_fee_particular_category_id != #{transport_particular_id.to_i} and is_tmp=#{false}",:group=>"finance_fee_particular_category_id").uniq
        @fee_particulars_transport = FinanceFeeParticularCategory.find(:all, :conditions=>"id = #{transport_particular_id.to_i}")
        #@all_particulars = FinanceFeeParticular.find(:all,:group=>"finance_fee_particular_category_id").uniq
        @all_particulars = FinanceFeeParticularCategory.active
      end
    end
    render :update do |page|
      page.replace_html "batchs" ,:partial => "fee_collection_batchs"
      page.replace_html "category_generated_table" ,:partial => "fees_collection_particular_list"
    end
  end
  
  def fee_collection_batch_update_student
    if params[:id].present?
      id_str = params[:id].split("-")
      finance_fee_category_id = id_str[1]
      if finance_fee_category_id.to_i > 0
        finance_fee_particular_category_id = id_str[0]
        
        @fee_category = FinanceFeeCategory.find(finance_fee_category_id)
        is_late = 0
        if finance_fee_particular_category_id.index('F') == 0 
          is_late = 1
        end
        if finance_fee_particular_category_id.index('F') == 0 or finance_fee_particular_category_id.to_i == 0
          @batches=Batch.active.find(:all,:joins=>[{:finance_fee_particulars=>:finance_fee_category},:course],:conditions=>"finance_fee_categories.id =#{@fee_category.id} and finance_fee_particulars.is_deleted=#{false}",:order=>"courses.code ASC").uniq
        else
          @batches=Batch.active.find(:all,:joins=>[{:finance_fee_particulars=>:finance_fee_category},:course],:conditions=>"finance_fee_categories.id =#{@fee_category.id} and finance_fee_particulars.is_deleted=#{false} and finance_fee_particulars.finance_fee_particular_category_id = #{finance_fee_particular_category_id}",:order=>"courses.code ASC").uniq
        end
      end
    end
    render :update do |page|
      page.replace_html "batchs" ,:partial => "fee_collection_batch_student"
      if is_late == 1
        page << "$('discount_types_radio').hide()"
        page << "j('#fee_discount_discount').css('width','82%')"
        page << "$('percent_span').show()"
        page << "j('#fee_discount_batch_id').select2()"
      else
        page << "$('discount_types_radio').show()"
        page << "j('#fee_discount_discount').css('width','82%')"
        page << "$('percent_span').hide()"
        page << "j('#fee_discount_batch_id').select2()"
      end
    end
  end
  
  def update_student_for_advance
    if params[:id].present?
      id_str = params[:id].split("-")
      finance_fee_category_id = id_str[1]
      if finance_fee_category_id.to_i > 0
        finance_fee_particular_category_id = id_str[0]
        
        @fee_category = FinanceFeeCategory.find(finance_fee_category_id)
        is_late = 0
        if finance_fee_particular_category_id.index('F') == 0 
          is_late = 1
        end
        if finance_fee_particular_category_id.index('F') == 0 or finance_fee_particular_category_id.to_i == 0
          @batches=Batch.active.find(:all,:joins=>[{:finance_fee_particulars=>:finance_fee_category},:course],:conditions=>"finance_fee_categories.id =#{@fee_category.id} and finance_fee_particulars.is_deleted=#{false}",:order=>"courses.code ASC").uniq
        else
          @batches=Batch.active.find(:all,:joins=>[{:finance_fee_particulars=>:finance_fee_category},:course],:conditions=>"finance_fee_categories.id =#{@fee_category.id} and finance_fee_particulars.is_deleted=#{false} and finance_fee_particulars.finance_fee_particular_category_id = #{finance_fee_particular_category_id}",:order=>"courses.code ASC").uniq
        end
      end
    end
    render :update do |page|
      page.replace_html "batchs" ,:partial => "student_collection_for_advance"
      if is_late == 1
        page << "$('discount_types_radio').hide()"
        page << "j('#fee_discount_discount').css('width','82%')"
        page << "$('percent_span').show()"
      else
        page << "$('discount_types_radio').show()"
        page << "j('#fee_discount_discount').css('width','82%')"
        page << "$('percent_span').hide()"
      end
    end
  end
  
  def load_batch_student
    if params[:id].present?
      @batch_id = params[:id]
      @students = Student.active.find_all_by_batch_id(@batch_id, :order => 'first_name ASC, middle_name ASC, last_name ASC')
    end
    render :update do |page|
      page.replace_html "students" ,:partial => "fee_collection_student_list"
    end
  end
  
  def get_particular_by_category
    if params[:id].present?
      cat_id = params[:id]
      if cat_id.to_i != 0
        discount_type = params[:discount_type]
        select_options = ["Total Fees", "0-" + params[:id].to_s]
        finance_fee_particulars = FinanceFeeParticular.active.find(:all,:joins=>[:finance_fee_particular_category],:conditions=>"finance_fee_particulars.finance_fee_category_id =#{params[:id]} and finance_fee_particulars.is_tmp=#{false} and finance_fee_particulars.is_deleted=#{false}",:group=>"finance_fee_particulars.finance_fee_particular_category_id").uniq
        if discount_type.to_i == 1
          fines = Fine.active
          @particular = [select_options] + finance_fee_particulars.map{|p| [p.finance_fee_particular_category.name, p.finance_fee_particular_category.id.to_s + "-" + params[:id].to_s]} + fines.map{|p| [p.name, "F" + p.id.to_s + "-" + params[:id].to_s]}
        else
          @particular = [select_options] + finance_fee_particulars.map{|p| [p.finance_fee_particular_category.name, p.finance_fee_particular_category.id.to_s + "-" + params[:id].to_s]}
        end
      end
    end
    render :update do |page|
      page.replace_html "discount_on" ,:partial => "fee_discount_on"
      page.replace_html "batchs" ,:text => ""
      page.replace_html "students" ,:text => ""
    end
  end
  
  def particular_details
    unless params[:batches].nil?
      if params[:transport].to_i == 0
        @transport = false
        @batches = Batch.find(:all, :conditions => "id IN (#{params[:batches]})")
        @particular_category_id = params[:particular_category_id]
        @fee_category_id = params[:fee_category_id]
        #@fee_particulars = FinanceFeeParticular.find(:all, :conditions => "finance_fee_category_id = #{params[:fee_category_id]} and finance_fee_particular_category_id = #{params[:particular_category_id]} and batch_id IN (#{params[:batches]})")
        divs = params[:div_id]
        obj = params[:obj]
        render :update do |page|
          page.replace_html divs ,:partial => "particular_details"
          page << "j('##{divs}').show()"
          page << "j('##{obj}').text('Hide Particular Details');"
          page << "j('##{obj}').removeClass('particular_details');"
          page << "j('##{obj}').addClass('hide_particular_details');"
          page << 'j(".select2-combo").select2(); '
        end
      else
        @transport = true
        @vehicles = Vehicle.all
        @particular_category_id = params[:particular_category_id]
        @fee_category_id = params[:fee_category_id]
        #@fee_particulars = FinanceFeeParticular.find(:all, :conditions => "finance_fee_category_id = #{params[:fee_category_id]} and finance_fee_particular_category_id = #{params[:particular_category_id]} and batch_id IN (#{params[:batches]})")
        divs = params[:div_id]
        obj = params[:obj]
        render :update do |page|
          page.replace_html divs ,:partial => "particular_details"
          page << "j('##{divs}').show()"
          page << "j('##{obj}').text('Hide Particular Details');"
          page << "j('##{obj}').removeClass('particular_details');"
          page << "j('##{obj}').addClass('hide_particular_details');"
          page << 'j(".select2-combo").select2(); '
        end
      end
    else
      divs = params[:div_id]
      render :update do |page|
        page.replace_html divs ,:text => ""
      end
    end
  end
  
  def particular_batch_details
    unless params[:transport].blank?
      if params[:transport].to_i == 0
        unless params[:batch_id].blank?
          @transport = false
          @batch_id = params[:batch_id]
          @fee_particulars = FinanceFeeParticular.find(:all, :conditions => "is_deleted = #{false} and finance_fee_category_id = #{params[:fee_category_id]} and finance_fee_particular_category_id = #{params[:particular_category_id]} and batch_id = #{params[:batch_id]}")
          render :update do |page|
            page.replace_html "batch_particular_info" ,:partial => "particular_batch_details"
            page << 'j(".spinner-batch").remove(); '
          end
        else
          render :update do |page|
            page.replace_html "batch_particular_info",:text => ""
            page << 'j(".spinner-batch").remove(); '
          end
        end
      else
        unless params[:vehicle_id].blank?
          @transport = true
          @vehicle_id = params[:vehicle_id]
          @transports = Transport.find_all_by_vehicle_id(@vehicle_id)
          render :update do |page|
            page.replace_html "batch_particular_info" ,:partial => "particular_batch_details"
            page << 'j(".spinner-batch").remove(); '
          end
        else
          render :update do |page|
            page.replace_html "batch_particular_info",:text => ""
            page << 'j(".spinner-batch").remove(); '
          end
        end
      end
    else
      render :update do |page|
        page.replace_html "batch_particular_info",:text => ""
        page << 'j(".spinner-batch").remove(); '
      end
    end
  end
  
  def get_particular_by_category_student
    if params[:id].present?
      select_options = ["Total Fees", "0-" + params[:id].to_s]
      finance_fee_particulars = FinanceFeeParticular.active.find(:all,:joins=>[:finance_fee_particular_category],:conditions=>"finance_fee_particulars.finance_fee_category_id =#{params[:id]} and finance_fee_particulars.is_tmp=#{false} and finance_fee_particulars.is_deleted=#{false}",:group=>"finance_fee_particulars.finance_fee_particular_category_id").uniq
      fines = Fine.active
      @particular = [select_options] + finance_fee_particulars.map{|p| [p.finance_fee_particular_category.name, p.finance_fee_particular_category.id.to_s + "-" + params[:id].to_s]} + fines.map{|p| [p.name, "F" + p.id.to_s + "-" + params[:id].to_s]}
    end
    render :update do |page|
      page.replace_html "discount_on" ,:partial => "fee_discount_on_student"
      page.replace_html "batchs" ,:text => ""
    end
  end
  
  def get_particular_by_student
    if params[:id].present?
      select_options = ["Total Fees", "0-" + params[:id].to_s]
      finance_fee_particulars = FinanceFeeParticular.active.find(:all,:joins=>[:finance_fee_particular_category],:conditions=>"finance_fee_particulars.finance_fee_category_id =#{params[:id]} and finance_fee_particulars.is_tmp=#{false} and finance_fee_particulars.is_deleted=#{false}",:group=>"finance_fee_particulars.finance_fee_particular_category_id").uniq
      @particular = [select_options] + finance_fee_particulars.map{|p| [p.name, p.finance_fee_particular_category.id.to_s + "-" + params[:id].to_s]}
    end
    render :update do |page|
      page.replace_html "particular-box" ,:partial => "fee_particular_for_student"
      page.replace_html "batchs" ,:text => ""
    end
  end

  def fee_collection_new
    @fines=Fine.active
    @fee_categories=FinanceFeeCategory.find(:all, :conditions => "finance_fee_categories.is_visible = 1", :joins=>"INNER JOIN finance_fee_particulars on finance_fee_particulars.finance_fee_category_id=finance_fee_categories.id AND finance_fee_particulars.is_tmp = 0 AND finance_fee_particulars.is_deleted = 0 INNER JOIN batches on batches.id=finance_fee_particulars.batch_id AND batches.is_active = 1 AND batches.is_deleted = 0 AND finance_fee_categories.is_deleted=0",:group=>'concat(finance_fee_categories.name,finance_fee_categories.description)')
    @finance_fee_collection = FinanceFeeCollection.new
  end

  def fee_collection_create
    sent_remainder = false
    unless params[:sent_remainder].nil?
      sent_remainder = true
    end
    
    auto_adjust_advance = false
    unless params[:auto_adjust_advance].nil?
      auto_adjust_advance = true
    end
    
    for_admission = false
    unless params[:for_admission].nil?
      for_admission = true
    end
    #abort(auto_adjust_advance.inspect)
    particular_ids = []
    unless params[:particular_id].nil?
      particular_ids = params[:particular_id]
    end
    
    transport_particular_id = []
    unless params[:transport_particular_id].nil?
      transport_particular_id = params[:transport_particular_id]
    end
    
    particular_names = []
    unless params[:particular_name].nil?
      particular_names = params[:particular_name]
    end
    
    default_particular_names = []
    unless params[:default_particular_name].nil?
      default_particular_names = params[:default_particular_name]
    end
    
    transport_particular_name = []
    unless params[:transport_particular_name].nil?
      transport_particular_name = params[:transport_particular_name]
    end
    
    default_transport_particular_name = []
    unless params[:default_transport_particular_name].nil?
      default_transport_particular_name = params[:default_transport_particular_name]
    end
    
    @user = current_user
    @fee_categories=FinanceFeeCategory.find(:all,:joins=>"INNER JOIN finance_fee_particulars on finance_fee_particulars.finance_fee_category_id=finance_fee_categories.id AND finance_fee_particulars.is_tmp = 0 AND finance_fee_particulars.is_deleted = 0 INNER JOIN batches on batches.id=finance_fee_particulars.batch_id AND batches.is_active = 1 AND batches.is_deleted = 0 AND finance_fee_categories.is_deleted=0",:group=>'finance_fee_categories.name')
    unless params[:finance_fee_collection].nil?
      fee_category_name = params[:finance_fee_collection][:fee_category_id]
      @fee_category = FinanceFeeCategory.find_all_by_id(fee_category_name, :conditions=>['is_deleted is false'])
    end
    category =[]
    @finance_fee_collection = FinanceFeeCollection.new
    if request.post?
      
      #Delayed::Job.enqueue(DelayedFeeCollectionJob.new(@user,params[:finance_fee_collection],params[:fee_collection], sent_remainder, particular_ids, particular_names, transport_particular_id, transport_particular_name, default_particular_names, default_transport_particular_name, auto_adjust_advance, for_admission))
      @collection = params[:finance_fee_collection]
      @fee_collection = params[:fee_collection]
      @sent_remainder = sent_remainder
      @particular_ids = particular_ids
      @particular_names = particular_names
      @default_particular_names = default_particular_names
      @transport_particular_id = transport_particular_id
      @transport_particular_name = transport_particular_name
      @default_transport_particular_name = default_transport_particular_name
      @auto_adjust_advance = auto_adjust_advance
      @for_admission = for_admission
      
      finance_fee_category_id = @collection[:fee_category_id]
      if finance_fee_category_id.to_i != 0
        finance_fee_category = FinanceFeeCategory.find(finance_fee_category_id)
        new_finance_fee_category = FinanceFeeCategory.new
        new_finance_fee_category.name = @collection[:name]
        new_finance_fee_category.is_master = finance_fee_category.is_master
        new_finance_fee_category.is_visible = 0
        new_finance_fee_category.parent_id = finance_fee_category_id
      else
        new_finance_fee_category = FinanceFeeCategory.new
        new_finance_fee_category.name = "Common"
        new_finance_fee_category.is_master = true
        new_finance_fee_category.is_visible = 0
        new_finance_fee_category.parent_id = finance_fee_category_id
      end
      if new_finance_fee_category.save
        finance_fees_auto_category = FinanceFeesAutoCategory.new
        finance_fees_auto_category.finance_fee_category_id = finance_fee_category_id
        finance_fees_auto_category.finance_fee_auto_category_id = new_finance_fee_category.id
        finance_fees_auto_category.save
        #abort('here')
        @collection[:fee_category_id] = new_finance_fee_category.id

        unless @fee_collection.nil?
          category = @fee_collection[:category_ids]
          subject = "#{t('fees_submission_date')}"

          @finance_fee_collection = FinanceFeeCollection.new(
            :name => @collection[:name],
            :title => @collection[:title],
            :start_date => @collection[:start_date],
            :end_date => @collection[:end_date],
            :due_date => @collection[:due_date],
            :fee_category_id => @collection[:fee_category_id],
            :fine_id=>@collection[:fine_id],
            :for_admission=>@for_admission
          )
          FinanceFeeCollection.transaction do
            if @finance_fee_collection.save
              @particular_ids.each_with_index do |p_id, i|
                finance_fee_particulars = FinanceFeeParticular.find(:all, :conditions => "finance_fee_particular_category_id = #{p_id.to_i} and finance_fee_category_id = #{finance_fee_category_id} and is_deleted = #{false}")
                unless finance_fee_particulars.nil?
                  finance_fee_particulars.each do |ffp|
                    p_name = ffp.name
                    default_particular_name = @default_particular_names[i]
                    if default_particular_name.to_i == 0
                      particular_name = @particular_names[i]
                      p_names = particular_name.split("_")
                      p_name_id = p_names[0]
                      if p_name_id.to_i == ffp.finance_fee_particular_category_id.to_i
                        p_name = particular_name.gsub(p_id.to_s + "_", "")
                      end
                    end
                    execute = true
                    if ffp.receiver_type == "Batch"
                      particular_batch = Batch.find(:first, :conditions => "id = #{ffp.receiver_id}")
                      if particular_batch.blank?
                        execute = false
                      end
                    elsif ffp.receiver_type == "StudentCategory"
                      particular_student_category = StudentCategory.find(:first, :conditions => "id = #{ffp.receiver_id}")
                      if particular_student_category.blank?
                        execute = false
                      end
                    elsif ffp.receiver_type == "Student"
                      particular_student = Student.find(:first, :conditions => "id = #{ffp.receiver_id}")
                      if particular_student.blank?
                        execute = false
                      end
                    end
                    if execute
                      finance_fee_particular_new = FinanceFeeParticular.new
                      finance_fee_particular_new.name = p_name
                      finance_fee_particular_new.amount = ffp.amount
                      finance_fee_particular_new.finance_fee_category_id = new_finance_fee_category.id
                      finance_fee_particular_new.finance_fee_particular_category_id = ffp.finance_fee_particular_category_id
                      finance_fee_particular_new.receiver_id = ffp.receiver_id
                      finance_fee_particular_new.receiver_type = ffp.receiver_type
                      finance_fee_particular_new.batch_id = ffp.batch_id
                      finance_fee_particular_new.is_tmp = ffp.is_tmp
                      finance_fee_particular_new.opt = 0
                      finance_fee_particular_new.save
                    end
                  end
                end
              end

              unless @transport_particular_id.blank?
                @transport_particular_id.each_with_index do |p_id, i|
                  @vehicles = Vehicle.all
                  unless @vehicles.blank?
                    @vehicles.each do |vehicle|
                      @transports = Transport.find_all_by_vehicle_id(vehicle.id)
                      unless @transports.nil?
                        @transports.each do |transport|
                          default_transport_particular_name = @default_transport_particular_name[i]
                          if default_transport_particular_name.to_i == 1
                            p_name = "Transport Fee"
                          else  
                            particular_name = @transport_particular_name[i]
                            if particular_name.blank?
                              p_name = "Transport Fee"
                            else
                              p_names = particular_name.split("_")
                              p_name_id = p_names[0]
                              if p_name_id.to_i == p_id.to_i
                                p_name = particular_name.gsub(p_id.to_s + "_", "")
                              end
                            end
                          end
                          s = Student.find(:first, :conditions => "id = #{transport.receiver_id}")
                          unless s.blank?
                            finance_fee_particular_new = FinanceFeeParticular.new
                            finance_fee_particular_new.name = p_name
                            finance_fee_particular_new.description = "--Vehical No: ---" + vehicle.vehicle_no + ", --Route: ---" + transport.route.destination unless transport.route.nil?
                            finance_fee_particular_new.amount = transport.bus_fare
                            finance_fee_particular_new.finance_fee_category_id = new_finance_fee_category.id
                            finance_fee_particular_new.finance_fee_particular_category_id = p_id
                            finance_fee_particular_new.receiver_id = transport.receiver_id
                            finance_fee_particular_new.receiver_type = 'Student'
                            finance_fee_particular_new.batch_id = s.batch_id
                            finance_fee_particular_new.is_tmp = 0
                            finance_fee_particular_new.opt = 0
                            finance_fee_particular_new.save
                          end
                        end
                      end
                    end
                  end
                end
              end

              fee_discounts = FeeDiscount.all(:conditions=>"finance_fee_category_id=#{finance_fee_category_id} and is_deleted = #{false}")
              unless fee_discounts.nil?
                fee_discounts.each do |f|
                  execute = true
                  if f.receiver_type == "Batch"
                    discount_batch = Batch.find(:first, :conditions => "id = #{f.receiver_id}")
                    if discount_batch.blank?
                      execute = false
                    end
                  elsif f.receiver_type == "StudentCategory"
                    discount_student_category = StudentCategory.find(:first, :conditions => "id = #{f.receiver_id}")
                    if discount_student_category.blank?
                      execute = false
                    end
                  elsif f.receiver_type == "Student"
                    discount_student = Student.find(:first, :conditions => "id = #{f.receiver_id}")
                    if discount_student.blank?
                      execute = false
                    end
                  end
                  if execute
                    fee_discount_new = FeeDiscount.new
                    fee_discount_new.name = f.name
                    fee_discount_new.type = f.type
                    fee_discount_new.is_onetime = f.is_onetime
                    fee_discount_new.receiver_id = f.receiver_id
                    fee_discount_new.scholarship_id = f.scholarship_id
                    fee_discount_new.finance_fee_category_id = new_finance_fee_category.id
                    fee_discount_new.finance_fee_particular_category_id = f.finance_fee_particular_category_id
                    fee_discount_new.is_late = f.is_late
                    fee_discount_new.is_visible = f.is_visible
                    fee_discount_new.is_amount = f.is_amount
                    fee_discount_new.discount = f.discount
                    fee_discount_new.receiver_type = f.receiver_type
                    fee_discount_new.batch_id = f.batch_id
                    fee_discount_new.parent_id = f.id
                    fee_discount_new.save
                  end
                end
              end

              new_event =  Event.create(:title=> "Fees Due", :description =>@collection[:name], :start_date => @finance_fee_collection.due_date.to_datetime, :end_date => @finance_fee_collection.due_date.to_datetime, :is_due => true , :origin=>@finance_fee_collection)
              category.each do |b|
                b=b.to_i
                FeeCollectionBatch.create(:finance_fee_collection_id=>@finance_fee_collection.id,:batch_id=>b)
                fee_category_name = @collection[:fee_category_id]
                @students = Student.find_all_by_batch_id(b)
                @fee_category= FinanceFeeCategory.find_by_id(@collection[:fee_category_id])

                unless @fee_category.fee_particulars.all(:conditions=>"is_tmp = 0 and is_deleted=false and batch_id=#{b}").collect(&:receiver_type).include?"Batch"
                  cat_ids=@fee_category.fee_particulars.select{|s| s.receiver_type=="StudentCategory"  and (!s.is_deleted and s.batch_id==b.to_i)}.collect(&:receiver_id)
                  student_ids=@fee_category.fee_particulars.select{|s| s.receiver_type=="Student" and (!s.is_deleted and s.batch_id==b.to_i)}.collect(&:receiver_id)
                  @students = @students.select{|stu| (cat_ids.include?stu.student_category_id or student_ids.include?stu.id)}
                end
                body = "<p><b>#{t('fee_submission_date_for')} <i>"+fee_category_name.to_s+"</i> #{t('has_been_published')} </b>
                  \n \n  #{t('start_date')} : "+@finance_fee_collection.start_date.to_s+" \n"+
                  " #{t('end_date')} :"+@finance_fee_collection.end_date.to_s+" \n "+
                  " #{t('due_date')} :"+@finance_fee_collection.due_date.to_s+" \n \n \n "+
                  " #{t('check_your')}  #{t('fee_structure')}"


                recipient_ids = []

                unless @for_admission
                  @students.each do |s|

                    unless s.has_paid_fees
                      due = 0.0
                      if @auto_adjust_advance
                        amount_to_pay = 0.0
                        amount_paid = 0.0
                        student_fee_ledgers = StudentFeeLedger.find(:all, :select => "sum( amount_to_pay ) as amount_to_pay,  sum( amount_paid ) as  amount_paid ", :conditions => "student_id = #{s.id}")
                        unless student_fee_ledgers.blank?
                            student_fee_ledgers.each do |student_fee_ledger|
                              amount_to_pay += student_fee_ledger.amount_to_pay
                              amount_paid += student_fee_ledger.amount_paid
                            end
                        end
                        due = amount_to_pay - amount_paid
                        advance_amount_paid_listed = 0.0
                        advance = 0.0
                      end
                      #if s.id == 25438
                      #  abort(due.inspect)
                      #end

                      FinanceFee.new_student_fee(@finance_fee_collection,s)
                      date = FinanceFeeCollection.find(@finance_fee_collection.id)
                      finance_fee = s.finance_fee_by_date(@finance_fee_collection)

                      if due < 0 and @auto_adjust_advance
                        advance = due * -1

                        exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(s.id,date.id).map(&:fee_particular_id)
                        unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
                          exclude_particular_ids = exclude_particular_ids
                        else
                          exclude_particular_ids = [0]
                        end

                        transaction = FinanceTransaction.new
                        transaction.title = "#{t('receipt_no')}. F#{finance_fee.id}"
                        transaction.category = FinanceTransactionCategory.find_by_name("Fee")
                        transaction.payee = s
                        transaction.finance = finance_fee
                        transaction.amount = advance
                        transaction.fine_included = false
                        transaction.fine_amount = 0.00
                        transaction.transaction_date = Date.today
                        transaction.payment_mode = "Advance Adjustment"
                        transaction.save

                        @transaction_ids = FinanceTransaction.find(:all, :conditions => ["payee_id = '#{s.id}'"]).map(&:id)
                        #if s.id == 25419
                        #  abort(@transaction_ids.inspect)
                        #end
                        unless  @transaction_ids.blank?
                          paid_advances = FinanceTransactionParticular.find(:all, :conditions => "particular_id = 0 and particular_type = 'Particular' AND transaction_type = 'Advance' AND finance_transaction_id IN (" + @transaction_ids.join(",") + ")")
                          unless paid_advances.blank?
                            advance_amount_paid = 0.0
                            paid_advances.each do |paid_advance|
                              advance_amount_paid += paid_advance.amount
                            end
                            if advance_amount_paid > advance
                              advance_amount_paid = advance
                            end
                            advance_amount_paid_listed += advance_amount_paid

                            particular_category_id = 0
                            if MultiSchool.current_school.id == 352
                              particular_category_id = 54
                            else
                              @finance_fee_category = FinanceFeeParticularCategory.find(:first,:conditions => ["is_deleted = ? and (name = 'Tuition Fees' or name = 'Tuition Fee')", false])
                              unless @finance_fee_category.blank?
                                particular_category_id = @finance_fee_category.id
                              end
                            end
                            unless particular_category_id == 0
                              fee_particulars = date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.finance_fee_particular_category_id = '#{particular_category_id}' and finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_tmp=#{false} and is_deleted=#{false} and batch_id=#{s.batch_id}").select{|par| (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
                              unless fee_particulars.blank?
                                fee_particular = fee_particulars[0]
                                fee_transaction_particular_id = fee_particular.id
                              end
                              finance_transaction_particular = FinanceTransactionParticular.new
                              finance_transaction_particular.finance_transaction_id = transaction.id
                              finance_transaction_particular.particular_id = fee_transaction_particular_id
                              finance_transaction_particular.particular_type = 'Particular'
                              finance_transaction_particular.transaction_type = 'Advance'
                              finance_transaction_particular.amount = advance_amount_paid
                              finance_transaction_particular.transaction_date = transaction.transaction_date
                              finance_transaction_particular.save
                            else
                              finance_transaction_particular = FinanceTransactionParticular.new
                              finance_transaction_particular.finance_transaction_id = transaction.id
                              finance_transaction_particular.particular_id = 0
                              finance_transaction_particular.particular_type = 'Particular'
                              finance_transaction_particular.transaction_type = 'Advance'
                              finance_transaction_particular.amount = advance_amount_paid
                              finance_transaction_particular.transaction_date = transaction.transaction_date
                              finance_transaction_particular.save
                            end
                          end

                          remaining_amount = advance - advance_amount_paid_listed
                          
                          if remaining_amount > 0
                            paid_advances = FinanceTransactionParticular.find(:all, :conditions => "particular_id > 0 and particular_type = 'Particular' AND transaction_type = 'Advance' AND finance_transaction_id IN (" + @transaction_ids.join(",") + ")")
                           
                            unless paid_advances.blank?
                              paid_advances.each do |paid_advance|
                                particular_id = paid_advance.particular_id
                                
                                particular = FinanceFeeParticular.find(particular_id)
                                 
                                unless particular.blank?
                                  particular_category_id = particular.finance_fee_particular_category_id
                                 
                                  fee_particulars = date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.finance_fee_particular_category_id = '#{particular_category_id}' and finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_tmp=#{false} and is_deleted=#{false} and batch_id=#{s.batch_id}").select{|par| (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
                        
                                  unless fee_particulars.blank?
                                    fee_particular = fee_particulars[0]
                                    paid_particular = FinanceTransactionParticular.find(:first, :conditions => "particular_id = #{fee_particular.id} and particular_type = 'Particular' AND transaction_type = 'Fee Collection' AND finance_transaction_id = #{transaction.id}")
                                    
                                    unless paid_particular.blank?
                                      advance_amount_paid_listed += paid_advance.amount
                                      amt = paid_particular.amount
                                      amt += paid_advance.amount
                                      if amt > advance
                                        amt = advance
                                      end
                                      paid_particular.update_attributes(:amount=>amt)
                                    else
                                      
                                      adv_amount = paid_advance.amount
                                      if adv_amount > fee_particular.amount
                                        adv_amount = fee_particular.amount
                                      end
                                      if adv_amount > advance
                                        adv_amount = advance
                                      end
                                      finance_transaction_particular = FinanceTransactionParticular.new
                                      finance_transaction_particular.finance_transaction_id = transaction.id
                                      finance_transaction_particular.particular_id = fee_particular.id
                                      finance_transaction_particular.particular_type = 'Particular'
                                      finance_transaction_particular.transaction_type = 'Fee Collection'
                                      finance_transaction_particular.amount = adv_amount
                                      finance_transaction_particular.transaction_date = transaction.transaction_date
                                      finance_transaction_particular.save
                                      advance_amount_paid_listed += adv_amount
                                    end
                                  end
                                end
                              end
                            end
                          end
                        end

                        remaining_amount = advance - advance_amount_paid_listed
                        if remaining_amount > 0
                          particular_category_id = 0
                          if MultiSchool.current_school.id == 352
                            particular_category_id = 54
                          else
                            @finance_fee_category = FinanceFeeParticularCategory.find(:first,:conditions => ["is_deleted = ? and (name = 'Tuition Fees' or name = 'Tuition Fee')", false])
                            unless @finance_fee_category.blank?
                              particular_category_id = @finance_fee_category.id
                            end
                          end
                          unless particular_category_id == 0
                            fee_particulars = date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.finance_fee_particular_category_id = '#{particular_category_id}' and finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_tmp=#{false} and is_deleted=#{false} and batch_id=#{s.batch_id}").select{|par| (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
                            unless fee_particulars.blank?
                              fee_particular = fee_particulars[0]
                              paid_particular = FinanceTransactionParticular.first(:conditions => "particular_id = #{fee_particular.id} and particular_type = 'Particular' AND transaction_type = 'Fee Collection' AND finance_transaction_id = #{transaction.id}")
                              unless paid_particular.blank?
                                amt = paid_particular.amount
                               
                                amt += remaining_amount
                                if amt > fee_particular.amount
                                  remaining_amount = amt - fee_particular.amount
                                  amt = fee_particular.amount
                                end
                                paid_particular.update_attributes(:amount=>amt)
                                
                                if remaining_amount > 0
                                  finance_transaction_particular = FinanceTransactionParticular.new
                                  finance_transaction_particular.finance_transaction_id = transaction.id
                                  finance_transaction_particular.particular_id = fee_particular.id
                                  finance_transaction_particular.particular_type = 'Particular'
                                  finance_transaction_particular.transaction_type = 'Advance'
                                  finance_transaction_particular.amount = remaining_amount
                                  finance_transaction_particular.transaction_date = transaction.transaction_date
                                  finance_transaction_particular.save
                                end
                              else
                                amt = remaining_amount
                               
                                if amt > fee_particular.amount
                                  remaining_amount = amt - fee_particular.amount
                                  amt = fee_particular.amount
                                end
                                finance_transaction_particular = FinanceTransactionParticular.new
                                finance_transaction_particular.finance_transaction_id = transaction.id
                                finance_transaction_particular.particular_id = fee_particular.id
                                finance_transaction_particular.particular_type = 'Particular'
                                finance_transaction_particular.transaction_type = 'Fee Collection'
                                finance_transaction_particular.amount = amt
                                finance_transaction_particular.transaction_date = transaction.transaction_date
                                finance_transaction_particular.save
                                
                                if remaining_amount > 0
                                  finance_transaction_particular = FinanceTransactionParticular.new
                                  finance_transaction_particular.finance_transaction_id = transaction.id
                                  finance_transaction_particular.particular_id = fee_particular.id
                                  finance_transaction_particular.particular_type = 'Particular'
                                  finance_transaction_particular.transaction_type = 'Advance'
                                  finance_transaction_particular.amount = remaining_amount
                                  finance_transaction_particular.transaction_date = transaction.transaction_date
                                  finance_transaction_particular.save
                                end
                              end
                            end
                          end
                        end

                        bal = finance_fee.balance
                        bal = bal - transaction.amount
                        if bal < 0
                          bal = 0
                          finance_fee.update_attributes( :is_paid=>true, :balance => 0.0)
                        else
                          finance_fee.update_attributes(:balance => bal)
                        end
                      end

                      recipient_ids << s.user.id if s.user
                      recipient_ids << s.immediate_contact.user_id if s.immediate_contact.present?
                    end
                  end
                end

                unless @for_admission
                  recipient_ids = recipient_ids.compact
                  BatchEvent.create(:event_id => new_event.id, :batch_id => b )
                  if  @sent_remainder
                    Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => @user.id,
                        :recipient_ids => recipient_ids,
                        :subject=>subject,
                        :body=>body ))
                  end
                end
                #abort('here')
                prev_record = Configuration.find_by_config_key("job/FinanceFeeCollection/1")
                if prev_record.present?
                  prev_record.update_attributes(:config_value=>Time.now)
                else
                  Configuration.create(:config_key=>"job/FinanceFeeCollection/1", :config_value=>Time.now)
                end
              end
            else
              @error = true
              new_finance_fee_category.destroy
              raise ActiveRecord::Rollback
            end

          end
        end
      end
      
      #Delayed::Job.enqueue(DelayedFeeCollectionJob.new(@user,params[:finance_fee_collection],params[:fee_collection], sent_remainder, particular_ids, particular_names, transport_particular_id, transport_particular_name, default_particular_names, default_transport_particular_name, auto_adjust_advance, for_admission))
      
      flash[:notice]="#{t('collection_is_in_queue')}" + " <a href='/scheduled_jobs/FinanceFeeCollection/1'>" + "#{t('cick_here_to_view_the_scheduled_job')}"
      #flash[:notice] = t('flash_msg33')

    end
    redirect_to :action => 'fee_collection_new'
  end
  
  def fee_collection_advance_create
    unless params[:fee_collection_advance].nil? or params[:fee_collection_advance].empty? or params[:fee_collection_advance].blank?
      unless params[:fee_collection][:students].nil? or params[:fee_collection][:students].empty? or params[:fee_collection][:students].blank?
        start_date = Date.parse(params[:finance_fee_collection][:start_date])
        begining_of_month = start_date.beginning_of_month
        end_of_month = start_date.end_of_month
        
        advance_month = params[:fee_collection_advance][:start_month]
        a_advance_month = advance_month.split("_")
        mon_yr_s = a_advance_month[1]
        a_mon_yr_s = mon_yr_s.split(",")
        s_date = Date.parse(a_mon_yr_s[1].strip.to_s + "-" + a_mon_yr_s[0].strip.to_s + "-01")
        
        advance_month = params[:fee_collection_advance][:end_month]
        a_advance_month = advance_month.split("_")
        mon_yr_s = a_advance_month[1]
        a_mon_yr_s = mon_yr_s.split(",")
        e_date = Date.parse(a_mon_yr_s[1].strip.to_s + "-" + a_mon_yr_s[0].strip.to_s + "-01")
        end_month_data = e_date.end_of_month
        
        fee_category_id = params[:fee_collection_advance][:finance_fee_category_id]	
       
        regenerate = 0
        unless params[:regenerate].nil? or params[:regenerate].empty? or params[:regenerate].blank?
          regenerate = params[:regenerate].to_i
        end
        student_ids = params[:fee_collection][:students]
        error = false
        message = ""
        fee_ids = []
        enable_fee = []
        
        s_particular = params[:fee_collection_advance][:particular]	
        a_particular = s_particular.split("-")
        particular_id = a_particular[0]
        
        fee_collect_advance = FinanceFeeCollection.find(:all, :conditions => "is_advance_fee_collection = #{true} and start_date >= '#{s_date.strftime("%Y-%m-%d")}' and start_date <= '#{end_month_data.strftime("%Y-%m-%d")}' and fee_category_id = '#{fee_category_id}'")
        unless fee_collect_advance.nil? or fee_collect_advance.empty? or fee_collect_advance.blank?
          student_ids_str = student_ids.join(",")
          fee_collect_advance.each do |f|
            fees = FinanceFee.find(:all, :conditions=>"fee_collection_id = #{f.id} AND finance_fees.student_id IN (#{student_ids_str})")
            unless fees.nil? or fees.empty? or fees.blank?
              fadv = FinanceFeeAdvance.find_by_fee_collection_id(f.id)
              unless fadv.nil? or fadv.blank?
                if particular_id.to_i == 0
                  error = true
                  message = "one or more students has already been assigned for Advance payment, Please correct the error and try again"
                else
                  if fadv.particular_id.to_i == particular_id.to_i
                    error = true
                    message = "one or more students has already been assigned for Advance payment, Please correct the error and try again"
                  end
                end
              end
            end
          end
        end
        
        unless error 
          fee_collect = FinanceFeeCollection.find(:all, :conditions => "is_advance_fee_collection = #{false} and start_date >= '#{s_date.strftime("%Y-%m-%d")}' and start_date <= '#{end_month_data.strftime("%Y-%m-%d")}' and fee_category_id = '#{fee_category_id}'")

          unless fee_collect.nil? or fee_collect.empty? or fee_collect.blank?
            i = 0
            fee_collect.each do |f|
              student_ids.each do |si|
                fee = FinanceFee.first(:conditions=>"fee_collection_id = #{f.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{si}'")
                unless fee.nil?
                  enable_fee[i] = true
                  fee_ids[i] = f.id
                  paid_fees = fee.finance_transactions
                  unless paid_fees.blank?
                    enable_fee[i] = false
                    error = true
                    message = "Student already has paid fees for this Date range.\n\n Please remove the transaction Manually before creating Advance Fee Collection"
                  else
                    if regenerate == 0
                      enable_fee[i] = false
                      error = true
                      message = "Student already has paid fees for this Date range.\n\n Please remove the transaction Manually before creating Advance Fee Collection"
                    end
                  end
                end
              end
              i += 1
            end
          end
        end
        
        if error
          flash[:notice] = message
        else
          e_mon = end_month_data.year * 12 + end_month_data.month
          s_mon = s_date.year * 12 + s_date.month
          mon_diff = (e_mon - s_mon) + 1
          
          unless params[:sent_remainder].nil?
            sent_remainder = true
          end

          @user = current_user
          @fee_categories=FinanceFeeCategory.find(:all,:joins=>"INNER JOIN finance_fee_particulars on finance_fee_particulars.finance_fee_category_id=finance_fee_categories.id AND finance_fee_particulars.is_tmp = 0 AND finance_fee_particulars.is_deleted = 0 INNER JOIN batches on batches.id=finance_fee_particulars.batch_id AND batches.is_active = 1 AND batches.is_deleted = 0 AND finance_fee_categories.is_deleted=0",:group=>'finance_fee_categories.name')
          unless params[:fee_collection_advance].nil?
            fee_category_name = params[:fee_collection_advance][:finance_fee_category_id]
            @fee_category = FinanceFeeCategory.find_all_by_id(fee_category_name, :conditions=>['is_deleted is false'])
          end
          category =[]
          
          batch_id = params[:fee_collection_advance][:batch_id]
          
          @finance_fee_collection = FinanceFeeCollection.new
          if request.post?
            @collection = params[:finance_fee_collection]
            @finance_fee_collection = FinanceFeeCollection.new(
              :name                       => @collection[:name],
              :start_date                 => @collection[:start_date],
              :end_date                   => @collection[:end_date],
              :due_date                   => @collection[:due_date],
              :fee_category_id            => params[:fee_collection_advance][:finance_fee_category_id],
              :include_transport          => false,
              :fine_id                    => @collection[:fine_id],
              :is_advance_fee_collection  => true
            )
            
            FinanceFeeCollection.transaction do
              if @finance_fee_collection.save
                new_event =  Event.create(:title=> "Fees Due", :description =>@collection[:name], :start_date => @finance_fee_collection.due_date.to_datetime, :end_date => @finance_fee_collection.due_date.to_datetime, :is_due => true , :origin=>@finance_fee_collection)
                
                recipient_ids = []
                
                FeeCollectionBatch.create(:finance_fee_collection_id=>@finance_fee_collection.id,:batch_id=>batch_id)
                student_ids.each do |si|
                  std = Student.find(si)
                  
                  finance_fee_advance = FinanceFeeAdvance.new
                  finance_fee_advance.fee_collection_id = @finance_fee_collection.id
                  finance_fee_advance.student_id = std.id
                  finance_fee_advance.batch_id = batch_id
                  finance_fee_advance.no_of_month = mon_diff
                  finance_fee_advance.start_month = s_date.strftime("%B, %Y")
                  finance_fee_advance.end_month = end_month_data.strftime("%B, %Y")
                  finance_fee_advance.start_date = s_date.strftime("%Y-%m-%d")
                  finance_fee_advance.end_date = end_month_data.strftime("%Y-%m-%d")
                  finance_fee_advance.particular_id = particular_id
                  
                  if finance_fee_advance.save
                    
                    FinanceFee.new_student_fee_advance(@finance_fee_collection,std,mon_diff,particular_id, finance_fee_advance.id)
                    j = 0
                    fee_ids.each do |fid|
                      if enable_fee[j]
                        fee = FinanceFee.first(:conditions=>"fee_collection_id = #{fid}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{std.id}'")
                        unless fee.nil?
                          balance = 0
                          @total_payable = 0
                          if particular_id.to_i != 0
                            date_id = fee.fee_collection_id
                            date_pre =  FinanceFeeCollection.find(date_id)
                            
                            fee_advs = []
                            fee_advs = FeesAdvance.find(:all, :conditions => "fee_id = #{fee.id}").map(&:advance_fee_id)
                            unless fee_advs.blank?
                              unless fee_advs.include?(finance_fee_advance.id)
                                fee_advs.push(finance_fee_advance.id)
                              end
                            else
                              fee_advs.push(finance_fee_advance.id)
                            end
                            
                            fee_advs = fee_advs.reject { |f| f.to_s.empty? }
                            if fee_advs.blank?
                              fee_advs[0] = 0
                            end
                            
                            finance_fee_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{fee_advs.join(",")})")  
                            
                            adv_fee_particulars = []
                            adv_fee_particulars = finance_fee_advances.map(&:particular_id)
                            unless adv_fee_particulars.blank?
                              unless adv_fee_particulars.include?(finance_fee_advance.particular_id)
                                adv_fee_particulars.push(finance_fee_advance.particular_id)
                              end
                            else
                              adv_fee_particulars.push(finance_fee_advance.particular_id)
                            end
                            
                            unless adv_fee_particulars.include?(0)
                              adv_fee_particulars = adv_fee_particulars.reject { |f| f.to_s.empty? }
                              if adv_fee_particulars.blank?
                                adv_fee_particulars[0] = 0
                              end
                              fee_particulars = date_pre.finance_fee_particulars.all(:conditions=>"finance_fee_particular_category_id NOT IN (#{adv_fee_particulars.join(",")}) and is_tmp=#{false} and is_deleted=#{false} and batch_id=#{std.batch_id}").select{|par| (par.receiver.present?) and (par.receiver==std or par.receiver==std.student_category or par.receiver==std.batch) }
                              @total_payable = fee_particulars.map{|fp| fp.amount}.sum.to_f
                              unless @total_payable == 0
                                @total_discount = 0

                                calculate_discount(date_pre, fee.batch, std, false, finance_fee_advances, false)
                                balance=(@total_payable-@total_discount).to_f
                              end
                            end
                          end
                          
                          fee_id = fee.id
                          feetable = FinanceFee.find(fee_id)
                          feetable.update_attributes(:balance => balance,:has_advance_fee_id=> true)
                          
                          fee_advance = FeesAdvance.new
                          fee_advance.advance_fee_id = finance_fee_advance.id
                          fee_advance.fee_id = fee_id
                          fee_advance.save
                        end
                      end
                      j += 1
                    end
                  end
                  recipient_ids << std.user.id if std.user
                  recipient_ids << std.immediate_contact.user_id if std.immediate_contact.present?
                end
                
                recipient_ids = recipient_ids.compact
                BatchEvent.create(:event_id => new_event.id, :batch_id => batch_id )
                
                subject = "#{t('fees_submission_date')}"
                body = "<p><b>#{t('fee_submission_date_for')} <i>"+fee_category_name+"</i> #{t('has_been_published')} </b>
                        \n \n  #{t('start_date')} : "+@finance_fee_collection.start_date.to_s+" \n"+
                  " #{t('end_date')} :"+@finance_fee_collection.end_date.to_s+" \n "+
                  " #{t('due_date')} :"+@finance_fee_collection.due_date.to_s+" \n \n \n "+
                  " #{t('check_your')}  #{t('fee_structure')}"
                      
                if  sent_remainder
                  Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => @user.id,
                      :recipient_ids => recipient_ids,
                      :subject=>subject,
                      :body=>body ))
                end
              else
                @error = true
                raise ActiveRecord::Rollback
              end
            end
            flash[:notice] = t('flash_msg33')

          end
        end
      else
        flash[:notice]="No Student Select for Advance Payment"
      end
    else
      flash[:notice]="Please correct your info before submit"
    end
    redirect_to :action => 'advance_fee_collection'
  end

  def fee_collection_view
    @batchs = Batch.active
  end
  
  def advance_fee_collection_view
    @finance_fee_collections = FinanceFeeCollection.find(:all, :conditions => "is_advance_fee_collection = #{true}")
  end
  
  def fee_collections
    @finance_fee_collections = FinanceFeeCollection.find(:all, :conditions => "is_advance_fee_collection = #{false} and for_admission = #{true} and finance_fee_collections.is_deleted = '#{false}'", :group => "name")
  end
  
  def advance_fee_collection
    @config = Configuration.get_multiple_configs_as_hash ['SessionStartMonth','SessionEndMonth']
    @fines=Fine.active
    @fee_categories=FinanceFeeCategory.find(:all,:joins=>"INNER JOIN finance_fee_particulars on finance_fee_particulars.finance_fee_category_id=finance_fee_categories.id AND finance_fee_particulars.is_tmp = 0 AND finance_fee_particulars.is_deleted = 0 INNER JOIN batches on batches.id=finance_fee_particulars.batch_id AND batches.is_active = 1 AND batches.is_deleted = 0 AND finance_fee_categories.is_deleted=0",:group=>'concat(finance_fee_categories.name,finance_fee_categories.description)')
    @finance_fee_collection = FinanceFeeCollection.new
  end

  def fee_collection_dates_batch
    if params[:id].present?
      @batch= Batch.find(params[:id])
      @finance_fee_collections = @batch.finance_fee_collections.find(:all, :conditions => "is_advance_fee_collection = #{false} and for_admission = #{false}")
      render :update do |page|
        page.replace_html 'fee_collection_dates', :partial => 'fee_collection_dates_batch'
      end
    else
      render :update do |page|
        page.replace_html 'fee_collection_dates', :text => ''
      end
    end
  end
  
  def sent_notification
    if params[:batch_id].present?
      @batch=Batch.find(params[:batch_id])
      @finance_fee_collections = @batch.finance_fee_collections
      
      @user = current_user
      
      Delayed::Job.enqueue(DelayedFeeCollectionNotificationJob.new(@user, params[:id], params[:batch_id],true))
      flash[:notice]="Notification will sent for selected Fee collection"
      
      render :update do |page|
        page.replace_html 'fee_collection_dates', :partial => 'fee_collection_dates_batch'
      end
    else
      render :update do |page|
        page.replace_html 'fee_collection_dates', :text => ''
      end
    end
  end
  
  def sent_notification_all
    if params[:id].present?
      @finance_fee_collection = FinanceFeeCollection.find(params[:id])
      @batches = @finance_fee_collection.fee_collection_batches.map(&:batch_id)
      
      @finance_fee_collections = FinanceFeeCollection.find(:all, :conditions => "is_advance_fee_collection = #{false} and finance_fee_collections.is_deleted = '#{false}'")
      
      @user = current_user
      
      Delayed::Job.enqueue(DelayedFeeCollectionNotificationJob.new(@user, params[:id], @batches, false))
      flash[:notice]="Notification will sent for selected Fee collection"
      
      render :update do |page|
        page.replace_html 'fee_collection_dates', :partial => 'fee_collections_view'
      end
    else
      render :update do |page|
        page.replace_html 'fee_collection_dates', :text => ''
      end
    end
  end

  def fee_collection_edit
    @finance_fee_collection = FinanceFeeCollection.find params[:id]
    @batch=Batch.find(params[:batch_id])
  end
  
  def fee_collection_edit_all
    @finance_fee_collection = FinanceFeeCollection.find params[:id]
  end
  
  def fee_collection_assign_discount
    @fee_collection_id = params[:id]
    @fee_collection = FinanceFeeCollection.find(:first, :conditions => "id = #{@fee_collection_id}")
    unless @fee_collection.blank?
      @discounts = FeeDiscount.find_all_by_batch_id_and_is_onetime_and_finance_fee_category_id_and_is_deleted_and_is_visible(params[:batch_id], true, @fee_collection.fee_category_id, false, true )
      @batch_id = params[:batch_id]
      @fee_collection_discount = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id(@fee_collection_id, params[:batch_id]).map(&:fee_discount_id)
    end
  end
  
  def fee_collection_assign_discount_student
    @student = Student.find(params[:student_id])
    @fee_collection_id = params[:id]
    @fee_collection = FinanceFeeCollection.find(:first, :conditions => "id = #{@fee_collection_id}")
    unless @fee_collection.blank?
      @discounts = FeeDiscount.find_all_by_batch_id_and_is_onetime_and_receiver_type_and_receiver_id_and_finance_fee_category_id_and_is_deleted_and_is_visible(params[:batch_id], true, 'Student', params[:student_id], @fee_collection.fee_category_id, false, true )
      @batch_id = params[:batch_id]
      @fee_collection_discount = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id(@fee_collection_id, params[:batch_id]).map(&:fee_discount_id)
    end
  end
  
  def assign_fee_discount_to_collection_student
    unless params[:fee_collection_id].nil?
      @discount_id = params[:id]
      @fee_collection_id = params[:fee_collection_id]
      
      discount = FeeDiscount.find(@discount_id)
      
      finance_fee_category_id = discount.finance_fee_category_id
      finance_fee_particular_category_id = discount.finance_fee_particular_category_id
      is_late = discount.is_late
      batch_id = discount.batch_id
      @batch_id = params[:batch_id]
      @receiver_id = discount.receiver_id
      @type_discount = params[:type_discount]
      unless params[:type_discount].nil?
        if params[:type_discount] == "student"
          receiver_id = discount.receiver_id
          if receiver_id.to_i == params[:receiver_id].to_i
            if is_late
              f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{@fee_collection_id} and fee_discount_id = #{@discount_id} and batch_id = #{@batch_id} and is_late = 1")
              if f.blank?
                fee_discount_collection = FeeDiscountCollection.new(
                  :finance_fee_collection_id => @fee_collection_id,
                  :fee_discount_id           => @discount_id,
                  :batch_id                  => @batch_id,
                  :is_late                   => 1
                )

                if fee_discount_collection.save
                  @batch   = Batch.find(@batch_id)
                  @date    =  @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
                  student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
                  
                  @from_batch_fee = true
                  unless params[:student_fees].nil?
                    if params[:student_fees].to_i == 1
                      @from_batch_fee = false
                    end
                  end

                  @student = Student.find(params[:receiver_id])
                  @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{@student.id}'")

                  unless @fee.nil?
                    get_fees_details(student_ids)
                    
                    render :update do |page|
                      page.replace_html "student", :partial => "student_fees_submission"
                      page << "loadJS();"
                      page << 'j(".select2-combo").select2();'
                      page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign_student'
                    end
                  else
                    render :update do |page|
                      page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign_student'
                    end
                  end
                end
              else
                @batch   = Batch.find(@batch_id)
                @date    =  @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
                student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')

                @from_batch_fee = true
                unless params[:student_fees].nil?
                  if params[:student_fees].to_i == 1
                    @from_batch_fee = false
                  end
                end

                @student = Student.find(params[:receiver_id])
                @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{@student.id}'")

                unless @fee.nil?
                  get_fees_details(student_ids)
                  
                  render :update do |page|
                    page.replace_html "student", :partial => "student_fees_submission"
                    page << "loadJS();"
                    page << 'j(".select2-combo").select2();'
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign_student'
                  end
                else
                  render :update do |page|
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign_student'
                  end
                end
              end
            else
              f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{@fee_collection_id} and fee_discount_id = #{@discount_id} and batch_id = #{@batch_id} and is_late = 0")
              if f.blank?
                @fee_collection = FinanceFeeCollection.find(@fee_collection_id)

                collection_discount = CollectionDiscount.new(:fee_discount_id=>discount.id,:finance_fee_collection_id=>@fee_collection_id, :finance_fee_particular_category_id => discount.finance_fee_particular_category_id)
                collection_discount.save
                
                found = false
                error_text = ""
                if discount.finance_fee_particular_category_id == 0
                  @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@fee_collection_id} and is_paid=#{false} and students.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")

                  s = @fee.student

                  found = false
                  unless s.has_paid_fees
                    bal = FinanceFee.check_update_student_fee(@fee_collection, s, @fee)
                    if bal >= 0
                      found = true
                      FinanceFee.update_student_fee(@fee_collection, s, @fee)
                      
                      exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(s.id,@fee_collection.id).map(&:fee_particular_id)
                      unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
                        exclude_particular_ids = exclude_particular_ids
                      else
                        exclude_particular_ids = [0]
                      end
                      if discount.finance_fee_particular_category_id == 0
                        fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                      else
                        fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id} and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                      end
                      payable_ampt = fee_particulars.map{|p| p.amount}.sum.to_f
                      discount_amt = payable_ampt * discount.discount.to_f/ (discount.is_amount?? payable_ampt : 100)

                      student_fee_ledger = StudentFeeLedger.new
                      student_fee_ledger.student_id = s.id
                      student_fee_ledger.ledger_date = Date.today
                      student_fee_ledger.ledger_title = discount.name
                      student_fee_ledger.amount_to_pay = 0.0
                      student_fee_ledger.fee_id = @fee.id
                      student_fee_ledger.particular_id = discount.id
                      student_fee_ledger.amount_paid = discount_amt
                      student_fee_ledger.save
                    else
                      found = false
                      collection_discount.destroy
                    end
                  end
                  unless found 
                    error_text = "Student Discount can't be assign, discount amount is greater than Fee Amount"
                  end
                else
                  student = Student.find(:first, :conditions => "id = #{receiver_id.to_i}")
                  unless student.nil?
                    fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{student.batch_id} and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                    amount = fee_particulars.map(&:amount).sum
                    
                    discount_amt = amount * discount.discount.to_f/ (discount.is_amount?? amount : 100)
                    collect_discounts = CollectionDiscount.find(:all, :conditions => "finance_fee_collection_id = #{@fee_collection.id} and fee_discount_id != #{discount.id}")
                    fee_discount_ids = collect_discounts.map(&:fee_discount_id).uniq
                    
                    unless fee_discount_ids.blank?
                      fee_discount_ids = fee_discount_ids.reject { |f| f.to_s.empty? }
                      if fee_discount_ids.blank?
                        fee_discount_ids[0] = 0
                      end
                      fee_discounts = FeeDiscount.find(:all, :conditions => "id IN (#{fee_discount_ids.join(",")}) and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                      fee_discounts.each do |f_discount|
                        is_discount_excluded = false
                        excluded_discounts = StudentExcludeDiscount.find(:all, :conditions => "fee_collection_id = #{@fee_collection_id} and student_id = #{student.id} and fee_discount_id = #{f_discount.id}")
                        unless excluded_discounts.blank?
                          is_discount_excluded = true
                        end
                        unless is_discount_excluded
                          discount_amt += amount * f_discount.discount.to_f/ (f_discount.is_amount?? amount : 100)
                        end
                      end
                    end
                    remaining_amount = amount - discount_amt
                    if remaining_amount >= 0
                      found = true
                      @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@fee_collection_id} and is_paid=#{false} and students.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")
                      s = student
                      FinanceFee.update_student_fee(@fee_collection, student, @fee)
                      
                      exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(s.id,@fee_collection.id).map(&:fee_particular_id)
                      unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
                        exclude_particular_ids = exclude_particular_ids
                      else
                        exclude_particular_ids = [0]
                      end
                      if discount.finance_fee_particular_category_id == 0
                        fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                      else
                        fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id} and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                      end
                      payable_ampt = fee_particulars.map{|p| p.amount}.sum.to_f
                      discount_amt = payable_ampt * discount.discount.to_f/ (discount.is_amount?? payable_ampt : 100)

                      student_fee_ledger = StudentFeeLedger.new
                      student_fee_ledger.student_id = s.id
                      student_fee_ledger.ledger_date = Date.today
                      student_fee_ledger.ledger_title = discount.name
                      student_fee_ledger.amount_to_pay = 0.0
                      student_fee_ledger.fee_id = @fee.id
                      student_fee_ledger.particular_id = discount.id
                      student_fee_ledger.amount_paid = discount_amt
                      student_fee_ledger.save
                    else
                      found = false
                      collection_discount.destroy
                    end
                  else
                    found = false
                  end
                  unless found 
                    error_text = "Student Discount can't be assign, discount amount is greater than Particular Amount"
                  end
                end
                
                if found
                  fee_discount_collection = FeeDiscountCollection.new(
                    :finance_fee_collection_id => @fee_collection_id,
                    :fee_discount_id           => @discount_id,
                    :batch_id                  => @batch_id,
                    :is_late                   => 0
                  )
                  fee_discount_collection.save
                  
                  @batch   = Batch.find(@batch_id)
                  @date    =  @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
                  student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')

                  @from_batch_fee = true
                  unless params[:student_fees].nil?
                    if params[:student_fees].to_i == 1
                      @from_batch_fee = false
                    end
                  end

                  @student = Student.find(params[:receiver_id])
                  @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{@student.id}'")

                  unless @fee.nil?
                    get_fees_details(student_ids)
                    
                    render :update do |page|
                      page.replace_html "student", :partial => "student_fees_submission"
                      page << "loadJS();"
                      page << 'j(".select2-combo").select2();'
                      page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign_student'
                    end
                  else
                    render :update do |page|
                      page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign_student'
                    end
                  end
                else
                  collection_discount.destroy
                  
                  render :update do |page|
                    page.replace_html 'student_discount_error', :text => error_text
                    page << "$('student_discount_div').show();"
                    page << "setTimeout(function(){$('student_discount_div').hide();}, 3000)"
                    page << "j('#assign_student_#{@discount_id.to_s}').attr('href','#');"
                    page << "j('#assign_student_#{@discount_id.to_s}').attr('onclick',j('#assign_student_#{@discount_id}').data('onclick'));"
                    page << "j('#assign_student_#{@discount_id.to_s}').removeAttr('data-onclick');"
                  end
                end
                
              else
                @batch   = Batch.find(@batch_id)
                @date    =  @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
                student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
                
                @from_batch_fee = true
                unless params[:student_fees].nil?
                  if params[:student_fees].to_i == 1
                    @from_batch_fee = false
                  end
                end
                
                @student = Student.find(params[:receiver_id])
                @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{@student.id}'")
                
                unless @fee.nil?
                  get_fees_details(student_ids)
                  
                  render :update do |page|
                    page.replace_html "student", :partial => "student_fees_submission"
                    page << "loadJS();"
                    page << 'j(".select2-combo").select2();'
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign_student'
                  end
                else
                  render :update do |page|
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign_student'
                  end
                end
              end
            end
          end
        end
      end
    else
      render :text => ''
    end
  end

  def assign_fee_discount_to_collection
    unless params[:fee_collection_id].nil?
      @discount_id = params[:id]
      @fee_collection_id = params[:fee_collection_id]
      
      discount = FeeDiscount.find(@discount_id)
      
      finance_fee_category_id = discount.finance_fee_category_id
      finance_fee_particular_category_id = discount.finance_fee_particular_category_id
      is_late = discount.is_late
      batch_id = discount.batch_id
      @batch_id = params[:batch_id]
      @receiver_id = discount.receiver_id
      @type_discount = params[:type_discount]
      unless params[:type_discount].nil?
        if params[:type_discount] == "student"
          receiver_id = discount.receiver_id
          if receiver_id.to_i == params[:receiver_id].to_i
            if is_late
              f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{@fee_collection_id} and fee_discount_id = #{@discount_id} and batch_id = #{@batch_id} and is_late = 1")
              if f.blank?
                fee_discount_collection = FeeDiscountCollection.new(
                  :finance_fee_collection_id => @fee_collection_id,
                  :fee_discount_id           => @discount_id,
                  :batch_id                  => @batch_id,
                  :is_late                   => 1
                )

                if fee_discount_collection.save
                  render :update do |page|
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign'
                  end
                end
              else
                render :update do |page|
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign'
                end
              end
            else
              f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{@fee_collection_id} and fee_discount_id = #{@discount_id} and batch_id = #{@batch_id} and is_late = 0")
              if f.blank?
                @fee_collection = FinanceFeeCollection.find(@fee_collection_id)

                collection_discount = CollectionDiscount.new(:fee_discount_id=>discount.id,:finance_fee_collection_id=>@fee_collection_id, :finance_fee_particular_category_id => discount.finance_fee_particular_category_id)
                collection_discount.save
                
                found = false
                if discount.finance_fee_particular_category_id == 0
                  @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@fee_collection_id} and is_paid=#{false} and students.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")

                  s = @fee.student

                  unless s.has_paid_fees
                    bal = FinanceFee.check_update_student_fee(@fee_collection, s, @fee)
                    if bal >= 0
                      found = true
                      FinanceFee.update_student_fee(@fee_collection, s, @fee)
                      
                      exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(s.id,@fee_collection.id).map(&:fee_particular_id)
                      unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
                        exclude_particular_ids = exclude_particular_ids
                      else
                        exclude_particular_ids = [0]
                      end
                      if discount.finance_fee_particular_category_id == 0
                        fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                      else
                        fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id} and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                      end
                      payable_ampt = fee_particulars.map{|p| p.amount}.sum.to_f
                      discount_amt = payable_ampt * discount.discount.to_f/ (discount.is_amount?? payable_ampt : 100)

                      student_fee_ledger = StudentFeeLedger.new
                      student_fee_ledger.student_id = s.id
                      student_fee_ledger.ledger_date = Date.today
                      student_fee_ledger.ledger_title = discount.name
                      student_fee_ledger.amount_to_pay = 0.0
                      student_fee_ledger.fee_id = @fee.id
                      student_fee_ledger.particular_id = discount.id
                      student_fee_ledger.amount_paid = discount_amt
                      student_fee_ledger.save
                    else
                      found = true
                      collection_discount.destroy
                    end
                  end
                  unless found 
                    error_text = "Student Discount can't be assign, discount amount is greater than Fee amount"
                  end
                else
                  student = Student.find(:first, :conditions => "id = #{receiver_id.to_i}")
                  unless student.nil?
                    fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{student.batch_id} and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                    amount = fee_particulars.map(&:amount).sum
                    discount_amt = amount * discount.discount.to_f/ (discount.is_amount?? amount : 100)
                    collect_discounts = CollectionDiscount.find(:all, :conditions => "finance_fee_collection_id = #{@fee_collection.id} and fee_discount_id != #{discount.id}")
                    fee_discount_ids = collect_discounts.map(&:fee_discount_id).uniq
                    unless fee_discount_ids.blank?
                      fee_discount_ids = fee_discount_ids.reject { |f| f.to_s.empty? }
                      if fee_discount_ids.blank?
                        fee_discount_ids[0] = 0
                      end
                      fee_discounts = FeeDiscount.find(:all, :conditions => "id IN (#{fee_discount_ids.join(",")}) and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                      fee_discounts.each do |f_discount|
                        is_discount_excluded = false
                        excluded_discounts = StudentExcludeDiscount.find(:all, :conditions => "fee_collection_id = #{@fee_collection_id} and student_id = #{student.id} and fee_discount_id = #{f_discount.id}")
                        unless excluded_discounts.blank?
                          is_discount_excluded = true
                        end
                        unless is_discount_excluded
                          discount_amt += amount * f_discount.discount.to_f/ (f_discount.is_amount?? amount : 100)
                        end
                      end
                    end
                    remaining_amount = amount - discount_amt
                    if remaining_amount >= 0
                      found = true
                      @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@fee_collection_id} and is_paid=#{false} and students.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")
                      s = student
                      FinanceFee.update_student_fee(@fee_collection, student, @fee)
                      
                      exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(s.id,@fee_collection.id).map(&:fee_particular_id)
                      unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
                        exclude_particular_ids = exclude_particular_ids
                      else
                        exclude_particular_ids = [0]
                      end
                      if discount.finance_fee_particular_category_id == 0
                        fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                      else
                        fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id} and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                      end
                      payable_ampt = fee_particulars.map{|p| p.amount}.sum.to_f
                      discount_amt = payable_ampt * discount.discount.to_f/ (discount.is_amount?? payable_ampt : 100)

                      student_fee_ledger = StudentFeeLedger.new
                      student_fee_ledger.student_id = s.id
                      student_fee_ledger.ledger_date = Date.today
                      student_fee_ledger.ledger_title = discount.name
                      student_fee_ledger.amount_to_pay = 0.0
                      student_fee_ledger.fee_id = @fee.id
                      student_fee_ledger.particular_id = discount.id
                      student_fee_ledger.amount_paid = discount_amt
                      student_fee_ledger.save
                    else
                      found = false
                      collection_discount.destroy
                    end
                  else
                    found = false
                  end
                  unless found 
                    error_text = "Student Discount can't be assign, discount amount is greater than Particular Amount"
                  end
                end
                if found
                  fee_discount_collection = FeeDiscountCollection.new(
                    :finance_fee_collection_id => @fee_collection_id,
                    :fee_discount_id           => @discount_id,
                    :batch_id                  => @batch_id,
                    :is_late                   => 0
                  )
                  fee_discount_collection.save
                  render :update do |page|
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign'
                  end
                else
                  collection_discount.destroy
                  render :update do |page|
                    page.replace_html 'student_discount_error', :text => error_text
                    page << "$('student_discount_div').show();"
                    page << "setTimeout(function(){$('student_discount_div').hide();}, 3000)"
                    page << "j('#assign_student_#{@discount_id.to_s}').attr('href','#');"
                    page << "j('#assign_student_#{@discount_id.to_s}').attr('onclick',j('#assign_student_#{@discount_id}').data('onclick'));"
                    page << "j('#assign_student_#{@discount_id.to_s}').removeAttr('data-onclick');"
                  end
                end
                
              else
                render :update do |page|
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign'
                end
              end
            end
          end
        elsif params[:type_discount] == "categoy"
          receiver_id = discount.receiver_id
          if receiver_id.to_i == params[:receiver_id].to_i
            if is_late
              f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{@fee_collection_id} and fee_discount_id = #{@discount_id} and batch_id = #{@batch_id} and is_late = 1")
              if f.blank?
                fee_discount_collection = FeeDiscountCollection.new(
                  :finance_fee_collection_id => @fee_collection_id,
                  :fee_discount_id           => @discount_id,
                  :batch_id                  => @batch_id,
                  :is_late                   => 1
                )

                if fee_discount_collection.save
                  render :update do |page|
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign'
                  end
                end
              else
                render :update do |page|
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign'
                end
              end
            else
              f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{@fee_collection_id} and fee_discount_id = #{@discount_id} and batch_id = #{@batch_id} and is_late = 0")
              if f.blank?
                @fee_collection = FinanceFeeCollection.find(@fee_collection_id)

                collection_discount = CollectionDiscount.new(:fee_discount_id=>discount.id,:finance_fee_collection_id=>@fee_collection_id, :finance_fee_particular_category_id => discount.finance_fee_particular_category_id)
                collection_discount.save
                
                found = true
                std_ids = []
                total_fee_discount = false
                if discount.finance_fee_particular_category_id == 0
                  total_fee_discount = true
                  @fees = FinanceFee.find(:all, :conditions=>"fee_collection_id = #{@fee_collection_id} and students.batch_id = #{batch_id} and is_paid=#{false} and student_categories.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id INNER JOIN student_categories ON student_categories.id = students.student_category_id ")
                  k = 0
                  @fees.each do |f|
                    s = f.student
                    @fee = f
                    unless s.has_paid_fees
                      bal = FinanceFee.check_update_student_fee(@fee_collection, s, f)
                      if bal < 0
                        discount_amt = bal * discount.discount.to_f/ (discount.is_amount?? bal : 100)
                        collect_discounts = CollectionDiscount.find(:all, :conditions => "finance_fee_collection_id = #{@fee_collection.id} and fee_discount_id != #{discount.id}")
                        fee_discount_ids = collect_discounts.map(&:fee_discount_id)
                        unless fee_discount_ids.blank?
                          fee_discount_ids = fee_discount_ids.reject { |f| f.to_s.empty? }
                          if fee_discount_ids.blank?
                            fee_discount_ids[0] = 0
                          end
                          fee_discounts = FeeDiscount.find(:all, :conditions => "id IN (#{fee_discount_ids.join(",")}) and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
                          fee_discounts.each do |f_discount|
                            discount_amt += bal * f_discount.discount.to_f/ (f_discount.is_amount?? bal : 100)
                          end
                        end
                        
                        found = false
                        std_ids[k] = {:admission_no => s.admission_no, :full_name => s.full_name, :sid => s.id, :roll_no => s.class_roll_no, :balance => f.balance, :discount_amt => discount_amt}
                        k += 1
                      end
                    end
                  end
                  unless found 
                    error_text = "Some Student Discount can't be assign, discount amount is greater than Fee amount. <br /><a href='javascript:;' id='list_of_student_category' style='font-size: 12px;'>List of students</a>"
                  else
                    @fees.each do |f|
                      s = f.student
                      @fee = f
                      unless s.has_paid_fees
                        FinanceFee.update_student_fee(@fee_collection, s, f)
                        
                        exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(s.id,@fee_collection.id).map(&:fee_particular_id)
                        unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
                          exclude_particular_ids = exclude_particular_ids
                        else
                          exclude_particular_ids = [0]
                        end
                        if discount.finance_fee_particular_category_id == 0
                          fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                        else
                          fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id} and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                        end
                        payable_ampt = fee_particulars.map{|p| p.amount}.sum.to_f
                        discount_amt = payable_ampt * discount.discount.to_f/ (discount.is_amount?? payable_ampt : 100)

                        student_fee_ledger = StudentFeeLedger.new
                        student_fee_ledger.student_id = s.id
                        student_fee_ledger.ledger_date = Date.today
                        student_fee_ledger.ledger_title = discount.name
                        student_fee_ledger.amount_to_pay = 0.0
                        student_fee_ledger.fee_id = @fee.id
                        student_fee_ledger.particular_id = discount.id
                        student_fee_ledger.amount_paid = discount_amt
                        student_fee_ledger.save
                      end
                    end
                  end
                else
                  batch = Batch.find(batch_id)
                  unless batch.blank?
                    students = batch.students
                    k = 0
                    unless students.blank?
                      students.each do |student|
                        fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{student.batch_id} and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                        amount = fee_particulars.map(&:amount).sum
                        discount_amt = amount * discount.discount.to_f/ (discount.is_amount?? amount : 100)
                        collect_discounts = CollectionDiscount.find(:all, :conditions => "finance_fee_collection_id = #{@fee_collection.id} and fee_discount_id != #{discount.id}")
                        fee_discount_ids = collect_discounts.map(&:fee_discount_id).uniq
                        
                        unless fee_discount_ids.blank?
                          fee_discount_ids = fee_discount_ids.reject { |f| f.to_s.empty? }
                          if fee_discount_ids.blank?
                            fee_discount_ids[0] = 0
                          end
                          fee_discounts = FeeDiscount.find(:all, :conditions => "id IN (#{fee_discount_ids.join(",")}) and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                          fee_discounts.each do |f_discount|
                            is_discount_excluded = false
                            excluded_discounts = StudentExcludeDiscount.find(:all, :conditions => "fee_collection_id = #{@fee_collection.id} and student_id = #{student.id} and fee_discount_id = #{f_discount.id}")
                            
                            unless excluded_discounts.blank?
                              is_discount_excluded = true
                            end
                            
                            unless is_discount_excluded
                              discount_amt += amount * f_discount.discount.to_f/ (f_discount.is_amount?? amount : 100)
                            end
                          end
                        end
                        remaining_amount = amount - discount_amt
                        if remaining_amount < 0
                          found = false
                          std_ids[k] = {:admission_no => student.admission_no, :full_name => student.full_name, :sid => student.id, :roll_no => student.class_roll_no, :balance => amount, :discount_amt => discount_amt}
                          k += 1
                        end
                      end
                      unless found 
                        error_text = "Some Student Discount can't be assign, discount amount is greater than Particular amount. <br /><a href='javascript:;' id='list_of_student_category' style='font-size: 12px;'>List of students</a>"
                      end
                    else
                      render :update do |page|
                        page.replace_html 'category_discount_error', :text => "Some Error occur while assigning Discount, Students not found please contact Administrator"
                        page << "$('category_discount_div').show();"
                        page << "j('#assign_student_#{@discount_id.to_s}').attr('href','#');"
                        page << "j('#assign_student_#{@discount_id.to_s}').attr('onclick',j('#assign_student_#{@discount_id}').data('onclick'));"
                        page << "j('#assign_student_#{@discount_id.to_s}').removeAttr('data-onclick');"
                      end
                    end
                  else
                    render :update do |page|
                      page.replace_html 'category_discount_error', :text => "Some Error occur while assigning Discount, Batch not found please contact Administrator"
                      page << "$('category_discount_div').show();"
                      page << "j('#assign_student_#{@discount_id.to_s}').attr('href','#');"
                      page << "j('#assign_student_#{@discount_id.to_s}').attr('onclick',j('#assign_student_#{@discount_id}').data('onclick'));"
                      page << "j('#assign_student_#{@discount_id.to_s}').removeAttr('data-onclick');"
                    end
                  end
                end
                if found
                  fee_discount_collection = FeeDiscountCollection.new(
                    :finance_fee_collection_id => @fee_collection_id,
                    :fee_discount_id           => @discount_id,
                    :batch_id                  => @batch_id,
                    :is_late                   => 0
                  )
                  fee_discount_collection.save
                  
                  render :update do |page|
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign'
                  end
                else
                  collection_discount.destroy
                  
                  data_std = ""
                  std_ids.each do |s|
                    data_std += "<tr>"
                    data_std += "<td style='text-align: left;'><a href='/student/fee_details/" + s[:sid].to_s + "/"  + @fee_collection_id.to_s + "' target='_blank'>" + s[:admission_no] + "</a></td>"
                    data_std += "<td style='text-align: left;'>" + s[:full_name] + "</td>"
                    data_std += "<td style='text-align: center;'>" + s[:roll_no] + "</td>"
                    fbal = sprintf('%.2f', s[:balance])
                    data_std += "<td style='text-align: right; padding-right: 5px;'>" + fbal.to_s + "</td>"
                    fdiscount = sprintf('%.2f', s[:discount_amt])
                    data_std += "<td style='text-align: right; padding-right: 5px;'>" + fdiscount.to_s + "</td>"
                    data_std += "</tr>"
                  end
                  balance_txt = "Balance"
                  unless total_fee_discount
                    balance_txt = "Total Particular Amount"
                  end
                   
                  render :update do |page|
                    page.replace_html 'student_list_mismatch_discount', :text => data_std
                    page.replace_html 'category_discount_error', :text => error_text
                    page.replace_html 'balance_txt_catgory', :text => balance_txt
                    page << "$('category_discount_div').show();"
                    page << "j('#assign_student_#{@discount_id.to_s}').attr('href','#');"
                    page << "j('#assign_student_#{@discount_id.to_s}').attr('onclick',j('#assign_student_#{@discount_id}').data('onclick'));"
                    page << "j('#assign_student_#{@discount_id.to_s}').removeAttr('data-onclick');"
                  end
                end
                
              else
                render :update do |page|
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign'
                end
              end
            end
          end
        elsif params[:type_discount] == "batch"
          receiver_id = discount.receiver_id
          if receiver_id.to_i == params[:receiver_id].to_i
            if is_late
              f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{@fee_collection_id} and fee_discount_id = #{@discount_id} and batch_id = #{@batch_id} and is_late = 1")
              if f.blank?
                fee_discount_collection = FeeDiscountCollection.new(
                  :finance_fee_collection_id => @fee_collection_id,
                  :fee_discount_id           => @discount_id,
                  :batch_id                  => @batch_id,
                  :is_late                   => 1
                )

                if fee_discount_collection.save
                  render :update do |page|
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign'
                  end
                end
              else
                render :update do |page|
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign'
                end
              end
            else
              f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{@fee_collection_id} and fee_discount_id = #{@discount_id} and batch_id = #{@batch_id} and is_late = 0")
              if f.blank?
                @fee_collection = FinanceFeeCollection.find(@fee_collection_id)

                collection_discount = CollectionDiscount.new(:fee_discount_id=>discount.id,:finance_fee_collection_id=>@fee_collection_id, :finance_fee_particular_category_id => discount.finance_fee_particular_category_id)
                collection_discount.save
                
                found = true
                std_ids = []
                total_fee_discount = false
                if discount.finance_fee_particular_category_id == 0
                  total_fee_discount = true
                  @fees = FinanceFee.find(:all, :conditions=>"fee_collection_id = #{@fee_collection_id} and finance_fees.batch_id = #{batch_id} and is_paid=#{false} and students.batch_id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id INNER JOIN batches ON batches.id = students.batch_id ")

                  k = 0
                  @fees.each do |f|
                    s = f.student
                    @fee = f
                    unless s.has_paid_fees
                      bal = FinanceFee.check_update_student_fee(@fee_collection, s, f)
                      if bal < 0
                        discount_amt = bal * discount.discount.to_f/ (discount.is_amount?? bal : 100)
                        collect_discounts = CollectionDiscount.find(:all, :conditions => "finance_fee_collection_id = #{@fee_collection.id} and fee_discount_id != #{discount.id}")
                        fee_discount_ids = collect_discounts.map(&:fee_discount_id)
                        unless fee_discount_ids.blank?
                          fee_discount_ids = fee_discount_ids.reject { |f| f.to_s.empty? }
                          if fee_discount_ids.blank?
                            fee_discount_ids[0] = 0
                          end
                          fee_discounts = FeeDiscount.find(:all, :conditions => "id IN (#{fee_discount_ids.join(",")}) and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
                          fee_discounts.each do |f_discount|
                            discount_amt += bal * f_discount.discount.to_f/ (f_discount.is_amount?? bal : 100)
                          end
                        end
                        found = false
                        std_ids[k] = {:admission_no => s.admission_no, :full_name => s.full_name, :sid => s.id, :roll_no => s.class_roll_no, :balance => f.balance, :discount_amt => discount_amt}
                        k += 1
                      end
                    end
                  end
                  unless found 
                    error_text = "Some Student Discount can't be assign, discount amount is greater than Fee amount. <br /><a href='javascript:;' id='list_of_student_batch' style='font-size: 12px;'>List of students</a>"
                  else
                    @fees.each do |f|
                      s = f.student
                      @fee = f
                      unless s.has_paid_fees
                        FinanceFee.update_student_fee(@fee_collection, s, f)
                        
                        exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(s.id,@fee_collection.id).map(&:fee_particular_id)
                        unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
                          exclude_particular_ids = exclude_particular_ids
                        else
                          exclude_particular_ids = [0]
                        end
                        if discount.finance_fee_particular_category_id == 0
                          fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                        else
                          fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id} and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                        end
                        payable_ampt = fee_particulars.map{|p| p.amount}.sum.to_f
                        discount_amt = payable_ampt * discount.discount.to_f/ (discount.is_amount?? payable_ampt : 100)

                        student_fee_ledger = StudentFeeLedger.new
                        student_fee_ledger.student_id = s.id
                        student_fee_ledger.ledger_date = Date.today
                        student_fee_ledger.ledger_title = discount.name
                        student_fee_ledger.amount_to_pay = 0.0
                        student_fee_ledger.fee_id = @fee.id
                        student_fee_ledger.particular_id = discount.id
                        student_fee_ledger.amount_paid = discount_amt
                        student_fee_ledger.save
                      end
                    end
                  end
                else
                  batch = Batch.find(batch_id)
                  unless batch.blank?
                    students = batch.students
                    k = 0
                    unless students.blank?
                      students.each do |student|
                        fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{student.batch_id} and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                        amount = fee_particulars.map(&:amount).sum
                        discount_amt = amount * discount.discount.to_f/ (discount.is_amount?? amount : 100)
                        collect_discounts = CollectionDiscount.find(:all, :conditions => "finance_fee_collection_id = #{@fee_collection.id} and fee_discount_id != #{discount.id}")
                        fee_discount_ids = collect_discounts.map(&:fee_discount_id).uniq
                        unless fee_discount_ids.blank?
                          fee_discount_ids = fee_discount_ids.reject { |f| f.to_s.empty? }
                          if fee_discount_ids.blank?
                            fee_discount_ids[0] = 0
                          end
                          fee_discounts = FeeDiscount.find(:all, :conditions => "id IN (#{fee_discount_ids.join(",")}) and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                          fee_discounts.each do |f_discount|
                            is_discount_excluded = false
                            excluded_discounts = StudentExcludeDiscount.find(:all, :conditions => "fee_collection_id = #{@fee_collection.id} and student_id = #{student.id} and fee_discount_id = #{f_discount.id}")
                            unless excluded_discounts.blank?
                              is_discount_excluded = true
                            end
                            unless is_discount_excluded
                              discount_amt += amount * f_discount.discount.to_f/ (f_discount.is_amount?? amount : 100)
                            end
                          end
                        end
                        remaining_amount = amount - discount_amt
                        if remaining_amount < 0
                          found = false
                          std_ids[k] = {:admission_no => student.admission_no, :full_name => student.full_name, :sid => student.id, :roll_no => student.class_roll_no, :balance => amount, :discount_amt => discount_amt}
                          k += 1
                        end
                      end
                      unless found 
                        error_text = "Some Student Discount can't be assign, discount amount is greater than Particular amount. <br /><a href='javascript:;' id='list_of_student_batch' style='font-size: 12px;'>List of students</a>"
                      end
                    else
                      render :update do |page|
                        page.replace_html 'batch_discount_error', :text => "Some Error occur while assigning Discount, Students not found please contact Administrator"
                        page << "$('batch_discount_div').show();"
                        page << "j('#assign_student_#{@discount_id.to_s}').attr('href','#');"
                        page << "j('#assign_student_#{@discount_id.to_s}').attr('onclick',j('#assign_student_#{@discount_id}').data('onclick'));"
                        page << "j('#assign_student_#{@discount_id.to_s}').removeAttr('data-onclick');"
                      end
                    end
                  else
                    render :update do |page|
                      page.replace_html 'batch_discount_error', :text => "Some Error occur while assigning Discount, Batch not found please contact Administrator"
                      page << "$('batch_discount_div').show();"
                      page << "j('#assign_student_#{@discount_id.to_s}').attr('href','#');"
                      page << "j('#assign_student_#{@discount_id.to_s}').attr('onclick',j('#assign_student_#{@discount_id}').data('onclick'));"
                      page << "j('#assign_student_#{@discount_id.to_s}').removeAttr('data-onclick');"
                    end
                  end
                end
                if found
                  fee_discount_collection = FeeDiscountCollection.new(
                    :finance_fee_collection_id => @fee_collection_id,
                    :fee_discount_id           => @discount_id,
                    :batch_id                  => @batch_id,
                    :is_late                   => 0
                  )
                  fee_discount_collection.save
                  
                  render :update do |page|
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign'
                  end
                else
                  collection_discount.destroy
                  
                  data_std = ""
                  std_ids.each do |s|
                    data_std += "<tr>"
                    data_std += "<td style='text-align: left;'><a href='/student/fee_details/" + s[:sid].to_s + "/"  + @fee_collection_id.to_s + "' target='_blank'>" + s[:admission_no] + "</a></td>"
                    data_std += "<td style='text-align: left;'>" + s[:full_name] + "</td>"
                    data_std += "<td style='text-align: center;'>" + s[:roll_no] + "</td>"
                    fbal = sprintf('%.2f', s[:balance])
                    fdiscount = sprintf('%.2f', s[:discount_amt])
                    data_std += "<td style='text-align: right;'>" + fbal.to_s + "</td>"
                    data_std += "<td style='text-align: right; padding-right: 5px;'>" + fdiscount.to_s + "</td>"
                    data_std += "</tr>"
                  end
                  
                  balance_txt = "Balance"
                  unless total_fee_discount
                    balance_txt = "Total Particular Amount"
                  end
                  
                  render :update do |page|
                    page.replace_html 'student_list_mismatch_discount_batch', :text => data_std
                    page.replace_html 'batch_discount_error', :text => error_text
                    page.replace_html 'balance_txt_batch', :text => balance_txt
                    page << "$('batch_discount_div').show();"
                    page << "j('#assign_student_#{@discount_id.to_s}').attr('href','#');"
                    page << "j('#assign_student_#{@discount_id.to_s}').attr('onclick',j('#assign_student_#{@discount_id}').data('onclick'));"
                    page << "j('#assign_student_#{@discount_id.to_s}').removeAttr('data-onclick');"
                  end
                end
              else
                render :update do |page|
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_assign'
                end
              end
            end
          end
        end
      end
    else
      render :text => ''
    end
  end
  
  def remove_fee_discount_from_collection_student
    unless params[:fee_collection_id].nil?
      @discount_id = params[:id]
      @fee_collection_id = params[:fee_collection_id]
      
      discount = FeeDiscount.find(@discount_id)
      
      finance_fee_category_id = discount.finance_fee_category_id
      finance_fee_particular_category_id = discount.finance_fee_particular_category_id
      is_late = discount.is_late
      batch_id = discount.batch_id
      @batch_id = params[:batch_id]
      @receiver_id = discount.receiver_id
      @type_discount = params[:type_discount]
      unless params[:type_discount].nil?
        if params[:type_discount] == "student"
          receiver_id = discount.receiver_id
          if receiver_id.to_i == params[:receiver_id].to_i
            fee_discount_collection = FeeDiscountCollection.find(:first, :conditions => ["finance_fee_collection_id = ? and fee_discount_id = ? and batch_id = ?", @fee_collection_id, @discount_id, @batch_id])
            f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{@fee_collection_id} and fee_discount_id = #{@discount_id} and batch_id = #{@batch_id}")
            unless f.nil?
              f.destroy
              
              unless is_late
                collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
                collection_discount.destroy
                
                @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
                @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@fee_collection_id} and is_paid=#{false} and students.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")

                s = @fee.student
                unless s.has_paid_fees
                  FinanceFee.update_student_fee(@fee_collection, s, @fee)
                  student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and particular_id = #{discount.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0")
                  unless student_fee_ledgers.blank?
                      student_fee_ledgers.each do |student_fee_ledger|
                        student_fee_ledger.destroy
                      end
                    else
                      bal = FinanceFee.get_student_balance(@fee_collection, s, @fee)
                      student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0 and particular_id > 0")
                      amt = 0
                      unless student_fee_ledgers.blank?
                        student_fee_ledgers.each do |student_fee_ledger|
                          if student_fee_ledger.particular_id == discount.id
                            amt += student_fee_ledger.amount_paid.to_f
                          end
                        end
                      end
                      bal = bal + amt
                      bal = 0 if bal < 0
                      student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0 and particular_id = 0")
                      unless student_fee_ledgers.blank?
                        student_fee_ledgers.each do |student_fee_ledger|
                          student_fee_ledger.update_attributes(:amount_to_pay=>bal)
                        end
                      end
                    end
                end
                
                @batch   = Batch.find(@batch_id)
                @date    =  @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
                student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
                
                @from_batch_fee = true
                unless params[:student_fees].nil?
                  if params[:student_fees].to_i == 1
                    @from_batch_fee = false
                  end
                end
                
                @student = Student.find(params[:receiver_id])
                @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{@student.id}'")
                
                unless @fee.nil?
                  get_fees_details(student_ids)
                  
                  render :update do |page|
                    page.replace_html "student", :partial => "student_fees_submission"
                    page << "loadJS();"
                    page << 'j(".select2-combo").select2();'
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove_student'
                  end
                else
                  render :update do |page|
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove_student'
                  end
                end
              else
                collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
                if collection_discount.present?
                  collection_discount.destroy
                end
                
                @batch   = Batch.find(@batch_id)
                @date    =  @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
                student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
                
                @from_batch_fee = true
                unless params[:student_fees].nil?
                  if params[:student_fees].to_i == 1
                    @from_batch_fee = false
                  end
                end
                
                @student = Student.find(params[:receiver_id])
                @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{@student.id}'")
                
                unless @fee.nil?
                  get_fees_details(student_ids)
                  
                  render :update do |page|
                    page.replace_html "student", :partial => "student_fees_submission"
                    page << "loadJS();"
                    page << 'j(".select2-combo").select2();'
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove_student'
                  end
                else
                  render :update do |page|
                    page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove_student'
                  end
                end
              end
            else
              collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
              if collection_discount.present?
                collection_discount.destroy
              end
              
              @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
              @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@fee_collection_id} and is_paid=#{false} and students.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")

              s = @fee.student
              unless s.has_paid_fees
                FinanceFee.update_student_fee(@fee_collection, s, @fee)
                student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and particular_id = #{discount.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0")
                unless student_fee_ledgers.blank?
                    student_fee_ledgers.each do |student_fee_ledger|
                      student_fee_ledger.destroy
                    end
                  else
                    bal = FinanceFee.get_student_balance(@fee_collection, s, @fee)
                    student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0 and particular_id > 0")
                    amt = 0
                    unless student_fee_ledgers.blank?
                      student_fee_ledgers.each do |student_fee_ledger|
                        if student_fee_ledger.particular_id == discount.id
                          amt += student_fee_ledger.amount_paid.to_f
                        end
                      end
                    end
                    bal = bal + amt
                    bal = 0 if bal < 0
                    student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0 and particular_id = 0")
                    unless student_fee_ledgers.blank?
                      student_fee_ledgers.each do |student_fee_ledger|
                        student_fee_ledger.update_attributes(:amount_to_pay=>bal)
                      end
                    end
                  end
              end
              
              @batch   = Batch.find(@batch_id)
              @date    =  @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
              student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')

              @from_batch_fee = true
              unless params[:student_fees].nil?
                if params[:student_fees].to_i == 1
                  @from_batch_fee = false
                end
              end

              @student = Student.find(params[:receiver_id])
              @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{@student.id}'")
              
              unless @fee.nil?
                get_fees_details(student_ids)

                render :update do |page|
                  page.replace_html "student", :partial => "student_fees_submission"
                  page << "loadJS();"
                  page << 'j(".select2-combo").select2();'
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove_student'
                end
              else
                render :update do |page|
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove_student'
                end
              end
            end
          else
            render :update do |page|
              page << "j('#remove_student_#{@discount_id.to_s}').attr('href','#');"
              page << "j('#remove_student_#{@discount_id.to_s}').attr('onclick',j('#remove_student_#{@discount_id}').data('onclick'));"
              page << "j('#remove_student_#{@discount_id.to_s}').removeAttr('data-onclick');"
            end
          end
        end
      end
    else
      render :text => ''
    end
  end
  
  def remove_fee_discount_from_collection
    unless params[:fee_collection_id].nil?
      @discount_id = params[:id]
      @fee_collection_id = params[:fee_collection_id]
      
      discount = FeeDiscount.find(@discount_id)
      
      finance_fee_category_id = discount.finance_fee_category_id
      finance_fee_particular_category_id = discount.finance_fee_particular_category_id
      is_late = discount.is_late
      batch_id = discount.batch_id
      @batch_id = params[:batch_id]
      @receiver_id = discount.receiver_id
      @type_discount = params[:type_discount]
      unless params[:type_discount].nil?
        if params[:type_discount] == "student"
          receiver_id = discount.receiver_id
          if receiver_id.to_i == params[:receiver_id].to_i
            fee_discount_collection = FeeDiscountCollection.find(:first, :conditions => ["finance_fee_collection_id = ? and fee_discount_id = ? and batch_id = ?", @fee_collection_id, @discount_id, @batch_id])
            f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{@fee_collection_id} and fee_discount_id = #{@discount_id} and batch_id = #{@batch_id}")
            unless f.nil?
              f.destroy
              
              unless is_late
                collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
                collection_discount.destroy
                
                @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
                @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@fee_collection_id} and is_paid=#{false} and students.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")

                s = @fee.student
                unless s.has_paid_fees
                  FinanceFee.update_student_fee(@fee_collection, s, @fee)
                  student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and particular_id = #{discount.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0")
                  unless student_fee_ledgers.blank?
                      student_fee_ledgers.each do |student_fee_ledger|
                        student_fee_ledger.destroy
                      end
                    else
                      bal = FinanceFee.get_student_balance(@fee_collection, s, @fee)
                      student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0 and particular_id > 0")
                      amt = 0
                      unless student_fee_ledgers.blank?
                        student_fee_ledgers.each do |student_fee_ledger|
                          if student_fee_ledger.particular_id == discount.id
                            amt += student_fee_ledger.amount_paid.to_f
                          end
                        end
                      end
                      bal = bal + amt
                      bal = 0 if bal < 0
                      student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0 and particular_id = 0")
                      unless student_fee_ledgers.blank?
                        student_fee_ledgers.each do |student_fee_ledger|
                          student_fee_ledger.update_attributes(:amount_to_pay=>bal)
                        end
                      end
                    end
                end
                
                render :update do |page|
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove'
                end
              else
                collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
                if collection_discount.present?
                  collection_discount.destroy
                end
                
                render :update do |page|
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove'
                end
              end
            else
              collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
              if collection_discount.present?
                collection_discount.destroy
              end
              
              @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
              @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@fee_collection_id} and is_paid=#{false} and students.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")

              s = @fee.student
              unless s.has_paid_fees
                FinanceFee.update_student_fee(@fee_collection, s, @fee)
                student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and particular_id = #{discount.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0")
                unless student_fee_ledgers.blank?
                    student_fee_ledgers.each do |student_fee_ledger|
                      student_fee_ledger.destroy
                    end
                  else
                    bal = FinanceFee.get_student_balance(@fee_collection, s, @fee)
                    student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0 and particular_id > 0")
                    amt = 0
                    unless student_fee_ledgers.blank?
                      student_fee_ledgers.each do |student_fee_ledger|
                        if student_fee_ledger.particular_id == discount.id
                          amt += student_fee_ledger.amount_paid.to_f
                        end
                      end
                    end
                    bal = bal + amt
                    bal = 0 if bal < 0
                    student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0 and particular_id = 0")
                    unless student_fee_ledgers.blank?
                      student_fee_ledgers.each do |student_fee_ledger|
                        student_fee_ledger.update_attributes(:amount_to_pay=>bal)
                      end
                    end
                  end
              end
              
              render :update do |page|
                page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove'
              end
            end
          else
            render :update do |page|
              page << "j('#remove_student_#{@discount_id.to_s}').attr('href','#');"
              page << "j('#remove_student_#{@discount_id.to_s}').attr('onclick',j('#remove_student_#{@discount_id}').data('onclick'));"
              page << "j('#remove_student_#{@discount_id.to_s}').removeAttr('data-onclick');"
            end
          end
        elsif params[:type_discount] == "categoy"
          receiver_id = discount.receiver_id
          if receiver_id.to_i == params[:receiver_id].to_i
            fee_discount_collection = FeeDiscountCollection.find(:first, :conditions => ["finance_fee_collection_id = ? and fee_discount_id = ? and batch_id = ?", @fee_collection_id, @discount_id, @batch_id])
            f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{@fee_collection_id} and fee_discount_id = #{@discount_id} and batch_id = #{@batch_id}")
            unless f.nil?
              f.destroy
              
              unless is_late
                collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
                collection_discount.destroy
                
                @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
                @fees = FinanceFee.find(:all, :conditions=>"fee_collection_id = #{@fee_collection_id} and students.batch_id = #{batch_id} and is_paid=#{false} and student_categories.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id INNER JOIN student_categories ON student_categories.id = students.student_category_id ")
              
                @fees.each do |f|
                  s = f.student
                  @fee = f
                  unless s.has_paid_fees
                    FinanceFee.update_student_fee(@fee_collection, s, f)
                    student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and particular_id = #{discount.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0")
                    unless student_fee_ledgers.blank?
                        student_fee_ledgers.each do |student_fee_ledger|
                          student_fee_ledger.destroy
                        end
                      else
                        bal = FinanceFee.get_student_balance(@fee_collection, s, @fee)
                        student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0 and particular_id > 0")
                        amt = 0
                        unless student_fee_ledgers.blank?
                          student_fee_ledgers.each do |student_fee_ledger|
                            if student_fee_ledger.particular_id == discount.id
                              amt += student_fee_ledger.amount_paid.to_f
                            end
                          end
                        end
                        bal = bal + amt
                        bal = 0 if bal < 0
                        student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0 and particular_id = 0")
                        unless student_fee_ledgers.blank?
                          student_fee_ledgers.each do |student_fee_ledger|
                            student_fee_ledger.update_attributes(:amount_to_pay=>bal)
                          end
                        end
                      end
                  end
                end
                
                render :update do |page|
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove'
                end
              else
                collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
                if collection_discount.present?
                  collection_discount.destroy
                end
                
                render :update do |page|
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove'
                end
              end
            else
              collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
              if collection_discount.present?
                collection_discount.destroy
              end
              
              @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
              @fees = FinanceFee.find(:all, :conditions=>"fee_collection_id = #{@fee_collection_id} and students.batch_id = #{batch_id} and is_paid=#{false} and student_categories.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id INNER JOIN student_categories ON student_categories.id = students.student_category_id ")

              @fees.each do |f|
                s = f.student
                @fee = f
                unless s.has_paid_fees
                  FinanceFee.update_student_fee(@fee_collection, s, f)
                  student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and particular_id = #{discount.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0")
                  unless student_fee_ledgers.blank?
                      student_fee_ledgers.each do |student_fee_ledger|
                        student_fee_ledger.destroy
                      end
                    else
                      bal = FinanceFee.get_student_balance(@fee_collection, s, @fee)
                      student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0 and particular_id > 0")
                      amt = 0
                      unless student_fee_ledgers.blank?
                        student_fee_ledgers.each do |student_fee_ledger|
                          if student_fee_ledger.particular_id == discount.id
                            amt += student_fee_ledger.amount_paid.to_f
                          end
                        end
                      end
                      bal = bal + amt
                      bal = 0 if bal < 0
                      student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0 and particular_id = 0")
                      unless student_fee_ledgers.blank?
                        student_fee_ledgers.each do |student_fee_ledger|
                          student_fee_ledger.update_attributes(:amount_to_pay=>bal)
                        end
                      end
                    end
                end
              end
              
              render :update do |page|
                page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove'
              end
            end
          else
            render :update do |page|
              page << "j('#remove_student_#{@discount_id.to_s}').attr('href','#');"
              page << "j('#remove_student_#{@discount_id.to_s}').attr('onclick',j('#remove_student_#{@discount_id}').data('onclick'));"
              page << "j('#remove_student_#{@discount_id.to_s}').removeAttr('data-onclick');"
            end
          end
        elsif params[:type_discount] == "batch"
          receiver_id = discount.receiver_id
          if receiver_id.to_i == params[:receiver_id].to_i
            fee_discount_collection = FeeDiscountCollection.find(:first, :conditions => ["finance_fee_collection_id = ? and fee_discount_id = ? and batch_id = ?", @fee_collection_id, @discount_id, @batch_id])
            f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{@fee_collection_id} and fee_discount_id = #{@discount_id} and batch_id = #{@batch_id}")
            unless f.nil?
              f.destroy
              
              unless is_late
                collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
                collection_discount.destroy
                
                @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
                @fees = FinanceFee.find(:all, :conditions=>"fee_collection_id = #{@fee_collection_id} and finance_fees.batch_id = #{batch_id} and is_paid=#{false} and students.batch_id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id INNER JOIN batches ON batches.id = students.batch_id ")
              
                @fees.each do |f|
                  s = f.student
                  @fee = f
                  unless s.has_paid_fees
                    FinanceFee.update_student_fee(@fee_collection, s, f)
                    student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and particular_id = #{discount.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0")
                    unless student_fee_ledgers.blank?
                        student_fee_ledgers.each do |student_fee_ledger|
                          student_fee_ledger.destroy
                        end
                      else
                        bal = FinanceFee.get_student_balance(@fee_collection, s, @fee)
                        student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0 and particular_id > 0")
                        amt = 0
                        unless student_fee_ledgers.blank?
                          student_fee_ledgers.each do |student_fee_ledger|
                            if student_fee_ledger.particular_id == discount.id
                              amt += student_fee_ledger.amount_paid.to_f
                            end
                          end
                        end
                        bal = bal + amt
                        bal = 0 if bal < 0
                        student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0 and particular_id = 0")
                        unless student_fee_ledgers.blank?
                          student_fee_ledgers.each do |student_fee_ledger|
                            student_fee_ledger.update_attributes(:amount_to_pay=>bal)
                          end
                        end
                      end
                  end
                end
                
                render :update do |page|
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove'
                end
              else
                collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
                if collection_discount.present?
                  collection_discount.destroy
                end
                
                render :update do |page|
                  page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove'
                end
              end
            else
              collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
              if collection_discount.present?
                collection_discount.destroy
              end
              
              @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
              @fees = FinanceFee.find(:all, :conditions=>"fee_collection_id = #{@fee_collection_id} and finance_fees.batch_id = #{batch_id} and is_paid=#{false} and students.batch_id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id INNER JOIN batches ON batches.id = students.batch_id ")

              @fees.each do |f|
                s = f.student
                @fee = f
                unless s.has_paid_fees
                  FinanceFee.update_student_fee(@fee_collection, s, f)
                  student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and particular_id = #{discount.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0")
                  unless student_fee_ledgers.blank?
                      student_fee_ledgers.each do |student_fee_ledger|
                        student_fee_ledger.destroy
                      end
                    else
                      bal = FinanceFee.get_student_balance(@fee_collection, s, @fee)
                      student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0 and particular_id > 0")
                      amt = 0
                      unless student_fee_ledgers.blank?
                        student_fee_ledgers.each do |student_fee_ledger|
                          if student_fee_ledger.particular_id == discount.id
                            amt += student_fee_ledger.amount_paid.to_f
                          end
                        end
                      end
                      bal = bal + amt
                      bal = 0 if bal < 0
                      student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0 and particular_id = 0")
                      unless student_fee_ledgers.blank?
                        student_fee_ledgers.each do |student_fee_ledger|
                          student_fee_ledger.update_attributes(:amount_to_pay=>bal)
                        end
                      end
                    end
                end
              end
              
              render :update do |page|
                page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove'
              end
            end
          else
            render :update do |page|
              page << "j('#remove_student_#{@discount_id.to_s}').attr('href','#');"
              page << "j('#remove_student_#{@discount_id.to_s}').attr('onclick',j('#remove_student_#{@discount_id}').data('onclick'));"
              page << "j('#remove_student_#{@discount_id.to_s}').removeAttr('data-onclick');"
            end
          end
        end
      end
    else
      render :text => ''
    end
  end

  def fee_collection_update
    @batch=Batch.find(params[:batch_id])
    @user = current_user
    finance_fee_collection = FinanceFeeCollection.find params[:id]
    attributes=finance_fee_collection.attributes
    attributes.delete_if{|key,value| ["id","name","start_date","end_date","due_date","created_at"].include? key }
    @finance_fee_collection=FinanceFeeCollection.new(attributes)
    @error=true
    events = @finance_fee_collection.event
    @students=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id",:conditions=>"students.batch_id=#{@batch.id} and finance_fees.fee_collection_id=#{finance_fee_collection.id}")
    render :update do |page|
      FinanceFeeCollection.transaction do
        # if params[:finance_fee_collection][:due_date].to_date >= params[:finance_fee_collection][:end_date].to_date
        finance_fee_collection.delete_collection(@batch.id)
        if @finance_fee_collection.update_attributes(params[:finance_fee_collection])
          new_event =  Event.create(:title=> "Fees Due", :description =>@finance_fee_collection.name, :start_date => @finance_fee_collection.due_date.to_datetime, :end_date => @finance_fee_collection.due_date.to_datetime, :is_due => true , :origin=>@finance_fee_collection)
          BatchEvent.create(:event_id => new_event.id, :batch_id => @batch.id )
          FeeCollectionBatch.create(:finance_fee_collection_id=>@finance_fee_collection.id,:batch_id=>@batch.id)
          @error=false
          events.update_attributes(:start_date=> @finance_fee_collection.due_date.to_datetime, :end_date=> @finance_fee_collection.due_date.to_datetime, :description=>params[:finance_fee_collection][:name]) unless events.blank?
          fee_category_name = @finance_fee_collection.fee_category.name
          subject = "#{t('fees_submission_date')}"
          body = "<p><b>#{t('fee_submission_date_for')} <i>"+fee_category_name+"</i> #{t('has_been_updated')}</b> <br /><br/>
                                #{t('start_date')} : "+@finance_fee_collection.start_date.to_s+"<br />"+
            " #{t('end_date')} : "+@finance_fee_collection.end_date.to_s+" <br />"+
            " #{t('due_date')} : "+@finance_fee_collection.due_date.to_s+" <br /><br /><br />"+
            " #{t('check_your')} #{t('fee_structure')} <br/><br/><br/> "
          recipient_ids = []

          @students.each do |s|

            unless s.has_paid_fees
              FinanceFee.new_student_fee(@finance_fee_collection,s)
              recipient_ids << s.user.id if s.user
            end
          end

          Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => @user.id,
              :recipient_ids => recipient_ids,
              :subject=>subject,
              :body=>body ))
          @finance_fee_collections = @batch.finance_fee_collections.find(:all,:conditions => ["finance_fee_collections.is_deleted = '#{false}'"])
          page.replace_html 'form-errors', :text => ''
          page << "Modalbox.hide();"
          page.replace_html 'fee_collection_dates', :partial => 'fee_collection_list'
          page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('finance.flash12')}</p>"
        else
          raise ActiveRecord::Rollback

        end
        #      else
        #        page.replace_html 'form-errors', :text => "<div id='error-box'><ul><li>#{t('flash_msg15')} .</li></ul></div>"
        #        flash[:notice]=""
        #
        #      end
      end
      if @error
        page.replace_html 'form-errors', :partial => 'class_timings/errors', :object => @finance_fee_collection
        page.visual_effect(:highlight, 'form-errors')
      end
    end
    @finance_fee_collections = @batch.finance_fee_collections.find(:all,:conditions => ["finance_fee_collections.is_deleted = '#{false}'"])
  end
  
  def fee_collection_summary_details
    @finance_fee_collection = FinanceFeeCollection.find params[:id]
    @finance_fee_total = FinanceFee.count(:all, :conditions => "fee_collection_id = #{params[:id]}")
    
    @batches = @finance_fee_collection.fee_collection_batches.map(&:batch_id)
    
    fee_category_id = @finance_fee_collection.fee_category_id
    @fee_category= FinanceFeeCategory.find_by_id(fee_category_id)
    total_students = 0
    @batches.each do |batch|
      b = batch.to_i
      students = Student.find_all_by_batch_id(b)
      unless @fee_category.fee_particulars.all(:conditions=>"is_tmp = 0 and is_deleted=false and batch_id=#{b}").collect(&:receiver_type).include?"Batch"
        cat_ids=@fee_category.fee_particulars.select{|s| s.receiver_type=="StudentCategory"  and (!s.is_deleted and s.batch_id==b.to_i)}.collect(&:receiver_id)
        student_ids=@fee_category.fee_particulars.select{|s| s.receiver_type=="Student" and (!s.is_deleted and s.batch_id==b.to_i)}.collect(&:receiver_id)
        students = students.select{|stu| (cat_ids.include?stu.student_category_id or student_ids.include?stu.id)}
      end
      
      total_students += students.length
    end
    
    @total_students = total_students
    @new_student = total_students.to_i - @finance_fee_total.to_i
   
    @finance_transaction_total = FinanceTransaction.count(:all, :conditions => "finance_fees.fee_collection_id = #{params[:id]}", :joins => "INNER JOIN finance_fees ON finance_fees.id = finance_transactions.finance_id and finance_transactions.finance_type = 'FinanceFee'")
  end
  
  def remove_fee_collection_summary_details
    @finance_fee_collection = FinanceFeeCollection.find params[:id]
  end
  
  def fee_collection_regenrate
    @user = current_user
    
    #Delayed::Job.enqueue(DelayedFeeCollectionRegeneration.new(params[:id], false, false, false, @user, false))
    
    @fee_collection_id = params[:id]
    @new_student_only = false
    @skip_paid_student = false
    @skip_new_student = false
    @sent_remainder = false
    
    
    @finance_fee_collection = FinanceFeeCollection.find(@fee_collection_id)
    batches = @finance_fee_collection.fee_collection_batches.map(&:batch_id)
    
    recipient_ids = []
    
    batches.each do |b|
      b = b.to_i
      batch = Batch.find(b)
      fee_category_id = @finance_fee_collection.fee_category_id
      @fee_category= FinanceFeeCategory.find_by_id(fee_category_id)
      @students = Student.find_all_by_batch_id(b)
      
      unless @fee_category.fee_particulars.all(:conditions=>"is_tmp = 0 and is_deleted=false and batch_id=#{b}").collect(&:receiver_type).include?"Batch"
        cat_ids=@fee_category.fee_particulars.select{|s| s.receiver_type=="StudentCategory"  and (!s.is_deleted and s.batch_id==b.to_i)}.collect(&:receiver_id)
        student_ids=@fee_category.fee_particulars.select{|s| s.receiver_type=="Student" and (!s.is_deleted and s.batch_id==b.to_i)}.collect(&:receiver_id)
        @students = @students.select{|stu| (cat_ids.include?stu.student_category_id or student_ids.include?stu.id)}
      end
      
      @students.each do |s|
        fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@fee_collection_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{s.id}'")
        
        #New Student
        if fee.nil?
          unless @skip_new_student
            FinanceFee.new_student_fee(@finance_fee_collection,s)

            recipient_ids << s.user.id if s.user
            recipient_ids << s.immediate_contact.user_id if s.immediate_contact.present?
          end
        else
          advance_fee_collection = false
          @self_advance_fee = false
          @fee_has_advance_particular = false
          unless @new_student_only
            paid_fees = fee.finance_transactions
            
            if fee.has_advance_fee_id
              if @finance_fee_collection.is_advance_fee_collection
                @self_advance_fee = true
                advance_fee_collection = true
              end
              @fee_has_advance_particular = true
              @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
              @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
              if @advance_ids.blank?
                @advance_ids[0] = 0
              end
              @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
            end

            if advance_fee_collection
              fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
              if fee_collection_advances_particular.include?(0)
                @fee_particulars = @fee_category.fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==batch) }
                @finance_fee_particulars = @finance_fee_collection.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==batch) }.map(&:id)
                
              else
                fee_collection_advances_particular = fee_collection_advances_particular.reject { |a| a.to_s.empty? }
                if fee_collection_advances_particular.blank?
                  fee_collection_advances_particular[0] = 0
                end
                @fee_particulars = @fee_category.fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==batch) }
                @finance_fee_particulars = @finance_fee_collection.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==batch) }.map(&:id)
              end
            else
              @fee_particulars = @fee_category.fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==batch) }
              @finance_fee_particulars = @finance_fee_collection.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==batch) }.map(&:id)
            end
            @fee_particulars.each do |fp|
              unless @finance_fee_particulars.include?(fp.id)
                @collection_particulars = CollectionParticular.find_or_create_by_finance_fee_collection_id_and_finance_fee_particular_id(@finance_fee_collection.id, fp.id)
              end
            end
            
            
            if advance_fee_collection
              fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
              if fee_collection_advances_particular.include?(0)
                @fee_particulars = @finance_fee_collection.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==batch) }
              else
                fee_collection_advances_particular = fee_collection_advances_particular.reject { |a| a.to_s.empty? }
                if fee_collection_advances_particular.blank?
                  fee_collection_advances_particular[0] = 0
                end
                @fee_particulars = @finance_fee_collection.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==batch) }
                @finance_fee_particulars = @finance_fee_collection.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==batch) }.map(&:id)
              end
            else
              @fee_particulars = @finance_fee_collection.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==batch) }
            end
            
            
            if advance_fee_collection
              month = 1
              payable = 0
              @fee_collection_advances.each do |fee_collection_advance|
                @fee_particulars.each do |particular|
                  if fee_collection_advance.particular_id == particular.finance_fee_particular_category_id
                    payable += particular.amount * fee_collection_advance.no_of_month.to_i
                  else
                    payable += particular.amount
                  end
                end
              end
              @total_payable=payable.to_f
            else  
              @total_payable=@fee_particulars.map{|fp| fp.amount}.sum.to_f
            end
            
            @total_discount = 0
            
            
            discounts=FeeDiscount.find_all_by_finance_fee_category_id_and_batch_id_and_is_onetime_and_is_late(@finance_fee_collection.fee_category_id, batch.id,false, false, :conditions=>"is_deleted=0 and finance_fee_particular_category_id > 0")
            discounts.each do |discount|
              CollectionDiscount.create(:fee_discount_id=>discount.id,:finance_fee_collection_id=>@finance_fee_collection.id, :finance_fee_particular_category_id => discount.finance_fee_particular_category_id)
              #FeeDiscountCollection.find_or_create_by_finance_fee_collection_id_and_fee_discount_id_and_batch_id_and_is_late(@finance_fee_collection.id, discount.id, batch.id, discount.is_late)
            end

            if discounts.length == 0
              discounts=FeeDiscount.find_all_by_finance_fee_category_id_and_batch_id_and_is_onetime_and_finance_fee_particular_category_id(@finance_fee_collection.fee_category_id, batch.id,false, false, :conditions=>"is_deleted=0")
              discounts.each do |discount|
                CollectionDiscount.create(:fee_discount_id=>discount.id,:finance_fee_collection_id=>@finance_fee_collection.id, :finance_fee_particular_category_id => 0)
                #FeeDiscountCollection.find_or_create_by_finance_fee_collection_id_and_fee_discount_id_and_batch_id_and_is_late(@finance_fee_collection.id, discount.id, batch.id, discount.is_late)
              end
            end
            
            if advance_fee_collection
              FinanceFee.calculate_discount_new(@total_payable, @finance_fee_collection, batch, s, true, @fee_collection_advances, @fee_has_advance_particular)
            else
              if @fee_has_advance_particular
                FinanceFee.calculate_discount_new(@total_payable, @finance_fee_collection, batch, s, false, @fee_collection_advances, @fee_has_advance_particular)
              else
                FinanceFee.calculate_discount_new(@total_payable, @finance_fee_collection, batch, s, false, nil, @fee_has_advance_particular)
              end
            end
            #abort(@total_discount.to_s)
            bal=(@total_payable-@total_discount).to_f
            
            paid_amount = 0
            found_paid_fees = false
            unless paid_fees.blank? 
              found_paid_fees = true
              paid_fees.each do |pf|
                paid_amount += pf.amount
              end
            end
            
            bal = bal - paid_amount
            if bal < 0
              bal = 0
            end
            
            if @skip_paid_student 
              unless found_paid_fees
                ff = FinanceFee.find(fee.id)
                ff.update_attributes(:balance=>bal)
              end
            else
              ff = FinanceFee.find(fee.id)
              ff.update_attributes(:balance=>bal)
            end
          end
          recipient_ids << s.user.id if s.user
          recipient_ids << s.immediate_contact.user_id if s.immediate_contact.present?
        end
      end
      
      recipient_ids = recipient_ids.compact
      
      if  @sent_remainder
        Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => @user.id,
            :recipient_ids => recipient_ids,
            :subject=>subject,
            :body=>body ))
      end

      prev_record = Configuration.find_by_config_key("job/FeeCollectionRegeneration/1")
      if prev_record.present?
        prev_record.update_attributes(:config_value=>Time.now)
      else
        Configuration.create(:config_key=>"job/FeeCollectionRegeneration/1", :config_value=>Time.now)
      end
      
    end
    
    
    render :update do |page|
      page << "j('#fee_collection_queue').show()"
      page.replace_html 'fee_collection_queue', :text => "#{t('recollection_is_in_queue')}" + " <a href='/scheduled_jobs/FeeCollectionRegeneration/1' style='text-decoration: underline;'>" + "#{t('cick_here_to_view_the_scheduled_job')}"
      page.visual_effect(:highlight, 'fee_collection_queue')
    end
    #
  end
  
  def fee_collection_update_all
    unless params[:finance_fee_collection].nil?
      finance_fee_collection = FinanceFeeCollection.find params[:id]
      finance_fee_collection.update_attributes(params[:finance_fee_collection])
      
      @finance_fee_collections = FinanceFeeCollection.find(:all, :conditions => "is_advance_fee_collection = #{false} and finance_fee_collections.is_deleted = '#{false}'")
    else
      page.replace_html 'form-errors', :partial => 'update_errors'
      page.visual_effect(:highlight, 'form-errors')
    end
  end

  def fee_collection_delete
    @batch=Batch.find(params[:batch_id])
    @finance_fee_collection = FinanceFeeCollection.find params[:id]
    @finance_fee_collection.delete_collection(@batch.id)
    @finance_fee_collections = @batch.finance_fee_collections.find(:all,:conditions => ["finance_fee_collections.is_deleted = '#{false}'"])
  end

  #fees_submission-----------------------------------

  def fees_submission_batch
    @batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>true},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
    @inactive_batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>false},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
    @dates = []
  end

  def update_fees_collection_dates
    unless params[:batch_id].nil? or params[:batch_id].empty? or params[:batch_id].blank?
      @batch = Batch.find(params[:batch_id])
      @dates = @batch.finance_fee_collections
      render :update do |page|
        page.replace_html "fees_collection_dates", :partial => "fees_collection_dates"
        page.replace_html "student", :text => ""
      end
    else
      @dates = []
      render :update do |page|
        page.replace_html "fees_collection_dates", :partial => "fees_collection_dates"
        page.replace_html "student", :text => ""
      end
    end
  end
  
  def update_bill_generation_dates

    @batch = Batch.find(params[:batch_id])
    @dates = @batch.finance_fee_collections
    render :update do |page|
      page.replace_html "fees_collection_dates", :partial => "bill_generation_dates"
      page.replace_html "student", :text => ""
      page << "j('#student').hide()"
    end
  end
  
  def load_particular_wise_reports
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      @date    =  @fee_collection = FinanceFeeCollection.find(params[:date])
      @batches =  @date.batches
      
      batches = @batches.map(&:id)
      batches = batches.reject { |b| b.to_s.empty? }
      if batches.blank?
        batches[0] = 0
      end
      tmp_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id IN (#{batches.join(',')})", :group => "name")
      particulars_name = tmp_particulars.map(&:name)
      
      k = 1
      @particulars = []
      i = 1
      tmp_particulars.each_with_index do |particular, j|
        tmp = {}
        tmp['id'] = j+1
        tmp['name'] = particular.name
        tmp['amount'] = 0.00
        tmp['plusminus'] = true
        tmp['opt'] = 1
        @particulars << tmp
        i += 1
      end
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Total Payable"
      tmp['amount'] = 0.00
      tmp['plusminus'] = false
      tmp['opt'] = 0
      @particulars << tmp
      i += 1
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Discount"
      tmp['amount'] = 0.00
      tmp['plusminus'] = true
      tmp['opt'] = 2
      @particulars << tmp
      i += 1
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Fine"
      tmp['amount'] = 0.00
      tmp['plusminus'] = true
      tmp['opt'] = 1
      @particulars << tmp
      i += 1
      
      @bill_generations = []
      #Rails.cache.delete("particular_wise_bill_generation_#{@date.id}")
      bill_generations_data = Rails.cache.fetch("particular_wise_bill_generation_#{@date.id}"){
        bill_generations_data = []
        @batches.each do |batch|
          student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{batch.id}'").collect(&:student_id)

          bill_generations_students = []
          unless student_ids.blank?
            student_ids.each do |sid|
              tmp = {}

              @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{sid}'")

              unless @fee.nil?
                advance_fee_collection = false
                @self_advance_fee = false
                @fee_has_advance_particular = false
        
                @student = @fee.student

                tmp['id'] = sid
                tmp['name'] = @student.full_name
                @financefee = @student.finance_fee_by_date @date
                
                if @financefee.has_advance_fee_id
                  if @date.is_advance_fee_collection
                    @self_advance_fee = true
                    advance_fee_collection = true
                  end
                  @fee_has_advance_particular = true
                  @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
                  @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
                  if @advance_ids.blank?
                    @advance_ids[0] = 0
                  end
                  @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
                end
                #if @financefee.advance_fee_id.to_i > 0
                
                @due_date = @fee_collection.due_date
                @paid_fees = @fee.finance_transactions

                exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
                unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
                  exclude_particular_ids = exclude_particular_ids
                else
                  exclude_particular_ids = [0]
                end
                  
                if advance_fee_collection
                  fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
                  fee_collection_advances_particular = fee_collection_advances_particular.reject { |f| f.to_s.empty? }
                  if fee_collection_advances_particular.blank?
                    fee_collection_advances_particular[0] = 0
                  end
                  
                  if fee_collection_advances_particular.include?(0)
                    fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==batch) }
                  else
                    fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==batch) }
                  end
                else
                  fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==batch) }
                end
                
                fee_particulars.each do |fee_particular|
                  if particulars_name.include?(fee_particular.name)
                    tmp[fee_particular.name] = fee_particular.amount
                  end
                end

                if advance_fee_collection
                  total_payable = (fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
                else  
                  total_payable = fee_particulars.map{|s| s.amount}.sum.to_f
                end
                
                tmp['Total Payable'] = total_payable
                @total_discount = 0

                if advance_fee_collection
                  calculate_discount(@date, @fee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
                else
                  if @fee_has_advance_particular
                    calculate_discount(@date, @fee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
                  else
                    calculate_discount(@date, @fee.batch, @student, false, nil, @fee_has_advance_particular)
                  end
                end

                tmp['Discount'] = @total_discount

                bal=(total_payable-@total_discount).to_f
                @submission_date = Date.today
                if @financefee.is_paid
                  @paid_fees = @financefee.finance_transactions
                  days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
                else
                  days=(Date.today-@date.due_date.to_date).to_i
                end

                fine_enabled = true
                student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
                unless student_fee_configuration.blank?
                  if student_fee_configuration.config_value.to_i == 1
                    fine_enabled = true
                  else
                    fine_enabled = false
                  end
                end
                
                if @tmp_paid_fees.blank?
                  @tmp_paid_fees = @financefee.finance_transactions
                end
                unless @tmp_paid_fees.blank?
                  @tmp_paid_fees.each do |paid_fee|
                    transaction_id = paid_fee.id
                    online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
                    unless online_payments.blank?
                      fine_enabled = false
                    end
                  end
                end
                
                auto_fine=@date.fine

                @has_fine_discount = false
                if days > 0 and auto_fine and fine_enabled #and @financefee.is_paid == false
                  @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
                  @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule

                  calculate_extra_fine(@date, batch, @student, @fine_rule)

                  @new_fine_amount = @fine_amount
                  get_fine_discount(@date, batch, @student)
                  if @fine_amount < 0
                    @fine_amount = 0
                  end
                end

                tmp['is_paid'] = false

                strip_fine = false
                @paid_fees = @fee.finance_transactions
                tmp['paid_fees'] = @paid_fees

                unless @paid_fees.blank?
                  strip_fine = true
                  tmp['is_paid'] = true
                  @paid_fees.each do |pf|
                    if pf.transaction_date > @date.due_date.to_date
                      strip_fine = false
                    end
                  end
                end

                if strip_fine
                  @fine_amount = 0
                end

                tmp['Fine'] = @fine_amount

                bill_generations_students << tmp
              end
            end
            unless bill_generations_students.blank?
              tmp = {}
              tmp['id'] = batch.id
              tmp['name'] = batch.full_name
              particulars_with_amount = []
              bill_generations_students.each do |fee_data|
                unless @particulars.blank?
                  @particulars.each_with_index do |particular, i|
                    particular['amount'] += fee_data[particular['name']] unless fee_data[particular['name']].nil?
                    particulars_with_amount[i] = particular
                  end
                end
              end
              particulars_with_amount.each_with_index do |particular, i|
                tmp[particular['name']] = particular['amount'] unless particular['amount'].nil?
              end
              
              bill_generations_data << tmp
              
              @particulars.each_with_index do |particular, i|
                particular['amount'] = 0.0
              end
            end
          end
          #k += 1
          #if k > 3
          #  break
          #end
        end
        bill_generations_data
      }
      @bill_generations = bill_generations_data
      render :update do |page|
        page.replace_html "student", :partial => "particular_wise_reports"
      end
    else
      render :update do |page|
        page.replace_html "student", :text => ''
      end
    end
  end
  
  def load_bill_status_report
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      @date    =  @fee_collection = FinanceFeeCollection.find(params[:date])
      @batches =  @date.batches
      
      batches = @batches.map(&:id)
      batches = batches.reject { |b| b.to_s.empty? }
      if batches.blank?
        batches[0] = 0
      end
      tmp_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id IN (#{batches.join(',')})", :group => "name")
      particulars_name = tmp_particulars.map(&:name)
      
      k = 1
      @particulars = []
      i = 1
      tmp_particulars.each_with_index do |particular, j|
        tmp = {}
        tmp['id'] = j+1
        tmp['name'] = particular.name
        tmp['amount'] = 0.00
        tmp['plusminus'] = true
        tmp['opt'] = 1
        @particulars << tmp
        i += 1
      end
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Total Payable"
      tmp['amount'] = 0.00
      tmp['plusminus'] = false
      tmp['opt'] = 0
      @particulars << tmp
      i += 1
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Discount"
      tmp['amount'] = 0.00
      tmp['plusminus'] = true
      tmp['opt'] = 2
      @particulars << tmp
      i += 1
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Fine"
      tmp['amount'] = 0.00
      tmp['plusminus'] = true
      tmp['opt'] = 1
      @particulars << tmp
      i += 1
      
      @bill_generations = []
      #Rails.cache.delete("particular_wise_bill_status_report_#{@date.id}")
      bill_generations_data = Rails.cache.fetch("particular_wise_bill_status_report_#{@date.id}"){
        bill_generations_data = []
        @batches.each do |batch|
          student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{batch.id}'").collect(&:student_id)
          is_paid_student = 0
          is_unpaid_student = 0
          bill_generations_students = []
          unless student_ids.blank?
            student_ids.each do |sid|
              tmp = {}

              @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{sid}'")

              unless @fee.nil?
                advance_fee_collection = false
                @self_advance_fee = false
                @fee_has_advance_particular = false
                
                @student = @fee.student

                tmp['id'] = sid
                tmp['name'] = @student.full_name
                @financefee = @student.finance_fee_by_date @date
                
                if @financefee.has_advance_fee_id
                  if @date.is_advance_fee_collection
                    @self_advance_fee = true
                    advance_fee_collection = true
                  end
                  @fee_has_advance_particular = true
                  @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
                  @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
                  if @advance_ids.blank?
                    @advance_ids[0] = 0
                  end
                  @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
                end
                #if @financefee.advance_fee_id.to_i > 0
                
                @due_date = @fee_collection.due_date
                @paid_fees = @fee.finance_transactions

                exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
                unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
                  exclude_particular_ids = exclude_particular_ids
                else
                  exclude_particular_ids = [0]
                end
                
                if advance_fee_collection
                  fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
                  fee_collection_advances_particular = fee_collection_advances_particular.reject { |f| f.to_s.empty? }
                  if fee_collection_advances_particular.blank?
                    fee_collection_advances_particular[0] = 0
                  end
                  
                  if fee_collection_advances_particular.include?(0)
                    fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==batch) }
                  else
                    fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==batch) }
                  end
                else
                  fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==batch) }
                end
                
                fee_particulars.each do |fee_particular|
                  if particulars_name.include?(fee_particular.name)
                    tmp[fee_particular.name] = fee_particular.amount
                  end
                end

                if advance_fee_collection
                  total_payable = (fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
                else  
                  total_payable = fee_particulars.map{|s| s.amount}.sum.to_f
                end
                tmp['Total Payable'] = total_payable
                @total_discount = 0

                if advance_fee_collection
                  calculate_discount(@date, @fee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
                else
                  if @fee_has_advance_particular
                    calculate_discount(@date, @fee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
                  else
                    calculate_discount(@date, @fee.batch, @student, false, nil, @fee_has_advance_particular)
                  end
                end
                
                tmp['Discount'] = @total_discount

                bal=(total_payable-@total_discount).to_f
                @submission_date = Date.today
                if @financefee.is_paid
                  @paid_fees = @financefee.finance_transactions
                  days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
                else
                  days=(Date.today-@date.due_date.to_date).to_i
                end

                fine_enabled = true
                student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
                unless student_fee_configuration.blank?
                  if student_fee_configuration.config_value.to_i == 1
                    fine_enabled = true
                  else
                    fine_enabled = false
                  end
                end
                
                if @tmp_paid_fees.blank?
                  @tmp_paid_fees = @financefee.finance_transactions
                end
                unless @tmp_paid_fees.blank?
                  @tmp_paid_fees.each do |paid_fee|
                    transaction_id = paid_fee.id
                    online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
                    unless online_payments.blank?
                      fine_enabled = false
                    end
                  end
                end
                
                auto_fine=@date.fine

                @has_fine_discount = false
                if days > 0 and auto_fine and fine_enabled #and @financefee.is_paid == false
                  @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
                  @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule

                  calculate_extra_fine(@date, batch, @student, @fine_rule)

                  @new_fine_amount = @fine_amount
                  get_fine_discount(@date, batch, @student)
                  if @fine_amount < 0
                    @fine_amount = 0
                  end
                end

                tmp['is_paid'] = false

                strip_fine = false
                @paid_fees = @fee.finance_transactions
                tmp['paid_fees'] = @paid_fees

                unless @paid_fees.blank?
                  is_paid_student += 1
                  strip_fine = true
                  tmp['is_paid'] = true
                  paid_amount = 0.0
                  @paid_fees.each do |pf|
                    paid_amount += pf.amount
                    if pf.transaction_date > @date.due_date.to_date
                      strip_fine = false
                    end
                  end
                else
                  is_unpaid_student += 1
                end

                if strip_fine
                  @fine_amount = 0
                end

                tmp['Fine'] = @fine_amount
                tmp['paid_amount'] = paid_amount


                bill_generations_students << tmp
              end
            end
            unless bill_generations_students.blank?
              tmp = {}
              tmp['id'] = batch.id
              tmp['name'] = batch.full_name
              particulars_with_amount = []
              paid_amount = 0
              bill_generations_students.each do |fee_data|
                paid_amount += fee_data['paid_amount'] unless fee_data['paid_amount'].nil?
                unless @particulars.blank?
                  @particulars.each_with_index do |particular, i|
                    particular['amount'] += fee_data[particular['name']] unless fee_data[particular['name']].nil?
                    particulars_with_amount[i] = particular
                  end
                end
              end
              particulars_with_amount.each_with_index do |particular, i|
                tmp[particular['name']] = particular['amount'] unless particular['amount'].nil?
              end
              tmp['paid_student_count'] = is_paid_student
              tmp['unpaid_student_count'] = is_unpaid_student
              tmp['paid_amount'] = paid_amount
              
              bill_generations_data << tmp
              @particulars.each_with_index do |particular, i|
                particular['amount'] = 0.0
              end
            end
          end
          #k += 1
          #if k > 3
          #  break
          #end
        end
        bill_generations_data
      }
      @bill_generations = bill_generations_data
      render :update do |page|
        page.replace_html "student", :partial => "bill_status_reports"
      end
    else
      render :update do |page|
        page.replace_html "student", :text => ''
      end
    end
  end
  
  def load_bill_generation_reports
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      
      @multi_date = false
      @defaulters = []
      if params[:opt].nil?
        @opt = 0;
        @b_id = params[:batch_id];
        @d_id = params[:date];
        @batch   = Batch.find(:all, :conditions => "id = #{params[:batch_id]}")
        batches = @batch.map{|b| b.id}  
        if batches.blank?
          batches[0] = 0
        end
        
        @dates   = FinanceFeeCollection.find(:all, :conditions => "id = #{params[:date]}")
        unless @dates.blank?
          @dates_id = @dates.map(&:id)
          if @dates_id.blank?
            @dates_id[0] = 0
          end
        else
          @dates_id[0] = 0
        end
      else
        if params[:opt].to_i == 1
          @opt = 1;
          @filter_by_course = params[:filter_by_course];
          @d_id = params[:date];
          if params[:filter_by_course].to_i == 1
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            school_course_id = school_course_id.reject { |s| s.to_s.empty? }
            if school_course_id.blank?
              school_course_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
          elsif params[:filter_by_course].to_i == 2
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
          else
            batches = Batch.all.map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
          end
          if batches.blank?
            batches[0] = 0
          end
          
          @date = FinanceFeeCollection.find(params[:date])
          unless @date.blank?
            @date_name = @date.name
            @dates = FinanceFeeCollection.find_all_by_name(@date_name)
            unless @dates.blank?
              @dates_id = @dates.map(&:id)
              if @dates_id.blank?
                @dates_id[0] = 0
              end
            else
              @dates_id[0] = 0
            end
          end
        else
          @opt = 2;
          @b_id = params[:batch_id]
          @section_id = params[:section_id]
          @course_name = params[:course_name]
          @d_id = params[:date]
          batch_id = 0
          course_id = 0
          class_id = 0
          unless params[:batch_id].nil?
            batch_id = params[:batch_id]
          end

          unless params[:course_name].nil?
            class_id = params[:course_name]
          end

          unless params[:section_id].blank?
            course_id = params[:section_id]
          end

          batch_name = ""
          batches = [0]
          if batch_id.to_i > 0
            batch = Batch.find batch_id
            batch_name = batch.name
          end

          class_name = ""
          if class_id.to_i > 0
            course = Course.find class_id
            class_name = course.course_name
          end

          unless batch_name.blank?
            if course_id == 0
              batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
              #abort(bclass_name.)
              unless class_id == 0
                courses = batches_all.map{|b| b.course_id}   
                #abort(courses.inspect)
                #batches = batches_all.map{|b| b.id}
                @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      

                @dates = []
                unless @sections.blank?
                  batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
                  #batches = batches_all.map{|b| b.id}
                end
              end
            else
              batches_all = Batch.find_all_by_name_and_is_deleted_and_course_id(batch_name,false, course_id)
            end

            batches = batches_all.map{|b| b.id}    
          end
          if batches.blank?
            batches[0] = 0
          end
          
          @date = FinanceFeeCollection.find(params[:date])
          unless @date.blank?
            @date_name = @date.name
            @dates = FinanceFeeCollection.find_all_by_name(@date_name)
            unless @dates.blank?
              @dates_id = @dates.map(&:id)
              if @dates_id.blank?
                @dates_id[0] = 0
              end
            else
              @dates_id[0] = 0
            end
          end
        end
      end
      
      #abort(batches.inspect)
      #abort(@dates_id.inspect)
      #@batch   = Batch.find(params[:batch_id])
      @dates    =  @fee_collections = FinanceFeeCollection.find(:all, :conditions => "id IN (#{@dates_id.join(',')})")
      unless @dates.blank?
        @dates_data_id = @dates.map(&:id)
        if @dates_data_id.blank?
          @dates_data_id[0] = 0
        end
      end
      #student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id IN (#{batches.join(',')})").collect(&:student_id)
      #student_ids = FinanceFee.paginate(:all,:conditions=>"batch_id IN (#{batches.join(',')}) and fee_collection_id IN (#{@dates_data_id.join(',')})",:page => params[:page], :per_page => 10)
      
      @student_particulars = {}
      @student_summaries = {}
      @students = {}
      particulars = []
      particular_categories = []
      @student_finance_fees = FinanceFee.paginate(:all,:conditions=>"finance_fees.batch_id IN (#{batches.join(',')}) and finance_fees.fee_collection_id IN (#{@dates_data_id.join(',')})", :joins => "INNER JOIN students ON students.id = finance_fees.student_id",:page => params[:page], :per_page => 500)
      #student_finance_fees = FinanceFee.find(:all,:conditions=>"finance_fees.batch_id IN (#{batches.join(',')}) and finance_fees.fee_collection_id IN (#{@dates_data_id.join(',')})", :joins => "INNER JOIN students ON students.id = finance_fees.student_id")
      
      unless @student_finance_fees.blank?
        @student_finance_fees.each do |fee|
          exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(fee.student.id, fee.finance_fee_collection.id).map(&:fee_particular_id)
          unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
            exclude_particular_ids = exclude_particular_ids
          else
            exclude_particular_ids = [0]
          end

          @student_particulars[fee.student.id] = []
          @students[fee.student.id] = []
          @student_summaries[fee.student.id] = []
          fee_particulars = fee.finance_fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{fee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==fee.student or par.receiver==fee.student.student_category or par.receiver==fee.batch) }
          
          @student_particulars[fee.student.id] << fee_particulars
          @students[fee.student.id] << fee.student
          particulars << fee_particulars.map(&:name)
          particular_categories << fee_particulars.map{|fp| fp.finance_fee_particular_category.name}
          #if fee.student.id == 32262
          #  abort(particular_categories.inspect)
          #end

          @total_discount = 0
          @total_payable=fee_particulars.map{|s| s.amount}.sum.to_f
          calculate_discount(fee.finance_fee_collection, fee.batch, fee.student, false, nil, false)

          paid_amount = 0
          paid_fine = 0
          paid_fees = fee.finance_transactions
          unless paid_fees.blank?
            paid_fines = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Fine' AND finance_transaction_id IN (" + paid_fees.map(&:id).join(",") + ")")
            tmp_paid_fines = []
            unless paid_fines.nil?
              paid_fines.each do |pf|
                paid_fine = pf.amount
              end
            end
            paid_discounts = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Adjustment' AND transaction_type = 'Discount' AND finance_transaction_id IN (" + paid_fees.map(&:id).join(",") + ")")
            discount = 0
            unless paid_discounts.nil?
              paid_discounts.each do |pf|
                discount = pf.amount
              end
            end
            @total_discount = discount
            paid_amount += paid_fees.map(&:amount).sum.to_f
          end
          @student_summaries[fee.student.id] << {"discount" => @total_discount, 'fine' => paid_fine, "paid_amount" => paid_amount}
        end
        
#        #abort(particular_categories.inspect)
#        ar_particular_categories = particular_categories
#        pt = []
#        ar_particular_categories.each_with_index do |particular_categories, i|
#          particular_categories.each do |particular_category|
#            pt << particular_category
#          end
#        end
#        @particular_categories = pt
        
        fee_cateroies = FinanceFeeParticularCategory.active
        @particular_categories = fee_cateroies.map{|p| p.name}
        #if fee.student_id == 28046
            #abort(pt.inspect)
         # end
        render :update do |page|
          page << "j('#student').show();"
          page.replace_html "student", :partial => "bill_generation_reports"
        end
      else
          render :update do |page|
            page << "j('#student').hide();"
            page.replace_html "student", :text => ''
          end
      end  
    else
      render :update do |page|
        page << "j('#student').hide();"
        page.replace_html "student", :text => ''
      end
    end
  end
  
  def bill_generation_report_batch
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      @batch   = Batch.find(params[:batch_id])
      @date    =  @fee_collection = FinanceFeeCollection.find(params[:date])
      student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id)

      tmp_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}", :group => "name")
      particulars_name = tmp_particulars.map(&:name)
      @particulars = []
      i = 1
      tmp_particulars.each_with_index do |particular, j|
        tmp = {}
        tmp['id'] = j+1
        tmp['name'] = particular.name
        tmp['amount'] = 0.00
        tmp['plusminus'] = true
        tmp['opt'] = 1
        @particulars << tmp
        i += 1
      end
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Total Payable"
      tmp['amount'] = 0.00
      tmp['plusminus'] = false
      tmp['opt'] = 0
      @particulars << tmp
      i += 1
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Discount"
      tmp['amount'] = 0.00
      tmp['plusminus'] = true
      tmp['opt'] = 2
      @particulars << tmp
      i += 1
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Fine"
      tmp['amount'] = 0.00
      tmp['plusminus'] = true
      tmp['opt'] = 1
      @particulars << tmp
      i += 1
      
      @bill_generations = []

      student_ids.each do |sid|
        tmp = {}
        
        @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{sid}'")
        
        unless @fee.nil?
          advance_fee_collection = false
          @self_advance_fee = false
          @fee_has_advance_particular = false
        
          @student = @fee.student
          
          tmp['id'] = sid
          tmp['name'] = @student.full_name
          @financefee = @student.finance_fee_by_date @date
          
          if @financefee.has_advance_fee_id
            if @date.is_advance_fee_collection
              @self_advance_fee = true
              advance_fee_collection = true
            end
            @fee_has_advance_particular = true
            @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
            @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
            if @advance_ids.blank?
              @advance_ids[0] = 0
            end
            @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
          end
        
          #if @financefee.advance_fee_id.to_i > 0
          
          @due_date = @fee_collection.due_date
          @paid_fees = @fee.finance_transactions
          
          exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
          unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
            exclude_particular_ids = exclude_particular_ids
          else
            exclude_particular_ids = [0]
          end
          
          if advance_fee_collection
            
            if @fee_collection_advances.particular_id == 0
              fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
            else
              fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
            end
          else
            fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
          end
          fee_particulars.each do |fee_particular|
            if particulars_name.include?(fee_particular.name)
              tmp[fee_particular.name] = fee_particular.amount
            end
          end
          
          if advance_fee_collection
            total_payable = (fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
          else  
            total_payable = fee_particulars.map{|s| s.amount}.sum.to_f
          end
          tmp['Total Payable'] = total_payable
          @total_discount = 0
        
          if advance_fee_collection
            calculate_discount(@date, @fee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
          else
            if @fee_has_advance_particular
              calculate_discount(@date, @fee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
            else
              calculate_discount(@date, @fee.batch, @student, false, nil, @fee_has_advance_particular)
            end
          end
          
          tmp['Discount'] = @total_discount
          
          bal=(total_payable-@total_discount).to_f
          @submission_date = Date.today
          if @financefee.is_paid
            @paid_fees = @financefee.finance_transactions
            days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
          else
            days=(Date.today-@date.due_date.to_date).to_i
          end

          fine_enabled = true
          student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
          unless student_fee_configuration.blank?
            if student_fee_configuration.config_value.to_i == 1
              fine_enabled = true
            else
              fine_enabled = false
            end
          end
          
          if @tmp_paid_fees.blank?
            @tmp_paid_fees = @financefee.finance_transactions
          end
          
          unless @tmp_paid_fees.blank?
            @tmp_paid_fees.each do |paid_fee|
              transaction_id = paid_fee.id
              online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
              unless online_payments.blank?
                fine_enabled = false
              end
            end
          end
          
          auto_fine=@date.fine

          @has_fine_discount = false
          if days > 0 and auto_fine and fine_enabled #and @financefee.is_paid == false
            @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
            @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule

            calculate_extra_fine(@date, @batch, @student, @fine_rule)

            @new_fine_amount = @fine_amount
            get_fine_discount(@date, @batch, @student)
            if @fine_amount < 0
              @fine_amount = 0
            end
          end
          
          tmp['is_paid'] = false
          
          strip_fine = false
          @paid_fees = @fee.finance_transactions
          tmp['paid_fees'] = @paid_fees
          
          unless @paid_fees.blank?
            strip_fine = true
            tmp['is_paid'] = true
            @paid_fees.each do |pf|
              if pf.transaction_date > @date.due_date.to_date
                strip_fine = false
              end
            end
          end
          
          if strip_fine
            @fine_amount = 0
          end
          
          tmp['Fine'] = @fine_amount
          
          
          @bill_generations << tmp
        end
      end
    end
  end
  
  def bill_generation_report_batch_paid
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      @batch   = Batch.find(params[:batch_id])
      @date    =  @fee_collection = FinanceFeeCollection.find(params[:date])
      student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id)

      tmp_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}", :group => "name")
      particulars_name = tmp_particulars.map(&:name)
      @particulars = []
      i = 1
      tmp_particulars.each_with_index do |particular, j|
        tmp = {}
        tmp['id'] = j+1
        tmp['name'] = particular.name
        tmp['amount'] = 0.00
        tmp['plusminus'] = true
        tmp['opt'] = 1
        @particulars << tmp
        i += 1
      end
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Total Payable"
      tmp['amount'] = 0.00
      tmp['plusminus'] = false
      tmp['opt'] = 0
      @particulars << tmp
      i += 1
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Discount"
      tmp['amount'] = 0.00
      tmp['plusminus'] = true
      tmp['opt'] = 2
      @particulars << tmp
      i += 1
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Fine"
      tmp['amount'] = 0.00
      tmp['plusminus'] = true
      tmp['opt'] = 1
      @particulars << tmp
      i += 1
      
      @bill_generations = []

      student_ids.each do |sid|
        tmp = {}
        
        @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{sid}'")
        
        unless @fee.nil?
          advance_fee_collection = false
          @self_advance_fee = false
          @fee_has_advance_particular = false
        
          @student = @fee.student
          
          tmp['id'] = sid
          tmp['name'] = @student.full_name
          @financefee = @student.finance_fee_by_date @date
          
          if @financefee.has_advance_fee_id
            if @date.is_advance_fee_collection
              @self_advance_fee = true
              advance_fee_collection = true
            end
            @fee_has_advance_particular = true
            @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
            @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
            if @advance_ids.blank?
              @advance_ids[0] = 0
            end
            @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
          end
          #if @financefee.advance_fee_id.to_i > 0
            
          @due_date = @fee_collection.due_date
          @paid_fees = @fee.finance_transactions
          
          exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
          unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
            exclude_particular_ids = exclude_particular_ids
          else
            exclude_particular_ids = [0]
          end
          
          if advance_fee_collection
            
            if @fee_collection_advances.particular_id == 0
              fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
            else
              fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
            end
          else
            fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
          end
          fee_particulars.each do |fee_particular|
            if particulars_name.include?(fee_particular.name)
              tmp[fee_particular.name] = fee_particular.amount
            end
          end
          
          if advance_fee_collection
            total_payable = (fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
          else  
            total_payable = fee_particulars.map{|s| s.amount}.sum.to_f
          end
          tmp['Total Payable'] = total_payable
          @total_discount = 0
        
          if advance_fee_collection
            calculate_discount(@date, @fee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
          else
            if @fee_has_advance_particular
              calculate_discount(@date, @fee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
            else
              calculate_discount(@date, @fee.batch, @student, false, nil, @fee_has_advance_particular)
            end
          end
          
          tmp['Discount'] = @total_discount
          
          bal=(total_payable-@total_discount).to_f
          @submission_date = Date.today
          if @financefee.is_paid
            @paid_fees = @financefee.finance_transactions
            days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
          else
            days=(Date.today-@date.due_date.to_date).to_i
          end

          fine_enabled = true
          student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
          unless student_fee_configuration.blank?
            if student_fee_configuration.config_value.to_i == 1
              fine_enabled = true
            else
              fine_enabled = false
            end
          end
          
          if @tmp_paid_fees.blank?
            @tmp_paid_fees = @financefee.finance_transactions
          end
          
          unless @tmp_paid_fees.blank?
            @tmp_paid_fees.each do |paid_fee|
              transaction_id = paid_fee.id
              online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
              unless online_payments.blank?
                fine_enabled = false
              end
            end
          end
          
          auto_fine=@date.fine

          @has_fine_discount = false
          if days > 0 and auto_fine and fine_enabled #and @financefee.is_paid == false
            @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
            @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule

            calculate_extra_fine(@date, @batch, @student, @fine_rule)

            @new_fine_amount = @fine_amount
            get_fine_discount(@date, @batch, @student)
            if @fine_amount < 0
              @fine_amount = 0
            end
          end
          
          is_paid = false
          tmp['is_paid'] = false
          
          strip_fine = false
          @paid_fees = @fee.finance_transactions
          tmp['paid_fees'] = @paid_fees
          tmp['amount_paid'] = 0
          unless @paid_fees.blank?
            is_paid = true
            strip_fine = true
            tmp['is_paid'] = true
            @paid_fees.each do |pf|
              tmp['amount_paid'] += pf.amount
              if pf.transaction_date > @date.due_date.to_date
                strip_fine = false
              end
            end
          end
          
          if strip_fine
            @fine_amount = 0
          end
          
          tmp['Fine'] = @fine_amount
          
          if is_paid
            @bill_generations << tmp
          end
        end
      end
    end
  end
  
  def bill_generation_report_batch_unpaid
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      @batch   = Batch.find(params[:batch_id])
      @date    =  @fee_collection = FinanceFeeCollection.find(params[:date])
      student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id)

      tmp_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}", :group => "name")
      particulars_name = tmp_particulars.map(&:name)
      @particulars = []
      i = 1
      tmp_particulars.each_with_index do |particular, j|
        tmp = {}
        tmp['id'] = j+1
        tmp['name'] = particular.name
        tmp['amount'] = 0.00
        tmp['plusminus'] = true
        tmp['opt'] = 1
        @particulars << tmp
        i += 1
      end
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Total Payable"
      tmp['amount'] = 0.00
      tmp['plusminus'] = false
      tmp['opt'] = 0
      @particulars << tmp
      i += 1
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Discount"
      tmp['amount'] = 0.00
      tmp['plusminus'] = true
      tmp['opt'] = 2
      @particulars << tmp
      i += 1
      
      tmp = {}
      tmp['id'] = i
      tmp['name'] = "Fine"
      tmp['amount'] = 0.00
      tmp['plusminus'] = true
      tmp['opt'] = 1
      @particulars << tmp
      i += 1
      
      @bill_generations = []

      student_ids.each do |sid|
        tmp = {}
        
        @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{sid}'")
        
        unless @fee.nil?
          advance_fee_collection = false
          @self_advance_fee = false
          @fee_has_advance_particular = false
        
          @student = @fee.student
          
          tmp['id'] = sid
          tmp['name'] = @student.full_name
          @financefee = @student.finance_fee_by_date @date
          
          if @financefee.has_advance_fee_id
            if @date.is_advance_fee_collection
              @self_advance_fee = true
              advance_fee_collection = true
            end
            @fee_has_advance_particular = true
            @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
            @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
            if @advance_ids.blank?
              @advance_ids[0] = 0
            end
            @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
          end
          
          #if @financefee.advance_fee_id.to_i > 0
          
          @due_date = @fee_collection.due_date
          @paid_fees = @fee.finance_transactions
          
          exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
          unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
            exclude_particular_ids = exclude_particular_ids
          else
            exclude_particular_ids = [0]
          end
          
          if advance_fee_collection
            if @fee_collection_advances.particular_id == 0
              fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
            else
              fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
            end
          else
            fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
          end
          fee_particulars.each do |fee_particular|
            if particulars_name.include?(fee_particular.name)
              tmp[fee_particular.name] = fee_particular.amount
            end
          end
          
          if advance_fee_collection
            total_payable = (fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
          else  
            total_payable = fee_particulars.map{|s| s.amount}.sum.to_f
          end
          tmp['Total Payable'] = total_payable
          @total_discount = 0
        
          if advance_fee_collection
            calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
          else
            if @fee_has_advance_particular
              calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
            else
              calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
            end
          end
          
          tmp['Discount'] = @total_discount
          
          bal=(total_payable-@total_discount).to_f
          @submission_date = Date.today
          if @financefee.is_paid
            @paid_fees = @financefee.finance_transactions
            days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
          else
            days=(Date.today-@date.due_date.to_date).to_i
          end

          fine_enabled = true
          student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
          unless student_fee_configuration.blank?
            if student_fee_configuration.config_value.to_i == 1
              fine_enabled = true
            else
              fine_enabled = false
            end
          end
          
          if @tmp_paid_fees.blank?
            @tmp_paid_fees = @financefee.finance_transactions
          end
          
          unless @tmp_paid_fees.blank?
            @tmp_paid_fees.each do |paid_fee|
              transaction_id = paid_fee.id
              online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
              unless online_payments.blank?
                fine_enabled = false
              end
            end
          end
          
          auto_fine=@date.fine

          @has_fine_discount = false
          if days > 0 and auto_fine and fine_enabled #and @financefee.is_paid == false
            @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
            @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule

            calculate_extra_fine(@date, @batch, @student, @fine_rule)

            @new_fine_amount = @fine_amount
            get_fine_discount(@date, @batch, @student)
            if @fine_amount < 0
              @fine_amount = 0
            end
          end
          
          is_paid = false
          tmp['is_paid'] = false
          
          strip_fine = false
          @paid_fees = @fee.finance_transactions
          tmp['paid_fees'] = @paid_fees
          unless @paid_fees.blank?
            is_paid = true
            strip_fine = true
            tmp['is_paid'] = true
            @paid_fees.each do |pf|
              if pf.transaction_date > @date.due_date.to_date
                strip_fine = false
              end
            end
          end
          
          if strip_fine
            @fine_amount = 0
          end
          
          tmp['Fine'] = @fine_amount
          
          if is_paid == false
            @bill_generations << tmp
          end
        end
      end
    end
  end

  def create_fees_with_tmp_particular
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      @batch   = Batch.find(params[:batch_id])
      @date    =  @fee_collection = FinanceFeeCollection.find(params[:date])
      
      student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
      
      @student = Student.find(params[:student])
      @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{@student.id}'")
      
      @particular_id = params[:particular_category]

      @particular_n = FinanceFeeParticularCategory.first(:select=>"name",:conditions=>"id = #{@particular_id} AND is_deleted = #{false}")
      @particular_name = params[:particular].gsub('--', '&')
      #abort(@particular_name.inspect)
      # @finance_fee_particular_category = FinanceFeeParticularCategory.find_or_create_by_name_and_description_and_is_deleted(@particular_name, '',false)
      
      #@particular = @batch.finance_fee_particulars.find_by_finance_fee_category_id_and_finance_fee_particular_category_id_and_receiver_type_and_receiver_id(@date.fee_category_id, @particular_id, 'Student', @student.id)
      @particular = nil
      
      if @particular.nil?
        @o_particular = {}
        @o_particular[:amount] = params[:amount]
        @o_particular[:description] = ''
        @o_particular[:finance_fee_category_id] = @date.fee_category_id
        @o_particular[:finance_fee_particular_category_id] = @particular_id
        @o_particular[:name] = @particular_name
        @o_particular[:receiver_type] = 'Student'

        @finance_fee_particular = @batch.finance_fee_particulars.new(@o_particular)
        @finance_fee_particular.receiver_id = @student.id
        @finance_fee_particular.is_tmp = true
        @error = true unless @finance_fee_particular.save

        @collection_particulars = CollectionParticular.find_or_create_by_finance_fee_collection_id_and_finance_fee_particular_id(@date.id, @finance_fee_particular.id)
      else 
        @finance_fee_particular = @particular
        @collection_particulars = CollectionParticular.find_or_create_by_finance_fee_collection_id_and_finance_fee_particular_id(@date.id, @finance_fee_particular.id)
      end
      
      FinanceFee.new_student_fee_with_tmp_particular(@date,@student)
      fee = FinanceFee.find_by_student_id_and_fee_collection_id_and_batch_id_and_is_paid(@student.id, @date.id, @student.batch_id, false)
      unless fee.blank?
        student_fee_ledger = StudentFeeLedger.new
        student_fee_ledger.student_id = @student.id
        student_fee_ledger.ledger_date = Date.today
        student_fee_ledger.ledger_title = @particular_name + " for " + @date.name
        student_fee_ledger.amount_to_pay = params[:amount].to_f
        student_fee_ledger.fee_id = fee.id
        student_fee_ledger.particular_id = @finance_fee_particular.id
        student_fee_ledger.save
      end

      unless @fee.is_paid
        if @particular_name.downcase == 'vat'
          if params[:no_vat].nil?
            render :update do |page|
              page << 'j(".chk_extra_particular").show();'
              page << 'j("#fee_vat_chk").hide();'
              page << 'j("#fee_vat_amount").attr("type","hidden");'
              page << 'j(".assign_vat_to_user").hide();'
              page << 'j("#fee_amount_vat").show();'
              page << 'j("#fee_amount_vat" + " span").html(parseFloat(j("#fee_vat_amount").val()).toFixed(2));';
              page << 'calculateTotalFees();'
              page.replace_html "assign_vat_to_user", :text => ""
              page.replace_html "rm_vat", :partial => "remove_tmp_particular"
            end
          else
            render :update do |page|
              page.replace_html "particulars_tr_id", :partial => "temp_particular"
              page << 'j(".chk_extra_particular").show();'
              page << 'j("#particulars_tr_id").addClass("particulars_tr");'
              page << 'j("#particulars_tr_id").attr("id","particular_' + @finance_fee_particular.id.to_s + '");'
              page << 'j("#particulars_tr_extra").remove();'
              page << 'resetSN();'
              page << 'calculateAmountToPay(0);'
              page << 'calculateDiscount();'
              page << 'calculateTotalFees() ;'
            end
          end
        else
          render :update do |page|
            page.replace_html "particulars_tr_id", :partial => "temp_particular"
            page << 'j(".chk_extra_particular").show();'
            page << 'j("#particulars_tr_id").addClass("particulars_tr");'
            page << 'j("#particulars_tr_id").attr("id","particular_' + @finance_fee_particular.id.to_s + '");'
            page << 'j("#particulars_tr_extra").remove();'
            page << 'resetSN();'
            page << 'calculateAmountToPay(0);'
            page << 'calculateDiscount();'
            page << 'calculateTotalFees() ;'
          end

        end
      else
        load_fees_submission_batch
      end


    end
  end
  
  def create_absent_fine_particular_multiple
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      #@batch   = Batch.find(params[:batch_id])
      @date    =  @fee_collection = FinanceFeeCollection.find(params[:date])
      
      @fine_amount = params[:fine_amount]
      @fine_name = params[:fine_name]
      
      require "yaml"
      finance_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/finance_absent_fine.yml")['school']
      all_schools = finance_config['ids'].split(",")
      current_school = MultiSchool.current_school.id
      @absent_fine = false
      if all_schools.include?(current_school.to_s)
        @absent_fine = true
      end
      
      if @absent_fine
        finance_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/finance_absent_fine.yml")['school']
        particular_category_id = finance_config['absent_particular_id_' + MultiSchool.current_school.id.to_s]
        if particular_category_id.blank?
          particular_category_id = 0
        end
        @particular_category_id = particular_category_id
        
      else
        @finance_fee_particular_category = FinanceFeeParticularCategory.find_or_create_by_name_and_description_and_is_deleted(@fine_name, '',false)
        @particular_category_id = @finance_fee_particular_category.id
      end
      
      #abort(@particular_category_id.inspect)
      
      #
      #@particular_category_id = 187
      @finance_fee_particular_category = FinanceFeeParticularCategory.find(:first, :conditions => "id = #{@particular_category_id}")
      
      @particular_category_id = @finance_fee_particular_category.id
      
      @student_ids_n_amounts = params[:students].split(",")
      @student_ids_n_amounts.each do |student_ids_n_amount|
        a_student_id_n_days = student_ids_n_amount.split("-")
        s_id = a_student_id_n_days[0].to_i
        days = a_student_id_n_days[1].to_i
        
        amt = @fine_amount.to_f * days.to_f
        
        @student = Student.find(s_id)
        
        @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{@student.id}'")
        
        @particular_id = @particular_category_id
        #@particular_name = @finance_fee_particular_category.name
        @particular_name = @finance_fee_particular_category.name
        @particular = @fee.batch.finance_fee_particulars.find_by_finance_fee_category_id_and_finance_fee_particular_category_id_and_receiver_type_and_receiver_id_and_amount(@date.fee_category_id, @particular_id, 'Student', @student.id, amt)
        
        if @particular.nil?
          @o_particular = {}
          @o_particular[:amount] = amt
          @o_particular[:description] = ''
          @o_particular[:finance_fee_category_id] = @date.fee_category_id
          @o_particular[:finance_fee_particular_category_id] = @particular_id
          @o_particular[:name] = @particular_name
          @o_particular[:receiver_type] = 'Student'

          @finance_fee_particular = @fee.batch.finance_fee_particulars.new(@o_particular)
          @finance_fee_particular.receiver_id = @student.id
          @finance_fee_particular.is_tmp = true
          @error = true unless @finance_fee_particular.save

          @collection_particulars = CollectionParticular.find_or_create_by_finance_fee_collection_id_and_finance_fee_particular_id(@date.id, @finance_fee_particular.id)
        else 
          @finance_fee_particular = @particular
          @collection_particulars = CollectionParticular.find_or_create_by_finance_fee_collection_id_and_finance_fee_particular_id(@date.id, @finance_fee_particular.id)
        end
        
        FinanceFee.new_student_fee_with_tmp_particular(@date,@student)
        fee = FinanceFee.find_by_student_id_and_fee_collection_id_and_batch_id_and_is_paid(@student.id, @date.id, @student.batch_id, false)
        unless fee.blank?
          student_fee_ledger = StudentFeeLedger.new
          student_fee_ledger.student_id = @student.id
          student_fee_ledger.ledger_date = Date.today
          student_fee_ledger.ledger_title = @particular_name + " for " + @date.name
          student_fee_ledger.amount_to_pay = amt.to_f
          student_fee_ledger.fee_id = fee.id
          student_fee_ledger.particular_id = @finance_fee_particular.id
          student_fee_ledger.save
        end
        
      end
      
      unless params[:batch_id].blank?
        @finance_fees = FinanceFee.all(:select=>"finance_fees.id,finance_fees.student_id,finance_fees.is_paid,finance_fees.balance",:joins=>"INNER JOIN students ON students.id = finance_fees.student_id",:order => "if(students.class_roll_no = '' or students.class_roll_no is null,0,cast(students.class_roll_no as unsigned)),students.first_name ASC", :conditions=>"finance_fees.fee_collection_id = #{params[:date]} AND finance_fees.batch_id = #{params[:batch_id]}")
        render :update do |page|
          page.replace_html "resultDiv", :partial => "collection_details_view_inactive"
          page << "j('#absent_fine_clicked').hide();"
          page << "j('#add_absent_fine').trigger('click');"
          page << "j('#fine_name').val('');"
          page << "j('#fine_amount').val('');"
          page << "j('#save_absent_fine').removeAttr('disabled');"
          page << "j('#save_absent_fine_panel').hide();"
        end
      else
        @finance_fees = FinanceFee.all(:select=>"finance_fees.id,finance_fees.student_id,finance_fees.is_paid,finance_fees.balance",:joins=>"INNER JOIN students ON students.id = finance_fees.student_id",:order => "if(students.class_roll_no = '' or students.class_roll_no is null,0,cast(students.class_roll_no as unsigned)),students.first_name ASC", :conditions=>"finance_fees.fee_collection_id = #{params[:date]}")
        render :update do |page|
          page << "j('#absent_fine_clicked').hide();"
          page << "j('#add_absent_fine').trigger('click');"
          page << "j('#fine_name').val('');"
          page << "j('#fine_amount').val('');"
          page << "j('#save_absent_fine').removeAttr('disabled');"
          page << "j('#save_absent_fine_panel').hide();"
          page.replace_html "particulars", :partial => "collection_details_view_admission"
        end
      end
#      @finance_fees = FinanceFee.all(:select=>"finance_fees.id,finance_fees.student_id,finance_fees.is_paid,finance_fees.balance",:joins=>"INNER JOIN students ON students.id = finance_fees.student_id",:order => "if(students.class_roll_no = '' or students.class_roll_no is null,0,cast(students.class_roll_no as unsigned)),students.first_name ASC", :conditions=>"finance_fees.fee_collection_id = #{params[:date]} AND finance_fees.batch_id = #{params[:batch_id]}")

    end
  end
  
  def create_fees_with_tmp_discount
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      unless params[:discount_name].nil? or params[:discount_name].empty? or params[:discount_name].blank?
        discount_name = params[:discount_name].gsub("_____","%")
        discount_name = discount_name.gsub("____",")")
        discount_name = discount_name.gsub("___","(")
      else
        discount_name = "Discount"
      end
      @batch   = Batch.find(params[:batch_id])
      @date    =  @fee_collection = FinanceFeeCollection.find(params[:date])
      @fee_category_id = @fee_collection.fee_category_id
      
      unless params[:discount_ids].blank?
        @discount_ids = params[:discount_ids]
        discount_ids = params[:discount_ids].split(",")
        discount_ids.each do |did|
          discount = FeeDiscount.find(did)
          fee_collection_id = params[:date]
          if discount.receiver_type == "Student"
            finance_fee_category_id = discount.finance_fee_category_id
            finance_fee_particular_category_id = discount.finance_fee_particular_category_id
            is_late = discount.is_late
            batch_id = discount.batch_id
            
            receiver_id = discount.receiver_id
            if receiver_id.to_i == params[:student].to_i
              f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{fee_collection_id} and fee_discount_id = #{did} and batch_id = #{@batch.id}")
              unless f.nil?
                f.destroy
                unless is_late
                  collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,fee_collection_id, discount.finance_fee_particular_category_id)
                  collection_discount.destroy
                  
                  @fee_collection = FinanceFeeCollection.find(fee_collection_id)
                  @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{fee_collection_id} and is_paid=#{false} and students.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")

                  s = @fee.student
                  unless s.has_paid_fees
                    FinanceFee.update_student_fee(@fee_collection, s, @fee)
                    
                    exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(s.id,@fee_collection.id).map(&:fee_particular_id)
                    unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
                      exclude_particular_ids = exclude_particular_ids
                    else
                      exclude_particular_ids = [0]
                    end
                    if discount.finance_fee_particular_category_id == 0
                      fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                    else
                      fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id} and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                    end
                    payable_ampt = fee_particulars.map{|p| p.amount}.sum.to_f
                    discount_amt = payable_ampt * discount.discount.to_f/ (discount.is_amount?? payable_ampt : 100)

                    student_fee_ledger = StudentFeeLedger.new
                    student_fee_ledger.student_id = s.id
                    student_fee_ledger.ledger_date = Date.today
                    student_fee_ledger.ledger_title = discount.name
                    student_fee_ledger.amount_to_pay = 0.0
                    student_fee_ledger.fee_id = @fee.id
                    student_fee_ledger.particular_id = discount.id
                    student_fee_ledger.amount_paid = discount_amt
                    student_fee_ledger.save
                  end

                  if discount.is_visible == false
                    discount.destroy
                  end
                else
                  collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,fee_collection_id, discount.finance_fee_particular_category_id)
                  if collection_discount.present?
                    collection_discount.destroy
                  end
                  
                  if discount.is_visible == false
                    discount.destroy
                  end
                end
              else
                collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,fee_collection_id, discount.finance_fee_particular_category_id)
                if collection_discount.present?
                  collection_discount.destroy
                end
                
                @fee_collection = FinanceFeeCollection.find(fee_collection_id)
                @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{fee_collection_id} and is_paid=#{false} and students.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")

                s = @fee.student
                unless s.has_paid_fees
                  FinanceFee.update_student_fee(@fee_collection, s, @fee)

                  
                  exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(s.id,@fee_collection.id).map(&:fee_particular_id)
                  unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
                    exclude_particular_ids = exclude_particular_ids
                  else
                    exclude_particular_ids = [0]
                  end
                  if discount.finance_fee_particular_category_id == 0
                    fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                  else
                    fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id} and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
                  end
                  payable_ampt = fee_particulars.map{|p| p.amount}.sum.to_f
                  discount_amt = payable_ampt * discount.discount.to_f/ (discount.is_amount?? payable_ampt : 100)
                  
                  student_fee_ledger = StudentFeeLedger.new
                  student_fee_ledger.student_id = s.id
                  student_fee_ledger.ledger_date = Date.today
                  student_fee_ledger.ledger_title = discount.name
                  student_fee_ledger.amount_to_pay = 0.0
                  student_fee_ledger.fee_id = @fee.id
                  student_fee_ledger.particular_id = discount.id
                  student_fee_ledger.amount_paid = discount_amt
                  student_fee_ledger.save
                end

                if discount.is_visible == false
                  discount.destroy
                end
              end
            end
          else 
            @fee_collection = FinanceFeeCollection.find(fee_collection_id)
            
            student_exclude_discount = StudentExcludeDiscount.new
            student_exclude_discount.fee_discount_id = discount.id
            student_exclude_discount.fee_collection_id = @fee_collection.id
            student_exclude_discount.student_id = params[:student]
            student_exclude_discount.save
            
            @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{fee_collection_id} and is_paid=#{false} and students.id = #{params[:student]}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")
            s = @fee.student
            unless s.has_paid_fees
              FinanceFee.update_student_fee(@fee_collection, s, @fee)
              
              exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(s.id,@fee_collection.id).map(&:fee_particular_id)
              unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
                exclude_particular_ids = exclude_particular_ids
              else
                exclude_particular_ids = [0]
              end
              if discount.finance_fee_particular_category_id == 0
                fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
              else
                fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id} and finance_fee_particular_category_id = #{discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
              end
              payable_ampt = fee_particulars.map{|p| p.amount}.sum.to_f
              discount_amt = payable_ampt * discount.discount.to_f/ (discount.is_amount?? payable_ampt : 100)

              student_fee_ledger = StudentFeeLedger.new
              student_fee_ledger.student_id = s.id
              student_fee_ledger.ledger_date = Date.today
              student_fee_ledger.ledger_title = discount.name
              student_fee_ledger.amount_to_pay = 0.0
              student_fee_ledger.fee_id = @fee.id
              student_fee_ledger.particular_id = discount.id
              student_fee_ledger.amount_paid = discount_amt
              student_fee_ledger.save
            end
          end
        end
      else
        @discount_ids = "0"
      end
      
      @fee_discount = FeeDiscount.new
      @fee_discount.is_onetime = true
      @fee_discount.name = discount_name
      @fee_discount.finance_fee_category_id = @fee_category_id
      @fee_discount.batch_id = @batch.id
      @fee_discount.discount = params[:amount].to_f
      @fee_discount.receiver_type="Student"
      @fee_discount.receiver_id = params[:student]
      @fee_discount.finance_fee_particular_category_id = params[:discount_on].to_i
      @fee_discount.is_late = false
      @fee_discount.is_onetime = true
      @fee_discount.is_amount = true
      @fee_discount.is_visible = false
      
      @student = Student.find(params[:student])
      
      unless @fee_discount.save
        #abort(@fee_discount.errors.inspect)
        render :update do |page|
          page << 'alert("Some error occur while adding the discount, please try again later");'
          page << 'j("#remove-extra-discount-spin").removeClass("fa-spinner");'
          page << 'j("#remove-extra-discount-spin").removeClass("fa-spin");'
          page << 'j("#remove-extra-discount-spin").removeClass("fa-minus-circle");'
          page << 'j("#remove-extra-discount-spin").attr("id","remove-extra-discount");'
        end
      else
        collection_discount = CollectionDiscount.new(:fee_discount_id=>@fee_discount.id,:finance_fee_collection_id=>@fee_collection.id, :finance_fee_particular_category_id => @fee_discount.finance_fee_particular_category_id)
        collection_discount.save
                
        @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@fee_collection.id} and is_paid=#{false} and students.id = #{@fee_discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")
#abort(@fee.inspect)
        s = @fee.student
        bal = FinanceFee.check_update_student_fee(@fee_collection, s, @fee)
        if bal >= 0
          FinanceFee.update_student_fee(@fee_collection, s, @fee)
          #abort(s.inspect)
          exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(s.id,@fee_collection.id).map(&:fee_particular_id)
          unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
            exclude_particular_ids = exclude_particular_ids
          else
            exclude_particular_ids = [0]
          end
          if @fee_discount.finance_fee_particular_category_id == 0
            fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
          else
            fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@fee.batch.id} and finance_fee_particular_category_id = #{@fee_discount.finance_fee_particular_category_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==@fee.batch) }
          end
          payable_ampt = fee_particulars.map{|p| p.amount}.sum.to_f
          discount_amt = payable_ampt * @fee_discount.discount.to_f/ (@fee_discount.is_amount?? payable_ampt : 100)
          discount_amt = 0.0 if discount_amt.to_f.nan?
          #abort(discount_amt.inspect)
          student_fee_ledger = StudentFeeLedger.new
          student_fee_ledger.student_id = s.id
          student_fee_ledger.ledger_date = Date.today
          student_fee_ledger.ledger_title = @fee_discount.name
          student_fee_ledger.amount_to_pay = 0.0
          student_fee_ledger.fee_id = @fee.id
          student_fee_ledger.particular_id = @fee_discount.id
          student_fee_ledger.amount_paid = discount_amt
          student_fee_ledger.save
           
          fee_discount_collection = FeeDiscountCollection.new(
            :finance_fee_collection_id => @fee_collection.id,
            :fee_discount_id           => @fee_discount.id,
            :batch_id                  => @batch.id,
            :is_late                   => 0
          )
          fee_discount_collection.save
          
          @discount_on = params[:discount_on]
          render :update do |page|
            page.replace_html "discount_tr_id", :partial => "temp_discount"
            page << 'j("#discount_tr_id").addClass("discount_tr");'
            page << 'j("#discount_tr_id").attr("id","discount_' + @fee_discount.id.to_s + '");'
            page << 'j("#discount_tr_extra").remove();'
            page << 'resetSN();'
            page << 'calculateAmountToPay(0);'
            page << 'calculateDiscount();'
            page << 'disabled_discount_val(' + @discount_on.to_s + ', "' + @discount_ids + '");'
            page << 'hide_discount_of_onetime(' + @discount_on.to_s + ', "' + @discount_ids + '");'
          end
        else
          @fee_discount.destroy
          collection_discount.destroy
          
          render :update do |page|
            page << 'alert("Some error occur while adding the discount, Discount amount can\'t be greater than fees amount");'
            page << 'j("#remove-extra-discount-spin").removeClass("fa-spinner");'
            page << 'j("#remove-extra-discount-spin").removeClass("fa-spin");'
            page << 'j("#remove-extra-discount-spin").removeClass("fa-minus-circle");'
            page << 'j("#remove-extra-discount-spin").attr("id","remove-extra-discount");'
          end
        end
      end
    end
  end
  
  def remove_tmp_discount_from_fee
    @discount_id = params[:discount_id]
    @fee_collection_id = params[:date]

    discount = FeeDiscount.find(@discount_id)

    finance_fee_category_id = discount.finance_fee_category_id
    finance_fee_particular_category_id = discount.finance_fee_particular_category_id
    is_late = discount.is_late
    batch_id = discount.batch_id
    @batch_id = params[:batch_id]
    @receiver_id = discount.receiver_id
    
    receiver_id = discount.receiver_id
    if discount.receiver_type == "Student"
      if receiver_id.to_i == params[:student].to_i
        f = FeeDiscountCollection.find(:first, :conditions => "finance_fee_collection_id = #{@fee_collection_id} and fee_discount_id = #{@discount_id} and batch_id = #{@batch_id}")

        unless f.nil?
          f.destroy
          unless is_late
            collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
            collection_discount.destroy
            
            @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
            @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@fee_collection_id} and is_paid=#{false} and students.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")

            s = @fee.student
            unless s.has_paid_fees
              FinanceFee.update_student_fee(@fee_collection, s, @fee)
              
              student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and particular_id = #{discount.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0")
              unless student_fee_ledgers.blank?
                student_fee_ledgers.each do |student_fee_ledger|
                  student_fee_ledger.destroy
                end
              else
                bal = FinanceFee.get_student_balance(@fee_collection, s, @fee)
                student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0 and particular_id > 0")
                amt = 0
                unless student_fee_ledgers.blank?
                  student_fee_ledgers.each do |student_fee_ledger|
                    if student_fee_ledger.particular_id == discount.id
                      amt += student_fee_ledger.amount_paid.to_f
                    end
                  end
                end
                bal = bal + amt
                bal = 0 if bal < 0
                student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0 and particular_id = 0")
                unless student_fee_ledgers.blank?
                  student_fee_ledgers.each do |student_fee_ledger|
                    student_fee_ledger.update_attributes(:amount_to_pay=>bal)
                  end
                end
              end
              
            end

            if discount.is_visible == false
              discount.destroy
            end
            render :update do |page|
              page << "reload_discount(#{@discount_id});"
            end
          else
            collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
            if collection_discount.present?
              collection_discount.destroy
            end
            
            if discount.is_visible == false
              discount.destroy
            end
            render :update do |page|
              page << "reload_discount(#{@discount_id});"
            end
          end
        else
          collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
          if collection_discount.present?
            collection_discount.destroy
          end
          
          @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
          @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@fee_collection_id} and is_paid=#{false} and students.id = #{discount.receiver_id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")

          s = @fee.student
          unless s.has_paid_fees
            FinanceFee.update_student_fee(@fee_collection, s, @fee)
            
            student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and particular_id = #{discount.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0")
            unless student_fee_ledgers.blank?
                student_fee_ledgers.each do |student_fee_ledger|
                  student_fee_ledger.destroy
                end
              else
                bal = FinanceFee.get_student_balance(@fee_collection, s, @fee)
                student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0 and particular_id > 0")
                amt = 0
                unless student_fee_ledgers.blank?
                  student_fee_ledgers.each do |student_fee_ledger|
                    if student_fee_ledger.particular_id == discount.id
                      amt += student_fee_ledger.amount_paid.to_f
                    end
                  end
                end
                bal = bal + amt
                bal = 0 if bal < 0
                student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0 and particular_id = 0")
                unless student_fee_ledgers.blank?
                  student_fee_ledgers.each do |student_fee_ledger|
                    student_fee_ledger.update_attributes(:amount_to_pay=>bal)
                  end
                end
              end
          end

#          if discount.is_visible == false
#            discount.destroy
#          end
          render :update do |page|
            page << "reload_discount(#{@discount_id});"
          end
        end
      else
        render :update do |page|
          page << "alert('An error occur while removing the discount please try again later');"
        end
      end
    else
      @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
      
      student_exclude_discount = StudentExcludeDiscount.new
      student_exclude_discount.fee_discount_id = discount.id
      student_exclude_discount.fee_collection_id = @fee_collection.id
      student_exclude_discount.student_id = params[:student]
      student_exclude_discount.save

      @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@fee_collection_id} and is_paid=#{false} and students.id = #{params[:student]}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")
      s = @fee.student
      unless s.has_paid_fees
        FinanceFee.update_student_fee(@fee_collection, s, @fee)
        
        student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and particular_id = #{discount.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0")
        unless student_fee_ledgers.blank?
          student_fee_ledgers.each do |student_fee_ledger|
            student_fee_ledger.destroy
          end
        else
          bal = FinanceFee.get_student_balance(@fee_collection, s, @fee)
          student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay = 0.00 and amount_paid > 0.00 and transaction_id = 0 and particular_id > 0")
          amt = 0
          unless student_fee_ledgers.blank?
            student_fee_ledgers.each do |student_fee_ledger|
              if student_fee_ledger.particular_id == discount.id
                amt += student_fee_ledger.amount_paid.to_f
              end
            end
          end
          bal = bal + amt
          bal = 0 if bal < 0
          student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{s.id} and fee_id = #{@fee.id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0 and particular_id = 0")
          unless student_fee_ledgers.blank?
            student_fee_ledgers.each do |student_fee_ledger|
              student_fee_ledger.update_attributes(:amount_to_pay=>bal)
            end
          end
        end
      end
      render :update do |page|
        page << "reload_discount(#{@discount_id});"
      end
    end
  end
  
  def remove_tmp_particular_from_fee
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      @batch   = Batch.find(params[:batch_id])
      @date    =  @fee_collection = FinanceFeeCollection.find(params[:date])
      
      @student = Student.find(params[:student])
      @particular_id = params[:particular_id]
      
      @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{@student.id}'")
      
      @particular = @batch.finance_fee_particulars.find(@particular_id)
      @particular_name = ""
      unless @particular.nil?
        @particular_name = @particular.name
        @collection_particulars = CollectionParticular.find_by_finance_fee_collection_id_and_finance_fee_particular_id(@date.id, @particular.id)
        @particular.delete
        @collection_particulars.delete
      end
      
      FinanceFee.new_student_fee_with_tmp_particular(@date,@student)
      fee = FinanceFee.find_by_student_id_and_fee_collection_id_and_batch_id_and_is_paid(@student.id, @date.id, @student.batch_id, false)
      unless fee.blank?
        student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{@student.id} and fee_id = #{fee.id} and particular_id = #{@particular_id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0")
        unless student_fee_ledgers.blank?
          student_fee_ledgers.each do |student_fee_ledger|
            student_fee_ledger.destroy
          end
        else
          bal = FinanceFee.get_student_balance(@date, @student, fee)
          student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{@student.id} and fee_id = #{fee.id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0 and particular_id > 0")
          amt = 0
          unless student_fee_ledgers.blank?
            student_fee_ledgers.each do |student_fee_ledger|
              amt += student_fee_ledger.amount_to_pay.to_f
            end
          end
          bal = bal - amt
          bal = 0 if bal < 0
          student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{@student.id} and fee_id = #{fee.id} and amount_to_pay > 0.00 and amount_paid = 0.00 and transaction_id = 0 and particular_id = 0")
          unless student_fee_ledgers.blank?
            student_fee_ledgers.each do |student_fee_ledger|
              student_fee_ledger.update_attributes(:amount_to_pay=>bal)
            end
          end
        end
        #
      end
      
      if @particular_name.downcase == "vat"
        render :update do |page|
          page.replace_html "vat_div", :partial => "vat_submission"
          page << 'j("#vat_as_particular_blank_space_1").remove();'
          page << 'j("#vat_as_particular_blank_space_2").remove();'
          page << 'j("#vat_as_particular_blank_space_3").remove();'
          page << 'j("#vat_as_particular_blank_space_4").remove();'
          page << 'j("#vat_as_particular").remove();'
          page << 'resetSN();'
          page << 'calculateAmountToPay(0);'
          page << 'calculateDiscount();'
        end
      else
        render :update do |page|
          page << 'j("#particular_' + @particular_id.to_s + '").remove();'
          page << 'resetSN();'
          page << 'calculateAmountToPay(0);'
          page << 'calculateDiscount();'
        end
      end
      
    end
  end
  
  def fee_collection_dues
    @batches=Batch.active
  end
  
  def fee_dues_create
    @error = false
    unless params[:fee_dues].nil? or params[:fee_dues].empty? or params[:fee_dues].blank?
      unless params[:fee_collection][:students].nil? or params[:fee_collection][:students].empty? or params[:fee_collection][:students].blank?
        dues = params[:fee_dues][:dues]
        student_ids = params[:fee_collection][:students]
        student_ids.each do |si|
          s = Student.find(si)
          s.update_attributes(:has_dues=>true,:previous_dues=>dues.to_f)
        end
        flash[:notice] = "Student Dues Added Successfully"
        redirect_to :controller => "finance", :action => "fee_collection_dues"
      else
        flash[:notice] = "No Student found to save"
      end
    else
      flash[:notice] = "Invalid Form Submit"
    end
  end
  
  def load_fees_submission_batch
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      if params[:student]
        fees = FinanceFee.first(:conditions=>"fee_collection_id = #{params[:date]} and student_id = #{params[:student]}" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')  
      end
#      unless params[:batch_id].blank?
#      else
#      end
      unless fees.nil?
        batch_id = fees.batch_id
      else
        batch_id = params[:batch_id]
      end
      #abort(batch_id.to_s)
      @batch   = Batch.find(batch_id)
      @date    =  @fee_collection = FinanceFeeCollection.find(params[:date])
      
      student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
      
      @dates   = @batch.finance_fee_collections
      
      @from_batch_fee = true
      unless params[:student_fees].nil?
        if params[:student_fees].to_i == 1
          @from_batch_fee = false
        end
      end
      
      @show_only_structure = false
      unless params[:student_fees_struct].nil?
        if params[:student_fees_struct].to_i == 1
          @show_only_structure = true
        end
      end

      if params[:student]
        @student = Student.find(params[:student])
        @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = '#{@student.id}'")
      else
        @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id} and FIND_IN_SET(students.id,'#{ student_ids}')" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
      end
      
      unless @fee.nil?
        advance_fee_collection = false
        @self_advance_fee = false
        @fee_has_advance_particular = false
        
        @student ||= @fee.student
        @prev_student = @student.previous_fee_student(@date.id,student_ids)
        @next_student = @student.next_fee_student(@date.id,student_ids)
        @financefee = @student.finance_fee_by_date @date
        if @financefee.has_advance_fee_id
          if @date.is_advance_fee_collection
            @self_advance_fee = true
            advance_fee_collection = true
          end
          @fee_has_advance_particular = true
          @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
          @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
          if @advance_ids.blank?
            @advance_ids[0] = 0
          end
          @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
        end
        @due_date = @fee_collection.due_date
        @paid_fees = @fee.finance_transactions
        #abort(@paid_fees.map(&:id).inspect)
        @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])

        exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
        unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
          exclude_particular_ids = exclude_particular_ids
        else
          exclude_particular_ids = [0]
        end
          
        if advance_fee_collection
          fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
          fee_collection_advances_particular = fee_collection_advances_particular.reject { |a| a.to_s.empty? }
          if fee_collection_advances_particular.blank?
            fee_collection_advances_particular[0] = 0
          end
          
          if fee_collection_advances_particular.include?(0)
            @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
          else
            @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
          end
        else
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
        end
        
        if advance_fee_collection
          month = 1
          payable = 0
          @fee_collection_advances.each do |fee_collection_advance|
            @fee_particulars.each do |particular|
              if fee_collection_advance.particular_id == particular.finance_fee_particular_category_id
                payable += particular.amount * fee_collection_advance.no_of_month.to_i
              else
                payable += particular.amount
              end
            end
          end
          @total_payable=payable.to_f
        else  
          @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
        end
        
        @total_discount = 0
        
        @adv_fee_discount = false
        @actual_discount = 1
        
        if advance_fee_collection
          calculate_discount(@date, @fee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
        else
          if @fee_has_advance_particular
            calculate_discount(@date, @fee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
          else
            calculate_discount(@date, @fee.batch, @student, false, nil, @fee_has_advance_particular)
          end
        end
        
        bal=(@total_payable-@total_discount).to_f
        unless params[:submission_date].nil? or params[:submission_date].empty? or params[:submission_date].blank?
          require 'date'
          @submission_date = Date.parse(params[:submission_date])
          days=(Date.parse(params[:submission_date])-@date.due_date.to_date).to_i
        else
          @submission_date = Date.today
          if @financefee.is_paid
            @paid_fees = @financefee.finance_transactions
            unless @paid_fees.blank?
              days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
            else
              days=(Date.today-@date.due_date.to_date).to_i
            end
          else
            days=(Date.today-@date.due_date.to_date).to_i
          end
        end
        
        fine_enabled = true
        student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
        unless student_fee_configuration.blank?
          if student_fee_configuration.config_value.to_i == 1
            fine_enabled = true
          else
            fine_enabled = false
          end
        end
        
        if @tmp_paid_fees.blank?
          @tmp_paid_fees = @financefee.finance_transactions
        end
        #abort(@tmp_paid_fees.inspect)
        unless @tmp_paid_fees.blank?
          @tmp_paid_fees.each do |paid_fee|
            transaction_id = paid_fee.id
            online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
            unless online_payments.blank?
              fine_enabled = false
            end
          end
        end
        
        auto_fine=@date.fine
        
        @has_fine_discount = false
        if days > 0 and auto_fine and fine_enabled #and @financefee.is_paid == false
          @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
          @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
          
          calculate_extra_fine(@date, @batch, @student, @fine_rule)
          
          @new_fine_amount = @fine_amount
          get_fine_discount(@date, @batch, @student)
          #abort(@new_fine_amount.to_s)
          if @fine_amount < 0
            @fine_amount = 0
          end
        end
        #abort(@fine_amount.to_s)
        @fine_amount=0 if @financefee.is_paid
        
        unless advance_fee_collection
          if @total_discount == 0
            @adv_fee_discount = true
            @actual_discount = 0
            calculate_discount(@date, @fee.batch, @student, false, nil, @fee_has_advance_particular)
          end
        end

        fee_particulars_categories = @fee_particulars.map(&:finance_fee_particular_category_id)
        # abort(fee_particulars_categories.inspect)
        @finance_fee_particular_categories_all = FinanceFeeParticularCategory.active

        @finance_fee_particular_categories = @finance_fee_particular_categories_all.select{|fp| !fee_particulars_categories.include?(fp.id) }
        
        #Enable Duplicate Particular
        #@finance_fee_particular_categories = []
        @finance_fee_particular_categories = @finance_fee_particular_categories_all

        unless params[:option].nil?
          render :update do |page|
            page << "#{@finance_fee_particular_categories.map.to_json}"
          end
        else
          render :update do |page|
            page.replace_html "student", :partial => "student_fees_submission"
            page << "loadJS();"
            page << 'j(".select2-combo").select2();'
          end
        end


      else
        render :update do |page|
          page.replace_html "student", :text => '<p class="flash-msg">No students have been assigned this fee.</p>'
        end
      end
    else
      render :update do |page|
        page.replace_html "student", :text => ''
      end
    end
  end

  def fee_particulars_ajax
    fee_particulars_categories = @fee_particulars.map(&:finance_fee_particular_category_id)
    @finance_fee_particular_categories_all = FinanceFeeParticularCategory.active
    @finance_fee_particular_categories = @finance_fee_particular_categories_all.select{|fp| !fee_particulars_categories.include?(fp.id) }
  end

  def update_ajax
    advance_fee_collection = false
    @self_advance_fee = false
    @fee_has_advance_particular = false
        
    @batch   = Batch.find(params[:batch_id])
    @date = @fee_collection = FinanceFeeCollection.find(params[:date])
    student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
    @dates = @batch.finance_fee_collections
    @student = Student.find(params[:student]) if params[:student]
    @student ||= FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}",:joins=>'INNER JOIN students ON finance_fees.student_id = students.id').student
    @prev_student = @student.previous_fee_student(@date.id,student_ids)
    @next_student = @student.next_fee_student(@date.id,student_ids)
    @due_date = @fee_collection.due_date
    total_fees =0

    @financefee = @student.finance_fee_by_date @date

    @show_only_structure = false
    @from_batch_fee = false
    unless params[:from_batch].nil?
      if params[:from_batch]
        @from_batch_fee = true
      end
    end
    
    if @financefee.has_advance_fee_id
      if @date.is_advance_fee_collection
        @self_advance_fee = true
        advance_fee_collection = true
      end
      @fee_has_advance_particular = true
      @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
      @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
      if @advance_ids.blank?
        @advance_ids[0] = 0
      end
      @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
    end
    
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    
    exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
    unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
      exclude_particular_ids = exclude_particular_ids
    else
      exclude_particular_ids = [0]
    end
    
    if advance_fee_collection
      fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
      fee_collection_advances_particular = fee_collection_advances_particular.reject { |a| a.to_s.empty? }
      if fee_collection_advances_particular.blank?
        fee_collection_advances_particular[0] = 0
      end
      
      if fee_collection_advances_particular.include?(0)
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      end
    else
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
    end
    
    if advance_fee_collection
      month = 1
      payable = 0
      @fee_collection_advances.each do |fee_collection_advance|
        @fee_particulars.each do |particular|
          if fee_collection_advance.particular_id == particular.finance_fee_particular_category_id
            payable += particular.amount * fee_collection_advance.no_of_month.to_i
          else
            payable += particular.amount
          end
        end
      end
      @total_payable=payable.to_f
    else  
      @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
    end
    
    @total_discount = 0
    
    if advance_fee_collection
      calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
    else
      if @fee_has_advance_particular
        calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
      else
        calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
      end
    end
    
    bal=(@total_payable-@total_discount).to_f
    unless params[:submission_date].nil? or params[:submission_date].empty? or params[:submission_date].blank?
      require 'date'
      @submission_date = Date.parse(params[:submission_date])
      days=(Date.parse(params[:submission_date])-@date.due_date.to_date).to_i
    else
      @submission_date = Date.today
      if @financefee.is_paid
        @paid_fees = @financefee.finance_transactions
        days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
      else
        days=(Date.today-@date.due_date.to_date).to_i
      end
    end

    fine_enabled = true
    student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
    unless student_fee_configuration.blank?
      if student_fee_configuration.config_value.to_i == 1
        fine_enabled = true
      else
        fine_enabled = false
      end
    end
    
    if @tmp_paid_fees.blank?
      @tmp_paid_fees = @financefee.finance_transactions
    end
    
    unless @tmp_paid_fees.blank?
      @tmp_paid_fees.each do |paid_fee|
        transaction_id = paid_fee.id
        online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
        unless online_payments.blank?
          fine_enabled = false
        end
      end
    end
    
    auto_fine=@date.fine

    @has_fine_discount = false
    if days > 0 and auto_fine and fine_enabled #and @financefee.is_paid == false
      @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
      @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule

      calculate_extra_fine(@date, @batch, @student, @fine_rule)

      @new_fine_amount = @fine_amount
      get_fine_discount(@date, @batch, @student)
      if @fine_amount < 0
        @fine_amount = 0
      end
    end
    
    total_fees =@financefee.balance.to_f+params[:fine_amount_to_pay].to_f
    unless params[:fine].nil?
      unless @financefee.is_paid == true
        total_fees += params[:fine].to_f
      else
        total_fees = params[:fine].to_f
      end
    end
    
    unless params[:fee_vat].nil?
      if params[:fee_vat] == 'on'
        particularNames = @fee_particulars.map{|fee| fee.name.downcase}
        vat_as_particular = false
        if particularNames.include?('vat')
          vat_as_particular = true
        end
        if vat_as_particular == false
          total_fees += params[:fee_vat_amount].to_f
        end
      end
    end
    
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id_and_fee_collection_id(@student.id, @financefee.fee_collection_id).map(&:fee_discount_id)
    exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    discount_on_total_fee = false
    one_time_discounts_on_particulars = @date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
    onetime_discounts = @date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
    if onetime_discounts.length > 0
      discount_on_total_fee = true
    end
    @paid_fees = @financefee.finance_transactions
    particular_remaining_amount = 0
    tot_payable = 0
    @fee_particulars.each do |fp|
      if fp.name.downcase != "vat"
        paidAmount = 0
        payable_ampt = fp.amount.to_f
        particular_amount = fp.amount.to_f
        tot_payable += fp.amount.to_f
        transaction_ids = @paid_fees.map(&:id)
        unless transaction_ids.blank?
          transaction_ids = transaction_ids.reject { |t| t.to_s.empty? }
          if transaction_ids.blank?
            transaction_ids[0] = 0
          end
          paidFess = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Particular' AND transaction_type = 'Fee Collection' AND finance_transaction_id IN (" + transaction_ids.join(",") + ") AND particular_id = #{fp.id}")
          unless paidFess.blank?
            paidAmount += paidFess.map(&:amount).sum.to_f
          end
        end
        particular_amount = particular_amount - paidAmount.to_f
        unless params["fee_particular_" + fp.id.to_s].nil?
          if params["fee_particular_" + fp.id.to_s] == "on"
            paid_amount = params["fee_particular_amount_" + fp.id.to_s].to_f
            left_amount = particular_amount - paid_amount
            if  left_amount > 0
              particular_remaining_amount += left_amount
            end
          else
            particular_remaining_amount += particular_amount
          end
        else
          particular_remaining_amount += particular_amount
        end
        if discount_on_total_fee == false
          particular_category_id = fp.finance_fee_particular_category_id 
          exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
          if exclude_discount_ids.blank?
            exclude_discount_ids[0] = 0
          end
          onetime_discounts = @date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{@batch.id}").select{|par|  ((par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch))  }
          if onetime_discounts.length > 0
            onetime_discounts.each do |d|   
              paidAmount = 0
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              transaction_ids = @paid_fees.map(&:id)
              unless transaction_ids.blank?
                transaction_ids = transaction_ids.reject { |t| t.to_s.empty? }
                if transaction_ids.blank?
                  transaction_ids[0] = 0
                end
                paidFess = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Adjustment' AND transaction_type = 'Discount' AND finance_transaction_id IN (" + transaction_ids.join(",") + ") AND particular_id = #{d.id}")
                unless paidFess.blank?
                  paidAmount += paidFess.map(&:amount).sum.to_f
                end
              end
              if paidAmount  == 0
                unless params["fee_discount_" + d.id.to_s].nil?
                  if params["fee_discount_" + d.id.to_s] == "on"
                    paid_discount_amount = params["fee_discount_amount_" + d.id.to_s].to_f
                    remaining_amt = discount_amt - paid_discount_amount
                    if remaining_amt < particular_amount
                      particular_remaining_amount -= remaining_amt
                    end
                  else
                    if discount_amt < particular_amount
                      particular_remaining_amount -= discount_amt
                    end
                  end
                else
                  if discount_amt < particular_amount
                    particular_remaining_amount -= discount_amt
                  end
                end
              else
                discount_amt = discount_amt - paidAmount
                unless params["fee_discount_" + d.id.to_s].nil?
                  if params["fee_discount_" + d.id.to_s] == "on"
                    paid_discount_amount = params["fee_discount_amount_" + d.id.to_s].to_f
                    remaining_amt = discount_amt - paid_discount_amount
                    if remaining_amt < particular_amount
                      particular_remaining_amount -= remaining_amt
                    end
                  else
                    if discount_amt < particular_amount
                      particular_remaining_amount -= discount_amt
                    end
                  end
                else
                  if discount_amt < particular_amount
                    particular_remaining_amount -= discount_amt
                  end
                end
              end
            end
          end
        end
      else
        payable_ampt = fp.amount.to_f
        unless params[:fee_vat].nil?
          if params[:fee_vat] == 'on'
            if params[:fee_vat_amount].to_f > payable_ampt
              fp.amount = params[:fee_vat_amount].to_f
              fp.save
            end
          end
        end
      end
    end
    
    if discount_on_total_fee
      total_discount = 0
      onetime_discounts.each do |d|
        tmp_discount = tot_payable * d.discount.to_f/ (d.is_amount?? tot_payable : 100)
        total_discount = total_discount + tmp_discount
      end
      paidAmount = 0
      transaction_ids = @paid_fees.map(&:id)
      unless transaction_ids.blank?
        transaction_ids = transaction_ids.reject { |t| t.to_s.empty? }
        if transaction_ids.blank?
          transaction_ids[0] = 0
        end
        paidFess = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Adjustment' AND transaction_type = 'Discount' AND finance_transaction_id IN (" + transaction_ids.join(",") + ") AND particular_id = 0")
        unless paidFess.blank?
          paidAmount += paidFess.map(&:amount).sum.to_f
        end
      end
      total_discount = total_discount - paidAmount
      unless params["fee_discount_0"].nil?
        if params["fee_discount_0"] == "on"
          paid_discount_amount = params["fee_discount_amount_0"].to_f
          total_discount = total_discount - paid_discount_amount
        end
      end
      if total_discount > particular_remaining_amount
        particular_remaining_amount -= total_discount
      end
    end
    
    unless params[:fee_vat].nil?
      if params[:fee_vat] != 'on' and vat_as_particular
        particular_remaining_amount += params[:fee_vat_amount].to_f
      end
    else
      if vat_as_particular
        particular_remaining_amount += params[:fee_vat_amount].to_f
      end
    end
    
    unless params[:tot_fee_amount].to_f <= 0
      unless params[:fees][:payment_mode].blank?
        
        #unless Champs21Precision.set_and_modify_precision(params[:tot_fee_amount]).to_f > Champs21Precision.set_and_modify_precision(total_fees).to_f
        transaction = FinanceTransaction.new
        (@financefee.balance.to_f > params[:tot_fee_amount].to_f ) ? transaction.title = "#{t('receipt_no')}. (#{t('partial')}) F#{@financefee.id}" :  transaction.title = "#{t('receipt_no')}. F#{@financefee.id}"
        transaction.category = FinanceTransactionCategory.find_by_name("Fee")
        transaction.payee = @student
        transaction.amount = params[:tot_fee_amount].to_f
          
        unless params[:fee_fine].nil?
          if params[:fee_fine] == 'on'
            transaction.fine_amount = params[:fine_amount_to_pay].to_f
            transaction.fine_included = true  unless params[:fine_amount_to_pay].nil? 
          else
            transaction.fine_amount = 0
            transaction.fine_included = false
          end
        else
          transaction.fine_amount = 0
          transaction.fine_included = false
        end
          
        remaining_amount = total_fees.to_f - params[:tot_fee_amount].to_f
        if remaining_amount < 0
          remaining_amount = 0
        end
        
        unless params[:fee_vat].nil?
          if params[:fee_vat] == 'on'
            transaction.vat_amount = params[:fee_vat_amount].to_f
            transaction.vat_included = true  unless params[:fee_vat_amount].nil?
          else
            transaction.vat_amount = 0
            transaction.vat_included = false
          end
        else
          transaction.vat_amount = 0
          transaction.vat_included = false
        end
         
        transaction.finance_balance = particular_remaining_amount
          
        total_fine_amount = 0
          
        transaction.finance = @financefee
        transaction.payment_mode = params[:fees][:payment_mode]
        transaction.transaction_date = params[:fees][:transaction_date]
        if transaction.save
          is_paid = remaining_amount<=0 ? true : false
          @financefee.update_attributes( :is_paid=>is_paid)

          @paid_fees = @financefee.finance_transactions
            
          @fee_particulars.each do |fp|
            advanced = false
            particular_amount = fp.amount.to_f
            unless params["fee_particular_" + fp.id.to_s].nil?
              if params["fee_particular_" + fp.id.to_s] == "on"
                paid_amount = params["fee_particular_amount_" + fp.id.to_s].to_f
                left_amount = particular_amount - paid_amount
                amount_paid = 0
                if  left_amount == 0
                  amount_paid = particular_amount
                elsif  left_amount < 0
                  advanced = true
                  amount_paid = particular_amount
                elsif left_amount > 0
                  amount_paid = paid_amount
                end
                finance_transaction_particular = FinanceTransactionParticular.new
                finance_transaction_particular.finance_transaction_id = transaction.id
                finance_transaction_particular.particular_id = fp.id
                finance_transaction_particular.particular_type = 'Particular'
                finance_transaction_particular.transaction_type = 'Fee Collection'
                finance_transaction_particular.amount = amount_paid
                finance_transaction_particular.transaction_date = transaction.transaction_date
                finance_transaction_particular.save
                  
                if advanced
                  left_amount = paid_amount - particular_amount
                  finance_transaction_particular = FinanceTransactionParticular.new
                  finance_transaction_particular.finance_transaction_id = transaction.id
                  finance_transaction_particular.particular_id = fp.id
                  finance_transaction_particular.particular_type = 'Particular'
                  finance_transaction_particular.transaction_type = 'Advance'
                  finance_transaction_particular.amount = left_amount
                  finance_transaction_particular.transaction_date = transaction.transaction_date
                  finance_transaction_particular.save
                end
              end
            end
          end
            
          unless @onetime_discounts.blank?
            @onetime_discounts.each do |od|
              unless params["fee_discount_" + od.id.to_s].nil?
                if params["fee_discount_" + od.id.to_s] == "on"
                  discount_amount = params["fee_discount_amount_" + od.id.to_s].to_f
                  finance_transaction_particular = FinanceTransactionParticular.new
                  finance_transaction_particular.finance_transaction_id = transaction.id
                  finance_transaction_particular.particular_id = od.id
                  finance_transaction_particular.particular_type = 'Adjustment'
                  finance_transaction_particular.transaction_type = 'Discount'
                  finance_transaction_particular.amount = discount_amount
                  finance_transaction_particular.transaction_date = transaction.transaction_date
                  finance_transaction_particular.save
                end
              end
            end
          end
            
          unless @discounts.blank?
            @discounts.each do |od|
              unless params["fee_discount_" + od.id.to_s].nil?
                if params["fee_discount_" + od.id.to_s] == "on"
                  discount_amount = params["fee_discount_amount_" + od.id.to_s].to_f
                  finance_transaction_particular = FinanceTransactionParticular.new
                  finance_transaction_particular.finance_transaction_id = transaction.id
                  finance_transaction_particular.particular_id = od.id
                  finance_transaction_particular.particular_type = 'Adjustment'
                  finance_transaction_particular.transaction_type = 'Discount'
                  finance_transaction_particular.amount = discount_amount
                  finance_transaction_particular.transaction_date = transaction.transaction_date
                  finance_transaction_particular.save
                end
              end
            end
          end
            
          unless params[:fee_vat].nil?
            if params[:fee_vat] == "on"
              vat_amount = params[:fee_vat_amount].to_f
              finance_transaction_particular = FinanceTransactionParticular.new
              finance_transaction_particular.finance_transaction_id = transaction.id
              finance_transaction_particular.particular_id = 0
              finance_transaction_particular.particular_type = 'VAT'
              finance_transaction_particular.transaction_type = ''
              finance_transaction_particular.amount = vat_amount
              finance_transaction_particular.transaction_date = transaction.transaction_date
              finance_transaction_particular.save
            end
          end
            
          unless params[:fee_fine].nil?
            if params[:fee_fine] == "on"
              fine_amount = params[:fine_amount_to_pay].to_f
              finance_transaction_particular = FinanceTransactionParticular.new
              finance_transaction_particular.finance_transaction_id = transaction.id
              finance_transaction_particular.particular_id = 0
              finance_transaction_particular.particular_type = 'Fine'
              finance_transaction_particular.transaction_type = ''
              finance_transaction_particular.amount = fine_amount
              finance_transaction_particular.transaction_date = transaction.transaction_date
              finance_transaction_particular.save
              
              transaction_date = transaction.transaction_date
              student_fee_ledger = StudentFeeLedger.new
              student_fee_ledger.student_id = @student.id
              student_fee_ledger.ledger_title = "Fine On " + transaction_date.strftime("%d %B, %Y")
              student_fee_ledger.ledger_date = transaction_date.strftime("%Y-%m-%d")
              student_fee_ledger.amount_to_pay = fine_amount.to_f
              student_fee_ledger.transaction_id = transaction.id
              student_fee_ledger.fee_id = @financefee.id
              student_fee_ledger.is_fine = 1
              student_fee_ledger.save
            end
          end
            
          if @has_fine_discount
            @discounts_on_lates.each do |fd|
              unless params["fee_fine_discount_" + fd.id.to_s].nil?
                if params["fee_fine_discount_" + fd.id.to_s] == "on"
                  discount_amount = params["fee_fine_discount_amount_" + fd.id.to_s].to_f
                  finance_transaction_particular = FinanceTransactionParticular.new
                  finance_transaction_particular.finance_transaction_id = transaction.id
                  finance_transaction_particular.particular_id = fd.id
                  finance_transaction_particular.particular_type = 'FineAdjustment'
                  finance_transaction_particular.transaction_type = 'Discount'
                  finance_transaction_particular.amount = discount_amount
                  finance_transaction_particular.transaction_date = transaction.transaction_date
                  finance_transaction_particular.save
                end
              end
            end
          end
        end
        
        transaction_id = transaction.id
        particular_amount = 0.00
        particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction_id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Fee Collection'"], :group => "finance_transaction_particulars.finance_transaction_id")
        particular_wise_transactions.each do |pt|
          particular_amount += pt.amount.to_f
        end

        particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction_id} and finance_transaction_particulars.particular_type = 'Particular' and finance_transaction_particulars.transaction_type = 'Advance'"], :group => "finance_transaction_particulars.finance_transaction_id")
        particular_wise_transactions.each do |pt|
          particular_amount += pt.amount.to_f
        end

        particular_wise_transactions = FinanceTransactionParticular.find(:all, :select => "sum( finance_transaction_particulars.amount ) as amount", :conditions => ["finance_transaction_particulars.finance_transaction_id = #{transaction_id} and finance_transaction_particulars.particular_type = 'Adjustment' and finance_transaction_particulars.transaction_type = 'Discount'"], :group => "finance_transaction_particulars.finance_transaction_id")
        particular_wise_transactions.each do |pt|
          particular_amount -= pt.amount.to_f
        end

        if particular_amount.to_f != transaction.amount.to_f
          finance_notmatch_transaction = FinanceNotmatchTransaction.new
          finance_notmatch_transaction.transaction_id = transaction_id
          finance_notmatch_transaction.run_from = "FinanceController - UpdateAjax"
          finance_notmatch_transaction.save
        end
        #else
        #  @paid_fees = @financefee.finance_transactions
        #  @financefee.errors.add_to_base("#{t('flash19')}")
        #end
      else
        @paid_fees = @financefee.finance_transactions
        @financefee.errors.add_to_base("#{t('select_one_payment_mode')}")
      end
    else
      @paid_fees = @financefee.finance_transactions
      @financefee.errors.add_to_base("#{t('flash23')}")
    end
    
    @fine_amount=0 if @financefee.is_paid
    
    @financefee = @student.finance_fee_by_date @date
    @paid_fees = @financefee.finance_transactions
    unless params[:from_admin].nil? or params[:from_admin].blank? or params[:from_admin].empty?
      if params[:from_admin].to_i == 1
        render :update do |page|
          page.replace_html "student", :partial => "student_fees_submission"
          page << "loadJS();"
          page << 'j(".select2-combo").select2();'
        end
      else
        render :update do |page|
          page.replace_html 'student_fee_details', :partial => "student_fee_details"
        end
      end
    else
      render :update do |page|
        page.replace_html "student", :partial => "student_fees_submission"
        page << "loadJS();"
        page << 'j(".select2-combo").select2();'
      end
    end
    

  end
  
  def student_fee_receipt_all_pdf
    #    online_payments = Payment.all
    #    s = []
    #    p = []
    #    online_payments.each do |op|
    #      trans_id = op.finance_transaction_id
    #      unless trans_id.nil?
    #        trans = FinanceTransaction.find(:first, :conditions => "id=#{trans_id}")
    #        unless trans.nil?
    #        else
    #          p << op.id
    #        end
    #      else
    #        p << op.id
    #      end
    #    end
    #    abort(p.inspect)
    @batch=Batch.find(params[:batch_id])
    @students = @batch.students 
    @date = @fee_collection = FinanceFeeCollection.find(params[:id])
    @due_date = @fee_collection.due_date
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])
    
    @all_financefee = []
    @all_paid_fees = []
    @all_fee_particulars = []
    @all_total_discount = []
    @all_total_payable = []
    @all_fine_rule = []
    @all_fine_amount = []
    @all_has_fine_discount = []
    @all_onetime_discounts = []
    @all_onetime_discounts_amount = []
    @all_discounts_amount = []
    @all_new_fine_amount = []
    @all_discounts_on_lates = []
    @all_discounts_late_amount = []
    @all_discounts = []
    
    @students.each do |student|
      @student = student
      @iloop = student.id
      f_report = @student.finance_fee_by_date @date 
      unless f_report.blank?
        advance_fee_collection = false
        @self_advance_fee = false
        @fee_has_advance_particular = false
        
        @all_financefee[student.id] = f_report 
        
        if @all_financefee[student.id].advance_fee_id.to_i > 0
          if @date.is_advance_fee_collection
            @self_advance_fee = true
            advance_fee_collection = true
          end
          
          @fee_has_advance_particular = true
          @advance_id = @all_financefee[student.id].advance_fee_id
          @fee_collection_advances = FinanceFeeAdvance.find(@advance_id)
        end
      
        @all_paid_fees[student.id] = @all_financefee[student.id].finance_transactions
        
        exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
        unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
          exclude_particular_ids = exclude_particular_ids
        else
          exclude_particular_ids = [0]
        end
        
        if advance_fee_collection
          
          if @fee_collection_advances.particular_id == 0
            @all_fee_particulars[student.id] = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch)}
          else
            @all_fee_particulars[student.id] = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@batch.id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch)}
          end
        else
          @all_fee_particulars[student.id] = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch)}
        end
    
        @all_total_discount[student.id] = 0
        
        if advance_fee_collection
          @all_total_payable[student.id] = (@all_fee_particulars[student.id].map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
        else  
          @all_total_payable[student.id] = @all_fee_particulars[student.id].map{|s| s.amount}.sum.to_f
        end
        
        if advance_fee_collection
          calculate_discount_index_all(@date, @all_financefee[@student.id].batch, @student,@student.id, true, @fee_collection_advances, @fee_has_advance_particular)
        else
          if @fee_has_advance_particular
            calculate_discount_index_all(@date, @all_financefee[@student.id].batch, @student,@student.id, false, @fee_collection_advances, @fee_has_advance_particular)
          else
            calculate_discount_index_all(@date, @all_financefee[@student.id].batch, @student,@student.id, false, nil, @fee_has_advance_particular)
          end
        end
        
        bal=(@all_total_payable[@iloop]-@all_total_discount[@iloop]).to_f
        days=(Date.today-@date.due_date.to_date).to_i
        
        fine_enabled = true
        student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
        unless student_fee_configuration.blank?
          if student_fee_configuration.config_value.to_i == 1
            fine_enabled = true
          else
            fine_enabled = false
          end
        end
        
        unless @tmp_paid_fees.blank?
          @tmp_paid_fees = @all_financefee[@iloop].finance_transactions
        end
        
        unless @tmp_paid_fees.blank?
          @tmp_paid_fees.each do |paid_fee|
            transaction_id = paid_fee.id
            online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
            unless online_payments.blank?
              fine_enabled = false
            end
          end
        end
        
        auto_fine=@date.fine
        
        @all_has_fine_discount[@iloop] = false
        if days > 0 and auto_fine and @all_financefee[@iloop].is_paid == false and fine_enabled

          @all_fine_rule[@iloop]=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
          @all_fine_amount[@iloop]=@all_fine_rule[@iloop].is_amount ? @all_fine_rule[@iloop].fine_amount : (bal*@all_fine_rule[@iloop].fine_amount)/100 if @all_fine_rule[@iloop]
          calculate_extra_fine_index_all(@date, @all_financefee[@iloop].batch, @student, @all_fine_rule[@iloop],@iloop)
          @all_new_fine_amount[@iloop] = @all_fine_amount[@iloop]
          get_fine_discount_index_all(@date, @all_financefee[@iloop].batch, @student,@iloop)
          if @all_fine_amount[@iloop] < 0
            @all_fine_amount[@iloop] = 0
          end
        end

        @all_fine_amount[@iloop]=0 if @all_financefee[@iloop].is_paid
      end
    end

    if MultiSchool.current_school.id == 312
      render :pdf => 'student_fee_receipt_all_pdf',
        :orientation => 'Landscape', :zoom => 1.00,
        :page_size => 'Legal',
        :margin => {    :top=> 10,
        :bottom => 0,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    elsif  MultiSchool.current_school.id == 3
      render :pdf => 'student_fee_receipt_all_pdf',
        :orientation => 'Portrait', :zoom => 1.00,
        :page_size => 'A4',
        :margin => {    :top=> 10,
        :bottom => 0,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
   elsif  MultiSchool.current_school.id == 352
        unless params[:admission].blank?
          render :pdf => 'student_fee_receipt_all_pdf',
            :orientation => 'Landscape', :zoom => 1.00,
            :page_size => 'Legal',
            :margin => {    :top=> 5,
            :bottom => 0,
            :left=> 0,
            :right => 0},
            :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
            :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}  
        else
          render :pdf => 'student_fee_receipt_all_pdf',
            :orientation => 'Portrait', :zoom => 1.00,
            :page_size => 'A4',
            :margin => {:top=> 10,
            :bottom => 0,
            :left=> 10,
            :right => 10},
            :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
            :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
        end
    else 
      render :pdf => 'student_fee_receipt_all_pdf',
        :orientation => 'Landscape', :zoom => 1.00,
        :page_size => 'A4',
        :margin => {    :top=> 10,
        :bottom => 0,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    end

  end

  def student_fee_receipt_pdf
    @dates_array = params[:id2].split("|")
    @student = Student.find(params[:id])
    unless params[:batch_id].nil?
      @batch=Batch.find(params[:batch_id])
    else
      @batch=Batch.find(@student.batch_id)
    end
    @currency_type = currency
    if @dates_array.count == 1
      advance_fee_collection = false
      @self_advance_fee = false
      @fee_has_advance_particular = false
      
      @date = @fee_collection = FinanceFeeCollection.find(params[:id2])
      @financefee = @student.finance_fee_by_date @date
      
      if @financefee.has_advance_fee_id
        if @date.is_advance_fee_collection
          @self_advance_fee = true
          advance_fee_collection = true
        end
        @fee_has_advance_particular = true
        @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
        @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
        if @advance_ids.blank?
          @advance_ids[0] = 0
        end
        @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
      end
      
      @student_has_due = false
      @std_finance_fee_due = FinanceFee.find(:first,:conditions=>["finance_fee_collections.due_date < ? and finance_fees.is_paid = 0 and finance_fees.student_id = ?", @date.due_date,@student.id],:include=>"finance_fee_collection")
      unless @std_finance_fee_due.blank?
        @student_has_due = true
      end
      @due_date = @fee_collection.due_date
      @paid_fees = @financefee.finance_transactions
      @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])
      
      exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
      unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
        exclude_particular_ids = exclude_particular_ids
      else
        exclude_particular_ids = [0]
      end
      
      if advance_fee_collection
        fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
        fee_collection_advances_particular = fee_collection_advances_particular.reject { |f| f.to_s.empty? }
        if fee_collection_advances_particular.blank?
          fee_collection_advances_particular[0] = 0
        end
        
        if fee_collection_advances_particular.include?(0)
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
        else
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@financefee.batch_id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
        end
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
      end
      
      @total_discount = 0
      
      if advance_fee_collection
        month = 1
        payable = 0
        @fee_collection_advances.each do |fee_collection_advance|
          @fee_particulars.each do |particular|
            if fee_collection_advance.particular_id == particular.finance_fee_particular_category_id
              payable += particular.amount * fee_collection_advance.no_of_month.to_i
            else
              payable += particular.amount
            end
          end
        end
        @total_payable=payable.to_f
      else  
        @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
      end
      
      @adv_fee_discount = false
      @actual_discount = 1

      if advance_fee_collection
        calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
      else
        if @fee_has_advance_particular
          calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
        else
          calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
        end
      end
      
      
      days=(Date.today-@date.due_date.to_date).to_i
      
      fine_enabled = true
      student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
      unless student_fee_configuration.blank?
        if student_fee_configuration.config_value.to_i == 1
          fine_enabled = true
        else
          fine_enabled = false
        end
      end
      
      if @tmp_paid_fees.blank?
        @tmp_paid_fees = @financefee.finance_transactions
      end
      
      unless @tmp_paid_fees.blank?
        @tmp_paid_fees.each do |paid_fee|
          transaction_id = paid_fee.id
          online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
          unless online_payments.blank?
            fine_enabled = false
          end
        end
      end
      
      auto_fine=@date.fine
      
      bal=(@total_payable-@total_discount).to_f
      unless params[:submission_date].nil? or params[:submission_date].empty? or params[:submission_date].blank?
        require 'date'
        @submission_date = Date.parse(params[:submission_date])
        days=(Date.parse(params[:submission_date])-@date.due_date.to_date).to_i
      else
        @submission_date = Date.today
        if @financefee.is_paid
          @paid_fees = @financefee.finance_transactions
          days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
        else
          days=(Date.today-@date.due_date.to_date).to_i
        end
      end

      fine_enabled = true
      student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
      unless student_fee_configuration.blank?
        if student_fee_configuration.config_value.to_i == 1
          fine_enabled = true
        else
          fine_enabled = false
        end
      end
      
      if @tmp_paid_fees.blank?
        @tmp_paid_fees = @financefee.finance_transactions
      end
      
      unless @tmp_paid_fees.blank?
        @tmp_paid_fees.each do |paid_fee|
          transaction_id = paid_fee.id
          online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
          unless online_payments.blank?
            fine_enabled = false
          end
        end
      end
      
      auto_fine=@date.fine

      @has_fine_discount = false
      if days > 0 and auto_fine and fine_enabled #and @financefee.is_paid == false
        @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
        @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule

        calculate_extra_fine(@date, @financefee.batch, @student, @fine_rule)

        @new_fine_amount = @fine_amount
        get_fine_discount(@date, @financefee.batch, @student)
        if @fine_amount < 0
          @fine_amount = 0
        end
      end
      
      @fine_amount=0 if @financefee.is_paid
      
      #    render :layout => false
      if MultiSchool.current_school.id == 312
        render :pdf => 'student_fee_receipt_pdf',
          :orientation => 'Landscape', :zoom => 1.00,
          :page_size => 'Legal',
          :margin => {    :top=> 10,
          :bottom => 0,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}    
      elsif MultiSchool.current_school.id == 325
        render :pdf => 'student_fee_receipt_pdf',
          :orientation => 'Landscape', :zoom => 1.00,
          :page_size => 'A5',
          :margin => {    :top=> 10,
          :bottom => 0,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      elsif  MultiSchool.current_school.id == 3
        render :pdf => 'student_fee_receipt_pdf',
          :orientation => 'Portrait', :zoom => 1.00,
          :page_size => 'A4',
          :margin => {:top=> 10,
          :bottom => 0,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      elsif  MultiSchool.current_school.id == 352
        unless params[:admission].blank?
          render :pdf => 'student_fee_receipt_pdf',
            :orientation => 'Landscape', :zoom => 1.00,
            :page_size => 'Legal',
            :margin => {    :top=> 5,
            :bottom => 0,
            :left=> 0,
            :right => 0},
            :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
            :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}  
        else
          render :pdf => 'student_fee_receipt_pdf',
            :orientation => 'Portrait', :zoom => 1.00,
            :page_size => 'A4',
            :margin => {:top=> 10,
            :bottom => 0,
            :left=> 10,
            :right => 10},
            :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
            :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
        end
      else
        render :pdf => 'student_fee_receipt_pdf',
          :orientation => 'Landscape', :zoom => 1.00,
          :page_size => 'A4',
          :margin => {    :top=> 10,
          :bottom => 0,
          :left=> 10,
          :right => 10},
          :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
          :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
      end
    else
      @iloop = 0
      
      @date = []
      @fee_collection = []
      @due_date = []
      @fee_category = []
      @paid_fees = []
      @financefee = []
      @fee_particulars = []
      @discounts = []
      @total_payable = []

      @total_discount = []
      @fine_rule = []
      @fine_amount = []
      @has_fine_discount = []
      @onetime_discounts = []
      @onetime_discounts_amount = []
      @discounts_amount = []
      @new_fine_amount = []
      @discounts_on_lates = []
      @discounts_late_amount = []
      @dates_array.each do |date|
        advance_fee_collection = false
        @self_advance_fee = false
        @fee_has_advance_particular = false
        
        @date[@iloop] = @fee_collection[@iloop] = FinanceFeeCollection.find(date)
        @financefee[@iloop] = @student.finance_fee_by_date(@date[@iloop])
        
        if @financefee[@iloop].advance_fee_id.to_i > 0
          if @date.is_advance_fee_collection
            @self_advance_fee = true
            advance_fee_collection = true
          end
          
          @fee_has_advance_particular = true
          @advance_id = @financefee[@iloop].advance_fee_id
          @fee_collection_advances = FinanceFeeAdvance.find(@advance_id)
        end
        
        @due_date[@iloop] = @fee_collection[@iloop].due_date
        @fee_category[@iloop] = FinanceFeeCategory.find(@fee_collection[@iloop].fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
        flash[:warning]=nil
        flash[:notice]=nil
        @paid_fees[@iloop] = @financefee[@iloop].finance_transactions

        if advance_fee_collection
          if @fee_collection_advances.particular_id == 0
            @fee_particulars[@iloop] = @date[@iloop].finance_fee_particulars.all(:conditions=>"batch_id=#{@financefee[@iloop].batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[@iloop].batch) }
          else
            @fee_particulars[@iloop] = @date[@iloop].finance_fee_particulars.all(:conditions=>"batch_id=#{@financefee[@iloop].batch_id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[@iloop].batch) }
          end
        else
          @fee_particulars[@iloop] = @date[@iloop].finance_fee_particulars.all(:conditions=>"batch_id=#{@financefee[@iloop].batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[@iloop].batch) }
        end
    
        @total_discount[@iloop] = 0
        
        if advance_fee_collection
          @total_payable[@iloop] = (@fee_particulars[@iloop].map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
        else  
          @total_payable[@iloop]=@fee_particulars[@iloop].map{|s| s.amount}.sum.to_f
        end
        
        if advance_fee_collection
          calculate_discount_index(@date[@iloop], @financefee[@iloop].batch, @student, @iloop, true, @fee_collection_advances, @fee_has_advance_particular)
        else
          if @fee_has_advance_particular
            calculate_discount_index(@date[@iloop], @financefee[@iloop].batch, @student, @iloop, false, @fee_collection_advances, @fee_has_advance_particular)
          else
            calculate_discount_index(@date[@iloop], @financefee[@iloop].batch, @student, @iloop, false, nil, @fee_has_advance_particular)
          end
        end
        
        bal=(@total_payable[@iloop]-@total_discount[@iloop]).to_f
        days=(Date.today-@date[@iloop].due_date.to_date).to_i
        
        fine_enabled = true
        student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date[@iloop].id} and config_key = 'fine_payment_student'")
        unless student_fee_configuration.blank?
          if student_fee_configuration.config_value.to_i == 1
            fine_enabled = true
          else
            fine_enabled = false
          end
        end
        
        unless @tmp_paid_fees.blank?
          @tmp_paid_fees = @financefee[@iloop].finance_transactions
        end
        
        unless @tmp_paid_fees.blank?
          @tmp_paid_fees.each do |paid_fee|
            transaction_id = paid_fee.id
            online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
            unless online_payments.blank?
              fine_enabled = false
            end
          end
        end
        
        auto_fine=@date[@iloop].fine
        
        @has_fine_discount[@iloop] = false
        if days > 0 and auto_fine and @financefee[@iloop].is_paid == false and fine_enabled
          @fine_rule[@iloop]=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date[@iloop].created_at}'"],:order=>'fine_days ASC')
          @fine_amount[@iloop]=@fine_rule[@iloop].is_amount ? @fine_rule[@iloop].fine_amount : (bal*@fine_rule[@iloop].fine_amount)/100 if @fine_rule[@iloop]
          calculate_extra_fine_index(@date[@iloop], @financefee[@iloop].batch, @student, @fine_rule[@iloop],@iloop)
          @new_fine_amount[@iloop] = @fine_amount[@iloop]
          get_fine_discount_index(@date[@iloop], @financefee[@iloop].batch, @student,@iloop)
          if @fine_amount[@iloop] < 0
            @fine_amount[@iloop] = 0
          end
        end

        @fine_amount[@iloop]=0 if @financefee[@iloop].is_paid
        @iloop = @iloop+1
      end
      
      render :pdf => 'student_fee_receipt_pdf',
        :orientation => 'Landscape', :zoom => 1.00,
        :page_size => 'A5',
        :margin => {    :top=> 10,
        :bottom => 0,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    end 
  end
  
  def student_fee_receipt_pdf_multiple
    @date = [] 
    @fee_collection = []
    @student_fee = []
    
    unless params[:fees].nil?
      fees = params[:fees].split(",")
      student_id = params[:id]
      
      @fee_collections = fees
      @student = Student.find(student_id)
      fees.each do |fee|
        f = fee.to_i
        finance_fee = FinanceFee.find(f)
        fee_collection_id = finance_fee.fee_collection_id
        
        @date[f] = @fee_collection[f] = FinanceFeeCollection.find(fee_collection_id)
        @student_fee[f] = FinanceFee.check_update_student_fee(@date[f], @student, finance_fee)
      end
    end
    
    if MultiSchool.current_school.id == 352
      render :pdf => 'student_fee_receipt_pdf_multiple',
        :orientation => 'Portrait', :zoom => 1.00,
        :page_size => 'A4',
        :margin => {:top=> 10,
        :bottom => 0,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    else
      render :pdf => 'student_fee_receipt_pdf',
        :orientation => 'Landscape', :zoom => 1.00,
        :page_size => 'A4',
        :margin => {    :top=> 10,
        :bottom => 0,
        :left=> 10,
        :right => 10},
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
    end
  end
  
  def update_vat_ajax
    if request.post?
      @show_only_structure = false
      @from_batch_fee = true
      unless params[:from_batch_fee].nil?
        @from_batch_fee = params[:from_batch_fee]
      else
        @from_batch_fee = false
      end
      
      advance_fee_collection = false
      @self_advance_fee = false
      @fee_has_advance_particular = false
        
      @date = @fee_collection = FinanceFeeCollection.find(params[:vat][:date])
      @batch   = Batch.find(params[:vat][:batch_id])
      student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
      @dates = @batch.finance_fee_collections
      @student = Student.find(params[:vat][:student]) if params[:vat][:student]
      @student ||= FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}",:joins=>'INNER JOIN students ON finance_fees.student_id = students.id').student
      @prev_student = @student.previous_fee_student(@date.id,student_ids)
      @next_student = @student.next_fee_student(@date.id, student_ids)

      @financefee = @student.finance_fee_by_date @date
      
      if @financefee.has_advance_fee_id
        if @date.is_advance_fee_collection
          @self_advance_fee = true
          advance_fee_collection = true
        end
        @fee_has_advance_particular = true
        @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
        @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
        if @advance_ids.blank?
          @advance_ids[0] = 0
        end
        @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
      end
      
      #if @financefee.advance_fee_id.to_i > 0
        
      @paid_fees = @financefee.finance_transactions
      unless params[:vat][:vat_amount].to_f < 0
        @vat = (params[:vat][:vat_amount])
      else
        @financefee.errors.add_to_base("#{t('flash24')}")
      end
      
      unless params[:vat][:fine].to_f <= 0
        @fine = (params[:vat][:fine])
      end

      @due_date = @fee_collection.due_date

      exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
      unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
        exclude_particular_ids = exclude_particular_ids
      else
        exclude_particular_ids = [0]
      end
      
      @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])
      if advance_fee_collection
        if @fee_collection_advances.particular_id == 0
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
        else
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@batch.id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
        end
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      end
      
      #@discounts=@date.fee_discounts.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch)}

      if advance_fee_collection
        @total_payable = (@fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
      else  
        @total_payable = @fee_particulars.map{|s| s.amount}.sum.to_f
      end
      @total_discount = 0
    
      if advance_fee_collection
        calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
      else
        if @fee_has_advance_particular
          calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
        else
          calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
        end
      end
      
      bal=(@total_payable-@total_discount).to_f
      days=(Date.today-@date.due_date.to_date).to_i
      
      fine_enabled = true
      student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
      unless student_fee_configuration.blank?
        if student_fee_configuration.config_value.to_i == 1
          fine_enabled = true
        else
          fine_enabled = false
        end
      end
      
      if @tmp_paid_fees.blank?
        @tmp_paid_fees = @financefee.finance_transactions
      end
      
      unless @tmp_paid_fees.blank?
        @tmp_paid_fees.each do |paid_fee|
          transaction_id = paid_fee.id
          online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
          unless online_payments.blank?
            fine_enabled = false
          end
        end
      end
      
      auto_fine=@date.fine
      
      @has_fine_discount = false
      
      if days > 0 and auto_fine and @financefee.is_paid == false and fine_enabled
        @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
        @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
        calculate_extra_fine(@date, @batch, @student, @fine_rule)
        @new_fine_amount = @fine_amount
        get_fine_discount(@date, @batch, @student)
        if @fine_amount < 0
          @fine_amount = 0
        end
      end
      
      @fine_amount=0 if @financefee.is_paid
      
      render :update do |page|
        page.replace_html "student", :partial => "student_fees_submission", :with => @fine
        page << "loadJS();"
        page << 'j(".select2-combo").select2();'
      end
    end
  end

  def update_fine_ajax
    if request.post?  
      
      @show_only_structure = false
      @from_batch_fee = true
      unless params[:from_batch_fee].nil?
        @from_batch_fee = params[:from_batch_fee]
      else
        @from_batch_fee = false
      end
      
      advance_fee_collection = false
      @self_advance_fee = false
      @fee_has_advance_particular = false
        
      @date = @fee_collection = FinanceFeeCollection.find(params[:fine][:date])
      @batch   = Batch.find(params[:fine][:batch_id])
      student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
      @dates = @batch.finance_fee_collections
      @student = Student.find(params[:fine][:student]) if params[:fine][:student]
      @student ||= FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}",:joins=>'INNER JOIN students ON finance_fees.student_id = students.id').student
      @prev_student = @student.previous_fee_student(@date.id,student_ids)
      @next_student = @student.next_fee_student(@date.id, student_ids)

      @financefee = @student.finance_fee_by_date @date
      
      if @financefee.has_advance_fee_id
        if @date.is_advance_fee_collection
          @self_advance_fee = true
          advance_fee_collection = true
        end
        @fee_has_advance_particular = true
        @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
        @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
        if @advance_ids.blank?
          @advance_ids[0] = 0
        end
        @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
      end
      
      #if @financefee.advance_fee_id.to_i > 0
      
      @paid_fees = @financefee.finance_transactions
      unless params[:fine][:fee].to_f <= 0
        @fine = (params[:fine][:fee])
      else
        @financefee.errors.add_to_base("#{t('flash24')}")
      end
      
      unless params[:fine][:vat].to_f < 0
        @vat = (params[:fine][:vat])
      end

      @due_date = @fee_collection.due_date

      exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
      unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
        exclude_particular_ids = exclude_particular_ids
      else
        exclude_particular_ids = [0]
      end
      
      @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])
      if advance_fee_collection
        
        if @fee_collection_advances.particular_id == 0
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
        else
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@batch.id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
        end
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      end
      @total_discount = 0
      if advance_fee_collection
        @total_payable = (@fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
      else  
        @total_payable = @fee_particulars.map{|s| s.amount}.sum.to_f
      end
      
      if advance_fee_collection
        calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
      else
        if @fee_has_advance_particular
          calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
        else
          calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
        end
      end
      bal=(@total_payable-@total_discount).to_f
      days=(Date.today-@date.due_date.to_date).to_i
      
      fine_enabled = true
      student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
      unless student_fee_configuration.blank?
        if student_fee_configuration.config_value.to_i == 1
          fine_enabled = true
        else
          fine_enabled = false
        end
      end
      
      if @tmp_paid_fees.blank?
        @tmp_paid_fees = @financefee.finance_transactions
      end
      
      unless @tmp_paid_fees.blank?
        @tmp_paid_fees.each do |paid_fee|
          transaction_id = paid_fee.id
          online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
          unless online_payments.blank?
            fine_enabled = false
          end
        end
      end
      
      auto_fine=@date.fine
      
      @has_fine_discount = false
      if days > 0 and auto_fine and @financefee.is_paid == false and fine_enabled
        @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
        @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
        calculate_extra_fine(@date, @batch, @student, @fine_rule)
        @new_fine_amount = @fine_amount
        get_fine_discount(@date, @batch, @student)
        if @fine_amount < 0
          @fine_amount = 0
        end
      end
        
      @fine_amount=0 if @financefee.is_paid
      
      render :update do |page|
        page.replace_html "student", :partial => "student_fees_submission", :with => @fine
        page << "loadJS();"
        page << 'j(".select2-combo").select2();'  
      end
    end
  end

  def search_logic                 #student search (fees submission)
    query = params[:query]
    if query.length>= 3
      params[:query].gsub! '+', ' '
      @students_result = Student.find(:all,
        :conditions => ["first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                            OR admission_no LIKE ? OR (concat(first_name, \" \", last_name) LIKE ? ) ",
          "%#{query}%","%#{query}%","#{query}%",
          "%#{query}%", "%#{query}%" ],
        :order => "batch_id asc,first_name asc") unless query == ''
    else
      @students_result = Student.find(:all,
        :conditions => ["admission_no = ? " , query],
        :order => "batch_id asc,first_name asc") unless query == ''
    end
    render :layout => false
  end

  def fees_student_dates
    unless params[:test].blank?
      updated = 0
      created = 0
      particular_wise_found = 0
      if params[:test].to_i == 1
#        finance_fees = FinanceFee.find(:all, :conditions => "updated_at > '2019-11-14 07:27:45'")
#        #abort(finance_fees.inspect)
#        unless finance_fees.blank?
#          finance_fees.each do |fee|
#            student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{fee.student_id} and fee_id = #{fee.id} and amount_to_pay > 0 and amount_paid = 0 and particular_id = 0")
#            if student_fee_ledgers.blank?
#              s = Student.find(fee.student_id)
#              student_fee_ledgers_particulars = StudentFeeLedger.find(:all, :conditions => "student_id = #{fee.student_id} and fee_id = #{fee.id} and amount_to_pay > 0 and amount_paid = 0 and particular_id > 0")
#              particular_amount = 0.0
#              unless student_fee_ledgers_particulars.blank?
#                student_fee_ledgers_particulars.each do |student_fee_ledgers_particular|
#                  particular_amount += student_fee_ledgers_particular.amount_to_pay
#                  particular_wise_found += 1
#                end
#              end
#              unless s.blank?
#                date = FinanceFeeCollection.find(:first, :conditions => "id = #{fee.fee_collection_id}")
#                unless date.blank?
#                  balance = FinanceFee.get_student_balance(date, s, fee)
#                  balance = balance - particular_amount
#                  if balance < 0
#                    balance = 0
#                  end
#                  ledger_date = date.start_date
#                  student_fee_ledger = StudentFeeLedger.new
#                  student_fee_ledger.student_id = s.id
#                  student_fee_ledger.ledger_date = ledger_date
#                  student_fee_ledger.amount_to_pay = balance.to_f
#                  student_fee_ledger.fee_id = fee.id
#                  student_fee_ledger.save
#                  created += 1
#                end
#              end
#            else
#              updated += 1
#              #student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{fee.student_id} and fee_id = #{fee.id} and amount_to_pay > 0 and amount ")
#            end
#          end
#        end
        finance_transactions = FinanceTransaction.find(:all, :conditions => "updated_at > '2019-11-14 07:27:45' and finance_id IS NOT NULL")
        #abort(finance_fees.inspect)
        unless finance_transactions.blank?
          finance_transactions.each do |finance_transaction|
            student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{finance_transaction.payee_id} and fee_id = #{finance_transaction.finance_id} and amount_to_pay = 0 and amount_paid > 0 and particular_id = 0")
            if student_fee_ledgers.blank?
              s = Student.find(:first, :conditions => "id = #{finance_transaction.payee_id}")
              unless s.blank?
                payments = Payment.find(:all, :conditions => "finance_transaction_id = #{finance_transaction.id}", :group => "order_id")
                unless payments.blank?
                  fee = FinanceFee.find(:first, :conditions => "id = #{finance_transaction.finance_id}")
                  unless fee.nil?
                    payment = payments[0]
                    balance = finance_transaction.amount
                    unless payment.transaction_datetime.blank?
                      ledger_date = payment.transaction_datetime.strftime("%Y-%m-%d")
                    else
                      ledger_date = f.transaction_date.strftime("%Y-%m-%d")
                    end
                    student_fee_ledger = StudentFeeLedger.new
                    student_fee_ledger.student_id = s.id
                    student_fee_ledger.ledger_date = ledger_date
                    student_fee_ledger.fee_id = fee.id
                    student_fee_ledger.amount_paid = balance.to_f
                    student_fee_ledger.transaction_id = finance_transaction.id
                    order_ids = payments.map(&:order_id).uniq
                    student_fee_ledger.order_id = order_ids.join(",")
                    student_fee_ledger.save
                  end
                  created += 1
                end
              end
            else
              updated += 1
              #student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{fee.student_id} and fee_id = #{fee.id} and amount_to_pay > 0 and amount ")
            end
          end
        end
        abort(updated.to_s + "  " + created.to_s + "  " + particular_wise_found.to_s)
      end
    end
#    @students = Student.active
#    #@students = Student.find(:all, :conditions => "id = 24170")
#    #abort(@student.inspect)
#    @students.each do |s|
#      student_fee_ledger = StudentFeeLedger.new
#      student_fee_ledger.student_id = s.id
#      student_fee_ledger.ledger_date = s.admission_date
#      student_fee_ledger.save
#    end
#
#    
#    @students = Student.active
#    #@students = Student.find(:all, :conditions => "id = 24170")
#    @students.each do |s|
#      finance_fees = FinanceFee.find(:all, :conditions => "student_id = #{s.id}")
#      unless finance_fees.nil?
#        finance_fees.each do |fee|
#          date = FinanceFeeCollection.find(:first, :conditions => "id = #{fee.fee_collection_id}")
#          unless date.nil?
#            balance = FinanceFee.get_student_balance(date, s, fee)
#            ledger_date = date.start_date
#            student_fee_ledger = StudentFeeLedger.new
#            student_fee_ledger.student_id = s.id
#            student_fee_ledger.ledger_date = ledger_date
#            student_fee_ledger.amount_to_pay = balance.to_f
#            student_fee_ledger.fee_id = fee.id
#            student_fee_ledger.save
#          end
#        end
#      end
#    end

#    @students = Student.active
#    #@students = Student.find(:all, :conditions => "id = 24170")
#    @students.each do |s|
#      finance_transactions = FinanceTransaction.find(:all, :conditions => "finance_transactions.payee_id = #{s.id} AND finance_transactions.finance_id IS NOT NULL ", :joins => "INNER JOIN payments ON payments.finance_transaction_id = finance_transactions.id")
#      unless finance_transactions.nil?
#        finance_transactions.each do |transaction|
#          payments = Payment.find(:all, :conditions => "finance_transaction_id = #{transaction.id}", :group => "order_id")
#          unless payments.nil? or payments.empty? or payments.blank?
#            fee = FinanceFee.find(:first, :conditions => "id = #{transaction.finance_id}")
#            unless fee.nil?
#              balance = transaction.amount
#              ledger_date = transaction.transaction_date
#              student_fee_ledger = StudentFeeLedger.new
#              student_fee_ledger.student_id = s.id
#              student_fee_ledger.ledger_date = ledger_date
#              student_fee_ledger.fee_id = fee.id
#              student_fee_ledger.amount_paid = balance.to_f
#              student_fee_ledger.transaction_id = transaction.id
#              order_ids = payments.map(&:order_id).uniq
#              student_fee_ledger.order_id = order_ids.join(",")
#              student_fee_ledger.save
#            end
#          end
#        end
#      end
#    end
    @student = Student.find(params[:id])
    #@dates=FinanceFeeCollection.find(:all,:joins=>"INNER JOIN fee_collection_batches on fee_collection_batches.finance_fee_collection_id=finance_fee_collections.id INNER JOIN finance_fees on finance_fees.fee_collection_id=finance_fee_collections.id",:conditions=>"finance_fees.student_id='#{@student.id}' and finance_fees.batch_id='#{@student.batch.id}' and finance_fee_collections.is_deleted=#{false} and fee_collection_batches.is_deleted=#{false}").uniq
    @dates=FinanceFeeCollection.find(:all,:joins=>"INNER JOIN fee_collection_batches on fee_collection_batches.finance_fee_collection_id=finance_fee_collections.id INNER JOIN finance_fees on finance_fees.fee_collection_id=finance_fee_collections.id",:conditions=>"finance_fees.student_id='#{@student.id}' and finance_fee_collections.is_deleted=#{false} and fee_collection_batches.is_deleted=#{false}").uniq # and finance_fees.batch_id='#{@student.batch.id}'
  end

  def fees_submission_student
    if params[:date].present?
      advance_fee_collection = false
      @self_advance_fee = false
      @fee_has_advance_particular = false
        
      @dates_array = params[:date].split(",")
      if @dates_array.count == 1
        @student = Student.find(params[:id])
        @date = @fee_collection = FinanceFeeCollection.find(params[:date])
        @financefee = @student.finance_fee_by_date(@date)
        
        if @financefee.has_advance_fee_id
          if @date.is_advance_fee_collection
            @self_advance_fee = true
            advance_fee_collection = true
          end
          @fee_has_advance_particular = true
          @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
          @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
          if @advance_ids.blank?
            @advance_ids[0] = 0
          end
          @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
        end
        
        #if @financefee.advance_fee_id.to_i > 0
        
        @due_date = @fee_collection.due_date
        @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
        flash[:warning]=nil
        flash[:notice]=nil

        @paid_fees = @financefee.finance_transactions

        exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
        unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
          exclude_particular_ids = exclude_particular_ids
        else
          exclude_particular_ids = [0]
        end
        
        if advance_fee_collection
          
          if @fee_collection_advances.particular_id == 0
            @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
          else
            @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@financefee.batch_id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
          end
        else
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
        end
        if advance_fee_collection
          @total_payable = (@fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
        else  
          @total_payable = @fee_particulars.map{|s| s.amount}.sum.to_f
        end
        @total_discount = 0
        
        if advance_fee_collection
          calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
        else
          if @fee_has_advance_particular
            calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
          else
            calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
          end
        end
        
        bal=(@total_payable-@total_discount).to_f
        
        #days=(Date.today-@date.due_date.to_date).to_i
        unless params[:submission_date].nil? or params[:submission_date].empty? or params[:submission_date].blank?
          require 'date'
          @submission_date = Date.parse(params[:submission_date])
          days=(Date.parse(params[:submission_date])-@date.due_date.to_date).to_i
        else
          @submission_date = Date.today
          if @financefee.is_paid
            @paid_fees = @financefee.finance_transactions
            days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
          else
            days=(Date.today-@date.due_date.to_date).to_i
          end
        end 
        
        fine_enabled = true
        student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
        unless student_fee_configuration.blank?
          if student_fee_configuration.config_value.to_i == 1
            fine_enabled = true
          else
            fine_enabled = false
          end
        end
        
        if @tmp_paid_fees.blank?
          @tmp_paid_fees = @financefee.finance_transactions
        end
        
        unless @tmp_paid_fees.blank?
          @tmp_paid_fees.each do |paid_fee|
            transaction_id = paid_fee.id
            online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
            unless online_payments.blank?
              fine_enabled = false
            end
          end
        end
        
        auto_fine=@date.fine
        
        @has_fine_discount = false
        if days > 0 and auto_fine and @financefee.is_paid == false and fine_enabled
          @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
          @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
          calculate_extra_fine(@date, @financefee.batch, @student, @fine_rule)
          @new_fine_amount = @fine_amount
          get_fine_discount(@date, @financefee.batch, @student)
          if @fine_amount < 0
            @fine_amount = 0
          end
        end
        
        @fine_amount=0 if @financefee.is_paid
        
        render :update do |page|
          page.replace_html "fee_submission", :partial => "fees_submission_form"
        end
      else
        @iloop = 0
        @student = Student.find(params[:id])
        @date = []
        @fee_collection = []
        @due_date = []
        @fee_category = []
        @paid_fees = []
        @financefee = []
        @fee_particulars = []
        @discounts = []
        @total_payable = []

        @total_discount = []
        @fine_rule = []
        @fine_amount = []
        @has_fine_discount = []
        @onetime_discounts = []
        @onetime_discounts_amount = []
        @discounts_amount = []
        @new_fine_amount = []
        @discounts_on_lates = []
        @discounts_late_amount = []
        @dates_array.each do |date|
          advance_fee_collection = false
          @self_advance_fee = false
          @fee_has_advance_particular = false
        
          @date[@iloop] = @fee_collection[@iloop] = FinanceFeeCollection.find(date)
          @financefee[@iloop] = @student.finance_fee_by_date(@date[@iloop])
          
          if @financefee[@iloop].advance_fee_id.to_i > 0
            if @date.is_advance_fee_collection
              @self_advance_fee = true
              advance_fee_collection = true
            end
            
            @fee_has_advance_particular = true
            @advance_id = @financefee[@iloop].advance_fee_id
            @fee_collection_advances = FinanceFeeAdvance.find(@advance_id)
          end
    
          @due_date[@iloop] = @fee_collection[@iloop].due_date
          @fee_category[@iloop] = FinanceFeeCategory.find(@fee_collection[@iloop].fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
          flash[:warning]=nil
          flash[:notice]=nil
          @paid_fees[@iloop] = @financefee[@iloop].finance_transactions
          
          if advance_fee_collection
            if @fee_collection_advances.particular_id == 0
              @fee_particulars[@iloop] = @date[@iloop].finance_fee_particulars.all(:conditions=>"batch_id=#{@financefee[@iloop].batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[@iloop].batch) }
            else
              @fee_particulars[@iloop] = @date[@iloop].finance_fee_particulars.all(:conditions=>"batch_id=#{@financefee[@iloop].batch_id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[@iloop].batch) }
            end
          else
            @fee_particulars[@iloop] = @date[@iloop].finance_fee_particulars.all(:conditions=>"batch_id=#{@financefee[@iloop].batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[@iloop].batch) }
          end
        
          @total_discount[@iloop] = 0
          if advance_fee_collection
            @total_payable[@iloop] = (@fee_particulars[@iloop].map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
          else  
            @total_payable[@iloop] = @fee_particulars[@iloop].map{|s| s.amount}.sum.to_f
          end
          
          if advance_fee_collection
            calculate_discount_index(@date[@iloop], @financefee[@iloop].batch, @student, @iloop, true, @fee_collection_advances, @fee_has_advance_particular)
          else
            if @fee_has_advance_particular
              calculate_discount_index(@date[@iloop], @financefee[@iloop].batch, @student, @iloop, false, @fee_collection_advances, @fee_has_advance_particular)
            else
              calculate_discount_index(@date[@iloop], @financefee[@iloop].batch, @student, @iloop, false, nil, @fee_has_advance_particular)
            end
          end
          
          bal=(@total_payable[@iloop]-@total_discount[@iloop]).to_f
          days=(Date.today-@date[@iloop].due_date.to_date).to_i
          
          fine_enabled = true
          student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date[@iloop].id} and config_key = 'fine_payment_student'")
          unless student_fee_configuration.blank?
            if student_fee_configuration.config_value.to_i == 1
              fine_enabled = true
            else
              fine_enabled = false
            end
          end
          
          unless @tmp_paid_fees.blank?
            @tmp_paid_fees = @financefee[@iloop].finance_transactions
          end
          
          unless @tmp_paid_fees.blank?
            @tmp_paid_fees.each do |paid_fee|
              transaction_id = paid_fee.id
              online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
              unless online_payments.blank?
                fine_enabled = false
              end
            end
          end
          
          auto_fine=@date[@iloop].fine
          
          @has_fine_discount[@iloop] = false
          if days > 0 and auto_fine and @financefee[@iloop].is_paid == false and fine_enabled
            @fine_rule[@iloop]=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date[@iloop].created_at}'"],:order=>'fine_days ASC')
            @fine_amount[@iloop]=@fine_rule[@iloop].is_amount ? @fine_rule[@iloop].fine_amount : (bal*@fine_rule[@iloop].fine_amount)/100 if @fine_rule[@iloop]
            calculate_extra_fine_index(@date[@iloop], @financefee[@iloop].batch, @student, @fine_rule[@iloop],@iloop)
            @new_fine_amount[@iloop] = @fine_amount[@iloop]
            get_fine_discount_index(@date[@iloop], @financefee[@iloop].batch, @student,@iloop)
            if @fine_amount[@iloop] < 0
              @fine_amount[@iloop] = 0
            end
          end

          @fine_amount[@iloop]=0 if @financefee[@iloop].is_paid
          @iloop = @iloop+1
        end
        render :update do |page|
          page.replace_html "fee_submission", :partial => "fees_submission_form_multi"
        end
      end  
    else
      render :update do |page|
        page.replace_html "fee_submission", :text=>""
      end
    end
  end
  
  def update_student_vat_ajax
    advance_fee_collection = false
    @self_advance_fee = false
    @fee_has_advance_particular = false
        
    @student = Student.find(params[:vat][:student])
    @date = @fee_collection = FinanceFeeCollection.find(params[:vat][:date])
    @financefee = @student.finance_fee_by_date(@date)
    
    if @financefee.has_advance_fee_id
      if @date.is_advance_fee_collection
        @self_advance_fee = true
        advance_fee_collection = true
      end
      @fee_has_advance_particular = true
      @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
      @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
      if @advance_ids.blank?
        @advance_ids[0] = 0
      end
      @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
    end
    
    #if @financefee.advance_fee_id.to_i > 0
    
    unless params[:vat][:vat_amount].to_f < 0
      @vat = (params[:vat][:vat_amount])
      flash[:notice] = nil
    else
      flash[:notice] = "#{t('flash24')}"
    end
    unless params[:vat][:fine].to_f <= 0
      @fine = (params[:vat][:fine])
    end
    @paid_fees = @financefee.finance_transactions
    @due_date = @fee_collection.due_date
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    
    exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
    unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
      exclude_particular_ids = exclude_particular_ids
    else
      exclude_particular_ids = [0]
    end
    
    if advance_fee_collection
      if @fee_collection_advances.particular_id == 0
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
      end
    else
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
    end
    
    @total_discount = 0
    if advance_fee_collection
      @total_payable = (@fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
    else  
      @total_payable = @fee_particulars.map{|s| s.amount}.sum.to_f
    end
    
    if advance_fee_collection
      calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
    else
      if @fee_has_advance_particular
        calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
      else
        calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
      end
    end
      
    bal=(@total_payable-@total_discount).to_f
    days=(Date.today-@date.due_date.to_date).to_i
    
    fine_enabled = true
    student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
    unless student_fee_configuration.blank?
      if student_fee_configuration.config_value.to_i == 1
        fine_enabled = true
      else
        fine_enabled = false
      end
    end
    
    if @tmp_paid_fees.blank?
      @tmp_paid_fees = @financefee.finance_transactions
    end
    
    unless @tmp_paid_fees.blank?
      @tmp_paid_fees.each do |paid_fee|
        transaction_id = paid_fee.id
        online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
        unless online_payments.blank?
          fine_enabled = false
        end
      end
    end
    
    auto_fine=@date.fine
    
    @has_fine_discount = false
    if days > 0 and auto_fine and @financefee.is_paid == false and fine_enabled
      @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
      @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
      calculate_extra_fine(@date, @batch, @student, @fine_rule)
      @new_fine_amount = @fine_amount
      get_fine_discount(@date, @batch, @student)
      if @fine_amount < 0
        @fine_amount = 0
      end
    end
    render :update do |page|
      page.replace_html "fee_submission", :partial => "fees_submission_form"
    end

  end

  def update_student_fine_ajax
    advance_fee_collection = false
    @self_advance_fee = false
    @fee_has_advance_particular = false
        
    @student = Student.find(params[:fine][:student])
    @date = @fee_collection = FinanceFeeCollection.find(params[:fine][:date])
    @financefee = @student.finance_fee_by_date(@date)
    
    if @financefee.has_advance_fee_id
      if @date.is_advance_fee_collection
        @self_advance_fee = true
        advance_fee_collection = true
      end
      @fee_has_advance_particular = true
      @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
      @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
      if @advance_ids.blank?
        @advance_ids[0] = 0
      end
      @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
    end
    
    #if @financefee.advance_fee_id.to_i > 0
      
    unless params[:fine][:fee].to_f < 0
      @fine = (params[:fine][:fee])
      flash[:notice] = nil
    else
      flash[:notice] = "#{t('flash24')}"
    end
    unless params[:fine][:vat].to_f <= 0
      @vat = (params[:fine][:vat])
    end  
    @paid_fees = @financefee.finance_transactions
    @due_date = @fee_collection.due_date
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
    unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
      exclude_particular_ids = exclude_particular_ids
    else
      exclude_particular_ids = [0]
    end
      
    if advance_fee_collection
      if @fee_collection_advances.particular_id == 0
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
      end
    else
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
    end
    
    @total_discount = 0
    if advance_fee_collection
      @total_payable = (@fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
    else  
      @total_payable = @fee_particulars.map{|s| s.amount}.sum.to_f
    end
    
    if advance_fee_collection
      calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
    else
      if @fee_has_advance_particular
        calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
      else
        calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
      end
    end
    
    bal=(@total_payable-@total_discount).to_f
    days=(Date.today-@date.due_date.to_date).to_i
    
    fine_enabled = true
    student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
    unless student_fee_configuration.blank?
      if student_fee_configuration.config_value.to_i == 1
        fine_enabled = true
      else
        fine_enabled = false
      end
    end
    
    if @tmp_paid_fees.blank?
      @tmp_paid_fees = @financefee.finance_transactions
    end
    
    unless @tmp_paid_fees.blank?
      @tmp_paid_fees.each do |paid_fee|
        transaction_id = paid_fee.id
        online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
        unless online_payments.blank?
          fine_enabled = false
        end
      end
    end
    
    auto_fine=@date.fine
    
    @has_fine_discount = false 
    if days > 0 and auto_fine and @financefee.is_paid == false and fine_enabled
      @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
      @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
      calculate_extra_fine(@date, @batch, @student, @fine_rule)
      @new_fine_amount = @fine_amount
      get_fine_discount(@date, @batch, @student)
      if @fine_amount < 0
        @fine_amount = 0
      end
    end
    
    render :update do |page|
      page.replace_html "fee_submission", :partial => "fees_submission_form"
    end

  end

  def select_payment_mode
    if  params[:payment_mode]=="#{t('others')}"
      render :update do |page|
        page.replace_html "payment_mode", :partial => "select_payment_mode"
      end
    else
      render :update do |page|
        page.replace_html "payment_mode", :text=>""
      end
    end
  end

  def fees_submission_save
    advance_fee_collection = false
    @self_advance_fee = false
    @fee_has_advance_particular = false
        
    @student = Student.find(params[:student])
    @date = @fee_collection = FinanceFeeCollection.find(params[:date])
    @financefee = @date.fee_transactions(@student.id)
    
    if @financefee.has_advance_fee_id
      if @date.is_advance_fee_collection
        @self_advance_fee = true
        advance_fee_collection = true
      end
      @fee_has_advance_particular = true
      @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
      @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
      if @advance_ids.blank?
        @advance_ids[0] = 0
      end
      @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
    end
    
    #if @financefee.advance_fee_id.to_i > 0
    
    @due_date = @fee_collection.due_date
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
    unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
      exclude_particular_ids = exclude_particular_ids
    else
      exclude_particular_ids = [0]
    end
      
    if advance_fee_collection
      if @fee_collection_advances.particular_id == 0
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
      end
    else
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
    end
    
    @total_discount = 0
    if advance_fee_collection
      @total_payable = (@fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
    else  
      @total_payable = @fee_particulars.map{|s| s.amount}.sum.to_f
    end
    
    if advance_fee_collection
      calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
    else
      if @fee_has_advance_particular
        calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
      else
        calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
      end
    end
    
    total_fees = @financefee.balance.to_f+Champs21Precision.set_and_modify_precision(params[:special_fine]).to_f
    unless params[:fine].nil?
      total_fees += Champs21Precision.set_and_modify_precision(params[:fine]).to_f
    end
    unless params[:vat].nil?
      total_fees += Champs21Precision.set_and_modify_precision(params[:vat]).to_f
    end
    bal=(@total_payable-@total_discount).to_f
    days=(Date.today-@date.due_date.to_date).to_i
    
    fine_enabled = true
    student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
    unless student_fee_configuration.blank?
      if student_fee_configuration.config_value.to_i == 1
        fine_enabled = true
      else
        fine_enabled = false
      end
    end
    
    if @tmp_paid_fees.blank?
      @tmp_paid_fees = @financefee.finance_transactions
    end
    
    unless @tmp_paid_fees.blank?
      @tmp_paid_fees.each do |paid_fee|
        transaction_id = paid_fee.id
        online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
        unless online_payments.blank?
          fine_enabled = false
        end
      end
    end
    
    auto_fine=@date.fine
    
    @has_fine_discount = false
    if days > 0 and auto_fine and @financefee.is_paid == false and fine_enabled
      @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
      @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
      calculate_extra_fine(@date, @batch, @student, @fine_rule)
      @new_fine_amount = @fine_amount
      get_fine_discount(@date, @batch, @student)
      if @fine_amount < 0
        @fine_amount = 0
      end
    end

    @paid_fees = @financefee.finance_transactions

    if request.post?

      unless params[:fees][:fees_paid].to_f  <= 0
        unless params[:fees][:payment_mode].blank?
          #unless Champs21Precision.set_and_modify_precision(params[:fees][:fees_paid]).to_f > Champs21Precision.set_and_modify_precision(total_fees).to_f
          transaction = FinanceTransaction.new
          (@financefee.balance.to_f > params[:fees][:fees_paid].to_f ) ? transaction.title = "#{t('receipt_no')}. (#{t('partial')}) F#{@financefee.id}" :  transaction.title = "#{t('receipt_no')}. F#{@financefee.id}"
          transaction.category = FinanceTransactionCategory.find_by_name("Fee")
          transaction.payee = @student
          transaction.finance = @financefee
          transaction.fine_included = true  unless params[:fine].nil?
          transaction.amount = params[:fees][:fees_paid].to_f
          transaction.fine_amount = params[:fine].to_f
          transaction.vat_amount = params[:vat].to_f
          transaction.vat_included = true  unless params[:vat].nil?
          total_fine_amount = 0
          if params[:special_fine]
            total_fine_amount +=params[:special_fine].to_f
          end
          if params[:fine]
            total_fine_amount +=params[:fine].to_f
          end
            
          if total_fine_amount and total_fees==params[:fees][:fees_paid].to_f
            transaction.fine_amount = params[:fine].to_f+params[:special_fine].to_f
            transaction.fine_included = true
            @fine_amount=0
          end
          transaction.transaction_date = Date.today
          transaction.payment_mode = params[:fees][:payment_mode]
          transaction.payment_note = params[:fees][:payment_note]
          transaction.transaction_date = params[:fees][:transaction_date]
          transaction.save
          if transaction.save
            is_paid =@financefee.balance<=0 ? true : false
            @financefee.update_attributes( :is_paid=>is_paid)

            @paid_fees = @financefee.finance_transactions

            proccess_particulars_category = []
            loop_particular = 0
            @fee_particulars.each do |fp|
              finance_fee_particular_category_id = fp.finance_fee_particular_category_id
              unless proccess_particulars_category.include?(finance_fee_particular_category_id)
                proccess_particulars_category[loop_particular] = finance_fee_particular_category_id
                f_particular = @fee_particulars.select{|fpp| fpp.finance_fee_particular_category_id == finance_fee_particular_category_id }
                amount_particular = f_particular.map{|f_p| f_p.amount.to_f}.sum

                onetime_discounts = @onetime_discounts.select{ |od| od.finance_fee_particular_category_id == finance_fee_particular_category_id }
                unless onetime_discounts.nil? or onetime_discounts.empty? 
                  onetime_discounts.each do |od|
                    amount_particular = amount_particular - @onetime_discounts_amount[od.id]
                  end
                end
                unless @discounts.blank?
                  discounts = @discounts.select{ |od| od.finance_fee_particular_category_id == finance_fee_particular_category_id }
                  unless discounts.nil? or discounts.empty? 
                    discounts.each do |od|
                      amount_particular = amount_particular - @discounts_amount[od.id]
                    end
                  end
                end

                finance_transaction_particular = FinanceTransactionParticular.new
                finance_transaction_particular.finance_transaction_id = transaction.id
                finance_transaction_particular.particular_id = finance_fee_particular_category_id
                finance_transaction_particular.particular_type = 'ParticularCategory'
                finance_transaction_particular.amount = amount_particular
                finance_transaction_particular.transaction_date = transaction.transaction_date
                finance_transaction_particular.save

                loop_particular = loop_particular + 1
              end
            end

            @onetime_discounts.each do |od|
              finance_transaction_particular = FinanceTransactionParticular.new
              finance_transaction_particular.finance_transaction_id = transaction.id
              finance_transaction_particular.particular_id = od.id
              finance_transaction_particular.particular_type = 'OnetimeDiscount'
              finance_transaction_particular.amount = @onetime_discounts_amount[od.id]
              finance_transaction_particular.transaction_date = transaction.transaction_date
              finance_transaction_particular.save
            end
            unless @discounts.blank?
              @discounts.each do |od|
                finance_transaction_particular = FinanceTransactionParticular.new
                finance_transaction_particular.finance_transaction_id = transaction.id
                finance_transaction_particular.particular_id = od.id
                finance_transaction_particular.particular_type = 'Discount'
                finance_transaction_particular.amount = @discounts_amount[od.id]
                finance_transaction_particular.transaction_date = transaction.transaction_date
                finance_transaction_particular.save
              end
            end

            if transaction.vat_included?
              finance_transaction_particular = FinanceTransactionParticular.new
              finance_transaction_particular.finance_transaction_id = transaction.id
              finance_transaction_particular.particular_id = 0
              finance_transaction_particular.particular_type = 'Vat'
              finance_transaction_particular.amount = transaction.vat_amount
              finance_transaction_particular.transaction_date = transaction.transaction_date
              finance_transaction_particular.save
            end

            if total_fine_amount and Champs21Precision.set_and_modify_precision(total_fees)==params[:fees][:fees_paid]
              finance_transaction_particular = FinanceTransactionParticular.new
              finance_transaction_particular.finance_transaction_id = transaction.id
              finance_transaction_particular.particular_id = 0
              finance_transaction_particular.particular_type = 'Fine'
              finance_transaction_particular.amount = total_fine_amount
              finance_transaction_particular.transaction_date = transaction.transaction_date
              finance_transaction_particular.save
              
              transaction_date = transaction.transaction_date
              student_fee_ledger = StudentFeeLedger.new
              student_fee_ledger.student_id = @student.id
              student_fee_ledger.ledger_title = "Fine On " + transaction_date.strftime("%d %B, %Y")
              student_fee_ledger.ledger_date = transaction_date.strftime("%Y-%m-%d")
              student_fee_ledger.amount_to_pay = total_fine_amount.to_f
              student_fee_ledger.transaction_id = transaction.id
              student_fee_ledger.fee_id = @financefee.id
              student_fee_ledger.is_fine = 1
              student_fee_ledger.save
            end

            if @has_fine_discount
              @discounts_on_lates.each do |od|
                finance_transaction_particular = FinanceTransactionParticular.new
                finance_transaction_particular.finance_transaction_id = transaction.id
                finance_transaction_particular.particular_id = od.id
                finance_transaction_particular.particular_type = 'Fine Discount'
                finance_transaction_particular.amount = @discounts_late_amount[od.id]
                finance_transaction_particular.transaction_date = transaction.transaction_date
                finance_transaction_particular.save
              end
            end 
          end
          is_paid = @financefee.balance==0 ? true : false
          @financefee.update_attributes(:is_paid=>is_paid)
          flash[:warning] = "#{t('flash14')}"
          flash[:notice]=nil
          #else
          #  flash[:warning]=nil
          #  flash[:notice] = "#{t('flash19')}"
          #end
        else
          flash[:warning]=nil
          flash[:notice] = "#{t('select_one_payment_mode')}"
        end
      else
        flash[:warning]=nil
        flash[:notice] = "#{t('flash23')}"
      end
    end
    render :update do |page|
      page.replace_html "fee_submission", :partial => "fees_submission_form"
    end
  end

  def fees_student_structure_search_logic # student search fees structure
    query = params[:query]
    unless query.length < 3
      @students_result = Student.find(:all,
        :conditions => ["first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                         OR admission_no = ? OR (concat(first_name, \" \", last_name) LIKE ? ) ",
          "#{query}%","#{query}%","#{query}%","#{query}", "#{query}" ],
        :order => "batch_id asc,first_name asc") unless query == ''
    else
      @students_result = Student.find(:all,
        :conditions => ["admission_no = ? " , query],
        :order => "batch_id asc,first_name asc") unless query == ''
    end
    render :layout => false
  end

  def fees_structure_dates
    @student = Student.find(params[:id])
    #@dates = @student.batch.fee_collection_dates
    @student_fees = FinanceFee.find_all_by_student_id(@student.id,:select=>'fee_collection_id')
    @student_dates = ""
    @student_fees.map{|s| @student_dates += s.fee_collection_id.to_s + ","}
    @dates = FinanceFeeCollection.find(:all,:conditions=>"FIND_IN_SET(id,\"#{@student_dates}\") and is_deleted = 0")
  end

  def fees_structure_for_student
    advance_fee_collection = false
    @self_advance_fee = false
    @fee_has_advance_particular = false
    
    @student = Student.find(params[:id])
    @date = @fee_collection = FinanceFeeCollection.find(params[:date])
    @finance_fee=@student.finance_fee_by_date(@fee_collection)
    
    if @finance_fee.advance_fee_id.to_i > 0
      if @date.is_advance_fee_collection
        @self_advance_fee = true
        advance_fee_collection = true
      end
      
      @fee_has_advance_particular = true
      @advance_id = @finance_fee.advance_fee_id
      @fee_collection_advances = FinanceFeeAdvance.find(@advance_id)
    end
    
    exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
    unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
      exclude_particular_ids = exclude_particular_ids
    else
      exclude_particular_ids = [0]
    end
    
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    if advance_fee_collection
      if @fee_collection_advances.particular_id == 0
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@finance_fee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@finance_fee.batch) }
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@finance_fee.batch_id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@finance_fee.batch) }
      end
    else
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@finance_fee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@finance_fee.batch) }
    end
    
    @total_discount = 0
    if advance_fee_collection
      @total_payable = (@fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
    else  
      @total_payable = @fee_particulars.map{|s| s.amount}.sum.to_f
    end
    
    if advance_fee_collection
      calculate_discount(@fee_collection, @finance_fee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
    else
      if @fee_has_advance_particular
        calculate_discount(@fee_collection, @finance_fee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
      else
        calculate_discount(@fee_collection, @finance_fee.batch, @student, false, nil, @fee_has_advance_particular)
      end
    end
    
    render :update do |page|
      page.replace_html "fees_structure" , :partial => "fees_structure"
    end
  end

  def student_fees_structure
    @student = Student.find(params[:id])
    @fee_collection = FinanceFeeCollection.find params[:id2]
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    @fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"batch_id=#{@student.batch_id} and is_deleted=#{false}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) and (!par.is_deleted and par.batch_id==@student.batch_id)}

  end

  def fees_defaulters
    @batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>true},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
    batches = @batches.map{|b| b.course_id}
    #@courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    @courses = []
    @dates = []
    @sections = []
  end

  def fine_configuration
    @batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>true},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
    batches = @batches.map{|b| b.course_id}
    #@courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    @courses = []
    @dates = []
    @sections = []
  end

  def single_payment_permission
    @batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>true},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
    batches = @batches.map{|b| b.course_id}
    #@courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    @courses = []
    @dates = []
    @sections = []
    
    @student_fee_configurations = StudentFeeConfiguration.find(:all, :conditions => "config_key = 'combined_payment_student' and config_value = #{false}")
  end

  def filter_single_payment_permission
    @student_fee_configurations = []
    unless params[:student_admission_no].blank?
      @student_fee_configurations = StudentFeeConfiguration.find(:all, :joins => "INNER JOIN students ON students.id = student_fee_configurations.student_id", :conditions => "student_fee_configurations.config_key = 'combined_payment_student' and student_fee_configurations.config_value = #{false} and students.admission_no = '#{params[:student_admission_no]}'")
    else
      if params[:student_admission_no].blank?
        @student_fee_configurations = StudentFeeConfiguration.find(:all, :conditions => "config_key = 'combined_payment_student' and config_value = #{false}")
      end
    end
    
    render :update do |page|
      page.replace_html "student" , :partial => "single_permission_students"
    end
  end

  def single_payment_permission_filter
    @student_fee_configurations = []
    if params[:opt].nil?
      unless params[:batch_id].blank?
        batch_id = params[:batch_id]
        @student_fee_configurations = StudentFeeConfiguration.find(:all, :joins => "INNER JOIN students ON students.id = student_fee_configurations.student_id INNER JOIN batches ON batches.id = students.batch_id", :conditions => "student_fee_configurations.config_key = 'combined_payment_student' and student_fee_configurations.config_value = #{false} and batches.id = '#{batch_id}'")
      else
        @student_fee_configurations = StudentFeeConfiguration.find(:all, :conditions => "config_key = 'combined_payment_student' and config_value = #{false}")
      end
    else
      if params[:opt].to_i == 1
        @filter_by_course = params[:filter_by_course];
        @d_id = params[:date];
        if params[:filter_by_course].to_i == 1
          eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
          tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
          hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
          college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
          college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
          if college_courses_id.blank?
            college_courses_id[0] = 0
          end
          school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
          school_course_id = school_course_id.reject { |s| s.to_s.empty? }
          if school_course_id.blank?
            school_course_id[0] = 0
          end
          batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
        elsif params[:filter_by_course].to_i == 2
          eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
          tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
          hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
          college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
          college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
          if college_courses_id.blank?
            college_courses_id[0] = 0
          end
          #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
          batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          #if batches.blank?
          #  batches[0] = 0
          #end
        else
          batches = Batch.all.map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          #if batches.blank?
          #  batches[0] = 0
          #end
        end
        #@student_fee_configurations = StudentFeeConfiguration.find(:all, :joins => "INNER JOIN students ON students.id = student_fee_configurations.student_id INNER JOIN batches ON batches.id = students.batch_id", :conditions => "student_fee_configurations.config_key = 'combined_payment_student' and student_fee_configurations.config_value = #{false} and batches.id IN '#{batches.join(",")}'")
        unless batches.blank?
          @student_fee_configurations = StudentFeeConfiguration.find(:all, :joins => "INNER JOIN students ON students.id = student_fee_configurations.student_id INNER JOIN batches ON batches.id = students.batch_id", :conditions => "student_fee_configurations.config_key = 'combined_payment_student' and student_fee_configurations.config_value = #{false} and batches.id IN (#{batches.join(",")})")
        else
          @student_fee_configurations = StudentFeeConfiguration.find(:all, :conditions => "config_key = 'combined_payment_student' and config_value = #{false}")
        end
      else  
          batch_id = 0
          course_id = 0
          class_id = 0
          unless params[:batch_id].nil?
            batch_id = params[:batch_id]
          end

          unless params[:course_name].nil?
            class_id = params[:course_name]
          end

          unless params[:section_id].blank?
            course_id = params[:section_id]
          end

          batch_name = ""
          batches = [0]
          if batch_id.to_i > 0
            batch = Batch.find batch_id
            batch_name = batch.name
          end

          class_name = ""
          if class_id.to_i > 0
            course = Course.find class_id
            class_name = course.course_name
          end

          unless batch_name.blank?
            if course_id == 0
              batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
              #abort(bclass_name.)
              unless class_id == 0
                courses = batches_all.map{|b| b.course_id}   
                #abort(courses.inspect)
                #batches = batches_all.map{|b| b.id}
                @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      

                @dates = []
                unless @sections.blank?
                  batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
                  #batches = batches_all.map{|b| b.id}
                end
              end
            else
              batches_all = Batch.find_all_by_name_and_is_deleted_and_course_id(batch_name,false, course_id)
            end

            batches = batches_all.map{|b| b.id}    
          end
          
          unless batches.blank?
            @student_fee_configurations = StudentFeeConfiguration.find(:all, :joins => "INNER JOIN students ON students.id = student_fee_configurations.student_id INNER JOIN batches ON batches.id = students.batch_id", :conditions => "student_fee_configurations.config_key = 'combined_payment_student' and student_fee_configurations.config_value = #{false} and batches.id IN (#{batches.join(",")})")
          else
            @student_fee_configurations = StudentFeeConfiguration.find(:all, :conditions => "config_key = 'combined_payment_student' and config_value = #{false}")
          end
      end
    end
    render :update do |page|
      page.replace_html "student" , :partial => "single_permission_students"
    end
  end

  def update_batches
    @course = Course.find(params[:course_id])
    @batchs = @course.batches

    render :update do |page|
      page.replace_html "batches_list", :partial => "batches_list"
    end
  end

  def set_order_id_with_finance_transaction
    unless params[:transaction_id].nil? and params[:payment_id].nil?
      transaction_id = params[:transaction_id]
      payment_id = params[:payment_id]
      student_id = params[:student_id]
      finance_id = params[:finance_id]
      payment = Payment.find_by_id_and_payee_id(payment_id, student_id)
      @transaction = FinanceTransaction.find_by_id_and_payee_id(transaction_id, student_id)
      @finance_fee = FinanceFee.find_by_id_and_student_id(finance_id, student_id)
      unless payment.blank? and @transaction.blank? and @finance_fee.blank?
        payment.update_attributes(:finance_transaction_id=>@transaction.id, :payment_id => @finance_fee.id)
        student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{student_id} and fee_id = #{@finance_fee.id} and transaction_id = #{@transaction.id} and is_fine = #{true}")
        unless student_fee_ledgers.blank?
          student_fee_ledgers.each do |student_fee_ledger|
            unless payment.transaction_datetime.blank?
              student_fee_ledger.update_attributes(:ledger_date => payment.transaction_datetime.strftime("%Y-%m-%d"), :ledger_title => "Fine On " + payment.transaction_datetime.strftime("%d %B, %Y"))
            else
              student_fee_ledger.update_attributes(:ledger_date => @transaction.transaction_date.strftime("%Y-%m-%d"), :ledger_title => "Fine On " + @transaction.transaction_date.strftime("%d %B, %Y"))
            end
          end
        end
        
        student_fee_ledger = StudentFeeLedger.find(:first, :conditions => "student_id = #{student_id} and fee_id = #{@finance_fee.id} and transaction_id = #{@transaction.id} and is_fine = #{false}")
        unless student_fee_ledger.blank?
          amount_paid = student_fee_ledger.amount_paid
          if amount_paid.to_f != @transaction.amount.to_f
            student_fee_ledger.update_attributes(:amount_paid => @transaction.amount.to_f)
          end
        else
          student_fee_ledger = StudentFeeLedger.new
          student_fee_ledger.student_id = student_id
          unless payment.transaction_datetime.blank?
            student_fee_ledger.ledger_date = payment.transaction_datetime.strftime("%Y-%m-%d")
          else
            student_fee_ledger.ledger_date = @transaction.transaction_date.strftime("%Y-%m-%d")
          end
          student_fee_ledger.ledger_title = @finance_fee.finance_fee_collection.title  
          student_fee_ledger.amount_to_pay = 0.00
          student_fee_ledger.fee_id = @finance_fee.id
          student_fee_ledger.amount_paid = @transaction.amount.to_f
          student_fee_ledger.transaction_id = @transaction.id
          student_fee_ledger.order_id = payment.order_id
          student_fee_ledger.save
        end
        render :update do |page|
          page.replace_html "payment_mode_order_id", :partial => 'order_id_for_transaction'
          page << "j('#payment_mode_order_id').css('text-align', 'left')"
        end
      end
    end
  end
  
  def update_fees_collection_dates_defaulters
    @batch  = Batch.find(params[:batch_id])
    @dates = @batch.finance_fee_collections.all(:order => "start_date DESC")
    render :update do |page|
      page.replace_html "fees_collection_dates", :partial => "fees_collection_dates_defaulters"
      page << "j('#fees_defaulters_dates_id').select2();"
    end
  end
  
  def update_fine_configurations_date
    @batch  = Batch.find(params[:batch_id])
    @dates = @batch.finance_fee_collections.all(:order => "start_date DESC", :conditions => "fine_id is not null")
    render :update do |page|
      page.replace_html "fine_configuration_dates_div", :partial => "fine_configuration_dates"
      page << "j('#fine_configuration_dates_id').select2();"
    end
  end

  def update_fees_collection_dates_defaulters_school_college
    unless params[:filter_by_course].blank?
      if params[:filter_by_course].to_i == 1
        eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
        tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
        hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
        college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
        college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
        if college_courses_id.blank?
          college_courses_id[0] = 0
        end
        school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
        school_course_id = school_course_id.reject { |s| s.to_s.empty? }
        if school_course_id.blank?
          school_course_id[0] = 0
        end
        batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
      elsif params[:filter_by_course].to_i == 2
        eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
        tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
        hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
        college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
        college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
        if college_courses_id.blank?
          college_courses_id[0] = 0
        end
        #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
        batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
      else
        batches = Batch.all.map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
      end

      @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false}", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
      finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
      if finance_fee_collection_ids.blank?
        finance_fee_collection_ids[0] = 0
      end
      @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false}", :group => "name", :order => "start_date desc")

      render :update do |page|
        page.replace_html "fees_collection_dates_school_college", :partial => "fees_collection_dates_defaulters_school_college"
        page << "j('#fees_defaulters_dates_id_school_college').select2();"
      end
    else
      @dates = []
      render :update do |page|
        page.replace_html "fees_collection_dates_school_college", :partial => "fees_collection_dates_defaulters_school_college"
        page << "j('#fees_defaulters_dates_id_school_college').select2();"
      end
    end
  end
  
  def update_fine_configuration_school_college
    unless params[:filter_by_course].blank?
      if params[:filter_by_course].to_i == 1
        eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
        tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
        hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
        college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
        college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
        if college_courses_id.blank?
          college_courses_id[0] = 0
        end
        school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
        school_course_id = school_course_id.reject { |s| s.to_s.empty? }
        if school_course_id.blank?
          school_course_id[0] = 0
        end
        batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
      elsif params[:filter_by_course].to_i == 2
        eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
        tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
        hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
        college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
        college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
        if college_courses_id.blank?
          college_courses_id[0] = 0
        end
        #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
        batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
      else
        batches = Batch.all.map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
      end

      @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false} and finance_fee_collections.fine_id is not null", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
      finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
      if finance_fee_collection_ids.blank?
        finance_fee_collection_ids[0] = 0
      end
      @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false} and fine_id is not null", :group => "name", :order => "start_date desc")

      render :update do |page|
        page.replace_html "fine_configuration_dates_school_college", :partial => "fine_configuration_school_college"
        page << "j('#fine_configuration_dates_id_school_college').select2();"
      end
    else
      @dates = []
      render :update do |page|
        page.replace_html "fine_configuration_dates_school_college", :partial => "fine_configuration_school_college"
        page << "j('#fine_configuration_dates_id_school_college').select2();"
      end
    end
  end

  def update_fees_collection_bill_generation_school_college
    unless params[:filter_by_course].blank?
      if params[:filter_by_course].to_i == 1
        eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
        tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
        hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
        college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
        college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
        if college_courses_id.blank?
          college_courses_id[0] = 0
        end
        school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
        school_course_id = school_course_id.reject { |s| s.to_s.empty? }
        if school_course_id.blank?
          school_course_id[0] = 0
        end
        batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
      elsif params[:filter_by_course].to_i == 2
        eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
        tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
        hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
        college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
        college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
        if college_courses_id.blank?
          college_courses_id[0] = 0
        end
        #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
        batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
      else
        batches = Batch.all.map(&:id)
        batches = batches.reject { |b| b.to_s.empty? }
        if batches.blank?
          batches[0] = 0
        end
      end

      @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false}", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
      finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
      if finance_fee_collection_ids.blank?
        finance_fee_collection_ids[0] = 0
      end
      @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false}", :group => "name", :order => "start_date desc")
      #abort(@dates.map(&:id).inspect)
      render :update do |page|
        page.replace_html "fees_collection_bill_school_college", :partial => "fees_collection_bill_school_college"
        page << "j('#bill_generation_dates_id_school_college').select2();"
        page.replace_html "student", :text => ""
        page << "j('#student').hide()"
      end
    else
      @dates = []
      #abort(@dates.map(&:id).inspect)
      render :update do |page|
        page.replace_html "fees_collection_bill_school_college", :partial => "fees_collection_bill_school_college"
        page << "j('#bill_generation_dates_id_school_college').select2();"
        page.replace_html "student", :text => ""
        page << "j('#student').hide()"
      end
    end
  end

  def update_fees_collection_dates_defaulters_sections
    batch_id = 0
    course_id = 0
    section_id = 0
    unless params[:section_id].blank?
      section_id = params[:section_id]
    end
    
    unless params[:course_id].blank?
      course_id = params[:course_id]
    end
    
    unless params[:batch_id].blank?
      batch_id = params[:batch_id]
    end
    
    batch_name = ""
    batches = [0]
    if batch_id.to_i > 0
      batch = Batch.find batch_id
      batch_name = batch.name
    end
    
    class_name = ""
    if course_id.to_i > 0
      course = Course.find course_id
      class_name = course.course_name
    end

    unless batch_name.blank?
      if section_id == 0
        batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
        unless course_id == 0
          courses = batches_all.map{|b| b.course_id}    
          #batches = batches_all.map{|b| b.id}
          @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      

          @dates = []
          unless @sections.blank?
            batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
            #batches = batches_all.map{|b| b.id}
          end
        end
      else
        batches_all = Batch.find_all_by_name_and_is_deleted_and_course_id(batch_name,false, section_id)
      end

      batches = batches_all.map{|b| b.id}    
    end
    if batches.blank?
      batches[0] = 0
    end
    
    @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false}", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
    finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
    if finance_fee_collection_ids.blank?
      finance_fee_collection_ids[0] = 0
    end
    @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false}", :group => "name", :order => "start_date desc")
    
    render :update do |page|
      page.replace_html "fees_collection_dates_defaulters_sections", :partial => "fees_collection_dates_defaulters_sections"
      page << "j('#fees_defaulters_sections').select2();"
    end
  end

  def update_fine_configuration_sections
    batch_id = 0
    course_id = 0
    section_id = 0
    unless params[:section_id].blank?
      section_id = params[:section_id]
    end
    
    unless params[:course_id].blank?
      course_id = params[:course_id]
    end
    
    unless params[:batch_id].blank?
      batch_id = params[:batch_id]
    end
    
    batch_name = ""
    batches = [0]
    if batch_id.to_i > 0
      batch = Batch.find batch_id
      batch_name = batch.name
    end
    
    class_name = ""
    if course_id.to_i > 0
      course = Course.find course_id
      class_name = course.course_name
    end

    unless batch_name.blank?
      if section_id == 0
        batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
        unless course_id == 0
          courses = batches_all.map{|b| b.course_id}    
          #batches = batches_all.map{|b| b.id}
          @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      

          @dates = []
          unless @sections.blank?
            batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
            #batches = batches_all.map{|b| b.id}
          end
        end
      else
        batches_all = Batch.find_all_by_name_and_is_deleted_and_course_id(batch_name,false, section_id)
      end

      batches = batches_all.map{|b| b.id}    
    end
    if batches.blank?
      batches[0] = 0
    end
    
    @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false} and finance_fee_collections.fine_id is not null", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
    finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
    if finance_fee_collection_ids.blank?
      finance_fee_collection_ids[0] = 0
    end
    @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false} and fine_id is not null", :group => "name", :order => "start_date desc")
    
    render :update do |page|
      page.replace_html "fine_configuration_sections_div", :partial => "fine_configuration_sections"
      page << "j('#fine_configuration_sections').select2();"
    end
  end

  def update_fees_collection_bill_generation_sections
    batch_id = 0
    course_id = 0
    section_id = 0
    unless params[:section_id].blank?
      section_id = params[:section_id]
    end
    
    unless params[:course_id].blank?
      course_id = params[:course_id]
    end
    
    unless params[:batch_id].blank?
      batch_id = params[:batch_id]
    end
    
    batch_name = ""
    batches = [0]
    if batch_id.to_i > 0
      batch = Batch.find batch_id
      batch_name = batch.name
    end
    
    class_name = ""
    if course_id.to_i > 0
      course = Course.find course_id
      class_name = course.course_name
    end

    unless batch_name.blank?
      if section_id == 0
        batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
        unless course_id == 0
          courses = batches_all.map{|b| b.course_id}    
          #batches = batches_all.map{|b| b.id}
          @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      

          @dates = []
          unless @sections.blank?
            batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
            #batches = batches_all.map{|b| b.id}
          end
        end
      else
        batches_all = Batch.find_all_by_name_and_is_deleted_and_course_id(batch_name,false, section_id)
      end

      batches = batches_all.map{|b| b.id}    
    end
    if batches.blank?
      batches[0] = 0
    end
    
    @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false}", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
    finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
    if finance_fee_collection_ids.blank?
      finance_fee_collection_ids[0] = 0
    end
    @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false}", :group => "name", :order => "start_date desc")
    
    render :update do |page|
      page.replace_html "fees_collection_bill_generation_sections", :partial => "fees_collection_bill_generation_sections"
      page << "j('#bill_generation_sections').select2();"
      page.replace_html "student", :text => ""
      page << "j('#student').hide()"
    end
  end
  
  def get_section_data_finance
    @batch_name = ""
    @class_name = ""
    @sections = []
    @dates = []
    
    unless params[:batch_id].blank?
      batch_id = 0
      unless params[:batch_id].nil?
        batch_id = params[:batch_id]
      end
      
      class_id = 0
      unless params[:class_name].nil?
        class_id = params[:class_name]
      end

      batch_name = ""
      if batch_id.to_i > 0
        batch = Batch.find batch_id
        batch_name = batch.name
        @batch_name = batch_name
      end
      
      class_name = ""
      if class_id.to_i > 0
        course = Course.find class_id
        class_name = course.course_name
        @class_name = class_name
      end

      unless params[:class_name].blank?
        batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)

        courses = batches_all.map{|b| b.course_id}    
        #batches = batches_all.map{|b| b.id}
        @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      
        
        @dates = []
        unless @sections.blank?
          batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
          batches = batches_all.map{|b| b.id}
          #abort(batches.inspect)
          
          @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false}", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
          finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
          if finance_fee_collection_ids.blank?
            finance_fee_collection_ids[0] = 0
          end
          @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false}", :group => "name", :order => "start_date desc")
        end
      else 
        batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)

        #courses = batches_all.map{|b| b.course_id}    
        batches = batches_all.map{|b| b.id}
        @sections = []

        @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false}", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
        finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
        if finance_fee_collection_ids.blank?
          finance_fee_collection_ids[0] = 0
        end
        @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false}", :group => "name", :order => "start_date desc")
        
        #@sections =  Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0",class_name]) 
        #@dates = []
      end
    end
    render :update do |page|
      page.replace_html 'batches', :partial => 'batches_finance', :object => @sections
      page.replace_html "fees_collection_dates_defaulters_sections", :partial => "fees_collection_dates_defaulters_sections"
      page << "j('#fees_defaulters_sections').select2();"
    end
  end
  
  def get_section_data_finance_for_fine_settings
    @batch_name = ""
    @class_name = ""
    @sections = []
    @dates = []
    
    unless params[:batch_id].blank?
      batch_id = 0
      unless params[:batch_id].nil?
        batch_id = params[:batch_id]
      end
      
      class_id = 0
      unless params[:class_name].nil?
        class_id = params[:class_name]
      end

      batch_name = ""
      if batch_id.to_i > 0
        batch = Batch.find batch_id
        batch_name = batch.name
        @batch_name = batch_name
      end
      
      class_name = ""
      if class_id.to_i > 0
        course = Course.find class_id
        class_name = course.course_name
        @class_name = class_name
      end

      unless params[:class_name].blank?
        batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)

        courses = batches_all.map{|b| b.course_id}    
        #batches = batches_all.map{|b| b.id}
        @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      
        
        @dates = []
        unless @sections.blank?
          batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
          batches = batches_all.map{|b| b.id}
          #abort(batches.inspect)
          
          @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false} and finance_fee_collections.fine_id is not null", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
          finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
          if finance_fee_collection_ids.blank?
            finance_fee_collection_ids[0] = 0
          end
          @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false} and fine_id is not null", :group => "name", :order => "start_date desc")
        end
      else 
        batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)

        #courses = batches_all.map{|b| b.course_id}    
        batches = batches_all.map{|b| b.id}
        @sections = []

        @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false} and finance_fee_collections.fine_id is not null", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
        finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
        if finance_fee_collection_ids.blank?
          finance_fee_collection_ids[0] = 0
        end
        @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false} and fine_id is not null", :group => "name", :order => "start_date desc")
        
        #@sections =  Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0",class_name]) 
        #@dates = []
      end
    end
    render :update do |page|
      page.replace_html 'batches', :partial => 'batches_finance_for_fine_settings', :object => @sections
      page.replace_html "fine_configuration_sections_div", :partial => "fine_configuration_sections"
      page << "j('#fine_configuration_sections').select2();"
    end
  end
  
  def get_section_data_for_bill
    @batch_name = ""
    @class_name = ""
    @sections = []
    @dates = []
    
    unless params[:batch_id].blank?
      batch_id = 0
      unless params[:batch_id].nil?
        batch_id = params[:batch_id]
      end
      
      class_id = 0
      unless params[:class_name].nil?
        class_id = params[:class_name]
      end

      batch_name = ""
      if batch_id.to_i > 0
        batch = Batch.find batch_id
        batch_name = batch.name
        @batch_name = batch_name
      end
      
      class_name = ""
      if class_id.to_i > 0
        course = Course.find class_id
        class_name = course.course_name
        @class_name = class_name
      end

      unless params[:class_name].blank?
        batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)

        courses = batches_all.map{|b| b.course_id}    
        #batches = batches_all.map{|b| b.id}
        @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      
        
        @dates = []
        unless @sections.blank?
          batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
          batches = batches_all.map{|b| b.id}
          #abort(batches.inspect)
          
          @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false}", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
          finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
          if finance_fee_collection_ids.blank?
            finance_fee_collection_ids[0] = 0
          end
          @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false}", :group => "name", :order => "start_date desc")
        end
      else 
        batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)

        #courses = batches_all.map{|b| b.course_id}    
        batches = batches_all.map{|b| b.id}
        @sections = []

        @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false}", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
        finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
        if finance_fee_collection_ids.blank?
          finance_fee_collection_ids[0] = 0
        end
        @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false}", :group => "name", :order => "start_date desc")
        
        #@sections =  Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0",class_name]) 
        #@dates = []
      end
    end
    render :update do |page|
      page.replace_html 'batches', :partial => 'batches_finance_for_bill', :object => @sections
      page.replace_html "fees_collection_bill_generation_sections", :partial => "fees_collection_bill_generation_sections"
      page << "j('#bill_generation_sections').select2();"
      page.replace_html "student", :text => ""
      page << "j('#student').hide()"
      
#      page.replace_html 'batches', :partial => 'batches_finance', :object => @sections
#      page.replace_html "fees_collection_dates_defaulters_sections", :partial => "fees_collection_dates_defaulters_sections"
#      page << "j('#fees_defaulters_sections').select2();"
    end
  end
  
  def get_section_data_single
    @batch_name = ""
    @class_name = ""
    @sections = []
    @dates = []
    
    unless params[:batch_id].blank?
      batch_id = 0
      unless params[:batch_id].nil?
        batch_id = params[:batch_id]
      end
      
      class_id = 0
      unless params[:class_name].nil?
        class_id = params[:class_name]
      end

      batch_name = ""
      if batch_id.to_i > 0
        batch = Batch.find batch_id
        batch_name = batch.name
        @batch_name = batch_name
      end
      
      class_name = ""
      if class_id.to_i > 0
        course = Course.find class_id
        class_name = course.course_name
        @class_name = class_name
      end

      unless params[:class_name].blank?
        batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)

        courses = batches_all.map{|b| b.course_id}    
        #atches = batches_all.map{|b| b.id}
        
        @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      
        
        unless @sections.blank?
          batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
          batches = batches_all.map{|b| b.id}
          #abort(batches.inspect)
          
          unless batches.blank?
            @student_fee_configurations = StudentFeeConfiguration.find(:all, :joins => "INNER JOIN students ON students.id = student_fee_configurations.student_id INNER JOIN batches ON batches.id = students.batch_id", :conditions => "student_fee_configurations.config_key = 'combined_payment_student' and student_fee_configurations.config_value = #{false} and batches.id IN (#{batches.join(",")})")
          end
        end
      else 
        @sections = []
        @student_fee_configurations = StudentFeeConfiguration.find(:all, :conditions => "config_key = 'combined_payment_student' and config_value = #{false}")
      end
    else
      @student_fee_configurations = StudentFeeConfiguration.find(:all, :conditions => "config_key = 'combined_payment_student' and config_value = #{false}")
    end
    render :update do |page|
      page.replace_html 'batches', :partial => 'batches_single', :object => @sections
      page.replace_html "student" , :partial => "single_permission_students"
    end
  end
  
  def get_classes_finance
    @courses = []
    @dates = []
    unless params[:batch_id].blank?
      @batch_name = false
      unless params[:batch_id].empty?
        batch_data = Batch.find params[:batch_id]
        batch_name = batch_data.name
      end 

      batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)

      courses = batches_all.map{|b| b.course_id}
      batches = batches_all.map{|b| b.id}

      @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false}", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
      finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
      if finance_fee_collection_ids.blank?
        finance_fee_collection_ids[0] = 0
      end
      @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false}", :group => "name", :order => "start_date desc")

      @courses = Course.find(:all, :conditions => ["id IN (?) and is_deleted = 0", courses], :group => "course_name", :select => "id,course_name,no_call", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
      #abort(@courses.inspect)
    end
    @sections = []
    render :update do |page|
      page.replace_html "course_data", :partial => 'courses_data', :object => @courses
      page.replace_html 'batches', :partial => 'batches_finance', :object => @sections
      page.replace_html "fees_collection_dates_defaulters_sections", :partial => "fees_collection_dates_defaulters_sections"
      page << "j('#fees_defaulters_sections').select2();"
    end
  end
  
  def get_classes_finance_for_fine_settings
    @courses = []
    @dates = []
    unless params[:batch_id].blank?
      @batch_name = false
      unless params[:batch_id].empty?
        batch_data = Batch.find params[:batch_id]
        batch_name = batch_data.name
      end 

      batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)

      courses = batches_all.map{|b| b.course_id}
      batches = batches_all.map{|b| b.id}

      @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false}", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
      finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
      if finance_fee_collection_ids.blank?
        finance_fee_collection_ids[0] = 0
      end
      @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false} and fine_id is not null", :group => "name", :order => "start_date desc")
      @courses = Course.find(:all, :conditions => ["id IN (?) and is_deleted = 0", courses], :group => "course_name", :select => "id,course_name,no_call", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
      #abort(@courses.inspect)
    end
    @sections = []
    render :update do |page|
      page.replace_html "course_data", :partial => 'courses_data_for_fine_settings', :object => @courses
      page.replace_html 'batches', :partial => 'batches_finance_for_fine_settings', :object => @sections
      page.replace_html "fine_configuration_sections_div", :partial => "fine_configuration_sections"
      page << "j('#fine_configuration_sections').select2();"
    end
  end
  
  def get_classes_finance_for_bill
    @courses = []
    @dates = []
    unless params[:batch_id].blank?
      @batch_name = false
      unless params[:batch_id].empty?
        batch_data = Batch.find params[:batch_id]
        batch_name = batch_data.name
      end 

      batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)

      courses = batches_all.map{|b| b.course_id}
      batches = batches_all.map{|b| b.id}

      @fee_collection_batches = FeeCollectionBatch.find(:all, :conditions => "fee_collection_batches.batch_id IN (#{batches.join(",")}) and finance_fee_collections.is_deleted = #{false}", :joins => "INNER JOIN finance_fee_collections ON finance_fee_collections.id = fee_collection_batches.finance_fee_collection_id", :group => "fee_collection_batches.finance_fee_collection_id")
      finance_fee_collection_ids = @fee_collection_batches.map(&:finance_fee_collection_id)
      if finance_fee_collection_ids.blank?
        finance_fee_collection_ids[0] = 0
      end
      @dates = FinanceFeeCollection.find(:all, :conditions => "id IN (#{finance_fee_collection_ids.join(",")}) and is_deleted = #{false}", :group => "name", :order => "start_date desc")

      @courses = Course.find(:all, :conditions => ["id IN (?) and is_deleted = 0", courses], :group => "course_name", :select => "id,course_name,no_call", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
      #abort(@courses.inspect)
    end
    @sections = []
    render :update do |page|
      page.replace_html "course_data_for_bill", :partial => 'courses_data_for_bill', :object => @courses
      page.replace_html 'batches', :partial => 'batches_finance_for_bill', :object => @sections
      page.replace_html "fees_collection_bill_generation_sections", :partial => "fees_collection_bill_generation_sections"
      page.replace_html "student", :text => ""
      page << "j('#student').hide()"
      page << "j('#bill_generation_sections').select2();"
    end
  end
  
  def get_classes_single_payment
    @courses = []
    @dates = []
    unless params[:batch_id].blank?
      @batch_name = false
      unless params[:batch_id].empty?
        batch_data = Batch.find params[:batch_id]
        batch_name = batch_data.name
      end 

      batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)

      courses = batches_all.map{|b| b.course_id}
      batches = batches_all.map{|b| b.id}

      @courses = Course.find(:all, :conditions => ["id IN (?) and is_deleted = 0", courses], :group => "course_name", :select => "id,course_name,no_call", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
      
      unless batches.blank?
        @student_fee_configurations = StudentFeeConfiguration.find(:all, :joins => "INNER JOIN students ON students.id = student_fee_configurations.student_id INNER JOIN batches ON batches.id = students.batch_id", :conditions => "student_fee_configurations.config_key = 'combined_payment_student' and student_fee_configurations.config_value = #{false} and batches.id IN (#{batches.join(",")})")
      end
    else
      @student_fee_configurations = StudentFeeConfiguration.find(:all, :conditions => "config_key = 'combined_payment_student' and config_value = #{false}")
    end
    @sections = []
    render :update do |page|
      page.replace_html "course_data", :partial => 'courses_data_single', :object => @courses
      page.replace_html 'batches', :partial => 'batches_finance', :object => @sections
      page.replace_html "student" , :partial => "single_permission_students"
    end
  end

  def fees_defaulters_students
    @multi_date = false
    @defaulters = []
    if params[:opt].nil?
      @opt = 0;
      @b_id = params[:batch_id];
      @d_id = params[:date];
      @batch   = Batch.find(params[:batch_id])
      #@students = @batch.students
      unless params[:date].blank?
        unless params[:date] == '0'
          @date = FinanceFeeCollection.find(params[:date])
          @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id='#{@date.id}' and finance_fees.balance > 0 and students.batch_id='#{@batch.id}'"],:order=>"students.admission_no ASC").uniq
        else
          @date = 0
          @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id",:conditions=>["finance_fees.balance > 0 and students.batch_id='#{@batch.id}'"],:order=>"students.admission_no ASC").uniq
        end
        render :update do |page|
          page << "j('#student').show();"
          page.replace_html "student", :partial => "student_defaulters"
        end
      else
        render :update do |page|
          page << "j('#student').hide();"
        end
      end
    else
      if params[:opt].to_i == 1
        @opt = 1;
        @filter_by_course = params[:filter_by_course];
        @d_id = params[:date];
        if params[:filter_by_course].to_i == 1
          eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
          tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
          hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
          college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
          college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
          if college_courses_id.blank?
            college_courses_id[0] = 0
          end
          school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
          school_course_id = school_course_id.reject { |s| s.to_s.empty? }
          if school_course_id.blank?
            school_course_id[0] = 0
          end
          batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
        elsif params[:filter_by_course].to_i == 2
          eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
          tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
          hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
          college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
          college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
          if college_courses_id.blank?
            college_courses_id[0] = 0
          end
          #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
          batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
        else
          batches = Batch.all.map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
        end
        #@students = Student.find(:all,:conditions=>["batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
        unless params[:date].blank?
          unless params[:date] == '0'
            @multi_date = true
            @date = FinanceFeeCollection.find(params[:date])
            unless @date.blank?
              @date_name = @date.name
              @dates = FinanceFeeCollection.find_all_by_name(@date_name)
              unless @dates.blank?
                @dates_id = @dates.map(&:id)
                if @dates_id.blank?
                  @dates_id[0] = 0
                end
              else
                @dates_id[0] = 0
              end
              #abort(@dates.map(&:id).inspect)
              @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id IN (#{@dates_id.join(",")}) and finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
              #abort(@defaulters.inspect)
            end
          else
            @date = 0
            @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id",:conditions=>["finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
          end
          render :update do |page|
            page << "j('#student').show();"
            page.replace_html "student", :partial => "student_defaulters"
          end
        else
          render :update do |page|
            page << "j('#student').hide();"
          end
        end
      else
        @opt = 2;
        @b_id = params[:batch_id]
        @section_id = params[:section_id]
        @course_name = params[:course_name]
        @d_id = params[:date]
        batch_id = 0
        course_id = 0
        class_id = 0
        unless params[:batch_id].nil?
          batch_id = params[:batch_id]
        end
        
        unless params[:course_name].nil?
          class_id = params[:course_name]
        end

        unless params[:section_id].blank?
          course_id = params[:section_id]
        end

        batch_name = ""
        batches = [0]
        if batch_id.to_i > 0
          batch = Batch.find batch_id
          batch_name = batch.name
        end
        
        class_name = ""
        if class_id.to_i > 0
          course = Course.find class_id
          class_name = course.course_name
        end

        unless batch_name.blank?
          if course_id == 0
            batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
            #abort(bclass_name.)
            unless class_id == 0
              courses = batches_all.map{|b| b.course_id}   
              #abort(courses.inspect)
              #batches = batches_all.map{|b| b.id}
              @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      

              @dates = []
              unless @sections.blank?
                batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
                #batches = batches_all.map{|b| b.id}
              end
            end
          else
            batches_all = Batch.find_all_by_name_and_is_deleted_and_course_id(batch_name,false, course_id)
          end

          batches = batches_all.map{|b| b.id}    
        end
        if batches.blank?
          batches[0] = 0
        end
        #abort(batches.inspect)
        #@students = Student.find(:all,:conditions=>["batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
        unless params[:date].blank?
          unless params[:date] == '0'
            if course_id == 0
              @multi_date = true
              @date = FinanceFeeCollection.find(params[:date])
              unless @date.blank?
                @date_name = @date.name
                @dates = FinanceFeeCollection.find_all_by_name(@date_name)
                unless @dates.blank?
                  @dates_id = @dates.map(&:id)
                  if @dates_id.blank?
                    @dates_id[0] = 0
                  end
                else
                  @dates_id[0] = 0
                end
                #abort(@dates.map(&:id).inspect)
                @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id IN (#{@dates_id.join(",")}) and finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
                #abort(@defaulters.inspect)
              end
            else  
              @date = FinanceFeeCollection.find(params[:date])
              @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id='#{@date.id}' and finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
            end
          else
            @date = 0
            @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id",:conditions=>["finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
          end
          render :update do |page|
            page << "j('#student').show();"
            page.replace_html "student", :partial => "student_defaulters"
          end
        else
          render :update do |page|
            page << "j('#student').hide();"
          end
        end
      end
    end
  end
  
  def fine_configuration_students
    @multi_date = false
    @defaulters = []
    if params[:opt].nil?
      @opt = 0;
      @b_id = params[:batch_id];
      @d_id = params[:date];
      @batch   = Batch.find(params[:batch_id])
      #@students = @batch.students
      unless params[:date].blank?
        @date = FinanceFeeCollection.find(params[:date])
        @dates_id = [@date.id]
        extra_conditions = ""
        @student_admission_no = ""
        unless params[:student_admission_no].blank?
          @student_admission_no = params[:student_admission_no]
          extra_conditions = " AND students.admission_no LIKE '#{params[:student_admission_no]}%%'"
        end
        @fine_configurations = Student.paginate(:all, :joins => "INNER JOIN finance_fees on finance_fees.student_id = students.id",:conditions=>["finance_fees.fee_collection_id='#{@date.id}' and finance_fees.balance > 0 and students.batch_id='#{@batch.id}'" + extra_conditions],:order=>"students.admission_no ASC",:page => params[:page], :per_page => 50).uniq
        #abort(@fine_configurations.inspect)
        render :update do |page|
          page << "j('#student').show();"
          page.replace_html "student", :partial => "student_fine_configurations"
        end
      else
        render :update do |page|
          page << "j('#student').hide();"
        end
      end
    else
      if params[:opt].to_i == 1
        @opt = 1;
        @filter_by_course = params[:filter_by_course];
        @d_id = params[:date];
        if params[:filter_by_course].to_i == 1
          eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
          tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
          hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
          college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
          college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
          if college_courses_id.blank?
            college_courses_id[0] = 0
          end
          school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
          school_course_id = school_course_id.reject { |s| s.to_s.empty? }
          if school_course_id.blank?
            school_course_id[0] = 0
          end
          batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
        elsif params[:filter_by_course].to_i == 2
          eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
          tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
          hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
          college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
          college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
          if college_courses_id.blank?
            college_courses_id[0] = 0
          end
          #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
          batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
        else
          batches = Batch.all.map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
        end
        #@students = Student.find(:all,:conditions=>["batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
        unless params[:date].blank?
          @multi_date = true
          @date = FinanceFeeCollection.find(params[:date])
          unless @date.blank?
            @date_name = @date.name
            @dates = FinanceFeeCollection.find_all_by_name(@date_name)
            unless @dates.blank?
              @dates_id = @dates.map(&:id)
              if @dates_id.blank?
                @dates_id[0] = 0
              end
            else
              @dates_id[0] = 0
            end
            #abort(@dates.map(&:id).inspect)
            extra_conditions = ""
            @student_admission_no = ""
            unless params[:student_admission_no].blank?
              @student_admission_no = params[:student_admission_no]
              extra_conditions = " AND students.admission_no LIKE '#{params[:student_admission_no]}%%'"
            end
            @fine_configurations = Student.paginate(:all, :joins => "INNER JOIN finance_fees on finance_fees.student_id = students.id",:conditions=>["finance_fees.fee_collection_id IN (#{@dates_id.join(",")}) and finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})" + extra_conditions],:order=>"students.admission_no ASC",:page => params[:page], :per_page => 50).uniq
          end
          render :update do |page|
            page << "j('#student').show();"
            page.replace_html "student", :partial => "student_fine_configurations"
          end
        else
          render :update do |page|
            page << "j('#student').hide();"
          end
        end
      else
        @opt = 2;
        @b_id = params[:batch_id]
        @section_id = params[:section_id]
        @course_name = params[:course_name]
        @d_id = params[:date]
        batch_id = 0
        course_id = 0
        class_id = 0
        unless params[:batch_id].nil?
          batch_id = params[:batch_id]
        end
        
        unless params[:course_name].nil?
          class_id = params[:course_name]
        end

        unless params[:section_id].blank?
          course_id = params[:section_id]
        end

        batch_name = ""
        batches = [0]
        if batch_id.to_i > 0
          batch = Batch.find batch_id
          batch_name = batch.name
        end
        
        class_name = ""
        if class_id.to_i > 0
          course = Course.find class_id
          class_name = course.course_name
        end

        unless batch_name.blank?
          if course_id == 0
            batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
            #abort(bclass_name.)
            unless class_id == 0
              courses = batches_all.map{|b| b.course_id}   
              #abort(courses.inspect)
              #batches = batches_all.map{|b| b.id}
              @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      

              @dates = []
              unless @sections.blank?
                batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
                #batches = batches_all.map{|b| b.id}
              end
            end
          else
            batches_all = Batch.find_all_by_name_and_is_deleted_and_course_id(batch_name,false, course_id)
          end

          batches = batches_all.map{|b| b.id}    
        end
        if batches.blank?
          batches[0] = 0
        end
        #abort(batches.inspect)
        #@students = Student.find(:all,:conditions=>["batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
        unless params[:date].blank?
          if course_id == 0
            @multi_date = true
            @date = FinanceFeeCollection.find(params[:date])
            unless @date.blank?
              @date_name = @date.name
              @dates = FinanceFeeCollection.find_all_by_name(@date_name)
              unless @dates.blank?
                @dates_id = @dates.map(&:id)
                if @dates_id.blank?
                  @dates_id[0] = 0
                end
              else
                @dates_id[0] = 0
              end
              extra_conditions = ""
              @student_admission_no = ""
              unless params[:student_admission_no].blank?
                @student_admission_no = params[:student_admission_no]
                extra_conditions = " AND students.admission_no LIKE '#{params[:student_admission_no]}%%'"
              end
              @fine_configurations = Student.paginate(:all, :joins => "INNER JOIN finance_fees on finance_fees.student_id = students.id",:conditions=>["finance_fees.fee_collection_id IN (#{@dates_id.join(",")}) and finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})" + extra_conditions],:order=>"students.admission_no ASC",:page => params[:page], :per_page => 50).uniq
            end
          else  
            @date = FinanceFeeCollection.find(params[:date])
            @dates_id = [@date.id]
            #student_ids = FinanceFee.paginate(:all,:conditions=>"batch_id IN (#{batches.join(',')}) and fee_collection_id IN (#{@dates_data_id.join(',')})")
            extra_conditions = ""
            @student_admission_no = ""
            unless params[:student_admission_no].blank?
              @student_admission_no = params[:student_admission_no]
              extra_conditions = " AND students.admission_no LIKE '#{params[:student_admission_no]}%%'"
            end
            @fine_configurations = Student.paginate(:all, :joins => "INNER JOIN finance_fees on finance_fees.student_id = students.id",:conditions=>["finance_fees.fee_collection_id ='#{@date.id}' and finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})" + extra_conditions],:order=>"students.admission_no ASC",:page => params[:page], :per_page => 50).uniq
            #@defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id='#{@date.id}' and finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
          end
          render :update do |page|
            page << "j('#student').show();"
            page.replace_html "student", :partial => "student_fine_configurations"
          end
        else
          render :update do |page|
            page << "j('#student').hide();"
          end
        end
      end
    end
  end

  def fee_defaulters_pdf
#    @batch   = Batch.find(params[:batch_id])
#    unless params[:date] == '0'
#      @date = @finance_fee_collection = FinanceFeeCollection.find(params[:date])
#      @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id",:conditions=>["finance_fees.fee_collection_id='#{@date.id}' and finance_fees.balance > 0 and students.batch_id='#{@batch.id}'"],:select=>["students.*,finance_fees.balance as balance"],:order=>"cast(students.class_roll_no as unsigned) ASC").uniq
#    else
#      # @date = @finance_fee_collection = FinanceFeeCollection.find(params[:date])
#      @date = 0
#      @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id",:conditions=>["finance_fees.balance > 0 and students.batch_id='#{@batch.id}'"],:select=>["students.*,finance_fees.balance as balance"],:order=>"cast(students.class_roll_no as unsigned) ASC").uniq
#    end

    @multi_date = false
    @defaulters = []
    if params[:opt].nil?
      @opt = 0;
      @b_id = params[:batch_id];
      @d_id = params[:date];
      @batch   = Batch.find(params[:batch_id])
      #@students = @batch.students
      unless params[:date].blank?
        unless params[:date] == '0'
          @date = FinanceFeeCollection.find(params[:date])
          @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id='#{@date.id}' and finance_fees.balance > 0 and students.batch_id='#{@batch.id}'"],:order=>"students.admission_no ASC").uniq
        else
          @date = 0
          @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id",:conditions=>["finance_fees.balance > 0 and students.batch_id='#{@batch.id}'"],:order=>"students.admission_no ASC").uniq
        end
      end
    else
      if params[:opt].to_i == 1
        @opt = 1;
        @filter_by_course = params[:filter_by_course];
        @d_id = params[:date];
        if params[:filter_by_course].to_i == 1
          eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
          tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
          hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
          college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
          college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
          if college_courses_id.blank?
            college_courses_id[0] = 0
          end
          school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
          school_course_id = school_course_id.reject { |s| s.to_s.empty? }
          if school_course_id.blank?
            school_course_id[0] = 0
          end
          batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
        elsif params[:filter_by_course].to_i == 2
          eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
          tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
          hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
          college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
          college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
          if college_courses_id.blank?
            college_courses_id[0] = 0
          end
          #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
          batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
        else
          batches = Batch.all.map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
        end
        #@students = Student.find(:all,:conditions=>["batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
        unless params[:date].blank?
          unless params[:date] == '0'
            @multi_date = true
            @date = FinanceFeeCollection.find(params[:date])
            unless @date.blank?
              @date_name = @date.name
              @dates = FinanceFeeCollection.find_all_by_name(@date_name)
              unless @dates.blank?
                @dates_id = @dates.map(&:id)
                if @dates_id.blank?
                  @dates_id[0] = 0
                end
              else
                @dates_id[0] = 0
              end
              #abort(@dates.map(&:id).inspect)
              @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id IN (#{@dates_id.join(",")}) and finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
              #abort(@defaulters.inspect)
            end
          else
            @date = 0
            @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id",:conditions=>["finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
          end
        end
      else
        @opt = 2;
        @b_id = params[:batch_id]
        @section_id = params[:section_id]
        @course_name = params[:course_name]
        @d_id = params[:date]
        batch_id = 0
        course_id = 0
        class_id = 0
        unless params[:batch_id].nil?
          batch_id = params[:batch_id]
        end
        
        unless params[:course_name].nil?
          class_id = params[:course_name]
        end

        unless params[:section_id].blank?
          course_id = params[:section_id]
        end

        batch_name = ""
        batches = [0]
        if batch_id.to_i > 0
          batch = Batch.find batch_id
          batch_name = batch.name
        end
        
        class_name = ""
        if class_id.to_i > 0
          course = Course.find class_id
          class_name = course.course_name
        end

        unless batch_name.blank?
          if course_id == 0
            batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
            #abort(bclass_name.)
            unless class_id == 0
              courses = batches_all.map{|b| b.course_id}   
              #abort(courses.inspect)
              #batches = batches_all.map{|b| b.id}
              @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      

              @dates = []
              unless @sections.blank?
                batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
                #batches = batches_all.map{|b| b.id}
              end
            end
          else
            batches_all = Batch.find_all_by_name_and_is_deleted_and_course_id(batch_name,false, course_id)
          end

          batches = batches_all.map{|b| b.id}    
        end
        if batches.blank?
          batches[0] = 0
        end
        #abort(batches.inspect)
        #@students = Student.find(:all,:conditions=>["batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
        unless params[:date].blank?
          unless params[:date] == '0'
            if course_id == 0
              @multi_date = true
              @date = FinanceFeeCollection.find(params[:date])
              unless @date.blank?
                @date_name = @date.name
                @dates = FinanceFeeCollection.find_all_by_name(@date_name)
                unless @dates.blank?
                  @dates_id = @dates.map(&:id)
                  if @dates_id.blank?
                    @dates_id[0] = 0
                  end
                else
                  @dates_id[0] = 0
                end
                #abort(@dates.map(&:id).inspect)
                @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id IN (#{@dates_id.join(",")}) and finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
                #abort(@defaulters.inspect)
              end
            else  
              @date = FinanceFeeCollection.find(params[:date])
              @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id='#{@date.id}' and finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
            end
          else
            @date = 0
            @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id",:conditions=>["finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
          end
        end
      end
    end

    @currency_type = currency

    render :pdf => 'fee_defaulters_pdf',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 22,
      :bottom => 30,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_header_defaulters.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end

  def fee_defaulters_excel
    @multi_date = false
    @defaulters = []
    if params[:opt].nil?
      @opt = 0;
      @b_id = params[:batch_id];
      @d_id = params[:date];
      @batch   = Batch.find(params[:batch_id])
      #@students = @batch.students
      unless params[:date].blank?
        unless params[:date] == '0'
          @date = FinanceFeeCollection.find(params[:date])
          @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id='#{@date.id}' and finance_fees.balance > 0 and students.batch_id='#{@batch.id}'"],:order=>"students.admission_no ASC").uniq
        else
          @date = 0
          @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id",:conditions=>["finance_fees.balance > 0 and students.batch_id='#{@batch.id}'"],:order=>"students.admission_no ASC").uniq
        end
      end
    else
      if params[:opt].to_i == 1
        @opt = 1;
        @filter_by_course = params[:filter_by_course];
        @d_id = params[:date];
        if params[:filter_by_course].to_i == 1
          eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
          tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
          hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
          college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
          college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
          if college_courses_id.blank?
            college_courses_id[0] = 0
          end
          school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
          school_course_id = school_course_id.reject { |s| s.to_s.empty? }
          if school_course_id.blank?
            school_course_id[0] = 0
          end
          batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
        elsif params[:filter_by_course].to_i == 2
          eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
          tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
          hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
          college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
          college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
          if college_courses_id.blank?
            college_courses_id[0] = 0
          end
          #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
          batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
        else
          batches = Batch.all.map(&:id)
          batches = batches.reject { |b| b.to_s.empty? }
          if batches.blank?
            batches[0] = 0
          end
        end
        #@students = Student.find(:all,:conditions=>["batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
        unless params[:date].blank?
          unless params[:date] == '0'
            @multi_date = true
            @date = FinanceFeeCollection.find(params[:date])
            unless @date.blank?
              @date_name = @date.name
              @dates = FinanceFeeCollection.find_all_by_name(@date_name)
              unless @dates.blank?
                @dates_id = @dates.map(&:id)
                if @dates_id.blank?
                  @dates_id[0] = 0
                end
              else
                @dates_id[0] = 0
              end
              #abort(@dates.map(&:id).inspect)
              @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id IN (#{@dates_id.join(",")}) and finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
              #abort(@defaulters.inspect)
            end
          else
            @date = 0
            @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id",:conditions=>["finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
          end
        end
      else
        @opt = 2;
        @b_id = params[:batch_id]
        @section_id = params[:section_id]
        @course_name = params[:course_name]
        @d_id = params[:date]
        batch_id = 0
        course_id = 0
        class_id = 0
        unless params[:batch_id].nil?
          batch_id = params[:batch_id]
        end
        
        unless params[:course_name].nil?
          class_id = params[:course_name]
        end

        unless params[:section_id].blank?
          course_id = params[:section_id]
        end

        batch_name = ""
        batches = [0]
        if batch_id.to_i > 0
          batch = Batch.find batch_id
          batch_name = batch.name
        end
        
        class_name = ""
        if class_id.to_i > 0
          course = Course.find class_id
          class_name = course.course_name
        end

        unless batch_name.blank?
          if course_id == 0
            batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
            #abort(bclass_name.)
            unless class_id == 0
              courses = batches_all.map{|b| b.course_id}   
              #abort(courses.inspect)
              #batches = batches_all.map{|b| b.id}
              @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      

              @dates = []
              unless @sections.blank?
                batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
                #batches = batches_all.map{|b| b.id}
              end
            end
          else
            batches_all = Batch.find_all_by_name_and_is_deleted_and_course_id(batch_name,false, course_id)
          end

          batches = batches_all.map{|b| b.id}    
        end
        if batches.blank?
          batches[0] = 0
        end
        #abort(batches.inspect)
        #@students = Student.find(:all,:conditions=>["batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
        unless params[:date].blank?
          unless params[:date] == '0'
            if course_id == 0
              @multi_date = true
              @date = FinanceFeeCollection.find(params[:date])
              unless @date.blank?
                @date_name = @date.name
                @dates = FinanceFeeCollection.find_all_by_name(@date_name)
                unless @dates.blank?
                  @dates_id = @dates.map(&:id)
                  if @dates_id.blank?
                    @dates_id[0] = 0
                  end
                else
                  @dates_id[0] = 0
                end
                #abort(@dates.map(&:id).inspect)
                @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id IN (#{@dates_id.join(",")}) and finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
                #abort(@defaulters.inspect)
              end
            else  
              @date = FinanceFeeCollection.find(params[:date])
              @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id='#{@date.id}' and finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
            end
          else
            @date = 0
            @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id",:conditions=>["finance_fees.balance > 0 and students.batch_id IN (#{batches.join(",")})"],:order=>"students.admission_no ASC").uniq
          end
        end
      end
    end

    @currency_type = currency

    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    sheet1 = new_book.create_worksheet :name => 'fee_defaulters'
    
    amount_format = Spreadsheet::Format.new({
      :size             => 12,
      :number_format    => "0.00"
    });
  
    format = Spreadsheet::Format.new
    format.bottom = :thin
    format.top = :thin
  
    title_format = Spreadsheet::Format.new({
      :weight           => :bold,
      :size             => 11,
      :horizontal_align => :centre,
      :vertical_align   => :centre
    })
    header_title_format = Spreadsheet::Format.new({
      :weight           => :bold,
      :size             => 18,
      :horizontal_align => :centre,
      :vertical_align   => :centre,
#      :color            => :blue, 
#      :pattern_fg_color => :ash, 
#      :pattern          => 1,
    })
    sub_header_title_format = Spreadsheet::Format.new({
      :weight           => :bold,
      :size             => 11,
      :horizontal_align => :centre,
      :vertical_align   => :centre,
      :top              =>  :thin,
      :bottom           =>  :thin
#      :color            => :blue, 
#      :pattern_fg_color => :ash, 
#      :pattern          => 1,
    })
    top_border_format = Spreadsheet::Format.new({
      :top              =>  :thin,
    })
    bottom_border_format = Spreadsheet::Format.new({
      :bottom           =>  :thin
    })
  
    center_format = Spreadsheet::Format.new({
      :size             => 10,
      :horizontal_align => :centre
    })
  
    row_loop = 0
    row_1 = [Configuration.get_config_value('InstitutionName')]
    new_book.worksheet(0).insert_row(row_loop, row_1)
    #new_book.worksheet(0).row(row_loop).border(0)
    
    row_loop += 1
    new_book.worksheet(0).insert_row(row_loop, row_1)
    row_loop += 1
    new_book.worksheet(0).insert_row(row_loop, row_1)
    new_book.worksheet(0).merge_cells(0, 0, row_loop, 7)
    new_book.worksheet(0).row(0).set_format(0, header_title_format)
    #new_book.worksheet(0).format.border(1, 1, 1, 1)
    
    row_loop += 1
    unless @batch.blank?
      batch_version = @batch.name.split(" ")
      current_row = row_loop
      
      row_1 = ["#{t('shift')} :"+ " " "#{batch_version[0].strip}",'',"#{t('version')} :"+ " " "#{batch_version[1].strip}", "","#{t('class')} :"+ " " "#{@batch.course.course_name}", "", "#{t('section')} :"+ " " "#{@batch.course.section_name}",'']
      new_book.worksheet(0).insert_row(row_loop, row_1)
      row_loop += 1
      
      row_1 = [""]
      new_book.worksheet(0).insert_row(row_loop, row_1)
      row_loop += 1
      
      row_1 = [""]
      new_book.worksheet(0).insert_row(row_loop, row_1)
      
      new_book.worksheet(0).merge_cells(current_row, 0, row_loop, 1)
      new_book.worksheet(0).merge_cells(current_row, 2, row_loop, 3)
      new_book.worksheet(0).merge_cells(current_row, 4, row_loop, 5)
      new_book.worksheet(0).merge_cells(current_row, 6, row_loop, 7)
      new_book.worksheet(0).row(current_row).set_format(0, sub_header_title_format)
      new_book.worksheet(0).row(current_row).set_format(1, sub_header_title_format)
      new_book.worksheet(0).row(current_row).set_format(2, sub_header_title_format)
      new_book.worksheet(0).row(current_row).set_format(3, sub_header_title_format)
      new_book.worksheet(0).row(current_row).set_format(4, sub_header_title_format)
      new_book.worksheet(0).row(current_row).set_format(5, sub_header_title_format)
      new_book.worksheet(0).row(current_row).set_format(6, sub_header_title_format)
      new_book.worksheet(0).row(current_row).set_format(7, sub_header_title_format)
      
      new_book.worksheet(0).row(row_loop).set_format(0, bottom_border_format)
      new_book.worksheet(0).row(row_loop).set_format(1, bottom_border_format)
      new_book.worksheet(0).row(row_loop).set_format(2, bottom_border_format)
      new_book.worksheet(0).row(row_loop).set_format(3, bottom_border_format)
      new_book.worksheet(0).row(row_loop).set_format(4, bottom_border_format)
      new_book.worksheet(0).row(row_loop).set_format(5, bottom_border_format)
      new_book.worksheet(0).row(row_loop).set_format(6, bottom_border_format)
      new_book.worksheet(0).row(row_loop).set_format(7, bottom_border_format)
      row_loop += 1
    else
      row_1 = [""]
      new_book.worksheet(0).insert_row(row_loop, row_1)
      new_book.worksheet(0).merge_cells(row_loop, 0, row_loop, 7)
      new_book.worksheet(0).row(row_loop).set_format(0, top_border_format)
      new_book.worksheet(0).row(row_loop).set_format(1, top_border_format)
      new_book.worksheet(0).row(row_loop).set_format(2, top_border_format)
      new_book.worksheet(0).row(row_loop).set_format(3, top_border_format)
      new_book.worksheet(0).row(row_loop).set_format(4, top_border_format)
      new_book.worksheet(0).row(row_loop).set_format(5, top_border_format)
      new_book.worksheet(0).row(row_loop).set_format(6, top_border_format)
      new_book.worksheet(0).row(row_loop).set_format(7, top_border_format)
      
      row_loop += 1
    end
    
    current_row = row_loop
    row_1 = ['#','Student ID','Student Name', 'Roll No','Student Category', 'Due Amount','Contact Number','Remarks']
    new_book.worksheet(0).insert_row(row_loop, row_1)
    row_loop += 1
    
    row_1 = [""]
    new_book.worksheet(0).insert_row(row_loop, row_1)
    row_loop += 1

    row_1 = [""]
    new_book.worksheet(0).insert_row(row_loop, row_1)
    
    new_book.worksheet(0).merge_cells(current_row, 0, row_loop, 0)
    new_book.worksheet(0).merge_cells(current_row, 1, row_loop, 1)
    new_book.worksheet(0).merge_cells(current_row, 2, row_loop, 2)
    new_book.worksheet(0).merge_cells(current_row, 3, row_loop, 3)
    new_book.worksheet(0).merge_cells(current_row, 4, row_loop, 4)
    new_book.worksheet(0).merge_cells(current_row, 5, row_loop, 5)
    new_book.worksheet(0).merge_cells(current_row, 6, row_loop, 6)
    new_book.worksheet(0).merge_cells(current_row, 7, row_loop, 7)
    
    
    new_book.worksheet(0).row(current_row).set_format(0, title_format)
    new_book.worksheet(0).row(current_row).set_format(1, title_format)
    new_book.worksheet(0).row(current_row).set_format(2, title_format)
    new_book.worksheet(0).row(current_row).set_format(3, title_format)
    new_book.worksheet(0).row(current_row).set_format(4, title_format)
    new_book.worksheet(0).row(current_row).set_format(5, title_format)
    new_book.worksheet(0).row(current_row).set_format(6, title_format)
    new_book.worksheet(0).row(current_row).set_format(7, title_format)
    
    new_book.worksheet(0).column(0).width = 10
    new_book.worksheet(0).column(1).width = 15
    new_book.worksheet(0).column(2).width = 30
    new_book.worksheet(0).column(3).width = 12
    new_book.worksheet(0).column(4).width = 'Student Category'.length + 10
    new_book.worksheet(0).column(5).width = 'Due Amount(BDT)'.length + 10
    new_book.worksheet(0).column(6).width = 'Contact Number'.length + 10
    new_book.worksheet(0).column(7).width = 25
    
    
    row_loop += 1
    sl = 1
    unless @defaulters.blank?
      @defaulters.each do |s|
        unless @date == 0
          if @multi_date
            dates = @dates.map(&:id)
            fees = s.finance_fees.select{ |f| dates.include?(f.fee_collection_id.to_i)}
            @date = FinanceFeeCollection.find(fees[0].fee_collection_id)  
          else
            fees = s.finance_fees.select{ |f| f.fee_collection_id.to_i == @date.id.to_i}
          end
        else
          fees = s.finance_fees
        end
        b = Batch.find(s.batch_id)
        sc = StudentCategory.find(s.student_category_id)
        b_v = b.name.split(" ")
        
        fee_remarks = ""
        unless params[:date] == '0'
        fee_remarks = @date.name
        cnt = 1
        else
        cnt = 0
        fees.each_with_index do |fee,m|
          unless fee.is_paid
            if m == fees.length - 1
            fee_remarks += fee.finance_fee_collection.name
            else
            fee_remarks += fee.finance_fee_collection.name + ", "
            end
            cnt += 1
          end
        end
        end
        
        #unless sc.receiver.blank?
          data_row = [sl, s.admission_no, s.full_name, s.class_roll_no, sc.name, fees.map(&:balance).sum, s.sms_number,fee_remarks ]
          new_book.worksheet(0).insert_row(row_loop, data_row)
          new_book.worksheet(0).row(row_loop).set_format(0, center_format)
          new_book.worksheet(0).row(row_loop).set_format(1, center_format)
          new_book.worksheet(0).row(row_loop).set_format(3, center_format)
          new_book.worksheet(0).row(row_loop).set_format(4, center_format)
          new_book.worksheet(0).row(row_loop).set_format(6, center_format)
          new_book.worksheet(0).row(row_loop).set_format(5, amount_format)
          row_loop+=1
          sl+=1
        #end
      end
    end
    
    sheet1.add_header(Configuration.get_config_value('InstitutionName'))
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => "fee_defaulters.xls", :type =>  "application/vnd.ms-excel"
  end
  
  def fee_collection_pdf
    @batch   = Batch.find(params[:batch_id])
    @date = @finance_fee_collection = FinanceFeeCollection.find(params[:date])
    @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id='#{@date.id}' and finance_fees.batch_id='#{@batch.id}'"],:select=>["students.*, finance_fees.id as fee_id,finance_fees.balance as balance"],:order=>"students.class_roll_no ASC").uniq
    @currency_type = currency

    render :pdf => 'fee_collection_pdf',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 22,
      :bottom => 30,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_header_defaulters.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def bill_generation_report_pdf
    @currency_type = currency
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      
      @multi_date = false
      @defaulters = []
      if params[:opt].nil?
        @opt = 0;
        @b_id = params[:batch_id];
        @d_id = params[:date];
        @batch   = Batch.find(:all, :conditions => "id = #{params[:batch_id]}")
        batches = @batch.map{|b| b.id}  
        if batches.blank?
          batches[0] = 0
        end
        
        @dates   = FinanceFeeCollection.find(:all, :conditions => "id = #{params[:date]}")
        unless @dates.blank?
          @dates_id = @dates.map(&:id)
          if @dates_id.blank?
            @dates_id[0] = 0
          end
        else
          @dates_id[0] = 0
        end
      else
        if params[:opt].to_i == 1
          @opt = 1;
          @filter_by_course = params[:filter_by_course];
          @d_id = params[:date];
          if params[:filter_by_course].to_i == 1
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            school_course_id = school_course_id.reject { |s| s.to_s.empty? }
            if school_course_id.blank?
              school_course_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
          elsif params[:filter_by_course].to_i == 2
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
          else
            batches = Batch.all.map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
          end
          if batches.blank?
            batches[0] = 0
          end
          
          @date = FinanceFeeCollection.find(params[:date])
          unless @date.blank?
            @date_name = @date.name
            @dates = FinanceFeeCollection.find_all_by_name(@date_name)
            unless @dates.blank?
              @dates_id = @dates.map(&:id)
              if @dates_id.blank?
                @dates_id[0] = 0
              end
            else
              @dates_id[0] = 0
            end
          end
        else
          @opt = 2;
          @b_id = params[:batch_id]
          @section_id = params[:section_id]
          @course_name = params[:course_name]
          @d_id = params[:date]
          batch_id = 0
          course_id = 0
          class_id = 0
          unless params[:batch_id].nil?
            batch_id = params[:batch_id]
          end

          unless params[:course_name].nil?
            class_id = params[:course_name]
          end

          unless params[:section_id].blank?
            course_id = params[:section_id]
          end

          batch_name = ""
          batches = [0]
          if batch_id.to_i > 0
            batch = Batch.find batch_id
            batch_name = batch.name
          end

          class_name = ""
          if class_id.to_i > 0
            course = Course.find class_id
            class_name = course.course_name
          end

          unless batch_name.blank?
            if course_id == 0
              batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
              #abort(bclass_name.)
              unless class_id == 0
                courses = batches_all.map{|b| b.course_id}   
                #abort(courses.inspect)
                #batches = batches_all.map{|b| b.id}
                @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      

                @dates = []
                unless @sections.blank?
                  batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
                  #batches = batches_all.map{|b| b.id}
                end
              end
            else
              batches_all = Batch.find_all_by_name_and_is_deleted_and_course_id(batch_name,false, course_id)
            end

            batches = batches_all.map{|b| b.id}    
          end
          if batches.blank?
            batches[0] = 0
          end
          
          @date = FinanceFeeCollection.find(params[:date])
          unless @date.blank?
            @date_name = @date.name
            @dates = FinanceFeeCollection.find_all_by_name(@date_name)
            unless @dates.blank?
              @dates_id = @dates.map(&:id)
              if @dates_id.blank?
                @dates_id[0] = 0
              end
            else
              @dates_id[0] = 0
            end
          end
        end
      end
      
      #abort(batches.inspect)
      #abort(@dates_id.inspect)
      #@batch   = Batch.find(params[:batch_id])
      @dates    =  @fee_collections = FinanceFeeCollection.find(:all, :conditions => "id IN (#{@dates_id.join(',')})")
      unless @dates.blank?
        @dates_data_id = @dates.map(&:id)
        if @dates_data_id.blank?
          @dates_data_id[0] = 0
        end
      end
      #student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id IN (#{batches.join(',')})").collect(&:student_id)
      #student_ids = FinanceFee.paginate(:all,:conditions=>"batch_id IN (#{batches.join(',')}) and fee_collection_id IN (#{@dates_data_id.join(',')})",:page => params[:page], :per_page => 10)
      
      @student_particulars = {}
      @student_summaries = {}
      @students = {}
      particulars = []
      particular_categories = []
      @student_finance_fees = FinanceFee.paginate(:all,:conditions=>"finance_fees.batch_id IN (#{batches.join(',')}) and finance_fees.fee_collection_id IN (#{@dates_data_id.join(',')})", :joins => "INNER JOIN students ON students.id = finance_fees.student_id",:page => params[:page], :per_page => 500)
      #student_finance_fees = FinanceFee.find(:all,:conditions=>"finance_fees.batch_id IN (#{batches.join(',')}) and finance_fees.fee_collection_id IN (#{@dates_data_id.join(',')})", :joins => "INNER JOIN students ON students.id = finance_fees.student_id")
      
      unless @student_finance_fees.blank?
        @student_finance_fees.each do |fee|
          exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(fee.student.id, fee.finance_fee_collection.id).map(&:fee_particular_id)
          unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
            exclude_particular_ids = exclude_particular_ids
          else
            exclude_particular_ids = [0]
          end

          @student_particulars[fee.student.id] = []
          @students[fee.student.id] = []
          @student_summaries[fee.student.id] = []
          fee_particulars = fee.finance_fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{fee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==fee.student or par.receiver==fee.student.student_category or par.receiver==fee.batch) }
          
          @student_particulars[fee.student.id] << fee_particulars
          @students[fee.student.id] << fee.student
          particulars << fee_particulars.map(&:name)
          particular_categories << fee_particulars.map{|fp| fp.finance_fee_particular_category.name}
          #if fee.student.id == 32262
          #  abort(particular_categories.inspect)
          #end

          @total_discount = 0
          @total_payable=fee_particulars.map{|s| s.amount}.sum.to_f
          calculate_discount(fee.finance_fee_collection, fee.batch, fee.student, false, nil, false)

          
          paid_amount = 0
          paid_fine = 0
          paid_fees = fee.finance_transactions
          unless paid_fees.blank?
            paid_fines = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Fine' AND finance_transaction_id IN (" + paid_fees.map(&:id).join(",") + ")")
            tmp_paid_fines = []
            unless paid_fines.nil?
              paid_fines.each do |pf|
                paid_fine = pf.amount
              end
            end
            
            paid_discounts = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Adjustment' AND transaction_type = 'Discount' AND finance_transaction_id IN (" + paid_fees.map(&:id).join(",") + ")")
            discount = 0
            unless paid_discounts.nil?
              paid_discounts.each do |pf|
                discount = pf.amount
              end
            end
            @total_discount = discount
            paid_amount += paid_fees.map(&:amount).sum.to_f
          end
          #abort(@total_payable.to_s + "  " + paid_fine.to_s + "  " + @total_discount.to_s)
          total_fees = (@total_payable + paid_fine) - @total_discount
          @student_summaries[fee.student.id] << {"total_fee" => total_fees, "discount" => @total_discount, 'fine' => paid_fine, "paid_amount" => paid_amount}
        end
        
#        ar_particular_categories = particular_categories
#        pt = []
#        ar_particular_categories.each_with_index do |particular_categories, i|
#          particular_categories.each do |particular_category|
#            pt << particular_category
#          end
#        end
#        @particular_categories = pt
#        
        fee_cateroies = FinanceFeeParticularCategory.active
        @particular_categories = fee_cateroies.map{|p| p.name}
        #if fee.student_id == 28046
            #abort(pt.inspect)
         # end
      end  
    end

    render :pdf => 'bill_generation_report_pdf',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 22,
      :bottom => 30,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_header_defaulters.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def bill_generation_report_xls
    @currency_type = currency
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      
      @multi_date = false
      @defaulters = []
      if params[:opt].nil?
        @opt = 0;
        @b_id = params[:batch_id];
        @d_id = params[:date];
        @batch   = Batch.find(:all, :conditions => "id = #{params[:batch_id]}")
        batches = @batch.map{|b| b.id}  
        if batches.blank?
          batches[0] = 0
        end
        
        @dates   = FinanceFeeCollection.find(:all, :conditions => "id = #{params[:date]}")
        unless @dates.blank?
          @dates_id = @dates.map(&:id)
          if @dates_id.blank?
            @dates_id[0] = 0
          end
        else
          @dates_id[0] = 0
        end
      else
        if params[:opt].to_i == 1
          @opt = 1;
          @filter_by_course = params[:filter_by_course];
          @d_id = params[:date];
          if params[:filter_by_course].to_i == 1
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            school_course_id = school_course_id.reject { |s| s.to_s.empty? }
            if school_course_id.blank?
              school_course_id[0] = 0
            end
            batches = Batch.find(:all, :conditions => "course_id IN (#{school_course_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
          elsif params[:filter_by_course].to_i == 2
            eleven_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%eleven%' or UPPER(course_name) LIKE '%XI%'").map(&:id)
            tweleve_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%twelve%' or UPPER(course_name) LIKE '%XII%'").map(&:id)
            hsc_courses_id = Course.find(:all, :conditions => "LOWER(course_name) LIKE '%hsc%'").map(&:id)
            college_courses_id = eleven_courses_id + tweleve_courses_id + hsc_courses_id
            college_courses_id = college_courses_id.reject { |c| c.to_s.empty? }
            if college_courses_id.blank?
              college_courses_id[0] = 0
            end
            #school_course_id = Course.find(:all, :conditions => "ID NOT IN (#{college_courses_id.join(",")})").map(&:id)
            batches = Batch.find(:all, :conditions => "course_id IN (#{college_courses_id.join(",")})").map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
          else
            batches = Batch.all.map(&:id)
            batches = batches.reject { |b| b.to_s.empty? }
            if batches.blank?
              batches[0] = 0
            end
          end
          if batches.blank?
            batches[0] = 0
          end
          
          @date = FinanceFeeCollection.find(params[:date])
          unless @date.blank?
            @date_name = @date.name
            @dates = FinanceFeeCollection.find_all_by_name(@date_name)
            unless @dates.blank?
              @dates_id = @dates.map(&:id)
              if @dates_id.blank?
                @dates_id[0] = 0
              end
            else
              @dates_id[0] = 0
            end
          end
        else
          @opt = 2;
          @b_id = params[:batch_id]
          @section_id = params[:section_id]
          @course_name = params[:course_name]
          @d_id = params[:date]
          batch_id = 0
          course_id = 0
          class_id = 0
          unless params[:batch_id].nil?
            batch_id = params[:batch_id]
          end

          unless params[:course_name].nil?
            class_id = params[:course_name]
          end

          unless params[:section_id].blank?
            course_id = params[:section_id]
          end

          batch_name = ""
          batches = [0]
          if batch_id.to_i > 0
            batch = Batch.find batch_id
            batch_name = batch.name
          end

          class_name = ""
          if class_id.to_i > 0
            course = Course.find class_id
            class_name = course.course_name
          end

          unless batch_name.blank?
            if course_id == 0
              batches_all = Batch.find_all_by_name_and_is_deleted(batch_name,false)
              #abort(bclass_name.)
              unless class_id == 0
                courses = batches_all.map{|b| b.course_id}   
                #abort(courses.inspect)
                #batches = batches_all.map{|b| b.id}
                @sections = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id in (?)",class_name, courses])      

                @dates = []
                unless @sections.blank?
                  batches_all = Batch.find(:all, :conditions => "name = '#{batch_name}' and is_deleted = '#{false}' and course_id IN (#{@sections.map(&:id).join(",")})")
                  #batches = batches_all.map{|b| b.id}
                end
              end
            else
              batches_all = Batch.find_all_by_name_and_is_deleted_and_course_id(batch_name,false, course_id)
            end

            batches = batches_all.map{|b| b.id}    
          end
          if batches.blank?
            batches[0] = 0
          end
          
          @date = FinanceFeeCollection.find(params[:date])
          unless @date.blank?
            @date_name = @date.name
            @dates = FinanceFeeCollection.find_all_by_name(@date_name)
            unless @dates.blank?
              @dates_id = @dates.map(&:id)
              if @dates_id.blank?
                @dates_id[0] = 0
              end
            else
              @dates_id[0] = 0
            end
          end
        end
      end
      
      #abort(batches.inspect)
      #abort(@dates_id.inspect)
      #@batch   = Batch.find(params[:batch_id])
      @dates    =  @fee_collections = FinanceFeeCollection.find(:all, :conditions => "id IN (#{@dates_id.join(',')})")
      unless @dates.blank?
        @dates_data_id = @dates.map(&:id)
        if @dates_data_id.blank?
          @dates_data_id[0] = 0
        end
      end
      #student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id IN (#{batches.join(',')})").collect(&:student_id)
      #student_ids = FinanceFee.paginate(:all,:conditions=>"batch_id IN (#{batches.join(',')}) and fee_collection_id IN (#{@dates_data_id.join(',')})",:page => params[:page], :per_page => 10)
      
      @student_particulars = {}
      @student_summaries = {}
      @students = {}
      particulars = []
      particular_categories = []
      @student_finance_fees = FinanceFee.paginate(:all,:conditions=>"finance_fees.batch_id IN (#{batches.join(',')}) and finance_fees.fee_collection_id IN (#{@dates_data_id.join(',')})", :joins => "INNER JOIN students ON students.id = finance_fees.student_id",:page => params[:page], :per_page => 500)
      #student_finance_fees = FinanceFee.find(:all,:conditions=>"finance_fees.batch_id IN (#{batches.join(',')}) and finance_fees.fee_collection_id IN (#{@dates_data_id.join(',')})", :joins => "INNER JOIN students ON students.id = finance_fees.student_id")
      
      unless @student_finance_fees.blank?
        @student_finance_fees.each do |fee|
          exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(fee.student.id, fee.finance_fee_collection.id).map(&:fee_particular_id)
          unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
            exclude_particular_ids = exclude_particular_ids
          else
            exclude_particular_ids = [0]
          end

          @student_particulars[fee.student.id] = []
          @students[fee.student.id] = []
          @student_summaries[fee.student.id] = []
          fee_particulars = fee.finance_fee_collection.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{fee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==fee.student or par.receiver==fee.student.student_category or par.receiver==fee.batch) }
          
          @student_particulars[fee.student.id] << fee_particulars
          @students[fee.student.id] << fee.student
          particulars << fee_particulars.map(&:name)
          particular_categories << fee_particulars.map{|fp| fp.finance_fee_particular_category.name}
          #if fee.student.id == 32262
          #  abort(particular_categories.inspect)
          #end

          @total_discount = 0
          @total_payable=fee_particulars.map{|s| s.amount}.sum.to_f
          calculate_discount(fee.finance_fee_collection, fee.batch, fee.student, false, nil, false)

          
          paid_amount = 0
          paid_fine = 0
          paid_fees = fee.finance_transactions
          unless paid_fees.blank?
            paid_fines = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Fine' AND finance_transaction_id IN (" + paid_fees.map(&:id).join(",") + ")")
            tmp_paid_fines = []
            unless paid_fines.nil?
              paid_fines.each do |pf|
                paid_fine = pf.amount
              end
            end
            
            paid_discounts = FinanceTransactionParticular.find(:all, :conditions => "particular_type = 'Adjustment' AND transaction_type = 'Discount' AND finance_transaction_id IN (" + paid_fees.map(&:id).join(",") + ")")
            discount = 0
            unless paid_discounts.nil?
              paid_discounts.each do |pf|
                discount = pf.amount
              end
            end
            @total_discount = discount
            paid_amount += paid_fees.map(&:amount).sum.to_f
          end
          #abort(@total_payable.to_s + "  " + paid_fine.to_s + "  " + @total_discount.to_s)
          total_fees = (@total_payable + paid_fine) - @total_discount
          @student_summaries[fee.student.id] << {"total_fee" => total_fees, "discount" => @total_discount, 'fine' => paid_fine, "paid_amount" => paid_amount}
        end
        
#        fee_cateroies = FinanceFeeParticularCategory.active
#        @particular = fee_cateroies.map{|p| p.name}
#        ar_particular_categories = particular_categories
#        pt = []
#        ar_particular_categories.each_with_index do |particular_categories, i|
#          particular_categories.each do |particular_category|
#            pt << particular_category
#          end
#        end
#        @particular_categories = pt
#        
        fee_cateroies = FinanceFeeParticularCategory.active
        @particular_categories = fee_cateroies.map{|p| p.name}
        #if fee.student_id == 28046
            #abort(pt.inspect)
         # end
      end
      require 'spreadsheet'
      Spreadsheet.client_encoding = 'UTF-8'
      new_book = Spreadsheet::Workbook.new
      amount_format = Spreadsheet::Format.new({
        :size             => 11,
        :number_format    => "0.00"
      });
    
      title_format = Spreadsheet::Format.new({
        :weight           => :bold,
        :size             => 11,
        :horizontal_align => :centre
      })
    
      center_format = Spreadsheet::Format.new({
        :size             => 11,
        :horizontal_align => :centre
      })

      sheet1 = new_book.create_worksheet :name => 'bill_generation_report'
      row_1 = []
      row_1 << '#'
      row_1 << 'Student ID'
      row_1 << 'Student Name'
      row_1 << 'Class'
      row_1 << 'Section'
      @particular_categories.each do |particular|
        row_1 << particular
      end
      row_1 << 'Discount'
      row_1 << 'Fine'
      row_1 << 'Total Fees'
      row_1 << 'Paid Amount'
      row_1 << 'Due'
      row_1 << 'Advance'
      new_book.worksheet(0).insert_row(0, row_1)
      new_book.worksheet(0).row(0).set_format(0, title_format)
      new_book.worksheet(0).column(0).width = 15
      new_book.worksheet(0).row(0).set_format(1, title_format)
      new_book.worksheet(0).column(1).width = 15
      new_book.worksheet(0).row(0).set_format(2, title_format)
      new_book.worksheet(0).column(2).width = 50
      new_book.worksheet(0).row(0).set_format(3, title_format)
      new_book.worksheet(0).column(3).width = 15
      new_book.worksheet(0).row(0).set_format(4, title_format)
      new_book.worksheet(0).column(4).width = 15
      k = 5
      @particular_categories.each do |particular|
        new_book.worksheet(0).row(0).set_format(k, title_format)
        new_book.worksheet(0).column(k).width = particular.length + 5
        k += 1
      end
      new_book.worksheet(0).row(0).set_format(k, title_format)
      new_book.worksheet(0).column(k).width = 15
      k += 1
      new_book.worksheet(0).row(0).set_format(k, title_format)
      new_book.worksheet(0).column(k).width = 15
      k += 1
      new_book.worksheet(0).row(0).set_format(k, title_format)
      new_book.worksheet(0).column(k).width = 15
      k += 1
      new_book.worksheet(0).row(0).set_format(k, title_format)
      new_book.worksheet(0).column(k).width = 15
      k += 1
      new_book.worksheet(0).row(0).set_format(k, title_format)
      new_book.worksheet(0).column(k).width = 15
      k += 1
      new_book.worksheet(0).row(0).set_format(k, title_format)
      new_book.worksheet(0).column(k).width = 15
      k += 1
      
      ind = 1
      l = 1
      unless @students.blank?
        @students.each do |k, fee_data|
          tmp = []
          s = fee_data[0]
          tmp << l.to_s
          tmp << s.admission_no
          tmp << s.full_name
          tmp << s.batch.course.course_name
          tmp << s.batch.course.section_name
          discount = 0.0
          fine = 0.0
          paid_amount = 0.0
          total_fee = 0.0
          unless @student_particulars[k].blank?
            unless @particular_categories.blank?
              @particular_categories.each do |particular|
                sp_amounts = @student_particulars[k][0].map{|sp| sp.amount if sp.finance_fee_particular_category.name == particular }
                particular_amount = 0.00
                sp_amounts.each do |sp_amount|
                  particular_amount += sp_amount.to_f
                end
                total_fee += particular_amount
                tmp << particular_amount
              end
            else
              tmp << 0.00
            end
          end
          unless @student_summaries[k].blank?
            student_summaries = @student_summaries[k][0]
            discount = student_summaries['discount'].to_f
            total_fee -= student_summaries['discount'].to_f
            
            fine = student_summaries['fine'].to_f
            total_fee += student_summaries['fine'].to_f
            
            paid_amount = student_summaries['paid_amount'].to_f
          end
          advance = 0.0
          tmp << discount
          tmp << fine
          tmp << total_fee
          tmp << paid_amount
          remaining = total_fee - paid_amount.to_f
          if remaining < 0
            advance = remaining * -1
            remaining = 0.0
          end
          tmp << remaining
          tmp << advance
          
          new_book.worksheet(0).insert_row(ind, tmp)
          new_book.worksheet(0).row(ind).set_format(0, center_format)
          new_book.worksheet(0).row(ind).set_format(1, center_format)
          new_book.worksheet(0).row(ind).set_format(3, center_format)
          new_book.worksheet(0).row(ind).set_format(4, center_format)
          
          m = 5
          @particular_categories.each do |particular|
            new_book.worksheet(0).row(ind).set_format(m, amount_format)
            m += 1
          end
          new_book.worksheet(0).row(ind).set_format(m, amount_format)
          m += 1
          new_book.worksheet(0).row(ind).set_format(m, amount_format)
          m += 1
          new_book.worksheet(0).row(ind).set_format(m, amount_format)
          m += 1
          new_book.worksheet(0).row(ind).set_format(m, amount_format)
          m += 1
          new_book.worksheet(0).row(ind).set_format(m, amount_format)
          m += 1
          new_book.worksheet(0).row(ind).set_format(m, amount_format)
          m += 1
          l += 1
          ind += 1
        end
      end
      
      sheet1.add_header(Configuration.get_config_value('InstitutionName'))
      spreadsheet = StringIO.new 
      new_book.write spreadsheet 
      send_data spreadsheet.string, :filename => "bill_generation_report.xls", :type =>  "application/vnd.ms-excel"
    end

    
  end

  def pay_fees_defaulters
    advance_fee_collection = false
    @self_advance_fee = false
    @fee_has_advance_particular = false
        
    @batch=Batch.find(params[:batch_id])
    @student = Student.find(params[:id])
    @date = @fee_collection = FinanceFeeCollection.find(params[:date])
    @financefee = @student.finance_fee_by_date(@date)
    
    if @financefee.has_advance_fee_id
      if @date.is_advance_fee_collection
        @self_advance_fee = true
        advance_fee_collection = true
      end
      @fee_has_advance_particular = true
      @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
      @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
      if @advance_ids.blank?
        @advance_ids[0] = 0
      end
      @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
    end
    
    #if @financefee.advance_fee_id.to_i > 0
      
    @due_date = @fee_collection.due_date
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    flash[:warning]=nil
    flash[:notice]=nil

    @paid_fees = @financefee.finance_transactions

    exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
    unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
      exclude_particular_ids = exclude_particular_ids
    else
      exclude_particular_ids = [0]
    end
    
    if advance_fee_collection
      if @fee_collection_advances.particular_id == 0
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@batch.id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      end
    else
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
    end
    
    if advance_fee_collection
      @total_payable = (@fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
    else  
      @total_payable = @fee_particulars.map{|s| s.amount}.sum.to_f
    end
    @total_discount = 0

    if advance_fee_collection
      calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
    else
      if @fee_has_advance_particular
        calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
      else
        calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
      end
    end
    
    bal=(@total_payable-@total_discount).to_f
    days=(Date.today-@date.due_date.to_date).to_i
    
    fine_enabled = true
    student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
    unless student_fee_configuration.blank?
      if student_fee_configuration.config_value.to_i == 1
        fine_enabled = true
      else
        fine_enabled = false
      end
    end
    
    if @tmp_paid_fees.blank?
      @tmp_paid_fees = @financefee.finance_transactions
    end
    
    unless @tmp_paid_fees.blank?
      @tmp_paid_fees.each do |paid_fee|
        transaction_id = paid_fee.id
        online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
        unless online_payments.blank?
          fine_enabled = false
        end
      end
    end
    
    auto_fine=@date.fine
    
    @has_fine_discount = false
    if days > 0 and auto_fine and @financefee.is_paid == false and fine_enabled
      @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
      @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
      calculate_extra_fine(@date, @batch, @student, @fine_rule)
      @new_fine_amount = @fine_amount
      get_fine_discount(@date, @batch, @student)
      if @fine_amount < 0
        @fine_amount = 0
      end
    end

    @fine_amount=0 if @financefee.is_paid
  end

  def update_defaulters_fine_ajax
    @student = Student.find(params[:fine][:student])
    @date = FinanceFeeCollection.find(params[:fine][:date])
    @financefee = @date.fee_transactions(@student.id)
    @fee_collection = FinanceFeeCollection.find(params[:fine][:date])
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    @fee_particulars = @date.fees_particulars(@student)
    unless params[:fine][:fee].to_f < 0
      @fine = params[:fine][:fee].to_f

      total_fees = 0
      @fee_particulars.each do |p|
        total_fees += p.amount
      end
      total_fees += @fine unless @fine.nil?
    else
      flash[:notice] = "#{t('flash24')}"
    end
    redirect_to  :action => "pay_fees_defaulters", :id=> @student.id, :date=> @date.id, :fine => @fine,:batch_id=>params[:batch_id]
  end

  def compare_report

  end

  def report_compare
    if (date_format(params[:start_date]).nil? or date_format(params[:end_date]).nil? or date_format(params[:start_date2]).nil? or date_format(params[:end_date2]).nil?)
      flash[:notice]= "#{t('invalid_date_format')}"
      redirect_to :controller => "user", :action => "dashboard"
    else
      fixed_category_name
      @hr = Configuration.find_by_config_value("HR")
      @start_date = (params[:start_date]).to_date
      @end_date = (params[:end_date]).to_date
      @start_date2 = (params[:start_date2]).to_date
      @end_date2 = (params[:end_date2]).to_date
      @fixed_cat_ids = @fixed_cat_ids.reject { |f| f.to_s.empty? }
      if @fixed_cat_ids.blank?
        @fixed_cat_ids[0] = 0
      end
      @transactions = FinanceTransaction.find(:all,
        :order => 'transaction_date desc', :conditions => ["transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'"])
      @transactions2 = FinanceTransaction.find(:all,
        :order => 'transaction_date desc', :conditions => ["transaction_date >= '#{@start_date2}' and transaction_date <= '#{@end_date2}'"])
      @other_transaction_categories = FinanceTransaction.find(:all,params[:page], :conditions => ["transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'and category_id NOT IN (#{@fixed_cat_ids.join(",")})"],
        :order => 'transaction_date').map{|ft| ft.category}.uniq
      #    @other_transactions = FinanceTransaction.report(@start_date,@end_date,params[:page])
      @other_transaction_categories2 = FinanceTransaction.find(:all,params[:page], :conditions => ["transaction_date >= '#{@start_date2}' and transaction_date <= '#{@end_date2}'and category_id NOT IN (#{@fixed_cat_ids.join(",")})"],
        :order => 'transaction_date').map{|ft| ft.category}.uniq
      #    @transactions_fees = FinanceTransaction.total_fees(@start_date,@end_date)
      #@transactions_fees2 = FinanceTransaction.total_fees(@start_date2,@end_date2)
      #    employees = Employee.find(:all)
      #    @salary = Employee.total_employees_salary(employees, @start_date, @end_date)
      #    @salary2 = Employee.total_employees_salary(employees, @start_date2, @end_date2)
      @salary = MonthlyPayslip.total_employees_salary(@start_date, @end_date)
      @salary2 = MonthlyPayslip.total_employees_salary(@start_date2, @end_date2)
      @donations_total = FinanceTransaction.donations_triggers(@start_date,@end_date)
      @donations_total2 = FinanceTransaction.donations_triggers(@start_date2,@end_date2)
      @transactions_fees = FinanceTransaction.total_fees(@start_date,@end_date).map{|t| t.transaction_total.to_f}.sum
      @transactions_fees2 = FinanceTransaction.total_fees(@start_date2,@end_date2).map{|t| t.transaction_total.to_f}.sum
      @batchs = Batch.find(:all)
      @grand_total = FinanceTransaction.grand_total(@start_date,@end_date)
      @grand_total2 = FinanceTransaction.grand_total(@start_date2,@end_date2)
      @category_transaction_totals = {}
      Champs21Plugin::FINANCE_CATEGORY.each do |category|
        @category_transaction_totals["#{category[:category_name]}"] =   FinanceTransaction.total_transaction_amount(category[:category_name],@start_date,@end_date)
      end
      @category_transaction_totals2 = {}
      Champs21Plugin::FINANCE_CATEGORY.each do |category|
        @category_transaction_totals2["#{category[:category_name]}"] =   FinanceTransaction.total_transaction_amount(category[:category_name],@start_date2,@end_date2)
      end
      @graph = open_flash_chart_object(960, 500, "graph_for_compare_monthly_report?start_date=#{@start_date}&end_date=#{@end_date}&start_date2=#{@start_date2}&end_date2=#{@end_date2}")
    end
  end

  def month_date
    @start_date = params[:start_date]
    @end_date  = params[:end_date]
  end

  def partial_payment
    render :update do |page|
      page.replace_html "partial_payment", :partial => "partial_payment"
    end
  end


  def pdf_fee_structure
    @student = Student.find(params[:id])
    
    @institution_name = Configuration.find_by_config_key("InstitutionName")
    @institution_address = Configuration.find_by_config_key("InstitutionAddress")
    @institution_phone_no = Configuration.find_by_config_key("InstitutionPhoneNo")
    @currency_type = currency
    @fee_collection = FinanceFeeCollection.find params[:id2]
    
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id_and_fee_collection_id(@student.id, @fee_collection.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    @finance_fee=@student.finance_fee_by_date(@fee_collection)
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    @fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"batch_id=#{@finance_fee.batch_id}").select{|par| par.receiver==@student or par.receiver==@student.student_category or par.receiver==@finance_fee.batch}
    exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
    if exclude_discount_ids.blank?
      exclude_discount_ids[0] = 0
    end
    @discounts=@fee_collection.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and batch_id=#{@finance_fee.batch_id}").select{|par| par.receiver==@student or par.receiver==@student.student_category or par.receiver==@finance_fee.batch}
    @total_discount = 0
    @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
    @total_discount =@discounts.map{|d| @total_payable * d.discount.to_f/(d.is_amount? ? @total_payable : 100)}.sum.to_f unless @discounts.nil?

    render :pdf => 'pdf_fee_structure'

    #        respond_to do |format|
    #            format.pdf { render :layout => false }
    #        end
  end

  def graph_for_update_monthly_report

    start_date = (params[:start_date]).to_date
    end_date = (params[:end_date]).to_date
    employees = Employee.find(:all)

    hr = Configuration.find_by_config_value("HR")
    donations_total = FinanceTransaction.donations_triggers(start_date,end_date)
    fees = FinanceTransaction.total_fees(start_date,end_date).map{|t| t.transaction_total.to_f}.sum
    income = FinanceTransaction.total_other_trans(start_date,end_date)[0]
    expense = FinanceTransaction.total_other_trans(start_date,end_date)[1]
    #    other_transactions = FinanceTransaction.find(:all,
    #      :conditions => ["transaction_date >= '#{start_date}' and transaction_date <= '#{end_date}'and category_id !='#{3}' and category_id !='#{2}'and category_id !='#{1}'"])


    x_labels = []
    data = []
    largest_value =0

    unless hr.nil?
      salary = FinanceTransaction.sum('amount',:conditions=>{:title=>"Monthly Salary",:transaction_date=>start_date..end_date}).to_f
      unless salary <= 0
        x_labels << "#{t('salary')}"
        data << salary-(salary*2)
        largest_value = salary if largest_value < salary
      end
    end
    unless donations_total <= 0
      x_labels << "#{t('donations')}"
      data << donations_total
      largest_value = donations_total if largest_value < donations_total
    end

    unless fees <= 0
      x_labels << "#{t('fees_text')}"
      data << fees
      largest_value = fees if largest_value < fees
    end

    Champs21Plugin::FINANCE_CATEGORY.each do |category|
      transaction = FinanceTransaction.total_transaction_amount(category[:category_name],start_date,end_date)
      amount = transaction[:amount]
      unless amount <= 0
        x_labels << "#{category[:category_name]}"
        transaction[:category_type] == "income" ? data << amount : data << amount-(amount*2)
        largest_value = amount if largest_value < amount
      end
    end

    unless income <= 0
      x_labels << "#{t('other_income')}"
      data << income
      largest_value = income if largest_value < income
    end
    unless expense <= 0
      x_labels << "#{t('other_expense')}"
      data << expense-(expense*2)
      largest_value = expense if largest_value < expense
    end


    #    other_transactions.each do |trans|
    #      x_labels << trans.title
    #      if trans.category.is_income? and trans.master_transaction_id == 0
    #        data << trans.amount
    #      else
    #        data << ("-"+trans.amount.to_s).to_i
    #      end
    #      largest_value = trans.amount if largest_value < trans.amount
    #    end

    largest_value += 500

    bargraph = BarFilled.new()
    bargraph.width = 1;
    bargraph.colour = '#bb0000';
    bargraph.dot_size = 3;
    bargraph.text = "#{t('amount')}"
    bargraph.values = data

    x_axis = XAxis.new
    x_axis.labels = x_labels

    y_axis = YAxis.new
    y_axis.set_range(Champs21Precision.set_and_modify_precision(largest_value-(largest_value*2)),Champs21Precision.set_and_modify_precision(largest_value),Champs21Precision.set_and_modify_precision(largest_value/5))

    title = Title.new("#{t('finance_transactions')}")

    x_legend = XLegend.new("Examination name")
    x_legend.set_style('{font-size: 14px; color: #778877}')

    y_legend = YLegend.new("Marks")
    y_legend.set_style('{font-size: 14px; color: #770077}')

    chart = OpenFlashChart.new
    chart.set_title(title)
    chart.set_x_legend = x_legend
    chart.set_y_legend = y_legend
    chart.y_axis = y_axis
    chart.x_axis = x_axis

    chart.add_element(bargraph)


    render :text => chart.render

  end
  
  def graph_for_compare_monthly_report

    start_date = (params[:start_date]).to_date
    end_date = (params[:end_date]).to_date
    start_date2 = (params[:start_date2]).to_date
    end_date2 = (params[:end_date2]).to_date
    employees = Employee.find(:all)

    hr = Configuration.find_by_config_value("HR")
    donations_total = FinanceTransaction.donations_triggers(start_date,end_date)
    donations_total2 = FinanceTransaction.donations_triggers(start_date2,end_date2)
    fees = FinanceTransaction.total_fees(start_date,end_date).map{|t| t.transaction_total.to_f}.sum
    fees2 = FinanceTransaction.total_fees(start_date2,end_date2).map{|t| t.transaction_total.to_f}.sum
    income = FinanceTransaction.total_other_trans(start_date,end_date)[0]
    income2 = FinanceTransaction.total_other_trans(start_date2,end_date2)[0]
    expense = FinanceTransaction.total_other_trans(start_date,end_date)[1]
    expense2 = FinanceTransaction.total_other_trans(start_date2,end_date2)[1]

    #    other_transactions = FinanceTransaction.find(:all,
    #      :conditions => ["transaction_date >= '#{start_date}' and transaction_date <= '#{end_date}'and category_id !='#{3}' and category_id !='#{2}'and category_id !='#{1}'"])
    #    other_transactions2 = FinanceTransaction.find(:all,
    #      :conditions => ["transaction_date >= '#{start_date2}' and transaction_date <= '#{end_date2}'and category_id !='#{3}' and category_id !='#{2}'and category_id !='#{1}'"])


    x_labels = []
    data = []
    data2 = []
    largest_value =0

    unless hr.nil?
      salary = Employee.total_employees_salary(employees,start_date,end_date)
      salary2 = Employee.total_employees_salary(employees,start_date2,end_date2)
      unless salary <= 0 and salary2 <= 0
        x_labels << "#{t('salary')}"
        data << salary-(salary*2)
        data2 << salary2-(salary2*2)
        largest_value = salary if largest_value < salary
        largest_value = salary2 if largest_value < salary2
      end
    end
    unless donations_total <= 0 and donations_total2 <= 0
      x_labels << "#{t('donations')}"
      data << donations_total
      data2 << donations_total2
      largest_value = donations_total if largest_value < donations_total
      largest_value = donations_total2 if largest_value < donations_total2
    end

    unless fees <= 0 and fees2 <= 0
      x_labels << "#{t('fees_text')}"
      data << Champs21Precision.set_and_modify_precision(fees).to_f
      data2 << Champs21Precision.set_and_modify_precision(fees2).to_f
      largest_value = fees if largest_value < fees
      largest_value = fees2 if largest_value < fees2
    end

    Champs21Plugin::FINANCE_CATEGORY.each do |category|
      transaction1 =   FinanceTransaction.total_transaction_amount(category[:category_name],start_date,end_date)
      transaction2 =   FinanceTransaction.total_transaction_amount(category[:category_name],start_date2,end_date2)
      amount1 = transaction1[:amount]
      amount2 = transaction2[:amount]
      unless amount1 <= 0 and amount2 <= 0
        x_labels << "#{category[:category_name]}"
        transaction1[:category_type] == "income" ? data << amount1 : data << amount1-(amount1*2)
        transaction2[:category_type] == "income" ? data2 << amount2 : data2 << amount2-(amount2*2)
        largest_value = amount1 if largest_value < amount1
        largest_value = amount2 if largest_value < amount2
      end
    end

    unless income <= 0 and income2 <= 0
      x_labels << "#{t('other_income')}"
      data << income
      data2 << income2
      largest_value = income if largest_value < income
      largest_value = income2 if largest_value < income2
    end

    unless expense <= 0 and expense2 <= 0
      x_labels << "#{t('other_expense')}"
      data << Champs21Precision.set_and_modify_precision(expense-(expense*2)).to_f
      data2 << Champs21Precision.set_and_modify_precision(expense2-(expense2*2)).to_f
      largest_value = expense if largest_value < expense
      largest_value = expense2 if largest_value < expense2
    end

    #       other = 0
    #    other_transactions.each do |trans|
    #
    #      if trans.category.is_income? and trans.master_transaction_id == 0
    #        other += trans.amount
    #      else
    #        other -= trans.amount
    #      end
    #    end
    #    x_labels << "other"
    #    data << other
    #    largest_value = other if largest_value < other
    #    other2 = 0
    #    other_transactions2.each do |trans2|
    #      if trans2.category.is_income?
    #        other2 += trans2.amount
    #      else
    #        other2 -= trans2.amount
    #      end
    #    end
    #    data2 << other2
    #    largest_value = other2 if largest_value < other2

    largest_value += 500

    bargraph = BarFilled.new()
    bargraph.width = 1;
    bargraph.colour = '#bb0000';
    bargraph.dot_size = 3;
    bargraph.text = "#{t('for_the_period')} #{start_date}-#{end_date}"
    bargraph.values = data
    bargraph2 = BarFilled.new()
    bargraph2.width = 1;
    bargraph2.colour = '#000000';
    bargraph2.dot_size = 3;
    bargraph2.text = "#{t('for_the_period')} #{start_date2}-#{end_date2}"
    bargraph2.values = data2

    x_axis = XAxis.new
    x_axis.labels = x_labels

    y_axis = YAxis.new
    y_axis.set_range(Champs21Precision.set_and_modify_precision(largest_value-(largest_value*2)),Champs21Precision.set_and_modify_precision(largest_value),Champs21Precision.set_and_modify_precision(largest_value/5))

    title = Title.new("#{t('finance_transactions')}")

    x_legend = XLegend.new("#{t('examination_name')}")
    x_legend.set_style('{font-size: 14px; color: #778877}')

    y_legend = YLegend.new("#{t('marks')}")
    y_legend.set_style('{font-size: 14px; color: #770077}')

    chart = OpenFlashChart.new
    chart.set_title(title)
    chart.set_x_legend = x_legend
    chart.set_y_legend = y_legend
    chart.y_axis = y_axis
    chart.x_axis = x_axis

    chart.add_element(bargraph)
    chart.add_element(bargraph2)


    render :text => chart.render

  end

  #ddnt complete this graph!
  def graph_for_transaction_comparison

    start_date = (params[:start_date]).to_date
    end_date = (params[:end_date]).to_date
    employees = Employee.find(:all)

    hr = Configuration.find_by_config_value("HR")
    donations_total = FinanceTransaction.donations_triggers(start_date,end_date)
    fees = FinanceTransaction.total_fees(start_date,end_date).map{|t| t.transaction_total.to_f}.sum
    income = FinanceTransaction.total_other_trans(start_date,end_date)[0]
    expense = FinanceTransaction.total_other_trans(start_date,end_date)[1]
    #    other_transactions = FinanceTransaction.find(:all,
    #      :conditions => ["transaction_date >= '#{start_date}' and transaction_date <= '#{end_date}'and category_id !='#{3}' and category_id !='#{2}'and category_id !='#{1}'"])


    x_labels = []
    data1 = []
    data2 = []

    largest_value =0

    unless hr.nil?
      salary = Employee.total_employees_salary(employees,start_date,end_date)
    end
    unless salary <= 0
      x_labels << "#{t('salary')}"
      data << salary-(salary*2)
      largest_value = salary if largest_value < salary
    end
    unless donations_total <= 0
      x_labels << "#{t('donations')}"
      data << donations_total
      largest_value = donations_total if largest_value < donations_total
    end

    unless fees <= 0
      x_labels << "#{t('fees_text')}"
      data << fees
      largest_value = fees if largest_value < fees
    end

    unless income <= 0
      x_labels << "#{t('other_income')}"
      data << income
      largest_value = income if largest_value < income
    end

    unless expense <= 0
      x_labels << "#{t('other_expense')}"
      data << expense
      largest_value = expense if largest_value < expense
    end

    #    other_transactions.each do |trans|
    #      x_labels << trans.title
    #      if trans.category.is_income? and trans.master_transaction_id == 0
    #        data << trans.amount
    #      else
    #        data << ("-"+trans.amount.to_s).to_i
    #      end
    #      largest_value = trans.amount if largest_value < trans.amount
    #    end

    largest_value += 500

    bargraph = BarFilled.new()
    bargraph.width = 1;
    bargraph.colour = '#bb0000';
    bargraph.dot_size = 3;
    bargraph.text = "#{t('amount')}"
    bargraph.values = data

    x_axis = XAxis.new
    x_axis.labels = x_labels

    y_axis = YAxis.new
    y_axis.set_range(largest_value-(largest_value*2),largest_value,largest_value/5)

    title = Title.new("#{t('finance_transactions')}")

    x_legend = XLegend.new("#{t('examination_name')}")
    x_legend.set_style('{font-size: 14px; color: #778877}')

    y_legend = YLegend.new("#{t('marks')}")
    y_legend.set_style('{font-size: 14px; color: #770077}')

    chart = OpenFlashChart.new
    chart.set_title(title)
    chart.set_x_legend = x_legend
    chart.set_y_legend = y_legend
    chart.y_axis = y_axis
    chart.x_axis = x_axis

    chart.add_element(bargraph)


    render :text => chart.render


  end
  
  #other Fees
  def other_fee_new
    @other_fees = OtherFee.new(params[:other_fees])
    if request.post? and @other_fees.save
      flash[:notice] = "#{f('succesfully_saved')}"
      redirect_to  :action => 'other_fee'
    end
  end
  
  def other_fees
    @other_fees = OtherFee.paginate({:is_deleted=>0},:page => params[:page], :per_page => 10)
  end
  
  def delete_other_fee
    @other_fee = OtherFee.find(params[:id])
    @other_fee.update_attributes(:is_deleted=>true)
    flash[:notice] = "#{t('succesfully_removed')}"
    redirect_to  :action => 'other_fee'
  end
  
  #fee Discount
  def fee_discounts
    @batches = Batch.active
  end
  
  def fee_discount_new
    @batches = Batch.active
  end

  def load_discount_create_form
    if params[:type]== "batch_wise"
      @fee_categories =  FinanceFeeCategory.find(:all, :conditions => "finance_fee_categories.is_visible = #{true}", :joins=>"INNER JOIN finance_fee_particulars on finance_fee_particulars.finance_fee_category_id=finance_fee_categories.id AND finance_fee_particulars.is_tmp = 0 AND finance_fee_particulars.is_deleted = 0 INNER JOIN batches on batches.id=finance_fee_particulars.batch_id AND batches.is_active = 1 AND batches.is_deleted = 0 AND finance_fee_categories.is_deleted=0",:group=>'finance_fee_categories.name')
      @fee_discount = BatchFeeDiscount.new
      render :update do |page|
        page.replace_html "form-box", :partial => "batch_wise_discount_form";
        page.replace_html 'form-errors', :text =>""
      end
    elsif params[:type]== "category_wise"
      @fee_categories = FinanceFeeCategory.find(:all, :conditions => "finance_fee_categories.is_visible = #{true}", :joins=>"INNER JOIN finance_fee_particulars on finance_fee_particulars.finance_fee_category_id=finance_fee_categories.id AND finance_fee_particulars.is_tmp = 0 AND finance_fee_particulars.is_deleted = 0 INNER JOIN batches on batches.id=finance_fee_particulars.batch_id AND batches.is_active = 1 AND batches.is_deleted = 0 AND finance_fee_categories.is_deleted=0",:group=>'finance_fee_categories.name')
      @student_categories = StudentCategory.active
      render :update do |page|
        page.replace_html "form-box", :partial => "category_wise_discount_form"
        page.replace_html 'form-errors', :text =>""
      end
    elsif params[:type] == "student_wise"
      @fee_categories = FinanceFeeCategory.find(:all, :conditions => "finance_fee_categories.is_visible = #{true}", :joins=>"INNER JOIN finance_fee_particulars on finance_fee_particulars.finance_fee_category_id=finance_fee_categories.id AND finance_fee_particulars.is_tmp = 0 AND finance_fee_particulars.is_deleted = 0 INNER JOIN batches on batches.id=finance_fee_particulars.batch_id AND batches.is_active = 1 AND batches.is_deleted = 0 AND finance_fee_categories.is_deleted=0",:group=>'finance_fee_categories.name')
      @courses = Course.active
      render :update do |page|
        page.replace_html "form-box", :partial => "student_wise_discount_form"
        page.replace_html 'form-errors', :text =>""
      end
    else
      render :update do |page|
        page.replace_html "form-box", :text => ""
        page.replace_html 'form-errors', :text =>""
      end
    end
  end

  def load_discount_batch
    if params[:id].present?
      @course = Course.find(params[:id])
      @batches =Batch.find(:all,:joins=>"INNER JOIN students on students.batch_id=batches.id",:conditions=>"batches.course_id=#{@course.id}").uniq
      #@batches = @course.batches.active
      render :update do |page|
        page.replace_html "batch-box", :partial => "fee_discount_batch_list"
      end
    else
      render :update do |page|
        page.replace_html "batch-box", :text => ""
      end
    end
  end

  def load_batch_fee_category
    if params[:batch].present?
      @batch=Batch.find(params[:batch])
      fees_categories =FinanceFeeCategory.find(:all,:joins=>"INNER JOIN category_batches on category_batches.finance_fee_category_id=finance_fee_categories.id INNER JOIN finance_fee_particulars on finance_fee_particulars.finance_fee_category_id=category_batches.finance_fee_category_id",
        :conditions=>"finance_fee_particulars.batch_id=#{@batch.id} and category_batches.batch_id=#{@batch.id} AND finance_fee_particulars.is_tmp = 0 and finance_fee_particulars.is_deleted=false and finance_fee_categories.is_deleted=false and finance_fee_categories.is_master=1").uniq
      #fees_categories = @batch.finance_fee_categories.find(:all,:conditions=>"is_deleted = 0 and is_master = 1")
      @fees_categories=[]
      fees_categories.each do |f|
        particulars=f.fee_particulars.select{|s| s.is_deleted==false}
        unless particulars.empty?
          @fees_categories << f
        end
      end
      render :update do |page|
        page.replace_html "fee-category-box", :partial => "fee_discount_category_list"
      end
    else
      render :update do |page|
        page.replace_html "fee-category-box", :text => ""
      end
    end
  end

  def batch_wise_discount_create
    unless params[:fee_collection].blank?
      unless params[:discount_on].nil? or params[:discount_on].blank?
        id_str = params[:discount_on][:discount_on].split("-")
        finance_fee_category_id = id_str[1]
        if finance_fee_category_id.to_i > 0
          finance_fee_particular_category_id = id_str[0]
          is_late = 0
          if finance_fee_particular_category_id.index('F') == 0
            is_late = 1
            finance_fee_particular_category_id = finance_fee_particular_category_id.gsub("F","")
          end
        else
          @fee_discount = BatchFeeDiscount.new(params[:fee_discount])
          @fee_discount.errors.add_to_base("#{t('fees_particular_cant_be_blank')}")
          @error = true
        end
      else
        @fee_discount = BatchFeeDiscount.new(params[:fee_discount])
        @fee_discount.errors.add_to_base("#{t('fees_particular_cant_be_blank')}")
        @error = true
      end
      if ! @error
        FeeDiscount.transaction do
          params[:fee_collection][:category_ids].each do |c|
            # @fee_category = FinanceFeeCategory.find(params[:category])
            @fee_discount = FeeDiscount.new(params[:fee_discount])
            # @fee_discount.finance_fee_category_id =params[:category]
            @fee_discount.is_onetime=params[:fee_discount][:type]
            @fee_discount.receiver_type="Batch"
            @fee_discount.receiver_id = c
            @fee_discount.finance_fee_particular_category_id = finance_fee_particular_category_id
            @fee_discount.is_late = is_late
            @fee_discount.batch_id = c
            unless @fee_discount.save
              @error = true
              raise ActiveRecord::Rollback
            else
              if @fee_discount.is_onetime
                fee_discount_id = @fee_discount.id
                finance_fees_auto_categories = FinanceFeesAutoCategory.find(:all, :conditions => "finance_fee_category_id = #{@fee_discount.finance_fee_category_id}")
                unless finance_fees_auto_categories.blank?
                  finance_fees_auto_categories.each do |finance_fees_auto_category|
                    fee_discount = FeeDiscount.new(params[:fee_discount])
                    fee_discount.finance_fee_category_id = finance_fees_auto_category.finance_fee_auto_category_id
                    fee_discount.receiver_type="Batch"
                    fee_discount.is_onetime=params[:fee_discount][:type]
                    fee_discount.batch_id=c
                    fee_discount.receiver_id = c
                    fee_discount.finance_fee_particular_category_id = finance_fee_particular_category_id
                    fee_discount.is_late = is_late
                    fee_discount.parent_id = fee_discount_id
                    fee_discount.save
                  end
                end
              end
            end
          end
        end
      end
    else
      @fee_discount = BatchFeeDiscount.new(params[:fee_discount])
      @fee_discount.errors.add_to_base("#{t('fees_category_cant_be_blank')}")
      @error = true
    end
  end

  def category_wise_fee_discount_create
    unless params[:fee_collection].blank?
      unless params[:discount_on].nil? or params[:discount_on].blank?
        id_str = params[:discount_on][:discount_on].split("-")
        finance_fee_category_id = id_str[1]
        if finance_fee_category_id.to_i > 0
          finance_fee_particular_category_id = id_str[0]
          is_late = 0
          if finance_fee_particular_category_id.index('F') == 0
            is_late = 1
            finance_fee_particular_category_id = finance_fee_particular_category_id.gsub("F","")
          end
        else
          @fee_discount = BatchFeeDiscount.new(params[:fee_discount])
          @fee_discount.errors.add_to_base("#{t('fees_particular_cant_be_blank')}")
          @error = true
        end
      else
        @fee_discount = BatchFeeDiscount.new(params[:fee_discount])
        @fee_discount.errors.add_to_base("#{t('fees_particular_cant_be_blank')}")
        @error = true
      end
      
      if ! @error
        FeeDiscount.transaction do
          params[:fee_collection][:category_ids].each do |c|
            #@fee_category = FinanceFeeCategory.find(c)
            @fee_discount = FeeDiscount.new(params[:fee_discount])
            #        @fee_discount.finance_fee_category_id = params[:category]
            @fee_discount.receiver_type="StudentCategory"
            @fee_discount.is_onetime=params[:fee_discount][:type]
            @fee_discount.batch_id=c
            @fee_discount.finance_fee_particular_category_id = finance_fee_particular_category_id
            @fee_discount.is_late = is_late
            unless @fee_discount.save
              @error = true
              @fee_discount.errors.add_to_base("#{t('select_student_category')}") if params[:fee_discount][:receiver_id].empty?
              raise ActiveRecord::Rollback
            else
              if @fee_discount.is_onetime
                fee_discount_id = @fee_discount.id
                finance_fees_auto_categories = FinanceFeesAutoCategory.find(:all, :conditions => "finance_fee_category_id = #{@fee_discount.finance_fee_category_id}")
                unless finance_fees_auto_categories.blank?
                  finance_fees_auto_categories.each do |finance_fees_auto_category|
                    fee_discount = FeeDiscount.new(params[:fee_discount])
                    fee_discount.finance_fee_category_id = finance_fees_auto_category.finance_fee_auto_category_id
                    fee_discount.receiver_type="StudentCategory"
                    fee_discount.is_onetime=params[:fee_discount][:type]
                    fee_discount.batch_id=c
                    fee_discount.finance_fee_particular_category_id = finance_fee_particular_category_id
                    fee_discount.is_late = is_late
                    fee_discount.parent_id = fee_discount_id
                    fee_discount.save
                  end
                end
              end
            end
          end
        end
      end
    else
      @fee_discount = FeeDiscount.new(params[:fee_discount])
      @fee_discount.errors.add_to_base("#{t('batch_cant_be_blank')}")
      @error = true
    end
  end

  def student_wise_fee_discount_create
    @error = false
    @fee_discount = FeeDiscount.new(params[:fee_discount])
    batch=Batch.find_by_id(params[:fee_discount][:batch_id])
    unless (params[:fee_discount][:finance_fee_category_id]).blank?
      unless params[:discount_on].nil? or params[:discount_on].blank?
        id_str = params[:discount_on][:discount_on].split("-")
        finance_fee_category_id = id_str[1]
        if finance_fee_category_id.to_i > 0
          finance_fee_particular_category_id = id_str[0]
          is_late = 0
          if finance_fee_particular_category_id.index('F') == 0
            is_late = 1
            finance_fee_particular_category_id = finance_fee_particular_category_id.gsub("F","")
          end
        else
          @fee_discount = BatchFeeDiscount.new(params[:fee_discount])
          @fee_discount.errors.add_to_base("#{t('fees_particular_cant_be_blank')}")
          @error = true
        end
      else
        @fee_discount = BatchFeeDiscount.new(params[:fee_discount])
        @fee_discount.errors.add_to_base("#{t('fees_particular_cant_be_blank')}")
        @error = true
      end
      @fee_category = FinanceFeeCategory.find(@fee_discount.finance_fee_category_id)
      unless (params[:fee_collection][:students]).blank?
        student_ids = params[:fee_collection][:students]
        student_ids.each do |si|
          s = Student.find(si)
          unless s.nil?
            if FeeDiscount.find_by_type_and_receiver_id('StudentFeeDiscount',s.id,:conditions=>"finance_fee_category_id = #{@fee_category.id}").present?
              @error = true
              @fee_discount.errors.add_to_base("#{t('flash20')} - #{a}")
            end
            unless (s.batch_id == batch.id)
              @error = true
              @fee_discount.errors.add_to_base("#{a} #{t('does_not_belong_to_batch')} #{batch.full_name}")
            end
          else
            @error = true
            @fee_discount.errors.add_to_base("#{a} #{t('is_invalid_admission_no')}")
          end
        end
        unless @error
          student_ids.each do |si|
            s = Student.find(si)
            @fee_discount =FeeDiscount.new(params[:fee_discount])
            @fee_discount.is_onetime=params[:fee_discount][:type]
            @fee_discount.receiver_type="Student"
            @fee_discount.receiver_id = s.id
            @fee_discount.batch_id=s.batch_id
            @fee_discount.finance_fee_particular_category_id = finance_fee_particular_category_id
            @fee_discount.is_late = is_late
            unless @fee_discount.save
              @error = true
            else
              if @fee_discount.is_onetime
                fee_discount_id = @fee_discount.id
                finance_fees_auto_categories = FinanceFeesAutoCategory.find(:all, :conditions => "finance_fee_category_id = #{@fee_discount.finance_fee_category_id}")
                unless finance_fees_auto_categories.blank?
                  finance_fees_auto_categories.each do |finance_fees_auto_category|
                    fee_discount =FeeDiscount.new(params[:fee_discount])
                    fee_discount.finance_fee_category_id = finance_fees_auto_category.finance_fee_auto_category_id
                    fee_discount.is_onetime=params[:fee_discount][:type]
                    fee_discount.receiver_type="Student"
                    fee_discount.receiver_id = s.id
                    fee_discount.batch_id=s.batch_id
                    fee_discount.finance_fee_particular_category_id = finance_fee_particular_category_id
                    fee_discount.is_late = is_late
                    fee_discount.parent_id = fee_discount_id
                    fee_discount.save
                  end
                end
              end
            end
          end
        end
      else
        @error = true
        @fee_discount.errors.add_to_base("#{t('admission_cant_be_blank')}")
      end
    else
      @error = true
      @fee_discount.errors.add_to_base("#{t('fees_category_cant_blank')}")
    end
  end

  def update_master_fee_category_list
    @batch = Batch.find(params[:id])
    @fee_categories=@batch.finance_fee_categories.find(:all,:conditions=>"is_master=1 and is_deleted= 0")
    #@fee_categories = FinanceFeeCategory.find_all_by_batch_id(@batch.id, :conditions=>"is_master=1 and is_deleted= 0")
    render :update do |page|
      page.replace_html "master-category-box", :partial => "update_master_fee_category_list"
    end
  end

  def show_fee_discounts
    @batch=Batch.find(params[:b_id])
    if params[:id]==""
      render :update do |page|
        page.replace_html "discount-box", :text=>""
      end
    else
      if params[:id] == '0'
        @discounts = FeeDiscount.all(:conditions=>["batch_id='#{@batch.id}' and finance_fee_category_id= 0"])
      else
        @fee_category = FinanceFeeCategory.find(params[:id])
        @discounts = @fee_category.fee_discounts.all(:conditions=>["batch_id='#{@batch.id}' and is_deleted= 0"])
      end
      
      render :update do |page|
        page.replace_html "discount-box", :partial => "show_fee_discounts"
      end
     
    end
  end

  def edit_fee_discount
    @fee_discount = FeeDiscount.find(params[:id])
  end

  def update_fee_discount
    #
    #@fee_discount.errors.add_to_base("#{t('admission_cant_be_blank')}")
    @fee_discount = FeeDiscount.find(params[:id])
    unless @fee_discount.update_attributes(params[:fee_discount])
      @error = true
    else
      if @fee_discount.is_onetime 
        fee_discount_id = @fee_discount.id
        fee_discounts = FeeDiscount.find(:all, :conditions => "parent_id = #{fee_discount_id}")
        unless fee_discounts.blank?
          fee_discounts.each do |f|
            collection_discounts = CollectionDiscount.find(:all, :conditions => "fee_discount_id = #{f.id}")
            if collection_discounts.blank?
                f.update_attributes(params[:fee_discount])
            end
          end
        end
      end
      @fee_category = @fee_discount.finance_fee_category
      @discounts = @fee_category.fee_discounts.all(:conditions=>["batch_id='#{@fee_discount.batch_id}'  and is_deleted= 0"])
      #@fee_category.is_collection_open ? @discount_edit = false : @discount_edit = true
    end
  end

  def delete_fee_discount
    @fee_discount = FeeDiscount.find(params[:id])
    #batch=@fee_discount.batch
    @fee_category = FinanceFeeCategory.find(@fee_discount.finance_fee_category_id)
    @error = true  unless @fee_discount.update_attributes(:is_deleted=>true)
    unless @error
      if @fee_discount.is_onetime 
        fee_discount_id = @fee_discount.id
        fee_discounts = FeeDiscount.find(:all, :conditions => "parent_id = #{fee_discount_id}")
        unless fee_discounts.blank?
          fee_discounts.each do |f|
            collection_discounts = CollectionDiscount.find(:all, :conditions => "fee_discount_id = #{f.id}")
            if collection_discounts.blank?
              f.update_attributes(:is_deleted=>true)
            end
          end
        end
      end
    end
    #abort(@fee_discount.inspect)
    unless @fee_category.nil?
      @discounts = @fee_category.fee_discounts.all(:conditions=>["batch_id='#{@fee_discount.batch_id}' and is_deleted= #{false}"])
      #@fee_category.is_collection_open ? @discount_edit = false : @discount_edit = true
    end
    render :update do |page|
      page.replace_html "discount-box", :partial => "show_fee_discounts"
      if @error
        page.replace_html 'form-errors', :partial => 'errors', :object => @fee_discount
      else
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('particulars_deleted_successfully')}.</p>"
      end
      #page.replace_html "flash-notice", :text => "<p class='flash-msg'>#{t('discount_deleted_successfully')}.</p>"
    end

  end

  # def collection_details_view
  #   @fee_collection = FinanceFeeCollection.find(params[:id])
  #   @particulars = @fee_collection.finance_fee_particulars.all(:conditions=>["batch_id='#{params[:batch_id]}'"])
  #   @total_payable=@particulars.map{|s| s.amount}.sum.to_f
  #   @discounts = @fee_collection.fee_discounts.all(:conditions=>["batch_id='#{params[:batch_id]}'"])
  #   end
  def collection_details_view
    fee_collection  =  FinanceFeeCollection.find(:first, :conditions => "id IN (#{params[:id]})")
    unless fee_collection.blank?
      for_admission = false
      if fee_collection.for_admission.type.to_s == "FalseClass" or fee_collection.for_admission.type.to_s == "TrueClass"
        if fee_collection.for_admission
          for_admission = true
        end
      else
        if fee_collection.for_admission.to_i == 1
          for_admission = true
        end
      end
      if for_admission
        redirect_to  :action => "collection_details_view_admission",:id => params[:id]
      end
    end
      
    require "yaml"
    finance_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/finance_absent_fine.yml")['school']
    all_schools = finance_config['ids'].split(",")
    current_school = MultiSchool.current_school.id
    @absent_fine = false
    if all_schools.include?(current_school.to_s)
      @absent_fine = true
    end
    
    @option = 0;
    unless params[:batch_id].nil?
      @finance_fees = FinanceFee.all(:select=>"finance_fees.id,finance_fees.fee_collection_id,finance_fees.student_id,finance_fees.is_paid,finance_fees.balance",:joins=>"INNER JOIN students ON students.id = finance_fees.student_id",:order => "if(students.class_roll_no = '' or students.class_roll_no is null,0,cast(students.class_roll_no as unsigned)),students.first_name ASC", :conditions=>"finance_fees.fee_collection_id = #{params[:id]} AND finance_fees.batch_id = #{params[:batch_id]}")
    else
      fee_collection  =  FinanceFeeCollection.find(:first, :conditions => "id IN (#{params[:id]})")
      unless fee_collection.nil?  
        fee_collection_name = fee_collection.name
        fee_collections = FinanceFeeCollection.find_all_by_name(fee_collection_name)
        fee_collections_ids = fee_collections.map(&:id)
        fee_collections_ids = fee_collections_ids.reject { |f| f.to_s.empty? }
        if fee_collections_ids.blank?
          fee_collections_ids[0] = 0
        end 
        @finance_fees = FinanceFee.all(:select=>"finance_fees.id,finance_fees.student_id,finance_fees.is_paid,finance_fees.balance",:joins=>"INNER JOIN students ON students.id = finance_fees.student_id",:order => "if(students.class_roll_no = '' or students.class_roll_no is null,0,cast(students.class_roll_no as unsigned)),students.first_name ASC", :conditions=>"finance_fees.fee_collection_id IN (#{fee_collections_ids.join(",")})")
      end
    end
  end
  
  def collection_details_view_admission
    unless params[:id].blank?
      @fee_id = params[:id]
      fee_collection  =  FinanceFeeCollection.find(:first, :conditions => "id = #{params[:id]}")
      unless fee_collection.nil?  
        @finance_fees = FinanceFee.all(:select=>"finance_fees.id,finance_fees.student_id,finance_fees.is_paid,finance_fees.balance",:joins=>"INNER JOIN students ON students.id = finance_fees.student_id",:order => "if(students.class_roll_no = '' or students.class_roll_no is null,0,cast(students.class_roll_no as unsigned)),students.first_name ASC", :conditions=>"finance_fees.fee_collection_id = #{fee_collection.id}")
      end
    end
  end
  
  def search_student_no_fees
    @fee_id = 0
    unless params[:fee_id].blank?
      @fee_id = params[:fee_id]
    end
    unless params[:query].blank?
      @students = Student.active.find(:all,
          :conditions => ["(finance_fees.student_id IS NULL) and (students.first_name LIKE ? OR 
                            students.middle_name LIKE ? OR students.last_name LIKE ? 
                            OR students.admission_no LIKE ? OR (concat(students.first_name, \" \", 
                            students.last_name) LIKE ? ) OR (concat(students.first_name, \"+\", students.last_name) LIKE ? )) ",
            "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
            "%#{params[:query]}%", "%#{params[:query]}%", "%#{params[:query]}%" ],
          :joins => "LEFT JOIN finance_fees ON finance_fees.student_id = students.id",
          :order => "students.batch_id asc,students.first_name asc",:include =>  [{:batch=>:course}]) unless params[:query] == ''
    end
    render :update do |page|
      page << 'j("#student-list").show();'
      page.replace_html "student-list", :partial => "search_student_with_no_fees"
    end
  end

  def collection_details_view_inactive
    #@finance_fees = FinanceFee.all(:select=>"finance_fees.id,finance_fees.student_id,finance_fees.is_paid,finance_fees.balance",:joins=>"INNER JOIN students ON students.id = finance_fees.student_id", :conditions=>"finance_fees.fee_collection_id = #{params[:id]} AND finance_fees.batch_id = #{params[:batch_id]}")
    @finance_fees = FinanceFee.all(:select=>"finance_fees.id,finance_fees.student_id,finance_fees.is_paid,finance_fees.balance",:joins=>"INNER JOIN students ON students.id = finance_fees.student_id",:order => "if(students.class_roll_no = '' or students.class_roll_no is null,0,cast(students.class_roll_no as unsigned)),students.first_name ASC", :conditions=>"finance_fees.fee_collection_id = #{params[:id]} AND finance_fees.batch_id = #{params[:batch_id]}")
    @option = params[:option]
    @batch = Batch.find(params[:batch_id])
    @students = @batch.students
    finance_student_ids = @finance_fees.map(&:student_id)
    student_ids = @students.map(&:id)
    inactive_student_ids = student_ids - finance_student_ids
    unless inactive_student_ids.blank?
      inactive_student_ids = inactive_student_ids.reject { |i| i.to_s.empty? }
      if inactive_student_ids.blank?
        inactive_student_ids[0] = 0
      end
      @inactive_students = Student.find(:all, :conditions => "id In (#{inactive_student_ids.join(",")})")
    end
    render :update do |page|
      page.replace_html "resultDiv", :partial => "collection_details_view_inactive"
    end
  end
  
  def advance_collection_details_view
    @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "fee_collection_id = '#{params[:id]}'")
  end
  
  def fee_collections_details_view
    @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "fee_collection_id = '#{params[:id]}'")
  end

  def fixed_category_name
    @cat_names = ['Fee','Salary','Donation']
    @plugin_cat = []
    Champs21Plugin::FINANCE_CATEGORY.each do |category|
      @cat_names << "#{category[:category_name]}"
      @plugin_cat << "#{category[:category_name]}"
    end
    @fixed_cat_ids = FinanceTransactionCategory.find(:all,:conditions=>{:name=>@cat_names}).collect(&:id)
  end
  
  def delete_transaction_fees_defaulters
    transaction_deletion
    redirect_to  :action => "pay_fees_defaulters",:id => @student,:date => @date,:batch_id=>params[:batch_id]
  end
  
  def delete_transaction_for_student
    transaction_deletion
    render :update do |page|
      page.replace_html "fee_submission", :partial => "fees_submission_form"
    end
  end
  
  def delete_transaction_by_batch
    @from_batch_fee = true
    unless params[:student_fees].nil?
      if params[:student_fees].to_i == 1
        @from_batch_fee = false
      end
    end

    @show_only_structure = false
    unless params[:student_fees_struct].nil?
      if params[:student_fees_struct].to_i == 1
        @show_only_structure = true
      end
    end
    
    @show_only_structure = false
    transaction_deletion
    @batch   = Batch.find(params[:batch_id])
    student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
    @dates   = FinanceFeeCollection.find(:all)
    @fee = FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}" ,:joins=>'INNER JOIN students ON finance_fees.student_id = students.id')
    @student ||= @fee.student
    @prev_student = @student.previous_fee_student(@date.id,student_ids)
    @next_student = @student.next_fee_student(@date.id,student_ids)

    render :update do |page|
      page.replace_html "student", :partial => "student_fees_submission"
      page << "loadJS();"
      page << 'j(".select2-combo").select2();'
    end
  end
  
  def transaction_deletion
    @batch   = Batch.find(params[:batch_id])
    advance_fee_collection = false
    @self_advance_fee = false
    @fee_has_advance_particular = false
        
    @student = Student.find(params[:id])
    @date = @fee_collection = FinanceFeeCollection.find(params[:date])
    @financefee = @student.finance_fee_by_date(@date)
    
    if @financefee.has_advance_fee_id
      if @date.is_advance_fee_collection
        @self_advance_fee = true
        advance_fee_collection = true
      end
      @fee_has_advance_particular = true
      @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
      @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
      if @advance_ids.blank?
        @advance_ids[0] = 0
      end
      @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
    end
    
    #if @financefee.advance_fee_id.to_i > 0
      
    @financetransaction=FinanceTransaction.find(params[:transaction_id])
    balance=FinanceFee.get_student_balance(@date, @student, @financefee)
    @financefee.update_attributes(:is_paid=>false,:balance=>balance)
    FeeTransaction.destroy_all(:finance_transaction_id=>params[:transaction_id])

    if @financetransaction
      transaction_attributes=@financetransaction.attributes
      transaction_attributes.delete "id"
      transaction_attributes.delete "created_at"
      transaction_attributes.delete "updated_at"
      transaction_attributes.merge!(:user_id=>current_user.id,:collection_name=>@fee_collection.name)
      cancelled_transaction=CancelledFinanceTransaction.new(transaction_attributes)
      if @financetransaction.destroy
        cancelled_transaction.save
      end

    end
    @due_date = @fee_collection.due_date
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])

    flash[:warning]=nil
    flash[:notice]=nil

    @paid_fees = @financefee.finance_transactions


    exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
    unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
      exclude_particular_ids = exclude_particular_ids
    else
      exclude_particular_ids = [0]
    end
    
    if advance_fee_collection
      fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
      fee_collection_advances_particular = fee_collection_advances_particular.reject { |a| a.to_s.empty? }
      if fee_collection_advances_particular.blank?
        fee_collection_advances_particular[0] = 0
      end
      
      
      if fee_collection_advances_particular.include?(0)
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      end
    else
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
    end
    @total_discount = 0
    
    if advance_fee_collection
      month = 1
      payable = 0
      @fee_collection_advances.each do |fee_collection_advance|
        @fee_particulars.each do |particular|
          if fee_collection_advance.particular_id == particular.finance_fee_particular_category_id
            payable += particular.amount * fee_collection_advance.no_of_month.to_i
          else
            payable += particular.amount
          end
        end
      end
      @total_payable=payable.to_f
    else  
      @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
    end
    
    
    if advance_fee_collection
      calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
    else
      if @fee_has_advance_particular
        calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
      else
        calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
      end
    end
    
    bal=(@total_payable-@total_discount).to_f
    
    unless params[:submission_date].nil? or params[:submission_date].empty? or params[:submission_date].blank?
      require 'date'
      @submission_date = Date.parse(params[:submission_date])
      days=(Date.parse(params[:submission_date])-@date.due_date.to_date).to_i
    else
      @submission_date = Date.today
      if @financefee.is_paid
        @paid_fees = @financefee.finance_transactions
        days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
      else
        days=(Date.today-@date.due_date.to_date).to_i
      end
    end
    
    fine_enabled = true
    student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
    unless student_fee_configuration.blank?
      if student_fee_configuration.config_value.to_i == 1
        fine_enabled = true
      else
        fine_enabled = false
      end
    end
    
    if @tmp_paid_fees.blank?
      @tmp_paid_fees = @financefee.finance_transactions
    end
    
    unless @tmp_paid_fees.blank?
      @tmp_paid_fees.each do |paid_fee|
        transaction_id = paid_fee.id
        online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
        unless online_payments.blank?
          fine_enabled = false
        end
      end
    end
    
    auto_fine=@date.fine
    
    @has_fine_discount = false
    if days > 0 and auto_fine and fine_enabled #and @financefee.is_paid == false
      @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
      @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule

      calculate_extra_fine(@date, @batch, @student, @fine_rule)

      @new_fine_amount = @fine_amount
      get_fine_discount(@date, @batch, @student)
      if @fine_amount < 0
        @fine_amount = 0
      end
    end
    
    @fine_amount=0 if @financefee.is_paid
        
    unless advance_fee_collection
      if @total_discount == 0
        @adv_fee_discount = true
        @actual_discount = 0
        calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
      end
    end
  end

  def update_deleted_transactions
    @transactions =CancelledFinanceTransaction.paginate(:page => params[:page], :per_page => 20,:conditions=>["created_at >='#{Date.today}' and created_at <'#{Date.today+1.day}'"],:order=>'created_at desc')
  end

  def transaction_filter_by_date
    @start_date=params[:s_date]
    @end_date=params[:e_date]
    @transactions = CancelledFinanceTransaction.paginate(:page => params[:page], :per_page => 20,
      :order => 'created_at desc', :conditions => ["created_at >= '#{@start_date}' and created_at < '#{@end_date.to_date+1.day}'"])
    render :update do |page|
      page.replace_html 'search_div', :partial=>"finance/search_by_date_deleted_transactions"
    end
  end

  def list_deleted_transactions
    @transactions =CancelledFinanceTransaction.paginate(:page => params[:page], :per_page => 20,:conditions=>["created_at >='#{Date.today}' and created_at <'#{Date.today+1.day}'"],:order=>'created_at desc')
    render :update do |page|
      page.replace_html 'deleted_transactions', :partial=>"finance/deleted_transactions"
    end
  end

  def search_fee_collection
    if params[:option]==t('fee_collection_name')
      @transactions = CancelledFinanceTransaction.paginate(:page => params[:page], :per_page => 20,:order=>'created_at desc',
        :conditions => ["collection_name LIKE ?",
          "#{params[:query]}%"]) unless params[:query] == ''
    elsif params[:option]==t('date_text')
      @transactions = CancelledFinanceTransaction.paginate(:page => params[:page], :per_page => 20,:order=>'created_at desc',
        :conditions => ["created_at LIKE ?",
          "#{params[:query]}%"]) unless params[:query] == ''
    else
      if Champs21Plugin.can_access_plugin?("champs21_instant_fee")
        @transactions = CancelledFinanceTransaction.paginate(:page => params[:page], :per_page => 20,:order=>'created_at desc',:joins=>'LEFT OUTER JOIN students ON students.id = payee_id LEFT OUTER JOIN employees ON employees.id = payee_id LEFT OUTER JOIN instant_fees ON instant_fees.id = finance_id' ,
          :conditions => ["students.admission_no LIKE ? OR employees.employee_number LIKE ? OR instant_fees.guest_payee LIKE ?",
            "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%"]) unless params[:query] == ''
      else
        @transactions = CancelledFinanceTransaction.paginate(:page => params[:page], :per_page => 20,:order=>'created_at desc',:joins=>'LEFT OUTER JOIN students ON students.id = payee_id LEFT OUTER JOIN employees ON employees.id = payee_id' ,
          :conditions => ["students.admission_no LIKE ? OR employees.employee_number LIKE ?",
            "#{params[:query]}%","#{params[:query]}%"]) unless params[:query] == ''
      end
    end

    render :update do |page|
      page.replace_html 'search_div', :partial=>"finance/search_deleted_transactions"
    end
    #render :partial => "finance/search_deleted_transactions"
  end

  def transactions_advanced_search
    if (params[:search] or params[:date])


      search_attr=params[:search].delete_if { |k, v| v=="" }
      condition_attr=""
      search_attr.keys.each do |k|
        if ["collection_name","category_id"].include?(k)

          condition_attr=condition_attr+" AND cancelled_finance_transactions.#{k} LIKE ? "

        elsif ["first_name","admission_no"].include?(k)
          condition_attr=condition_attr+" AND students.#{k} LIKE ?"
        elsif ["employee_number","employee_name"].include?(k)

          k=="employee_number"? condition_attr=condition_attr+" AND employees.#{k} LIKE ?" : condition_attr=condition_attr+" AND employees.first_name LIKE ?"
        else
          condition_attr=condition_attr+" AND instant_fees.#{k} LIKE ?" if Champs21Plugin.can_access_plugin?("champs21_instant_fee")
        end

      end
      #p condition_attr.split(' ')[1..-1].join(' ')
      unless condition_attr.empty?
        condition_attr=condition_attr.split(' ')[1..-1].join(' ')
        condition_attr="("+condition_attr+")"+" AND (cancelled_finance_transactions.created_at < ? AND cancelled_finance_transactions.created_at > ?)"
      else
        condition_attr= "(cancelled_finance_transactions.created_at < ? AND cancelled_finance_transactions.created_at > ?)"
      end
      condition_array=[]
      condition_array << condition_attr
      search_attr.values.each{|c| condition_array<< (c+"%")}
      #i=2
      condition_array<<"#{params[:date][:end_date].to_date+1.day}%"
      condition_array<<"#{params[:date][:start_date]}%"
      #params[:date].values.each{|d| i=i-1;condition_array<< (d.to_date+i.day)}
      if Champs21Plugin.can_access_plugin?("champs21_instant_fee")
        @transactions = CancelledFinanceTransaction.paginate(:page => params[:page], :per_page => 20,:order=>'created_at desc',:joins=>'LEFT OUTER JOIN students ON students.id = payee_id LEFT OUTER JOIN employees ON employees.id = payee_id LEFT OUTER JOIN instant_fees ON instant_fees.id = finance_id' ,
          :conditions => condition_array) unless params[:query] == ''
      else
        @transactions = CancelledFinanceTransaction.paginate(:page => params[:page], :per_page => 20,:order=>'created_at desc',:joins=>'LEFT OUTER JOIN students ON students.id = payee_id LEFT OUTER JOIN employees ON employees.id = payee_id ' ,
          :conditions => condition_array) unless params[:query] == ''
      end
      @searched_for = ""
      search_attr.each do|k,v|
        @searched_for=@searched_for+ "<span> #{k.humanize} </span>"
        @searched_for=@searched_for+ ":" +v.humanize+" "

      end
      params[:date].each do|k,v|
        @searched_for=@searched_for+ "<span> #{k.humanize} </span>"
        @searched_for=@searched_for+ ":" +v.humanize+" "

      end
      if params[:remote]=="remote"
        render :update do |page|
          page.replace_html 'search-result', :partial=>"finance/transaction_advanced_search"
        end
      end
    end
  end

  def new_refund
    @refund_rule=RefundRule.new
    @collections=FinanceFeeCollection.find(:all,:conditions=>{:is_deleted=>false},:group=>:name)
  end

  def create_refund

    @refund_rule=RefundRule.new
    @collections=FinanceFeeCollection.find(:all,:conditions=>{:is_deleted=>false},:group=>:name)
    if request.post?
      @refund_rule.attributes=params[:refund_rule]
      @refund_rule.user=current_user
      if @refund_rule.save
        flash[:notice]="#{t('refund_rule_created')}"
        redirect_to :controller=>'finance',:action=>'create_refund'
      else
        render :create_refund
      end
    end
  end

  def refund_student_search
    query = params[:query]
    if query.length>= 3
      @students= Student.find(:all,:joins=>'INNER JOIN finance_fees ON finance_fees.student_id = students.id AND finance_fees.balance=0',
        :conditions => ["first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                            OR admission_no = ? OR (concat(first_name, \" \", last_name) LIKE ? ) ",
          "#{query}%","#{query}%","#{query}%",
          "#{query}", "#{query}" ],
        :order => "batch_id asc,first_name asc") unless query == ''
      @students=@students.uniq
    else
      @students = Student.find(:all,:joins=>'INNER JOIN finance_fees ON finance_fees.student_id = students.id AND finance_fees.balance=0',
        :conditions => ["admission_no = ? " , query],
        :order => "batch_id asc,first_name asc") unless query == ''
    end
    render :layout => false
  end

  def fees_refund_dates
    @student=Student.find(params[:id])
    @dates= FinanceFeeCollection.find(:all,:joins=>"INNER JOIN finance_fees ON finance_fees.fee_collection_id = finance_fee_collections.id AND finance_fees.student_id='#{@student.id}' AND finance_fees.balance = 0")
  end

  def fees_refund_student
    advance_fee_collection = false
    @self_advance_fee = false
    @fee_has_advance_particular = false
        
    @student = Student.find(params[:id])
    if params[:date].present?
      @date = @fee_collection = FinanceFeeCollection.find(params[:date])
      @financefee = @student.finance_fee_by_date(@date)

      if @financefee.has_advance_fee_id
        if @date.is_advance_fee_collection
          @self_advance_fee = true
          advance_fee_collection = true
        end
        @fee_has_advance_particular = true
        @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
        @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
        if @advance_ids.blank?
          @advance_ids[0] = 0
        end
        @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
      end
      
      #if @financefee.advance_fee_id.to_i > 0
        
      @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])

      @paid_fees = @financefee.finance_transactions

      exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
      unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
        exclude_particular_ids = exclude_particular_ids
      else
        exclude_particular_ids = [0]
      end
      
      @refund_amount=0
      if advance_fee_collection
        if @fee_collection_advances.particular_id == 0
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
        else
          @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
        end
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
      end
      @total_discount = 0
      if advance_fee_collection
        @total_payable = (@fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
      else  
        @total_payable = @fee_particulars.map{|s| s.amount}.sum.to_f
      end
      
      if advance_fee_collection
        calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
      else
        if @fee_has_advance_particular
          calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
        else
          calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
        end
      end
      
      @collection=FinanceFeeCollection.find_by_name(@date.name,:conditions=>{:is_deleted=>false})
      @refund_rule=@collection.refund_rules.find(:first,:order=>'refund_validity ASC',:conditions=>["refund_validity >=  '#{Date.today}'"])
      @fee_refund=@financefee.fee_refund
      unless @refund_rule
        #@fee_refund=@financefee.fee_refund
        @refund_rule=@fee_refund.refund_rule if @fee_refund
      end
      
      bal=(@total_payable-@total_discount).to_f
      days=(Date.today-@date.due_date.to_date).to_i
      
      fine_enabled = true
      student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
      unless student_fee_configuration.blank?
        if student_fee_configuration.config_value.to_i == 1
          fine_enabled = true
        else
          fine_enabled = false
        end
      end
      
      if @tmp_paid_fees.blank?
        @tmp_paid_fees = @financefee.finance_transactions
      end
      
      unless @tmp_paid_fees.blank?
        @tmp_paid_fees.each do |paid_fee|
          transaction_id = paid_fee.id
          online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
          unless online_payments.blank?
            fine_enabled = false
          end
        end
      end
      
      auto_fine=@date.fine
      
      @has_fine_discount = false
      if days > 0 and auto_fine and @financefee.is_paid == false and fine_enabled
        @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
        @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
        calculate_extra_fine(@date, @batch, @student, @fine_rule)
        @new_fine_amount = @fine_amount
        get_fine_discount(@date, @batch, @student)
        if @fine_amount < 0
          @fine_amount = 0
        end
      end
      
      @refund_amount=bal*(@refund_rule.refund_percentage.to_f)/100 if @refund_rule
      
      if request.post?
        FeeRefund.transaction do
          transaction = FinanceTransaction.new
          transaction.receipt_no="refund-#{@date.id}-#{@student.id}-#{@refund_rule.id}"
          transaction.title = "#{@refund_rule.name} &#x200E;(#{@student.first_name}) &#x200E;"
          transaction.category = FinanceTransactionCategory.find_by_name("Refund")
          transaction.payee = @student
          transaction.amount = params[:fees][:amount].to_f
          transaction.transaction_date = Date.today
          transaction.description = params[:fees][:reason]
          if transaction.save

            @fee_refund=transaction.build_fee_refund(params[:fees])
            @fee_refund.finance_fee_id=@financefee.id
            @fee_refund.user=current_user
            @fee_refund.refund_rule=@refund_rule
            unless @fee_refund.save
              raise ActiveRecord::Rollback
            end
          end
        end

        render :update do |page|

          page.replace_html "refund", :partial => "fees_refund_form"
        end

      else
        render :update do |page|
          page.replace_html "fee_submission", :partial => "fees_refund_form"
        end
      end
    else
      render :update do |page|
        page.replace_html "fee_submission", :text => ""
      end
    end
  end

  def fee_refund_student_pdf
    advance_fee_collection = false
    @self_advance_fee = false
    @fee_has_advance_particular = false
        
    @student = Student.find(params[:id])
    @date = @fee_collection = FinanceFeeCollection.find(params[:date])
    @financefee = @student.finance_fee_by_date(@date)

    if @financefee.has_advance_fee_id
      if @date.is_advance_fee_collection
        @self_advance_fee = true
        advance_fee_collection = true
      end
      @fee_has_advance_particular = true
      @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
      @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
      if @advance_ids.blank?
        @advance_ids[0] = 0
      end
      @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
    end
    
    #if @financefee.advance_fee_id.to_i > 0
      
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])

    @paid_fees = @financefee.finance_transactions

    exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
    unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
      exclude_particular_ids = exclude_particular_ids
    else
      exclude_particular_ids = [0]
    end
    
    @refund_amount=0
    if advance_fee_collection
      if @fee_collection_advances.particular_id == 0
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id} and finance_fee_particular_category_id = #{@fee_collection_advances.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
      end
    else
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
    end
    @total_discount = 0
    
    if advance_fee_collection
      @total_payable = (@fee_particulars.map{|s| s.amount}.sum * @fee_collection_advances.no_of_month.to_i).to_f
    else  
      @total_payable = @fee_particulars.map{|s| s.amount}.sum.to_f
    end
    
    if advance_fee_collection
      calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
    else
      if @fee_has_advance_particular
        calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
      else
        calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
      end
    end
    
    fee_refund=@financefee.fee_refund
    @refund_amount=fee_refund.amount.to_f
    @refund_percentage=fee_refund.refund_rule.refund_percentage
    render :pdf => 'fee_refund_student_pdf'
  end

  def view_refunds
    @page=0
    @current_user=current_user
    @start_date=Date.today
    @end_date=Date.today
    if @current_user.admin? or @current_user.privileges.collect(&:name).include? "FinanceControl"
      if params[:id]
        @refunds =FeeRefund.paginate(:page => params[:page], :per_page => 10,:joins=>[:finance_fee],:conditions=>["finance_fees.student_id='#{params[:id].to_i}' and fee_refunds.created_at >='#{@start_date}' and fee_refunds.created_at <'#{@end_date+1.day}'"],:order=>'created_at desc')
      else
        @refunds =FeeRefund.paginate(:page => params[:page], :per_page => 10,:conditions=>["created_at >='#{@start_date}' and created_at <'#{@end_date+1.day}'"],:order=>'created_at desc')
      end
    elsif @current_user.parent?
      @refunds =FeeRefund.paginate(:page => params[:page], :per_page => 10,:joins=>[:finance_fee],:conditions=>["finance_fees.student_id='#{@current_user.guardian_entry.ward_id}' and fee_refunds.created_at >='#{Date.today}' and fee_refunds.created_at <'#{Date.today+1.day}'"],:order=>'created_at desc')
    else
      @refunds =FeeRefund.paginate(:page => params[:page], :per_page => 10,:joins=>[:finance_fee],:conditions=>["finance_fees.student_id='#{@current_user.student_entry.id}' and fee_refunds.created_at >='#{Date.today}' and fee_refunds.created_at <'#{Date.today+1.day}'"],:order=>'created_at desc')
    end
  end

  def refund_student_view
    @page=0
    @refunds =FeeRefund.paginate(:page => params[:page], :per_page => 5,:joins=>[:finance_transaction],:conditions=>["finance_transactions.payee_id='#{params[:id].to_i}' and finance_transactions.payee_type='Student'"],:order=>'created_at desc')
  end

  def refund_student_view_pdf
    refund_student_view
    render :pdf => 'refund_student_view_pdf'
  end

  def list_refunds
    @start_date=Date.today
    @end_date=Date.today
    @refunds =FeeRefund.paginate(:page => params[:page], :per_page => 5,:conditions=>["created_at >='#{Date.today}' and created_at <'#{Date.today+1.day}'"],:order=>'created_at desc')
    @page=params[:page]? params[:page].to_i-1 : 0
    render :update do |page|
      page.replace_html 'search_div', :partial=>"finance/view_refunds"
    end
  end

  def refund_filter_by_date
    @start_date=params[:s_date].to_date
    @end_date=params[:e_date].to_date
    @page=params[:page]? params[:page].to_i-1 : 0
    @current_user=current_user
    if @current_user.admin?  or @current_user.privileges.collect(&:name).include? "FinanceControl"
      @refunds = FeeRefund.paginate(:page => params[:page], :per_page => 10,
        :order => 'created_at desc', :conditions => ["created_at >= '#{@start_date}' and created_at < '#{@end_date.to_date+1.day}'"])
    elsif @current_user.parent?
      @refunds = FeeRefund.paginate(:page => params[:page], :per_page => 10,:joins=>[:finance_fee],
        :order => 'created_at desc', :conditions => ["finance_fees.student_id='#{@current_user.guardian_entry.ward_id}' and created_at >= '#{@start_date}' and created_at < '#{@end_date.to_date+1.day}'"])
    else
      @refunds = FeeRefund.paginate(:page => params[:page], :per_page => 10,:joins=>[:finance_fee],
        :order => 'created_at desc', :conditions => ["finance_fees.student_id='#{@current_user.student_entry.id}' and fee_refunds.created_at >= '#{@start_date}' and fee_refunds.created_at < '#{@end_date.to_date+1.day}'"])
    end
    render :update do |page|
      page.replace_html 'search_div', :partial=>"finance/view_refunds_by_date"
    end
  end

  def search_fee_refunds
    @page=params[:page]? params[:page].to_i-1 : 0

    if params[:option]==t('student_name')
      @refunds=FeeRefund.paginate(:page => params[:page], :per_page => 10,:joins=>'INNER JOIN finance_fees on finance_fees.id=fee_refunds.finance_fee_id INNER JOIN students on students.id=finance_fees.student_id',
        :order => 'created_at desc', :conditions => ["students.first_name LIKE ?",
          "#{params[:query]}%"])
    else
      @refunds=FeeRefund.paginate(:page => params[:page], :per_page => 10,:joins=>'INNER JOIN finance_fees on finance_fees.id=fee_refunds.finance_fee_id INNER JOIN finance_fee_collections on finance_fee_collections.id=finance_fees.fee_collection_id',
        :order => 'created_at desc', :conditions => ["finance_fee_collections.name LIKE ?",
          "#{params[:query]}%"])
    end
    render :update do |page|
      page.replace_html 'search_div', :partial=>"finance/view_refunds_by_search"
    end
  end

  def refund_search_pdf

    if params[:option]==t('student_name')
      @refunds=FeeRefund.find(:all,:joins=>'INNER JOIN finance_fees on finance_fees.id=fee_refunds.finance_fee_id INNER JOIN students on students.id=finance_fees.student_id',
        :order => 'created_at desc', :conditions => ["students.first_name LIKE ?",
          "#{params[:query]}%"])
    elsif params[:option]==t('fee_collection_name') or params[:option]=="Fee Collection Name"
      @refunds=FeeRefund.find(:all,:joins=>'INNER JOIN finance_fees on finance_fees.id=fee_refunds.finance_fee_id INNER JOIN finance_fee_collections on finance_fee_collections.id=finance_fees.fee_collection_id',
        :order => 'created_at desc', :conditions => ["finance_fee_collections.name LIKE ?",
          "#{params[:query]}%"])
    else
      if date_format_check
        if (params[:option] or (@start_date and @end_date))
          @refunds = FeeRefund.find(:all,
            :order => 'created_at desc', :conditions => ["created_at >= '#{@start_date}' and created_at < '#{@end_date.to_date+1.day}'"])

        else
          error=true

        end
      end
    end
    if error
      flash[:notice]="#{t('invalid_date_format')}"
      redirect_to :controller => "user", :action => "dashboard"
    else
      render :pdf => 'refund_search_pdf'
    end
  end

  def generate_fine
    @fine=Fine.new
    @fine_rule=FineRule.new
    @fines=Fine.active
  end

  def fine_list
    if params[:id].present?
      @fine=Fine.find(params[:id])
      @fine_rules=@fine.fine_rules.order_in_fine_days
    end
    render :update do |page|
      page.replace_html "fines" ,:partial => "list_fines"
    end
  end

  def fine_slabs_edit_or_create

    if params[:id].present?
      if params[:id]=="0"
        @fine=Fine.new
        render :update do |page|
          page.replace_html "form-errors", :text=>""
          page.replace_html "select_fine", :partial=> "new_fine"
          page.replace_html "flash_box", :text=> ""
        end
      else
        @fine=Fine.find(params[:id])
        render :update do |page|
          page.replace_html "flash_box", :text=> ""
          page.replace_html "form-errors", :text=>""
          page.replace_html "select_fine", :partial=> "list_fine_slabs"
        end
      end
    end

    if request.post?
      if params[:fine_id].nil?
        flash[:notice]="#{t('fine_created_successfully')}"
      else
        flash[:notice]="#{t('fine_slabs_updated')}"
      end
      if  params[:fine][:is_deleted].present?
        flash[:notice]= "#{t('fine_deleted')}"
      end
      fine_id=params[:fine_id]
      @fine=Fine.find_or_initialize_by_id(fine_id)
      if @fine.update_attributes(params[:fine])
        # @fine=Fine.find(params[:fine_id])
        render :update do |page|
          page.redirect_to "generate_fine"
        end
      else
        flash[:notice]=nil
        render :update do |page|
          page.replace_html "form-errors", :partial=>"errors",:object=>@fine
          unless fine_id.present?
            page.replace_html "select_fine", :partial=> "fine_errors"
          else
            page.replace_html "select_fine", :partial=> "list_fine_slabs"
          end
        end
      end
    end
  end

  def regenerate_student_fees
    fee_collection_id = 0
    fee_id = 0
    student = params[:student_id]
    if params[:option].blank?
      finance =  FinanceFee.find_by_id(params[:fee_id])
      unless finance.blank?
        fee_collection_id =  finance.fee_collection_id
        fee_id =  finance.id
        date_id = finance.fee_collection_id
        student_id = finance.student_id
        date = FinanceFeeCollection.find(date_id)
        student = Student.find(student_id)
        finance.current_user_id = current_user.id
        if finance.destroy
          discounts_on_particulars=date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{student.batch_id} and is_onetime=#{true} and is_late=#{false}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          discounts_ids = discounts_on_particulars.map(&:id)
          discounts_ids.each do |did|
            collection_discount = CollectionDiscount.find_by_finance_fee_collection_id_and_fee_discount_id(date_id, did)
            collection_discount.destroy
          end
          tmp_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{student.batch_id} and is_tmp=#{true}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          tmp_particulars = tmp_particulars.map(&:id)
          tmp_particulars.each do |tp|
            collection_particular = CollectionParticular.find_by_finance_fee_collection_id_and_finance_fee_particular_id(date_id, tp)
            collection_particular.destroy
          end
          unless fee_collection_id == 0
            fee_collection = FinanceFeeCollection.find(fee_collection_id)
            student = Student.find_by_id(params[:student_id])
            FinanceFee.new_student_fee(fee_collection,student)
            #FinanceFee.new_student_fee_with_tmp_particular(fee_collection,student)
            fee = FinanceFee.find_by_student_id_and_fee_collection_id(student.id, fee_collection_id)
            @fee_data = fee
            balance = FinanceFee.get_student_balance(fee_collection,student,fee)

            bal = (render_to_string :text => "#{balance}")
            html = (render_to_string :partial => "action_collection_details_view")

            render :update do |page|
              page << "j('#student_amount_#{student.id}_#{fee_id}').html('#{bal}');"
              page << 'j("#action_tr_' + student.id.to_s + '_' + fee_id.to_s + '").html("' + escape_javascript(html) + '");'
              page << "j('#student_amount_#{student.id}_#{fee_id}').attr('id','student_amount_#{student.id}_#{fee.id}')"
              page << "j('#student_fee_#{fee_id}_#{student.id}').attr('id','student_fee_#{fee.id}_#{student.id}')"
              page << "j('#action_tr_#{student.id}_#{fee_id}').attr('id','action_tr_#{student.id}_#{fee.id}')"
              page << "update_total();"
              page << "update_amount_to_string(#{student.id}, #{fee.id});"
            end
          else
            render :update do |page|
              page << 'j("#student_fee_' + finance.id.to_s + '_' + student_id.to_s + '").after("<tr><td id=\'error_student_' + finance.id.to_s + '_' + student_id.to_s + '\' colspan=\'6\' style=\'text-align: center; border-collapse: separate !important; background: #f9e79f;font-family: verdana; font-size: 12px;font-weight: bold;\'>Finance Fee Can\'t be regenerated, Please try again later</td></tr>");'
              page << 'setTimeout(function(){ j("#error_student_' + finance.id.to_s + '_' + student_id.to_s + '").remove(); }, 3000);';
              #page << "searchAjax(1);"
            end
          end
        else
          render :update do |page|
            page << 'j("#student_fee_' + finance.id.to_s + '_' + student_id.to_s + '").after("<tr><td id=\'error_student_' + finance.id.to_s + '_' + student_id.to_s + '\' colspan=\'6\' style=\'text-align: center; border-collapse: separate !important; background: #f9e79f;font-family: verdana; font-size: 12px;font-weight: bold;\'>Finance Fee Can\'t be regenerated, Transaction exists for this fee, please remove the transaction and try again</td></tr>");'
            page << 'setTimeout(function(){ j("#error_student_' + finance.id.to_s + '_' + student_id.to_s + '").remove(); }, 3000);';
            #page << "searchAjax(1);"
          end
        end
      end
    else
      fee_collection_id = params[:fee_id]
      student_ids = params[:student_ids]
      student_infos = []
      if params[:option]
        if params[:option].to_i == 1
          student_ids.each do |v|
            student = Student.find_by_id(v.to_i)
            fee = FinanceFee.find_by_student_id_and_fee_collection_id(student.id, fee_collection_id)
            unless fee.blank?
              fee.current_user_id = current_user.id
              fee_id =  fee.id
              date_id = fee.fee_collection_id
              student_id = fee.student_id
              date = FinanceFeeCollection.find(date_id)
              if fee.destroy
                discounts_on_particulars=date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{student.batch_id} and is_onetime=#{true} and is_late=#{false}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                discounts_ids = discounts_on_particulars.map(&:id)
                discounts_ids.each do |did|
                  collection_discount = CollectionDiscount.find_by_finance_fee_collection_id_and_fee_discount_id(date_id, did)
                  collection_discount.destroy
                end
                tmp_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{student.batch_id} and is_tmp=#{true}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                tmp_particulars = tmp_particulars.map(&:id)
                tmp_particulars.each do |tp|
                  collection_particular = CollectionParticular.find_by_finance_fee_collection_id_and_finance_fee_particular_id(date_id, tp)
                  collection_particular.destroy
                end
                fee_collection = FinanceFeeCollection.find(fee_collection_id)
                FinanceFee.new_student_fee(fee_collection,student)
                student_infos[student_id] = 1
              else
                student_infos[student_id] = 0
              end
            else
              student_id = student.student_id
              fee_collection = FinanceFeeCollection.find(fee_collection_id)
              FinanceFee.new_student_fee(fee_collection,student)
              student_infos[student_id] = 1
            end
          end
          has_error = student_infos.select{|s| s == 0 }
          s_data = "Some Student Fees can't be regenerated as transaction exists for those fees, \n\nplease remove the transaction and try again"

          s_data = "Regenerated fees successful." if has_error.blank?
          render :update do |page|
            page << 'alert("' + escape_javascript(s_data) + '")'
            page << "searchAjax(1);"
          end
        elsif params[:option].to_i == 2
          student_ids.each do |v|
            student = Student.find_by_id(v.to_i)
            fee_collection = FinanceFeeCollection.find(fee_collection_id)
            FinanceFee.new_student_fee(fee_collection,student)
          end
          s_data = "Regenerated fees successful." 
          render :update do |page|
            page << 'alert("' + escape_javascript(s_data) + '")'
            page << "j('.show_inactive_fa').trigger('click');"
          end
        end
      end
    end

  end

  def delete_student_fees
    finance =  FinanceFee.find_by_id(params[:fee_id])
    unless finance.blank?
     finance.current_user_id = current_user.id
#    paid_fees = finance.finance_transactions
#    if paid_fees.blank?
      date_id = finance.fee_collection_id
      student_id = finance.student_id
      date = FinanceFeeCollection.find(date_id)
      student = Student.find(student_id)
      discounts_on_particulars=date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{student.batch_id} and is_onetime=#{true} and is_late=#{false}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      discounts_ids = discounts_on_particulars.map(&:id)
      discounts_ids.each do |did|
        collection_discount = CollectionDiscount.find_by_finance_fee_collection_id_and_fee_discount_id(date_id, did)
        collection_discount.destroy
      end
      if finance.destroy
        render :update do |page|
          page << 'j("#student_fee_' + finance.id.to_s + '_' + student_id.to_s + ' td").css("background-color", "#FFADAD");'
          page << 'j("#action_tr_' + student_id.to_s + '_' + finance.id.to_s + '").html("");'
          page << 'setTimeout(function(){ j("#student_fee_' + finance.id.to_s + '_' + student_id.to_s + '").remove(); resetSN(); resetDiv(); update_total();}, 3000);';
          #page << "searchAjax(1);"
        end
      else
        render :update do |page|
          page << 'j("#student_fee_' + finance.id.to_s + '_' + student_id.to_s + '").after("<tr><td id=\'error_student_' + finance.id.to_s + '_' + student_id.to_s + '\' colspan=\'6\' style=\'text-align: center; border-collapse: separate !important; background: #f9e79f;font-family: verdana; font-size: 12px;font-weight: bold;\'>Student Fee can\'t be deleted, Finance Transaction exists for this fee</td></tr>");'
          page << 'setTimeout(function(){ j("#error_student_' + finance.id.to_s + '_' + student_id.to_s + '").remove(); }, 3000);';
        end
      end
    else
      render :update do |page|
        page << 'alert("Student Fee can\'t be deleted, An error occur please try again later");'
      end
    end
  end

  def delete_student_fees_from_collection
    finance =  FinanceFee.find_by_id(params[:fee_id])
    date_id = finance.fee_collection_id
    student_id = finance.student_id
    date = FinanceFeeCollection.find(date_id)
    student = Student.find(student_id)
    discounts_on_particulars=date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{student.batch_id} and is_onetime=#{true} and is_late=#{false}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
    discounts_ids = discounts_on_particulars.map(&:id)
    discounts_ids.each do |did|
      collection_discount = CollectionDiscount.find_by_finance_fee_collection_id_and_fee_discount_id(date_id, did)
      collection_discount.destroy
    end
    finance.current_user_id = current_user.id
    finance.destroy
    load_fees_submission_batch
  end

  def generate_student_fees
    fee_collection = FinanceFeeCollection.find(params[:fee_id])
    student = Student.find_by_id(params[:student_id])
    finance_fee = FinanceFee.find(:first, :conditions => "student_id = #{student.id} and fee_collection_id = #{params[:fee_id]} and batch_id = #{student.batch.id}")
    if finance_fee.blank?
      FinanceFee.new_student_fee(fee_collection,student)
      for_admission = false
      if fee_collection.for_admission.type.to_s == "FalseClass" or fee_collection.for_admission.type.to_s == "TrueClass"
        if fee_collection.for_admission
          for_admission = true
        end
      else
        if fee_collection.for_admission.to_i == 1
          for_admission = true
        end
      end
      if for_admission
        @finance_fees = FinanceFee.all(:select=>"finance_fees.id,finance_fees.student_id,finance_fees.is_paid,finance_fees.balance",:joins=>"INNER JOIN students ON students.id = finance_fees.student_id",:order => "if(students.class_roll_no = '' or students.class_roll_no is null,0,cast(students.class_roll_no as unsigned)),students.first_name ASC", :conditions=>"finance_fees.fee_collection_id = #{params[:fee_id]}")
        render :update do |page|
          page.replace_html "particulars", :partial => "collection_details_view_admission"
        end
#      else
#        render :update do |page|
#          page.replace_html "particulars", :partial => "collection_details_view_admission"
#        end
      end
    else
      for_admission = false
      if fee_collection.for_admission.type.to_s == "FalseClass" or fee_collection.for_admission.type.to_s == "TrueClass"
        if fee_collection.for_admission
          for_admission = true
        end
      else
        if fee_collection.for_admission.to_i == 1
          for_admission = true
        end
      end
      if for_admission
        @finance_fees = FinanceFee.all(:select=>"finance_fees.id,finance_fees.student_id,finance_fees.is_paid,finance_fees.balance",:joins=>"INNER JOIN students ON students.id = finance_fees.student_id",:order => "if(students.class_roll_no = '' or students.class_roll_no is null,0,cast(students.class_roll_no as unsigned)),students.first_name ASC", :conditions=>"finance_fees.fee_collection_id = #{params[:fee_id]}")
        render :update do |page|
          page << "alert('Student already assign to this fee')"
          page.replace_html "particulars", :partial => "collection_details_view_admission"
        end
      end
    end
  end

  def generate_student_fees_from_profile
    fee_collection = FinanceFeeCollection.find(params[:fee_id])
    student = Student.find_by_id(params[:student_id])
    finance_fee = FinanceFee.find(:first, :conditions => "student_id = #{student.id} and fee_collection_id = #{params[:fee_id]} and batch_id = #{student.batch.id}")
    if finance_fee.blank?
      FinanceFee.new_student_fee(fee_collection,student)
      @student = student
      @dates=FinanceFeeCollection.find(:all,:joins=>"INNER JOIN fee_collection_batches on fee_collection_batches.finance_fee_collection_id=finance_fee_collections.id INNER JOIN finance_fees on finance_fees.fee_collection_id=finance_fee_collections.id",:conditions=>"finance_fees.student_id='#{student.id}'  and finance_fee_collections.is_deleted=#{false} and ((finance_fees.balance > 0 and finance_fees.batch_id<>#{student.batch_id}) or (finance_fees.batch_id=#{student.batch_id}) )").uniq
    
      @dates_all = FinanceFeeCollection.find(:all,:joins=>"INNER JOIN fee_collection_batches on fee_collection_batches.finance_fee_collection_id=finance_fee_collections.id INNER JOIN finance_fees on finance_fees.fee_collection_id=finance_fee_collections.id",:conditions=>"finance_fees.student_id='#{student.id}'  and finance_fee_collections.is_deleted=#{false} ", :order=>'finance_fee_collections.due_date DESC').uniq # and finance_fees.batch_id = #{@student.batch_id}
      @dates_admission = student.batch.finance_fee_collections.find(:all,:conditions=>"finance_fee_collections.is_deleted=#{false} and finance_fee_collections.for_admission = #{true}", :order=>'finance_fee_collections.due_date DESC').uniq # and finance_fees.batch_id = #{@student.batch_id}
      render :update do |page|
        page.replace_html "fee_div", :partial => "student_fee_profile"
      end
    else
      @dates=FinanceFeeCollection.find(:all,:joins=>"INNER JOIN fee_collection_batches on fee_collection_batches.finance_fee_collection_id=finance_fee_collections.id INNER JOIN finance_fees on finance_fees.fee_collection_id=finance_fee_collections.id",:conditions=>"finance_fees.student_id='#{student.id}'  and finance_fee_collections.is_deleted=#{false} and ((finance_fees.balance > 0 and finance_fees.batch_id<>#{student.batch_id}) or (finance_fees.batch_id=#{student.batch_id}) )").uniq
      @student = student
      
      @dates_all = FinanceFeeCollection.find(:all,:joins=>"INNER JOIN fee_collection_batches on fee_collection_batches.finance_fee_collection_id=finance_fee_collections.id INNER JOIN finance_fees on finance_fees.fee_collection_id=finance_fee_collections.id",:conditions=>"finance_fees.student_id='#{student.id}'  and finance_fee_collections.is_deleted=#{false} ", :order=>'finance_fee_collections.due_date DESC').uniq # and finance_fees.batch_id = #{@student.batch_id}
      @dates_admission = student.batch.finance_fee_collections.find(:all,:conditions=>"finance_fee_collections.is_deleted=#{false} and finance_fee_collections.for_admission = #{true}", :order=>'finance_fee_collections.due_date DESC').uniq # and finance_fees.batch_id = #{@student.batch_id}
      render :update do |page|
        page.replace_html "fee_div", :partial => "student_fee_profile"
      end
    end
  end
  
  #Student Scholarship start
  
  def student_scholarship
    @courses = Course.active
    fee_cateroies = FinanceFeeParticularCategory.active
    @particular = fee_cateroies.map{|p| [p.name, p.id]}
  end
  
  def view_scholarships
    @scholarships = FeeDiscount.find(:all, :conditions => ["finance_fee_category_id = 0"])
    @scholarships_student_ids = []
    unless @scholarships.blank?
      @finance_fee_category = FinanceFeeParticularCategory.find(:first,:conditions => ["is_deleted = ? and name = 'Tuition Fees'", false])
      unless @finance_fee_category.blank?
        category_id = @finance_fee_category.id
        @scholarships.each do |s|
          unless s.receiver.blank?
            student = s.receiver
            fee_particulars = FinanceFeeParticular.all(:conditions=>"finance_fee_category_id = 0 and is_deleted=#{false} and batch_id=#{student.batch.id} and finance_fee_particular_category_id = #{category_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt=fee_particulars.map{|st| st.amount}.sum.to_f
            discount_amt = payable_ampt * s.discount.to_f/ (s.is_amount?? payable_ampt : 100)
            @scholarships_student_ids[student.id] = discount_amt
          end
        end
      else
        @scholarships.each do |s|
          unless s.receiver.blank?
            @scholarships_student_ids[s.receiver.id] = 0
          end
        end
      end
    end
  end
  
  def download_pdf_scholarships
    @scholarships = FeeDiscount.find(:all, :conditions => ["finance_fee_category_id = 0"])
    
    @scholarships_student_ids = []
    unless @scholarships.blank?
      @finance_fee_category = FinanceFeeParticularCategory.find(:first,:conditions => ["is_deleted = ? and name = 'Tuition Fees'", false])
      unless @finance_fee_category.blank?
        category_id = @finance_fee_category.id
        @scholarships.each do |s|
          unless s.receiver.blank?
            student = s.receiver
            fee_particulars = FinanceFeeParticular.all(:conditions=>"finance_fee_category_id = 0 and is_deleted=#{false} and batch_id=#{student.batch.id} and finance_fee_particular_category_id = #{category_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt=fee_particulars.map{|st| st.amount}.sum.to_f
            discount_amt = payable_ampt * s.discount.to_f/ (s.is_amount?? payable_ampt : 100)
            @scholarships_student_ids[student.id] = discount_amt
          end
        end
      else
        @scholarships.each do |s|
          unless s.receiver.blank?
            @scholarships_student_ids[s.receiver.id] = 0
          end
        end
      end
    end
    
    render :pdf => "download_pdf_scholarships",
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {:top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  
  def download_excel_scholarships    
    require 'spreadsheet'
    Spreadsheet.client_encoding = 'UTF-8'
    new_book = Spreadsheet::Workbook.new
    amount_format = Spreadsheet::Format.new({
      :size             => 12,
      :number_format    => "0.00"
    });
    
    sheet1 = new_book.create_worksheet :name => 'student_scholarships'
    
    row_1 = ['#','Student ID','Student Name','Class, Section','Scholarship Name','Scholarship On','Discount','Amount']
    new_book.worksheet(0).insert_row(0, row_1)
    @scholarships = FeeDiscount.find(:all, :conditions => ["finance_fee_category_id = 0"])
    
    @scholarships_student_ids = []
    unless @scholarships.blank?
      @finance_fee_category = FinanceFeeParticularCategory.find(:first,:conditions => ["is_deleted = ? and name = 'Tuition Fees'", false])
      unless @finance_fee_category.blank?
        category_id = @finance_fee_category.id
        @scholarships.each do |s|
          unless s.receiver.blank?
            student = s.receiver
            fee_particulars = FinanceFeeParticular.all(:conditions=>"finance_fee_category_id = 0 and is_deleted=#{false} and batch_id=#{student.batch.id} and finance_fee_particular_category_id = #{category_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt=fee_particulars.map{|st| st.amount}.sum.to_f
            discount_amt = payable_ampt * s.discount.to_f/ (s.is_amount?? payable_ampt : 100)
            @scholarships_student_ids[student.id] = discount_amt
          end
        end
      else
        @scholarships.each do |s|
          unless s.receiver.blank?
            @scholarships_student_ids[s.receiver.id] = 0
          end
        end
      end
    end
    
    row_loop = 1
    sl = 1
    @scholarships.each do |sc|
      unless sc.receiver.blank?
        batch = sc.receiver.batch.nil? ? '' : sc.receiver.batch.course_section
        type = sc.is_amount == true ? 'BDT'  : '%'
        discount = sc.discount.to_s+type
        data_row = [sl,sc.receiver.admission_no, sc.receiver.full_name, batch, sc.name, sc.finance_fee_particular_category.name, discount,@scholarships_student_ids[sc.receiver.id]]
        new_book.worksheet(0).insert_row(row_loop, data_row)
        new_book.worksheet(0).row(row_loop).set_format(6, amount_format)
        new_book.worksheet(0).row(row_loop).set_format(7, amount_format)
        row_loop+=1
        sl+=1
      end
    end
    
    sheet1.add_header(Configuration.get_config_value('InstitutionName'))
    spreadsheet = StringIO.new 
    new_book.write spreadsheet 
    send_data spreadsheet.string, :filename => "Student_scholarship.xls", :type =>  "application/vnd.ms-excel"
  end
  
  def update_scholarship
     unless params[:id].nil?
        @fee_discount = FeeDiscount.find_by_id(params[:id].to_i)
        unless params[:amount].blank?
        @fee_discount.discount = params[:amount]
        end
        unless params[:type].nil?
        @fee_discount.is_amount = params[:type]
        end
        @fee_discount.save
        
        render :update do |page|
          page.replace_html "editID-"+ @fee_discount.id.to_s ,:partial => "scholarship_amount"
        end
     end
  end
  
  def delete_scholarship
     unless params[:id].nil?
        @fee_discount = FeeDiscount.find_by_id(params[:id].to_i)
        @fee_discount.destroy
        
        render :update do |page|
          page.replace_html "deleteID-"+ params[:id] ,""
        end
     end
  end
  
  def load_scholarship_students
    if params[:query].length>= 3
        params[:query].gsub! '+', ' '
        @students = Student.active.find(:all,
          :conditions => ["first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                            OR admission_no LIKE ? OR (concat(first_name, \" \", last_name) LIKE ? ) OR (concat(first_name, \"+\", last_name) LIKE ? ) ",
            "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
            "%#{params[:query]}%", "%#{params[:query]}%", "%#{params[:query]}%" ],
          :order => "batch_id asc,first_name asc",:include =>  [{:batch=>:course}]) unless params[:query] == ''
      else
        @students = Student.active.find(:all,
          :conditions => ["admission_no = ? " , params[:query]],
          :order => "batch_id asc,first_name asc",:include =>  [{:batch=>:course}]) unless params[:query] == ''
      end   
    render :update do |page|
      page.replace_html "student-search-results" ,:partial => "scholarship_student_list"
    end
  end
  
  def create_student_scholarship
    unless (params[:fee_discount][:students]).blank?

        student_ids = params[:fee_discount][:students]
        uniqstds = student_ids.uniq
 
        @scholarship = Scholarship.new
        @scholarship.name = params[:fee_discount][:name]
        @scholarship.discount_particular_id = params[:fee_discount][:discount_on]
        @scholarship.is_amount = params[:fee_discount][:is_amount]
        @scholarship.amount = params[:fee_discount][:discount]
        @scholarship.save

        uniqstds.each do |si|
          s = Student.find(si)
          @fee_discount = FeeDiscount.new
          @fee_discount.name = params[:fee_discount][:name]
          @fee_discount.discount = params[:fee_discount][:discount]
          @fee_discount.receiver_type ="Student"
          @fee_discount.receiver_id = s.id
          @fee_discount.scholarship_id = @scholarship.id
          @fee_discount.batch_id = s.batch_id
          @fee_discount.finance_fee_category_id = 0
          @fee_discount.finance_fee_particular_category_id = params[:fee_discount][:discount_on]
          @fee_discount.is_amount = params[:fee_discount][:is_amount]
          unless @fee_discount.save
            @error = true
          end
        end
        flash[:notice] = "Scholarship Created Successfully"
     else
         flash[:notice] = "No Student Selected"
     end
    redirect_to :action => 'student_scholarship' 
  end
  #Student Scholarship end
  
  private

  def date_format(date)
    /(\d{4}-\d{2}-\d{2})/.match(date)
  end

  def get_fees_details(student_ids)
    @show_only_structure = false
    advance_fee_collection = false
    @self_advance_fee = false
    @fee_has_advance_particular = false

    @student ||= @fee.student
    @prev_student = @student.previous_fee_student(@date.id,student_ids)
    @next_student = @student.next_fee_student(@date.id,student_ids)
    @financefee = @student.finance_fee_by_date @date
    if @financefee.has_advance_fee_id
      if @date.is_advance_fee_collection
        @self_advance_fee = true
        advance_fee_collection = true
      end
      @fee_has_advance_particular = true
      @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
      @advance_ids = @advance_ids.reject { |a| a.to_s.empty? }
      if @advance_ids.blank?
        @advance_ids[0] = 0
      end
      @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
    end
    @due_date = @fee_collection.due_date
    @paid_fees = @fee.finance_transactions
    #abort(@paid_fees.map(&:id).inspect)
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])

    exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
    unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
      exclude_particular_ids = exclude_particular_ids
    else
      exclude_particular_ids = [0]
    end
      
    if advance_fee_collection
      fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
      if fee_collection_advances_particular.include?(0)
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      else
        fee_collection_advances_particular = fee_collection_advances_particular.reject { |a| a.to_s.empty? }
        if fee_collection_advances_particular.blank?
          fee_collection_advances_particular[0] = 0
        end
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      end
    else
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
    end

    if advance_fee_collection
      month = 1
      payable = 0
      @fee_collection_advances.each do |fee_collection_advance|
        @fee_particulars.each do |particular|
          if fee_collection_advance.particular_id == particular.finance_fee_particular_category_id
            payable += particular.amount * fee_collection_advance.no_of_month.to_i
          else
            payable += particular.amount
          end
        end
      end
      @total_payable=payable.to_f
    else  
      @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
    end

    @total_discount = 0

    @adv_fee_discount = false
    @actual_discount = 1

    if advance_fee_collection
      calculate_discount(@date, @financefee.batch, @student, true, @fee_collection_advances, @fee_has_advance_particular)
    else
      if @fee_has_advance_particular
        calculate_discount(@date, @financefee.batch, @student, false, @fee_collection_advances, @fee_has_advance_particular)
      else
        calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
      end
    end

    bal=(@total_payable-@total_discount).to_f
    unless params[:submission_date].nil? or params[:submission_date].empty? or params[:submission_date].blank?
      require 'date'
      @submission_date = Date.parse(params[:submission_date])
      days=(Date.parse(params[:submission_date])-@date.due_date.to_date).to_i
    else
      @submission_date = Date.today
      if @financefee.is_paid
        @paid_fees = @financefee.finance_transactions
        days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
      else
        days=(Date.today-@date.due_date.to_date).to_i
      end
    end

    fine_enabled = true
    student_fee_configuration = StudentFeeConfiguration.find(:first, :conditions => "student_id = #{@student.id} and date_id = #{@date.id} and config_key = 'fine_payment_student'")
    unless student_fee_configuration.blank?
      if student_fee_configuration.config_value.to_i == 1
        fine_enabled = true
      else
        fine_enabled = false
      end
    end
    
    if @tmp_paid_fees.blank?
      @tmp_paid_fees = @financefee.finance_transactions
    end
    
    unless @tmp_paid_fees.blank?
      @tmp_paid_fees.each do |paid_fee|
        transaction_id = paid_fee.id
        online_payments = Payment.find_by_finance_transaction_id_and_payee_id(transaction_id, @student.id)
        unless online_payments.blank?
          fine_enabled = false
        end
      end
    end
    
    auto_fine=@date.fine

    @has_fine_discount = false
    if days > 0 and auto_fine and fine_enabled #and @financefee.is_paid == false
      @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
      @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule

      calculate_extra_fine(@date, @batch, @student, @fine_rule)

      @new_fine_amount = @fine_amount
      get_fine_discount(@date, @batch, @student)
      if @fine_amount < 0
        @fine_amount = 0
      end
    end

    @fine_amount=0 if @financefee.is_paid

    unless advance_fee_collection
      if @total_discount == 0
        @adv_fee_discount = true
        @actual_discount = 0
        calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
      end
    end
  end


end

