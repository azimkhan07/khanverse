import { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { ArrowLeft, FileText, KeyRound, Server, CheckCircle2, PackageCheck, BookOpen, MessageSquare, Lock, Rocket } from 'lucide-react';
import { motion } from 'framer-motion';
import api from '../../shared/api';
import { showDialog } from '../../shared/components/Dialog';
import ProjectChat from '../../shared/components/ProjectChat';

const fallbackProject = {
    id: 201, title: 'E-commerce Redesign', buyer: { full_name: 'Alice Johnson' },
    budget: 2500, status: 'in_progress', deadline: '2026-10-01',
    description: 'Full redesign of e-commerce storefront.',
    progress: 60, attachments: [],     messages: [
        { id: 1, sender: 'buyer', message: 'Please make the homepage design a priority.', created_at: '2026-08-22' },
        { id: 2, sender: 'seller', message: 'Will do. Rough draft by Friday.', created_at: '2026-08-22' },
    ],
};

const statusBadgeMap = { open: 'badge-primary', in_progress: 'badge-warning', delivered: 'badge-info', completed: 'badge-success', cancelled: 'badge-danger' };
const statusOptions = ['open', 'in_progress', 'delivered', 'completed', 'cancelled'];
const statusLabels = { open: 'Open', in_progress: 'In Progress', delivered: 'Delivered', completed: 'Completed', cancelled: 'Cancelled' };

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    show: { opacity: 1, y: 0, transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] } },
};

const stagger = {
    hidden: { opacity: 0 },
    show: { opacity: 1, transition: { staggerChildren: 0.08 } },
};

function ProjectDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [project, setProject] = useState(fallbackProject);
    const [newStatus, setNewStatus] = useState('');

    const [keyInfo, setKeyInfo] = useState(null);
    const [keyInput, setKeyInput] = useState('');
    const [hostings, setHostings] = useState([]);
    const [keyVerified, setKeyVerified] = useState(false);

    const loadHostings = async () => {
        try {
            const { data } = await api.get(`/seller/projects/${id}/hostings`);
            setHostings(data.hostings || []);
            setKeyVerified(!!data.key_verified);
        } catch { /* ignore */ }
    };

    useEffect(() => {
        api.get(`/seller/projects/${id}`).then(({ data }) => {
            setProject(data);
            setNewStatus(data.status || '');
            setKeyVerified(!!data.key_verified || data.delivery_method === 'digital');
            if (data.delivery_method === 'hosting') loadHostings();
        }).catch(() => {});
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [id]);

    const revealKey = async () => {
        try {
            const { data } = await api.get(`/seller/projects/${id}/delivery-key`);
            setKeyInfo({
                key: data.key,
                ...(data.shown_at ? { shown_at: data.shown_at } : {}),
                alreadyShown: !data.key,
            });
        } catch (err) {
            await showDialog({ type: 'warning', title: 'Key Already Shown', message: err.response?.data?.message || 'Unable to show key.' });
            setKeyInfo({ alreadyShown: true });
        }
    };

    const verifyKey = async () => {
        if (!keyInput.trim()) return;
        try {
            const { data } = await api.post(`/seller/projects/${id}/verify-key`, { key: keyInput });
            setKeyVerified(true);
            setKeyInput('');
            if (data.verified) loadHostings();
            await showDialog({ type: 'success', title: 'Key Verified', message: data.message });
        } catch (err) {
            await showDialog({ type: 'danger', title: 'Verification Failed', message: err.response?.data?.message || 'Invalid key.' });
        }
    };

    const forgotKey = async () => {
        const ok = await showDialog({ type: 'warning', title: 'Reset Delivery Key', message: 'A new key will be generated and sent to your notifications.', confirmText: 'Yes, reset key' });
        if (!ok) return;
        try {
            const { data } = await api.post(`/seller/projects/${id}/forgot-key`);
            await showDialog({ type: 'success', title: 'Key Reset', message: data.message });
        } catch (err) {
            await showDialog({ type: 'danger', title: 'Failed', message: err.response?.data?.message || 'Failed to reset key.' });
        }
    };

    const updateStatus = async () => {
        if (!newStatus || newStatus === project.status) return;
        const confirmed = await showDialog({ type: 'warning', title: 'Change Project Status', message: `Set "${project.title}" to "${statusLabels[newStatus] || newStatus.replace('_', ' ')}"?`, confirmText: 'Yes, update' });
        if (!confirmed) return;
        try {
            const { data } = await api.post(`/seller/projects/${id}/status`, { status: newStatus });
            await showDialog({ type: 'success', title: 'Status Updated', message: data.message });
            setProject((prev) => ({ ...prev, status: newStatus }));
            if (newStatus === 'delivered') {
                setHostings([]);
            }
        } catch (err) {
            await showDialog({ type: 'danger', title: 'Update Failed', message: err.response?.data?.message || 'Update failed.' });
        }
    };

    const markLive = async () => {
        const ok = await showDialog({
            type: 'warning',
            title: 'Go Live & Mark Delivered',
            message: 'The project will be marked as delivered and the stored hosting access details will be permanently deleted from the database.',
            confirmText: 'Yes, mark delivered',
        });
        if (!ok) return;
        try {
            const { data } = await api.post(`/seller/projects/${id}/status`, { status: 'delivered' });
            setNewStatus('delivered');
            setHostings([]);
            setProject((prev) => ({ ...prev, status: 'delivered' }));
            await showDialog({ type: 'success', title: 'Project Delivered', message: data.message });
        } catch (err) {
            await showDialog({ type: 'danger', title: 'Failed', message: err.response?.data?.message || 'Failed to mark project delivered.' });
        }
    };

    const muted = { color: 'var(--text-muted)' };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}>
            <motion.div
                className="page-heading page-toolbar"
                initial={{ opacity: 0, y: -16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, ease: [0.22, 1, 0.36, 1] }}
            >
                <div>
                    <button className="btn btn-secondary btn-sm" onClick={() => navigate('/projects')} style={{ marginBottom: 12 }}>
                        <ArrowLeft size={14} /> Back to Projects
                    </button>
                    <h1>{project.title}</h1>
                </div>
                <span className={`badge ${statusBadgeMap[project.status] || 'badge-info'}`}>{statusLabels[project.status] || project.status?.replace('_', ' ')}</span>
            </motion.div>

            <motion.div className="detail-grid" variants={stagger} initial="hidden" animate="show">
                <motion.div className="detail-box" variants={fadeUp}>
                    <h3>Project Information</h3>
                    <div className="detail-row"><span className="label">Budget</span><span className="value">₹{Number(project.budget).toLocaleString('en-IN')}</span></div>
                    <div className="detail-row"><span className="label">Deadline</span><span className="value">{project.deadline ? new Date(project.deadline).toLocaleDateString() : '-'}</span></div>
                    <div className="detail-row"><span className="label">Client</span><span className="value">{project.buyer?.full_name || '-'}</span></div>
                    <div className="detail-row"><span className="label">Delivery</span><span className="value">{project.delivery_method === 'hosting' ? 'Hosting / Handover' : 'Digital'}</span></div>
                    <p style={{ marginTop: 16, color: 'var(--text-secondary)', lineHeight: 1.7 }}>{project.description}</p>
                </motion.div>
                <motion.div className="detail-box" variants={fadeUp}>
                    <h3>Progress</h3>
                    <div className="progress-wrap">
                        <div className="progress-bar"><div className="progress-fill" style={{ width: `${project.progress || 0}%` }} /></div>
                        <span style={{ marginTop: 6 }}>{project.progress || 0}% complete</span>
                    </div>
                    <div style={{ marginTop: 20, display: 'flex', gap: 8, flexDirection: 'column' }}>
                        <Link to={`/projects/${id}/files`} className="btn btn-secondary" style={{ width: '100%' }}>
                            <FileText size={16} /> Manage Project Files
                        </Link>
                        <Link to={`/projects/${id}/deliver`} className="btn btn-primary" style={{ width: '100%' }}>
                            <PackageCheck size={16} /> Submit Delivery
                        </Link>
                    </div>
                </motion.div>
            </motion.div>

            {project.delivery_method === 'hosting' && (
                <motion.div
                    className="detail-box"
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5, delay: 0.15, ease: [0.22, 1, 0.36, 1] }}
                >
                    <div className="card-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <h3 style={{ display: 'flex', alignItems: 'center', gap: 6 }}><KeyRound size={16} /> Delivery Key &amp; Hosting</h3>
                        {keyVerified && <span className="badge badge-success" style={{ display: 'flex', alignItems: 'center', gap: 4 }}><CheckCircle2 size={12} /> Verified</span>}
                    </div>

                    {!keyVerified && (
                        <>
                            <p style={{ fontSize: 13, color: 'var(--text-secondary)', marginTop: 8 }}>
                                This hosting project is protected by a delivery key. Get your key once below, then verify it to unlock the buyer&apos;s hosting login details. The key is hidden from admin.
                            </p>
                            <div style={{ display: 'flex', gap: 8, marginTop: 12, flexWrap: 'wrap' }}>
                                <button className="btn btn-primary btn-sm" onClick={revealKey}><KeyRound size={14} /> Get Delivery Key</button>
                                <button className="btn btn-secondary btn-sm" onClick={forgotKey}><BookOpen size={14} /> Forgot Key</button>
                            </div>

                            {keyInfo && (
                                <motion.div
                                    initial={{ opacity: 0, height: 0 }}
                                    animate={{ opacity: 1, height: 'auto' }}
                                    transition={{ duration: 0.35 }}
                                    style={{ marginTop: 12, background: 'var(--accent-light, #eef2ff)', borderRadius: 10, padding: 14 }}
                                >
                                    {keyInfo.alreadyShown ? (
                                        <>
                                            <p style={{ fontSize: 13, color: 'var(--text-muted)' }}>You already claimed this key earlier. Use it below.</p>
                                            {keyInfo.verified ? null : <p style={{ fontSize: 13, marginTop: 6 }}>If you lost it, use the <strong>Forgot Key</strong> button to get a new one sent to your notifications and email.</p>}
                                        </>
                                    ) : (
                                        <>
                                            <p style={{ fontSize: 13, marginTop: 6 }}>Your one-time delivery key (save this — it will not be shown again):</p>
                                            <div style={{ fontSize: 22, fontWeight: 700, letterSpacing: 2, color: 'var(--accent)', margin: '8px 0' }}>{keyInfo.key}</div>
                                        </>
                                    )}
                                </motion.div>
                            )}

                            <div style={{ marginTop: 14 }}>
                                <label className="form-label">Enter Delivery Key to unlock hosting</label>
                                <div className="status-form">
                                    <input
                                        className="form-input"
                                        placeholder="XXXX-XXXX-XXXX"
                                        value={keyInput}
                                        onChange={(e) => setKeyInput(e.target.value)}
                                        style={{ textTransform: 'uppercase' }}
                                    />
                                    <button className="btn btn-primary" onClick={verifyKey} disabled={!keyInput.trim()}>Verify</button>
                                </div>
                            </div>
                        </>
                    )}

                    {!keyVerified && hostings.length > 0 && (
                        <div style={{ marginTop: 14 }}>
                            <p style={{ fontSize: 13, color: 'var(--text-muted)' }}>
                                Buyer has submitted hosting access. Locked preview — only the server identity is shown. Enter your key above to unlock the rest.
                            </p>
                            {hostings.map((h) => (
                                <div
                                    className="hosting-box"
                                    key={h.id}
                                    style={{ marginTop: 10, border: '1px solid var(--border-color)', borderRadius: 10, padding: 14 }}
                                >
                                    <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 10 }}>
                                        <Lock size={15} style={muted} />
                                        <strong style={{ textTransform: 'capitalize' }}>{h.hosting_type || 'Hosting'}</strong>
                                        <span className="badge badge-info" style={{ marginLeft: 'auto' }}>Locked</span>
                                    </div>
                                    <div className="detail-grid" style={{ gap: 8 }}>
                                        <div><div className="label">Host</div><div className="value" style={{ fontFamily: 'monospace' }}>{h.host || '-'}</div></div>
                                        <div><div className="label">Username</div><div className="value" style={{ fontFamily: 'monospace' }}>{h.username || '-'}</div></div>
                                        <div><div className="label">Port</div><div className="value">••••</div></div>
                                        <div><div className="label">Password</div><div className="value">••••••••</div></div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}

                    {keyVerified && (
                        <>
                            <p style={{ fontSize: 13, color: 'var(--success)', marginTop: 8, display: 'flex', alignItems: 'center', gap: 6 }}>
                                <CheckCircle2 size={15} /> Key verified — hosting details unlocked below.
                            </p>

                            {hostings.length === 0 ? (
                                <motion.div
                                    className="empty-state"
                                    initial={{ opacity: 0, scale: 0.9 }}
                                    animate={{ opacity: 1, scale: 1 }}
                                    transition={{ duration: 0.4 }}
                                    style={{ marginTop: 12, padding: '32px 16px', textAlign: 'center' }}
                                >
                                    <div style={{ width: 48, height: 48, borderRadius: '50%', background: 'var(--bg-secondary, #f1f5f9)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 12px' }}>
                                        <Server size={22} style={{ color: 'var(--text-muted)' }} />
                                    </div>
                                    <p>
                                        {project.status === 'delivered'
                                            ? 'Hosting access details were cleared after this project was marked delivered.'
                                            : 'Buyer has not submitted hosting details yet.'}
                                    </p>
                                </motion.div>
                            ) : (
                                <>
                                    {hostings.map((h) => (
                                        <motion.div
                                            className="hosting-box"
                                            key={h.id}
                                            initial={{ opacity: 0, y: 12 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            transition={{ duration: 0.4 }}
                                            style={{ marginTop: 14, border: '1px solid var(--border-color)', borderRadius: 10, padding: 14 }}
                                        >
                                            <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 10 }}>
                                                <Server size={16} style={muted} />
                                                <strong style={{ textTransform: 'capitalize' }}>{h.hosting_type || 'Hosting'} Details</strong>
                                                <span className="badge badge-success" style={{ marginLeft: 'auto' }}>Unlocked</span>
                                            </div>
                                            <div className="detail-grid" style={{ gap: 8 }}>
                                                <div><div className="label">Host</div><div className="value" style={{ fontFamily: 'monospace' }}>{h.host || '-'}</div></div>
                                                <div><div className="label">Port</div><div className="value">{h.port || '-'}</div></div>
                                                <div><div className="label">Protocol</div><div className="value">{h.protocol || '-'}</div></div>
                                                <div><div className="label">Username</div><div className="value" style={{ fontFamily: 'monospace' }}>{h.username || '-'}</div></div>
                                                <div><div className="label">Password</div><div className="value" style={{ fontFamily: 'monospace' }}>{h.password || '-'}</div></div>
                                                {h.provider && <div><div className="label">Provider</div><div className="value">{h.provider}</div></div>}
                                                {h.domain && <div><div className="label">Domain</div><div className="value">{h.domain}</div></div>}
                                            </div>
                                            {h.notes && <p style={{ fontSize: 13, color: 'var(--text-muted)', marginTop: 8 }}>{h.notes}</p>}
                                        </motion.div>
                                    ))}

                                    {project.status !== 'delivered' && (
                                        <div style={{ marginTop: 14, paddingTop: 14, borderTop: '1px dashed var(--border-color)' }}>
                                            <button className="btn btn-success" onClick={markLive}><Rocket size={15} /> Make Live &amp; Mark Delivered</button>
                                            <p style={{ fontSize: 12, color: 'var(--text-muted)', marginTop: 8 }}>
                                                Deploy the project using the details above, then mark it delivered. Stored hosting credentials are permanently deleted once the project is delivered.
                                            </p>
                                        </div>
                                    )}
                                </>
                            )}
                        </>
                    )}
                </motion.div>
            )}

            <motion.div
                className="detail-box"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: 0.2, ease: [0.22, 1, 0.36, 1] }}
            >
                <div className="card-header"><h3>Update Status</h3></div>
                <div className="status-form">
                    <select className="form-select" value={newStatus} onChange={(e) => setNewStatus(e.target.value)}>
                        {statusOptions.map((s) => <option key={s} value={s}>{statusLabels[s] || s.replace('_', ' ')}</option>)}
                    </select>
                    <button className="btn btn-primary" onClick={updateStatus} disabled={!newStatus || newStatus === project.status}>Update</button>
                </div>
            </motion.div>

            <motion.div
                className="detail-box"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: 0.25, ease: [0.22, 1, 0.36, 1] }}
            >
                <h3 style={{ display: 'flex', alignItems: 'center', gap: 8 }}><MessageSquare size={18} style={{ color: 'var(--accent)' }} /> Project Chat</h3>
                {project.status === 'completed' && (
                    <div className="alert alert-info" style={{ marginTop: 4 }}>This project is complete. Chat is read-only.</div>
                )}
                <ProjectChat projectId={project.id} me="seller" disabled={project.status === 'completed'} />
            </motion.div>

            <style>{`
                .form-label { display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:var(--text-secondary); }
                .label { color: var(--text-muted); font-size: 12px; }
                .detail-grid { display:grid; grid-template-columns: 1fr 1fr; gap: 12px; }
                .hosting-box .label { font-size:11px; margin-bottom:2px; }
                .hosting-box .value { font-size:13px; }
            `}</style>
        </motion.div>
    );
}

export default ProjectDetail;
