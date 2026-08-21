const lenis = new Lenis({
  duration: 1.6,
  easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
  orientation: 'vertical',
  gestureOrientation: 'vertical',
  smoothWheel: true,
  wheelMultiplier: 0.7,
  touchMultiplier: 1.4,
});

function raf(time) {
  lenis.raf(time);
  requestAnimationFrame(raf);
}
requestAnimationFrame(raf);

let rawScroll = 0,
  smoothScroll = 0;
lenis.on('scroll', ({ scroll }) => {
  rawScroll = scroll;
});

const heroContent = document.getElementById('heroContent');
const navbar = document.getElementById('navbar');
const heroStrip = document.getElementById('heroStrip');
const scrollIndicator = document.getElementById('scrollIndicator');

function updateEffects() {
  smoothScroll += (rawScroll - smoothScroll) * 0.06;
  const vh = window.innerHeight;
  const progress = Math.min(smoothScroll / vh, 1);
  if (smoothScroll < vh * 1.2) {
    heroContent.style.transform = `translateY(${smoothScroll * 0.25}px)`;
    heroContent.style.opacity = Math.max(0, 1 - progress * 1.4);
    if (progress > 0.08) {
      navbar.style.transform = 'translateY(-120%)';
      navbar.style.opacity = '0';
    } else {
      navbar.style.transform = 'translateY(0)';
      navbar.style.opacity = '1';
    }
    heroStrip.style.opacity = Math.max(0, 1 - progress * 2);
    scrollIndicator.style.opacity = Math.max(0, 1 - progress * 3.5);
  }
  requestAnimationFrame(updateEffects);
}
requestAnimationFrame(updateEffects);

const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');
const destinationsList = ['Kyoto, Japan', 'Santorini, Greece', 'Swiss Alps', 'Bali, Indonesia', 'Patagonia, Argentina', 'Iceland', 'Machu Picchu, Peru', 'Amalfi Coast, Italy', 'Maldives'];
let placeholderInterval = null;

searchInput.addEventListener('focus', () => {
  if (!searchInput.value) {
    let i = 0;
    placeholderInterval = setInterval(() => {
      if (document.activeElement !== searchInput || searchInput.value) {
        clearInterval(placeholderInterval);
        return;
      }
      searchInput.placeholder = destinationsList[i % destinationsList.length] + '?';
      i++;
    }, 2000);
  }
});
searchInput.addEventListener('blur', () => {
  if (placeholderInterval) clearInterval(placeholderInterval);
  searchInput.placeholder = 'Search tours…';
});
searchBtn.addEventListener('click', () => {
  const dest = searchInput.value.trim() || 'your dream destination';
  showToast(`Planning your trip to ${dest}...`);
});
searchInput.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') searchBtn.click();
});

function showToast(message) {
  const existing = document.querySelector('.custom-toast');
  if (existing) existing.remove();
  const toast = document.createElement('div');
  toast.className = 'custom-toast';
  toast.style.cssText = `position:fixed;bottom:28px;right:28px;z-index:9999;background:#1a1a17;color:#fff;padding:16px 28px;border-radius:16px;font-size:0.85rem;font-family:'Inter',sans-serif;display:flex;align-items:center;gap:12px;box-shadow:0 20px 50px rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.08);transform:translateY(20px);opacity:0;transition:all 0.4s cubic-bezier(0.4,0,0.2,1);`;
  toast.innerHTML = `<i class="bi bi-check-circle-fill" style="color:#4ADE80;font-size:1.1rem;"></i><span>${message}</span>`;
  document.body.appendChild(toast);
  requestAnimationFrame(() => {
    toast.style.transform = 'translateY(0)';
    toast.style.opacity = '1';
  });
  setTimeout(() => {
    toast.style.transform = 'translateY(20px)';
    toast.style.opacity = '0';
    setTimeout(() => toast.remove(), 400);
  }, 3500);
}

// ===== TOUR CATEGORIES CAROUSEL =====
// =========================================================
// TOUR CATEGORIES — CENTERED 3 CARD CAROUSEL
// =========================================================

