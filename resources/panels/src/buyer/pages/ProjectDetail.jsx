import { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft, MessageSquare, FileText, Server, CheckCircle2, Layers, X } from 'lucide-react';
import api from '../../shared/api';
import { showDialog } from '../../shared/components/Dialog';
import ProjectChat from '../../shared/components/ProjectChat';

const fallbackProject = {
    id: 201, title: 'E-commerce Redesign', seller: { full_name: 'Sara Ali' },
    budget: 2500, status: 'in_progress', deadline: '2026-10-01',
    description: 'Full redesign of e-commerce storefront.', progress: 60,
    attachments: [],
};

const statusBadgeMap = { open: 'badge-primary', in_progress: 'badge-warning', delivered: 'badge-info', completed: 'badge-success', cancelled: 'badge-danger' };
const statusLabels = { open: 'Open', in_progress: 'In Progress', delivered: 'Delivered', completed: 'Completed', cancelled: 'Cancelled' };

const HOSTING_TYPES = ['ftp', 'cpanel', 'hosting', 'git'];

const container = {
    hidden: {},
    show: { transition: { staggerChildren: 0.08 } },
};

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

const fieldVariant = {
    hidden: { opacity: 0, y: 14 },
    show: (i) => ({ opacity: 1, y: 0, transition: { duration: 0.4, delay: i * 0.05, ease: [0.22, 1, 0.36, 1] } }),
};

function ProjectDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [project, setProject] = useState(fallbackProject);
    const [hosting, setHosting] = useState(null);
    const [form, setForm] = useState({ hosting_type: 'ftp', host: '', port: '', username: '', password: '', domain: '', notes: '' });
    const [busy, setBusy] = useState(false);
    const [showModal, setShowModal] = useState(false);
    const [showChat, setShowChat] = useState(false);

    useEffect(() => {
        api.get(`/buyer/projects/${id}`).then(({ data }) => {
            setProject(data);
            if (data.hostings && data.hostings.length > 0) {
                const h = data.hostings[0];
                setHosting(h);
                setForm({ hosting_type: h.hosting_type, host: h.host, port: h.port, username: h.username, password: h.password || '', domain: h.domain, notes: h.notes });
            }
        }).catch(() => {});
    }, [id]);

    const canProvide = project.delivery_method === 'hosting' && project.status === 'completed';
    const canProvideOrEdit = canProvide && project.status !== 'delivered';
    const showDigitalFiles = project.delivery_method !== 'hosting' && ['completed', 'delivered'].includes(project.status);

    const saveHosting = async (e) => {
        e.preventDefault();
        setBusy(true);
        try {
            const { data } = await api.post(`/buyer/projects/${id}/hosting`, form);
            setHosting(data.hosting);
            setShowModal(false);
            await showDialog({ type: 'success', title: 'Hosting Saved', message: 'Hosting details saved. The seller can now access them using the delivery key.' });
        } catch (err) {
            await showDialog({ type: 'danger', title: 'Failed', message: err.response?.data?.message || 'Failed to save hosting details.' });
        } finally {
            setBusy(false);
        }
    };

    const openForm = () => {
        if (hosting) {
            setForm({ hosting_type: hosting.hosting_type, host: hosting.host, port: hosting.port, username: hosting.username, password: '', domain: hosting.domain, notes: hosting.notes });
        } else {
            setForm({ hosting_type: 'ftp', host: '', port: '', username: '', password: '', domain: '', notes: '' });
        }
        setShowModal(true);
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading page-toolbar" variants={fadeUp} initial="hidden" animate="show">
                <div>
                    <button className="btn btn-secondary btn-sm" onClick={() => navigate('/projects')} style={{ marginBottom: 12 }}>
                        <ArrowLeft size={14} /> Back to Projects
                    </button>
                    <h1>{project.title}</h1>
                </div>
                <span className={`badge ${statusBadgeMap[project.status] || 'badge-info'}`}>{statusLabels[project.status] || project.status?.replace('_', ' ')}</span>
            </motion.div>

            <motion.div className="detail-grid" variants={container} initial="hidden" animate="show">
                <motion.div className="detail-box" variants={fadeUp}>
                    <h3><Layers size={16} style={{ marginRight: 6, verticalAlign: 'middle' }} />Project Information</h3>
                    <div className="detail-row"><span className="label">Seller</span><span className="value">{project.seller?.full_name || '-'}</span></div>
                    <div className="detail-row"><span className="label">Budget</span><span className="value">₹{Number(project.budget).toLocaleString('en-IN')}</span></div>
                    <div className="detail-row"><span className="label">Deadline</span><span className="value">{project.deadline ? new Date(project.deadline).toLocaleDateString() : '-'}</span></div>
                    <div className="detail-row"><span className="label">Delivery</span><span className="value">{project.delivery_method === 'hosting' ? 'Hosting / Handover' : 'Digital'}</span></div>
                    <p style={{ marginTop: 16, color: 'var(--text-secondary)', lineHeight: 1.7 }}>{project.description}</p>
                </motion.div>
                <motion.div className="detail-box" variants={fadeUp}>
                    <h3>Progress</h3>
                    <div className="progress-wrap">
                        <div className="progress-bar"><div className="progress-fill" style={{ width: `${project.progress || 0}%` }} /></div>
                        <span style={{ marginTop: 6 }}>{project.progress || 0}% complete</span>
                    </div>
                    <div style={{ marginTop: 20 }}>
                        <motion.div whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }}>
                            <button className="btn btn-secondary" style={{ width: '100%' }} onClick={() => setShowChat(s => !s)}>
                                <MessageSquare size={16} /> {showChat ? 'Hide Chat' : 'Chat with Seller'}
                            </button>
                        </motion.div>
                    </div>
                    {showChat && (
                        <div style={{ marginTop: 16 }}>
                            <ProjectChat projectId={project.id} me="buyer" />
                        </div>
                    )}
                </motion.div>
            </motion.div>

            {project.delivery_method === 'hosting' && (
                <motion.div className="detail-box" initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5, delay: 0.2 }}>
                    <div className="card-header" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                        <h3 style={{ display: 'flex', alignItems: 'center', gap: 6 }}><Server size={16} /> Hosting Details</h3>
                        {hosting && <span className="badge badge-success" style={{ display: 'flex', alignItems: 'center', gap: 4 }}><CheckCircle2 size={12} /> Submitted</span>}
                    </div>

                    {project.status === 'delivered' && (
                        <p style={{ fontSize: 13, color: 'var(--text-muted)', marginTop: 8 }}>This project is live. Hosting access details were cleared after delivery.</p>
                    )}

                    {canProvideOrEdit && (
                        <>
                            <p style={{ fontSize: 13, color: 'var(--text-muted)', marginTop: 8 }}>
                                Submit your hosting or server access details below. The seller will only see these after verifying the secure delivery key.
                            </p>
                            <div style={{ marginTop: 12 }}>
                                <motion.div whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }}>
                                    <button className="btn btn-primary" onClick={openForm}>
                                        <Server size={15} /> {hosting ? 'Update Hosting Details' : 'Provide Hosting Details'}
                                    </button>
                                </motion.div>
                            </div>
                        </>
                    )}

                    {!canProvide && project.status !== 'delivered' && (
                        <p style={{ fontSize: 13, color: 'var(--text-muted)', marginTop: 8, fontStyle: 'italic' }}>
                            {hosting
                                ? 'Hosting details submitted. Waiting for the project to be completed by the seller before further updates.'
                                : 'This option will be enabled once the seller completes the project.'}
                        </p>
                    )}

                    {hosting && !canProvideOrEdit && project.status !== 'delivered' && (
                        <div style={{ marginTop: 12, padding: '10px 14px', background: 'var(--accent-light, #eef2ff)', borderRadius: 10 }}>
                            <p style={{ fontSize: 13, color: 'var(--text-secondary)' }}>
                                <strong>Submitted server:</strong> {hosting.host} &middot; Username: {hosting.username}
                            </p>
                            <p style={{ fontSize: 12, color: 'var(--text-muted)', marginTop: 4 }}>Seller unlocks the remaining details with the delivery key.</p>
                        </div>
                    )}

                    <style>{`
                        .form-label { display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:var(--text-secondary); }
                        .label { color: var(--text-muted); font-size: 12px; }
                        .detail-grid { display:grid; grid-template-columns: 1fr 1fr; gap: 12px; }
                        .detail-row .label { display:block; font-size: 12px; }
                    `}</style>
                </motion.div>
            )}

            {showDigitalFiles && project.attachments && project.attachments.length > 0 && (
                <motion.div className="detail-box" initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5, delay: 0.3 }}>
                    <h3>Deliverables</h3>
                    {project.attachments.map((f) => (
                        <div key={f.id} className="file-item">
                            <div className="file-info">
                                <FileText size={16} />
                                <span className="file-name">{f.file_name || f.name}</span>
                            </div>
                            <a className="btn btn-secondary btn-sm" href={f.url || f.file_path || '#'} download>Download</a>
                        </div>
                    ))}
                </motion.div>
            )}

            {showDigitalFiles && (!project.attachments || project.attachments.length === 0) && (
                <motion.div className="detail-box" initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5, delay: 0.3 }}>
                    <p style={{ fontSize: 13, color: 'var(--text-muted)', textAlign: 'center', padding: 16 }}>
                        The project is complete. Download deliverable files here once uploaded by the seller.
                    </p>
                </motion.div>
            )}

            <AnimatePresence>
                {showModal && (
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        transition={{ duration: 0.2 }}
                        onClick={() => setShowModal(false)}
                        style={{ position: 'fixed', inset: 0, zIndex: 1000, background: 'rgba(0,0,0,0.45)', backdropFilter: 'blur(4px)', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: 20 }}
                    >
                        <motion.div
                            initial={{ opacity: 0, scale: 0.92, y: 20 }}
                            animate={{ opacity: 1, scale: 1, y: 0 }}
                            exit={{ opacity: 0, scale: 0.95, y: 12 }}
                            transition={{ duration: 0.3, ease: [0.22, 1, 0.36, 1] }}
                            onClick={(e) => e.stopPropagation()}
                            style={{ background: 'var(--bg-card, #fff)', borderRadius: 16, padding: 24, width: '100%', maxWidth: 560, maxHeight: '90vh', overflowY: 'auto', border: '1px solid var(--border-color)' }}
                        >
                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
                                <h3 style={{ display: 'flex', alignItems: 'center', gap: 8, margin: 0 }}><Server size={18} /> {hosting ? 'Update' : 'Provide'} Hosting Details</h3>
                                <button onClick={() => setShowModal(false)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: 'var(--text-muted)' }}><X size={18} /></button>
                            </div>
                            <p style={{ fontSize: 13, color: 'var(--text-muted)', marginBottom: 16 }}>
                                The seller will only see these details after verifying the secure delivery key.
                            </p>
                            <form onSubmit={saveHosting}>
                                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                                    <div>
                                        <label className="form-label">Hosting Type</label>
                                        <select className="form-input" value={form.hosting_type} onChange={(e) => setForm({ ...form, hosting_type: e.target.value })}>
                                            {HOSTING_TYPES.map((t) => <option key={t} value={t} style={{ textTransform: 'capitalize' }}>{t}</option>)}
                                        </select>
                                    </div>
                                    <div>
                                        <label className="form-label">Host / IP Address</label>
                                        <input className="form-input" placeholder="ftp.example.com" value={form.host} onChange={(e) => setForm({ ...form, host: e.target.value })} required />
                                    </div>
                                    <div>
                                        <label className="form-label">Port (optional)</label>
                                        <input className="form-input" placeholder="21" value={form.port} onChange={(e) => setForm({ ...form, port: e.target.value })} />
                                    </div>
                                    <div>
                                        <label className="form-label">Username</label>
                                        <input className="form-input" placeholder="username" value={form.username} onChange={(e) => setForm({ ...form, username: e.target.value })} required />
                                    </div>
                                    <div>
                                        <label className="form-label">Password</label>
                                        <input className="form-input" type="password" placeholder="••••••••" value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} required={!!hosting ? false : true} />
                                    </div>
                                    <div>
                                        <label className="form-label">Domain (optional)</label>
                                        <input className="form-input" placeholder="yoursite.com" value={form.domain} onChange={(e) => setForm({ ...form, domain: e.target.value })} />
                                    </div>
                                </div>
                                <div style={{ marginTop: 12 }}>
                                    <label className="form-label">Notes (optional)</label>
                                    <textarea className="form-input" rows={2} placeholder="Any instructions for the seller…" value={form.notes} onChange={(e) => setForm({ ...form, notes: e.target.value })} />
                                </div>
                                <div style={{ marginTop: 18, display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
                                    <button type="button" className="btn btn-secondary" onClick={() => setShowModal(false)}>Cancel</button>
                                    <button type="submit" className="btn btn-primary" disabled={busy}>{busy ? 'Saving…' : (hosting ? 'Update Details' : 'Save Details')}</button>
                                </div>
                            </form>
                        </motion.div>
                    </motion.div>
                )}
            </AnimatePresence>
        </motion.div>
    );
}

export default ProjectDetail;