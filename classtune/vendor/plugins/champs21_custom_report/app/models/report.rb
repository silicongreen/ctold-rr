class Report < ActiveRecord::Base
  
  has_many :report_queries,:dependent => :destroy
  has_many :report_columns,:dependent => :destroy

  accepts_nested_attributes_for  :report_columns
  accepts_nested_attributes_for :report_queries,
    :reject_if => proc { |attributes|
    attributes['query'].blank? and (attributes['date_query(1i)'].blank? or
        attributes['date_query(2i)'].blank? or
        attributes['date_query(3i)'].blank?)
  }

  validates_presence_of :name, :report_columns, :report_queries

  def after_initialize
    unless model_object.nil?
      model_object.extend JoinScope
      model_object.extend AdditionalFieldScope
    end
  end
  
  def search_param
    sp={}
    self.report_queries.each do |rq|
      sp[rq.query_string]= rq.query unless ['join','additional'].include? rq.column_type
    end
    sp[:join_params]=join_params
    sp[:additional_field_params]=additional_field_params
    sp
  end
  
  def join_params
    jp={}
    cond={}
    join_queries=report_queries.group_by(&:column_type)['join']
    unless join_queries.blank?
      join_queries = join_queries.group_by(&:table_name)
      jp[:joins] = join_queries.keys.collect{|k| eval(k).table_name.singularize.to_sym}
      join_queries.each do |k,rqs|
        cond[k]=[]
        rqs.each do |rq|
          cond[k] << rq.make_query
        end
      end
      q_str=[]
      cond.values.each do |str|
        q_str << "(#{str.join(" OR ")})"
      end
      jp[:conditions]=[q_str.join(" AND ")]
    end
    jp
  end
  
  def additional_field_params
    ap={}
    cond={}
    additional_field_queries = report_queries.group_by(&:column_type)['additional']
    unless additional_field_queries.blank?
      additional_field_queries = additional_field_queries.group_by(&:table_name)
      ap[:joins] = additional_field_queries.keys.collect{|k| eval(k).table_name.to_sym}
      additional_field_queries.each do |k,rqs|
        cond[k]=[]
        rqs.each do |rq|
          cond[k] << rq.make_query_for_additional_field
        end
      end
      q_str=[]
      cond.values.each do |str|
        q_str << "(#{str.join(" OR ")})"
      end
      ap[:conditions]=[q_str.join(" OR ")]
    end
    ap
  end

  def include_param
    ip=[]
    model_name = Kernel.const_get(self.model)    
    self.report_columns.each do |rc|
      (ip << rc.method.to_sym ) if model_name.fields_to_search[:association].include? rc.method.to_sym
    end
    ip
  end
  def to_csv
    std = 0
    csv = FasterCSV.generate do |csv|
      
      cols = []
      self.report_columns.each do |rc|
        if t(rc.title) == "Parent first name" || t(rc.title) == "Parent last name" || t(rc.title) == "Parent relation"
            std = 1
        else
          cols << t(rc.title)
        end  
      end
      if std == 1 
        cols << "Father"
        cols << "Mobile"
        cols << "Email"
        cols << "Mother"
        cols << "Mobile"
        cols << "Email"
      end
      csv << cols
      
      search_results = model_object.report_search(self.search_param).all(:include=>self.include_param)
      search_results.uniq.each do |obj|
        cols = []
        self.report_columns.each do |col|
          if t(col.title) == "Parent first name" || t(col.title) == "Parent last name" || t(col.title) == "Parent relation"
           
          else
            cols << "#{obj.send(col.method)}"
          end
        end
        if std == 1
          count_guardian = 0
          father = 0
          guardians = GuardianStudents.find_all_by_student_id(obj.id)
          unless guardians.blank?
            guardians.each do |gur|
              gurdian = Guardian.find_by_id(gur.guardian_id)
              unless gurdian.blank?
                if gurdian.relation.index("Father") || gurdian.relation.index("father")
                  cols << gurdian.first_name.to_s+" "+gurdian.last_name.to_s
                  cols << gurdian.mobile_phone
                  cols << gurdian.email
                  count_guardian = count_guardian+1
                  father = 1
                  break
                end
              end
            end
            
            if father == 0
              cols << ""
              cols << ""
              cols << ""
            end
            
            guardians.each do |gur|
              gurdian = Guardian.find_by_id(gur.guardian_id)
              unless gurdian.blank?
                if gurdian.relation.index("Mother") || gurdian.relation.index("mother")
                  cols << gurdian.first_name.to_s+" "+gurdian.last_name.to_s
                  cols << gurdian.mobile_phone
                  cols << gurdian.email
                  count_guardian = count_guardian+1
                  break
                end
              end
            end
            
            if count_guardian == 0 || (count_guardian == 1 && father = 1)
              cols << ""
              cols << ""
              cols << ""
            end
            
          else
            cols << ""
            cols << ""
            cols << ""
            cols << ""
            cols << ""
            cols << ""
          end  
          
        end
        
        csv << cols
      end
    end
    csv
  end
  
  def to_csv_sjws_2
    std = 0
    p_data = 0
    p_data2 = 0
    p_data3 = 0
    csv = FasterCSV.generate do |csv|
      
      cols = []
      cols << "SL"
      self.report_columns.each do |rc|
        if (t(rc.title) == "Parent first name" || t(rc.title) == "Parent last name" || t(rc.title) == "Parent relation") && p_data == 0
            p_data = 1
            cols << "Father's Name"
            cols << "Mother's Name"
            
        elsif t(rc.title) == "Parent mobile phone" && p_data2 == 0
            cols << "Father's Mobile"
            cols << "Mother's Mobile"
            p_data2 = 1
        elsif t(rc.title) == "Parent email" && p_data3 == 0
            cols << "Father's Email"
            cols << "Mother's Email"
            p_data3 = 1
        elsif t(rc.title) != "Parent first name" && t(rc.title) != "Parent last name" && t(rc.title) != "Parent relation" && t(rc.title) != "Parent mobile phone" && t(rc.title) != "Parent email"    
          cols << t(rc.title)
        end  
      end
     
      csv << cols
      
      search_results = model_object.report_search(self.search_param).all(:include=>self.include_param)
      sl = 0
      search_results.uniq.each do |obj|
        p_data = 0
        p_data2 = 0
        p_data3 = 0
        sl = sl+1
        cols = []
        cols << sl
        guardians = GuardianStudents.find_all_by_student_id(obj.id)
        self.report_columns.each do |col|
          
          if (t(col.title) == "Parent first name" || t(col.title) == "Parent last name" || t(col.title) == "Parent relation") && p_data == 0
                p_data = 1
                unless guardians.blank?
                  guardians.each do |gur|
                  gurdian = Guardian.find_by_id(gur.guardian_id)
                  unless gurdian.blank?
                    if gurdian.relation.index("Father") || gurdian.relation.index("father")
                      cols << gurdian.first_name.to_s+" "+gurdian.last_name.to_s
                      p_data = 2
                      break
                    end
                  end
                  end 
                  if p_data == 1
                    cols << "" 
                    p_data = 2
                  end

                    guardians.each do |gur|
                    gurdian = Guardian.find_by_id(gur.guardian_id)
                    unless gurdian.blank?
                      if gurdian.relation.index("Mother") || gurdian.relation.index("mother")
                        cols << gurdian.first_name.to_s+" "+gurdian.last_name.to_s
                        p_data = 3
                        break
                      end
                    end
                  end 
                  if p_data == 2
                    cols << "" 
                    p_data = 3
                  end
                else
                  cols << "" 
                  cols << "" 
                end
            
          elsif t(col.title) == "Parent mobile phone" && p_data2 == 0
                
                p_data2 = 1
                unless guardians.blank?
                  guardians.each do |gur|
                  gurdian = Guardian.find_by_id(gur.guardian_id)
                  unless gurdian.blank?
                    if gurdian.relation.index("Father") || gurdian.relation.index("father")
                      cols << gurdian.mobile_phone
                      p_data2 = 2
                      break
                    end
                  end
                  end 
                  if p_data2 == 1
                    cols << "" 
                    p_data2 = 2
                  end

                    guardians.each do |gur|
                    gurdian = Guardian.find_by_id(gur.guardian_id)
                    unless gurdian.blank?
                      if gurdian.relation.index("Mother") || gurdian.relation.index("mother")
                        cols << gurdian.mobile_phone
                        p_data2 = 3
                        break
                      end
                    end
                  end 
                  if p_data2 == 2
                    cols << "" 
                    p_data2 = 3
                  end
                else
                  cols << "" 
                  cols << "" 
                end
          elsif t(col.title) == "Parent email" && p_data3 == 0
                
                p_data3 = 1
                unless guardians.blank?
                  guardians.each do |gur|
                  gurdian = Guardian.find_by_id(gur.guardian_id)
                  unless gurdian.blank?
                    if gurdian.relation.index("Father") || gurdian.relation.index("father")
                      cols << gurdian.email
                      p_data3 = 2
                      break
                    end
                  end
                  end 
                  if p_data3 == 1
                    cols << "" 
                    p_data3 = 2
                  end

                    guardians.each do |gur|
                    gurdian = Guardian.find_by_id(gur.guardian_id)
                    unless gurdian.blank?
                      if gurdian.relation.index("Mother") || gurdian.relation.index("mother")
                        cols << gurdian.email
                        p_data3 = 3
                        break
                      end
                    end
                  end 
                  if p_data3 == 2
                    cols << "" 
                    p_data3 = 3
                  end
                else
                  cols << "" 
                  cols << "" 
                end
          elsif t(col.title) != "Parent first name" && t(col.title) != "Parent last name" && t(col.title) != "Parent relation" && t(col.title) != "Parent mobile phone" && t(col.title) != "Parent email"
            cols << "#{obj.send(col.method)}"
          end
        end
        
        
        csv << cols
      end
    end
    csv
  end
  
  def to_csv_sjws
    std = 0
    csv = FasterCSV.generate do |csv|
      
      cols = []
      self.report_columns.each do |rc|
        if t(rc.title) == "Parent first name" || t(rc.title) == "Parent last name" || t(rc.title) == "Parent relation"
            std = 1
        elsif t(rc.title) == "First Name"
          cols << "Full Name"
        elsif t(rc.title) == "Last Name" || t(rc.title) == "Surname" 
          
        elsif t(rc.title) == "Middle Name"
          
        else
          cols << t(rc.title)
        end  
      end
      if std == 1 
        cols << "Father Details"
        cols << "Mother Details"
      end
      csv << cols
      
      search_results = model_object.report_search(self.search_param).all(:include=>self.include_param)
      search_results.uniq.each do |obj|
        cols = []
     
        self.report_columns.each do |col|
          if t(col.title) == "Parent first name" || t(col.title) == "Parent last name" || t(col.title) == "Parent relation"
           
          elsif t(col.title) == "First Name"
              cols <<  "#{obj.send("full_name")}"
          elsif t(col.title) == "Middle Name"
              
          elsif t(col.title) == "Last Name" || t(col.title) == "Surname" 
              
          else
            cols << "#{obj.send(col.method)}"
          end
        end
        if std == 1
          count_guardian = 0
          father = 0
          guardians = GuardianStudents.find_all_by_student_id(obj.id)
          unless guardians.blank?
            guardians.each do |gur|
              gurdian = Guardian.find_by_id(gur.guardian_id)
              unless gurdian.blank?
                if gurdian.relation.index("Father") || gurdian.relation.index("father")
                  cols << gurdian.first_name.to_s+" "+gurdian.last_name.to_s
                  count_guardian = count_guardian+1
                  father = 1
                  break
                end
              end
            end
            
            if father == 0
              cols << ""
            end
            
            guardians.each do |gur|
              gurdian = Guardian.find_by_id(gur.guardian_id)
              unless gurdian.blank?
                if gurdian.relation.index("Mother") || gurdian.relation.index("mother")
                  cols << gurdian.first_name.to_s+" "+gurdian.last_name.to_s
                  count_guardian = count_guardian+1
                  break
                end
              end
            end
            
            if count_guardian == 0 || (count_guardian == 1 && father = 1)
              cols << ""
            end
            
          else
            cols << ""
            cols << ""
          end 
          csv << cols
          
          cols = []
          self.report_columns.each do |col|
            if t(col.title) == "Parent first name" || t(col.title) == "Parent last name" || t(col.title) == "Parent relation"

            elsif t(col.title) == "First Name"
                cols <<  ""
            elsif t(col.title) == "Middle Name"

            elsif t(col.title) == "Last Name" || t(col.title) == "Surname" 

            else
              cols << ""
            end
          end
          
          count_guardian = 0
            father = 0
            guardians = GuardianStudents.find_all_by_student_id(obj.id)
            unless guardians.blank?
              guardians.each do |gur|
                gurdian = Guardian.find_by_id(gur.guardian_id)
                unless gurdian.blank?
                  if gurdian.relation.index("Father") || gurdian.relation.index("father")
                    cols << gurdian.mobile_phone
                    count_guardian = count_guardian+1
                    father = 1
                    break
                  end
                end
              end

              if father == 0
                cols << ""
              end

              guardians.each do |gur|
                gurdian = Guardian.find_by_id(gur.guardian_id)
                unless gurdian.blank?
                  if gurdian.relation.index("Mother") || gurdian.relation.index("mother")
                    cols << gurdian.mobile_phone
                    count_guardian = count_guardian+1
                    break
                  end
                end
              end

              if count_guardian == 0 || (count_guardian == 1 && father = 1)
                cols << ""
              end

            else
              cols << ""
              cols << ""
            end
            csv << cols
            
            cols = []
            self.report_columns.each do |col|
                if t(col.title) == "Parent first name" || t(col.title) == "Parent last name" || t(col.title) == "Parent relation"

                elsif t(col.title) == "First Name"
                    cols <<  ""
                elsif t(col.title) == "Middle Name"

                elsif t(col.title) == "Last Name" || t(col.title) == "Surname" 

                else
                  cols << ""
                end
              end
          
            count_guardian = 0
            father = 0
            guardians = GuardianStudents.find_all_by_student_id(obj.id)
            unless guardians.blank?
              guardians.each do |gur|
                gurdian = Guardian.find_by_id(gur.guardian_id)
                unless gurdian.blank?
                  if gurdian.relation.index("Father") || gurdian.relation.index("father")
                    cols << gurdian.email
                    count_guardian = count_guardian+1
                    father = 1
                    break
                  end
                end
              end

              if father == 0
                cols << ""
              end

              guardians.each do |gur|
                gurdian = Guardian.find_by_id(gur.guardian_id)
                unless gurdian.blank?
                  if gurdian.relation.index("Mother") || gurdian.relation.index("mother")
                    cols << gurdian.email
                    count_guardian = count_guardian+1
                    break
                  end
                end
              end

              if count_guardian == 0 || (count_guardian == 1 && father = 1)
                cols << ""
              end

            else
              cols << ""
              cols << ""
            end 
            csv << cols
        end
        
        
      end
      
    end
    csv
  end

  def model_object
    Kernel.const_get(self.model) unless self.model.nil?
  end
end
  
