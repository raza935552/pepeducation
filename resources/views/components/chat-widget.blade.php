{{-- Professor Peptides live chat widget — self-contained (no Tailwind/app.css
     or csrf-meta dependency) so it works on the main site AND standalone
     landers. Needs Alpine; the main layout already loads it, landers include
     it via partials.chat-embed. Polling-based, no websockets. --}}
<div x-data="ppChatWidget()" x-init="boot()" class="pp-cw" x-cloak>

    <div x-show="showTeaser && !open" x-transition.scale.origin.bottom.right class="cw-teaser" @click="toggle()">
        <button type="button" @click.stop="dismissTeaser()" class="cw-teaser-x" aria-label="Dismiss">&times;</button>
        <span>👋 Questions about peptides? <b>Ask me</b></span>
    </div>

    <button type="button" @click="toggle()" x-show="!open" x-transition.scale.origin.bottom.right
            class="cw-bubble" :class="attn ? 'cw-attn' : ''" aria-label="Chat with us">
        <svg x-show="!unread" width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l1.3-3.9A7.96 7.96 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <span x-show="unread" x-text="unread" class="cw-bubble-badge"></span>
        <span x-show="attn && !unread" class="cw-attn-dot"></span>
        <span class="cw-bubble-tip">Need help? Chat with us 👋</span>
    </button>

    <div x-show="open" x-transition.scale.origin.bottom.right class="cw-panel">
        <div class="cw-header">
            <div class="cw-h-left">
                <div class="cw-avatar">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 4a3 3 0 110 6 3 3 0 010-6zm0 14a8 8 0 01-6.3-3.1c.03-2 4.2-3.1 6.3-3.1s6.27 1.1 6.3 3.1A8 8 0 0112 20z"/></svg>
                </div>
                <div>
                    <p class="cw-title">Professor Peptides</p>
                    <p class="cw-sub"><span class="cw-dot" :class="online ? 'cw-dot-on' : 'cw-dot-off'"></span><span x-text="online ? 'Online — usually replies in minutes' : 'Ask me anything — research & education'"></span></p>
                </div>
            </div>
            <button type="button" @click="toggle()" class="cw-close" aria-label="Close">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>

        <div x-show="!started" class="cw-prechat">
            <div class="cw-pc-head">
                <div class="cw-prechat-icon">
                    <svg width="28" height="28" fill="none" stroke="#fff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l1.3-3.9A7.96 7.96 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="cw-pc-title">Hi there 👋</h3>
                <p class="cw-pc-sub">Ask about any peptide, our calculators &amp; guides, or where to buy.</p>
            </div>
            <div class="cw-pc-form">
                <input type="text" x-model="name" placeholder="Your name (optional)" class="cw-input">
                <input type="email" x-model="email" placeholder="Email *" class="cw-input" @keydown.enter="begin()">
                <p x-show="error" x-text="error" class="cw-err"></p>
                <button type="button" @click="begin()" :disabled="busy" class="cw-btn-primary"><span x-show="!busy">Start chat</span><span x-show="busy">…</span></button>
                <p class="cw-note">We'll only use your email to reply to you.</p>
            </div>
        </div>

        <div x-show="started" x-ref="scroll" class="cw-body">
            <template x-for="m in messages" :key="m.id">
                <div class="cw-row" :class="m.sender === 'visitor' ? 'cw-row-out' : 'cw-row-in'">
                    <div class="cw-msg" :class="m.sender === 'visitor' ? 'cw-out' : 'cw-in'">
                        <p class="cw-msg-body" x-html="format(m.body)"></p>
                        <span class="cw-time" x-text="(m.sender !== 'visitor' && m.author_name ? m.author_name + ' · ' : '') + m.time"></span>
                    </div>
                </div>
            </template>
            <div x-show="started && messages.length <= 1" x-transition class="cw-chips">
                <button type="button" class="cw-chip" @click="suggest('What are the most popular peptides?')">🧪 Popular peptides</button>
                <button type="button" class="cw-chip" @click="suggest('Show me your calculators')">🧮 Calculators</button>
                <button type="button" class="cw-chip" @click="suggest('Where can I buy peptides?')">🛒 Where to buy</button>
            </div>
            <div x-show="botTyping" class="cw-row cw-row-in">
                <div class="cw-msg cw-in cw-typing"><span>Assistant is typing</span><span class="cw-dots"><i></i><i></i><i></i></span></div>
            </div>
        </div>

        <div x-show="started && rating === null && messages.length >= 3" class="cw-csat">
            <span>Was this helpful?</span>
            <button type="button" @click="rate(1)" class="cw-csat-btn" aria-label="Helpful">👍</button>
            <button type="button" @click="rate(0)" class="cw-csat-btn" aria-label="Not helpful">👎</button>
        </div>
        <div x-show="rated" x-transition class="cw-csat cw-csat-thanks">Thanks for your feedback! 🙏</div>

        <div x-show="started && !humanRequested" class="cw-human-bar">
            <button type="button" @click="requestHuman()" class="cw-human-btn">🙋 Talk to a human</button>
        </div>

        <div x-show="started" class="cw-footer">
            <input type="text" x-model="input" @keydown.enter="sendMsg()" placeholder="Type a message…" class="cw-msg-input">
            <button type="button" @click="sendMsg()" :disabled="!input.trim()" class="cw-send" aria-label="Send">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
            </button>
        </div>
        <div class="cw-brand">Professor Peptides · educational, research use only</div>
    </div>
