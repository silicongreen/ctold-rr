namespace :champs21_inventory do
  desc "Install Champs21 Inventory Module"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_inventory/public ."
  end
end
