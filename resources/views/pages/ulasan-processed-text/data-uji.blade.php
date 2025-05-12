@extends('layouts.app')
@section('pages-style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
@endsection
@section('pages-script')
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        let table = new DataTable('#kt_datatable_example_1');
        function getProduct(id) 
        {
           $.ajax({
                url: "{{url('review-get-product')}}"+"/"+id,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    var temp = [];
                    $.each(data, function (key, value) {
                        temp.push({
                            v: value,
                            k: key
                        });
                    });

                    var x = document.getElementById("list-product");
                    $('#list-product').empty();
                    var opt_head = document.createElement('option');
                    opt_head.text = 'Select Product';
                    opt_head.value = '0';
                    opt_head.disabled = true;
                    opt_head.selected = true;
                    x.appendChild(opt_head);
                    //console.log(temp[1]);
                    for (var i = 0; i < temp[1].v.length; i++) {
                        var opt = document.createElement('option');
                        opt.value = temp[1].v[i].id;
                        opt.text = temp[1].v[i].nama_produk;
                        x.appendChild(opt);
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }
    </script> 
@endsection
@section('content')
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
                                   <!--  <th>Sentimen System</th> -->
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @foreach ($data as $key => $item)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$item->konten}}</td>
                                        <!-- <td>
                                            {{$item->label}}
                                        </td> -->
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