namespace :champs21_custom_report do
  desc "Install Champs21 Custom Report Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_custom_report/public ."
  end
end