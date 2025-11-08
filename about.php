<?php
// Database connection
$host = 'localhost';
$dbname = 'u339369243_cws_legal_db';
$username = 'u339369243_cws_legal_user';
$password = 'CWS_Legal_2024!';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Fallback to default values if database connection fails
    $pdo = null;
}

// Get site settings
$settings = [
    'site_name' => 'CWS Legal Chambers',
    'site_phone' => '+33 757934452',
    'site_email' => 'info@cwslegalchambers.com',
    'site_address' => '106 Boulevard Haussmann, Paris 75008, France'
];

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM site_settings LIMIT 1");
        $db_settings = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($db_settings) {
            $settings = array_merge($settings, $db_settings);
        }
    } catch (Exception $e) {
        // Use default settings
    }
}

// About content
$about_content = [
    'mission' => 'To provide exceptional legal representation while maintaining the highest standards of integrity, professionalism, and client service.',
    'vision' => 'To be the premier law firm in Paris, recognized for our unwavering commitment to justice and our clients\' success.',
    'values' => [
        'Integrity' => 'We maintain the highest ethical standards in all our dealings.',
        'Excellence' => 'We strive for excellence in every case and every client interaction.',
        'Compassion' => 'We understand that legal issues can be stressful and provide compassionate support.',
        'Results' => 'We are committed to achieving the best possible outcomes for our clients.'
    ],
    'history' => 'Founded in 1999, CWS Legal Chambers has grown from a small practice to one of Paris\' most respected law firms. Our founder, John Ward, started the firm with a simple mission: to provide exceptional legal representation to those who need it most.',
    'achievements' => [
        'Over $500 million recovered for clients',
        'More than 10,000 successful cases',
        '90% success rate across all practice areas',
        '25+ years of dedicated service',
        'AV Preeminent rating from Martindale-Hubbell',
        'Super Lawyers recognition for multiple attorneys'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?= htmlspecialchars($settings['site_name']) ?></title>
    <meta name="description" content="Learn about CWS Legal Chambers - Paris' premier law firm with over 25 years of excellence. Discover our mission, values, and commitment to justice.">
    <meta name="keywords" content="about us, law firm history, legal team, mission, values, CWS Legal Chambers, Paris law firm">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --navy: #002147;
            --gold: #ffd700;
            --white: #ffffff;
            --gray-light: #f8f9fa;
            --gray: #6c757d;
            --gray-dark: #343a40;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: var(--gray-dark);
            background: var(--white);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        .header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-main {
            padding: 15px 0;
        }
        
        .header-main .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo img {
            height: 50px;
            width: auto;
        }
        
        .nav {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        
        .nav a {
            color: var(--gray-dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .nav a:hover {
            color: var(--navy);
        }
        
        .btn-consultation {
            background: var(--navy);
            color: var(--white);
            padding: 12px 24px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-consultation:hover {
            background: #003366;
            transform: translateY(-2px);
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .search-container {
            position: relative;
        }
        
        .search-icon {
            color: var(--gray-dark);
            font-size: 18px;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .search-icon:hover {
            color: var(--navy);
        }
        
        .search-box {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--white);
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: none;
            z-index: 1000;
            min-width: 250px;
        }
        
        .search-box.active {
            display: block;
        }
        
        .search-box input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
        }
        
        /* Hamburger Menu */
        .hamburger-menu {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
        }
        
        .hamburger-menu span {
            width: 25px;
            height: 3px;
            background: var(--navy);
            margin: 3px 0;
            transition: all 0.3s;
            border-radius: 2px;
        }
        
        .hamburger-menu.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }
        
        .hamburger-menu.active span:nth-child(2) {
            opacity: 0;
        }
        
        .hamburger-menu.active span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--navy) 0%, #003366 100%);
            color: var(--white);
            padding: 100px 0;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 3.5em;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .hero p {
            font-size: 1.3em;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .hero-stat {
            text-align: center;
        }
        
        .hero-stat-number-container {
            display: flex;
            align-items: baseline;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .hero-stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: var(--gold);
            line-height: 1;
            transition: all 0.3s ease;
        }
        
        .hero-stat-symbol {
            font-size: 1.8em;
            font-weight: 600;
            color: var(--gold);
            margin-left: 5px;
            line-height: 1;
        }
        
        .hero-stat-label {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        /* About Section */
        .about-section {
            padding: 80px 0;
            background: var(--white);
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title {
            font-size: 2.5em;
            color: var(--navy);
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .section-subtitle {
            font-size: 1.2em;
            color: var(--gray);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 80px;
            align-items: center;
        }
        
        .about-text h3 {
            font-size: 1.8em;
            color: var(--navy);
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .about-text p {
            color: var(--gray-dark);
            margin-bottom: 20px;
            line-height: 1.8;
            font-size: 1.1em;
        }
        
        .about-image {
            text-align: center;
        }
        
        .about-image img {
            width: 100%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        /* Mission & Values Section */
        .mission-values {
            background: var(--gray-light);
            padding: 80px 0;
        }
        
        .mission-values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }
        
        .mission-card {
            background: var(--white);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .mission-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .mission-card h3 {
            color: var(--navy);
            font-size: 1.5em;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .mission-card p {
            color: var(--gray-dark);
            line-height: 1.6;
            font-size: 1.1em;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .value-card {
            background: var(--white);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
            border-top: 5px solid var(--gold);
        }
        
        .value-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .value-icon {
            width: 60px;
            height: 60px;
            background: var(--navy);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            color: var(--white);
            font-size: 1.5em;
        }
        
        .value-card h4 {
            color: var(--navy);
            font-size: 1.3em;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .value-card p {
            color: var(--gray-dark);
            line-height: 1.6;
        }
        
        /* Achievements Section */
        .achievements {
            padding: 80px 0;
            background: var(--white);
        }
        
        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .achievement-card {
            background: linear-gradient(135deg, var(--navy) 0%, #003366 100%);
            color: var(--white);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .achievement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .achievement-icon {
            font-size: 2.5em;
            color: var(--gold);
            margin-bottom: 20px;
        }
        
        .achievement-card h4 {
            font-size: 1.2em;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .achievement-card p {
            opacity: 0.9;
            line-height: 1.6;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--navy) 0%, #003366 100%);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }
        
        .cta-content h2 {
            font-size: 2.5em;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .cta-content p {
            font-size: 1.2em;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background: var(--gold);
            color: var(--navy);
            padding: 15px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #e6c200;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--white);
            border: 2px solid var(--white);
            padding: 15px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: var(--white);
            color: var(--navy);
        }
        
        /* Footer */
        .footer {
            background: var(--navy);
            color: var(--white);
            padding: 40px 0;
            text-align: center;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .footer-section h3 {
            color: var(--gold);
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .footer-section p,
        .footer-section a {
            color: var(--white);
            text-decoration: none;
            margin-bottom: 10px;
            display: block;
        }
        
        .footer-section a:hover {
            color: var(--gold);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            margin-top: 20px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header-main .container {
                padding: 0 15px;
            }
            
            .hamburger-menu {
                display: flex;
            }
            
            .nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--white);
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                flex-direction: column;
                padding: 20px;
                gap: 10px;
                z-index: 1000;
                max-height: 80vh;
                overflow-y: auto;
            }
            
            .nav.active {
                display: flex;
            }
            
            .nav a {
                padding: 12px 15px;
                border-bottom: 1px solid #eee;
                text-align: center;
                font-size: 16px;
                font-weight: 500;
                color: var(--navy);
                transition: all 0.3s ease;
            }
            
            .nav a:hover {
                background: var(--gray-light);
                color: var(--navy);
            }
            
            .nav a:last-child {
                border-bottom: none;
                margin-top: 10px;
                background: var(--navy);
                color: var(--white);
                border-radius: 5px;
                font-weight: 600;
            }
            
            .nav a:last-child:hover {
                background: #003366;
                color: var(--white);
            }
            
            .header-right {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .hero h1 {
                font-size: 2.5em;
            }
            
            .hero-stats {
                gap: 30px;
            }
            
            .about-content {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            
            .mission-values-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .values-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .achievements-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-primary,
            .btn-secondary {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-main">
            <div class="container">
                <a href="index.php" class="logo">
                    <img src="images/logos/logo-main.svg" alt="<?= htmlspecialchars($settings['site_name']) ?>">
                </a>
                <nav class="nav" id="main-nav">
                    <a href="index.php">Home</a>
                    <a href="services.php">Services</a>
                    <a href="about.php">About</a>
                    <a href="team.php">Our Team</a>
                    <a href="case-results.php">Case Results</a>
                    <a href="testimonials.php">Testimonials</a>
                    <a href="faq.php">FAQ</a>
                    <a href="blog.php">Blog</a>
                    <a href="contact.php">Contact</a>
                    <a href="tel:<?= htmlspecialchars($settings['site_phone']) ?>" class="btn-consultation">Free Consultation</a>
                </nav>
                <div class="header-right">
                    <div class="search-container">
                        <div class="search-icon" onclick="toggleSearch()">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="search-box" id="search-box">
                            <input type="text" placeholder="Search..." id="search-input">
                        </div>
                    </div>
                    <button class="hamburger-menu" id="hamburger-menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>About CWS Legal Chambers</h1>
            <p>For over 25 years, we have been Paris' premier law firm, dedicated to fighting for justice and protecting our clients' rights.</p>
            
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-number-container">
                        <span class="hero-stat-number" data-target="25">0</span>
                        <span class="hero-stat-symbol">+</span>
                    </div>
                    <span class="hero-stat-label">Years Experience</span>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number-container">
                        <span class="hero-stat-number" data-target="10000">0</span>
                        <span class="hero-stat-symbol">+</span>
                    </div>
                    <span class="hero-stat-label">Cases Won</span>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number-container">
                        <span class="hero-stat-number" data-target="500">0</span>
                        <span class="hero-stat-symbol">M+</span>
                    </div>
                    <span class="hero-stat-label">Recovered</span>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number-container">
                        <span class="hero-stat-number" data-target="90">0</span>
                        <span class="hero-stat-symbol">%</span>
                    </div>
                    <span class="hero-stat-label">Success Rate</span>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Story</h2>
                <p class="section-subtitle">From humble beginnings to becoming one of Paris' most respected law firms</p>
            </div>
            
            <div class="about-content">
                <div class="about-text">
                    <h3>Founded on Principles of Justice</h3>
                    <p><?= htmlspecialchars($about_content['history']) ?></p>
                    <p>Today, CWS Legal Chambers stands as a testament to what can be achieved when legal expertise meets unwavering dedication to client success. Our team of experienced attorneys continues to uphold the highest standards of legal practice while maintaining the personal touch that has defined our firm from the beginning.</p>
                    <p>We believe that everyone deserves access to exceptional legal representation, regardless of their circumstances. This belief drives everything we do and has helped us build lasting relationships with clients who trust us with their most important legal matters.</p>
                </div>
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=500&h=400&fit=crop" alt="CWS Legal Chambers Office">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Values Section -->
    <section class="mission-values">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Mission & Values</h2>
                <p class="section-subtitle">The principles that guide everything we do</p>
            </div>
            
            <div class="mission-values-grid">
                <div class="mission-card">
                    <h3>Our Mission</h3>
                    <p><?= htmlspecialchars($about_content['mission']) ?></p>
                </div>
                <div class="mission-card">
                    <h3>Our Vision</h3>
                    <p><?= htmlspecialchars($about_content['vision']) ?></p>
                </div>
            </div>
            
            <div class="values-grid">
                <?php foreach ($about_content['values'] as $value => $description): ?>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-<?= strtolower(str_replace(' ', '-', $value)) === 'integrity' ? 'shield-alt' : (strtolower(str_replace(' ', '-', $value)) === 'excellence' ? 'star' : (strtolower(str_replace(' ', '-', $value)) === 'compassion' ? 'heart' : 'trophy')) ?>"></i>
                    </div>
                    <h4><?= htmlspecialchars($value) ?></h4>
                    <p><?= htmlspecialchars($description) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Achievements Section -->
    <section class="achievements">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Achievements</h2>
                <p class="section-subtitle">Recognition and results that speak to our commitment to excellence</p>
            </div>
            
            <div class="achievements-grid">
                <?php foreach ($about_content['achievements'] as $achievement): ?>
                <div class="achievement-card">
                    <div class="achievement-icon">
                        <i class="fas fa-<?= strpos($achievement, 'million') !== false ? 'dollar-sign' : (strpos($achievement, 'cases') !== false ? 'gavel' : (strpos($achievement, 'success') !== false ? 'chart-line' : (strpos($achievement, 'years') !== false ? 'calendar-alt' : (strpos($achievement, 'rating') !== false ? 'star' : 'award')))) ?>"></i>
                    </div>
                    <h4><?= htmlspecialchars($achievement) ?></h4>
                    <p>Recognition of our commitment to excellence and client success</p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Work With Us?</h2>
                <p>Experience the difference that 25+ years of legal excellence can make for your case. Contact us today for a free consultation.</p>
                
                <div class="cta-buttons">
                    <a href="contact.php" class="btn-primary">Get Free Consultation</a>
                    <a href="team.php" class="btn-secondary">Meet Our Team</a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Animated counter functionality
        function animateCounter(element, target, duration = 2000) {
            const start = 0;
            const increment = target / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 16);
        }
        
        // Intersection Observer for counter animation
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px 0px -100px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = parseInt(entry.target.getAttribute('data-target'));
                    if (target && !entry.target.classList.contains('animated')) {
                        entry.target.classList.add('animated');
                        animateCounter(entry.target, target);
                    }
                }
            });
        }, observerOptions);
        
        // Observe all counter elements
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.hero-stat-number');
            counters.forEach(counter => {
                observer.observe(counter);
            });
        });
        
        // Search functionality
        function toggleSearch() {
            const searchBox = document.getElementById('search-box');
            const searchInput = document.getElementById('search-input');
            
            if (searchBox.classList.contains('active')) {
                searchBox.classList.remove('active');
                searchInput.blur();
            } else {
                searchBox.classList.add('active');
                searchInput.focus();
            }
        }
        
        // Close search when clicking outside
        document.addEventListener('click', function(event) {
            const searchContainer = document.querySelector('.search-container');
            const searchBox = document.getElementById('search-box');
            
            if (!searchContainer.contains(event.target)) {
                searchBox.classList.remove('active');
            }
        });
        
        // Search functionality
        document.getElementById('search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value.trim();
                if (searchTerm) {
                    // Simple search functionality - you can enhance this
                    window.location.href = 'index.php?search=' + encodeURIComponent(searchTerm);
                }
            }
        });
        
        // Hamburger menu functionality
        document.getElementById('hamburger-menu').addEventListener('click', function() {
            const nav = document.getElementById('main-nav');
            const hamburger = this;
            
            nav.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
        
        // Close mobile menu when clicking on a link
        document.querySelectorAll('.nav a').forEach(link => {
            link.addEventListener('click', function() {
                const nav = document.getElementById('main-nav');
                const hamburger = document.getElementById('hamburger-menu');
                
                nav.classList.remove('active');
                hamburger.classList.remove('active');
            });
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.getElementById('main-nav');
            const hamburger = document.getElementById('hamburger-menu');
            const header = document.querySelector('.header-main');
            
            if (!header.contains(event.target)) {
                nav.classList.remove('active');
                hamburger.classList.remove('active');
            }
        });
    </script>
</body>
</html>