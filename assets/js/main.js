$(document).ready(() => {
  // Gestione click sui prodotti
  $(document).on("click", ".product-card", function () {
    const productId = $(this).data("id")
    addProductToCart(productId)
  })

  // Gestione quantità nel carrello
  $(document).on("click", ".quantity-increase", function () {
    const productId = $(this).data("id")
    updateCartItemQuantity(productId, 1)
  })

  $(document).on("click", ".quantity-decrease", function () {
    const productId = $(this).data("id")
    updateCartItemQuantity(productId, -1)
  })

  // Rimuovi prodotto dal carrello
  $(document).on("click", ".remove-item", function () {
    const productId = $(this).data("id")
    removeProductFromCart(productId)
  })

  // Svuota carrello
  $("#clearCart").click(() => {
    clearCart()
  })

  // Calcolo sconto e totale
  $("#discount").on("input", () => {
    updateCartTotals()
  })

  // Checkout
  $("#checkoutBtn").click(() => {
    processCheckout()
  })

  // Gestione prodotti
  $("#saveProductBtn").click(() => {
    saveProduct()
  })

  $(document).on("click", ".edit-product", function () {
    const productId = $(this).data("id")
    editProduct(productId)
  })

  $(document).on("click", ".delete-product", function () {
    const productId = $(this).data("id")
    $("#deleteProductModal").data("id", productId).modal("show")
  })

  $("#confirmDeleteBtn").click(() => {
    const productId = $("#deleteProductModal").data("id")
    deleteProduct(productId)
  })

  // Filtro prodotti
  $("#productFilterForm select, #productFilterForm input").change(() => {
    filterProducts()
  })

  // Filtro transazioni
  $("#transactionFilterForm").submit((e) => {
    e.preventDefault()
    filterTransactions()
  })

  // Visualizza dettagli transazione
  $(document).on("click", ".view-transaction", function () {
    const transactionId = $(this).data("id")
    viewTransactionDetails(transactionId)
  })

  // Stampa scontrino
  $(document).on("click", ".print-receipt", function () {
    const transactionId = $(this).data("id")
    printReceipt(transactionId)
  })

  // Test stampante
  $("#testPrinterBtn").click(() => {
    testPrinterConnection()
  })

  // Salva impostazioni
  $("#saveSettingsBtn").click(() => {
    saveSettings()
  })

  // Backup e ripristino
  $("#backupBtn").click(() => {
    backupDatabase()
  })

  $("#restoreBtn").click(() => {
    restoreDatabase()
  })

  // Esporta transazioni
  $("#exportTransactionsBtn").click(() => {
    exportTransactions()
  })
})

// Funzioni per la gestione del carrello
function addProductToCart(productId) {
  $.ajax({
    url: "ajax/cart.php",
    type: "POST",
    data: {
      action: "add",
      product_id: productId,
      quantity: 1,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        updateCartDisplay()
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante l'aggiunta del prodotto al carrello")
    },
  })
}

function updateCartItemQuantity(productId, change) {
  $.ajax({
    url: "ajax/cart.php",
    type: "POST",
    data: {
      action: "update",
      product_id: productId,
      change: change,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        updateCartDisplay()
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante l'aggiornamento della quantità")
    },
  })
}

function removeProductFromCart(productId) {
  $.ajax({
    url: "ajax/cart.php",
    type: "POST",
    data: {
      action: "remove",
      product_id: productId,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        updateCartDisplay()
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante la rimozione del prodotto dal carrello")
    },
  })
}

function clearCart() {
  $.ajax({
    url: "ajax/cart.php",
    type: "POST",
    data: {
      action: "clear",
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        updateCartDisplay()
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante lo svuotamento del carrello")
    },
  })
}

function updateCartDisplay() {
  $.ajax({
    url: "ajax/cart.php",
    type: "GET",
    data: {
      action: "get",
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        // Aggiorna la visualizzazione del carrello
        renderCartItems(response.cart)
        updateCartTotals()

        // Abilita/disabilita il pulsante di checkout
        if (response.cart.length > 0) {
          $("#checkoutBtn").prop("disabled", false)
        } else {
          $("#checkoutBtn").prop("disabled", true)
        }
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante l'aggiornamento del carrello")
    },
  })
}

