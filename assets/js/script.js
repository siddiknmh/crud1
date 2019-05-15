document.addEventListener('DOMContentLoaded', function(){
    var link = document.querySelectorAll('.delete');
    for(i=0; i<link.length; i++){
        link[i].addEventListener('click', function(e){
            if (!confirm("Are you sure!")) {
                e.preventDefault();
            }
        });
    }
});