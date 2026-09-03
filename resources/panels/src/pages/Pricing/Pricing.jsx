import { motion } from "framer-motion";
import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";
import SectionHeading from "../../components/common/SectionHeading";
import Badge from "../../components/common/Badge";

import pricing from "../../data/pricing";

import "../../theme/css/pricing.css";

const gridVariants = {
    hidden: {},
    visible: { transition: { staggerChildren: 0.12 } },
};

const cardVariants = {
    hidden: { opacity: 0, y: 40, scale: 0.96 },
    visible: {
        opacity: 1,
        y: 0,
        scale: 1,
        transition: { duration: 0.55, ease: [0.22, 1, 0.36, 1] },
    },
};

function Pricing() {

    return (

        <>

            <PageHero
                title="Pricing Plans"
                subtitle="Choose the perfect plan for your freelancing journey."
            />

            <PageContainer>

                <SectionHeading
                    tag="PRICING"
                    title="Simple & Transparent Pricing"
                    subtitle="Choose the plan that best fits your needs."
                />

                <motion.div
                    className="pricing-grid"
                    variants={gridVariants}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: "-60px" }}
                >

                    {

                        pricing.map((plan) => (

                            <motion.div
                                className={`pricing-card ${plan.popular ? "active" : ""}`}
                                key={plan.id}
                                variants={cardVariants}
                                whileHover={{ y: -10, transition: { duration: 0.3 } }}
                            >

                                {

                                    plan.popular && (

                                        <Badge>

                                            Most Popular

                                        </Badge>

                                    )

                                }

                                <h3>

                                    {plan.name}

                                </h3>

                                <div className="price">

                                    ${plan.price}

                                    <span>

                                        {plan.duration}

                                    </span>

                                </div>

                                <p>

                                    {plan.description}

                                </p>

                                <ul>

                                    {

                                        plan.features.map((feature, index) => (

                                            <li key={index}>

                                                ✓ {feature}

                                            </li>

                                        ))

                                    }

                                </ul>

                                <button className="pricing-btn">

                                    Get Started

                                </button>

                            </motion.div>

                        ))

                    }

                </motion.div>

            </PageContainer>

        </>

    );

}

export default Pricing;
