namespace :champs21_moodle do
  desc "Install Champs21 Moodle Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_moodle/public ."
  end
end
