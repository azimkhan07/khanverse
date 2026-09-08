import { useState, useEffect, useRef } from 'react';
import { useNavigate } from 'react-router-dom';
import {
    Bell, CheckCheck, PanelLeft, Settings, User, LockKeyhole, LogOut,
    ChevronDown,
} from 'lucide-react';
import ThemeToggle from "../../components/common/ThemeToggle";
import api from '../api';

function NotificationBell({ base }) {
    const [count, setCount] = useState(0);
    const [list, setList] = useState([]);
    const [open, setOpen] = useState(false);
    const ref = useRef(null);

    const load = () => {
        api.get(`/${base}/notifications`, { params: { per_page: 5 } }).then((res) => {
            setList(res.data.data || []);
            setCount(typeof res.data.unread_count === 'number' ? res.data.unread_count : 0);
        }).catch(() => {});
    };

    useEffect(() => {
        load();
        const iv = setInterval(load, 30000);
        return () => clearInterval(iv);
    }, [base]);

    useEffect(() => {
        const onClick = (e) => { if (ref.current && !ref.current.contains(e.target)) setOpen(false); };
        document.addEventListener('mousedown', onClick);
        return () => document.removeEventListener('mousedown', onClick);
    }, []);

    const markAll = async () => {
        await api.post(`/${base}/notifications/read-all`);
        setCount(0); load();
    };

    return (
        <div className="topnav-bell" ref={ref}>
            <button className="bell-btn" onClick={() => setOpen((o) => !o)}>
                <Bell size={17} />
                {count > 0 && <span className="bell-dot">{count > 9 ? '9+' : count}</span>}
            </button>
            {open && (
                <div className="bell-dropdown">
                    <div className="bell-head">
                        <strong>Notifications</strong>
                        <button className="btn-link" onClick={markAll}><CheckCheck size={13} /> Mark all</button>
                    </div>
                    <div className="bell-list">
                        {list.length === 0 ? <div className="empty">No notifications</div> : list.map((n, i) => (
                            <a key={n.id || i} className="bell-item" href={n.url || '#main-app'}>
                                <div style={{ fontWeight: 600, fontSize: 12.5 }}>{n.title}</div>
                                <div style={{ fontSize: 11.5, color: '#6b7280' }}>{n.message}</div>
                                <div style={{ fontSize: 10.5, color: '#9ca3af' }}>{n.time}</div>
                            </a>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}

function LockScreen({ user }) {
    const [leaving, setLeaving] = useState(false);
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [busy, setBusy] = useState(false);
    const inputRef = useRef(null);
    const name = user?.name || 'User';

    useEffect(() => {
        if (inputRef.current) inputRef.current.focus();
    }, []);

    const unlock = async () => {
        if (!password || busy) return;
        setBusy(true);
        setError('');
        try {
            const { data } = await api.post('/lock/verify', { password });
            if (data.status) {
                setLeaving(true);
                setTimeout(() => window.location.reload(), 320);
            } else {
                setError(data.message || 'Incorrect password.');
                setPassword('');
                if (inputRef.current) inputRef.current.focus();
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Unable to verify. Try again.');
            setPassword('');
            if (inputRef.current) inputRef.current.focus();
        } finally {
            setBusy(false);
        }
    };

    return (
        <div className={`lock-screen ${leaving ? 'lock-leave' : ''}`}>
            <svg className="lock-bg" viewBox="0 0 1440 320" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <linearGradient id="lockAur1" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stopColor="#6d28d9" />
                        <stop offset="50%" stopColor="#8b5cf6" />
                        <stop offset="100%" stopColor="#06b6d4" />
                    </linearGradient>
                    <linearGradient id="lockAur2" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stopColor="#ec4899" />
                        <stop offset="100%" stopColor="#8b5cf6" />
                    </linearGradient>
                </defs>
                <path fill="url(#lockAur1)" opacity="0.55" d="M0,224L60,213.3C120,203,240,181,360,181.3C480,181,600,203,720,213.3C840,224,960,224,1080,202.7C1200,181,1320,139,1380,117.3L1440,96L1440,320L0,320Z"></path>
                <path fill="url(#lockAur2)" opacity="0.45" d="M0,128L60,138.7C120,149,240,171,360,165.3C480,160,600,128,720,138.7C840,149,960,203,1080,213.3C1200,224,1320,192,1380,176L1440,160L1440,320L0,320Z"></path>
            </svg>

            <div className="lock-card">
                <div className="lock-icon">
                    <LockKeyhole size={30} />
                </div>
                <img
                    className="lock-avatar"
                    src={user?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=6d28d9&color=fff`}
                    alt={name}
                />
                <h2>{name}</h2>
                <p>Your workspace is locked. Enter your password to continue.</p>
                <div className="lock-input">
                    <input
                        ref={inputRef}
                        type="password"
                        className="form-input"
                        placeholder="Password"
                        value={password}
                        disabled={leaving}
                        onChange={(e) => setPassword(e.target.value)}
                        onKeyDown={(e) => e.key === 'Enter' && unlock()}
                    />
                </div>
                {error && <div className="lock-error">{error}</div>}
                <button className="btn btn-primary" onClick={unlock} disabled={leaving || busy}>
                    {leaving ? 'Unlocking...' : busy ? 'Verifying...' : 'Unlock'}
                </button>
            </div>
        </div>
    );
}

function UserMenu({ user, base }) {
    const [open, setOpen] = useState(false);
    const [locked, setLocked] = useState(false);
    const ref = useRef(null);
    const navigate = useNavigate();

    useEffect(() => {
        const onDown = (e) => { if (open && ref.current && !ref.current.contains(e.target)) setOpen(false); };
        document.addEventListener('mousedown', onDown);
        return () => document.removeEventListener('mousedown', onDown);
    }, [open]);

    const go = (path) => { setOpen(false); navigate(path); };

    const logout = async (e) => {
        e.preventDefault();
        try { sessionStorage.removeItem("skillnest-install-dismissed-session"); } catch { /* ignore */ }
        try { await api.post('/logout'); } catch { /* ignore */ }
        window.location.href = '/login';
    };

    const role = ['seller', 'buyer', 'admin'].includes(base) ? base : 'admin';
    const canSettings = role !== 'admin';
    const name = user?.name || 'User';

    return (
        <>
            <div className="topnav-user" ref={ref}>
                <button className="user-menu-btn" onClick={() => setOpen((o) => !o)} aria-label="Account menu">
                    <img
                        src={user?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=4F46E5&color=fff`}
                        alt={name}
                    />
                    <span>{name}</span>
                    <ChevronDown size={15} className="user-menu-chevron" />
                </button>

                {open && (
                    <div className="user-dropdown">
                        <div className="user-dropdown-head">
                            <img src={user?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=4F46E5&color=fff`} alt="" />
                            <div>
                                <strong>{name}</strong>
                                <span>{user?.email || ''}</span>
                            </div>
                        </div>
                        <div className="user-dropdown-menu">
                            {canSettings && (
                                <button onClick={() => go('/settings')}>
                                    <Settings size={16} /> Settings
                                </button>
                            )}
                            <button onClick={() => go('/profile')}>
                                <User size={16} /> Profile
                            </button>
                            <button onClick={() => { setOpen(false); setLocked(true); }}>
                                <LockKeyhole size={16} /> Lock Screen
                            </button>
                            <button className="danger" onClick={logout}>
                                <LogOut size={16} /> Logout
                            </button>
                        </div>
                    </div>
                )}
            </div>

            {locked && (
                <LockScreen user={user} />
            )}
        </>
    );
}

function TopNav({ title, user, isDark, toggleTheme, sidebarHidden, onToggleSidebar }) {
    const base = window.location.pathname.split('/')[1] || 'admin';

    return (
        <div className="top-nav">
            <div className="topnav-left">
                {onToggleSidebar && (
                    <button className="topnav-menu-btn" onClick={onToggleSidebar} title={sidebarHidden ? "Show sidebar" : "Hide sidebar"}>
                        <PanelLeft size={18} />
                    </button>
                )}
                <h2>{title}</h2>
            </div>
            <div className="topnav-right">
                <NotificationBell base={base} />
                <ThemeToggle isDark={isDark} onToggle={toggleTheme} size="small" />
                {user && <UserMenu user={user} base={base} />}
            </div>
        </div>
    );
}

export default TopNav;