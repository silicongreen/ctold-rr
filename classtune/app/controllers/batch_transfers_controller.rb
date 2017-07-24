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

class BatchTransfersController < ApplicationController
  before_filter :login_required
  filter_access_to :all
   
  def index
    @batches = Batch.active 
  end

  def show
    flash[:notice] = nil
    @classes = []
    @batches = []
    @batch_no = 0
    @course_name = ""
    @courses = []
    @batch = Batch.find params[:id], :include => [:students],:order => "students.first_name ASC"
    @batches = Batch.active - @batch.to_a
    if Batch.active.find(:all, :group => "name").length == 1
      batches = Batch.active
      batch_name = batches[0].name
      batches = Batch.find(:all, :conditions => ["name = ?", batch_name]).map{|b| b.course_id}
      @courses = Course.find(:all, :conditions => ["id IN (?)", batches], :group => "course_name", :select => "course_name", :order => "cast(replace(course_name, 'Class ', '') as SIGNED INTEGER) asc")
    end
    defaulter_students = @batch.students.collect{|student| student.finance_fees}.flatten.collect{|s| s.is_paid}
    if defaulter_students.include? false
      flash[:notice] = "#{t('flash5')}"
    end
  end

  def transfer
    require 'net/http'
    require 'uri'
    require "yaml"
    
    if request.post? 
      @batch = Batch.find params[:id], :include => [:students], :order => "students.first_name ASC"
      if params[:transfer][:to].present? and params[:session].present?
        unless params[:transfer][:students].nil?         
          Delayed::Job.enqueue(DelayedBatchTranfer.new(params[:transfer][:students].join(","),params[:id],params[:transfer][:to],params[:session],false,"","",@local_tzone_time,current_user,request.domain,params[:start_previous],params[:end_previous],params[:start_next],params[:end_next],params[:transfer_all]))
        end
        flash[:notice] = "#{t('flash1')}"
        redirect_to :controller => 'batch_transfers'
      else
        @batches = Batch.active - @batch.to_a
        @batch.errors.add_to_base("#{t('select_a_batch_to_continue')}")
        render :template=> "batch_transfers/show"
      end
    else
      redirect_to :action=>"show", :id=> params[:id]
    end
  end

  def graduation
    @batch = Batch.find params[:id], :include => [:students], :order => "students.first_name ASC"
    
    flash[:notice] = nil
