require 'dispatcher'
require "list"
  
module Champs21ApplicantRegistration
  def self.attach_overrides
    ActiveRecord::Base.instance_eval { include ActiveRecord::Acts::List }
    Dispatcher.to_prepare :champs21_applicant_registration do
      ::Course.instance_eval { has_one :registration_course }
    end
  end
end