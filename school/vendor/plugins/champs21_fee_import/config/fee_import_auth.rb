authorization do

  role :admin do
    has_permission_on [:fee_imports],
      :to => [
        :import_fees,
        :select_student,
        :list_students_by_batch,
        :list_fees_for_student
      ]
  end

  role :finance_control do
    has_permission_on [:fee_imports],
      :to => [
        :import_fees,
        :select_student,
        :list_students_by_batch,
        :list_fees_for_student
      ]
  end

  role :student do

  end


  role :employee do

  end

  role :admission do
    has_permission_on [:fee_imports],
      :to => [
        :import_fees
      ]
  end

end