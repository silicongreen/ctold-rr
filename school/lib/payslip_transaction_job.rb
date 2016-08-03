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

class PayslipTransactionJob

  def initialize(*args)
    opts = args.extract_options!

    @employee_ids = opts[:employee_id].split(',')
    @salary_date = Date.parse(opts[:salary_date])
  end

  def perform
    @employee_ids.each do |employee_id|
      employee = Employee.find_by_id employee_id
      if employee.present?
        monthly_payslips = MonthlyPayslip.find(:all,:conditions=>["employee_id=? AND salary_date = ?", employee.id, @salary_date],:include=>:payroll_category)
        individual_payslips =  IndividualPayslipCategory.find(:all,:conditions=>["employee_id=? AND salary_date = ?", employee.id, @salary_date])
        salary  = Employee.calculate_salary(monthly_payslips, individual_payslips)

        finance_transaction=FinanceTransaction.create(
          :title => "Monthly Salary",
          :description => "Salary of #{employee.employee_number} for the month #{I18n.l(@salary_date, :format=>:month_year)}",
          :amount => salary[:net_amount],
          :category_id => FinanceTransactionCategory.find_by_name('Salary').id,
          :transaction_date => Date.today,
          :payee => employee
        )
         monthly_payslips.each{|payslip| payslip.update_attributes(:finance_transaction_id=>finance_transaction.id) }
      end
    end
  end

end
