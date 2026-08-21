<?php
// Fetch categories with images from package_type_images table
// Only categories with actual images in backend will be returned
 $categories = getCategoriesWithPackageImages($pdo);
?>
<?php if (!empty($categories)): ?>
<section class="tour-categories-section" id="categories">

 <div class="tour-category-stamp tour-category-stamp-1" aria-hidden="true">
    <img src="<?= SITE_URL; ?>assets/images/stamp1.png" alt="">
</div>

<div class="tour-category-stamp tour-category-stamp-2" aria-hidden="true">
    <img src="<?= SITE_URL; ?>assets/images/stamp2.png" alt="">
</div>

  <div style="max-width:1460px; margin:0 auto; padding:0 30px;">

    <div style="text-align:center; max-width:570px; margin:0 auto 40px; position:relative; z-index:3;">
      <div class="cat-badge" style="margin-bottom:14px;">
        <span class="badge-icon"><svg viewBox="0 0 16 16">
            <circle cx="8" cy="8" r="6" fill="#5e5e5e" />
          </svg></span>
        <span class="badge-text">Tour Categories</span>
        <span class="badge-icon"><svg viewBox="0 0 16 16">
            <circle cx="8" cy="8" r="6" fill="#5e5e5e" />
          </svg></span>
      </div>
      <div class="categories-title">
        <span>Discover</span><span class="italic">Adventures</span><span>That</span><span>Fit</span><span>You</span>
      </div>
    </div>

    <div class="categories-viewport" id="catViewport">
      <div class="categories-track" id="catTrack">
        <?php foreach ($categories as $category): ?>
          <a href="<?= SITE_URL; ?>category.php?type=<?= urlencode($category['name']); ?>" class="category-card">
            <div class="card-image">
              <img src="<?= $category['image_url']; ?>" alt="<?= $category['name']; ?>" loading="lazy">
              <div class="card-overlay"><span class="arrow-icon"><i class="bi bi-arrow-up-right"></i></span></div>
            </div>
            <div class="card-title"><?= $category['name']; ?></div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="category-dots" id="categoryDots"></div>

    <div style="display:flex; justify-content:center; align-items:center; gap:16px; margin-top:32px; position:relative; z-index:3; flex-wrap:wrap;">
      <a href="<?= SITE_URL; ?>categories.php" class="btn-view-all">
        <span class="btn-text">View All Categories</span>
        <span class="icon-wrap"><i class="bi bi-arrow-up-right"></i></span>
      </a>
     
    </div>
  </div>
</section>
<?php endif; ?>