<?php include 'includes/header.php'; ?>
<div class="about-hero-section">
    <div class="about-hero-content">
        <h1>About Us</h1>
        <p>Welcome to the Mankraft Fitness Center, your partner in achieving health and wellness goals.</p>
    </div>
</div>

<div class="about-intro">
    <div class="intro-container">
        <!-- Mission Section -->
        <div class="mission">
            <h2>Our Mission</h2>
            <p>
                At Mankraft Fitness Center, we believe in empowering individuals to lead healthier, happier lives.
                Our mission is to make fitness accessible, enjoyable, and achievable for everyone.
            </p>
        </div>
        <!-- Vision Section -->
        <div class="vision">
            <h2>Our Vision</h2>
            <p>
                To become the leading fitness platform that inspires and empowers individuals worldwide to achieve their
                health and wellness goals. We envision a future where fitness is an integral part of everyone's lifestyle,
                supported by cutting-edge technology and a vibrant community.
            </p>
        </div>
    </div>
</div>

<div class="about-values">
    <h2>Why Choose Us?</h2>
    <div class="values-container">
        <div class="value-card">
            <i class="fas fa-dumbbell"></i>
            <h3>Top-Notch Equipment</h3>
            <p>We provide the latest gym equipment to help you achieve your fitness goals efficiently.</p>
        </div>
        <div class="value-card">
            <i class="fas fa-users"></i>
            <h3>Community Driven</h3>
            <p>Join a supportive community that motivates you to stay consistent on your fitness journey.</p>
        </div>
        <div class="value-card">
            <i class="fas fa-heartbeat"></i>
            <h3>Holistic Approach</h3>
            <p>We focus on both physical and mental well-being for a balanced and fulfilling lifestyle.</p>
        </div>
    </div>
<div class="about-team">
    <h2>Meet Our Team</h2>
    <div class="team-container">
        <div class="team-card">
            <img src="images/team1.jpg" alt="Trainer 1">
            <h3>Ahmad Solehin</h3>
            <p>Certified Personal Trainer</p>
        </div>
        <div class="team-card">
            <img src="images/team2.jpg" alt="Trainer 2">
            <h3>Azfar Harith</h3>
            <p>Yoga & Pilates Expert</p>
        </div>
        <div class="team-card">
            <img src="images/team3.jpg" alt="Trainer 3">
            <h3>Hafizuddin Raemee</h3>
            <p>Strength & Conditioning Coach</p>
        </div>
        <div class="team-card">
            <img src="images/team4.jpg" alt="Trainer 4">
            <h3>Fauzi Halim</h3>
            <p>Strength & Conditioning Coach</p>
        </div>
        <div class="team-card">
            <img src="images/team5.jpg" alt="Trainer 5">
            <h3>Meor Amir</h3>
            <p>Nutrition Specialist</p>
        </div>
    </div>
</div>

<div class="about-testimonials">
    <h2>What Our Customers Say</h2>
    <div class="testimonial-wrapper">
        <!-- Original Testimonials -->
        <div class="testimonial-card">
            <p>"The Mankraft Fitness Center has completely transformed my fitness journey. The trainers are amazing and the community is so supportive!"</p>
            <div class="customer-name">- Khairul Akashah</div>
        </div>
        <div class="testimonial-card">
            <p>"I love the variety of equipment and classes available. It's the best gym experience I've ever had."</p>
            <div class="customer-name">- Farah Wardina</div>
        </div>
        <div class="testimonial-card">
            <p>"Joining this gym was the best decision I've made for my health. The holistic approach really works for me."</p>
            <div class="customer-name">- Adib Fikri</div>
        </div>
        <div class="testimonial-card">
            <p>"A truly exceptional experience. The trainers are dedicated and the environment is perfect for achieving my fitness goals."</p>
            <div class="customer-name">- Asyraaf</div>
        </div>
        <div class="testimonial-card">
            <p>"The support from the staff and trainers is unmatched. They genuinely care about your progress and well-being."</p>
            <div class="customer-name">- Syed Ilham</div>
        </div>
        <div class="testimonial-card">
            <p>"The Mankraft Fitness Center has completely transformed my fitness journey. The trainers are amazing and the community is so supportive!"</p>
            <div class="customer-name">- Paan</div>
        </div>
        <div class="testimonial-card">
            <p>"I love the variety of equipment and classes available. It's the best gym experience I've ever had."</p>
            <div class="customer-name">- Ipan</div>
        </div>
        <div class="testimonial-card">
            <p>"Joining this gym was the best decision I've made for my health. The holistic approach really works for me."</p>
            <div class="customer-name">- Amri</div>
        </div>
        <div class="testimonial-card">
            <p>"A truly exceptional experience. The trainers are dedicated and the environment is perfect for achieving my fitness goals."</p>
            <div class="customer-name">- Wan</div>
        </div>
        <div class="testimonial-card">
            <p>"The support from the staff and trainers is unmatched. They genuinely care about your progress and well-being."</p>
            <div class="customer-name">- Najmi</div>
        </div>
    </div>
</div>

<!-- Scroll Up Button -->
<button id="scrollUpBtn" title="Go to top">
    <i class="fas fa-chevron-up"></i> <!-- Font Awesome icon -->
</button>
<div class="about-footer">
    <h2>Ready to Join Us?</h2>
    <p>Take the first step toward your fitness journey today. Join our community and achieve your goals!</p>
    <a href="auth/register.php" class="btn-primary">Sign Up Now</a>
</div>
<script>
    const testimonialWrapper = document.querySelector('.testimonial-wrapper');
    testimonialWrapper.addEventListener('mouseenter', () => {
        testimonialWrapper.style.animationPlayState = 'paused';
    });
    testimonialWrapper.addEventListener('mouseleave', () => {
        testimonialWrapper.style.animationPlayState = 'running';
    });
</script>
<script src="js/scrollUpButton.js"></script>
<script src="js/main.js"></script> <!-- External JavaScript -->

<?php include 'includes/footer.php'; ?>
