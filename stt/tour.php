<?php
require_once './config/config.php';

// Get tour ID from URL
$tourId = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($tourId)) {
  header('Location: ' . SITE_URL . 'tours.php');
  exit;
}

// Get tour details
$tour = getTourPackage($pdo, $tourId);

if (!$tour) {
  header('Location: ' . SITE_URL . 'tours.php');
  exit;
}

// Get additional data
$mainImage     = getTourMainImage($pdo, $tour['main_image']);
$galleryImages = getTourGalleryImages($pdo, $tour['gallery_images']);
$features      = getTourFeatures($pdo, $tour['features']);
$itinerary     = getTourItinerary($pdo, $tour['itinerary']);

// =========================================================
// TOUR PRICE + OFFER
// =========================================================

$basePrice = (float)($tour['price'] ?? 0);

// Get the correct tour ID
$currentTourId = $tour['id'] ?? $tour['package_id'] ?? $tourId;

// Find active offer for this tour
$tourOffer = getTourOffer($pdo, $currentTourId);

// Calculate discounted price
$priceData = calculateTourOfferPrice($basePrice, $tourOffer);

$originalPrice = $priceData['original_price'];
$discountAmount = $priceData['discount_amount'];
$finalPrice = $priceData['final_price'];
$discountText = $priceData['discount_text'];
$hasOffer = $priceData['has_offer'];

$siteTitle  = getSiteTitle($pdo);
$siteName   = getSiteName($pdo);
$footerText = getFooterText($pdo);
$logo       = getWebsiteLogo($pdo);
$favicon    = getFavicon($pdo);
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
  <title><?= htmlspecialchars($tour['package_name']); ?> - <?= $siteTitle; ?></title>

  <!-- CSS Files -->
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/styles.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/tour.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/navbar.css">
</head>

