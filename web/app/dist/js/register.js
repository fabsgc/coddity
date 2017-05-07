/* Ouverture de l'upload */
$('#upload-picture-image').click(function(){
    $("#step_one_picture").trigger('click');
});

/* Affichage de l'aperçu de l'image */
document.getElementById('step_one_picture').onchange = function (evt) {
    var tgt = evt.target || window.event.srcElement,
        files = tgt.files;

    if (FileReader && files && files.length) {
        if(files[0].type.match(/^image\//)) {
            var fr = new FileReader();

            fr.onload = function () {
                document.getElementById('upload-picture-image').src = fr.result;
            }

            fr.readAsDataURL(files[0]);
        }
    }
}