@if(env('TINYMCE_API_KEY'))
    <script src="https://cdn.tiny.cloud/1/{{ env('TINYMCE_API_KEY') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '{{ $selector }}',
            menubar: false,
            language: 'de',
            plugins: 'lists link',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link',
            branding: false
        });
    </script>
@endif
