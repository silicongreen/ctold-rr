require 'dispatcher'
# Champs21Task
module Champs21Task
  def self.attach_overrides
    ::User.instance_eval { include UserExtension }
  end
  
  module UserExtension
    def self.included(base)
      base.instance_eval do
        has_many :tasks
        has_many :task_comments
        has_many :task_assignees,:foreign_key=>:assignee_id
        has_many :assigned_tasks,:through=>:task_assignees
      end
    end
  end
end