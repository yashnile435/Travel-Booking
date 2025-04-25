<?php
require_once 'config/config.php';
$page_title = "Previous Destinations";
$isLoginPage = false;
$isBookingPage = false;
$isDestinationsPage = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Bappa Tours and Travels</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js" defer></script>
</head>
<body>
    <!-- Simple header with company name -->
    <header class="simple-header">
        <a href="index.php" class="company-title">
            <img src="assets/images/logo.jpg" alt="Bappa Tours & Travels" class="logo-img">
            <span class="company-name">Bappa Tours & Travels</span>
        </a>
    </header>

    <section class="hero-section inner-hero">
        <div class="container">
            <div class="hero-content">
                <h1>Our Previous Destinations</h1>
                <p>Explore the beautiful places we've visited and get inspired for your next journey</p>
            </div>
        </div>
    </section>

    <?php
    try {
        // Fetch featured destinations
        $stmt = $pdo->prepare("SELECT * FROM destinations WHERE is_featured = 1 ORDER BY visit_date DESC LIMIT 3");
        $stmt->execute();
        $featured_destinations = $stmt->fetchAll();
        
        // Fetch all destinations ordered by date
        $stmt = $pdo->prepare("SELECT * FROM destinations ORDER BY visit_date DESC");
        $stmt->execute();
        $all_destinations = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Error fetching destinations: " . $e->getMessage();
    }
    ?>

    <!-- All Destinations Section -->
    <section class="all-destinations">
        <div class="container">
            <div class="section-header">
                <h2>Our Travel Collection</h2>
                <p>Explore all the amazing places we've been to</p>
            </div>
            
            <?php if(!empty($all_destinations)): ?>
                <div class="destinations-grid">
                    <?php foreach($all_destinations as $destination): ?>
                        <div class="destination-card" id="destination-<?php echo $destination['id']; ?>">
                            <div class="destination-img">
                                <img src="<?php echo htmlspecialchars($destination['image_url']); ?>" alt="<?php echo htmlspecialchars($destination['title']); ?>">
                                <?php if($destination['is_featured']): ?>
                                    <span class="featured-badge">Featured</span>
                                <?php endif; ?>
                            </div>
                            <div class="destination-info">
                                <h3><?php echo htmlspecialchars($destination['title']); ?></h3>
                                <div class="destination-meta">
                                    <div class="location">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($destination['location']); ?>
                                    </div>
                                    <div class="date">
                                        <i class="far fa-calendar-alt"></i> <?php echo date("d M Y", strtotime($destination['visit_date'])); ?>
                                    </div>
                                </div>
                                <div class="destination-description">
                                    <p><?php echo nl2br(htmlspecialchars($destination['description'])); ?></p>
                                </div>
                                
                                <?php if(!empty($destination['highlights'])): ?>
                                    <div class="destination-highlights">
                                        <h4>Highlights:</h4>
                                        <ul>
                                            <?php 
                                            $highlights = explode(',', $destination['highlights']);
                                            foreach($highlights as $highlight): 
                                                $highlight = trim($highlight);
                                                if(!empty($highlight)):
                                            ?>
                                                <li><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($highlight); ?></li>
                                            <?php 
                                                endif;
                                            endforeach; 
                                            ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="button-group">
                                    <button class="btn-share" data-destination="<?php echo htmlspecialchars($destination['title']); ?>" data-url="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'] . '#destination-' . $destination['id']); ?>">
                                        <i class="fas fa-share-alt"></i> Share
                                    </button>
                                    <a href="user/booking.php?location=<?php echo urlencode($destination['location']); ?>" class="btn-book">
                                        <i class="fas fa-car"></i> Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-destinations">
                    <p>No destinations found. Please check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Share Modal -->
    <div class="share-modal" id="shareModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Share this Destination</h3>
            <p id="shareDestinationTitle"></p>
            <div class="share-options">
                <a href="#" class="share-facebook" target="_blank">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>
                <a href="#" class="share-twitter" target="_blank">
                    <i class="fab fa-twitter"></i> Twitter
                </a>
                <a href="#" class="share-whatsapp" target="_blank">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <a href="#" class="share-email">
                    <i class="fas fa-envelope"></i> Email
                </a>
            </div>
            <div class="copy-link">
                <input type="text" id="shareLinkInput" readonly>
                <button id="copyLinkBtn">Copy Link</button>
            </div>
        </div>
    </div>

    <style>
        /* Simple header styles */
        .simple-header {
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 12px 20px;
            text-align: center;
            position: relative;
        }
        
        .company-title {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            gap: 10px;
        }
        
        .logo-img {
            height: 35px;
            width: auto;
            object-fit: contain;
        }
        
        .company-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #54880e;
            font-family: "Times New Roman", Times, serif;
        }
        
        /* Global styles */
        body {
            margin: 0;
            font-family: "Times New Roman", Times, serif;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Root variables */
        :root {
            --primary-color: #54880e;
            --primary-hover: #446e0b;
            --text-dark: #333;
            --text-light: #666;
            --white: #fff;
        }
        
        /* Featured Destinations Section */
        .featured-destinations {
            padding: 60px 0;
            background-color: #f8f9fa;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .section-header h2 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 10px;
        }
        
        .section-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .featured-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .featured-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .featured-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .featured-img {
            height: 250px;
            overflow: hidden;
        }
        
        .featured-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .featured-card:hover .featured-img img {
            transform: scale(1.05);
        }
        
        .featured-info {
            padding: 20px;
        }
        
        .featured-info h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.5rem;
            color: #333;
        }
        
        .destination-location, .destination-date {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            color: #666;
            font-size: 0.95rem;
        }
        
        .destination-location i, .destination-date i {
            margin-right: 8px;
            color: var(--primary-color);
        }
        
        /* All Destinations Section */
        .all-destinations {
            padding: 60px 0;
        }
        
        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .destination-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            position: relative;
        }
        
        .destination-card:hover {
            transform: translateY(-5px);
        }
        
        .destination-img {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .destination-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .destination-card:hover .destination-img img {
            transform: scale(1.05);
        }
        
        .featured-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--primary-color);
            color: white;
            padding: 5px 10px;
            font-size: 0.8rem;
            border-radius: 20px;
        }
        
        .destination-info {
            padding: 20px;
        }
        
        .destination-info h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.4rem;
            color: #333;
        }
        
        .destination-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .destination-meta i {
            color: var(--primary-color);
            margin-right: 5px;
        }
        
        .destination-description {
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .destination-highlights {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .destination-highlights h4 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.1rem;
            color: #333;
        }
        
        .destination-highlights ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .destination-highlights li {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }
        
        .destination-highlights li i {
            color: var(--primary-color);
            margin-right: 8px;
        }
        
        .btn-share {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px 10px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            transition: color 0.3s ease;
        }
        
        .btn-share:hover {
            color: var(--primary-color);
        }
        
        .btn-share i {
            margin-right: 5px;
        }
        
        /* Book Now button styles */
        .btn-book {
            background: var(--primary-color);
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px 10px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            text-decoration: none;
            border-radius: 4px;
            margin-left: 10px;
        }
        
        .btn-book:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }
        
        .btn-book i {
            margin-right: 5px;
        }
        
        /* Button container */
        .button-group {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }
        
        .destination-info {
            display: flex;
            flex-direction: column;
        }
        
        .destination-info > button,
        .destination-info > a {
            display: inline-flex;
        }
        
        /* Add a flex container for the buttons */
        .destination-card .destination-info {
            position: relative;
        }
        
        .destination-card .btn-share,
        .destination-card .btn-book {
            margin-top: 10px;
        }
        
        /* Responsive adjustments for buttons */
        @media (max-width: 576px) {
            .destination-card .btn-share,
            .destination-card .btn-book {
                padding: 8px 12px;
                font-size: 1rem;
            }
            
            .button-group {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .btn-book {
                margin-left: 0;
            }
        }
        
        /* Share Modal */
        .share-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }
        
        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        .share-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        
        .share-options a {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        
        .share-facebook {
            background-color: #3b5998;
        }
        
        .share-twitter {
            background-color: #1da1f2;
        }
        
        .share-whatsapp {
            background-color: #25d366;
        }
        
        .share-email {
            background-color: #ea4335;
        }
        
        .copy-link {
            display: flex;
            margin-top: 15px;
        }
        
        .copy-link input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
        }
        
        .copy-link button {
            padding: 10px 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }
        
        .hero-section.inner-hero {
            height: 40vh;
            min-height: 300px;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/images/destinations-hero.jpg') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
            margin-top: 0;
        }
        
        .hero-section.inner-hero h1 {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .hero-section.inner-hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Responsive styling */
        @media (max-width: 768px) {
            .hero-section.inner-hero {
                min-height: 250px;
            }
            
            .hero-section.inner-hero h1 {
                font-size: 2.5rem;
            }
            
            .hero-section.inner-hero p {
                font-size: 1.1rem;
            }
        }
        
        @media (max-width: 576px) {
            .hero-section.inner-hero {
                min-height: 200px;
            }
            
            .hero-section.inner-hero h1 {
                font-size: 2rem;
            }
            
            .hero-section.inner-hero p {
                font-size: 1rem;
            }
            
            .company-name {
                font-size: 1.1rem;
            }
        }
        
        @media (max-width: 360px) {
            .hero-section.inner-hero h1 {
                font-size: 1.8rem;
            }
            
            .logo-img {
                height: 30px;
            }
        }
        
        @media (max-width: 992px) {
            .featured-grid, .destinations-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .hero-section.inner-hero h1 {
                font-size: 2.5rem;
            }
            
            .featured-grid, .destinations-grid {
                grid-template-columns: 1fr;
            }
            
            .share-options {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Share modal functionality
            const shareModal = document.getElementById('shareModal');
            const shareButtons = document.querySelectorAll('.btn-share');
            const closeModal = document.querySelector('.close-modal');
            const shareLinkInput = document.getElementById('shareLinkInput');
            const copyLinkBtn = document.getElementById('copyLinkBtn');
            const shareDestinationTitle = document.getElementById('shareDestinationTitle');
            
            // Share buttons click event
            shareButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const destination = this.getAttribute('data-destination');
                    const url = window.location.origin + window.location.pathname + this.getAttribute('data-url');
                    
                    // Set modal content
                    shareDestinationTitle.textContent = destination;
                    shareLinkInput.value = url;
                    
                    // Set social media share links
                    document.querySelector('.share-facebook').href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                    document.querySelector('.share-twitter').href = `https://twitter.com/intent/tweet?text=Check out ${encodeURIComponent(destination)}&url=${encodeURIComponent(url)}`;
                    document.querySelector('.share-whatsapp').href = `https://api.whatsapp.com/send?text=${encodeURIComponent(destination + ' - ' + url)}`;
                    document.querySelector('.share-email').href = `mailto:?subject=Check out this destination: ${encodeURIComponent(destination)}&body=${encodeURIComponent('I thought you might be interested in this destination: ' + url)}`;
                    
                    // Show modal
                    shareModal.style.display = 'flex';
                });
            });
            
            // Close modal
            closeModal.addEventListener('click', function() {
                shareModal.style.display = 'none';
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === shareModal) {
                    shareModal.style.display = 'none';
                }
            });
            
            // Copy link button
            copyLinkBtn.addEventListener('click', function() {
                shareLinkInput.select();
                document.execCommand('copy');
                
                // Change button text temporarily
                const originalText = this.textContent;
                this.textContent = 'Copied!';
                setTimeout(() => {
                    this.textContent = originalText;
                }, 2000);
            });
            
            // Smooth scroll to destination
            const scrollLinks = document.querySelectorAll('.scroll-to');
            scrollLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>

    <!-- QR Code Section -->
    <div class="qr-code-container">
        <div class="qr-content">
            <h3>Scan for Navigation</h3>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=https://maps.app.goo.gl/SuKGRwG1pr2Ft2aA7" alt="Branch Address QR Code" class="qr-image">
            <p>Scan to navigate to our branch location</p>
        </div>
    </div>

    <style>
        .qr-code-container {
            background-color: #f9f9f9;
            padding: 40px 0;
            text-align: center;
            margin-top: 40px;
            border-top: 1px solid #eee;
        }

        .qr-content {
            max-width: 300px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .qr-content h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .qr-image {
            width: 200px;
            height: 200px;
            margin: 10px auto;
            display: block;
            border: 1px solid #eee;
            padding: 5px;
        }

        .qr-content p {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .qr-content {
                max-width: 260px;
                padding: 15px;
            }

            .qr-image {
                width: 180px;
                height: 180px;
            }
        }
    </style>
</body>
</html> 