@include('layouts.dashboard.chart-card',[
    'card_title' => "School Type Counts",
    'export_chart_btn_id' => "exportSchoolTypeCount",
    'canvas_id' => "totalSchoolTypeCount"
])

<style>
  #schoolCounts {
    max-height: 650px;
  }
</style>

@push('scripts')
<script>
var ctx = document.getElementById("totalSchoolTypeCount").getContext('2d');
var chartData = @json($schoolTypeCount);

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: chartData.labels,
    datasets: chartData.datasets
  },
  options: {
    animation: {
      animateScale: true
    },
    responsive: true,
    plugins: {
      legend: {
        display: true,
        position: 'bottom',
        align: 'start',
        labels: {
          boxWidth: 10
        }
      }
    },
    scales: {
      x: {
        stacked: true,
        title: {
          display: true,
          text: 'School Types'
        }
      },
      y: {
        stacked: true,
        beginAtZero: true,
        ticks: {
          callback: function(value) {
            if (Number.isInteger(value)) return value;
          }
        },
        title: {
          display: true,
          text: 'Number of Schools'
        }
      }
    }
  }
});

document.getElementById('exportSchoolTypeCount').addEventListener('click', function() {
    var canvas = document.getElementById('totalSchoolTypeCount');
    var image = canvas.toDataURL('image/png').replace("image/png", "image/octet-stream");
    var link = document.createElement('a');
    link.download = 'school_type_count.png';
    link.href = image;
    link.click();
});
</script>
@endpush
