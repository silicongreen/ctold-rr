namespace :champs21_library do
  desc "Install Champs21 Library Module"
  task :install do
    system "rsync -ruv --exclude=.svn vendor/plugins/champs21_library/public ."
  end
end
