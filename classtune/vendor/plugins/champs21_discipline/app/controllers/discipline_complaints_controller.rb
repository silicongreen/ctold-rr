class DisciplineComplaintsController < ApplicationController
  before_filter :login_required
  before_filter :check_permission, :only=>[:index]
  filter_access_to :all, :except=>[:show,:download_attachment]
  filter_access_to [:show,:download_attachment], :attribute_check=>true
  before_filter :complaint_present, :only => [:edit,:update,:destroy,:show,:list_comments,:decision,:decision_close]
  def index
    @discipline_complaints=DisciplineComplaint.sort_discipline(params[:sort_param],current_user.id).paginate(:page => params[:page], :per_page=>10)
    if request.xhr?
      render(:update) do |page|
        page.replace_html'list',:partial=>'sort_pending'
      end
    end
  end

  def new
    @discipline_complaint=DisciplineComplaint.new
    @last_registered_complaint=DisciplineComplaint.cmp_no
    @date=Date.today
  end

  def create
    attachment_fields = params[:discipline_complaint][:attachment]
    params[:discipline_complaint].delete(:attachment)
    @discipline_complaint=DisciplineComplaint.new(params[:discipline_complaint])
    @discipline_complaint.user_id=current_user.id
    if @discipline_complaint.save
      @master=@discipline_complaint.build_discipline_master("user_id"=>current_user.id)
      if @master.save
        unless attachment_fields.nil?
          @attachment=@master.discipline_attachments.build(:attachment=>attachment_fields)
          @attachment.save
        end
      end
      recipients=@discipline_complaint.discipline_participations.reject{|e| e.user_id==current_user.id}.collect(&:user_id)
      subject = t('complaint')
      body ="#{t('you_are_a_participant_on_complaint')} '"+@discipline_complaint.subject+"'"
      Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
          :recipient_ids => recipients,
          :subject=>subject,
          :body=>body))
      redirect_to :controller=>"discipline_complaints", :action=>"index"
      flash[:notice]="#{t('flash2')}"
    else
      @date=params[:discipline_complaint][:trial_date]
      params[:discipline_complaint][:complaint_no].present? ? @last_registered_complaint = params[:discipline_complaint][:complaint_no] : @last_registered_complaint =nil
      render :action=>"new"
    end
  end

  def edit
    @discipline_attachment=@discipline_complaint.discipline_master.discipline_attachments.last
  end

  def update
    attachment_fields = params[:discipline_complaint][:attachment]
    params[:discipline_complaint].delete(:attachment)
    participations=@discipline_complaint.discipline_participations.all
    @discipline_complaint.action_taken=0;
    @discipline_attachment=@discipline_complaint.discipline_master.discipline_attachments.last
    if @discipline_complaint.update_attributes(params[:discipline_complaint])
      @master=@discipline_complaint.discipline_master
      unless attachment_fields.nil?
        @attachment=@master.discipline_attachments.build(:attachment=>attachment_fields)
        @attachment.save
      end
      new_participations=@discipline_complaint.discipline_participations.all
      @discipline_complaint.discipline_participations.each do |participation|
        participation.update_attributes(:action_taken=>false)
      end
      new_added=new_participations-participations
      recipients=participations.reject{|e| e.user_id==current_user.id}.collect(&:user_id)
      subject1 = t('complaint')
      body1 ="#{t('the_complaint')} '"+@discipline_complaint.subject+"' #{t('under_your_participatin_is updated')}"
      Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
          :recipient_ids => recipients,
          :subject=>subject1,
          :body=>body1))
      recipients=new_added.reject{|e| e.user_id==current_user.id}.collect(&:user_id)
      subject2 = t('complaint')
      body2 ="#{t('you_are_a_participant_on_complaint')} '"+@discipline_complaint.subject+"'"
      Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
          :recipient_ids => recipients,
          :subject=>subject2,
          :body=>body2))
      redirect_to :controller=>"discipline_complaints", :action=>"index"
      flash[:notice]="#{t('flash1')}"
    else
      @discipline_complainees=@discipline_complaint.discipline_complainees.each{|dc| dc[:_destroy] = ""}
      @discipline_accusations=@discipline_complaint.discipline_accusations.each{|dc| dc[:_destroy] = ""}
      @discipline_juries=@discipline_complaint.discipline_juries.each{|dc| dc[:_destroy] = ""}
      @discipline_members=@discipline_complaint.discipline_members.each{|dc| dc[:_destroy] = ""}
      render :action=>"edit"
    end
  end

  def download_attachment   
    upload = DisciplineAttachment.find(params[:id1])
    send_file upload.attachment.path,
      :filename => upload.attachment_file_name,
      :type => upload.attachment_content_type,
      :disposition => 'attachment'
  end

  def delete_attachment
    @discipline_attachment = DisciplineAttachment.find(params[:id])
    id=@discipline_attachment.discipline_participation.discipline_complaint.id
    @discipline_attachment.destroy
    redirect_to :controller=>"discipline_complaints",:action=>"edit",:id=>id
    flash[:notice]="#{t('flash3')}"
  end

  def destroy
    @discipline_complaint.destroy
    flash[:notice]="#{t('flash3')}"
    redirect_to:controller=>"discipline_complaints", :action=>"index"
  end

  def search_complaint_ajax
    if params[:query].present?
      @discipline_complaints = DisciplineComplaint.sort_discipline("",current_user.id) & DisciplineComplaint.find(:all,:include=>[:discipline_master],
        :conditions => "(subject LIKE \"#{params[:query]}%\"
                          OR complaint_no LIKE \"#{params[:query]}%\")",
        :order => "subject asc") unless params[:query] == ''
    else
      @discipline_complaints =DisciplineComplaint.sort_discipline("",current_user.id)
    end
    render :layout => false
  end

  def search_complainee
    students= User.active.find(:all, :conditions=>["student=1 AND (username LIKE ? OR first_name LIKE ?)", "%#{params[:query]}%","%#{params[:query]}%"])
    render :json=>{'query'=>params["query"],'suggestions'=>students.collect{|s| s.full_name.length+s.username.length > 20 ? s.full_name[0..(18-s.username.length)]+".. "+"-"+s.username : s.full_name+"-"+s.username},'data'=>students.collect(&:id)  }
  end
   
  def search_accused
    students= User.active.find(:all, :conditions=>["student=1 AND (username LIKE ? OR first_name LIKE ?)", "%#{params[:query]}%","%#{params[:query]}%"])
    render :json=>{'query'=>params["query"],'suggestions'=>students.collect{|s| s.full_name.length+s.username.length > 20 ? s.full_name[0..(18-s.username.length)]+".. "+"-"+s.username : s.full_name+"-"+s.username},'data'=>students.collect(&:id)  }
  end

  def search_juries
    juries= User.active.find(:all, :conditions=>["employee=1 AND (username LIKE ? OR first_name LIKE ?)", "%#{params[:query]}%","%#{params[:query]}%"])
    render :json=>{'query'=>params["query"],'suggestions'=>juries.collect{|s| s.full_name.length+s.username.length > 20 ? s.full_name[0..(18-s.username.length)]+".. "+"-"+s.username : s.full_name+"-"+s.username},'data'=>juries.collect(&:id)  }
  end

  def search_users
    users= User.active.find(:all, :conditions=>["(admin=1 OR employee=1 OR student=1)  AND (username LIKE ? OR first_name LIKE ?)", "%#{params[:query]}%","%#{params[:query]}%"])
    render :json=>{'query'=>params["query"],'suggestions'=>users.collect{|s| s.full_name.length+s.username.length > 20 ? s.full_name[0..(18-s.username.length)]+".. "+"-"+s.username : s.full_name+"-"+s.username},'data'=>users.collect(&:id)  }
  end

  def show
      @discipline_comments = @discipline_complaint.discipline_comments.all(:order=>"updated_at DESC").paginate( :page => params[:page], :per_page => 5)
      @privilege_admin=@discipline_complaint.discipline_participations.find_by_user_id(current_user.id)
      @privilege=((@privilege_admin.present?)||(current_user.admin?))
      jury_ids=@discipline_complaint.discipline_juries.collect(&:user_id)
      @close_privilege=(jury_ids.include?(current_user.id)) and (@discipline_complaint.action_taken==false)
      participation_ids=@discipline_complaint.discipline_participations.collect(&:user_id)
      @action_privillage=((((participation_ids.include?(current_user.id))||current_user.admin?)||DisciplineComplaint.is_privileged_user(current_user.id)) and (@discipline_complaint.action_taken==true))
      @discipline_attachment= @discipline_complaint.discipline_master.discipline_attachments.last
  end

  def list_comments
    @discipline_comments = @discipline_complaint.discipline_comments.all(:order=>"updated_at DESC").paginate( :page => params[:page], :per_page => 5)
    @privilege_admin=@discipline_complaint.discipline_participations.find_by_user_id(current_user.id)
    @privilege=((@privilege_admin.present?)||(current_user.admin?))
    render :update do |page|
      page.replace_html 'comments_list', :partial=>"discipline_complaints/comment"
    end
  end

  def create_comment
    if params[:comment] and params[:comment][:body]
      @discipline_complaint=DisciplineComplaint.find(params[:complaint_id])
      @privilege_admin=@discipline_complaint.discipline_participations.find_by_user_id(current_user.id)
      @privilege=((@privilege_admin.present?)||(current_user.admin?))
      @discipline_comment=@discipline_complaint.discipline_comments.build(:body=>params[:comment][:body],:user_id=>current_user.id)
      @discipline_comment.save
      @discipline_complaint.update_attributes(:updated_at=>Date.today)
      @discipline_comments = @discipline_complaint.discipline_comments.all(:order=>"updated_at DESC").paginate( :page => params[:page], :per_page => 5)
      render(:update) do |page|
        page.replace_html "comments_list",:partial=>"comment"
        page.select('form').each { |f| f.reset }
      end
    elsif params[:child_id]
      @parent=DisciplineComment.find(params[:child_id])
      @discipline_complaint=DisciplineComplaint.find(@parent.commentable_id)
      @privilege_admin=@discipline_complaint.discipline_participations.find_by_user_id(current_user.id)
      @privilege=((@privilege_admin.present?)||(current_user.admin?))
      @discipline_comment=@parent.replies.build(:body=>params[:child_comment][:body],:user_id=>current_user.id)
      @discipline_comment.save
      @discipline_complaint.update_attributes(:updated_at=>Date.today)
      @discipline_comments = @discipline_complaint.discipline_comments.all(:order=>"updated_at DESC").paginate( :page => params[:page], :per_page => 5)
      render(:update) do |page|
        page.replace_html "comments_list",:partial=>"comment"
      end
    end
  end

  def reply
    @child_comment=DisciplineComment.find(params[:id])
    render(:update) do |page|
      page.replace_html "reply#{params[:id]}",:partial=>'reply'
    end
  end

  def destroy_comment
    @comment=DisciplineComment.find(params[:id1])
    if @comment.commentable_type=="DisciplineComplaint"
      @discipline_complaint=DisciplineComplaint.find(@comment.commentable_id)
    else
      @discipline_complaint=DisciplineComplaint.find(DisciplineComment.find(@comment.commentable_id).commentable_id)
    end
    @comment.destroy
    @discipline_comments = @discipline_complaint.discipline_comments.all(:order=>"updated_at DESC").paginate( :page => params[:page], :per_page => 5)
    @privilege_admin=@discipline_complaint.discipline_participations.find_by_user_id(current_user.id)
    @privilege=((@privilege_admin.present?)||(current_user.admin?))
    render(:update) do |page|
      page.replace_html "comments_list",:partial=>"comment"
    end
  end

  def decision
    if (current_user.admin? || DisciplineComplaint.is_privileged_user(current_user.id))|| @discipline_complaint.discipline_participations.collect(&:user_id).include?(current_user.id)|| @discipline_complaint.discipline_participations.collect(&:user_id).include?(Student.find(Guardian.find_by_user_id(current_user.id).current_ward_id).user_id)
      @convicted=@discipline_complaint.discipline_accusations

      @verdict=DisciplineAction.new
    else
      redirect_to :controller=>"user" ,:action=>"dashboard"
      flash[:notice]="#{t('flash_msg4')}"
    end
    if request.post?
      @verdict=@discipline_complaint.discipline_actions.build(:body=>params[:verdict][:body],:remarks=>params[:verdict][:remarks],:user_id=>current_user.id)
      unless (params[:message_ids].blank?) or (params[:verdict][:body]).blank?
        if @verdict.save
          @discipline_complaint.update_attributes(:updated_at=>Date.today)
          (params[:message_ids]).each do |f|
            @verdict.discipline_student_actions.create(:discipline_participation_id=>f)
          end
          render(:update) do |page|
            page.replace_html "take-action",:partial=>"decision",:object=>@verdict
          end
        else
          render(:update) do |page|
            page.replace_html "error",:partial=>"error"
          end
        end
      else
        if params[:verdict][:remarks].blank?
          @verdict.errors.add_to_base("#{t('verdict_cant_blank')}")
        end
        if params[:verdict][:body].blank?
          @verdict.errors.add_to_base("#{t('comments_cant_be_blank')}")
        end
        if params[:message_ids].blank?
          @verdict.errors.add_to_base("#{t('select_atleast_one_accused')}")
        end
        render(:update) do |page|
          page.replace_html "error",:partial=>"error"
        end
      end
    end
  end

  def decision_remove
    @discipline_action=DisciplineAction.find(params[:act_id])
    @discipline_complaint=@discipline_action.discipline_complaint
    @convicted=@discipline_complaint.discipline_accusations
    @discipline_action.destroy
    render(:update) do |page|
      page.replace_html "act#{params[:act_id]}",:text=>""
    end
  end

  def decision_close
    if @discipline_complaint.discipline_actions.blank?
      flash[:notice]="#{t('flash4')}"
      redirect_to:controller=>"discipline_complaints", :action=>"decision"
    else
      @discipline_complaint.update_attributes(:action_taken=>true)
      participants_recipients=[]
      @discipline_complaint.discipline_participations.each do |participation|
        participation.update_attributes(:action_taken=>true)
        unless current_user.id==participation.user_id
          participants_recipients.push participation.user_id
        end
      end
      subject = t('complaint')
      body ="#{t('action_taken_on_complaint')} '"+@discipline_complaint.subject+"'"
      Delayed::Job.enqueue(DelayedReminderJob.new( :sender_id  => current_user.id,
          :recipient_ids => participants_recipients,
          :subject=>subject,
          :body=>body ))
      @discipline_complaint.update_attributes(:updated_at=>Date.today)
      flash[:notice]="#{t('flash5')}"
      redirect_to:controller=>"discipline_complaints", :action=>"index"
    end
  end
  private
  def complaint_present
    @discipline_complaint=DisciplineComplaint.find(params[:id])
  end
end