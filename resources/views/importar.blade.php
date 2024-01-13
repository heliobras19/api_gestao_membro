<form method="post" enctype="multipart/form-data" action="{{url('/importar')}}">
    @csrf
    <input type="file" name="input_file">
    <button type="submit">Enviar</button>
</form>
