<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Ajax</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center">Laravel Ajax Crud Operations</h1>
        <hr>
        <div class="row mt-5">
            <div class="col-6 offset-3">
                <form id="myForm">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label for="">Select State</label>
                        <select name="state_id" class="form-control">
                            @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">City Name</label>
                        <input type="text" name="city_name" class="form-control">
                    </div>
                    <button id="submit" class="btn btn-success">Add City</button>
                </form>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col">
                <table id="cities" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>State</th>
                            <th>Status</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update City</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateForm">
                    {{csrf_field()}}
                    <!-- hidden id input field -->
                    <input type="hidden" name="id">

                    <label for="">Select State</label>
                    <select name="edit_state_id" class="form-control">
                        @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                        @endforeach
                    </select>
                    <div class="form-group">
                        <label for="">City Name</label>
                        <input name="edit_city_name" type="text" class="form-control">
                    </div>
                    <label for="">Select Status</label>
                    <select name="edit_status" class="form-control">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" id="update" class="btn btn-primary">Update City</button>
            </div>
            </div>
        </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {

            //Data Insert Code
            $('#submit').click(function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: "{{ url('addCity') }}",
                    type: "post",
                    dataType: "json",
                    data: $('#myForm').serialize(),
                    success: function(response) {
                        $('#myForm')[0].reset();
                        console.log(response);
                        table.ajax.reload();
                    }
                });
            });
            // Data Display Code
            var table = $('#cities').DataTable( {
                ajax: "{{ url('getCities') }}",
                columns: [
                    { "data": "city_name" },
                    { "data": "state_name" },
                    { 
                        "data": null,
                        render: function(data, type, row) {
                            if(row.status == "Active") {
                                return `<button class="btn btn-sm btn-success">Active</button>`;
                            } else {
                                return `<button class="btn btn-sm btn-warning">Inactive</button>`;
                            }
                        }
                    },
                    { 
                        "data": null,
                        render: function(data, type, row) {
                            return `<button data-id="${row.id}" class="btn btn-info" data-toggle="modal" data-target="#exampleModal" id="edit"><i class="fa fa-edit"></i></button>`;
                        }
                    },
                    { 
                        "data": null,
                        render: function(data, type, row) {
                            return `<button data-id="${row.id}" class="btn btn-danger" id="delete"><i class="fa fa-trash"></i></button>`;
                        }
                    }
                ]
            } );

            // edit city code goes here
            $(document).on('click', '#edit', function() {
                $.ajax({
                    url: "{{ url('getCityById') }}",
                    type: "post",
                    dataType: 'json',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": $(this).data('id')
                    },
                    success: function(response) {
                        $('input[name="id"]').val(response.data.id);
                        $('select[name="edit_state_id"]').val(response.data.state_id);
                        $('input[name="edit_city_name"]').val(response.data.city_name);
                        $('select[name="edit_status"]').val(response.data.status);
                    }
                })
            })

            // Update city code goes here
            $(document).on('click', '#update', function() {
                if(confirm('Are you sure you want to update??')) {
                    $.ajax({
                        url: '{{ url("updateCity") }}',
                        type: 'post',
                        dataType: 'json',
                        data: $('#updateForm').serialize(),
                        success: function(response) {
                            $('#updateForm')[0].reset();
                            table.ajax.reload();
                            $('#exampleModal').modal('hide')
                        }
                    })
                }
            })

            // delete city code goes here
            $(document).on('click', '#delete', function() {
                if(confirm('Are you sure you want delete??')){
                    $.ajax({
                        url: "{{ url('deleteCityById') }}",
                        type: "post",
                        dataType: 'json',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": $(this).data('id')
                        },
                        success: function(response) {
                            table.ajax.reload();
                        }
                    })
                }
            })
        });
    </script>
</body>
</html>