import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { ArrowLeft, Banknote, CircleDollarSign } from 'lucide-react';
import { motion } from 'framer-motion';
import api from '../../shared/api';

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    show: { opacity: 1, y: 0, transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] } },
};

function Withdraw() {
    const navigate = useNavigate();
    const [amount, setAmount] = useState('');
    const [method, setMethod] = useState('bank');
    const [account, setAccount] = useState('');
    const [message, setMessage] = useState(null);
    const [processing, setProcessing] = useState(false);

    const submit = async (e) => {
        e.preventDefault();
        setProcessing(true);
        setMessage(null);
        try {
            const { data } = await api.post('/seller/wallet/withdraw', { amount, method, account });
            setMessage({ type: 'success', text: data.message || 'Withdrawal request submitted.' });
            setTimeout(() => navigate('/wallet/withdraw-history'), 800);
        } catch (err) {
            setMessage({ type: 'error', text: err.response?.data?.message || 'Withdrawal failed.' });
        } finally {
            setProcessing(false);
        }
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}>
            <motion.div
                className="page-heading"
                initial={{ opacity: 0, y: -16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, ease: [0.22, 1, 0.36, 1] }}
            >
                <button className="btn btn-secondary btn-sm" onClick={() => navigate('/wallet')} style={{ marginBottom: 12 }}>
                    <ArrowLeft size={14} /> Back to Wallet
                </button>
                <h1 style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <Banknote size={24} style={{ color: 'var(--accent)' }} />
                    Withdraw Funds
                </h1>
                <p>Request a withdrawal to your bank account.</p>
            </motion.div>

            {message && (
                <motion.div
                    className={`alert ${message.type === 'success' ? 'alert-success' : 'alert-error'}`}
                    initial={{ opacity: 0, y: -10 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.35 }}
                >
                    {message.text}
                </motion.div>
            )}

            <motion.div
                className="detail-box"
                variants={fadeUp}
                initial="hidden"
                animate="show"
                style={{ maxWidth: 560 }}
            >
                <form onSubmit={submit}>
                    <div className="form-group">
                        <label style={{ display: 'flex', alignItems: 'center', gap: 6 }}><CircleDollarSign size={14} /> Amount (₹)</label>
                        <input className="form-input" type="number" min="1" step="0.01" value={amount} onChange={(e) => setAmount(e.target.value)} required />
                    </div>
                    <div className="form-group">
                        <label>Withdrawal Method</label>
                        <select className="form-select" value={method} onChange={(e) => setMethod(e.target.value)}>
                            <option value="bank">Bank Transfer</option>
                            <option value="upi">UPI</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                    <div className="form-group">
                        <label>Account Details</label>
                        <textarea className="form-input" value={account} onChange={(e) => setAccount(e.target.value)}
                            placeholder={method === 'bank' ? 'Account number, IFSC, account name' : method === 'upi' ? 'UPI ID' : 'PayPal email'}
                            required />
                    </div>
                    <div style={{ display: 'flex', gap: 10 }}>
                        <motion.button
                            type="submit"
                            className="btn btn-primary"
                            disabled={processing}
                            whileHover={{ scale: 1.02 }}
                            whileTap={{ scale: 0.98 }}
                        >
                            {processing ? 'Processing...' : 'Request Withdrawal'}
                        </motion.button>
                        <Link to="/wallet/withdraw-history" className="btn btn-secondary">Withdraw History</Link>
                    </div>
                </form>
            </motion.div>
        </motion.div>
    );
}

export default Withdraw;
