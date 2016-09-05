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

class Guardian < ActiveRecord::Base
  attr_accessor :pass, :save_to_free, :save_log
  
  belongs_to :country
  belongs_to :ward, :class_name => 'Student', :foreign_key=>:sibling_id
  belongs_to :user
  has_many   :wards,:class_name => 'Student', :foreign_key => 'sibling_id', :primary_key=>:ward_id

  validates_presence_of :first_name, :relation,:ward_id
  validates_format_of     :email, :with => /^[A-Z0-9._%-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i,   :allow_blank=>true,
    :message => :must_be_a_valid_email_address
  before_destroy :immediate_contact_nil
  before_validation :email_strip
  before_save :add_attr_vals
  after_save :reset_argv
  #after_create :set_sibling_id

  def email_strip
    self.email = self.email.strip if email
  end

  def validate
    errors.add(:dob, :cant_be_a_future_date) if self.dob > Date.today unless self.dob.nil?
  end

  def add_attr_vals
    unless self.save_to_free.nil?
      ARGV[0] = self.pass
      ARGV[1] = self.save_to_free
    end
  end
  
  def reset_argv
    unless ARGV[0].nil? and ARGV[1].nil?
      ARGV[0] = nil
      ARGV[1] = nil
    end
    
    check_guardian = GuardianStudents.find_by_student_id_and_guardian_id(self.ward_id,self.id)
    if check_guardian.nil?
      stdgu = GuardianStudents.new
      stdgu.student_id = self.ward_id
      stdgu.guardian_id = self.id
      stdgu.save
    end 
  end
  
  def is_immediate_contact?
    ward.immediate_contact_id == id
  end

  def full_name
    "#{first_name} #{last_name}"
  end

  def archive_guardian(archived_student,id)
    student=Student.find(id)
    guardian_attributes = self.attributes
    #guardian_attributes.merge!(:sibling_id=>student.sibling_id)
    #guardian_attributes.merge!(:former_id=>self.id)
    guardian_attributes.delete "id"
    guardian_attributes.delete "user_id"
    guardian_attributes["ward_id"] = student.sibling_id
    if d=ArchivedGuardian.create(guardian_attributes)
      # guardian_attributes.delete "ward_id"
      #d.update_attributes(guardian_attributes)
      if student.all_siblings.empty?
        self.user.soft_delete if self.user.present?
        self.destroy
        #      else
        #        self.update_attributes(:ward_id=>student.siblings.select{|w| w.id!=id}.first.id)
        #        update_attributes(:sibling_id=>ward_id)
        #        Student.update_all({:sibling_id=>ward_id},{:id=>student.siblings.collect(&:id)})
        #self.update_attributes(:ward_id=>Student.find(:first,:conditions=>("id=#{archived_student.sibling_id}" or "sybling_id=#{archived_student.sibling_id}" )))
      end
    end
  end

  def create_guardian_user(student,is_deleted=false)
    user = User.new do |u|
      u.first_name = self.first_name
      u.last_name = self.last_name
      g_start = "p1"
      u_name=g_start+student.admission_no.to_s
      
      if u_name.index(MultiSchool.current_school.code.to_s+"-")==nil
        school_id = MultiSchool.current_school.id
        school_id_str = school_id.to_s.length < 2 ? "0" + school_id.to_s : "" + school_id.to_s
        u_name = school_id_str+"-"+u_name
      end 
      
      begin
        user_record=User.find_by_username(u_name)
        if user_record.present?
          g_start = g_start.next
          u_name=g_start+student.admission_no.to_s
          if u_name.index(MultiSchool.current_school.code.to_s+"-")==nil
            school_id = MultiSchool.current_school.id
            school_id_str = school_id.to_s.length < 2 ? "0" + school_id.to_s : "" + school_id.to_s
            u_name = school_id_str+"-"+u_name
          end
        end
      end while user_record.present?
      u.username = u_name
      u.password = self.pass.blank? ? "123456" : self.pass.to_s
      u.role = 'Parent'
      u.email = ( email == '' or User.active.find_by_email(self.email) ) ? self.email.to_s : ""
      
      u.is_deleted = is_deleted
      
      u.pass = self.pass.blank? ? "123456" : self.pass.to_s
      u.guardian = true
     
      u.save_to_log = true
      u.admission_no = student.admission_no
      
      unless self.save_to_free.nil?
        if self.save_to_free
          u.save_to_free = 1
          u.nationality_id = self.country_id
          u.middle_name = ""
        end
      end
      
    end
    
    self.update_attributes(:user_id => user.id) if user.save
  end



  def self.shift_user(student, pass = nil, save_to_free = nil)
    current_student=Student.find(student.id)
    current_guardian =  student.immediate_contact
    Guardian.find(:all,:conditions=>"ward_id=#{current_student.sibling_id}").each do |g|
      #student.guardians.each do |g|

      unless (student.all_siblings).collect(&:immediate_contact_id).include?(g.id)

        parent_user = g.user
        parent_user.soft_delete if parent_user.present? and (parent_user.is_deleted==false)and ((current_guardian.present? ) and current_guardian!=g)
        #parent_user.soft_delete if parent_user.present? and (parent_user.is_deleted==false) and ((current_guardian.present? and current_guardian.user.present?) and current_guardian.user!=parent_user)

      end
    end
    if current_guardian.present?
      if current_guardian.user.present?
        current_guardian.user.update_attribute(:is_deleted,false) if current_guardian.user.is_deleted
      else
        unless pass.nil?
          current_guardian.pass = pass
        end
        unless save_to_free.nil?
          current_guardian.save_to_free = save_to_free
        end
        current_guardian.create_guardian_user(student)
      end
    end
  end

  def immediate_contact_nil
    student = self.current_ward
    if student.present? and (student.immediate_contact_id==self.id)
      student.update_attribute(:immediate_contact_id,nil)
    end
  end
  
  def guardian_student
    students=[]
    batch_students= GuardianStudents.find_all_by_guardian_id(self.id)
    batch_students.each do|bs|
      stu = Student.find_by_id(bs.student_id)
      students.push stu unless stu.nil?
    end
    return students
  end

  #EDITED FOR MULTIPLE GUARDIAN
  def current_ward
    #Student.find_by_id_and_immediate_contact_id(current_ward_id,id)
    Student.find(:first,:conditions=>["id=?",current_ward_id])

  end
  
  #EDITED FOR MULTIPLE GUARDIAN
  def current_ward_id
    (Champs21.present_student_id.present?) ? student=Student.find(Champs21.present_student_id) : student=nil
    checked = false
    if student.present?
      batch_students= GuardianStudents.find_all_by_guardian_id(self.id)
      batch_students.each do|bs|
        if bs.student_id == student.id
          checked = true
        end
      end
      
    end
    if checked
      Champs21.present_student_id
    else
      batch_students= GuardianStudents.find_all_by_guardian_id(self.id)
      if !batch_students.nil?
        batch_students.first.student_id
      end
    end
  end
  
#  def current_ward_id
#    (Champs21.present_student_id.present? and wards.collect(&:id).include?((Champs21.present_student_id).to_i)) ? student=Student.find(Champs21.present_student_id) : student=nil
#    if (student.present? and student.immediate_contact_id==id)
#      Champs21.present_student_id
#    else
#      wards.select{|w| w.immediate_contact_id==id}.first.id
#    end
#  end
  def set_sibling_id
    update_attribute(:sibling_id,id)
  end
end
