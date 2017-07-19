namespace :champs21_data_palette do
  desc "Explaining what the task does"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_data_palette/public ."
  end
end
