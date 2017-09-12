class MarksController < ApplicationController
  include ActionView::Helpers::TextHelper
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time
  
  
  def get_class
    require 'json'
    batch_name = params[:batch]  
    if current_user.employee
      batches = @current_user.employee_record.batches
      batches += @current_user.employee_record.subjects.collect{|b| b.batch}
      batches = batches.uniq unless batches.empty?
      batches.reject! {|s| s.name!=batch_name}
    else
       batches = Batch.find_all_by_name_and_is_deleted(batch_name,false)
    end  
    @class_list = []
    @class_names = []
    k = 0
    unless batches.blank?
      batches.each do |batch|
        unless batch.course.blank?
          if !@class_names.include?(batch.course.course_name)
            @class_list[k] = []
            @class_list[k][0] = batch.id
            @class_list[k][1] = batch.name
            @class_list[k][2] = batch.course.course_name
            @class_names << batch.course.course_name
            k =k+1
          end
        end
      end
    end
    
    json_data = {:data => @class_list}
    @data = JSON.generate(json_data)
    render :text => @data
  end
  
  def get_exam_subject
    require 'json'
    
    if current_user.employee
      @emp_subjects = current_user.employee_record.subjects.active
      @batches= current_user.employee_record.batches
      unless @batches.blank?
        @batches.each do |batch|
          @emp_subjects += batch.subjects
        end
      end
    
    @emp_subjects = @emp_subjects.uniq unless @batches.empty?
    
    elsif current_user.admin
      @emp_subjects = Subject.active
    end
    
    exam_id = params[:exam_id]
    exam_connect = ExamConnect.find(exam_id)
    
    @group_exams = GroupedExam.find_all_by_connect_exam_id(exam_connect.id)
    k = 0
    data = []
    @subjects = []
    @group_exams.each do |group_exam|
      exams = Exam.find_all_by_exam_group_id(group_exam.exam_group_id)
      exams.each do |exam|
        exam_subject = exam.subject
        if !exam_subject.blank? and !@subjects.include?(exam_subject) and @emp_subjects.include?(exam_subject) 
          @subjects << exam_subject  
          data[k] = @template.link_to(exam_subject.name.to_s, '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s)
          k = k+1
        end    
      end

    end
    json_data = {:data => data}
    @data = JSON.generate(json_data)
    render :text => @data 
    
  end
  
  def get_exams
    require 'json'
    batch_id = params[:batch_id]
    data_type = params[:data_type]
    if data_type.to_i == 1
      @exam_connect = ExamConnect.active.find(:all,:conditions=>"batch_id="+batch_id+" and (result_type=3 or result_type=4) and is_deleted=0")
    elsif data_type.to_i == 2
      @exam_connect = ExamConnect.active.find(:all,:conditions=>"batch_id="+batch_id+" and (result_type=1 or result_type=2) and is_deleted=0")
    else
      @exam_connect = ExamConnect.active.find(:all,:conditions=>"batch_id="+batch_id+" and is_deleted=0")
    end 
    
    k = 0
    data = []
    unless @exam_connect.blank?
      @exam_connect.each do |exam_connect|
        if data_type.to_i == 1 or data_type.to_i == 2
          data[k] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'tabulation/' +exam_connect.id.to_s, :id=>"exams_id_"+exam_connect.id.to_s)
        elsif data_type.to_i == 3
          data[k] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'continues/' +exam_connect.id.to_s, :id=>"exams_id_"+exam_connect.id.to_s)
        elsif data_type.to_i == 4
          data[k] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'comment_tabulation_pdf/' +exam_connect.id.to_s, :id=>"exams_id_"+exam_connect.id.to_s)
        else
          data[k] = "<a href='javascript:void(0);' id='exams_id_"+exam_connect.id.to_s+"' onclick='get_exam_subject("+exam_connect.id.to_s+")' >"+exam_connect.name.to_s+"</a>"
        end  
        k = k+1
      end
    end
    
    json_data = {:data => data}
    @data = JSON.generate(json_data)
    render :text => @data 
  end
  
  def get_section
    require 'json'
    batch_name = params[:batch]
    course_name = params[:course_name]
    if current_user.employee
      batches = @current_user.employee_record.batches
      batches += @current_user.employee_record.subjects.collect{|b| b.batch}
      batches = batches.uniq unless batches.empty?
      batches.reject! {|s| s.name!=batch_name}
    else
      batches = Batch.find_all_by_name_and_is_deleted(batch_name,false)
    end 
