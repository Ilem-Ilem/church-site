<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doxa commission Global</title>
    <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/vendor/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/vendor/aos/aos.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="/assets/vendor/aos/aos.js"></script>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @livewireStyles
    @vite(['resources/js/app.js'])
    <style>
        .main {
            width: 80%;
            margin: 0 auto;
        }

        @media (max-width:700px) {
            .main {
                width: 90%;
            }
        }

        .countdown-section {
            padding: 120px 30px;
        }
    </style>
</head>

<body style="background-color: rgba(238, 238, 238, 0.761);">
    <nav class="navbar navbar-expand-md custom-navbar fixed-top">
        <div class="container-fluid">
            <img src="/Img/doxa.PNG" alt="logo" class="logo">

            <!-- Large screen nav links -->
            <div class="collapse navbar-collapse d-none d-md-flex justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="sermon.html">Message</a></li>
                    <li class="nav-item"><a class="nav-link" href="about_us.html">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="cell.html">Cell</a></li>
                    <li class="nav-item"><a class="nav-link" href="event.html">Event</a></li>
                    <li class="nav-item"><a class="nav-link" href="map.html">Location</a></li>
                    <li class="nav-item"><a class="nav-link" href="belivers.html">Believers academy</a></li>
                    <li class="nav-item"><a class="nav-link" href="transport.html">Need a Ride</a></li>
                </ul>
            </div>

            <!-- Mobile toggle -->
            <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasNav" aria-controls="offcanvasNav">
                <i class="bi bi-list text-white" style="font-size: 1.8rem;"></i>
            </button>
        </div>
    </nav>
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.custom-navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
    <x-flash></x-flash>
    {{ $slot }}

    <footer class="main-footer">

        <div class="row mx-2">

            <div class="footer-content-con row mx-2">
                <div class="footer-content col-lg-3 col-md-6">
                    <!-- <img src="Img/doxa.PNG"> -->
                    <h2>Doxa commissin Global</h2>
                    <h4>Bringing nation into God's glory world</h3>
                        <div class="footer-info">
                            <p>
                                <i class="fa-solid fa-location-dot"></i>129 Goldie, Adjacent amika
                                utuk, Calabar, Cross River State, Nigeria.
                            </p>
                            <p><i class="fa-solid fa-phone"></i>+234 [Your Phone Number]</p>
                            <p>
                                <i class="fa-regular fa-envelope"></i>
                                <a href="mailto:info@doxachurch.org">info@doxachurch.org</a>
                            </p>
                        </div>
                </div>

                <div class="footer-content col-lg-3 col-md-6">
                    <h2>Sunday Glory Life service</h2>
                    <p>7am, 8:30am, 10am and 4pm</p>
                    <h4>Thursday Glory Experience</h4>
                    <p>5:30pm</p>
                </div>

                <div class="footer-content col-lg-3 col-md-6">
                    <h2>Quick Links</h2>
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="messages.html">Messages</a></li>
                        <li><a href="about.html">About</a></li>
                        <li><a href="map.html">Location</a></li>
                        <li><a href="believers_class.html">Believers Class</a></li>
                    </ul>
                </div>

                <div class="footer-content newsletter col-lg-3 col-md-6">
                    <h3 class=>Stay Connected</h3>
                    <p></p>Subscribe to our newsletter for updates and spiritual insights.</p>
                    <form id="newsletter-form">
                        <input type="email" id="newsletter-email" placeholder="Your email address" required>
                        <button type="submit">
                            Subscribe
                        </button>
                    </form>
                    <div class="footer-icons-wrapper">
                        <a href="https://www.facebook.com/DoxaCommissionGlobal/" aria-label="Facebook"><i
                                class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/doxa_commission/?hl=en" aria-label="Instagram"><i
                                class="fab fa-instagram"></i></a>
                        <a href="https://www.tiktok.com/discover/doxa-commission-global" aria-label="TikTok"><i
                                class="fab fa-tiktok"></i></a>
                        <a href="https://www.youtube.com/channel/UCZtReUAxK3S6qBKnV5G6PTw" aria-label="YouTube"><i
                                class="fab fa-youtube"></i></a>
                        <a href="#" aria-label="Spotify"><i class="fab fa-spotify"></i></a>
                    </div>
                    <p style="color: rgb(204, 198, 198); text-align: center; margin-top: 8px;">Follow us for update
                        and
                        inspiration!</p>
                </div>
            </div>

            <div class="break"></div>

            <div class="footer-2">
                <p>&copy; Copyright Doxa Commission Global <span id="current-year"></span> All Right Reserved. Desinged
                    and Developed by Doxa
                    database
                </p>
            </div>

        </div>
        <script>
            let year = new Date().getFullYear()
            document.getElementById('current-year').innerHTML = year
        </script>
        @livewireScripts
            @fluxScripts


    </footer>
