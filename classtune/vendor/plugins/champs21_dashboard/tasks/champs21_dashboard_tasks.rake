namespace :champs21_dashboard do
  desc "Explaining what the task does"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_dashboard/public ."
  end
end
