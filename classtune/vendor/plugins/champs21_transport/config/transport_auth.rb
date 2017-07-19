authorization do
  #transport
  role :transport_admin do
    has_permission_on [:transport],
      :to=>[
      :index,
      :dash_board,
      :search_ajax,
      :transport_details,
      :ajax_transport_details,
      :add_transport,
      :update_vehicle,
      :load_fare,
      :seat_description,
      :delete_transport,
      :edit_transport,
      :student_transport_details,
      :employee_transport_details,
      :pdf_report,
      :vehicle_report,
      :vehicle_report_csv,
      :single_vehicle_details,
      :add_transport_all,
      :single_vehicle_details_csv
    ]
    has_permission_on [:transport_fee],
      :to=>[
      :index,
      :transport_fee_collections,
      :transport_fee_collection_new,
      :transport_fee_collection_create,
      :transport_fee_collection_view,
      :transport_fee_collection_details,
      :transport_fee_collection_edit,
      :transport_fee_collection_date_edit,
      :transport_fee_collection_date_update,
      :transport_fee_collection_update,
      :transport_fee_collection_delete,
      :delete_fee_collection_date,
      :transport_fee_pay,
      :transport_fee_defaulters_view,
      :transport_fee_defaulters_details,
      :transport_defaulters_fee_pay,
      :tsearch_logic,
      :fees_student_dates,
      :fees_employee_dates,
      :update_fee_collection_dates,
      :fees_submission_student,
      :fees_submission_employee,
      :transport_fee_collection_pay,
      :transport_fee_collection_details,
      :employee_transport_fee_collection,
      :employee_transport_fee_collection_details,
      :defaulters_update_fee_collection_dates,
      :defaulters_update_fee_collection_details,
      :defaulters_transport_fee_collection_details,
      :employee_defaulters_transport_fee_collection,
      :employee_defaulters_transport_fee_collection_details,
      :transport_fee_search,
      :student_fee_receipt_pdf,
      :update_fine_ajax,
      :update_employee_fine_ajax,
      :update_student_fine_ajax,
      :update_employee_fine_ajax2,
      :update_defaulters_fine_ajax,
      :update_employee_defaulters_fine_ajax,
      :update_user_ajax,
      :update_batch_list_ajax,
      :fees_submission_defaulter_student,
      :transport_fee_receipt_pdf,
      :transport_fees_report,
      :batch_transport_fees_report,
      :employee_transport_fees_report,
      :select_payment_mode,
      :student_profile_fee_details,
      :delete_transport_transaction

    ]
    has_permission_on [:routes],
      :to=>[
      :index,
      :new,
      :create,
      :edit,
      :update,
      :destroy,
      :show,
      :route_schedule,
      :destroy_schedule,
      :edit_routes_schedule,
      :update_routes_schedule,
      :add_routes_schedules,
      :create_routes_schedule
    ]
    has_permission_on [:vehicles],
      :to=>[
      :index,
      :new,
      :create,
      :edit,
      :update,
      :destroy,
      :show
    ]
  end
  role :admin do
    includes :transport_admin
  end

  role :student do
    has_permission_on [:transport],
      :to=>[
      :student_transport_details
    ]
    has_permission_on [:transport_fee],
      :to=>[
      :student_profile_fee_details,
      :transport_fee_receipt_pdf
    ]
  end

  role :parent do
    has_permission_on [:transport],
      :to=>[
      :student_transport_details
    ]
    has_permission_on [:transport_fee],
      :to=>[
      :student_profile_fee_details,
      :transport_fee_receipt_pdf
    ]
  end

  role :employee do
    has_permission_on [:transport],
      :to=>[
      :employee_transport_details
    ]
  end



end