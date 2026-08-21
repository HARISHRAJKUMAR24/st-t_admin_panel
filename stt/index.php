<?php
require_once './config/config.php';

// Get the hero image URL directly
$heroImage = getHeroImage($pdo);
$siteName = getSiteName($pdo);
$siteTitle = getSiteTitle($pdo);
$footerText = getFooterText($pdo);
$logo = getWebsiteLogo($pdo);
$favicon = getFavicon($pdo);
$travelPackages = getTravelPackages($pdo, 6);
$allTourNames = getAllTourNames($pdo);
$carRentals = getCarRentals($pdo, 6);

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
  <title><?= $siteTitle; ?></title>
  <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
  <link rel="stylesheet" href="./assets/css/styles.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/styles.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/navbar.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/tour-categories.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/dream-cta.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/stories.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/travel-packages.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/car-rentals.css">
</head>

<style>
  /* =========================================================
   SEARCH CONTAINER & DROPDOWN
========================================================= */

  .search-container {
    position: relative;
    width: 100%;
    max-width: 580px;
    margin: 0 auto;
    z-index: 100;
  }

  /* Search Bar */
  .search-bar {
    display: flex;
    align-items: center;
    gap: 0;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.6);
    border-radius: 60px;
    padding: 6px 6px 6px 24px;
    box-shadow:
      0 4px 30px rgba(0, 0, 0, 0.08),
      0 1px 3px rgba(0, 0, 0, 0.04);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .search-bar:focus-within {
    border-color: rgba(255, 255, 255, 0.9);
    box-shadow:
      0 8px 40px rgba(0, 0, 0, 0.12),
      0 2px 6px rgba(0, 0, 0, 0.06);
    transform: translateY(-2px);
  }

  .search-bar.has-results {
    border-radius: 24px 24px 0 0;
    border-bottom-color: transparent;
  }

  .search-icon {
    color: #999;
    font-size: 18px;
    flex-shrink: 0;
    margin-right: 12px;
  }

  .search-bar input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    font-family: 'Inter', sans-serif;
    font-size: 15px;
    font-weight: 400;
    color: #1a1a1a;
    padding: 12px 0;
    min-width: 0;
  }

  .search-bar input::placeholder {
    color: #aaa;
    font-weight: 400;
  }

  .search-clear-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: none;
    background: transparent;
    color: #888;
    font-size: 14px;
    cursor: pointer;
    border-radius: 50%;
    transition: all 0.2s ease;
    flex-shrink: 0;
  }

  .search-clear-btn:hover {
    background: rgba(0, 0, 0, 0.05);
    color: #555;
  }

  .search-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border: none;
    background: #1a1a1a;
    color: #fff;
    font-size: 18px;
    cursor: pointer;
    border-radius: 50%;
    transition: all 0.25s ease;
    flex-shrink: 0;
  }

  .search-btn:hover {
    background: #333;
    transform: scale(1.05);
  }

  /* Search Dropdown */
  .search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.6);
    border-top: 1px solid rgba(0, 0, 0, 0.06);
    border-radius: 0 0 24px 24px;
    box-shadow:
      0 20px 60px rgba(0, 0, 0, 0.15),
      0 4px 12px rgba(0, 0, 0, 0.06);
    max-height: 440px;
    overflow: hidden;
    display: none;
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .search-dropdown.active {
    display: block;
    opacity: 1;
    transform: translateY(0);
  }

  /* Dropdown Header */
  .search-dropdown-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px 12px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
  }

  .search-dropdown-title {
    font-family: 'Inter', sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .search-dropdown-count {
    font-family: 'Inter', sans-serif;
    font-size: 11px;
    font-weight: 500;
    color: #aaa;
    background: #f5f5f5;
    padding: 3px 10px;
    border-radius: 20px;
  }

  /* Dropdown Results */
  .search-dropdown-results {
    max-height: 320px;
    overflow-y: auto;
    padding: 8px;
  }

  .search-dropdown-results::-webkit-scrollbar {
    width: 6px;
  }

  .search-dropdown-results::-webkit-scrollbar-track {
    background: transparent;
  }

  .search-dropdown-results::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 3px;
  }

  .search-dropdown-results::-webkit-scrollbar-thumb:hover {
    background: #bbb;
  }

  /* Search Result Item */
  .search-result-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
  }

  .search-result-item:hover {
    background: #f8f8f6;
  }

  .search-result-item.active {
    background: #f0efed;
  }

  .search-result-image {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
    background: #f0ebe6;
  }

  .search-result-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .search-result-info {
    flex: 1;
    min-width: 0;
  }

  .search-result-category {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 600;
    color: #e9a88d;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 4px;
  }

  .search-result-name {
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: #1a1a1a;
    line-height: 1.3;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .search-result-name mark {
    background: rgba(233, 168, 141, 0.3);
    color: inherit;
    padding: 0 2px;
    border-radius: 3px;
  }

  .search-result-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 12px;
    color: #888;
  }

  .search-result-meta span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .search-result-meta i {
    font-size: 11px;
    color: #ccc;
  }

  .search-result-price {
    text-align: right;
    flex-shrink: 0;
  }

  .search-result-price .price {
    font-family: 'Inter', sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: #1a1a1a;
  }

  .search-result-price .price small {
    font-size: 10px;
    font-weight: 400;
    color: #999;
  }

  .search-result-arrow {
    color: #ccc;
    font-size: 16px;
    flex-shrink: 0;
    transition: all 0.2s ease;
  }

  .search-result-item:hover .search-result-arrow {
    color: #1a1a1a;
    transform: translateX(3px);
  }

  /* Empty State */
  .search-dropdown-empty {
    padding: 40px 20px;
    text-align: center;
  }

  .search-empty-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #f5f5f3;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
  }

  .search-empty-icon i {
    font-size: 22px;
    color: #ccc;
  }

  .search-dropdown-empty p {
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    font-weight: 500;
    color: #555;
    margin: 0 0 6px;
  }

  .search-dropdown-empty span {
    font-size: 12px;
    color: #aaa;
  }

  /* Loading State */
  .search-dropdown-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 40px 20px;
  }

  .search-spinner {
    width: 24px;
    height: 24px;
    border: 2px solid #eee;
    border-top-color: #1a1a1a;
    border-radius: 50%;
    animation: searchSpin 0.8s linear infinite;
  }

  @keyframes searchSpin {
    to {
      transform: rotate(360deg);
    }
  }

  .search-dropdown-loading span {
    font-size: 13px;
    color: #888;
  }

  /* Dropdown Footer */
  .search-dropdown-footer {
    padding: 12px 16px;
    border-top: 1px solid rgba(0, 0, 0, 0.06);
  }

  .search-view-all {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 16px;
    background: #f8f8f6;
    border-radius: 12px;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: #555;
    text-decoration: none;
    transition: all 0.2s ease;
  }

  .search-view-all:hover {
    background: #f0efed;
    color: #1a1a1a;
  }

  .search-view-all i {
    font-size: 14px;
    transition: transform 0.2s ease;
  }

  .search-view-all:hover i {
    transform: translateX(3px);
  }

  /* =========================================================
   SEARCH RESPONSIVE
========================================================= */

  @media (max-width: 767px) {
    .search-container {
      max-width: 100%;
    }

    .search-bar {
      padding: 4px 4px 4px 18px;
      border-radius: 50px;
    }

    .search-bar.has-results {
      border-radius: 20px 20px 0 0;
    }

    .search-bar input {
      font-size: 14px;
      padding: 10px 0;
    }

    .search-icon {
      font-size: 16px;
      margin-right: 10px;
    }

    .search-btn {
      width: 42px;
      height: 42px;
      font-size: 16px;
    }

    .search-dropdown {
      border-radius: 0 0 20px 20px;
      max-height: 380px;
    }

    .search-result-item {
      padding: 10px;
      gap: 10px;
    }

    .search-result-image {
      width: 52px;
      height: 52px;
      border-radius: 10px;
    }

    .search-result-name {
      font-size: 13px;
    }

    .search-result-meta {
      font-size: 11px;
      gap: 8px;
    }

    .search-result-price .price {
      font-size: 13px;
    }

    .search-result-arrow {
      display: none;
    }
  }

  @media (max-width: 480px) {
    .search-bar {
      padding: 3px 3px 3px 14px;
    }

    .search-bar input {
      font-size: 13px;
      padding: 9px 0;
    }

    .search-icon {
      font-size: 14px;
      margin-right: 8px;
    }

    .search-clear-btn {
      width: 32px;
      height: 32px;
      font-size: 12px;
    }

    .search-btn {
      width: 38px;
      height: 38px;
      font-size: 14px;
    }

    .search-dropdown {
      border-radius: 0 0 16px 16px;
      max-height: 340px;
    }

    .search-dropdown-header {
      padding: 12px 14px 10px;
    }

    .search-result-item {
      padding: 8px;
      gap: 8px;
    }

    .search-result-image {
      width: 46px;
      height: 46px;
    }

    .search-result-name {
      font-size: 12px;
    }

    .search-result-meta {
      flex-wrap: wrap;
      gap: 6px;
    }

    .search-result-price .price {
      font-size: 12px;
    }
  }

  /* =========================================================
   TOUR HIGHLIGHT EFFECT
========================================================= */

  .tour-card-highlight {
    animation: tourHighlight 2s ease;
    box-shadow: 0 0 0 3px rgba(233, 168, 141, 0.5) !important;
  }

  @keyframes tourHighlight {
    0% {
      box-shadow: 0 0 0 0 rgba(233, 168, 141, 0.6);
    }

    30% {
      box-shadow: 0 0 0 8px rgba(233, 168, 141, 0.3);
    }

    60% {
      box-shadow: 0 0 0 12px rgba(233, 168, 141, 0.1);
    }

    100% {
      box-shadow: 0 0 0 0 rgba(233, 168, 141, 0);
    }
  }

  /* Section highlight glow */
  .section-highlight::before {
    content: '';
    position: absolute;
    top: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 60%;
    height: 40px;
    background: radial-gradient(ellipse, rgba(233, 168, 141, 0.3), transparent);
    pointer-events: none;
    animation: sectionGlow 2s ease forwards;
  }

  @keyframes sectionGlow {
    0% {
      opacity: 0;
    }

    30% {
      opacity: 1;
    }

    100% {
      opacity: 0;
    }
  }

  .hero {
    background-image: url("<?= $heroImage; ?>");
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
  }

  .travel-footer {
    position: relative;
    width: 100%;
    min-height: 760px;
    overflow: hidden;
    color: #fff;
    background-image: url("<?= SITE_URL; ?>assets/images/footer.png");
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    isolation: isolate;
  }
