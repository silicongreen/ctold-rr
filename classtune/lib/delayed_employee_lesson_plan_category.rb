require 'i18n'
class DelayedEmployeeLesssonPlanCategory
  def initialize()
    
  end

  def perform
    @employees = Employee.find(:all,:order=>'first_name ASC')
    now = I18n.l(@local_tzone_time.to_datetime, :format=>'%Y-%m-%d %H:%M:%S')
    unless @employees.blank?
      @employees.each do |emp|
        lesson_cat = LessonplanCategory.find_by_author_id(emp.user_id)
        if lesson_cat.blank?
          ["Weekly","Monthly","Yearly"].each do |val|
            lessonplan_category = LessonplanCategory.new
            lessonplan_category.name = val
            lessonplan_category.school_id = MultiSchool.current_school.id
            lessonplan_category.author_id = emp.user_id
            
            lessonplan_category.created_at = now
            lessonplan_category.updated_at = now
            lessonplan_category.save()
          end
        end
      end
    end
  end

end