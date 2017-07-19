# Champs21Assigment
require 'dispatcher'

Dispatcher.to_prepare :champs21_assignment do
  ::Subject.instance_eval { has_many :assignments }
  ::Student.instance_eval { has_many :assignment_answers }
end

module Champs21Assignment
  def self.dependency_check(record,type)
    if type=="permanant"
      if record.class.to_s == "Student"
        return true if Assignment.find(:all, :conditions=>["find_in_set (#{record.id},student_list)"]).present?
      end
    end
    return false
  end
end


#
