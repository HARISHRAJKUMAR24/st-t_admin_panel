<?php
require_once './config/config.php';

// Get category type from URL
 $categoryType = isset($_GET['type']) ? urldecode($_GET['type']) : '';

if (empty($categoryType)) {
    header('Location: ' . SITE_URL);
    exit;
}

// Get category details
 $category = getPackageTypeByName($pdo, $categoryType);

if (!$category) {
    $category = [
        'name' => $categoryType,
        'type_id' => 'PKT000',
        'image' => null,
        'image_url' => getDefaultCategoryImage($categoryType)
    ];
} else {
    $category['image_url'] = !empty($category['image']) ? ADMIN_URL . $category['image'] : getDefaultCategoryImage($category['name']);
}

// Get tours for this category
 $tours = getToursByPackageType($pdo, $categoryType);

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
  <title><?= htmlspecialchars($category['name']); ?> Tours - <?= $siteTitle; ?></title>
  <link rel="stylesheet" href="./assets/css/styles.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/styles.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/category-detail.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/navbar.css">
</head>

<body>

  <?php include './includes/navbar.php'; ?>

  <!-- Hero — image sits behind the fixed navbar -->
  <section class="cat-hero" id="catHero">
    <a href="<?= SITE_URL; ?>" class="cat-back">
      <span class="cat-back-circle"><i class="bi bi-arrow-left"></i></span>
      <span class="back-text">All Categories</span>
    </a>

    <div class="cat-hero-bg">
      <img src="<?= $category['image_url']; ?>" alt="<?= htmlspecialchars($category['name']); ?>" loading="eager">
    </div>
    <div class="cat-hero-overlay"></div>

    <div class="cat-hero-content">
      <div class="cat-hero-inner">
        <div class="cat-hero-badge">
          <i class="bi bi-compass-fill"></i>
          Tour Category
        </div>
        <h1 class="cat-hero-title"><?= htmlspecialchars($category['name']); ?></h1>
        <p class="cat-hero-sub">Explore our handpicked <?= strtolower($category['name']); ?> experiences curated just for you</p>
      </div>
    </div>

    <div class="cat-scroll-hint">
      <span>Scroll</span>
      <div class="cat-scroll-line"></div>
    </div>
  </section>

  <!-- Toolbar -->
  <div class="cat-toolbar">
    <div class="cat-toolbar-left">
      <div class="cat-count-chip">
        <i class="bi bi-grid-3x3-gap-fill"></i>
        <?= count($tours); ?> Tours Found
        <span class="chip-label"><?= htmlspecialchars($category['name']); ?></span>
      </div>
    </div>
    <div class="cat-toolbar-right">
      <button class="cat-view-btn active" id="gridViewBtn" onclick="setView('grid')" title="Grid view" aria-label="Grid view">
        <i class="bi bi-grid-3x3-gap-fill"></i>
      </button>
      <button class="cat-view-btn" id="listViewBtn" onclick="setView('list')" title="List view" aria-label="List view">
        <i class="bi bi-list-ul"></i>
      </button>
    </div>
  </div>

  <!-- Tours -->
  <div class="cat-content">
    <?php if (!empty($tours)): ?>
      <div class="cat-grid" id="catGrid">
        <?php foreach ($tours as $tour):
          $mainImage = !empty($tour['main_image']) ? ADMIN_URL . $tour['main_image'] : SITE_URL . 'assets/images/default-tour.jpg';
          $tourLink = SITE_URL . 'tour.php?id=' . ($tour['id'] ?? $tour['package_id']);
        ?>
          <a href="<?= $tourLink; ?>" class="cat-tour-card">
            <div class="cat-card-image">
              <img src="<?= $mainImage; ?>" alt="<?= htmlspecialchars($tour['package_name']); ?>" loading="lazy">
              <div class="cat-card-image-gradient"></div>
              <div class="cat-card-type"><?= htmlspecialchars($tour['package_type'] ?? $category['name']); ?></div>
              <?php if (!empty($tour['days_count'])): ?>
              <div class="cat-card-duration">
                <i class="bi bi-calendar3"></i>
                <?= getTourDuration($tour['days_count']); ?>
              </div>
              <?php endif; ?>
              <div class="cat-card-arrow">
                <i class="bi bi-arrow-up-right"></i>
              </div>
            </div>
            <div class="cat-card-body">
              <h3 class="cat-card-title"><?= htmlspecialchars($tour['package_name']); ?></h3>
              <p class="cat-card-desc"><?= htmlspecialchars($tour['small_description'] ?? $tour['short_description'] ?? 'Discover this amazing tour experience curated just for you.'); ?></p>
              <div class="cat-card-divider"></div>
              <div class="cat-card-footer">
                <div class="cat-card-price">
                  <?php if (!empty($tour['price'])): ?>
                    ₹<?= number_format($tour['price'], 0); ?>
                    <small>/person</small>
                  <?php else: ?>
                    <span class="cat-card-contact">Contact for Price</span>
                  <?php endif; ?>
                </div>
                <div class="cat-card-cta">
                  View <i class="bi bi-arrow-right"></i>
                </div>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="cat-empty">
        <div class="cat-empty-icon">
          <i class="bi bi-airplane"></i>
        </div>
        <h3>No <em><?= htmlspecialchars($category['name']); ?></em> Tours Yet</h3>
        <p>We're crafting amazing <?= strtolower($category['name']); ?> tours.<br>Check back soon or explore other categories.</p>
        <a href="<?= SITE_URL; ?>categories.php" class="cat-empty-btn">
          Explore All Categories
          <span class="btn-circle"><i class="bi bi-compass"></i></span>
        </a>
      </div>
    <?php endif; ?>
  </div>

  <?php include './includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Lenis Smooth Scroll -->
  <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
  <script>
    // ---- Lenis smooth scroll ----
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

    // ---- Hero zoom-in on load ----
    window.addEventListener('load', function() {
      document.getElementById('catHero').classList.add('cat-loaded');
    });

    // ---- Intersection Observer for card fade-up ----
    const tourCards = document.querySelectorAll('.cat-tour-card');
    if (tourCards.length) {
      const cardObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('cat-visible');
            cardObserver.unobserve(entry.target);
          }
        });
      }, {
        threshold: 0.10,
        rootMargin: '0px 0px -30px 0px'
      });

      tourCards.forEach(function(card) {
        cardObserver.observe(card);
      });
    }

    // ---- Grid / List toggle ----
    function setView(mode) {
      var grid = document.getElementById('catGrid');
      var gridBtn = document.getElementById('gridViewBtn');
      var listBtn = document.getElementById('listViewBtn');

      if (mode === 'list') {
        grid.classList.add('list-view');
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
      } else {
        grid.classList.remove('list-view');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
      }

      // Re-trigger visibility for cards already in view
      tourCards.forEach(function(card) {
        var rect = card.getBoundingClientRect();
        if (rect.top < window.innerHeight && rect.bottom > 0) {
          card.classList.add('cat-visible');
        }
      });
    }

    // ---- Navbar transparency via CSS class ----
    const navbar = document.querySelector('nav');
    const hero = document.getElementById('catHero');

    if (navbar && hero) {
      function updateNavClass() {
        var heroBottom = hero.offsetTop + hero.offsetHeight;

        if (window.scrollY < heroBottom - 100) {
          navbar.classList.add('nav-over-hero');
        } else {
          navbar.classList.remove('nav-over-hero');
        }
      }

      window.addEventListener('scroll', updateNavClass, { passive: true });
      // Set initial state immediately
      updateNavClass();
    }
  </script>
</body>
</html>