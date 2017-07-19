class SchoolDomain < ActiveRecord::Base
  belongs_to :linkable, :polymorphic=>true

  validates_format_of :domain, :with => /^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/ix
  validates_uniqueness_of :domain, :message=>"this domain is not available"

  RESTRICTED_SUB_DOMAINS = open("#{File.dirname(__FILE__)}/../../config/restricted_domains.txt",'r').map {|line| Regexp.new "^#{line.strip}\\.#{MultiSchool.default_domain.gsub(/\./,'\.')}$"}

  def validate
     self.errors.add(:domain,"this domain is reserved") unless RESTRICTED_SUB_DOMAINS.select{|d| d.match self.domain}.blank?
  end
end
