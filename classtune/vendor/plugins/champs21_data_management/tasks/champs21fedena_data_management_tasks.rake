namespace :champs21_data_management do
  desc "Install Data Management Module"
  task :install do
    system "rsync -ruv --exclude=.svn vendor/plugins/champs21_data_management/public ."
  end
end
