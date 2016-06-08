namespace :champs21_pay do
  desc "Install Champs21 Pay Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_pay/public ."
  end
end
