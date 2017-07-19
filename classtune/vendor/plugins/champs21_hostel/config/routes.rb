ActionController::Routing::Routes.draw do |map|
  
  #hostel
  map.resources :hostels,:collection=>{:hostel_dashboard=>:get,:room_availability_details=>[:get],:room_availability_details_csv=>[:get],:individual_room_details=>[:get]}
  map.resources :wardens
  map.resources :room_details

  map.namespace(:api) do |api|
    api.resources :hostels
  end
  
end