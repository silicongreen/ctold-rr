#Champs21
#Copyright 2011 teamCreative Private Limited
#
#This product includes software developed at
#Project Champs21 - http://www.champs21.com/
#
#Licensed under the Apache License, Version 2.0 (the "License");
#you may not use this file except in compliance with the License.
#You may obtain a copy of the License at
#
#  http://www.apache.org/licenses/LICENSE-2.0
#
#Unless required by applicable law or agreed to in writing, software
#distributed under the License is distributed on an "AS IS" BASIS,
#WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#See the License for the specific language governing permissions and
#limitations under the License.

class User < ActiveRecord::Base
  attr_accessor :password, :role, :old_password, :new_password, :confirm_password
  
  attr_accessor :pass, :save_to_free, :gender, :nationality_id, :birth_date
  attr_accessor :middle_name, :save_to_log, :guardian, :admission_no

  validates_uniqueness_of :username, :scope=> [:is_deleted],:if=> 'is_deleted == false' #, :email
  validates_length_of     :username, :within => 1..50
  validates_length_of     :password, :within => 4..40, :allow_nil => true
  validates_format_of     :username, :with => /^[A-Z0-9._-]*$/i,
    :message => :must_contain_only_letters
  validates_format_of     :email, :with => /^[A-Z0-9._%-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i,   :allow_blank=>true,
    :message => :must_be_a_valid_email_address
  validates_presence_of   :role , :on=>:create
  validates_presence_of   :password, :on => :create
  validates_presence_of   :first_name

  has_and_belongs_to_many :privileges
  has_many  :user_events
  has_many  :events,:through=>:user_events

  has_many :user_menu_links
  has_many :menu_links, :through=>:user_menu_links

  has_one :student_entry,:class_name=>"Student",:foreign_key=>"user_id"
  has_one :guardian_entry,:class_name=>"Guardian",:foreign_key=>"user_id"
  has_one :archived_student_entry,:class_name=>"ArchivedStudent",:foreign_key=>"user_id"
  has_one :employee_entry,:class_name=>"Employee",:foreign_key=>"user_id"
  has_one :archived_employee_entry,:class_name=>"ArchivedEmployee",:foreign_key=>"user_id"
  has_one :biometric_information, :dependent => :destroy

  
  named_scope :active,{:conditions=>["is_approved = true and (is_deleted=false OR parent=1)"]}
  named_scope :activevisible, :conditions => { :is_deleted => false,:is_visible=> 1 }
  named_scope :inactive, :conditions => { :is_deleted => true }

  after_save :create_default_menu_links, :save_user_to_free

  def before_save
    self.salt = random_string(8) if self.salt == nil
    self.hashed_password = Digest::SHA1.hexdigest(self.salt + self.password) unless self.password.nil?
    if self.new_record?
      self.admin, self.student, self.employee = false, false, false
      self.admin    = true if self.role == 'Admin'
      self.student  = true if self.role == 'Student'
      self.employee = true if self.role == 'Employee'
      self.parent = true if self.role == 'Parent'
      self.is_first_login = true
    end
  end

  def save_user_to_free
    unless self.save_to_free.nil?
      if self.save_to_free
        pass = self.pass.blank? ? "123456" : self.pass.to_s
        unless self.save_to_log.nil?
          if self.save_to_log
            if self.employee
                ar_teacher_log_params = {"employee_number" => self.username, "password" => pass, 
                                        "first_name"  => self.first_name, "middle_name"  => self.middle_name, 
                                        "last_name"  => self.last_name, "teacher_id" => self.id, "school_id" => MultiSchool.current_school.id}

                employee_tmp = TeacherLog.find_by_employee_number_and_school_id(self.username, MultiSchool.current_school.id)
                unless employee_tmp.nil?
                  employee_tmp.update_attributes(ar_teacher_log_params)
                else
                  teacher_log = TeacherLog.new ar_teacher_log_params
                  teacher_log.save
                end
            elsif self.student
              school_id = MultiSchool.current_school.id
              school_id_str = school_id.to_s.length < 2 ? "0" + school_id.to_s : "" + school_id.to_s
              username = self.username
              admission_no = username.gsub(school_id_str + "-", '')


              ar_student_gurdian_params = {"admission_no" => admission_no, "s_username" => username, "s_password"  => self.pass, 
                                           "s_first_name"  => self.first_name, "s_middle_name"  => self.middle_name, 
                                           "s_last_name"  => self.last_name, "student_id" => self.id, "school_id" => school_id}

              student_gurdian_tmp = StudentsGuardian.find_by_s_username_and_school_id(username, school_id)
              unless student_gurdian_tmp.nil?
                student_gurdian_tmp.update_attributes(ar_student_gurdian_params)
              else
                student_gurdian = StudentsGuardian.new ar_student_gurdian_params
                student_gurdian.save 
              end
            end
            
            if self.admin == false and self.employee == false and self.student == false
              unless self.guardian.nil?
                if self.guardian
                  school_id = MultiSchool.current_school.id
                  school_id_str = school_id.to_s.length < 2 ? "0" + school_id.to_s : "" + school_id.to_s
                  student_username = school_id_str + "-" + self.admission_no 
                  student_gurdian = StudentsGuardian.find_by_s_username_and_school_id(student_username, school_id)
                  
                  unless student_gurdian.nil?
                    ar_student_gurdian_params = {"g_first_name" => self.first_name, "g_last_name" => self.last_name, "g_username"  => self.username, 
                                                 "g_password"  => self.pass, "guardian_id" => self.id}
                    student_gurdian.update_attributes(ar_student_gurdian_params)  
                    
                  end
                  
                  
                  
                end
              end
            end
          end
        end
        
        user_type = "3"
        if self.student == true
          user_type = "2"
        end
        
        if self.admin == false and self.employee == false and self.student == false
          unless self.guardian.nil?
            if self.guardian
              user_type = "4"
            end
          end
        end
        
        champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
        api_endpoint = champs21_api_config['api_url']
        uri = URI(api_endpoint + "api/user/createuser")
        http = Net::HTTP.new(uri.host, uri.port)

        auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
        
        gender_values = {}
        dob_values = {}
        unless self.gender.nil?
          gender_values = {
            "gender"            => self.gender == 'f' ? '0' : '1'
          }
        end
        
        unless self.birth_date.nil?
          dob_values = {
            "dob"               => self.birth_date.to_s
          }
        end
        
        comp_values = {
            "paid_id"           => self.id, 
            "paid_username"     => self.username, 
            "paid_password"     => pass, 
            "password"          => pass, 
            "paid_school_id"    => MultiSchool.current_school.id, 
            "paid_school_code"  => MultiSchool.current_school.code.to_s,
            "first_name"        => self.first_name, 
            "middle_name"       => self.middle_name, 
            "last_name"         => self.last_name,
            "country"           => self.nationality_id.nil? ? '14' : self.nationality_id.to_s, 
            "email"             => self.username, 
            "user_type"         => user_type
        }
        
        values = comp_values.merge(gender_values).merge(dob_values)
        
        is_employee = self.employee == true ? true : false
        is_admin =  self.admin == true ? true : false
        is_student =  self.student == true ? true : false
        is_guardian =  self.guardian == true ? true : false
        auth_req.set_form_data(values)
        auth_res = http.request(auth_req)
        
        begin
        @auth_response = JSON::parse(auth_res.body)  
        unless @auth_response["status"].nil?
          if @auth_response["status"]["code"] == 200 and @auth_response["status"]['msg'] == "Successfully Saved"
            ARGV[1001] = "SAVED_SUCCESSFULLY"
          else  
            ar_log_param = {
            'username'  => self.username,
            'error'     => @auth_response.inspect,
            'trace'     => "Invalid Status code or message",
            'admin'     => is_admin,
            'employee'  => is_employee,
            'student'   => is_student,
            'guardian'  => is_guardian,
            'status'    => "0"
          }
          error_log = ErrorLogOnCreateUser.new ar_log_param
          error_log.save  
            ARGV[1001] = "INVALID_STATUS"
          end
        else
          ar_log_param = {
            'username'  => self.username,
            'error'     => @auth_response.inspect,
            'trace'     => "Status not in response",
            'admin'     => is_admin,
            'employee'  => is_employee,
            'student'   => is_student,
            'guardian'  => is_guardian,
            'status'    => "0"
          }
          error_log = ErrorLogOnCreateUser.new ar_log_param
          error_log.save  
          ARGV[1001] = "SOMTHING_WRONG_ON_FREE_DB"
        end
        rescue JSON::ParserError => e
          ar_log_param = {
            'username'  => self.username,
            'error'     => e.inspect,
            'trace'     => e.backtrace.inspect,
            'admin'     => is_admin,
            'employee'  => is_employee,
            'student'   => is_student,
            'guardian'  => is_guardian,
            'status'    => "0"
          }
          error_log = ErrorLogOnCreateUser.new ar_log_param
          error_log.save  
          ARGV[1001] = "JSON_PARSE_ERROR"
        end
      end
    end
  end
  
  def create_default_menu_links
    
    package_id = 0
    packages = SchoolPackage.find_by_school_id(MultiSchool.current_school.id)
    unless packages.nil? or packages.blank?
      package_id = packages.package_id
    end
    
    changes_to_be_checked = ['admin','student','employee','parent']
    check_changes = self.changed & changes_to_be_checked
    if (self.new_record? or check_changes.present?)
      self.menu_links = []
      default_links = []
      if self.admin?
        default_link_name = ["human_resource","settings","students","calendar_text","news_text","event_text"]
        main_links_general = MenuLink.find(:all, :conditions => ["name IN (?) and link_type = 'general'", default_link_name])
        default_links = default_links + main_links_general
        main_links_general.each do|link|
          sub_links = MenuLink.find(:all, :conditions => ["higher_link_id = ? and link_type = 'general' ",link.id])
          default_links = default_links + sub_links
          
          sub_links = MenuLink.find(:all, :conditions => ["higher_link_id = ? and link_type = 'user_menu' and ID in (select menu_id from package_menus where package_id = ?) ",link.id, package_id])
          default_links = default_links + sub_links
        end
        
        main_links_user = MenuLink.find(:all, :conditions => ["name IN (?) and link_type = 'user_menu' and ID in (select menu_id from package_menus where package_id = ?)", default_link_name, package_id])
        
        default_links = default_links + main_links_user
        main_links_user.each do|link|
          sub_links = MenuLink.find(:all, :conditions => ["higher_link_id = ? and link_type = 'general' ",link.id])
          default_links = default_links + sub_links
          
          sub_links = MenuLink.find(:all, :conditions => ["higher_link_id = ? and link_type = 'user_menu' and ID in (select menu_id from package_menus where package_id = ?) ",link.id, package_id])
          default_links = default_links + sub_links
        end
      elsif self.employee?
        
        default_links = MenuLink.find_all_by_user_type("employee")
        menu_for_teacher = MenuLink.find_all_by_name(["news_text","calendar_text"])
        menu_for_teacher.each do |menu|
          menu_id = menu.id
          menu_links = MenuLink.find_by_id(menu_id)
          if menu_links.link_type == 'user_menu'
            school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
            unless school_menu_links.blank?
              default_links << menu
            end
          elsif menu_links.link_type == 'general'  
              default_links << menu
          end
        end
        
        #own_links.each do |own|
        #  default_links << own
        #end
        #default_links = own_links + default_links
        #default_links = own_links + MenuLink.find_all_by_name(["news_text","calendar_text"])
      else
        own_links = []
        links_for_student =  MenuLink.find_all_by_name_and_user_type(["my_profile","timetable_text","academics"],"student")
        links_for_student.each do |menu|
          menu_id = menu.id
          menu_links = MenuLink.find_by_id(menu_id)
          if menu_links.link_type == 'own' and menu_links.reference_id > 0
            menu_id = menu.reference_id
            school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
            unless school_menu_links.blank?
              default_links << menu
            end
          else
              default_links << menu
          end
        end
      
        menu_for_student = MenuLink.find_all_by_name(["news_text","calendar_text"])
        menu_for_student.each do |menu|
          menu_id = menu.id
          menu_links = MenuLink.find_by_id(menu_id)
          if menu_links.link_type == 'user_menu'
            school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
            unless school_menu_links.blank?
              default_links << menu
            end
          elsif menu_links.link_type == 'general'  
              default_links << menu
          end
        end
      end
      self.menu_links = default_links
    end
  end

  def delete_user_menu_caches
    Rails.cache.delete("user_quick_links#{self.id}")
    menu_cats = MenuLinkCategory.all
    menu_cats.each do|cat|
      Rails.cache.delete("user_cat_links_#{cat.id}_#{self.id}")
    end
  end


  def student_record
    self.is_deleted ? self.archived_student_entry : self.student_entry
  end

  def employee_record
    self.is_deleted ? self.archived_employee_entry : self.employee_entry
  end

  def full_name
    "#{first_name} #{last_name}"
  end
  
  def all_new_reminders_user
    new_reminder = Reminder.find_all_by_recipient(self.id, :conditions=>"is_read = false and is_deleted_by_recipient = false",:limit=>10,:order=>"created_at DESC")
    return new_reminder
  end
  
  def get_banner
    require 'net/http'
    require 'uri'
    require "yaml"
    
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
    champs21_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/app.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    
    uri = URI(api_endpoint + "api/freeschool/getbanner")
    http = Net::HTTP.new(uri.host, uri.port)
    auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
    auth_req.set_form_data({"school_id" => self.school_id})
   
 
    banner = []
    if champs21_config['from'] == "remote"
      auth_res = http.request(auth_req)    
      @auth_response = JSON::parse(auth_res.body)
      if @auth_response['status']['code']==200
          banner = @auth_response
      end  
    end
    return banner
  end


  def reminder_count
    reminders = Reminder.count(:all,:group =>  'rtype' , :conditions => ["recipient = '#{self.id}' and is_read= 0"])
    return reminders
  end
  def check_reminders
    reminders =[]
    reminders = Reminder.find(:all , :conditions => ["recipient = '#{self.id}'"])
    count = 0
    reminders.each do |r|
      unless r.is_read
        count += 1
      end
    end
    return count
  end

  def self.authenticate?(username, password)
    u = User.find_by_username username
    u.hashed_password == Digest::SHA1.hexdigest(u.salt + password)
  end

  def random_string(len)
    randstr = ""
    chars = ("0".."9").to_a + ("a".."z").to_a + ("A".."Z").to_a
    len.times { randstr << chars[rand(chars.size - 1)] }
    randstr
  end

  def role_name
    return "#{t('admin')}" if self.admin?
    return "#{t('student_text')}" if self.student?
    return "#{t('employee_text')}" if self.employee?
    return "#{t('parent')}" if self.parent?
    return nil
  end

  def role_symbols
    prv = []
    privileges.map { |privilege| prv << privilege.name.underscore.to_sym } unless @privilge_symbols

    @privilge_symbols ||= if admin?
      [:admin] + prv
    elsif student?
      [:student] + prv
    elsif employee?
      [:employee] + prv
    elsif parent?
      [:parent] + prv
    else
      prv  
    end
  end

  def is_allowed_to_mark_attendance?
    if self.employee?
      attendance_type = Configuration.get_config_value('StudentAttendanceType')
      if ((self.employee_record.subjects.present? and attendance_type == 'SubjectWise') or (self.employee_record.batches.find(:all,:conditions=>{:is_deleted=>false,:is_active=>true}).present? and attendance_type == 'Daily'))
        return true
      end
    end
    return false
  end

  def can_view_results?
    if self.employee?
      return true if self.employee_record.batches.find(:all,:conditions=>{:is_deleted=>false,:is_active=>true}).present?
    end
    return false
  end

  def has_assigned_subjects?
    if self.employee?
      employee_subjects= self.employee_record.subjects
      if employee_subjects.empty?
        return false
      else
        return true
      end
    else
      return false
    end
  end

  def clear_menu_cache
    Rails.cache.delete("user_main_menu#{self.id}")
    Rails.cache.delete("user_autocomplete_menu#{self.id}")
  end
  def clear_school_name_cache(request_host)
    Rails.cache.delete("current_school_name/#{request_host}")
  end

  def parent_record
    #    p=Student.find_by_admission_no(self.username[1..self.username.length])
    unless guardian_entry.nil?
      guardian_entry.current_ward
    else
      Student.find_by_admission_no(self.username[1..self.username.length])
    end

    #    p '-------------'
    #    p self.username[1..self.username.length]
    #     Student.find_by_sibling_no_and_immediate_contact(self.username[1..self.username.length])
    #guardian_entry.ward
  end

  def has_subject_in_batch(b)
    employee_record.subjects.collect(&:batch_id).include? b.id
  end

  def days_events(date)
    all_events=[]
    case(role_name)
    when "Admin"
      all_events=Event.find(:all,:conditions => ["? between date(events.start_date) and date(events.end_date)",date])
    when "Student"
      all_events+= events.all(:conditions=>["? between date(events.start_date) and date(events.end_date)",date])
      all_events+= student_record.batch.events.all(:conditions=>["? between date(events.start_date) and date(events.end_date)",date])
      all_events+= Event.all(:conditions=>["(? between date(events.start_date) and date(events.end_date)) and is_common = true",date])
    when "Parent"
      all_events+= events.all(:conditions=>["? between date(events.start_date) and date(events.end_date)",date])
      all_events+= parent_record.user.events.all(:conditions=>["? between date(events.start_date) and date(events.end_date)",date])
      all_events+= parent_record.batch.events.all(:conditions=>["? between date(events.start_date) and date(events.end_date)",date])
      all_events+= Event.all(:conditions=>["(? between date(events.start_date) and date(events.end_date)) and is_common = true",date])
    when "Employee"
      all_events+= events.all(:conditions=>["? between events.start_date and events.end_date",date])
      all_events+= employee_record.employee_department.events.all(:conditions=>["? between date(events.start_date) and date(events.end_date)",date])
      all_events+= Event.all(:conditions=>["(? between date(events.start_date) and date(events.end_date)) and is_exam = true",date])
      all_events+= Event.all(:conditions=>["(? between date(events.start_date) and date(events.end_date)) and is_common = true",date])
    end
    all_events
  end

  def next_event(date)
    all_events=[]
    case(role_name)
    when "Admin"
      all_events=Event.find(:all,:conditions => ["? < date(events.end_date)",date],:order=>"start_date")
    when "Student"
      all_events+= events.all(:conditions=>["? < date(events.end_date)",date])
      all_events+= student_record.batch.events.all(:conditions=>["? < date(events.end_date)",date],:order=>"start_date")
      all_events+= Event.all(:conditions=>["(? < date(events.end_date)) and is_common = true",date],:order=>"start_date")
    when "Parent"
      all_events+= events.all(:conditions=>["? < date(events.end_date)",date])
      all_events+= parent_record.user.events.all(:conditions=>["? < date(events.end_date)",date])
      all_events+= parent_record.batch.events.all(:conditions=>["? < date(events.end_date)",date],:order=>"start_date")
      all_events+= Event.all(:conditions=>["(? < date(events.end_date)) and is_common = true",date],:order=>"start_date")
    when "Employee"
      all_events+= events.all(:conditions=>["? < date(events.end_date)",date],:order=>"start_date")
      all_events+= employee_record.employee_department.events.all(:conditions=>["? < date(events.end_date)",date],:order=>"start_date")
      all_events+= Event.all(:conditions=>["(? < date(events.end_date)) and is_exam = true",date],:order=>"start_date")
      all_events+= Event.all(:conditions=>["(? < date(events.end_date)) and is_common = true",date],:order=>"start_date")
    end
    start_date=all_events.collect(&:start_date).min
    unless start_date
      return ""
    else
      next_date=(start_date.to_date<=date ? date+1.days : start_date )
      next_date
    end
  end
  def soft_delete
    self.update_attributes(:is_deleted =>true)
  end

  def user_type
    admin? ? "Admin" : employee? ? "Employee" : student? ? "Student" : "Parent"
  end
  def school_details
    name=Configuration.get_config_value('InstitutionName').present? ? "#{Configuration.get_config_value('InstitutionName')}," :""
    address=Configuration.get_config_value('InstitutionAddress').present? ? "#{Configuration.get_config_value('InstitutionAddress')}," :""
    Configuration.get_config_value('InstitutionPhoneNo').present?? phone="#{' Ph:'}#{Configuration.get_config_value('InstitutionPhoneNo')}" :""
    return (name+"#{' '}#{address}"+"#{phone}").chomp(',')
  end
  def school_name
    Configuration.get_config_value('InstitutionName')
  end
end
