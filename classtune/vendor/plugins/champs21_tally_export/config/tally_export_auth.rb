authorization do

  role :admin do
    has_permission_on [:tally_exports],
      :to => [
      :index,
      :settings,
      :general_settings,
      :companies,
      :set_tally_company_company_name,
      :delete_company,
      :voucher_types,
      :set_tally_voucher_type_voucher_name,
      :delete_voucher,
      :accounts,
      :set_tally_account_account_name,
      :delete_account,
      :ledgers,
      :view_ledgers,
      :create_ledger,
      :edit_ledger,
      :delete_ledger,
      :manual_sync,
      :bulk_export,
      :schedule,
      :downloads,
      :download,
      :export_journal,
      :export_receipt,
      :update_fees_collection_dates,
      :load_fees_submission_batch,
      :load_student_details,
      :download_journal,
      :download_receipt,
      :get_batches,
      :get_batches_receipt,
      :export_batches,
      :get_user_by_batches,
      :failed_syncs
    ]
  end

  role :finance_control do
    has_permission_on [:tally_exports],
      :to => [
      :index,
      :settings,
      :general_settings,
      :companies,
      :set_tally_company_company_name,
      :delete_company,
      :voucher_types,
      :set_tally_voucher_type_voucher_name,
      :delete_voucher,
      :accounts,
      :set_tally_account_account_name,
      :delete_account,
      :ledgers,
      :view_ledgers,
      :create_ledger,
      :edit_ledger,
      :delete_ledger,
      :manual_sync,
      :bulk_export,
      :schedule,
      :downloads,
      :download,
      :export_journal,
      :export_receipt,
      :update_fees_collection_dates,
      :load_fees_submission_batch,
      :load_student_details,
      :download_journal,
      :download_receipt,
      :get_batches,
      :export_batches,
      :get_user_by_batches,
      :failed_syncs
    ]
  end

  role :employee do

  end

end