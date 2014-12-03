require "vote_fu"
require "dispatcher"

module Champs21Blog
  
  def self.attach_overrides
    Dispatcher.to_prepare :champs21_blog do
      ::User.instance_eval { include UserExtension }
    end
  end

  module UserExtension
    def self.included(base)
      base.instance_eval do
        has_one :blog
        has_many :blog_comments
        acts_as_voter
      end
    end
  end
end

