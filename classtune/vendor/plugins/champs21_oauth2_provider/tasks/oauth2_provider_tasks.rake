namespace :champs21_oauth2_provider do
  desc "Install Champs21 Oauth2 Provider"
  task :install do
    system "rsync -ruv --exclude=.svn vendor/plugins/champs21_oauth2_provider/public ."
  end
end
