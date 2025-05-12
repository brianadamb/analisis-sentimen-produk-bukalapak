"use strict";

// Class definition
var KTDatatablesServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;

    // Private functions
    var initDatatable = function () {
        let token = sessionStorage.getItem('token');
	    // $.ajaxSetup({
        //      beforeSend: function(xhr) {
        //         xhr.setRequestHeader('Authorization', 'Bearer ' + token);
        //      }
	    // });
        dt = $("#kt_datatable_example_2").DataTable({
            searchDelay: 500,
            serverSide: true,
            pagingType: 'full_numbers',
            order: [[5, 'desc']],
            stateSave: true,
            ajax: {
                url: "http://localhost:8000/api/merchant",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                },
            },
            columns: [
                { data: 'id' },
                { data: 'nama_toko' },
                { data: 'user_toko' },
                { data: 'link' },
                { data: 'created_at' },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                                Actions
                                <span class="svg-icon svg-icon-5 m-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                            <path d="M6.70710678,15.7071068 C6.31658249,16.0976311 5.68341751,16.0976311 5.29289322,15.7071068 C4.90236893,15.3165825 4.90236893,14.6834175 5.29289322,14.2928932 L11.2928932,8.29289322 C11.6714722,7.91431428 12.2810586,7.90106866 12.6757246,8.26284586 L18.6757246,13.7628459 C19.0828436,14.1360383 19.1103465,14.7686056 18.7371541,15.1757246 C18.3639617,15.5828436 17.7313944,15.6103465 17.3242754,15.2371541 L12.0300757,10.3841378 L6.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000003, 11.999999) rotate(-180.000000) translate(-12.000003, -11.999999)"></path>
                                        </g>
                                    </svg>
                                </span>
                            </a>
                            <!--begin::Menu-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a class="menu-link px-3" onclick="editRows(`+data.id+`)" data-bs-toggle="modal" data-bs-target="#kt_modal_new_target">
                                        Edit
                                    </a>
                                </div>
                                <!--end::Menu item-->
                                
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3" data-kt-docs-table-filter="delete_row">
                                        Delete
                                    </a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu-->
                        `;
                    },
                },
            ],
        });

        table = dt.$;

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dt.on('draw', function () {
            // initToggleToolbar();
            // toggleToolbars();
            handleDeleteRows();
            // handleEditRows();
            KTMenu.createInstances();
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            dt.search(e.target.value).draw();
        });
    }

    var handleEditRows = () => {
        const editButtons = document.querySelectorAll('[data-kt-docs-table-filter="edit_row"]');

        editButtons.forEach(d => {
            // Delete button on click
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Select parent row
                const parent = e.target.closest('tr');

                const id = parent.querySelectorAll('td')[0].innerText;
                console.log(id);
            })
        });
    }

   

    // Delete customer
    var handleDeleteRows = () => {
        // Select all delete buttons
        const deleteButtons = document.querySelectorAll('[data-kt-docs-table-filter="delete_row"]');

        deleteButtons.forEach(d => {
            // Delete button on click
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Select parent row
                const parent = e.target.closest('tr');

                // Get customer name
                const customerName = parent.querySelectorAll('td')[1].innerText;

                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                Swal.fire({
                    text: "Are you sure you want to delete " + customerName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        // Simulate delete request -- for demo purpose only
                        Swal.fire({
                            text: "Deleting " + customerName,
                            icon: "info",
                            buttonsStyling: false,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(function () {
                            Swal.fire({
                                text: "You have deleted " + customerName + "!.",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function () {
                                // delete row data from server and re-draw datatable
                                dt.draw();
                            });
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: customerName + " was not deleted.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            })
        });
    }

    // Reset Filter
    var handleResetForm = () => {
        // Select reset button
        const resetButton = document.querySelector('[data-kt-docs-table-filter="reset"]');

        // Reset datatable
        resetButton.addEventListener('click', function () {
            // Reset payment type
            filterPayment[0].checked = true;

            // Reset datatable --- official docs reference: https://datatables.net/reference/api/search()
            dt.search('').draw();
        });
    }

    // Init toggle toolbar
    // var initToggleToolbar = function () {
    //     // Toggle selected action toolbar
    //     // Select all checkboxes
    //     const container = document.querySelector('#kt_datatable_example_2');

    //     // Select elements
    //     const deleteSelected = document.querySelector('[data-kt-docs-table-select="delete_selected"]');

    //     // Deleted selected rows
    //     deleteSelected.addEventListener('click', function () {
    //         // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
    //         Swal.fire({
    //             text: "Are you sure you want to delete selected customers?",
    //             icon: "warning",
    //             showCancelButton: true,
    //             buttonsStyling: false,
    //             showLoaderOnConfirm: true,
    //             confirmButtonText: "Yes, delete!",
    //             cancelButtonText: "No, cancel",
    //             customClass: {
    //                 confirmButton: "btn fw-bold btn-danger",
    //                 cancelButton: "btn fw-bold btn-active-light-primary"
    //             },
    //         }).then(function (result) {
    //             if (result.value) {
    //                 // Simulate delete request -- for demo purpose only
    //                 Swal.fire({
    //                     text: "Deleting selected customers",
    //                     icon: "info",
    //                     buttonsStyling: false,
    //                     showConfirmButton: false,
    //                     timer: 2000
    //                 }).then(function () {
    //                     Swal.fire({
    //                         text: "You have deleted all selected customers!.",
    //                         icon: "success",
    //                         buttonsStyling: false,
    //                         confirmButtonText: "Ok, got it!",
    //                         customClass: {
    //                             confirmButton: "btn fw-bold btn-primary",
    //                         }
    //                     }).then(function () {
    //                         // delete row data from server and re-draw datatable
    //                         dt.draw();
    //                     });
    //                 });
    //             } else if (result.dismiss === 'cancel') {
    //                 Swal.fire({
    //                     text: "Selected customers was not deleted.",
    //                     icon: "error",
    //                     buttonsStyling: false,
    //                     confirmButtonText: "Ok, got it!",
    //                     customClass: {
    //                         confirmButton: "btn fw-bold btn-primary",
    //                     }
    //                 });
    //             }
    //         });
    //     });
    // }

    // Toggle toolbars
    // var toggleToolbars = function () {
    //     // Define variables
    //     const container = document.querySelector('#kt_datatable_example_2');
    //     const toolbarBase = document.querySelector('[data-kt-docs-table-toolbar="base"]');
    //     const toolbarSelected = document.querySelector('[data-kt-docs-table-toolbar="selected"]');
    //     const selectedCount = document.querySelector('[data-kt-docs-table-select="selected_count"]');

    //     // Detect checkboxes state & count
    //     let checkedState = false;
    //     let count = 0;

    //     // Toggle toolbars
    //     if (checkedState) {
    //         selectedCount.innerHTML = count;
    //         toolbarBase.classList.add('d-none');
    //         toolbarSelected.classList.remove('d-none');
    //     } else {
    //         toolbarBase.classList.remove('d-none');
    //         toolbarSelected.classList.add('d-none');
    //     }
    // }

    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
            // initToggleToolbar();
            // handleFilterDatatable();
            handleEditRows();
            handleDeleteRows();
            //handleResetForm();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});

function editRows(id)
{
    //console.log(id);
    let token = sessionStorage.getItem('token');
	$.ajaxSetup({
    beforeSend: function(xhr) {
        xhr.setRequestHeader('Authorization', 'Bearer ' + token);
    }
	});
    $.ajax({
		url: "http://localhost:8000/api/merchant/show/"+id,
        context: document.body,
		type: 'GET',
		dataType: 'json',
        success: function (response){
            console.log(response.data.nama_toko);
			$('#nama_toko').val(response.data.nama_toko);
            $('#user_toko').val(response.data.user_toko);
            $('#link').val(response.data.nama_toko);
            $('#id_toko').val(response.data.id);
        }
    });
}