import { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { ArrowLeft, PackageCheck, CheckCircle2, KeyRound } from 'lucide-react';
import { motion } from 'framer-motion';
import api from '../../shared/api';
import { showDialog } from '../../shared/components/Dialog';

function DeliveryForm() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [project, setProject] = useState(null);
    const [keyVerified, setKeyVerified] = useState(false);
    const [form, setForm] = useState({ title: '', description: '', delivery_notes: '' });
    const [keyInput, setKeyInput] = useState('');
    const [busy, setBusy] = useState(false);
    const [verifying, setVerifying] = useState(false);

    useEffect(() => {
        api.get(`/seller/projects/${id}`).then(({ data }) => {
            setProject(data);
            setKeyVerified(!!data.key_verified || data.delivery_method === 'digital');
        }).catch(() => navigate('/projects'));
    }, [id, navigate]);

    const verifyKey = async () => {
        if (!keyInput.trim()) return;
        setVerifying(true);
        try {
            const { data } = await api.post(`/seller/projects/${id}/verify-key`, { key: keyInput });
            setKeyVerified(true);
            setKeyInput('');
            await showDialog({ type: 'success', title: 'Key Verified', message: data.message });
        } catch (err) {
            await showDialog({ type: 'danger', title: 'Verification Failed', message: err.response?.data?.message || 'Invalid key.' });
        } finally {
            setVerifying(false);
        }
    };

    const submit = async (e) => {
        e.preventDefault();
        if (!form.title.trim()) return;
        setBusy(true);
        try {
            const { data } = await api.post(`/seller/projects/${id}/deliver`, form);
            await showDialog({ type: 'success', title: 'Delivery Submitted', message: data.message });
            navigate(`/projects/${id}`);
        } catch (err) {
            await showDialog({ type: 'danger', title: 'Failed', message: err.response?.data?.message || 'Failed to submit delivery.' });
        } finally {
            setBusy(false);
        }
    };

    if (!project) {
        return (
            <motion.div
                className="empty-state"
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.5 }}
                style={{ padding: '80px 20px', textAlign: 'center' }}
            >
                <div style={{ width: 48, height: 48, borderRadius: '50%', background: 'var(--accent-light, #eef2ff)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 12px' }}>
                    <PackageCheck size={22} style={{ color: 'var(--accent)' }} />
                </div>
                <p>Loading…</p>
            </motion.div>
        );
    }

    const requiresKey = project.delivery_method === 'hosting' && !keyVerified;

    return (
        <motion.div className="kv-form-page" initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}>
            <motion.div
                className="page-heading page-toolbar"
                initial={{ opacity: 0, y: -16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, ease: [0.22, 1, 0.36, 1] }}
            >
                <div>
                    <Link to={`/projects/${id}`} className="btn btn-secondary btn-sm" style={{ marginBottom: 12 }}>
                        <ArrowLeft size={14} /> Back to Project
                    </Link>
                    <h1>Submit Delivery</h1>
                </div>
                <span className="badge badge-primary" style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
                    <PackageCheck size={13} /> {project.title}
                </span>
            </motion.div>

            <motion.div
                className="kv-form-hero"
                initial={{ opacity: 0, y: 16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: 0.1, ease: [0.22, 1, 0.36, 1] }}
            >
                <h2>{requiresKey ? 'Verify delivery key first' : 'Hand over the completed work'}</h2>
                <p>{requiresKey ? 'This hosting project requires key verification before delivery.' : 'This submission notifies the buyer for review.'}</p>
            </motion.div>

            {requiresKey && (
                <motion.div
                    className="kv-form-card"
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5, delay: 0.15, ease: [0.22, 1, 0.36, 1] }}
                    style={{ marginBottom: 20 }}
                >
                    <h3 style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 14 }}><KeyRound size={18} /> Unlock Hosting Access</h3>
                    <p style={{ fontSize: 13, color: 'var(--text-muted)', marginBottom: 14 }}>
                        You verified this key earlier or need to enter it to unlock the buyer&apos;s hosting credentials before delivering.
                    </p>
                    <div className="status-form">
                        <input
                            className="form-input"
                            placeholder="XXXX-XXXX-XXXX"
                            value={keyInput}
                            onChange={(e) => setKeyInput(e.target.value)}
                            style={{ textTransform: 'uppercase' }}
                        />
                        <button className="btn btn-primary" onClick={verifyKey} disabled={!keyInput.trim() || verifying}>
                            {verifying ? 'Verifying…' : 'Verify Key'}
                        </button>
                    </div>
                </motion.div>
            )}

            {keyVerified && (
                <motion.form
                    onSubmit={submit}
                    className="kv-form-card"
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5, delay: 0.2, ease: [0.22, 1, 0.36, 1] }}
                >
                    <motion.div
                        initial={{ opacity: 0, scale: 0.95 }}
                        animate={{ opacity: 1, scale: 1 }}
                        transition={{ duration: 0.4, delay: 0.3 }}
                        style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 18, color: 'var(--success)' }}
                    >
                        <CheckCircle2 size={16} /> <strong style={{ fontSize: 14 }}>Access unlocked — ready to deliver</strong>
                    </motion.div>
                    <div className="kv-form-grid">
                        <div className="kv-field full">
                            <label>Title</label>
                            <input className="form-input" placeholder="e.g. Final delivery — Website v1" value={form.title}
                                onChange={(e) => setForm({ ...form, title: e.target.value })} required />
                        </div>
                        <div className="kv-field full">
                            <label>Description</label>
                            <textarea className="form-input" rows={4} placeholder="What was delivered" value={form.description}
                                onChange={(e) => setForm({ ...form, description: e.target.value })} />
                        </div>
                        <div className="kv-field full">
                            <label>Delivery Notes</label>
                            <textarea className="form-input" rows={3} placeholder="Notes for the buyer" value={form.delivery_notes}
                                onChange={(e) => setForm({ ...form, delivery_notes: e.target.value })} />
                        </div>
                    </div>
                    <div className="kv-form-actions">
                        <Link to={`/projects/${id}`} className="btn btn-secondary">Cancel</Link>
                        <motion.button
                            type="submit"
                            className="btn btn-primary"
                            disabled={busy}
                            whileHover={{ scale: 1.02 }}
                            whileTap={{ scale: 0.98 }}
                        >
                            <PackageCheck size={15} /> {busy ? 'Submitting…' : 'Submit Delivery'}
                        </motion.button>
                    </div>
                </motion.form>
            )}
        </motion.div>
    );
}

export default DeliveryForm;
