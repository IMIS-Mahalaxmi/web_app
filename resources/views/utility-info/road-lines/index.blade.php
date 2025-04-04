@extends('layouts.dashboard')
@section('title', $page_title)
@push('style')
<style type="text/css">
    .dataTables_filter {
        display: none;
    }
</style>
@endpush
@section('content')
<div class="card">
    <div class="card-header">
    <a href="{{ action('MapsController@index') }}#add_road_control" class="btn btn-info">Add Road</a>
        @can('Export Roadlines to CSV')
        <a href="{{ action('UtilityInfo\RoadlineController@export') }}" id="export" class="btn btn-info">Export to
            CSV</a>
        @endcan
        @can('Export Roadlines to Shape')
        <a href="#" id="export-shp" class="btn btn-info">Export to Shape File</a>
        @endcan
        @can('Export Roadlines to KML')
        <a href="#" id="export-kml" class="btn btn-info">Export to KML</a>
        @endcan
        <a class="btn btn-info float-right" id="headingOne" type="button" data-toggle="collapse"
            data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Show Filter</a>
    </div><!-- /.box-header -->
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <form class="form-horizontal" id="filter-form">
                                    <div class="form-group row">
                                    <label for="code" class="col-md-2 col-form-label ">Code</label>
                                        <div class="col-md-2" >
                                            <input type="text" class="form-control" id="code" placeholder="Code" oninput="validateAlphanumeric(this)" />
                                        </div>
                                        <label for="code" class="col-md-2 col-form-label ">Hierarchy</label>
                                        <div class="col-md-2" >
                                            <select class="form-control" id="road_hier_select">
                                                <option value="">Hierarchy</option>
                                                <option value="Strategic Urban Road">Strategic Urban Road</option>
                                                <option value="Feeder Road">Feeder Road</option>
                                                <option value="Other Road">Other Road</option>
                                            </select>
                                        </div>
                                         <label for="code" class="col-md-2 col-form-label ">Surface
                                            Type</label>
                                        <div class="col-md-2" >
                                            <select class="form-control" id="surface_type">
                                                <option value="">Surface Type</option>
                                                <option value="Earthen">Earthen</option>
                                                <option value="Gravelled">Gravelled</option>
                                                <option value="Metalled">Metalled</option>
                                                <option value="Brick Paved">Brick Paved</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="name" class="col-md-2 col-form-label ">Road Name</label>
                                        <div class="col-md-2" >
                                            <input type="text" class="form-control" id="name" placeholder="Road Name" />
                                        </div>
                                        <label for="carrying_width" class="col-md-2 col-form-label ">Carrying Width</label>
                                        <div class="col-md-2" >
                                            <input type="text" class="form-control" id="carrying_width" placeholder="Carrying Width" />
                                        </div>
                                    </div>
                                    <div class="card-footer text-right">
                                        <button type="submit" class="btn btn-info ">Filter</button>
                                        <button id="reset-filter" type="reset" class="btn btn-info">Reset</button>
                                    </div>
                                </form>
                            </div>
                            <!--- accordion body!-->
                        </div>
                        <!--- collapseOne!-->
                    </div>
                    <!--- accordion item!-->
                </div>
                <!--- accordion !-->
            </div>
        </div>
        <!--- row !-->
    </div>
    <!--- card body !-->
    <div class="card-body">
        <div style="overflow: auto; width: 100%;">
            <table id="data-table" class="table table-bordered table-striped dtr-inline" width="100%">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Road Name</th>
                        <th>Hierarchy</th>
                        <th>Right of Way (m)</th>
                        <th>Carrying Width (m)</th>
                        <th>Surface Type</th>
                        <th>Road Length (m)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div><!-- /.box-body -->
