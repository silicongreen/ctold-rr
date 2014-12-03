authorization do

  # admin privileges
  role :admin do
    includes :task_management
  end

  role :student do
    includes :basic_task_privileges
  end

  role :employee do
    includes :basic_task_privileges
  end

  role :basic_task_privileges do
    has_permission_on [:tasks],
      :to => [
      :index,
      :show,
      :download_attachment,
      :list_created_tasks,
      :list_assigned_tasks,
      :assigned_to,
    ]
    has_permission_on [:task_comments],
      :to => [
      :create,
      :destroy,
      :download_attachment,
    ]
  end

  role :task_management do
    has_permission_on [:tasks],
      :to => [
      :index,
      :new,
      :create,
      :edit,
      :update,
      :destroy,
      :show,
      :download_attachment,
      :list_employees,
      :select_employee_department,
      :select_users,
      :select_student_course,
      :select_users,
      :to_employees,
      :to_students,
      :to_schools,
      :update_recipient_list,
      :toggle_status,
      :list_created_tasks,
      :list_assigned_tasks,
      :assigned_to,
    ]
    has_permission_on [:task_comments],
      :to => [
      :create,
      :destroy,
      :download_attachment,
    ]
  end

end
