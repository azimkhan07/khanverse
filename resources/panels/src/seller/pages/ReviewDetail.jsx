import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft, Star, MessageSquareText } from 'lucide-react';
import { motion } from 'framer-motion';
import api from '../../shared/api';

const fallbackReview = {
    id: 1, buyer: { full_name: 'Alice Johnson', avatar: null },
    service: { title: 'Logo Design' }, rating: 5, review: 'Amazing work, delivered on time!',
    created_at: '2026-08-20',
};

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    show: { opacity: 1, y: 0, transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] } },
};

function ReviewDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [review, setReview] = useState(fallbackReview);

    useEffect(() => {
        api.get(`/seller/reviews/${id}`).then(({ data }) => setReview(data)).catch(() => {});
    }, [id]);

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}>
            <motion.div
                className="page-heading"
                initial={{ opacity: 0, y: -16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, ease: [0.22, 1, 0.36, 1] }}
            >
                <button className="btn btn-secondary btn-sm" onClick={() => navigate('/reviews')} style={{ marginBottom: 12 }}>
                    <ArrowLeft size={14} /> Back to Reviews
                </button>
                <h1 style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <MessageSquareText size={24} style={{ color: 'var(--accent)' }} />
                    Review #{review.id}
                </h1>
            </motion.div>

            <motion.div
                className="detail-box"
                variants={fadeUp}
                initial="hidden"
                animate="show"
            >
                <motion.div
                    className="review-card"
                    initial={{ opacity: 0, scale: 0.97 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ duration: 0.5, delay: 0.15, ease: [0.22, 1, 0.36, 1] }}
                >
                    <div className="review-header">
                        <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                            <img
                                src={review.buyer?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(review.buyer?.full_name || 'U')}&background=6d28d9&color=fff`}
                                alt="" style={{ width: 48, height: 48, borderRadius: '50%', objectFit: 'cover' }}
                            />
                            <div>
                                <strong>{review.buyer?.full_name}</strong>
                                <div style={{ fontSize: 13, color: 'var(--text-muted)' }}>
                                    {review.service?.title} · {review.created_at ? new Date(review.created_at).toLocaleDateString() : ''}
                                </div>
                            </div>
                        </div>
                        <div className="review-stars">
                            {Array.from({ length: 5 }).map((_, i) => (
                                <motion.div
                                    key={i}
                                    initial={{ opacity: 0, scale: 0 }}
                                    animate={{ opacity: 1, scale: 1 }}
                                    transition={{ duration: 0.3, delay: 0.3 + i * 0.06, ease: [0.22, 1, 0.36, 1] }}
                                >
                                    <Star size={18} fill={i < review.rating ? 'currentColor' : 'none'} style={{ color: i < review.rating ? '#f59e0b' : 'var(--border)' }} />
                                </motion.div>
                            ))}
                        </div>
                    </div>
                    <p style={{ color: 'var(--text-secondary)', lineHeight: 1.7, marginTop: 8 }}>{review.review}</p>
                </motion.div>
            </motion.div>
        </motion.div>
    );
}

export default ReviewDetail;
