require 'fileutils'
include FileUtils

class SchoolsController <  MultiSchoolController
  helper_method :admin_user_session
  helper_method :school_group_session
  before_filter :find_school, :only=>[:show,:edit,:update,:destroy,:add_domain,:delete_domain,:sms_settings,:smtp_settings,:check_smtp_settings,
    :generate_settings,:remove_settings,:remove_smtp_settings,:whitelabel_settings,:remove_whitelabel_settings,:show_sms_logs,:show_sms_messages,:profile,:domain]

  protect_from_forgery :except => [:create_school]
  before_filter :require_admin_session, :except => [:create_school]
  
  filter_access_to [:index, :new,:create,:send_notification_all]
  filter_access_to [:show,:edit,:update, :destroy,:add_domain,:delete_domain,:sms_settings,:smtp_settings,:check_smtp_settings,
    :generate_settings,:remove_settings,:remove_smtp_settings,:whitelabel_settings,:remove_whitelabel_settings,:show_sms_logs,:show_sms_messages,:profile,:domain], :attribute_check=>true

  CONN = ActiveRecord::Base.connection
  # GET /schools
  # GET /schools.xml
  
  def create_school
    require 'net/http'
    
    errors = {}
    
    if params[:token].nil? or params[:institution].nil? or params[:school].nil?
      errors['invalid_request'] = 'Invalid Request'
      respond_to do |format| format.json  { render :json => errors, :status => 400 } end
      return
    end
    
    token = Token.find(:last, :conditions => ["token = ? AND token_domain = ? AND token_purpose = ? AND status = ?", params[:token][:token], params[:token][:token_domain], params[:token][:token_purpose], params[:token][:status]])
    
    unless token.nil?
      if token.expire_at < Time.now
        errors['invalid_token'] = 'Invalid Token'
        respond_to do |format| format.json  { render :json => errors, :status => 403 } end
        return
      else
        token.status = 0
        token.save
      end
    else
      errors['invalid_token'] = 'Invalid Token'
      respond_to do |format| format.json  { render :json => errors, :status => 403 } end
      return
    end
    
    @school_group = SchoolGroup.find(2, :conditions=>{:is_deleted=>false})
    
    @school = @school_group.schools.build(params[:school])
    @school.creator_id = 2
    
    ##
    # UPLOAD Batch Excel File
    ##
    o = [('a'..'z'), ('A'..'Z'), (0..9)].map { |i| i.to_a }.flatten
    rand_val = (0...16).map { o[rand(o.length)] }.join
    directory = "public/uploads/seeds"
    b_checked = false
    
    unless params[:school][:import].nil? or params[:school][:import].empty?
      b_checked = true
      
      if params[:school][:import].is_a?(Hash)
        params[:school][:import] = params[:school][:import].values
      elsif params[:school][:import].is_a?(Array)
        params[:school][:import] = params[:school][:import]
      else
        params[:school][:import] = params[:school][:import].split(',').map{|m| m.to_s.strip}
      end
      
    end
    
    if b_checked and params[:school][:import].include?('default_class_seeds')
      @school.import_class_seed = true
    else
      unless @school.class_file.blank?
        params[:school][:import_class_seed] = true
        ext = File.extname(@school.class_file.original_filename);
        basename = File.basename(@school.class_file.original_filename, ext)
        name =  basename + "-" + rand_val + ext
        # create the file path
        path = File.join(directory, name)
        @school.class_file_name = path
        @school.import_class_seed = true
        # write the file
        File.open(path, "wb") { |f| f.write(@school.class_file.read) }
        FileUtils.chmod 0777, path, :verbose => true
      else
        @school.class_file_name = ""
        @school.import_class_seed = false
      end
    end
    
    ##
    # END Upload Batch File
    ##
    
    ##
    # UPLOAD Employee Category Excel File
    ##
    if b_checked and params[:school][:import].include?('default_emp_category_seeds')
      @school.import_cate_seeds = true
    else
      unless @school.emp_cat_file.blank?
        ext = File.extname(@school.emp_cat_file.original_filename);
        basename = File.basename(@school.emp_cat_file.original_filename, ext)
        name =  basename + "-" + rand_val + ext
        # create the file path
        path = File.join(directory, name)
        @school.emp_cat_file_name = path
        @school.import_cate_seeds = true
        # write the file
        File.open(path, "wb") { |f| f.write(@school.emp_cat_file.read) }
        FileUtils.chmod 0777, path, :verbose => true
      else
        @school.emp_cat_file_name = ""
        @school.import_cate_seeds = false
      end
    end  
    ##
    # END Upload Employee Category File
    ##
    
    ##
    # UPLOAD Employee Department Excel File
    ##
    if b_checked and params[:school][:import].include?('default_emp_dept_seeds')
      @school.import_dept_seed = true
    else
      unless @school.emp_dept_file.blank?
        ext = File.extname(@school.emp_dept_file.original_filename);
        basename = File.basename(@school.emp_dept_file.original_filename, ext)
        name =  basename + "-" + rand_val + ext
        # create the file path
        path = File.join(directory, name)
        @school.emp_dept_file_name = path
        @school.import_dept_seed = true
        # write the file
        File.open(path, "wb") { |f| f.write(@school.emp_dept_file.read) }
        FileUtils.chmod 0777, path, :verbose => true
      else
        @school.emp_dept_file_name = ""
        @school.import_dept_seed = false
      end
    end
    ##
    # END Upload Employee Department File
    ##
    
    ##
    # UPLOAD Employee Grade Excel File
    ##
    if b_checked and params[:school][:import].include?('default_emp_grade_seeds')
      @school.import_emp_grade = true
    else
      unless @school.emp_grade_file.blank?
        ext = File.extname(@school.emp_grade_file.original_filename);
        basename = File.basename(@school.emp_grade_file.original_filename, ext)
        name =  basename + "-" + rand_val + ext
        # create the file path
        path = File.join(directory, name)
        @school.emp_grade_file_name = path
        @school.import_emp_grade = true
        # write the file
        File.open(path, "wb") { |f| f.write(@school.emp_grade_file.read) }
        FileUtils.chmod 0777, path, :verbose => true
      else
        @school.emp_grade_file_name = ""
        @school.import_emp_grade = false
      end
    end
    ##
    # END Upload Employee Grade File
    ##
    
    ##
    # UPLOAD Exam Grade Excel File
    ##
    if b_checked and params[:school][:import].include?('default_exam_grade_seeds')
      @school.import_exam_grade = true
    else
      unless @school.exam_grade_file.blank?
        ext = File.extname(@school.exam_grade_file.original_filename);
        basename = File.basename(@school.exam_grade_file.original_filename, ext)
        name =  basename + "-" + rand_val + ext
        # create the file path
        path = File.join(directory, name)
        @school.exam_grade_file_name = path
        @school.import_exam_grade = true
        # write the file
        File.open(path, "wb") { |f| f.write(@school.exam_grade_file.read) }
        FileUtils.chmod 0777, path, :verbose => true
      else
        @school.exam_grade_file_name = ""
        @school.import_exam_grade = false
      end
    end
    ##
    # END Upload Exam Grade File
    ##
    
    unless params[:menu_data].nil? or params[:menu_data].blank?
      if params[:menu_data].is_a?(Hash)
        params[:menu_data] = params[:menu_data].values
      end
      if params[:menu_data].is_a?(Array)
        params[:menu_data] = params[:menu_data]
      end
      if params[:menu_data].is_a?(String)
        params[:menu_data] = params[:menu_data].split(',').map{|m| m.to_i}
      end
      @school.menu_datas = params[:menu_data]
    else
      @school.menu_datas = ""
    end  
    
    @school.build_available_plugin(:plugins=>[])
    unless params[:package].nil? or params[:package].blank?
      params[:package] = params[:package].values
      @school.package = params[:package]
      @packages_plugins = PackageMenu.find(:all, :conditions => ["package_id = ? and menu_id = 0",params[:package]], :select => "plugins_name")
      @school.available_plugin.plugins = @packages_plugins.map{|pp| pp.plugins_name}
    else
      @school.package = []
      @settings_plugin = YAML.load_file(File.dirname(__FILE__)+"/../../config/plugins.yml")
      @required_plugins = @settings_plugin["required_plugins"]
        
      @school.available_plugin = AvailablePlugin.new()
      @school.available_plugin.plugins = @required_plugins
    end
    
    #    @school.build_available_plugin(:plugins=>[]) unless @school.available_plugin
    #    abort @school.available_plugin.inspect  
    unless params[:school][:code].nil? or params[:school][:code].empty?
      params[:school][:code] = params[:school][:code].downcase
    end
      
    @free_feed_for_admin_param = "0"
    @free_feed_for_teacher_param = "0"
    @free_feed_for_student_param = "0"
    unless params[:free_feed].nil? or params[:free_feed].empty?
      params[:free_feed].each do |free_feed|
        if free_feed == 'free_feed_for_admin'
          @free_feed_for_admin_param = "1"
        elsif free_feed == 'free_feed_for_teacher'
          @free_feed_for_teacher_param = "1"
        elsif free_feed == 'free_feed_for_student'
          @free_feed_for_student_param = "1"
        end
      end
    end
      
    domains = ['api','plus','hook','cp-api','stage','market','weblogin']
      
    @validated = true
    
    school_code = School.find(:last, :conditions => ["code = ? ", params[:school][:code]])
    
    unless school_code.nil?
      if school_code.code == params[:school][:code]
        @validated = false
        errors['school_code'] = 'School Code Unavailable'
        respond_to do |format| format.json  { render :json => errors, :status => 406 } end
        return
      end
    end
    
    if domains.include?(params[:school][:code])
      @validated = false
      errors['school_code'] = 'School Code Unavailable'
      respond_to do |format| format.json  { render :json => errors, :status => 406 } end
      return
    end
      
    @school.activation_code = 100000 + rand(900000)
    @school.is_deleted = 0
    if @validated and @school.save
      
      Configuration.find_or_create_by_config_key("InstitutionAddress").update_attributes(:config_value=>params[:institution][:institution_address])
      Configuration.find_or_create_by_config_key("InstitutionPhoneNo").update_attributes(:config_value=>params[:institution][:institution_phone_no])
        
      Configuration.find_or_create_by_config_key("FreeFeedForAdmin").update_attributes(:config_value=>0)
      Configuration.find_or_create_by_config_key("FreeFeedForTeacher").update_attributes(:config_value=>0)
      Configuration.find_or_create_by_config_key("FreeFeedForStudent").update_attributes(:config_value=>0)
        
      Configuration.find_or_create_by_config_key("PaletteSetting").update_attributes(:config_value=>params[:palette_setting])
        
      unless params[:free_feed].nil? or params[:free_feed].empty?
        params[:free_feed].each do |free_feed|
          if free_feed == 'free_feed_for_admin'
            Configuration.find_or_create_by_config_key("FreeFeedForAdmin").update_attributes(:config_value=>1)
          elsif free_feed == 'free_feed_for_teacher'
            Configuration.find_or_create_by_config_key("FreeFeedForTeacher").update_attributes(:config_value=>1)
          elsif free_feed == 'free_feed_for_student'
            Configuration.find_or_create_by_config_key("FreeFeedForStudent").update_attributes(:config_value=>1)
          end
        end
      end
      
      respond_to do |format| format.json  { render :json => @school, :status => 200 } end
      return
    end
    
    return
  end
  

  
  def index 
    @schools = admin_user_session.school_group.schools.active.paginate(:order=>"name ASC",:page => params[:page], :per_page=>10)

    respond_to do |format|
      format.html # index.html.erb
      format.xml  { render :xml => @schools }
    end
  end
  
  def test_schools  
    
    @schools = admin_user_session.school_group.schools.active.is_test_school.paginate(:order=>"name ASC",:page => params[:page], :per_page=>10)

    respond_to do |format|
      format.html # test_schools.erb
      format.xml  { render :xml => @schools }
    end
  end
  
  def running_schools  
   
    @schools = admin_user_session.school_group.schools.active.is_running_school.paginate(:order=>"name ASC",:page => params[:page], :per_page=>10)

    respond_to do |format|
      format.html # running_schools.erb
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
    
    MultiSchool.current_school = @school
    @institution_address = Configuration.get_config_value("InstitutionAddress")
    @institution_phone_no = Configuration.get_config_value("InstitutionPhoneNo")
    
    @is_test_school = @school.is_test_school
    
    @free_feed_for_admin = Configuration.get_config_value("FreeFeedForAdmin")
    @free_feed_for_teacher = Configuration.get_config_value("FreeFeedForTeacher")
    @free_feed_for_student = Configuration.get_config_value("FreeFeedForStudent")
    
    @palette_setting = Configuration.get_config_value("PaletteSetting")
    
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

  def show_students_list
    @school = School.find(params[:id], :conditions=>{:is_deleted=>false})
    #sql = "SELECT sg.*
    #FROM
    #students_guardians AS sg
    #WHERE sg.school_id = '#{@school.id}'
    #GROUP BY sg.student_id
    #ORDER BY sg.id ASC"
    #     @student_data = StudentsGuardians.paginate_by_sql(sql, :page => params[:page], :per_page => 30)
    #     @total_student = Student.active.count
    
    #current_page = current_page || 1
    #per_page = 10
    #records_fetch_point = (current_page - 1) * per_page
    
    @conn = ActiveRecord::Base.connection 
    sql = "SELECT s.`id` as student_id,s.`admission_no`  ,s.`first_name`,s.`middle_name`,s.`last_name`,s.`immediate_contact_id`,s.`school_id`,
              fu.paid_username,fu.paid_password FROM 
              students as s left join tds_free_users as fu on s.user_id=fu.paid_id where fu.paid_school_id=#{@school.id}"


    #@student_data = @conn.execute(sql).all_hashes
    @student_data = StudentsGuardians.paginate_by_sql(sql, :page => params[:page], :per_page => 50)
    #put @student_data.inspect
    #abort(@student_data.inspect)
    #@total_student = @student_data.count
    sql2 = "Select id FROM students where is_active = true and is_deleted = false"
    @total_student = @conn.execute(sql2).all_hashes
  end
  
  #  def download_student_list
  #    @school = School.find(params[:id])
  #    @student_data = StudentsGuardians.find(:all,:conditions=>{:school_id=>@school.id})
  #    csv_string=FasterCSV.generate do |csv|
  #      cols=["Admission No.","Batch","Student","Student Username","Student Password","Guardian","Guardian Username","Guardian Password","Guardian Phone","Guardian2","Guardian2 Username","Guardian2 Password","Guardian2 Phone","Guardian3","Guardian3 Username","Guardian3 Password","Guardian3 Phone"]
  #      csv << cols
  #      @student_data.each_with_index do |b,i|
  #        @student = Student.find_by_user_id(b.student_id)  
  #        unless @student.nil?
  #          @batch = Batch.find_by_id(@student.batch_id)
  #          @course = Course.find_by_id(@batch.course_id)
  #        else
  #          @batch = nil          
  #        end
  #        batch_str = ""
  #        unless @batch.nil?
  #          if @batch.name != "" and @batch.name != "General"
  #            batch_str = batch_str + "Shift:" + @batch.name+" | "
  #          end
  #          if @course.course_name != ""
  #            batch_str = batch_str + "Class:" + @course.course_name
  #          end
  #          if @course.section_name != ""
  #            batch_str = batch_str + " | " + "Section:" + @course.section_name
  #          end
  #        end
  #        s_name = b.s_first_name + " " + b.s_last_name
  #        if b.g_first_name.nil? || b.g_last_name.nil?
  #          g_name = "N/A"
  #        else
  #          g_name = b.g_first_name + " " + b.g_last_name
  #        end
  #        
  #        @conn = ActiveRecord::Base.connection 
  #        sql = "SELECT g.`first_name`,g.`last_name`,g.office_phone1,
  #                    g.relation,fu.paid_username,fu.paid_password FROM 
  #                    guardians as g left join tds_free_users as fu on g.user_id=fu.paid_id where g.ward_id=#{@student.id} and fu.paid_school_id=#{@school.id}#"
  #
  #        guardian_data = @conn.execute(sql).all_hashes
  #        
  #        col=[]
  #        col<< "#{b.admission_no}"
  #        col<< "#{batch_str}"
  #        col<< "#{s_name}"
  #        col<< "#{b.s_username}"
  #        col<< "#{b.s_password}"
  #        if guardian_data.count>1
  #          guardian_data.each_with_index do |glist,i|
  #            col<< "#{glist['first_name']} #{glist['last_name']}"
  #            col<< "#{glist['paid_username']}"
  #            col<< "#{glist['paid_password']}"
  #            col<< "#{glist['office_phone1']}"
  #          end
  #        else
  #          col<< "#{g_name}"
  #          col<< "#{b.g_username}"
  #          col<< "#{b.g_password}"
  #          col<< "#{b.g_phone}"
  #        end
  #        
  #        csv<< col
  #      end
  #    end
  #    filename = "#{@school.name}-student-list-#{Time.now.to_date.to_s}.csv"
  #    send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
  #  end
  
  def download_student_list
    @school = School.find(params[:id])
    #@student_data = StudentsGuardians.find(:all,:conditions=>{:school_id=>@school.id})
    @conn = ActiveRecord::Base.connection 
    sql = "SELECT s.`id` as student_id,s.`admission_no`  ,s.`first_name`,s.`middle_name`,s.`last_name`,s.`immediate_contact_id`,s.`school_id`,
              fu.paid_username,fu.paid_password FROM 
              students as s left join tds_free_users as fu on s.user_id=fu.paid_id where fu.paid_school_id=#{@school.id}"


    @student_data = @conn.execute(sql).all_hashes
    
    csv_string = FasterCSV.generate do |csv|
      
      #      csv << headers
        
      @student_data.each_with_index do |b,i|
        
        sch = School.find(b['school_id'])
        MultiSchool.current_school = sch
        
        @student = Student.find(b['student_id'])  
        
        #        @student = Student.find(:conditions => [ "user_id = ?", b.student_id])
        #        @student = Student.find_by_user_id(b.student_id)  
        
        unless @student.nil?
          #          @batch = Batch.find_by_id(@student[0].batch_id)
          @batch = Batch.find_by_sql ["SELECT * FROM batches WHERE id = ?", @student.batch_id]
          #@course = Course.find_by_id(@batch.course_id)
          @course = Course.find_by_sql ["SELECT * FROM courses WHERE id = ?", @batch[0].course_id]
      
        
          batch_str = ""
          unless @batch[0].nil?
            if @batch[0].name != "" and @batch[0].name != "General"
              batch_str = batch_str + "Shift:" + @batch[0].name+" | "
            end
            if @course[0].course_name != ""
              batch_str = batch_str + "Class:" + @course[0].course_name
            end
            if @course[0].section_name != ""
              batch_str = batch_str + " | " + "Section:" + @course[0].section_name
            end
          end
        
          @conn = ActiveRecord::Base.connection 
          #sql = "SELECT g.`first_name`,g.`last_name`,g.office_phone1,g.mobile_phone,
          ##          g.relation,fu.paid_username,fu.paid_password FROM 
          #          guardians as g left join tds_free_users as fu on g.user_id=fu.paid_id where g.ward_id=#{@student.id} and fu.paid_school_id=#{@school.id}"
          #
          sql = "SELECT g.`first_name`,g.`last_name`,g.office_phone1,g.mobile_phone,
