require 'dispatcher'
# Champs21OnlineExam

class Champs21OnlineExam
  def self.attach_overrides
    Dispatcher.to_prepare :champs21_online_exam do
#      ActionController::Base.instance_eval { include Tinymce::Hammer::ControllerMethods }
#      ActionView::Base.instance_eval { include Tinymce::Hammer::ViewHelpers }
#      ActionView::Helpers::FormBuilder.instance_eval { include Tinymce::Hammer::BuilderMethods }
      Student.instance_eval { include StudentExtension }
    end

  end
  
  def self.application_layout_header
    "layouts/online_exam"
  end
  
  module StudentExtension
    def self.included(base)
      base.instance_eval do
        has_many :transport_fees, :as => 'receiver'
      end
    end
    def available_online_exams
      server_time = Time.now
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
      exams = OnlineExamGroup.find_all_by_batch_id(self.batch_id, :conditions=> "subject_id IS NOT NULL and end_date >= '#{local_tzone_time.to_date}' and start_date <= '#{local_tzone_time.to_date}' and is_published = '1'", :include => [:subject])
      exams.reject {|e| OnlineExamAttendance.exists?( :student_id => self.id, :online_exam_group_id=>e.id)}
    end
  end

  def self.dependency_check(record,type)
    if type == "permanant"
      if record.class.to_s == "Student"
        return true if OnlineExamAttendance.find_by_student_id(record.id).present?
      end
     end
     return false
  end
end