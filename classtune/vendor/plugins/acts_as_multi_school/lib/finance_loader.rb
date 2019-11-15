module FinanceLoader

  def self.included(base)
#    base.send :before_filter, :calculate_discount
#    base.send :before_filter, :calculate_discount_index_all
#    base.send :before_filter, :calculate_discount_index
#    base.send :before_filter, :calculate_extra_fine
#    base.send :before_filter, :calculate_extra_fine_index_all
#    base.send :before_filter, :calculate_extra_fine_index
#    base.send :before_filter, :get_fine_discount
#    base.send :before_filter, :get_fine_discount_index
#    base.send :before_filter, :get_fine_discount_index_all
  end

  private
  
  def calculate_discount(date,batch,student,is_advance_fee_collection,advance_fee,fee_has_advance_particular)
    financefee = student.finance_fee_by_date date
    batch = financefee.batch
    
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id_and_fee_collection_id(student.id,date.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
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
        exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
        if exclude_discount_ids.blank?
          exclude_discount_ids[0] = 0
        end
        @discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        @discounts_amount = []
        @discounts.each do |d|
          @discounts_amount[d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
          @total_discount = @total_discount + @discounts_amount[d.id]
        end
      else
        advance_fee_particular = advance_fee_particular.reject { |a| a.to_s.empty? }
        if advance_fee_particular.blank?
          advance_fee_particular[0] = 0
        end
        exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
        if exclude_discount_ids.blank?
          exclude_discount_ids[0] = 0
        end
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
        exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
        if exclude_discount_ids.blank?
          exclude_discount_ids[0] = 0
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
          exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
          if exclude_discount_ids.blank?
            exclude_discount_ids[0] = 0
          end
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
          exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
          if exclude_discount_ids.blank?
            exclude_discount_ids[0] = 0
          end
          onetime_discount_particulars_id = onetime_discount_particulars_id.reject { |o| o.to_s.empty? }
          if onetime_discount_particulars_id.blank?
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
              exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
              if exclude_discount_ids.blank?
                exclude_discount_ids[0] = 0
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
        exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
        if exclude_discount_ids.blank?
          exclude_discount_ids[0] = 0
        end
        advance_fee_particular = advance_fee_particular.reject { |a| a.to_s.empty? }
        if advance_fee_particular.blank?
          advance_fee_particular[0] = 0
        end
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
          exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
          if exclude_discount_ids.blank?
            exclude_discount_ids[0] = 0
          end
          advance_fee_particular = advance_fee_particular.reject { |a| a.to_s.empty? }
          if advance_fee_particular.blank?
            advance_fee_particular[0] = 0
          end
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
            exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
            if exclude_discount_ids.blank?
              exclude_discount_ids[0] = 0
            end
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
    #abort(@onetime_discounts.inspect)
  end
  
  def calculate_discount_index_all(date,batch,student,ind,is_advance_fee_collection,advance_fee,fee_has_advance_particular)
    financefee = student.finance_fee_by_date date
    batch = financefee.batch
    
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id_and_fee_collection_id(student.id,date.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []
    
    if MultiSchool.current_school.id == 312
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee.particular_id.to_i == 0)
        exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
        if exclude_discount_ids.blank?
          exclude_discount_ids[0] = 0
        end
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if @all_onetime_discounts[ind].length > 0
          @all_onetime_discounts_amount[ind] = []
          @all_onetime_discounts[ind].each do |d|
            @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
            @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
          end
        end
      else
        exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
        if exclude_discount_ids.blank?
          exclude_discount_ids[0] = 0
        end
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if @all_onetime_discounts[ind].length > 0
          @all_onetime_discounts_amount[ind] = []
          @all_onetime_discounts[ind].each do |d|
            @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
            @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
          end
        end
      end
    else
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee.particular_id.to_i == 0)
        exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
        if exclude_discount_ids.blank?
          exclude_discount_ids[0] = 0
        end
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @all_onetime_discounts[ind].length > 0
          one_time_total_amount_discount= true
          @all_onetime_discounts_amount[ind] = []
          @all_onetime_discounts[ind].each do |d|
            @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
            @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
          end
        else
          exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
          if exclude_discount_ids.blank?
            exclude_discount_ids[0] = 0
          end
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
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
          exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
          if exclude_discount_ids.blank?
            exclude_discount_ids[0] = 0
          end
          onetime_discount_particulars_id = onetime_discount_particulars_id.reject { |o| o.to_s.empty? }
          if onetime_discount_particulars_id.blank?
            onetime_discount_particulars_id[0] = 0
          end
          
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)}
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @all_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
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
              exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
              if exclude_discount_ids.blank?
                exclude_discount_ids[0] = 0
              end
              @all_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              @all_discounts_amount[ind] = []
              @all_discounts[ind].each do |d|
                @all_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
                @all_total_discount[ind] = @all_total_discount[ind] + @all_discounts_amount[ind][d.id]
              end
            end
          end
        end
      else
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee.discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
        if exclude_discount_ids.blank?
          exclude_discount_ids[0] = 0
        end
        @all_onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @all_onetime_discounts[ind].length > 0
          one_time_total_amount_discount= true
          @all_onetime_discounts_amount[ind] = []
          @all_onetime_discounts[ind].each do |d|
            @all_onetime_discounts_amount[ind][d.id] = @all_total_payable[ind] * d.discount.to_f/ (d.is_amount?? @all_total_payable[ind] : 100)
            @all_total_discount[ind] = @all_total_discount[ind] + @all_onetime_discounts_amount[ind][d.id]
          end
        end

        unless one_time_total_amount_discount
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch)}
          exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
          if exclude_discount_ids.blank?
            exclude_discount_ids[0] = 0
          end
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @all_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @all_discounts_amount[ind] = []
            @all_discounts[ind].each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              unless fee_particulars_single.nil? or fee_particulars_single.empty? or fee_particulars_single.blank?
                payable_ampt = (fee_particulars_single.map{|l| l.amount}.sum.to_f * advance_fee.no_of_month.to_i).to_f
                discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
                @all_discounts_amount[ind][d.id] = discount_amt
                @all_total_discount[ind] = @all_total_discount[ind] + discount_amt
              end
            end
          end
        end
      end
    end
  end
  
  def calculate_discount_index(date,batch,student,ind,is_advance_fee_collection,advance_fee,fee_has_advance_particular)
    financefee = student.finance_fee_by_date date
    batch = financefee.batch
    
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id_and_fee_collection_id(student.id,date.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    one_time_discount = false
    one_time_total_amount_discount = false
    onetime_discount_particulars_id = []
    
    if MultiSchool.current_school.id == 312
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee.particular_id.to_i == 0)
        exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
        if exclude_discount_ids.blank?
          exclude_discount_ids[0] = 0
        end
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if @onetime_discounts[ind].length > 0
          @onetime_discounts_amount[ind] = []
          @onetime_discounts[ind].each do |d|
            @onetime_discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
          end
        end
      else
        exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
        if exclude_discount_ids.blank?
          exclude_discount_ids[0] = 0
        end
        fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
        @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_late=#{false}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
        if @onetime_discounts[ind].length > 0
          @onetime_discounts_amount[ind] = []
          @onetime_discounts[ind].each do |d|
            @onetime_discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
          end
        end
      end
    else
      if is_advance_fee_collection == false or (is_advance_fee_collection && advance_fee.particular_id.to_i == 0)
        exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
        if exclude_discount_ids.blank?
          exclude_discount_ids[0] = 0
        end
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @onetime_discounts[ind].length > 0
          one_time_total_amount_discount= true
          @onetime_discounts_amount[ind] = []
          @onetime_discounts[ind].each do |d|
            @onetime_discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
          end
        else
          exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
          if exclude_discount_ids.blank?
            exclude_discount_ids[0] = 0
          end
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
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
          exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
          if exclude_discount_ids.blank?
            exclude_discount_ids[0] = 0
          end
          onetime_discount_particulars_id = onetime_discount_particulars_id.reject { |o| o.to_s.empty? }
          if onetime_discount_particulars_id.blank?
            onetime_discount_particulars_id[0] = 0
          end
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id > 0 and fee_discounts.finance_fee_particular_category_id NOT IN (" + onetime_discount_particulars_id.join(",") + ")").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
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
              exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
              if exclude_discount_ids.blank?
                exclude_discount_ids[0] = 0
              end
              @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = 0").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              @discounts_amount[ind] = []
              @discounts[ind].each do |d|
                @discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
                @total_discount[ind] = @total_discount[ind] + @discounts_amount[ind][d.id]
              end
            end
          end
        end
      else
        exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
        if exclude_discount_ids.blank?
          exclude_discount_ids[0] = 0
        end
        one_time_discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        @onetime_discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{true} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
        if @onetime_discounts[ind].length > 0
          one_time_total_amount_discount= true
          @onetime_discounts_amount[ind] = []
          @onetime_discounts[ind].each do |d|
            @onetime_discounts_amount[ind][d.id] = @total_payable * d.discount.to_f/ (d.is_amount?? @total_payable : 100)
            @total_discount[ind] = @total_discount[ind] + @onetime_discounts_amount[ind][d.id]
          end
        end

        unless one_time_total_amount_discount
          exclude_discount_ids = exclude_discount_ids.reject { |e| e.to_s.empty? }
          if exclude_discount_ids.blank?
            exclude_discount_ids[0] = 0
          end
          fee_particulars = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  (par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==batch) }
          discounts_on_particulars = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par| ((par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
          if discounts_on_particulars.length > 0
            @discounts[ind] = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{batch.id} and is_onetime=#{false} and is_late=#{false} and fee_discounts.finance_fee_particular_category_id = #{advance_fee.particular_id}").select{|par|  ((par.receiver.present?) and (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch)) and (fee_particulars.map(&:finance_fee_particular_category_id).include?(par. finance_fee_particular_category_id)) }
            @discounts_amount[ind] = []
            @discounts[ind].each do |d|   
              fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"is_deleted=#{false} and batch_id=#{batch.id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
              payable_ampt = (fee_particulars_single.map{|l| l.amount}.sum.to_f * advance_fee.no_of_month.to_i).to_f
              discount_amt = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
              @discounts_amount[ind][d.id] = discount_amt
              @total_discount[ind] = @total_discount[ind] + discount_amt
            end
          end
        end
      end
    end
  end
  
  def calculate_extra_fine(date,batch,student,fine_rule)
    if MultiSchool.current_school.id == 340
      #GET THE NEXT ALL months 
      extra_fine = 0
      s_date = date.start_date.to_date.beginning_of_month
      e_date = date.start_date.to_date.end_of_month
      other_months = FinanceFeeCollection.find(:all, :conditions => ["start_date NOT BETWEEN ? AND ? and is_deleted=#{false}", s_date, e_date], :order => "due_date asc")
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
      s_date = date.start_date.to_date.beginning_of_month
      e_date = date.start_date.to_date.end_of_month
      other_months = FinanceFeeCollection.find(:all, :conditions => ["start_date NOT BETWEEN ? AND ? and is_deleted=#{false}", s_date, e_date], :order => "due_date asc")
      unless other_months.nil? or other_months.empty?
        other_months.each do |other_month|
          fee_for_batch = FeeCollectionBatch.find(:all, :conditions => ["batch_id = ? and is_deleted=#{false} and finance_fee_collection_id != ?", batch.id, date.id])
          unless fee_for_batch.nil? or fee_for_batch.empty?
            fine_amount = fine_rule.fine_amount if fine_rule
            extra_fine = extra_fine + fine_amount
          end
        end
      end
      @all_fine_amount[ind] = @all_fine_amount[ind] + extra_fine
    end
  end
  
  def calculate_extra_fine_index(date,batch,student,fine_rule,ind)
    if MultiSchool.current_school.id == 340
      #GET THE NEXT ALL months 
      extra_fine = 0
      s_date = date.start_date.to_date.beginning_of_month
      e_date = date.start_date.to_date.end_of_month
      other_months = FinanceFeeCollection.find(:all, :conditions => ["start_date NOT BETWEEN ? AND ? and is_deleted=#{false}", s_date, e_date], :order => "due_date asc")
      unless other_months.nil? or other_months.empty?
        other_months.each do |other_month|
          fee_for_batch = FeeCollectionBatch.find(:all, :conditions => ["batch_id = ? and is_deleted=#{false} and finance_fee_collection_id != ?", batch.id, date.id])
          unless fee_for_batch.nil? or fee_for_batch.empty?
            fine_amount = fine_rule.fine_amount if fine_rule
            extra_fine = extra_fine + fine_amount
          end
        end
      end
      @fine_amount[ind] = @fine_amount[ind] + extra_fine
    end
  end
  
  def get_fine_discount(date,batch,student)
    if !@fine_amount.blank? and @fine_amount > 0
      fee_collection_discount_ids = FeeDiscountCollection.active.find_all_by_finance_fee_collection_id_and_batch_id_and_is_late(date.id, batch.id, true).map(&:fee_discount_id)
      unless fee_collection_discount_ids.nil? or fee_collection_discount_ids.empty?
        fee_collection_discount_ids = fee_collection_discount_ids.reject { |f| f.to_s.empty? }
        if fee_collection_discount_ids.blank?
          fee_collection_discount_ids[0] = 0
        end
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
        fee_collection_discount_ids = fee_collection_discount_ids.reject { |f| f.to_s.empty? }
        if fee_collection_discount_ids.blank?
          fee_collection_discount_ids[0] = 0
        end
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
        fee_collection_discount_ids = fee_collection_discount_ids.reject { |f| f.to_s.empty? }
        if fee_collection_discount_ids.blank?
          fee_collection_discount_ids[0] = 0
        end
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
  
  def arrange_pay(student_id, fee_collection_id, submission_date)
    advance_fee_collection = false
    @self_advance_fee = false
    @fee_has_advance_particular = false

    @student = Student.find(student_id)
    @batch = @student.batch

    @date = @fee_collection = FinanceFeeCollection.find(fee_collection_id)
    @student_has_due = false
    @std_finance_fee_due = FinanceFee.find(:first,:conditions=>["finance_fee_collections.due_date < ? and finance_fees.is_paid = 0 and finance_fees.student_id = ?", @date.due_date,@student.id],:include=>"finance_fee_collection")
    unless @std_finance_fee_due.blank?
      @student_has_due = true
    end
    @financefee = @student.finance_fee_by_date(@date)

    if @financefee.has_advance_fee_id
      if @date.is_advance_fee_collection
        @self_advance_fee = true
        advance_fee_collection = true
      end
      @fee_has_advance_particular = true
      @advance_ids = @financefee.fees_advances.map(&:advance_fee_id)
      @fee_collection_advances = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids.join(",")})")
    end

    @due_date = @fee_collection.due_date

    flash[:warning]=nil
    #flash[:notice]=nil

    @trans_id_ssl_commerce = "tran"+student_id.to_s+fee_collection_id.to_s
    @paid_fees = @financefee.finance_transactions

    exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date.id).map(&:fee_particular_id)
    unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
      exclude_particular_ids = exclude_particular_ids
    else
      exclude_particular_ids = [0]
    end
    
    if advance_fee_collection
      fee_collection_advances_particular = @fee_collection_advances.map(&:particular_id)
      if fee_collection_advances_particular.include?(0)
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
      else
        @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@financefee.batch_id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
      end
    else
      @fee_particulars = @date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@financefee.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee.batch) }
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

    #calculate_discount(@date, @financefee.batch, @student, @financefee.is_paid)
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
    unless submission_date.nil? or submission_date.empty? or submission_date.blank?
      require 'date'
      @submission_date = Date.parse(submission_date)
      days=(Date.parse(submission_date)-@date.due_date.to_date).to_i
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

    unless advance_fee_collection
      if @total_discount == 0
        @adv_fee_discount = true
        @actual_discount = 0
        calculate_discount(@date, @financefee.batch, @student, false, nil, @fee_has_advance_particular)
      end
    end

    total_fees =@financefee.balance.to_f+@fine_amount.to_f

    if @active_gateway == "trustbank"
      paid_fees = @financefee.finance_transactions
      paid_amount = 0.0
      unless paid_fees.nil? or paid_fees.blank?
        paid_fees.each do |pf|
          paid_amount += pf.amount
        end
      end
      remaining_amount = bal - paid_amount

      remaining_amount = total_fees - paid_amount

      unless @financefee.is_paid
        finance_order = FinanceOrder.find(:first, :conditions => "finance_fee_id = #{@financefee.id} and student_id = #{@financefee.student_id} and batch_id = #{@financefee.batch_id} and status = 0")
        unless finance_order.nil?
          @order_id = "O" + finance_order.id.to_s
          finance_order.update_attributes(:order_id => @order_id)
        else
          finance_order = FinanceOrder.new()
          finance_order.finance_fee_id = @financefee.id
          finance_order.student_id = @financefee.student_id
          finance_order.batch_id = @financefee.batch_id
          finance_order.balance = remaining_amount
          finance_order.save
          @order_id = "O" + finance_order.id.to_s
          finance_order.update_attributes(:order_id => @order_id)
        end
        
        payment = Payment.find(:first, :conditions => "order_id = '#{@order_id}'")
        unless payment.nil?
          finance_transaction_id = payment.finance_transaction_id
          unless finance_transaction_id.nil?
            finance_order = FinanceOrder.new()
            finance_order.finance_fee_id = @financefee.id
            finance_order.student_id = @financefee.student_id
            finance_order.batch_id = @financefee.batch_id
            finance_order.balance = remaining_amount
            finance_order.save
            @order_id = "O" + finance_order.id.to_s
            finance_order.update_attributes(:order_id => @order_id)
          end

        end
      else
        if remaining_amount > 0
          finance_order = FinanceOrder.new()
          finance_order.finance_fee_id = @financefee.id
          finance_order.student_id = @financefee.student_id
          finance_order.batch_id = @financefee.batch_id
          finance_order.balance = remaining_amount
          finance_order.save
          @order_id = "O" + finance_order.id.to_s
          @order_id_saved = true
          finance_order.update_attributes(:order_id => @order_id)
        end
      end
    end

    if @active_gateway == "Authorize.net"
      @sim_transaction = AuthorizeNet::SIM::Transaction.new(@merchant_id,@certificate, total_fees,{:hosted_payment_form => true,:x_description => "Fee-#{@student.admission_no}-#{@fee_collection.name}"})
      @sim_transaction.instance_variable_set("@custom_fields",{:x_description => "Fee (#{@student.full_name}-#{@student.admission_no}-#{@fee_collection.name})"})
      @sim_transaction.set_hosted_payment_receipt(AuthorizeNet::SIM::HostedReceiptPage.new(:link_method => AuthorizeNet::SIM::HostedReceiptPage::LinkMethod::GET, :link_text => "Back to #{current_school_name}", :link_url => URI.parse("http://#{request.host_with_port}/student/fee_details/#{student_id}/#{fee_collection_id}?create_transaction=1&only_path=false")))
    end
  end
  
  def arrange_multiple_pay(student_id, fees, submission_date)
    @order_id_saved = false
    @student = Student.find(student_id)
    @batch = @student.batch

    @fees_collections = fees

    @self_advance_fee = [] 
    @fee_has_advance_particular = []
    @date = [] 
    @fee_collection = []
    @student_has_due = []
    @financefee = []
    @advance_ids = []
    @fee_collection_advances = []
    @paid_fees = []
    @fee_particulars = []
    @total_payable = []
    @total_discount = []
    @adv_fee_discount = []
    @actual_discount = []
    @discounts_amount = []
    @discounts = []
    @onetime_discounts = []
    @onetime_discounts_amount = []
    @has_fine_discount = []
    @fine = []
    @fine_rule = []
    @fine_amount = []
    @vat = []
    @amount_to_pay = []
    @new_fine_amount = []

    fees.each do |fee|
      f = fee.to_i
      finance_fee = FinanceFee.find(f)
      fee_collection_id = finance_fee.fee_collection_id
      advance_fee_collection = false
      @self_advance_fee[f] = false
      @fee_has_advance_particular[f] = false

      @date[f] = @fee_collection[f] = FinanceFeeCollection.find(fee_collection_id)
      @student_has_due[f] = false
      @std_finance_fee_due = FinanceFee.find(:first,:conditions=>["finance_fee_collections.due_date < ? and finance_fees.is_paid = 0 and finance_fees.student_id = ?", @date[f].due_date,@student.id],:include=>"finance_fee_collection")
      unless @std_finance_fee_due.blank?
        @student_has_due[f] = true
      end
      @financefee[f] = @student.finance_fee_by_date(@date[f])

      if @financefee[f].has_advance_fee_id
        if @date[f].is_advance_fee_collection
          @self_advance_fee[f] = true
          advance_fee_collection = true
        end
        @fee_has_advance_particular[f] = true
        @advance_ids[f] = @financefee[f].fees_advances.map(&:advance_fee_id)
        @fee_collection_advances[f] = FinanceFeeAdvance.find(:all, :conditions => "id IN (#{@advance_ids[f].join(",")})")
      end

      @due_date = @fee_collection[f].due_date

      flash[:warning]=nil
      #flash[:notice]=nil

      @trans_id_ssl_commerce = "tran"+student_id.to_s+fee_collection_id.to_s
      @paid_fees[f] = @financefee[f].finance_transactions

      exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(@student.id,@date[f].id).map(&:fee_particular_id)
      unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
        exclude_particular_ids = exclude_particular_ids
      else
        exclude_particular_ids = [0]
      end
      
      if advance_fee_collection
        fee_collection_advances_particular = @fee_collection_advances[f].map(&:particular_id)
        if fee_collection_advances_particular.include?(0)
          @fee_particulars[f] = @date[f].finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@financefee[f].batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[f].batch) }
        else
          @fee_particulars[f] = @date[f].finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@financefee[f].batch_id} and finance_fee_particular_category_id IN (#{fee_collection_advances_particular.join(",")})").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[f].batch) }
        end
      else
        @fee_particulars[f] = @date[f].finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{@financefee[f].batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@financefee[f].batch) }
      end

      if advance_fee_collection
        month = 1
        payable = 0
        @fee_collection_advances[f].each do |fee_collection_advance|
          @fee_particulars[f].each do |particular|
            if fee_collection_advance.particular_id == particular.finance_fee_particular_category_id
              payable += particular.amount * fee_collection_advance.no_of_month.to_i
            else
              payable += particular.amount
            end
          end
        end
        @total_payable[f]=payable.to_f
      else  
        @total_payable[f]=@fee_particulars[f].map{|s| s.amount}.sum.to_f
      end

      @total_discount[f] = 0

      @adv_fee_discount[f] = false
      @actual_discount[f] = 1

      if advance_fee_collection
        calculate_discount_index(@date[f],@financefee[f].batch,@student,f,true,@fee_collection_advances[f],@fee_has_advance_particular[f])
      else
        if @fee_has_advance_particular[f]
          calculate_discount_index(@date[f], @financefee[f].batch, @student,f, false, @fee_collection_advances[f], @fee_has_advance_particular[f])
        else
          calculate_discount_index(@date[f], @financefee[f].batch, @student,f, false, nil, @fee_has_advance_particular[f])
        end
      end

      bal=(@total_payable[f]-@total_discount[f]).to_f
      unless submission_date.nil? or submission_date.empty? or submission_date.blank?
        require 'date'
        @submission_date = Date.parse(submission_date)
        days=(Date.parse(submission_date)-@date[f].due_date.to_date).to_i
      else
        @submission_date = Date.today
        if @financefee[f].is_paid
          @paid_fees[f] = @financefee[f].finance_transactions
          days=(@paid_fees[f].first.transaction_date-@date[f].due_date.to_date).to_i
        else
          days=(Date.today-@date[f].due_date.to_date).to_i
        end
      end

      auto_fine=@date[f].fine

      @has_fine_discount[f] = false
      if days > 0 and auto_fine #and @financefee[f].is_paid == false
        @fine_rule[f]=auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date[f].created_at}'"],:order=>'fine_days ASC')
        @fine_amount[f]=@fine_rule[f].is_amount ? @fine_rule[f].fine_amount : (bal*@fine_rule[f].fine_amount)/100 if @fine_rule[f]

        calculate_extra_fine_index(@date[f], @financefee[f].batch, @student, @fine_rule[f],f)

        @new_fine_amount[f] = @fine_amount[f]
        get_fine_discount_index(@date[f], @financefee[f].batch, @student, f)
        if @fine_amount[f] < 0
           @fine_amount[f] = 0
        end
      end

      @fine_amount[f]=0 if @financefee[f].is_paid

      unless advance_fee_collection
        if @total_discount[f] == 0
          @adv_fee_discount[f] = true
          @actual_discount[f] = 0
          calculate_discount_index(@date[f], @financefee[f].batch, @student, f,false, nil, @fee_has_advance_particular[f])
        end
      end

      total_fees =@financefee[f].balance.to_f+@fine_amount[f].to_f

      if @active_gateway == "trustbank"
        paid_fees = @financefee[f].finance_transactions
        
        paid_amount = 0.0
        unless paid_fees.blank?
          paid_fees.each do |pf|
            paid_amount += pf.amount
          end
        end
        remaining_amount = total_fees - paid_amount

        unless @financefee[f].is_paid
          finance_order = FinanceOrder.find(:first, :conditions => "finance_fee_id = #{@financefee[f].id} and student_id = #{@financefee[f].student_id} and batch_id = #{@financefee[f].batch_id} and status = 0")
          unless finance_order.blank?
            if @order_id_saved
              finance_order.update_attributes(:order_id => @order_id)
            else
              @order_id = "O" + finance_order.id.to_s
              finance_order.update_attributes(:order_id => @order_id)
              @order_id_saved = true
            end

          else
            if @order_id_saved
              finance_order = FinanceOrder.new()
              finance_order.finance_fee_id = @financefee[f].id
              finance_order.order_id = @order_id
              finance_order.student_id = @financefee[f].student_id
              finance_order.batch_id = @financefee[f].batch_id
              finance_order.balance = remaining_amount
              finance_order.save
            else
              finance_order = FinanceOrder.new()
              finance_order.finance_fee_id = @financefee[f].id
              finance_order.student_id = @financefee[f].student_id
              finance_order.batch_id = @financefee[f].batch_id
              finance_order.balance = remaining_amount
              finance_order.save
              @order_id = "O" + finance_order.id.to_s
              @order_id_saved = true
              finance_order.update_attributes(:order_id => @order_id)
            end
          end
          
          payment = Payment.find(:first, :conditions => "order_id = '#{@order_id}'")
          unless payment.blank?
            finance_transaction_id = payment.finance_transaction_id
            unless finance_transaction_id.nil?
              finance_order = FinanceOrder.new()
              finance_order.finance_fee_id = @financefee[f].id
              finance_order.student_id = @financefee[f].student_id
              finance_order.batch_id = @financefee[f].batch_id
              finance_order.balance = remaining_amount
              finance_order.save
              @order_id = "O" + finance_order.id.to_s
              finance_order.update_attributes(:order_id => @order_id)
            end
          end
        else
          if remaining_amount > 0
            finance_order = FinanceOrder.new()
            finance_order.finance_fee_id = @financefee[f].id
            finance_order.student_id = @financefee[f].student_id
            finance_order.batch_id = @financefee[f].batch_id
            finance_order.balance = remaining_amount
            finance_order.save
            @order_id = "O" + finance_order.id.to_s
            @order_id_saved = true
            finance_order.update_attributes(:order_id => @order_id)
          end
        end
      end

      if @active_gateway == "Authorize.net"
        @sim_transaction = AuthorizeNet::SIM::Transaction.new(@merchant_id,@certificate, total_fees,{:hosted_payment_form => true,:x_description => "Fee-#{@student.admission_no}-#{@fee_collection[f].name}"})
        @sim_transaction.instance_variable_set("@custom_fields",{:x_description => "Fee (#{@student.full_name}-#{@student.admission_no}-#{@fee_collection[f].name})"})
        @sim_transaction.set_hosted_payment_receipt(AuthorizeNet::SIM::HostedReceiptPage.new(:link_method => AuthorizeNet::SIM::HostedReceiptPage::LinkMethod::GET, :link_text => "Back to #{current_school_name}", :link_url => URI.parse("http://#{request.host_with_port}/student/fee_details/#{student_id}/#{fee_collection_id}?create_transaction=1&only_path=false")))
      end
    end
  end
  
  def pay_student(amount_from_gateway, total_fees, request_params, orderId, trans_date, ref_id)
    unless @financefee.is_paid?
      unless amount_from_gateway.to_f < 0
        #if orderId.to_s == "O1049432"
                            #abort(amount_from_gateway.to_s + "  " + total_fees.to_s)
                          #end
      #unless amount_from_gateway.to_f > Champs21Precision.set_and_modify_precision(total_fees).to_f
