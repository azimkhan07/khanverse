import { motion } from 'framer-motion';

function StatCard({ icon, label, value, color = 'blue', index = 0 }) {
    return (
        <motion.div
            className="stat-card"
            initial={{ opacity: 0, y: 24, scale: 0.96 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            transition={{ duration: 0.45, delay: index * 0.08, ease: [0.22, 1, 0.36, 1] }}
            whileHover={{ y: -6, scale: 1.02, transition: { duration: 0.25 } }}
        >
            <div className={`stat-icon ${color}`}>{icon}</div>
            <h3>{value}</h3>
            <p>{label}</p>
        </motion.div>
    );
}

export default StatCard;
