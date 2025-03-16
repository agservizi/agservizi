<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Registro Transazioni</h2>
    <button type="button" class="btn btn-primary" id="exportTransactionsBtn">
        <i class="bi bi-file-earmark-excel"></i> Esporta
    </button>
</div>

<div class="card mb-4">
    <div class="card-header">
        <form id="transactionFilterForm" class="row g-3">
            <div class="col-md-3">
                <label for="filterDataDa" class="form-label">Data da</label>
                <input type="date" class="form-control" id="filterDataDa" name="data_da" value="<?php echo date('Y-m-01'); ?>">
            </div>
            <div class="col-md-3">
                <label for="filterDataA" class="form-label">Data a</label>
                <input type="date" class="form-control" id="filterDataA" name="data_a" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="col-md-4">
                <label for="filterCliente" class="form-label">Cliente</label>
                <input type="text" class="form-control" id="filterCliente" name="cliente" placeholder="Nome, cognome o telefono">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">Cerca</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>N° Scontrino</th>
                        <th class="text-end">Totale</th>
                        <th class="text-end">IVA</th>
                        <th class="text-end">Sconto</th>
                        <th>Pagamento</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody id="transactionsList">
                    <?php
                    $transactions = searchTransactions([
                        'data_da' => date('Y-m-01'),
                        'data_a' => date('Y-m-d')
                    ]);
                    foreach ($transactions as $transaction):
                    ?>
                    <tr>
                        <td><?php echo $transaction['id']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($transaction['data'])); ?></td>
                        <td>
                            <?php 
                            $cliente = trim($transaction['cliente_nome'] . ' ' . $transaction['cliente_cognome']);
                            echo $cliente ? $cliente : 'Cliente generico';
                            ?>
                        </td>
                        <td><?php echo $transaction['numero_scontrino']; ?></td>
                        <td class="text-end"><?php echo number_format($transaction['totale'], 2); ?> €</td>
                        <td class="text-end"><?php echo number_format($transaction['iva'], 2); ?> €</td>
                        <td class="text-end"><?php echo number_format($transaction['sconto'], 2); ?> €</td>
                        <td>
                            <?php
                            switch ($transaction['metodo_pagamento']) {
                                case 'contanti':
                                    echo 'Contanti';
                                    break;
                                case 'carta':
                                    echo 'Carta';
                                    break;
                                case 'bonifico':
                                    echo 'Bonifico';
                                    break;
                                default:
                                    echo $transaction['metodo_pagamento'];
                            }
                            ?>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-info view-transaction" data-id="<?php echo $transaction['id']; ?>">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary print-receipt" data-id="<?php echo $transaction['id']; ?>">
                                <i class="bi bi-printer"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal per visualizzare i dettagli della transazione -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionDetailsModalLabel">Dettagli Transazione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Informazioni Transazione</h6>
                        <p>ID: <span id="detailsId"></span></p>
                        <p>Data: <span id="detailsData"></span></p>
                        <p>Numero Scontrino: <span id="detailsNumeroScontrino"></span></p>
                        <p>Metodo Pagamento: <span id="detailsMetodoPagamento"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Informazioni Cliente</h6>
                        <p>Nome: <span id="detailsClienteNome"></span></p>
                        <p>Cognome: <span id="detailsClienteCognome"></span></p>
                        <p>Telefono: <span id="detailsClienteTelefono"></span></p>
                        <p>Email: <span id="detailsClienteEmail"></span></p>
                        <p>Codice Fiscale: <span id="detailsClienteCF"></span></p>
                    </div>
                </div>
                <h6>Prodotti</h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Nome</th>
                                <th class="text-center">Quantità</th>
                                <th class="text-end">Prezzo</th>
                                <th class="text-end">IVA</th>
                                <th class="text-end">Totale</th>
                            </tr>
                        </thead>
                        <tbody id="detailsProducts"></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">Subtotale:</th>
                                <th class="text-end" id="detailsSubtotal"></th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end">IVA:</th>
                                <th class="text-end" id="detailsIva"></th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end">Sconto:</th>
                                <th class="text-end" id="detailsSconto"></th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end">Totale:</th>
                                <th class="text-end" id="detailsTotale"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary" id="printDetailsBtn">Stampa</button>
            </div>
        </div>
    </div>
</div>

