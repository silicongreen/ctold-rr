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

# Configure your SMS API settings
require 'net/http'
require 'yaml'
require 'translator'

class DelayedBatchTranferNew
  attr_accessor :students,:to,:session,:local_tzone_time,:current_user,:rolls

  def initialize(students, to, session, local_tzone_time,current_user,rolls)
    @students = students.split(",")
    @rolls = rolls.split(",")
    @to = to
    @session = session
    @local_tzone_time = local_tzone_time
    @current_user = current_user
  end

  def perform
   
    @connect_exam_done = []
    if !@students.blank?
      @to_batch = Batch.find(@to)
      now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
      done_batches = []
      exam_groups = {}
      connect_exam = {}
      batches = {}
      batch_transfer_ids = {}
      std_string = @students.join(",")
      students = Student.find_all_by_id(@students,:order=>"FIELD(id,"+std_string+")")
      reminder_recipient_ids = []
      iloop = 0
      students.each do |student|
        unless done_batches.include?(student.batch_id)
          done_batches << student.batch_id
          batches[student.batch_id] = @batch = Batch.find_by_id(student.batch_id)
          @connect_exam = connect_exam[@batch.id] = ExamConnect.active.find_all_by_batch_id(@batch.id) 
          exam_groups[@batch.id] = ExamGroup.active.find_all_by_batch_id(@batch.id)
          batchboj = BatchTransfer.new
          batchboj.from_id = @batch.id
          batchboj.to_id = @to
          batchboj.session = @session
          batchboj.created_at = now
          batchboj.updated_at = now
          batchboj.save()
          batch_transfer_ids[@batch.id] = batchboj.id
          
          @new_course = Course.find @to_batch.course_id
          @new_course.session = @session
          @new_course.save
          unless connect_exam[@batch.id].blank?
            @connect_exam.each do |ec|
              unless @connect_exam_done.include?(ec.id)
                @connect_exam_done << ec.id
                prev_exam = PreviousExam.find_all_by_connect_exam_id(ec.id)
                unless prev_exam.blank?
                  prev_exam.each do |pe|
                    p_exam = PreviousExam.find(pe.id)
                    p_exam.is_finished = 1
                    p_exam.save
                  end
                end
              end     
            end
          end
          
        end
     
        @batch = batches[student.batch_id]
        batch_student = student.batch_students.find_or_create_by_batch_id_and_session(student.batch_id,@session)
        unless exam_groups[@batch.id].blank?
          exam_groups[@batch.id].each do |eg|
            create_group_exam_student(@batch,student,eg,now,batch_student.id)
          end
        end
        unless connect_exam[@batch.id].blank?
          connect_exam[@batch.id].each do |ec|
            create_combined_exam_student(@batch,student,ec,now,batch_student.id) 
          end
        end
        
        @student_electives = StudentsSubject.all(:conditions=>{:student_id=>student.id,:batch_id=>@batch.id,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
        unless @student_electives.blank?
          @student_electives.each do |e_subject|
            subject_new = Subject.find_by_code_and_batch_id(e_subject.subject.code,@to,:conditions=>{:is_deleted=>false})
            unless subject_new.blank?
                subobj = StudentsSubject.new
                subobj.batch_id = @to
                subobj.student_id = student.id
                subobj.subject_id = subject_new.id
                subobj.elective_type = e_subject.elective_type
                subobj.save
            end
          end
        end
        finace_transfer(@to,student)
        student.update_attribute(:batch_transfer_done, 1)
        student.update_attribute(:batch_id, @to)
        student.update_attribute(:session, @session)
        student.update_attribute(:has_paid_fees,0)
        student.update_attribute(:is_promoted,1)
        student.update_attribute(:class_roll_no,@rolls[iloop])

        reminder_recipient_ids << student.user_id            
        unless student.immediate_contact.nil? 
          reminder_recipient_ids << student.immediate_contact.user_id
        end
        
        iloop = iloop+1
      end
      unless reminder_recipient_ids.empty?
        Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => @current_user.id,
            :recipient_ids => reminder_recipient_ids,
            :subject=>"Promoted",
            :rtype=>25,
            :rid=>0,
            :body=>"Congratulation. You have been promoted to new Class. Wish you all the best." ))
      end
      
      
      
    end
    
  end
  
  private
  def finace_transfer(to,student)
    
  end
  def save_batch_transfer(from,to,session,now)
    batchboj = BatchTransfer.new
    batchboj.from_id = from
    batchboj.to_id = to
    batchboj.session = session
    batchboj.created_at = now
    batchboj.updated_at = now
    batchboj.save()
    @batch_tranfer_id = batchboj.id
  end
  
  def create_group_exam_student(batch,s,eg,now,batch_student_id)
    examgroup = GroupExamStudent.new
    examgroup.batch_id = batch.id
    examgroup.student_id = s.id
    examgroup.class_roll_no = s.class_roll_no
    examgroup.exam_group_id = eg.id
    examgroup.batch_student_id = batch_student_id
    examgroup.year = eg.exam_date
    examgroup.created_at = now
    examgroup.updated_at = now
    examgroup.save
  end
  
  def create_combined_exam_student(batch,s,ec,now,batch_student_id)
    examconnect = ExamConnectStudent.new
    examconnect.batch_id = batch.id
    examconnect.student_id = s.id
    examconnect.class_roll_no = s.class_roll_no
    examconnect.exam_connect_id = ec.id
    examconnect.batch_student_id = batch_student_id
    examconnect.year = ec.published_date
    examconnect.created_at = now
    examconnect.updated_at = now
    examconnect.save
  end
  
end