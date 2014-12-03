namespace :champs21_instant_fee do
  desc "Install Champs21 Instant Fee Module"
  task :install do
    system "rsync -ruv --exclude=.svn vendor/plugins/champs21_instant_fee/public ."
  end
end
