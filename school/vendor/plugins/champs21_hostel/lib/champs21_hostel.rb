require 'dispatcher'
# Champs21Hostel
module Champs21Hostel
  def self.attach_overrides
    Dispatcher.to_prepare :champs21_hostel do
      ::Employee.instance_eval { has_many :wardens, :dependent => :destroy }
      ::FinanceTransaction.instance_eval { include FinanceTransactionExtension }
      ::Student.instance_eval { include StudentExtension }
      ::Batch.instance_eval { include BatchExtension }
    end
  end
  
  def self.dependency_check(record,type)
    if type == "permanant"
      if record.class.to_s == "Student"
        return true if record.room_allocations.all(:conditions=>"is_vacated=0").present?
        return true if record.hostel_fees.active.present?
      elsif record.class.to_s == "Employee"
        return true if record.wardens.all.present?
      end
    end
    return false
  end

  def self.student_profile_fees_hook
    "hostel_fee/student_profile_fees"
  end


  module StudentExtension
    def self.included(base)
      base.instance_eval do
        has_many :room_allocations, :dependent => :destroy
        has_many :hostel_fees
      end
    end

    def current_allocation
      RoomAllocation.find_by_student_id(self.id,:conditions=>"is_vacated=0")
    end

    def hostel_fee_transactions(fee_collection)
      HostelFee.find_by_hostel_fee_collection_id_and_student_id(fee_collection.id,self.id)
    end
  
    def hostel_fee_balance(fee_collection_id)
      fee_collection= HostelFeeCollection.find(fee_collection_id)
      hostelfee = self.hostel_fee_transactions(fee_collection)
      paid_fees = hostelfee.finance_transaction unless hostelfee.finance_transaction_id.blank?
      unless paid_fees.nil?
        #      balance= hostelfee.rent.to_f - paid_fees.amount.to_f
        balance=0
      else
        balance=hostelfee.rent.to_f
      end
      return balance
    end

    def hostel_fee_collections
      HostelFeeCollection.find_all_by_batch_id(self.batch ,:joins=>'INNER JOIN hostel_fees ON hostel_fee_collections.id = hostel_fees.hostel_fee_collection_id',:conditions=>"hostel_fees.student_id = #{self.id} and hostel_fee_collections.is_deleted = 0")
    end

  end

  module BatchExtension
    def self.included(base)
      base.instance_eval do
        has_many :room_allocations, :through => :students
      end
    end

    def room_allocations_present
      flag = false
      unless self.room_allocations.blank?
        self.room_allocations.each do |room|
          flag = true unless room.is_vacated
        end
      end
      return flag
    end
  end

  module FinanceTransactionExtension
    def hosteller
      fee = self.finance
      student = fee.student
      student ||= ArchivedStudent.find_by_former_id(fee.student_id)
      student.full_name
    end
  end
end




#