function renderCartItems(cart) {
  let html = ""

  if (cart.length === 0) {
    html = '<tr><td colspan="6" class="text-center py-3">Il carrello è vuoto</td></tr>'
  } else {
    cart.forEach((item) => {
      html += `
            <tr>
                <td>${item.codice}</td>
                <td>${item.nome}</td>
                <td class="text-center">
                    <div class="input-group input-group-sm quantity-control">
                        <button class="btn btn-outline-secondary quantity-decrease" type="button" data-id="${item.id}">-</button>
                        <input type="text" class="form-control text-center quantity-input" value="${item.quantity}" readonly>
                        <button class="btn btn-outline-secondary quantity-increase" type="button" data-id="${item.id}">+</button>
                    </div>
                </td>
                <td class="text-end">${Number.parseFloat(item.prezzo).toFixed(2)} €</td>
                <td class="text-end">${(Number.parseFloat(item.prezzo) * Number.parseInt(item.quantity)).toFixed(2)} €</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-danger remove-item" data-id="${item.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            `
    })
  }

  $("#cartItems").html(html)
}

function updateCartTotals() {
  $.ajax({
    url: "ajax/cart.php",
    type: "GET",
    data: {
      action: "totals",
      discount: $("#discount").val() || 0,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        $("#subtotal").text(Number.parseFloat(response.totals.subtotal).toFixed(2) + " €")
        $("#iva").text(Number.parseFloat(response.totals.iva).toFixed(2) + " €")
        $("#discountAmount").text(Number.parseFloat(response.totals.discount).toFixed(2) + " €")
        $("#total").text(Number.parseFloat(response.totals.total).toFixed(2) + " €")
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante il calcolo dei totali")
    },
  })
}

function processCheckout() {
  const customerData = getCustomerFormData()
  const discount = $("#discount").val() || 0
  const paymentMethod = $("#paymentMethod").val()

  $.ajax({
    url: "ajax/checkout.php",
    type: "POST",
    data: {
      customer: customerData,
      discount: discount,
      payment_method: paymentMethod,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        // Mostra il modal di conferma
        $("#receiptNumber").text(response.receipt.number)
        $("#receiptDate").text(response.receipt.date)
        $("#receiptTotal").text(Number.parseFloat(response.receipt.total).toFixed(2) + " €")
        $("#transactionModal").modal("show")

        // Svuota il carrello
        updateCartDisplay()

        // Resetta il form cliente
        $("#customerForm")[0].reset()
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante il checkout")
    },
  })
}

function getCustomerFormData() {
  const formData = {}
  $("#customerForm")
    .serializeArray()
    .forEach((field) => {
      formData[field.name] = field.value
    })
  return formData
}

// Funzioni per la gestione dei prodotti
function saveProduct() {
  const formData = {}
  $("#productForm")
    .serializeArray()
    .forEach((field) => {
      formData[field.name] = field.value
    })

  $.ajax({
    url: "ajax/products.php",
    type: "POST",
    data: {
      action: "save",
      product: formData,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        $("#addProductModal").modal("hide")
        location.reload() // Ricarica la pagina per mostrare i prodotti aggiornati
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante il salvataggio del prodotto")
    },
  })
}

function editProduct(productId) {
  $.ajax({
    url: "ajax/products.php",
    type: "GET",
    data: {
      action: "get",
      id: productId,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        // Popola il form con i dati del prodotto
        $("#productId").val(response.product.id)
        $("#productCodice").val(response.product.codice)
        $("#productNome").val(response.product.nome)
        $("#productGestore").val(response.product.gestore)
        $("#productTipo").val(response.product.tipo)
        $("#productPrezzo").val(response.product.prezzo)
        $("#productIva").val(response.product.iva)
        $("#productQuantita").val(response.product.quantita)
        $("#productDescrizione").val(response.product.descrizione)

        // Aggiorna il titolo del modal
        $("#addProductModalLabel").text("Modifica Prodotto")

        // Mostra il modal
        $("#addProductModal").modal("show")
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante il recupero dei dati del prodotto")
    },
  })
}

function deleteProduct(productId) {
  $.ajax({
    url: "ajax/products.php",
    type: "POST",
    data: {
      action: "delete",
      id: productId,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        $("#deleteProductModal").modal("hide")
        location.reload() // Ricarica la pagina per aggiornare la lista prodotti
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante l'eliminazione del prodotto")
    },
  })
}

