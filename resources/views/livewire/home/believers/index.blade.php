<?php
// TODO: create the sign up for believers class
//TODO: create the notification to the team lead for the registration of  new user
// TODO:create the track my progress page

// create the class_table with columns('id', 'name', 'date', 'time')
// create the student class table with column('id', 'user_id', 'class_completed', 'status'. 'cert')
//TODO: create a student monitor dashboard for classes to allow student monitor their classes, nad give room to allow student request cert and request admin to allow them add a class like completed
//TODO: allow student to lay complain and take permission if they will be absent and the reason
//TODO: allow the team lead to accept the permission when needed or reject
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.layout')] class extends Component {
    //
}; ?>

<div>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-con {
            max-width: 800px;
            margin: 100px auto;
        }

        .section-title {
            font-size: 23px;
        }

        .navbar {
            background: linear-gradient(to right, #357be4, #294dc0) !important;
            height: 85px;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .status-box {
            padding: 1.5rem;
            border-radius: 0.5rem;
            border-left: 5px solid;
        }

        .status-box-open {
            border-color: #28a745;
            background-color: #e2f0e7;
        }

        .status-box-closed {
            border-color: #dc3545;
            background-color: #f8d7da;
        }

        .status-box-open .status-text {
            font-weight: bold;
            color: #28a745;
            margin-bottom: 0.5rem;
        }

        .status-box-closed .status-text {
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 0.5rem;
        }

        .link-text a {
            color: #007bff;
            text-decoration: underline;
        }

        .modal-body-scrollable {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>
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
    <div class="container card-con">
        <div class="card p-4 p-md-5">
            <h1 class="mb-3 fw-bold">Believers Academy</h1>
            <p class="lead">Our Believers Class is a foundational course designed for new converts and anyone seeking
                to
                deepen their understanding of core Christian doctrines. It's a welcoming environment where you can ask
                questions, grow in faith, and connect with other believers.</p>

            <p class="mt-4 fw-semibold">In this class, you will learn about:</p>
            <ul class="list-style-type-disc">
                <li>The fundamentals of salvation and new birth</li>
                <li>The person and work of the Holy Spirit</li>
                <li>The importance of prayer and Bible study</li>
                <li>Understanding the church and fellowship</li>
                <li>Living a victorious Christian life</li>
            </ul>

            <p class="mt-4">We believe that a strong foundation in these truths is essential for every believer to
                thrive in their walk with God.</p>

            <div class="status-box status-box-open mt-4">
                <p class="status-text mb-2">Status: Open for Registration!</p>
                <p class="link-text mb-0">New classes begin every first Sunday of the month. Click <a href="#"
                        class="text-decoration-underline text-primary fw-bold" data-bs-toggle="modal"
                        data-bs-target="#registrationModal">here</a> to register.</p>
            </div>
        </div>
    </div>

    <!-- Registration Modal -->
    <div class="modal fade modal-lg" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="section-title" id="registrationModalLabel">Believers Academy Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-scrollable">
                    <p>Please fill out the form below to register for our upcoming Believers Acedemy. We look forward to
                        welcoming you!</p>
                    <form>
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" placeholder="Your Full Name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email"
                                placeholder="your.email@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" placeholder="+234 801 234 5678">
                        </div>
                        <div class="mb-3">
                            <label for="howHeard" class="form-label">How did you hear about Doxa Church?</label>
                            <select class="form-select" id="howHeard">
                                <option selected disabled>Select an option</option>
                                <option>Friend or Family</option>
                                <option>Social Media</option>
                                <option>Website</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="questions" class="form-label">Any questions or specific areas of
                                interest?</label>
                            <textarea class="form-control" id="questions" rows="3"
                                placeholder="e.g., I'm interested in learning more about prayer."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>

</div>
