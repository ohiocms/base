export default {
    componentUpdated: function (el, binding, vnode) {

        let expression = vnode.data.directives.find(function (o) {
            return o.name === 'model';
        }).expression;

        tinymce.init({
            target: el,
            height: 300,
            menubar: false,
            convert_urls: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste code'
            ],
            toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
            init_instance_callback: function (editor) {
                editor.on('init', function (e) {
                    editor.setContent(_.get(vnode.context, expression));
                });
                editor.on('NodeChange Change KeyUp', function (e) {
                    _.set(vnode.context, expression, editor.getContent());
                });
            }
        });
    }
}