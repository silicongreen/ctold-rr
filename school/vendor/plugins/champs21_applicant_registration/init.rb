require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_applicant_registration")

Champs21Plugin.register = {
  :name=>"champs21_applicant_registration",
  :description=>"Champs21 Applicant Registration",
  :auth_file=>"config/applicant_regi_auth.rb",
  :more_menu=>{:title=>"applicant_regi_label",:controller=>"applicants_admin",:action=>"index",:target_id=>"more-parent"},
  :multischool_models=>%w{ApplicantAddlAttachment ApplicantAddlFieldGroup ApplicantAddlField ApplicantAddlFieldValue ApplicantAddlValue ApplicantGuardian ApplicantPreviousData Applicant ApplicantRegistrationSetting RegistrationCourse PinGroup PinNumber CoursePin ApplicantAdditionalDetail},
  #:finance=>{:category_name=>"applicant registration"}
}

Champs21ApplicantRegistration.attach_overrides

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end
     

