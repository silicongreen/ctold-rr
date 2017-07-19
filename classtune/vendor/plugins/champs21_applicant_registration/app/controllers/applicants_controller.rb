class ApplicantsController < ApplicationController
  require 'authorize_net'
  helper :authorize_net
  layout :choose_layout
  before_filter :login_required,:except=>[:new,:create,:complete,:show_form,:print_application,:show_pin_entry_form,:get_amount,:registration_return]
  before_filter :load_common
  before_filter :load_lang,:only=>[:new,:create,:complete,:show_form,:show_pin_entry_form]
  before_filter :set_precision
  def choose_layout
    return 'application' if action_name == 'edit' or action_name == 'update'
    'applicants'
  end

  def new
    @courses = RegistrationCourse.active.all(:order => "courses.course_name",:joins => :course)
  end

  def show_pin_entry_form
    if request.xhr?
      render :update do |page|
        @course = RegistrationCourse.find(params[:course_id])
        unless @course.nil?
          if !course_pin_system_registered_for_course(@course.course_id)
            page.replace_html 'pin_entry_form',:partial => 'pin_entry_form'
            page.replace_html 'form',:text => ''
          else
            @countries = Country.all
            @applicant = Applicant.new
            @applicant_guardian = @applicant.build_applicant_guardian
            @selected_value = Configuration.default_country
            @applicant.build_applicant_guardian
            @applicant.build_applicant_previous_data
            @applicant.applicant_addl_attachments.build
            @addl_field_groups = ApplicantAddlFieldGroup.find(:all,:conditions=>{:registration_course_id=>params[:course_id],:is_active=>true})
            @subjects = @course.course.batches.map(&:all_elective_subjects).flatten.compact.map(&:code).compact.flatten.uniq
            @ele_subjects={}
            subject_amounts=@course.course.subject_amounts
            elective_subject_amounts= subject_amounts.find_all_by_code(@subjects)
            @subjects.each do |sub|
              subject=elective_subject_amounts.find_by_code(sub)
              @ele_subjects.merge!(sub=>subject ? subject.amount.to_f: 0 )
            end
            @selected_subject_ids = @applicant.subject_ids.nil? ? [] : @applicant.subject_ids
            additional_mandatory_fields = StudentAdditionalField.active.all(:conditions => {:is_mandatory => true})
            additional_fields = StudentAdditionalField.find_all_by_id(@course.additional_field_ids).compact
            @additional_fields = (additional_mandatory_fields + additional_fields).uniq.compact.flatten
            @applicant_additional_details = @applicant.applicant_additional_details
            @currency = currency
            if @course.subject_based_fee_colletion == true
              normal_subjects=@course.course.batches.map(&:normal_batch_subject).flatten.compact.map(&:code).compact.flatten.uniq
              @normal_subject_amount=subject_amounts.find(:all,:conditions => {:code => normal_subjects}).flatten.compact.map(&:amount).sum
            else
              @normal_subject_amount = @course.amount.to_f
            end
            page.replace_html 'form',:partial => 'form'
            page.replace_html 'pin_entry_form',:text => ''
          end
        else
          page.replace_html 'form',:text => ''
          page.replace_html 'pin_entry_form',:text => ''
        end
      end
    else
      flash[:notice] = t('flash_register')
      redirect_to new_applicant_path
    end
  end

  def show_form
    if request.post?
      pin_no = PinNumber.find_by_number(params[:pin][:pin_number])
      if pin_no.nil?
        flash[:notice] = t('flash5')
        redirect_to new_applicant_path
      else
        if Date.today > pin_no.pin_group.valid_till.to_date or Date.today < pin_no.pin_group.valid_from.to_date
          flash[:notice] = t('flash2')
          redirect_to new_applicant_path
        else
          unless pin_no.is_active?
            flash[:notice] = t('flash3')
            redirect_to new_applicant_path
          else
            if pin_no.is_registered?
              flash[:notice] = t('flash4')
              redirect_to new_applicant_path
            else
              unless pin_no.pin_group.course_ids.include? params[:pin][:course_id]
                flash[:notice] = t('flash5')
                redirect_to new_applicant_path
              else
                @applicant = Applicant.new
                @countries = Country.all
                @course = RegistrationCourse.find(params[:pin][:course_id])
                @subjects = @course.course.batches.map(&:all_elective_subjects).flatten.compact.map(&:code).compact.flatten.uniq
                @ele_subjects={}
                subject_amounts=@course.course.subject_amounts
                elective_subject_amounts= subject_amounts.find_all_by_code(@subjects)
                @subjects.each do |sub|
                  subject=elective_subject_amounts.find_by_code(sub)
                  @ele_subjects.merge!(sub=>subject ? subject.amount.to_f: 0 )
                end
                @selected_subject_ids = @applicant.subject_ids.nil? ? [] : @applicant.subject_ids
                @pin_number = params[:pin][:pin_number]
                @applicant_guardian = @applicant.build_applicant_guardian
                @selected_value = Configuration.default_country
                @applicant.build_applicant_guardian
                @applicant.build_applicant_previous_data
                @applicant.applicant_addl_attachments.build
                @addl_field_groups = ApplicantAddlFieldGroup.find(:all,:conditions=>{:registration_course_id=>@course.id,:is_active=>true})
                additional_mandatory_fields = StudentAdditionalField.active.all(:conditions => {:is_mandatory => true})
                additional_fields = StudentAdditionalField.find_all_by_id(@course.additional_field_ids).compact
                @additional_fields = (additional_mandatory_fields + additional_fields).uniq.compact.flatten
                @applicant_additional_details = @applicant.applicant_additional_details
                @currency = currency
                if @course.subject_based_fee_colletion == true
                  normal_subjects=@course.course.batches.map(&:normal_batch_subject).flatten.compact.map(&:code).compact.flatten.uniq
                  @normal_subject_amount=subject_amounts.find(:all,:conditions => {:code => normal_subjects}).flatten.compact.map(&:amount).sum
                else
                  @normal_subject_amount = @course.amount.to_f
                end
              end
            end
          end
        end
      end
    else
      flash[:notice] = t('flash_register')
      redirect_to new_applicant_path
    end
  end

  def create
    if Champs21Plugin.can_access_plugin?("champs21_pay")
      if (PaymentConfiguration.config_value("enabled_fees").present? and PaymentConfiguration.config_value("enabled_fees").include? "Application Registration")
        @active_gateway = PaymentConfiguration.config_value("champs21_gateway")
        if @active_gateway == "Paypal"
          @merchant_id = PaymentConfiguration.config_value("paypal_id")
          @merchant ||= String.new
          @certificate = PaymentConfiguration.config_value("paypal_certificate")
          @certificate ||= String.new
        elsif @active_gateway == "Authorize.net"
          @merchant_id = PaymentConfiguration.config_value("authorize_net_merchant_id")
          @merchant_id ||= String.new
          @certificate = PaymentConfiguration.config_value("authorize_net_transaction_password")
          @certificate ||= String.new
        end
      end
    end
    current_school_name = Configuration.find_by_config_key('InstitutionName').try(:config_value)
    @currency = currency
    @courses = RegistrationCourse.active
    if params[:applicant][:subject_ids].nil?
      params[:applicant][:subject_ids]=[]
    else
      params[:applicant][:subject_ids].each_with_index do |e,i|
        params[:applicant][:subject_ids][i]=(e.split-e.split.last.to_a).join(" ")
      end
    end
    @course = RegistrationCourse.find(params[:applicant][:registration_course_id])
    @pin_number = params[:applicant][:pin_number]
    @applicant = Applicant.new(params[:applicant])
    @subjects = @course.course.batches.map(&:all_elective_subjects).flatten.compact.map(&:code).compact.flatten.uniq
    @ele_subjects={}
    subject_amounts=@course.course.subject_amounts
    elective_subject_amounts= subject_amounts.find_all_by_code(@subjects)
    @subjects.each do |sub|
      subject=elective_subject_amounts.find_by_code(sub)
      @ele_subjects.merge!(sub=>subject ? subject.amount.to_f: 0 )
    end
    @selected_subject_ids = @applicant.subject_ids.nil? ? [] : @applicant.subject_ids
    @applicant_guardian = @applicant.build_applicant_guardian(params[:applicant_guardian])
    @applicant_previous_data = @applicant.build_applicant_previous_data(params[:applicant_previous_data])
    @addl_field_groups = ApplicantAddlFieldGroup.find(:all,:conditions=>{:registration_course_id=>@applicant.registration_course_id,:is_active=>true},\
        :include=>[:applicant_addl_fields=>[:applicant_addl_field_values]])
    additional_mandatory_fields = StudentAdditionalField.active.all(:conditions => {:is_mandatory => true})
    additional_fields = StudentAdditionalField.find_all_by_id(@course.additional_field_ids).compact
    @additional_fields = (additional_mandatory_fields + additional_fields).uniq.compact.flatten
    @applicant_additional_details = @applicant.applicant_additional_details
    @currency = currency
    if @course.subject_based_fee_colletion == true
      normal_subjects=@course.course.batches.map(&:normal_batch_subject).flatten.compact.map(&:code).compact.flatten.uniq
      @applicant.normal_subject_ids=normal_subjects
      if params[:applicant][:subject_ids].present?
        @ele_subject_amount=subject_amounts.find(:all,:conditions => {:code => params[:applicant][:subject_ids]}).flatten.compact.map(&:amount).sum
      else
        @ele_subject_amount=0
      end
      @normal_subject_amount=subject_amounts.find(:all,:conditions => {:code => normal_subjects}).flatten.compact.map(&:amount).sum
      @registration_amount = @normal_subject_amount+@ele_subject_amount
      @applicant.amount = @registration_amount.to_f
    else
      @applicant.amount = @course.amount.to_f
    end

    if @applicant.valid?
      pin_no = PinNumber.find_by_number(@applicant.pin_number)
      pin_no.update_attributes(:is_registered => true) unless pin_no.nil?
      if @course.include_additional_details == true
        @error=false
        mandatory_fields = StudentAdditionalField.find(:all, :conditions=>{:is_mandatory=>true, :status=>true})
        mandatory_fields.each do|m|
          unless params[:applicant_additional_details][m.id.to_s.to_sym].present?
            @applicant.errors.add_to_base("#{m.name} must contain atleast one selected option.")
            @error=true
          else
            if params[:applicant_additional_details][m.id.to_s.to_sym][:additional_info]==""
              @applicant.errors.add_to_base("#{m.name} cannot be blank.")
              @error=true
            end
          end
        end
        unless @error==true
          if params[:applicant_additional_details].present?
            params[:applicant_additional_details].each_pair do |k, v|
              addl_info = v['additional_info']
              addl_field = StudentAdditionalField.find_by_id(k)
              if addl_field.input_type == "has_many"
                addl_info = addl_info.join(", ")
              end
              addl_detail = @applicant.applicant_additional_details.build(:additional_field_id => k,:additional_info => addl_info)
              addl_detail.valid?
              addl_detail.save if addl_detail.valid?
            end
          end
          @applicant.save
          flash[:notice] = t('flash_success')
          render :template => "applicants/success"
        else
          render "show_form",:course_id => @applicant.registration_course_id
        end
      else
        @applicant.save
        flash[:notice] = t('flash_success')
        render :template => "applicants/success"
      end
    else
      render "show_form",:course_id => @applicant.registration_course_id
    end
  end

  def complete
    @applicant = Applicant.find(params[:applicant])
  end


  def load_common
    @countries = Country.all
  end

  def edit
    @applicant = Applicant.find params[:id]
    @selected_value = Configuration.default_country
    @course = @applicant.registration_course
    @subjects = @course.course.batches.map(&:all_elective_subjects).flatten.compact.map(&:code).compact.flatten.uniq
    @ele_subjects={}
    subject_amounts=@course.course.subject_amounts
    elective_subject_amounts= subject_amounts.find_all_by_code(@subjects)
    @subjects.each do |sub|
      subject=elective_subject_amounts.find_by_code(sub)
      @ele_subjects.merge!(sub=>subject ? subject.amount.to_f: 0 )
    end
    @normal_subjects = @course.course.batches.map(&:normal_batch_subject).flatten.compact.map(&:code).compact.flatten.uniq
    @selected_subject_ids = @applicant.subject_ids.nil? ? [] : @applicant.subject_ids
    @ele_subject_amount = subject_amounts.find(:all,:conditions => {:code => @selected_subject_ids}).flatten.compact.map(&:amount).sum
    @currency = currency
    @normal_selected_subject_ids = @applicant.normal_subject_ids.nil? ? [] : @applicant.normal_subject_ids
    @normal_subject_amount=subject_amounts.find(:all,:conditions => {:code => @normal_selected_subject_ids}).flatten.compact.map(&:amount).sum
    @applicant.addl_field_hash
    @applicant_guardian = @applicant.applicant_guardian
    @applicant_previous_data = @applicant.applicant_previous_data
    @addl_field_groups = ApplicantAddlFieldGroup.find(:all,:conditions=>{:registration_course_id=> @applicant.registration_course_id, :is_active => true})
    additional_mandatory_fields = StudentAdditionalField.active.all(:conditions => {:is_mandatory => true})
    additional_fields = StudentAdditionalField.find_all_by_id(@course.additional_field_ids).compact
    @additional_fields = (additional_mandatory_fields + additional_fields).uniq.compact.flatten
    @applicant_additional_details = @applicant.applicant_additional_details
  end

  def update
    @applicant = Applicant.find(params[:id])
    @course = @applicant.registration_course
    @subjects = @course.course.batches.map(&:all_elective_subjects).flatten.compact.map(&:code).compact.flatten.uniq
    @ele_subjects={}
    subject_amounts=@course.course.subject_amounts
    elective_subject_amounts= subject_amounts.find_all_by_code(@subjects)
    @subjects.each do |sub|
      subject=elective_subject_amounts.find_by_code(sub)
      @ele_subjects.merge!(sub=>subject ? subject.amount.to_f: 0 )
    end
    @normal_subjects = @course.course.batches.map(&:normal_batch_subject).flatten.compact.map(&:code).compact.flatten.uniq
    @normal_subject_amount=subject_amounts.find(:all,:conditions => {:code => @normal_subjects}).flatten.compact.map(&:amount).sum
    @selected_subject_ids = @applicant.subject_ids.nil? ? [] : @applicant.subject_ids
    @ele_subject_amount = subject_amounts.find(:all,:conditions => {:code => @selected_subject_ids}).flatten.compact.map(&:amount).sum
    @currency = currency
    @normal_selected_subject_ids = @applicant.normal_subject_ids.nil? ? [] : @applicant.normal_subject_ids
    @applicant.addl_field_hash
    @applicant_guardian = @applicant.applicant_guardian
    @applicant_previous_data = @applicant.applicant_previous_data
    @addl_field_groups = ApplicantAddlFieldGroup.find(:all,:conditions=>{:registration_course_id=> @applicant.registration_course_id, :is_active => true})
    additional_mandatory_fields = StudentAdditionalField.active.all(:conditions => {:is_mandatory => true})
    additional_fields = StudentAdditionalField.find_all_by_id(@course.additional_field_ids).compact
    @additional_fields = (additional_mandatory_fields + additional_fields).uniq.compact.flatten
    @applicant_additional_details = @applicant.applicant_additional_details
    if params[:applicant][:subject_ids].nil?
      params[:applicant][:subject_ids]=[]
    else
      params[:applicant][:subject_ids].each_with_index do |e,i|
        params[:applicant][:subject_ids][i]=(e.split-e.split.last.to_a).join(" ")
      end
    end
    if params[:applicant][:normal_subject_ids].nil?
      params[:applicant][:normal_subject_ids]=[]
    end
    if Applicant.new(params[:applicant]).valid?
      if @course.subject_based_fee_colletion == true
        if params[:applicant][:subject_ids].present?
          all_subjects = params[:applicant][:subject_ids]
          all_subjects += params[:applicant][:normal_subject_ids] unless  params[:applicant][:normal_subject_ids].nil?
        else
          all_subjects = params[:applicant][:normal_subject_ids] unless  params[:applicant][:normal_subject_ids].nil?
        end
        total_amount = @course.course.subject_amounts.find(:all,:conditions => {:code => all_subjects}).flatten.compact.map(&:amount).sum
        @applicant.amount = total_amount.to_f
      else
        @applicant.amount = @course.amount.to_f
      end
      if @applicant.applicant_guardian == nil
        @applicant_guardian = @applicant.build_applicant_guardian(params[:applicant_guardian])
        @applicant_previous_data = @applicant.build_applicant_previous_data(params[:applicant_previous_data])
      else
        @applicant_guardian = @applicant.applicant_guardian
        @applicant_guardian.attributes = params[:applicant_guardian]
        @applicant_previous_data = @applicant.applicant_previous_data
        @applicant_previous_data.attributes = params[:applicant_previous_data]
      end
      if @course.include_additional_details == true
        @error=false
        mandatory_fields = StudentAdditionalField.find(:all, :conditions=>{:is_mandatory=>true, :status=>true})
        mandatory_fields.each do|m|
          unless params[:applicant_additional_details][m.id.to_s.to_sym].present?
            @applicant.errors.add_to_base("#{m.name} must contain atleast one selected option.")
            @error=true
            @applicant_guardian = @applicant.applicant_guardian
            @applicant_previous_data = @applicant.applicant_previous_data
            render :edit and return
          else
            if params[:applicant_additional_details][m.id.to_s.to_sym][:additional_info]==""
              @applicant.errors.add_to_base("#{m.name} cannot be blank.")
              @error=true
              @applicant_guardian = @applicant.applicant_guardian
              @applicant_previous_data = @applicant.applicant_previous_data
              render :edit and return
            end
          end
        end
        unless @error==true
          if params[:applicant_additional_details].present?
            params[:applicant_additional_details].each_pair do |k, v|
              addl_info = v['additional_info']
              addl_field = StudentAdditionalField.find_by_id(k)
              if addl_field.input_type == "has_many"
                addl_info = addl_info.join(", ")
              end
              prev_record = @applicant.applicant_additional_details.find_by_additional_field_id(k)
              unless prev_record.nil?
                unless addl_info.present?
                  prev_record.destroy
                else
                  prev_record.update_attributes(:additional_info => addl_info)
                end
              else
                addl_detail = @applicant.applicant_additional_details.build(:additional_field_id => k,:additional_info => addl_info)
                addl_detail.save if addl_detail.valid?
              end
            end
          end
          @applicant_previous_data.errors.each{|attr,msg| @applicant.errors.add(attr.to_sym,"#{msg}")} unless @applicant_previous_data.save
          @applicant_guardian.errors.each{|attr,msg| @applicant.errors.add(attr.to_sym,"#{msg}")} unless (@applicant_guardian.save and @applicant_previous_data.errors.blank?)
          if (@applicant.errors.blank? and @applicant.update_attributes(params[:applicant]))
            redirect_to :controller=>"applicants_admins",:action=>"view_applicant",:id=>@applicant.id and return
          else
            render :edit and return
          end
        end
      else
        @applicant_previous_data.errors.each{|attr,msg| @applicant.errors.add(attr.to_sym,"#{msg}")} unless @applicant_previous_data.save
        @applicant_guardian.errors.each{|attr,msg| @applicant.errors.add(attr.to_sym,"#{msg}")} unless (@applicant_guardian.save and @applicant_previous_data.errors.blank?)
        @applicant_guardian.save
        if (@applicant.errors.blank? and @applicant.update_attributes(params[:applicant]))
          redirect_to :controller=>"applicants_admins",:action=>"view_applicant",:id=>@applicant.id and return
        else
          render :edit
        end
      end
    else
      @applicant.update_attributes(params[:applicant])
      render :edit
    end
  end

  def print_application
    if params[:token_check].nil?
      @applicant = Applicant.find(params[:id])
    else
      @applicant ||= Applicant.find_by_print_token(params[:token_check][:print_token])
    end
    @addl_values = @applicant.applicant_addl_values
    @additional_details = @applicant.applicant_additional_details
    @financetransaction=FinanceTransaction.find_by_title("Applicant Registration - #{@applicant.reg_no} - #{@applicant.full_name}")
    if Champs21Plugin.can_access_plugin?("champs21_pay")
      @active_gateway = PaymentConfiguration.config_value("champs21_gateway")
      if @active_gateway.nil?
        render :pdf => "application",:zoom => 0.90 and return
      end
      if (PaymentConfiguration.config_value("enabled_fees").present? and PaymentConfiguration.config_value("enabled_fees").include? "Application Registration")
        online_payment = Payment.find_by_payee_id_and_payee_type(@applicant.id,'Applicant')
        if online_payment.nil?
          @online_transaction_id = @applicant.has_paid == true ? nil : t('fee_not_paid')
        else
          if online_payment.gateway_response.keys.include? :transaction_id
            @online_transaction_id = online_payment.gateway_response[:transaction_id]
          elsif online_payment.gateway_response.include? :x_trans_id
            @online_transaction_id = online_payment.gateway_response[:x_trans_id]
          end
        end
      else
        @online_transaction_id = nil
      end
    end
    render :pdf => "application",:zoom => 0.90
  end

  def registration_return
    @currency = currency
    @active_gateway = PaymentConfiguration.config_value("champs21_gateway")
    if @active_gateway == "Paypal"
      @merchant_id = PaymentConfiguration.config_value("paypal_id")
      @certificate = PaymentConfiguration.config_value("paypal_certificate")
    elsif @active_gateway == "Authorize.net"
      @merchant_id = PaymentConfiguration.config_value("authorize_net_merchant_id")
      @certificate = PaymentConfiguration.config_value("authorize_net_transaction_password")
    end
    @applicant = Applicant.find(params[:id])
    gateway_response = Hash.new
    if @active_gateway == "Paypal"
      gateway_response = {
        :amount => params[:amt],
        :status => params[:st],
        :transaction_id => params[:tx]
      }
    elsif @active_gateway == "Authorize.net"
      gateway_response = {
        :x_response_code => params[:x_response_code],
        :x_response_reason_code => params[:x_response_reason_code],
        :x_response_reason_text => params[:x_response_reason_text],
        :x_avs_code => params[:x_avs_code],
        :x_auth_code => params[:x_auth_code],
        :x_trans_id => params[:x_trans_id],
        :x_method => params[:x_method],
        :x_card_type => params[:x_card_type],
        :x_account_number => params[:x_account_number],
        :x_first_name => params[:x_first_name],
        :x_last_name => params[:x_last_name],
        :x_company => params[:x_company],
        :x_address => params[:x_address],
        :x_city => params[:x_city],
        :x_state => params[:x_state],
        :x_zip => params[:x_zip],
        :x_country => params[:x_country],
        :x_phone => params[:x_phone],
        :x_fax => params[:x_fax],
        :x_invoice_num => params[:x_invoice_num],
        :x_description => params[:x_description],
        :x_type => params[:x_type],
        :x_cust_id => params[:x_cust_id],
        :x_ship_to_first_name => params[:x_ship_to_first_name],
        :x_ship_to_last_name => params[:x_ship_to_last_name],
        :x_ship_to_company => params[:x_ship_to_company],
        :x_ship_to_address => params[:x_ship_to_address],
        :x_ship_to_city => params[:x_ship_to_city],
        :x_ship_to_zip => params[:x_ship_to_zip],
        :x_ship_to_country => params[:x_ship_to_country],
        :x_amount => params[:x_amount],
        :x_tax => params[:x_tax],
        :x_duty => params[:x_duty],
        :x_freight => params[:x_freight],
        :x_tax_exempt => params[:x_tax_exempt],
        :x_po_num => params[:x_po_num],
        :x_cvv2_resp_code => params[:x_cvv2_resp_code],
        :x_MD5_hash => params[:x_MD5_hash],
        :x_cavv_response => params[:x_cavv_response],
        :x_method_available => params[:x_method_available],
      }
    end
    amount_from_gateway = 0
    if @active_gateway == "Paypal"
      amount_from_gateway = params[:amt]
    elsif @active_gateway == "Authorize.net"
      amount_from_gateway = params[:x_amount]
    end
    if (amount_from_gateway.to_f == @applicant.amount.to_f)
      receipt = String.new
      if @active_gateway == "Paypal"
        receipt = params[:tx]
      elsif @active_gateway == "Authorize.net"
        receipt = params[:x_trans_id]
      end
      payment = Payment.new(:payee => @applicant,:payment_type => "Application",:payment_id => ActiveSupport::SecureRandom.hex,:gateway_response => gateway_response)
      if payment.save
        gateway_status = false
        if @active_gateway == "Paypal"
          gateway_status = true if payment.gateway_response[:status] == "Completed"
        elsif @active_gateway == "Authorize.net"
          gateway_status = true if payment.gateway_response[:x_response_reason_code] == "1"
        end
        if gateway_status.to_s == "true"
          transaction = @applicant.mark_paid
          payment.update_attributes(:finance_transaction_id => transaction.id)
          online_transaction_id = payment.gateway_response[:transaction_id]
          online_transaction_id ||= payment.gateway_response[:x_trans_id]
          flash[:notice] = "#{t('payment_success')} #{online_transaction_id}"
        else
          flash[:notice] = "#{t('payment_failed')}"
        end
      else
        flash[:notice] = "#{t('payment_failed')}"
      end
    else
      flash[:notice] = "#{t('payment_failed')}"
    end
    render "applicants/success"
  end


  def load_lang
    if params[:lang]
      session[:register_lang] = params[:lang]
    else
      session[:register_lang] = "en" unless session[:register_lang]
    end
    I18n.locale = session[:register_lang]
  end

end
