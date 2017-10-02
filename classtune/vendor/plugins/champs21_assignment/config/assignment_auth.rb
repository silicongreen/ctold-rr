authorization do

  role :parent do
    has_permission_on [:assignment_answers],
      :to=>[      
      :download_attachment,            
      :show]
    has_permission_on [:assignments],
      :to=>[
      :assignment_student_list,
      :download_attachment,
      :index,
      :subject_assignments,
      :subject_assignments2,
      :subjects_students_list,
       ]
        has_permission_on :assignments, :to=>:show, :join_by=> :or do
        if_attribute :assignment_student_ids=> contains {user.guardian_entry.current_ward_id}
     end
     
  end
  
  role :student do
    has_permission_on [:assignment_answers],
      :to=>[
      :create,
      :done,
      :download_attachment,
      :edit,
      :new,
      :show,
      :update]
    has_permission_on [:assignments],
      :to=>[
      :assignment_student_list,
      :download_attachment,
      :index,
      :subject_assignments,
      :subject_assignments2,
      :subjects_students_list,
       ]
        has_permission_on :assignments, :to=>:show, :join_by=> :or do
        if_attribute :assignment_student_ids=> contains {user.student_record.id}
     end
     
  end

  role :employee do
    has_permission_on [:assignments],
      :to=>[
      :defaulter_registration,
      :defaulter_students,
      :get_homework_filter_publisher,
      :assignment_student_list,
      :subject_assignments3,
      :create,
      :destroy,
      :published_homework,
      :publisher_homework,
      :download_attachment,
      :edit,
      :index,
      :publisher,
      :show_publisher,
      :new,
      :subject_assignments,
      :subject_assignments_publisher,
      :subjects_students_list,
      :showsubjects,
      :showsubjects_publisher,
      :update]
    has_permission_on [:assignment_answers],
      :to=>[
      :download_attachment,
      :evaluate_assignment,
      :show]
      has_permission_on :assignments, :to=>:show, :join_by=> :or do
       if_attribute :employee_id => is {user.employee_record.id if user.employee and user.employee_record}
     end
   
  end
  
  role :admin do
    has_permission_on [:assignments],
      :to=>[
      :defaulter_registration,
      :defaulter_students,
      :get_homework_filter,
      :get_homework_filter_publisher,
      :assignment_student_list,
      :subject_assignments3,
      :create,
      :destroy,
      :published_homework,
      :publisher_homework,
      :download_attachment,
      :edit,
      :index,
      :publisher,
      :show_publisher,
      :new,
      :showsubjects,
      :showsubjects_publisher,
      :subject_assignments,
      :subject_assignments_publisher,
      :subjects_students_list,
      :update]
    has_permission_on [:assignment_answers],
      :to=>[
      :download_attachment,
      :evaluate_assignment,
      :show]
      has_permission_on :assignments, :to=>:show, :join_by=> :or do
       
     end
   
  end
end