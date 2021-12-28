// Embeded youtube
const dailymotionTag = '<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;"> <iframe style="width:100%;height:100%;position:absolute;left:0px;top:0px;overflow:hidden" frameborder="0" type="text/html" src="https://www.dailymotion.com/embed/video/idVideo?autoplay=1" width="100%" height="100%" allowfullscreen allow="autoplay"> </iframe> </div>';
const youtubeTag = '<iframe width="560" height="315" src="https://www.youtube.com/embed/idVideo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
function getDailymotionTag(value) {
    idVideo = value.match(/^[^=]+\/\s*(.*)\/?\?.*$/);
    //console.log(idVideo[1]);
    
    tag = dailymotionTag.replace(/(idVideo)/g, function() {
        return idVideo[1];
    });
    
    addPreview(tag);
}

function getYoutubeTag(value) {
    idVideo = value.match(/^[^=]+=\s*(.*)$/);
    //console.log(idVideo[1]);
    
    tag = youtubeTag.replace(/(idVideo)/g, function() {
        return idVideo[1];
    });
    
    console.log(tag);
    addPreview(tag);
}

function getEmbedTag(value) {
    if(value.includes("youtube.com")) {
        getYoutubeTag(value);
    }
    if(value.includes("dailymotion.com")) {
        getDailymotionTag(value);
    }
}

function addPreview(value) {
    let previewDiv = document.querySelector("#preview");
    previewDiv.innerHTML += value;
}

window.onload = () => {
    let imageInput = document.querySelector("#images");
    let mediaInput = document.querySelector("#medias");
    let previewDiv = document.querySelector("#preview");
    
    imageInput.addEventListener("change", function() {
        // Restore value of preview box
        previewDiv.innerHTML = '';
        let imagesValues = imageInput.files;
        for (var i = 0; i < imagesValues.length; i++) {
            // Add preview image inside the form
            previewDiv.innerHTML += '<img src="'+window.URL.createObjectURL(imagesValues[i])+'" width="100px" height="100px"/>';
        }
    });
    
    mediaInput.addEventListener("change", function() {
    
        getEmbedTag(mediaInput.value);
    });

}