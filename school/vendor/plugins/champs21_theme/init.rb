require 'translator'
require File.join(File.dirname(__FILE__), "lib", "champs21_theme")
require 'dispatcher'
require 'action_view/helpers/asset_tag_helper'

Champs21Plugin.register = {
  :name=>"champs21_theme",
  :description=>"Champs21 Theme"   
}

Dir[File.join("#{File.dirname(__FILE__)}/config/locales/*.yml")].each do |locale|
  I18n.load_path.unshift(locale)
end

if RAILS_ENV == 'development'
  ActiveSupport::Dependencies.load_once_paths.\
    reject!{|x| x =~ /^#{Regexp.escape(File.dirname(__FILE__))}/}
end

Dispatcher.to_prepare :champs21_theme do
  ApplicationHelper.class_eval do
    def get_stylesheets_with_theme
      if Champs21Plugin.can_access_plugin?("champs21_theme")
        @direction = (rtl?) ? 'rtl/' : ''
        stylesheets = [] unless stylesheets
        if controller.controller_path == 'user' and controller.action_name == 'dashboard'
          stylesheets << @direction+'_layouts/dashboard'
        elsif controller.controller_path == 'user' and controller.action_name == 'login'
          stylesheets << @direction+"_layouts/login"
        elsif controller.controller_path == 'user' and controller.action_name == 'forgot_password'
          stylesheets << @direction+"_layouts/forgotpw"
          stylesheets << @direction+"_styles/style"
          return stylesheets
        else
          stylesheets << @direction+'application'
          stylesheets << @direction+'popup.css'
        end
        stylesheets << @direction+'_styles/ui.all.css'
        stylesheets << @direction+'modalbox'
        stylesheets << @direction+'autosuggest-menu.css'
        ["#{@direction}#{controller.controller_path}/#{controller.action_name}"].each do |ss|
          stylesheets << ss
        end
        plugin_css_overrides = Champs21Plugin::CSS_OVERRIDES["#{controller.controller_path}_#{controller.action_name}"]
        stylesheets << plugin_css_overrides.collect{|p| "#{@direction}plugin_css/#{p}"}
        
        Champs21Plugin::ADDITIONAL_LINKS[:icon_class_link].each do |mod|
          if Champs21Plugin.can_access_plugin?(mod[:plugin_name].to_s)
            stylesheets << @direction+(mod[:stylesheet_path].to_s)
          end
        end

        current_theme = ActiveRecord::Base::Configuration.get_config_value "CurrentTheme"
        unless current_theme.nil? or current_theme == 'default'
          unless rtl?
            theme_style_path = "/themes/#{current_theme}/#{current_theme}.css"
          else
            theme_style_path = "/themes/#{current_theme}/#{current_theme}-rtl.css"
          end
          stylesheets << theme_style_path if File.exists?("#{Rails.public_path}#{theme_style_path}")
          calendar_style_path = "/themes/#{current_theme}/calendar.css"
          stylesheets << (File.exists?("#{Rails.public_path}#{calendar_style_path}") ? calendar_style_path : "calendar")
        else
          stylesheets << "calendar"
        end
        return stylesheets
      else
        get_stylesheets_without_theme
      end
    end

    alias_method_chain :get_stylesheets, :theme

    def get_forgotpw_stylesheets_with_theme
      if Champs21Plugin.can_access_plugin?("champs21_theme")
        @direction = (rtl?) ? 'rtl/' : ''
        stylesheets = [] unless stylesheets
        stylesheets << @direction+"_layouts/forgotpw"
        stylesheets << @direction+"_styles/style"
        current_theme = ActiveRecord::Base::Configuration.get_config_value "CurrentTheme"
        unless current_theme.nil? or current_theme == 'default'
          unless rtl?
            theme_style_path = "/themes/#{current_theme}/#{current_theme}.css"
          else
            theme_style_path = "/themes/#{current_theme}/#{current_theme}-rtl.css"
          end
          stylesheets << theme_style_path if File.exists?("#{Rails.public_path}#{theme_style_path}")
        end
        return stylesheets
      else
        get_forgotpw_stylesheets_without_theme
      end
    end

    alias_method_chain :get_forgotpw_stylesheets, :theme
  end
end 
