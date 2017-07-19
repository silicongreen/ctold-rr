namespace :champs21_tally_export do
  desc "Install Champs21 Tally Export Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_tally_export/public ."
  end
end
