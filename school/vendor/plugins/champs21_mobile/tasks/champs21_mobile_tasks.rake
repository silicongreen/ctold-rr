namespace :champs21_mobile do
  desc "Install Champs21 Mobile"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_mobile/public ."
  end
end