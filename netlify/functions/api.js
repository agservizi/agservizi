const { Pool } = require('pg');
const jwt = require('jsonwebtoken');

// Configurazione del database
const pool = new Pool({
  connectionString: process.env.DATABASE_URL,
  ssl: {
    rejectUnauthorized: false
  }
});

exports.handler = async (event, context) => {
  const path = event.path.replace('/.netlify/functions/api/', '');
  const method = event.httpMethod;
  
  // Gestione autenticazione
  if (path === 'login' && method === 'POST') {
    try {
      const { email, password } = JSON.parse(event.body);
      // Implementa la logica di autenticazione
      // Esempio semplificato:
      const user = { id: 1, nome: 'Utente', cognome: 'Demo', ruolo: 'admin' };
      const token = jwt.sign(user, process.env.JWT_SECRET, { expiresIn: '8h' });
      
      return {
        statusCode: 200,
        body: JSON.stringify({ token, user })
      };
    } catch (error) {
      return { statusCode: 500, body: JSON.stringify({ error: 'Server error' }) };
    }
  }
  
  // Implementa altri endpoint necessari
  return {
    statusCode: 404,
    body: JSON.stringify({ error: 'Not Found' })
  };
};