g.relation,fu.paid_username,fu.paid_password FROM 
guardians as g left join tds_free_users as fu on g.user_id=fu.paid_id left join guardian_students as gs on g.id=gs.guardian_id where gs.student_id=#{@student.id} and fu.paid_school_id=#{@school.id}"
          
          guardian_data = @conn.execute(sql).all_hashes
          #abort(guardian_data.inspect)
          rows = []
          rows << "#{@school.name}"
          csv << rows
        
          rows = []
          rows << "Admission No."
          rows << "#{b['admission_no']}"
          csv << rows
        
          rows = []
          rows << "Batch"
          rows << "#{batch_str}"
          csv << rows
        
          rows = []
          rows << "Student"
          rows << "#{b['first_name']} #{b['last_name']}"
          csv << rows
        
          rows = []
          rows << "Student Username"
          rows << "#{b['paid_username']}"
          csv << rows
        
          rows = []
          rows << "Student Password"
          rows << "#{b['paid_password']}"
          csv << rows
        
          if guardian_data.count > 1
            guardian_data.each_with_index do |glist,i|
            
              j = i+1
              gPhone = ""
              if !glist['office_phone1'].blank?
                gPhone = gPhone + glist['office_phone1']
              end
              if !glist['mobile_phone'].blank?
                if !glist['office_phone1'].blank?
                  gPhone = gPhone + " | " + glist['mobile_phone']
                else  
                  gPhone = gPhone + glist['mobile_phone']
                end
              end
            
              rows = []
              rows << "Guardian" + j.to_s
              rows << "#{glist['first_name']} #{glist['last_name']}"
              csv << rows
            
              rows = []
              rows << "Guardian"+j.to_s+" Username"
              rows << "#{glist['paid_username']}"
              csv << rows
            
              rows = []
              rows << "Guardian"+j.to_s+" Password"
              rows << "#{glist['paid_password']}"
              csv << rows
            
              rows = []
              rows << "Guardian"+j.to_s+" Phone"
              rows << "#{gPhone}"
              csv << rows
            
            end
          else
            guardian_data.each_with_index do |glist,i|
              gPhone = ""
              if !glist['office_phone1'].blank?
                gPhone = gPhone + glist['office_phone1']
              end
              if !glist['mobile_phone'].blank?
                if !glist['office_phone1'].blank?
                  gPhone = gPhone + " | " + glist['mobile_phone']
                else  
                  gPhone = gPhone + glist['mobile_phone']
                end
              end
              
              rows = []
              rows << "Guardian"
              rows << "#{glist['first_name']} #{glist['last_name']}"
              csv << rows

              rows = []
              rows << "Guardian Username"
              rows << "#{glist['paid_username']}"
              csv << rows

              rows = []
              rows << "Guardian Password"
              rows << "#{glist['paid_password']}"
              csv << rows

              rows = []
              rows << "Guardian Phone"
              rows << "#{gPhone}"
              csv << rows
            end
          end
        
          rows = []
          csv << rows
        end
      end
      
      
    end
    
    filename = "#{@school.name}-student-list-#{Time.now.to_date.to_s}.csv"
    send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
  end
  
  def show_teacher_list
    @school = School.find(params[:id], :conditions=>{:is_deleted=>false}) 
    #@teacher_data = TeacherLog.paginate(:conditions=>{:school_id=>@school.id}, :order=>"id ASC", :page => params[:page], :per_page => 30)
    
    
    @conn = ActiveRecord::Base.connection 
    sql = "SELECT e.`employee_number`,e.`first_name`,e.`middle_name`,e.`last_name`,e.gender,
                    fu.paid_username,fu.paid_password FROM 
                    employees as e left join tds_free_users as fu on e.user_id=fu.paid_id   left join users as u on e.user_id=u.id where fu.paid_school_id=#{@school.id} and u.employee = 1 order by e.id"
        
    @teacher_data = @conn.execute(sql).all_hashes
    
    #abort(@teacher_data.inspect)
    
  end
  
  def download_teacher_list
    @school = School.find(params[:id])
    #@teacher_data = TeacherLog.find(:all,:conditions=>{:school_id=>@school.id})
    @conn = ActiveRecord::Base.connection 
    sql = "SELECT e.`employee_number`,e.`first_name`,e.`middle_name`,e.`last_name`,e.gender,
          fu.paid_username,fu.paid_password FROM 
          employees as e left join tds_free_users as fu on e.user_id=fu.paid_id   left join users as u on e.user_id=u.id  where fu.paid_school_id=#{@school.id} and u.employee = 1 order by e.id"
        
    @teacher_data = @conn.execute(sql).all_hashes
    
    csv_string=FasterCSV.generate do |csv|
      cols=["Employee No.","Gender","First Name","Middle Name","Last Name","User Name","Password"]
      csv << cols
      @teacher_data.each_with_index do |b,i|
        col=[]
        col<< "#{b['employee_number']}"
        col<< "#{b['gender']}"
        col<< "#{b['first_name']}"
        col<< "#{b['middle_name']}"
        col<< "#{b['last_name']}"
        col<< "#{b['paid_username']}"
        col<< "#{b['paid_password']}"
        
        csv<< col
      end
    end
    filename = "#{@school.name}-teacher-list-#{Time.now.to_date.to_s}.csv"
    send_data(csv_string, :type => 'text/csv; charset=utf-8; header=present', :filename => filename)
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
        10.times { randstr << chars[rand(chars.size - 1)] }
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
    @packages = Package.find(:all)
    
    @import = ['default_class_seeds','default_emp_category_seeds','default_emp_dept_seeds','default_emp_grade_seeds','default_exam_grade_seeds']
    @assign = ['create_new_n_assign']
    @package_id = []
    @menu_datas = ""
    @institutional_address = ""
    @institutional_phone_no = ""
    @free_feed_for_admin = "0"
    @free_feed_for_teacher = "0"
    @free_feed_for_student = "1"
    @is_test_school = 1    
    
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
    @packages = Package.find(:all)
    
    @menu_datas = ""
    @school.build_available_plugin(:plugins=>[]) unless @school.available_plugin
    MultiSchool.current_school = @school
    @institution_address = Configuration.get_config_value("InstitutionAddress")
    @institution_phone_no = Configuration.get_config_value("InstitutionPhoneNo")
    
    @free_feed_for_admin = Configuration.get_config_value("FreeFeedForAdmin")
    @free_feed_for_teacher = Configuration.get_config_value("FreeFeedForTeacher")
    @free_feed_for_student = Configuration.get_config_value("FreeFeedForStudent")
    @palette_setting = Configuration.get_config_value("PaletteSetting")
    
    @is_test_school = @school.is_test_school
    
    @package_id = SchoolPackage.find(:all,:conditions => ["school_id = ?",@school.id],:select => "package_id").map{|sp| sp.package_id}
    @menu_datas = SchoolMenuLink.find(:all,:conditions => ["school_id = ?",@school.id],:select => "menu_link_id").map{|sml| sml.menu_link_id}.join(',')
  end

  # POST /schools
  # POST /schools.xml
  def create
    require 'net/http'
    
    @school_group = admin_user_session.school_group
    @school = @school_group.schools.build(params[:school])
    @school.creator_id = admin_user_session.id
    #    abort params.inspect
    ##
    # UPLOAD Batch Excel File
    ##
    o = [('a'..'z'), ('A'..'Z'), (0..9)].map { |i| i.to_a }.flatten
    rand_val = (0...16).map { o[rand(o.length)] }.join
    directory = "public/uploads/seeds"
    b_checked = false
    unless params[:school][:import].nil? or params[:school][:import].empty?
      b_checked = true 
    end
    if b_checked and params[:school][:import].include?('default_class_seeds')
      @school.import_class_seed = true
    else
      unless @school.class_file.blank?
        params[:school][:import_class_seed] = true
        ext = File.extname(@school.class_file.original_filename);
        basename = File.basename(@school.class_file.original_filename, ext)
        name =  basename + "-" + rand_val + ext
        # create the file path
        path = File.join(directory, name)
        @school.class_file_name = path
        @school.import_class_seed = true
        # write the file
        File.open(path, "wb") { |f| f.write(@school.class_file.read) }
        FileUtils.chmod 0777, path, :verbose => true
      else
        @school.class_file_name = ""
        @school.import_class_seed = false
      end
    end
    
    ##
    # END Upload Batch File
    ##
    
    ##
    # UPLOAD Employee Category Excel File
    ##
    if b_checked and params[:school][:import].include?('default_emp_category_seeds')
      @school.import_cate_seeds = true
    else
      unless @school.emp_cat_file.blank?
        ext = File.extname(@school.emp_cat_file.original_filename);
        basename = File.basename(@school.emp_cat_file.original_filename, ext)
        name =  basename + "-" + rand_val + ext
        # create the file path
        path = File.join(directory, name)
        @school.emp_cat_file_name = path
        @school.import_cate_seeds = true
        # write the file
        File.open(path, "wb") { |f| f.write(@school.emp_cat_file.read) }
        FileUtils.chmod 0777, path, :verbose => true
      else
        @school.emp_cat_file_name = ""
        @school.import_cate_seeds = false
      end
    end  
    ##
    # END Upload Employee Category File
    ##
    
    ##
    # UPLOAD Employee Department Excel File
    ##
    if b_checked and params[:school][:import].include?('default_emp_dept_seeds')
      @school.import_dept_seed = true
    else
      unless @school.emp_dept_file.blank?
        ext = File.extname(@school.emp_dept_file.original_filename);
        basename = File.basename(@school.emp_dept_file.original_filename, ext)
        name =  basename + "-" + rand_val + ext
        # create the file path
        path = File.join(directory, name)
        @school.emp_dept_file_name = path
        @school.import_dept_seed = true
        # write the file
        File.open(path, "wb") { |f| f.write(@school.emp_dept_file.read) }
        FileUtils.chmod 0777, path, :verbose => true
      else
        @school.emp_dept_file_name = ""
        @school.import_dept_seed = false
      end
    end
    ##
    # END Upload Employee Department File
    ##
    
    ##
    # UPLOAD Employee Grade Excel File
    ##
    if b_checked and params[:school][:import].include?('default_emp_grade_seeds')
      @school.import_emp_grade = true
    else
      unless @school.emp_grade_file.blank?
        ext = File.extname(@school.emp_grade_file.original_filename);
        basename = File.basename(@school.emp_grade_file.original_filename, ext)
        name =  basename + "-" + rand_val + ext
        # create the file path
        path = File.join(directory, name)
        @school.emp_grade_file_name = path
        @school.import_emp_grade = true
        # write the file
        File.open(path, "wb") { |f| f.write(@school.emp_grade_file.read) }
        FileUtils.chmod 0777, path, :verbose => true
      else
        @school.emp_grade_file_name = ""
        @school.import_emp_grade = false
      end
    end
    ##
    # END Upload Employee Grade File
    ##
    
    ##
    # UPLOAD Exam Grade Excel File
    ##
    if b_checked and params[:school][:import].include?('default_exam_grade_seeds')
      @school.import_exam_grade = true
    else
      unless @school.exam_grade_file.blank?
        ext = File.extname(@school.exam_grade_file.original_filename);
        basename = File.basename(@school.exam_grade_file.original_filename, ext)
        name =  basename + "-" + rand_val + ext
        # create the file path
        path = File.join(directory, name)
        @school.exam_grade_file_name = path
        @school.import_exam_grade = true
        # write the file
        File.open(path, "wb") { |f| f.write(@school.exam_grade_file.read) }
        FileUtils.chmod 0777, path, :verbose => true
      else
        @school.exam_grade_file_name = ""
        @school.import_exam_grade = false
      end
    end
    ##
    # END Upload Exam Grade File
    ##
    
    
    respond_to do |format|
      unless params[:menu_data].nil? or params[:menu_data].blank?
        @school.menu_datas = params[:menu_data]
      else
        @school.menu_datas = ""
      end  
      
      unless params[:package].nil? or params[:package].blank?
        @school.package = params[:package]
        @packages_plugins = PackageMenu.find(:all, :conditions => ["package_id = ? and menu_id = 0",params[:package]], :select => "plugins_name")
        @school.available_plugin.plugins = @packages_plugins.map{|pp| pp.plugins_name}
      else
        @school.package = []
        @settings_plugin = YAML.load_file(File.dirname(__FILE__)+"/../../config/plugins.yml")
        @required_plugins = @settings_plugin["required_plugins"]
        
        @school.available_plugin = AvailablePlugin.new()
        @school.available_plugin.plugins = @required_plugins
      end
      
      @school.build_available_plugin(:plugins=>[]) unless @school.available_plugin
      
      unless params[:school][:code].nil? or params[:school][:code].empty?
        params[:school][:code] = params[:school][:code].downcase
      end
      
      @free_feed_for_admin_param = "0"
      @free_feed_for_teacher_param = "0"
      @free_feed_for_student_param = "0"
      unless params[:free_feed].nil? or params[:free_feed].empty?
        params[:free_feed].each do |free_feed|
          if free_feed == 'free_feed_for_admin'
            @free_feed_for_admin_param = "1"
          elsif free_feed == 'free_feed_for_teacher'
            @free_feed_for_teacher_param = "1"
          elsif free_feed == 'free_feed_for_student'
            @free_feed_for_student_param = "1"
          end
        end
      end
      
      domains = ['api','plus','hook','cp-api','stage','market','weblogin']
      
      @validated = true
      
      if domains.include?(params[:school][:code])
        flash[:notice] = "<span style='color: red;'>Invalid Subdomain Code</span>"
        @validated = false
      end
      
      @school.activation_code = 100000 + rand(900000)
      
      if @validated and @school.save
        
        subscription = SubscriptionInfo.new
        subscription.userid = admin_user_session.id
        subscription.school_id = @school.id
        subscription.start_date = Time.now.to_date
        subscription.end_date = "2030-12-20"
        subscription.no_of_student = false
        subscription.current_count = false
        subscription.is_unlimited = true
        subscription.save
        
        
        url = 'http://cp-api.champs21.com/cp3.php?subdomain='+params[:school][:code]
        resp = Net::HTTP.get_response(URI.parse(url))

        Configuration.find_or_create_by_config_key("InstitutionAddress").update_attributes(:config_value=>params[:institution][:institution_address])
        Configuration.find_or_create_by_config_key("InstitutionPhoneNo").update_attributes(:config_value=>params[:institution][:institution_phone_no])
        
        Configuration.find_or_create_by_config_key("FreeFeedForAdmin").update_attributes(:config_value=>0)
        Configuration.find_or_create_by_config_key("FreeFeedForTeacher").update_attributes(:config_value=>0)
        Configuration.find_or_create_by_config_key("FreeFeedForStudent").update_attributes(:config_value=>0)
        
        Configuration.find_or_create_by_config_key("PaletteSetting").update_attributes(:config_value=>params[:palette_setting])
        
        unless params[:free_feed].nil? or params[:free_feed].empty?
          params[:free_feed].each do |free_feed|
            if free_feed == 'free_feed_for_admin'
              Configuration.find_or_create_by_config_key("FreeFeedForAdmin").update_attributes(:config_value=>1)
            elsif free_feed == 'free_feed_for_teacher'
              Configuration.find_or_create_by_config_key("FreeFeedForTeacher").update_attributes(:config_value=>1)
            elsif free_feed == 'free_feed_for_student'
              Configuration.find_or_create_by_config_key("FreeFeedForStudent").update_attributes(:config_value=>1)
            end
          end
        end
        
        b_manual_assign = false
        check_assign = false
        
        unless params[:assign_free_school].nil? or params[:assign_free_school].empty?
          check_assign = true
        end
        
        if check_assign and params[:assign_free_school].include?("create_new_n_assign")
          b_manual_assign = true
          redirect_to :controller => "schools", :action => "assign_free_school", :assign_free_school => "1", :step_2 => "1", :id => @school.id and return 
        elsif check_assign and params[:assign_free_school].include?("manual_assign")
          b_manual_assign = true
          redirect_to :controller => "schools", :action => "assign_school", :school_step_2 => true, :id => @school.id and return 
        end
        
        if b_manual_assign != true
          flash[:notice] = "<span style='color: black;'>School was successfully created.<span><br /> <span style='color:#666666 !important;'>You can access this school at <a href='http://#{@school.school_domains.first.try(:domain)}' style='color:#990000 !important;font-weight: normal;' target='_blank'>#{@school.school_domains.first.try(:domain)}</a>&nbsp; &nbsp; Username: <b style='font-weight: normal;color: black;'>"+params[:school][:code]+"-admin</b>&nbsp;&nbsp Password: <b style='font-weight: normal;color: black;'>cctune0793</b></span>"
          format.html { redirect_to(@school) }
          format.xml  { render :xml => @school, :status => :created, :location => @school }
        end
      else
        @packages = Package.find(:all)
        unless params[:package].nil? or params[:package].blank?
          @package_id = params[:package]
        else
          @package_id = []
        end
        
        unless params[:menu_data].nil? or params[:menu_data].blank?
          @menu_datas = params[:menu_data].join(",")
        else   
          @menu_datas = ""
        end
        @institution_address = params[:institution][:institution_address]
        @institution_phone_no = params[:institution][:institution_phone_no]
        
        @free_feed_for_admin = @free_feed_for_admin_param
        @free_feed_for_teacher = @free_feed_for_teacher_param
        @free_feed_for_student = @free_feed_for_student_param
        
        unless params[:school][:import].nil?
          @import = params[:school][:import]
        else
          @import = []
        end
        
        unless params[:assign_free_school].nil?
          @assign = params[:assign_free_school]
        else
          @assign = []
        end
        
        format.html { render :action => "new" }
        format.xml  { render :xml => @school.errors, :status => :unprocessable_entity }
      end
    end
  end

  def assign_school
    @school = School.find(params[:id])
    @show_save_message = false
    @view_skip = false
    unless params[:school_step_2].nil? or params[:school_step_2].blank? or params[:school_step_2].empty?
      @view_skip = params[:school_step_2]
      @show_save_message = true
    end
    @school_id = params[:id]
    
    @school_name = @school.name
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    uri = URI(api_endpoint + "api/freeschool/getassignschool")
    http = Net::HTTP.new(uri.host, uri.port)
    auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
    auth_req.set_form_data({"paid_school_id" => @school.id })
    auth_res = http.request(auth_req)
    @free_schools_data = JSON::parse(auth_res.body)
    @free_schools = @free_schools_data['data']['assing']
    
    unless @free_schools.nil? or @free_schools.empty?
      @assigned = true
      @assigned_id = @free_schools['id']
      @assigned_name = @free_schools['name']
    else  
      @assigned = false
    end
  end
  
  def skip_redirect
    @school = School.find(params[:id])
    flash[:notice] = "<span style='color: black;'>School was successfully created.<span><br /> <span style='color:#666666 !important;'>You can access this school at <a href='http://#{@school.school_domains.first.try(:domain)}' style='color:#990000 !important;font-weight: normal;' target='_blank'>#{@school.school_domains.first.try(:domain)}</a>&nbsp; &nbsp; Username: <b style='font-weight: normal;color: black;'>"+@school.code+"-admin</b>&nbsp;&nbsp Password: <b style='font-weight: normal;color: black;'>cctune0793</b></span>"
    respond_to do |format|
      format.html { redirect_to(schools_url) }
      format.xml  { head :ok }
    end
  end
  
  def create_n_assign
    @school = School.find(params[:id])
    @show_flash_message = false
    unless params[:show_flash_message].nil? or params[:show_flash_message].blank? or params[:show_flash_message].empty?
      @show_flash_message = params[:show_flash_message] == "true" ? true : false
      
    end
    
    if @show_flash_message
      flash[:notice] = "<span style='color: black;'>School was successfully created.<span><br /> <span style='color:#666666 !important;'>You can access this school at <a href='http://#{@school.school_domains.first.try(:domain)}' style='color:#990000 !important;font-weight: normal;' target='_blank'>#{@school.school_domains.first.try(:domain)}</a>&nbsp; &nbsp; Username: <b style='font-weight: normal;color: black;'>"+@school.code+"-admin</b>&nbsp;&nbsp Password: <b style='font-weight: normal;color: black;'>champs21</b></span>"
    end
    
    respond_to do |format|
      if @show_flash_message
        format.html { redirect_to(schools_url) }
        format.xml  { head :ok }
      else
        format.html { redirect_to(@school) }
        format.xml  { render :xml => @school, :status => :created, :location => @school }
      end
    end
  end
  
  def search_ajax
    @show_save = false
    unless params[:load_on_startup].nil? or params[:load_on_startup].blank? or params[:load_on_startup].empty?
      @load_on_startup = params[:load_on_startup]
      @school_name = params[:query]
      @school_id = params[:school_id]
    else
      @load_on_startup = "0"
    end
    
    unless params[:view_skip].nil? or params[:view_skip].blank? or params[:view_skip].empty?
      @view_skip = params[:view_skip] == "false" ? false : true
    else  
      @view_skip = false
    end
    
    unless params[:show_save].nil? or params[:show_save].blank? or params[:show_save].empty?
      @show_save = params[:show_save] == "true" ? true : false
    end
    query = params[:query]
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    uri = URI(api_endpoint + "api/freeschool/getschool")
    http = Net::HTTP.new(uri.host, uri.port)
    auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
    auth_req.set_form_data({"term" => query })
    auth_res = http.request(auth_req)
    @free_schools_data = JSON::parse(auth_res.body)
    @free_schools = @free_schools_data['data']['schools']
  end
  
  def assign_free_school
    @school = School.find(params[:id])
    @step_2 = params[:step_2]
    @assign_free = params[:assign_free]
    
    if @assign_free == "1"
      @free_school_id = params[:school_id]
    end
    @paid_school_id = @school.id
    @school_name = @school.name
    @school_code = @school.code
    
    MultiSchool.current_school = @school
    @institution_address = Configuration.get_config_value("InstitutionAddress")
    
    champs21_api_config = YAML.load_file("#{RAILS_ROOT.to_s}/config/champs21.yml")['champs21']
    api_endpoint = champs21_api_config['api_url']
    if @assign_free == "1"
      uri = URI(api_endpoint + "api/freeschool/assign")
    else
      uri = URI(api_endpoint + "api/freeschool/create")
    end
    http = Net::HTTP.new(uri.host, uri.port)
    auth_req = Net::HTTP::Post.new(uri.path, initheader = {'Content-Type' => 'application/x-www-form-urlencoded'})
    if @assign_free == "1"
      auth_req.set_form_data({"name" => @school_name, "code" => @school_code, "paid_school_id" => @paid_school_id, "free_school_id" => @free_school_id })
    else
      auth_req.set_form_data({"name" => @school_name, "code" => @school_code, "paid_school_id" => @paid_school_id, "location" =>  @institution_address})
    end
    
    auth_res = http.request(auth_req)
    
    if @step_2 == "1"
      flash[:notice] = "<span style='color: black;'>School was successfully created.<span><br /> <span style='color:#666666 !important;'>You can access this school at <a href='http://#{@school.school_domains.first.try(:domain)}' style='color:#990000 !important;font-weight: normal;' target='_blank'>#{@school.school_domains.first.try(:domain)}</a>&nbsp; &nbsp; Username: <b style='font-weight: normal;color: black;'>"+@school.code+"-admin</b>&nbsp;&nbsp Password: <b style='font-weight: normal;color: black;'>champs21</b></span>"
    end
    respond_to do |format|
      if @step_2 == "1"
        format.html { redirect_to(schools_url) }
        format.xml  { head :ok }
      else
        format.html { redirect_to(@school) }
        format.xml  { render :xml => @school, :status => :created, :location => @school }
      end
    end
    #abort(@school.inspect)
  end
  
  # PUT /schools/1
  # PUT /schools/1.xml
  def update
    MultiSchool.current_school = @school
    unless @school.activation_code?
      params[:school][:activation_code] = 100000 + rand(900000)
    end
    respond_to do |format|
      params[:school][:available_plugin_attributes]={:plugins=>[]} unless params[:school][:available_plugin_attributes]
      if @school.update_attributes(params[:school])
        
        unless params[:package].nil? or params[:package].blank?
          
          params[:package].each do |pid|
            @school_package = SchoolPackage.find_by_school_id(@school.id)
            unless @school_package.nil?
              @school_package.update_attributes(:package_id => pid)
            else
              @school_package = SchoolPackage.new()
              @school_package.school_id = @school.id
              @school_package.package_id = pid
              @school_package.save
            end
            
          end
        end
        
        SchoolMenuLink.delete_all(:school_id => @school.id)
        unless params[:menu_data].nil? or params[:menu_data].blank?
          @menu_datas = params[:menu_data].join(",")
          @menu_dts = @menu_datas.split(",")
          @menu_dts.each do |menu|
            @school_menu = SchoolMenuLink.new()
            @school_menu.menu_link_id = menu
            @school_menu.school_id = @school.id
            @school_menu.save
          end
        end

        Configuration.find_or_create_by_config_key("InstitutionAddress").update_attributes(:config_value=>params[:institution][:institution_address])
        Configuration.find_or_create_by_config_key("InstitutionPhoneNo").update_attributes(:config_value=>params[:institution][:institution_phone_no])
        
        Configuration.find_or_create_by_config_key("FreeFeedForAdmin").update_attributes(:config_value=>0)
        Configuration.find_or_create_by_config_key("FreeFeedForTeacher").update_attributes(:config_value=>0)
        Configuration.find_or_create_by_config_key("FreeFeedForStudent").update_attributes(:config_value=>0)
        
        Configuration.find_or_create_by_config_key("PaletteSetting").update_attributes(:config_value=>params[:palette_setting])
        
        unless params[:free_feed].nil? or params[:free_feed].empty?
          params[:free_feed].each do |free_feed|
            if free_feed == 'free_feed_for_admin'
              Configuration.find_or_create_by_config_key("FreeFeedForAdmin").update_attributes(:config_value=>1)
            elsif free_feed == 'free_feed_for_teacher'
              Configuration.find_or_create_by_config_key("FreeFeedForTeacher").update_attributes(:config_value=>1)
            elsif free_feed == 'free_feed_for_student'
              Configuration.find_or_create_by_config_key("FreeFeedForStudent").update_attributes(:config_value=>1)
            end
          end
        end
        
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
