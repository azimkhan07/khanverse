/*
|--------------------------------------------------------------------------
| Seller Profile JS
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", function () {

    /*
    |--------------------------------------------------------------------------
    | Counter Animation
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll(".counter").forEach(counter => {

        let target = parseInt(counter.dataset.target);

        let count = 0;

        let speed = Math.ceil(target / 80);

        let interval = setInterval(function () {

            count += speed;

            if (count >= target) {

                count = target;

                clearInterval(interval);

            }

            counter.innerText = count;

        }, 20);

    });




    /*
    |--------------------------------------------------------------------------
    | Progress Circle Animation
    |--------------------------------------------------------------------------
    */

    const progressCircle = document.querySelector(".progress-circle");

    if (progressCircle) {

        let percent = parseInt(progressCircle.dataset.progress || 72);

        let current = 0;

        let interval = setInterval(function () {

            current++;

            progressCircle.style.background =
                `conic-gradient(#4F46E5 ${current * 3.6}deg,#ECECEC 0deg)`;

            let value = progressCircle.querySelector(".inner");

            if (value) {

                value.innerHTML = current + "%";

            }

            if (current >= percent) {

                clearInterval(interval);

            }

        }, 15);

    }




    /*
    |--------------------------------------------------------------------------
    | Image Preview
    |--------------------------------------------------------------------------
    */

    const imageInput = document.querySelector("#profile_image");

    if (imageInput) {

        imageInput.addEventListener("change", function (e) {

            let file = e.target.files[0];

            if (!file) return;

            let reader = new FileReader();

            reader.onload = function (event) {

                let image = document.querySelector(".profile-avatar img");

                if (image) {

                    image.src = event.target.result;

                }

            };

            reader.readAsDataURL(file);

        });

    }




    /*
    |--------------------------------------------------------------------------
    | Ripple Button
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll(".gradient-btn").forEach(button => {

        button.addEventListener("click", function (e) {

            let ripple = document.createElement("span");

            ripple.classList.add("ripple");

            this.appendChild(ripple);

            let x = e.clientX - e.target.offsetLeft;

            let y = e.clientY - e.target.offsetTop;

            ripple.style.left = x + "px";

            ripple.style.top = y + "px";

            setTimeout(function () {

                ripple.remove();

            }, 600);

        });

    });




    /*
    |--------------------------------------------------------------------------
    | Password Strength
    |--------------------------------------------------------------------------
    */

    const password = document.querySelector("#password");

    if (password) {

        password.addEventListener("keyup", function () {

            let value = this.value;

            let strength = document.querySelector("#passwordStrength");

            if (!strength) return;

            if (value.length < 6) {

                strength.innerHTML =
                    '<span class="text-danger">Weak Password</span>';

            }

            else if (value.length < 10) {

                strength.innerHTML =
                    '<span class="text-warning">Medium Password</span>';

            }

            else {

                strength.innerHTML =
                    '<span class="text-success">Strong Password</span>';

            }

        });

    }




    /*
    |--------------------------------------------------------------------------
    | Smooth Scroll
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {

        anchor.addEventListener("click", function (e) {

            let target = document.querySelector(this.getAttribute("href"));

            if (!target) return;

            e.preventDefault();

            target.scrollIntoView({

                behavior: "smooth"

            });

        });

    });




    /*
    |--------------------------------------------------------------------------
    | Scroll Reveal
    |--------------------------------------------------------------------------
    */

    const observer = new IntersectionObserver(entries => {

        entries.forEach(entry => {

            if (entry.isIntersecting) {

                entry.target.classList.add("fade-up");

            }

        });

    });

    document.querySelectorAll(".glass-card,.profile-card,.stats-box,.social-card")
        .forEach(item => {

            observer.observe(item);

        });




    /*
    |--------------------------------------------------------------------------
    | Floating Tilt Effect
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll(".profile-card").forEach(card => {

        card.addEventListener("mousemove", function (e) {

            let rect = this.getBoundingClientRect();

            let x = e.clientX - rect.left;

            let y = e.clientY - rect.top;

            let rotateX = (y / rect.height - .5) * -8;

            let rotateY = (x / rect.width - .5) * 8;

            this.style.transform =
                `perspective(900px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;

        });

        card.addEventListener("mouseleave", function () {

            this.style.transform =
                "perspective(900px) rotateX(0deg) rotateY(0deg)";

        });

    });




    /*
    |--------------------------------------------------------------------------
    | Live Clock
    |--------------------------------------------------------------------------
    */

    const clock = document.querySelector("#liveClock");

    if (clock) {

        setInterval(function () {

            let date = new Date();

            clock.innerHTML = date.toLocaleTimeString();

        }, 1000);

    }

});


// Live Clock

/*
|--------------------------------------------------------------------------
| Live Date
|--------------------------------------------------------------------------
*/

const liveDate = document.querySelector("#liveDate");

if (liveDate) {

    setInterval(function () {

        let date = new Date();

        liveDate.innerHTML = date.toLocaleDateString('en-IN', {

            day: '2-digit',

            month: 'short',

            year: 'numeric'

        });

    }, 1000);

}
