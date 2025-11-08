<?php
// Backup of current index.php
require_once "admin/config.php";

// Ensure get_setting function is available
if (!function_exists('get_setting')) {
    function get_setting($key, $default = '') {
        global $pdo;
        if (!$pdo) return $default;
        
        try {
            $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            $result = $stmt->fetch();
            return $result ? $result['setting_value'] : $default;
        } catch (Exception $e) {
            return $default;
        }
    }
}

// Get site settings with better error handling
$settings = [];
try {
    // Try settings table first (new structure)
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // Fallback to site_settings table (old structure)
    try {
$stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
    } catch (Exception $e2) {
        // No settings table exists - use defaults
        $settings = [
    'site_name' => 'CWS Legal Chambers',
    'site_email' => 'contact@cwslegalchambers.com',
    'site_phone' => '(212) 555-1234',
    'site_address' => '123 Legal Plaza, Suite 1000, New York, NY 10001',
    'total_recovered' => '500',
    'cases_won' => '10000',
    'success_rate' => '90',
            'years_experience' => '25'
        ];
    }
}

// Default location content - safe fallback
$location_content = [
    'phone' => '(212) 555-1234',
    'email' => 'contact@cwslegalchambers.com',
    'full_address' => '123 Legal Plaza, Suite 1000, New York, NY 10001',
    'address' => '123 Legal Plaza, Suite 1000',
    'city' => 'New York',
    'state' => 'NY',
    'zip' => '10001',
    'location_reference' => 'New York\'s premier legal powerhouse',
    'location_reference_about' => 'one of New York\'s premier law firms',
    'location_reference_footer' => 'Premier legal services in New York with over 25 years of excellence',
    'page_title_location' => 'Premier Law Firm in New York'
];

// Page metadata
$page_title = $settings['site_name'] . " - " . $location_content['page_title_location'];
$page_description = "Leading law firm with 25+ years of excellence. Specializing in Personal Injury, Business Law, Estate Planning. Free consultation. Call " . $location_content['phone'] . ".";
$page_keywords = "law firm, attorney, lawyer, " . $location_content['city'] . ", personal injury, business law, estate planning";

// Get all locations
$all_locations = [];
try {
    $stmt = $pdo->query("SELECT * FROM locations WHERE is_active = 1 ORDER BY sort_order ASC, name ASC");
    $all_locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $all_locations = [];
}

// Get services
$services = [];
try {
    $stmt = $pdo->query("SELECT * FROM services WHERE active = 1 ORDER BY sort_order ASC LIMIT 6");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $services = [];
}

// Get team members
$legal_experts = [];
try {
    $stmt = $pdo->query("SELECT * FROM team WHERE active = 1 ORDER BY sort_order ASC, name ASC LIMIT 3");
    $legal_experts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $legal_experts = [];
}

// Get about content
$about_content = [];
try {
    $tables = $pdo->query("SHOW TABLES LIKE 'settings'")->fetchAll();
    if (!empty($tables)) {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'about_%'");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $about_content[$row['setting_key']] = $row['setting_value'];
        }
    }
} catch (Exception $e) {
    // Settings table might not exist
}

// Set defaults for about section
$about_defaults = [
    'about_title' => 'Why Choose CWS Legal Chambers?',
    'about_description_1' => 'With over 25 years of dedicated service, CWS Legal Chambers has established itself as one of New York\'s premier law firms. Our team of experienced attorneys combines deep legal expertise with a genuine commitment to our clients\' success.',
    'about_description_2' => 'We understand that legal matters can be overwhelming. That\'s why we provide personalized attention, clear communication, and aggressive representation to protect your rights and interests.',
    'about_feature_1_title' => 'Proven Track Record',
    'about_feature_1_description' => 'Over $500 million recovered for our clients',
    'about_feature_2_title' => 'No Win, No Fee',
    'about_feature_2_description' => 'You don\'t pay unless we win your case',
    'about_feature_3_title' => '24/7 Availability',
    'about_feature_3_description' => 'We\'re here when you need us most',
    'about_experience_years' => '25',
    'about_image_alt' => 'CWS Legal Team'
];

