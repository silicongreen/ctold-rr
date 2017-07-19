# To change this template, choose Tools | Templates
# and open the template in the editor.

module Champs21Dashboard

  module NewsDashboardText
    def self.included(base)
      base.class_eval do
        def news_dashboard_text
          "<div class='subcontent-header themed_text'>#{self.title}</div>
           <div class='subcontent-footer'>
            <div class='footer-date'>#{t('posted_on')} #{I18n.l(self.created_at,:format=>"%A, %d %B, %Y")}</div>
            <div class='footer-comment'>
             <span class='footer-comment-icon'></span> #{self.comments.count}
            </div>
           </div>".html_safe
        end
      end
    end
  end

  module EventsDashboardText
    def self.included(base)
      base.class_eval do
        def events_dashboard_text
          "<div class='subcontent-header themed_text'>#{self.title}</div>
           <div class='subcontent-info'>
            #{I18n.l(self.start_date,:format=>"%d %B, %Y %I.%M%p")} - #{I18n.l(self.end_date,:format=>"%d %B, %Y %I.%M%p")}
           </div>
           <div class='subcontent-info'>#{self.description}</div> ".html_safe
        end
        def fees_due_dashboard_text
          collection = self.origin
          unless collection.nil?
            col_class = collection.class.name
            fee_type = (col_class == "FinanceFeeCollection") ? "default_col" : (col_class == "HostelFeeCollection") ? "hostel_col" : (col_class == "TransportFeeCollection") ? "transport_col" : "none_col"
            cur_usr = Authorization.current_user
            if col_class == "FinanceFeeCollection"
              if cur_usr.admin or cur_usr.role_symbols.include?(:finance_control)
                "<div class='subcontent-header themed_text'>#{collection.name}</div>
           <div class='subcontent-info'>#{t("fee_type")} : #{t(fee_type)}</div>
           <div class='subcontent-info'>#{t("batches_text")} : #{collection.batches.collect(&:full_name).join(", ")}</div> ".html_safe
              elsif cur_usr.student
                "<div class='subcontent-header themed_text'>#{collection.name}</div>
           <div class='subcontent-info'>#{t("fee_type")} : #{t(fee_type)}</div>
           <div class='subcontent-info'>#{t("batch")} : #{cur_usr.student_record.batch.full_name}</div> ".html_safe
              elsif cur_usr.parent
                "<div class='subcontent-header themed_text'>#{collection.name}</div>
           <div class='subcontent-info'>#{t("fee_type")} : #{t(fee_type)}</div>
           <div class='subcontent-info'>#{t("batch")} : #{cur_usr.guardian_entry.current_ward.batch.full_name}</div> ".html_safe
              end
            else
              if collection.batch
                "<div class='subcontent-header themed_text'>#{collection.name}</div>
           <div class='subcontent-info'>#{t("fee_type")} : #{t(fee_type)}</div>
           <div class='subcontent-info'>#{t("batch")} : #{collection.batch.full_name}</div> ".html_safe
              else
                "<div class='subcontent-header themed_text'>#{collection.name}</div>
           <div class='subcontent-info'>#{t("fee_type")} : #{t(fee_type)}</div>".html_safe
              end
            end
          else
            col_class = self.origin_type
            fee_type = (col_class == "FinanceFeeCollection") ? "default_col" : (col_class == "HostelFeeCollection") ? "hostel_col" : (col_class == "TransportFeeCollection") ? "transport_col" : "none_col"
            "<div class='subcontent-header themed_text'>#{t("deleted_collection")}</div>
           <div class='subcontent-info'>#{t("fee_type")} : #{t(fee_type)}</div>".html_safe
          end
        end

      end
    end
  end

  module StudentsDashboardText
    def self.included(base)
      base.class_eval do
        def admitted_students_dashboard_text
          "<div class='subcontent-header themed_text'>#{self.full_name}</div>
          <div class='subcontent-info'>#{t("adm_no")} : #{self.admission_no}</div>
          <div class='subcontent-info'>#{t("batch")} : #{self.batch.full_name}</div>".html_safe
        end
      end
    end
  end

  module ArchivedStudentsDashboardText
    def self.included(base)
      base.class_eval do
        def relieved_students_dashboard_text
          "<div class='subcontent-header themed_text'>#{self.full_name}</div>
          <div class='subcontent-info'>#{t("adm_no")} : #{self.admission_no}</div>
          <div class='subcontent-info'>#{t("batch")} : #{self.batch.full_name}</div>".html_safe
        end
      end
    end
  end

  module AttendanceDashboardText
    def self.included(base)
      base.class_eval do
        def absent_students_dashboard_text
          leave_type = (self.forenoon == true and self.afternoon == true) ? "Full Day" : (self.forenoon == true ? "Forenoon" : "Afternoon")
          stu = self.student
          unless stu
            stu = ArchivedStudent.find_by_former_id(self.student_id)
          end
          "<div class='subcontent-header themed_text'>#{stu.full_name}</div>
          <div class='subcontent-info'>#{t("adm_no")} : #{stu.admission_no}</div>
          <div class='subcontent-info'>#{t("batch")} : #{stu.batch.full_name}</div>
          <div class='subcontent-info'>Absent for : #{leave_type}</div>".html_safe
        end
      end
    end
  end

  module EmployeesDashboardText
    def self.included(base)
      base.class_eval do
        def admitted_employees_dashboard_text
          "<div class='subcontent-header themed_text'>#{self.full_name}</div>
          <div class='subcontent-info'>#{t("emp_no")} : #{self.employee_number}</div>
          <div class='subcontent-info'>#{t("department")} : #{self.employee_department.name}</div>".html_safe
        end
      end
    end
  end

  module ArchivedEmployeesDashboardText
    def self.included(base)
      base.class_eval do
        def removed_employees_dashboard_text
          "<div class='subcontent-header themed_text'>#{self.full_name}</div>
          <div class='subcontent-info'>#{t("emp_no")} : #{self.employee_number}</div>
          <div class='subcontent-info'>#{t("department")} : #{self.employee_department.name}</div>".html_safe
        end
      end
    end
  end

  module EmployeeAttendanceDashboardText
    def self.included(base)
      base.class_eval do
        def employees_on_leave_dashboard_text
          employee = self.employee
          leave_type = self.is_half_day ? "half_day" : "full_day"
          if employee
            "<div class='subcontent-header themed_text'>#{employee.full_name}</div>
          <div class='subcontent-info'>#{t("emp_no")} : #{employee.employee_number}</div>
          <div class='subcontent-info'>#{t("department")} : #{employee.employee_department.name}</div>
          <div class='subcontent-info'>#{t("leave_type")} : #{t(leave_type)}</div>".html_safe
          else
            "<div class='subcontent-header themed_text'>#{t("deleted_employee")}</div>
          <div class='subcontent-info'>#{t("leave_type")} : #{t(leave_type)}</div>".html_safe
          end
        end
      end
    end
  end

  module ExamDashboardText
    def self.included(base)
      base.class_eval do
        def examinations_dashboard_text
          exam_group = self.exam_group
          "<div class='timetable_left'><img src='images/icons/subjects/#{self.subject.icon_number}'></div>
          <div class='timetable_right'>
          <div class='subcontent-header themed_text'>#{exam_group.name}</div>
          <div class='subcontent-info'>#{t("subject_text")} : #{self.subject.name} (#{self.subject.code})</div>
          <div class='subcontent-info'>#{I18n.l(self.start_time,:format=>"%I.%M%p")} - #{I18n.l(self.end_time,:format=>"%I.%M%p")}</div>
          <div class='subcontent-info'>#{t("batch")} : #{exam_group.batch.full_name}</div></div>".html_safe
        end
      end
    end
  end

  module ApplyLeaveDashboardText
    def self.included(base)
      base.class_eval do
        def leave_applications_dashboard_text
          employee = self.employee
          if employee
            "<div class='subcontent-info'>#{EmployeeLeaveType.find(self.employee_leave_types_id).name}</div>
          <div class='subcontent-info'>#{I18n.l(self.start_date,:format=>"%d %B, %Y")} - #{I18n.l(self.end_date,:format=>"%d %B, %Y")}</div>
          <div class='subcontent-header themed_text'>#{employee.full_name}</div>
          <div class='subcontent-info'>#{t("emp_no")} : #{employee.employee_number}</div>
          <div class='subcontent-info'>#{t("department")} : #{employee.employee_department.name}</div>".html_safe
          else
            "<div class='subcontent-info'>#{EmployeeLeaveType.find(self.employee_leave_types_id).name}</div>
          <div class='subcontent-info'>#{I18n.l(self.start_date,:format=>"%d %B, %Y")} - #{I18n.l(self.end_date,:format=>"%d %B, %Y")}</div>
          <div class='subcontent-header themed_text'>#{t("deleted_employee")}</div>".html_safe
          end
        end

        #        def employees_on_leave_dashboard_text
        #          employee = self.employee
        #          "<div class='subcontent-info'>#{EmployeeLeaveType.find(self.employee_leave_types_id).name}</div>
        #          <div class='subcontent-info'>#{self.start_date.strftime("%d %B, %Y")} - #{self.end_date.strftime("%d %B, %Y")}</div>
        #         <div class='subcontent-header themed_text'>#{employee.full_name}</div>
        #          <div class='subcontent-info'>Employee No. : #{employee.employee_number}</div>
        #          <div class='subcontent-info'>Department : #{employee.employee_department.name}</div>#".html_safe
        #        end
      end
    end
  end

  module SmsDashboardText
    def self.included(base)
      base.class_eval do
        def sms_sent_dashboard_text
          message = self.sms_message
          "<div class='subcontent-header themed_text'>#{self.mobile}</div>
          <div class='subcontent-info'>#{CGI.unescape(message.body)}</div>
          <div class='subcontent-footer'>
          <div class='footer-date'>#{t("sent_at")} #{self.created_at.strftime("%I.%M%p")}</div>
          </div>".html_safe
        end
      end
    end
  end

  module FinanceDashboardText
    def self.included(base)
      base.class_eval do
        def finance_dashboard_text
          total_income = FinanceTransaction.sum(:amount, :conditions=>["transaction_date = ? AND category_id IN (select id from finance_transaction_categories where is_income = 1)",self.transaction_date])
          total_expense = FinanceTransaction.sum(:amount, :conditions=>["transaction_date = ? AND category_id IN (select id from finance_transaction_categories where is_income = 0)",self.transaction_date])
          currency = Configuration.currency
          "<div class='subcontent-header themed_text'>
          <span class='header-left'>#{t("total_income")} (#{currency}) : </span><span class='header-right'>#{Champs21Precision.set_and_modify_precision(total_income)}</span>
          </div>
          <div class='subcontent-header themed_text'>
          <span class='header-left'>#{t("total_expense")} (#{currency}) : </span><span class='header-right'>#{Champs21Precision.set_and_modify_precision(total_expense)}</span>
          </div>".html_safe
        end
      end
    end
  end

  module TimetableDashboardText
    def self.included(base)
      base.class_eval do
        def timetable_dashboard_text     
          if self.subject.icon_number?
            "<div class='timetable_left'><img src='images/icons/subjects/#{self.subject.icon_number}'></div><div class='timetable_right'><div class='subcontent-info'>#{I18n.l(self.class_timing.start_time,:format=>"%I.%M%p")} - #{I18n.l(self.class_timing.end_time,:format=>"%I.%M%p")}</div>
            <div class='subcontent-header themed_text'>#{self.subject.name}</div>
            <div class='subcontent-info'>#{self.batch.full_name}hhhh</div></div>".html_safe
          else
            "<div class='timetable_left'><img src='images/icons/subjects/8.png'></div><div class='timetable_right'><div class='subcontent-info'>#{I18n.l(self.class_timing.start_time,:format=>"%I.%M%p")} - #{I18n.l(self.class_timing.end_time,:format=>"%I.%M%p")}</div>
            <div class='subcontent-header themed_text'>#{self.subject.name}</div>
            <div class='subcontent-info'>#{self.batch.full_name}hhhh</div></div>".html_safe
          end
        end
      end
    end
  end

  module TaskDashboardText
    def self.included(base)
      base.class_eval do
        def tasks_due_dashboard_text
          "<div class='subcontent-header themed_text'>#{self.title}</div>
           <div class='subcontent-footer'>
            <div class='footer-date'>#{t("posted_by")} #{self.user.present? ? self.user.full_name : t('deleted_user')}</div>
            <div class='footer-comment'>
             <span class='footer-comment-icon'></span> #{self.task_comments.count}
            </div>
           </div>".html_safe
        end
      end
    end
  end

  module DiscussionDashboardText
    def self.included(base)
      base.class_eval do
        def discussions_dashboard_text
          "<div class='subcontent-header themed_text'>#{self.post_title}</div>
           <div class='subcontent-info'>#{t("group_text")} : #{self.group.group_name}</div>
           <div class='subcontent-footer'>
            <div class='footer-date'>#{t("posted_by")} #{self.user.present? ? self.user.full_name : t("deleted_user")}</div>
            <div class='footer-comment'>
             <span class='footer-comment-icon'></span> #{self.group_post_comments.count}
            </div>
           </div>".html_safe
        end
      end
    end
  end

  module BlogDashboardText
    def self.included(base)
      base.class_eval do
        def blogs_dashboard_text
          "<div class='subcontent-header themed_text'>#{self.title}</div>
           <div class='subcontent-info'>#{t("blog_text")} : #{self.blog.name}</div>
           <div class='subcontent-footer'>
            <div class='footer-date'>#{t("posted_by")} #{self.blog.user.present? ? self.blog.user.full_name : t('deleted_user')}</div>
            <div class='footer-comment'>
             <span class='footer-comment-icon'></span> #{self.blog_comments.count}
            </div>
           </div>".html_safe
        end
      end
    end
  end

  module PollDashboardText
    def self.included(base)
      base.class_eval do
        def polls_dashboard_text
          "<div class='subcontent-header themed_text'>#{self.title}</div>
           <div class='subcontent-info'>#{t("votes")} : #{self.poll_votes.count}</div>
           <div class='subcontent-footer'>
            <div class='footer-date'>#{t("created_by")} #{self.poll_creator.present? ? self.poll_creator.full_name : t('deleted_user')}</div>
           </div>".html_safe
        end
      end
    end
  end

  module LibraryDashboardText
    def self.included(base)
      base.class_eval do
        def book_return_due_dashboard_text
          book = self.book
          "<div class='subcontent-info'>#{t("book_no")} : #{book.book_number}</div>
          <div class='subcontent-header themed_text'>#{book.title}</div>
          <div class='subcontent-info'>#{t("author")} : #{book.author}</div>
          <div class='subcontent-footer'>
          <div class='footer-date'>#{t("issued_on")} #{I18n.l(self.issue_date,:format=>"%d %B, %Y")}</div>
          </div>".html_safe
        end
      end
    end
  end

  module OnlineMeetingDashboardText
    def self.included(base)
      base.class_eval do
        def online_meetings_dashboard_text
          "<div class='subcontent-header themed_text'>#{self.name}</div>
          <div class='subcontent-info'>#{t("scheduled_on")} : #{I18n.l(self.scheduled_on,:format=>"%I.%M%p")}</div>
          <div class='subcontent-info'>#{t("server")} : #{self.server.name}</div>
          <div class='subcontent-footer'>
          <div class='footer-date'>#{t("created_by")} #{self.user.present? ? self.user.full_name : t('deleted_user')}</div>
          </div>".html_safe
        end
      end
    end
  end

  module PlacementDashboardText
    def self.included(base)
      base.class_eval do
        def placements_dashboard_text
          "<div class='subcontent-header themed_text'>#{self.title}</div>
          <div class='subcontent-info'>#{t("company")} : #{self.company}</div>".html_safe
        end
      end
    end
  end

  module BirthdayDashboardText
    def self.included(base)
      base.class_eval do
        def birthdays_dashboard_text
          if self.admin? or self.employee?
            employee = self.employee_record
            if employee and employee.photo.file?
              photo_path = employee.photo.url(:original, false)
            else
              photo_path = "images/HR/default_employee.png"
            end
            "<div class='birthday-subcontent'>
            <div class='birthday-image'>
            <img src='#{photo_path}' alt='#{employee.full_name}' class='image-icon'>
            </div>
            <div class='birthday-text'>
            <div class='subcontent-header themed_text'>#{employee.full_name}</div>
            <div class='subcontent-info'>#{t("department")} : #{employee.employee_department.name}</div>
            </div></div>".html_safe
          else
            student = self.student_record
            if student.photo.file?
              photo_path = student.photo.url(:original, false)
            else
              photo_path = "images/master_student/profile/default_student.png"
            end
            "<div class='birthday-subcontent'>
            <div class='birthday-image'>
            <img src='#{photo_path}' alt='#{student.full_name}' class='image-icon'>
            </div>
            <div class='birthday-text'>
            <div class='subcontent-header themed_text'>#{student.full_name}</div>
            <div class='subcontent-info'>#{t("batch")} : #{student.batch.full_name}</div>
            </div></div>".html_safe
          end
        end
      end
    end
  end

  module GalleryDashboardText
    def self.included(base)
      base.class_eval do
        def photos_added_dashboard_text
          "<div class='birthday-subcontent'>
            <div class='gallery-image'>
            <img src='/galleries/show_image/#{self.id}' alt='#{self.name}' class='gallery-icon'>
            </div>
            <div class='gallery-text'>
            <div class='subcontent-header themed_text'>#{self.name}</div>
            <div class='subcontent-info'>#{t("category")} : #{self.gallery_category.name}</div>
            </div></div>".html_safe
        end
      end
    end
  end
  
  
  module AssignmentDashboardText    
    def self.included(base)
      base.class_eval do
        def homework_dashboard_text
          "<div class='subcontent-header themed_text'>#{self.title}</div>
           <div class='subcontent-info'>
            #{I18n.l(self.duedate,:format=>"%d %B, %Y %I.%M%p")}
           </div>
           <div class='subcontent-info'>#{self.content}</div> ".html_safe
        end
        

      end
    end
  end

end

