require 'i18n'
class DelayedFeeCollectionNotificationJob
  attr_accessor :user,:collection,:fee_collection
  def initialize(user,fee_collection_id, batch_id)
    @user = user
    @fee_collection_id = fee_collection_id
    @batch_id = batch_id
  end
  include I18n
  def t(obj)
    I18n.t(obj)
  end
  def perform

    unless @fee_collection_id.nil?
      @finance_fee_collection = FinanceFeeCollection.find(@fee_collection_id)
      subject = "#{t('fees_submission_date')}"

      b=@batch_id.to_i
      fee_category_name = @finance_fee_collection[:fee_category_id]
      @students = Student.find_all_by_batch_id(b)
      @fee_category= FinanceFeeCategory.find_by_id(@finance_fee_collection[:fee_category_id])

      unless @fee_category.fee_particulars.all(:conditions=>"is_deleted=false and batch_id=#{b}").collect(&:receiver_type).include?"Batch"
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
          guardians = s.student_guardian
          unless guardians.blank?
            guardians.each do |guardian|
              unless guardian.user_id.nil?
                recipient_ids << guardian.user_id
              end
            end
          end
#          recipient_ids << s.user.id if s.user
#          recipient_ids << s.immediate_contact.user_id if s.immediate_contact.present?
        end
      end
      recipient_ids = recipient_ids.compact
      
      Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => @user.id,
          :recipient_ids => recipient_ids,
          :subject=>subject,
          :body=>body ))

    end
  end

end

