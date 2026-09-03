import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft, Upload, Trash2, ImageIcon } from 'lucide-react';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';

const fallbackService = {
    id: 1, title: 'Logo Design', category: { name: 'Design' }, price: 50,
    delivery_days: 3, revisions: 2, status: 'active', description: 'Professional logo design.',
    thumbnail: null, images: [],
};

const statusBadgeMap = { active: 'badge-success', paused: 'badge-warning', draft: 'badge-info', inactive: 'badge-danger' };

const fadeUp = {
    hidden: { opacity: 0, y: 28 },
    visible: (i = 0) => ({
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, delay: i * 0.1, ease: [0.22, 1, 0.36, 1] },
    }),
};

const stagger = {
    visible: { transition: { staggerChildren: 0.08 } },
};

function ServiceDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [service, setService] = useState(fallbackService);
    const [files, setFiles] = useState([]);
    const [uploading, setUploading] = useState(false);
    const [message, setMessage] = useState(null);

    useEffect(() => {
        api.get(`/seller/services/${id}`).then(({ data }) => {
            setService({
                ...data,
                thumbnail: data.thumbnail ? (data.thumbnail.startsWith('http') ? data.thumbnail : `/storage/${data.thumbnail}`) : null,
                images: (data.images || []).map((img) => ({
                    ...img,
                    image: img.image && !img.image.startsWith('http') ? `/storage/${img.image}` : img.image,
                })),
            });
        }).catch(() => {});
    }, [id]);

    const uploadImages = async () => {
        if (!files.length) return;
        setUploading(true);
        setMessage(null);
        const fd = new FormData();
        Array.from(files).forEach((f) => fd.append('images[]', f));
        try {
            const { data } = await api.post(`/seller/services/${id}/gallery`, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
            setService((prev) => ({ ...prev, images: [...(prev.images || []), ...(data.images || [])] }));
            setFiles([]);
            setMessage({ type: 'success', text: data.message || 'Image(s) uploaded.' });
        } catch (err) {
            setMessage({ type: 'error', text: err.response?.data?.message || 'Upload failed.' });
        } finally {
            setUploading(false);
        }
    };

    const deleteImage = async (imgId) => {
        const ok = await showConfirm({ title: 'Please confirm', message: 'Delete this image?', type: 'danger', confirmText: 'Delete' });
        if (!ok) return;
        try {
            await api.delete(`/seller/services/${id}/gallery/${imgId}`);
            setService((prev) => ({ ...prev, images: (prev.images || []).filter((im) => im.id !== imgId) }));
            setMessage({ type: 'success', text: 'Image deleted.' });
        } catch (err) {
            setMessage({ type: 'error', text: err.response?.data?.message || 'Delete failed.' });
        }
    };

    return (
        <motion.div initial="hidden" animate="visible" variants={stagger}>
            <motion.div className="page-heading page-toolbar" variants={fadeUp}>
                <div>
                    <button className="btn btn-secondary btn-sm" onClick={() => navigate('/services')} style={{ marginBottom: 12 }}>
                        <ArrowLeft size={14} /> Back to Services
                    </button>
                    <h1>{service.title}</h1>
                    <p>Service # {service.id}</p>
                </div>
                <motion.span
                    className={`badge ${statusBadgeMap[service.status] || 'badge-info'}`}
                    whileHover={{ scale: 1.1 }}
                    transition={{ type: 'spring', stiffness: 400, damping: 17 }}
                >
                    {service.status}
                </motion.span>
            </motion.div>

            {message && <motion.div className={`alert ${message.type === 'success' ? 'alert-success' : 'alert-error'}`} initial={{ opacity: 0, y: -10 }} animate={{ opacity: 1, y: 0 }}>{message.text}</motion.div>}

            <motion.div className="detail-grid" variants={fadeUp} custom={1}>
                <motion.div className="detail-box" variants={fadeUp} custom={1}>
                    <h3>Service Information</h3>
                    {service.thumbnail && (
                        <motion.img
                            src={service.thumbnail}
                            alt={service.title}
                            style={{ width: '100%', maxHeight: 200, objectFit: 'cover', borderRadius: 12, marginBottom: 16 }}
                            initial={{ opacity: 0, scale: 0.95 }}
                            animate={{ opacity: 1, scale: 1 }}
                            transition={{ duration: 0.4 }}
                        />
                    )}
                    <div className="detail-row"><span className="label">Category</span><span className="value">{service.category?.name || '-'}</span></div>
                    <div className="detail-row"><span className="label">Price</span><span className="value">â‚¹{Number(service.price).toLocaleString('en-IN')}</span></div>
                    <div className="detail-row"><span className="label">Delivery Time</span><span className="value">{service.delivery_days} Days</span></div>
                    <div className="detail-row"><span className="label">Revisions</span><span className="value">{service.revisions}</span></div>
                </motion.div>
            </motion.div>

            <motion.div className="detail-box" style={{ marginBottom: 24 }} variants={fadeUp} custom={2}>
                <h3>Description</h3>
                <p style={{ whiteSpace: 'pre-line', color: 'var(--text-secondary)', lineHeight: 1.7 }}>
                    {service.description || 'No description provided.'}
                </p>
            </motion.div>

            <motion.div className="detail-box" variants={fadeUp} custom={3}>
                <div className="card-header">
                    <h3>Gallery</h3>
                    <div style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                        <input type="file" accept="image/*" multiple onChange={(e) => setFiles(e.target.files)} style={{ fontSize: 13 }} />
                        <motion.button
                            className="btn btn-primary btn-sm"
                            onClick={uploadImages}
                            disabled={uploading || !files.length}
                            whileHover={{ scale: 1.05 }}
                            whileTap={{ scale: 0.95 }}
                        >
                            <Upload size={14} /> {uploading ? 'Uploading...' : 'Upload'}
                        </motion.button>
                    </div>
                </div>
                {(!service.images || service.images.length === 0) ? (
                    <motion.div
                        className="empty-state"
                        initial={{ opacity: 0, scale: 0.9 }}
                        animate={{ opacity: 1, scale: 1 }}
                        transition={{ delay: 0.2, duration: 0.4 }}
                    >
                        <ImageIcon size={48} style={{ color: 'var(--text-muted)', marginBottom: 12 }} strokeWidth={1.2} />
                        <p>No gallery images found.</p>
                    </motion.div>
                ) : (
                    <motion.div
                        style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(150px, 1fr))', gap: 16 }}
                        initial="hidden"
                        animate="visible"
                        variants={stagger}
                    >
                        {service.images.map((img) => (
                            <motion.div
                                key={img.id}
                                variants={fadeUp}
                                whileHover={{ scale: 1.04, transition: { duration: 0.25 } }}
                                style={{ position: 'relative', borderRadius: 12, overflow: 'hidden', cursor: 'pointer' }}
                                onMouseEnter={(e) => { e.currentTarget.querySelector('.gallery-delete').style.opacity = '1'; }}
                                onMouseLeave={(e) => { e.currentTarget.querySelector('.gallery-delete').style.opacity = '0'; }}
                            >
                                <img
                                    src={img.image || img.path || img.url}
                                    alt=""
                                    style={{ width: '100%', height: 120, objectFit: 'cover', borderRadius: 12, display: 'block' }}
                                />
                                <button
                                    className="btn btn-danger btn-sm gallery-delete"
                                    onClick={() => deleteImage(img.id)}
                                    style={{ position: 'absolute', top: 8, right: 8, padding: 6, opacity: 0, transition: 'opacity 0.2s ease' }}
                                >
                                    <Trash2 size={14} />
                                </button>
                            </motion.div>
                        ))}
                    </motion.div>
                )}
            </motion.div>
        </motion.div>
    );
}

export default ServiceDetail;
