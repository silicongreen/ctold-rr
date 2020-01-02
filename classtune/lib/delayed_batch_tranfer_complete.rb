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

class DelayedBatchTranferComplete
  attr_accessor :local_tzone_time

  def initialize(local_tzone_time,current_user)
    @local_tzone_time = local_tzone_time
  end

  def perform
   
      @all_grouped_exam = ExamGroup.active.find(:all)
      @all_connect_exam = ExamConnect.active.find(:all)
      students = Student.find(:all,:conditions=>["batch_transfer_done = ?", false]);
      now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
      done_batches = []
      exam_groups = {}
      connect_exam = {}
      batches = {}
      students.each do |student|
        unless done_batches.include(student.batch_id)
          done_batches << student.batch_id
          batches[student.batch_id] = @batch = Batch.find_by_id(student.batch_id)
          @connect_exam = connect_exam[@batch.id] = ExamConnect.active.find_all_by_batch_id(@batch.id) 
          exam_groups[@batch.id] = ExamGroup.active.find_all_by_batch_id(@batch.id)
          batchboj = BatchTransfer.new
          batchboj.from_id = @batch.id
          batchboj.to_id = @batch.id
          batchboj.session = @batch.course.session
          batchboj.created_at = now
          batchboj.updated_at = now
          batchboj.save()
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
      end
      
      unless @all_grouped_exam.blank?
          @all_grouped_exam.each do |eg|
            eg.update_attribute(:is_deleted,true)
          end
      end

      unless @all_connect_exam.blank?
        @all_connect_exam.each do |ec|
          ec.update_attribute(:is_deleted,true)
        end
      end 
      
      Student.update_all("batch_transfer_done='0'",  ["batch_transfer_done = ? and school_id = ?",1,MultiSchool.current_school.id])
    
  end
  
  
end