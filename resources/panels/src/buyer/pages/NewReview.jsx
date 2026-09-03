import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft, Star, PenLine } from 'lucide-react';
import api from '../../shared/api';

const container = {
    hidden: {},
    show: { transition: { staggerChildren: 0.08 } },
};

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

const starBounce = (i) => ({
    hidden: { opacity: 0, scale: 0.3 },
    show: { opacity: 1, scale: 1, transition: { duration: 0.35, delay: 0.2 + i * 0.07, type: 'spring', stiffness: 300, damping: 15 } },
});

function NewReview() {
    const { orderId } = useParams();
    const navigate = useNavigate();
    const [order, setOrder] = useState(null);
    const [rating, setRating] = useState(5);
    const [hover, setHover] = useState(0);
    const [review, setReview] = useState('');
    const [message, setMessage] = useState(null);
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        if (orderId) {
            api.get(`/buyer/orders/${orderId}`).then(({ data }) => setOrder(data)).catch(() => {});
        }
    }, [orderId]);

    const submit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        setMessage(null);
        try {
            await api.post('/buyer/reviews', {
                order_id: orderId,
                seller_id: order?.seller?.id,
                rating,
                review,
            });
            setMessage({ type: 'success', text: 'Review submitted! Redirecting...' });
            setTimeout(() => navigate('/reviews'), 1200);
        } catch (err) {
            setMessage({ type: 'error', text: err.response?.data?.errors?.review?.[0] || err.response?.data?.message || 'Submission failed.' });
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading" variants={fadeUp} initial="hidden" animate="show">
                <button className="btn btn-secondary btn-sm" onClick={() => navigate('/reviews')} style={{ marginBottom: 12 }}>
                    <ArrowLeft size={14} /> Back to Reviews
                </button>
                <h1><PenLine size={22} style={{ marginRight: 8, verticalAlign: 'middle' }} />Write a Review</h1>
                <p>{order?.service?.title ? `Reviewing: ${order.service.title}` : ''}</p>
            </motion.div>

            {message && <motion.div className={`alert ${message.type === 'success' ? 'alert-success' : 'alert-error'}`} initial={{ opacity: 0, y: -10 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.3 }}>{message.text}</motion.div>}

            <motion.div className="detail-box" style={{ maxWidth: 560 }} variants={container} initial="hidden" animate="show">
                <form onSubmit={submit}>
                    <motion.div className="form-group" variants={fadeUp}>
                        <label>Your Rating</label>
                        <div className="review-stars" style={{ gap: 6 }}>
                            {Array.from({ length: 5 }).map((_, i) => {
                                const value = i + 1;
                                const active = (hover || rating) >= value;
                                return (
                                    <motion.button
                                        type="button"
                                        key={i}
                                        custom={i}
                                        variants={starBounce(i)}
                                        onMouseEnter={() => setHover(value)}
                                        onMouseLeave={() => setHover(0)}
                                        onClick={() => setRating(value)}
                                        whileHover={{ scale: 1.25 }}
                                        whileTap={{ scale: 0.85 }}
                                        style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 2 }}
                                    >
                                        <Star
                                            size={28}
                                            fill={active ? 'currentColor' : 'none'}
                                            style={{ color: active ? '#f59e0b' : 'var(--border-color)', transition: 'color 0.15s' }}
                                        />
                                    </motion.button>
                                );
                            })}
                        </div>
                    </motion.div>
                    <motion.div className="form-group" variants={fadeUp}>
                        <label>Review</label>
                        <textarea className="form-input" rows="5" value={review} onChange={(e) => setReview(e.target.value)} placeholder="Share your experience..." required />
                    </motion.div>
                    <motion.div variants={fadeUp}>
                        <motion.div whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }}>
                            <button type="submit" className="btn btn-primary" disabled={submitting}>
                                {submitting ? 'Submitting...' : 'Submit Review'}
                            </button>
                        </motion.div>
                    </motion.div>
                </form>
            </motion.div>
        </motion.div>
    );
}

export default NewReview;
