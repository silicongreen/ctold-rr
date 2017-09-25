ActionController::Routing::Routes.draw do |map|
  
 #assignment
  map.resources :assignments,:collection=>{:get_homework_filter=>[:get]},:has_many => :assignment_answers
  
 # map.resources :attendance_reports
end