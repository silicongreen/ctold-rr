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

class FinanceDonation < ActiveRecord::Base
  belongs_to :transaction, :class_name => 'FinanceTransaction',  :dependent => :destroy
  validates_presence_of :donor, :amount
  validates_numericality_of :amount, :greater_than_or_equal_to => 0, :message =>:must_be_positive

  before_create :create_finance_transaction
  before_save :verify_precision

  def verify_precision
    self.amount = Champs21Precision.set_and_modify_precision self.amount
  end

  def create_finance_transaction
    transaction = FinanceTransaction.create(
      :title => "#{t('donation_from')}" + donor,
      :description => description,
      :amount => amount,
      :transaction_date => transaction_date,
      :category => FinanceTransactionCategory.find_by_name('Donation')
    )
    self.transaction_id = transaction.id
  end
end
