<div class="modal" id="editModal{{$item->id}}" tabindex="-1" aria-labelledby="editModal{{$item->id}}"
  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Data {{$item->nama_produk}}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{url('product/update/'.$item->id)}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="d-flex flex-column mb-8 fv-row">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
              <span class="required">Nama Product</span>
              <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                title="Specify a target name for future usage and reference"></i>
            </label>
            <input type="text" required class="form-control form-control-solid" name="nama_produk"
              placeholder="Masukkan Nama Merchant" value="{{$item->nama_produk}}" />
          </div>
          <div class="d-flex flex-column mb-8 fv-row">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
              <span class="required">Link Product</span>
              <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                title="Specify a target name for future usage and reference"></i>
            </label>
            <textarea required class="form-control form-control-solid" name="link" placeholder="Masukkan Link Product"
              name="link">{{$item->link}}</textarea>
          </div>
          <div class="d-flex flex-column mb-8 fv-row">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
              <span class="required">Nama Merchant</span>
              <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                title="Specify a target name for future usage and reference"></i>
            </label>
            <select class="form-select" data-control="select2" name="merchant_id" data-placeholder="Select Merchant"
              data-dropdown-parent="#editModal{{$item->id}}">
              {{-- <option value="{{$item->merchant}}">{{$item->merchant->nama_toko}}</option> --}}
              @foreach($merchant as $key => $item1) 
              <option value="{{$item1->id}}" {{$item->merchant_id == $item1->id ? 'selected':''}}>
                {{$item1->nama_toko}}  
              </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update Data</button>
        </div>
      </form>
    </div>
  </div>
</div>