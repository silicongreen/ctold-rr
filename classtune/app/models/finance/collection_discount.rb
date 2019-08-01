class CollectionDiscount < ActiveRecord::Base
  belongs_to :fee_discount
  belongs_to :finance_fee_collection
  
  after_destroy :delete_exclude_discount
  
  def delete_exclude_discount
    fee_discount = FeeDiscount.find(:first, :conditions => "id = #{fee_discount_id}")
    
    unless fee_discount.blank?
      if fee_discount.is_onetime
        student_exclude_discounts = StudentExcludeDiscount.find(:all, :conditions => "fee_discount_id = #{fee_discount_id}")
        
        unless student_exclude_discounts.nil?
          student_exclude_discounts.each do |student_exclude_discount|
            student_exclude_discount.destroy
          end
        end
      end
    end
  end
  
end
