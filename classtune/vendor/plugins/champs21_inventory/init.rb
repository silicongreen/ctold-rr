# Include hook code here

require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_inventory")


Champs21Plugin.register = {
  :name=>"champs21_inventory",
  :description=>"Champs21 Inventory Module",
  :auth_file=>"config/inventory_auth.rb",
  :more_menu=> {:title=>"inventory_text",:controller=>"inventories",:action=>"index",:target_id=>"more-parent"},
  :sub_menus=>[ {:title=>"store_category",:controller=>"store_categories",:action=>"index",:target_id=>"champs21_inventory"},
    {:title=>"store_type",:controller=>"store_types",:action=>"index",:target_id=>"champs21_inventory"},
    {:title=>"store",:controller=>"stores",:action=>"index",:target_id=>"champs21_inventory"},
    {:title=>"store_item",:controller=>"store_items",:action=>"index",:target_id=>"champs21_inventory"},
    {:title=>"supplier type",:controller=>"supplier_types",:action=>"index",:target_id=>"champs21_inventory"},
    {:title=>"supplier",:controller=>"suppliers",:action=>"index",:target_id=>"champs21_inventory"},
    {:title=>"indent",:controller=>"indents",:action=>"index",:target_id=>"champs21_inventory"},
    {:title=>"purchase order",:controller=>"purchase_orders",:action=>"index",:target_id=>"champs21_inventory"},
    {:title=>"grn",:controller=>"grns",:action=>"index",:target_id=>"champs21_inventory"}],
  :multischool_models=>%w{GrnItem Grn IndentItem Indent PurchaseItem PurchaseOrder StoreCategory StoreItem Store StoreType Supplier SupplierType},
  :finance=>{:category_name=>"Inventory",:is_income =>0,  :destination=>{:controller=>"grns" , :action => "report"}}
}


Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

if RAILS_ENV == 'development'
  ActiveSupport::Dependencies.load_once_paths.reject!{|x| x =~ /^#{Regexp.escape(File.dirname(__FILE__))}/}
end



