// Funzioni di utilità
function updateClock() {
    const now = new Date();
    const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' };
    document.getElementById('current-time').innerHTML = '<i class="bi bi-clock"></i> ' + now.toLocaleDateString('it-IT', options).replace(',', '');
}

// Gestione autorizzazione
function checkAuth() {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login.html';
        return false;
    }
    return true;
}

// Caricamento moduli
async function loadModule(moduleName) {
    if (!checkAuth()) return;
    
    try {
        const response = await fetch(`/.netlify/functions/api/module/${moduleName}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        if (!response.ok) throw new Error('Errore nel caricamento del modulo');
        
        const data = await response.json();
        document.getElementById('main-content').innerHTML = data.content;
        
        // Esegui eventuali script inclusi nella risposta
        if (data.scripts) {
            eval(data.scripts);
        }
    } catch (error) {
        console.error("Errore:", error);
        document.getElementById('main-content').innerHTML = `<div class="alert alert-danger">Errore nel caricamento del modulo: ${error.message}</div>`;
    }
}

// Carica la sidebar
async function loadSidebar() {
    try {
        const response = await fetch('/.netlify/functions/api/sidebar', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        if (!response.ok) throw new Error('Errore nel caricamento della sidebar');
        
        const data = await response.json();
        document.getElementById('sidebar').innerHTML = data.content;
    } catch (error) {
        console.error("Errore sidebar:", error);
    }
}

// Gestione routing
function handleRouting() {
    const hash = window.location.hash.substring(1) || '/dashboard';
    const [_, module, action] = hash.split('/');
    
    loadModule(module || 'dashboard');
}

// Gestione logout
document.getElementById('logout-btn').addEventListener('click', (e) => {
    e.preventDefault();
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = '/login.html';
});

// Carica le info utente
function loadUserInfo() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user.nome && user.cognome) {
        document.getElementById('user-info').textContent = `${user.nome} ${user.cognome} (${user.ruolo})`;
    }
}

// Inizializzazione
document.addEventListener('DOMContentLoaded', () => {
    if (!checkAuth()) return;
    
    updateClock();
    setInterval(updateClock, 60000);
    
    loadSidebar();
    loadUserInfo();
    handleRouting();
    
    window.addEventListener('hashchange', handleRouting);
});
