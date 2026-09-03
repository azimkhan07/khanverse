import { useRef } from "react";
import { motion, useScroll, useTransform } from "framer-motion";

export function SectionParallax({ children, className = "", distance = 50 }) {
    const ref = useRef(null);
    const { scrollYProgress } = useScroll({
        target: ref,
        offset: ["start end", "end start"],
    });

    const y = useTransform(scrollYProgress, [0, 1], [distance, -distance]);
    const opacity = useTransform(scrollYProgress, [0, 0.16, 0.84, 1], [0, 1, 1, 0.15]);

    return (
        <motion.div ref={ref} className={className} style={{ y, opacity }}>
            {children}
        </motion.div>
    );
}
