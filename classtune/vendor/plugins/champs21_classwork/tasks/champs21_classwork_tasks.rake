namespace :champs21_classwork do
  desc "Install Champs21 Classwork Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_classwork/public ."
  end
  desc "Update Paths for Task Module"
  task :update_plugins_paths => :environment do
    models = [Classwork, ClassworkAnswer]
    prefix = "public/uploads/classworks"
    prefix1 = "#{prefix.gsub("#{models.first.table_name}","#{models.first.table_name}_backup")}"
    sub_paths = {
      "Classwork" => ":employee_id",
      "ClassworkAnswer" => ":classwork_employee_id/:classwork_id/answers/:student_id"
    }

    log = Logger.new("log/paperclip_path_update.log")
    begin
      classworks = Classwork.connection.select_all("select * from #{Classwork.table_name} where attachment_file_name is not NULL;").present?
      classwork_answers = ClassworkAnswer.connection.select_all("select * from #{ClassworkAnswer.table_name} where attachment_file_name is not NULL;").present?
      if classworks or classwork_answers
        File.rename "#{prefix}", "#{prefix1}"
        models.each do |model|
          log.debug("#{model}")
          model.send :include, PaperclipPathUpdate

          model.attachment_definitions.each do |d|
            sub_path1 = sub_paths["#{model}"]
            recs = model.connection.select_all("select * from #{model.table_name} where attachment_file_name is not NULL;")
            recs.each do |rec|
              begin
                rec = model.send :instantiate,rec unless rec.nil?
                rec = model.find_without_school rec.id
                file = "#{prefix1}/#{sub_path1}/#{rec.attachment_file_name}" if model == ClassworkAnswer
                file = "#{prefix1}/#{sub_path1}/#{rec.id}/#{rec.attachment_file_name}" if model == Classwork
                if File.exists? file and ((File.size? file) == rec.attachment_file_size)
                  unless rec.update_attribute(d.first.to_sym, File.open(file))
                    log.debug("#{rec.id}----#{rec.errors.full_messages}")
                  end
                end
              rescue Exception => err
                log.debug("#{err.message}")
                log.debug("------------")
                log.debug("#{err.backtrace.inspect}")
              end
            end
          end
        end
        sub_path2 = prefix1.gsub("#{models.first.table_name}_backup","#{models.first.table_name}_backup_done")
        File.rename "#{prefix1}", "#{sub_path2}"
        # BELOW 3 lines can delete backup folder after successful moving of files to new location
        #            if File.exists? prefix1
        #              FileUtils.rm_r prefix1
        #            end
      end

    rescue Exception => e
      log.debug("#{e.message}")
      log.debug("------------")
      log.debug("#{e.backtrace.inspect}")
      puts e
      puts "Failed to complete task! Reverting process"
      if File.exists? "uploads/classworks"
        puts "Restoring old data of classworks plugin"
        FileUtils.rm_r "uploads/classworks"
        if File.exists? prefix1
          File.rename "#{prefix1}","#{prefix}"
        end
      end
    end
  end
end
