namespace :champs21_blog do
  desc "Install Champs21 Blog Module"
  task :install do
    #system "rsync -ruv --exclude=.svn vendor/plugins/champs21_blog/db/migrate db"
    system "rsync -ruv --exclude=.svn vendor/plugins/champs21_blog/public ."
  end
end
