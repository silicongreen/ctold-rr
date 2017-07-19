module MultiSchool
  module Mailer
    def self.included(base)
      base.class_eval do
        def initialize_with_school(method_name=nil,*parameters)
          @current_school = MultiSchool.current_school ? MultiSchool.current_school : \
            ((MultiSchool.current_school_group.try(:type) == "MultiSchoolGroup") ? MultiSchool.current_school_group : nil)
          self.class.smtp_settings = SMTP_SETTINGS
          if @current_school
            self.class.delivery_method = :smtp
            self.class.smtp_settings = @current_school.effective_smtp_settings
          end
          initialize_without_school(method_name,*parameters)
        end

        alias_method_chain :initialize, :school

        def create_with_school!(method_name, *parameters)
          create_without_school!(method_name, *parameters)
          if @current_school.nil? || Champs21Setting.smtp_settings == self.class.smtp_settings
            @from = "'Champs21' <noreply@champs21.com>"
            @headers['return-path'] = 'noreply@champs21.com'
          end
          @mail = create_mail
        end

        alias_method_chain :create!, :school
        
      end
    end
  end
end
