WickedPdf.config = {
    :wkhtmltopdf => '/opt/wkhtmltopdf-amd64',
    :layout => "pdf.html",
    :margin => {    :top=> 50,
                    :bottom => 20,
                    :left=> 10,
                    :right => 10},
    :encoding => "utf8",
    :header => {:html => { :template=> 'layouts/pdf_header.html'}},
    :footer => {:html => { :template=> 'layouts/pdf_footer.html'}}
    #:exe_path => '/usr/bin/wkhtmltopdf'
}
