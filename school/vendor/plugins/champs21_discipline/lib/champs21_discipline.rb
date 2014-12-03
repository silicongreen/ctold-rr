 
module Champs21Discipline
  def self.attach_overrides
    Dispatcher.to_prepare :champs21_discipline do
      ::User.instance_eval {include UserExtension}
    end
  end
  
  module UserExtension
    def self.included(base)
      base.instance_eval do
        has_many :discipline_complaints
        has_many :discipline_participations
        has_many :discipline_comments
        has_many :discipline_actions
      end
    end
  end

  def self.dependency_check(record,type)
    if type == "permanant"
      if record.class.to_s == "Student" or record.class.to_s == "Employee" or record.class.to_s == "Parent"
        return true if record.user.present? and record.user.discipline_participations.present?
      end
    end
    return false
  end

end
