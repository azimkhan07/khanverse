import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Wallet as WalletIcon, CreditCard, ArrowDownRight, Plus, Receipt } from 'lucide-react';
import { Link } from 'react-router-dom';
import StatCard from '../../shared/components/StatCard';
import api from '../../shared/api';

const fallbackTx = [
    { id: 1, remarks: 'Wallet deposit', type: 'deposit', credit: 500, debit: 0, created_at: '2026-08-16' },
    { id: 2, remarks: 'Payment for Logo Design', type: 'payment', credit: 0, debit: 120, created_at: '2026-08-15' },
];

const container = {
    hidden: {},
    show: { transition: { staggerChildren: 0.06 } },
};

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

const slideIn = {
    hidden: { opacity: 0, x: -16 },
    show: { opacity: 1, x: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

function WalletPage() {
    const [wallet, setWallet] = useState({ balance: 0 });
    const [recent, setRecent] = useState([]);

    useEffect(() => {
        api.get('/buyer/wallet').then(({ data }) => setWallet(data.balance !== undefined ? data : { balance: 0 })).catch(() => {});
        api.get('/buyer/wallet/transactions').then(({ data: res }) => {
            const list = Array.isArray(res.data) ? res.data : (res.data || []);
            if (list.length) setRecent(list.slice(0, 5));
        }).catch(() => setRecent(fallbackTx));
    }, []);

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading page-toolbar" variants={fadeUp} initial="hidden" animate="show">
                <div>
                    <h1><WalletIcon size={22} style={{ marginRight: 8, verticalAlign: 'middle' }} />Wallet</h1>
                    <p>Your balance and transactions.</p>
                </div>
                <motion.div whileHover={{ scale: 1.04 }} whileTap={{ scale: 0.96 }}>
                    <Link to="/wallet/deposit" className="btn btn-primary"><Plus size={16} /> Deposit</Link>
                </motion.div>
            </motion.div>

            <motion.div className="stat-grid" variants={container} initial="hidden" animate="show">
                <StatCard icon={<WalletIcon />} label="Balance" value={`â‚¹${Number(wallet.balance || 0).toLocaleString('en-IN')}`} color="green" index={0} />
                <StatCard icon={<CreditCard />} label="Deposits" value="Deposit" color="blue" index={1} />
            </motion.div>

            <motion.div className="detail-box" initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5, delay: 0.2 }}>
                <div className="card-header">
                    <h3><Receipt size={16} style={{ marginRight: 6, verticalAlign: 'middle' }} />Recent Transactions</h3>
                </div>
                {recent.length === 0 ? (
                    <div className="empty-state" style={{ padding: '32px 16px' }}>
                        <CreditCard size={36} style={{ opacity: 0.3, marginBottom: 8 }} />
                        <p style={{ color: 'var(--text-muted)' }}>No transactions yet.</p>
                    </div>
                ) : (
                    <motion.div variants={container} initial="hidden" animate="show">
                        {recent.map((t) => {
                            const isCredit = t.credit > 0;
                            const amt = isCredit ? t.credit : t.debit;
                            return (
                                <motion.div key={t.id} className="detail-row" variants={slideIn}>
                                    <span className="label">
                                        <strong>{t.remarks || t.type}</strong>
                                        <br /><span style={{ color: 'var(--text-muted)', fontSize: 12 }}>{t.type} · {t.created_at ? new Date(t.created_at).toLocaleDateString() : ''}</span>
                                    </span>
                                    <span className="value" style={{ display: 'flex', gap: 6, alignItems: 'center', color: isCredit ? '#22c55e' : '#ef4444' }}>
                                        {!isCredit && <ArrowDownRight size={14} />}
                                        {isCredit ? '+' : '-'}â‚¹{Number(amt).toLocaleString('en-IN')}
                                    </span>
                                </motion.div>
                            );
                        })}
                    </motion.div>
                )}
            </motion.div>
        </motion.div>
    );
}

export default WalletPage;
