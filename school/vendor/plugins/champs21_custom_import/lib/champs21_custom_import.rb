require 'dispatcher'

module Champs21CustomImport
  def self.attach_overrides
    Dispatcher.to_prepare :champs21_custom_import do
      ::Guardian.instance_eval { include GuardianExtension }
    end
  end

  module GuardianExtension
    def self.included(base)
      base.instance_eval do
        attr_accessor_with_default :set_immediate_contact,"NOSET"
        attr_accessor :ward_admission_number
        after_create :update_immediate_contact
      end

      def update_immediate_contact
        if set_immediate_contact.present?
          siblings = Student.find_all_by_admission_no_and_sibling_id(set_immediate_contact.split(','),ward_id)
          siblings.each{|sibling| sibling.update_attributes(:immediate_contact_id => id)}
        end
      end
    end
  end
end