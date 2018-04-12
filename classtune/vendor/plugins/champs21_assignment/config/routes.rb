ActionController::Routing::Routes.draw do |map|
  
 #assignment
  map.resources :assignments,:collection=>{:get_homework_filter=>[:get],:use_homework=>[:get],:publisher=>[:get],:show_publisher=>[:get],:approved_homework=>[:get],:create_use_homwork=>[:post],:subject_assignments_approved=>[:get], :get_homework_filter_publisher=>[:get],:subject_assignments_publisher=>[:get]},:has_many => :assignment_answers
  
 # map.resources :attendance_reports
end