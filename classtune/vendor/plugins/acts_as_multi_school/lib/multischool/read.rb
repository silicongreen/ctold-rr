module MultiSchool

  module Read
    
    def self.included(base)
      #abort(base.table_name)
      if base.table_name == "payments"
        if MultiSchool.current_school.id != 352
          base.table_name = MultiSchool.current_school.code + "_payments"
        end
        #abort(table_name.inspect)
      end 
    end
    
    def find(*args)
      target_school = MultiSchool.current_school
      if target_school.nil?    
        raise MultiSchool::Exceptions::SchoolNotSelected,"School Not Selected"
      else
        if self.table_name == "payments"
          if MultiSchool.current_school.id != 352
            self.table_name = MultiSchool.current_school.code + "_payments"
          else
            self.table_name = "payments"  
          end
        end 
        if self.table_name == "finance_orders"
          if MultiSchool.current_school.id != 352
            self.table_name = MultiSchool.current_school.code + "_finance_orders"
          else
            self.table_name = "finance_orders"  
          end
        end 
        #abort(self.inspect)
        with_scope(:find => {:conditions  => {:school_id  => target_school.id}}) do
          super
        end
      end

    end

    def count(*args)
      target_school = MultiSchool.current_school

      if target_school.nil?
        raise MultiSchool::Exceptions::SchoolNotSelected,"School Not Selected"
      else
        if self.table_name == "payments"
          if MultiSchool.current_school.id != 352
            self.table_name = MultiSchool.current_school.code + "_payments"
          else
            self.table_name = "payments"  
          end
        end 
        if self.table_name == "finance_orders"
          if MultiSchool.current_school.id != 352
            self.table_name = MultiSchool.current_school.code + "_finance_orders"
          else
            self.table_name = "finance_orders"  
          end
        end 
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
        if self.table_name == "payments"
          if MultiSchool.current_school.id != 352
            self.table_name = MultiSchool.current_school.code + "_payments"
          else
            self.table_name = "payments"  
          end
        end 
        if self.table_name == "finance_orders"
          if MultiSchool.current_school.id != 352
            self.table_name = MultiSchool.current_school.code + "_finance_orders"
          else
            self.table_name = "finance_orders"  
          end
        end 
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
        if self.table_name == "payments"
          if MultiSchool.current_school.id != 352
            self.table_name = MultiSchool.current_school.code + "_payments"
          else
            self.table_name = "payments"  
          end
        end 
        if self.table_name == "finance_orders"
          if MultiSchool.current_school.id != 352
            self.table_name = MultiSchool.current_school.code + "_finance_orders"
          else
            self.table_name = "finance_orders"  
          end
        end 
        with_scope(:find => {:conditions  => {:school_id  => target_school.id}}) do
          super
        end
      end
    end

  end

end
