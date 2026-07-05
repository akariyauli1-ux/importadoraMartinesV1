const wppconnect = require('@wppconnect-team/wppconnect');
const mysql = require('mysql2/promise');

// MySQL Connection Configuration
const dbConfig = {
  host: '127.0.0.1',
  user: 'root',
  password: '',
  database: 'importadora_martinez',
  port: 3306
};

let whatsappClient = null;

// Formats Bolivian/global phone numbers to WhatsApp JID format
function formatPhoneNumber(phone) {
  // Strip all non-numeric characters
  let cleanNumber = phone.replace(/\D/g, '');
  
  // If Bolivian number (8 digits starting with 6 or 7 usually), prepend Bolivia country code (591)
  if (cleanNumber.length === 8 && (cleanNumber.startsWith('6') || cleanNumber.startsWith('7'))) {
    cleanNumber = '591' + cleanNumber;
  }
  
  // Append JID suffix
  return `${cleanNumber}@c.us`;
}

async function startBot() {
  console.log('Iniciando sesión en WhatsApp...');
  
  try {
    whatsappClient = await wppconnect.create({
      session: 'taller-martinez',
      catchQR: (base64Qr, asciiQR, attempts, urlCode) => {
        console.log('\n--- ESCANEE EL SIGUIENTE CÓDIGO QR CON SU WHATSAPP ---');
        console.log(asciiQR); // Render QR in console terminal
        console.log('------------------------------------------------------\n');
      },
      statusFind: (statusSession, session) => {
        console.log('Estado de la sesión:', statusSession);
      },
      headless: true, // Run headless browser in background
      devtools: false,
      useChrome: true,
      debug: false,
      logQR: false,
      autoClose: 0, // Disable auto close
    });

    console.log('¡WhatsApp conectado con éxito y listo para enviar mensajes!');
    
    // Start polling the database
    setInterval(pollDatabase, 5000); // Poll every 5 seconds

  } catch (error) {
    console.error('Error al inicializar el Bot de WhatsApp:', error);
    process.exit(1);
  }
}

async function pollDatabase() {
  let connection;
  try {
    connection = await mysql.createConnection(dbConfig);
    
    // Find unsent messages
    const [rows] = await connection.execute(
      'SELECT id, telefono, mensaje FROM mensajes_whatsapp_pendientes WHERE enviado = 0 LIMIT 5'
    );
    
    if (rows.length === 0) {
      return; // Nothing to send
    }

    console.log(`Encontrados ${rows.length} mensajes pendientes por enviar...`);

    for (const msg of rows) {
      const recipient = formatPhoneNumber(msg.telefono);
      console.log(`Enviando mensaje a ${msg.telefono} (JID: ${recipient})...`);
      
      try {
        await whatsappClient.sendText(recipient, msg.mensaje);
        console.log(`✓ Mensaje enviado con éxito a ${msg.telefono}`);
        
        // Update database status
        await connection.execute(
          'UPDATE mensajes_whatsapp_pendientes SET enviado = 1, enviado_at = NOW() WHERE id = ?',
          [msg.id]
        );
      } catch (sendError) {
        console.error(`✗ Falló el envío del mensaje ID ${msg.id} a ${msg.telefono}:`, sendError);
      }
    }

  } catch (dbError) {
    console.error('Error de base de datos durante el polling:', dbError);
  } finally {
    if (connection) {
      await connection.end();
    }
  }
}

startBot();
