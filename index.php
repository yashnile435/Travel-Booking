<?php
require_once('config/config.php');
// Add this debug line temporarily
// error_log('Index page session: ' . print_r($_SESSION, true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bappa Tours and Travels</title>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <style>
        :root {
            --primary-font: "Times New Roman", Times, serif;
            --primary-color: #54880e;  /* Green color */
            --primary-hover: #446e0b;  /* Darker green for hover */
            --text-dark: #333;
            --text-light: #666;
            --white: #fff;
            --light-green: rgba(84,136,14,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--primary-font);
        }

        .slider-container {
            width: 100%;
            height: 80vh;
            padding: 20px;
        }

        .swiper {
            width: 100%;
            height: 100%;
        }

        .swiper-slide {
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: #f8f8f8;
            overflow: hidden;
        }

        .swiper-slide img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .header {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: white;
            margin-top: 0;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.2em;
            color: #ddd;
        }

        /* New navbar styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .company-name {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--text-dark);
            font-family: var(--primary-font);
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: color 0.3s ease;
            font-family: var(--primary-font);
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .login-btn {
            padding: 0.5rem 1.5rem;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .login-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(68, 110, 11, 0.2);
            color: var(--white);
        }

        /* Update Home Section Styles */
        .home {
            height: 100vh;
            width: 100%;
            overflow: hidden;
            padding-top: 80px;
            background-color: #f5f5f5;
        }

        .slide {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 10%;
            min-height: calc(100vh - 80px);
        }

        /* Add different background gradients for each slide */
        .slide-one {
            background: linear-gradient(to right, #fff 60%, var(--light-green) 40%);
        }

        .slide-two {
            background: linear-gradient(to right, #fff 60%, var(--light-green) 40%);
        }

        .slide-three {
            background: linear-gradient(to right, #fff 60%, var(--light-green) 40%);
        }

        .slide-four {
            background: linear-gradient(to right, #fff 60%, var(--light-green) 40%);
        }

        .col-1 {
            flex: 1;
            padding-right: 5%;
        }

        .col-2 {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .col-2 img {
            max-width: 100%;
            height: auto;
            animation: float 3s ease-in-out infinite;
        }

        .col-1 h4 {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .col-1 h1 {
            font-size: 3.5rem;
            color: #333;
            margin-bottom: 2rem;
            line-height: 1.2;
        }

        @keyframes float {
            0% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0); }
        }

        /* Cars Section Styles */
        .cars {
            background: linear-gradient(135deg, #fff 0%, var(--light-green) 100%);
            padding: 4rem 5%;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: var(--text-dark);
            margin-bottom: 3rem;
            font-family: var(--primary-font);
            position: relative;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .section-title::before {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 10px;
            background: var(--primary-color);
            border-radius: 5px;
        }

        .car-card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
            height: 450px;
            display: flex;
            flex-direction: column;
            border: 1px solid var(--light-green);
            position: relative;
        }

        .car-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            border-color: var(--primary-color);
            background-color: rgba(84, 136, 14, 0.1);
        }

        .car-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-bottom: 2px solid #f0f0f0;
            transition: transform 0.3s ease;
        }

        .car-card:hover .car-image {
            transform: scale(1.05);
        }

        .car-details {
            padding: 1.5rem;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(to bottom, #fff, #f8f8f8);
            height: 150px;
        }

        .car-details h3 {
            font-size: 1.8rem;
            color: var(--text-dark);
            margin-bottom: 0.8rem;
            font-family: var(--primary-font);
            position: relative;
            padding-bottom: 10px;
        }

        .car-details h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .car-details p {
            color: var(--text-light);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 1rem;
            font-family: var(--primary-font);
        }

        .book-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .book-btn:hover {
            background: var(--primary-hover);
        }

        /* Enhanced About Section Styles */
        .about {
            padding: 100px 0;
            background: linear-gradient(135deg, #fff 0%, var(--light-green) 100%);
            position: relative;
            overflow: hidden;
        }

        .about::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 30% 50%, rgba(84,136,14,0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
        }

        .about h2 {
            font-size: 3.5rem;
            text-align: center;
            color: var(--text-dark);
            margin-bottom: 3rem;
            position: relative;
            padding-bottom: 20px;
            font-weight: bold;
        }

        .about h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #54880e, #007bff);
            border-radius: 2px;
        }

        .about-text {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .about-text p {
            font-size: 1.2rem;
            line-height: 1.8;
            color: #555;
            margin-bottom: 2rem;
            text-align: justify;
        }

        .highlights {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            margin-top: 50px;
        }

        .highlight-item {
            background: white;
            padding: 35px 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--light-green);
        }

        .highlight-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(84,136,14,0.1) 0%, rgba(0,123,255,0.1) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .highlight-item:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--primary-color);
        }

        .highlight-item:hover::before {
            opacity: 1;
        }

        .highlight-item i {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            transition: all 0.4s ease;
            position: relative;
        }

        .highlight-item:hover i {
            transform: scale(1.2) rotate(10deg);
            color: #007bff;
        }

        .highlight-item h4 {
            font-size: 1.4rem;
            color: #333;
            margin-bottom: 1rem;
            font-weight: 600;
            position: relative;
        }

        .highlight-item p {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
            margin: 0;
        }

        /* Add animation for highlight items */
        .highlight-item:nth-child(1) { animation: fadeInUp 0.5s ease forwards; }
        .highlight-item:nth-child(2) { animation: fadeInUp 0.5s ease 0.2s forwards; }
        .highlight-item:nth-child(3) { animation: fadeInUp 0.5s ease 0.4s forwards; }
        .highlight-item:nth-child(4) { animation: fadeInUp 0.5s ease 0.6s forwards; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom styles for each highlight item */
        .highlight-item:nth-child(1) i { color: #ffc107; }
        .highlight-item:nth-child(2) i { color: #54880e; }
        .highlight-item:nth-child(3) i { color: #007bff; }
        .highlight-item:nth-child(4) i { color: #dc3545; }

        /* Responsive Design */
        @media (max-width: 992px) {
            .highlights {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            
            .about h2 {
                font-size: 3rem;
            }
        }

        @media (max-width: 768px) {
            .about {
                padding: 60px 0;
            }
            
            .about-text p {
                font-size: 1.1rem;
                line-height: 1.6;
            }
            
            .highlight-item {
                padding: 25px 20px;
            }
            
            .highlight-item i {
                font-size: 2.5rem;
            }
            
            .highlight-item h4 {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 576px) {
            .highlights {
                grid-template-columns: 1fr;
                max-width: 320px;
                margin: 30px auto 0;
            }
            
            .about h2 {
                font-size: 2.5rem;
            }
            
            .about-text p {
                font-size: 1rem;
            }
        }

        /* Enhanced Footer Styles */
        .footer {
            background: linear-gradient(135deg, #f8f9fa, rgba(84,136,14,0.1));
            padding: 60px 0 30px;
            color: #333;
            position: relative;
            box-shadow: 0 -5px 15px rgba(84,136,14,0.1);
            /* border-top: 3px solid var(--primary-color); */
        }

        .footer::before {
            display: none;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .social-contact {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(84,136,14,0.1);
            margin-bottom: 30px;
            border-left: 4px solid var(--primary-color);
        }

        .contact-info {
            display: flex;
            gap: 30px;
        }

        .contact-link {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #333;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 10px;
            background: rgba(84,136,14,0.05);
            transition: all 0.3s ease;
            border: 1px solid rgba(84,136,14,0.1);
        }

        .contact-link:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
            color: white;
        }

        .contact-link:hover i, .contact-link:hover box-icon {
            color: white;
        }

        .contact-link i {
            font-size: 1.2rem;
            color: var(--primary-color);
            transition: color 0.3s ease;
        }

        /* Social Links with Tooltip Effect */
        .social-links {
            position: relative;
        }

        .wrapper {
            display: inline-flex;
        }

        .wrapper .icon {
            margin: 0 5px;
            text-align: center;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            position: relative;
            z-index: 2;
            text-decoration: none;
            transition: 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .wrapper .icon span {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 45px;
            width: 45px;
            color: var(--primary-color);
            background: rgba(84,136,14,0.05);
            border-radius: 50%;
            position: relative;
            z-index: 2;
            box-shadow: 0px 5px 10px rgba(84,136,14,0.1);
            transition: 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border: 1px solid rgba(84,136,14,0.1);
        }

        .wrapper .icon span i {
            font-size: 20px;
        }

        .wrapper .icon .tooltip {
            position: absolute;
            top: 0;
            z-index: 1;
            background: #fff;
            color: #fff;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 25px;
            opacity: 0;
            pointer-events: none;
            box-shadow: 0px 10px 10px rgba(0, 0, 0, 0.1);
            transition: 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .wrapper .icon:hover .tooltip {
            top: -45px;
            opacity: 1;
            pointer-events: auto;
        }

        .icon .tooltip:before {
            position: absolute;
            content: "";
            height: 10px;
            width: 10px;
            background: inherit;
            left: 50%;
            bottom: -5px;
            transform: translateX(-50%) rotate(45deg);
        }

        .wrapper .icon:hover span {
            color: #fff;
        }

        .wrapper .icon:hover span,
        .wrapper .icon:hover .tooltip {
            text-shadow: 0px -1px 0px rgba(0, 0, 0, 0.4);
        }

        .wrapper .facebook:hover span,
        .wrapper .facebook:hover .tooltip {
            background: #3b5999;
        }

        .wrapper .instagram:hover span,
        .wrapper .instagram:hover .tooltip {
            background: #e1306c;
        }

        .wrapper .justdial:hover span,
        .wrapper .justdial:hover .tooltip {
            background: #f0b31a;
        }

        .wrapper .email:hover span,
        .wrapper .email:hover .tooltip {
            background: #ea4335;
        }

        .wrapper .card:hover span,
        .wrapper .card:hover .tooltip {
            background: #54880e;
        }

        .wrapper .icon .jd-text {
            font-weight: bold;
            font-size: 16px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(84,136,14,0.2);
        }

        .footer-bottom p {
            color: #446e0b;
            font-size: 0.9rem;
        }

        /* Responsive adjustments for footer */
        @media (max-width: 768px) {
            .social-contact {
                flex-direction: column;
                gap: 20px;
            }

            .contact-info {
                flex-direction: column;
                gap: 15px;
                width: 100%;
            }

            .contact-link {
                width: 100%;
                justify-content: center;
            }

            .wrapper {
                margin-top: 15px;
            }

            .wrapper .icon {
                margin: 0 8px;
            }
        }

        @media (max-width: 480px) {
            .wrapper .icon span {
                height: 40px;
                width: 40px;
            }
            
            .wrapper .icon span i {
                font-size: 18px;
            }
            
            .wrapper .icon {
                margin: 0 5px;
            }
        }

        /* Update Car Swiper Styles */
        .car-swiper {
            padding: 20px 10px 50px !important;
        }

        .car-swiper .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background: var(--primary-color);
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .car-swiper .swiper-pagination-bullet-active {
            opacity: 1;
            width: 30px;
            border-radius: 6px;
        }

        .car-swiper .swiper-button-next,
        .car-swiper .swiper-button-prev {
            color: var(--primary-color);
            background: var(--white);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .car-swiper .swiper-button-next:after,
        .car-swiper .swiper-button-prev:after {
            font-size: 18px;
        }

        .car-swiper .swiper-button-next:hover,
        .car-swiper .swiper-button-prev:hover {
            background: var(--primary-color);
            color: var(--white);
        }

        /* Add responsive adjustments */
        @media (max-width: 768px) {
            .car-card {
                height: 400px;
            }

            .car-image {
                height: 250px;
            }

            .car-details {
                height: 150px;
                padding: 1rem;
            }

            .car-details h3 {
                font-size: 1.5rem;
            }

            .car-details p {
                font-size: 1rem;
            }
        }

        /* Update Swiper Navigation */
        .swiper-button-next,
        .swiper-button-prev {
            color: var(--primary-color) !important;
            background: var(--white);
            width: 40px !important;
            height: 40px !important;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: var(--primary-color);
            color: var(--white) !important;
        }

        .swiper-pagination-bullet {
            background: var(--primary-color) !important;
            opacity: 0.5;
        }

        .swiper-pagination-bullet-active {
            opacity: 1;
            width: 20px !important;
            border-radius: 5px;
        }

        /* Update Email Container Styles */
        .email-container {
            position: relative;
        }

        .email-tooltip {
            display: none;
        }

        /* Add animation for modal opening */
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .gallery-modal.active .modal-content-wrapper {
            animation: modalFadeIn 0.3s ease forwards;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .swiper-slide {
                flex-direction: column;
            }

            .col-1, .col-2 {
                width: 100%;
                padding: 0 20px;
            }

            .col-1 h1 {
                font-size: 2.5rem;
            }

            .col-1 h4 {
                font-size: 1.2rem;
            }
        }

        /* Featured Destinations Section Styles */
        .featured-destinations-section {
            padding: 80px 0;
            background-color: #f8f9fa;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .section-header h2 {
            font-size: 2.5rem;
            color: var(--text-dark);
            margin-bottom: 10px;
        }
        
        .section-header p {
            color: var(--text-light);
            font-size: 1.1rem;
        }
        
        .featured-destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .destination-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .destination-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .destination-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .destination-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .destination-card:hover .destination-image img {
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
        
        .destination-details {
            padding: 20px;
        }
        
        .destination-details h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.4rem;
            color: var(--text-dark);
        }
        
        .destination-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: var(--text-light);
        }
        
        .destination-meta i {
            color: var(--primary-color);
            margin-right: 5px;
        }
        
        .destination-excerpt {
            margin-bottom: 15px;
            line-height: 1.6;
            color: var(--text-light);
        }
        
        .view-more-btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }
        
        .view-more-btn:hover {
            background-color: var(--primary-hover);
        }
        
        .view-all {
            text-align: center;
        }
        
        .view-all-btn {
            display: inline-block;
            padding: 10px 25px;
            background-color: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .view-all-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .no-destinations, .error {
            text-align: center;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .error {
            color: #dc3545;
        }
        
        @media (max-width: 768px) {
            .featured-destinations-grid {
                grid-template-columns: 1fr;
            }
            
            .destination-meta {
                flex-direction: column;
                gap: 5px;
            }
        }

        .destination-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .view-more-btn, .book-now-btn {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .view-more-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .view-more-btn:hover {
            background-color: var(--primary-hover);
        }

        .book-now-btn {
            background-color: #007bff;
            color: white;
        }

        .book-now-btn:hover {
            background-color: #0056b3;
        }

        /* Business Card Modal Styles */
        .gallery-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.95);
            z-index: 2000;
            opacity: 0;
            transition: all 0.3s ease;
            padding: 0;
        }

        .gallery-modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
        }

        .modal-content-wrapper {
            position: relative;
            max-width: 500px;
            width: 90%;
            margin: 0 auto;
            text-align: center;
            transform: scale(0.9);
            transition: transform 0.3s ease;
            padding: 20px;
        }

        .gallery-modal.active .modal-content-wrapper {
            transform: scale(1);
        }

        .gallery-image {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.3);
            display: block;
            margin: 0 auto;
        }

        .gallery-close {
            position: absolute;
            top: -50px;
            right: 0;
            color: #fff;
            font-size: 2.5rem;
            cursor: pointer;
            transition: transform 0.3s ease;
            background: rgba(255,255,255,0.1);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2001;
        }

        .gallery-close:hover {
            transform: rotate(90deg);
        }

        .download-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            padding: 12px 25px;
            background: var(--primary-color);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: auto;
            min-width: 200px;
        }

        .download-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-3px);
        }
        
        @media (max-width: 768px) {
            .modal-content-wrapper {
                width: 95%;
                padding: 15px;
            }

            .gallery-close {
                top: -40px;
                right: 0;
            }
            
            .gallery-image {
                max-height: 70vh;
            }
        }

        .car-info {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 0.9rem;
            color: var(--text-light);
        }
        
        .car-info span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .car-info i {
            color: var(--primary-color);
        }
        
        .car-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-top: 10px;
            font-weight: 500;
        }
        
        .car-status.available {
            background: #d1fae5;
            color: #059669;
        }
        
        .car-status.unavailable {
            background: #fee2e2;
            color: #dc2626;
        }
    </style>
</head>
<body>
    <?php 
    $isLoginPage = false;  // Set this before including navbar
    include 'includes/navbar.php'; 
    ?>

    <!-- New Home Section with Improved Slider -->
    <div class="home" id="home">
        <div class="swiper myHome">
            <div class="swiper-wrapper">
                <div class="swiper-slide slide slide-one">
                    <div class="col-1">
                        <h4>Welcome to Bappa Tours and Travels</h4>
                        <h1>Best way to book the car</h1>
                    </div>
                    <div class="col-2">
                        <img src="assets/images/democar1.png" alt="Car Image 1">
                    </div>
                </div>
                <div class="swiper-slide slide">
                    <div class="col-1">
                        <h4>Welcome to Bappa Tours and Travels</h4>
                        <h1>Premium Car Services</h1>
                    </div>
                    <div class="col-2">
                        <img src="assets/images/democar2.png" alt="Car Image 2">
                    </div>
                </div>
                <div class="swiper-slide slide">
                    <div class="col-1">
                        <h4>Welcome to Bappa Tours and Travels</h4>
                        <h1>Comfortable Journey</h1>
                    </div>
                    <div class="col-2">
                        <img src="assets/images/democar3.png" alt="Car Image 3">
                    </div>
                </div>
                <div class="swiper-slide slide">
                    <div class="col-1">
                        <h4>Welcome to Bappa Tours and Travels</h4>
                        <h1>Comfortable Journey</h1>
                    </div>
                    <div class="col-2">
                        <img src="assets/images/democar4.png" alt="Car Image 4">
                    </div>
                </div>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>

    <!-- Cars Section -->
    <section class="cars" id="cars">
        <h2 class="section-title">Our Cars</h2>
        <div class="swiper car-swiper">
            <div class="swiper-wrapper">
                <?php
                try {
                    // Fetch all cars from the database
                    $stmt = $pdo->query("SELECT * FROM cars ORDER BY car_name");
                    $cars = $stmt->fetchAll();
                    
                    if (!empty($cars)) {
                        foreach ($cars as $car) {
                            ?>
                            <div class="swiper-slide">
                                <div class="car-card">
                                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['car_name']); ?>" class="car-image">
                                    <div class="car-details">
                                        <h3><?php echo htmlspecialchars($car['car_name']); ?></h3>
                                        <p><?php echo htmlspecialchars($car['description']); ?></p>
                                        <div class="car-info">
                                            <span><i class="fas fa-users"></i> <?php echo htmlspecialchars($car['passengers']); ?> passengers</span>
                                        </div>
                                        <?php if ($car['is_available']) : ?>
                                            <span class="car-status available">Available</span>
                                        <?php else : ?>
                                            <span class="car-status unavailable">Not Available</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="swiper-slide"><div class="car-card"><div class="car-details"><h3>No cars found</h3></div></div></div>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="swiper-slide"><div class="car-card"><div class="car-details"><h3>Error loading cars</h3></div></div></div>';
                    error_log("Error fetching cars: " . $e->getMessage());
                }
                ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
        <style>
            .car-info {
                display: flex;
                justify-content: space-between;
                margin-top: 10px;
                font-size: 0.9rem;
                color: var(--text-light);
            }
            
            .car-info span {
                display: flex;
                align-items: center;
                gap: 5px;
            }
            
            .car-info i {
                color: var(--primary-color);
            }
            
            .car-status {
                display: inline-block;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 0.8rem;
                margin-top: 10px;
                font-weight: 500;
            }
            
            .car-status.available {
                background: #d1fae5;
                color: #059669;
            }
            
            .car-status.unavailable {
                background: #fee2e2;
                color: #dc2626;
            }
        </style>
    </section>

    <!-- Featured Destinations Section -->
    <section class="featured-destinations-section" id="destinations">
        <div class="container">
            <div class="section-header">
                <h2>Our Featured Destinations</h2>
                <p>Explore some of the beautiful places we've traveled to</p>
            </div>
            
            <div class="destinations-wrapper">
                <?php
                try {
                    // Fetch featured destinations
                    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE is_featured = 1 ORDER BY visit_date DESC LIMIT 3");
                    $stmt->execute();
                    $featured_destinations = $stmt->fetchAll();
                    
                    if (!empty($featured_destinations)) {
                        echo '<div class="featured-destinations-grid">';
                        foreach ($featured_destinations as $destination) {
                            ?>
                            <div class="destination-card">
                                <div class="destination-image">
                                    <img src="<?php echo htmlspecialchars($destination['image_url']); ?>" alt="<?php echo htmlspecialchars($destination['title']); ?>">
                                    <span class="featured-badge">Featured</span>
                                </div>
                                <div class="destination-details">
                                    <h3><?php echo htmlspecialchars($destination['title']); ?></h3>
                                    <div class="destination-meta">
                                        <div class="location">
                                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($destination['location']); ?>
                                        </div>
                                        <div class="date">
                                            <i class="far fa-calendar-alt"></i> <?php echo date("d M Y", strtotime($destination['visit_date'])); ?>
                                        </div>
                                    </div>
                                    <p class="destination-excerpt"><?php echo substr(htmlspecialchars($destination['description']), 0, 120); ?>...</p>
                                    <div class="destination-buttons">
                                        <a href="destinations.php#destination-<?php echo $destination['id']; ?>" class="view-more-btn">View Details</a>
                                        <a href="user/booking.php?location=<?php echo urlencode($destination['location']); ?>" class="book-now-btn">Book Now</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        echo '</div>';
                        echo '<div class="view-all"><a href="destinations.php" class="view-all-btn">View All Destinations</a></div>';
                    } else {
                        echo '<p class="no-destinations">No featured destinations found.</p>';
                    }
                } catch (PDOException $e) {
                    echo '<p class="error">Error fetching destinations: ' . $e->getMessage() . '</p>';
                }
                ?>
            </div>
        </div>
    </section>
    
    <!-- About Us Section -->
    <section class="about" id="about">
        <div class="about-container">
            <h2>About Us</h2>
            <div class="about-content">
                <div class="about-text">
                    <p>Welcome to Bappa Tours and Travels, your premier destination for exceptional travel experiences. Since 2002, we've been dedicated to providing reliable, comfortable, and affordable car rental services that exceed expectations.</p>
                    <p>Our commitment to excellence, coupled with our well-maintained fleet and professional service, ensures that every journey with us is memorable and hassle-free. Whether you're traveling for business or leisure, we're here to make your trip special.</p>
                    <div class="highlights">
                        <div class="highlight-item">
                            <i class="fas fa-award"></i>
                            <h4>23+ Years Experience</h4>
                            <p>Trusted service provider since 2002</p>
                        </div>
                        <div class="highlight-item">
                        <i class="fas fa-car"></i>
                            <h4>Premium Fleet</h4>
                            <p>Wide range of well-maintained vehicles</p>
                        </div>
                        <div class="highlight-item">
                            <i class="fas fa-headset"></i>
                            <h4>24/7 Support</h4>
                            <p>Always here when you need us</p>
                        </div>
                        <div class="highlight-item">
                            <i class="fas fa-shield-alt"></i>
                            <h4>Safe & Reliable</h4>
                            <p>Your safety is our priority</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer" id="contact">
        <div class="footer-container">

            <!-- About Us Section -->

            <div class="social-contact">
                <div class="contact-info">
                    <a href="tel:+919011333966" class="contact-link">
                    <box-icon type='solid' name='phone' color='#fff'></box-icon>
                        <span>+91 9011333966</span>
                    </a>
                    <a href="https://wa.me/919011333966" target="_blank" class="contact-link">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>
                    <a href="https://maps.app.goo.gl/SuKGRwG1pr2Ft2aA7" target="_blank" class="contact-link">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Find Us</span>
                    </a>
                </div>
                <div class="social-links">
                    <div class="wrapper">
                        <a href="https://www.justdial.com/Jalgaon/Bappa-Tours-Travels-Near-Neharu-Chowk-Pratap-Nagar/9999PX257-X257-180911143524-V6F1_BZDET" 
                           target="_blank" 
                           class="icon justdial">
                            <div class="tooltip">JustDial</div>
                            <span><span class="jd-text">JD</span></span>
                        </a>
                        <a href="https://www.facebook.com/profile.php?id=100093140499818" 
                           target="_blank" 
                           class="icon facebook">
                            <div class="tooltip">Facebook</div>
                            <span><i class="fab fa-facebook-f"></i></span>
                        </a>
                        <a href="https://www.instagram.com/bappa_travels_15?igsh=OWw3bmwxczI2bGZy" 
                           target="_blank" 
                           class="icon instagram">
                            <div class="tooltip">Instagram</div>
                            <span><i class="fab fa-instagram"></i></span>
                        </a>
                        <a href="mailto:travels.bappa15@gmail.com?subject=Inquiry%20for%20Car%20Booking&body=Hello%20Bappa%20Tours%20and%20Travels" 
                           class="icon email">
                            <div class="tooltip">Email</div>
                            <span><i class="far fa-envelope"></i></span>
                        </a>
                        <a href="#" 
                           class="icon card" 
                           id="viewBusinessCard">
                            <div class="tooltip">Business Card</div>
                            <span><i class="fas fa-id-card"></i></span>
                        </a>
                    </div>
                    
                    <div class="gallery-modal" id="galleryModal">
                        <span class="gallery-close">&times;</span>
                        <div class="modal-content-wrapper">
                            <img src="./assets/images/card.jpg" alt="Business Card" class="gallery-image" id="businessCardImage">
                            <a href="./assets/images/card.jpg" download="card.jpg" class="download-btn" id="downloadCard">
                                <i class="fas fa-download"></i> Download Card
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Bappa Tours and Travels. All rights reserved.</p>
            </div>
        </div>
    </footer>


    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        // Home Slider
        const homeSwiper = new Swiper('.myHome', {
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            effect: 'slide',
            slidesPerView: 1,
            spaceBetween: 0,
            breakpoints: {
                640: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 1,
                },
                1024: {
                    slidesPerView: 1,
                }
            }
        });

        // Cars Slider with Auto-play
        const carSwiper = new Swiper('.car-swiper', {
            loop: true,
            slidesPerView: 1,
            spaceBetween: 20,
            centeredSlides: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            },
            effect: "coverflow",
            coverflowEffect: {
                rotate: 0,
                stretch: 0,
                depth: 100,
                modifier: 2,
                slideShadows: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                dynamicBullets: true
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                968: {
                    slidesPerView: 3,
                    centeredSlides: false
                }
            }
        });

        // Add hover pause functionality
        const carSwiperEl = document.querySelector('.car-swiper');
        carSwiperEl.addEventListener('mouseenter', () => {
            carSwiper.autoplay.stop();
        });
        carSwiperEl.addEventListener('mouseleave', () => {
            carSwiper.autoplay.start();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const viewCardBtn = document.getElementById('viewBusinessCard');
            const modal = document.getElementById('galleryModal');
            const closeBtn = document.querySelector('.gallery-close');
            const downloadBtn = document.getElementById('downloadCard');

            // Open modal and trigger download
            viewCardBtn.addEventListener('click', function(e) {
                e.preventDefault();
                modal.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
                
                // Trigger download after a delay
                setTimeout(() => {
                    downloadBtn.click();
                }, 1000);
            });

            // Close modal
            function closeModal() {
                modal.classList.remove('active');
                document.body.style.overflow = ''; // Restore scrolling
            }

            closeBtn.addEventListener('click', closeModal);

            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });

            // Prevent modal close when clicking on content
            document.querySelector('.modal-content-wrapper').addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Close modal with escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    closeModal();
                }
            });
        });
    </script>
</body>
</html> 