@extends('clients.layouts.app')

@section('content')
    <style type="text/css">
        .inf-content {
            border: 1px solid #DDDDDD;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            box-shadow: 7px 7px 7px rgba(0, 0, 0, 0.3);
        }
    </style>

    <body style="margin-top: 200px;">
        <div class="container bootstrap snippets bootdey" style="margin-top: 200px;">
            <div class="panel-body inf-content">
                <div class="row">
                    <div class="col-md-4">
                        <img alt="" style="width:600px;" title="" class="img-circle img-thumbnail isTooltip"
                            src="{{ Storage::url(Auth::user()->avatar) }}" data-original-title="Usuario">
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-user-information">
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>
                                                <span class="glyphicon glyphicon-user  text-primary"></span>
                                                Tên
                                            </strong>
                                        </td>
                                        <td class="text-primary">
                                            {{ Auth::user()->name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>
                                                <span class="glyphicon glyphicon-envelope text-primary"></span>
                                                Email
                                            </strong>
                                        </td>
                                        <td class="text-primary">
                                            {{ Auth::user()->email }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>
                                                <span class="glyphicon glyphicon-calendar text-primary"></span>
                                                Thời gian tạo
                                            </strong>
                                        </td>
                                        <td class="text-primary">
                                            {{ Auth::user()->created_at }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>
                                                <span class="glyphicon glyphicon-calendar text-primary"></span>
                                                Thời gian cập nhật
                                            </strong>
                                        </td>
                                        <td class="text-primary">
                                            {{ Auth::user()->updated_at }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center mt-3">
                            <a href="#"
                                class="btn btn-primary btn-sm px-4 py-2 rounded-pill shadow-sm text-decoration-none">
                                <i class="glyphicon glyphicon-edit"></i> Sửa thông tin
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
        <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
        <script src="https://netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script type="text/javascript"></script>
    </body>
@endsection
