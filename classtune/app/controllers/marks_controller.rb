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
      exams =  Exam.find_all_by_subject_id(sub.id)
      
      unless exams.blank?
        exams.each do |exam|  
          exam_group =  exam.exam_group
          exam_group_batch = exam_group.batch
          @exams.push exam unless exam.nil? or exam_group.result_published == true
          
        end
      end
    end 
   
    @exams.sort! { |a, b|  b.id <=> a.id }
    k = 0
    data = []
    @exams.each do |exam|
      @exam_group = exam.exam_group
      exam_group_batch = @exam_group.batch
      exam_subject = exam.subject
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
    @exam_connect =ExamConnect.find(:all)
    k = 0
    data = []
    @exam_connect.each do |exam_connect|
      exam_connect_batch =  exam_connect.batch
      @subjects = []
      @group_exams = GroupedExam.find_all_by_connect_exam_id(exam_connect.id)
      @group_exams.each do |group_exam|
        exams = Exam.find_all_by_exam_group_id(group_exam.exam_group_id)
          
        exams.each do |exam|
          exam_subject = exam.subject
          if !@subjects.include?(exam_subject) 
            if @employee_subjects.include?(exam_subject) or @current_user.admin?
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
  
  def data_connect_exam_report
    @employee_subjects = current_user.employee_record.subjects.active
    
    @today = @local_tzone_time.to_date
    school_id = MultiSchool.current_school.id
    @exam_connect = ExamConnect.find(:all)
    k = 0
    data = []
    @exam_connect.each do |exam_connect|
      exam_connect_batch = exam_connect.batch
      @subjects = []
      @group_exams = GroupedExam.find_all_by_connect_exam_id(exam_connect.id)
      @group_exams.each do |group_exam|
        exams = Exam.find_all_by_exam_group_id(group_exam.exam_group_id)
        exams.each do |exam|
          exam_subject = exam.subject
          if !@subjects.include?(exam_subject) 
            if @employee_subjects.include?(exam_subject.id) or @current_user.admin?
              @subjects << exam_subject
              data[k] = []
              data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s+" (All Result)", '/exam/' + 'continues/' +exam_connect.id.to_s, :target => "_blank")
              data[k][1] = @template.link_to(exam_connect.name.to_s+" (Tablulation)", '/exam/' + 'tabulation/' +exam_connect.id.to_s, :target => "_blank")
              data[k][2] = @template.link_to(exam_subject.name.to_s+" (Marksheet)", '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
              data[k][3] = @template.link_to("Comment Entry", '/exam/' + 'comment_tabulation/' +exam_connect.id.to_s, :target => "_blank")
              data[k][4] = @template.link_to("Results", '/exam/' + 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
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
      employee= current_user.employee_record
      @employee_obj = Employee.find_by_id(employee.id)
      @batches = @employee_obj.batches
    elsif current_user.admin
      @batches = Batch.active
    end  
    
  end
  def connect_exam
    @exams_data = ExamConnect.find(:all,:group=>"name")
    if current_user.employee
      employee= current_user.employee_record
      @employee_obj = Employee.find_by_id(employee.id)
      @batches = @employee_obj.batches
    elsif current_user.admin
      @batches = Batch.active
    end 
  end
  def examgroup
    if params[:batch_name] == "0"
      @exams_data = ExamConnect.find(:all,:group=>"name")
    else
      @batches = Batch.active
      @batch_id = 0
      @batches.each do |batch|
        if batch.full_name == params[:batch_name]
          @batch_id = batch.id
          break
        end
      end
      if @batch_id.blank?
        @exams_data = ExamConnect.find(:all,:group=>"name")
      else
        @exams_data = ExamConnect.find_all_by_batch_id(@batch_id)
      end  
    end  
    render :layout => false
  end
  
  def connect_exam_report
    @exams_data = ExamConnect.find(:all,:group=>"name")
    if current_user.employee
      employee= current_user.employee_record
      @employee_obj = Employee.find_by_id(employee.id)
      @batches = @employee_obj.batches
    elsif current_user.admin
      @batches = Batch.active
    end 
  end
 
end
