$(document).ready(function() {
    var table = $('#example').DataTable({
        "ajax": "fetch_data.php",
        "columns": [{
                "data": "employeeid"
            },
            {
                "data": "employeename",
                "className": "text-truncate"
            },
            {
                "data": "positionname",
                "className": "text-truncate"
            },
            {
                "data": "sexen",
                "className": "text-truncate"
            },
            {
                "data": "dob"
            },
            {
                "data": "address",
                "className": "text-truncate"
            },
            {
                "data": "phonenumber",
                "className": "text-truncate"
            },
            {
                "data": null,
                "className": "center",
                "defaultContent": '<div class="dropdown">' +
                    '<button class="btn btn-primary dropdown-toggle actionIcon" type="button" id="actionDropdown" data-bs-toggle="dropdown" aria-expanded="false">' +
                    'Action' +
                    '</button>' +
                    '<ul class="dropdown-menu" aria-labelledby="actionDropdown">' +
                    '<li><button type="button" class="dropdown-item editBtn text-warning"><i class="fas fa-edit"></i> Edit</button></li>' +
                    '<li><button type="button" class="dropdown-item viewBtn text-secondary"><i class="fas fa-eye"></i> View</button></li>' +
                    '<li><button type="button" class="dropdown-item deleteBtn text-danger"><i class="fas fa-trash-alt"></i> Delete</button></li>' +
                    '</ul>' +
                    '</div>'
            }
        ],
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "pageLength": 10,
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        "language": {
            "search": "Filter rows:",
            "lengthMenu": "Number of rows: _MENU_",
            // You can customize other language options here if needed
        },
        "columnDefs": [{
            "targets": -1,
            "orderable": false
        }]
    });

    $.ajax({
        url: 'fetch_options.php',
        type: 'GET',
        success: function(data) {
            var options = JSON.parse(data);
            $.each(options.positions, function(index, value) {
                $('#addPosition, #editPosition').append('<option value="' + value
                    .positionid + '">' + value.positionname + '</option>');
            });
            $.each(options.genders, function(index, value) {
                $('#addGender, #editGender').append('<option value="' + value
                    .sexid +
                    '">' + value.sexen + '</option>');
            });
            $.each(options.statuses, function(index, value) {
                $('#addStatus, #editStatus').append('<option value="' + value
                    .statusid +
                    '">' + value.statusname + '</option>');
            });
        }
    });

    function loadOptions() {
        return $.ajax({
            url: 'fetch_options.php', // Adjust this path as necessary
            type: 'GET',
            dataType: 'json'
        });
    }

    // Event listener for edit button click
    $('#example tbody').on('click', 'button.editBtn', function() {
        var data = table.row($(this).parents('tr')).data();
        var employeeId = data.employeeid;

        // Fetch employee details based on employeeId
        $.ajax({
            url: 'get_employee_details.php', // Adjust this path as necessary
            type: 'GET',
            data: {
                employeeid: employeeId
            },
            dataType: 'json',
            success: function(employeeData) {
                // Set the hidden input field for employee ID
                $('#editId').val(employeeData.employeeid);

                // Set the values for other fields
                $('#editName').val(employeeData.employeename);
                $('#editDob').val(employeeData.dob);
                $('#editAddress').val(employeeData.address);
                $('#editPhoneNumber').val(employeeData.phonenumber);
                $('#editEmail').val(employeeData.email);
                $('#editTelegram').val(employeeData.telegram);

                // Load options and then set the selected values
                loadOptions().done(function(options) {
                    // Clear existing options
                    $('#editPosition').empty();
                    $('#editGender').empty();
                    $('#editStatus').empty();

                    // Populate position options
                    $.each(options.positions, function(index, value) {
                        $('#editPosition').append(
                            '<option value="' +
                            value.positionid + '">' + value
                            .positionname + '</option>');
                    });

                    // Populate gender options
                    $.each(options.genders, function(index, value) {
                        $('#editGender').append('<option value="' +
                            value.sexid + '">' + value.sexen +
                            '</option>');
                    });

                    // Populate status options
                    $.each(options.statuses, function(index, value) {
                        $('#editStatus').append('<option value="' +
                            value.statusid + '">' + value
                            .statusname + '</option>');
                    });

                    // Check if the desired value exists in the options and set it as selected
                    $('#editPosition').val(employeeData.positionid);
                    $('#editGender').val(employeeData.sexid);
                    $('#editStatus').val(employeeData.statusid);

                    // Show the modal
                    $('#editModal').modal('show');
                });
            }
        });
    });

    // Add new record
    $('#addForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'add_record.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                $('#addModal').modal('hide');
                table.ajax.reload();
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
            }
        });
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'edit_record.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                $('#editModal').modal('hide');
                table.ajax.reload();
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
            }
        });
    });

    // Delete record
    $('#example tbody').on('click', 'button.deleteBtn', function() {
        var data = table.row($(this).parents('tr')).data();
        if (confirm("Are you sure you want to delete this record?")) {
            $.ajax({
                url: 'delete_record.php',
                type: 'POST',
                data: {
                    employeeid: data.employeeid
                },
                success: function(data) {
                    table.ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.log("Error: " + error);
                }
            });
        }
    });

    // View record
    $('#example tbody').on('click', 'button.viewBtn', function() {
        var data = table.row($(this).parents('tr')).data();

        // Construct the complete image URL
        var imageUrl = '../../public/img/' + data
            .photo;
        $('#employeePhoto').attr('src', imageUrl);

        // Set other form fields
        $('#employeeId').val(data.employeeid);
        $('#employeeName').val(data.employeename);
        $('#employeePosition').val(data.positionname);
        $('#employeeGender').val(data.sexen);
        $('#employeeDob').val(data.dob);
        $('#employeeAddress').val(data.address);
        $('#employeePhoneNumber').val(data.phonenumber);
        $('#employeeEmail').val(data.email);
        $('#employeeTelegram').val(data.telegram);
        $('#employeeCreated').val(data.created);
        $('#employeeStatus').val(data.statusname);

        // Show the modal
        var employeeModal = new bootstrap.Modal(document.getElementById('employeeModal'));
        employeeModal.show();
    });

});


// import and export
function exportData() {
    $.ajax({
        url: 'export_data.php', // Adjust this path as necessary
        type: 'GET',
        success: function(data) {
            var csvContent = "data:text/csv;charset=utf-8," + encodeURIComponent(data);
            var link = document.createElement("a");
            link.setAttribute("href", csvContent);
            link.setAttribute("download", "employee_data.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            alert("Export Data Successfully");
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
            alert("Error: " + error);
        }
    });
}


function importData(file) {
    if (!file) {
        alert("Please select a file to upload.");
        return;
    }

    var formData = new FormData();
    formData.append('file', file);

    $.ajax({
        url: 'import_data.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (typeof response === 'string') {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    alert("Error parsing response: " + e.message);
                    return;
                }
            }

            if (response.success) {
                alert("Data imported successfully");
            } else {
                alert(response.error);
            }
        },
        error: function(xhr, status, error) {
            alert(xhr.responseText);
        }
    });
}