require 'spreadsheet'
require 'fileutils'
include FileUtils

def get_subject_icon(get_subject_icon)
  icon_file_path = File.join(Rails.root,'public', 'images', 'icons', 'subjects')
  subject_icon = ""
  icons = {"science" => ['biology.png','checmistry.png','physics.png'], "math" => ['math.png', 'geomatry.png']}
  unless get_subject_icon.nil?
    @subject_name = get_subject_icon.strip.downcase
    @subject_name_santized = @subject_name.gsub(" ","_")
    @subject_name_santized_png = @subject_name_santized + ".png"

    icon_file = File.join(icon_file_path, @subject_name_santized_png)

    if File.exist?(icon_file)
      subject_icon = @subject_name_santized_png
    else
      ar_subjects_images = @subject_name.split(" ")
      found_icon = false
      ar_subjects_images.each do |i|
        unless icons[i].nil?
          icon_image = icons['science'][rand(icons['science'].length)]
        else
          icon_image = i + ".png"
        end
        icon_file = File.join(icon_file_path, icon_image)
        if File.exist?(icon_file)
          subject_icon = icon_image
          break
        end
      end
    end
  end
  return subject_icon
end

def get_subject_list(subjects_data)
  icon_file_path = File.join(Rails.root,'public', 'images', 'icons', 'subjects')
  subject_icon = ""
  icons = {"science" => ['biology.png','checmistry.png','physics.png'], "mathematics" => ['math.png', 'geomatry.png'], "math" => ['math.png', 'geomatry.png']}
  @subjects_tmp_ar = []
  unless subjects_data.nil?
    unless subjects_data[0].nil?
      if subjects_data[0].strip == "*"
        return @subjects_tmp_ar
      else
        subject_name_data = subjects_data[0].strip

        unless subjects_data[1].nil? or subjects_data[1].empty?
          if subjects_data[1].strip != "*"
            subject_code = subjects_data[1].strip
          else  
            subject_code = subject_name_data[0,3].upcase + "001"
          end
        else
          subject_code = subject_name_data[0,3].upcase + "001"
        end

        unless subjects_data[2].nil? or subjects_data[2].empty?
          if subjects_data[2].strip != "*"
            max_weekly_classes = subjects_data[2].strip
          else
            max_weekly_classes = "5"
          end
        else
          max_weekly_classes = "5"
        end

        unless subjects_data[3].nil? or subjects_data[3].empty?
          if subjects_data[3].strip != "*"
            credit_hours = subjects_data[3].strip
          else
            credit_hours = "100"
          end
        else
          credit_hours = "100"
        end

        unless subjects_data[4].nil? or subjects_data[4].empty?
          if subjects_data[4].strip != "*"
            subject_icon = subjects_data[4].strip
          end
        end

        if subject_icon.strip.length > 0
          icon_file = File.join(icon_file_path, subject_icon)
          icon_found = false 
          if File.exist?(icon_file)
            icon_found = true
          end
        end

        if ! icon_found
          subject_name = subject_name_data.strip.downcase
          subject_name_santized = subject_name.gsub(" ","_")
          subject_name_santized_png = subject_name_santized + ".png"

          icon_file = File.join(icon_file_path, subject_name_santized_png)

          if File.exist?(icon_file)
            subject_icon = subject_name_santized_png
          else
            ar_subjects_images = subject_name.split(" ")

            found_icon = false
            ar_subjects_images.each do |i|
              unless icons[i].nil?
                icon_image = icons[i][rand(icons[i].length)]
              else
                icon_image = i + ".png"
              end
              icon_file = File.join(icon_file_path, icon_image)
              if File.exist?(icon_file)
                subject_icon = icon_image
                break
              end
            end
          end
        end
        
        @subjects_tmp_ar = [subject_name_data, subject_code, max_weekly_classes, credit_hours, subject_icon]
      end
    end
  end
  return @subjects_tmp_ar
end

