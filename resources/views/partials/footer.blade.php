<footer class="bg-dark text-white mt-5 py-5 flex-shrink-0">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="fw-bold mb-3">Doxa Commission Global</h5>
                <p class="text-muted">Your trusted place of worship and spiritual growth.</p>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="fw-bold mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('home') }}" class="text-muted text-decoration-none">Home</a></li>
                    <li><a href="{{ route('sermons.index') }}" class="text-muted text-decoration-none">Messages</a></li>
                    <li><a href="{{ route('events.index') }}" class="text-muted text-decoration-none">Events</a></li>
                    <li><a href="{{ route('believers.academy') }}" class="text-muted text-decoration-none">Believers Academy</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="fw-bold mb-3">Contact Us</h5>
                <p class="text-muted mb-2"><i class="fas fa-map-marker-alt me-2"></i>123 Church Street, City</p>
                <p class="text-muted mb-2"><i class="fas fa-phone me-2"></i>+234 XXX XXX XXXX</p>
                <p class="text-muted"><i class="fas fa-envelope me-2"></i>info@doxacommission.org</p>
            </div>
        </div>
        <hr class="bg-secondary my-4">
        <div class="row">
            <div class="col-md-6">
                <p class="text-muted mb-0">&copy; {{ date('Y') }} Doxa Commission Global. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-muted text-decoration-none me-3"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-muted text-decoration-none me-3"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-muted text-decoration-none"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
</footer>
