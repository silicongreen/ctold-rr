class FeeImportsController < ApplicationController
  before_filter :login_required
  filter_access_to :all
  
  def import_fees
    @student = Student.find_by_id(params[:id])
    #@fee_collection_dates = FinanceFeeCollection.find(:all,:joins=>"INNER JOIN fee_collection_batches on fee_collection_batches.finance_fee_collection_id=finance_fee_collections.id",:conditions=>"fee_collection_batches.batch_id=#{@student.batch_id} and finance_fee_collections.is_deleted=false")
   #@fee_collection_dates = FinanceFeeCollection.find(:all,:joins=>"INNER JOIN finance_fee_particulars on finance_fee_particulars.finance_fee_category_id=finance_fee_collections.fee_category_id and finance_fee_particulars.receiver_type='Batch'",:conditions=>"finance_fee_particulars.batch_id='#{@student.batch_id}' and finance_fee_collections.is_deleted=false and finance_fee_particulars.is_deleted=false").uniq
   @fee_collection_dates =FinanceFeeParticular.find(:all,:joins=>"INNER JOIN collection_particulars on collection_particulars.finance_fee_particular_id=finance_fee_particulars.id INNER JOIN finance_fee_collections on finance_fee_collections.id=collection_particulars.finance_fee_collection_id",:conditions=>"finance_fee_particulars.batch_id='#{@student.batch_id}' and finance_fee_particulars.receiver_type='Batch'",:select=>"finance_fee_collections.*").uniq
    if @fee_collection_dates.blank?
      flash[:notice] = t('add_the_additional_details')
      redirect_to :controller => "student", :action => "admission4", :id => @student.id,:imported=>'1'
    end
    if request.post?
      unless params[:fees].nil?
        dates = FinanceFeeCollection.find(params[:fees][:collection_ids])
        unless @student.has_paid_fees
        dates.each do |date|
          FinanceFee.new_student_fee(date,@student)
          #FinanceFee.create(:student_id => @student.id,:fee_collection_id => date) unless date.nil?
        end
        end
        flash[:notice] = "#{t('add_the_additional_details')}"
        redirect_to :controller => "student", :action => "admission4", :id => @student.id, :imported=>'1'
      else
        flash[:notice] = "#{t('please_select_fee_collection')}"
        redirect_to :action => 'import_fees', :id=>@student.id
      end
    end
  end

  def select_student
    @batches = Batch.active
    if request.post?
      if params[:fees_list].present?
        if params[:fees_list][:multiple].to_i == 0
          @student = Student.find_by_id(params[:fees_list][:student_id])
          @batch_selected=@student.batch
          collection_dates
          @finance_fees = FinanceFee.find_all_by_student_id(@student.id)
          @student_fees = @finance_fees.map{|s| s.fee_collection_id}
          #@payed_fees = @finance_fees.map{|s| s.fee_collection_id unless s.transaction_id.nil? }.compact
          @payed_fees=FinanceFee.find(:all,:joins=>"INNER JOIN fee_transactions on fee_transactions.finance_fee_id=finance_fees.id INNER JOIN finance_fee_collections on finance_fee_collections.id=finance_fees.fee_collection_id",:conditions=>"finance_fees.student_id=#{@student.id}",:select=>"finance_fees.fee_collection_id").map{|s| s.fee_collection_id}
          @payed_fees ||= []
          dates = []
          dates = params[:fees_list][:collection_ids].to_a unless params[:fees_list].nil?
          @fee_collection_dates.each do |date|
            if @student_fees.include?(date.id)
              unless dates.include?(date.id.to_s)
                fee = FinanceFee.find_by_student_id_and_fee_collection_id(@student.id, date.id)
                fee.destroy if fee.finance_transactions.empty?
              end
            else

              if dates.include?(date.id.to_s)
                FinanceFee.new_student_fee(date,@student)
  #              finance_fee= FinanceFee.create(:student_id => @student.id,:fee_collection_id => date.id)
  #              @fee_particulars = date.finance_fee_particulars.select{|par| par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch}
  #              @discounts=date.fee_discounts.select{|par| par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch}
  #
  #              @total_discount = 0
  #              @total_payable=@fee_particulars.map{|l| l.amount}.sum.to_f
  #              @total_discount =@discounts.map{|d| @total_payable * d.discount.to_f/(d.is_amount? ? @total_payable : 100)}.sum.to_f unless @discounts.nil?
  #              balance=@total_payable-@total_discount
  #              finance_fee.update_attributes(:balance=>balance)
                flash[:notice]="#{t('selected_fees_assigned_to_the_student_successfully')}"
              end

            end
          end
          @students = Student.find_all_by_batch_id(@student.batch_id, :order => 'first_name ASC')
          @finance_fees = FinanceFee.find_all_by_student_id(@student.id)
          @student_fees = @finance_fees.map{|s| s.fee_collection_id}
          @payed_fees=FinanceFee.find(:all,:joins=>"INNER JOIN fee_transactions on fee_transactions.finance_fee_id=finance_fees.id INNER JOIN finance_fee_collections on finance_fee_collections.id=finance_fees.fee_collection_id",:conditions=>"finance_fees.student_id=#{@student.id}",:select=>"finance_fees.fee_collection_id").map{|s| s.fee_collection_id}
          @payed_fees ||= []
        else
          @batch_data = Batch.find params[:fees_list][:batch_id_selected]
          @student_ids = params[:fees_list][:students]
          unless @student_ids.blank?
            @student_ids.each do |student_id|
              @student = Student.find_by_id(student_id.to_i)
              
              @batch_selected=@student.batch
              collection_dates_new
              @finance_fees = FinanceFee.find_all_by_student_id(@student.id)
              @student_fees = @finance_fees.map{|s| s.fee_collection_id}
              #@payed_fees = @finance_fees.map{|s| s.fee_collection_id unless s.transaction_id.nil? }.compact
              @payed_fees=FinanceFee.find(:all,:joins=>"INNER JOIN fee_transactions on fee_transactions.finance_fee_id=finance_fees.id INNER JOIN finance_fee_collections on finance_fee_collections.id=finance_fees.fee_collection_id",:conditions=>"finance_fees.student_id=#{@student.id}",:select=>"finance_fees.fee_collection_id").map{|s| s.fee_collection_id}
              @payed_fees ||= []
              dates = []
              dates = params[:fees_list][:collection_ids].to_a unless params[:fees_list].nil?
              @fee_collection_dates.each do |date|
                if @student_fees.include?(date.id)
                  unless dates.include?(date.id.to_s)
                    fee = FinanceFee.find_by_student_id_and_fee_collection_id(@student.id, date.id)
                    unless fee.blank?
                      fee.destroy if fee.finance_transactions.empty?
                    end
                  end
                else

                  if dates.include?(date.id.to_s)
                    FinanceFee.new_student_fee(date,@student)
      #              finance_fee= FinanceFee.create(:student_id => @student.id,:fee_collection_id => date.id)
      #              @fee_particulars = date.finance_fee_particulars.select{|par| par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch}
      #              @discounts=date.fee_discounts.select{|par| par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch}
      #
      #              @total_discount = 0
      #              @total_payable=@fee_particulars.map{|l| l.amount}.sum.to_f
      #              @total_discount =@discounts.map{|d| @total_payable * d.discount.to_f/(d.is_amount? ? @total_payable : 100)}.sum.to_f unless @discounts.nil?
      #              balance=@total_payable-@total_discount
      #              finance_fee.update_attributes(:balance=>balance)
                    flash[:notice]="#{t('selected_fees_assigned_to_the_student_successfully')}"
                  end

                end
              end
            end
          end
          @batches = Batch.active
          @students = Student.find_all_by_batch_id(@batch.id,:conditions=>"has_paid_fees=#{false}", :order => 'first_name ASC')
          #collection_dates_new
          #already_assigned_ids = [2569,2570,2571,2644]
          already_assigned_ids = []
          @student_fees = already_assigned_ids #@fee_collection_dates.map{|fc| fc.id}
          @payed_fees ||= []
        end
      end
    end
  end

  def list_students_by_batch
    unless params[:batch][:batch_id].blank?
      @batch = Batch.find params[:batch][:batch_id]
      if params[:query].blank?
        @students = Student.find_all_by_batch_id(params[:batch][:batch_id],:conditions=>"has_paid_fees=#{false}", :order => 'first_name ASC')
      else
        @students = Student.active.find(:all,
        :conditions => ["(first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                        OR admission_no LIKE ? OR (concat(first_name, \" \", last_name) LIKE ? ) OR (concat(first_name, \"+\", last_name) LIKE ? )) and batch_id = ? ",
        "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
        "%#{params[:query]}%", "%#{params[:query]}%", "%#{params[:query]}%", params[:batch][:batch_id] ],
        :order => "batch_id asc,first_name asc") unless params[:query] == ''
      end  
    else
      @students = Student.active.find(:all,
      :conditions => ["first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?
                        OR admission_no LIKE ? OR (concat(first_name, \" \", last_name) LIKE ? ) OR (concat(first_name, \"+\", last_name) LIKE ? ) ",
        "#{params[:query]}%","#{params[:query]}%","#{params[:query]}%",
        "%#{params[:query]}%", "%#{params[:query]}%", "%#{params[:query]}%" ],
      :order => "batch_id asc,first_name asc") unless params[:query] == ''
    end  
    
    render :partial => 'summary_transaction'
  end

  def list_fees_for_student
    @student = Student.find_by_id(params[:student])
    collection_dates
    @finance_fees = FinanceFee.find_all_by_student_id(@student.id)
    @student_fees = @finance_fees.map{|s| s.fee_collection_id}
    @payed_fees=FinanceFee.find(:all,:joins=>"INNER JOIN fee_transactions on fee_transactions.finance_fee_id=finance_fees.id INNER JOIN finance_fee_collections on finance_fee_collections.id=finance_fees.fee_collection_id",:conditions=>"finance_fees.student_id=#{@student.id}",:select=>"finance_fees.fee_collection_id").map{|s| s.fee_collection_id}
    # @payed_fees = @finance_fees.map{|s| s.fee_collection_id unless s.transaction_id.nil? }.compact
    @payed_fees ||= []
    render :update do |page|
      page.replace_html 'fees_list', :partial => 'fees_list'
    end
  end
  
  def collection_dates
    @fee_collection_dates=[]
    if MultiSchool.current_school.id == 3481
      @fee_collection_date = @batch.finance_fee_collections
    else
      @fee_collection_date = @student.batch.finance_fee_collections
    end
    
    
    @fee_collection_date.each do |f|
      flag=0
      particulars=f.finance_fee_particulars.all(:conditions=>"batch_id=#{@student.batch_id} ").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
      particulars.each do |p|
        if p.receiver==@student or p.receiver=@student.student_category or p.receiver=@student.batch
          flag=1
        end

        #        unless p.admission_no.nil?
        #          flag=1
        #          if p.admission_no==@student.admission_no
        #            flag=0
        #          end
        #        else
        #          flag=0
        #        end

      end
      
      if MultiSchool.current_school.id == 3481
        @fee_collection_dates << f
      end
      
      if flag==1
        @fee_collection_dates << f
      end

    end

  end
  
  def collection_dates_new
    @fee_collection_dates=[]
    if MultiSchool.current_school.id == 3481
      @fee_collection_date = @batch_data.finance_fee_collections
    else
      @fee_collection_date = @student.batch.finance_fee_collections
    end
    
    
    @fee_collection_date.each do |f|
      flag=0
      particulars=f.finance_fee_particulars.all(:conditions=>"batch_id=#{@student.batch_id} ").select{|par|  (par.receiver.present?) and (par.receiver==@student or par.receiver==@student.student_category or par.receiver==@student.batch) }
      particulars.each do |p|
        if p.receiver==@student or p.receiver=@student.student_category or p.receiver=@student.batch
          flag=1
        end

        #        unless p.admission_no.nil?
        #          flag=1
        #          if p.admission_no==@student.admission_no
        #            flag=0
        #          end
        #        else
        #          flag=0
        #        end

      end
      
      if MultiSchool.current_school.id == 3481
        @fee_collection_dates << f
      end
      
      if flag==1
        @fee_collection_dates << f
      end

    end

  end
end
