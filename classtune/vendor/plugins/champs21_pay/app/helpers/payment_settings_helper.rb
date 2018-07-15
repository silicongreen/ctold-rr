module PaymentSettingsHelper
  def config_value(key)
    PaymentConfiguration.find_by_config_key(key).try(:config_value)
  end

  def paypal_pay_button(certificate,merchant_id,item_name,amount,return_url,paid_fees = Array.new,button_style = String.new)
    @certificate = certificate
    @merchant_id = merchant_id
    @item_name = item_name
    @amount = amount
    @return_url = return_url
    @paid_fees = paid_fees
    @button_style = button_style

    render :partial => "gateway_payments/paypal/paypal_form"
  end
  
  def ssl_commerce_pay_button(store_id,store_password,amount,item_name,return_url,cancel_url,fail_url,trans_id_ssl_commerce,paid_fees = Array.new,button_style = String.new)
    @store_id = store_id
    @store_password = store_password
    @amount = amount
    @item_name = item_name
    @return_url = return_url
    @cancel_url = cancel_url
    @fail_url = fail_url
    @button_style = button_style
    @paid_fees = paid_fees
    @trans_id_ssl_commerce = trans_id_ssl_commerce
    render :partial => "gateway_payments/ssl_commerce/ssl_commerce_form"
  end

  def authorize_net_pay_button(merchant_id,certificate,amount,item_name,return_url,paid_fees = Array.new,button_style = String.new)
    @merchant_id = merchant_id
    @certificate = certificate
    @amount = amount
    @item_name = item_name
    @return_url = return_url
    @button_style = button_style
    @paid_fees = paid_fees
    @sim_transaction = AuthorizeNet::SIM::Transaction.new(@merchant_id,@certificate, @amount,{:hosted_payment_form => true})
    @sim_transaction.instance_variable_set("@custom_fields",{:x_description => @item_name})
    @sim_transaction.set_hosted_payment_receipt(AuthorizeNet::SIM::HostedReceiptPage.new(:link_method => AuthorizeNet::SIM::HostedReceiptPage::LinkMethod::GET, :link_text => "Back to #{current_school_name}", :link_url => URI.parse(@return_url)))

    render :partial => "gateway_payments/authorize_net/authorize_net_form"
  end

  def get_payment_url
    payment_urls = Hash.new
    if File.exists?("#{Rails.root}/vendor/plugins/champs21_pay/config/online_payment_url.yml")
      payment_urls = YAML.load_file(File.join(Rails.root,"vendor/plugins/champs21_pay/config/","online_payment_url.yml"))
    end
    active_gateway = PaymentConfiguration.config_value("champs21_gateway")
    if active_gateway == "Paypal"
      payment_url = payment_urls["paypal_url"]
      payment_url ||= "https://www.sandbox.paypal.com/cgi-bin/webscr"
    elsif active_gateway == "Authorize.net"
      payment_url = eval(payment_urls["authorize_net_url"].to_s)
      payment_url ||= eval("AuthorizeNet::SIM::Transaction::Gateway::TEST")
    elsif active_gateway == "ssl.commerce"
    
        payment_url = payment_urls["ssl_commerce_url"]
        payment_url ||= "https://securepay.sslcommerz.com/gwprocess/testbox/v3/process.php"
    
    end
    payment_url
  end
end
