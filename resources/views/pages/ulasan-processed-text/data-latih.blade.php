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
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-xxl">
                <div class="card p-6">
                    <div class="d-flex flex-stack mb-5">
                        <!--begin::Toolbar-->
                        <h1>Total Data Set : <b>{{count($data)}}</b></h1>
                        <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                            <!--begin::Add customer-->
                           <!--  <a class="btn btn-primary" 
                            href="{{url('/ulasan_processed_text/labeling_data_latih')}}?reset=true" 
                            onclick="return confirm('Perhatian : Proses yang akan dilakukan membutuhkan waktu cukup lama, jadi ketika sudah di klik mohon tunggu sampai data terseksekusi!')">
                            Lakukan Pelabelan Ulang
                            </a> -->
                            <!--end::Add customer-->
                        </div>
                        <!--end::Toolbar-->
                    </div>                  
                    <form action="{{url('ulasan_processed_text/labeling_data_latih')}}" method="GET">
                        <input type="text" name="reset" value="true" hidden>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label for="merchant_id" class="form-label">Merchant</label>
                                    <select class="form-select" data-control="select2" id="merchant_id" required name="merchant_id">
                                        <option value="" disabled selected>Select Merchant</option>
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
                                        <option value="" required disabled selected>Select Product</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label for="latih" class="form-label">Data Latih (%)</label>
                                            <input required type="number" min="1" max="100" class="form-control" id="latih" name="latih" value="{{ $request->latih }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label for="uji" class="form-label">Data Uji (%)</label>
                                            <input required type="number" min="1" max="100" class="form-control" id="uji" name="uji" value="{{ $request->uji }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="d-flex justify-content-end">
                                    <div class="row w-50">
                                        <div class="col-md-6 col-lg-3 ms-auto">
                                            <button onclick="return confirm('Perhatian : Proses yang akan dilakukan membutuhkan waktu cukup lama, jadi ketika sudah di klik mohon tunggu sampai data terseksekusi!')" type="submit" class="btn btn-primary btn-md w-100">
                                                <i class="fas fa-filter fs-4 me-2"></i>Filter
                                            </button>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <a href="{{url('ulasan_processed_text/labeling_data_latih')}}" class="btn btn-secondary btn-md w-100">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>                                     
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
                                @foreach ($data as $key => $item)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$item->konten}}</td>
                                        <td>
                                            <ul>
                                                @foreach(json_decode($item->label_arr,true) as $it)
                                                    <li>
                                                        {{$it}}
                                                    </li>
                                                @endforeach
                                            </ul>
                                            {{$item->label}}<br>
                                            <b>Bobot : </b> {{$item->bobot_label}}
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