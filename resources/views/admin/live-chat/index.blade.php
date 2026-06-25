<x-admin-layout>
    <x-slot name="header">Live Chat</x-slot>

    <div x-data="ppLiveChat({{ Js::from($conversations->map(fn($c)=>['id'=>$c->id,'name'=>$c->displayName(),'email'=>$c->email,'status'=>$c->status,'mode'=>$c->mode,'unread'=>$c->unread_for_admin,'human'=>(bool)$c->human_requested,'rating'=>$c->rating,'last'=>optional($c->last_message_at)->diffForHumans()])) }}, {{ (int) ($openId ?? 0) }})"
         x-init="boot()" class="space-y-4">

        {{-- KPIs --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white rounded-lg shadow px-4 py-3"><p class="text-xs text-gray-500">Conversations (30d)</p><p class="text-xl font-bold">{{ number_format($stats['total']) }}</p></div>
            <div class="bg-white rounded-lg shadow px-4 py-3"><p class="text-xs text-gray-500">Open now</p><p class="text-xl font-bold">{{ number_format($stats['open_now']) }}</p></div>
            <div class="bg-white rounded-lg shadow px-4 py-3"><p class="text-xs text-gray-500">Bot-resolved</p><p class="text-xl font-bold">{{ $stats['bot_resolved_pct'] }}%</p></div>
            <div class="bg-white rounded-lg shadow px-4 py-3"><p class="text-xs text-gray-500">Leads (30d)</p><p class="text-xl font-bold">{{ number_format($stats['leads']) }}</p></div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden flex" style="height:calc(100vh - 280px);min-height:460px;">
            {{-- Sidebar --}}
            <div class="w-72 border-r border-gray-200 flex flex-col">
                <div class="px-4 py-3 border-b border-gray-100 text-xs font-medium text-gray-500 uppercase">Conversations</div>
                <div class="flex-1 overflow-y-auto divide-y divide-gray-100">
                    <template x-for="c in conversations" :key="c.id">
                        <button type="button" @click="openConv(c.id)" class="w-full text-left px-4 py-3 hover:bg-gray-50" :class="active && active.id===c.id ? 'bg-blue-50' : ''">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-medium text-gray-900 truncate" x-text="c.name"></span>
                                <span x-show="c.unread>0" x-text="c.unread" class="ml-2 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[11px] font-bold bg-red-500 text-white"></span>
                            </div>
                            <div class="text-xs text-gray-500 truncate" x-text="c.email"></div>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-[11px] text-gray-400" x-text="c.last"></span>
                                <span class="flex items-center gap-1">
                                    <span x-show="c.human" title="Human requested">🙋</span>
                                    <span x-show="c.rating===1">👍</span><span x-show="c.rating===0">👎</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded" :class="c.status==='open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'" x-text="c.status"></span>
                                </span>
                            </div>
                        </button>
                    </template>
                    <div x-show="conversations.length===0" class="px-4 py-8 text-center text-sm text-gray-400">No conversations yet.</div>
                </div>
            </div>

            {{-- Active --}}
            <div class="flex-1 flex flex-col">
                <template x-if="!active"><div class="flex-1 flex items-center justify-center text-gray-400 text-sm">Select a conversation to reply.</div></template>
                <template x-if="active">
                    <div class="flex-1 flex flex-col min-h-0">
                        <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900" x-text="active.name"></p>
                                <p class="text-xs text-gray-500" x-text="active.email + (active.human_requested ? ' · 🙋 wants a human' : '')"></p>
                            </div>
                            <button type="button" @click="closeConv()" class="text-sm text-gray-500 hover:text-gray-800" x-text="active.status==='open' ? 'Close chat' : 'Reopen'"></button>
                        </div>
                        <div x-ref="thread" class="flex-1 overflow-y-auto p-5 space-y-2 bg-gray-50">
                            <template x-for="m in active.messages" :key="m.id">
                                <div class="flex" :class="m.sender==='visitor' ? 'justify-start' : 'justify-end'">
                                    <div class="max-w-[70%] px-3.5 py-2 rounded-2xl text-sm shadow-sm" :class="m.sender==='visitor' ? 'bg-white border border-gray-200 text-gray-800 rounded-bl-sm' : 'bg-blue-600 text-white rounded-br-sm'">
                                        <p class="break-words" x-html="format(m.body)"></p>
                                        <span class="block text-[10px] opacity-60 mt-1" x-text="(m.sender!=='visitor' && m.author_name ? m.author_name+' · ' : '') + m.time"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="border-t border-gray-200 p-3">
                            <div class="flex items-center gap-2">
                                <input type="text" x-model="reply" @keydown.enter="send()" placeholder="Type your reply…" class="flex-1 px-4 py-2 border border-gray-300 rounded-full text-sm focus:ring-blue-500 focus:border-blue-500">
                                <button type="button" @click="send()" :disabled="!reply.trim()" class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-full hover:bg-blue-700 disabled:opacity-50">Send</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function ppLiveChat(initial, openId){
        return {
            conversations: initial||[], active:null, reply:'',
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            boot(){
                this.heartbeat(); setInterval(()=>this.heartbeat(), 25000);
                setInterval(()=>this.refreshList(), 8000);
                setInterval(()=>this.pollActive(), 5000);
                if (openId) this.openConv(openId);
            },
            async openConv(id){
                const r = await fetch('{{ url('admin/live-chat') }}/'+id, {headers:{'Accept':'application/json'}});
                this.active = await r.json();
                const c = this.conversations.find(x=>x.id===id); if(c) c.unread=0;
                this.scrollDown();
            },
            async pollActive(){
                if(!this.active) return;
                try {
                    const r = await fetch('{{ url('admin/live-chat') }}/'+this.active.id, {headers:{'Accept':'application/json'}});
                    const d = await r.json();
                    (d.messages||[]).forEach(m=>{ if(!this.active.messages.some(x=>x.id===m.id)){ this.active.messages.push(m); this.scrollDown(); } });
                } catch(e){}
            },
            async send(){
                const body=this.reply.trim(); if(!body||!this.active) return; this.reply='';
                const r = await fetch('{{ url('admin/live-chat') }}/'+this.active.id+'/reply', {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':this.csrf,'Accept':'application/json'}, body:JSON.stringify({body})});
                const d = await r.json(); if(d.message){ this.addMsg(d.message); this.scrollDown(); }
            },
            async closeConv(){
                if(!this.active) return;
                const r = await fetch('{{ url('admin/live-chat') }}/'+this.active.id+'/close', {method:'POST', headers:{'X-CSRF-TOKEN':this.csrf,'Accept':'application/json'}});
                const d = await r.json(); this.active.status=d.status; this.refreshList();
            },
            addMsg(m){ if(!m||!this.active) return; if(this.active.messages.some(x=>x.id===m.id)) return; this.active.messages.push(m); },
            scrollDown(){ this.$nextTick(()=>{ const t=this.$refs.thread; if(t) t.scrollTop=t.scrollHeight; }); },
            async refreshList(){ try { const r = await fetch('{{ route('admin.live-chat.list') }}', {headers:{'Accept':'application/json'}}); const d = await r.json(); this.conversations=d.conversations||[]; } catch(e){} },
            heartbeat(){ fetch('{{ route('admin.live-chat.heartbeat') }}', {method:'POST', headers:{'X-CSRF-TOKEN':this.csrf,'Accept':'application/json'}}).catch(()=>{}); },
            format(text){ const esc=String(text==null?'':text).replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); return esc.replace(/(https?:\/\/[^\s<]+)/g,'<a href="$1" target="_blank" rel="noopener" class="underline">$1</a>').replace(/\n/g,'<br>'); },
        };
    }
    </script>
    @endpush
</x-admin-layout>
