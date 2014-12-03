class OnlineExamController < ApplicationController
  before_filter :login_required #,:online_exam_enabled
  filter_access_to :all

  def index
    
  end

  def new_online_exam
    @online_exam_group  = OnlineExamGroup.new(params[:online_exam_group])
    @batches = Batch.active
    if request.post?
      unless params[:batch_ids].nil?
        @batch_ids= params[:batch_ids]
        @batch_ids.each do |b|
          @online_exam_group  = OnlineExamGroup.new(params[:online_exam_group])
          @online_exam_group.batch_id = b
          if @online_exam_group.save
            @id=@online_exam_group.id
            @flag=1
          else
            @flag=0
          end
        end
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

  def view_online_exam
    @batches = Batch.active
  end

  def show_active_exam
    @exams = OnlineExamGroup.paginate(:page => params[:page], :per_page => 20 ,:conditions=>[ "batch_id = '#{params[:batch_id]}'"], :include=> :online_exam_attendances,:order=>"id DESC")
    render :partial=>'active_exam_list'
  end

  def edit_exam_group
    @exam_group = OnlineExamGroup.find(params[:id])
    respond_to do |format|
      format.js { render :action => 'edit_group' }
    end
  end

  def update_exam_group
    @exam_group = OnlineExamGroup.find(params[:id])
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
    @exam_group=OnlineExamGroup.find(params[:id])
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
