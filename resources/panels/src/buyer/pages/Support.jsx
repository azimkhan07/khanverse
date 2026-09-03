import { motion } from 'framer-motion';
import { Info, Headphones, Mail } from 'lucide-react';
import { showDialog } from '../../shared/components/Dialog';

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

function Support() {
    const openInfo = () => {
        showDialog({
            type: 'info',
            title: 'Support & Help',
            message: 'Live chat support is launching soon. For any query regarding your orders, projects or wallet, email us at support@khanverse.com and our team will get back to you within 24 hours.',
        });
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading" variants={fadeUp} initial="hidden" animate="show">
                <h1><Headphones size={22} style={{ marginRight: 8, verticalAlign: 'middle' }} />Support</h1>
                <p>Get help from our support team.</p>
            </motion.div>
            <motion.div
                className="detail-box"
                initial={{ opacity: 0, y: 24 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: 0.15 }}
                style={{ border: '1px solid var(--border)', borderRadius: 16, padding: 24 }}
            >
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 10 }}>
                    <h3 style={{ margin: 0 }}>Need assistance?</h3>
                    <motion.div whileHover={{ scale: 1.04 }} whileTap={{ scale: 0.96 }}>
                        <button className="btn btn-secondary btn-sm" onClick={openInfo}>
                            <Info size={15} /> More Info
                        </button>
                    </motion.div>
                </div>
                <p>Chat with our support team for any queries. This feature is coming soon.</p>
                <div style={{ marginTop: 16, padding: '12px 16px', borderRadius: 12, background: 'rgba(109,40,217,0.06)', display: 'flex', alignItems: 'center', gap: 10 }}>
                    <Mail size={16} style={{ color: 'var(--accent)', flexShrink: 0 }} />
                    <p style={{ margin: 0, color: 'var(--text-muted)' }}>For now, reach us at <strong>support@khanverse.com</strong></p>
                </div>
            </motion.div>
        </motion.div>
    );
}

export default Support;
