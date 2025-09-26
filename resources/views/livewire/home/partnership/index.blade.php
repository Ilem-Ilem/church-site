<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout};

new  #[Layout('components.layouts.layout')]  class extends Component {
    //
}; ?>

<div>

    <div class="d-flex flex-column min-vh-100">
        <!-- Main Content -->
        <main class="flex-grow-1">
            <div class="container-fluid px-4 px-sm-6 px-lg-8 py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <div class="d-flex flex-column gap-5">
                            <div class="position-relative w-100" style="height: 384px;">
                                <div class="w-100 h-100 rounded-lg shadow-lg overflow-hidden">
                                    <div class="w-100 h-100 bg-cover bg-center"
                                        style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBQgJsLm3cy4TkE1XuD8Bras0FFkONp001tRfZVehPEsSg9ZPsqMgfaQufhm0Byl752-P9BIPqU_R3JNThN5UfrxaRxTz3P-uTRJ_A3BKEdQRHEJ78mVpmsSGPy4qvoS04QFY82x6QrdIq87TMW8vCZsUZ_CN8xGhPxXYt50B1BQvSwFy_J8Qd2pA2Xl_zH-QFJsGZaQh5DeUEQqnhmXSU-cNHShZJminRImAARDNiudEJ48ElwWTsswtLJDnbLAcFUE4SQKGBXLjVs");'>
                                    </div>
                                    <div class="position-absolute top-0 start-0 w-100 h-100 dark-image-overlay"></div>
                                    <div class="position-absolute bottom-0 start-0 p-4 text-white">
                                        <h1 class="fs-2 fw-bold">Want to be a Partner?</h1>
                                        <p class="mt-2 text-white">Explore partnership opportunities and leverage our
                                            cutting-edge technology to drive mutual growth and success.</p>
                                        <button class="btn custom-primary text-white mt-3 rounded-lg fw-bold">Become a
                                            Partner</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card p-4 shadow-lg border">
                                <h2 class="fs-3 fw-bold">Location Payment Details</h2>
                                <p class="text-muted mt-2">Select a location to view details on supported payment
                                    methods, fees, and currencies.</p>
                                <div class="col-12 mt-3">
                                    <label class="form-label text-muted" for="conclave-location">Select Location</label>
                                    <select class="form-select rounded-lg" id="conclave-location"
                                        name="conclave-location">
                                        <option value="">Select a location</option>
                                        <option value="North America">North America</option>
                                        <option value="Europe">Europe</option>
                                        <option value="Asia-Pacific">Asia-Pacific</option>
                                        <option value="Latin America">Latin America</option>
                                        <option value="Middle East & Africa">Middle East & Africa</option>
                                    </select>
                                </div>
                                <div id="payment-details" class="mt-4">
                                    <div class="text-center text-muted">
                                        <p>Please select a location from the dropdown above to view payment information.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card p-4 shadow-lg border">
                            <h2 class="fs-3 fw-bold">Partnership Form</h2>
                            <p class="text-muted mt-2">Fill out the form below to get in touch with our partnerships
                                team.</p>
                            <form class="mt-4 row g-3">
                                <div class="col-12">
                                    <label class="form-label text-muted" for="company-name">Name</label>
                                    <input class="form-control rounded-lg" id="company-name" name="company-name"
                                        placeholder="Your name" type="text" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted" for="email">Email Address</label>
                                    <input class="form-control rounded-lg" id="email" name="email"
                                        placeholder="you@example.com" type="email" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted" for="phone">Phone Number</label>
                                    <input class="form-control rounded-lg" id="phone" name="number"
                                        placeholder="(123) 456-7890" type="number" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted" for="location">Preferred Location
                                        Conclave</label>
                                    <select class="form-select rounded-lg" id="location" name="location">
                                        <option>Select a Conclave</option>
                                        <option>North America</option>
                                        <option>Europe</option>
                                        <option>Asia-Pacific</option>
                                        <option>Latin America</option>
                                        <option>Middle East & Africa</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted" for="message">Briefly describe your
                                        partnership
                                        interests</label>
                                    <textarea class="form-control rounded-lg" id="message" name="message"
                                        placeholder="Tell us what you're looking for in a partnership." rows="4"></textarea>
                                </div>
                                <div class="col-12">
                                    <button class="btn custom-primary text-white w-100 rounded-lg fw-bold"
                                        type="submit">
                                        Submit Inquiry
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
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
                    <p>&copy; Copyright Doxa Commission Global <span id="current-year"></span> All Right Reserved.
                        Desinged and Developed by Doxa
                        database
                    </p>
                </div>

            </div>
            <script>
                let year = new Date().getFullYear()
                document.getElementById('current-year').innerHTML = year
            </script>

        </footer>
    </div>


    <script>
        const locationDropdown = document.getElementById('conclave-location');
        const paymentDetails = document.getElementById('payment-details');

        const paymentData = {
            "North America": {
                accountNumber: "987-654-321-0",
                accountName: "InnovateTech Holdings",
                bank: "Global Union Bank"
            },
            "Europe": {
                accountNumber: "EUR-123-456-789",
                accountName: "InnovateTech Europe",
                bank: "EuroLink Financial"
            },
            "Asia-Pacific": {
                accountNumber: "APAC-456-789-012",
                accountName: "InnovateTech Asia",
                bank: "Pacific Capital Bank"
            },
            "Latin America": {
                accountNumber: "LA-789-012-345",
                accountName: "InnovateTech LatAm",
                bank: "South American Trust"
            },
            "Middle East & Africa": {
                accountNumber: "MEA-012-345-678",
                accountName: "InnovateTech MEA",
                bank: "Desert Financial Services"
            }
        };

        locationDropdown.addEventListener('change', (event) => {
            const selectedLocation = event.target.value;
            if (selectedLocation) {
                const data = paymentData[selectedLocation];
                paymentDetails.innerHTML = `
                    <h5 class="fw-bold">Details for ${selectedLocation}:</h5>
                    <ul class="list-unstyled">
                        <li><strong>Account Number:</strong> ${data.accountNumber}</li>
                        <li><strong>Account Name:</strong> ${data.accountName}</li>
                        <li><strong>Bank:</strong> ${data.bank}</li>
                    </ul>
                `;
            } else {
                paymentDetails.innerHTML = `
                    <div class="text-center text-muted">
                        <p>Please select a location from the dropdown above to view payment information.</p>
                    </div>
                `;
            }
        });
    </script>

</div>
