namespace :champs21_placement do
  desc "Install Champs21 Placement Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_placement/public ."
  end
end
