require 'spreadsheet'
require 'fileutils'
include FileUtils

@positions = []
@emp = []
@dept = []
@grade = []

if File.exist?(ARGV[0])
  seed_file =  ARGV[0]


Spreadsheet.open(seed_file) do |book|
  book.worksheet('Sheet1').each do |row|
    break if row[0].nil? 

    @positions = []

    #By Default we have 3 rows so constantly count for now
    @row_data = row[1].split(":");
    if @row_data.length > 1 && @row_data[0].downcase == "position"
      @position_data = @row_data[1].split(',')
      i = 0
      num_position = @position_data.length                

      while i < num_position do
        @positions << {"name" => @position_data[i].strip, "status" => 1}
        i += 1;
      end
    else
	    @emp = nil
	    break 	
    end
    @emp_data = row[0].split(":")  
	 
    if @emp_data.length > 1 && @emp_data[0].downcase == "category"
        @emp << {"name" => @emp_data[1], "prefix" => @emp_data[1][0,3].upcase, "status" => 1, "employee_positions_attributes" => @positions}
    else
      @emp = nil
      break;
    end

  end
end

unless @emp.nil?
  @emp.each do |param|
    @employee_category = EmployeeCategory.new param
    @employee_category.save(false)
  end
end

  FileUtils.rm(ARGV[0])
end


if File.exist?(ARGV[1])
  seed_file =  ARGV[1]


Spreadsheet.open(seed_file) do |book|
  book.worksheet('Sheet1').each do |row|
    break if row[0].nil? 

    @dept = []

    #By Default we have 3 rows so constantly count for now
    @row_data = row[0].split(":");
    if @row_data.length > 1 && @row_data[0].downcase == "department"
      @dept_data = @row_data[1].split(',')
      i = 0
      num_position = @dept_data.length                

      while i < num_position do
        @dept << {"name" => @dept_data[i].strip, "code" => @dept_data[i][0,4].upcase,  "status" => 1}
        i += 1;
      end
    else
	    @dept = nil
	    break 	
    end
     
  end
end

unless @dept.nil?
  @dept.each do |param|
    @employee_department = EmployeeDepartment.new param
    @employee_department.save(false)
  end
end

  FileUtils.rm(ARGV[1])
end


if File.exist?(ARGV[2])
  seed_file =  ARGV[2]


Spreadsheet.open(seed_file) do |book|
  book.worksheet('Sheet1').each do |row|
    break if row[0].nil? 

    @grade = []

    #By Default we have 3 rows so constantly count for now
    @row_data = row[0].split(":");
    if @row_data.length > 1 && @row_data[0].downcase == "grade"
      @grade_data = @row_data[1].split(',')
      i = 0
      num_position = @grade_data.length                

      while i < num_position do
        @grade << {"name" => @grade_data[i].strip, "priority" => i+1, "max_hours_day" => 6, "max_hours_week" => 40,  "status" => 1}
        i += 1;
      end
    else
	    @dept = nil
	    break 	
    end
     
  end
end

unless @grade.nil?
  @grade.each do |param|
    @employee_grade = EmployeeGrade.new param
    @employee_grade.save(false)
  end
end

  FileUtils.rm(ARGV[2])
end