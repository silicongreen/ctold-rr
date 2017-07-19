require 'dispatcher'
# Champs21Transport
module Champs21Transport
  def self.attach_overrides
    Dispatcher.to_prepare :champs21_transport do
      ::Employee.instance_eval { has_many :transport_fees, :as => 'receiver' }
      ::Employee.instance_eval { has_one:transport, :as => 'receiver', :dependent => :destroy }
      ::Batch.instance_eval { has_many :transports, :through=>:students }
      ::Batch.class_eval {
        def active_transports
          transports.find(:all,:include => :vehicle, :conditions => ["vehicles.status = ?","Active"])
        end
      }
      ::Student.instance_eval { include  StudentExtension }
      ::FinanceTransaction.instance_eval { include FinanceTransactionExtension }
    end
  end
  
  def self.dependency_check(record,type)
    if type == "permanant"
      if record.class.to_s == "Student" or record.class.to_s == "Employee"
        return true if record.transport.present?
        return true if record.transport_fees.active.present?
      end
    end
    return false
  end
  def self.student_profile_fees_hook
    "transport_fee/student_profile_fees"
  end


  module StudentExtension
    def self.included(base)
      base.instance_eval do
        has_many :transport_fees, :as => 'receiver'
        has_one :transport, :as => 'receiver', :dependent => :destroy
      end
    end

    def transport_fee_collections
      TransportFeeCollection.find_all_by_batch_id(self.batch ,:joins=>'INNER JOIN transport_fees ON transport_fee_collections.id = transport_fees.transport_fee_collection_id',:conditions=>"transport_fees.receiver_id = #{self.id} and transport_fee_collections.is_deleted = 0")
    end

    def transport_fee_balance(fee_collection_id)
      fee_collection= TransportFeeCollection.find(fee_collection_id)
      transportfee = self.transport_fee_transactions(fee_collection)
      paid_fees = transportfee.finance_transaction unless transportfee.transaction_id.blank?
      unless paid_fees.nil?
        #      balance= hostelfee.rent.to_f - paid_fees.amount.to_f
        balance=0
      else
        balance=transportfee.bus_fare.to_f
      end
      return balance
    end

    def transport_fee_transactions(fee_collection)
      TransportFee.find_by_transport_fee_collection_id_and_receiver_id(fee_collection.id,self.id)
    end

  end


  module FinanceTransactionExtension
    def transport_student
      student = self.finance.receiver
      student ||= ArchivedStudent.find_by_former_id(self.finance.receiver_id)
      "#{student.full_name}- &#x200E;(#{student.batch.full_name})&#x200E;"
    end

    def transport_employee
      employee = self.finance.receiver
      employee ||= ArchivedEmployee.find_by_former_id(self.finance.receiver_id)
      "#{employee.full_name}"
    end
  end
end
#
