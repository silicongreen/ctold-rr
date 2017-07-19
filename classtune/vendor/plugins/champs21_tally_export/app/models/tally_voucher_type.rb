class TallyVoucherType < ActiveRecord::Base
  has_many :tally_ledgers

  validates_presence_of :voucher_name
end
