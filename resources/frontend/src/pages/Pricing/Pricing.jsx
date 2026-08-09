import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";
import SectionHeading from "../../components/common/SectionHeading";
import Badge from "../../components/common/Badge";

import pricing from "../../data/pricing";

import "../../theme/css/pricing.css";

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

                <div className="pricing-grid">

                    {

                        pricing.map((plan) => (

                            <div
                                className={`pricing-card ${plan.popular ? "active" : ""}`}
                                key={plan.id}
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

                            </div>

                        ))

                    }

                </div>

            </PageContainer>

        </>

    );

}

export default Pricing;
