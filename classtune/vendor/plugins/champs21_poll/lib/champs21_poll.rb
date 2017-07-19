# Champs21Poll
require 'dispatcher'
module Champs21Poll
  def self.attach_overrides
    Dispatcher.to_prepare :champs21_poll do
      ::Batch.instance_eval { has_many :poll_members, :as => 'member' }
      ::Batch.instance_eval { has_many :poll_question, :through=>:poll_members }
      ::Student.instance_eval { delegate :poll_question ,:to=>:batch }
      ::EmployeeDepartment.instance_eval { has_many :poll_members, :as => 'member' }
      ::EmployeeDepartment.instance_eval { has_many :poll_question, :through=>:poll_members }
      ::Employee.instance_eval { delegate :poll_question ,:to=>:employee_department }
      ::User.instance_eval { include  UserExtension }
    end
  end
  
  unloadable
  def self.dashboard_layout_left_sidebar
    "poll_left_sidebar"
  end


  module UserExtension
    def already_voted?(poll_question_id)
      PollVote.find(:all,:conditions=>["user_id = ? and poll_question_id = ?",self.id,poll_question_id]).present?
    end

    def accessible_poll
      if self.admin
        poll = PollQuestion.find(:all)
      elsif self.student
        student = self.student_record
        poll = student.poll_question
      elsif self.employee
        employee = self.employee_record
        poll = employee.poll_question
      end
      return poll
    end
  end
end