#          unless total_fees < 0
            #abort('here3 ' + amount_from_gateway.to_s + "  " + total_fees.to_s)
          transaction = FinanceTransaction.new
          transaction.title = "#{t('receipt_no')}. F#{@financefee.id}"
          transaction.category = FinanceTransactionCategory.find_by_name("Fee")
          transaction.payee = @student
          transaction.finance = @financefee
          transaction.amount = total_fees
          
          fine_included = false
          fine_amount = 0.00
          unless (@fine.to_f + @fine_amount.to_f).zero?
              fine_included = true
              fine_amount = @fine.to_f + @fine_amount.to_f
          else
              unless trans_date.nil? or trans_date.empty? or trans_date.blank?
                require 'date'
                days=(Date.parse(trans_date)-@date.due_date.to_date).to_i
              else
                days=(Date.today-@date.due_date.to_date).to_i
              end

              auto_fine=@date.fine

              if days > 0 and auto_fine #and @financefee.is_paid == false
                fine_rule = auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date.created_at}'"],:order=>'fine_days ASC')
                fine_amount = fine_rule.is_amount ? fine_rule.fine_amount : (total_fees*fine_rule.fine_amount)/100 if fine_rule
                fine_included = true
              end
          end
          if fine_included
            transaction.fine_included = fine_included
            transaction.fine_amount = fine_amount
          end
          
          transaction.transaction_date = trans_date.to_date #Date.today
          transaction.payment_mode = "Online Payment"
          transaction.save
          if transaction.save
            is_paid =@financefee.balance==0 ? true : false
            @financefee.update_attributes( :is_paid=>is_paid)

            @paid_fees = @financefee.finance_transactions

            proccess_particulars_category = []
            loop_particular = 0
            unless request_params.nil?
              @fee_particulars.each do |fp|
                advanced = false
                particular_amount = fp.amount.to_f
                unless request_params["fee_particular_" + fp.id.to_s].nil?
                  if request_params["fee_particular_" + fp.id.to_s] == "on"
                    paid_amount = request_params["fee_particular_amount_" + fp.id.to_s].to_f
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
                  else
