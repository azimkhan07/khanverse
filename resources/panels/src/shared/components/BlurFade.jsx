import { motion } from "framer-motion";

function BlurFade({
    children,
    className = "",
    delay = 0,
    y = 16,
    blur = "6px",
    once = true,
    as = "div",
}) {
    const MotionTag = motion[as] || motion.div;

    return (
        <MotionTag
            className={className}
            initial={{ opacity: 0, filter: `blur(${blur})`, y }}
            whileInView={{ opacity: 1, filter: "blur(0px)", y: 0 }}
            viewport={{ once, margin: "0px 0px -10% 0px" }}
            transition={{ duration: 0.6, delay, ease: [0.21, 0.47, 0.32, 0.98] }}
        >
            {children}
        </MotionTag>
    );
}

export default BlurFade;