function filterProducts() {
  const gestore = $("#filterGestore").val()
  const tipo = $("#filterTipo").val()
  const search = $("#filterSearch").val()

  $.ajax({
    url: "ajax/products.php",
    type: "GET",
    data: {
      action: "filter",
      gestore: gestore,
      tipo: tipo,
      search: search,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        // Aggiorna la tabella dei prodotti
        renderProductsList(response.products)
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante il filtraggio dei prodotti")
    },
  })
}

function renderProductsList(products) {
  let html = ""

  if (products.length === 0) {
    html = '<tr><td colspan="8" class="text-center py-3">Nessun prodotto trovato</td></tr>'
  } else {
    products.forEach((product) => {
      let tipoText = ""
      switch (product.tipo) {
        case "sim":
          tipoText = "SIM"
          break
        case "dispositivo":
          tipoText = "Dispositivo"
          break
        case "ricarica":
          tipoText = "Ricarica"
          break
        default:
          tipoText = product.tipo
      }

      html += `
            <tr>
                <td>${product.codice}</td>
                <td>${product.nome}</td>
                <td>${product.gestore_nome}</td>
                <td>${tipoText}</td>
                <td class="text-end">${Number.parseFloat(product.prezzo).toFixed(2)} €</td>
                <td class="text-center">${product.iva}</td>
                <td class="text-center">${product.quantita}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-primary edit-product" data-id="${product.id}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-product" data-id="${product.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            `
    })
  }

  $("#productsList").html(html)
}

// Funzioni per la gestione delle transazioni
function filterTransactions() {
  const dataDa = $("#filterDataDa").val()
  const dataA = $("#filterDataA").val()
  const cliente = $("#filterCliente").val()

  $.ajax({
    url: "ajax/transactions.php",
    type: "GET",
    data: {
      action: "filter",
      data_da: dataDa,
      data_a: dataA,
      cliente: cliente,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        // Aggiorna la tabella delle transazioni
        renderTransactionsList(response.transactions)
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante il filtraggio delle transazioni")
    },
  })
}

function renderTransactionsList(transactions) {
  let html = ""

  if (transactions.length === 0) {
    html = '<tr><td colspan="9" class="text-center py-3">Nessuna transazione trovata</td></tr>'
  } else {
    transactions.forEach((transaction) => {
      let cliente = transaction.cliente_nome + " " + transaction.cliente_cognome
      cliente = cliente.trim() ? cliente : "Cliente generico"

      let metodoPagamento = ""
      switch (transaction.metodo_pagamento) {
        case "contanti":
          metodoPagamento = "Contanti"
          break
        case "carta":
          metodoPagamento = "Carta"
          break
        case "bonifico":
          metodoPagamento = "Bonifico"
          break
        default:
          metodoPagamento = transaction.metodo_pagamento
      }

      html += `
            <tr>
                <td>${transaction.id}</td>
                <td>${formatDate(transaction.data)}</td>
                <td>${cliente}</td>
                <td>${transaction.numero_scontrino}</td>
                <td class="text-end">${Number.parseFloat(transaction.totale).toFixed(2)} €</td>
                <td class="text-end">${Number.parseFloat(transaction.iva).toFixed(2)} €</td>
                <td class="text-end">${Number.parseFloat(transaction.sconto).toFixed(2)} €</td>
                <td>${metodoPagamento}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-info view-transaction" data-id="${transaction.id}">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary print-receipt" data-id="${transaction.id}">
                        <i class="bi bi-printer"></i>
                    </button>
                </td>
            </tr>
            `
    })
  }

  $("#transactionsList").html(html)
}

function formatDate(dateString) {
  const date = new Date(dateString)
  return (
    date.toLocaleDateString("it-IT") + " " + date.toLocaleTimeString("it-IT", { hour: "2-digit", minute: "2-digit" })
  )
}

