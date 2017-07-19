require 'net/http'
class Champs21EmailAlertEmailMaker
  attr_accessor :recipients, :message,:subject,:sender,:hostname,:footer,:rtl,:school_id
  def initialize(sender,subject,message, recipients,hostname,footer,rtl)
    @recipients = recipients
    @message = message
    @subject=subject
    @sender=sender
    @hostname=hostname
    @footer=footer
    @rtl=rtl
    unless @sender.present?
      @sender=Champs21.present_user.email if Champs21.present_user.present?
    end

  end
  def perform
    Champs21Emailer::deliver_emails(@sender,@recipients, @subject, @message,@hostname,@footer,@rtl)
  end
end
