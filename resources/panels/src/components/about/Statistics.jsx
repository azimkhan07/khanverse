import { useState, useEffect, useRef } from "react";
import { motion } from "framer-motion";
import {
    Users,
    Briefcase,
    Globe,
    Star
} from "lucide-react";

function useCountUp(target, duration = 2000) {
    const [count, setCount] = useState(0);
    const ref = useRef(null);
    const hasAnimated = useRef(false);

    useEffect(() => {
        const node = ref.current;
        if (!node) return;

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting && !hasAnimated.current) {
                    hasAnimated.current = true;
                    const numericTarget = parseFloat(target.replace(/[^0-9.]/g, ""));
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
    }, [target, duration]);

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
    hidden: { opacity: 0, y: 30 },
    visible: {
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] },
    },
};

const statCards = [
    { Icon: Users, value: "15K+", label: "Verified Freelancers" },
    { Icon: Briefcase, value: "8K+", label: "Projects Completed" },
    { Icon: Globe, value: "120+", label: "Countries Served" },
    { Icon: Star, value: "4.9", label: "Average Rating", suffix: "★" },
];

function AnimatedStatCard({ Icon, value, label, suffix = "" }) {
    const { count, ref } = useCountUp(value, 2000);
    const numSuffix = suffix || value.replace(/[0-9.]/g, "");

    return (
        <motion.div
            className="stats-card"
            ref={ref}
            variants={cardVariants}
            whileHover={{ y: -6, transition: { duration: 0.3 } }}
        >
            <Icon size={42} />
            <h3>{count}{numSuffix}</h3>
            <p>{label}</p>
        </motion.div>
    );
}

function Statistics() {

    return (

        <section className="about-stats-section">

            <div className="container">

                <div className="section-heading">

                    <span className="section-tag">

                        OUR ACHIEVEMENTS

                    </span>

                    <h2>

                        Trusted By Thousands Worldwide

                    </h2>

                    <p>

                        We help businesses and freelancers connect through a
                        secure, AI-powered marketplace.

                    </p>

                </div>

                <motion.div
                    className="stats-grid"
                    variants={containerVariants}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: "-60px" }}
                >

                    {statCards.map((card) => (
                        <AnimatedStatCard
                            key={card.label}
                            Icon={card.Icon}
                            value={card.value}
                            label={card.label}
                            suffix={card.suffix}
                        />
                    ))}

                </motion.div>

            </div>

        </section>

    );

}

export default Statistics;
