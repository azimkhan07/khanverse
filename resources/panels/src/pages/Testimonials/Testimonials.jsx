import { useState, useEffect } from "react";
import { motion } from "framer-motion";
import "../../theme/css/testimonials.css";
import { Star, Quote, TrendingUp, Users, Globe, Award } from "lucide-react";
import frontendApi from "../../shared/frontendApi";

const containerVariants = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.1,
        },
    },
};

const cardVariants = {
    hidden: { opacity: 0, y: 40, filter: "blur(6px)" },
    visible: {
        opacity: 1,
        y: 0,
        filter: "blur(0px)",
        transition: { duration: 0.6, ease: [0.21, 0.47, 0.32, 0.98] },
    },
};

const statsContainerVariants = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.08,
            delayChildren: 0.3,
        },
    },
};

const statVariants = {
    hidden: { opacity: 0, scale: 0.8 },
    visible: {
        opacity: 1,
        scale: 1,
        transition: { duration: 0.4, ease: "easeOut" },
    },
};

function Testimonials() {
    const [testimonials, setTestimonials] = useState([]);
    const [stats, setStats] = useState([]);

    useEffect(() => {
        frontendApi.get("/frontend/home")
            .then((res) => {
                const s = res.data?.stats;
                if (s) {
                    setStats([
                        { icon: Users, number: s.sellers, label: "Freelancers" },
                        { icon: TrendingUp, number: s.services, label: "Active Services" },
                        { icon: Award, number: s.delivered, label: "Orders Delivered" },
                        { icon: Globe, number: s.countries, label: "Countries" },
                    ].filter((st) => Number(st.number) > 0));
                }
            })
            .catch(() => {});

        frontendApi.get("/frontend/testimonials")
            .then((res) => {
                const data = Array.isArray(res.data) ? res.data : [];
                setTestimonials(data.map((t) => ({
                    id: t.id,
                    name: t.name,
                    role: t.designation || t.company || "Client",
                    rating: t.rating || 5,
                    review: t.review,
                    image: t.image_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(t.name || "U")}&background=4F46E5&color=fff`,
                })));
            })
            .catch(() => setTestimonials([]));
    }, []);

    if (testimonials.length === 0) return null;

    return (
        <section className="testimonials">
            <div className="container">
                <motion.div
                    className="section-heading"
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, margin: "-60px" }}
                    transition={{ duration: 0.6, ease: [0.22, 1, 0.36, 1] }}
                >
                    <span>Testimonials</span>
                    <h2>What Our Clients Say</h2>
                    <p>Real feedback from real clients who hired freelancers on KhanVerse.</p>
                </motion.div>

                <div className="testimonials-marquee">
                    <motion.div
                        className="testimonials-track"
                        variants={containerVariants}
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "0px 0px -10% 0px" }}
                    >
                        {[...testimonials, ...testimonials].map((item, idx) => (
                            <motion.div
                                className="testimonial-card"
                                key={`${item.id}-${idx}`}
                                variants={cardVariants}
                                whileHover={{ y: -8, transition: { duration: 0.3 } }}
                            >
                                <motion.div
                                    initial={{ opacity: 0.6 }}
                                    whileHover={{ opacity: 1, scale: 1.1 }}
                                    transition={{ duration: 0.3 }}
                                >
                                    <Quote className="quote-icon" size={36} />
                                </motion.div>
                                <div className="testimonial-stars">
                                    {[...Array(item.rating)].map((_, index) => (
                                        <motion.div
                                            key={index}
                                            initial={{ scale: 0 }}
                                            whileInView={{ scale: 1 }}
                                            viewport={{ once: true }}
                                            transition={{ delay: index * 0.05 + 0.2, type: "spring", stiffness: 400, damping: 15 }}
                                        >
                                            <Star size={18} fill="#fbbf24" color="#fbbf24" />
                                        </motion.div>
                                    ))}
                                </div>
                                <p className="testimonial-text">"{item.review}"</p>
                                <div className="testimonial-author">
                                    <img className="testimonial-avatar" src={item.image} alt={item.name} />
                                    <div className="testimonial-info">
                                        <h4>{item.name}</h4>
                                        <span>{item.role}</span>
                                    </div>
                                </div>
                            </motion.div>
                        ))}
                    </motion.div>
                </div>

                {stats.length > 0 && (
                    <motion.div
                        className="trusted-stats"
                        variants={statsContainerVariants}
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-40px" }}
                    >
                        {stats.map((stat, i) => {
                            const Icon = stat.icon;
                            return (
                                <motion.div className="trusted-stat" key={i} variants={statVariants}>
                                    <div className="trusted-stat-icon">
                                        <Icon size={22} />
                                    </div>
                                    <h3>{Number(stat.number).toLocaleString("en-IN")}+</h3>
                                    <p>{stat.label}</p>
                                </motion.div>
                            );
                        })}
                    </motion.div>
                )}
            </div>
        </section>
    );
}

export default Testimonials;