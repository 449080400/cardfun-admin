<form class="form-horizontal" role="form" method ="post"  action="{{ route('admin.products.import_store') }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="form-inline">
        <input id="f_upload" name="import_file" type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">导入</button>
        </div>
    </div>
</form>