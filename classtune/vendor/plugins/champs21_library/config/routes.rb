#Copyright 2010 teamCreative Private Limited
#This product includes software developed at
#Project Champs21 - http://www.champs21.com/
#
#Licensed under the Apache License, Version 2.0 (the "License");
#you may not use this file except in compliance with the License.
#You may obtain a copy of the License at
#
#  http://www.apache.org/licenses/LICENSE-2.0
#
#Unless required by applicable law or agreed to in writing,
#software distributed under the License is distributed on an
#"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
#KIND, either express or implied.  See the License for the
#specific language governing permissions and limitations
#under the License.
ActionController::Routing::Routes.draw do |map|

  map.resources :books,:collection => {:add_additional_details => [:get,:post,:put],
    :edit_additional_details => [:get,:post,:put],
    :additional_data => [:get,:post],
    :edit_additional_data => [:get,:post,:put],
    :library_transactions=>[:get,:post],
    :book_call_numbers => [:get,:post],
    :upload_call_numbers => [:get,:post],
    :add_call_numbers => [:get,:post],
    :create_call_number => [:get,:post],
    :edit_call_number => [:get,:post],
    :update_call_number=> [:get,:post],
    :destroy_call_number => [:get,:post],
    },
    :member => {:change_field_priority => [:get,:post],
    :delete_additional_details => [:get,:post,:put]}

  map.namespace(:api) do |api|
    api.resources :books
  end
end
