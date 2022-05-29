ActionController::Routing::Routes.draw do |map|
  
 #classwork
  map.resources :classworks,:collection=>{:get_classwork_filter=>[:get],:subject_classworks=>[:get],:download_pdf=>[:get],:download_pdf_publush_date=>[:get],:download_excell=>[:get]},:has_many => :classwork_answers
 # map.resources :attendance_reports
end