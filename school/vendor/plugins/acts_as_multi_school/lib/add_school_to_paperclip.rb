module AddSchoolToPaperclip

  def self.included(base)
    base.class_eval do
      begin
        self.attachment_definitions.each do |k,v|
          path_arr = v[:path].split("/")
          path_arr[2] = ":school_id/"+path_arr[2] unless v[:path].include?(":school_id")
          path = path_arr.join("/")
          v[:path] = path
          
          url_arr = v[:url].split("/")
          url_arr[2] = ":school_id/"+url_arr[2] unless v[:url].include?(":school_id")
          url = url_arr.join("/")
          v[:url] = url
        end
        Paperclip.interpolates :school_id do |school, style|
          custom_id_partition school.instance.school.id
        end
      rescue
        # if a model doesn't have attachment, it will be rescued.
      end
    end
  end
  
end