#                    particular_amount = fp.amount.to_f
#                    finance_transaction_particular = FinanceTransactionParticular.new
#                    finance_transaction_particular.finance_transaction_id = transaction.id
#                    finance_transaction_particular.particular_id = fp.id
#                    finance_transaction_particular.particular_type = 'Particular'
#                    finance_transaction_particular.transaction_type = 'Fee Collection'
#                    finance_transaction_particular.amount = particular_amount
#                    finance_transaction_particular.transaction_date = transaction.transaction_date
#                    finance_transaction_particular.save
                  end
                else
#                  particular_amount = fp.amount.to_f
#                  finance_transaction_particular = FinanceTransactionParticular.new
#                  finance_transaction_particular.finance_transaction_id = transaction.id
#                  finance_transaction_particular.particular_id = fp.id
#                  finance_transaction_particular.particular_type = 'Particular'
#                  finance_transaction_particular.transaction_type = 'Fee Collection'
#                  finance_transaction_particular.amount = particular_amount
#                  finance_transaction_particular.transaction_date = transaction.transaction_date
#                  finance_transaction_particular.save
                end
              end

              unless @onetime_discounts.blank?
                @onetime_discounts.each do |od|
                  unless request_params["fee_discount_" + od.id.to_s].nil?
                    if request_params["fee_discount_" + od.id.to_s] == "on"
                      discount_amount = request_params["fee_discount_amount_" + od.id.to_s].to_f
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = od.id
                      finance_transaction_particular.particular_type = 'Adjustment'
                      finance_transaction_particular.transaction_type = 'Discount'
                      finance_transaction_particular.amount = discount_amount
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save
                    else
