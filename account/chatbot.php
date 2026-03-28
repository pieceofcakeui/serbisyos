<?php
require_once '../functions/auth.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serbisyos Chatbot</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/chatbot.css">
    <style>
        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
        }
        .history-group-title {
            padding: 18px 15px 0 5px;
            pointer-events: none;
        }
        .chat-history ul .history-group-title .history-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            margin: 0;
        }
        .chat-history ul li {
            list-style-type: none;
        }
    </style>
</head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="chat-layout-container" id="chatLayoutContainer">
            <div class="chat-sidebar">
                <div class="sidebar-top">
                    <div class="sidebar-header">
                        <div class="sidebar-header-main">
                            <img src="../assets/img/chatbot/chatbot.png" alt="Logo" class="sidebar-logo">
                            <span class="sidebar-title">Serbisyos</span>
                        </div>
                        <button class="sidebar-close-btn" id="sidebarCloseBtn">&times;</button>
                    </div>
                    <button class="new-chat-btn" id="newChatBtn">
                        <i class="fas fa-plus"></i> New Chat
                    </button>
                </div>
                <div class="chat-history">
                    <ul id="chatHistoryList">
                    </ul>
                </div>
                <div class="sidebar-footer">
                    <div class="chatbot-powered">
                        <small>Powered by Serbisyos & Google Gemini</small>
                    </div>
                </div>
            </div>
            <div class="chat-main-content">
                <div class="chatbot-window" id="chatbotWindow">
                    <div class="chatbot-header">
    <button class="chat-sidebar-toggle" id="chatSidebarToggle">
        <i class="bi bi-layout-text-window-reverse" style="font-size: 15px;"></i> 
    </button>
    <a href="home" style="margin-left: auto; background: none; border: none; color: black; font-weight: 200; font-size: 15px; cursor: pointer; text-decoration: none;">
        back
    </a>
