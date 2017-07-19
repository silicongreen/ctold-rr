# Champs21TestPlugin
require 'action_view/helpers/asset_tag_helper'

class Champs21Theme
  unloadable

  def self.general_settings_form
    "configuration/theme_select"
  end

  def self.available_themes
    directory = "#{Rails.public_path}/themes"
    themes = Dir.entries(directory).select {|entry| File.directory? File.join(directory,entry) and !(entry =='.' || entry == '..') }
    return [['Default', 'default']]+themes.collect { |theme| [theme.titleize, theme] }
  end

  def self.selected_theme
    Configuration.get_config_value("CurrentTheme")
  end

  
end


