    //footer status stuff
    function getLanguage(){
        document.getElementById("lang").innerHTML =
            "Language: " + navigator.language;
    }
    document.addEventListener("DOMContentLoaded", getLanguage);

    //online status
    function getStatus(){
        if (navigator.onLine) document.getElementById("status").innerHTML = "Status: Online";
        else document.getElementById("status").innerHTML = "Status: Offline";
    }
     document.addEventListener("DOMContentLoaded", getStatus);



    //browser being used
    function getBrowser(){
        let browser  = "unkown";
        const agent = navigator.userAgent;
    
        if (agent.includes("Firefox")) browser = "Mozilla Firefox";
        else if (agent.includes("Edge")) browser = "Microsoft Edge";
        else if (agent.includes("Chrome")) browser = "Google Chrome";
        else if (agent.includes("Opera")) browser = "Opera";
        else if (agent.includes("Safari") && agent.includes("Chrome")) browser = "Safari";

        document.getElementById("agent").innerHTML = "Platform: " + browser;
    }
    document.addEventListener("DOMContentLoaded", getBrowser);

    //cookies status
    function getCookies(){
    if (navigator.cookieEnabled) 
        document.getElementById("cookies").innerHTML = "Cookies: Enabled";
    else 
        document.getElementById("cookies").textContent = "Cookies: Disabled";
    }
     document.addEventListener("DOMContentLoaded", getCookies);

    //misc
    document.getElementById("ram").innerHTML = "CPU Cores: " + navigator.hardwareConcurrency;
    

//ensures that the name and surname do not have numbers in them
    function validateContactUs(){
        let fName = document.getElementById("fname").value;
        let sName = document.getElementById("sname").value;

        if (/\d/.test(fName) || /\d/.test(sName)){//tests if the variable has any numbers. returns true or false
            window.alert("Error: A name or surname cannot contain any numbers.");
            return false;//prevents the form from being submitted
        }
        return true;
    }

    function confirmSubmit(){
        let contactForm = document.getElementById("contact-form");

        contactForm.addEventListener("submit", (event) =>{  //when the submit event occurs, call validateContactUs
            if(!validateContactUs()){
                event.preventDefault(); //submits he form if all validation checks are true
                return;
            }

            window.alert("Thank you for sending us a message :D");
        });
    }

    document.addEventListener("DOMContentLoaded", confirmSubmit());   //once the entire document has loaded then run confirm SUbmit




    //hamburger menu code for smaller screens
    function openMenu(){
        const hamburger = document.querySelector(".hamburger");
        const navMenu = document.querySelector(".nav-links");

        hamburger.addEventListener("mousedown", () =>{
            hamburger.classList.toggle("active");                      //adds the .active class
            navMenu.classList.toggle("active");
        })

        document.querySelectorAll(".nav-item").forEach(n => n.  //selects all the links in the menu that match the class and loops through each of them
            addEventListener("mousedown", () =>{    //creates an event for when the button is clicked.
                hamburger.classList.remove("active");
                navMenu.classList.remove("active"); //removes the .active class
            })
        )
    }
    document.addEventListener("DOMContentLoaded", openMenu); //prepares the events in openMenu once the document has loaded


    //makes scrolling up the screen smooth
    function toTop(){
    const topButton = document.getElementById("toTop");

    topButton.addEventListener("click", () =>{   //creates an event for when the button is clicked.
        window.scrollTo({
            top: 0, //scrolls to the top of the screen.
            behavior: "smooth"
        });
    });
    }
     document.addEventListener("DOMContentLoaded",toTgit);