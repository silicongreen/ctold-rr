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

class FinanceTransactionCategory < ActiveRecord::Base
  has_many :finance_transactions,:class_name => 'FinanceTransaction', :foreign_key => 'category_id'
  has_one  :trigger, :class_name => "FinanceTransactionTrigger", :foreign_key => "category_id"


  validates_presence_of :name
  validates_uniqueness_of  :name, :case_sensitive => false, :scope => :deleted, :if => 'deleted == false'

  named_scope :expense_categories, :conditions => "is_income = false AND name NOT LIKE 'Salary'and deleted = 0"
  # named_scope :income_categories, :conditions => "is_income = true AND name NOT IN ('Fee','Salary','Donation','Library','Hostel','Transport') and deleted = 0"

  #  def self.expense_categories
  #    FinanceTransactionCategory.all(:conditions => "is_income = false AND name NOT LIKE 'Salary'")
  #  end
  #
  #  def self.income_categories
  #    FinanceTransactionCategory.all(:conditions => "is_income = true AND name NOT LIKE 'Fee' AND name NOT LIKE 'Donation'")
  #  end

  def self.income_categories
    cat_names = ["'Fee'","'Salary'","'Donation'"]
    Champs21Plugin::FINANCE_CATEGORY.each do |category|
      cat_names << "'#{category[:category_name]}'"
    end
    self.find(:all,:conditions=>"is_income = true AND name NOT IN (#{cat_names.join(',')}) and deleted = 0")
  end

  def is_fixed
    cat_names = ['fee','salary','donation']
    Champs21Plugin::FINANCE_CATEGORY.each do |category|
      cat_names << "#{category[:category_name].downcase}"
    end
    return true if cat_names.include?(self.name.downcase)
    return false
  end

  def total_income(start_date,end_date)
    if is_income
      self.finance_transactions.sum('amount',:conditions => ["transaction_date >= '#{start_date}' and transaction_date <= '#{end_date}' and master_transaction_id=0"])
    else
      0
    end
  end

  def total_expense(start_date,end_date)
    if is_income
      self.finance_transactions.sum('amount',:conditions => ["transaction_date >= '#{start_date}' and transaction_date <= '#{end_date}' and master_transaction_id!=0"])
    else
      self.finance_transactions.sum('amount',:conditions => ["transaction_date >= '#{start_date}' and transaction_date <= '#{end_date}'"])
    end
  end

end