</div>

                    <div class="chatbot-messages" id="chatbotMessages">
                    </div>
                    <div class="chatbot-footer">
                        <div class="image-preview-container" id="imagePreviewContainer">
                            <img id="imagePreview" src="" alt="Image Preview">
                            <button class="remove-image-btn" id="removeImageBtn">&times;</button>
                        </div>
                        <div class="chatbot-input-container">
                            <input type="text" id="chatbotInput" placeholder="Ask about car problems...">
                            <input type="file" id="imageInput" accept="image/jpeg, image/jpg, image/png, image/webp" style="display: none;">
                            <button id="chatbotUpload"><i class="fas fa-image"></i></button>
                            <button id="chatbotSend"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    
    <?php include 'include/emergency-modal.php'; ?>

    <div class="modal fade" id="deleteChatModal" tabindex="-1" aria-labelledby="deleteChatModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteChatModalLabel">Confirm Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete this chat history? This action cannot be undone.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
          </div>
        </div>
      </div>
    </div>

      <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chatbotMessages = document.getElementById('chatbotMessages');
            const chatbotInput = document.getElementById('chatbotInput');
            const chatbotSend = document.getElementById('chatbotSend');
            const chatbotUpload = document.getElementById('chatbotUpload');
            const imageInput = document.getElementById('imageInput');
            const newChatBtn = document.getElementById('newChatBtn');
            const chatHistoryList = document.getElementById('chatHistoryList');
            const chatSidebarToggle = document.getElementById('chatSidebarToggle');
            const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
            const chatLayoutContainer = document.getElementById('chatLayoutContainer');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const imagePreview = document.getElementById('imagePreview');
            const removeImageBtn = document.getElementById('removeImageBtn');
            const API_URL = 'backend/gemini-proxy.php';
            const FETCH_SHOPS_URL = 'backend/fetch_shops.php';
            const HISTORY_API_URL = 'backend/chat_history.php';

            const deleteModalElement = document.getElementById('deleteChatModal');
            const deleteModal = new bootstrap.Modal(deleteModalElement);
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            let chatIdToDelete = null;


            let waitingForLocation = false;
            let userPreferences = { language: 'en', location: null };
            let selectedImageBase64 = null;
            let selectedImageFileName = null;
            let conversationHistory = [];
            let currentChatId = null;

            const initialSystemPrompt = [{ role: "user", parts: [{ text: `You are "Serv," a professional automotive assistant from Serbisyos, a directory platform for auto repair shops in the Philippines. You must follow these strict guidelines: \n\n1. SCOPE: Only discuss automotive repair, maintenance, car problems, and Serbisyos services. If an image is uploaded, identify what is in the image in the context of a vehicle. Refuse all non-automotive topics politely by stating you can only assist with car-related issues. \n\n2. LANGUAGE: Your response MUST match the user's language. If the user speaks English, respond in English. If they speak Tagalog, respond in Tagalog. If they use Taglish (a mix of Tagalog and English), you MUST also respond in Taglish. This is a strict rule. \n\n3. PERSONALITY: Professional, polite, helpful, and knowledgeable. Use a formal but friendly tone. ALWAYS include relevant emojis in your responses to make them more engaging and friendly. \n\n4. SHOP RECOMMENDATIONS: NEVER recommend shops outside of the Serbisyos network. Only use Serbisyos partner shops. \n\n5. BOOKING/EMERGENCY: You can only explain processes; you cannot actually book appointments or provide emergency services directly. \n\n6. RESPONSES: Keep responses concise, helpful, and actionable. Always offer next steps. Include appropriate emojis to make responses more engaging. \n\n7. REDIRECTS: For off-topic questions, politely redirect to automotive topics.` }] }, { role: "model", parts: [{ text: "Hello! 👋 I'm Serv, your professional automotive assistant from Serbisyos. 🚗✨ I'm here to help you with car repair needs, find partner shops, and provide automotive guidance. You can also upload an image of a car part for identification. 📸 How may I assist you today? 😊" }] }];
            const faqs = { 'q1': { question: "How do I search for auto repair shops in my area?", answer: "🔍 Use our search feature at the top of the homepage. Enter your location (barangay, city, or address) and optionally specify the type of service you need. You can filter results by rating, or services offered. Click \"Apply Filter\" to see a list of nearby auto repair shops with their address and ratings. 📍⭐" }, 'q2': { question: "How do reviews and ratings work?", answer: "⭐ Customers can leave ratings (1-5 stars) and detailed reviews after creating an account and verifying their visit. Our system averages these ratings and displays them prominently. We have strict anti-fraud measures to ensure authentic reviews. 🛡️ You can sort shops by rating and read detailed customer experiences. 📖" }, 'q3': { question: "Can I book appointments through Serbisyos?", answer: "📅 Many shops offer direct booking through our platform. Look for the \"Book Appointment\" button on their profile. For shops without this feature, we provide direct contact information so you can schedule service directly. 📞 Some shops also offer real-time availability calendars. ⏰" }, 'q4': { question: "How can I add my auto repair shop to the directory?", answer: "🏪 If you want to list your auto repair shop, you can apply through <a href=\"become-a-partner.php\" style=\"color: #1a73e8; text-decoration: none;\">Become a Partner</a>. It's free to apply as long as you complete all requirements correctly. ✅ You will wait 1-3 business days for approval. If approved, you can receive an email or notification through the Serbisyos platform and manage your shop profile to enable advance booking or emergency assistance services if you offer them. 🚀" }, 'q5': { question: "What is Serbisyos?", answer: "🏢 Serbisyos connects vehicle owners with trusted local auto repair services through a user-friendly platform featuring detailed directories, reviews, and service info—making it easy to find reliable help when it's needed most. 🤝 Currently, we focus on Iloilo Province in the Philippines and do not accept listings outside of this area. 🌏" } };
            const responses = { en: { welcome: "Hello! 👋 I'm Serv, your automotive assistant from Serbisyos. 🚗✨\n\nI can help you with:\n📍 Finding nearby repair shops\n🔧 Car repair tips\n🔍 Basic diagnosis guidance\n📸 Identifying car parts from an image\n❓ Frequently Asked Questions\n\nHow can I assist you today? 😊", offTopic: "🚗 I'm here to help with automotive repair concerns. Let's talk about your vehicle needs - how can I assist you? 😊", needLocation: "📍 To find the best repair shops for you, please share your location (city, municipality, or province). For example: 'Iloilo City' or 'Iloilo' 🗺️", noShopsFound: "😅 I couldn't find any partner shops in that area. Could you try a nearby city or check the spelling of your location? 🔍", carTips: "💡 I'd be happy to share automotive maintenance tips! What specific area interests you? Engine care 🔧, tire maintenance 🛞, battery care 🔋, or general upkeep? 🚗", diagnosis: "🔍 I can provide basic diagnostic guidance, but professional inspection is always recommended. What symptoms is your vehicle showing? 🚗💭" }, tl: { welcome: "Kumusta! 👋 Ako si Serv, inyong automotive assistant mula sa Serbisyos. 🚗✨\n\nMaaari kong tulungan kayo sa:\n📍 Paghanap ng malapit na repair shops\n🔧 Car repair tips\n🔍 Basic diagnosis guidance\n📸 Pagkilala ng piyesa ng sasakyan mula sa larawan\n❓ Mga Madalas Itanong (FAQs)\n\nPaano ko kayo matutulungan ngayon? 😊", offTopic: "🚗 Nandito ako para tumulong sa automotive repair concerns. Usapan natin ang inyong vehicle needs - paano ko kayo matutulungan? 😊", needLocation: "📍 Para makahanap ng pinakamahusay na repair shops para sa inyo, mangyaring ibahagi ang inyong location (siyudad, munisipalidad, o probinsya). Halimbawa: 'Iloilo City' o 'Iloilo' 🗺️", noShopsFound: "😅 Hindi ko nahanap ang partner shops sa lugar na iyan. Pwede bang subukan ang malapit na siyudad o tingnan ang spelling ng location? 🔍", carTips: "💡 Masaya akong magbahagi ng automotive maintenance tips! Anong specific area ang gusto ninyo? Engine care 🔧, tire maintenance 🛞, battery care 🔋, o general upkeep? 🚗", diagnosis: "🔍 Makapagbibigay ako ng basic diagnostic guidance, pero professional inspection ay palaging recommended. Anong symptoms ang pinapakita ng inyong vehicle? 🚗💭" } };

            function createQuickButtons(language = 'en') {
                const buttons = language === 'tl' ? { findShop: '📍 Maghanap ng Shop', tips: '💡 Repair Tips', diagnosis: '🔍 Car Diagnosis', faqs: '🤔 FAQs' } : { findShop: '📍 Find a Shop', tips: '💡 Repair Tips', diagnosis: '🔍 Car Diagnosis', faqs: '🤔 FAQs' };
                return `<div class="quick-actions-container"><button onclick="handleQuickAction('findShop', this)" class="quick-action-button">${buttons.findShop}</button><button onclick="handleQuickAction('tips', this)" class="quick-action-button">${buttons.tips}</button><button onclick="handleQuickAction('diagnosis', this)" class="quick-action-button">${buttons.diagnosis}</button><button onclick="handleQuickAction('faqs', this)" class="quick-action-button">${buttons.faqs}</button></div>`;
            }

            window.handleQuickAction = function (action, buttonElement) {
                switch (action) {
                    case 'findShop': handleSendMessage(userPreferences.language === 'tl' ? 'Maghanap ng repair shop' : 'Find a repair shop'); break;
                    case 'tips': handleRepairTips(); break;
                    case 'diagnosis': handleCarDiagnosis(); break;
                    case 'faqs': handleSendMessage('FAQs'); break;
                }
            };

          async function handleRepairTips() {
    const userMessage = userPreferences.language === 'tl' ? 'Bigyan mo ako ng car repair tips' : 'Give me car repair tips';
    const botMessage = userPreferences.language === 'tl' ? '💡 Sige! Anong uri ng tips ang gusto mong malaman? Halimbawa: `Gulong` 🛞, `Preno` 🛑, `Baterya` 🔋, o `Engine` 🔧?' : '💡 Of course! What kind of tips are you interested in? For example: `Tires` 🛞, `Brakes` 🛑, `Battery` 🔋, or `Engine` 🔧?';
    
    setUiLoading(true);
    addMessage('user', userMessage);
    
    try {
        await saveUserMessage(userMessage, null, currentChatId === null);
        showTypingIndicator();
        
        setTimeout(async () => {
            removeTypingIndicator();
            addMessage('bot', botMessage);
            await saveBotMessage(botMessage);
            setUiLoading(false);
        }, 1200);
    } catch (error) {
        console.error('Error in handleRepairTips:', error);
        removeTypingIndicator();
        addMessage('bot', '😔 There was an error. Please try again.');
        setUiLoading(false);
    }
}
           async function handleCarDiagnosis() {
    const userMessage = userPreferences.language === 'tl' ? 'Tulungan mo ako sa car diagnosis' : 'Help me with car diagnosis';
    const botMessage = userPreferences.language === 'tl' ? '🔍 Sige po. Pakilarawan ang problema ng iyong sasakyan. Halimbawa, "May maingay na tunog kapag pumepreno." 🔊 Pwede ka ring mag-upload ng litrato. 📸' : '🔍 I can help with that. Please describe the problem your vehicle is experiencing. For example, "My car is making a loud squealing noise when I brake." 🔊 You can also upload a photo of the issue. 📸';
    
    setUiLoading(true);
    addMessage('user', userMessage);
    
    try {
        await saveUserMessage(userMessage, null, currentChatId === null);
        showTypingIndicator();
        
        setTimeout(async () => {
            removeTypingIndicator();
            addMessage('bot', botMessage);
            await saveBotMessage(botMessage);
            setUiLoading(false);
        }, 1200);
    } catch (error) {
        console.error('Error in handleCarDiagnosis:', error);
        removeTypingIndicator();
        addMessage('bot', '😔 There was an error. Please try again.');
        setUiLoading(false);
    }
}

            window.handleFAQClick = async function (questionKey) {
                const question = faqs[questionKey].question;
                const answer = faqs[questionKey].answer;
                addMessage('user', question);
                setUiLoading(true);
                await saveUserMessage(question, currentChatId === null);
                showTypingIndicator();
                setTimeout(async () => {
                    removeTypingIndicator();
                    let finalAnswerForDisplay = answer;
                    const lang = userPreferences.language;
                    finalAnswerForDisplay += '\n\n';
                    if (lang === 'tl') {
                        finalAnswerForDisplay += '🔗 Para sa iba pang mga tanong, bisitahin ang aming <a href="need-help.php" style="color: #1a73e8; text-decoration: none;">Help & Support</a> page.';
                    } else {
                        finalAnswerForDisplay += '🔗 For additional questions, visit our <a href="need-help.php" style="color: #1a73e8; text-decoration: none;">Help & Support</a> page.';
                    }
                    addMessage('bot', finalAnswerForDisplay, true, true);
                    await saveBotMessage(answer);
                    setUiLoading(false);
                }, 1500);
            };

            function showTypingIndicator() {
                if (document.querySelector('.typing-indicator')) return;
                const typingIndicator = document.createElement('div');
                typingIndicator.classList.add('chatbot-message', 'bot', 'typing-indicator');
                typingIndicator.innerHTML = `<div class="message-content"><span></span><span></span><span></span></div>`;
                chatbotMessages.appendChild(typingIndicator);
                scrollToBottom();
            }

            function removeTypingIndicator() {
                const typingIndicator = document.querySelector('.typing-indicator');
                if (typingIndicator) {
                    typingIndicator.remove();
                }
            }

            function detectLanguage(message) {
                const tagalogWords = ['ako', 'ikaw', 'tayo', 'kami', 'kayo', 'sila', 'ang', 'ng', 'sa', 'ay', 'mga', 'na', 'hanap', 'gusto', 'kailangan', 'problema', 'sakit', 'sira', 'ayos', 'repair', 'shop', 'malapit', 'kumusta', 'salamat', 'paano', 'saan', 'ano', 'sino', 'kailan', 'bakit', 'magkano'];
                const lowerMessage = message.toLowerCase();
                const tagalogCount = tagalogWords.filter(word => lowerMessage.includes(word)).length;
                if (tagalogCount >= 2) {
                    userPreferences.language = 'tl';
                } else if (tagalogCount === 0) {
                    userPreferences.language = 'en';
                }
            }

          async function handleSendMessage(message) {
    const imageToSend = selectedImageBase64;
    
    if (!message.trim() && !imageToSend) return;
    
    if (chatbotSend.disabled) {
        addMessage('bot', '⏳ Please wait for my previous response...');
        return;
    }
    
    addMessage('user', message, false, false, imageToSend);
    detectLanguage(message);
    chatbotInput.value = '';
    
    setUiLoading(true);
    clearImagePreview();
    
    try {
        // PALITAN ANG LINE NA ITO:
        await saveUserMessage(message, imageToSend, currentChatId === null);
        await routeMessage(message);
    } catch (error) {
        console.error("Error in message send process:", error);
        removeTypingIndicator();
        addMessage('bot', '😔 An error occurred while sending. Please try again.');
        setUiLoading(false);
    }
}

           async function saveUserMessage(message, imageBase64Data, isNewChat) {
    try {
        const userMessageData = { 
            action: 'add_message', 
            chat_id: currentChatId, 
            role: 'user', 
            message: message, 
            image_data: imageBase64Data, 
            original_filename: selectedImageFileName 
        };
        
        const historyEntry = { 
            role: 'user', 
            parts: [{ text: message }] 
        };

        if (imageBase64Data) {
            let cleanImageData = imageBase64Data;
            if (cleanImageData.startsWith('data:image/')) {
                cleanImageData = cleanImageData.split(',')[1];
            }
            historyEntry.parts.push({ 
                inlineData: { 
                    mimeType: 'image/jpeg', 
                    data: cleanImageData 
                } 
            });
        }
        
        conversationHistory.push(historyEntry);

        const saveUserResponse = await fetch(HISTORY_API_URL, { 
            method: 'POST', 
            headers: { 
                'Content-Type': 'application/json', 
                'Accept': 'application/json' 
            }, 
            body: JSON.stringify(userMessageData) 
        });

        if (!saveUserResponse.ok) { 
            throw new Error(`Backend Error: ${saveUserResponse.status}`); 
        }
        
        const saveUserData = await saveUserResponse.json();
        
        if (saveUserData.error) { 
            throw new Error(`Backend Logic Error: ${saveUserData.error}`); 
        }
        
        if (saveUserData.chat_id) { 
            currentChatId = saveUserData.chat_id; 
        }
        
        if (isNewChat && currentChatId) { 
            await fetchAndDisplayHistory(); 
        }
    } catch (error) {
        console.error("Error saving user message:", error);
        addMessage('bot', `😔 I'm having trouble saving our conversation. Please try again.`);
        conversationHistory.pop();
        throw error;
    }
}
            async function routeMessage(message) {
                if (waitingForLocation) { await handleLocationResponse(message); return; }
                if (isShopFindingIntent(message)) { await handleShopFinding(message); return; }
                if (message.toLowerCase().includes('faq')) { await displayFAQs(); return; }
                await getGeminiResponse();
            }

            function isShopFindingIntent(message) {
                const keywords = ['shop', 'repair', 'mechanic', 'garage', 'service', 'find', 'recommend', 'near', 'around', 'hanap', 'mekaniko', 'saan', 'malapit', 'talyer'];
                return keywords.some(keyword => message.toLowerCase().includes(keyword));
            }

            function isNonIloiloLocationQuery(message) {
                const nonIloiloLocations = ['manila', 'cebu', 'davao', 'bacolod', 'antique', 'capiz', 'aklan', 'guimaras', 'makati', 'quezon city', 'pasig', 'taguig', 'cavite', 'laguna', 'batangas', 'rizal', 'pampanga', 'bulacan', 'negros'];
                const lowerCaseMessage = message.toLowerCase();
                return nonIloiloLocations.some(loc => lowerCaseMessage.includes(loc));
            }

            function extractLocation(message) {
                const locations = ['cabatuan', 'iloilo city', 'iloilo', 'passi', 'ajuy', 'alimodian', 'anilao', 'badiangan', 'balasan', 'banate', 'barotac nuevo', 'barotac viejo', 'batad', 'bingawan', 'calinog', 'carles', 'concepcion', 'dingle', 'dueñas', 'dumangas', 'estancia', 'guimbal', 'igbaras', 'janiuay', 'lambunao', 'leganes', 'lemery', 'leon', 'maasin', 'miagao', 'mina', 'new lucena', 'oton', 'pavia', 'pototan', 'san dionisio', 'san enrique', 'san joaquin', 'san miguel', 'san rafael', 'santa barbara', 'sara', 'tigbauan', 'tubungan', 'zarraga'];
                const lowerCaseMessage = message.toLowerCase();
                for (const loc of locations) {
                    const regex = new RegExp(`\\b${loc}\\b`);
                    if (regex.test(lowerCaseMessage)) { return loc; }
                }
                const patterns = [/(?:in|sa|from)\s+([a-zA-Z\s]+?)(?:,|\s+iloilo|\s*$)/i, /([a-zA-Z\s]+?)(?:,|\s+)\s*iloilo/i, /([a-zA-Z\s]+)/i];
                for (const pattern of patterns) {
                    const match = lowerCaseMessage.match(pattern);
                    if (match && match[1]) {
                        const extractedLocation = match[1].trim().toLowerCase();
                        for (const loc of locations) {
                            if (extractedLocation === loc || extractedLocation.includes(loc) || loc.includes(extractedLocation)) { return loc; }
                        }
                    }
                }
                return null;
            }

          async function handleShopFinding(message) {
    const iloiloLocation = extractLocation(message);
    
    if (iloiloLocation) {
        waitingForLocation = false;
        await handleLocationResponse(iloiloLocation);
    } else if (isNonIloiloLocationQuery(message)) {
        waitingForLocation = false;
        const msg = "😔 I apologize, but Serbisyos currently operates exclusively within Iloilo Province. We don't have partner shops there at the moment, but we hope to expand in the future! 🌟";
        removeTypingIndicator();
        addMessage('bot', msg);
        await saveBotMessage(msg);
        setUiLoading(false);
    } else {
        waitingForLocation = true;
        const lang = userPreferences.language;
        const msg = responses[lang].needLocation;
        removeTypingIndicator();
        addMessage('bot', msg);
        await saveBotMessage(msg);
        setUiLoading(false);
    }
}

          async function handleLocationResponse(message) {
    waitingForLocation = false;
    showTypingIndicator();
    try {
        const location = message.trim();
        userPreferences.location = location;
        const response = await fetch(FETCH_SHOPS_URL, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ location: location, searchType: 'both' }) });
        const data = await response.json();
        if (data.success && data.data && data.data.length > 0) {
            removeTypingIndicator();
            await displayShops(data.data, location);
        } else {
            removeTypingIndicator();
            const lang = userPreferences.language;
            const msg = responses[lang].noShopsFound;
            addMessage('bot', msg);
            await saveBotMessage(msg);
        }
    } catch (error) {
        console.error('Error fetching shops:', error);
        removeTypingIndicator();
        addMessage('bot', "😔 Error finding shops. Please try again.");
    } finally {
        setUiLoading(false);
    }
}

           async function displayShops(shops, location) {
    const lang = userPreferences.language;
    const header = lang === 'tl' ?
        `🏪 Narito ang mga partner repair shops sa ${location}:` :
        `🏪 Here are partner repair shops in ${location}:`;
    
    let shopsHtml = `<strong style="display: block; margin-bottom: 15px;">${header}</strong>`;
    let textForHistory = `${header}\n`;

    shops.forEach(shop => {
        const rating = shop.rating ? `⭐ ${shop.rating}` : '⭐ No ratings yet';
        const bookingStatus = shop.show_book_now ? '✅ Yes' : '❌ No';
        const emergencyStatus = shop.show_emergency ? '🚨 Yes' : '❌ No';
        const shopLink = `/account/shop/${shop.shop_slug}`;

        shopsHtml += `<a href="${shopLink}" target="_blank" style="display: flex; align-items: center; border: 1px solid #e0e0e0; border-radius: 8px; padding: 12px; margin-bottom: 12px; background: #f9f9f9; text-decoration: none; color: inherit; transition: box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'"><img src="${shop.logo_url}" alt="${shop.shop_name} Logo" style="width: 60px; height: 60px; border-radius: 8px; margin-right: 15px; object-fit: cover;" onerror="this.onerror=null;this.src='uploads/shop_logo/logo.jpg';"><div style="flex-grow: 1;"><div style="font-weight: bold; color: #1a73e8; font-size: 1.1em;">🔧 ${shop.shop_name}</div><small style="display: block; margin: 2px 0; color: #555;">📍 ${shop.full_address}</small><small style="display: block; margin: 2px 0; color: #555;">${rating}</small><small style="display: block; margin-top: 5px;"><span style="margin-right: 15px;">📅 Online Booking: <strong>${bookingStatus}</strong></span><span>🚨 Emergency Service: <strong>${emergencyStatus}</strong></span></small></div></a>`;
        
        textForHistory += `\n- ${shop.shop_name} at ${shop.full_address} (${rating}). Online Booking: ${bookingStatus.replace('✅ ', '').replace('❌ ', '')}, Emergency Service: ${emergencyStatus.replace('🚨 ', '').replace('❌ ', '')}.`;
    });

    const footer = lang === 'tl' ?
        '<br>💡 Pwede kayong mag-click sa kahit anong shop para sa more details at booking. May iba pa akong matutulungan sa inyo? 😊' :
        '<br>💡 You can click on any shop for more details and booking options. Is there anything else I can help you with? 😊';

    addMessage('bot', shopsHtml + footer, true, true, null, 'shops');
    await saveBotMessage(`__SHOPS_DISPLAY__${JSON.stringify({ shops, location, lang })}`);

    setUiLoading(false);
}
            async function displayFAQs() {
                showTypingIndicator();
                const header = '❓ Here are some frequently asked questions:';
                let faqHtml = `<strong style="display: block; margin-bottom: 15px;">${header}</strong><div class="quick-actions-container" style="display: grid; gap: 10px;">`;
                for (const key in faqs) {
                    faqHtml += `<button onclick="handleFAQClick('${key}')" class="quick-action-button faq-question" style="text-align: left; padding: 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='#f8f9fa'">${faqs[key].question}</button>`;
                }
                faqHtml += '</div>';
                removeTypingIndicator();
                addMessage('bot', faqHtml, true, true, null, 'faq');
                await saveBotMessage('__FAQ_DISPLAY__');
            }

          async function getGeminiResponse() {
    showTypingIndicator();

    try {
        const cleanedHistory = conversationHistory.map(msg => {
            if (msg.parts && Array.isArray(msg.parts)) {
                const cleanedParts = msg.parts.map(part => {
                    if (part.text) {
                        return { text: part.text };
                    }
                    if (part.inlineData && part.inlineData.data && part.inlineData.mimeType) {
                        return {
                            inlineData: {
                                mimeType: part.inlineData.mimeType,
                                data: part.inlineData.data
                            }
                        };
                    }
                    return part;
                });
                return { role: msg.role, parts: cleanedParts };
            }
            return msg;
        });

        console.log('Sending request to API...'); // Debug log

        const response = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                contents: cleanedHistory,
                generationConfig: {
                    temperature: 0.7,
                    maxOutputTokens: 1000,
                    topP: 0.8,
                    topK: 40
                }
            })
        });

        console.log('Response status:', response.status); // Debug log

        if (!response.ok) {
            const errorText = await response.text();
            console.error(`API Error ${response.status}:`, errorText);
            
            let errorData;
            try {
                errorData = JSON.parse(errorText);
            } catch {
                throw new Error(`API Error: ${response.status}`);
            }
            
            throw new Error(errorData.error?.message || `API Error: ${response.status}`);
        }

        const data = await response.json();
        console.log('API Response:', data);

        if (data.error) {
            console.error('Gemini API Error:', data.error);
            throw new Error(data.error.message || JSON.stringify(data.error));
        }

        const botResponseText =
            data.candidates?.[0]?.content?.parts?.[0]?.text ||
            "🤔 I'm not sure how to respond to that. Could you rephrase?";

        removeTypingIndicator();
        addMessage('bot', botResponseText);
        await saveBotMessage(botResponseText);
        
        setUiLoading(false);
        conversationHistory.push({ 
            role: 'model', 
            parts: [{ text: botResponseText }] 
        });

        return 'success';
    } catch (error) {
        console.error('Error in getGeminiResponse:', error);
        removeTypingIndicator();

        let errorMessage = "😔 I'm having a technical issue right now. Please try again.";

        if (error.message.includes('API key')) {
            errorMessage = "🔑 API configuration error. Please contact support.";
        } else if (error.message.includes('quota')) {
            errorMessage = "📊 API quota exceeded. Please try again later.";
        } else if (error.message.includes('429')) {
            errorMessage = "⏳ Too many requests. Please wait 10-20 seconds.";
        } else if (error.message.includes('400')) {
            errorMessage = "📸 Request error. Try a simpler message or different image.";
        } else if (error.message.includes('500')) {
            errorMessage = "🛠️ Server error. Please try again in a few moments.";
        }

        console.error('Detailed error:', error.message);
        
        addMessage('bot', errorMessage);
        setUiLoading(false);
        
        return 'error_generic';
    }
}


            async function saveBotMessage(messageText) {
                if (!currentChatId) return;
                try {
                    conversationHistory.push({ role: 'model', parts: [{ text: messageText }] });
                    const botMessageData = { action: 'add_message', chat_id: currentChatId, role: 'bot', message: messageText };
                    const response = await fetch(HISTORY_API_URL, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(botMessageData) });
                    if (!response.ok) { throw new Error(`Backend Error saving bot message: ${response.status}`); }
                } catch (error) { console.error('Error saving bot message:', error); }
            }

            async function fetchAndDisplayHistory() {
                try {
                    const response = await fetch(HISTORY_API_URL + '?action=get_titles', { method: 'GET', headers: { 'Accept': 'application/json' } });
                    if (!response.ok) throw new Error(`Error fetching history: ${response.status}`);
                    const chats = await response.json();
                    chatHistoryList.innerHTML = '';

                    const pinnedChats = chats.filter(chat => chat.is_pinned);
                    const unpinnedChats = chats.filter(chat => !chat.is_pinned);

                    if (chats.length === 0) {
                        chatHistoryList.innerHTML = '<li><span style="color: #999; padding: 12px 15px; display: block;">No conversations yet</span></li>';
                        return;
                    }

                    if (pinnedChats.length > 0) {
                        const titleLi = document.createElement('li');
                        titleLi.classList.add('history-group-title');
                        titleLi.innerHTML = `<p class="history-title">Pinned</p>`;
                        chatHistoryList.appendChild(titleLi);

                        pinnedChats.forEach(chat => {
                            const li = document.createElement('li');
                            li.classList.add('history-item');

                            const pinIcon = 'bi-pin-angle-fill';
                            const pinText = 'Unpin';

                            li.innerHTML = `
                                <div class="history-item-container">
                                    <a href="#" class="history-link" data-chat-id="${chat.id}">${chat.title || 'New Chat'}</a>
                                    <div class="history-item-controls">
                                        <i class="bi bi-pin-angle-fill pinned-icon"></i>
                                        <div class="dropdown">
                                            <button class="history-menu-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-dark">
                                                <li><a class="dropdown-item pin-chat-btn" href="#" data-chat-id="${chat.id}"><i class="bi ${pinIcon} me-2"></i>${pinText}</a></li>
                                                <li><a class="dropdown-item delete-chat-btn" href="#" data-chat-id="${chat.id}"><i class="bi bi-trash me-2"></i>Delete</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>`;
                            chatHistoryList.appendChild(li);
                        });
                    }

                    const groups = { "Today": [], "Yesterday": [], "Last 7 Days": [], "Last 30 Days": [], "Older": [] };
                    const now = new Date();
                    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

                    unpinnedChats.forEach(chat => {
                        const chatDate = new Date(chat.updated_at);
                        const chatDay = new Date(chatDate.getFullYear(), chatDate.getMonth(), chatDate.getDate());
                        const diffTime = today - chatDay;
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        if (diffDays === 0) { groups["Today"].push(chat); }
                        else if (diffDays === 1) { groups["Yesterday"].push(chat); }
                        else if (diffDays <= 7) { groups["Last 7 Days"].push(chat); }
                        else if (diffDays <= 30) { groups["Last 30 Days"].push(chat); }
                        else { groups["Older"].push(chat); }
                    });

                    const groupOrder = ["Today", "Yesterday", "Last 7 Days", "Last 30 Days", "Older"];
                    let hasUnpinnedContent = false;
                    groupOrder.forEach(groupName => {
                        if (groups[groupName].length > 0) {
                            hasUnpinnedContent = true;
                            const titleLi = document.createElement('li');
                            titleLi.classList.add('history-group-title');
                            if (pinnedChats.length > 0) {
                                titleLi.style.marginTop = '15px';
                            }
                            titleLi.innerHTML = `<p class="history-title">${groupName}</p>`;
                            chatHistoryList.appendChild(titleLi);

                            groups[groupName].forEach(chat => {
                                const li = document.createElement('li');
                                li.classList.add('history-item');

                                const pinIcon = 'bi-pin-angle';
                                const pinText = 'Pin';

                                li.innerHTML = `
                                    <div class="history-item-container">
                                        <a href="#" class="history-link" data-chat-id="${chat.id}">${chat.title || 'New Chat'}</a>
                                        <div class="history-item-controls">
                                            <div class="dropdown">
                                                <button class="history-menu-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-dark">
                                                    <li><a class="dropdown-item pin-chat-btn" href="#" data-chat-id="${chat.id}"><i class="bi ${pinIcon} me-2"></i>${pinText}</a></li>
                                                    <li><a class="dropdown-item delete-chat-btn" href="#" data-chat-id="${chat.id}"><i class="bi bi-trash me-2"></i>Delete</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>`;
                                chatHistoryList.appendChild(li);
                            });
                        }
                    });

                    if (pinnedChats.length === 0 && !hasUnpinnedContent) {
                        chatHistoryList.innerHTML = '<li><span style="color: #999; padding: 12px 15px; display: block;">No conversations yet</span></li>';
                    }

                } catch (error) {
                    console.error('Error fetching chat history:', error);
                    chatHistoryList.innerHTML = '<li><span style="color: #ff6b6b; padding: 12px 15px; display: block;">Error loading history</span></li>';
                }
            }

            async function loadChatHistory(chatId) {
                showTypingIndicator();
                try {
                    const response = await fetch(`${HISTORY_API_URL}?action=get_conversation&chat_id=${chatId}`, { method: 'GET', headers: { 'Accept': 'application/json' } });
                    if (!response.ok) throw new Error(`Error loading chat: ${response.status}`);
                    const data = await response.json();
                    if (data.error) throw new Error(`Backend Error on load: ${data.error}`);
                    currentChatId = parseInt(chatId);
                    conversationHistory = [...initialSystemPrompt];
                    chatbotMessages.innerHTML = '';

                    document.querySelectorAll('.history-item').forEach(link => { link.classList.remove('active'); });
                    const activeLink = document.querySelector(`[data-chat-id="${chatId}"]`);
                    if (activeLink) {
                        activeLink.closest('.history-item').classList.add('active');
                    }

                    data.forEach(msg => {
                        if (msg.role && msg.parts && msg.parts.length > 0) {
                            const textPart = msg.parts.find(p => p.text);
                            const imagePart = msg.parts.find(p => p.inlineData);
                            conversationHistory.push(msg);

                            if (textPart && textPart.text) {
                                if (textPart.text === '__FAQ_DISPLAY__') {
                                    const header = '❓ Here are some frequently asked questions:';
                                    let faqHtml = `<strong style="display: block; margin-bottom: 15px;">${header}</strong><div class="quick-actions-container" style="display: grid; gap: 10px;">`;
                                    for (const key in faqs) { faqHtml += `<button onclick="handleFAQClick('${key}')" class="quick-action-button faq-question" style="text-align: left; padding: 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='#f8f9fa'">${faqs[key].question}</button>`; }
                                    faqHtml += '</div>';
                                    addMessage('bot', faqHtml, true, true, null, 'faq');
                                } else if (textPart.text.startsWith('__SHOPS_DISPLAY__')) {
                                    try {
                                        const shopDataStr = textPart.text.replace('__SHOPS_DISPLAY__', '');
                                        const shopData = JSON.parse(shopDataStr);
                                        const header = shopData.lang === 'tl' ? `🏪 Narito ang mga partner repair shops sa ${shopData.location}:` : `🏪 Here are partner repair shops in ${shopData.location}:`;
                                        let shopsHtml = `<strong style="display: block; margin-bottom: 15px;">${header}</strong>`;
                                        shopData.shops.forEach(shop => {
                                            const rating = shop.rating ? `⭐ ${shop.rating}` : '⭐ No ratings yet';
                                            const bookingStatus = shop.show_book_now ? '✅ Yes' : '❌ No';
                                            const emergencyStatus = shop.show_emergency ? '🚨 Yes' : '❌ No';
                                            const shopLink = `/account/shop/${shop.shop_slug}`;
                                            shopsHtml += `<a href="${shopLink}" target="_blank" style="display: flex; align-items: center; border: 1px solid #e0e0e0; border-radius: 8px; padding: 12px; margin-bottom: 12px; background: #f9f9f9; text-decoration: none; color: inherit; transition: box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'"><img src="${shop.logo_url}" alt="${shop.shop_name} Logo" style="width: 60px; height: 60px; border-radius: 8px; margin-right: 15px; object-fit: cover;" onerror="this.onerror=null;this.src='uploads/shop_logo/logo.jpg';"><div style="flex-grow: 1;"><div style="font-weight: bold; color: #1a73e8; font-size: 1.1em;">🔧 ${shop.shop_name}</div><small style="display: block; margin: 2px 0; color: #555;">📍 ${shop.full_address}</small><small style="display: block; margin: 2px 0; color: #555;">${rating}</small><small style="display: block; margin-top: 5px;"><span style="margin-right: 15px;">📅 Online Booking: <strong>${bookingStatus}</strong></span><span>🚨 Emergency Service: <strong>${emergencyStatus}</strong></span></small></div></a>`;
                                        });
                                        const footer = shopData.lang === 'tl' ? '<br>💡 Pwede kayong mag-click sa kahit anong shop para sa more details at booking. May iba pa ba akong matutulungan sa inyo? 😊' : '<br>💡 You can click on any shop for more details and booking options. Is there anything else I can help you with? 😊';
                                        addMessage('bot', shopsHtml + footer, true, true, null, 'shops');
                                    } catch (parseError) {
                                        console.error('Error parsing shop data:', parseError);
                                        addMessage('bot', '🔧 Shop information was here but couldn\'t be displayed.');
                                    }
                                } else {
                                    addMessage(msg.role === 'model' ? 'bot' : 'user', textPart.text, false, false, imagePart?.inlineData?.data);
                                }
                            }
                        }
                    });
                    await moveActiveChat(chatId);
                    if (window.innerWidth <= 768) { chatLayoutContainer.classList.remove('sidebar-open'); }
                } catch (error) {
                    console.error('Error loading chat history:', error);
                    addMessage('bot', '😔 There was an error loading this conversation. Please try again.');
                } finally {
                    removeTypingIndicator();
                }
            }

            async function moveActiveChat(chatId) {
                try {
                    await fetch(HISTORY_API_URL, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ action: 'update_chat_timestamp', chat_id: chatId }) });
                    await fetchAndDisplayHistory();
                    document.querySelectorAll('.history-item').forEach(link => { link.classList.remove('active'); });
                    const activeLink = document.querySelector(`[data-chat-id="${chatId}"]`);
                    if (activeLink) {
                        activeLink.closest('.history-item').classList.add('active');
                    }
                } catch (error) { console.error('Error updating chat timestamp:', error); }
            }

            function startNewChat() {
                chatbotMessages.innerHTML = '';
                conversationHistory = [...initialSystemPrompt];
                currentChatId = null;
                waitingForLocation = false;
                clearImagePreview();
                document.querySelectorAll('.history-item').forEach(link => { link.classList.remove('active'); });
                addInitialWelcomeMessage();
                fetchAndDisplayHistory();
            }

            function showDeleteConfirmationModal(chatId) {
                chatIdToDelete = chatId;
                deleteModal.show();
            }

            confirmDeleteBtn.addEventListener('click', async () => {
                if (chatIdToDelete) {
                    deleteModal.hide();
                    await deleteChat(chatIdToDelete);
                    chatIdToDelete = null;
                }
            });

            async function deleteChat(chatId) {
                try {
                    const response = await fetch(HISTORY_API_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ action: 'delete_chat', chat_id: chatId })
                    });

                    if (!response.ok) {
                        throw new Error(`Server error: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        if (currentChatId === parseInt(chatId)) {
                            startNewChat();
                        } else {
                            fetchAndDisplayHistory();
                        }
                    } else {
                        throw new Error(result.error || 'Failed to delete chat.');
                    }
                } catch (error) {
                    console.error('Error deleting chat:', error);
                    alert('Error deleting chat: ' + error.message);
                }
            }

            async function togglePinChat(chatId) {
                try {
                    const response = await fetch(HISTORY_API_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ action: 'pin_chat', chat_id: chatId })
                    });

                    if (!response.ok) {
                        throw new Error(`Server error: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        await fetchAndDisplayHistory();

                        if (currentChatId) {
                            document.querySelectorAll('.history-item').forEach(link => { link.classList.remove('active'); });
                            const activeLink = document.querySelector(`[data-chat-id="${currentChatId}"]`);
                            if (activeLink) {
                                activeLink.closest('.history-item').classList.add('active');
                            }
                        }
                    } else {
                        throw new Error(result.error || 'Failed to pin chat.');
                    }
                } catch (error) {
                    console.error('Error pinning chat:', error);
                    alert('Error pinning chat: ' + error.message);
                }
            }


            function addInitialWelcomeMessage() {
                showTypingIndicator();
                setTimeout(() => {
                    removeTypingIndicator();
                    const lang = userPreferences.language;
                    const welcomeMessage = responses[lang].welcome;
                    const buttons = createQuickButtons(lang);
                    addMessage('bot', welcomeMessage.replace(/\n/g, '<br>') + buttons, true, false);
                }, 1500);
            }

            function addMessage(sender, message, isHtml = false, isFullWidth = false, imageBase64 = null, messageType = null) {
                const messageElement = document.createElement('div');
                messageElement.classList.add('chatbot-message', sender);
                if (isFullWidth) { messageElement.classList.add('full-width'); }
                const contentDiv = document.createElement('div');
                contentDiv.classList.add('message-content');
                let contentHtml = '';
                if (messageType === 'faq' || message === '__FAQ_DISPLAY__') {
                    const header = '❓ Here are some frequently asked questions:';
                    contentHtml = `<strong style="display: block; margin-bottom: 15px;">${header}</strong><div class="quick-actions-container" style="display: grid; gap: 10px;">`;
                    for (const key in faqs) { contentHtml += `<button onclick="handleFAQClick('${key}')" class="quick-action-button faq-question" style="text-align: left; padding: 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='#f8f9fa'">${faqs[key].question}</button>`; }
                    contentHtml += '</div>';
                } else if (messageType === 'shops' || message.startsWith('__SHOPS_DISPLAY__')) {
                    if (message.startsWith('__SHOPS_DISPLAY__')) { contentHtml = '🔧 Shop information displayed above.'; } else { contentHtml = message; }
                } else {
                    if (isHtml) { contentHtml = message; } else { contentHtml = message.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>'); }
                }
                if (imageBase64) {
                    let imageDataUri = imageBase64.startsWith('data:') ? imageBase64 : `data:image/jpeg;base64,${imageBase64}`;
                    contentHtml += `<br><img src="${imageDataUri}" class="uploaded-image" alt="Uploaded image" style="max-width: 200px; max-height: 200px; border-radius: 10px; margin-top: 10px;" onerror="console.error('Failed to load image:', this.src);">`;
                }
                contentDiv.innerHTML = contentHtml;
                messageElement.appendChild(contentDiv);
                chatbotMessages.appendChild(messageElement);
                scrollToBottom();
            }

           function setUiLoading(isLoading) {
    chatbotInput.disabled = isLoading;
    chatbotSend.disabled = isLoading;
    chatbotUpload.disabled = isLoading;
    
    if (isLoading) {
        chatbotInput.placeholder = "Please wait...";
    } else {
        chatbotInput.placeholder = "Ask about car problems...";
        chatbotInput.focus();
    }
}


            function scrollToBottom() { chatbotMessages.scrollTop = chatbotMessages.scrollHeight; }
            function showImagePreview(imageBase64) {
                selectedImageBase64 = imageBase64;
                imagePreview.src = `data:image/jpeg;base64,${selectedImageBase64}`;
                imagePreviewContainer.style.display = 'block';
            }
            function clearImagePreview() {
                selectedImageBase64 = null;
                selectedImageFileName = null;
                imagePreview.src = '';
                imagePreviewContainer.style.display = 'none';
                imageInput.value = '';
            }

            chatbotUpload.addEventListener('click', () => imageInput.click());
            removeImageBtn.addEventListener('click', clearImagePreview);
            imageInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const maxSize = 4 * 1024 * 1024;
                    if (file.size > maxSize) { addMessage('bot', '📸 The image is too large. Please upload an image smaller than 4MB.'); return; }
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) { addMessage('bot', '📸 Please upload a valid image file (JPEG, PNG, GIF, or WebP).'); return; }
                    selectedImageFileName = file.name;
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        try { const base64String = event.target.result.split(',')[1]; showImagePreview(base64String); }
                        catch (error) { console.error('Error reading image:', error); addMessage('bot', '📸 There was an error reading your image. Please try again.'); }
                    };
                    reader.onerror = () => { addMessage('bot', '📸 There was an error reading your image. Please try again.'); };
                    reader.readAsDataURL(file);
                }
            });

            chatSidebarToggle.addEventListener('click', (e) => { e.stopPropagation(); chatLayoutContainer.classList.toggle('sidebar-open'); });
            sidebarCloseBtn.addEventListener('click', () => { chatLayoutContainer.classList.remove('sidebar-open'); });
            newChatBtn.addEventListener('click', startNewChat);

            chatHistoryList.addEventListener('click', (e) => {
                const link = e.target.closest('.history-link');
                const deleteBtn = e.target.closest('.delete-chat-btn');
                const pinBtn = e.target.closest('.pin-chat-btn');

                if (deleteBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    const chatId = deleteBtn.dataset.chatId;
                    showDeleteConfirmationModal(chatId);
                    return;
                }

                if (pinBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    const chatId = pinBtn.dataset.chatId;
                    togglePinChat(chatId);
                    return;
                }

                if (link) {
                    e.preventDefault();
                    const chatId = link.dataset.chatId;
                    if (currentChatId !== parseInt(chatId)) {
                        loadChatHistory(chatId);
                    } else if (window.innerWidth <= 768) {
                        chatLayoutContainer.classList.remove('sidebar-open');
                    }
                }
            });

            chatbotSend.addEventListener('click', () => handleSendMessage(chatbotInput.value));
            chatbotInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') handleSendMessage(chatbotInput.value); });

            startNewChat();
        });
    </script>

</body>
</html>