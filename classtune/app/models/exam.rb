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

class Exam < ActiveRecord::Base
  validates_presence_of :start_time, :end_time, :if => :no_date_not_present?
  validates_numericality_of :maximum_marks, :minimum_marks, :allow_nil => true
  validates_presence_of :maximum_marks, :minimum_marks, :if => :validation_should_present?, :on=>:update
  belongs_to :exam_group
  belongs_to :subject, :conditions => { :is_deleted => false }
  before_destroy :removable?
  before_save :update_exam_group_date
  

  has_one :event ,:as=>:origin

  has_many :exam_scores
  has_many :archived_exam_scores
  has_many :previous_exam_scores
  has_many :assessment_scores
  #  has_and_belongs_to_many :cce_reports
  delegate :name,:is_current, :to => :exam_group
  
  accepts_nested_attributes_for :exam_scores
  
  def after_save
    grouped_exams = GroupedExam.find_all_by_exam_group_id(self.exam_group.id)
    exam_subject_id = self.subject.id
    
    unless grouped_exams.blank?
      grouped_exams.each do |grouped_exam|
        Rails.cache.delete("marksheet_#{grouped_exam.connect_exam_id}_#{exam_subject_id}")
        Rails.cache.delete("tabulation_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}")
        Rails.cache.delete("continues_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}")
        key = "student_exam_#{grouped_exam.connect_exam_id}_#{grouped_exam.batch_id}"
        Rails.cache.delete_matched(/#{key}*/)
      end
    end
  end
  
  def no_date_not_present?
    if self.no_date?
      return false
    else
      return true
    end
  end

  def validation_should_present?
    if self.exam_group.exam_type=="Grades"
      return false
    else
      return true
    end
  end
  
  def removable?
    self.exam_scores.reject{|es| es.marks.nil? and es.grading_level_id.nil?}.empty?

  end

  def validate
    errors.add_to_base :minmarks_cant_be_more_than_maxmarks \
      if minimum_marks and maximum_marks and minimum_marks > maximum_marks
    errors.add_to_base :minmarks_cant_be_more_than_maxmarks \
      if minimum_marks and maximum_marks and minimum_marks > maximum_marks
    unless self.start_time.nil? or self.end_time.nil?
      errors.add_to_base :end_time_cannot_before_start_time if self.end_time < self.start_time
    end
  end

  def before_save
    self.weightage = 0 if self.weightage.nil?
    #update_exam_group_date
  end

  def after_create
    create_exam_event
  end

  def after_update
    update_exam_event
  end

  def score_for(student_id)
    exam_score = self.exam_scores.find(:first, :conditions => { :student_id => student_id })
    exam_score.nil? ? ExamScore.new : exam_score
  end

  def class_average_marks
    results = ExamScore.find_all_by_exam_id(self)
    scores = results.collect { |x| (x.marks/x.exam.maximum_marks)*100 unless x.marks.nil? or x.exam.maximum_marks.nil?}
    scores.delete(nil)
    return (scores.sum / scores.size) unless scores.size == 0
    return 0
  end
  
  def full_name
    "#{name}"
  end
  
  def modified_full_name
    #group = self.exam_group
    
    #if group.is_current==1
      "#{name} (Current)"
    #end
  end
  
  def fa_groups
    subject.fa_groups.select{|fg| fg.cce_exam_category_id == exam_group.cce_exam_category_id}
  end

  private
  def update_exam_group_date
    group = self.exam_group
    if self.no_date?
      if group.exam_date.blank?
        group.update_attribute(:exam_date,"1979-01-01")
      end 
    elsif group.exam_date_edited==0 and self.start_time.to_date.to_s!="1979-01-01"
      group.update_attribute(:exam_date, self.start_time.to_date)
      group.update_attribute(:exam_date_edited, 1)
      group.update_attribute(:only_comment_base, 0)
    elsif self.start_time.to_date.to_s!="1979-01-01"
      group.update_attribute(:exam_date, self.start_time.to_date) if !group.exam_date.nil? and self.start_time.to_date < group.exam_date
    end
  end

  def create_exam_event
    if self.event.blank? and self.no_date.blank?
      new_event = Event.create do |e|
        e.title       = "#{t('exam_text')}"
        e.description = "#{self.exam_group.name} #{t('for')} #{self.subject.batch.full_name} - #{self.subject.name}"
        e.start_date  = self.start_time
        e.end_date    = self.end_time
        e.is_exam     = true
        e.origin      = self
      end
      batch_event = BatchEvent.create do |be|
        be.event_id = new_event.id
        be.batch_id = self.exam_group.batch_id
      end
      #self.event_id = new_event.id
      self.update_attributes(:event_id=>new_event.id)
    end
  end

  def update_exam_event
    self.event.update_attributes(:start_date => self.start_time, :end_date => self.end_time) unless self.event.blank?
  end
end