#    batches = Batch.find_all_by_name(batch_name)
    @batch_list = []
    k = 0
    unless batches.blank?
      batches.each do |batch|
        unless batch.course.blank?
          if batch.course.course_name == course_name
            @batch_list[k] = []
            @batch_list[k][0] = batch.id
            @batch_list[k][1] = batch.name
            @batch_list[k][2] = batch.course.course_name
            @batch_list[k][3] = batch.course.section_name
            k =k+1
          end
        end
      end
    end
    json_data = {:data => @batch_list}
    @data = JSON.generate(json_data)
    render :text => @data
    
  end
  
  def data
    if current_user.employee
      @subjects = current_user.employee_record.subjects.active
      @batches= current_user.employee_record.batches
      unless @batches.blank?
        @batches.each do |batch|
          @subjects += batch.subjects
        end
      end
    
    @subjects = @subjects.uniq unless @batches.empty?
    
    elsif current_user.admin
      @subjects = Subject.active
    end  
    @subjects.reject! {|s| !s.batch.is_active}
    @exams = []
    all_sub_id = @subjects.map(&:id)
    all_exams =  Exam.find_all_by_subject_id(all_sub_id,:include=>[{:exam_group=>[:batch]},:subject])
    all_exams.each do |exam|
        @exams.push exam unless exam.nil?
    end 
   
    @exams.sort! { |a, b|  b.id <=> a.id }
    k = 0
    data = []
    @exams.each do |exam|
      @exam_group = exam.exam_group
      unless @exam_group.blank?
        exam_group_batch = @exam_group.batch
        exam_subject = exam.subject
        unless exam_subject.blank? or @exam_group.result_published == true or @exam_group.is_deleted == true
          data[k] = []

          data[k][0] = @template.link_to exam_group_batch.full_name, [@exam_group, exam], :target => "_blank"
          data[k][1] = @template.link_to @exam_group.name, [@exam_group, exam], :target => "_blank"
          data[k][2] = @template.link_to exam_subject.name, [@exam_group, exam], :target => "_blank"

          k = k+1
        end
      end
    end
    json_data = {:data => data}
    @data = JSON.generate(json_data)
    render :text => @data
  end
  
  def data_connect_exam
