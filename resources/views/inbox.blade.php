<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ url('vendors/bootstrap/bootstrap.min.css') }}">
    <title>PESAN</title>
    <style>
        body{
            padding: 10rem 0;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="card">
            <div class="card-header">Manajement Pesan</div>
            <div class="card-body">
                <div id="inboxForm">
                    <form id="inboxFormData" class="mb-5" onsubmit="return submitForm('#inboxFormData')">
                        <div class="card">
                            <div class="card-header">Form Data</div>
                            <div class="card-body">
                                <div class="row row-cols-3">
                                    @foreach($config['table'] as $idx => $row)
                                    @if($row['form'] == true)
                                    <div class="col-6 mb-5">
                                        <label for="{{ $row['field'] }}" class="form-label">{{ $row['label'] }}</label>
                                        <input class="form-control" type="{{ $row['data_type'] }}" id="{{ $row['field'] }}" name="{{ $row['field'] }}" required>
                                    </div>
                                    @endif
                                    @endforeach
                                    <div class="col-12 mb-5">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" name="message" id="message" cols="30" rows="10" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <input type="hidden" id="old_id" name="old_id" required>
                                <button class="btn btn-outline-danger btn-sm" type="reset" onclick="$('#inboxFormData').hide()">Close</button>
                                <button class="btn btn-outline-success btn-sm" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="inboxList">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="text-left">List Data</h2>
                            <div class="text-right">
                                <button onclick="inboxAdd()" class="btn btn-outline-primary btn-sm">Add Pesan</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="inboxFormFilter" onsubmit="return getListData('#inboxList', true)">
                                <div class="container">
                                    <div class="row row-cols-3">
                                        @foreach($config['table'] as $idx => $row)
                                        @if($row['search'] == true)
                                        <div class="col-6 mb-5">
                                            <label for="filter_{{ $row['field'] }}" class="form-label">Filter {{ $row['label'] }}</label>
                                            <input class="form-control" type="{{ $row['data_type'] }}" id="filter_{{ $row['field'] }}" name="filter_{{ $row['field'] }}" onchange="$('#inboxFormFilter').submit()">
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                            </form>
                            <label id="inboxListPanel">
                                Show <select name="show" id="show" onchange="return getListData('#inboxList', true)"><option value="10">10</option><option value="25">25</option><option value="50">50</option></select> entries || Order By 
                                <select name="orderBy" id="orderBy" onchange="return getListData('#inboxList', true)">@foreach($config['table'] as $idx => $row) @if($row['order'] == true)<option value="{{ $row['field'] }}">{{ $row['label'] }}</option>@endif @endforeach</select> : 
                                <select name="orderByValue" id="orderByValue" onchange="return getListData('#inboxList', true)"><option value="DESC">DESC</option><option value="ASC">ASC</option></select> || Halaman : 
                                <input type="number" min="1" max="1" id="page" name="page" value="1"  onchange="return getListData('#inboxList', false)"> dari <strong></strong>
                            </label>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        @foreach($config['table'] as $idx => $row)
                                        <th>{{ $row['label'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/javascript" src="{{ url('vendors/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendors/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript">
        var tempConfig = '{{ base64_encode(json_encode($config)) }}'
        tempConfig = JSON.parse(atob(tempConfig))
        const configPage = tempConfig

        httpRequest = (target, method, param) => {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: target,
                    type: method,
                    data: param,
                    dataType: 'json',
                    success : function(result) { resolve(result) },
                    error : function(err) { reject(err) }
                })
            })
        }

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $( document ).ready(function() {
            $('#inboxFormData').hide()
            getListData('#inboxList', true)
        });

        inboxAdd = () => {
            $('#inboxFormData [name=old_id]').val(null)
            $('#inboxFormData button[type=reset]').click()
            $('#inboxFormData').show()
        }

        submitForm = (elem) => {
            let input = {}
            input['name'] = $(elem+' [name=name]').val()
            input['email'] = $(elem+' [name=email]').val()
            input['subjeck'] = $(elem+' [name=subjeck]').val()
            input['phone'] = $(elem+' [name=phone]').val()
            input['message'] = $(elem+' [name=message]').val()
            input['id'] = $(elem+' [name=old_id]').val()
            let type = 'store'
            if (input['id'] != null && input['id'] != '') { type = 'update' }
            let method = configPage['endpoint'][type]['method']
            let endpoint = configPage['endpoint'][type]['url']

            httpRequest(endpoint,method,input).then(function(res){
                alert(res.message)
                inboxOpen(res.id)
                getListData('#inboxList', true)
            })
            return false
        }

        inboxDelete = (id) => {
            let endpoint = configPage.endpoint.delete.url
            let method = configPage.endpoint.delete.method
            httpRequest(endpoint,method,{id}).then(function(res){
                alert(res.message)
                getListData('#inboxList', true)
            })
        }

        inboxOpen = (id) => {
            let endpoint = configPage.endpoint.open.url
            let method = configPage.endpoint.open.method
            httpRequest(endpoint,method,{id}).then(function(res){
                inboxAdd()
                let elem = '#inboxFormData'
                $(elem+' [name=name]').val(res.data.name)
                $(elem+' [name=email]').val(res.data.email)
                $(elem+' [name=subjeck]').val(res.data.subjeck)
                $(elem+' [name=phone]').val(res.data.phone)
                $(elem+' [name=message]').val(res.data.message)
                $(elem+' [name=old_id]').val(res.data.id)
            })
        }

        getListData = (elem, firstPage) => {
            let endpoint = configPage.endpoint.list.url
            let method = configPage.endpoint.list.method
            let condition = {}
                condition['created_at'] = $(elem+' [name=filter_created_at]').val()
                condition['name'] = $(elem+' [name=filter_name]').val()
                condition['email'] = $(elem+' [name=filter_email]').val()
                condition['subjeck'] = $(elem+' [name=filter_subjeck]').val()
                condition['phone'] = $(elem+' [name=filter_phone]').val()
                condition['show'] = $(elem+' [name=show]').val()
                condition['orderBy'] = $(elem+' [name=orderBy]').val()
                condition['orderByValue'] = $(elem+' [name=orderByValue]').val()
                condition['page'] = 1
            if (firstPage == false) { condition['page'] = $(elem+' [name=page]').val() }

            httpRequest(endpoint,method,condition).then(function(res){
                const datas = res.datas
                $(elem+' [name=page]').val(datas.current_page)
                $(elem+' [name=page]').attr('max',datas.last_page)
                $(elem+' #inboxListPanel strong').html(datas.last_page)
                $(elem+' table tbody').html('')
                
                if (datas.data.length == 0) {
                    $(elem+' table tbody').html('<tr><td colspan="'+configPage.table.length+'" class="text-center">---Not Found Data---</td></tr>')
                }else{
                    $.each(datas.data, function(idx, row){
                        let row_table = '<tr>'
                        $.each(configPage.table, function(tIdx, tRow){
                            if (tRow.field == 'tools') {
                                row_table += '<td>'
                                row_table += '<div class="btn-group" role="group" aria-label="tools">'
                                row_table += '<button onclick="inboxDelete('+row.id+')" type="button" class="btn-sm btn btn-outline-danger">Delete</button>'
                                row_table += '<button onclick="inboxOpen('+row.id+')" type="button" class="btn-sm btn btn-outline-primary">Open</button>'
                                row_table += '</div>'
                                row_table += '</td>'
                            }else{ row_table += '<td>'+row[tRow.field]+'</td>' }
                        })
                        row_table += '</tr>'
                        $(elem+' table tbody').append(row_table)
                    })
                }
            })
            return false
        }
    </script>
</body>
</html>