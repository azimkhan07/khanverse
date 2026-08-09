import PageHero from "../../components/common/PageHero";
import SectionHeading from "../../components/common/SectionHeading";

import faq from "../../data/faq";

import "../../theme/css/faq.css";

function FAQ() {

    return (

        <>

            <PageHero
                title="Frequently Asked Questions"
                subtitle="Find answers to the most common questions about KhanVerse."
            />

            <section className="faq-section">

                <div className="container">

                    <SectionHeading
                        tag="FAQ"
                        title="Need Help?"
                        subtitle="Here are the answers to our most frequently asked questions."
                    />

                    <div className="faq-wrapper">

                        {

                            faq.map((item) => (

                                <div
                                    className="faq-card"
                                    key={item.id}
                                >

                                    <h3>

                                        {item.question}

                                    </h3>

                                    <p>

                                        {item.answer}

                                    </p>

                                </div>

                            ))

                        }

                    </div>

                </div>

            </section>

        </>

    );

}

export default FAQ;
