# Champs21Assigment
require 'dispatcher'

Dispatcher.to_prepare :champs21_classwork do
  ::Subject.instance_eval { has_many :classworks }
  ::Student.instance_eval { has_many :classwork_answers }
end

module Champs21Classwork
  def self.dependency_check(record,type)
    if type=="permanant"
      if record.class.to_s == "Student"
        return true if Classwork.find(:all, :conditions=>["find_in_set (#{record.id},student_list)"]).present?
      end
    end
    return false
  end
end


#
