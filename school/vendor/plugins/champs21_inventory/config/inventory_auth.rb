authorization do


  #Inventory module
  role :inventory do
    has_permission_on [:inventories],  :to => [:index,:search, :search_ajax,:reports,:select_sort_order,:indent_report_csv,:purchase_order_csv,:grn_report_csv]
    has_permission_on [:store_categories],  :to => [:index, :edit, :destroy, :new, :create, :update]
    has_permission_on [:store_types],  :to => [:index, :edit, :destroy, :new, :create, :update]
    has_permission_on [:stores],  :to => [:index, :edit, :destroy,:new, :create, :update]
    has_permission_on [:store_items],  :to => [:index, :edit, :destroy, :new, :create, :update, :search_ajax]
    has_permission_on [:supplier_types],  :to => [:index, :edit, :destroy, :show, :new, :create, :update]
    has_permission_on [:suppliers],  :to => [:index, :edit, :destroy, :show, :new, :create, :update]
    has_permission_on [:indents],  :to => [:index, :edit, :destroy, :show, :new, :create, :update, :acceptance,  :update_item, :indent_pdf,:update_storeitem]
    has_permission_on [:purchase_orders],  :to => [:index, :edit, :destroy, :show, :new, :create, :update, :po_pdf,:update_supplier, :acceptance,:update_store,:update_item,:raised_grns]
    has_permission_on [:grns],  :to => [:index, :destroy, :show, :new, :create,:grn_pdf,:update_po, :report, :report_detail]
  end

  role :admin do
    includes :inventory
  end


  role :inventory_manager do
    has_permission_on [:indents],  :to => [:index, :edit, :destroy, :show, :new, :create, :update, :acceptance,:set_manager, :update_item, :indent_pdf,:update_storeitem ]
    has_permission_on [:purchase_orders],  :to => [:index, :edit, :destroy, :show, :new, :create, :update,:po_pdf,:update_supplier, :acceptance,:update_store,:update_item,:raised_grns]
    has_permission_on [:store_items],  :to => [:index, :edit, :destroy, :show, :new, :create, :update, :search_ajax]
    has_permission_on [:inventories],  :to => [:index ,:search, :search_ajax,]
  end


  role :inventory_basics do
    has_permission_on [:indents],  :to => [:index, :edit, :destroy, :show, :new, :create, :update, :set_manager, :update_item, :indent_pdf,:update_storeitem,:acceptance]
    has_permission_on [:store_items],  :to => [:index,  :search_ajax]
    has_permission_on [:inventories],  :to => [:index]
  end
end

