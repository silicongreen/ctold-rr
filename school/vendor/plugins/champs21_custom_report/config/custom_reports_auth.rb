
authorization do
  role :custom_report_control do
    has_permission_on [:custom_reports],:to=>[:generate,:show,:index,:delete,:select_school,:to_csv,:dashboard,:custom_report_pdf]
  end
  role :custom_report_view do
    has_permission_on [:custom_reports],:to=>[:show,:index,:to_csv,:custom_report_pdf]
  end
  role :admin do
    includes :custom_report_control
  end
 
end