(function () {

    const track = document.getElementById("catTrack");
    const viewport = document.getElementById("catViewport");
    const dotsContainer = document.getElementById("categoryDots");

    if (!track || !viewport || !dotsContainer) return;

    const cards = Array.from(
        track.querySelectorAll(".category-card")
    );

    if (!cards.length) return;

    const totalCards = cards.length;

    let currentIndex = 1;

    let isAnimating = false;

    let autoPlayInterval = null;


    // =====================================================
    // DIMENSIONS
    // =====================================================

    function getDimensions() {

        const width = window.innerWidth;

        if (width <= 576) {

            return {
                cardWidth: 230,
                step: 230
            };

        }

        if (width <= 992) {

            return {
                cardWidth: 270,
                step: 270
            };

        }

        return {
            cardWidth: 300,
            step: 322
        };

    }


    // =====================================================
    // CARD CLASSES
    // =====================================================

    function updateCardClasses() {

        cards.forEach(function (card, index) {

            card.classList.remove(
                "center-card",
                "side-card",
                "hidden-card"
            );


            if (index === currentIndex) {

                card.classList.add("center-card");

            }

            else if (
                index === currentIndex - 1 ||
                index === currentIndex + 1
            ) {

                card.classList.add("side-card");

            }

            else {

                card.classList.add("hidden-card");

            }

        });

    }


    // =====================================================
    // CENTER ACTIVE CARD
    // =====================================================

    function updateCarousel(animate = true) {

        const dimensions = getDimensions();

        const cardWidth = dimensions.cardWidth;

        const step = dimensions.step;

        const viewportWidth =
            viewport.clientWidth;


        /*
         * Position the ACTIVE card exactly
         * in the center of the viewport.
         */

        const centerPosition =
            (viewportWidth / 2) -
            (cardWidth / 2);


        const activePosition =
            currentIndex * step;


        const translateX =
            centerPosition -
            activePosition;


        track.style.transition = animate
            ? "transform .7s cubic-bezier(.22,.61,.36,1)"
            : "none";


        track.style.transform =
            "translate3d(" +
            translateX +
            "px,0,0)";


        updateCardClasses();

        updateDots();

    }


    // =====================================================
    // DOTS
    // =====================================================

    function createDots() {

        dotsContainer.innerHTML = "";


        cards.forEach(function (card, index) {

            const dot =
                document.createElement("button");


            dot.type = "button";

            dot.className =
                "category-dot" +
                (
                    index === currentIndex
                        ? " active"
                        : ""
                );


            dot.setAttribute(
                "aria-label",
                "Show category " +
                (index + 1)
            );


            dot.addEventListener(
                "click",
                function () {

                    goToSlide(index);

                }
            );


            dotsContainer.appendChild(dot);

        });

    }


    function updateDots() {

        const dots =
            dotsContainer.querySelectorAll(
                ".category-dot"
            );


        dots.forEach(function (dot, index) {

            dot.classList.toggle(
                "active",
                index === currentIndex
            );

        });

    }


    // =====================================================
    // GO TO
    // =====================================================

    function goToSlide(index) {

        if (isAnimating) return;


        if (index < 0) {

            index = totalCards - 1;

        }


        if (index >= totalCards) {

            index = 0;

        }


        if (index === currentIndex) return;


        isAnimating = true;

        currentIndex = index;


        updateCarousel(true);


        setTimeout(function () {

            isAnimating = false;

        }, 720);

    }


    // =====================================================
    // NEXT
    // =====================================================

    function nextSlide() {

        goToSlide(
            currentIndex + 1
        );

    }


    // =====================================================
    // PREVIOUS
    // =====================================================

    function prevSlide() {

        goToSlide(
            currentIndex - 1
        );

    }


    // =====================================================
    // SWIPE
    // =====================================================

    let touchStartX = 0;

    let touchEndX = 0;


    viewport.addEventListener(
        "touchstart",
        function (event) {

            touchStartX =
                event.changedTouches[0].screenX;

        },
        {
            passive: true
        }
    );


    viewport.addEventListener(
        "touchend",
        function (event) {

            touchEndX =
                event.changedTouches[0].screenX;


            const distance =
                touchStartX - touchEndX;


            if (Math.abs(distance) < 50) {
                return;
            }


            if (distance > 0) {

                nextSlide();

            } else {

                prevSlide();

            }

        },
        {
            passive: true
        }
    );


    // =====================================================
    // KEYBOARD
    // =====================================================

    document.addEventListener(
        "keydown",
        function (event) {

            if (event.key === "ArrowRight") {

                nextSlide();

            }

            if (event.key === "ArrowLeft") {

                prevSlide();

            }

        }
    );


    // =====================================================
    // AUTOPLAY
    // =====================================================

    function stopAutoPlay() {

        if (autoPlayInterval) {

            clearInterval(
                autoPlayInterval
            );

            autoPlayInterval = null;

        }

    }


    function startAutoPlay() {

        stopAutoPlay();


        autoPlayInterval =
            setInterval(
                function () {

                    if (!isAnimating) {

                        nextSlide();

                    }

                },
                4500
            );

    }


    const section =
        document.getElementById(
            "categories"
        );


    if (section) {

        section.addEventListener(
            "mouseenter",
            stopAutoPlay
        );


        section.addEventListener(
            "mouseleave",
            startAutoPlay
        );

    }


    // =====================================================
    // RESIZE
    // =====================================================

    let resizeTimer;


    window.addEventListener(
        "resize",
        function () {

            clearTimeout(
                resizeTimer
            );


            resizeTimer =
                setTimeout(
                    function () {

                        updateCarousel(false);

                    },
                    150
                );

        }
    );


    // =====================================================
    // INITIALIZE
    // =====================================================

    createDots();

    updateCarousel(false);

    startAutoPlay();

})();
// ===== SCROLL REVEAL FOR PROMISE CARDS =====
(function() {
  var promiseCards = document.querySelectorAll('.promise-card');
  var promiseHeading = document.querySelector('.promise-heading');
  var promiseBadge = document.querySelector('.promise-badge');

  var revealElements = [];
  if (promiseBadge) revealElements.push(promiseBadge);
  if (promiseHeading) revealElements.push(promiseHeading);
  promiseCards.forEach(function(card) {
    revealElements.push(card);
  });

  revealElements.forEach(function(el) {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.7s cubic-bezier(0.22, 0.61, 0.36, 1), transform 0.7s cubic-bezier(0.22, 0.61, 0.36, 1)';
  });

  var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        var idx = revealElements.indexOf(entry.target);
        var delay = idx * 100;
        setTimeout(function() {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }, delay);
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.15,
    rootMargin: '0px 0px -40px 0px'
  });

  revealElements.forEach(function(el) {
    observer.observe(el);
  });
})();

