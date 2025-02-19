// Sélection des éléments HTML
const dropArea = document.getElementById("drop-area");
const fileInput = document.getElementById("file-input");
const previewContainer = document.getElementById("preview-container");

// Empêche les comportements par défaut pour drag & drop
["dragenter", "dragover", "dragleave", "drop"].forEach((event) => {
    dropArea.addEventListener(event, (e) => e.preventDefault());
    dropArea.addEventListener(event, (e) => e.stopPropagation());
});

// Highlight visuel lorsque l'utilisateur survole ou touche la zone
["dragenter", "dragover"].forEach((event) => {
    dropArea.addEventListener(event, () => dropArea.classList.add("drag-uploader__drop-area--highlight"));
});

["dragleave", "drop"].forEach((event) => {
    dropArea.addEventListener(event, () => dropArea.classList.remove("drag-uploader__drop-area--highlight"));
});

// Gestion du dépôt de fichiers
dropArea.addEventListener("drop", (e) => {
    const files = e.dataTransfer.files;
    handleFiles(files);
});

// Gestion du clic ou de la sélection via le champ input
fileInput.addEventListener("change", (e) => {
    const files = e.target.files;
    handleFiles(files);
});

// Ajout du support tactile pour les appareils mobiles
dropArea.addEventListener("touchstart", (e) => {
    e.preventDefault();
    dropArea.classList.add("drag-uploader__drop-area--highlight");
});

dropArea.addEventListener("touchend", (e) => {
    e.preventDefault();
    dropArea.classList.remove("drag-uploader__drop-area--highlight");
});

// Fonction pour gérer les fichiers
function handleFiles(files) {
    [...files].forEach((file) => {
        if (validateFile(file)) {
            previewFile(file);
        } else {
            alert("Seuls les fichiers JPEG, PNG, GIF ou WebP sont acceptés.");
        }
    });
}

// Validation des fichiers (formats acceptés)
function validateFile(file) {
    const validTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    return validTypes.includes(file.type);
}

// Prévisualisation des fichiers
function previewFile(file) {
    const reader = new FileReader();
    reader.readAsDataURL(file);

    reader.onload = () => {
        const img = document.createElement("img");
        img.src = reader.result;
        img.alt = file.name;
        previewContainer.appendChild(img);
    };
}
