# To change this template, choose Tools | Templates
# and open the template in the editor.

module Champs21DataPalette
  module UserMethod
    def self.included(base)
      base.send :after_save, :create_default_palettes

      base.class_eval do
        def own_palettes
          my_palettes = self.palettes.all(:include=>[:palette_queries,:user_palettes])
          permitted_palettes = Palette.compatible_palettes(my_palettes,self.role_symbols)
          unpermitted_palettes = my_palettes - permitted_palettes
          unpermitted_palettes.map{|p| UserPalette.find_by_palette_id_and_user_id(p.id,self.id).destroy}
          return permitted_palettes
        end
        
        def create_default_palettes
          data_pallettes_plugins = {"polls" => "champs21_poll", "placements" => "champs21_placement", "book_return_due" => "champs21_library", "photos_added" => "champs21_gallery", "discussions" => "champs21_discussion", "blogs" => "champs21_blog", "online_meetings" => "champs21_bigbluebutton", "homework" => "champs21_assignment"}
          
          school_id = MultiSchool.current_school.id
          if Champs21Plugin.can_access_plugin?("champs21_data_palette")
            changes_to_be_checked = ['admin','student','employee','parent']
            check_changes = self.changed & changes_to_be_checked
            if (self.new_record? or check_changes.present?)
              UserPalette.find_all_by_user_id(self.id).map{|p| p.destroy}
              default_palettes = []
              if self.admin?
                default_palettes_admin = Palette.find_all_by_name(["employees_on_leave","finance","absent_students","news","events","fees_due"])
                default_palettes_admin.each do |dp| 
                  if data_pallettes_plugins[dp.name].nil?
                    menu_id = dp.menu_id
                    if menu_id > 0
                        menu_links = MenuLink.find_by_id(menu_id)
                        if menu_links.link_type == 'user_menu'
                            school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
                            unless school_menu_links.blank?
                              default_palettes << dp
                            end
                        elsif menu_links.link_type != 'not_active_menu' and menu_links.link_type != 'own'
                          default_palettes << dp
                        end    
                    else
                      default_palettes << dp
                    end
                  else
                    plugins_name = data_pallettes_plugins[dp.name]
                    plugins_data = AvailablePlugin.find_by_associated_id(MultiSchool.current_school.id)
                    unless plugins_data.nil?
                      plugins = plugins_data.plugins
                      if plugins.include?(plugins_name)
                        default_palettes << dp
                      end
                    else
                      unless ARGV[1].nil? or ARGV[1].blank? or ARGV[1].empty? 
                        package_id = ARGV[1]
                        plugins_data = PackageMenu.find(:first, :conditions => ["package_id = ? AND plugins_name = ?",package_id,plugins_name])
                        unless plugins_data.nil?
                          default_palettes << dp
                        end 
                      end
                    end
                  end  
                end
                pallete_names = ["employees_on_leave","finance","absent_students","news","events","fees_due"]
                pallete_required = 5 - default_palettes.length
                if pallete_required > 0
                  i = 0
                  default_palettes_admin = Palette.compatible_palettes(Palette.find(:all, :conditions => ["name NOT IN (?)",pallete_names]),[:admin])
                  default_palettes_admin.each do |dp| 
                    if data_pallettes_plugins[dp.name].nil?
                      menu_id = dp.menu_id
                      if menu_id > 0
                          menu_links = MenuLink.find_by_id(menu_id)
                          if menu_links.link_type == 'user_menu'
                              school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
                              unless school_menu_links.blank?
                                  default_palettes << dp
                                  if i + 1 == pallete_required
                                    break
                                  end
                                  i = i + 1
                              end
                          elsif menu_links.link_type != 'not_active_menu' and menu_links.link_type != 'own'
                              default_palettes << dp
                              if i + 1 == pallete_required
                                break
                              end
                              i = i + 1
                          end    
                      else
                          default_palettes << dp
                          if i + 1 == pallete_required
                            break
                          end
                          i = i + 1
                      end
                    else
                      plugins_name = data_pallettes_plugins[dp.name]
                      plugins_data = AvailablePlugin.find_by_associated_id(MultiSchool.current_school.id)
                      unless plugins_data.nil?
                        plugins = plugins_data.plugins
                        if plugins.include?(plugins_name)
                          default_palettes << dp
                        end
                      else
                        unless ARGV[1].nil? or ARGV[1].blank? or ARGV[1].empty? 
                          package_id = ARGV[1]
                          plugins_data = PackageMenu.find(:first, :conditions => ["package_id = ? AND plugins_name = ?",package_id,plugins_name])
                          unless plugins_data.nil?
                            default_palettes << dp
                            if i + 1 == pallete_required
                              break
                            end
                            i = i + 1
                          end 
                        end
                      end
                    end
                  end
                end
              elsif self.employee?
                teacher_palette = ["employees_on_leave","leave_applications","news","events","timetable"]
                default_palettes_teacher = Palette.find(:all, :conditions => ["name IN (?) AND menu_type IN ('general', 'user_menu_teacher')", teacher_palette])
                default_palettes_teacher.each do |dp| 
                  if data_pallettes_plugins[dp.name].nil?
                    if dp.menu_type == "user_menu_teacher"
                      default_palettes << dp
                    else
                      menu_id = dp.menu_id
                      if menu_id > 0
                          menu_links = MenuLink.find_by_id(menu_id)
                          if menu_links.link_type == 'user_menu'
                              school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
                              unless school_menu_links.blank?
                                default_palettes << dp
                              end
                          elsif menu_links.link_type != 'not_active_menu'
                            default_palettes << dp
                          end    
                      else
                        default_palettes << dp
                      end
                    end
                  else
                    plugins_name = data_pallettes_plugins[dp.name]
                    plugins_data = AvailablePlugin.find_by_associated_id(MultiSchool.current_school.id)
                    unless plugins_data.nil?
                      plugins = plugins_data.plugins
                      if plugins.include?(plugins_name)
                        default_palettes << dp
                      end
                    else
                      unless ARGV[1].nil? or ARGV[1].blank? or ARGV[1].empty? 
                        package_id = ARGV[1]
                        plugins_data = PackageMenu.find(:first, :conditions => ["package_id = ? AND plugins_name = ?",package_id,plugins_name])
                        unless plugins_data.nil?
                          default_palettes << dp
                        end 
                      end
                    end
                  end  
                end
                
                
                pallete_required = 5 - default_palettes.length
                if pallete_required > 0
                  i = 0
                  default_palettes_teacher = Palette.compatible_palettes(Palette.find(:all, :conditions => ["name NOT IN (?)",teacher_palette]),[:employee])
                  default_palettes_teacher.each do |dp| 
                    if data_pallettes_plugins[dp.name].nil?
                      menu_id = dp.menu_id
                      if menu_id > 0
                          menu_links = MenuLink.find_by_id(menu_id)
                          if menu_links.link_type == 'user_menu'
                              school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
                              unless school_menu_links.blank?
                                  default_palettes << dp
                                  if i + 1 == pallete_required
                                    break
                                  end
                                  i = i + 1
                              end
                          elsif menu_links.link_type != 'not_active_menu' and menu_links.link_type != 'own'
                              default_palettes << dp
                              if i + 1 == pallete_required
                                break
                              end
                              i = i + 1
                          end    
                      else
                          default_palettes << dp
                          if i + 1 == pallete_required
                            break
                          end
                          i = i + 1
                      end
                    else
                      plugins_name = data_pallettes_plugins[dp.name]
                      plugins_data = AvailablePlugin.find_by_associated_id(MultiSchool.current_school.id)
                      unless plugins_data.nil?
                        plugins = plugins_data.plugins
                        if plugins.include?(plugins_name)
                          default_palettes << dp
                          if i + 1 == pallete_required
                            break
                          end
                          i = i + 1
                        end
                      else
                        unless ARGV[1].nil? or ARGV[1].blank? or ARGV[1].empty? 
                          package_id = ARGV[1]
                          plugins_data = PackageMenu.find(:first, :conditions => ["package_id = ? AND plugins_name = ?",package_id,plugins_name])
                          unless plugins_data.nil?
                            default_palettes << dp
                            if i + 1 == pallete_required
                              break
                            end
                            i = i + 1
                          end 
                        end
                      end
                    end
                  end
                end
              else
                student_pallettes = ["homework","examinations","timetable","events","fees_due","news"]
                default_palettes_student = Palette.find_all_by_name(student_pallettes)
                default_palettes_student.each do |dp| 
                  if data_pallettes_plugins[dp.name].nil?
                    menu_id = dp.menu_id
                    if menu_id > 0
                        menu_links = MenuLink.find_by_id(menu_id)
                        if menu_links.link_type == 'user_menu'
                            school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
                            unless school_menu_links.blank?
                              default_palettes << dp
                            end
                        elsif menu_links.link_type != 'not_active_menu'
                          default_palettes << dp
                        end    
                    else
                      default_palettes << dp
                    end
                  else
                    plugins_name = data_pallettes_plugins[dp.name]
                    plugins_data = AvailablePlugin.find_by_associated_id(MultiSchool.current_school.id)
                    unless plugins_data.nil?
                      plugins = plugins_data.plugins
                      if plugins.include?(plugins_name)
                        default_palettes << dp
                      end
                    else
                      unless ARGV[1].nil? or ARGV[1].blank? or ARGV[1].empty? 
                        package_id = ARGV[1]
                        plugins_data = PackageMenu.find(:first, :conditions => ["package_id = ? AND plugins_name = ?",package_id,plugins_name])
                        unless plugins_data.nil?
                          default_palettes << dp
                        end 
                      end
                    end
                  end
                end
                pallete_required = 5 - default_palettes.length
                if pallete_required > 0
                  i = 0
                  if self.student
                    default_palettes_student = Palette.compatible_palettes(Palette.find(:all, :conditions => ["name NOT IN (?)",student_pallettes]),[:student])
                  else
                    default_palettes_student = Palette.compatible_palettes(Palette.find(:all, :conditions => ["name NOT IN (?)",student_pallettes]),[:parent])
                  end

                  default_palettes_student.each do |dp| 
                    if data_pallettes_plugins[dp.name].nil?
                      menu_id = dp.menu_id
                      if menu_id > 0
                          menu_links = MenuLink.find_by_id(menu_id)
                          if menu_links.link_type == 'user_menu'
                              school_menu_links = SchoolMenuLink.find(:all, :conditions => ["school_id = ? and menu_link_id = ?",MultiSchool.current_school.id, menu_id], :select => "menu_link_id")
                              unless school_menu_links.blank?
                                  default_palettes << dp
                                  if i + 1 == pallete_required
                                    break
                                  end
                                  i = i + 1
                              end
                          elsif menu_links.link_type != 'not_active_menu' and menu_links.link_type != 'own'
                              default_palettes << dp
                              if i + 1 == pallete_required
                                break
                              end
                              i = i + 1
                          end    
                      else
                          default_palettes << dp
                          if i + 1 == pallete_required
                            break
                          end
                          i = i + 1
                      end
                    else
                      plugins_name = data_pallettes_plugins[dp.name]
                      plugins_data = AvailablePlugin.find_by_associated_id(MultiSchool.current_school.id)
                      unless plugins_data.nil?
                        plugins = plugins_data.plugins
                        if plugins.include?(plugins_name)
                          default_palettes << dp
                          if i + 1 == pallete_required
                            break
                          end
                          i = i + 1
                        end
                      else
                        unless ARGV[1].nil? or ARGV[1].blank? or ARGV[1].empty? 
                          package_id = ARGV[1]
                          plugins_data = PackageMenu.find(:first, :conditions => ["package_id = ? AND plugins_name = ?",package_id,plugins_name])
                          unless plugins_data.nil?
                            default_palettes << dp
                            if i + 1 == pallete_required
                              break
                            end
                            i = i + 1
                          end 
                        end
                      end
                    end
                  end
                end
              end
              default_palettes.each do|p|
                UserPalette.create(:user_id=>self.id,:palette_id=>p.id)
              end
            end
          end
        end

      end
    end

  end

  module DashboardOverride
    def self.included(base)
      base.send :before_filter, :redirect_to_palettes, :only=>[:dashboard]
    end

    def redirect_to_palettes
      if current_user.parent?
        session[:student_id]=params[:id].present?? params[:id] : current_user.guardian_entry.current_ward.id
        Champs21.present_student_id=session[:student_id]
      end
      redirect_to :controller=>"data_palettes", :action=>"index" if can_access_request?(:index,:data_palettes)
      return
    end
  end
end
