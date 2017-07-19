class WardensController < ApplicationController
  before_filter :login_required

  
  def index

  end

  def new
    @warden = Warden.new
    @selected = params[:id]
    @departments = EmployeeDepartment.find(:all)
    @hostel = Hostel.find(:all)
    @employees = []
  end

  def update_employees
    @employees = Employee.find_all_by_employee_department_id params[:department_id]
    render :update do |page|
      page.replace_html 'employee_list', :partial => 'employee_list'
    end
  end

  def create
    @warden = Warden.new(params[:warden])
    @departments = EmployeeDepartment.find(:all)
    @selected = params[:id]
    @hostel = Hostel.find(:all)
    @employees = []
    respond_to do |format|
      if @warden.save
        @hostel = Hostel.find @warden.hostel_id
        format.html { redirect_to(@hostel) }
      else
        format.html { render :action => "new" }
      end
    end
    flash[:notice] = "#{t('warden_assigned_succesfully')}"
  end

  def show
    @hostel=Hostel.find params[:id]
    @warden = Warden.find_all_by_hostel_id(params[:id])
  end

  def destroy
    @warden = Warden.find(params[:id])
    hostel = Hostel.find @warden.hostel_id
    @warden.destroy
    redirect_to hostel
    flash[:notice]= "#{t('warden_removed_successfully')}"
  end

end