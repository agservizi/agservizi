<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Gestione Prodotti</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
        <i class="bi bi-plus-circle"></i> Nuovo Prodotto
    </button>
</div>

<div class="card">
    <div class="card-header">
        <form id="productFilterForm" class="row g-3">
            <div class="col-md-4">
                <label for="filterGestore" class="form-label">Gestore</label>
                <select class="form-select" id="filterGestore" name="gestore">
                    <option value="">Tutti i gestori</option>
                    <?php foreach (getProviders() as $key => $name): ?>
                    <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="filterTipo" class="form-label">Tipo</label>
                <select class="form-select" id="filterTipo" name="tipo">
                    <option value="">Tutti i tipi</option>
                    <option value="sim">SIM</option>
                    <option value="dispositivo">Dispositivi</option>
                    <option value="ricarica">Ricariche</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="filterSearch" class="form-label">Cerca</label>
                <input type="text" class="form-control" id="filterSearch" name="search" placeholder="Nome o codice">
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Codice</th>
                        <th>Nome</th>
                        <th>Gestore</th>
                        <th>Tipo</th>
                        <th class="text-end">Prezzo</th>
                        <th class="text-center">IVA %</th>
                        <th class="text-center">Quantità</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody id="productsList">
                    <?php
                    $products = getProducts();
                    foreach ($products as $product):
                    $provider_name = getProviders()[$product['gestore']] ?? $product['gestore'];
                    ?>
                    <tr>
                        <td><?php echo $product['codice']; ?></td>
                        <td><?php echo $product['nome']; ?></td>
                        <td><?php echo $provider_name; ?></td>
                        <td>
                            <?php
                            switch ($product['tipo']) {
                                case 'sim':
                                    echo 'SIM';
                                    break;
                                case 'dispositivo':
                                    echo 'Dispositivo';
                                    break;
                                case 'ricarica':
                                    echo 'Ricarica';
                                    break;
                                default:
                                    echo $product['tipo'];
                            }
                            ?>
                        </td>
                        <td class="text-end"><?php echo number_format($product['prezzo'], 2); ?> €</td>
                        <td class="text-center"><?php echo $product['iva']; ?></td>
                        <td class="text-center"><?php echo $product['quantita']; ?></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-primary edit-product" data-id="<?php echo $product['id']; ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-product" data-id="<?php echo $product['id']; ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal per aggiungere/modificare prodotto -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Nuovo Prodotto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="productForm">
                    <input type="hidden" id="productId" name="id" value="">
                    <div class="mb-3">
                        <label for="productCodice" class="form-label">Codice</label>
                        <input type="text" class="form-control" id="productCodice" name="codice" required>
                    </div>
                    <div class="mb-3">
                        <label for="productNome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="productNome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="productGestore" class="form-label">Gestore</label>
                        <select class="form-select" id="productGestore" name="gestore" required>
                            <?php foreach (getProviders() as $key => $name): ?>
                            <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="productTipo" class="form-label">Tipo</label>
                        <select class="form-select" id="productTipo" name="tipo" required>
                            <option value="sim">SIM</option>
                            <option value="dispositivo">Dispositivo</option>
                            <option value="ricarica">Ricarica</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="productPrezzo" class="form-label">Prezzo</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="productPrezzo" name="prezzo" step="0.01" min="0" required>
                            <span class="input-group-text">€</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="productIva" class="form-label">IVA %</label>
                        <input type="number" class="form-control" id="productIva" name="iva" min="0" max="100" value="22" required>
                    </div>
                    <div class="mb-3">
                        <label for="productQuantita" class="form-label">Quantità</label>
                        <input type="number" class="form-control" id="productQuantita" name="quantita" min="0" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="productDescrizione" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="productDescrizione" name="descrizione" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" id="saveProductBtn">Salva</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal per conferma eliminazione -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProductModalLabel">Conferma eliminazione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare questo prodotto?</p>
                <p>Questa azione non può essere annullata.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Elimina</button>
            </div>
        </div>
    </div>
</div>

