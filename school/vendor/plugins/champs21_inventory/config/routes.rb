ActionController::Routing::Routes.draw do |map|
  map.resources :store_categories
  map.resources :store_types
  map.resources :stores
  map.resources :store_items,:collection => {:search_ajax => [:get,:post],:index => [:get,:post]}
  map.resources :supplier_types
  map.resources :suppliers
  map.resources :inventories, :collection => {:search => [:get],:search_ajax => [:get,:post],:reports=>[:get],:select_sort_order=>[:get],:indent_report_csv=>[:get],:purchase_order_csv=>[:get],:grn_report_csv=>[:get]}
  map.resources :indents,:member => {:indent_pdf => [:get],:acceptance => [:get,:post]}
  map.resources :purchase_orders, :member => { :acceptance => [:get,:post],:po_pdf => [:get],:raised_grns => [:get] }
  map.resources :grns,:member => {:grn_pdf => [:get]},:collection => {:report => [:get]}
end
