# Champs21Discussion
require 'dispatcher'
module Champs21Discussion
  def self.attach_overrides
    Dispatcher.to_prepare :champs21_discussion do
      ::User.instance_eval { include UserExtension }
    end
  end
  
  module UserExtension
    def self.included(base)
      base.instance_eval do
        has_many :groups, :dependent => :destroy
        has_many :group_members, :dependent => :destroy
        has_many :group_posts
        has_many :group_post_comments
        has_many :group_files
      end
    end
    
    def member_groups
      if self.admin? or self.privileges.include?(Privilege.find_by_name("GroupCreate"))
        groups=Group.all
      else
        group_ids=GroupMember.find(:all, :conditions=>{:user_id=>self.id})
        groups=Group.find(:all,:conditions=>["id IN (?)",group_ids.map{|group| group.group_id}])
      end
    end
  end
end