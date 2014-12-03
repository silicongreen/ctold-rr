class SchoolsController <  MultiSchoolController
  helper_method :admin_user_session
  helper_method :school_group_session
  before_filter :find_school, :only=>[:show,:edit,:update,:destroy,:add_domain,:delete_domain,:sms_settings,:smtp_settings,:check_smtp_settings,
    :generate_settings,:remove_settings,:remove_smtp_settings,:whitelabel_settings,:remove_whitelabel_settings,:show_sms_logs,:show_sms_messages,:profile,:domain]

  filter_access_to [:index, :new,:create]
  filter_access_to [:show,:edit,:update,:destroy,:add_domain,:delete_domain,:sms_settings,:smtp_settings,:check_smtp_settings,
    :generate_settings,:remove_settings,:remove_smtp_settings,:whitelabel_settings,:remove_whitelabel_settings,:show_sms_logs,:show_sms_messages,:profile,:domain], :attribute_check=>true

  CONN = ActiveRecord::Base.connection
  # GET /schools
  # GET /schools.xml
  def index   
    @schools = admin_user_session.school_group.schools.active.paginate(:order=>"id DESC",:page => params[:page], :per_page=>10)

    respond_to do |format|
      format.html # index.html.erb
      format.xml  { render :xml => @schools }
    end
  end

  def search
    schools =  School.find(:all,:conditions=>["((schools.name LIKE ? OR schools.code LIKE ?) AND schools.school_group_id = ? AND schools.is_deleted = ?)","#{params[:query]}%","#{params[:query]}%",admin_user_session.school_group.id,false])

    render :json=>{:query=>params[:query],:suggestions=>schools.collect(&:name), :data=>schools.collect(&:id)}
  end

  # GET /schools/1
  # GET /schools/1.xml
  def show
    @sms_settings = School.load_sms_settings
    @current_school_settings = @sms_settings[@school.code]

    respond_to do |format|
      format.html # show.html.erb
      format.xml  { render :xml => @school }
    end
  end

  def domain
    render :partial=>"domain"
  end

  def profile
    render :partial=>"profile"
  end
  
  def show_student_code
   # activation_code = StudentActivationCode.new()
    @school = School.find(params[:id])
    @student_code = StudentActivationCode.paginate(:conditions=>{:school_id=>@school.id}, :order=>"id DESC", :page => params[:page], :per_page => 10)
   
  end
  
  def download_student_code
    @school = School.find(params[:id])
    @student_code = StudentActivationCode.find(:all,:conditions=>{:school_id=>@school.id})
    csv_string=FasterCSV.generate do |csv|
      cols=["Activation Code","Status"]
      csv << cols
      @student_code.each_with_index do |b,i|
        col=[]
        col<< "#{b.code}"
        col<< "#{b.is_active}"
        
        csv<< col
      end
    end
    filename = "#{@school.name}-student-code-#{Time.now.to_date.to_s}.csv"
    send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
  end
  
  def add_student
    @school = School.find(params[:id], :conditions=>{:is_deleted=>false})   
    if request.post?
      inserts = []
      number_of_student = params[:student][:number_of_student].to_i
      number_of_student.times do |i|
        randstr = @school.code.to_s+"-"+i.to_s+"-"
        chars = ("0".."9").to_a + ("a".."z").to_a + ("A".."Z").to_a
        20.times { randstr << chars[rand(chars.size - 1)] }
        inserts.push "(#{@school.id},'#{randstr}')"
      end
      sql = "insert into student_activation_codes (`school_id`,`code`) VALUES #{inserts.join(", ")}"
      CONN.execute sql
      
      flash[:notice]="Student added for  #{@school.name}"
    end
   
  end

  # GET /schools/new
  # GET /schools/new.xml
  def new
    
    @school_group = admin_user_session.school_group
    @school = @school_group.schools.build
    @school.school_domains.build
    @school.build_available_plugin(:plugins=>[])
    respond_to do |format|
      format.html # new.html.erb
      format.xml  { render :xml => @school }
    end
  end

  # GET /schools/1/edit
  def edit
    @school.build_available_plugin(:plugins=>[]) unless @school.available_plugin
  end

  # POST /schools
  # POST /schools.xml
  def create
    require 'net/http'

    url = 'http://hook.champs21.com/cp1.php?subdomain='+params[:school][:code]
    resp = Net::HTTP.get_response(URI.parse(url)) # get_response takes an URI object

    @school_group = admin_user_session.school_group
    @school = @school_group.schools.build(params[:school])
    @school.creator_id = admin_user_session.id
    
    respond_to do |format|
      @school.build_available_plugin(:plugins=>[]) unless @school.available_plugin
      if @school.save
        Configuration.find_or_create_by_config_key("InstitutionAddress").update_attributes(:config_value=>params[:institution][:institution_address])
        Configuration.find_or_create_by_config_key("InstitutionPhoneNo").update_attributes(:config_value=>params[:institution][:institution_phone_no])
        flash[:notice] = "<span style='color: black;'>School was successfully created.<span><br /> <span style='color:#666666 !important;'>You can access this school at <a href='http://#{@school.school_domains.first.try(:domain)}' style='color:#990000 !important;font-weight: normal;' target='_blank'>#{@school.school_domains.first.try(:domain)}</a>&nbsp; &nbsp; Username: <b style='font-weight: normal;color: black;'>"+params[:school][:code]+"-admin</b>&nbsp;&nbsp Password: <b style='font-weight: normal;color: black;'>123456</b></span>"
        format.html { redirect_to(@school) }
        format.xml  { render :xml => @school, :status => :created, :location => @school }
      else
        format.html { render :action => "new" }
        format.xml  { render :xml => @school.errors, :status => :unprocessable_entity }
      end
    end
  end

  # PUT /schools/1
  # PUT /schools/1.xml
  def update
    MultiSchool.current_school = @school
    respond_to do |format|
      params[:school][:available_plugin_attributes]={:plugins=>[]} unless params[:school][:available_plugin_attributes]
      if @school.update_attributes(params[:school])
        flash[:notice] = 'School was successfully updated.'
        format.html { redirect_to(@school) }
        format.xml  { head :ok }
      else
        format.html { render :action => "edit" }
        format.xml  { render :xml => @school.errors, :status => :unprocessable_entity }
      end
    end
  end

  # DELETE /schools/1
  # DELETE /schools/1.xml
  def destroy
    MultiSchool.current_school = @school
    @school.soft_delete
    flash[:notice]="School deleted successfully"
    respond_to do |format|
      format.html { redirect_to(schools_url) }
      format.xml  { head :ok }
    end
  end

  def add_domain
    @domain = @school.school_domains.new(params[:add_domain])
    render :update do |page|
      if @domain.save
        message="Domain added."
        page.insert_html :bottom, 'domains', :partial=>"added_domain"       
      else
        message = "Unable to add domain - #{@domain.errors.full_messages.join(',')}"
      end
      page.replace_html 'message_div', message
    end
  end

  def delete_domain
    domain = @school.school_domains.find_by_id(params[:domain_id])
    destroyed = domain.destroy if domain
    @message = (destroyed ?  "Domain deleted" : "Could not delete domain - #{@domain.errors.full_messages.join(',')}")
    render :partial=>'domain'
  end

  def sms_settings
    @current_school_settings = @school.sms_credential
    if request.post?
      unless @current_school_settings.nil?
        @current_school_settings.update_attributes(:settings=>params[:sms_config])
        flash[:notice]="SMS settings for #{@school.name} have been updated successfully"
      else
        @school.create_sms_credential(:settings=>params[:sms_config])
        flash[:notice]="SMS settings for #{@school.name} have been created successfully"
      end
      redirect_to :action=>"sms_settings", :id=>@school.id
    end

  end

  def smtp_settings
    @current_school_settings = @school.smtp_setting
    if request.post?
      unless @current_school_settings.nil?
        @current_school_settings.update_attributes(:settings=>params[:smtp_config])
        flash[:notice]="SMTP settings for #{@school.name} have been updated successfully"
      else
        @school.create_smtp_setting(:settings=>params[:smtp_config])
        flash[:notice]="SMTP settings for #{@school.name} have been created successfully"
      end
      redirect_to :action=>"smtp_settings", :id=>@school.id
    end
  end

  def whitelabel_settings
    @current_school_settings = @school.whitelabel_setting
    if request.post?
      unless @current_school_settings.nil?
        @current_school_settings.update_attributes(:settings=>params[:whitelabel_config])
        flash[:notice]="Whitelabel settings for #{@school.name} have been updated successfully"
      else
        @school.create_whitelabel_setting(:settings=>params[:whitelabel_config])
        flash[:notice]="Whitelabel settings for #{@school.name} have been created successfully"
      end
      redirect_to :action=>"whitelabel_settings", :id=>@school.id
    end
  end

  def remove_whitelabel_settings
    whitelabel_settings = @school.whitelabel_setting
    unless whitelabel_settings.nil?
      whitelabel_settings.destroy
    end
    redirect_to :action=>:whitelabel_settings, :id=>@school.id
  end

  def generate_settings
    @sms_settings = School.load_sms_settings
    @current_school_settings = @sms_settings[@school.code]
    @school.create_sms_settings if @current_school_settings.nil?
    redirect_to :action=>:sms_settings, :id=>@school.id
  end

  def remove_settings
    sms_settings = @school.sms_credential
    unless sms_settings.nil?
      sms_settings.destroy
    end
    redirect_to :action=>:sms_settings, :id=>@school.id
  end

  def remove_smtp_settings
    smtp_settings = @school.smtp_setting
    unless smtp_settings.nil?
      smtp_settings.destroy
    end
    redirect_to :action=>:smtp_settings, :id=>@school.id
  end

  def show_sms_messages
    MultiSchool.current_school = @school
    @sms_messages = SmsMessage.paginate(:conditions=>{:school_id=>@school.id}, :order=>"id DESC", :page => params[:page], :per_page => 30)
    @total_sms = Configuration.get_config_value("TotalSmsCount")
  end

  def show_sms_logs
    MultiSchool.current_school = @school
    @sms_message = SmsMessage.find(params[:id2])
    @sms_logs = @sms_message.sms_logs.paginate( :order=>"id DESC", :page => params[:page], :per_page => 30)
  end

  def list_schools
    if params[:multi_school_group_id]
      @multi_school_group = SchoolGroup.find(params[:multi_school_group_id])
      @schools = School.paginate(:all,:conditions=>{:school_group_id=>@multi_school_group.id,:is_deleted=>false},:page => params[:page], :per_page=>10)
    end
    render "admin_users/schools_list"
  end

  private

  def find_school
    @school = School.find(params[:id], :conditions=>{:is_deleted=>false})
    @school_group = @school.school_group
  end

  def get_url(school_id)
    MultiSchool.current_school = School.find school_id
    config = SmsSetting.get_sms_config
    unless config.blank?
      sendername = config['sms_settings']['sendername']
      sms_url = config['sms_settings']['host_url']
      username = config['sms_settings']['username']
      password = config['sms_settings']['password']
      success_code = config['sms_settings']['success_code']
      username_mapping = config['parameter_mappings']['username']
      username_mapping ||= 'username'
      password_mapping = config['parameter_mappings']['password']
      password_mapping ||= 'password'
      phone_mapping = config['parameter_mappings']['phone']
      phone_mapping ||= 'phone'
      sender_mapping = config['parameter_mappings']['sendername']
      sender_mapping ||= 'sendername'
      message_mapping = config['parameter_mappings']['message']
      message_mapping ||= 'message'
      encoded_message = URI.encode("your message")
      unless config['additional_parameters'].blank?
        additional_param = ""
        config['additional_parameters'].split(',').each do |param|
          additional_param += "&#{param}"
        end
      end
    end
    "#{sms_url}?#{username_mapping}=#{username}&#{password_mapping}=#{password}&#{sender_mapping}=#{sendername}&#{message_mapping}=#{encoded_message}#{additional_param}&#{phone_mapping}="
  end
end
