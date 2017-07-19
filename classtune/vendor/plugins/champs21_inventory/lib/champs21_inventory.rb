module Champs21Inventory
  def self.dependency_check(record,type)
    if type == "permanant"
      if record.class.to_s == "Employee"
        return true if Indent.count(:joins=>"LEFT OUTER JOIN `users` ON `users`.id = `indents`.user_id LEFT OUTER JOIN `users` managers_indents ON `managers_indents`.id = `indents`.manager_id",:conditions=>["(indents.user_id=? or indents.manager_id=?) and indents.is_deleted ='0'",record.user_id,record.user_id]) > 0
      end
    end
    return false
  end
end
