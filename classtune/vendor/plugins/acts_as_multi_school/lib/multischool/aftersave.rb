module MultiSchool

  module Aftersave
    
    def self.included(base)
      base.send :after_save,:reload_cache
      base.send :after_destroy,:reload_cache
      
      base.send :belongs_to,:school
      School.send :has_many,base.table_name,:dependent=>:destroy
      base.send :before_validation,:insert_school_id
      base.send :validates_presence_of,:school_id  
    end

    def reload_cache
      require 'fileutils'
      
      cacheModels = self.class
      
      dir_name = Rails.root.join('public', 'uploads', 'cache', MultiSchool.current_school.id.to_s, 'models', self.class.table_name);
      FileUtils::mkdir_p (dir_name) unless File.exist?(dir_name)
      FileUtils::chmod 0777, dir_name
      Rails.cache.delete("#{MultiSchool.current_school.id}/models/#{self.class.table_name}/#{self.class.table_name}_records")
      data = cacheModels.find(:all)
      Rails.cache.write("#{MultiSchool.current_school.id}/models/#{self.class.table_name}/#{self.class.table_name}_records", data)
    end

    def insert_school_id
      self.school_id = MultiSchool.current_school.id
    end
  end

end
