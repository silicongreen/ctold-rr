namespace :champs21_hostel do
  desc "Install Champs21 Hostel Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_hostel/public ."
  end
end
