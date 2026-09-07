import { ArrowRight, Store } from "lucide-react";
import { Link } from "react-router-dom";
import { motion } from "framer-motion";
import "../../theme/css/cta.css";

function CTA() {
    return (
        <section className="cta">
            <div className="container">
                <motion.div
                    className="cta-box"
                    initial={{ opacity: 0, y: 50 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, margin: "-80px" }}
                    transition={{ duration: 0.7, ease: [0.22, 1, 0.36, 1] }}
                >
                    <div className="cta-aurora">
                        <div className="aurora-blob aurora-blob-1" />
                        <div className="aurora-blob aurora-blob-2" />
                        <div className="aurora-blob aurora-blob-3" />
                    </div>

                    <motion.span
                        className="cta-badge"
                        initial={{ opacity: 0, scale: 0.8 }}
                        whileInView={{ opacity: 1, scale: 1 }}
                        viewport={{ once: true }}
                        transition={{ delay: 0.2, duration: 0.4 }}
                    >
                        🚀 Join SkillNest Today
                    </motion.span>

                    <motion.h2
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ delay: 0.3, duration: 0.5 }}
                    >
                        Ready to Build Your Next Big Project?
                    </motion.h2>

                    <motion.p
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ delay: 0.4, duration: 0.5 }}
                    >
                        Hire talented freelancers, grow your business or start
                        selling your skills on SkillNest today.
                    </motion.p>

                    <motion.div
                        className="cta-actions"
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ delay: 0.5, duration: 0.5 }}
                    >
                        <motion.div whileHover={{ scale: 1.05 }} whileTap={{ scale: 0.97 }}>
                            <Link to="/register" className="cta-primary">
                                Get Started
                                <ArrowRight size={16} />
                            </Link>
                        </motion.div>
                        <motion.div whileHover={{ scale: 1.05 }} whileTap={{ scale: 0.97 }}>
                            <Link to="/register" className="cta-secondary">
                                <Store size={16} />
                                Become a Seller
                            </Link>
                        </motion.div>
                    </motion.div>
                </motion.div>
            </div>
        </section>
    );
}

export default CTA;