</div>

@once
<style>
.pp-cw [x-cloak], .pp-cw[x-cloak]{display:none !important;}
.pp-cw{font-family:Figtree,ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;}
.pp-cw button{cursor:pointer;border:0;background:none;font-family:inherit;}
.cw-bubble{position:fixed;bottom:24px;right:24px;width:60px;height:60px;border-radius:9999px;display:flex;align-items:center;justify-content:center;color:#fff;background:#2563eb;box-shadow:0 10px 30px rgba(0,0,0,.25);z-index:2147483000;transition:transform .2s,box-shadow .2s;animation:cw-bob 3.2s ease-in-out infinite;}
.cw-bubble:hover{transform:scale(1.06);animation:none;}
/* Always-on pulse ring so the launcher keeps "popping" */
.cw-bubble::before{content:'';position:absolute;inset:0;border-radius:9999px;background:#2563eb;z-index:-1;animation:cw-pulse 2.2s ease-out infinite;}
.cw-attn{animation:cw-wiggle 2.6s ease-in-out infinite;}
@keyframes cw-pulse{0%{transform:scale(1);opacity:.45;}100%{transform:scale(1.9);opacity:0;}}
@keyframes cw-bob{0%,92%,100%{transform:translateY(0);}96%{transform:translateY(-5px);}}
@keyframes cw-wiggle{0%,88%,100%{transform:rotate(0);}90%{transform:rotate(-12deg);}93%{transform:rotate(10deg);}96%{transform:rotate(-6deg);}}
.cw-attn:hover{animation:none;}
.cw-bubble-tip{position:absolute;right:72px;top:50%;transform:translateY(-50%);white-space:nowrap;background:#0f172a;color:#fff;font-size:13px;font-weight:600;padding:8px 12px;border-radius:10px;opacity:1;pointer-events:none;transition:opacity .15s,transform .15s;box-shadow:0 6px 18px rgba(0,0,0,.22);animation:cw-tip-in .4s ease both;}
.cw-bubble-tip::after{content:'';position:absolute;right:-5px;top:50%;transform:translateY(-50%);border:5px solid transparent;border-left-color:#0f172a;}
@keyframes cw-tip-in{from{opacity:0;transform:translateY(-50%) translateX(8px);}to{opacity:1;transform:translateY(-50%) translateX(0);}}
@media(max-width:480px){.cw-bubble-tip{display:none;}}
.cw-attn-dot{position:absolute;top:-2px;right:-2px;width:14px;height:14px;border-radius:9999px;background:#22c55e;border:2px solid #fff;}
.cw-bubble-badge{position:absolute;top:-2px;right:-2px;min-width:20px;height:20px;padding:0 5px;border-radius:9999px;background:#ef4444;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;border:2px solid #fff;}
.cw-teaser{position:fixed;bottom:96px;right:24px;max-width:235px;background:#fff;color:#1f2937;font-size:13.5px;line-height:1.4;padding:12px 30px 12px 14px;border-radius:16px;border-bottom-right-radius:4px;box-shadow:0 12px 30px rgba(0,0,0,.18);cursor:pointer;z-index:2147483000;}
.cw-teaser b{color:#2563eb;}
.cw-teaser-x{position:absolute;top:6px;right:8px;font-size:16px;line-height:1;color:#9ca3af;}
.cw-panel{position:fixed;bottom:24px;right:24px;width:380px;max-width:calc(100vw - 32px);height:600px;max-height:calc(100vh - 48px);background:#fff;border-radius:24px;box-shadow:0 24px 60px rgba(0,0,0,.28);display:flex;flex-direction:column;overflow:hidden;z-index:2147483000;}
.cw-header{display:flex;align-items:center;justify-content:space-between;padding:16px;background:#2563eb;color:#fff;}
.cw-h-left{display:flex;align-items:center;gap:12px;}
.cw-title{margin:0;font-weight:600;color:#fff;font-size:15px;line-height:1.2;}
.cw-sub{margin:2px 0 0;display:flex;align-items:center;gap:6px;font-size:12px;color:rgba(255,255,255,.85);line-height:1.2;}
.cw-close{color:rgba(255,255,255,.8);}
.cw-close:hover{color:#fff;}
.cw-avatar{width:40px;height:40px;border-radius:9999px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;color:#fff;}
.cw-dot{width:8px;height:8px;border-radius:9999px;display:inline-block;}
.cw-dot-on{background:#34d399;box-shadow:0 0 0 3px rgba(52,211,153,.3);}
.cw-dot-off{background:#fbbf24;}
.cw-prechat{flex:1;padding:24px;overflow-y:auto;display:flex;flex-direction:column;justify-content:center;}
.cw-pc-head{text-align:center;margin-bottom:20px;}
.cw-prechat-icon{width:56px;height:56px;border-radius:9999px;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;background:#2563eb;}
.cw-pc-title{margin:0;font-weight:700;font-size:18px;color:#0f172a;}
.cw-pc-sub{margin:4px 0 0;font-size:14px;color:#6b7280;}
.cw-pc-form > * + *{margin-top:12px;}
.cw-input{width:100%;box-sizing:border-box;padding:11px 14px;border:1px solid #e5e7eb;border-radius:12px;font-size:14px;outline:none;}
.cw-input:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.12);}
.cw-err{margin:0;font-size:12px;color:#ef4444;}
.cw-note{margin:0;font-size:11px;color:#9ca3af;text-align:center;}
.cw-btn-primary{width:100%;padding:12px;border-radius:12px;font-weight:600;font-size:14px;color:#fff;background:#2563eb;}
.cw-btn-primary:disabled{opacity:.6;}
.cw-body{flex:1;padding:16px;overflow-y:auto;background:#f5f7fb;display:flex;flex-direction:column;gap:8px;}
.cw-row{display:flex;}
.cw-row-out{justify-content:flex-end;}
.cw-row-in{justify-content:flex-start;}
.cw-msg{max-width:80%;padding:9px 13px;border-radius:16px;font-size:14px;line-height:1.45;box-shadow:0 1px 2px rgba(0,0,0,.06);}
.cw-msg-body{margin:0;word-break:break-word;}
.cw-out{background:#2563eb;color:#fff;border-bottom-right-radius:4px;}
.cw-in{background:#fff;color:#1f2937;border:1px solid #eef0f2;border-bottom-left-radius:4px;}
.cw-time{display:block;font-size:10px;opacity:.6;margin-top:3px;text-align:right;}
.cw-typing{display:inline-flex;align-items:center;gap:8px;color:#6b7280;font-size:12.5px;}
.cw-dots{display:inline-flex;gap:3px;}
.cw-dots i{width:6px;height:6px;border-radius:9999px;background:#9ca3af;display:inline-block;animation:cw-blink 1.2s infinite both;}
.cw-dots i:nth-child(2){animation-delay:.2s;}
.cw-dots i:nth-child(3){animation-delay:.4s;}
@keyframes cw-blink{0%,80%,100%{opacity:.25;}40%{opacity:1;}}
.cw-chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:auto;padding-top:8px;}
.cw-chip{font-size:12.5px;padding:7px 12px;border-radius:9999px;background:#fff;border:1px solid #2563eb;color:#2563eb;font-weight:600;}
.cw-chip:hover{background:#2563eb;color:#fff;}
.cw-csat{display:flex;align-items:center;justify-content:center;gap:10px;padding:8px 12px;background:#fff;border-top:1px solid #f1f3f5;font-size:13px;color:#6b7280;}
.cw-csat-btn{font-size:18px;line-height:1;}
.cw-csat-btn:hover{transform:scale(1.25);}
.cw-csat-thanks{color:#2563eb;font-weight:600;}
.cw-human-bar{padding:6px 12px;background:#fff;border-top:1px solid #f1f3f5;text-align:center;}
.cw-human-btn{font-size:12.5px;color:#2563eb;font-weight:600;}
.cw-human-btn:hover{text-decoration:underline;}
.cw-footer{display:flex;align-items:center;gap:8px;padding:12px;border-top:1px solid #eef0f2;background:#fff;}
.cw-msg-input{flex:1;box-sizing:border-box;padding:11px 14px;border:1px solid #e5e7eb;border-radius:9999px;font-size:14px;outline:none;}
.cw-msg-input:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.12);}
.cw-send{width:42px;height:42px;border-radius:9999px;display:flex;align-items:center;justify-content:center;color:#fff;background:#2563eb;flex-shrink:0;}
.cw-send:disabled{opacity:.4;}
.cw-brand{text-align:center;font-size:10px;color:#9ca3af;padding:6px 0 8px;background:#fff;}
.cw-msg-body a{text-decoration:underline;}
@media(max-width:480px){.cw-panel{width:calc(100vw - 16px);height:calc(100vh - 24px);bottom:8px;right:8px;}.cw-bubble{bottom:16px;right:16px;}.cw-teaser{bottom:84px;right:16px;}}
</style>
<script>
window.ppChatWidget = function () {
    return {
        open:false, started:false, online:false, unread:0, busy:false, error:'',
        token:null, name:'', email:'', input:'', messages:[], attn:false, showTeaser:false,
        humanRequested:false, botTyping:false, rating:null, rated:false, _botTimer:null,
        csrf: @json(csrf_token()),

        async boot(){
            this.token = localStorage.getItem('ppcw_token');
            try {
                const r = await this.api('{{ route('chat.init') }}', {token:this.token});
                this.online = r.online;
                if (r.started){ this.started=true; this.token=r.token; this.name=r.name||''; this.messages=r.messages||[]; this.humanRequested=!!r.human_requested; this.rating=(r.rating===0||r.rating===1)?r.rating:null; }
            } catch(e){}
            setInterval(()=>this.refreshOnline(), 60000);
            setInterval(()=>this.poll(), 7000);
            // Persistent bubble tooltip is shown by default; timed/exit teaser off.
            this.attn = !localStorage.getItem('ppcw_opened') && !this.started;
        },
        toggle(){ this.open=!this.open; if(this.open){ this.unread=0; this.attn=false; this.showTeaser=false; localStorage.setItem('ppcw_opened','1'); this.$nextTick(()=>this.scrollDown()); } },
        dismissTeaser(){ this.showTeaser=false; this.attn=false; localStorage.setItem('ppcw_opened','1'); },

        async begin(){
            this.error='';
            if(!this.email.trim() || !this.email.includes('@')){ this.error='Please enter a valid email.'; return; }
            this.busy=true;
            try {
                const r = await this.api('{{ route('chat.start') }}', {name:this.name, email:this.email, message:''});
                this.token=r.token; localStorage.setItem('ppcw_token', this.token);
                this.online=r.online; this.messages=r.messages||[]; this.started=true;
                this.humanRequested=!!r.human_requested; this.rating=(r.rating===0||r.rating===1)?r.rating:null;
                this.$nextTick(()=>this.scrollDown());
            } catch(e){ this.error='Something went wrong. Please try again.'; }
            this.busy=false;
        },
        async sendMsg(forced){
            const body = (typeof forced==='string'?forced:this.input).trim(); if(!body) return;
            if(typeof forced!=='string') this.input='';
            try {
                this.botTyping = !this.humanRequested;
                const r = await this.api('{{ route('chat.send') }}', {token:this.token, body});
                this.addMsg(r.message); this.scrollDown();
                const replies = r.replies||[];
                if (replies.length){ clearTimeout(this._botTimer); this._botTimer = setTimeout(()=>{ replies.forEach(m=>this.addMsg(m)); this.botTyping=false; this.scrollDown(); }, 500); }
                else { this.botTyping=false; }
            } catch(e){ this.botTyping=false; if(typeof forced!=='string') this.input=body; }
        },
        suggest(t){ this.sendMsg(t); },
        async requestHuman(){
            if(this.humanRequested) return;
            this.humanRequested=true; this.botTyping=false;
            try { const r = await this.api('{{ route('chat.handoff') }}', {token:this.token}); if(r&&r.message){ this.addMsg(r.message); this.scrollDown(); } } catch(e){}
        },
        async rate(v){
            if(this.rating!==null) return;
            this.rating=v; this.rated=true;
            try { await this.api('{{ route('chat.rate') }}', {token:this.token, rating:v}); } catch(e){}
            setTimeout(()=>{ this.rated=false; }, 4000);
        },
        async poll(){
            if(!this.started || !this.token) return;
            const after = this.messages.length ? this.messages[this.messages.length-1].id : 0;
            try {
                const r = await fetch('{{ route('chat.poll') }}?token='+encodeURIComponent(this.token)+'&after='+after);
                const d = await r.json();
                (d.messages||[]).forEach(m=>{ if(m.sender!=='visitor'){ this.addMsg(m); if(!this.open) this.unread++; } });
                if((d.messages||[]).length) this.scrollDown();
            } catch(e){}
        },
        async refreshOnline(){ try { const r = await this.api('{{ route('chat.init') }}', {token:this.token}); this.online=r.online; } catch(e){} },
        format(text){
            const esc = String(text==null?'':text).replace(/[&<>"]/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
            return esc.replace(/(https?:\/\/[^\s<]+)/g,'<a href="$1" target="_blank" rel="noopener">$1</a>').replace(/\n/g,'<br>');
        },
        addMsg(m){ if(!m || this.messages.some(x=>x.id===m.id)) return; if(m.sender && m.sender!=='visitor') this.botTyping=false; this.messages.push(m); },
        scrollDown(){ this.$nextTick(()=>{ const s=this.$refs.scroll; if(s) s.scrollTop=s.scrollHeight; }); },
        async api(url, payload){
            const r = await fetch(url, {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':this.csrf,'Accept':'application/json'}, body:JSON.stringify(payload)});
            if(!r.ok) throw new Error('http'); return r.json();
        },
    };
};
</script>
@endonce
