import { motion } from "framer-motion";
import "../../theme/css/why-khanverse.css";
import { whyData } from "../../components/whyKhanVerse/whyData";

const containerVariants = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.1,
        },
    },
};

const cardVariants = {
    hidden: { opacity: 0, y: 50, filter: "blur(6px)" },
    visible: {
        opacity: 1,
        y: 0,
        filter: "blur(0px)",
        transition: { duration: 0.6, ease: [0.21, 0.47, 0.32, 0.98] },
    },
};

function WhyKhanVerse() {
    return (
        <section className="why-khanverse">
            <div className="container">
                <motion.div
                    className="section-heading"
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, margin: "-60px" }}
                    transition={{ duration: 0.6, ease: [0.22, 1, 0.36, 1] }}
                >
                    <span>Why KhanVerse</span>
                    <h2>Built for the Future of Freelancing</h2>
                    <p>Everything you need to hire, work and grow securely.</p>
                </motion.div>

                <motion.div
                    className="why-grid"
                    variants={containerVariants}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: "0px 0px -10% 0px" }}
                >
                    {whyData.map((item) => {
                        const Icon = item.icon;
                        return (
                            <motion.div
                                className="why-card"
                                key={item.id}
                                variants={cardVariants}
                                whileHover={{ y: -6, scale: 1.02, transition: { duration: 0.3 } }}
                            >
                                <motion.div
                                    className="why-icon"
                                    whileHover={{ scale: 1.1 }}
                                    transition={{ type: "spring", stiffness: 300 }}
                                >
                                    <Icon size={40} />
                                </motion.div>
                                <h3>{item.title}</h3>
                                <p>{item.description}</p>
                            </motion.div>
                        );
                    })}
                </motion.div>
            </div>
        </section>
    );
}

export default WhyKhanVerse;
