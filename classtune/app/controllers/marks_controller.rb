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
  
  def get_exam_subject_participation
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
        if !exam_subject.blank? and !@subjects.include?(exam_subject) and @emp_subjects.include?(exam_subject) and exam_subject.no_exams!=true 
          @subjects << exam_subject  
          data[k] = @template.link_to(exam_subject.name.to_s, '/exam/' + 'class_performance_student/' +exam_connect.id.to_s+"?subject_id="+exam_subject.id.to_s)
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
      @exam_connect = ExamConnect.active.find(:all,:conditions=>"batch_id="+batch_id+" and (result_type=1 or result_type=2 or result_type=5  or result_type=6) and is_deleted=0")
    else
      @exam_connect = ExamConnect.active.find(:all,:conditions=>"batch_id="+batch_id+" and is_deleted=0")
    end 
    
    k = 0
    data = []
    unless @exam_connect.blank?
      @exam_connect.each do |exam_connect|
        if data_type.to_i == 1 or data_type.to_i == 2
          data[k] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'tabulation/' +exam_connect.id.to_s, :id=>"exams_id_"+exam_connect.id.to_s)
          k = k+1
        elsif data_type.to_i == 3
          data[k] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'continues/' +exam_connect.id.to_s, :id=>"exams_id_"+exam_connect.id.to_s)
          k = k+1
        elsif data_type.to_i == 4
          data[k] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'comment_tabulation_pdf/' +exam_connect.id.to_s, :id=>"exams_id_"+exam_connect.id.to_s)
          k = k+1
        elsif data_type.to_i == 5
          data[k] = "<a href='javascript:void(0);' id='exams_id_"+exam_connect.id.to_s+"' onclick='get_exam_subject("+exam_connect.id.to_s+")' >"+exam_connect.name.to_s+"</a>"
          k = k+1
        elsif data_type.to_i == 6
          if exam_connect.result_type == 1 or exam_connect.result_type == 2
            data[k] = "<a href='javascript:void(0);' id='exams_id_"+exam_connect.id.to_s+"' onclick='get_exam_subject_participation("+exam_connect.id.to_s+")' >"+exam_connect.name.to_s+"</a>"
            k = k+1
          end
          
        else
          if exam_connect.result_type == 1 or exam_connect.result_type == 2
            data[k] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'class_performance_student/' +exam_connect.id.to_s, :id=>"exams_id_"+exam_connect.id.to_s)
            k = k+1
          end
          
        end  
        
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
    @subjects.reject! {|s| !s.batch or !s.batch.is_active}
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
        unless exam_subject.blank?  or exam_group_batch.blank? or @exam_group.is_deleted == true or (@exam_group.result_published == true and MultiSchool.current_school.id != 323)
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
    @employee_subjects = []
    @batches2 = []
    unless @current_user.admin?
      @employee_subjects = current_user.employee_record.subjects.active
      @batches= current_user.employee_record.batches
      unless @batches.blank?
        @batches.each do |batch|
          @employee_subjects += batch.subjects
        end
      end
      @batches2 = @batches.map(&:id)
      @employee_subjects = @employee_subjects.uniq unless @batches.empty?
      @employee_subjects = @employee_subjects.map(&:id)
    end
    
    @today = @local_tzone_time.to_date
    school_id = MultiSchool.current_school.id
    @exam_connect =ExamConnect.active.find(:all,:select => "exam_connects.id,exam_connects.result_type,exam_connects.name,batches.name as batch_name,batches.is_deleted,courses.course_name,courses.section_name,exam_connects.batch_id",:joins=>[{:batch=>[:course]}],:conditions =>["exam_connects.school_id = ?",MultiSchool.current_school.id])
    k = 0
    data = []
    @exam_connect.each do |exam_connect|
      exam_connect_batch = exam_connect.batch_name+" "+exam_connect.course_name+" "+exam_connect.section_name
      @subjects = []
      @group_exams = GroupedExam.find_all_by_connect_exam_id(exam_connect.id,:select => "grouped_exams.exam_group_id")
      @exam_group_ids = @group_exams.map(&:exam_group_id)
      exams = Exam.find_all_by_exam_group_id(@exam_group_ids,:select => "exams.id,exams.subject_id,subjects.name as subject_name",:joins=>[:subject])
      unless exams.blank?   
        exams.each do |exam|
          if !@subjects.include?(exam.subject_id) 
            if @employee_subjects.include?(exam.subject_id) or @current_user.admin?
              @subjects << exam.subject_id
              data[k] = []
              data[k][0] = @template.link_to(exam_connect_batch.to_s, '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam.subject_id.to_s, :target => "_blank")
              if @current_user.admin? or (!@batches2.blank? and @batches2.include?(exam_connect.batch_id))
                data[k][1] = @template.link_to(exam_connect.name+"(Comment Entry)", '/exam/' + 'comment_tabulation/' +exam_connect.id.to_s+'?blank_page=1', :target => "_blank")
              else
                data[k][1] = @template.link_to(exam_connect.name, '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam.subject_id.to_s, :target => "_blank")
              end  
              data[k][2] = @template.link_to(exam.subject_name, '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam.subject_id.to_s, :target => "_blank")
              if MultiSchool.current_school.id != 340
               data[k][3] = @template.link_to("Marksheet", '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam.subject_id.to_s, :target => "_blank")
              end 
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
   @employee_subjects = []
   unless @current_user.admin?
      @employee_subjects = current_user.employee_record.subjects.active
      @batches= current_user.employee_record.batches
      unless @batches.blank?
        @batches.each do |batch|
          @employee_subjects += batch.subjects
        end
      end
      @employee_subjects = @employee_subjects.uniq unless @batches.empty?
      @employee_subjects = @employee_subjects.map(&:id)
    end
    @today = @local_tzone_time.to_date
    school_id = MultiSchool.current_school.id
    @exam_connect =ExamConnect.active.find(:all,:select => "exam_connects.id,exam_connects.result_type,exam_connects.name,batches.name as batch_name,batches.is_deleted,courses.course_name,courses.section_name,exam_connects.batch_id",:joins=>[{:batch=>[:course]}],:conditions =>["exam_connects.school_id = ?",MultiSchool.current_school.id])
    k = 0
    data = []
    @exam_connect.each do |exam_connect|
     
      exam_connect_batch = exam_connect.batch_name+" "+exam_connect.course_name+" "+exam_connect.section_name
      if exam_connect.is_deleted == 0
        @subjects = []
        @group_exams = GroupedExam.find_all_by_connect_exam_id(exam_connect.id,:select => "grouped_exams.exam_group_id")
         
        @exam_group_ids = @group_exams.map(&:exam_group_id)
        
        exams = Exam.find_all_by_exam_group_id(@exam_group_ids,:select => "exams.id,exams.subject_id,subjects.name as subject_name",:joins=>[:subject])
        exams.each do |exam|
          
          if !@subjects.include?(exam.subject_id) 
            if @employee_subjects.include?(exam.subject_id) or @current_user.admin?
              @subjects << exam.subject_id
              
              data[k] = []
              if school_id == 340
                #Sir John Wilson School
                if exam_connect.result_type == 1
                  data[k][0] = exam_connect_batch.to_s
                  data[k][1] = exam_connect.name.to_s+"("+exam.subject_name.to_s+")" 
                  data[k][2] = "<a href='/exam/effot_gradesheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Effort/Grade Sheet</a>"
                  data[k][3] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Pupil Progress Report</a>"
                  data[k][4] = "<a href='/exam/continues/#{exam_connect.id.to_s}' target='_blank'>REPORT CARD</a>"
                elsif exam_connect.result_type == 2
                  data[k][0] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>#{exam_connect_batch.to_s}</a>"
                  data[k][1] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>#{exam_connect.name.to_s}</a>"
                  data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>#{exam.subject_name.to_s}</a>"
                  data[k][3] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Pupil Progress Report</a>"
                  data[k][4] = "<a href='/exam/continues/#{exam_connect.id.to_s}' target='_blank'>REPORT CARD</a>"
                elsif exam_connect.result_type == 3
                  data[k][0] = "<a href='/exam/continues/#{exam_connect.id.to_s}' target='_blank'>#{exam_connect_batch.to_s} (All Result)</a>"
                  data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
                  data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>#{exam.subject_name.to_s} (Marksheet)</a>"
                  data[k][3] = "-"
                  data[k][4] = "<a href='/exam/effot_gradesheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Effort/Grade Sheet</a>"
                elsif exam_connect.result_type == 4
                  data[k][0] = exam_connect_batch.to_s
                  data[k][1] = exam_connect.name.to_s
                  data[k][2] = exam.subject_name.to_s
                  data[k][3] = "<a href='/exam/effot_gradesheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Effort/Grade Sheet</a>"
                  data[k][4] = "<a href='/exam/continues/#{exam_connect.id.to_s}' target='_blank'>REPORT CARD</a>"
                elsif exam_connect.result_type == 7
                  data[k][0] = exam_connect_batch.to_s
                  data[k][1] = exam_connect.name.to_s
                  data[k][2] = exam.subject_name.to_s
                  data[k][3] = "-"
                  data[k][4] = "<a href='/exam/score_sheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Score Sheet</a>"
                elsif exam_connect.result_type == 5
                  data[k][0] = "<a href='/exam/continues/#{exam_connect.id.to_s}' target='_blank'>#{exam_connect_batch.to_s} (All Result)</a>"
                  data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
                  data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>#{exam.subject_name.to_s} (Marksheet)</a>"
                  data[k][3] = "-"
                  data[k][4] = "<a href='/exam/effot_gradesheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Effort/Grade Sheet</a>"
                elsif exam_connect.result_type == 6
                  data[k][0] = exam_connect_batch.to_s
                  data[k][1] = exam_connect.name.to_s+"("+exam.subject_name.to_s+")" 
                  data[k][2] = exam.subject_name.to_s
                  data[k][3] = "<a href='/exam/effot_gradesheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Effort/Grade Sheet</a>"
                  data[k][4] = "<a href='/exam/continues/#{exam_connect.id.to_s}' target='_blank'>REPORT CARD</a>"
                elsif exam_connect.result_type == 9
                  data[k][0] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>#{exam_connect_batch.to_s}</a>"
                  data[k][1] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>#{exam_connect.name.to_s}</a>"
                  data[k][2] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>#{exam.subject_name.to_s}</a>"
                  data[k][3] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>Report Card</a>"
                  data[k][4] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>Results</a>"
                elsif exam_connect.result_type == 10
                  data[k][0] = exam_connect_batch.to_s
                  data[k][1] = exam_connect.name.to_s
                  data[k][2] = exam.subject_name.to_s
                  data[k][3] = "-"
                  data[k][4] = "<a href='/exam/continues/#{exam_connect.id.to_s}' target='_blank'>REPORT CARD</a>"
                elsif exam_connect.result_type == 11
                  data[k][0] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>#{exam_connect_batch.to_s}</a>"
                  data[k][1] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>#{exam_connect.name.to_s}</a>"
                  data[k][2] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>#{exam.subject_name.to_s}</a>"
                  data[k][3] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>Report Card</a>"
                  data[k][4] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>Results</a>"
                elsif exam_connect.result_type == 12
                  data[k][0] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>#{exam_connect_batch.to_s}</a>"
                  data[k][1] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>#{exam_connect.name.to_s}</a>"
                  data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>#{exam.subject_name.to_s}</a>"
                  data[k][3] = "-"
                  data[k][4] = "<a href='/exam/effot_gradesheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Effort/Grade Sheet</a>"
                elsif exam_connect.result_type == 13
                  data[k][0] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>#{exam_connect_batch.to_s}</a>"
                  data[k][1] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>#{exam_connect.name.to_s}</a>"
                  data[k][2] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>#{exam.subject_name.to_s}</a>"
                  data[k][3] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>Report Card</a>"
                  data[k][4] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>Results</a>"
                elsif exam_connect.result_type == 14
                  data[k][0] = exam_connect_batch.to_s
                  data[k][1] = exam_connect.name.to_s+"("+exam.subject_name.to_s+")" 
                  data[k][2] = "<a href='/exam/effot_gradesheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}&evaluation=1' target='_blank'>EVALUATION REPORT</a>"
                  data[k][3] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Pupil Progress Report</a>"
                  data[k][4] = "<a href='/exam/continues/#{exam_connect.id.to_s}' target='_blank'>REPORT CARD</a>"
                end

              else
                data[k][0] = "<a href='/exam/continues/#{exam_connect.id.to_s}' target='_blank'>#{exam_connect_batch.to_s} (All Result)</a>"
                data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
                data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>#{exam.subject_name.to_s} (Marksheet)</a>"
                data[k][3] = "<a href='/exam/comment_tabulation_pdf/#{exam_connect.id.to_s}' target='_blank'>Comment Entry</a>"
                data[k][4] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}' target='_blank'>Results</a>"

              end
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
