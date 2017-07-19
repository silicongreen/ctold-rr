class Store < ActiveRecord::Base
  belongs_to :store_category
  belongs_to :store_type
  has_many :store_items
  has_many :indents

  validates_presence_of:name, :code, :store_type_id , :store_category_id

  named_scope :active,{ :conditions => { :is_deleted => false }}


  def validate
    if Store.active.reject{|s| s.id == id}.find_by_code(code).present? and is_deleted == false
      errors.add("code","is already taken")
    end
  end

  def can_be_deleted?
    (store_items.active.present? or indents.active.present?) ? false : true
  end

  def full_name
    "#{name}-#{code}"
  end

end
