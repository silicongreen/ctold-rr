namespace :acts_as_multi_school do

  desc "Migrate school tables for multischool"
  task :migrate => :environment do
    ActiveRecord::Migrator.migrate("vendor/plugins/acts_as_multi_school/db/migrate/",ENV["VERSION"] ? ENV["VERSION"].to_i : nil)
    Rake::Task["db:schema:dump"].invoke if ActiveRecord::Base.schema_format == :ruby
  end

  desc "Make new migration file adding school_id into new models"
  task :build_migrations => :environment do
    MultiSchoolMigration::MakeMigration.new.make_migration
  end

  desc "Rollback multischool migrations"
  task :rollback => :environment do
    ActiveRecord::Migrator.rollback("vendor/plugins/acts_as_multi_school/db/migrate/",ENV["STEP"] ? ENV["STEP"].to_i : 1)
    Rake::Task["db:schema:dump"].invoke if ActiveRecord::Base.schema_format == :ruby
  end

  desc "Copying assets"
  task :asset_copy do
    system "rsync --exclude=.svn -ruv vendor/plugins/acts_as_multi_school/public ."
  end

  desc "Creating default data"
  task :create_default_data do
    settings = MultiSchool.multischool_settings
    unless MultiSchoolGroup.exists?
      admin = MultiSchoolAdmin.find_or_create_by_username(:username=>"admin",:password=>"123456",:email=>"info@champs21.com",:full_name=>"Administrator",:contact_no=>'9123123456',:permission_type=>1)
      group = MultiSchoolGroup.find_or_create_by_name(:name=>settings["organization_details"]["name"],:license_count=>settings["max_school_count"],:whitelabel_enabled=>settings["organization_details"]["whitelabel"])
      admin.multi_school_group = group
      group.school_domains.build(:domain=>(settings["domain"]||"lvh.me"))
      group.save
    end
  end
  
end

namespace :champs21 do
  desc "Multischool - Full setup"
  task :install_champs21_multischool => :environment do
    Rake::Task["db:migrate"].execute
    Rake::Task["champs21:plugins:db:migrate"].execute
    Rake::Task["champs21:plugins:asset_copy"].execute
    Rake::Task["acts_as_multi_school:migrate"].execute
    Rake::Task["acts_as_multi_school:build_migrations"].execute
    Rake::Task["acts_as_multi_school:migrate"].execute
    Rake::Task["acts_as_multi_school:create_default_data"].execute
    Rake::Task["acts_as_multi_school:asset_copy"].execute
  end
  
  desc "Multischool - Update Menu Links"
  task :setup_acl => :environment do
    default_menus_to_set_user_menus = [4,5,6,15,16,17,19,20,21,22,23,24,25,26,27,28,29,30,31,32,36,37,39,40,41,42,43,44,45,46,47,99,119,120,121,122,123,124,125,127,128,129,130]
    default_menus_to_set_not_active_menu = [13]
    menu_links = MenuLink.find_all_by_link_type(["general"])
    menu_links.each do |menus|
      unless default_menus_to_set_user_menus.select{|dm| dm == menus.id}.blank?
        menus.update_attributes(:link_type => 'user_menu')
      end
      unless default_menus_to_set_not_active_menu.select{|dm| dm == menus.id}.blank?
        menus.update_attributes(:link_type => 'not_active_menu')
      end
    end
    
    menu_links_permission = ["50,15"]
    menu_links = MenuLink.find(:all,:conditions => ["link_type = 'own'"])
    menu_links.each do |menus|
      menu_links_permission.each do |mlp|
        a_ml = mlp.split(",")
        menu_id = a_ml[0]
        if menu_id == menus.id
          menus.update_attributes(:reference_id => a_ml[1])
        end
      end
    end
    
    palettes_data = ["1+25,general","2+53,user_menu_student","3+36,general","4+13,general","5+40,general","6+19,general","7+0","8+31,general","9+41,general","10+32,general","11+3,general","12+1,general","13+35,general","14+35,general","15+4,general","16+56,general","17+57,general","18+63,general","19+65,general","20+89,general","21+103,general","22+104,general","23+0","24+55,general","25+53,user_menu_teacher"]
    @palletes = Palette.all(:order=>"id ASC")
    @palletes.each do |pallete|
      palette_id = pallete.id
      found = false
      a_pallette_data = ""
      palettes_data.each do |p|
        p_data = p.split("+")
        if p_data[0].to_s.strip == palette_id.to_s.strip
          a_pallette_data = p_data[1]
          found = true
          break
        end
      end
      if found == true
        a_data = a_pallette_data.split(",")
        pallete.update_attributes(:menu_id => a_data[0])
        unless a_data[1].nil? or a_data[1].blank? or a_data[1].empty?
          pallete.update_attributes(:menu_type => a_data[1])
        else
          pallete.update_attributes(:menu_type => "")
        end
      end
    end
    
    privillege_data = ["1+27,general","2+28,general","3+28,general","4+3,general","5+2,general","6+31,general","7+15,general","8+4,general","9+33,general","10+8,general","11+10,general","12+32,general""13+11,general","14+40,general","15+19,general","16+4,general","17+36,general","18+37,general","19+38,general","20+13,general","21+2,general","22+113,general","23+54,plugins","24+57,plugins","25+58,plugins","26+59,plugins","27+59,plugins","28+61,plugins","29+61,plugins","30+62,plugins","31+57,plugins","32+64,plugins","33+64,plugins","34+65,plugins","35+69,plugins","36+78,plugins","37+78,plugins","38+78,plugins","39+89,plugins","40+99,general","41+99,general","42+103,plugins","43+104,plugins","44+0,general","45+107,plugins"]
    @privilleges = Privilege.find(:all, :conditions => ["privilege_tag_id IS NOT NULL"])
    @privilleges.each do |privillege|
      privillege_id = privillege.id
      found = false
      a_privillege_data = ""
      privillege_data.each do |p|
        p_data = p.split("+")
        if p_data[0].to_s.strip == privillege_id.to_s.strip
          a_privillege_data = p_data[1]
          found = true
          break
        end
      end
      if found == true
        a_data = a_privillege_data.split(",")
        privillege.update_attributes(:menu_id => a_data[0])
        unless a_data[1].nil? or a_data[1].blank? or a_data[1].empty?
          privillege.update_attributes(:menu_type => a_data[1])
        else
          privillege.update_attributes(:menu_type => "")
        end
      end
    end
  end

  desc "Multischool - run db seed for all plugins for all existing schools"
  task :seed_schools => :environment do
    School.find_in_batches do |schools|
      schools.each do |s|
        s.create_champs21_school_seed
        School.update_all({:last_seeded_at => Time.now},{:id  => s.id})
      end
    end
    Rake::Task["champs21:records:update"].execute
  end
  
  namespace :plugins do
    
    desc "Run plugin install rake tasks all champs21 plugins"
    task :asset_copy => :environment do
      Champs21Plugin::AVAILABLE_MODULES.each do |m|
        Rake::Task["#{m[:name]}:install"].execute
      end
    end
    
  end

end
