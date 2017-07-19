require 'email_alert/champs21_email_alert_msg_parse'
module Champs21EmailAlert
  module EmailAlertOnCreate

    def self.included(base)
      base.send :after_create,:email_send_on_creation
    end

    include Champs21EmailAlertMsgParse

    def email_send_on_creation
      data_selected=Champs21EmailAlert.alert_bodies.find_all_by_model(self.class.name.underscore.to_sym).find{|m| (m.conditions.nil? or instance_eval(&m.conditions) and m.hook==:after_create) and EmailAlert.active.collect(&:model_name).include?(m.name.to_s)} and Champs21Plugin.can_access_plugin?("champs21_email_alert")
      if data_selected.present?
        data_selected.mail_to.each do|send_to|
          if EmailAlert.find_by_model_name(data_selected.name.to_s).mail_to.include?(send_to.recipient)
            instance_eval(&send_to.to).uniq.select{|to| to.first.present?}.each do |recp|
              instance_eval(&send_to.stud_name).nil?? a={:recipient_name=>recp.last} : a={:recipient_name=>recp.last,:student_name=>instance_eval(&send_to.stud_name)[recp.first]}
              sender=""
              message_variables= msg_parse(send_to.message)
              message="#{t("#{send_to.recipient.to_s}_#{data_selected.name.to_s}",message_variables.merge!(a))}"
              subject_variables=msg_parse(send_to.subject)
              subject="#{t("#{send_to.recipient}_subject_#{data_selected.name}",subject_variables.merge!(a))}"
              footer="#{t("footer",{:school_name=>Configuration.get_config_value('InstitutionName'),:school_details=>msg_parse(send_to.footer).values.first})}"               
                Delayed::Job.enqueue(Champs21EmailAlertEmailMaker.new(sender,subject,message,recp.first,Champs21.hostname,footer,Champs21.rtl))
            end
          end
        end
      end
    end

  end
end
