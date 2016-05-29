authorization do

  role :dashboards do
    has_permission_on [:dashboards],
      :to=>[
      :index,
      :update_palette,
      :toggle_minimize,
      :remove_palette,
      :refresh_palette,
      :show_palette_list,
      :modify_user_palettes,
      :sort_palettes,
      :view_more,
      :quize_data,
      :notice_data,
      :notice_main,
      :homework_data,
      :class_routine_data_student,
      :exam_routine_data_student,
      :exam_result_data_student,
      :routine_data,
      :employee_homework_data,
      :employee_task_data,
      :employee_exam_routine_data,
      :employee_quiz_data,
      :quiz_result_data_student,
      :getglobalsearch
      ]
  end


  role :admin do
    includes :dashboards
  end

  role :employee do
    includes :dashboards
  end

  role :student do
    includes :dashboards
  end

  role :parent do
    includes :dashboards
  end



end