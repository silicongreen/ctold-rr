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
authorization do

  #custom - privileges
  role :students_control do
    has_permission_on [:library],
      :to => [:student_library_details]
    
  end


  #library module
  role :librarian do
    has_permission_on [:book_movement],
      :to => [
      :issue_book,
      :user_search,
      :update_user,
      :return_book,
      :return_book_detail,
      :update_return,
      :reserve_book,
      :direct_issue_book,
      :renewal,
      :update_renewal
    ]
    has_permission_on [:books],
      :to => [
      :book_call_numbers,
      :add_call_numbers,
      :upload_book,
      :upload_call_numbers,
      :create_call_number,
      :edit_call_number,
      :update_call_number,
      :destroy_call_number,
      :filter_by,
      :index,
      :new,
      :create,
      :edit,
      :update,
      :show,
      :destroy,
      :sort_by,
      :add_additional_details,
      :change_field_priority,
      :edit_additional_details,
      :delete_additional_details,
      :additional_data,
      :edit_additional_data,
      :library_transactions,
      :search_library_transactions,
      :library_transaction_filter_by_date,
      :delete_library_transaction
    ]
    has_permission_on [:library],
      :to => [
      :index,
      :search_book,
      :search_result,
      :detail_search,
      :availabilty,
      :card_setting,
      :show_setting,
      :add_new_setting,
      :create_setting,
      :edit_card_setting,
      :update_card_setting,
      :delete_card_setting,
      :movement_log,
      :movement_log_csv,
      :movement_log_details,
      :library_report,
      :library_report_pdf,
      :batch_library_report,
      :batch_library_report_pdf,
      :student_library_details,
      :employee_library_details
    ]
  end
  #end library

  # admin privileges
  role :admin do

    includes :librarian
  end


  #employee - privileges
  role :employee do
    has_permission_on [:book_movement],
      :to => [
      :user_search,
      :update_user,
      :reserve_book
    ]
    has_permission_on [:books],
      :to => [
      :index,
      :book_call_numbers,
      :filter_by,
      :show,
      :sort_by

    ]
    has_permission_on [:library],
      :to => [
      :index,
      :search_book,
      :search_result,
      :detail_search,
      :availabilty ,
      :employee_library_details]
  end
  # student- privileges
  role :student do

    has_permission_on [:book_movement],
      :to => [

      :user_search,
      :update_user,
      :reserve_book
    ]
    has_permission_on [:books],
      :to => [
      :index,
      :show,
      :sort_by

    ]
    has_permission_on [:library],
      :to => [
      :index,
      :search_book,
      :search_result,
      :detail_search,
      :availabilty ,
      :student_library_details]
    #end library------

  end
end