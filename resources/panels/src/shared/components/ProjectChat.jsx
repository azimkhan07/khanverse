import { useState, useEffect, useRef } from 'react';
import { motion } from 'framer-motion';
import { MessageSquare, Send } from 'lucide-react';
import api from '../api';

export default function ProjectChat({ projectId, me, disabled }) {
    const [convId, setConvId] = useState(null);
    const [messages, setMessages] = useState([]);
    const [text, setText] = useState('');
    const [busy, setBusy] = useState(false);
    const scrollRef = useRef(null);

    const vendor = ['seller', 'buyer'].includes(me) ? me : 'buyer';

    useEffect(() => {
        let mounted = true;
        api.post('/chat/open', { project_id: projectId }).then(({ data }) => {
            if (!mounted) return;
            setConvId(data.conversation_id);
            return api.get(`/chat/${data.conversation_id}/messages`);
        }).then((res) => {
            if (mounted && res) setMessages(res.data.messages || []);
        }).catch(() => {});
        return () => { mounted = false; };
    }, [projectId]);

    useEffect(() => {
        if (scrollRef.current) scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
    }, [messages]);

    const send = async () => {
        if (!text.trim() || disabled || !convId) return;
        const optimistic = {
            id: 'tmp-' + Date.now(),
            sender_id: 0,
            message: text,
            created_at: new Date().toISOString(),
        };
        setMessages((prev) => [...prev, optimistic]);
        setText('');
        setBusy(true);
        const fd = new FormData();
        fd.append('conversation_id', convId);
        fd.append('message', text);
        try {
            const { data } = await api.post('/chat/send', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
            setMessages((prev) => prev.map((m) => (m.id === optimistic.id ? (data.data || optimistic) : m)));
            if (convId) api.post(`/chat/${convId}/seen`).catch(() => {});
        } catch {
            /* keep optimistic */
        } finally {
            setBusy(false);
        }
    };

    return (
        <div className="chat-box">
            <div className="chat-messages" ref={scrollRef}>
                {messages.length === 0 ? (
                    <div className="empty-state" style={{ padding: '32px 16px', textAlign: 'center' }}>
                        <div style={{ width: 48, height: 48, borderRadius: '50%', background: 'var(--bg-secondary, #f1f5f9)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 12px' }}>
                            <MessageSquare size={22} style={{ color: 'var(--text-muted)' }} />
                        </div>
                        <p>No messages yet. Start the conversation below.</p>
                    </div>
                ) : (
                    messages.map((m, i) => {
                        const mine = typeof m.is_mine === 'boolean'
                            ? m.is_mine
                            : m.sender === vendor;
                        return (
                            <motion.div
                                key={m.id}
                                className={`chat-bubble ${mine ? 'self' : ''}`}
                                initial={{ opacity: 0, y: 10, scale: 0.97 }}
                                animate={{ opacity: 1, y: 0, scale: 1 }}
                                transition={{ duration: 0.3, delay: i * 0.04 }}
                            >
                                <p>{m.message}</p>
                                <small>{m.chat_time || (m.created_at ? new Date(m.created_at).toLocaleString() : '')}</small>
                            </motion.div>
                        );
                    })
                )}
            </div>
            <div className="chat-input">
                <input
                    className="form-input"
                    placeholder={disabled ? 'Chat is read-only' : 'Type a message...'}
                    value={text}
                    disabled={disabled}
                    onChange={(e) => setText(e.target.value)}
                    onKeyDown={(e) => e.key === 'Enter' && send()}
                />
                <button className="btn btn-primary" onClick={send} disabled={disabled || busy}><Send size={16} /></button>
            </div>
        </div>
    );
}