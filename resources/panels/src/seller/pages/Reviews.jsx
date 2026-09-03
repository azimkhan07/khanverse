import { useState, useEffect } from 'react';
import { Eye, Star, MessageSquareText } from 'lucide-react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import DataTable from '../../shared/components/DataTable';
import api from '../../shared/api';

const fallbackData = [
    { id: 1, buyer: { full_name: 'Alice Johnson' }, service: { title: 'Logo Design' }, rating: 5, review: 'Amazing work!', created_at: '2026-08-20' },
    { id: 2, buyer: { full_name: 'Bob Smith' }, service: { title: 'Web Development' }, rating: 4, review: 'Very professional.', created_at: '2026-08-18' },
];

function Reviews() {
    const [data, setData] = useState([]);

    useEffect(() => {
        api.get('/seller/reviews').then(({ data: res }) => setData(Array.isArray(res.data) ? res.data : []))
            .catch(() => setData(fallbackData));
    }, []);

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}>
            <motion.div
                className="page-heading"
                initial={{ opacity: 0, y: -16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, ease: [0.22, 1, 0.36, 1] }}
            >
                <h1 style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <MessageSquareText size={24} style={{ color: 'var(--accent)' }} />
                    Reviews
                </h1>
                <p>See what buyers are saying about your work.</p>
            </motion.div>

            {data.length === 0 ? (
                <motion.div
                    className="empty-state"
                    initial={{ opacity: 0, scale: 0.9 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ duration: 0.5, delay: 0.2 }}
                    style={{ padding: '60px 20px', textAlign: 'center' }}
                >
                    <div style={{ width: 64, height: 64, borderRadius: '50%', background: 'var(--accent-light, #eef2ff)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 16px' }}>
                        <Star size={28} style={{ color: 'var(--accent)' }} />
                    </div>
                    <h3 style={{ marginBottom: 6 }}>No reviews yet</h3>
                    <p style={{ color: 'var(--text-muted)' }}>Reviews from buyers will appear here.</p>
                </motion.div>
            ) : (
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5, delay: 0.1 }}
                >
                    <DataTable
                        columns={[
                            { key: 'id', label: 'ID' },
                            { key: 'buyer', label: 'Buyer' },
                            { key: 'service', label: 'Service' },
                            { key: 'rating', label: 'Rating' },
                            { key: 'date', label: 'Date' },
                            { key: 'action', label: 'Action' },
                        ]}
                        data={data}
                        renderRow={(row, i) => (
                            <motion.tr
                                key={row.id ?? i}
                                initial={{ opacity: 0, x: -12 }}
                                animate={{ opacity: 1, x: 0 }}
                                transition={{ duration: 0.35, delay: i * 0.05, ease: [0.22, 1, 0.36, 1] }}
                            >
                                <td>#{row.id}</td>
                                <td>{row.buyer?.full_name || '-'}</td>
                                <td>{row.service?.title || '-'}</td>
                                <td>
                                    <span style={{ display: 'inline-flex', alignItems: 'center', gap: 4, color: 'var(--warning, #f59e0b)' }}>
                                        {row.rating} <Star size={14} />
                                    </span>
                                </td>
                                <td>{row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'}</td>
                                <td><Link to={`/reviews/${row.id}`} className="btn btn-secondary btn-sm"><Eye size={14} /> View</Link></td>
                            </motion.tr>
                        )}
                    />
                </motion.div>
            )}
        </motion.div>
    );
}

export default Reviews;
