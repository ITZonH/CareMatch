
<!DOCTYPE html>
<!--
Name: Hamed Zon
ID: C00259228
Date: 16/10/2026
Description: This is the first page users will see when they visit CareMatch AI
-->

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CareMatch AI | Healthcare Recruitment</title>

    <!-- connecting the homepage CSS file -->
    <link rel="stylesheet" href="assets/css/home.css">
</head>

<body>

    <!-- displaying the main website header -->
    <header class="site-header">

        <!-- displaying the CareMatch AI branding -->
        <div class="header-container">

            <a href="index.php" class="site-logo">
                CareMatch <span>AI</span>
            </a>

            <!-- navigation links for workers and employers -->
            <nav aria-label="Main navigation">

                <a href="auth/worker_login.php">
                    Worker Login
                </a>

                <a href="auth/employer_login.php">
                    Employer Login
                </a>

                <a href="auth/worker_register.php" class="nav-button">
                    Get Started
                </a>

            </nav>

        </div>

    </header>

    <!-- this section holds the main homepage content -->
    <main>

        <!-- displaying the homepage introduction -->
        <section class="hero-section">

            <div class="hero-container">

                <div class="hero-content">

                    <span class="hero-label">
                        Healthcare Recruitment Platform
                    </span>

                    <!-- displaying the main homepage heading -->
                    <h1>
                        Connecting Healthcare Professionals
                        with the <span>Right Opportunities</span>
                    </h1>

                    <!-- explaining what CareMatch AI does -->
                    <p>
                        CareMatch AI helps healthcare professionals
                        discover career opportunities and connects
                        healthcare organisations with the right talent.
                    </p>

                    <!-- navigation buttons for workers and employers -->
                    <div class="hero-buttons">

                        <a href="auth/worker_register.php"
                           class="primary-button">
                            Find a Job
                        </a>

                        <a href="auth/employer_register.php"
                           class="secondary-button">
                            Hire Healthcare Staff
                        </a>

                    </div>

                    <!-- displaying a short platform message -->
                    <p class="hero-note">
                        A simpler way to connect healthcare talent
                        with healthcare employers.
                    </p>

                </div>

                <!-- displaying the homepage information card -->
                <div class="hero-visual">

                    <div class="visual-card">

                        <div class="visual-icon">+</div>

                        <h2>Welcome to CareMatch AI</h2>

                        <p>
                            One platform connecting healthcare workers
                            and employers.
                        </p>

                        <div class="visual-divider"></div>

                        <div class="visual-feature">
                            <span class="feature-symbol">✓</span>
                            <span>Healthcare Professionals</span>
                        </div>

                        <div class="visual-feature">
                            <span class="feature-symbol">✓</span>
                            <span>Healthcare Employers</span>
                        </div>

                        <div class="visual-feature">
                            <span class="feature-symbol">✓</span>
                            <span>Secure User Accounts</span>
                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- displaying the main user options -->
        <section class="user-section">

            <div class="section-container">

                <div class="section-heading">

                    <h2>How Can We Help You?</h2>

                    <p>
                        Choose the option that best describes you
                        to get started with CareMatch AI.
                    </p>

                </div>

                <div class="user-cards">

                    <!-- information for healthcare workers -->
                    <article class="user-card">

                        <div class="user-icon">+</div>

                        <h3>I'm a Healthcare Professional</h3>

                        <p>
                            Create your account and begin your
                            journey towards new healthcare
                            career opportunities.
                        </p>

                        <a href="auth/worker_register.php"
                           class="card-button">
                            Register as a Worker
                        </a>

                        <p class="card-login">
                            Already registered?
                            <a href="auth/worker_login.php">
                                Log In
                            </a>
                        </p>

                    </article>

                    <!-- information for healthcare employers -->
                    <article class="user-card">

                        <div class="user-icon">▣</div>

                        <h3>I'm a Healthcare Employer</h3>

                        <p>
                            Register your organisation and
                            connect with healthcare professionals
                            through CareMatch AI.
                        </p>

                        <a href="auth/employer_register.php"
                           class="card-button">
                            Register as an Employer
                        </a>

                        <p class="card-login">
                            Already registered?
                            <a href="auth/employer_login.php">
                                Log In
                            </a>
                        </p>

                    </article>

                </div>

            </div>

        </section>

    </main>

    <!-- displaying the website footer -->
    <footer class="site-footer">

        <p>
            &copy; 2026 CareMatch AI. All rights reserved.
        </p>

    </footer>

</body>
</html>
