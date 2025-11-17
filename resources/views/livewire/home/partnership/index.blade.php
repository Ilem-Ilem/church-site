<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Rule, Comoputed};
use App\Models\Accounts;
use App\Models\Events;
use App\Models\Partnership;
use App\Models\Chapter;

new #[Layout('components.layouts.layout')] class extends Component {
    public $selected_chapter = '';
    public $name;
    public $email;
    public $phone;
    public $preferred_location;
    public $partnership_interests;
    public $organization;
    public $website;
    public $partnership_type;
    public $proposed_amount;
    public $conclaves;
    public $donationAccounts;

    public function mount()
    {
        $this->conclaves = Chapter::all();
        $this->donationAccounts = collect();
    }

    public function updatedSelectedChapter($value)
    {
        if ($value) {
            $this->donationAccounts = Accounts::where('account_type', 'donation')->where('chapter_id', $value)
            ->get();
        } else {
            $this->donationAccounts = collect();
        }
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'preferred_location' => 'nullable|string|max:255',
            'partnership_interests' => 'nullable|string',
            'organization' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'partnership_type' => 'nullable|in:financial,strategic,ministry,technology,other',
            'proposed_amount' => 'nullable|numeric|min:0',
        ]);

        Partnership::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'preferred_location' => $this->preferred_location,
            'partnership_interests' => $this->partnership_interests,
            'organization' => $this->organization,
            'website' => $this->website,
            'partnership_type' => $this->partnership_type,
            'proposed_amount' => $this->proposed_amount,
        ]);

        session()->flash('message', 'Partnership inquiry submitted successfully!');

        // Reset form
        $this->reset(['name', 'email', 'phone', 'preferred_location', 'partnership_interests', 'organization', 'website', 'partnership_type', 'proposed_amount']);
    }
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
                                    <label class="form-label text-muted" for="conclave-location">Select Conclave</label>
                                    <select class="form-select rounded-lg" id="conclave-location"
                                        name="conclave-location" wire:model.live="selected_chapter">
                                        <option value="">Select a Conclave</option>
                                        @foreach ($this->conclaves as $conclave)
                                            <option value="{{ $conclave->id }}">{{ $conclave->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="payment-details" class="mt-4">
                                    @if ($donationAccounts->isNotEmpty())
                                        <h5 class="fw-bold">Donation Accounts for
                                            {{ $this->conclaves->where('id', $this->selected_chapter)->first()->name ?? 'Selected Conclave' }}:
                                        </h5>
                                        @foreach ($donationAccounts as $account)
                                            <div class="card mb-3 p-3">
                                                <h6>{{ $account->account_name }}</h6>
                                                <ul class="list-unstyled">
                                                    <li><strong>Account Number:</strong>
                                                        {{ $account->formatted_account_number }}</li>
                                                    <li><strong>Bank:</strong> {{ $account->bank_name }}</li>
                                                    <li><strong>Currency:</strong> {{ $account->currency }}</li>
                                                    @if ($account->supported_payment_methods)
                                                        <li><strong>Supported Payment Methods:</strong>
                                                            {{ implode(', ', $account->supported_payment_methods) }}
                                                        </li>
                                                    @endif
                                                    @if ($account->minimum_amount)
                                                        <li><strong>Minimum Amount:</strong> {{ $account->currency }}
                                                            {{ number_format($account->minimum_amount, 2) }}</li>
                                                    @endif
                                                    @if ($account->maximum_amount)
                                                        <li><strong>Maximum Amount:</strong> {{ $account->currency }}
                                                            {{ number_format($account->maximum_amount, 2) }}</li>
                                                    @endif
                                                    @if ($account->special_instructions)
                                                        <li><strong>Special Instructions:</strong>
                                                            {{ $account->special_instructions }}</li>
                                                    @endif
                                                    <li><strong>Contact:</strong> {{ $account->contact_email }}</li>
                                                </ul>
                                            </div>
                                        @endforeach
                                    @elseif($this->selected_chapter)
                                        <div class="text-center text-muted">
                                            <p>No donation accounts available for this location.</p>
                                        </div>
                                    @else
                                        <div class="text-center text-muted">
                                            <p>Please select a location from the dropdown above to view payment
                                                information.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card p-4 shadow-lg border">
                            <h2 class="fs-3 fw-bold">Partnership Form</h2>
                            <p class="text-muted mt-2">Fill out the form below to get in touch with our partnerships
                                team.</p>
                            <form class="mt-4 row g-3" wire:submit.prevent="submit">
                                @if (session('message'))
                                    <div class="alert alert-success">
                                        {{ session('message') }}
                                    </div>
                                @endif
                                <div class="col-12">
                                    <label class="form-label text-muted" for="company-name">Name</label>
                                    <input class="form-control rounded-lg" id="company-name" name="company-name"
                                        placeholder="Your name" type="text" wire:model="name" />
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted" for="email">Email Address</label>
                                    <input class="form-control rounded-lg" id="email" name="email"
                                        placeholder="you@example.com" type="email" wire:model="email" />
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted" for="phone">Phone Number</label>
                                    <input class="form-control rounded-lg" id="phone" name="phone"
                                        placeholder="(123) 456-7890" type="text" wire:model="phone" />
                                    @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted" for="preferred_location">Preferred Location
                                        Conclave</label>
                                    <select class="form-select rounded-lg" id="preferred_location"
                                        name="preferred_location" wire:model="preferred_location">
                                        <option value="">Select a Conclave</option>
                                        @foreach ($this->conclaves as $conclave)
                                            <option value="{{ $conclave->name }}">{{ $conclave->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('preferred_location')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted" for="organization">Organization</label>
                                    <input class="form-control rounded-lg" id="organization" name="organization"
                                        placeholder="Your organization" type="text" wire:model="organization" />
                                    @error('organization')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted" for="website">Website</label>
                                    <input class="form-control rounded-lg" id="website" name="website"
                                        placeholder="https://yourwebsite.com" type="url" wire:model="website" />
                                    @error('website')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted" for="partnership_type">Partnership
                                        Type</label>
                                    <select class="form-select rounded-lg" id="partnership_type"
                                        name="partnership_type" wire:model="partnership_type">
                                        <option value="">Select Type</option>
                                        <option value="financial">Financial</option>
                                        <option value="strategic">Strategic</option>
                                        <option value="ministry">Ministry</option>
                                        <option value="technology">Technology</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('partnership_type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted" for="proposed_amount">Proposed Amount</label>
                                    <input class="form-control rounded-lg" id="proposed_amount"
                                        name="proposed_amount" placeholder="0.00" type="number" step="0.01"
                                        wire:model="proposed_amount" />
                                    @error('proposed_amount')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted" for="message">Briefly describe your
                                        partnership
                                        interests</label>
                                    <textarea class="form-control rounded-lg" id="message" name="message"
                                        placeholder="Tell us what you're looking for in a partnership." rows="4"
                                        wire:model="partnership_interests"></textarea>
                                    @error('partnership_interests')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
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
                            <li><a href="{{ route('home') }}" wire:navigate>Home</a></li>
                            <li><a href="{{ route('sermons.index') }}" wire:navigate>Messages</a></li>
                            <li><a href="{{ route('home') }}" wire:navigate>About</a></li>
                            <li><a href="{{ route('home') }}" wire:navigate>Location</a></li>
                            <li><a href="{{ route('believers.academy') }}" wire:navigate>Believers Class</a></li>
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




</div>
