[
    {
      "name"    => "Teacher", 
      "prefix"  => "Th",
      "status"  =>  1,
      "employee_positions_attributes"  => 
        [
        { 
          "name"    => "Principal",
          "status"  =>  1,
        },
        { 
          "name"    => "Senior",
          "status"  =>  1,
        },
        { 
          "name"    => "Junior",
          "status"  =>  1,
        }
        ] 
      }
].each do |param|
    @employee_category = EmployeeCategory.new param
    @employee_category.save(false)
end

[
    {
      "name"    => "All Department", 
      "code"    => "All",
      "status"  => 1
    },
    {
      "name"    => "Bangla", 
      "code"    => "BAN",
      "status"  => 1
    },
    {
      "name"    => "English", 
      "code"    => "ENG",
      "status"  => 1
    },
    {
      "name"    => "Science", 
      "code"    => "SCI",
      "status"  => 1
    },
    {
      "name"    => "MATH", 
      "code"    => "MAT",
      "status"  => 1
    },
    {
      "name"    => "Islam", 
      "code"    => "IS",
      "status"  => 1
    },
      
].each do |param|
    @employee_department = EmployeeDepartment.new param
    @employee_department.save(false)
end

[
    {
      "name"           => "Principal", 
      "priority"       => 2,
      "max_hours_day"  => 6,
      "max_hours_week" => 30,
      "status"         => 1
    },
    {
      "name"           => "Senior Teacher", 
      "priority"       => 3,
      "max_hours_day"  => 6,
      "max_hours_week" => 30,
      "status"         => 1
    },
    {
      "name"           => "Junior Teacher", 
      "priority"       => 4,
      "max_hours_day"  => 6,
      "max_hours_week" => 30,
      "status"         => 1
    }
      
].each do |param|
    @employee_grade = EmployeeGrade.new param
    @employee_grade.save(false)
end