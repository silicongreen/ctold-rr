require 'spreadsheet'
require 'fileutils'
include FileUtils
if File.exist?(ARGV[0])
  @grade = []
  seed_file =  ARGV[0]

  
  Spreadsheet.open(seed_file) do |book|
    book.worksheet('Sheet1').each do |row|
      break if row[0].nil? 

      #By Default we have 3 rows so constantly count for now
      @row_data = row[0].split(",");

      if @row_data.length > 1
        @grade << {"name" => @row_data[0].strip.upcase, "min_score" => @row_data[1].to_s}
      end
    end
  end  
  @grade.each do |param|
    GradingLevel.create(param)
  end
   
  FileUtils.rm(ARGV[0])
end

