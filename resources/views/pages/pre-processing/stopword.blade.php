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
                        <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                            <!--begin::Add customer-->
                            <h1>Total Data Set : <b>{{count($reviews)}}</b></h1>
                            <!--end::Add customer-->
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <form action="{{url('pre_processing/stopword')}}" method="GET">
                        <div class="row">
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
                                        <button type="submit" class="btn btn-primary btn-md w-100"
                                        onclick="return confirm('Perhatian: Proses yang akan dilakukan membutuhkan waktu cukup lama, jadi ketika sudah diklik mohon tunggu sampai data tereksekusi!')">
                                            <i class="fas fa-filter fs-4 me-2"></i>Filter
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <a href="/pre_processing/stopword" class="btn btn-secondary btn-md w-100">Reset</a>
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
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @foreach ($reviews as $key => $item)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$item->konten}}</td>
                                        <td>{{$item->stopword}}</td>
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