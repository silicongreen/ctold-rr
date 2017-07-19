class Default < ActiveRecord::Base
  validates_presence_of :key, :value
  
  def get_default_keys
    keys = {}
    keys['employee_category'] = 'Employee Category'
    keys['employee_position'] = 'Employee Position'
    keys['employee_deparnment'] = 'Employee Deparnment'
    keys['employee_grade'] = 'Employee Grade'
    keys['subject'] = 'Subject'
    return Hash[keys.sort]
  end
  
end