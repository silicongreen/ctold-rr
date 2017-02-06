authorization do

  role :parent do
    has_permission_on [:classwork_answers],
      :to=>[      
      :download_attachment,            
      :show]
    has_permission_on [:classworks],
      :to=>[
      :classwork_student_list,
      :download_attachment,
      :index,
      :subject_classworks,
      :subject_classworks2,
      :subjects_students_list,
       ]
        has_permission_on :classworks, :to=>:show, :join_by=> :or do
        if_attribute :classwork_student_ids=> contains {user.guardian_entry.current_ward_id}
     end
     
  end
  
  role :student do
    has_permission_on [:classwork_answers],
      :to=>[
      :create,
      :done,
      :download_attachment,
      :edit,
      :new,
      :show,
      :update]
    has_permission_on [:classworks],
      :to=>[
      :classwork_student_list,
      :download_attachment,
      :index,
      :subject_classworks,
      :subject_classworks2,
      :subjects_students_list,
       ]
        has_permission_on :classworks, :to=>:show, :join_by=> :or do
        if_attribute :classwork_student_ids=> contains {user.student_record.id}
     end
     
  end

  role :employee do
    has_permission_on [:classworks],
      :to=>[
      :classwork_student_list,
      :subject_classworks3,
      :create,
      :destroy,
      :published_classwork,
      :download_attachment,
      :edit,
      :index,
      :new,
      :subject_classworks,
      :subjects_students_list,
      :showsubjects,
      :update]
    has_permission_on [:classwork_answers],
      :to=>[
      :download_attachment,
      :evaluate_classwork,
      :show]
      has_permission_on :classworks, :to=>:show, :join_by=> :or do
       if_attribute :employee_id => is {user.employee_record.id if user.employee and user.employee_record}
     end
   
  end
  
  role :admin do
    has_permission_on [:classworks],
      :to=>[
      :classwork_student_list,
      :subject_classworks3,
      :create,
      :destroy,
      :published_classwork,
      :download_attachment,
      :edit,
      :index,
      :new,
      :showsubjects,
      :subject_classworks,
      :subjects_students_list,
      :update]
    has_permission_on [:classwork_answers],
      :to=>[
      :download_attachment,
      :evaluate_classwork,
      :show]
      has_permission_on :classworks, :to=>:show, :join_by=> :or do
       
     end
   
  end
end