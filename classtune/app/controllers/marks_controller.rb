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
    if data_type.to_i == 8
      @exam_connect = ExamConnect.active.find(:all,:conditions=>"(result_type=1 or result_type=2 or result_type=4) and is_deleted=0 and school_id="+MultiSchool.current_school.id.to_s,:group=>"name")
    elsif data_type.to_i == 1
      @exam_connect = ExamConnect.active.find(:all,:conditions=>"batch_id="+batch_id+" and (result_type=3 or result_type=4 or result_type=9  or result_type=12) and is_deleted=0")
    elsif data_type.to_i == 2
      @exam_connect = ExamConnect.active.find(:all,:conditions=>"batch_id="+batch_id+" and (result_type=1 or result_type=2 or result_type=5  or result_type=6  or result_type=7  or result_type=8  or result_type=10  or result_type=11  or result_type=13  or result_type=14  or result_type=15) and is_deleted=0")
    else
      @exam_connect = ExamConnect.active.find(:all,:conditions=>"batch_id="+batch_id+" and is_deleted=0")
    end 
    
    k = 0
    data = []
    unless @exam_connect.blank?
      @exam_connect.each do |exam_connect|
        if data_type.to_i == 8
          data[k] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'failed_grade/' +exam_connect.id.to_s+"#view=FitH", :id=>"exams_id_"+exam_connect.id.to_s)
          k = k+1
        elsif data_type.to_i == 1 or data_type.to_i == 2
          data[k] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'tabulation/' +exam_connect.id.to_s+"#view=FitH", :id=>"exams_id_"+exam_connect.id.to_s)
          k = k+1
        elsif data_type.to_i == 3
          data[k] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'continues/' +exam_connect.id.to_s+"#view=FitH", :id=>"exams_id_"+exam_connect.id.to_s)
          k = k+1
        elsif data_type.to_i == 4
          data[k] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'comment_tabulation_pdf/' +exam_connect.id.to_s+"#view=FitH", :id=>"exams_id_"+exam_connect.id.to_s)
          k = k+1
        elsif data_type.to_i == 5
          data[k] = "<a href='javascript:void(0);' id='exams_id_"+exam_connect.id.to_s+"' onclick='get_exam_subject("+exam_connect.id.to_s+")' >"+exam_connect.name.to_s+"</a>"
          k = k+1
        elsif data_type.to_i == 6
          if exam_connect.result_type == 1 or exam_connect.result_type == 2 or exam_connect.result_type == 8 or exam_connect.result_type == 9 or result_type=13  or result_type=14  or result_type=15
            data[k] = "<a href='javascript:void(0);' id='exams_id_"+exam_connect.id.to_s+"' onclick='get_exam_subject_participation("+exam_connect.id.to_s+")' >"+exam_connect.name.to_s+"</a>"
            k = k+1
          end
          
        else
          if exam_connect.result_type == 1 or exam_connect.result_type == 2 or exam_connect.result_type == 8 or exam_connect.result_type == 9
            data[k] = @template.link_to(exam_connect.name.to_s, '/exam/' + 'class_performance_student/' +exam_connect.id.to_s+"#view=FitH", :id=>"exams_id_"+exam_connect.id.to_s)
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
  
  def not_posted_data
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
    all_exams =  Exam.find_all_by_subject_id(all_sub_id,:include=>[{:exam_group=>[:batch]},:subject],:conditions =>["batches.is_deleted = ?",false])
    all_exam_id = all_exams.map(&:id)
    all_score = ExamScore.find_all_by_exam_id(all_exam_id,:select=>"exam_id",:group=>"exam_id")
    all_posted_exam_id = all_score.map(&:exam_id)
    all_exams.each do |exam|
      unless all_posted_exam_id.include?(exam.id)
        @exams.push exam unless exam.nil?
      end
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
    all_exams =  Exam.find_all_by_subject_id(all_sub_id,:include=>[{:exam_group=>[:batch]},:subject],:conditions =>["batches.is_deleted = ? and exam_groups.is_deleted = ?",false, false])
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
        show_exam = true
        all_group_exam = GroupedExam.find_all_by_exam_group_id(@exam_group.id)
        unless all_group_exam.blank?
          map_connect_id = all_group_exam.map(&:connect_exam_id)
          all_exam_connect = ExamConnect.find_all_by_id(map_connect_id)
          unless all_exam_connect.blank?
            all_exam_connect.each do |exam_connect|
              if exam_connect.is_published.to_i == 1
                show_exam = false
                break
              end
            end
          end
        end
        unless exam_subject.blank?  or exam_group_batch.blank? or @exam_group.is_deleted == true or (@exam_group.result_published == true and MultiSchool.current_school.id != 323)
          if show_exam == true
            data[k] = []

            data[k][0] = @template.link_to exam_group_batch.full_name, [@exam_group, exam], :target => "_blank"
            data[k][1] = @template.link_to @exam_group.name, [@exam_group, exam], :target => "_blank"
            data[k][2] = @template.link_to exam_subject.name, [@exam_group, exam], :target => "_blank"

            k = k+1
          end
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
    if !@current_user.admin? and MultiSchool.current_school.id != 323
      @exam_connect =ExamConnect.active.find(:all,:select => "exam_connects.id,exam_connects.result_type,exam_connects.result_type,exam_connects.name,batches.name as batch_name,batches.is_deleted,courses.course_name,courses.section_name,exam_connects.batch_id",:joins=>[{:batch=>[:course]}],:conditions =>["exam_connects.school_id = ? and batches.is_deleted = ? and courses.is_deleted = ? and is_published = ?",MultiSchool.current_school.id, false, false, false])
    else
      @exam_connect =ExamConnect.active.find(:all,:select => "exam_connects.id,exam_connects.result_type,exam_connects.result_type,exam_connects.name,batches.name as batch_name,batches.is_deleted,courses.course_name,courses.section_name,exam_connects.batch_id",:joins=>[{:batch=>[:course]}],:conditions =>["exam_connects.school_id = ? and batches.is_deleted = ? and courses.is_deleted = ?",MultiSchool.current_school.id, false, false])
    end  
    k = 0
    data = []
    @exam_connect.each do |exam_connect|
      exam_connect_batch = exam_connect.batch_name+" "+exam_connect.course_name+" "+exam_connect.section_name
      @subjects = []
      @group_exams = GroupedExam.find_all_by_connect_exam_id(exam_connect.id,:select => "grouped_exams.exam_group_id")
      @exam_group_ids = @group_exams.map(&:exam_group_id)
      exams = Exam.find_all_by_exam_group_id(@exam_group_ids,:select => "exams.id,exams.subject_id,subjects.name as subject_name,exam_groups.exam_category as exam_category",:joins=>[:subject,:exam_group],:conditions =>["subjects.is_deleted = ?", false])
      unless exams.blank?   
        exams.each do |exam|
          if !@subjects.include?(exam.subject_id) 
            if @employee_subjects.include?(exam.subject_id) or @current_user.admin?
              @subjects << exam.subject_id
              data[k] = []
              data[k][0] = @template.link_to(exam_connect_batch.to_s, '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam.subject_id.to_s, :target => "_blank")
              sjws_exam_type_array = [4,6,9,10,11]
              if MultiSchool.current_school.id == 340 and sjws_exam_type_array.include?(exam_connect.result_type.to_i) and (current_user.admin? or current_user.employee_record.is_advisor)
                data[k][1] = @template.link_to(exam_connect.name+"(Comment Entry)", '/exam/' + 'comment_tabulation/' +exam_connect.id.to_s+'?blank_page=1', :target => "_blank")
              elsif MultiSchool.current_school.id != 340 and (@current_user.admin? or (!@batches2.blank? and @batches2.include?(exam_connect.batch_id)))
                data[k][1] = @template.link_to(exam_connect.name+"(Comment Entry)", '/exam/' + 'comment_tabulation/' +exam_connect.id.to_s+'?blank_page=1', :target => "_blank")
              else
                data[k][1] = @template.link_to("<b>"+exam_connect.name+"(Marks Entry)</b>", '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam.subject_id.to_s, :target => "_blank")
              end  
              if MultiSchool.current_school.id == 340 && exam_connect.result_type==12
                data[k][2] = @template.link_to(exam.subject_name+" [Evaluation]", '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam.subject_id.to_s, :target => "_blank")
              elsif MultiSchool.current_school.id == 340 && has_subject_group(exam.subject_id)
                data[k][2] = @template.link_to(exam.subject_name+" [CT]", '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam.subject_id.to_s, :target => "_blank")+" | "+@template.link_to(exam.subject_name+" [Evaluation]", '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam.subject_id.to_s+"?evaluation=1", :target => "_blank")
              else  
                data[k][2] = @template.link_to(exam.subject_name, '/exam/' + 'connect_exam_subject_comments/' +exam_connect.id.to_s+"|"+exam.subject_id.to_s, :target => "_blank")
              end
              if MultiSchool.current_school.id != 340
                data[k][3] = @template.link_to("Marksheet", '/exam/' + 'marksheet/' +exam_connect.id.to_s+"?subject_id="+exam.subject_id.to_s, :target => "_blank")+" "+@template.link_to("Excell", '/exam/' + 'marksheet_excell/' +exam_connect.id.to_s+"?subject_id="+exam.subject_id.to_s, :target => "_blank")
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
    @exam_connect =ExamConnect.active.find(:all,:select => "exam_connects.id,exam_connects.result_type,exam_connects.name,batches.name as batch_name,batches.is_deleted,courses.course_name,courses.section_name,exam_connects.batch_id",:joins=>[{:batch=>[:course]}],:conditions =>["exam_connects.school_id = ? and batches.is_deleted = ? and courses.is_deleted = ?",MultiSchool.current_school.id, false, false])
    k = 0
    data = []
    c_exam_array = []
    @exam_connect.each do |exam_connect|
     
      exam_connect_batch = exam_connect.batch_name+" "+exam_connect.course_name+" "+exam_connect.section_name
      @subjects = []
      @group_exams = GroupedExam.find_all_by_connect_exam_id(exam_connect.id,:select => "grouped_exams.exam_group_id")
         
      @exam_group_ids = @group_exams.map(&:exam_group_id)
        
      exams = Exam.find_all_by_exam_group_id(@exam_group_ids,:select => "exams.id,exams.subject_id,subjects.name as subject_name,subjects.no_exams_sjws as no_exams_sis",:joins=>[:subject],:conditions =>["subjects.is_deleted = ?", false])
      exams.each do |exam|   
        if !@subjects.include?(exam.subject_id) 
          if @employee_subjects.include?(exam.subject_id) or @current_user.admin?
            @subjects << exam.subject_id
              
            
            if school_id == 340
              data[k] = []
              #Sir John Wilson School
              if exam_connect.result_type == 1
                data[k][0] = exam_connect_batch.to_s
                data[k][1] = exam_connect.name.to_s+"("+exam.subject_name.to_s+")" 
                data[k][2] = "<a href='/exam/effot_gradesheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>Effort/Grade Sheet</a>"
                data[k][3] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>Pupil Progress Report</a>"
                data[k][4] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>"
                data[k][5] = "<a href='/exam/d_grade_students/#{exam_connect.id.to_s}#view=FitH' target='_blank'>D Grade Students</a>"
                data[k][6] = "<a href='/exam/section_wise_subject_comparisam/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Comparison</a>"
              elsif exam_connect.result_type == 2
                data[k][0] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam_connect_batch.to_s}</a>"
                data[k][1] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam_connect.name.to_s}</a>"
                data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s}</a>"
                data[k][3] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>Pupil Progress Report</a>"
                data[k][4] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>"
                data[k][5] = "-"
                data[k][6] = "-"
              elsif exam_connect.result_type == 3
                data[k][0] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect_batch.to_s} (All Result)</a>"
                data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
                data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s} (Marksheet)</a>"
                data[k][3] = "-"
                data[k][4] = "<a href='/exam/effot_gradesheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>Effort/Grade Sheet</a>"
                data[k][5] = "-"
                data[k][6] = "-"
              elsif exam_connect.result_type == 4
                data[k][0] = exam_connect_batch.to_s
                data[k][1] = exam_connect.name.to_s
                data[k][2] = exam.subject_name.to_s
                data[k][3] = "<a href='/exam/effot_gradesheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>Effort/Grade Sheet</a>"
                data[k][4] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>"
                data[k][5] = "<a href='/exam/d_grade_students/#{exam_connect.id.to_s}#view=FitH' target='_blank'>D Grade Students</a>"
                data[k][6] = "<a href='/exam/section_wise_subject_comparisam/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Comparison</a>"
              elsif exam_connect.result_type == 7
                data[k][0] = exam_connect_batch.to_s
                data[k][1] = exam_connect.name.to_s
                data[k][2] = exam.subject_name.to_s
                data[k][3] = "-"
                data[k][4] = "<a href='/exam/score_sheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>Score Sheet</a>"
                data[k][5] = "-"
                data[k][6] = "-"
              elsif exam_connect.result_type == 5
                data[k][0] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect_batch.to_s} (All Result)</a>"
                data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
                data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s} (Marksheet)</a>"
                data[k][3] = "-"
                data[k][4] = "<a href='/exam/effot_gradesheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>Effort/Grade Sheet</a>"
                data[k][5] = "-"
                data[k][6] = "-"
              elsif exam_connect.result_type == 6
                data[k][0] = exam_connect_batch.to_s
                data[k][1] = exam_connect.name.to_s+"("+exam.subject_name.to_s+")" 
                data[k][2] = exam.subject_name.to_s
                data[k][3] = "<a href='/exam/effot_gradesheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>Effort/Grade Sheet</a>"
                data[k][4] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>"
                data[k][5] = "<a href='/exam/d_grade_students/#{exam_connect.id.to_s}#view=FitH' target='_blank'>D Grade Students</a>"
                data[k][6] = "<a href='/exam/section_wise_subject_comparisam/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Comparison</a>"
              elsif exam_connect.result_type == 9
                data[k][0] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>#{exam_connect_batch.to_s}</a>"
                data[k][1] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>#{exam_connect.name.to_s}</a>"
                data[k][2] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s}</a>"
                data[k][3] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>Report Card</a>"
                data[k][4] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>Results</a>"
                data[k][5] = "<a href='/exam/d_grade_students/#{exam_connect.id.to_s}#view=FitH' target='_blank'>D Grade Students</a>"
                data[k][6] = "<a href='/exam/section_wise_subject_comparisam/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Comparison</a>"
              elsif exam_connect.result_type == 10
                data[k][0] = exam_connect_batch.to_s
                data[k][1] = exam_connect.name.to_s
                data[k][2] = exam.subject_name.to_s
                data[k][3] = "-"
                data[k][4] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>"
                data[k][5] = "<a href='/exam/d_grade_students/#{exam_connect.id.to_s}#view=FitH' target='_blank'>D Grade Students</a>"
                data[k][6] = "<a href='/exam/section_wise_subject_comparisam/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Comparison</a>"
              elsif exam_connect.result_type == 11
                data[k][0] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>#{exam_connect_batch.to_s}</a>"
                data[k][1] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>#{exam_connect.name.to_s}</a>"
                data[k][2] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s}</a>"
                data[k][3] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>Report Card</a>"
                data[k][4] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>Results</a>"
                data[k][5] = "<a href='/exam/d_grade_students/#{exam_connect.id.to_s}#view=FitH' target='_blank'>D Grade Students</a>"
                data[k][6] = "<a href='/exam/section_wise_subject_comparisam/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Comparison</a>"
              elsif exam_connect.result_type == 12
                data[k][0] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam_connect_batch.to_s}</a>"
                data[k][1] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam_connect.name.to_s}</a>"
                data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s}</a>"
                data[k][3] = "-"
                data[k][4] = "<a href='/exam/effot_gradesheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>Effort/Grade Sheet</a>"
                data[k][5] = "-"
                data[k][6] = "-"
              elsif exam_connect.result_type == 13
                data[k][0] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>#{exam_connect_batch.to_s}</a>"
                data[k][1] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>#{exam_connect.name.to_s}</a>"
                data[k][2] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s}</a>"
                data[k][3] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>Report Card</a>"
                data[k][4] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>Results</a>"
                data[k][5] = "-"
                data[k][6] = "-"
              elsif exam_connect.result_type == 14
                data[k][0] = exam_connect_batch.to_s 
                data[k][1] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD ("+exam_connect.name.to_s+")</a>"
                data[k][2] = "<a href='/exam/continues/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>REPORT CARD ("+exam.subject_name.to_s+")</a>"
                data[k][3] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}&evaluation=1' target='_blank'>EVALUATION REPORT</a>"
                data[k][4] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>Pupil Progress Report</a>"
                data[k][5] = "<a href='/exam/d_grade_students/#{exam_connect.id.to_s}#view=FitH' target='_blank'>D Grade Students</a>"
                data[k][6] = "<a href='/exam/section_wise_subject_comparisam/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Comparison</a>"
              elsif exam_connect.result_type == 15
                data[k][0] = exam_connect_batch.to_s
                data[k][1] = exam_connect.name.to_s
                data[k][2] = exam.subject_name.to_s
                data[k][3] = "-"
                data[k][4] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>"
                data[k][5] = "<a href='/exam/d_grade_students/#{exam_connect.id.to_s}#view=FitH' target='_blank'>D Grade Students</a>"
                data[k][6] = "<a href='/exam/section_wise_subject_comparisam/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}' target='_blank'>Comparison</a>"
              end  
              k = k+1
            elsif school_id == 348
              if exam_connect.result_type == 1 or exam_connect.result_type == 2  or exam_connect.result_type == 7 
                unless c_exam_array.include?(exam_connect.id.to_i)
                  data[k] = []
                  data[k][0] = exam_connect_batch.to_s
                  data[k][1] = exam_connect.name.to_s
                  data[k][2] = "All"
                  data[k][3] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>"
                  c_exam_array << exam_connect.id.to_i
                  k = k+1
                end
              elsif exam_connect.result_type == 15
                  data[k] = []
                  data[k][0] = exam_connect_batch.to_s
                  data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
                  data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s} (Marksheet)</a>&nbsp;&nbsp;<a href='/exam/sis_report_excell/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>Excell</a>"
                  if exam_connect.result_type.to_i > 8 && exam_connect.result_type.to_i != 11
                    data[k][3] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>&nbsp;&nbsp;<a href='/exam/continues/#{exam_connect.id.to_s}?covid=1#view=FitH' target='_blank'>REPORT CARD COVID</a>&nbsp;&nbsp; <a href='/exam/continues/#{exam_connect.id.to_s}?transscript=1#view=FitH' target='_blank'>Transcript</a>"
                  else
                    data[k][3] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>&nbsp;&nbsp;<a href='/exam/continues/#{exam_connect.id.to_s}?covid=1#view=FitH' target='_blank'>REPORT CARD COVID</a>"
                  end
                  k = k+1
              else
                if exam.no_exams_sis.to_i == 0
                  data[k] = []
                  data[k][0] = exam_connect_batch.to_s
                  data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
                  data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s} (Marksheet)</a>"
                  if exam_connect.result_type.to_i > 8 && exam_connect.result_type.to_i != 11
                    data[k][3] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>&nbsp;&nbsp;<a href='/exam/continues/#{exam_connect.id.to_s}?covid=1#view=FitH' target='_blank'>REPORT CARD COVID</a>&nbsp;&nbsp; <a href='/exam/continues/#{exam_connect.id.to_s}?transscript=1#view=FitH' target='_blank'>Transcript</a>"
                  else
                    data[k][3] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>&nbsp;&nbsp;<a href='/exam/continues/#{exam_connect.id.to_s}?covid=1#view=FitH' target='_blank'>REPORT CARD COVID</a>"
                  end
                  k = k+1
                end
              end
            elsif school_id == 352 or school_id == 324 or school_id == 357
              unless c_exam_array.include?(exam_connect.id.to_i)
                data[k] = []
                if ((exam_connect.result_type == 17 or exam_connect.result_type == 19 or exam_connect.result_type == 20 or exam_connect.result_type == 21) and  school_id != 352)
                  data[k][0] = exam_connect_batch.to_s
                  data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
                  data[k][2] = "-"
                  data[k][3] = "-"
                  data[k][4] = "-"
                  data[k][5] = "-"
                  data[k][6] = "-"
                  data[k][7] = "-"
                  data[k][8] = "-"
                  data[k][9] = "-"
                  c_exam_array << exam_connect.id.to_i
                elsif (exam_connect.result_type == 18 and  school_id != 352)
                  data[k][0] = exam_connect_batch.to_s
                  data[k][1] = exam_connect.name.to_s
                  data[k][2] = "-"
                  data[k][3] = "-"
                  data[k][4] = "-"
                  data[k][5] = "-"
                  data[k][6] = "-"
                  data[k][7] = "-"
                  data[k][8] = "-"
                  data[k][9] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>"
                  c_exam_array << exam_connect.id.to_i
                elsif (exam_connect.result_type >= 13 and  school_id != 352)
                  data[k][0] = exam_connect_batch.to_s
                  data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
                  data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}' target='_blank'>#{exam.subject_name.to_s} (Marksheet)</a>"
                  data[k][3] = "-"
                  data[k][4] = "-"
                  data[k][5] = "-"
                  data[k][6] = "-"
                  data[k][7] = "-"
                  data[k][8] = "-"
                  data[k][9] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>"
                  c_exam_array << exam_connect.id.to_i
                else  
                  
                  data[k][0] = exam_connect_batch.to_s
                  data[k][1] = "<a href='/exam/tabulation_excell/#{exam_connect.id.to_s}' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
                  data[k][2] = "<a href='/exam/mert_list_sagc/#{exam_connect.id.to_s}' target='_blank'>Merit List</a>"
                  data[k][3] = "<a href='/exam/mert_list_sagc/#{exam_connect.id.to_s}?class=1' target='_blank'>Merit List (All)</a>"
                  data[k][4] = "<a href='/exam/summary_report/#{exam_connect.id.to_s}' target='_blank'>Summary Report</a>"
                  data[k][5] = "<a href='/exam/summary_report/#{exam_connect.id.to_s}?class=1' target='_blank'>Summary Report (All)</a>"
                  data[k][6] = "<a href='/exam/subject_wise_pass_failed/#{exam_connect.id.to_s}' target='_blank'>Subject Pass Fail</a>"
                  data[k][7] = "<a href='/exam/subject_wise_pass_failed/#{exam_connect.id.to_s}?class=1' target='_blank'>Subject Pass Fail (All)</a>"
                  data[k][8] = "<a href='/marks/non_posted_marks_entry/#{exam_connect.id.to_s}' target='_blank'>Non Posted Marks</a>"
                  data[k][9] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>REPORT CARD</a>"
                  c_exam_array << exam_connect.id.to_i
                  
                end
                k = k+1
              end
            elsif school_id == 356
              if exam_connect.result_type == 3
                data[k] = []
                data[k][0] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect_batch.to_s} (All Result)</a>"
                data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
                data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s} (Marksheet)</a>"
                data[k][3] = "-"
                data[k][4] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>Results</a>"
                k = k+1
              else
                data[k] = []
                data[k][0] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect_batch.to_s} (All Result)</a>"
                data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
                data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s} (Marksheet)</a>"
                data[k][3] = "<a href='/exam/comment_tabulation_pdf/#{exam_connect.id.to_s}#view=FitH' target='_blank'>Comment Entry</a>"
                data[k][4] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>Results</a>"
                k = k+1
              end
            elsif school_id == 342
              data[k] = []
              data[k][0] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect_batch.to_s} (All Result)</a>"
              data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>&nbsp;&nbsp;<a href='/exam/tabulation_excell_sjis/#{exam_connect.id.to_s}'>Excel</a>"
              data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s} (Marksheet)</a>"
              data[k][3] = "<a href='/exam/comment_tabulation_pdf/#{exam_connect.id.to_s}#view=FitH' target='_blank'>Comment Entry</a>"
              data[k][4] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>Results</a>"
              k = k+1
            elsif school_id == 362
              data[k] = []
              data[k][0] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect_batch.to_s} (All Result)</a>"
              data[k][1] = "#{exam_connect.name.to_s}"
              data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s} (Marksheet)</a>"
              data[k][3] = "-"
              data[k][4] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>Results</a>"
              k = k+1
            elsif exam_connect.result_type == 12 and  school_id != 323 and  school_id != 319
              data[k] = []
              data[k][0] = exam_connect_batch.to_s
              data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
              data[k][2] = "-"
              data[k][3] = "-"
              data[k][4] = "-"
              c_exam_array << exam_connect.id.to_i
              k = k+1
            else
              data[k] = []
              data[k][0] = "<a href='/exam/continues/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect_batch.to_s} (All Result)</a>"
              data[k][1] = "<a href='/exam/tabulation/#{exam_connect.id.to_s}#view=FitH' target='_blank'>#{exam_connect.name.to_s} (Tablulation)</a>"
              data[k][2] = "<a href='/exam/marksheet/#{exam_connect.id.to_s}?subject_id=#{exam.subject_id.to_s}#view=FitH' target='_blank'>#{exam.subject_name.to_s} (Marksheet)</a>"
              data[k][3] = "<a href='/exam/comment_tabulation_pdf/#{exam_connect.id.to_s}#view=FitH' target='_blank'>Comment Entry</a>"
              data[k][4] = "<a href='/exam/generated_report5?connect_exam=#{exam_connect.id.to_s}&batch_id=#{exam_connect.batch_id.to_s}#view=FitH' target='_blank'>Results</a>"
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
      @batches2 = @current_user.employee_record.batches
      @batches2 += @current_user.employee_record.subjects.collect{|b| b.batch}
      @batches2 = @batches2.uniq unless @batches2.empty?
      @batches = []
      unless @batches2.blank?
        @batches2.each do |batch|
          if batch.is_deleted == false
            @batches << batch
          end
        end
      end
      
    elsif current_user.admin
      @batches = Batch.active
    end  
    
  end
  def connect_exam
    @exams_data = ExamConnect.active.find(:all,:group=>"name",:conditions =>["school_id = ?",MultiSchool.current_school.id])
    if current_user.employee
      @batches2 = @current_user.employee_record.batches
      @batches2 += @current_user.employee_record.subjects.collect{|b| b.batch}
      @batches2 = @batches2.uniq unless @batches2.empty?
      @batches = []
      unless @batches2.blank?
        @batches2.each do |batch|
          if batch.is_deleted == false
            @batches << batch
          end
        end
      end
      
    elsif current_user.admin
      @batches = Batch.active
    end 
  end
  def non_posted_marks_entry
    @connect_exam_obj = ExamConnect.active.find(params[:id])
    @batch = Batch.find(@connect_exam_obj.batch_id)
    render :pdf => 'non_posted_marks_entry',
      :orientation => 'Portrait', :zoom => 1.00,
      :margin => {    :top=> 10,
      :bottom => 10,
      :left=> 10,
      :right => 10},
      :header => {:html => { :template=> 'layouts/pdf_empty_header.html'}},
      :footer => {:html => { :template=> 'layouts/pdf_empty_footer.html'}}
  end
  def non_posted_exam
    @exams_data = ExamConnect.active.find(:all,:group=>"name",:conditions =>["school_id = ?",MultiSchool.current_school.id])
    if current_user.employee
      @batches2 = @current_user.employee_record.batches
      @batches2 += @current_user.employee_record.subjects.collect{|b| b.batch}
      @batches2 = @batches2.uniq unless @batches2.empty?
      @batches = []
      unless @batches2.blank?
        @batches2.each do |batch|
          if batch.is_deleted == false
            @batches << batch
          end
        end
      end
      
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
      @batches2 = @current_user.employee_record.batches
      @batches2 += @current_user.employee_record.subjects.collect{|b| b.batch}
      @batches2 = @batches2.uniq unless @batches2.empty?
      @batches = []
      unless @batches2.blank?
        @batches2.each do |batch|
          if batch.is_deleted == false
            @batches << batch
          end
        end
      end
    elsif current_user.admin
      @batches = Batch.active
    end 
  end
 
end