</style>

<body>

  <!-- ====== NAVBAR ====== -->
  <?php include './includes/navbar.php'; ?>


  <!-- ====== HERO SECTION ====== -->
  <?php include './sections/hero.php'; ?>


  <!-- ====== TOUR CATEGORIES ====== -->
  <section id="categories">
    <?php include './sections/tour-categories.php'; ?>
  </section>


  <!-- ====== OUR PROMISE TO YOU ====== -->
  <section id="about">
    <?php include './sections/promise.php'; ?>
  </section>


  <!-- ====== FEATURED TOURS ====== -->
  <section id="tours">
    <?php include './sections/featured-tours.php'; ?>
  </section>


  <!-- ====== TRAVEL PACKAGES ====== -->
  <section id="travel">
    <?php include './sections/car-rental-booking.php'; ?>
  </section>


  <!-- ====== CAR TTravel Booking ====== -->
     <section id="travel-booking">
    <?php include './sections/travel-booking.php'; ?>
  </section>


  <!-- ====== PLAN YOUR DREAM JOURNEY CTA ====== -->
  <section id="offers">
    <?php include './sections/dream-cta.php'; ?>
  </section>


  <!-- ====== TOP DESTINATIONS ====== -->
  <section id="destinations">
    <?php include './sections/top-destinations.php'; ?>
  </section>


  <!-- ====== HOW WE PLAN YOUR JOURNEY ====== -->
  <section id="journey">
    <?php include './sections/journey-plan.php'; ?>
  </section>


  <!-- ====== OUR JOURNEY IN NUMBERS ====== -->
  <section id="stats">
    <?php include './sections/stats.php'; ?>
  </section>


  <!-- ====== STORIES / TESTIMONIALS ====== -->
  <section id="stories">
    <?php include './sections/stories.php'; ?>
  </section>


  <!-- ====== FINAL CTA ====== -->
  <section id="footer">
    <?php include './sections/world-waiting.php'; ?>
  </section>


  <!-- ====== FOOTER ====== -->
  <?php include './includes/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.TOUR_SEARCH_DATA = <?= json_encode(getTourPackages($pdo, 0, 'active')); ?>;
  </script>
  <script>
    setTimeout(function() {
      (function() {
        var input = document.getElementById('searchInput');
        var dropdown = document.getElementById('searchDropdown');
        var resultsDiv = document.getElementById('searchResults');
        var countEl = document.getElementById('searchCount');
        var emptyEl = document.getElementById('searchEmpty');
        var emptyQueryEl = document.getElementById('searchEmptyQuery');
        var clearBtn = document.getElementById('searchClearBtn');
        var searchBtnEl = document.getElementById('searchBtn');
        var container = document.getElementById('searchContainer');
        var adminUrl = '<?= ADMIN_URL; ?>';
        var siteUrl = '<?= SITE_URL; ?>';
        var data = window.TOUR_SEARCH_DATA || [];

        if (!input || !dropdown) {
          return;
        }

        input.addEventListener('input', function() {
          var q = this.value.toLowerCase().trim();
          if (q.length < 2) {
            dropdown.classList.remove('active');
            if (clearBtn) clearBtn.style.display = 'none';
            return;
          }
          if (clearBtn) clearBtn.style.display = 'flex';

          var found = data.filter(function(t) {
            return (t.package_name || '').toLowerCase().indexOf(q) !== -1 ||
              (t.package_type || '').toLowerCase().indexOf(q) !== -1 ||
              (t.short_description || '').toLowerCase().indexOf(q) !== -1;
          });

          if (found.length === 0) {
            resultsDiv.style.display = 'none';
            emptyEl.style.display = 'block';
            if (emptyQueryEl) emptyQueryEl.textContent = q;
            if (countEl) countEl.textContent = '0 results';
          } else {
            emptyEl.style.display = 'none';
            resultsDiv.style.display = 'block';
            if (countEl) countEl.textContent = found.length + ' found';
            var html = '';
            for (var i = 0; i < found.length; i++) {
              var t = found[i];
              var img = t.main_image ?
                (t.main_image.indexOf('http') === 0 ? t.main_image : adminUrl + t.main_image) :
                siteUrl + 'assets/images/default-tour.jpg';
              var days = parseInt(t.days_count) || 1;
              var price = parseFloat(t.price) || 0;
              var priceText = price > 0 ? '\u20B9' + price.toFixed(2) : 'Contact us';

              html += '<div class="search-result-item" data-tour-id="' + t.id + '" data-tour-name="' + t.package_name + '">';
              html += '<div class="search-result-image"><img src="' + img + '" alt="' + t.package_name + '" loading="lazy"></div>';
              html += '<div class="search-result-info">';
              html += '<div class="search-result-category"><i class="bi bi-tag"></i> ' + (t.package_type || 'Tour') + '</div>';
              html += '<div class="search-result-name">' + t.package_name + '</div>';
              html += '<div class="search-result-meta"><span><i class="bi bi-clock"></i> ' + days + 'D / ' + (days - 1) + 'N</span></div>';
              html += '</div>';
              html += '<div class="search-result-price"><span class="price">' + priceText + '</span></div>';
              html += '<i class="bi bi-arrow-right search-result-arrow"></i>';
              html += '</div>';
            }
            resultsDiv.innerHTML = html;

            var items = resultsDiv.querySelectorAll('.search-result-item');
            for (var j = 0; j < items.length; j++) {
              (function(item) {
                item.addEventListener('click', function() {
                  input.value = this.getAttribute('data-tour-name');
                  dropdown.classList.remove('active');
                  if (clearBtn) clearBtn.style.display = 'none';
                  var sec = document.getElementById('featuredTours');
                  if (sec) {
                    window.scrollTo({
                      top: sec.offsetTop - 80,
                      behavior: 'smooth'
                    });
                  }
                });
              })(items[j]);
            }
          }
          dropdown.classList.add('active');
        });

        if (clearBtn) {
          clearBtn.addEventListener('click', function() {
            input.value = '';
            this.style.display = 'none';
            dropdown.classList.remove('active');
            input.focus();
          });
        }

        if (searchBtnEl) {
          searchBtnEl.addEventListener('click', function() {
            input.dispatchEvent(new Event('input'));
            input.focus();
          });
        }

        document.addEventListener('click', function(e) {
          if (container && !container.contains(e.target)) {
            dropdown.classList.remove('active');
          }
        });

        input.addEventListener('keydown', function(e) {
          if (e.key === 'Escape') {
            dropdown.classList.remove('active');
            input.blur();
          }
        });

      })();
    }, 200);
  </script>
  <script src="<?= SITE_URL; ?>assets/js/main.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {

      const menuBtn =
        document.getElementById("travelMenuBtn");

      const menu =
        document.getElementById("travelMenu");

      const closeBtn =
        document.getElementById("travelMenuClose");

      const backdrop =
        document.querySelector(".travel-menu-backdrop");

      const menuLinks =
        document.querySelectorAll(".menu-scroll-link");


      if (!menuBtn || !menu) {
        return;
      }


      /* =====================================================
         OPEN MENU
         ===================================================== */

      function openMenu() {

        menu.classList.add("active");

        menu.setAttribute(
          "aria-hidden",
          "false"
        );

        menuBtn.setAttribute(
          "aria-expanded",
          "true"
        );

        document.body.classList.add(
          "travel-menu-open"
        );

      }


      /* =====================================================
         CLOSE MENU
         ===================================================== */

      function closeMenu() {

        menu.classList.remove("active");

        menu.setAttribute(
          "aria-hidden",
          "true"
        );

        menuBtn.setAttribute(
          "aria-expanded",
          "false"
        );

        document.body.classList.remove(
          "travel-menu-open"
        );

      }


      menuBtn.addEventListener(
        "click",
        openMenu
      );


      closeBtn.addEventListener(
        "click",
        closeMenu
      );


      backdrop.addEventListener(
        "click",
        closeMenu
      );


      /* =====================================================
         ESC KEY
         ===================================================== */

      document.addEventListener(
        "keydown",
        function(event) {

          if (
            event.key === "Escape" &&
            menu.classList.contains("active")
          ) {

            closeMenu();

          }

        }
      );


      /* =====================================================
         MENU SECTION NAVIGATION
         ===================================================== */

      menuLinks.forEach(function(link) {

        link.addEventListener(
          "click",
          function(event) {

            const href =
              this.getAttribute("href");

            if (!href) {
              return;
            }


            /*
             * Get section ID
             */

            const hash =
              href.split("#")[1];

            if (!hash) {
              return;
            }


            const target =
              document.getElementById(hash);


            /*
             * Section exists on current page
             */

            if (target) {

              event.preventDefault();

              closeMenu();

              setTimeout(function() {

                target.scrollIntoView({
                  behavior: "smooth",
                  block: "start"
                });

              }, 300);

              return;
            }


            /*
             * Section doesn't exist on current page.
             *
             * Go to homepage with hash.
             * Homepage JS below will smooth-scroll.
             */

            closeMenu();

          }
        );

      });


      /* =====================================================
         SMOOTH SCROLL AFTER COMING FROM ANOTHER PAGE
         ===================================================== */

      const hash =
        window.location.hash;


      if (hash) {

        const target =
          document.querySelector(hash);

        if (target) {

          /*
           * Wait until page is fully rendered.
           */

          setTimeout(function() {

            target.scrollIntoView({
              behavior: "smooth",
              block: "start"
            });

          }, 400);

        }

      }

    });
  </script>
</body>

</html>