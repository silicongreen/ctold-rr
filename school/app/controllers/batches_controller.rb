#Champs21
#Copyright 2011 teamCreative Private Limited
#
#This product includes software developed at
#Project Champs21 - http://www.champs21.com/
#
#Licensed under the Apache License, Version 2.0 (the "License");
#you may not use this file except in compliance with the License.
#You may obtain a copy of the License at
#
#  http://www.apache.org/licenses/LICENSE-2.0
#
#Unless required by applicable law or agreed to in writing, software
#distributed under the License is distributed on an "AS IS" BASIS,
#WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#See the License for the specific language governing permissions and
#limitations under the License.

class BatchesController < ApplicationController
  before_filter :init_data,:except=>[:assign_tutor,:update_employees,:assign_employee,:remove_employee,:batches_ajax]
  filter_access_to :all
  before_filter :login_required
  def index
    @batches = @course.batches
  end

  def new
    @batch = @course.batches.build
  end

  def create
    @batch = @course.batches.build(params[:batch])

    if @batch.save
      flash[:notice] = "#{t('flash1')}"
      unless params[:import_subjects].nil?
        msg = []
        msg << "<ol>"
        course_id = @batch.course_id
        @previous_batch = Batch.find(:first,:order=>'id desc', :conditions=>"batches.id < '#{@batch.id }' AND batches.is_deleted = 0 AND course_id = ' #{course_id }'",:joins=>"INNER JOIN subjects ON subjects.batch_id = batches.id  AND subjects.is_deleted = 0")
        unless @previous_batch.blank?
          subjects = Subject.find_all_by_batch_id(@previous_batch.id,:conditions=>'is_deleted=false')
          subjects.each do |subject|
            if subject.elective_group_id.nil?
              Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>@batch.id,:no_exams=>subject.no_exams,
                :max_weekly_classes=>subject.max_weekly_classes,:elective_group_id=>subject.elective_group_id,:credit_hours=>subject.credit_hours,:is_deleted=>false)
            else
              elect_group_exists = ElectiveGroup.find_by_name_and_batch_id(ElectiveGroup.find(subject.elective_group_id).name,@batch.id)
              if elect_group_exists.nil?
                elect_group = ElectiveGroup.create(:name=>ElectiveGroup.find(subject.elective_group_id).name,
                  :batch_id=>@batch.id,:is_deleted=>false)
                Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>@batch.id,:no_exams=>subject.no_exams,
                  :max_weekly_classes=>subject.max_weekly_classes,:elective_group_id=>elect_group.id,:credit_hours=>subject.credit_hours,:is_deleted=>false)
              else
                Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>@batch.id,:no_exams=>subject.no_exams,
                  :max_weekly_classes=>subject.max_weekly_classes,:elective_group_id=>elect_group_exists.id,:credit_hours=>subject.credit_hours,:is_deleted=>false)
              end
            end
            msg << "<li>#{subject.name}</li>"
          end
          msg << "</ol>"
        else
          msg = nil
          flash[:no_subject_error] = "#{t('flash7')}"
        end
      end
      flash[:subject_import] = msg unless msg.nil?
      err = ""
      err1 = "<span style = 'margin-left:15px;font-size:15px,margin-bottom:20px;'><b>#{t('following_pblm_occured_while_saving_the_batch')}</b></span>"
      unless params[:import_fees].nil?
        fee_msg = []
        course_id = @batch.course_id
        @previous_batch = Batch.find(:first,:order=>'id desc', :conditions=>"batches.id < '#{@batch.id }' AND batches.is_deleted = 0 AND course_id = ' #{course_id }'",:joins=>"INNER JOIN category_batches ON category_batches.batch_id = batches.id  INNER JOIN finance_fee_categories on finance_fee_categories.id=category_batches.finance_fee_category_id AND finance_fee_categories.is_deleted = 0 AND is_master= 1")
        unless @previous_batch.blank?
          fee_msg << "<ol>"
          categories = CategoryBatch.find_all_by_batch_id(@previous_batch.id)

          categories.each do |c|

            particulars = FinanceFeeParticular.find(:all,:conditions=>"(receiver_type='Batch' or receiver_type='StudentCategory') and (batch_id=#{@previous_batch.id}) and (finance_fee_category_id=#{c.finance_fee_category_id})")

            #particulars = c.finance_fee_category.fee_particulars.all(:conditions=>"receiver_type='Batch' or receiver_type='StudentCategory'")
            particulars.reject!{|pt|pt.deleted_category}
            fee_discounts = FeeDiscount.find_all_by_finance_fee_category_id_and_batch_id(c.finance_fee_category_id,@previous_batch.id)

            #category_discounts = StudentCategoryFeeDiscount.find_all_by_finance_fee_category_id(c.id)
            unless particulars.blank? and fee_discounts.blank?
              new_category = CategoryBatch.new(:batch_id=>@batch.id,:finance_fee_category_id=>c.finance_fee_category_id)
              if new_category.save
                fee_msg << "<li>#{c.finance_fee_category.name}</li>"
                particulars.each do |p|
                  receiver_id=p.receiver_type=='Batch' ? @batch.id : p.receiver_id
                  new_particular = FinanceFeeParticular.new(:name=>p.name,:description=>p.description,:amount=>p.amount,\
                      :batch_id=>@batch.id,:receiver_id=>receiver_id,:receiver_type=>p.receiver_type)
                  new_particular.finance_fee_category_id = new_category.finance_fee_category_id
                  unless new_particular.save
                    err += "<li> #{t('particular')} #{p.name} #{t('import_failed')}.</li>"
                  end
                end
                fee_discounts.each do |disc|
                  discount_attributes = disc.attributes
                  discount_attributes.delete "type"
                  discount_attributes.delete "finance_fee_category_id"
                  discount_attributes.delete "batch_id"
                  discount_attributes['receiver_id']=@batch.id if disc.receiver_type=='Batch'
                  discount_attributes["batch_id"]= @batch.id
                  discount_attributes["finance_fee_category_id"]= new_category.finance_fee_category_id
                  unless FeeDiscount.create(discount_attributes)
                    err += "<li> #{t('discount ')} #{disc.name} #{t('import_failed')}.</li>"
                  end
                end
                #                category_discounts.each do |disc|
                #                  discount_attributes = disc.attributes
                #                  discount_attributes.delete "type"
                #                  discount_attributes.delete "finance_fee_category_id"
                #                  discount_attributes["finance_fee_category_id"]= new_category.id
                #                  unless StudentCategoryFeeDiscount.create(discount_attributes)
                #                    err += "<li>  #{t(' discount ')} #{disc.name} #{t(' import_failed')}.</li><br/>"
                #                  end
                #                end
              else

                err += "<li>  #{t('category')} #{c.finance_fee_category.name}1 #{t('import_failed')}.</li>"
              end
            else

              err += "<li>  #{t('category')} #{c.finance_fee_category.name}2 #{t('import_failed')}.</li>"

            end
          end
          fee_msg << "</ol>"
          @fee_import_error = false
          flash[:fees_import_error] =nil
        else
          flash[:fees_import_error] =t('no_fee_import_message')
          @fee_import_error = true
        end
      end
      flash[:warn_notice] =  err1 + err unless err.empty?
      flash[:fees_import] =  fee_msg unless fee_msg.nil?

      redirect_to [@course, @batch]
    else
      @grade_types=[]
      gpa = Configuration.find_by_config_key("GPA").config_value
      if gpa == "1"
        @grade_types << "GPA"
      end
      cwa = Configuration.find_by_config_key("CWA").config_value
      if cwa == "1"
        @grade_types << "CWA"
      end
      render 'new'
    end
  end

  def edit
  end

  def update
    if @batch.update_attributes(params[:batch])
      flash[:notice] = "#{t('flash2')}"
      redirect_to [@course, @batch]
    else
      render 'edit'
      #flash[:notice] ="#{t('flash3')}"
      #redirect_to  edit_course_batch_path(@course, @batch)
    end
  end

  def show
    @students = @batch.students
  end

  def destroy
    if @batch.students.empty? and @batch.subjects.empty?
      @batch.inactivate
      flash[:notice] = "#{t('flash4')}"
      redirect_to @course
    else
      flash[:warn_notice] = "<p>#{t('batches.flash5')}</p>" unless @batch.students.empty?
      flash[:warn_notice] = "<p>#{t('batches.flash6')}</p>" unless @batch.subjects.empty?
      redirect_to [@course, @batch]
    end
  end

  def assign_tutor
    @batch = Batch.find_by_id(params[:id])
    if @batch.nil?
      page_not_found
    else
      @assigned_employee=@batch.employees
      @departments = EmployeeDepartment.find(:all,:order=>'name ASC')
    end
  end

  def update_employees
    @employees = Employee.find_all_by_employee_department_id(params[:department_id]).sort_by{|e| e.full_name.downcase}
    @batch = Batch.find_by_id(params[:batch_id])
    @assigned_employee=@batch.employees
    render :update do |page|
      page.replace_html 'employee-list', :partial => 'employee_list'
    end
  end

  def assign_employee
    @batch = Batch.find_by_id(params[:batch_id])
    @employees = Employee.find_all_by_employee_department_id(params[:department_id]).sort_by{|e| e.full_name.downcase}
    @batch.employee_ids=@batch.employee_ids << params[:id]
    @assigned_employee=@batch.employees
    render :update do |page|
      page.replace_html 'employee-list', :partial => 'employee_list'
      page.replace_html 'tutor-list', :partial => 'assigned_tutor_list'
    end
  end

  def remove_employee
    @batch = Batch.find_by_id(params[:batch_id])
    @employees = Employee.find_all_by_employee_department_id(params[:department_id]).sort_by{|e| e.full_name.downcase}
    @batch.employees.delete(Employee.find params[:id])
    @assigned_employee = @batch.employees
    render :update do |page|
      page.replace_html 'employee-list', :partial => 'employee_list'
      page.replace_html 'tutor-list', :partial => 'assigned_tutor_list'
    end
  end

  def batches_ajax
    if request.xhr?
      @course = Course.find_by_id(params[:course_id]) unless params[:course_id].blank?
      @batches = @course.batches.active if @course
      if params[:type]=="list"
        render :partial=>"list"
      end
    end
  end
  private
  def init_data
    @batch = Batch.find params[:id] if ['show', 'edit', 'update', 'destroy'].include? action_name
    @course = Course.find params[:course_id]
  end
end