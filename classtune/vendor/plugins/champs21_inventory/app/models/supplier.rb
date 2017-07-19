class Supplier < ActiveRecord::Base
  belongs_to :supplier_type
  has_many :purchase_orders
  

  validates_presence_of :supplier_type_id, :name
  validates_format_of     :tin_no, :with => /^[A-Z0-9]*$/i
  validates_numericality_of :contact_no
  
  named_scope :active,{ :conditions => { :is_deleted => false }}

  def validate
    if Supplier.active.find_by_supplier_type_id_and_contact_no(supplier_type_id,contact_no).to_a.reject{|st| st.id == id}.present? and is_deleted == false
      errors.add("contact_no","is already taken")
    end
  end

  def can_be_deleted?
    purchase_orders.active.present? ? false : true
  end
end
