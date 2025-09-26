<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout};
use App\MOdels\{LandingPageSetting};

new #[Layout('components.layouts.layout')] class extends Component {
    public $landing;

    public $carousels;

    public function mount()
    {
        $this->landing = LandingPageSetting::first();

        $this->carousels = $this->landing->carousel;
    }
}; ?>

<div>

    <!-- Offcanvas menu -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-white" id="offcanvasNavLabel">Doxa Commission Global</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav">
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="sermon.html">Message</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="about_us.html">About</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="cell.html">Cell</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="event.html">Event</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="map.html">Location</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="belivers.html">Believers academy</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="transport.html">Need a Ride</a></li>
                </div>

            </ul>
        </div>
    </div>



    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-inner">
            @foreach ($carousels as $index => $carousel)
                @php
                    $carousel_image = $carousel['image'];
                @endphp
                <div class="carousel-item @if($index == 0)
                    active
                @endif"
                    style="background-image: url('@if ($carousel_image) {{ asset("/storage/$carousel_image") }}@else
                {{ asset('/Img/IMG-20250101-WA0021.jpg') }} @endif');">
                    <div class="carousel-caption">
                        <h1>{{ $carousel['title'] }}</h1>
                        <p>{{ $carousel['subtitle'] }}</p>
                    </div>
                </div>
            @endforeach


        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">Previous</span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <script>
        const carousel = document.querySelector('#heroCarousel');
        const carouselInstance = new bootstrap.Carousel(carousel, {
            interval: 3000,
            ride: 'carousel',
            wrap: true
        });

        carousel.addEventListener('mouseenter', () => carouselInstance.pause());
        carousel.addEventListener('mouseleave', () => carouselInstance.cycle());
    </script>

    <div class=" mt-4 main">
        <!-- -----contdown------- -->
        <section class="countdown-section mt-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="service-cards">
                            <div class="section-title">Sunday Service</div>
                            <div id="sunday-countdown" class="countdown text-center"></div>
                            <p class="mt-3 text-center">Sundays: 7:00AM, 8:30AM, 10:00AM & 4:00 PM</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="service-cards">
                            <div class="section-title">Thursday Service</div>
                            <div id="thursday-countdown" class="countdown text-center"></div>
                            <p class="mt-3 text-center">Thursdays @ 5:30 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Doxa Related News / Special Service Announcements -->
        <section class="mt-5">
            <h2 class="section-title">Doxa News & Special Services</h2>
            <div class="news-grid">
                <!-- News Item 1 -->
                <div class="news-item">
                    <h3>Thanksgiving Service Announced!</h3>
                    <p>
                        Join us for a special Thanksgiving Service on [Date] at [Time].
                        Come with a heart of gratitude!
                    </p>
                    <a href="#" class="read-more-link">Read More</a>
                </div>
                <!-- News Item 2 -->
                <div class="news-item">
                    <h3>PDS (Prophetic Deliverance Service)</h3>
                    <p>
                        Prepare for a powerful Prophetic Deliverance Service on [Date] at
                        [Time]. Don't miss out!
                    </p>
                    <a href="#" class="read-more-link">Read More</a>
                </div>
                <!-- News Item 3 -->
                <div class="news-item">
                    <h3>New Sermon Series: "Faith Unlocked"</h3>
                    <p>
                        Starting next Sunday, dive deep into our new series on unlocking
                        your faith. Be there!
                    </p>
                    <a href="#" class="read-more-link">Read More</a>
                </div>
            </div>
        </section>

        <!-- GET CONNECTED -->
        <header class="page-header text-center mt-5">
            <div class="container">
                <h1 class="display-6">Get Connected</h1>
                <p class="mt-2">Take your next step in faith and community with these quick actions</p>
            </div>
        </header>
        <!-- GET CONNECTED GRID -->
        <main class="container pb-4">
            <div class="row gc-grid">
                <!-- Book Appointment -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="gc-card h-100 d-flex flex-column">
                        <div>
                            <div class="d-flex align-items-start gap-3">
                                <div class="gc-icon"><i class="bi bi-calendar2-check"></i></div>
                                <div>
                                    <div class="gc-title">Book Appointment</div>
                                    <div class="gc-desc">Schedule time with our pastor or counselors</div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('appointment') }}" class="btn btn-brand w-100 py-2" wire:navigate>Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prayer Request -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="gc-card h-100 d-flex flex-column">
                        <div class="d-flex align-items-start gap-3">
                            <div class="gc-icon" style="background:#fff2f2; color:#ef4444; border-color:#ffc9c9"><i
                                    class="bi bi-heart" aria-hidden="true"></i></div>
                            <div>
                                <div class="gc-title">Prayer Request</div>
                                <div class="gc-desc">Submit your prayer needs to our community</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('prayer.request') }}" class="btn btn-brand w-100 py-2" wire:navigate>Submit Request</a>
                        </div>
                    </div>
                </div>

                <!-- Event Sign-Up (Coming Soon) -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="gc-card h-100 d-flex flex-column">
                        <div class="d-flex align-items-start gap-3">
                            <div class="gc-icon" style="background:#eef7ff; color:#1d4ed8; border-color:#cfe3ff"><i
                                    class="bi bi-calendar-event"></i></div>
                            <div>
                                <div class="gc-title">Event Sign-Up</div>
                                <div class="gc-desc">Register for upcoming conferences and retreats</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-ghost w-100 py-2" disabled>Coming Soon</button>
                        </div>
                    </div>
                </div>

                <!-- Believers' Class (Open badge) -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="gc-card h-100 d-flex flex-column">
                        <div class="d-flex align-items-start gap-3">
                            <div class="gc-icon" style="background:#eefcf3; color:#16a34a; border-color:#c7f0d7"><i
                                    class="bi bi-mortarboard"></i></div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="gc-title mb-0">Believers' Class</div>
                                    <span class="badge rounded-pill badge-open">Open</span>
                                </div>
                                <div class="gc-desc">Join our faith development program</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('believers.academy') }}" class="btn btn-brand w-100 py-2">Learn More</a>
                        </div>
                    </div>
                </div>

                <!-- Partnership Inquiry -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="gc-card h-100 d-flex flex-column">
                        <div class="d-flex align-items-start gap-3">
                            <div class="gc-icon" style="background:#f4f1ff; color:#6d28d9; border-color:#e3dbff"><i
                                    class="bi bi-people"></i></div>
                            <div>
                                <div class="gc-title">Partnership Inquiry</div>
                                <div class="gc-desc">Explore partnership opportunities and learn more on being a
                                    partner
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('home.partnership.index', request()->query()) }}" class="btn btn-brand w-100 py-2">Apply Now</a>
                        </div>
                    </div>
                </div>

                <!-- Watch Previous Services -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="gc-card h-100 d-flex flex-column">
                        <div class="d-flex align-items-start gap-3">
                            <div class="gc-icon" style="background:#f0f9ff; color:#0ea5e9; border-color:#cfefff"><i
                                    class="bi bi-play-circle"></i></div>
                            <div>
                                <div class="gc-title">Watch Previous Services</div>
                                <div class="gc-desc">Catch up on recent worship services</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="#" class="btn btn-brand w-100 py-2">Watch Now</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-end"></div>
        </main>

        <!-- Testimonies Section (Kept on Home as it's a common feature) -->
        <section class="mt-5">
            <h2 class="section-title">Inspiring Testimonies</h2>
            <div class="testimony-grid">
                <!-- Testimony 1 -->
                <div class="testimony-item">
                    <p>
                        "God healed me miraculously from a chronic illness after attending
                        the healing service. I am forever grateful!"
                    </p>
                    <p class="author">- Sister Joy Emmanuel</p>
                </div>
                <!-- Testimony 2 -->
                <div class="testimony-item">
                    <p>
                        "Through the teachings at Doxa Church, my business experienced
                        unprecedented growth. To God be the glory!"
                    </p>
                    <p class="author">- Brother David Okoro</p>
                </div>
                <!-- Add more testimonies as needed -->
            </div>
            <div class="share-testimony-wrapper">
                <p class="victory-report-text"></p>
                <!-- Modal trigger button -->
                <button type="button" class="share-testimony-button" data-bs-toggle="modal"
                    data-bs-target="#modalId">
                    Share your testiomny
                </button>
            </div>
        </section>

        <!-- -----CONTACT US/SEND MESSAGE -->
        <section class=" mt-5 mb-3">
            <div class="card p-3">
                <div class="row">
                    <div class="col-lg-6">
                        <section id="contact">
                            <h2 class="section-title">Contact Us</h2>
                            <div class="contact-grid">
                                <div class="contact-info">
                                    <p>
                                        <i class="fa-solid fa-location-dot"></i>129 Goldie, Adjacent amika
                                        utuk, Calabar, Cross River State, Nigeria.
                                    </p>
                                    <p><i class="fa-solid fa-phone"></i>+234 [Your Phone Number]</p>
                                    <p>
                                        <i class="fa-regular fa-envelope"></i>
                                        <a href="mailto:info@doxachurch.org">info@doxachurch.org</a>
                                    </p>
                                    <p><strong>Service Times:</strong></p>
                                    <ul class="service-times-list">
                                        <p><b>Sunday we hold four Glory Life services</b></p>
                                        <li><b>Sundays:</b> 7am, 8:30am, 10am and 4pm</li>
                                        <p><b>Glory Exprience</b></p>
                                        <li><b>Thursday:</b> 5:30 PM</li>
                                    </ul>
                                </div>
                                <div class="contact-form-wrapper" id="message">
                                </div>

                            </div>
                    </div>
                    <div class="col-lg-6">
                        <h3>Send Us a Message</h3>
                        <form action="">
                            <div class="row">
                                <label for="" class="form-label">Name:</label>
                                <div>
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control " name="formId1" id="formId1"
                                            placeholder="" />
                                        <label for="formId1">Name</label>
                                    </div>

                                </div>
                                <label for="" class="form-label">Email:</label>
                                <div>
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control " name="formId1" id="formId1"
                                            placeholder="" />
                                        <label for="formId1">Email</label>
                                    </div>

                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Message</label>
                                <textarea class="form-control" name="" id="" rows="3"></textarea>
                            </div>
                            <input type="submit" value="contact" class="btn btn-sm"
                                style="background: var(--accent-color); color: white;">
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- All the modals  -->
    <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title section-title" id="modalTitleId">
                        Victory Report Form
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="">
                        <div class="row">
                            <div>
                                <label for="name" class="form-label">Your Name (Optional)</label>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control " name="formId1" id="formId1"
                                        placeholder="" />
                                    <label for="formId1">Name</label>
                                </div>

                            </div>
                            <div>
                                <label for="Emai" class="form-label">Your Email</label>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="formId1" id="formId1"
                                        placeholder="" />
                                    <label for="formId1">Email</label>
                                </div>

                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="testimony" class="form-label">testimony</label>
                            <textarea class="form-control" name="" id="" rows="3"></textarea>
                        </div>
                        <!-- <div class="mb-3">
                            <img src="" alt="" id="test-image">
                            <input type="file" name="image" id="upload" accept="image/*">
                        </div> -->
                        <div class="mb-3">
                            <label for="" class="form-label">Choose file(optional)</label>
                            <input type="file" class="form-control" name="" id="" placeholder=""
                                aria-describedby="fileHelpId" />
                            <div id="fileHelpId" class="form-text">Upload image (e.g Before and after, Doctor report
                                etc)</div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Submit Testimony</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Optional: Place to the bottom of scripts -->
    <script>
        const myModal = new bootstrap.Modal(
            document.getElementById("modalId"),
            // opti/ons,
        );
    </script>

    @fluxScripts

    <!-- COUNTDOWN -->
    <script>
        function getNextSundayService() {
            const now = new Date();
            const sundayMorning = new Date();
            sundayMorning.setDate(now.getDate() + ((0 + 7 - now.getDay()) % 7));
            sundayMorning.setHours(8, 0, 0, 0);
            const sundayEvening = new Date(sundayMorning);
            sundayEvening.setHours(16, 0, 0, 0);

            if (now < sundayMorning) return sundayMorning;
            if (now >= sundayMorning && now < new Date(sundayMorning.getTime() + 5 * 60 * 60 * 1000)) {
                // Service ongoing until 1 PM, show countdown to evening
                return sundayEvening;
            }
            if (now < sundayEvening) return sundayEvening;

            // After evening service, go to next week's morning
            sundayMorning.setDate(sundayMorning.getDate() + 7);
            return sundayMorning;
        }

        function getNextOccurrence(dayOfWeek, hour, minute) {
            const now = new Date();
            let result = new Date();
            result.setDate(now.getDate() + ((dayOfWeek + 7 - now.getDay()) % 7));
            result.setHours(hour, minute, 0, 0);
            if (result < now) result.setDate(result.getDate() + 7);
            return result;
        }

        function updateCountdown(elementId, targetDate) {
            const now = new Date().getTime();
            const distance = targetDate - now;
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            document.getElementById(elementId).textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }

        function startCountdowns() {
            setInterval(() => {
                updateCountdown('sunday-countdown', getNextSundayService());
                updateCountdown('thursday-countdown', getNextOccurrence(4, 17, 30));
            }, 1000);
        }

        startCountdowns();
    </script>
    </body>

    </html>
</div>
