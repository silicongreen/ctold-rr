namespace :champs21_oauth do
  desc "Install Champs21 Google Oauth"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_oauth/public ."
  end
end
