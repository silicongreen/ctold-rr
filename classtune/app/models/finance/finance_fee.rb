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

class FinanceFee < ActiveRecord::Base
  
  belongs_to :finance_fee_collection ,:foreign_key => 'fee_collection_id'
  has_many   :finance_transactions ,:as=>:finance
  has_many   :cancelled_finance_transactions ,:as=>:finance
  has_many   :components, :class_name => 'FinanceFeeComponent', :foreign_key => 'fee_id'
  belongs_to :student
  belongs_to :batch
  has_many   :finance_transactions,:through=>:fee_transactions
  has_many   :fee_transactions
  has_many   :fees_advances, :foreign_key => 'fee_id'
  has_one    :fee_refund
  named_scope :active , :joins=>[:finance_fee_collection] ,:conditions=>{:finance_fee_collections=>{:is_deleted=>false}}

  def check_transaction_done
    unless self.transaction_id.nil?
      return true
    else
      return false
    end
  end

  def former_student
    ArchivedStudent.find_by_former_id(self.student_id)
  end
  
  def due_date
    finance_fee_collection.due_date.strftime "%a,%d %b %Y"
  end

  def payee_name
    if student
      "#{student.full_name} - #{student.admission_no}"
    elsif former_student
      "#{former_student.full_name} - #{former_student.admission_no}"
    else
      "#{t('user_deleted')}"
    end
  end

  def self.new_student_fee(date,student)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id(student.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_tmp=#{false} and is_deleted=#{false} and batch_id=#{student.batch_id}").select{|par| (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
    total_payable = fee_particulars.map{|s| s.amount}.sum.to_f
    
    discounts_on_particulars=date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
    if discounts_on_particulars.length > 0
      total_discount = 0
      discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      discounts.each do |d|   
        fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_tmp=#{false} and is_deleted=#{false} and batch_id=#{student.batch_id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
          payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
          discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
          total_discount = total_discount + discount_amt
        end
      end
    else  
      total_discount = 0
      discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      discounts.each do |d|
        discounts_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
        total_discount = total_discount + discounts_amount
      end
    end
    balance=Champs21Precision.set_and_modify_precision(total_payable-total_discount)
    FinanceFee.create(:student_id => student.id,:fee_collection_id => date.id,:balance=>balance,:batch_id=>student.batch_id)
  end
  
  def self.new_student_fee_advance(date,student,no_of_month, particular_id, advance_id)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id(s.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    if particular_id.to_i == 0
      fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_tmp=#{false} and is_deleted=#{false} and batch_id=#{student.batch_id}").select{|par| (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      total_payable = fee_particulars.map{|s| s.amount}.sum.to_f
    else
      fee_particulars = date.finance_fee_particulars.all(:conditions=>"finance_fee_particular_category_id = #{particular_id.to_i} and is_tmp=#{false} and is_deleted=#{false} and batch_id=#{student.batch_id}").select{|par| (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      total_payable = fee_particulars.map{|s| s.amount}.sum.to_f
    end
    
    total_payable = no_of_month.to_i * total_payable
    total_discount = 0
    
    if particular_id.to_i == 0
      discounts_on_particulars=date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      if discounts_on_particulars.length > 0
        discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        discounts.each do |d|   
          fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_tmp=#{false} and is_deleted=#{false} and batch_id=#{student.batch_id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            payable_ampt = payable_ampt * no_of_month.to_i
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            total_discount = total_discount + discount_amt
          end
        end
      else  
        discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        discounts.each do |d|
          discounts_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
          total_discount = total_discount + discounts_amount
        end
      end
    else
      discounts_on_particulars=date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = '#{particular_id.to_i}'").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
      if discounts_on_particulars.length > 0
        discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = '#{particular_id.to_i}'").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        discounts.each do |d|   
          fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_tmp=#{false} and is_deleted=#{false} and batch_id=#{student.batch_id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            payable_ampt = payable_ampt * no_of_month.to_i
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            total_discount = total_discount + discount_amt
          end
        end
      else  
        discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        discounts.each do |d|
          discounts_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
          total_discount = total_discount + discounts_amount
        end  
      end
    end
    #abort(total_discount.inspect)
    balance=Champs21Precision.set_and_modify_precision(total_payable-total_discount)
    finance_fee = FinanceFee.new
    finance_fee.student_id = student.id
    finance_fee.fee_collection_id = date.id
    finance_fee.balance = balance
    finance_fee.batch_id = student.batch_id
    finance_fee.has_advance_fee_id = true
    finance_fee.save
    
    fee_advance = FeesAdvance.new
    fee_advance.advance_fee_id = advance_id.to_i
    fee_advance.fee_id = finance_fee.id
    fee_advance.save
    #FinanceFee.create(:student_id => student.id,:fee_collection_id => date.id,:balance=>balance,:batch_id=>student.batch_id, :advance_fee_id => advance_id.to_i)
  end
  
  def self.new_student_fee_with_tmp_particular(date,student)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id(student.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{student.batch_id}").select{|par| (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
    total_payable = fee_particulars.map{|s| s.amount}.sum.to_f
    total_discount = 0
    batch = Batch.find(student.batch_id)
    onetime_discount_particulars_id = []
    one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
    onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
    if onetime_discounts.length > 0
      one_time_total_amount_discount= true
      onetime_discounts_amount = []
      onetime_discounts.each do |d|
        onetime_discounts_amount[d.id] = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
        total_discount = total_discount + onetime_discounts_amount[d.id]
      end
    else
      fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
      onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
      if onetime_discounts.length > 0
        one_time_discount = true
        onetime_discounts_amount = []
        i = 0
        onetime_discounts.each do |d|   
          onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
          fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            onetime_discounts_amount[d.id] = discount_amt
            total_discount = total_discount + discount_amt
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
      discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
      if discounts_on_particulars.length > 0
        discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        discounts_amount = []
        discounts.each do |d|   
          fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            discounts_amount[d.id] = discount_amt
            total_discount = total_discount + discount_amt
          end
        end
      else  
        unless one_time_discount
          discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          discounts_amount = []
          discounts.each do |d|
            discounts_amount[d.id] = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
            total_discount = total_discount + discounts_amount[d.id]
          end
        end
      end
    end
    
    balance=Champs21Precision.set_and_modify_precision(total_payable-total_discount)
    fee = FinanceFee.find_by_student_id_and_fee_collection_id_and_batch_id_and_is_paid(student.id, date.id, student.batch_id, false)
    unless fee.blank?
      paid_fees = fee.finance_transactions
      unless paid_fees.blank?
        paid_fees_amount = paid_fees.map(&:amount).sum
        bal = balance.to_f - paid_fees_amount.to_f
      else
        bal = balance.to_f
      end
      fee.update_attributes(:balance=>bal)
    else
      fee_paid = FinanceFee.find_by_student_id_and_fee_collection_id_and_batch_id(student.id, date.id, student.batch_id)
      paid_fees = fee_paid.finance_transactions
      unless paid_fees.blank?
        paid_fees_amount = paid_fees.map(&:amount).sum
        bal = balance.to_f - paid_fees_amount.to_f
      else
        bal = balance.to_f
      end
      bal = 0 if bal.to_i < 0
      # abort(paid_fees_amount)
      # revised_balance = 0
      # fee_particulars.each do |fv|
      #   if fv.is_tmp
      #     revised_balance = revised_balance.to_f + fv.amount.to_f
      #   end
      #
      # end
      fee_paid.update_attributes(:is_paid=>0,:balance=>bal)
    end
  end
  
  def self.calculate_discount_new(total_payable, date,batch,student,is_advance_fee_collection,advance_fee,fee_has_advance_particular)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id(student.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    @total_payable = total_payable
    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []
    advance_fee_particular = []
    unless advance_fee.nil? or advance_fee.empty? or advance_fee.blank?
      advance_fee_particular = advance_fee.map(&:particular_id)
    end
    
    if MultiSchool.current_school.id == 312
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee_particular.include?(0))
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)) }
        @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        @discounts_amount = []
          @discounts.each do |d|
            @discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount = @total_discount + @discounts_amount[d.id]
        end
      else
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)) }
        @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        @discounts_amount = []
          @discounts.each do |d|
            @discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount = @total_discount + @discounts_amount[d.id]
        end
      end
    else
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee_particular.include?(0))
        deduct_fee = 0
        if fee_has_advance_particular and !advance_fee_particular.include?(0)
          unless advance_fee_particular.blank?
            particular_id = advance_fee_particular.join(",")
            fee_particulars_deduct = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{particular_id})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
            deduct_fee = fee_particulars_deduct.map{|l| l.amount}.sum.to_f
          end
        end
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @onetime_discounts.length > 0
          one_time_total_amount_discount= true
          @onetime_discounts_amount = []
          @onetime_discounts.each do |d|
            @onetime_discounts_amount[d.id] = (@total_payable - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount = @total_discount + @onetime_discounts_amount[d.id]
          end
        else
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          @onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
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
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
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
              
              deduct_fee = 0
              if fee_has_advance_particular and !advance_fee_particular.include?(0)
                unless advance_fee_particular.blank?
                  particular_id = advance_fee_particular.join(",")
                  fee_particulars_deduct = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{particular_id})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
                  deduct_fee = fee_particulars_deduct.map{|l| l.amount}.sum.to_f
                end
              end
              @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              @discounts_amount = []
              @discounts.each do |d|
                @discounts_amount[d.id] = (@total_payable - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
                @total_discount = @total_discount + @discounts_amount[d.id]
              end
            end
          end
        end
      else
        one_time_total_amount_discount= false
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        
        if @onetime_discounts.length > 0
          one_time_total_amount_discount= true
          @onetime_discounts_amount = []
          @onetime_discounts.each do |d|
            @onetime_discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount = @total_discount + @onetime_discounts_amount[d.id]
          end
        end

        unless one_time_total_amount_discount
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id IN (#{advance_fee_particular.join(",")})").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @discounts_amount = []
            @discounts.each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                month = 1
                unless advance_fee.nil?
                  advance_fee.each do |fee|
                    if fee.particular_id == fee_particulars_single.finance_fee_particular_category_id
                        month = fee.no_of_month.to_i
                    end
                  end
                end
                payable_ampt = (fee_particulars_single.map{|l| l.amount}.sum.to_f * month.to_i).to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @discounts_amount[d.id] = discount_amt
                @total_discount = @total_discount + discount_amt
              end
            end
          else  
            unless one_time_discount
              deduct_fee = 0
              @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              @discounts_amount = []
              @discounts.each do |d|
                @discounts_amount[d.id] = (@total_payable - deduct_fee) * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
                @total_discount = @total_discount + @discounts_amount[d.id]
              end
            end  
          end
        end
      end
    end
  end
  
  def self.update_student_fee(date, s, fee)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id(s.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{s.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
    
    total_payable=fee_particulars.map{|st| st.amount}.sum.to_f
    total_discount = 0

    if MultiSchool.current_school.id == 312
      discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_late=#{false}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
      discounts.each do |d|
        discounts_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
        total_discount = total_discount + discounts_amount
      end
    else  
      one_time_discount = false
      one_time_total_amount_discount = false
      onetime_discount_particulars_id = []
      
      onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }

      if onetime_discounts.length > 0
        onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
        if onetime_discounts.length > 0
          one_time_total_amount_discount= true
          onetime_discounts.each do |d|

            onetime_discounts_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
            total_discount = total_discount + onetime_discounts_amount
          end
        end
      else
        onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
        i = 0
        if onetime_discounts.length > 0
          one_time_discount = true
          onetime_discounts.each do |d|   
            onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{s.batch_id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
            unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
              payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              total_discount = total_discount + discount_amt
              i = i + 1
            end
          end
        end
      end

      unless one_time_total_amount_discount

        if onetime_discount_particulars_id.empty?
          onetime_discount_particulars_id[0] = 0
        end
        discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
        if discounts_on_particulars.length > 0
          discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }

          discounts.each do |d|   
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{s.batch_id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
            unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
              payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              total_discount = total_discount + discount_amt
            end
          end
        else  
          unless one_time_discount
            discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
            discounts.each do |d|
              discounts_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
              total_discount = total_discount + discounts_amount
            end
          end
        end
      end
    end
    
    bal = ( total_payable - total_discount )
    finance_fee = FinanceFee.find_by_id_and_is_paid(fee.id, false)
    finance_fee.update_attributes(:balance=>bal)
  end
  
  def self.get_student_balance(date, s, fee)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id(s.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{fee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==fee.batch) }
    
    total_payable=fee_particulars.map{|st| st.amount}.sum.to_f
    total_discount = 0

    if MultiSchool.current_school.id == 312
      discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{fee.batch_id} and is_late=#{false}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==fee.batch) }
      discounts.each do |d|
        discounts_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
        total_discount = total_discount + discounts_amount
      end
    else  
      one_time_discount = false
      one_time_total_amount_discount = false
      onetime_discount_particulars_id = []
      
      onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{fee.batch_id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==s or par.receiver==s.student_category or par.receiver==fee.batch) }

      if onetime_discounts.length > 0
        onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{fee.batch_id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==fee.batch) }
        if onetime_discounts.length > 0
          one_time_total_amount_discount= true
          onetime_discounts.each do |d|

            onetime_discounts_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
            total_discount = total_discount + onetime_discounts_amount
          end
        end
      else
        onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{fee.batch_id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==fee.batch) }
        i = 0
        if onetime_discounts.length > 0
          one_time_discount = true
          onetime_discounts.each do |d|   
            onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{fee.batch_id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==s or par.receiver==s.student_category or par.receiver==fee.batch) }
            unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
              payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              total_discount = total_discount + discount_amt
              i = i + 1
            end
          end
        end
      end

      unless one_time_total_amount_discount

        if onetime_discount_particulars_id.empty?
          onetime_discount_particulars_id[0] = 0
        end
        discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{fee.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| (par.receiver==s or par.receiver==s.student_category or par.receiver==fee.batch) }
        if discounts_on_particulars.length > 0
          discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{fee.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==fee.batch) }

          discounts.each do |d|   
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{fee.batch_id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==s or par.receiver==s.student_category or par.receiver==fee.batch) }
            unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
              payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              total_discount = total_discount + discount_amt
            end
          end
        else  
          unless one_time_discount
            discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{fee.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==fee.batch) }
            discounts.each do |d|
              discounts_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
              total_discount = total_discount + discounts_amount
            end
          end
        end
      end
    end
    
    bal = ( total_payable - total_discount )
    bal
  end
  
  def self.check_update_student_fee(date, s, fee)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id(s.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{s.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
    total_payable=fee_particulars.map{|st| st.amount}.sum.to_f
    total_discount = 0

    if MultiSchool.current_school.id == 312
      discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_late=#{false}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
      discounts.each do |d|
        discounts_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
        total_discount = total_discount + discounts_amount
      end
    else  
      one_time_discount = false
      one_time_total_amount_discount = false
      onetime_discount_particulars_id = []

      onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }

      if onetime_discounts.length > 0
        onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
        if onetime_discounts.length > 0
          one_time_total_amount_discount= true
          onetime_discounts.each do |d|

            onetime_discounts_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
            total_discount = total_discount + onetime_discounts_amount
          end
        end
      else
        onetime_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
        i = 0
        if onetime_discounts.length > 0
          one_time_discount = true
          onetime_discounts.each do |d|   
            onetime_discount_particulars_id[i] = d.finance_fee_particular_category_id
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{s.batch_id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
            unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
              payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              total_discount = total_discount + discount_amt
              i = i + 1
            end
          end
        end
      end

      unless one_time_total_amount_discount

        if onetime_discount_particulars_id.empty?
          onetime_discount_particulars_id[0] = 0
        end
        discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
        if discounts_on_particulars.length > 0
          discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }

          discounts.each do |d|   
            fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{s.batch_id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
            unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
              payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              total_discount = total_discount + discount_amt
            end
          end
        else  
          unless one_time_discount
            discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{s.batch_id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==s.batch) }
            discounts.each do |d|
              discounts_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
              total_discount = total_discount + discounts_amount
            end
          end
        end
      end
    end
    
    bal = ( total_payable - total_discount )
    bal
  end
  
end
