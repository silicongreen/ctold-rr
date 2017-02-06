ActionController::Routing::Routes.draw do |map|
  
 #classwork
  map.resources :classworks,:has_many => :classwork_answers
 # map.resources :attendance_reports
end