import { motion } from 'framer-motion';

const getVariants = (direction, distance) => {
  const map = {
    up: { hidden: { opacity: 0, y: distance }, visible: { opacity: 1, y: 0 } },
    down: { hidden: { opacity: 0, y: -distance }, visible: { opacity: 1, y: 0 } },
    left: { hidden: { opacity: 0, x: -distance }, visible: { opacity: 1, x: 0 } },
    right: { hidden: { opacity: 0, x: distance }, visible: { opacity: 1, x: 0 } },
    fade: { hidden: { opacity: 0 }, visible: { opacity: 1 } },
    scale: { hidden: { opacity: 0, scale: 0.92 }, visible: { opacity: 1, scale: 1 } },
  };
  return map[direction] || map.up;
};

export function ScrollReveal({
  children,
  direction = 'up',
  delay = 0,
  distance = 30,
  duration = 0.7,
  once = true,
  className = '',
}) {
  const variants = getVariants(direction, distance);

  return (
    <motion.div
      className={className}
      initial="hidden"
      whileInView="visible"
      viewport={{ once, margin: '-20px' }}
      variants={variants}
      transition={{ duration, delay, ease: [0.22, 1, 0.36, 1] }}
    >
      {children}
    </motion.div>
  );
}

export function ScrollRevealGroup({
  children,
  staggerChildren = 0.08,
  direction = 'up',
  distance = 30,
  duration = 0.6,
  once = true,
  className = '',
}) {
  const variants = getVariants(direction, distance);

  const containerVariants = {
    hidden: {},
    visible: {
      transition: {
        staggerChildren,
        delayChildren: 0,
      },
    },
  };

  return (
    <motion.div
      className={className}
      initial="hidden"
      whileInView="visible"
      viewport={{ once, margin: '-20px' }}
      variants={containerVariants}
    >
      {Array.isArray(children)
        ? children.map((child, i) => (
            <motion.div
              key={child.key || i}
              variants={variants}
              transition={{ duration, ease: [0.22, 1, 0.36, 1] }}
            >
              {child}
            </motion.div>
          ))
        : (
            <motion.div
              variants={variants}
              transition={{ duration, ease: [0.22, 1, 0.36, 1] }}
            >
              {children}
            </motion.div>
          )}
    </motion.div>
  );
}
