namespace :champs21_custom_import do
  desc "Install Champs21 Data Import Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_custom_import/public ."
  end
end
