class AdminUser < ActiveRecord::Base

  attr_accessor :password, :new_password

  validates_presence_of :username,:full_name,:email
  validates_presence_of :password,:on=>:create
  validates_uniqueness_of :username
  validates_length_of     :username, :within => 1..20, :if => Proc.new{|au| au.username.present?}
  validates_format_of :username, :with => /^[A-Z0-9._-]*$/i, :message => "must be alphanumeric"
  validates_format_of     :email, :with => /^[A-Z0-9._%-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i,
    :message => "invalid email", :if => Proc.new{|au| au.email.present?}

  has_one :school_group_user
  has_one :school_group, :through=>:school_group_user
  has_many :schools, :foreign_key=>:creator_id
  has_many :users, :as=>:higher_user, :foreign_key=>:higher_user_id
  belongs_to :higher_user, :class_name=>"AdminUser"
  
  named_scope :active, :conditions => { :is_deleted => false }
  named_scope :inactive, :conditions => { :is_deleted => true }
  named_scope :for_group, lambda {|group| (["MasterAdmin"].include? user.class.to_s) ? {} : {:conditions=>{:admin_user_id=>user.id}}}

  def before_save
    self.password_salt = random_string(8) if self.password_salt == nil
    self.crypted_password = Digest::SHA1.hexdigest(self.password_salt + self.password) unless self.password.blank? 
  end

  def self.authenticate?(username, password)
    u = case MultiSchool.current_school_group.class.to_s
    when "MultiSchoolGroup"
      AdminUser.find_by_username(username, :conditions=>{:school_group_users=>{:school_group_id=>MultiSchool.current_school_group.id}},:joins=>:school_group_user)
    else
      AdminUser.find_by_username(username,:conditions=>"type NOT IN ('MultiSchoolAdmin')")
    end
    if u
      u if u.crypted_password == Digest::SHA1.hexdigest(u.password_salt + password)
    else
      false
    end
  end

  def change_password(param_values)
    if AdminUser.authenticate?(self.username, param_values[:password])
      if param_values[:new_password].present?
        self.update_attributes(:password=>param_values[:new_password])
        return true
      else
        self.password = param_values[:password]
        self.errors.add(:new_password,"can't be blank")
        return false
      end
    else
      self.password = param_values[:password]
      self.new_password = param_values[:new_password]
      self.errors.add(:password,"is incorrect")
      return false
    end
  end

  def allowed_plugins
    unless self.school_group.nil?
      self.school_group.allowed_plugins
    else
      self.class.all_plugins
    end
  end

  def self.all_plugins
    @@all_plugins ||= Champs21Plugin::AVAILABLE_MODULES.collect{|plugin| plugin[:name]}
  end

  def random_string(len)
    randstr = ""
    chars = ("0".."9").to_a + ("a".."z").to_a + ("A".."Z").to_a
    len.times { randstr << chars[rand(chars.size - 1)] }
    randstr
  end

  def self.inherited(child)
    child.instance_eval do
      def model_name
        AdminUser.model_name
      end
    end
    super
  end

  def role_symbols
    [self.class.to_s.underscore.to_sym]
  end
end
