module MultiSchool

  module Get

    def find(*args)
      
      target_school = MultiSchool.current_school
      if target_school.nil?    
        raise MultiSchool::Exceptions::SchoolNotSelected,"School Not Selected"
      else 
        is_join_has = false
        args.each do |arg|
          if arg.is_a? Hash
            arg.each do |k,v|
              if (k.to_s == "joins")
                is_join_has = true
              elsif k.to_s == "include"  
                is_join_has = true
              elsif k.to_s == "conditions"
                if v.is_a? Hash
                else  
                  is_join_has = true
                end
              end
            end
          end
        end
        
        if is_join_has == false
          found_order = false
          found_conditions = false
          ind = 2
          order_by = ""
          order_type = ""
          conditions_key = []
          conditions_value = []
          datas = Rails.cache.read("#{target_school.id}/models/#{self.table_name}/#{self.table_name}_records")
          unless datas.nil? or datas.blank?
            if args.length == 1
              unless args[0].is_a?(Array)
                ind = -1;
                if args[0].to_s == 'first'
                  ind = 0
                elsif args[0].to_s == 'last'
                  ind = 1
                elsif args[0].to_s == 'all'
                  ind = 2
                end
                
                if ind == -1
                  datas = datas.select{|dt| args[0].to_i == dt.id}
                  datas = datas[0]
                else
                  if ind == 0
                    datas = datas.first
                  elsif ind == 1
                    datas = datas.last
                  end
                end
              else  
                datas = datas.select{|dt| args[0].include?(dt.id.to_s)}
                datas = datas[0]
              end
            else  
              args.each do |arg|
                if arg.is_a? Hash
                  arg.each do |k,v|
                    if k.to_s == "order"
                      found_order = true
                      orders = v.split(" ")
                      if orders.length == 1
                        order_type = "asc"
                      else
                        order_type = orders[1].strip.downcase
                      end
                      order_by = orders[0].strip
                    elsif k.to_s == "conditions"
                      found_conditions = true 
                      if v.is_a? Hash
                        c = 0
                        v.each do |d,e|
                          conditions_key[c] = d.to_s
                          conditions_value[c] = e.to_s
                          c += 1
                        end
                      end
                    end
                  end
                else  
                  if arg.to_s == 'first'
                    ind = 0
                  elsif arg.to_s == 'last'
                    ind = 1
                  elsif arg.to_s == 'all'
                    ind = 2
                  end
                end
              end
              
              if found_order
                unless order_type.nil? or order_type.empty?
                  unless order_by.nil? or order_by.empty?
                    if order_type == "desc"
                      datas = datas.sort_by{|m| m[order_by]}.reverse
                    else
                      datas = datas.sort_by{|m| m[order_by]}
                    end
                  end
                else
                  unless order_by.nil? or order_by.empty?
                    datas = datas.sort_by{|m| m[order_by]}
                  end
                end
              end
              if found_conditions   
                self_columns_name = self.column_names
                self_columns_types = self.columns.map(&:type)
                unless conditions_key.nil? or conditions_key.empty? or conditions_key.blank?
                  p = 0
                  conditions_key.each do |key|
                    if self_columns_name.include?(key)
                      ind_key = self_columns_name.index(key)
                      ind_type = self_columns_types[ind_key].to_s
                      
                      if ind_type.downcase == "integer"
                        datas = datas.select{|dt| dt[key] == conditions_value[p].to_s.strip.to_i}
                      elsif ind_type.downcase == "date"  
                        datas = datas.select{|dt| dt[key] == conditions_value[p].to_s.strip.to_date}
                      else  
                        datas = datas.select{|dt| dt[key] == conditions_value[p].to_s.strip}
                      end
                    end
                    p += 1
                  end
                end
              end
              if ind == 0
                datas = datas.first
              elsif ind == 1
                datas = datas.last
              end
            end
            datas
          else
            with_scope(:find => {:conditions  => {:school_id  => target_school.id}}) do
              super
            end
          end
        else
          with_scope(:find => {:conditions  => {:school_id  => target_school.id}}) do
            super
          end
        end
      end

    end

    def count(*args)
      target_school = MultiSchool.current_school

      if target_school.nil?
        raise MultiSchool::Exceptions::SchoolNotSelected,"School Not Selected"
      else
        with_scope(:find => {:conditions  => {:school_id  => target_school.id}}) do
          super
        end
      end

    end

    def sum(*args)
      target_school = MultiSchool.current_school

      if target_school.nil?
        raise MultiSchool::Exceptions::SchoolNotSelected,"School Not Selected"
      else
        with_scope(:find => {:conditions  => {:school_id  => target_school.id}}) do
          super
        end
      end

    end
    
    def exists?(*args)
      target_school = MultiSchool.current_school
      if target_school.nil?
        raise MultiSchool::Exceptions::SchoolNotSelected,"School Not Selected"
      else
        with_scope(:find => {:conditions  => {:school_id  => target_school.id}}) do
          super
        end
      end
    end

  end

end
