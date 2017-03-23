class MarksController < ApplicationController
  include ActionView::Helpers::TextHelper
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time
  
  
  def data
    if current_user.employee
      @subjects = current_user.employee_record.subjects.active
    elsif current_user.admin
      @subjects = Subject.active
    end  
    @subjects.reject! {|s| !s.batch.is_active}
    @exams = []
    @subjects.each do |sub|
      exams = Rails.cache.fetch("subject_exam_#{sub.id}"){
        exams_data = Exam.find_all_by_subject_id(sub.id)
        exams_data
      }
      
      unless exams.blank?
        exams.each do |exam|  
          exam_group = Rails.cache.fetch("exam_group_from_exam_#{exam.id}"){
            exam_group_data = exam.exam_group
            exam_group_data
          }
          exam_group_batch = Rails.cache.fetch("batch_from_exam_group_#{exam_group.id}"){
            exam_group_batch_data = exam_group.batch
            exam_group_batch_data
          }
          @exams.push exam unless exam.nil? or exam_group.result_published == true
          
        end
      end
    end 
   
    @exams.sort! { |a, b|  b.id <=> a.id }
    k = 0
    data = []
    @exams.each do |exam|
      @exam_group = Rails.cache.fetch("exam_group_from_exam_#{exam.id}"){
        exam_group_data = exam.exam_group
        exam_group_data
      } 
      exam_group_batch = Rails.cache.fetch("batch_from_exam_group_#{@exam_group.id}"){
        exam_group_batch_data = exam_group.batch
        exam_group_batch_data
      }
      exam_subject = Rails.cache.fetch("subject_from_exam_#{exam.id}"){
        exam_subject_data = exam.subject
        exam_subject_data
      }
      data[k] = []
      
      data[k][0] = @template.link_to exam_group_batch.full_name, [@exam_group, exam], :target => "_blank"
      data[k][1] = @template.link_to @exam_group.name, [@exam_group, exam], :target => "_blank"
      data[k][2] = @template.link_to exam_subject.name, [@exam_group, exam], :target => "_blank"
      if exam.subject.no_exams.blank? and exam.no_date.to_i == 0
        data[k][3] = I18n.l(exam.start_time,:format=>"%d %b,%Y %I:%M %p")
        data[k][4] = I18n.l(exam.end_time,:format=>"%d %b,%y %I:%M %p")
      else
        data[k][3] = "N/A"
        data[k][4] = "N/A"
      end
      k = k+1
    end
    json_data = {:data => data}
    @data = JSON.generate(json_data)
    render :text => @data
  end
  
  def data_connect_exam
    @employee_subjects = current_user.employee_record.subjects.active
    
    @today = @local_tzone_time.to_date
    school_id = MultiSchool.current_school.id
    @exam_connect = Rails.cache.fetch("connect_exam_all_#{school_id}"){
      exams_data = ExamConnect.find(:all)
      exams_data
    }
    k = 0
    data = []
    @exam_connect.each do |exam_connect|
      exam_connect_batch = Rails.cache.fetch("batch_from_exam_connect_#{exam_connect.id}"){
        exam_connect_batch_data = exam_connect.batch
        exam_connect_batch_data
      }
      @subjects = []
      @group_exams = Rails.cache.fetch("group_exam_from_exam_connect_#{exam_connect.id}"){
        exam_connect_group_exams_data = GroupedExam.find_all_by_connect_exam_id(exam_connect.id)
        exam_connect_group_exams_data
      }
      @group_exams.each do |group_exam|
        exams = Rails.cache.fetch("exam_from_exam_group_#{group_exam.exam_group_id}"){
          exam_data = Exam.find_all_by_exam_group_id(group_exam.exam_group_id)
          exam_data
        }
        exams.each do |exam|
          exam_subject = Rails.cache.fetch("subject_from_exam_#{exam.id}"){
            exam_subject_data = exam.subject
            exam_subject_data
          }
          if !@subjects.include?(exam_subject) 
            if @employee_subjects.include?(exam_subject.id) or @current_user.admin?
              @subjects << exam_subject
              data[k] = []
              data[k][0] = @template.link_to(exam_connect_batch.full_name, '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam_subject.id.to_s, :target => "_blank")
              data[k][1] = @template.link_to(exam_connect.name, '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam_subject.id.to_s, :target => "_blank")
              data[k][2] = @template.link_to(exam_subject.name, '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam_subject.id.to_s, :target => "_blank")
              k = k+1
            end
          end    
        end       
      end          
        
    end
    json_data = {:data => data}
    @data = JSON.generate(json_data)
    render :text => @data
  end
  
  def index
    if current_user.employee
      @batches = @employee_obj.batches
    elsif current_user.admin
      @batches = Batch.active
    end  
    
  end
  def connect_exam
    if current_user.employee
      @batches = @employee_obj.batches
    elsif current_user.admin
      @batches = Batch.active
    end 
  end
 
end
