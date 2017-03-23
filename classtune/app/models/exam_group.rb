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

class ExamGroup < ActiveRecord::Base
  validates_presence_of :name
  belongs_to :batch
  belongs_to :grouped_exam

  has_many :exams, :dependent => :destroy, :order => 'exams.start_time'
  before_destroy :removable?
  belongs_to :cce_exam_category

  accepts_nested_attributes_for :exams

  attr_accessor :maximum_marks, :minimum_marks, :weightage
  validates_associated :exams

  after_save :invalidate_student_cache, :on=>:update

  validates_uniqueness_of :cce_exam_category_id, :scope=>:batch_id, :message=>"already assigned for another Exam Group",:unless => lambda { |e| e.cce_exam_category_id.nil?}
  
  def removable?
    self.exams.reject{|e| e.removable?}.empty?
  end

  def invalidate_student_cache
    batch.delete_student_cce_report_cache if result_published_changed?
    exams = self.exams
    grouped_exams = GroupedExam.find_all_by_exam_group_id(self.id)
    unless exams.blank?
      exams.each do |exam|
        Rails.cache.delete("exam_group_from_exam_#{exam.id}")
      end
    end
    unless grouped_exams.blank?
      grouped_exams.each do |grouped_exam|
        Rails.cache.delete("group_exam_from_exam_connect_#{grouped_exam.connect_exam_id}")
      end
    end
    
    Rails.cache.delete("batch_from_exam_group_#{self.id}")
    
  end

  def before_save
    self.exam_date = self.exam_date || Date.today 
  end

  def before_validation
    if self.exam_type.downcase == "grades"
      self.exams.each do |ex|
        ex.maximum_marks = 0
        ex.minimum_marks = 0
      end
    end
  end

  def batch_average_marks(marks)
    batch = self.batch
    exams = self.exams
    batch_students = batch.students
    total_students_marks = 0
    #   total_max_marks = 0
    students_attended = []
    exams.each do |exam|
      batch_students.each do |student|
        exam_score = ExamScore.find_by_student_id_and_exam_id(student.id,exam.id)
        unless exam_score.nil?
          unless exam_score.marks.nil?
            total_students_marks = total_students_marks+exam_score.marks
            unless students_attended.include? student.id
              students_attended.push student.id
            end
          end
        end
      end
      #      total_max_marks = total_max_marks+exam.maximum_marks
    end
    unless students_attended.size == 0
      batch_average_marks = total_students_marks/students_attended.size
    else
      batch_average_marks = 0
    end
    return batch_average_marks if marks == 'marks'
    #   return total_max_marks if marks == 'percentage'
  end

  def weightage
    grp = GroupedExam.find_by_batch_id_and_exam_group_id(self.batch.id,self.id)
    unless grp.nil?
      weight = grp.weightage
    else
      weight=0
    end
    return weight
  end

  def archived_batch_average_marks(marks)
    batch = self.batch
    exams = self.exams
    batch_students = ArchivedStudent.find_all_by_batch_id(self.batch.id)
    total_students_marks = 0
    #   total_max_marks = 0
    students_attended = []
    exams.each do |exam|
      batch_students.each do |student|
        exam_score = ArchivedExamScore.find_by_student_id_and_exam_id(student.id,exam.id)
        unless exam_score.nil?
          unless exam_score.marks.nil?
            total_students_marks = total_students_marks+exam_score.marks
            unless students_attended.include? student.id
              students_attended.push student.id
            end
          end
        end
      end
      #      total_max_marks = total_max_marks+exam.maximum_marks
    end
    unless students_attended.size == 0
      batch_average_marks = total_students_marks/students_attended.size
    else
      batch_average_marks = 0
    end
    return batch_average_marks if marks == 'marks'
  end

  def batch_average_percentage
    
  end

  def subject_wise_batch_average_marks(subject_id)
    batch = self.batch
    subject = Subject.find(subject_id)
    exam = Exam.find_by_exam_group_id_and_subject_id(self.id,subject.id)
    batch_students = batch.students
    total_students_marks = 0
    #   total_max_marks = 0
    students_attended = []

    batch_students.each do |student|
      exam_score = ExamScore.find_by_student_id_and_exam_id(student.id,exam.id)
      unless exam_score.nil?
        total_students_marks = total_students_marks+ (exam_score.marks || 0)
        unless students_attended.include? student.id
          students_attended.push student.id
        end
      end
    end
    #      total_max_marks = total_max_marks+exam.maximum_marks
    unless students_attended.size == 0
      subject_wise_batch_average_marks = total_students_marks/students_attended.size.to_f
    else
      subject_wise_batch_average_marks = 0
    end
    return subject_wise_batch_average_marks
    #   return total_max_marks if marks == 'percentage'
  end

  def total_marks(student)
    exams = Exam.find_all_by_exam_group_id(self.id)
    total_marks = 0
    max_total = 0
    exams.each do |exam|
      exam_score = ExamScore.find_by_exam_id_and_student_id(exam.id,student.id)
      total_marks = total_marks + (exam_score.marks || 0) unless exam_score.nil?
      max_total = max_total + exam.maximum_marks unless exam_score.nil?
    end
    result = [total_marks,max_total]
  end

  def archived_total_marks(student)
    exams = Exam.find_all_by_exam_group_id(self.id)
    total_marks = 0
    max_total = 0
    exams.each do |exam|
      exam_score = ArchivedExamScore.find_by_exam_id_and_student_id(exam.id,student.id)
      total_marks = total_marks + (exam_score.marks || 0 ) unless exam_score.nil?
      max_total = max_total + exam.maximum_marks unless exam_score.nil?
    end
    result = [total_marks,max_total]
  end

  def is_removable(batches_id)
    removable = false
    exam_group_name = self.name
    batches_id.each do |b|
      tmp_exam = ExamGroup.find(:first, :conditions => ["name = ? and exam_type = ? and exam_category = ? and exam_date = ? and batch_id = ?", exam_group_name, self.exam_type, self.exam_category, self.exam_date, b])
      unless tmp_exam.nil?
        unless tmp_exam.removable?
          removable = true
          break
        end
      end
    end
    return removable
  end
  
  def course
    batch.course if batch
  end
  def parent_email
    email=[]
    self.batch.students.select{|s| s.is_email_enabled? and s.immediate_contact.present?}.each do |p|
      email<< p.immediate_contact.email.zip(p.immediate_contact.first_name).flatten

    end
    return email
  end
  def student_parent_email
    email={}
    self.batch.students.select{|s| s.immediate_contact.present?}.each do |p|
      hsh= (p.immediate_contact.email.zip(p.first_name.to_a))
      hs=hsh.first
      if hs.present?
        if email.keys.include?hs.first
          u=email[hs.first].gsub(" and ",",")+" and "+hs.last
          email[hs.first]=u
        else
          email.merge!(hs.first=>hs.last)
        end
      end
    end
    return email

  end
  
  def other_batches(batches_ids, b_show_assign = true, execute = true)
    unless execute
      return ""
    end
    batch_id = self.batch_id
    exam_group_name = self.name
    
    
    
    no_of_batches = batches_ids.length
    
    ar_exams = ExamGroup.find(:all, :conditions => ["name LIKE ? and batch_id IN (?) and exam_type = ? and exam_category = ? and exam_date = ?", self.name, batches_ids, self.exam_type, self.exam_category, self.exam_date]).map{|eg| eg.batch_id}
    
    s_message = "<p class='course_text'>Only for: "
    
    if ar_exams.length == no_of_batches
      return ""
    else
      batches_found = no_of_batches - ar_exams.length
      half_num_batches = ( no_of_batches / 2).floor
      
      s_assign_link_register = "Assign this exam to other sections"
      if batches_found <= half_num_batches
        s_message = "<p class='course_text'>This exam not registered to: <br />"
        ar_exams = batches_ids - ar_exams
        s_assign_link_register = "Assign this Exam"
      else  
        s_message = "<p class='course_text'>Only for: "
        s_assign_link_register = "Assign this exam to other sections"
      end
      
      ar_batches_name = Batch.find(:all, :conditions => ["id IN (?)", ar_exams ], :group => "name").map{|b| b.name}
      
      if ar_batches_name.length > 1
        s_message = s_message + "<br />"
      end
      i = 0
      ar_batches_name.each do |b|
        s_section_name = Course.find(:all, :conditions => ["batches.id IN (?) and batches.name = ?", ar_exams, b ], :joins => "INNER JOIN batches ON batches.course_id = courses.id").map{|c| c.section_name}.join(', ')
        if Batch.active.find(:all, :group => "name").length > 1
          s_message = s_message + b + " shift, Section: " + s_section_name
        else
          s_message = s_message + " Section: " + s_section_name
        end
        if ar_batches_name.length > i + 1
          s_message = s_message + "<br />"
        end
        i += 1
      end
      s_message = s_message + "</p>"
      if b_show_assign
        s_message = s_message + "|||" + s_assign_link_register
      end
      return s_message
    end
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

  def self.exam_schedule_details(parameters)
    sort_order=parameters[:sort_order]
    batch_id=parameters[:batch_id]
    if batch_id.nil? or batch_id[:batch_ids].blank?
      if sort_order.nil?
        examgroups=ExamGroup.all(:select=>"exam_groups.id,exam_groups.name,batches.name as batch_name,batches.id as batch_id,exam_type,courses.code",:conditions=>{:is_published=>true,:result_published=>false,:batches=>{:is_deleted=>false,:is_active=>true}},:joins=>[:batch=>:course],:order=>'name ASC')
        exam_group_ids=examgroups.collect(&:id)
        exams=Exam.all(:select=>"subjects.name,start_time,end_time,maximum_marks,minimum_marks,exam_group_id",:conditions=>{:exam_groups=>{:result_published=>false,:is_published=>true,:id=>exam_group_ids}},:joins=>[:exam_group,:subject]).group_by(&:exam_group_id)
      else
        examgroups=ExamGroup.all(:select=>"exam_groups.id,exam_groups.name,batches.name as batch_name,batches.id as batch_id,exam_type,courses.code",:conditions=>{:is_published=>true,:result_published=>false,:batches=>{:is_deleted=>false,:is_active=>true}},:joins=>[:batch=>:course],:order=>sort_order)
        exam_group_ids=examgroups.collect(&:id)
        exams=Exam.all(:select=>"subjects.name,start_time,end_time,maximum_marks,minimum_marks,exam_group_id",:conditions=>{:exam_groups=>{:result_published=>false,:is_published=>true,:id=>exam_group_ids}},:joins=>[:exam_group,:subject]).group_by(&:exam_group_id)
      end
    else
      if sort_order.nil?
        examgroups=ExamGroup.all(:select=>"exam_groups.id,exam_groups.name,batches.name as batch_name,batches.id as batch_id,exam_type,courses.code",:conditions=>{:is_published=>true,:result_published=>false,:batches=>{:id=>batch_id[:batch_ids]}},:joins=>[:batch=>:course],:order=>'name ASC')
        exam_group_ids=examgroups.collect(&:id)
        exams=Exam.all(:select=>"subjects.name,start_time,end_time,maximum_marks,minimum_marks,exam_group_id",:conditions=>{:exam_groups=>{:result_published=>false,:is_published=>true,:id=>exam_group_ids}},:joins=>[:exam_group,:subject]).group_by(&:exam_group_id)
      else
        examgroups=ExamGroup.all(:select=>"exam_groups.id,exam_groups.name,batches.name as batch_name,batches.id as batch_id,exam_type,courses.code",:conditions=>{:is_published=>true,:result_published=>false,:batches=>{:id=>batch_id[:batch_ids]}},:joins=>[:batch=>:course],:order=>sort_order)
        exam_group_ids=examgroups.collect(&:id)
        exams=Exam.all(:select=>"subjects.name,start_time,end_time,maximum_marks,minimum_marks,exam_group_id",:conditions=>{:exam_groups=>{:result_published=>false,:is_published=>true,:id=>exam_group_ids}},:joins=>[:exam_group,:subject]).group_by(&:exam_group_id)
      end
    end
    examgroups.each do |e|
      exam=exams[e.id]
      unless exam.nil?
        ex_name=[]
        max_mark=[]
        min_mark=[]
        start_tim=[]
        end_tim=[]
        exam.each do |s|
          ex_name << "#{s.name}"
          max_mark << " #{s.maximum_marks}"
          min_mark << " #{s.minimum_marks}"
          start_tim << " #{s.start_time}"
          end_tim << " #{s.end_time}"
        end
        exam_name= ex_name.join("\n")
        exam_max_mark= max_mark.join("\n")
        exam_min_mark= min_mark.join("\n")
        exam_start_time= start_tim.join("\n")
        exam_end_time= end_tim.join("\n")
      end
      e["exam_name"]=exam_name
      e["exam_max_mark"]=exam_max_mark
      e["exam_min_mark"]=exam_min_mark
      e["exam_start_time"]=exam_start_time
      e["exam_end_time"]=exam_end_time
    end
    data=[]
    col_heads=["#{t('no_text')}","#{t('exam_group')} #{t('name')}","#{t('batch_name')}","#{t('exam_type')}"," #{t('exam_text')}#{t('name')}", " #{t('maximum_marks')} ", " #{t('minimum_marks')} ", "#{t('start_time')} ", "#{t('end_time')}"]
    data << col_heads
    examgroups.each_with_index do |obj,i|
      col=[]
      col << "#{i+1}"
      col << "#{obj.name}"
      col << "#{obj.code}-#{obj.batch_name}"
      col << "#{obj.exam_type}"
      col << "#{obj.exam_name}"
      col << "#{obj.exam_max_mark}"
      col << "#{obj.exam_min_mark}"
      col << "#{obj.exam_start_time}"
      col << "#{obj.exam_end_time}"
      col = col.flatten
      data << col
    end
    return data
  end

end