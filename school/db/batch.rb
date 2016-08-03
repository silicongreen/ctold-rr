[
    {
      "course_name"         => "Class 9", 
      "section_name"        => "A", 
      "code"                => "C9A001", 
      "grading_type"        => "1",
      "batches_attributes"  => 
        [{ 
          "name"                => "Morning", 
          "start_date"          => Date.today, 
          "end_date"            => Date.today+1.year,
          "subjects_attributes" => 
            [ 
              { 
                "name"                => "Bangla 1st Paper", 
                "code"                => "BAN001", 
                "max_weekly_classes"  => 5,
                "credit_hours"        => 100
              },
              { 
                "name"                => "Bangla 2nd Paper", 
                "code"                => "BAN101", 
                "max_weekly_classes"  => 5,
                "credit_hours"        => 110
              },
              { 
                "name"                => "English 1st Paper", 
                "code"                => "ENG001", 
                "max_weekly_classes"  => 5,
                "credit_hours"        => 120
              },
              { 
                "name"                => "English 2nd Paper", 
                "code"                => "ENG101", 
                "max_weekly_classes"  => 5,
                "credit_hours"        => 130
              },
              { 
                "name"                => "Mathmatics", 
                "code"                => "MATH100", 
                "max_weekly_classes"  => 5,
                "credit_hours"        => 140
              },
              { 
                "name"                => "Science", 
                "code"                => "SCI201", 
                "max_weekly_classes"  => 5,
                "credit_hours"        => 150
              }
            ]
        }] 
      },
      {
      "course_name"         => "Class 10", 
      "section_name"        => "A", 
      "code"                => "C10A001", 
      "grading_type"        => "1",
      "batches_attributes"  => 
        [{ 
          "name"                  => "Morning", 
          "start_date"            => Date.today, 
          "end_date"              => Date.today+1.year,
          "subjects_attributes"   => 
            [ 
              { 
                "name"                => "Bangla 1st Paper", 
                "code"                => "BAN001", 
                "max_weekly_classes"  => 5,
                "credit_hours"        => 90
              },
              { 
                "name"                => "Bangla 2nd Paper", 
                "code"                => "BAN101", 
                "max_weekly_classes"  => 5,
                "credit_hours"        => 100
              },
              { 
                "name"                => "English 1st Paper", 
                "code"                => "ENG001", 
                "max_weekly_classes"  => 5,
                "credit_hours"        => 110
              },
              { 
                "name"                => "English 2nd Paper", 
                "code"                => "ENG101", 
                "max_weekly_classes"  => 5,
                "credit_hours"        => 120
              },
              { 
                "name"                => "Mathmatics", 
                "code"                => "MATH100", 
                "max_weekly_classes"  => 5,
                "credit_hours"        => 130
              },
              { 
                "name"                => "Science", 
                "code"                => "SCI201", 
                "max_weekly_classes"  => 5,
                "credit_hours"        => 140
              }
            ]
        }] 
      }
  ].each do |param|
    @course = Course.new param
    @course.save(false)
  end