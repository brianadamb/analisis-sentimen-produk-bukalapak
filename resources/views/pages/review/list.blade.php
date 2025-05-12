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

                    var opt_head = document.createElement('option');
                    opt_head.text = 'Semua';
                    opt_head.value = 'all';
                    // opt_head.disabled = true;
                    // opt_head.selected = true;
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

        function disableSubmit()
        {
            if($('#merchant_id').val() == null)
            {
                alert('Pilih merchant dulu');
            }else
            {
                if($('#list-product').val() == null)
                {
                    alert('Pilih product dulu');
                }else
                {
                    $('#loader-review').show();
                    $('#submit-review').prop('disabled',true);
                    $('#submit-review-get').submit();
                }
            }
            
            
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
                    <form action="{{url('review')}}" id="submit-review-get">
                    <div class="row">
                        <div class="col-sm-4">
                           <div class="d-flex flex-column mb-8 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                              <span class="required">Merchant</span>
                              <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                title="Specify a target name for future usage and reference"></i>
                            </label>
                            <select class="form-select" name="merchant_id" id="merchant_id" data-control="select2" data-placeholder="Select Merchant" onchange="getProduct(this.value)">
                              <option disabled selected value="">Select Merchant</option>
                              <option value="all" {{$request->merchant_id == 'all'  ? 'selected' : ''}}>
                                 Semua
                              </option>
                              @foreach($merchant as $key => $item)
                              <option value="{{$item->id}}" 
                                      {{$item->id == $request->merchant_id ? 'selected' : ''}}>
                                {{$item->nama_toko}}
                              </option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="d-flex flex-column mb-8 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                              <span class="required">Product</span>
                              <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                title="Specify a target name for future usage and reference"></i>
                            </label>
                            <select id="list-product" class="form-select" name="product_id" data-control="select2" 
                                    data-placeholder="Select Product">
                            <option disabled selected value="">Select Product</option>
                            <option value="all" {{$request->product_id == 'all'  ? 'selected' : ''}}>
                                Semua
                            </option>
                            @foreach($product as $pKey => $pItem)
                                <option value="{{$pItem->id}}" 
                                        {{$pItem->id == $request->product_id  ? 'selected' : ''}}>
                                    {{$pItem->nama_produk}} 
                                </option>
                            @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-2 pt-8" style="display: none" id="loader-review">
                           
                            <img src="{{url('/')}}/loader.gif" style="width: 25%">
                        
                        </div>
                        <div class="col-sm-2 pt-8">
                            <button type="button" onclick="disableSubmit();" id="submit-review" class="btn btn-primary" >
                                Get Data
                                <i class="fa fa-arrow-right"></i>
                            </button>
                             
                        </div>
                        <div class="col-sm-2 pt-8">
                            <a href="{{url('review-export')}}" target="_blank" class="btn btn-warning" >
                                Download Data
                                
                            </a>
                        </div>
                    </div>
                    </form>
                    <!--begin::Datatable-->
                    <table id="kt_datatable_example_1" class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th>No</th>
                                <th>Komen Name</th>
                                <th>Title</th>
                                <th>Konten</th>
                                <th>Rate</th>
                                <th>Created Date</th>
                                <th>Updated Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @foreach ($review as $key => $item)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$item->komen_name}}</td>
                                    <td>{{$item->title}}</td>
                                    <td>{{$item->konten}}</td>
                                    <td>
                                        @for($i = 0; $i < $item->rate; $i++)
                                            <i style="color: yellow;" class="fa fa-star"></i>
                                        @endfor
                                    </td>
                                    <td>{{$item->created_at}}</td>
                                    <td>{{$item->updated_at}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!--end::Datatable-->
                </div>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
@endsection
