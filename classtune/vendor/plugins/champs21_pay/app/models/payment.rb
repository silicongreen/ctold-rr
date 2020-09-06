class Payment < ActiveRecord::Base
  belongs_to :payment, :polymorphic => true
  belongs_to :payee, :polymorphic => true
  belongs_to :finance_transaction

  serialize :gateway_response
  serialize :validation_response

  before_save :remove_other
  
#  before_create :change_table
#  before_save :change_table
#  before_update :change_table
  
  after_initialize do |payment|
    unless MultiSchool.current_school.nil?
      if MultiSchool.current_school.id != 352
        self.table_name = MultiSchool.current_school.code + "_payments"
      else
        self.table_name = "payments"  
      end
    end
  end
  
#  def before_create
#    if payment_type == "FinanceFee"
##      if Payment.find_by_payee_id_and_payment_id_and_payment_type(payee_id,payment_id,'FinanceFee').present?
##        false
##      else
#        true
##      end
#    elsif payment_type == "Application"
#      if Payment.find_by_payee_id(payee_id).present?
#        false
#      else
#        true
#      end
#    end
#  end

  def remove_other
     payments = Payment.find(:all, :conditions => "gateway_txt != '#{gateway_txt}' and order_id = '#{order_id}' and payment_id = '#{payment_id}' and payee_id = '#{payee_id}'")
     unless payments.blank?
	payments.each do |p|
	    p.destroy
	end
     end
  end

  def payee_name
    if payee.nil?
      if payee_type == 'Student'
        ArchivedStudent.find_by_former_id(payee_id).try(:full_name) || "NA"
      elsif payee_type == 'Guardian'
        ArchivedGuardian.find_by_former_id(payee_id).try(:full_name) || "NA"
      elsif payee_type == 'Applicant'
        "NA"
      end
    else
      payee.full_name
    end
  end

  def payee_admission_no
    if payee.nil?
      if payee_type == 'Student'
        ArchivedStudent.find_by_former_id(payee_id).try(:full_name) || "NA"
      elsif payee_type == 'Guardian'
        ArchivedGuardian.find_by_former_id(payee_id).try(:full_name) || "NA"
      elsif payee_type == 'Applicant'
        "NA"
      end
    else
      payee.admission_no
    end
  end

  def payee_roll_no 
    if payee.nil?
      if payee_type == 'Student'
        ArchivedStudent.find_by_former_id(payee_id).try(:full_name) || "NA"
      elsif payee_type == 'Guardian'
        ArchivedGuardian.find_by_former_id(payee_id).try(:full_name) || "NA"
      elsif payee_type == 'Applicant'
        "NA"
      end
    else
      payee.class_roll_no
    end
  end

  def payee_batch_full_name
    if payee.nil?
      if payee_type == 'Student'
        ArchivedStudent.find_by_former_id(payee_id).try(:full_name) || "NA"
      elsif payee_type == 'Guardian'
        ArchivedGuardian.find_by_former_id(payee_id).try(:full_name) || "NA"
      elsif payee_type == 'Applicant'
        "NA"
      end
    else
      payee.batch.full_name
    end
  end

  def payee_user
    if payee.nil?
      if payee_type == 'Student'
        ArchivedStudent.find_by_former_id(payee_id).try(:admission_no) || "NA"
      elsif payee_type == 'Guardian'
        ArchivedGuardian.find_by_former_id(payee_id).try(:user).try(:username) || "NA"
      elsif payee_type == 'Applicant'
        "NA"
      end
    else
      payee_type == 'Applicant' ? payee.try(:reg_no) : payee.try(:user).try(:username)
    end
  end
  
  def set_ledger
    
    unless finance_transaction_id.nil? 
      unless finance_transaction_id.blank?
        finance_transaction = FinanceTransaction.find(:first, :conditions => "id = #{finance_transaction_id}")
        unless finance_transaction.nil?
          student_fee_ledger = StudentFeeLedger.new
          student_fee_ledger.student_id = payee_id
          student_fee_ledger.ledger_date = finance_transaction.transaction_date
          student_fee_ledger.amount_paid = finance_transaction.amount.to_f
          student_fee_ledger.fee_id = payment_id
          student_fee_ledger.transaction_id = finance_transaction.id
          student_fee_ledger.save
        end
      end
    end
  end
  
  def update_ledger
    unless finance_transaction_id.nil? 
      unless finance_transaction_id.blank?
        finance_transaction = FinanceTransaction.find(:first, :conditions => "id = #{finance_transaction_id}")
        unless finance_transaction.blank?
          student_fee_ledgers = StudentFeeLedger.find(:all, :conditions => "student_id = #{payee_id} and transaction_id = #{finance_transaction.id} and is_fine = #{false}")
          unless student_fee_ledgers.blank?
            student_fee_ledgers.each do |fee_ledger|
              student_fee_ledger = StudentFeeLedger.find(fee_ledger.id)
                student_fee_ledger.update_attributes(:amount_paid => finance_transaction.amount.to_f)
            end
          else
            student_fee_ledger = StudentFeeLedger.new
            student_fee_ledger.student_id = payee_id
            unless transaction_datetime.blank?
              student_fee_ledger.ledger_date = transaction_datetime.strftime("%Y-%m-%d")
            else
              student_fee_ledger.ledger_date = finance_transaction.transaction_date
            end
            student_fee_ledger.amount_paid = finance_transaction.amount.to_f
            student_fee_ledger.fee_id = payment_id
            student_fee_ledger.transaction_id = finance_transaction.id
            student_fee_ledger.save
          end
        end
      end
    end
  end
end