$about_content = array_merge($about_defaults, $about_content);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords" content="<?php echo $page_keywords; ?>">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --navy: #002147;
            --gold: #ffd700;
            --white: #ffffff;
            --gray-light: #f8f9fa;
            --gray: #6c757d;
            --gray-dark: #343a40;
            --font-primary: 'Inter', sans-serif;
            --font-heading: 'Playfair Display', serif;
        }
        
        body {
            font-family: var(--font-primary);
            color: var(--gray-dark);
            line-height: 1.6;
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
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
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
            background: #001534;
        }
        
        /* Search */
        .search-icon { 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            width: 40px; 
            height: 40px; 
            background: var(--gray-light);
            border-radius: 50%; 
            cursor: pointer; 
            transition: all 0.3s ease; 
            color: var(--gray-dark);
            border: 1px solid #e9ecef;
        }
        
        .search-icon:hover { 
            background: var(--navy); 
            color: var(--white); 
            transform: scale(1.05);
        }
        
        .search-container { 
            position: absolute; 
            top: 100%; 
            right: 0; 
            background: var(--white); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 5px;
            opacity: 0; 
            visibility: hidden; 
            transform: translateY(-10px); 
            transition: all 0.3s ease; 
            z-index: 1001;
        }
        
        .search-container.active { 
            opacity: 1; 
            visibility: visible; 
            transform: translateY(0); 
        }
        
        .search-form {
            display: flex;
            align-items: center;
            padding: 8px 15px;
        }
        
        .search-input {
            border: none;
            background: transparent;
            outline: none;
            padding: 8px 12px;
            font-size: 14px;
            flex: 1;
            width: 100%;
        }
        
        .search-btn {
            background: none;
            border: none;
            color: var(--gray-dark);
            cursor: pointer;
            padding: 8px;
            transition: all 0.3s ease;
            border-radius: 50%;
        }
        
        .search-btn:hover {
            color: var(--navy);
            background: rgba(0,33,71,0.1);
        }
        
        /* Hamburger Menu */
        .hamburger-menu {
            display: none;
            flex-direction: column;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 5px;
            gap: 4px;
        }
        
        .hamburger-menu span {
            width: 25px;
            height: 3px;
            background: var(--navy);
            transition: all 0.3s;
            border-radius: 2px;
        }
        
        .hamburger-menu.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }
        
        .hamburger-menu.active span:nth-child(2) {
            opacity: 0;
        }
        
        .hamburger-menu.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }
        
        /* Hero Slider */
        .hero-slider {
            position: relative;
            height: 100vh;
            min-height: 600px;
            overflow: hidden;
        }
        
        .slider-container {
            position: relative;
            width: 100%;
            height: 100%;
        }
        
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        
        .slide.active {
            opacity: 1;
        }
        
        .slide-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .slide-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            text-align: center;
            color: var(--white);
            padding: 0 20px;
        }
        
        .slide-badge {
            background: var(--gold);
            color: var(--navy);
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        .slide-content h1 {
            font-family: var(--font-heading);
            font-size: 48px;
            margin-bottom: 20px;
            line-height: 1.2;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .slide-description {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 600px;
        }
        
        .slide-buttons {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
            justify-content: center;
        }
        
        .trust-badges {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .trust-badges span {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: var(--gold);
            border: none;
            color: var(--navy);
            font-size: 24px;
            font-weight: bold;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 3;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .slider-arrow:hover {
            background: var(--white);
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .slider-arrow.prev {
            left: 30px;
        }
        
        .slider-arrow.next {
            right: 30px;
        }
        
        .slider-nav {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 3;
        }
        
        .nav-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .nav-dot.active {
            background: var(--gold);
        }
        
        /* Legal Scenes Statistics */
        .legal-scenes-stats {
            padding: 80px 0;
            background: var(--white);
        }
        
        .legal-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 50px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .legal-stat-item {
            padding: 20px;
        }
        
        .legal-stat-label {
            font-size: 18px;
            color: var(--navy);
            font-weight: 600;
            margin-bottom: 15px;
            text-align: left;
        }
        
        .progress-bar-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .progress-bar {
            flex: 1;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--gold) 0%, #ffd700 100%);
            border-radius: 4px;
            width: 0%;
            transition: width 2s ease-in-out;
            position: relative;
        }
        
        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.3) 50%, transparent 100%);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .progress-percentage {
            font-size: 18px;
            font-weight: bold;
            color: var(--navy);
            min-width: 40px;
            text-align: right;
        }
        
        /* Awards Section */
        .awards {
            padding: 80px 0;
            background: var(--gray-light);
        }
        
        .awards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }
        
        .award-item {
            background: var(--white);
            padding: 40px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .award-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gold) 0%, #ffd700 100%);
        }
        
        .award-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .award-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--gold) 0%, #ffd700 100%);
            border-radius: 50%;
            color: var(--navy);
            font-size: 32px;
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
        }
        
        .award-item h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--navy);
            font-weight: 700;
        }
        
        .award-item p {
            color: var(--gray-dark);
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.5;
        }
        
        .award-year {
            background: var(--navy);
            color: var(--white);
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }
        
        /* Proven Excellence Section */
        .proven-excellence {
            padding: 80px 0;
            background: var(--white);
        }
        
        .excellence-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-top: 50px;
        }
        
        .excellence-stat-item {
            background: linear-gradient(135deg, var(--navy) 0%, #003366 100%);
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            color: var(--white);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .excellence-stat-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,215,0,0.1) 0%, rgba(255,215,0,0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .excellence-stat-item:hover::before {
            opacity: 1;
        }
        
        .excellence-stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,16,46,0.3);
        }
        
        .excellence-stat-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gold);
            border-radius: 50%;
            color: var(--navy);
            font-size: 28px;
            position: relative;
            z-index: 2;
        }
        
        .excellence-stat-number-container {
            display: flex;
            align-items: baseline;
            justify-content: center;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }
        
        .excellence-stat-number {
            font-size: 3.5em;
            font-weight: bold;
            line-height: 1;
            color: var(--white);
        }
        
        .excellence-stat-symbol {
            font-size: 1.8em;
            font-weight: 600;
            color: var(--gold);
            margin-left: 5px;
            line-height: 1;
        }
        
        .excellence-stat-description {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            position: relative;
            z-index: 2;
        }
        
        .excellence-stat-subtitle {
            font-size: 14px;
            opacity: 0.8;
            position: relative;
            z-index: 2;
        }
        
        /* Trusted By Section */
        .trusted-by {
            padding: 80px 0;
            background: var(--gray-light);
        }
        
        .trusted-logos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-top: 50px;
            align-items: center;
        }
        
        .trusted-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 20px;
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .trusted-logo:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .trusted-logo i {
            font-size: 48px;
            color: var(--navy);
            margin-bottom: 15px;
        }
        
        .trusted-logo span {
            font-size: 18px;
            font-weight: 600;
            color: var(--navy);
        }
        
        /* Google Reviews Section */
        .google-reviews {
            padding: 80px 0;
            background: var(--white);
        }
        
        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 50px;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .google-logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .google-logo i {
            font-size: 32px;
            color: #4285f4;
        }
        
        .google-logo span {
            font-size: 24px;
            font-weight: 600;
            color: var(--navy);
        }
        
        .reviews-rating {
            text-align: right;
        }
        
        .stars {
            display: flex;
            gap: 2px;
            margin-bottom: 10px;
            justify-content: flex-end;
        }
        
        .stars i {
            color: #ffd700;
            font-size: 18px;
        }
        
        .rating-number {
            font-size: 32px;
            font-weight: bold;
            color: var(--navy);
            margin-bottom: 5px;
        }
        
        .reviews-count {
            font-size: 16px;
            color: var(--gray-dark);
        }
        
        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .review-item {
            background: var(--white);
            border: 1px solid #e0e0e0;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .review-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.12);
        }
        
        .review-profile {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .profile-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
            border: 3px solid var(--gold);
        }
        
        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 5px;
        }
        
        .review-date {
            font-size: 14px;
            color: var(--gray-dark);
        }
        
        .review-content {
            margin-left: 65px;
        }
        
        .review-stars {
            display: flex;
            gap: 2px;
            margin-bottom: 15px;
        }
        
        .review-stars i {
            color: #ffd700;
            font-size: 16px;
        }
        
        .review-text {
            font-size: 16px;
            line-height: 1.6;
            color: var(--gray-dark);
            font-style: italic;
        }
        
        /* Legal Experts Section */
        .legal-experts {
            padding: 0;
        }
        
        .legal-experts-header {
            background: linear-gradient(135deg, var(--navy) 0%, #003366 100%);
            padding: 80px 0;
        }
        
        .legal-experts-content {
            background: var(--gray-light);
            padding: 80px 0;
        }
        
        .experts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 40px;
            margin-top: 50px;
        }
        
        .expert-card {
            background: var(--white);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .expert-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gold) 0%, #ffd700 100%);
        }
        
        .expert-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .expert-profile {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }
        
        .expert-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid var(--gold);
            flex-shrink: 0;
            position: relative;
        }
        
        .expert-avatar::after {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--gold), #ffd700, var(--gold));
            z-index: -1;
            animation: rotate 3s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .expert-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .expert-info {
            flex: 1;
        }
        
        .expert-name {
            font-size: 24px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 8px;
        }
        
        .expert-title {
            font-size: 18px;
            color: var(--gold);
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .expert-education {
            font-size: 16px;
            color: var(--gray-dark);
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .expert-bar {
            font-size: 14px;
            color: var(--gray-dark);
            margin-bottom: 8px;
        }
        
        .expert-experience {
            font-size: 14px;
            color: var(--gray-dark);
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .expert-credentials {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .credential {
            background: linear-gradient(135deg, var(--navy) 0%, #003366 100%);
            color: var(--white);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            display: inline-block;
        }
        
        .btn {
            padding: 15px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-primary {
            background: var(--gold);
            color: var(--navy);
        }
        
        .btn-primary:hover {
            background: #e6c200;
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--white);
            border: 2px solid var(--white);
        }
        
        .btn-secondary:hover {
            background: var(--white);
            color: var(--navy);
        }
        
        /* Statistics */
        .stats {
            background: var(--gray-light);
            padding: 60px 0;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }
        
        .stat-item {
            text-align: center;
            padding: 30px 20px;
            background: var(--white);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: var(--navy);
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 18px;
            color: var(--gray-dark);
            margin-bottom: 5px;
        }
        
        .stat-description {
            color: var(--gray);
            font-size: 14px;
        }
        
        /* About Section */
        .about {
            padding: 80px 0;
        }
        
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }
        
        .about-text h2 {
            font-family: var(--font-heading);
            font-size: 36px;
            margin-bottom: 20px;
            color: var(--navy);
        }
        
        .about-text p {
            margin-bottom: 20px;
            color: var(--gray-dark);
        }
        
        .about-features {
            margin-top: 30px;
        }
        
        .feature {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .feature-icon {
            color: var(--gold);
            font-size: 20px;
            margin-top: 5px;
        }
        
        .about-image {
            position: relative;
        }
        
        .about-image img {
            width: 100%;
            border-radius: 10px;
        }
        
        .experience-badge {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: var(--navy);
            color: var(--white);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .experience-number {
            font-size: 2em;
            font-weight: bold;
        }
        
        /* Services Section */
        .services {
            padding: 80px 0;
            background: var(--gray-light);
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title {
            font-family: var(--font-heading);
            font-size: 36px;
            margin-bottom: 15px;
            color: var(--navy);
        }
        
        .section-subtitle {
            font-size: 18px;
            color: var(--gray);
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .service-card {
            background: var(--white);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
        }
        
        .service-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--navy);
            border-radius: 50%;
            color: var(--white);
            font-size: 24px;
        }
        
        .service-title {
            font-size: 20px;
            margin-bottom: 15px;
            color: var(--navy);
        }
        
        .service-description {
            color: var(--gray-dark);
            margin-bottom: 20px;
        }
        
        .service-link {
            color: var(--navy);
            text-decoration: none;
            font-weight: 600;
        }
        
        .service-link:hover {
            text-decoration: underline;
        }
        
        /* Call to Action Section */
        .cta {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--navy) 0%, #003366 100%);
            color: var(--white);
            text-align: center;
        }
        
        .cta-content h2 {
            font-size: 36px;
            margin-bottom: 20px;
            font-family: var(--font-heading);
        }
        
        .cta-content p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* Testimonials Section */
        .testimonials {
            padding: 80px 0;
            background: var(--gray-light);
        }
        
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }
        
        .testimonial-item {
            background: var(--white);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .testimonial-item::before {
            content: '"';
            position: absolute;
            top: 20px;
            left: 30px;
            font-size: 60px;
            color: var(--gold);
            font-family: serif;
            line-height: 1;
        }
        
        .testimonial-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .testimonial-stars {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
            margin-top: 20px;
        }
        
        .testimonial-stars i {
            color: #ffd700;
            font-size: 18px;
        }
        
        .testimonial-text {
            font-size: 16px;
            line-height: 1.6;
            color: var(--gray-dark);
            margin-bottom: 25px;
            font-style: italic;
        }
        
        .testimonial-author h4 {
            font-size: 18px;
            color: var(--navy);
            margin-bottom: 5px;
        }
        
        .testimonial-author span {
            color: var(--gold);
            font-weight: 600;
        }
        
        /* FAQ Section */
        .faq {
            padding: 80px 0;
            background: var(--white);
        }
        
        .faq-grid {
            max-width: 800px;
            margin: 50px auto 0;
        }
        
        .faq-item {
            background: var(--white);
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .faq-item:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .faq-question {
            padding: 25px 30px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--gray-light);
            transition: all 0.3s ease;
        }
        
        .faq-question:hover {
            background: var(--gold);
            color: var(--navy);
        }
        
        .faq-question h3 {
            font-size: 18px;
            margin: 0;
            color: var(--navy);
            font-weight: 600;
        }
        
        .faq-question i {
            font-size: 20px;
            color: var(--navy);
            transition: transform 0.3s ease;
        }
        
        .faq-item.active .faq-question i {
            transform: rotate(45deg);
        }
        
        .faq-answer {
            padding: 0 30px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .faq-item.active .faq-answer {
            padding: 25px 30px;
            max-height: 200px;
        }
        
        .faq-answer p {
            color: var(--gray-dark);
            line-height: 1.6;
            margin: 0;
        }
        
        /* Awards Section */
        .awards {
            padding: 80px 0;
            background: var(--gray-light);
        }
        
        .awards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .award-item {
            background: var(--white);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .award-item:hover {
            transform: translateY(-5px);
        }
        
        .award-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gold);
            border-radius: 50%;
            color: var(--navy);
            font-size: 24px;
        }
        
        .award-item h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: var(--navy);
        }
        
        .award-item p {
            color: var(--gray-dark);
            margin-bottom: 15px;
        }
        
        .award-year {
            background: var(--navy);
            color: var(--white);
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
        }
        
        /* Case Results Section */
        .case-results {
            padding: 0;
        }
        
        .case-results-header {
            background: linear-gradient(135deg, var(--navy) 0%, #003366 100%);
            padding: 80px 0;
        }
        
        .case-results-content {
            background: var(--gray-light);
            padding: 60px 0;
        }
        
        .case-results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .case-result-item {
            background: var(--white);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .case-result-item:hover {
            transform: translateY(-5px);
        }
        
        .result-amount {
            font-size: 2.5em;
            font-weight: bold;
            color: var(--navy);
            margin-bottom: 10px;
        }
        
        .result-type {
            font-size: 18px;
            color: var(--gold);
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .result-description {
            color: var(--gray-dark);
            margin-bottom: 15px;
        }
        
        .result-year {
            background: var(--navy);
            color: var(--white);
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
        }
        
        /* Footer */
        .footer {
            background: var(--gray-dark);
            color: var(--white);
            padding: 50px 0 20px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-about h3 {
            color: var(--gold);
            margin-bottom: 15px;
        }
        
        .footer-links h4 {
            color: var(--gold);
            margin-bottom: 15px;
        }
        
        .footer-links ul {
            list-style: none;
        }
        
        .footer-links a {
            color: var(--white);
            text-decoration: none;
            line-height: 2;
            opacity: 0.8;
        }
        
        .footer-links a:hover {
            color: var(--gold);
            opacity: 1;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            text-align: center;
            opacity: 0.8;
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
            
            .search-icon {
                order: 1;
            }
            
            .hamburger-menu {
                order: 2;
            }
            
            .slide-content h1 {
                font-size: 32px;
            }
            
            .slide-buttons {
                flex-direction: column;
            }
            
            .about-content {
                grid-template-columns: 1fr;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
            }
            
            .trusted-logos {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 20px;
            }
            
            .trusted-logo {
                padding: 20px 15px;
            }
            
            .trusted-logo i {
                font-size: 36px;
            }
            
            .trusted-logo span {
                font-size: 16px;
            }
            
            .reviews-header {
                flex-direction: column;
                text-align: center;
            }
            
            .reviews-rating {
                text-align: center;
            }
            
            .reviews-grid {
                grid-template-columns: 1fr;
            }
            
            .review-content {
                margin-left: 0;
                margin-top: 15px;
            }
            
            .experts-grid {
                grid-template-columns: 1fr;
            }
            
            .expert-profile {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
            
            .expert-avatar {
                width: 100px;
                height: 100px;
                margin: 0 auto;
            }
            
            .expert-credentials {
                align-items: center;
            }
            
            .cta-content h2 {
                font-size: 28px;
            }
            
            .cta-content p {
                font-size: 18px;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .testimonials-grid {
                grid-template-columns: 1fr;
            }
            
            .testimonial-item {
                padding: 30px 20px;
            }
            
            .faq-question {
                padding: 20px;
            }
            
            .faq-question h3 {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-main">
            <div class="container">
                <a href="/" class="logo">
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
                    <div class="search-icon" onclick="toggleSearch()">
                        <i class="fas fa-search"></i>
                    </div>
                    <button class="hamburger-menu" id="hamburger-menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="search-container" id="searchContainer">
            <form class="search-form" action="search.php" method="GET">
                <input type="text" name="q" placeholder="Search..." class="search-input" id="searchInput">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </header>

    <!-- Hero Slider Section -->
    <section class="hero-slider">
        <div class="slider-container">
            <!-- Slide 1 -->
            <div class="slide active">
                <div class="slide-bg" style="background-image: linear-gradient(135deg, rgba(0,16,46,0.9) 0%, rgba(0,16,46,0.3) 100%), url('https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=1920&h=700&fit=crop');"></div>
                    <div class="slide-content">
                        <span class="slide-badge"><?= htmlspecialchars(get_setting('slider_1_badge', '★ Top Rated Law Firm ' . date('Y'))) ?></span>
                    <h1><?= get_setting('slider_1_title', 'Defending Your Rights.<br>Delivering Justice.') ?></h1>
                    <p class="slide-description"><?= htmlspecialchars(get_setting('slider_1_description', 'With over 25 years of excellence, CWS Legal Chambers stands as New York\'s premier legal powerhouse, combining unmatched expertise with personalized attention.')) ?></p>
                        <div class="slide-buttons">
                            <a href="<?= htmlspecialchars(get_setting('slider_1_button_1_url', '#contact')) ?>" class="btn btn-primary"><?= htmlspecialchars(get_setting('slider_1_button_1_text', 'Get Free Consultation')) ?></a>
                        <a href="tel:<?= str_replace(['(', ')', ' ', '-'], '', get_setting('slider_1_phone', $settings['site_phone'] ?? '(212) 555-1234')) ?>" class="btn btn-secondary"><?= htmlspecialchars(get_setting('slider_1_button_2_text', 'Call Now: ' . ($settings['site_phone'] ?? '(212) 555-1234'))) ?></a>
                        </div>
                        <div class="trust-badges">
                        <span><?= htmlspecialchars(get_setting('slider_1_badge_1', 'Super Lawyers')) ?></span>
                        <span><?= htmlspecialchars(get_setting('slider_1_badge_2', 'Best Law Firms')) ?></span>
                        <span><?= htmlspecialchars(get_setting('slider_1_badge_3', 'AV Rated')) ?></span>
                        <span><?= htmlspecialchars(get_setting('slider_1_badge_4', 'Bar Association')) ?></span>
                        </div>
                    </div>
                </div>
            
            <!-- Slide 2 -->
            <div class="slide">
                <div class="slide-bg" style="background-image: linear-gradient(135deg, rgba(0,16,46,0.9) 0%, rgba(0,16,46,0.3) 100%), url('https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?w=1920&h=700&fit=crop');"></div>
                    <div class="slide-content">
                    <span class="slide-badge"><?= htmlspecialchars(get_setting('slider_2_badge', '🏆 Award-Winning Legal Team')) ?></span>
                    <h1><?= get_setting('slider_2_title', 'Personal Injury<br>Champions') ?></h1>
                    <p class="slide-description"><?= htmlspecialchars(get_setting('slider_2_description', 'Recovered over $500 million for our clients with a 95% success rate. When you\'re injured, trust the experts who fight for maximum compensation.')) ?></p>
                        <div class="slide-buttons">
                        <a href="<?= htmlspecialchars(get_setting('slider_2_button_1_url', '#contact')) ?>" class="btn btn-primary"><?= htmlspecialchars(get_setting('slider_2_button_1_text', 'Free Case Evaluation')) ?></a>
                        <a href="tel:<?= str_replace(['(', ')', ' ', '-'], '', get_setting('slider_2_phone', $settings['site_phone'] ?? '(212) 555-1234')) ?>" class="btn btn-secondary"><?= htmlspecialchars(get_setting('slider_2_button_2_text', 'Call Now: ' . ($settings['site_phone'] ?? '(212) 555-1234'))) ?></a>
                        </div>
                        <div class="trust-badges">
                        <span><?= htmlspecialchars(get_setting('slider_2_badge_1', '$500M+ Recovered')) ?></span>
                        <span><?= htmlspecialchars(get_setting('slider_2_badge_2', '95% Success Rate')) ?></span>
                        <span><?= htmlspecialchars(get_setting('slider_2_badge_3', 'No Win, No Fee')) ?></span>
                        <span><?= htmlspecialchars(get_setting('slider_2_badge_4', '24/7 Support')) ?></span>
                        </div>
                    </div>
                </div>
            
            <!-- Slide 3 -->
            <div class="slide">
                <div class="slide-bg" style="background-image: linear-gradient(135deg, rgba(0,16,46,0.9) 0%, rgba(0,16,46,0.3) 100%), url('https://images.unsplash.com/photo-1589994965851-a8f479c573a9?w=1920&h=700&fit=crop');"></div>
                <div class="slide-content">
                    <span class="slide-badge"><?= htmlspecialchars(get_setting('slider_3_badge', '⚖️ Business Law Experts')) ?></span>
                    <h1><?= get_setting('slider_3_title', 'Corporate Legal<br>Solutions') ?></h1>
                    <p class="slide-description"><?= htmlspecialchars(get_setting('slider_3_description', 'From startup formation to complex mergers, our business law team provides comprehensive legal solutions for companies of all sizes.')) ?></p>
                    <div class="slide-buttons">
                        <a href="<?= htmlspecialchars(get_setting('slider_3_button_1_url', '#contact')) ?>" class="btn btn-primary"><?= htmlspecialchars(get_setting('slider_3_button_1_text', 'Schedule Consultation')) ?></a>
                        <a href="tel:<?= str_replace(['(', ')', ' ', '-'], '', get_setting('slider_3_phone', $settings['site_phone'] ?? '(212) 555-1234')) ?>" class="btn btn-secondary"><?= htmlspecialchars(get_setting('slider_3_button_2_text', 'Call Now: ' . ($settings['site_phone'] ?? '(212) 555-1234'))) ?></a>
                    </div>
                    <div class="trust-badges">
                        <span><?= htmlspecialchars(get_setting('slider_3_badge_1', 'Fortune 500 Clients')) ?></span>
                        <span><?= htmlspecialchars(get_setting('slider_3_badge_2', 'Complex Litigation')) ?></span>
                        <span><?= htmlspecialchars(get_setting('slider_3_badge_3', 'Contract Expertise')) ?></span>
                        <span><?= htmlspecialchars(get_setting('slider_3_badge_4', 'M&A Specialists')) ?></span>
                        </div>
                </div>
            </div>
            
            <!-- Slide 4 -->
            <div class="slide">
                <div class="slide-bg" style="background-image: linear-gradient(135deg, rgba(0,16,46,0.9) 0%, rgba(0,16,46,0.3) 100%), url('https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?w=1920&h=700&fit=crop');"></div>
                <div class="slide-content">
                    <span class="slide-badge"><?= htmlspecialchars(get_setting('slider_4_badge', '🛡️ Estate Planning Specialists')) ?></span>
                    <h1><?= get_setting('slider_4_title', 'Protect Your<br>Family\'s Future') ?></h1>
                    <p class="slide-description"><?= htmlspecialchars(get_setting('slider_4_description', 'Secure your legacy with comprehensive estate planning, wills, and trusts. Our experienced attorneys ensure your family\'s financial security.')) ?></p>
                    <div class="slide-buttons">
                        <a href="<?= htmlspecialchars(get_setting('slider_4_button_1_url', '#contact')) ?>" class="btn btn-primary"><?= htmlspecialchars(get_setting('slider_4_button_1_text', 'Free Estate Review')) ?></a>
                        <a href="tel:<?= str_replace(['(', ')', ' ', '-'], '', get_setting('slider_4_phone', $settings['site_phone'] ?? '(212) 555-1234')) ?>" class="btn btn-secondary"><?= htmlspecialchars(get_setting('slider_4_button_2_text', 'Call Now: ' . ($settings['site_phone'] ?? '(212) 555-1234'))) ?></a>
                    </div>
                    <div class="trust-badges">
                        <span><?= htmlspecialchars(get_setting('slider_4_badge_1', 'Trust & Estate Planning')) ?></span>
                        <span><?= htmlspecialchars(get_setting('slider_4_badge_2', 'Asset Protection')) ?></span>
                        <span><?= htmlspecialchars(get_setting('slider_4_badge_3', 'Tax Optimization')) ?></span>
                        <span><?= htmlspecialchars(get_setting('slider_4_badge_4', 'Family Wealth')) ?></span>
                        </div>
                </div>
            </div>
            
            <!-- Navigation Arrows -->
            <button class="slider-arrow prev">‹</button>
            <button class="slider-arrow next">›</button>
            
            <!-- Navigation Dots -->
            <div class="slider-nav">
                    <span class="nav-dot active" onclick="currentSlide(1)"></span>
                <span class="nav-dot" onclick="currentSlide(2)"></span>
                <span class="nav-dot" onclick="currentSlide(3)"></span>
                <span class="nav-dot" onclick="currentSlide(4)"></span>
            </div>
        </div>
    </section>

    <!-- Legal Scenes Statistics Section -->
    <section class="legal-scenes-stats">
        <div class="container">
            <div class="legal-stats-grid">
                <div class="legal-stat-item">
                    <div class="legal-stat-label">Legal and Political Scenes</div>
                    <div class="progress-bar-container">
                    <div class="progress-bar">
                            <div class="progress-fill" data-width="90"></div>
                    </div>
                        <div class="progress-percentage">90%</div>
                </div>
                    </div>
                <div class="legal-stat-item">
                    <div class="legal-stat-label">Experienced Individuals</div>
                    <div class="progress-bar-container">
                    <div class="progress-bar">
                            <div class="progress-fill" data-width="85"></div>
                    </div>
                        <div class="progress-percentage">85%</div>
                </div>
                    </div>
                <div class="legal-stat-item">
                    <div class="legal-stat-label">Client Satisfaction</div>
                    <div class="progress-bar-container">
                    <div class="progress-bar">
                            <div class="progress-fill" data-width="95"></div>
                        </div>
                        <div class="progress-percentage">95%</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Awards & Recognition Section -->
    <section class="awards">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?= htmlspecialchars(get_setting('awards_section_title', 'Awards & Recognition')) ?></h2>
                <p class="section-subtitle"><?= htmlspecialchars(get_setting('awards_section_subtitle', 'Recognized excellence in legal practice')) ?></p>
            </div>
            <div class="awards-grid">
                <div class="award-item">
                    <div class="award-icon">
                        <i class="<?= htmlspecialchars(get_setting('award_1_icon', 'fas fa-trophy')) ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars(get_setting('award_1_title', 'Super Lawyers')) ?></h3>
                    <p><?= htmlspecialchars(get_setting('award_1_description', 'Top 10 of attorneys in Europe')) ?></p>
                    <span class="award-year"><?= htmlspecialchars(get_setting('award_1_year', '2024')) ?></span>
                </div>
                <div class="award-item">
                    <div class="award-icon">
                        <i class="<?= htmlspecialchars(get_setting('award_2_icon', 'fas fa-medal')) ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars(get_setting('award_2_title', 'Best Law Firms')) ?></h3>
                    <p><?= htmlspecialchars(get_setting('award_2_description', 'France News & World Report')) ?></p>
                    <span class="award-year"><?= htmlspecialchars(get_setting('award_2_year', '2024')) ?></span>
                </div>
                <div class="award-item">
                    <div class="award-icon">
                        <i class="<?= htmlspecialchars(get_setting('award_3_icon', 'fas fa-star')) ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars(get_setting('award_3_title', 'AV Preeminent')) ?></h3>
                    <p><?= htmlspecialchars(get_setting('award_3_description', 'Martindale-Hubbell Rating')) ?></p>
                    <span class="award-year"><?= htmlspecialchars(get_setting('award_3_year', '2024')) ?></span>
                </div>
                <div class="award-item">
                    <div class="award-icon">
                        <i class="<?= htmlspecialchars(get_setting('award_4_icon', 'fas fa-certificate')) ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars(get_setting('award_4_title', 'Bar Association')) ?></h3>
                    <p><?= htmlspecialchars(get_setting('award_4_description', 'Paris State Bar Member')) ?></p>
                    <span class="award-year"><?= htmlspecialchars(get_setting('award_4_year', '25+ Years')) ?></span>
                </div>
                <div class="award-item">
                    <div class="award-icon">
                        <i class="<?= htmlspecialchars(get_setting('award_5_icon', 'fas fa-building')) ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars(get_setting('award_5_title', 'Chamber of Commerce')) ?></h3>
                    <p><?= htmlspecialchars(get_setting('award_5_description', 'Business Excellence Award')) ?></p>
                    <span class="award-year"><?= htmlspecialchars(get_setting('award_5_year', '2023')) ?></span>
                </div>
                <div class="award-item">
                    <div class="award-icon">
                        <i class="<?= htmlspecialchars(get_setting('award_6_icon', 'fas fa-gavel')) ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars(get_setting('award_6_title', 'Trial Lawyers')) ?></h3>
                    <p><?= htmlspecialchars(get_setting('award_6_description', 'Association Member')) ?></p>
                    <span class="award-year"><?= htmlspecialchars(get_setting('award_6_year', '20+ Years')) ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Proven Excellence Section -->
    <section class="proven-excellence">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?= htmlspecialchars(get_setting('statistics_section_title', 'Proven Excellence')) ?></h2>
                <p class="section-subtitle"><?= htmlspecialchars(get_setting('statistics_section_subtitle', 'Numbers that speak for our commitment to justice')) ?></p>
            </div>
            <div class="excellence-stats-grid">
                <div class="excellence-stat-item">
                    <div class="excellence-stat-icon">
                        <i class="<?= htmlspecialchars(get_setting('stat_1_icon', 'fas fa-dollar-sign')) ?>"></i>
                    </div>
                    <div class="excellence-stat-number-container">
                        <span class="excellence-stat-number" data-target="<?= htmlspecialchars(get_setting('stat_1_number', '500')) ?>"><?= htmlspecialchars(get_setting('stat_1_number', '500')) ?></span>
                        <span class="excellence-stat-symbol"><?= htmlspecialchars(get_setting('stat_1_symbol', 'M+')) ?></span>
                        </div>
                    <div class="excellence-stat-description"><?= htmlspecialchars(get_setting('stat_1_description', 'Recovered')) ?></div>
                    <div class="excellence-stat-subtitle"><?= htmlspecialchars(get_setting('stat_1_subtitle', 'For our clients')) ?></div>
                    </div>
                <div class="excellence-stat-item">
                    <div class="excellence-stat-icon">
                        <i class="<?= htmlspecialchars(get_setting('stat_2_icon', 'fas fa-trophy')) ?>"></i>
                    </div>
                    <div class="excellence-stat-number-container">
                        <span class="excellence-stat-number" data-target="<?= htmlspecialchars(get_setting('stat_2_number', '10000')) ?>"><?= htmlspecialchars(get_setting('stat_2_number', '10000')) ?></span>
                        <span class="excellence-stat-symbol"><?= htmlspecialchars(get_setting('stat_2_symbol', '+')) ?></span>
                        </div>
                    <div class="excellence-stat-description"><?= htmlspecialchars(get_setting('stat_2_description', 'Cases Won')) ?></div>
                    <div class="excellence-stat-subtitle"><?= htmlspecialchars(get_setting('stat_2_subtitle', 'Successful outcomes')) ?></div>
                    </div>
                <div class="excellence-stat-item">
                    <div class="excellence-stat-icon">
                        <i class="<?= htmlspecialchars(get_setting('stat_3_icon', 'fas fa-percentage')) ?>"></i>
                </div>
                    <div class="excellence-stat-number-container">
                        <span class="excellence-stat-number" data-target="<?= htmlspecialchars(get_setting('stat_3_number', '90')) ?>"><?= htmlspecialchars(get_setting('stat_3_number', '90')) ?></span>
                        <span class="excellence-stat-symbol"><?= htmlspecialchars(get_setting('stat_3_symbol', '%')) ?></span>
                    </div>
                    <div class="excellence-stat-description"><?= htmlspecialchars(get_setting('stat_3_description', 'Success Rate')) ?></div>
                    <div class="excellence-stat-subtitle"><?= htmlspecialchars(get_setting('stat_3_subtitle', 'Client satisfaction')) ?></div>
                        </div>
                <div class="excellence-stat-item">
                    <div class="excellence-stat-icon">
                        <i class="<?= htmlspecialchars(get_setting('stat_4_icon', 'fas fa-calendar-alt')) ?>"></i>
                    </div>
                    <div class="excellence-stat-number-container">
                        <span class="excellence-stat-number" data-target="<?= htmlspecialchars(get_setting('stat_4_number', '25')) ?>"><?= htmlspecialchars(get_setting('stat_4_number', '25')) ?></span>
                        <span class="excellence-stat-symbol"><?= htmlspecialchars(get_setting('stat_4_symbol', '+')) ?></span>
                </div>
                    <div class="excellence-stat-description"><?= htmlspecialchars(get_setting('stat_4_description', 'Years Experience')) ?></div>
                    <div class="excellence-stat-subtitle"><?= htmlspecialchars(get_setting('stat_4_subtitle', 'Legal expertise')) ?></div>
                    </div>
            </div>
        </div>
    </section>

    <!-- Trusted By Section -->
    <section class="trusted-by">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Trusted By</h2>
                <p class="section-subtitle">Prestigious clients and partners</p>
            </div>
            <div class="trusted-logos">
                <div class="trusted-logo">
                        <i class="fas fa-building"></i>
                        <span>Fortune 500</span>
                    </div>
                <div class="trusted-logo">
                    <i class="fas fa-landmark"></i>
                        <span>Government</span>
                    </div>
                <div class="trusted-logo">
                        <i class="fas fa-shield-alt"></i>
                        <span>Insurance</span>
                    </div>
                <div class="trusted-logo">
                        <i class="fas fa-hospital"></i>
                        <span>Healthcare</span>
                    </div>
                <div class="trusted-logo">
                        <i class="fas fa-industry"></i>
                        <span>Manufacturing</span>
                    </div>
                <div class="trusted-logo">
                        <i class="fas fa-chart-line"></i>
                        <span>Finance</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Google Reviews Section -->
    <section class="google-reviews">
        <div class="container">
            <div class="reviews-header">
                <div class="google-logo">
                    <i class="fab fa-google"></i>
                    <span>Google Reviews</span>
                </div>
                <div class="reviews-rating">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="rating-number"><?= htmlspecialchars(get_setting('index_google_rating', '5.0')) ?></div>
                    <div class="reviews-count">Based on <?= htmlspecialchars(get_setting('index_google_reviews_count', '127')) ?>+ reviews</div>
                </div>
            </div>
            <div class="reviews-grid">
                <div class="review-item">
                    <div class="review-profile">
                        <div class="profile-avatar">
                            <img src="<?= htmlspecialchars(get_setting('index_testimonial_1_photo', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face')) ?>" alt="<?= htmlspecialchars(get_setting('index_testimonial_1_name', 'Robert Williams')) ?>" class="avatar-img">
                            </div>
                        <div class="profile-info">
                            <div class="profile-name"><?= htmlspecialchars(get_setting('index_testimonial_1_name', 'Robert Williams')) ?></div>
                            <div class="review-date"><?= date('M j, Y', strtotime(get_setting('index_testimonial_1_date', '2024-12-15'))) ?></div>
                        </div>
                    </div>
                    <div class="review-content">
                        <div class="review-stars">
                            <?php for ($i = 0; $i < (int)get_setting('index_testimonial_1_rating', '5'); $i++): ?>
                            <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="review-text">"<?= htmlspecialchars(get_setting('index_testimonial_1_text', 'CWS Legal Chambers provided exceptional representation in our business litigation case. Their expertise and dedication resulted in a favorable outcome that exceeded our expectations.')) ?>"</p>
                    </div>
                </div>
                <div class="review-item">
                    <div class="review-profile">
                        <div class="profile-avatar">
                            <img src="<?= htmlspecialchars(get_setting('index_testimonial_2_photo', 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=100&h=100&fit=crop&crop=face')) ?>" alt="<?= htmlspecialchars(get_setting('index_testimonial_2_name', 'Maria Garcia')) ?>" class="avatar-img">
                            </div>
                        <div class="profile-info">
                            <div class="profile-name"><?= htmlspecialchars(get_setting('index_testimonial_2_name', 'Maria Garcia')) ?></div>
                            <div class="review-date"><?= date('M j, Y', strtotime(get_setting('index_testimonial_2_date', '2024-12-08'))) ?></div>
                        </div>
                    </div>
                    <div class="review-content">
                        <div class="review-stars">
                            <?php for ($i = 0; $i < (int)get_setting('index_testimonial_2_rating', '5'); $i++): ?>
                            <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="review-text">"<?= htmlspecialchars(get_setting('index_testimonial_2_text', 'The team\'s attention to detail and strategic approach helped me navigate a complex personal injury case. I couldn\'t have asked for better legal representation.')) ?>"</p>
                    </div>
                </div>
                <div class="review-item">
                    <div class="review-profile">
                        <div class="profile-avatar">
                            <img src="<?= htmlspecialchars(get_setting('index_testimonial_3_photo', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face')) ?>" alt="<?= htmlspecialchars(get_setting('index_testimonial_3_name', 'David Brown')) ?>" class="avatar-img">
                            </div>
                        <div class="profile-info">
                            <div class="profile-name"><?= htmlspecialchars(get_setting('index_testimonial_3_name', 'David Brown')) ?></div>
                            <div class="review-date"><?= date('M j, Y', strtotime(get_setting('index_testimonial_3_date', '2024-12-02'))) ?></div>
                        </div>
                    </div>
                    <div class="review-content">
                        <div class="review-stars">
                            <?php for ($i = 0; $i < (int)get_setting('index_testimonial_3_rating', '5'); $i++): ?>
                            <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="review-text">"<?= htmlspecialchars(get_setting('index_testimonial_3_text', 'Professional, knowledgeable, and results-driven. CWS Legal Chambers helped me secure my family\'s future with comprehensive estate planning services.')) ?>"</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Meet Our Legal Experts Section -->
    <section class="legal-experts">
        <div class="legal-experts-header">
        <div class="container">
            <div class="section-header">
                    <h2 class="section-title" style="color: white;"><?= htmlspecialchars(get_setting('team_section_title', 'Meet Our Legal Experts')) ?></h2>
                    <p class="section-subtitle" style="color: rgba(255, 255, 255, 0.9);"><?= htmlspecialchars(get_setting('team_section_subtitle', 'Highly qualified attorneys with proven track records')) ?></p>
            </div>
                        </div>
                    </div>
        <div class="legal-experts-content">
            <div class="container">
                <div class="experts-grid">
                    <div class="expert-card">
                        <div class="expert-profile">
                            <div class="expert-avatar">
                                <img src="<?= htmlspecialchars(get_setting('team_1_photo', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face')) ?>" alt="<?= htmlspecialchars(get_setting('team_1_name', 'John Smith')) ?>" class="expert-img">
                            </div>
                            <div class="expert-info">
                                <h3 class="expert-name"><?= htmlspecialchars(get_setting('team_1_name', 'John Smith')) ?></h3>
                                <p class="expert-title"><?= htmlspecialchars(get_setting('team_1_title', 'Senior Partner & Founder')) ?></p>
                                <p class="expert-education"><?= htmlspecialchars(get_setting('team_1_education', 'Harvard Law School - Juris Doctor')) ?></p>
                                <p class="expert-bar"><?= htmlspecialchars(get_setting('team_1_bar', 'Bar Admissions: NY, CA, FL, TX')) ?></p>
                                <p class="expert-experience"><?= htmlspecialchars(get_setting('team_1_experience', '25+ Years Experience in Personal Injury')) ?></p>
                                <div class="expert-credentials">
                                    <span class="credential"><?= htmlspecialchars(get_setting('team_1_credential_1', 'Super Lawyers - Top 5% (2020-2024)')) ?></span>
                                    <span class="credential"><?= htmlspecialchars(get_setting('team_1_credential_2', 'Former District Attorney - Manhattan')) ?></span>
                            </div>
                            </div>
                            </div>
                            </div>
                    <div class="expert-card">
                        <div class="expert-profile">
                            <div class="expert-avatar">
                                <img src="<?= htmlspecialchars(get_setting('team_2_photo', 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=200&h=200&fit=crop&crop=face')) ?>" alt="<?= htmlspecialchars(get_setting('team_2_name', 'Sarah Mitchell')) ?>" class="expert-img">
                        </div>
                            <div class="expert-info">
                                <h3 class="expert-name"><?= htmlspecialchars(get_setting('team_2_name', 'Sarah Mitchell')) ?></h3>
                                <p class="expert-title"><?= htmlspecialchars(get_setting('team_2_title', 'Partner - Business Law')) ?></p>
                                <p class="expert-education"><?= htmlspecialchars(get_setting('team_2_education', 'Yale Law School - Juris Doctor')) ?></p>
                                <p class="expert-bar"><?= htmlspecialchars(get_setting('team_2_bar', 'Bar Admissions: NY, CT, NJ')) ?></p>
                                <p class="expert-experience"><?= htmlspecialchars(get_setting('team_2_experience', '20+ Years Experience in Corporate Law')) ?></p>
                                <div class="expert-credentials">
                                    <span class="credential"><?= htmlspecialchars(get_setting('team_2_credential_1', 'AV Preeminent Rating - Martindale-Hubbell')) ?></span>
                                    <span class="credential"><?= htmlspecialchars(get_setting('team_2_credential_2', 'Fortune 500 Legal Counsel')) ?></span>
                    </div>
                </div>
                        </div>
                    </div>
                    <div class="expert-card">
                        <div class="expert-profile">
                            <div class="expert-avatar">
                                <img src="<?= htmlspecialchars(get_setting('team_3_photo', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face')) ?>" alt="<?= htmlspecialchars(get_setting('team_3_name', 'Michael Rodriguez')) ?>" class="expert-img">
                            </div>
                            <div class="expert-info">
                                <h3 class="expert-name"><?= htmlspecialchars(get_setting('team_3_name', 'Michael Rodriguez')) ?></h3>
                                <p class="expert-title"><?= htmlspecialchars(get_setting('team_3_title', 'Partner - Estate Planning')) ?></p>
                                <p class="expert-education"><?= htmlspecialchars(get_setting('team_3_education', 'Columbia Law School - Juris Doctor')) ?></p>
                                <p class="expert-bar"><?= htmlspecialchars(get_setting('team_3_bar', 'Bar Admissions: NY, NJ, PA')) ?></p>
                                <p class="expert-experience"><?= htmlspecialchars(get_setting('team_3_experience', '18+ Years Experience in Estate Planning')) ?></p>
                                <div class="expert-credentials">
                                    <span class="credential"><?= htmlspecialchars(get_setting('team_3_credential_1', 'Best Lawyers - Trusts & Estates')) ?></span>
                                    <span class="credential"><?= htmlspecialchars(get_setting('team_3_credential_2', 'Certified Specialist - Estate Planning')) ?></span>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- About Section -->
    <section class="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2><?= htmlspecialchars($about_content['about_title']) ?></h2>
                    <p><?= htmlspecialchars($about_content['about_description_1']) ?></p>
                    <p><?= htmlspecialchars($about_content['about_description_2']) ?></p>
                    <div class="about-features">
                        <div class="feature">
                            <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <strong><?= htmlspecialchars($about_content['about_feature_1_title']) ?></strong><br>
                                <?= htmlspecialchars($about_content['about_feature_1_description']) ?>
                            </div>
                        </div>
                        <div class="feature">
                            <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <strong><?= htmlspecialchars($about_content['about_feature_2_title']) ?></strong><br>
                                <?= htmlspecialchars($about_content['about_feature_2_description']) ?>
                            </div>
                        </div>
                        <div class="feature">
                            <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <strong><?= htmlspecialchars($about_content['about_feature_3_title']) ?></strong><br>
                                <?= htmlspecialchars($about_content['about_feature_3_description']) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="about-image">
                    <img src="images/team/team-meeting.svg" alt="<?= htmlspecialchars($about_content['about_image_alt']) ?>">
                    <div class="experience-badge">
                        <div class="experience-number"><?= htmlspecialchars($about_content['about_experience_years']) ?>+</div>
                        <div>Years of Excellence</div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Client Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?= htmlspecialchars(get_setting('index_testimonials_title', 'What Our Clients Say')) ?></h2>
                <p class="section-subtitle"><?= htmlspecialchars(get_setting('index_testimonials_subtitle', 'Real results for real people')) ?></p>
            </div>
            <div class="testimonials-grid">
                <!-- Testimonial 1 -->
                <div class="testimonial-item">
                    <div class="testimonial-content">
                        <div class="testimonial-stars">
                            <?php for ($i = 0; $i < (int)get_setting('index_testimonial_1_rating', '5'); $i++): ?>
                            <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="testimonial-text">"<?= htmlspecialchars(get_setting('index_testimonial_1_text', 'CWS Legal Chambers provided exceptional representation in our business litigation case. Their expertise and dedication resulted in a favorable outcome that exceeded our expectations.')) ?>"</p>
                        <div class="testimonial-author">
                            <div class="author-info">
                                <h4><?= htmlspecialchars(get_setting('index_testimonial_1_name', 'Robert Williams')) ?></h4>
                                <span><?= htmlspecialchars(get_setting('index_testimonial_1_title', 'CEO, TechCorp')) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="testimonial-item">
                    <div class="testimonial-content">
                        <div class="testimonial-stars">
                            <?php for ($i = 0; $i < (int)get_setting('index_testimonial_2_rating', '5'); $i++): ?>
                            <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="testimonial-text">"<?= htmlspecialchars(get_setting('index_testimonial_2_text', 'The team\'s attention to detail and strategic approach helped me navigate a complex personal injury case. I couldn\'t have asked for better legal representation.')) ?>"</p>
                        <div class="testimonial-author">
                            <div class="author-info">
                                <h4><?= htmlspecialchars(get_setting('index_testimonial_2_name', 'Maria Garcia')) ?></h4>
                                <span><?= htmlspecialchars(get_setting('index_testimonial_2_title', 'Client')) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="testimonial-item">
                    <div class="testimonial-content">
                        <div class="testimonial-stars">
                            <?php for ($i = 0; $i < (int)get_setting('index_testimonial_3_rating', '5'); $i++): ?>
                            <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="testimonial-text">"<?= htmlspecialchars(get_setting('index_testimonial_3_text', 'Professional, knowledgeable, and results-driven. CWS Legal Chambers helped me secure my family\'s future with comprehensive estate planning services.')) ?>"</p>
                        <div class="testimonial-author">
                            <div class="author-info">
                                <h4><?= htmlspecialchars(get_setting('index_testimonial_3_name', 'David Brown')) ?></h4>
                                <span><?= htmlspecialchars(get_setting('index_testimonial_3_title', 'Client')) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Case Results Section -->
    <section class="case-results">
        <div class="case-results-header">
        <div class="container">
            <div class="section-header">
                    <h2 class="section-title" style="color: white;"><?= htmlspecialchars(get_setting('case_results_section_title', 'Recent Case Results')) ?></h2>
                    <p class="section-subtitle" style="color: rgba(255, 255, 255, 0.9);"><?= htmlspecialchars(get_setting('case_results_section_subtitle', 'Proven track record of successful outcomes')) ?></p>
            </div>
            </div>
        </div>
        <div class="case-results-content">
            <div class="container">
                <div class="case-results-grid">
                    <div class="case-result-item">
                    <div class="result-amount"><?= htmlspecialchars(get_setting('featured_case_1_amount', '$50M')) ?></div>
                    <div class="result-type"><?= htmlspecialchars(get_setting('featured_case_1_type', 'Personal Injury')) ?></div>
                    <div class="result-description"><?= htmlspecialchars(get_setting('featured_case_1_description', 'Medical malpractice case resulting in largest settlement in firm history')) ?></div>
                    <div class="result-year"><?= htmlspecialchars(get_setting('featured_case_1_year', '2024')) ?></div>
                </div>
                    <div class="case-result-item">
                    <div class="result-amount"><?= htmlspecialchars(get_setting('featured_case_2_amount', '$25M')) ?></div>
                    <div class="result-type"><?= htmlspecialchars(get_setting('featured_case_2_type', 'Product Liability')) ?></div>
                    <div class="result-description"><?= htmlspecialchars(get_setting('featured_case_2_description', 'Defective product case against major manufacturer')) ?></div>
                    <div class="result-year"><?= htmlspecialchars(get_setting('featured_case_2_year', '2024')) ?></div>
                </div>
                    <div class="case-result-item">
                    <div class="result-amount"><?= htmlspecialchars(get_setting('featured_case_3_amount', '$15M')) ?></div>
                    <div class="result-type"><?= htmlspecialchars(get_setting('featured_case_3_type', 'Business Litigation')) ?></div>
                    <div class="result-description"><?= htmlspecialchars(get_setting('featured_case_3_description', 'Contract dispute resolution for Fortune 500 client')) ?></div>
                    <div class="result-year"><?= htmlspecialchars(get_setting('featured_case_3_year', '2023')) ?></div>
                </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Practice Areas</h2>
                <p class="section-subtitle">Comprehensive legal services tailored to your needs</p>
            </div>
            <div class="services-grid">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div class="service-icon">
                            <i class="fas fa-gavel"></i>
                    </div>
                        <h3 class="service-title"><?= htmlspecialchars($service['name']) ?></h3>
                        <p class="service-description"><?= htmlspecialchars($service['description']) ?></p>
                        <a href="services.php" class="service-link">Learn More →</a>
                </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback services -->
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3 class="service-title">Business Law</h3>
                        <p class="service-description">Comprehensive legal services for businesses of all sizes, including formation, contracts, and litigation.</p>
                        <a href="services.php" class="service-link">Learn More →</a>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <h3 class="service-title">Personal Injury</h3>
                        <p class="service-description">Dedicated representation for individuals injured due to negligence or wrongful conduct.</p>
                        <a href="services.php" class="service-link">Learn More →</a>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h3 class="service-title">Estate Planning</h3>
                        <p class="service-description">Protect your family's future with comprehensive estate planning and will services.</p>
                        <a href="services.php" class="service-link">Learn More →</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?= htmlspecialchars(get_setting('faq_section_title', 'Frequently Asked Questions')) ?></h2>
                <p class="section-subtitle"><?= htmlspecialchars(get_setting('faq_section_subtitle', 'Get answers to common legal questions')) ?></p>
                            </div>
            <div class="faq-grid">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3><?= htmlspecialchars(get_setting('faq_1_question', 'How much does a consultation cost?')) ?></h3>
                        <i class="fas fa-plus"></i>
                        </div>
                    <div class="faq-answer">
                        <p><?= htmlspecialchars(get_setting('faq_1_answer', 'We offer free initial consultations for all potential clients. During this consultation, we\'ll evaluate your case and discuss your legal options without any obligation.')) ?></p>
                            </div>
                        </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3><?= htmlspecialchars(get_setting('faq_2_question', 'Do you work on a contingency basis?')) ?></h3>
                        <i class="fas fa-plus"></i>
                            </div>
                    <div class="faq-answer">
                        <p><?= htmlspecialchars(get_setting('faq_2_answer', 'Yes, for personal injury cases, we work on a "no win, no fee" basis. You don\'t pay attorney fees unless we successfully recover compensation for you.')) ?></p>
                        </div>
                    </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3><?= htmlspecialchars(get_setting('faq_3_question', 'How long do cases typically take?')) ?></h3>
                        <i class="fas fa-plus"></i>
                </div>
                    <div class="faq-answer">
                        <p><?= htmlspecialchars(get_setting('faq_3_answer', 'Case duration varies depending on complexity. Simple cases may resolve in months, while complex litigation can take years. We\'ll provide realistic timelines during consultation.')) ?></p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3><?= htmlspecialchars(get_setting('faq_4_question', 'What areas of law do you practice?')) ?></h3>
                        <i class="fas fa-plus"></i>
            </div>
                    <div class="faq-answer">
                        <p><?= htmlspecialchars(get_setting('faq_4_answer', 'We specialize in personal injury, business law, estate planning, family law, criminal defense, and employment law. Our experienced team covers most legal needs.')) ?></p>
        </div>
            </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3><?= htmlspecialchars(get_setting('faq_5_question', 'Do you offer payment plans?')) ?></h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p><?= htmlspecialchars(get_setting('faq_5_answer', 'Yes, we understand legal fees can be challenging. We offer flexible payment plans and work with clients to find solutions that fit their budget.')) ?></p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3><?= htmlspecialchars(get_setting('faq_6_question', 'How do I get started with my case?')) ?></h3>
                        <i class="fas fa-plus"></i>
            </div>
                    <div class="faq-answer">
                        <p><?= htmlspecialchars(get_setting('faq_6_answer', 'Simply call us for a free consultation. We\'ll discuss your situation, answer your questions, and explain how we can help. No obligation, just honest legal advice.')) ?></p>
        </div>
            </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
            <h2><?= htmlspecialchars(get_setting('cta_section_title', 'Ready to Get Started?')) ?></h2>
                <p><?= htmlspecialchars(get_setting('cta_section_description', 'Contact us today for a free consultation and let us fight for your rights.')) ?></p>
            <div class="cta-buttons">
                    <a href="<?= htmlspecialchars(get_setting('cta_button_1_url', '#contact')) ?>" class="btn btn-primary"><?= htmlspecialchars(get_setting('cta_button_1_text', 'Get Free Consultation')) ?></a>
                    <a href="tel:<?= htmlspecialchars($settings['site_phone'] ?? '(212) 555-1234') ?>" class="btn btn-secondary"><?= htmlspecialchars(get_setting('cta_button_2_text', 'Call Now: ' . ($settings['site_phone'] ?? '(212) 555-1234'))) ?></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-about">
                    <h3><?= htmlspecialchars($settings['site_name']) ?></h3>
                    <p><?= htmlspecialchars(get_setting('footer_company_description', 'Premier legal services in New York with over 25 years of excellence. We\'re committed to protecting your rights and delivering justice.')) ?></p>
                </div>
                <div class="footer-links">
                    <h4>Practice Areas</h4>
                    <ul>
                        <li><a href="#"><?= htmlspecialchars(get_setting('footer_practice_area_1', 'Personal Injury')) ?></a></li>
                        <li><a href="#"><?= htmlspecialchars(get_setting('footer_practice_area_2', 'Business Law')) ?></a></li>
                        <li><a href="#"><?= htmlspecialchars(get_setting('footer_practice_area_3', 'Estate Planning')) ?></a></li>
                        <li><a href="#"><?= htmlspecialchars(get_setting('footer_practice_area_4', 'Immigration')) ?></a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?= htmlspecialchars(get_setting('footer_link_1_url', 'about.php')) ?>"><?= htmlspecialchars(get_setting('footer_link_1_text', 'About Us')) ?></a></li>
                        <li><a href="<?= htmlspecialchars(get_setting('footer_link_2_url', 'team.php')) ?>"><?= htmlspecialchars(get_setting('footer_link_2_text', 'Our Team')) ?></a></li>
                        <li><a href="<?= htmlspecialchars(get_setting('footer_link_3_url', 'case-results.php')) ?>"><?= htmlspecialchars(get_setting('footer_link_3_text', 'Case Results')) ?></a></li>
                        <li><a href="<?= htmlspecialchars(get_setting('footer_link_4_url', 'contact.php')) ?>"><?= htmlspecialchars(get_setting('footer_link_4_text', 'Contact')) ?></a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Contact Info</h4>
                    <ul>
                        <li><?= htmlspecialchars(get_setting('footer_address', '106 Boulevard Haussmann')) ?></li>
                        <li><?= htmlspecialchars(get_setting('footer_city', 'Paris')) ?>, <?= htmlspecialchars(get_setting('footer_state', '75008')) ?> <?= htmlspecialchars(get_setting('footer_country', 'France')) ?></li>
                        <li><a href="tel:<?= str_replace(['(', ')', ' ', '-'], '', get_setting('footer_phone', '+33 757934452')) ?>"><?= htmlspecialchars(get_setting('footer_phone', '+33 757934452')) ?></a></li>
                        <li><a href="mailto:<?= htmlspecialchars(get_setting('footer_email', 'info@cwslegalchambers.com')) ?>"><?= htmlspecialchars(get_setting('footer_email', 'info@cwslegalchambers.com')) ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p><?= htmlspecialchars(get_setting('footer_copyright', '© ' . date('Y') . ' ' . $settings['site_name'] . '. All rights reserved.')) ?> | <a href="<?= htmlspecialchars(get_setting('footer_privacy_url', 'privacy-policy.php')) ?>" style="color: var(--gold);">Privacy Policy</a> | <a href="<?= htmlspecialchars(get_setting('footer_terms_url', 'terms-of-service.php')) ?>" style="color: var(--gold);">Terms of Service</a></p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded successfully');
            
            // Hamburger menu
            const hamburgerMenu = document.getElementById('hamburger-menu');
            const mainNav = document.getElementById('main-nav');
            
            hamburgerMenu.addEventListener('click', function() {
                hamburgerMenu.classList.toggle('active');
                mainNav.classList.toggle('active');
            });
            
            // Close mobile menu when clicking on a link
            const navLinks = mainNav.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    hamburgerMenu.classList.remove('active');
                    mainNav.classList.remove('active');
                });
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!hamburgerMenu.contains(event.target) && !mainNav.contains(event.target)) {
                    hamburgerMenu.classList.remove('active');
                    mainNav.classList.remove('active');
                }
            });
        });
        
        function toggleSearch() {
            const searchContainer = document.getElementById('searchContainer');
            const searchInput = document.getElementById('searchInput');
            
            searchContainer.classList.toggle('active');
            
            // Focus on input when search opens
            if (searchContainer.classList.contains('active')) {
                setTimeout(() => {
                    searchInput.focus();
                }, 100);
            }
        }
        
        // Close search when clicking outside
        document.addEventListener('click', function(event) {
            const searchContainer = document.getElementById('searchContainer');
            const searchIcon = document.querySelector('.search-icon');
            
            if (!searchContainer.contains(event.target) && !searchIcon.contains(event.target)) {
                searchContainer.classList.remove('active');
            }
        });
        
        // Hero Slider JavaScript
        let currentSlideIndex = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.nav-dot');
        const prevBtn = document.querySelector('.slider-arrow.prev');
        const nextBtn = document.querySelector('.slider-arrow.next');
        
        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            if (slides[index]) {
                slides[index].classList.add('active');
            }
            if (dots[index]) {
                dots[index].classList.add('active');
            }
        }
        
        function nextSlide() {
            currentSlideIndex = (currentSlideIndex + 1) % slides.length;
            showSlide(currentSlideIndex);
        }
        
        function prevSlide() {
            currentSlideIndex = (currentSlideIndex - 1 + slides.length) % slides.length;
            showSlide(currentSlideIndex);
        }
        
        function currentSlide(index) {
            currentSlideIndex = index - 1;
            showSlide(currentSlideIndex);
        }
        
        // Event listeners
        if (nextBtn) nextBtn.addEventListener('click', nextSlide);
        if (prevBtn) prevBtn.addEventListener('click', prevSlide);
        
        // Auto-play slider
        setInterval(nextSlide, 5000);
        
        // Animate progress bars when they come into view
        function animateProgressBars() {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.getAttribute('data-width');
                if (width) {
                    bar.style.width = width + '%';
                }
            });
        }
        
        // Track which counters have been animated
        const animatedCounters = new Set();
        
        // Animate counters when they come into view
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number, .excellence-stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                if (target && target > 0 && !animatedCounters.has(counter)) {
                    // Mark as animated
                    animatedCounters.add(counter);
                    // Reset to 0 first
                    counter.textContent = '0';
                    // Then animate to target
                    animateCounter(counter, target);
                }
            });
        }
        
        // Individual counter animation function
        function animateCounter(element, target, duration = 2000) {
            const start = 0;
            const increment = target / (duration / 16);
            let current = start;
            
            // Clear any existing animation
            if (element.animationTimer) {
                clearInterval(element.animationTimer);
            }
            
            element.animationTimer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(element.animationTimer);
                    element.animationTimer = null;
                }
                element.textContent = Math.floor(current);
            }, 16);
        }
        
        // Intersection Observer for scroll-triggered animations
        const observerOptions = {
            threshold: 0.3,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (entry.target.classList.contains('legal-scenes-stats')) {
                        animateProgressBars();
                    }
                    if (entry.target.classList.contains('stats') || entry.target.classList.contains('proven-excellence')) {
                        // Add a small delay to ensure the section is fully visible
                        setTimeout(() => {
                            animateCounters();
                        }, 100);
                    }
                }
            });
        }, observerOptions);
        
        // Observe sections for animation
        const legalStatsSection = document.querySelector('.legal-scenes-stats');
        const statsSection = document.querySelector('.stats');
        const provenExcellenceSection = document.querySelector('.proven-excellence');
        
        if (legalStatsSection) observer.observe(legalStatsSection);
        if (statsSection) observer.observe(statsSection);
        if (provenExcellenceSection) observer.observe(provenExcellenceSection);
        
        // Also observe individual counter elements for better mobile support
        const counterElements = document.querySelectorAll('.excellence-stat-number, .stat-number');
        counterElements.forEach(counter => {
            observer.observe(counter);
        });
        
        // Mobile-friendly scroll event as backup
        let hasAnimated = false;
        window.addEventListener('scroll', () => {
            if (!hasAnimated) {
                const provenExcellenceSection = document.querySelector('.proven-excellence');
                if (provenExcellenceSection) {
                    const rect = provenExcellenceSection.getBoundingClientRect();
                    const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
                    
                    if (isVisible) {
                        hasAnimated = true;
                        setTimeout(() => {
                            animateCounters();
                        }, 200);
                    }
                }
            }
        });
        
        // FAQ functionality
        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            question.addEventListener('click', () => {
                // Close other open items
                faqItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                    }
                });
                // Toggle current item
                item.classList.toggle('active');
            });
        });
    </script>
</body>
</html>
