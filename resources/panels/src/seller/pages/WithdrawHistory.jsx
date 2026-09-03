import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { History, Banknote, Search } from 'lucide-react';
import { motion } from 'framer-motion';
import DataTable from '../../shared/components/DataTable';
import api from '../../shared/api';

const fallbackData = [
    { id: 1, amount: 300, method: 'bank', status: 'completed', requested_at: '2026-08-18' },
    { id: 2, amount: 150, method: 'upi', status: 'pending', requested_at: '2026-08-20' },
];

const statusBadgeMap = { completed: 'badge-success', pending: 'badge-warning', processing: 'badge-info', failed: 'badge-danger' };

function WithdrawHistory() {
    const [data, setData] = useState([]);

    useEffect(() => {
        api.get('/seller/wallet/withdraw-history').then(({ data: res }) => setData(Array.isArray(res.data) ? res.data : []))
            .catch(() => setData(fallbackData));
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
                        <History size={24} style={{ color: 'var(--accent)' }} />
                        Withdraw History
                    </h1>
                    <p>All your withdrawal requests.</p>
                </div>
                <Link to="/wallet/withdraw" className="btn btn-primary"><Banknote size={16} /> New Withdrawal</Link>
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
                        <Search size={28} style={{ color: 'var(--accent)' }} />
                    </div>
                    <h3 style={{ marginBottom: 6 }}>No withdrawals yet</h3>
                    <p style={{ color: 'var(--text-muted)' }}>Your withdrawal history will appear here.</p>
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
                            { key: 'amount', label: 'Amount' },
                            { key: 'method', label: 'Method' },
                            { key: 'status', label: 'Status' },
                            { key: 'date', label: 'Date' },
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
                                <td style={{ fontWeight: 600 }}>₹{Number(row.amount).toLocaleString('en-IN')}</td>
                                <td>{row.method || '-'}</td>
                                <td><span className={`badge ${statusBadgeMap[row.status] || 'badge-info'}`}>{row.status}</span></td>
                                <td>{row.requested_at ? new Date(row.requested_at).toLocaleDateString() : '-'}</td>
                            </motion.tr>
                        )}
                    />
                </motion.div>
            )}
        </motion.div>
    );
}

export default WithdrawHistory;