#                      discount_amount = @onetime_discounts_amount[od.id].to_f
#                      finance_transaction_particular = FinanceTransactionParticular.new
#                      finance_transaction_particular.finance_transaction_id = transaction.id
#                      finance_transaction_particular.particular_id = od.id
#                      finance_transaction_particular.particular_type = 'Adjustment'
#                      finance_transaction_particular.transaction_type = 'Discount'
#                      finance_transaction_particular.amount = discount_amount
#                      finance_transaction_particular.transaction_date = transaction.transaction_date
#                      finance_transaction_particular.save
                    end
                  else
#                    discount_amount = @onetime_discounts_amount[od.id].to_f
#                    finance_transaction_particular = FinanceTransactionParticular.new
#                    finance_transaction_particular.finance_transaction_id = transaction.id
#                    finance_transaction_particular.particular_id = od.id
#                    finance_transaction_particular.particular_type = 'Adjustment'
#                    finance_transaction_particular.transaction_type = 'Discount'
#                    finance_transaction_particular.amount = discount_amount
#                    finance_transaction_particular.transaction_date = transaction.transaction_date
#                    finance_transaction_particular.save
                  end
                end
              end

              unless @discounts.blank?
                @discounts.each do |od|
                  unless request_params["fee_discount_" + od.id.to_s].nil?
                    if request_params["fee_discount_" + od.id.to_s] == "on"
                      discount_amount = request_params["fee_discount_amount_" + od.id.to_s].to_f
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = od.id
                      finance_transaction_particular.particular_type = 'Adjustment'
                      finance_transaction_particular.transaction_type = 'Discount'
                      finance_transaction_particular.amount = discount_amount
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save
                    else
#                      discount_amount = @discounts_amount[od.id]
#                      finance_transaction_particular = FinanceTransactionParticular.new
#                      finance_transaction_particular.finance_transaction_id = transaction.id
#                      finance_transaction_particular.particular_id = od.id
#                      finance_transaction_particular.particular_type = 'Adjustment'
#                      finance_transaction_particular.transaction_type = 'Discount'
#                      finance_transaction_particular.amount = discount_amount
#                      finance_transaction_particular.transaction_date = transaction.transaction_date
#                      finance_transaction_particular.save
                    end
                  else
#                    discount_amount = @discounts_amount[od.id]
#                    finance_transaction_particular = FinanceTransactionParticular.new
#                    finance_transaction_particular.finance_transaction_id = transaction.id
#                    finance_transaction_particular.particular_id = od.id
#                    finance_transaction_particular.particular_type = 'Adjustment'
#                    finance_transaction_particular.transaction_type = 'Discount'
#                    finance_transaction_particular.amount = discount_amount
#                    finance_transaction_particular.transaction_date = transaction.transaction_date
#                    finance_transaction_particular.save
                  end
                end
              end

              unless request_params[:fee_vat].nil?
                if request_params[:fee_vat] == "on"
                  vat_amount = request_params[:fee_vat_amount].to_f
                  finance_transaction_particular = FinanceTransactionParticular.new
                  finance_transaction_particular.finance_transaction_id = transaction.id
                  finance_transaction_particular.particular_id = 0
                  finance_transaction_particular.particular_type = 'VAT'
                  finance_transaction_particular.transaction_type = ''
                  finance_transaction_particular.amount = vat_amount
                  finance_transaction_particular.transaction_date = transaction.transaction_date
                  finance_transaction_particular.save
                else
#                  if transaction.vat_included?
#                    vat_amount = transaction.vat_amount
#                    finance_transaction_particular = FinanceTransactionParticular.new
#                    finance_transaction_particular.finance_transaction_id = transaction.id
#                    finance_transaction_particular.particular_id = 0
#                    finance_transaction_particular.particular_type = 'VAT'
#                    finance_transaction_particular.transaction_type = ''
#                    finance_transaction_particular.amount = vat_amount
#                    finance_transaction_particular.transaction_date = transaction.transaction_date
#                    finance_transaction_particular.save
#                  end
                end
              else
#                if transaction.vat_included?
#                  vat_amount = transaction.vat_amount
#                  finance_transaction_particular = FinanceTransactionParticular.new
#                  finance_transaction_particular.finance_transaction_id = transaction.id
#                  finance_transaction_particular.particular_id = 0
#                  finance_transaction_particular.particular_type = 'VAT'
#                  finance_transaction_particular.transaction_type = ''
#                  finance_transaction_particular.amount = vat_amount
#                  finance_transaction_particular.transaction_date = transaction.transaction_date
#                  finance_transaction_particular.save
#                end
              end
              
              if fine_included
                finance_transaction_particular = FinanceTransactionParticular.new
                finance_transaction_particular.finance_transaction_id = transaction.id
                finance_transaction_particular.particular_id = 0
                finance_transaction_particular.particular_type = 'Fine'
                finance_transaction_particular.transaction_type = ''
                finance_transaction_particular.amount = fine_amount
                finance_transaction_particular.transaction_date = transaction.transaction_date
                finance_transaction_particular.save
              end
            
              if @has_fine_discount
                @discounts_on_lates.each do |fd|
                  unless request_params["fee_fine_discount_" + fd.id.to_s].nil?
                    if request_params["fee_fine_discount_" + fd.id.to_s] == "on"
                      discount_amount = request_params["fee_fine_discount_amount_" + fd.id.to_s].to_f
                      finance_transaction_particular = FinanceTransactionParticular.new
                      finance_transaction_particular.finance_transaction_id = transaction.id
                      finance_transaction_particular.particular_id = fd.id
                      finance_transaction_particular.particular_type = 'FineAdjustment'
                      finance_transaction_particular.transaction_type = 'Discount'
                      finance_transaction_particular.amount = discount_amount
                      finance_transaction_particular.transaction_date = transaction.transaction_date
                      finance_transaction_particular.save
                    else
#                      discount_amount = @discounts_late_amount[od.id]
#                      finance_transaction_particular = FinanceTransactionParticular.new
#                      finance_transaction_particular.finance_transaction_id = transaction.id
#                      finance_transaction_particular.particular_id = fd.id
#                      finance_transaction_particular.particular_type = 'FineAdjustment'
#                      finance_transaction_particular.transaction_type = 'Discount'
#                      finance_transaction_particular.amount = discount_amount
#                      finance_transaction_particular.transaction_date = transaction.transaction_date
#                      finance_transaction_particular.save
                    end
                  else
