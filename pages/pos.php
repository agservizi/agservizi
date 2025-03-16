<div class="row">
    <!-- Colonna sinistra: Selezione prodotti -->
    <div class="col-md-3">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Gestori</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="?page=pos" class="list-group-item list-group-item-action <?php echo !isset($_GET['gestore']) ? 'active' : ''; ?>">
                        Tutti i gestori
                    </a>
                    <?php foreach (getProviders() as $key => $name): ?>
                    <a href="?page=pos&gestore=<?php echo $key; ?>" class="list-group-item list-group-item-action <?php echo isset($_GET['gestore']) && $_GET['gestore'] == $key ? 'active' : ''; ?>">
                        <?php echo $name; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Prodotti</h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="sim-tab" data-bs-toggle="tab" data-bs-target="#sim" type="button" role="tab" aria-controls="sim" aria-selected="true">SIM</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="devices-tab" data-bs-toggle="tab" data-bs-target="#devices" type="button" role="tab" aria-controls="devices" aria-selected="false">Dispositivi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="recharges-tab" data-bs-toggle="tab" data-bs-target="#recharges" type="button" role="tab" aria-controls="recharges" aria-selected="false">Ricariche</button>
                    </li>
                </ul>
                <div class="tab-content p-2" id="productTabsContent">
                    <div class="tab-pane fade show active" id="sim" role="tabpanel" aria-labelledby="sim-tab">
                        <div class="row row-cols-2 g-2">
                            <?php 
                            $provider = isset($_GET['gestore']) ? $_GET['gestore'] : null;
                            $sim_products = getProducts($provider, 'sim');
                            foreach ($sim_products as $product): 
                            ?>
                            <div class="col">
                                <div class="card h-100 product-card" data-id="<?php echo $product['id']; ?>">
                                    <div class="card-body p-2 text-center">
                                        <h6 class="card-title"><?php echo $product['nome']; ?></h6>
                                        <p class="card-text"><?php echo number_format($product['prezzo'], 2); ?> €</p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="devices" role="tabpanel" aria-labelledby="devices-tab">
                        <div class="row row-cols-2 g-2">
                            <?php 
                            $devices_products = getProducts($provider, 'dispositivo');
                            foreach ($devices_products as $product): 
                            ?>
                            <div class="col">
                                <div class="card h-100 product-card" data-id="<?php echo $product['id']; ?>">
                                    <div class="card-body p-2 text-center">
                                        <h6 class="card-title"><?php echo $product['nome']; ?></h6>
                                        <p class="card-text"><?php echo number_format($product['prezzo'], 2); ?> €</p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="recharges" role="tabpanel" aria-labelledby="recharges-tab">
                        <div class="row row-cols-2 g-2">
                            <?php 
                            $recharges_products = getProducts($provider, 'ricarica');
                            foreach ($recharges_products as $product): 
                            ?>
                            <div class="col">
                                <div class="card h-100 product-card" data-id="<?php echo $product['id']; ?>">
                                    <div class="card-body p-2 text-center">
                                        <h6 class="card-title"><?php echo $product['nome']; ?></h6>
                                        <p class="card-text"><?php echo number_format($product['prezzo'], 2); ?> €</p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Colonna centrale: Carrello -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Carrello</h5>
                <button class="btn btn-sm btn-outline-light" id="clearCart">Svuota</button>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Codice</th>
                            <th>Descrizione</th>
                            <th class="text-center">Q.tà</th>
                            <th class="text-end">Prezzo</th>
                            <th class="text-end">Totale</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cartItems">
                        <?php if (empty($_SESSION['cart'])): ?>
                        <tr>
                            <td colspan="6" class="text-center py-3">Il carrello è vuoto</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                        <tr>
                            <td><?php echo $item['codice']; ?></td>
                            <td><?php echo $item['nome']; ?></td>
                            <td class="text-center">
                                <div class="input-group input-group-sm quantity-control">
                                    <button class="btn btn-outline-secondary quantity-decrease" type="button" data-id="<?php echo $item['id']; ?>">-</button>
                                    <input type="text" class="form-control text-center quantity-input" value="<?php echo $item['quantity']; ?>" readonly>
                                    <button class="btn btn-outline-secondary quantity-increase" type="button" data-id="<?php echo $item['id']; ?>">+</button>
                                </div>
                            </td>
                            <td class="text-end"><?php echo number_format($item['prezzo'], 2); ?> €</td>
                            <td class="text-end"><?php echo number_format($item['prezzo'] * $item['quantity'], 2); ?> €</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-danger remove-item" data-id="<?php echo $item['id']; ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <?php $totals = calculateCartTotal(); ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="discount" class="form-label">Sconto</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="discount" min="0" step="0.01" value="0">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="paymentMethod" class="form-label">Metodo di pagamento</label>
                            <select class="form-select" id="paymentMethod">
                                <option value="contanti">Contanti</option>
                                <option value="carta">Carta di credito/debito</option>
                                <option value="bonifico">Bonifico bancario</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td>Subtotale</td>
                                <td class="text-end" id="subtotal"><?php echo number_format($totals['subtotal'], 2); ?> €</td>
                            </tr>
                            <tr>
                                <td>IVA</td>
                                <td class="text-end" id="iva"><?php echo number_format($totals['iva'], 2); ?> €</td>
                            </tr>
                            <tr>
                                <td>Sconto</td>
                                <td class="text-end" id="discountAmount">0.00 €</td>
                            </tr>
                            <tr class="fw-bold">
                                <td>Totale</td>
                                <td class="text-end" id="total"><?php echo number_format($totals['total'], 2); ?> €</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-primary" id="checkoutBtn" <?php echo empty($_SESSION['cart']) ? 'disabled' : ''; ?>>
                        <i class="bi bi-receipt"></i> Emetti scontrino
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Colonna destra: Dati cliente -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Dati cliente</h5>
            </div>
            <div class="card-body">
                <form id="customerForm">
                    <div class="mb-3">
                        <label for="codiceFiscale" class="form-label">Codice Fiscale</label>
                        <input type="text" class="form-control" id="codiceFiscale" name="codice_fiscale">
                    </div>
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome">
                    </div>
                    <div class="mb-3">
                        <label for="cognome" class="form-label">Cognome</label>
                        <input type="text" class="form-control" id="cognome" name="cognome">
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Cellulare</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="abilitaSms">
                        <label class="form-check-label" for="abilitaSms">
                            Abilità SMS
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="abilitaEmail">
                        <label class="form-check-label" for="abilitaEmail">
                            Abilità E-Mail
                        </label>
                    </div>
                    <div class="mb-3">
                        <label for="tesseraFidelity" class="form-label">Tessera Fidelity</label>
                        <input type="text" class="form-control" id="tesseraFidelity" name="tessera_fidelity">
                    </div>
                    <div class="mb-3">
                        <label for="dataRicontatto" class="form-label">Data Ricontatto</label>
                        <input type="date" class="form-control" id="dataRicontatto" name="data_ricontatto">
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal per la conferma della transazione -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalLabel">Conferma transazione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle-fill"></i> Transazione completata con successo!
                </div>
                <p>Numero scontrino: <span id="receiptNumber">-</span></p>
                <p>Data: <span id="receiptDate">-</span></p>
                <p>Totale: <span id="receiptTotal">-</span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary" id="printReceiptBtn">Stampa scontrino</button>
            </div>
        </div>
    </div>
</div>

