namespace :champs21_poll do
  desc "Install Champs21 Poll Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_poll/public ."
  end
end
