namespace :champs21_theme do
  desc "Install Champs21 Theming Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_theme/public ."
  end
end
