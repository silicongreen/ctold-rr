module Champs21EmailAlert
module Champs21EmailAlertMsgParse
  def msg_parse(k)
    str={}
    k.each do |w|
      str.merge!( w.gsub(".","_").to_sym=>"#{self.instance_eval(w)}")
    end
    return str
  end
end
end