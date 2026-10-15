const { 
    default: makeWASocket, 
    useMultiFileAuthState, 
    DisconnectReason, 
    downloadMediaMessage,
    fetchLatestBaileysVersion
} = require('@whiskeysockets/baileys');
const qrcode = require('qrcode-terminal');
const pino = require('pino');
const axios = require('axios');
const express = require('express');
const cors = require('cors');
const fs = require('fs');
const path = require('path');

// 1. Initialize Express App for sending messages back from Laravel
const app = express();
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

const PORT = 3000;
const LARAVEL_WEBHOOK_URL = 'http://127.0.0.1:8000/api/webhook/whatsapp';
const WEBHOOK_TOKEN = 'siakad_wa_webhook_secret_key'; // Matches the token in Laravel .env

let sock = null; // WhatsApp Socket instance
let botStatus = {
    status: 'offline', // 'offline', 'connecting', 'qr', 'connected'
    qr: null,          // base64 QR data URL
    user: null         // user details
};

// Status endpoint for Laravel status check
app.get('/status', (req, res) => {
    const token = req.headers['x-webhook-token'] || req.query.token;
    if (token !== WEBHOOK_TOKEN) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    res.json(botStatus);
});

// Outgoing endpoint: Laravel will POST here to reply to students
app.post(['/message/sendText', '/message/sendText/:instance'], async (req, res) => {
    const { number, text } = req.body;
    
    if (!sock) {
        return res.status(503).json({ error: 'WhatsApp bot is not connected yet.' });
    }
    if (!number || !text) {
        return res.status(400).json({ error: 'Parameters "number" and "text" are required.' });
    }

    try {
        // Format number to JID (preserve JID if domain is already present like @lid or @s.whatsapp.net)
        let jid = number;
        if (!jid.includes('@')) {
            let formattedNumber = number.replace(/[^0-9]/g, '');
            if (formattedNumber.startsWith('0')) {
                formattedNumber = '62' + formattedNumber.substring(1);
            }
            jid = `${formattedNumber}@s.whatsapp.net`;
        }

        // Simulasikan status "mengetik" (typing status) agar terlihat alami/seperti manusia dan menghindari ban
        try {
            await sock.sendPresenceUpdate('composing', jid);
            const typingDelay = Math.min(3000, Math.max(1000, text.length * 15)); // Jeda 15ms per karakter, min 1 detik, max 3 detik
            await new Promise(resolve => setTimeout(resolve, typingDelay));
            await sock.sendPresenceUpdate('paused', jid);
        } catch (presenceErr) {
            console.warn('⚠️ Gagal mengirim status mengetik:', presenceErr.message);
        }

        console.log(`✉️ Sending message to ${jid}: ${text}`);
        await sock.sendMessage(jid, { text: text });
        
        return res.json({ success: true, message: 'Message sent successfully.' });
    } catch (error) {
        console.error('❌ Failed to send WhatsApp message:', error.message);
        return res.status(500).json({ error: error.message });
    }
});

