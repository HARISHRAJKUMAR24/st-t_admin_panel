<?php
require_once './config/config.php';

// Get all tour packages
$allTours = getTourPackages($pdo);
$siteTitle = getSiteTitle($pdo);
$siteName = getSiteName($pdo);
$footerText = getFooterText($pdo);
$logo = getWebsiteLogo($pdo);
$favicon = getFavicon($pdo);

// Get unique categories for filter
$categories = [];
foreach ($allTours as $tour) {
  if (!empty($tour['package_type']) && !in_array($tour['package_type'], $categories)) {
    $categories[] = $tour['package_type'];
  }
}
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
  <title>All Tours - <?= $siteTitle; ?></title>
  <link rel="stylesheet" href="./assets/css/styles.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/styles.css">
  <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/navbar.css">
</head>

<style>
  .tours-header {
    background: #f8f5f0;
    padding: 80px 0 40px;
    text-align: center;
  }

  .tours-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 30px;
    padding: 40px 0;
  }

  .tour-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    text-decoration: none;
    color: inherit;
  }

  .tour-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
  }

  .tour-card-image {
    position: relative;
    height: 240px;
    overflow: hidden;
  }

  .tour-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }

  .tour-card:hover .tour-card-image img {
    transform: scale(1.05);
  }

  .tour-card-category {
    position: absolute;
    top: 16px;
    left: 16px;
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
  }

  .tour-card-body {
    padding: 20px;
  }

  .tour-card-body h3 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 8px;
  }

  .tour-card-body p {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 16px;
  }

  .tour-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
    border-top: 1px solid #eee;
  }

  .tour-card-price {
    font-size: 1.3rem;
    font-weight: 700;
  }

  .tour-card-price small {
    font-size: 0.8rem;
    font-weight: 400;
    color: #666;
  }

  .tour-card-price {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 2px;
  }

  .tour-card-original-price {
    color: #888;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: line-through;
  }

  .tour-card-final-price {
    font-size: 1.3rem;
    font-weight: 800;
    color: #111;
  }

  .tour-card-final-price small {
    font-size: 0.75rem;
    font-weight: 400;
    color: #666;
  }

  .tour-card-discount {
    display: inline-block;
    margin-top: 3px;
    padding: 3px 8px;
    border-radius: 5px;
    background: #e8f7ee;
    color: #15803d;
    font-size: 0.7rem;
    font-weight: 700;
  }

  .tour-card-duration {
    font-size: 0.85rem;
    color: #666;
  }

  .filter-section {
    padding: 20px 0;
  }

  .filter-btn {
    padding: 8px 20px;
    border-radius: 30px;
    border: 2px solid #ddd;
    background: transparent;
    margin: 4px 6px;
    transition: all 0.3s ease;
    cursor: pointer;
  }

  .filter-btn:hover,
  .filter-btn.active {
    background: #000;
    color: #fff;
    border-color: #000;
  }

  .no-tours {
    text-align: center;
    padding: 60px 0;
    color: #666;
  }
</style>

<body>
  <?php include './includes/navbar.php'; ?>

  <section class="tours-header">
    <div class="container">
      <h1>All Tour Packages</h1>
      <p class="text-muted">Discover our curated collection of amazing tours</p>
    </div>
  </section>

  <section class="container">
    <!-- Filter Section -->
    <?php if (!empty($categories)): ?>
      <div class="filter-section text-center">
        <button class="filter-btn active" data-filter="all">All</button>
        <?php foreach ($categories as $category): ?>
          <button class="filter-btn" data-filter="<?= strtolower($category); ?>"><?= $category; ?></button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Tours Grid -->
    <div class="tours-grid" id="toursGrid">
      <?php if (!empty($allTours)): ?>
        <?php foreach ($allTours as $tour):

          $mainImage = !empty($tour['main_image'])
            ? ADMIN_URL . $tour['main_image']
            : SITE_URL . 'assets/images/default-tour.jpg';

          $tourId = $tour['id'] ?? $tour['package_id'];

          $tourLink = SITE_URL . 'tour.php?id=' . $tourId;

          // Get active offer for this tour
          $tourOffer = getTourOffer($pdo, $tourId);

          // Calculate original + discounted price
          $priceData = calculateTourOfferPrice(
            $tour['price'] ?? 0,
            $tourOffer
          );

          $originalPrice = $priceData['original_price'];
          $finalPrice    = $priceData['final_price'];
          $discountText  = $priceData['discount_text'];
          $hasOffer      = $priceData['has_offer'];
        ?>
          <a href="<?= $tourLink; ?>" class="tour-card" data-category="<?= strtolower($tour['package_type'] ?? ''); ?>">
            <div class="tour-card-image">
              <img src="<?= $mainImage; ?>" alt="<?= $tour['package_name']; ?>" loading="lazy">
              <span class="tour-card-category"><?= $tour['package_type'] ?? 'Tour'; ?></span>
            </div>
            <div class="tour-card-body">
              <h3><?= $tour['package_name']; ?></h3>
              <p><?= $tour['short_description'] ?? ''; ?></p>
              <div class="tour-card-footer">
                <div class="tour-card-price">

                  <?php if ($originalPrice > 0): ?>

                    <?php if ($hasOffer): ?>

                      <div class="tour-card-original-price">
                        ₹<?= number_format($originalPrice, 0); ?>
                      </div>

                      <div class="tour-card-final-price">
                        ₹<?= number_format($finalPrice, 0); ?>
                        <small>/person</small>
                      </div>

                      <span class="tour-card-discount">
                        <?= htmlspecialchars($discountText); ?>
                      </span>

                    <?php else: ?>

                      <div class="tour-card-final-price">
                        ₹<?= number_format($originalPrice, 0); ?>
                        <small>/person</small>
                      </div>

                    <?php endif; ?>

                  <?php else: ?>

                    Contact for Price

                  <?php endif; ?>

                </div>
                <span class="tour-card-duration">
                  <i class="bi bi-clock"></i> <?= getTourDuration($tour['days_count'] ?? 0); ?>
                </span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-tours col-12">
          <i class="bi bi-box" style="font-size: 3rem;"></i>
          <h3>No Tours Available</h3>
          <p>Check back later for new tour packages.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <?php include './includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Filter functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        // Update active state
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const filter = this.dataset.filter;
        const cards = document.querySelectorAll('.tour-card');

        cards.forEach(card => {
          if (filter === 'all' || card.dataset.category === filter) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  </script>
</body>

</html>