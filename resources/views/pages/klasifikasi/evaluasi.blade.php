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
@endsection
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="card p-12">
                    <form action="{{url('klasifikasi/evaluasi')}}" method="GET">
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
                                        <a href="/klasifikasi/evaluasi" class="btn btn-secondary btn-md w-100">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <h2 align="center">Confusion Matrix</h2>
                    <br>
                    <div class="table-responsive">
                        <table border="3" class="table align-middle table-row-dashed fs-6 gy-5">
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
            </div>
        </div>
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="card p-12">
                    <h2 align="center">Metrik Evaluasi</h2>
                    <br>
                    <div class="table-responsive">
                        <table border="3" class="table align-middle table-row-dashed fs-6 gy-5">
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
                                        <td>{{round($metric['precision'], 2) * 100}}%</td>
                                        <td>{{round($metric['recall'], 2) * 100}}%</td>
                                       <!--  <td>{{round($metric['f1_score'], 2)}}</td> -->
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="text-align: center; border: 1px solid black">
                                    <td colspan="3">
                                        <b>Akurasi</b> : {{round($accuracy, 2) * 100}}%
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection