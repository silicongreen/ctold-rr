require 'i18n'
class DelayedFeeCollectionJob
  attr_accessor :user,:collection,:fee_collection
  def initialize(user,collection,fee_collection, sent_remainder,include_transport, include_employee)
    @user = user
    @collection = collection
    @fee_collection = fee_collection
    @sent_remainder = sent_remainder
    @include_transport = include_transport
    @include_employee = include_employee
  end
  include I18n
  def t(obj)
    I18n.t(obj)
  end
  def perform

    unless @fee_collection.nil?
      category = @fee_collection[:category_ids]
      subject = "#{t('fees_submission_date')}"

      @finance_fee_collection = FinanceFeeCollection.new(
        :name => @collection[:name],
        :start_date => @collection[:start_date],
        :end_date => @collection[:end_date],
        :due_date => @collection[:due_date],
        :fee_category_id => @collection[:fee_category_id],
        :include_transport => @include_transport,
        :fine_id=>@collection[:fine_id]
      )
      FinanceFeeCollection.transaction do
        if @finance_fee_collection.save
          new_event =  Event.create(:title=> "Fees Due", :description =>@collection[:name], :start_date => @finance_fee_collection.due_date.to_datetime, :end_date => @finance_fee_collection.due_date.to_datetime, :is_due => true , :origin=>@finance_fee_collection)
          category.each do |b|
            b=b.to_i
            FeeCollectionBatch.create(:finance_fee_collection_id=>@finance_fee_collection.id,:batch_id=>b)
            fee_category_name = @collection[:fee_category_id]
            @students = Student.find_all_by_batch_id(b)
            @fee_category= FinanceFeeCategory.find_by_id(@collection[:fee_category_id])

            unless @fee_category.fee_particulars.all(:conditions=>"is_deleted=false and batch_id=#{b}").collect(&:receiver_type).include?"Batch"
              cat_ids=@fee_category.fee_particulars.select{|s| s.receiver_type=="StudentCategory"  and (!s.is_deleted and s.batch_id==b.to_i)}.collect(&:receiver_id)
              student_ids=@fee_category.fee_particulars.select{|s| s.receiver_type=="Student" and (!s.is_deleted and s.batch_id==b.to_i)}.collect(&:receiver_id)
              @students = @students.select{|stu| (cat_ids.include?stu.student_category_id or student_ids.include?stu.id)}
            end
            body = "<p><b>#{t('fee_submission_date_for')} <i>"+fee_category_name+"</i> #{t('has_been_published')} </b>
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
          
          if @include_transport
            @batchs = @fee_collection[:category_ids]
            @transport_fee_collection = TransportFeeCollection.new(
              :name => @collection[:name] + " - Transport",
              :fee_collection_id => @finance_fee_collection.id,
              :start_date => @collection[:start_date],
              :end_date => @collection[:end_date],
              :due_date => @collection[:due_date]
            )
            unless @transport_fee_collection.valid?
              @error = true
            end
            unless @batchs.blank? 
              unless @batchs.blank?
                @batchs.each do |b|
                  batch = Batch.find(b)
                  @transport_fee_collection = TransportFeeCollection.new(
                      :name => @collection[:name] + " - Transport",
                      :fee_collection_id => @finance_fee_collection.id,
                      :start_date => @collection[:start_date],
                      :end_date => @collection[:end_date],
                      :due_date => @collection[:due_date],
                      :batch_id => b
                  )

                  if @transport_fee_collection.save
                    @event= Event.create(:title=> "#{t('transport_fee_text')}", :description=> "#{t('fee_name')}: #{@collection[:name]} - Transport", :start_date=> @collection[:due_date], :end_date=> @collection[:due_date], :is_due => true, :origin=>@transport_fee_collection)
                    recipients = []
                    subject = "#{t('fees_submission_date')}"
                    body = "<p><b>#{t('fee_submission_date_for')} <i>"+ "#{@transport_fee_collection.name}" +"</i> #{t('has_been_published')} </b><br /><br/>
                                    #{t('start_date')} : "+@transport_fee_collection.start_date.to_s+" <br />"+
                      " #{t('end_date')} :"+@transport_fee_collection.end_date.to_s+" <br /> "+
                      " #{t('due_date')} :"+@transport_fee_collection.due_date.to_s+" <br /><br /><br /> "+
                      "#{t('regards')}, <br/>" + @user.full_name.capitalize
                    batch.active_transports.each do |t|
                      student = t.receiver
                      unless student.nil?
                        recipients << student.user.id
                        TransportFee.create(:receiver =>student, :bus_fare => t.bus_fare, :transport_fee_collection_id=>@transport_fee_collection.id)
                        UserEvent.create(:event_id=> @event.id, :user_id => student.user.id)
                      end
                    end

                    #if sent_remainder
                    #  Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => @user.id,
                    #      :recipient_ids => recipients,
                    #      :subject=>subject,
                    #      :body=>body ))
                    #end
                  else
                    @error = true
                  end
                end
              end
              if @include_employee
                @transport_fee_collection = TransportFeeCollection.new(
                      :name => @collection[:name] + " - Transport",
                      :fee_collection_id => @finance_fee_collection.id,
                      :start_date => @collection[:start_date],
                      :end_date => @collection[:end_date],
                      :due_date => @collection[:due_date]
                )
                if @transport_fee_collection.save
                  recipients = []
                  @event=Event.create(:title=> "#{t('transport_fee_text')}", :description=> "#{t('fee_name')}: #{@collection[:name]} - Transport", :start_date=> @collection[:due_date], :end_date=> @collection[:due_date], :is_due => true, :origin=>@transport_fee_collection)
                  subject = "#{t('fees_submission_date')}"
                  body = "<p><b>#{t('fee_submission_date_for')} <i>"+ "#{@transport_fee_collection.name}" +"</i> #{t('has_been_published')} </b><br /><br/>
                                    #{t('start_date')} : "+@transport_fee_collection.start_date.to_s+" <br />"+
                    " #{t('end_date')} :"+@transport_fee_collection.end_date.to_s+" <br /> "+
                    " #{t('due_date')} :"+@transport_fee_collection.due_date.to_s+" <br /><br /><br /> "+
                    "#{t('regards')}, <br/>" + @user.full_name.capitalize
                  employee_transport = Transport.find(:all,:include => :vehicle, :conditions => ["receiver_type = 'Employee' AND vehicles.status = ?", "Active"])
                  employee_transport.each do |t|
                    emp = t.receiver
                    unless emp.nil?
                      TransportFee.create(:receiver =>emp,  :bus_fare => t.bus_fare, :transport_fee_collection_id=>@transport_fee_collection.id)
                      UserEvent.create(:event_id=> @event.id, :user_id => emp.user.id)
                      recipients << emp.user.id
                    end
                  end
                  #if sent_remainder
                  #  Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => @user.id,
                  #      :recipient_ids => recipients,
                  #      :subject=>subject,
                  #      :body=>body ))
                  #end
                else
                  @error = true
                end
              end
            else
              @error = true
              @transport_fee_collection.errors.add_to_base("#{t('please_select_a_batch_or_emp')}")
            end
          end
          
        else
          @error = true
          raise ActiveRecord::Rollback
        end

      end
    end
  end

end