#                    discount_amount = @discounts_late_amount[od.id]
#                    finance_transaction_particular = FinanceTransactionParticular.new
#                    finance_transaction_particular.finance_transaction_id = transaction.id
#                    finance_transaction_particular.particular_id = fd.id
#                    finance_transaction_particular.particular_type = 'FineAdjustment'
#                    finance_transaction_particular.transaction_type = 'Discount'
#                    finance_transaction_particular.amount = discount_amount
#                    finance_transaction_particular.transaction_date = transaction.transaction_date
#                    finance_transaction_particular.save
                  end
                end
              end


              @finance_order = FinanceOrder.find_by_order_id(orderId)
              @finance_order.update_attributes(:status => 1)

            else
              @fee_particulars.each do |fp|
                particular_amount = fp.amount.to_f
                finance_transaction_particular = FinanceTransactionParticular.new
                finance_transaction_particular.finance_transaction_id = transaction.id
                finance_transaction_particular.particular_id = fp.id
                finance_transaction_particular.particular_type = 'Particular'
                finance_transaction_particular.transaction_type = 'Fee Collection'
                finance_transaction_particular.amount = particular_amount
                finance_transaction_particular.transaction_date = transaction.transaction_date
                finance_transaction_particular.save
              end

              unless @onetime_discounts.blank?
                @onetime_discounts.each do |od|
                  discount_amount = @onetime_discounts_amount[od.id].to_f
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


              unless @discounts.blank?
                @discounts.each do |od|
                  discount_amount = @discounts_amount[od.id]
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

              if transaction.vat_included?
                vat_amount = transaction.vat_amount
                finance_transaction_particular = FinanceTransactionParticular.new
                finance_transaction_particular.finance_transaction_id = transaction.id
                finance_transaction_particular.particular_id = 0
                finance_transaction_particular.particular_type = 'VAT'
                finance_transaction_particular.transaction_type = ''
                finance_transaction_particular.amount = vat_amount
                finance_transaction_particular.transaction_date = transaction.transaction_date
                finance_transaction_particular.save
              end

              if fine_included
                finance_transaction_particular = FinanceTransactionParticular.new
                finance_transaction_particular.finance_transaction_id = transaction.id
                finance_transaction_particular.particular_id = 0
                finance_transaction_particular.particular_type = 'Fine'
                finance_transaction_particular.transaction_type = ''
                finance_transaction_particular.amount = fine_amount
                finance_transaction_particular.transaction_date = transaction.transaction_date
                finance_transaction_particular.save
              end


              if @has_fine_discount
                @discounts_on_lates.each do |fd|
                  discount_amount = @discounts_late_amount[od.id]
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
            finance_notmatch_transaction.run_from = "PaymentSettingsController - PayStudent"
            finance_notmatch_transaction.save
          end
          
          payment = Payment.find_by_order_id_and_payee_id_and_payment_id(orderId, @student.id, @financefee.id)
          if payment.gateway_response[:verified].to_i == 0
            unless payment.validation_response.nil?
              if payment.validation_response[:verified].to_i == 1
                payment.update_attributes(:gateway_response => payment.validation_response)
              end
            end
          end
          
          unless payment.gateway_response.nil?
            unless payment.gateway_response[:tran_date].nil?
              dt = payment.gateway_response[:tran_date].split(".")
              transaction_datetime = dt[0]
              
              payment.update_attributes(:transaction_datetime => transaction_datetime)
            end
          end
          
          payment.update_attributes(:finance_transaction_id => transaction.id)
          unless @financefee.transaction_id.nil?
            tid =   @financefee.transaction_id.to_s + ",#{transaction.id}"
          else
            tid=transaction.id
          end
          is_paid = @financefee.balance==0 ? true : false
          
          student_fee_ledger = StudentFeeLedger.new
          student_fee_ledger.student_id = @student.id
          student_fee_ledger.ledger_date = transaction.transaction_date
          student_fee_ledger.ledger_title = ""
          student_fee_ledger.amount_to_pay = 0.0
          student_fee_ledger.fee_id = @financefee.id
          student_fee_ledger.amount_paid = transaction.amount
          student_fee_ledger.transaction_id = transaction.id
          student_fee_ledger.order_id = orderId
          student_fee_ledger.save


          @financefee.update_attributes(:transaction_id=>tid, :is_paid=>is_paid)
          @paid_fees = FinanceTransaction.find(:all,:conditions=>"FIND_IN_SET(id,\"#{tid}\")")
          online_transaction_id = payment.gateway_response[:transaction_id]
          online_transaction_id ||= payment.gateway_response[:x_trans_id]

          g_data = Guardian.find_by_user_id(current_user.id);
          if !g_data.blank? && !g_data.email.blank?
            header_txt = "#{t('payment_success')} #{online_transaction_id}"
            body_txt = render_to_string(:template => 'gateway_payments/paypal/student_fee_receipt', :layout => false)
            champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
            api_endpoint = champs21_api_config['api_url']
            form_data = {}
            form_data['body'] = body_txt
            form_data['header'] = header_txt
            form_data['email'] = g_data.email
            form_data['first_name'] = g_data.first_name
            form_data['last_name'] = g_data.last_name

            api_uri = URI(api_endpoint + "api/user/paymentmail")


            http = Net::HTTP.new(api_uri.host, api_uri.port)
            request = Net::HTTP::Post.new(api_uri.path)
            request.set_form_data(form_data)
            http.request(request)
          end 


          sms_setting = SmsSetting.new()
          if sms_setting.student_sms_active or sms_setting.parent_sms_active    
            message = "Fees received BDT #AMOUNT# for #UNAME#(#UID#) as on #PAIDDATE# by TBL. TranID-#TRANID# TranRef-#TRANREF#, Sender - SAGC"
            if File.exists?("#{Rails.root}/config/sms_text_#{MultiSchool.current_school.id}.yml")
              sms_text_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/sms_text_#{MultiSchool.current_school.id}.yml")['school']
              message = sms_text_config['feepaid']
            end
            recipients = []
            unless @student.sms_number.nil? or @student.sms_number.empty? or @student.sms_number.blank?
              message = message.gsub("#UNAME#", @student.full_name)
              message = message.gsub("#UID#", @student.admission_no)
              message = message.gsub("#AMOUNT#", amount_from_gateway.to_s)
              message = message.gsub("#PAIDDATE#", trans_date.to_date.strftime("%d-%m-%Y"))
              message = message.gsub("#TRANID#", orderId)
              message = message.gsub("#TRANREF#", ref_id)
              recipients.push @student.sms_number
            else
              unless @student.phone2.nil? or @student.phone2.empty? or @student.phone2.blank?
                message = message
                message = message.gsub("#UNAME#", @student.full_name)
                message = message.gsub("#UID#", @student.admission_no)
                message = message.gsub("#AMOUNT#", amount_from_gateway.to_s)
                message = message.gsub("#PAIDDATE#", trans_date.to_date.strftime("%d-%m-%Y"))
                message = message.gsub("#TRANID#", orderId)
                message = message.gsub("#TRANREF#", ref_id)
                recipients.push @student.phone2
              end
            end
            messages = []
            messages[0] = message
            #sms = Delayed::Job.enqueue(SmsManager.new(message,recipients))
            #send_sms_finance(messages,recipients)
          end


          flash[:success] = "Thanks for your payment, payment was Successfull. Your order ID is: #{orderId}"
        #else
        #  flash[:notice] = "#{t('payment_failed')}"
        #end
      else
        flash[:notice] = "#{t('payment_failed')}"
      end
    else
      flash[:notice] = "#{t('flash_payed')}"
    end
  end
  
  def pay_student_index(amount_from_gateway, total_fees, request_params, orderId, trans_date, ref_id, fees)
    unless amount_from_gateway.to_f < 0
      unless amount_from_gateway.to_f > Champs21Precision.set_and_modify_precision(total_fees).to_f
        transaction_parent = FinanceTransaction.new
        transaction_parent.title = "#{t('receipt_no')}. F#{orderId}"
        transaction_parent.category = FinanceTransactionCategory.find_by_name("Fee")
        transaction_parent.payee = @student
        #transaction_parent.finance = @financefee[f]
        transaction_parent.amount = total_fees
        transaction_parent.fine_included = false
        transaction_parent.fine_amount = 0.00
        #transaction_parent.transaction_date = Date.today
        transaction_parent.transaction_date = trans_date.to_date
        transaction_parent.payment_mode = "Online Payment"
        transaction_parent.save

        #abort(transaction_parent.inspect)
        if transaction_parent.save
          fees.each do |fee|
            f = fee.to_i
            #abort(request_params.inspect)
            unless @financefee[f].is_paid?
              unless amount_from_gateway.to_f < 0
                  unless amount_from_gateway.to_f > Champs21Precision.set_and_modify_precision(total_fees).to_f

                  transaction = FinanceTransaction.new
                  transaction.title = "#{t('receipt_no')}. F#{@financefee[f].id}"
                  transaction.category = FinanceTransactionCategory.find_by_name("Fee")
                  transaction.payee = @student
                  transaction.finance = @financefee[f]
                  transaction.amount = request_params["amount_to_pay_#{f.to_s}"]
                  
                  fine_included = false
                  fine_amount = 0.00
                  unless (@fine[f].to_f + @fine_amount[f].to_f).zero?
                      fine_included = true
                      fine_amount = @fine[f].to_f + @fine_amount[f].to_f
                  else
                      unless trans_date.nil? or trans_date.empty? or trans_date.blank?
                        require 'date'
                        days=(Date.parse(trans_date)-@date[f].due_date.to_date).to_i
                      else
                        days=(Date.today-@date[f].due_date.to_date).to_i
                      end

                      auto_fine=@date[f].fine

                      if days > 0 and auto_fine #and @financefee.is_paid == false
                        fine_rule = auto_fine.fine_rules.find(:last,:conditions=>["fine_days <= '#{days}' and created_at <= '#{@date[f].created_at}'"],:order=>'fine_days ASC')
                        fine_amount = fine_rule.is_amount ? fine_rule.fine_amount : (bal*fine_rule.fine_amount)/100 if fine_rule
                        fine_included = true
                      end
                  end
                  if fine_included
                    transaction_parent.fine_included = fine_included
                    transaction_parent.fine_amount = fine_amount
                  end
                  
                  #transaction.fine_included = (@fine[f].to_f + @fine_amount[f].to_f).zero? ? false : true
                  #transaction.fine_amount = @fine[f].to_f + @fine_amount[f].to_f
                  #transaction.transaction_date = Date.today
                  transaction.transaction_date = trans_date.to_date #Date.today
                  transaction.payment_mode = "Online Payment"
                  transaction.is_child_transaction = true
                  transaction.parent_transaction_id = transaction_parent.id
                  transaction.save
                  if transaction.save
#                    total_fine_amount = 0
#                    unless (@fine[f].to_f + @fine_amount[f].to_f).zero?
#                      total_fine_amount = @fine[f].to_f + @fine_amount[f].to_f
#                    end
                    is_paid =@financefee[f].balance==0 ? true : false
                    @financefee[f].update_attributes( :is_paid=>is_paid)

                    proccess_particulars_category = []
                    loop_particular = 0
                    unless request_params.nil?
                      @fee_particulars[f].each do |fp|
                        advanced = false
                        particular_amount = fp.amount.to_f
                        unless request_params["fee_particular_" + fp.id.to_s + "_" + f.to_s].nil?
                          if request_params["fee_particular_" + fp.id.to_s + "_" + f.to_s] == "on"
                            paid_amount = request_params["fee_particular_amount_" + fp.id.to_s + "_" + f.to_s].to_f
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

                      unless @onetime_discounts[f].blank?
                        @onetime_discounts[f].each do |od|
                          unless request_params["fee_discount_" + od.id.to_s + "_" + f.to_s].nil?
                            if request_params["fee_discount_" + od.id.to_s + "_" + f.to_s] == "on"
                              discount_amount = request_params["fee_discount_amount_" + od.id.to_s + "_" + f.to_s].to_f
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

                      unless @discounts[f].blank?
                        @discounts[f].each do |od|
                          unless request_params["fee_discount_" + od.id.to_s + "_" + f.to_s].nil?
                            if request_params["fee_discount_" + od.id.to_s + "_" + f.to_s] == "on"
                              discount_amount = request_params["fee_discount_amount_" + od.id.to_s + "_" + f.to_s].to_f
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

                      unless request_params["fee_vat" + "_" + f.to_s].nil?
                        if request_params["fee_vat" + "_" + f.to_s] == "on"
                          vat_amount = request_params["fee_vat_amount" + "_" + f.to_s].to_f
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

#                      unless request_params["fee_fine" + "_" + f.to_s].nil?
#                        if request_params["fee_fine" + "_" + f.to_s] == "on"
#                          fine_amount = request_params["fine_amount_to_pay" + "_" + f.to_s].to_f
#                          finance_transaction_particular = FinanceTransactionParticular.new
#                          finance_transaction_particular.finance_transaction_id = transaction.id
#                          finance_transaction_particular.particular_id = 0
#                          finance_transaction_particular.particular_type = 'Fine'
#                          finance_transaction_particular.transaction_type = ''
#                          finance_transaction_particular.amount = fine_amount
#                          finance_transaction_particular.transaction_date = transaction.transaction_date
#                          finance_transaction_particular.save
#                        end
#                      end

                      if fine_included
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = 0
                        finance_transaction_particular.particular_type = 'Fine'
                        finance_transaction_particular.transaction_type = ''
                        finance_transaction_particular.amount = fine_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      end

                      if @has_fine_discount[f]
                        @discounts_on_lates[f].each do |fd|
                          unless request_params["fee_fine_discount_" + fd.id.to_s + "_" + f.to_s].nil?
                            if request_params["fee_fine_discount_" + fd.id.to_s + "_" + f.to_s] == "on"
                              discount_amount = request_params["fee_fine_discount_amount_" + fd.id.to_s + "_" + f.to_s].to_f
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


                      @finance_order = FinanceOrder.find_by_order_id_and_finance_fee_id(orderId, f)
                      #@finance_order[f] = FinanceOrder.find_by_order_id(orderId)
                      @finance_order.update_attributes(:status => 1)

                    else
                      @fee_particulars[f].each do |fp|
                        particular_amount = fp.amount.to_f
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = fp.id
                        finance_transaction_particular.particular_type = 'Particular'
                        finance_transaction_particular.transaction_type = 'Fee Collection'
                        finance_transaction_particular.amount = particular_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      end

                      unless @onetime_discounts[f].blank?
                        @onetime_discounts[f].each do |od|
                          discount_amount = @onetime_discounts_amount[f][od.id].to_f
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


                      unless @discounts[f].blank?
                        @discounts[f].each do |od|
                          discount_amount = @discounts_amount[f][od.id]
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

                      if transaction.vat_included?
                        vat_amount = transaction.vat_amount
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = 0
                        finance_transaction_particular.particular_type = 'VAT'
                        finance_transaction_particular.transaction_type = ''
                        finance_transaction_particular.amount = vat_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      end

