module MultiSchool

  module Read

    def find(*args)
      target_school = MultiSchool.current_school

      if target_school.nil?    
        raise MultiSchool::Exceptions::SchoolNotSelected,"School Not Selected"
      else    
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
