class OnlineExamController < ApplicationController
  before_filter :login_required #,:online_exam_enabled
  before_filter :check_permission, :only=>[:index]
  filter_access_to :all

  def index
    
  end

  def new_online_exam
    @current_user = current_user
    @online_exam_group  = OnlineExamGroup.new(params[:online_exam_group])
    @batches = Batch.active.find(:all, :group => "name")
    
    if @current_user.admin? or @current_user.employee?
      @classes = []
      @batch_no = 0
      @course_name = ""
      @courses = []
    end

    if request.post?
      
      if params[:batch_id].nil?
        batch_name = ""
        if Batch.active.find(:all, :group => "name").length > 1
          unless params[:student].nil?
            unless params[:student][:batch_name].nil?
              batch_id = params[:student][:batch_name]
              batches_data = Batch.find_by_id(batch_id)
              batch_name = batches_data.name
            end
          end
        else
          batches = Batch.active
          batch_name = batches[0].name
        end
        course_id = 0
        unless params[:course_id].nil?
          course_id = params[:course_id]
        end
        if course_id == 0
          unless params[:student].nil?
            unless params[:student][:section].nil?
              course_id = params[:student][:section]
            end
          end
        end

        @batch_data = Rails.cache.fetch("course_data_#{course_id}_#{batch_name.parameterize("_")}_#{current_user.id}"){
          if batch_name.length == 0
            batches = Batch.find_by_course_id(course_id)
          else
            batches = Batch.find_by_course_id_and_name(course_id, batch_name)
          end
          batches
        }
        @batch_id = 0
        unless @batch_data.nil?
          params[:batch_id] = @batch_data.id 
        end
      else
        batch = Batch.find(params[:batch_id])
        params[:batch_id] = batch.id
      end
      
      unless params[:batch_id].nil?
        @batch_ids = params[:batch_id]
        #@batch_ids.each do |b|
        
        @online_exam_group  = OnlineExamGroup.new(params[:online_exam_group])
        @online_exam_group.batch_id = @batch_ids

        unless params[:assignment].nil?
          unless params[:assignment][:subject].nil?
            @online_exam_group.subject_id = params[:assignment][:subject]
          end
        end
        
        if @online_exam_group.save
          @id=@online_exam_group.id
          @flag=1
        else
          @flag=0
        end
          
        #end
        
        if @flag==1
          redirect_to :action=>:new_question ,:id=>@id
        else
          render :action=>:new_online_exam
        end
      else
        @online_exam_group.errors.add_to_base("#{t('batch_cant_be_blank')}")
        render :action=>:new_online_exam 
      end
    end
    
  end

  def new_question
    #    @online_exam_group  = OnlineExamGroup.new(params[:online_exam_group])
    #    @batches = Batch.active
    #    @group_ids = Array.new
    #    unless params[:batch_ids].nil?
    #      @batch_ids= params[:batch_ids]
    #      @batch_ids.each do |b|
    #        @online_exam_group  = OnlineExamGroup.new(params[:online_exam_group])
    #        @online_exam_group.batch_id = b
    #        if @online_exam_group.save
    #          @group_ids.push(@online_exam_group.id)
    #        else
    #          render :action=>:new_online_exam and return
    #        end
    #      end
    #    else
    #      @online_exam_group.errors.add_to_base("#{t('batch_cant_be_blank')}")
    #      render :action=>:new_online_exam and return
    #    end
    exam_group=OnlineExamGroup.find(params[:id])
    @group_ids=OnlineExamGroup.find(:all,:conditions=>{:name=>exam_group.name,:start_date=>exam_group.start_date,:end_date=>exam_group.end_date,:maximum_time=>exam_group.maximum_time,:pass_percentage=>exam_group.pass_percentage,:option_count=>exam_group.option_count,:is_deleted=>exam_group.is_deleted,:is_published=>exam_group.is_published}).collect(&:id)
    @option_count  = exam_group.option_count.to_i
    @online_exam_question = OnlineExamQuestion.new
    @option_count.to_i.times { @online_exam_question.online_exam_options.build }
  end

  def create_question
    unless params[:group_ids].nil? and params[:options_count].nil?
      @group_ids = params[:group_ids]
      @option_count  = (params[:option_count]).to_i
      @group_ids.each do |g|
        @online_exam_question = OnlineExamQuestion.new(params[:online_exam_question])
        @online_exam_question.online_exam_group_id = g
        if @online_exam_question.save
          @online_exam_question = OnlineExamQuestion.new
          @option_count.to_i.times { @online_exam_question.online_exam_options.build }
          flash[:notice]= "#{t('question_created_successfully')}"
        end
      end
    else
      flash[:notice]="#{t('flash_msg4')}"
      redirect_to :controller=>'user',:action=>'dashboard'
    end
  end
  
  def get_classes
    school_id = MultiSchool.current_school.id
    @courses = Rails.cache.fetch("classes_data_#{params[:batch_id]}_#{school_id}"){
      unless params[:batch_id].empty?
        batch_data = Batch.find params[:batch_id]
        batch_name = batch_data.name
        batches = Batch.find(:all, :conditions => ["name = ? and is_deleted = 0", batch_name]).map{|b| b.course_id}
        tmp_classes = Course.find(:all, :conditions => ["id IN (?) and is_deleted = 0", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
      else  
        tmp_classes = []
      end
      class_data = tmp_classes
      class_data
    }
    @classes = []
    @batch_id = ''
    @course_name = ""
    render :update do |page|
      page.replace_html 'course', :partial => 'courses', :object => @courses
    end
  end
  
  def get_section_data
    batch_id = 0
    unless params[:student].nil?
      unless params[:student][:batch_name].nil?
        batch_id = params[:student][:batch_name]
      end
    end
    
    unless params[:advv_search].nil?
      unless params[:advv_search][:batch_name].nil?
        batch_id = params[:advv_search][:batch_name]
      end
    end
    
    school_id = MultiSchool.current_school.id
    @classes = Rails.cache.fetch("section_data_#{params[:class_name].parameterize("_")}_#{batch_id}_#{school_id}"){
      if batch_id.to_i > 0
        batch = Batch.find batch_id
        batch_name = batch.name
        
        batches = Batch.find(:all, :conditions => ["name = ? and is_active = 1 and is_deleted = 0", batch_name]).map{|b| b.course_id}
        tmp_class_data = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0 and id IN (?)",params[:class_name], batches])
      else  
        tmp_class_data = Course.find(:all, :conditions => ["course_name LIKE ? and is_deleted = 0",params[:class_name]])
      end
      class_data = tmp_class_data
      class_data
    }
    @selected_section = 0
    
    @batch_id = 0
    @courses = []
    
    render :update do |page|
      page.replace_html 'section', :partial => 'sections', :object => @classes
    end
  end

  def view_online_exam
    @classes = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    @batches = Batch.active.find(:all, :group => "name")
  end

  def show_active_exam
    
    if params[:batch_id].nil?
        batch_name = ""
        if Batch.active.find(:all, :group => "name").length > 1
          unless params[:student].nil?
            unless params[:student][:batch_name].nil?
              batch_id = params[:student][:batch_name]
              batches_data = Batch.find_by_id(batch_id)
              batch_name = batches_data.name
            end
          end
        else
          batches = Batch.active
          batch_name = batches[0].name
        end
        course_id = 0
        unless params[:course_id].nil?
          course_id = params[:course_id]
        end
        if course_id == 0
          unless params[:student].nil?
            unless params[:student][:section].nil?
              course_id = params[:student][:section]
            end
          end
        end

        @batch_data = Rails.cache.fetch("course_data_#{course_id}_#{batch_name.parameterize("_")}_#{current_user.id}"){
          if batch_name.length == 0
            batches = Batch.find_by_course_id(course_id)
          else
            batches = Batch.find_by_course_id_and_name(course_id, batch_name)
          end
          batches
        }
        @batch_id = 0
        unless @batch_data.nil?
          params[:batch_id] = @batch_data.id 
        end
    else
      batch = Batch.find(params[:batch_id])
      params[:batch_id] = batch.id
    end
    
    #    if @batch_id == ''
    #      @subjects = []
    #    else
    #      @batch = Batch.find @batch_id
    #      @normal_subjects = Subject.find_all_by_batch_id(@batch,:conditions=>"elective_group_id IS NULL AND is_deleted = false")
    #      @student_electives =StudentsSubject.all(:conditions=>{:batch_id=>@batch,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
    #      @elective_subjects = []
    #      @student_electives.each do |e|
    #        @elective_subjects.push Subject.find(e.subject_id)
    #      end
    #      @subjects = @normal_subjects+@elective_subjects
    #    end
    
    #params[:batch_id] = params[:student][:batch_name]
    
    @exams = OnlineExamGroup.paginate(:page => params[:page], :per_page => 20 ,:conditions=>[ "batch_id = '#{params[:batch_id]}'"], :include=> [:online_exam_attendances, :subject], :order=>"id DESC")
    render :partial=>'active_exam_list'
  end

  def edit_exam_group
    @exam_group = OnlineExamGroup.find(params[:id])
    
    @batch_no = @exam_group.batch_id
    @batch_id = @exam_group.batch_id
    
    @batch_data = Batch.active.find(:first, :conditions => ["batches.id = ?", @batch_id])
    @course_data = Course.find_by_id(@batch_data.course_id)
    @course_name = @course_data.course_name
    @classes = Course.find(:all, :conditions => ["course_name LIKE ?",@course_name])
    @tmp_courses = @classes.map{|c| c.id}
    
    @courses = []
    @subjects = []
    @elective_subjects = []
    @batches = Batch.active.find(:all, :group => "name", :conditions => ['course_id IN (?)', @tmp_courses] )
    
    batch_name = @batch_data.name
    batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
    
    @courses = Course.find(:all, :conditions => ["id IN (?) and is_deleted = 0", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    @classes = Course.find(:all, :conditions => ["id IN (?) and is_deleted = 0 and course_name = ?", batches, @course_name])
    
    @selected_section = @course_data.id
    
    unless @batch_id.nil?
      @normal_subjects = Subject.find_all_by_batch_id(@batch_data,:conditions=>"elective_group_id IS NULL AND is_deleted = false")
      @student_electives =StudentsSubject.all(:conditions=>{:batch_id=>@batch_data,:subjects=>{:is_deleted=>false}},:joins=>[:subject])
      @student_electives.each do |e|
        @elective_subjects.push Subject.find(e.subject_id)
      end
      @subjects = @normal_subjects + @elective_subjects
    end
    
    respond_to do |format|
      format.js { render :action => 'edit_group' }
    end
  end

  def update_exam_group
    @exam_group = OnlineExamGroup.find(params[:id])
    @exam_group.subject_id = params[:assignment][:subject]
    unless @exam_group.update_attributes(params[:exam_group])
      @error = true
    end
    @exams = OnlineExamGroup.paginate(:page => params[:page], :conditions=>[ "batch_id = '#{@exam_group.batch_id}'"], :order=>"id DESC")
  end

  def delete_exam_group
    @exam_group = OnlineExamGroup.find(params[:id])
    batch_id = @exam_group.batch_id
    if @exam_group.destroy
      #flash[:notice]="#{t('exam_group_successfully_deleted')}"
    end
    @exams = OnlineExamGroup.find_all_by_batch_id(batch_id)
    render :update do |page|
      page.replace_html 'exam-list', :partial=>'active_exam_list'
      page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('exam_group_successfully_deleted')}</p>"
    end
  end

  def exam_details
    @exam_group=OnlineExamGroup.find(params[:id],:include => [:subject])
    @attendance=@exam_group.has_attendence
    @exam_questions = @exam_group.online_exam_questions.all(:include=>[:online_exam_options])
  end

  def edit_question
    @question = OnlineExamQuestion.find(params[:id])
    if request.post? and @question.update_attributes(params[:question])
      redirect_to :action=>:exam_details, :id=>@question.online_exam_group_id
    end
  end

  def delete_question
    @question = OnlineExamQuestion.find(params[:id])
    exam_group = @question.online_exam_group_id
    @question.destroy
    redirect_to :action=>:exam_details, :id=>exam_group
  end

  def edit_exam_option
    @option = OnlineExamOption.find(params[:id])
    if request.post? and @option.update_attributes(params[:option])
      redirect_to :action=>:exam_details, :id=>@option.online_exam_question.online_exam_group_id
    end
  end

  def add_extra_question
    @exam_group = OnlineExamGroup.find(params[:id])
    @online_exam_question = OnlineExamQuestion.new
    @exam_group.option_count.to_i.times { @online_exam_question.online_exam_options.build }
    if request.post?
      @online_exam_question = OnlineExamQuestion.new(params[:online_exam_question])
      @online_exam_question.online_exam_group_id = @exam_group.id
      if @online_exam_question.save
        redirect_to :action=>:exam_details, :id=>@exam_group.id
      end
    end
  end

  def publish_exam
    @exam_group = OnlineExamGroup.find(params[:id])
    unless @exam_group.online_exam_questions.blank?
      @exam_group.update_attributes(:is_published=>true)
      if @exam_group.batch_id?
        reminder_recipient_ids = []
        batch_ids = {}
        student_ids = {}
        @batch_students = Student.find(:all, :conditions=>"batch_id = #{@exam_group.batch_id}")
        
        @batch_students.each do |s|
          reminder_recipient_ids << s.user_id
          batch_ids[s.user_id] = s.batch_id
          student_ids[s.user_id] =  s.id
          unless s.immediate_contact.nil?
            reminder_recipient_ids << s.immediate_contact.user_id
            batch_ids[s.immediate_contact.user_id] = s.batch_id
            student_ids[s.immediate_contact.user_id] = s.id
          end
        end
        unless reminder_recipient_ids.empty?
          Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
          :recipient_ids => reminder_recipient_ids,
          :subject=>"New Online Exam",
          :rtype=>15,
          :rid=>@exam_group.id,
          :student_id => student_ids,
          :batch_id => batch_ids,
          :body=>"New Online Exam \""+@exam_group.name.to_s+"\" Published" ))
        end
      end
      
    end
    @exams = OnlineExamGroup.paginate(:page => params[:page], :per_page => 20, :conditions=>[ "batch_id = '#{@exam_group.batch_id}'"], :order=>"id DESC")
    if @exam_group.online_exam_questions.blank?
      render :update do |page|
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('sorry_cannot_publish_an_exam_without_questions_please_add_minimum_one_question')}</p>"
      end
    else
      render :update do |page|
        page.replace_html 'exam-list', :partial=>'active_exam_list'
        page.replace_html 'flash_box', :text => "<p class='flash-msg'>#{t('exam_published')}</p>"
      end
    end
  end

  def view_result
    @batches = Batch.active
  end

  def update_exam_list
    @exams = OnlineExamGroup.find_all_by_batch_id(params[:batch_id],:conditions=>"is_published = 1",:order=>"id DESC")
    render :update do |page|
      page.replace_html 'exam-list', :partial=>'exam_list'
    end
  end

  def exam_result
    @exam_group = OnlineExamGroup.find(params[:id])
    @attendance = @exam_group.online_exam_attendances
    @attendance.reject!{|s|s.student.nil?}
  end
    
  def exam_result_pdf
    @exam_group = OnlineExamGroup.find(params[:id])
    @attendance = @exam_group.online_exam_attendances
    @attendance.reject!{|s|s.student.nil?}
    @batch = @exam_group.batch
    render :pdf=>'Online_exam_result'
  end

  def reset_exam
    @batches = Batch.active.all(:include=>:course)
  end

  def update_student_exam
    @exams = OnlineExamGroup.find_all_by_batch_id(params[:batch_id],:conditions=>"is_published = 1",:order=>"id DESC")
    render :update do |page|
      page.replace_html 'exam-list', :partial=>'student_exam_list'
    end
  end

  def update_student_list
    @exam_group = OnlineExamGroup.find(params[:id])
    @attendance = @exam_group.online_exam_attendances
    render :update do |page|
      page.replace_html 'student-list', :partial=>'student_list'
    end
  end

  def update_reset_exam
    unless request.get?
      unless params[:att_id].blank?
        ActiveRecord::Base.transaction do
          OnlineExamAttendance.hard_delete(params[:att_id])
        end
        flash[:notice]="#{t('exam_reset_successful_for_selected_students')}"
        redirect_to :action=>:index
      else
        flash[:notice]="#{t('sorry_no_students_selected')}"
        redirect_to :action => :index
      end
    else
      flash[:notice] = "#{t('flash_msg4')}"
      redirect_to :controller => "user", :action => "dashboard"
    end
  end
end
