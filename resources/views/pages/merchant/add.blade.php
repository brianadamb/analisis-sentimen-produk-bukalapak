<div class="modal" id="addModal" tabindex="-1" aria-labelledby="addModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Data Merchant</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{url('merchant/store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="d-flex flex-column mb-8 fv-row">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
              <span class="required">Nama Merchant</span>
              <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                title="Specify a target name for future usage and reference"></i>
            </label>
            <input type="text" required class="form-control form-control-solid" name="nama_toko"
              placeholder="Masukkan Nama Merchant" />
          </div>
          <div class="d-flex flex-column mb-8 fv-row">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
              <span class="required">Deskripsi Merchant</span>
              <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                title="Specify a target name for future usage and reference"></i>
            </label>
            <textarea required class="form-control form-control-solid" placeholder="Masukkan Deskripsi Merchant"
              name="user_toko"></textarea>
          </div>
          <div class="d-flex flex-column mb-8 fv-row">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
              <span class="required">Link Merchant</span>
              <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                title="Specify a target name for future usage and reference"></i>
            </label>
            <input type="text" required class="form-control form-control-solid" name="link" placeholder="Link Merchant"
              value="" />
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