<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero" data-aos="fade-up">
  <div class="hero-content">
    <h1>Agenzia Plinio - Soluzioni Semplici per la Tua Vita Digitale</h1>
    <p>Servizi di pagamento, spedizioni, telefonia, energia e soluzioni digitali</p>
    <div class="hero-buttons">
      <a href="#services" class="btn btn-primary">Scopri i Servizi</a>
      <a href="contatti.php" class="btn btn-outline">Contattaci</a>
    </div>
  </div>
</section>

<!-- Featured Services -->
<section id="services" class="services-grid">
  <div class="container">
    <h2 class="section-title" data-aos="fade-up">I Nostri Servizi</h2>
    <div class="services-container">
      <?php
      // Array of services
      $services = [
        [
          'icon' => 'fa-credit-card',
          'title' => 'Pagamenti',
          'description' => 'Paga bollette, ricariche e molto altro in modo semplice e veloce.',
          'link' => 'servizi/pagamenti.php'
        ],
        [
          'icon' => 'fa-shipping-fast',
          'title' => 'Spedizioni',
          'description' => 'Spedisci pacchi in Italia e all\'estero con i migliori corrieri.',
          'link' => 'servizi/spedizioni.php'
        ],
        [
          'icon' => 'fa-mobile-alt',
          'title' => 'Telefonia',
          'description' => 'Scopri le migliori offerte di telefonia mobile e fissa.',
          'link' => 'servizi/telefonia.php'
        ],
        [
          'icon' => 'fa-bolt',
          'title' => 'Energia',
          'description' => 'Attiva contratti luce e gas con i migliori fornitori sul mercato.',
          'link' => 'servizi/energia.php'
        ],
        [
          'icon' => 'fa-id-card',
          'title' => 'SPID',
          'description' => 'Attiva la tua identità digitale in modo semplice e veloce.',
          'link' => 'servizi/spid.php'
        ],
        [
          'icon' => 'fa-envelope',
          'title' => 'PEC',
          'description' => 'Crea e gestisci la tua casella di Posta Elettronica Certificata.',
          'link' => 'servizi/pec.php'
        ],
        [
          'icon' => 'fa-signature',
          'title' => 'Firma Digitale',
          'description' => 'Richiedi e attiva la tua firma digitale per documenti legali.',
          'link' => 'servizi/firma-digitale.php'
        ],
        [
          'icon' => 'fa-search',
          'title' => 'Visure',
          'description' => 'Richiedi visure camerali, catastali e certificati.',
          'link' => 'servizi/visure.php'
        ]
      ];

      // Display services
      foreach ($services as $service) {
        echo '<div class="service-card" data-aos="fade-up" data-aos-delay="100">';
        echo '<div class="service-icon"><i class="fas ' . $service['icon'] . '"></i></div>';
        echo '<h3>' . $service['title'] . '</h3>';
        echo '<p>' . $service['description'] . '</p>';
        echo '<a href="' . $service['link'] . '" class="btn-link">Scopri di più <i class="fas fa-arrow-right"></i></a>';
        echo '</div>';
      }
      ?>
    </div>
  </div>
</section>

<!-- Partners -->
<section class="partners">
  <div class="container">
    <h2 class="section-title" data-aos="fade-up">I Nostri Partner</h2>
    <div class="partners-slider" data-aos="fade-up" data-aos-delay="100">
      <div class="partner"><img src="assets/images/partners/fastweb.png" alt="Fastweb"></div>
      <div class="partner"><img src="assets/images/partners/windtre.png" alt="WindTre"></div>
      <div class="partner"><img src="assets/images/partners/iliad.png" alt="Iliad"></div>
      <div class="partner"><img src="assets/images/partners/enel.png" alt="Enel Energia"></div>
      <div class="partner"><img src="assets/images/partners/a2a.png" alt="A2A Energia"></div>
      <div class="partner"><img src="assets/images/partners/poste.png" alt="Poste Italiane"></div>
    </div>
  </div>
</section>

<!-- Latest News -->
<section class="latest-news">
  <div class="container">
    <h2 class="section-title" data-aos="fade-up">Ultime Novità</h2>
    <div class="news-container">
      <?php
      // Get latest 3 articles from database (simplified example)
      $articles = [
        [
          'title' => 'Come attivare lo SPID in 5 semplici passi',
          'date' => '15 Marzo 2023',
          'image' => 'assets/images/blog/spid.jpg',
          'excerpt' => 'Scopri come attivare facilmente la tua identità digitale SPID seguendo questa semplice guida passo-passo.',
          'link' => 'blog/come-attivare-spid.php'
        ],
        [
          'title' => 'Risparmiare sulla bolletta con le nuove offerte luce e gas',
          'date' => '10 Marzo 2023',
          'image' => 'assets/images/blog/energia.jpg',
          'excerpt' => 'Consigli pratici per risparmiare sulle bollette di luce e gas approfittando delle nuove offerte sul mercato.',
          'link' => 'blog/risparmiare-bollette.php'
        ],
        [
          'title' => 'Vantaggi della firma digitale per professionisti e aziende',
          'date' => '5 Marzo 2023',
          'image' => 'assets/images/blog/firma-digitale.jpg',
          'excerpt' => 'Scopri tutti i vantaggi della firma digitale per la tua attività professionale o aziendale.',
          'link' => 'blog/vantaggi-firma-digitale.php'
        ]
      ];

      // Display articles
      foreach ($articles as $article) {
        echo '<div class="news-card" data-aos="fade-up" data-aos-delay="100">';
        echo '<div class="news-image"><img src="' . $article['image'] . '" alt="' . $article['title'] . '"></div>';
        echo '<div class="news-content">';
        echo '<span class="news-date">' . $article['date'] . '</span>';
        echo '<h3>' . $article['title'] . '</h3>';
        echo '<p>' . $article['excerpt'] . '</p>';
        echo '<a href="' . $article['link'] . '" class="btn-link">Leggi di più <i class="fas fa-arrow-right"></i></a>';
        echo '</div>';
        echo '</div>';
      }
      ?>
    </div>
    <div class="text-center" data-aos="fade-up">
      <a href="blog.php" class="btn btn-primary">Vedi tutti gli articoli</a>
    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="cta" data-aos="fade-up">
  <div class="container">
    <div class="cta-content">
      <h2>Hai bisogno di assistenza?</h2>
      <p>Contattaci per ricevere supporto o informazioni sui nostri servizi</p>
      <a href="contatti.php" class="btn btn-light">Contattaci ora</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

