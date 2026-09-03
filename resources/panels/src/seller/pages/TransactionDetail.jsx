import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft, Receipt } from 'lucide-react';
import { motion } from 'framer-motion';
import api from '../../shared/api';

const fallbackTx = {
    id: 1, type: 'payment', amount: 500, status: 'completed',
    description: 'Order #1002', created_at: '2026-08-22',
    reference: 'TXN-1001', balance_after: 1250.75,
};

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    show: { opacity: 1, y: 0, transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] } },
};

const stagger = {
    hidden: { opacity: 0 },
    show: { opacity: 1, transition: { staggerChildren: 0.1 } },
};

function TransactionDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [tx, setTx] = useState(fallbackTx);

    useEffect(() => {
        api.get(`/seller/wallet/transaction/${id}`).then(({ data }) => setTx(data)).catch(() => {});
    }, [id]);

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}>
            <motion.div
                className="page-heading"
                initial={{ opacity: 0, y: -16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, ease: [0.22, 1, 0.36, 1] }}
            >
                <button className="btn btn-secondary btn-sm" onClick={() => navigate('/wallet/transactions')} style={{ marginBottom: 12 }}>
                    <ArrowLeft size={14} /> Back to Transactions
                </button>
                <h1 style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <Receipt size={24} style={{ color: 'var(--accent)' }} />
                    Transaction #{tx.id}
                </h1>
            </motion.div>

            <motion.div className="detail-grid" variants={stagger} initial="hidden" animate="show">
                <motion.div className="detail-box" variants={fadeUp}>
                    <h3>Transaction Details</h3>
                    <div className="detail-row"><span className="label">Description</span><span className="value">{tx.description || '-'}</span></div>
                    <div className="detail-row"><span className="label">Type</span><span className="value"><span className="badge badge-info">{tx.type}</span></span></div>
                    <div className="detail-row"><span className="label">Amount</span><span className="value" style={{ color: tx.amount < 0 || tx.direction === 'debit' ? '#ef4444' : '#22c55e', fontWeight: 600 }}>
                        {tx.amount < 0 || tx.direction === 'debit' ? '-' : '+'}₹{Math.abs(Number(tx.amount)).toLocaleString('en-IN')}
                    </span></div>
                </motion.div>
                <motion.div className="detail-box" variants={fadeUp}>
                    <h3>Status</h3>
                    <div className="detail-row"><span className="label">Status</span><span className="value"><span className={`badge ${tx.status === 'completed' ? 'badge-success' : tx.status === 'pending' ? 'badge-warning' : 'badge-danger'}`}>{tx.status}</span></span></div>
                    <div className="detail-row"><span className="label">Reference</span><span className="value">{tx.reference || '-'}</span></div>
                    <div className="detail-row"><span className="label">Date</span><span className="value">{tx.created_at ? new Date(tx.created_at).toLocaleString() : '-'}</span></div>
                    {tx.balance_after != null && <div className="detail-row"><span className="label">Balance After</span><span className="value">₹{Number(tx.balance_after).toLocaleString('en-IN')}</span></div>}
                </motion.div>
            </motion.div>
        </motion.div>
    );
}

export default TransactionDetail;
