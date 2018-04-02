function show_summary_total( data_summary )
{
    if (j("#summary")[0])
    {
        Morris.Donut({
              element: 'summary',
              data: data_summary,
              colors: ['#26B99A', '#34495E', '#ACADAC', '#3498DB'],
              formatter: function (y) {
                return y;
              },
              resize: true
        });
    }
}

function show_summary_own( data_own_summary )
{
    if (j("#own_summary")[0])
    {
        Morris.Bar({
              element: 'own_summary',
              data: data_own_summary,
              xkey: 'device',
              ykeys: ['geekbench'],
              labels: ['Value'],
              barRatio: 0.4,
              barColors: ['#3498DB', '#34495E', '#ACADAC', '#3498DB'],
              xLabelAngle: 35,
              hideHover: 'auto',
              resize: true
        });
    }
}

