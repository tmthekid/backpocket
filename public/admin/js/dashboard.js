function getNewVendors(duration) {
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            document.querySelector(`#${duration}`).innerHTML = JSON.parse(
                xhr.responseText
            ).length;
        }
    };
    xhr.open("GET", `/admin/vendors/new-this-${duration}`, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send();
}

const vendorList = document.querySelector("#vendorFilterList");
document
    .querySelector("#searchVendor")
    .addEventListener("mouseenter", function() {
        vendorList.style.display = "block";
    });
vendorList.addEventListener("mouseleave", function() {
    this.style.display = "none";
});
vendorList.addEventListener("mouseenter", function() {
    this.style.display = "block";
});
document.querySelector("#searchVendor").addEventListener("keyup", function(e) {
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            const list = JSON.parse(xhr.responseText);
            const filterList = document.querySelector("#vendorFilterList");
            filterList.innerHTML = " ";
            if (list.length > 0) {
                for (let l of list) {
                    const a = document.createElement("a");
                    a.innerText = l.name;
                    a.href = `/admin/vendors/${l.id}`;
                    filterList.appendChild(a);
                }
            }
        }
    };
    xhr.open("POST", "/admin/vendors/search", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.setRequestHeader(
        "X-CSRF-TOKEN",
        document.querySelector('input[name="_token"]').value
    );
    xhr.send(JSON.stringify({ search: e.target.value }));
});

getNewVendors("week");
getNewVendors("month");
