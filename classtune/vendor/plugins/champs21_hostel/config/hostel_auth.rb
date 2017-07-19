authorization do


  role :hostel_admin do
    has_permission_on [:hostels],
      :to=>[
      :index,
      :new,
      :create,
      :edit,
      :update,
      :show,
      :destroy,
      :update_employees,
      :hostel_dashboard,
      :student_hostel_details,
      :room_delete,
      :room_availability_details,
      :room_availability_details_csv,
      :room_list,
      :room_list_csv,
      :individual_room_details
    ]
    has_permission_on [:hostel_fee],
      :to=>[
      :hostel_fee_collection,
      :hostel_fee_collection_new,
      :hostel_fee_collection_edit,
      :update_hostel_fee_collection_date,
      :hostel_fee_collection_create,
      :hostel_fee_collection_view,
      :batchwise_collection_dates,
      :hostel_fee_pay,
      :update_fee_collection_dates,
      :hostel_fee_collection_details,
      :pay_fees,
      :hostel_fee_defaulters,
      :update_fee_collection_defaulters_dates,
      :hostel_fee_collection_defaulters_details,
      :pay_defaulters_fees,
      :index,
      :search_ajax,
      :student_hostel_fee,
      :fees_submission_student,
      :hostel_fee_collection_pay,
      :student_fee_receipt_pdf,
      :delete_fee_collection_date,
      :hostel_fees_report,
      :batch_hostel_fees_report,
      :hostel_fee_submission_student,
      :update_student_fine_ajax,
      :select_payment_mode,
      :student_profile_fee_details,
      :delete_hostel_fee_transaction
    ]
    has_permission_on [:room_details],
      :to=>[
      :index,
      :update_room_list,
      :new,
      :create,
      :destroy,
      :edit,
      :update,
      :show    ]
    has_permission_on [:room_allocate],
      :to=>[
      :index,
      :search_ajax,
      :assign_room,
      :room_details,
      :allocate,
      :vacate,
      :change_room,
      :change_room_details,
      :relocate  ]
  end
  
  

  role :admin do
    includes :hostel_admin
  end

  role :student do
    has_permission_on [:hostels],
      :to=>[
      :student_hostel_details ]
    
    has_permission_on [:hostel_fee],
      :to=>[
      :student_profile_fee_details,
      :student_fee_receipt_pdf
    ]
  end
  
  
  role :parent do
    has_permission_on [:hostel_fee],
      :to=>[
      :student_profile_fee_details,
      :student_fee_receipt_pdf
    ]
  end


end