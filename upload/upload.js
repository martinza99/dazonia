const dropzone = document.body;
dropzone.addEventListener("dragenter", stopDefault, false);
dropzone.addEventListener("dragover", stopDefault, false);
dropzone.addEventListener("drop", dropHandler, false);

function stopDefault(event) {
	event.preventDefault();
	event.stopPropagation();
}

function dropHandler(event) {
	stopDefault(event);
	for (const file of event.dataTransfer.files) {
		const proBar = document.createElement("progress");
		proBar.className = "uploadProgress";
		const progressContainer = document.querySelector("#uploadProgress");
		progressContainer.appendChild(proBar);
		proBar.style.display = "block";
		proBar.style.backgroundColor = "";
		const formData = new FormData();
		formData.append("dragged", true);
		formData.append("file", file);
		const xhr = new XMLHttpRequest();
		xhr.onload = function (event) {
			progressContainer.removeChild(proBar);
		};
		xhr.upload.onprogress = function (event) {
			if (!event.lengthComputable) proBar.value = 0;
			proBar.value = event.loaded / event.total;
		};

		xhr.open("POST", "/upload.php");
		xhr.send(formData);
	}
}
