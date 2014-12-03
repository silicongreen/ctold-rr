namespace :champs21_discipline do
  desc "Install Champs21 Discipline Module"
  task :install do
    system "rsync -ruv --exclude=.svn vendor/plugins/champs21_discipline/public ."
  end
end