// ===== DREAM CTA PHOTO SLIDER =====
(function() {
  const ctaPhotos = document.querySelectorAll('.cta-photo');
  if (!ctaPhotos.length) return;
  let currentPhoto = 0;
  setInterval(function() {
    ctaPhotos[currentPhoto].classList.remove('active');
    currentPhoto = (currentPhoto + 1) % ctaPhotos.length;
    ctaPhotos[currentPhoto].classList.add('active');
  }, 3200);
})();

// ===== TOP DESTINATIONS CAROUSEL =====
// ===== TOP DESTINATIONS CAROUSEL =====
(function() {
  const destinationsData = [{
      name: "Japan",
      color: "#c1355c",
      image: "<?= SITE_URL; ?>images/destination-japan.jpg",
      text: "Quiet temples, neon-lit streets and seasons shifting in perfect harmony.",
      region: "Asia · East Asia",
      flags: "🇯🇵"
    },
    {
      name: "Morocco",
      color: "#e00027",
      image: "<?= SITE_URL; ?>images/destination-morocco.jpg",
      text: "Winding medinas, Saharan stars and spice markets glowing at dawn.",
      region: "Africa · North Africa",
      flags: "🇲🇦"
    },
    {
      name: "Iceland",
      color: "#3b6ea5",
      image: "<?= SITE_URL; ?>images/destination-iceland.jpg",
      text: "Glaciers, waterfalls and endless dramatic landscapes.",
      region: "Europe · Northern Europe",
      flags: "🇮🇸"
    },
    {
      name: "Maldives",
      color: "#2f8f6f",
      image: "<?= SITE_URL; ?>images/destination-maldives.jpg",
      text: "Crystal waters, peaceful islands and unforgettable ocean escapes.",
      region: "Asia · Indian Ocean",
      flags: "🇲🇻"
    }
  ];

  let current = 1;
  let isAnimating = false;

  const card = document.querySelector('.destination-main-card');
  const image = document.querySelector('.destination-image img');
  const title = document.querySelector('.destination-card-content h3');
  const description = document.querySelector('.destination-card-content p');
  const region = document.querySelector('.destination-card-content small');
  const locFlags = document.querySelector('.location-flags');
  const flagBadge = document.getElementById('destinationFlag');

  const dotEls = [
    document.getElementById('orbitDot1'),
    document.getElementById('orbitDot2'),
    document.getElementById('orbitDot3'),
    document.getElementById('orbitDot4')
  ];

  function updateOrbitDots() {
    const total = destinationsData.length;
    const previous = destinationsData[(current - 1 + total) % total];
    const active = destinationsData[current];
    const next = destinationsData[(current + 1) % total];
    const fourth = destinationsData[(current + 2) % total];

    dotEls[0].style.backgroundColor = previous.color;
    dotEls[1].style.backgroundColor = next.color;
    dotEls[2].style.backgroundColor = active.color;
    dotEls[3].style.backgroundColor = fourth.color;
  }

  function clearDelays() {
    [title, description, region].forEach(function(el) {
      el.style.transitionDelay = '0s';
    });
    if (flagBadge) flagBadge.style.transitionDelay = '0s';
  }

  function updateDestination(index, direction) {
    if (isAnimating) return;
    isAnimating = true;

    current = (index + destinationsData.length) % destinationsData.length;
    const item = destinationsData[current];
    const dir = direction === 'next' ? 1 : -1;

    /* ── PHASE 1 : Flow out ─────────────────────────── */
    // Card subtle shrink
    card.style.transition = 'transform .55s cubic-bezier(.22,.61,.36,1)';
    card.style.transform = 'scale(0.97)';

    // Flag 3D flip out
    if (flagBadge) {
      flagBadge.style.transitionDelay = '0s';
      flagBadge.style.transition = 'transform .28s ease-in, opacity .18s ease';
      flagBadge.style.transform = 'rotateX(90deg) scale(0.7)';
      flagBadge.style.opacity = '0';
    }

    // Image fade + slight zoom
    image.style.transition = 'opacity .28s ease, transform .35s ease';
    image.style.opacity = '0';
    image.style.transform = 'scale(1.06)';

    // Text stagger out — each element 65ms after the previous
    [title, description, region].forEach(function(el, i) {
      el.style.transitionDelay = '0s';
      el.style.transition = 'opacity .22s ease, transform .22s ease';
      el.style.transitionDelay = (i * 0.065) + 's';
      el.style.opacity = '0';
      el.style.transform = 'translateY(' + (10 * dir) + 'px)';
    });

    // Orbit dots shift colors immediately (CSS transition handles smoothness)
    updateOrbitDots();

    /* ── PHASE 2 : Swap content (no transition) ─────── */
    setTimeout(function() {
      image.src = item.image;
      image.alt = item.name;
      title.textContent = item.name;
      description.textContent = item.text;
      region.textContent = item.region;

      if (flagBadge) flagBadge.textContent = item.flags;

      if (locFlags) {
        var allFlags = destinationsData.map(function(d) { return d.flags; });
        allFlags.sort(function() { return 0.5 - Math.random(); });
        locFlags.textContent = allFlags.slice(0, 2).join(' ');
      }

      // Position elements at "entry" state — no transition
      card.style.transition = 'none';
      card.style.transform = 'scale(0.97)';

      image.style.transition = 'none';
      image.style.transform = 'scale(0.94)';
      image.style.opacity = '0';

      [title, description, region].forEach(function(el) {
        el.style.transition = 'none';
        el.style.transitionDelay = '0s';
        el.style.transform = 'translateY(' + (-10 * dir) + 'px)';
        el.style.opacity = '0';
      });

      if (flagBadge) {
        flagBadge.style.transition = 'none';
        flagBadge.style.transitionDelay = '0s';
        flagBadge.style.transform = 'rotateX(-90deg) scale(0.7)';
        flagBadge.style.opacity = '0';
      }

      // Force reflow so browser registers the "from" state
      void card.offsetWidth;

      /* ── PHASE 3 : Flow in ────────────────────────── */
      // Card expands back
      card.style.transition = 'transform .6s cubic-bezier(.22,.61,.36,1)';
      card.style.transform = 'scale(1)';

      // Image fades in with subtle zoom
      image.style.transition = 'opacity .45s ease, transform .6s cubic-bezier(.22,.61,.36,1)';
      image.style.opacity = '1';
      image.style.transform = 'scale(1)';

      // Text stagger in — each element 75ms after the previous
      [title, description, region].forEach(function(el, i) {
        el.style.transition = 'opacity .32s ease, transform .38s cubic-bezier(.22,.61,.36,1)';
        el.style.transitionDelay = (i * 0.075 + 0.06) + 's';
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
      });

      // Flag 3D flip in
      if (flagBadge) {
        flagBadge.style.transition = 'transform .4s cubic-bezier(.22,.61,.36,1), opacity .25s ease';
        flagBadge.style.transitionDelay = '0.12s';
        flagBadge.style.transform = 'rotateX(0deg) scale(1)';
        flagBadge.style.opacity = '1';
      }

      // Cleanup after everything settles
      setTimeout(function() {
        clearDelays();
        isAnimating = false;
      }, 750);

    }, 340);
  }

  document.querySelector('.destination-prev')?.addEventListener('click', function() {
    updateDestination(current - 1, 'prev');
  });
  document.querySelector('.destination-next')?.addEventListener('click', function() {
    updateDestination(current + 1, 'next');
  });

  // Initial render
  updateDestination(current, 'next');
  setTimeout(function() { isAnimating = false; }, 750);

})();

