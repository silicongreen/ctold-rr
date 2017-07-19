namespace :champs21_google_doc do
  desc "Install Champs21 Google Doc"
  task :install do
    system "rsync --exclude=.svn -ruv vendor/plugins/champs21_google_doc/public ."

    Dir.mkdir("#{Rails.root}/public/google_docs") unless File.exists?("#{Rails.root}/public/google_docs")
    Dir.mkdir("#{Rails.root}/public/google_uploads") unless File.exists?("#{Rails.root}/public/google_uploads")
  end
end
