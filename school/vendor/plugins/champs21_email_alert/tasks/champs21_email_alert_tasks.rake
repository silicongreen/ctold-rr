namespace :champs21_email_alert do
  desc "Install Champs21 Email Alert Plugin Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_email_alert/public ."
  end
end
