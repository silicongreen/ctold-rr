require 'dispatcher'
# Champs21Discussion
module Champs21Gallery
  def self.attach_overrides
    Dispatcher.to_prepare :champs21_gallery do
      ::Student.instance_eval { has_many :gallery_tags, :as => :member, :dependent => :destroy }
      ::Employee.instance_eval { has_many :gallery_tags, :as => :member, :dependent => :destroy }
    end
  end
end