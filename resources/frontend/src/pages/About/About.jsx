import AboutHero from "../../components/About/AboutHero";
import AboutStory from "../../components/About/AboutStory";
import Mission from "../../components/About/Mission";
import Statistics from "../../components/About/Statistics";
import Team from "../../components/About/Team";
import CTA from "../../components/About/CTA";
import "../../theme/css/about.css"

function About() {
    return (
        <>
            <AboutHero />
            <AboutStory />
            <Mission />
            <Statistics />
            <Team />
            <CTA />
        </>
    );
}

export default About;