function viewTransactionDetails(transactionId) {
  $.ajax({
    url: "ajax/transactions.php",
    type: "GET",
    data: {
      action: "details",
      id: transactionId,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        // Popola i dettagli della transazione
        $("#detailsId").text(response.transaction.id)
        $("#detailsData").text(formatDate(response.transaction.data))
        $("#detailsNumeroScontrino").text(response.transaction.numero_scontrino)
        $("#detailsMetodoPagamento").text(response.transaction.metodo_pagamento)

        // Popola i dati del cliente
        $("#detailsClienteNome").text(response.transaction.cliente_nome || "-")
        $("#detailsClienteCognome").text(response.transaction.cliente_cognome || "-")
        $("#detailsClienteTelefono").text(response.transaction.cliente_telefono || "-")
        $("#detailsClienteEmail").text(response.transaction.cliente_email || "-")
        $("#detailsClienteCF").text(response.transaction.cliente_cf || "-")

        // Popola i prodotti
        let productsHtml = ""
        response.details.forEach((item) => {
          productsHtml += `
                    <tr>
                        <td>${item.codice}</td>
                        <td>${item.nome}</td>
                        <td class="text-center">${item.quantita}</td>
                        <td class="text-end">${Number.parseFloat(item.prezzo).toFixed(2)} €</td>
                        <td class="text-end">${Number.parseFloat(item.iva).toFixed(2)} %</td>
                        <td class="text-end">${(Number.parseFloat(item.prezzo) * Number.parseInt(item.quantita)).toFixed(2)} €</td>
                    </tr>
                    `
        })
        $("#detailsProducts").html(productsHtml)

        // Popola i totali
        $("#detailsSubtotal").text(
          Number.parseFloat(
            response.transaction.totale - response.transaction.iva + Number.parseFloat(response.transaction.sconto),
          ).toFixed(2) + " €",
        )
        $("#detailsIva").text(Number.parseFloat(response.transaction.iva).toFixed(2) + " €")
        $("#detailsSconto").text(Number.parseFloat(response.transaction.sconto).toFixed(2) + " €")
        $("#detailsTotale").text(Number.parseFloat(response.transaction.totale).toFixed(2) + " €")

        // Mostra il modal
        $("#transactionDetailsModal").modal("show")
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante il recupero dei dettagli della transazione")
    },
  })
}

function printReceipt(transactionId) {
  $.ajax({
    url: "ajax/transactions.php",
    type: "POST",
    data: {
      action: "print",
      id: transactionId,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        alert("Scontrino inviato alla stampante")
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante la stampa dello scontrino")
    },
  })
}

// Funzioni per le impostazioni
function saveSettings() {
  const storeInfo = {}
  $("#storeInfoForm")
    .serializeArray()
    .forEach((field) => {
      storeInfo[field.name] = field.value
    })

  const printerSettings = {}
  $("#printerSettingsForm")
    .serializeArray()
    .forEach((field) => {
      printerSettings[field.name] = field.value
    })

  $.ajax({
    url: "ajax/settings.php",
    type: "POST",
    data: {
      action: "save",
      store_info: storeInfo,
      printer_settings: printerSettings,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        alert("Impostazioni salvate con successo")
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante il salvataggio delle impostazioni")
    },
  })
}

function testPrinterConnection() {
  const printerSettings = {}
  $("#printerSettingsForm")
    .serializeArray()
    .forEach((field) => {
      printerSettings[field.name] = field.value
    })

  $.ajax({
    url: "ajax/settings.php",
    type: "POST",
    data: {
      action: "test_printer",
      printer_settings: printerSettings,
    },
    dataType: "json",
    success: (response) => {
      if (response.success) {
        alert("Connessione alla stampante riuscita")
      } else {
        alert(response.message)
      }
    },
    error: () => {
      alert("Errore durante il test della connessione alla stampante")
    },
  })
}

function backupDatabase() {
  window.location.href = "ajax/backup.php?action=backup"
}

function restoreDatabase() {
  // Crea un input file nascosto
  const fileInput = $('<input type="file" accept=".sql" style="display: none;">')
  $("body").append(fileInput)

  // Simula il click sull'input file
  fileInput.click()

  // Gestisci la selezione del file
  fileInput.on("change", function () {
    const file = this.files[0]
    if (file) {
      const formData = new FormData()
      formData.append("action", "restore")
      formData.append("backup_file", file)

      $.ajax({
        url: "ajax/backup.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: (response) => {
          if (response.success) {
            alert("Database ripristinato con successo")
            location.reload()
          } else {
            alert(response.message)
          }
        },
        error: () => {
          alert("Errore durante il ripristino del database")
        },
      })
    }

    // Rimuovi l'input file
    fileInput.remove()
  })
}

function exportTransactions() {
  const dataDa = $("#filterDataDa").val()
  const dataA = $("#filterDataA").val()
  const cliente = $("#filterCliente").val()

  window.location.href = `ajax/export.php?data_da=${dataDa}&data_a=${dataA}&cliente=${cliente}`
}

