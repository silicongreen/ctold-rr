class AddAttachmentsExportFileToTallyExportFile < ActiveRecord::Migration
  def self.up
    add_column :tally_export_files, :export_file_file_name, :string
    add_column :tally_export_files, :export_file_content_type, :string
    add_column :tally_export_files, :export_file_file_size, :integer
    add_column :tally_export_files, :export_file_updated_at, :datetime
  end

  def self.down
    remove_column :tally_export_files, :export_file_file_name
    remove_column :tally_export_files, :export_file_content_type
    remove_column :tally_export_files, :export_file_file_size
    remove_column :tally_export_files, :export_file_updated_at
  end
end