#    @employee_subjects = current_user.employee_record.subjects.active
    
    @employee_subjects = current_user.employee_record.subjects.active
    @batches= current_user.employee_record.batches
    unless @batches.blank?
      @batches.each do |batch|
        @employee_subjects += batch.subjects
      end
    end
    @employee_subjects = @employee_subjects.uniq unless @batches.empty?
    
    @today = @local_tzone_time.to_date
    school_id = MultiSchool.current_school.id
    @exam_connect =ExamConnect.active.find(:all,:select => "id,name,batch_id",:include=>[:batch],:conditions =>["school_id = ?",MultiSchool.current_school.id])
    k = 0
    data = []
    @exam_connect.each do |exam_connect|
      exam_connect_batch =  exam_connect.batch
      @subjects = []
      @group_exams = GroupedExam.find_all_by_connect_exam_id(exam_connect.id)
      @exam_group_ids = @group_exams.map(&:exam_group_id)
      exams = Exam.find_all_by_exam_group_id(@exam_group_ids,:select => "id,subject_id",:include=>[:subject])
      unless exams.blank?   
        exams.each do |exam|
          exam_subject = exam.subject
          if !exam_subject.blank? and !@subjects.include?(exam_subject) 
            if @employee_subjects.include?(exam_subject) or @current_user.admin?
              @subjects << exam_subject
              data[k] = []
              data[k][0] = @template.link_to(exam_connect_batch.full_name, '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam_subject.id.to_s, :target => "_blank")
              if @current_user.admin? or (!@batches.blank? and @batches.include?(exam_connect_batch))
                data[k][1] = @template.link_to(exam_connect.name+"(Comment Entry)", '/exam/' + 'comment_tabulation/' +exam_connect.id.to_s+'?blank_page=1', :target => "_blank")
              else
                data[k][1] = @template.link_to(exam_connect.name, '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam_subject.id.to_s, :target => "_blank")
              end  
              data[k][2] = @template.link_to(exam_subject.name, '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam_subject.id.to_s, :target => "_blank")
              data[k][3] = @template.link_to("Marksheet", '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
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
    @batches= current_user.employee_record.batches
    unless @batches.blank?
      @batches.each do |batch|
        @employee_subjects += batch.subjects
      end
    end
    
    @employee_subjects = @employee_subjects.uniq unless @batches.empty?
    
    @today = @local_tzone_time.to_date
    school_id = MultiSchool.current_school.id
    @exam_connect = ExamConnect.active.find(:all,:conditions =>["school_id = ?",MultiSchool.current_school.id])
    k = 0
    data = []
    @exam_connect.each do |exam_connect|
      exam_connect_batch = exam_connect.batch
      unless exam_connect_batch.blank?
        @subjects = []
        @group_exams = GroupedExam.find_all_by_connect_exam_id(exam_connect.id)
        @group_exams.each do |group_exam|
          exams = Exam.find_all_by_exam_group_id(group_exam.exam_group_id)
          exams.each do |exam|
            exam_subject = exam.subject
            if !exam_subject.blank? and !@subjects.include?(exam_subject) 
              if @employee_subjects.include?(exam_subject) or @current_user.admin?
                @subjects << exam_subject
                data[k] = []
                if school_id == 340
                  #Sir John Wilson School
                  if exam_connect.result_type == 1
                    data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s, '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][1] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][2] = @template.link_to(exam_subject.name.to_s, '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][3] = @template.link_to("Pupil Progress Report", '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][4] = @template.link_to("Results", '/exam/' + 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                  elsif exam_connect.result_type == 2
                    data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s, '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][1] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][2] = @template.link_to(exam_subject.name.to_s, '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][3] = @template.link_to("Pupil Progress Report", '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][4] = @template.link_to("Results", '/exam/' + 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                  elsif exam_connect.result_type == 3
                    data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s+" (All Result)", 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][1] = @template.link_to(exam_connect.name.to_s+" (Tablulation)", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][2] = @template.link_to(exam_subject.name.to_s+" (Marksheet)", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][3] = @template.link_to("Effort/Grade Sheet", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][4] = @template.link_to("Results", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                  elsif exam_connect.result_type == 4
                    data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s+" (All Result)", 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][1] = @template.link_to(exam_connect.name.to_s+" (Tablulation)", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][2] = @template.link_to(exam_subject.name.to_s+" (Marksheet)", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][3] = @template.link_to("Effort/Grade Sheet", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][4] = @template.link_to("Results", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                  elsif exam_connect.result_type == 7
                    data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s, '/exam/' + 'score_sheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][1] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'score_sheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][2] = @template.link_to(exam_subject.name.to_s, '/exam/' + 'score_sheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][3] = @template.link_to("Score Sheet", '/exam/' + 'score_sheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][4] = @template.link_to("Results", '/exam/' + 'score_sheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                  elsif exam_connect.result_type == 5
                    data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s+" (All Result)", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][1] = @template.link_to(exam_connect.name.to_s+" (Tablulation)", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][2] = @template.link_to(exam_subject.name.to_s+" (Marksheet)", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][3] = @template.link_to("Effort/Grade Sheet", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][4] = @template.link_to("Results", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                  elsif exam_connect.result_type == 6
                    data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s+" (All Result)", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][1] = @template.link_to(exam_connect.name.to_s+" (Tablulation)", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][2] = @template.link_to(exam_subject.name.to_s+" (Marksheet)", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][3] = @template.link_to("Effort/Grade Sheet", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][4] = @template.link_to("Results", '/exam/' + 'effot_gradesheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                  elsif exam_connect.result_type == 9
                    data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][1] = @template.link_to(exam_connect.name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][2] = @template.link_to(exam_subject.name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][3] = @template.link_to("Report Card", '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][4] = @template.link_to("Results", '/exam/' + 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                  elsif exam_connect.result_type == 10
                    data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][1] = @template.link_to(exam_connect.name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][2] = @template.link_to(exam_subject.name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][3] = @template.link_to("Report Card", '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][4] = @template.link_to("Results", '/exam/' + 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                  elsif exam_connect.result_type == 11
                    data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][1] = @template.link_to(exam_connect.name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][2] = @template.link_to(exam_subject.name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][3] = @template.link_to("Report Card", '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][4] = @template.link_to("Results", '/exam/' + 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                  elsif exam_connect.result_type == 12
                    data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s, '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][1] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][2] = @template.link_to(exam_subject.name.to_s, '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][3] = @template.link_to("Evaluation Sheet", '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                    data[k][4] = @template.link_to("Results", '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                  elsif exam_connect.result_type == 13
                    data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][1] = @template.link_to(exam_connect.name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][2] = @template.link_to(exam_subject.name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][3] = @template.link_to("Report Card", '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][4] = @template.link_to("Results", '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                  elsif exam_connect.result_type == 14
                    data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][1] = @template.link_to(exam_connect.name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][2] = @template.link_to(exam_subject.name.to_s, '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][3] = @template.link_to("Report Card", '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                    data[k][4] = @template.link_to("Results", '/exam/' 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")
                  end
                  
                else
                  data[k][0] = @template.link_to(exam_connect_batch.full_name.to_s+" (All Result)", '/exam/' + 'continues/' +exam_connect.id.to_s, :target => "_blank")
                  data[k][1] = @template.link_to(exam_connect.name.to_s+" (Tablulation)", '/exam/' + 'tabulation/' +exam_connect.id.to_s, :target => "_blank")
                  data[k][2] = @template.link_to(exam_subject.name.to_s+" (Marksheet)", '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s, :target => "_blank")
                  data[k][3] = @template.link_to("Comment Entry", '/exam/' + 'comment_tabulation_pdf/' +exam_connect.id.to_s, :target => "_blank")
                  data[k][4] = @template.link_to("Results", '/exam/' + 'generated_report5?connect_exam='+exam_connect.id.to_s+"&batch_id="+exam_connect_batch.id.to_s, :target => "_blank")

                end
                k = k+1
              end
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
      @batches = @current_user.employee_record.batches
      @batches += @current_user.employee_record.subjects.collect{|b| b.batch}
      @batches = @batches.uniq unless @batches.empty?
    elsif current_user.admin
      @batches = Batch.active
    end  
    
  end
  def connect_exam
    @exams_data = ExamConnect.active.find(:all,:group=>"name",:conditions =>["school_id = ?",MultiSchool.current_school.id])
    if current_user.employee
      @batches = @current_user.employee_record.batches
      @batches += @current_user.employee_record.subjects.collect{|b| b.batch}
      @batches = @batches.uniq unless @batches.empty?
      
    elsif current_user.admin
      @batches = Batch.active
    end 
  end
  def examgroup
    if params[:batch_name] == "0"
      @exams_data = ExamConnect.active.find(:all,:group=>"name",:conditions =>["school_id = ?",MultiSchool.current_school.id])
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
        @exams_data = ExamConnect.active.find(:all,:group=>"name",:conditions =>["school_id = ?",MultiSchool.current_school.id])
      else
        @exams_data = ExamConnect.active.find_all_by_batch_id(@batch_id)
      end  
    end  
    render :layout => false
  end
  
  def connect_exam_report
    @exams_data = ExamConnect.active.find(:all,:group=>"name",:conditions =>["school_id = ?",MultiSchool.current_school.id])
    if current_user.employee
      @batches = @current_user.employee_record.batches
      @batches += @current_user.employee_record.subjects.collect{|b| b.batch}
      @batches = @batches.uniq unless @batches.empty?
    elsif current_user.admin
      @batches = Batch.active
    end 
  end
 
end
