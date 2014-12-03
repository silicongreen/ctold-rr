authorization do

  role :student do
    has_permission_on [:assignment_answers],
      :to=>[
      :create,
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
      :subjects_students_list,
       ]
        has_permission_on :assignments, :to=>:show, :join_by=> :or do
        if_attribute :assignment_student_ids=> contains {user.student_record.id}
     end
     
  end

  role :employee do
    has_permission_on [:assignments],
      :to=>[
      :assignment_student_list,
      :create,
      :destroy,
      :download_attachment,
      :edit,
      :index,
      :new,
      :subject_assignments,
      :subjects_students_list,
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

end