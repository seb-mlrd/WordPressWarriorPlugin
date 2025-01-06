document.addEventListener('DOMContentLoaded', function () {
    let frame;
    const selectButton = document.getElementById('select-images-button');
    const hiddenInput = document.getElementById('photo_wall_images');
    const previewContainer = document.getElementById('selected-images');

    // Gestion de la sélection des images
    if (selectButton) {
        selectButton.addEventListener('click', function (event) {
            event.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: 'Sélectionner des images',
                button: {
                    text: 'Utiliser ces images'
                },
                multiple: true
            });

            frame.on('select', function () {
                const attachments = frame.state().get('selection').toJSON();
                const imageIds = attachments.map(att => att.id);
                // const imagesHtml = attachments.map(att => {
                //     const thumbUrl = att.sizes.thumbnail ? att.sizes.thumbnail.url : att.sizes.full.url;
                //     return `<tr><td><img src="${thumbUrl}" alt="${att.alt || ''}" style="margin: 5px; max-width: 100px; max-height: 100px;"></td><td><button class="remove-image-button" data-id="${att.id}">Supprimer</button></td></tr>`;
                // });

                hiddenInput.value = imageIds.join(',');
                previewContainer.innerHTML = '<table style="width:100%; border: 1px solid #ccc; border-collapse: collapse;"><tr><th>Image</th><th>Actions</th></tr>' + imagesHtml.join('') + '</table>';

                // Ajouter des gestionnaires de suppression pour chaque image
                addRemoveListeners();
            });

            frame.open();
        });
    }

    // // Fonction pour ajouter les listeners de suppression
    // function addRemoveListeners() {
    //     const removeButtons = document.querySelectorAll('.remove-image-button');
    //     removeButtons.forEach(function(button) {
    //         button.addEventListener('click', function() {
    //             const imageId = this.getAttribute('data-id');
    //             let imageIds = hiddenInput.value.split(',');

    //             // Retirer l'image du tableau
    //             imageIds = imageIds.filter(id => id !== imageId);
    //             hiddenInput.value = imageIds.join(',');

    //             // Retirer la ligne du tableau
    //             this.closest('tr').remove();
    //         });
    //     });
    // }

    // // Ajout des listeners au démarrage si les images sont déjà affichées
    // if (previewContainer.querySelectorAll('.remove-image-button').length > 0) {
    //     addRemoveListeners();
    // }
});
