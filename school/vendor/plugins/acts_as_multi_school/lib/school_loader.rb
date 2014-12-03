module SchoolLoader

  def self.included(base)
    base.send :before_filter, :load_school
    base.send :before_filter, :request_scope_validity
    base.send :before_filter, :set_title
  end

  private
  
  def load_school
    domain = SchoolDomain.find_by_domain(request.host)
    @linkable = domain.linkable unless domain.blank?
    render :file=>"public/404.html",:status=>404 and return unless @linkable.present?
    if domain.linkable_type=="School"
      if @linkable.access_locked
        render :file=>"public/403.html", :status=>403 and return
      end
      MultiSchool.current_school= @linkable
      session[:current_school_group] = ""
    else
      MultiSchool.current_school_group = @linkable
      session[:current_school_group] = @linkable.id
      redirect_to schools_url and return if request.request_uri == "/"
    end

  end

  def request_scope_validity
    if @linkable && @linkable.type.to_s=="School"
      render :file=>"public/404.html",:status=>404 and return if self.class.superclass.to_s=="MultiSchoolController"
    elsif (@linkable && @linkable.type.to_s=="MultiSchoolGroup") || @linkable.nil?
      render :file=>"public/404.html",:status=>404 and return if self.class.superclass.to_s!="MultiSchoolController"
    end
  end

  def set_title
    @title = Champs21Setting.company_details[:company_name]
  end
  
end
