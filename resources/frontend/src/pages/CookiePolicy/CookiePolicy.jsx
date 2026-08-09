import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";
import SectionHeading from "../../components/common/SectionHeading";

import policies from "../../data/policies";

import "../../theme/css/policies.css";

function CookiePolicy() {

    return (

        <>

            <PageHero
                title="Cookie Policy"
                subtitle="Learn how KhanVerse uses cookies to improve your experience."
            />

            <PageContainer>

                <SectionHeading
                    tag="COOKIES"
                    title={policies.cookies.title}
                    subtitle="Understanding how cookies work on our platform."
                />

                <div className="policy-content">

                    <p>

                        {policies.cookies.description}

                    </p>

                    <h3>

                        What Are Cookies?

                    </h3>

                    <p>

                        Cookies are small text files stored on your device
                        that help improve website functionality and user experience.

                    </p>

                    <h3>

                        Why We Use Cookies

                    </h3>

                    <p>

                        We use cookies to remember your preferences,
                        improve performance, analyze traffic and enhance security.

                    </p>

                    <h3>

                        Managing Cookies

                    </h3>

                    <p>

                        You can manage or disable cookies anytime through
                        your browser settings. Some features may not work properly
                        if cookies are disabled.

                    </p>

                </div>

            </PageContainer>

        </>

    );

}

export default CookiePolicy;
