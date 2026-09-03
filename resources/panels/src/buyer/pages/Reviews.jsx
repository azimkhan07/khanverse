import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Eye, Star, Plus, MessageSquare } from 'lucide-react';
import { Link, useNavigate } from 'react-router-dom';
import DataTable from '../../shared/components/DataTable';
import api from '../../shared/api';

const fallbackData = [
    { id: 1, seller: { full_name: 'Sara Ali' }, order: { id: 1001, service: { title: 'Logo Design' } }, rating: 5, review: 'Great work!', created_at: '2026-08-20' },
    { id: 2, seller: { full_name: 'Usman Tariq' }, order: { id: 1002, service: { title: 'SEO Audit' } }, rating: 4, review: 'Good.' , created_at: '2026-08-22' },
];

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

const rowVariant = {
    hidden: { opacity: 0, y: 12 },
    show: (i) => ({ opacity: 1, y: 0, transition: { duration: 0.35, delay: i * 0.04, ease: [0.22, 1, 0.36, 1] } }),
};

function Reviews() {
    const navigate = useNavigate();
    const [data, setData] = useState([]);

    useEffect(() => {
        api.get('/buyer/reviews').then(({ data: res }) => setData(Array.isArray(res.data) ? res.data : []))
            .catch(() => setData(fallbackData));
    }, []);

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading page-toolbar" variants={fadeUp} initial="hidden" animate="show">
                <div>
                    <h1><MessageSquare size={22} style={{ marginRight: 8, verticalAlign: 'middle' }} />My Reviews</h1>
                    <p>Reviews you've written for sellers.</p>
                </div>
                <motion.div whileHover={{ scale: 1.04 }} whileTap={{ scale: 0.96 }}>
                    <button className="btn btn-primary" onClick={() => navigate('/orders')}><Plus size={16} /> Review an Order</button>
                </motion.div>
            </motion.div>

            <DataTable
                columns={[
                    { key: 'id', label: 'ID' },
                    { key: 'seller', label: 'Seller' },
                    { key: 'service', label: 'Service' },
                    { key: 'rating', label: 'Rating' },
                    { key: 'date', label: 'Date' },
                    { key: 'action', label: 'Action' },
                ]}
                data={data}
                renderRow={(row, i) => (
                    <motion.tr key={row.id ?? i} custom={i} variants={rowVariant} initial="hidden" animate="show">
                        <td>#{row.id}</td>
                        <td>{row.seller?.full_name || '-'}</td>
                        <td>{row.order?.service?.title || '-'}</td>
                        <td><span style={{ display: 'inline-flex', alignItems: 'center', gap: 4, color: '#f59e0b' }}>{row.rating} <Star size={14} /></span></td>
                        <td>{row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'}</td>
                        <td><Link to={`/reviews/${row.id}`} className="btn btn-secondary btn-sm"><Eye size={14} /> View</Link></td>
                    </motion.tr>
                )}
            />
        </motion.div>
    );
}

export default Reviews;
