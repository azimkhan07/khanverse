import { useState } from 'react';
import { motion } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
import { ArrowLeft, Wallet, CreditCard } from 'lucide-react';
import api from '../../shared/api';

const container = {
    hidden: {},
    show: { transition: { staggerChildren: 0.08 } },
};

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

function WalletDeposit() {
    const navigate = useNavigate();
    const [amount, setAmount] = useState('');
    const [payment, setPayment] = useState('card');
    const [message, setMessage] = useState(null);
    const [processing, setProcessing] = useState(false);

    const submit = async (e) => {
        e.preventDefault();
        setProcessing(true);
        setMessage(null);
        try {
            await api.post('/buyer/wallet/deposit', { amount });
            setMessage({ type: 'success', text: 'Deposit successful!' });
            setTimeout(() => navigate('/wallet'), 1000);
        } catch (err) {
            setMessage({ type: 'error', text: err.response?.data?.message || 'Deposit failed.' });
        } finally {
            setProcessing(false);
        }
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading" variants={fadeUp} initial="hidden" animate="show">
                <button className="btn btn-secondary btn-sm" onClick={() => navigate('/wallet')} style={{ marginBottom: 12 }}>
                    <ArrowLeft size={14} /> Back to Wallet
                </button>
                <h1><Wallet size={22} style={{ marginRight: 8, verticalAlign: 'middle' }} />Deposit Funds</h1>
                <p>Add money to your wallet.</p>
            </motion.div>

            {message && <motion.div className={`alert ${message.type === 'success' ? 'alert-success' : 'alert-error'}`} initial={{ opacity: 0, y: -10 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.3 }}>{message.text}</motion.div>}

            <motion.div className="detail-box" style={{ maxWidth: 560 }} variants={container} initial="hidden" animate="show">
                <form onSubmit={submit}>
                    <motion.div className="form-group" variants={fadeUp}>
                        <label>Amount (â‚¹)</label>
                        <input className="form-input" type="number" min="1" step="0.01" value={amount} onChange={(e) => setAmount(e.target.value)} required />
                    </motion.div>
                    <motion.div className="form-group" variants={fadeUp}>
                        <label>Payment Method</label>
                        <select className="form-select" value={payment} onChange={(e) => setPayment(e.target.value)}>
                            <option value="card">Credit / Debit Card</option>
                            <option value="upi">UPI</option>
                            <option value="netbanking">Net Banking</option>
                        </select>
                    </motion.div>
                    <motion.div variants={fadeUp}>
                        <motion.div whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }}>
                            <button type="submit" className="btn btn-primary" disabled={processing}>
                                <CreditCard size={16} style={{ marginRight: 6 }} />
                                {processing ? 'Processing...' : `Deposit â‚¹${Number(amount || 0).toLocaleString('en-IN')}`}
                            </button>
                        </motion.div>
                    </motion.div>
                </form>
            </motion.div>
        </motion.div>
    );
}

export default WalletDeposit;