// 2. Main WhatsApp Connection Function
async function connectToWhatsApp() {
    botStatus.status = 'connecting';
    botStatus.qr = null;
    botStatus.user = null;
    let state, saveCreds;
    try {
        console.log('🔄 Fetching latest WhatsApp Web version...');
        const { version, isLatest } = await fetchLatestBaileysVersion();
        console.log(`ℹ️ Using WhatsApp Web v${version.join('.')}, isLatest: ${isLatest}`);

        console.log('🔄 Connecting to WhatsApp...');
        
        // Auth State will be saved in "auth_info_baileys" folder.
        const authState = await useMultiFileAuthState('auth_info_baileys');
        state = authState.state;
        saveCreds = authState.saveCreds;

        sock = makeWASocket({
            version,
            auth: state,
            printQRInTerminal: false,
            logger: pino({ level: 'silent' }),
            browser: ['Windows', 'Chrome', '20.0.0'], // Bypasses the 405 Method Not Allowed error
        });
    } catch (err) {
        console.error('❌ Error initializing socket:', err.message);
        setTimeout(connectToWhatsApp, 5000);
        return;
    }



    // Save login credentials on changes
    sock.ev.on('creds.update', saveCreds);

    // Connection Events
    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;
        
        if (qr) {
            console.log('📷 Scan the QR Code below using WhatsApp on your phone (Menu > Linked Devices > Link a Device):');
            qrcode.generate(qr, { small: true });
            try {
                const QRCodeLib = require('qrcode');
                botStatus.status = 'qr';
                botStatus.qr = await QRCodeLib.toDataURL(qr);
            } catch (qrErr) {
                console.error('❌ Failed to generate QR Code Data URL:', qrErr.message);
            }
        }

        if (connection === 'close') {
            const statusCode = lastDisconnect?.error?.output?.statusCode;
            const isLoggedOut = statusCode === DisconnectReason.loggedOut || statusCode === 401;
            const shouldReconnect = !isLoggedOut;

            botStatus.status = 'offline';
            botStatus.qr = null;
            botStatus.user = null;

            console.log('❌ Connection closed due to ', lastDisconnect?.error, ', reconnecting: ', shouldReconnect);
            if (shouldReconnect) {
                botStatus.status = 'connecting';
                connectToWhatsApp();
            } else {
                console.log('🔒 Logged out. Cleaning up "auth_info_baileys" session files...');
                try {
                    fs.rmSync(path.join(__dirname, 'auth_info_baileys'), { recursive: true, force: true });
                    console.log('✅ Session cleared! Reconnecting to display QR code...');
                    setTimeout(connectToWhatsApp, 2000);
                } catch (err) {
                    console.error('❌ Failed to auto-delete auth_info_baileys:', err.message);
                }
            }
        } else if (connection === 'open') {
            console.log('✅ SUCCESS: WhatsApp Bot is connected and active!');
            botStatus.status = 'connected';
            botStatus.qr = null;
            botStatus.user = sock.user || null;
        }
    });

    // Inbound Messages Event
    sock.ev.on('messages.upsert', async (m) => {
        if (m.type !== 'notify') return;

        for (const msg of m.messages) {
            // Ignore messages sent by the bot itself
            if (msg.key.fromMe) continue;

            const sender = msg.key.remoteJid;

            // Ignore group messages (JIDs ending with @g.us)
            if (sender && sender.endsWith('@g.us')) continue;

            const messageType = Object.keys(msg.message || {})[0];
            
            // We only process image messages (payment proof screenshots)
            const isImage = messageType === 'imageMessage';
            const text = msg.message?.conversation || 
                         msg.message?.extendedTextMessage?.text || 
                         msg.message?.imageMessage?.caption || '';

            if (isImage) {
                console.log(`📸 Received image message from ${sender}. Processing proof...`);

                try {
                    // Download image buffer directly using Baileys helper
                    const buffer = await downloadMediaMessage(msg, 'buffer', {}, { 
                        logger: pino({ level: 'silent' }) 
                    });

                    if (buffer) {
                        const base64Image = buffer.toString('base64');
                        
                        console.log(`📤 Sending proof webhook to SIAKAD Laravel...`);
                        
                        // Send POST to Laravel endpoint
                        const response = await axios.post(`${LARAVEL_WEBHOOK_URL}?token=${WEBHOOK_TOKEN}`, {
                            sender: sender,
                            message: text,
                            base64: base64Image
                        }, {
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Webhook-Token': WEBHOOK_TOKEN
                            },
                            timeout: 45000 // 45 seconds timeout (allow Gemini API time to execute)
                        });

                        console.log('✅ Response from Laravel:', response.data);
                    } else {
                        console.error('❌ Failed to download message media.');
                    }
                } catch (error) {
                    console.error('❌ Error processing incoming webhook:', error.response?.data || error.message);
                }
            } else if (text) {
                console.log(`💬 Chat from ${sender}: ${text}`);

                const lowerText = text.toLowerCase();
                const queryKeywords = ['cek tagihan', 'sisa tagihan', 'cek saldo', 'daftar tagihan', 'tagihan saya', 'sisa ukt', 'sisa spp', 'info tagihan'];
                const isQuery = queryKeywords.some(keyword => lowerText.includes(keyword)) || lowerText === 'tagihan' || lowerText === 'saldo';

                if (isQuery) {
                    console.log(`📤 Sending billing query webhook to SIAKAD Laravel for ${sender}...`);
                    try {
                        await axios.post(`${LARAVEL_WEBHOOK_URL}?token=${WEBHOOK_TOKEN}`, {
                            sender: sender,
                            message: text,
                            is_query: true
                        }, {
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Webhook-Token': WEBHOOK_TOKEN
                            },
                            timeout: 20000
                        });
                    } catch (error) {
                        console.error('❌ Error processing query webhook:', error.response?.data || error.message);
                    }
                    return;
                }

                // Check if the text message looks like a payment query/confirmation
                const paymentKeywords = [
                    'bayar', 'transfer', 'tf', 'bukti', 'lunas', 'konfirmasi', 
                    'spp', 'ukt', 'nominal', 'rekening', 'nim', 'pembayaran'
                ];

                const isPaymentRelated = paymentKeywords.some(keyword => lowerText.includes(keyword));

                if (isPaymentRelated) {
                    const replyText = `Halo! 😊\n\nUntuk melakukan konfirmasi pembayaran secara otomatis, silakan kirimkan *foto/screenshot struk bukti transfer* Anda dan sertakan NIM Anda pada caption/pesan gambar tersebut.\n\nSistem AI kami akan memproses struk tersebut dan mendaftarkannya ke antrean verifikasi secara otomatis. Terima kasih!`;

                    try {
                        console.log(`✉️ Sending guidance reply to ${sender}`);
                        await sock.sendMessage(sender, { text: replyText });
                    } catch (err) {
                        console.error('❌ Failed to send guidance message:', err.message);
                    }
                }
            }
        }
    });
}

// Start Express Server
app.listen(PORT, () => {
    console.log(`🚀 API Server running locally on http://localhost:${PORT}`);
});

// Start WhatsApp socket connection
connectToWhatsApp();
