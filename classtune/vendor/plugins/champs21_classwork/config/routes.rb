ActionController::Routing::Routes.draw do |map|
  
 #classwork
  map.resources :classworks,:collection=>{:get_classwork_filter=>[:get],:subject_classworks=>[:get]},:has_many => :classwork_answers
 # map.resources :attendance_reports
end