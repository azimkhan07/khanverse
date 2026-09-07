import { useRef, useEffect, useState } from "react";
import { motion, useScroll, useTransform } from "framer-motion";

function useIsMobile() {
    const [mobile, setMobile] = useState(() => {
        if (typeof window === "undefined") return false;
        return window.matchMedia("(max-width: 767px)").matches;
    });
    useEffect(() => {
        const mq = window.matchMedia("(max-width: 767px)");
        const onChange = (e) => setMobile(e.matches);
        mq.addEventListener ? mq.addEventListener("change", onChange) : mq.addListener(onChange);
        return () => {
            mq.removeEventListener ? mq.removeEventListener("change", onChange) : mq.removeListener(onChange);
        };
    }, []);
    return mobile;
}

export function SectionParallax({ children, className = "", distance = 50 }) {
    const isMobile = useIsMobile();
    const ref = useRef(null);
    const { scrollYProgress } = useScroll({
        target: ref,
        offset: ["start end", "end start"],
    });

    const y = useTransform(scrollYProgress, [0, 1], [distance, -distance]);
    const opacity = useTransform(scrollYProgress, [0, 0.16, 0.84, 1], [0, 1, 1, 0.15]);

    if (isMobile) {
        return (
            <motion.div
                ref={ref}
                className={className}
                initial={{ opacity: 0, y: 24 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true, amount: 0.12 }}
                transition={{ duration: 0.55, ease: [0.22, 1, 0.36, 1] }}
            >
                {children}
            </motion.div>
        );
    }

    return (
        <motion.div ref={ref} className={className} style={{ y, opacity }}>
            {children}
        </motion.div>
    );
}