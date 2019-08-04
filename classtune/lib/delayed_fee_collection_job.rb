require 'i18n'
class DelayedFeeCollectionJob
  attr_accessor :user,:collection,:fee_collection, :sent_remainder, :particular_ids, :particular_names, :transport_particular_id, :transport_particular_name
  def initialize(user,collection,fee_collection, sent_remainder, particular_ids, particular_names,transport_particular_id, transport_particular_name, default_particular_names, default_transport_particular_name)
    @user = user
    @collection = collection
    @fee_collection = fee_collection
    @sent_remainder = sent_remainder
    @particular_ids = particular_ids
    @particular_names = particular_names
    @default_particular_names = default_particular_names
    @transport_particular_id = transport_particular_id
    @transport_particular_name = transport_particular_name
    @default_transport_particular_name = default_transport_particular_name
  end
  include I18n
  def t(obj)
    I18n.t(obj)
  end
  def perform

    finance_fee_category_id = @collection[:fee_category_id]
    if finance_fee_category_id.to_i != 0
      finance_fee_category = FinanceFeeCategory.find(finance_fee_category_id)
      new_finance_fee_category = FinanceFeeCategory.new
      new_finance_fee_category.name = @collection[:name]
      new_finance_fee_category.is_master = finance_fee_category.is_master
      new_finance_fee_category.is_visible = 0
      new_finance_fee_category.parent_id = finance_fee_category_id
    else
      new_finance_fee_category = FinanceFeeCategory.new
      new_finance_fee_category.name = "Common"
      new_finance_fee_category.is_master = true
      new_finance_fee_category.is_visible = 0
      new_finance_fee_category.parent_id = finance_fee_category_id
    end
    if new_finance_fee_category.save
      finance_fees_auto_category = FinanceFeesAutoCategory.new
      finance_fees_auto_category.finance_fee_category_id = finance_fee_category_id
      finance_fees_auto_category.finance_fee_auto_category_id = new_finance_fee_category.id
      finance_fees_auto_category.save
      #abort('here')
      @collection[:fee_category_id] = new_finance_fee_category.id

      unless @fee_collection.nil?
        category = @fee_collection[:category_ids]
        subject = "#{t('fees_submission_date')}"

        @finance_fee_collection = FinanceFeeCollection.new(
          :name => @collection[:name],
          :title => @collection[:title],
          :start_date => @collection[:start_date],
          :end_date => @collection[:end_date],
          :due_date => @collection[:due_date],
          :fee_category_id => @collection[:fee_category_id],
          :fine_id=>@collection[:fine_id]
        )
        FinanceFeeCollection.transaction do
          if @finance_fee_collection.save
            @particular_ids.each_with_index do |p_id, i|
              finance_fee_particulars = FinanceFeeParticular.find(:all, :conditions => "finance_fee_particular_category_id = #{p_id.to_i} and finance_fee_category_id = #{finance_fee_category_id} and is_deleted = #{false}")
              unless finance_fee_particulars.nil?
                finance_fee_particulars.each do |ffp|
                  p_name = ffp.name
                  default_particular_name = @default_particular_names[i]
                  if default_particular_name.to_i == 0
                    particular_name = @particular_names[i]
                    p_names = particular_name.split("_")
                    p_name_id = p_names[0]
                    if p_name_id.to_i == ffp.finance_fee_particular_category_id.to_i
                      p_name = particular_name.gsub(p_id.to_s + "_", "")
                    end
                  end
                  execute = true
                  if ffp.receiver_type == "Batch"
                    particular_batch = Batch.find(:first, :conditions => "id = #{ffp.receiver_id}")
                    if particular_batch.blank?
                      execute = false
                    end
                  elsif ffp.receiver_type == "StudentCategory"
                    particular_student_category = StudentCategory.find(:first, :conditions => "id = #{ffp.receiver_id}")
                    if particular_student_category.blank?
                      execute = false
                    end
                  elsif ffp.receiver_type == "Student"
                    particular_student = Student.find(:first, :conditions => "id = #{ffp.receiver_id}")
                    if particular_student.blank?
                      execute = false
                    end
                  end
                  if execute
                    finance_fee_particular_new = FinanceFeeParticular.new
                    finance_fee_particular_new.name = p_name
                    finance_fee_particular_new.amount = ffp.amount
                    finance_fee_particular_new.finance_fee_category_id = new_finance_fee_category.id
                    finance_fee_particular_new.finance_fee_particular_category_id = ffp.finance_fee_particular_category_id
                    finance_fee_particular_new.receiver_id = ffp.receiver_id
                    finance_fee_particular_new.receiver_type = ffp.receiver_type
                    finance_fee_particular_new.batch_id = ffp.batch_id
                    finance_fee_particular_new.is_tmp = ffp.is_tmp
                    finance_fee_particular_new.opt = 0
                    finance_fee_particular_new.save
                  end
                end
              end
            end

            unless @transport_particular_id.blank?
              @transport_particular_id.each_with_index do |p_id, i|
                @vehicles = Vehicle.all
                unless @vehicles.blank?
                  @vehicles.each do |vehicle|
                    @transports = Transport.find_all_by_vehicle_id(vehicle.id)
                    unless @transports.nil?
                      @transports.each do |transport|
                        default_transport_particular_name = @default_transport_particular_name[i]
                        if default_transport_particular_name.to_i == 1
                          p_name = "Transport Fee"
                        else  
                          particular_name = @transport_particular_name[i]
                          if particular_name.blank?
                            p_name = "Transport Fee"
                          else
                            p_names = particular_name.split("_")
                            p_name_id = p_names[0]
                            if p_name_id.to_i == p_id.to_i
                              p_name = particular_name.gsub(p_id.to_s + "_", "")
                            end
                          end
                        end
                        s = Student.find(:first, :conditions => "id = #{transport.receiver_id}")
                        unless s.blank?
                          finance_fee_particular_new = FinanceFeeParticular.new
                          finance_fee_particular_new.name = p_name
                          finance_fee_particular_new.description = "--Vehical No: ---" + vehicle.vehicle_no + ", --Route: ---" + transport.route.destination unless transport.route.nil?
                          finance_fee_particular_new.amount = transport.bus_fare
                          finance_fee_particular_new.finance_fee_category_id = new_finance_fee_category.id
                          finance_fee_particular_new.finance_fee_particular_category_id = p_id
                          finance_fee_particular_new.receiver_id = transport.receiver_id
                          finance_fee_particular_new.receiver_type = 'Student'
                          finance_fee_particular_new.batch_id = s.batch_id
                          finance_fee_particular_new.is_tmp = 0
                          finance_fee_particular_new.opt = 0
                          finance_fee_particular_new.save
                        end
                      end
                    end
                  end
                end
              end
            end

            fee_discounts = FeeDiscount.all(:conditions=>"finance_fee_category_id=#{finance_fee_category_id} and is_deleted = #{false}")
            unless fee_discounts.nil?
              fee_discounts.each do |f|
                execute = true
                if f.receiver_type == "Batch"
                  discount_batch = Batch.find(:first, :conditions => "id = #{f.receiver_id}")
                  if discount_batch.blank?
                    execute = false
                  end
                elsif f.receiver_type == "StudentCategory"
                  discount_student_category = StudentCategory.find(:first, :conditions => "id = #{f.receiver_id}")
                  if discount_student_category.blank?
                    execute = false
                  end
                elsif f.receiver_type == "Student"
                  discount_student = Student.find(:first, :conditions => "id = #{f.receiver_id}")
                  if discount_student.blank?
                    execute = false
                  end
                end
                if execute
                  fee_discount_new = FeeDiscount.new
                  fee_discount_new.name = f.name
                  fee_discount_new.type = f.type
                  fee_discount_new.is_onetime = f.is_onetime
                  fee_discount_new.receiver_id = f.receiver_id
                  fee_discount_new.scholarship_id = f.scholarship_id
                  fee_discount_new.finance_fee_category_id = new_finance_fee_category.id
                  fee_discount_new.finance_fee_particular_category_id = f.finance_fee_particular_category_id
                  fee_discount_new.is_late = f.is_late
                  fee_discount_new.is_visible = f.is_visible
                  fee_discount_new.is_amount = f.is_amount
                  fee_discount_new.discount = f.discount
                  fee_discount_new.receiver_type = f.receiver_type
                  fee_discount_new.batch_id = f.batch_id
                  fee_discount_new.parent_id = f.id
                  fee_discount_new.save
                end
              end
            end

            new_event =  Event.create(:title=> "Fees Due", :description =>@collection[:name], :start_date => @finance_fee_collection.due_date.to_datetime, :end_date => @finance_fee_collection.due_date.to_datetime, :is_due => true , :origin=>@finance_fee_collection)
            category.each do |b|
              b=b.to_i
              FeeCollectionBatch.create(:finance_fee_collection_id=>@finance_fee_collection.id,:batch_id=>b)
              fee_category_name = @collection[:fee_category_id]
              @students = Student.find_all_by_batch_id(b)
              @fee_category= FinanceFeeCategory.find_by_id(@collection[:fee_category_id])

              unless @fee_category.fee_particulars.all(:conditions=>"is_tmp = 0 and is_deleted=false and batch_id=#{b}").collect(&:receiver_type).include?"Batch"
                cat_ids=@fee_category.fee_particulars.select{|s| s.receiver_type=="StudentCategory"  and (!s.is_deleted and s.batch_id==b.to_i)}.collect(&:receiver_id)
                student_ids=@fee_category.fee_particulars.select{|s| s.receiver_type=="Student" and (!s.is_deleted and s.batch_id==b.to_i)}.collect(&:receiver_id)
                @students = @students.select{|stu| (cat_ids.include?stu.student_category_id or student_ids.include?stu.id)}
              end
              body = "<p><b>#{t('fee_submission_date_for')} <i>"+fee_category_name.to_s+"</i> #{t('has_been_published')} </b>
                \n \n  #{t('start_date')} : "+@finance_fee_collection.start_date.to_s+" \n"+
                " #{t('end_date')} :"+@finance_fee_collection.end_date.to_s+" \n "+
                " #{t('due_date')} :"+@finance_fee_collection.due_date.to_s+" \n \n \n "+
                " #{t('check_your')}  #{t('fee_structure')}"


              recipient_ids = []

              @students.each do |s|

                unless s.has_paid_fees
                  FinanceFee.new_student_fee(@finance_fee_collection,s)

                  recipient_ids << s.user.id if s.user
                  recipient_ids << s.immediate_contact.user_id if s.immediate_contact.present?
                end
              end
              recipient_ids = recipient_ids.compact
              BatchEvent.create(:event_id => new_event.id, :batch_id => b )
              if  @sent_remainder
                Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => @user.id,
                    :recipient_ids => recipient_ids,
                    :subject=>subject,
                    :body=>body ))
              end

              prev_record = Configuration.find_by_config_key("job/FinanceFeeCollection/1")
              if prev_record.present?
                prev_record.update_attributes(:config_value=>Time.now)
              else
                Configuration.create(:config_key=>"job/FinanceFeeCollection/1", :config_value=>Time.now)
              end
            end
          else
            @error = true
            new_finance_fee_category.destroy
            raise ActiveRecord::Rollback
          end

        end
      end
    end
  end

end

