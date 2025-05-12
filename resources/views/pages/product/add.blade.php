<div class="modal" id="addModal" tabindex="-1" aria-labelledby="addModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Data Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{url('product/store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="d-flex flex-column mb-8 fv-row">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
              <span class="required">Nama Product</span>
              <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                title="Specify a target name for future usage and reference"></i>
            </label>
            <input type="text" required class="form-control form-control-solid" name="nama_produk"
              placeholder="Masukkan Nama Produk" />
          </div>
          <div class="d-flex flex-column mb-8 fv-row">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
              <span class="required">Link Product</span>
              <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                title="Specify a target name for future usage and reference"></i>
            </label>
            <input type="text" required class="form-control form-control-solid" name="link" placeholder="Link Product"
              value="" />
          </div>
          <div class="d-flex flex-column mb-8 fv-row">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
              <span class="required">Nama Merchant</span>
              <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                title="Specify a target name for future usage and reference"></i>
            </label>
            <select class="form-select" name="merchant_id" data-control="select2" data-placeholder="Select Merchant"
              data-dropdown-parent="#addModal">
              <option value=" ">Select Merchant</option>
              @foreach($merchant as $key => $item)
              <option value="{{$item->id}}">{{$item->nama_toko}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Add Data</button>
        </div>
      </form>
    </div>
  </div>
</div>