<body class="tour-detail-page">

  <?php include './includes/navbar.php'; ?>

  <!-- Hero -->
  <section class="td-hero" id="tdHero">
    <a href="<?= SITE_URL; ?>" class="td-back">
      <span class="td-back-circle"><i class="bi bi-arrow-left"></i></span>
      Back
    </a>

    <div class="td-hero-bg" style="background-image: url('<?= $mainImage; ?>');"></div>
    <div class="td-hero-overlay"></div>

    <div class="td-hero-content">
      <div class="td-hero-inner">
        <div class="td-hero-badge reveal-up" data-delay="0">
          <i class="bi bi-geo-alt-fill"></i>
          <?= htmlspecialchars($tour['package_type'] ?? 'Tour Package'); ?>
        </div>

        <h1 class="td-hero-title reveal-up" data-delay="100"><?= htmlspecialchars($tour['package_name']); ?></h1>

        <?php if (!empty($tour['short_description'])): ?>
          <p class="td-hero-sub reveal-up" data-delay="200"><?= htmlspecialchars($tour['short_description']); ?></p>
        <?php endif; ?>

        <div class="td-hero-meta reveal-up" data-delay="300">
          <?php if (!empty($tour['days_count'])): ?>
            <div class="td-meta-chip">
              <i class="bi bi-calendar3"></i>
              <?= getTourDuration($tour['days_count']); ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($tour['adults'])): ?>
            <div class="td-meta-chip">
              <i class="bi bi-people-fill"></i>
              <?= $tour['adults']; ?> Adults
            </div>
          <?php endif; ?>

          <div class="td-meta-chip price-chip">
            <i class="bi bi-currency-rupee"></i>

            <?php if ($originalPrice > 0): ?>

              <?php if ($hasOffer): ?>

                <span class="td-price-original">
                  ₹<?= number_format($originalPrice, 0); ?>
                </span>

                <strong class="td-price-offer">
                  ₹<?= number_format($finalPrice, 0); ?>
                </strong>

                <span class="td-price-discount">
                  <?= htmlspecialchars($discountText); ?>
                </span>

              <?php else: ?>

                <strong>
                  ₹<?= number_format($originalPrice, 0); ?>
                </strong>

              <?php endif; ?>

              <small style="font-weight:400; color:#888; font-size:12px;">
                /person
              </small>

            <?php else: ?>

              Contact for Price

            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Main Content -->
  <div class="td-main">

    <!-- Left Column -->
    <div class="td-left">

      <!-- Description -->
      <div class="td-card reveal-up" data-delay="0">
        <div class="td-section-label">
          <i class="bi bi-info-circle-fill"></i>
          Overview
        </div>
        <h2 class="td-heading">About This <em>Tour</em></h2>
        <p class="td-desc"><?= nl2br(htmlspecialchars($tour['description'] ?? 'No description available.')); ?></p>
      </div>

      <!-- Features -->
      <?php if (!empty($features)): ?>
        <div class="td-card reveal-up" data-delay="0">
          <div class="td-section-label">
            <i class="bi bi-stars"></i>
            Inclusions
          </div>
          <h2 class="td-heading">What's <em>Included</em></h2>
          <div class="td-features-grid">
            <?php foreach ($features as $index => $feature): ?>
              <div class="td-feature reveal-up" data-delay="<?= $index * 50; ?>">
                <div class="td-feature-icon">
                  <?php if (!empty($feature['icon_url'])): ?>
                    <img src="<?= $feature['icon_url']; ?>" alt="<?= htmlspecialchars($feature['name']); ?>">
                  <?php else: ?>
                    <i class="bi bi-check-lg"></i>
                  <?php endif; ?>
                </div>
                <div class="td-feature-text"><?= htmlspecialchars($feature['name']); ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Itinerary -->
      <?php if (!empty($itinerary)): ?>
        <div class="td-card reveal-up" data-delay="0">
          <div class="td-section-label">
            <i class="bi bi-route"></i>
            Schedule
          </div>
          <h2 class="td-heading">Day-by-Day <em>Itinerary</em></h2>
          <div class="td-timeline">
            <?php $dayNum = 1;
            foreach ($itinerary as $day => $details): ?>
              <div class="td-day reveal-up" data-delay="<?= ($dayNum - 1) * 80; ?>">
                <div class="td-day-dot"></div>
                <div class="td-day-label">Day <?= $dayNum; ?></div>
                <h4 class="td-day-title"><?= htmlspecialchars($details['title'] ?? 'Day ' . $dayNum); ?></h4>
                <p class="td-day-desc"><?= htmlspecialchars($details['description'] ?? ''); ?></p>
              </div>
            <?php $dayNum++;
            endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Gallery -->
      <?php if (!empty($galleryImages)): ?>
        <div class="td-card reveal-up" data-delay="0">
          <div class="td-section-label">
            <i class="bi bi-camera-fill"></i>
            Photos
          </div>
          <h2 class="td-heading">Tour <em>Gallery</em></h2>
          <div class="td-gallery-grid">
            <?php foreach ($galleryImages as $index => $image): ?>
              <img src="<?= $image; ?>" alt="Gallery image <?= $index + 1; ?>" loading="lazy" onclick="openLightbox(<?= $index; ?>)" class="reveal-up" data-delay="<?= $index * 60; ?>">
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

    </div>

    <!-- Right Column — Booking Card -->
    <div class="td-right">
      <div class="td-booking-card reveal-up" data-delay="100">
        <div class="td-booking-header">
        <div class="td-booking-price">

  <?php if ($originalPrice > 0): ?>

    <?php if ($hasOffer): ?>

      <div class="td-price-main">
        ₹<?= number_format($finalPrice, 0); ?>
        <small>/person</small>
      </div>

      <div class="td-price-offer-row">

        <span class="td-price-original">
          ₹<?= number_format($originalPrice, 0); ?>
        </span>

        <span class="td-price-discount">
          <?= htmlspecialchars($discountText); ?>
        </span>

      </div>

    <?php else: ?>

      <div class="td-price-main">
        ₹<?= number_format($originalPrice, 0); ?>
        <small>/person</small>
      </div>

    <?php endif; ?>

  <?php else: ?>

    <div class="td-price-main">
      Contact Us
    </div>

  <?php endif; ?>

