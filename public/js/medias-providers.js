const dailymotionTag = "<div style=\"position:relative;padding-bottom:56.25%;height:0;overflow:hidden;\"> <iframe style=\"width:100%;height:100%;position:absolute;left:0px;top:0px;overflow:hidden\" frameborder=\"0\" type=\"text/html\" src=\"https://www.dailymotion.com/embed/video/idVideo?autoplay=1\" width=\"100%\" height=\"100%\" allowfullscreen allow=\"autoplay\"> </iframe> </div>";

const youtubeTag = "<iframe width=\"100%\" height=\"100%\" style=\"width:100%;height:100%;\" src=\"https://www.youtube.com/embed/idVideo\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";

function getDailymotionTag(value) {
    var idVideo = value.match(/^[^=]+\/\s*(.*)\/?\?.*$/);
    var tag = dailymotionTag.replace(/(idVideo)/g, function() {
        return idVideo[1];
    });
    return tag;
}

function getYoutubeTag(value) {
    var idVideo = value.match(/^[^=]+=\s*(.*)$/);
    var tag = youtubeTag.replace(/(idVideo)/g, function() {
        return idVideo[1];
    });
    return tag;
}

function getEmbedTag(value) {
    if(value.includes("youtube.com")) {
        return getYoutubeTag(value);
    }
    if(value.includes("dailymotion.com")) {
        return getDailymotionTag(value);
    }
}