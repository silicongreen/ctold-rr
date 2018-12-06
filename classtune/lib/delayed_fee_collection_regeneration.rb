require 'i18n'
class DelayedFeeCollectionRegeneration
  attr_accessor :user,:collection,:fee_collection
  def initialize(fee_collection_id, new_student_only, skip_paid_student, skip_new_student, user, sent_remainder)
    @fee_collection_id = fee_collection_id
    @new_student_only = new_student_only
    @skip_paid_student = skip_paid_student
    @skip_new_student = skip_new_student
    @user = user
    @sent_remainder = sent_remainder
  end
  include I18n
  def t(obj)
    I18n.t(obj)
  end
  def perform
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
              @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
            end

            if advance_fee_collection
              fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
              if fee_collection_advances_particular.include?(0)
                @fee_particulars = @fee_category.fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==batch) }
                @finance_fee_particulars = @finance_fee_collection.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==s or par.receiver==s.student_category or par.receiver==batch) }.map(&:id)
                
              else
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
              FinanceFee.calculate_discount_new(@finance_fee_collection, batch, s, true, @fee_collection_advances, @fee_has_advance_particular)
            else
              if @fee_has_advance_particular
                FinanceFee.calculate_discount_new(@finance_fee_collection, batch, s, false, @fee_collection_advances, @fee_has_advance_particular)
              else
                FinanceFee.calculate_discount_new(@finance_fee_collection, batch, s, false, nil, @fee_has_advance_particular)
              end
            end
            
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
    
    
    
  end

end

