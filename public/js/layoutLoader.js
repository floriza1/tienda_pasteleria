document.addEventListener("DOMContentLoaded", () => {
    // Cargar header
    fetch("header.html")
        .then(res => res.text())
        .then(data => {
            document.getElementById("header-container").innerHTML = data;
        });

    // Cargar footer
    fetch("footer.html")
        .then(res => res.text())
        .then(data => {
            document.getElementById("footer-container").innerHTML = data;
        });
});
