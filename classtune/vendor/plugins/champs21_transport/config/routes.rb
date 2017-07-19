ActionController::Routing::Routes.draw do |map|
  
  # transport
  map.resources :vehicles
  map.resources :routes

  map.namespace(:api) do |api|
    api.resources :vehicles
  end

end