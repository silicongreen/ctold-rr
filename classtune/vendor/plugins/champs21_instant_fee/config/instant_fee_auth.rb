authorization do

  role :admin do
    has_permission_on [:instant_fees],
      :to=>[
      :index,
      :manage_fees,
      :new_category,
      :create_category,
      :edit_category,
      :update_category,
      :delete_category,
      :new_particular,
      :create_particular,
      :edit_particular,
      :update_particular,
      :delete_particular,
      :new_category_particular,
      :create_category_particular,
      :list_particulars,
      :new_instant_fees,
      :tsearch_logic,
      :category_type,
      :handle_category,
      :handle_category_for_guest,
      :create_instant_fee,
      :report,
      :report_detail,
      :instant_fee_created_detail,
      :print_reciept,
      :select_payment_mode,
      :delete_transaction_for_instant_fee,
      :show_instant_fee_transactions,
      :list_instant_fee_transactions,
      :instant_fee_transaction_filter_by_date
    ]
  end

  role :finance_control do
    has_permission_on [:instant_fees],
      :to=>[
      :index,
      :manage_fees,
      :new_category,
      :create_category,
      :edit_category,
      :update_category,
      :delete_category,
      :new_particular,
      :create_particular,
      :edit_particular,
      :update_particular,
      :delete_particular,
      :new_category_particular,
      :create_category_particular,
      :list_particulars,
      :new_instant_fees,
      :tsearch_logic,
      :category_type,
      :handle_category,
      :handle_category_for_guest,
      :create_instant_fee,
      :report,
      :report_detail,
      :instant_fee_created_detail,
      :print_reciept,
      :select_payment_mode,
      :select_payment_mode,
      :delete_transaction_for_instant_fee,
      :show_instant_fee_transactions,
      :list_instant_fee_transactions,
      :instant_fee_transaction_filter_by_date
    ]
  end

end