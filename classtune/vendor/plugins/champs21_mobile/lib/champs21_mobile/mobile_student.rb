module Champs21Mobile
  module MobileStudent

    def self.included(base)
      base.instance_eval do
        before_filter :is_mobile_user?
      end
    end

    def mobile_fee
      @student=Student.find(params[:id])
      @dates = FinanceFeeCollection.find_all_by_batch_id(@student.batch ,:joins=>'INNER JOIN finance_fees ON finance_fee_collections.id = finance_fees.fee_collection_id',:conditions=>"finance_fees.student_id = #{@student.id} and finance_fee_collections.is_deleted = 0")
      @page_title=t('fee_status')
      render :layout =>"mobile"
    end

    private

    def is_mobile_user?
      unless Champs21Plugin.can_access_plugin?("champs21_mobile")
        if Champs21Mobile::MobileStudent.instance_methods.include?(action_name)
          flash[:notice]=t('flash_msg4')
          redirect_to :controller => 'user', :action => 'dashboard'
        end
      end
    end

  end
end