if File.exist?(ARGV[0])
  seed_file =  ARGV[0]
  
  @b_found_shift = false
  @class_import = []
  
  @error = false

  @batches = []
  @subjects = []
  @shift = []
  @sections = ""
  @sections_ar = []
  @classes = []
  @b_saved = ARGV[1]
  l = 1
  sh = 1
  #First Row = Shift
  #Second Row = Class Information
  #File Column = Class
  #Second Column = Section
  #Third Column = Subject
  Spreadsheet.open(seed_file) do |book|
    book.worksheet('Sheet1').each do |row|
      
      empty_strings_ar = ['-','*']
      
      #LOOK IF THERE IS A SHIFT FOR THIS SCHOOL
      @shift_data = []
      unless row[0].nil? 
        if row[0].to_s.index('.0') != nil
          row[0] = row[0].to_i.to_s
        end
        @shift_data = row[0].split(":")
      end
      
      if sh == 1 and @shift_data.length == 1
        unless @shift_data[0].nil?
          if @shift_data[0].to_s.strip.length == 0
            @shift << {"name" => "General", "start_date" => Date.today, "end_date" => Date.today+1.year}
          elsif empty_strings_ar.include?(@shift_data[0].to_s)
            @shift << {"name" => "General", "start_date" => Date.today, "end_date" => Date.today+1.year}
          else
            @shift << {"name" => @shift_data[0].to_s.strip, "start_date" => Date.today, "end_date" => Date.today+1.year}
          end
        else
          @shift << {"name" => "General", "start_date" => Date.today, "end_date" => Date.today+1.year}
        end  
      elsif sh == 1 and @shift_data[0].downcase == "section"
        unless @shift_data[1].nil?
           if @shift_data[1].downcase == "none"
             @shift << {"name" => "General", "start_date" => Date.today, "end_date" => Date.today+1.year}
           elsif @shift_data[1].to_s.strip.length == 0
              @shift << {"name" => "General", "start_date" => Date.today, "end_date" => Date.today+1.year}  
           elsif @shift_data[1].index(',') == nil  
             @shift << {"name" => @shift_data[1].strip, "start_date" => Date.today, "end_date" => Date.today+1.year} 	
           elsif @shift_data[1].index(',') != nil  
              @shifts = @shift_data[1].split(',')
              @shifts.each do |s|
                @shift << {"name" => s.strip, "start_date" => Date.today, "end_date" => Date.today+1.year}
              end
           else
             @shift << {"name" => "General", "start_date" => Date.today, "end_date" => Date.today+1.year}
           end
        else
          @shift << {"name" => "General", "start_date" => Date.today, "end_date" => Date.today+1.year}
        end
      else
        
        if sh == 1 and @shift.length == 0
          @shift << {"name" => "General", "start_date" => Date.today, "end_date" => Date.today+1.year}
        end
        
        unless row[1].nil?
          @section_column_data = row[1].split(":")
          if @section_column_data.length == 1
            unless @section_column_data[0].nil?
              if empty_strings_ar.include?(@section_column_data[0].to_s)
                @sections_ar = ['A']
              elsif ! empty_strings_ar.include?(@section_column_data[0].to_s) 
                  if @section_column_data[0].index(',') == nil
                    if @section_column_data[0].index('-') == nil
                       @sections_ar = [@section_column_data[0].strip]
                    else
                       @sections = @section_column_data[0]
                       @sections_ar = @sections.strip.split('-')		 
                    end
                  else
                    @sections = @section_column_data[0]
                    @sections_ar = @sections.strip.split(',')		 
                  end
              elsif @section_column_data[0].to_s == 'section'    
                @sections_ar = ['A']
              end
            else
              @sections_ar = ['A']
            end
          elsif @section_column_data.length > 1
            unless @section_column_data[0].nil?
              if @section_column_data[0].downcase == "section"
                unless @section_column_data[1].nil?
                  if @section_column_data[1].strip.length == 0
                    @sections_ar = ['A']    
                  elsif @section_column_data[1].strip.length == 1
                    if empty_strings_ar.include?(@section_column_data[1].strip.to_s)
                      @sections_ar = ['A']    
                    else
                      @sections_ar = [@section_column_data[1].strip]    
                    end
                  else
                    if @section_column_data[1].index(',') == nil
                      if @section_column_data[1].index('-') == nil
                         @sections_ar = [@section_column_data[1].strip]
                      else
                         @sections = @section_column_data[1]
                         @sections_ar = @sections.strip.split('-')		 
                      end
                    else
                      @sections = @section_column_data[1]
                      @sections_ar = @sections.strip.split(',')		 
                    end
                  end
                else
                  @sections_ar = ['A']
                end
              else
                @sections_ar = ['A']
              end
            else
              unless @section_column_data[1].nil?
                if @section_column_data[1].strip.length == 0
                  @sections_ar = ['A']    
                elsif @section_column_data[1].strip.length == 1
                  if empty_strings_ar.include?(@section_column_data[1].strip.to_s)
                    @sections_ar = ['A']    
                  else
                    @sections_ar = [@section_column_data[1].strip]    
                  end
                else
                  if @section_column_data[1].index(',') == nil
                    if @section_column_data[1].index('-') == nil
                       @sections_ar = [@section_column_data[1].strip]
                    else
                       @sections = @section_column_data[1]
                       @sections_ar = @sections.strip.split('-')		 
                    end
                  else
                    @sections = @section_column_data[1]
                    @sections_ar = @sections.strip.split(',')		 
                  end
                end
              else
                @sections_ar = ['A']
              end
            end
          else
            @sections_ar = ['A']    
          end   
        else
          @sections_ar = ['A']    
        end
        
        @subjects = []
        #By Default we have 3 rows so constantly count for now
        @row_data = row[2].split(":");
        subject_icon = ""
        if @row_data.length == 1 and empty_strings_ar.include?(@row_data[0].to_s)
          @error = true
          ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"
        elsif @row_data.length == 1 and @row_data[0].index('|') == nil
          if @row_data.length == 1 and @row_data[0].index(',') == nil
            if @row_data.length == 1 and @row_data[0].index('-') == nil
              subject_icon = get_subject_icon(@row_data[0])
              @subjects << {"name" => @row_data[0].strip, "code" => @row_data[0][0,3].upcase + "001", "max_weekly_classes" => 5, "credit_hours" => 100, "icon_number" => subject_icon}
            else
              @subjects_data = @row_data[0].split('-')
              subjects_list = get_subject_list(@subjects_data)
              unless subjects_list.nil? or subjects_list.empty?
                @subjects << {"name" => subjects_list[0], "code" => subjects_list[1], "max_weekly_classes" => subjects_list[2], "credit_hours" => subjects_list[3], "icon_number" => subjects_list[4]}
              else
                @error = true
                ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"
              end
            end
          else
            @subjects_data = @row_data[0].split(',')
            subjects_list = get_subject_list(@subjects_data)
            unless subjects_list.nil? or subjects_list.empty?
              @subjects << {"name" => subjects_list[0], "code" => subjects_list[1], "max_weekly_classes" => subjects_list[2], "credit_hours" => subjects_list[3], "icon_number" => subjects_list[4]}
            else
              @error = true
              ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"
            end
          end
        elsif @row_data.length == 1 and @row_data[0].index('|') != nil
          @subjects_data = @row_data[0].strip.split('|')
          @subjects_data.each do |s|
            if s.index(',') == nil
              if s.index('-') == nil
                subject_icon = get_subject_icon(s)
                @subjects << {"name" => s.strip, "code" => s[0,3].upcase + "001", "max_weekly_classes" => 5, "credit_hours" => 100, "icon_number" => subject_icon}
              else
                @subjects_data_inner = s.split('-')
                subjects_list = get_subject_list(@subjects_data_inner)
                unless subjects_list.nil? or subjects_list.empty?
                  @subjects << {"name" => subjects_list[0], "code" => subjects_list[1], "max_weekly_classes" => subjects_list[2], "credit_hours" => subjects_list[3], "icon_number" => subjects_list[4]}
                else
                  @error = true
                  ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"
                end
              end
            else
              @subjects_data_ini = s.split(',')
              subjects_list = get_subject_list(@subjects_data_ini)
              unless subjects_list.nil? or subjects_list.empty?
                @subjects << {"name" => subjects_list[0], "code" => subjects_list[1], "max_weekly_classes" => subjects_list[2], "credit_hours" => subjects_list[3], "icon_number" => subjects_list[4]}
              else
                @error = true
                ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"
              end
            end
          end
        elsif @row_data.length > 1 && @row_data[0].downcase == "subject"
       		@subjects_data = @row_data[1].split('|')
          @subjects_data.each do |s|
            if empty_strings_ar.include?(s.to_s)
              @error = true
              ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"
            elsif s.index('|') == nil
              if s.index(',') == nil
                if s.index('-') == nil
                  subject_icon = get_subject_icon(s)
                  @subjects << {"name" => s.strip, "code" => s[0,3].upcase + "001", "max_weekly_classes" => 5, "credit_hours" => 100, "icon_number" => subject_icon}
                else
                  @subjects_data = s.split('-')
                  subjects_list = get_subject_list(@subjects_data)
                  unless subjects_list.nil? or subjects_list.empty?
                    @subjects << {"name" => subjects_list[0], "code" => subjects_list[1], "max_weekly_classes" => subjects_list[2], "credit_hours" => subjects_list[3], "icon_number" => subjects_list[4]}
                  else
                    @error = true
                    ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"
                  end
                end
              else
                @subjects_data = s.split(',')
                subjects_list = get_subject_list(@subjects_data)
                unless subjects_list.nil? or subjects_list.empty?
                  @subjects << {"name" => subjects_list[0], "code" => subjects_list[1], "max_weekly_classes" => subjects_list[2], "credit_hours" => subjects_list[3], "icon_number" => subjects_list[4]}
                else
                  @error = true
                  ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"
                end
              end
            elsif s.index('|') != nil
              @subjects_data_inner = s.strip.split('|')
              @subjects_data_inner.each do |si|
                if si.index(',') == nil
                  if si.index('-') == nil
                    subject_icon = get_subject_icon(si)
                    @subjects << {"name" => si.strip, "code" => si[0,3].upcase + "001", "max_weekly_classes" => 5, "credit_hours" => 100, "icon_number" => subject_icon}
                  else
                    @subjects_data_ini = s.split('-')
                    subjects_list = get_subject_list(@subjects_data_ini)
                    unless subjects_list.nil? or subjects_list.empty?
                      @subjects << {"name" => subjects_list[0], "code" => subjects_list[1], "max_weekly_classes" => subjects_list[2], "credit_hours" => subjects_list[3], "icon_number" => subjects_list[4]}
                    else
                      @error = true
                      ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"
                    end
                  end
                else
                  @subjects_data_ini = si.split(',')
                  subjects_list = get_subject_list(@subjects_data_ini)
                  unless subjects_list.nil? or subjects_list.empty?
                    @subjects << {"name" => subjects_list[0], "code" => subjects_list[1], "max_weekly_classes" => subjects_list[2], "credit_hours" => subjects_list[3], "icon_number" => subjects_list[4]}
                  else
                    @error = true
                    ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"
                  end
                end
              end
            end
          end
        else
          @error = true
          ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"
        end
        
        i = 0;
        num_shifts = @shift.length
        while i < num_shifts do
          @shift[i]["subjects_attributes"] = @subjects
          i += 1;
        end
        
        
        @class_column_data = row[0].split(":")
        
        if @class_column_data.length == 1 and empty_strings_ar.include?(@class_column_data[0].to_s)
           @error = true
           ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"
        elsif @class_column_data.length == 1 and @class_column_data[0].to_s.strip.length.to_i == 0
           @error = true
           ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"   
        elsif @class_column_data.length == 1 and @class_column_data[0].length > 0
           @class_name = @class_column_data[0].strip
           if @class_name.downcase.index('class') == nil
             @class_name = "Class " + @class_name.strip
           end
           
           @sections_ar.each do |s|
              @class_initial = @class_name[0,1].upcase
              @section_initial = s[0,1].upcase
              
              num_zeros = 4
              nums = l.to_s
              n = nums.bytesize
              num_zeros = num_zeros - n

              k = 0;
              zeros = ""
              while k < num_zeros do
                zeros += "0"
                k += 1
              end	
              
              @code = @class_initial + @section_initial + zeros + l.to_s
              @classes = []
              @classes << {"course_name" => @class_name, "section_name" => s, "code" => @code, "grading_type" => 1, "batches_attributes" => @shift}
              
              if @error
                if ARGV[1] == true
                  @course = Course.new @classes[0]
                  @course.save(false)
                end
              else
                @course = Course.new @classes[0]
                @course.save(false)
              end
           end
           @class_import << {"class_name"  => @class_name}
        elsif @class_column_data.length > 1 && @class_column_data[0].downcase == "class"
          if @class_column_data[1].to_s.strip.length == 0
              @error = true
              ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"   
          elsif @class_column_data[1].to_s.strip.length > 0
              @class_name = @class_column_data[1].strip
              unless @class_named.nil?
                if @class_named.downcase.index('class') == nil
                  @class_name = "Class " + @class_name.strip
                end
              end
              @sections_ar.each do |s|
                 @class_initial = @class_name[0,1].upcase
                 @section_initial = s[0,1].upcase

                 num_zeros = 4
                 nums = l.to_s
                 n = nums.bytesize
                 num_zeros = num_zeros - n

                 k = 0;
                 zeros = ""
                 while k < num_zeros do
                   zeros += "0"
                   k += 1
                 end	

                 @code = @class_initial + @section_initial + zeros + l.to_s
                 @classes = []
                 @classes << {"course_name" => @class_name, "section_name" => s, "code" => @code, "grading_type" => 1, "batches_attributes" => @shift}
                 if @error
                    if ARGV[1] == true
                      @course = Course.new @classes[0]
                      @course.save(false)
                    end
                  else
                    @course = Course.new @classes[0]
                    @course.save(false)
                end
              end
              @class_import << {"class_name"  => @class_name}
          end
        else
          @error = true
          ARGV[3] = "You must select at least one subject for the class you are going to import, please contact System Admin for more details about the excel format"   
        end
        l+= 1
      end     	
      sh += 1
    end
  end
  FileUtils.rm(ARGV[0])

end

if @error  and ARGV[1] == false
  ARGV[2] = "error"
  ARGV[4] = @class_import
  ARGV[5] = l - 1
else
  ARGV[2] == "success"
end
