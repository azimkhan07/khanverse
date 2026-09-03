import { useState, useEffect, useRef } from 'react';
import { Send, MessageSquare, Smile } from 'lucide-react';
import api from '../api';

export default function LiveChat() {
    const [conversations, setConversations] = useState([]);
    const [activeId, setActiveId] = useState(null);
    const [messages, setMessages] = useState([]);
    const [text, setText] = useState('');
    const [sending, setSending] = useState(false);
    const [openNew, setOpenNew] = useState(false);
    const [projectId, setProjectId] = useState('');
    const listRef = useRef(null);

    const loadConversations = () => {
        api.get(`/chat`).then((res) => {
            const c = res.data.conversations || [];
            if (JSON.stringify(c) !== JSON.stringify(conversations)) setConversations(c);
        }).catch(() => {});
    };

    const loadMessages = (id) => {
        api.get(`/chat/${id}/messages`).then((res) => {
            setMessages(res.data.messages || []);
        }).catch(() => {});
    };

    useEffect(() => { loadConversations(); }, []);

    // polling for real-time feel
    useEffect(() => {
        const iv = setInterval(() => {
            loadConversations();
            if (activeId) loadMessages(activeId);
        }, 2500);
        return () => clearInterval(iv);
    }, [activeId]);

    useEffect(() => {
        if (listRef.current) listRef.current.scrollTop = listRef.current.scrollHeight;
    }, [messages]);

    const openConv = (id) => {
        setActiveId(id);
        loadMessages(id);
        api.post(`/chat/${id}/seen`).catch(() => {});
    };

    const send = async (e) => {
        e.preventDefault();
        if (!text.trim() || !activeId) return;
        setSending(true);
        try {
            await api.post(`/chat/send`, { conversation_id: activeId, message: text });
            setText('');
            loadMessages(activeId);
            loadConversations();
        } catch {}
        setSending(false);
    };

    const openByProject = async (e) => {
        e.preventDefault();
        try {
            const res = await api.post(`/chat/open`, { project_id: projectId });
            setOpenNew(false); setProjectId('');
            openConv(res.data.conversation_id);
            loadConversations();
        } catch {}
    };

    return (
        <div style={{ display: 'flex', gap: 0, height: 'calc(100vh - 220px)', minHeight: 480 }}>
            {/* Conversation list */}
            <div style={{ width: 320, borderRight: '1px solid var(--border,#E5E7EB)', display: 'flex', flexDirection: 'column' }}>
                <div style={{ padding: '14px 18px', borderBottom: '1px solid var(--border,#E5E7EB)', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                    <strong>Messages</strong>
                    <button className="btn btn-primary btn-sm" onClick={() => setOpenNew(true)}><MessageSquare size={13} /> New</button>
                </div>
                <div style={{ overflowY: 'auto', flex: 1 }}>
                    {conversations.length === 0 ? (
                        <div className="empty">No conversations yet</div>
                    ) : conversations.map((c) => (
                        <div key={c.id}
                            onClick={() => openConv(c.id)}
                            style={{ padding: '13px 18px', cursor: 'pointer', borderBottom: '1px solid var(--border,#F1F5F9)', background: activeId === c.id ? 'var(--accent-light)' : 'transparent', display: 'flex', alignItems: 'center', gap: 10 }}
                        >
                            <div className="avatar-sm">{c.other_user?.image ? <img src={c.other_user.image} alt="" style={{ width: '100%', height: '100%', borderRadius: '50%', objectFit: 'cover' }} /> : (c.other_user?.name?.[0] || '?')}</div>
                            <div style={{ flex: 1, minWidth: 0 }}>
                                <div style={{ fontWeight: 600, fontSize: 13 }}>{c.other_user?.name || 'User'}</div>
                                <div style={{ fontSize: 11.5, color: '#9ca3af', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                    {c.last_message?.message || 'No messages'}
                                </div>
                            </div>
                            {c.last_message_at && <div style={{ fontSize: 10, color: '#9ca3af' }}>{new Date(c.last_message_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>}
                        </div>
                    ))}
                </div>
            </div>

            {/* Chat window */}
            <div style={{ flex: 1, display: 'flex', flexDirection: 'column', minWidth: 0 }}>
                {!activeId ? (
                    <div className="empty" style={{ flex: 1, display: 'grid', placeItems: 'center' }}>
                        <div><MessageSquare size={30} style={{ margin: '0 auto 8px', color: '#cbd5e1' }} /><p>Select a conversation</p></div>
                    </div>
                ) : (
                    <>
                        <div ref={listRef} style={{ flex: 1, overflowY: 'auto', padding: '18px', display: 'flex', flexDirection: 'column', gap: 8 }}>
                            {messages.map((m) => (
                                <div key={m.id} style={{ alignSelf: m.sender_id === (window.__USER__?.id) ? 'flex-end' : 'flex-start', maxWidth: '72%' }}>
                                    <div style={{ padding: '9px 13px', borderRadius: 12, background: m.sender_id === (window.__USER__?.id) ? 'var(--accent, #4F46E5)' : 'var(--bg-hover,#F1F5F9)', color: m.sender_id === (window.__USER__?.id) ? '#fff' : 'inherit', fontSize: 13 }}>
                                        {m.message}
                                        {m.attachment && <div style={{ fontSize: 11, opacity: 0.8, marginTop: 4 }}>📎 {m.attachment}</div>}
                                        <div style={{ fontSize: 10, opacity: 0.7, marginTop: 3, textAlign: 'right' }}>{m.chat_time || new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                                    </div>
                                </div>
                            ))}
                        </div>
                        <form onSubmit={send} style={{ display: 'flex', gap: 8, padding: 14, borderTop: '1px solid var(--border,#E5E7EB)' }}>
                            <button type="button" className="btn-icon" title="Emoji (coming soon)" style={{ flexShrink: 0 }}><Smile size={16} /></button>
                            <input style={{ flex: 1 }} placeholder="Type a message..." value={text} onChange={(e) => setText(e.target.value)} />
                            <button type="submit" className="btn btn-primary btn-sm" disabled={sending}><Send size={14} /> Send</button>
                        </form>
                    </>
                )}
            </div>

            {openNew && (
                <div className="modal-overlay" onClick={() => setOpenNew(false)}>
                    <div className="modal-box" style={{ maxWidth: 420 }} onClick={(e) => e.stopPropagation()}>
                        <div className="modal-header"><h3>Start Conversation</h3><button className="modal-close" onClick={() => setOpenNew(false)}>×</button></div>
                        <div className="modal-body">
                            <form className="modal-content" onSubmit={openByProject}>
                                <div className="form-group">
                                    <label>Project ID</label>
                                    <input value={projectId} onChange={(e) => setProjectId(e.target.value)} placeholder="Enter project id to chat" required />
                                </div>
                                <div className="modal-actions">
                                    <button type="button" className="btn" onClick={() => setOpenNew(false)}>Cancel</button>
                                    <button type="submit" className="btn btn-primary btn-sm">Open Chat</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

