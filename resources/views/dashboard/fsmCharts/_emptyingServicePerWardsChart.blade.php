<!-- Last Modified Date: 18-04-2024
Developed By: Innovative Solution Pvt. Ltd. (ISPL)   -->
@include('layouts.dashboard.chart-card',[
    'card_title' => "Summary of Applications, Emptying Services, Sludge Disposal, and Feedback by Ward",
    'export_chart_btn_id' => "exportemptyingServicePerWardsChart",
    'canvas_id' => "emptyingServicePerWardsChart"
])
@push('scripts')
<script>
var ctx = document.getElementById("emptyingServicePerWardsChart");
var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: [<?php echo implode(',', $emptyingServicePerWardsChart['labels']); ?>],
    datasets: [
        @foreach($emptyingServicePerWardsChart['datasets'] as $dataset)
        {
            stack: <?php echo $dataset['stack']; ?>,
            label: <?php echo $dataset['label']; ?>,
            backgroundColor: <?php echo $dataset['color']; ?>,
            data: [<?php echo implode(',', $dataset['data']); ?>],
        },
        @endforeach
    ]
},
  options: {
    animation:{
      animateScale:true
    },
    responsive: true,
      legend: {
         labels: {
              boxWidth: 10
          }
      },
    scales: {
     xAxes: [{
                 scaleLabel: {
        display: true,
        labelString: 'Wards',
        //fontSize: 10,
      },
            
        }],
        yAxes: [{
            stacked: true,
            ticks: {
                beginAtZero: true,
                userCallback: function(label, index, labels) {
                     // when the floored value is the same as the value we have a whole number
                     if (Math.floor(label) === label) {
                         return label;
                     }

                 }
              
            }
        }]
    },
    tooltips: {
        mode: 'index',
    }
   
  }
});
document.getElementById('exportemptyingServicePerWardsChart').addEventListener("click", downloadIMG);
  //donwload pdf from original canvas
  function downloadIMG() {
    var newCanvas = document.querySelector('#emptyingServicePerWardsChart');

    //create image from dummy canvas
    var newCanvasImg = newCanvas.toDataURL("image/png", 1.0);
    var a = document.createElement('a');
    a.href =newCanvas.toDataURL("image/png", 1.0);

    a.download = 'Summary of Applications, Emptying Services, Sludge Disposal, and Feedback by Ward.png';

    // Trigger the download
    a.click();
      }
</script>
@endpush