// ===== JOURNEY STEP ROTATOR =====
(function() {
  const steps = [{
      title: "Discover",
      desc: "Tell us your dream destination and travel style — we shortlist the experiences that fit.",
      avatar: "https://picsum.photos/seed/stepdiscover/200/200.jpg"
    },
    {
      title: "Design",
      desc: "Your dedicated planner builds a day-by-day itinerary around your pace and budget.",
      avatar: "https://picsum.photos/seed/stepdesign/200/200.jpg"
    },
    {
      title: "Book",
      desc: "Flights, stays and local guides are secured and confirmed, all in one place.",
      avatar: "https://picsum.photos/seed/stepbook/200/200.jpg"
    },
    {
      title: "Travel & Discover",
      desc: "Depart with full confidence. We're reachable 24/7 throughout your journey for anything you need.",
      avatar: "https://picsum.photos/seed/steptravel/200/200.jpg"
    }
  ];
  const numEls = document.querySelectorAll('.journey-step-num');
  const titleEl = document.getElementById('journeyStepTitle');
  const descEl = document.getElementById('journeyStepDesc');
  const avatarEl = document.getElementById('journeyAvatar');
  if (!numEls.length || !titleEl || !descEl || !avatarEl) return;

  let stepIndex = 3;

  function renderStep(i) {
    stepIndex = i;
    numEls.forEach(function(el, idx) {
      el.classList.toggle('active', idx === stepIndex);
    });
    [titleEl, descEl, avatarEl].forEach(function(el) {
      el.style.opacity = '0';
    });
    setTimeout(function() {
      titleEl.textContent = steps[stepIndex].title;
      descEl.textContent = steps[stepIndex].desc;
      avatarEl.src = steps[stepIndex].avatar;
      [titleEl, descEl, avatarEl].forEach(function(el) {
        el.style.opacity = '1';
      });
    }, 220);
  }

  numEls.forEach(function(el, idx) {
    el.addEventListener('click', function() {
      renderStep(idx);
    });
  });

  renderStep(stepIndex);
  let journeyTimer = setInterval(function() {
    renderStep((stepIndex + 1) % steps.length);
  }, 4500);
  const journeySection = document.querySelector('.journey-section');
  if (journeySection) {
    journeySection.addEventListener('mouseenter', function() {
      clearInterval(journeyTimer);
    });
    journeySection.addEventListener('mouseleave', function() {
      journeyTimer = setInterval(function() {
        renderStep((stepIndex + 1) % steps.length);
      }, 4500);
    });
  }
})();

