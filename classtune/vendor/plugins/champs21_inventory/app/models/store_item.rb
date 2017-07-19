
class StoreItem < ActiveRecord::Base
  belongs_to :store
  has_many :indent_items
  has_many :purchase_items
  has_many :grn_items
  
  validates_presence_of :item_name,:quantity,:unit_price,:batch_number,:store_id
  validates_numericality_of  :quantity, :greater_than_or_equal_to => 0
  validates_numericality_of  :unit_price,:greater_than => 0
  validates_numericality_of :tax, :greater_than_or_equal_to => 0, :less_than => 100

  named_scope :active,{ :conditions => { :is_deleted => false }}

  before_save :verify_precision

  def verify_precision
#    self.unit_price = Champs21Precision.set_and_modify_precision self.unit_price
#    self.tax = Champs21Precision.set_and_modify_precision self.tax
  end

  def validate
    if StoreItem.active.reject{|si| si.id == id}.find_by_item_name(item_name).present? and is_deleted == false
      errors.add("item_name","is already taken")
    end

    if StoreItem.active.reject{|si| si.id == id}.find_by_batch_number(batch_number).present? and is_deleted == false
      errors.add("batch_number","is already taken")
    end
  end
  
  def can_be_deleted?
    (indent_items.active.present? or purchase_items.active.present? or grn_items.active.present?) ? false : true
  end

  def can_edit_store(user_in_question)
    user_in_question.privileges.map(&:name).include?('Inventory') or user_in_question.admin?
  end
end
