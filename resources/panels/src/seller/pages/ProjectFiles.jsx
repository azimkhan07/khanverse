import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft, Upload, Trash2, Download, FileIcon, FolderOpen } from 'lucide-react';
import { motion } from 'framer-motion';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';

const fallbackFiles = [
    { id: 1, name: 'wireframes.fig', size: 2048, uploaded_at: '2026-08-21' },
    { id: 2, name: 'final-assets.zip', size: 12800, uploaded_at: '2026-08-23' },
];

function fmtSize(bytes) {
    if (!bytes) return '-';
    if (bytes > 1024 * 1024) return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    return `${Math.round(bytes / 1024)} KB`;
}

const stagger = {
    hidden: { opacity: 0 },
    show: { opacity: 1, transition: { staggerChildren: 0.06 } },
};

const item = {
    hidden: { opacity: 0, y: 16 },
    show: { opacity: 1, y: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

function ProjectFiles() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [files, setFiles] = useState(fallbackFiles);
    const [selected, setSelected] = useState([]);
    const [uploading, setUploading] = useState(false);
    const [message, setMessage] = useState(null);

    useEffect(() => {
        api.get(`/seller/projects/${id}/attachments`).then(({ data }) => {
            setFiles(Array.isArray(data) ? data : (data.attachments || []));
        }).catch(() => {});
    }, [id]);

    const uploadFiles = async () => {
        if (!selected.length) return;
        setUploading(true);
        setMessage(null);
        const fd = new FormData();
        Array.from(selected).forEach((f) => fd.append('attachments[]', f));
        try {
            await api.post(`/seller/projects/${id}/attachments/upload`, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
            const fresh = await api.get(`/seller/projects/${id}/attachments`);
            setFiles(Array.isArray(fresh.data) ? fresh.data : (fresh.data.attachments || []));
            setSelected([]);
            setMessage({ type: 'success', text: 'Files uploaded.' });
        } catch (err) {
            setMessage({ type: 'error', text: err.response?.data?.message || 'Upload failed.' });
        } finally {
            setUploading(false);
        }
    };

    const deleteFile = async (fileId) => {
        const ok = await showConfirm({ title: 'Please confirm', message: 'Delete this file?', type: 'danger', confirmText: 'Delete' });
        if (!ok) return;
        await api.delete(`/seller/projects/attachment/${fileId}`);
        setFiles((prev) => prev.filter((f) => f.id !== fileId));
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}>
            <motion.div
                className="page-heading page-toolbar"
                initial={{ opacity: 0, y: -16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, ease: [0.22, 1, 0.36, 1] }}
            >
                <div>
                    <button className="btn btn-secondary btn-sm" onClick={() => navigate(`/projects/${id}`)} style={{ marginBottom: 12 }}>
                        <ArrowLeft size={14} /> Back to Project
                    </button>
                    <h1 style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                        <FolderOpen size={24} style={{ color: 'var(--accent)' }} />
                        Project Files #{id}
                    </h1>
                </div>
                <div style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                    <input type="file" multiple onChange={(e) => setSelected(e.target.files)} style={{ fontSize: 13 }} />
                    <button className="btn btn-primary btn-sm" onClick={uploadFiles} disabled={uploading || !selected.length}>
                        <Upload size={14} /> {uploading ? 'Uploading...' : 'Upload'}
                    </button>
                </div>
            </motion.div>

            {message && (
                <motion.div
                    className={`alert ${message.type === 'success' ? 'alert-success' : 'alert-error'}`}
                    initial={{ opacity: 0, y: -10 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.35 }}
                >
                    {message.text}
                </motion.div>
            )}

            <motion.div
                className="detail-box"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: 0.1, ease: [0.22, 1, 0.36, 1] }}
            >
                {files.length === 0 ? (
                    <motion.div
                        className="empty-state"
                        initial={{ opacity: 0, scale: 0.9 }}
                        animate={{ opacity: 1, scale: 1 }}
                        transition={{ duration: 0.5, delay: 0.2 }}
                        style={{ padding: '48px 20px', textAlign: 'center' }}
                    >
                        <div style={{ width: 64, height: 64, borderRadius: '50%', background: 'var(--accent-light, #eef2ff)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 16px' }}>
                            <Upload size={28} style={{ color: 'var(--accent)' }} />
                        </div>
                        <h3 style={{ marginBottom: 6 }}>No files uploaded yet</h3>
                        <p style={{ color: 'var(--text-muted)' }}>Upload project files to share with the buyer.</p>
                    </motion.div>
                ) : (
                    <motion.div variants={stagger} initial="hidden" animate="show">
                        {files.map((f) => (
                            <motion.div
                                key={f.id}
                                className="file-item"
                                variants={item}
                                whileHover={{ backgroundColor: 'var(--hover-bg, rgba(0,0,0,0.02))', transition: { duration: 0.15 } }}
                                style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '12px 8px', borderBottom: '1px solid var(--border-color)', gap: 12 }}
                            >
                                <div className="file-info" style={{ display: 'flex', alignItems: 'center', gap: 10, flex: 1 }}>
                                    <div style={{ width: 36, height: 36, borderRadius: 8, background: 'var(--accent-light, #eef2ff)', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                                        <FileIcon size={18} style={{ color: 'var(--accent)' }} />
                                    </div>
                                    <div>
                                        <span className="file-name" style={{ fontWeight: 600, fontSize: 14 }}>{f.name || f.original_name || `File ${f.id}`}</span>
                                        <div className="file-meta" style={{ fontSize: 12, color: 'var(--text-muted)' }}>{fmtSize(f.size)} · {f.uploaded_at ? new Date(f.uploaded_at).toLocaleDateString() : '-'}</div>
                                    </div>
                                </div>
                                <div style={{ display: 'flex', gap: 6 }}>
                                    <a className="btn btn-secondary btn-sm" href={f.url || f.path || '#'} download><Download size={14} /></a>
                                    <button className="btn btn-danger btn-sm" onClick={() => deleteFile(f.id)}><Trash2 size={14} /></button>
                                </div>
                            </motion.div>
                        ))}
                    </motion.div>
                )}
            </motion.div>
        </motion.div>
    );
}

export default ProjectFiles;
