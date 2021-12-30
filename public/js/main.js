/**
 * Delete trick listener
 */
function deleteTrickListener() {
    var tricks = document.querySelectorAll("[data-delete-trick]");
    for(trick of tricks) {
        trick.addEventListener("click", function(event) {
            event.preventDefault();
            console.log(this.dataset.token);
            if(confirm("Are you sure you want to delete this trick?")) {
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"__token": this.dataset.token})
                })
                .then(response => response.json())
                .then(data => console.log(data))
                .catch(e => console.log(e))
            }
        });
    }
}

/**
 * Add new trick type listener
 */
function addNewTypeListener() {
    let checkbox = document.querySelector("#trick_addNewType");
    checkbox.checked = false;
    checkbox.addEventListener("change", function() {
        let form = this.closest("form");

        var data = "";
        var mainElement = "#trick_trickType";
        var secondElement = "#trick_newTrickType";
        if(this.checked) {
            // Restore default value to selector
            form.querySelector("#trick_trickType").setAttribute("value", "");

            // Add value only if cheched
            data = this.name + "=" + this.value;
            mainElement = "#trick_newTrickType";
            secondElement = "#trick_trickType";
        }
        
        fetch(form.action, {
            method: form.getAttribute("method"),
            body: data,
            mode: "cors",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded; charset:UTF-8;"
            }
        })
        .then(response => response.text())
        .then(html => {
            let content = document.createElement("html");
            content.innerHTML = html;
            let trickTypeElement = content.querySelector(mainElement);
            document.querySelector(secondElement).replaceWith(trickTypeElement);
        })
        .catch( e => console.log(e))
    });
}

/**
 * Update images listener
 */
function updateImageListener() {
    var updateImages = document.querySelectorAll("[data-update-image]");
    for(updateImage of updateImages) {
        updateImage.addEventListener("click", function(e) {
            e.preventDefault();
            var myModal = new bootstrap.Modal(document.querySelector("#modal-window"));
            myModal.show();

            fetch(this.getAttribute("href"), {
                method: "GET"
            })
            .then(response => response.text())
            .then(content => {
                let modalContent = document.querySelector("#modal-body");
                modalContent.innerHTML = content;

                /**
                * Update image src with temp
                **/
                var imageInput = document.querySelector("#trick_image_image");
                let image = document.querySelector("#img-preview");
                imageInput.addEventListener("change", function() {
                    let imagesValues = imageInput.files;
                    for (var i = 0; i < imagesValues.length; i++) {
                        image.src = window.URL.createObjectURL(imagesValues[i]);
                    }
                });

                let form = document.querySelector("form[name='trick_image']");
                form.addEventListener("submit", function(event) {
                    event.preventDefault();
                    let data = new FormData(form);
                    fetch(form.getAttribute("action"), {
                        method: form.getAttribute("method"),
                        body: data
                    })
                    .then(response => response.text())
                    .then(html => {
                         modalContent.innerHTML = html;
                    })
                    .catch( e => console.log(e))
                })
            })
            .catch( e => console.log(e))
        });
    }
}

/**
 * Delete images listener
 */
function deleteImageListener() {
    var deleteImages = document.querySelectorAll("[data-delete-image]");
    for(deleteImage of deleteImages) {
        deleteImage.addEventListener("click", function(event) {
            event.preventDefault();
            if(confirm("Are you sure you want to delete this image?")) {
                console.log("Oui");
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"__token": this.dataset.token})
                })
                .then(response => response.json())
                .then(data => console.log(data))
                .catch(e => console.log(e))
            }
        });
    }
}

/**
 * Add medias listener
 */
function addMediaListener() {
    // Prototype from Symfony Doc
    const addTagFormDeleteLink = (tagFormLi) => {
        const removeFormButton = document.createElement('button')
        removeFormButton.classList
        removeFormButton.innerText = 'Delete this media'

        tagFormLi.append(removeFormButton);

        removeFormButton.addEventListener('click', (e) => {
            e.preventDefault()
            // remove the li for the tag form
            tagFormLi.remove();
        });
    }

    const addFormToCollection = (e) => {
        const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

        const item = document.createElement('li');

        item.innerHTML = collectionHolder
            .dataset
            .prototype
            .replace(
            /__name__/g,
            collectionHolder.dataset.index
            );

        collectionHolder.appendChild(item);
        addTagFormDeleteLink(item);

        collectionHolder.dataset.index++;
    };

    document
        .querySelectorAll('.add_item_link')
        .forEach(btn => btn.addEventListener("click", addFormToCollection));
}

/**
 * Update medias listener
 */
function updateMediaListener() {
    var updateMedias = document.querySelectorAll("[data-update-media]");
    for(updateMedia of updateMedias) {
        updateMedia.addEventListener("click", function(event) {
            event.preventDefault();
            // Update modal title
            document.querySelector("#modalTitle").innerHTML = "Edit trick media";
            var myModal = new bootstrap.Modal(document.querySelector("#modal-window"));
            myModal.show();

            fetch(this.getAttribute("href"), {
                method: "GET"
            })
            .then(response => response.text())
            .then(content => {
                let modalContent = document.querySelector("#modal-body");

                modalContent.innerHTML = content;

                let form = document.querySelector("form[name='trick_media']");
                let preview = document.querySelector("#media-preview");
                let urlMediaInput = document.querySelector("#trick_media_urlTrickMedia");

                // Add dynamically embed tag
                var getTag = getEmbedTag(urlMediaInput.value);
                preview.innerHTML = getTag;

                urlMediaInput.addEventListener("change", function(e) {
                    getTag = getEmbedTag(urlMediaInput.value);
                    preview.innerHTML = getTag;
                });

                form.addEventListener("submit", function(event) {
                    event.preventDefault();
                    let data = new FormData(form);
                    fetch(form.getAttribute("action"), {
                        method: form.getAttribute("method"),
                        body: data
                    })
                    .then(response => response.text())
                    .then(html => {
                        modalContent.innerHTML = html;
                    })
                    .catch( e => console.log(e))
                })

            })
            .catch( e => console.log(e))
        });
    }
}

/**
 * Delete medias listener
 */
function deleteMediaListener() {
    var deleteMedias = document.querySelectorAll("[data-delete-media]");
    for(deleteMedia of deleteMedias) {
        deleteMedia.addEventListener("click", function(event) {
            event.preventDefault();
            if(confirm("Are you sure you want to delete this media?")) {
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"__token": this.dataset.token})
                })
                .then(response => response.json())
                .then(data => console.log(data))
                .catch(e => console.log(e))
            }
        });
    }
}
