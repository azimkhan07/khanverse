import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";
import SectionHeading from "../../components/common/SectionHeading";

import policies from "../../data/policies";

import "../../theme/css/policies.css";

function PrivacyPolicy() {

    return (

        <>

            <PageHero
                title="Privacy Policy"
                subtitle="Your privacy and data security are important to us."
            />

            <PageContainer>

                <SectionHeading
                    tag="PRIVACY"
                    title={policies.privacy.title}
                    subtitle="Please read our privacy policy carefully."
                />

                <div className="policy-content">

                    <p>

                        {policies.privacy.description}

                    </p>

                    <h3>

                        Information We Collect

                    </h3>

                    <p>

                        We collect only the information required to provide our
                        services efficiently and securely.

                    </p>

                    <h3>

                        How We Use Your Information

                    </h3>

                    <p>

                        Your information is used to improve our services,
                        provide customer support and enhance your experience.

                    </p>

                    <h3>

                        Data Security

                    </h3>

                    <p>

                        We implement modern security practices to keep your
                        personal information protected.

                    </p>

                </div>

            </PageContainer>

        </>

    );

}

export default PrivacyPolicy;
