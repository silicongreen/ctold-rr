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

class ConfigurationController < ApplicationController
  before_filter :login_required
  before_filter :check_permission,:only=>[:index,:settings]
  filter_access_to :all

  FILE_EXTENSIONS = [".jpg",".jpeg",".png",".gif",".png"]
  FILE_MAXIMUM_SIZE_FOR_FILE=1048576

  def settings
    @config = Configuration.get_multiple_configs_as_hash ['InstitutionName', 'InstitutionAddress', 'InstitutionPhoneNo', \
        'StudentAttendanceType', 'CurrencyType', 'ExamResultType', 'AdmissionNumberAutoIncrement','EmployeeNumberAutoIncrement', \
        'Locale','FinancialYearStartDate','FinancialYearEndDate','EnableNewsCommentModeration','RoutineViewTeacherShortCode','RountineViewPeriodNameNoTiming','DefaultCountry',\
        'TimeZone','FirstTimeLoginEnable','FeeReceiptNo','EnableSibling','PrecisionCount',\
        'FreeFeedForAdmin','FreeFeedForTeacher','FreeFeedForStudent','HomeworkWillForwardOnly','ReminderNeedAdminApproval', 'FontFace','NoticeComment','ParentCanEdit','TeacherCanEdit']
    @grading_types = Course::GRADINGTYPES
    @enabled_grading_types = Configuration.get_grading_types
    @time_zones = TimeZone.all
    @school_detail = SchoolDetail.first || SchoolDetail.new
    @countries=Country.all
    @fonts = Configuration.get_fonts()
    
    if request.post?
      
      Configuration.set_config_values(params[:configuration])
      session[:language] = nil unless session[:language].nil?
      unless params[:school_detail].nil?
        @school_detail.logo = params[:school_detail][:school_logo] unless params[:school_detail][:school_logo].nil?
        @school_detail.cover = params[:school_detail][:school_cover] unless params[:school_detail][:school_cover].nil?      
      end
      #
      
	  unless @school_detail.save
        @config = Configuration.get_multiple_configs_as_hash ['InstitutionName', 'InstitutionAddress', 'InstitutionPhoneNo', \
            'StudentAttendanceType', 'CurrencyType', 'ExamResultType', 'AdmissionNumberAutoIncrement','EmployeeNumberAutoIncrement', \
            'Locale','FinancialYearStartDate','FinancialYearEndDate','EnableNewsCommentModeration','RoutineViewTeacherShortCode','RountineViewPeriodNameNoTiming','DefaultCountry','TimeZone',\
            'FirstTimeLoginEnable','EnableSibling','FreeFeedForAdmin','FreeFeedForTeacher','FreeFeedForStudent','HomeworkWillForwardOnly','ReminderNeedAdminApproval', 'FontFace','NoticeComment','ParentCanEdit','TeacherCanEdit']
        return
      end
      @current_user.clear_menu_cache
      @current_user.clear_school_name_cache(request.host)
      Configuration.clear_school_cache(@current_user)
      News.new.reload_news_bar
      flash[:notice] = "#{t('flash_msg8')}"
      redirect_to :action => "settings"  and return
    end
  end
end
