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

class ImportdataController < ApplicationController
  before_filter :login_required
#  before_filter :check_permission,:only=>[:import_batches]
  filter_access_to :all
  def import_batches
    require 'net/http'
    o = [('a'..'z'), ('A'..'Z'), (0..9)].map { |i| i.to_a }.flatten
    rand_val = (0...16).map { o[rand(o.length)] }.join
    directory = "public/uploads/seeds"
    if request.post?
      ARGV[0] = "";
      file_name = params[:import_batches][:class_file];
      unless file_name.nil?
        ext = File.extname(file_name.original_filename);
        basename = File.basename(file_name.original_filename, ext)
        name =  basename + "-" + rand_val + ext
        path = File.join(directory, name)
        File.open(path, "wb") { |f| f.write(file_name.read) }
        FileUtils.chmod 0777, path, :verbose => true
        ARGV[0] = path
        unless params[:procced].nil?
          ARGV[1] = true
        else  
          ARGV[1] = false
        end
        ARGV[2] = ""
        seed_file = File.join(Rails.root,'db', 'importbatch.rb')
        load(seed_file) if File.exist?(seed_file)
        if ARGV[2] == "error"
          flash[:notice]="Incorrect Excel format, found " + ARGV[5].to_s + " row but valid Class Found " + ARGV[4].length.to_s + ". <br/ ><br /> Classes are: " + ARGV[4].map{|l| l["class_name"]}.join(', ') + " <br /><br />Please Check and reupload the excel and checked the proced anyway checkbox if you want to upload this excel file with " + ARGV[4].length.to_s + " Class"
        else  
          flash[:notice]="Shift and Class imported successfully"
        end
        redirect_to :action=>'import_batches'
      end
    end  
  end
  
  def import_grade
    require 'net/http'
    o = [('a'..'z'), ('A'..'Z'), (0..9)].map { |i| i.to_a }.flatten
    rand_val = (0...16).map { o[rand(o.length)] }.join
    directory = "public/uploads/seeds"
    if request.post?
      ARGV[0] = "";
      grade_file = params[:import_grade][:grade_file];
      unless grade_file.nil?
        ext = File.extname(grade_file.original_filename);
        basename = File.basename(grade_file.original_filename, ext)
        name =  basename + "-" + rand_val + ext
        path = File.join(directory, name)
        File.open(path, "wb") { |f| f.write(grade_file.read) }
        FileUtils.chmod 0777, path, :verbose => true
        ARGV[0] = path
        seed_file = File.join(Rails.root,'db', 'grade.rb')
        load(seed_file) if File.exist?(seed_file)
        flash[:notice]="Grade imported successfully"
        redirect_to :action=>'import_grade'
      end
    end  
  end
  
   def import_employee_data
    require 'net/http'
    o = [('a'..'z'), ('A'..'Z'), (0..9)].map { |i| i.to_a }.flatten
    rand_val = (0...16).map { o[rand(o.length)] }.join
    directory = "public/uploads/seeds"
    if request.post?
      ARGV[0] = ""
      ARGV[1] = ""
      ARGV[2] = ""
      emp_cat_file_name = params[:import_employee][:emp_cat_file_name];
      emp_dept_file = params[:import_employee][:emp_dep_file_name];
      emp_grade_file_name = params[:import_employee][:emp_grade_file_name];
      
      unless emp_cat_file_name.nil? && emp_dept_file.nil? && emp_grade_file_name.nil?
        unless emp_cat_file_name.nil?
          ext = File.extname(emp_cat_file_name.original_filename);
          basename = File.basename(emp_cat_file_name.original_filename, ext)
          name =  basename + "-" + rand_val + ext
          path = File.join(directory, name)
          File.open(path, "wb") { |f| f.write(emp_cat_file_name.read) }
          FileUtils.chmod 0777, path, :verbose => true
          ARGV[0] = path
        end

        unless emp_dept_file.nil?
          ext = File.extname(emp_dept_file.original_filename);
          basename = File.basename(emp_dept_file.original_filename, ext)
          name =  basename + "-" + rand_val + ext
          path = File.join(directory, name)
          File.open(path, "wb") { |f| f.write(emp_dept_file.read) }
          FileUtils.chmod 0777, path, :verbose => true
          ARGV[1] = path
          
        end

        unless emp_grade_file_name.nil?
          ext = File.extname(emp_grade_file_name.original_filename);
          basename = File.basename(emp_grade_file_name.original_filename, ext)
          name =  basename + "-" + rand_val + ext
          path = File.join(directory, name)
          File.open(path, "wb") { |f| f.write(emp_grade_file_name.read) }
          FileUtils.chmod 0777, path, :verbose => true
          ARGV[2] = path    
        end
        
        seed_file = File.join(Rails.root,'db', 'importemployee.rb')
        load(seed_file) if File.exist?(seed_file)
        flash[:notice]="Employee department category and grade update successfully"
        redirect_to :action=>'import_employee_data'
      end
      
    end  
  end
  
end