#    defaulter_students = @batch.students.collect{|student| student.finance_fees}.flatten.collect{|s| s.is_paid}
#    if defaulter_students.include? false
#      flash[:notice] = "#{t('flash5')}"
#    end
    params[:ids]
    unless params[:ids].nil?
      @ids = params[:ids]
      @id_lists = @ids.map { |st_id| ArchivedStudent.find_by_admission_no(st_id) }
    end
    if request.post?   
      student_id_list = params[:graduate][:students]
      @student_list = student_id_list.map { |st_id| Student.find(st_id) }
      @admission_list = []
      @student_list.each do |s|
        @admission_list.push s.admission_no
      end
      Delayed::Job.enqueue(DelayedBatchTranfer.new(student_id_list.join(","),params[:id],0,params[:graduate][:status_description],true,params[:graduate][:status_description],params[:leaving_date],@local_tzone_time,current_user,request.domain,params[:start_previous],params[:end_previous],params[:start_next],params[:end_next],'Yes'))
      flash[:notice]= "#{t('flash2')}"
      redirect_to :action=>"graduation", :id=>params[:id], :ids => @admission_list
    end
  end

  def subject_transfer
    @batch = Batch.find(params[:id])
    @elective_groups = @batch.elective_groups.all(:conditions => {:is_deleted => false})
    @normal_subjects = @batch.normal_batch_subject
    @elective_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>["elective_group_id IS NOT NULL AND is_deleted = false"])
  end

  def get_previous_batch_subjects
    @batch = Batch.find(params[:id])
    course_id = @batch.course_id
    @previous_batch = Batch.find(:first,:order=>'id desc', :conditions=>"batches.id < '#{@batch.id }' AND batches.is_deleted = 0 AND course_id = ' #{course_id }'",:joins=>"INNER JOIN subjects ON subjects.batch_id = batches.id  AND subjects.is_deleted = 0")
    unless @previous_batch.blank?
      @previous_batch_normal_subject = @previous_batch.normal_batch_subject
      @elective_groups = @previous_batch.elective_groups.all(:conditions => {:is_deleted => false})
      @previous_batch_electives = Subject.find_all_by_batch_id(@previous_batch.id,:conditions=>["elective_group_id IS NOT NULL AND is_deleted = false"])
      render(:update) do |page|
        page.replace_html 'previous-batch-subjects', :partial=>"previous_batch_subjects"
      end
    else
      render(:update) do |page|
        page.replace_html 'msg', :text=>"<p class='flash-msg'>#{t('batch_transfers.flash4')}</p>"
      end
    end
  end

  def update_batch
    if params[:course_name].present?
      @courses_data = Course.find(:all, :conditions => ["course_name = ? and is_deleted = 0", params[:course_name]])
      @course_ids = @courses_data.map{|c| c.id} 
      @batches = Batch.find(:all, :conditions => ["course_id IN (?) and is_deleted = 0 and is_active = 1", @course_ids])
      @batches_name = Batch.find(:all, :conditions => ["course_id IN (?) and is_deleted = 0 and is_active = 1", @course_ids], :group => "name").map{|bn| bn.name}
      render(:update) do |page|
        page.replace_html 'update_batch', :partial=>'list_courses'
      end
    else
      render(:update) do |page|
        page.replace_html 'update_batch', :text=>''
      end
    end
  end

  def assign_previous_batch_subject
    subject = Subject.find(params[:id])
    batch = Batch.find(params[:id2])
    sub_exists = Subject.find_by_batch_id_and_name(batch.id,subject.name, :conditions => { :is_deleted => false})
    if sub_exists.nil?
      if subject.elective_group_id == nil
        Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>batch.id,:no_exams=>subject.no_exams,:no_exams_sjws=>subject.no_exams_sjws,
          :max_weekly_classes=>subject.max_weekly_classes,:credit_hours=>subject.credit_hours,:elective_group_id=>subject.elective_group_id,:is_deleted=>false)
      else
        elect_group_exists = ElectiveGroup.find_by_name_and_batch_id(ElectiveGroup.find(subject.elective_group_id).name,batch.id)
        if elect_group_exists.nil?
          elect_group = ElectiveGroup.create(:name=>ElectiveGroup.find(subject.elective_group_id).name,
            :batch_id=>batch.id,:is_deleted=>false)
          Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>batch.id,:no_exams=>subject.no_exams,:no_exams_sjws=>subject.no_exams_sjws,
            :max_weekly_classes=>subject.max_weekly_classes,:credit_hours=>subject.credit_hours,:elective_group_id=>elect_group.id,:is_deleted=>false)
        else
          Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>batch.id,:no_exams=>subject.no_exams,:no_exams_sjws=>subject.no_exams_sjws,
            :max_weekly_classes=>subject.max_weekly_classes,:credit_hours=>subject.credit_hours,:elective_group_id=>elect_group_exists.id,:is_deleted=>false)
        end
      end
      render(:update) do |page|
        page.replace_html "prev-subject-name-#{subject.id}", :text=>""
        page.replace_html "errors", :text=>"#{subject.name}  #{t('has_been_added_to_batch')}:#{batch.name}"
      end
    else
      render(:update) do |page|
        page.replace_html "prev-subject-name-#{subject.id}", :text=>""
        page.replace_html "errors", :text=>"<div class=\"errorExplanation\" ><p>#{batch.name} #{t('already_has_subject')} #{subject.name}</p></div>"
      end
    end
  end

  def assign_all_previous_batch_subjects
    msg = ""
    err = ""
    batch = Batch.find(params[:id])
    course = batch.course
    all_batches = course.batches(:order=>'id asc')
    all_batches.reject! {|b| b.is_deleted?}
    all_batches.reject! {|b| b.subjects.empty?}
    @previous_batch = all_batches[all_batches.size-2]
    subjects = Subject.find_all_by_batch_id(@previous_batch.id,:conditions=>'is_deleted=false')
    subjects.each do |subject|
      sub_exists = Subject.find_by_batch_id_and_name(batch.id,subject.name, :conditions => { :is_deleted => false})
      if sub_exists.nil?
        if subject.elective_group_id.nil?
          Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>batch.id,:no_exams=>subject.no_exams,:no_exams_sjws=>subject.no_exams_sjws,
            :max_weekly_classes=>subject.max_weekly_classes,:credit_hours=>subject.credit_hours,:elective_group_id=>subject.elective_group_id,:is_deleted=>false)
        else
          elect_group_exists = ElectiveGroup.find_by_name_and_batch_id(ElectiveGroup.find(subject.elective_group_id).name,batch.id)
          if elect_group_exists.nil?
            elect_group = ElectiveGroup.create(:name=>ElectiveGroup.find(subject.elective_group_id).name,
              :batch_id=>batch.id,:is_deleted=>false)
            Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>batch.id,:no_exams=>subject.no_exams,:no_exams_sjws=>subject.no_exams_sjws,
              :max_weekly_classes=>subject.max_weekly_classes,:credit_hours=>subject.credit_hours,:elective_group_id=>elect_group.id,:is_deleted=>false)
          else
            Subject.create(:name=>subject.name,:code=>subject.code,:batch_id=>batch.id,:no_exams=>subject.no_exams,:no_exams_sjws=>subject.no_exams_sjws,
              :max_weekly_classes=>subject.max_weekly_classes,:credit_hours=>subject.credit_hours,:elective_group_id=>elect_group_exists.id,:is_deleted=>false)
          end
        end
        msg += "<li> #{t('the_subject')} #{subject.name}  #{t('has_been_added_to_batch')} #{batch.name}</li>"
      else
        err +=   "<li>#{t('batch')} #{batch.name} #{t('already_has_subject')} #{subject.name}" + "</li>"
      end
    end
    @batch = batch
    course = batch.course
    all_batches = course.batches
    @previous_batch = all_batches[all_batches.size-2]
    @previous_batch_normal_subject = @previous_batch.normal_batch_subject
    @elective_groups = @previous_batch.elective_groups
    @previous_batch_electives = Subject.find_all_by_batch_id(@previous_batch.id,:conditions=>["elective_group_id IS NOT NULL AND is_deleted = false"])
    render(:update) do |page|
      page.replace_html 'previous-batch-subjects', :text=>"<p>#{t('subjects_assigned')}</p> "
      unless msg.empty?
        page.replace_html "msg", :text=>"<div class=\"flash-msg\"><ul>" +msg +"</ul></p>"
      end
      unless err.empty?
        page.replace_html "errors", :text=>"<div class=\"errorExplanation\" ><p>#{t('following_errors_found')} :</p><ul>" +err + "</ul></div>"
      end
    end

  end



  def new_subject
    @subject = Subject.new
    @batch = Batch.find params[:id] if request.xhr? and params[:id]
    @elective_group = ElectiveGroup.find params[:id2] unless params[:id2].nil?
    @images = Dir.glob("#{RAILS_ROOT}/public/images/icons/subjects/*.png")
    respond_to do |format|
      format.js { render :action => 'new_subject' }
    end
  end

  def create_subject
    @subject = Subject.new(params[:subject])
    @batch = @subject.batch
    if @subject.save
      @subjects = @subject.batch.normal_batch_subject
      @normal_subjects = @subjects
      @elective_groups = ElectiveGroup.find_all_by_batch_id(@batch.id)
      @elective_subjects = Subject.find_all_by_batch_id(@batch.id,:conditions=>["elective_group_id IS NOT NULL AND is_deleted = false"])
    else
      @error = true
    end
  end

end