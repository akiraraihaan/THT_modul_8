function startLogin(){
    window.location.href = "login.php";
}

function login(event) {
    event.preventDefault();

    const name = document.querySelector("#name");
    const nim = document.querySelector("#nim");

    const oldErrors = document.querySelectorAll(".error");
    oldErrors.forEach(error => error.remove());

    let isValid = true;

    if (name.value === "") {
        const p = document.createElement("p");
        p.setAttribute("class", "error text-red-500 text-sm mt-1");
        p.innerHTML = "Nama harus diisi!";
        name.parentElement.appendChild(p);
        isValid = false;
    }

    if (nim.value === "") {
        const p = document.createElement("p");
        p.setAttribute("class", "error text-red-500 text-sm mt-1");
        p.innerHTML = "NIM harus diisi!";
        nim.parentElement.appendChild(p);
        isValid = false;
    } else if (!(/^[0-9]{9}$/.test(nim.value))) {
        const p = document.createElement("p");
        p.setAttribute("class", "error text-red-500 text-sm mt-1");
        p.innerHTML = "NIM harus berupa angka dengan panjang 9 digit!";
        nim.parentElement.appendChild(p);
        isValid = false;
    }

    if (isValid) {
        localStorage.setItem('playerName', name.value);
        localStorage.setItem('playerNIM', nim.value);
        window.location.href = "quiz.php";
    }
}
