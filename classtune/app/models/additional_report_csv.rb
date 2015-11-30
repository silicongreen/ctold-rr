class AdditionalReportCsv < ActiveRecord::Base
  serialize :parameters, Hash
  has_attached_file :csv_report,    
	:url => "/uploads/report/csv_report_download/:class/:attachment/:id/:style/:attachment_fullname?:timestamp",
    :path => "public/uploads/report/csv_report_download/:class/:attachment/:id/:style/:basename.:extension"

  def csv_generation
    method_name=self.method_name
    data=self.model_name.camelize.constantize.send(method_name,self.parameters)
    file_path="tmp/#{Time.now.strftime("%H%M%S%d%m%Y")}_#{method_name}.csv"
    FasterCSV.open(file_path, "wb") do |csv|
      data.each do |row_data|
        csv << row_data
      end
    end
    self.csv_report = open(file_path)
    if self.save
      File.delete(file_path)
    end
  end

end
#:url => "/report/csv_report_download/:id",
#:path => "uploads/:class/:attachment/:id_partition/:style/:basename.:extension"
