require 'dispatcher'
module Champs21Pay
  require 'authorize_net'
  GATEWAYS = ["sslcommerce","trustbank","bkash","citybank"]
  PAYPAL_CONFIG_KEYS = ["paypal_id"]
  AUTHORIZENET_CONFIG_KEYS = ["authorize_net_merchant_id","authorize_net_transaction_password"]
  SSLCOMMERCE_CONFIG_KEYS = ["is_test_sslcommerce", "store_id","store_password"]
  TRUSTBANK_CONFIG_KEYS = ["is_test_trustbank", "merchant_id","keycode_verification"]
  BKASH_CONFIG_KEYS = ["is_test_bkash", "app_key","app_secret","username","password"]
  CITYBANK_CONFIG_KEYS = ["is_test_citybank","merchant_id", "api_user_name","api_password"]

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