</div>
          <p class="td-booking-type"><?= htmlspecialchars($tour['package_type'] ?? 'Tour Package'); ?></p>
        </div>

        <div class="td-booking-body">
          <?php if (!empty($tour['days_count'])): ?>
            <div class="td-booking-row">
              <span class="td-booking-row-label">
                <i class="bi bi-calendar3"></i> Duration
              </span>
              <span class="td-booking-row-value"><?= getTourDuration($tour['days_count']); ?></span>
            </div>
          <?php endif; ?>

          <?php if (!empty($tour['package_type'])): ?>
            <div class="td-booking-row">
              <span class="td-booking-row-label">
                <i class="bi bi-tag"></i> Type
              </span>
              <span class="td-booking-row-value"><?= htmlspecialchars($tour['package_type']); ?></span>
            </div>
          <?php endif; ?>

          <?php if (!empty($tour['adults'])): ?>
            <div class="td-booking-row">
              <span class="td-booking-row-label">
                <i class="bi bi-people"></i> Adults
              </span>
              <span class="td-booking-row-value"><?= $tour['adults']; ?></span>
            </div>
          <?php endif; ?>

          <?php if (!empty($tour['children'])): ?>
            <div class="td-booking-row">
              <span class="td-booking-row-label">
                <i class="bi bi-person"></i> Children
              </span>
              <span class="td-booking-row-value"><?= $tour['children']; ?></span>
            </div>
          <?php endif; ?>

          <?php if (!empty($tour['infants'])): ?>
            <div class="td-booking-row">
              <span class="td-booking-row-label">
                <i class="bi bi-baby"></i> Infants
              </span>
              <span class="td-booking-row-value"><?= $tour['infants']; ?></span>
            </div>
          <?php endif; ?>

          <button type="button" class="td-book-btn" id="tdBookNowBtn">
            <i class="bi bi-calendar-check"></i> Book Now
          </button>

          <a href="https://wa.me/?text=<?= urlencode('Hi, I am interested in: ' . $tour['package_name']); ?>" target="_blank" class="td-book-whatsapp">
            <i class="bi bi-whatsapp"></i> Chat on WhatsApp
          </a>

          <div class="td-booking-guarantee">
            <i class="bi bi-shield-check"></i>
            Best Price Guarantee · Free Cancellation
          </div>
        </div>
      </div>
    </div>

  </div>

  <?php include './includes/footer.php'; ?>

  <!-- ==================== BOOKING MODAL ==================== -->
  <div class="td-modal-overlay" id="tdBookingModal">
    <div class="td-modal-card">

      <!-- FORM STATE -->
      <div class="td-modal-form" id="tdModalForm">
        <div class="td-modal-header">
          <div class="td-modal-header-info">
            <h3 class="td-modal-package-name"><?= htmlspecialchars($tour['package_name']); ?></h3>
            <p class="td-modal-price">

              <?php if ($originalPrice > 0): ?>

                <?php if ($hasOffer): ?>

                  <span class="td-price-original">
                    ₹<?= number_format($originalPrice, 0); ?>
                  </span>

                  <strong class="td-price-offer">
                    ₹<?= number_format($finalPrice, 0); ?>
                  </strong>

                  <span class="td-price-discount">
                    <?= htmlspecialchars($discountText); ?>
                  </span>

                <?php else: ?>

                  <strong>
                    ₹<?= number_format($originalPrice, 0); ?>
                  </strong>

                <?php endif; ?>

                /person

              <?php else: ?>

                Contact for price

              <?php endif; ?>

            </p>
          </div>
          <button type="button" class="td-modal-close" id="tdModalClose" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="td-modal-body">
          <div class="td-modal-divider"></div>

          <!-- Server error -->
          <div class="td-modal-server-error" id="tdServerError">
            <i class="bi bi-exclamation-circle"></i>
            <span id="tdServerErrorText"></span>
          </div>

          <form id="tdBookingForm" novalidate>
            <input type="hidden" name="package_id" value="<?= htmlspecialchars($tour['id'] ?? $tour['package_id'] ?? ''); ?>">
            <input type="hidden" name="package_name" value="<?= htmlspecialchars($tour['package_name']); ?>">

            <!-- Returning customer badge -->
            <div class="td-returning-badge" id="tdReturningBadge">
              <i class="bi bi-person-check-fill"></i>
              <span>Welcome back! We found your name.</span>
            </div>

            <div class="td-form-group">
              <label class="td-form-label" for="tdCustomerName">Your Name</label>
              <input type="text" class="td-form-input" id="tdCustomerName" name="customer_name" placeholder="Enter your full name" autocomplete="name" maxlength="100">
              <div class="td-form-error" id="tdNameError">
                <i class="bi bi-exclamation-circle"></i>
                <span id="tdNameErrorText"></span>
              </div>
            </div>

            <div class="td-form-group">
              <label class="td-form-label" for="tdMobileNumber">Mobile Number</label>
              <input type="tel" class="td-form-input" id="tdMobileNumber" name="mobile_number" placeholder="Enter 10-digit mobile number" autocomplete="tel" maxlength="10" inputmode="numeric">
              <div class="td-form-error" id="tdMobileError">
                <i class="bi bi-exclamation-circle"></i>
                <span id="tdMobileErrorText"></span>
              </div>
            </div>

            <button type="submit" class="td-modal-submit" id="tdModalSubmit">
              <span class="td-submit-icon"><i class="bi bi-calendar-check"></i></span>
              <span class="td-submit-text">Submit Booking</span>
              <span class="td-spinner"></span>
            </button>
          </form>
        </div>
      </div>

      <!-- SUCCESS STATE -->
      <div class="td-modal-success" id="tdModalSuccess">
        <div class="td-success-icon">
          <i class="bi bi-check-lg"></i>
        </div>
        <h3 class="td-success-title">Booking Submitted!</h3>
        <p class="td-success-message">Thank you! Our team will contact you shortly to confirm your booking.</p>
        <button type="button" class="td-success-close" id="tdSuccessClose">Done</button>
      </div>

    </div>
  </div>

  <!-- ==================== LIGHTBOX ==================== -->
  <div class="td-lightbox" id="tdLightbox">
    <button class="td-lightbox-close" id="tdLightboxClose">
      <i class="bi bi-x-lg"></i>
    </button>
    <button class="td-lightbox-nav td-lightbox-prev" id="tdLightboxPrev">
      <i class="bi bi-chevron-left"></i>
    </button>
    <button class="td-lightbox-nav td-lightbox-next" id="tdLightboxNext">
      <i class="bi bi-chevron-right"></i>
    </button>
    <img id="tdLightboxImg" src="" alt="Gallery preview">
    <div class="td-lightbox-counter" id="tdLightboxCounter"></div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
  <script>
    (function() {
      'use strict';

      var lenis = new Lenis({
        duration: 1.2,
        easing: function(t) {
          return Math.min(1, 1.001 - Math.pow(2, -10 * t));
        },
        orientation: 'vertical',
        smoothWheel: true,
        wheelMultiplier: 1,
        touchMultiplier: 2,
      });

      function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
      }
      requestAnimationFrame(raf);

      var navbar = document.querySelector('nav');
      var hero = document.getElementById('tdHero');

      if (navbar && hero) {
        function updateNavClass() {
          var heroBottom = hero.offsetTop + hero.offsetHeight;
          if (window.scrollY < heroBottom - 100) {
            navbar.classList.add('nav-over-hero');
          } else {
            navbar.classList.remove('nav-over-hero');
          }
        }
        window.addEventListener('scroll', updateNavClass, {
          passive: true
        });
        updateNavClass();
      }

      var galleryImages = [];
      document.querySelectorAll('.td-gallery-grid img').forEach(function(img) {
        galleryImages.push(img.src);
      });
      var currentLightboxIndex = 0;

      window.addEventListener('load', function() {
        var hero = document.getElementById('tdHero');
        if (hero) {
          requestAnimationFrame(function() {
            hero.classList.add('loaded');
          });
        }
        revealObserver.takeRecords().forEach(handleReveal);
      });

      var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      function handleReveal(entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        var delay = parseInt(el.getAttribute('data-delay')) || 0;
        setTimeout(function() {
          el.classList.add('revealed');
        }, delay);
        revealObserver.unobserve(el);
      }

      var revealObserver = new IntersectionObserver(
        function(entries) {
          entries.forEach(handleReveal);
        }, {
          threshold: 0.08,
          rootMargin: '0px 0px -40px 0px'
        }
      );

      if (!prefersReduced) {
        document.querySelectorAll('.reveal-up').forEach(function(el) {
          revealObserver.observe(el);
        });
      } else {
        document.querySelectorAll('.reveal-up').forEach(function(el) {
          el.classList.add('revealed');
        });
      }

      var modal = document.getElementById('tdBookingModal');
      var bookBtn = document.getElementById('tdBookNowBtn');
      var modalClose = document.getElementById('tdModalClose');
      var successClose = document.getElementById('tdSuccessClose');
      var bookingForm = document.getElementById('tdBookingForm');
      var submitBtn = document.getElementById('tdModalSubmit');
      var formState = document.getElementById('tdModalForm');
      var successState = document.getElementById('tdModalSuccess');
      var serverError = document.getElementById('tdServerError');
      var serverErrTxt = document.getElementById('tdServerErrorText');
      var nameInput = document.getElementById('tdCustomerName');
      var mobileInput = document.getElementById('tdMobileNumber');
      var nameError = document.getElementById('tdNameError');
      var nameErrTxt = document.getElementById('tdNameErrorText');
      var mobileError = document.getElementById('tdMobileError');
      var mobileErrTxt = document.getElementById('tdMobileErrorText');
      var returnBadge = document.getElementById('tdReturningBadge');

      function openModal() {
        formState.classList.remove('td-form-hidden');
        successState.classList.remove('td-success-visible');
        submitBtn.classList.remove('td-submitting');
        submitBtn.disabled = false;
        serverError.classList.remove('td-error-visible');
        returnBadge.classList.remove('td-badge-visible');
        clearFieldError(nameInput, nameError);
        clearFieldError(mobileInput, mobileError);
        bookingForm.reset();

        modal.classList.add('td-modal-open');
        document.body.style.overflow = 'hidden';
        lenis.stop();
        setTimeout(function() {
          nameInput.focus();
        }, 350);
      }

      function closeModal() {
        modal.classList.remove('td-modal-open');
        document.body.style.overflow = '';
        setTimeout(function() {
          lenis.start();
        }, 300);
      }

      bookBtn.addEventListener('click', openModal);
      modalClose.addEventListener('click', closeModal);
      successClose.addEventListener('click', closeModal);
      modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
      });
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('td-modal-open')) closeModal();
      });

      function showFieldError(input, errorEl, text) {
        input.classList.add('td-input-error');
        errorEl.classList.add('td-error-visible');
        errorEl.querySelector('span').textContent = text;
      }

      function clearFieldError(input, errorEl) {
        input.classList.remove('td-input-error');
        errorEl.classList.remove('td-error-visible');
      }

      nameInput.addEventListener('input', function() {
        clearFieldError(nameInput, nameError);
        returnBadge.classList.remove('td-badge-visible');
      });

      mobileInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        clearFieldError(mobileInput, mobileError);
      });

      var checkTimer = null;
      mobileInput.addEventListener('input', function() {
        var val = this.value;
        clearTimeout(checkTimer);

        if (val.length === 10) {
          checkTimer = setTimeout(function() {
            fetch('<?= SITE_URL; ?>ajax/book-tour-check.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'mobile_number=' + encodeURIComponent(val)
              })
              .then(function(r) {
                return r.json();
              })
              .then(function(data) {
                if (data.found && data.name) {
                  nameInput.value = data.name;
                  returnBadge.classList.add('td-badge-visible');
                }
              })
              .catch(function() {
                // Silent fail
              });
          }, 400);
        } else {
          returnBadge.classList.remove('td-badge-visible');
        }
      });

      bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();

        var name = nameInput.value.trim();
        var mobile = mobileInput.value.trim();
        var valid = true;

        if (!name) {
          showFieldError(nameInput, nameError, 'Please enter your name.');
          valid = false;
        } else if (name.length < 2) {
          showFieldError(nameInput, nameError, 'Name must be at least 2 characters.');
          valid = false;
        } else if (!/^[a-zA-Z\s'.-]+$/.test(name)) {
          showFieldError(nameInput, nameError, 'Only letters, spaces, hyphens and dots allowed.');
          valid = false;
        }

        if (!mobile) {
          showFieldError(mobileInput, mobileError, 'Please enter your mobile number.');
          valid = false;
        } else if (!/^[0-9]{10}$/.test(mobile)) {
          showFieldError(mobileInput, mobileError, 'Enter a valid 10-digit mobile number.');
          valid = false;
        }

        if (!valid) return;

        serverError.classList.remove('td-error-visible');
        submitBtn.classList.add('td-submitting');
        submitBtn.disabled = true;

        var formData = new FormData(bookingForm);

        fetch('<?= SITE_URL; ?>ajax/book-tour.php', {
            method: 'POST',
            body: formData
          })
          .then(function(res) {
            return res.json();
          })
          .then(function(data) {
            submitBtn.classList.remove('td-submitting');
            submitBtn.disabled = false;

            if (data.success) {
              formState.classList.add('td-form-hidden');
              successState.classList.add('td-success-visible');
            } else {
              serverErrTxt.textContent = data.message || 'Something went wrong.';
              serverError.classList.add('td-error-visible');
            }
          })
          .catch(function() {
            submitBtn.classList.remove('td-submitting');
            submitBtn.disabled = false;
            serverErrTxt.textContent = 'Network error. Check your connection and try again.';
            serverError.classList.add('td-error-visible');
          });
      });

      var lightbox = document.getElementById('tdLightbox');
      var lightboxImg = document.getElementById('tdLightboxImg');
      var lightboxCounter = document.getElementById('tdLightboxCounter');
      var lightboxClose = document.getElementById('tdLightboxClose');
      var lightboxPrev = document.getElementById('tdLightboxPrev');
      var lightboxNext = document.getElementById('tdLightboxNext');
      var lightboxVisible = false;

      window.openLightbox = function(index) {
        if (!galleryImages.length) return;
        currentLightboxIndex = index;
        showLightboxImage();
        lightbox.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        lenis.stop();
        lightboxVisible = true;
        requestAnimationFrame(function() {
          lightbox.classList.add('td-lightbox-open');
        });
      };

      function showLightboxImage() {
        lightboxImg.src = galleryImages[currentLightboxIndex];
        lightboxCounter.textContent = (currentLightboxIndex + 1) + ' / ' + galleryImages.length;
        lightboxPrev.style.display = galleryImages.length > 1 ? 'flex' : 'none';
        lightboxNext.style.display = galleryImages.length > 1 ? 'flex' : 'none';
      }

      function closeLightbox() {
        lightbox.classList.remove('td-lightbox-open');
        lightboxVisible = false;
        setTimeout(function() {
          lightbox.style.display = 'none';
          lightboxImg.src = '';
          document.body.style.overflow = '';
          lenis.start();
        }, 300);
      }

      function lightboxPrevImage() {
        currentLightboxIndex = (currentLightboxIndex - 1 + galleryImages.length) % galleryImages.length;
        lightboxImg.style.opacity = '0';
        setTimeout(function() {
          showLightboxImage();
          lightboxImg.style.opacity = '1';
        }, 150);
      }

      function lightboxNextImage() {
        currentLightboxIndex = (currentLightboxIndex + 1) % galleryImages.length;
        lightboxImg.style.opacity = '0';
        setTimeout(function() {
          showLightboxImage();
          lightboxImg.style.opacity = '1';
        }, 150);
      }

      lightboxClose.addEventListener('click', function(e) {
        e.stopPropagation();
        closeLightbox();
      });
      lightboxPrev.addEventListener('click', function(e) {
        e.stopPropagation();
        lightboxPrevImage();
      });
      lightboxNext.addEventListener('click', function(e) {
        e.stopPropagation();
        lightboxNextImage();
      });
      lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) closeLightbox();
      });

      document.addEventListener('keydown', function(e) {
        if (!lightboxVisible) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') lightboxPrevImage();
        if (e.key === 'ArrowRight') lightboxNextImage();
      });

      var touchStartX = 0;
      lightbox.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
      }, {
        passive: true
      });
      lightbox.addEventListener('touchend', function(e) {
        var diff = touchStartX - e.changedTouches[0].screenX;
        if (Math.abs(diff) > 50) {
          if (diff > 0) lightboxNextImage();
          else lightboxPrevImage();
        }
      }, {
        passive: true
      });

      if (!prefersReduced) {
        var heroBg = document.querySelector('.td-hero-bg');
        var heroContent = document.querySelector('.td-hero-content');

        lenis.on('scroll', function(e) {
          var scrollY = e.animatedScroll || window.scrollY;
          var heroHeight = document.getElementById('tdHero').offsetHeight;

          if (scrollY < heroHeight) {
            var progress = scrollY / heroHeight;
            if (heroBg) {
              heroBg.style.transform = 'scale(' + (1.05 - progress * 0.05) + ') translateY(' + (scrollY * 0.25) + 'px)';
            }
            if (heroContent) {
              heroContent.style.opacity = 1 - progress * 1.4;
              heroContent.style.transform = 'translateY(' + (scrollY * 0.15) + 'px)';
            }
          }
        });
      }

      var backToTop = document.createElement('button');
      backToTop.className = 'td-scroll-top';
      backToTop.innerHTML = '<i class="bi bi-chevron-up"></i>';
      backToTop.setAttribute('aria-label', 'Back to top');
      document.body.appendChild(backToTop);

      lenis.on('scroll', function(e) {
        if (e.animatedScroll > 600) {
          backToTop.classList.add('visible');
        } else {
          backToTop.classList.remove('visible');
        }
      });

      backToTop.addEventListener('click', function() {
        lenis.scrollTo(0, {
          duration: 1.4
        });
      });

    })();
  </script>
</body>

</html>