require 'i18n'
class DelayedFeeCollectionJob
  attr_accessor :user,:collection,:fee_collection
  def initialize(user,collection,fee_collection, sent_remainder,include_transport, include_employee, particular_ids, particular_names,transport_particular_id, transport_particular_name)
    @user = user
    @collection = collection
    @fee_collection = fee_collection
    @sent_remainder = sent_remainder
    @include_transport = include_transport
    @include_employee = include_employee
    @particular_ids = particular_ids
    @particular_names = particular_names
    @transport_particular_id = transport_particular_id
    @transport_particular_name = transport_particular_name
  end
  include I18n
  def t(obj)
    I18n.t(obj)
  end
  def perform

    finance_fee_category_id = @collection[:fee_category_id]
    finance_fee_category = FinanceFeeCategory.find(finance_fee_category_id)
    new_finance_fee_category = FinanceFeeCategory.new
    new_finance_fee_category.name = @collection[:name]
    new_finance_fee_category.is_master = finance_fee_category.is_master
    new_finance_fee_category.is_visible = 0
    new_finance_fee_category.save
    
    #abort('here')
    @collection[:fee_category_id] = new_finance_fee_category.id
    
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
          @particular_ids.each_with_index do |p_id, i|
            finance_fee_particulars = FinanceFeeParticular.find(:all, :conditions => "finance_fee_particular_category_id = #{p_id.to_i} and finance_fee_category_id = #{finance_fee_category_id}")
            unless finance_fee_particulars.nil?
              finance_fee_particulars.each do |ffp|
                p_name = ffp.name
                particular_name = @particular_names[i]
                p_names = particular_name.split("_")
                p_name_id = p_names[0]
                if p_name_id.to_i == ffp.finance_fee_particular_category_id.to_i
                  p_name = particular_name.gsub(p_id.to_s + "_", "")
                end
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
          
          @transport_particular_id.each_with_index do |p_id, i|
            finance_fee_particulars = FinanceFeeParticular.find(:all, :conditions => "finance_fee_particular_category_id = #{p_id.to_i} and finance_fee_category_id = 0")
            unless finance_fee_particulars.nil?
              finance_fee_particulars.each do |ffp|
                p_name = ffp.name
                particular_name = @transport_particular_name[i]
                p_names = particular_name.split("_")
                p_name_id = p_names[0]
                if p_name_id.to_i == ffp.finance_fee_particular_category_id.to_i
                  p_name = particular_name.gsub(p_id.to_s + "_", "")
                end
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
          new_finance_fee_category.destroy
          raise ActiveRecord::Rollback
        end

      end
    end
  end

end

