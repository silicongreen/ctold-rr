class PaymentNewConfiguration < ActiveRecord::Base
  validates_presence_of :config_key

  serialize :config_value

  class << self
    def config_value(key)
      PaymentNewConfiguration.find_by_config_key(key).try(:config_value)
    end
  end
  
end
