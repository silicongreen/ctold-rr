class Champs21Emailer < ActionMailer::Base

def emails(sender,recipients, subject, message,hostname,footer,rtl)
    recipient_emails = (recipients.class == String) ? recipients.gsub(' ','').split(',').compact : recipients.compact
    setup_emails(sender, recipient_emails, subject, message,hostname,footer,rtl)
end

  protected
  def setup_emails(sender, emails, subject, message,hostname,footer,rtl)
    @from = sender
    @bcc = emails
    @subject = subject
    @sent_on = Time.now
    @body['message'] = message
    @body['hostname'] = hostname
    @body['footer']=footer
    @body['rtl']=rtl
    @body['email']=emails
    @content_type="text/html; charset=utf-8"
  end

end