document.querySelector('.custom-file-input').addEventListener('change',function(e){
    var fileName = document.getElementById("document_attachment").files[0].name;
    var nextSibling = e.target.nextElementSibling
    nextSibling.innerText = fileName
})