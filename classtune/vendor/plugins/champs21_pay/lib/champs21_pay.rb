require 'dispatcher'
module Champs21Pay
  require 'authorize_net'
  GATEWAYS = ["ssl.commerce","trustbank"]
  PAYPAL_CONFIG_KEYS = ["paypal_id"]
  AUTHORIZENET_CONFIG_KEYS = ["authorize_net_merchant_id","authorize_net_transaction_password"]
  SSL_COMMERCE_CONFIG_KEYS = ["store_id","store_password"]
  TRUST_BANK_CONFIG_KEYS = ["merchant_id"]

  def self.attach_overrides
    Dispatcher.to_prepare :champs21_pay do
      ::ActionView::Base.instance_eval { include PaymentSettingsHelper }
      ::StudentController.instance_eval { include OnlinePayment::StudentPay }
      ::StudentController.instance_eval { helper :authorize_net }
      ::FinanceController.instance_eval { include OnlinePayment::StudentPayReceipt }
      ::FinanceTransaction.instance_eval { has_one :payment }
      ::Student.instance_eval { has_many :payments, :as => :payee }
      ::Guardian.instance_eval { has_many :payments, :as => :payee }
    end

  end
  
  def self.dependency_check(record,type)

  end
end