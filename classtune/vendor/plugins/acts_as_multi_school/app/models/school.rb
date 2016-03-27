class School < ActiveRecord::Base

  validates_presence_of :name,:code
  #  validates_uniqueness_of :code, :scope=>:is_deleted, :message=>'is reserved'
  validates_length_of :code,:maximum=>20

  belongs_to :school_group
  belongs_to :creator, :class_name=>"AdminUser"
  has_many :school_domains, :as=>:linkable, :dependent => :destroy
  has_one :sms_credential, :as=>:owner
  has_one :smtp_setting, :as=>:owner
  has_one :whitelabel_setting, :as=>:owner
  accepts_nested_attributes_for :school_domains
  has_one :available_plugin, :as=>:associated
  accepts_nested_attributes_for :available_plugin
  
  attr_accessor :emp_dept_file, :class_file, :emp_cat_file, :emp_grade_file, :exam_grade_file, :emp_dept_file_name, :class_file_name, :emp_cat_file_name, :emp_grade_file_name, :exam_grade_file_name, :menu_datas, :package, :import, :import_class_seed, :import_cate_seeds, :import_dept_seed, :import_emp_grade, :import_exam_grade, :assign_free_school
  
  after_create :create_champs21_school
  before_destroy :remove_sms_settings

  named_scope :active,{:conditions => { :is_deleted => false}}
  named_scope :is_test_school,{:conditions => { :is_test_school => true}}
  named_scope :is_running_school,{:conditions => { :is_test_school => false}}

  def validate
    if school_group.type == "MultiSchoolGroup" && school_group.parent_group.nil?
      limit = max_school_count_setting
      if school_group.schools.active.count >= limit
        self.errors.add_to_base("Maximum number of School Licenses exceeded.")
      end
    end if new_record?
  end

  def maker_id
    creator_id
  end
 
  def check_allowed_school_limit
    limit = max_school_count_setting
    if School.count(:conditions=>{:is_deleted=>false})>=limit
      errors.add_to_base("You are not allowed to create more than #{limit} schools")
      false
    end
  end

  def create_champs21_school
    MultiSchool.current_school = self
    pack_id = 0
    unless self.package.nil? or self.package.blank?
      self.package.each do |pid|
        @school_package = SchoolPackage.new()
        @school_package.school_id = MultiSchool.current_school.id
        @school_package.package_id = pid
        @school_package.save
        pack_id = pid
      end
    end

    unless self.menu_datas.nil? or self.menu_datas.blank?
      @menu_datas = self.menu_datas.join(",")
      if @menu_datas.strip.length > 0
        @menu_dts = @menu_datas.split(",")
        @menu_dts.each do |menu|
          unless menu.nil?
            @school_menu = SchoolMenuLink.new()
            @school_menu.menu_link_id = menu
            @school_menu.school_id = MultiSchool.current_school.id
            @school_menu.save
          end
        end
      end
    end
    
    seed_file = File.join(Rails.root,'db', 'seeds.rb')
    ARGV[0] = self.exam_grade_file_name
    ARGV[1] = pack_id
    ARGV[2] = self.import_exam_grade
    load(seed_file) if File.exist?(seed_file)
    Champs21Plugin.load_plugin_seed_data
    
    Configuration.find_or_create_by_config_key("InstitutionName").update_attributes(:config_value=>self.name)
    
    ARGV[0] = self.class_file_name
    ARGV[1] = self.import_class_seed
    seed_file = File.join(Rails.root,'db', 'batch.rb')
    load(seed_file) if File.exist?(seed_file)
    
    ARGV[0] = self.emp_cat_file_name
    ARGV[1] = self.emp_dept_file_name
    ARGV[2] = self.emp_grade_file_name
    
    ARGV[3] = self.import_cate_seeds
    ARGV[4] = self.import_dept_seed
    ARGV[5] = self.import_emp_grade
    seed_file = File.join(Rails.root,'db', 'employee.rb')
    load(seed_file) if File.exist?(seed_file)
    
    #Configuration.find_or_create_by_config_key("TimeZone").update_attributes(:config_value=>10)
    self.school_group.load_local_settings(self)
    RecordUpdate.update_school_run(self.id)
  end

  def create_champs21_school_seed
    MultiSchool.current_school = self
    seed_file = File.join(Rails.root,'db', 'seeds.rb')
    load(seed_file) if File.exist?(seed_file)
    Champs21Plugin.load_plugin_seed_data
    #    self.school_group.load_local_settings(self)
  end
  
  def update_menu_links
    MultiSchool.current_school = self
    #seed_file = File.join(Rails.root,'db', 'seeds.rb')
    #load(seed_file) if File.exist?(seed_file)
    #hamps21Plugin.load_plugin_seed_data
    #    self.school_group.load_local_settings(self)
  end

  def multischool_setting_file
    File.join(RAILS_ROOT,"vendor","plugins","acts_as_multi_school","config","multischool_settings.yml")
  end

  def multischool_setting
    MultiSchool.multischool_settings
  end

  def settings_file_exists?
    File.exists?(multischool_setting_file)
  end

  def valid_settings_file?
    settings_file_exists? and multischool_setting["settings"].present?
  end

  def max_school_count_setting
    multischool_setting["max_school_count"].to_i
  end

  def create_sms_settings
    sms_settings = YAML::load(File.open("#{Rails.root}/config/sms_settings.yml.example"))
    ms_sms_settings = YAML::load(File.open("#{Rails.root}/config/sms_settings_multischool.yml")) if File.exists?("#{Rails.root}/config/sms_settings_multischool.yml")
    ms_sms_settings ||= Hash.new
    ms_sms_settings[self.code] = sms_settings
    ms_sms_settings_file = File.new("#{Rails.root}/config/sms_settings_multischool.yml", "w+")
    ms_sms_settings_file.syswrite(ms_sms_settings.to_yaml)
    ms_sms_settings_file.close
  end

  def self.load_sms_settings
    settings = {}
    if File.exists?("#{Rails.root}/config/sms_settings_multischool.yml")
      settings = YAML::load(File.open("#{Rails.root}/config/sms_settings_multischool.yml"))
    end
    return settings
  end

  def self.update_sms_settings(new_settings = Hash.new)
    ms_sms_settings_file = File.new("#{Rails.root}/config/sms_settings_multischool.yml", "w+")
    ms_sms_settings_file.syswrite(new_settings.to_yaml)
    ms_sms_settings_file.close
  end

  def remove_sms_settings
    sms_settings = School.load_sms_settings
    sms_settings.delete(self.code)
    School.update_sms_settings(sms_settings)
  end

  def soft_delete
    if update_attribute(:is_deleted,true)
      school_domains.destroy_all
    end
  end

  def available_plugins
    available_plugin ? available_plugin.plugins : []
  end

  def effective_sms_settings
    if inherit_sms_settings
      school_group.effective_sms_settings
    else
      (sms_credential && (sms_credential.settings.is_a? Hash))?  sms_credential.settings : nil
    end
  end

  def effective_smtp_settings
    if inherit_smtp_settings
      school_group.effective_smtp_settings
    else
      (smtp_setting && (smtp_setting.settings.is_a? Hash))? smtp_setting.settings_to_sym : nil
    end    
  end

  private
  #  def cache_flush
  #    users.each do |user|
  #      Rails.cache.delete("user_main_menu#{user.id}")
  #    end
  #  end

end

