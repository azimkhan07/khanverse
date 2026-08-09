import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";
import SectionHeading from "../../components/common/SectionHeading";

import policies from "../../data/policies";

import "../../theme/css/policies.css";

function TermsConditions() {

    return (

        <>

            <PageHero
                title="Terms & Conditions"
                subtitle="Please read these terms before using KhanVerse."
            />

            <PageContainer>

                <SectionHeading
                    tag="TERMS"
                    title={policies.terms.title}
                    subtitle="These terms govern your use of our platform."
                />

                <div className="policy-content">

                    <p>

                        {policies.terms.description}

                    </p>

                    <h3>

                        User Responsibilities

                    </h3>

                    <p>

                        Users are responsible for maintaining account security,
                        providing accurate information and following our community guidelines.

                    </p>

                    <h3>

                        Payments

                    </h3>

                    <p>

                        All payments are processed securely. Refunds and disputes
                        are handled according to our platform policies.

                    </p>

                    <h3>

                        Prohibited Activities

                    </h3>

                    <p>

                        Fraud, spam, copyright violations and illegal activities
                        are strictly prohibited on KhanVerse.

                    </p>

                </div>

            </PageContainer>

        </>

    );

}

export default TermsConditions;
