namespace :champs21_bigbluebutton do
  desc "Install BigblueButton module for Champs21"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_bigbluebutton/public ."
  end
end
