import { useState, useEffect } from 'react';
import { Eye, CreditCard, Search } from 'lucide-react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import DataTable from '../../shared/components/DataTable';
import FilterSelect from '../../shared/components/FilterSelect';
import api from '../../shared/api';

const typeOptions = [
    { value: '', label: 'All Types' },
    { value: 'payment', label: 'Payment' },
    { value: 'withdrawal', label: 'Withdrawal' },
    { value: 'refund', label: 'Refund' },
    { value: 'commission', label: 'Commission' },
];

const fallbackData = [
    { id: 1, type: 'payment', amount: 500, status: 'completed', description: 'Order #1002', created_at: '2026-08-22' },
    { id: 2, type: 'withdrawal', amount: 300, status: 'completed', description: 'Bank withdrawal', created_at: '2026-08-18' },
    { id: 3, type: 'refund', amount: 80, status: 'pending', description: 'Refund', created_at: '2026-08-15' },
];

function WalletTransactions() {
    const [data, setData] = useState([]);
    const [type, setType] = useState('');

    useEffect(() => {
        api.get('/seller/wallet/transactions', { params: type ? { type } : {} })
            .then(({ data: res }) => setData(Array.isArray(res.data) ? res.data : []))
            .catch(() => setData(fallbackData));
    }, [type]);

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
                        <CreditCard size={24} style={{ color: 'var(--accent)' }} />
                        Transactions
                    </h1>
                    <p>All your wallet transactions.</p>
                </div>
                <div className="toolbar-filters">
                    <FilterSelect
                        value={type}
                        onChange={(e) => setType(e.target.value)}
                        options={typeOptions}
                    />
                </div>
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
                    <h3 style={{ marginBottom: 6 }}>No transactions found</h3>
                    <p style={{ color: 'var(--text-muted)' }}>Transactions will appear here once you start earning.</p>
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
                            { key: 'description', label: 'Description' },
                            { key: 'type', label: 'Type' },
                            { key: 'amount', label: 'Amount' },
                            { key: 'status', label: 'Status' },
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
                                <td>{row.description || '-'}</td>
                                <td><span className="badge badge-info">{row.type}</span></td>
                                <td style={{ color: row.amount < 0 || row.direction === 'debit' ? '#ef4444' : '#22c55e', fontWeight: 600 }}>
                                    ₹{Math.abs(Number(row.amount)).toLocaleString('en-IN')}
                                </td>
                                <td><span className={`badge ${row.status === 'completed' ? 'badge-success' : row.status === 'pending' ? 'badge-warning' : 'badge-danger'}`}>{row.status}</span></td>
                                <td>{row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'}</td>
                                <td><Link to={`/wallet/transaction/${row.id}`} className="btn btn-secondary btn-sm"><Eye size={14} /> View</Link></td>
                            </motion.tr>
                        )}
                    />
                </motion.div>
            )}
        </motion.div>
    );
}

export default WalletTransactions;
