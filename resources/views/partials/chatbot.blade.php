<button id="chat-toggle-btn" type="button" onclick="toggleChat()">
    Tanya <strong>EIMI</strong>
</button>

<div id="chat-widget">
    <div class="chat-header">
        <span>Asisten Konsultasi EIMI</span>
        <button type="button" onclick="toggleChat()" style="background:none; border:none; color:#000; cursor:pointer; font-size:18px;">&times;</button>
    </div>
    
    <div class="chat-body" id="chat-messages">
        <div class="message bot">
            Halo! Kenalin aku <strong>EIMI</strong>, asisten virtual HealthSeek!<br><br>
            Ada keluhan kesehatan? Ceritakan saja di sini, aku bakal bantu carikan Rumah Sakit di Sumatera Utara yang sesuai untuk kamu!
        </div>
    </div>

    <div class="chat-footer">
        <input type="text" id="chat-input" placeholder="Ketik keluhan (misal: sakit dada)..." onkeypress="handleEnter(event)">
        <button type="button" onclick="sendMessage()"><strong>Kirim</strong></button>
    </div>
</div>

<style>
    #chat-toggle-btn {
        position: fixed; bottom: 30px; right: 30px;
        background-color: #ffc107; color: #000;
        border: none; padding: 15px 30px; border-radius: 10px;
        font-size: 16px; font-weight: bold; cursor: pointer;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        z-index: 9999; transition: transform 0.3s;
    }
    #chat-toggle-btn:hover { transform: scale(1.05);}

    #chat-widget {
        position: fixed; bottom: 100px; right: 30px;
        width: 350px; height: 450px;
        background: white; border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        display: none; 
        flex-direction: column; overflow: hidden; z-index: 9999;
        font-family: sans-serif;
    }

    .chat-header { background: #ffc107; color: #000; padding: 15px; display:flex; justify-content:space-between; align-items:center; font-weight:bold;}

    .chat-body { flex: 1; padding: 15px; overflow-y: auto; background: #f8f9fa; display: flex; flex-direction: column; gap: 10px; }

    .chat-footer { padding: 10px; border-top: 1px solid #eee; display: flex; gap: 5px; background: white; }
    .chat-footer input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 20px; outline: none; }
    .chat-footer button { background: #ffc107; color: #000; border: none; padding: 0 15px; border-radius: 20px; cursor: pointer; }

    .message { max-width: 85%; padding: 10px 14px; border-radius: 15px; font-size: 14px; line-height: 1.4; word-wrap: break-word; }
    .message.bot { background: #e9ecef; color: #333; align-self: flex-start; border-bottom-left-radius: 2px; }
    .message.user { background: #5c5c5c; color: white; align-self: flex-end; border-bottom-right-radius: 2px; }
    
    .chat-rs-link { display: block; background: white; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ddd; text-decoration: none; color: #333; transition:0.2s; }
    .chat-rs-link:hover { background: #f1f1f1; border-color: #007bff; }
    .chat-rs-link strong { color: #4c8547ff; }
</style>

<script>
    function toggleChat() {
        const widget = document.getElementById('chat-widget');
        widget.style.display = (widget.style.display === 'none' || widget.style.display === '') ? 'flex' : 'none';
        if(widget.style.display === 'flex') document.getElementById('chat-input').focus();
    }

    function handleEnter(e) {
        if (e.key === 'Enter') sendMessage();
    }

    function sendMessage() {
        const input = document.getElementById('chat-input');
        const messages = document.getElementById('chat-messages');
        const text = input.value.trim();

        if (text === "") return;

        messages.innerHTML += `<div class="message user">${text}</div>`;
        input.value = '';
        messages.scrollTop = messages.scrollHeight;

        const loadingId = 'loading-' + Date.now();
        messages.innerHTML += `<div class="message bot" id="${loadingId}">...</div>`;
        messages.scrollTop = messages.scrollHeight;

        // 3. Kirim ke Server
        fetch("{{ route('api.chat') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({ message: text })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById(loadingId).remove();

            let replyHtml = data.reply;

            if (data.recommendations && data.recommendations.length > 0) {
                replyHtml += `<div style="margin-top:10px;">`;
                data.recommendations.forEach(rs => {
                    let url = "{{ route('rumahSakit.detail', ['id' => ':id']) }}";
                    url = url.replace(':id', rs.id.value);
                    
                    replyHtml += `
                        <a href="${url}" target="_blank" class="chat-rs-link">
                            <strong>${rs.nama.value}</strong><br>
                            <span style="font-size:11px; color:#666;">Tipe ${rs.tipe.value}</span>
                        </a>
                    `;
                });
                replyHtml += `</div>`;
            }

            messages.innerHTML += `<div class="message bot">${replyHtml}</div>`;
            messages.scrollTop = messages.scrollHeight;
        })
        .catch(error => {
            console.error(error);
            document.getElementById(loadingId).innerHTML = "Maaf, ada gangguan koneksi.";
        });
    }
</script>