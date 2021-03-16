
<div class="card card-default">
    <div class="card-header">
        <div class="card-title">
            <h5><strong>{{$header}}</strong></h5>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-condensed table-responsive-block table-responsive" id="{{$id}}">
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{$header}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr></tr>
            </tbody>
        </table>
    </div>
</div>
@section('page-js')
    <script>
        $(document).ready(function (e) {
            var table = $('#vendorsTable');
            table.DataTable({
                "searching": false,
                "lengthMenu": [ 5, 10, 25, 50, 75, 100 ],
                "order": [[ 0, "desc" ]],
                "pageLength": 5,
                "ajax": {
                    url: "/admin/vendors/recent",
                    dataSrc: ""
                },
                "columns": [
                    {data: 'id', name: 'id'},
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'address'},
                    {data: 'store_no'},
                    {data: 'tax_no'}
                ]
            });
        });
    </script>
@endsection