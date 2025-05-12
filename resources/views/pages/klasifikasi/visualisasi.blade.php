@extends('layouts.app')
@section('pages-style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
@endsection
@section('pages-script')
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        let table = new DataTable('#kt_datatable_example_1');
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#merchant_id').change(function() {
                var merchant_id = $(this).val();
                console.log(merchant_id);
                $('#product_id').empty().append('<option value="0" disabled selected>Select Product</option>');

                $.ajax({
                    url: '/get-data',
                    type: 'POST',
                    data: {
                        merchant_id: merchant_id
                    },
                    cache: false,
                    success: function (data) {
                        console.log(data);
                        // $('#product_id').empty();
                        data.product.forEach(function(item) {
                            $('#product_id').append('<option value="'+item.id+'">'+item.nama_produk+'</option>');
                        });

                        var selectedProduct = '{{ $request->product_id }}';
                        if(selectedProduct) {
                            $('#product_id').val(selectedProduct);
                        }
                    }
                });
            });
            var selectedMerchant = '{{ $request->merchant_id }}';
            if(selectedMerchant) {
                $('#merchant_id').val(selectedMerchant);
                $('#merchant_id').trigger('change');
            }
        });
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
                    { label: "Negatif", y: {{$negatif}}  },
                    { label: "Netral",  y: {{$netral}}  }
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
    var myWords = <?php echo $arrAllOkePositif?>
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

    var myWordsNegatif = <?php echo $arrAllOkeNegatif?>
    // set the dimensions and margins of the graph
    var marginNegatif = {top: 10, right: 10, bottom: 10, left: 10},
        width = 450 - marginNegatif.left - marginNegatif.right,
        height = 450 - marginNegatif.top - marginNegatif.bottom;

    // append the svg object to the body of the page
    var svgNegatif = d3.select("#my_dataviz_negatif").append("svg")
        .attr("width", width + marginNegatif.left + marginNegatif.right)
        .attr("height", height + marginNegatif.top + marginNegatif.bottom)
        .append("g")
        .attr("transform",
              "translate(" + marginNegatif.left + "," + marginNegatif.top + ")");

    var fillNegatif = d3.scaleOrdinal(d3.schemeCategory10);
    var layoutNegatif = d3.layout.cloud()
      .size([width, height])
      .words(myWordsNegatif.map(function(d) {
        return {text: d, size: 10 + Math.random() * 90};
    }))
      .padding(5)
      .rotate(function() { return ~~(Math.random() * 2) * 90; })
       .font("Impact")
      .fontSize(function(d) { return d.size; })
      .on("end", drawNegatif);
    layoutNegatif.start();

    function drawNegatif(words) {
      svgNegatif
        .append("g")
            .attr("transform", "translate(" + layoutNegatif.size()[0] / 2 + "," + layoutNegatif.size()[1] / 2 + ")")
            .selectAll("text")
            .data(words)
            .enter().append("text")
            .style("font-size", function(d) { return d.size + "px"; })
            .style("font-family", "Impact")
            .style("fill", function(d, i) { return fillNegatif(i); })
            .attr("text-anchor", "middle")
            .attr("transform", function(d) {
                return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
            })
            .text(function(d) { return d.text; });
    }

        var myWordsNetral = <?php echo $arrAllOkeNetral?>
    // set the dimensions and margins of the graph
    var marginNetral = {top: 10, right: 10, bottom: 10, left: 10},
        width = 450 - marginNetral.left - marginNetral.right,
        height = 450 - marginNetral.top - marginNetral.bottom;

    // append the svg object to the body of the page
    var svgNetral = d3.select("#my_dataviz_netral").append("svg")
        .attr("width", width + marginNetral.left + marginNetral.right)
        .attr("height", height + marginNetral.top + marginNetral.bottom)
        .append("g")
        .attr("transform",
              "translate(" + marginNetral.left + "," + marginNetral.top + ")");

    var fillNetral = d3.scaleOrdinal(d3.schemeCategory10);
    var layoutNetral = d3.layout.cloud()
      .size([width, height])
      .words(myWordsNetral.map(function(d) {
        return {text: d, size: 10 + Math.random() * 90};
    }))
      .padding(5)
      .rotate(function() { return ~~(Math.random() * 2) * 90; })
       .font("Impact")
      .fontSize(function(d) { return d.size; })
      .on("end", drawNetral);
    layoutNetral.start();

    function drawNetral(words) {
      svgNetral
        .append("g")
            .attr("transform", "translate(" + layoutNetral.size()[0] / 2 + "," + layoutNetral.size()[1] / 2 + ")")
            .selectAll("text")
            .data(words)
            .enter().append("text")
            .style("font-size", function(d) { return d.size + "px"; })
            .style("font-family", "Impact")
            .style("fill", function(d, i) { return fillNetral(i); })
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
        <form action="{{url('klasifikasi/visualisasi')}}" method="GET">
            <div class="row mb-6">
                <div class="col-md-4">
                    <div class="form-group mb-2">
                        <label for="merchant_id" class="form-label">Merchant</label>
                        <select class="form-select" data-control="select2" id="merchant_id" name="merchant_id">
                            <option value="0" disabled selected>Select Merchant</option>
                            @foreach ($merchants as $item)
                                <option value="{{$item->id}}" {{ $request->merchant_id == $item->id ? 'selected' : '' }}>{{$item->nama_toko}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-2">
                        <label for="product_id" class="form-label">Product</label>
                        <select class="form-select" data-control="select2" id="product_id" name="product_id">
                            <option value="0" disabled selected>Select Product</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-3 mb-1 mt-8">
                    <div class="row">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-md w-100">
                                <i class="fas fa-filter fs-4 me-2"></i>Filter
                            </button>
                        </div>
                        <div class="col-6">
                            <a href="/klasifikasi/visualisasi" class="btn btn-secondary btn-md w-100">Reset</a>
                        </div>
                    </div>
                </div>
            </div>
        </form> 
        @if($request->product_id != null)
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="row">
                    <div class="col-md-6" align="center">
                        <h2 align="center">Grafik Data Uji</h2>
                        <br>
                        <div id="chartContainer" style="height: 400px; width: 100%;"></div>
                    </div>
                    <div style="padding: 7%" class="col-md-6" align="center">
                        <h2 align="center">Bayak Data</h2>
                        <br>
                        <h3>Positif : <b>{{$positif}}</b></h3>
                        <h3>Negatif : <b>{{$negatif}}</b></h3>
                        <h3>Netral : <b>{{$netral}}</b></h3>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @if($request->product_id != null)
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <h3 align="center">Geser kanan kiri untuk melihat data netral</h3>
        <div class="post d-flex flex-column-fluid table-responsive" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="card p-6">
                    <h2 align="center">Word Cloud Positif</h2>
                    <br>
                    <div id="my_dataviz"></div>
                    <br>
                    <div class="row">
                        @foreach($positifJumlah as $positifJumlahKey => $positifJumlahValue)
                            <div class="col-md-4">
                                {{$positifJumlahKey}} : {{$positifJumlahValue}}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div id="kt_content_container" class="container-xxl">
                <div class="card p-6">
                    <h2 align="center">Word Cloud Negatif</h2>
                    <br>
                    <div id="my_dataviz_negatif"></div>
                    <br>
                    <div class="row">
                        @foreach($negatifJumlah as $negatifJumlahKey => $negatifJumlahValue)
                            <div class="col-md-4">
                                {{$negatifJumlahKey}} : {{$negatifJumlahValue}}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div id="kt_content_container" class="container-xxl">
                <div class="card p-6">
                    <h2 align="center">Word Cloud Netral</h2>
                    <br>
                    <div id="my_dataviz_netral"></div>
                    <br>
                    <div class="row">
                        @foreach($netralJumlah as $netralJumlahKey => $netralJumlahValue)
                            <div class="col-md-4">
                                {{$netralJumlahKey}} : {{$netralJumlahValue}}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection