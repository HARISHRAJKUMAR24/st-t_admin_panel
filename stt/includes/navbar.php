<?php
// Get the website logo from database
$logo = getWebsiteLogo($pdo);
?>
<style>
  /* =========================================================
   TRAVEL MENU - SMALL DROPDOWN
   ========================================================= */

  .travel-menu-dropdown {
    position: fixed;
    top: 82px;
    left: 38px;

    width: 390px;
    max-width: calc(100vw - 30px);

    background: rgba(255, 255, 255, 0.98);
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 18px;

    box-shadow:
      0 20px 55px rgba(0, 0, 0, 0.16),
      0 4px 15px rgba(0, 0, 0, 0.06);

    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);

    z-index: 9998;

    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px) scale(0.98);
    transform-origin: top left;

    pointer-events: none;

    transition:
      opacity .22s ease,
      transform .22s ease,
      visibility .22s ease;
  }


  /* OPEN */

  .travel-menu-dropdown.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
    pointer-events: auto;
  }


  /* =========================================================
   INNER
   ========================================================= */

  .travel-menu-inner {
    width: 100%;
    padding: 22px;
  }


  /* =========================================================
   TOP
   ========================================================= */

  .travel-menu-top {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding-bottom: 15px;
    margin-bottom: 8px;

    border-bottom: 1px solid #e8e5df;
  }

  .travel-menu-small {
    display: block;

    color: #bd755e;

    font-size: 9px;
    font-weight: 700;

    letter-spacing: 1.8px;
    text-transform: uppercase;

    margin-bottom: 5px;
  }

  .travel-menu-top h2 {
    margin: 0;

    color: #1c1c1a;

    font-family:
      Georgia,
      "Times New Roman",
      serif;

    font-size: 20px;
    font-weight: 500;

    line-height: 1.15;
    letter-spacing: -0.3px;
  }

  .travel-menu-top h2 em {
    color: #bd755e;
    font-style: italic;
  }


  /* =========================================================
   CLOSE BUTTON
   ========================================================= */

  .travel-menu-close {
    width: 32px;
    height: 32px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid #e5e2dc;
    border-radius: 50%;

    background: #f7f6f3;
    color: #222;

    cursor: pointer;

    transition: all .2s ease;
  }

  .travel-menu-close i {
    font-size: 11px;
  }

  .travel-menu-close:hover {
    background: #1c1c1a;
    color: #fff;
    transform: rotate(90deg);
  }


  /* =========================================================
   LINKS
   ========================================================= */

  .travel-menu-links {
    display: grid;

    grid-template-columns: repeat(2, 1fr);

    column-gap: 10px;
    row-gap: 3px;
  }


  /* Individual item */

  .travel-menu-link {
    min-height: 48px;

    display: flex;
    align-items: center;

    gap: 10px;

    padding: 0 8px;

    border-radius: 9px;

    color: #282724;

    text-decoration: none;

    transition:
      background .2s ease,
      color .2s ease,
      transform .2s ease;
  }


  /* Number */

  .menu-number {
    min-width: 20px;

    color: #aaa;

    font-size: 9px;
    font-weight: 600;
  }


  /* Title */

  .menu-link-title {
    flex: 1;

    font-family:
      Georgia,
      "Times New Roman",
      serif;

    font-size: 15px;
    font-weight: 500;
  }


  /* Arrow */

  .travel-menu-link>i {
    color: #aaa;

    font-size: 10px;

    transition:
      transform .2s ease,
      color .2s ease;
  }


  /* Hover */

  .travel-menu-link:hover {
    background: #f5f3ee;
    color: #bd755e;

    transform: translateX(2px);
  }

  .travel-menu-link:hover>i {
    color: #bd755e;
    transform: translate(2px, -2px);
  }


  /* =========================================================
   BOTTOM
   ========================================================= */

  .travel-menu-bottom {
    margin-top: 12px;

    padding: 12px 14px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    border-radius: 10px;

    background: #1c1c1a;
    color: #fff;
  }

  .travel-menu-bottom span {
    color: #aaa;

    font-size: 10px;
  }

  .travel-menu-bottom a {
    display: inline-flex;
    align-items: center;

    gap: 5px;

    color: #fff;

    text-decoration: none;

    font-size: 10px;
    font-weight: 600;
  }

  .travel-menu-bottom a i {
    color: #fedbe5;
    font-size: 10px;
  }


  /* =========================================================
   MOBILE
   ========================================================= */

  @media (max-width: 700px) {

    .travel-menu-dropdown {
      top: 72px;
      left: 15px;

      width: 330px;
      max-width: calc(100vw - 30px);

      border-radius: 15px;
    }

    .travel-menu-inner {
      padding: 17px;
    }

    .travel-menu-top h2 {
      font-size: 18px;
    }

    .travel-menu-small {
      font-size: 8px;
    }

    .travel-menu-links {
      grid-template-columns: 1fr 1fr;
      column-gap: 5px;
    }

    .travel-menu-link {
      min-height: 43px;
      gap: 6px;
      padding: 0 5px;
    }

    .menu-number {
      display: none;
    }

    .menu-link-title {
      font-size: 13px;
    }

    .travel-menu-link>i {
      font-size: 9px;
    }

    .travel-menu-bottom {
      padding: 10px 12px;
    }
  }
</style>
<nav class="navbar-travelio d-flex justify-content-center" id="navbar">
  <div class="navbar-container d-flex justify-content-between align-items-center w-100">
    <div class="d-flex align-items-center">
      <button
        type="button"
        class="menu-btn"
        id="travelMenuBtn"
        aria-label="Open Menu"
        aria-expanded="false">
        <span class="menu-icon">
          <i class="bi bi-grid-fill"></i>
        </span>

        <span class="menu-text">
          Menu
        </span>
      </button>
    </div>
    <div class="nav-logo">
      <?php if (!empty($logo)): ?>
        <!-- Display the logo from admin panel -->
        <a href="<?= SITE_URL; ?>">
          <img src="<?= $logo; ?>" alt="<?= getSiteName($pdo); ?>" class="navbar-logo-img" style="height: auto; width: auto; max-height: 85px; border-radius:15px">
        </a>
      <?php else: ?>
        <!-- Fallback: Display the SVG logo if no logo in database -->
        <a href="<?= SITE_URL; ?>">
          <svg viewBox="0 0 194 74" fill="none" xmlns="http://www.w3.org/2000/svg" style="height: 50px; width: auto;">
            <path d="M27.1 18.5L19.5 36.7L11.9 18.5H0.5L13.4 46.5V68H25.6V46.5L38.5 18.5H27.1Z" fill="white" />
            <path d="M54.2 18.5V68H65.9V47.7H76.1V37.7H65.9V28.5H78V18.5H54.2Z" fill="white" />
            <path d="M93.6 18.5V68H105.3V47.7H115.5V37.7H105.3V28.5H117.4V18.5H93.6Z" fill="white" />
            <path d="M133 18.5V68H144.7V47.7H154.9V37.7H144.7V28.5H156.8V18.5H133Z" fill="white" />
            <path d="M172.4 18.5L164.8 36.7L157.2 18.5H145.8L158.7 46.5V68H170.9V46.5L183.8 18.5H172.4Z" fill="white" />
            <circle cx="22" cy="68" r="6" fill="#fedbe5" />
            <circle cx="68" cy="68" r="6" fill="#fedbe5" />
            <circle cx="118" cy="68" r="6" fill="#fedbe5" />
            <circle cx="167" cy="68" r="6" fill="#fedbe5" />
          </svg>
        </a>
      <?php endif; ?>
    </div>
    <div class="d-flex align-items-center">
  <a href="<?= SITE_URL; ?>#tours"
   class="btn-plan plan-trip-link">

    <span class="btn-text">Plan a Trip</span>

    <span class="icon-wrap">
        <i class="bi bi-arrow-up-right"></i>
    </span>

</a>
    </div>
  </div>
</nav>

<!-- =========================================================
     TRAVEL MENU DROPDOWN
     ========================================================= -->

