let theme = localStorage.getItem("theme");
const body = document.body;
const navbar = document.querySelector(".navbar");

if (theme) {
    if (body.classList.contains("light")) {
        body.classList.replace("light", theme);
    } else {
        body.classList.replace("dark", theme);
    }
} else {
    localStorage.setItem("theme", "light");
}

$('#dark').on("click",() => {
    body.classList.replace("light", 'dark');
    localStorage.setItem("theme", "dark");
    cek();
});

$('#light').on("click",() => {
    body.classList.replace("dark", 'light');
    localStorage.setItem("theme", "light");
    cek();
});

$(".navbar-toggler").on("click",()=>{
    setTimeout(()=>{
        cek();
    },800);
})

let cek = () => {

    if (window.pageYOffset > 30) {
        if (localStorage.getItem("theme") == 'light') {
            navbar.classList.replace("bg-transparent", "bg-white");
            navbar.classList.replace("bg-dark-custom", "bg-white");
            navbar.classList.add("shadow-gray");
        } else {
            navbar.classList.replace("bg-transparent", "bg-dark-custom");
            navbar.classList.replace("bg-white", "bg-dark-custom");
            navbar.classList.remove("shadow-gray");
        }
    } else if (window.pageYOffset < 30) {  
        if($(".navbar-collapse").hasClass("show")===false){
            if (localStorage.getItem("theme") == 'light') {
                navbar.classList.replace("bg-white", "bg-transparent");
                navbar.classList.remove("shadow-gray");
            } else {
                navbar.classList.replace("bg-dark-custom", "bg-transparent");
            }
        }
    }

}

window.addEventListener("scroll", cek);

$(".navbar-toggler").on("click",() => {
    if(localStorage.getItem("theme") == 'light'){
        navbar.classList.replace("bg-transparent", "bg-white");
        navbar.classList.replace("bg-dark-custom", "bg-white");
    }else{
        navbar.classList.replace("bg-transparent", "bg-dark-custom");
        navbar.classList.replace("bg-white", "bg-dark-custom");
    }
});

document.addEventListener("click", (e) => {
    let item = e.target;
    if (item.classList[1] === "tentang-nav") {
        window.scrollTo(0, document.getElementById("info-covid").offsetTop - 100);
    } else if (item.classList[1] === "pencegahan-nav") {
        window.scrollTo(0, document.getElementById("tips-pencegahan").offsetTop - 100);
    } else if (item.classList[1] === "data-nav" || item.classList[0] == 'more-detail-btn') {
        window.scrollTo(0, document.getElementById("data-covid").offsetTop - 100);
    } else if (item.classList[1] === "rujukan-nav") {
        window.scrollTo(0, document.getElementById("rs-rujukan").offsetTop - 80);
    }
});