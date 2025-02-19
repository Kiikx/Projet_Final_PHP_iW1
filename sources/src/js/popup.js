function openEditPopup(groupId, groupName) {
    document.getElementById("groupId").value = groupId;
    document.getElementById("groupName").value = groupName;
    document.getElementById("groupModal").style.display = "flex";
}

function closeEditPopup() {
    document.getElementById("groupModal").style.display = "none";
}