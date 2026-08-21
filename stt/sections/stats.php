<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Our Journey in Numbers</title>
<style>
  /* =========================================================
     SCOPED RESET — prevents host page's global CSS
     (italic fonts, pill backgrounds, list styles, etc.)
     from leaking into this section
     ========================================================= */
  .stats-section, .stats-section * {
    box-sizing: border-box;
    font-style: normal !important;
    text-decoration: none !important;
    list-style: none !important;
  }

  body {
    margin: 0;
    background: #fffdf5;
  }

  /* =========================================================
     SECTION / BACKGROUND
     ========================================================= */
  .stats-section {
    position: relative;
    width: 100%;
    background: #f6f0c8 !important;
    padding: 60px 20px 90px;
    overflow: hidden;
    font-family: 'Poppins', 'Segoe UI', sans-serif !important;
  }

  .stats-worldmap {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }

  .stats-inner {
    position: relative;
    max-width: 1200px;
    width: 100%;
    margin: 0 auto !important;
    text-align: center !important;
    z-index: 2;
  }

  /* Badge */
  .stats-badge {
    display: inline-block;
    padding: 8px 22px;
    border: 1px solid #c9a86a;
    border-radius: 30px;
    color: #8a6d3b !important;
    font-size: 13px;
    letter-spacing: .5px;
    background: rgba(255,255,255,.4) !important;
  }

  /* Big number + avatars */
  .stats-number-wrap {
    position: relative;
    margin: 30px auto 10px;
    width: fit-content;
  }

  .stats-number {
    font-size: 90px;
    font-weight: 700 !important;
    color: #2b2b2b !important;
    line-height: 1;
    font-style: normal !important;
  }

  .stats-tag {
    display: block;
    margin-top: 6px;
    font-size: 15px;
    color: #6b6b6b !important;
    letter-spacing: .5px;
    background: none !important;
    padding: 0 !important;
    border-radius: 0 !important;
  }

  .stats-avatar {
    position: absolute;
    width: 56px;
    height: 56px;
    border-radius: 50% !important;
    border: 3px solid #fff;
    object-fit: cover;
    box-shadow: 0 6px 14px rgba(0,0,0,.15);
    animation: statsFloat 4s ease-in-out infinite;
  }

  /* kill any tooltip/label bubble the host page attaches to avatars */
  .stats-avatar::before,
  .stats-avatar::after {
    content: none !important;
    display: none !important;
  }

  @keyframes statsFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
  }

  /* Stats row */
  .stats-row {
    display: flex !important;
    justify-content: center !important;
    align-items: flex-start;
    gap: 60px;
    margin-top: 55px;
    flex-wrap: wrap;
    width: 100%;
  }

  .stats-item {
    max-width: 260px;
    flex: 1 1 220px;
    text-align: center !important;
  }

  .stats-item .num {
    font-size: 40px;
    font-weight: 700 !important;
    color: #2b2b2b !important;
    font-style: normal !important;
  }

  /* Force plain text label — no pill / background from host theme */
  .stats-item .tag-label {
    display: block !important;
    font-size: 14px;
    font-weight: 600 !important;
    color: #3d3d3d !important;
    margin-top: 4px;
    background: none !important;
    background-color: transparent !important;
    padding: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
  }

  .stats-item .divider {
    width: 40px;
    height: 2px;
    background: #c9a86a !important;
    margin: 14px auto;
  }

  .stats-item .desc {
    font-size: 13px;
    color: #7a7a7a !important;
    line-height: 1.5;
    margin: 0;
  }

  /* =========================================================
     STATS IMAGE MARQUEE
     ========================================================= */

  .stats-marquee {
    position: relative;
    width: 100%;
    max-width: 100vw;
    margin-top: 70px;
    overflow: hidden !important;
    -webkit-mask-image: linear-gradient(to right, transparent 0%, #000 4%, #000 96%, transparent 100%);
    mask-image: linear-gradient(to right, transparent 0%, #000 4%, #000 96%, transparent 100%);
  }

  .stats-marquee-track {
    display: flex !important;
    align-items: center;
    width: max-content;
    gap: 22px;
    animation: statsImageMarquee 38s linear infinite;
    will-change: transform;
  }

  /* explicit reset so host page's `img, div` rules can't resize/reshape cards */
  .stats-marquee-item {
    position: relative;
    flex: 0 0 210px !important;
    width: 210px !important;
    max-width: 210px !important;
    height: 360px !important;
    max-height: 360px !important;
    overflow: hidden !important;
    border-radius: 28px;
    background: #ddd;
    transform-origin: center center;
    box-shadow: 0 12px 30px rgba(60, 40, 20, 0.08);
    transition: transform .4s ease, box-shadow .4s ease;
    margin: 0 !important;
    padding: 0 !important;
  }

  .stats-marquee-item img {
    display: block !important;
    width: 100% !important;
    height: 100% !important;
    max-width: none !important;
    object-fit: cover !important;
    transition: transform .6s ease;
    margin: 0 !important;
    border-radius: 0 !important;
  }

  .stats-marquee-item:hover {
    transform: rotate(0deg) scale(1.03);
    z-index: 5;
    box-shadow: 0 20px 45px rgba(60, 40, 20, 0.16);
  }

  .stats-marquee-item:hover img {
    transform: scale(1.05);
  }

  .stats-marquee-item.item-1 { height: 330px !important; max-height: 330px !important; transform: rotate(-3deg) translateY(12px); }
  .stats-marquee-item.item-2 { height: 365px !important; max-height: 365px !important; transform: rotate(2deg) translateY(-4px); }
  .stats-marquee-item.item-3 { height: 375px !important; max-height: 375px !important; transform: rotate(-1.5deg) translateY(0); }
  .stats-marquee-item.item-4 { height: 390px !important; max-height: 390px !important; transform: rotate(2deg) translateY(-12px); }
  .stats-marquee-item.item-5 { height: 410px !important; max-height: 410px !important; transform: rotate(-2deg) translateY(-20px); }
  .stats-marquee-item.item-6 { height: 370px !important; max-height: 370px !important; transform: rotate(2deg) translateY(-2px); }
  .stats-marquee-item.item-7 { height: 350px !important; max-height: 350px !important; transform: rotate(-2deg) translateY(10px); }
  .stats-marquee-item.item-8 { height: 385px !important; max-height: 385px !important; transform: rotate(2deg) translateY(-8px); }
  .stats-marquee-item.item-9 { height: 340px !important; max-height: 340px !important; transform: rotate(-3deg) translateY(12px); }

  @keyframes statsImageMarquee {
    from { transform: translateX(0); }
    to { transform: translateX(-50%); }
  }

  .stats-marquee:hover .stats-marquee-track {
    animation-play-state: paused;
  }

  @media (min-width: 1200px) {
    .stats-marquee-item { width: 225px !important; max-width: 225px !important; flex-basis: 225px !important; }
    .stats-marquee-track { gap: 24px; animation-duration: 42s; }
  }

  @media (max-width: 991px) {
    .stats-marquee { margin-top: 55px; }
    .stats-marquee-track { gap: 16px; animation-duration: 32s; }
    .stats-marquee-item { width: 180px !important; max-width: 180px !important; flex-basis: 180px !important; height: 310px !important; max-height: 310px !important; border-radius: 22px; }
    .stats-row { gap: 30px; }
  }

  @media (max-width: 767px) {
    .stats-number { font-size: 60px; }
    .stats-avatar { width: 40px; height: 40px; }
    .stats-section { padding: 40px 15px 60px; }
    .stats-marquee {
      margin-top: 40px;
      -webkit-mask-image: linear-gradient(to right, transparent 0%, #000 8%, #000 92%, transparent 100%);
      mask-image: linear-gradient(to right, transparent 0%, #000 8%, #000 92%, transparent 100%);
    }
    .stats-marquee-track { gap: 12px; animation-duration: 28s; }
    .stats-marquee-item,
    .stats-marquee-item.item-1, .stats-marquee-item.item-2, .stats-marquee-item.item-3,
    .stats-marquee-item.item-4, .stats-marquee-item.item-5, .stats-marquee-item.item-6,
    .stats-marquee-item.item-7, .stats-marquee-item.item-8, .stats-marquee-item.item-9 {
      width: 150px !important; max-width: 150px !important; flex-basis: 150px !important;
      height: 245px !important; max-height: 245px !important; border-radius: 18px; transform: none;
    }
  }

  @media (max-width: 480px) {
    .stats-marquee-item,
    .stats-marquee-item.item-1, .stats-marquee-item.item-2, .stats-marquee-item.item-3,
    .stats-marquee-item.item-4, .stats-marquee-item.item-5, .stats-marquee-item.item-6,
    .stats-marquee-item.item-7, .stats-marquee-item.item-8, .stats-marquee-item.item-9 {
      width: 135px !important; max-width: 135px !important; flex-basis: 135px !important;
      height: 220px !important; max-height: 220px !important; border-radius: 16px;
    }
    .stats-marquee-track { gap: 10px; }
    .stats-row { gap: 20px; }
  }
</style>
</head>
<body>

<section class="stats-section">
  <svg class="stats-worldmap" viewBox="0 0 1000 460" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <g fill="#c9a86a">
      <g opacity=".55">
        <circle cx="120" cy="140" r="3" /><circle cx="140" cy="150" r="3" /><circle cx="160" cy="145" r="3" />
        <circle cx="130" cy="165" r="3" /><circle cx="155" cy="170" r="3" /><circle cx="180" cy="160" r="3" />
        <circle cx="200" cy="150" r="3" /><circle cx="220" cy="155" r="3" /><circle cx="240" cy="145" r="3" />
        <circle cx="150" cy="190" r="3" /><circle cx="170" cy="200" r="3" /><circle cx="190" cy="210" r="3" />
        <circle cx="210" cy="205" r="3" /><circle cx="230" cy="195" r="3" />
      </g>
      <g opacity=".55">
        <circle cx="420" cy="120" r="3" /><circle cx="445" cy="115" r="3" /><circle cx="470" cy="125" r="3" />
        <circle cx="495" cy="110" r="3" /><circle cx="430" cy="145" r="3" /><circle cx="455" cy="150" r="3" />
        <circle cx="480" cy="140" r="3" /><circle cx="505" cy="135" r="3" /><circle cx="530" cy="120" r="3" />
        <circle cx="440" cy="170" r="3" /><circle cx="465" cy="180" r="3" /><circle cx="490" cy="175" r="3" />
        <circle cx="515" cy="165" r="3" /><circle cx="540" cy="150" r="3" />
      </g>
      <g opacity=".55">
        <circle cx="620" cy="160" r="3" /><circle cx="645" cy="150" r="3" /><circle cx="670" cy="165" r="3" />
        <circle cx="695" cy="155" r="3" /><circle cx="720" cy="170" r="3" /><circle cx="745" cy="160" r="3" />
        <circle cx="770" cy="150" r="3" /><circle cx="640" cy="190" r="3" /><circle cx="665" cy="200" r="3" />
        <circle cx="690" cy="195" r="3" /><circle cx="715" cy="205" r="3" /><circle cx="740" cy="195" r="3" />
        <circle cx="800" cy="165" r="3" /><circle cx="825" cy="175" r="3" />
      </g>
      <g opacity=".4">
        <circle cx="470" cy="250" r="3" /><circle cx="495" cy="260" r="3" /><circle cx="480" cy="285" r="3" />
        <circle cx="505" cy="295" r="3" /><circle cx="460" cy="310" r="3" /><circle cx="485" cy="320" r="3" />
      </g>
      <g opacity=".4">
        <circle cx="700" cy="300" r="3" /><circle cx="725" cy="310" r="3" /><circle cx="750" cy="320" r="3" />
        <circle cx="715" cy="335" r="3" /><circle cx="740" cy="345" r="3" />
      </g>
    </g>
  </svg>

  <div class="stats-inner">
    <span class="stats-badge">✥&nbsp; Our Journey in Numbers &nbsp;✥</span>

    <div class="stats-number-wrap">
      <div class="stats-number">12,000+</div>
      <span class="stats-tag">Happy Travellers</span>
      <img class="stats-avatar" style="top:-6px; left:-96px;" src="https://picsum.photos/seed/travelerA1/80/80.jpg" alt="">
      <img class="stats-avatar" style="top:-42px; right:-150px; animation-delay:1s;" src="https://picsum.photos/seed/travelerB2/80/80.jpg" alt="">
      <img class="stats-avatar" style="bottom:-10px; left:-130px; animation-delay:2s;" src="https://picsum.photos/seed/travelerC3/80/80.jpg" alt="">
      <img class="stats-avatar" style="bottom:-34px; right:-110px; animation-delay:.5s;" src="https://picsum.photos/seed/travelerD4/80/80.jpg" alt="">
    </div>

    <div class="stats-row">
      <div class="stats-item">
        <div class="num">60+</div>
        <span class="tag-label">Unique Tour Packages</span>
        <div class="divider"></div>
        <p class="desc">From budget adventures to private luxury escapes</p>
      </div>
      <div class="stats-item">
        <div class="num">1.5+</div>
        <span class="tag-label">Years of Experience</span>
        <div class="divider"></div>
        <p class="desc">Perfecting meaningful travel experiences since 2009</p>
      </div>
      <div class="stats-item">
        <div class="num">98%</div>
        <span class="tag-label">Repeat Booking Rate</span>
        <div class="divider"></div>
        <p class="desc">Most travelers come back to plan their next trip</p>
      </div>
    </div>

    <div class="stats-marquee">
      <div class="stats-marquee-track">
        <div class="stats-marquee-item item-1"><img src="https://picsum.photos/seed/roadtrip1/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-2"><img src="https://picsum.photos/seed/tramcity2/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-3"><img src="https://picsum.photos/seed/gaudipark3/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-4"><img src="https://picsum.photos/seed/hikerjoy4/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-5"><img src="https://picsum.photos/seed/forestwalk5/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-6"><img src="https://picsum.photos/seed/carnival6/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-7"><img src="https://picsum.photos/seed/selfiepeak7/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-8"><img src="https://picsum.photos/seed/lonelyroad8/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-9"><img src="https://picsum.photos/seed/oldtram9/500/650.jpg" alt=""></div>

        <div class="stats-marquee-item item-1"><img src="https://picsum.photos/seed/roadtrip1/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-2"><img src="https://picsum.photos/seed/tramcity2/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-3"><img src="https://picsum.photos/seed/gaudipark3/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-4"><img src="https://picsum.photos/seed/hikerjoy4/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-5"><img src="https://picsum.photos/seed/forestwalk5/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-6"><img src="https://picsum.photos/seed/carnival6/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-7"><img src="https://picsum.photos/seed/selfiepeak7/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-8"><img src="https://picsum.photos/seed/lonelyroad8/500/650.jpg" alt=""></div>
        <div class="stats-marquee-item item-9"><img src="https://picsum.photos/seed/oldtram9/500/650.jpg" alt=""></div>
      </div>
    </div>
  </div>
</section>

</body>
</html>