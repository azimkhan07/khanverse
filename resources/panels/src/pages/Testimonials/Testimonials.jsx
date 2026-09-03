import { useState, useEffect } from "react";
import { motion } from "framer-motion";
import "../../theme/css/testimonials.css";
import { testimonials as staticTestimonials } from "../../components/testimonials/testimonialsData";
import { Star, Quote, TrendingUp, Users, Globe, Award } from "lucide-react";
import frontendApi from "../../shared/frontendApi";

const stats = [
    { icon: Users, number: "15K+", label: "Freelancers" },
    { icon: TrendingUp, number: "8K+", label: "Projects Done" },
    { icon: Globe, number: "120+", label: "Countries" },
    { icon: Award, number: "98%", label: "Satisfaction" },
];

const containerVariants = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.1,
        },
    },
};

const cardVariants = {
    hidden: { opacity: 0, y: 40 },
    visible: {
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] },
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

    useEffect(() => {
        frontendApi.get("/frontend/testimonials")
            .then((res) => {
                const data = Array.isArray(res.data) ? res.data : [];
                if (data.length > 0) {
                    setTestimonials(data.map((t) => ({
                        id: t.id,
                        name: t.name,
                        role: t.designation || t.company || "Client",
                        rating: t.rating || 5,
                        review: t.review,
                        image: t.image || "https://placehold.co/120",
                    })));
                } else {
                    setTestimonials(staticTestimonials);
                }
            })
            .catch(() => setTestimonials(staticTestimonials));
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
                    <p>Thousands of businesses trust KhanVerse to hire top freelancers from around the world.</p>
                </motion.div>

                <div className="testimonials-marquee">
                    <motion.div
                        className="testimonials-track"
                        variants={containerVariants}
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
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
                                <h3>{stat.number}</h3>
                                <p>{stat.label}</p>
                            </motion.div>
                        );
                    })}
                </motion.div>
            </div>
        </section>
    );
}

export default Testimonials;
