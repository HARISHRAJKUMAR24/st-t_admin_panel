<?php
require_once './config/config.php';

 $allCategories = getCategoriesWithPackageImages($pdo);
 $siteTitle = getSiteTitle($pdo);
 $siteName = getSiteName($pdo);
 $footerText = getFooterText($pdo);
 $logo = getWebsiteLogo($pdo);
 $favicon = getFavicon($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="<?= $favicon; ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Source+Serif+4:ital,wght@0,400;0,600;1,400;1,600&display=swap" rel="stylesheet">
  <title>All Categories - <?= $siteTitle; ?></title>
  <link rel="stylesheet" href="./assets/css/styles.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/styles.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/categories.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/navbar.css">
</head>

<body>
  <?php include './includes/navbar.php'; ?>

  <!-- Hero Banner — image covers behind navbar -->
  <section class="categories-hero">
    <div class="categories-hero-bg">
<img
    src="<?= SITE_URL; ?>assets/images/category.jpeg"
    alt="Categories Hero"
    loading="eager"
>    </div>
    <div class="categories-hero-overlay"></div>

    <div class="categories-hero-content">
      <div class="categories-hero-badge">
        <i class="bi bi-compass"></i>
        <span>Explore All</span>
      </div>
      <h1 class="categories-hero-title">
        All Tour <span class="italic">Categories</span>
      </h1>
      <p class="categories-hero-sub">
        Discover our diverse range of curated travel experiences across the world
      </p>
    </div>

    <div class="categories-scroll-hint">
      <span>Scroll</span>
      <div class="categories-scroll-line"></div>
    </div>
  </section>

  <!-- Categories Grid -->
  <section class="categories-section">
    <div class="categories-section-inner">
      <div class="categories-grid">
        <?php foreach ($allCategories as $category): 
          $tourCount = count(getToursByPackageType($pdo, $category['name']));
        ?>
          <a href="<?= SITE_URL; ?>category.php?type=<?= urlencode($category['name']); ?>" class="cat-card">
            <div class="cat-card-image">
              <img src="<?= $category['image_url']; ?>" alt="<?= htmlspecialchars($category['name']); ?>" loading="lazy">
              <div class="cat-card-image-overlay"></div>
            </div>
            <div class="cat-card-body">
              <h3 class="cat-card-name"><?= htmlspecialchars($category['name']); ?></h3>
              <div class="cat-card-meta">
                <span class="cat-card-count">
                  <i class="bi bi-layers"></i>
                  <?= $tourCount; ?> Tour<?= $tourCount !== 1 ? 's' : ''; ?>
                </span>
                <span class="cat-card-arrow">
                  <i class="bi bi-arrow-right"></i>
                </span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="categories-back-wrap">
        <a href="<?= SITE_URL; ?>" class="categories-back-btn">
          Back to Home
          <span class="back-arrow"><i class="bi bi-arrow-left"></i></span>
        </a>
      </div>
    </div>
  </section>

  <?php include './includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Lenis Smooth Scroll -->
  <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
  <script>
    // Initialize Lenis smooth scroll
    const lenis = new Lenis({
      duration: 1.2,
      easing: function(t) {
        return Math.min(1, 1.001 - Math.pow(2, -10 * t));
      },
      orientation: 'vertical',
      smoothWheel: true,
    });

    function raf(time) {
      lenis.raf(time);
      requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);

    // Intersection Observer for fade-up cards
    const catCards = document.querySelectorAll('.cat-card');
    if (catCards.length) {
      const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('cat-visible');
            observer.unobserve(entry.target);
          }
        });
      }, {
        threshold: 0.12,
        rootMargin: '0px 0px -40px 0px'
      });

      catCards.forEach(function(card) {
        observer.observe(card);
      });
    }

    // Make navbar transparent on this page when at top
    const navbar = document.querySelector('nav');
    if (navbar) {
      function updateNavbar() {
        if (window.scrollY < 80) {
          navbar.style.background = 'transparent';
          navbar.style.boxShadow = 'none';
          // Make nav links white when on hero
      
        } else {
          navbar.style.background = '';
          navbar.style.boxShadow = '';
          var navLinks = navbar.querySelectorAll('.nav-link, .navbar-brand, .bi');
          navLinks.forEach(function(el) {
            el.style.color = '';
          });
        }
      }
      window.addEventListener('scroll', updateNavbar, { passive: true });
      updateNavbar();
    }
  </script>
</body>
</html>