import { useState, useEffect, useRef } from "react";
import { motion } from "framer-motion";
import "../../theme/css/statistics.css";
import { Users, Briefcase, ThumbsUp, Globe } from "lucide-react";
import frontendApi from "../../shared/frontendApi";

const iconComponents = {
    Users,
    Briefcase,
    ThumbsUp,
    Globe,
};

function useCountUp(target, duration = 2000, startOnView = false) {
    const [count, setCount] = useState(0);
    const ref = useRef(null);
    const hasAnimated = useRef(false);

    useEffect(() => {
        if (!startOnView) return;
        const node = ref.current;
        if (!node) return;

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting && !hasAnimated.current) {
                    hasAnimated.current = true;
                    const numericTarget = parseFloat(String(target).replace(/[^0-9.]/g, ""));
                    if (isNaN(numericTarget)) {
                        setCount(target);
                        return;
                    }
                    const startTime = performance.now();
                    const step = (now) => {
                        const elapsed = now - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        const eased = 1 - Math.pow(1 - progress, 3);
                        setCount(Math.floor(eased * numericTarget));
                        if (progress < 1) {
                            requestAnimationFrame(step);
                        } else {
                            setCount(numericTarget);
                        }
                    };
                    requestAnimationFrame(step);
                }
            },
            { threshold: 0.3 }
        );
        observer.observe(node);
        return () => observer.disconnect();
    }, [target, duration, startOnView]);

    return { count, ref };
}

const containerVariants = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.12,
        },
    },
};

const cardVariants = {
    hidden: { opacity: 0, y: 40, scale: 0.95, filter: "blur(6px)" },
    visible: {
        opacity: 1,
        y: 0,
        scale: 1,
        filter: "blur(0px)",
        transition: { duration: 0.6, ease: [0.21, 0.47, 0.32, 0.98] },
    },
};

function AnimatedStat({ item }) {
    const Icon = iconComponents[item.icon] || Globe;
    const { count, ref } = useCountUp(item.number, 2000, true);
    const suffix = typeof item.number === "string" ? item.number.replace(/[0-9.]/g, "") : "";

    return (
        <motion.div
            className="stat-card"
            ref={ref}
            variants={cardVariants}
            whileHover={{ y: -6, transition: { duration: 0.3 } }}
        >
            <div className="stat-icon">
                <Icon size={24} />
            </div>
            <h3 className="stat-number">{count}{suffix}</h3>
            <p className="stat-label">{item.title}</p>
        </motion.div>
    );
}

function Statistics() {
    const [items, setItems] = useState([]);

    useEffect(() => {
        frontendApi
            .get("/frontend/home")
            .then((res) => {
                const s = res.data?.stats;
                if (!s) return;
                const stats = [
                    { number: s.services, title: "Services", icon: "Briefcase" },
                    { number: s.sellers, title: "Active Sellers", icon: "Users" },
                    { number: s.delivered, title: "Orders Delivered", icon: "Globe" },
                    { number: s.buyers, title: "Happy Buyers", icon: "ThumbsUp" },
                ].filter((item) => Number(item.number) > 0);
                setItems(stats);
            })
            .catch(() => {});
    }, []);

    if (items.length === 0) return null;

    return (
        <section className="statistics">
            <div className="container">
                <motion.div
                    className="section-heading section-heading-dark"
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, margin: "-60px" }}
                    transition={{ duration: 0.6, ease: [0.22, 1, 0.36, 1] }}
                >
                    <span>Our Achievements</span>
                    <h2>Trusted by Businesses Worldwide</h2>
                    <p>Real numbers. Every count on SkillNest reflects actual marketplace activity.</p>
                </motion.div>

                <motion.div
                    className="stats-grid"
                    variants={containerVariants}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: "0px 0px -10% 0px" }}
                >
                    {items.map((item, i) => (
                        <AnimatedStat key={i} item={item} />
                    ))}
                </motion.div>
            </div>
        </section>
    );
}

export default Statistics;