authorization do

  role :view_results  do
    #online exam
    has_permission_on [:online_exam],
      :to => [
      :index,
      :view_result,
      :update_exam_list,
      :exam_result,
      :get_classes,
      :get_section_data,
      :exam_result_pdf
    ]
  end

  role :examination_control do
    #online exam
    has_permission_on [:online_exam],
      :to => [
      :index,
      :new_online_exam,
      :new_question,
      :create_question,
      :view_online_exam,
      :show_active_exam,
      :edit_exam_group,
      :update_exam_group,
      :exam_details,
      :edit_question,
      :delete_question,
      :edit_exam_option,
      :add_extra_question,
      :publish_exam,
      :view_result,
      :update_exam_list,
      :exam_result,
      :exam_result_pdf,
      :reset_exam,
      :update_student_exam,
      :update_student_list,
      :update_reset_exam,
      :get_classes,
      :get_section_data,
      :delete_exam_group
    ]
  end

  role :admin do
    #online exam
    has_permission_on [:online_exam],
      :to => [
      :index,
      :new_online_exam,
      :new_question,
      :create_question,
      :view_online_exam,
      :show_active_exam,
      :edit_exam_group,
      :update_exam_group,
      :exam_details,
      :edit_question,
      :delete_question,
      :edit_exam_option,
      :add_extra_question,
      :publish_exam,
      :view_result,
      :update_exam_list,
      :exam_result,
      :exam_result_pdf,
      :reset_exam,
      :update_student_exam,
      :update_student_list,
      :update_reset_exam,
      :get_classes,
      :get_section_data,
      :delete_exam_group
    ]
  end

  role :student do
    has_permission_on [:online_student_exam],
      :to => [
      :index,
      :start_exam,
      :save_exam,
      :save_scores,
      :started_exam,
      :exam_list
    ]
  end
  
  role :parent do
    has_permission_on [:online_student_exam],
      :to => [
      :index,
      :start_exam,
      :save_exam,
      :save_scores,
      :started_exam,
      :exam_list
    ]
  end

end