<div class="travel-menu-dropdown" id="travelMenuDropdown">

  <div class="travel-menu-inner">

    <div class="travel-menu-top">

      <div>
        <span class="travel-menu-small">
          EXPLORE
        </span>

        <h2>
          Discover Your <em>Journey</em>
        </h2>
      </div>

      <button
        type="button"
        class="travel-menu-close"
        id="travelMenuClose"
        aria-label="Close Menu">
        <i class="bi bi-x-lg"></i>
      </button>

    </div>

    <div class="travel-menu-links">

      <a href="<?= SITE_URL; ?>#offers"
        class="travel-menu-link">

        <span class="menu-number">01</span>
        <span class="menu-link-title">Offers</span>
        <i class="bi bi-arrow-up-right"></i>

      </a>


      <a href="<?= SITE_URL; ?>#tours"
        class="travel-menu-link">

        <span class="menu-number">02</span>
        <span class="menu-link-title">Tours</span>
        <i class="bi bi-arrow-up-right"></i>

      </a>


      <a href="<?= SITE_URL; ?>#about"
        class="travel-menu-link">

        <span class="menu-number">03</span>
        <span class="menu-link-title">About</span>
        <i class="bi bi-arrow-up-right"></i>

      </a>


      <a href="<?= SITE_URL; ?>#categories"
        class="travel-menu-link">

        <span class="menu-number">04</span>
        <span class="menu-link-title">Categories</span>
        <i class="bi bi-arrow-up-right"></i>

      </a>


      <a href="<?= SITE_URL; ?>#travel"
        class="travel-menu-link">

        <span class="menu-number">05</span>
        <span class="menu-link-title">Travel</span>
        <i class="bi bi-arrow-up-right"></i>

      </a>


      <a href="<?= SITE_URL; ?>#stories"
        class="travel-menu-link">

        <span class="menu-number">06</span>
        <span class="menu-link-title">Stories</span>
        <i class="bi bi-arrow-up-right"></i>

      </a>


      <a href="<?= SITE_URL; ?>#contact"
        class="travel-menu-link">

        <span class="menu-number">07</span>
        <span class="menu-link-title">Contact</span>
        <i class="bi bi-arrow-up-right"></i>

      </a>

    </div>

  </div>

</div>
<script>
  document.addEventListener("DOMContentLoaded", function() {

    const menuBtn =
      document.getElementById("travelMenuBtn");

    const menu =
      document.getElementById("travelMenuDropdown");

    const closeBtn =
      document.getElementById("travelMenuClose");

    if (!menuBtn || !menu) {
      return;
    }


    /* =====================================================
       OPEN
       ===================================================== */

    function openMenu() {

      menu.classList.add("active");

      menuBtn.setAttribute(
        "aria-expanded",
        "true"
      );

      document.body.classList.add(
        "travel-menu-open"
      );

    }


    /* =====================================================
       CLOSE
       ===================================================== */

    function closeMenu() {

      menu.classList.remove("active");

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
      function() {

        if (menu.classList.contains("active")) {
          closeMenu();
        } else {
          openMenu();
        }

      }
    );


    closeBtn.addEventListener(
      "click",
      closeMenu
    );


    /* =====================================================
       MENU LINKS
       ===================================================== */

    document
      .querySelectorAll(".travel-menu-link")
      .forEach(function(link) {

        link.addEventListener(
          "click",
          function(event) {

            const href =
              this.getAttribute("href");

            if (!href) {
              return;
            }


            const hash =
              href.split("#")[1];

            if (!hash) {
              return;
            }


            const target =
              document.getElementById(hash);


            /*
             * Target exists on current page
             */

            if (target) {

              event.preventDefault();

              closeMenu();

              setTimeout(function() {

                target.scrollIntoView({
                  behavior: "smooth",
                  block: "start"
                });

              }, 350);

            }

          }
        );

      });


    /* =====================================================
       ESC
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
       CLOSE WHEN CLICKING OUTSIDE
       ===================================================== */

    menu.addEventListener(
      "click",
      function(event) {

        if (
          event.target === menu
        ) {

          closeMenu();

        }

      }
    );


    /* =====================================================
       SMOOTH SCROLL AFTER COMING FROM ANOTHER PAGE
       ===================================================== */

    if (window.location.hash) {

      const target =
        document.getElementById(
          window.location.hash.substring(1)
        );

      if (target) {

        setTimeout(function() {

          target.scrollIntoView({
            behavior: "smooth",
            block: "start"
          });

        }, 500);

      }

    }

  });


</script>