// ===== STATS PHOTO MARQUEE =====
(function() {
  const track = document.getElementById('statsMarqueeTrack');
  if (!track) return;
  track.innerHTML += track.innerHTML;
})();


// ===== CTA POPUP MODAL =====
(function() {
  var modal = document.getElementById('ctaModal');
  var closeBtn = document.getElementById('ctaModalClose');
  var form = document.getElementById('ctaForm');
  var successBox = document.getElementById('ctaSuccess');
  var nameInput = document.getElementById('ctaName');
  var phoneInput = document.getElementById('ctaPhone');
  var messageInput = document.getElementById('ctaMessage');
  var nameError = document.getElementById('ctaNameError');
  var phoneError = document.getElementById('ctaPhoneError');
  var messageError = document.getElementById('ctaMessageError');

  if (!modal) return;

  // Open
  document.querySelectorAll('.dream-cta-button').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      modal.classList.add('active');
      document.body.style.overflow = 'hidden';
      resetForm();
      setTimeout(function() { nameInput.focus(); }, 400);
    });
  });

  // Close
  function closeModal() {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }

  closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', function(e) {
    if (e.target === modal) closeModal();
  });
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modal.classList.contains('active')) closeModal();
  });

  // Validate
  function setError(input, errorEl, msg) {
    input.closest('.cta-input-wrap').classList.add('error');
    errorEl.textContent = msg;
    errorEl.classList.add('visible');
  }

  function clearError(input, errorEl) {
    input.closest('.cta-input-wrap').classList.remove('error');
    errorEl.textContent = '';
    errorEl.classList.remove('visible');
  }

  // Live clear
  nameInput.addEventListener('input', function() { clearError(nameInput, nameError); });
  phoneInput.addEventListener('input', function() { clearError(phoneInput, phoneError); });
  messageInput.addEventListener('input', function() { clearError(messageInput, messageError); });

  function validate() {
    var valid = true;
    var nameVal = nameInput.value.trim();
    var phoneVal = phoneInput.value.trim();
    var msgVal = messageInput.value.trim();

    if (!nameVal || nameVal.length < 2) {
      setError(nameInput, nameError, 'Please enter your full name.');
      valid = false;
    }

    var phoneClean = phoneVal.replace(/[\s\-\(\)\+]/g, '');
    if (!phoneVal || phoneClean.length < 7 || !/^\d+$/.test(phoneClean)) {
      setError(phoneInput, phoneError, 'Please enter a valid mobile number.');
      valid = false;
    }

    if (!msgVal || msgVal.length < 3) {
      setError(messageInput, messageError, 'Please write a short message.');
      valid = false;
    }

    return valid;
  }

  // Submit
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    if (!validate()) return;

    var btn = document.getElementById('ctaSubmitBtn');
    btn.style.pointerEvents = 'none';
    btn.style.opacity = '0.6';
    btn.querySelector('span').textContent = 'Sending...';

    setTimeout(function() {
      form.style.display = 'none';
      document.querySelector('.cta-modal-header').style.display = 'none';
      successBox.classList.add('show');
      btn.style.pointerEvents = '';
      btn.style.opacity = '';
      btn.querySelector('span').textContent = 'Send Message';

      setTimeout(closeModal, 3200);
    }, 1200);
  });

  function resetForm() {
    form.reset();
    form.style.display = '';
    document.querySelector('.cta-modal-header').style.display = '';
    successBox.classList.remove('show');
    clearError(nameInput, nameError);
    clearError(phoneInput, phoneError);
    clearError(messageInput, messageError);
  }
})();


