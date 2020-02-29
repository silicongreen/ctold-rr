authorization do

  role :payment do
    has_permission_on [:online_payments],:to =>[
      :index
    ]
    has_permission_on [:payment_settings],:to=>[
      :settings,
      :show_gateway_fields,
      :return_to_champs21_pages,
      :index,
      :order_verifications,
      :verify_payment,
      :transactions,
      :transaction_list,
      :order_verifications_partials,
      :search_transaction,
      :search_transaction_bkash
    ]
  end

  role :masteradmin do
    includes  :payment
  end

  role :admin do
    includes  :payment
  end

  role :student do
    has_permission_on [:finance], :to => [
      :student_fee_receipt_pdf,
      :student_fee_receipt_pdf_multiple
    ]
  end

  role :parent do
    has_permission_on [:finance], :to => [
      :student_fee_receipt_pdf,
      :student_fee_receipt_pdf_multiple
    ]
  end

end