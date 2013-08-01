$(document).ready(function(){
      var currentDate, targetDate, timeDif;
      
      //$.getJSON("time.php", function(data){
      //currentDate = data.currentTime;
      ////Pull from MySQL
      //targetDate = data.targetTime;
      
      currentDate = new Date().getTime();
      targetDate = new Date(currentDate + 600 ).getTime();
      
      init();
      
      //})
      
      
      function init(){
            var Days, Hours, Minutes, Seconds;
            
            timeDif = targetDate - currentDate;
      
            function updateTime(){
                  Seconds= timeDif;
                  Days = Math.floor(Seconds/86400);
                  Seconds -= Days * 86400;
                  
                  Hours = Math.floor(Seconds/3600);
                  Seconds -= Hours * 3600;
                  
                  Minutes = Math.floor(Seconds/60);
                  Seconds -= Minutes*60;
            
                  Seconds = Math.floor(Seconds);
            }
            
            function tick(){
                  clearTimeout(timer);
                  updateTime();
                  displayTime();
                  
                  if(timeDif>0){
                        timeDif--;
                        timer = setTimeout(tick,1*1000);
                  }else{
                        
                        $("#timeDisplay").html("Timer Done");
                  }
            }
            
            function displayTime(){
                  var out;
                  
                  out = Minutes+" Minutes "+
                        Seconds+" Seconds";
                  
                  $("#timeDisplay").html(out);
                  
            }
            
            var timer = setTimeout(tick, 1*1000);
      }
});