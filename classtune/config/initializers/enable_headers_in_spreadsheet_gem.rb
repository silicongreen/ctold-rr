require 'spreadsheet'
module Spreadsheet
  module Excel
    module Writer
##
# Writer class for Excel Worksheets. Most write_* method correspond to an
# Excel-Record/Opcode. You should not need to call any of its methods directly.
# If you think you do, look at #write_worksheet
  class Worksheet
          alias_method :write_from_scratch_without_header_footer, :write_from_scratch

          def write_from_scratch
            if @worksheet.header
              write_op opcode(:header), [@worksheet.header.bytesize, 0].pack("vC"), @worksheet.header
            end
            if @worksheet.footer
              write_op opcode(:footer), [@worksheet.footer.bytesize, 0].pack("vC"), @worksheet.footer
            end
            write_from_scratch_without_header_footer
          end
        end
      end
    end

  class Worksheet
    attr_accessor :header, :footer

    def add_header(str)
      @header = str
    end

    def add_footer(str)
      @footer = str
    end
  end
end
