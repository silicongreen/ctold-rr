class EmployeeSubjectController < ApplicationController
  include ActionView::Helpers::TextHelper
  filter_access_to :all
  before_filter :login_required
  before_filter :default_time_zone_present_time
  
  def add_subject
    employee = @current_user.employee_record
    @employee_subjects = employee.subjects
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    @batches = Batch.active
    @subjects = []
    if request.post? && !params[:subject_assignment].blank? && !params[:subject_assignment][:subject_id].blank?
      subject_id = params[:subject_assignment][:subject_id]
      employee_subject = EmployeesSubject.find_by_employee_id_and_subject_id(employee.id,subject_id)
      if employee_subject.blank?
        EmployeesSubject.create(:employee_id => employee.id, :subject_id => subject_id)
      end
      flash[:notice] = "Subject successfully saved"
      redirect_to :controller => "employee_subject", :action => "add_subject"
    end
  end
  def delete_employee_subject
    employee_subject = EmployeesSubject.find_by_employee_id_and_subject_id(@current_user.employee_record.id,params[:id])
    @subject = Subject.find(employee_subject.subject_id)
    if TimetableEntry.find_all_by_subject_id_and_employee_id(@subject.id,employee_subject.employee_id).blank?
      EmployeesSubject.find(params[:id]).destroy
    else
      TimetableEntry.destroy_all(:subject_id => @subject.id,:employee_id=>employee_subject.employee_id)
      EmployeesSubject.find(params[:id]).destroy
    end
    flash[:notice] = "Subject successfully deleted"
    redirect_to :controller => "employee_subject", :action => "add_subject"
  end
 
  
end
