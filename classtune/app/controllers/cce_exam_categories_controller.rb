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

class CceExamCategoriesController < ApplicationController
  before_filter :login_required
  filter_access_to :all
  def index
    @categories=CceExamCategory.all
  end
  def new
    @category=CceExamCategory.new
  end
  def create
    @category=CceExamCategory.new(params[:cce_exam_category])
    if @category.save
      flash[:notice]="#{t('exam_category_created_successfully')}"
      @categories=CceExamCategory.all
    else
      @error=true
    end
  end

  def edit
    @category=CceExamCategory.find(params[:id])
  end

  def update
    @category=CceExamCategory.find(params[:id])
    @category.name=params[:cce_exam_category][:name]
    @category.desc=params[:cce_exam_category][:desc]
    if @category.save
      flash[:notice]="#{t('exam_category_updated_successfully')}"
      @categories=CceExamCategory.all
    else
      @error=true
    end
  end
  def destroy
    @category=CceExamCategory.find(params[:id])
    if @category.destroy
      flash[:notice]="#{t('exam_category_deleted')}"
    else
      flash[:notice]="#{t('exam_category_cannot_be_deleted_dependant_data_present')}"
    end
    redirect_to :action => "index"
  end
end
