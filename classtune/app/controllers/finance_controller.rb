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
  helper_method :calculate_discount
  helper_method :calculate_extra_fine
  helper_method :get_fine_discount
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
    if date_format_check
      unless @start_date > @end_date
        @fin_start_date = Configuration.find_by_config_key('FinancialYearStartDate').config_value
        @fin_end_date = Configuration.find_by_config_key('FinancialYearEndDate').config_value
        @finance_fee_category = FinanceFeeParticularCategory.find(:all,:conditions => ["is_deleted = ?", false])
        @all_fees_extra_particulers = []
        @all_fees_extra_particulers << "Discount"
        @all_fees_extra_particulers << "Fine"
        @all_fees_extra_particulers << "VAT"
        @transactions = FinanceTransaction.find(:all, :order => 'finance_transactions.transaction_date desc', :conditions => ["finance_transactions.transaction_date >= '#{@start_date}' and finance_transactions.transaction_date <= '#{@end_date}'"])
        @transactions_particular = FinanceTransactionParticular.find(:all, :order => 'finance_transactions.transaction_date desc', :conditions => ["finance_transactions.transaction_date >= '#{@start_date}' and finance_transactions.transaction_date <= '#{@end_date}'"],:include=>"finance_transaction")
      end
    end
  end
  
  
  def transaction_pdf_fees_csv
    fixed_category_name
    if date_format_check
      unless @start_date > @end_date
        
        @fin_start_date = Configuration.find_by_config_key('FinancialYearStartDate').config_value
        @fin_end_date = Configuration.find_by_config_key('FinancialYearEndDate').config_value
        
        @finance_fee_category = FinanceFeeParticularCategory.find(:all,:conditions => ["is_deleted = ?", false])
        @all_fees_extra_particulers = []
        @all_fees_extra_particulers << "Discount"
        @all_fees_extra_particulers << "Fine"
        @all_fees_extra_particulers << "VAT"
        @transactions_particular = FinanceTransactionParticular.find(:all, :order => 'transaction_date desc', :conditions => ["transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'"])
  
    
      end
    end
    
   
    csv = FasterCSV.generate do |csv|
      cols = []
      cols << "Month"
      @finance_fee_category.each do |fees_particuler|
        cols << fees_particuler.name
      end
      @all_fees_extra_particulers.each do |fees_extra_particuler|
        cols << fees_extra_particuler
      end
      cols << "Total Collection"
      csv << cols
      fee_total = {}
      grand_total = 0.0
      (@start_date.to_date..@end_date.to_date).each do |day|
        total_fees = 0.0
        cols = []
        cols << I18n.l(day.to_date,:format=>"%d %b %Y")
        i = 0
        @finance_fee_category.each do |fees_particuler|
          fee_amount = 0.0
          
          unless @transactions_particular.blank?
            @transactions_particular.each do |transactions_particular|
              if transactions_particular.transaction_date.to_date == day.to_date and transactions_particular.particular_id == fees_particuler.id and transactions_particular.particular_type = "ParticularCategory"
                fee_amount+=transactions_particular.amount  
              end
            end
          end
          total_fees = total_fees.to_f+fee_amount.to_f
          if fee_amount!=0
            cols << fee_amount.to_s
          else
            cols << "-"
          end  
          if fee_total[i].blank?
            fee_total[i] = 0
          end
          fee_total[i] = fee_total[i]+fee_amount 
          grand_total = grand_total.to_f+fee_amount.to_f
          i = i+1
        end
        
        discount_amount = 0
        unless @transactions_particular.blank?
          @transactions_particular.each do |transactions_particular|
            if transactions_particular.transaction_date.to_date == day.to_date and (transactions_particular.particular_type == "Discount" or transactions_particular.particular_type == "OnetimeDiscount" or transactions_particular.particular_type == "Fine Discount")
              discount_amount+=transactions_particular.amount
            end
          end
        end
        
        if discount_amount!=0
          cols << "["+discount_amount.to_s+"]"
        else
          cols << "-"
        end 
        
        if fee_total[i].blank?
           fee_total[i] = 0
        end
        fee_total[i] += discount_amount
        i = i+1
        
        fine_amount = 0
        unless @transactions_particular.blank?
          @transactions_particular.each do |transactions_particular|
            if transactions_particular.transaction_date.to_date == day.to_date and transactions_particular.particular_type == "Fine" 
              fine_amount+=transactions_particular.amount
            end
          end
        end
        
        if fine_amount!=0
          cols << fine_amount.to_s
        else
          cols << "-"
        end 
        
        if fee_total[i].blank?
           fee_total[i] = 0
        end
        fee_total[i] += fine_amount
        total_fees += fine_amount
        grand_total += fine_amount
        i = i+1
        
        vat_amount = 0
        unless @transactions_particular.blank?
          @transactions_particular.each do |transactions_particular|
            if transactions_particular.transaction_date.to_date == day.to_date and transactions_particular.particular_type == "Vat"
              vat_amount+=transactions_particular.amount
            end
          end
        end
        
        if vat_amount!=0
          cols << vat_amount.to_s
        else
          cols << "-"
        end 
        
        if fee_total[i].blank?
           fee_total[i] = 0
        end
        fee_total[i] += vat_amount
        total_fees += vat_amount
        grand_total += vat_amount
        i = i+1
        
        
        if total_fees!=0
            cols << total_fees.to_s
        else
          cols << "-"
        end
        
        csv << cols
      end
      cols = []
      cols << "Total Collection"
      i = 0
      @finance_fee_category.each do |fees_particuler|
        if fee_total[i].blank? or fee_total[i] == 0
            cols << "-"
        else
          cols << fee_total[i].to_s
        end
        i = i+1
      end
      
      @all_fees_extra_particulers.each do |fees_particuler|
        if fee_total[i].blank? or fee_total[i] == 0
            cols << "-"
        else
          cols << fee_total[i].to_s
        end
        i = i+1
      end
      
      if grand_total!=0
        cols << grand_total.to_s
      else
        cols << "-"
      end
      csv << cols
    end
    filename = "monthly-report-#{Time.now.to_date.to_s}.csv"
    send_data(csv, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
  end
  
  def transaction_pdf_fees_month_csv
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
    
    
    csv = FasterCSV.generate do |csv|
      cols = []
      cols << "Month"
      @finance_fee_category.each do |fees_particuler|
        cols << fees_particuler.name
      end
      @all_fees_extra_particulers.each do |fees_extra_particuler|
        cols << fees_extra_particuler
      end
      cols << "Total Collection"
      csv << cols
      fee_total = {}
      grand_total = 0.0
      @dates.each do |day|
        total_fees = 0.0
        cols = []
        cols << day.last
        i = 0
        @finance_fee_category.each do |fees_particuler|
          fee_amount = 0.0
          
          unless @transactions_particular.blank?
            @transactions_particular.each do |transactions_particular|
              if transactions_particular.transaction_date.to_date.beginning_of_month.strftime("%b-%y") == day.first.strftime("%b-%y") and transactions_particular.particular_id == fees_particuler.id and transactions_particular.particular_type = "ParticularCategory"
                fee_amount+=transactions_particular.amount  
              end
            end
          end
          total_fees = total_fees.to_f+fee_amount.to_f
          if fee_amount!=0
            cols << fee_amount.to_s
          else
            cols << "-"
          end  
          if fee_total[i].blank?
            fee_total[i] = 0
          end
          fee_total[i] = fee_total[i]+fee_amount 
          grand_total = grand_total.to_f+fee_amount.to_f
          i = i+1
        end
        
        discount_amount = 0
        unless @transactions_particular.blank?
          @transactions_particular.each do |transactions_particular|
            if transactions_particular.transaction_date.to_date.beginning_of_month.strftime("%b-%y") == day.first.strftime("%b-%y") and (transactions_particular.particular_type == "Discount" or transactions_particular.particular_type == "OnetimeDiscount" or transactions_particular.particular_type == "Fine Discount")
              discount_amount+=transactions_particular.amount
            end
          end
        end
        
        if discount_amount!=0
          cols << "["+discount_amount.to_s+"]"
        else
          cols << "-"
        end 
        
        if fee_total[i].blank?
           fee_total[i] = 0
        end
        fee_total[i] += discount_amount
        i = i+1
        
        fine_amount = 0
        unless @transactions_particular.blank?
          @transactions_particular.each do |transactions_particular|
            if transactions_particular.transaction_date.to_date.beginning_of_month.strftime("%b-%y") == day.first.strftime("%b-%y") and transactions_particular.particular_type == "Fine" 
              fine_amount+=transactions_particular.amount
            end
          end
        end
        
        if fine_amount!=0
          cols << fine_amount.to_s
        else
          cols << "-"
        end 
        
        if fee_total[i].blank?
           fee_total[i] = 0
        end
        fee_total[i] += fine_amount
        total_fees += fine_amount
        grand_total += fine_amount
        i = i+1
        
        vat_amount = 0
        unless @transactions_particular.blank?
          @transactions_particular.each do |transactions_particular|
            if transactions_particular.transaction_date.to_date.beginning_of_month.strftime("%b-%y") == day.first.strftime("%b-%y") and transactions_particular.particular_type == "Vat"
              vat_amount+=transactions_particular.amount
            end
          end
        end
        
        if vat_amount != 0
          cols << vat_amount.to_s
        else
          cols << "-"
        end 
        
        if fee_total[i].blank?
           fee_total[i] = 0
        end
        fee_total[i] += vat_amount
        total_fees += vat_amount
        grand_total += vat_amount
        i = i+1
        
        
        if total_fees!=0
            cols << total_fees.to_s
        else
          cols << "-"
        end
        
        csv << cols
      end
      cols = []
      cols << "Total Collection"
      i = 0
      @finance_fee_category.each do |fees_particuler|
        if fee_total[i].blank? or fee_total[i] == 0
            cols << "-"
        else
          cols << fee_total[i].to_s
        end
        i = i+1
      end
      
      @all_fees_extra_particulers.each do |fees_particuler|
        if fee_total[i].blank? or fee_total[i] == 0
            cols << "-"
        else
          cols << fee_total[i].to_s
        end
        i = i+1
      end
      
      if grand_total!=0
        cols << grand_total.to_s
      else
        cols << "-"
      end
      csv << cols
    end
    filename = "monthly-report-#{Time.now.to_date.to_s}.csv"
    send_data(csv, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
  end
  
  def transaction_pdf_fees_month
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
        @fin_start_date = Configuration.find_by_config_key('FinancialYearStartDate').config_value
        @fin_end_date = Configuration.find_by_config_key('FinancialYearEndDate').config_value
        
        @finance_fee_category = FinanceFeeParticularCategory.find(:all,:conditions => ["is_deleted = ?", false])
        @all_fees_extra_particulers = []
        @all_fees_extra_particulers << "Discount"
        @all_fees_extra_particulers << "Fine"
        @all_fees_extra_particulers << "VAT"
        @transactions_particular = FinanceTransactionParticular.find(:all, :order => 'transaction_date desc', :conditions => ["transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'"])
        
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

  def transaction_pdf_fees
    fixed_category_name
    if date_format_check
      unless @start_date > @end_date
        @finance_fee_category = FinanceFeeParticularCategory.find(:all,:conditions => ["is_deleted = ?", false])
        @all_fees_extra_particulers = []
        @all_fees_extra_particulers << "Discount"
        @all_fees_extra_particulers << "Fine"
        @all_fees_extra_particulers << "VAT"
        @transactions_particular = FinanceTransactionParticular.find(:all, :order => 'transaction_date desc', :conditions => ["transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'"])
       
        render :pdf => 'transaction_pdf_fees',
        :margin => {:top=> 10,
        :bottom => 10,
        :left=> 10,
        :right => 10},
        :orientation => 'Landscape',
        :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
        :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
        end
    end
  end

  def transaction_pdf
    fixed_category_name
    @hr = Configuration.find_by_config_value("HR")
    if date_format_check
      @transactions = FinanceTransaction.find(:all,
        :order => 'transaction_date desc', :conditions => ["transaction_date >= '#{@start_date}' and transaction_date <= '#{@end_date}'"])
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
    @batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>true},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
    @inactive_batches = Batch.find(:all,:conditions=>{:is_deleted=>false,:is_active=>false},:joins=>:course,:select=>"`batches`.*,CONCAT(courses.code,'-',batches.name) as course_full_name",:order=>"course_full_name")
    @dates = []
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
    @finance_fee_category = FinanceFeeCategory.find(params[:id])
    #categories=FinanceFeeCategory.find(:all,:include=>:category_batches,:conditions=>"name=@finance_fee_category.name and description=@finance_fee_category.description and is_deleted=#{false}").map{|d| d if d.category_batches.empty?}.compact
    #    categories=FinanceFeeCategory.find(:all,:include=>:category_batches,:conditions=>"name='#{@finance_fee_category.name}' and description='#{@finance_fee_category.description}' and is_deleted=#{false}").uniq.map{|d| d if d.batch_id==@batch.id}.compact
    #    if categories.present?
    #      @finance_fee_category = FinanceFeeCategory.find_by_name_and_batch_id_and_is_deleted(@finance_fee_category.name,@batch.id,false)
    #    end
    #@particulars = FinanceFeeParticular.paginate(:page => params[:page],:joins=>"INNER JOIN finance_fee_categories on finance_fee_categories.id=finance_fee_particulars.finance_fee_category_id",:conditions => ["finance_fee_particulars.is_deleted = '#{false}' and finance_fee_categories.name = '#{@finance_fee_category.name}' and finance_fee_categories.description = '#{@finance_fee_category.description}' and finance_fee_particulars.batch_id='#{@batch.id}' "])
    @particulars = FinanceFeeParticular.paginate(:page => params[:page],:conditions => ["finance_fee_particulars.is_deleted = '#{false}' and finance_fee_particulars.is_tmp = '#{false}' and finance_fee_particulars.finance_fee_category_id = '#{@finance_fee_category.id}' and finance_fee_particulars.batch_id='#{@batch.id}' "], :joins => [:finance_fee_particular_category])
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
          @finance_fee_category = FinanceFeeCategory.find(@feeparticulars.finance_fee_category_id)
          @particulars = FinanceFeeParticular.paginate(:page => params[:page],:conditions => ["is_deleted = '#{false}' and finance_fee_category_id = '#{@finance_fee_category.id}' and batch_id='#{@feeparticulars.batch_id}'"])
          page.replace_html 'form-errors', :text => ''
          page << "Modalbox.hide();"
          page.replace_html 'categories', :partial => 'master_particulars_list'
          page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('flash_msg14')}</p>"
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
      @master_categories =@batches.finance_fee_categories.find(:all, :conditions =>["is_deleted = '#{false}' and is_master = 1 "])
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
      page.replace_html "batchs" ,:partial => "fee_collection_batchs"
      if is_late == 1
        page << "$('discount_types_radio').hide()"
        page << "j('#fee_discount_discount').css('width','84%')"
        page << "$('percent_span').show()"
      else
        page << "$('discount_types_radio').show()"
        page << "j('#fee_discount_discount').css('width','94%')"
        page << "$('percent_span').hide()"
      end
    end
  end
  
  def fee_collection_batch_list
    if params[:id].present?
      @fee_category=FinanceFeeCategory.find(params[:id])
      #@batches= Batch.find(:all,:joins=>"INNER JOIN `finance_fee_particulars` ON `batches`.id = `finance_fee_particulars`.batch_id INNER JOIN finance_fee_categories on finance_fee_categories.id=finance_fee_particulars.finance_fee_category_id INNER JOIN courses on courses.id=batches.course_id",:conditions=>"finance_fee_categories.name = '#{@fee_category.name}' and finance_fee_categories.description = '#{@fee_category.description}' and finance_fee_particulars.is_deleted=#{false}",:order=>"courses.code ASC").uniq
      @batches = Batch.active.find(:all,:joins=>[{:finance_fee_particulars=>:finance_fee_category},:course],:conditions=>"finance_fee_categories.id =#{@fee_category.id} and finance_fee_particulars.is_deleted=#{false}",:order=>"courses.code ASC").uniq
    end
    render :update do |page|
      page.replace_html "batchs" ,:partial => "fee_collection_batchs"
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
        page << "j('#fee_discount_discount').css('width','84%')"
        page << "$('percent_span').show()"
      else
        page << "$('discount_types_radio').show()"
        page << "j('#fee_discount_discount').css('width','94%')"
        page << "$('percent_span').hide()"
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
        page << "j('#fee_discount_discount').css('width','84%')"
        page << "$('percent_span').show()"
      else
        page << "$('discount_types_radio').show()"
        page << "j('#fee_discount_discount').css('width','94%')"
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
          @particular = [select_options] + finance_fee_particulars.map{|p| [p.name, p.finance_fee_particular_category.id.to_s + "-" + params[:id].to_s]} + fines.map{|p| [p.name, "F" + p.id.to_s + "-" + params[:id].to_s]}
        else
          @particular = [select_options] + finance_fee_particulars.map{|p| [p.name, p.finance_fee_particular_category.id.to_s + "-" + params[:id].to_s]}
        end
      end
    end
    render :update do |page|
      page.replace_html "discount_on" ,:partial => "fee_discount_on"
      page.replace_html "batchs" ,:text => ""
      page.replace_html "students" ,:text => ""
    end
  end
  
  def get_particular_by_category_student
    if params[:id].present?
      select_options = ["Total Fees", "0-" + params[:id].to_s]
      finance_fee_particulars = FinanceFeeParticular.active.find(:all,:joins=>[:finance_fee_particular_category],:conditions=>"finance_fee_particulars.finance_fee_category_id =#{params[:id]} and finance_fee_particulars.is_tmp=#{false} and finance_fee_particulars.is_deleted=#{false}",:group=>"finance_fee_particulars.finance_fee_particular_category_id").uniq
      fines = Fine.active
      @particular = [select_options] + finance_fee_particulars.map{|p| [p.name, p.finance_fee_particular_category.id.to_s + "-" + params[:id].to_s]} + fines.map{|p| [p.name, "F" + p.id.to_s + "-" + params[:id].to_s]}
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
      fines = Fine.active
      @particular = [select_options] + finance_fee_particulars.map{|p| [p.name, p.finance_fee_particular_category.id.to_s + "-" + params[:id].to_s]} + fines.map{|p| [p.name, "F" + p.id.to_s + "-" + params[:id].to_s]}
    end
    render :update do |page|
      page.replace_html "particular-box" ,:partial => "fee_particular_for_student"
      page.replace_html "batchs" ,:text => ""
    end
  end

  def fee_collection_new
    @fines=Fine.active
    @fee_categories=FinanceFeeCategory.find(:all,:joins=>"INNER JOIN finance_fee_particulars on finance_fee_particulars.finance_fee_category_id=finance_fee_categories.id AND finance_fee_particulars.is_tmp = 0 AND finance_fee_particulars.is_deleted = 0 INNER JOIN batches on batches.id=finance_fee_particulars.batch_id AND batches.is_active = 1 AND batches.is_deleted = 0 AND finance_fee_categories.is_deleted=0",:group=>'concat(finance_fee_categories.name,finance_fee_categories.description)')
    @finance_fee_collection = FinanceFeeCollection.new
  end

  def fee_collection_create
    sent_remainder = false
    unless params[:sent_remainder].nil?
      sent_remainder = true
    end
    
    include_transport = false
    unless params[:include_transport].nil?
      include_transport = true
    end
    
    include_employee = false
    unless params[:include_employee].nil?
      include_employee = true
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
      Delayed::Job.enqueue(DelayedFeeCollectionJob.new(@user,params[:finance_fee_collection],params[:fee_collection], sent_remainder,include_transport, include_employee))
      
      flash[:notice]="#{t('collection_is_in_queue')}" + " <a href='/scheduled_jobs/FinanceFeeCollection/1'>" + "#{t('cick_here_to_view_the_scheduled_job')}"
      #flash[:notice] = t('flash_msg33')

    end
    redirect_to :action => 'fee_collection_new'
  end

  def fee_collection_view
    @batchs = Batch.active
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
      @finance_fee_collections = @batch.finance_fee_collections
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
      
      Delayed::Job.enqueue(DelayedFeeCollectionNotificationJob.new(@user, params[:id], params[:batch_id]))
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

  def fee_collection_edit
    @finance_fee_collection = FinanceFeeCollection.find params[:id]
    @batch=Batch.find(params[:batch_id])
  end
  
  def fee_collection_assign_discount
    @discounts = FeeDiscount.find_all_by_batch_id_and_is_onetime(params[:batch_id], true )
    @fee_collection_id = params[:id]
    @batch_id = params[:batch_id]
    @fee_collection_discount = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id(@fee_collection_id, params[:batch_id]).map(&:fee_discount_id)
  end

  def assign_fee_discount_to_collection
    unless params[:fee_collection_id].nil?
      @discount_id = params[:id]
      @fee_collection_id = params[:fee_collection_id]
      @batch_id = params[:batch_id]
      
      discount = FeeDiscount.find(@discount_id)
      receiver_id = discount.receiver_id
      finance_fee_category_id = discount.finance_fee_category_id
      finance_fee_particular_category_id = discount.finance_fee_particular_category_id
      is_late = discount.is_late
      batch_id = discount.batch_id
       
      
      if is_late
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
        @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
        CollectionDiscount.create(:fee_discount_id=>discount.id,:finance_fee_collection_id=>@fee_collection_id, :finance_fee_particular_category_id => discount.finance_fee_particular_category_id)

        @fee = FinanceFee.all(:conditions=>"fee_collection_id = #{@fee_collection.id} and is_paid=#{false}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")

        @fee.each do |fe|
          s = fe.student

          unless s.has_paid_fees
              FinanceFee.update_student_fee(@fee_collection, s, fe)
          end
        end
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
      end
    else
      render :text => ''
    end
  end
  
  def remove_fee_discount_from_collection
    unless params[:fee_collection_id].nil?
      @fee_collection_id = params[:fee_collection_id]
      @discount_id = params[:id]
      @batch_id = params[:batch_id]
      
      discount = FeeDiscount.find(@discount_id)
      receiver_id = discount.receiver_id
      finance_fee_category_id = discount.finance_fee_category_id
      finance_fee_particular_category_id = discount.finance_fee_particular_category_id
      is_late = discount.is_late
      batch_id = discount.batch_id
      
      fee_discount_collection = FeeDiscountCollection.find(:first, :conditions => ["finance_fee_collection_id = ? and fee_discount_id = ? and batch_id = ?", @fee_collection_id, @discount_id, @batch_id])
      
      unless fee_discount_collection.nil?
        
        fee_discount_collection.destroy
        
        unless is_late
          @fee_collection = FinanceFeeCollection.find(@fee_collection_id)
          collection_discount = CollectionDiscount.find_by_fee_discount_id_and_finance_fee_collection_id_and_finance_fee_particular_category_id(discount.id,@fee_collection_id, discount.finance_fee_particular_category_id)
          collection_discount.destroy

          @fee = FinanceFee.all(:conditions=>"fee_collection_id = #{@fee_collection.id} and is_paid=#{false}" ,:joins=>"INNER JOIN students ON finance_fees.student_id = students.id")

          @fee.each do |fe|
            s = fe.student

            unless s.has_paid_fees
                FinanceFee.update_student_fee(@fee_collection, s, fe)
            end
          end
        end
        
        render :update do |page|
          page.replace_html 'discount_option_' + @discount_id.to_s, :partial => 'fee_collection_discount_remove'
        end
      else
        render :text => ''
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

    @batch = Batch.find(params[:batch_id])
    @dates = @batch.finance_fee_collections
    render :update do |page|
      page.replace_html "fees_collection_dates", :partial => "fees_collection_dates"
    end
  end
  
  def update_bill_generation_dates

    @batch = Batch.find(params[:batch_id])
    @dates = @batch.finance_fee_collections
    render :update do |page|
      page.replace_html "fees_collection_dates", :partial => "bill_generation_dates"
    end
  end
  
  def load_particular_wise_reports
    unless params[:date].nil? or params[:date].empty? or params[:date].blank?
      @date    =  @fee_collection = FinanceFeeCollection.find(params[:date])
      @batches =  @date.batches
      
      tmp_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id IN (#{@batches.map(&:id).join(',')})", :group => "name")
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
                @student = @fee.student

                tmp['id'] = sid
                tmp['name'] = @student.full_name
                @financefee = @student.finance_fee_by_date @date
                @due_date = @fee_collection.due_date
                @paid_fees = @fee.finance_transactions

                fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==batch) }
                fee_particulars.each do |fee_particular|
                  if particulars_name.include?(fee_particular.name)
                    tmp[fee_particular.name] = fee_particular.amount
                  end
                end

                total_payable=fee_particulars.map{|s| s.amount}.sum.to_f
                tmp['Total Payable'] = total_payable
                @total_discount = 0

                calculate_discount(@date, batch, @student, @fee.is_paid)

                tmp['Discount'] = @total_discount

                bal=(total_payable-@total_discount).to_f
                @submission_date = Date.today
                if @financefee.is_paid
                  @paid_fees = @financefee.finance_transactions
                  days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
                else
                  days=(Date.today-@date.due_date.to_date).to_i
                end

                auto_fine=@date.fine

                @has_fine_discount = false
                if days > 0 and auto_fine #and @financefee.is_paid == false
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
      
      tmp_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id IN (#{@batches.map(&:id).join(',')})", :group => "name")
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
                @student = @fee.student

                tmp['id'] = sid
                tmp['name'] = @student.full_name
                @financefee = @student.finance_fee_by_date @date
                @due_date = @fee_collection.due_date
                @paid_fees = @fee.finance_transactions

                fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==batch) }
                fee_particulars.each do |fee_particular|
                  if particulars_name.include?(fee_particular.name)
                    tmp[fee_particular.name] = fee_particular.amount
                  end
                end

                total_payable=fee_particulars.map{|s| s.amount}.sum.to_f
                tmp['Total Payable'] = total_payable
                @total_discount = 0

                calculate_discount(@date, batch, @student, @fee.is_paid)

                tmp['Discount'] = @total_discount

                bal=(total_payable-@total_discount).to_f
                @submission_date = Date.today
                if @financefee.is_paid
                  @paid_fees = @financefee.finance_transactions
                  days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
                else
                  days=(Date.today-@date.due_date.to_date).to_i
                end

                auto_fine=@date.fine

                @has_fine_discount = false
                if days > 0 and auto_fine #and @financefee.is_paid == false
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
          @student = @fee.student
          
          tmp['id'] = sid
          tmp['name'] = @student.full_name
          @financefee = @student.finance_fee_by_date @date
          @due_date = @fee_collection.due_date
          @paid_fees = @fee.finance_transactions
          
          fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
          fee_particulars.each do |fee_particular|
            if particulars_name.include?(fee_particular.name)
              tmp[fee_particular.name] = fee_particular.amount
            end
          end
          
          total_payable=fee_particulars.map{|s| s.amount}.sum.to_f
          tmp['Total Payable'] = total_payable
          @total_discount = 0
        
          calculate_discount(@date, @batch, @student, @fee.is_paid)
          
          tmp['Discount'] = @total_discount
          
          bal=(total_payable-@total_discount).to_f
          @submission_date = Date.today
          if @financefee.is_paid
            @paid_fees = @financefee.finance_transactions
            days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
          else
            days=(Date.today-@date.due_date.to_date).to_i
          end

          auto_fine=@date.fine

          @has_fine_discount = false
          if days > 0 and auto_fine #and @financefee.is_paid == false
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
      render :update do |page|
        page.replace_html "student", :partial => "bill_generation_reports"
      end
    else
      render :update do |page|
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
          @student = @fee.student
          
          tmp['id'] = sid
          tmp['name'] = @student.full_name
          @financefee = @student.finance_fee_by_date @date
          @due_date = @fee_collection.due_date
          @paid_fees = @fee.finance_transactions
          
          fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
          fee_particulars.each do |fee_particular|
            if particulars_name.include?(fee_particular.name)
              tmp[fee_particular.name] = fee_particular.amount
            end
          end
          
          total_payable=fee_particulars.map{|s| s.amount}.sum.to_f
          tmp['Total Payable'] = total_payable
          @total_discount = 0
        
          calculate_discount(@date, @batch, @student, @fee.is_paid)
          
          tmp['Discount'] = @total_discount
          
          bal=(total_payable-@total_discount).to_f
          @submission_date = Date.today
          if @financefee.is_paid
            @paid_fees = @financefee.finance_transactions
            days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
          else
            days=(Date.today-@date.due_date.to_date).to_i
          end

          auto_fine=@date.fine

          @has_fine_discount = false
          if days > 0 and auto_fine #and @financefee.is_paid == false
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
          @student = @fee.student
          
          tmp['id'] = sid
          tmp['name'] = @student.full_name
          @financefee = @student.finance_fee_by_date @date
          @due_date = @fee_collection.due_date
          @paid_fees = @fee.finance_transactions
          
          fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
          fee_particulars.each do |fee_particular|
            if particulars_name.include?(fee_particular.name)
              tmp[fee_particular.name] = fee_particular.amount
            end
          end
          
          total_payable=fee_particulars.map{|s| s.amount}.sum.to_f
          tmp['Total Payable'] = total_payable
          @total_discount = 0
        
          calculate_discount(@date, @batch, @student, @fee.is_paid)
          
          tmp['Discount'] = @total_discount
          
          bal=(total_payable-@total_discount).to_f
          @submission_date = Date.today
          if @financefee.is_paid
            @paid_fees = @financefee.finance_transactions
            days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
          else
            days=(Date.today-@date.due_date.to_date).to_i
          end

          auto_fine=@date.fine

          @has_fine_discount = false
          if days > 0 and auto_fine #and @financefee.is_paid == false
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
          @student = @fee.student
          
          tmp['id'] = sid
          tmp['name'] = @student.full_name
          @financefee = @student.finance_fee_by_date @date
          @due_date = @fee_collection.due_date
          @paid_fees = @fee.finance_transactions
          
          fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
          fee_particulars.each do |fee_particular|
            if particulars_name.include?(fee_particular.name)
              tmp[fee_particular.name] = fee_particular.amount
            end
          end
          
          total_payable=fee_particulars.map{|s| s.amount}.sum.to_f
          tmp['Total Payable'] = total_payable
          @total_discount = 0
        
          calculate_discount(@date, @batch, @student, @fee.is_paid)
          
          tmp['Discount'] = @total_discount
          
          bal=(total_payable-@total_discount).to_f
          @submission_date = Date.today
          if @financefee.is_paid
            @paid_fees = @financefee.finance_transactions
            days=(@paid_fees.first.transaction_date-@date.due_date.to_date).to_i
          else
            days=(Date.today-@date.due_date.to_date).to_i
          end

          auto_fine=@date.fine

          @has_fine_discount = false
          if days > 0 and auto_fine #and @financefee.is_paid == false
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
      
      @particular_name = params[:particular]
      
      @finance_fee_particular_category = FinanceFeeParticularCategory.find_or_create_by_name_and_description_and_is_deleted(@particular_name, '',false)
      
      @particular = @batch.finance_fee_particulars.find_by_finance_fee_category_id_and_finance_fee_particular_category_id_and_name_and_receiver_type_and_receiver_id(@date.fee_category_id, @finance_fee_particular_category.id, @particular_name, 'Student', @student.id)
      if @particular.nil?
        @o_particular = {}
        @o_particular[:amount] = params[:amount]
        @o_particular[:description] = ''
        @o_particular[:finance_fee_category_id] = @date.fee_category_id
        @o_particular[:finance_fee_particular_category_id] = @finance_fee_particular_category.id
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
      
      if @particular_name.downcase == 'vat'
        if params[:no_vat].nil?
          render :update do |page|
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
            page << 'j("#particulars_tr_id").addClass("particulars_tr");'
            page << 'j("#particulars_tr_id").attr("id","particular_' + @finance_fee_particular.id.to_s + '");'
            page << 'j("#particulars_tr_extra").remove();'
            page << 'resetSN();'
            page << 'calculateAmountToPay(0);'
            page << 'calculateDiscount();'
          end
        end
      else
        render :update do |page|
          page.replace_html "particulars_tr_id", :partial => "temp_particular"
          page << 'j("#particulars_tr_id").addClass("particulars_tr");'
          page << 'j("#particulars_tr_id").attr("id","particular_' + @finance_fee_particular.id.to_s + '");'
          page << 'j("#particulars_tr_extra").remove();'
          page << 'resetSN();'
          page << 'calculateAmountToPay(0);'
          page << 'calculateDiscount();'
        end
        
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
      @batch   = Batch.find(params[:batch_id])
      @date    =  @fee_collection = FinanceFeeCollection.find(params[:date])
      student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')

      @dates   = @batch.finance_fee_collections
      
      @from_batch_fee = true
      unless params[:student_fees].nil?
        if params[:student_fees].to_i == 1
          @from_batch_fee = false
        end
      end

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
        #abort(@paid_fees.map(&:id).inspect)
        @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])

        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
        
        @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
        @total_discount = 0
        
        calculate_discount(@date, @batch, @student, @fee.is_paid)

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
        
        auto_fine=@date.fine
        
        @has_fine_discount = false
        if days > 0 and auto_fine #and @financefee.is_paid == false
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
          page.replace_html "student", :partial => "student_fees_submission"
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

  def update_ajax
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

    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
    
    @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
    
    @total_discount = 0
    
    calculate_discount(@date, @batch, @student, @financefee.is_paid)
    
    require 'date'
    bal=(@total_payable-@total_discount).to_f
    days=(Date.parse(params[:fees][:transaction_date])-@date.due_date.to_date).to_i
    auto_fine=@date.fine
    
    @has_fine_discount = false
    if days > 0 and auto_fine and @financefee.is_paid == false
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
    
    discount_on_total_fee = false
    one_time_discounts_on_particulars = @date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
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
          onetime_discounts = @date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{@batch.id}").select{|par|  ((par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch))  }
          if onetime_discounts.length > 0
            onetime_discounts.each do |d|   
              paidAmount = 0
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              transaction_ids = @paid_fees.map(&:id)
              unless transaction_ids.blank?
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
    render :update do |page|
      page.replace_html "student", :partial => "student_fees_submission"

    end

  end
  
  def student_fee_receipt_all_pdf
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
        @all_financefee[student.id] = f_report 
      
        @all_paid_fees[student.id] = @all_financefee[student.id].finance_transactions
        @all_fee_particulars[student.id] = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch)}
        @all_total_discount[student.id] = 0
        @all_total_payable[student.id] = @all_fee_particulars[student.id].map{|s| s.amount}.sum.to_f
        calculate_discount_index_all(@date, @student.batch, @student,@student.id, @all_financefee[@iloop].is_paid)

        bal=(@all_total_payable[@iloop]-@all_total_discount[@iloop]).to_f
        days=(Date.today-@date.due_date.to_date).to_i
        auto_fine=@date.fine
        @all_has_fine_discount[@iloop] = false
        if days > 0 and auto_fine and @all_financefee[@iloop].is_paid == false

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
    @batch=Batch.find(params[:batch_id])
    @currency_type = currency
    if @dates_array.count == 1
      @date = @fee_collection = FinanceFeeCollection.find(params[:id2])
      @financefee = @student.finance_fee_by_date @date
      @student_has_due = false
      @std_finance_fee_due = FinanceFee.find(:first,:conditions=>["finance_fee_collections.due_date < ? and finance_fees.is_paid = 0 and finance_fees.student_id = ?", @date.due_date,@student.id],:include=>"finance_fee_collection")
      unless @std_finance_fee_due.blank?
        @student_has_due = true
      end
      @due_date = @fee_collection.due_date
      @paid_fees = @financefee.finance_transactions
      @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])
      
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch)}
      @total_discount = 0
      @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
      
      calculate_discount(@date, @student.batch, @student, @financefee.is_paid)
      
      bal=(@total_payable-@total_discount).to_f
      days=(Date.today-@date.due_date.to_date).to_i
      auto_fine=@date.fine
      
      @has_fine_discount = false
      if days > 0 and auto_fine and @financefee.is_paid == false
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
        @date[@iloop] = @fee_collection[@iloop] = FinanceFeeCollection.find(date)
        @financefee[@iloop] = @student.finance_fee_by_date(@date[@iloop])
        @due_date[@iloop] = @fee_collection[@iloop].due_date
        @fee_category[@iloop] = FinanceFeeCategory.find(@fee_collection[@iloop].fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
        flash[:warning]=nil
        flash[:notice]=nil
        @paid_fees[@iloop] = @financefee[@iloop].finance_transactions

        @fee_particulars[@iloop] = @date[@iloop].finance_fee_particulars.all(:conditions=>"batch_id=#{@financefee[@iloop].batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[@iloop].batch) }
        @total_discount[@iloop] = 0
        @total_payable[@iloop]=@fee_particulars[@iloop].map{|s| s.amount}.sum.to_f

        calculate_discount_index(@date[@iloop], @financefee[@iloop].batch, @student, @iloop, @financefee[@iloop].is_paid)

        bal=(@total_payable[@iloop]-@total_discount[@iloop]).to_f
        days=(Date.today-@date[@iloop].due_date.to_date).to_i
        auto_fine=@date[@iloop].fine
        @has_fine_discount[@iloop] = false
        if days > 0 and auto_fine and @financefee[@iloop].is_paid == false
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
  
  def update_vat_ajax
    if request.post?
      @date = @fee_collection = FinanceFeeCollection.find(params[:vat][:date])
      @batch   = Batch.find(params[:vat][:batch_id])
      student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
      @dates = @batch.finance_fee_collections
      @student = Student.find(params[:vat][:student]) if params[:vat][:student]
      @student ||= FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}",:joins=>'INNER JOIN students ON finance_fees.student_id = students.id').student
      @prev_student = @student.previous_fee_student(@date.id,student_ids)
      @next_student = @student.next_fee_student(@date.id, student_ids)

      @financefee = @student.finance_fee_by_date @date
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

      @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      
      #@discounts=@date.fee_discounts.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch)}

      @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
      @total_discount = 0
    
      calculate_discount(@date, @batch, @student, @financefee.is_paid)
      
      bal=(@total_payable-@total_discount).to_f
      days=(Date.today-@date.due_date.to_date).to_i
      auto_fine=@date.fine
      @has_fine_discount = false
      
      if days > 0 and auto_fine and @financefee.is_paid == false
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

      end
    end
  end

  def update_fine_ajax
    if request.post?
      @date = @fee_collection = FinanceFeeCollection.find(params[:fine][:date])
      @batch   = Batch.find(params[:fine][:batch_id])
      student_ids=@date.finance_fees.find(:all,:conditions=>"batch_id='#{@batch.id}'").collect(&:student_id).join(',')
      @dates = @batch.finance_fee_collections
      @student = Student.find(params[:fine][:student]) if params[:fine][:student]
      @student ||= FinanceFee.first(:conditions=>"fee_collection_id = #{@date.id}",:joins=>'INNER JOIN students ON finance_fees.student_id = students.id').student
      @prev_student = @student.previous_fee_student(@date.id,student_ids)
      @next_student = @student.next_fee_student(@date.id, student_ids)

      @financefee = @student.finance_fee_by_date @date
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

      @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted = false"])
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
      @total_discount = 0
      @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
      
      calculate_discount(@date, @batch, @student, @financefee.is_paid)
      bal=(@total_payable-@total_discount).to_f
      days=(Date.today-@date.due_date.to_date).to_i
      auto_fine=@date.fine
      @has_fine_discount = false
      if days > 0 and auto_fine and @financefee.is_paid == false
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
    @student = Student.find(params[:id])
    @dates=FinanceFeeCollection.find(:all,:joins=>"INNER JOIN fee_collection_batches on fee_collection_batches.finance_fee_collection_id=finance_fee_collections.id INNER JOIN finance_fees on finance_fees.fee_collection_id=finance_fee_collections.id",:conditions=>"finance_fees.student_id='#{@student.id}' and finance_fee_collections.is_deleted=#{false} and fee_collection_batches.is_deleted=#{false}").uniq
  end

  def fees_submission_student
    if params[:date].present?
      @dates_array = params[:date].split(",")
      if @dates_array.count == 1
        @student = Student.find(params[:id])
        @date = @fee_collection = FinanceFeeCollection.find(params[:date])
        @financefee = @student.finance_fee_by_date(@date)
        @due_date = @fee_collection.due_date
        @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
        flash[:warning]=nil
        flash[:notice]=nil

        @paid_fees = @financefee.finance_transactions

        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
        @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
        @total_discount = 0
        
        calculate_discount(@date, @financefee.batch, @student, @financefee.is_paid)
        
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
        
        auto_fine=@date.fine
        @has_fine_discount = false
        if days > 0 and auto_fine and @financefee.is_paid == false
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
          @date[@iloop] = @fee_collection[@iloop] = FinanceFeeCollection.find(date)
          @financefee[@iloop] = @student.finance_fee_by_date(@date[@iloop])
          @due_date[@iloop] = @fee_collection[@iloop].due_date
          @fee_category[@iloop] = FinanceFeeCategory.find(@fee_collection[@iloop].fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
          flash[:warning]=nil
          flash[:notice]=nil
          @paid_fees[@iloop] = @financefee[@iloop].finance_transactions
          
          @fee_particulars[@iloop] = @date[@iloop].finance_fee_particulars.all(:conditions=>"batch_id=#{@financefee[@iloop].batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[@iloop].batch) }
          @total_discount[@iloop] = 0
          @total_payable[@iloop]=@fee_particulars[@iloop].map{|s| s.amount}.sum.to_f
          
          calculate_discount_index(@date[@iloop], @financefee[@iloop].batch, @student, @iloop, @financefee[@iloop].is_paid)
        
          bal=(@total_payable[@iloop]-@total_discount[@iloop]).to_f
          days=(Date.today-@date[@iloop].due_date.to_date).to_i
          auto_fine=@date[@iloop].fine
          @has_fine_discount[@iloop] = false
          if days > 0 and auto_fine and @financefee[@iloop].is_paid == false
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
    @student = Student.find(params[:vat][:student])
    @date = @fee_collection = FinanceFeeCollection.find(params[:vat][:date])
    @financefee = @student.finance_fee_by_date(@date)
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
    
    @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
    @total_discount = 0
    @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
    
    calculate_discount(@date, @student.batch, @student, @financefee.is_paid)
      
    bal=(@total_payable-@total_discount).to_f
    days=(Date.today-@date.due_date.to_date).to_i
    auto_fine=@date.fine
    @has_fine_discount = false
    if days > 0 and auto_fine and @financefee.is_paid == false
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
    @student = Student.find(params[:fine][:student])
    @date = @fee_collection = FinanceFeeCollection.find(params[:fine][:date])
    @financefee = @student.finance_fee_by_date(@date)
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
    @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
    @total_discount = 0
    @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
    
    calculate_discount(@date, @student.batch, @student, @financefee.is_paid)
    
    bal=(@total_payable-@total_discount).to_f
    days=(Date.today-@date.due_date.to_date).to_i
    auto_fine=@date.fine
    @has_fine_discount = false 
    if days > 0 and auto_fine and @financefee.is_paid == false
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
    @student = Student.find(params[:student])
    @date = @fee_collection = FinanceFeeCollection.find(params[:date])
    @financefee = @date.fee_transactions(@student.id)

    @due_date = @fee_collection.due_date
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
    @total_discount = 0
    @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
    
    calculate_discount(@date, @student.batch, @student, @financefee.is_paid)
    
    total_fees = @financefee.balance.to_f+Champs21Precision.set_and_modify_precision(params[:special_fine]).to_f
    unless params[:fine].nil?
      total_fees += Champs21Precision.set_and_modify_precision(params[:fine]).to_f
    end
    unless params[:vat].nil?
      total_fees += Champs21Precision.set_and_modify_precision(params[:vat]).to_f
    end
    bal=(@total_payable-@total_discount).to_f
    days=(Date.today-@date.due_date.to_date).to_i
    auto_fine=@date.fine
    @has_fine_discount = false
    if days > 0 and auto_fine and @financefee.is_paid == false
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
    @student = Student.find(params[:id])
    @fee_collection = FinanceFeeCollection.find params[:date]
    @finance_fee=@student.finance_fee_by_date(@fee_collection)
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    @fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"batch_id=#{@finance_fee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@finance_fee.batch) }
    @total_discount = 0
    @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
    
    calculate_discount(@fee_collection, @finance_fee.batch, @student, @finance_fee.is_paid)
    
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
    @dates = []
  end

  def update_batches
    @course = Course.find(params[:course_id])
    @batchs = @course.batches

    render :update do |page|
      page.replace_html "batches_list", :partial => "batches_list"
    end
  end

  def update_fees_collection_dates_defaulters
    @batch  = Batch.find(params[:batch_id])
    @dates = @batch.finance_fee_collections
    render :update do |page|
      page.replace_html "fees_collection_dates", :partial => "fees_collection_dates_defaulters"
    end
  end

  def fees_defaulters_students
    @batch   = Batch.find(params[:batch_id])
    @date = FinanceFeeCollection.find(params[:date])
    @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id='#{@date.id}' and finance_fees.balance > 0 and finance_fees.batch_id='#{@batch.id}'"],:order=>"students.first_name ASC").uniq
    render :update do |page|
      page.replace_html "student", :partial => "student_defaulters"
    end
  end

  def fee_defaulters_pdf
    @batch   = Batch.find(params[:batch_id])
    @date = @finance_fee_collection = FinanceFeeCollection.find(params[:date])
    @defaulters=Student.find(:all,:joins=>"INNER JOIN finance_fees on finance_fees.student_id=students.id ",:conditions=>["finance_fees.fee_collection_id='#{@date.id}' and finance_fees.balance > 0 and finance_fees.batch_id='#{@batch.id}'"],:select=>["students.*,finance_fees.balance as balance"],:order=>"students.first_name ASC").uniq
    @currency_type = currency

    render :pdf => 'fee_defaulters_pdf'
  end

  def pay_fees_defaulters
    @batch=Batch.find(params[:batch_id])
    @student = Student.find(params[:id])
    @date = @fee_collection = FinanceFeeCollection.find(params[:date])
    @financefee = @student.finance_fee_by_date(@date)
    @due_date = @fee_collection.due_date
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    flash[:warning]=nil
    flash[:notice]=nil

    @paid_fees = @financefee.finance_transactions

    @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{@batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@batch) }
    @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
    @total_discount = 0

    calculate_discount(@date, @batch, @student, @financefee.is_paid)

    bal=(@total_payable-@total_discount).to_f
    days=(Date.today-@date.due_date.to_date).to_i
    auto_fine=@date.fine
    @has_fine_discount = false
    if days > 0 and auto_fine and @financefee.is_paid == false
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
    @finance_fee=@student.finance_fee_by_date(@fee_collection)
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])
    @fee_particulars = @fee_collection.finance_fee_particulars.all(:conditions=>"batch_id=#{@finance_fee.batch_id}").select{|par| par.receiver==@student or par.receiver==@student.student_category or par.receiver==@finance_fee.batch}
    @discounts=@fee_collection.fee_discounts.all(:conditions=>"batch_id=#{@finance_fee.batch_id}").select{|par| par.receiver==@student or par.receiver==@student.student_category or par.receiver==@finance_fee.batch}
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
      @fee_categories =  FinanceFeeCategory.find(:all,:joins=>"INNER JOIN finance_fee_particulars on finance_fee_particulars.finance_fee_category_id=finance_fee_categories.id AND finance_fee_particulars.is_tmp = 0 AND finance_fee_particulars.is_deleted = 0 INNER JOIN batches on batches.id=finance_fee_particulars.batch_id AND batches.is_active = 1 AND batches.is_deleted = 0 AND finance_fee_categories.is_deleted=0",:group=>'finance_fee_categories.name')
      @fee_discount = BatchFeeDiscount.new
      render :update do |page|
        page.replace_html "form-box", :partial => "batch_wise_discount_form";
        page.replace_html 'form-errors', :text =>""
      end
    elsif params[:type]== "category_wise"
      @fee_categories = FinanceFeeCategory.find(:all,:joins=>"INNER JOIN finance_fee_particulars on finance_fee_particulars.finance_fee_category_id=finance_fee_categories.id AND finance_fee_particulars.is_tmp = 0 AND finance_fee_particulars.is_deleted = 0 INNER JOIN batches on batches.id=finance_fee_particulars.batch_id AND batches.is_active = 1 AND batches.is_deleted = 0 AND finance_fee_categories.is_deleted=0",:group=>'finance_fee_categories.name')
      @student_categories = StudentCategory.active
      render :update do |page|
        page.replace_html "form-box", :partial => "category_wise_discount_form"
        page.replace_html 'form-errors', :text =>""
      end
    elsif params[:type] == "student_wise"
      @fee_categories = FinanceFeeCategory.find(:all,:joins=>"INNER JOIN finance_fee_particulars on finance_fee_particulars.finance_fee_category_id=finance_fee_categories.id AND finance_fee_particulars.is_tmp = 0 AND finance_fee_particulars.is_deleted = 0 INNER JOIN batches on batches.id=finance_fee_particulars.batch_id AND batches.is_active = 1 AND batches.is_deleted = 0 AND finance_fee_categories.is_deleted=0",:group=>'finance_fee_categories.name')
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

      @fee_category = FinanceFeeCategory.find(params[:id])
      @discounts = @fee_category.fee_discounts.all(:conditions=>["batch_id='#{@batch.id}' and is_deleted= 0"])

      render :update do |page|
        page.replace_html "discount-box", :partial => "show_fee_discounts"
      end
    end
  end

  def edit_fee_discount
    @fee_discount = FeeDiscount.find(params[:id])
  end

  def update_fee_discount
    @fee_discount = FeeDiscount.find(params[:id])
    unless @fee_discount.update_attributes(params[:fee_discount])
      @error = true
    else
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
    unless @fee_category.nil?
      @discounts = @fee_category.fee_discounts.all(:conditions=>["batch_id='#{@fee_discount.batch_id}' and is_deleted= #{false}"])
      #@fee_category.is_collection_open ? @discount_edit = false : @discount_edit = true
    end
    render :update do |page|
      page.replace_html "discount-box", :partial => "show_fee_discounts"
      page.replace_html "flash-notice", :text => "<p class='flash-msg'>#{t('discount_deleted_successfully')}.</p>"
    end

  end

  def collection_details_view
    @fee_collection = FinanceFeeCollection.find(params[:id])
    @particulars = @fee_collection.finance_fee_particulars.all(:conditions=>["batch_id='#{params[:batch_id]}'"])
    @total_payable=@particulars.map{|s| s.amount}.sum.to_f
    @discounts = @fee_collection.fee_discounts.all(:conditions=>["batch_id='#{params[:batch_id]}'"])
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
    end
  end
  
  def transaction_deletion
    @student = Student.find(params[:id])
    @date = @fee_collection = FinanceFeeCollection.find(params[:date])
    @financefee = @student.finance_fee_by_date(@date)
    @financetransaction=FinanceTransaction.find(params[:transaction_id])
    balance=@financefee.balance+(@financetransaction.amount-@financetransaction.fine_amount)
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
    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])

    flash[:warning]=nil
    flash[:notice]=nil

    @paid_fees = @financefee.finance_transactions


    @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) and (!par.is_deleted )}
    @total_discount = 0
    @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
    
    calculate_discount(@date, @student.batch, @student, @financefee.is_paid)
    
    bal=(@total_payable-@total_discount).to_f
    days=(Date.today-@date.due_date.to_date).to_i
    auto_fine=@date.fine
    @has_fine_discount = false
    if days > 0 and auto_fine and @financefee.is_paid == false
      @fine_rule=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
      @fine_amount=@fine_rule.is_amount ? @fine_rule.fine_amount : (bal*@fine_rule.fine_amount)/100 if @fine_rule
      calculate_extra_fine(@date, @batch, @student, @fine_rule)
      @new_fine_amount = @fine_amount
      get_fine_discount(@date, @batch, @student)
      if @fine_amount < 0
         @fine_amount = 0
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
    @student = Student.find(params[:id])
    if params[:date].present?
      @date = @fee_collection = FinanceFeeCollection.find(params[:date])
      @financefee = @student.finance_fee_by_date(@date)


      @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])

      @paid_fees = @financefee.finance_transactions

      @refund_amount=0
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
      @total_discount = 0
      @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
      
      calculate_discount(@date, @student.batch, @student, @financefee.is_paid)
      
      @collection=FinanceFeeCollection.find_by_name(@date.name,:conditions=>{:is_deleted=>false})
      @refund_rule=@collection.refund_rules.find(:first,:order=>'refund_validity ASC',:conditions=>["refund_validity >=  '#{Date.today}'"])
      @fee_refund=@financefee.fee_refund
      unless @refund_rule
        #@fee_refund=@financefee.fee_refund
        @refund_rule=@fee_refund.refund_rule if @fee_refund
      end
      
      bal=(@total_payable-@total_discount).to_f
      days=(Date.today-@date.due_date.to_date).to_i
      auto_fine=@date.fine
      @has_fine_discount = false
      if days > 0 and auto_fine and @financefee.is_paid == false
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
    @student = Student.find(params[:id])
    @date = @fee_collection = FinanceFeeCollection.find(params[:date])
    @financefee = @student.finance_fee_by_date(@date)


    @fee_category = FinanceFeeCategory.find(@fee_collection.fee_category_id,:conditions => ["is_deleted IS NOT NULL"])

    @paid_fees = @financefee.finance_transactions

    @refund_amount=0
    @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"batch_id=#{@student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
    @total_discount = 0
    @total_payable=@fee_particulars.map{|s| s.amount}.sum.to_f
    
    calculate_discount(@date, @student.batch, @student, @financefee.is_paid)
    
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

  private

  def date_format(date)
    /(\d{4}-\d{2}-\d{2})/.match(date)
  end

  def calculate_discount(date,batch,student,is_paid)
    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []
    
    if MultiSchool.current_school.id == 312
      fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)) }
      @discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
      @discounts_amount = []
        @discounts.each do |d|
          @discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
          @total_discount = @total_discount + @discounts_amount[d.id]
      end
    else
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
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        @onetime_discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if @onetime_discounts.length > 0
          one_time_discount = true
          @onetime_discounts_amount = []
          i = 0
          @onetime_discounts.each do |d|   
            onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
              payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              @onetime_discounts_amount[d.id] = discount_amt
              @total_discount = @total_discount + discount_amt
              i = i + 1
            end
          end
        end
      end

      unless one_time_total_amount_discount
        if onetime_discount_particulars_id.empty?
          onetime_discount_particulars_id[0] = 0
        end
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if discounts_on_particulars.length > 0
          @discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          @discounts_amount = []
          @discounts.each do |d|   
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
              payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              @discounts_amount[d.id] = discount_amt
              @total_discount = @total_discount + discount_amt
            end
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
  end
  
  def calculate_discount_index_all(date,batch,student,ind,is_paid)
    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []
    
    if MultiSchool.current_school.id == 312
      fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
      @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
      if @all_onetime_discounts[ind].length > 0
        @all_onetime_discounts_amount[ind] = []
          @all_onetime_discounts[ind].each do |d|
            @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
            @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
        end
      end
    else
      one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      if @all_onetime_discounts[ind].length > 0
        one_time_total_amount_discount= true
        @all_onetime_discounts_amount[ind] = []
        @all_onetime_discounts[ind].each do |d|
          @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
          @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
        end
      else
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if @all_onetime_discounts[ind].length > 0
          one_time_discount = true
          @all_onetime_discounts_amount[ind] = []
          i = 0
          @all_onetime_discounts[ind].each do |d|   
            onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
              payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              @all_onetime_discounts_amount[ind][d.id] = discount_amt
              @all_total_discount[ind] = @all_total_discount[ind] + discount_amt
              i = i + 1
            end
          end
        end
      end

      unless one_time_total_amount_discount
        if onetime_discount_particulars_id.empty?
          onetime_discount_particulars_id[0] = 0
        end
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)}
        discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if discounts_on_particulars.length > 0
          @all_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          @all_discounts_amount[ind] = []
          @all_discounts[ind].each do |d|   
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
              payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              @all_discounts_amount[ind][d.id] = discount_amt
              @all_total_discount[ind] = @all_total_discount[ind] + discount_amt
            end
          end
        else  
          unless one_time_discount
            @all_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            @all_discounts_amount[ind] = []
            @all_discounts[ind].each do |d|
              @all_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
              @all_total_discount[ind] = @all_total_discount[ind] + @all_discounts_amount[ind][d.id]
            end
          end
        end
      end
    end
  end
  
  def calculate_discount_index(date,batch,student,ind,is_paid)
    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []
    
    if MultiSchool.current_school.id == 312
      fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
      @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
      if @onetime_discounts[ind].length > 0
        @onetime_discounts_amount[ind] = []
          @onetime_discounts[ind].each do |d|
            @onetime_discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
        end
      end
    else
      one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      if @onetime_discounts[ind].length > 0
        one_time_total_amount_discount= true
        @onetime_discounts_amount[ind] = []
        @onetime_discounts[ind].each do |d|
          @onetime_discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
          @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
        end
      else
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if @onetime_discounts[ind].length > 0
          one_time_discount = true
          @onetime_discounts_amount[ind] = []
          i = 0
          @onetime_discounts[ind].each do |d|   
            onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            @onetime_discounts_amount[ind][d.id] = discount_amt
            @total_discount[ind] = @total_discount[ind] + discount_amt
            i = i + 1
          end
        end
      end

      unless one_time_total_amount_discount
        if onetime_discount_particulars_id.empty?
          onetime_discount_particulars_id[0] = 0
        end
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if discounts_on_particulars.length > 0
          @discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          @discounts_amount[ind] = []
          @discounts[ind].each do |d|   
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            @discounts_amount[ind][d.id] = discount_amt
            @total_discount[ind] = @total_discount[ind] + discount_amt
          end
        else  
          unless one_time_discount
            @discounts[ind] = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            @discounts_amount[ind] = []
            @discounts[ind].each do |d|
              @discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
              @total_discount[ind] = @total_discount[ind] + @discounts_amount[ind][d.id]
            end
          end
        end
      end
    end
  end
  
  def calculate_new_discount(date,batch,student,total_payable)
    total_discount = 0
    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []
    
    if MultiSchool.current_school.id == 312
      fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
      onetime_discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par| ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
      if onetime_discounts.length > 0
        onetime_discounts_amount = []
          onetime_discounts.each do |d|
            onetime_discounts_amount[d.id] = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
            total_discount = total_discount + onetime_discounts_amount[d.id]
        end
      end
    else
      one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      onetime_discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) }
      if onetime_discounts.length > 0
        one_time_total_amount_discount= true
        onetime_discounts_amount = []
        onetime_discounts.each do |d|
          onetime_discounts_amount[d.id] = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
          total_discount = total_discount + onetime_discounts_amount[d.id]
        end
      else
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        onetime_discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par| ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if onetime_discounts.length > 0
          one_time_discount = true
          onetime_discounts_amount = []
          i = 0
          onetime_discounts.each do |d|   
            onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            onetime_discounts_amount[d.id] = discount_amt
            total_discount = total_discount + discount_amt
            i = i + 1
          end
        end
      end

      unless one_time_total_amount_discount
        if onetime_discount_particulars_id.empty?
          onetime_discount_particulars_id[0] = 0
        end
        discounts_on_particulars = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) }
        if discounts_on_particulars.length > 0
          discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) }
          discounts_amount = []
          discounts.each do |d|   
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            discounts_amount[d.id] = discount_amt
            total_discount = total_discount + discount_amt
          end
        else  
          unless one_time_discount
            discounts = date.fee_discounts.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            discounts_amount = []
            discounts.each do |d|
              discounts_amount[d.id] = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
              total_discount = total_discount + discounts_amount[d.id]
            end
          end
        end
      end
    end
    return total_discount
  end
  
  def calculate_extra_fine(date,batch,student,fine_rule)
    if MultiSchool.current_school.id == 340
      #GET THE NEXT ALL months 
      extra_fine = 0
      other_months = FinanceFeeCollection.find(:all, :conditions => ["due_date > ? and is_deleted=#{false}", date.due_date], :order => "due_date asc")
      unless other_months.nil? or other_months.empty?
        other_months.each do |other_month|
          fee_for_batch = FeeCollectionBatch.find(:all, :conditions => ["batch_id = ? and is_deleted=#{false} and finance_fee_collection_id != ?", batch.id, date.id])
          unless fee_for_batch.nil? or fee_for_batch.empty?
            fine_amount = fine_rule.fine_amount if fine_rule
            extra_fine = extra_fine + fine_amount
          end
        end
      end
      @fine_amount = @fine_amount + extra_fine
    end
  end
  
  def calculate_extra_fine_index_all(date,batch,student,fine_rule,ind)
   if MultiSchool.current_school.id == 340
      #GET THE NEXT ALL months 
      extra_fine = 0
      other_months = FinanceFeeCollection.find(:all, :conditions => ["due_date > ?", date.due_date], :order => "due_date asc")
      unless other_months.nil? or other_months.empty?
        other_months.each do |other_month|
          fine_amount = fine_rule.fine_amount if fine_rule
          extra_fine = extra_fine + fine_amount
        end
      end
      @all_fine_amount[ind] = @all_fine_amount[ind] + extra_fine
    end
  end
  
  def calculate_extra_fine_index(date,batch,student,fine_rule,ind)
    if MultiSchool.current_school.id == 340
      #GET THE NEXT ALL months 
      extra_fine = 0
      other_months = FinanceFeeCollection.find(:all, :conditions => ["due_date > ?", date.due_date], :order => "due_date asc")
      unless other_months.nil? or other_months.empty?
        other_months.each do |other_month|
          fine_amount = fine_rule.fine_amount if fine_rule
          extra_fine = extra_fine + fine_amount
        end
      end
      @fine_amount[ind] = @fine_amount[ind] + extra_fine
    end
  end
  
  def get_fine_discount(date,batch,student)
    if !@fine_amount.blank? and @fine_amount > 0
      fee_collection_discount_ids = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id_and_is_late(date.id, batch.id, true).map(&:fee_discount_id)
      unless fee_collection_discount_ids.nil? or fee_collection_discount_ids.empty?
        @discounts_on_lates = FeeDiscount.find(:all, :conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{true} and id IN (" + fee_collection_discount_ids.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @discounts_on_lates.length > 0
          @has_fine_discount = true
          @discounts_late_amount = []
          @discounts_on_lates.each do |d|   
            if @fine_amount > 0
              discount_amt = @new_fine_amount * d.discount.to_f/ (d.is_amount?? @new_fine_amount : 100)
              @fine_amount = @fine_amount - discount_amt
              if @fine_amount < 0
                discount_amt = 0
              end
              @discounts_late_amount[d.id] = discount_amt
            else
              @discounts_late_amount[d.id] = 0
            end
          end
        end
      end
    else
      @fine_amount = 0
    end  
  end
  
  def get_fine_discount_index(date,batch,student,ind)
    if @fine_amount[ind] > 0
      fee_collection_discount_ids = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id_and_is_late(date.id, batch.id, true).map(&:fee_discount_id)
      unless fee_collection_discount_ids.nil? or fee_collection_discount_ids.empty?
        @discounts_on_lates[ind] = FeeDiscount.find(:all, :conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{true} and id IN (" + fee_collection_discount_ids.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @discounts_on_lates[ind].length > 0
          @has_fine_discount[ind] = true
          @discounts_late_amount[ind] = []
          @discounts_on_lates[ind].each do |d|   
            if @fine_amount[ind] > 0
              discount_amt = @new_fine_amount[ind] * d.discount.to_f/ (d.is_amount?? @new_fine_amount[ind] : 100)
              @fine_amount[ind] = @fine_amount[ind] - discount_amt
              if @fine_amount[ind] < 0
                discount_amt = 0
              end
              @discounts_late_amount[ind][d.id] = discount_amt
            else
              @discounts_late_amount[ind][d.id] = 0
            end
          end
        end
      end
    end
  end
  
  
  def get_fine_discount_index_all(date,batch,student,ind)
    
    if @all_fine_amount[ind] > 0
      fee_collection_discount_ids = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id_and_is_late(date.id, batch.id, true).map(&:fee_discount_id)
      unless fee_collection_discount_ids.nil? or fee_collection_discount_ids.empty?
        @all_discounts_on_lates[ind] = FeeDiscount.find(:all, :conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{true} and id IN (" + fee_collection_discount_ids.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @all_discounts_on_lates[ind].length > 0
          @all_has_fine_discount[ind] = true
          @all_discounts_late_amount[ind] = []
          @all_discounts_on_lates[ind].each do |d|   
            if @all_fine_amount[ind] > 0
              discount_amt = @all_new_fine_amount[ind] * d.discount.to_f/ (d.is_amount?? @all_new_fine_amount[ind] : 100)
              @all_fine_amount[ind] = @all_fine_amount[ind] - discount_amt
              if @all_fine_amount[ind] < 0
                discount_amt = 0
              end
              @all_discounts_late_amount[ind][d.id] = discount_amt
            else
              @all_discounts_late_amount[ind][d.id] = 0
            end
          end
        end
      end
    end
  end

end

