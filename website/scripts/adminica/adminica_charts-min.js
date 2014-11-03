function adminicaCharts(){if($(".flot").length>0){var e=[];for(var t=0;t<14;t+=.2)e.push([t,Math.sin(t)+8]);var n=[[0,5],[2,7],[4,11],[6,1],[8,8],[10,7],[12,9],[14,3]],r=[[1,3],[3,8],[5,5],[7,13],[9,8],[11,5],[13,8],[15,5]],i=[[0,12],[7,12],[8,2.5],[12,2.5],[15,7]],s=[];for(var t=-20;t<20;t+=.4)s.push([t,Math.tan(t)+t*5]);var o=[[1988,483994],[1989,479060],[1991,401949],[1993,402375],[1994,377867],[1996,337946],[1997,336185],[1998,328611],[2e3,342172],[2001,344932],[2003,440813],[2004,480451],[2006,528692]],u=[],a=Math.floor(Math.random()*5)+1;for(var t=0;t<a;t++)u[t]={label:"Series"+(t+1),data:Math.floor(Math.random()*100)+1};var f=[{label:"Slice 1",data:[[1,117]],color:"#122b45"},{label:"Slice 2",data:[[1,30]],color:"#064792"},{label:"Slice 3",data:[[1,44]],color:"#4C5766"},{label:"Slice 4",data:[[1,90]],color:"#9e253b"},{label:"Slice 5",data:[[1,70]],color:"#8d579a"},{label:"Slice 6",data:[[1,80]],color:"#2b4356"}],l=[{label:"Slice 1",data:[[1,117]],color:"#122b45"},{label:"Slice 2",data:[[1,30]],color:"#064792"},{label:"Slice 3",data:[[1,44]],color:"#4C5766"},{label:"Slice 4",data:[[1,90]],color:"#9e253b"},{label:"Slice 5",data:[[1,70]],color:"#8d579a"},{label:"Slice 6",data:[[1,80]],color:"#2b4356"}],c={colors:["#4C5766 ","#313841 "]},h={colors:["#1C5EA0 ","#064792 "]},p={colors:["#2b4356 ","#122b45 "]},d={colors:["#9e253b ","#7C1F30 "]},v={colors:["#3d8336 ","#277423 "]},m={colors:["#9b6ca6 ","#8d579a "]},g={colors:["#53453e ","#3b2e28 "]},y={colors:["#D0D6DA","#B4BBC1"]},b="#4C5766 ",w="#1C5EA0 ",E="#2b4356 ",S="#9e253b ",x="#3d8336",T="#9b6ca6",N="#53453e";$.plot($("#flot_pie_1"),f,{series:{pie:{innerRadius:0,show:!0},grid:{hoverable:!0,clickable:!0}}});$.plot($("#flot_bar"),[{shadowSize:25,label:"Bar Chart 1",color:T,data:n,bars:{show:!0,fill:!0,fillColor:m,lineWidth:0,border:!1}},{shadowSize:25,label:"Bar Chart 2",color:"#4C5766",data:r,bars:{show:!0,fill:!0,fillColor:c,lineWidth:0,border:!1}}],{grid:{show:!0,aboveData:!1,backgroundColor:{colors:["#fff","#eee"]},labelMargin:15,borderWidth:1,borderColor:"#cccccc",clickable:!0,hoverable:!0,autoHighlight:!0,mouseActiveRadius:10},legend:{show:!0,labelBoxBorderColor:"#fff",noColumns:5,margin:10,backgroundColor:"#fff"}});$.plot($("#flot_line"),[{shadowSize:5,label:"Line Chart 1",color:w,data:e,lines:{show:!0,fill:!0,fillColor:y,lineWidth:4}},{shadowSize:5,label:"Line Chart 2",color:S,data:n,lines:{show:!0,fill:!1,lineWidth:4},points:{show:!0,fill:!1,lineWidth:2}}],{grid:{show:!0,aboveData:!1,backgroundColor:{colors:["#fff","#eee"]},labelMargin:15,borderWidth:1,borderColor:"#cccccc",clickable:!0,hoverable:!0,autoHighlight:!0,mouseActiveRadius:10},legend:{show:!0,labelBoxBorderColor:"#fff",noColumns:5,margin:10,backgroundColor:"#fff"}});$.plot($("#flot_points"),[{shadowSize:10,label:"Points Chart",color:w,data:o,points:{show:!0,fill:!0,fillColor:"#ffffff",lineWidth:3},lines:{show:!0,fill:!0,fillColor:c,lineWidth:5}}],{grid:{show:!0,aboveData:!1,backgroundColor:{colors:["#fff","#eee"]},labelMargin:15,borderWidth:1,borderColor:"#cccccc",clickable:!0,hoverable:!0,autoHighlight:!0,mouseActiveRadius:10},legend:{show:!0,labelBoxBorderColor:"#fff",noColumns:5,margin:10,backgroundColor:"#fff"}})}if($.fn.sparkline){$(".random_number_3").each(function(){var e=Math.floor(Math.random()*7),t=Math.floor(Math.random()*6),n=Math.floor(Math.random()*5);$(this).text(e+","+t+","+n)});$(".random_number_5").each(function(){var e=Math.floor(Math.random()*7),t=Math.floor(Math.random()*6),n=Math.floor(Math.random()*5),r=Math.floor(Math.random()*-1),i=Math.floor(Math.random()*5);$(this).text(e+","+t+","+n+","+r+","+i)});$(".spark_pie.small").sparkline("html",{type:"pie",sliceColors:["#354254","#419DF9","#13578A"]});$(".spark_line.small").sparkline("html",{type:"line",lineWidth:"1",lineColor:"#419DF9",fillColor:"#ccc",spotRadius:"2",spotColor:"#13578A",minSpotColor:"",maxSpotColor:""});$(".spark_bar.small").sparkline("html",{type:"bar",barColor:"#13578A"});$(".spark_pie.medium").sparkline("html",{type:"pie",height:"50px",width:"50px",sliceColors:["#354254","#419DF9","#13578A"]});$(".spark_line.medium").sparkline("html",{type:"line",height:"50px",width:"50px",lineWidth:"1",lineColor:"#419DF9",fillColor:"#ccc",spotRadius:"2",spotColor:"#13578A",minSpotColor:"",maxSpotColor:""});$(".spark_bar.medium").sparkline("html",{type:"bar",height:"50px",barColor:"#419DF9",barWidth:10,negBarColor:"#DA3737",colorMap:{1:"red",2:"red",3:"orange",4:"green",5:"green"}});$(".spark_pie.large").sparkline("html",{type:"pie",height:"75px",width:"75px",sliceColors:["#354254","#419DF9","#13578A"]});$(".spark_line.large").sparkline("html",{type:"line",height:"60px",width:"80%",lineWidth:"2",lineColor:"#419DF9",fillColor:"#ccc",spotRadius:"3",spotColor:"#13578A",minSpotColor:"",maxSpotColor:""});$(".spark_bar.large").sparkline("html",{type:"bar",height:"60px",barColor:"#419DF9",barWidth:15,negBarColor:"#DA3737",colorMap:{1:"red",2:"red",3:"orange",4:"green",5:"green"}});$(".spark_line_wide").sparkline("html",{type:"line",height:"20px",width:"100%",lineWidth:"2",lineColor:"#419DF9",fillColor:"",spotRadius:"2	",spotColor:"#3FC846",minSpotColor:"#DA3737",maxSpotColor:"#3FC846"})}};