#                      if total_fine_amount
#                        fine_amount = total_fine_amount
#                        finance_transaction_particular = FinanceTransactionParticular.new
#                        finance_transaction_particular.finance_transaction_id = transaction.id
#                        finance_transaction_particular.particular_id = 0
#                        finance_transaction_particular.particular_type = 'Fine'
#                        finance_transaction_particular.transaction_type = ''
#                        finance_transaction_particular.amount = fine_amount
#                        finance_transaction_particular.transaction_date = transaction.transaction_date
#                        finance_transaction_particular.save
#                      end

                      if fine_included
                        finance_transaction_particular = FinanceTransactionParticular.new
                        finance_transaction_particular.finance_transaction_id = transaction.id
                        finance_transaction_particular.particular_id = 0
                        finance_transaction_particular.particular_type = 'Fine'
                        finance_transaction_particular.transaction_type = ''
                        finance_transaction_particular.amount = fine_amount
                        finance_transaction_particular.transaction_date = transaction.transaction_date
                        finance_transaction_particular.save
                      end


                      if @has_fine_discount[f]
                        @discounts_on_lates[f].each do |fd|
                          discount_amount = @discounts_late_amount[f][od.id]
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
                  finance_notmatch_transaction.run_from = "PaymentSettingsController - PayStudentIndex"
                  finance_notmatch_transaction.save
                end

                  payment = Payment.find_by_order_id_and_payee_id_and_payment_id(orderId, @student.id, f)
                  #abort(payment.inspect)
                  
                  if payment.gateway_response[:verified].to_i == 0
                    unless payment.validation_response.nil?
                      if payment.validation_response[:verified].to_i == 1
                        payment.update_attributes(:gateway_response => payment.validation_response)
                      end
                    end
                  end

                  unless payment.gateway_response.nil?
                    unless payment.gateway_response[:tran_date].nil?
                      dt = payment.gateway_response[:tran_date].split(".")
                      transaction_datetime = dt[0]

                      payment.update_attributes(:transaction_datetime => transaction_datetime)
                    end
                  end

                  payment.update_attributes(:finance_transaction_id => transaction.id)
                  
                  student_fee_ledger = StudentFeeLedger.new
                  student_fee_ledger.student_id = @student.id
                  student_fee_ledger.ledger_date = transaction.transaction_date
                  student_fee_ledger.ledger_title = ""
                  student_fee_ledger.amount_to_pay = 0.0
                  student_fee_ledger.fee_id = @financefee[f].id
                  student_fee_ledger.amount_paid = transaction.amount
                  student_fee_ledger.transaction_id = transaction.id
                  student_fee_ledger.order_id = orderId
                  student_fee_ledger.save
                  
                  unless @financefee[f].transaction_id.nil?
                    tid =   @financefee[f].transaction_id.to_s + ",#{transaction.id}"
                  else
                    tid=transaction.id
                  end
                  is_paid = @financefee[f].balance==0 ? true : false



                  @financefee[f].update_attributes(:transaction_id=>tid, :is_paid=>is_paid)
                  #@paid_fees = FinanceTransaction.find(:all,:conditions=>"FIND_IN_SET(id,\"#{tid}\")")
                  online_transaction_id = payment.gateway_response[:transaction_id]
                  online_transaction_id ||= payment.gateway_response[:x_trans_id]

                  g_data = Guardian.find_by_user_id(current_user.id);
                  if !g_data.blank? && !g_data.email.blank?
                    header_txt = "#{t('payment_success')} #{online_transaction_id}"
                    body_txt = render_to_string(:template => 'gateway_payments/paypal/student_fee_receipt', :layout => false)
                    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
                    api_endpoint = champs21_api_config['api_url']
                    form_data = {}
                    form_data['body'] = body_txt
                    form_data['header'] = header_txt
                    form_data['email'] = g_data.email
                    form_data['first_name'] = g_data.first_name
                    form_data['last_name'] = g_data.last_name

                    api_uri = URI(api_endpoint + "api/user/paymentmail")


                    http = Net::HTTP.new(api_uri.host, api_uri.port)
                    request = Net::HTTP::Post.new(api_uri.path)
                    request.set_form_data(form_data)
                    http.request(request)
                  end 


                  sms_setting = SmsSetting.new()
                  if sms_setting.student_sms_active or sms_setting.parent_sms_active    
                    message = "Fees received BDT #AMOUNT# for #UNAME#(#UID#) as on #PAIDDATE# by TBL. TranID-#TRANID# TranRef-#TRANREF#, Sender - SAGC"
                    if File.exists?("#{Rails.root}/config/sms_text_#{MultiSchool.current_school.id}.yml")
                      sms_text_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/sms_text_#{MultiSchool.current_school.id}.yml")['school']
                      message = sms_text_config['feepaid']
                    end
                    recipients = []
                    unless @student.sms_number.nil? or @student.sms_number.empty? or @student.sms_number.blank?
                      message = message.gsub("#UNAME#", @student.full_name)
                      message = message.gsub("#UID#", @student.admission_no)
                      message = message.gsub("#AMOUNT#", amount_from_gateway.to_s)
                      message = message.gsub("#PAIDDATE#", trans_date.to_date.strftime("%d-%m-%Y"))
                      message = message.gsub("#TRANID#", orderId)
                      message = message.gsub("#TRANREF#", ref_id)
                      recipients.push @student.sms_number
                    else
                      unless @student.phone2.nil? or @student.phone2.empty? or @student.phone2.blank?
                        message = message
                        message = message.gsub("#UNAME#", @student.full_name)
                        message = message.gsub("#UID#", @student.admission_no)
                        message = message.gsub("#AMOUNT#", amount_from_gateway.to_s)
                        message = message.gsub("#PAIDDATE#", trans_date.to_date.strftime("%d-%m-%Y"))
                        message = message.gsub("#TRANID#", orderId)
                        message = message.gsub("#TRANREF#", ref_id)
                        recipients.push @student.phone2
                      end
                    end
                    messages = []
                    messages[0] = message
                    #sms = Delayed::Job.enqueue(SmsManager.new(message,recipients))
                    send_sms_finance(messages,recipients)
                  end


                  flash[:success] = "Thanks for your payment, payment was Successfull. Your order ID is: #{orderId}"
                else
                  flash[:notice] = "#{t('payment_failed')}"
                end
              else
                flash[:notice] = "#{t('payment_failed')}"
              end
            else
              flash[:notice] = "#{t('flash_payed')}"
            end

          end
        end
      end
    end
  end
    
  def send_sms_finance(multi_message, recipients)
    @recipients = recipients.map{|r| r.gsub(' ','')}
    @multi_message = multi_message
    @config = SmsSetting.get_sms_config
    unless @config.blank?
      @sendername = @config['sms_settings']['sendername']
      @sms_url = @config['sms_settings']['host_url']
      @username = @config['sms_settings']['username']
      @password = @config['sms_settings']['password']
      @success_code = @config['sms_settings']['success_code']
      @username_mapping = @config['parameter_mappings']['username']
      @username_mapping ||= 'username'
      @password_mapping = @config['parameter_mappings']['password']
      @password_mapping ||= 'password'
      @phone_mapping = @config['parameter_mappings']['phone']
      @phone_mapping ||= 'phone'
      @sender_mapping = @config['parameter_mappings']['sendername']
      @sender_mapping ||= 'sendername'
      @message_mapping = @config['parameter_mappings']['message']
      @message_mapping ||= 'message'
      unless @config['additional_parameters'].blank?
        @additional_param = ""
        @config['additional_parameters'].split(',').each do |param|
          @additional_param += "&#{param}"
        end
      end
    end

    if @config.present?
      @sms_hash = {"user"=>@username,"pass"=>@password,"sid" =>@sendername}

      i = 0
      @i_sms_loop = 0
      @recipients.each do |recipient|
       message = @multi_message[i]
       message_escape = CGI::escape message
       if @i_sms_loop == 3
         message_log = SmsMessage.new(:body=> message_escape)
         message_log.save
         message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>"Successfull")
         @sms_hash["sms[#{@i_sms_loop}][0]"] = recipient
         @sms_hash["sms[#{@i_sms_loop}][1]"] = message
         @sms_hash["sms[#{@i_sms_loop}][2]"] = @i_sms_loop

         api_uri = URI.parse(@sms_url)
         http = Net::HTTP.new(api_uri.host, api_uri.port)
         request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
         request.set_form_data(@sms_hash)

         http.request(request)

         sms_count = Configuration.find_by_config_key("TotalSmsCount")
         new_count = sms_count.config_value.to_i + 4
         sms_count.update_attributes(:config_value=>new_count)

         @sms_hash = {"user"=>@username,"pass"=>@password,"sid" =>@sendername}

         @i_sms_loop = 0
       elsif recipient.equal? @recipients.last
         message_log = SmsMessage.new(:body=> message_escape)
         message_log.save
         message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>"Successfull")
         @sms_hash["sms[#{@i_sms_loop}][0]"] = recipient
         @sms_hash["sms[#{@i_sms_loop}][1]"] = message
         @sms_hash["sms[#{@i_sms_loop}][2]"] = @i_sms_loop

         api_uri = URI.parse(@sms_url)
         http = Net::HTTP.new(api_uri.host, api_uri.port)
         request = Net::HTTP::Post.new(api_uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
         request.set_form_data(@sms_hash)
         http.request(request)

         sms_count = Configuration.find_by_config_key("TotalSmsCount")
         new_count = sms_count.config_value.to_i + 1+@i_sms_loop
         sms_count.update_attributes(:config_value=>new_count)
       else
         @sms_hash["sms[#{@i_sms_loop}][0]"] = recipient
         @sms_hash["sms[#{@i_sms_loop}][1]"] = message
         @sms_hash["sms[#{@i_sms_loop}][2]"] = @i_sms_loop
         message_log = SmsMessage.new(:body=> message_escape)
         message_log.save
         message_log.sms_logs.create(:mobile=>recipient,:gateway_response=>"Successfull")
         @i_sms_loop = @i_sms_loop+1
       end   

       i += 1
      end
    end
  end
  
  def arrange_particular_category_wise(date, student, transaction_particular, fee)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id_and_fee_collection_id(student.id,date.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(student.id,date.id).map(&:fee_particular_id)
    unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
      exclude_particular_ids = exclude_particular_ids
    else
      exclude_particular_ids = [0]
    end
    fee_particulars = date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
    total_payable=fee_particulars.map{|st| st.amount}.sum.to_f
    
    discount_amount = []
    discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
    unless discounts.blank?
      discounts.each do |d|
        if d.finance_fee_particular_category_id == 0
          d_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
          discount_amount[d.id] = d_amount
        else
          fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          unless fee_particulars_single.blank?
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            d_amount = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            discount_amount[d.id] = d_amount
          else
            discount_amount[d.id] = 0
          end
        end
      end
    end
    
    if transaction_particular.particular_type == "Particular"
      particular_id = transaction_particular.particular_id
      if transaction_particular.transaction_type == "Fee Collection" or transaction_particular.transaction_type == "Advance"
        finance_fee_particular = FinanceFeeParticular.find(:first, :conditions => "id = #{particular_id}")
        unless finance_fee_particular.blank?
          if finance_fee_particular.receiver_type == "StudentCategory"
            if finance_fee_particular.receiver_id.to_i != student.student_category_id.to_i
              finance_fee_particular_categories = FinanceFeeParticular.find(:all, :conditions => "finance_fee_category_id = #{finance_fee_particular.finance_fee_category_id} and finance_fee_particular_category_id = #{finance_fee_particular.finance_fee_particular_category_id} and batch_id = #{finance_fee_particular.batch_id} and receiver_type = 'StudentCategory' and receiver_id = #{student.student_category_id}")
              unless finance_fee_particular_categories.blank?
                finance_fee_particular_category = finance_fee_particular_categories[0]
                new_finance_fee_particular = copy_particular(finance_fee_particular_category, finance_fee_particular, student, fee)
                unless new_finance_fee_particular.nil?
                  exclude_current_category_particular(finance_fee_particular_category, student,fee)
                  #abort(new_finance_fee_particular.inspect)
                  transaction_particular.update_attributes(:particular_id => new_finance_fee_particular.id)
                end
              end
            end
          end
        end
      end
    elsif transaction_particular.particular_type == "Adjustment" or  transaction_particular.particular_type == "FineAdjustment"
      discount_id = transaction_particular.particular_id
      if transaction_particular.transaction_type == "Discount"
        fee_discount = FeeDiscount.find(:first, :conditions => "id = #{discount_id}")
        unless fee_discount.blank?
          if fee_discount.receiver_type == "StudentCategory"
            if fee_discount.receiver_id.to_i != student.student_category_id.to_i
              fee_discount_categories = FeeDiscount.find(:all, :conditions => "finance_fee_category_id = #{fee_discount.finance_fee_category_id} and finance_fee_particular_category_id = #{fee_discount.finance_fee_particular_category_id} and batch_id = #{fee_discount.batch_id} and receiver_type = 'StudentCategory' and receiver_id = #{student.student_category_id}")
              unless fee_discount_categories.blank?
                fee_discount_category = fee_discount_categories[0]
                current_discount_amount = 0
                unless discount_amount[fee_discount.id].blank?
                  current_discount_amount =  discount_amount[fee_discount.id]
                end
                new_fee_discount = copy_discount(fee_discount_category, fee_discount, student, fee, current_discount_amount)
                unless new_finance_fee_particular.nil?
                  exclude_current_category_discount(fee_discount_category, student,fee)
                  #abort(new_finance_fee_particular.inspect)
                  transaction_particular.update_attributes(:particular_id => new_fee_discount.id)
                end
              end
            end
          else
            if fee_discount.receiver_type == "Student" and fee_discount.is_amount == false
              fee_discount_categories = FeeDiscount.find(:all, :conditions => "finance_fee_category_id = #{fee_discount.finance_fee_category_id} and finance_fee_particular_category_id = #{fee_discount.finance_fee_particular_category_id} and batch_id = #{fee_discount.batch_id} and receiver_type = 'StudentCategory' and receiver_id = #{student.student_category_id}")
              unless fee_discount_categories.blank?
                fee_discount_category = fee_discount_categories[0]
                current_discount_amount = 0
                unless discount_amount[fee_discount.id].blank?
                  current_discount_amount =  discount_amount[fee_discount.id]
                end
                new_fee_discount = copy_discount(fee_discount_category, fee_discount, student, fee, current_discount_amount)
                unless new_finance_fee_particular.nil?
                  exclude_current_category_discount(fee_discount_category, student,fee)
                  #abort(new_finance_fee_particular.inspect)
                  transaction_particular.update_attributes(:particular_id => new_fee_discount.id)
                end
              end
            elsif fee_discount.receiver_type == "Batch" and fee_discount.is_amount == false
              fee_discount_categories = FeeDiscount.find(:all, :conditions => "finance_fee_category_id = #{fee_discount.finance_fee_category_id} and finance_fee_particular_category_id = #{fee_discount.finance_fee_particular_category_id} and batch_id = #{fee_discount.batch_id} and receiver_type = 'StudentCategory' and receiver_id = #{student.student_category_id}")
              unless fee_discount_categories.blank?
                fee_discount_category = fee_discount_categories[0]
                current_discount_amount = 0
                unless discount_amount[fee_discount.id].blank?
                  current_discount_amount =  discount_amount[fee_discount.id]
                end
                new_fee_discount = copy_discount(fee_discount_category, fee_discount, student, fee, current_discount_amount)
                unless new_finance_fee_particular.nil?
                  exclude_current_category_discount(fee_discount_category, student,fee)
                  #abort(new_finance_fee_particular.inspect)
                  transaction_particular.update_attributes(:particular_id => new_fee_discount.id)
                end
              end
            end
          end
        end
      end
    end
  end
  
  def adjust_particular_category_wise(date, student, transaction_particular, fee)
    exclude_discount_ids = StudentExcludeDiscount.find_all_by_student_id_and_fee_collection_id(student.id,date.id).map(&:fee_discount_id)
    unless exclude_discount_ids.nil? or exclude_discount_ids.empty? or exclude_discount_ids.blank?
      exclude_discount_ids = exclude_discount_ids
    else
      exclude_discount_ids = [0]
    end
    
    exclude_particular_ids = StudentExcludeParticular.find_all_by_student_id_and_fee_collection_id(student.id,date.id).map(&:fee_particular_id)
    unless exclude_particular_ids.nil? or exclude_particular_ids.empty? or exclude_particular_ids.blank?
      exclude_particular_ids = exclude_particular_ids
    else
      exclude_particular_ids = [0]
    end
    fee_particulars = date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
    total_payable=fee_particulars.map{|st| st.amount}.sum.to_f
    
    discount_amount = []
    discounts = date.fee_discounts.all(:conditions=>"fee_discounts.id not in (#{exclude_discount_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
    unless discounts.blank?
      discounts.each do |d|
        if d.finance_fee_particular_category_id == 0
          d_amount = total_payable * d.discount.to_f/ (d.is_amount?? total_payable : 100)
          discount_amount[d.id] = d_amount
        else
          fee_particulars_single = date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.id not in (#{exclude_particular_ids.join(",")}) and is_deleted=#{false} and batch_id=#{student.batch_id} and finance_fee_particular_category_id=#{d.finance_fee_particular_category_id}").select{|par| (par.receiver==student or par.receiver==student.student_category or par.receiver==student.batch) }
          unless fee_particulars_single.blank?
            payable_ampt = fee_particulars_single.map{|l| l.amount}.sum.to_f
            d_amount = payable_ampt * d.discount.to_f/ (d.is_amount?? payable_ampt : 100)
            discount_amount[d.id] = d_amount
          else
            discount_amount[d.id] = 0
          end
        end
      end
    end
    
    if transaction_particular.particular_type == "Particular"
      particular_id = transaction_particular.particular_id
      
      if transaction_particular.transaction_type == "Fee Collection" or transaction_particular.transaction_type == "Advance"
        finance_fee_particular = FinanceFeeParticular.find(:first, :conditions => "id = #{particular_id}")
        unless finance_fee_particular.blank?
          if finance_fee_particular.receiver_type == "StudentCategory"
            if finance_fee_particular.receiver_id.to_i != student.student_category_id.to_i
              finance_fee_particular_categories = FinanceFeeParticular.find(:all, :conditions => "finance_fee_category_id = #{finance_fee_particular.finance_fee_category_id} and finance_fee_particular_category_id = #{finance_fee_particular.finance_fee_particular_category_id} and batch_id = #{finance_fee_particular.batch_id} and receiver_type = 'StudentCategory' and receiver_id = #{student.student_category_id}")
              unless finance_fee_particular_categories.blank?
                finance_fee_particular_category = finance_fee_particular_categories[0]
                transaction_particular.update_attributes(:particular_id => finance_fee_particular_category.id)
              end
            end
          end
        end
      end
    elsif transaction_particular.particular_type == "Adjustment" or  transaction_particular.particular_type == "FineAdjustment"
      discount_id = transaction_particular.particular_id
      if transaction_particular.transaction_type == "Discount"
        fee_discount = FeeDiscount.find(:first, :conditions => "id = #{discount_id}")
        unless fee_discount.blank?
          if fee_discount.receiver_type == "StudentCategory"
            if fee_discount.receiver_id.to_i != student.student_category_id.to_i
              fee_discount_categories = FeeDiscount.find(:all, :conditions => "finance_fee_category_id = #{fee_discount.finance_fee_category_id} and finance_fee_particular_category_id = #{fee_discount.finance_fee_particular_category_id} and batch_id = #{fee_discount.batch_id} and receiver_type = 'StudentCategory' and receiver_id = #{student.student_category_id}")
              unless fee_discount_categories.blank?
                fee_discount_category = fee_discount_categories[0]
                current_discount_amount = 0
                unless discount_amount[fee_discount.id].blank?
                  current_discount_amount =  discount_amount[fee_discount.id]
                end
                new_fee_discount = copy_discount(fee_discount_category, fee_discount, student, fee, current_discount_amount)
                unless new_finance_fee_particular.nil?
                  exclude_current_category_discount(fee_discount_category, student,fee)
                  #abort(new_finance_fee_particular.inspect)
                  transaction_particular.update_attributes(:particular_id => new_fee_discount.id)
                end
              end
            end
          else
            if fee_discount.receiver_type == "Student" and fee_discount.is_amount == false
              fee_discount_categories = FeeDiscount.find(:all, :conditions => "finance_fee_category_id = #{fee_discount.finance_fee_category_id} and finance_fee_particular_category_id = #{fee_discount.finance_fee_particular_category_id} and batch_id = #{fee_discount.batch_id} and receiver_type = 'StudentCategory' and receiver_id = #{student.student_category_id}")
              unless fee_discount_categories.blank?
                fee_discount_category = fee_discount_categories[0]
                current_discount_amount = 0
                unless discount_amount[fee_discount.id].blank?
                  current_discount_amount =  discount_amount[fee_discount.id]
                end
                new_fee_discount = copy_discount(fee_discount_category, fee_discount, student, fee, current_discount_amount)
                unless new_finance_fee_particular.nil?
                  exclude_current_category_discount(fee_discount_category, student,fee)
                  #abort(new_finance_fee_particular.inspect)
                  transaction_particular.update_attributes(:particular_id => new_fee_discount.id)
                end
              end
            elsif fee_discount.receiver_type == "Batch" and fee_discount.is_amount == false
              fee_discount_categories = FeeDiscount.find(:all, :conditions => "finance_fee_category_id = #{fee_discount.finance_fee_category_id} and finance_fee_particular_category_id = #{fee_discount.finance_fee_particular_category_id} and batch_id = #{fee_discount.batch_id} and receiver_type = 'StudentCategory' and receiver_id = #{student.student_category_id}")
              unless fee_discount_categories.blank?
                fee_discount_category = fee_discount_categories[0]
                current_discount_amount = 0
                unless discount_amount[fee_discount.id].blank?
                  current_discount_amount =  discount_amount[fee_discount.id]
                end
                new_fee_discount = copy_discount(fee_discount_category, fee_discount, student, fee, current_discount_amount)
                unless new_finance_fee_particular.nil?
                  exclude_current_category_discount(fee_discount_category, student,fee)
                  #abort(new_finance_fee_particular.inspect)
                  transaction_particular.update_attributes(:particular_id => new_fee_discount.id)
                end
              end
            end
          end
        end
      end
    end
  end
  
  def exclude_current_category_discount(fee_discount_category, student, fee)
    student_exclude_discount = StudentExcludeDiscount.new
    student_exclude_discount.student_id = student.id
    student_exclude_discount.fee_discount_id = fee_discount_category.id
    student_exclude_discount.fee_collection_id = fee.fee_collection_id
    student_exclude_discount.save
  end
  
  def exclude_current_category_particular(finance_fee_particular_category, student, fee)
    student_exclude_particular = StudentExcludeParticular.new
    student_exclude_particular.student_id = student.id
    student_exclude_particular.fee_particular_id = finance_fee_particular_category.id
    student_exclude_particular.fee_collection_id = fee.fee_collection_id
    student_exclude_particular.save
  end
  
  def copy_particular(finance_fee_particular_category, finance_fee_particular, student, fee)
    new_finance_fee_particular = FinanceFeeParticular.find(:first, :conditions => "finance_fee_category_id = #{finance_fee_particular.finance_fee_category_id} and finance_fee_particular_category_id = #{finance_fee_particular.finance_fee_particular_category_id} and batch_id = #{finance_fee_particular.batch_id} and receiver_type = 'Student' and receiver_id = #{student.id} and parent_id = #{finance_fee_particular_category.id} and change_for = #{finance_fee_particular.id}")
    if new_finance_fee_particular.blank?
      new_finance_fee_particular = FinanceFeeParticular.new
      new_finance_fee_particular.name = finance_fee_particular.name
      new_finance_fee_particular.description = finance_fee_particular.description
      new_finance_fee_particular.amount = finance_fee_particular.amount
      new_finance_fee_particular.finance_fee_category_id = finance_fee_particular.finance_fee_category_id
      new_finance_fee_particular.finance_fee_particular_category_id = finance_fee_particular.finance_fee_particular_category_id
      new_finance_fee_particular.student_category_id = finance_fee_particular.student_category_id
      new_finance_fee_particular.admission_no = finance_fee_particular.admission_no
      new_finance_fee_particular.student_id = finance_fee_particular.student_id
      new_finance_fee_particular.parent_id = finance_fee_particular_category.id
      new_finance_fee_particular.change_for = finance_fee_particular.id
      new_finance_fee_particular.is_deleted = 0
      new_finance_fee_particular.receiver_id = student.id
      new_finance_fee_particular.receiver_type = "Student"
      new_finance_fee_particular.batch_id = finance_fee_particular.batch_id
      new_finance_fee_particular.is_tmp = 1
      new_finance_fee_particular.opt = finance_fee_particular.opt
      if new_finance_fee_particular.save
        collection_particular = CollectionParticular.new
        collection_particular.finance_fee_collection_id = fee.fee_collection_id
        collection_particular.finance_fee_particular_id = new_finance_fee_particular.id
        collection_particular.save
        return new_finance_fee_particular
      end
    else
      return new_finance_fee_particular
    end
    return nil
  end
  
  def copy_discount(fee_discount_category, fee_discount, student, fee, current_discount_amount)
    new_fee_discount = FeeDiscount.find(:first, :conditions => "finance_fee_category_id = #{finance_fee_particular.finance_fee_category_id} and finance_fee_particular_category_id = #{finance_fee_particular.finance_fee_particular_category_id} and batch_id = #{finance_fee_particular.batch_id} and receiver_type = 'Student' and receiver_id = #{student.id} and parent_id = #{finance_fee_particular_category.id} and change_for = #{finance_fee_particular.id}")
    if new_fee_discount.blank?
      new_fee_discount = FeeDiscount.new
      new_fee_discount.is_onetime = true
      new_fee_discount.name = fee_discount.name
      new_fee_discount.receiver_id = student.id
      new_fee_discount.scholarship_id = fee_discount.scholarship_id
      new_fee_discount.finance_fee_category_id = fee_discount.finance_fee_category_id
      new_fee_discount.finance_fee_particular_category_id = fee_discount.finance_fee_particular_category_id
      new_fee_discount.is_late = fee_discount.is_late
      new_fee_discount.is_visible = 0
      new_fee_discount.amount = current_discount_amount
      new_fee_discount.is_amount = true
      new_fee_discount.receiver_type = "Student"
      new_fee_discount.batch_id = fee_discount.batch_id
      new_fee_discount.parent_id = fee_discount.parent_id
      new_fee_discount.p_id = fee_discount_category.id
      new_fee_discount.change_for = fee_discount.id
      new_fee_discount.is_deleted = 0
      
      if new_fee_discount.save
        collection_discount = CollectionDiscount.new
        collection_discount.finance_fee_collection_id = fee.fee_collection_id
        collection_discount.fee_discount_id = new_fee_discount.id
        collection_discount.save
        return new_fee_discount
      end
    else
      return new_fee_discount
    end
    return nil
  end
  
  def reset_fees(date, student, fee)
    student_exclude_particulars = StudentExcludeParticular.find(:all, :conditions => "student_id = #{student.id} and fee_collection_id = #{fee.fee_collection_id}")
    unless student_exclude_particulars.blank?
      student_exclude_particulars.each do |student_exclude_particular|
        student_exclude_particular.destroy
      end
    end
    
    student_exclude_discounts = StudentExcludeDiscount.find(:all, :conditions => "student_id = #{student.id} and fee_collection_id = #{fee.fee_collection_id}")
    unless student_exclude_discounts.blank?
      student_exclude_discounts.each do |student_exclude_discount|
        student_exclude_discount.destroy
      end
    end
    
    finance_particulars = date.finance_fee_particulars.all(:conditions=>"finance_fee_particulars.parent_id != 0 and finance_fee_particulars.change_for != 0 and is_deleted=#{false} and batch_id=#{student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==student) }
    
    finance_particulars.each do |fp|
      particular_id = fp.change_for
      collection_particulars = CollectionParticular.find(:all, :conditions => "finance_fee_collection_id = #{fee.fee_collection_id} and finance_fee_particular_id = #{fp.id}")
      unless collection_particulars.nil?
        collection_particulars.each do |collection_particular|
          collection_particular.destroy
        end
      end

      transaction_particulars = FinanceTransactionParticular.find(:all, :conditions => "particular_id = #{fp.id} and particular_type = 'Particular' and transaction_type = 'Fee Collection'")
      unless transaction_particulars.nil?
        transaction_particulars.each do |transaction_particular|
          transaction_particular.update_attributes(:particular_id => particular_id)
        end
      end

      transaction_particulars = FinanceTransactionParticular.find(:all, :conditions => "particular_id = #{fp.id} and particular_type = 'Particular' and transaction_type = 'Advance'")
      unless transaction_particulars.nil?
        transaction_particulars.each do |transaction_particular|
          transaction_particular.update_attributes(:particular_id => particular_id)
        end
      end
      fp.destroy
    end
    
    finance_discounts = date.fee_discounts.all(:conditions=>"fee_discounts.p_id != 0 and fee_discounts.change_for != 0 and is_deleted=#{false} and batch_id=#{student.batch_id}").select{|par|  (par.receiver.present?) and (par.receiver==student) }
    
    finance_discounts.each do |d|
      discount_id = d.change_for
      collection_discounts = CollectionDiscount.find(:all, :conditions => "finance_fee_collection_id = #{fee.fee_collection_id} and fee_discount_id = #{d.id}")
      unless collection_discounts.nil?
        collection_discounts.each do |collection_discount|
          collection_discount.destroy
        end
      end

      transaction_discounts = FinanceTransactionParticular.find(:all, :conditions => "particular_id = #{d.id} and particular_type = 'Adjustment' and transaction_type = 'Discount'")
      unless transaction_discounts.nil?
        transaction_discounts.each do |transaction_discount|
          transaction_discount.update_attributes(:particular_id => discount_id)
        end
      end

      transaction_discounts = FinanceTransactionParticular.find(:all, :conditions => "particular_id = #{d.id} and particular_type = 'FineAdjustment' and transaction_type = 'Discount'")
      unless transaction_discounts.nil?
        transaction_discounts.each do |transaction_discount|
          transaction_discount.update_attributes(:particular_id => discount_id)
        end
      end
      d.destroy
    end
    
    bal = FinanceFee.get_student_actual_balance(date, student, fee)
    fee.update_attributes(:balance=>bal)
    if bal.to_f == 0.00
      fee.update_attributes(:is_paid=>true)
    end
  end
  
end
