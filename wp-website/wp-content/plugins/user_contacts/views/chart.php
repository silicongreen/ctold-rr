
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        //data.addRows([["12am-4am",2],["4am-8am",0],["8am-12pm",0],["12pm-4pm",0],["4pm-8pm",0],["8pm-12am",0]]);
        data.addRows(
          <?php echo json_encode($j_array); ?>
        );

        // Set chart options
        var options = {'title':'User Preferred Time To Chat',
                       'width':1000,
                       'height':800,is3D: true};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        options.chartArea = { 'left':250,'width': '100%', 'height': '80%'}
        chart.draw(data, options);
      }
</script>
<div class="wrap">
    <div id="chart_div"></div>
</div>