# To change this template, choose Tools | Templates
# and open the template in the editor.

module Champs21Dashboard
  module UserMethodDashboard
    def self.included(base)
      

      base.class_eval do
        def own_dashboard
          my_palettes = self.dashboards.all(:include=>[:dashboard_queries,:user_dashboards])
          permitted_palettes = Dashboard.compatible_palettes(my_palettes,self.role_symbols)
          unpermitted_palettes = my_palettes - permitted_palettes
          unpermitted_palettes.map{|p| UserDashboard.find_by_dashboard_id_and_user_id(p.id,self.id).destroy}
          return permitted_palettes
        end
        
        

      end
    end

  end

  
end
