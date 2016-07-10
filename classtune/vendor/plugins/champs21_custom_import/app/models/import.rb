class Import < ActiveRecord::Base
  require 'set'
  require 'fileutils'

  attr_accessor :file_path,:job_type,:row_counter, :user_id

  belongs_to :export
  has_many :import_log_details,:dependent => :destroy

  default_scope :order => "created_at DESC"


  def perform
    build_core_data(self.csv_file_file_name)
    prev_record = Configuration.find_by_config_key("job/Import/#{self.job_type}")
    if prev_record.present?
      prev_record.update_attributes(:config_value=>Time.now)
    else
      Configuration.create(:config_key=>"job/Import/#{self.job_type}", :config_value=>Time.now)
    end
  end

  def csv_save(upload)
    self.save
    name =  upload.original_filename
    Dir.mkdir("#{RAILS_ROOT}/public/csv_file") unless File.exists?("#{RAILS_ROOT}/public/csv_file")
    directory = "#{RAILS_ROOT}/public/csv_file/#{id}"
    Dir.mkdir(directory) unless File.exists?("#{RAILS_ROOT}/public/csv_file/#{id}")
    path = File.join(directory, name)
    FileUtils.mv(upload.path, path)
    self.csv_file_file_name = path
    self.csv_file_updated_at = updated_at
    self.csv_file_file_size = File.size("#{path}")
    self.save
  end

  def load_yaml
    model = export.model
    if File.exists?("#{Rails.root}/vendor/plugins/champs21_custom_import/config/models/#{model.underscore}.yml")
      exports = YAML.load_file(File.join(Rails.root,"vendor/plugins/champs21_custom_import/config/models","#{model.underscore}.yml"))
    end
    exports
  end

  def process_csv_file(file)
    settings = load_yaml
    model_name = export.model
    all_rows = Array.new
    FasterCSV.foreach(file) do |row|
      all_rows << row.join(',')
    end
    header_row = all_rows.first.split(',')
    core_columns = header_row.select{|row| row.split('|').second.to_s.downcase.gsub(' ','_').camelize.to_s == model_name.to_s}
    associated_columns = header_row - core_columns
    injected_columns = header_row.select{|row| row.split('|').second.to_s == "inject"}
    associated_columns = associated_columns - injected_columns
    [core_columns,associated_columns,injected_columns]
  end

  def get_updated_header_columns
    settings = load_yaml
    model = export.model
    core_columns = Export.place_overrides(model).map{|column| "#{column.split('|').first}|#{column.split('|').second.to_s.underscore.humanize}"}.compact.flatten
    associated_models = settings[model.underscore]["associates"].nil? ? Array.new : settings[model.underscore]["associates"].keys.map{|key| key.to_s}
    associated_columns = Export.prepare_associated_columns(model,associated_models)
    injected_columns = settings[model.underscore]["inject"].nil? ? Array.new : settings[model.underscore]["inject"].map{|inj| "#{inj.to_s.humanize}|inject"}
    join_models = settings[model.underscore]["joins"].nil? ? Array.new : settings[model.downcase]["joins"].keys.map{|key| key.to_s}
    join_columns = Export.prepare_join_columns(model,join_models)
    associated_columns = [associated_columns,join_columns].compact.flatten.map{|column| "#{column.split('|').first}|#{column.split('|').second.to_s.humanize}"}
    [core_columns,associated_columns,injected_columns]
  end

  def check_header_format(file)
    self.save
    file_columns = process_csv_file(file)
    database_columns = get_updated_header_columns
    database_core_columns = Set.new(database_columns.first)
    injected_columns = Set.new(database_columns.third)
    found_core_match = file_columns.find{|core_array| database_core_columns == Set.new(core_array) }.present?
    found_inject_match = database_columns.third.blank? ? true : file_columns.find{|inject_array| injected_columns == Set.new(inject_array)}.present?
    found_associate_match = (file_columns.second & database_columns.second) == file_columns.second ? true : false
    if found_core_match and found_associate_match and found_inject_match
      true
    else
      self.status = "Failed"
      self.import_log_details.create(:status => "Failed",:description => "csv_format_error")
      false
    end
  end

  def build_core_data(file)
    settings = load_yaml
    model_name = export.model.constantize
    core_rows = Array.new
    header_row = Array.new
    inject_rows = Array.new
    if check_header_format(file) == true
      i = 0
      FasterCSV.foreach(file) do |csv_row|
        new_model_instance = model_name.new
        value_hash = Hash.new
        if i == 0
          header_row = csv_row
          core_rows = csv_row.select{|element| element.split('|').second.to_s.downcase.gsub(' ','_').camelize.to_s == model_name.to_s }
          inject_rows = csv_row.select{|element| element.split('|').second.to_s.downcase.gsub(' ','_').to_s == "inject" }
        else
          execute_save = true
          core_rows.each do |core_row|
            self.row_counter = i + 1
            index = header_row.index(core_row)
            database_row_name = settings[model_name.to_s.underscore]["overrides"].select{|key,value| value.to_s == core_row.split('|').first.to_s}.first.nil? ? nil : settings[model_name.to_s.underscore]["overrides"].select{|key,value| value.to_s == core_row.split('|').first.to_s}.first.first unless settings[model_name.to_s.underscore]["overrides"].nil?
            database_row_name = core_row.split('|').first.downcase.gsub(' ','_') if database_row_name.nil?
            
            if database_row_name == "student_activation_code"
              @user = User.find_by_id self.user_id
              if @user.admin and @user.is_visible
                execute_save = true
              end
            end
            
            process_row = database_row_name.dup
            process_row.slice! "_id"
            assoc = settings[model_name.to_s.underscore]["associations"].present? ? settings[model_name.to_s.underscore]["associations"].find{|assoc| process_row.pluralize.index(assoc.to_s)==0 } : Array.new
            process_row = assoc ? assoc : process_row
            if settings[model_name.to_s.underscore]["associations"].present? and settings[model_name.to_s.underscore]["associations"].include? process_row.to_sym
             
              if settings[model_name.to_s.underscore]["map_combination"].present? and settings[model_name.to_s.underscore]["map_combination"].keys.include? process_row.to_s
                map_method = settings[model_name.to_s.underscore]["map_combination"][process_row.to_s]
                scope_to_apply = process_row.to_s.camelize.constantize.scopes.keys.include? :active
                all_data = scope_to_apply == true ? process_row.to_s.camelize.constantize.active.map{|data| [data.id,data.send(map_method)]} : process_row.to_s.camelize.constantize.all.map{|data| [data.id,data.send(map_method)]}
                associated_id = all_data.select{|data| data.second == csv_row[index]}.first.nil? ? nil : all_data.select{|data| data.second == csv_row[index]}.first.first
              else
                assoc_reflection = model_name.reflect_on_association(process_row.to_sym)
                associated_model = assoc_reflection.klass
                associated_column = settings[model_name.to_s.underscore]["associated_columns"][process_row.to_s]
                primary_key = model_name.reflect_on_association(process_row.to_sym).options[:foreign_key] || :id
                associated_id =  case assoc_reflection.macro
                when :belongs_to
                  associated_model.find(:first,:conditions  => {associated_column.to_sym => csv_row[index]}).try(primary_key)
                else
                  assoc_reflection.klass.find(:first,:conditions  => {associated_column.to_sym => csv_row[index]}).try(primary_key)
                end
              end
              value_hash = value_hash.merge(database_row_name.to_sym => associated_id)
            else
              if settings[model_name.to_s.underscore]["booleans"].present? and settings[model_name.to_s.underscore]["booleans"].include? database_row_name.to_sym
                if csv_row[index].present?
                  value_hash = value_hash.merge(database_row_name.to_sym => 1)
                else
                  value_hash = value_hash.merge(database_row_name.to_sym => 0)
                end
              else
                if (settings[model_name.to_s.underscore]["attr_accessor_list"].present? and settings[model_name.to_s.underscore]["attr_accessor_list"].include? database_row_name.to_sym)
                  value_hash = value_hash.merge(database_row_name.to_sym => csv_row[index])
                else
                  if model_name.columns_hash[database_row_name].nil?
                      csv_row[index] = csv_row[index].nil? ? "" : csv_row[index]
                  else  
                    if model_name.columns_hash[database_row_name].sql_type == "varchar(255)"
                      csv_row[index] = csv_row[index].nil? ? "" : csv_row[index]
                    end
                  end
                  unless model_name.columns_hash[database_row_name].nil?
                    value_hash = value_hash.merge(database_row_name.to_sym => csv_row[index])
                  end
                end
              end
            end
          end
          
          save_activation_code = false
          if execute_save == false
            unless value_hash[:student_activation_code].nil?
              unless value_hash[:student_activation_code].empty?
                activation_code = value_hash[:student_activation_code]
                activation = StudentActivationCode.find_by_code activation_code
                unless activation.nil?
                  if activation.is_active == true
                    execute_save = true
                    save_activation_code = true
                  end
                end
              end
            end
          end
          
          unless inject_rows.blank? 
            if settings[model_name.to_s.underscore]["inject"].present? and settings[model_name.to_s.underscore]["finders"].present?
              value_hash = process_injections(model_name,value_hash,inject_rows,header_row,csv_row)
            end
          end 
          
          ar_models_to_save_log = ['student', 'employee', 'guardian']
          if ar_models_to_save_log.include?(model_name.to_s.underscore)
            unless value_hash[:pass].nil?
              unless value_hash[:pass].blank?
                if value_hash[:pass] == "1"
                  value_hash[:pass] = (('2'..'9').to_a + ('a'..'h').to_a + ('p'..'z').to_a + ('A'..'H').to_a + ('P'..'Z').to_a).shuffle.first(6).join
                elsif value_hash[:pass] == "0"
                  value_hash[:pass] = "123456"
                end
              else
                value_hash[:pass] = "123456"
              end
            else
              value_hash[:pass] = "123456"
            end
          end
          
          if execute_save
            if model_name.to_s.underscore == "subject"
              if value_hash[:code].nil? or value_hash[:code].blank?
                unless value_hash[:name].nil? or value_hash[:name].empty?
                  value_hash[:code] = Subject.set_subject_code(value_hash[:name][0,3].upcase, value_hash[:batch_id])
                end
              end
              value_hash[:credit_hours] = "100.00"
            end
            
            
            if ar_models_to_save_log.include?(model_name.to_s.underscore)
              value_hash[:save_log] = true
              value_hash[:save_to_free] = true
            end
            new_model_instance = model_name.new(value_hash)
            if settings[model_name.to_s.underscore]["mandatory_joins"].present?
              new_model_instance = build_join_data(new_model_instance,csv_row,header_row, true)
            end
            
            if new_model_instance.present? and new_model_instance.valid?
              if new_model_instance.save
                if ar_models_to_save_log.include?(model_name.to_s.underscore)
                  unless new_model_instance.user.nil?
                    new_model_instance.user.save_to_log = nil
                    new_model_instance.user.save_to_free = nil
                  end
                end
                if save_activation_code
                  activation_code = value_hash[:student_activation_code]
                  activation = StudentActivationCode.find_by_code activation_code
                  activation.update_attributes(:is_active => false, :student_id => new_model_instance.id)
                end
                
                unless ARGV[1001].nil?
                  if ARGV[1001].to_s == "SAVED_SUCCESSFULLY"
                    build_associate_data(new_model_instance,csv_row,header_row, true)
                  else
                    self.import_log_details.create(:status => "Failed",:model => self.row_counter,:description => "Student record is uploaded to database with error " + ARGV[1001].to_s + ". Please contact with system admin for more details") 
                    build_associate_data(new_model_instance,csv_row,header_row, false)
                  end
                  ARGV[1001] = nil
                else
                  build_associate_data(new_model_instance,csv_row,header_row, true)
                end
              else
                self.import_log_details.create(:status => "Failed",:model => self.row_counter,:description => "#{new_model_instance.errors.full_messages.join(", ")}") unless new_model_instance.nil?
              end
            else
              self.import_log_details.create(:status => "Failed",:model => self.row_counter,:description => "#{new_model_instance.errors.full_messages.join(", ")}") unless new_model_instance.nil?
            end
          else  
            self.import_log_details.create(:status => "Failed",:model => self.row_counter,:description => "Invalid Activation Code")
          end
        end
        
        i += 1
      end
      
      if import_log_details.all.select{|ild| ild.status == "Failed"}.blank? 
        self.status = "Success"
      else
        self.status = "Complete with errors"
      end
      self.save
    else  
      self.status = "Failed"
      self.save
      self.import_log_details.create(:status => "Failed",:model => self.row_counter,:description => "export_format_not_matching")
    end
  end

  def process_injections(model,value_hash,inject_rows,header_row,csv_row)
    settings = load_yaml
    finders = settings[model.to_s.underscore]["finders"]
    unless finders.nil?
      core_find = finders.select{|key,value| value.is_a? Array}.join(',')
      main_hash = Hash.new
      core_find_models = core_find.split(',').reject{|cfm| cfm == core_find.split(',').first}
      core_find_models.each do |core_find_model|
        column_find = settings[model.to_s.underscore]["finders"][core_find_model]
        model_hash = Hash.new
        column_find.each do |key,value|
          index = header_row.index("#{value.humanize}|inject")
          if settings[model.to_s.underscore]["map_column"].present? and settings[model.to_s.underscore]["map_column"].present? and settings[model.to_s.underscore]["map_column"].keys.include? key
            map_model = settings[model.to_s.underscore]["map_column"][key].camelize.constantize
            map_method = settings[model.to_s.underscore]["map_combination"][map_model.to_s.underscore]
            scope_to_apply = map_model.scopes.keys.include? :active
            get_collection = scope_to_apply == true ? map_model.active : map_model.all
            found_value = get_collection.select{|element| element.send(map_method) == csv_row[index]}.first.try(:id)
            model_hash = model_hash.merge(key.to_sym => found_value)
          else
            model_hash = model_hash.merge(key.to_sym => csv_row[index])
          end
        end
        data = core_find_model.camelize.constantize.find(:first,:conditions => model_hash).try(:id)
        main_hash = main_hash.merge((core_find_model.underscore + "_id").to_sym => data)
      end
      main_data = core_find.split(',').first.camelize.constantize.find(:first,:conditions => main_hash).try(:id)
    end
    value_hash = value_hash.merge((core_find.split(',').first + "_id").to_sym => main_data)
  end

  def build_associate_data(new_model_instance,csv_row,header_row, saved_to_free)
    settings = load_yaml
    flag = 0
    associated_models = settings[new_model_instance.class.name.underscore]["associates"].nil? ? Array.new : settings[new_model_instance.class.name.underscore]["associates"].keys
    associated_models.each do |associated_model|
      associated_model_name = associated_model.camelize.constantize
      associated_rows = header_row.select{|element| element.split('|').second.to_s.downcase.gsub(' ','_' ).to_s == associated_model.to_s }
      search_model = settings[new_model_instance.class.name.underscore]["associates"][associated_model].camelize.constantize
      search_column = settings[new_model_instance.class.name.underscore]["associate_column_search"][search_model.to_s.underscore]
      insert_column = settings[new_model_instance.class.name.underscore]["associate_columns"][associated_model]
      associated_rows.each do |associated_row|
        index = header_row.index(associated_row)
        search_row = associated_row.split('|').first
        if associated_model_name.reflect_on_association(search_model.to_s.underscore.to_sym).present? and associated_model_name.reflect_on_association(search_model.to_s.underscore.to_sym).options.present? and associated_model_name.reflect_on_association(search_model.to_s.underscore.to_sym).options[:foreign_key].present?
          child_associate_column = associated_model_name.reflect_on_association(search_model.to_s.underscore.to_sym).options[:foreign_key]
        else
          child_associate_column = search_model.to_s.underscore + "_id"
        end
        associate_value_id = search_model.find(:first,:conditions => {search_column.to_sym => search_row}).try(:id)
        new_associate_record = new_model_instance.send(associated_model.pluralize).new(insert_column.to_sym => csv_row[index],(child_associate_column).to_sym => associate_value_id)
        if new_associate_record.valid?
          new_associate_record.save
        else
          flag = 1
          self.import_log_details.create(:status => "Failed",:model => self.row_counter,:description => "#{new_associate_record.errors.full_messages.join("\n")}")
        end
      end
    end
    if flag == 0
      unless settings[new_model_instance.class.name.to_s.underscore]["mandatory_joins"].present?
        build_join_data(new_model_instance,csv_row,header_row, saved_to_free)
      end
    elsif flag == 1
      associated_models.map{|associated_model| new_model_instance.send(associated_model.pluralize).all.map{|element| element.destroy}}
      if settings[new_model_instance.class.name.underscore]["dependent"].present?
        new_model_instance.send(settings[new_model_instance.class.name.underscore]["dependent"]).try(:destroy)
      end
      new_model_instance.destroy
    end
  end

  def build_join_data(new_model_instance,csv_row,header_row, saved_to_free)
    settings = load_yaml
    join_models = settings[new_model_instance.class.name.underscore]["joins"].nil? ? Array.new : settings[new_model_instance.class.name.underscore]["joins"].keys
    value_hash = Hash.new
    if join_models.blank?
      if saved_to_free
        self.import_log_details.create(:status => "Success",:model => self.row_counter,:description => "uploaded_to_database_successfully")
      end
    end
    join_models.each do |join_model|
      join_model_name = join_model.singularize.camelize.constantize
      join_search_column = settings[new_model_instance.class.name.underscore]["join_column_search"][join_model]
      parent_model_name = settings[new_model_instance.class.name.underscore]["joins"][join_model_name.to_s.downcase.pluralize].singularize.camelize.constantize
      join_rows = header_row.select{|element| element.split('|').second.to_s.downcase.gsub(' ','_' ).pluralize.to_s == join_model_name.to_s.downcase.pluralize}
      join_values = Array.new
      join_rows.each do |join_row|
        index = header_row.index(join_row)
        search_value = join_row.split('|')
        if csv_row[index].present?
          join_value = join_model_name.find(:first,:conditions => {join_search_column.to_sym => search_value})
          join_values << join_value
        end
      end
      unless new_model_instance.class.name.to_s == parent_model_name.to_s
        if new_model_instance.send(parent_model_name.to_s.downcase).update_attributes(join_model.to_sym => join_values)
          if saved_to_free
            self.import_log_details.create(:status => "Success",:model => self.row_counter,:description => "uploaded_to_database_successfully")
          end
        else
          new_model_instance.send(parent_model_name.to_s.downcase).update_attributes(join_model.to_sym => [])
          new_model_instance.destroy         
          self.import_log_details.create(:status => "Failed",:model => self.row_counter,:description => "Join data save error")
        end
      else       
        if new_model_instance.update_attributes(join_model.to_sym => join_values)
          self.import_log_details.create(:status => "Success",:model => self.row_counter,:description => "uploaded_to_database_successfully")
        else
          #new_model_instance.send(parent_model_name.to_s.downcase).update_attributes(join_model.to_sym => [])
          self.import_log_details.create(:status => "Failed",:model => self.row_counter,:description => "#{new_model_instance.errors.full_messages.join(', ')}")
          if settings[new_model_instance.class.name.underscore]["dependent"].present?
            new_model_instance.send(settings[new_model_instance.class.name.underscore]["dependent"]).try(:destroy)
          end
          new_model_instance.destroy
        end
      end
    end
    new_model_instance.frozen? ? nil : new_model_instance
  end

  def self.default_time_zone_present_time(time_stamp)
    server_time = time_stamp
    server_time_to_gmt = server_time.getgm
    local_tzone_time = server_time
    time_zone = Configuration.find_by_config_key("TimeZone")
    unless time_zone.nil?
      unless time_zone.config_value.nil?
        zone = TimeZone.find(time_zone.config_value)
        if zone.difference_type=="+"
          local_tzone_time = server_time_to_gmt + zone.time_difference
        else
          local_tzone_time = server_time_to_gmt - zone.time_difference
        end
      end
    end
    return local_tzone_time
  end
end





