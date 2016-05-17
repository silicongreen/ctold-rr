class Task < ActiveRecord::Base
 
  after_create :notify_assignees
  before_update :notify_assignees_about_update
  belongs_to :user
  has_many :task_comments
  has_many :task_assignees
  has_many :assignees, :through=>:task_assignees, :class_name=>'User'

  has_attached_file :attachment,
#    :path => "uploads/:class/:user_id/:id_partition/:basename.:extension",
#    :url => "/tasks/download_attachment/:id"
	:url => "/uploads/:class/:attachment/:id/:style/:attachment_fullname?:timestamp",
    :path => "public/uploads/:class/:attachment/:id/:style/:basename.:extension"

  validates_presence_of :user_id, :title, :description, :status, :due_date, :start_date

  validates_inclusion_of :status, :in => %w(Assigned Completed), :message => :invalid_status
  validates_attachment_size :attachment, :less_than => 500.kilobytes,  :message=> :should_be_less_than

  delegate :first_name,:to => :user,:allow_nil=>true,:prefix=>:user

  def can_be_viewed_by?(user_in_question)
    (user_in_question==self.user || self.assignees.include?(user_in_question))
  end

  def can_be_downloaded_by?(user_in_question)
    (user_in_question==self.user || self.assignees.include?(user_in_question))
  end

  def task_can_be_deleted_by?(user_in_question)
    (user_in_question==self.user and (user_in_question.admin? or user_in_question.privileges.collect(&:name).include?("TaskManagement")))
  end

  def task_can_be_edited_by?(user_in_question)
    (user_in_question==self.user and (user_in_question.admin? or user_in_question.privileges.collect(&:name).include?("TaskManagement")))
  end
  def validate
    if start_date.to_date > due_date.to_date
      self.errors.add(:due_date, :cannot_be_before_start_date)
    end
    if self.new_record?
      unless self.due_date.nil?
        self.errors.add(:due_date, :should_be_in_the_future) if self.due_date < Date.today
      end
    end
  end
  
  def due?
    due_date >= Date.today
  end

  def notify_assignees
    recipients = self.assignees
#    unless recipients.blank?
#      recipients.each do |u|
#        Reminder.create(:sender=> self.user_id,
#          :recipient=> u.id,
#          :subject=>"#{t('new_task')} : #{self.title}",
#          :body=>" #{t('new_task_description')}: #{self.description} <br/> #{t('end_date')} : " + self.due_date.strftime("%d %B %Y"))
#      end
#    end
  end
  def notify_assignees_about_update
    recipients = self.assignees
#    recipients.each do |u|
#      if self.changed.include?("status")
#        Reminder.create(:sender=> self.user_id,:recipient=> u.id,:subject=>"#{t('task_status_changed')}: #{self.title}",
#          :body=>" #{t('task_status_changed_to')} : #{self.status}")
#      else
#        Reminder.create(:sender=> self.user_id,:recipient=> u.id,:subject=>"#{t('task_updated')} : #{self.title}",
#          :body=>" #{t('new_task_description')}: #{self.description} <br/> #{t('end_date')} : " + self.due_date.strftime("%d %B %Y"))
#      end
#    end
  end

  def self.latest_comments_for_user(user,limit)
    all_tasks = user.tasks.collect(&:id) + user.assigned_tasks.collect(&:id)
    TaskComment.find(:all,:conditions=>{:task_id=>all_tasks.uniq},:include=>:task,:limit=>limit,:order=>"updated_at DESC")
  end

  
  Paperclip.interpolates :user_id  do |attachment, style|
    attachment.instance.user_id
  end
end
