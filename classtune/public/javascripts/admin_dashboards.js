j(document).ready(function(){
    j("#loading_news").show();
    j("#loading_events").show();
    j("#loading_user_login_today").show();
    j("#loading_user_login_web").show();
    j("#loading_user_login_app").show();
    j("#loading_student_present").show();
    j("#loading_campus_attendance").show();
    j("#loading_hr_attendance").show();
    j("#loading_attendance_graph").show();
    j("#loading_running_class").show();
    j("#loading_routine_all").show();
    j("#loading_task").show();
    j("#loading_room_info").show();
    j("#loading_summary").show();
    j.ajax({
          url: '/dashboards/get_summary_strip/',
          data: { },
          type: 'POST',
          cache: true,
          success: function(data){
              j("#loading_user_login_today").hide();
              j("#loading_user_login_web").hide();
              j("#loading_user_login_app").hide();
              j("#loading_student_present").hide();
              j("#loading_campus_attendance").hide();
              j("#loading_hr_attendance").hide();
              j("#summary_strip").html(data);
              j.ajax({
                    url: '/dashboards/get_news/',
                    data: { },
                    type: 'POST',
                    cache: true,
                    success: function(data){
                        j("#loading_news").hide();
                        j("#news_dashboard").html(data);
                        j.ajax({
                              url: '/dashboards/get_events/',
                              data: { },
                              type: 'POST',
                              cache: true,
                              success: function(data){
                                  j("#loading_events").hide();
                                  j("#events_dashboard").html(data);
                                  j.ajax({
                                        url: '/dashboards/get_attendace_graph/',
                                        data: { },
                                        type: 'POST',
                                        cache: true,
                                        success: function(data){
                                            j("#loading_attendance_graph").hide();
                                            j("#attendance_graph").html(data);
                                            reload_select2();
                                            var total_presents = j("#total_presents").val().split(",");
                                            var total_absents = j("#total_absents").val().split(",");
                                            reloadGraph(total_presents, total_absents);
                                            present = total_presents;
                                            absent = total_absents;
                                            j.ajax({
                                                  url: '/dashboards/get_routines_data/',
                                                  data: { },
                                                  type: 'POST',
                                                  cache: true,
                                                  success: function(data){
                                                      j("#loading_running_class").hide();
                                                      j("#running_class").html(data);
                                                      if (j.trim(data).length == 0)
                                                      {
                                                          j("#running_class_panel").hide();
                                                      }
                                                      j.ajax({
                                                            url: '/dashboards/get_all_routines/',
                                                            data: { filter_enable: 0 },
                                                            type: 'POST',
                                                            cache: true,
                                                            success: function(data){
                                                                j("#loading_routine_all").hide();
                                                                j("#routine_all").html(data);
                                                                reload_select2();
                                                                j.ajax({
                                                                      url: '/dashboards/get_tasks_count/',
                                                                      data: { },
                                                                      type: 'POST',
                                                                      cache: true,
                                                                      success: function(data){
                                                                          j("#loading_task").hide();
                                                                          j("#tasks_div").html(data);
                                                                          j.ajax({
                                                                                url: '/dashboards/get_own_summary/',
                                                                                data: { },
                                                                                type: 'POST',
                                                                                cache: true,
                                                                                success: function(data){
                                                                                    j("#loading_summary").hide();
                                                                                    j("#own_summary_div").html(data);
                                                                                    var data_summary = [
                                                                                      {label: 'Lesson Plan', value : j("#total_lesson_plan").val()}, 
                                                                                      {label: 'Assignments', value : j("#total_assignment").val()},
                                                                                      {label: 'Assignments Submitted', value : j("#assignments_submitted").val()}
                                                                                    ];
                                                                                    var data_own_summary = [
                                                                                      {device: 'LessonPlan', geekbench : j("#total_lesson_plan").val()}, 
                                                                                      {device: 'Assignments', geekbench : j("#total_assignment").val()},
                                                                                      {device: 'Submitted', geekbench : j("#assignments_submitted").val()},
                                                                                      {device: 'Classes', geekbench : j("#total_class_length").val()}
                                                                                    ];
                                                                                    show_summary_total(data_summary);

                                                                                    show_summary_own(data_own_summary);
                                                                                    reload_select2();
                                                                                    reload_select2_ajax();
                                                                                }
                                                                          });
                                                                      }
                                                                });
                                                            }
                                                      });
                                                  }
                                            });
                                        }
                                  });
                              }
                        });
                    }
              });
          }
    });
});