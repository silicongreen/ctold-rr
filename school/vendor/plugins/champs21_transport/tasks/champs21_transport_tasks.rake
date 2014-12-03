namespace :champs21_transport do
  desc "Install Champs21 Transport Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_transport/public ."
  end
end
