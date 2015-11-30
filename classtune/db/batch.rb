require 'spreadsheet'
require 'fileutils'
include FileUtils

seed_file = File.join(Rails.root,'seeds', 'batches.xls')

unless ARGV[0].nil?
  if File.exist?(ARGV[0])
    seed_file =  ARGV[0]
  end
end

@batches = []
@subjects = []
@shift = []
@sections = ""
@sections_ar = []
@classes = []
l = 1
#First Row = Shift
#Second Row = Class Information
#File Column = Class
#Second Column = Section
#Third Column = Subject
unless ARGV[1].nil?
  if ARGV[1]
    Spreadsheet.open(seed_file) do |book|
      book.worksheet('Sheet1').each do |row|
        break if row[0].nil? 

        #LOOK IF THERE IS A SHIFT FOR THIS SCHOOL
        @shift_data = row[0].split(":")
        if @shift_data[0].downcase == "section" && @shift_data[1].downcase == "none"
           @shift << {"name" => "Common", "start_date" => Date.today, "end_date" => Date.today+1.year}
        elsif @shift_data[0].downcase == "section" && @shift_data[1].index(',') == nil
           @shift << {"name" => @shift_data[1].strip, "start_date" => Date.today, "end_date" => Date.today+1.year} 	
        elsif @shift_data[0].downcase == "section" && @shift_data[1].index(',') != nil
      @shifts = @shift_data[1].split(',')
      i = 0
      num_shifts = @shifts.length
            while i < num_shifts
        @shift << {"name" => @shifts[i].strip, "start_date" => Date.today, "end_date" => Date.today+1.year}
        i += 1;
      end
        else
       break if row[1].nil?
       @section_column_data = row[1].split(":")
       if @section_column_data.length > 1 && @section_column_data[0].downcase == "section"
           @sections = @section_column_data[1]
           @sections_ar = @sections.strip.split('-')		
       else
           @batches = nil
           break 	
       end   
       @subjects = []

           #By Default we have 3 rows so constantly count for now
           @row_data = row[2].split(":");
           if @row_data.length > 1 && @row_data[0].downcase == "subject"
              @subjects_data = @row_data[1].split('|')
              i = 0
              num_subject = @subjects_data.length                

              while i < num_subject do
                    @sub = @subjects_data[i].split('-')

                    if @sub.length == 2 then
                        @subjects << {"name" => @sub[0].strip, "code" => @sub[1].strip, "max_weekly_classes" => 5, "credit_hours" => 100}
                    else
                        @subjects << {"name" => @sub[0].strip, "code" => "none", "max_weekly_classes" => 5, "credit_hours" => 100}
                    end
                    i += 1;
              end

        i = 0;
        num_shifts = @shift.length
        while i < num_shifts do
          @shift[i]["subjects_attributes"] = @subjects
          i += 1;
        end
       else
          @batches = nil
          break 	
           end

       @class_column_data = row[0].split(":")
             if @class_column_data.length > 1 && @class_column_data[0].downcase == "class"
                 @class_name = @class_column_data[1].strip
           i = 0
           num_section = @sections_ar.length

           while i < num_section do
        @class_initial = @class_name[0,1].upcase
        @section_initial = @sections_ar[i][0,1].upcase

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
        @classes << {"course_name" => @class_name, "section_name" => @sections_ar[i], "code" => @code, "grading_type" => 1, "batches_attributes" => @shift}
        @course = Course.new @classes[0]
        @course.save(false)
        i += 1
           end

             else
                 @batches = nil
                 break
             end
          l+= 1
        end     	

      end
    @batches = @classes	
    end
  end
end

unless ARGV[0].nil?
  if File.exist?(ARGV[0])
    FileUtils.rm(ARGV[0])
  end
end

#unless @batches.nil?
#  @batches.each do |param|
#    	@course = Course.new param
#    	@course.save(false)
#  end
#end
