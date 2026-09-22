<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Privacy Policy - Fare Price</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: #f4f6f9;
        color: #333;
        line-height: 1.7;
    }

    .header {
        background: linear-gradient(135deg, #007bff, #00c6ff);
        color: #fff;
        padding: 40px 20px;
        text-align: center;
    }

    .header h1 {
        font-size: 32px;
        font-weight: 600;
    }

    .header p {
        margin-top: 10px;
        font-size: 14px;
        opacity: 0.9;
    }

    .container {
        max-width: 1000px;
        margin: -30px auto 30px;
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    h2 {
        font-size: 20px;
        margin-bottom: 10px;
        color: #007bff;
    }

    p {
        font-size: 14px;
        color: #555;
        margin-bottom: 15px;
    }

    ul {
        padding-left: 20px;
        margin-bottom: 20px;
    }

    ul li {
        font-size: 14px;
        margin-bottom: 8px;
    }

    .section {
        margin-bottom: 30px;
    }

    .divider {
        height: 1px;
        background: #eee;
        margin: 25px 0;
    }

    .highlight {
        background: #f1f8ff;
        padding: 15px;
        border-left: 4px solid #007bff;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    a {
        color: #007bff;
        text-decoration: none;
        word-break: break-all;
    }

    .footer {
        text-align: center;
        font-size: 13px;
        color: #888;
        padding: 20px;
    }

    @media(max-width: 600px){
        .header h1 {
            font-size: 24px;
        }

        .container {
            padding: 20px;
        }
    }
</style>

</head>

<body>

<div class="header">
    <h1>Privacy Policy</h1>
    <p>Fare Price - Transparent & Reliable Fare Calculation</p>
</div>

<div class="container">


    <div class="section">
        <h2>About Fare Price</h2>
        <div class="highlight">
            Fare Price, located in Chennai, is a platform designed to simplify fare calculation and transportation-related services.
        </div>
        <p>
            Acting as a reliable digital solution, we help users estimate fares, manage travel-related data, and access relevant services efficiently.
            Our goal is to provide a seamless, transparent, and user-friendly experience.
        </p>
        <p>
            With strong technical capabilities and a focus on accuracy, Fare Price enhances how users calculate and manage travel expenses.
        </p>
    </div>

    <div class="divider"></div>

    <div class="section">
        <h2>Use of Your Personal Data</h2>
        <ul>
            <li>To provide and maintain our Service</li>
            <li>To manage your account</li>
            <li>To handle transactions and support</li>
            <li>To contact you (Email, SMS, Notifications)</li>
            <li>To provide offers and promotions</li>
            <li>To manage support requests</li>
            <li>For business transfers</li>
            <li>For analytics and improvements</li>
        </ul>
    </div>

    <div class="divider"></div>

    <div class="section">
        <h2>Sharing of Your Personal Information</h2>
        <ul>
            <li>With service providers</li>
            <li>During mergers or acquisitions</li>
            <li>With affiliates</li>
            <li>With business partners</li>
            <li>With other users (reviews/comments)</li>
            <li>With your consent</li>
        </ul>
    </div>

    <div class="divider"></div>

    <div class="section">
        <h2>Retention of Data</h2>
        <p>We retain data only as long as necessary for legal and operational purposes.</p>
    </div>

    <div class="divider"></div>

    <div class="section">
        <h2>Location Information</h2>
        <div class="highlight">
            Location data is essential for fare calculation and emergency features.
        </div>
        <ul>
            <li>While app is in use</li>
            <li>Background usage</li>
            <li>Even when closed (with permission)</li>
        </ul>
        <p>We do not sell your location data.</p>
    </div>

    <div class="divider"></div>

    <div class="section">
        <h2>Data Transfer</h2>
        <p>Your data may be transferred securely across regions as required.</p>
    </div>

    <div class="divider"></div>

    <div class="section">
        <h2>User Safety Policy</h2>
        <ul>
            <li>No abusive content allowed</li>
            <li>Report system available</li>
            <li>Reviewed within 24 hours</li>
            <li>Accounts may be suspended</li>
        </ul>
    </div>

    <div class="divider"></div>

    <div class="section">
        <h2>User Content Policy</h2>
        <ul>
            <li>No fake or misleading content</li>
            <li>Violations lead to suspension</li>
        </ul>
    </div>

    <div class="divider"></div>

    <div class="section">
        <h2>Delete Your Account</h2>
        <p>
            {{-- Built from the site's own address, so it follows wherever this is
                 hosted. It used to be typed out as autobazaar.online, which would
                 have pointed back at the old server after a move — and Google Play
                 checks this link when the app is reviewed. --}}
            <a href="{{ route('fareprice.account-delete') }}" target="_blank">
                {{ route('fareprice.account-delete') }}
            </a>
        </p>
        <p>Deletion is immediate after confirmation.</p>
    </div>

    <div class="divider"></div>

    <div class="section">
        <h2>Contact</h2>
        <p><strong>Phone:</strong> 7305335733</p>
    </div>


</div>

<div class="footer">
    © {{date('Y')}} Fare Price. All rights reserved.
</div>

</body>
</html>
