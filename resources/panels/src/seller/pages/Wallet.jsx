import { useState, useEffect } from 'react';
import { DollarSign, CreditCard, Wallet as WalletIcon, ArrowDownRight, ArrowUpRight, WalletMinimal } from 'lucide-react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import StatCard from '../../shared/components/StatCard';
import api from '../../shared/api';

const fallbackWallet = { balance: 1250.75, pending: 450.0, total_earned: 12450.0 };

const fallbackTx = [
    { id: 1, type: 'payment', amount: 500, status: 'completed', description: 'Order #1002', created_at: '2026-08-22', direction: 'credit' },
    { id: 2, type: 'withdrawal', amount: -300, status: 'completed', description: 'Bank withdrawal', created_at: '2026-08-18', direction: 'debit' },
];

const stagger = {
    hidden: { opacity: 0 },
    show: { opacity: 1, transition: { staggerChildren: 0.06 } },
};

const item = {
    hidden: { opacity: 0, y: 12 },
    show: { opacity: 1, y: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

function WalletPage() {
    const [wallet, setWallet] = useState(fallbackWallet);
    const [recent, setRecent] = useState(fallbackTx);

    useEffect(() => {
        api.get('/seller/wallet').then(({ data }) => setWallet(data)).catch(() => {});
        api.get('/seller/wallet/transactions').then(({ data: res }) => {
            const list = Array.isArray(res.data) ? res.data : (res.data || []);
            if (list.length) setRecent(list.slice(0, 5));
        }).catch(() => {});
    }, []);

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}>
            <motion.div
                className="page-heading page-toolbar"
                initial={{ opacity: 0, y: -16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, ease: [0.22, 1, 0.36, 1] }}
            >
                <div>
                    <h1 style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                        <WalletMinimal size={24} style={{ color: 'var(--accent)' }} />
                        Wallet
                    </h1>
                    <p>Track your earnings and withdrawals.</p>
                </div>
                <div style={{ display: 'flex', gap: 10 }}>
                    <Link to="/wallet/withdraw" className="btn btn-primary"><DollarSign size={16} /> Withdraw</Link>
                    <Link to="/wallet/transactions" className="btn btn-secondary"><CreditCard size={16} /> Transactions</Link>
                </div>
            </motion.div>

            <div className="stat-grid">
                <StatCard icon={<WalletIcon />} label="Available Balance" value={`₹${Number(wallet.balance || 0).toLocaleString('en-IN')}`} color="green" index={0} />
                <StatCard icon={<CreditCard />} label="Pending" value={`₹${Number(wallet.pending || 0).toLocaleString('en-IN')}`} color="orange" index={1} />
                <StatCard icon={<DollarSign />} label="Total Earned" value={`₹${Number(wallet.total_earned || 0).toLocaleString('en-IN')}`} color="blue" index={2} />
            </div>

            <motion.div
                className="detail-box"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: 0.2, ease: [0.22, 1, 0.36, 1] }}
            >
                <div className="card-header">
                    <h3>Recent Transactions</h3>
                    <Link to="/wallet/transactions" className="btn btn-secondary btn-sm">View All</Link>
                </div>
                {recent.length === 0 ? (
                    <motion.div
                        className="empty-state"
                        initial={{ opacity: 0, scale: 0.9 }}
                        animate={{ opacity: 1, scale: 1 }}
                        transition={{ duration: 0.5, delay: 0.3 }}
                        style={{ padding: '48px 20px', textAlign: 'center' }}
                    >
                        <div style={{ width: 64, height: 64, borderRadius: '50%', background: 'var(--accent-light, #eef2ff)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 16px' }}>
                            <CreditCard size={28} style={{ color: 'var(--accent)' }} />
                        </div>
                        <h3 style={{ marginBottom: 6 }}>No transactions yet</h3>
                        <p style={{ color: 'var(--text-muted)' }}>Your earnings and withdrawals will show up here.</p>
                    </motion.div>
                ) : (
                    <motion.div variants={stagger} initial="hidden" animate="show">
                        {recent.map((t) => (
                            <motion.div key={t.id} className="detail-row" variants={item}>
                                <span className="label">
                                    <strong>{t.description}</strong>
                                    <br /><span style={{ color: 'var(--text-muted)', fontSize: 12 }}>{t.type} · {t.created_at ? new Date(t.created_at).toLocaleDateString() : ''}</span>
                                </span>
                                <span className="value" style={{ display: 'flex', gap: 6, alignItems: 'center', color: t.amount < 0 || t.direction === 'debit' ? '#ef4444' : '#22c55e' }}>
                                    {t.amount < 0 || t.direction === 'debit' ? <ArrowDownRight size={14} /> : <ArrowUpRight size={14} />}
                                    ₹{Math.abs(Number(t.amount)).toLocaleString('en-IN')}
                                </span>
                            </motion.div>
                        ))}
                    </motion.div>
                )}
            </motion.div>
        </motion.div>
    );
}

export default WalletPage;
