@extends('layouts.app')
@section('pages-style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
@endsection
@section('pages-script')
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        let table = new DataTable('#kt_datatable_example_1');
    </script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"> </script>
    <script type="text/javascript">
        window.onload = function () {

        var chart = new CanvasJS.Chart("chartContainer", {
            theme: "light1", // "light2", "dark1", "dark2"
            animationEnabled: true, // change to true      
            title:{
                text: "Hasil Prediksi"
            },
            data: [
            {
                // Change type to "bar", "area", "spline", "pie",etc.
                type: "column",
                dataPoints: [
                    
                    { label: "Positif",  y: {{$positif}}  },
                    { label: "Negatif", y: {{$negatif}}  }
                ]
            }
            ]
        });
        chart.render();

        }
    </script>
<script src="https://d3js.org/d3.v4.js"></script>
<script src="https://cdn.jsdelivr.net/gh/holtzy/D3-graph-gallery@master/LIB/d3.layout.cloud.js"></script>
<script type="text/javascript">
    
    // List of words
    var myWords = <?php echo $arrAllOke?>

    // set the dimensions and margins of the graph
    var margin = {top: 10, right: 10, bottom: 10, left: 10},
        width = 450 - margin.left - margin.right,
        height = 450 - margin.top - margin.bottom;

    // append the svg object to the body of the page
    var svg = d3.select("#my_dataviz").append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform",
              "translate(" + margin.left + "," + margin.top + ")");

    var fill = d3.scaleOrdinal(d3.schemeCategory10);
    // Constructs a new cloud layout instance. It run an algorithm to find the position of words that suits your requirements
    var layout = d3.layout.cloud()
      .size([width, height])
      .words(myWords.map(function(d) {
        return {text: d, size: 10 + Math.random() * 90};
    }))
      .padding(5)
      .rotate(function() { return ~~(Math.random() * 2) * 90; })
       .font("Impact")
      .fontSize(function(d) { return d.size; })
      .on("end", draw);
    layout.start();


    // This function takes the output of 'layout' above and draw the words
    // Better not to touch it. To change parameters, play with the 'layout' variable above
    function draw(words) {
      svg
        .append("g")
            .attr("transform", "translate(" + layout.size()[0] / 2 + "," + layout.size()[1] / 2 + ")")
            .selectAll("text")
            .data(words)
            .enter().append("text")
            .style("font-size", function(d) { return d.size + "px"; })
            .style("font-family", "Impact")
            .style("fill", function(d, i) { return fill(i); })
            .attr("text-anchor", "middle")
            .attr("transform", function(d) {
                return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
            })
            .text(function(d) { return d.text; });
    }
</script>
@endsection
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="card p-6">
                    <h2 align="center">Grafik Data Uji</h2>
                    <br>
                    <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                </div>
            </div>
            <div id="kt_content_container" class="container-xxl">
                <div class="card p-6">
                    <h2 align="center">Word Cloud</h2>
                    <br>
                    <div id="my_dataviz"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="card p-12">
                    <h2 align="center">Confusion Matrix</h2>
                    <br>
                    <div class="table-responsive">
                        <table border="2" class="table align-middle table-row-dashed fs-6 gy-5">
                             <thead>
                                <tr>
                                    <th rowspan="2" 
                                        style="text-align: center;
                                               padding-bottom: 7%;
                                               border: 1px solid black">
                                        <b>Aktual</b>
                                    </th>
                                    <th colspan="3" 
                                        style="text-align: center;border: 1px solid black">
                                        <b>Prediksi Sentimen</b>
                                    </th>
                                </tr>
                                <tr style="border: 1px solid black">
                                    <th style="text-align: center;"><b>Positif</b></th>
                                    <th style="text-align: center;"><b>Negatif</b></th>
                                    <th style="text-align: center;"><b>Netral</b></th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @foreach ($confusionMatrix as $actual => $item)
                                    <tr style="text-align: center;border: 1px solid black">
                                        <td>
                                            <b style="color: black">{{$actual}}</b>
                                        </td>
                                       @foreach ($item as $predicted => $count) 
                                            <td style="color: red">{{$count}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id="kt_content_container" class="container-xxl">
                <div class="card p-12">
                    <h2 align="center">Metrik Evaluasi</h2>
                    <br>
                    <div class="table-responsive">
                        <table border="2" class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr style="border: 1px solid black">
                                    <th style="text-align: center;"><b>Sentimen</b></th>
                                    <th style="text-align: center;"><b>Precision</b></th>
                                    <th style="text-align: center;"><b>Recall</b></th>
                                   <!--  <th style="text-align: center;">F1-score</th> -->
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @foreach ($metrics as $label => $metric)
                                    <tr style="text-align: center; border: 1px solid black">
                                        <td><b style="color: black">{{$label}}</b></td>
                                        <td>{{round($metric['precision'], 2)}}</td>
                                        <td>{{round($metric['recall'], 2)}}</td>
                                       <!--  <td>{{round($metric['f1_score'], 2)}}</td> -->
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-xxl">
                <div class="card p-6">
                    <div class="table-responsive">
                        <!--begin::Datatable-->
                        <table id="kt_datatable_example_1" class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th>No</th>
                                    <th>Konten</th>
                                    <th>Sentimen System</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @foreach ($dataUji as $key => $item)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$item->konten}}</td>
                                        <td>
                                            {{$item->label}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!--end::Datatable-->
                    </div>
                </div>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
@endsection