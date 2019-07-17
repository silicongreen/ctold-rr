class Scholarship < ActiveRecord::Base
  belongs_to :finance_fee_particular_category, :foreign_key => 'discount_particular_id'
end
