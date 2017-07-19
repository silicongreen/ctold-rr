require 'dispatcher'
# Champs21InstantFee
module Champs21InstantFee
  def self.attach_overrides
    Dispatcher.to_prepare :champs21_instant_fee do
      ::Student.instance_eval { has_many :instant_fees, :as => 'payee' }
      ::Employee.instance_eval { has_many :instant_fees, :as => 'payee' }
    end
  end
end


#