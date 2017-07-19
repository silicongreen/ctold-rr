namespace :champs21_online_exam do
  desc "Install Champs21 Online Exam Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_online_exam/public ."
  end
end
