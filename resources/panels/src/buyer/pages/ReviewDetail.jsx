import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft, Star, MessageSquare } from 'lucide-react';
import api from '../../shared/api';

const fallbackReview = {
    id: 1, seller: { full_name: 'Sara Ali' }, order: { service: { title: 'Logo Design' } },
    rating: 5, review: 'Great work!', created_at: '2026-08-20',
};

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

const starPop = (i) => ({
    hidden: { opacity: 0, scale: 0.3, rotate: -30 },
    show: { opacity: 1, scale: 1, rotate: 0, transition: { duration: 0.4, delay: 0.3 + i * 0.08, ease: [0.22, 1, 0.36, 1] } },
});

function ReviewDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [review, setReview] = useState(fallbackReview);

    useEffect(() => {
        api.get(`/buyer/reviews/${id}`).then(({ data }) => setReview(data)).catch(() => {});
    }, [id]);

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading" variants={fadeUp} initial="hidden" animate="show">
                <button className="btn btn-secondary btn-sm" onClick={() => navigate('/reviews')} style={{ marginBottom: 12 }}>
                    <ArrowLeft size={14} /> Back to Reviews
                </button>
                <h1><MessageSquare size={22} style={{ marginRight: 8, verticalAlign: 'middle' }} />Review #{review.id}</h1>
            </motion.div>

            <motion.div className="detail-box" initial={{ opacity: 0, y: 24 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5, delay: 0.15 }}>
                <div className="review-card">
                    <div className="review-header">
                        <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                            <motion.img
                                src={`https://ui-avatars.com/api/?name=${encodeURIComponent(review.seller?.full_name || 'S')}&background=6d28d9&color=fff`}
                                alt="" style={{ width: 48, height: 48, borderRadius: '50%', objectFit: 'cover' }}
                                initial={{ opacity: 0, scale: 0.6 }}
                                animate={{ opacity: 1, scale: 1 }}
                                transition={{ duration: 0.4, delay: 0.1, ease: [0.22, 1, 0.36, 1] }}
                            />
                            <div>
                                <strong>{review.seller?.full_name}</strong>
                                <div style={{ fontSize: 13, color: 'var(--text-muted)' }}>
                                    {review.order?.service?.title} · {review.created_at ? new Date(review.created_at).toLocaleDateString() : ''}
                                </div>
                            </div>
                        </div>
                        <div className="review-stars">
                            {Array.from({ length: 5 }).map((_, i) => (
                                <motion.div key={i} custom={i} variants={starPop(i)} initial="hidden" animate="show">
                                    <Star size={18} fill={i < review.rating ? 'currentColor' : 'none'} style={{ color: i < review.rating ? '#f59e0b' : 'var(--border-color)' }} />
                                </motion.div>
                            ))}
                        </div>
                    </div>
                    <motion.p style={{ color: 'var(--text-secondary)', lineHeight: 1.7, marginTop: 8 }} initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ delay: 0.5, duration: 0.4 }}>{review.review}</motion.p>
                </div>
            </motion.div>
        </motion.div>
    );
}

export default ReviewDetail;
