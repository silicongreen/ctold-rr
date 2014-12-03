require 'acts_as_multi_school'
require 'delayed_seed_school'
require 'champs21_patches'
require 'multischool/champs21_plugin'
require 'multischool/champs21_setting_override'
require 'multischool/authorization_overrides_for_plugin'
require 'multischool/mailer'
require 'school_loader'
require 'multi_school_migration'
require 'dispatcher'

Authorization::AUTH_DSL_FILES << "#{RAILS_ROOT}/vendor/plugins/acts_as_multi_school/config/acts_as_multi_school_auth.rb"

ActiveRecord::Base.send :include, MultiSchool
ActionController::Base.send :include, SchoolLoader
ActionMailer::Base.send :include, MultiSchool::Mailer
Champs21Plugin.send :include, MultiSchool::Champs21PluginWrapper
Champs21Setting.send :include, MultiSchool::Champs21SettingOverride
Champs21Setting.send :unloadable

Dispatcher.to_prepare :acts_as_multischool do
  MultiSchool::AuthorizationOverrides.attach_overrides
  MultiSchool.setup_multi_school_from_yml #loads model names from plugins config/multischool_models.yml
  MultiSchool.setup_multi_school_for_models(Champs21Plugin::MULTI_SCHOOL_MODELS.flatten)
  MultiSchool.setup_multi_school_for_classes(Champs21Plugin::MULTI_SCHOOL_CLASSES.flatten)
  MultiSchool.configure_subdomain
  News.send(:include, Champs21Patches::NewsFragmentCachePatch)
  SmsSetting.send(:include, Champs21Patches::SmsSettingPatch)
  SmsMessage.send(:include, Champs21Patches::SmsLogPatch)
  RecordUpdate.send(:include, Champs21Patches::SchoolSeed)
  PaperclipAttachment.send(:include, Champs21Patches::SelectSchoolToPaperclip)
end