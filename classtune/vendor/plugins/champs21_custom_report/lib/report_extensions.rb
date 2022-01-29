
module StudentExtensions
  def self.extended(base)
    base.class_eval do
      cattr_accessor :fields_to_search, :fields_to_display
      
      has_one :course, :through=>:batch
      named_scope :course_id_in,Proc.new {|s|
        {:joins => :batch,:conditions => ['batches.course_id in (?)',s]}
      }

      delegate :first_name,:last_name,:relation,
        :dob,:education,:occupation,
        :income,:email,:office_address_line1,
        :office_address_line2,:city,:state,
        :office_phone1,:mobile_phone,:passport,
        :to=>:immediate_contact,:prefix=>"parent",:allow_nil=>true
  
      base.fields_to_search=YAML::load(File.open(File.dirname(__FILE__)+'/../config/report_fields.yml'))[:fields_to_search][:student]
      base.fields_to_display=YAML::load(File.open(File.dirname(__FILE__)+'/../config/report_fields.yml'))[:fields_to_display][:student]
    end
    super
  end 
  
end

module EmployeeExtensions
  def self.extended(base)
    base.class_eval do
      cattr_accessor :fields_to_search, :fields_to_display
      base.fields_to_search=YAML::load(File.open(File.dirname(__FILE__)+'/../config/report_fields.yml'))[:fields_to_search][:employee]
      base.fields_to_display=YAML::load(File.open(File.dirname(__FILE__)+'/../config/report_fields.yml'))[:fields_to_display][:employee]
    end
    super
  end

end
