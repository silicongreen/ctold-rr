require 'authorizations'
require 'champs21'

CHAMPS21_DEFAULTS = {
  :company_name => 'Champs21',
  :company_url  => 'http://www.champs21.com',
  :company_api_url  => 'http://www.api.champs21.dev',
  :mathjaxurl => 'http://latex.uzity.com/MathJax/MathJax.js?config=TeX-AMS-MML_HTMLorMML'
}

#USER_SETTINGS = {} 

if File.exists?("#{RAILS_ROOT}/config/company_details.yml")
  company_settings = YAML.load_file(File.join(RAILS_ROOT,"config","company_details.yml"))
  USER_SETTINGS = {:company_name => company_settings['company_details']['company_name'],
                   :company_url  => company_settings['company_details']['company_url']
  }
end

CHAMPS21_SETTINGS = CHAMPS21_DEFAULTS.merge!(USER_SETTINGS)

Champs21::Authorizations.attach_overrides #attaching methods to declarative authorization
