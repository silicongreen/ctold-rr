class ApiController < ActionController::Base
  require 'rexml/parseexception'

  before_filter :login_required

  def login_required
    if current_user.nil?
      response.header["WWW-Authenticate"]="OAuth realm='Champs21 api'"
      render :status => :unauthorized, :text=>'invalid-request'
    end
  end

  def current_user
    @current_user ||= User.find_by_id session[:user_id]
  end

  if Rails.env.production?
    rescue_from ActiveRecord::RecordNotFound do |exception|
      @xml = Builder::XmlMarkup.new
      logger.info "[Champs21Rescue] AR-Record_Not_Found #{exception.to_s}"
      log_error exception
      render "single_access_tokens/500.xml", :status => 500  and return
    end

    rescue_from REXML::ParseException do |exception|
      @xml = Builder::XmlMarkup.new
      logger.info "[Champs21Rescue] Malformed XML Error #{exception.to_s}"
      log_error exception
      render "single_access_tokens/500.xml", :status => 500  and return
    end
    
    rescue_from NoMethodError do |exception|
      @xml = Builder::XmlMarkup.new
      logger.info "[Champs21Rescue] No method error #{exception.to_s}"
      log_error exception
      render "single_access_tokens/500.xml"  and return
    end

    rescue_from ActionController::InvalidAuthenticityToken do|exception|
      @xml = Builder::XmlMarkup.new
      logger.info "[Champs21Rescue] Invalid Authenticity Token #{exception.to_s}"
      log_error exception
      render "single_access_tokens/500.xml"  and return
    end

    rescue_from Searchlogic::Search::UnknownConditionError do|exception|
      @xml = Builder::XmlMarkup.new
      logger.info "[Champs21Rescue] Unknow Condition Error #{exception.to_s}"
      log_error exception
      render "single_access_tokens/500.xml"  and return
    end
  end

end
