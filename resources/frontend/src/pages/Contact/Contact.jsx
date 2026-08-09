import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";
import SectionHeading from "../../components/common/SectionHeading";

import contact from "../../data/contact";

import "../../theme/css/contact.css";

function Contact() {

    return (

        <>

            <PageHero
                title="Contact Us"
                subtitle="We'd love to hear from you. Get in touch with our team."
            />

            <PageContainer>

                <SectionHeading
                    tag="CONTACT"
                    title="Let's Start A Conversation"
                    subtitle="Feel free to reach out anytime. Our team is always ready to help."
                />

                <div className="contact-wrapper">

                    <div className="contact-info">

                        <div className="contact-card">

                            <h3>Email</h3>

                            <p>{contact.email}</p>

                        </div>

                        <div className="contact-card">

                            <h3>Phone</h3>

                            <p>{contact.phone}</p>

                        </div>

                        <div className="contact-card">

                            <h3>Address</h3>

                            <p>{contact.address}</p>

                        </div>

                    </div>

                    <div className="contact-form">

                        <form>

                            <input
                                type="text"
                                placeholder="Your Name"
                            />

                            <input
                                type="email"
                                placeholder="Email Address"
                            />

                            <input
                                type="text"
                                placeholder="Subject"
                            />

                            <textarea
                                rows="6"
                                placeholder="Your Message"
                            ></textarea>

                            <button type="submit">

                                Send Message

                            </button>

                        </form>

                    </div>

                </div>

            </PageContainer>

        </>

    );

}

export default Contact;
