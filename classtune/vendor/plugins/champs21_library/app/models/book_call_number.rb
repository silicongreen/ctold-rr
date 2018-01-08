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
class BookCallNumber < ActiveRecord::Base
  validates_presence_of :title, :author
  validates_uniqueness_of :call_number
  has_many :book
  before_destroy :delete_dependency
  after_save :save_book_title

  cattr_reader :per_page

  @@per_page = 25
  def save_book_title
    books = Book.find_all_by_book_call_number_id(self.id)
    unless books.blank?
      books.each do |book|
        book_obj = Book.find(book.id)
        book_obj.title = self.title
        book_obj.author = self.author
        book_obj.save
      end  
    end
  end

  def delete_dependency
    books = Book.find_all_by_book_call_number_id(self.id)
    Book.destroy_all(:id => books.map(&:id))
  end

end
