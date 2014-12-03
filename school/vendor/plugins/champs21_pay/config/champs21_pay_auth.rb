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
      :transactions
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
      :student_fee_receipt_pdf
    ]
  end

  role :parent do
    has_permission_on [:finance], :to => [
      :student_fee_receipt_pdf
    ]
  end

end