import AboutHero from "../../components/about/AboutHero";
import AboutStory from "../../components/about/AboutStory";
import Mission from "../../components/about/Mission";
import Statistics from "../../components/about/Statistics";
import Team from "../../components/about/Team";
import CTA from "../../components/about/CTA";
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
