namespace :champs21_applicant_registration do
  desc "Install Applicant Registration Module"
  task :install do
    system "rsync -ruv --exclude=.svn vendor/plugins/champs21_applicant_registration/public ."
  end
  desc "Update Paths for Applicant Registration Module"
  task :update_plugins_paths => :environment do

    models = [Applicant,ApplicantAddlAttachment]
    sub_paths = {
      "Applicant" => "public/system/applicants",
      "ApplicantAddlAttachment" => "public/system/applicant_addl_attachments"
    }
    log = Logger.new("log/paperclip_path_update.log")
    models.each do |model|
      begin
        log.debug("#{model}")
        model.send :include, PaperclipPathUpdate
        model.attachment_definitions.each do |d|
          file_type = "#{d.first}_file_name"
          if model.connection.select_all("select * from #{model.table_name} where #{file_type} is not NULL;").present?
            sub_path1_old = sub_paths["#{model}"]
            sub_path1 = sub_path1_old.gsub("#{model.table_name}","#{model.table_name}_backup")
            File.rename "#{sub_path1_old}", "#{sub_path1}"
            
            arr = Dir["#{sub_path1}/*/*/"].map {|a| File.basename(a) }
            arr.each do |arr_l|
              begin
                rec = model.find_without_school arr_l.to_i
                if rec.present? and rec[file_type].present?
                  file = "#{sub_path1}/#{d.first.to_s.pluralize}/#{arr_l}/original/#{rec[file_type]}"
                  if File.exists? file
                    unless rec.update_attribute(d.first.to_sym, File.open(file))
                      log.debug("#{rec.id}----#{rec.errors.full_messages}")
                    end
                  end
                end
              rescue Exception => err
                log.debug("#{err.message}")
                log.debug("------------")
                log.debug("#{err.backtrace.inspect}")
              end
            end
            # BELOW 4 lines can delete old folder after successful moving of files to new location
            #           file = "public/system/#{model.table_name}"
            #           if File.exists? file
            #             FileUtils.rm_r file
            #           end
            sub_path2 = sub_path1.gsub("#{model.table_name}_backup","#{model.table_name}_backup_done")
            File.rename "#{sub_path1}", "#{sub_path2}"
          end
        end
      rescue Exception => e
        log.debug("#{e.message}")
        log.debug("------------")
        log.debug("#{e.backtrace.inspect}")
        
        puts e
        puts "Failed to complete task! Reverting process"
        sub_path1_old = sub_paths["#{model}"]
        sub_path1 = sub_path1_old.gsub("#{model.table_name}","#{model.table_name}_backup")

        if File.exists? sub_path1
          puts "Restoring old data of #{model.table_name} module"
          if File.exists? sub_path1_old
            FileUtils.rm_r sub_path1_old
          end
          File.rename "#{sub_path1}","#{sub_path1_old}"
        end

      end
    end

  end
end
