{{ Form::open(array('route' => array('admin.core.license.storage.clean'))) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $directoryName }}</h4>
</div>
<div class="modal-body">
    <div class="row row-0" style="margin: -5px -5px 5px -5px">
        <div class="col-sm-4">
            <div class="form-group">
                <div class="input-group">
                    {{ Form::text('search', null, ['class' => 'form-control', 'placeholder' => 'Filtrar por nome ou tamanho do ficheiro...']) }}
                    <div class="input-group-addon"><i class="fas fa-search"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-1 col-sm-offset-5">
            <p class="text-right m-t-7 m-r-5">Ordenar por:</p>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::select('sort_by', [
                    'filename_asc'  => 'Nome Ascendente',
                    'filename_desc' => 'Nome Descendente',
                    'size_asc'      => 'Tamanho Ascendente',
                    'size_desc'     => 'Tamanho Descendente',
                    'created_asc'   => 'Data Ascendente',
                    'created_desc'  => 'Data Descendente'
                ], null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="directory-preview">
        <div class="row row-5">
            @forelse($files as $file)
            <?php $filepath = str_replace(public_path(), '', str_replace(' ', '%20',$file->getRealPath()));?>
            <div class="col-sm-1" data-file="{{ $filepath }}" data-filename="{{ str_replace('.' . $file->getExtension(), '', $file->getFilename()) }}" data-size="{{ $file->getSize() }}" data-created="{{ $file->getMTime() }}">
                <div class="item">
                    <div class="item-preview">
                        <a href="#" class="file-delete">
                            <i class="fas fa-times-circle"></i>
                        </a>
                        <a href="{{ @$storage ? route('admin.core.license.file.download', ['file' => $filepath]) : asset($filepath) }}" target="_blank">
                        @if(in_array($file->getExtension(), ['jpg', 'jpeg', 'png', 'bmp', 'gif']))
                            <div style="background-image: url({{ asset($filepath) }})" class="img"></div>
                        @elseif(in_array($file->getExtension(), ['pdf']))
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAzMDkuMjY3IDMwOS4yNjciIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDMwOS4yNjcgMzA5LjI2NzsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI2NHB4IiBoZWlnaHQ9IjY0cHgiPgo8Zz4KCTxwYXRoIHN0eWxlPSJmaWxsOiNFMjU3NEM7IiBkPSJNMzguNjU4LDBoMTY0LjIzbDg3LjA0OSw4Ni43MTF2MjAzLjIyN2MwLDEwLjY3OS04LjY1OSwxOS4zMjktMTkuMzI5LDE5LjMyOUgzOC42NTggICBjLTEwLjY3LDAtMTkuMzI5LTguNjUtMTkuMzI5LTE5LjMyOVYxOS4zMjlDMTkuMzI5LDguNjUsMjcuOTg5LDAsMzguNjU4LDB6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojQjUzNjI5OyIgZD0iTTI4OS42NTgsODYuOTgxaC02Ny4zNzJjLTEwLjY3LDAtMTkuMzI5LTguNjU5LTE5LjMyOS0xOS4zMjlWMC4xOTNMMjg5LjY1OCw4Ni45ODF6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojRkZGRkZGOyIgZD0iTTIxNy40MzQsMTQ2LjU0NGMzLjIzOCwwLDQuODIzLTIuODIyLDQuODIzLTUuNTU3YzAtMi44MzItMS42NTMtNS41NjctNC44MjMtNS41NjdoLTE4LjQ0ICAgYy0zLjYwNSwwLTUuNjE1LDIuOTg2LTUuNjE1LDYuMjgydjQ1LjMxN2MwLDQuMDQsMi4zLDYuMjgyLDUuNDEyLDYuMjgyYzMuMDkzLDAsNS40MDMtMi4yNDIsNS40MDMtNi4yODJ2LTEyLjQzOGgxMS4xNTMgICBjMy40NiwwLDUuMTktMi44MzIsNS4xOS01LjY0NGMwLTIuNzU0LTEuNzMtNS40OS01LjE5LTUuNDloLTExLjE1M3YtMTYuOTAzQzIwNC4xOTQsMTQ2LjU0NCwyMTcuNDM0LDE0Ni41NDQsMjE3LjQzNCwxNDYuNTQ0eiAgICBNMTU1LjEwNywxMzUuNDJoLTEzLjQ5MmMtMy42NjMsMC02LjI2MywyLjUxMy02LjI2Myw2LjI0M3Y0NS4zOTVjMCw0LjYyOSwzLjc0LDYuMDc5LDYuNDE3LDYuMDc5aDE0LjE1OSAgIGMxNi43NTgsMCwyNy44MjQtMTEuMDI3LDI3LjgyNC0yOC4wNDdDMTgzLjc0MywxNDcuMDk1LDE3My4zMjUsMTM1LjQyLDE1NS4xMDcsMTM1LjQyeiBNMTU1Ljc1NSwxODEuOTQ2aC04LjIyNXYtMzUuMzM0aDcuNDEzICAgYzExLjIyMSwwLDE2LjEwMSw3LjUyOSwxNi4xMDEsMTcuOTE4QzE3MS4wNDQsMTc0LjI1MywxNjYuMjUsMTgxLjk0NiwxNTUuNzU1LDE4MS45NDZ6IE0xMDYuMzMsMTM1LjQySDkyLjk2NCAgIGMtMy43NzksMC01Ljg4NiwyLjQ5My01Ljg4Niw2LjI4MnY0NS4zMTdjMCw0LjA0LDIuNDE2LDYuMjgyLDUuNjYzLDYuMjgyczUuNjYzLTIuMjQyLDUuNjYzLTYuMjgydi0xMy4yMzFoOC4zNzkgICBjMTAuMzQxLDAsMTguODc1LTcuMzI2LDE4Ljg3NS0xOS4xMDdDMTI1LjY1OSwxNDMuMTUyLDExNy40MjUsMTM1LjQyLDEwNi4zMywxMzUuNDJ6IE0xMDYuMTA4LDE2My4xNThoLTcuNzAzdi0xNy4wOTdoNy43MDMgICBjNC43NTUsMCw3Ljc4LDMuNzExLDcuNzgsOC41NTNDMTEzLjg3OCwxNTkuNDQ3LDExMC44NjMsMTYzLjE1OCwxMDYuMTA4LDE2My4xNTh6Ii8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" />
                        @elseif(in_array($file->getExtension(), ['xls', 'xlsx', 'csv']))
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMTI4cHgiIGhlaWdodD0iMTI4cHgiPgo8cGF0aCBzdHlsZT0iZmlsbDojRUNFRkYxOyIgZD0iTTQ5Niw0MzIuMDExSDI3MmMtOC44MzIsMC0xNi03LjE2OC0xNi0xNnMwLTMxMS4xNjgsMC0zMjBzNy4xNjgtMTYsMTYtMTZoMjI0ICBjOC44MzIsMCwxNiw3LjE2OCwxNiwxNnYzMjBDNTEyLDQyNC44NDMsNTA0LjgzMiw0MzIuMDExLDQ5Niw0MzIuMDExeiIvPgo8Zz4KCTxwYXRoIHN0eWxlPSJmaWxsOiMzODhFM0M7IiBkPSJNMzM2LDE3Ni4wMTFoLTY0Yy04LjgzMiwwLTE2LTcuMTY4LTE2LTE2czcuMTY4LTE2LDE2LTE2aDY0YzguODMyLDAsMTYsNy4xNjgsMTYsMTYgICBTMzQ0LjgzMiwxNzYuMDExLDMzNiwxNzYuMDExeiIvPgoJPHBhdGggc3R5bGU9ImZpbGw6IzM4OEUzQzsiIGQ9Ik0zMzYsMjQwLjAxMWgtNjRjLTguODMyLDAtMTYtNy4xNjgtMTYtMTZzNy4xNjgtMTYsMTYtMTZoNjRjOC44MzIsMCwxNiw3LjE2OCwxNiwxNiAgIFMzNDQuODMyLDI0MC4wMTEsMzM2LDI0MC4wMTF6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojMzg4RTNDOyIgZD0iTTMzNiwzMDQuMDExaC02NGMtOC44MzIsMC0xNi03LjE2OC0xNi0xNnM3LjE2OC0xNiwxNi0xNmg2NGM4LjgzMiwwLDE2LDcuMTY4LDE2LDE2ICAgUzM0NC44MzIsMzA0LjAxMSwzMzYsMzA0LjAxMXoiLz4KCTxwYXRoIHN0eWxlPSJmaWxsOiMzODhFM0M7IiBkPSJNMzM2LDM2OC4wMTFoLTY0Yy04LjgzMiwwLTE2LTcuMTY4LTE2LTE2czcuMTY4LTE2LDE2LTE2aDY0YzguODMyLDAsMTYsNy4xNjgsMTYsMTYgICBTMzQ0LjgzMiwzNjguMDExLDMzNiwzNjguMDExeiIvPgoJPHBhdGggc3R5bGU9ImZpbGw6IzM4OEUzQzsiIGQ9Ik00MzIsMTc2LjAxMWgtMzJjLTguODMyLDAtMTYtNy4xNjgtMTYtMTZzNy4xNjgtMTYsMTYtMTZoMzJjOC44MzIsMCwxNiw3LjE2OCwxNiwxNiAgIFM0NDAuODMyLDE3Ni4wMTEsNDMyLDE3Ni4wMTF6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojMzg4RTNDOyIgZD0iTTQzMiwyNDAuMDExaC0zMmMtOC44MzIsMC0xNi03LjE2OC0xNi0xNnM3LjE2OC0xNiwxNi0xNmgzMmM4LjgzMiwwLDE2LDcuMTY4LDE2LDE2ICAgUzQ0MC44MzIsMjQwLjAxMSw0MzIsMjQwLjAxMXoiLz4KCTxwYXRoIHN0eWxlPSJmaWxsOiMzODhFM0M7IiBkPSJNNDMyLDMwNC4wMTFoLTMyYy04LjgzMiwwLTE2LTcuMTY4LTE2LTE2czcuMTY4LTE2LDE2LTE2aDMyYzguODMyLDAsMTYsNy4xNjgsMTYsMTYgICBTNDQwLjgzMiwzMDQuMDExLDQzMiwzMDQuMDExeiIvPgoJPHBhdGggc3R5bGU9ImZpbGw6IzM4OEUzQzsiIGQ9Ik00MzIsMzY4LjAxMWgtMzJjLTguODMyLDAtMTYtNy4xNjgtMTYtMTZzNy4xNjgtMTYsMTYtMTZoMzJjOC44MzIsMCwxNiw3LjE2OCwxNiwxNiAgIFM0NDAuODMyLDM2OC4wMTEsNDMyLDM2OC4wMTF6Ii8+CjwvZz4KPHBhdGggc3R5bGU9ImZpbGw6IzJFN0QzMjsiIGQ9Ik0yODIuMjA4LDE5LjY5MWMtMy42NDgtMy4wNC04LjU0NC00LjM1Mi0xMy4xNTItMy4zOTJsLTI1Niw0OEM1LjQ3Miw2NS43MDcsMCw3Mi4yOTksMCw4MC4wMTF2MzUyICBjMCw3LjY4LDUuNDcyLDE0LjMwNCwxMy4wNTYsMTUuNzEybDI1Niw0OGMwLjk2LDAuMTkyLDEuOTUyLDAuMjg4LDIuOTQ0LDAuMjg4YzMuNzEyLDAsNy4zMjgtMS4yOCwxMC4yMDgtMy42OCAgYzMuNjgtMy4wNCw1Ljc5Mi03LjU4NCw1Ljc5Mi0xMi4zMnYtNDQ4QzI4OCwyNy4yNDMsMjg1Ljg4OCwyMi43MzEsMjgyLjIwOCwxOS42OTF6Ii8+CjxwYXRoIHN0eWxlPSJmaWxsOiNGQUZBRkE7IiBkPSJNMjIwLjAzMiwzMDkuNDgzbC01MC41OTItNTcuODI0bDUxLjE2OC02NS43OTJjNS40NC02Ljk3Niw0LjE2LTE3LjAyNC0yLjc4NC0yMi40NjQgIGMtNi45NDQtNS40NC0xNi45OTItNC4xNi0yMi40NjQsMi43ODRsLTQ3LjM5Miw2MC45MjhsLTM5LjkzNi00NS42MzJjLTUuODU2LTYuNzItMTUuOTY4LTcuMzI4LTIyLjU2LTEuNTA0ICBjLTYuNjU2LDUuODI0LTcuMzI4LDE1LjkzNi0xLjUwNCwyMi41Nmw0NCw1MC4zMDRMODMuMzYsMzEwLjE4N2MtNS40NCw2Ljk3Ni00LjE2LDE3LjAyNCwyLjc4NCwyMi40NjQgIGMyLjk0NCwyLjI3Miw2LjQzMiwzLjM2LDkuODU2LDMuMzZjNC43NjgsMCw5LjQ3Mi0yLjExMiwxMi42NC02LjE3Nmw0MC44LTUyLjQ4bDQ2LjUyOCw1My4xNTIgIGMzLjE2OCwzLjY0OCw3LjU4NCw1LjUwNCwxMi4wMzIsNS41MDRjMy43NDQsMCw3LjQ4OC0xLjMxMiwxMC41MjgtMy45NjhDMjI1LjE4NCwzMjYuMjE5LDIyNS44NTYsMzE2LjEwNywyMjAuMDMyLDMwOS40ODN6Ii8+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                        @elseif(in_array($file->getExtension(), ['doc', 'docx']))
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMTI4cHgiIGhlaWdodD0iMTI4cHgiPgo8Zz4KCTxwYXRoIHN0eWxlPSJmaWxsOiMxNTY1QzA7IiBkPSJNMjk0LjY1NiwxMy4wMTRjLTIuNTMxLTIuMDU2LTUuODYzLTIuODQyLTkuMDQ1LTIuMTMzbC0yNzcuMzMzLDY0ICAgQzMuMzk3LDc2LjAwMy0wLjA0Nyw4MC4zNjksMCw4NS4zNzd2MzYyLjY2N2MwLjAwMiw1LjI2MywzLjg0Myw5LjczOSw5LjA0NSwxMC41MzlsMjc3LjMzMyw0Mi42NjcgICBjNS44MjMsMC44OTUsMTEuMjY5LTMuMDk5LDEyLjE2NC04LjkyMWMwLjA4Mi0wLjUzNSwwLjEyNC0xLjA3NiwwLjEyNC0xLjYxN1YyMS4zNzdDMjk4LjY3NiwxOC4xMjQsMjk3LjE5OSwxNS4wNDUsMjk0LjY1NiwxMy4wMTQgICB6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojMTU2NUMwOyIgZD0iTTUwMS4zMzQsNDU4LjcxSDI4OGMtNS44OTEsMC0xMC42NjctNC43NzYtMTAuNjY3LTEwLjY2N2MwLTUuODkxLDQuNzc2LTEwLjY2NywxMC42NjctMTAuNjY3ICAgaDIwMi42NjdWNzQuNzFIMjg4Yy01Ljg5MSwwLTEwLjY2Ny00Ljc3Ni0xMC42NjctMTAuNjY3UzI4Mi4xMDksNTMuMzc3LDI4OCw1My4zNzdoMjEzLjMzM2M1Ljg5MSwwLDEwLjY2Nyw0Ljc3NiwxMC42NjcsMTAuNjY3ICAgdjM4NEM1MTIsNDUzLjkzNSw1MDcuMjI1LDQ1OC43MSw1MDEuMzM0LDQ1OC43MXoiLz4KPC9nPgo8cGF0aCBzdHlsZT0iZmlsbDojRkFGQUZBOyIgZD0iTTE4MS4zMzQsMzUyLjA0NGMtNC43NTMtMC4wMDUtOC45MjgtMy4xNTUtMTAuMjQtNy43MjNMMTM4LjY2NywyMzAuODdMMTA2LjI0LDM0NC4zMjEgIGMtMi4zNDIsNS42NjEtOC44Myw4LjM1Mi0xNC40OTIsNi4wMWMtMi43MjItMS4xMjYtNC44ODQtMy4yODgtNi4wMS02LjAxTDQzLjA3MiwxOTQuOTg4Yy0xLjc4Ni01LjYxNCwxLjMxOC0xMS42MTIsNi45MzItMTMuMzk4ICBjNS42MTQtMS43ODYsMTEuNjEyLDEuMzE4LDEzLjM5OCw2LjkzMmMwLjA2MywwLjE5OCwwLjEyLDAuMzk4LDAuMTcyLDAuNTk5TDk2LDMwMi41NUwxMjguNDI3LDE4OS4xICBjMi4zNDItNS42NjEsOC44My04LjM1MiwxNC40OTItNi4wMWMyLjcyMiwxLjEyNiw0Ljg4NCwzLjI4OCw2LjAxLDYuMDFsMzIuNDA1LDExMy40NTFsMzIuNDI3LTExMy40MjkgIGMxLjUzNS01LjYxNCw3LjMzMS04LjkyMSwxMi45NDUtNy4zODZjMC4wOCwwLjAyMiwwLjE1OSwwLjA0NSwwLjIzOSwwLjA2OGM1LjY2LDEuNjIyLDguOTM1LDcuNTIzLDcuMzE3LDEzLjE4NGwtNDIuNjY3LDE0OS4zMzMgIEMxOTAuMjgxLDM0OC44OTcsMTg2LjA5NCwzNTIuMDQ4LDE4MS4zMzQsMzUyLjA0NHoiLz4KPGc+Cgk8cGF0aCBzdHlsZT0iZmlsbDojMTU2NUMwOyIgZD0iTTQ1OC42NjcsMTM4LjcxSDI4OGMtNS44OTEsMC0xMC42NjctNC43NzYtMTAuNjY3LTEwLjY2N2MwLTUuODkxLDQuNzc2LTEwLjY2NywxMC42NjctMTAuNjY3ICAgaDE3MC42NjdjNS44OTEsMCwxMC42NjcsNC43NzYsMTAuNjY3LDEwLjY2N0M0NjkuMzM0LDEzMy45MzUsNDY0LjU1OCwxMzguNzEsNDU4LjY2NywxMzguNzF6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojMTU2NUMwOyIgZD0iTTQ1OC42NjcsMjAyLjcxSDI4OGMtNS44OTEsMC0xMC42NjctNC43NzYtMTAuNjY3LTEwLjY2N3M0Ljc3Ni0xMC42NjcsMTAuNjY3LTEwLjY2N2gxNzAuNjY3ICAgYzUuODkxLDAsMTAuNjY3LDQuNzc2LDEwLjY2NywxMC42NjdTNDY0LjU1OCwyMDIuNzEsNDU4LjY2NywyMDIuNzF6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojMTU2NUMwOyIgZD0iTTQ1OC42NjcsMjY2LjcxSDI4OGMtNS44OTEsMC0xMC42NjctNC43NzYtMTAuNjY3LTEwLjY2N2MwLTUuODkxLDQuNzc2LTEwLjY2NywxMC42NjctMTAuNjY3ICAgaDE3MC42NjdjNS44OTEsMCwxMC42NjcsNC43NzYsMTAuNjY3LDEwLjY2N0M0NjkuMzM0LDI2MS45MzUsNDY0LjU1OCwyNjYuNzEsNDU4LjY2NywyNjYuNzF6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojMTU2NUMwOyIgZD0iTTQ1OC42NjcsMzMwLjcxSDI4OGMtNS44OTEsMC0xMC42NjctNC43NzYtMTAuNjY3LTEwLjY2N2MwLTUuODkxLDQuNzc2LTEwLjY2NywxMC42NjctMTAuNjY3ICAgaDE3MC42NjdjNS44OTEsMCwxMC42NjcsNC43NzYsMTAuNjY3LDEwLjY2N0M0NjkuMzM0LDMyNS45MzUsNDY0LjU1OCwzMzAuNzEsNDU4LjY2NywzMzAuNzF6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojMTU2NUMwOyIgZD0iTTQ1OC42NjcsMzk0LjcxSDI4OGMtNS44OTEsMC0xMC42NjctNC43NzYtMTAuNjY3LTEwLjY2N2MwLTUuODkxLDQuNzc2LTEwLjY2NywxMC42NjctMTAuNjY3ICAgaDE3MC42NjdjNS44OTEsMCwxMC42NjcsNC43NzYsMTAuNjY3LDEwLjY2N0M0NjkuMzM0LDM4OS45MzUsNDY0LjU1OCwzOTQuNzEsNDU4LjY2NywzOTQuNzF6Ii8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" />
                        @else
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAzMDkuMjY3IDMwOS4yNjciIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDMwOS4yNjcgMzA5LjI2NzsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI2NHB4IiBoZWlnaHQ9IjY0cHgiPgo8Zz4KCTxwYXRoIHN0eWxlPSJmaWxsOiNFNEU3RTc7IiBkPSJNMzguNjU4LDBoMTY0LjIzbDg3LjA0OSw4Ni43MTF2MjAzLjIyN2MwLDEwLjY3OS04LjY1OSwxOS4zMjktMTkuMzI5LDE5LjMyOUgzOC42NTggICBjLTEwLjY3LDAtMTkuMzI5LTguNjUtMTkuMzI5LTE5LjMyOVYxOS4zMjlDMTkuMzI5LDguNjUsMjcuOTg5LDAsMzguNjU4LDB6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojQzJDNUM3OyIgZD0iTTI4OS42NTgsODYuOTgxaC02Ny4zNzJjLTEwLjY3LDAtMTkuMzI5LTguNjU5LTE5LjMyOS0xOS4zMjlWMC4xOTNMMjg5LjY1OCw4Ni45ODF6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojQ0NEMEQyOyIgZD0iTTU3Ljk4OCwxMjUuNjR2MTkuMzI5SDI1MS4yOFYxMjUuNjRINTcuOTg4eiBNNTcuOTg4LDE4My42MzdIMjUxLjI4di0xOS4zMjlINTcuOTg4VjE4My42Mzd6ICAgIE01Ny45ODgsMjIyLjI4NkgyNTEuMjh2LTE5LjMyOUg1Ny45ODhWMjIyLjI4NnogTTU3Ljk4OCwyNjAuOTQ0SDI1MS4yOHYtMTkuMzJINTcuOTg4VjI2MC45NDR6IE0xNjQuMjk4LDg2Ljk4MUg1Ny45ODh2MTkuMzI5ICAgaDEwNi4zMTFMMTY0LjI5OCw4Ni45ODFMMTY0LjI5OCw4Ni45ODF6IE0xNjQuMjk4LDQ4LjMyM0g1Ny45ODh2MTkuMzI5aDEwNi4zMTFMMTY0LjI5OCw0OC4zMjNMMTY0LjI5OCw0OC4zMjN6Ii8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" />
                        @endif
                        </a>
                    </div>
                    <div class="item-name" title="{{ $file->getFilename() }}">{{ $file->getFilename() }}</div>
                    <div class="item-size">{{ human_filesize($file->getSize()) }}</div>
                </div>
            </div>
            @empty
            <div class="col-sm-12">
                <h4 class="text-center text-muted" style="margin-top: 150px;">
                    <i class="fas fa-folder-open-o"></i> Não há ficheiros nesta directoria
                </h4>
            </div>
            @endforelse
        </div>
    </div>
</div>
<div class="modal-footer">
    <a href="{{ route('admin.core.license.directory.clean', ['folder' => $directoryName]) }}" data-method="delete"
       data-confirm="Pretende eliminar todos os ficheiros da diretoria selecionada?"
       data-confirm-title="Limpar diretoria"
       class="btn btn-sm btn-default pull-left">
        <i class="fas fa-trash-alt"></i> Limpar Diretoria
    </a>
    <a href="{{ route('admin.core.license.directory.compact', ['folder' => $directoryName]) }}"
       data-method="post"
       data-confirm="Pretende compactar todos os ficheiros da diretoria selecionada?"
       data-confirm-title="Compactar diretoria"
       data-confirm-label="Compactar"
       data-confirm-class="btn-success"
       class="btn btn-sm btn-default pull-left">
        <i class="fas fa-compress"></i> Compactar
    </a>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>
{{ Form::close() }}
@include('admin.core.license.modals.confirm')
<script>

    $('.select2').select2(Init.select2());

    $('[name="search"]').on('keyup', function () {
        var searchTxt = $(this).val().toUpperCase();
        if(searchTxt == '') {
            $('[data-file]').show();
        } else {
            $('[data-file]').hide();
            $('[data-file]').each(function(){
                if($(this).text().toUpperCase().indexOf(searchTxt.toUpperCase()) != -1){
                    $(this).show();
                }
            });
        }
    })

    $('[name="sort_by"]').on('change', function () {
        var sortVal = $(this).val();
        sortVal = sortVal.split("_");

        var sortBy  = sortVal[0]
        var sortDir = sortVal[1]

        var $wrapper = $('.directory-preview .row .col-sm-1');

        $wrapper.sort(function (a, b) {

            if(sortDir == 'asc') {
                if(sortBy == 'filename') {
                    if($(a).data(sortBy) < $(b).data(sortBy)) { return -1; }
                    if($(a).data(sortBy) > $(b).data(sortBy)) { return 1; }
                    return 0;
                } else {
                    return $(a).data(sortBy) - $(b).data(sortBy);
                }
            } else {
                if(sortBy == 'filename') {
                    if($(b).data(sortBy) < $(a).data(sortBy)) { return -1; }
                    if($(b).data(sortBy) > $(a).data(sortBy)) { return 1; }
                    return 0;
                } else {
                    return $(b).data(sortBy) - $(a).data(sortBy);
                }
            }

        });

        $wrapper.appendTo('.directory-preview .row');
    })

    $('.file-delete').on('click', function(e) {
        e.preventDefault();
        var fileUrl = $(this).closest('[data-file]').data('file');
        $('#modal-confirm').addClass('in').show();
        $('#modal-confirm input[name="file"]').val(fileUrl);
    })

    $('[data-confirm-btn]').on('click', function(){
        if($(this).data('confirm-btn') == '0') {
            $('#modal-confirm').removeClass('in').hide();
        } else {
            var fileUrl = $('#modal-confirm input[name="file"]').val()
            $.post("{{ route('admin.core.license.file.destroy') }}", {file:fileUrl}, function(){
                $.bootstrapGrowl("<i class='fas fa-check'></i> Ficheiro removido com sucesso.", {type: 'success', align: 'center', width: 'auto', delay: 8000});
                $('#modal-confirm').removeClass('in').hide();
                $('[data-file="'+fileUrl+'"]').remove();
            }).error(function () {
                $.bootstrapGrowl("<i class='fas fa-exclamation-circle'></i> Ocorreu um erro ao eliminar ficheiro.", {type: 'error', align: 'center', width: 'auto', delay: 8000});
            })
        }
    })

    /*$('.btn-obsolete').on('click', function(e){
        e.preventDefault();

        var $this = $(this);
        $this.button('loading');

        $.get($this.attr('href'), function(){

        }).error(function () {
            $.bootstrapGrowl("<i class='fas fa-exclamation-circle'></i> Ocorreu um erro ao consultar ficheiros obsoletos.", {type: 'error', align: 'center', width: 'auto', delay: 8000});
        }).always(function() {
            $this.button('reset')
        })
    })*/
</script>