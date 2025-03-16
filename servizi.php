<?php
$page_title = "I Nostri Servizi";
include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>I Nostri Servizi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Servizi</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Services Overview -->
<section class="services-overview">
    <div class="container">
        <div class="services-intro" data-aos="fade-up">
            <h2>Soluzioni Complete per le Tue Esigenze</h2>
            <p>AG Servizi Via Plinio 72 offre una vasta gamma di servizi per privati e aziende. Dalla gestione dei pagamenti alle spedizioni, dai contratti di telefonia ed energia ai servizi digitali come SPID, PEC e Firma Digitale, siamo il tuo punto di riferimento unico per semplificare la tua vita quotidiana.</p>
        </div>
        
        <!-- Services Categories -->
        <div class="services-categories">
            <!-- Pagamenti -->
            <div class="service-category" data-aos="fade-up">
                <div class="category-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="category-content">
                    <h3>Pagamenti</h3>
                    <p>Effettua pagamenti di bollette, ricariche telefoniche, MAV, RAV, F24 e molto altro in modo semplice e veloce.</p>
                    <ul>
                        <li>Bollette (luce, gas, acqua, telefono)</li>
                        <li>Ricariche telefoniche</li>
                        <li>Bollo auto</li>
                        <li>MAV, RAV, F24</li>
                        <li>PagoPA</li>
                    </ul>
                    <a href="servizi/pagamenti.php" class="btn btn-outline">Scopri di più</a>
                </div>
            </div>
            
            <!-- Spedizioni -->
            <div class="service-category" data-aos="fade-up">
                <div class="category-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div class="category-content">
                    <h3>Spedizioni</h3>
                    <p>Invia pacchi e documenti in Italia e all'estero con i migliori corrieri, a tariffe competitive.</p>
                    <ul>
                        <li>Spedizioni nazionali</li>
                        <li>Spedizioni internazionali</li>
                        <li>Ritiro e consegna a domicilio</li>
                        <li>Imballaggio professionale</li>
                        <li>Tracking online</li>
                    </ul>
                    <a href="servizi/spedizioni.php" class="btn btn-outline">Scopri di più</a>
                </div>
            </div>
            
            <!-- Telefonia -->
            <div class="service-category" data-aos="fade-up">
                <div class="category-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="category-content">
                    <h3>Telefonia</h3>
                    <p>Attiva contratti di telefonia mobile e fissa con i migliori operatori, trovando l'offerta più adatta alle tue esigenze.</p>
                    <ul>
                        <li>Offerte Fastweb</li>
                        <li>Offerte WindTre</li>
                        <li>Offerte Iliad</li>
                        <li>Offerte Sky</li>
                        <li>Consulenza personalizzata</li>
                    </ul>
                    <a href="servizi/telefonia.php" class="btn btn-outline">Scopri di più</a>
                </div>
            </div>
            
            <!-- Energia -->
            <div class="service-category" data-aos="fade-up">
                <div class="category-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="category-content">
                    <h3>Energia</h3>
                    <p>Attiva contratti luce e gas con i migliori fornitori sul mercato, risparmiando sulla bolletta.</p>
                    <ul>
                        <li>Offerte Enel Energia</li>
                        <li>Offerte A2A Energia</li>
                        <li>Offerte Fastweb Energia</li>
                        <li>Analisi consumi</li>
                        <li>Consulenza per il risparmio</li>
                    </ul>
                    <a href="servizi/energia.php" class="btn btn-outline">Scopri di più</a>
                </div>
            </div>
            
            <!-- SPID -->
            <div class="service-category" data-aos="fade-up">
                <div class="category-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="category-content">
                    <h3>SPID</h3>
                    <p>Attiva la tua identità digitale SPID per accedere ai servizi online della Pubblica Amministrazione e dei privati aderenti.</p>
                    <ul>
                        <li>Attivazione guidata</li>
                        <li>Assistenza completa</li>
                        <li>Riconoscimento in sede</li>
                        <li>Supporto post-attivazione</li>
                    </ul>
                    <a href="servizi/spid.php" class="btn btn-outline">Scopri di più</a>
                </div>
            </div>
            
            <!-- PEC -->
            <div class="service-category" data-aos="fade-up">
                <div class="category-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="category-content">
                    <h3>PEC</h3>
                    <p>Attiva la tua casella di Posta Elettronica Certificata per comunicazioni ufficiali con valore legale.</p>
                    <ul>
                        <li>Attivazione immediata</li>
                        <li>Spazio casella personalizzabile</li>
                        <li>Assistenza tecnica</li>
                        <li>Rinnovo automatico</li>
                    </ul>
                    <a href="servizi/pec.php" class="btn btn-outline">Scopri di più</a>
                </div>
            </div>
            
            <!-- Firma Digitale -->
            <div class="service-category" data-aos="fade-up">
                <div class="category-icon">
                    <i class="fas fa-signature"></i>
                </div>
                <div class="category-content">
                    <h3>Firma Digitale</h3>
                    <p>Richiedi e attiva la tua firma digitale per firmare documenti elettronici con pieno valore legale.</p>
                    <ul>
                        <li>Firma remota</li>
                        <li>Firma su smart card</li>
                        <li>Firma su token USB</li>
                        <li>Validità 3 anni</li>
                        <li>Assistenza completa</li>
                    </ul>
                    <a href="servizi/firma-digitale.php" class="btn btn-outline">Scopri di più</a>
                </div>
            </div>
            
            <!-- Visure -->
            <div class="service-category" data-aos="fade-up">
                <div class="category-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="category-content">
                    <h3>Visure</h3>
                    <p>Richiedi visure camerali, catastali e certificati presso la nostra agenzia.</p>
                    <ul>
                        <li>Visure camerali</li>
                        <li>Visure catastali</li>
                        <li>Visure PRA</li>
                        <li>Certificati</li>
                        <li>Consegna rapida</li>
                    </ul>
                    <a href="servizi/visure.php" class="btn btn-outline">Scopri di più</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="why-choose-us" data-aos="fade-up">
    <div class="container">
        <h2 class="section-title">Perché Scegliere AG Servizi</h2>
        
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Professionalità</h3>
                <p>Personale qualificato e costantemente aggiornato per offrirti il miglior servizio possibile.</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <h3>Convenienza</h3>
                <p>Tariffe competitive e trasparenti, senza costi nascosti o sorprese.</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Assistenza</h3>
                <p>Supporto continuo prima, durante e dopo l'erogazione dei servizi.</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Rapidità</h3>
                <p>Servizi veloci ed efficienti per rispettare il tuo tempo.</p>
            </div>
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

