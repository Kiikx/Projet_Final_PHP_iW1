document.addEventListener("DOMContentLoaded", () => {
    const dropArea = document.getElementById("drop-area");
    const fileInput = document.getElementById("file-input");
    const previewContainer = document.getElementById("preview-container");
    const uploadForm = document.getElementById("upload-form");
    const uploadStatus = document.getElementById("upload-status");

    if (!uploadForm || !fileInput || !uploadStatus) {
        console.error("❌ Erreur : Certains éléments du formulaire sont introuvables.");
        return;
    }

    console.log("✅ Formulaire détecté, initialisation OK.");

    // Gestion du drag & drop
    ["dragover", "dragenter"].forEach(event => {
        dropArea.addEventListener(event, (e) => {
            e.preventDefault();
            dropArea.classList.add("highlight");
        });
    });

    ["dragleave", "drop"].forEach(event => {
        dropArea.addEventListener(event, (e) => {
            e.preventDefault();
            dropArea.classList.remove("highlight");
        });
    });

    dropArea.addEventListener("drop", (e) => {
        const files = e.dataTransfer.files;
        handleFiles(files);
    });

    // Gestion du changement de fichier
    fileInput.addEventListener("change", (e) => {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        const file = files[0]; // Prend uniquement le premier fichier
        if (!file || !["image/jpeg", "image/png", "image/gif", "image/webp"].includes(file.type)) {
            alert("Seuls les fichiers JPEG, PNG, GIF ou WebP sont acceptés.");
            return;
        }

        console.log("📂 Fichier sélectionné :", file.name);
        previewFile(file);
    }

    function previewFile(file) {
        const reader = new FileReader();
        reader.onload = () => {
            previewContainer.innerHTML = `<img src="${reader.result}" alt="Aperçu" style="max-width: 100%; height: auto;">`;
        };
        reader.readAsDataURL(file);
    }

    // Gestion de l'upload via AJAX
    uploadForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        console.log("📤 Tentative d'envoi du formulaire...");

        const formData = new FormData(uploadForm);
        uploadStatus.textContent = "⏳ Envoi en cours...";
        uploadStatus.style.color = "blue";

        try {
            const response = await fetch(uploadForm.action, {
                method: "POST",
                body: formData
            });

            const result = await response.text();
            console.log("🔄 Réponse serveur :", result);

            if (!response.ok) {
                throw new Error(`Erreur ${response.status} : ${result}`);
            }

            uploadStatus.textContent = "✅ Upload réussi !";
            uploadStatus.style.color = "green";
        } catch (error) {
            console.error("❌ Erreur lors de l'envoi :", error);
            uploadStatus.textContent = `❌ Erreur : ${error.message}`;
            uploadStatus.style.color = "red";
        }
    });
});
