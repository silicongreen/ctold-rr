# Champs21Dashboard
module Champs21Dashboard

  def self.create(name,model,plugin,icon,&block)
    palette = Dashboard.new(:name=>name,:model_name=>model,:icon=>icon,:plugin=>plugin)
    palette.instance_eval &block if block_given?
    palette
  end
  
end