</div><!-- /.box -->
@stop
@push('scripts')
<script>
    $(function() {
        var dataTable = $('#data-table').DataTable({
            bFilter: false,
            processing: true,
            serverSide: true,
            scrollCollapse: true,
            "bStateSave": true,
            "stateDuration": 1800, // In seconds; keep state for half an hour
            ajax: {
                url: '{!! url("utilityinfo/roadlines/data") !!}',
                data: function(d) {
                    d.code = $('#code').val();
                    d.hierarchy = $('#road_hier_select').val();
                    d.surface_type = $('#surface_type').val();
                    d.name = $('#name').val();
                    d.carrying_width = $('#carrying_width').val();
                }
            },
            columns: [{
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'hierarchy',
                    name: 'hierarchy'
                },
                {
                    data: 'right_of_way',
                    name: 'right_of_way',
                    render: function(data) {
                        if (data !== null) {
                            return parseFloat(data).toFixed(2);
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    data: 'carrying_width',
                    name: 'carrying_width',
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: 'surface_type',
                    name: 'surface_type'
                },
                {
                    data: 'length',
                    name: 'length'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [0, 'desc']
            ]
        }).on('draw', function() {
            $('.delete').on('click', function(e) {
                var form = $(this).closest("form");
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            });
        });
        var code = '',
        hierarchy = '',
        surface_type = '';

        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            dataTable.draw();
            code = $('#code').val();
            hierarchy = $('#road_hier_select').val();
            surface_type = $('#surface_type').val();
            name = $('#name').val();
            carrying_width = $('#carrying_width').val();
        });

        filterDataTable(dataTable);
        resetDataTable(dataTable);
        //  $('#data-table_filter input[type=search]').attr('readonly', 'readonly');
        $("#export").on("click", function(e) {
            e.preventDefault();
            var searchData = $('input[type=search]').val();
            var code = $('#code').val();
            var hierarchy = $('#road_hier_select').val();
            var surface_type = $('#surface_type').val();
            var carrying_width = $('#carrying_width').val();
            window.location.href = "{!! url('utilityinfo/roadlines/export?searchData=') !!}" + searchData +
                "&code=" + code + "&hierarchy=" + hierarchy + "&surface_type=" + surface_type + "&name=" + name + "&carrying_width=" + carrying_width ;
        })
        $("#export-shp").on("click", function(e) {
            e.preventDefault();
            var cql_param = getCQLParams();
            window.location.href = "{{ Config::get("constants.GEOSERVER_URL") }}wfs?service=WFS&version=1.0.0&request=GetFeature&authkey={{ Config::get("constants.AUTH_KEY") }}&typeName={{ Config::get("constants.GEOSERVER_WORKSPACE") }}:roadlines_layer+&CQL_FILTER=" + cql_param + " &outputFormat=SHAPE-ZIP&format_options=filename:Road Network.zip";
        })
        $("#export-kml").on("click", function(e) {
            e.preventDefault();
            var cql_param = getCQLParams();

            window.location.href = "{{ Config::get("constants.GEOSERVER_URL") }}wfs?service=WFS&version=1.0.0&request=GetFeature&authkey={{ Config::get("constants.AUTH_KEY") }}&typeName={{ Config::get("constants.GEOSERVER_WORKSPACE") }}:roadlines_layer+&CQL_FILTER=" + cql_param +" &outputFormat=KML&format_options=filename:Road Network.kml";
        });
        function getCQLParams() {
            code = $('#code').val();
            hierarchy = $('#road_hier_select').val();
            surface_type = $('#surface_type').val();
            carrying_width = $('#carrying_width').val();
            var cql_param = "1=1 AND deleted_at IS NULL";
            if (code) {
               cql_param += " AND code ILIKE '%" + code + "%'";
            }
            if (hierarchy) {
                cql_param += " AND hierarchy ='" + hierarchy + "'";
            }
            if (surface_type) {
                cql_param += " AND surface_type ='" + surface_type + "'";
            }
            if (name) {
               cql_param += " AND name ILIKE '%" + name + "%'";
            }
             if (carrying_width) {
                cql_param += " AND carrying_width ='" + carrying_width + "'";
            }
            return encodeURI(cql_param);
        }
    });
</script>
@endpush