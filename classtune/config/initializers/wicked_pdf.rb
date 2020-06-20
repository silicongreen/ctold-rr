WickedPdf.config = {
    :wkhtmltopdf => '/usr/bin/wkhtmltopdf',
    :layout => "pdf.html",
    :margin => {    :top=> 30,
                    :bottom => 30,
                    :left=> 10,
                    :right => 10},
    :encoding => "utf8",
    :header => {:html => { :template=> 'layouts/pdf_header.html'}},
    :footer => {:html => { :template=> 'layouts/pdf_footer.html'}}
    #:exe_path => '/usr/bin/wkhtmltopdf'
}
