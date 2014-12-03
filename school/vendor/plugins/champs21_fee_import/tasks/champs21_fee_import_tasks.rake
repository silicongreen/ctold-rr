namespace :champs21_fee_import do
  desc "Install Champs21 Fee Import Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_fee_import/public ."
  end
end
